<?php

namespace Aimchat\JCLibPack;

/**
 * Interface JCDateTimeInterface
 *
 * @package Aimchat\JCLibPack
 */
interface JCDateTimeInterface extends JCStringInterface, \DateTimeInterface
{
	/**
	 * @return string
	 */
	public function getStringFormat() :string;

	/**
	 * @param string $stringFormat
	 * @return JCDateTimeInterface
	 */
	public function setStringFormat(string $stringFormat) :JCDateTimeInterface;
}