<?php

namespace Automattic\WooCommerce\Caching;

/**
 * Base class for caching objects (or associative arrays) that have a unique identifier.
 * At the very least, derived classes need to implement '$object_type', but usually it will be convenient
 * to override some of the other protected members.
 */
abstract class ObjectCache
{
	public const DEFAULT_EXPIRATION = -1;

	public const MAX_EXPIRATION = 24*60*60;

	/**
	 * This needs to be set in each derived class.
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Default value for the duration of the objects in the cache, in seconds
	 * (may not be used depending on the cache engine used WordPress cache implementation).
	 *
	 * @var int
	 */
	protected $expiration = 3600;

	/**
	 * Temporarily used when retrieving data in 'get'.
	 *
	 * @var array
	 */
	private $last_cached_data;

	/**
	 * The cache engine to use.
	 *
	 * @var CacheEngine
	 */
	private $cache_engine = null;

	private $cache_key_prefix;

	private $cache_key_prefix_option_name;

	public function __construct()
	{
		if( '' === $this->object_type ) {
			throw new CacheException('Class ' . get_class($this) . ' has an empty value for $object_type' );
		}

		$this->cache_key_prefix_option_name = 'wp_object_cache_key_prefix_' . $this->object_type;
		$this->cache_key_prefix = $this->get_cache_key_prefix();
	}

	public function get_object_type() {
		return $this->object_type;
	}

	private function get_cache_engine() {
		if(null === $this->cache_engine) {
			$engine = wc_get_container()->get(WpCacheEngine::class);
			$this->cache_engine = apply_filters('wc_object_cache_get_engine', $engine, $this);
		}
		return $this->cache_engine;
	}

	private function create_cache_key_prefix(): string {
		$prefix_variable_part = dechex( microtime(true) * 1000 ) . bin2hex( random_bytes(8) );
		$prefix = "woocommerce_object_cache|{$this->object_type}|{$prefix_variable_part}|";
		if(!add_option($this->cache_key_prefix_option_name, $prefix)) {
			throw new CacheException("Can't store the key prefix option", $this);
		}
		return $prefix;
	}

	private function get_cache_key_prefix(): string {
		$value = get_option($this->cache_key_prefix_option_name);
		if(!$value) {
			$value = $this->create_cache_key_prefix();
		}
		return $value;
	}

	/**
	 * Add an object to the cache, or update an already cached object.
	 *
	 * @param object|array $object The object to be cached.
	 * @param int|string|null $id If null, get_object_id will be executed.
	 * @param int $expiration Expiration of the cached data in seconds from the current time.
	 * @return bool True on success, false on error.
	 */
	public function set( $id = null, $object, int $expiration = self::DEFAULT_EXPIRATION ): bool {
		if( $object === null ) {
			throw new CacheException("Can't cache a null value", $this, $id);
		}

		if( ! is_array( $object ) && ! is_object( $object ) ) {
			throw new CacheException("Can't cache a non-object, non-array value", $this, $id);
		}

		if(!is_string($id) && !is_int($id) && !is_null($id)) {
			throw new CacheException("Object id must be an int, a string, or null for 'set'", $this, $id);
		}

		$this->verify_expiration_value($expiration);

		if( $id === null ) {
			$id = $this->get_object_id( $object );
			if( $id === null ) {
				throw new CacheException("Null id supplied and the cache class doesn't implement get_object_id");
			}
		}

		$data = $this->validate_and_serialize( $object );
		if( array_key_exists( '_errors', $data ) ) {
			throw new CacheException("Object validation/serialization failed", $this, $id, $data['_errors']);
		}

		$data = apply_filters( "woocommerce_after_serializing_{$this->object_type}_for_caching", $data, $object, $id );

		$this->last_cached_data == $data;
		return $this->get_cache_engine()->cache_object($this->cache_key_prefix . $id, $data, $expiration === self::DEFAULT_EXPIRATION ? $this->expiration : $expiration);
	}

	private function verify_expiration_value(int $expiration): void {
		if($expiration !== self::DEFAULT_EXPIRATION && ($expiration<1 || $expiration > self::MAX_EXPIRATION)) {
			throw new CacheException("Invalid expiration value, must be ObjectCache::DEFAULT_EXPIRATION or a value between 1 and ObjectCache::MAX_EXPIRATION", $this);
		}
	}

	/**
	 * Retrieve a cached object.
	 *
	 * @param int|string $id The id of the object to retrieve.
	 * @param callable|null $get_from_datastore_callback Optional callback to get the object if it's not cached
	 * @param int $expiration Expiration of the cached data in seconds from the current time, used if an object is retrieved from datastore and cached.
	 * @return object|array|null Cached object, or null if it's not cached and can't be retrieved from datastore.
	 */
	public function get( $id, callable $get_from_datastore_callback = null, int $expiration = self::DEFAULT_EXPIRATION ) {
		if(!is_string($id) && !is_int($id)) {
			throw new CacheException("Object id must be an int or a string for 'get'", $this);
		}

		$this->verify_expiration_value($expiration);

		$data = $this->get_cache_engine()->get_cached_object($this->cache_key_prefix . $id);
		if( $data === null ) {
			if( $get_from_datastore_callback ) {
				$object = $get_from_datastore_callback( $id );
			}
			else {
				$object = $this->get_from_datastore( $id );
			}

			if( $object === null ) {
				return null;
			}

			$this->set( $object, $id, $expiration );
			$data = $this->last_cached_data;
		}

		$object = $this->deserialize( $data );
		$object = apply_filters( "woocommerce_after_deserializing_{$this->object_type}_from_cache", $object, $data, $id );

		return $object;
	}

	/**
	 * Remove an object from the cache.
	 *
	 * @param int|string $id The id of the object to remove.
	 * @return void
	 */
	public function remove( $id ): bool {
		$result = $this->get_cache_engine()->delete_cached_object($this->cache_key_prefix . $id);

		do_action( "woocommerce_after_removing_{$this->object_type}_from_cache", $id, $result );

		return $result;
	}

	/**
	 * Remove all the objects from cache.
	 *
	 * @return void
	 */
	public function flush(): void {
		$this->cache_key_prefix = $this->create_cache_key_prefix();

		do_action( "woocommerce_after_flushing_{$this->object_type}_cache" );
	}

	/**
	 * Is a given object cached?
	 *
	 * @param int|string $id The id of the object to check.
	 * @return bool True if there's a cached object with the specified id.
	 */
	public function is_cached( $id ): bool {
		return $this->get_cache_engine()->is_cached($id);
	}

	protected function get_object_id( $object ) {
		return null;
	}

	/**
	 * Validate an object and convert it to a serialized form suitable for caching.
	 *
	 * @param array|object $object
	 * @return array An associative array of data to cache, or with the key '_errors' being an error message strings array if validation fails.
	 */
	protected function validate_and_serialize( $object ): array {
		return [ 'data' => $object ];
	}

	/**
	 * Deserializes a set of object data after having been retrieved from the cache.
	 *
	 * @param array $serialized Serialized object data as it was returned by 'validate_and_serialize'.
	 * @return object|array
	 */
	protected function deserialize( array $serialized ) {
		return $serialized[ 'data' ];
	}

	protected function get_from_datastore( $id ) {
		return null;
	}
}
