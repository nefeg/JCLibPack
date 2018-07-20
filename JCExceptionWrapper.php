<?php
namespace Umbrella\JCLibPack;

/**
 * Class JCExceptionWrapper
 *
 * @package Umbrella\JCLibPack
 */
abstract class JCExceptionWrapper extends \Exception
{

	/**
	 * @param \Throwable $throwable
	 * @param string     $message
	 * @param int        $code
	 * @return static
	 */
	static public function wrap(\Throwable $throwable, string $message = "", int $code = 0 ) {

		$Exception = new static(
			$message ?: $throwable->getMessage(),
			$code ?: $throwable->getCode(),
			$throwable
		);

		return $Exception;
	}
}