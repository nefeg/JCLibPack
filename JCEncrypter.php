<?php

namespace JCLibPack;

/**
 * Class JCEncrypter
 *
 * @package JCLibPack
 * @see http://php.net/manual/ru/function.openssl-private-decrypt.php
 */
class JCEncrypter
{
	//Block size for encryption block cipher
	private const ENCRYPT_BLOCK_SIZE = 200;// this for 2048 bit key for example, leaving some room

	//Block size for decryption block cipher
	private const DECRYPT_BLOCK_SIZE = 256;// this again for 2048 bit key

	/**
	 * generate key pair: `openssl rsa -in private.pem -outform PEM -pubout -out public.pem`
	 *
	 * @param $plainData
	 * @param $privatePEMKey
	 * @return string
	 */
	static public function encrypt_RSA($plainData, $privatePEMKey) :string
	{
		$encrypted = '';
		$plainData = str_split($plainData, static::ENCRYPT_BLOCK_SIZE);
		foreach($plainData as $chunk)
		{
			$partialEncrypted = '';

			//using for example OPENSSL_PKCS1_PADDING as padding
			$encryptionOk = openssl_private_encrypt($chunk, $partialEncrypted, $privatePEMKey, OPENSSL_PKCS1_PADDING);

			if($encryptionOk === false){return false;}//also you can return and error. If too big this will be false
			$encrypted .= $partialEncrypted;
		}
		return base64_encode($encrypted);//encoding the whole binary String as MIME base 64
	}

	/**
	 * @param $data
	 * @param $publicPEMKey
	 * @return string
	 */
	static public function decrypt_RSA($data, $publicPEMKey) :string
	{
		$decrypted = '';

		//decode must be done before spliting for getting the binary String
		$data = str_split(base64_decode($data), static::DECRYPT_BLOCK_SIZE);

		foreach($data as $chunk)
		{
			$partial = '';

			//be sure to match padding
			$decryptionOK = openssl_public_decrypt($chunk, $partial, $publicPEMKey, OPENSSL_PKCS1_PADDING);

			if($decryptionOK === false){return false;}//here also processed errors in decryption. If too big this will be false
			$decrypted .= $partial;
		}
		return $decrypted;
	}


//	/**
//	 * @param string $string
//	 * @param string $key
//	 * @return string
//	 */
//	static public function decryptString(string $string, string $key) :string{
//
//		return sodium_crypto_secretbox_open($string, 'salt', $key);
//
//	}
//
//	/**
//	 * @param string $string
//	 * @param string $key
//	 * @return string
//	 */
//	static public function encryptString(string $string, string $key) :string{
//
//		return sodium_crypto_secretbox($string, 'salt', $key);
//
//	}
}