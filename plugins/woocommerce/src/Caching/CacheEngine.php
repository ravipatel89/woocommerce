<?php

namespace Automattic\WooCommerce\Caching;

interface CacheEngine
{
	public function get_cached_object(string $key);

	public function cache_object(string $key, $object, int $expiration): bool;

	public function delete_cached_object(string $key): bool;

	public function is_cached(string $key): bool;
}
