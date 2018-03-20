<?php

class MultiotpIntelliSms
/**
 * @class     MultiotpIntelliSms
 * @brief     SMS message using IntelliSMS infrastructure.
 *
 * http://www.intellisoftware.co.uk/sms-gateway/http-interface/
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.1.0.6
 * @date      2018-02-26
 * @since     2013-05-14
 *
 * Change Log
 *
 *   2018-02-26 5.1.0.6 SysCo/al __construct instead of the name of the class
 */
{
    var $content;
    var $originator;
    var $password;
    var $recipient;
    var $server_timeout;
    var $servers;
    var $userkey;


    function __construct($userkey, $password)
    {
        $this->userkey = $userkey;
        $this->password = $password;
        $this->recipient = array();
        $this->originator = "multiOTP";
        $this->server_timeout = 5;

        $this->useRegularServer();
    }


    function useRegularServer()
    {
        $this->servers = array("www.intellisoftware.co.uk:80",
                               "www.intellisoftware2.co.uk:80" );
    }


    function useSslServer()
    {
        /* Not working yet, see on IntelliSoftware side
        $this->servers = array("ssl://www.intellisoftware.co.uk:443",
                               "ssl://www.intellisoftware2.co.uk:443" );
        */
        $this->useRegularServer();
        return false;
    }


    function setTimeout($timeout)
    {
        $this->server_timeout = $timeout;
    }


    function setOriginator($originator)
    {
        $this->originator = $originator;
    }


    function setRecipient($r)
    {
        $recipient = $r;
        $recipient = str_replace(' ','',$recipient);
        $recipient = str_replace('(','',$recipient);
        $recipient = str_replace(')','',$recipient);
        $recipient = str_replace('+','00',$recipient);

        if ('00' == substr($recipient,0,2)) {
            $recipient = substr($recipient,2);
        }
        $this->recipient = array( "number" => $recipient);
    }


    function setContent($content)
    /*
     * The content is automatically converted from ISO to UTF-8 if needed
     */
    {
        $text = $content;
        $encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
        if ("UTF-8" != $encoding) {
            $text = utf8_encode($text);
        }
        $this->content = $text;
    }


    function getOneSendContent($content)
    {
        $send_data = "";
        
        $send_data = $send_data.(("" == $send_data)?"":"&").'username='.urlencode($this->userkey);
        $send_data = $send_data.(("" == $send_data)?"":"&").'password='.urlencode($this->password);

        $originator = "";
        if ($this->originator != "") {
            $send_data = $send_data.(("" == $send_data)?"":"&").'from='.urlencode($this->originator);
        }

        $recipient = "";
        if (count($this->recipient) > 0) {
            $send_data = $send_data.(("" == $send_data)?"":"&").'to='.urlencode($this->recipient["number"]);
        }
        
        $send_data = $send_data.(("" == $send_data)?"":"&").'text='.urlencode($content);

        // Optional, mesage type (1=SMS; 6 = voice-SMS)
        $send_data = $send_data.(("" == $send_data)?"":"&").'type=1';

        // Optional, maximum number of concatenated SMS messages
        // $send_data = $send_data.(("" == $send_data)?"":"&").'maxconcat=1';

        return $send_data;
    }


    function getOneBalanceContent($content)
    {
        $balance_data = "";
        
        $balance_data = $balance_data.(("" == $balance_data)?"":"&").'username='.urlencode($this->userkey);
        $balance_data = $balance_data.(("" == $balance_data)?"":"&").'password='.urlencode($this->password);

        return $balance_data;
    }


    function getCredits()
    {
        $result = $this->send($this->getOneBalanceContent($this->content), "/smsgateway/getbalance.aspx");
        // BALANCE:100         The number of remaining credits follows 'BALANCE:'
        // ERR:LOGIN_INVALID   Username or Password is invalid
        // ERR:NO_XXXXXXXXXXX  A mandatory parameter is missing
        // ERR:INTERNAL_ERROR  Unable to process request at this time
        if (0 === strpos($result, "BALANCE")) {
            list($state, $result) = explode(":", $result);
        } else {
            $result = 0;
        }
        return $result;
    }


    function sendSMS($content = '')
    {
        if ('' != $content) {
            $this->setContent($content);
        }

        return $this->send($this->getOneSendContent($this->content));
    }


    function send($msg, $path = "")
    {
        $result = 0;
        foreach ($this->servers as $server) {
            // list($host, $port) = explode(":", $server);
            
            $pos = strpos($server, '://');
            if (FALSE === $pos) {
                $protocol = '';
            } else {
                switch (strtolower(substr($server,0,$pos))) {
                    case 'https':
                    case 'ssl':
                        $protocol = 'ssl://';
                        break;
                    case 'tls':
                        $protocol = 'tls://';
                        break;
                    default:
                        $protocol = '';
                        break;
                }
                $server = substr($server,$pos+3);
            }
            
            $pos = strpos($server, '/');
            if (FALSE === $pos) {
                $host = $server;
                $url = '/';
            } else {
                $host = substr($server,0,$pos);
                $url = substr($server,$pos); // And not +1 as we want the / at the beginning
            }
            
            $pos = strpos($host, ':');
            if (FALSE === $pos) {
                $port = 80;
            } else {
                $port = substr($host,$pos+1);
                $host = substr($host,0,$pos);
            }
            
            if ("" != $path) {
                $result = trim($this->sendToServer($msg, $protocol.$host, $port, $path));
            } else {
                $result = trim($this->sendToServer($msg, $protocol.$host, $port));
            }
            if (substr($result,0,2) == "ID") {
                return $result;
            }
        }
        return $result;
    }


    function sendToServer($msg, $host, $port, $path = "/smsgateway/sendmsg.aspx")
    {
        $errno = 0;
        $errdesc = 0;
        $protocol = "";

        if (function_exists("stream_socket_client")) {
            $sslContext = stream_context_create(
                array('ssl' => array(
                      'verify_peer'         => false,
                      'verify_peer_name'    => false,
                      'disable_compression' => true,
                      'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4')));
            $fp = @stream_socket_client($protocol.$host.":".$port, $errno, $errdesc, $this->server_timeout, STREAM_CLIENT_CONNECT, $sslContext);
        } else {
            $fp = @fsockopen($protocol.$host, $port, $errno, $errdesc, $this->server_timeout);
        }

        if ($fp) {
            fputs($fp, "POST $path HTTP/1.0\r\n");
            fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-Length: ".strlen($msg)."\r\n");
            fputs($fp, "User-Agent: multiOTP\r\n");
            fputs($fp, "Host: ".$host."\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $msg."\r\n");
            fputs($fp, "\r\n");

            $reply = '';
            while (!feof($fp)) {
                $reply.= fgets($fp, 1024);
            }

            fclose($fp);

            $reply_array = explode ("\n", $reply);
            $reply = '';

            $end_of_header = FALSE;
            
            // loop until we have an empty line, and than take the result
            foreach ($reply_array as $reply_one) {
                if ($end_of_header) {
                    $reply.= $reply_one;
                } elseif ("" == trim($reply_one)) {
                    $end_of_header = TRUE;
                }
            }

            $result = $reply;
        } else {
            $result = "";
        }
        return $result;
    }
}

?>