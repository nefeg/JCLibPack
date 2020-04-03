<?php

namespace JCLibPack;

use JCLibPack\Exception\JCException;

/**
 * Class JCJson
 *
 * @package Jelly\System
 */
class JCJson implements  JCStringInterface, JCArrayInterface, \Serializable, \ArrayAccess, \Iterator
{
	/**
	 * @var array
	 */
	private $source;

	/**
	 * @param      $source
	 * @param bool $assoc
	 * @param int  $depth
	 * @param int  $options
	 * @return JCJson|static
	 * @throws JCException
	 */
	static public function factory($source, $assoc = true, $depth = 512, $options = 0){

		if ($source instanceof static) 
			return $source;
		
		$source = static::_normalizeData($source, $assoc, $depth, $options);

		return new static($source);
	}

	/**
	 * @param      $source
	 * @param      $onErrResult
	 * @param bool $assoc
	 * @param int  $depth
	 * @param int  $options
	 * @return static|mixed
	 */
	static public function tryFactory($source, $onErrResult, $assoc = true, $depth = 512, $options = 0) {

		try{
			
			$result = static::factory($source, $assoc, $depth, $options);
			
		}catch (\Exception $e){

			$result = $onErrResult;
		}

		return $result;
	}


	/**
	 * @param      $source
	 * @param bool $assoc
	 * @param int  $depth
	 * @param int  $options
	 * @return string
	 * @throws JCException
	 */
	static protected function _normalizeData($source, $assoc = true, $depth = 512, $options = 0){

		if ($source instanceof JCArrayInterface)
			$source = $source->__toArray();
		else
			if (is_object($source) && method_exists($source, '__toString'))
				$source = (string) $source;


		if (is_string($source)){
			$source = json_decode($source, $assoc, $depth, $options);

			if (json_last_error())
				throw new JCException("JCJson: ". json_last_error_msg());
		}

		if (!is_array($source))
			throw new JCException(__CLASS__ . ' --> $source must be a string or array or implement method __toString or toArray: ' . gettype($source));

		return $source;
	}


	/**
	 * JCJson constructor.
	 *
	 * @param array $source
	 */
	public function __construct(array $source){

		$this->source = $source;
	}


	/**
	 * @return string
	 * @throws JCException
	 */
	public function __toString() :string{
		$string = json_encode($this->source);

		if (json_last_error())
			throw new JCException(json_last_error_msg());

		return $string;
	}

	/**
	 * @return array
	 */
	public function __toArray() :array {

		return $this->source;
	}

	#######################
	/**** ArrayAccess ****/
	#######################

	/**
	 * Whether a offset exists
	 *
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->source[$offset]);
	}

	/**
	 * Alias to offsetExists()
	 * 
	 * @param $key
	 * @return bool
	 */
	public function has($key) {
		return $this->offsetExists($key);
	}

	/**
	 * Offset to retrieve
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->source[$offset];
	}

	/**
	 * Alias to offsetGet()
	 * 
	 * @param $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->offsetGet($key);
	}

	/**
	 * Offset to set
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 * @return JCJson
	 */
	public function offsetSet($offset, $value) {
		$this->source[$offset] = $value;
		
		return $this;
	}

	/**
	 * Alias to offsetSet()
	 *
	 * @param $key
	 * @param $value
	 * @return JCJson
	 */
	public function set($key, $value) {
		
		return $this->offsetSet($key, $value);
	}

	/**
	 * Offset to unset
	 *
	 * @param mixed $offset
	 * @return JCJson
	 */
	public function offsetUnset($offset) {
		unset($this->source[$offset]);
		
		return $this;
	}

	/**
	 * @param $key
	 * @return JCJson
	 */
	public function rm($key) {
		return $this->offsetUnset($key);
	}

	########################
	/**** Serializable ****/
	########################

	/**
	 * String representation of object
	 *
	 * @link  http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize() :string{
		
		return (string) $this;
	}

	/**
	 * Constructs the object
	 *
	 * @link  http://php.net/manual/en/serializable.unserialize.php
	 * @param string $serialized The string representation of the object.
	 *
	 * @return void
	 * @throws JCException
	 * @since 5.1.0
	 */
	public function unserialize($serialized) {

		$this->source = static::_normalizeData($serialized);
	}



	####################
	/**** Iterator ****/
	####################

	/**
	 * Return the current element
	 *
	 * @link  http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current() {

		return current($this->source);
	}

	/**
	 * Move forward to next element
	 *
	 * @link  http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next() {

		next($this->source);
	}

	/**
	 * Return the key of the current element
	 *
	 * @link  http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key() {

		return key($this->source);
	}

	/**
	 * Checks if current position is valid
	 *
	 * @link  http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 *        Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid() {

		return key($this->source) !== null;
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @link  http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind() {

		reset($this->source);
	}
}