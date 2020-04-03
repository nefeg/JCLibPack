<?php

namespace JCLibPack;

use Exception;

/**
 * Class JCMediaHelper
 *
 * @package App
 */
class JCMediaHelper
{
	/**
	 * @var string
	 */
	static public $tmpDir       = '/tmp';
	/**
	 * @var string
	 */
	static public $tmpPrefix    = 'JCMediaHelper-';

	/**
	 * @param string $filename
	 * @return array
	 */
	static public function detectType(string $filename) :array{
		return explode('/', mime_content_type($filename));
	}

	/**
	 * @param int $width_orig
	 * @param int $height_orig
	 * @param int $wMax
	 * @param int $hMax
	 * @return array
	 */
	static private function calcSize(int $width_orig, int $height_orig, int $wMax, int $hMax) :array
	{
		$width      = $wMax;
		$height     = $hMax;
		$ratio_orig = $width_orig/$height_orig;


		if ($wMax/$hMax > $ratio_orig)
			$width = $hMax * $ratio_orig;
		else
			$height = $wMax / $ratio_orig;

		return [$width, $height];
	}

	/**
	 * @param string $filename
	 * @param int    $wMax
	 * @param int    $hMax
	 * @param string $type (jpeg|png|gif|auto)
	 * @return string
	 * @throws Exception
	 */
	static public function resizeImage(string $filename, int $wMax, int $hMax, string $type = 'auto') :string
	{
		if ($type == 'auto')
			list(,$type) = explode('/', mime_content_type($filename));

		switch ($type){
			case 'jpeg':    return static::resizeJpeg($filename, $wMax, $hMax);
			case 'png':     return static::resizePng($filename, $wMax, $hMax);
			case 'gif':     return static::resizeGif($filename, $wMax, $hMax);
			default:
				throw new \Exception('JCMediaHelper: Unknown image type'); break;
		}

	}


	/**
	 * @param string $filename
	 * @param int    $wMax
	 * @param int    $hMax
	 * @return string
	 */
	static public function resizeJpeg(string $filename, int $wMax, int $hMax) :string
	{
		list($width_orig, $height_orig) = getimagesize($filename);
		list($width, $height)           = static::calcSize($width_orig, $height_orig, $wMax, $hMax);

		$imageResized = imagecreatetruecolor($width, $height);

		imagecopyresampled($imageResized, imagecreatefromjpeg($filename), 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

		imagejpeg($imageResized, $outPath = tempnam(static::$tmpDir, static::$tmpPrefix));

		return $outPath;
	}

	/**
	 * @param string $filename
	 * @param int    $wMax
	 * @param int    $hMax
	 * @return string
	 */
	static public function resizePng(string $filename, int $wMax, int $hMax) :string
	{
		list($width_orig, $height_orig) = getimagesize($filename);
		list($width, $height)           = static::calcSize($width_orig, $height_orig, $wMax, $hMax);

		$imageResized = imagecreatetruecolor($width, $height);

		imagecopyresampled($imageResized, imagecreatefrompng($filename), 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

		imagejpeg($imageResized, $outPath = tempnam(static::$tmpDir, static::$tmpPrefix));

		return $outPath;
	}

	/**
	 * @param string $filename
	 * @param int    $wMax
	 * @param int    $hMax
	 * @return string
	 */
	static public function resizeGif(string $filename, int $wMax, int $hMax) :string
	{
		list($width_orig, $height_orig) = getimagesize($filename);
		list($width, $height)           = static::calcSize($width_orig, $height_orig, $wMax, $hMax);

		$imageResized = imagecreatetruecolor($width, $height);

		imagecopyresampled($imageResized, imagecreatefromgif($filename), 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

		imagejpeg($imageResized, $outPath = tempnam(static::$tmpDir, static::$tmpPrefix));

		return $outPath;
	}

	/**
	 * @param string $filename
	 * @param int    $position - position from start (second)
	 * @param int    $wMax
	 * @param int    $hMax
	 * @return string
	 * @throws Exception
	 */
	static public function makeThumbnail(string $filename, int $wMax, int $hMax, int $position = 1)
	{
		try{
			$Ffmpeg = \FFMpeg\FFMpeg::create();

		}catch (\Throwable $exception){

			$Ffmpeg = \FFMpeg\FFMpeg::create([
				'ffmpeg.binaries'  => exec('which ffmpeg'),
				'ffprobe.binaries' => exec('which ffprobe')
			]);
		}

		$video = $Ffmpeg->open($filename);

		$video
			->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($position))
			->save($outPath = tempnam(static::$tmpDir, static::$tmpPrefix));

		return self::resizeImage($outPath, $wMax, $hMax);
	}
}