<?php
/**
 * @file  multiotp.cli.header.php
 * @brief Command line implementation of the multiOTP PHP class.
 *
 * multiOTP PHP CLI header - Strong two-factor authentication PHP class
 * https://www.multiotp.net
 *
 * Visit http://forum.multiotp.net/ for additional support.
 *
 * Donation are always welcome! Please check https://www.multiOTP.net
 * and you will find the magic button ;-)
 *
 * If the name of this file is multiotp.php, it means that it is already
 * the result of the merge of the two files multiotp.cli.header.php and
 * multiotp.class.php
 *
 * The MultiOTP PHP CLI header is simply merged with the MultiOTP PHP
 * class in order to provide an authentication command line script.
 *
 * This script can be used as an external authentication provider with at
 * least the following RADIUS servers:
 *  - TekRADIUS LT, a free Radius server for Windows with SQLite backend
 *    (http:/www.tekradius.com)
 *  - TekRADIUS, a free Radius server for Windows with MS-SQL backend
 *    (http:/www.tekradius.com)
 *  - FreeRADIUS, a free Radius server implementation for Linux
 *    and *nix environments (http://freeradius.org)
 *  - WinRADIUS, the FreeRADIUS implementation ported for Windows
 *    (http://winradius.eu/)
 *
 * For Windows, you can also use the multiotp.exe file provided, which is
 * an embedded PHP interpreter together with the result of the merge.
 *
 * PHP 5.3.0 or higher is supported.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.9.4.0
 * @date      2022-11-04
 * @since     2010-06-08
 * @copyright (c) 2010-2022 SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2010-2022 SysCo systemes de communication sa
 *   SysCo (tm) is a trademark of SysCo systemes de communication sa
 *   (http://www.sysco.ch)
 *   All rights reserved.
 * 
 *   This file is part of the MultiOTP PHP class
 *
 *   MultiOTP PHP class is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public License as
 *   published by the Free Software Foundation, either version 3 of the License,
 *   or (at your option) any later version.
 * 
 *   MultiOTP PHP class is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 * 
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with MultiOTP PHP class.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Command line usage
 *
 *   Type multiotp -help to have the full description of the options,
 *    and have a look at the readme.txt file for enhanced explanations
 *
 *
 * Return codes
 *
 *   0 OK: Token accepted
 *
 *   9 INFO: Access Challenge returned back to the client
 *  10 INFO: Access Challenge returned back to the client
 *
 *  11 INFO: User successfully created or updated
 *  12 INFO: User successfully deleted
 *  13 INFO: User PIN code successfully changed
 *  14 INFO: Token has been resynchronized successfully
 *  15 INFO: Tokens definition file successfully imported
 *  16 INFO: QRcode successfully created
 *  17 INFO: UrlLink successfully created
 *  18 INFO: SMS code request received
 *  19 INFO: Requested operation successfully done
 *
 *  20 ERROR: User blacklisted
 *  21 ERROR: User doesn't exist
 *  22 ERROR: User already exists
 *  23 ERROR: Invalid algorithm
 *  24 ERROR: Token locked (too many tries)
 *  25 ERROR: Token delayed (too many tries, but still a hope in a few minutes)
 *  26 ERROR: The time based token has already been used
 *  27 ERROR: Resynchronization of the token has failed
 *  28 ERROR: Unable to write the changes in the file
 *  29 ERROR: Token doesn't exist
 *
 *  30 ERROR: At least one parameter is missing
 *  31 ERROR: Tokens definition file doesn't exist
 *  32 ERROR: Tokens definition file not successfully imported
 *  33 ERROR: Encryption hash error, encryption key is not the same
 *  34 ERROR: Linked user doesn't exist
 *  35 ERROR: User not created
 *  36 ERROR: Token doesn't exist
 *  37 ERROR: Token already attributed
 *  38 ERROR: User is deactivated
 *  39 ERROR: Requested operation aborted
 *
 *  40 ERROR: SQL query error
 *  41 ERROR: SQL error
 *  42 ERROR: They key is not in the table schema
 *  43 ERROR: SQL entry cannot be updated
 *
 *  50 ERROR: QRcode not created
 *  51 ERROR: UrlLink not created (no provisionable client for this protocol)
 *  58 ERROR: File is missing
 *  59 ERROR: Bad restore configuration password
 *
 *  60 ERROR: No information on where to send SMS code
 *  61 ERROR: SMS code request received, but an error occured during transmission
 *  62 ERROR: SMS provider not supported
 *  63 ERROR: This SMS code has expired
 *  64 ERROR: Cannot resent an SMS code right now
 *  69 ERROR: Failed to send email
 *
 *  70 ERROR: Server authentication error
 *  71 ERROR: Server request is not correctly formatted
 *  72 ERROR: Server answer is not correctly formatted
 *  79 ERROR: AD/LDAP connection error
 *
 *  80 ERROR: Server cache error
 *  81 ERROR: Cache too old for this user, account autolocked
 *  82 ERROR: User not allowed for this device
 *  88 ERROR: Device is not defined as a HA slave
 *  89 ERROR: Device is not defined as a HA master
 *
 *  93 ERROR: Authentication failed (time based token probably out of sync)
 *  94 ERROR: API request error
 *  95 ERROR: API authentication failed
 *  96 ERROR: Authentication failed (CRC error)
 *  97 ERROR: Authentication failed (wrong private id)
 *  98 ERROR: Authentication failed (wrong token length)
 *  99 ERROR: Authentication failed (and other possible unknown errors)
 *
 *
 * Radius integration examples
 *
 *   Example 1 (FreeRADIUS 2.x under Linux or Windows, new fashion)
 *
 *     Have a look in the readme file, everything is explained.
 *
 *
 *   Example 2 (FreeRADIUS under Linux or Windows, old fashion)
 *
 *     Define a DEFAULT entry in the /etc/freeradius/users file like this:
 *     DEFAULT Auth-Type = Accept
 *     Exec-Program-Wait = "/usr/local/bin/multiotp.php %{User-Name} %{User-Password}",
 *     Fall-Through = Yes,
 *     Reply-Message = "Hello, %{User-Name}"
 *
 *
 *   Example 3 (TekRADIUS or TekRADIUS LT under Windows)
 *
 *     TekRADIUS supports a Default Username to be used when a matching user
 *     profile cannot be found for an incoming RADIUS authentication request.
 *     So a quick and easy way is to create in the TekRADIUS Manager a User
 *     named 'Default' that belongs to the existing 'Default' Group.
 *     Then add to this Default user the following attribute :
 *     Check  External-Executable  C:\multitop\multiotp.exe %ietf|1% %ietf|2%
 *
 *
 * External files created
 *
 *   Users database files in the subfolder called users
 *   Tokens database files in the subfolder called tokens
 *
 *
 * External file needed
 *
 *   Users database files in the subfolder called users
 *   Tokens database files in the subfolder called tokens
 *
 *
 * Special issues
 *
 *   If you need specific developements concerning strong authentication,
 *   do not hesistate to contact us per email at info@multiotp.net.
 *
 *
 * Change Log
 *
 *   Please check the readme file for the whole change log since 2010
 *********************************************************************/

global $argc;
global $argv;

if (!isset($multiotp)) {
	  require_once('multiotp.class.php');
}

// Trick to define the current folder as the script folder
function get_script_dir()
{
    // Detect the current folder, change Windows notation to universal notation if needed
    $current_folder = convert_to_unix_path(getcwd());
    $current_script_folder = convert_to_unix_path(isset($_SERVER["argv"][0])?$_SERVER["argv"][0]:'');
    if ('' == (trim($current_script_folder))) {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $current_script_folder = $_SERVER['SCRIPT_FILENAME'];
        } elseif (isset($argv[0])) {
            $current_script_folder = dirname($current_folder."/".$argv[0]);
        }
    }
    
    if (false === mb_strpos($current_script_folder,"/")) {
        $current_script_folder_detected = dirname($current_folder."/fake.file");
    } else {
        $current_script_folder_detected = dirname($current_script_folder);
    }

    if (mb_substr($current_script_folder_detected,-1) != "/") {
        $current_script_folder_detected.="/";
    }
    return convert_to_windows_path_if_needed($current_script_folder_detected);
}


// Function to convert into a unix path notation
function convert_to_unix_path(
    $path
) {
    return str_replace("\\","/",$path);
}


// Function to convert into a windows path notation if needed
function convert_to_windows_path_if_needed(
    $path
) {
    $result = $path;
    if (false !== mb_strpos($result,":")) {
        $result = str_replace("/","\\",$result);
    }
    return $result;
}


// Clean quotes of the parameters if any
if (!function_exists('clean_quotes')) {
    function clean_quotes(
        $value
    ) {
        $cleaned = FALSE;
        $var = $value;
        if ((1 < mb_strlen($var)) && ((('"' == mb_substr($var,0,1)) && ('"' == mb_substr($var,-1))) || (("'" == mb_substr($var,0,1)) && ("'" == mb_substr($var,-1))))) {
            $var = mb_substr($var, 1, mb_strlen($var)-2);
            $cleaned = TRUE;
        }
        if ($cleaned) {
          $var = clean_quotes($var);
        }
        return $var;
    }
}


// CLI mode initialization (if not, it's the http local proxy mode)
$cli_mode = TRUE;


// Local proxy mode (non-CLI mode) detection and adaptation
if ('127.0.0.1'==(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'')) {
    if (isset($_POST['argv']) || isset($_GET['argv'])) {
        $cli_mode = FALSE;
        $folder_path = dirname(__FILE__).'/';
        if (!@file_exists($folder_path.'scripts/')) {
          $folder_path = '/usr/local/bin/multiotp/';
        }
        if (!@file_exists($folder_path.'templates/')) {
            $folder_path = '';
        } else {
            $chdir_result = chdir($folder_path);
        }
        $detected_folder_path = $folder_path;
        ob_start();
    }
}

if ($cli_mode) {
    // We try to detect the current folder where multiOTP is installed
    $folder_path = get_script_dir();
    $chdir_result = chdir($folder_path);
    $detected_folder_path = $folder_path;

    // Trick to have mostly the correct timezone in embedded command line version
    // and to avoid error messages when using time functions
    if (function_exists("date_default_timezone_get"))
    {
        $actual_timezone = @date_default_timezone_get();
        if (function_exists("date_default_timezone_set"))
        {
            @date_default_timezone_set($actual_timezone);
        }
    }

    // Be sure that STDIN, STDOUT and STDERR are defined correctly for command line edition
    if (!defined('STDIN')) {
        define('STDIN', @fopen('php://stdin', 'r'));
    }
    if (!defined('STDOUT')) {
        define('STDOUT', @fopen('php://stdout', 'w'));
    }
    if (!defined('STDERR')) {
        define('STDERR', @fopen('php://stderr', 'w'));
    }
}


// Initialize some variables
$command             = "";
$not_a_command       = false;
$command_array       = array();
$call_method         = "";
$cp_mode             = false;
$display_help        = false;
$display_status      = false;
$prefix_pin          = false;
$crlf                = "\n"; // was chr(13).chr(10);
$result              = 99; // Unknown error
$token_id_creation   = false;
$mysql_parameters    = array();
$pgsql_parameters    = array();
$no_php_info         = false;
$param_info_debug    = false;
$show_false_pin      = false;
$base_dir            = '';
$source_tag          = '';
$source_ip           = '';
$source_mac          = '';
$calling_ip          = '';
$calling_mac         = '';
$chap_id             = '';
$chap_challenge      = '';
$chap_password       = '';
$ms_chap_challenge   = '';
$ms_chap_response    = '';
$ms_chap2_response   = '';
$verbose_prefix      = '';
$display_log         = false;
$enable_log          = false;
$verbose_log         = false;
$initialize_backend  = false;
$keep_local          = false;
$encrypted_password  = false;
$request_nt_key      = false;
$server_cache_level  = '';
$server_secret       = '';
$server_timeout      = '';
$server_url          = '';
$state               = '';
$sync_delete_retention_days = '';
$write_config_data   = false;
$write_param_data    = false;
$nt_key_only         = false;


// Extract all parameters
$param_count = 0;
$all_args = array();
$all_args_size = 20;

if ($cli_mode) {
    $loop_start = 1;
    $argv = isset($_SERVER["argv"]) ? $_SERVER["argv"] : (isset($argv) ? $argv : "");
    $argc = intval(isset($_SERVER["argc"]) ? $_SERVER["argc"] : (isset($argc) ? $argc : 0));
} else {
    $argv = array();
    $loop_start = 1;
    if (isset($_POST['argv']) || isset($_GET['argv'])) {
        $argv[] = __FILE__;
        $all_argv = explode(chr(0), base64_decode(isset($_POST['argv'])?$_POST['argv']:$_GET['argv']));
        foreach ($all_argv as $one_argv) {
            if ('' != trim($one_argv)) {
                $argv[] = trim($one_argv);
            }
        }
    }
    $argc = count($argv);
}


