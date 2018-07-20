<?php
namespace Umbrella\JCLibPack;

use Umbrella\JCLibPack\Exception\JCException;

/**
 * Class JCCmdLib
 * Wrapper for symfony's console commands
 *
 * @package Umbrella\JCLibPack
 */
class JCCmdLib
{
	/**
	 * Path to console script for DEV environment
	 * @var string|null
	 */
	static public $devPath;
	/**
	 * Path to console script for PROD environment
	 * @var string|null
	 */
	static public $prodPath;

	/**
	 * @param string $cmd
	 * @param string $env
	 * @param array  $arguments
	 * @param array  $options
	 * @param array  $noneValueOptions
	 * @return string
	 * @throws \Exception
	 */
	static public function compileCmd(string $cmd, string $env, array $arguments = [], array $options = [], array $noneValueOptions = []){

		return implode(' ', [
			'php',
			static::getConsolePath($env),
			$cmd,
			static::compileArgs($arguments),
			static::compileOptions($options),
            static::compileNoneValueOptions($noneValueOptions),
			"--env=$env"
		]);
	}

	/**
	 * @param array $arguments
	 * @return string
	 */
	static public function compileArgs(array $arguments) :string{

		return implode(' ', $arguments);
	}


	/**
	 * @param array $options
	 * @return string
	 * @throws \Exception
	 */
	static public function compileOptions(array $options) {

		$optArray = [];
		foreach ($options as $key => $value){

			$optArray[] = static::compileOption($key, $value);
		}

		return implode(' ', $optArray);
	}


	/**
	 * @param string $key
	 * @param        $value
	 * @return string
	 * @throws \Exception
	 */
	static private function compileOption(string $key, $value) :string
	{
		$optArray = [];
		if (is_scalar($value))
			$optArray[] = "--$key=" . escapeshellarg($value);

		elseif (is_iterable($value)){

			foreach ($value as $item) {
				$optArray[] = static::compileOption($key, $item);
			}

		}elseif(is_object($value)){
			if (!method_exists($value, '__toString'))
				throw new JCException('JCCmd: Error while string conversion: '. get_class($value));

			$optArray[] = static::compileOption($key, (string)$value);
		}

		return implode(' ', $optArray);
	}

    /**
     * @param array $options
     * @return string
     */
    static public function compileNoneValueOptions(array $options) {

        $optArray = [];
        foreach ($options as $key => $value){

            if(filter_var($value, FILTER_VALIDATE_BOOLEAN))
                $optArray[] = "--$key";
        }

        return implode(' ', $optArray);
    }

	/**
	 * @param string $env
	 * @return string
	 */
	static public function getConsolePath(string $env) :string
	{
		if ($env == 'prod' && static::$prodPath)
			return realpath(static::$prodPath);

		return static::$devPath ? realpath(static::$devPath) : realpath(__DIR__ . '/../../../bin/console');
	}
}