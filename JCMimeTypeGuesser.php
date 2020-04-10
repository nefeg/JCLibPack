<?php

namespace Aimchat\JCLibPack;

use Aimchat\JCLibPack\Exception\AccessDeniedException;
use Aimchat\JCLibPack\Exception\FileNotFoundException;

/**
 * Class JCMimeTypeGuesser
 *
 * @package App\Jelly\System\File\MimeType
 * @see \Symfony\Component\Mime\FileinfoMimeTypeGuesser
 */
class JCMimeTypeGuesser
{

	/**
	 * Default mime-type
	 */
	const UNKNOWN_MIME_TYPE = 'application/octet-stream';
	/**
	 * @var string
	 */
	private $magicFile;

	/**
	 * Returns whether this guesser is supported on the current OS/PHP setup.
	 *
	 * @return bool
	 */
	public static function isSupported()
	{
		return function_exists('finfo_open');
	}

	/**
	 * JCMimeTypeGuesser constructor.
	 *
	 * @param null $magicFile
	 */
	public function __construct($magicFile = null)
    {
	    $this->magicFile = $magicFile;
    }

	/**
	 * Guesses the mime type of the file with the given path.
	 *
	 * @param string $path The path to the file
	 *
	 * @return string The mime type or NULL, if none could be guessed
	 *
	 * @throws FileNotFoundException If the file does not exist
	 * @throws AccessDeniedException If the file could not be read
	 */
	public function guess($path) :?string
	{
		if (!is_file($path)) {
			throw new FileNotFoundException($path);
		}

		if (!is_readable($path)) {
			throw new AccessDeniedException($path);
		}

		if (!self::isSupported()) {
			return null;
		}

		if (!$finfo = new \finfo(FILEINFO_MIME_TYPE, $this->magicFile)) {
			return null;
		}

		$mime =  $finfo->file($path);

		return $mime == self::UNKNOWN_MIME_TYPE ? $this->detectFileMimeType($path) : $mime;
	}


    /**
     * @param string $path
     * @return string
     */
    protected function detectFileMimeType($path='')
    {
        $filename = escapeshellcmd($path);
        $command = "file -b --mime-type -m /usr/share/misc/magic {$filename}";

        $mimeType = trim(shell_exec($command));

        return $mimeType ?? self::UNKNOWN_MIME_TYPE;
    }
}