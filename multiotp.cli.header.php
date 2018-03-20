<?php
/**
 * @file  multiotp.cli.header.php
 * @brief Command line implementation of the multiOTP PHP class.
 *
 * multiOTP PHP CLI header - Strong two-factor authentication PHP class
 * http://www.multiotp.net
 *
 * Visit http://forum.multiotp.net/ for additional support.
 *
 * Donation are always welcome! Please check http://www.multiotp.net
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
 * @version   5.1.1.2
 * @date      2018-03-20
 * @since     2010-06-08
 * @copyright (c) 2010-2018 SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2010-2018 SysCo systemes de communication sa
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

 *  10 INFO: Access Challenge returned back to the client

 *  11 INFO: User successfully created or updated
 *  12 INFO: User successfully deleted
 *  13 INFO: User PIN code successfully changed
 *  14 INFO: Token has been resynchronized successfully
 *  15 INFO: Tokens definition file successfully imported
 *  16 INFO: QRcode successfully created
 *  17 INFO: UrlLink successfully created
 *  18 INFO: SMS code request received
 *  19 INFO: Requested operation successfully done

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
 *  37 ERROR: Token already attributed
 *  38 ERROR: User is desactivated
 *  39 ERROR: Requested operation aborted
 *
 *  41 ERROR: SQL error
 *
 *  50 ERROR: QRcode not created
 *  51 ERROR: UrlLink not created (no provisionable client for this protocol)
 *
 *  60 ERROR: No information on where to send SMS code
 *  61 ERROR: SMS code request received, but an error occured during transmission
 *  62 ERROR: SMS provider not supported
 *
 *  70 ERROR: Server authentication error
 *  71 ERROR: Server request is not correctly formatted
 *  72 ERROR: Server answer is not correctly formatted
 *
 *  80 ERROR: Server cache error
 *  81 ERROR: Cache too old for this user, account autolocked
 *
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
 * Users feedbacks and comments
 *
 * 2018-02-13 Jonathan Garber (via GitHub)
 *   Thanks for your feedback about various issues.
 *
 * 2017-04-19 Frank van der Aa, Vanboxtel BV (NL)
 *   Thanks a lot for your valuable implementation suggestion about PostgreSQL.
 *   The proposed code has been adapted and integrated in the project.
 *
 * 2017-01-05 Stefan Kügler, SerNet GmbH (DE)
 *   Thanks for your feedbacks on the last beta edition.
 *
 * 2017-01-04 Frank van der Aa, Vanboxtel (NL)
 *   Thanks for your feedback concerning leading zeros that can be omitted for the OTP or the PIN.
 *   This has been fixed for the next 5.0.3.4 release.
 *
 * 2016-12-07, 2016-12-01 Stefan Kügler, SerNet GmbH (DE)
 *   Thanks for your feedbacks on the last beta edition.
 *
 * 2016-12-02, Jim Bailey (USA)
 *   Thanks for your feedbacks with some features proposals.
 *
 * 2016-11-25 SKB Kontur (RU)
 *   Thanks for your appreciated $$$ donation.
 *
 * 2016-11-23 Serg Avtukhovich, SKB Kontur (RU)
 *   Serg had some issues with large Active Directory. He did the beta tests for several improvements.
 *
 * 2016-11-10 SerNet GmbH (DE)
 *   MANY thanks for your appreciated $$$ sponsorship for new implemented features proposed by Stefan Kügler.
 *
 * 2016-04-18 Serg Avtukhovich, SKB Kontur (RU)
 *   Serg had some strange problems when using multiOTP in client/server mode.
 *   After some trials, we fix the issue with the server when "log on display" is activated.
 *
 * 2015-12-20 Svetoslav Mateev, STS Soft (BG)
 *   Thanks for your appreciated $$$ donation.
 *
 * 2015-12-18 Sam Leach, Warwickshire County Council (UK)
 *   Sam informed us that a huge AD/LDAP organizational unit (100'000 users)
 *   crashed the sync process. This has been internally reproduced and corrected.
 *
 * 2015-08-10 Edward Kovarski (CA)
 *   Edward informed us that some special chars in the LDAP/AD group name
 *    was killing the SyncLdapUsers process. This has been corrected.
 *
 * 2015-07-14 Pierre-Nicolas Paradis, SherWeb (FR)
 *   Pierre-Nicolas informed us that it was still not possible to change
 *    the admin password using the web GUI. This has been corrected.
 *
 * 2015-06-23 Jun Li (CN)
 *   As proposed by Jun Li, launching the command line version without
 *    enough parameters returns now a 30 error code (instead of 19).
 *    Side effect is that -help is now required to display help page.
 *
 * 2015-06-02 Jean-François Perillo, Kudelski Security (CH)
 *   As proposed by Jean-François, token length error information has been
 *    added in the regular log and the autoresync is now enabled by default.
 *
 * 2015-06-02 Sébastien Charlier, Thesis SA (CH)
 * 2015-03-09 Martin
 *   Martin and Sébastien informed us that passwords containing the minus sign are not accepted.
 *
 * 2015-02-16 Sylvain Maret, Kudelski Security (CH)
 *   Sylvain informed us that Gemalto PSKC file don't provide the time interval for TOTP tokens.
 *   RFC default value (30 seconds) is now set by default if no time interval is given.
 *
 * 2015-01-27 Thomas Klute, ingenit GmbH & Co. KG (DE)
 *   Thanks Thomas for you feedback concerning a potential exploit with dots and slashes in a username.
 *   Even if no information can be extracted using this method, it's always good to patch this kind of weakness.
 *
 * 2015-01-08 Markus Arnoldi, LEWA Attendorn GmbH (DE)
 *   Useful comments about prefix PIN handling, documentation has been enhanced.
 *   Two new command line options are now available (fastcreatenopin and fastecreatewithpin)
 *
 * 2014-12-22 Sajid Hameed, Network Places Ltd (UK)
 *   Questions about users lockout, documentation has been enhanced.
 *   Three command line options information has been added in the documentation.
 *
 * 2014-12-15 Steve Jacot-Guillarmod, Swissdotnet SA (CH)
 *   Thanks Steve for your valuable feedback about LDAP sync and groups
 *   handling with a specific Synology OpenLDAP server implementation.
 *
 * 2014-11-04 Yubico Inc. (USA) / Yubico AB (S) / Yubico Ltd. (UK)
 *   BIG THANKS to the Yubico team which provides us several YubiKeys for the
 *   workshop organized during the Application Security Forum in Yverdon-les-Bains (Switzerland).
 *   Starting with version 4.3.0.0, YubiKeys (both Yubico OTP and HOTP) are now also supported and easy to import.
 *   (simply import the YubiKey traditional format log file)
 *
 * 2014-10-13 Adam Twardowski, Choopa LLC (USA)
 *   Thanks Adam for your valuable feedback concerning a bug with the NT_KEY generation if prefix PIN is enabled.
 *   Adam discovered the bug and fixed it when he configured pptpd with
 *   FreeRADIUS in order to set up a PPTP VPN with strong authentication.
 *
 * 2014-06-17 Stefan Kügler, SerNet GmbH (DE)
 *   Stefan proposes to add Active Directory msRADIUSFramedIPAddress attribute
 *   synchronization in order to distribute the Framed-IP-Address to a user.
 *
 * 2014-04-04 Stefan Kügler, SerNet GmbH (DE)
 * 2014-04-01 Daniel Särnström, Donator AB (SE)
 *   Daniel & Stefan asks some info in order to import tokens without a know format.
 *   Good question, multiOTP supports now importation of tokens from CSV file.
 *
 * 2014-04-02 Prashant Kumar, Alscient (UK)
 *   Prash is playing with FreeRADIUS and VPN (PPTP with MPPE). This requires radius to send MPPE keys.
 *   Interesting feedback, multiOTP provides now NT_KEY, like the ntlm_auth external helper.
 *
 * 2014-03-31 Alex Tasikas (GR)
 *   Thanks Alex for your valuable feedback concerning some bugs in LDAP support.
 *
 * 2014-03-25 Prashant Kumar, Alscient (UK)
 *   As proposed by Prash, we have added the possibility to modify the list of attributes to encrypt.
 *
 * 2014-03-17 Arthur de Jong, West Consulting (NL)
 *   Arthur gave some feedbacks concerning distributing the source code in the
 *    "preferred form of the work for making modifications".
 *
 * 2014-03-14 Soeren Malchow, MCON (DE)
 *   Thanks for your feedback concerning a bug in the SQL request for the log table.
 *
 * 2014-01-27 Henk van der Helm (NL)
 *   MANY thanks for your appreciated $$$ donation.
 *
 * 2014-01-19 Erik Nylund (FI)
 *   Thanks four your feedback concerning specific parameters order in QRCode for Microsoft Authenticator
 *
 * 2014-01-14 Sylvain Maret, Kudelski Security (CH)
 *   Thanks for your feedback concerning possible zero division in the ComputeOathTruncate method.
 *    Method has been altered in order to be more compatible with almost any PHP version.
 *   Thanks also for the suggestion to resync without the prefix PIN. Both are supported now.
 *
 * 2014-01-08/09  Cheng Shao-Pin (CN)
 *   Thanks for your feedback concerning possible missing JSON extension in old PHP distribution
 *    and possible image functions incompatibilities with some PHP versions during QRcode generation.
 *   Thanks also for your appreciated $ donation.
 *
 * 2014-01-08  Cheng Shao-Pin (CN) and Daniel Särnström, Donator AB (SE)
 *   Thanks for your feedback concerning md5.js missing in the distribution.
 *
 * 2013-12-20 Rico Zeiss, Hermann Wegener GmbH & Co. KG (DE)
 *   MANY thanks for your appreciated $$$ sponsorship to support us to add MS-CHAP and MS-CHAPv2 in a next release.
 *
 * 2013-12-18 Xavier Céspedes (ES)
 *   Thanks to Xavier who noticed a problem with the hex2bin() function duringthe scratch password generation.
 *   In the meantime, the GetUserScratchPasswordsList() function has been improved and fixed and is in the 4.1 release.
 *
 * 2013-09-20 Sean Butler-Lee (IE)
 *   Thanks a lot for announcing a bug with the GetUserScratchPasswordsArray() method.
 *
 * 2013-08-22,26 Frank Bongrand (FR)
 *   Thanks a lot for valuable feedbacks concerning some minor bugs in 4.0.4 and 4.0.6
 *
 * 2013-08-21 Henk van der Helm (NL)
 *   Thanks a lot for a valuable feedback concerning some minor bugs in 4.0.4
 *
 * 2013-08-15 Donator AB (SE)
 *   MANY thanks for your appreciated $$$ sponsorship to support us to add self-registration in a next release.
 *
 * 2013-08-13 Daniel Särnström, Donator AB (SE)
 *   Daniel proposed to add self-registration and pskc v12 with encrypted data support (OATH compliant).
 * 
 * 2013-07-25 Dominik Pretzsch from Last Squirrel IT (DE)
 *   After some discussions with Dominik, integration of the client/server support in the basic library
 *
 * 2013-07-23 Stefan Kügler (DE) (again ;-)
 *   Stefan proposed to add the possibility to show the log, which is especially convenient for MySQL log.
 *   He proposed also to be able to call an external program to send SMS.
 *
 * 2013-07-11 Stefan Kügler (DE)
 *   Stefan proposed to add a lock and unlock option for the user.
 *
 * 2013-06-19 SerNet GmbH (DE)
 *   MANY thanks for your appreciated $$$ sponsorship after we implemented some features proposed by Stefan Kügler.
 *
 * 2013-06-13 Henk van der Helm (NL) (again ;-)
 *   Henk proposed to be able to have a specific description for the software token.
 *   (we use the already existing user description attribute)
 *
 * 2013-06-01 Stefan Irion (CH)
 *   Thanks for your appreciated $$ donation.
 *
 * 2013-05-14 Henk van der Helm (NL)
 *   Henk asked to support also the provider IntelliSMS. Thanks for the $$ sponsorship!
 *
 * 2013-05-03 Stefan Kügler (DE)
 *   Stefan proposed to lower the default max_time_window to 600 seconds.
 *
 * 2013-03-04 Alan DeKok (CA)
 *   Alan proposed in the freeradius mailing-list to put a prefix to be able to handle the
 *   debug info by the freeradius server.
 *
 * 2012-11-28  Gareth Thomas
 *   Thanks for your appreciated $$ donation.
 *
 * 2012-03-16 Nicolas Goralski (LU)
 *   Nicolas proposed an enhancement in order to support PAM. Thanks also for the $$ sponsorship!
 *     (with the -checkpam option in the command line edition)
 *
 * 2011-05-19 Fabiano Domeniconi (CH)
 *   Fabiano found old info in the samples, CheckToken() is not boolean anymore! Samples fixed.
 *
 * 2011-04-24 Steven Roddis (AU)
 *   Steven asked for more examples. Thanks to Steven for the $ donation ;-)
 *
 * 2010-09-15 Jasper Pol (NL)
 *   Jasper has added an initial MySQL backend support
 *
 * 2010-09-13 Brenno Hiemstra (NL)
 *   Brenno reported bad extra spaces after the #!/usr/bin/php in the Linux version of multiotp.php
 *
 * 2010-08-20 C. Christophi, BirdNet (CH)
 *   Documentation enhancement proposal for the TekRADIUS part, thanks !
 *
 * 2010-07-19 SysCo/al (CH)
 *   Well, as requested by some users, the new "class" design is done, enjoy !
 *
 *
 * Change Log
 *
 *   2018-03-16 5.1.1.1 SysCo/al FIX: command line -set error for ldap-pwd and prefix-pin
 *   2018-02-26 5.1.0.6 SysCo/al Regular registry entries are now used directly from the Credential Provider.
 *   2018-02-19 5.1.0.3 SysCo/al Credential Provider multiOTPOptions registry entry is used if available
 *   2017-11-10 5.0.6.0 SysCo/al New -cp option (Credential Provider mode)
 *   2017-05-29 5.0.4.5 SysCo/al PostgreSQL support, based on source code provided by Frank van der Aa
 *   2017-02-21 5.0.3.6 SysCo/al Seed can now be given in Base32 format
 *   2017-02-03 5.0.3.5 SysCo/al -user-info fixed and replaced by a call to the GetUserInfo method
 *   2017-01-24 5.0.3.4 SysCo/al It's now possible to do several commands at once with the CLI edition
 *                               Some new commands added
 *                               Commands -user-info and -ldap-user-info enhanced
 *                               Commands -lock and -unlock return now 19 (instead of 99)
 *   2016-11-14 5.0.3.0 SysCo/al Better SSL support
 *                               Some new commands added
 *   2016-11-04 5.0.2.6 SysCo/al Better SSL support
 *                               Specific LDAP/AD attribute used as the synchronised account name can be defined
 *                               Implementing new library options
 *                               Additional error information sent back in the Reply-Message attribute
 *                                  (the debug prefix must be set to Reply-Message = )
 *                               Backup configuration file can now be restored in commercial version without any change
 *   2016-08-02 5.0.1.4 SysCo/al Command -network-info added
 *                               More debug information
 *   2015-07-18 4.3.2.6 SysCo/al Minor fixes
 *   2015-07-15 4.3.2.5 SysCo/al Calling multiotp CLI without parameter returns now error code 30 (instead of 19)
 *   2015-06-24 4.3.2.4 SysCo/al multiotp_account automatic support
 *   2015-06-10 4.3.2.3 SysCo/al Enhancements for the Dev(Talks): demo
 *   2015-06-09 4.3.2.2 SysCo/al Additional CLI features (fastcreatenopin, fastcreatewithpin)
 *                               Initialize-backend process enhanced
 *                               Resync during authentication (autoresync) is now better handled in the class directly
 *   2014-12-09 4.3.1.0 SysCo/al MULTIOTP_PATH environment variable support
 *                               CLI local proxy mode support added to speed up the command line
 *                               Scratch password need also the prefix PIN if it's activated
 *                               OTP with integrated serial numbers better supported (in PAP)
 *                               Generic LDAP support (no more only Microsoft AD compatible LDAP)
 *                               Raspberry Pi edition has the local proxy mode activated to speed up the process
 *   2014-11-04 4.3.0.0 SysCo/al Command -lockeduserslist added
 *                               Resynchronization is now done with ResyncToken() method instead of CheckToken()
 *                      SysCo/yj Changing examples : %message -> %msg; Added " around parameter sms-api-id in the example
 *   2014-06-12 4.2.4.3 SysCo/al Bug fix concerning aspsms provider
 *   2014-04-13 4.2.4.2 SysCo/al Minor fixes
 *   2014-04-06 4.2.4.1 SysCo/al Fixed bug concerning LDAP handling
 *                               NT_KEY support added (for FreeRADIUS further handling)
 *                               Tokens CSV import (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
 *                               When a user is deleted, the token(s) attributed to this user is/are unassigned
 *                               New option -user-info added
 *   2014-03-30 4.2.4   SysCo/al Fixed bug concerning MySQL handling and mysqli support added
 *                               Enhanced SetAttributesToEncrypt function
 *                               New implementation for some external classes
 *                               Generated QRcode are better
 *                               LOT of new QA tests, more than 60 different tests (including PHP class and command line versions)
 *                               Enhanced documentation
 *   2014-03-13 4.2.3   SysCo/al Updated examples
 *   2014-03-03 4.2.2   SysCo/al Cleaned some non-interpreted TekRADIUS variables (for old TeKRADIUS releases)
 *                               Some values can now go back to TekRADIUS
 *   2014-02-07 4.2.0   SysCo/al MS-CHAP and MS-CHAPv2 fully supported
 *   2014-01-21 4.1.2   SysCo/al Direct call of class methods using -call-method
 *   2014-01-20 4.1.1   SysCo/al Minor fixes
 *   2013-12-23 4.1.0   SysCo/al Some modifications in order to correctly handle the class methods
 *                               It is now possible to activate or desactivate a user
 *                               Encrypted pskc files are now supported
 *   2013-08-30 4.0.7   SysCo/al GetScriptFolder() was still buggy sometimes, thanks Frank for the feedback
 *                               File mode of the created QRcode file is also changed base on GetLinuxFileMode()
 *   2013-08-25 4.0.6   SysCo/al base32_encode() is now RFC compliant with uppercases
 *                               GetUserTokenQrCode() and GetTokenQrCode() where buggy
 *                               GetScriptFolder() use now __FILE__ if the full path is included
 *                               When doing a check in the CLI header, @... is automatically removed from the
 *                                username if the user doesn't exist, and the check is done on the clean name
 *                               Added a lot of tests to enhance release quality
 *   2013-08-21 4.0.5   SysCo/al Fixed the check of the cache lifetime
 *                               Added a temporary server blacklist during the same instances
 *                               Default server timeout is now set to 1 second
 *   2013-08-20 4.0.4   SysCo/al Added an optional group attribute for the user
 *                                (which will be send with the Radius Filter-Id option)
 *                               Added scratch passwords generation (if the token is lost)
 *                               Automatic database schema upgrade using method UpgradeSchemaIfNeeded()
 *                               Added client/server support with local cache
 *                               Added CHAP authentication support (PAP is of course still supported)
 *                               The encryption key is now a parameter of the class constructor
 *                               The method SetEncryptionKey('MyPersonalEncryptionKey') IS DEPRECATED
 *                               The method DefineMySqlConnection IS DEPRECATED
 *                               Full MySQL support, including tables creation (see example and SetSqlXXXX methods)
 *                               Added email, sms and seed_password to users attributes
 *                               Added sms support (aspsms, clickatell, intellisms, exec)
 *                               Added prefix support for debug mode (in order to send Reply-Message := to Radius)
 *                               Added a lot of new methods to handle easier the users and the tokens
 *                               General speedup by using available native functions for hash_hmac and others
 *                               Default max_time_window has been lowered to 600 seconds (thanks Stefan for suggestion)
 *                               Integrated Google Authenticator support with integrated base 32 seed handling
 *                               Integrated QRcode generator library (from Y. Swetake)
 *                               General options in an external configuration file
 *                               Comments have been reformatted and enhanced for automatic documentation
 *                               Development process enhanced, source code reorganized, external contributions are
 *                                added automatically at the end of the library after an internal build release
 *   2011-10-25 3.9.2   SysCo/al Improved get_script_dir() for Linux/Windows compatibility
 *   2011-09-15 3.9.1   SysCo/al Some quick fixes concerning multiple users
 *   2011-09-13 3.9.0   SysCo/al Adding support for account with multiple users
 *   2011-07-06 3.2.0   SysCo/al Encryption hash handling with additional error message 33
 *                                (if the key has changed)
 *                               Adding more examples
 *                               Adding generic user with multiple account
 *                                (Real account name is combined: "user" and "account password")
 *                               Adding log options, now default doesn't log token value anymore
 *                               Debugging MySQL backend support for the token handling
 *                               Fixed automatic detection of \ or / for script path detection
 *   2010-12-19 3.1.1   SysCo/al Better MySQL backend support, including in CLI version
 *   2010-09-15 3.1.0   SysCo/al Removed bad extra spaces in the multiotp.php file for Linux
 *                               MySQL backend support
 *   2010-09-02 3.0.0   SysCo/al Adding tokens handling support, including importing XML tokens definition file
 *                                (http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00)
 *                               Enhanced flat database file format (multiotp is still compatible with old formats)
 *                               Internal method SetDataReadFlag renamed to SetUserDataReadFlag
 *                               Internal method GetDataReadFlag renamed to GetUserDataReadFlag
 *   2010-08-21 2.0.4   SysCo/al Enhancement in order to use an alternate php "compiler" for Windows command line
 *                               Documentation enhancement
 *   2010-08-18 2.0.3   SysCo/al Minor notice fix, define timezone if not defined (for embedded command line)
 *                               If user doesn't exist, do not create the related flat file after a check
 *   2010-07-21 2.0.2   SysCo/al Fix to create correctly the folders "users" and "log" if needed
 *   2010-07-19 2.0.1   SysCo/al Adding more information in the help text
 *   2010-07-19 2.0.0   SysCo/al New design using a class and a cli header stub
 *   2010-06-15 1.1.5   SysCo/al Adding OATH/TOTP support
 *   2010-06-15 1.1.4   SysCo/al Project renamed to multiotp to avoid overlapping
 *   2010-06-08 1.1.3   SysCo/al Typo in script folder detection
 *   2010-06-08 1.1.2   SysCo/al Typo in variable name
 *   2010-06-08 1.1.1   SysCo/al Status bar during resynchronization
 *   2010-06-08 1.1.0   SysCo/al Fix in the example, distribution not compressed
 *   2010-06-07 1.0.0   SysCo/al Initial implementation
 *
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

    if (substr($current_script_folder_detected,-1) != "/") {
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
        if ((1 < strlen($var)) && ((('"' == substr($var,0,1)) && ('"' == substr($var,-1))) || (("'" == substr($var,0,1)) && ("'" == substr($var,-1))))) {
            $var = substr($var, 1, strlen($var)-2);
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
$write_config_data   = false;
$write_param_data    = false;


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

    $current_arg = clean_quotes($argv[$arg_loop]);

    $not_a_command = FALSE;

    if ("-activate" == mb_strtolower($current_arg)) {
        $command = "activate";
    } elseif ("-assign-token" == mb_strtolower($current_arg)) {
        $command = "assign-token";
    } elseif ("-callapi" == mb_strtolower($current_arg)) {
        $command = "callapi";
    } elseif ("-backup-config" == mb_strtolower($current_arg)) {
        $command = "backup-config";
    } elseif ("-call-method=" == substr(mb_strtolower($current_arg),0,13)) {
        $command = "call-method";
        $src_array = explode("=",$current_arg,2);
        if (2 == count($src_array)) {
            $call_method = $src_array[1];
        }
    } elseif ("-check" == mb_strtolower($current_arg)) {
        $command = "check";
    } elseif ("-check-ldap-password" == mb_strtolower($current_arg)) {
        $command = "check-ldap-password";
    } elseif ("-checkpam" == mb_strtolower($current_arg)) {
        $command = "checkpam";
    } elseif ("-config" == mb_strtolower($current_arg)) {
        $command = "config";
    } elseif ("-create" == mb_strtolower($current_arg)) {
        $command = "create";
    } elseif ("-createga" == mb_strtolower($current_arg)) {
        $command = "createga";
    } elseif ("-custominfo" == mb_strtolower($current_arg)) {
        $command = "custominfo";
    } elseif ("-default-dialin-ip-mask" == mb_strtolower($current_arg)) {
        $command = "default-dialin-ip-mask";
    } elseif ("-delete" == mb_strtolower($current_arg)) {
        $command = "delete";
    } elseif ("-delete-token" == mb_strtolower($current_arg)) {
        $command = "delete-token";
    } elseif ("-desactivate" == mb_strtolower($current_arg)) {
        $command = "desactivate";
    } elseif ("-dialin-ip-address" == mb_strtolower($current_arg)) {
        $command = "dialin-ip-address";
    } elseif ("-dialin-ip-mask" == mb_strtolower($current_arg)) {
        $command = "dialin-ip-mask";
    } elseif ("-fastcreate" == mb_strtolower($current_arg)) {
        $command = "fastcreate";
    } elseif ("-fastcreatenopin" == mb_strtolower($current_arg)) {
        $command = "fastcreatenopin";
    } elseif ("-fastcreatewithpin" == mb_strtolower($current_arg)) {
        $command = "fastcreatewithpin";
    } elseif ("-help" == mb_strtolower($current_arg)) {
        $command = "help";
    } elseif ("-import" == mb_strtolower($current_arg)) {
        $command = "import";
    } elseif ("-import-alpine-xml" == mb_strtolower($current_arg)) {
        $command = "import-alpine-xml";
    } elseif ("-import-csv" == mb_strtolower($current_arg)) {
        $command = "import-csv";
    } elseif ("-import-dat" == mb_strtolower($current_arg)) {
        $command = "import-dat";
    } elseif ("-import-pskc" == mb_strtolower($current_arg)) {
        $command = "import-pskc";
    } elseif ("-import-sql" == mb_strtolower($current_arg)) {
        $command = "import-sql";
    } elseif ("-import-xml" == mb_strtolower($current_arg)) {
        $command = "import-xml";
    } elseif ("-import-yubikey" == mb_strtolower($current_arg)) {
        $command = "import-yubikey";
    } elseif ("-initialize-backend" == mb_strtolower($current_arg)) {
        $command = "initialize-backend";
        $initialize_backend = true;
    } elseif ("-lockeduserslist" == mb_strtolower($current_arg)) {
        $command = "lockeduserslist";
    } elseif ("-ldap-users-list" == mb_strtolower($current_arg)) {
        $command = "ldap-users-list";
    } elseif ("-ldap-users-sync" == mb_strtolower($current_arg)) {
        $command = "ldap-users-sync";
    } elseif ("-ldap-user-info" == mb_strtolower($current_arg)) {
        $command = "ldap-user-info";
    } elseif ("-ldap-check" == mb_strtolower($current_arg)) {
        $command = "ldap-check";
    } elseif ("-phpinfo" == mb_strtolower($current_arg)) {
        $command = "phpinfo";
    } elseif ("-libhash" == mb_strtolower($current_arg)) {
        $command = "libhash";
    } elseif ("-lock" == mb_strtolower($current_arg)) {
        $command = "lock";
    } elseif ("-mysql" == mb_strtolower($current_arg)) {
        $command = "mysql";
    } elseif ("-pgsql" == mb_strtolower($current_arg)) {
        $command = "pgsql";
    } elseif ("-php-version" == mb_strtolower($current_arg)) {
        $command = "php-version";
    } elseif ("-purge-lock-folder" == mb_strtolower($current_arg)) {
        $command = "purge-lock-folder";
    } elseif ("-purge-ldap-cache-folder" == mb_strtolower($current_arg)) {
        $command = "purge-ldap-cache-folder";
    } elseif ("-qrcode" == mb_strtolower($current_arg)) {
        $command = "qrcode";
    } elseif ("-requiresms" == mb_strtolower($current_arg)) {
        $command = "requiresms";
    } elseif ("-remove-token" == mb_strtolower($current_arg)) {
        $command = "remove-token";
    } elseif ("-restore-config" == mb_strtolower($current_arg)) {
        $command = "restore-config";
    } elseif ("-resync" == mb_strtolower($current_arg)) {
        $command = "resync";
    } elseif ("-scratchlist" == mb_strtolower($current_arg)) {
        $command = "scratchlist";
    } elseif ("-seed-info" == mb_strtolower($current_arg)) {
        $command = "seed";
    } elseif ("-set" == mb_strtolower($current_arg)) {
        $command = "set";
    } elseif ("-showlog" == mb_strtolower($current_arg)) {
        $command = "showlog";
    } elseif ("-tokenslist" == mb_strtolower($current_arg)) {
        $command = "tokenslist";
    } elseif ("-unlock" == mb_strtolower($current_arg)) {
        $command = "unlock";
    } elseif ("-update" == mb_strtolower($current_arg)) {
        $command = "update";
    } elseif ("-update-pin" == mb_strtolower($current_arg)) {
        $command = "update-pin";
    } elseif ("-urllink" == mb_strtolower($current_arg)) {
        $command = "urllink";
    } elseif ("-user-info" == mb_strtolower($current_arg)) {
        $command = "user-info";
    } elseif ("-userslist" == mb_strtolower($current_arg)) {
        $command = "userslist";
    } elseif (("-version" == mb_strtolower($current_arg)) || ("-v" == mb_strtolower($current_arg))) {
        $command = "version";
    } elseif ("-version-only" == mb_strtolower($current_arg)) {
        $command = "version-only";
    } else {
        // The current argument is not a command
        $not_a_command = TRUE;
        if ("-base-dir=" == substr(mb_strtolower($current_arg),0,10)) {
            $base_array = explode("=",$current_arg,2);
            if (2 == count($base_array)) {
                $base_dir = clean_quotes($base_array[1]);
            }
        } elseif ("-src=" == substr(mb_strtolower($current_arg),0,5)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $source_ip = clean_quotes($src_array[1]);
            }
        } elseif ("-tag=" == substr(mb_strtolower($current_arg),0,5)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $source_tag = clean_quotes($src_array[1]);
            }
        } elseif ("-mac=" == substr(mb_strtolower($current_arg),0,5)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $source_mac = clean_quotes($src_array[1]);
            }
        } elseif ("-calling-ip=" == substr(mb_strtolower($current_arg),0,12)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $calling_ip = clean_quotes($src_array[1]);
            }
        } elseif ("-calling-mac=" == substr(mb_strtolower($current_arg),0,13)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $calling_mac = clean_quotes($src_array[1]);
            }
        } elseif ("-chap-id=" == substr(mb_strtolower($current_arg),0,16)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $chap_id = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(substr($chap_id,0,6))) || ("%ietf" == mb_strtolower(substr($chap_id,0,5)))) {
                    $chap_id = '';
                }
            }
        } elseif ("-chap-challenge=" == substr(mb_strtolower($current_arg),0,16)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $chap_challenge = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(substr($chap_challenge,0,6))) || ("%ietf" == mb_strtolower(substr($chap_challenge,0,5)))) {
                    $chap_challenge = '';
                }
            }
        } elseif ("-chap-password=" == substr(mb_strtolower($current_arg),0,15)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $chap_password = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(substr($chap_password,0,6))) || ("%ietf" == mb_strtolower(substr($chap_password,0,5)))) {
                    $chap_password = '';
                } else {
                    $encrypted_password = true;
                }
            }
        } elseif ("-ms-chap-challenge=" == substr(mb_strtolower($current_arg),0,19)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $ms_chap_challenge = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(substr($ms_chap_challenge,0,6))) || ("%ietf" == mb_strtolower(substr($ms_chap_challenge,0,5)))) {
                    $ms_chap_challenge = '';
                }
            }
        } elseif ("-ms-chap-response=" == substr(mb_strtolower($current_arg),0,18)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $ms_chap_response = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(substr($ms_chap_response,0,6))) || ("%ietf" == mb_strtolower(substr($ms_chap_response,0,5)))) {
                    $ms_chap_response = '';
                } else {
                    $encrypted_password = true;
                }
            }
        } elseif ("-ms-chap2-response=" == substr(mb_strtolower($current_arg),0,19)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $ms_chap2_response = clean_quotes($src_array[1]);
                if (("%msoft" == mb_strtolower(substr($ms_chap2_response,0,6))) || ("%ietf" == mb_strtolower(substr($ms_chap2_response,0,5)))) {
                    $ms_chap2_response = '';
                } else {
                    $encrypted_password = true;
                }
            }
        } elseif ("-server-url=" == substr(mb_strtolower($current_arg),0,12)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_url = trim(str_replace(",",";",str_replace(" ",";",clean_quotes($src_array[1]))));
            }
        } elseif ("-server-cache-level=" == substr(mb_strtolower($current_arg),0,20)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_cache_level = clean_quotes($src_array[1]);
            }
        } elseif ("-server-secret=" == substr(mb_strtolower($current_arg),0,15)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_secret = clean_quotes($src_array[1]);
            }
        } elseif ("-server-timeout=" == substr(mb_strtolower($current_arg),0,16)) {
            $src_array = explode("=",$current_arg,2);
            if (2 == count($src_array)) {
                $server_timeout = clean_quotes($src_array[1]);
            }
        } elseif ("-cp" == mb_strtolower($current_arg)) {
            $cp_mode = true;
        } elseif ("-debug" == mb_strtolower($current_arg)) {
            $verbose_log = true;
        } elseif ("-display-log" == mb_strtolower($current_arg)) {
            $display_log = true;
        } elseif ("-log" == mb_strtolower($current_arg)) {
            $enable_log = true;
        } elseif ("-keep-local" == mb_strtolower($current_arg)) {
            $keep_local = true;
        } elseif ("-no-php-info" == mb_strtolower($current_arg)) {
            $no_php_info = true;
        } elseif ("-no-prefix-pin" == mb_strtolower($current_arg)) {
            $set_prefix_pin = false;
        } elseif ("-param" == mb_strtolower($current_arg)) {
            $param_info_debug = true;
        } elseif ("-prefix-pin" == mb_strtolower($current_arg)) {
            $set_prefix_pin = true;
        } elseif (("-request-nt-key" == mb_strtolower($current_arg)) || ("--request-nt-key" == mb_strtolower($current_arg))) {
            $request_nt_key = true;
        } elseif ("-show-false-pin" == mb_strtolower($current_arg)) {
            $show_false_pin = true;
        } elseif ("-status" == mb_strtolower($current_arg)) {
            $display_status = true;
        } elseif ("-token-id" == mb_strtolower($current_arg)) {
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
if ($write_param_data) {
    $write_result = $multiotp->WriteConfigData();
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

if (($multiotp->IsDeveloperMode())) {
  $loop_start = 1;
  $temp_radius = '';
  for ($arg_loop=$loop_start; $arg_loop < $argc; $arg_loop++)
  {
    $one_radius = clean_quotes($argv[$arg_loop]);
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
                $mysql_parameters = explode(",",mb_strtolower($all_args[1]));
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
                $pgsql_parameters = explode(",",mb_strtolower($all_args[1]));
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
            if  ($param_count < 3) {
                $result = 30; // ERROR: At least one parameter is missing
            } else {
                $backup_file = ('' != trim($all_args[2])) ? $all_args[2] : 'multiotp.cfg';
                if (TRUE === ($multiotp->RestoreConfiguration(array('restore_file' => $backup_file, 'encryption_key' => $all_args[1])))) {
                  $result = 19; // INFO: Requested operation successfully done
                } else {
                  $result = 99; // ERROR
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
            $self_registration = '';
            $otp_inline = '';
            if  ($param_count > 1) {
                if (!$multiotp->CheckUserExists($all_args[1])) {
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
                        $part1 = substr($all_args[1], 0, mb_strpos($all_args[1], ':'));
                        $part2 = substr($all_args[1], mb_strpos($all_args[1], ':')+1);
                        if ($multiotp->IsSelfRegistrationEnabled() && ($multiotp->CheckTokenExists($part1))) {
                            $self_registration = $part1;
                            $all_args[1] = $part2;
                        } elseif ($multiotp->IsUserRequestLdapPasswordEnabled() && ($multiotp->CheckUserExists($part1))) {
                            $all_args[1] = $part1;
                            $otp_inline = $part2;
                        }
                    }
                    if (false !== mb_strpos($all_args[1], '@')) {
                        $cleaned_user = substr($all_args[1], 0, mb_strpos($all_args[1], '@'));
                        if ($multiotp->CheckUserExists($cleaned_user)) {
                            $all_args[1] = $cleaned_user;
                            $multiotp->SetUser($all_args[1]);
                        }
                    } elseif (false !== mb_strpos($all_args[1], "\\")) {
                        $cleaned_user = substr($all_args[1], mb_strpos($all_args[1], "\\")+1);
                        if ($multiotp->CheckUserExists($cleaned_user)) {
                            $all_args[1] = $cleaned_user;
                            $multiotp->SetUser($all_args[1]);
                        }
                    } else {
                        $clean_phone = $multiotp->CleanPhoneNumber($all_args[1]);
                        if ($multiotp->CheckUserExists($clean_phone)) {
                            $all_args[1] = $clean_phone;
                            $multiotp->SetUser($all_args[1]);
                        }
                    }
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
                        switch (mb_strtoupper($all_args[2]))
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
                                $multiotp->SetSmsUserkey($actual_array[1]);
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
                            case 'tel-default-country-code':
                                $multiotp->SetTelDefaultCountryCode($actual_array[1]);
                                $write_config_data = true;
                                break;
                            case 'token-serial-number-length':
                                $multiotp->SetTokenSerialNumberLength($actual_array[1]);
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
                    if ($multiotp->WriteConfigData()) {
                        $result = 19; // INFO: Requested operation successfully done
                    }
                }
            }
            break;
        case "import":
            if (!@file_exists($all_args[1])) {
                $result = 31; // ERROR: Tokens definition file doesn't exist.
            } else {
                if ($multiotp->ImportTokensFile($all_args[1], $all_args[1], $all_args[2])) {
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
                if ($multiotp->ImportTokensFromPskc($all_args[1], $all_args[2])) {
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
                    echo substr(str_repeat(" ", 23).$array_key, -23).": ".$info_value.$crlf;
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
                echo "SMS-code are supported (current providers: aspsms,clickatell,intellisms).".$crlf;
                echo "Customized SMS sender program supported by specifying exec as SMS provider.".$crlf;
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
                
                reset($multiotp->_errors_text);
                while(list($key, $value) = each($multiotp->_errors_text)) {
                    echo substr("  ".$key, -2)." ".$value." ".$crlf;
                }
                echo $crlf;
                echo $crlf;
                echo "Usage:".$crlf;
                echo $crlf;
                echo " PLEASE NOT THAT BY DEFAULT, A PREFIX PIN IS REQUIRED.".$crlf;
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
                echo "      algo: available algorithms are mOTP, HOTP and TOTP".$crlf;
                echo "      seed: hexadecimal or base32 seed of the token".$crlf;
                echo "       pin: private pin code of the user".$crlf;
                echo "    digits: number of digits given by the token".$crlf;
                echo "       pos: for HOTP algorithm, position of the next awaited event".$crlf;
                echo "  interval: for mOTP and TOTP algorithms, token interval time in seconds".$crlf;
                echo $crlf;
                echo " multiotp -import tokens_definition_file [key|pass] (auto-detect format)".$crlf;
                echo " multiotp -import-csv csv_tokens_file.csv (tokens definition in a file)".$crlf;
                echo "   (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)".$crlf;
                echo " multiotp -import-pskc pskc_tokens_file.pskc [key|pass] (PSKC format, RFC 6030)".$crlf;
                echo " multiotp -import-yubikey yubikey_traditional_format_log.csv (YubiKey)".$crlf;
                echo " multiotp -import-dat importAlpine.dat (SafeWord/Aladdin/SafeNet tokens)".$crlf;
                echo " multiotp -import-alpine-xml alpineXml.xml (SafeWord/Aladdin/SafeNet)".$crlf;
                echo " multiotp -import-xml xml_tokens_definition_file.xml (old Feitian)".$crlf;
                echo " multiotp -import-sql tokens_definition_file.sql (ZyXEL/Authenex)".$crlf;
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
                echo "     ldap-default-algorithm: [totp|hotp|motp] default algorithm for new users".$crlf;
                echo "    ldap-domain-controllers: LDAP/AD domain controller(s), comma separated".$crlf;
                echo "       ldap-group-attribute: LDAP/AD group attribute (default is memberOf)".$crlf;
                echo "   ldap-group-cn-identifier: LDAP/AD group cn identifier".$crlf;
                echo "                             (default is sAMAccountName for AD, cn for LDAP)".$crlf;
                echo "              ldap-in-group: LDAP/AD group(s) in which users should be in".$crlf;
                echo "       ldap-network-timeout: LDAP/AD network timeout (in seconds)".$crlf;
                echo "                  ldap-port: LDAP/AD port (default is set to 389)".$crlf;
                echo "       ldap-server-password: LDAP/AD server password".$crlf;
                echo "           ldap-server-type: [1|2] LDAP/AD server type (1=AD, 2=standard LDAP)".$crlf;
                echo "                   ldap-ssl: [0|1] enable/disable LDAP/AD SSL connection".$crlf;
                echo " ldap-synced-user-attribute: LDAP/AD attribute used as the account name".$crlf;
                echo "            ldap-time-limit: LDAP/AD number of sec. to wait for search results".$crlf;
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
                echo "         server-cache-level: [0|1] enable/allow cache from server to client".$crlf;
                echo "      server-cache-lifetime: lifetime in seconds of the cached information".$crlf;
                echo "              server-secret: shared secret used for client/server operation".$crlf;
                echo "             server-timeout: timeout value for the connection to the server".$crlf;
                echo "                server-type: [xml] type of the server".$crlf;
                echo "                             (only xml server type is able to do caching)".$crlf;
                echo "                 server-url: full url of the server(s) for client/server mode".$crlf;
                echo "                             (server_url_1;server_url_2 is accepted)".$crlf;
                echo "                 sms-api-id: SMS API id (clickatell only, give your XML API id)".$crlf;
                echo "                             with exec as provider, define the script to call".$crlf;
                echo "                             (available variables: %from, %to, %msg)".$crlf;
                echo "                sms-message: SMS message to display before the OTP".$crlf;
                echo "             sms-originator: SMS sender (if authorized by provider)".$crlf;
                echo "               sms-password: SMS account password".$crlf;
                echo "               sms-provider: SMS provider (aspsms,clickatell,intellisms,exec)".$crlf;
                echo "                sms-userkey: SMS account username or userkey".$crlf;
                echo "                 sql-server: SQL server (FQDN or IP)".$crlf;
                echo "               sql-username: SQL username".$crlf;
                echo "               sql-password: SQL password".$crlf;
                echo "               sql-database: SQL database".$crlf;
                echo "           sql-config-table: SQL config table, default is multiotp_config".$crlf;
                echo "          sql-devices-table: SQL devices table, default is multiotp_devices".$crlf;
                echo "              sql-log-table: SQL log table, default is multiotp_log".$crlf;
                echo "           sql-tokens-table: SQL tokens table, default is multiotp_tokens".$crlf;
                echo "            sql-users-table: SQL users table, default is multiotp_users".$crlf;
                echo "   tel-default-country-code: Default country code for phone number".$crlf;
                echo " token-serial-number-length: Length of the serial number of the tokens".$crlf;
                echo "                             (used for self-registration)".$crlf;
                echo $crlf;
                echo " multiotp -initialize-backend (when all options are set, it will initialize".$crlf;
                echo "                               the backend, including creating the tables)".$crlf;
                echo $crlf;
                echo " multiotp -set user option1=value1 option2=value2 ... optionN=valueN".$crlf;
                echo "  options are  email: update the email of the user".$crlf;
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
                echo " -tag=Client-Shortname".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Client/server inline parameters:".$crlf;
                echo $crlf;
                echo " -server-cache-level=[0|1] enable/allow cache from server to client".$crlf;
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
                echo $crlf;
                echo $crlf;
                echo "Backup/restore commands:".$crlf;
                echo $crlf;
                echo " multiotp -backup-config password [file-name]".$crlf;
                echo " multiotp -restore-config password file-name".$crlf;
                echo "   By default, the file name is multiotp.cfg in the current folder.".$crlf;
                echo $crlf;
                echo $crlf;
                echo "Other information commands:".$crlf;
                echo $crlf;
                echo " multiotp -phpinfo         : print the current PHP version".$crlf;
                echo " multiotp -showlog         : print the log file".$crlf;
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
                echo " -param          All parameters are logged for debugging purposes".$crlf;
                echo " -php-version    Display the current version of the running PHP interpreter".$crlf;
                echo " -request-nt-key This will return the NT_KEY to the radius server".$crlf;
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
                echo "A ready to use binary image can be downloaded at http://download.multiotp.net/".$crlf;
                echo $crlf;
                echo "multiOTP open source is also available as a ready to use virtual appliance in".$crlf;
                echo "standard OVA, VMware optimized or Hyper-V formats.".$crlf;
                echo "Virtual appliance images can be downloaded at http://download.multiotp.net/".$crlf;
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
                echo " multiOTP Pro 501V (https://www.multiOTP.com)".$crlf;
                echo "  Pro version virtual appliance, with full web GUI, 1 free user licence".$crlf;
                echo " multiOTP Pro 420B (https://www.multiOTP.com)".$crlf;
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
                $current_attribute = trim(substr($one_radius_message, 0, mb_strpos($one_radius_message, trim($multiotp->GetRadiusReplyAttributor()))));
                foreach ($ignore_radius_array as $one_ignore_attribute) {
                    if (false !== mb_strpos(mb_strtoupper($current_attribute),mb_strtoupper($one_ignore_attribute))) {
                        $ignore_attribute = true;
                    }
                }
                if (!$ignore_attribute) {
                    $radius_additional.= $radius_separator.$one_radius_message;
                    $radius_separator = $multiotp->GetRadiusReplySeparator();
                }
            }
        }
        if ($request_nt_key) {
            $nt_key = trim($multiotp->GetNtKey());
            if ('' != $nt_key) {
                $radius_additional.= $radius_separator."NT_KEY: ".$nt_key.$crlf;
            }
        }
        if (0 < strlen($radius_additional)) {
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