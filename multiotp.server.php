<?php
/**
 * @file  multiotp.server.php
 * @brief web service for the multiOTP class.
 *
 * multiOTP web service - Strong two-factor authentication PHP class
 * http://www.multiotp.net
 *
 * Visit http://forum.multiotp.net/ for additional support.
 *
 * Donation are always welcome! Please check http://www.multiotp.net
 * and you will find the magic button ;-)
 *
 * The multiOTP web service is simply merged with the multiOTP PHP class
 * in order to provide the server part of the client/server solution.
 *
 * This file can be used with any web server supporting PHP as
 * script language, like the following web servers:
 *  - nginx is a light one under Linux
 *    (http://nginx.org/)
 *  - Mongoose is a light one under Windows
 *    (http://code.google.com/p/mongoose/)
 *  - The Apache HTTP server is a very well known web server running under Linux and Windows
 *    (http://httpd.apache.org/)
 *
 *
 * PHP 5.3.0 or higher is supported.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
 * @version   4.3.2.5
 * @date      2015-07-15
 * @since     2013-08-06
 * @copyright (c) 2013-2015 SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2010-2015 SysCo systemes de communication sa
 *   SysCo (tm) is a trademark of SysCo systemes de communication sa
 *   (http://www.sysco.ch)
 *   All rights reserved.
 * 
 *   This file is part of the multiOTP PHP class
 *
 *   multiOTP PHP class is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public License as
 *   published by the Free Software Foundation, either version 3 of the License,
 *   or (at your option) any later version.
 * 
 *   multiOTP PHP class is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 * 
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with MultiOTP PHP class.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Usage
 *
 *   You will have to send an XML formatted content in the field named data
 *    in a POSTed form.
 *
 *
 * Special issues
 *
 *   If you need specific developments concerning strong authentication,
 *   do not hesitate to contact us per email at developer@sysco.ch.
 *
 *
 * Users feedbacks and comments
 *
 * 2013-07-25 Dominik Pretzsch from Last Squirrel IT
 *   After some discussions with Dominik, integration of the
 *    client/server support in the basic library
 *
 *
 * Change Log
 *
 *   2015-07-15 4.3.2.5 SysCo/al Change of the admin password has been fixed
 *   2015-06-10 4.3.2.3 SysCo/al Enhancements for the Dev(Talks): demo
 *   2015-06-09 4.3.2.2 SysCo/al More option available from the GUI, Dev Talks Edition
 *   2014-12-09 4.3.1.0 SysCo/al Speed improvement (apc options have been tuned)
 *   2014-11-04 4.3.0.0 SysCo/al Updated GUI
 *   2014-04-13 4.2.4.2 SysCo/al Version synchronization
 *   2014-03-30 4.2.4   SysCo/al Forum link added on the GUI, minor bug fixes
 *   2014-03-01 4.2.2   SysCo/al More options available from the GUI
 *   2014-01-20 4.1.1   SysCo/al Version synchronization
 *   2013-12-23 4.1.0   SysCo/al Adding basic web functionalities
 *   2013-08-30 4.0.7   SysCo/al Version synchronization
 *   2013-08-25 4.0.6   SysCo/al Enhanced default page
 *   2013-08-20 4.0.4   SysCo/al Initial release
 *
 *********************************************************************/

///////////////////////////////////////////////////////////////////////////
// For your convenience, the class file is directly integrated in this file
///////////////////////////////////////////////////////////////////////////
require_once('multiotp.class.php');

$multiotp = new Multiotp('DefaultCliEncryptionKey', FALSE);

$multiotp_etc_dir  = '/etc/multiotp';
if (file_exists($multiotp_etc_dir))
{
    // Let says that the new created files have this linux mode
    $multiotp->SetLinuxFileMode('0666');
    
    $multiotp->SetConfigFolder   ($multiotp_etc_dir. '/config/');
    $multiotp->SetDevicesFolder  ($multiotp_etc_dir. '/devices/');
    $multiotp->SetGroupsFolder   ($multiotp_etc_dir. '/groups/');
    $multiotp->SetTokensFolder   ($multiotp_etc_dir. '/tokens/');
    $multiotp->SetUsersFolder    ($multiotp_etc_dir. '/users/');
}

$multiotp_log_dir  = '/var/log/multiotp';
if (file_exists($multiotp_log_dir))
{
    $multiotp->SetLogFolder($multiotp_log_dir. '/');
}

$multiotp->ReadConfigData();

$data = isset($_POST['data'])?$_POST['data']:'';
$method = substr(isset($_GET['method'])?$_GET['method']:(isset($_POST['method'])?$_POST['method']:''),0,255);
$options = isset($_GET['options'])?$_GET['options']:(isset($_POST['options'])?$_POST['options']:'');
$postdata = file_get_contents("php://input");

