<?php
namespace Umbrella\JCLibPack;

/**
 * Class JCHelper
 *
 * @package Umbrella\JCLibPack
 */
class JCHelper
{
    /**
     *
     * cast all values to bool except values under key contains in $keys array and vice versa
     *
     * @param array $array
     * @param array $keys
     * @param bool $flag
     * @return array
     */
    static function castToBoolArray(array $array, array $keys, $flag = false){
        $result = array();
        foreach ($array as $k => $v) {
            if(in_array($k, $keys) == $flag){
                $result[$k] = filter_var($v, FILTER_VALIDATE_BOOLEAN);
            } else {
                $result[$k] = $v;
            }
        }
        return $result;
    }

	/**
	 * Cast all class properties to bool except properties under key contains in $keys array and vice versa
	 *
	 * @param object $object
	 * @param array  $keys
	 * @param bool   $flag
	 * @return object
	 * @throws \ErrorException
	 * @throws \ReflectionException
	 */
    static function castToBoolObject($object, array $keys, $flag = false){
        if(!is_object($object)){
            throw new \ErrorException("Please provide for castToBoolObject method an object");
        }

        $reflectionClass = new \ReflectionClass(get_class($object));
        foreach($reflectionClass->getProperties() as $property){
            if(in_array($property->getName(), $keys) == $flag){
                if($property->isProtected() or $property->isPrivate()){
                    $reflectionSetMethod = $reflectionClass->getMethod('set' . ucfirst($property->getName()));
                    $reflectionGetMethod = $reflectionClass->getMethod('get' . ucfirst($property->getName()));
                    if($reflectionSetMethod instanceof \ReflectionMethod and $reflectionGetMethod instanceof \ReflectionMethod){
                        $setMethod = $reflectionSetMethod->getName();
                        $getMethod = $reflectionGetMethod->getName();
                        $object->$setMethod(filter_var($object->$getMethod(), FILTER_VALIDATE_BOOLEAN));
                    }
                }
                if($property->isPublic()){
                    $propertyName = $property->getName();
                    $object->$propertyName = filter_var($object->$propertyName, FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        return $object;
    }

	/**
	 *
	 * modify string from underscore to camelCase
	 *
	 * @param      $str
	 * @param bool $firstCapitalLetter
	 * @param bool $castToLowerCase - cast string to lowercase before convert
	 * @return string
	 */
    static function underscore2Camelcase($str, $firstCapitalLetter = false, $castToLowerCase = true) {

        $words = explode('_', $castToLowerCase?strtolower($str):$str);

        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }

        if($firstCapitalLetter == false){
            $return = lcfirst($return);
        }

        return $return;
    }

	/**
	 *
	 * Id is unique in array of objects?
	 *
	 * @param $needle
	 * @param $hash
	 * @return bool
	 * @throws \TypeMismatchException
	 */
    static function isUniqueId($needle, $hash){

        foreach($hash as $item){
        	if (!$item instanceof JCIdentifyInterface)
		        throw new \TypeMismatchException("Expected JCIdentifyInterface");

            if($item->getId() == $needle)
                return false;
        }

        return true;
    }

    /**
     * @param $image1
     * @param $image2
     * @return bool
     */
    static function isSameImage($image1, $image2){
        return md5_file($image1) == md5_file($image2);
    }

    /**
     * @param int $length
     * @param $code_prefix - string prefix for code
     * @param $code_sufix - string sufix for code
     * @return string
     */
    public static function generateRandomCode($length = 16, $code_prefix = '', $code_sufix = '') {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        $now = new \DateTime();
        $randomString .= $now->format('Y') . $now->format('m') . $now->format('d');
        return $code_prefix . $randomString . $code_sufix;
    }

    /**
     * @param int $length
     * @param bool $leadingNils
     * @return string
     */
    public static function generateRandomDigitCode($length = 7, $leadingNils = true) :string {
        $code = (string)rand(0, (int)str_repeat('9', $length));

        if($leadingNils)
            $code = str_repeat('0', $length - strlen($code)) . $code;

        return $code;
    }

    /**
     * @param int $lowerCaseCount
     * @param int $upperCaseCount
     * @param int $digitsCount
     * @return string
     */
    public static function getAlphaNumGeneratedString($lowerCaseCount = 5, $upperCaseCount = 2, $digitsCount = 2){
        $character_set_array = array();
        $character_set_array[] = array('count' => (int) $lowerCaseCount, 'characters' => 'abcdefghijklmnopqrstuvwxyz');
        $character_set_array[] = array('count' => (int) $upperCaseCount, 'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $character_set_array[] = array('count' => (int) $digitsCount, 'characters' => '0123456789');
        $temp_array = array();
        foreach ($character_set_array as $character_set) {
            for ($i = 0; $i < $character_set['count']; $i++) {
                $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
            }
        }
        shuffle($temp_array);
        return implode('', $temp_array);
    }

	/**
	 * Generate crypto strong string
	 *
	 * @param int    $length
	 * @param bool   $salt
	 * @param string $excludes
	 * @param string $replace
	 * @return string
	 * @throws \Exception
	 */
	static public function generateCryptCode($length = 32, $salt = true, $excludes = '/\\=', $replace = '___'){
		if(!isset($length) || intval($length) <= 8 ){
			$length = 32;
		}

		$code = null;

		if (function_exists('random_bytes')) {
			$code = bin2hex(random_bytes($length));
		}
		if (function_exists('mcrypt_create_iv')) {
			$code = bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			$code = bin2hex(openssl_random_pseudo_bytes($length));
		}

		if (!$code) throw new \Exception('generateCryptCode fail!');

		if ($salt)
			$code = substr(strtr(base64_encode(hex2bin($code)), '+', self::generateRandomCode(rand(0,8)) ), 0, $length + rand(1, 32));

		if ($excludes)
			$code = strtr($code, $excludes, $replace);

		return $code;
	}


    /**
     * Recursively delete directory
     *
     * @param $dir
     */
    static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        self::rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }

    /**
     * Get date as string which accepts js date object constructor (multi-browser)
     *
     * @param $date
     * @return null|string
     */
    static function getJSLegacyDate($date){
        if($date instanceof \DateTime)
            return $date->format('Y-m-d\TH:i:sP');
        else
            return null;
    }

    /**
     * Return DateTime object sync with server time
     *
     * @param \DateTime $date
     * @return \DateTime
     */
    static function getSyncTime(\DateTime $date){
        $serverTimeZone = str_replace(array("\r", "\n"), '', `date +%Z`);
        return $date->setTimezone(new \DateTimeZone($serverTimeZone));
    }

	/**
	 * @param $value
	 * @param int $min
	 * @param int $max
	 * @param int $default
	 * @return int
	 * @throws \Exception
	 */
	static function getLimitNumericValue($value, $min = 0, $max = 50, $default = 0) {

		if($min > $max)
			throw new \Exception('Max limit should be more than min limit.');

		if(!is_null($value)) {
			$value = !is_null($min) && $value < $min ? $min : $value;
			$value = !is_null($max) && $value > $max ? $max : $value;
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 * @param $value
	 * @param int $min
	 * @param int $max
	 * @param int $default
	 * @return int
	 * @throws \Exception
	 */
	static function getLimitIntValue($value, $min = 0, $max = 50, $default = 0) : int {

		return (int) static::getLimitNumericValue($value, $min, $max, $default);
	}

	/**
	 * @param     $value
	 * @param int $min
	 * @param int $max
	 * @param int $default
	 * @return float
	 * @throws \Exception
	 */
	static function getLimitFloatValue($value, $min = 0, $max = 50, $default = 0) : float {

		return (float) static::getLimitNumericValue($value, $min, $max, $default);
	}

    /**
     * @param string $str
     * @return string
     */
    static function transliterate(string $str) {
        /*
         * constructor option to make transliteration plain e.g. ÈâuÑ -> Eaun
         */
        $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII; [\u0100-\u7fff] remove');
        return $transliterator->transliterate($str);
    }

	/**
	 * @param      $bytes
	 * @param null $force_unit
	 * @param null $format
	 * @param bool $si
	 * @return string
	 */
	static public function castBytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
	{
		// Format string
		$format = ($format === NULL) ? '%01.2f %s' : (string) $format;

		// IEC prefixes (binary)
		if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
		{
			$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
			$mod   = 1024;
		}
		// SI prefixes (decimal)
		else
		{
			$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
			$mod   = 1000;
		}

		// Determine unit to use
		if (($power = array_search((string) $force_unit, $units)) === FALSE)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}

    /**
     * @param string $string
     * @return array
     */
    static public function getEmailsFromString(string $string) : array {
        $pattern = "/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";
        preg_match_all($pattern, $string, $matches);

        return $matches[0];
    }

    static public function getPhone(string $phoneString) :string{
	    return preg_replace('/[^0-9]/', '', $phoneString);
    }
}