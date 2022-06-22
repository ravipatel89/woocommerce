<?php

namespace Automattic\WooCommerce\Caching;

class CacheException extends \Exception
{
	private $errors;
	private $thrower;
	private $cached_id;

	// Redefine the exception so message isn't optional
	public function __construct($message, ObjectCache $thrower, $cached_id = null, array $errors = null, $code = 0, Throwable $previous = null) {
		$this->errors = $errors ?? [];
		$this->thrower = $thrower;
		$this->cached_id = $cached_id;

		parent::__construct($message, $code, $previous);
	}

	// custom string representation of object
	public function __toString() {
		$cached_id_part = $$this->cached_id ? ", id: {$this->cached_id}" : '';
		return __CLASS__ . ": [{$this->thrower->get_object_type()}{$cached_id_part}]: {$this->message}\n";
	}

	public function get_errors(): array {
		return $this->errors;
	}

	public function get_thrower(): object {
		return $this->thrower;
	}
}
