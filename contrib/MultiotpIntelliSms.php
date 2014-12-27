<?php

class MultiotpIntelliSms
/**
 * @class     MultiotpIntelliSms
 * @brief     SMS message using IntelliSMS infrastructure.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   4.2.4
 * @date      2014-03-30
 * @since     2013-05-14
 */
{
    var $content;
    var $originator;
    var $password;
    var $recipient;
    var $server_timeout;
    var $servers;
    var $userkey;

    function MultiotpIntelliSms($userkey, $password)
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
        $this->servers = array("ssl://www.intellisoftware.co.uk:443",
                               "ssl://www.intellisoftware2.co.uk:443" );
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

        if ('00' == substr($recipient,0,2))
        {
            $recipient = substr($recipient,2);
        }
        $this->recipient = array( "number" => $recipient);
    }

    function setContent($content)
    {
        $this->content = $content;
    }

    function getOneSendContent($content)
    {
        $send_data = "";
        
        $send_data = $send_data.(("" == $send_data)?"":"&").'username='.urlencode($this->userkey);
        $send_data = $send_data.(("" == $send_data)?"":"&").'password='.urlencode($this->password);

        $originator = "";
        if ($this->originator != "")
        {
            $send_data = $send_data.(("" == $send_data)?"":"&").'from='.urlencode($this->originator);
        }

        $recipient = "";
        if (count($this->recipient) > 0)
        {
            $send_data = $send_data.(("" == $send_data)?"":"&").'to='.urlencode($this->recipient["number"]);
        }
        
        $send_data = $send_data.(("" == $send_data)?"":"&").'text='.urlencode($content);

        return $send_data;
    }

    function sendSMS()
    {
        return $this->send($this->getOneSendContent($this->content));
    }

    function send($msg)
    {
        $result = 0;
        foreach ($this->servers as $server)
        {
            // list($host, $port) = explode(":", $server);
            
            $pos = strpos($server, '://');
            if (FALSE === $pos)
            {
                $protocol = '';
            }
            else
            {
                switch (strtolower(substr($server,0,$pos)))
                {
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
            if (FALSE === $pos)
            {
                $host = $server;
                $url = '/';
            }
            else
            {
                $host = substr($server,0,$pos);
                $url = substr($server,$pos); // And not +1 as we want the / at the beginning
            }
            
            $pos = strpos($host, ':');
            if (FALSE === $pos)
            {
                $port = 80;
            }
            else
            {
                $port = substr($host,$pos+1);
                $host = substr($host,0,$pos);
            }
            
            $result = trim($this->sendToServer($msg, $protocol.$host, $port));
            if (substr($result,0,2) == "ID")
            {
                return $result;
            }
        }
        return $result;
    }

    function sendToServer($msg, $host, $port)
    {
        $errno = 0;
        $errdesc = 0;
        $fp = fsockopen($host, $port, $errno, $errdesc, $this->server_timeout); // 'ssl://'.$host
        if ($fp)
        {
            fputs($fp, "POST /smsgateway/sendmsg.aspx HTTP/1.0\r\n");
            fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-Length: ".strlen($msg)."\r\n");
            fputs($fp, "User-Agent: multiOTP\r\n");
            fputs($fp, "Host: ".$host."\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $msg."\r\n");
            fputs($fp, "\r\n");

            $reply = '';
            while (!feof($fp))
            {
                $reply.= fgets($fp, 1024);
            }

            fclose($fp);

            $reply_array = split("\n", $reply);
            $reply = '';

            $end_of_header = FALSE;
            
            // loop until we have an empty line, and than take the result
            foreach ($reply_array as $reply_one)
            {
                if ($end_of_header)
                {
                    $reply.= $reply_one;
                }
                elseif ("" == trim($reply_one))
                {
                    $end_of_header = TRUE;
                }
            }

            $result = $reply;
        }
        else
        {
            $result = "";
        }
        return $result;
    }
}

?>