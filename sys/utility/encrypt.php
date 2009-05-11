<?
/**
*
* Copyright (c) 2009, Jon Gilkison and Massify LLC.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* - Redistributions of source code must retain the above copyright notice,
*   this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer in the
*   documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* This is a modified BSD license (the third clause has been removed).
* The BSD license may be found here:
* 
* http://www.opensource.org/licenses/bsd-license.php
*
*/

uses('sys.app.config');

/**
 * Provides two-way keyed encoding using XOR Hashing and Mcrypt
 * Stolen from CodeIgniter
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/encryption.html
 */
class Encrypt 
{
	private $encryption_key	= '';
	private $hash_type	= 'sha1';
	private $mcrypt_exists = FALSE;
	private $mcrypt_cipher;
	private $mcrypt_mode;
	
	/**
	 * Constructor
	 *
	 * Simply determines whether the mcrypt library exists.
	 *
	 */
	function __constructor()
	{
		$config=Config::Get('encrypt');
		
		$this->encryption_key=md5($config->key);
		$this->mcrypt_exists = ( ! function_exists('mcrypt_encrypt')) ? FALSE : TRUE;
	}
  	

	/**
	 * Encode
	 *
	 * Encodes the message string using bitwise XOR encoding.
	 * The key is combined with a random hash, and then it
	 * too gets converted using XOR. The whole thing is then run
	 * through mcrypt (if supported) using the randomized key.
	 * The end result is a double-encrypted message string
	 * that is randomized with each call to this function,
	 * even if the supplied message and key are the same.
	 *
	 * @access	public
	 * @param	string	the string to encode
	 * @param	string	the key
	 * @return	string
	 */
	function encode($string, $key = '')
	{
		$key = $this->encryption_key;
		$enc = $this->_xor_encode($string, $key);
		
		if ($this->mcrypt_exists === TRUE)
			$enc = $this->mcrypt_encode($enc, $key);

		return base64_encode($enc);		
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Decode
	 *
	 * Reverses the above process
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function decode($string, $key = '')
	{
		$key = $this->encryption_key;
		$dec = base64_decode($string);
		
		 if ($dec === FALSE)
		 	return FALSE;
		
		if ($this->mcrypt_exists === TRUE)
			$dec = $this->mcrypt_decode($dec, $key);
		
		return $this->_xor_decode($dec, $key);
	}
  	
	// --------------------------------------------------------------------

	/**
	 * XOR Encode
	 *
	 * Takes a plain-text string and key as input and generates an
	 * encoded bit-string using XOR
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _xor_encode($string, $key)
	{
		$rand = '';
		while (strlen($rand) < 32)
			$rand .= mt_rand(0, mt_getrandmax());

		$rand = $this->hash($rand);
		
		$enc = '';
		for ($i = 0; $i < strlen($string); $i++)
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
				
		return $this->_xor_merge($enc, $key);
	}
  	
	// --------------------------------------------------------------------

	/**
	 * XOR Decode
	 *
	 * Takes an encoded string and key as input and generates the
	 * plain-text original message
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);
		
		$dec = '';
		for ($i = 0; $i < strlen($string); $i++)
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));
	
		return $dec;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * XOR key + string Combiner
	 *
	 * Takes a string and key as input and computes the difference using XOR
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++)
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);

		return $str;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Encrypt using Mcrypt
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function mcrypt_encode($data, $key)
	{	
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect);
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Decrypt using Mcrypt
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function mcrypt_decode($data, $key)
	{
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Cipher
	 *
	 * @access	public
	 * @param	constant
	 * @return	string
	 */
	function set_cipher($cipher)
	{
		$this->mcrypt_cipher = $cipher;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Mode
	 *
	 * @access	public
	 * @param	constant
	 * @return	string
	 */
	function set_mode($mode)
	{
		$this->mcrypt_mode = $mode;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt cipher Value
	 *
	 * @access	private
	 * @return	string
	 */	
	function _get_cipher()
	{
		if ($this->mcrypt_cipher == '')
		{
			$this->mcrypt_cipher = MCRYPT_RIJNDAEL_256;
		}

		return $this->mcrypt_cipher;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt MOde Value
	 *
	 * @access	private
	 * @return	string
	 */	
	function _get_mode()
	{
		if ($this->mcrypt_mode == '')
		{
			$this->mcrypt_mode = MCRYPT_MODE_ECB;
		}
		
		return $this->mcrypt_mode;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Set the Hash type
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function set_hash($type = 'sha1')
	{
		$this->hash_type = ($type != 'sha1' AND $type != 'md5') ? 'sha1' : $type;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Hash encode a string
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function hash($str)
	{
		return ($this->hash_type == 'sha1') ? $this->sha1($str) : md5($str);
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Generate an SHA1 Hash
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function sha1($str)
	{
		if ( ! function_exists('sha1'))
		{
			if ( ! function_exists('mhash'))
			{	
				require_once(BASEPATH.'libraries/Sha1'.EXT);
				$SH = new CI_SHA;
				return $SH->generate($str);
			}
			else
			{
				return bin2hex(mhash(MHASH_SHA1, $str));
			}
		}
		else
		{
			return sha1($str);
		}	
	}
	
}
