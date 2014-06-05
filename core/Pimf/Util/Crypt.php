<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://krsteski.de/new-bsd-license New BSD License
 */
namespace Pimf\Util;

/**
 *
 * @package Util
 * @link    https://bugs.php.net/bug.php?id=39736
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Crypt
{
    /**
     * The key with which the data will be encrypted or decrypted
     *
     * @var string
     */
    private $key;

    /**
     * Size of the IV belonging to a specific cipher/mode combination
     *
     * @var int
     */
    private $ivsize;

    public function __construct()
    {
        $this->key    = pack('H*', 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3');
        $this->ivsize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    }

    /**
     * @param string $plaintext
     *
     * @return string As base64_encode
     */
    public function encrypt($plaintext)
    {
        $iv         = mcrypt_create_iv($this->ivsize, MCRYPT_RAND);
        $ciphertext = @mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $plaintext, MCRYPT_MODE_CBC, $iv);

        self::bombIfErrorOccurred();

        return base64_encode($iv . $ciphertext);
    }

    /**
     * @param string $ciphertext As base64_encode
     *
     * @return string
     */
    public function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        $ivdec      = substr($ciphertext, 0, $this->ivsize);
        $ciphertext = substr($ciphertext, $this->ivsize);
        $plaintext  = @mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $ciphertext, MCRYPT_MODE_CBC, $ivdec);

        self::bombIfErrorOccurred();

        return rtrim($plaintext, "\0\4");
    }

    /**
     * @throws \UnexpectedValueException
     */
    private static function bombIfErrorOccurred()
    {
        $err = error_get_last();
        if ($err !== null) {
            throw new \UnexpectedValueException($err['message']);
        }
    }
}
