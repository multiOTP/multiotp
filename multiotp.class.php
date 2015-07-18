<?php
/**
 * @file  multiotp.class.php
 * @brief Main file of the multiOTP PHP class.
 *
 * @mainpage
 *
 * multiOTP PHP class - strong two-factor authentication PHP class
 * multiOTP is OATH certified for TOTP/HOTP
 *
 * http://www.multiOTP.net/
 *
 * Visit http://forum.multiotp.net/ for additional support.
 *
 * The multiOTP package is the lightest package available that provides so many
 * strong authentication functionalities and goodies, and best of all, for anyone
 * that is interested about security issues, it's a fully open source solution!
 *
 * This package is the result of a *bunch* of work. If you are happy using this
 * package, [Donation] are always welcome to support this project.
 * Please check http://www.multiOTP.net/ and you will find the magic button ;-)
 * https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PRS3VDNYL58HJ
 *
 * If you need some specific features in the open source edition of multiOTP,
 * please contact us in order to discuss about a sponsorship in order to
 * prioritize your needs.
 *
 * The multiOTP class is a strong authentication class in pure PHP
 * that supports the following algorithms:
 *  - OATH/HOTP RFC 4226 (http://tools.ietf.org/html/rfc4226)
 *  - OATH/TOTP RFC 6238 (http://tools.ietf.org/html/rfc6238)
 *  - Google Authenticator (OATH/HOTP or OATH/TOTP, base32 seed, QRcode provisioning)
 *    (http://code.google.com/p/google-authenticator/)
 *  - Yubico OTP (http://yubico.com/yubikey)
 *  - mOTP (http://motp.sourceforge.net/)
 *  - emergency scratch passwords
 *  - SMS tokens
 *
 * This class can be used as is in your own PHP project, but it can also be
 * used easily as an external authentication provider with at least the
 * following RADIUS servers (using the multiotp command line script):
 *  - FreeRADIUS, a free RADIUS server implementation for Linux and
 *    and *nix environments (http://freeradius.org/)
 *  - WinRADIUS, the FreeRADIUS implementation ported for Windows
 *    (http://winradius.eu/)
 *  - FreeRADIUS for Windows, an other FreeRADIUS implementation ported
 *    for Windows (http://sourceforge.net/projects/freeradius/)
 *  - TekRADIUS LT, a free RADIUS server for Windows with SQLite backend
 *    (http:/www.tekradius.com/)
 *  - TekRADIUS, a free RADIUS server for Windows with MS-SQL backend
 *    (http:/www.tekradius.com/)
 *
 * This class is also used as the central component in various commercial
 * products and services developed by SysCo systemes de communication sa:
 *  - multiOTP Pro, available as a virtual appliance or a device in order
 *    to provide a complete strong authentication solution with a simple
 *    to use web based interface (http://www.multiotp.com/)
 *  - secuPASS.net, a simple service to centralize provisioning and SMS
 *    authentication for (free) Wifi hotspot (http://www.secupass.net/)
 *
 * The Readme file contains additional information.
 *
 * PHP 5.3.0 or higher is supported.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   4.3.2.6
 * @date      2015-07-18
 * @since     2010-06-08
 * @copyright (c) 2010-2015 SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2010-2015 SysCo systemes de communication sa
 *   SysCo (tm) is a trademark of SysCo systemes de communication sa
 *   (http://www.sysco.ch/)
 *   All rights reserved.
 * 
 *   This file is part of the multiOTP project.
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
 *   License along with multiOTP PHP class.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Usage
 *
 *   require_once('multiotp.class.php');
 *   $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *   // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *   // after creating the class without argument is DEPRECATED
 *   $multiotp->SetUser('user');
 *   $result = $multiotp->CheckToken('token');
 *
 *
 * Examples
 *
 *  Create a new user
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetUser('username');
 *    $multiotp->SetUserPrefixPin(0); // We don’t want the prefix PIN feature for this example
 *    $multiotp->SetUserAlgorithm('TOTP');
 *    $multiotp->SetUserTokenSeed('D6F9DF7C0110C85D6F9D');
 *    $multiotp->SetUserPin('1111'); // Useless for TOTP in this case without prefix PIN feature
 *    $multiotp->SetUserTokenNumberOfDigits(6);
 *    $multiotp->SetUserTokenTimeInterval(30);
 *    $multiotp->WriteUserData();
 *  
 *  
 *  Verify a token
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetUser('username');
 *    if (0 == $multiotp->CheckToken('token')) {
 *        // Authentication accepted
 *    } else {
 *        // Authentication rejected
 *    }
 *  
 *  
 *  Resync a user (normally only useful for HOTP, but useful too if TOTP/mOTP device or server is not well synchronized)
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetUser('username');
 *    if (0 == $multiotp->CheckToken('token1','token2')) // it must two consecutive tokens {
 *        // Synchronization successful
 *    } else {
 *        // Synchronization failed
 *    }
 *
 *
 *  Verify a token and be sure to encrypt some more data in the flat file
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetAttributesToEncrypt('*user_pin*token_seed*token_serial*seed_password*');
 *    $multiotp->SetUser('username');
 *    if (0 == $multiotp->CheckToken('token')) {
 *        // Authentication accepted
 *    } else {
 *        // Authentication rejected
 *    }
 *  
 *
 *   For examples on how to integrate it with radius servers, please have a look
 *   to the readme.txt file or read the header of the multiotp.cli.header.php file.
 *
 *
 * External files created
 *
 *   Users database files in the subfolder called users (or anywhere else if defined)
 *   Tokens database files in the subfolder called tokens (or anywhere else if defined)
 *   Log file in the subfolder called log (or anywhere else if defined)
 *   Configuration file in the subfolder called config (or anywhere else if defined)
 *
 *
 * External files needed
 *
 *   Users database files in the subfolder called users
 *   Tokens database files in the subfolder called tokens
 *
 *
 * External packages used
 *
 *   NuSOAP - PHP Web Services Toolkit 1.123 (LGPLv2.1)
 *   NuSphere Corporation
 *   http://sourceforge.net/projects/nusoap/
 *
 *   phpseclib 0.3.8 (MIT License)
 *   MMVI Jim Wigginton
 *   http://phpseclib.sourceforge.net/
 *
 *   PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY 2.1 (LGPLv2.1)
 *   Scott Barnett
 *   http://adldap.sourceforge.net/
 *
 *   PHP radius class 1.2.2 (LGPLv3)
 *   André Liechti
 *   http://developer.sysco.ch/php/
 *
 *   PHP Syslog class 1.1.2 (FREE "AS IS")
 *   André Liechti
 *   http://developer.sysco.ch/php/
 *
 *   QRcode image PHP scripts 0.50j (FREE "AS IS")
 *   Y. Swetake
 *   http://www.swetake.com/qr/index-e.html
 *
 *   status_bar.php (2010) (FREE "AS IS")
 *   dealnews.com, Inc.
 *   http://brian.moonspot.net/status_bar.php.txt
 *
 *   TCPDF 6.0.061 (LGPLv3)
 *   Nicola Asuni
 *   http://www.tcpdf.org/
 *
 *   XML Parser Class 1.3.0 (LGPLv3)
 *   Adam A. Flynn
 *   http://www.criticaldevelopment.net/xml/
 *
 *   XPertMailer package 4.0.5 (LGPLv2.1)
 *   Tanase Laurentiu Iulian
 *   http://xpertmailer.sourceforge.net/
 *
 *
 * Special issues
 *
 *   If you need specific developements concerning strong authentication,
 *   do not hesistate to contact us per email at info@multiotp.net.
 *
 *
 * Other related ressources
 *
 *   Mobile-OTP: Strong Two-Factor Authentication with Mobile Phones:
 *     http://motp.sourceforge.net/
 *
 *   The Initiative for Open Authentication:
 *     http://www.openauthentication.org/
 *
 *   TekRADIUS, a free RADIUS server for windows, available in two versions (MS-SQL and SQLite):
 *     http://www.tekradius.com/
 *
 *   FreeRADIUS, a free Radius server implementation for Linux and *nix environments:
 *     http://www.freeradius.org/
 *
 *   WinRADIUS, the FreeRADIUS implementation ported for Windows
 *     (http://winradius.eu/)
 *
 *   FreeRADIUS for Windows, an other freeRADIUS server implementation ported
 *     for Windows (http://sourceforge.net/projects/freeradius/)
 *
 *   Additional Portable Symmetric Key Container (PSKC) Algorithm Profiles
 *     RFC 6030 (http://tools.ietf.org/html/rfc6030)
 *
 *   Google Authenticator (based on OATH/TOTP)
 *     http://code.google.com/p/google-authenticator/
 *
 *
 * Users feedbacks and comments
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
 * Todos
 *
 *   Add more comments in the main class file
 *
 *
 * Change Log
 *
 *   2015-07-18 4.3.2.6 SysCo/al New ResetTempUserArray method (as we want to move away from global array in the near future)
 *                               For _user_data, default values are now extracted from the definition array
 *                               QRcode generation for mOTP (motp://[SITENAME]:[USERNAME]?secret=[SECRET-KEY])
 *   2015-07-15 4.3.2.5 SysCo/al Calling multiotp CLI without parameter returns now error code 30 (instead of 19)
 *   2015-06-24 4.3.2.4 SysCo/al multi_account automatic support
 *                               Scratch password generation (UTF)
 *   2015-06-10 4.3.2.3 SysCo/al Enhancements for the Dev(Talks): demo
 *   2015-06-09 4.3.2.2 SysCo/al Empty users are refused
 *                               TOTP time interval of imported tokens is set by default to 30s
 *                               More accuracy in the logged information
 *                               Refactoring backend methods, sharing code
 *                               Refactoring some ugly parts (!)
 *                               Documentation update concerning lockout functions and prefix PIN prefix
 *                               Special token entry 'Sms' is now also accepted, like 'SMS' or 'sms', to send an SMS token
 *                               The minus (-) in the prefix password is now supported (it was filtered to fix some rare user issues)
 *                               The autoresync option is now enabled by default
 *                               Resync during authentication (autoresync) is now better handled in the class directly
 *                               The server_cache_level is now set to 1 by default (instead of 0)
 *                               If the token length is not correct, it's now written in the log
 *                               Some LDAP messages are now only logged in debug mode
 *   2014-12-15 4.3.1.1 SysCo/al Better generic LDAP support
 *                                 - description sync done in the following order: description, gecos, displayName
 *                                 - memberOf is not always implemented, alternative method to sync users based on group names.
 *                                 - disabled account synchronization using shadowExpire or sambaAcctFlags
 *                               Better Active Directory support
 *                                 - accountExpires is now supported for synchronization
 *                                 - ms-DS-User-Account-Control-Computed (to handle locked out accounts, available since Windows 2003)
 *   2014-12-09 4.3.1.0 SysCo/al MULTIOTP_PATH environment variable support
 *                               CLI proxy added to speed up the command line
 *                               Scratch password need also the prefix PIN if it's activated
 *                               OTP with integrated serial numbers better supported (in PAP)
 *                               Generic LDAP support (instead of Microsoft AD support only)
 *                               Raspberry Pi edition has now a special proxy to speed up the command line
 *   2014-11-04 4.3.0.0 SysCo/al It's now possible to use the AD/LDAP password instead of the PIN code
 *                               Yubico OTP support, including keys import using the log file in Traditional format
 *                               qrcode() stub enhanced to check if the required folders are available
 *                               SyncLdapUsers completely redesigned
 *                                 - no more complete array in memory
 *                                 - MultiotpAdLdap class also enhanced accordingly
 *                                   - cached group_cn requests
 *                                   - cached recursive_groups requests
 *                                   - new "by element" functions
 *                               Demo mode support
 *                               Bug fix concerning the NT_KEY generation with enabled prefix PIN (thanks Adam)
 *                               ResyncToken() method added (instead of using CheckToken() method for synchronization)
 *   2014-06-12 4.2.4.3 SysCo/al Bug fix concerning aspsms provider
 *   2014-04-13 4.2.4.2 SysCo/al XML parsing consolidation, one library for the whole project
 *                               Fixed bug concerning tokens CSV import
 *   2014-04-06 4.2.4.1 SysCo/al Fixed bug concerning LDAP handling
 *                               NT_KEY support added (for FreeRADIUS further handling)
 *                               Tokens CSV import (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
 *                               When a user is deleted, the token(s) attributed to this user is/are unassigned
 *                               New option -user-info added
 *   2014-03-30 4.2.4   SysCo/al Fixed bug concerning MySQL handling and mysqli support added
 *                               Enhanced SetAttributesToEncrypt function
 *                               New implementation fo some external classes
 *                               Generated QRcode are better
 *                               LOT of new QA tests, more than 60 different tests (including PHP class and command line versions)
 *                               Enhanced documentation
 *   2014-03-13 4.2.3   SysCo/al Fixed bug for clear text password going back to TekRADIUS (PIN was always prefixed for mOTP)
 *                               Fixed bug when client/server mode is activated, but not working well
 *   2014-03-03 4.2.2   SysCo/al Better AD/LDAP integration
 *                               Web GUI is now complete for a simple usage, including hardware tokens import
 *                               Better template for provisioning information
 *                               Some values can now go back to TekRADIUS
 *                               If activated, prefix PIN is now also requested for SMS authentication
 *                               More information in the logs
 *                               Better list of the external packages used
 *   2014-02-14 4.2.1   SysCo/al AD/LDAP is now fully supported in order to create users based on AD/LDAP content
 *                                (with groups filtering)
 *   2014-02-07 4.2.0   SysCo/al MS-CHAP and MS-CHAPv2 are now supported
 *                                (md4 implementation added for PHP backward compatibility)
 *                               Enhanced LDAP configuration structure
 *                               Fixed bug during token attribution to users
 *                                (a "no name" token appeared sometimes)
 *   2014-01-20 4.1.1   SysCo/al md5.js was missing in the public distribution
 *                               Alternate json_encode function is defined if the JSON extension is not loaded
 *                               Fixed possible image functions incompatibilities with some PHP versions
 *                                during QRcode generation
 *                               As suggested by Sylvain, token resync doesn't need prefix PIN anymore
 *                                (but still accepted)
 *                               More verbosity in the logs in debug mode
 *                               Specific parameters order in QRCode for Microsoft Authenticator support
 *                                (thanks to Erik Nylund)
 *   2013-12-23 4.1.0   SysCo/al The open source edition of multiOTP is OATH certified ;-)
 *                                (that means full compatibility with any OATH tokens and encrypted PSKC import support)
 *                               Raspberry Pi nanocomputer is now fully supported
 *                               Basic web interface
 *                               Self-registration of hardware tokens is now possible
 *                                PAP mode: if self-registration is enabled, a user can register a non-attributed token by typing
 *                                [serial number][OTP] instead of [OTP]. If user has a prefix PIN, type [serial number][PIN][OTP])
 *                                PAP/CHAP mode: if self-registration is enabled, a user can register a non-attributed token by typing
 *                                [username:serialnumber] as the username and the [OTP] in the password field.
 *                                If user has a prefix PIN, [PIN][OTP] must be typed in the password field
 *                               Automatic resync/unlock option during authentication (PAP only). When the autoresync option
 *                                is enabled, any user can resync his token by typing [OTP1] [OTP2] in the password field. 
 *                                If user has a prefix PIN, he must type [PIN][OTP1] [PIN][OTP2].
 *                               Tokens with less than 3 characters are not accepted anymore in CheckToken()
 *                               Default Linux file mode is now set by default (0666 for created and changed files)
 *                               Error 28 is returned if the file is not writable, even after a successful login
 *                               Added GetUsersCount() function
 *                               Added GenerateSmsToken() function
 *                               Added Groups management functions
 *                               Added Tokens assignation functions
 *                               Added SetUserActivated(1|0) and GetUserActivated() function
 *                               Added SetUserSynchronized(1|0) and GetUserSynchronized() function
 *                               scratch_passwords is now a text field in the database
 *                               The third parameter of the Decrypt method is now mandatory
 *                               Some modifications in order to correctly handle the class methods
 *   2013-09-22 4.0.9   SysCo/al Fixed a bug in GetUserScratchPasswordsArray. If a user had no scratch password
 *                                and the implementation accepted blank password, it was accepted
 *                               Fixed a bug where scratch passwords generation used odd numbers of characters for hex2bin()
 *   2013-08-30 4.0.7   SysCo/al GetScriptFolder() was still buggy sometimes, thanks Frank for the feedback
 *                               File mode of the created QRcode file is also changed based on GetLinuxFileMode()
 *                               'sms' as the password to request an SMS token can now be sent in lower or uppercase
 *                               Added a description attribute for the tokens
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
 *                               The method SetEncryptionKey('MyPersonalEncryptionKey') is DEPRECATED
 *                               The method DefineMySqlConnection is DEPRECATED
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
 *   2011-10-25 3.9.2   SysCo/al Some quick fixes after intensive check
 *                               Improved get_script_dir() in CLI for Linux/Windows compatibility
 *   2011-09-15 3.9.1   SysCo/al Some quick fixes concerning multiple users
 *   2011-09-13 3.9.0   SysCo/al Added support for account with multiple users
 *   2011-07-06 3.2.0   SysCo/al Encryption hash handling with additional error message 33
 *                                (if the key has changed)
 *                               Added more examples
 *                               Added generic user with multiple account
 *                                (Real account name is combined: "user" and "account password")
 *                               Added log options, now default doesn't log token value anymore
 *                               Debugging MySQL backend support for the token handling
 *                               Fixed automatic detection of \ or / for script path detection
 *   2010-12-19 3.1.1   SysCo/al Better MySQL backend support, including in CLI version
 *   2010-09-15 3.1.0   SysCo/al Removed bad extra spaces in the multiotp.php file for Linux
 *                               MySQL backend support
 *   2010-09-02 3.0.0   SysCo/al Added tokens handling support
 *                                including importing XML tokens definition file
 *                                (http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00)
 *                               Enhanced flat database file format (multiotp is still compatible with old versions)
 *                               Internal method SetDataReadFlag renamed to SetUserDataReadFlag
 *                               Internal method GetDataReadFlag renamed to GetUserDataReadFlag
 *   2010-08-21 2.0.4   SysCo/al Enhancement in order to use an alternate php "compiler" for Windows command line
 *                               Documentation enhancement
 *   2010-08-18 2.0.3   SysCo/al Minor notice fix
 *   2010-07-21 2.0.2   SysCo/al Fix to create correctly the folders "users" and "log" if needed
 *   2010-07-19 2.0.1   SysCo/al Foreach was not working well in PHP4, replaced at some places
 *   2010-07-19 2.0.0   SysCo/al New design using a class, mOTP support, cleaning of the code
 *   2010-06-15 1.1.5   SysCo/al Added OATH/TOTP support
 *   2010-06-15 1.1.4   SysCo/al Project renamed to multiotp to avoid overlapping
 *   2010-06-08 1.1.3   SysCo/al Typo in script folder detection
 *   2010-06-08 1.1.2   SysCo/al Typo in variable name
 *   2010-06-08 1.1.1   SysCo/al Status bar during resynchronization
 *   2010-06-08 1.1.0   SysCo/al Fix in the example, distribution not compressed
 *   2010-06-07 1.0.0   SysCo/al Initial implementation
 *********************************************************************/

class Multiotp
/**
 * @class     Multiotp
 * @brief     Main class definition of the multiOTP project.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   4.3.2.6
 * @date      2015-07-18
 * @since     2010-07-18
 */
{
    var $_version;                  // Current version of the library
    var $_date;                     // Current date of the library
    var $_copyright;                // Copyright message of the library, don't change it !
    var $_website;                  // Website dedicated to this LGPL library, please don't change it !

    var $_base_dir;                 // Specific base directory
    var $_valid_algorithms;         // String containing valid algorithms to be used, separated by *, like *mOTP*HOTP*TOTP*YubicoOTP*
    var $_attributes_to_encrypt;    // Attributes to encrypt in the flat files
    var $_encryption_key;           // Symetric encryption key for the users files and the tokens files
    var $_source_tag;               // Source tag of the request (for a shared installation for example)
    var $_source_ip;                // Source IP of the request (for a RADIUS request for example, Packet-Src-IP-Address)
    var $_source_mac;               // Source MAC of the request (for a RADIUS request for example, Called-Station-Id)
    var $_calling_ip;               // Source IP of the request (for a RADIUS request for example, Framed-IP-Address)
    var $_calling_mac;              // Source MAC of the request (for a RADIUS request for example, Calling-Station-Id)
    var $_chap_challenge;           // CHAP-Challenge (instead of traditional PAP password)
    var $_chap_id;                  // CHAP-Id (instead of traditional PAP password)
    var $_chap_password;            // CHAP-Password (instead of traditional PAP password)
    var $_ms_chap_challenge;        // MS-CHAP challenge
    var $_ms_chap_response;         // MS-CHAP response
    var $_ms_chap2_response;        // MS-CHAP2 response
    var $_ms_nt_key;                // NTLM NT key
    var $_errors_text;              // An array containing errors text description
    var $_config_data;              // An array with all the general config related info
    var $_config_folder;            // Folder where the general config file is written
    var $_device;                   // Current device
    var $_device_data;              // An array with all the device related info
    var $_group;                    // Current group
    var $_group_data;               // An array with all the group related info
    var $_user;                     // Current user, case insensitive
    var $_user_data;                // An array with all the user related info
    var $_user_data_read_flag;      // Indicate if the user data has been read from the database file
    var $_users_folder;             // Folder where users definition files are stored
    var $_qrcode_folder;            // Folder where qrcode files are stored
    var $_templates_folder;         // Folder where template files are stored
    var $_devices_folder;           // Folder where devices definition files are stored
    var $_groups_folder;            // Folder where groups definition files are stored
    var $_token;                    // Current token, case insensitive
    var $_token_data;               // An array with all the token related info
    var $_token_data_read_flag;     // Indicate if the token data has been read from the database file
    var $_tokens_folder;            // Folder where tokens definition files are stored
    var $_log_folder;               // Folder where log file is written
    var $_log_file_name;            // Name of the log file
    var $_log_flag;                 // Enable or disable the log
    var $_log_header_written;       // Internal flag to know if the header was already written or not in the log file
    var $_log_verbose_flag;         // Enable or disable the verbose mode for the log
    var $_log_display_flag;         // Log will also be displayed on the console
    var $_last_imported_tokens;     // An array containing the names (which are mostly the serials) of the last imported tokens
    var $_reply_array_for_radius;   // Specific reply message(s) for the radius (to be displayed in all cases by the command line tool)
    var $_initialize_backend;       // Initialize backend flag
    var $_debug_via_html;           // Set the debug output to HTML standard
    var $_linux_file_mode;          // File mode of the created linux files in octal (for example '0644')
    var $_server_challenge;         // Server challenge for client-server mutual authentication
    var $_xml_dump_in_log;          // For internal debugging only
    var $_servers_temp_bad_list;    // Temporary list of servers that are not currently responding well
    var $_test_server_secret;       // Temporary server secret for tests
    var $_last_clear_otp_value;     // Last clear OTP value (including the prefix if typed)
    var $_last_ldap_error;          // Last LDAP/AD error (boolean)
    var $_parser_pointers;          // An array of pointers used for iterative parsing
    var $_cache_folder;             // Folder where the cache file is written
    var $_ldap_server_reachable;    // Flag to know if the LDAP server is reachable
    var $_sql_tables;               // Array of alias names for SQL tables    


    function Multiotp(
        $encryption_key = "",
        $initialize_backend = false,
        $base_dir = "",
        $config_dir = ""
    )
    /**
     * @brief   Class constructor.
     *
     * @param   string  $encryption_key      A specific encryption key to encrypt stored data instead of the default one.
     * @param   boolean $initialize_backend  If we initialize the backend, we don't want to write in the database before the end of the initialization.
     * @param   boolean $base_dir            Define the base directory, which is always better than automatic detection.
     * @param   boolean $config_dir          Define the config directory, which is always better than automatic detection.
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.3.2.6
     * @date    2015-07-18
     * @since   2010-07-18
     */
    {
        // destructor definition (for PHP 4 compatibility)
        if (!version_compare(phpversion(), '5', '>='))
        {
            register_shutdown_function(array(&$this, '__destruct'));
        }

        // Ignore the LDAP certificate validity (for Windows only)
        putenv('LDAPTLS_REQCERT=never');


        $this->_class = base64_decode('bXVsdGlPVFA=');
        $this->_version = '4.3.2.6'; // You should add a suffix for your changes (for example 4.3.2.2-andy-2015-06-09)
        $this->_date = '2015-07-18'; // You should add a suffix for your changes (for example YYYY-MM-DD / YYY2-M2-XX)
        $this->_copyright = base64_decode('KGMpIDIwMTAtMjAxNSBTeXNDbyBzeXN0ZW1lcyBkZSBjb21tdW5pY2F0aW9uIHNh');
        $this->_website = base64_decode('aHR0cDovL3d3dy5tdWx0aU9UUC5uZXQ=');
        
        $this->_log_header_written    = FALSE; // Flag indicating if the header has already been written in the log file or not
        $this->_valid_algorithms      = '*mOTP*HOTP*TOTP*YubicoOTP*'; // Supported algorithms, don't change it (unless you have added the handling of a new algorithm ;-)
        $this->_attributes_to_encrypt = '*admin_password_hash*challenge*device_secret*ldap_hash_cache*ldap_server_password*scratch_passwords*seed_password*server_secret*sms_api_id*sms_otp*sms_password*sms_userkey*smtp_password*sql_password*token_seed*user_pin*'; // This default list of attributes can be changed using SetAttributesToEncrypt(). Each attribute must be between "*".
        
        $this->_ldap_server_reachable = FALSE;
        
        $this->_sql_tables = array('cache',
                                   'config',
                                   'devices',
                                   'groups',
                                   'log',
                                   'tokens',
                                   'users'
                                  );
        
        $this->_sql_tables_schema['cache']   = array(
            'active_users_count'      => "int(10) DEFAULT -1",
            'last_update'             => "int(10) DEFAULT 0",
            'locked_users_count'      => "int(10) DEFAULT -1",
            'locked_users_list'       => "int(10) DEFAULT -1",
            'users_count'             => "int(10) DEFAULT -1");
        $this->_sql_tables_index['cache']    = '**';
        $this->_sql_tables_ignore['cache']   = "**";
        
        $this->_sql_tables_schema['config']  = array(
            'actual_version'             => "varchar(255) DEFAULT ''",
            'admin_password_hash'        => "varchar(255) DEFAULT ''",
            'attributes_to_encrypt'      => "varchar(255) DEFAULT ''",
            // Backend storage type (files / mysql)
            'backend_type'               => "varchar(255) DEFAULT 'files'",
            // By default, backend_type is not validated
            'backend_type_validated'     => "int(10) DEFAULT 0",
            'auto_resync'                => "int(1) DEFAULT 1",
            'cache_data'                 => "int(1) DEFAULT 0",
            'cache_ldap_hash'            => "int(1) DEFAULT 1",
            'case_sensitive_users'       => "int(1) DEFAULT 0",
            'clear_otp_attribute'        => "varchar(255) DEFAULT ''",
            // No console authentication by default
            'console_authentication'     => "int(10) DEFAULT 0",
            // Debug mode (to enable it permanently)
            'debug'                      => "int(10) DEFAULT 0",
            'default_user_group'         => "varchar(255) DEFAULT ''",
            'default_request_ldap_pwd'   => "int(10) DEFAULT 1",
            'default_request_prefix_pin' => "int(10) DEFAULT 1",
            'demo_mode'                  => "int(10) DEFAULT 0",
            // Display log mode (to enable it permanently)
            'display_log'                => "int(10) DEFAULT 0",
            'domain_name'                => "varchar(255) DEFAULT ''",
            'email_admin_address'        => "varchar(255) DEFAULT ''",
            'encryption_key_full_path'   => "varchar(255) DEFAULT ''",
            // Locking delay in seconds between two trials after "max_delayed_failures" failures
            'failure_delayed_time'       => "int(10) DEFAULT 300",
            'group_attribute'            => "varchar(255) DEFAULT 'Filter-Id'",
            'hash_salt_full_path'        => "varchar(255) DEFAULT ''",
            'issuer'                     => "varchar(255) DEFAULT 'multiOTP'",
            'last_update'                => "int(10) DEFAULT 0",
            'ldap_account_suffix'        => "varchar(255) DEFAULT ''",
            'ldap_activated'             => "int(1) DEFAULT 0",
            'ldap_base_dn'               => "varchar(255) DEFAULT ''",
            'ldap_bind_dn'               => "varchar(255) DEFAULT ''",
            'ldap_cn_identifier'         => "varchar(255) DEFAULT 'sAMAccountName'",
            'ldap_domain_controllers'    => "varchar(255) DEFAULT ''",
            'ldap_group_attribute'       => "varchar(255) DEFAULT 'memberOf'",
            'ldap_group_cn_identifier'   => "varchar(255) DEFAULT 'sAMAccountName'",
            // Hash cache time: 7 * 24 * 60 * 60 = 604800 = 1 week
            'ldap_hash_cache_time'       => "int(10) DEFAULT 604800",
            'ldap_in_group'              => "varchar(255) DEFAULT ''",
            'ldap_network_timeout'       => "int(10) DEFAULT 10",
            'ldap_port'                  => "varchar(255) DEFAULT '389'",
            'ldap_server_password'       => "varchar(255) DEFAULT ''",
            // Default type 1 is Active Directory, 2 for Generic LDAP
            'ldap_server_type'           => "int(10) DEFAULT 1",
            'ldap_ssl'                   => "int(1) DEFAULT 0",
            'ldap_time_limit'            => "int(10) DEFAULT 30",
            'log'                        => "int(10) DEFAULT 0",
            'max_block_failures'         => "int(10) DEFAULT 6",
            'max_delayed_failures'       => "int(10) DEFAULT 3",
            'max_event_resync_window'    => "int(10) DEFAULT 10000",
            'max_event_window'           => "int(10) DEFAULT 100",
            'max_time_resync_window'     => "int(10) DEFAULT 90000",
            // Maximum time window to be accepted, in seconds (+/-)
            // Initialized to a little bit more than +/- 10 minutes
            // (was 8000 seconds in version 3.x, and Stefan Kügler suggested to put a lower default value)
            'max_time_window'            => "int(10) DEFAULT 600",
            'ntp_server'                 => "varchar(255) DEFAULT 'pool.ntp.org'",
            'radius_reply_attributor'    => "varchar(255) DEFAULT ' = '",
            'radius_reply_separator_hex' => "varchar(255) DEFAULT '".bin2hex(',')."'",
            'scratch_passwords_digits'   => "int(10) DEFAULT 6",
            'scratch_passwords_amount'   => "int(10) DEFAULT 10",
            'self_registration'          => "int(1) DEFAULT 1",
            // Client-server configuration
            'server_cache_level'         => "int(10) DEFAULT 1",
            // 1552000 = 6 monthes
            'server_cache_lifetime'      => "int(10) DEFAULT 15552000",
            'server_secret'              => "varchar(255) DEFAULT 'ClientServerSecret'",
            'server_timeout'             => "int(10) DEFAULT 5",
            'server_type'                => "varchar(255) DEFAULT 'xml'",
            'server_url'                 => "varchar(255) DEFAULT ''",
            'sms_api_id'                 => "varchar(255) DEFAULT ''",
            'sms_message_prefix'         => "varchar(255) DEFAULT '%s is your SMS-Code'",
            'sms_originator'             => "varchar(255) DEFAULT 'multiOTP'",
            'sms_password'               => "varchar(255) DEFAULT ''",
            'sms_provider'               => "varchar(255) DEFAULT ''",
            'sms_userkey'                => "varchar(255) DEFAULT ''",
            'sms_digits'                 => "int(10) DEFAULT 6",
            // SMS timeout before authenticating (in seconds)
            'sms_timeout'                => "int(10) DEFAULT 180",
            'smtp_auth'                  => "int(10) DEFAULT 0",
            'smtp_password'              => "varchar(255) DEFAULT ''",
            'smtp_port'                  => "int(10) DEFAULT 25",
            'smtp_sender'                => "varchar(255) DEFAULT ''",
            'smtp_sender_name'           => "varchar(255) DEFAULT ''",
            'smtp_server'                => "varchar(255) DEFAULT ''",
            'smtp_ssl'                   => "int(10) DEFAULT 0",
            'smtp_username'              => "varchar(255) DEFAULT ''",
            'sql_server'                 => "varchar(255) DEFAULT ''",
            'sql_username'               => "varchar(255) DEFAULT ''",
            'sql_password'               => "varchar(255) DEFAULT ''",
            'sql_database'               => "varchar(255) DEFAULT ''",
            // Default SQL table names. If empty, the related data will be written to a file.
            'sql_config_table'           => "varchar(255) DEFAULT 'multiotp_config'",
            'sql_cache_table'            => "varchar(255) DEFAULT 'multiotp_cache'",
            'sql_devices_table'          => "varchar(255) DEFAULT 'multiotp_devices'",
            'sql_groups_table'           => "varchar(255) DEFAULT 'multiotp_groups'",
            'sql_log_table'              => "varchar(255) DEFAULT 'multiotp_log'",
            'sql_tokens_table'           => "varchar(255) DEFAULT 'multiotp_tokens'",
            'sql_users_table'            => "varchar(255) DEFAULT 'multiotp_users'",
            'syslog_facility'            => "int(10) DEFAULT 7",
            'syslog_level'               => "int(10) DEFAULT 5",
            'syslog_port'                => "int(10) DEFAULT 514",
            'syslog_server'              => "varchar(255) DEFAULT ''",
            'tel_default_country_code'   => "varchar(255) DEFAULT ''",
            'timezone'                   => "varchar(255) DEFAULT 'Europe/Zurich'",
            'token_serial_number_length' => "varchar(255) DEFAULT '12'",
            'token_otp_list_of_length'   => "varchar(255) DEFAULT '6'",
            'verbose_log_prefix'         => "varchar(255) DEFAULT ''",
            'encryption_hash'            => "varchar(255) DEFAULT ''");
        $this->_sql_tables_index['config']   = '**';
        $this->_sql_tables_ignore['config']  = '*backend_type*backend_type_validated*sql_server*sql_username*sql_password*sql_database*sql_config_table*';

        
        $this->_sql_tables_schema['devices'] = array(
            'device_id'                  => "varchar(255) DEFAULT ''",
            'challenge_response_enabled' => "int(1) DEFAULT 0",
            'description'                => "varchar(255) DEFAULT ''",
            'device_group'               => "varchar(255) DEFAULT ''",
            'device_secret'              => "varchar(255) DEFAULT ''",
            'ip_or_fqdn'                 => "varchar(255) DEFAULT ''",
            'last_update'                => "int(10) DEFAULT 0",
            'shortname'                  => "varchar(255) DEFAULT ''",
            'sms_challenge_enabled'      => "int(1) DEFAULT 0",
            'subnet'                     => "varchar(255) DEFAULT ''",
            'text_sms_challenge'         => "varchar(255) DEFAULT 'Please enter the code received on your mobile phone'",
            'text_token_challenge'       => "varchar(255) DEFAULT 'Please enter the code displayed on the token'",
            'encryption_hash'            => "varchar(255) DEFAULT ''");
        $this->_sql_tables_index['devices']  = '*device_id*ip_or_fqdn*shortname*';
        $this->_sql_tables_ignore['devices'] = "**";

        $this->_sql_tables_schema['groups']  = array(
            'group_id'                => "varchar(255) DEFAULT ''",
            'description'             => "varchar(255) DEFAULT ''",
            'name'                    => "varchar(255) DEFAULT ''",
            'last_update'             => "int(10) DEFAULT 0",
            'encryption_hash'         => "varchar(255) DEFAULT ''");
        $this->_sql_tables_index['groups']   = '*group_id*name*';
        $this->_sql_tables_ignore['groups'] = "**";

        $this->_sql_tables_schema['log']     = array(
            'category'                => "varchar(100) DEFAULT ''",
            'datetime'                => "datetime DEFAULT NULL",
            'destination'             => "varchar(100) DEFAULT ''",
            'last_update'             => "int(10) DEFAULT 0",
            'logentry'                => "text",
            'note'                    => "varchar(255) DEFAULT ''",
            'severity'                => "varchar(100) DEFAULT ''",
            'source'                  => "varchar(100) DEFAULT ''",
            'user'                    => "varchar(255) DEFAULT ''");
        $this->_sql_tables_index['log']      = '*datetime*';
        $this->_sql_tables_ignore['log']     = "**";

        $this->_sql_tables_schema['tokens']  = array(
            'algorithm'               => "varchar(255) DEFAULT ''",
            'attributed_users'        => "varchar(255) DEFAULT ''",
            'delta_time'              => "int(10) DEFAULT 0",
            'description'             => "varchar(255) DEFAULT ''",
            'error_counter'           => "int(10) DEFAULT 0",
            'format'                  => "varchar(255) DEFAULT ''",
            'issuer'                  => "varchar(255) DEFAULT ''",
            'key_algorithm'           => "varchar(255) DEFAULT ''",
            'last_error'              => "int(10) DEFAULT 0",
            'last_event'              => "int(10) DEFAULT -1",
            'last_login'              => "int(10) DEFAULT 0",
            'last_update'             => "int(10) DEFAULT 0",
            'locked'                  => "int(1) DEFAULT 0",
            'manufacturer'            => "varchar(255) DEFAULT 'multiOTP'",
            'number_of_digits'        => "int(10) DEFAULT 6",
            'otp'                     => "varchar(255) DEFAULT ''",
            'time_interval'           => "int(10) DEFAULT 0",
            'token_algo_suite'        => "varchar(255) DEFAULT ''",
            'token_id'                => "varchar(255) DEFAULT ''",
            // Token seed, default set to the RFC test seed, hexadecimal coded
            'token_seed'              => "varchar(255) DEFAULT '3132333435363738393031323334353637383930'",
            'token_serial'            => "varchar(255) DEFAULT ''",
            'encryption_hash'         => "varchar(255) DEFAULT ''");
        $this->_sql_tables_index['tokens']   = '*attributed_users*token_id*token_serial*';
        $this->_sql_tables_ignore['tokens']  = "**";

        $this->_sql_tables_schema['users']   = array(
            'algorithm'               => "varchar(255) DEFAULT ''",
            // Autolock time (for cached data)
            'autolock_time'           => "int(10) DEFAULT 0",
            // Challenge initialization
            'challenge'               => "varchar(255) DEFAULT ''",
            'challenge_validity'      => "int(10) DEFAULT 0",
            // Delta time in seconds for a time based token
            'delta_time'              => "int(10) DEFAULT 0",
            // Desactivated user info
            'desactivated'            => "int(1) DEFAULT 0",
            'description'             => "varchar(255) DEFAULT ''",
            'email'                   => "varchar(255) DEFAULT ''",
            // Login error counter
            'error_counter'           => "int(10) DEFAULT 0",
            'group'                   => "varchar(255) DEFAULT ''",
            'key_id'                  => "varchar(255) DEFAULT ''",
            // Last error login
            'last_error'              => "int(10) DEFAULT 0",
            // Last successful event
            'last_event'              => "int(10) DEFAULT -1",
            // Last successful login
            'last_login'              => "int(10) DEFAULT 0",
            'last_update'             => "int(10) DEFAULT 0",
            // LDAP password hash caching mechanism
            'ldap_hash_cache'         => "varchar(255) DEFAULT ''",
            'ldap_hash_validity'      => "int(10) DEFAULT 0",
            // Token locked
            'locked'                  => "int(1) DEFAULT 0",
            // User is a special multi-account user (the real user is in the token, like this: "user[space]token"
            'multi_account'           => "int(10) DEFAULT 0",
            // Number of digits returned by the token
            'number_of_digits'        => "int(10) DEFAULT 6",
            // Request the LDAP password as a prefix of the returned token value
            'request_ldap_pwd'        => "int(10) DEFAULT 0",
            'request_prefix_pin'      => "int(10) DEFAULT 0",
            'scratch_passwords'       => "text",
            'seed_password'           => "varchar(255) DEFAULT ''",
            'sms'                     => "varchar(255) DEFAULT ''",
            'sms_otp'                 => "varchar(255) DEFAULT ''",
            // User sms otp validity
            'sms_validity'            => "int(10) DEFAULT 0",
            // Synchronized user info
            'synchronized'            => "int(1) DEFAULT 0",
            'synchronized_channel'    => "varchar(255) DEFAULT ''",
            'synchronized_dn'         => "varchar(255) DEFAULT ''",
            'synchronized_server'     => "varchar(255) DEFAULT ''",
            'synchronized_time'       => "int(10) DEFAULT 0",
            // Time interval in seconds for a time based token
            'time_interval'           => "int(10) DEFAULT 0",
            'token_algo_suite'        => "varchar(255) DEFAULT ''",
            // Token seed, default set to the RFC test seed, hexadecimal coded
            'token_seed'              => "varchar(255) DEFAULT '3132333435363738393031323334353637383930'",
            'token_serial'            => "varchar(255) DEFAULT ''",
            'user'                    => "varchar(255) DEFAULT ''",
            'user_pin'                => "varchar(255) DEFAULT ''",
            'encryption_hash'         => "varchar(255) DEFAULT ''");
        $this->_sql_tables_index['users']    = '*desactivated*locked*user*';
        $this->_sql_tables_ignore['users']   = "**";

        if ("" == $encryption_key)
        {
            $this->_encryption_key = 'MuLtIoTpEnCrYpTiOn'; // This default value should be changed for each project using SetEncryptionKey()
        }
        else
        {
            $this->_encryption_key = $encryption_key;
        }

        $current_dir = $base_dir;
        if ("" == $current_dir)
        {
            $env_folder_path = getenv('MULTIOTP_PATH');
            if (FALSE !== $env_folder_path)
            {
                $current_dir = $env_folder_path;
            }
        }
        $this->SetBaseDir($current_dir);

        if ("" != trim($config_dir))
        {
            $this->SetConfigFolder($config_dir, TRUE, FALSE);
        }
        
        $this->_parser_pointers         = array();
        
        $this->_hash_salt               = 'MySalt'; // Can be Set in your application using SetHashSalt()
        $this->_random_salt             = "Random"; // Updated regulary with SetRandomSalt

        $this->_source_tag              = "";

        $this->_source_ip               = "";
        $this->_source_mac              = "";
        
        $this->_calling_ip              = "";
        $this->_calling_mac             = "";

        $this->_chap_challenge          = "";
        $this->_chap_id                 = "";
        $this->_chap_password           = "";
        
        $this->_ms_nt_key               = "";
        
        $this->_encryption_check        = TRUE; // Check if the encryption hash is valid, default is TRUE

        $this->_user                    = ""; // Name of the current user to authenticate
        $this->_user_data_read_flag     = FALSE; // Flag to know if the data concerning the current user has been read
        $this->_users_folder            = ""; // Folders which contain the users flat files

        $this->_last_ldap_error         = FALSE;
        $this->_log_file_name           = 'multiotp.log';
        $this->_log_flag                = FALSE;
        $this->_log_folder              = ""; // Folder which contains the log file
        $this->_log_verbose_flag        = FALSE;
        $this->_log_display_flag        = FALSE;
        
        $this->_mysql_database_link     = NULL;
        $this->_mysqli                  = NULL;
        
        $this->_migration_from_file     = FALSE; // To allow an automatic migration of users profiles,
                                                 // enable a database backend and set the migration option ;-) !

        $this->_reply_array_for_radius = array();
        
        $this->_servers_temp_bad_list  = array();

        $this->_initialize_backend = $initialize_backend;
        
        $this->_debug_via_html = FALSE;
        
        $this->_linux_file_mode = "";


        $this->ReadConfigData(true); // Read the configuration data, for the encryption information only
        if (("" == $encryption_key) || ('MuLtIoTpEnCrYpTiOn' == $encryption_key) || ('DefaultCliEncryptionKey' == $encryption_key))
        {
            if (("" != $this->GetEncryptionKeyFullPath()) && file_exists($this->GetEncryptionKeyFullPath()))
            {
                if ($encryption_key_file_handler = fopen($this->GetEncryptionKeyFullPath(), "rt"))
                {
                    $temp_encryption_key = trim(fgets($encryption_key_file_handler));
                    if ("" != $temp_encryption_key)
                    {
                        $this->SetEncryptionKey($temp_encryption_key, FALSE);
                    }
                    fclose($encryption_key_file_handler);
                }
            }
        }

        $this->_server_challenge = $this->GetEncryptionKey();

        $this->_keep_local = FALSE;
        
        $this->_xml_dump_in_log = FALSE; // For debugging purpose only
        
        $this->_sms_providers_array = array(array("aspsms", "aspsms", "http://www.aspsms.com/"),
                                            array("clickatell", "Clickatell", "http://www.clickatell.com/"),
                                            array("intellisms", "IntelliSMS", "http://www.intellisms.co.uk/")
                                           );

        // As various accounts are using the same files
        $this->SetLinuxFileMode('0666');
        
        // Reset/initialize the errors text array, should be the first reset method to call
        $this->ResetErrorsArray();

        // Reset/initialize the config array, should be the second reset method to call
        $this->ResetConfigArray();
        
        // Reset/initialize the device array
        $this->ResetDeviceArray();

        // Reset/initialize the group array
        $this->ResetGroupArray();

        // Reset/initialize the user array
        $this->ResetUserArray();
        
        // Reset/initialize the token array
        $this->ResetTokenArray();

        // In case of initialization, we will disable the backend validation
        $this->ReadConfigData();
 
        // Reset/initialize the cache array
        $this->ResetCacheArray();
        if ($this->IsCacheData())
        {
            $this->ReadCacheData();
        }
    }

    // Class destructor, also called in PHP4
    function __destruct()
    {
        /*
        if ($this->IsCacheData())
        {
            $this->WriteCacheData();
        }
        */
    }


    // Customized information (to be overcharged if needed)
    function GetCustomInfo()
    {
        return "";
    }


    function GetLibraryHash($param1 = "", $param2 = "")
    {
        if (file_exists(__FILE__))
        {
            $me_handler = fopen(__FILE__, "rt");
            $content = "";
            while (!feof($me_handler))
            {
                $content.= fgets($me_handler);
            }
            fclose($me_handler);
            $hash = md5($content);
        }
        else
        {
            $hash = '00000000000000000000000000000000';
        }
        return ($hash);
    }

    function UpgradeSchemaIfNeeded()
    {
        if ($this->GetActualVersion() != $this->GetVersion())
        {
            if ($this->InitializeBackend() < 20)
            {
                $this->SetActualVersion($this->GetVersion());
                $this->WriteConfigData();
            }
        }
    }


    // Reset the errors array
    function ResetErrorsArray()
    {
        $this->_errors_text[0] = "OK: Token accepted";

        $this->_errors_text[10] = "INFO: Access Challenge returned back to the client";

        $this->_errors_text[11] = "INFO: User successfully created or updated";
        $this->_errors_text[12] = "INFO: User successfully deleted";
        $this->_errors_text[13] = "INFO: User PIN code successfully changed";
        $this->_errors_text[14] = "INFO: Token has been resynchronized successfully";
        $this->_errors_text[15] = "INFO: Tokens definition file successfully imported";
        $this->_errors_text[16] = "INFO: QRcode successfully created";
        $this->_errors_text[17] = "INFO: UrlLink successfully created";
        $this->_errors_text[18] = "INFO: SMS code request received";
        $this->_errors_text[19] = "INFO: Requested operation successfully done";

        $this->_errors_text[21] = "ERROR: User doesn't exist";
        $this->_errors_text[22] = "ERROR: User already exists";
        $this->_errors_text[23] = "ERROR: Invalid algorithm";
        $this->_errors_text[24] = "ERROR: User locked (too many tries)";
        $this->_errors_text[25] = "ERROR: User delayed (too many tries, but still a hope in a few minutes)";
        $this->_errors_text[26] = "ERROR: The token has already been used";
        $this->_errors_text[27] = "ERROR: Resynchronization of the token has failed";
        $this->_errors_text[28] = "ERROR: Unable to write the changes in the file";
        $this->_errors_text[29] = "ERROR: Token doesn't exist";

        $this->_errors_text[30] = "ERROR: At least one parameter is missing";
        $this->_errors_text[31] = "ERROR: Tokens definition file doesn't exist";
        $this->_errors_text[32] = "ERROR: Tokens definition file not successfully imported";
        $this->_errors_text[33] = "ERROR: Encryption hash error, encryption key is not matching";
        $this->_errors_text[34] = "ERROR: Linked user doesn't exist";
        $this->_errors_text[35] = "ERROR: User not created";
        $this->_errors_text[37] = "ERROR: Token already attributed";
        $this->_errors_text[38] = "ERROR: User is desactivated";
        $this->_errors_text[39] = "ERROR: Requested operation aborted";
       
        $this->_errors_text[41] = "ERROR: SQL error";
        
        $this->_errors_text[50] = "ERROR: QRcode not created";
        $this->_errors_text[51] = "ERROR: UrlLink not created (no provisionable client for this protocol)";

        $this->_errors_text[60] = "ERROR: No information on where to send SMS code";
        $this->_errors_text[61] = "ERROR: SMS code request received, but an error occurred during transmission";
        $this->_errors_text[62] = "ERROR: SMS provider not supported";
        
        $this->_errors_text[70] = "ERROR: Server authentication error";
        $this->_errors_text[71] = "ERROR: Server request is not correctly formatted";
        $this->_errors_text[72] = "ERROR: Server answer is not correctly formatted";
        
        $this->_errors_text[80] = "ERROR: Server cache error";
        $this->_errors_text[81] = "ERROR: Cache too old for this user, account autolocked";

        $this->_errors_text[98] = "ERROR: Authentication failed (wrong token length)";
        $this->_errors_text[99] = "ERROR: Authentication failed (and other possible unknown errors)";
    }


    function GetErrorText($error_number = 99)
    {
        $text = "";
        if (isset($this->_errors_text[$error_number]))
        {
            $text = $this->_errors_text[$error_number];
        }
        elseif (intval($error_number) > 0)
        {
            $text = $this->_errors_text[99];
        }
        return $text;
    }


    // Reset the cache array
    function ResetCacheArray()
    {
        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['cache']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['cache'])) {
            $pos = strpos(strtoupper($valid_format), 'DEFAULT');
            $value = "";
            if ($pos !== FALSE) {
                $value = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                if (("'" == substr($value,0,1)) && ("'" == substr($value,-1))) {
                    $value = substr($value,1,-1);
                }
            }
            $this->_cache_data[$valid_key] = $value;
        }
    }

    
    function WriteData(
        $item,
        $table,
        $folder,
        $data_array,
        $force_file = false,
        $id_field = '',
        $id_value = '',
        $id_case_sensitive = false,
        $automatically = false,
        $update_last_change = true
    ) {
    /**
     * @brief   Write specific data in the backend (SQL table or file), generic method
     *
     * @param   string  $item                Item family to be handled (Cache, Configuration, Token, etc.)
     * @param   string  $table               Name of the table if the backend is handling tables
     * @param   string  $folder              Name of the folder if the backend is handling folders
     * @param   string  $data_array          Data array of the item to be written
     * @param   string  $force_file          File backend must also always be used
     * @param   string  $id_field            Index field of the item if the backend is handlinh tables
     * @param   string  $id_value            Value of the indexed item
     * @param   string  $id_case_sensitive   We want to be case sensitive for the backend storage
     * @param   string  $automatically       The process is done automatically (for long content only)
     * @param   string  $update_last_change  Update the last_update field (true by default)
     * @retval  boolean                      Result of the operation
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @date    2015-01-23
     * @since   2014-12-30
     */
        if ('configuration' == strtolower($item)) {
            $filename = 'multiotp.ini';
            $force_file = true;
        } elseif ('cache' == strtolower($item)) {
            $filename = 'cache.ini';
        } else {
            $filename = $id_value.'.db';
        }

        $now_epoch = time();
        if ($update_last_change) {
            $data_array['last_update'] = $now_epoch;
        }
        $result = false;

        $esc_id_value = escape_mysql_string($id_value);
        $item_created = FALSE;
        
        $data_array['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
        
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_'.$table.'_table'])) || ('files' == $this->GetBackendType()) || $force_file) {
            if (('files' == $this->GetBackendType()) || $force_file) {
                $file_time = $now_epoch;
                if (!$id_case_sensitive) {
                    $filename = strtolower($filename);
                }
                $file_created = FALSE;
                if (!file_exists($folder.$filename)) {
                    $item_created = TRUE;
                    $file_created = TRUE;
                } elseif ((!$update_last_change) && (!$file_created)) {
                    $file_time = filemtime($folder.$filename);
                }
                if (!($file_handler = fopen($folder.$filename, "wt"))) {
                    $this->WriteLog("Error: database file for ".trim($item." ".$id_value)." cannot be written", FALSE, FALSE, 28, 'System', '');
                }
                else {
                    fwrite($file_handler,"multiotp-database-format-v3"."\n");
                    
                    if ('configuration' == strtolower($item)) {
                        fwrite($file_handler,"; If backend is set to something different than files,\n");
                        fwrite($file_handler,"; and backend_type_validated is set to 1,\n");
                        fwrite($file_handler,"; only the specific information needed for the backend\n");
                        fwrite($file_handler,"; is used from this config file.\n");
                        fwrite($file_handler,"\n");
                    }
                    
                    // foreach (array() as $key => $value) // this is not working well in PHP4
                    reset($data_array);
                    while(list($key, $value) = each($data_array)) {
                        if ('' != trim($key)) {
                            $line = strtolower($key);
                            if (FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$key.'*'))) {
                                $value = $this->Encrypt($key,$value,$this->GetEncryptionKey());
                                $line = $line.":";
                            }
                            $line = $line."=".$value;
                            fwrite($file_handler,$line."\n");
                        }
                    }
                    $result = TRUE;
                    fclose($file_handler);
                    if ((!$update_last_change) && (!$file_created)) {
                        touch($folder.$filename, $file_time);
                    }
                    if ($file_created && ('' != $this->GetLinuxFileMode())) {
                        chmod($folder.$filename, octdec($this->GetLinuxFileMode()));
                    }
                }
                if ($this->GetVerboseFlag()) {
                    if ($file_created) {
                        $this->WriteLog("Info: *File created: ".$folder.$filename, FALSE, FALSE, 19, 'System', "");
                    }
                }                    
            }
            if ('mysql' == $this->GetBackendType()) {
                if ($this->OpenMysqlDatabase()) {
                    $result = TRUE;
                    $sQi_Columns = '';
                    $sQi_Values  = '';
                    $sQu_Data    = '';
                    reset($data_array);
                    while(list($key, $value) = each($data_array)) {
                        $in_the_schema = FALSE;
                        reset($this->_sql_tables_schema[$table]);
                        while(list($valid_key, $valid_format) = each($this->_sql_tables_schema[$table])) {
                            if ($valid_key == $key) {
                                $in_the_schema = TRUE;
                            }
                        }
                        if (($in_the_schema) && ($key != $id_field)) {
                            if ((FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$key.'*'))) && ('' != $value)) {
                                $value = 'ENC:'.$this->Encrypt($key,$value,$this->GetEncryptionKey()).':ENC';
                            }
                            $value = escape_mysql_string($value);
                            $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                            $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                            $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                        } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                            $this->WriteLog("Warning: *The key ".$key." is not in the $table database schema", FALSE, FALSE, 98, 'System', '');
                        }
                    }
                    $num_rows = 0;
                    $sQuery = "SELECT * FROM `".$this->_config_data['sql_'.$table.'_table']."`";
                    if ('' != $id_field) {
                        $sQuery.= " WHERE `$id_field`='".$esc_id_value."'";
                    }
                    
                    if (is_object($this->_mysqli)) {
                        if (!($result = $this->_mysqli->query($sQuery))) {
                            $this->WriteLog("Error: SQL database query error ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                        } else {
                            $num_rows = $result->num_rows;                                    
                        }
                    } elseif (!($result = mysql_query($sQuery, $this->_mysql_database_link))) {
                        $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                    } else {
                        $num_rows = mysql_num_rows($result);
                    }

                    if ($num_rows > 0) {
                        $sQuery = "UPDATE `".$this->_config_data['sql_'.$table.'_table']."` SET ".substr($sQu_Data,0,-1);
                        if ('' != $id_field) {
                            $sQuery.= " WHERE `$id_field`='".$esc_id_value."'";
                        }
                        if (is_object($this->_mysqli)) {
                            if (!($rResult = $this->_mysqli->query($sQuery))) {
                                $this->WriteLog("Error: SQL database query error ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                                $result = FALSE;
                            }
                        } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                            $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                            $result = FALSE;
                        }
                    } else {
                        if ('' != $id_field) {
                            $sQuery = "INSERT INTO `".$this->_config_data['sql_'.$table.'_table']."` (`$id_field`,".substr($sQi_Columns,0,-1).") VALUES ('".$esc_id_value."',".substr($sQi_Values,0,-1).")";
                        } else {
                            $sQuery = "INSERT INTO `".$this->_config_data['sql_'.$table.'_table']."` (".substr($sQi_Columns,0,-1).") VALUES (".substr($sQi_Values,0,-1).")";
                        }
                        if (is_object($this->_mysqli)) {
                            if (!($rResult = $this->_mysqli->query($sQuery))) {
                                $this->WriteLog("Error: SQL database query error ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                            } elseif (0 == $this->_mysqli->affected_rows) {
                                $this->WriteLog("Error: SQL database entry for ".trim($item." ".$id_value)." cannot be created or changed", FALSE, FALSE, 28, 'System', '');
                                $result = FALSE;
                            } else {
                                $item_created = TRUE;
                            }
                        } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                            $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                            $result = FALSE;
                            break;
                        } elseif (0 == mysql_affected_rows($this->_mysql_database_link)) {
                            $this->WriteLog("Error: SQL database entry for ".trim($item." ".$id_value)." cannot be created or changed", FALSE, FALSE, 28, 'System', '');
                            $result = FALSE;
                        } else {
                            $item_created = TRUE;
                        }
                    }
                }
            }
        }
        if ($item_created && $result) {
            if ($automatically) {
                $this->WriteLog("Info: ".trim($item." ".$id_value)." automatically created", FALSE, FALSE, 19, 'System', '');
            }
            else {
                $this->WriteLog("Info: ".trim($item." ".$id_value)." manually created", FALSE, FALSE, 19, 'System', '');
            }
        }
        return $result;
    }


    function ReadCacheValue($key)
    {
        return ((isset($this->_cache_data[$key]))?$this->_cache_data[$key]:"");
    }


    function WriteCacheValue($key, $value)
    {
        $this->_cache_data[$key] = $value;
    }


    function ReadCacheData() {
        $this->ResetCacheArray();
        $result = false;
        
        // First, we read the cache file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
            $cache_filename = 'cache.ini'; // File exists in v3 format only, we don't need any conversion
            if (file_exists($this->GetCacheFolder().$cache_filename)) {
                $file_handler = fopen($this->GetCacheFolder().$cache_filename, "rt");
                $first_line = trim(fgets($file_handler));
                
                while (!feof($file_handler)) {
                    $line = str_replace(chr(10), "", str_replace(chr(13), "", fgets($file_handler)));
                    $line_array = explode("=",$line,2);
                    if (('#' != substr($line, 0, 1)) && (';' != substr($line, 0, 1)) && ("" != trim($line)) && (isset($line_array[1]))) {
                        if ("" != $line_array[0]) {
                            $this->_cache_data[strtolower($line_array[0])] = $line_array[1];
                        }
                    }
                }
                fclose($file_handler);
                $result = TRUE;
            }
        }
        
        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated()) {
            switch ($this->GetBackendType()) {
                case 'mysql':
                    if ($this->OpenMysqlDatabase()) {
                        if ("" != $this->_config_data['sql_cache_table']) {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_cache_table']."` ";
                            
                            $aRow = NULL;

                            if (is_object($this->_mysqli)) {
                                if (!($result = $this->_mysqli->query($sQuery))) {
                                    $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                                    $result = FALSE;
                                } else {
                                    $aRow = $result->fetch_assoc();
                                }
                            } else {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                    $this->WriteLog("Error: ".mysql_error()." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                                    $result = FALSE;
                                } else {
                                    $aRow = mysql_fetch_assoc($rResult);
                                }
                            }

                            if (NULL != $aRow) {
                                $result = TRUE;
                                while(list($key, $value) = @each($aRow)) {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['cache']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['cache'])) {
                                        if ($valid_key == $key) {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if ($in_the_schema) {
                                        $this->_cache_data[$key] = $value;
                                    } elseif (('unique_id' != $key) && $this->GetVerboseFlag()) {
                                        $this->WriteLog("Warning: *the key ".$key." is not in the cache database schema", FALSE, FALSE, 98, 'System', "");
                                    }
                                }
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }
        
        // If cache is too old (more than one day), we reset the cache and we save it
        if (((24*60*60) + intval($this->_cache_data['last_update'])) < time()) {
            $this->ResetCacheArray();
            $this->WriteCacheData();
        }
        return $result;
    }


    function WriteCacheData()
    {
        $result = $this->WriteData('Cache',
                                   'cache',
                                   $this->GetCacheFolder(),
                                   $this->_cache_data
                                  );
        return $result;
    }


    // Reset the config array
    function ResetConfigArray() {
        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['config']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['config'])) {
            $pos = strpos(strtoupper($valid_format), 'DEFAULT');
            $value = "";
            if ($pos !== FALSE) {
                $value = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                if (("'" == substr($value,0,1)) && ("'" == substr($value,-1))) {
                    $value = substr($value,1,-1);
                }
            }
            $this->_config_data[$valid_key] = $value;
        }
    }


    // TODO, could be nice to backup the configuration in a file somewhere
    function BackupConfiguration(
        $param1 = "",
        $param2 = "",
        $param3 = "",
        $param4 = ""
    ) {
        return TRUE;
    }


    function SetConsoleAuthentication($value)
    {
        $this->_config_data['console_authentication'] = ((intval($value) > 0)?1:0);
    }


    function EnableConsoleAuthentication()
    {
        $this->_config_data['console_authentication'] = 1;
    }


    function DisableConsoleAuthentication()
    {
        $this->_config_data['console_authentication'] = 0;
    }


    function IsConsoleAuthentication()
    {
        return (1 == ($this->_config_data['console_authentication']));
    }


    function SetLogFileName(
        $filename
    ) {
        $this->_log_file_name = trim($filename);
    }


    function GetLogFileName() {
        return $this->_log_file_name;
    }

    function SetLogHeaderWritten(
        $log_header_written
    ) {
        $this->_log_header_written = $log_header_written;
    }


    function GetLogHeaderWritten() {
        return $this->_log_header_written;
    }


    function SetLogFolder(
        $folder
    ) {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/") {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_log_folder = $new_folder;
        if (!file_exists($new_folder)) {
            @mkdir($new_folder);
        }
    }


    function GetLogFolder() {
        if ("" == $this->_log_folder) {
            $this->SetLogFolder($this->GetScriptFolder()."log/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_log_folder);
    }


    function WriteLog(
        $info,
        $file_only = false,
        $hide_on_display = false,
        $error_code = 9999,
        $category = '*DEFAULT*',
        $user = '*DEFAULT*',
        $overwrite_severity = -1
    ) {
    /**
     * @brief   Write information in the log file/database, to the syslog server and on the screen
     *
     * @param   string  $info                Information to log
     * @param   boolean $file_only           Define that the information must not be written in the database
     *                                        (in case of database error for example)
     * @param   boolean $hide_on_display     Define that the information must never be displayed
     * @param   int     $error_code          Error code (to define a matching severity, which can be overwritten if needed)
     * @param   string  $category            Define the category, will be "Authentication" if not defined
     * @param   string  $user                Define the user concerned, the default value will take $this->GetUser()
     * @param   int     $overwrite_severity  Define the severity (0-7), -1 will take the default severity, based on error number
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.1.0
     * @date    2014-01-03
     * @since   2010-12-19
     *
     *   Severity values:
     *     0 Emergency: system is unusable
     *     1 Alert: action must be taken immediately
     *     2 Critical: critical conditions
     *     3 Error: error conditions
     *     4 Warning: warning conditions
     *     5 Notice: normal but significant condition (default value)
     *     6 Informational: informational messages
     *     7 Debug: debug-level messages
     */

        if ('*DEFAULT*' != $user) {
            $user_log = $user;
        } else {
            $user_log = $this->GetUser();
        }
        
        if ('*DEFAULT*' != $category) {
            $category_log = $category;
        } else {
            $category_log = "Authentication"; // $this->_class;
        }

        if (0 == $error_code) {
            $severity = 5;
        } elseif (8888 <= $error_code) {
            $severity = 7;
        } elseif (20 > $error_code) {
            $severity = 6;
        } elseif (100 > $error_code) {
            $severity = 4;
        } elseif (200 > $error_code) {
            $severity = 3;
        } elseif (300 > $error_code) {
            $severity = 2;
        } elseif (400 > $error_code) {
            $severity = 1;
        } elseif (500 > $error_code) {
            $severity = 0;
        } else {
            $severity = 3;
        }
        
        if ((intval($overwrite_severity) >= 0) && (intval($overwrite_severity) <= 7)) {
            $severity = intval($overwrite_severity);
        }
        
        switch ($severity) {
            case 0:
                $severity_txt = 'emergency';
                break;
            case 1:
                $severity_txt = 'alert';
                break;
            case 2:
                $severity_txt = 'critical';
                break;
            case 3:
                $severity_txt = 'error';
                break;
            case 4:
                $severity_txt = 'warning';
                break;
            case 5:
                $severity_txt = 'notice';
                break;
            case 6:
                $severity_txt = 'info';
                break;
            case 7:
                $severity_txt = 'debug';
                break;
            default:
                $severity_txt = 'error';
        }

        $post_info = "";
        $pre_info = "";
        if ("" != ($this->GetSourceIp().$this->GetSourceMac())) {
            $post_info.= "from ";
            if ("" != $this->GetSourceIp()) {
                $post_info.= $this->GetSourceIp().' ';
            }
            if ("" != $this->GetSourceMac()) {
                $post_info.= '['.$this->GetSourceMac().'] ';
            }
        }
        if ("" != ($this->GetCallingIp().$this->GetCallingMac())) {
            $post_info.= "for ";
            if ("" != $this->GetCallingIp()) {
                $post_info.= $this->GetCallingIp().' ';
            }
            if ("" != $this->GetCallingMac()) {
                $post_info.= '['.$this->GetCallingMac().'] ';
            }
        }
        $log_info = trim(trim($pre_info).' '.$info.' '.trim($post_info));
        
        // Cleaning the log info, just to be sure that we don't have tabs (\t)
        // in them, and also that the CRLF, CR or LF is the good done (\n)
        $log_info = str_replace(chr(13).chr(10), "<<CRLF>>", $log_info);
        $log_info = str_replace(chr(13), "<<CRLF>>", $log_info);
        $log_info = str_replace(chr(10), "<<CRLF>>", $log_info);
        $log_info = str_replace("<<CRLF>>", "\n", $log_info);
        $log_info = str_replace("\t", " ", $log_info);

        $log_datetime = date("Y-m-d H:i:s");
        
        // In the logfile, we don't want to have several lines for one entry,
        // therefore we are replacing the "\n" with "; " (or <br /> if we want to debug in HTML mode
        
        $logfile_content = $log_datetime."\t".$severity_txt."\t".$user_log."\t".$category_log."\t".str_replace("\n", $this->IsDebugViaHtml()?"<br />":"; ", $log_info);

        if (($this->GetDisplayLogFlag()) && (!$hide_on_display)) {
            $display_text = "\nLOG ".$log_datetime.' '.$severity_txt.' '.(("" == $user_log)?"":'(user '.$user_log.') ').$category_log.' '.$log_info."\n";
            if ($this->IsDebugViaHtml()) {
                $display_text = str_replace("\n","<br />\n", $display_text);
            }
            echo $display_text;
        }
        
        if ("" != trim($this->GetSysLogServer())) {
            if ($severity <= $this->GetSyslogLevel()) {
                $syslog = new MultiotpSyslog();
                $syslog->SetFacility($this->GetSyslogFacility());
                $syslog->SetSeverity($severity);
                $syslog->SetHostname($this->GetSystemName());
                $syslog->SetFqdn($this->GetSystemName().(("" != $this->GetDomainName())?'.'.$this->GetDomainName():""));
                $syslog->SetIpFrom($this->GetLocalIpAddress());
                $syslog->SetProcess('multiOTP');
                $syslog->SetContent(str_replace("\n", "; ", $log_info));
                $syslog->SetServer($this->GetSysLogServer());
                $syslog->SetPort($this->GetSysLogPort());
                $syslog->Send();
            }
        }

        $log_link = NULL;
        if ($this->IsLogEnabled()) {
            if ((!$file_only) && ('mysql' == $this->GetBackendType()) && $this->GetBackendTypeValidated() && ("" != $this->_config_data['sql_log_table'])) {
                if ('mysql' == $this->GetBackendType()) {
                    if ($this->OpenMysqlDatabase()) {
                        $log_severity_escaped = escape_mysql_string($severity_txt);
                        $log_user_escaped = escape_mysql_string($user_log);
                        $log_category_escaped = escape_mysql_string($category_log);
                        $log_info_escaped = substr(escape_mysql_string($log_info),0,255);

                        $sQuery  = "INSERT INTO `".$this->_config_data['sql_log_table']."` (`datetime`,`severity`,`user`,`category`,`logentry`) VALUES ('".$log_datetime."','".$log_severity_escaped."','".$log_user_escaped."','".$log_category_escaped."','".$log_info_escaped."')";
                        
                        if (is_object($this->_mysqli)) {
                            if (!($rResult = $this->_mysqli->query($sQuery))) {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".trim($this->_mysqli->error), TRUE, FALSE, 199, 'System', "");
                            }
                        } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                            $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', "");
                        }
                    }
                    //mysql_close($log_link);
                }
            } else {
                if (!file_exists($this->GetLogFolder())) {
                    @mkdir($this->GetLogFolder());
                }
                $file_created = (!file_exists($this->GetLogFolder().$this->GetLogFileName()));
                $log_file_handle = fopen($this->GetLogFolder().$this->GetLogFileName(),"ab+");
                if ($this->GetVerboseFlag()) {
                    if (!$this->GetLogHeaderWritten()) {
                        fwrite($log_file_handle,str_repeat("=",40)."\n");
                        fwrite($log_file_handle,'multiotp '.$this->GetVersion()."\n");
                        if ($this->GetVerboseFlag()) {
                            fwrite($log_file_handle,'Your script is running from '.$this->GetScriptFolder()."\n");
                        }
                    }
                    $this->SetLogHeaderWritten(TRUE);
                }
                
                fwrite($log_file_handle,$logfile_content."\n");
                fclose($log_file_handle);
                if ($file_created && ("" != $this->GetLinuxFileMode())) {
                    chmod($this->GetLogFolder().$this->GetLogFileName(), octdec($this->GetLinuxFileMode()));
                }
            }
        }
    }


    function ShowLog($as_result = FALSE) {
        $result = "";
        if ('mysql' == $this->GetBackendType()) {
            if ($this->OpenMysqlDatabase()) {
                $sQuery  = "SELECT * FROM `".$this->_config_data['sql_log_table']."`";
                
                if (is_object($this->_mysqli)) {
                    if (!($rResult = $this->_mysqli->query($sQuery))) {
                        $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', "");
                        $result = FALSE;
                    } else {
                        while ($aRow = $rResult->fetch_assoc()) {
                            if ($as_result) {
                                $result.= trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                            } else {
                                echo trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                            }
                        }                         
                    }
                } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                    $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', "");
                    $result = FALSE;
                } else {
                    while ($aRow = mysql_fetch_assoc($rResult)) {
                        if ($as_result) {
                            $result.= trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                        } else {
                            echo trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                        }
                    }                         
                }
            }
            //mysql_close($log_link);
        } elseif (file_exists($this->GetLogFolder().$this->GetLogFileName())) {
            $log_file_handle = fopen($this->GetLogFolder().$this->GetLogFileName(),"r");
            while (!feof($log_file_handle)) {
                if ($as_result) {
                    $result.= trim(fgets($log_file_handle))."\n";
                } else {
                    echo trim(fgets($log_file_handle))."\n";
                }
            }
            fclose($log_file_handle);
        }
        return $result;
    }


    function ClearLog() {
        $result = TRUE;
        if ('mysql' == $this->GetBackendType()) {
            if ($this->OpenMysqlDatabase()) {
                $sQuery  = "TRUNCATE `".$this->_config_data['sql_log_table']."`";
                
                if (is_object($this->_mysqli)) {
                    if (!($rResult = $this->_mysqli->query($sQuery))) {
                        $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', "");
                        $result = FALSE;
                    }
                } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                    $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', "");
                    $result = FALSE;
                }
            } else {
                $result = FALSE;
            }
        }

        if (file_exists($this->GetLogFolder().$this->GetLogFileName())) {
            unlink($this->GetLogFolder().$this->GetLogFileName());
        }
        return $result;
    }


    function EnableLog() {
        $this->_log_flag = TRUE;
        if ("" == $this->_log_folder) {
            $this->SetLogFolder($this->GetScriptFolder()."log/");
        }
    }


    function IsLogEnabled() {
        return (TRUE === $this->_log_flag);
    }


    function DisableLog() {
        $this->_log_flag = FALSE;
    }


    function EnableVerboseLog() {
        $this->EnableLog();
        $this->_log_verbose_flag = TRUE;
    }


    function DisableVerboseLog() {
        $this->_log_verbose_flag = FALSE;
    }


    function GetVerboseFlag() {
        return $this->_log_verbose_flag;
    }


    function EnableDisplayLog() {
        $this->_log_display_flag = TRUE;
    }


    function DisableDisplayLog() {
        $this->_log_display_flag = FALSE;
    }


    function GetDisplayLogFlag() {
        return $this->_log_display_flag;
    }


    function SetDemoMode(
        $value
    ) {
        $this->_config_data['demo_mode'] = ((intval($value) > 0)?1:0);
    }


    function EnableDemoMode() {
        $this->_config_data['demo_mode'] = 1;
    }


    function DisableDemoMode() {
        $this->_config_data['demo_mode'] = 0;
    }


    function IsDemoMode() {
        return (1 == ($this->_config_data['demo_mode']));
    }


    function SetCacheLdapHash(
        $value
    ) {
        $this->_config_data['cache_ldap_hash'] = ((intval($value) > 0)?1:0);
    }


    function EnableCacheLdapHash() {
        $this->_config_data['cache_ldap_hash'] = 1;
    }


    function DisableCacheLdapHash() {
        $this->_config_data['cache_ldap_hash'] = 0;
    }


    function IsCacheLdapHash() {
        return (1 == ($this->_config_data['cache_ldap_hash']));
    }


    function IsLdapServerReachable() {
        return (TRUE === $this->_ldap_server_reachable);
    }


    function SetLdapServerReachable(
        $value
    ) {
        $this->_ldap_server_reachable = (TRUE === $value);
    }


    function SetEncryptionKeyFullPath(
        $full_path
    ) {
        $this->_config_data['encryption_key_full_path'] = $full_path;
    }


    function GetEncryptionKeyFullPath() {
        return trim(isset($this->_config_data['encryption_key_full_path'])?$this->_config_data['encryption_key_full_path']:"");
    }


    function SetHashSaltFullPath(
        $full_path
    ) {
        $this->_config_data['hash_salt_full_path'] = $full_path;
    }


    function GetHashSaltFullPath() {
        return trim($this->_config_data['hash_salt_full_path']);
    }


    function SetConfigFolder(
        $folder,
        $create = true,
        $read_config = true
    ) {
    /**
     * @brief   Set the configuration folder (for the config file).
     *
     * @param   string  $folder       Full path to the config folder.
     * @param   boolean $create       Create the folder if it doesn't exists.
     * @param   boolean $read_config  Read directly the configuration file.
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.0.0
     * @date    2013-05-13
     */
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/") {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_config_folder = $new_folder;
        if ($create && (!file_exists($new_folder))) {
            if (!@mkdir($new_folder)) {
                $this->WriteLog("Error: Unable to create the missing config folder ".$new_folder, true, false, 299, 'System', "");
            }
        }
        if ($read_config) {
            $this->ReadConfigData();
        }
    }


    function GetConfigFolder(
        $create_if_not_exist = false
    ) {
    /**
     * @brief   Get the configuration folder (for the config file).
     *
     * @param   boolean $create_if_not_exist Create the folder if it doesn't exists.
     * @retval  string                       Full path to the config folder.
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.0.0
     * @date    2013-05-13
     */
        $config_folder = $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
        if ("" == $config_folder) {
            $this->SetConfigFolder($this->GetScriptFolder()."config/", $create_if_not_exist);
        } elseif (!file_exists($config_folder)) {
            if ($create_if_not_exist) {
                if (!@mkdir($config_folder)) {
                    $this->WriteLog("Error: Unable to create the missing config folder ".$config_folder, FALSE, FALSE, 299, 'System', "");
                }
            }
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
    }


    function SetCacheFolder(
        $folder,
        $create = true,
        $read_cache = true
    ) {
    /**
     * @brief   Set the cache folder (for the cache file).
     *
     * @param   string  $folder       Full path to the cache folder.
     * @param   boolean $create       Create the folder if it doesn't exists.
     * @param   boolean $read_cache   Read directly the cache file.
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.2.5.0
     * @date    2014-07-25
     */
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/") {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_cache_folder = $new_folder;
        if ($create && (!file_exists($new_folder))) {
            if (!@mkdir($new_folder)) {
                $this->WriteLog("Error: Unable to create the missing cache folder ".$new_folder, TRUE, FALSE, 299, 'System', "");
            }
        }
        if ($read_cache) {
            $this->ReadCacheData();
        }
    }


    function GetCacheFolder(
        $create_if_not_exist = false
        ) {
    /**
     * @brief   Get the cache folder (for the cache file).
     *
     * @param   boolean $create_if_not_exist Create the folder if it doesn't exists.
     * @retval  string                       Full path to the cache folder.
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.2.5.0
     * @date    2014-07-25
     */
        $cache_folder = $this->ConvertToWindowsPathIfNeeded($this->_cache_folder);
        if ("" == $cache_folder) {
            $this->SetCacheFolder($this->GetScriptFolder()."cache/", $create_if_not_exist);
        } elseif (!file_exists($cache_folder)) {
            if ($create_if_not_exist) {
                if (!@mkdir($cache_folder)) {
                    $this->WriteLog("Error: Unable to create the missing cache folder ".$cache_folder, FALSE, FALSE, 299, 'System', "");
                }
            }
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_cache_folder);
    }


    function GetLocalIpAddress() {
        $local_ip_address = "";
        if (strtolower(substr(PHP_OS, 0, 3)) === 'win') { // Windows
            exec("ipconfig /all", $output);
            foreach($output as $line) {
                $line = $line."  ";
                if (preg_match("/.*IPv4.*[^\.]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})[^\.]+/", $line)) {
                    preg_match_all("/[^\.[:xdigit:]]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})/", $line, $result_array, PREG_SET_ORDER);
                    if (isset($result_array[0][1])) {
                        $temp = trim($result_array[0][1]);
                        if ('0.0.0.0' != $temp) {
                            $local_ip_address = strtoupper($temp);
                            break;
                        }
                    }
                }
            }
        } else { // Linux
            exec("ifconfig eth0 | grep \"inet addr\" | grep -o -E '([[:xdigit:]]{1,3}\.){3}[[:xdigit:]]{1,3}'", $output);
            $local_ip_address = strtoupper($output[0]);
        }
        return $local_ip_address;
    }


    function GetSystemName() {
        $system_name = trim(php_uname('n'));
        return $system_name;
    }


    function SetDomainName($value) {
        $this->_config_data['domain_name'] = ($value);
    }


    function GetDomainName() {
        return $this->_config_data['domain_name'];
    }


    function SetEmailAdminAddress(
        $value
    ) {
        $this->_config_data['email_admin_address'] = ($value);
    }


    function GetEmailAdminAddress() {
        return $this->_config_data['email_admin_address'];
    }


    function SetHashSalt(
        $salt
    ) {
        $this->_hash_salt  = trim($salt);
    }


    function GetHashSalt() {
        $salt = $this->_hash_salt;
        if ((("" == $salt) ||
             ("MySalt" == $salt) ||
             ("AjaxH@shS@lt" == $salt)
            ) &&
            ("" != $this->GetHashSaltFullPath()) &&
            file_exists($this->GetHashSaltFullPath())) {
            if ($hash_salt_file_handler = fopen($this->GetHashSaltFullPath(), "rt")) {
                $temp = trim(fgets($hash_salt_file_handler));
                if ("" != $temp) {
                    $salt = $temp;
                }
                fclose($hash_salt_file_handler);
            }
        }
        return trim($salt);
    }


    function SetRandomSalt(
        $salt
    ) {
        $this->_random_salt  = trim($salt);
    }


    function GetRandomSalt() {
        return trim($this->_random_salt);
    }


    function SetAdminPassword(
        $password
    ) {
        return $this->SetConfigAttribute('admin_password_hash',md5($this->GetHashSalt().$password.$this->GetHashSalt()));
    }


    function SetAdminPasswordHash(
        $password_hash
    ) {
        return $this->SetConfigAttribute('admin_password_hash',$password_hash);
    }

    // Weak security check: the client side should return password (for internal call only)
    function CheckAdminPassword(
        $password
    ) {
        return ($this->GetConfigAttribute('admin_password_hash') == md5($this->GetHashSalt().$password.$this->GetHashSalt()));
    }


    // Regular security check: the client side should return md5(hash_salt + password + hash_salt)
    function CheckAdminPasswordHash(
        $password_hash_with_salt
    ) {
        if (32 == strlen($password_hash_with_salt)) {
            return ($this->GetConfigAttribute('admin_password_hash') == $password_hash_with_salt);
        } else {
            return false;
        }
    }


    // Better security check: the client side should return md5(salt + md5(hash_salt + password + hash_salt) + salt)
    function CheckAdminPasswordHashWithRandomSalt($password_hash_with_salt) {
        if (32 == strlen($password_hash_with_salt)) {
            return (md5($this->GetRandomSalt().$this->GetConfigAttribute('admin_password_hash').$this->GetRandomSalt()) == $password_hash_with_salt);
        } else {
            return false;
        }
    }


    function EnableDebugViaHtml() {
        $this->_debug_via_html = TRUE;
    }


    function IsDebugViaHtml() {
        return ($this->_debug_via_html);
    }


    function EnableKeepLocal() {
        $this->_keep_local = TRUE;
    }


    function IsKeepLocal() {
        return ($this->_keep_local);
    }


    function SetLinuxFileMode(
        $mode
    ) {
        $this->_linux_file_mode = $mode;
    }


    function GetLinuxFileMode() {
        return ($this->_linux_file_mode);
    }


    function SetConfigData(
        $key,
        $value
    ) {
        if (isset($this->_config_data[$key])) {
            $this->_config_data[$key] = $value;
        }
    }


    function SetLogOption(
        $value
    ) {
        $this->_config_data['log'] = $value;
        if (1 == $this->_config_data['log']) {
            $this->EnableLog();
        }
    }


    function SetDebugOption(
        $value
    ) {
        $this->_config_data['debug'] = $value;
        if (1 == $this->_config_data['debug']) {
            $this->EnableVerboseLog();
        }
    }


    function SetDisplayLogOption(
        $value
    ) {
        $this->_config_data['display_log'] = $value;
        if (1 == $this->_config_data['display_log']) {
            $this->EnableDisplayLog();
        }
    }


    function SetMigrationFromFile(
        $value
    ) {
        $this->_migration_from_file = ($value?TRUE:FALSE);
    }


    function GetMigrationFromFile() {
        return $this->_migration_from_file;
    }


    function SetBackendType(
        $type
    ) {
        $this->_config_data['backend_type'] = $type;
        $this->_config_data['backend_type_validated'] = 0;
    }


    function GetBackendType() {
        return $this->_config_data['backend_type'];
    }


    function SetBackendTypeValidated(
        $backend_type_validated,
        $value
    ) {
        if ("" != $backend_type_validated) {
            $this->_config_data['backend_type'] = $backend_type_validated;
        }
        $this->_config_data['backend_type_validated'] = ($value?1:0);
    }

    function GetBackendTypeValidated() {
        return (1 == (isset($this->_config_data['backend_type_validated'])?$this->_config_data['backend_type_validated']:0));
    }

    function SetScratchPasswordsDigits(
        $value
    ) {
        $this->_config_data['scratch_passwords_digits'] = $value;
    }


    function GetScratchPasswordsDigits() {
        return $this->_config_data['scratch_passwords_digits'];
    }


    function SetDefaultUserGroup(
        $value
    ) {
        $this->_config_data['default_user_group'] = $value;
    }


    function GetDefaultUserGroup() {
        return $this->_config_data['default_user_group'];
    }


    function SetGroupAttribute(
        $value
    ) {
        $this->_config_data['group_attribute'] = $value;
    }


    function GetGroupAttribute() {
        return $this->_config_data['group_attribute'];
    }


    function SetIssuer(
        $value
    ) {
        $this->_config_data['issuer'] = $value;
    }


    function GetIssuer() {
        if (isset($this->_config_data['issuer'])) {
            return $this->_config_data['issuer'];
        } else {
            return "";
        }
    }


    function SetClearOtpAttribute($value) {
        $this->_config_data['clear_otp_attribute'] = $value;
    }


    function GetClearOtpAttribute() {
        return $this->_config_data['clear_otp_attribute'];
    }


    function SetSqlServer(
        $server
    ) {
        $this->_config_data['sql_server'] = $server;
    }


    function SetSqlUsername(
        $username
    ) {
        $this->_config_data['sql_username'] = $username;
    }


    function SetSqlPassword(
        $password
    ) {
        $this->_config_data['sql_password'] = $password;
    }


    function SetSqlDatabase(
        $database
    ) {
        $this->_config_data['sql_database'] = $database;
    }


    function SetSqlTableName(
        $table_to_define,
            $table_name
    ) {
        if (isset($this->_config_data['sql_'.$table_to_define.'_table'])) {
            $this->_config_data['sql_'.$table_to_define.'_table'] = $table_name;
        }
    }


    function MySqlAddRowIfNeeded($table, $row, $row_type, $is_an_index = FALSE) {
        $result = FALSE;
        if (is_object($this->_mysqli)) {
            $sql_query = "SELECT `".$row."` FROM ".$table;
            if ($result = $this->_mysqli->query($sql_query)) {
                $result = TRUE;
            } else { //$select_row = $result->fetch_assoc();
                $sql_query = "ALTER TABLE ".$table." ADD `".$row."` ".$row_type;
                if ($is_an_index) {
                    $sql_query.= " , ADD INDEX ( `".$row."` )";
                }
                if (!$this->_mysqli->query($sql_query)) {
                    $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sql_query, TRUE, FALSE, 199, 'System', "");
                    $result = FALSE;
                }
            }
        } elseif (NULL != $this->_mysql_database_link) {
            $sql_query = "SELECT `".$row."` FROM ".$table;
            if (($select_row = mysql_query($sql_query, $this->_mysql_database_link))) {
                $result = TRUE;
            } elseif (!$select_row) {
                $sql_query = "ALTER TABLE ".$table." ADD `".$row."` ".$row_type;
                if ($is_an_index) {
                    $sql_query.= " , ADD INDEX ( `".$row."` )";
                }
                if (!mysql_query($sql_query, $this->_mysql_database_link)) {
                    $this->WriteLog("Error: ".mysql_error()." ".$sql_query, TRUE, FALSE, 199, 'System', "");
                    $result = FALSE;
                }
            }
        } elseif ($this->GetVerboseFlag()) {
            $this->WriteLog("Error: *The database link is down!", TRUE, FALSE, 199, 'System', "");
        }
        return $result;
    }


    function OpenMysqlDatabase() {
        if ((is_object($this->_mysqli)) || (NULL != $this->_mysql_database_link)) {
            $result = TRUE;
        } else {
            $result = FALSE;
            if (("" != $this->_config_data['sql_server']) &&
                ("" != $this->_config_data['sql_username']) &&
                ("" != $this->_config_data['sql_password']) &&
                ("" != $this->_config_data['sql_database'])) {
                if (class_exists('mysqli')) {
                    $this->_mysqli = @new mysqli($this->_config_data['sql_server'],
                                                 $this->_config_data['sql_username'],
                                                 $this->_config_data['sql_password'],
                                                 $this->_config_data['sql_database']);
                    if (0 != $this->_mysqli->connect_errno) {
                        $this->WriteLog("Error: Bad SQL authentication parameters, ".$this->_mysqli->connect_errno.', '.trim($this->_mysqli->connect_error), TRUE, FALSE, 199, 'System', "");
                        unset($this->_mysqli);
                        $this->_mysqli = NULL;
                    } else {
                        $result = TRUE;
                    }
                } elseif (!($this->_mysql_database_link = mysql_connect($this->_config_data['sql_server'],
                                                                        $this->_config_data['sql_username'],
                                                                        $this->_config_data['sql_password']))) {
                    $this->WriteLog("Error: Bad SQL authentication parameters, ".mysql_error(), TRUE, FALSE, 199, 'System', "");
                } else {
                    if (!mysql_select_db($this->_config_data['sql_database'])) {
                        $this->WriteLog("Error: Bad SQL database", TRUE, FALSE, 199, 'System', "");
                        mysql_close($this->_mysql_database_link);
                        $this->_mysql_database_link = NULL;
                    } else {
                        $result = TRUE;
                    }
                }
            }
        }
        return $result;
    }


    function InitializeBackend() {
        $write_config_data = false;
        $backend_type = $this->GetBackendType();
        if ('mysql' == $backend_type) {
            if ($this->OpenMysqlDatabase()) {
                foreach ($this->_sql_tables as $sql_table) {
                    if ("" != $this->_config_data['sql_'.$sql_table.'_table']) {
                        $sql_query = "CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_'.$sql_table.'_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));";
                        if (is_object($this->_mysqli)) {
                            if (!($result = $this->_mysqli->query($sql_query))) {
                                $this->WriteLog("Error: Bad SQL request ($sql_query), ".trim($this->_mysqli->error), TRUE, FALSE, 199, 'System', "");
                                return 41;
                            }
                        } elseif (!mysql_query($sql_query, $this->_mysql_database_link)) {
                            $this->WriteLog("Error: Bad SQL request (CREATE TABLE ".$this->_config_data['sql_'.$sql_table.'_table']."), ".mysql_error(), TRUE, FALSE, 199, 'System', "");
                            return 41;
                        }
                        reset($this->_sql_tables_schema[$sql_table]);
                        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema[$sql_table])) {
                            $this->MySqlAddRowIfNeeded($this->_config_data['sql_'.$sql_table.'_table'], $valid_key, $valid_format, (FALSE !== strpos($this->_sql_tables_index[$sql_table], "*".$valid_key."*")));
                        }
                    }
                }
                $this->SetBackendTypeValidated($backend_type, TRUE);
                $write_config_data = true;
            }
        }
        if ($write_config_data) {
            $this->WriteConfigData();
        }
        return 19;
    }


    function IsOptionInSchema($schema, $option) {
        $in_the_schema = FALSE;
        if (isset($this->_sql_tables_schema[$schema])) {
            reset($this->_sql_tables_schema[$schema]);
            while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema[$schema])) {
                if ($valid_key == $option) {
                    $in_the_schema = TRUE;
                }
            }
        }
        return $in_the_schema;
    }


    function ReadConfigData(
        $encryption_only = false
    ) {
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_config_data['encryption_hash'] = "";

        // First, we read the config file in any case
        $config_filename = 'multiotp.ini'; // File exists in v3 format only, we don't need any conversion
        if (file_exists($this->GetConfigFolder().$config_filename))
        {
            $file_handler = fopen($this->GetConfigFolder().$config_filename, "rt");
            $first_line = trim(fgets($file_handler));
            
            while (!feof($file_handler))
            {
                $line = str_replace(chr(10), "", str_replace(chr(13), "", fgets($file_handler)));
                $line_array = explode("=",$line,2);
                if (('#' != substr($line, 0, 1)) && (';' != substr($line, 0, 1)) && ("" != trim($line)) && (isset($line_array[1])))
                {
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                    }
                    if ("" != $line_array[0])
                    {
                        $this->_config_data[strtolower($line_array[0])] = $line_array[1];
                    }
                }
            }
            fclose($file_handler);
            $result = TRUE;
            if (("" != $this->_config_data['encryption_hash']) && (!$encryption_only))
            {
                if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                {
                    $this->_config_data['encryption_hash'] = "ERROR";
                    $this->WriteLog("Error: the configuration encryption key is not matching", FALSE, FALSE, 299, 'System', "");
                    $result = FALSE;
                }
            }
        }
        
        if (!$encryption_only)
        {
            if ($this->_initialize_backend)
            {
                $this->SetBackendTypeValidated("", FALSE);
                $this->WriteConfigData();
            }
            // And now, we override the values if another backend type is defined
            if ($this->GetBackendTypeValidated())
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            if ("" != $this->_config_data['sql_config_table'])
                            {
                                $sQuery  = "SELECT * FROM `".$this->_config_data['sql_config_table']."` ";
                                
                                $aRow = NULL;

                                if (is_object($this->_mysqli))
                                {
                                    if (!($result = $this->_mysqli->query($sQuery)))
                                    {
                                        $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                                        $result = FALSE;
                                    }
                                    else
                                    {
                                        $aRow = $result->fetch_assoc();
                                    }
                                }
                                else
                                {
                                    if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                    {
                                        $this->WriteLog("Error: ".mysql_error()." ".$sQuery, TRUE, FALSE, 199, 'System', "");
                                        $result = FALSE;
                                    }
                                    else
                                    {
                                        $aRow = mysql_fetch_assoc($rResult);
                                    }
                                }

                                if (NULL != $aRow) {
                                    $result = TRUE;
                                    while(list($key, $value) = @each($aRow)) {
                                        $in_the_schema = FALSE;
                                        reset($this->_sql_tables_schema['config']);
                                        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['config'])) {
                                            if ($valid_key == $key) {
                                                $in_the_schema = TRUE;
                                            }
                                        }
                                        if ($in_the_schema) {
                                            if (FALSE === strpos($this->_sql_tables_ignore['config'], "*".$valid_key."*")) {
                                                if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                                    $value = substr($value,4);
                                                    $value = substr($value,0,strlen($value)-4);
                                                    $this->_config_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                                } else {
                                                    $this->_config_data[$key] = $value;
                                                }
                                            }
                                        } elseif (('unique_id' != $key) && $this->GetVerboseFlag()) {
                                            $this->WriteLog("Warning: *the key ".$key." is not in the config database schema", FALSE, FALSE, 98, 'System', "");
                                        }
                                    }
                                }
                            }
                            if (("" != $this->_config_data['encryption_hash']) && ($this->_encryption_check)) {
                                if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                                    $this->_config_data['encryption_hash'] = "ERROR";
                                    $this->WriteLog("Error: the configuration mysql encryption key is not matching", FALSE, FALSE, 299, 'System', "");
                                    $result = FALSE;
                                }
                            }
                        }
                        break;
                    default:
                    // Nothing to do if the backend type is unknown
                        break;
                }
            }
            
            if (isset($this->_config_data['log']) && (1 == $this->_config_data['log']))
            {
                $this->EnableLog();
            }

            if (isset($this->_config_data['debug']) && (1 == $this->_config_data['debug']))
            {
                $this->EnableVerboseLog();
            }

            if (isset($this->_config_data['display_log']) && (1 == $this->_config_data['display_log']))
            {
                $this->EnableDisplayLog();
            }
            
            $this->SetAttributesToEncrypt(trim(isset($this->_config_data['attributes_to_encrypt'])?$this->_config_data['attributes_to_encrypt']:""));
            
            $timezone = $this->GetTimezone(); // Read the timezone (and set it in PHP automatically)
        }
        
        if ((!isset($this->_config_data['server_secret'])) || ('' == $this->_config_data['server_secret'])) {
            $this->_config_data['server_secret'] = 'ClientServerSecret';
        }
        return $result;
    }


    function WriteConfigData()
    {
        $result = $this->WriteData('Configuration',
                                   'config',
                                   $this->GetConfigFolder(true),
                                   $this->_config_data,
                                   true
                                  );
        return $result;
    }


    // Reset the temporary user array
    function ResetTempUserArray() {
        $temp_user_array = array();

        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['users']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users'])) {
            $pos = strpos(strtoupper($valid_format), 'DEFAULT');
            $value = "";
            if ($pos !== FALSE) {
                $value = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                if (("'" == substr($value,0,1)) && ("'" == substr($value,-1))) {
                    $value = substr($value,1,-1);
                }
            }
            $temp_user_array[$valid_key] = $value;
        }

        // Request the pin as a prefix of the returned token value
        $temp_user_array['request_prefix_pin'] = $this->GetDefaultRequestPrefixPin();
        
        return $temp_user_array;
    }


    // Reset the user array
    function ResetUserArray() {
        $this->_user_data = array();
        $this->_user_data = $this->ResetTempUserArray();

        // The user data array is not read actually
        $this->SetUserDataReadFlag(false);
    }


    function ResetTokenArray() {
        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['tokens']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens'])) {
            $pos = strpos(strtoupper($valid_format), 'DEFAULT');
            $value = "";
            if ($pos !== FALSE) {
                $value = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                if (("'" == substr($value,0,1)) && ("'" == substr($value,-1))) {
                    $value = substr($value,1,-1);
                }
            }
            $this->_token_data[$valid_key] = $value;
        }
        $this->_token_data['issuer'] = $this->GetIssuer();
        
        // The token data array is not read actually
        $this->SetTokenDataReadFlag(FALSE);
    }


    function ResetDeviceArray() {
        reset($this->_sql_tables_schema['devices']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['devices'])) {
            $pos = strpos(strtoupper($valid_format), 'DEFAULT');
            $value = "";
            if ($pos !== FALSE) {
                $value = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                if (("'" == substr($value,0,1)) && ("'" == substr($value,-1))) {
                    $value = substr($value,1,-1);
                }
            }
            $this->_device_data[$valid_key] = $value;
        }
    }


    function ResetGroupArray() {
        reset($this->_sql_tables_schema['groups']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['groups'])) {
            $pos = strpos(strtoupper($valid_format), 'DEFAULT');
            $value = "";
            if ($pos !== FALSE) {
                $value = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                if (("'" == substr($value,0,1)) && ("'" == substr($value,-1))) {
                    $value = substr($value,1,-1);
                }
            }
            $this->_group_data[$valid_key] = $value;
        }
    }


    function CleanPhoneNumber(
        $phone_number
    ) {
        $pn = trim(preg_replace('[\D]', "", $phone_number));
        // $pn_len = strlen($pn);
      
        if ('00' == substr($pn,0, 2)) {
            $pn = substr($pn, 2);
        } elseif ('0' == substr($pn,0, 1)) {
            $pn = $this->GetTelDefaultCountryCode() . substr($pn, 1);
        }
        return $pn;
    }


    function GetClassName() {
        return $this->_class;
    }


    function GetVersion() {
        return $this->_version;
    }


    function GetDate() {
        return $this->_date;
    }


    function GetVersionDate() {
        return $this->_version." (".$this->_date.")";
    }


    function GetFullVersionInfo() {
        return $this->_class." ".$this->_version." (".$this->_date.")";
    }


    function GetCopyright() {
        return $this->_copyright;
    }


    function GetWebsite() {
        return $this->_website;
    }


    function SetSourceTag(
        $value = ""
    ) {
        $this->_source_tag = trim($value);
    }


    function GetSourceTag() {
        return trim($this->_source_tag);
    }


    function SetSourceIp(
        $value = ""
    ) {
        $this->_source_ip = $value;
    }


    function GetSourceIp() {
        return $this->_source_ip;
    }


    function SetSourceMac(
        $value
    ) {
        $this->_source_mac = $value;
    }


    function GetSourceMac() {
        return $this->_source_mac;
    }


    function SetCallingIp(
        $value
    ) {
        $this->_calling_ip = $value;
    }


    function GetCallingIp() {
        return $this->_calling_ip;
    }


    function SetCallingMac(
        $value
    ) {
        $this->_calling_mac = $value;
    }


    function GetCallingMac() {
        return $this->_calling_mac;
    }


    function SetChapChallenge(
        $hex_value
    ) {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos) {
            $temp = $hex_value;
        } else {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_chap_challenge = strtolower($temp);
    }


    function GetChapChallenge() {
        return strtolower($this->_chap_challenge);
    }


    function SetChapPassword(
        $hex_value
    ) {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos) {
            $temp = $hex_value;
        } else {
            $temp = substr($hex_value, $pos+1);
        }
        
        if (32 < strlen($temp)) {
            $this->SetChapId(substr($temp, 0, 2));
            $temp = substr($temp, 2);
        }
        $this->_chap_password = strtolower($temp);
    }


    function GetChapPassword() {
        return strtolower($this->_chap_password);
    }


    function SetMsChapChallenge(
        $hex_value
    ) {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos) {
            $temp = $hex_value;
        } else {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_ms_chap_challenge = strtolower($temp);
    }


    function GetMsChapChallenge() {
        return strtolower($this->_ms_chap_challenge);
    }


    function SetMsChapResponse(
        $hex_value
    ) {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos) {
            $temp = $hex_value;
        } else {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_ms_chap_response = strtolower($temp);
    }


    function GetMsChapResponse() {
        return strtolower($this->_ms_chap_response);
    }


    function SetMsChap2Response(
        $hex_value
    ) {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos) {
            $temp = $hex_value;
        } else {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_ms_chap2_response = strtolower($temp);
    }


    function GetMsChap2Response() {
        return strtolower($this->_ms_chap2_response);
    }


    function SetChapId(
        $hex_value
    ) {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos) {
            $temp = $hex_value;
        } else {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_chap_id = strtolower($temp);
    }


    function GetChapId() {
        return strtolower($this->_chap_id);
    }


    function SetNtKey(
        $hex_value
    ) {
        $temp = $hex_value;
        if (16 == strlen($temp)) {
            $temp = bin2hex($temp);
        }
        $pos = strpos(strtolower($temp), 'x');
        if (FALSE !== $pos) {
            $temp = substr($temp, $pos+1);
        }
        if (32 != strlen($temp)) {
            $temp = '';
        }
        $this->_ms_nt_key = strtoupper($temp);
    }


    function GetNtKey() {
        $temp = $this->_ms_nt_key;
        if (16 == strlen($temp)) {
            $temp = bin2hex($temp);
        } elseif (32 != strlen($temp)) {
            $temp = '';
        }
        return strtoupper($temp);
    }


    function GetSmsProvidersArray() {
        return $this->_sms_providers_array;
    }


    function GetSmsProvidersList() {
        $providers_list = '';
        foreach($this->GetSmsProvidersArray() as $one_provider) {
            $providers_list.= (('' != $providers_list)?"\t":'');
            $providers_list.= $one_provider[1];
            $providers_list.= ('' != $one_provider[0])?' ('.$one_provider[0].')':'';
            $providers_list.= ('' != $one_provider[2])?', '.$one_provider[2]:'';
        }
        return $providers_list;
    }


    function SetSmsProvider(
        $value
    ) {
        $this->_config_data['sms_provider'] = $value;
    }


    function GetSmsProvider() {
        return $this->_config_data['sms_provider'];
    }


    function SetSmsOriginator(
        $value
    ) {
        $this->_config_data['sms_originator'] = $value;
    }


    function GetSmsOriginator() {
        return $this->_config_data['sms_originator'];
    }


    function SetTelDefaultCountryCode(
        $value
    ) {
        $this->_config_data['tel_default_country_code'] = $value;
    }


    function GetTelDefaultCountryCode() {
        return $this->_config_data['tel_default_country_code'];
    }


    function SetSmsUserkey(
        $value
    ) {
        $this->_config_data['sms_userkey'] = $value;
    }


    function GetSmsUserkey() {
        return $this->_config_data['sms_userkey'];
    }


    function SetSmsPassword(
        $value
    ) {
        $this->_config_data['sms_password'] = $value;
    }


    function GetSmsPassword() {
        return $this->_config_data['sms_password'];
    }


    function SetSmsApiId(
        $value
    ) {
        $this->_config_data['sms_api_id'] = $value;
    }


    function GetSmsApiId() {
        return $this->_config_data['sms_api_id'];
    }


    function SetDefaultRequestLdapPwd(
        $value
    ) {
        $this->_config_data['default_request_ldap_pwd'] = ((intval($value) > 0)?1:0);
    }


    function GetDefaultRequestLdapPwd() {
        return $this->_config_data['default_request_ldap_pwd'];
    }


    function IsDefaultRequestLdapPwd() {
        return (1 == ($this->_config_data['default_request_ldap_pwd']));
    }


    function SetDefaultRequestPrefixPin(
        $value
    ) {
        $this->_config_data['default_request_prefix_pin'] = ((intval($value) > 0)?1:0);
    }


    function GetDefaultRequestPrefixPin() {
        return $this->_config_data['default_request_prefix_pin'];
    }


    function IsDefaultRequestPrefixPin() {
        return (1 == ($this->_config_data['default_request_prefix_pin']));
    }


    function EnableLdapError() {
        $this->_last_ldap_error = TRUE;
    }


    function DisableLdapError() {
        $this->_last_ldap_error = FALSE;
    }


    function IsLdapError() {
        return $this->_last_ldap_error;
    }


    function SetLdapActivated(
        $value
    ) {
        $this->_config_data['ldap_activated'] = ((intval($value) > 0)?1:0);
    }


    function EnableLdapActivated() {
        $this->_config_data['ldap_activated'] = 1;
    }


    function DisableLdapActivated() {
        $this->_config_data['ldap_activated'] = 0;
    }


    function IsLdapActivated() {
        return (1 == ($this->_config_data['ldap_activated']));
    }


    function SetLdapSsl(
        $value
    ) {
        $this->_config_data['ldap_ssl'] = ((intval($value) > 0)?1:0);
    }


    function EnableLdapSsl() {
        $this->_config_data['ldap_ssl'] = 1;
    }


    function DisableLdapSsl() {
        $this->_config_data['ldap_ssl'] = 0;
    }


    function IsLdapSsl() {
        return (1 == ($this->_config_data['ldap_ssl']));
    }


    function SetLdapAccountSuffix(
        $value
    ) {
        $this->_config_data['ldap_account_suffix'] = $value;
    }


    function GetLdapAccountSuffix() {
        return $this->_config_data['ldap_account_suffix'];
    }


    function SetLdapCnIdentifier(
        $value
    ) {
        if ('' != trim($value)) {
            $this->_config_data['ldap_cn_identifier'] = trim($value);
        }
    }


    function GetLdapCnIdentifier() {
        return ($this->_config_data['ldap_cn_identifier']);
    }


    function SetLdapGroupCnIdentifier(
        $value
    ) {
        if ('' != trim($value)) {
            $this->_config_data['ldap_group_cn_identifier'] = trim($value);
        }
    }


    function GetLdapGroupCnIdentifier() {
        return ($this->_config_data['ldap_group_cn_identifier']);
    }


    function GetLdapFieldsArray() {
        if (1 == $this->GetLdapServerType()) { // Active Directory
            // "department" removed, while not used
            $ldap_fields = array($this->GetLdapCnIdentifier(),
                                 "mail",
                                 $this->GetLdapGroupAttribute(),
                                 "displayName",
                                 "description",
                                 "telephoneNumber",
                                 "primaryGroupID",
                                 "mobile",
                                 "msNPAllowDialin",
                                 "userAccountControl", // (userAccountControl & 2) -> Account disabled
                                 "ms-DS-User-Account-Control-Computed", // (ms-DS-User-Account-Control-Computed & 16) -> Account locked
                                 "accountExpires", // Expiration of the account in 100-nanosecond (10000000 x epoch time !)
                                 "distinguishedName"
                                );
        } else { // Generic LDAP, no attribute like "msNPAllowDialin"
            /*
             * shadowexpire: -1: not used, otherwise, number of days since 01.01.1970 when the account will be disabled
             * sambaAcctFlags (details: http://pig.made-it.com/samba-accounts.html):
             *  U    Regular user account.
             *  W    Workstation Trust Account.
             *  S    Server Trust Account.
             *  I    Domain Trust Account.
             *  M    MNS logon user account (Majority Node Set (MNS) logon account).
             *  H    Home directory required.
             *  N    No password required. This means the account has no password.
             *  X    Password does not expire.
             *  D    Account disabled.
             *  T    Temporary duplicate of other account.
             *  L    The account has been automatically locked.
             */
            $ldap_fields = array($this->GetLdapCnIdentifier(),
                                 "mail",
                                 $this->GetLdapGroupAttribute(),
                                 "displayName",
                                 "description",
                                 "gecos",
                                 "telephoneNumber",
                                 "gidNumber",
                                 "mobile",
                                 "sambaAcctFlags",
                                 "shadowExpire",
                                 "distinguishedName"
                                );
        }
        return ($ldap_fields);
    }


    function SetLdapBaseDn(
        $value
    ) {
        $this->_config_data['ldap_base_dn'] = $value;
    }


    function GetLdapBaseDn() {
        return $this->_config_data['ldap_base_dn'];
    }


    function SetLdapBindDn(
        $value
    ) {
        $this->_config_data['ldap_bind_dn'] = $value;
    }


    function GetLdapBindDn() {
        return encode_utf8_if_needed($this->_config_data['ldap_bind_dn']);
    }


    function SetLdapDomainControllers(
        $value
    ) {
        $this->_config_data['ldap_domain_controllers'] = trim($value);
    }


    function GetLdapDomainControllers() {
        return $this->_config_data['ldap_domain_controllers'];
    }


    function GetLdapPrimaryController() {
        $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()));
        $controllers_array = explode(" ",$this->GetLdapDomainControllers());
        return trim(isset($controllers_array[0])?$controllers_array[0]:'');
    }


    function GetLdapSecondaryController() {
        $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()));
        $controllers_array = explode(" ",$this->GetLdapDomainControllers());
        return trim(isset($controllers_array[1])?$controllers_array[1]:'');
    }


    function SetLdapInGroup(
        $value
    ) {
        $this->_config_data['ldap_in_group'] = $value;
    }


    function GetLdapInGroup() {
        return $this->_config_data['ldap_in_group'];
    }


    function SetLdapGroupAttribute(
        $value
    ) {
        if ('' != trim($value)) {
            $this->_config_data['ldap_group_attribute'] = trim($value);
        }
    }


    function GetLdapGroupAttribute() {
        return ($this->_config_data['ldap_group_attribute']);
    }


    function SetLdapServerPassword(
        $value
    ) {
        $this->_config_data['ldap_server_password'] = $value;
    }


    function GetLdapServerPassword() {
        return encode_utf8_if_needed($this->_config_data['ldap_server_password']);
    }


    function SetLdapPort(
        $value
    ) {
        $this->_config_data['ldap_port'] = intval($value);
    }


    function GetLdapPort() {
        return $this->_config_data['ldap_port'];
    }


    function SetLdapServerType(
        $value,
        $default_parameters = false
    ) {
        $this->_config_data['ldap_server_type'] = intval($value);
        
        // These values are not in the options for now
        if (1 == $value) { // Active Directory
            $this->SetLdapGroupCnIdentifier('sAMAccountName');
        } else { // Generic LDAP
            $this->SetLdapGroupCnIdentifier('cn');
        }
        if ($default_parameters) {
            if (1 == $value) { // Active Directory
                $this->SetLdapCnIdentifier('sAMAccountName');
            } else { // Generic LDAP
                $this->SetLdapCnIdentifier('uid');
            }
        }
    }


    function GetLdapServerType() {
        return $this->_config_data['ldap_server_type'];
    }


    function SetLdapTimeLimit(
        $value
    ) {
        $this->_config_data['ldap_time_limit'] = intval($value);
    }


    function GetLdapTimeLimit() {
        return $this->_config_data['ldap_time_limit'];
    }


    function SetLdapNetworkTimeout(
        $value
    ) {
        $this->_config_data['ldap_network_timeout'] = intval($value);
    }


    function GetLdapNetworkTimeout() {
        return $this->_config_data['ldap_network_timeout'];
    }


    function SetLdapHashCacheTime(
        $value
    ) {
        $this->_config_data['ldap_hash_cache_time'] = intval($value);
    }


    function GetLdapHashCacheTime() {
        return $this->_config_data['ldap_hash_cache_time'];
    }


    function SetSmsMessage(
        $value
    ) {
        $this->_config_data['sms_message_prefix'] = $value;
    }


    function GetSmsMessage() {
        return $this->_config_data['sms_message_prefix'];
    }


    function SetSmsDigits(
        $value
    ) {
        $this->_config_data['sms_digits'] = intval($value);
    }


    function GetSmsDigits() {
        return $this->_config_data['sms_digits'];
    }


    function SetSmsTimeout(
        $value
    ) {
        $this->_config_data['sms_timeout'] = intval($value);
    }


    function GetSmsTimeout() {
        return $this->_config_data['sms_timeout'];
    }


    function SetConfigAttribute(
        $attribute,
        $value
    ) {
        $result = FALSE;
        if ($this->IsOptionInSchema('config',$attribute)) {
            $this->_config_data[$attribute] = $value;
            $result = TRUE;
        }
        return $result;
    }


    function GetConfigAttribute(
        $attribute
    ) {
        return isset($this->_config_data[$attribute])?$this->_config_data[$attribute]:'';
    }


    function SetMaxTimeWindow(
        $time_window
    ) {
        $this->_config_data['max_time_window'] = intval($time_window);
    }


    function GetMaxTimeWindow() {
        return $this->_config_data['max_time_window'];
    }


    function SetMaxTimeResyncWindow(
        $time_resync_window
    ) {
        $this->_config_data['max_time_resync_window'] = intval($time_resync_window);
    }


    function GetMaxTimeResyncWindow() {
        return $this->_config_data['max_time_resync_window'];
    }


    function SetMaxEventWindow(
        $event_window
    ) {
        $this->_config_data['max_event_window'] = intval($event_window);
    }


    function GetMaxEventWindow() {
        return $this->_config_data['max_event_window'];
    }


    function SetMaxEventResyncWindow(
        $event_resync_window
    ) {
        $this->_config_data['max_event_resync_window'] = intval($event_resync_window);
    }


    function GetMaxEventResyncWindow() {
        return $this->_config_data['max_event_resync_window'];
    }


    function SetMaxBlockFailures(
        $max_failures
    ) {
        $this->_config_data['max_block_failures'] = $max_failures;
    }


    function GetMaxBlockFailures() {
        return $this->_config_data['max_block_failures'];
    }


    function SetServerCacheLevel(
        $value
    ) {
        $this->_config_data['server_cache_level'] = intval($value);
    }


    function GetServerCacheLevel() {
        return intval($this->_config_data['server_cache_level']);
    }


    function SetServerCacheLifetime(
        $value
    ) {
        $this->_config_data['server_cache_lifetime'] = intval($value);
    }


    function GetServerCacheLifetime() {
        return intval($this->_config_data['server_cache_lifetime']);
    }


    function SetServerChallenge(
        $value
    ) {
        $this->_server_challenge = $value;
    }


    function GetServerChallenge() {
        return $this->_server_challenge;
    }


    function SetServerSecret(
        $value
    ) {
        $this->_config_data['server_secret'] = $value;
    }


    function GetServerSecret() {
        return $this->_config_data['server_secret'];
    }


    function SetServerType(
        $value
    ) {
        $this->_config_data['server_type'] = $value;
    }


    function GetServerType() {
        return $this->_config_data['server_type'];
    }


    function SetServerTimeout(
        $value
    ) {
        $this->_config_data['server_timeout'] = intval($value);
    }


    function GetServerTimeout() {
        return intval($this->_config_data['server_timeout']);
    }


    function SetServerUrl(
        $value
    ) {
        $this->_config_data['server_url'] = trim($value);
    }


    function GetServerUrl() {
        return trim($this->_config_data['server_url']);
    }


    function SetSelfRegistration(
        $value
    ) {
        $this->_config_data['self_registration'] = ((intval($value) > 0)?1:0);
    }


    function EnableSelfRegistration() {
        $this->_config_data['self_registration'] = 1;
    }


    function DisableSelfRegistration() {
        $this->_config_data['self_registration'] = 0;
    }


    function IsSelfRegistrationEnabled() {
        return (1 == ($this->_config_data['self_registration']));
    }


    function SetAutoResync(
        $value
    ) {
        $this->_config_data['auto_resync'] = ((intval($value) > 0)?1:0);
    }


    function EnableAutoResync() {
        $this->_config_data['auto_resync'] = 1;
    }


    function DisableAutoResync() {
        $this->_config_data['auto_resync'] = 0;
    }


    function IsAutoResync() {
        return (1 == ($this->_config_data['auto_resync']));
    }


    function SetCacheData(
        $value
    ) {
        $this->_config_data['cache_data'] = ((intval($value) > 0)?1:0);
    }


    function EnableCacheData() {
        $this->_config_data['cache_data'] = 1;
    }


    function DisableCacheData() {
        $this->_config_data['cache_data'] = 0;
    }


    function IsCacheData() {
        return (1 == ($this->_config_data['cache_data']));
    }


    function SetCaseSensitiveUsers() {
        $this->_config_data['case_sensitive_users'] = ((intval($value) > 0)?1:0);
    }


    function EnableCaseSensitiveUsers() {
        $this->_config_data['case_sensitive_users'] = 1;
    }


    function DisableCaseSensitiveUsers() {
        $this->_config_data['case_sensitive_users'] = 0;
    }


    function IsCaseSensitiveUsers() {
        return (1 == ($this->_config_data['case_sensitive_users']));
    }


    function SetNtpServer(
        $ntp_server
    ) {
        $this->_config_data['ntp_server'] = $ntp_server;
    }


    function GetNtpServer() {
        return trim($this->_config_data['ntp_server']);
    }


    function SetRadiusReplyAttributor(
        $radius_reply_attributor
    ) {
        $this->_config_data['radius_reply_attributor'] = $radius_reply_attributor;
    }


    function GetRadiusReplyAttributor() {
        return ($this->_config_data['radius_reply_attributor']);
    }


    function SetRadiusReplySeparator(
        $radius_reply_separator
    ) {
        switch (strtolower($radius_reply_separator)) {
            case 'colon':
                $radius_reply_separator = ':';
                break;
            case 'comma':
                $radius_reply_separator = ',';
                break;
            case 'cr':
                $radius_reply_separator = chr(13);
                break;
            case 'crlf':
                $radius_reply_separator = chr(13).chr(10);
                break;
            case 'lf':
                $radius_reply_separator = chr(10);
                break;
            case 'semicolon':
                $radius_reply_separator = ';';
                break;
        }
        $this->_config_data['radius_reply_separator_hex'] = bin2hex($radius_reply_separator);
    }


    function GetRadiusReplySeparator()
    {
        return hex2bin($this->_config_data['radius_reply_separator_hex']);
    }


    function SetTimezone($timezone)
    {
        $this->_config_data['timezone'] = $timezone;
        if (function_exists('date_default_timezone_set'))
        {
            date_default_timezone_set($timezone);
        }
    }


    function GetTimezone()
    {
        $timezone = trim(isset($this->_config_data['timezone'])?$this->_config_data['timezone']:'');
        if (('' != $timezone) && (function_exists('date_default_timezone_set')))
        {
            date_default_timezone_set($timezone);
        }
        return $timezone;
    }


    function SetSmtpAuth($value)
    {
        $this->_config_data['smtp_auth'] = ((intval($value) > 0)?1:0);
    }


    function GetSmtpAuth()
    {
        return (($this->_config_data['smtp_auth'] > 0)?1:0);
    }


    function IsSmtpAuth()
    {
        return (1 == ($this->_config_data['smtp_auth']));
    }


    function SetSmtpPassword($value)
    {
        $this->_config_data['smtp_password'] = $value;
    }


    function GetSmtpPassword()
    {
        return $this->_config_data['smtp_password'];
    }


    function SetSmtpPort($value)
    {
        $this->_config_data['smtp_port'] = intval($value);
    }


    function GetSmtpPort()
    {
        return intval($this->_config_data['smtp_port']);
    }


    function SetSmtpSender($value)
    {
        $this->_config_data['smtp_sender'] = $value;
    }


    function GetSmtpSenderName()
    {
        return $this->_config_data['smtp_sender_name'];
    }


    function SetSmtpSenderName($value)
    {
        $this->_config_data['smtp_sender_name'] = $value;
    }


    function GetSmtpSender()
    {
        return $this->_config_data['smtp_sender'];
    }


    function SetSmtpServer($value)
    {
        $this->_config_data['smtp_server'] = $value;
    }


    function GetSmtpServer()
    {
        return $this->_config_data['smtp_server'];
    }


    function SetSmtpSsl($value)
    {
        $this->_config_data['smtp_ssl'] = ((intval($value) > 0)?1:0);
    }


    function GetSmtpSsl()
    {
        return (($this->_config_data['smtp_ssl'] > 0)?1:0);
    }


    function SetSmtpUsername($value)
    {
        $this->_config_data['smtp_username'] = $value;
    }


    function GetSmtpUsername()
    {
        return $this->_config_data['smtp_username'];
    }


    function SetSyslogFacility($value)
    {
        $this->_config_data['syslog_facility'] = $value;
    }


    function GetSyslogFacility()
    {
        return $this->_config_data['syslog_facility'];
    }


    function SetSyslogLevel($value)
    {
        $this->_config_data['syslog_level'] = intval($value);
    }


    function GetSyslogLevel()
    {
        return intval($this->_config_data['syslog_level']);
    }


    function SetSysLogPort($value)
    {
        $this->_config_data['syslog_port'] = intval($value);
    }


    function GetSysLogPort()
    {
        return intval($this->_config_data['syslog_port']);
    }


    function SetSysLogServer($value)
    {
        $this->_config_data['syslog_server'] = $value;
    }


    function GetSysLogServer()
    {
        return $this->_config_data['syslog_server'];
    }


    function DefineMySqlConnection($sql_server, $sql_user, $sql_passwd, $sql_db, $sql_log_table = NULL, $sql_users_table = NULL, $sql_tokens_table = NULL)
    /**
     * @brief   DEPRECATED: Define the SQL parameters for the MySQL backend
     *
     * @param   string  $sql_server        MySQL server
     * @param   string  $sql_user          MySQL user
     * @param   string  $sql_passwd        MySQL password
     * @param   string  $sql_db            MySQL database
     * @param   string  $sql_log_table     MySQL log table
     * @param   string  $sql_users_table   MySQL users table
     * @param   string  $sql_tokens_table  MySQL tokens table
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.0.1
     * @date    2013-06-09
     * @since   2010-12-18
     */
    {
        // Backend storage type
        $this->SetBackendType('mysql');
        $this->SetSqlServer($sql_server);
        $this->SetSqlUsername($sql_user);
        $this->SetSqlPassword($sql_passwd);
        $this->SetSqlDatabase($sql_db);
        
        // If table names are not defined, we keep the default value defined in the class constructor.
        if (NULL !== $sql_log_table)
        {
            $this->SetSqlTableName('log', $sql_log_table);
        }
        if (NULL !== $sql_users_table)
        {
            $this->SetSqlTableName('users', $sql_users_table);
        }
        if (NULL !== $sql_tokens_table)
        {
            $this->SetSqlTableName('tokens', $sql_tokens_table);
        }
    }


    /*********************************************************************
     *
     * Name: ComputeMotp
     * Short description: Compute the mOTP result
     *
     * Creation 2010-06-07
     * Update 2013-12-23
     * @package multiotp
     * @version 2.0.0
     * @author SysCo/al
     *
     * @param   string  $seed_and_pin  Key used to compute the mOTP result (seed is in hexa)
     * @param   int     $timestep      Timestep used to calculate the token
     * @param   int     $token_size    Token size
     * @return  string                 mOTP result
     *
     *********************************************************************/
    function ComputeMotp($seed_and_pin, $timestep, $token_size)
    {
        return strtolower(substr(md5($timestep.$seed_and_pin),0,$token_size));
    }


    /*********************************************************************
     *
     * Name: GenerateOathHotp
     * Short description: Compute the HOTP token
     *
     * Creation 2013-11-26
     * Update 2013-12-23
     * @package multiotp
     * @version 4.1.0
     * @author SysCo/al
     *
     * @param   string  $key      Key used to compute the HOTP token
     * @param   int     $counter  Counter position
     * @param   int     $length   Token size
     * @return  string            HOTP token
     *
     *********************************************************************/
    function GenerateOathHotp($key, $counter, $length = 6, $hash_algo = 'HMAC-SHA1')
    {
        return $this->ComputeOathTruncate($this->ComputeOathHotp($key, $counter, $hash_algo),$length);
    }    


    /*********************************************************************
     *
     * Name: ComputeOathHotp
     * Short description: Compute the OATH defined hash
     *
     * Creation 2010-06-07
     * Update 2010-07-19
     * @package multiotp
     * @version 3.0.0
     * @author SysCo/al
     *
     * @param   string  $key      Key used to compute the OATH hash
     * @param   int     $counter  Counter position
     * @return  string            Full OATH hash
     *
     *********************************************************************/
    function ComputeOathHotp($key, $counter, $hash_algo = 'HMAC-SHA1')
    {
        // Counter
        //the counter value can be more than one byte long, so we need to go multiple times
        $cur_counter = array(0,0,0,0,0,0,0,0);
        for($i=7;$i>=0;$i--)
        {
            $cur_counter[$i] = pack ('C*', $counter);
            $counter = $counter >> 8;
        }
        $bin_counter = implode($cur_counter);
        // Pad to 8 chars
        if (strlen ($bin_counter) < 8)
        {
            $bin_counter = str_repeat(chr(0), 8 - strlen($bin_counter)) . $bin_counter;
        }

        // HMAC hash
        if ('HMAC-SHA512' == strtoupper($hash_algo))
        {
            $hash = hash_hmac('sha512', $bin_counter, $key);
        }
        elseif ('HMAC-SHA256' == strtoupper($hash_algo))
        {
            $hash = hash_hmac('sha256', $bin_counter, $key);
        }
        elseif ('HMAC-MD5' == strtoupper($hash_algo))
        {
            $hash = hash_hmac('md5', $bin_counter, $key);
        }
        else // if ('HMAC-SHA1' == strtoupper($hash_algo))
        {
            $hash = hash_hmac('sha1', $bin_counter, $key);
        }
        return $hash;
    }


    /*********************************************************************
     *
     * Name: ComputeOathTruncate
     * Short description: Truncate the result as defined by the OATH
     *
     * Creation 2010-06-07
     * Update   2014-01-15
     * @package multiotp
     * @version 4.1.1
     * @author SysCo/al
     *
     * @param   string  $hash     Full OATH hash to be truncated
     * @param   int     $length   Length of the result token
     * @return  string            Truncated OATH hash
     *
     *********************************************************************/
    function ComputeOathTruncate($hash, $length = 6)
    {
        // Convert hash to decimal
        foreach(str_split($hash,2) as $hex)
        {
            $hmac_result[]=hexdec($hex);
        }

        // Find offset
        $offset = $hmac_result[(strlen($hash)/2)-1] & 0xf;

        // Adapted algorithm ("substr -10" instead of "% pow(10,$length)")
        $result = substr(str_repeat('0',$length).
                         sprintf('%u',
                                      (($hmac_result[$offset+0] & 0x7f) << 24 ) |
                                      (($hmac_result[$offset+1] & 0xff) << 16 ) |
                                      (($hmac_result[$offset+2] & 0xff) << 8 ) |
                                       ($hmac_result[$offset+3] & 0xff)
                                ),
                         -$length);
        return $result;
    }


    function CalculateChapPassword($secret, $hex_chap_id = '', $hex_chap_challenge = '')
    {
        
        if ($hex_chap_id != '')
        {
            $id = hex2bin($hex_chap_id);
        }
        elseif (32 < strlen($this->GetChapPassword()))
        {
            $id = hex2bin(substr($this->GetChapPassword(),0,2));
        }
        else
        {
            $id = hex2bin($this->GetChapId());
        }
        
        if ($hex_chap_challenge != '')
        {
            $challenge = hex2bin($hex_chap_challenge);
        }
        else
        {
            $challenge = hex2bin($this->GetChapChallenge());
        }
        
        return md5($id.$secret.$challenge);
    }


    function Convert2Unicode($value) 
    {
        $unicode = '';
        $string = (string) $value;
        for ($i = 0; $i < strlen($string); $i++)
        {
            $asc = ord($string{$i}) << 8;
            $unicode .= sprintf("%X", $asc);
        }
        return pack('H*', $unicode);
    }


    function Padding7to8($value)
    {
        static $odd_parity = array(  1,  1,  2,  2,  4,  4,  7,  7,  8,  8, 11, 11, 13, 13, 14, 14,
                                    16, 16, 19, 19, 21, 21, 22, 22, 25, 25, 26, 26, 28, 28, 31, 31,
                                    32, 32, 35, 35, 37, 37, 38, 38, 41, 41, 42, 42, 44, 44, 47, 47,
                                    49, 49, 50, 50, 52, 52, 55, 55, 56, 56, 59, 59, 61, 61, 62, 62,
                                    64, 64, 67, 67, 69, 69, 70, 70, 73, 73, 74, 74, 76, 76, 79, 79,
                                    81, 81, 82, 82, 84, 84, 87, 87, 88, 88, 91, 91, 93, 93, 94, 94,
                                    97, 97, 98, 98,100,100,103,103,104,104,107,107,109,109,110,110,
                                   112,112,115,115,117,117,118,118,121,121,122,122,124,124,127,127,
                                   128,128,131,131,133,133,134,134,137,137,138,138,140,140,143,143,
                                   145,145,146,146,148,148,151,151,152,152,155,155,157,157,158,158,
                                   161,161,162,162,164,164,167,167,168,168,171,171,173,173,174,174,
                                   176,176,179,179,181,181,182,182,185,185,186,186,188,188,191,191,
                                   193,193,194,194,196,196,199,199,200,200,203,203,205,205,206,206,
                                   208,208,211,211,213,213,214,214,217,217,218,218,220,220,223,223,
                                   224,224,227,227,229,229,230,230,233,233,234,234,236,236,239,239,
                                   241,241,242,242,244,244,247,247,248,248,251,251,253,253,254,254);

        $raw = '';
        for ($i = 0; $i < strlen($value); $i++)
        {
            $raw .= sprintf('%08s', decbin(ord($value{$i})));
        }

        $str1 = explode('-', substr(chunk_split($raw, 7, '-'), 0, -1));
        $x = '';
        foreach($str1 as $char)
        {
            $x .= sprintf('%02s', dechex($odd_parity[bindec($char. '0')]));
        }

        return pack('H*', $x);
    }


    function DesHashEcb($clear)
    {
        $cipher = new Crypt_DES(CRYPT_DES_MODE_ECB);
        $cipher->setKey($this->Padding7to8($clear));
        return $cipher->encrypt('KGS!@#$%');
    }


    function LmPasswordHash($clear)
    {
        $clear = substr(strtoupper($clear.str_repeat("\0",14)), 0, 14);
        return substr($this->DesHashEcb(substr($clear, 0, 7)),0,8).substr($this->DesHashEcb(substr($clear, 7, 7)),0,8);
    }


    function NtPasswordHash($clear) 
    {
        return pack('H*',hash('md4', $this->Convert2Unicode($clear)));
    }


    function NtPasswordHashHash($hash) 
    {
        return pack('H*',hash('md4', $hash));
    }


    function CalculateMsChapResponse($secret, $hex_mschap_challenge = '', $hex_mschap_response = '')
    {
        $temp_challenge = ('' != $hex_mschap_challenge)?$hex_mschap_challenge:$this->GetMsChapChallenge();
        $pos = strpos(strtolower($temp_challenge), 'x');
        if (FALSE !== $pos)
        {
            $temp_challenge = substr($temp_challenge, $pos+1);
        }

        $temp_response  = ('' != $hex_mschap_response)?$hex_mschap_response:$this->GetMsChapResponse();
        $this->SetMsChapResponse($temp_response);
        $pos = strpos(strtolower($temp_response), 'x');
        if (FALSE !== $pos)
        {
            $temp_response = substr($temp_response, $pos+1);
        }

        $mschap_challenge = hex2bin($temp_challenge);
        $mschap_response  = hex2bin($temp_response);

        if (24 == strlen($mschap_response))
        {
            $mschap_response = str_repeat("\0",2+24).$mschap_response;
        }
        
        $id          = substr($mschap_response,0,1);
        $flag        = ord(substr($mschap_response,1,1));   // 1 = use NT-Response, 0 = use LM-Response
        $lm_response = substr($mschap_response,2,24);  // LM-Response
        $nt_response = substr($mschap_response,26,24); // NT-Response
        
        if (1 == $flag)
        {
            $hash = $this->NtPasswordHash($secret);
            $response = $nt_response;
            $hash_for_nt_key = $hash;
            
        }
        else
        {
            $hash = $this->LmPasswordHash($secret);
            $response = $lm_response;
            $hash_for_nt_key = $this->NtPasswordHash($secret);
        }

        $this->SetNtKey(bin2hex($this->NtPasswordHashHash($hash)));

        $challenge = $mschap_challenge;

        $hash = substr($hash.str_repeat("\0",21), 0, 21);

        $cipher = new Crypt_DES(CRYPT_DES_MODE_ECB);
        $cipher->setKey($this->Padding7to8(substr($hash, 0, 7)));
        $part1 = substr($cipher->encrypt(substr($challenge,0,8)),0,8);
        
        $cipher->setKey($this->Padding7to8(substr($hash, 7, 7)));
        $part2 = substr($cipher->encrypt(substr($challenge,0,8)),0,8);
        
        $cipher->setKey($this->Padding7to8(substr($hash, 14, 7)));
        $part3 = substr($cipher->encrypt(substr($challenge,0,8)),0,8);
        
        $calculated_response = $part1.$part2.$part3;
        
        if ($calculated_response == $response)
        {
            $result = strtolower(bin2hex($mschap_response));
        }
        else
        {
            $result = 'Error: '.bin2hex($calculated_response).' instead of '.bin2hex($nt_response);
        }
        return $result;
    }


    function CheckMsChapResponse($secret, $hex_mschap_challenge = '', $hex_mschap_response = '')
    {
        $result = $this->CalculateMsChapResponse($secret, $hex_mschap_challenge, $hex_mschap_response);
        
        return ($this->GetMsChapResponse() == strtolower($result));
    }


    function CalculateMsChap2Response($user, $secret, $domain = "", $hex_mschap_challenge = '', $hex_mschap2_response = '')
    {
        $temp_challenge = ('' != $hex_mschap_challenge)?$hex_mschap_challenge:$this->GetMsChapChallenge();
        $pos = strpos(strtolower($temp_challenge), 'x');
        if (FALSE !== $pos)
        {
            $temp_challenge = substr($temp_challenge, $pos+1);
        }

        $temp_response  = ('' != $hex_mschap2_response)?$hex_mschap2_response:$this->GetMsChap2Response();
        $this->SetMsChap2Response($temp_response);
        $pos = strpos(strtolower($temp_response), 'x');
        if (FALSE !== $pos)
        {
            $temp_response = substr($temp_response, $pos+1);
        }

        $mschap_challenge = hex2bin($temp_challenge);
        $mschap2_response = hex2bin($temp_response);

        if (24 == strlen($mschap2_response))
        {
            $mschap2_response = str_repeat("\0",2+24).$mschap2_response;
        }

        $id             = substr($mschap2_response,0,1);
        $flag           = ord(substr($mschap2_response,1,1)); // 0 (reserved for future use)
        $peer_challenge = substr($mschap2_response,2,16);
        $empty          = substr($mschap2_response,18,8);
        $nt_response    = substr($mschap2_response,26,24);
        
        $hash = $this->NtPasswordHash($secret);

        $this->SetNtKey(bin2hex($this->NtPasswordHashHash($hash)));

        /*
        $kr = hash_hmac('md5',
                        pack('H*',hash('md4', $hash)),
                        $this->Convert2Unicode(strtoupper($user).$domain)
                       );
                       
        $nt_response_sig = hash_hmac('md5',
                                     $kr,
                                     $nt_response
                                    );

        $nt_key = hash_hmac('md5',
                            $kr,
                            $nt_response_sig
                           );

        $this->SetNtKey($nt_key);
        */

        if (8 == strlen($mschap_challenge))
        {
            $challenge = $mschap_challenge;
        }
        else
        {
            $challenge = substr(pack('H*',hash('sha1', $peer_challenge.$mschap_challenge.$user)), 0, 8);
        }

        $hash = substr($hash.str_repeat("\0",21), 0, 21);
        
        $cipher = new Crypt_DES(CRYPT_DES_MODE_ECB);
        $cipher->setKey($this->Padding7to8(substr($hash, 0, 7)));
        $part1 = substr($cipher->encrypt(substr($challenge,0,8)),0,8);
        
        $cipher->setKey($this->Padding7to8(substr($hash, 7, 7)));
        $part2 = substr($cipher->encrypt(substr($challenge,0,8)),0,8);
        
        $cipher->setKey($this->Padding7to8(substr($hash, 14, 7)));
        $part3 = substr($cipher->encrypt(substr($challenge,0,8)),0,8);
        
        $calculated_response = $part1.$part2.$part3;

        if ($calculated_response == $nt_response)
        {
            $result = strtolower(bin2hex($mschap2_response));
        }
        else
        {
            $result = 'Error: '.bin2hex($calculated_response).' instead of '.bin2hex($nt_response);
        }
        return $result;
    }


    function CheckMsChap2Response($user, $secret, $domain = "", $hex_mschap_challenge = '', $hex_mschap2_response = '')
    {
        $result = $this->CalculateMsChap2Response($user, $secret, $domain, $hex_mschap_challenge, $hex_mschap2_response);
        
        return ($this->GetMsChap2Response() == strtolower($result));
    }


    function SetEncryptionKey($key, $read_config = TRUE)
    {
        $this->_encryption_key = $key;
        if ($read_config)
        {
            $this->ReadConfigData();
        }
    }


    function GetEncryptionKey()
    {
        return $this->_encryption_key;
    }


    function CalculateControlHash($value_to_hash)
    {
        return strtoupper(md5("CaLcUlAtE".$value_to_hash."cOnTrOlHaSh"));
    }


    function Encrypt($key, $value, $encryption_key)
    {
        $result = '';
        if (strlen($encryption_key) > 0)
        {
            if (0 < strlen($value))
            {
                for ($i=0;  $i < strlen($value); $i++)
                {
                    $encrypt_char = ord(substr($encryption_key,$i % strlen($encryption_key),1));
                    $key_char = ord(substr($key,$i % strlen($key),1));
                    $result .= chr($encrypt_char^$key_char^ord(substr($value,$i,1)));
                }
                $result = base64_encode($result);
            }
        }
        else
        {
            $result = $value;
        }
        return $result;
    }


    function Decrypt($key, $value, $encryption_key)
    /**
     * @brief   Decrypt the encrypted value of a label using an encryption key.
     *
     * @param   string  $key             Label of the value.
     * @param   string  $value           Encrypted value.
     * @param   string  $encryption_key  Encryption key.
     * @retval  string                   Decrypted value.
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 1.1.0
     * @date    2013-11-02
     */
    {
        $result = '';
        if (strlen($encryption_key) > 0)
        {
            if (0 < strlen($value))
            {
                $value_to_decrypt = base64_decode($value);
                for ($i=0;  $i < strlen($value_to_decrypt); $i++)
                {
                    $encrypt_char = ord(substr($encryption_key,$i % strlen($encryption_key),1));
                    $key_char = ord(substr($key,$i % strlen($key),1));
                    $result .= chr($encrypt_char^$key_char^ord(substr($value_to_decrypt,$i,1)));
                }
            }
        }
        else
        {
            $result = $value;
        }
        return $result;
    }


    function SetMaxDelayedFailures($failures)
    {
        $this->_config_data['max_delayed_failures'] = $failures;
    }


    function GetMaxDelayedFailures()
    {
        return $this->_config_data['max_delayed_failures'];
    }


    function SetMaxDelayedTime($seconds)
    {
        $this->_config_data['failure_delayed_time'] = $seconds;
    }


    function GetMaxDelayedTime()
    {
        return $this->_config_data['failure_delayed_time'];
    }


    function SetActualVersion($value)
    {
        $this->_config_data['actual_version'] = $value;
    }


    function GetActualVersion()
    {
        return $this->_config_data['actual_version'];
    }


    /*********************************************************************
     *
     * Name: CreateUser
     * Short description: Create a new user
     *
     * Creation 2013-02-08
     * Update 2014-03-03
     * @package multiotp
     * @version 4.2.2
     * @author SysCo/al
     *
     * @param   string  $user      
     * @param   int     $prefix_pin_needed [-1|0|1]
     * @param   string  $algorithm
     * @param   string  $seed
     * @param   string  $pin
     * @param   string  $number_of_digits
     * @param   string  $time_interval_or_next_event
     * @param   string  $email
     * @param   string  $sms
     * @param   string  $description
     * @param   string  $group
     * @param   string  $token_algo_suite
     * @param   int     $activated [1|0]
     * @param   int     $synchronized [0|1]
     * @return  boolean
     *
     *********************************************************************/
    function CreateUser($user,
                        $prefix_pin_needed = -1,
                        $algorithm = 'totp',
                        $seed = '',
                        $pin = '',
                        $number_of_digits = 6,
                        $time_interval_or_next_event = -1,
                        $email = '',
                        $sms = '',
                        $description = '',
                        $group = '',
                        $token_algo_suite = '',
                        $activated = 1,
                        $synchronized = 0,
                        $ldap_pwd_needed = -1,
                        $automatically = FALSE
                       )
    {
        $result = FALSE;
        if ('' != trim($user))
        {
            if ((intval($ldap_pwd_needed) < 0) && (1 == $synchronized))
            {
                $request_ldap_pwd = $this->GetDefaultRequestLdapPwd();
            }
            else
            {
                $request_ldap_pwd = intval($ldap_pwd_needed);
            }
            if (intval($prefix_pin_needed) < 0)
            {
                $request_prefix_pin = $this->GetDefaultRequestPrefixPin();
            }
            else
            {
                $request_prefix_pin = intval($prefix_pin_needed);
            }
            if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user))
            {
                $result = FALSE; // ERROR: User already exists, or user is not set
                if ('' == $user)
                {
                    $this->WriteLog("Error: User is not set", FALSE, FALSE, 21, 'User', '');
                }
                else
                {
                    $this->WriteLog("Error: User ".$user." already exists", FALSE, FALSE, 22, 'User', $user);
                }
            }
            else
            {
                $this->SetUser($user);
                $this->SetUserPrefixPin($request_prefix_pin);
                $this->SetUserRequestLdapPassword($request_ldap_pwd);
                $this->SetUserAlgorithm($algorithm);
                $this->SetUserTokenAlgoSuite($token_algo_suite);

                $the_pin = $pin;
                if ('' == $the_pin)
                {
                    $the_pin = mt_rand(1000,9999);
                }
                $this->SetUserTokenNumberOfDigits($number_of_digits);

                /* This option is too long
                if (function_exists('openssl_random_pseudo_bytes')) {
                    $the_seed = (('' == $seed)?bin2hex(openssl_random_pseudo_bytes(20)):$seed);
                } else {
                */
                    $the_seed = (('' == $seed)?substr(md5(date("YmdHis").mt_rand(100000,999999)),0,20).substr(md5(mt_rand(100000,999999).date("YmdHis")),0,20):$seed);
                /* } */
                
                if ('hotp' == strtolower($algorithm))
                {
                    $next_event = ((-1 == $time_interval_or_next_event)?0:$time_interval_or_next_event);
                    $time_interval = 0;
                }
                else
                {
                    $next_event = 0;
                    $time_interval = ((-1 == $time_interval_or_next_event)?30:$time_interval_or_next_event);
                    if ("motp" == strtolower($algorithm))
                    {
                        // $the_seed = (('' == $seed)?substr(md5(date("YmdHis").mt_rand(100000,999999)),0,16):$seed);
                        $time_interval = 10;
                        if ((strlen($the_pin) < 4) || (0 == intval($the_pin)))
                        {
                            $the_pin = mt_rand(1000,9999);
                        }
                        $the_pin = substr($the_pin, 0, 4);
                    }
                }

                $this->SetUserPin($the_pin);
                $this->SetUserTokenSeed($the_seed);
                $this->SetUserTokenLastEvent($next_event - 1);
                $this->SetUserTokenTimeInterval($time_interval);
                
                $this_email = trim($email);
                if (('' == $this_email) && (FALSE !== strpos($user, '@')))
                {
                    $this_email = $user;
                }

                $this->SetUserEmail($this_email);
                $this->SetUserGroup(trim($group));
                $this->SetUserSms($sms);
                $this->SetUserDescription($description);
                $this->SetUserActivated($activated);
                $this->SetUserSynchronized($synchronized);
                $result = $this->WriteUserData($automatically); // WriteUserData write in the log file
            }
        }
        return $result;
    }


    /*********************************************************************
     * Name: CreateUserFromToken
     * Short description: Create a new user based on a token
     *
     * Creation 2013-02-17
     * Update 2013-12-23
     * @package multiotp
     * @version 4.1.0
     * @author SysCo/al
     *
     * @param   string  $user
     * @param   string  $token
     * @param   int     $email
     * @param   int     $sms
     * @param   string  $pin
     * @param   int     $prefix_pin_needed [0|1]
     * @param   string  $description
     * @param   string  $group
     * @return  int
     *********************************************************************/
    function CreateUserFromToken($user,
                                 $token,
                                 $email = '',
                                 $sms = '',
                                 $pin = '',
                                 $prefix_pin_needed = -1,
                                 $description = '',
                                 $group = '')
    {
        if (intval($prefix_pin_needed) < 0)
        {
            $request_prefix_pin = $this->GetDefaultRequestPrefixPin();
        }
        else
        {
            $request_prefix_pin = intval($prefix_pin_needed);
        }
        if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user))
        {
            $result = FALSE;
            if ('' == $user)
            {
                $this->WriteLog("Error: User is not set", FALSE, FALSE, 21, 'User', '');
            }
            else
            {
                $this->WriteLog("Error: User ".$user." already exists", FALSE, FALSE, 22, 'User', $user);
            }
        }
        elseif (!$this->ReadTokenData($token))
        {
            $result = FALSE;
            $this->WriteLog("Error: information about token ".$token." for user $user cannot be accessed", FALSE, FALSE, 29, 'Token', $token);
        }
        else
        {
            $this->AddTokenAttributedUsers($user);
            if (!$this->WriteTokenData())
            {
                $result = 28; // ERROR: Unable to write the changes in the file
                $this->WriteLog("Error: Unable to write the changes in the file for the token ".$this->GetToken(), FALSE, FALSE, $result, 'Token', $user);
            }
            else
            {
                $this->SetUser($user);
                $this->SetUserPrefixPin($request_prefix_pin);
                $this->SetUserKeyId($token);
                $this->SetUserTokenSerialNumber($token);
                $this->SetUserAlgorithm($this->GetTokenAlgorithm());
                $this->SetUserTokenAlgoSuite($this->GetTokenAlgoSuite());
                $this->SetUserTokenSeed($this->GetTokenSeed());
                $this->SetUserTokenNumberOfDigits($this->GetTokenNumberOfDigits());
                $this->SetUserTokenTimeInterval($this->GetTokenTimeInterval());
                $this->SetUserTokenLastEvent($this->GetTokenLastEvent());

                $the_pin = $pin;
                if ('' == $the_pin)
                {
                    $the_pin = mt_rand(1000,9999);
                }
                
                $this_email = trim($email);
                if (('' == $this_email) && (FALSE !== strpos($user, '@')))
                {
                    $this_email = $user;
                }
                
                $this->SetUserPin($the_pin);
                $this->SetUserEmail($this_email);
                $this->SetUserGroup(trim($group));
                $this->SetUserSms($sms);
                $this->SetUserDescription($description);
                
                $result = $this->WriteUserData(); // WriteUserData write in the log file
            }
        }
        return $result;
    }


    /**
     * @brief   Create the QRcode for the current user.
     *
     * @param   string  $user
     * @param   string  $display_name
     * @param   string  $file_name
     * @return  boolean or binary
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.0.6
     * @date    2013-08-25
     * @since   2013-02-17
     */
    function GetUserTokenQrCode($user = '', $display_name = '', $file_name = 'binary')
    {
        $result = FALSE;
        if (!function_exists('ImageCreate'))
        {
            $this->WriteLog("Error: PHP GD library is not installed", FALSE, FALSE, 299, 'System', '');
            return $result;
        }
        else
        {
            $data = $this->GetUserTokenUrlLink($user,$display_name);
            if($data)
            {
                $result = $this->qrcode($data, $file_name);
            }
            return $result;
        }
    }


    /**
     * @brief   Create the QRcode for the current token.
     *
     * @param   string  $token
     * @param   string  $display_name
     * @param   string  $file_name
     * @return  boolean or binary
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.1.1
     * @date    2014-01-19
     * @since   2013-02-18
     */
    function GetTokenQrCode($token = '', $display_name = '', $file_name = 'binary')
    {
        $result = FALSE;
        if (!function_exists('ImageCreate'))
        {
            $this->WriteLog("Error: PHP GD library is not installed", FALSE, FALSE, 299, 'System', '');
            return $result;
        }
        else
        {
            $data = $this->GetTokenUrlLink($token,$display_name);
            if($data)
            {
                $result = $this->qrcode($data, $file_name);
            }
            return $result;
        }
    }


    function GenerateHtmlQrCode($user = '', $alternate_html_template = '', $keep_qrcode_tags = FALSE)
    {
        $code_width=200;
        $code_height=200;

        if ('' != $user)
        {
            $this->SetUser($user);
        }

        $user = encode_utf8_if_needed($user);
        
        $descr = encode_utf8_if_needed($this->GetUserDescription());
        $descr = encode_utf8_if_needed(empty($descr) ? $user : $descr);

        if ('' != trim($alternate_html_template))
        {
            $html = $alternate_html_template;
        }
        else
        {
            // get template file
            $html = file_get_contents($this->GetTemplatesFolder().'template.html');
        }


        // Keep or clean LDAP information if not used
        // if ($this->IsUserSynchronized() && ('LDAP' == $this->GetUserSynchronizedChannel()) && $this->IsUserRequestLdapPasswordEnabled())
        if ($this->IsUserRequestLdapPasswordEnabled())
        {
            $request_ldap_pwd = TRUE;
            $html = preg_replace('/<!--\s*\{\/IfMultiotpUserLdapPwd\}\s*-->/', '', $html);
            $html = preg_replace('/<!--\s*\{IfMultiotpUserLdapPwd\}\s*-->/', '', $html);
        }
        else
        {
            $request_ldap_pwd = FALSE;
            $html = preg_replace('/<!--\s*\{\/IfMultiotpUserLdapPwd\}\s*-->/', ' -- {/IfMultiotpUserLdapPwd} -->', $html);
            $html = preg_replace('/<!--\s*\{IfMultiotpUserLdapPwd\}\s*-->/', '<!-- {/IfMultiotpUserLdapPwd} -- ', $html);
        }


        // Keep or clean pin information if not used
        if ($this->IsUserPrefixPin() && (!$request_ldap_pwd))
        {
            $html = preg_replace('/<!--\s*\{\/IfMultiotpUserPin\}\s*-->/', '', $html);
            $html = preg_replace('/<!--\s*\{IfMultiotpUserPin\}\s*-->/', '', $html);
        }
        else
        {
            $html = preg_replace('/<!--\s*\{\/IfMultiotpUserPin\}\s*-->/', ' -- {/IfMultiotpUserPin} -->', $html);
            $html = preg_replace('/<!--\s*\{IfMultiotpUserPin\}\s*-->/', '<!-- {/IfMultiotpUserPin} -- ', $html);
        }
        
        $token_serial = trim($this->GetUserTokenSerialNumber());
        if (('' == $token_serial) || (1 > strlen($token_serial)))
        {
            $html = preg_replace('/<!--\s*\{\/IfMultiotpUserTokenSerial\}\s*-->/', ' -- {/IfMultiotpUserTokenSerial} -->', $html);
            $html = preg_replace('/<!--\s*\{IfMultiotpUserTokenSerial\}\s*-->/', '<!-- {/IfMultiotpUserTokenSerial} -- ', $html);
        }
        else
        {
            $html = preg_replace('/<!--\s*\{\/IfMultiotpUserTokenSerial\}\s*-->/', '', $html);
            $html = preg_replace('/<!--\s*\{IfMultiotpUserTokenSerial\}\s*-->/', '', $html);
        }

        // Simplify current algorithm info
        $html = preg_replace('/IfMultiotpUserAlgorithm="[BCHIMOPTUY,]*'.strtoupper($this->GetUserAlgorithm()).'[BCHIMOPTUY,]*"}/', 'IfMultiotpUserAlgorithm="'.strtoupper($this->GetUserAlgorithm()).'"}', $html);


        // Clean other algorithms info
        foreach (explode("\t",$this->GetAlgorithmsList()) as $algorithm_one)
        {
            if (strtoupper($algorithm_one) != strtoupper($this->GetUserAlgorithm()))
            {
                $html = preg_replace('/<!--\s*\{\/IfMultiotpUserAlgorithm="[BCHIMOPTUY,]*'.strtoupper($algorithm_one).'[BCHIMOPTUY,]*"\}\s*-->/', ' -- {/IfMultiotpUserAlgorithm="DELETE"} -->', $html);
                $html = preg_replace('/<!--\s*\{IfMultiotpUserAlgorithm="[BCHIMOPTUY,]*'.strtoupper($algorithm_one).'[BCHIMOPTUY,]*"\}\s*-->/', '<!-- {IfMultiotpUserAlgorithm="DELETE"} -- ', $html);
            }
        }
        
        $html_cleaned = "";
        $html_slice = explode("-->",$html);
        foreach($html_slice as $one_slice)
        {
            $comment_pos = strpos($one_slice,'<!--');
            if(FALSE !== $comment_pos)
            {
                $html_cleaned.=substr($one_slice,0,$comment_pos);
            }
        }
        $html_cleaned .= end($html_slice);
        $html = $html_cleaned."\n";

        $html = str_replace('{MultiotpUserDescriptionUC}', strtoupper($descr), $html);
        $html = str_replace('{MultiotpUserDescription}', $descr, $html);

        $html = str_replace('{MultiotpUserAccount}', $user, $html);                    
        $html = str_replace('{MultiotpUserPin}', $this->GetUserPin(), $html);
        $html = str_replace('{MultiotpUserAlgorithm}', strtoupper($this->GetUserAlgorithm()), $html);
        $html = str_replace('{MultiotpUserTokenSeed}', $this->GetUserTokenSeed(), $html);
        $html = str_replace('{MultiotpUserTokenSeedBase32}', base32_encode(hex2bin($this->GetUserTokenSeed())), $html);
        $html = str_replace('{MultiotpUserTokenNumberOfDigits}', $this->GetUserTokenNumberOfDigits(), $html);
        $html = str_replace('{MultiotpUserTokenTimeInterval}', $this->GetUserTokenTimeInterval(), $html);
        $html = str_replace('{MultiotpUserTokenNextEvent}', 1+$this->GetUserTokenLastEvent(), $html);
        $html = str_replace('{MultiotpUserTokenSerial}', $token_serial, $html);

        $regex_url='/\surl=(.*?)[\}\s}]/';
        $regex_format='/\sformat=\"?([^\"\}]*)\"?.*\}/';
        $regex_w='/\swidth=(.*?)[\}\s]/';
        $regex_h='/\sheight=(.*?)[\}\s]/';

        // Date and time replacement
        $regex_tag='/\{MultiotpDateTime(.*)\}/';
        $format = "Y-m-d H:i:s";
        if(preg_match_all($regex_tag, $html, $matches))
        {
            foreach ($matches[0] as $item)
            {
                if(!empty($item))
                {
                    if(preg_match($regex_format, $item, $values))
                    {
                        $format = $values[1];
                    }
                    $html = str_replace($item, date($format), $html);
                }
            }
        }

        if (!$keep_qrcode_tags)
        {
            // Smartphone apps qrcode
            $regex_tag='/\{MultiotpQrCodeUrl\s(.*?)\}/';
            if(preg_match_all($regex_tag, $html, $matches))
            {
                foreach ($matches[0] as $item) {
                    $url = '';
                    $w = $code_width;
                    $h = $code_height;
                    if(!empty($item))
                    {
                        if(preg_match($regex_url, $item, $values))
                        {
                            $url= str_replace('"', '', explode('=', $values[0],2));
                            $url = $url[1];
                        }
                        if(preg_match($regex_w, $item, $values))
                        {
                            $w = str_replace('"', '', explode('=', $values[0],2));
                            $w = trim(str_replace('}', '', $w[1]));
                        }
                        if(preg_match($regex_h, $item, $values))
                        {
                            $h = str_replace('"', '', explode('=', $values[0],2));
                            $h = trim(str_replace('}', '', $h[1]));
                        }
                        $html = str_replace($item, "<a id=\"QrCodeUrl\" href=\"".$url."\" target=\"blank\"><img border=\"0\" width=\"".$w."\" height=\"".$h."\" src=\"data:image/png;base64,".base64_encode($this->qrcode($url, 'binary'))."\" /></a>", $html);
                    }
                }
            }
            // User token qrcode
            $regex_tag='/\{MultiotpQrCodeUserToken\s(.*?)\}/';

            if(preg_match_all($regex_tag, $html, $matches))
            {
                foreach ($matches[0] as $item)
                {
                    $url = $this->GetUserTokenUrlLink($user, $descr);
                    $w = $code_width;
                    $h = $code_height;
                    if(!empty($item))
                    {
                        if(preg_match($regex_w, $item, $values))
                        {
                            $w = str_replace('"', '', explode('=', $values[0],2));
                            $w = trim(str_replace('}', '', $w[1]));
                        }
                        if(preg_match($regex_h, $item, $values))
                        {
                            $h = str_replace('"', '', explode('=', $values[0],2));
                            $h = trim(str_replace('}', '', $h[1]));
                        }
                        $html = str_replace($item, "<a id=\"QrCodeUserToken\" href=\"".$url."\" target=\"blank\"><img border=\"0\" width=\"".$w."\" height=\"".$h."\" src=\"data:image/png;base64,".base64_encode($this->qrcode($url, 'binary'))."\"></a>", $html);
                    }
                }
            }
        }
        
        return $html;
    }


    /*********************************************************************
     *
     * Name: GetUserTokenUrlLink
     * Short description: Create the Urllink for the current user
     *
     * Creation 2013-04-29
     * Update 2013-04-29
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $user
     * @param   string  $display_name
     * @return  boolean (FALSE) or string
     *
     *********************************************************************/
    function GetUserTokenUrlLink($user = '', $display_name = '')
    {
        $result = false;
        
        $user_array = $this->ReadUserDataArray(('' != $user)?$user:$this->GetUser());
        if (false !== $user_array) {
            $the_user       = $user;
            $description    = $user_array['description'];
            $q_algorithm    = $user_array['algorithm'];
            $q_algo_suite   = $user_array['token_algo_suite'];
            $q_period       = $user_array['time_interval'];
            $q_digits       = $user_array['number_of_digits'];
            $q_seed         = $user_array['token_seed'];
            $q_counter      = $user_array['last_event'] + 1;
            $q_display_name = (('' != $display_name)?$display_name:(('' != $description)?$description:$the_user));
            $q_issuer       = $this->GetIssuer();
            switch (strtolower($q_algorithm)) {
                case 'totp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.rawurlencode($q_issuer);
                    break;
                case 'hotp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&counter='.$q_counter.'&issuer='.rawurlencode($q_issuer);
                    break;
                case 'motp':
                    $result = 'motp://'.rawurlencode($q_issuer).'/'.rawurlencode($q_display_name).'?secret='.$q_seed;
                    break;
                /*
                case 'token2':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.$q_issuer;
                    break;
                case 'motp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.$q_issuer;
                    break;
                */
                
                default:
                    // $result = FALSE;
                    $result = 'http://motp.sourceforge.net/no_qrcode_compatible_client_for_this_algorithm';
                    $this->WriteLog("Error: No known URL compatible client for this algorithm", FALSE, FALSE, 23, 'System', '');
            }
        } else {
            // $result = '';
        }
        return $result;
    }


    /**
     * @brief   Create the Urllink for the current token
     *
     * @param   string  $token
     * @param   string  $display_name
     * @return  boolean (FALSE) or string
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.1.1
     * @date    2014-01-19
     * @since   2014-01-19
     */
    function GetTokenUrlLink($token = '', $display_name = '')
    {
        $result = FALSE;
        if ('' != $token)
        {
            $this->SetToken($token);
        }

        if ($this->ReadTokenData())
        {
            $the_token      = $this->GetToken();
            $q_algorithm    = $this->GetTokenAlgorithm();
            $q_algo_suite   = $this->GetTokenAlgoSuite();
            $q_period       = $this->GetTokenTimeInterval();
            $q_digits       = $this->GetTokenNumberOfDigits();
            $q_seed         = $this->GetTokenSeed();
            $q_counter      = $this->GetTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:$the_token);
            $q_issuer       = $this->GetTokenIssuer();

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.rawurlencode($q_issuer);
                    break;
                case 'hotp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&counter='.$q_counter.'&issuer='.rawurlencode($q_issuer);
                    break;
                case 'motp':
                    $result = 'motp://'.rawurlencode($q_issuer).'/'.rawurlencode($q_display_name).'?secret='.$q_seed;
                    break;
                /*
                case 'token2':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.$q_issuer;
                    break;
                case 'motp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.$q_issuer;
                    break;
                */
                default:
                    // $result = FALSE;
                    $result = 'http://http://motp.sourceforge.net/no_qrcode_compatible_client_for_this_algorithm';
                    $this->WriteLog("Error: No known URL compatible client for this algorithm", FALSE, FALSE, 23, 'System', '');
            }
        }
        else
        {
            // $result = '';
        }
        return $result;
    }


    /*********************************************************************
     *
     * Name: FastCreateUser
     * Short description: Quickly create a new user with a new token (GA compatible)
     *
     * Creation 2013-02-16
     * Update 2014-03-03
     * @package multiotp
     * @version 4.2.2
     * @author SysCo/al
     *
     * @param   string  $user      
     * @param   string  $email
     * @param   string  $sms
     * @param   int     $prefix_pin_needed [-1|0|1]
     * @param   string  $algorithm [totp|hotp|motp]
     * @param   int     $activated [1|0]
     * @param   string  $description
     * @param   string  $group
     * @param   int     $synchronized [0|1]
     * @param   string  $pin
     * @param   boolean $automatically         Process is done automatically  
     * @param   string  $synchronized_channel 
     * @param   string  $synchronized_server
     * @param   string  $synchronized_dn
     * @param   string  $ldap_pwd_needed
     * @return  boolean
     *
     *********************************************************************/
    function FastCreateUser($user,
                            $email = '',
                            $sms = '',
                            $prefix_pin_needed = -1,
                            $algorithm = "totp",
                            $activated=1,
                            $description = "",
                            $group = "*DEFAULT*",
                            $synchronized = 0,
                            $pin = '',
                            $automatically = false,
                            $synchronized_channel = '',
                            $synchronized_server = '',
                            $synchronized_dn = '',
                            $ldap_pwd_needed = -1
                           )
    {
        $result = FALSE;
        if ('' != trim($user))
        {
            if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user))
            {
                $this->WriteLog("Error: Unable to create the user ".$user." because it already exists", FALSE, FALSE, 22, 'User', $user);
            }
            else
            {
                if ((intval($ldap_pwd_needed) < 0) && (1 == $synchronized))
                {
                    $request_ldap_pwd = $this->GetDefaultRequestLdapPwd();
                }
                else
                {
                    $request_ldap_pwd = intval($ldap_pwd_needed);
                }
                                   
                if (intval($prefix_pin_needed) < 0)
                {
                    $prefix_required = $this->GetDefaultRequestPrefixPin();
                }
                else
                {
                    $prefix_required = intval($prefix_pin_needed);
                }
            
                $this->SetUser($user, false); // This will do also an automatic reset of the user array

                $this->SetUserEmail($email);
                $this->SetUserDescription($description);
                $this->SetUserGroup(('*DEFAULT*' == $group)?$this->GetDefaultUserGroup():$group);
                $this->SetUserSms($sms);
                $this->SetUserAlgorithm($algorithm);
                
                $this->SetUserTokenAlgoSuite(''); // Default algorithm suite (HMAC-SHA1)
                $the_pin = $pin;
                if ('' == $the_pin)
                {
                    $the_pin = mt_rand(1000,9999);
                }
                $this->SetUserPrefixPin($prefix_required);
                $this->SetUserTokenNumberOfDigits(6);
                $next_event = 0;

                /* This option is too long
                if (function_exists('openssl_random_pseudo_bytes')) {
                    $seed = bin2hex(openssl_random_pseudo_bytes(20));
                } else {
                */
                    $seed = substr(md5(date("YmdHis").mt_rand(100000,999999)),0,20).substr(md5(mt_rand(100000,999999).date("YmdHis")),0,20);
                /* } */

                if ("totp" == strtolower($algorithm))
                {
                    $time_interval = 30;
                }
                elseif ("motp" == strtolower($algorithm))
                {
                    $seed = substr($seed,0,16);
                    $time_interval = 10;
                    if ((strlen($the_pin) < 4) || (0 == intval($the_pin)))
                    {
                        $the_pin = mt_rand(1000,9999);
                    }
                }
                else
                {
                    $time_interval = 0;
                }

                $this->SetUserPin($the_pin);
                $this->SetUserTokenSeed($seed);
                $this->SetUserTokenLastEvent($next_event-1);
                $this->SetUserTokenTimeInterval($time_interval);

                $this->SetUserActivated($activated);

                $this->SetUserSynchronized($synchronized);

                if (($automatically) && (1 == $synchronized))
                {
                    $this->SetUserSynchronizedTime();
                    $this->SetUserSynchronizedChannel($synchronized_channel);
                    $this->SetUserSynchronizedServer($synchronized_server);
                    $this->SetUserSynchronizedDn($synchronized_dn);
                }

                $this->SetUserRequestLdapPassword($request_ldap_pwd);

                $result = $this->WriteUserData($automatically); // WriteUserData write in the log file
            }
        }
        return $result;
    }


    function SetUser($user, $auto_read_data = true)
    {
        if ('' != $user)
        {
            if ($user != $this->GetUser())
            {
                $this->ResetUserArray();
                $this->_user = $user;
                if (!$this->IsCaseSensitiveUsers())
                {
                    $this->_user = strtolower($this->_user);
                }
                if ($auto_read_data) {
                    $this->ReadUserData('', false); // First parameter empty, otherwise it will loop with SetUser !
                }
            }
        }
        else
        {
            $this->ResetUserArray();
        }
    }


    function RenameCurrentUser($new_user, $no_error_info = FALSE)
    {
        $esc_actual = escape_mysql_string($this->GetUser());
        $esc_new    = escape_mysql_string($new_user);
        $result = FALSE;
        if ($this->CheckUserExists($new_user)) // Check if the new user already exists
        {
            $this->WriteLog("Error: Unable to rename the current user ".$this->GetUser()." to $new_user because $new_user already exists", FALSE, FALSE, 22, 'User');
        }
        else
        {
            if ($this->CheckUserExists()) // Check if the current user already exists
            {
                if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
                {
                    switch ($this->GetBackendType())
                    {
                        case 'mysql':
                            if ($this->OpenMysqlDatabase())
                            {
                                if ('' != $this->_config_data['sql_users_table'])
                                {
                                    $sQuery = "UPDATE `".$this->_config_data['sql_users_table']."` SET user='".strtolower($esc_new)."' WHERE `user`='".$esc_actual."'";
                                    
                                    if (is_object($this->_mysqli))
                                    {
                                        if (!($rResult = $this->_mysqli->query($sQuery)))
                                        {
                                            if (!$no_error_info)
                                            {
                                                $this->WriteLog("Error: Could not rename the user ".$this->GetUser().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'User');
                                            }
                                        }
                                        else
                                        {
                                            $num_rows = $this->_mysqli->affected_rows;
                                        }
                                    }
                                    elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                    {
                                        if (!$no_error_info)
                                        {
                                            $this->WriteLog("Error: Could not rename the user ".$this->GetUser().": ".mysql_error(), FALSE, FALSE, 28, 'User');
                                        }
                                    }
                                    else
                                    {
                                        $num_rows = mysql_affected_rows($this->_mysql_database_link);
                                    }
                                    
                                    if (0 == $num_rows)
                                    {
                                        $this->WriteLog("Error: Could not rename the user ".$this->GetUser().". User does not exist", FALSE, FALSE, 21, 'User');
                                    }
                                    else
                                    {
                                        $this->WriteLog("Info: User ".$this->GetUser()." successfully renamed to $new_user", FALSE, FALSE, 11, 'User');
                                        $result = TRUE;
                                    }
                                }
                            }
                            break;
                        case 'files':
                        default:
                            $old_user_filename = strtolower(str_replace('/','',$this->GetUser())).'.db';
                            $new_user_filename = strtolower(str_replace('/','',$new_user)).'.db';
                            rename($this->GetUsersFolder().$old_user_filename, $this->GetUsersFolder().$new_user_filename);
                            $result = TRUE;
                            break;
                    }
                }
            }
            if ($result)
            {
                $this->_user = strtolower($new_user);
            }
        }
        return $result;
    }


    function GetUser()
    {
        return $this->_user;
    }


    // Check if user exists (locally only)
    function CheckUserExists($user = '', $no_server_check = FALSE, $no_error = FALSE)
    {
        $check_user = ('' != $user)?$user:$this->GetUser();
        $result = FALSE;

        if ('' != trim($check_user))
        {
            $server_result = -1;
            if ((!$no_server_check) && ('' != $this->GetServerUrl()))
            {
                $server_result = $this->CheckUserExistsOnServer($check_user);
                if (22 == $server_result)
                {
                    // We return only if the user exists, so we check also the local one
                    $result = TRUE;
                    return $result;
                }
            }

            if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '{$check_user}'";
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                if (!$no_error)
                                {
                                    $this->WriteLog("Error: User ".$check_user." does not exist", FALSE, FALSE, 299, 'System', '');
                                }
                                $result = FALSE;
                            }
                            else
                            {
                                $result = TRUE;
                            }
                        }
                        break;
                    case 'files':
                    default:
                        $user_filename = str_replace('/','',$check_user).'.db';
                        if (!$this->IsCaseSensitiveUsers())
                        {
                            $user_filename = strtolower($user_filename);
                        }
                        $result = file_exists($this->GetUsersFolder().$user_filename);
                        break;
                }
            }
        }
        return $result;
    }


    function LockUser($user = '')
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user, false);
        }
        if ($this->ReadUserData('', FALSE, TRUE)) // LOCALLY ONLY, not on the server if any
        {
            $this->SetUserLocked(1);
            if ($this->GetVerboseFlag())
            {
                $this->WriteLog("Info: *User ".$this->GetUser()." successfully locked", FALSE, FALSE, 19, 'User');
            }
            $this->WriteUserData();
            $result = TRUE;
        }
        return $result;
    }


    function UnlockUser($user = '')
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user, false);
        }
        if ($this->ReadUserData('', FALSE, TRUE)) // LOCALLY ONLY, not on the server if any
        {
            $this->SetUserErrorCounter(0);
            $this->SetUserLocked(0);
            if ($this->GetVerboseFlag())
            {
                $this->WriteLog("Info: *User ".$this->GetUser()." successfully unlocked", FALSE, FALSE, 19, 'User');
            }
            $this->WriteUserData();
            $result = TRUE;
        }
        return $result;
    }


    function DeleteUser($user = '', $no_error_info = FALSE)
    {
        if ('' != $user)
        {
            $this->SetUser($user, false);
        }
        
        $result = FALSE;
        
        // First, we delete the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $user_filename = str_replace('/','',$this->GetUser()).'.db';
            if (!$this->IsCaseSensitiveUsers())
            {
                $user_filename = strtolower($user_filename);
            }
            if (!file_exists($this->GetUsersFolder().$user_filename))
            {
                if (!$no_error_info)
                {
                    if ($this->GetVerboseFlag())
                    {
                        $this->WriteLog("Error: *Unable to delete user ".$this->GetUser().", the users database file ".$this->GetUsersFolder().$user_filename." does not exist", FALSE, FALSE, 21, 'User');
                    }
                    else
                    {
                        $this->WriteLog("Error: Unable to delete user ".$this->GetUser(), FALSE, FALSE, 29, 'User');
                    }
                }
            }
            else
            {
                $result = unlink($this->GetUsersFolder().$user_filename);
                if ($result)
                {
                    if ($this->GetVerboseFlag())
                    {
                        $this->WriteLog("Info: *User ".$this->GetUser()." successfully deleted", FALSE, FALSE, 12, 'User');
                    }
                }
                elseif (!$this->GetMigrationFromFile())
                {
                    if (!$no_error_info)
                    {
                        $this->WriteLog("Error: Unable to delete user ".$this->GetUser(), FALSE, FALSE, 28, 'User');
                    }
                }
            }
        }

        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_users_table'])
                        {
                            $sQuery  = "DELETE FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '".$this->_user."'";
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    if (!$no_error_info)
                                    {
                                        $this->WriteLog("Error: Could not delete user ".$this->GetUser().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'User');
                                    }
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete user ".$this->GetUser().": ".mysql_error(), FALSE, FALSE, 28, 'User');
                                }
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete user ".$this->GetUser().". User does not exist", FALSE, FALSE, 21, 'User');
                                }
                            }
                            else
                            {
                                if ($this->GetVerboseFlag())
                                {
                                    $this->WriteLog("Info: *User ".$this->GetUser()." successfully deleted", FALSE, FALSE, 12, 'User');
                                }
                                $result = TRUE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }                        
        }
        
        foreach(explode("\t", $this->GetTokensList()) as $one_token)
        {
            if ($this->RemoveTokenAttributedUsers($one_token, $this->GetUser()))
            {
                $this->WriteTokenData();
            }
        }
        
        return $result;
    }


    function GetUsersCount()
    {
        if (($this->IsCacheData()) && (intval($this->ReadCacheValue('users_count')) >= 0))
        {
            $users_count = intval($this->ReadCacheValue('users_count'));
        }
        else
        {
            $users_count = 0;
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery  = "SELECT user FROM `".$this->_config_data['sql_users_table']."` ";
                        
                        if (is_object($this->_mysqli))
                        {
                            if (!($result = $this->_mysqli->query($sQuery)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                            else
                            {
                                while ($aRow = $result->fetch_assoc())
                                {
                                    $users_count++;
                                }
                            }
                        }
                        else
                        {
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                            }
                            else
                            {
                                while ($aRow = mysql_fetch_assoc($rResult))
                                {
                                    $users_count++;
                                }                         
                            }
                        }
                    }
                    break;
                case 'files':
                default:
                    if ($users_handle = @opendir($this->GetUsersFolder()))
                    {
                        while ($file = readdir($users_handle))
                        {
                            if ((substr($file, -3) == ".db") && ($file != '.db'))
                            {
                                $users_count++;
                            }
                        }
                        closedir($users_handle);
                    }
            }
            if (($this->IsCacheData()) && ($users_count >= 0))
            {
                $this->WriteCacheValue('users_count', $users_count);
                $this->WriteCacheData();
            }
        }
        return $users_count;
    }


    function ReadUserDataArray($user = '', $create = false, $no_server_check = false)
    {
        $array_user = ('' != $user)?$user:$this->GetUser();
        $result = false;

        // We reset all values (we know the key based on the schema)
        $temp_user_array = $this->ResetTempUserArray();

        // First, we read the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $user_filename = str_replace('/','',$array_user).'.db';
            if (!$this->IsCaseSensitiveUsers())
            {
                $user_filename = strtolower($user_filename);
            }
            if (!file_exists($this->GetUsersFolder().$user_filename))
            {
                if (!$create)
                {
                    $this->WriteLog("Error: database file ".$this->GetUsersFolder().$user_filename." for user ".$array_user." does not exist", FALSE, FALSE, 21, 'System', '');
                }
            }
            else
            {
                $temp_user_array['multi_account'] = 0;
                $temp_user_array['time_interval'] = 0;
                
                $file_handler = fopen($this->GetUsersFolder().$user_filename, "rt");
                $first_line = trim(fgets($file_handler));
                $v3 = (false !== strpos(strtolower($first_line),"multiotp-database-format-v3"));
                
                // First version format support
                if (false === strpos(strtolower($first_line),"multiotp-database-format")) {
                    $temp_user_array['algorithm']          = $first_line;
                    $temp_user_array['token_seed']         = trim(fgets($file_handler));
                    $temp_user_array['user_pin']           = trim(fgets($file_handler));
                    $temp_user_array['number_of_digits']   = trim(fgets($file_handler));
                    $temp_user_array['last_event']         = intval(trim(fgets($file_handler)) - 1);
                    $temp_user_array['request_prefix_pin'] = intval(trim(fgets($file_handler)));
                    $temp_user_array['last_login']         = intval(trim(fgets($file_handler)));
                    $temp_user_array['error_counter']      = intval(trim(fgets($file_handler)));
                    $temp_user_array['locked']             = intval(trim(fgets($file_handler)));
                } else {
                    while (!feof($file_handler)) {
                        $line = trim(fgets($file_handler));
                        $line_array = explode("=",$line,2);
                        if ($v3) { // v3 format, only tags followed by := instead of = are encrypted
                            if (":" == substr($line_array[0], -1)) {
                                $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                            }
                        } else { // v2 format, only defined tags are encrypted
                            if (FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$line_array[0].'*'))) {
                                $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                            }
                        }
                        if ('' != trim($line_array[0])) {
                            $temp_user_array[strtolower($line_array[0])] = $line_array[1];
                        }
                    }
                }
                fclose($file_handler);
                $result = true;
                if ('' != $temp_user_array['encryption_hash']) {
                    if ($temp_user_array['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                        $temp_user_array['encryption_hash'] = "ERROR";
                        $this->WriteLog("Error: the user information encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                        $result = false;
                    }
                }
            }
        }


        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_users_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '".$array_user."'";
                            $aRow = NULL;
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 199, 'System', '');
                                    $result = false;
                                }
                                else
                                {
                                    $aRow = $rResult->fetch_assoc();
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                                    $result = false;
                                }
                                else
                                {
                                    $aRow = mysql_fetch_assoc($rResult);
                                }
                            }

                            if (NULL != $aRow)
                            {
                                $result = false;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['users']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if (($in_the_schema) && ($key != 'user'))
                                    {
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $temp_user_array[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                        }
                                        else
                                        {
                                            $temp_user_array[$key] = $value;
                                        }
                                    }                                    
                                    elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: *The key ".$key." is not in the users database schema", FALSE, FALSE, 98, 'System', '');
                                    }
                                    $result = true;
                                }
                                if(0 == count($aRow) && !$create)
                                {
                                    $this->WriteLog("Error: SQL database entry for user ".$array_user." does not exist", FALSE, FALSE, 299, 'System', '');
                                }
                            }
                        }
                        if ('' != $temp_user_array['encryption_hash'])
                        {
                            if ($temp_user_array['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $temp_user_array['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the users mysql encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                                $result = false;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }

        // And now, we do the ReadUserData online on the server
        $server_result = -1;
        if ((!$no_server_check) && ('' != $this->GetServerUrl()))
        {
            $server_result = $this->ReadUserDataOnServer($array_user);
            if (20 < strlen($server_result))
            {
                $temp_user_array['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
                $server_array = explode("\n",$server_result);
                $server_result = 19;

                foreach ($server_array as $one_line)
                {
                    $line = trim($one_line);
                    $line_array = explode("=",$line,2);
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0], $line_array[1], $this->GetServerSecret());
                    }
                    if ('' != trim($line_array[0]))
                    {
                        if ('encryption_hash' != strtolower($line_array[0]))
                        {
                            $temp_user_array[strtolower($line_array[0])] = $line_array[1];
                        }
                    }
                }
                $result = true;
            }
        }

        if (false !== $result) {
            return $temp_user_array;
        } else {
            return false;
        }
    }


    function ReadUserData($user = '', $create = FALSE, $no_server_check = FALSE)
    {
        if ('' != $user) {
            $this->SetUser($user, false);
        }
        $result = false;
        $temp_user_array = $this->ReadUserDataArray($user, $create, $no_server_check);
        if (false !== $temp_user_array) {
            $this->_user_data = $temp_user_array;
            $result = true;
        } else {
            $this->_user_data = $this->ResetUserArray();
        }

        $this->SetUserDataReadFlag($result);
        return $result;
    }


    function WriteUserData(
        $automatically = false,
        $update_last_change = true
    ) {
        if ('' == trim($this->GetUser())) {
            $result = false;
        } else {
            $result = $this->WriteData('User',
                                       'users',
                                       $this->GetUsersFolder(),
                                       $this->_user_data,
                                       false,
                                       'user',
                                       $this->GetUser(),
                                       $this->IsCaseSensitiveUsers(),
                                       $automatically,
                                       $update_last_change
                                      );
        }
        return $result;
    }


    function GetUsersList()
    {
        return $this->GetList('user', 'sql_users_table', $this->GetUsersFolder());
    }


    function GetLockedUsersList($limit = 0)
    {
        if (($this->IsCacheData()) && (($this->ReadCacheValue('locked_users_list')) != '-1'))
        {
            $locked_users_list = ($this->ReadCacheValue('locked_users_list'));
        }
        else
        {
            $locked_users_list = '';
            if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery  = "SELECT user FROM `".$this->_config_data['sql_users_table']."` WHERE (`locked` = 1) ORDER BY user ASC";
                            if ($limit > 0)
                            {
                                $sQuery.= " LIMIT 0,".$limit;
                            }
                            if (is_object($this->_mysqli))
                            {
                                if (!($result = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    while ($aRow = $result->fetch_assoc())
                                    {
                                        if ('' != $aRow['user'])
                                        {
                                            $locked_users_list.= (('' != $locked_users_list)?"\t":'').$aRow['user'];
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                                }
                                else
                                {
                                    while ($aRow = mysql_fetch_assoc($rResult))
                                    {
                                        if ('' != $aRow['user'])
                                        {
                                            $locked_users_list.= (('' != $locked_users_list)?"\t":'').$aRow['user'];
                                        }
                                    }                         
                                }
                            }
                        }
                        break;
                    case 'files':
                    default:
                        $locked_users_count = 0;
                        $active_users_count = 0;
                        $users_count = 0;
                        if ($users_handle = @opendir($this->GetUsersFolder()))
                        {
                            while ($file = readdir($users_handle))
                            {
                                $locked = FALSE;
                                $desactivated = FALSE;
                                if ((substr($file, -3) == ".db") && ($file != '.db'))
                                {
                                    $current_user = substr($file,0,-3);
                                    $file_handler = fopen($this->GetUsersFolder().$file, "rt");
                                    $first_line = trim(fgets($file_handler));
                                    $v3 = (FALSE !== strpos(strtolower($first_line),"multiotp-database-format-v3"));
                                    if (FALSE !== strpos(strtolower($first_line),"multiotp-database-format")) // Format V3
                                    {
                                        while (!feof($file_handler))
                                        {
                                            $line = trim(fgets($file_handler));
                                            $line_array = explode("=",$line,2);
                                            if ($v3) // v3 format, only tags followed by := instead of = are encrypted
                                            {
                                                if (":" == substr($line_array[0], -1))
                                                {
                                                    $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                    $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                }
                                            }
                                            else // v2 format, only defined tags are encrypted
                                            {
                                                if (FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$line_array[0].'*')))
                                                {
                                                    $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                }
                                            }
                                            if ('locked' == trim($line_array[0]))
                                            {
                                                if (1 == (isset($line_array[1])?$line_array[1]:0))
                                                {
                                                    $locked = TRUE;
                                                }
                                            }
                                            if ('desactivated' == trim($line_array[0]))
                                            {
                                                if (1 == (isset($line_array[1])?$line_array[1]:0))
                                                {
                                                    $desactivated = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    fclose($file_handler);
                                    $users_count++;
                                    
                                    if ($locked)
                                    {
                                        $locked_users_list.= (('' != $locked_users_list)?"\t":'').$current_user;
                                        $locked_users_count++;
                                    }
                                    if (!$desactivated)
                                    {
                                        $active_users_count++;
                                    }
                                }
                                if (($limit > 0) && (locked_users_count >= $limit))
                                {
                                    break;
                                }
                            }
                            closedir($users_handle);
                            
                            if (($limit <= 0) && ($this->IsCacheData()))
                            {
                                $this->WriteCacheValue('locked_users_list', $locked_users_list);
                                if ($locked_users_count >= 0)
                                {
                                    $this->WriteCacheValue('locked_users_count', $locked_users_count);
                                }
                                if ($active_users_count >= 0)
                                {
                                    $this->WriteCacheValue('active_users_count', $active_users_count);
                                }
                                if ($users_count >= 0)
                                {
                                    $this->WriteCacheValue('users_count', $users_count);
                                }
                                $this->WriteCacheData();
                            }
                            
                        }
                }
            }
        }
        return $locked_users_list;
    }


    function GetLockedUsersCount()
    {
        if (($this->IsCacheData()) && (intval($this->ReadCacheValue('locked_users_count')) >= 0))
        {
            $locked_users_count = intval($this->ReadCacheValue('locked_users_count'));
        }
        else
        {
            $locked_users_count = 0;
            $active_users_count = -1;
            if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery  = "SELECT COUNT(user) AS counter FROM `".$this->_config_data['sql_users_table']."` WHERE (`locked` = 1)";
                            if (is_object($this->_mysqli))
                            {
                                if (!($result = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    if ($aRow = $result->fetch_assoc())
                                    {
                                        $locked_users_count = $aRow['counter'];
                                    }
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                                }
                                else
                                {
                                    if ($aRow = mysql_fetch_assoc($rResult))
                                    {
                                        $locked_users_count = $aRow['counter'];
                                    }
                                }
                            }
                        }
                        break;
                    case 'files':
                    default:
                        $active_users_count = 0;
                        $users_count = 0;
                        if ($users_handle = @opendir($this->GetUsersFolder()))
                        {
                            while ($file = readdir($users_handle))
                            {
                                $locked = FALSE;
                                $desactivated = FALSE;
                                if ((substr($file, -3) == ".db") && ($file != '.db'))
                                {
                                    $current_user = substr($file,0,-3);
                                    $file_handler = fopen($this->GetUsersFolder().$file, "rt");
                                    $first_line = trim(fgets($file_handler));
                                    $v3 = (FALSE !== strpos(strtolower($first_line),"multiotp-database-format-v3"));
                                    if (FALSE !== strpos(strtolower($first_line),"multiotp-database-format")) // Format V3
                                    {
                                        while (!feof($file_handler))
                                        {
                                            $line = trim(fgets($file_handler));
                                            $line_array = explode("=",$line,2);
                                            if ($v3) // v3 format, only tags followed by := instead of = are encrypted
                                            {
                                                if (":" == substr($line_array[0], -1))
                                                {
                                                    $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                    $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                }
                                            }
                                            else // v2 format, only defined tags are encrypted
                                            {
                                                if (FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$line_array[0].'*')))
                                                {
                                                    $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                }
                                            }
                                            if ('locked' == trim($line_array[0]))
                                            {
                                                if (1 == (isset($line_array[1])?$line_array[1]:0))
                                                {
                                                    $locked = TRUE;
                                                }
                                            }
                                            if ('desactivated' == trim($line_array[0]))
                                            {
                                                if (1 == (isset($line_array[1])?$line_array[1]:0))
                                                {
                                                    $desactivated = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    fclose($file_handler);
                                    $users_count++;
                                    
                                    if ($locked)
                                    {
                                        $locked_users_count++;
                                    }
                                    if (!$desactivated)
                                    {
                                        $active_users_count++;
                                    }
                                }
                            }
                            closedir($users_handle);
                        }
                }
            }
            if ($this->IsCacheData())
            {
                if ($locked_users_count >= 0)
                {
                    $this->WriteCacheValue('locked_users_count', $locked_users_count);
                }
                if ($active_users_count >= 0)
                {
                    $this->WriteCacheValue('active_users_count', $active_users_count);
                }
                if ($users_count >= 0)
                {
                    $this->WriteCacheValue('users_count', $users_count);
                }
                $this->WriteCacheData();                
            }
        }
        return $locked_users_count;
    }


    function GetActiveUsersList($limit = 0)
    {
        $list = '';
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery  = "SELECT user FROM `".$this->_config_data['sql_users_table']."` WHERE (`desactivated` = 0) ORDER BY user ASC";
                        if ($limit > 0)
                        {
                            $sQuery.= " LIMIT 0,".$limit;
                        }
                        if (is_object($this->_mysqli))
                        {
                            if (!($result = $this->_mysqli->query($sQuery)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                            else
                            {
                                while ($aRow = $result->fetch_assoc())
                                {
                                    if ('' != $aRow['user'])
                                    {
                                        $list.= (('' != $list)?"\t":'').$aRow['user'];
                                    }
                                }
                            }
                        }
                        else
                        {
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                            }
                            else
                            {
                                while ($aRow = mysql_fetch_assoc($rResult))
                                {
                                    if ('' != $aRow['user'])
                                    {
                                        $list.= (('' != $list)?"\t":'').$aRow['user'];
                                    }
                                }                         
                            }
                        }
                    }
                    break;
                case 'files':
                default:
                    $active_users_count = 0;
                    $locked_users_count = 0;
                    $users_count = 0;
                    if ($users_handle = @opendir($this->GetUsersFolder()))
                    {
                        while ($file = readdir($users_handle))
                        {
                            $desactivated = FALSE;
                            $locked = FALSE;
                            if ((substr($file, -3) == ".db") && ($file != '.db'))
                            {
                                $current_user = substr($file,0,-3);
                                $file_handler = fopen($this->GetUsersFolder().$file, "rt");
                                $first_line = trim(fgets($file_handler));
                                $v3 = (FALSE !== strpos(strtolower($first_line),"multiotp-database-format-v3"));
                                if (FALSE !== strpos(strtolower($first_line),"multiotp-database-format")) // Format V3
                                {
                                    while (!feof($file_handler))
                                    {
                                        $line = trim(fgets($file_handler));
                                        $line_array = explode("=",$line,2);
                                        if ($v3) // v3 format, only tags followed by := instead of = are encrypted
                                        {
                                            if (":" == substr($line_array[0], -1))
                                            {
                                                $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                            }
                                        }
                                        else // v2 format, only defined tags are encrypted
                                        {
                                            if (FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$line_array[0].'*')))
                                            {
                                                $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                            }
                                        }
                                        if ('desactivated' == trim($line_array[0]))
                                        {
                                            if (1 == (isset($line_array[1])?$line_array[1]:0))
                                            {
                                                $desactivated = TRUE;
                                            }
                                        }
                                        if ('locked' == trim($line_array[0]))
                                        {
                                            if (1 == (isset($line_array[1])?$line_array[1]:0))
                                            {
                                                $locked = TRUE;
                                            }
                                        }
                                    }
                                }
                                fclose($file_handler);
                                $users_count++;
                                
                                if (!$desactivated)
                                {
                                    $list.= (('' != $list)?"\t":'').$current_user;
                                    $active_users_count++;
                                }
                                if ($locked)
                                {
                                    $locked_users_count++;
                                }
                            }
                            if (($limit > 0) && (active_users_count >= $limit))
                            {
                                break;
                            }
                        }
                        closedir($users_handle);
                        
                        if (($limit <= 0) && ($this->IsCacheData()))
                        {
                            if ($locked_users_count >= 0)
                            {
                                $this->WriteCacheValue('locked_users_count', $locked_users_count);
                            }
                            if ($active_users_count >= 0)
                            {
                                $this->WriteCacheValue('active_users_count', $active_users_count);
                            }
                            if ($users_count >= 0)
                            {
                                $this->WriteCacheValue('users_count', $users_count);

                            }
                            $this->WriteCacheData();
                        }
                    }
            }
        }
        return $list;
    }


    function GetActiveUsersCount()
    {
        if (($this->IsCacheData()) && (intval($this->ReadCacheValue('active_users_count')) >= 0))
        {
            $active_users_count = intval($this->ReadCacheValue('active_users_count'));
        }
        else
        {
            $active_users_count = 0;
            $locked_users_count = -1;
            if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery  = "SELECT COUNT(user) AS counter FROM `".$this->_config_data['sql_users_table']."` WHERE (`desactivated` = 0)";
                            if (is_object($this->_mysqli))
                            {
                                if (!($result = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    if ($aRow = $result->fetch_assoc())
                                    {
                                        $active_users_count = $aRow['counter'];
                                    }
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                                }
                                else
                                {
                                    if ($aRow = mysql_fetch_assoc($rResult))
                                    {
                                        $active_users_count = $aRow['counter'];
                                    }
                                }
                            }
                        }
                        break;
                    case 'files':
                    default:
                        $locked_users_count = 0;
                        $users_count = 0;
                        if ($users_handle = @opendir($this->GetUsersFolder()))
                        {
                            while ($file = readdir($users_handle))
                            {
                                $desactivated = FALSE;
                                $locked = FALSE;
                                if ((substr($file, -3) == ".db") && ($file != '.db'))
                                {
                                    $current_user = substr($file,0,-3);
                                    $file_handler = fopen($this->GetUsersFolder().$file, "rt");
                                    $first_line = trim(fgets($file_handler));
                                    $v3 = (FALSE !== strpos(strtolower($first_line),"multiotp-database-format-v3"));
                                    if (FALSE !== strpos(strtolower($first_line),"multiotp-database-format")) // Format V3
                                    {
                                        while (!feof($file_handler))
                                        {
                                            $line = trim(fgets($file_handler));
                                            $line_array = explode("=",$line,2);
                                            if ($v3) // v3 format, only tags followed by := instead of = are encrypted
                                            {
                                                if (":" == substr($line_array[0], -1))
                                                {
                                                    $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                    $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                }
                                            }
                                            else // v2 format, only defined tags are encrypted
                                            {
                                                if (FALSE !== strpos(strtolower($this->GetAttributesToEncrypt()), strtolower('*'.$line_array[0].'*')))
                                                {
                                                    $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                }
                                            }
                                            if ('desactivated' == trim($line_array[0]))
                                            {
                                                if (1 == (isset($line_array[1])?$line_array[1]:0))
                                                {
                                                    $desactivated = TRUE;
                                                }
                                            }
                                            if ('locked' == trim($line_array[0]))
                                            {
                                                if (1 == (isset($line_array[1])?$line_array[1]:0))
                                                {
                                                    $locked = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    fclose($file_handler);
                                    $users_count++;
                                    
                                    if (!$desactivated)
                                    {
                                        $active_users_count++;
                                    }
                                    if ($locked)
                                    {
                                        $locked_users_count++;
                                    }
                                }
                            }
                            closedir($users_handle);
                        }
                }
            }
            if ($this->IsCacheData())
            {
                if ($active_users_count >= 0)
                {
                    $this->WriteCacheValue('active_users_count', $active_users_count);
                }
                if ($locked_users_count >= 0)
                {
                    $this->WriteCacheValue('locked_users_count', $locked_users_count);
                }
                if ($users_count >= 0)
                {
                    $this->WriteCacheValue('users_count', $users_count);
                }
                $this->WriteCacheData();
            }
        }
        return $active_users_count;
    }


    function GetDetailedUsersArray()
    // Completely new edition 2014-07-21
    {
        $users_array = array();
        $result = $this->GetNextUserArray(TRUE);
        if (isset($result['user']))
        {
            $users_array[$result['user']] = $result;
        }
        do
        {
            if ($result = $this->GetNextUserArray())
            {
                if (isset($result['user']))
                {
                    $users_array[$result['user']] = $result;
                }
            }
        }
        while (FALSE !== $result);
        return $users_array;
    }


    function GetNextUserArray($first = FALSE, $fields = NULL)
    {
        if (NULL != $fields)
        {
            $fields_array = $fields;
        }
        else
        {
            $fields_array = array('user',
                                  'description',
                                  'email',
                                  'group',
                                  'desactivated',
                                  'locked',
                                  'sms',
                                  'synchronized',
                                  'synchronized_channel',
                                  'synchronized_server',
                                  'synchronized_time',
                                  'token_serial',
                                  'synchronized_dn'
                                 );
        }
        $raw_id = $fields_array[0];
        
        $fields_text = '';
        $fields_separator = '';
        foreach($fields_array as $one_field)
        {
            $fields_text.= $fields_separator.'`'.$one_field.'`';
            $fields_separator = ',';
        }
        
        $table_name = 'sql_users_table';
        $folder = $this->GetUsersFolder();
        $parser_id = 'GET_NEXT_USER_ARRAY';
        $user_array = false;

        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data[$table_name])) || ('files' == $this->GetBackendType()))
        {
            if ($first)
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery = "SELECT ".$fields_text." FROM `".$this->_config_data[$table_name]."`";
                            if (is_object($this->_mysqli))
                            {
                                if (!($this->_parser_pointers[$parser_id] = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                    $this->_parser_pointers[$parser_id] = FALSE;
                                    $result = FALSE;
                                    return $result;
                                }
                            }
                            else
                            {
                                if (!($this->_parser_pointers[$parser_id] = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                                    $this->_parser_pointers[$parser_id] = FALSE;
                                    $result = FALSE;
                                    return $result;
                                }
                            }
                        }
                        break;
                    case 'files':
                    default:
                        if (!($this->_parser_pointers[$parser_id] = @opendir($folder)))
                        {
                            $result = FALSE;
                            return $result;
                        }
                }
            } // if ($first)
            
            if (isset($this->_parser_pointers[$parser_id]) && (FALSE !== $this->_parser_pointers[$parser_id]))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            if (is_object($this->_mysqli))
                            {
                                do
                                {
                                    $aRow = $this->_parser_pointers[$parser_id]->fetch_assoc();
                                }
                                while ((FALSE !== $aRow)
                                       &&
                                       (NULL !== $aRow)
                                       &&
                                       ('' == $aRow['user'])
                                      );
                            }
                            else
                            {
                                do
                                {
                                    $aRow = mysql_fetch_assoc($this->_parser_pointers[$parser_id]);
                                }
                                while ((FALSE !== $aRow)
                                       &&
                                       (NULL !== $aRow)
                                       &&
                                       ('' == $aRow['user'])
                                      );
                            }
                            if (isset($aRow['user']))
                            {
                                $user_array = array('user'                 => $aRow['user'],
                                                    'description'          => $aRow['description'],
                                                    'email'                => $aRow['email'],
                                                    'enabled'              => (0 == $aRow['desactivated']),
                                                    'group'                => $aRow['group'],
                                                    'locked'               => (1 == $aRow['locked']),
                                                    'sms'                  => $aRow['sms'],
                                                    'synchronized'         => (1 == $aRow['synchronized']),
                                                    'synchronized_channel' => $aRow['synchronized_channel'],
                                                    'synchronized_server'  => $aRow['synchronized_server'],
                                                    'synchronized_time'    => $aRow['synchronized_time'],
                                                    'token'                => $aRow['token_serial'],
                                                    'synchronized_dn'      => $aRow['synchronized_dn']
                                                    );
                            }
                        }
                        break;
                    case 'files':
                    default:
                        do
                        {
                            $file = readdir($this->_parser_pointers[$parser_id]);
                        }
                        while ((FALSE !== $file)
                               &&
                               ((substr($file, -3) != ".db")
                                ||
                                ($file == '.db'))
                              );
                        if (FALSE !== $file)
                        {
                            $user = substr($file,0,-3);
                            $this->SetUser($user);
                            $user_array = array('user'                 => $user,
                                                'description'          => $this->GetUserDescription(),
                                                'email'                => $this->GetUserEmail(),
                                                'enabled'              => (1 == $this->GetUserActivated()),
                                                'group'                => $this->GetUserGroup(),
                                                'locked'               => (1 == $this->GetUserLocked()),
                                                'sms'                  => $this->GetUserSms(),
                                                'synchronized'         => (1 == $this->GetUserSynchronized()),
                                                'synchronized_channel' => $this->GetUserSynchronizedChannel(),
                                                'synchronized_server'  => $this->GetUserSynchronizedServer(),
                                                'synchronized_time'    => $this->GetUserSynchronizedTime(),
                                                'token'                => $this->GetUserTokenSerialNumber(),
                                                'synchronized_dn'      => $this->GetUserSynchronizedDn()
                                               );
                        }
                        else
                        {
                            $user_array = FALSE;
                            closedir($this->_parser_pointers[$parser_id]);
                        }
                }
            }
            
        }
        if (FALSE === $user_array)
        {
            unset($this->_parser_pointers[$parser_id]);
        }
        return $user_array;
    }


    function GetAlgorithmsList()
    {
        $algorithms_list = '';
        $algorithms_array = explode("*",$this->_valid_algorithms);
        foreach ($algorithms_array as $algorithm_one)
        {
            if ('' != trim($algorithm_one))
            {
                $algorithms_list.= (('' != $algorithms_list)?"\t":'').trim($algorithm_one);
            }
        }
        return $algorithms_list;
    }


    function IsValidAlgorithm($algo_to_check)
    {
        return (FALSE !== strpos(strtolower($this->_valid_algorithms), strtolower('*'.$algo_to_check.'*')));
    }


    function GetUserScratchPasswordsArray($user = '')
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        if ($this->_user_data['scratch_passwords'] != '')
        {
            return (explode(",",$this->_user_data['scratch_passwords']));
        }
        else
        {
            return array();
        }
        return (explode(",",$this->_user_data['scratch_passwords']));
    }


    function RemoveUserUsedScratchPassword($to_remove)
    {
        $scratch_passwords = trim($this->_user_data['scratch_passwords']);
        if (FALSE !== ($pos = strpos($scratch_passwords, $to_remove)))
        {
            $scratch_passwords = trim(substr($scratch_passwords.' ', $pos+strlen($to_remove)+1));
            $this->_user_data['scratch_passwords'] = $scratch_passwords;
            $result = $this->WriteUserData();
        }
        return TRUE;
    }


    function GetScratchPasswordsAmount()
    {
        return $this->_config_data['scratch_passwords_amount'];
    }


    function SetScratchPasswordsAmount($value)
    {
        // Must be between 3 and 400
        $amount = intval($value);
        $amount = ($amount < 3)?3:$amount;
        $amount = ($amount > 400)?400:$amount;
        $this->_config_data['scratch_passwords_amount'] = $amount;
        return TRUE;
    }


    function GetUserScratchPasswordsList($user = '')
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        $digits = $this->GetScratchPasswordsDigits();

        /* This option is too long
        if (function_exists('openssl_random_pseudo_bytes')) {
            $seed = openssl_random_pseudo_bytes(16);
        } else {
        */
            $seed = hex2bin(md5('sCratchP@sswordS'.$this->GetUser().bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999))));
        /* } */

        $scratch_loop = $this->GetScratchPasswordsAmount();
        if (($scratch_loop * (1+$digits) * 2.5) > 65535)
        {
            $scratch_loop = inval(65535 / ((1+$digits) * 2.5));
            $this->SetScratchPasswordsAmount($scratch_loop);
        }
        $scratch_passwords = trim($this->_user_data['scratch_passwords']);
        if (strlen($scratch_passwords) > ((1.5 * $scratch_loop) * (1 + $digits)))
        {
            $scratch_passwords = '';
        }
        $passwords_list = '';

        for ($i=0; $i<$scratch_loop; $i++)
        {
            $one_password = $this->GenerateOathHotp($seed,$i,$digits);
            $scratch_passwords.= (('' != $scratch_passwords)?",":'').$one_password;
            $passwords_list.= (('' != $passwords_list)?"\t":'').$one_password;
        }
        $this->_user_data['scratch_passwords'] = $scratch_passwords;
        $result = $this->WriteUserData();
        if (!$result)
        {
            $passwords_list = '';
        }
        return ($passwords_list);
    }


    function SetUserDataReadFlag($flag)
    {
        $this->_user_data_read_flag = $flag;
        return TRUE;
    }


    function GetUserDataReadFlag()
    {
        return $this->_user_data_read_flag;
    }


    function SetUserMultiAccount($value)
    {
        $this->_user_data['multi_account'] = $value;
        return TRUE;
    }


    function GetUserMultiAccount()
    {
        return $this->_user_data['multi_account'];
    }


    function SetUserEmail($first_param, $second_param = "*-*")
    {
        $valid = FALSE;
        $result = "";
        if ($second_param == "*-*")
        {
            if (('' == $first_param) || (FALSE !== strpos($first_param, '@')))
            {
                $result = $first_param;
                $valid = TRUE;
            }
        }
        else
        {
            $this->SetUser($first_param);
            if (('' == $second_param) || (FALSE !== strpos($second_param, '@')))
            {
                $result = $second_param;
                $valid = TRUE;
            }
        }
        $this->_user_data['email'] = $result;

        return $valid;
    }


    function SetUserAttribute($first_param, $second_param, $third_param = "*-*")
    {
        $result = FALSE;
        if ($third_param == "*-*")
        {
            if ($this->IsOptionInSchema('users', $first_param))
            {
                $this->_user_data[$first_param] = $second_param;
                $result = TRUE;
            }
        }
        else
        {
            if ($this->IsOptionInSchema('users', $second_param))
            {
                $this->SetUser($first_param);
                $this->_user_data[$second_param] = $third_param;
                $result = TRUE;
            }
        }
        return $result;
    }


    function GetUserEmail($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['email'];
    }


    function SetUserGroup($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        $this->_user_data['group'] = $result;

        return $result;
    }


    function GetUserGroup($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['group'];
    }


    function SetUserDescription($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        $this->_user_data['description'] = $result;
        
        $this->SetUserMultiAccount((FALSE !== strpos($result,'multi_account'))?1:0);

        return $result;
    }


    function GetUserDescription($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['description'];
    }


    function SetUserSeedPassword($value)
    {
        $this->_user_data['seed_password'] = $value;
    }


    function GetUserSeedPassword()
    {
        return $this->_user_data['seed_password'];
    }


    function SetUserSms($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        $this->_user_data['sms'] = $result;
        return TRUE;
    }


    function GetUserSms($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['sms'];
    }


    function SetUserPrefixPin($value)
    {
        $this->_user_data['request_prefix_pin'] = ((intval($value) > 0)?1:0);
        return TRUE;
    }


    function GetUserPrefixPin()
    {
        return $this->_user_data['request_prefix_pin'];
    }


    function EnableUserPrefixPin()
    {
        $this->_user_data['request_prefix_pin'] = 1;
        return TRUE;
    }


    function DisableUserPrefixPin()
    {
        $this->_user_data['request_prefix_pin'] = 0;
        return TRUE;
    }


    function IsUserPrefixPin()
    {
        return (1 == ($this->_user_data['request_prefix_pin']));
    }


    function SetUserRequestLdapPassword($value)
    {
        $this->_user_data['request_ldap_pwd'] = ((intval($value) > 0)?1:0);
        return TRUE;
    }


    function EnableUserRequestLdapPassword()
    {
        $this->_user_data['request_ldap_pwd'] = 1;
        return TRUE;
    }


    function DisableUserRequestLdapPassword()
    {
        $this->_user_data['request_ldap_pwd'] = 0;
        return TRUE;
    }


    function IsUserRequestLdapPasswordEnabled()
    {
        return (1 == ($this->_user_data['request_ldap_pwd']));
    }


    function SetUserLdapHashCache($first_param, $second_param = "*-*")
    {
        $value = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $value = $second_param;
        }
        $this->_user_data['ldap_hash_cache'] = $value;
        $this->_user_data['ldap_hash_validity'] = time() + $this->GetLdapHashCacheTime();
        return TRUE;
    }
    
   
    function ResetUserLdapHashCache($user = '')
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        $this->_user_data['ldap_hash_cache'] = '';
        $this->_user_data['ldap_hash_validity'] = 0;
        return TRUE;
    }


    function GetUserLdapHashCache($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        if ($this->_user_data['ldap_hash_validity'] >= time())
        {
            $value = $this->_user_data['ldap_hash_cache'];
        }
        else
        {
            $this->_user_data['ldap_hash_cache'] = '';
            $value = '';
        }
        return $value;
    }


    function SetUserAlgorithm($algorithm)
    {
        $result = FALSE;
        if ($this->IsValidAlgorithm($algorithm))
        {
            $this->_user_data['algorithm'] = strtolower($algorithm);
            $result = TRUE;
        }
        else
        {
            $this->WriteLog("Error: ".$algorithm." algorithm is unknown", FALSE, FALSE, 23, 'User');
        }
        return $result;
    }


    function GetUserAlgorithm($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        $result = strtolower($this->_user_data['algorithm']);
        if (FALSE === strpos(strtolower($this->_valid_algorithms), strtolower('*'.$result.'*')))
        {
            $result = '';
        }

        return $result;
    }


    function SetUserTokenAlgoSuite($token_algo_suite)
    {
        $this->_user_data['token_algo_suite'] = strtoupper(('' == $token_algo_suite)?'HMAC-SHA1':$token_algo_suite);
        return TRUE;
    }


    function GetUserTokenAlgoSuite($user = '')
    {
        return strtoupper(('' == $this->_user_data['token_algo_suite'])?'HMAC-SHA1':$this->_user_data['token_algo_suite']);
    }


    function SetUserTokenSeed($seed)
    {
        $this->_user_data['token_seed'] = $seed;
    }


    function GetUserTokenSeed()
    {
        return $this->_user_data['token_seed'];
    }


    function SetUserSmsOtp($value)
    {
        $this->_user_data['sms_otp'] = $value;
    }


    function GetUserSmsOtp()
    {
        // Be sure that we never have an SMS OTP smaller than 4 digits
        if (strlen($this->_user_data['sms_otp']) < 4)
        {
            $this->_user_data['sms_otp'] = md5($this->GetEncryptionKey().$this->GetUserTokenSeed().mt_rand(100000,999999).date("YmdHis"));
        }
        return $this->_user_data['sms_otp'];
    }


    function SetUserSmsValidity($value)
    {
        $this->_user_data['sms_validity'] = $value;
    }


    function GetUserSmsValidity()
    {
        return $this->_user_data['sms_validity'];
    }


    function SetUserPin($pin)
    {
        $this->_user_data['user_pin'] = $pin;
    }


    function GetUserPin($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['user_pin'];
    }


    function SetUserAutolockTime($value)
    {
        $this->_user_data['autolock_time'] = intval($value);
    }


    function GetUserAutolockTime()
    {
        return intval($this->_user_data['autolock_time']);
    }


    function SetUserTokenDeltaTime($delta_time)
    {
        $this->_user_data['delta_time'] = $delta_time;
    }


    function GetUserTokenDeltaTime()
    {
        return $this->_user_data['delta_time'];
    }


    function SetUserKeyId($key_id)
    {
        $this->_user_data['key_id'] = $key_id;
    }


    function GetUserKeyId()
    {
        return $this->_user_data['key_id'];
    }


    function SetUserTokenNumberOfDigits($number_of_digits)
    {
        $this->_user_data['number_of_digits'] = $number_of_digits;
    }


    function GetUserTokenNumberOfDigits()
    {
        return $this->_user_data['number_of_digits'];
    }


    function SetUserTokenTimeInterval($interval)
    {
        if (intval($interval) > 0)
        {
            $this->_user_data['time_interval'] = intval($interval);
        }
    }


    function GetUserTokenTimeInterval()
    {
        return $this->_user_data['time_interval'];
    }


    function GetUserEncryptionHash()
    {
        return $this->_user_data['encryption_hash'];
    }


    function SetUserTokenSerialNumber($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $value = $second_param;
        }
        $this->_user_data['token_serial'] = strtolower($value);

        return $value;
    }


    // TODO Add new method RemoveUserTokenSerialNumber/AddUserTokenSerialNumber like AddTokenAttributedUsers
    
    function GetUserTokenSerialNumber($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return strtolower($this->_user_data['token_serial']);
    }


    function SetUserTokenLastEvent($last_event)
    {
        $this->_user_data['last_event'] = $last_event;
    }


    function GetUserTokenLastEvent()
    {
        return $this->_user_data['last_event'];
    }


    function SetUserTokenLastLogin($time)
    {
        $this->_user_data['last_login'] = $time;
    }


    function GetUserTokenLastLogin()
    {
        return $this->_user_data['last_login'];
    }


    function SetUserTokenLastError($time)
    {
        $this->_user_data['last_error'] = $time;
    }


    function GetUserTokenLastError()
    {
        return $this->_user_data['last_error'];
    }


    function SetUserLocked($first_param, $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            $data = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $this->_user_data['locked'] = $data;

        return $data;
    }


    function GetUserLocked($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return intval($this->_user_data['locked']);
    }


    function SetUserActivated($first_param, $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            $data = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $desactive = ($data > 0)?0:1;
        $this->_user_data['desactivated'] = $desactive;
        
        if (0 == $desactive) {
            $this->SetUserErrorCounter(0);
            $this->SetUserLocked(0);
        }

        return $data;
    }


    function GetUserActivated($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        $active = intval($this->_user_data['desactivated'] > 0)?0:1;
        return $active;
    }


    function SetUserSynchronized($first_param, $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            $data = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $this->_user_data['synchronized'] = $data;

        return $data;
    }


    function GetUserSynchronized($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return intval($this->_user_data['synchronized']);
    }
    

    function IsUserSynchronized($user = '')
    {
        return (1 == ($this->GetUserSynchronized($user)));
    }
    

    function SetUserSynchronizedChannel($first_param, $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            $data = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $this->_user_data['synchronized_channel'] = $data;

        return $data;
    }


    function GetUserSynchronizedChannel($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return ($this->_user_data['synchronized_channel']);
    }
    

    function SetUserSynchronizedDn($first_param, $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            $data = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $this->_user_data['synchronized_dn'] = $data;

        return $data;
    }


    function GetUserSynchronizedDn($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return ($this->_user_data['synchronized_dn']);
    }
    

    function SetUserSynchronizedServer($first_param, $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            $data = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $this->_user_data['synchronized_server'] = $data;

        return $data;
    }


    function GetUserSynchronizedServer($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return ($this->_user_data['synchronized_server']);
    }
    

    function SetUserSynchronizedTime($first_param = "*-*", $second_param = "*-*")
    {
        $data = 0;
        if ($second_param == "*-*")
        {
            if ($first_param == "*-*")
            {
                $data = time();
            }
            else
            {
                $data = $first_param;
            }
        }
        else
        {
            $this->SetUser($first_param);
            $data = $second_param;
        }
        $this->_user_data['synchronized_time'] = $data;

        return $data;
    }


    function GetUserSynchronizedTime($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return intval($this->_user_data['synchronized_time']);
    }


    function SetUserErrorCounter($counter)
    {
        $this->_user_data['error_counter'] = $counter;
    }


    function GetUserErrorCounter()
    {
        return $this->_user_data['error_counter'];
    }


    /*********************************************************************
     *
     * Name: CreateToken
     * Short description: Create a new token, without parameter, create
     *                      a Google Authenticator compatible token
     *
     * Creation 2013-02-08
     * Update 2013-12-23
     * @package multiotp
     * @version 4.1.0
     * @author SysCo/al
     *
     * @param   string  $serial      
     * @param   string  $algorithm
     * @param   string  $seed
     * @param   int     $number_of_digits
     * @param   int     $time_interval_or_next_event
     * @param   string  $manufacturer
     * @param   string  $issuer
     * @param   string  $description
     * @return  boolean
     *
     *********************************************************************/
    function CreateToken($serial = '', $algorithm = 'totp', $seed = '', $number_of_digits = 6, $time_interval_or_next_event = -1, $manufacturer = 'multiOTP', $issuer = '', $description = '', $token_algo_suite = '')
    {
        $the_serial = strtolower($serial);
        if ('' == $the_serial)
        {
            $the_serial = strtolower('mu'.bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999)));
        }
        $the_description = $description;
        if ('' == $the_description)
        {
            $the_description = trim($manufacturer.' '.$the_serial);
        }
        $the_token = strtolower($the_serial);
        if ($this->ReadTokenData($the_token, TRUE))
        {
            return FALSE; // ERROR: token already exists.
        }
        else
        {
            $this->SetToken($the_serial);
            $this->SetTokenDescription($the_description);
            $this->SetTokenManufacturer(('' != $manufacturer)?$manufacturer:'multiOTP');
            $this->SetTokenIssuer(('' != $issuer)?$issuer:$this->GetIssuer());
            $this->SetTokenSerialNumber($the_serial);
            $this->SetTokenAlgorithm(strtolower($algorithm));
            $this->SetTokenAlgoSuite(strtolower($token_algo_suite));
            $this->SetTokenKeyAlgorithm(strtolower($algorithm));
            $this->SetTokenOtp('TRUE');

            $this->SetTokenFormat('DECIMAL');
            $this->SetTokenNumberOfDigits($number_of_digits);
            $this->SetTokenDeltaTime(0);
            
            /* This option is too long
            if (function_exists('openssl_random_pseudo_bytes')) {
                $the_seed = (('' == $seed)?bin2hex(openssl_random_pseudo_bytes(20)):$seed);
            } else {
            */
                $the_seed = (('' == $seed)?substr(md5(date("YmdHis").mt_rand(100000,999999)),0,20).substr(md5(mt_rand(100000,999999).date("YmdHis")),0,20):$seed);
            /* } */

            if ('hotp' == strtolower($algorithm))
            {
                $next_event = ((-1 == $time_interval_or_next_event)?0:$time_interval_or_next_event);
                $time_interval = 0;
            }
            else
            {
                $next_event = 0;
                $time_interval = ((-1 == $time_interval_or_next_event)?30:$time_interval_or_next_event);
                if ("motp" == strtolower($algorithm))
                {
                    /* This option is too long
                    if (function_exists('openssl_random_pseudo_bytes')) {
                        $the_seed = (('' == $seed)?bin2hex(openssl_random_pseudo_bytes(8)):$seed);
                    } else {
                    */
                        $the_seed = (('' == $seed)?substr(md5(date("YmdHis").mt_rand(100000,999999)),0,16):$seed);
                    /* } */
                    $time_interval = 10;
                }
            }

            $this->SetTokenSeed($the_seed);
            $this->SetTokenLastEvent($next_event - 1);
            $this->SetTokenTimeInterval($time_interval);
            
            return $this->WriteTokenData();
        }
    }    


    function SetTokenSerialNumberLength($value)
    {
        $this->_config_data['token_serial_number_length'] = trim($value);
    }
    
    
    function AddTokenSerialNumberLength($length)
    {
        if (intval($length) > 0)
        {
            $actual = trim($this->GetTokenSerialNumberLength());
            $length_exists = FALSE;

            // We add the serial number length only if it is not already attributed
            $token_serial_number_length_array = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$actual))));
            foreach($token_serial_number_length_array as $one_length)
            {
                if (intval($one_length) == intval($length))
                {
                    $length_exists = TRUE;
                    break;
                }
            }
            if (!$length_exists)
            {
                $actual.=' '.intval($length);
                $this->SetTokenSerialNumberLength($actual);

                // And we save this information directly in the configuration
                $this->WriteConfigData();
            }
        }
        return TRUE;
    }


    function GetTokenSerialNumberLength()
    {
        $token_serial_number_length = $this->_config_data['token_serial_number_length'];
        if (FALSE === strpos($token_serial_number_length, '12'))
        {
            // 12 is the RFC size of the serial number, we must have it and we add it if needed
            $token_serial_number_length.=' 12';
        }
        return $token_serial_number_length;
    }


    function SetTokenOtpListOfLength($value)
    {
        $this->_config_data['token_otp_list_of_length'] = trim($value);
    }
    
    
    function AddTokenOtpListOfLength($length)
    {
        if (intval($length) > 0)
        {
            $actual = trim($this->GetTokenOtpListOfLength());
            $length_exists = FALSE;

            // We add the OTP length only if it is not already attributed
            $token_otp_list_of_length_array = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$actual))));
            foreach($token_otp_list_of_length_array as $one_length)
            {
                if (intval($one_length) == intval($length))
                {
                    $length_exists = TRUE;
                    break;
                }
            }
            if (!$length_exists)
            {
                $actual.=' '.intval($length);
                $this->SetTokenOtpListOfLength($actual);

                // And we save this information directly in the configuration
                $this->WriteConfigData();
            }
        }
        return TRUE;
    }


    function GetTokenOtpListOfLength()
    {
        $token_otp_list_of_length = $this->_config_data['token_otp_list_of_length'];
        if (FALSE === strpos($token_otp_list_of_length, '6'))
        {
            // 6 is an RFC size of the OTP, we should have it and we add it if needed
            $token_otp_list_of_length.=' 6';
        }
        return $token_otp_list_of_length;
    }


    function SetTokenDescription($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetToken($first_param);
            $value = $second_param;
        }
        $this->_token_data['description'] = $value;
        return $value;
    }


    function GetTokenDescription($token = '')
    {
        if($token != '')
        {
            $this->SetToken($token);
        }
        return $this->_token_data['description'];
    }


    function SetToken($token)
    {
        $this->ResetTokenArray();
        $this->_token = strtolower($token);
        $this->ReadTokenData('', TRUE); // First parameter empty, otherwise it will loop with SetToken !
    }


    function RenameCurrentToken($new_token, $no_error_info = FALSE)
    {
        $esc_actual = escape_mysql_string($this->GetToken());
        $esc_new    = escape_mysql_string($new_token);
        $result = FALSE;
        if ($this->CheckTokenExists($new_token)) // Check if the new token already exists
        {
            $this->WriteLog("Error: Unable to rename the current token ".$this->GetToken()." to ".$new_token." because it already exists", FALSE, FALSE, 28, 'Token', '');
        }
        else
        {
            if ($this->CheckTokenExists()) // Check if the current token already exists
            {
                if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType()))
                {
                    switch ($this->GetBackendType())
                    {
                        case 'mysql':
                            if ($this->OpenMysqlDatabase())
                            {
                                if ('' != $this->_config_data['sql_tokens_table'])
                                {
                                    $sQuery = "UPDATE `".$this->_config_data['sql_tokens_table']."` SET token_id='".$esc_new."' WHERE `token_id`='".$esc_actual."'";
                                    
                                    if (is_object($this->_mysqli))
                                    {
                                        if (!($rResult = $this->_mysqli->query($sQuery)))
                                        {
                                            if (!$no_error_info)
                                            {
                                                $this->WriteLog("Error: Could not rename the token ".$this->GetToken().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'Token');
                                            }
                                        }
                                        else
                                        {
                                            $num_rows = $this->_mysqli->affected_rows;
                                        }
                                    }
                                    elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                    {
                                        if (!$no_error_info)
                                        {
                                            $this->WriteLog("Error: Could not rename the token ".$this->GetToken().": ".mysql_error(), FALSE, FALSE, 28, 'Token');
                                        }
                                    }
                                    else
                                    {
                                        $num_rows = mysql_affected_rows($this->_mysql_database_link);
                                    }
                                    
                                    if (0 == $num_rows)
                                    {
                                        $this->WriteLog("Error: Could not rename the token ".$this->GetToken().". Token does not exist", FALSE, FALSE, 29, 'Token');
                                    }
                                    else
                                    {
                                        $this->WriteLog("Info: Token ".$this->GetToken()." successfully renamed to $new_token", FALSE, FALSE, 19, 'Token');
                                        $result = TRUE;
                                    }
                                }
                            }
                            break;
                        case 'files':
                        default:
                            $old_token_filename = strtolower($this->GetToken()).'.db';
                            $new_token_filename = strtolower($new_token).'.db';
                            rename($this->GetTokensFolder().$old_token_filename, $this->GetTokensFolder().$new_token_filename);
                            $result = TRUE;
                            break;
                    }
                }
            }
            if ($result)
            {
                $this->_token = $new_token;
            }
        }
        return $result;
    }


    function GetToken()
    {
        return strtolower($this->_token);
    }


    function CheckTokenExists($token = '')
    {
        $check_token = strtolower('' != $token)?$token:$this->GetToken();
        $result = FALSE;
        
        if ('' != trim($check_token))
        {
            if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType()))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '{$check_token}'";
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                $this->WriteLog("Error: Token ".$check_token.". does not exist", FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                            else
                            {
                                $result = TRUE;
                            }
                        }
                        break;
                    case 'files':
                    default:
                        $token_filename = strtolower($check_token).'.db';
                        $result = file_exists($this->GetTokensFolder().$token_filename);
                        break;
                }
            }
        }
        return $result;
    }


    function ResetLastImportedTokensArray()
    {
        $this->_last_imported_tokens = array();
    }


    function AddLastImportedToken($token)
    {
        $this->_last_imported_tokens[] = $token;
    }


    function GetLastImportedTokens()
    {
        return $this->_last_imported_tokens;
    }


    function SetTokenManufacturer($manufacturer)
    {
        $this->_token_data['manufacturer'] = $manufacturer;
    }


    function GetTokenManufacturer()
    {
        return $this->_token_data['manufacturer'];
    }


    function GetTokenEncryptionHash()
    {
        return $this->_token_data['encryption_hash'];
    }


    // This will (re)set only one user to the token
    function SetTokenAttributedUsers($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetToken($first_param);
            $result = $second_param;
        }
        $this->_token_data['attributed_users'] = $result;

        return $result;
    }


    function AddTokenAttributedUsers($first_param, $second_param = "*-*")
    {
        $data = "";
        $result = FALSE;
        if ($second_param == "*-*")
        {
            $data = $first_param;
            $token = $this->GetToken();
        }
        else
        {
            $token = $first_param;
            if ($this->CheckTokenExists($token))
            {
                $this->SetToken($token);
            }
            $data = $second_param;
        }
        if ($this->CheckTokenExists($token))
        {
            $actual = trim($this->GetTokenAttributedUsers());
            // We attribute the user only if it is not already attributed
            if (FALSE === strpos(','.$actual.',', ','.$data.','))
            {
                $this->SetTokenAttributedUsers($actual.(('' != $actual)?',':'').$data);
            }
            $result = TRUE;
        }
        return $result;
    }


    function RemoveTokenAttributedUsers($first_param, $second_param = "*-*")
    {
        $data = "";
        $result = FALSE;
        if ($second_param == "*-*")
        {
            $data = $first_param;
            $token = $this->GetToken();
        }
        else
        {
            $token = $first_param;
            if ($this->CheckTokenExists($token))
            {
                $this->SetToken($token);
            }
            $data = $second_param;
        }
        if ($this->CheckTokenExists($token))
        {
            if (FALSE !== strpos(','.trim($this->_token_data['attributed_users']).',', ','.$data.','))
            {
                $actual = str_replace(','.$data.',',',',','.trim($this->_token_data['attributed_users']).',');
                $this->SetTokenAttributedUsers(substr($actual,1, strlen($actual)-2));
                $result = TRUE;
            }
        }
        return $result;
    }


    function GetTokenAttributedUsers($token = '')
    {
        if($token != '')
        {
            $this->SetToken($token);
        }
        return $this->_token_data['attributed_users'];
    }


    function SetTokenSerialNumber($token_serial)
    {
        $this->_token_data['token_serial'] = strtolower($token_serial);
        $len_token_serial = strlen($token_serial);
        if ($len_token_serial > 0)
        {
            // We add this length automatically in the list of the existing serial number length
            $this->AddTokenSerialNumberLength($len_token_serial);
        }
    }


    function GetTokenSerialNumber()
    {
        return strtolower($this->_token_data['token_serial']);
    }


    function SetTokenIssuer($issuer)
    {
        if ('' == $issuer)
        {
            $this->_token_data['issuer'] = $this->GetIssuer();
        }
        else
        {
            $this->_token_data['issuer'] = $issuer;
        }
    }


    function GetTokenIssuer()
    {
        return $this->_token_data['issuer'];
    }


    function SetTokenKeyAlgorithm($key_algorithm)
    {
        $this->_token_data['key_algorithm'] = $key_algorithm;
    }


    function GetTokenKeyAlgorithm()
    {
        return $this->_token_data['key_algorithm'];
    }


    function SetTokenAlgorithm($algorithm)
    {
        $result = FALSE;
        if (FALSE === strpos(strtolower($this->_valid_algorithms), strtolower('*'.$algorithm.'*')))
        {
            $this->WriteLog("Error: ".$algorithm." algorithm unknown for token ".$this->GetToken(), FALSE, FALSE, 23, 'Token');
        }
        else
        {
            $this->_token_data['algorithm'] = strtolower($algorithm);
            $result = TRUE;
        }
        return $result;
    }


    function GetTokenAlgorithm()
    {
        $result = $this->_token_data['algorithm'];
        if (FALSE === strpos(strtolower($this->_valid_algorithms), strtolower('*'.$result.'*')))
        {
            $result = '';
        }

        return $result;
    }


    function SetTokenAlgoSuite($token_algo_suite)
    {
        $this->_token_data['token_algo_suite'] = strtoupper(('' == $token_algo_suite)?'HMAC-SHA1':$token_algo_suite);
        return TRUE;
    }


    function GetTokenAlgoSuite()
    {
        return strtoupper(('' == $this->_token_data['token_algo_suite'])?'HMAC-SHA1':$this->_token_data['token_algo_suite']);
    }


    function SetTokenOtp($otp)
    {
        $this->_token_data['otp'] = $otp;
    }


    function GetTokenOtp()
    {
        return $this->_token_data['otp'];
    }


    function SetTokenFormat($format)
    {
        $this->_token_data['format'] = $format;
    }


    function GetTokenFormat()
    {
        return $this->_token_data['format'];
    }


    function SetTokenNumberOfDigits($number_of_digits)
    {
        $this->_token_data['number_of_digits'] = $number_of_digits;
        // We add this number of digits automatically in the list of the existing list of length
        $this->AddTokenOtpListOfLength($number_of_digits);
    }


    function GetTokenNumberOfDigits()
    {
        return $this->_token_data['number_of_digits'];
    }


    function SetTokenLastEvent($last_event)
    {
        $this->_token_data['last_event'] = $last_event;
    }


    function GetTokenLastEvent()
    {
        return $this->_token_data['last_event'];
    }


    function SetTokenLastLogin($time)
    {
        $this->_token_data['last_login'] = $time;
    }


    function GetTokenLastLogin()
    {
        return $this->_token_data['last_login'];
    }


    function SetTokenErrorCounter($counter)
    {
        $this->_token_data['error_counter'] = $counter;
    }


    function GetTokenErrorCounter()
    {
        return $this->_token_data['error_counter'];
    }


    function SetTokenDeltaTime($delta_time)
    {
        $this->_token_data['delta_time'] = $delta_time;
    }


    function GetTokenDeltaTime()
    {
        return $this->_token_data['delta_time'];
    }


    function SetTokenTimeInterval($time_interval)
    {
        $this->_token_data['time_interval'] = $time_interval;
    }


    function GetTokenTimeInterval()
    {
        return $this->_token_data['time_interval'];
    }


    /**
     * @brief   Set the token seed in hexadecimal
     *
     * @param   string  $token_seed  Token in hexadecimal
     * @return  none
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.1.0
     * @date      2014-01-04
     * @since   2010-08-12
     */
    function SetTokenSeed($token_seed)
    {
        $this->_token_data['token_seed'] = $token_seed;
    }


    function GetTokenSeed()
    {
        return $this->_token_data['token_seed'];
    }


    function SetTokensFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_tokens_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: Unable to create the missing tokens folder ".$new_folder, FALSE, FALSE, 28, 'System',  '');
            }
        }
    }


    function GetTokensFolder()
    {
        if ('' == $this->_tokens_folder)
        {
            $this->SetTokensFolder($this->GetScriptFolder()."tokens/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_tokens_folder);
    }


    function GetTokensList()
    {
        return $this->GetList('token_id', 'sql_tokens_table', $this->GetTokensFolder());
    }


    function DeleteToken($token = '', $no_error_info = FALSE)
    {
        if ('' != $token)
        {
            $this->SetToken($token);
        }
        
        $result = FALSE;
        
        // First, we delete the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $token_filename = strtolower($this->_token).'.db';
            if (!file_exists($this->GetTokensFolder().$token_filename))
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: *Unable to delete token ".$this->GetToken().", the tokens database file ".$this->GetTokensFolder().$token_filename." does not exist", FALSE, FALSE, 29, 'Token',  '');
                }
                else
                {
                    $this->WriteLog("Error: Unable to delete token ".$this->GetToken(), FALSE, FALSE, 29, 'Token',  '');
                }
            }
            else
            {
                $result = unlink($this->GetTokensFolder().$token_filename);
                if ($result)
                {
                    if ($this->GetVerboseFlag())
                    {
                        $this->WriteLog("Info: *Token ".$this->GetToken()." successfully deleted", FALSE, FALSE, 19, 'Token', '');
                    }
                }
                else
                {
                    $this->WriteLog("Error: Unable to delete token ".$this->GetToken(), FALSE, FALSE, 28, 'Token',  '');
                }
            }
        }

        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_tokens_table'])
                        {
                            $sQuery  = "DELETE FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '".$this->GetToken()."'";
                            
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    if (!$no_error_info)
                                    {
                                        $this->WriteLog("Error: Could not delete token ".$this->GetToken().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'Token', '');
                                    }
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete token ".$this->GetToken().": ".mysql_error(), FALSE, FALSE, 28, 'Token', '');
                                }
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete token ".$this->GetToken().". Token does not exist", FALSE, FALSE, 29, 'Token', '');
                                }
                            }
                            else
                            {
                                if ($this->GetVerboseFlag())
                                {
                                    $this->WriteLog("Info: token ".$this->GetToken()." successfully deleted", FALSE, FALSE, 19, 'Token', '');
                                }
                                $result = TRUE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }                        
        }
        return $result;
    }


    function ReadTokenData($token = '', $create = FALSE)
    {
        if ('' != $token)
        {
            $this->SetToken($token);
        }
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_token_data['encryption_hash'] = '';
        
        // First, we read the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $token_filename = strtolower($this->GetToken()).'.db';
            if (!file_exists($this->GetTokensFolder().$token_filename))
            {
                if (!$create)
                {
                    $this->WriteLog("Error: database file ".$this->GetTokensFolder().$token_filename." for token ".$this->_token." does not exist", FALSE, FALSE, 29, 'System', '');
                }
            }
            else
            {
                $file_handler = fopen($this->GetTokensFolder().$token_filename, "rt");
                $first_line = trim(fgets($file_handler));
                
                while (!feof($file_handler))
                {
                    $line = trim(fgets($file_handler));
                    $line_array = explode("=",$line,2);
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                    }
                    if ('' != trim($line_array[0]))
                    {
                        $this->_token_data[strtolower($line_array[0])] = $line_array[1];
                    }
                }
                
                fclose($file_handler);
                $result = TRUE;

                if ('' != $this->_token_data['encryption_hash'])
                {
                    if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                    {
                        $this->_token_data['encryption_hash'] = "ERROR";
                        $this->WriteLog("Error: the token information encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                        $result = FALSE;
                    }
                }
            }
        }

        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_tokens_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '".$this->_token."'";
                            $aRow = NULL;
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 199, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    $aRow = $rResult->fetch_assoc();
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    $aRow = mysql_fetch_assoc($rResult);
                                }
                            }

                            if (NULL != $aRow)
                            {
                                $result = FALSE;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['tokens']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if (($in_the_schema) && ($key != 'token_id'))
                                    {
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $this->_token_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                        }
                                        else
                                        {
                                            $this->_token_data[$key] = $value;
                                        }
                                    }                                    
                                    elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: *The key ".$key." is not in the tokens database schema", FALSE, FALSE, 98, 'System', '');
                                    }
                                    $result = TRUE;
                                }
                                if(0 == count($aRow) && !$create)
                                {
                                    $this->WriteLog("Error: SQL database entry for token ".$this->_token." does not exist", FALSE, FALSE, 29, 'System', '');
                                }
                            }
                        }
                        if ('' != $this->_token_data['encryption_hash'])
                        {
                            if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $this->_token_data['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the tokens mysql encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }
        $this->SetTokenDataReadFlag($result);
        return $result;
    }


    function WriteTokenData()
    {
        if ('' == trim($this->GetToken())) {
            $result = false;
        } else {
            $result = $this->WriteData('Token',
                                       'tokens',
                                       $this->GetTokensFolder(),
                                       $this->_token_data,
                                       false,
                                       'token_id',
                                       $this->GetToken()
                                      );
        }
        return $result;
    }


    function SetLastClearOtpValue($value = '')
    {
        $this->_last_clear_otp_value = $value;
    }


    function GetLastClearOtpValue()
    {
        return $this->_last_clear_otp_value;
    }


    function ResetTemporaryBadServer()
    {
        $this->_servers_temp_bad_list = array();
    }


    function AddTemporaryBadServer($server)
    {
        $this->_servers_temp_bad_list[] = $server;
    }


    function GetTemporaryBadServer()
    {
        return $this->_servers_temp_bad_list;
    }


    function CheckUserLdapPassword($ldap_username, $ldap_password)
    {
        $this->SetLdapServerReachable(FALSE);
        $result = FALSE;

        // DistinguishedName must be encoded in UTF-8
        $ldap_bind_dn = encode_utf8_if_needed($ldap_username);
        
        if (('' != $ldap_username) && (FALSE === strpos(strtolower($ldap_bind_dn), 'cn=')))
        {
            $ldap_bind_dn = 'CN='.$ldap_bind_dn.','.$this->GetLdapBaseDn();
        }

        if (!function_exists('ldap_connect'))
        {
            $this->WriteLog("Error: PHP LDAP library is not installed", FALSE, FALSE, 299, 'System', '');
            $this->EnableLdapError();
        }
        elseif (('' != $this->GetLdapDomainControllers()) && ('' != $ldap_username) && ('' != $ldap_password))
        {
            $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
            $ldap_options = array('account_suffix'     => $this->GetLdapAccountSuffix(),
                                  'ad_password'        => $ldap_password,
                                  'ad_username'        => $ldap_bind_dn,
                                  'base_dn'            => $this->GetLdapBaseDn(),
                                  'cn_identifier'      => $this->GetLdapCnIdentifier(),
                                  'domain_controllers' => $domain_controllers,
                                  'group_attribute'    => $this->GetLdapGroupAttribute(),
                                  'group_cn_identifier'=> $this->GetLdapGroupCnIdentifier(),
                                  'ldap_server_type'   => $this->GetLdapServerType(),
                                  'network_timeout'    => $this->GetLdapNetworkTimeout(),
                                  'port'               => $this->GetLdapPort(),
                                  'recursive_groups'   => TRUE,
                                  'time_limit'         => $this->GetLdapTimeLimit(),
                                  'use_ssl'            => $this->IsLdapSsl()
                                 );

            $ldap_connection=new MultiotpAdLdap($ldap_options);

            $this->SetLdapServerReachable($ldap_connection->IsServerReachable());

            $result = !$ldap_connection->IsError();

            unset($ldap_connection);
        }
        return $result;
    }


    function GetLdapUsersList($user_filter = "*")
    {
        $this->DisableLdapError();
        $users_list = '';
        $in_groups_array = array();
        $result_array = array();
        
        if (!function_exists('ldap_connect'))
        {
            $result = FALSE;
            $this->WriteLog("Error: PHP LDAP library is not installed", FALSE, FALSE, 299, 'System', '');
            $this->EnableLdapError();
        }
        elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword()))
        {
            $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
            $ldap_options = array('account_suffix'     => $this->GetLdapAccountSuffix(),
                                  'ad_password'        => $this->GetLdapServerPassword(),
                                  'ad_username'        => $this->GetLdapBindDn(),
                                  'base_dn'            => $this->GetLdapBaseDn(),
                                  'cn_identifier'      => $this->GetLdapCnIdentifier(),
                                  'domain_controllers' => $domain_controllers,
                                  'group_attribute'    => $this->GetLdapGroupAttribute(),
                                  'group_cn_identifier'=> $this->GetLdapGroupCnIdentifier(),
                                  'ldap_server_type'   => $this->GetLdapServerType(),
                                  'network_timeout'    => $this->GetLdapNetworkTimeout(),
                                  'port'               => $this->GetLdapPort(),
                                  'recursive_groups'   => TRUE,
                                  'time_limit'         => $this->GetLdapTimeLimit(),
                                  'use_ssl'            => $this->IsLdapSsl()
                                 );

            $ldap_connection=new MultiotpAdLdap($ldap_options);
            if ($users_info = $ldap_connection->users_info($user_filter,
                                                           $this->GetLdapFieldsArray()))
            {
                if ($ldap_connection->IsError())
                {
                    $this->EnableLdapError();
                }
                // We continue only if there is no error
                else
                {
                    $all_results = (isset($users_info['count'])?$users_info['count']:0);
                    for ($results=0; $results < $all_results; $results++)
                    {
                        $accountdisable = FALSE;
                        $one_user = $users_info[$results];
                        $user = decode_utf8_if_needed(isset($one_user[strtolower($this->GetLdapCnIdentifier())][0])?($one_user[strtolower($this->GetLdapCnIdentifier())][0]):'');
                        if (isset($one_user['useraccountcontrol'][0]))
                        {
                            if (0 != ($one_user['useraccountcontrol'][0] & 2))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['ms-ds-user-account-control-computed'][0]))
                        {
                            if (0 != ($one_user['ms-ds-user-account-control-computed'][0] & 16))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['accountexpires'][0]))
                        {
                            if (($one_user['accountexpires'][0] > 0) && ((($one_user['accountexpires'][0] / 10000000) - 11644473600) < time()))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        
                        if (isset($one_user['shadowexpire'][0]))
                        {
                            if (($one_user['shadowexpire'][0] >= 0) && ((86400 * $one_user['shadowexpire'][0]) < time()))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['sambaacctflags'][0]))
                        {
                            if ((FALSE !== strpos($one_user['sambaacctflags'][0], "D")) || (FALSE !== strpos($one_user['sambaacctflags'][0], "L")))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        
                        if (!$accountdisable)
                        {
                            if ('' == trim($this->GetLdapInGroup()))
                            {
                                $in_a_group = TRUE;
                            }
                            else
                            {
                                $in_a_group = FALSE;
                                $in_groups_array_raw = explode(" ",trim(str_replace(","," ",str_replace(";"," ",strtolower($this->GetLdapInGroup())))));
                                foreach($in_groups_array_raw as $one_group)
                                {
                                    $in_groups_array[] = trim($one_group);
                                }
                            }

                            $groups_array_raw = $ldap_connection->user_groups($user);
                            $groups_array = array();
                            foreach($groups_array_raw as $one_group)
                            {
                                $this_group = decode_utf8_if_needed($one_group);
                                $groups_array[] = $this_group;
                                if (in_array(strtolower($this_group), $in_groups_array))
                                {
                                    $in_a_group = TRUE;
                                }
                            }

                            if ($in_a_group)
                            {
                                $users_list.= (('' != $users_list)?"\t":'').$user;
                            }
                        }
                    }
                }
            }
            else
            {
                $this->EnableLdapError();
                $this->WriteLog("Error: no LDAP binding", FALSE, FALSE, 30, 'LDAP', '');
            }
        }
        else
        {
            $this->WriteLog("Error: No LDAP connection information", FALSE, FALSE, 30, 'LDAP', '');
        }
        return $users_list;
    }


    function GetLdapUsersInfoArray($user_filter = "*", $include_disabled = TRUE, $ignore_in_group = FALSE)
    {
        $this->DisableLdapError();
        $in_groups_array = array();
        $result_array = array();

        if (!function_exists('ldap_connect'))
        {
            $result = FALSE;
            $this->WriteLog("Error: PHP LDAP library is not installed", FALSE, FALSE, 299, 'System', '');
            $this->EnableLdapError();
        }
        elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword()))
        {
            $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
            $ldap_options = array('account_suffix'     => $this->GetLdapAccountSuffix(),
                                  'ad_password'        => $this->GetLdapServerPassword(),
                                  'ad_username'        => $this->GetLdapBindDn(),
                                  'base_dn'            => $this->GetLdapBaseDn(),
                                  'cn_identifier'      => $this->GetLdapCnIdentifier(),
                                  'domain_controllers' => $domain_controllers,
                                  'group_attribute'    => $this->GetLdapGroupAttribute(),
                                  'group_cn_identifier'=> $this->GetLdapGroupCnIdentifier(),
                                  'ldap_server_type'   => $this->GetLdapServerType(),
                                  'network_timeout'    => $this->GetLdapNetworkTimeout(),
                                  'port'               => $this->GetLdapPort(),
                                  'recursive_groups'   => TRUE,
                                  'time_limit'         => $this->GetLdapTimeLimit(),
                                  'use_ssl'            => $this->IsLdapSsl()
                                 );

            $ldap_connection=new MultiotpAdLdap($ldap_options);
            if ($users_info = $ldap_connection->users_info($user_filter,
                                                           $this->GetLdapFieldsArray()))
            {
                if ($ldap_connection->IsError())
                {
                    $this->EnableLdapError();
                }
                // We continue only if there is no error
                else
                {
                    // Prepare the array "users_in_groups" if we are using a generic LDAP and an LdapInGroup Filtering
                    if (1 != $this->GetLdapServerType()) // Generic LDAP, eventually no memberOf function like in AD
                    {
                        $users_in_groups = array();
                        if ('' != trim($this->GetLdapInGroup()))
                        {
                            $in_groups_array_raw = explode(" ",trim(str_replace(","," ",str_replace(";"," ",strtolower($this->GetLdapInGroup())))));
                            foreach($in_groups_array_raw as $one_group)
                            {
                                $temp_array = $ldap_connection->group_users($one_group);
                                foreach($temp_array as $one_temp)
                                {
                                    $one_user = decode_utf8_if_needed($one_temp);
                                    if (!isset($users_in_groups[decode_utf8_if_needed($one_user)]))
                                    {
                                        $users_in_groups[$one_user] = $one_group;
                                    }
                                    else
                                    {
                                        $users_in_groups[$one_user] = $users_in_groups[$one_user].",".$one_group;
                                    }
                                }
                            }
                        }
                    }
                
                    $all_results = (isset($users_info['count'])?$users_info['count']:0);
                    for ($results=0; $results < $all_results; $results++)
                    {
                        $accountdisable = FALSE;
                        $one_user = $users_info[$results];
                        $user = decode_utf8_if_needed(isset($one_user[strtolower($this->GetLdapCnIdentifier())][0])?($one_user[strtolower($this->GetLdapCnIdentifier())][0]):'');
                        if (!$this->IsCaseSensitiveUsers())
                        {
                            $user = strtolower($user);
                        }
                        if (isset($one_user['useraccountcontrol'][0]))
                        {
                            if (0 != ($one_user['useraccountcontrol'][0] & 2))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['ms-ds-user-account-control-computed'][0]))
                        {
                            if (0 != ($one_user['ms-ds-user-account-control-computed'][0] & 16))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['accountexpires'][0]))
                        {
                            if (($one_user['accountexpires'][0] > 0) && ((($one_user['accountexpires'][0] / 10000000) - 11644473600) < time()))
                            {
                                $accountdisable = TRUE;
                            }
                        }

                        if (isset($one_user['shadowexpire'][0]))
                        {
                            if (($one_user['shadowexpire'][0] >= 0) && ((86400 * $one_user['shadowexpire'][0]) < time()))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['sambaacctflags'][0]))
                        {
                            if ((FALSE !== strpos($one_user['sambaacctflags'][0], "D")) || (FALSE !== strpos($one_user['sambaacctflags'][0], "L")))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        
                        if ($include_disabled || (!$accountdisable))
                        {
                            if ('' == trim($this->GetLdapInGroup()))
                            {
                                $in_a_group = TRUE;
                            }
                            else
                            {
                                $in_a_group = FALSE;
                                $in_groups_array_raw = explode(" ",trim(str_replace(","," ",str_replace(";"," ",strtolower($this->GetLdapInGroup())))));
                                foreach($in_groups_array_raw as $one_group)
                                {
                                    $in_groups_array[] = trim($one_group);
                                }
                            }

                            if (1 != $this->GetLdapServerType()) // Generic LDAP, eventually no memberOf function like in AD
                            {
                                if (isset($users_in_groups[$user]))
                                $in_a_group = TRUE;
                            }
                            else // AD
                            {
                                // $groups_array_raw = $ldap_connection->user_groups($user);
                                $groups_array_raw=$ldap_connection->nice_names($one_user[$ldap_connection->_group_attribute]); //presuming the entry returned is our guy (unique usernames)

                                if ($ldap_connection->_recursive_groups)
                                {
                                    foreach ($groups_array_raw as $id => $group_name){
                                        $extra_groups=$ldap_connection->recursive_groups($group_name, TRUE); // recursive_groups with cache only
                                        if ('' != $ldap_connection->get_warning_message()) {
                                            $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                                        }
                                        if ($this->GetVerboseFlag() && ('' != $ldap_connection->get_debug_message())) {
                                            $this->WriteLog("Debug: ".$ldap_connection->get_debug_message(), FALSE, FALSE, 98, 'LDAP', '');
                                        }
                                        $groups_array_raw=array_merge($groups_array_raw,$extra_groups);
                                    }
                                }
                                
                                foreach($groups_array_raw as $one_group)
                                {
                                    $this_group = decode_utf8_if_needed($one_group);
                                    $groups_array[] = $this_group;
                                    if (in_array(strtolower($this_group), $in_groups_array))
                                    {
                                        $in_a_group = TRUE;
                                    }
                                }
                            }
                            
                            if ($ignore_in_group || $in_a_group)
                            {
                                $result_array[$user]['user'] = $user;
                                $result_array[$user]['groups'] = $groups_array;
                                $result_array[$user]['accountdisable'] = $accountdisable;
                                $result_array[$user]['mail'] = (isset($one_user['mail'][0])?decode_utf8_if_needed($one_user['mail'][0]):"");
                                $result_array[$user]['displayname'] = (isset($one_user['displayname'][0])?decode_utf8_if_needed($one_user['displayname'][0]):"");
                                $result_array[$user]['mobile'] = (isset($one_user['mobile'][0])?decode_utf8_if_needed($one_user['mobile'][0]):"");
                                $result_array[$user]['msnpallowdialin'] = ("TRUE" == (isset($one_user['msnpallowdialin'][0])?($one_user['msnpallowdialin'][0]):"FALSE"));
                                $result_array[$user]['synchronized_dn'] = (isset($one_user['distinguishedname'][0])?decode_utf8_if_needed($one_user['distinguishedname'][0]):"");
                            }
                        }
                    }
                }
            }
            else
            {
                $this->EnableLdapError();
                $this->WriteLog("Error: LDAP connection failed", FALSE, FALSE, 30, 'LDAP', '');
            }
        }
        else
        {
            $this->EnableLdapError();
            $this->WriteLog("Error: no LDAP connection information", FALSE, FALSE, 30, 'LDAP', '');
        }
        return $result_array;
    }


    function TestLdapUser($value)
    {
        $result = FALSE;
        $user_to_check = ($this->IsCaseSensitiveUsers()?$value:strtolower($value));
        $ldap_users_array = $this->GetLdapUsersInfoArray();
        if (!$this->IsLdapError())
        {
            foreach($ldap_users_array as $one_ldap_user)
            {
                $user = $one_ldap_user['user'];
                $user = ($this->IsCaseSensitiveUsers()?$user:strtolower($user));
                if ($user_to_check == $user)
                {
                    $result = TRUE;
                    break;
                }
            }
        }
        return $result;
    }


    /**
     * @brief   Synchronize AD/LDAP users
     *
     * @param   string  $user_filter           User name filter (* by default)
     * @param   boolean $include_disabled      Disabled users will also be synced
     * @param   boolean $ignore_in_group       Don't check if the users are in the selected groups or not
     *
     * @return  boolean                        Function has been successfully called
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.3.2.0
     * @date    2015-02-18
     * @since   2014-11-04 (completely redesigned)
     */
    function SyncLdapUsers(
        $user_filter = "*",
        $include_disabled = TRUE,
        $ignore_in_group = FALSE
    ) {
        $start_sync_time = time();

        $this->DisableLdapError();
        $in_groups_array = array();
        $result = FALSE;

        if (!function_exists('ldap_connect'))
        {
            $this->WriteLog("Error: PHP LDAP library is not installed", FALSE, FALSE, 299, 'System', '');
            $this->EnableLdapError();
        }
        // TODO: later, we could loop in several base-dn (semicolon separated)
        elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword()))
        {
            $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
            $ldap_options = array('account_suffix'     => $this->GetLdapAccountSuffix(),
                                  'ad_password'        => $this->GetLdapServerPassword(),
                                  'ad_username'        => $this->GetLdapBindDn(),
                                  'base_dn'            => $this->GetLdapBaseDn(),
                                  'cn_identifier'      => $this->GetLdapCnIdentifier(),
                                  'domain_controllers' => $domain_controllers,
                                  'group_attribute'    => $this->GetLdapGroupAttribute(),
                                  'group_cn_identifier'=> $this->GetLdapGroupCnIdentifier(),
                                  'ldap_server_type'   => $this->GetLdapServerType(),
                                  'network_timeout'    => $this->GetLdapNetworkTimeout(),
                                  'port'               => $this->GetLdapPort(),
                                  'recursive_groups'   => TRUE,
                                  'time_limit'         => $this->GetLdapTimeLimit(),
                                  'use_ssl'            => $this->IsLdapSsl()
                                 );

            $ldap_connection = new MultiotpAdLdap($ldap_options);
            
            if ($ldap_connection->IsError())
            {
                $this->EnableLdapError();
            }
            // We continue only if there is no error
            else
            {
                // Put all group_cn in cache
                $ldap_connection->group_cn(1, FALSE, TRUE);

                // Put all recursive_groups in cache
                $all_groups = $ldap_connection->all_groups(FALSE, '*', TRUE, TRUE);
                reset($all_groups);
                while(list($key, $one_group) = each($all_groups))
                {
                    $ldap_connection->recursive_groups($one_group);
                }

                $ldap_created_counter = 0;
                $modified_counter = 0;
                $result = TRUE;

                $page_cookie = '';


                // Prepare the array "users_in_groups" if we are using a generic LDAP and an LdapInGroup Filtering
                if (1 != $this->GetLdapServerType()) // Generic LDAP, eventually no memberOf function like in AD
                {
                    $users_in_groups = array();
                    if ('' != trim($this->GetLdapInGroup()))
                    {
                        $in_groups_array_raw = explode(" ",trim(str_replace(","," ",str_replace(";"," ",strtolower($this->GetLdapInGroup())))));
                        foreach($in_groups_array_raw as $one_group)
                        {
                            $temp_array = $ldap_connection->group_users($one_group);
                            foreach($temp_array as $one_temp)
                            {
                                $one_user = decode_utf8_if_needed($one_temp);
                                if (!isset($users_in_groups[decode_utf8_if_needed($one_user)]))
                                {
                                    $users_in_groups[$one_user] = $one_group;
                                }
                                else
                                {
                                    $users_in_groups[$one_user] = $users_in_groups[$one_user].",".$one_group;
                                }
                            }
                        }
                    }
                }

                do
                { // ldap pagination loop
                    if (function_exists('ldap_control_paged_result'))
                    {
                        ldap_control_paged_result($ldap_connection->_conn, 1000, true, $page_cookie); // Page size of 1000
                    }
                    $one_user = $ldap_connection->one_user_info(TRUE,
                                                                $user_filter,
                                                                $this->GetLdapFieldsArray(),
                                                                TRUE // $group_cn_cache_only = TRUE
                                                               );
                    if ($ldap_connection->IsError())
                    {
                        $this->EnableLdapError();
                        $this->WriteLog("Error: LDAP connection failed", FALSE, FALSE, 30, 'LDAP', '');
                        return FALSE;
                    }
                    if ('' != $ldap_connection->get_warning_message()) {
                        $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                    }
                    if ($this->GetVerboseFlag() && ('' != $ldap_connection->get_debug_message())) {
                        $this->WriteLog("Debug: ".$ldap_connection->get_debug_message(), FALSE, FALSE, 98, 'LDAP', '');
                    }

                    do
                    {
                        $accountdisable = FALSE;
                        $groups_array = array();
                        $user = decode_utf8_if_needed(isset($one_user[strtolower($this->GetLdapCnIdentifier())][0])?($one_user[strtolower($this->GetLdapCnIdentifier())][0]):'');
                        if (!$this->IsCaseSensitiveUsers())
                        {
                            $user = strtolower($user);
                        }
                        if (isset($one_user['useraccountcontrol'][0]))
                        {
                            if (0 != ($one_user['useraccountcontrol'][0] & 2))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['ms-ds-user-account-control-computed'][0]))
                        {
                            if (0 != ($one_user['ms-ds-user-account-control-computed'][0] & 16))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['accountexpires'][0]))
                        {
                            if (($one_user['accountexpires'][0] > 0) && ((($one_user['accountexpires'][0] / 10000000) - 11644473600) < time()))
                            {
                                $accountdisable = TRUE;
                            }
                        }

                        if (isset($one_user['shadowexpire'][0]))
                        {
                            if (($one_user['shadowexpire'][0] >= 0) && ((86400 * $one_user['shadowexpire'][0]) < time()))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if (isset($one_user['sambaacctflags'][0]))
                        {
                            if ((FALSE !== strpos($one_user['sambaacctflags'][0], "D")) || (FALSE !== strpos($one_user['sambaacctflags'][0], "L")))
                            {
                                $accountdisable = TRUE;
                            }
                        }
                        if ($include_disabled || (!$accountdisable))
                        {
                            if ('' == trim($this->GetLdapInGroup()))
                            {
                                $in_a_group = TRUE;
                            }
                            else
                            {
                                $in_a_group = FALSE;
                                $in_groups_array_raw = explode(" ",trim(str_replace(","," ",str_replace(";"," ",strtolower($this->GetLdapInGroup())))));
                                foreach($in_groups_array_raw as $one_group)
                                {
                                    $in_groups_array[] = trim($one_group);
                                }
                            }

                            if (1 != $this->GetLdapServerType()) // Generic LDAP, eventually no memberOf function like in AD
                            {
                                if (isset($users_in_groups[$user]))
                                $in_a_group = TRUE;
                            }
                            else // AD
                            {
                                // $groups_array_raw = $ldap_connection->user_groups($user);
                                $groups_array_raw=$ldap_connection->nice_names($one_user[$ldap_connection->_group_attribute]); //presuming the entry returned is our guy (unique usernames)

                                if ($ldap_connection->_recursive_groups)
                                {
                                    foreach ($groups_array_raw as $id => $group_name){
                                        $extra_groups=$ldap_connection->recursive_groups($group_name, TRUE); // recursive_groups with cache only
                                        if ('' != $ldap_connection->get_warning_message())
                                        {
                                            $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                                        }
                                        $groups_array_raw=array_merge($groups_array_raw,$extra_groups);
                                    }
                                }
                                
                                foreach($groups_array_raw as $one_group)
                                {
                                    $this_group = decode_utf8_if_needed($one_group);
                                    $groups_array[] = $this_group;
                                    if (in_array(strtolower($this_group), $in_groups_array))
                                    {
                                        $in_a_group = TRUE;
                                    }
                                }
                            }

                            if ($ignore_in_group || $in_a_group)
                            {
                                $description = '';
                                if (isset($one_user['description'][0]))
                                {
                                    $description = trim($one_user['description'][0]);
                                }
                                if (('' == $description) && (isset($one_user['gecos'][0])))
                                {
                                    $description = trim($one_user['gecos'][0]);
                                }
                                if (('' == $description) && (isset($one_user['displayname'][0])))
                                {
                                    $description = trim($one_user['displayname'][0]);
                                }
                                if ('' == $description)
                                {
                                    $description = $user;
                                }

                                // $user;
                                $ldap_groups = $groups_array;
                                $ldap_email = trim(isset($one_user['mail'][0])?decode_utf8_if_needed($one_user['mail'][0]):"");
                                $ldap_description = decode_utf8_if_needed($description);
                                $ldap_sms = (isset($one_user['mobile'][0])?decode_utf8_if_needed($one_user['mobile'][0]):"");
                                $ldap_msnpallowdialin = ("TRUE" == (isset($one_user['msnpallowdialin'][0])?($one_user['msnpallowdialin'][0]):"FALSE"));
                                $ldap_enabled = ((!$accountdisable)?1:0);
                                $ldap_synchronized_dn = trim(isset($one_user['distinguishedname'][0])?decode_utf8_if_needed($one_user['distinguishedname'][0]):"");
                                
                                if (!$this->CheckUserExists($user, true, true)) // $no_server_check = TRUE; $no_error = TRUE
                                // User doesn't exist yet
                                {
                                    if ('' == $ldap_description)
                                    {
                                        $ldap_description = $user;
                                    }
                                    $result = $this->FastCreateUser($user,
                                                                    $ldap_email,
                                                                    $ldap_sms,
                                                                    -1, // Prefix pin needed
                                                                    "totp",
                                                                    $ldap_enabled,
                                                                    $ldap_description,
                                                                    $this->GetDefaultUserGroup(), // Group
                                                                    1,  // Synchronized
                                                                    '', // Pin
                                                                    true, // Automatically
                                                                    'LDAP', // Synchronized channel
                                                                    $this->GetLdapDomainControllers(), // Synchronized server
                                                                    $ldap_synchronized_dn,
                                                                    -1 // Set to default value if the user is created  automatically
                                                                   );
                                    if ($result) {
                                        $this->SyncUserModified(true, $user);
                                        $ldap_created_counter++;
                                    }
                                }
                                else
                                // User already exists
                                {
                                    $this->SetUser($user);
                                    if (1 == $this->GetUserSynchronized())
                                    {
                                        $description = $this->GetUserDescription();
                                        $email = $this->GetUserEmail();
                                        $enabled = $this->GetUserActivated();
                                        $sms = $this->GetUserSms();
                                        $synchronized_channel = $this->GetUserSynchronizedChannel();
                                        $synchronized_dn = $this->GetUserSynchronizedDn();
                                        $synchronized_server = $this->GetUserSynchronizedServer();
                                        $modified = FALSE;

                                        if (('' != $ldap_description) && ($description != $ldap_description))
                                        {
                                            $this->SetUserDescription($ldap_description);
                                            $modified = TRUE;
                                        }
                                        
                                        if (('' != $ldap_email) && ($email != $ldap_email))
                                        {
                                            $this->SetUserEmail($ldap_email);
                                            $modified = TRUE;
                                        }

                                        if ($enabled != $ldap_enabled)
                                        {
                                            $this->SetUserActivated($ldap_enabled);
                                            $modified = TRUE;
                                        }

                                        if (('' != $ldap_sms) && ($sms != $ldap_sms))
                                        {
                                            $this->SetUserSms($ldap_sms);
                                            $modified = TRUE;
                                        }

                                        if ($synchronized_channel != 'LDAP')
                                        {
                                            $this->SetUserSynchronizedChannel('LDAP');
                                            $modified = TRUE;
                                        }

                                        if ($synchronized_dn != $ldap_synchronized_dn)
                                        {
                                            $this->SetUserSynchronizedDn($ldap_synchronized_dn);
                                            $modified = TRUE;
                                        }

                                        if (('' != $this->GetLdapDomainControllers()) && ($synchronized_server != $this->GetLdapDomainControllers()))
                                        {
                                            $this->SetUserSynchronizedServer($this->GetLdapDomainControllers());
                                            $modified = TRUE;
                                        }

                                        // We set to the default value for LDAP password if the user is updated by synchronization
                                        $this->SetUserRequestLdapPassword($this->GetDefaultRequestLdapPwd());

                                        $this->SetUserSynchronizedTime();
                                        
                                        $this->WriteUserData(TRUE, $modified); // $automatically = TRUE, $update_last_change = $modified
                                        if ($modified) {
                                            $this->SyncUserModified(false, $user);
                                            $modified_counter++;
                                        }
                                    }
                                }
                            }
                        }
                    } // Loop of LDAP parsing and synchronization
                    while ($one_user = $ldap_connection->one_user_info(FALSE, NULL, NULL, TRUE)); // $group_cn_cache_only = TRUE

                    if (function_exists('ldap_control_paged_result_response'))
                    {
                        ldap_control_paged_result_response($ldap_connection->_conn, $ldap_connection->_oui_sr, $page_cookie);
                    }
                } // ldap pagination loop
                while (($page_cookie !== null) && ($page_cookie != ''));

                if (function_exists('ldap_control_paged_result'))
                {
                    // Reset LDAP paged result
                    ldap_control_paged_result($ldap_connection->_conn, 1000);
                }

                // Loop on all existing users to disable the "not-synchronized-yet" synchronized users
                $one_user = $this->GetNextUserArray(TRUE);
                do
                {
                    if (isset($one_user['user']))
                    {
                        $modified_user = $one_user['user'];
                        $modified_description = $one_user['description'];
                        $modified_email = $one_user['email'];
                        $modified_enabled = $one_user['enabled'];
                        $modified_locked = $one_user['locked'];
                        $modified_sms = $one_user['sms'];
                        $modified_synchronized = $one_user['synchronized'];
                        $modified_synchronized_channel = $one_user['synchronized_channel'];
                        $modified_synchronized_dn = $one_user['synchronized_dn'];
                        $modified_synchronized_server = $one_user['synchronized_server'];
                        $modified_synchronized_time = $one_user['synchronized_time'];
                        $modified_token_serial = $one_user['token'];
                        if (($modified_synchronized) && ($modified_synchronized_time < $start_sync_time))
                        // The existing user is enabled and marked as synchronized but is not in the external database/LDAP
                        {
                            if ($modified_enabled)
                            {
                                $this->SetUser($modified_user);
                                $this->SetUserActivated(0);
                                $this->WriteUserData(TRUE);
                                $modified_counter++;
                            }
                        }
                    }
                }
                while ($one_user = $this->GetNextUserArray());
                

                $time_info = gmdate("H:i:s", time()-$start_sync_time);
                $info_txt = '';
                
                if ($modified_counter > 0)
                {
                    $ldap_counter_suffix = ((1 < $modified_counter)?'s':'');
                    $info_txt.= $modified_counter." user$ldap_counter_suffix updated";
                }
                if ($ldap_created_counter > 0)
                {
                    if ('' != $info_txt)
                    {
                        $info_txt.= ', ';
                    }
                    $ldap_counter_suffix = ((1 < $ldap_created_counter)?'s':'');
                    $info_txt.= $ldap_created_counter." user$ldap_counter_suffix created";
                }
                if ('' == $info_txt)
                {
                    $info_txt = 'No modification about LDAP users';
                }
                $this->WriteLog("Info: $info_txt (processed in $time_info)", FALSE, FALSE, 19, 'LDAP', '');
            } // We have done this loop only if there was no error before
        } // End of successful LDAP parameters
        else
        {
            $this->EnableLdapError();
            $this->WriteLog("Error: no LDAP connection information", FALSE, FALSE, 30, 'LDAP', '');
        }
        return $result;
    }


    // It's possible to overload this stub in order to do something when the current user is modified (or created)
    function SyncUserModified($created = false, $user = '') {
        return true;
    }


    function CheckLdapAuthentication()
    /**
     * @brief   Write information in the log file/database, to the syslog server and on the screen
     *
     * @retval  boolean   Result of the test
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.2.4.2
     * @date    2014-04-13
     * @since   2014-02-07
     */
    {
        $result = FALSE;
        if (!function_exists('ldap_connect'))
        {
            $this->WriteLog("Error: PHP LDAP library is not installed", FALSE, FALSE, 299, 'System', '');
        }
        elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword()))
        {
            $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
            $ldap_options = array('account_suffix'     => $this->GetLdapAccountSuffix(),
                                  'ad_password'        => $this->GetLdapServerPassword(),
                                  'ad_username'        => $this->GetLdapBindDn(),
                                  'base_dn'            => $this->GetLdapBaseDn(),
                                  'cn_identifier'      => $this->GetLdapCnIdentifier(),
                                  'domain_controllers' => $domain_controllers,
                                  'group_attribute'    => $this->GetLdapGroupAttribute(),
                                  'group_cn_identifier'=> $this->GetLdapGroupCnIdentifier(),
                                  'ldap_server_type'   => $this->GetLdapServerType(),
                                  'network_timeout'    => $this->GetLdapNetworkTimeout(),
                                  'port'               => $this->GetLdapPort(),
                                  'recursive_groups'   => TRUE,
                                  'time_limit'         => $this->GetLdapTimeLimit(),
                                  'use_ssl'            => $this->IsLdapSsl()
                                 );

            define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
            
            $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
            mt_srand(doubleval(microtime()) * 100000000); // for older php versions
            $domain_controller = ($domain_controllers[array_rand($domain_controllers)]);
            
            
            foreach($domain_controllers as $dc)
            {
                $port = $this->GetLdapPort();
                $controller = $dc;
                $protocol = "ldap://";
                if ($this->IsLdapSsl())
                {
                    $protocol = "ldaps://";
                }
                $pos = strpos($dc, "://");
                if ($pos !== FALSE)
                {
                    $protocol = substr($dc, 0, $pos+3);
                    $dc = substr($dc, $pos+3);
                }
                $pos = strpos($dc, ":");
                if ($pos !== FALSE)
                {
                    $port = substr($dc, $pos+1);
                    $dc = substr($dc, 0, $pos);
                }

                /* DEBUG
                echo "DEBUG PROTOCOL: ".$protocol.$dc.":".$port."\n";
                if ($this->GetVerboseFlag())
                {
                    ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
                }
                */
                
                if ($ldapconn = @ldap_connect($protocol.$dc.":".$port))
                {
                    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
                    if (@ldap_bind($ldapconn, ($this->GetLdapBindDn().$this->GetLdapAccountSuffix()), ($this->GetLdapServerPassword())))
                    {
                        /*
                        echo "DEBUG\n";
                        if (ldap_get_option($ldapconn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error))
                        {
                            echo "Error Binding to LDAP: $extended_error";
                        }
                        else
                        {
                            echo "Error Binding to LDAP: No additional information is available.";
                        }
                        */
                        $result = TRUE;
                    }
                    else
                    {
                        // echo "DEBUG LDAP: ".ldap_error($ldapconn);
                    }
                    @ldap_unbind($ldapconn);
                }
                if ($result)
                {
                    break;
                }
            }
        }
        return $result;
    }


    function SetTokenDataReadFlag($flag)
    {
        $this->_token_data_read_flag = $flag;
    }


    function GetTokenDataReadFlag()
    {
        return $this->_token_data_read_flag;
    }


    function SetBaseDir($base_dir)
    {
        $this->_base_dir = $this->ConvertToUnixPath($base_dir);
    }


    function GetBaseDir()
    {
        return ($this->_base_dir);
    }


    function GetScriptFolder()
    /**
     * @brief   Get the folder of this script.
     *
     * @retval  string  Full path to the script folder.
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.0.7
     * @date    2013-08-28
     * @since   2010-06-07
     */
    {
        if ('' != $this->GetBaseDir())
        {
            $current_script_folder_detected = $this->ConvertToUnixPath($this->GetBaseDir());
        }
        else
        {
            $current_script_folder_detected = $this->ConvertToUnixPath(dirname(__FILE__));
        }

        if (substr($current_script_folder_detected,-1) != "/")
        {
            $current_script_folder_detected.="/";
        }
        return $this->ConvertToWindowsPathIfNeeded($current_script_folder_detected);
    }


    function ConvertToUnixPath($path)
    {
        return str_replace("\\","/",$path);
    }


    function ConvertToWindowsPathIfNeeded($path)
    {
        $result = $path;
        if (FALSE !== strpos($result,":"))
        {
            $result = str_replace("/","\\",$result);
        }
        return $result;
    }


    function GetReplyMessageForRadius()
    {
        return (isset($this->_reply_array_for_radius[0])?$this->_reply_array_for_radius[0]:'');
    }


    function SetReplyMessageForRadius($value)
    {
        $this->_reply_array_for_radius = array();
        $this->AddReplyArrayMessageForRadius($value);
    }


    function GetReplyArrayForRadius()
    {
        return $this->_reply_array_for_radius;
    }


    function AddReplyArrayForRadius($value)
    {
        $this->_reply_array_for_radius[] = $value;
    }


    // Adding extra information for the result (if any)
    Function AddExtraRadiusInfo()
    {
        $group = trim($this->GetUserGroup());
        if (('' != $group) && ('' != $this->GetGroupAttribute()))
        {
            $this->AddReplyArrayForRadius($this->GetGroupAttribute().$this->GetRadiusReplyAttributor().'"'.$group.'"');
        }
        if (('' != $this->GetLastClearOtpValue()) && ('' != $this->GetClearOtpAttribute()))
        {
            $this->AddReplyArrayForRadius($this->GetClearOtpAttribute().$this->GetRadiusReplyAttributor().'"'.$this->GetLastClearOtpValue().'"');
        }
    }


    function SetVerboseLogPrefix($value)
    {
        $this->_config_data['verbose_log_prefix'] = $value;
    }


    function GetVerboseLogPrefix()
    {
        return $this->_config_data['verbose_log_prefix'];
    }


    function SetAttributesToEncrypt($attributes_to_encrypt)
    {
        $attributes = trim($attributes_to_encrypt);
        if (('' != $attributes) && ('*' == substr($attributes,0,1)) && ('*' == substr($attributes,-1)))
        {
            $this->_attributes_to_encrypt = $attributes;
        }
    }


    function GetAttributesToEncrypt()
    {
        return $this->_attributes_to_encrypt;
    }



    function SetUsersFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_users_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: Unable to create the missing users folder ".$new_folder, FALSE, FALSE, 28, 'System', '');
            }
        }
    }


    function GetUsersFolder()
    {
        if ('' == $this->_users_folder)
        {
            $this->SetUsersFolder($this->GetScriptFolder()."users/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_users_folder);
    }


    function SetDevicesFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_devices_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: Unable to create the missing devices folder ".$new_folder, FALSE, FALSE, 28, 'System', '');
            }
        }
    }


    function GetDevicesFolder()
    {
        if ('' == $this->_devices_folder)
        {
            $this->SetDevicesFolder($this->GetScriptFolder()."devices/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_devices_folder);
    }


    function SetQrCodeFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_qrcode_folder = $new_folder;
    }


    function GetQrCodeFolder()
    {
        if ('' == $this->_qrcode_folder)
        {
            $this->SetQrCodeFolder($this->GetScriptFolder()."qrcode/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_qrcode_folder);
    }


    function SetTemplatesFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_templates_folder = $new_folder;
    }


    function GetTemplatesFolder()
    {
        if ('' == $this->_templates_folder)
        {
            $this->SetTemplatesFolder($this->GetScriptFolder()."templates/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_templates_folder);
    }


    function SetGroupsFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_groups_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: Unable to create the missing groups folder ".$new_folder, FALSE, FALSE, 28, 'System', '');
            }
        }
    }


    function GetGroupsFolder()
    {
        if ('' == $this->_groups_folder)
        {
            $this->SetGroupsFolder($this->GetScriptFolder()."groups/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_groups_folder);
    }


    function SendSms($sms_recipient, $sms_message_to_send, $real_user = '', $originator = '', $provider = '', $userkey = '', $password = '', $api_id = '', $write_log = TRUE)
    {
        $sms_number = $this->CleanPhoneNumber($sms_recipient);

        $result = 62;
        
        $sms_originator = (('' != $originator)?$originator:$this->GetSmsOriginator());
        $sms_provider = strtolower((('' != $provider)?$provider:$this->GetSmsProvider()));
        $sms_userkey = (('' != $userkey)?$userkey:$this->GetSmsUserkey());
        $sms_password = (('' != $password)?$password:$this->GetSmsPassword());
        $sms_api_id = (('' != $api_id)?$api_id:$this->GetSmsApiId());
       
        if ("aspsms" == $sms_provider)
        {
            $sms_message = new MultiotpAspSms($sms_userkey, $sms_password);
            $sms_message->setOriginator($sms_originator);
            $sms_message->setRecipient($sms_number);
            $sms_message->setContent(decode_utf8_if_needed($sms_message_to_send));
            $sms_result = intval($sms_message->sendSMS());
            
            if (1 != $sms_result)
            {
                $result = 61; // ERROR: SMS code request received, but an error occurred during transmission
                if ($write_log)
                {
                    $this->WriteLog("Error: SMS code request received for ".$real_user.", but the ".$sms_provider." error ".$sms_result." occurred during transmission to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
                }
            }
            else
            {
                $result = 18; // INFO: SMS code request received
                if ($write_log)
                {
                    $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$sms_provider." to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
                }
            }
        }
        elseif ("clickatell" == $sms_provider)
        {
            $sms_message = new MultiotpClickatell($sms_userkey, $sms_password, $sms_api_id);
            $sms_message->useRegularServer();
            $sms_message->setOriginator($sms_originator);
            $sms_message->setRecipient($sms_number);
            $sms_message->setContent(encode_utf8_if_needed($sms_message_to_send));
            $sms_result = intval($sms_message->sendSMS());
            
            if (1 != $sms_result)
            {
                $result = 61; // ERROR: SMS code request received, but an error occurred during transmission
                if ($write_log)
                {
                    $this->WriteLog("Error: SMS code request received for ".$real_user.", but the ".$sms_provider." error ".$sms_result." occurred during transmission to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
                }
            }
            else
            {
                $result = 18; // INFO: SMS code request received
                if ($write_log)
                {
                    $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$sms_provider." to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
                }
            }
        }
        elseif ("intellisms" == $sms_provider)
        {
            $sms_message = new MultiotpIntelliSms($sms_userkey, $sms_password);
            $sms_message->useRegularServer();
            $sms_message->setOriginator($sms_originator);
            $sms_message->setRecipient($sms_number);
            $sms_message->setContent(encode_utf8_if_needed($sms_message_to_send));
            $sms_result = $sms_message->sendSMS();
            
            if ("ID" != substr($sms_result,0,2))
            {
                $result = 61; // ERROR: SMS code request received, but an error occurred during transmission
                if ($write_log)
                {
                    $this->WriteLog("Error: SMS code request received for ".$real_user.", but the ".$sms_provider." error ".$sms_result." occurred during transmission to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
                }
            }
            else
            {
                $result = 18; // INFO: SMS code request received
                if ($write_log)
                {
                    $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$sms_provider." to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
                }
            }
        }
        elseif ("exec" == $sms_provider)
        {
            $exec_cmd = $sms_api_id;
            $exec_cmd = str_replace('%from', $sms_originator, $exec_cmd);
            $exec_cmd = str_replace('%to',  $sms_number,  $exec_cmd);
            $exec_cmd = str_replace('%msg',  encode_utf8_if_needed($sms_message_to_send),  $exec_cmd);
            exec($exec_cmd, $output);
            $result = 18; // INFO: SMS code request received
            if ($write_log)
            {
                $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$exec_cmd, FALSE, FALSE, $result, 'SMS', $real_user);
            }
        }
        else
        {
            $result = 62; // ERROR: SMS provider not supported
            if ($write_log)
            {
                $this->WriteLog("Error: SMS provider ".$sms_provider." not supported", FALSE, FALSE, $result, 'SMS', $real_user);
            }
        }
        return $result;
    }


    function GenerateSmsToken($user = '')
    {
        $now_epoch = time();
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        else
        {
            $user = $this->GetUser();
        }
        $sms_number = $this->CleanPhoneNumber($this->GetUserSms());
        if ('' != $sms_number)
        {
            $sms_message_prefix = trim($this->GetSmsMessage());
            $sms_now_steps = $now_epoch;
            $sms_digits = $this->GetSmsDigits();
            $sms_seed_bin = hex2bin(md5('sMs'.$this->GetEncryptionKey().$this->GetUserTokenSeed().$user.$now_epoch));
            $sms_token = $this->GenerateOathHotp($sms_seed_bin,$sms_now_steps,$sms_digits);
            $this->SetUserSmsOtp($sms_token);
            $this->SetUserSmsValidity($now_epoch + $this->GetSmsTimeout());

            $sms_nice_token = $this->ConvertToNiceToken($sms_token);
            
            if (FALSE !== strpos($sms_message_prefix, '%s'))
            {
                $sms_message_to_send = sprintf($sms_message_prefix, $sms_nice_token);
            }
            else
            {
                $sms_message_to_send = $sms_message_prefix.' '.$sms_nice_token;
            }

            $result = $this->SendSms($sms_number, $sms_message_to_send, $user);
        }
        else
        {
            $result = 60; // ERROR: no information on where to send SMS code
            $this->WriteLog("Error: no information on where to send SMS code for ".$real_user, FALSE, FALSE, $result, 'SMS', $real_user);
        }
        $this->WriteUserData();
        return $result;
    }


    function ConvertToNiceToken($regular_token)
    {
        $token_length = strlen($regular_token);
        if (9 <= $token_length)
        {
            $sms_nice_token = substr($regular_token,0,3).'-'.substr($regular_token,3,3).'-'.substr($regular_token,6,($token_length-6));
        }
        elseif (6 < $token_length)
        {
            $sms_nice_token = substr($regular_token,0,intval($token_length/2)).'-'.substr($regular_token,intval($token_length/2),$token_length);
        }
        else
        {
            $sms_nice_token = $regular_token;
        }
        return $sms_nice_token;
    }


    /**
     * @brief   Resync the token of a user and return true or false
     *
     * @param   string  $user                  User to check
     * @param   string  $input                 Token to check
     * @param   string  $input_sync            Second token to check for resync
     * @param   string  $display_status        Display the status bar
     * @param   string  $ignore_lock           Ignore the fact that the user is locked
     * @param   string  $resync_enc_pass       Resynchronization with an encrypted password
     * @param   string  $no_server_check       Ignore any server(s) (if any)  to do the check
     * @param   string  $self_register_serial  Serial number of the self registered hardware token
     *                                          (if any, and not combined as a prefix of the input)
     * @param   string  $hardware_tokens_list  Comma separated list of hardware tokens also attributed
     * @return  boolean                        Resync was successful or not
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.3.0.0
     * @date    2014-11-04
     * @since   2014-10-17
     */
    function ResyncUserToken($user = '',
                             $input = '',
                             $input_sync = '',
                             $display_status = FALSE,
                             $ignore_lock = FALSE,
                             $resync_enc_pass = FALSE,
                             $no_server_check = FALSE,
                             $self_register_serial = '',
                             $hardware_tokens_list = '')
    {
        $the_hardware_tokens_list = $hardware_tokens_list;
        if ('' != $user)
        {
            $this->SetUser($user);
            $the_hardware_tokens_list = $this->GetUserTokenSerialNumber();
        }
        $result = $this->ResyncToken($input,
                                     $input_sync,
                                     $display_status,
                                     $ignore_lock,
                                     $resync_enc_pass,
                                     $no_server_check,
                                     $self_register_serial,
                                     $the_hardware_tokens_list);
        return $result;
    }


    /**
     * @brief   Resync the token of the current user and return true or false
     *
     * @param   string  $input                 Token to check
     * @param   string  $input_sync            Second token to check for resync
     * @param   string  $display_status        Display the status bar
     * @param   string  $ignore_lock           Ignore the fact that the user is locked
     * @param   string  $resync_enc_pass       Resynchronization with an encrypted password
     * @param   string  $no_server_check       Ignore any server(s) (if any)  to do the check
     * @param   string  $self_register_serial  Serial number of the self registered hardware token
     *                                          (if any, and not combined as a prefix of the input)
     * @param   string  $hardware_tokens_list  Comma separated list of hardware tokens also attributed
     * @return  boolean                        Resync was successful or not
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.3.0.0
     * @date    2014-11-04
     * @since   2014-10-17
     */
    function ResyncToken($input = '',
                         $input_sync = '',
                         $display_status = FALSE,
                         $ignore_lock = FALSE,
                         $resync_enc_pass = FALSE,
                         $no_server_check = FALSE,
                         $self_register_serial = '',
                         $hardware_tokens_list = '')
    {
        $result = $this->CheckToken($input,
                                    $input_sync,
                                    $display_status,
                                    $ignore_lock,
                                    $resync_enc_pass,
                                    $no_server_check,
                                    $self_register_serial,
                                    $hardware_tokens_list);

        // Both resynchronization and authentication are TRUE
        return (($result == 14) || ($result == 0));
    }


    /**
     * @brief   Check the token of a user and give the result, with resync options.
     *
     * @param   string  $user                  User to check
     * @param   string  $input                 Token to check
     * @param   string  $input_sync            Second token to check for resync
     * @param   string  $display_status        Display the status bar
     * @param   string  $ignore_lock           Ignore the fact that the user is locked
     * @param   string  $resync_enc_pass       Resynchronization with an encrypted password
     * @param   string  $no_server_check       Ignore any server(s) (if any)  to do the check
     * @param   string  $self_register_serial  Serial number of the self registered hardware token
     *                                          (if any, and not combined as a prefix of the input)
     * @param   string  $hardware_tokens_list  Comma separated list of hardware tokens also attributed
     * @return  int                            Error code (0: successful authentication, 1n: info, >=20: error)
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.1.1
     * @date    2014-01-15
     * @since   2010-08-12
     */
    function CheckUserToken($user = '',
                            $input = '',
                            $input_sync = '',
                            $display_status = FALSE,
                            $ignore_lock = FALSE,
                            $resync_enc_pass = FALSE,
                            $no_server_check = FALSE,
                            $self_register_serial = '',
                            $hardware_tokens_list = '')
    {
        $the_hardware_tokens_list = $hardware_tokens_list;
        if ('' != $user)
        {
            $this->SetUser($user);
            $the_hardware_tokens_list = $this->GetUserTokenSerialNumber();
        }
        return $this->CheckToken($input,
                                 $input_sync,
                                 $display_status,
                                 $ignore_lock,
                                 $resync_enc_pass,
                                 $no_server_check,
                                 $self_register_serial,
                                 $the_hardware_tokens_list);
    }


    /**
     * @brief   Check the token of the actual user and give the result, with resync options.
     *
     * @param   string  $input                 Token to check
     * @param   string  $input_sync_param      Second token to check for resync
     * @param   string  $display_status        Display the status bar
     * @param   string  $ignore_lock           Ignore the fact that the user is locked
     * @param   string  $resync_enc_pass       Resynchronization with an encrypted password
     * @param   string  $no_server_check       Ignore any server(s) (if any)  to do the check
     * @param   string  $self_register_serial  Serial number of the self registered hardware token
     *                                          (if any, and not combined as a prefix of the input)
     * @param   string  $hardware_tokens_list  Comma separated list of hardware tokens also attributed
     *
     * @return  int                            Error code (0: successful authentication, 1n: info, >=20: error)
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
     * @version 4.3.2.2
     * @date    2015-06-09
     * @since   2010-06-07
     */
    function CheckToken(
        $input = '',
        $input_sync_param = '',
        $display_status = FALSE,
        $ignore_lock = FALSE,
        $resync_enc_pass = FALSE,
        $no_server_check = FALSE,
        $self_register_serial = '',
        $hardware_tokens_list = ''
        ) {

        $this->SetLastClearOtpValue();
        $calculated_token = '';

        $input_sync = $input_sync_param;
        
        // 4.3.2.2
        // As external passwords are now supported,
        // we cannot trim or remove the minus anymore.
        // We disabled trim(str_replace('-','',$input))
        $input_to_check = $input;
        $real_user = $this->GetUser();
        // We don't accept any input without at least 3 characters (like 'sms')
        if (strlen($input_to_check) < 3) {
            $input_to_check = "! <3 digits";
        }
        
        $server_result = -1;
        // Check on the external server(s) first
        if ((!$no_server_check) && ('' != $this->GetServerUrl())) {
            // For multi-account definition, we are also looking on the server(s) if any
            if ($this->ReadUserData($real_user)) {
                // multi account works only if authentication is done with PAP
                if (1 == intval($this->GetUserMultiAccount())) {
                    $pos = strrpos($input_to_check, " ");
                    if ($pos !== FALSE) {
                        $real_user = substr($input_to_check,0,$pos);
                        $input_to_check = trim(substr($input_to_check,$pos+1));
                        if (strlen($input_to_check) < 3) {
                            $input_to_check = "! <3 digits";
                        }
                    }
                }
            }
        
            if ('' != $this->GetChapPassword()) {
                if (32 < strlen($this->GetChapPassword())) {
                    $hex_id = substr($this->GetChapPassword(),0,2);
                } else {
                    $hex_id = $this->GetChapId();
                }
        
                $server_result = $this->CheckUserTokenOnServer($real_user, $this->GetChapPassword(), 'CHAP', $hex_id, $this->GetChapChallenge());
            } else {
                $server_result = $this->CheckUserTokenOnServer($real_user, $input_to_check);
            }

            if ($this->_xml_dump_in_log) {
                $this->WriteLog("Debug: CheckUserTokenOnServer returns ".$server_result, FALSE, FALSE, 8888, 'Debug', '');
            }
        }

        if ($this->GetVerboseFlag() && $this->IsKeepLocal()) {
            $this->WriteLog("Info: *Local users are kept locally", FALSE, FALSE, 8888, 'System', '');
        }
        if (0 == $server_result) {
            $result = 0;
            $this->WriteLog("Info: User ".$this->GetUser()." successfully logged in using an external server", FALSE, FALSE, $result, 'User');
        } elseif (18 == $server_result) {
            $result = 18; // ERROR: User doesn't exist. (on the server)
            $this->WriteLog("Info: SMS code request received and sent for ".$this->GetUser()." to ".$this->CleanPhoneNumber($this->GetUserSms()), FALSE, FALSE, $result, 'SMS', $this->GetUser());
        } elseif ((21 == $server_result) && (!$this->IsKeepLocal())) {
            $this->DeleteUser($real_user, TRUE); // $no_error_info = TRUE
            $result = 21; // ERROR: User doesn't exist. (on the server)
            $this->WriteLog("Error: User ".$this->GetUser()." doesn't exist", FALSE, FALSE, $result, 'User');
        } elseif ((($server_result >= 0) && (22 <= $server_result) && (70 > $server_result)) || (90 <= $server_result)) {
            // We want to stop only if it's an error (but not -1), except if the user doesn't exist (>= 22), if it's a 7x (server) or 8x (cache) error
            $result = $server_result;
            // Already logged using CheckUserTokenOnServer
            // $this->WriteLog("Error: server sent back the error ".$server_result, FALSE, FALSE, $result, 'Server', '');
        } elseif (!$this->ReadUserData($real_user, FALSE, TRUE)) {
            // LOCALLY ONLY
            $result = 21; // ERROR: User doesn't exist.
            $this->WriteLog("Error: User ".$this->GetUser()." doesn't exist", FALSE, FALSE, $result, 'User');
        } else {
            // *********************************************
            // Let's go for the whole authentication process
            // *********************************************
            $result = 99; // Unknown error

            // multi account works only if authentication is done with PAP
            if (1 == intval($this->GetUserMultiAccount())) {
                $pos = strrpos($input_to_check, " ");
                if ($pos !== FALSE) {
                    $real_user = substr($input_to_check,0,$pos);
                    $input_to_check = trim(substr($input_to_check,$pos+1));
                    if (strlen($input_to_check) < 3) {
                        $input_to_check = "! <3 digits";
                    }
                }
        
                // LOCALLY ONLY
                if (!$this->ReadUserData($real_user, FALSE, TRUE)) {
                    $result = 34; // ERROR: linked user doesn't exist.
                    $this->WriteLog("Error: linked user ".$real_user." doesn't exist", FALSE, FALSE, $result, 'User', $real_user);
                    return $result;
                }
            }

            // From here now, we know already which user we are testing exactly
            
                
            // First we check if the users is activated or not.
            if (1 != $this->GetUserActivated()) {
                $result = 38; // ERROR: User is desactivated.
                $this->WriteLog("Error: User ".$real_user." is desactivated", FALSE, FALSE, $result, 'User', $real_user);
                return $result;
            }

            $detected_serial_number = '';

            // Check if self-registration of tokens is enabled and try the autoregistration if needed
            if ($this->IsSelfRegistrationEnabled()) {
                // Self-registration serial number is directly given in the dedicated field
                if ('' != $self_register_serial) {
                    if ($this->CheckTokenExists($self_register_serial)) {
                        $detected_serial_number = $self_register_serial;
                    }
                }
            }
            
            if ('' == $detected_serial_number) {
                // Looking for an existing token with this serial number
                $token_serial_number_length = $this->GetTokenSerialNumberLength();
                $token_serial_number_length_array = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$token_serial_number_length))));
                foreach($token_serial_number_length_array as $one_serial_number_length) {
                    if (intval($one_serial_number_length) > 0) {
                        $token_otp_list_of_length = $this->GetTokenOtpListOfLength();
                        $token_otp_list_of_length_array = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$token_otp_list_of_length))));
                        foreach($token_otp_list_of_length_array as $one_token_otp_length) {
                            if (intval($one_token_otp_length) > 0) {
                                if (strlen($input_to_check) >= (intval($one_serial_number_length) + intval($one_token_otp_length))) {
                                    $check_serial = substr($input_to_check,
                                                           -(intval($one_serial_number_length)+intval($one_token_otp_length)),
                                                           -intval($one_token_otp_length)
                                                          );
                                    if ($this->CheckTokenExists($check_serial)) {
                                        $detected_serial_number = $check_serial;
                                        /*
                                        $input_to_check = substr($input_to_check,
                                                                 0,
                                                                 -(intval($one_serial_number_length)+intval($one_token_otp_length))
                                                                ).
                                                          substr($input_to_check,
                                                                 -intval($one_token_otp_length)
                                                                );
                                        */
                                        // It can appears twice, so we do a replace
                                        $input_to_check = str_replace($detected_serial_number, '', $input_to_check);
                                        break(2);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            
            // If detected, we remove the serial number in the input_sync
            if ('' != $detected_serial_number) {
                $input_sync = str_replace($detected_serial_number, '', $input_sync);
            }


            if ($this->IsSelfRegistrationEnabled()) {
                // echo "DEBUG SelfRegisterHardwareToken: $detected_serial_number / $self_input_to_check\n";

                // TODO check if the serial number is in the list (instead of a single token)
                if (('' != $detected_serial_number) && ($detected_serial_number != $this->GetUserTokenSerialNumber())) {
                    $result = $this->SelfRegisterHardwareToken($real_user,$detected_serial_number, $input_to_check, $input);
                    if (0 == $result) {
                        return $result;
                    }
                }
            }

            // From here now, we know already which user we are testing exactly,
            // and also if a serial number is defined (and the input to check has been recalculated).
            // TODO: Without serial number we have to check with all tokens attributed to this user
            
            $now_epoch = time();
            
            if (($this->GetUserAutolockTime() > 0) && ($this->GetUserAutolockTime() < $now_epoch)) {
                $result = 81; // ERROR: Cache too old for this user, account autolocked
                $this->WriteLog("Error: cache too old for user ".$real_user.", account autolocked.", FALSE, FALSE, $result, 'User', $real_user);
                return $result;
            }

            if ('' != $this->GetChapPassword()) {
                $input_to_check = $this->GetChapPassword();
            } elseif ('' != $this->GetMsChapResponse()) {
                $input_to_check = $this->GetMsChapResponse();
            } elseif ('' != $this->GetMsChap2Response()) {
                $input_to_check = $this->GetMsChap2Response();
            }

            // Check if we have to validate an SMS code
            if ($this->GetUserSmsValidity() > $now_epoch) {
                $ldap_check_passed = FALSE;
                $ldap_to_check = 'LDAP_FALSE';
                
                // AD/LDAP case
                if ((1 == $this->GetUserPrefixPin()) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                    $code_confirmed = $this->GetUserSmsOtp();
                    $this->SetLastClearOtpValue($code_confirmed);
                    $code_to_check = substr($input_to_check, -strlen($code_confirmed));
                    $ldap_to_check = substr($input_to_check, 0, strlen($input_to_check) - strlen($code_to_check));
                    if ($code_to_check == $code_confirmed) {
                        if (('' != $ldap_to_check) && ($this->CheckUserLdapPassword($this->GetUserSynchronizedDn(), $ldap_to_check))) {
                            $ldap_check_passed = TRUE;
                            if ($this->IsCacheLdapHash()) {
                                // The LDAP password is stored in a cache
                                $this->SetUserLdapHashCache(bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check))));
                            }
                        } elseif ($this->IsCacheLdapHash()) {
                            if (!$this->IsLdapServerReachable()) {
                                if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                                }
                                if ($this->GetUserLdapHashCache() == bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                    $ldap_check_passed = TRUE;
                                    // TODO Write a specific message in the log
                                }
                            } else {
                                $ldap_check_passed = FALSE;
                                $ldap_to_check = 'LDAP_FALSE';
                                $this->ResetUserLdapHashCache();
                                if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                                }
                            }
                        }
                    }
                } else {
                    // It is a real prefix pin, not an LDAP/AD prefix
                    $code_confirmed = ((1 == $this->GetUserPrefixPin())?$this->GetUserPin():'').$this->GetUserSmsOtp();
                    $this->SetLastClearOtpValue($code_confirmed);
                    if ('' != $this->GetChapPassword()) {
                        $code_confirmed = $this->CalculateChapPassword($code_confirmed);
                    } elseif ('' != $this->GetMsChapResponse()) {
                        $code_confirmed = $this->CalculateMsChapResponse($code_confirmed);
                    } elseif ('' != $this->GetMsChap2Response()) {
                        $code_confirmed = $this->CalculateMsChap2Response($real_user, $code_confirmed);
                    }
                }

                if ($ldap_check_passed || ($input_to_check == $code_confirmed)) {
                    $this->SetUserSmsOtp(md5($this->GetEncryptionKey().mt_rand(100000,999999).$this->GetUserTokenSeed().$now_epoch)); // Now SMS code is no more available, and the next one is difficult to guess ;-)
                    $this->SetUserSmsValidity($now_epoch); // And the validity time is set to the successful login time

                    // We are unlocking the user if needed
                    $this->SetUserErrorCounter(0);
                    $this->SetUserLocked(0);
                    // Finally, we update the last login of the user
                    $this->SetUserTokenLastLogin($now_epoch);
                    $result = 0; // OK: This is the correct token
                    if (!$this->WriteUserData()) {
                        $result = 28; // ERROR: Unable to write the changes in the file
                        $this->WriteLog("Error: Unable to write the changes in the file for the user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                    } else {
                        $this->WriteLog("Ok: User ".$this->GetUser()." successfully logged in with SMS token", FALSE, FALSE, $result, 'User');
                    }
                    
                    if (0 == $result) {
                        $this->AddExtraRadiusInfo();
                    }
                    return $result;
                }
            }
            
            // Check if we have to validate a scratch password
            foreach ($this->GetUserScratchPasswordsArray() as $one_password) {
                // AD/LDAP case
                if ((1 == $this->GetUserPrefixPin()) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                    $ldap_check_passed = FALSE;
                    $ldap_to_check = 'LDAP_FALSE';

                    $code_confirmed = $one_password;
                    $this->SetLastClearOtpValue($code_confirmed);
                    $code_to_check = substr($input_to_check, -strlen($code_confirmed));
                    $ldap_to_check = substr($input_to_check, 0, strlen($input_to_check) - strlen($code_to_check));
                    
                    if ($code_to_check == $code_confirmed) {
                        if (('' != $ldap_to_check) && ($this->CheckUserLdapPassword($this->GetUserSynchronizedDn(), $ldap_to_check))) {
                            $ldap_check_passed = TRUE;
                            if ($this->IsCacheLdapHash()) {
                                // The LDAP password is stored in a cache
                                $this->SetUserLdapHashCache(bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check))));
                            }
                        }
                        elseif ($this->IsCacheLdapHash()) {
                            if (!$this->IsLdapServerReachable()) {
                                if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                                }
                                if ($this->GetUserLdapHashCache() == bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                    $ldap_check_passed = TRUE;
                                    // TODO Write a specific message in the log
                                }
                            } else {
                                $ldap_check_passed = FALSE;
                                $ldap_to_check = 'LDAP_FALSE';
                                $this->ResetUserLdapHashCache();
                                if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                                }
                            }
                        }
                    }
                } else {
                    // It is a real prefix pin, not an LDAP/AD prefix
                    $code_confirmed = ((1 == $this->GetUserPrefixPin())?$this->GetUserPin():'').$one_password;
                    $this->SetLastClearOtpValue($code_confirmed);
                    if ('' != $this->GetChapPassword()) {
                        $code_confirmed = $this->CalculateChapPassword($code_confirmed);
                    } elseif ('' != $this->GetMsChapResponse()) {
                        $code_confirmed = $this->CalculateMsChapResponse($code_confirmed);
                    } elseif ('' != $this->GetMsChap2Response()) {
                        $code_confirmed = $this->CalculateMsChap2Response($real_user, $code_confirmed);
                    }
                }
                
                if ($ldap_check_passed || ($input_to_check == $code_confirmed)) {
                    // We are unlocking the regular token if needed
                    $this->SetUserErrorCounter(0);
                    $this->SetUserLocked(0);
                    // Finally, we update the last login of the user
                    $this->SetUserTokenLastLogin($now_epoch);
                    $this->RemoveUserUsedScratchPassword($one_password);
                    $result = 0; // OK: This is the correct token
                    if (!$this->WriteUserData()) {
                        $result = 28; // ERROR: Unable to write the changes in the file
                        $this->WriteLog("Error: Unable to write the changes in the file for the user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                    } else {
                        $this->WriteLog("Ok: User ".$this->GetUser()." successfully logged in with a scratch password", FALSE, FALSE, $result, 'User');
                    }
                    
                    if (0 == $result) {
                        $this->AddExtraRadiusInfo();
                    }
                    return $result;
                }
            }
            // Check if a code request per SMS is done
            $code_confirmed = 'sms';
            $code_confirmed_upper = 'SMS';
            $code_confirmed_camel = 'Sms';
            $this->SetLastClearOtpValue($code_confirmed);
            if ('' != $this->GetChapPassword()) {
                $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                $code_confirmed_upper = strtoupper($this->CalculateChapPassword($code_confirmed_upper));
                $code_confirmed_camel = strtoupper($this->CalculateChapPassword($code_confirmed_camel));
            } elseif ('' != $this->GetMsChapResponse()) {
                $code_confirmed = strtolower($this->CalculateMsChapResponse($code_confirmed));
                $code_confirmed_upper = strtoupper($this->CalculateMsChapResponse($code_confirmed_upper));
                $code_confirmed_camel = strtoupper($this->CalculateMsChapResponse($code_confirmed_camel));
            } elseif ('' != $this->GetMsChap2Response()) {
                $code_confirmed = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                $code_confirmed_upper = strtoupper($this->CalculateMsChap2Response($real_user, $code_confirmed_upper));
                $code_confirmed_camel = strtoupper($this->CalculateMsChap2Response($real_user, $code_confirmed_camel));
            }
            
            // If something like 'sms' or 'SMS' is detected, we generate an SMS token
            if ((strtolower($input_to_check) == $code_confirmed) || (strtoupper($input_to_check) == $code_confirmed_upper) || (strtoupper($input_to_check) == $code_confirmed_camel)) {
                return $this->GenerateSmsToken();
            }

            // TODO check multiple tokens (loop)

            $pin               = $this->GetUserPin();
            $need_prefix       = (1 == $this->GetUserPrefixPin());
            $last_event        = $this->GetUserTokenLastEvent();
            $last_login        = $this->GetUserTokenLastLogin();
            $digits            = $this->GetUserTokenNumberOfDigits();
            $error_counter     = $this->GetUserErrorCounter();
            $time_window       = $this->GetMaxTimeWindow();
            $event_window      = $this->GetMaxEventWindow();
            $time_sync_window  = $this->GetMaxTimeResyncWindow();
            $event_sync_window = $this->GetMaxEventResyncWindow();

            $seed              = $this->GetUserTokenSeed();
            $seed_bin          = hex2bin($seed);
            $delta_time        = $this->GetUserTokenDeltaTime();
            $interval          = $this->GetUserTokenTimeInterval();
            $token_algo_suite  = $this->GetUserTokenAlgoSuite();
            if (0 >= $interval) {
                $interval = 1;
            }

            $now_steps         = intval($now_epoch / $interval);
            $step_window       = intval($time_window / $interval);
            $step_sync_window  = intval($time_sync_window / $interval);
            $last_login_step   = intval($last_login / $interval);
            $delta_step        = $delta_time / $interval;
            
            $prefix_pin = ($need_prefix?$pin:'');


            // 4.3.2.2
            // Check if resynchronisation can be done automatically
            $needed_space_pos = (strlen($input_to_check)-$digits-1);
            if (('' == $input_sync) && ($needed_space_pos >= $digits) && (($needed_space_pos === strrpos($input_to_check, ' ')) || (($needed_space_pos-strlen($prefix_pin)) === strrpos($input_to_check, ' '))) && ($this->IsAutoResync())) {
                if (($need_prefix) && ($this->IsUserRequestLdapPasswordEnabled())) {
                    $ldap_to_check = substr($input_to_check, 0, - ($digits + 1 + $digits));
                    if ('' != $ldap_to_check) {
                        if ($this->CheckUserLdapPassword($this->GetUserSynchronizedDn(), $ldap_to_check)) {
                            $input_sync = substr($input_to_check, -$digits);
                            $input_to_check = substr($input_to_check, 0, - ($digits + 1));
                        }
                    }
                } elseif ($prefix_pin == substr($input_to_check, 0, strlen($prefix_pin))) {
                        $separator_pos = strrpos($input_to_check, ' ');
                        $input_sync = str_replace($prefix_pin, '', substr($input_to_check, $separator_pos+1));
                        $input_to_check = substr($input_to_check, 0, $separator_pos);
                }
            }


            if ((1 == $this->GetUserLocked()) && ('' == $input_sync) && (!$resync_enc_pass) && (!$ignore_lock)) {
                $result = 24; // ERROR: User locked;
                $this->WriteLog("Error: User ".$this->GetUser()." locked after ".$this->GetUserErrorCounter()." failed authentications", FALSE, FALSE, $result, 'User');
            } elseif(($this->GetUserErrorCounter() >= $this->GetMaxDelayedFailures()) && ('' == $input_sync) && ($now_epoch < ($this->GetUserTokenLastError() + $this->GetMaxDelayedTime())) && (!$ignore_lock)) {
                $result = 25; // ERROR: User delayed;
                $this->WriteLog("Error: User ".$this->GetUser()." delayed for ".$this->GetMaxDelayedTime()." seconds after ".$this->GetUserErrorCounter()." failed authentications", FALSE, FALSE, $result, 'User');
            } else {
                $ldap_check_passed = FALSE;
                $ldap_to_check = 'LDAP_FALSE';
                if (($need_prefix) && ($this->IsUserRequestLdapPasswordEnabled())) {
                    if ($input_to_check != '') {
                        $ldap_to_check = substr($input_to_check, 0, strlen($input_to_check) - $digits);
                        if ('' != $ldap_to_check) {
                            if ($this->CheckUserLdapPassword($this->GetUserSynchronizedDn(), $ldap_to_check)) {
                                $ldap_check_passed = TRUE;
                                // TODO Write a specific message in the log
                                if ($this->IsCacheLdapHash()) {
                                    $this->SetUserLdapHashCache(bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check))));
                                }
                            } elseif ($this->IsCacheLdapHash()) {
                                if (!$this->IsLdapServerReachable()) {
                                    if ($this->GetVerboseFlag()) {
                                        $this->WriteLog("Debug: user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                                    }
                                    if ($this->GetUserLdapHashCache() == bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                        $ldap_check_passed = TRUE;
                                        // TODO Write a specific message in the log
                                    }
                                } else {
                                    $ldap_check_passed = FALSE;
                                    $ldap_to_check = 'LDAP_FALSE';
                                    $this->ResetUserLdapHashCache();
                                    if ($this->GetVerboseFlag()) {
                                        $this->WriteLog("Debug: user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                                    }
                                }
                            }
                        }
                    }
                    if (!$ldap_check_passed) {
                        $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                        $input_to_check = "LDAP_FAILED";
                        $result = 99;
                    }
                }
                
                switch (strtolower($this->GetUserAlgorithm())) {
                    case 'motp':
                        if (('' == $input_sync) && (!$resync_enc_pass)) {
                            $max_steps = 2 * $step_window;
                        } else {
                            $max_steps = 2 * $step_sync_window;
                        }
                        $check_step = 1;

                        do {
                            $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                            $pure_calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step, $digits);
                            $calculated_token = $pure_calculated_token;
                            
                            if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                $code_confirmed_without_pin = $calculated_token;
                                $code_confirmed = $calculated_token;
                                $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                $this->SetLastClearOtpValue($code_confirmed);
                            } else {
                                if ($need_prefix) {
                                    $calculated_token = $pin.$calculated_token;
                                }

                                $code_confirmed_without_pin = $pure_calculated_token;
                                $code_confirmed = $calculated_token;
                                $this->SetLastClearOtpValue($code_confirmed);
                                if ('' != $this->GetChapPassword()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                                } elseif ('' != $this->GetMsChapResponse()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateMsChapResponse($code_confirmed));
                                } elseif ('' != $this->GetMsChap2Response()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                                }
                            }
                            
                            if (('' == $input_sync) && (!$resync_enc_pass)) {
                                // With mOTP, the code should not be prefixed, so we accept of course always input without prefix!
                                if (($input_to_check == $code_confirmed) || ($input_to_check == $code_confirmed_without_pin)) {
                                    if ($input_to_check == $code_confirmed_without_pin) {
                                        $code_confirmed = $code_confirmed_without_pin;
                                    }
                                    if (($now_steps+$additional_step+$delta_step) > $last_login_step) {
                                        $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                        $this->SetUserTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                        $this->SetUserErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                        $this->WriteLog("Ok: User ".$this->GetUser()." successfully logged in", FALSE, FALSE, $result, 'User');
                                    } else {
                                        $this->SetUserErrorCounter($error_counter+1);
                                        $this->SetUserTokenLastError($now_epoch);
                                        $result = 26; // ERROR: this token has already been used
                                        $this->WriteLog("Error: token of user ".$this->GetUser()." already used", FALSE, FALSE, $result, 'User');
                                    }
                                } else {
                                    $check_step++;
                                }
                            } elseif (($input_to_check == $code_confirmed) || ($input_to_check == $code_confirmed_without_pin)) {
                                $pure_sync_calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step+1, $digits);
                                $sync_calculated_token = $pure_sync_calculated_token;
                                
                                if (($need_prefix) && ($input_sync != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                    $input_sync = substr($input_sync, -strlen($code_confirmed));                            
                                } elseif ($need_prefix) {
                                    $sync_calculated_token = $pin.$sync_calculated_token;
                                }
                                if ((($input_sync == $sync_calculated_token) || ($input_sync == $pure_sync_calculated_token)) && (($now_steps+$additional_step+$delta_step+1) > $last_login_step)) {
                                    $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step+1) * $interval);
                                    $this->SetUserTokenDeltaTime(($additional_step+$delta_step+1) * $interval);
                                    $this->SetUserErrorCounter(0);
                                    $this->SetUserLocked(0);
                                    $result = 14; // INFO: token is now synchronized
                                    $this->WriteLog("Info: token for user ".$this->GetUser()." is now resynchronized with a delta of ".(($additional_step+$delta_step+1) * $interval). " seconds", FALSE, FALSE, $result, 'User');
                                    $result = 0; // INFO: authentication is successful, regardless of the PIN code if needed, as the PIN code is already used to generate the token
                                } else {
                                    $result = 27; // ERROR: resync failed
                                    $this->WriteLog("Error: resync for user ".$this->GetUser()." has failed", FALSE, FALSE, $result, 'User');
                                }
                            } else {
                                $check_step++;
                                if ($display_status) {
                                    MultiotpShowStatus($check_step, $max_steps);
                                }
                            }
                        } while (($check_step < $max_steps) && (90 <= $result));
                        if ($display_status) {
                            echo "\r\n";
                        }
                        if (90 <= $result) {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                        }
                        break;
                    case 'hotp';
                        if (('' == $input_sync)&& (!$resync_enc_pass)) {
                            $max_steps = 2 * $event_window;
                        } else {
                            $max_steps = 2 * $event_sync_window;
                        }
                        $check_step = 1;
                        do {
                            $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                            $pure_calculated_token = $this->GenerateOathHotp($seed_bin,$last_event+$additional_step,$digits,$token_algo_suite);
                            $calculated_token = $pure_calculated_token;
                            if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                $code_confirmed_without_pin = $calculated_token;
                                $code_confirmed = $calculated_token;
                                $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                $this->SetLastClearOtpValue($code_confirmed);
                            } else {
                                if ($need_prefix) {
                                    $calculated_token = $pin.$calculated_token;
                                }
                                
                                $code_confirmed_without_pin = $pure_calculated_token;
                                $code_confirmed = $calculated_token;
                                $this->SetLastClearOtpValue($code_confirmed);
                                if ('' != $this->GetChapPassword()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                                } elseif ('' != $this->GetMsChapResponse()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateMsChapResponse($code_confirmed));
                                } elseif ('' != $this->GetMsChap2Response()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                                }
                            }
                            
                            if (('' == $input_sync) && (!$resync_enc_pass)) {
                                if ($input_to_check == $code_confirmed) {
                                    if ($additional_step >= 1) {
                                        $this->SetUserTokenLastLogin($now_epoch);
                                        $this->SetUserTokenLastEvent($last_event+$additional_step);
                                        $this->SetUserErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                        $this->WriteLog("OK: User ".$this->GetUser()." successfully logged in", FALSE, FALSE, $result, 'User');
                                    } else {
                                        $this->SetUserErrorCounter($error_counter+1);
                                        $this->SetUserTokenLastError($now_epoch);
                                        $result = 26; // ERROR: this token has already been used
                                        $this->WriteLog("Error: token of user ".$this->GetUser()." already used", FALSE, FALSE, $result, 'User');
                                    }
                                } else {
                                    $check_step++;
                                }
                            } elseif (($input_to_check == $code_confirmed) || ($input_to_check == $code_confirmed_without_pin)) {
                                $pure_sync_calculated_token = $this->GenerateOathHotp($seed_bin, $last_event+$additional_step+1,$digits,$token_algo_suite);
                                $sync_calculated_token = $pure_sync_calculated_token;
                                
                                if (($need_prefix) && ($input_sync != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                    $input_sync = substr($input_sync, -strlen($code_confirmed));                            
                                } elseif ($need_prefix) {
                                    $sync_calculated_token = $pin.$sync_calculated_token;
                                }
                                if ((($input_sync == $sync_calculated_token) || ($input_sync == $pure_sync_calculated_token)) && ($additional_step >= 1)) {
                                    $this->SetUserTokenLastLogin($now_epoch);
                                    $this->SetUserTokenLastEvent($last_event+$additional_step+1);
                                    $this->SetUserErrorCounter(0);
                                    $this->SetUserLocked(0);
                                    $result = 14; // INFO: token is now synchronized
                                    $this->WriteLog("Info: token for user ".$this->GetUser()." is now resynchronized with the last event ".($last_event+$additional_step+1), FALSE, FALSE, $result, 'User');
                                    if ($input_to_check == $code_confirmed) {
                                        $result = 0; // INFO: authentication is successful, as the prefix has also been typed (if any)
                                    }
                                } else {
                                    $result = 27; // ERROR: resync failed
                                    $this->WriteLog("Error: resync for user ".$this->GetUser()." has failed", FALSE, FALSE, $result, 'User');
                                }
                            } else {
                                $check_step++;
                                if ($display_status) {
                                    MultiotpShowStatus($check_step, $max_steps);
                                }
                            }
                        } while (($check_step < $max_steps) && ((90 <= $result)));
                        if ($display_status) {
                            echo "\r\n";
                        }
                        if (90 <= $result) {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                        }
                        break;
                    case 'yubicootp';
                        $yubikey_class = new MultiotpYubikey();
                        $bad_precheck = FALSE;
                        if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                            if (!$ldap_check_passed) {
                                $input_to_check.= '_BAD_LDAP_CHECK';
                                $bad_precheck = TRUE;
                            }
                            $this->SetLastClearOtpValue($input_to_check);
                        } else {
                            if ($need_prefix) {
                                if ($pin != substr($input_to_check, 0, strlen($pin))) {
                                    $this->SetLastClearOtpValue($input_to_check);
                                    $input_to_check.= '_BAD_PREFIX';
                                    $bad_precheck = TRUE;
                                }
                            }
                        }

                        if (!$bad_precheck) {
                            // Check only the last 32 digits, the first 12 are the serial number
                            $result = $yubikey_class->CheckYubicoOtp(substr($input_to_check, -32),
                                                                     $seed,
                                                                     $last_event);
                        }

                        if (0 == $result) {
                            $calculated_token = $input_to_check;
                            $this->SetUserTokenLastLogin($now_epoch);
                            $this->SetUserTokenLastEvent($yubikey_class->GetYubicoOtpLastCount());
                            $this->SetUserErrorCounter(0);
                            $result = 0; // OK: This is the correct token
                            $this->WriteLog("OK: User ".$this->GetUser()." successfully logged in", FALSE, FALSE, $result, 'User');
                        } elseif (26 == $result) {
                            $this->SetUserErrorCounter(1); // TODO $error_counter+1, includes resync
                            $this->SetUserTokenLastError($now_epoch);
                            $result = 26; // ERROR: this token has already been used
                            $this->WriteLog("Error: token of user ".$this->GetUser()." already used", FALSE, FALSE, $result, 'User');
                        } else {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                        }
                        break;
                    case 'totp';
                        if (('' == $input_sync) && (!$resync_enc_pass)) {
                            $max_steps = 2 * $step_window;
                        } else {
                            $max_steps = 2 * $step_sync_window;
                        }
                        $check_step = 1;
                        do {
                            $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                            $pure_calculated_token = $this->GenerateOathHotp($seed_bin,$now_steps+$additional_step+$delta_step,$digits,$token_algo_suite);
                            $calculated_token = $pure_calculated_token;
                            
                            if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                $code_confirmed_without_pin =  $calculated_token;
                                $code_confirmed = $calculated_token;
                                $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                $this->SetLastClearOtpValue($code_confirmed);
                            } else {
                                if ($need_prefix) {
                                    $calculated_token = $pin.$calculated_token;
                                }

                                $code_confirmed_without_pin = $pure_calculated_token;
                                $code_confirmed = $calculated_token;
                                $this->SetLastClearOtpValue($code_confirmed);
                                if ('' != $this->GetChapPassword()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                                } elseif ('' != $this->GetMsChapResponse()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateMsChapResponse($code_confirmed));
                                } elseif ('' != $this->GetMsChap2Response()) {
                                    $code_confirmed_without_pin = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed_without_pin));
                                    $code_confirmed = strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                                }
                            }
                            
                            if (('' == $input_sync) && (!$resync_enc_pass)) {
                                if ($input_to_check == $code_confirmed) {
                                    if (($now_steps+$additional_step+$delta_step) > $last_login_step) {
                                        $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                        $this->SetUserTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                        $this->SetUserErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                        $this->WriteLog("OK: User ".$this->GetUser()." successfully logged in", FALSE, FALSE, $result, 'User');
                                    } else {
                                        $this->SetUserErrorCounter($error_counter+1);
                                        $this->SetUserTokenLastError($now_epoch);
                                        $result = 26; // ERROR: this token has already been used
                                        $this->WriteLog("Error: token of user ".$this->GetUser()." already used", FALSE, FALSE, $result, 'User');
                                    }
                                } else {
                                    $check_step++;
                                }
                            } elseif (($input_to_check == $code_confirmed) || ($input_to_check == $code_confirmed_without_pin)) {
                                $pure_sync_calculated_token = $this->GenerateOathHotp($seed_bin,$now_steps+$additional_step+$delta_step+1,$digits,$token_algo_suite);
                                $sync_calculated_token = $pure_sync_calculated_token;
                                
                                if (($need_prefix) && ($input_sync != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                    $input_sync = substr($input_sync, -strlen($code_confirmed));                            
                                } elseif ($need_prefix) {
                                    $sync_calculated_token = $pin.$sync_calculated_token;
                                }
                                if ((($input_sync == $sync_calculated_token) || ($input_sync == $pure_sync_calculated_token)) && (($now_steps+$additional_step+$delta_step) > $last_login_step)) {
                                    $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step+1) * $interval);
                                    $this->SetUserTokenDeltaTime(($additional_step+$delta_step+1) * $interval);
                                    $this->SetUserErrorCounter(0);
                                    $this->SetUserLocked(0);
                                    $result = 14; // INFO: token is now synchronized
                                    $this->WriteLog("Info: token for user ".$this->GetUser()." is now resynchronized with a delta of ".(($additional_step+$delta_step+1) * $interval). " seconds", FALSE, FALSE, $result, 'User');
                                    if ($input_to_check == $code_confirmed) {
                                        $result = 0; // INFO: authentication is successful, as the prefix has also been typed (if any)
                                    }
                                } else {
                                    $result = 27; // ERROR: resync failed
                                    $this->WriteLog("Error: resync for user ".$this->GetUser()." has failed", FALSE, FALSE, $result, 'User');
                                }
                            } else {
                                $check_step++;
                                if ($display_status) {
                                    MultiotpShowStatus($check_step, $max_steps);
                                }
                            }
                        } while (($check_step < $max_steps) && (90 <= $result));
                        if ($display_status) {
                            echo "\r\n";
                        }
                        if (90 <= $result) {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                        }
                        break;
                    default:
                        $result = 23;
                        $this->WriteLog("Error: ".$this->GetUserAlgorithm()." algorithm is unknown", FALSE, FALSE, $result, 'User');
                }
            }

            if (0 == $result) {
                $this->SetUserLocked(0);
            }
            
            if (90 <= $result) {
                if ($this->GetVerboseFlag()) {
                    if ('' != $this->GetChapPassword()) {
                        $this->WriteLog("*(authentication typed by the user is CHAP encrypted)", FALSE, FALSE, $result, 'User');
                    } elseif ('' != $this->GetMsChapResponse()) {
                        $this->WriteLog("*(authentication typed by the user is MS-CHAP encrypted)", FALSE, FALSE, $result, 'User');
                    } elseif ('' != $this->GetMsChap2Response()) {
                        $this->WriteLog("*(authentication typed by the user is MS-CHAP V2 encrypted)", FALSE, FALSE, $result, 'User');
                    } elseif ((strlen($input_to_check) == strlen($calculated_token))) {
                        $this->WriteLog("*(authentication typed by the user: ".$input_to_check.")", FALSE, FALSE, $result, 'User');
                    } else {
                        $result = 98;
                        $this->WriteLog("*(authentication typed by the user is ".strlen($input_to_check)." chars long instead of ".strlen($calculated_token)." chars)", FALSE, FALSE, $result, 'User');
                    }
                } elseif (('' == $this->GetChapPassword()) &&
                          ('' == $this->GetMsChapResponse()) &&
                          ('' == $this->GetMsChap2Response()) &&
                          ((strlen($input_to_check) != strlen($calculated_token)))
                         ) {
                    $result = 98;
                    $this->WriteLog("Error: authentication typed by the user is ".strlen($input_to_check)." chars long instead of ".strlen($calculated_token)." chars", FALSE, FALSE, $result, 'User');
                }
            }
            
            if ($this->GetUserErrorCounter() >= $this->GetMaxBlockFailures()) {
                $this->SetUserLocked(1);
            }
            if (!$this->WriteUserData()) {
                $result = 28; // ERROR: Unable to write the changes in the file
                $this->WriteLog("Error: Unable to write the changes in the file for the user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
            }
        } // end of the else block of the test: if (!$this->ReadUserData($real_user))

        if (0 == $result) {
            $this->AddExtraRadiusInfo();
        }
        return $result;
    }


    function SelfRegisterHardwareToken($user, $serial, $input, $original_input = '')
    {
        // TODO the whole process has to be changed to support multi tokens
        $result = 99; // Unknown error
        $calculated_token = '';
        if ('' == $original_input)
        {
            $original_input = $input;
        }
        $serial_number = strtolower($serial);
        if ($this->ReadUserData($user))
        {
            $pin = $this->GetUserPin();
            $need_prefix = (1 == $this->GetUserPrefixPin());

            if ($this->ReadTokenData($serial_number))
            {
                $attributed_users = trim($this->GetTokenAttributedUsers());
                if ('' != trim($attributed_users))
                {
                    if (FALSE === strpos(','.$attributed_users.',', ','.$user.','))
                    {
                        $result = 37; // ERROR: Token already attributed
                        $this->WriteLog("Error: Token ".$this->GetToken()." already attributed", FALSE, FALSE, $result, 'Token', $user);
                    }
                    // else $result = 99; // The token is already attributed to this user, stay neutral with the error
                }
                else
                {
                    $algorithm = $this->GetTokenAlgorithm();
                    $token_algo_suite = $this->GetTokenAlgoSuite();
                    $seed = $this->GetTokenSeed();
                    $digits = $this->GetTokenNumberOfDigits();
                    $time_interval = $this->GetTokenTimeInterval();
                    $last_event = $this->GetTokenLastEvent();
                    $delta_time = $this->GetTokenDeltaTime();
                    $last_login = $this->GetTokenLastLogin();
                    $error_counter = $this->GetTokenErrorCounter();

                    $now_epoch = time();

                    $input_to_check = $input;
                    $interval = (0 >= $time_interval)?1:$time_interval;
                    $seed_bin = hex2bin($seed);

                    if (strlen($input_to_check) < 3)
                    {
                        $input_to_check = "! <3 digits";
                    }

                    $now_steps         = intval($now_epoch / $interval);
                    $time_window       = $this->GetMaxTimeWindow();
                    $step_window       = intval($time_window / $interval);
                    $event_window      = $this->GetMaxEventWindow();
                    $time_sync_window  = $this->GetMaxTimeResyncWindow();
                    $step_sync_window  = intval($time_sync_window / $interval);
                    $event_sync_window = $this->GetMaxEventResyncWindow();
                    $last_login_step   = intval($last_login / $interval);
                    $delta_step        = $delta_time / $interval;
                        
                    $ldap_check_passed = FALSE;
                    $ldap_to_check = 'LDAP_FALSE';
                    if (($need_prefix) && ($this->IsUserRequestLdapPasswordEnabled()))
                    {
                        if ($input_to_check != '')
                        {
                            $ldap_to_check = substr($input_to_check, 0, strlen($input_to_check) - $digits);
                            if ('' != $ldap_to_check)
                            {
                                if ($this->CheckUserLdapPassword($this->GetUserSynchronizedDn(), $ldap_to_check))
                                {
                                    $ldap_check_passed = TRUE;
                                    // TODO Write a specific message in the log
                                    if ($this->IsCacheLdapHash())
                                    {
                                        $this->SetUserLdapHashCache(bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check))));
                                    }
                                }
                                elseif ($this->IsCacheLdapHash())
                                {
                                    if (!$this->IsLdapServerReachable())
                                    {
                                        if ($this->GetVerboseFlag())
                                        {
                                            $this->WriteLog("Debug: user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                                        }
                                        if ($this->GetUserLdapHashCache() == bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check))))
                                        {
                                            $ldap_check_passed = TRUE;
                                            // TODO Write a specific message in the log
                                        }
                                    }
                                    else
                                    {
                                        $ldap_check_passed = FALSE;
                                        $ldap_to_check = 'LDAP_FALSE';
                                        $this->ResetUserLdapHashCache();
                                        if ($this->GetVerboseFlag())
                                        {
                                            $this->WriteLog("Debug: user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                                        }
                                    }
                                }
                            }
                        }
                        if (!$ldap_check_passed)
                        {
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                            $input_to_check = "LDAP_FAILED";
                            $result = 99;
                        }
                    }

                    switch (strtolower($algorithm))
                    {
                        case 'motp':
                            $max_steps = 2 * $step_sync_window;
                            $check_step = 1;
                            do
                            {
                                $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                                $pure_calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step, $digits);
                                $calculated_token = $pure_calculated_token;
                                
                                if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled()))
                                {
                                    $code_confirmed_without_pin = $calculated_token;
                                    $code_confirmed = $calculated_token;
                                    $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                    if (!$ldap_check_passed)
                                    {
                                        $input_to_check.= '_BAD_LDAP_CHECK';
                                    }
                                    $this->SetLastClearOtpValue($original_input);
                                }
                                else
                                {
                                    if ($need_prefix)
                                    {
                                        $calculated_token = $pin.$calculated_token;
                                    }
                                    
                                    $code_confirmed_without_pin = $pure_calculated_token;
                                    $code_confirmed = $calculated_token;
                                    $this->SetLastClearOtpValue($original_input);
                                    if ('' != $this->GetChapPassword())
                                    {
                                        $code_confirmed_without_pin = strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                        $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                                    }
                                    elseif ('' != $this->GetMsChapResponse())
                                    {
                                        $code_confirmed_without_pin = strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                        $code_confirmed = strtolower($this->CalculateMsChapResponse($code_confirmed));
                                    }
                                    elseif ('' != $this->GetMsChap2Response())
                                    {
                                        $code_confirmed_without_pin = strtolower($this->CalculateMsChap2Response($user, $code_confirmed_without_pin));
                                        $code_confirmed = strtolower($this->CalculateMsChap2Response($user, $code_confirmed));
                                    }
                                }

                                if (($input_to_check == $code_confirmed) || ($input_to_check == $code_confirmed_without_pin))
                                {
                                    if (($now_steps+$additional_step+$delta_step) > $last_login_step)
                                    {
                                        $this->SetTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                        $this->SetTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                        $this->SetTokenErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                    }
                                    else
                                    {
                                        $result = 26; // ERROR: this token has already been used
                                    }
                                }
                                else
                                {
                                    $check_step++;
                                }
                            }
                            while (($check_step < $max_steps) && (90 <= $result));
                            break;
                        case 'hotp';
                            $max_steps = $event_sync_window;
                            $check_step = 1;
                            do
                            {
                                $pure_calculated_token = $this->GenerateOathHotp($seed_bin,$last_event+$check_step,$digits,$token_algo_suite);
                                $calculated_token = $pure_calculated_token;
                                
                                if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled()))
                                {
                                    $code_confirmed_without_pin = $calculated_token;
                                    $code_confirmed = $calculated_token;
                                    $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                    if (!$ldap_check_passed)
                                    {
                                        $input_to_check.= '_BAD_LDAP_CHECK';
                                    }
                                    $this->SetLastClearOtpValue($original_input);
                                }
                                else
                                {
                                    if ($need_prefix)
                                    {
                                        $calculated_token = $pin.$calculated_token;
                                    }
                                    
                                    $code_confirmed = $calculated_token;
                                    $this->SetLastClearOtpValue($original_input);
                                    if ('' != $this->GetChapPassword())
                                    {
                                        $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                                    }
                                    elseif ('' != $this->GetMsChapResponse())
                                    {
                                        $code_confirmed = strtolower($this->CalculateMsChapPassword($code_confirmed));
                                    }
                                    elseif ('' != $this->GetMsChap2Response())
                                    {
                                        $code_confirmed = strtolower($this->CalculateMsChap2Password($user, $code_confirmed));
                                    }
                                }

                                if ($input_to_check == $code_confirmed)
                                {
                                    $this->SetTokenLastLogin($now_epoch);
                                    $this->SetTokenLastEvent($last_event+$check_step);
                                    $this->SetTokenErrorCounter(0);
                                    $result = 0; // OK: This is the correct token
                                }
                                else
                                {
                                    $check_step++;
                                }
                            }
                            while (($check_step < $max_steps) && (90 <= $result));
                            break;
                        case 'yubicootp':
                            $yubikey_class = new MultiotpYubikey();
                            $bad_precheck = FALSE;
                            if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled()))
                            {
                                if (!$ldap_check_passed)
                                {
                                    $input_to_check.= '_BAD_LDAP_CHECK';
                                    $bad_precheck = TRUE;
                                }
                                $this->SetLastClearOtpValue($original_input);
                            }
                            else
                            {
                                if ($need_prefix)
                                {
                                    if ($pin != substr($input_to_check, 0, strlen($pin)))
                                    {
                                        $this->SetLastClearOtpValue($original_input);
                                        $input_to_check.= '_BAD_PREFIX';
                                        $bad_precheck = TRUE;
                                    }
                                }
                            }

                            if (!$bad_precheck)
                            {
                                // Check only the last 32 digits, the first 12 are the serial number
                                $result = $yubikey_class->CheckYubicoOtp(substr($input_to_check, -32),
                                                                         $seed,
                                                                         $last_event);
                            }
                            if (0 == $result)
                            {
                                $calculated_token = $input_to_check;
                                $this->SetTokenLastLogin($now_epoch);
                                $this->SetTokenLastEvent($yubikey_class->GetYubicoOtpLastCount());
                                $this->SetTokenErrorCounter(0);
                                $result = 0; // OK: This is the correct token
                            }
                            break;
                        case 'totp';
                            $max_steps = 2 * $step_sync_window;
                            $check_step = 1;
                            do
                            {
                                $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                                $pure_calculated_token = $this->GenerateOathHotp($seed_bin,$now_steps+$additional_step+$delta_step,$digits,$token_algo_suite);
                                $calculated_token = $pure_calculated_token;
                                
                                if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled()))
                                {
                                    $code_confirmed_without_pin = $calculated_token;
                                    $code_confirmed = $calculated_token;
                                    $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                    if (!$ldap_check_passed)
                                    {
                                        $input_to_check.= '_BAD_LDAP_CHECK';
                                    }
                                    $this->SetLastClearOtpValue($original_input);
                                }
                                else
                                {
                                    if ($need_prefix)
                                    {
                                        $calculated_token = $pin.$calculated_token;
                                    }

                                    $code_confirmed = $calculated_token;
                                    $this->SetLastClearOtpValue($original_input);
                                    if ('' != $this->GetChapPassword())
                                    {
                                        $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                                    }
                                    elseif ('' != $this->GetMsChapResponse())
                                    {
                                        $code_confirmed = strtolower($this->CalculateMsChapPassword($code_confirmed));
                                    }
                                    elseif ('' != $this->GetMsChap2Response())
                                    {
                                        $code_confirmed = strtolower($this->CalculateMsChap2Password($user, $code_confirmed));
                                    }
                                }

                                if ($input_to_check == $code_confirmed)
                                {
                                    if (($now_steps+$additional_step+$delta_step) > $last_login_step)
                                    {
                                        $this->SetTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                        $this->SetTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                        $this->SetTokenErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                    }
                                    else
                                    {
                                        $result = 26; // ERROR: this token has already been used
                                    }
                                }
                                else
                                {
                                    $check_step++;
                                }
                            }
                            while (($check_step < $max_steps) && (90 <= $result));
                            break;
                        default:
                            $result = 23; // ERROR: Invalid algorithm
                    }

                    if (90 <= $result)
                    {
                        if ($this->GetVerboseFlag())
                        {
                            if ((strlen($input_to_check) == strlen($calculated_token)))
                            {
                                $this->WriteLog("*(authentication typed by the user: ".$input_to_check.")", FALSE, FALSE, $result, 'User', $user);
                            }
                            else
                            {
                                $result = 98;
                                $this->WriteLog("*(authentication typed by the user is ".strlen($input_to_check)." chars long instead of ".strlen($calculated_token)." chars", FALSE, FALSE, $result, 'User', $user);
                            }
                        }
                    }
                    
                    if (0 == $result)
                    {
                        $this->AddTokenAttributedUsers($user);
                        if (!$this->WriteTokenData())
                        {
                            $result = 28; // ERROR: Unable to write the changes in the file
                            $this->WriteLog("Error: Unable to write the changes in the file for the token ".$this->GetToken(), FALSE, FALSE, $result, 'Token', $user);
                        }
                        else
                        {
                            $this->SetUserTokenSerialNumber($serial_number);
                            $this->SetUserAlgorithm($this->GetTokenAlgorithm());
                            $this->SetUserTokenAlgoSuite($this->GetTokenAlgoSuite());
                            $this->SetUserTokenSeed($this->GetTokenSeed());
                            $this->SetUserTokenNumberOfDigits($this->GetTokenNumberOfDigits());
                            $this->SetUserTokenTimeInterval($this->GetTokenTimeInterval());
                            $this->SetUserTokenLastEvent($this->GetTokenLastEvent());
                            $this->SetUserTokenDeltaTime($this->GetTokenDeltaTime());
                            $this->SetUserTokenLastLogin($this->GetTokenLastLogin());
                            $this->SetUserErrorCounter(0);
                            if (!$this->WriteUserData())
                            {
                                $result = 28; // ERROR: Unable to write the changes in the file
                                $this->WriteLog("Error: Unable to write the changes in the file for the user ".$this->GetUser(), FALSE, FALSE, $result, 'System', '');
                            }
                            else
                            {
                                $this->WriteLog("OK: token ".$this->GetToken()." successfully attributed to user ".$this->GetUser(), FALSE, FALSE, 19, 'User');
                            }
                        }
                    }
                }
            }
            else
            {
                $result = 29; // ERROR: Token doesn't exist
                $this->WriteLog("Error: Token ".$this->GetToken()." does not exist", FALSE, FALSE, $result, 'Token');
            }
        }
        else
        {
            $result = 29; // ERROR: User doesn't exist
            $this->WriteLog("Error: User ".$this->GetUser()." does not exist", FALSE, FALSE, $result, 'User');
        }
        return $result;
    } // End of SelfRegisterHardwareToken


    function ImportTokensFile($file, $original_name = '', $cipher_password = '', $key_mac = "")
    {
        if (!file_exists($file))
        {
            $result = FALSE;
        }
        else
        {
            $data1000 = @file_get_contents($file, FALSE, NULL, -1, 1000);
            $file_name = ('' != $original_name)?$original_name:$file;
            if (FALSE !== strpos(strtolower($data1000), strtolower('"urn:ietf:params:xml:ns:keyprov:pskc"')))
            {
                $result = $this->ImportTokensFromPskc($file, $cipher_password, $key_mac);
            }
            elseif (FALSE !== strpos(strtolower($data1000), strtolower('LOGGING START')))
            {
                $result = $this->ImportYubikeyTraditional($file);
            }
            elseif ((FALSE !== strpos(strtolower($data1000), strtolower('AUTHENEXDB'))) && ('.sql' == strtolower(substr($file_name, -4))))
            {
                $result = $this->ImportTokensFromAuthenexSql($file);
            }
            elseif ((FALSE !== strpos(strtolower($data1000), strtolower('SafeWord Authenticator Records'))) && ('.dat' == strtolower(substr($file_name, -4))))
            {
                $result = $this->ImportTokensFromAlpineDat($file);
            }
            elseif (FALSE !== strpos(strtolower($data1000), strtolower('<ProductName>eTPass')))
            // elseif (('.xml' == strtolower(substr($file_name, -4))) && (FALSE !== strpos(strtolower($file_name), 'alpine')))
            {
                $result = $this->ImportTokensFromAlpineXml($file);
            }
            elseif ('.xml' == strtolower(substr($file_name, -4)))
            {
                $result = $this->ImportTokensFromXml($file);
            }
            else
            {
                $result = $this->ImportTokensFromCsv($file);
            }
        }
        return $result;
    }


    function DecodeCipherValue($encrypted_tree, $cipher_array, $integer_value = FALSE)
    {
        $Secret = '';
        $cipher_aes = new Crypt_AES();

        $encryption_method_tag = (isset($encrypted_tree->{$cipher_array['xenc_ns'].'encryptionmethod'})?$cipher_array['xenc_ns']:'').'encryptionmethod';
        $encryption_method_algorithm_url = isset($encrypted_tree->{$encryption_method_tag}[0]->tagAttrs["algorithm"])?($encrypted_tree->{$encryption_method_tag}[0]->tagAttrs["algorithm"]):'';
        $encryption_method_algorithm = (FALSE !== strpos($encryption_method_algorithm_url,'#aes128-cbc'))?'aes128':((FALSE !== strpos($encryption_method_algorithm_url,'#kw-aes128'))?'kw-ases128':'');
        $cipher_data_tag = (isset($encrypted_tree->{$cipher_array['xenc_ns'].'cipherdata'})?$cipher_array['xenc_ns']:'').'cipherdata';
        $cipher_value_tag = (isset($encrypted_tree->{$cipher_data_tag}[0]->{$cipher_array['xenc_ns'].'ciphervalue'})?$cipher_array['xenc_ns']:'').'ciphervalue';
        $cipher_value = isset($encrypted_tree->{$cipher_data_tag}[0]->{$cipher_value_tag}[0]->tagData)?($encrypted_tree->{$cipher_data_tag}[0]->{$cipher_value_tag}[0]->tagData):'';

        if ('' != $cipher_array['Password'])
        {
            if ('' == $cipher_array['KeyDerivationMethodAlgorithm'])
            {
                $cipher_aes->setKey($cipher_array['Password']);
                $Secret = (substr($cipher_aes->decrypt(base64_decode($cipher_value)),16));
                if ('' == $Secret)
                {
                    $cipher_aes->setKey(hex2bin(preg_replace("/[^A-Fa-f0-9]/", '', $cipher_array['Password'])));
                    $Secret = (substr($cipher_aes->decrypt(base64_decode($cipher_value)),16));
                }
            }
            elseif ('pkcs5' == $cipher_array['KeyDerivationMethodAlgorithm'])
            {
                $cipher_aes->setPassword($cipher_array['Password'], 'pbkdf2', 'sha1', $cipher_array['Salt'], $cipher_array['IterationCount'], $cipher_array['KeyLength']);
                $Secret = (substr($cipher_aes->decrypt(base64_decode($cipher_value)),16));
            }
        }
        if (('' != $Secret) && ($integer_value))
        {
            $value = 0;
            for( $i = 0; $i < strlen($Secret); $i++ )
            {
                $value = ($value << 8) | ord($Secret[$i]);
            }
            $Secret = $value;
        }
        return $Secret;
    }

    
    function ImportTokensFromPskc($pskc_file, $cipher_password = '', $keymac = '')
    {
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($pskc_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$pskc_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            //Get the XML document loaded into a variable
            $sXmlData = @file_get_contents($pskc_file);
                    
            //Set up the parser object
            $xml = new MultiotpXmlParser($sXmlData, TRUE);

            //Parse it !
            $xml->Parse();

            $ds_ns = '';
            $pkcs5_ns = '';
            $pskc_ns = '';
            $xenc_ns = '';
            $xenc11_ns = '';

            if (isset($xml->document))
            {
                $keycontainer = $xml->document;
                reset($keycontainer->tagAttrs);
                while(list($attribute_key, $attribute_value) = each($keycontainer->tagAttrs))
                {
                    if ('http://www.w3.org/2000/09/xmldsig#' == $attribute_value)
                    {
                        $ds_ns = substr($attribute_key,strpos($attribute_key,':')+1);
                        $ds_ns.= ('' != $ds_ns)?':':'';
                    }
                    if ('http://www.rsasecurity.com/rsalabs/pkcs/schemas/pkcs-5v2-0#' == $attribute_value)
                    {
                        $pkcs5_ns = substr($attribute_key,strpos($attribute_key,':')+1);
                        $pkcs5_ns.= ('' != $pkcs5_ns)?'_':'';
                    }
                    if ('urn:ietf:params:xml:ns:keyprov:pskc' == $attribute_value)
                    {
                        $pskc_ns = substr($attribute_key,strpos($attribute_key,':')+1);
                        $pskc_ns.= ('' != $pskc_ns)?'_':'';
                    }
                    if ('http://www.w3.org/2001/04/xmlenc#' == $attribute_value)
                    {
                        $xenc_ns = substr($attribute_key,strpos($attribute_key,':')+1);
                        $xenc_ns.= ('' != $xenc_ns)?'_':'';
                    }
                    if ('http://www.w3.org/2009/xmlenc11#' == $attribute_value)
                    {
                        $xenc11_ns = substr($attribute_key,strpos($attribute_key,':')+1);
                        $xenc11_ns.= ('' != $xenc11_ns)?'_':'';
                    }
                }

                $CipherArray = array();
                $CipherArray['Password'] = $cipher_password;
                $CipherArray['xenc_ns'] = $xenc_ns;

                $EncryptionKey_tag = (isset($keycontainer->{$pskc_ns.'encryptionkey'})?$pskc_ns:'').'encryptionkey';
                $DerivedKey_tag = (isset($keycontainer->{$EncryptionKey_tag}[0]->{$xenc11_ns.'derivedkey'})?$xenc11_ns:'').'derivedkey';
                $KeyDerivationMethod_tag = (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$xenc11_ns.'keyderivationmethod'})?$xenc11_ns:'').'keyderivationmethod';
                $KeyDerivationMethodAlgorithmUrl = isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->tagAttrs["algorithm"])?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->tagAttrs["algorithm"]):'';
                $CipherArray['KeyDerivationMethodAlgorithm'] = ((FALSE !== strpos($KeyDerivationMethodAlgorithmUrl,'#pbkdf2'))?'pkcs5':'');
                // http://www.rsasecurity.com/rsalabs/pkcs/schemas/pkcs-5v2-0#pbkdf2
                // http://www.rsasecurity.com/rsalabs/pkcs/schemas/pkcs-5#pbkdf2
                $CipherArray['Salt'] = '';
                $CipherArray['IterationCount'] = 0;
                $CipherArray['KeyLength'] = 0;
                if ('pkcs5' == $CipherArray['KeyDerivationMethodAlgorithm'])
                {
                    $pkcs5_PBKDF2_params_tag = (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_ns.'pbkdf2_params'})?$pkcs5_ns:'').'pbkdf2_params';
                    $pkcs5_Salt_tag = 'salt';
                    $pkcs5_Salt_Specified_tag = 'specified';
                    $CipherArray['Salt'] = base64_decode(isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_PBKDF2_params_tag}[0]->{$pkcs5_Salt_tag}[0]->{$pkcs5_Salt_Specified_tag}[0]->tagData)?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_PBKDF2_params_tag}[0]->{$pkcs5_Salt_tag}[0]->{$pkcs5_Salt_Specified_tag}[0]->tagData):'');
                    $pkcs5_IterationCount_tag = 'iterationcount';
                    $CipherArray['IterationCount'] = intval(isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_PBKDF2_params_tag}[0]->{$pkcs5_IterationCount_tag}[0]->tagData)?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_PBKDF2_params_tag}[0]->{$pkcs5_IterationCount_tag}[0]->tagData):0);
                    $pkcs5_KeyLength_tag = 'keylength';
                    $CipherArray['KeyLength'] = intval(isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_PBKDF2_params_tag}[0]->{$pkcs5_KeyLength_tag}[0]->tagData)?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_PBKDF2_params_tag}[0]->{$pkcs5_KeyLength_tag}[0]->tagData):0);
                }
                
                $KeyPackage_tag = (isset($keycontainer->{$pskc_ns.'keypackage'})?$pskc_ns:'').'keypackage';
                // Extract each key
                // foreach($keycontainer[0][$KeyPackage_tag] as $keypackage) // this is not working well in PHP4
                reset($keycontainer->{$KeyPackage_tag});
                while(list($keypackage_key, $keypackage) = each($keycontainer->{$KeyPackage_tag}))
                {
                    $DeviceInfo_tag = (isset($keypackage->{$pskc_ns.'deviceinfo'})?$pskc_ns:'').'deviceinfo';
                    
                    $Manufacturer_tag = (isset($keypackage->{$pskc_ns.'deviceinfo'}[0]->{$pskc_ns.'manufacturer'})?$pskc_ns:'').'manufacturer';
                    $Manufacturer = (isset($keypackage->{$pskc_ns.'deviceinfo'}[0]->{$Manufacturer_tag}[0]->tagData)?($keypackage->{$pskc_ns.'deviceinfo'}[0]->{$Manufacturer_tag}[0]->tagData):'');
                    
                    $SerialNo_tag = (isset($keypackage->{$DeviceInfo_tag}[0]->{$pskc_ns.'serialno'})?$pskc_ns:'').'serialno';
                    $SerialNo = (isset($keypackage->{$DeviceInfo_tag}[0]->{$SerialNo_tag}[0]->tagData)?($keypackage->{$DeviceInfo_tag}[0]->{$SerialNo_tag}[0]->tagData):'');

                    $CryptoModuleInfo_tag = (isset($keypackage->{$pskc_ns.'cryptomoduleinfo'})?$pskc_ns:'').'cryptomoduleinfo';
                    
                    $CryptoId_tag = (isset($keypackage->{$CryptoModuleInfo_tag}[0]->{$pskc_ns.'id'})?$pskc_ns:'').'id';
                    $CryptoId = (isset($keypackage->{$CryptoModuleInfo_tag}[0]->{$CryptoId_tag}[0]->tagData)?($keypackage->{$CryptoModuleInfo_tag}[0]->{$CryptoId_tag}[0]->tagData):'');

                    $Key_tag = (isset($keypackage->{$pskc_ns.'key'})?$pskc_ns:'').'key';
                    
                    $AlgorithmUrl = isset($keypackage->{$Key_tag}[0]->tagAttrs["algorithm"])?($keypackage->{$Key_tag}[0]->tagAttrs["algorithm"]):'';
                    $Algorithm = (FALSE !== strpos($AlgorithmUrl,'hotp'))?'hotp':((FALSE !== strpos($AlgorithmUrl,'totp'))?'totp':'');
                    // $Algorithm = (FALSE !== strpos($AlgorithmUrl,'hotp'))?'hotp':((FALSE !== strpos($AlgorithmUrl,'totp'))?'totp':((FALSE !== strpos($AlgorithmUrl,'ocra'))?'ocra':''));

                    $KeyId = isset($keypackage->{$Key_tag}[0]->tagAttrs["id"])?($keypackage->{$Key_tag}[0]->tagAttrs["id"]):'';
                    
                    $Issuer_tag = (isset($keypackage->{$Key_tag}[0]->{$pskc_ns.'issuer'})?$pskc_ns:'').'issuer';
                    $Issuer = (isset($keypackage->{$Key_tag}[0]->{$Issuer_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Issuer_tag}[0]->tagData):'');
                    
                    $AlgorithmParameters_tag = (isset($keypackage->{$Key_tag}[0]->{$pskc_ns.'algorithmparameters'})?$pskc_ns:'').'algorithmparameters';
                    
                    $Suite_tag = (isset($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$pskc_ns.'suite'})?$pskc_ns:'').'suite';
                    $Suite = (isset($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$Suite_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$Suite_tag}[0]->tagData):'HMAC-SHA1');
                    $ResponseFormat_tag = (isset($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$pskc_ns.'responseformat'})?$pskc_ns:'').'responseformat';
                    $Length = isset($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$ResponseFormat_tag}[0]->tagAttrs["length"])?($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$ResponseFormat_tag}[0]->tagAttrs["length"]):0;
                    $Encoding = isset($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$ResponseFormat_tag}[0]->tagAttrs["encoding"])?($keypackage->{$Key_tag}[0]->{$AlgorithmParameters_tag}[0]->{$ResponseFormat_tag}[0]->tagAttrs["encoding"]):'DECIMAL';
                    
                    $Data_tag = (isset($keypackage->{$Key_tag}[0]->{$pskc_ns.'data'})?$pskc_ns:'').'data';
                    
                    $Secret_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'secret'})?$pskc_ns:'').'secret';
                    $SecretPlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                    $Secret = base64_decode(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$SecretPlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$SecretPlainValue_tag}[0]->tagData):'');
                    $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                    if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$EncryptedValue_tag}[0]))
                    {
                        $SecretEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$EncryptedValue_tag}[0];
                        $Secret = $this->DecodeCipherValue($SecretEncryptedPath, $CipherArray);
                    }

                    
                    $Counter_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'counter'})?$pskc_ns:'').'counter';
                    $CounterPlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                    $Counter = intval(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$CounterPlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$CounterPlainValue_tag}[0]->tagData):0);
                    $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                    if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$EncryptedValue_tag}[0]))
                    {
                        $CounterEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$EncryptedValue_tag}[0];
                        $Counter = $this->DecodeCipherValue($CounterEncryptedPath, $CipherArray, TRUE);
                    }

                    $Time_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'time'})?$pskc_ns:'').'time';
                    $TimePlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                    $Time = intval(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$TimePlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$TimePlainValue_tag}[0]->tagData):'');
                    $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                    if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$EncryptedValue_tag}[0]))
                    {
                        $TimeEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$EncryptedValue_tag}[0];
                        $Time = $this->DecodeCipherValue($TimeEncryptedPath, $CipherArray, TRUE);
                    }
                    
                    $TimeInterval_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'timeinterval'})?$pskc_ns:'').'timeinterval';
                    $TimeIntervalPlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                    $TimeInterval = intval(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$TimeIntervalPlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$TimeIntervalPlainValue_tag}[0]->tagData):30);
                    $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                    if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$EncryptedValue_tag}[0]))
                    {
                        $TimeIntervalEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$EncryptedValue_tag}[0];
                        $TimeInterval = $this->DecodeCipherValue($TimeIntervalEncryptedPath, $CipherArray, TRUE);
                    }

                    $Policy_tag = (isset($keypackage->{$Key_tag}[0]->{$pskc_ns.'policy'})?$pskc_ns:'').'policy';
                    $PINPolicy_tag = (isset($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$pskc_ns.'pinpolicy'})?$pskc_ns:'').'pinpolicy';
                    $PINPolicyAttributes = isset($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$PINPolicy_tag}[0]->tagAttrs[0])?($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$PINPolicy_tag}[0]->tagAttrs):'';
                    $SerialNo = (('' == $SerialNo)?$KeyId:$SerialNo);

                    if (('' != $Algorithm) && ('' != $SerialNo) && ('' != $Secret))
                    {
                        $this->SetToken($SerialNo);
                        $this->SetTokenDescription(trim($Manufacturer.' '.$SerialNo));
                        $this->SetTokenManufacturer($Manufacturer);
                        $this->SetTokenIssuer($Issuer);
                        $this->SetTokenSerialNumber($SerialNo);
                        $this->SetTokenKeyAlgorithm($AlgorithmUrl);
                        $this->SetTokenAlgorithm($Algorithm);
                        $this->SetTokenAlgoSuite($Suite);
                        $this->SetTokenOtp("TRUE");
                        $this->SetTokenFormat($Encoding);
                        $this->SetTokenNumberOfDigits($Length);
                        if ($Counter >= 0)
                        {
                            $this->SetTokenLastEvent($Counter-1);
                        }
                        else
                        {
                            $this->SetTokenLastEvent(0);
                        }
                        $this->SetTokenDeltaTime($Time);
                        $this->SetTokenTimeInterval($TimeInterval);
                        $this->SetTokenSeed(bin2hex($Secret));
                        
                        if ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Info: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            $this->WriteLog("Info: Token with SerialNo ".$SerialNo." successfully imported", FALSE, FALSE, 15, 'Token', '');
                        }
                        if ($this->GetVerboseFlag())
                        {
                            $full_token_data = '';
                            reset($this->_token_data);
                            while(list($key, $value) = each($this->_token_data))
                            {
                                if ('' != $value)
                                {
                                    $full_token_data = $full_token_data."  Token ".$SerialNo." - ".$key.": ".$value."\n";
                                }
                            }
                            $this->WriteLog("Debug: *".$full_token_data, FALSE, FALSE, 8888, 'Debug', '');
                        }
                    }
                    else
                    {
                        $result = FALSE;
                        $this->WriteLog("Info: Token with SerialNo ".$SerialNo." failed during importation", FALSE, FALSE, 32, 'Token', '');
                    }
                }
            }
        }
        return $result;
    }


    function ImportYubikeyTraditional($yubikey_file)
    /*
     * YubiKey traditional format log file (csv)
     *  0 eventType: "Yubico OTP"|"OATH-HOTP"|"Static Password: Scan Code"|"Static Password"|"Challenge-Response: Yubico OTP"|"Challenge-Response: HMAC-SHA1"
     *  1 timestampLocal: "dd.mm.yyyy hh:ii"
     *  2 configSlot: 1|2
     *  3 pubIdTxt: cbdefghijkln
     *  4 pvtIdTxt: 1234567890ab
     *  5 secretKeyTxt: 1234567890abcdef1234567890abcdef
     *  6 currentAccessCodeTxt: ""|"xxxxxxxxxxxx"
     *  7 newAccessCodeTxt: ""|"xxxxxxxxxxxx"
     *  8 oathFixedModhex1: 0|1 (First byte in fixed part sent as modhex, OATH only)
     *  9 oathFixedModhex2: 0|1 (First two bytes in fixed part sent as modhex, OATH only)
     * 10 oathFixedModhex: 0|1 (Fixed part sent as modhex, OATH only)
     * 11 hotpDigits: 0|6|8
     * 12 oathMovingFactorSeed: 0|nnnn
     * 13 strongPw1: 0|1 (Static Password - Upper and lower case)
     * 14 strongPw2: 0|1 (Static Password - Alphanumeric)
     * 15 sendRef: 0|1 (Static Password - Send ! as prefix)
     * 16 chalBtnTrig: 0|1 (Challenge-Response - challenge requires button trigger)
     * 17 hmacLT64 1|0 (Challenge-Response: HMAC-SHA1 - 1: variable input, 0: fixed 64 byte input)
    */
    {
        $result = TRUE;
        $imported_tokens = 0;
        $this->ResetTokenArray();
        $this->ResetLastImportedTokensArray();
        if (!file_exists($yubikey_file))
        {
            $this->WriteLog("Error: YubiKeys log file ".$yubikey_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            //Get the document loaded into a variable
            $file_handler = fopen($yubikey_file, "rt");

            $yubikey_class = new MultiotpYubikey();
            
            while (!feof($file_handler))
            {
                $line = trim(fgets($file_handler));

                $line = str_replace(';',"\t", $line);
                $line = str_replace(',',"\t", $line);

                $line_array = explode("\t", $line);

                if (count($line_array) >= 18)
                {
                    $token_algo_suite = 'AES-128';
                    $manufacturer = "Yubico";
                    switch (trim($line_array[0]))
                    {
                        case 'Yubico OTP':
                            $algorithm = 'yubicootp';
                            break;
                        case 'OATH-HOTP':
                            $algorithm = 'hotp';
                            break;
                        default:
                            $algorithm = "";
                    }
                    $esn = trim($line_array[3]); // modhex
                    if (('hotp' == $algorithm) && (0 == intval($line_array[10])))
                    {
                        if (1 == intval($line_array[8]))
                        {
                            $esn = substr(trim($line_array[3]),0,2).$yubikey_class->ModHexToHex(substr(trim($line_array[3]),2));
                        }
                        elseif (1 == intval($line_array[9]))
                        {
                            $esn = substr(trim($line_array[3]),0,4).$yubikey_class->ModHexToHex(substr(trim($line_array[3]),4));
                        }
                        else
                        {
                            $esn = $yubikey_class->ModHexToHex(trim($line_array[3]));
                        }
                    }
                    $seed = trim($line_array[5]);
                    $interval_or_event = intval($line_array[12]);

                    if ('hotp' == $algorithm)
                    {
                        $digits = intval($line_array[11]);
                        $next_event = $interval_or_event;
                        $time_interval = 0;
                    }
                    elseif ("yubicootp" == $algorithm)
                    {
                        $digits = 32;
                        $next_event = 0;
                        $time_interval = 0;
                    }
                    
                    if ('' != $algorithm)
                    {
                        $this->SetToken($esn);
                        $this->SetTokenDescription(trim($manufacturer.' '.$esn));
                        $this->SetTokenManufacturer($manufacturer);
                        $this->SetTokenSerialNumber($esn);
                        $this->SetTokenSeed($seed);
                        $this->SetTokenAlgorithm($algorithm);
                        $this->SetUserTokenAlgoSuite($token_algo_suite);
                        $this->SetTokenNumberOfDigits($digits);
                        $this->SetTokenLastEvent($next_event - 1);
                        $this->SetTokenTimeInterval($time_interval);

                        $imported_tokens++;
                        
                        if ('' == $esn)
                        {
                            $this->WriteLog("Error: A token doesn't have any serial number", FALSE, FALSE, 32, 'Token', '');
                        }
                        elseif (!$this->IsValidAlgorithm($algorithm))
                        {
                            $this->WriteLog("Error: The algorithm ".$algorithm." is not recognized", FALSE, FALSE, 32, 'Token', '');
                        }
                        elseif ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Info: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported", FALSE, FALSE, 15, 'Token', '');
                        }
                        $this->ResetTokenArray();
                    }
                }
            }
            fclose($file_handler);
        }
        if (0 == $imported_tokens)
        {
            $result = FALSE;
        }
        return $result;
    }


    function ImportTokensFromCsv($csv_file)
    {
        $result = TRUE;
        $imported_tokens = 0;
        $this->ResetTokenArray();
        $this->ResetLastImportedTokensArray();
        if (!file_exists($csv_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$csv_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            //Get the document loaded into a variable
            $file_handler = fopen($csv_file, "rt");
            
            while (!feof($file_handler))
            {
                $line = trim(fgets($file_handler));

                $line = str_replace(';',"\t", $line);
                $line = str_replace(',',"\t", $line);

                $line_array = explode("\t", $line);

                if (count($line_array) >= 6)
                {
                    $esn               = trim($line_array[0]);
                    $manufacturer      = $line_array[1];
                    $algorithm         = strtolower($line_array[2]);
                    $seed              = $line_array[3];
                    $digits            = $line_array[4];
                    $interval_or_event = intval($line_array[5]);
                    
                    if ('hotp' == $algorithm)
                    {
                        $next_event = $interval_or_event;
                        $time_interval = 0;
                    }
                    else
                    {
                        $next_event = 0;
                        $time_interval = $interval_or_event;
                        if ("motp" == $algorithm)
                        {
                            $time_interval = 10;
                        }
                    }

                    $this->SetToken($esn);
                    $this->SetTokenDescription(trim($manufacturer.' '.$esn));
                    $this->SetTokenManufacturer($manufacturer);
                    $this->SetTokenSerialNumber($esn);
                    $this->SetTokenSeed($seed);
                    $this->SetTokenAlgorithm($algorithm);
                    $this->SetTokenNumberOfDigits($digits);
                    $this->SetTokenLastEvent($next_event - 1);
                    $this->SetTokenTimeInterval($time_interval);

                    $imported_tokens++;
                    
                    if ('' == $esn)
                    {
                        $this->WriteLog("Error: A token doesn't have any serial number", FALSE, FALSE, 32, 'Token', '');
                    }
                    elseif (!$this->IsValidAlgorithm($algorithm))
                    {
                        $this->WriteLog("Error: The algorithm ".$algorithm." is not recognized", FALSE, FALSE, 32, 'Token', '');
                    }
                    elseif ($this->CheckTokenExists())
                    {
                        $this->WriteLog("Info: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                    }
                    else
                    {
                        $result = $this->WriteTokenData() && $result;
                        $this->AddLastImportedToken($this->GetToken());
                        $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported", FALSE, FALSE, 15, 'Token', '');
                    }
                    $this->ResetTokenArray();
                }
            }
            fclose($file_handler);
        }
        if (0 == $imported_tokens)
        {
            $result = FALSE;
        }
        return $result;
    }


    function ImportTokensFromXml($xml_file)
    {
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($xml_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$xml_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            // http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00
            
            //Get the XML document loaded into a variable
            $sXmlData = @file_get_contents($xml_file);

            //Set up the parser object
            $xml = new MultiotpXmlParser($sXmlData);

            //Parse it !
            $xml->Parse();

            // Array of key types
            $key_types = array();
            
            if (isset($xml->document->keyproperties))
            {
                foreach ($xml->document->keyproperties as $keyproperty)
                {
                    $id = (isset($keyproperty->tagAttrs['xml:id'])?$keyproperty->tagAttrs['xml:id']:'');
                    
                    if ('' != $id)
                    {
                        $key_types[$id]['id'] = $id;
                        $key_types[$id]['issuer'] = (isset($keyproperty->issuer[0]->tagData)?$keyproperty->issuer[0]->tagData:'');
                        $key_types[$id]['keyalgorithm'] = (isset($keyproperty->tagAttrs['keyalgorithm'])?$keyproperty->tagAttrs['keyalgorithm']:'');
                        $pos = strrpos($key_types[$id]['keyalgorithm'], "#");
                        $key_types[$id]['algorithm'] = (($pos === false)?'':strtolower(substr($key_types[$id]['keyalgorithm'], $pos+1)));
                        $key_types[$id]['otp'] = (isset($keyproperty->usage[0]->tagAttrs['otp'])?$keyproperty->usage[0]->tagAttrs['otp']:'');
                        $key_types[$id]['format'] = (isset($keyproperty->usage[0]->responseformat[0]->tagAttrs['format'])?$keyproperty->usage[0]->responseformat[0]->tagAttrs['format']:'');
                        $key_types[$id]['length'] = (isset($keyproperty->usage[0]->responseformat[0]->tagAttrs['length'])?$keyproperty->usage[0]->responseformat[0]->tagAttrs['length']:-1);
                        $key_types[$id]['counter'] = (isset($keyproperty->data[0]->counter[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->counter[0]->plainvalue[0]->tagData:-1);
                        $key_types[$id]['time'] = (isset($keyproperty->data[0]->time[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->time[0]->plainvalue[0]->tagData:-1);
                        $key_types[$id]['timeinterval'] = (isset($keyproperty->data[0]->timeinterval[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->timeinterval[0]->plainvalue[0]->tagData:-1);
                        $key_types[$id]['suite'] = (isset($keyproperty->data[0]->suite[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->suite[0]->plainvalue[0]->tagData:'');
                    }
                }
            }
            
            if (isset($xml->document->device))
            {
                foreach ($xml->document->device as $device)
                {
                    $keyid = (isset($device->key[0]->tagAttrs['keyid'])?$device->key[0]->tagAttrs['keyid']:'');
                    if ('' != $keyid)
                    {
                        $this->ResetTokenArray();                        
                        $keyproperties = '';
                        $manufacturer = '';
                        $serialno = '';
                        $issuer = '';
                        $keyalgorithm = '';
                        $algorithm = '';
                        $otp = '';
                        $format = '';
                        $length = 0;
                        $counter = -1;
                        $time = 0;
                        $timeinterval = 0;
                        $secret = '';
                        $suite = '';
                        
                        if (isset($device->key[0]->tagAttrs['keyproperties']))
                        {
                            $keyproperties = $device->key[0]->tagAttrs['keyproperties'];
                            if (isset($key_types[$keyproperties]))
                            {
                                reset($key_types[$keyproperties]);
                                while(list($key, $value) = each($key_types[$keyproperties]))
                                {
                                    $$key = $value;
                                }
                            }
                        }
                        
                        $manufacturer = (isset($device->deviceinfo[0]->manufacturer[0]->tagData)?$device->deviceinfo[0]->manufacturer[0]->tagData:$manufacturer);
                        $serialno = (isset($device->deviceinfo[0]->serialno[0]->tagData)?$device->deviceinfo[0]->serialno[0]->tagData:$serialno);

                        $issuer = (isset($device->key[0]->issuer[0]->tagData)?$device->key[0]->issuer[0]->tagData:$issuer);
                        
                        if (isset($device->key[0]->tagAttrs['keyalgorithm']))
                        {
                            $keyalgorithm = $device->key[0]->tagAttrs['keyalgorithm'];
                            $pos = strrpos($keyalgorithm, "#");
                            $algorithm = (($pos === false)?$algorithm:strtolower(substr($keyalgorithm, $pos+1)));
                        }
                        
                        $otp = (isset($device->key[0]->usage[0]->tagAttrs['otp'])?$device->key[0]->usage[0]->tagAttrs['otp']:$otp);
                        $format = (isset($device->key[0]->usage[0]->responseformat[0]->tagAttrs['format'])?$device->key[0]->usage[0]->responseformat[0]->tagAttrs['format']:$format);
                        $length = (isset($device->key[0]->usage[0]->responseformat[0]->tagAttrs['length'])?$device->key[0]->usage[0]->responseformat[0]->tagAttrs['length']:$length);
                        $counter = (isset($device->key[0]->data[0]->counter[0])?$device->key[0]->data[0]->counter[0]->plainvalue[0]->tagData:$counter);
                        $time = (isset($device->key[0]->data[0]->time[0])?$device->key[0]->data[0]->time[0]->plainvalue[0]->tagData:$time);
                        $timeinterval = (isset($device->key[0]->data[0]->timeinterval[0])?$device->key[0]->data[0]->timeinterval[0]->plainvalue[0]->tagData:$timeinterval);
                        $suite = (isset($device->key[0]->data[0]->suite[0])?$device->key[0]->data[0]->suite[0]->plainvalue[0]->tagData:$suite);
                        
                        if (isset($device->key[0]->data[0]->secret[0]->plainvalue[0]->tagData))
                        {
                            $secret = bin2hex(base64_decode($device->key[0]->data[0]->secret[0]->plainvalue[0]->tagData));
                        }

                        if ('' == trim($serialno))
                        {
                            $serialno = trim($keyid);
                        }
                        $this->SetToken($serialno);
                        $this->SetTokenDescription(trim($manufacturer.' '.$keyid));
                        $this->SetTokenManufacturer($manufacturer);
                        $this->SetTokenIssuer($issuer);
                        $this->SetTokenSerialNumber($serialno);
                        $this->SetTokenKeyAlgorithm($keyalgorithm);
                        $this->SetTokenAlgorithm($algorithm);
                        $this->SetTokenAlgoSuite($suite);
                        $this->SetTokenOtp($otp);
                        $this->SetTokenFormat($format);
                        $this->SetTokenNumberOfDigits($length);
                        if ($counter >= 0)
                        {
                            $this->SetTokenLastEvent($counter-1);
                        }
                        else
                        {
                            $this->SetTokenLastEvent(0);
                        }
                        $this->SetTokenDeltaTime($time);
                        $this->SetTokenTimeInterval($timeinterval);
                        $this->SetTokenSeed($secret);
                        
                        if ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            $this->WriteLog("Info: Token with keyid ".$keyid." successfully imported", FALSE, FALSE, 15, 'Token', '');
                        }
                        if ($this->GetVerboseFlag())
                        {
                            $full_token_data = '';
                            reset($this->_token_data);
                            while(list($key, $value) = each($this->_token_data))
                            {
                                if ('' != $value)
                                {
                                    $full_token_data = $full_token_data."  Token ".$keyid." - ".$key.": ".$value."\n";
                                }
                            }
                            $this->WriteLog("Debug: *".$full_token_data, FALSE, FALSE, 8888, 'Debug', '');
                        }
                    }
                }
            }
        }
        return $result;
    }    


    function ImportTokensFromAlpineXml($xml_file)
    {
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($xml_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$xml_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            $sXmlData = @file_get_contents($xml_file);

            //Set up the parser object
            $xml = new MultiotpXmlParser($sXmlData);

            //Parse it !
            $xml->Parse();

            // Array of key types
            $key_types = array();
            if (isset($xml->document->token))
            {
                foreach ($xml->document->token as $token)
                {
                    $serial = (isset($token->tagAttrs['serial'])?$token->tagAttrs['serial']:'');
                    if ('' != $serial)
                    {
                        $this->ResetTokenArray();                        
                        $manufacturer = 'SafeWord';
                        $serialno = $serial;
                        $issuer = 'SafeWord';
                        $algorithm = 'HOTP';
                        $length = 6;
                        $counter = 0;
                        $time = 0;
                        $timeinterval = 0;
                        $secret = '';
                        
                        if (isset($token->applications[0]->application[0]->seed[0]->tagData))
                        {
                            $secret = $token->applications[0]->application[0]->seed[0]->tagData;
                        }
                        $this->SetToken($serialno);
                        $this->SetTokenDescription(trim($manufacturer.' '.$serialno));
                        $this->SetTokenManufacturer($manufacturer);
                        $this->SetTokenSerialNumber($serialno);
                        $this->SetTokenIssuer($issuer);
                        $this->SetTokenAlgorithm($algorithm);
                        $this->SetTokenNumberOfDigits($length);
                        $this->SetTokenLastEvent($counter-1);
                        $this->SetTokenDeltaTime($time);
                        $this->SetTokenTimeInterval($timeinterval);
                        $this->SetTokenSeed($secret);
                        
                        if ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            
                            $this->WriteLog("Info: Token with serial number ".$serialno." successfully imported", FALSE, FALSE, 15, 'Token', '');
                        }
                    }
                }
            }
        }
        return $result;
    }    


    function ImportTokensFromAlpineDat($data_file)
    {
        $ProductName = "";
        $this->ResetTokenArray();
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($data_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$data_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            // SafeWord Authenticator Records
            
            //Get the document loaded into a variable
            $file_handler = fopen($data_file, "rt");

            $line = trim(fgets($file_handler));
            
            $reference_header       = "SafeWord Authenticator Records";
            $reference_manufacturer = "SafeWord";
            
            if (FALSE !== strpos(strtolower($line), strtolower($reference_header)))
            {
                $manufacturer = $reference_manufacturer;
            
                while (!feof($file_handler))
                {
                    $line = trim(fgets($file_handler));
                    $line_array = explode(":",$line,2);
                    $line_array[0] = trim($line_array[0]);
                    $line_array[1] = trim((isset($line_array[1])?$line_array[1]:''));

                    switch (strtolower($line_array[0]))
                    {
                        case '# ===== safeword authenticator records $version':
                        case 'dn':
                            break;
                        case 'objectclass':
                            break;
                        case 'sccauthenticatorid':
                            $sccAuthenticatorId = $line_array[1];
                            $this->SetToken($sccAuthenticatorId);
                            $this->SetTokenDescription(trim($manufacturer.' '.$sccAuthenticatorId));
                            $this->SetTokenSerialNumber($sccAuthenticatorId);
                            break;
                        case 'scctokentype':
                            $sccTokenType = $line_array[1];
                            break;
                        case 'scctokendata':
                            $sccTokenData = $line_array[1];
                            $data_array = explode(";",$sccTokenData);
                            foreach ($data_array as $data_one)
                            {
                                $attribute_array = explode("=",$data_one,2);
                                $attribute_array[0] = trim($attribute_array[0]);
                                $attribute_array[1] = trim((isset($attribute_array[1])?$attribute_array[1]:''));
                                switch (strtolower($attribute_array[0]))
                                {
                                    case 'scckey':
                                        $sccKey = $attribute_array[1];
                                        $this->SetTokenSeed($sccKey); // 9C29B16121DB61E9D7216CB90016C45677B39009BBF825B5
                                        break;
                                    case 'sccMode':
                                        $sccMode = $attribute_array[1]; // E
                                        break;
                                    case 'sccpwlen':
                                        $sccPwLen = $attribute_array[1]; // 6
                                        $this->SetTokenNumberOfDigits($sccPwLen);
                                        break;
                                    case 'sccver':
                                        $sccVer = $attribute_array[1]; // 00000205
                                        break;
                                    case 'sccseq':
                                        $sccSeq = $attribute_array[1];
                                        $this->SetTokenLastEvent($sccSeq-1); // 0001
                                        break;
                                    case 'casemodel':
                                        $CaseModel = $attribute_array[1]; // 00000005
                                        break;
                                    case 'productiondate':
                                        $ProductionDate = $attribute_array[1]; // 07/28/2010
                                        break;
                                    case 'prtoductname':
                                    case 'productname':
                                        $ProductName = $attribute_array[1]; // eTPass 6.10
                                        break;
                                }
                            }
                            break;
                        case 'sccsignature':
                            if ($this->CheckTokenExists())
                            {
                                $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                            }
                            else
                            {
                                $this->SetTokenManufacturer($manufacturer);
                                $this->SetTokenIssuer($manufacturer);
                                $this->SetTokenAlgorithm('HOTP');
                                $result = $this->WriteTokenData() && $result;
                                $this->AddLastImportedToken($this->GetToken());
                                $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported", FALSE, FALSE, 15, 'Token', '');
                            }
                            $this->ResetTokenArray();
                            break;
                    }
                }
            }
            fclose($file_handler);
        }
        return $result;
    }


    function ImportTokensFromAuthenexSql($data_file)
    {
        $ProductName = "";
        $this->ResetTokenArray();
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($data_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$data_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
            $result = FALSE;
        }
        else
        {
            // Authenex Authenticator Records
            
            //Get the document loaded into a variable
            $file_handler = fopen($data_file, "rt");
            
            $line = trim(fgets($file_handler));
            
            $reference_header       = "AUTHENEXDB";
            $reference_manufacturer = "Authenex";
            
            if (FALSE !== strpos(strtolower($line), strtolower($reference_header)))
            {
                $manufacturer = $reference_manufacturer;
                
                while (!feof($file_handler))
                {
                    $line = trim(fgets($file_handler));

                    if (FALSE !== strpos(strtoupper($line), 'INSERT INTO OTP'))
                    {
                        $token_array = array();
                        $line_array = explode("(",$line,3);
                        $token_line = str_replace(")",",",$line_array[2]);
                        $token_array = explode(",",$token_line);
                        if (isset($token_array[1]))
                        {
                            $esn  = preg_replace('#\W#', '', $token_array[0]);
                            $seed = preg_replace('#\W#', '', $token_array[1]);
                            $this->SetToken($esn);
                            $this->SetTokenDescription(trim($manufacturer.' '.$esn));
                            $this->SetTokenManufacturer($manufacturer);
                            $this->SetTokenSerialNumber($esn);
                            $this->SetTokenSeed($seed);
                            $this->SetTokenAlgorithm('HOTP');
                            $this->SetTokenNumberOfDigits(6);
                            $this->SetTokenLastEvent(-1);
                        }
                        if ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported", FALSE, FALSE, 15, 'Token', '');
                        }
                        $this->ResetTokenArray();
                    }
                }
            }
            fclose($file_handler);
        }
        return $result;
    }


    /****************************
     ****************************
     ****************************
     ***   DEVICES HANDLING   ***
     ****************************
     ****************************
     ****************************/
     
    function CreateDevice($id = 0,
                          $description = '',
                          $device_secret = '',
                          $ip_or_fqdn = '',
                          $subnet = '',
                          $shortname = '',
                          $with_radius_update = TRUE,
                          $challenge_response_enabled = 0,
                          $text_token_challenge = '',
                          $sms_challenge_enabled = 0,
                          $text_sms_challenge = ''
                         )
    {
        $result = FALSE;
        $device_id = $id;
        if ((0 == $device_id) || ('' == $device_id))
        {
            $device_id = bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999));
        }
        if (!$this->ReadDeviceData($device_id, TRUE))
        {
            $this->SetDevice($device_id);
            $this->SetDeviceDescription($description);
            $this->SetDeviceSecret($device_secret);
            $this->SetDeviceIpOrFqdn($ip_or_fqdn);
            $this->SetDeviceSubnet($subnet);
            $this->SetDeviceShortname($shortname);
            $this->SetDeviceChallengeEnabled($challenge_response_enabled);
            $this->SetDeviceTextTokenChallenge($text_token_challenge);
            $this->SetDeviceSmsChallengeEnabled($sms_challenge_enabled);
            $this->SetDeviceTextSmsChallenge($text_sms_challenge);
            $result = $this->WriteDeviceData($with_radius_update);
        }
        return $result;
    }    


    function ReadDeviceData($device_id = '', $create = FALSE)
    {
        if ('' != $device_id)
        {
            $this->SetDevice($device_id);
        }
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_device_data['encryption_hash'] = '';
        
        // First, we read the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $device_filename = strtolower($this->GetDevice()).'.db';
            if (!file_exists($this->GetDevicesFolder().$device_filename))
            {
                if (!$create)
                {
                    $this->WriteLog("Error: database file ".$this->GetDevicesFolder().$device_filename." for device ".$this->_device." does not exist", FALSE, FALSE, 299, 'System', '');
                }
            }
            else
            {
                $file_handler = fopen($this->GetDevicesFolder().$device_filename, "rt");
                $first_line = trim(fgets($file_handler));
                
                while (!feof($file_handler))
                {
                    $line = trim(fgets($file_handler));
                    $line_array = explode("=",$line,2);
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                    }
                    if ('' != trim($line_array[0]))
                    {
                        $this->_device_data[strtolower($line_array[0])] = $line_array[1];
                    }
                }
                
                fclose($file_handler);
                $result = TRUE;

                if ('' != $this->_device_data['encryption_hash'])
                {
                    if ($this->_device_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                    {
                        $this->_device_data['encryption_hash'] = "ERROR";
                        $this->WriteLog("Error: the device information encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                        $result = FALSE;
                    }
                }
            }
        }

        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_devices_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_devices_table']."` WHERE `device_id` = '".$this->_device."'";
                            $aRow = NULL;
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 199, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    $aRow = $rResult->fetch_assoc();
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    $aRow = mysql_fetch_assoc($rResult);
                                }
                            }

                            if (NULL != $aRow)
                            {
                                $result = FALSE;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['devices']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['devices']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if (($in_the_schema) && ($key != 'device_id'))
                                    {
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $this->_device_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                        }
                                        else
                                        {
                                            $this->_device_data[$key] = $value;
                                        }
                                    }                                    
                                    elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: *The key ".$key." is not in the devices database schema", FALSE, FALSE, 98, 'System', '');
                                    }
                                    $result = TRUE;
                                }
                                if(0 == count($aRow) && !$create)
                                {
                                    $this->WriteLog("Error: SQL database entry for device ".$this->_device." does not exist", FALSE, FALSE, 299, 'System', '');
                                }
                            }
                        }
                        if ('' != $this->_device_data['encryption_hash'])
                        {
                            if ($this->_device_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $this->_device_data['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the devices mysql encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }
        return $result;
    }


    function WriteDeviceData(
        $with_radius_update = true // $with_radius_update (for future use)
    ) {
        if ('' == trim($this->GetDevice())) {
            $result = false;
        } else {
            $result = $this->WriteData('Device',
                                       'devices',
                                       $this->GetDevicesFolder(),
                                       $this->_device_data,
                                       false,
                                       'device_id',
                                       $this->GetDevice()
                                      );
        }
        return $result;
    }


    function SetDevice($device)
    {
        $this->ResetDeviceArray();
        $this->_device = strtolower($device);
        $this->ReadDeviceData('', TRUE); // First parameter empty, otherwise it will loop with SetDevice !
    }


    function GetDevice()
    {
        return $this->_device;
    }


    function SetDeviceDescription($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $result = $second_param;
        }
        $this->_device_data['description'] = $result;

        return $result;
    }


    function GetDeviceDescription($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['description'];
    }


    function SetDeviceShortname($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $result = $second_param;
        }
        $this->_device_data['shortname'] = $result;

        return $result;
    }


    function GetDeviceShortname($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['shortname'];
    }


    function SetDeviceIpOrFqdn($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $result = $second_param;
        }
        $this->_device_data['ip_or_fqdn'] = $result;

        return $result;
    }


    function GetDeviceIpOrFqdn($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['ip_or_fqdn'];
    }


    function SetDeviceSubnet($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $result = $second_param;
        }
        $this->_device_data['subnet'] = $result;

        return $result;
    }


    function GetDeviceSubnet($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['subnet'];
    }


    function SetDeviceSecret($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $result = $second_param;
        }
        $this->_device_data['device_secret'] = $result;

        return $result;
    }


    function GetDeviceSecret($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['device_secret'];
    }


    function SetDeviceChallengeEnabled($first_param, $second_param = "*-*")
    {
        $value = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $value = $second_param;
        }
        $this->_device_data['challenge_response_enabled'] = intval($value);

        return $value;
    }


    function GetDeviceChallengeEnabled($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return intval($this->_device_data['challenge_response_enabled']);
    }


    function SetDeviceTextTokenChallenge($first_param, $second_param = "*-*")
    {
        $value = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $value = $second_param;
        }
        $this->_device_data['text_token_challenge'] = $value;

        return $value;
    }


    function GetDeviceTextTokenChallenge($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['text_token_challenge'];
    }


    function SetDeviceSmsChallengeEnabled($first_param, $second_param = "*-*")
    {
        $value = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $value = $second_param;
        }
        $this->_device_data['sms_challenge_enabled'] = intval($value);

        return $value;
    }


    function GetDeviceSmsChallengeEnabled($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return intval($this->_device_data['sms_challenge_enabled']);
    }


    function SetDeviceTextSmsChallenge($first_param, $second_param = "*-*")
    {
        $value = "";
        if ($second_param == "*-*")
        {
            $value = $first_param;
        }
        else
        {
            $this->SetDevice($first_param);
            $value = $second_param;
        }
        $this->_device_data['text_sms_challenge'] = $value;

        return $value;
    }


    function GetDeviceTextSmsChallenge($device = '')
    {
        if($device != '')
        {
            $this->SetDevice($device);
        }
        return $this->_device_data['text_sms_challenge'];
    }


    function DeleteDevice($device = '', $no_error_info = FALSE)
    {
        if ('' != $device)
        {
            $this->SetDevice($device);
        }
        
        $result = FALSE;
        
        // First, we delete the device file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $device_filename = strtolower($this->_device).'.db';
            if (!file_exists($this->GetDevicesFolder().$device_filename))
            {
                if (!$no_error_info)
                {
                    $this->WriteLog("Error: Unable to delete device ".$this->_device.", database file ".$this->GetDevicesFolder().$device_filename." does not exist", FALSE, FALSE, 28, 'System', '');
                }
            }
            else
            {
                $result = unlink($this->GetDevicesFolder().$device_filename);
                if ($result)
                {
                    if ($this->GetVerboseFlag())
                    {
                        $this->WriteLog("Info: *Device ".$this->_device." successfully deleted", FALSE, FALSE, 19, 'Device', '');
                    }
                }
                else
                {
                    if (!$no_error_info)
                    {
                        $this->WriteLog("Error: Unable to delete device ".$this->_device, FALSE, FALSE, 28, 'Device', '');
                    }
                }
            }
        }

        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_devices_table'])
                        {
                            $sQuery  = "DELETE FROM `".$this->_config_data['sql_devices_table']."` WHERE `device_id` = '".$this->_device."'";
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    if (!$no_error_info)
                                    {
                                        $this->WriteLog("Error: Could not delete device ".$this->_device.": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'System', '');
                                    }
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete device ".$this->_device.": ".mysql_error(), FALSE, FALSE, 28, 'System', '');
                                }
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete device ".$this->_device.". Device does not exist", FALSE, FALSE, 28, 'System', '');
                                }
                            }
                            else
                            {
                                if ($this->GetVerboseFlag())
                                {
                                    $this->WriteLog("Info: *Device ".$this->_device." successfully deleted", FALSE, FALSE, 19, 'Device', '');
                                }
                                $result = TRUE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }                        
        }
        return $result;
    }


    function GetDevicesList()
    {
        return $this->GetList('device_id', 'sql_devices_table', $this->GetDevicesFolder());
    }


    /***************************
     ***************************
     ***************************
     ***   GROUPS HANDLING   ***
     ***************************
     ***************************
     ***************************/

    function CreateGroup($id = '', $name = '', $description = '')
    {
        $group_id = $id;
        if (('' == $group_id) || ('0' == $group_id))
        {
            $group_id = bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999));
        }
        if ($this->CheckGroupExists($group_id))
        {
            return FALSE; // ERROR: group already exists.
        }
        else
        {
            $this->SetGroup($group_id);
            $this->SetGroupName($name);
            $this->SetGroupDescription($description);
            return $this->WriteGroupData();
        }
    }    


    function ReadGroupData($group_id = '', $create = FALSE)
    {
        if ('' != $group_id)
        {
            $this->SetGroup($group_id);
        }
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_group_data['encryption_hash'] = '';
        
        // First, we read the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $group_filename = strtolower($this->GetGroup()).'.db';
            if (!file_exists($this->GetGroupsFolder().$group_filename))
            {
                if (!$create)
                {
                    $this->WriteLog("Error: database file ".$this->GetGroupsFolder().$group_filename." for group ".$this->_group." does not exist", FALSE, FALSE, 299, 'System', '');
                }
            }
            else
            {
                $file_handler = fopen($this->GetGroupsFolder().$group_filename, "rt");
                $first_line = trim(fgets($file_handler));
                
                while (!feof($file_handler))
                {
                    $line = trim(fgets($file_handler));
                    $line_array = explode("=",$line,2);
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                    }
                    if ('' != trim($line_array[0]))
                    {
                        $this->_group_data[strtolower($line_array[0])] = $line_array[1];
                    }
                }
                
                fclose($file_handler);
                $result = TRUE;

                if ('' != $this->_group_data['encryption_hash'])
                {
                    if ($this->_group_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                    {
                        $this->_group_data['encryption_hash'] = "ERROR";
                        $this->WriteLog("Error: the group information encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                        $result = FALSE;
                    }
                }
            }
        }

        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_groups_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_groups_table']."` WHERE `group_id` = '".$this->_group."'";
                            $aRow = NULL;
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 199, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    $aRow = $rResult->fetch_assoc();
                                }
                            }
                            else
                            {
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 199, 'System', '');
                                    $result = FALSE;
                                }
                                else
                                {
                                    $aRow = mysql_fetch_assoc($rResult);
                                }
                            }

                            if (NULL != $aRow)
                            {
                                $result = FALSE;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['groups']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['groups']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if (($in_the_schema) && ($key != 'group_id'))
                                    {
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $this->_group_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                        }
                                        else
                                        {
                                            $this->_group_data[$key] = $value;
                                        }
                                    }                                    
                                    elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: *The key ".$key." is not in the groups database schema", FALSE, FALSE, 98, 'System', '');
                                    }
                                    $result = TRUE;
                                }
                                if(0 == count($aRow) && !$create)
                                {
                                    $this->WriteLog("Error: SQL database entry for group ".$this->_group." does not exist", FALSE, FALSE, 299, 'System', '');
                                }
                            }
                        }
                        if ('' != $this->_group_data['encryption_hash'])
                        {
                            if ($this->_group_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $this->_group_data['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the groups mysql encryption key is not matching", FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }
        return $result;
    }


    function WriteGroupData()
    {
        if ('' == trim($this->GetGroup())) {
            $result = false;
        } else {
            $result = $this->WriteData('Group',
                                       'groups',
                                       $this->GetGroupsFolder(),
                                       $this->_group_data,
                                       false,
                                       'group_id',
                                       $this->GetGroup()
                                      );
        }
        return $result;
    }


    function SetGroup($group)
    {
        $this->ResetGroupArray();
        $this->_group = strtolower($group);
        $this->ReadGroupData('', TRUE); // First parameter empty, otherwise it will loop with SetGroup !
    }


    function GetGroup()
    {
        return $this->_group;
    }


    function SetGroupDescription($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetGroup($first_param);
            $result = $second_param;
        }
        $this->_group_data['description'] = $result;

        return $result;
    }


    function SetGroupName($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetGroup($first_param);
            $result = $second_param;
        }
        $this->_group_data['name'] = $result;

        return $result;
    }


    function GetGroupDescription($group = '')
    {
        if($group != '')
        {
            $this->SetGroup($group);
        }
        return $this->_group_data['description'];
    }


    function GetGroupName($group = '')
    {
        if($group != '')
        {
            $this->SetGroup($group);
        }
        return $this->_group_data['name'];
    }


    function DeleteGroup($group = '', $no_error_info = FALSE)
    {
        if ('' != $group)
        {
            $this->SetGroup($group);
        }
        
        $result = FALSE;
        
        // First, we delete the group file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $group_filename = strtolower($this->_group).'.db';
            if (!file_exists($this->GetGroupsFolder().$group_filename))
            {
                if (!$no_error_info)
                {
                    $this->WriteLog("Error: Unable to delete group ".$this->_group.", database file ".$this->GetGroupsFolder().$group_filename." does not exist", FALSE, FALSE, 28, 'System', '');
                }
            }
            else
            {
                $result = unlink($this->GetGroupsFolder().$group_filename);
                if ($result)
                {
                    if ($this->GetVerboseFlag())
                    {
                        $this->WriteLog("Info: *Group ".$this->_group." successfully deleted", FALSE, FALSE, 19, 'Group', '');
                    }
                }
                else
                {
                    if (!$no_error_info)
                    {
                        $this->WriteLog("Error: Unable to delete group ".$this->_group, FALSE, FALSE, 28, 'Group', '');
                    }
                }
            }
        }

        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_groups_table'])
                        {
                            $sQuery  = "DELETE FROM `".$this->_config_data['sql_groups_table']."` WHERE `group_id` = '".$this->_group."'";
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    if (!$no_error_info)
                                    {
                                        $this->WriteLog("Error: Could not delete group ".$this->_group.": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'System', '');
                                    }
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete group ".$this->_group.": ".mysql_error(), FALSE, FALSE, 28, 'System', '');
                                }
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete group ".$this->_group.". Group does not exist", FALSE, FALSE, 28, 'Group', '');
                                }
                            }
                            else
                            {
                                if ($this->GetVerboseFlag())
                                {
                                    $this->WriteLog("Info: *Group ".$this->_group." successfully deleted", FALSE, FALSE, 19, 'Group', '');
                                }
                                $result = TRUE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }                        
        }
        return $result;
    }


    // Check if group exists
    function CheckGroupExists($group = '')
    {
        $check_group = ('' != $group)?$group:$this->GetGroup();
        $result = FALSE;

        if ('' != trim($check_group))
        {
            if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_groups_table'])) || ('files' == $this->GetBackendType()))
            {
                switch ($this->GetBackendType())
                {
                    case 'mysql':
                        if ($this->OpenMysqlDatabase())
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_groups_table']."` WHERE `group_id` = '{$check_group}'";
                            
                            if (is_object($this->_mysqli))
                            {
                                if (!($rResult = $this->_mysqli->query($sQuery)))
                                {
                                    $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                }
                                else
                                {
                                    $num_rows = $this->_mysqli->affected_rows;
                                }
                            }
                            elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                            }
                            else
                            {
                                $num_rows = mysql_affected_rows($this->_mysql_database_link);
                            }
                            
                            if (0 == $num_rows)
                            {
                                $this->WriteLog("Error: Group ".$group.". does not exist", FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                            else
                            {
                                $result = TRUE;
                            }
                        }
                        break;
                    case 'files':
                    default:
                        $group_filename = strtolower($check_group).'.db';
                        $result = file_exists($this->GetGroupsFolder().$group_filename);
                        break;
                }
            }
        }
        return $result;
    }


    function GetGroupsList()
    {
        return $this->GetList('group_id', 'sql_groups_table', $this->GetGroupsFolder());
    }


    function GetList($raw_id, $table_name, $folder)
    {
        $list = '';
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data[$table_name])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery = "SELECT `".$raw_id."` FROM `".$this->_config_data[$table_name]."`";
                        if (is_object($this->_mysqli))
                        {
                            if (!($result = $this->_mysqli->query($sQuery)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 299, 'System', '');
                                $result = FALSE;
                            }
                            else
                            {
                                while ($aRow = $result->fetch_assoc())
                                {
                                    if ('' != $aRow[$raw_id])
                                    {
                                        $list.= (('' != $list)?"\t":'').$aRow[$raw_id];
                                    }
                                }
                            }
                        }
                        else
                        {
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 299, 'System', '');
                            }
                            else
                            {
                                while ($aRow = mysql_fetch_assoc($rResult))
                                {
                                    if ('' != $aRow[$raw_id])
                                    {
                                        $list.= (('' != $list)?"\t":'').$aRow[$raw_id];
                                    }
                                }                         
                            }
                        }
                    }
                    break;
                case 'files':
                default:
                    if ($file_handle = @opendir($folder))
                    {
                        while ($file = readdir($file_handle))
                        {
                            if ((substr($file, -3) == ".db") && ($file != '.db'))
                            {
                                $list.= (('' != $list)?"\t":'').substr($file,0,-3);
                            }
                        }
                        closedir($file_handle);
                    }
            }
        }
        return $list;
    }


    function GetHardwareType()
    {
        $type = "unknown";
        $os_running = php_uname();
        // Is it potentially a nanocomputer (BeagleBone Black or Raspberry Pi 2) ?
        if (FALSE !== strpos(strtolower($os_running), 'armv7l'))
        {
			$hardware = '';
			exec("cat /proc/cpuinfo", $output);
			foreach($output as $line)
			{
				$line = $line."  ";
				if (preg_match("/^Hardware\s*:\s*(.*)/", $line))
				{
					preg_match_all("/^Hardware\s*:\s*(.*)/", $line, $result_array, PREG_SET_ORDER);
					if (isset($result_array[0][1]))
					{
						$hardware = strtoupper(trim($result_array[0][1]));
						break;
					}
				}
			}
			if (FALSE !== strpos(strtolower($os_running), 'bcm27')) {
				$type = 'RP2'; // Raspberry Pi 2 (BCM2709)
			} else {
				$type = 'BBB'; // Beaglebone Black (Generic AM33XX and others)
			}
        }
        // Is it potentially a Raspberry Pi B/B+ ?
        elseif (FALSE !== strpos(strtolower($os_running), 'armv6l'))
        {
            $type = 'RPI';
		}
        // Is it potentially a Windows development platform ?
        elseif (strtolower(substr(PHP_OS, 0, 3)) === 'win')
        {
            $type = "DVP";
        }
        // Is it a virtual appliance and/or a Linux Debian edition
        elseif (FALSE !== strpos(strtolower($os_running), 'debian'))
        {
            $type = 'VAP';
        }
        return $type;
    }


    function GetRaspberryPiSerialNumber()
    {
        $serial = '';
        exec("cat /proc/cpuinfo", $output);
        foreach($output as $line)
        {
            $line = $line."  ";
            if (preg_match("/^Serial\s*:\s*(.*)/", $line))
            {
                preg_match_all("/^Serial\s*:\s*(.*)/", $line, $result_array, PREG_SET_ORDER);
                if (isset($result_array[0][1]))
                {
                    $serial = strtoupper(trim($result_array[0][1]));
                    break;
                }
            }
        }
        return $serial;
    }


    function ReadUserDataOnServer($user)
    {
        $result = 72;

        /* This option is too long
        if (function_exists('openssl_random_pseudo_bytes')) {
            $server_challenge = 'MOSH'.bin2hex(openssl_random_pseudo_bytes(16));
        } else {
        */
            $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().mt_rand(100000,999999));
        /* } */
        $this->SetServerChallenge($server_challenge);

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
<ServerChallenge>*ServerChallenge*</ServerChallenge>
<ReadUserData>
    <UserId>*UserId*</UserId>
</ReadUserData>
</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
        $xml_data = str_replace('*UserId*', $user, $xml_data);
        
        $xml_urls = $this->GetServerUrl();
        $xml_timeout = $this->GetServerTimeout();
        $xml_data_encoded = urlencode($xml_data);
        
        $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

        if (FALSE !== $response)
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Info: Host returned the following answer: $response", FALSE, FALSE, 8888, 'Debug', '');
            }
            
            if (FALSE !== strpos($response,'<multiOTP'))
            {
                $error_code = 99;
                
                //Set up the parser object
                $xml = new MultiotpXmlParser($response);

                //Parse it !
                $xml->Parse();

                if (isset($xml->document->errorcode[0]))
                {
                    $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                    
                    if ($server_password != md5('ReadUserData'.$this->GetServerSecret().$this->GetServerChallenge()))
                    {
                        $error_code = 70;
                    }
                    else
                    {
                        $error_code = (isset($xml->document->errorcode[0])?intval($xml->document->errorcode[0]->tagData):99);
                    }
                    $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->GetErrorText($error_code));

                    if ($this->_xml_dump_in_log)
                    {
                        $this->WriteLog("Info: Host returned the following result: $error_code ($error_description)", FALSE, FALSE, $error_code, 'Debug', '');
                    }
                }
                if ((19 == $error_code) && (isset($xml->document->user[0])))
                {
                    $result = (isset($xml->document->user[0]->userdata[0])?($xml->document->user[0]->userdata[0]->tagData):'');
                }
                else
                {
                    $this->WriteLog("Error: Host answers with the following error code: $error_code ($error_description)", FALSE, FALSE, 299, 'Client-Server', '');
                    $result = $error_code;
                }
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: *Host sent an incorrect answer: $response", FALSE, FALSE, 299, 'Client-Server', '');
                }
            }
        }
        return $result;
    }


    function CheckUserExistsOnServer($user = '')
    {
        $result = 72;
        
        /* This option is too long
        if (function_exists('openssl_random_pseudo_bytes')) {
            $server_challenge = 'MOSH'.bin2hex(openssl_random_pseudo_bytes(16));
        } else {
        */
            $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().mt_rand(100000,999999));
        /* } */
        $this->SetServerChallenge($server_challenge);

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
<ServerChallenge>*ServerChallenge*</ServerChallenge>
<CheckUserExists>
    <UserId>*UserId*</UserId>
</CheckUserExists>
</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
        $xml_data = str_replace('*UserId*', $user, $xml_data);
        
        $xml_urls = $this->GetServerUrl();
        $xml_timeout = $this->GetServerTimeout();
        $xml_data_encoded = urlencode($xml_data);
        
        $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

        if (FALSE !== $response)
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Info: Host returned the following answer: $response", FALSE, FALSE, 8888, 'Debug', '');
            }
            
            if (FALSE !== strpos($response,'<multiOTP'))
            {
                $error_code = 99;
                
                //Set up the parser object
                $xml = new MultiotpXmlParser($response);

                //Parse it !
                $xml->Parse();

                if (isset($xml->document->errorcode[0]))
                {
                    $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                    
                    if ($server_password != md5('CheckUserExists'.$this->GetServerSecret().$this->GetServerChallenge()))
                    {
                        $error_code = 70;
                    }
                    else
                    {
                        $error_code = (isset($xml->document->errorcode[0])?intval($xml->document->errorcode[0]->tagData):99);
                    }
                    $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->GetErrorText($error_code));

                    if ($this->_xml_dump_in_log)
                    {
                        $this->WriteLog("Info: Host returned the following result: $error_code ($error_description).", FALSE, FALSE, $error_code, 'Debug', '');
                    }
                }
                // User doesnt exist: 21 - User exists = 22
                $result = $error_code;
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: *Host sent an incorrect answer: $response", FALSE, FALSE, 8888, 'Client-Server', '');
                }
            }
        }
        return $result;
    }


    function CheckUserTokenOnServer($user, $password, $auth_method="PAP", $id= '', $challenge = '', $response2 = '')
    {
        $result = 72;
        
        /* This option is too long
        if (function_exists('openssl_random_pseudo_bytes')) {
            $server_challenge = 'MOSH'.bin2hex(openssl_random_pseudo_bytes(16));
        } else {
        */
            $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().mt_rand(100000,999999));
        /* } */
        $this->SetServerChallenge($server_challenge);

        switch (strtoupper($auth_method))
        {
            case 'CHAP':
                $chap_id        = $id;
                $chap_challenge = $challenge;
                $chap_password  = $password;
                $chap_hash      = '';
                break;
            case 'MS-CHAP':
                $ms_chap_id        = $id;
                $ms_chap_challenge = $challenge;
                $ms_chap_response  = $password;
                $chap_hash      = '';
                break;
            case ' MS-CHAPV2':
                $ms_chap_id        = $id;
                $ms_chap_challenge = $challenge;
                $ms_chap_response  = $password;
                $ms_chap2_response = $response2;
                $chap_hash      = '';
                break;
            case 'PAP':
            default:
                /*
                $chap_id        = '';
                $chap_challenge = md5(time());
                $chap_password  = $password;
                */
                $chap_id        = bin2hex(chr(mt_rand(0, 255)));
                $chap_challenge = md5(time());
                $chap_password  = $this->CalculateChapPassword($password, $chap_id, $chap_challenge);
                $chap_hash      = $this->Encrypt('ChapHash', $password, $chap_id.$server_challenge.$chap_id);
                break;
        }

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
<ServerChallenge>*ServerChallenge*</ServerChallenge>
<CheckUserToken>
    <UserId>*UserId*</UserId>
    <Chap>
        <ChapId>*ChapId*</ChapId>
        <ChapChallenge>*ChapChallenge*</ChapChallenge>
        <ChapPassword>*ChapPassword*</ChapPassword>
        <ChapHash>*ChapHash*</ChapHash>
    </Chap>
    <CacheLevel>*CacheLevel*</CacheLevel>
</CheckUserToken>
</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
        $xml_data = str_replace('*UserId*', $user, $xml_data);
        $xml_data = str_replace('*ChapId*', $chap_id, $xml_data);
        $xml_data = str_replace('*ChapChallenge*', $chap_challenge, $xml_data);
        $xml_data = str_replace('*ChapPassword*', $chap_password, $xml_data);
        $xml_data = str_replace('*ChapHash*', $chap_hash, $xml_data);
        $xml_data = str_replace('*CacheLevel*', $this->GetServerCacheLevel(), $xml_data);
        
        $xml_urls = $this->GetServerUrl();
        $xml_timeout = $this->GetServerTimeout();
        $xml_data_encoded = urlencode($xml_data);

        // $this->WriteLog("Debug: Host received the following request: $xml_data", FALSE, FALSE, 8888, 'Debug', '');
        
        $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

        if (FALSE !== $response)
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Debug: Host returned the following answer: $response", FALSE, FALSE, 8888, 'Debug', '');
            }

            if (FALSE !== strpos($response,'<multiOTP'))
            {
                $result = 99;
                $error_code = 99;
                
                //Set up the parser object
                $xml = new MultiotpXmlParser($response);

                //Parse it !
                $xml->Parse();

                if (isset($xml->document->errorcode[0]))
                {
                    $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                    
                    if ($server_password != md5('CheckUserToken'.$this->GetServerSecret().$this->GetServerChallenge()))
                    {
                        $error_code = 70;
                    }
                    else
                    {
                        $error_code = (isset($xml->document->errorcode[0])?intval($xml->document->errorcode[0]->tagData):99);
                    }
                    $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->GetErrorText($error_code));
                    $result = $error_code;

                    if ($this->_xml_dump_in_log)
                    {
                        $this->WriteLog("Info: Host returned the following result: $result ($error_description).", FALSE, FALSE, $result, 'Debug', '');
                    }
                }

                if ((0 == $error_code) && (isset($xml->document->cache[0])))
                {
                    if (isset($xml->document->cache[0]->user[0]))
                    {
                        foreach ($xml->document->cache[0]->user as $one_user)
                        {
                            $current_user = isset($one_user->tagAttrs['userid'])?$one_user->tagAttrs['userid']:'';
                            if ('' != $current_user)
                            {
                                $current_user_data = isset($one_user->userdata[0])?$one_user->userdata[0]->tagData:'';
                                if ('' != $current_user_data)
                                {
                                    $this->SetUser($current_user);
                                    $this->_user_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
                                    $current_user_array = explode("\n",$current_user_data);

                                    foreach ($current_user_array as $one_line)
                                    {
                                        $line = trim($one_line);
                                        $line_array = explode("=",$line,2);
                                        if (":" == substr($line_array[0], -1))
                                        {
                                            $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                            $line_array[1] = $this->Decrypt($line_array[0], $line_array[1], $this->GetServerSecret());
                                        }
                                        if ('' != trim($line_array[0]))
                                        {
                                            if ('encryption_hash' != strtolower($line_array[0]))
                                            {
                                                $this->_user_data[strtolower($line_array[0])] = $line_array[1];
                                            }
                                        }
                                    }
                                    $this->WriteUserData();
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: *Host sent an incorrect answer: $response", FALSE, FALSE, 8888, 'Client-Server', '');
                }
            }
        }
        $this->SetUser($user);
        return $result;
    }


    function PostHttpDataXmlRequest($xml_data, $xml_urls, $xml_timeout = 1)
    {
        $result = FALSE;
        $content_to_post = 'data='.$xml_data;
        $xml_url = explode(";",$xml_urls);
        
        foreach ($xml_url as $xml_url_one)
        {
            $server_to_ban = $xml_url_one;
            $skip = FALSE;
            foreach ($this->GetTemporaryBadServer() as $temp_bad_server)
            {
                if ($temp_bad_server == $server_to_ban)
                {
                    $skip = TRUE;
                }
            }
            
            if (!$skip)
            {
                $port = 80;

                $pos = strpos($xml_url_one, '://');
                if (FALSE === $pos)
                {
                    $protocol = '';
                }
                else
                {
                    switch (strtolower(substr($xml_url_one,0,$pos)))
                    {
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
                    
                    $xml_url_one = substr($xml_url_one,$pos+3);
                }
                
                $pos = strpos($xml_url_one, '/');
                if (FALSE === $pos)
                {
                    $host = $xml_url_one;
                    $url = '/';
                }
                else
                {
                    $host = substr($xml_url_one,0,$pos);
                    $url = substr($xml_url_one,$pos); // And not +1 as we want the / at the beginning
                }
                
                $pos = strpos($host, ':');
                if (FALSE !== $pos)
                {
                    $port = substr($host,$pos+1);
                    $host = substr($host,0,$pos);
                }
                
                $errno = 0;
                $errdesc = 0;
                $fp = @fsockopen($protocol.$host, $port, $errno, $errdesc, $xml_timeout);
                if (FALSE !== $fp)
                {
                    $info['timed_out'] = FALSE;
                    fputs($fp, "POST ".$url." HTTP/1.0\r\n");
                    fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
                    fputs($fp, "Content-Length: ".strlen($content_to_post)."\r\n");
                    fputs($fp, "User-Agent: multiOTP\r\n");
                    fputs($fp, "Host: ".$host."\r\n");
                    fputs($fp, "\r\n");
                    fputs($fp, $content_to_post);
                    fputs($fp, "\r\n");

                    stream_set_blocking($fp, TRUE);
                    stream_set_timeout($fp, $xml_timeout);
                    $info = stream_get_meta_data($fp); 
            
                    $reply = '';
                    $last_length = 0;
                    while ((!feof($fp)) && ((!$info['timed_out']) || ($last_length != strlen($reply))))
                    {
                        $last_length = strlen($reply);
                        $reply.= fgets($fp, 1024);
                        $info = stream_get_meta_data($fp);
                        @ob_flush(); // Avoid notice if any (if the buffer is empty and therefore cannot be flushed)
                        flush(); 
                    }
                    fclose($fp);

                    if ($info['timed_out'])
                    {
                        $this->WriteLog("Warning: timeout after $xml_timeout seconds for $protocol$host:$port$url with a result code of $errno ($errdesc).", FALSE, FALSE, 8888, 'Client-Server', '');
                    }
                    else
                    {
                        $pos = strpos(strtolower($reply), "\r\n\r\n");
                        $header = substr($reply, 0, $pos);
                        $answer = substr($reply, $pos + 4);
                        
                        $result = $answer;
                        if ($errno > 0)
                        {
                            $this->WriteLog("Info: $protocol$host:$port$url returns a resultcode of $errno ($errdesc).", FALSE, FALSE, 8888, 'Client-Server', '');
                        }
                        if (FALSE !== strpos($result,'<multiOTP'))
                        {
                            break;
                        }
                    }
                    // If we are here, something was bad with the actual server
                    $this->AddTemporaryBadServer($server_to_ban);
                    $log_info = "Info: temporary adding $server_to_ban to the list of banned servers, content not recognized";
                    if ($this->_xml_dump_in_log)
                    {
                        $log_info.= ": ".$result;
                    }

                    $this->WriteLog($log_info, FALSE, FALSE, 8888, 'Client-Server', '');
                }
                else
                {
                    $this->AddTemporaryBadServer($server_to_ban);
                    $this->WriteLog("Warning: Host $protocol$host on port $port not reached before a timeout of $xml_timeout seconds.", FALSE, FALSE, 8888, 'Client-Server', '');
                }
            }
            else
            {
                // This server has been skipped
                $this->WriteLog("Info: temporary skipping $xml_url_one due to timeout or inconsistent response.", FALSE, FALSE, 8888, 'Client-Server', '');
                $result = "";
            }
        }

        if (FALSE === strpos($result,'<multiOTP'))
        {
            $this->_servers_last_timeout = time();

            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Debug: timeout detected.", FALSE, FALSE, 8888, 'Debug', '');
            }
        }
        return $result;
    }


    function XmlServer($data)
    {
        // $this->WriteLog("Info: Host received the following request: $data", FALSE, FALSE, 8888, 'Debug', '');

        $cache_data      = '';
        $command_name    = '';
        $error_code      = 71;
        $server_password = '';
        $user_data       = '';
        $user_info       = '';
        $user_password   = '';

        $cache_data_template = <<<EOL
        <Cache>
        *UserInCache*</Cache>
EOL;

        $user_template = <<<EOL
            <User UserId="*UserId*">
                <UserData>*UserData*</UserData>
            </User>
EOL;

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
<DebugCode>*Command*</DebugCode>
<ServerPassword>*ServerPassword*</ServerPassword>
<ErrorCode>*ErrorCode*</ErrorCode>
<ErrorDescription>*ErrorDescription*</ErrorDescription>
*UserInfo**Cache*</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        
        if (FALSE !== strpos($data,'<multiOTP'))
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Info: Host answer is correctly formatted.", FALSE, FALSE, 8888, 'Debug', '');
                $this->WriteLog("Info: Host received the following request: $data", FALSE, FALSE, 8888, 'Debug', '');
            }
            
            //Set up the parser object
            $xml = new MultiotpXmlParser($data);

            //Parse it !
            $xml->Parse();

            $server_challenge = $this->Decrypt('ServerChallenge', (isset($xml->document->serverchallenge[0])?($xml->document->serverchallenge[0]->tagData):''),$this->GetServerSecret());

            if (isset($xml->document->checkusertoken[0]))
            {
                $command_name = 'CheckUserToken';
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: *CheckUserToken server request.", FALSE, FALSE, 8888, 'Server-Client', '');
                }
                $user_id = (isset($xml->document->checkusertoken[0]->userid[0])?($xml->document->checkusertoken[0]->userid[0]->tagData):'');
                $chap_id = (isset($xml->document->checkusertoken[0]->chap[0]->chapid[0])?($xml->document->checkusertoken[0]->chap[0]->chapid[0]->tagData):'00');
                $chap_challenge = (isset($xml->document->checkusertoken[0]->chap[0]->chapchallenge[0])?($xml->document->checkusertoken[0]->chap[0]->chapchallenge[0]->tagData):'');
                $chap_password = (isset($xml->document->checkusertoken[0]->chap[0]->chappassword[0])?($xml->document->checkusertoken[0]->chap[0]->chappassword[0]->tagData):'');

                $chap_hash = (isset($xml->document->checkusertoken[0]->chap[0]->chaphash[0])?($xml->document->checkusertoken[0]->chap[0]->chaphash[0]->tagData):'');
                if ('' != $chap_hash) {
                    $chap_hash = $this->Decrypt('ChapHash', $chap_hash, $chap_id.$server_challenge.$chap_id);
                }
                
                $cache_level = (isset($xml->document->checkusertoken[0]->cachelevel[0])?($xml->document->checkusertoken[0]->cachelevel[0]->tagData):0);
                if ($cache_level > $this->GetServerCacheLevel())
                {
                    $cache_level = $this->GetServerCacheLevel();
                }
                
                $error_code = 70;

                if ('MOSH' == substr($server_challenge, 0, 4)) // Ok, the challenge is encoded with the correct server secret
                {
                    if ('' != $chap_hash) {
                        $this->SetChapId('');
                        $this->SetChapChallenge('');
                        $this->SetChapPassword('');
                        $user_password = $chap_hash;
                    } elseif ('' == $chap_id) {
                        $this->SetChapId('');
                        $this->SetChapChallenge('');
                        $this->SetChapPassword('');
                        $user_password = $chap_password;
                    } else {
                        $this->SetChapId($chap_id);
                        $this->SetChapChallenge($chap_challenge);
                        $this->SetChapPassword($chap_password);
                    }
                    
                    if (!$this->CheckUserExists($user_id))
                    {
                        $error_code = 21; // ERROR: User doesn't exist
                    }                    
                    else
                    {
                        $error_code = $this->CheckUserToken($user_id, $user_password, '', FALSE, FALSE, FALSE, TRUE); // do_not_check_on_server = TRUE;
                        
                        $now_epoch = time();
                        $cache_lifetime = $this->GetServerCacheLifetime();

                        if ((0 < $cache_level) && (0 == $error_code))
                        {
                            if ($this->GetVerboseFlag())
                            {
                                $this->WriteLog("Info: *Cache level is set to $cache_level", FALSE, FALSE, 8888, 'Server-Client', '');
                            }
                            
                            reset($this->_user_data);
                            while(list($key, $value) = each($this->_user_data))
                            {
                                if ('' != trim($key))
                                {
                                    if ('encryption_hash' != $key)
                                    {
                                        $user_data.= strtolower($key);
                                        if ('autolock_time' == $key)
                                        {
                                            if (0 < $cache_lifetime)
                                            {
                                                if (($value == 0) || ($value > ($now_epoch + $cache_lifetime)))
                                                {
                                                    $value = ($now_epoch + $cache_lifetime);
                                                }
                                            }
                                        }
                                        $value = $this->Encrypt($key, $value, $this->GetServerSecret());
                                        $user_data = $user_data.":";
                                        $user_data = $user_data."=".$value;
                                        $user_data.= "\n";
                                    }
                                }
                            }

                            $cache_user = '';
                            $one_cache_user = str_replace('*UserId*', $user_id, $user_template);
                            $one_cache_user = str_replace('*UserData*', $user_data, $one_cache_user);
                            $cache_user .= $one_cache_user;
                            
                            $cache_data = str_replace('*UserInCache*', $cache_user, $cache_data_template);
                        }
                    }
                }
            } // End of CheckUserToken
            elseif (isset($xml->document->readuserdata[0]))
            {
                $command_name = 'ReadUserData';
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: *ReadUserData server request.", FALSE, FALSE, 8888, 'Server-Client', '');
                }
                $user_id = (isset($xml->document->readuserdata[0]->userid[0])?($xml->document->readuserdata[0]->userid[0]->tagData):'NO_USER_DETECTED!');

                $error_code = 70;

                if ('MOSH' == substr($server_challenge, 0, 4)) // Ok, the challenge is encoded with the correct server secret
                {
                    $error_code = 21; // ERROR: User doesn't exist

                    if ($this->ReadUserData($user_id, FALSE, TRUE)) // $no_server_check = TRUE;
                    {
                        $error_code = 19;
                        reset($this->_user_data);
                        while(list($key, $value) = each($this->_user_data))
                        {
                            if ('' != trim($key))
                            {
                                if ('encryption_hash' != $key)
                                {
                                    $user_data.= strtolower($key);
                                    $value = $this->Encrypt($key, $value, $this->GetServerSecret());
                                    $user_data = $user_data.":";
                                    $user_data = $user_data."=".$value;
                                    $user_data.= "\n";
                                }
                            }
                        }

                        $user_info = str_replace('*UserId*', $user_id, $user_template);
                        $user_info = str_replace('*UserData*', $user_data, $user_info);
                    }
                }
            } // End of ReadUserData
            elseif (isset($xml->document->checkuserexists[0]))
            {
                $command_name = 'CheckUserExists';
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: *CheckUserExists server request.", FALSE, FALSE, 8888, 'Server-Client', '');
                }
                $user_id = (isset($xml->document->checkuserexists[0]->userid[0])?($xml->document->checkuserexists[0]->userid[0]->tagData):'NO_USER_DETECTED!');

                $error_code = 70;

                if ('MOSH' == substr($server_challenge, 0, 4)) // Ok, the challenge is encoded with the correct server secret
                {
                    $error_code = 21; // ERROR: User doesn't exist

                    if ($this->CheckUserExists($user_id, TRUE)) // $no_server_check = TRUE;
                    {
                        $error_code = 22;
                    }
                }
            } // End of CheckUserExists
            
            $server_password = md5($command_name.$this->GetServerSecret().$server_challenge);
        }
        else
        if ($this->GetVerboseFlag())
        {
            $this->WriteLog("Info: *Server received the following request: $data", FALSE, FALSE, 8888, 'Server-Client', '');
        }
        

        $error_description = $this->GetErrorText($error_code);
        
        $xml_data = str_replace('*Command*', $command_name, $xml_data);
        $xml_data = str_replace('*ServerPassword*', $server_password, $xml_data);
        $xml_data = str_replace('*ErrorCode*', $error_code, $xml_data);
        $xml_data = str_replace('*ErrorDescription*', $error_description, $xml_data);
        $xml_data = str_replace('*UserInfo*', $user_info, $xml_data);
        $xml_data = str_replace('*Cache*', $cache_data, $xml_data);

        /****************************************
         * WE REALLY DO NOT WANT TO BE CACHED !!!
         ****************************************/
        header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if ($this->_xml_dump_in_log)
        {
            $this->WriteLog("Info: Server sent the following answer: $xml_data", FALSE, FALSE, 8888, 'Debug', '');
        }

        echo $xml_data;
    }


    // This method is a stub that calls the MultiotpQrcode with the good pathes
    function qrcode($data = '', $file_name = '', $image_type = "P", $ecc_level = "Q", $module_size = 4, $version = 0, $structure_m = 0, $structure_n = 0, $parity = 0, $original_data = '')
    {
        $result = '';

        $qrcode_folder = $this->GetQrCodeFolder();

        $path = $qrcode_folder.'data';
        $image_path = $qrcode_folder.'image';
        
        if (!(file_exists($path) && file_exists($image_path)))
        {
            $this->WriteLog("Error: QRcode files or folders are not available", FALSE, FALSE, 299, 'System', '');
        }
        else
        {
            $result = MultiotpQrcode($data, $file_name, $image_type, $ecc_level, $module_size, $version, $structure_m, $structure_n, $parity, $original_data, $path, $image_path);

            {
                $output_name = NULL;
                ob_start();
            }

            
            if (('' != trim($file_name)) && ('binary' != trim($file_name)) && ('' != $this->GetLinuxFileMode()))
            {
                if (file_exists($file_name))
                {
                    chmod($file_name, octdec($this->GetLinuxFileMode()));
                }
            }
        }
        
        return $result;
    }
    
    
}


//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
//                                                                  //
// The source codes of the next classes are not directly related to //
//  multiOTP but they are needed for extended functionalities.      //
//                                                                  //
// They are inserted directly in the class file to eliminitate any  //
//  include or require problem.                                     //
//                                                                  //
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////

require_once('contrib/MultiotpAspSms.php'); // External contribution

require_once('contrib/MultiotpClickatell.php'); // External contribution

require_once('contrib/MultiotpIntelliSms.php'); // External contribution

require_once('contrib/MultiotpTools.php'); // External contribution

/*******************************************************************
 * PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY 2.1 (LGPLv2.1) *
 * Scott Barnett                                                   *
 * http://adldap.sourceforge.net/                                  *
 *******************************************************************/
require_once('contrib/MultiotpAdLdap.php'); // External contribution

/*************************************************
 * QRcode image PHP scripts 0.50j (FREE "AS IS") *
 * Y. Swetake                                    *
 * http://www.swetake.com/qr/index-e.html        *
 *************************************************/
require_once('contrib/MultiotpQrcode.php'); // External contribution

/************************************************
 * status_bar.php (2010) (FREE "AS IS")         *
 * dealnews.com, Inc.                           *
 * http://brian.moonspot.net/status_bar.php.txt *
 ************************************************/
require_once('contrib/MultiotpShowStatus.php'); // External contribution

/*****************************************
 * PHP Syslog class 1.1.2 (FREE "AS IS") *
 * André Liechti                         *
 * http://developer.sysco.ch/php/        *
 *****************************************/
require_once('contrib/MultiotpSyslog.php'); // External contribution

/*******************************************
 * XML Parser Class 1.3.0 (LGPLv3)         *
 * Adam A. Flynn                           *
 * http://www.criticaldevelopment.net/xml/ *
 *******************************************/
require_once('contrib/MultiotpXmlParser.php'); // External contribution

/*****************************************
 * MultiotpYubikey Class (LGPLv3)        *
 * André Liechti                         *
 * http://www.multiotp.net/              *
 *****************************************/
require_once('contrib/MultiotpYubikey.php'); // External contribution

/*************************************
 * phpseclib 0.3.6 (MIT License)     *
 * MMVI Jim Wigginton                *
 * http://phpseclib.sourceforge.net/ *
 *************************************/
if (!function_exists('crypt_random_string')) {
    require_once('contrib/Random.php'); // External contribution
}
if (!class_exists('Math_BigInteger')) {
    require_once('contrib/BigInteger.php'); // External contribution
}
if (!class_exists('Crypt_Base')) {
    require_once('contrib/Base.php'); // External contribution
}
if (!class_exists('Crypt_Hash')) {
    require_once('contrib/Hash.php'); // External contribution
}
if (!class_exists('Crypt_Rijndael')) {
    require_once('contrib/Rijndael.php'); // External contribution
}
if (!class_exists('Crypt_AES')) {
    require_once('contrib/AES.php'); // External contribution
}
if (!class_exists('Crypt_DES')) {
    require_once('contrib/DES.php'); // External contribution
}
if (!class_exists('Crypt_TripleDES')) {
    require_once('contrib/TripleDES.php'); // External contribution
}
if (!class_exists('Crypt_RSA')) {
    require_once('contrib/RSA.php'); // External contribution
}

/********************************************************
 * XPertMailer package 4.0.5 (LGPLv2.1)                 *
 * Tanase Laurentiu Iulian                              *
 * http://xpertmailer.sourceforge.net/                  *
 ********************************************************/
if (!defined('DISPLAY_XPM4_ERRORS')) define('DISPLAY_XPM4_ERRORS', FALSE);
if (version_compare(phpversion(), '5', '>='))
{
    if (!class_exists('FUNC5')) {
        require_once('contrib/FUNC5.php'); // External contribution
    }

    if (!class_exists('MIME5')) {
        require_once('contrib/MIME5.php'); // External contribution
    }

    if (!class_exists('SMTP5')) {
        require_once('contrib/SMTP5.php'); // External contribution
    }

    if (!class_exists('MAIL5')) {
        require_once('contrib/MAIL5.php'); // External contribution
    }
    class MAIL extends MAIL5 { }
}
else
{
    if (!class_exists('FUNC4')) {
        require_once('contrib/FUNC4.php'); // External contribution
    }

    if (!class_exists('MIME4')) {
        require_once('contrib/MIME4.php'); // External contribution
    }

    if (!class_exists('SMTP4')) {
        require_once('contrib/SMTP4.php'); // External contribution
    }

    if (!class_exists('MAIL4')) {
        require_once('contrib/MAIL4.php'); // External contribution
    }
    class MAIL extends MAIL4 { }
}

?>