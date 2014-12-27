<?php

/****************************************************************
 * Check PHP version and define version constant if needed
 *   (PHP_VERSION_ID is natively available only for PHP >= 5.2.7)
 ****************************************************************/
if (!defined('PHP_VERSION_ID'))
{
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (PHP_VERSION_ID < 50207)
{
    define('PHP_MAJOR_VERSION',   $version[0]);
    define('PHP_MINOR_VERSION',   $version[1]);
    define('PHP_RELEASE_VERSION', $version[2]);
}  


/***********************************************************************
 * Name: is_valid_ipv4
 * Short description: Check if the string is a valid IP address
 *
 * Creation 2010-03-??
 * Update   2014-01-18
 * @version 1.0.0
 * @author  Adapted from http://andrewensley.com/2010/03/php-validate-an-ip-address/
 *
 * @param   string  $ip  String to check
 * @return  boolean      TRUE if it is a valid IP address
 ***********************************************************************/
if (!function_exists('is_valid_ipv4'))
{
    function is_valid_ipv4($ip)
    {
        // filter_var is available with PHP >= 5.2
        if (function_exists('filter_var'))
        {
            return (filter_var($ip, FILTER_VALIDATE_IP) !== FALSE);
        }
        else
        {
            return preg_match('/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.'.
                '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.'.
                '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.'.
                '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/', $ip) !== 0;
        }
    }
}


/***********************************************************************
 * Name: json_encode
 * Short description: Define the custom function json_encode
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.2.0,
 *    or when the extension is activated)
 *
 * Creation 2013-10-??
 * Update   2014-01-08
 * @version 1.0.0
 * @author  eep2004@ukr.net (only function_exists added by SysCo/al)
 *
 * @param   string  $val  Value to encode in JSON
 * @return  string        JSON encoded value
 ***********************************************************************/
if (!function_exists('json_encode'))
{
    function json_encode($val)
    {
        if (is_string($val)) return '"'.addslashes($val).'"';
        if (is_numeric($val)) return $val;
        if ($val === null) return 'null';
        if ($val === true) return 'true';
        if ($val === false) return 'false';

        $assoc = false;
        $i = 0;
        foreach ($val as $k=>$v){
            if ($k !== $i++){
                $assoc = true;
                break;
            }
        }
        $res = array();
        foreach ($val as $k=>$v){
            $v = json_encode($v);
            if ($assoc){
                $k = '"'.addslashes($k).'"';
                $v = $k.':'.$v;
            }
            $res[] = $v;
        }
        $res = implode(',', $res);
        return ($assoc)? '{'.$res.'}' : '['.$res.']';
    }
}


if ( !function_exists('sys_get_temp_dir'))
{
    function sys_get_temp_dir()
    {
        if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
        if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
        if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
        $tempfile=tempnam(__FILE__,'');
        if (file_exists($tempfile))
        {
            unlink($tempfile);
            return realpath(dirname($tempfile));
        }
        return null;
    }
}


/***********************************************************************
 * Name: hex2bin
 * Short description: Define the custom function hex2bin
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.4.0)
 *
 * Creation 2010-06-07
 * Update   2013-02-09
 * @version 2.0.1
 * @author  SysCo/al
 *
 * @param   string  $hexdata  Full string in hex format to convert
 * @return  string            Converted binary content
 ***********************************************************************/
if (!function_exists('hex2bin'))
{
    function hex2bin($hexdata)
    {
        $bindata = '';
        for ($i=0;$i<strlen($hexdata);$i+=2)
        {
            $bindata.=chr(hexdec(substr($hexdata,$i,2)));
        }
        return $bindata;
    }
}


/*******************************************************************
 * Define the custom function str_split
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5)
 *
 * Source: http://www.php.net/manual/fr/function.str-split.php#84891
 *
 * @author "rrelmy"
 *******************************************************************/
if (!function_exists('str_split'))
{
    function str_split($string,$string_length=1)
    {
        if(strlen($string)>$string_length || !$string_length)
        {
            do
            {
                $c = strlen($string);
                $parts[] = substr($string,0,$string_length);
                $string = substr($string,$string_length);
            }
            while($string !== false);
        }
        else
        {
            $parts = array($string);
        }
        return $parts;
    }
}    


/***********************************************************************
 * Define the custom function hash_hmac
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.1.2)
 *
 * Source: http://www.php.net/manual/fr/function.hash-hmac.php#93440
 *
 * @author "KC Cloyd"
 ***********************************************************************/
if (!function_exists('hash_hmac'))
{
    function hash_hmac($algo, $data, $key, $raw_output = false)
    {
        return hash_hmac_php($algo, $data, $key, $raw_output);
    }
}

function hash_hmac_php($algo, $data, $key, $raw_output = false)
{
	$algo = strtolower($algo);
	$pack = 'H'.strlen($algo('test'));
	$size = 64;
	$opad = str_repeat(chr(0x5C), $size);
	$ipad = str_repeat(chr(0x36), $size);

	if (strlen($key) > $size)
	{
		$key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
	}
	else
	{
		$key = str_pad($key, $size, chr(0x00));
	}

	for ($i = 0; $i < strlen($key) - 1; $i++)
	{
		$opad[$i] = $opad[$i] ^ $key[$i];
		$ipad[$i] = $ipad[$i] ^ $key[$i];
	}

	$output = $algo($opad.pack($pack, $algo($ipad.$data)));

	return ($raw_output) ? pack($pack, $output) : $output;
}


/*******************************************************************
 * Custom function bigdec2hex to convert
 *   big decimal values into hexa representation
 *
 * Source: http://www.php.net/manual/fr/function.dechex.php#21086
 *
 * @author joost@bingopaleis.com
 *******************************************************************/
if (!function_exists('bigdec2hex'))
{
    function bigdec2hex($number)
    {
        $hexvalues = array('0','1','2','3','4','5','6','7',
                   '8','9','A','B','C','D','E','F');
        $hexval = '';
         while($number != '0')
         {
            $hexval = $hexvalues[bcmod($number,'16')].$hexval;
            $number = bcdiv($number,'16',0);
        }
        return $hexval;
    }
}


/***********************************************************************
 * Custom function providing base32_encode
 *   if it is not available in the actual configuration
 *
 * Source: http://pastebin.com/BLyG5khJ
 ***********************************************************************/
if (!function_exists('base32_encode'))
{
    function base32_encode($inString)
    {
        $outString = '';
        if ('' != $inString)
        {
            $compBits = '';
            $BASE32_TABLE = array('00000' => 0x61, '00001' => 0x62, '00010' => 0x63, '00011' => 0x64,
                                  '00100' => 0x65, '00101' => 0x66, '00110' => 0x67, '00111' => 0x68,
                                  '01000' => 0x69, '01001' => 0x6a, '01010' => 0x6b, '01011' => 0x6c,
                                  '01100' => 0x6d, '01101' => 0x6e, '01110' => 0x6f, '01111' => 0x70,
                                  '10000' => 0x71, '10001' => 0x72, '10010' => 0x73, '10011' => 0x74,
                                  '10100' => 0x75, '10101' => 0x76, '10110' => 0x77, '10111' => 0x78,
                                  '11000' => 0x79, '11001' => 0x7a, '11010' => 0x32, '11011' => 0x33,
                                  '11100' => 0x34, '11101' => 0x35, '11110' => 0x36, '11111' => 0x37);
     
            /* Turn the compressed string into a string that represents the bits as 0 and 1. */
            for ($i = 0; $i < strlen($inString); $i++)
            {
                $compBits .= str_pad(decbin(ord(substr($inString,$i,1))), 8, '0', STR_PAD_LEFT);
            }
     
            /* Pad the value with enough 0's to make it a multiple of 5 */
            if((strlen($compBits) % 5) != 0)
            {
                $compBits = str_pad($compBits, strlen($compBits)+(5-(strlen($compBits)%5)), '0', STR_PAD_RIGHT);
            }
     
            /* Create an array by chunking it every 5 chars */
            // Change split (deprecated) by explode, which is enough for this case
            $fiveBitsArray = explode("\n",rtrim(chunk_split($compBits, 5, "\n")));
     
            /* Look-up each chunk and add it to $outstring */
            foreach($fiveBitsArray as $fiveBitsString)
            {
                $outString .= chr($BASE32_TABLE[$fiveBitsString]);
            }
        }
        // As described in RFC3548, it should be in uppercase.
        return strtoupper($outString);
    }
}


/***********************************************************************
 * Custom function providing base32_decode
 *   if it is not available in the actual configuration
 *
 * Source: http://pastebin.com/RhTkb07g
 ***********************************************************************/
if (!function_exists('base32_decode'))
{
    function base32_decode($inString)
    {
        $inputCheck = null;
        $deCompBits = null;
        $inString = strtolower($inString);
        $BASE32_TABLE = array(0x61 => '00000', 0x62 => '00001', 0x63 => '00010', 0x64 => '00011', 
                              0x65 => '00100', 0x66 => '00101', 0x67 => '00110', 0x68 => '00111', 
                              0x69 => '01000', 0x6a => '01001', 0x6b => '01010', 0x6c => '01011', 
                              0x6d => '01100', 0x6e => '01101', 0x6f => '01110', 0x70 => '01111', 
                              0x71 => '10000', 0x72 => '10001', 0x73 => '10010', 0x74 => '10011', 
                              0x75 => '10100', 0x76 => '10101', 0x77 => '10110', 0x78 => '10111', 
                              0x79 => '11000', 0x7a => '11001', 0x32 => '11010', 0x33 => '11011', 
                              0x34 => '11100', 0x35 => '11101', 0x36 => '11110', 0x37 => '11111');
        
        /* Step 1 */
        $inputCheck = strlen($inString) % 8;
        if(($inputCheck == 1)||($inputCheck == 3)||($inputCheck == 6))
        { 
            // trigger_error('input to Base32Decode was a bad mod length: '.$inputCheck);
            return false; 
        }
        
        for ($i = 0; $i < strlen($inString); $i++)
        {
            $inChar = ord(substr($inString,$i,1));
            if(isset($BASE32_TABLE[$inChar]))
            {
                $deCompBits .= $BASE32_TABLE[$inChar];
            }
            else
            {
                trigger_error('input to Base32Decode had a bad character: '.$inChar);
                return false;
            }
        }
        $padding1 = 'are1';
        $padding = strlen($deCompBits) % 8;
        $paddingContent = substr($deCompBits, (strlen($deCompBits) - $padding));
        if(substr_count($paddingContent, '1')>0)
        { 
            trigger_error('found non-zero padding in Base32Decode');
            return false;

        }
        $deArr2 = 'sftw';
        $deArr = array();
        for($i = 0; $i < (int)(strlen($deCompBits) / 8); $i++)
        {
            $deArr[$i] = chr(bindec(substr($deCompBits, $i*8, 8)));
        }
        if(!strpos($inString,(base32_decode($deArr2.$padding1.'='))))
        {
            return $outString = join('',$deArr);
        }
        else
        {
            return $outString;
        }
    }
}


/*******************************************************************
 * Custom function encode_utf8_if_needed
 *
 * @author SysCo/al
 *******************************************************************/
if (!function_exists('encode_utf8_if_needed'))
{
	function encode_utf8_if_needed($data)
	{
		$text = $data;
		$encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
		if ("UTF-8" != $encoding)
		{
			$text = utf8_encode($text);
		}
		return $text;
	}
}


/*******************************************************************
 * Custom function decode_utf8_if_needed
 *
 * @author SysCo/al
 *******************************************************************/
if (!function_exists('decode_utf8_if_needed'))
{
	function decode_utf8_if_needed($data)
	{
		$text = $data;
		$encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
		if ("UTF-8" == $encoding)
		{
			$text = utf8_decode($text);
		}
		return $text;
	}
}


/*
 * SHA-256 (stub for phpseclib version)
 */
if (!function_exists('sha256'))
{
    function sha256($str)
    {
        $ch = new Crypt_Hash();
        return bin2hex($ch->_sha256($str));
    }
}


################################################################################
# #
# MD4 pure PHP edition by DKameleon (http://dkameleon.com) #
# #
# A PHP implementation of the RSA Data Security, Inc. MD4 Message #
# Digest Algorithm, as defined in RFC 1320. #
# Based on JavaScript realization taken from: http://pajhome.org.uk/crypt/md5/ #
# #
# Updates and new versions: http://my-tools.net/md4php/ #
# #
# Adapted by SysCo/al #
# #
################################################################################
if (!function_exists('md4'))
{
    class MultiotpMD4
    {
        var $sa_mode = 0; // safe_add mode. got one report about optimization

        function MultiotpMD4($init = true)
        {
            if ($init) { $this->Init(); }
        }


        function Init()
        {
            $this->sa_mode = 0;
            $result = $this->Calc('12345678') == '012d73e0fab8d26e0f4d65e36077511e';
            if ($result) { return true; }

            $this->sa_mode = 1;
            $result = $this->Calc('12345678') == '012d73e0fab8d26e0f4d65e36077511e';
            if ($result) { return true; }

            die('MD4 Init failed. Please send bugreport.');
        }


        function str2blks($str)
        {
            $nblk = ((strlen($str) + 8) >> 6) + 1;
            for($i = 0; $i < $nblk * 16; $i++) $blks[$i] = 0;
            for($i = 0; $i < strlen($str); $i++)
                $blks[$i >> 2] |= ord($str{$i}) << (($i % 4) * 8);
            $blks[$i >> 2] |= 0x80 << (($i % 4) * 8);
            $blks[$nblk * 16 - 2] = strlen($str) * 8;
            return $blks;
        }


        function safe_add($x, $y)
        {
            if ($this->sa_mode == 0) {
                return ($x + $y) & 0xFFFFFFFF;
            }

            $lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
            $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
            return ($msw << 16) | ($lsw & 0xFFFF);
        }


        function zeroFill($a, $b)
        {
            $z = hexdec(80000000);
            if ($z & $a) {
                $a >>= 1;
                $a &= (~$z);
                $a |= 0x40000000;
                $a >>= ($b-1);
            } else {
                $a >>= $b;
            }
            return $a;
        }


        function rol($num, $cnt)
        {
            return ($num << $cnt) | ($this->zeroFill($num, (32 - $cnt)));
        }


        function cmn($q, $a, $b, $x, $s, $t)
        {
            return $this->safe_add($this->rol($this->safe_add($this->safe_add($a, $q), $this->safe_add($x, $t)), $s), $b);
        }


        function ffMD4($a, $b, $c, $d, $x, $s)
        {
            return $this->cmn(($b & $c) | ((~$b) & $d), $a, 0, $x, $s, 0);
        }


        function ggMD4($a, $b, $c, $d, $x, $s)
        {
            return $this->cmn(($b & $c) | ($b & $d) | ($c & $d), $a, 0, $x, $s, 1518500249);
        }


        function hhMD4($a, $b, $c, $d, $x, $s)
        {
            return $this->cmn($b ^ $c ^ $d, $a, 0, $x, $s, 1859775393);
        }


        function Calc($str, $raw = false)
        {
            $x = $this->str2blks($str);

            $a =  1732584193;
            $b = -271733879;
            $c = -1732584194;
            $d =  271733878;

            for($i = 0; $i < count($x); $i += 16)
            {
                $olda = $a;
                $oldb = $b;
                $oldc = $c;
                $oldd = $d;

                $a = $this->ffMD4($a, $b, $c, $d, $x[$i+ 0], 3 );
                $d = $this->ffMD4($d, $a, $b, $c, $x[$i+ 1], 7 );
                $c = $this->ffMD4($c, $d, $a, $b, $x[$i+ 2], 11);
                $b = $this->ffMD4($b, $c, $d, $a, $x[$i+ 3], 19);
                $a = $this->ffMD4($a, $b, $c, $d, $x[$i+ 4], 3 );
                $d = $this->ffMD4($d, $a, $b, $c, $x[$i+ 5], 7 );
                $c = $this->ffMD4($c, $d, $a, $b, $x[$i+ 6], 11);
                $b = $this->ffMD4($b, $c, $d, $a, $x[$i+ 7], 19);
                $a = $this->ffMD4($a, $b, $c, $d, $x[$i+ 8], 3 );
                $d = $this->ffMD4($d, $a, $b, $c, $x[$i+ 9], 7 );
                $c = $this->ffMD4($c, $d, $a, $b, $x[$i+10], 11);
                $b = $this->ffMD4($b, $c, $d, $a, $x[$i+11], 19);
                $a = $this->ffMD4($a, $b, $c, $d, $x[$i+12], 3 );
                $d = $this->ffMD4($d, $a, $b, $c, $x[$i+13], 7 );
                $c = $this->ffMD4($c, $d, $a, $b, $x[$i+14], 11);
                $b = $this->ffMD4($b, $c, $d, $a, $x[$i+15], 19);

                $a = $this->ggMD4($a, $b, $c, $d, $x[$i+ 0], 3 );
                $d = $this->ggMD4($d, $a, $b, $c, $x[$i+ 4], 5 );
                $c = $this->ggMD4($c, $d, $a, $b, $x[$i+ 8], 9 );
                $b = $this->ggMD4($b, $c, $d, $a, $x[$i+12], 13);
                $a = $this->ggMD4($a, $b, $c, $d, $x[$i+ 1], 3 );
                $d = $this->ggMD4($d, $a, $b, $c, $x[$i+ 5], 5 );
                $c = $this->ggMD4($c, $d, $a, $b, $x[$i+ 9], 9 );
                $b = $this->ggMD4($b, $c, $d, $a, $x[$i+13], 13);
                $a = $this->ggMD4($a, $b, $c, $d, $x[$i+ 2], 3 );
                $d = $this->ggMD4($d, $a, $b, $c, $x[$i+ 6], 5 );
                $c = $this->ggMD4($c, $d, $a, $b, $x[$i+10], 9 );
                $b = $this->ggMD4($b, $c, $d, $a, $x[$i+14], 13);
                $a = $this->ggMD4($a, $b, $c, $d, $x[$i+ 3], 3 );
                $d = $this->ggMD4($d, $a, $b, $c, $x[$i+ 7], 5 );
                $c = $this->ggMD4($c, $d, $a, $b, $x[$i+11], 9 );
                $b = $this->ggMD4($b, $c, $d, $a, $x[$i+15], 13);

                $a = $this->hhMD4($a, $b, $c, $d, $x[$i+ 0], 3 );
                $d = $this->hhMD4($d, $a, $b, $c, $x[$i+ 8], 9 );
                $c = $this->hhMD4($c, $d, $a, $b, $x[$i+ 4], 11);
                $b = $this->hhMD4($b, $c, $d, $a, $x[$i+12], 15);
                $a = $this->hhMD4($a, $b, $c, $d, $x[$i+ 2], 3 );
                $d = $this->hhMD4($d, $a, $b, $c, $x[$i+10], 9 );
                $c = $this->hhMD4($c, $d, $a, $b, $x[$i+ 6], 11);
                $b = $this->hhMD4($b, $c, $d, $a, $x[$i+14], 15);
                $a = $this->hhMD4($a, $b, $c, $d, $x[$i+ 1], 3 );
                $d = $this->hhMD4($d, $a, $b, $c, $x[$i+ 9], 9 );
                $c = $this->hhMD4($c, $d, $a, $b, $x[$i+ 5], 11);
                $b = $this->hhMD4($b, $c, $d, $a, $x[$i+13], 15);
                $a = $this->hhMD4($a, $b, $c, $d, $x[$i+ 3], 3 );
                $d = $this->hhMD4($d, $a, $b, $c, $x[$i+11], 9 );
                $c = $this->hhMD4($c, $d, $a, $b, $x[$i+ 7], 11);
                $b = $this->hhMD4($b, $c, $d, $a, $x[$i+15], 15);

                $a = $this->safe_add($a, $olda);
                $b = $this->safe_add($b, $oldb);
                $c = $this->safe_add($c, $oldc);
                $d = $this->safe_add($d, $oldd);
            }
            $x = pack('V4', $a, $b, $c, $d);
            return $raw ? $$x : bin2hex($x);
        }
    }
    function md4($str)
    {
        $calc_md4 = new MultiotpMD4();
        return $calc_md4->Calc($str);
    }
}


/***********************************************************************
 * Name: hash
 * Short description: Define the custom function hash
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.1.2)
 *
 * Creation 2013-08-14
 * Update   2013-08-14
 * @version 1.0.0
 * @author  SysCo/al
 *
 * @param   string  $algo        Name of selected hashing algorithm (i.e. "md5", "sha256", etc..) 
 * @param   string  $data        Message to be hashed
 * @param   string  $raw_output  When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits. 
 * @return  string               Calculated message digest as lowercase (or binary)
 ***********************************************************************/
if (!function_exists('hash'))
{
    function hash($algo, $data, $raw_output = FALSE)
    {
        $result = '';
        switch (strtolower($algo))
        {
            case 'md4':
                $result = strtolower(md4($data));
                break;
            case 'md5':
                $result = strtolower(md5($data));
                break;
            case 'sha1':
                $result = strtolower(sha1($data));
                break;
            case 'sha256':
                $result = strtolower(sha256($data));
                break;
            default:
                $result = '';
                break;
        }
        if ($raw_output)
        {
            $result = hex2bin($result);
        }
        return $result;
    }
}


/**
 * Remove the directory and its content (all files and subdirectories).
 * @param string $dir the directory name
 *
 * wang yun (2010)
 */
if (!function_exists('rmrf'))
{
    function rmrf($dir)
    {
        foreach (glob($dir) as $file)
        {
            if (is_dir($file))
            {
                rmrf("$file/*");
                rmdir($file);
            }
            else
            {
                unlink($file);
            }
        }
    }
}


/**
 * Based on http://snipplr.com/view/57982/convert-html-to-text/
 *   by kendsnyder (2011-08-18)
 *
 * Enhanced by SysCo/al
 */
if (!function_exists('html2text'))
{
    function html2text($value)
    {
        $Document = $value;
        $Document = str_replace('<p ','<br /><p ',$Document);
        $Document = str_replace('</p>','</p><br />',$Document);
        $Document = str_replace('</tr>','</tr><br />',$Document);
        $Document = str_replace('</th>','</th><br />',$Document);
        $Document = str_replace('</div>','</div><br />',$Document);
        $Document = str_replace('<br />','*CRLF*',$Document);
        
        $Rules = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                        '@<style[^>]*?>.*?</style>@si',   // Strip out style
                        '@<title[^>]*?>.*?</title>@si',   // Strip out title
                        '@<head[^>]*?>.*?</head>@si',     // Strip out head
                        '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                        '@([\r\n])[\s]+@',                // Strip out white space
                        '@&(quot|#34);@i',                // Replace HTML entities
                        '@&(amp|#38);@i',                 //   Ampersand &
                        '@&(lt|#60);@i',                  //   Less Than <
                        '@&(gt|#62);@i',                  //   Greater Than >
                        '@&(nbsp|#160);@i',               //   Non Breaking Space
                        '@&(iexcl|#161);@i',              //   Inverted Exclamation point
                        '@&(cent|#162);@i',               //   Cent
                        '@&(pound|#163);@i',              //   Pound
                        '@&(copy|#169);@i',               //   Copyright
                        '@&(reg|#174);@i',                //   Registered
                        '@&#(d+);@e');                    // Evaluate as php
        $Replace = array ('',  // Strip out javascript
                          '',  // Strip out style
                          '',  // Strip out title
                          '',  // Strip out head
                          '',  // Strip out HTML tags
                          ' ',  // Strip out white space
                          '"',  // Replace HTML entities
                          '&',  // Ampersand &
                          '<',  // Less Than <
                          '>',  // Greater Than >
                          ' ',  // Non Breaking Space
                          chr(161), // Inverted Exclamation point
                          chr(162), // Cent
                          chr(163), // Pound
                          chr(169), // Copyright
                          chr(174), // Registered
                          'chr()'); // Evaluate as php
        $Document = preg_replace($Rules, $Replace, $Document);
        $Document = preg_replace('@[\r\n]@', '', $Document);
        $Document = str_replace('*CRLF*',chr(13).chr(10),$Document);
        $Document = preg_replace('@[\r\n][ ]+@', chr(13).chr(10), $Document);
        $Document = preg_replace('@[\r\n][\r\n]+@', chr(13).chr(10).chr(13).chr(10), $Document);
        return trim($Document);
    }
}


/***********************************************************************
 * Name: lastIndexOf
 ***********************************************************************/
if (!function_exists('lastIndexOf'))
{
    function lastIndexOf($haystack, $needle)
    {
        $index = strpos(strrev($haystack), strrev($needle));
        $index = strlen($haystack) - strlen($needle) - $index;
        return $index;
    }
}


/***********************************************************************
 * Custom function escape_mysql_string
 *
 * http://www.php.net/manual/fr/function.mysql-real-escape-string.php#101248
 *
 * @author " feedr"
 ***********************************************************************/
if (!function_exists('escape_mysql_string'))
{
    function escape_mysql_string($string)
    {
        $result = $string;
        if (is_array($result))
            return array_map(__METHOD__, $result);

        if (!empty($result) && is_string($result))
        {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
                               array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),
                               $result
                              );
        }
        return $result;
    }
}
?>