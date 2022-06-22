<?php

namespace Automattic\WooCommerce\Caching;

class WpCacheEngine implements CacheEngine
{
	public const CACHE_GROUP_NAME = 'wc-object-cache';

	public function get_cached_object(string $key) {
		$value = wp_cache_get($key, self::CACHE_GROUP_NAME);
		return false === $value ? null : $value;
	}

	public function cache_object(string $key, $object, int $expiration): bool {
		return wp_cache_set($key, $object, self::CACHE_GROUP_NAME, $expiration);
	}

	public function delete_cached_object(string $key): bool {
		return wp_cache_delete($key, self::CACHE_GROUP_NAME);
	}

	public function is_cached(string $key): bool {
		return false !== wp_cache_get($key, self::CACHE_GROUP_NAME);
	}
}