for ($arg_loop=$loop_start; $arg_loop < $argc; $arg_loop++) {

    $current_arg = encode_utf8_if_needed(clean_quotes($argv[$arg_loop]));
    
    $not_a_command = FALSE;

    if ("-activate" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "activate";
    } elseif ("-assign-token" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "assign-token";
    } elseif ("-callapi" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "callapi";
    } elseif ("-backup-config" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "backup-config";
    } elseif ("-call-method=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,13)) {
        $command = "call-method";
        $src_array = explode("=",$current_arg,2);
        if (2 == count($src_array)) {
            $call_method = clean_quotes($src_array[1]);
        }
    } elseif ("-check" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "check";
    } elseif ("-iswithout2fa" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "iswithout2fa";
    } elseif ("-check-ldap-password" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "check-ldap-password";
    } elseif ("-checkpam" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "checkpam";
    } elseif ("-clearlog" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "clearlog";
    } elseif ("-config" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "config";
    } elseif ("-create" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "create";
    } elseif ("-createga" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "createga";
    } elseif ("-custominfo" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "custominfo";
    } elseif ("-default-dialin-ip-mask" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "default-dialin-ip-mask";
    } elseif ("-delete" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "delete";
    } elseif ("-delete-token" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "delete-token";
    } elseif ("-deactivate" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "deactivate";
    } elseif ("-desactivate" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "desactivate";
    } elseif ("-dialin-ip-address" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "dialin-ip-address";
    } elseif ("-dialin-ip-mask" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "dialin-ip-mask";
    } elseif ("-fastcreate" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "fastcreate";
    } elseif ("-fastcreatenopin" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "fastcreatenopin";
    } elseif ("-fastcreatewithpin" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "fastcreatewithpin";
    } elseif ("-help" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "help";
    } elseif ("-import" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import";
    } elseif ("-import-alpine-xml" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-alpine-xml";
    } elseif ("-import-csv" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-csv";
    } elseif ("-import-dat" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-dat";
    } elseif ("-import-pskc" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-pskc";
    } elseif ("-import-sql" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-sql";
    } elseif ("-import-xml" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-xml";
    } elseif ("-import-yubikey" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "import-yubikey";
    } elseif ("-initialize-backend" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "initialize-backend";
        $initialize_backend = true;
    } elseif ("-lockeduserslist" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "lockeduserslist";
    } elseif ("-ldap-users-list" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "ldap-users-list";
    } elseif ("-ldap-users-sync" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "ldap-users-sync";
    } elseif ("-ldap-user-info" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "ldap-user-info";
    } elseif ("-ldap-check" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "ldap-check";
    } elseif ("-phpinfo" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "phpinfo";
    } elseif ("-libhash" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "libhash";
    } elseif ("-lock" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "lock";
    } elseif ("-mysql" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "mysql";
    } elseif ("-pgsql" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "pgsql";
    } elseif ("-php-version" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "php-version";
    } elseif ("-purge-lock-folder" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "purge-lock-folder";
    } elseif ("-purge-ldap-cache-folder" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "purge-ldap-cache-folder";
    } elseif ("-qrcode" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "qrcode";
    } elseif ("-requiresms" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "requiresms";
    } elseif ("-remove-token" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "remove-token";
    } elseif ("-restore-config" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "restore-config";
    } elseif ("-resync" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "resync";
    } elseif ("-scratchlist" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "scratchlist";
    } elseif ("-seed-info" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "seed";
    } elseif ("-set" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "set";
    } elseif ("-showlog" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "showlog";
    } elseif ("-unlock" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "unlock";
    } elseif ("-update" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "update";
    } elseif ("-update-pin" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "update-pin";
    } elseif ("-urllink" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "urllink";
    } elseif ("-user-info" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "user-info";
    } elseif ("-userslist" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "userslist";
    } elseif (("-version" == mb_strtolower($current_arg,'UTF-8')) || ("-v" == mb_strtolower($current_arg,'UTF-8'))) {
        $command = "version";
    } elseif ("-version-only" == mb_strtolower($current_arg,'UTF-8')) {
        $command = "version-only";
    } else {
        // The current argument is not a command
        $not_a_command = TRUE;
        if ("-base-dir=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,10)) {
            $base_array = explode("=",$current_arg,2);
            if (2 == count($base_array)) {
                $base_dir = clean_quotes($base_array[1]);
            }
        } elseif ("-src=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,5)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $source_ip = clean_quotes($src_array[1]);
            }
        } elseif ("-tag=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,5)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $source_tag = clean_quotes($src_array[1]);
            }
        } elseif ("-mac=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,5)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $source_mac = clean_quotes($src_array[1]);
            }
        } elseif ("-calling-ip=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,12)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $calling_ip = clean_quotes($src_array[1]);
            }
        } elseif ("-calling-mac=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,13)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $calling_mac = clean_quotes($src_array[1]);
            }
        } elseif ("-chap-id=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,16)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $chap_id = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(mb_substr($chap_id,0,6),'UTF-8')) || ("%ietf" == mb_strtolower(mb_substr($chap_id,0,5),'UTF-8'))) {
                    $chap_id = '';
                }
            }
        } elseif ("-chap-challenge=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,16)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $chap_challenge = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(mb_substr($chap_challenge,0,6),'UTF-8')) || ("%ietf" == mb_strtolower(mb_substr($chap_challenge,0,5),'UTF-8'))) {
                    $chap_challenge = '';
                }
            }
        } elseif ("-chap-password=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,15)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $chap_password = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(mb_substr($chap_password,0,6),'UTF-8')) || ("%ietf" == mb_strtolower(mb_substr($chap_password,0,5),'UTF-8'))) {
                    $chap_password = '';
                } else {
                    $encrypted_password = true;
                }
            }
        } elseif ("-ms-chap-challenge=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,19)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $ms_chap_challenge = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(mb_substr($ms_chap_challenge,0,6),'UTF-8')) || ("%ietf" == mb_strtolower(mb_substr($ms_chap_challenge,0,5),'UTF-8'))) {
                    $ms_chap_challenge = '';
                }
            }
        } elseif ("-ms-chap-response=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,18)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $ms_chap_response = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(mb_substr($ms_chap_response,0,6),'UTF-8')) || ("%ietf" == mb_strtolower(mb_substr($ms_chap_response,0,5),'UTF-8'))) {
                    $ms_chap_response = '';
                } else {
                    $encrypted_password = true;
                }
            }
        } elseif ("-ms-chap2-response=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,19)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $ms_chap2_response = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(mb_substr($ms_chap2_response,0,6),'UTF-8')) || ("%ietf" == mb_strtolower(mb_substr($ms_chap2_response,0,5),'UTF-8'))) {
                    $ms_chap2_response = '';
                } else {
                    $encrypted_password = true;
                }
            }
        } elseif ("-server-url=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,12)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_url = trim(str_replace(",",";",str_replace(" ",";",clean_quotes($src_array[1]))));
            }
        } elseif ("-server-cache-level=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,20)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_cache_level = clean_quotes($src_array[1]);
            }
        } elseif ("-server-secret=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,15)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_secret = clean_quotes($src_array[1]);
            }
        } elseif ("-server-timeout=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,16)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_timeout = clean_quotes($src_array[1]);
            }
        } elseif ("-state=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,7)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $state = clean_quotes($src_array[1]);
            }
        } elseif ("-sync-delete-retention-days=" == mb_substr(mb_strtolower($current_arg,'UTF-8'),0,28)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $sync_delete_retention_days = clean_quotes($src_array[1]);
            }
        } elseif ("-cp" == mb_strtolower($current_arg,'UTF-8')) {
            $cp_mode = true;
        } elseif ("-debug" == mb_strtolower($current_arg,'UTF-8')) {
            $verbose_log = true;
        } elseif ("-display-log" == mb_strtolower($current_arg,'UTF-8')) {
            $display_log = true;
        } elseif ("-log" == mb_strtolower($current_arg,'UTF-8')) {
            $enable_log = true;
        } elseif ("-keep-local" == mb_strtolower($current_arg,'UTF-8')) {
            $keep_local = true;
        } elseif ("-no-php-info" == mb_strtolower($current_arg,'UTF-8')) {
            $no_php_info = true;
        } elseif ("-no-prefix-pin" == mb_strtolower($current_arg,'UTF-8')) {
            $set_prefix_pin = false;
        } elseif ("-nt-key-only" == mb_strtolower($current_arg,'UTF-8')) {
            $nt_key_only = true;
        } elseif ("-param" == mb_strtolower($current_arg,'UTF-8')) {
            $param_info_debug = true;
        } elseif ("-prefix-pin" == mb_strtolower($current_arg,'UTF-8')) {
            $set_prefix_pin = true;
        } elseif (("-request-nt-key" == mb_strtolower($current_arg,'UTF-8')) || ("--request-nt-key" == mb_strtolower($current_arg,'UTF-8'))) {
            $request_nt_key = true;
        } elseif ("-show-false-pin" == mb_strtolower($current_arg,'UTF-8')) {
            $show_false_pin = true;
        } elseif ("-status" == mb_strtolower($current_arg,'UTF-8')) {
            $display_status = true;
        } elseif ("-token-id" == mb_strtolower($current_arg,'UTF-8')) {
            $token_id_creation = true;
        } else {
            $param_count++;
            $all_args[$param_count] = $current_arg;
        }
    }

    if (("" != $command) && (!$not_a_command)) {
        $command_array[] = array('command'   => $command,
                                 'param_pos' => (1 + $param_count));
    }
}


// Be sure that non-existent parameters are empty
for ($i = ($param_count+1); $i <= $all_args_size; $i++) {
    $all_args[$i] = '';
}


// if not enough parameters, display error message
//  and indicate how to display the help page
if (($param_count < 1) &&
    ($command != "backup-config") &&
    ($command != "call-method") &&
    ($command != "checkpam") &&
    ($command != "clearlog") &&
    ($command != "custominfo") &&
    ($command != "network-info") &&
    ($command != "help") &&
    ($command != "initialize-backend") &&
    ($command != "ldap-check") &&
    ($command != "ldap-users-list") &&
    ($command != "ldap-users-sync") &&
    ($command != "libhash") &&
    ($command != "phpinfo") &&
    ($command != "showlog") &&
    ($command != "tokenslist")&&
    ($command != "userslist") &&
    ($command != "lockeduserslist") &&
    ($command != "version") &&
    ($command != "php-version") &&
    ($command != "purge-ldap-cache-folder") &&
    ($command != "purge-lock-folder") &&
    ($command != "version-only"))
{
    $command = "noparam";
    $command_array[] = array('command'   => $command,
                             'param_pos' => 1);
}


// Without any command, it should be the check command
if ('' == $command) {
    $command = "check";
    $command_array[] = array('command'   => $command,
                             'param_pos' => 1);
}


// If an environment variable is defined, we use it
$env_folder_path = getenv('MULTIOTP_PATH');
if (($env_folder_path !== false) && ($env_folder_path != '')) {
    $folder_path = $env_folder_path;
    $chdir_result = chdir($folder_path);
}


// If a base directory is given as a parameter, we use it in priority
if ('' != $base_dir) {
    $folder_path = $base_dir;
    $chdir_result = chdir($folder_path);
}


// Create a new Multiotp object
// The log and users subfolders are set by default under the folder of the script
// We set directly a specific encryption key for the config, tokens and users files
// PLEASE DO NOT CHANGE THIS LINE IF YOU DON'T KNOW WHAT YOU DO!
// IF YOU CHANGE THE ENCRYPTION KEY, YOUR PREVIOUS ENCRYPTED DATA WILL NOT BE READABLE ANYMORE

$multiotp_etc_dir = '/etc/multiotp';
$config_folder = $multiotp_etc_dir.'/config';
if (false === mb_strpos(getcwd(), '/')) {
  // if (!@file_exists($config_folder)) {
  $multiotp_etc_dir  = '';
  $config_folder = '';
}

