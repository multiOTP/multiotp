<?php
/**
 * @file  check.multiotp.class.php
 * @brief Check the implementation of some multiOTP functionalities
 *
 * multiOTP - Strong two-factor authentication PHP class package
 * http://www.multiotp.net
 *
 * Visit http://forum.multiotp.net/ for additional support.
 *
 * Donation are always welcome! Please check http://www.multiotp.net
 * and you will find the magic button ;-)
 *
 *
 * check.multiotp.class.php is a file implementing the Multiotp class
 * in order to check the compliance with RFC4226. It must be
 * placed in the same directory as the multiotp.class.php file.
 *
 * WARNING! DO NOT FORGET TO REMOVE this test file from your disk when you go in production !
 *
 *
 * PHP 5.3.0 or higher is supported.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.4.1.6
 * @date      2019-01-25
 * @since     2013-07-10
 * @copyright (c) 2013-2019 SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2013-2019 SysCo systemes de communication sa
 *   SysCo (tm) is a trademark of SysCo systemes de communication sa
 *   (http://www.sysco.ch/)
 *   All rights reserved.
 *
 *   This file is part of the multiOTP project.
 *
 *   This script is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public
 *   License as published by the Free Software Foundation; either
 *   version 3 of the License, or (at your option) any later version.
 *
 *   This script is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with multiOTP PHP class.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Usage
 *  
 *   The file must be placed in the same directory as multiotp.class.php
 *
 *
 * External file needed
 *
 *   multiotp.class.php
 *
 *
 * External file created
 *
 *   Multiotp class will create some internals folders and files
 *
 *
 * Change Log
 *
 *   2017-06-02 5.0.4.6 SysCo/al Enhanced tests display
 *   2017-05-29 5.0.4.5 SysCo/al Additional PostgreSQL backend included
 *                               Parameters adapted (set the $check_mysql_xxx and/or the $check_pgsql_xxx parameters below)
 *   2016-11-04 5.0.2.6 SysCo/al GetNetworkInfo() test included
 *   2015-07-17 4.3.2.6 SysCo/al Additional tests included
 *   2015-06-09 4.3.2.2 SysCo/al Additional tests included
 *   2014-11-04 4.3.0.0 SysCo/al Additional tests included
 *   2014-03-30 4.2.4.2 SysCo/al Additional tokens tests included
 *   2014-03-30 4.2.4   SysCo/al Additional tests included
 *                               MySQL backend test added (set the $check_sql_xxx parameters below)
 *                               List of attributes to encrypt in the backend is set to null during the tests
 *   2013-08-25 4.0.7   SysCo/al Version synchronization
 *   2013-08-25 4.0.6   SysCo/al File renamed to check.multiotp.class.php
 *   2013-07-10 4.0.4   SysCo/al Initial release of check.multiotp.php
 ***************************************************************/

/*

// PostgreSQL

# CREATE DATABASE multiotptest;
# \connect multiotptest
# CREATE USER root PASSWORD 'pass';
# CREATE SCHEMA multiotptest;
# GRANT ALL ON SCHEMA multiotptest to root;
# \list
# SELECT table_name FROM information_schema.tables WHERE table_schema='multiotptest';
# SELECT * from multiotptest.multiotp_config;

$GLOBALS['check_pgsql_server']   = '127.0.0.1:5432';
$GLOBALS['check_pgsql_username'] = 'root';
$GLOBALS['check_pgsql_password'] = 'pass';
$GLOBALS['check_pgsql_database'] = 'multiotptest';
$GLOBALS['check_pgsql_schema']   = 'multiotptest';


// MySQL

$GLOBALS['check_mysql_server']   = '127.0.0.1:3306';
$GLOBALS['check_mysql_username'] = 'root';
$GLOBALS['check_mysql_password'] = 'pass';
$GLOBALS['check_mysql_database'] = 'multiotptest';

*/

set_time_limit(3600);

$first_time = time();

if (!isset($GLOBALS['minima'])) {
    $GLOBALS['minima'] = isset($_GET['minima']);
}

if (!function_exists('echo_full')) {
    function echo_full($to_display) {
        if (!$GLOBALS['minima']) {
            @ob_end_flush();
            echo $to_display;
        }
    }
}

require_once('multiotp.class.php');

// MySQL server test parameters
$check_mysql_server   = isset($GLOBALS['check_mysql_server'])?$GLOBALS['check_mysql_server']:'';
$check_mysql_username = isset($GLOBALS['check_mysql_username'])?$GLOBALS['check_mysql_username']:'';
$check_mysql_password = isset($GLOBALS['check_mysql_password'])?$GLOBALS['check_mysql_password']:'';
$check_mysql_database = isset($GLOBALS['check_mysql_database'])?$GLOBALS['check_mysql_database']:'';

// PostgreSQL server test parameters
$check_pgsql_server   = isset($GLOBALS['check_pgsql_server'])?$GLOBALS['check_pgsql_server']:'';
$check_pgsql_username = isset($GLOBALS['check_pgsql_username'])?$GLOBALS['check_pgsql_username']:'';
$check_pgsql_password = isset($GLOBALS['check_pgsql_password'])?$GLOBALS['check_pgsql_password']:'';
$check_pgsql_database = isset($GLOBALS['check_pgsql_database'])?$GLOBALS['check_pgsql_database']:'';
$check_pgsql_schema   = isset($GLOBALS['check_pgsql_schema'])?$GLOBALS['check_pgsql_schema']:'';

$backend_array = array();

// Default backend is 'files'
$default_backend = 'files';

$backend_array[] = $default_backend;


// Tests counter
$tests = 0;


// Successes counter
$successes = 0;


