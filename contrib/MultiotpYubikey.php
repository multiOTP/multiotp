<?php
/*****************************************
 * MultiotpYubikey Class (LGPLv3)        *
 * André Liechti                         *
 * http://www.multiotp.net/              *
 *****************************************/

class MultiotpYubikey
/**
 * @class     MultiotpYubikey
 * @brief     Class definition for Yubikey handling.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   4.3.1.2
 * @date      2014-12-22
 * @since     2014-11-04
 */
{
    // TODO: support Dvorak keyboard "jxe.uidchtnbpygk" instead of "cbdefghijklnrtuv" (automatic detection with "x.py" detected or keyboard unknown)

    // How to get a Yubico API Key: https://upgrade.yubico.com/getapikey/
    var $_yubicloud_client_id        = 19042;                          // Client ID  (by default, this ID is for multiOTP open source)
    var $_yubicloud_secret_key       = 'a72X/qkw3vPeT+yRO6lWgipwjPM='; // Secret Key (by default, this key is for multiOTP open source)

    var $_yubicloud_timeout          = 10;                 // YubiCloud timeout in seconds
    var $_yubicloud_last_response    = array();            // YubiCloud last response array
    var $_yubicloud_last_result      = '';                 // YubiCloud last result (text)
    var $_yubicloud_max_time_window  = 600;                // YubiCloud maximum time window in seconds
	var $_yubico_modhex_chars        = "cbdefghijklnrtuv"; // ModHex values (instead of 0,1,2,3,4,5,6,7,8,9,0,a,b,c,d,e,f)
	var $_yubico_modhex_dvorak_chars = "jxe.uidchtnbpygk"; // Dvorak ModHex values (instead of 0,1,2,3,4,5,6,7,8,9,0,a,b,c,d,e,f)
	var $_yubico_dvorak_only_chars   = "x.py";             // Dvorak only chars
    var $_yubico_otp_last_count      = -1;                 // Default value of the last otp counter

    
    function MultiotpYubikey($yubicloud_client_id = 0, $yubicloud_secret_key = '')
    {
        if (0 < intval($yubicloud_client_id))
        {
            $this->_yubicloud_client_id = $yubicloud_client_id;
        }
        if (28 == strlen($yubicloud_secret_key))
        {
            $this->_yubicloud_secret_key = $yubicloud_secret_key;
        }
    }


    function CalculateHashHmac($algo, $data, $key, $raw_output = false)
    {
        if (function_exists('hash_hmac'))
        {
            return hash_hmac($algo, $data, $key, $raw_output);
        }
        else
        {
            /***********************************************************************
             * Simulate the function hash_hmac if it is not available
             *   (this function is natively available only for PHP >= 5.1.2)
             *
             * Source: http://www.php.net/manual/fr/function.hash-hmac.php#93440
             *
             * @author "KC Cloyd"
             ***********************************************************************/
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
    }


    function Iso13239Crc16($buffer)
    // http://forum.yubico.com/viewtopic.php?f=2&t=69
    {
        $crc = 0xffff;
        for($loop=0; $loop<strlen($buffer); $loop++)
        {
            $crc ^= ord($buffer[$loop]) & 0xff;
            for ($bit=0; $bit<8; $bit++)
            {
                $j=$crc & 1;
                $crc >>= 1;
                if ($j)
                {
                    $crc ^= 0x8408;
                }
            }
        }
        return $crc;
    }


    function CheckYubicoOtp($yubico_modhex_encrypted_part,
                            $secret,
                            $last_count = -1)
    {
        $result = 99;

        $encrypted_part = hex2bin($this->ModHexToHex($yubico_modhex_encrypted_part));
        $cipher_aes = new Crypt_AES(CRYPT_AES_MODE_ECB);
        $cipher_aes->setKey((hex2bin($secret)));
        $cipher_aes->disablePadding();
        $decrypted_part = $cipher_aes->decrypt(($encrypted_part));
        unset($cipher_aes);

        $uid        = bin2hex(substr($decrypted_part,  0, 6));
        $useCtr     = ord($decrypted_part[6]) + 256 * ord($decrypted_part[7]);
        $tstp       = ord($decrypted_part[8]) + 256 * ord($decrypted_part[9]) + 65536 * ord($decrypted_part[10]);
        $sessionCtr = ord($decrypted_part[11]);
        $rnd        = ord($decrypted_part[12]) + 256 * ord($decrypted_part[13]);
        $crc        = ord($decrypted_part[14]) + 256 * ord($decrypted_part[15]);
        $check_crc  = $this->Iso13239Crc16($decrypted_part);

        // Based on information available here: http://www.yubico.com/wp-content/uploads/2013/07/YubiKey-Manual-v3_1.pdf
        //
        // $uid         Private ID
        // $useCtr      Usage counter, non-volatile counter, incremented when device is used after a power-up or reset
        // $tstp        Timestamp, 8Hz, random value startup, wraps from 0xffffff to 0 (after 24 days)
        // $sessionCtr  Session usage counter, set to 0 at power-up, incremented by one after each generation
        // $rnd         Random number
        // $crc         Checksum, 16-bit ISO13239 1st complement checksum of the first 14 bytes, result added to the end
        //                $crc = 0xffff - $this->Iso13239Crc16(substr($decrypted_part, 0, 14)); // One's complement
        // $check_crc   Calculate the ISO13239 of the 16 bits, should give a fixed residual of 0xf0b8 if checksum is valid

        if (0xf0b8 == $check_crc) // Check should always give 0xf0b8
        {
            $counter_position = ($useCtr * 256) + $sessionCtr;
            if ($counter_position <= $last_count)
            {
                $result = 26; // ERROR: this token has already been used
            }
            else
            {
                $this->_yubico_otp_last_count = $counter_position;
                $result = 0;
            }
        }
        return $result;
    }


    function GetYubicoOtpLastCount()
    {
        return $this->_yubico_otp_last_count;
    }


    function GetYubiCloudLastResponse()
    {
        return $this->_yubicloud_last_response;
    }


    function GetYubiCloudLastResult()
    {
        return $this->_yubicloud_last_result;
    }


    function CheckOnYubiCloud($otp_to_check)
    /**
     * Validation Protocol Version 2.0 is implemented
     *   (https://code.google.com/p/yubikey-val-server-php/wiki/ValidationProtocolV20)
     * Old validation Protocol Version 1.0 is not implemented anymore
     *   (https://code.google.com/p/yubikey-val-server-php/wiki/ValidationProtocolV10)
     */
    {
        $this->_yubicloud_last_response = array();
        $this->_yubicloud_last_result = 'NOT_ENOUGH_ANSWERS';
        $yubiotp = trim($otp_to_check);
        $result = 99;
        if ((44 == strlen($yubiotp)) && ($this->IsModHex($yubiotp)))
        {
            $yubicloud_servers = array('api.yubico.com/wsapi/2.0/verify',
                                       'api2.yubico.com/wsapi/2.0/verify',
                                       'api3.yubico.com/wsapi/2.0/verify',
                                       'api4.yubico.com/wsapi/2.0/verify',
                                       'api5.yubico.com/wsapi/2.0/verify');

            $yubicloud_parameters = array('id'        => $this->_yubicloud_client_id,
                                          'otp'       => $yubiotp,
                                          'timestamp' => 1,
                                          'nonce'     => md5(uniqid(rand())),
                                       /* 'sl'        => '', */ /* precentage of syncing not well documented */
                                          'timeout'   => $this->_yubicloud_timeout
                                         );

            // Parameters must be in the right order in order to calculate the hash
            ksort($yubicloud_parameters);

            $url_parameters = '';
            
            foreach($yubicloud_parameters as $key=>$value)
            {
                $url_parameters .= "&".$key."=".$value;
            }

            $url_parameters = substr($url_parameters, 1);
            
            if (28 == strlen($this->_yubicloud_secret_key))
            {
                $yubicloud_hash = urlencode(base64_encode($this->CalculateHashHmac('sha1',
                                                                                   $url_parameters,
                                                                                   base64_decode($this->_yubicloud_secret_key),
                                                                                   TRUE
                                                                                  )));
                $url_parameters.= '&h='.$yubicloud_hash;
            }
            
            foreach($yubicloud_servers as $one_yubicloud_server)
            {
                $yubicloud_answer = '';
                $yubicloud_url = $one_yubicloud_server.'?'.$url_parameters;
            
                $protocol = ''; // Default is http
                $port = 80;
                $pos = strpos($yubicloud_url, '://');
                if (FALSE !== $pos)
                {
                    switch (strtolower(substr($yubicloud_url,0,$pos)))
                    {
                        case 'https':
                        case 'ssl':
                            $protocol = 'ssl://';
                            $port = 443;
                            break;
                        case 'tls':
                            $protocol = 'tls://';
                            $port = 443;
                            break;
                    }
                    
                    $yubicloud_url = substr($yubicloud_url,$pos+3);
                }
                
                $pos = strpos($yubicloud_url, '/');
                if (FALSE === $pos)
                {
                    $host = $yubicloud_url;
                    $url = '/';
                }
                else
                {
                    $host = substr($yubicloud_url,0,$pos);
                    $url = substr($yubicloud_url,$pos); // And not +1 as we want the / at the beginning
                }
                
                $pos = strpos($host, ':');
                if (FALSE !== $pos)
                {
                    $port = substr($host,$pos+1);
                    $host = substr($host,0,$pos);
                }
                
                $errno = 0;
                $errdesc = 0;
                $fp = @fsockopen($protocol.$host, $port, $errno, $errdesc, $this->_yubicloud_timeout);
                if (FALSE !== $fp)
                {
                    $info['timed_out'] = FALSE;
                    fputs($fp, "GET ".$url." HTTP/1.0\r\n");
                    fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
                    // fputs($fp, "Content-Length: ".strlen($content_to_post)."\r\n");
                    fputs($fp, "User-Agent: multiOTP\r\n");
                    fputs($fp, "Host: ".$host."\r\n");
                    fputs($fp, "\r\n");
                    // fputs($fp, $content_to_post);
                    fputs($fp, "\r\n");

                    stream_set_blocking($fp, TRUE);
                    stream_set_timeout($fp, $this->_yubicloud_timeout);
                    $info = stream_get_meta_data($fp); 
            
                    $reply = '';
                    $last_length = 0;
                    while ((!feof($fp)) && ((!$info['timed_out']) || ($last_length != strlen($reply))))
                    {
                        $last_length = strlen($reply);
                        $reply.= fgets($fp, 1024);
                        $info = stream_get_meta_data($fp);
                        @ob_flush(); // Avoid notice if any (if the buffer is empty and therefore cannot be flushed)
                        flush(); 
                    }
                    fclose($fp);

                    if (!($info['timed_out']))
                    {
                        $pos = strpos(strtolower($reply), "\r\n\r\n");
                        $header = substr($reply, 0, $pos);
                        $yubicloud_response = substr($reply, $pos + 4);
                        
                        $yubicloud_response_array = explode("\r\n", trim($yubicloud_response));
                        
                        $response = array();

                        $response['now_utc'] = date ("U");

                        foreach($yubicloud_response_array as $one_yubicloud_response)
                        {
                            /* = is also used in BASE64 encoding so we only replace the first = by # which is not used in BASE64 */
                            list($key,$value) = explode('=', $one_yubicloud_response, 2);
                            $response[$key] = $value;
                        }
                                            
                        $yubicloud_response_parameters = array('otp',
                                                               'nonce',
                                                               't',
                                                               'status',
                                                               'timestamp',
                                                               'sessioncounter',
                                                               'sessionuse',
                                                               'sl'
                                                              );

                        // Parameters must be in the right order in order to calculate the hash
                        sort($yubicloud_response_parameters);
                        
                        if (isset($response['t']))
                        {
                            $response['t_utc'] = date_format(date_create(substr($response['t'], 0, -4)), "U");
                        }

                        $parameters_for_hash = '';
                        foreach ($yubicloud_response_parameters as $one_parameter)
                        {
                            if (array_key_exists($one_parameter, $response))
                            {
                                if ('' != $parameters_for_hash)
                                {
                                    $parameters_for_hash.= '&';
                                }
                                $parameters_for_hash.= $one_parameter.'='.$response[$one_parameter];
                            }
                        }

                        $this->_yubicloud_last_response = $response;

                        $check_response_hash = "NO-VALID-SECRET-KEY";
                        if (28 == strlen($this->_yubicloud_secret_key))
                        {
                            $check_response_hash = base64_encode($this->CalculateHashHmac('sha1',
                                                                                          $parameters_for_hash,
                                                                                          base64_decode($this->_yubicloud_secret_key),
                                                                                          TRUE
                                                                                         ));
                        }
                        if (($check_response_hash != $response['h']) && ("NO-VALID-SECRET-KEY" != $check_response_hash))
                        {
                            $this->_yubicloud_last_result = 'BAD_SIGNATURE';
                            $result = 99;
                        }
                        elseif ($yubicloud_parameters['nonce'] != $response['nonce'])
                        {
                            $this->_yubicloud_last_result = 'BAD_NONCE';
                            $result = 99;
                        }
                        elseif($yubiotp != $response['otp'])
                        {
                            $this->_yubicloud_last_result = 'OTP_IS_DIFFERENT';
                            $result = 99;
                        }
                        elseif ((($response['t_utc'] - $this->_yubicloud_max_time_window) > $response['now_utc']) ||
                                (($response['t_utc'] + $this->_yubicloud_max_time_window) < $response['now_utc'])
                               )
                        {
                            $this->_yubicloud_last_result = 'OUT_OF_TIME_WINDOW';
                            $result = 99;
                        }
                        else
                        {
                            $this->_yubicloud_last_result = $response['status'];

                            switch ($response['status'])
                            {
                                case 'OK':
                                    $result = 0;
                                    break;
                                case 'BAD_OTP':
                                    $result = 23;
                                    break;
                                case 'REPLAYED_OTP':
                                case 'REPLAYED_REQUEST':
                                    $result = 26;
                                    break;
                                case 'BAD_SIGNATURE':
                                case 'MISSING_PARAMETER':
                                case 'NO_SUCH_CLIENT':
                                case 'OPERATION_NOT_ALLOWED':
                                case 'BACKEND_ERROR':
                                case 'NOT_ENOUGH_ANSWERS':
                                default:
                                    $result = 99;
                            }
                        }
                        if (99 != $result)
                        {
                            break;
                        }
                    }
                }
            }
        }
        return $result;
    }


    function IsModHex($modhex)
    {
        $result = FALSE;
        if (0 == (strlen($modhex) % 2))
        {
            for ($loop = 0; $loop < strlen($modhex); $loop++)
            {
                $value = strpos($this->_yubico_modhex_chars, strtolower($modhex[$loop]));
                if (FALSE === $value)
                {
                    return FALSE;
                }
            }
            $result = TRUE;
        }
		return $result;		
    }


	function HexToModHex($hexa)
    {
        $result = '';
        if (0 == (strlen($hexa) % 2))
        {
            for ($loop = 0; $loop < strlen($hexa); $loop++)
            {
                $value = hexdec(strtolower($hexa[$loop]));
                if ($value > 15)
                {
                    return FALSE;
                }
                $result.= $this->_yubico_modhex_chars[$value];
            }
        }
        else
        {
            $result = FALSE;
        }
		return $result;		
	}
    
    
	function ModHexToHex($modhex)
    {
        $result = '';
        if (0 == (strlen($modhex) % 2))
        {
            for ($loop = 0; $loop < strlen($modhex); $loop++)
            {
                $value = strpos($this->_yubico_modhex_chars, strtolower($modhex[$loop]));
                if (FALSE === $value)
                {
                    return FALSE;
                }
                $result.= dechex($value);
            }
        }
        else
        {
            $result = FALSE;
        }
		return $result;		
	}
}
?>