if (($command == "libhash") || ($command == "help") || ($command == "version") || ($command == "php-version")) {
  if (!isset($multiotp)) {
    $multiotp = new Multiotp('DefaultCliEncryptionKey', false, $folder_path, $config_folder);
    $multiotp->SetCredentialProviderMode($cp_mode);
    $multiotp->SetCliMode($cli_mode);
    $multiotp->SetCliProxyMode(!$cli_mode); // The CLI proxy mode is *NOT* the CLI mode
  }
} else {
  if (!isset($multiotp)) {
    $multiotp = new Multiotp('DefaultCliEncryptionKey', $initialize_backend, $folder_path, $config_folder);
    $multiotp->SetCredentialProviderMode($cp_mode);
    $multiotp->SetCliMode($cli_mode);
    $multiotp->SetCliProxyMode(!$cli_mode); // The CLI proxy mode is *NOT* the CLI mode
    if ('' != $multiotp_etc_dir) {
      $multiotp->SetLogFolder('/var/log/multiotp/');
      $multiotp->SetConfigFolder($multiotp_etc_dir.'/config/');
      $multiotp->SetDDnsFolder($multiotp_etc_dir.'/ddns/');
      $multiotp->SetDevicesFolder($multiotp_etc_dir.'/devices/');
      $multiotp->SetGroupsFolder($multiotp_etc_dir.'/groups/');
      $multiotp->SetTokensFolder($multiotp_etc_dir.'/tokens/');
      $multiotp->SetUsersFolder($multiotp_etc_dir.'/users/');
      $multiotp->SetCacheFolder('/tmp/cache/');
      $multiotp->SetLinuxFileMode('0666');
    }
    $multiotp->ReadConfigData();
  }
  
  $multiotp->UpgradeSchemaIfNeeded();
  $verbose_prefix = $multiotp->GetVerboseLogPrefix(); // for example Reply-Message := 
}

// Initialize multiOTP direct Credential Provider options
if ('' != $server_cache_level) {
    if ($multiotp->GetServerCacheLevel() != intval($server_cache_level)) {
        $multiotp->SetServerCacheLevel(intval($server_cache_level));
        $write_param_data = true;
        if (($multiotp->IsDeveloperMode())) {
          $multiotp->WriteLog('Developer: new server_cache_level='.$server_cache_level, false, false, 8888, 'Debug', '');
        }
    }
}
if ('' != $server_secret) {
    if ($multiotp->GetServerSecret() != $server_secret) {
        $multiotp->SetServerSecret($server_secret);
        $write_param_data = true;
        if (($multiotp->IsDeveloperMode())) {
          $multiotp->WriteLog('Developer: new server_secret='.$server_secret, false, false, 8888, 'Debug', '');
        }
    }
}
if ('' != $server_timeout) {
    if ($multiotp->GetServerTimeout() != intval($server_timeout)) {
        $multiotp->SetServerTimeout(intval($server_timeout));
        $write_param_data = true;
        if (($multiotp->IsDeveloperMode())) {
          $multiotp->WriteLog('Developer: new server_timeout='.$server_timeout, false, false, 8888, 'Debug', '');
        }
    }
}
if ('' != $server_url) {
    if ($multiotp->GetServerUrl() != $server_url) {
        $multiotp->SetServerUrl($server_url);
        $write_param_data = true;
        if (($multiotp->IsDeveloperMode())) {
          $multiotp->WriteLog('Developer: new server_url='.$server_url, false, false, 8888, 'Debug', '');
        }
    }
}
if ('' != $sync_delete_retention_days) {
    if ($multiotp->GetSyncDeleteRetentionDays() != intval($sync_delete_retention_days)) {
        $multiotp->SetSyncDeleteRetentionDays(intval($sync_delete_retention_days));
        $write_param_data = true;
        if (($multiotp->IsDeveloperMode())) {
          $multiotp->WriteLog('Developer: new sync_delete_retention_days='.$sync_delete_retention_days, false, false, 8888, 'Debug', '');
        }
    }
}
if ($write_param_data) {
    $write_result = $multiotp->WriteConfigData(array(), true);
    if (($multiotp->IsDeveloperMode())) {
        if ($write_result) {
            $multiotp->WriteLog('Developer: new configuration automatically written', false, false, 8888, 'Debug', '');
        } else {
            $multiotp->WriteLog('Developer: error during new configuration writing operation', false, false, 8888, 'Debug', '');
        }
    }
}
if (($multiotp->IsDeveloperMode())) {
    $multiotp->WriteLog('Developer: argv: '.print_r($argv, true), false, false, 8888, 'Debug', '');
}


// Initialize multiOTP options
if ($enable_log) {
    $multiotp->EnableLog();
}
if ($verbose_log) {
    $multiotp->EnableVerboseLog();
}
if ($display_log) {
    $multiotp->EnableDisplayLog();
}
if ($keep_local) {
    $multiotp->EnableKeepLocal();
}


$prefix_pin = $multiotp->IsDefaultRequestPrefixPin();
if (isset($set_prefix_pin)) {
    $prefix_pin = $set_prefix_pin;
}

$multiotp->SetSourceTag($source_tag);
$multiotp->SetSourceIp($source_ip);
$multiotp->SetSourceMac($source_mac);
$multiotp->SetCallingIp($calling_ip);
$multiotp->SetCallingMac($calling_mac);
$multiotp->SetChapId($chap_id);
$multiotp->SetChapChallenge($chap_challenge);
$multiotp->SetChapPassword($chap_password);
$multiotp->SetMsChapChallenge($ms_chap_challenge);
$multiotp->SetMsChapResponse($ms_chap_response);
$multiotp->SetMsChap2Response($ms_chap2_response);
$multiotp->SetState($state);

if (($multiotp->IsDeveloperMode())) {
  $loop_start = 1;
  $temp_radius = '';
  for ($arg_loop=$loop_start; $arg_loop < $argc; $arg_loop++)
  {
    $one_radius = encode_utf8_if_needed(clean_quotes($argv[$arg_loop]));
    if (false !== mb_strpos($one_radius,' ')) {
      $one_radius = '"'.$one_radius.'"';
    }
    $temp_radius.= '{'.$one_radius.'} ';
  }
  $multiotp->WriteLog('Developer: *parameter(s) received, displayed between {}: '.trim($temp_radius), false, false, 8888, 'Debug', '');
}


// This is to be able to loop for various commands (since 5.0.3.4)
$full_args = $all_args;

