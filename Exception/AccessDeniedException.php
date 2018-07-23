<?php
/**
 * Created by PhpStorm.
 * User: omni
 * Date: 24.07.2018
 * Time: 0:37
 */

namespace Umbrella\JCLibPack\Exception;

/**
 * Thrown when the access on a file was denied.
 *
 * @see \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
 */
class AccessDeniedException extends \Exception
{
	/**
	 * @param string $path The path to the accessed file
	 */
	public function __construct(string $path)
	{
		parent::__construct(sprintf('The file %s could not be accessed', $path));
	}
}