$browser_mode = isset($_SERVER["HTTP_USER_AGENT"]);
$html_mode = (!$GLOBALS['minima']) && $browser_mode;

// $crlf will skip a line in command line mode and also in browser mode
$crlf   = $html_mode?"<br />\n":"\r\n";
$b_on   = $html_mode?'<b>':'';
$b_off  = $html_mode?'</b>':'';
$h2_on  = $html_mode?'<h2>':' *** ';
$h2_off = $html_mode?'</h2>':' *** ';
$hr     = $html_mode?'<hr />':'----------'.$crlf;
$i_on   = $html_mode?'<i>':'';
$i_off  = $html_mode?'</i>':'';
$ok_on  = $html_mode?'<span style="color: green;"><b>':'';
$ok_off = $html_mode?'</b></span>':'';
$ko_on  = $html_mode?'<span style="color: red;"><b>':'';
$ko_off = $html_mode?'</b></span>':'';


// Declare and initialize the Multiotp class if not done by an other file including this one
if (!isset($multiotp))
{
	$multiotp = new Multiotp('DefaultCliEncryptionKey');
}
$multiotp->SetMaxEventResyncWindow(500); // 500 is enough and quicker for the check
$multiotp->EnableVerboseLog(); // Could be helpful at the beginning

// $multiotp->_config_data['attributes_to_encrypt'] = '**';  // For test purposes only

// Write the configuration information in the configuration file
$multiotp->WriteConfigData();


if (('' != $check_mysql_server) &&
    ('' != $check_mysql_username) &&
    ('' != $check_mysql_password) &&
    ('' != $check_mysql_database)
   ) {
    $backend_array[] = 'mysql';
}

if (('' != $check_pgsql_server) &&
    ('' != $check_pgsql_username) &&
    ('' != $check_pgsql_password) &&
    ('' != $check_pgsql_database) &&
    ('' != $check_pgsql_schema)
   ) {
    $backend_array[] = 'pgsql';
}

if ($html_mode && (!isset($GLOBALS['no_header']))) {
    echo <<<EOWEBHEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>
            multiOTP class implementation check
        </title>
        <style>
            body {
                font-family: Verdana, Helvetica, Arial;
                color: black;
                font-size: 10pt;
                font-weight: normal;
                text-decoration: none;
            }
        </style>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
        <script>
            // Finds y value of given object
            function findPos(obj)
            {
                var curtop = 0;
                if (obj.offsetParent)
                {
                    do
                    {
                        curtop += obj.offsetTop;
                    } while (obj = obj.offsetParent);
                    return [curtop];
                }
            }

            // Scroll to an object
            function scrollToObject(object_div)
            {
                //Get object
                var ObjectDiv = document.getElementById(object_div);

                //Scroll to location of ObjectDiv
                window.scroll(0,findPos(ObjectDiv));
            }
        </script>
    </head>
    <body onload="scrollToObject('test_result');">
EOWEBHEADER;
}


//====================================================================
// Display header and version information
echo_full($crlf);
echo_full($b_on.$multiotp->GetClassName()." HOTP implementation check".$b_off.$crlf);
echo_full("(RFC 4226, http://www.ietf.org/rfc/rfc4226.txt)".$crlf);
echo_full("-----------------------------------------------".$crlf);
echo_full($crlf);
echo_full($multiotp->GetFullVersionInfo());
echo_full(", running with PHP version ".phpversion().$crlf);
echo_full($crlf);
if (!$GLOBALS['minima']) {
    echo_full("Library hash: ".str_replace("\t",", ",$multiotp->GetLibraryHash()).$crlf);
    echo_full($crlf);
}
echo_full("Valid algorithms: ".str_replace("\t",", ",$multiotp->GetAlgorithmsList()).$crlf);
echo_full($crlf);
echo_full($b_on."List of supported SMS providers".$b_off.$crlf);
echo_full(str_replace("\t",$crlf,$multiotp->GetSmsProvidersList()));
echo_full($crlf);

