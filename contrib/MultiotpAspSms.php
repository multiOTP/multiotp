<?php

class MultiotpAspSms
/**
 * @class     MultiotpAspSms
 * @brief     SMS message using ASPSMS infrastructure.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.1.0.6
 * @date      2018-02-26
 * @since     2014-03-13
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
	var $raw_content;
    var $reply;

    function __construct(
        $userkey,
        $password
    ) {
        $this->userkey        = $userkey;
        $this->password       = $password;
        $this->originator     = "multiOTP";
        $this->recipient      = '';
        $this->server_timeout = 5;
        $this->reply          = '';

        $this->useRegularServer();
    }


    function useRegularServer()
    {
        $this->servers = array("xml1.aspsms.com:5061",
                               "xml1.aspsms.com:5098",
                               "xml2.aspsms.com:5061",
                               "xml2.aspsms.com:5098");
    }


    function useSslServer()
    {
        $this->useRegularServer();
        return false;
    }


    function setUserkey($userkey)
    {
        $this->userkey = $userkey;
    }


    function getUserkey()
    {
        return $this->userkey;
    }


    function setPassword($password)
    {
        $this->password = $password;
    }


    function getPassword()
    {
        return $this->password;
    }


    function setOriginator($originator)
    {
        $this->originator = $originator;
    }


    function getOriginator()
    {
        return $this->originator;
    }


    function setRecipient($recipient)
    {
        $string = $recipient;
        $string = str_replace(' ','',$string);
        $string = str_replace('(','',$string);
        $string = str_replace(')','',$string);
        $string = str_replace('+','00',$string);

        $this->recipient = $string;
    }


    function getRecipient()
    {
        return $this->recipient;
    }


    function setServerTimeout($timeout)
    {
        $this->server_timeout = $timeout;
    }


    function getServerTimeout()
    {
        return $this->server_timeout;
    }


    function setContent($content)
    /*
     * The content is automatically converted from UTF-8 to ISO if needed
     */
    {
		$text = $content;
		$encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
		if ("UTF-8" == $encoding) {
			$text = utf8_decode($text);
		}
		$this->content = $text;
    }


    function getContent()
    {
		return $this->content;
    }


    function setRawContent($raw_content)
    {
		$this->raw_content = $raw_content;
    }


    function setReply($reply)
    {
		$this->reply = $reply;
    }


    function getReply()
    {
		return $this->reply;
    }


    function getRawContent()
    {
		return $this->raw_content;
    }


    function sendSMS($content = '')
    /*
     * Result: 1=ok / 0=ko
     */
    {
        $result = 0;

        if ('' != $content) {
            $this->setContent($content);
        }

        /*
            $ucs2_content = bin2hex(mb_convert_encoding($this->getContent(), 'UCS-2', 'auto'));
        */

		$raw_content = "<Recipient>\r\n<PhoneNumber>".htmlspecialchars($this->getRecipient(), ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</PhoneNumber>\r\n</Recipient>\r\n".
                       "<Originator>".htmlspecialchars($this->getOriginator(), ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</Originator>\r\n".
                       "<MessageData>".htmlspecialchars($this->getContent(), ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</MessageData>\r\n".
                       "<Action>SendTextSMS</Action>\r\n";
        /*
            UCS2:
			   "<XSer>020108</XSer>\r\n".
			   "<MessageData>".$ucs2_content."</MessageData>\r\n".
			   "<Action>SendBinaryData</Action>\r\n".

            Standard:
			   "<MessageData>".htmlspecialchars($this->getContent(), ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</MessageData>\r\n".
			   "<Action>SendTextSMS</Action>\r\n".
        */

        $this->setRawContent($raw_content);
		$result = $this->sendToServer();
        
        return $result;
    }


    function getCredits()
    {
        $raw_content = "<Action>ShowCredits</Action>\r\n";
        $this->setRawContent($raw_content);
        $credits = '';
		if (1 == $this->sendToServer()) {
            $reply = $this->getReply();
            if (FALSE !== strpos($reply,"<Credits>")) {
                $begin_credits_pos = strpos($reply,"<Credits>");
                $end_credits_pos = strpos($reply,"</Credits>");
                $credits = substr($reply, $begin_credits_pos + strlen("<Credits>"), $end_credits_pos - $begin_credits_pos - strlen("<Credits>"));
            }
        }
        return $credits;
    }


    function sendToServer($raw_content = '')
    /*
     * Result: 1=ok / 0=ko
     */
    {
        $result = 0;

        if ('' != $raw_content) {
            $this->setRawContent($raw_content);
        }
        $full_content = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n".
                        "<aspsms>\r\n".
                        "<Userkey>".$this->getUserkey()."</Userkey>\r\n".
                        "<Password>".$this->getPassword()."</Password>\r\n".
                        "<AffiliateId>208355</AffiliateId>\r\n".
                        $this->getRawContent().
                        "</aspsms>\r\n";

        $actual_timeout = $this->getServerTimeout();
        foreach ($this->servers as $server) {
            list($host, $port) = explode(":", $server);
            $protocol = "";

            if (function_exists("stream_socket_client")) {
                $sslContext = stream_context_create(
                    array('ssl' => array(
                          'verify_peer'         => false,
                          'verify_peer_name'    => false,
                          'disable_compression' => true,
                          'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4')));
                $fp = @stream_socket_client($protocol.$host.":".$port, $errno, $errdesc, $this->getServerTimeout(), STREAM_CLIENT_CONNECT, $sslContext);
            } else {
                $fp = @fsockopen($host, $port, $errno, $errdesc, $this->getServerTimeout());
            }

			if ($fp) {
				fputs($fp, "POST /xmlsvr.asp HTTP/1.0\r\n");
				fputs($fp, "Content-Type: text/xml\r\n");
				fputs($fp, "Content-Length: ".strlen($full_content)."\r\n");
				fputs($fp, "\r\n");
				fputs($fp, $full_content);

				$reply = '';
				while (!feof($fp)) {
					$reply.= fgets($fp, 1024);
				}
                $this->setReply($reply);

				fclose($fp);

				if (FALSE !== strpos($reply,'<ErrorCode>1')) {
					$result = 1;
					break;
				}
			}
        }
        return $result;
    }
}

?>