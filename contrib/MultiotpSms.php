<?php

class MultiotpSms
/**
 * @class     MultiotpSms
 * @brief     SMS message using any SMS Provider.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.6.1.4
 * @date      2019-10-23
 * @since     2018-10-09
 *
 * Predefined providers:
 *       afilnet: Afilnet (HTTPS), https://www.afilnet.com/
 *        aspsms: aspsms.com (XML), https://www.aspsms.com/
 *    clickatell: Clickatell (legacy XML), https://archive.clickatell.com/developers/2015/10/08/xml/
 *   clickatell2: Clickatell (HTTPS), https://www.clickatell.com/
 *         ecall: eCall.ch (HTTPS), https://www.ecall.ch/
 *    intellisms: IntelliSMS.co.uk (HTTPS), https://www.intellisms.co.uk/
 *         nexmo: Nexmo (HTTPS), https://www.nexmo.com/
 *        nowsms: NowSMS.com (on-premises), https://www.nowsms.com/
 *      smseagle: SMSEagle (hardware gateway), https://www.smseagle.eu/
 *      swisscom: Swisscom LA (REST-JSON), https://messagingproxy.swisscom.ch:4300/rest/1.0.0/
 *
 *
 * Existing variables for URL and send_template:
 *   %api_id
 *   %from
 *   %ip
 *   %msg
 *   %password or %pass
 *   %port
 *   %to
 *   %username or %user
 *
 *
 * Examples:
 *
 *  // Predefined provider
 *  $sms_message = new MultiotpSms(array('provider'        => "clickatell",
 *                                       'api_id'          => "my_api_id",
 *                                       'username'        => "my_username",
 *                                       'password'        => "my_password",
 *                                       'from'            => "Sample",
 *                                       'to'              => "+41791234567",
 *                                       'msg'             => "Here is my message"
 *                                      ));
 *  echo ($sms_message->sendSMS() ? "OK" : "KO");
 *  echo "<br />\n";
 *  echo "Last reply info: ".$sms_message->getLastReplyInfo();
 *
 *  // Custom provider
 *  $sms_message2 = new MultiotpSms(array('provider'        => "my_local_sms_server",
 *                                        'username'        => "my_username",
 *                                        'password'        => "my_password",
 *                                        'url'             => "https://1.2.3.4/?PhoneNumber=%to&Text=%msg",
 *                                        'method'          => "GET",
 *                                        'encoding'        => "UTF",
 *                                        'status_success'  => "20",
 *                                        'content_success' => "Message Submitted",
 *                                        'basic_auth'      => true,
 *                                        'to'              => "+41791234567",
 *                                        'msg'             => "Here is my message"
 *                                       ));
 *  echo ($sms_message2->sendSMS() ? "OK" : "KO");
 *  echo "<br />\n";
 *  echo "Last reply info: ".$sms_message2->getLastReplyInfo();
 *
 *
 * Change Log
 *
 *   2019-10-23 5.4.0.3 SysCo/al Define all parameters for preconfigured providers
 *   2018-11-02 5.4.0.3 SysCo/al Adding and testing preconfigured providers
 *   2018-10-09 5.4.0.2 SysCo/al First implementation
 */
{
    // Standard values (for predefined providers)
    var $provider;
    var $api_id;
    var $ip;
    var $port;
    var $username;
    var $password;
    var $from;
    var $to;
    var $msg;
    
    // Custom values (for customized provider)
    var $url;
    var $send_template;
    var $method;
    var $encoding;
    var $status_success;
    var $content_success;
    var $no_double_zero;
    var $basic_auth;
    var $content_encoding;
    
    // Timeout
    var $timeout;

    // Debug flag
    var $debug;

    // This is automatically activated if needed
    var $encode_ampersand;

    var $last_result;
    var $reply_status;
    var $reply_content;


    function __construct(
        $config_array = array()
    ) {
        $this->resetValues();
        $this->provider = isset($config_array['provider']) ? trim($config_array['provider']) : '';
        if (isset($config_array['url'])) { $this->url = trim($config_array['url']); }
        if (isset($config_array['send_template'])) { $this->send_template = $config_array['send_template']; }
        if (isset($config_array['api_id'])) { $this->api_id = $config_array['api_id']; }
        if (isset($config_array['ip'])) { $this->ip = $config_array['ip']; }
        if (isset($config_array['port'])) { $this->port = $config_array['port']; }
        if (isset($config_array['username'])) { $this->username = $config_array['username']; }
        if (isset($config_array['password'])) { $this->password = $config_array['password']; }
        if (isset($config_array['from'])) { $this->from = $config_array['from']; }
        if (isset($config_array['to'])) { $this->setTo($config_array['to']); }
        if (isset($config_array['msg'])) { $this->setMsg($config_array['msg']); }
        if (isset($config_array['method'])) { $this->method = $config_array['method']; }
        if (isset($config_array['encoding'])) { $this->encoding = $config_array['encoding']; }
        if (isset($config_array['status_success'])) { $this->status_success = $config_array['status_success']; }
        if (isset($config_array['content_success'])) { $this->content_success = $config_array['content_success']; }
        if (isset($config_array['no_double_zero'])) { $this->no_double_zero = (TRUE == $config_array['no_double_zero']); }
        if (isset($config_array['basic_auth'])) { $this->basic_auth = (TRUE == $config_array['basic_auth']); }
        if (isset($config_array['content_encoding'])) { $this->content_encoding = $config_array['content_encoding']; }
        if (isset($config_array['debug'])) { $this->debug = (TRUE == $config_array['debug']); }
        if (isset($config_array['timeout'])) { $this->timeout = intval($config_array['timeout']); }
        if (isset($config_array['encode_ampersand'])) { $this->encode_ampersand = (TRUE == $config_array['encode_ampersand']); }
        if ($this->timeout < 1) {
            $this->timeout = 5;
        }
        if ('' != $this->provider) { $this->setProvider($this->provider); }
    }


    function resetValues($reset_provider = TRUE)
    {
        if ($reset_provider) {
            $this->provider = "";
        }
        $this->url = "";
        $this->send_template = "";
        $this->api_id = "";
        $this->ip = "";
        $this->port = "";
        $this->username = "";
        $this->password = "";
        $this->from = "";
        $this->to = "";
        $this->msg = "";
        $this->method = "POST-XML";
        $this->encoding = "UTF";
        $this->status_success = "";
        $this->content_success = "";
        $this->timeout = 5;
        $this->last_result = FALSE;
        $this->reply_status = "";
        $this->reply_content = "";
        $this->no_double_zero = FALSE;
        $this->basic_auth = FALSE;
        $this->content_encoding = "";

        $this->encode_ampersand = FALSE;
    }


    function setNoDoubleZero($value)
    {
        $this->no_double_zero = (TRUE == $value);
    }


    function getNoDoubleZero()
    {
        return (TRUE == $this->no_double_zero);
    }


    function setEncoding($encoding)
    {
        $this->encoding = mb_strtoupper(mb_substr($encoding, 0, 3));
    }


    function getEncoding()
    {
        $encoding = "UTF-8";
        if ('UTF' == mb_strtoupper(mb_substr($this->encoding, 0, 3))) {
            $encoding = "UTF-8";
        } elseif ('ISO' == mb_strtoupper(mb_substr($this->encoding, 0, 3))) {
            $encoding = "ISO-8859-1";
        }
        return $encoding;
    }


    function setProvider($provider)
    {
        switch ($provider) {
            case 'afilnet':
                $this->url = "https://www.afilnet.com/api/http/?class=sms&method=sendsms&user=%user&password=%pass&from=%from&to=%to&sms=%msg";
                $this->send_template = "";
                $this->method = "GET";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "\"status\":\"SUCCESS\"";
                $this->no_double_zero = TRUE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "URL";
                break;
            case 'aspsms':
                $this->url = "http://xml1.aspsms.com:5061/xmlsvr.asp http://xml1.aspsms.com:5098/xmlsvr.asp http://xml2.aspsms.com:5061/xmlsvr.asp http://xml2.aspsms.com:5098/xmlsvr.asp";
                $this->send_template = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n".
                                       "<aspsms>\r\n".
                                       "  <Userkey>%user</Userkey>\r\n".
                                       "  <Password>%pass</Password>\r\n".
                                       "  <AffiliateId>208355</AffiliateId>\r\n".
		                               "  <Recipient>\r\n".
                                       "    <PhoneNumber>%to</PhoneNumber>\r\n".
                                       "  </Recipient>\r\n".
                                       "  <Originator>%from</Originator>\r\n".
                                       "  <MessageData>%msg</MessageData>\r\n".
                                       "  <Action>SendTextSMS</Action>\r\n".
                                       "</aspsms>\r\n";
                $this->method = "POST-XML";
                $this->encoding = "ISO";
                $this->status_success = "20";
                $this->content_success = "<ErrorCode>1</ErrorCode>";
                $this->no_double_zero = FALSE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "HTML";
                break;
            case 'clickatell':
                $this->url = "https://api.clickatell.com/xml/xml http://api.clickatell.com/xml/xml";
                $this->send_template = "data=<clickAPI>".
                                         "<sendMsg>".
                                           "<api_id>%api_id</api_id>".
                                           "<user>%user</user>".
                                           "<password>%pass</password>".
                                           "<to>%to</to>".
                                           "<from>%from</from>".
                                           "<text><![CDATA[%msg]]></text>".
                                         "</sendMsg>".
                                       "</clickAPI>";
                $this->method = "POST";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "<apiMsgId>";
                $this->no_double_zero = TRUE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "";
                break;
            case 'clickatell2':
                $this->url = "https://platform.clickatell.com/messages/http/send?apiKey=%api_id&to=%to&content=%msg";
                $this->send_template = "";
                $this->method = "GET";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "\"accepted\":true";
                $this->no_double_zero = TRUE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "URL";
                break;
            case 'ecall':
                $this->url = "https://www1.ecall.ch/ecallurl/ecallurl.ASP https://www2.ecall.ch/ecallurl/ecallurl.ASP";
                $this->send_template = "WCI=Interface&Function=SendPage&AccountName=%user&AccountPassword=%pass&CallBack=%from&Address=%to&Message=%msg";
                $this->method = "POST";
                $this->encoding = "ISO";
                $this->status_success = "20";
                $this->content_success = "0";
                $this->no_double_zero = TRUE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "URL";
                $this->encode_ampersand = TRUE;
                break;
            case 'intellisms':
                $this->url = "https://www.intellisoftware.co.uk/smsgateway/sendmsg.aspx https://www.intellisoftware2.co.uk/smsgateway/sendmsg.aspx";
                $this->send_template = "username=%user&password=%pass&originator=%from&to=%to&text=%msg&type=1";
                $this->method = "POST";
                $this->encoding = "ISO";
                $this->status_success = "20";
                $this->content_success = "ID:";
                $this->no_double_zero = TRUE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "URL";
                break;
            case 'nexmo':
                $this->url = "https://rest.nexmo.com/sms/json";
                $this->send_template = "api_key=%api_id&api_secret=%pass&from=%from&to=%to&text=%msg";
                $this->method = "POST";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "\"status\": \"0\"";
                $this->no_double_zero = TRUE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "URL";
                break;
            case 'nowsms':
                $this->url = "http://%ip:%port/?PhoneNumber=%to&Text=%msg";
                $this->send_template = "";
                $this->method = "GET";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "Message Submitted";
                $this->no_double_zero = FALSE;
                $this->basic_auth = TRUE;
                $this->content_encoding = "";
                break;
            case 'smseagle':
                $this->url = "https://%ip:%port/index.php/http_api/send_sms?login=%user&pass=%pass&to=%to&message=%msg";
                $this->send_template = "";
                $this->method = "GET";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "OK";
                $this->no_double_zero = FALSE;
                $this->basic_auth = FALSE;
                $this->content_encoding = "";
                break;
            case 'swisscom':
                $this->url = "https://messagingproxy.swisscom.ch:4300/rest/1.0.0/submit_sm/%api_id";
                $this->send_template = "{\n".
                                       "\"source_addr_ton\": 5,\n".
                                       "\"source_addr_npi\": 0,\n".
                                       "\"source_addr\": \"%from\",\n".
                                       "\"destination_addr\": \"%to\",\n".
                                       "\"short_message\": \"%msg\"\n".
                                       "}";
                $this->method = "POST-JSON";
                $this->encoding = "UTF";
                $this->status_success = "20";
                $this->content_success = "\"command_status\":0";
                $this->no_double_zero = TRUE;
                $this->basic_auth = TRUE;
                $this->content_encoding = "QUOTES";
                break;
            default:
                break;
        }
    }


    function getCustomValues()
    {
        return array('url'                  => $this->url,
                     'ip'                   => $this->ip,
                     'port'                 => $this->port,
                     'send_template'        => $this->send_template,
                     'method'               => $this->method,
                     'encoding'             => $this->encoding,
                     'status_success'       => $this->status_success,
                     'content_success'      => $this->content_success,
                     'no_double_zero'       => $this->no_double_zero,
                     'basic_auth'           => $this->basic_auth,
                     'content_encoding'     => $this->content_encoding,
                    );
    }


    function setCustomValues(
        $custom_array = array()
    ) {
        if (isset($custom_array['url']))              { $this->url = $custom_array['url']; }
        if (isset($custom_array['ip']))               { $this->ip = $custom_array['ip']; }
        if (isset($custom_array['port']))             { $this->port = $custom_array['port']; }
        if (isset($custom_array['send_template']))    { $this->send_template = $custom_array['send_template']; }
        if (isset($custom_array['method']))           { $this->method = $custom_array['method']; }
        if (isset($custom_array['encoding']))         { $this->encoding = $custom_array['encoding']; }
        if (isset($custom_array['status_success']))   { $this->status_success = $custom_array['status_success']; }
        if (isset($custom_array['content_success']))  { $this->content_success = $custom_array['content_success']; }
        if (isset($custom_array['no_double_zero']))   { $this->no_double_zero = $custom_array['no_double_zero']; }
        if (isset($custom_array['basic_auth']))       { $this->basic_auth = $custom_array['basic_auth']; }
        if (isset($custom_array['content_encoding'])) { $this->content_encoding = $custom_array['content_encoding']; }
    }


    function getReplyStatus()
    {
        return $this->reply_status;
    }


    function getReplyContent()
    {
        return $this->reply_content;
    }


    function getLastReplyInfo()
    {
        $result = preg_replace('!\s+!', ' ', str_replace(chr(10), ' ', (str_replace(chr(13), ' ', $this->reply_content))));
        
        return (($this->reply_status != '') ? $this->reply_status.", " : '') . $result;
    }


    function getLastSendInfo()
    {
        $result = "provider: ".$this->provider."; ";
        $result.= "api_id: ".$this->api_id."; ";
        $result.= "username: ".$this->username."; ";
        $result.= "password: ".$this->password."; ";
        $result.= "from: ".$this->from."; ";
        $result.= "to: ".$this->to."; ";
        $result.= "msg: ".$this->msg."; ";

        $result.= "url: ".$this->url."; ";
        $result.= "send_template: ".$this->send_template."; ";
        $result.= "method: ".$this->method."; ";
        $result.= "encoding: ".$this->encoding."; ";
        $result.= "status_success: ".$this->status_success."; ";
        $result.= "content_success: ".$this->content_success."; ";
        $result.= "no_double_zero: ".$this->no_double_zero."; ";
        $result.= "basic_auth: ".$this->basic_auth."; ";
        $result.= "content_encoding: ".$this->content_encoding."; ";

        $result.= "encode_ampersand: ".$this->encode_ampersand."; ";

        $result.= "timeout: ".$this->timeout."; ";
        $result.= "debug: ".$this->debug."; ";
        
        $result = preg_replace('!\s+!', ' ', str_replace(chr(10), ' ', (str_replace(chr(13), ' ', $result))));
        
        return $result;
    }


    function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        if ($this->timeout < 1) {
            $this->timeout = 5;
        }
    }


    function setFrom($from)
    {
        $this->from = $from;
    }


    function setTo($to)
    {
        $this->to = $to;
    }


    function cleanTo($to = "")
    {
        if ("" != $to) {
            $value = $to;
        } else {
            $value = $this->to;
        }
        $value = str_replace(' ','',$value);
        $value = str_replace('(','',$value);
        $value = str_replace(')','',$value);
        $value = str_replace('+','00',$value);

        if (('00' == substr($value,0,2)) && ($this->getNoDoubleZero())) {
            $value = substr($value,2);
        } else {
        }
        return $value;
    }


    function enableBasicAuth()
    {
        $this->basic_auth = TRUE;
    }


    function disableBasicAuth()
    {
        $this->basic_auth = FALSE;
    }


    function enableDebug()
    {
        $this->debug = TRUE;
    }


    function disableDebug()
    {
        $this->debug = FALSE;
    }


    function setMsg($msg)
    /*
     * The msg is automatically converted from ISO to UTF-8 if needed
     */
    {
        $text = $msg;
        $encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
        if ("UTF-8" != $encoding) {
            $text = utf8_encode($text);
        }
        $this->msg = $text;
    }


    function setSendTemplate($send_template)
    /*
     * The msg is automatically converted from ISO to UTF-8 if needed
     */
    {
        $text = $send_template;
        $encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
        if ("UTF-8" != $encoding) {
            $text = utf8_encode($text);
        }
        $this->send_template = $text;
    }


    function encodeString($string, $encoding = "")
    {
        $text = $string;

        $encoding_to = $encoding;
        if ('' == $encoding_to) {
            $encoding_to = $this->getEncoding();
        }

        $encoding_from = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');

        if ("UTF-8" != $encoding_from) {
            if ('UTF' == strtoupper(substr($encoding_to, 0, 3))) {
                $text = utf8_encode($text);
            }
        } else {
            if ('UTF' != strtoupper(substr($encoding_to, 0, 3))) {
                $text = utf8_decode($text);
            }
        }

        return ($text);
    }
    
    
    function encodeHttp($string)
    {
        $text = $this->encodeString($string);
        if ("UTF" != strtoupper(substr($this->getEncoding(),0,3))) {
            $charset = 'ISO-8859-1';
        } else {
            $charset = 'UTF-8';
        }
        
        if ('HTML' == strtoupper($this->content_encoding)) {
            $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML401, $charset);
        } elseif ('URL' == strtoupper($this->content_encoding)) {
            $text = urlencode($text);
        } elseif ('QUOTES' == strtoupper($this->content_encoding)) {
            $text = str_replace('"', '\"', $text);
        }
        return $text;
    }

    
    function encodeUrl($string)
    {
        $text = $this->encodeString($string);
        $text = urlencode($text);
        return $text;
    }

    
    function sendSMS($msg = "")
    {
        $result = FALSE;

        if ('' != $msg) {
            $this->setMsg($msg);
        }
        
        $payload = $this->send_template;
        $payload = $this->encodeString($payload);
        
        $payload_msg = $this->msg;
        
        if (FALSE !== mb_strpos($payload, "![CDATA[", 0, mb_detect_encoding($payload . 'a' , 'UTF-8, ISO-8859-1'))) {
            $this->encode_ampersand = TRUE;
        }
        
        if ($this->encode_ampersand) {
            $payload_msg = str_replace('&', '%26', $payload_msg);
        }
        
        $payload = str_replace('%ip',       $this->ip,                                    $payload);
        if (intval($this->port) <= 0) {
            $payload = str_replace(':%port', '', $payload);
        } else {
            $payload = str_replace('%port', $this->port,                                  $payload);
        }
        $payload = str_replace('%msg',      $this->encodeHttp($payload_msg),              $payload);
        $payload = str_replace('%api_id',   $this->encodeHttp($this->api_id),             $payload);
        $payload = str_replace('%username', $this->encodeHttp($this->username),           $payload);
        $payload = str_replace('%user',     $this->encodeHttp($this->username),           $payload);
        $payload = str_replace('%password', $this->encodeHttp($this->password),           $payload);
        $payload = str_replace('%pass',     $this->encodeHttp($this->password),           $payload);
        $payload = str_replace('%to',       $this->encodeHttp($this->cleanTo($this->to)), $payload);
        $payload = str_replace('%from',     $this->encodeHttp($this->from),               $payload);

        $url_array = explode(' ', $this->url);

        foreach ($url_array as $one_url) {
            
            $one_url = str_replace('%ip',       $this->ip,                                   $one_url);
            if (intval($this->port) <= 0) {
                $one_url = str_replace(':%port', '', $one_url);
            } else {
                $one_url = str_replace('%port', $this->port,                                 $one_url);
            }
            $one_url = str_replace('%msg',      $this->encodeUrl($this->msg),                $one_url);
            $one_url = str_replace('%api_id',   $this->encodeUrl($this->api_id),             $one_url);
            $one_url = str_replace('%username', $this->encodeUrl($this->username),           $one_url);
            $one_url = str_replace('%user',     $this->encodeUrl($this->username),           $one_url);
            $one_url = str_replace('%password', $this->encodeUrl($this->password),           $one_url);
            $one_url = str_replace('%pass',     $this->encodeUrl($this->password),           $one_url);
            $one_url = str_replace('%to',       $this->encodeUrl($this->cleanTo($this->to)), $one_url);
            $one_url = str_replace('%from',     $this->encodeUrl($this->from),               $one_url);

            $server_port = 80;

            $pos = mb_strpos($one_url, '://');
            if (FALSE === $pos) {
                $protocol = '';
            } else {
                switch (mb_strtolower(substr($one_url,0,$pos))) {
                    case 'https':
                    case 'ssl':
                        $protocol = 'ssl://';
                        $server_port = 443;
                        break;
                    case 'tls':
                        $protocol = 'tls://';
                        $server_port = 443;
                        break;
                    default:
                        $protocol = '';
                        break;
                }
                $one_url = substr($one_url,$pos+3);
            }

            $pos = mb_strpos($one_url, '/');
            if (FALSE === $pos) {
                $host = $one_url;
                $url = '/';
            } else {
                $host = substr($one_url,0,$pos);
                $url = substr($one_url,$pos); // And not +1 as we want the / at the beginning
            }

            $pos = mb_strpos($host, ':');
            if (FALSE !== $pos) {
                $server_port = substr($host,$pos+1);
                $host = substr($host,0,$pos);
            }

            if (function_exists("stream_socket_client")) {
                $sslContext = stream_context_create(
                    array('ssl' => array(
                          'verify_peer'         => false,
                          'verify_peer_name'    => false,
                          'disable_compression' => true,
                          'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4')));
                $fp = @stream_socket_client($protocol.$host.":".$server_port, $errno, $errdesc, $this->timeout, STREAM_CLIENT_CONNECT, $sslContext);
            } else {
                $fp = @fsockopen($protocol.$host, $server_port, $errno, $errdesc, $this->timeout);
            }

            if (FALSE !== $fp) {
                $info['timed_out'] = FALSE;
                $output = "";
                if ('GET' == strtoupper(substr($this->method, 0, 3))) {
                    $output.= "GET";
                } elseif ('POST' == strtoupper(substr($this->method, 0, 4))) {
                    $output.= "POST";
                } else {
                    $output.= "POST";
                }
                $output.= " ".$url." HTTP/1.0\r\n";
                if ('XML' == strtoupper(substr($this->method, -3))) {
                    $output.= "Content-Type: text/xml\r\n";
                } elseif ('JSON' == strtoupper(substr($this->method, -4))) {
                    $output.= "Content-Type: application/json\r\n";
                } else {
                    $output.= "Content-Type: application/x-www-form-urlencoded\r\n";
                }
                if ($this->basic_auth) {
                    $auth_user = (('' != $this->username) ? $this->username : $this->api_id);
                    $output.= "Authorization: Basic ".base64_encode($auth_user.":".$this->password)."\r\n";
                }
                $output.= "Content-Length: ".strlen($payload)."\r\n";
                $output.= "User-Agent: multiOTP SMS\r\n";
                $output.= "Host: ".$host."\r\n";
                $output.= "\r\n";
                $output.= $payload;
                $output.= "\r\n";
                fputs($fp, $output);

                $stream_timeout = $this->timeout;
                stream_set_blocking($fp, TRUE);
                stream_set_timeout($fp, $stream_timeout);
                $info = stream_get_meta_data($fp); 

                $reply = '';
                $last_length = 0;
                while ((!feof($fp)) && ((!$info['timed_out']) || ($last_length != strlen($reply)))) {
                    $last_length = strlen($reply);
                    $reply.= @fgets($fp, 1024);
                    $info = stream_get_meta_data($fp);
                    // @ob_flush(); // Avoid notice if any (if the buffer is empty and therefore cannot be flushed)
                    // flush(); 
                }
                fclose($fp);

                if ($info['timed_out']) {
                    $result = FALSE;
                    $this->reply_status = "408";
                    $this->reply_content = "Timeout after $stream_timeout seconds for $protocol$host:$server_port with a result code of $errno ($errdesc)";
                } else {
                    $pos = mb_strpos(mb_strtolower($reply), "\r\n\r\n");
                    $header = substr($reply, 0, $pos);
                    $answer = substr($reply, $pos + 4);
                    $header_array = explode(" ", $header."   ");
                    $this->reply_status = intval($header_array[1]);
                    $this->reply_content = $answer;
                    $result_status = false;
                    $result_content = false;
                    if (intval(trim($this->status_success)) > 0) {
                        if (trim($this->status_success) == substr(trim($this->reply_status), 0, strlen($this->status_success))) {
                            $result_status = TRUE;
                        }
                    } else {
                        $result_status = TRUE;
                    }
                    if ('' != $this->content_success) {
                        if (FALSE !== mb_strpos($this->reply_content, $this->content_success)) {
                            $result_content = TRUE;
                        }
                    } else {
                        $result_content = TRUE;
                    }
                }
                $result = $result_status && $result_content;

                if (TRUE == $this->debug) {
                    echo "DEBUG ONE_URL: $one_url<br />\n)";
                    echo "DEBUG URL: $url<br />\n)";
                    echo "DEBUG SENT: $output<br />\n)";
                    echo "DEBUG REPLY: $reply<br />\n)";
                }
          }

            if (TRUE == $this->debug) {
                echo "DEBUG LAST ONE_URL: $one_url<br />\n)";
                echo "DEBUG LAST URL: $url<br />\n)";
            }

            if (TRUE == $this->debug) {
                echo "DEBUG payload: $payload<br />\n)";
                echo "DEBUG reply_status : ".$this->reply_status."\n<br />";
                echo "DEBUG reply_content : ".$this->reply_content."\n<br />";
                echo "DEBUG status_success : ".$this->status_success."\n<br />";
                echo "DEBUG content_success : ".$this->content_success."\n<br />";
            }

            if ($result) {
                return $result;
            }
        }
        return $result;
    }
}

?>