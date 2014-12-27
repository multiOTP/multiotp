<?php

class MultiotpAspSms
/**
 * @class     MultiotpAspSms
 * @brief     SMS message using ASPSMS infrastructure.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   4.2.4.3
 * @date      2014-06-12
 * @since     2014-03-13
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

    function MultiotpAspSms($userkey,
	                        $password
						   )
    {
        $this->userkey        = $userkey;
        $this->password       = $password;
        $this->originator     = "multiOTP";
        $this->recipient      = '';
        $this->server_timeout = 5;

        $this->servers = array("xml1.aspsms.com:5061",
							   "xml1.aspsms.com:5098",
							   "xml2.aspsms.com:5061",
                               "xml2.aspsms.com:5098"
							  );
    }


    function setOriginator($originator)
    {
        $this->originator = $originator;
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


    function setServerTimeout($timeout)
    {
        $this->server_timeout = $timeout;
    }


    function setContent($content)
    {
		$this->content = $content;
    }


    function sendSMS()
    {
		$result = 0;
/*
		$ucs2_content = bin2hex(mb_convert_encoding($this->content, 'UCS-2', 'auto'));
*/
		$sms = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n".
			   "<aspsms>\r\n".
			   "<Userkey>".$this->userkey."</Userkey>\r\n".
			   "<Password>".$this->password."</Password>\r\n".
			   "<AffiliateId>208355</AffiliateId>\r\n".
			   "<Recipient>\r\n<PhoneNumber>".htmlspecialchars($this->recipient, ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</PhoneNumber>\r\n</Recipient>\r\n".
			   "<Originator>".htmlspecialchars($this->originator, ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</Originator>\r\n".
			   "<MessageData>".htmlspecialchars($this->content, ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</MessageData>\r\n".
			   "<Action>SendTextSMS</Action>\r\n".
			   "</aspsms>\r\n";
/*
	UCS2:
			   "<XSer>020108</XSer>\r\n".
			   "<MessageData>".$ucs2_content."</MessageData>\r\n".
			   "<Action>SendBinaryData</Action>\r\n".

	Standard:
			   "<MessageData>".htmlspecialchars($this->content, ENT_QUOTES | ENT_HTML401, 'ISO-8859-1')."</MessageData>\r\n".
			   "<Action>SendTextSMS</Action>\r\n".
*/
			   
		$this->raw_content = $sms;

        foreach ($this->servers as $server)
        {
            list($host, $port) = explode(":", $server);
			$fp = fsockopen($host, $port, $errno, $errdesc, $this->server_timeout);
			if ($fp)
			{
				fputs($fp, "POST /xmlsvr.asp HTTP/1.0\r\n");
				fputs($fp, "Content-Type: text/xml\r\n");
				fputs($fp, "Content-Length: ".strlen($sms)."\r\n");
				fputs($fp, "\r\n");
				fputs($fp, $sms);

				$reply = '';
				while (!feof($fp))
				{
					$reply.= fgets($fp, 1024);
				}

				fclose($fp);

				if (FALSE !== strpos($reply,'<ErrorCode>1'))
				{
					$result = 1;
					break;
				}
			}
        }
        return $result;
    }
}

?>