if (FALSE !== strpos($data,'<multiOTP'))
{
    $multiotp->XmlServer($data);
}
elseif (FALSE !== strpos($postdata,'<SOAP-ENV'))
{
    echo "ERROR: No SOAP server support.";
}
else
{
    session_start();
    $multiotp->SetHashSalt('AjaxH@shS@lt'); // Shared secret
    $hash_salt = $multiotp->GetHashSalt();

    /****************************************
     * WE REALLY DO NOT WANT TO BE CACHED !!!
     ****************************************/
    header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (isset($_FILES['token_file']['tmp_name']))
    {
        if ((isset($_SESSION['logged']) && $_SESSION['logged']))
        {
            if (file_exists($_FILES['token_file']['tmp_name']) && (UPLOAD_ERR_OK == $_FILES["token_file"]["error"]))
            {
                $multiotp->ImportTokensFile($_FILES['token_file']['tmp_name'], $_FILES['token_file']['name'], isset($_POST['token_password'])?trim($_POST['token_password']):'');
            }
        }
        echo "DONE";
    }
    elseif ('' == $method)
    {
        /*********************
         * Basic web server *
         *********************/
         
        $actual_date   = date('Y-m-d H:i:s');
        $class_name    = $multiotp->GetClassName();
        $class_version = $multiotp->GetVersion();
        $class_date    = $multiotp->GetDate();
        $rpi_serial    = $multiotp->GetRaspberryPiSerialNumber();
        $rpi_info      = (('' != $rpi_serial)?"<br />\n        Raspberry Pi serial number: ".$rpi_serial."\n        ":'');

        $prefix_required0_checked = '';
        $prefix_required1_checked = '';
        
        if ($multiotp->IsDefaultRequestPrefixPin())
        {
            $prefix_required1_checked = ' checked="checked" ';
        }
        else
        {
            $prefix_required0_checked = ' checked="checked" ';
        }

        echo <<<EOWEBPAGE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>
            multiOTP web administration console
        </title>
        <style>
            body {
                font-family: Verdana, Helvetica, Arial;
                color: black;
                font-size: 10pt;
                font-weight: normal;
                text-decoration: none;
            }
            h2 {
                font-size: 12pt;
                font-weight: bold;
                margin-top: 0em;
                margin-bottom: 0em;
            }
            h3 {
                font-size: 11pt;
                font-weight: bold;
                margin-top: 0.5em;
                margin-bottom: 0.1em;
            }
            p {
                margin-top: 0em;
                margin-bottom: 0em;
            }
            th {
                text-align: right;
            }
            .info {
                font-style: italic;
                font-weight: normal;
            }
            .section_title {
                display: block;
                font-weight: bold;
            }
            .section_title a {
                color: black;
                text-decoration: none;
            }
            
            /*************************/
            /* Custom colors - BEGIN */
            body {
                background-color: black;
                color: white;
                font-family: Verdana, Helvetica, Arial;
                font-size: 10pt;
                font-weight: normal;
                text-decoration: none;
            }
            .custom_logo_white {
                color: white;
                font-weight: bold;
                font-size: 20px;
            }
            .custom_logo_red {
                color: red;
                font-weight: bold;
                font-size: 20px;
            }
            a {
                color: white;
                font-weight: bold;
                text-decoration: none;
            }
            a:hover {
                color: red;
            }
            .section_title a {
                color: white;
                text-decoration: none;
            }
            /* Custom colors - END */
            /***********************/


        </style>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
        <script type="text/javascript" src="md5.js"></script>
        <script>
            // Relative url page in order to execute the method remotely
            var url_page = location.protocol + '//' + location.host + location.pathname;

            // Shared secret
            var hash_salt = '$hash_salt';

            var selected_color = 'red';
            var unselected_color = 'black';
            var selected_background_color = '#e0e0e0';
            var unselected_background_color = '';
            var succeeded_color = '#80ff80';
            var failed_color = 'red';

            /*************************/
            /* Custom colors - BEGIN */

            var selected_color = 'red';
            var unselected_color = 'white';
            var selected_background_color = '#303030';
            var unselected_background_color = '';

            /* Custom colors - END */
            /***********************/
            
            function ChangeAdminPassword()
            {
                var newpassword = document.getElementById('newpassword').value;
                var newpassword2 = document.getElementById('newpassword2').value;
                document.getElementById('newpassword').value = '';
                document.getElementById('newpassword2').value = '';
                
                if ('' == newpassword)
                {
                    alert('Password is empty!');
                }
                else if (newpassword != newpassword2)
                {
                    alert('Passwords are not equal!');
                }
                else
                {
                    var hash_password = md5(hash_salt+newpassword+hash_salt);
                    RemoteCall('SetAdminPasswordHash', hash_password);
                    Logout();
                }
            }

            function Add()
            {
                var newuser = document.getElementById('newuser').value;
                var newemail = document.getElementById('newemail').value;
                var newsms = document.getElementById('newsms').value;
                var prefix_required = (document.getElementById('prefix_required1').checked?'1':'0');
                var algorithm = document.getElementById('algorithm').value;
                var token_serial = document.getElementById('token_serial').value;
                var pin = document.getElementById('pin').value;
                if (IsLoggedIn())
                {
                    RemoteCall('FastCreateUser', newuser+"\t"+newemail+"\t"+newsms+"\t"+prefix_required+"\t"+algorithm+"\t"+pin+"\t"+token_serial);
                    UpdatePage();
                }
                document.getElementById('newuser').value = '';
                document.getElementById('newemail').value = '';
                document.getElementById('newsms').value = '';
                document.getElementById('newuser').focus();
            }

            function DeleteToken(one_token)
            {
                if ('' != one_token)
                {
                    if (confirm('Are you sure you want to delete the token ' + one_token + '?'))
                    {
                        RemoteCall('DeleteToken', one_token);
                        UpdatePage();
                    }
                }
            }
            
            function DeleteUser(one_user)
            {
                if ('' != one_user)
                {
                    if (confirm('Are you sure you want to delete the user ' + one_user + '?'))
                    {
                        RemoteCall('DeleteUser', one_user);
                        UpdatePage();
                    }
                }
            }
            
            function ResyncUserNow(resync_user, resync_otp1, resync_otp2)
            {
                var resynced = ('true' == eval(RemoteCall('ResyncUser', resync_user+"\t"+resync_otp1+"\t"+resync_otp2)));
                if (resynced)
                {
                    Toggle('resync', 'none');
                    document.getElementById('resync_user').value = '';
                    document.getElementById('resync_otp1').value = '';
                    document.getElementById('resync_otp2').value = '';
                }
                else
                {
                    document.getElementById('resync_otp1').select();
                    document.getElementById('resync_otp1').focus();
                }
            }

            function CheckUserNow(check_user_user, check_user_otp)
            {
                var result = eval(RemoteCall('CheckToken', check_user_user+"\t"+check_user_otp));
                UpdatePage();
                if ('true' == result)
                {
                    document.getElementById('check_user_result').innerHTML = '<span style="color:'+succeeded_color+';">succeeded</span>';
                    document.getElementById('check_user_user').value = '';
                    document.getElementById('check_user_otp').value = '';
                }
                else
                {
                    document.getElementById('check_user_result').innerHTML = '<span style="color:'+failed_color+';">failed ('+result+')</span>';
                    document.getElementById('check_user_user').select();
                    document.getElementById('check_user_user').focus();
                    document.getElementById('check_user_otp').value = '';
                }
            }

            function IsLoggedIn()
            {
                var logged_in = ('true' == eval(RemoteCall('UserLoggedIn')));
                return logged_in;
            }

            function Login()
            {
                var random_salt = eval(RemoteCall('GetRandomSalt'));

                var user = document.getElementById('user').value;
                var password = document.getElementById('password').value;
                document.getElementById('password').value = '';

                var hash_password = md5(random_salt+md5(hash_salt+password+hash_salt)+random_salt);
                RemoteCall('Login', user+"\t"+hash_password);
                if (UpdatePage())
                {
                    Toggle('add_user', 'block');
                    document.getElementById('newuser').focus();
                }
            }

            function Logout()
            {
                RemoteCall("Login", "");
                Toggle('add_user', 'block');
                UpdatePage();
            }

            function PrintQrCode(one_user)
            {
                if ('' != one_user)
                {
                    var http_params = "method=PrintQrCode"+"&options="+encodeURIComponent(one_user);
                    var full_url = url_page +'?'+http_params;
                    window.open(full_url,'_blank');
                }
            }
            function ResyncUser(one_user)
            {
                document.getElementById('resync_user').value = one_user;
                Toggle('resync', 'block');
                document.getElementById('resync_otp1').focus();
            }


            function RebootDevice()
            {
                var result = GetHttp('RebootDevice','');
            }

            function RemoteCall(my_method, my_options, my_id, async, form_method)
            /******************************************************************************************
             * RemoteCall is a sample function that make an (a)synchronous GET or POST request to launch
             *   a remote command and return the result as an HTML content in the defined id if defined.
             *
             * @param   string   my_method   remote method to call
             * @param   string   my_options  options (separated by \t) for the called method
             * @param   string   my_id       id of the div/span to update asynchronously with the result
             * @param   boolean  async       define the asynchronous mode
             * @param   string   form_method method used to send the request (GET or POST)
             * @return  none
             ******************************************************************************************/
            {
                my_method = typeof my_method !== 'undefined' ? my_method : '';
                my_options = typeof my_options !== 'undefined' ? my_options : '';
                my_id = typeof my_id !== 'undefined' ? my_id : '';
                async = typeof async !== 'undefined' ? async : false;
                form_method = typeof form_method !== 'undefined' ? form_method : 'GET';

                async = false;

                var result = '';
                
                if ('POST' != form_method)
                {
                    form_method = 'GET';
                }

                var xmlhttp;
                if (window.XMLHttpRequest)
                { // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp=new XMLHttpRequest();
                }
                else
                { // code for IE6, IE5
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                
                if (true == async)
                {
                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200)
                        {
                            result = xmlhttp.responseText;
                            if ('' != my_id)
                            {
                                document.getElementById(my_id).innerHTML=eval(result);
                            }
                        }
                    }
                }

                var http_params = "method="+my_method+"&options="+encodeURIComponent(my_options);
                
                var full_url = url_page;
                var post_params = null;

                if ('GET' == form_method)
                {
                    full_url = url_page +'?'+http_params;
                }
                else
                {
                    post_params = http_params;
                }

                xmlhttp.open(form_method,full_url,false);
                if ('POST' == form_method)
                {
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.setRequestHeader("Content-length", post_params.length);
                    xmlhttp.setRequestHeader("Connection", "close");
                }
                xmlhttp.send(post_params);

                if (false == async)
                {
                    if (xmlhttp.status === 200)
                    {
                        result = xmlhttp.responseText;
                        if ('' != my_id)
                        {
                            document.getElementById(my_id).innerHTML=eval(result);
                        }
                    }
                    return (result);
                }
            }

            function Toggle(my_section, set_value)
            {
                var all_sections = ['add_user', 'change_password', 'import_tokens', 'resync', 'check_user', 'list_tokens'];

                // Flush the check section
                document.getElementById('check_user_user').value = '';
                document.getElementById('check_user_otp').value = '';
                document.getElementById('check_user_result').innerHTML = '';

                set_value = typeof set_value !== 'undefined' ? set_value : '';
                
                if ('all' == set_value)
                {
                    all_sections.forEach(function(one_section)
                    {
                        document.getElementById(one_section+'_section').style.display = 'block';
                        document.getElementById(one_section+'_title').style.backgroundColor = selected_background_color;
                        document.getElementById(one_section+'_text').style.color=selected_color;
                        section_title = document.getElementById(one_section+'_toggle').innerHTML;
                        if ('[+]' == section_title)
                        {
                            document.getElementById(one_section+'_toggle').innerHTML = '[-]';
                        }
                    });
                }
                else if ((('none' == document.getElementById(my_section+'_section').style.display) || ('block' == set_value)) && ('none' != set_value))
                {
                    all_sections.forEach(function(one_section)
                    {
                        if (one_section != my_section)
                        {
                            document.getElementById(one_section+'_section').style.display = 'none';
                            document.getElementById(one_section+'_title').style.backgroundColor = unselected_background_color;
                            document.getElementById(one_section+'_text').style.color=unselected_color;
                            section_title = document.getElementById(one_section+'_toggle').innerHTML;
                            if ('[-]' == section_title)
                            {
                                document.getElementById(one_section+'_toggle').innerHTML = '[+]';
                            }
                        }
                    });
                    document.getElementById(my_section+'_section').style.display = 'block';
                    document.getElementById(my_section+'_title').style.backgroundColor = selected_background_color;
                    document.getElementById(my_section+'_text').style.color=selected_color;
                    section_title = document.getElementById(my_section+'_toggle').innerHTML;
                    if ('[+]' == section_title)
                    {
                        document.getElementById(my_section+'_toggle').innerHTML = '[-]';
                    }
                    if ('add_user' == my_section)
                    {
                        document.getElementById('newuser').focus();
                    }
                    else if ('change_password' == my_section)
                    {
                        document.getElementById('newpassword').focus();
                    }
                    else if ('import_tokens' == my_section)
                    {
                        document.getElementById('token_file').value = '';
                        var ifrm = document.getElementById('upload_frame');
                        ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;
                        ifrm.document.open();
                        ifrm.document.write('Waiting...');
                        ifrm.document.close();
                    }
                    else if ('resync' == my_section) {
                        document.getElementById('resync_user').select();
                        document.getElementById('resync_user').focus();
                        document.getElementById('resync_otp1').value = '';
                        document.getElementById('resync_otp2').value = '';
                    }
                    else if ('check_user' == my_section)
                    {
                        document.getElementById('check_user_user').select();
                        document.getElementById('check_user_user').focus();
                    }
                }
                else
                {
                    document.getElementById(my_section+'_section').style.display = 'none';
                    document.getElementById(my_section+'_title').style.backgroundColor = unselected_background_color;
                    document.getElementById(my_section+'_text').style.color=unselected_color;
                    section_title = document.getElementById(my_section+'_toggle').innerHTML;
                    if ('[-]' == section_title)
                    {
                        document.getElementById(my_section+'_toggle').innerHTML = '[+]';
                    }
                }
            }

            function UnlockUser(one_user)
            {
                if ('' != one_user)
                {
                    RemoteCall('UnlockUser', one_user);
                    UpdatePage();
                }
            }

            function UpdatePage()
            {
                var logged_in = IsLoggedIn();
                document.getElementById('logged').innerHTML = (logged_in?'User authenticated':'User NOT authenticated');
                document.getElementById('logout_section').style.display=(logged_in?'block':'none');
                document.getElementById('login_section').style.display=(logged_in?'none':'block');
                document.getElementById('authenticated_section').style.display=(logged_in?'block':'none');
                if (!logged_in)
                {
                    document.getElementById('login_title').style.backgroundColor = selected_background_color;
                    document.getElementById('login_text').style.color=selected_color;
                }

                document.getElementById('token_type').style.display = 'table-row';
                document.getElementById('pin').value = '';
                
                UpdateTokensList();
                UpdateUsersList();
                
                return logged_in;
            }
            
            function UpdateTokensList()
            {
                // Tokens
                var tokens_counter = 0;

                var remotecall = eval(RemoteCall('GetTokensList'));
                var tokenslist = typeof remotecall !== 'undefined' ? remotecall.trim() : '';
 
                /*
                var remotecall = eval(RemoteCall('GetLockedTokensList'));
                var lockedlist = typeof remotecall !== 'undefined' ? remotecall.trim() : '';
                */
                var lockedlist = '';
 
                if ('' != tokenslist)
                {
                    var tokensarray = tokenslist.split("\t");

                    if ('false' == tokenslist)
                    {
                        tokenslist = 'not authorized';
                        tokensarray = [];
                    }
                    else
                    {
                        var lockedlistarray = lockedlist.split("\t");
                        
                        tokenslist = '';
                        for (var i = 0; i < tokensarray.length; i++)
                        {
                            tokenslist = tokenslist + '<button type="button" onclick="DeleteToken(\''+tokensarray[i]+'\');">Delete</button>';
                            
                            tokenslist = tokenslist + ' ' + tokensarray[i];
                            
                            /*
                            for (var j = 0; j < lockedlistarray.length; j++)
                            {
                                if (tokensarray[i] == lockedlistarray[j])
                                {
                                    tokenslist = tokenslist + ' (<a href="#" onclick="UnlockToken(\''+tokensarray[i]+'\');">unlock</a>)';
                                    break;
                                }
                            }
                            */
                            
                            tokenslist = tokenslist + '<br />';
                            
                            tokens_counter++;
                        }
                    }

                    select = document.getElementById("token_serial");
                    select.options.length = 1;
                    for (var i = 0; i < tokensarray.length; i++)
                    {
                        select.options[1 + i] = new Option(tokensarray[i], tokensarray[i]);
                        /*
                        var opt= document.getElementById('token_serial').options[1 + i];
                        opt.value = tokensarray[i];
                        opt.text  = tokensarray[i];
                        */
                    }                    
                }
                document.getElementById('tokenslist').innerHTML = tokenslist;

                tokenscounter_txt = '';
                tokenstitle_txt = 'List of hardware token';
                if (tokens_counter > 0)
                {
                    tokenscounter_txt = '('+tokens_counter+' token';
                    if (tokens_counter > 1)
                    {
                        tokenscounter_txt = tokenscounter_txt + 's';
                        tokenstitle_txt = tokenstitle_txt + 's';
                    }
                    tokenscounter_txt = tokenscounter_txt + ')';
                }
                document.getElementById('tokenscounter').innerHTML = tokenscounter_txt;
                document.getElementById('tokenstitle').innerHTML = tokenstitle_txt;
            }
            
            function UpdateUsersList()
            {
                // Users
                var counter = 0;

                var remotecall = eval(RemoteCall('GetUsersList'));
                var userslist = typeof remotecall !== 'undefined' ? remotecall.trim() : '';
 
                var remotecall = eval(RemoteCall('GetLockedUsersList'));
                var lockedlist = typeof remotecall !== 'undefined' ? remotecall.trim() : '';
 
                if ('' != userslist)
                {
                    var usersarray = userslist.split("\t");

                    if ('false' == userslist)
                    {
                        userslist = 'not authorized';
                    }
                    else
                    {
                        var lockedlistarray = lockedlist.split("\t");
                        
                        userslist = '';
                        for (var i = 0; i < usersarray.length; i++)
                        {
                            userslist = userslist + '<button type="button" onclick="DeleteUser(\''+usersarray[i]+'\');">Delete</button>';
                            userslist = userslist + '<button type="button" onclick="PrintQrCode(\''+usersarray[i]+'\');">Print</button>';
                            userslist = userslist + '<button type="button" onclick="ResyncUser(\''+usersarray[i]+'\');">Resync</button>';
                            
                            userslist = userslist + ' ' + usersarray[i];
                            
                            for (var j = 0; j < lockedlistarray.length; j++)
                            {
                                if (usersarray[i] == lockedlistarray[i])
                                {
                                    userslist = userslist + ' (<a href="#" onclick="UnlockUser(\''+usersarray[i]+'\');">unlock</a>)';
                                    break;
                                }
                            }
                            
                            userslist = userslist + '<br />';
                            
                            counter++;
                        }
                    }
                }
                document.getElementById('userslist').innerHTML = userslist;

                userscounter_txt = '';
                userstitle_txt = 'List of users';
                if (counter > 0)
                {
                    userscounter_txt = '('+counter+' user';
                    if (counter > 1)
                    {
                        userscounter_txt = userscounter_txt + 's';
                        // userstitle_txt = userstitle_txt + 's';
                    }
                    userscounter_txt = userscounter_txt + ')';
                }
                document.getElementById('userscounter').innerHTML = userscounter_txt;
                document.getElementById('userstitle').innerHTML = userstitle_txt;
            }
            
        </script>
    </head>
    <body onload=" if (UpdatePage()) { Toggle('add_user', 'block'); document.getElementById('newuser').focus(); }">
        <h2>multi<i>OTP</i> web administration console</h2>
        the open source strong authentication library
        <br />
        $class_name $class_version $class_date
        <br />
        <!-- div id="custom_info">
            <br />
            <span class="custom_logo_white">Dev</span><span class="custom_logo_red">(</span><span class="custom_logo_white">Talks</span><span class="custom_logo_red">)</span><span class="custom_logo_white">: Edition</span>
            <br />
            <br />
        </div -->
        Web service is ready $actual_date
        $rpi_info<hr />
        <div id="login_section">
        <form>
            <div id="package_info_section">
                This package is the result of a *bunch* of work. If you find this package useful, <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B78FJAH6RBNZ2">[Donation]</a> are always welcome to support this project.
                <br />
                Please check <a target="_blank" href="http://www.multiOTP.net/">http://www.multiOTP.net/</a> and you will find the magic button ;-)
				<br />
				Visit <a target="_blank" href="http://forum.multiotp.net/">http://forum.multiotp.net/</a> for additional support.
            </div>
            <hr />
            <div class="section_title" id="login_title"><span id="login_text">Login</span></div>
            Username: <input type=text" onfocus="this.blur();" name="user" id="user" length="20" value="admin" /> (default is admin)
            <span id="log_info"></span><span id="logged"></span>
            <br />
            Password: <input type="password" onfocus="this.value='';" name="password" id="password" length="20" value="1234" /> (default is 1234)
            &nbsp;
            <span id="login"><button type="button" onclick="Login();" >Login</button></span>
        </form>
        </div>
        <div id="logout_section">
        <form>
            <span id="logout"><button type="button" onclick="Logout();">Logout</button></span>
        </form>
        <hr />
        </div>
        <div id="authenticated_section">
            <div class="section_title" id="change_password_title"><a href="#" onclick="Toggle('change_password');"><span id="change_password_toggle">[+]</span> <span id="change_password_text">Change admin password</span></a></div>
            <div id="change_password_section" style="display:none;">
            <form>
                <table>
                    <tr>
                        <th>
                            New admin password:
                        </th>
                        <td>
                            <input type="password" name="newpassword" id="newpassword" length="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                             Retype new admin password:
                        </th>
                        <td>
                            <input type="password" name="newpassword2" id="newpassword2" length="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                        </th>
                        <td>
                            <button type="button" onclick="ChangeAdminPassword();">Apply</button>
                        </td>
                    </tr>
                </table>
            </form>
            <hr />
            </div>
            <div class="section_title" id="import_tokens_title"><a href="#" onclick="Toggle('import_tokens');"><span id="import_tokens_toggle">[+]</span> <span id="import_tokens_text">Import new hardware tokens</span></a></div>
            <div id="import_tokens_section" style="display:none;">
            <form target="upload_frame" enctype="multipart/form-data" method="post" action="">
                <table>
                    <tr>
                        <th>
                            Import tokens definition file (OATH PSKC, Yubico, etc.):
                        </th>
                        <td>
                            <input type="file" name="token_file" id="token_file" />
                        </td>
                    <tr>
                    <tr>
                        <th>
                            Password (if any):
                        </th>
                        <td>
                            <input type="password" name="token_password" id="token_password" />
                        </td>
                    <tr>
                    </tr>
                        <th>
                        </th>
                        <td>
                            <input type="submit" value="Import" onClick="Toggle('import_tokens', 'none'); setTimeout(function(){UpdateTokensList()},2500);" />
                        </td>
                    </tr>
                </table>
            </form>
            <iframe name="upload_frame" id="upload_frame" style="display:none;"></iframe>
            <hr />
            </div>
            <div class="section_title" id="list_tokens_title"><a href="#" onclick="Toggle('list_tokens');"><span id="list_tokens_toggle">[+]</span> <span id="list_tokens_text"><span id="tokenstitle">List of hardware tokens</span></span></a> <span class="info" id="tokenscounter"></span></div>
            <div id="list_tokens_section" style="display:none;">
                <br />
                <div id="tokenslist"></div>
            <hr />
            </div>
            <div class="section_title" id="add_user_title"><a href="#" onclick="Toggle('add_user');"><span id="add_user_toggle">[-]</span> <span id="add_user_text">Add a new user</span></a></div>
            <div id="add_user_section" style="display:block;">
            <form>
                <table>
                    <tr>
                        <th>
                            Username:
                        </th>
                        <td>
                            <input type=text" name="newuser" id="newuser" length="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Email address:
                        </th>
                        <td>
                            <input type=text" name="newemail" id="newemail" length="30" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Mobile phone (SMS):
                        </th>
                        <td>
                            <input type=text" name="newsms" id="newsms" length="30" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            With prefix PIN:
                        </th>
                        <td>
                            <input type="radio" name="prefix_required" id="prefix_required1" $prefix_required1_checked value="1">yes</input>
                            <input type="radio" name="prefix_required" id="prefix_required0" $prefix_required0_checked value="0">no</input>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Specific prefix PIN:
                        </th>
                        <td>
                            <input type=text" name="pin" id="pin" length="30" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Select a token:
                        </th>
                        <td>
                            <select name="token_serial" id="token_serial" onchange="if ('' != this.value) { document.getElementById('token_type').style.display = 'none' } else { document.getElementById('token_type').style.display = 'table-row' };">
                                <option value="" selected="selected">software</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="token_type" style="display:table-row;">
                        <th>
                            Token type:
                        </th>
                        <td>
                            <select name="algorithm" id="algorithm">
                                <option value="TOTP" selected="selected">TOTP</option>
                                <option value="HOTP">HOTP</option>
                                <option value="MOTP">MOTP</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                        </th>
                        <td>
                            <button type="button" onclick="Add();">Add this user</button>
                        </td>
                    </tr>
                </table>
            </form>
            <hr />
            </div>
            <div class="section_title" id="resync_title"><a href="#" onclick="Toggle('resync');"><span id="resync_toggle">[+]</span> <span id="resync_text">Resync a user</span></a></div>
            <div id="resync_section" style="display:none;">
            <form>
                <table>
                    <tr>
                        <th>
                            User to resync:
                        </th>
                        <td>
                            <input type="text" name="resync_user" id="resync_user" length="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                             First OTP:
                        </th>
                        <td>
                            <input type="text" name="resync_otp1" id="resync_otp1" length="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                             Second OTP:
                        </th>
                        <td>
                            <input type="text" name="resync_otp2" id="resync_otp2" length="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                        </th>
                        <td>
                            <button type="button" onclick="ResyncUserNow(document.getElementById('resync_user').value, document.getElementById('resync_otp1').value, document.getElementById('resync_otp2').value);">Resync now</button>
                        </td>
                    </tr>
                </table>
            </form>
            </div>
            <div class="section_title" id="check_user_title"><a href="#" onclick="Toggle('check_user');"><span id="check_user_toggle">[+]</span> <span id="check_user_text">Check a user</span></a></div>
            <div id="check_user_section" style="display:none;">
            <form>
                <table>
                    <tr>
                        <th>
                            User to check:
                        </th>
                        <td>
                            <input type="text" name="check_user_user" id="check_user_user" length="50" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                             OTP (with prefix if needed):
                        </th>
                        <td>
                            <input type="text" name="check_user_otp" id="check_user_otp" length="50" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                        </th>
                        <td>
                            <button type="button" onclick="CheckUserNow(document.getElementById('check_user_user').value, document.getElementById('check_user_otp').value);">Check now</button>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Test result: 
                        </th>
                        <td>
                            <span id="check_user_result"></span>
                        </td>
                    </tr>
                </table>
            </form>
            </div>
            <hr />
            <div class="section_title" id="list_users_title"><span id="list_users_toggle"></span><span id="list_users_text"><span id="userstitle">List of users</span></span> <span class="info" id="userscounter"></span></div>
            <div id="list_users_section" style="display:block;">
                <br />
                <div id="userslist"></div>
            </div>
        </div>
    </body>
</html>
EOWEBPAGE;
    }
    else
    {
        /*********************
         * Basic Ajax server *
         *********************/
        $method = preg_replace('/[^(x20-\x7F)]*/','', $method);
        $options = preg_replace('/[^(\x09\x20-\x7E\xA0-\xFF)]*/','', $options);
        $options_array = explode("\t",$options);
    
        // Set the default password to 1234 if no password is set
        if ('' == $multiotp->GetConfigAttribute('admin_password_hash'))
        {
            $multiotp->SetAdminPassword('1234');
            $multiotp->WriteConfigData();
        }
    
        if (!isset($_SESSION['logged']))
        {
            $_SESSION['logged'] = FALSE;
        }

        if (!isset($_SESSION['random_salt']))
        {
            $random_salt = substr(md5(time()."@".rand(100000,999999)),0,12);
            $_SESSION['random_salt'] = $random_salt;
        }
        $multiotp->SetRandomSalt($_SESSION['random_salt']);
        
        $ajax_result = "false";

        switch (strtoupper($method))
        {
            case strtoupper("GetRandomSalt"):
                $ajax_result = $multiotp->GetRandomSalt();
                break;
            case strtoupper("Login"):
                $result = FALSE;
                $username = substr(isset($options_array[0])?$options_array[0]:'',0,255);
                $password = substr(isset($options_array[1])?$options_array[1]:'',0,255);
                if ('admin' == $username)
                {
                    $result = $multiotp->CheckAdminPasswordHashWithRandomSalt($password);
                }

                if ($result)
                {
                    $_SESSION['logged'] = TRUE;
                    /* And we change the random_salt to avoid a second login with the same previous hash */
                    $random_salt = substr(md5(time()."@".rand(100000,999999)),0,12);
                    $_SESSION['random_salt'] = $random_salt;
                    $multiotp->SetRandomSalt($random_salt);
                    $ajax_result = "true";
                }
                else
                {
                    $_SESSION = array();
                    $_SESSION['logged'] = FALSE;
                    $ajax_result = "false";
                }
                break;
            default:
                if ((isset($_SESSION['logged']) && $_SESSION['logged']))
                {
                    /*******************************************************
                     * The next methods are allowed only if we are logged in
                     *******************************************************/
                    switch (strtoupper($method))
                    {
                        case strtoupper("DeleteUser"):
                            $ajax_result = $multiotp->DeleteUser($options_array[0]);
                            break;
                        case strtoupper("DeleteToken"):
                            $ajax_result = $multiotp->DeleteToken($options_array[0]);
                            break;
                        case strtoupper("FastCreateUser"):
                            $user              = trim((isset($options_array[0])?$options_array[0]:''));
                            $email             = trim((isset($options_array[1])?$options_array[1]:''));
                            $sms               = trim((isset($options_array[2])?$options_array[2]:''));
                            $prefix_pin_needed = intval(isset($options_array[3])?$options_array[3]:$multiotp->GetDefaultRequestPrefixPin());
                            $algorithm         = (isset($options_array[4])?$options_array[4]:"totp");
                            $pin               = (isset($options_array[5])?$options_array[5]:'');
                            $token_serial      = trim((isset($options_array[6])?$options_array[6]:''));
                            if ('' != $token_serial)
                            {
                                $ajax_result = $multiotp->CreateUserFromToken($user, $token_serial, $email, $sms, $pin, $prefix_pin_needed);
                            }
                            else
                            {
                                $ajax_result = $multiotp->FastCreateUser($user, $email, $sms, $prefix_pin_needed, $algorithm, 1, '', '*DEFAULT*', 0, $pin);
                            }
                            if (isset($options_array[3]))
                            {
                                $multiotp->SetDefaultRequestPrefixPin(intval($options_array[3]));
                                $multiotp->WriteConfigData();
                            }
                            break;
                        case strtoupper("GetFullVersionInfo"):
                            $ajax_result = $multiotp->GetFullVersionInfo();
                            break;
                        case strtoupper("GetTokensList"):
                            $ajax_result = $multiotp->GetTokensList();
                            break;
                        case strtoupper("GetUsersList"):
                            $ajax_result = $multiotp->GetUsersList();
                            break;
                        case strtoupper("GetLockedUsersList"):
                            $ajax_result = $multiotp->GetLockedUsersList();
                            break;
                        case strtoupper("PrintQrCode"):
                            echo $multiotp->GenerateHtmlQrCode($options_array[0]);
                            $ajax_result = '';
                            break;
                        case strtoupper("ResyncUser"):
                            $ajax_result = "false";
                            if ($multiotp->ReadUserData($options_array[0]))
                            {
                                $result = $multiotp->CheckToken($options_array[1], $options_array[2]);
                                if (14 == $result) // 14 Token has been resynchronized successfully
                                {
                                    $ajax_result = "true";
                                }
                            }
                            break;
                        case strtoupper("CheckToken"):
                            $ajax_result = '21 '.$multiotp->GetErrorText(21);
                            if ($multiotp->ReadUserData($options_array[0]))
                            {
                                $result = $multiotp->CheckToken($options_array[1]);
                                if (0 == $result)
                                {
                                    $ajax_result = "true";
                                }
                                else
                                {
                                    $ajax_result = $result.' '.$multiotp->GetErrorText($result);
                                }
                            }
                            break;
                        case strtoupper("SetAdminPasswordHash"):
                            if ($multiotp->IsDemoMode())
                            {
                                $result = "false";
                            }
                            else
                            {
                                $ajax_result = $multiotp->SetAdminPasswordHash($options_array[0]);
                                $multiotp->WriteConfigData();
                            }
                            break;
                        case strtoupper("UnlockUser"):
                            $ajax_result = $multiotp->UnlockUser($options_array[0]);
                            break;
                        case strtoupper("UserLoggedIn"):
                            $ajax_result = "true"; //User is logged if code arrives here!
                            break;
                        default:
                            $ajax_result = "false";
                            break;
                    }
                }
                else
                {
                    $ajax_result = "false";
                }
                break;
        }
        if ('' != $ajax_result)
        {
            echo json_encode($ajax_result);
        }
        @ob_flush();
        flush(); 
    }
}

?>