for ($every_command = 0; $every_command < count($command_array); $every_command++) {
    $command   = $command_array[$every_command]['command'];
    $param_pos = $command_array[$every_command]['param_pos'];
    if (($every_command + 1) < (count($command_array))) {
        $param_count = $command_array[$every_command + 1]['param_pos'] - $param_pos;
    } else {
        $param_count = 1 + count($full_args) - $param_pos;
    }
    for ($i = 1; $i <= $param_count; $i++) {
        $all_args[$i] = $full_args[$param_pos + $i - 1];
    }
    for ($i = ($param_count + 1); $i <= $all_args_size; $i++) {
        $all_args[$i] = '';
    }

    switch ($command) {
        case "mysql":
            if  ($param_count < 1) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                $mysql_parameters = explode(",",mb_strtolower($all_args[1],'UTF-8'));
                if (count($mysql_parameters) < 4) {
                    $result = 30; // ERROR: At least one parameter is missing
                } else {
                    $mysql_parameters = array_pad($mysql_parameters, 7, NULL);

                    // Backend storage type
                    $multiotp->SetBackendType('mysql');

                    $multiotp->SetSqlServer($mysql_parameters[0]);
                    $multiotp->SetSqlUsername($mysql_parameters[1]);
                    $multiotp->SetSqlPassword($mysql_parameters[2]);
                    $multiotp->SetSqlDatabase($mysql_parameters[3]);
                    
                    // If table names are not defined, we keep the default value defined in the class constructor.
                    if (NULL !== $mysql_parameters[4]) {
                        $multiotp->SetSqlTableName('log', $mysql_parameters[4]);
                    }
                    if (NULL !== $mysql_parameters[5]) {
                        $multiotp->SetSqlTableName('users', $mysql_parameters[5]);
                    }
                    if (NULL !== $mysql_parameters[6]) {
                        $multiotp->SetSqlTableName('tokens', $mysql_parameters[6]);
                    }
                }
            }
            break;
        case "pgsql":
            if  ($param_count < 1) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                $pgsql_parameters = explode(",",mb_strtolower($all_args[1],'UTF-8'));
                if (count($pgsql_parameters) < 5) {
                    $result = 30; // ERROR: At least one parameter is missing
                } else {
                    $pgsql_parameters = array_pad($pgsql_parameters, 8, NULL);

                    // Backend storage type
                    $multiotp->SetBackendType('pgsql');

                    $multiotp->SetSqlServer($pgsql_parameters[0]);
                    $multiotp->SetSqlUsername($pgsql_parameters[1]);
                    $multiotp->SetSqlPassword($pgsql_parameters[2]);
                    $multiotp->SetSqlDatabase($pgsql_parameters[3]);
                    $multiotp->SetSqlSchema($pgsql_parameters[4]);
                    
                    // If table names are not defined, we keep the default value defined in the class constructor.
                    if (NULL !== $pgsql_parameters[5]) {
                        $multiotp->SetSqlTableName('log', $pgsql_parameters[5]);
                    }
                    if (NULL !== $pgsql_parameters[6]) {
                        $multiotp->SetSqlTableName('users', $pgsql_parameters[6]);
                    }
                    if (NULL !== $pgsql_parameters[7]) {
                        $multiotp->SetSqlTableName('tokens', $pgsql_parameters[7]);
                    }
                }
            }
            break;
        case "version":
            $version_info = $multiotp->GetClassName()." ".$multiotp->GetVersion()." (".$multiotp->GetDate().")";
            if ($multiotp->GetCliProxyMode()) {
                $version_info.= " [CLI PROXY]";
            } elseif ($multiotp->GetCliMode()) {
                $version_info.= " [CLI]";
            }
            if ($multiotp->GetCredentialProviderMode()) {
                $version_info.= " [CP]";
            }
            $version_info.= $crlf;
            echo $version_info;
            $result = 19;
            break;
        case "version-only":
            echo $multiotp->GetVersion();
            $result = 19;
            break;
        case "php-version":
            echo 'PHP '.phpversion().$crlf;
            $result = 19;
            break;
        case "backup-config":
            if  (0 == $param_count) {
                // Backward compatibility
                $multiotp->BackupConfiguration();
                $result = 19; // INFO: Requested operation successfully done
            } elseif  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                $backup_file = ('' != trim($all_args[2])) ? $all_args[2] : 'multiotp.cfg';
                if (TRUE === ($multiotp->BackupConfiguration(array('backup_file'      => $backup_file,
                                                                   'encryption_key'   => $all_args[1],
                                                                   'flush_attributes' => array('admin_password_hash'))))) {
                  $result = 19; // INFO: Requested operation successfully done
                } else {
                  $result = 99; // ERROR
                }
            }
            break;
        case "restore-config":
            if  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                $backup_file = ('' != trim($all_args[2])) ? $all_args[2] : 'multiotp.cfg';
                if (file_exists($backup_file)) {
                    if (TRUE === ($multiotp->RestoreConfiguration(array('backup_file' => $backup_file,
                                                                        'restore_key' => $all_args[1])))) {
                      $result = 19; // INFO: Requested operation successfully done
                    } else {
                      $result = 99; // ERROR
                    }
                } else {
                  $result = 58; // ERROR: File is missing
                }
            }
            break;
        case "call-method";
            if (method_exists($multiotp, $call_method)) {
                if ('' != $all_args[4]) {
                  $call_result = $multiotp->$call_method($all_args[1], $all_args[2], $all_args[3], $all_args[4]);
                } elseif ('' != $all_args[3]) {
                  $call_result = $multiotp->$call_method($all_args[1], $all_args[2], $all_args[3]);
                } elseif ('' != $all_args[2]) {
                  $call_result = $multiotp->$call_method($all_args[1], $all_args[2]);
                } elseif ('' != $all_args[1]) {
                  $call_result = $multiotp->$call_method($all_args[1]);
                } else {
                  $call_result = $multiotp->$call_method();
                }
                if ($multiotp->GetVerboseFlag()) {
                    $multiotp->WriteLog('Debug: *Method '.$call_method.' returned the following result: '.print_r($call_result, true), false, false, 8888, 'Debug', '');
                }
                $result = 19;
            } else {
                if ($multiotp->GetVerboseFlag()) {
                    $multiotp->WriteLog("Debug: *Method $call_method doesn't exist", false, false, 8888, 'Debug', '');
                }
                $result = 99;
            }
            break;
        case "check";
            $check_result = false;
            $self_registration = '';
            $otp_inline = '';
            if  ($param_count > 1) {
                // If the exact given user is not found, we try some different stages
                // (check also the check command)
                if (!$multiotp->CheckUserExists($all_args[1])) {
                    $check_result = false;
                    if (false !== mb_strpos($all_args[1], ':')) {
                        /*************************************************************************
                         * Here we check special cases
                         *
                         * 1) serial_number:username (for alternate self-registration process)
                         *    Do not forget to activate self-registration !
                         *
                         * 2) username:OTP (for alternate authentication with OTP and AD password)
                         *    For example in order to do MS-CHAPv2 authentication
                         *
                         *************************************************************************/
                        $part1 = mb_substr($all_args[1], 0, mb_strpos($all_args[1], ':'));
                        $part2 = mb_substr($all_args[1], mb_strpos($all_args[1], ':')+1);
                        if ($multiotp->IsSelfRegistrationEnabled() && ($multiotp->CheckTokenExists($part1))) {
                            $self_registration = $part1;
                            $all_args[1] = $part2;
                        } elseif ($multiotp->IsUserRequestLdapPasswordEnabled() && ($multiotp->CheckUserExists($part1))) {
                            $all_args[1] = $part1;
                            $otp_inline = $part2;
                        }
                    }
                    

                    /// Return a real username if the initial one is not existing
                    $find_user = $multiotp->FindRealUserName($all_args[1], TRUE);
                    if ($find_user != $all_args[1]) {
                      $all_args[1] = $find_user;
                      $multiotp->SetUser($all_args[1]);
                    }
                } else {
                    $check_result = true;
                }
                # check extension can be added here
            }
            if (($param_count < 2) && (!$encrypted_password)) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                if ("ERROR" == $multiotp->GetUserEncryptionHash()) {
                    $result = 33; // ERROR: Encryption hash error, encryption key is not the same
                } else {
                    $result = 21; // ERROR: user doesn't exist.
                }
            } else {
                // Resynchronization information splitting (for autoresync) is now handled in CheckToken directly
                if ('' != $all_args[3]) {
                    for ($i = 3; $i <= $all_args_size; $i++) {
                        if ('' != $all_args[$i]) {
                            $all_args[2] = $all_args[2]." ".$all_args[$i];
                        }
                    }
                }
                $result = $multiotp->CheckToken($all_args[2], '', false, false, false, false, $self_registration); // Result provided by the MultiOTP class
                if (($multiotp->IsAutoResync()) && (14 == $result)) {
                    $result = 0;
                }
            }
            break;
        case "checkpam":
            if (!$multiotp->ReadUserData(isset($_ENV["PAM_USER"])?$_ENV["PAM_USER"]:'PAM_USER_NOT_DEFINED!')) {
                if ("ERROR" == $multiotp->GetUserEncryptionHash()) {
                    $result = 33; // ERROR: Encryption hash error, encryption key is not the same
                } else {
                    $result = 21; // ERROR: user doesn't exist.
                }
            } else {
                $result = $multiotp->CheckToken(isset($_ENV["PAM_AUTHTOK"])?$_ENV["PAM_AUTHTOK"]:'PAM_AUTHTOK_NOT_DEFINED!');
            }
            break;
        case "create":
        case "update":
            if (("create" == $command) && $multiotp->ReadUserData($all_args[1], true, true)) {
                $result = 22; // ERROR: user already exists.
            } elseif (("update" == $command) && (!$multiotp->ReadUserData($all_args[1], false, true))) {
                $result = 21; // ERROR: user doesn't exist.
            } elseif  ($param_count < 3) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                $multiotp->SetUser($all_args[1]);
                $multiotp->SetUserPrefixPin($prefix_pin?1:0);
                
                if ($token_id_creation) {
                    $key_id = $all_args[2];
                    if (!$multiotp->ReadTokenData($key_id)) {
                        $result = 29; // ERROR: token doesn't exist.
                    } else {
                        $multiotp->SetUserKeyId($key_id);
                        $multiotp->SetUserTokenSerialNumber($multiotp->GetTokenSerialNumber());
                        if (!$multiotp->SetUserAlgorithm($multiotp->GetTokenAlgorithm())) {
                            $result = 23; // ERROR: invalid algorithm
                        } else {
                            $multiotp->SetUserTokenSeed($multiotp->GetTokenSeed());
                            $multiotp->SetUserTokenNumberOfDigits($multiotp->GetTokenNumberOfDigits());
                            $multiotp->SetUserTokenTimeInterval($multiotp->GetTokenTimeInterval());
                            $multiotp->SetUserTokenLastEvent($multiotp->GetTokenLastEvent());
                            $multiotp->SetUserTokenAlgoSuite($multiotp->GetTokenAlgoSuite());
                            
                            $multiotp->SetUserPin($all_args[3]);
                            
                            if ($multiotp->WriteUserData()) {
                                $result = 11; // INFO: user successfully created or updated
                            } else {
                                $result = 28; // ERROR: Unable to write the changes in the file
                            }
                        }
                    }
                }
                elseif (!$multiotp->SetUserAlgorithm($all_args[2])) {
                    $result = 23; // ERROR: invalid algorithm
                } else {
                    $multiotp->SetUserTokenSeed($all_args[3]);
                    
                    if  ($param_count < 4) {
                        $result = 30; // ERROR: At least one parameter is missing
                    } else {
                        $multiotp->SetUserPin($all_args[4]);
                        if ('' == $all_args[5]) {
                            $all_args[5] = 6; // Default number of digits is set to 6
                        }
                        $multiotp->SetUserTokenNumberOfDigits($all_args[5]);
                        switch (mb_strtoupper($all_args[2],'UTF-8'))
                        {
                            // This is the time interval for mOTP
                            case "MOTP":
                                if ('' == $all_args[6]) {
                                    $all_args[6] = 10; // Default windows value interval for mOTP
                                }
                                $multiotp->SetUserTokenTimeInterval($all_args[6]);
                                break;
                            // This is the time interval for TOTP
                            case "TOTP":
                                if ('' == $all_args[6]) {
                                    $all_args[6] = 30; // Default windows value interval for TOTP
                                }
                                $multiotp->SetUserTokenTimeInterval($all_args[6]);
                                break;
                            // This is the next event for HOTP
                            case "HOTP":
                            default:
                                if ('' == $all_args[6]) {
                                    $all_args[6] = 0; // Default next event
                                }
                                $multiotp->SetUserTokenLastEvent($all_args[6]-1);
                                // -1 because we are saving the last event in the user file database
                                break;
                        }
                        if ($multiotp->WriteUserData()) {
                            $result = 11; // INFO: user successfully created or updated
                        } else {
                            $result = 28; // ERROR: Unable to write the changes in the file
                        }
                    }
                }
            }
            break;
        case "delete":
            if (!$multiotp->DeleteUser($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $result = 19; // INFO: user successfully deleted.
            }
            break;
        case "delete-token":
            if (!$multiotp->DeleteToken($all_args[1])) {
                $result = 36; // ERROR: token doesn't exist.
            } else {
                $result = 19; // INFO: token successfully deleted.
            }
            break;
        case "lock":
            if (!$multiotp->LockUser($all_args[1])) { // Write is done directly
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $result = 19; // OK
            }
            break;
        case "unlock":
            if (!$multiotp->UnlockUser($all_args[1])) { // Write is done directly
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $result = 19; // OK
            }
            break;
        case "callapi":
            $api_result = $multiotp->CallApi(array("script_uri" => $all_args[1],
                                                   "secret"     => $all_args[2]));
            $result = (FALSE !== mb_strpos($api_result, 'result_code')) ? 19 : 99;
            echo $api_result;
            break;
        case "assign-token":
            if (!$multiotp->AssignTokenToUser($all_args[1],
                                              $all_args[2])) {
                $result = 99; // ERROR
            } else {
                $result = 19; // OK
            }
            break;
        case "remove-token":
            if  ($param_count < 1) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                if ($multiotp->RemoveTokenFromUser($all_args[1])) {
                    $result = 19; // OK
                } else {
                    $result = 99; // ERROR
                }
            }
            break;
        case "default-dialin-ip-mask":
            if (!$multiotp->SetDefaultDialinIpMask($all_args[1])) {
                $result = 99; // ERROR
            } elseif ($multiotp->WriteConfigData()) {
                $result = 19; // OK
            } else {
                $result = 99; // ERROR
            }
            break;
        case "dialin-ip-address":
            if  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $multiotp->SetUserDialinIpAddress($all_args[1], $all_args[2]);
                if ($multiotp->WriteUserData()) {
                    $result = 19; // OK
                } else {
                    $result = 99; // ERROR
                }
            }
            break;
        case "dialin-ip-mask":
            if  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $multiotp->SetUserDialinIpMask($all_args[1], $all_args[2]);
                if ($multiotp->WriteUserData()) {
                    $result = 19; // OK
                } else {
                    $result = 99; // ERROR
                }
            }
            break;
        case "activate":
            if (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $multiotp->SetUserActivated($all_args[1],1);
                if ($multiotp->WriteUserData()) {
                    $result = 19; // OK
                } else {
                    $result = 99; // ERROR
                }
            }
            break;
        case "deactivate":
        case "desactivate":
            if (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $multiotp->SetUserActivated($all_args[1],0);
                if ($multiotp->WriteUserData()) {
                    $result = 19; // OK
                } else {
                    $result = 99; // ERROR
                }
            }
            break;
        case "requiresms":
            if (!$multiotp->CheckUserExists($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $result = $multiotp->GenerateSmsToken($all_args[1]); // It writes automatically in the database
            }
            break;
        case "resync":
            if  ($param_count < 3) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                if ($multiotp->ResyncToken($all_args[2], $all_args[3], $display_status)) {
                    $result = 14; // INFO: token is now synchronized
                }
            }
            break;
        case "seed":
            if  ($param_count < 3) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $result1 = $multiotp->CheckToken($all_args[2]);
                $result2 = $multiotp->CheckToken($all_args[3]);
                if ($result1 && $result2) {
                    $result = 19;
                } else {
                    $result = 99;
                }
            }
            break;
        case "update-pin":
            if  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $multiotp->SetUserPin($all_args[2]);
                if ($multiotp->WriteUserData()) {
                    $result = 13; // INFO: pin successfully changed
                }
            }
            break;
        case "user-info":
            $result_txt = $multiotp->GetUserInfo($all_args[1]);
            if ("" != $result_txt) {
              echo $result_txt;
              $result = 19;
            } else {
              $result = 99;
            }
            break;
        case "set":
            $write_user_data = false;
            if  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                for ($params = 2; $params < count($all_args); $params++) {
                    $actual_array = explode("=",$all_args[$params],2);
                    if (2 == count($actual_array)) {
                        switch ($actual_array[0]) {
                            case 'cache-level':
                                $multiotp->SetUserCacheLevel(intval($actual_array[1]));
                                $write_user_data = true;
                                break;
                            case 'cache-lifetime':
                                $multiotp->SetUserCacheLifetime(intval($actual_array[1]));
                                $write_user_data = true;
                                break;
                            case 'description':
                                $multiotp->SetUserDescription($actual_array[1]);
                                $write_user_data = true;
                                break;
                            case 'email':
                                $multiotp->SetUserEmail($actual_array[1]);
                                $write_user_data = true;
                                break;
                            case 'pin':
                                $multiotp->SetUserPin($actual_array[1]);
                                $write_user_data = true;
                                break;
                            case 'ldap-pwd':
                                $multiotp->SetUserRequestLdapPassword(intval($actual_array[1]));
                                $write_user_data = true;
                                break;
                            case 'prefix-pin':
                                $multiotp->SetUserPrefixPin(intval($actual_array[1]));
                                $write_user_data = true;
                                break;
                            case 'sms':
                                $multiotp->SetUserSms($actual_array[1]);
                                $write_user_data = true;
                                break;
                            default: // Just in case we need to change additional values that have no related method
                                $internal_user_option = str_replace("-", "_", $actual_array[0]);
                                if ($multiotp->SetUserAttribute($internal_user_option, $actual_array[1]))
                                {
                                    $write_user_data = true;
                                }
                                break;
                        }
                    }
                }
                if ($write_user_data) {
                    if ($multiotp->WriteUserData()) {
                        $result = 19; // INFO: Requested operation successfully done
                    }
                }
            }
            break;
        case "config":
            $config_result = true;
            $write_config_data = false;
            if  ($param_count < 1)
            {
                $result = 30; // ERROR: At least one parameter is missing
            }
            else
            {
                for ($params = 1; $params < count($all_args); $params++)
                {
                    $actual_array = explode("=",$all_args[$params],2);
                    if (2 == count($actual_array))
                    {
                        switch ($actual_array[0])
                        {
                            case 'attributes-to-encrypt':
                                $multiotp->SetAttributesToEncrypt($actual_array[1]);
                                $internal_config_option = str_replace("-", "_", $actual_array[0]);
                                if ($multiotp->SetConfigAttribute($internal_config_option, $actual_array[1]))
                                {
                                    $write_config_data = true;
                                }
                                break;
                            case 'autoresync':
                                $multiotp->SetAutoResync($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'backend-type':
                                $multiotp->SetBackendType($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'clear-otp-attribute':
                                $multiotp->SetClearOtpAttribute($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'debug':
                                $multiotp->SetDebugOption(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'display-log':
                                $multiotp->SetDisplayLogOption(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'debug-prefix':
                                $multiotp->SetVerboseLogPrefix($actual_array[1]);
                                $verbose_prefix = $multiotp->GetVerboseLogPrefix();
                                $write_config_data = true;
                                break;
                            case 'default-request-prefix-pin':
                                $multiotp->SetDefaultRequestPrefixPin(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'default-request-ldap-pwd':
                                $multiotp->SetDefaultRequestLdapPwd(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'group-attribute':
                                $multiotp->SetGroupAttribute($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'issuer':
                                $multiotp->SetIssuer($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-account-suffix':
                                $multiotp->SetLdapAccountSuffix($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-activated':
                                $multiotp->SetLdapActivated($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-base-dn':
                                $multiotp->SetLdapBaseDn($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-bind-dn':
                                $multiotp->SetLdapBindDn($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-cn-identifier':
                                $multiotp->SetLdapCnIdentifier($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-default-algorithm':
                                $multiotp->SetLdapDefaultAlgorithm($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-domain-controllers':
                                $multiotp->SetLdapDomainControllers($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-group-attribute':
                                $multiotp->SetLdapGroupAttribute($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-group-cn-identifier':
                                $multiotp->SetLdapGroupCnIdentifier($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-in-group':
                                $multiotp->SetLdapInGroup($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-network-timeout':
                                $multiotp->SetLdapNetworkTimeout(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'ldap-port':
                                $multiotp->SetLdapPort(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'ldap-server-password':
                                $multiotp->SetLdapServerPassword($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-server-type':
                                $multiotp->SetLdapServerType(intval($actual_array[1]), true);
                                $write_config_data = true;
                                break;
                            case 'ldap-ssl':
                                $multiotp->SetLdapSsl($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-synced-user-attribute':
                                $multiotp->SetLdapSyncedUserAttribute($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'ldap-time-limit':
                                $multiotp->SetLdapTimeLimit(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'ldap-users-dn':
                                $multiotp->SetLdapUsersDn($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'log':
                                $multiotp->SetLogOption(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'multiple-groups':
                                $multiotp->SetMultipleGroups(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'radius-reply-attributor':
                                $multiotp->SetRadiusReplyAttributor($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'radius-reply-separator':
                                $multiotp->SetRadiusReplySeparator($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'self-registration':
                                $multiotp->SetSelfRegistration(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'server-cache-level':
                                $multiotp->SetServerCacheLevel(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'server-cache-lifetime':
                                $multiotp->SetServerCacheLifetime(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'server-secret':
                                $multiotp->SetServerSecret($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'server-timeout':
                                $multiotp->SetServerTimeout(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'server-type':
                                $multiotp->SetServerType($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'server-url':
                                $multiotp->SetServerUrl($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-api-id':
                                $multiotp->SetSmsApiId($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-message':
                                $multiotp->SetSmsMessage($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-originator':
                                $multiotp->SetSmsOriginator($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-password':
                                $multiotp->SetSmsPassword($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-provider':
                                $multiotp->SetSmsProvider($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-userkey':
                            case 'sms-username':
                                $multiotp->SetSmsUsername($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-url':
                                $multiotp->SetSmsUrl($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-ip':
                                $multiotp->SetSmsIp($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-port':
                                $multiotp->SetSmsPort($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-send-template':
                                $multiotp->SetSmsSendTemplate($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-method':
                                $multiotp->SetSmsMethod($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-encoding':
                                $multiotp->SetSmsEncoding($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-status-success':
                                $multiotp->SetSmsStatusSuccess($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-content-success':
                                $multiotp->SetSmsContentSuccess($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-no-double-zero':
                                $multiotp->SetSmsNoDoubleZero($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-basic-auth':
                                $multiotp->SetSmsBasicAuth($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sms-content-encoding':
                                $multiotp->SetSmsContentEncoding($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-server':
                                $multiotp->SetSqlServer($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-username':
                                $multiotp->SetSqlUsername($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-password':
                                $multiotp->SetSqlPassword($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-database':
                                $multiotp->SetSqlDatabase($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-config-table':
                                $multiotp->SetSqlTableName('config',$actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-devices-table':
                                $multiotp->SetSqlTableName('devices',$actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-log-table':
                                $multiotp->SetSqlTableName('log',$actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-tokens-table':
                                $multiotp->SetSqlTableName('tokens',$actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sql-users-table':
                                $multiotp->SetSqlTableName('users',$actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'sync-delete-retention-days':
                                $multiotp->SetSyncDeleteRetentionDays(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'tel-default-country-code':
                                $multiotp->SetTelDefaultCountryCode($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'token-serial-number-length':
                                $multiotp->SetTokenSerialNumberLength($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'challenge-response-enabled':
                                $multiotp->SetGlobalChallengeResponse(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'sms-challenge-enabled':
                                $multiotp->SetGlobalSmsChallenge(intval($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'text-sms-challenge':
                                $multiotp->SetGlobalTextSmsChallenge(trim($actual_array[1]));
                                $write_config_data = true;
                                break;
                            case 'text-token-challenge':
                                $multiotp->SetGlobalTextTokenChallenge(trim($actual_array[1]));
                                $write_config_data = true;
                                break;
                            default: // Just in case we need to change additional values that have no related method
                                $internal_config_option = str_replace("-", "_", $actual_array[0]);
                                if ($multiotp->SetConfigAttribute($internal_config_option, $actual_array[1]))
                                {
                                    $write_config_data = true;
                                }
                                break;
                        }
                    }
                }
                if ($write_config_data) {
                    if ($multiotp->WriteConfigData(array(), true)) {
                        $result = 19; // INFO: Requested operation successfully done
                    }
                }
            }
            break;
        case "import":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                $import_password = $all_args[2];
                if (('' != $import_password) && (@file_exists($import_password))) {
                    $import_password = @file_get_contents($import_password);
                }
                if ($multiotp->ImportTokensFile($all_args[1], $all_args[1], $import_password)) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-csv":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportTokensFromCsv($all_args[1])) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-pskc":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                $import_password = $all_args[2];
                if (('' != $import_password) && (@file_exists($import_password))) {
                    $import_password = @file_get_contents($import_password);
                }
                if ($multiotp->ImportTokensFromPskc($all_args[1], $import_password)) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-yubikey":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportYubikeyTraditional($all_args[1])) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-xml":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportTokensFromXml($all_args[1])) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-alpine-xml":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportTokensFromAlpineXml($all_args[1])) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-dat":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportTokensFromAlpineDat($all_args[1])) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "import-sql":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportTokensFromAuthenexSql($all_args[1])) {
                    $result = 15; // INFO: Tokens definition file successfully imported
                } else {
                    $result = 32; // ERROR: Tokens definition file not successfully imported.
                }
            }
            break;
        case "iswithout2fa":
            if  ($param_count < 1) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                // If the exact given user is not found, we try some different stages
                // (check also the iswithout2fa command)
                if (!$multiotp->CheckUserExists($all_args[1])) {
                    $check_result = false;
                    if (false !== mb_strpos($all_args[1], ':')) {
                        /*************************************************************************
                         * Here we check special cases
                         *
                         * 1) serial_number:username (for alternate self-registration process)
                         *    Do not forget to activate self-registration !
                         *
                         * 2) username:OTP (for alternate authentication with OTP and AD password)
                         *    For example in order to do MS-CHAPv2 authentication
                         *
                         *************************************************************************/
                        $part1 = mb_substr($all_args[1], 0, mb_strpos($all_args[1], ':'));
                        $part2 = mb_substr($all_args[1], mb_strpos($all_args[1], ':')+1);
                        if ($multiotp->IsSelfRegistrationEnabled() && ($multiotp->CheckTokenExists($part1))) {
                            $self_registration = $part1;
                            $all_args[1] = $part2;
                        } elseif ($multiotp->IsUserRequestLdapPasswordEnabled() && ($multiotp->CheckUserExists($part1))) {
                            $all_args[1] = $part1;
                            $otp_inline = $part2;
                        }
                    }
                    

                    /// Return a real username if the initial one is not existing
                    $find_user = $multiotp->FindRealUserName($all_args[1], TRUE);
                    if ($find_user != $all_args[1]) {
                      $all_args[1] = $find_user;
                      $multiotp->SetUser($all_args[1]);
                    }
                } else {
                    $check_result = true;
                }
                # check extension can be added here
            }
            if (!$multiotp->ReadUserData($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                $result = 7; // INFO: User requires a token
                if ("without2fa" == mb_strtolower($multiotp->GetUserAlgorithm(),'UTF-8')) {
                    if (($multiotp->GetUserAutolockTime() > 0) && ($multiotp->GetUserAutolockTime() < time())) {
                        $multiotp->DeleteUser("", TRUE);
                        $this->WriteLog("Error: cache too old for user ".$real_user.", cache deleted.", FALSE, FALSE, $result, 'User', $real_user);
                        $result = 81; // ERROR: Cache too old for this user
                    } elseif (1 != $multiotp->GetUserActivated()) {
                      $multiotp->DeleteUser("", TRUE);
                      $result = 38; // ERROR: User is desactivated
                    } else {
                      $multiotp->WriteUserData(); // We cache the user locally
                      $result = 8; // INFO: User can be authenticated without a token (WITHOUT2FA)
                    }
                }
            }
            break;
        case "qrcode":
            if  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->CheckUserExists($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                if ($multiotp->GetUserTokenQrCode($all_args[1], '', $all_args[2])) {
                    $result = 16; // INFO: QRcode successfully created.
                } else {
                    $result = 50; // INFO: QRcode not created.
                }
            }
            break;
        case "urllink":
            if  ($param_count < 1) {
                $result = 30; // ERROR: At least one parameter is missing
            } elseif (!$multiotp->CheckUserExists($all_args[1])) {
                $result = 21; // ERROR: user doesn't exist.
            } else {
                if (false !== ($url_result = $multiotp->GetUserTokenUrlLink($all_args[1]))) {
                    echo $url_result.$crlf;
                    $result = 17; // INFO: UrlLink successfully created.
                } else {
                    $result = 51; // INFO: UrlLink not created.
                }
            }
            break;
        case "scratchlist":
            echo str_replace("\t",$crlf,$multiotp->GetUserScratchPasswordsList($all_args[1])).$crlf;
            $result = 19;
            break;
        case "userslist":
            echo str_replace("\t",$crlf,$multiotp->GetUsersList()).$crlf;
            $result = 19;
            break;
        case "lockeduserslist":
            echo str_replace("\t",$crlf,$multiotp->GetLockedUsersList()).$crlf;
            $result = 19;
            break;
        case "tokenslist":
            echo str_replace("\t",$crlf,$multiotp->GetTokensList()).$crlf;
            $result = 19;
            break;
        case "ldap-users-list":
            if ('' != $multiotp->_config_data['ldap_domain_controllers']) {
                $ldap_users_list = $multiotp->GetLdapUsersList();
                if ('' != $ldap_users_list) {
                    echo str_replace("\t",$crlf,$ldap_users_list).$crlf;
                    $result = 19;
                } else {
                    $result = 39;
                }
            } else {
                $result = 39;
            }
            break;
        case "ldap-user-info":
            $users_array = $multiotp->GetLdapUsersInfoArray($all_args[1], true, true);
            $user_separator = "";
            foreach ($users_array as $one_user_array) {
                echo $user_separator;
                $user_separator = $crlf;
                foreach ($one_user_array as $array_key => $array_value) {
                    if (is_array($array_value)) {
                        $info_value = "";
                        foreach ($array_value as $one_key => $one_value) {
                            $info_value.= (("" == $info_value) ? "" : ",").$one_value;
                        }
                    } else {
                        $info_value = $array_value;
                    }
                    echo mb_substr(str_repeat(" ", 23).$array_key, -23).": ".$info_value.$crlf;
                }
            }
            $result = 19;
            break;
        case "ldap-users-sync":
            // All users (*), include disabled, don't ignore in groups, display debug step if next argument > 0
            if ("" == $all_args[1]) {
                $all_args[1] = 60;
            }
            $result = (($multiotp->SyncLdapUsers("*", TRUE, FALSE, intval($all_args[1]))) ? 19 : 99);
            break;
        case "purge-ldap-cache-folder":
            $result = (($multiotp->PurgeLdapCacheFolder()) ? 19 : 99);
            break;
        case "purge-lock-folder":
            $result = (($multiotp->PurgeLockFolder()) ? 19 : 99);
            break;
        case "showlog":
            $multiotp->ShowLog();
            $result = 19;
            break;
        case "clearlog":
            $multiotp->ClearLog();
            $result = 19;
            break;
        case "ldap-check":
            $result = (($multiotp->CheckLdapAuthentication()) ? 19 : 99);
            break;
        case "check-ldap-password":
            $result = (($multiotp->CheckUserLdapPassword($all_args[1],$all_args[2])) ? 19 : 99);
            break;
        case "fastcreate":
        case "fastcreatenopin":
        case "fastcreatewithpin":
            if ($multiotp->CheckUserExists($all_args[1])) {
                $result = 22; // ERROR: user already exists.
            } elseif  ($param_count < 1) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                if ('fastcreatenopin' == $command) {
                    $prefix_pin = false;
                } elseif ('fastcreatewithpin' == $command) {
                    $prefix_pin = true;
                }
                if ($multiotp->CreateUser($all_args[1], $prefix_pin?1:0, "TOTP", '', (''!=$all_args[2])?$all_args[2]:'')) {
                    $result = 11; // INFO: user successfully created or updated
                } else {
                    $result = 35; // ERROR: user not created
                }
            }
            break;
        case "createga":
            if ($multiotp->ReadUserData($all_args[1], true)) {
                $result = 22; // ERROR: user already exists.
            } elseif  ($param_count < 2) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                if ($multiotp->CreateUser($all_args[1], 0, "TOTP", bin2hex(base32_decode($all_args[2])), (''!=$all_args[3])?$all_args[3]:'')) {
                    $result = 11; // INFO: user successfully created or updated
                } else {
                    $result = 35; // ERROR: user not created
                }
            }
            break;
        case "phpinfo":
            phpinfo();
            break;
        case "libhash":
            echo $multiotp->GetLibraryHash($all_args[1], $all_args[2]).$crlf;
            $result = 19;
            break;
        case "custominfo":
            echo $multiotp->GetCustomInfo().$crlf;
            $result = 19;
            break;
        case "network-info":
            echo implode($crlf, $multiotp->GetNetworkInfo());
            echo $crlf;
            $result = 19;
            break;
        case "noparam":
            $result = 30;
            echo $multiotp->GetClassName()." ".$multiotp->GetVersion()." (".$multiotp->GetDate().")";
            if (!$no_php_info) {
                if (PHP_MAJOR_VERSION > 4) {
                    echo ", running with PHP ".phpversion();
                }
                if ($multiotp->GetCliProxyMode()) {
                    echo " (CLI proxy mode)";
                } else {
                    echo " (CLI mode)";
                }
            }
            echo $crlf;
            echo $multiotp->GetCopyright().$crlf;
            echo $multiotp->GetWebsite()."   (you can try the [Donate] button ;-)".$crlf;
            echo $crlf;
            echo "Not enough parameters, type multiotp -help for information about the options.";
            echo $crlf;
            break;
        case "error":
            break;
        case "help":
        default:
            // Help or others, except the -initialize-backend option.
            if (!$initialize_backend) {
                $result = 999; // Info only
                echo $multiotp->GetClassName()." ".$multiotp->GetVersion()." (".$multiotp->GetDate().")";
                if (!$no_php_info) {
                    echo ", running with embedded PHP version ".phpversion();
                }
                echo $crlf;
                echo $multiotp->GetCopyright().$crlf;
                echo $multiotp->GetWebsite()."   (you can try the [Donate] button ;-)".$crlf;
                echo $crlf;
                if ($multiotp->GetVerboseFlag()) {
                    $script_folder = $multiotp->GetScriptFolder();
                    if (($detected_folder_path != '') && ($detected_folder_path != $script_folder)) {
                        echo "*Initial detected folder: ".$detected_folder_path.$crlf;
                    }
                    if ($base_dir != '') {
                        echo "*base_dir option folder: ".$folder_path.$crlf;
                    }
                    if (($env_folder_path !== false) && ($env_folder_path != '')) {
                        echo "*MULTIOTP_PATH variable folder: ".$env_folder_path.$crlf;
                    }
                    echo "*Script folder: ".$script_folder.$crlf;
                    echo $crlf;
                }
                echo "multiotp will check if the token of a user is correct, based on a specified".$crlf;
                echo "algorithm (currently Mobile-OTP (http://motp.sf.net), OATH/HOTP (RFC 4226) ".$crlf;
                echo "and OATH/TOTP (RFC 6238) are implemented). PSKC format supported (RFC 6030).".$crlf;
                echo "Supported encryption methods are PAP and CHAP.".$crlf;
                echo "Yubico OTP format supported (44 bytes long, with prefixed serial number).".$crlf;
                echo "SMS-code are supported (current providers: aspsms,clickatell,clickatell2,".$crlf;
                echo "                        intellisms,nexmo,nowsms,smseagle,swisscom,telnyx,".$crlf;
                echo "                        custom,exec).".$crlf;
                echo "Specific SMS sender program supported by specifying exec as SMS provider.".$crlf;
                echo $crlf;
                echo "Google Authenticator base32_seed tokens must be of n*8 characters.".$crlf;
                echo "Google Authenticator TOTP tokens must have a 30 seconds interval.".$crlf;
                echo "Available characters in base32 are only ABCDEFGHIJKLMNOPQRSTUVWXYZ234567".$crlf;
                echo $crlf;
                echo "To quickly create a user, use the -fastcreate option with the name of the user.".$crlf;
                echo "A quickly created user is compatible with Google Auth (30 seconds, 6 digits).".$crlf;
                echo "Depending on the prefix PIN option (WHICH IS ENABLED BY DEFAULT), a prefix PIN".$crlf;
                echo "will be requested or not before the displayed token.".$crlf;
                echo "If the PIN is not given, it is generated randomly.".$crlf;
                echo $crlf;
                echo "To quickly create a user without a prefix PIN request, use -fastcreatenopin".$crlf;
                echo $crlf;
                echo "To quickly create a user with a prefix PIN request, use -fastecreatewithpin".$crlf;
                echo $crlf;
                echo "If a token is locked (return code 24), you have to resync the token to unlock.".$crlf;
                echo "Requesting an SMS token (put sms as the password), and typing the received".$crlf;
                echo " token correctly will also unlock the token.".$crlf;
                if (!function_exists('ImageCreate')) {
                    echo $crlf;
                    echo "!!! You need to enable the gd2 library in order to create QRcode !!!".$crlf;
                }
                echo $crlf;
                echo "The check will return 0 for a correct token, and the other return code means:".$crlf;
                echo $crlf;
                echo "Return codes:".$crlf;
                echo $crlf;
                
                foreach ($multiotp->_errors_text as $key => $value) {
                    echo mb_substr("  ".$key, -2)." ".$value." ".$crlf;
                }
                echo $crlf;
                echo $crlf;
                echo "Usage:".$crlf;
                echo $crlf;
                echo " PLEASE NOTE THAT BY DEFAULT, A PREFIX PIN IS REQUIRED.".$crlf;
                echo $crlf;
                echo " multiotp user [prefix PIN]OTP (check the OTP (with prefix PIN) of the user)".$crlf;
                echo " multiotp -checkpam (to check with pam-script, using PAM_USER and PAM_AUTHTOK)".$crlf;
                echo $crlf;
                echo " multiotp -requiresms user (generate and send an SMS token to the user)".$crlf;
                echo " multiotp user sms (send an SMS token to the user)".$crlf;
                echo $crlf;
                echo " multiotp user [-chap-id=0x..] -chap-challenge=0x... -chap-password=0x...".$crlf;
                echo "   (the first byte of the chap-password value can contain the chap-id value)".$crlf;
                echo $crlf;
                echo " multiotp -fastcreate user [pin] (create a Google Auth compatible token)".$crlf;
                echo " multiotp -fastcreatenopin user [pin] (create a user without a prefix PIN)".$crlf;
                echo " multiotp -fastecreatewithpin user [pin] (create a user with a prefix PIN)".$crlf;
                echo " multiotp -createga user base32_seed [pin] (create Google Auth user with TOTP)".$crlf;
                echo " multiotp -create user algo seed pin digits [pos|interval]".$crlf;
                echo " multiotp -create -token-id user token-id pin".$crlf;
                echo $crlf;
                echo "  token-id: id of the previously imported token to attribute to the user".$crlf;
                echo "      user: name of the user (should be the account name)".$crlf;
                echo "      algo: available algorithms are mOTP, HOTP, TOTP, YubicoOTP and without2FA".$crlf;
                echo "      seed: hexadecimal or base32 seed of the token".$crlf;
                echo "       pin: private pin code of the user".$crlf;
                echo "    digits: number of digits given by the token".$crlf;
                echo "       pos: for HOTP algorithm, position of the next awaited event".$crlf;
                echo "  interval: for mOTP and TOTP algorithms, token interval time in seconds".$crlf;
                echo $crlf;
                echo " multiotp -import tokens_definition_file [key|pass|key_file]".$crlf;
                echo "   (auto-detect format)".$crlf;
                echo " multiotp -import-csv csv_tokens_file.csv (tokens definition in a file)".$crlf;
                echo "   (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)".$crlf;
                echo " multiotp -import-pskc pskc_tokens_file.pskc [key|pass|key_file]".$crlf;
                echo "   (PSKC format, RFC 6030)".$crlf;
                echo " multiotp -import-yubikey yubikey_traditional_format_log.csv (YubiKey)".$crlf;
                echo " multiotp -import-dat importAlpine.dat (SafeWord/Aladdin/SafeNet tokens)".$crlf;
                echo " multiotp -import-alpine-xml alpineXml.xml (SafeWord/Aladdin/SafeNet)".$crlf;
                echo " multiotp -import-xml xml_tokens_definition_file.xml (old Feitian)".$crlf;
                echo " multiotp -import-sql tokens_definition_file.sql (ZyXEL/Authenex)".$crlf;
                echo $crlf;
                echo " multiotp -iswithout2fa user (return 8 for WITHOUT2FA token, otherwise 7)".$crlf;
                echo $crlf;
                echo " multiotp -delete-token token".$crlf;
                echo $crlf;
                echo " multiotp -qrcode user png_file_name.png (only for TOTP and HOTP)".$crlf;
                echo " multiotp -urllink user (only for TOTP and HOTP, generate provisioning URL)".$crlf;
                echo $crlf;
                echo " multiotp -scratchlist user (generate & display scratch passwords for the user)".$crlf;
                echo $crlf;
                echo " multiotp -resync [-status] user token1 token2 (two consecutive tokens)".$crlf;
                echo " multiotp -update-pin user pin".$crlf;
                echo $crlf;
                echo " multiotp -assign-token user token-id (assign the token to the user)".$crlf;
                echo " multiotp -remove-token user (remove the token assigned to the user)".$crlf;
                echo $crlf;
                echo " multiotp -default-dialin-ip-mask (set the default dialin IP mask)".$crlf;
                echo " multiotp -dialin-ip-address user ip-address (set the user dialin IP address)".$crlf;
                echo " multiotp -dialin-ip-mask user ip-address (set the user dialin IP mask)".$crlf;
                echo $crlf;
                echo " multiotp -[des]activate user".$crlf;
                echo " multiotp -[un]lock user".$crlf;
                echo $crlf;
                echo " multiotp -delete user".$crlf;
                echo $crlf;
                echo " multiotp -user-info user".$crlf;
                echo $crlf;
                echo " multiotp -config option1=value1 option2=value2 ... optionN=valueN".$crlf;
                echo "  options are  ";
                echo                "  autoresync: [0|1] enable/disable autoresync during login".$crlf;
                echo "      attributes-to-encrypt: specific attributes list to encrypt, must be".$crlf;
                echo "                             surrounded by *, like '*token_seed*user_pin*'".$crlf;
                echo "               backend-type: backend storage type (files|mysql|pgsql)".$crlf;
                echo " challenge-response-enabled: [0|1] enable/disable Challenge-Response".$crlf;
                echo "        clear-otp-attribute: attribute to return for the clear OTP".$crlf;
                echo "                             (for example 'ietf|2' for TekRADIUS)".$crlf;
                echo "                      debug: [0|1] enable/disable enhanced log information".$crlf;
                echo "                             (code result are also displayed on the console)".$crlf;
                echo "               debug-prefix: add a prefix when using the debug mode".$crlf;
                echo "                             (for example 'Reply-Message := ' for FreeRADIUS)".$crlf;
                echo " default-request-prefix-pin: [0|1] prefix PIN enabled/disabled by default".$crlf;
                echo "   default-request-ldap-pwd: [0|1] LDAP/AD password enabled/disabled by default".$crlf;
                echo "                display-log: [0|1] enable/disable log display on the console".$crlf;
                echo "            group-attribute: attribute to return for the group membership".$crlf;
                echo "                             (for example 'Filter-Id' for FreeRADIUS)".$crlf;
                echo "                     issuer: default name of the issuer of the (soft) token".$crlf;
                echo "        ldap-account-suffix: LDAP/AD account suffix".$crlf;
                echo "             ldap-activated: [0|1] enable/disable LDAP/AD support".$crlf;
                echo "               ldap-base-dn: LDAP/AD base".$crlf;
                echo "               ldap-bind-dn: LDAP/AD bind ".$crlf;
                echo "         ldap-cn-identifier: LDAP/AD cn identifier (default is sAMAccountName)".$crlf;
                echo "     ldap-default-algorithm: [totp|hotp|motp|without2fa] default algorithm".$crlf;
                echo "                             for new LDAP/AD users".$crlf;
                echo "    ldap-domain-controllers: LDAP/AD domain controller(s), comma separated".$crlf;
                echo "       ldap-group-attribute: LDAP/AD group attribute (default is memberOf)".$crlf;
                echo "   ldap-group-cn-identifier: LDAP/AD group cn identifier".$crlf;
                echo "                             (default is sAMAccountName for AD, cn for LDAP)".$crlf;
                echo "              ldap-in-group: LDAP/AD group(s) in which users should be in".$crlf;
                echo "       ldap-network-timeout: LDAP/AD network timeout (in seconds)".$crlf;
                echo "                  ldap-port: LDAP/AD port (default is set to 389)".$crlf;
                echo "       ldap-server-password: LDAP/AD server password".$crlf;
                echo "           ldap-server-type: [1|2|4] LDAP/AD server type".$crlf;
                echo "                             (1=AD, 2=standard LDAP, 4=eDirectory)".$crlf;
                echo "                   ldap-ssl: [0|1] enable/disable LDAP/AD SSL connection".$crlf;
                echo " ldap-synced-user-attribute: LDAP/AD attribute used as the account name".$crlf;
                echo "            ldap-time-limit: LDAP/AD number of sec. to wait for search results".$crlf;
                echo "              ldap-users-dn: LDAP/AD users DN (optional, use base-dn if empty)".$crlf;
                echo "                             (you can put several DN separated by semicolons)".$crlf;
                echo "            ldaptls_reqcert: ['auto'|'never'|''|...] how to perform the LDAP TLS".$crlf;
                echo "                             server certificate checks (LDAPTLS_REQCERT)".$crlf;
                echo "                             'auto' means 'never' for Windows and '' for Linux".$crlf;
                echo "       ldaptls_cipher_suite: ['auto'|''|...] which cipher suite is used for the".$crlf;
                echo "                             LDAP TLS connection (LDAPTLS_CIPHER_SUITE)".$crlf;
                echo "                             'auto' means '' for PHP higher than 5.x and".$crlf;
                echo "                             'NORMAL:!VERS-TLS1.2' for PHP 5.x and before".$crlf;
                echo "                        log: [0|1] enable/disable log permanently".$crlf;
                echo "            multiple-groups: [0|1] enable/disable multiple groups per user".$crlf;
                echo "    radius-reply-attributor: [ += |=] how to attribute a value".$crlf;
                echo "                             ('=' for TekRADIUS, ' += ' for FreeRADIUS)".$crlf;
                echo "     radius-reply-separator: [,|:|;|cr|crlf] returned attributes separator".$crlf;
                echo "                             ('crlf' for TekRADIUS, ',' for FreeRADIUS)".$crlf;
                echo "          self-registration: [1|0] enable/disable self-registration of tokens".$crlf;
                echo "         server-cache-level: [1|0] enable/allow cache from server to client".$crlf;
                echo "      server-cache-lifetime: lifetime in seconds of the cached information".$crlf;
                echo "              server-secret: shared secret used for client/server operation".$crlf;
                echo "             server-timeout: timeout value for the connection to the server".$crlf;
                echo "                server-type: [xml] type of the server".$crlf;
                echo "                             (only xml server type is able to do caching)".$crlf;
                echo "                 server-url: full url of the server(s) for client/server mode".$crlf;
                echo "                             (server_url_1;server_url_2 is accepted)".$crlf;
                echo "                 sms-api-id: SMS API id (if any, give your REST/XML API id)".$crlf;
                echo "                             with exec as provider, define the script to call".$crlf;
                echo "                               (available variables: %from, %to, %msg)".$crlf;
                echo "                     sms-ip: IP address of the SMS server (for inhouse server)".$crlf;
                echo "      sms-challenge-enabled: [0|1] enable/disable SMS challenge".$crlf;
                echo "                sms-message: SMS message to display before the OTP".$crlf;
                echo "             sms-originator: SMS sender (if authorized by provider)".$crlf;
                echo "               sms-password: SMS account password".$crlf;
                echo "                   sms-port: Port of the SMS server (for inhouse server)".$crlf;
                echo "               sms-provider: SMS provider (aspsms,clickatell,clickatell2,".$crlf;
                echo "                             intellisms,nexmo,nowsms,smseagle,swisscom,telnyx,".$crlf;
                echo "                             custom,exec)".$crlf;
                echo "                sms-userkey: SMS account username or userkey".$crlf;
                echo $crlf;
                echo "Custom SMS provider only".$crlf;
                echo "                    sms-url: URL(s) of the custom SMS provider".$crlf;
                echo "                               (multiple URLs can be separated by [space],".$crlf;
                echo "                                supported variables : %api_id,%username,".$crlf;
                echo "                                %password,%from,%to,%msg,%ip,%url)".$crlf;
                echo "          sms-send-template: POST template content for custom SMS provider".$crlf;
                echo "                               (supported variables : %api_id,%username,".$crlf;
                echo "                                %password,%from,%to,%msg)".$crlf;
                echo "                 sms-method: [GET|POST|POST-JSON|POST-XML] send method".$crlf;
                echo "               sms-encoding: [ISO|UTF] characters encoding".$crlf;
                echo "         sms-status-success: status result if successful (partial supported)".$crlf;
                echo "                               (example: 20, for any 20x result)".$crlf;
                echo "        sms-content-success: content result if successful (partial supported)".$crlf;
                echo "                               (example: \"status\": \"0\")".$crlf;
                echo "       sms-content-encoding: [''|'HTML'|'URL'|'QUOTES'] Special content encoding".$crlf;
                echo "         sms-no-double-zero: [0|1] Remove double zero for international numbers".$crlf;
                echo "             sms-basic-auth: [0|1] Enable basic HTTP authentication".$crlf;
                echo "                               (sms-userkey:sms-password)".$crlf;
                echo $crlf;
                echo "                 sql-server: SQL server (FQDN or IP)".$crlf;
                echo "               sql-username: SQL username".$crlf;
                echo "               sql-password: SQL password".$crlf;
                echo "               sql-database: SQL database".$crlf;
                echo "           sql-config-table: SQL config table, default is multiotp_config".$crlf;
                echo "          sql-devices-table: SQL devices table, default is multiotp_devices".$crlf;
                echo "              sql-log-table: SQL log table, default is multiotp_log".$crlf;
                echo "           sql-tokens-table: SQL tokens table, default is multiotp_tokens".$crlf;
                echo "            sql-users-table: SQL users table, default is multiotp_users".$crlf;
                echo " sync-delete-retention-days: days of retention before deleting a no more".$crlf;
                echo "                             existing AD/LDAP user (0=disable only, no delete)".$crlf;
                echo "   tel-default-country-code: Default country code for phone number".$crlf;
                echo "         text-sms-challenge: Text displayed for the SMS challenge".$crlf;
                echo "       text-token-challenge: Text displayed for the challenge".$crlf;
                echo " token-serial-number-length: Length of the serial number of the tokens".$crlf;
                echo "                             (used for self-registration)".$crlf;
                echo $crlf;
                echo " multiotp -initialize-backend (when all options are set, it will initialize".$crlf;
                echo "                               the backend, including creating the tables)".$crlf;
                echo $crlf;
                echo " multiotp -set user option1=value1 option2=value2 ... optionN=valueN".$crlf;
                echo "  options are  email: update the email of the user".$crlf;
                echo "         cache-level: [1|0] enable/allow cache for this user on the client".$crlf;
                echo "      cache-lifetime: set/update lifetime in seconds of cached information".$crlf;
                echo "         description: set a description to the user, used for example during".$crlf;
                echo "                      the QRcode generation as the description of the account".$crlf;
                echo "               group: set/update the group of the user".$crlf;
                echo "            ldap-pwd: [0|1] the LDAP/AD password is used instead of the pin".$crlf;
                echo "                 pin: set/update the private pin code of the user".$crlf;
                echo "          prefix-pin: [0|1] the pin and the token must by merged by the user".$crlf;
                echo "                      (if your pin is 1234 and your token displays 5556677,".$crlf;
                echo "                      you will have to type 1234556677)".$crlf;
                echo "                 sms: set/update the sms phone number of the user".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Authentication parameters:".$crlf;
                echo $crlf;
                echo " -calling-ip=Framed-IP-Address".$crlf;
                echo " -calling-mac=Calling-Station-Id".$crlf;
                echo " -chap-challenge=0x... CHAP-Challenge".$crlf;
                echo " -chap-id=0x... Optional CHAP-Id".$crlf;
                echo "          (the first byte of the chap-password value should contain this value)".$crlf;
                echo " -chap-password=0x... CHAP-Password".$crlf;
                echo " -mac=Called-Station-Id".$crlf;
                echo " -ms-chap-challenge=0x... MS-CHAP-Challenge".$crlf;
                echo " -ms-chap-response=0x... MS-CHAP-Response".$crlf;
                echo " -ms-chap2-response=0x... MS-CHAP2-Response".$crlf;
                echo " -src=Packet-Src-IP-Address".$crlf;
                echo " -state=State".$crlf;
                echo " -tag=Client-Shortname".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Client/server inline parameters:".$crlf;
                echo $crlf;
                echo " -server-cache-level=[1|0] enable/allow cache from server to client".$crlf;
                echo " -server-secret=shared secret used for client/server operation".$crlf;
                echo " -server-timeout=timeout value for the connection to the server".$crlf;
                echo " -server-url=full url of the server(s) for client/server mode".$crlf;
                echo "             (-server-url=server_url_1;server_url_2 is accepted)".$crlf;
                echo $crlf;
                echo $crlf;
                echo "AD/LDAP integration:".$crlf;
                echo $crlf;
                echo " multiotp -ldap-check          : check the AD/LDAP connection".$crlf;
                echo " multiotp -ldap-user-info user : print the AD/LDAP information for this user".$crlf;
                echo " multiotp -ldap-users-list     : print the list of selected the AD/LDAP users".$crlf;
                echo " multiotp -ldap-users-sync     : launch the AD/LDAP synchronization".$crlf;
                echo "                                 (will check first if a lock file is present)".$crlf;
                echo " multiotp -sync-delete-retention-days=days of retention before deleting a no".$crlf;
                echo "                                      more existing AD/LDAP user".$crlf;
                echo "                                      (0=disable only the user, do not delete)".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Backup/restore commands:".$crlf;
                echo $crlf;
                echo " multiotp -backup-config  password [file-name]".$crlf;
                echo " multiotp -restore-config password [file-name]".$crlf;
                echo "   By default, the file name is multiotp.cfg in the current folder.".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Other information commands:".$crlf;
                echo $crlf;
                echo " multiotp -phpinfo         : print the current PHP version".$crlf;
                echo " multiotp -showlog         : print the log entries".$crlf;
                echo " multiotp -clearlog        : clear the log entries".$crlf;
                echo " multiotp -tokenslist      : print the list of the tokens".$crlf;
                echo " multiotp -userslist       : print the list of the users".$crlf;
                echo " multiotp -lockeduserslist : print the list of the locked users".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Special commands: ".$crlf;
                echo $crlf;
                echo " multiotp -purge-lock-folder".$crlf;
                echo "   This will delete the .lock files in the lock folder.".$crlf;
                echo "   .lock files are used to handle multiple instances.".$crlf;
                echo "   They are valid by default for 5 minutes.".$crlf;
                echo $crlf;
                echo " multiotp -purge-ldap-cache-folder".$crlf;
                echo "   This will delete the .cache files in the AD/LDAP cache folder.".$crlf;
                echo "   .cache files are used to speed up the AD/LDAP synchronizsation process.".$crlf;
                echo "   They are valid by default for 60 minutes.".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Other parameters:".$crlf;
                echo $crlf;
                echo " -base-dir=/full/path/to/the/main/folder/of/multiotp/".$crlf;
                echo "           (if the script folder is wrongly detected, this will fix the issue)".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Switches:".$crlf;
                echo $crlf;
                echo " -debug          Enhanced log information activated and code result on console".$crlf;
                echo "                 (the permanent state of debug can be set with -config debug=1)".$crlf;
                echo " -display-log    Log information will also be displayed on the console".$crlf;
                echo "                 (the permanent state can be set with -config display-log=1)".$crlf;
                echo " -help           Display this help page".$crlf;
                echo " -keep-local     Keep local user even if the server doesn't have it".$crlf;
                echo "                 (if the server doesn't have it, the local one will be checked)".$crlf;
                echo " -log            Log operation in the log subdirectory or in the database".$crlf;
                echo "                 (the permanent state of log can be set with -config log=1)".$crlf;
                echo " -network-info   Display network info (mode, ip, mask, gateway, dns1, dns2)".$crlf;
                echo " -nt-key-only    Return ONLY NT_KEY to the radius server".$crlf;
                echo " -param          All parameters are logged for debugging purposes".$crlf;
                echo " -php-version    Display the current version of the running PHP interpreter".$crlf;
                echo " -request-nt-key Return NT_KEY with the other attributes to the radius server".$crlf;
                echo " -status         Display a status bar during resynchronization".$crlf;
                echo " -version        Display the current version of the library".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Examples:".$crlf;
                echo $crlf;
                echo " multiotp -fastcreate gademo".$crlf;
                echo " multiotp -debug -createga gauser 2233445566777733".$crlf;
                echo " multiotp -debug -create alan TOTP 3683453456769abc3452 2233 6 60".$crlf;
                echo " multiotp -debug -set alan prefix-pin=1".$crlf;
                echo " multiotp -debug -create anna TOTP 56821bac24fbd2343393 4455 6 30".$crlf;
                echo " multiotp -debug -set anna prefix-pin=0".$crlf;
                echo " multiotp -debug -create john HOTP 31323334353637383930 5678 6 137".$crlf;
                echo " multiotp -debug -create -token-id rick 2010090201901 2345".$crlf;
                echo " multiotp -log -create jimmy mOTP 004f5a158bca13984d349a7f23 1234 6 10".$crlf;
                echo $crlf;
                echo " multiotp -set gademo description=\"VPN code for gademo\"".$crlf;
                echo " multiotp -set jimmy sms=41791234567".$crlf;
                echo $crlf;
                echo " multiotp jimmy sms".$crlf;
                echo $crlf;
                echo " multiotp -scratchlist gademo".$crlf;
                echo $crlf;
                echo " multiotp -display-log -log -debug jimmy ea2315".$crlf;
                echo " multiotp -display-log -log anna 546078".$crlf;
                echo " multiotp -display-log -log -checkpam".$crlf;
                echo " multiotp john 5678124578".$crlf;
                echo $crlf;
                echo " multiotp -debug -import tokens.pskc \"1234 5678 9012 3456 7890 1234 5678 9012\"".$crlf;
                echo " multiotp -debug -import-pskc tokens.pskc \"qwerty\"".$crlf;
                echo " multiotp -debug -import 10OTP_data01_upgrade.sql".$crlf;
                echo " multiotp -debug -import-dat importAlpine.dat".$crlf;
                echo $crlf;
                echo " multiotp -debug -qrcode gademo gademo.png".$crlf;
                echo " multiotp -debug -urllink john".$crlf;
                echo $crlf;
                echo " multiotp -resync john 5678456789 5678345231".$crlf;
                echo " multiotp -resync -status anna 4455487352 4455983513".$crlf;
                echo " multiotp -update-pin alan 4417".$crlf;
                echo $crlf;
                echo " multiotp -config debug-prefix=\"Reply-Message := \"".$crlf;
                echo $crlf;
                echo " multiotp -config server-cache-level=1 server-cache-lifetime=15552000".$crlf;
                echo " multiotp -config server-secret=MySharedSecret server-type=xml".$crlf;
                echo " multiotp -config server-timeout=3".$crlf;
                echo " multiotp -config server-url=http://my.server/multiotp/;my.server2:8112/secure/".$crlf;
                echo $crlf;
                echo " multiotp -config sms-provider=clickatell sms-userkey=CL1 sms-password=PASS".$crlf;
                echo " multiotp -config sms-api-id=1234567".$crlf;
                echo " multiotp -config sms-message=\"Your SMS-code is:\" sms-originator=Company".$crlf;
                echo " multiotp -config sms-message=\"Type %s as code\" sms-originator=0041797654321".$crlf;
                echo $crlf;
                echo " multiotp -config sms-provider=exec sms-api-id=\"/path/to/app %from %to \"%msg\"\"".$crlf;
                echo $crlf;
                echo " multiotp -config token-serial-number-length=10,12".$crlf;
                echo $crlf;
                echo " multiotp -config backend-type=mysql sql-server=fqdn.or.ip sql-database=dbname".$crlf;
                echo " multiotp -config sql-username=user sql-password=pass".$crlf;
                echo " multiotp -initialize-backend".$crlf;
                echo $crlf;
                echo " multiotp -config backend-type=pgsql sql-server=fqdn.or.ip sql-database=dbname".$crlf;
                echo " multiotp -config sql-schema=schemaname sql-username=user sql-password=pass".$crlf;
                echo " multiotp -initialize-backend".$crlf;
                echo $crlf;
                echo $crlf;
                echo "multiOTP can be combined with a Raspberry Pi (http://www.raspberrypi.org/) in".$crlf;
                echo "order to have a very low budget strong authentication device. Please look at".$crlf;
                echo "the readme file in order to learn how to set it up in a few steps.".$crlf;
                echo "The distribution is already optimized with an HTTP proxy to speed up the CLI.".$crlf;
                echo "A ready to use binary image can be downloaded at https://download.multiOTP.net/".$crlf;
                echo $crlf;
                echo "multiOTP open source is also available as a ready to use virtual appliance in".$crlf;
                echo "standard OVA, VMware optimized or Hyper-V formats.".$crlf;
                echo "Virtual appliance images can be downloaded at https://download.multiOTP.net/".$crlf;
                echo $crlf;
                echo "multiOTP web service is working fine with any web server supporting PHP.".$crlf;
                echo " - nginx is a light one under Linux and Windows (http://nginx.org/)".$crlf;
                echo " - Mongoose is a light one under Windows (http://code.google.com/p/mongoose/)".$crlf;
                echo " - and many others like Apache HTTP Server (http://httpd.apache.org/)".$crlf;
                echo $crlf;
                echo "multiOTP is working fine with FreeRADIUS under Linux (http://freeradius.org/)".$crlf;
                echo $crlf;
                echo "multiOTP is working fine under Windows with WinRADIUS, a port of FreeRADIUS".$crlf;
                echo "(http://winradius.eu/)".$crlf;
                echo $crlf;
                echo "When used with TekRADIUS (http://www.tekradius.com) the External-Executable".$crlf;
                echo "must be called like this: C:\multiotp\multiotp.exe %ietf|1% %ietf|2%".$crlf;
                echo "Check the readme file for more information".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Some of other products and services based on multiOTP:".$crlf;
                echo " multiOTP Credential Provider (https://download.multiotp.net/)".$crlf;
                echo "  Open-source Credential Provider for Windows Logon, based on MultiotpCPV2RDP".$crlf;
                echo " MultiotpCPV2RDP (https://github.com/arcadejust/MultiotpCPV2RDP)".$crlf;
                echo "  Open-source Credential Provider for Windows Logon, by arcadejust".$crlf;
                echo " mOTP-CP (https://goo.gl/Y8g4ON)".$crlf;
                echo "  Open-source Credential Provider for Windows Logon, by Last Squirrel IT".$crlf;
                echo " ownCloud OTP (https://goo.gl/mKjt43)".$crlf;
                echo "  Open-source One Time Password app for ownCloud (http://owncloud.org)".$crlf;
                echo " UserCredential (https://github.com/cymapgt/UserCredential)".$crlf;
                echo "  Open-source authentication PHP library by Cyril Ogana".$crlf;
                echo " multiOTP Pro 501V (https://www.multiotp.com)".$crlf;
                echo "  Pro version virtual appliance, with full web GUI, 1 free user licence".$crlf;
                echo " multiOTP Pro 420B (https://www.multiotp.com)".$crlf;
                echo "  Pro version tiny hardware device (BeagleBone Black), with full web GUI".$crlf;
                echo " multiOTP Enterprise (http:s//firmware.multiotp.com/enterprise/)".$crlf;
                echo "  Enterprise version virtual appliance, with HA master-slave support,".$crlf;
                echo "   also available as a Raspberry Pi image file".$crlf;
                echo " secuPASS.net (https://www.secuPASS.net)".$crlf;
                echo "  simple SMS trusting service for free WLAN Hotspot".$crlf;
                echo $crlf;
                echo "Don't hesitate to send us an email if your product uses our multiOTP library.".$crlf;
                echo $crlf;
                echo "Visit https://forum.multiotp.net/ for additional support".$crlf;
                echo $crlf;
                echo $crlf;
            }
            break;
    } // switch

    if ($param_info_debug) {
        $param_info = '';
        foreach ($all_args as $one_arg) {
            if ('' != $one_arg) {
                $param_info .= $one_arg.' ';
            }
        }
        $multiotp->WriteLog("Debug: *parameters used with command $command: ".trim($param_info), false, false, 8888, 'Debug', '');
    }

    if (20 <= $result) {
        break; // Error, we don't do the loop for the other commands
    }

} // for (new since 5.0.3.4, to be able to do multiple commands at once


if ($command != "libhash") {
    if ($initialize_backend) {
        $result = $multiotp->InitializeBackend(); // = 0xx
    }

    if (999 == $result) { // Help page only, we don't want to display the result code in this case
        $result = 30; // ERROR: At least one parameter is missing
    } else {
        $reply_message = '';
        // Log the result
        $result_log = $result.' '.(isset($multiotp->_errors_text[$result])?$multiotp->_errors_text[$result]:'');
        if ($multiotp->GetVerboseFlag()) {
            $reply_message = $result.' *'.(isset($multiotp->_errors_text[$result])?$multiotp->_errors_text[$result]:'');
        }
        if ($verbose_prefix != '') {
            $reply_message = $result;
            if ($multiotp->GetVerboseFlag()) {
                $reply_message.=' *'.(isset($multiotp->_errors_text[$result])?$multiotp->_errors_text[$result]:'');
            }
            $reply_message = $verbose_prefix."\"".$reply_message."\"";
            $result_log = $verbose_prefix."\"".$result_log."\"";
        }
        if ($multiotp->GetVerboseFlag()) {
            $multiotp->WriteLog('Debug: *'.$result_log, false, true, 8888, 'Debug', '');
        }
        if ($multiotp->GetDisplayLogFlag()) {
            echo $reply_message.$crlf;
        }

        // echo "DEBUG: $reply_message / $result / $verbose_prefix \n";
        if ($result > 19) {
            if ('' != $verbose_prefix) {
                $multiotp->AddReplyArrayForRadius($verbose_prefix."\"".(isset($multiotp->_errors_text[$result]) ? $multiotp->_errors_text[$result] : $result)."\"");
            } elseif ($multiotp->IsRadiusErrorReplyMessage()) {
                $multiotp->AddReplyArrayForRadius("Reply-Message := \"".(isset($multiotp->_errors_text[$result]) ? $multiotp->_errors_text[$result] : $result)."\"");
            }
        }

        $radius_additional = '';
        $radius_separator = '';

        if (count($multiotp->GetReplyArrayForRadius()) > 0) {
            $ignore_radius_array = explode(";","xxxx;yyyy");
            foreach ($multiotp->GetReplyArrayForRadius() as $one_radius_message) {
                $ignore_attribute = false;
                $current_attribute = trim(mb_substr($one_radius_message, 0, mb_strpos($one_radius_message, trim($multiotp->GetRadiusReplyAttributor()))));
                foreach ($ignore_radius_array as $one_ignore_attribute) {
                    if (false !== mb_strpos(mb_strtoupper($current_attribute,'UTF-8'),mb_strtoupper($one_ignore_attribute,'UTF-8'))) {
                        $ignore_attribute = true;
                    }
                }
                if (!$ignore_attribute) {
                    $radius_additional.= $radius_separator.$one_radius_message;
                    $radius_separator = $multiotp->GetRadiusReplySeparator();
                }
            }
        }
        if ($request_nt_key || $nt_key_only) {
            $nt_key = trim($multiotp->GetNtKey());
            if ('' != $nt_key) {
              if ($nt_key_only) {
                $radius_additional = "NT_KEY: ".$nt_key.$crlf;
              } else {
                $radius_additional.= $radius_separator."NT_KEY: ".$nt_key.$crlf;
              }
            }
        }
        if (0 < mb_strlen($radius_additional)) {
            if ($multiotp->GetVerboseFlag()) {
                $multiotp->WriteLog('Debug: *Attributes sent to the RADIUS server: '.$radius_additional, false, false, 8888, 'Debug', '');
            }
          echo $radius_additional."\r\n";
        }
    }
}

if (!$cli_mode) {
    header('X-multiOTP-Error-Level: '.intval($result));
    ob_end_flush();
}

if ($multiotp->GetCredentialProviderMode()) {
    echo "multiOTP Credential Provider mode";
}

exit(intval($result));
?>