<?php
namespace Umbrella\JCLibPack;

use DateTimeZone;

/**
 * Class JCDateTime
 *
 * @package Umbrella\JCLibPack
 */
class JCDateTime extends \DateTime implements JCDateTimeInterface
{
	const MYSQL_FORMAT     = 'Y-m-d H:i:s';

	/**
	 * @var string
	 */
	private $stringFormat;

	/**
	 * @param \DateTime $DateTime
	 * @return JCDateTimeInterface
	 */
	static public function import(\DateTime $DateTime) :JCDateTimeInterface{

		return $DateTime instanceof static 
			? $DateTime
			: new static("@{$DateTime->getTimestamp()}", $DateTime->getTimezone());
	}

	/**
	 * @param mixed $dateTime
	 * @return JCDateTimeInterface
	 */
	static public function importStr($dateTime) :JCDateTimeInterface{

		return self::isValidTimeStamp($dateTime)
			? new self("@{$dateTime}")
			: new self("{$dateTime}");
	}

	/**
	 * @param $timestamp
	 * @return bool
	 */
	static public function isValidTimeStamp(string $timestamp) :bool
	{
		return is_numeric($timestamp)
			& ((string) (int) $timestamp === $timestamp)
			& ($timestamp <= PHP_INT_MAX)
			& ($timestamp >= ~PHP_INT_MAX);
	}

	/**
	 * @param string       $time
	 * @param DateTimeZone $timezone
	 * @param string       $stringFormat
	 * @link http://php.net/manual/en/datetime.construct.php
	 */
	public function __construct($time = 'now', \DateTimeZone $timezone = null, $stringFormat = self::MYSQL_FORMAT) {

		$this->stringFormat = $stringFormat;
		$timezone           = $timezone ?? new DateTimeZone('UTC');

		parent::__construct($time, $timezone);
	}

	/**
	 * @\JMS\Serializer\Annotation\HandlerCallback("json", direction = "serialization")
	 * ^ it's not bug! don't touch annotation!
	 * @return string
	 */
	public function __toString() :string {

		return (string) $this->format( $this->getStringFormat() );
	}

	/**
	 * @return string
	 */
	public function getStringFormat() :string{
		
		return $this->stringFormat;
	}

	/**
	 * @param string $stringFormat
	 * @return JCDateTimeInterface
	 */
	public function setStringFormat(string $stringFormat) :JCDateTimeInterface{
		
		$this->stringFormat = $stringFormat;

		return $this;
	}
}