foreach ($backend_array as $backend) {
    $multiotp->SetBackendType($backend);
    if ('mysql' == $backend) {
        $multiotp->SetSqlServer($check_mysql_server);
        $multiotp->SetSqlUsername($check_mysql_username);
        $multiotp->SetSqlPassword($check_mysql_password);
        $multiotp->SetSqlDatabase($check_mysql_database);
        $multiotp->InitializeBackend();
    } elseif ('pgsql' == $backend) {
        $multiotp->SetSqlServer($check_pgsql_server);
        $multiotp->SetSqlUsername($check_pgsql_username);
        $multiotp->SetSqlPassword($check_pgsql_password);
        $multiotp->SetSqlDatabase($check_pgsql_database);
        $multiotp->SetSqlSchema($check_pgsql_schema);
        $multiotp->InitializeBackend();
    }
    $multiotp->WriteConfigData();
    if (!$GLOBALS['minima']) {
        echo $hr;
        echo_full($crlf);
        echo "Selected backend: ".$b_on.$backend.$b_off.$crlf;
        echo_full($crlf);
    }


    //====================================================================
    // TEST: Backup
    $tests++;
    echo_full($b_on."Configuration backup".$b_off.$crlf);

    @set_time_limit(0); // No time limit fot the backup

    $backup_start = time();
    $backup_file = sys_get_temp_dir().DIRECTORY_SEPARATOR."multiotp-backup-".date("Ymd-His").".bin";
    if (file_exists($backup_file)) {
      unlink($backup_file);
    }
    $encryption_key = "-backup-";
    $return_content = false;

    // $backup_file = "@";
    echo_full("Backup file: ".$backup_file.$crlf);
    $backup_content = $multiotp->BackupConfiguration(array("backup_file"    => $backup_file,
                                                           "encryption_key" => $encryption_key,
                                                           "encrypt_all"    => false, // is set to true by default
                                                           "return_content" => $return_content,
                                                           "keep_file"      => true,
                                                           "no_send"        => true
                                                          ));
    if ($return_content) {
        echo str_replace("\n", "<br />\n", $backup_content);
    }
    /*
    $file_handler = fopen($backup_file,"rt");
    while (!feof($file_handler)) {
        echo_full(fgets($file_handler));
        echo_full($crlf);
    }
    fclose($file_handler);
    */

    $backup_time = time() - $backup_start;
    if (0 >= $backup_time) {
        $backup_time += 1;
    }

    if (false !== $backup_content) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Configuration successfully backed up with pass $encryption_key in $backup_time second".(($backup_time > 1)?"s":"").$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to backup the configuration".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Clear the log
    $tests++;
    echo_full($b_on."Clear the log".$b_off.$crlf);
    if ($multiotp->ClearLog())
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Log successfully cleared".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to clear the log".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Write in the log
    $tests++;
    echo_full($b_on."Write in the log".$b_off.$crlf);
    
    $test_tag = '['.date("YmdHis").']';
    
    $multiotp->WriteLog("Test: test tag is $test_tag", FALSE, FALSE, 19, 'System', '');
    $log_content = $multiotp->ShowLog(TRUE);
    if (FALSE !== mb_strpos($log_content, $test_tag))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Log successfully updated".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to write in the log".$crlf);
    }
    echo_full($crlf);


    //============================================
    // TEST: Write the configuration file/database
    $tests++;
    echo_full($b_on."Write the configuration file/database".$b_off.$crlf);
    $backup_prefix = $multiotp->GetVerboseLogPrefix();
    $multiotp->SetVerboseLogPrefix('tESt');
    if ($multiotp->WriteConfigData())
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Configuration successfully written".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Failed while writing the configuration".$crlf);
    }
    echo_full($crlf);


    //===========================================
    // TEST: Read the configuration file/database
    $tests++;
    echo_full($b_on."Read the configuration file/database".$b_off.$crlf);
    $multiotp->SetVerboseLogPrefix('EMPTY');
    $multiotp->ReadConfigData();
    $test_prefix = $multiotp->GetVerboseLogPrefix();
    $multiotp->SetVerboseLogPrefix($backup_prefix);
    $multiotp->WriteConfigData();
    if ('tESt' == $test_prefix)
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Configuration successfully read".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Failed while reading the configuration ($test_prefix)".$crlf);
    }
    echo_full($crlf);


    $yubikey_class = new MultiotpYubikey();
    
    //====================================================================
    // TEST: Hexa to ModHex
    $tests++;
    echo_full($b_on."Check Hexa to ModHex encoding".$b_off.$crlf);
    $source   = '0123456789abcdef';
    $expected = 'cbdefghijklnrtuv';
    if (FALSE === ($result = $yubikey_class->HexToModHex($source)))
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Hexa to ModHex encoding failed".$crlf);
    }
    elseif ($expected == $result)
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Hexa to ModHex encoding successful ($result)".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Hexa to ModHex encoding failed ($result instead of $expected)".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: ModHex to Hexa
    $tests++;
    echo_full($b_on."Check ModHex to Hexa decoding".$b_off.$crlf);
    
    $source   = 'cbdefghijklnrtuv';
    $expected = '0123456789abcdef';
    if (FALSE === ($result = $yubikey_class->ModHexToHex($source)))
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." ModHex to Hexa encoding failed".$crlf);
    }
    elseif ($expected == $result)
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." ModHex to Hexa encoding successful ($result)".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." ModHex to Hexa encoding failed ($result instead of $expected)".$crlf);
    }
    echo_full($crlf);


   //====================================================================
    // Delete the user test_user if it exists
    echo_full($i_on);
    echo_full("Deleting the test_user".$crlf);
    if (!$multiotp->DeleteUser('test_user', TRUE))
    {
        echo_full("- INFO: User test_user doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: User test_user successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


   //====================================================================
    // Delete the user test_user twice if it exists
    echo_full($i_on);
    echo_full("Deleting the test_user (twice)".$crlf);
    if (!$multiotp->DeleteUser('test_user', TRUE))
    {
        echo_full("- INFO: User test_user doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: User test_user successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // Delete the token test_token if it exists
    echo_full($i_on);
    echo_full("Deleting the test_token".$crlf);
    if (!$multiotp->DeleteToken('test_token'))
    {
        echo_full("- INFO: Token test_token doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: Token test_token successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // TEST: Creating token test_token with the RFC test values HOTP token
    $tests++;
    echo_full($b_on."Creating token test_token with the RFC test values HOTP token".$b_off.$crlf);
    if ($multiotp->CreateToken('test_token', 'HOTP', '3132333435363738393031323334353637383930', 6, -1))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token test_token successfully created".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of test_token token failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Creating user test_user with the HOTP RFC test token test_token created before
    $tests++;
    echo_full($b_on."Creating user test_user with the HOTP RFC test token test_token created before".$b_off.$crlf);
    if (!$multiotp->CreateUserFromToken('test_user', 'test_token'))
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Token test_token doesn't exist".$crlf);
    }
    else
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_user successfully created with token test_token".$crlf);
        $successes++;
    }
    $multiotp->SetUser('test_user');
    $multiotp->SetUserPrefixPin(0);
    $multiotp->WriteUserData();
    echo_full($crlf);

    
    //====================================================================
    // TEST: Authenticating test_user with the first token of the RFC test values
    $tests++;
    echo_full($b_on."Authenticating test_user with the first token of the RFC test values".$b_off.$crlf);
    $multiotp->SetUser('test_user');
    if (0 == ($error = $multiotp->CheckToken('755224')))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token of the user test_user successfully accepted".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Error authenticating the user test_user with the first token".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Testing the replay rejection
    $tests++;
    echo_full($b_on."Testing the replay rejection".$b_off.$crlf);
    $multiotp->SetUser('test_user');
    if (0 != ($error = $multiotp->CheckToken('755224')))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token of the user test_user successfully REJECTED (replay)".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Replayed token *WRONGLY* accepted".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Resynchronizing the key
    $tests++;
    echo_full($b_on."Resynchronizing the key".$b_off.$crlf);
    if ($multiotp->ResyncUserToken('test_user', '338314', '254676', (!$browser_mode)))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token of the user test_user successfully resynchronized".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Token of the user test_user NOT resynchronized".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Testing a false resynchronisation (in the past, may take some time)
    $tests++;
    echo_full($b_on."Testing a false resynchronisation (in the past, may take some time)".$b_off.$crlf);
    $multiotp->SetUser('test_user');
    $start_time = time();
    if (!$multiotp->ResyncToken('287082', '359152', (!$browser_mode)))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token of test_user successfully NOT resynchronized (in the past), in less than ".(1+time()-$start_time)." second(s) ".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Token of user test_user *WRONGLY* resynchronized".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Get user information
    $tests++;
    echo_full($b_on."Getting user information".$b_off.$crlf);
    $test_user_info = $multiotp->GetUserInfo('test_user');

    if ($test_user_info != "")
    {
        echo_full(nl2br($test_user_info));
        echo_full("- ".$ok_on.'OK!'.$ok_off." User information returned".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." User information not returned".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // Delete the user test_user8 if it exists
    echo_full($i_on);
    echo_full("Deleting the test_user8".$crlf);
    if (!$multiotp->DeleteUser('test_user8', TRUE))
    {
        echo_full("- INFO: User test_user8 doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: User test_user8 successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // Delete the token test_token8 if it exists
    echo_full($i_on);
    echo_full("Deleting the test_token8".$crlf);
    if (!$multiotp->DeleteToken('test_token8'))
    {
        echo_full("- INFO: Token test_token8 doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: Token test_token8 successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // TEST: Creating token test_token8 with the RFC test values HOTP token
    $tests++;
    echo_full($b_on."Creating token test_token8 with the RFC test values HOTP token".$b_off.$crlf);
    if ($multiotp->CreateToken('test_token8', 'HOTP', '3132333435363738393031323334353637383930', 8, -1))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token test_token8 successfully created".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of test_token8 token failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Creating user test_user8 with the HOTP RFC test token test_token8 created before
    $tests++;
    echo_full($b_on."Creating user test_user8 with the HOTP RFC test token test_token8 created before".$b_off.$crlf);
    if (!$multiotp->CreateUserFromToken('test_user8', 'test_token8'))
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Token test_token8 doesn't exist".$crlf);
    }
    else
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_user8 successfully created with token test_token8".$crlf);
        $successes++;
    }
    $multiotp->SetUser('test_user8');
    $multiotp->SetUserPrefixPin(0);
    $multiotp->WriteUserData();
    echo_full($crlf);


    //====================================================================
    // TEST: Authenticating test_user8 with the first token of the RFC test values
    $tests++;
    echo_full($b_on."Authenticating test_user8 with the first 8 digits token of the RFC test values".$b_off.$crlf);
    $multiotp->SetUser('test_user8');
    if (0 == ($error = $multiotp->CheckToken('84755224')))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token of the user test_user8 successfully accepted".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Error authenticating the user test_user8 with the first token".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Deleting the Test_user2 if it exists
    echo_full($i_on);
    echo_full("Deleting the Test_user2".$crlf);
    if (!$multiotp->DeleteUser('Test_user2', TRUE))
    {
        echo_full("- INFO: User Test_user2 doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: User Test_user2 successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // TEST: Creating user tEst_user2 with the RFC test values HOTP token and PIN prefix
    $tests++;
    echo_full($b_on."Creating user tEst_user2 with the RFC test values HOTP token and PIN prefix".$b_off.$crlf);
    if ($multiotp->CreateUser('tEst_user2',1,'HOTP','3132333435363738393031323334353637383930','*!1-2-3-4?*',6,0))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User tEst_user2 successfully created".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of user tEst_user2 failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Authenticating teSt_user2 with the first token of the RFC test values with PIN
    $tests++;
    echo_full($b_on."Authenticating teSt_user2 with the first token of the RFC test values with PIN".$b_off.$crlf);
    $multiotp->SetUser('teSt_user2');
    if (0 == ($error = $multiotp->CheckToken('*!1-2-3-4?*755224')))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token of the user teSt_user2 (with prefix PIN) successfully accepted".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Error #".$error." authenticating user teSt_user2 with the first token and PIN prefix".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // Delete the user fast_user if it exists
    echo_full($i_on);
    echo_full("Deleting the user fast_user".$crlf);
    if (!$multiotp->DeleteUser('fast_user', TRUE))
    {
        echo_full("- INFO: User fast_user doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: User fast_user successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // Delete the user fast_user_renamed if it exists
    echo_full($i_on);
    echo_full("Deleting the user fast_user_renamed".$crlf);
    if (!$multiotp->DeleteUser('fast_user_renamed', TRUE))
    {
        echo_full("- INFO: User fast_user_renamed doesn't exist yet".$crlf);
    }
    else
    {
        echo_full("- INFO: User fast_user_renamed successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // TEST: Creating user fast_user using the one parameter FastCreateUser() function
    $tests++;
    echo_full($b_on."Creating user fast_user using the one parameter FastCreateUser() function".$b_off.$crlf);
    if ($multiotp->FastCreateUser('fast_user'))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User fast_user successfully created".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of user fast_user failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Check if user fast_user exists
    $tests++;
    echo_full($b_on."Check if the user fast_user exists".$b_off.$crlf);

    if ($multiotp->CheckUserExists('fast_user'))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User fast_user exists".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." fast_user does not exist".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Rename user fast_user
    $tests++;
    echo_full($b_on."Renaming the user fast_user to fast_user_renamed".$b_off.$crlf);

    $multiotp->SetUser('fast_user');

    if ($multiotp->RenameCurrentUser('fast_user_renamed'))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User fast_user successfully renamed to fast_user_renamed".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." RenameCurrentUser function failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Creating user test_wo2fa without 2FA token, but with a prefix PIN
    $tests++;
    echo_full($b_on."Creating user test_wo2fa without 2FA token, but with a prefix PIN".$b_off.$crlf);
    if ($multiotp->CreateUser('test_wo2fa',1,'without2FA','','!prefixpin!',0,0)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_wo2fa successfully created".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of user test_wo2fa failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Authenticating test_wo2fa without 2FA token and with correct PIN
    $tests++;
    echo_full($b_on."Authenticating test_wo2fa without 2FA token and with correct PIN".$b_off.$crlf);
    $multiotp->SetUser('test_wo2fa');
    if (0 == ($error = $multiotp->CheckToken('!prefixpin!'))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_wo2fa (with prefix PIN) successfully accepted".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Error #".$error." authenticating user test_wo2fa with the prefix PIN".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Authenticating test_wo2fa without 2FA token and with incorrect PIN
    $tests++;
    echo_full($b_on."Authenticating test_wo2fa without 2FA token and with incorrect PIN".$b_off.$crlf);
    $multiotp->SetUser('test_wo2fa');
    if (0 != ($error = $multiotp->CheckToken('badprefixpin'))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_wo2fa (with incorrect prefix PIN) successfully refused".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Authenticating user test_wo2fa with the incorrect prefix PIN accepted".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // Delete the user test_wo2fa if it exists
    echo_full($i_on);
    echo_full("Deleting the user test_wo2fa".$crlf);
    if (!$multiotp->DeleteUser('test_wo2fa', TRUE)) {
        echo_full("- INFO: User test_wo2fa doesn't exist yet".$crlf);
    } else {
        echo_full("- INFO: User test_wo2fa successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // TEST: Creating user test_wo2fa2 without 2FA token and without a prefix PIN
    $tests++;
    echo_full($b_on."Creating user test_wo2fa2 without 2FA token and without prefix PIN".$b_off.$crlf);
    if ($multiotp->CreateUser('test_wo2fa2',0,'without2FA','','!noprefixpin!',0,0)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_wo2fa2 successfully created".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of user test_wo2fa2 failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Authenticating test_wo2fa2 without 2FA token and without a prefix PIN
    $tests++;
    echo_full($b_on."Authenticating test_wo2fa2 without 2FA token and without a prefix PIN".$b_off.$crlf);
    $multiotp->SetUser('test_wo2fa2');
    if (0 == ($error = $multiotp->CheckToken(''))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_wo2fa2 (without prefix PIN) successfully accepted".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Error #".$error." authenticating user test_wo2fa2 without prefix PIN".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Authenticating test_wo2fa2 without 2FA token and with incorrect prefix PIN
    $tests++;
    echo_full($b_on."Authenticating test_wo2fa2 without 2FA token and with incorrect prefix PIN".$b_off.$crlf);
    $multiotp->SetUser('test_wo2fa2');
    if (0 != ($error = $multiotp->CheckToken('badprefixpin'))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User test_wo2fa2 (with incorrect prefix PIN) successfully refused".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Authenticating user test_wo2fa2 with the incorrect prefix PIN accepted".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // Delete the user test_wo2fa2 if it exists
    echo_full($i_on);
    echo_full("Deleting the user test_wo2fa2".$crlf);
    if (!$multiotp->DeleteUser('test_wo2fa2', TRUE)) {
        echo_full("- INFO: User test_wo2fa2 doesn't exist yet".$crlf);
    } else {
        echo_full("- INFO: User test_wo2fa2 successfully deleted".$crlf);
    }
    echo_full($i_off);
    echo_full($crlf);


    //====================================================================
    // TEST: WriteUserData / ReadUserData with fast_user_renamed
    $tests++;
    echo_full($b_on."Write/Read information concerning user fast_user_renamed".$b_off.$crlf);
    $test_value = 'tESt';
    $multiotp->ReadUserData('fast_user_renamed');
    $multiotp->SetUserDescription($test_value);
    $multiotp->WriteUserData();
    $multiotp->SetUserDescription('');
    $multiotp->ReadUserData('fast_user_renamed');

    if ($test_value == $multiotp->GetUserDescription())
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Write/Read information for fast_user_renamed successfully done".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Write/Read information for fast_user_renamed failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Delete the user fast_user_renamed
    $tests++;
    echo_full($b_on."Deleting the user fast_user_renamed".$b_off.$crlf);
    if ($multiotp->DeleteUser('fast_user_renamed'))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User fast_user fast_user_renamed successfully deleted".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." DeleteUser function failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Check if user fast_user exists
    $tests++;
    echo_full($b_on."Check if the user fast_user does not exist".$b_off.$crlf);

    if (!$multiotp->CheckUserExists('fast_user'))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User fast_user does not exist".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." fast_user exist".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Creating a QRcode provisioning file for the HOTP RFC test token
    $tests++;
    echo_full($b_on."Creating a QRcode provisioning file for the HOTP RFC test token".$b_off.$crlf);
    $size_result = $multiotp->qrcode('otpauth://hotp/multiOTP hotp test?counter=0&digits=6&secret='.base32_encode(hex2bin('3132333435363738393031323334353637383930')).'&issuer=multiOTP test', $multiotp->GetScriptFolder().'qrcode/qrHOTP.png');
    if (0 < $size_result)
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." HOTP QRcode successfully created".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." HOTP QRcode not created".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Creating a QRcode provisioning file for the TOTP RFC test token
    $tests++;
    echo_full($b_on."Creating a QRcode provisioning file for the TOTP RFC test token".$b_off.$crlf);
    $size_result = $multiotp->qrcode('otpauth://totp/multiOTP totp test?period=30&digits=6&secret='.base32_encode(hex2bin('3132333435363738393031323334353637383930')).'&issuer=multiOTP test', $multiotp->GetScriptFolder().'qrcode/qrTOTP.png');
    if (0 < $size_result)
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." TOTP QRcode successfully created".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." TOTP QRcode not created".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // Display the QRcode in the browser using inline images
    if ($html_mode && ($size_result > 0))
    {
        echo_full("Displaying inline image for the test_user (HOTP QRCode Google Auhtenticator token)".$crlf);
        echo_full("<img src=\"data:image/png;base64,".base64_encode($multiotp->GetUserTokenQrCode('test_user', 'multiOTP test_user token'))."\" alt=\"test_user test token\">".$crlf);
        echo_full($crlf);

        echo_full("Displaying inline image for TOTP QRCode Google Auhtenticator token".$crlf);
        $binary_result = $multiotp->qrcode('otpauth://totp/multiOTP totp test?secret='.base32_encode(hex2bin('3132333435363738393031323334353637383930')).'&digits=6&period=30&issuer=multiOTP test', "binary");
        
        echo_full("<img src=\"data:image/png;base64,".base64_encode($binary_result)."\" alt=\"multiOTP TOTP test token\">".$crlf);
        echo_full($crlf);
    }


    //====================================================================
    // Display the user token URL link
    echo_full("User token URL link for test_user".$crlf);
    echo_full($multiotp->GetUserTokenUrlLink('test_user', 'multiOTP test_user token').$crlf);
    echo_full($crlf);


    //====================================================================
    // TEST: Check Base32 functions
    $tests++;
    echo_full($b_on."Check Base32 functions".$b_off." (should return 3132333435363738393031323334353637383930)".$crlf);

    if ('3132333435363738393031323334353637383930' == bin2hex(base32_decode(base32_encode(hex2bin('3132333435363738393031323334353637383930')))))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Base32 functions successfully checked".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Base32 function failed".$crlf);
    }
    echo_full($crlf);

    
    //====================================================================
    // Locking the test_user
    echo_full($i_on);
    echo_full("Locking the test_user".$crlf);
    $multiotp->SetUser('test_user');
    $multiotp->SetUserErrorCounter(1000);
    $multiotp->WriteUserData();
    $multiotp->CheckToken('LOCKME');
    echo_full($crlf);


    //====================================================================
    // Delaying the test_user8
    echo_full($i_on);
    echo_full("Delaying the test_user8".$crlf);
    $multiotp->SetUser('test_user8');
    $multiotp->CheckToken('LOCKME1');
    $multiotp->CheckToken('LOCKME2');
    $multiotp->CheckToken('LOCKME3');
    $multiotp->CheckToken('LOCKME4');
    echo_full($crlf);


    //====================================================================
    // Delaying the tesT_user2
    echo_full($i_on);
    echo_full("Delaying the tesT_user2".$crlf);
    $multiotp->SetUser('tesT_user2');
    $multiotp->CheckToken('LOCKME1');
    $multiotp->CheckToken('LOCKME2');
    $multiotp->CheckToken('LOCKME3');
    $multiotp->CheckToken('LOCKME4');
    echo_full($crlf);


    //====================================================================
    // TEST: Number of existing users
    $tests++;
    echo_full($b_on."Number of existing users".$b_off.$crlf);
    $count = $multiotp->GetUsersCount();
    if (0 < $count)
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." $count existing users".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Failed to count existing users".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of existing users (originally tab separated)
    $tests++;
    echo_full($b_on."List of existing users".$b_off.$crlf);
    $list = $multiotp->GetUsersList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if ('' != trim($list))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of active users (originally tab separated)
    $tests++;
    echo_full($b_on."List of active users".$b_off.$crlf);
    $list = $multiotp->GetActiveUsersList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if ('' != trim($list))
    {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    }
    else
    {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of locked users (originally tab separated)
    $tests++;
    echo_full($b_on."List of locked users".$b_off.$crlf);
    $list = $multiotp->GetLockedUsersList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if ('' != trim($list)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of delayed users (originally tab separated)
    $tests++;
    echo_full($b_on."List of delayed users".$b_off.$crlf);
    $list = $multiotp->GetDelayedUsersList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if ('' != trim($list)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of existing users in an array
    $tests++;
    echo_full($b_on."List of existing users in an array".$b_off.$crlf);
    $counter = 0;
    foreach($multiotp->GetDetailedUsersArray() as $one_detail) {
        echo_full($one_detail['user'].': '.encode_utf8_if_needed($one_detail['description']).$crlf);
        $counter++;
    }
    if ($counter > 0) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Check if user fast_user exists
    $tests++;
    echo_full($b_on."Check if the user fast_user does not exist".$b_off.$crlf);

    if (!$multiotp->CheckUserExists('fast_user')) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." User fast_user does not exist".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." fast_user exist".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Import CSV test tokens definition file
    $tests++;
    echo_full($b_on."Import CSV test tokens definition file".$b_off.$crlf);

    if ($multiotp->ImportTokensFile('test-tokens.csv', 'test-tokens.csv')) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." File test-tokens.csv successfully imported".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to import test-tokens.csv file".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // List of existing tokens (originally tab separated)
    $tests++;
    echo_full($b_on."List of existing CSV tokens".$b_off.$crlf);
    $list = $multiotp->GetTokensList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if (FALSE !== mb_strpos(mb_strtolower($list), mb_strtolower('ABCDEF012302'))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." CSV Token ABCDEF012302 is present".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." CSV Token ABCDEF012302 is missing".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Import PSKC test tokens definition file
    $tests++;
    echo_full($b_on."Import PSKC test tokens definition file".$b_off.$crlf);
    
    $token_import_error = FALSE;
    if (!$multiotp->ImportTokensFile('oath/pskc-hotp-aes.txt', 'pskc-hotp-aes.txt', '12345678901234567890123456789012', '1122334455667788990011223344556677889900')) {
      if (!file_exists('oath/pskc-hotp-aes.txt')) {
        echo_full("- file oath/pskc-hotp-aes.txt doesn't exists".$crlf);
      } else {
        echo_full("- oath/pskc-hotp-aes.txt not imported correctly".$crlf);
      }
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/pskc-hotp-pbe.txt', 'pskc-hotp-pbe.txt', 'qwerty', 'bdaab8d648e850d25a3289364f7d7eaaf53ce581')) {
      echo_full("- oath/pskc-hotp-pbe.txt not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/pskc-totp-aes.txt', 'pskc-totp-aes.txt', '12345678901234567890123456789012', '1122334455667788990011223344556677889900')) {
      echo_full("- oath/pskc-totp-aes.txt not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/pskc-totp-pbe.txt', 'pskc-totp-pbe.txt', 'qwerty', 'bdaab8d648e850d25a3289364f7d7eaaf53ce581')) {
      echo_full("- oath/pskc-totp-pbe.txt not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/tokens_hotp_aes.pskc', 'tokens_hotp_aes.pskc', '12345678901234567890123456789012', '')) {
      echo_full("- oath/tokens_hotp_aes.pskc not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/tokens_totp_aes.pskc', 'tokens_totp_aes.pskc', '12345678901234567890123456789012', '')) {
      echo_full("- oath/tokens_totp_aes.pskc not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/tokens_hotp_pbe.pskc', 'tokens_hotp_pbe.pskc', 'qwerty', '')) {
      echo_full("- oath/tokens_hotp_pbe.pskc not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    if (!$multiotp->ImportTokensFile('oath/tokens_totp_pbe.pskc', 'tokens_totp_pbe.pskc', 'qwerty', '')) {
      echo_full("- oath/tokens_totp_pbe.pskc not imported correctly".$crlf);
      $token_import_error = TRUE;
    }
    // $multiotp->ImportTokensFile('oath/tokens_ocra_aes.pskc', 'tokens_ocra_aes.pskc', '12345678901234567890123456789012', '');
    // $multiotp->ImportTokensFile('oath/tokens_ocra_pbe.pskc', 'tokens_ocra_pbe.pskc', 'qwerty', '');

    if (!$token_import_error) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Test files from oath successfully imported".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to import test files from ".getcwd()."/oath".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // List of existing tokens (originally tab separated)
    $tests++;
    echo_full($b_on."List of existing tokens".$b_off.$crlf);
    $list = $multiotp->GetTokensList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if (FALSE !== mb_strpos(mb_strtolower($list), mb_strtolower('ZZ0100000000'))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token ZZ0100000000 is present".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Token ZZ0100000000 is missing".$crlf);
    }
    echo_full($crlf);

    
    //====================================================================
    // TEST: Rename token ZZ0100000000 to ZZ0100000001
    $tests++;
    echo_full($b_on."Rename token ZZ0100000000 to ZZ0100000001".$b_off.$crlf);
    $multiotp->SetToken('ZZ0100000000');
    if ($multiotp->RenameCurrentToken('ZZ0100000001')) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token ZZ0100000000 successfully renamed to ZZ0100000001".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to rename the token ZZ0100000000".$crlf);
        $multiotp->DeleteToken('ZZ0100000000');
    }
    echo_full($crlf);


    //=======================================
    // Check if the token ZZ0100000001 exists
    $tests++;
    echo_full($b_on."Check if the token ZZ0100000001 exists".$b_off.$crlf);
    if ($multiotp->CheckTokenExists('ZZ0100000001')) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Token ZZ0100000001 exists".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Token ZZ0100000001 is missing".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: WriteTokenData / ReadTokenData with ZZ0100000001
    $tests++;
    echo_full($b_on."Write/Read information concerning token ZZ0100000001".$b_off.$crlf);
    $test_value = 'tEStToKeN';
    $multiotp->ReadTokenData('ZZ0100000001');
    $multiotp->SetTokenDescription($test_value);
    $multiotp->WriteTokenData();
    $multiotp->SetTokenDescription('');
    $multiotp->ReadTokenData('ZZ0100000001');

    if ($test_value == $multiotp->GetTokenDescription()) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Write/Read information concerning token ZZ0100000001 successfully done".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Write/Read information concerning token ZZ0100000001 failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Delete tokens ZZ0000000000 and ZZ0100000001
    $tests++;
    echo_full($b_on."Delete tokens ZZ0000000000 and ZZ0100000001".$b_off.$crlf);
    if (($multiotp->DeleteToken('ZZ0000000000')) && ($multiotp->DeleteToken('ZZ0100000001')) && (!$multiotp->CheckTokenExists('ZZ0100000001'))) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Tokens ZZ0000000000 and ZZ0100000001 successfully deleted".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Failed during tokens deletion".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Create the device 123456 test_device
    $tests++;
    $multiotp->DeleteDevice("123456", TRUE);
    echo_full($b_on."Create the device test_device (123456)".$b_off.$crlf);
    if ($multiotp->CreateDevice("123456", 'test_device', 'test_secret', '123.124.125.126', '255.255.255.255', 'test_device', FALSE)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Device test_device successfully created".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of device test_device failed".$crlf);
    }
    echo_full($crlf);

    
    //====================================================================
    // TEST: Read the device 123456 test_device
    $tests++;
    echo_full($b_on."Read the device test_device (123456)".$b_off.$crlf);
    $multiotp->SetDeviceDescription('');
    $multiotp->ReadDeviceData("123456");
    $description = $multiotp->GetDeviceDescription();
    if ('test_device' == $description) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Device test_device successfully read".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Failed to read the device test_device".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of existing devices (originally tab separated)
    $tests++;
    echo_full($b_on."List of existing devices".$b_off.$crlf);
    $list = $multiotp->GetDevicesList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if ('' != trim($list)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Delete the device test_device
    $tests++;
    echo_full($b_on."Delete the device test_device".$b_off.$crlf);
    if ($multiotp->DeleteDevice("123456")) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Device test_device successfully deleted".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." DeleteDevice function failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Create the group 123456 test_group
    $tests++;
    $multiotp->DeleteGroup("123456", TRUE);
    echo_full($b_on."Create the group test_group (123456)".$b_off.$crlf);
    if ($multiotp->CreateGroup("123456", 'test_group', 'test_description')) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Group test_group successfully created".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Creation of group test_group failed".$crlf);
    }
    echo_full($crlf);

    
    //====================================================================
    // TEST: Read the group 123456 test_group
    $tests++;
    echo_full($b_on."Read the group test_group (123456)".$b_off.$crlf);
    $multiotp->SetGroupDescription('');
    $multiotp->ReadGroupData("123456");
    $description = $multiotp->GetGroupDescription();
    if ('test_description' == $description) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Group test_group successfully read".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Failed to read the group test_group".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Check if the group 123456 test_group exists
    $tests++;
    echo_full($b_on."Check if the group test_group (123456) exists".$b_off.$crlf);
    if ($multiotp->CheckGroupExists("123456")) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Group test_group exists".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Group test_group doesn't exists".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Set an ldap_in_group with special chars
    $tests++;
    echo_full($b_on."Set an ldap_in_group with special chars".$b_off.$crlf);
    $my_groups = "\"My-group\"";
    $multiotp->SetLdapInGroup($my_groups);
    $multiotp->WriteConfigData();
    $multiotp->ReadConfigData();
    $in_group = $multiotp->GetLdapInGroup();

    if ($in_group == $my_groups) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." The ldap-in-group is set correctly (<b>".htmlentities($in_group)."</b>)".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ok_on.'OK!'.$ok_off." The ldap-in-group is not set correctly (<b>".htmlentities($in_group)."</b>)".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: List of existing groups (originally tab separated)
    $tests++;
    echo_full($b_on."List of existing groups".$b_off.$crlf);
    $list = $multiotp->GetGroupsList();
    echo_full(str_replace("\t",", ",$list).$crlf);
    if ('' != trim($list)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." List is not empty".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." List is empty".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Delete the group test_group
    $tests++;
    echo_full($b_on."Delete the group test_group".$b_off.$crlf);
    if ($multiotp->DeleteGroup(123456)) {
        echo_full("- ".$ok_on.'OK!'.$ok_off." Group test_group successfully deleted".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." DeleteGroup function failed".$crlf);
    }
    echo_full($crlf);


    //====================================================================
    // TEST: Show the log
    $tests++;
    echo_full($b_on."Show the log".$b_off.$crlf);
    if (FALSE !== ($log = $multiotp->ShowLog(TRUE))) {
        echo_full(str_replace("\n",$crlf, $log));
        echo_full("- ".$ok_on.'OK!'.$ok_off." Log successfuly displayed".$crlf);
        $successes++;
    } else {
        echo_full("- ".$ko_on.'KO!'.$ko_off." Unable to show the log".$crlf);
    }
    echo_full($crlf);
}


//====================================================================

echo_full($b_on."Network information".$b_off.$crlf);
$network_info = $multiotp->GetNetworkInfo();
echo_full(implode($crlf, $network_info));
echo_full($crlf);
echo_full($crlf);

// TESTS result
if ($html_mode) {
    echo_full('<div id="test_result">');
}
echo_full($b_on);
if ($successes == $tests) {
    echo($ok_on."OK! ALL $tests TESTS HAVE PASSED SUCCESSFULLY !".$ok_off.$crlf);
} else {
    echo($ko_on."KO! ONLY $successes/$tests TESTS HAVE PASSED SUCCESSFULLY !".$ko_off.$crlf);
}
echo_full($b_off);
if ($html_mode) {
    echo_full('</div>');
}
echo_full($crlf);

echo_full($hr);

echo_full("Time spent for the whole check.multiotp.class.php: ".(1+time()-$first_time)." second(s)");
echo_full($crlf);
echo_full($crlf);

$multiotp->SetBackendType($default_backend);
$multiotp->_config_data['attributes_to_encrypt'] = '';
$multiotp->WriteConfigData();

if ($html_mode && (!isset($GLOBALS['no_header']))) {
    echo <<<EOWEBFOOTER
    </body>
</html>
EOWEBFOOTER;
}
?>