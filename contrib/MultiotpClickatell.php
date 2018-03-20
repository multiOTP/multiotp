<?php

class MultiotpClickatell
/**
 * @class     MultiotpClickatell
 * @brief     SMS message using Clickatell infrastructure.
 *
 * https://www.clickatell.com/downloads/xml/Clickatell_XML.pdf
 * https://www.clickatell.com/downloads/http/Clickatell_HTTP.pdf
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.1.0.6
 * @date      2018-02-26
 * @since     2013-05-14
 *
 * Change Log
 *
 *   2018-02-26 5.1.0.6 SysCo/al __construct instead of the name of the class
 *   2016-10-31 5.0.2.6 SysCo/al Using stream_socket_client with SSL context
 *   2016-06-09 4.3.4.3 SysCo/al Enhanced special chars supports
 *   2015-08-28 4.3.2.9 SysCo/al Enhanced FQDN support, SSL is now working
 */
{
    var $api_id;
    var $content;
    var $originator;
    var $password;
    var $recipient;
    var $server_timeout;
    var $servers;
    var $session_id;
    var $userkey;
    var $reply;


    function __construct($userkey, $password, $api_id)
    {
        $this->userkey = $userkey;
        $this->password = $password;
        $this->api_id = $api_id;
        $this->recipient = array();
        $this->originator = "multiOTP";
        $this->server_timeout = 5;

        $this->useRegularServer();
    }


    function useRegularServer()
    {
        $this->servers = array("api.clickatell.com" );
        return TRUE;
    }


    function useSslServer()
    {
        $this->servers = array("ssl://api.clickatell.com:443" );
        return TRUE;
    }


    function setTimeout($timeout)
    {
        $this->server_timeout = $timeout;
    }


    function setOriginator($originator)
    {
        $this->originator = $originator;
    }


    function setRecipient($r, $id = null)
    {
        $recipient = $r;
        $recipient = str_replace(' ','',$recipient);
        $recipient = str_replace('(','',$recipient);
        $recipient = str_replace(')','',$recipient);
        $recipient = str_replace('+','00',$recipient);

        if ('00' == substr($recipient,0,2)) {
            $recipient = substr($recipient,2);
        }
        $this->recipient = array( "number" => $recipient, "transaction" => $id);
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


    function setReply($reply)
    {
		$this->reply = $reply;
    }


    function getReply()
    {
		return $this->reply;
    }


    function getAuthXML()
    {
        return sprintf("data=<clickAPI>".
               "<auth>".
               "<api_id>".$this->api_id."</api_id>".
               "<user>".$this->userkey."</user>".
               "<password>".$this->password."</password>".
               "</auth>".
               "</clickAPI>");
    }

    function getOneSendXML($content)
    {
        $originator = "";
        if ($this->originator != "") {
            $originator = sprintf("<from>%s</from>", $this->originator);
        }

        $recipient = "";
        if (count($this->recipient) > 0) {
            if ($this->recipient["transaction"] != null) {
                $recipient .= sprintf("<to>%s</to>".
                "<climsgid>%s</climsgid>",
                ($this->recipient["number"]),
                ($this->recipient["transaction"]));
            }
            else {
                $recipient .= sprintf("<to>%s</to>",
                ($this->recipient["number"]));
            }
        }

       return ("data=<clickAPI>".
               "<sendMsg>".
               "<api_id>".$this->api_id."</api_id>".
               "<user>".$this->userkey."</user>".
               "<password>".$this->password."</password>".
               $recipient.
               "<text><![CDATA[".str_replace('&', '%26', $content)."]]></text>".
               $originator.
               "</sendMsg>".
               "</clickAPI>");
    }

    function sendSMS()
    /*
     * Result: 1=ok / 0=ko
     */
    {
        return $this->send($this->getOneSendXML(($this->content)));
    }


    function getCredits()
    {
        $credits = '';

        $full_content = sprintf("data=<clickAPI>".
                                "<getBalance>".
                                "<api_id>".$this->api_id."</api_id>".
                                "<user>".$this->userkey."</user>".
                                "<password>".$this->password."</password>".
                                "</getBalance>".
                                "</clickAPI>");

		if (1 == $this->send($full_content)) {
            $reply = $this->getReply();
            if (FALSE !== strpos($reply,"<ok>")) {
                $begin_ok_pos = strpos($reply,"<ok>");
                $end_ok_pos = strpos($reply,"</ok>");
                $credits = substr($reply, $begin_ok_pos + strlen("<ok>"), $end_ok_pos - $begin_ok_pos - strlen("<ok>"));
            }
        }
        return $credits;
    }


    function send($msg)
    {
        $result = 0;

        foreach ($this->servers as $server) {
            $server_array = parse_url($server);
            
            $port = 80;

            switch (isset($server_array["scheme"])?$server_array["scheme"]:'') {
                case 'https':
                case 'ssl':
                    $protocol = 'ssl://';
                    $port = 443;
                    break;
                case 'tls':
                    $protocol = 'tls://';
                    $port = 443;
                    break;
                default:
                    $protocol = '';
                    break;
            }

            $host = isset($server_array["host"])?$server_array["host"]:(isset($server_array["path"])?$server_array["path"]:'');

            if (isset($server_array["port"])) {
                $port = intval($server_array["port"]);
            }

            $result = $this->sendToServer($msg, $protocol.$host, $port);
            if (1 == $result) {
                return $result;
            }
        }
        return $result;
    }

    function sendToServer($msg, $protocol_host, $forced_port = '')
    {
        $server_array = parse_url($protocol_host);

        $port = 80;

        switch (isset($server_array["scheme"])?$server_array["scheme"]:'') {
            case 'https':
            case 'ssl':
                $protocol = 'ssl://';
                $port = 443;
                break;
            case 'tls':
                $protocol = 'tls://';
                $port = 443;
                break;
            default:
                $protocol = '';
                break;
        }

        $host = isset($server_array["host"])?$server_array["host"]:(isset($server_array["path"])?$server_array["path"]:'');

        if (isset($server_array["port"])) {
            $port = intval($server_array["port"]);
        }
        
        if (intval($forced_port) > 0) {
            $port = intval($forced_port);
        }
        
        $result = 0;
        $errno = 0;
        $errdesc = 0;

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
            $temp = "POST /xml/xml HTTP/1.0\r\n";
            $temp.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $temp.= "Content-Length: ".strlen($msg)."\r\n";
            $temp.= "User-Agent: multiOTP\r\n";
            $temp.= "Host: ".$host."\r\n";
            $temp.= "\r\n";
            $temp.= $msg;
            $temp.= "\r\n";

            fputs($fp, $temp);
                    
            $reply = '';
            while (!feof($fp)) {
                $reply.= fgets($fp, 1024);
            }
            $this->setReply($reply);

            fclose($fp);
            
            $result = ((FALSE !== strpos($reply,'<apiMsgId>')) || ((FALSE !== strpos($reply,'<ok>'))))?'1':'0';
        }

        return $result;
    }
}

?>