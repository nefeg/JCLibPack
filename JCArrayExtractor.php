<?php

namespace Umbrella\JCLibPack;


/**
 * Class JCArrayExtractor
 *
 * @package Umbrella\JCLibPack
 */
class JCArrayExtractor
{
	/**
	 * @var array
	 */
	private $data;

	/**
	 * JCArrayExtractor constructor.
	 *
	 * @param array $data
	 */
	public function __construct(array $data){

		$this->data = $data;
	}

	/**
	 * @param      $fieldName
	 * @param null $null
	 * @return mixed|null
	 */
	public function get($fieldName, $null = null) {
		return isset($this->data[$fieldName])? $this-> data[$fieldName] :$null;
	}
}