<?php

class MultiotpClickatell
/**
 * @class     MultiotpClickatell
 * @brief     SMS message using Clickatell infrastructure.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   4.3.0.1
 * @date      2015-05-11
 * @since     2013-05-14
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


    function MultiotpClickatell($userkey, $password, $api_id)
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
        $this->servers = array("api.clickatell.com:80" );
    }


    function useSslServer()
    {
        $this->servers = array("ssl://api.clickatell.com:443" );
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

        if ('00' == substr($recipient,0,2))
        {
            $recipient = substr($recipient,2);
        }
        $this->recipient = array( "number" => $recipient, "transaction" => $id);
    }


    function setContent($content)
    {
        $this->content = $content;
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
        if ($this->originator != "")
        {
            $originator = sprintf("<from>%s</from>", $this->originator);
        }

        $recipient = "";
        if (count($this->recipient) > 0)
        {
            if ($this->recipient["transaction"] != null)
            {
                $recipient .= sprintf("<to>%s</to>".
                "<climsgid>%s</climsgid>",
                htmlspecialchars($this->recipient["number"], ENT_QUOTES | ENT_HTML401, 'UTF-8'),
                htmlspecialchars($this->recipient["transaction"], ENT_QUOTES | ENT_HTML401, 'UTF-8'));
            }
            else
            {
                $recipient .= sprintf("<to>%s</to>",
                htmlspecialchars($this->recipient["number"], ENT_QUOTES | ENT_HTML401, 'UTF-8'));
            }
        }

       return sprintf("data=<clickAPI>".
               "<sendMsg>".
               "<api_id>".$this->api_id."</api_id>".
               "<user>".$this->userkey."</user>".
               "<password>".$this->password."</password>".
               $recipient.
               "<text>".$content."</text>".
               $originator.
               "</sendMsg>".
               "</clickAPI>");
    }

    function sendSMS()
    /*
     * Result: 1=ok / 0=ko
     */
    {
        return $this->send($this->getOneSendXML(htmlspecialchars($this->content, ENT_QUOTES | ENT_HTML401, 'UTF-8')));
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

        foreach ($this->servers as $server)
        {
            list($host, $port) = explode(":", $server);
            $result = $this->sendToServer($msg, $host, $port);
            if (1 == $result)
            {
                return $result;
            }
        }
        return $result;
    }

    function sendToServer($msg, $host, $port)
    {
        $result = 0;

        $errno = 0;
        $errdesc = 0;
        $fp = fsockopen($host, $port, $errno, $errdesc, $this->server_timeout);
        if ($fp)
        {
            fputs($fp, "POST /xml/xml HTTP/1.0\r\n");
            fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-Length: ".strlen($msg)."\r\n");
            fputs($fp, "User-Agent: multiOTP\r\n");
            fputs($fp, "Host: ".$host."\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $msg);

            $reply = '';
            while (!feof($fp))
            {
                $reply.= fgets($fp, 1024);
            }
            $this->setReply($reply);

            fclose($fp);
            
            $result = ((FALSE !== strpos($reply,'<apiMsgId>')) ||((FALSE !== strpos($reply,'<ok>'))))?'1':'0';
        }

        return $result;
    }
}

?>