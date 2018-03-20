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
 * that supports the following algorithms and RFC's:
 *  - RFC1994 CHAP (Challenge Handshake Authentication Protocol)
 *  - RFC2433 MS-CHAP (Microsoft PPP CHAP Extensions)
 *  - RFC2487 SMTP Service Extension for Secure SMTP over TLS
 *  - RFC2759 MS-CHAPv2 (Microsoft PPP CHAP Extensions, Version 2)
 *  - RFC2821 SMTP (Simple Mail Transfer Protocol)
 *  - RFC4226 OATH/HOTP (HOTP: An HMAC-Based One-Time Password Algorithm)
 *  - RFC5424 Syslog Protocol (client)
 *  - RFC6030 PSKC (Additional Portable Symmetric Key Container Algorithm Profiles)
 *  - RFC6238 OATH/TOTP (TOTP: Time-Based One-Time Password Algorithm)
 *  - Yubico OTP (http://yubico.com/yubikey)
 *  - mOTP (http://motp.sourceforge.net)
 *  - OATH/HOTP or OATH/TOTP, base32/hex/raw seed, QRcode provisioning
 *    (FreeOTP, Google Authenticator, ...)
 *  - SMS tokens (using aspsms, clickatell, intellisms, or even your own script)
 *  - TAN (emergency scratch passwords)
 *
 * This class can be used as is in your own PHP project, but it can also be
 * used easily as an external authentication provider with at least the
 * following RADIUS servers (using the multiotp command line script):
 *  - FreeRADIUS, a free RADIUS server implementation for Linux and
 *    and *nix environments (http://freeradius.org/)
 *  - WinRADIUS, the FreeRADIUS implementation ported for Windows
 *    (http://winradius.eu/)
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
 *  - multiOTP Enterprise, an HA master-slave virtual appliance to
 *    provide a complete strong authentication solution
 *  - secuPASS.net, a simple service to centralize provisioning and SMS
 *    authentication for (free) Wifi hotspot (http://www.secupass.net/)
 *
 * The Readme file contains additional information.
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
 *   phpseclib 1.0.6 (MIT License)
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
 *   TCPDF 6.2.13 (LGPLv3)
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
 *   Additional Portable Symmetric Key Container (PSKC) Algorithm Profiles
 *     RFC 6030 (http://tools.ietf.org/html/rfc6030)
 *
 *   Google Authenticator (based on OATH/TOTP)
 *     https://github.com/google/google-authenticator
 *
 *
 * Users feedbacks and comments
 *
 * 2018-02-13 Jonathan Garber (via GitHub)
 *   Thanks for your feedback about various issues.
 *
 * 2017-11-22 vak255 (via GitHub)
 *   Thanks for your feedback about a bad handled unicode issue.
 *   All strtoXXX and strpos have been changed to the the multibyte version
 *
 * 2017-06-11 Richard Green
 *   Thanks for your proposal about specific LDAPTLS configuration values to be moved in the confiug parameters
 *
 * 2017-04-19 Frank van der Aa, Vanboxtel BV (NL)
 *   Thanks a lot for your valuable implementation suggestion about PostgreSQL.
 *   The proposed code has been adapted and integrated in the project.
 *
 * 2017-02-14 Frank van der Aa, Vanboxtel BV (NL)
 *   Thanks for your proposal about GetList() method sorted output.
 *
 * 2017-02-09 Frank van der Aa, Vanboxtel BV (NL)
 *   Thanks for your debug about lockedlistarray[], the proposed
 *   GetDelayedUsersList() method and the delayed users display on the web GUI.
 *
 * 2017-02-02 Stefan Kügler, SerNet GmbH (DE)
 *   Thanks for your feedback on the last edition.
 *
 * 2017-01-24 Jean-François Perillo, Kudelski Security (CH)
 *   As proposed by Jean-François, requested LDAP password for synchronized users can be overwritten.
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
 * Todos
 *
 *   Add more comments in the main class file
 *
 *
 * Change Log
 *
 *   2018-03-20 5.1.1.2 SysCo/al FIX: typo in the source code of the command line option for ldap-pwd and prefix-pin
 *                               ENH: Dockerfile available
 *   2018-03-05 5.1.0.8 SysCo/al FIX: Enigma Virtual Box updated to version 8.10 (to create the special all-in-one-file)
 *   2018-02-27 5.1.0.7 SysCo/al FIX: [Receive an OTP by SMS] link is now fixed for Windows 10
 *   2018-02-26 5.1.0.6 SysCo/al ENH: Credential Provider registry entries are now always used when calling multiOTP.exe
 *   2018-02-21 5.1.0.5 SysCo/al FIX: To avoid virus false positive alert, multiOTP.exe is NO more packaged in one single file
 *                                    using Enigma, a php folder is now included in the multiOTP folder
 *                               FIX: multiOTPOptions registry entry is now useless
 *   2018-02-21 5.1.0.4 SysCo/al ENH: Credential Provider registry entries are used if available
 *   2018-02-19 5.1.0.3 SysCo/al Expired AD/LDAP password support
 *                               multiOTP Credential Provider (for Windows) improvements
 *                                (user@domain.name UPN support, default domain name supported and displayed, SMS request link)
 *                               "force_no_prefix_pin" option for devices (for example if the device is a
 *                                computer with multiOTP credential Provider and AD/LDAP synced password)
 *                               Better unicode handling, multibyte fonctions used when needed (mb_strtolower(), ...)
 *   2017-11-04 5.0.5.6 SysCo/al Better FreeRADIUS 3.x documentation
 *                               New radius tag prefix configuration option
 *                               New multiple groups device option
 *                               Some notice corrections (if the array element doesn't exist)
 *                               A user cannot be created with a leading backslash (fixed in FastCreateUser and CreateUserFromToken)
 *   2017-09-29 5.0.5.2 SysCo/al The proposed mOTP generator for Android/iOS is now OTP Authenticator
 *                               New xml QRCode provisioning format for mOTP (compatible with OTP Authenticator)
 *   2017-09-08 5.0.5.0 SysCo/al NirSoft nircmd.exe tool removed from the distribution (false virus detection)
 *                               Multiple URLs separator for client/server config is still ";", but [space] and "," are accepted
 *                               New developer mode for some specific detailed logs during development process only
 *   2017-07-07 5.0.4.9 SysCo/al New methods: SetLdapTlsReqcert, GetLdapTlsReqcert, SetLdapTlsCipherSuite, GetLdapTlsCipherSuite
 *                                to change config parameters, instead of hard coded parameters (for SSL/TLS LDAP connection)
 *                               Fixed too much detailed information in the log when trying
 *                                to detect a token serial number for self-registration
 *   2017-06-06 5.0.4.8 SysCo/al Fixed SSL/TLS LDAP failed connection for PHP 7.x (GnuTLS TLS1.2 restriction removed for PHP 7.x)
 *   2017-06-02 5.0.4.6 SysCo/al Fixed a typo in the ReadCacheData method for PostgreSQL support
 *                               Important, under Linux, the config, devices, groups, tokens and users folders are now always
 *                                located in /etc/multiotp/. Please be sure to make the move when you are upgrading
 *                               Cleaned some ugly PHP warnings when the backend is not initialized
 *   2017-05-29 5.0.4.5 SysCo/al Restore configuration added in Web GUI
 *                               Fixed configuration file directory under Windows in Web GUI
 *                               Fixed path with spaces handling for the command line edition (thanks Scott for the feedback)
 *                               PostgreSQL support, based on source code provided by Frank van der Aa
 *                               Fixed file_get_contents issue with offset parameter in PHP 7.x
 *   2017-05-16 5.0.4.4 SysCo/al GetList() is now sorted with files backend
 *                               A replay during a defined delay (default 60 seconds) of the previous refused password is rejected,
 *                                but the error counter is not incremented (SetLastFailedWhiteDelay and GetLastFailedWhiteDelay)
 *                               A user cannot be created with a leading backslash
 *   2017-02-23 5.0.3.7 SysCo/al Group names are now always trimed to avoid blank spaces
 *                               SetLinuxFolderMode() and GetLinuxFolderMode() methods added
 *   2017-02-21 5.0.3.6 SysCo/al GetDelayedUsersList() method added
 *                               GetList() is now sorted with MySQL backend
 *                               RestoreConfiguration() method updated, system configuration data can be ignored
 *                               SetUserTokenSeed() and SetTokenSeed() methods accept now also base32 and raw binary
 *                               The full windows package has been fixed and cleaned
 *   2017-02-03 5.0.3.5 SysCo/al GetUserInfo() method added
 *                               ImportTokensFromCsv fixed when the file is not readable
 *                               Fix possible endless loop when opening a file that exists but without the right to read it
 *   2017-01-26 5.0.3.4 SysCo/al It's now possible to do several commands at once with the CLI edition
 *                               New overwrite_request_ldap_pwd option (enabled by default).
 *                                If overwrite is enabled, default_request_ldap_pwd value is forced during synchronization
 *                               Multiple groups per user is now supported (not all devices support multiple groups).
 *                                (radius reply attributor has been changed to += by default)
 *                               multiotp -delete-token command has been added in the CLI
 *                               -lock and -unlock command return now 19 (instead of 99) in the CLI
 *                               Better support of DialinIp functions in command line usage
 *                               New LDAP cache management to support huge AD/LDAP, with cache on disk (system temporary folder)
 *                               New PurgeLockFolder() and PurgeLdapCacheFolder() method
 *                               The default proposed TOTP/HOTP generator for Android/iOS is now FreeOTP Authenticator
 *                               Better Eastern European languages support
 *                               Multiple purpose tokens provisioning format PSKCV10,
 *                                like Gemalto e3050cL and t1050 tokens, is now supported.
 *                               Various bug fixes and enhancements when using the proxy mode.
 *   2016-11-14 5.0.3.0 SysCo/al Log messages are better categorized
 *                               The user dialin IP address is synchronized from the Active Directory msRADIUSFramedIPAddress attribute
 *                               New IP dialin methods : SetUserDialinIpAddress(), SetUserDialinIpMask(), SetDefaultDialinIpMask(),
 *                                GetUserDialinIpAddress(), GetUserDialinIpMask(), GetDefaultDialinIpMask()
 *                               If the user dialin IP address is defined, Framed-IP-Address
 *                                and Framed-IP-Mask are delivered in the RADIUS answer
 *                               Enhanced token importation process (to support binary encryption key in hexadecimal 0xAABBCC format)
 *   2016-11-04 5.0.2.6 SysCo/al Better log message for automatically or manually created objects
 *                               External packages update
 *                               New GetUserLastLogin() and SetUserLastLogin() methods
 *                               Backup configuration file can now be restored in commercial version without any changes
 *   2016-10-16 5.0.2.5 SysCo/al Better SSL support using context if available (for PHP >= 5.3)
 *                               New methods SetTouchFolder(), GetTouchFolder(), TouchFolder(), FolderTouched() to offer asynchronous capabilities
 *                               New methods added for SOAP service
 *                               Weekly anonymized stats added (can be disabled). Anonymized stats include the following information:
 *                                backend type, AD/LDAP used or not, OS version, PHP version, library version, number of accounts defined,
 *                                number of tokens defined. They are sent on the stats.multiotp.net FQDN which is hosted in Switzerland.
 *                               It's now possible to select a specific LDAP/AD attribute used as the synchronised account name
 *                                SetLdapSyncedUserAttribute(), GetLdapSyncedUserAttribute()
 *                               An account can be tested from the dashboard
 *                               Unified configuration backup and restore format (BackupConfiguration)
 *                               Better support of MS-CHAPv2 in the provided appliances
 *                               Cached requests supported (cached during a specific amount of time, useful for WebDAV authentication)
 *                                (device option cache_result_enabled)
 *                               A try on the previous accepted password is rejected, but the error counter is not incremented
 *                               ForceNoDisplayLog() method added, in order to be able to disable log on display in server mode
 *                               XML parsing error are more verbose
 *                               XmlServer is now sending XML response with the specific Content-type: text/xml
 *                               YubicoOTP private id check is now implemented
 *                               SSL AD/LDAP also supported with Windows 2012 server
 *                               SyncLdapUsers is now using a semaphore file in order to avoid concurrent process for large AD/LDAP sync
 *                                (tested with 1'000 groups, 100'000 users, 1'000 users in the LDAP sync group)
 *                               AD/LDAP additional log information
 *                               New GetNetworkInfo and SetNetworkInfo methods
 *                               Special chars support enhanced in LDAP class (as described in RFC4515)
 *                               The default ldap_group_cn_identifier is now cn instead of sAMAccountName
 *                               The first matching group defined in AD/LDAP group(s) filtering is now defined for the user
 *                                (this group is returned as the Filter-Id (11) option in a successful RADIUS answer)
 *                               Enhanced SMS support for Clickatell, SSL is now also working
 *                               Bug fix concerning QRcode generation for mOTP
 *                               Code fixes
 *                               New AssignTokenToUser() and RemoveTokenFromUser() methods
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
 * @version   5.1.1.2
 * @date      2018-03-20
 * @since     2010-07-18
 */
{
  var $_class;                    // Name of the class
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
  var $_default_ssl_context;      // Default SSL context
  var $_sql_tables;               // Array of alias names for SQL tables
  var $_anonymous_stat_interval;  // Anonymous stat interval in seconds
  var $_no_display_log;           // No log on display (if runing as a web server for example)
  var $_cli_mode;                 // Flag to know if we are in CLI mode
  var $_cli_proxy_mode;           // Flag to know if we are in CLI proxy mode
  var $_cp_mode;                  // Flag to indicate that we are in Credential Provider mode
  var $_touch_folder;             // Touch folder (to detect changing elements)
  var $_touch_suffix_array;       // Touch suffix
  var $_lock_folder;              // Lock folder (to handle semaphore files)
  var $_lock_time;                // Valid time for a semaphore lock file, in seconds
  var $_ldap_sync_lock_file_name; // AD/LDAP synchronization lock file name
  var $_ldap_sync_stop_file_name; // AD/LDAP synchronization stop file name
  var $_last_http_status;         // Last HTTP status
  var $_bad_syslog_server;        // The Syslog server is temporarly bad

  /**
   * @brief   Class constructor.
   *
   * @param   string  $encryption_key      A specific encryption key to encrypt stored data instead of the default one.
   * @param   boolean $initialize_backend  If we initialize the backend, we don't want to write in the database before the end of the initialization.
   * @param   boolean $base_dir            Define the base directory, which is always better than automatic detection.
   * @param   boolean $config_dir          Define the config directory, which is always better than automatic detection.
   * @retval  void
   *
   * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version   5.1.1.2
   * @date      2018-03-20
   * @since     2010-07-18
   */
  function __construct(
      $encryption_key = "",
      $initialize_backend = false,
      $base_dir = "",
      $config_dir = ""
  ) {
      // destructor definition (for PHP 4 compatibility)
      if (!version_compare(phpversion(), '5', '>=')) {
          register_shutdown_function(array(&$this, '__destruct'));
      }

      // 3 seconds timeout (for Linux only) for gethostbyname() which can be used by third party modules
      putenv('RES_OPTIONS=retrans:1 retry:1 timeout:3 attempts:1');

      if (!isset($this->_class)) { $this->_class = base64_decode('bXVsdGlPVFA='); }
      if (!isset($this->_version)) {
        $temp_version = '@version   5.1.1.2'; // You should add a suffix for your changes (for example 5.0.3.2-andy-2016-10-XX)
        $this->_version = trim(substr($temp_version, 8));
      }
      if (!isset($this->_date)) {
        $temp_date = '@date      2018-03-20'; // You should update the date with the date of your changes
        $this->_date = trim(substr($temp_date, 8));
      }
      if (!isset($this->_copyright)) { $this->_copyright = base64_decode('KGMpIDIwMTAtMjAxOCBTeXNDbyBzeXN0ZW1lcyBkZSBjb21tdW5pY2F0aW9uIHNh'); }
      if (!isset($this->_website)) { $this->_website = base64_decode('aHR0cDovL3d3dy5tdWx0aU9UUC5uZXQ='); }
      
      $this->_anonymous_stat_interval = 604800; // Stat interval: 7 * 24 * 60 * 60 = 604800 = 1 week
      
      $this->_log_header_written    = FALSE; // Flag indicating if the header has already been written in the log file or not
      $this->_valid_algorithms      = '*mOTP*HOTP*TOTP*YubicoOTP*'; // Supported algorithms, don't change it (unless you have added the handling of a new algorithm ;-)
      $this->_attributes_to_encrypt = '*admin_password_hash*challenge*device_secret*ldap_hash_cache*ldap_server_password*scratch_passwords*seed_password*server_secret*sms_api_id*sms_otp*sms_password*sms_userkey*smtp_password*sql_password*token_seed*user_pin*'; // This default list of attributes can be changed using SetAttributesToEncrypt(). Each attribute must be between "*".
      
      $this->_no_display_log = false; // No log on display (if runing as a web server for example)

      $this->_cli_mode = false; // No CLI mode
      $this->_cli_proxy_mode = false; // No CLI proxy mode
      $this->_cp_mode = false; // By default, not on CP mode

      // BEGIN some specific files and folders initialization

      clearstatcache();

      $this->_touch_folder = '';
      $this->_touch_suffix_array = array();

      $this->SetLockTime(300);

      // Search for a fast temporary directory that flush at reboot
      $lock_folder = '/dev/shm/';
      if (!file_exists($lock_folder)) {
          // If /dev/shm/ is not available, we take the regular temporary directory and
          //   we clean the lock files which are older than the allowed lock time
          $lock_folder = sys_get_temp_dir()."/";
          $actual_folder = $lock_folder;
          $actual_filter = "multiotp-*.lock";
          if (($actual_dir = opendir($actual_folder)) !== FALSE) {
            while(($actual_file_name = readdir($actual_dir)) !== FALSE) {
              if (fnmatch($actual_filter, $actual_file_name)) {
                $actual_file = $actual_folder.$actual_file_name;
                if (filemtime($actual_file) <= (time() - $this->GetLockTime())) {
                  unlink($actual_file);
                }
              }
            }
          }
      }
      $this->SetLockFolder($lock_folder);

      $this->SetLdapSyncLockFileName("multiotp-ldap.lock");
      $this->SetLdapSyncStopFileName("multiotp-ldap.stop");

      // END some specific files and folders initialization

      $this->_ldap_server_reachable = FALSE;

      // http://phpsecurity.readthedocs.io/en/latest/Transport-Layer-Security-(HTTPS-SSL-and-TLS).html
      $this->_default_ssl_context = array(
          'ssl' => array(
              'verify_peer'         => false,
              'verify_peer_name'    => false,
              'disable_compression' => true,
              'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4'
          )
      );
      if (function_exists("stream_context_set_default")) {
          $default_context = stream_context_set_default($this->_default_ssl_context);
      }

      $this->_sql_tables = array('cache',
                                 'config',
                                 'devices',
                                 'groups',
                                 'log',
                                 'tokens',
                                 'users'
                                );

      // int() is for mysql and replaced by numeric() for pgSQL
      // datetime is for MySQL and replaced by timestamp for pgSQL

      $this->_sql_tables_schema['cache']   = array(
          'active_users_count'      => "int(10) DEFAULT -1",
          'devices_count'           => "int(10) DEFAULT -1",
          'last_update'             => "int(10) DEFAULT 0",
          'locked_users_count'      => "int(10) DEFAULT -1",
          'locked_users_list'       => "int(10) DEFAULT -1",
          'delayed_users_count'     => "int(10) DEFAULT -1",
          'delayed_users_list'      => "int(10) DEFAULT -1",
          'tokens_count'            => "int(10) DEFAULT -1",
          'users_count'             => "int(10) DEFAULT -1");
      $this->_sql_tables_index['cache']    = '**';
      $this->_sql_tables_ignore['cache']   = "**";
      
      $this->_sql_tables_schema['config']  = array(
          'actual_version'              => "varchar(255) DEFAULT ''",
          'admin_password_hash'         => "varchar(255) DEFAULT ''",
          'anonymous_stat'              => "int(1) DEFAULT 1",
          'anonymous_stat_last_update'  => "int(10) DEFAULT 0",
          'anonymous_stat_random_id'    => "varchar(255) DEFAULT ''",
          'attributes_to_encrypt'       => "varchar(255) DEFAULT ''",
          'auto_resync'                 => "int(1) DEFAULT 1",
          // Backend encoding (UTF-8 or others)
          'backend_encoding'            => "varchar(255) DEFAULT 'UTF-8'",
          // Backend storage type (files / mysql / pqsql)
          'backend_type'                => "varchar(255) DEFAULT 'files'",
          // By default, backend_type is not validated
          'backend_type_validated'      => "int(1) DEFAULT 0",
          'cache_data'                  => "int(1) DEFAULT 0",
          'cache_ldap_hash'             => "int(1) DEFAULT 1",
          'case_sensitive_users'        => "int(1) DEFAULT 0",
          'clear_otp_attribute'         => "varchar(255) DEFAULT ''",
          // No console authentication by default
          'console_authentication'      => "int(1) DEFAULT 0",
          // Debug mode (to enable it permanently)
          'debug'                       => "int(1) DEFAULT 0",
          'default_algorithm'           => "varchar(255) DEFAULT 'totp'",
          'default_dialin_ip_mask'      => "varchar(255) DEFAULT ''",
          'default_user_group'          => "varchar(255) DEFAULT ''",
          'default_request_ldap_pwd'    => "int(1) DEFAULT 1",
          'default_request_prefix_pin'  => "int(1) DEFAULT 1",
          'demo_mode'                   => "int(1) DEFAULT 0",
          'developer_mode'              => "int(1) DEFAULT 0",
          // Display log mode (to enable it permanently)
          'display_log'                 => "int(1) DEFAULT 0",
          'domain_name'                 => "varchar(255) DEFAULT ''",
          'email_admin_address'         => "varchar(255) DEFAULT ''",
          'encode_file_id'              => "int(1) DEFAULT 0",
          'encryption_key_full_path'    => "varchar(255) DEFAULT ''",
          // Locking delay in seconds between two trials after "max_delayed_failures" failures
          'failure_delayed_time'        => "int(10) DEFAULT 300",
          'group_attribute'             => "varchar(255) DEFAULT 'Filter-Id'",
          'hash_salt_full_path'         => "varchar(255) DEFAULT ''",
          'issuer'                      => "varchar(255) DEFAULT 'multiOTP'",
          'language'                    => "varchar(255) DEFAULT 'en'",
          'last_update'                 => "int(10) DEFAULT 0",
          'ldap_expired_password_valid' => "int(1) DEFAULT 1",
          'ldap_account_suffix'         => "varchar(255) DEFAULT ''",
          'ldap_activated'              => "int(1) DEFAULT 0",
          'ldap_base_dn'                => "varchar(255) DEFAULT ''",
          'ldap_bind_dn'                => "varchar(255) DEFAULT ''",
          'ldap_cache_folder'           => "varchar(255) DEFAULT 'tempdir'",
          'ldap_cache_on'               => "int(1) DEFAULT 1",
          'ldap_cn_identifier'          => "varchar(255) DEFAULT 'sAMAccountName'",
          'ldap_default_algorithm'      => "varchar(255) DEFAULT 'totp'",
          'ldap_domain_controllers'     => "varchar(255) DEFAULT ''",
          'last_failed_white_delay'     => "int(10) DEFAULT 60",
          'ldap_group_attribute'        => "varchar(255) DEFAULT 'memberOf'",
          'ldap_group_cn_identifier'    => "varchar(255) DEFAULT 'cn'",
          'ldap_groups_dn'              => "varchar(255) DEFAULT ''",
          // Hash cache time: 7 * 24 * 60 * 60 = 604800 = 1 week
          'ldap_hash_cache_time'        => "int(10) DEFAULT 604800",
          'ldap_in_group'               => "varchar(255) DEFAULT ''",
          'ldap_language_attribute'     => "varchar(255) DEFAULT 'preferredLanguage'",
          'ldap_network_timeout'        => "int(10) DEFAULT 10",
          'ldap_port'                   => "varchar(255) DEFAULT '389'",
          'ldap_recursive_cache_only'   => "int(1) DEFAULT 0",
          'ldap_recursive_groups'       => "int(1) DEFAULT 1",
          'ldap_server_password'        => "varchar(255) DEFAULT ''",
          // Default type 1 is Active Directory, 2 for Generic LDAP
          'ldap_server_type'            => "int(10) DEFAULT 1",
          'ldap_ssl'                    => "int(1) DEFAULT 0",
          'ldap_synced_user_attribute'  => "varchar(255) DEFAULT ''",
          'ldap_time_limit'             => "int(10) DEFAULT 30",
          'ldaptls_reqcert'             => "varchar(255) DEFAULT 'auto'",
          'ldaptls_cipher_suite'        => "varchar(255) DEFAULT 'auto'",
          'log'                         => "int(1) DEFAULT 0",
          'max_block_failures'          => "int(10) DEFAULT 6",
          'max_delayed_failures'        => "int(10) DEFAULT 3",
          'max_event_resync_window'     => "int(10) DEFAULT 10000",
          'max_event_window'            => "int(10) DEFAULT 100",
          'max_time_resync_window'      => "int(10) DEFAULT 90000",
          // Maximum time window to be accepted, in seconds (+/-)
          // Initialized to a little bit more than +/- 10 minutes
          // (was 8000 seconds in version 3.x, and Stefan Kügler suggested to put a lower default value)
          'max_time_window'             => "int(10) DEFAULT 600",
          'multiple_groups'             => "int(1) DEFAULT 0",
          'ntp_server'                  => "varchar(255) DEFAULT 'pool.ntp.org'",
          // Overwrite request_ldap_pwd value for synced users
          'overwrite_request_ldap_pwd'  => "int(1) DEFAULT 1",
          'radius_error_reply_message'  => "int(1) DEFAULT 1",
          'radius_reply_attributor'     => "varchar(255) DEFAULT ' += '",
          'radius_reply_separator_hex'  => "varchar(255) DEFAULT '".bin2hex(',')."'",
          'radius_tag_prefix'           => "varchar(255) DEFAULT ''",
          'scratch_passwords_digits'    => "int(10) DEFAULT 6",
          'scratch_passwords_amount'    => "int(10) DEFAULT 10",
          'self_registration'           => "int(1) DEFAULT 1",
          // Client-server configuration
          'server_cache_level'          => "int(10) DEFAULT 1",
          // 1552000 = 6 monthes
          'server_cache_lifetime'       => "int(10) DEFAULT 15552000",
          'server_secret'               => "varchar(255) DEFAULT 'ClientServerSecret'",
          'server_timeout'              => "int(10) DEFAULT 5",
          'server_type'                 => "varchar(255) DEFAULT 'xml'",
          // Server URL can contain multiple servers, they must be separated by ;
          'server_url'                  => "varchar(255) DEFAULT ''",
          'sms_api_id'                  => "varchar(255) DEFAULT ''",
          'sms_message_prefix'          => "varchar(255) DEFAULT '%s is your SMS-Code'",
          'sms_originator'              => "varchar(255) DEFAULT 'multiOTP'",
          'sms_password'                => "varchar(255) DEFAULT ''",
          'sms_provider'                => "varchar(255) DEFAULT ''",
          'sms_userkey'                 => "varchar(255) DEFAULT ''",
          'sms_digits'                  => "int(10) DEFAULT 6",
          // SMS timeout before authenticating (in seconds)
          'sms_timeout'                 => "int(10) DEFAULT 180",
          'smtp_auth'                   => "int(1) DEFAULT 0",
          'smtp_password'               => "varchar(255) DEFAULT ''",
          'smtp_port'                   => "int(10) DEFAULT 25",
          'smtp_sender'                 => "varchar(255) DEFAULT ''",
          'smtp_sender_name'            => "varchar(255) DEFAULT ''",
          'smtp_server'                 => "varchar(255) DEFAULT ''",
          'smtp_ssl'                    => "int(1) DEFAULT 0",
          'smtp_username'               => "varchar(255) DEFAULT ''",
          'sql_server'                  => "varchar(255) DEFAULT ''",
          'sql_username'                => "varchar(255) DEFAULT ''",
          'sql_password'                => "varchar(255) DEFAULT ''",
          'sql_database'                => "varchar(255) DEFAULT ''",
          'sql_schema'                  => "varchar(255) DEFAULT ''",
          // Default SQL table names. If empty, the related data will be written to a file.
          'sql_config_table'            => "varchar(255) DEFAULT 'multiotp_config'",
          'sql_cache_table'             => "varchar(255) DEFAULT 'multiotp_cache'",
          'sql_devices_table'           => "varchar(255) DEFAULT 'multiotp_devices'",
          'sql_groups_table'            => "varchar(255) DEFAULT 'multiotp_groups'",
          'sql_log_table'               => "varchar(255) DEFAULT 'multiotp_log'",
          'sql_tokens_table'            => "varchar(255) DEFAULT 'multiotp_tokens'",
          'sql_users_table'             => "varchar(255) DEFAULT 'multiotp_users'",
          'syslog_facility'             => "int(10) DEFAULT 7",
          'syslog_level'                => "int(10) DEFAULT 5",
          'syslog_port'                 => "int(10) DEFAULT 514",
          'syslog_server'               => "varchar(255) DEFAULT ''",
          'tel_default_country_code'    => "varchar(255) DEFAULT ''",
          'timezone'                    => "varchar(255) DEFAULT 'Europe/Zurich'",
          'token_serial_number_length'  => "varchar(255) DEFAULT '12'",
          'token_otp_list_of_length'    => "varchar(255) DEFAULT '6'",
          'verbose_log_prefix'          => "varchar(255) DEFAULT ''",
          'encryption_hash'             => "varchar(255) DEFAULT ''");
      $this->_sql_tables_index['config']   = '**';
      $this->_sql_tables_ignore['config']  = '*backend_type*backend_type_validated*sql_server*sql_username*sql_password*sql_database*sql_schema*sql_config_table*';

      
      $this->_sql_tables_schema['devices'] = array(
          'device_id'                  => "varchar(255) DEFAULT ''",
          'cache_result_enabled'       => "int(1) DEFAULT 0",
          'cache_timeout'              => "int(10) DEFAULT 3600",
          'challenge_response_enabled' => "int(1) DEFAULT 0",
          'description'                => "varchar(255) DEFAULT ''",
          'device_secret'              => "varchar(255) DEFAULT ''",
          'force_no_prefix_pin'        => "int(1) DEFAULT 0",
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
          'category'                => "varchar(255) DEFAULT ''",
          'datetime'                => "datetime DEFAULT NULL",
          'destination'             => "varchar(255) DEFAULT ''",
          'last_update'             => "int(10) DEFAULT 0",
          'logentry'                => "text",
          'note'                    => "varchar(255) DEFAULT ''",
          'severity'                => "varchar(255) DEFAULT ''",
          'source'                  => "varchar(255) DEFAULT ''",
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
          'key_id'                  => "varchar(255) DEFAULT ''",
          'key_usage'               => "varchar(255) DEFAULT ''",
          'issue_no'                => "varchar(255) DEFAULT ''",
          'issuer'                  => "varchar(255) DEFAULT ''",
          'key_algorithm'           => "varchar(255) DEFAULT ''",
          'last_error'              => "int(10) DEFAULT 0",
          'last_event'              => "int(10) DEFAULT -1",
          'last_login'              => "int(10) DEFAULT 0",
          'last_update'             => "int(10) DEFAULT 0",
          'locked'                  => "int(1) DEFAULT 0",
          'manufacturer'            => "varchar(255) DEFAULT 'multiOTP'",
          'model'                   => "varchar(255) DEFAULT ''",
          'number_of_digits'        => "int(10) DEFAULT 6",
          'otp'                     => "varchar(255) DEFAULT ''",
          'private_id'              => "varchar(255) DEFAULT ''",
          'serial_no'               => "varchar(255) DEFAULT ''",
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
          'attributed_tokens'       => "varchar(255) DEFAULT ''",
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
          'dialin_ip_address'       => "varchar(255) DEFAULT ''",
          'dialin_ip_mask'          => "varchar(255) DEFAULT ''",
          'email'                   => "varchar(255) DEFAULT ''",
          // Login error counter
          'error_counter'           => "int(10) DEFAULT 0",
          'group'                   => "varchar(255) DEFAULT ''",
          'key_id'                  => "varchar(255) DEFAULT ''",
          'language'                => "varchar(255) DEFAULT ''",
          'last_cached_credential'  => "varchar(255) DEFAULT ''",
          // Last error login
          'last_error'              => "int(10) DEFAULT 0",
          // Last successful event
          'last_event'              => "int(10) DEFAULT -1",
          // Last successful login
          'last_failed_credential'  => "varchar(255) DEFAULT ''",
          'last_failed_time'        => "int(10) DEFAULT 0",
          'last_login'              => "int(10) DEFAULT 0",
          'last_login_for_cache'    => "int(10) DEFAULT 0",
          'last_success_credential' => "varchar(255) DEFAULT ''",
          'last_update'             => "int(10) DEFAULT 0",
          // LDAP password hash caching mechanism
          'ldap_hash_cache'         => "varchar(255) DEFAULT ''",
          'ldap_hash_validity'      => "int(10) DEFAULT 0",
          // Token locked
          'locked'                  => "int(1) DEFAULT 0",
          // User is a special multi-account user (the real user is in the token, like this: "user[space]token"
          'multi_account'           => "int(1) DEFAULT 0",
          // Number of digits returned by the token
          'number_of_digits'        => "int(10) DEFAULT 6",
          'private_id'              => "varchar(255) DEFAULT ''",
          // Request the LDAP password as a prefix of the returned token value
          'request_ldap_pwd'        => "int(1) DEFAULT 0",
          'request_prefix_pin'      => "int(1) DEFAULT 0",
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
          'user_last_login'         => "int(10) DEFAULT 0",
          'user_pin'                => "varchar(255) DEFAULT ''",
          'user_principal_name'     => "varchar(255) DEFAULT ''",
          'encryption_hash'         => "varchar(255) DEFAULT ''");
      $this->_sql_tables_index['users']    = '*attributed_tokens*desactivated*locked*user*';
      $this->_sql_tables_ignore['users']   = "**";
      $this->_sql_tables_not_in_schema['users'] = array(
          'delayed_account',
          'delayed_time',
          'delayed_finished');

      if ("" == $encryption_key) {
          $this->_encryption_key = 'MuLtIoTpEnCrYpTiOn'; // This default value should be changed for each project using SetEncryptionKey()
      } else {
          $this->_encryption_key = $encryption_key;
      }

      $current_dir = $base_dir;
      if ("" == $current_dir) {
          $env_folder_path = getenv('MULTIOTP_PATH');
          if (false !== $env_folder_path) {
              $current_dir = $env_folder_path;
          }
      }
      $this->SetBaseDir($current_dir);

      if ("" != trim($config_dir)) {
          $this->SetConfigFolder($config_dir, true, false);
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
      
      $this->_encryption_check        = true; // Check if the encryption hash is valid, default is true

      $this->_user                    = ""; // Name of the current user to authenticate
      $this->_user_data_read_flag     = false; // Flag to know if the data concerning the current user has been read
      $this->_users_folder            = ""; // Folders which contain the users flat files

      $this->_last_ldap_error         = false;
      $this->_log_file_name           = 'multiotp.log';
      $this->_log_flag                = false;
      $this->_log_folder              = ""; // Folder which contains the log file
      $this->_log_verbose_flag        = false;
      $this->_log_display_flag        = false;
      
      $this->_mysql_database_link     = NULL;
      $this->_mysqli                  = NULL;
      $this->_pgsql_database_link     = NULL;

      $this->_migration_from_file     = false; // To allow an automatic migration of users profiles,
                                               // enable a database backend and set the migration option ;-) !

      $this->_reply_array_for_radius = array();
      
      $this->_servers_temp_bad_list  = array();

      $this->_initialize_backend = $initialize_backend;
      
      $this->_debug_via_html = false;
      
      $this->_linux_file_mode = "";

      $this->_last_http_status = 0;

      $this->_bad_syslog_server = false;

      $this->ReadConfigData(true); // Read the configuration data, for the encryption information only
      if (("" == $encryption_key) || ('MuLtIoTpEnCrYpTiOn' == $encryption_key) || ('DefaultCliEncryptionKey' == $encryption_key)) {
          if (("" != $this->GetEncryptionKeyFullPath()) && file_exists($this->GetEncryptionKeyFullPath())) {
              if ($encryption_key_file_handler = @fopen($this->GetEncryptionKeyFullPath(), "rt")) {
                  $temp_encryption_key = trim(fgets($encryption_key_file_handler));
                  if ("" != $temp_encryption_key) {
                      $this->SetEncryptionKey($temp_encryption_key, false);
                  }
                  fclose($encryption_key_file_handler);
              }
          }
      }

      $this->_server_challenge = $this->GetEncryptionKey();

      $this->_keep_local = false;
      
      $this->_xml_dump_in_log = false; // For debugging purpose only
      
      $this->_sms_providers_array = array(array("aspsms", "aspsms", "http://www.aspsms.com/"),
                                          array("clickatell", "Clickatell", "http://www.clickatell.com/"),
                                          array("intellisms", "IntelliSMS", "http://www.intellisms.co.uk/")
                                         );

      // As various accounts are using the same files
      $this->SetLinuxFileMode('0666');
      $this->SetLinuxFolderMode('0777');
      
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
      if ($this->IsCacheData()) {
          $this->ReadCacheData();
      }

      $ldaptls_reqcert = $this->GetLdapTlsReqcert();
      if ('auto' == $ldaptls_reqcert) {
          if (mb_strtolower(substr(PHP_OS, 0, 3)) === 'win') {
              // Ignore the LDAP certificate validity (for Windows only)
              putenv('LDAPTLS_REQCERT=never');
          }
      } elseif ('' != $ldaptls_reqcert) {
          putenv('LDAPTLS_REQCERT='.$ldaptls_reqcert);
      }

      $ldaptls_cipher_suite = $this->GetLdapTlsCipherSuite();
      if ('auto' == $ldaptls_cipher_suite) {
          if (!version_compare(phpversion(), '7', '>=')) {
              // Don't handle the TLS1.2 protocol during LDAP synchronization
              // (not compatible with Windows 2012 implementation)
              // GnuTLS Cipher information: http://gnutls.org/manual/html_node/Priority-Strings.html
              putenv('LDAPTLS_CIPHER_SUITE=NORMAL:!VERS-TLS1.2');
          }
      } elseif ('' != $ldaptls_cipher_suite) {
          putenv('LDAPTLS_CIPHER_SUITE='.$ldaptls_cipher_suite);
      }
  }

  // Class destructor, also called in PHP4
  function __destruct()
  {
      /*
      if ($this->IsCacheData()) {
          $this->WriteCacheData();
      }
      */
  }


  function SetRadiusTagPrefix($value) {
      $this->_config_data['radius_tag_prefix'] = trim($value);
      return trim($value);
  }


  function GetRadiusTagPrefix() {
      return trim($this->_config_data['radius_tag_prefix']);
  }
  
  
  function SetLastFailedWhiteDelay(
    $delay
  ) {
    $this->_config_data['last_failed_white_delay'] = $delay;
  }


  function GetLastFailedWhiteDelay()
  {
    return $this->_config_data['last_failed_white_delay'];
  }


  function SetLdapRecursiveGroups($value) {
      $this->_config_data['ldap_recursive_groups'] = ((intval($value) > 0)?1:0);
  }


  function SetLdapCacheOn($value) {
      $this->_config_data['ldap_cache_on'] = ((intval($value) > 0)?1:0);
  }


  function SetLdapRecursiveCacheOnly($value) {
      $this->_config_data['ldap_recursive_cache_only'] = ((intval($value) > 0)?1:0);
  }


  function SetLdapCacheFolder($value) {
      $folder = $value;
      if ($this->ConvertToWindowsPathIfNeeded(sys_get_temp_dir()."/") == $value) {
          $folder = "tempdir";
      }
      $this->_config_data['ldap_cache_folder'] = trim($folder);
  }


  function IsLdapRecursiveGroups() {
      return (1 == $this->_config_data['ldap_recursive_groups']);
  }


  function IsLdapCacheOn() {
      return (1 == $this->_config_data['ldap_cache_on']);
  }


  function IsLdapRecursiveCacheOnly() {
      return (1 == $this->_config_data['ldap_recursive_cache_only']);
  }


  function GetLdapCacheFolder() {
      $folder = trim($this->_config_data['ldap_cache_folder']);
      if ("tempdir" == $folder) {
          $folder = $this->ConvertToWindowsPathIfNeeded(sys_get_temp_dir()."/");
      }
      if (file_exists($folder) && touch($folder."test.cache")) {
          unlink($folder."test.cache");
          if (!file_exists($folder.".ldap_cache/")) {

              if (@mkdir(
                      $folder.".ldap_cache/",
                      ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                      true //recursive
              )) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Debug: *LDAP cache folder created (".$folder.".ldap_cache/".")", FALSE, FALSE, 8888, 'System', '');
                  }
              }
          }
          if (file_exists($folder.".ldap_cache/") && touch($folder.".ldap_cache/test.cache")) {
              unlink($folder.".ldap_cache/test.cache");
              $folder = $folder.".ldap_cache/";
          }
      } else {
          $folder = "";
      }
      if ($this->GetVerboseFlag()) {
        $this->WriteLog("Debug: *LDAP cache folder value: $folder", FALSE, FALSE, 8888, 'System', '');
      }
      return $folder;
  }


  function EncodeForBackend($value) {
    $encoding = mb_strtolower($this->GetBackendEncoding());
    if (("utf-8" == $encoding) || ("utf8" == $encoding)) {
      $result = encode_utf8_if_needed($value);
    } else {
      $result = decode_utf8_if_needed($value);
    }
    return $result;
  }


  function SetBackendEncoding(
    $encoding
  ) {
    $this->_config_data['backend_encoding'] = $encoding;
  }


  function GetBackendEncoding()
  {
    return $this->_config_data['backend_encoding'];
  }


  function SetMultipleGroups(
      $value
  ) {
      $this->_config_data['multiple_groups'] = ((intval($value) > 0)?1:0);
  }


  function EnableMultipleGroups()
  {
      $this->_config_data['multiple_groups'] = 1;
  }


  function DisableMultipleGroups()
  {
      $this->_config_data['multiple_groups'] = 0;
  }


  function IsMultipleGroupsEnabled()
  {
      return (1 == ($this->_config_data['multiple_groups']));
  }


  function SetUserDialinIpAddress(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = TRUE;
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $result = $this->SetUser($first_param);
          $value = $second_param;
      }
      $this->_user_data['dialin_ip_address'] = $value;

      return $result;
  }


  function GetUserDialinIpAddress(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['dialin_ip_address'];
  }


  function SetUserDialinIpMask(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = TRUE;
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $result = $this->SetUser($first_param);
          $value = $second_param;
      }
      $this->_user_data['dialin_ip_mask'] = $value;

      return $result;
  }


  function GetUserDialinIpMask(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['dialin_ip_mask'];
  }


  function SetDefaultDialinIpMask($ip_mask)
  {
      $this->_config_data['default_dialin_ip_mask'] = $ip_mask;
      return TRUE;
  }


  function GetDefaultDialinIpMask()
  {
      return $this->_config_data['default_dialin_ip_mask'];
  }


  function SetLastHttpStatus($status = 200)
  {
      $this->_last_http_status = intval($status);
  }


  function GetLastHttpStatus()
  {
      return intval($this->_last_http_status);
  }


  function SetLockTime($value)
  {
    $this->_lock_time = trim($value);
  }


  function GetLockTime()
  {
    return trim($this->_lock_time);
  }


  function SetLdapSyncStopFileName($value)
  {
    $this->_ldap_sync_stop_file_name = trim($value);
  }


  function GetLdapSyncStopFileName()
  {
    return trim($this->_ldap_sync_stop_file_name);
  }


  function SetLdapSyncLockFileName($value)
  {
    $this->_ldap_sync_lock_file_name = trim($value);
  }


  function GetLdapSyncLockFileName()
  {
    return trim($this->_ldap_sync_lock_file_name);
  }


  function SetLockFolder($value)
  {
    $this->_lock_folder = trim($value);
  }


  function GetLockFolder()
  {
    return trim($this->_lock_folder);
  }


  function PurgeLockFolder()
  {
    $actual_folder = $this->GetLockFolder();
    $actual_filter = "multiotp-*.lock";
    if (($actual_dir = opendir($actual_folder)) !== FALSE) {
      while(($actual_file_name = readdir($actual_dir)) !== FALSE) {
        if (fnmatch($actual_filter, $actual_file_name)) {
          $actual_file = $actual_folder.$actual_file_name;
          unlink($actual_file);
        }
      }
    }
    return TRUE;
  }


  function PurgeLdapCacheFolder()
  {
    $actual_filter = "ldap_*.cache";
    $actual_folder = $this->GetLdapCacheFolder();
    if (($actual_dir = opendir($actual_folder)) !== FALSE) {
      while(($actual_file_name = readdir($actual_dir)) !== FALSE) {
        if (fnmatch($actual_filter, $actual_file_name)) {
          $actual_file = $actual_folder.$actual_file_name;
          unlink($actual_file);
        }
      }
    }
    $actual_folder = $this->ConvertToWindowsPathIfNeeded(sys_get_temp_dir()."/");
    if (($actual_dir = opendir($actual_folder)) !== FALSE) {
      while(($actual_file_name = readdir($actual_dir)) !== FALSE) {
        if (fnmatch($actual_filter, $actual_file_name)) {
          $actual_file = $actual_folder.$actual_file_name;
          unlink($actual_file);
        }
      }
    }
    return TRUE;
  }


  /**
   * @brief   Set an array of suffix(es) to use when folder is touched
   *
   * @param   array $touch_suffix_array   Array of suffix(es) to use when folder is touched
   *
   * @retval  n/a
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.3.6
   * @date    2017-02-12
   * @since   2017-02-10
   */
  function SetTouchSuffixArray($touch_suffix_array)
  {
    $this->_touch_suffix_array = $touch_suffix_array;
    return TRUE;
  }


  function GetTouchSuffixArray()
  {
    return ($this->_touch_suffix_array);
  }


  function SetTouchFolder($value)
  {
    $this->_touch_folder = trim($value);
  }


  function GetTouchFolder()
  {
    return trim($this->_touch_folder);
  }


  function PurgeTouchFolder()
  {
    $actual_folder = $this->GetTouchFolder();
    if ("" != $actual_folder) {
      $actual_filter = "*.sync";
      if (($actual_dir = opendir($actual_folder)) !== FALSE) {
        while(($actual_file_name = readdir($actual_dir)) !== FALSE) {
          if (fnmatch($actual_filter, $actual_file_name)) {
            $actual_file = $actual_folder.$actual_file_name;
            unlink($actual_file);
          }
        }
      }
    }
    return TRUE;
  }


  function TouchFolder(
    $type_fn = "",
    $item_fn = "",
    $id_fn = "",
    $folder_touched = TRUE,
    $touch_info = ""
  ) {
    $touch_suffix_array = $this->GetTouchSuffixArray();
    if (('' != $this->GetTouchFolder()) && (0 < count($touch_suffix_array))) {
      if ($this->GetVerboseFlag()) {
        $this->WriteLog("Debug: *Touch element $type_fn $item_fn $id_fn", FALSE, FALSE, 8888, 'System', '');
      }
      foreach ($touch_suffix_array as $one_touch_suffix) {
        $filename_path = $this->GetTouchFolder().bin2hex($type_fn."\t".$item_fn."\t".$id_fn.(("" != $one_touch_suffix) ? ("\t".$one_touch_suffix) : "")).".sync";
        $result = touch($filename_path);
        if ($result && ('' != $this->GetLinuxFileMode())) {
          @chmod($filename_path, octdec($this->GetLinuxFileMode()));
        }
      }

      if ($folder_touched) {
        $this->FolderTouched($type_fn, $item_fn, $id_fn, $touch_info);
      }
    }
  }


  function FolderTouched(
      $type_fn = "",
      $item_fn = "",
      $id_fn = "",
      $touch_info = "") {
      // Stub for your own extension
  }


  function UserRestoreBeforeWrite()
  {
      // Stub for your own extension
  }


  function SetUserLanguage($value) {
    $this->_user_data['language'] = trim($value);
  }


  function GetUserLanguage($ignore_main_language = FALSE) {
    $language = trim($this->_user_data['language']);
    if (('' == $language) && (TRUE != $ignore_main_language)) {
      $language = $this->GetLanguage();
    }
    return mb_strtolower($language);
  }


  function SetLanguage($value) {
    $this->_config_data['language'] = trim($value);
  }


  function GetLanguage() {
    return mb_strtolower(trim($this->_config_data['language']));
  }


  // Customized information (to be overcharged if needed)
  function GetCustomInfo() {
    return "";
  }


  function GetLibraryHash(
    $param1 = "",
    $param2 = "",
    $param3 = ""
  ) {
    if (file_exists(__FILE__)) {
      if ($me_handler = @fopen(__FILE__, "rt")) {
        $content = "";
        while (!feof($me_handler)) {
          $content.= fgets($me_handler);
        }
        fclose($me_handler);
        $hash = md5($content);
      }
    } else {
      $hash = '00000000000000000000000000000000';
    }

    $this->SendWeeklyAnonymousStat();

    return ($hash);
  }

  function UpgradeSchemaIfNeeded()
  {
    if ($this->GetActualVersion() != $this->GetVersion()) {
      if ($this->InitializeBackend() < 20) {
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

    $this->_errors_text[20] = "ERROR: User blacklisted";
    $this->_errors_text[21] = "ERROR: User doesn't exist";
    $this->_errors_text[22] = "ERROR: User already exists";
    $this->_errors_text[23] = "ERROR: Invalid algorithm";
    $this->_errors_text[24] = "ERROR: User locked (too many tries)";
    $this->_errors_text[25] = "ERROR: User delayed (too many tries, but still a hope in a few minutes)";
    $this->_errors_text[26] = "ERROR: This token has already been used";
    $this->_errors_text[27] = "ERROR: Resynchronization of the token has failed";
    $this->_errors_text[28] = "ERROR: Unable to write the changes in the file";
    $this->_errors_text[29] = "ERROR: Token doesn't exist";

    $this->_errors_text[30] = "ERROR: At least one parameter is missing";
    $this->_errors_text[31] = "ERROR: Tokens definition file doesn't exist";
    $this->_errors_text[32] = "ERROR: Tokens definition file not successfully imported";
    $this->_errors_text[33] = "ERROR: Encryption hash error, encryption key is not matching";
    $this->_errors_text[34] = "ERROR: Linked user doesn't exist";
    $this->_errors_text[35] = "ERROR: User not created";
    $this->_errors_text[36] = "ERROR: Token doesn't exist";
    $this->_errors_text[37] = "ERROR: Token already attributed";
    $this->_errors_text[38] = "ERROR: User is desactivated";
    $this->_errors_text[39] = "ERROR: Requested operation aborted";
   
    $this->_errors_text[40] = "ERROR: SQL query error";
    $this->_errors_text[41] = "ERROR: SQL error";
    $this->_errors_text[42] = "ERROR: They key is not in the table schema";
    $this->_errors_text[43] = "ERROR: SQL entry cannot be updated";

    $this->_errors_text[50] = "ERROR: QRcode not created";
    $this->_errors_text[51] = "ERROR: UrlLink not created (no provisionable client for this protocol)";
    $this->_errors_text[59] = "ERROR: Bad restore configuration password";

    $this->_errors_text[60] = "ERROR: No information on where to send SMS code";
    $this->_errors_text[61] = "ERROR: SMS code request received, but an error occurred during transmission";
    $this->_errors_text[62] = "ERROR: SMS provider not supported";
    $this->_errors_text[63] = "ERROR: This SMS code has expired";
    $this->_errors_text[64] = "ERROR: Cannot resent an SMS code right now";
    $this->_errors_text[69] = "ERROR: Failed to send email";
    
    $this->_errors_text[70] = "ERROR: Server authentication error";
    $this->_errors_text[71] = "ERROR: Server request is not correctly formatted";
    $this->_errors_text[72] = "ERROR: Server answer is not correctly formatted";
    $this->_errors_text[79] = "ERROR: AD/LDAP connection error";
    
    $this->_errors_text[80] = "ERROR: Server cache error";
    $this->_errors_text[81] = "ERROR: Cache too old for this user, account autolocked";
    $this->_errors_text[82] = "ERROR: User not allowed for this device";
    $this->_errors_text[88] = "ERROR: Device is not defined as a HA slave";
    $this->_errors_text[89] = "ERROR: Device is not defined as a HA master";

    $this->_errors_text[94] = "ERROR: API request error";
    $this->_errors_text[95] = "ERROR: API authentication failed";
    $this->_errors_text[96] = "ERROR: Authentication failed (CRC error)";
    $this->_errors_text[97] = "ERROR: Authentication failed (wrong private id)";
    $this->_errors_text[98] = "ERROR: Authentication failed (wrong token length)";
    $this->_errors_text[99] = "ERROR: Authentication failed (and other possible unknown errors)";
  }


  function GetErrorText(
    $error_number = 99
  ) {
    $text = "";
    if (isset($this->_errors_text[$error_number])) {
      $text = $this->_errors_text[$error_number];
    } elseif (intval($error_number) > 0) {
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
      $pos = mb_strpos(mb_strtoupper($valid_format), 'DEFAULT');
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

  
  /**
   * @brief   Write specific data in the backend (SQL table or file), generic method
   *
   * @param   array/string $item           New unique array, or Item family to be handled (Cache, Configuration, Token, etc.)
   * @param   string  $table               Name of the table if the backend is handling tables
   * @param   string  $folder              Name of the folder if the backend is handling folders
   * @param   string  $data_array          Data array of the item to be written
   * @param   boolean $force_file          File backend must also always be used
   * @param   string  $id_field            Index field of the item if the backend is handling tables
   * @param   string  $id_value            Value of the indexed item
   * @param   string  $id_case_sensitive   We want to be case sensitive for the backend storage
   * @param   boolean $automatically       The process is done automatically (for long content only)
   * @param   boolean $update_last_change  Update the last_update field (true by default)
   * @param   boolean $no_encryption_hash  No encryption hash, and no field are encrypted (false by default)
   *
   * New unique array parameter
   *   string  item                Item family to be handled (Cache, Configuration, Token, etc.) or new array
   *   string  table               Name of the table if the backend is handling tables
   *   string  folder              Name of the folder if the backend is handling folders
   *   string  data_array          Data array of the item to be written
   *   boolean force_file          File backend must also always be used
   *   string  id_field            Index field of the item if the backend is handling tables
   *   string  id_value            Value of the indexed item
   *   string  id_case_sensitive   We want to be case sensitive for the backend storage
   *   boolean automatically       The process is done automatically (for long content only)
   *   boolean update_last_change  Update the last_update field (true by default)
   *   boolean no_encryption_hash  No encryption hash, and no field are encrypted (false by default)
   *   boolean encrypt_all         Encrypt all lines (if used with no_encryption_hash, no encryption hash are generated)
   *   string  encryption_key      Specific encryption key (if not defined, the default one is used)
   *   string  backup_file         Name of the backup file (if defined, everything is written in a backup file)
   *   string  raw_folder          Folder of a raw file to be backed up in configuration file
   *   string  raw_file            Raw file to be backed up
   *   boolean return_content      Return the content as the result 
   *   boolean flush_attributes    Table of attributes to flush before writing
   *
   * @retval  boolean              Result of the operation
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 4.3.4.3
   * @date    2016-06-11
   * @since   2014-12-30
   */
  function WriteData(
      $write_data_array         = '',
      $table_param              = '',
      $folder_param             = '',
      $data_array_param         = array(),
      $force_file_param         = false,
      $id_field_param           = '',
      $id_value_param           = '',
      $id_case_sensitive_param  = false,
      $automatically_param      = false,
      $update_last_change_param = true,
      $no_encryption_hash_param = false
  ) {
      if (is_array($write_data_array)) {
        $item               = isset($write_data_array['item'])?$write_data_array['item']:'';
        $table              = isset($write_data_array['table'])?$write_data_array['table']:'';
        $folder             = isset($write_data_array['folder'])?$write_data_array['folder']:'';
        $data_array         = isset($write_data_array['data_array'])?$write_data_array['data_array']:array();
        $force_file         = isset($write_data_array['force_file'])?$write_data_array['force_file']:false;
        $id_field           = isset($write_data_array['id_field'])?$write_data_array['id_field']:'';
        $id_value           = isset($write_data_array['id_value'])?$write_data_array['id_value']:'';
        $id_case_sensitive  = isset($write_data_array['id_case_sensitive'])?$write_data_array['id_case_sensitive']:false;
        $automatically      = isset($write_data_array['automatically'])?$write_data_array['automatically']:false;
        $update_last_change = isset($write_data_array['update_last_change'])?$write_data_array['update_last_change']:true;
        $no_encryption_hash = isset($write_data_array['no_encryption_hash'])?$write_data_array['no_encryption_hash']:false;

        $encrypt_all        = isset($write_data_array['encrypt_all'])?$write_data_array['encrypt_all']:false;
        $encryption_key     = isset($write_data_array['encryption_key'])?$write_data_array['encryption_key']:'';
        $backup_file        = isset($write_data_array['backup_file'])?$write_data_array['backup_file']:'';
        $raw_folder         = isset($write_data_array['raw_folder'])?$write_data_array['raw_folder']:'';
        $raw_file           = isset($write_data_array['raw_file'])?$write_data_array['raw_file']:'';
        $return_content     = isset($write_data_array['return_content'])?$write_data_array['return_content']:false;
        $flush_attributes   = isset($write_data_array['flush_attributes'])?$write_data_array['flush_attributes']:array();
        $encode_file_id     = isset($write_data_array['encode_file_id'])?$write_data_array['encode_file_id']:false;
      } else {
        // Backward compatibility
        $item               = $write_data_array;
        $table              = $table_param;
        $folder             = $folder_param;
        $data_array         = $data_array_param;
        $force_file         = $force_file_param;
        $id_field           = $id_field_param;
        $id_value           = $id_value_param;
        $id_case_sensitive  = $id_case_sensitive_param;
        $automatically      = $automatically_param;
        $update_last_change = $update_last_change_param;
        $no_encryption_hash = $no_encryption_hash_param;
        $encrypt_all        = false;
        $encryption_key     = '';
        $backup_file        = '';
        $raw_folder         = '';
        $raw_file           = '';
        $return_content     = false;
        $flush_attributes   = array();
      }
      $backup_format = ('' != $backup_file);
      if ($backup_format) {
        $force_file = true;
        // if ((false !== mb_strpos($backup_file,"/")) || (false !== mb_strpos($backup_file,"\\"))) {
          $folder = '';
        // }
      }

      foreach($flush_attributes as $one_flush_attribute) {
        if (isset($data_array[$one_flush_attribute])) {
          $data_array[$one_flush_attribute] = '';
        }
      }

      if ('' != $raw_file) {
        if (!file_exists($raw_folder.$raw_file)) {
          if ('' == $raw_folder) {
            $raw_folder = $folder;
          }
        }
        if (!file_exists($raw_folder.$raw_file)) {
          return false;
        }
      }
      $clean_raw_folder = str_replace($this->GetConfigFolder(), "--config-@-folder--", $raw_folder);

      if ('*CLEAR*' == $encryption_key) {
          $encryption_key = '';
      } elseif ('' == $encryption_key) {
          $encryption_key = $this->GetEncryptionKey();
      }

      $item_info = trim($item." ".$id_value);
      if ('group' == mb_strtolower($item)) {
          $item_info = trim($item." ".(isset($data_array['name'])?$data_array['name']:$id_value));
      } elseif ('device' == mb_strtolower($item)) {
          $item_info = trim($item." ".(isset($data_array['description'])?$data_array['description']:(isset($data_array['ip_or_fqdn'])?$data_array['ip_or_fqdn']:$id_value)));
      }

      if ('configuration' == mb_strtolower($item)) {
          $filename = 'multiotp.ini';
          $force_file = true;
      } elseif ('cache' == mb_strtolower($item)) {
          $filename = 'cache.ini';
      } else {
          if ($encode_file_id) {
              $filename = $this->EncodeFileId($id_value, $this->IsCaseSensitiveUsers()).'.db';
          } else {
              $filename = $id_value.'.db';
              if (!$this->IsCaseSensitiveUsers()) {
                  $filename = mb_strtolower($filename);
              }
          }
      }

      if ($backup_format) {
        $filename = $backup_file;
        $item_info = "backup process";
      }

      $now_epoch = time();
      if ($update_last_change) {
          $data_array['last_update'] = $now_epoch;
      }
      $result = false;

      $item_created = FALSE;
      
      $data_array['encryption_hash'] = $this->CalculateControlHash($encryption_key);

      if($no_encryption_hash) {
        unset($data_array['encryption_hash']);
      }

      if ((($this->GetBackendTypeValidated()) &&
           ((isset($this->_config_data['sql_'.$table.'_table'])) && ('' != $this->_config_data['sql_'.$table.'_table']))
          ) || 
          ('files' == $this->GetBackendType()) ||
          $force_file ||
          $backup_format
         ) {
          if (('files' == $this->GetBackendType()) || $force_file || $backup_format) {
              $file_time = $now_epoch;
              if (!$id_case_sensitive) {
                  $filename = mb_strtolower($filename);
              }
              $file_created = false;

              // We open a handler only if it's not a return content request
              if ('@' != mb_strtolower($filename)) {
                if (!file_exists($folder.$filename)) {
                    $item_created = true;
                    $file_created = true;
                } elseif ((!$update_last_change) && (!$file_created)) {
                    $file_time = filemtime($folder.$filename);
                }

                if ($backup_format) {
                  $file_handler = @fopen($folder.$filename,"ab+");
                  if (FALSE === $file_handler) {
                    usleep(500000); // Wait 0.5 seconds before retry to have more chance to do the job
                    $file_handler = @fopen($folder.$filename,"ab+");
                  }
                } else {
                  $file_handler = @fopen($folder.$filename, "wt"); // was wt
                  if (FALSE === $file_handler) {
                    usleep(500000); // Wait 0.5 seconds before retry to have more chance to do the job
                    $file_handler = @fopen($folder.$filename, "wt"); // was wt
                  }
                }
              } else {
                $file_handler = TRUE;
                $return_content = TRUE;
              }
              $line = "";

              if (FALSE === $file_handler) {
                  $this->WriteLog("Error: database file for ".$item_info." cannot be written", FALSE, FALSE, 28, 'System', '', 3);
              } else {
                if ($backup_format) {
                  if ($file_created) {
                    $config_time = date("YmdHis");
                    $line.= "# CONFIGURATION /".$config_time."/".substr(md5($config_time.$encryption_key.$config_time),0,14)."/\n";
                    $line.= "; #!#multiotp-database-format-v3\n";
                    $line.= "; #!#timestamp=".time()."\n";
                    $line.= "; #!#encryption_hash=".$this->CalculateControlHash($encryption_key)."\n";
                  }
                  $line.= "; #!#element-start#!#\n";
                  $line.= "; #!#element-timestamp=".time()."\n";
                  if (($backup_format) && ('' != $raw_file)) {
                    $line.= "; #!#type=file\n";
                    $line.= "; #!#item=$clean_raw_folder\n";
                    $line.= "; #!#id_value=$raw_file\n";
                  } else {
                    $line.= "; #!#type=data\n";
                    $line.= "; #!#item=$item\n";
                    $line.= "; #!#id_value=$id_value\n";
                  }
                } else {
                  $line.= "multiotp-database-format-v3\n";
                  if ('configuration' == mb_strtolower($item)) {
                    $line.= "; If backend is set to something different than files,\n";
                    $line.= "; and backend_type_validated is set to 1,\n";
                    $line.= "; only the specific information needed for the backend\n";
                    $line.= "; is used from this config file.\n";
                    $line.= "\n";
                  }
                }
                if (($backup_format) && ('' != $raw_file)) {
                  $key = "raw_data";
                  if ($raw_fn = @fopen($raw_folder.$raw_file, "rb")) {
                    while(!feof($raw_fn))
                    {
                      $line.= mb_strtolower($key);
                      $value = bin2hex(fread($raw_fn, 40));
                      if ($encrypt_all ||
                          ((!$no_encryption_hash) &&
                           ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$key.'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt())))
                          )
                         ) {
                        $value = $this->Encrypt($key,$value,$encryption_key);
                        $line.= ":";
                      }
                      $line.= "=".$value."\n";
                    }
                    fclose($raw_fn);
                  }
                } else {
                  // foreach (array() as $key => $value) // this is not working well in PHP4
                  reset($data_array);
                  while(list($key, $value) = each($data_array)) {
                    if ('' != trim($key)) {
                      $line.= mb_strtolower($key);
                      if ($encrypt_all ||
                          ((!$no_encryption_hash) &&
                           ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$key.'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt())))
                          )
                         ) {
                        $value = $this->Encrypt($key,$value,$encryption_key);
                        $line.= ":";
                      }
                      $line.= "=".$value."\n";
                    }
                  }
                } // (not a raw file)
                if ($backup_format) {
                  $line.= "; #!#element-stop#!#\n";
                  $line.= "; ##############################\n";
                }
                if ('@' != mb_strtolower($filename)) {
                  // foreach (explode("\n", $line) as $one_line) {
                  //   fwrite($file_handler, encode_utf8_if_needed($one_line)."\n");
                  // }
                  fwrite($file_handler, $line);
                  fclose($file_handler);
                }
                $result = $return_content ? $line : true;
                if ((!$update_last_change) && (!$file_created) && (!$backup_format)) {
                  touch($folder.$filename, $file_time);
                }
                if ($file_created && ('' != $this->GetLinuxFileMode())) {
                  @chmod($folder.$filename, octdec($this->GetLinuxFileMode()));
                }
              }
              if ($this->GetVerboseFlag()) {
                if ($file_created) {
                  $this->WriteLog("Info: *File created: ".$folder.$filename, FALSE, FALSE, 8888, 'System', '');
                }
              }                    
          }
          if ((!$backup_format) && ('mysql' == $this->GetBackendType())) {
              $esc_id_value = escape_mysql_string($id_value);
              if ($this->OpenMysqlDatabase()) {
                  $result = TRUE;
                  $sQi_Columns = '';
                  $sQi_Values  = '';
                  $sQu_Data    = '';
                  reset($data_array);
                  while(list($key, $value) = each($data_array)) {
                      $in_the_schema = FALSE;
                      reset($this->_sql_tables_schema[$table]);
                      $row_type = "";
                      while(list($valid_key, $valid_format) = each($this->_sql_tables_schema[$table])) {
                          $row_type = "";
                          if ((mb_strtolower(substr($valid_format, 0, 4)) == "int(") || (mb_strtolower(substr($valid_format, 0, 8)) == "numeric(")) {
                            $row_type = "int";
                          } elseif ((mb_strtolower(substr($valid_format, 0, 8)) == "datetime") || (mb_strtolower(substr($valid_format, 0, 9)) == "timestamp")) {
                            $row_type = "datetime";
                          }
                          if ($valid_key == $key) {
                              $in_the_schema = TRUE;
                              break;
                          }
                      }
                      $not_in_the_schema = FALSE;
                      if (isset($this->_sql_tables_not_in_schema[$table])) {
                          reset($this->_sql_tables_not_in_schema[$table]);
                          while(list($ignore_key, $ignore_format) = each($this->_sql_tables_not_in_schema[$table])) {
                              if ($ignore_key == $key) {
                                  $not_in_the_schema = TRUE;
                                  break;
                              }
                          }
                      }

                      if (($in_the_schema) && ($key != $id_field)) {
                          if (('' == trim($value)) && ("int" == $row_type)) {
                              $value = 0;
                          }
                          if (($encrypt_all ||
                               ((!$no_encryption_hash) &&
                                ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$key.'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt())))
                               )
                              ) &&
                              ('' != $value)
                             ) {
                            $value = 'ENC:'.$this->Encrypt($key,$value,$encryption_key).':ENC';
                          }
                          $value = escape_mysql_string($value);
                          $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                          $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                          $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                      } elseif ((!$in_the_schema) && (!$not_in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                          $this->WriteLog("Warning: *The key ".$key." is not in the $table table schema", FALSE, FALSE, 8888, 'System', '');
                      }
                  }
                  $num_rows = 0;
                  $sQuery = "SELECT * FROM `".$this->_config_data['sql_'.$table.'_table']."`";
                  if ('' != $id_field) {
                      $sQuery.= " WHERE `$id_field`='".$esc_id_value."'";
                  }
                  
                  if (is_object($this->_mysqli)) {
                      if (!($result = @$this->_mysqli->query($sQuery))) {
                          $this->WriteLog("Error: SQL query error ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 40, 'System', '', 3);
                      } else {
                          $num_rows = $result->num_rows;                                    
                      }
                  } elseif (!($result = @mysql_query($sQuery, $this->_mysql_database_link))) {
                      $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                  } else {
                      $num_rows = mysql_num_rows($result);
                  }

                  if ($num_rows > 0) {
                      $sQuery = "UPDATE `".$this->_config_data['sql_'.$table.'_table']."` SET ".substr($sQu_Data,0,-1);
                      if ('' != $id_field) {
                          $sQuery.= " WHERE `$id_field`='".$esc_id_value."'";
                      }
                      if (is_object($this->_mysqli)) {
                          if (!($rResult = @$this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: SQL query error ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 40, 'System', '', 3);
                              $result = FALSE;
                          }
                      } elseif (!($rResult = @mysql_query($sQuery, $this->_mysql_database_link))) {
                          $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                          $result = FALSE;
                      }
                  } else {
                      if ('' != $id_field) {
                          $sQuery = "INSERT INTO `".$this->_config_data['sql_'.$table.'_table']."` (`$id_field`,".substr($sQi_Columns,0,-1).") VALUES ('".$esc_id_value."',".substr($sQi_Values,0,-1).")";
                      } else {
                          $sQuery = "INSERT INTO `".$this->_config_data['sql_'.$table.'_table']."` (".substr($sQi_Columns,0,-1).") VALUES (".substr($sQi_Values,0,-1).")";
                      }
                      if (is_object($this->_mysqli)) {
                          if (!($rResult = @$this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: SQL query error ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 40, 'System', '', 3);
                          } elseif (0 == $this->_mysqli->affected_rows) {
                              $this->WriteLog("Error: SQL entry for ".$item_info." cannot be created or changed", FALSE, FALSE, 43, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $item_created = TRUE;
                          }
                      } elseif (!($rResult = @mysql_query($sQuery, $this->_mysql_database_link))) {
                          $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                          $result = FALSE;
                      } elseif (0 == mysql_affected_rows($this->_mysql_database_link)) {
                          $this->WriteLog("Error: SQL entry for ".$item_info." cannot be created or changed", FALSE, FALSE, 43, 'System', '', 3);
                          $result = FALSE;
                      } else {
                          $item_created = TRUE;
                      }
                  }
              }
          } elseif ((!$backup_format) && ('pgsql' == $this->GetBackendType())) {
              $esc_id_value = pg_escape_string($id_value);
              if ($this->OpenPGSQLDatabase()) {
                  $result = TRUE;
                  $sQi_Columns = '';
                  $sQi_Values  = '';
                  $sQu_Data    = '';
                  reset($data_array);
                  while(list($key, $value) = each($data_array)) {
                      $in_the_schema = FALSE;
                      reset($this->_sql_tables_schema[$table]);
                      $row_type = "";
                      while(list($valid_key, $valid_format) = each($this->_sql_tables_schema[$table])) {
                          $row_type = "";
                          if ((mb_strtolower(substr($valid_format, 0, 4)) == "int(") || (mb_strtolower(substr($valid_format, 0, 8)) == "numeric(")) {
                            $row_type = "int";
                          } elseif ((mb_strtolower(substr($valid_format, 0, 8)) == "datetime") || (mb_strtolower(substr($valid_format, 0, 9)) == "timestamp")) {
                            $row_type = "datetime";
                          }
                          if ($valid_key == $key) {
                              $in_the_schema = TRUE;
                              break;
                          }
                      }
                      $not_in_the_schema = FALSE;
                      if (isset($this->_sql_tables_not_in_schema[$table])) {
                          reset($this->_sql_tables_not_in_schema[$table]);
                          while(list($ignore_key, $ignore_format) = each($this->_sql_tables_not_in_schema[$table])) {
                              if ($ignore_key == $key) {
                                  $not_in_the_schema = TRUE;
                                  break;
                              }
                          }
                      }

                      if (($in_the_schema) && ($key != $id_field)) {
                          if (('' == trim($value)) && ("int" == $row_type)) {
                              $value = 0;
                          }
                          if (($encrypt_all ||
                               ((!$no_encryption_hash) &&
                                ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$key.'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt())))
                               )
                              ) &&
                              ('' != $value)
                             ) {
                            $value = 'ENC:'.$this->Encrypt($key,$value,$encryption_key).':ENC';
                          }
                          $value = pg_escape_string($value);
                          $sQu_Data    .= "\"{$key}\" = '{$value}',"; // Data for UPDATE query
                          $sQi_Columns .= "\"{$key}\","; // Columns for INSERT query
                          $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                      } elseif ((!$in_the_schema) && (!$not_in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                          $this->WriteLog("Warning: *The key ".$key." is not in the $table table schema", FALSE, FALSE, 8888, 'System', '');
                      }
                  }
                  $num_rows = 0;
                  $sQuery = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_'.$table.'_table']."\"";
                  if ('' != $id_field) {
                      $sQuery.= " WHERE \"".$id_field."\" = '".$esc_id_value."'";
                  }
                  
                  if (!($result = @pg_query($this->_pgsql_database_link, $sQuery))) {
                      $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                  } else {
                      $num_rows = pg_num_rows($result);
                  }

                  if ($num_rows > 0) {
                      $sQuery = "UPDATE \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_'.$table.'_table']."\" SET ".substr($sQu_Data,0,-1);
                      if ('' != $id_field) {
                          $sQuery.= " WHERE \"".$id_field."\" = '".$esc_id_value."'";
                      }
                      if (!($rResult = @pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                          $result = FALSE;
                      }
                  } else {
                      if ('' != $id_field) {
                          $sQuery = "INSERT INTO \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_'.$table.'_table']."\" (\"".$id_field."\",".substr($sQi_Columns,0,-1).") VALUES ('".$esc_id_value."',".substr($sQi_Values,0,-1).")";
                      } else {
                          $sQuery = "INSERT INTO \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_'.$table.'_table']."\" (".substr($sQi_Columns,0,-1).") VALUES (".substr($sQi_Values,0,-1).")";
                      }
                      if (!($rResult = @pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                          $result = FALSE;
                      } elseif (0 == pg_affected_rows($rResult)) {
                          $this->WriteLog("Error: SQL entry for ".$item_info." cannot be created or changed", FALSE, FALSE, 43, 'System', '', 3);
                          $result = FALSE;
                      } else {
                          $item_created = TRUE;
                      }
                  }
              }
          }
      }
      if (!$backup_format) {
          if ($item_created && $result) {
              if ($automatically) {
                  $this->WriteLog("Info: ".$item_info." automatically created", FALSE, FALSE, 19, 'System', '');
              }
              else {
                  $this->WriteLog("Info: ".$item_info." manually created", FALSE, FALSE, 19, 'System', '');
              }
          }
      }

      if ((!$backup_format) && ($update_last_change) && ('cache' != mb_strtolower($item))) {
        $this->TouchFolder(('' != $raw_file) ? 'file' : 'data',
                           ('' != $raw_file) ? $clean_raw_folder : $item,
                           ('' != $raw_file) ? $raw_file : $id_value,
                           TRUE,
                           "WriteData");
      }

      return $result;
  }


  function ReadCacheValue(
      $key
  ) {
      return ((isset($this->_cache_data[$key]))?$this->_cache_data[$key]:"");
  }


  function WriteCacheValue(
      $key,
      $value
  ) {
      $this->_cache_data[$key] = $value;
  }


  function ReadCacheData()
  {
      $this->ResetCacheArray();
      $result = false;
      
      // First, we read the cache file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $cache_filename = 'cache.ini'; // File exists in v3 format only, we don't need any conversion
          if (file_exists($this->GetCacheFolder().$cache_filename)) {
              if ($file_handler = @fopen($this->GetCacheFolder().$cache_filename, "rt")) {
                  $first_line = trim(fgets($file_handler));
                  
                  while (!feof($file_handler)) {
                      $line = str_replace(chr(10), "", str_replace(chr(13), "", fgets($file_handler)));
                      $line_array = explode("=",$line,2);
                      if (('#' != substr($line, 0, 1)) && (';' != substr($line, 0, 1)) && ("" != trim($line)) && (isset($line_array[1]))) {
                          if ("" != $line_array[0]) {
                              $this->_cache_data[mb_strtolower($line_array[0])] = $line_array[1];
                          }
                      }
                  }
                  fclose($file_handler);
                  $result = TRUE;
              }
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
                                  $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 41, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = $result->fetch_assoc();
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: ".mysql_error()." ".$sQuery, TRUE, FALSE, 41, 'System', '', 3);
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
                                          break;
                                      }
                                  }
                                  if ($in_the_schema) {
                                      $this->_cache_data[$key] = $value;
                                  } elseif (('unique_id' != $key) && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *the key ".$key." is not in the cache database schema", FALSE, FALSE, 8888, 'System', '');
                                  }
                              }
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ("" != $this->_config_data['sql_cache_table']) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_cache_table']."\" ";
                          
                          $aRow = NULL;

                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: ".pg_last_error()." ".$sQuery, TRUE, FALSE, 41, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $aRow = pg_fetch_assoc($rResult);
                          }

                          if (NULL != $aRow) {
                              $result = TRUE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['cache']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['cache'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if ($in_the_schema) {
                                      $this->_cache_data[$key] = $value;
                                  } elseif (('unique_id' != $key) && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *the key ".$key." is not in the cache database schema", FALSE, FALSE, 8888, 'System', '');
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


  function WriteCacheData(
      $write_cache_data_array = array()
  ) {
      $result = $this->WriteData(array_merge(array('item'               => 'Cache',
                                                   'table'              => 'cache',
                                                   'folder'             => $this->GetCacheFolder(),
                                                   'data_array'         => $this->_cache_data
                                                  ), $write_cache_data_array));
      return $result;
  }


  // Reset the config array
  function ResetConfigArray($array_to_reset = '')
  {
      if (!is_array($array_to_reset)) {
        $array_to_reset = $this->_sql_tables_schema['config'];
      }
      // First, we reset all values (we know the key based on the schema)
      reset($array_to_reset);
      while(list($valid_key, $valid_format) = @each($array_to_reset)) {
          $pos = mb_strpos(mb_strtoupper($valid_format), 'DEFAULT');
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


  function SetAnonymousStat(
      $value
  ) {
      $this->_config_data['anonymous_stat'] = ((intval($value) > 0)?1:0);
  }


  function EnableAnonymousStat()
  {
      $this->_config_data['anonymous_stat'] = 1;
  }


  function DisableAnonymousStat()
  {
      $this->_config_data['anonymous_stat'] = 0;
  }


  function IsAnonymousStat()
  {
      return (1 == ($this->_config_data['anonymous_stat']));
  }


  function SetLdapExpiredPasswordValid(
      $value
  ) {
      $this->_config_data['ldap_expired_password_valid'] = ((intval($value) > 0)?1:0);
  }


  function EnableLdapExpiredPasswordValid()
  {
      $this->_config_data['ldap_expired_password_valid'] = 1;
  }


  function DisableLdapExpiredPasswordValid()
  {
      $this->_config_data['ldap_expired_password_valid'] = 0;
  }


  function IsLdapExpiredPasswordValid()
  {
      return (1 == ($this->_config_data['ldap_expired_password_valid']));
  }
  
  
  function IsAnonymousStatTime()
  {
      return ($this->IsAnonymousStat() && (0 != $this->GetAnonymousStatInterval()) && (time() > ($this->GetAnonymousStatLastUpdate() + $this->GetAnonymousStatInterval())));
  }


  function GetAnonymousStatRandomId()
  {
      $result = trim($this->_config_data['anonymous_stat_random_id']);
      if ('' == $result) {
          $result = substr(md5(date("YmdHis").mt_rand(100000,999999)),0,20).substr(sha1(mt_rand(100000,999999).date("YmdHis")),0,20);
          $this->_config_data['anonymous_stat_random_id'] = $result;
      }
      return ($result);
  }
  
  
  function GetAnonymousStatLastUpdate()
  {
      return intval($this->_config_data['anonymous_stat_last_update']);
  }


  function UpdateAnonymousStatLastUpdate()
  {
      $this->_config_data['anonymous_stat_last_update'] = time();
  }


  function GetAnonymousStatInterval()
  {
    return ($this->_anonymous_stat_interval);
  }


  function SendWeeklyAnonymousStat()
  {
    if ($this->IsAnonymousStatTime()) {
      $result_stats = FALSE;
      $stats_array = array();
      $stats_array['id'] = sha1($this->GetAnonymousStatRandomId().$this->GetClassName());
      $stats_array['backend_type'] = $this->GetBackendType();
      $stats_array['class_name'] = $this->GetClassName();
      $stats_array['ldap_cn_identifier'] = $this->GetLdapCnIdentifier();
      $stats_array['ldap_enabled'] = ('' != $this->GetLdapDomainControllers());
      $stats_array['ldap_sync_user_attribute'] = $this->GetLdapSyncedUserAttribute();
      $stats_array['os'] = php_uname();
      if ($this->GetCliProxyMode()) {
          $stats_array['os'].= " [CLI PROXY]";
      } elseif ($this->GetCliMode()) {
          $stats_array['os'].= " [CLI]";
      }
      if ($this->GetCredentialProviderMode()) {
          $stats_array['os'].= " [CP]";
      }
      $stats_array['php'] = phpversion();
      $stats_array['tokens'] = intval($this->GetTokensCount());
      $stats_array['users'] = intval($this->GetUsersCount());
      $stats_array['version_date'] = $this->GetVersionDate();
      $stats_public_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtfZPCfqhemvRagng961LMbBHKVb2B3uSUsWlkcOqu5EuXfRIyIqurJR7vigJ0GMu4zMvrBilQQYegCjXtceo03mGSthAJ+6rTZ9Qvlu7GY0CUgUCranZ8Ckw8EXdEiUNTdgK1pKm6kef+wK4wc3V/sU+XYo8gbMbH9C5YsG/XUon4hPx+FSuNNU1IX/GhTcHo7Tmc5+kZZw4ImCGAsrXO/N4qYcn9Y11HceKiRyglAdRoBhM/pbhzl1rgSVxnfUu6R0NBDWRVW8l3NMkp1He8ugzP5dca2cdBYdIgslNKQwzGccWsxDEAkK1Q6htjmQ85g+qv2hiShEpOI/EiWw3uwIDAQAB";
      $rsa = new Crypt_RSA();
      $rsa->loadKey($stats_public_key);
      $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_OAEP);
      $encoded_stats_value = urlencode(base64_encode($rsa->encrypt(json_encode($stats_array))));
      $result_stats = $this->PostHttpDataXmlRequest($encoded_stats_value, "http://stats.multiotp.net/", 5);
      // if (FALSE !== mb_strpos($result_stats, "OK")) {
      // We have to upgrade the anonymous last update even if the answer id not correct, because we could be offline

      if ((FALSE !== mb_strpos($result_stats, "<infoweb>")) && (FALSE !== mb_strpos($result_stats, "</infoweb>"))) {
          $infoweb_start = mb_strpos($result_stats, "<infoweb>") + strlen("<infoweb>");
          $infoweb_stop = mb_strpos($result_stats, "</infoweb>");
          if ($infoweb_stop > $infoweb_start) {
              $infoweb = substr($result_stats, $infoweb_start, ($infoweb_stop - $infoweb_start));
              $infoweb_filename = "infoweb.html";
              if ($infoweb_handler = @fopen($multiotp->GetConfigFolder().$infoweb_filename, "wt")) {
                  fwrite($write, $infoweb);
                  fclose($write);
                  if ('' != $this->GetLinuxFileMode()) {
                      @chmod($multiotp->GetConfigFolder().$infoweb_filename, octdec($this->GetLinuxFileMode()));
                  }
              }
          }
      }

      $this->UpdateAnonymousStatLastUpdate();
      $this->WriteConfigData();

      /*
      if ($this->GetVerboseFlag()) {
        $stats_info = "";
        reset($stats_array);
        while(list($stats_key, $stats_value) = each($stats_array)) {
          $stats_info.= (("" != $stats_info) ? "; " : "") . "$stats_key=$stats_value";
        }
        $this->WriteLog("Debug: *Stats info: $stats_info", FALSE, FALSE, 8888, 'System', '');
      }
      */
    }    
  }


  /**
   * @brief   Backup the whole configuration in a unified format
   *            The regular functions are used with specific parameters:
   *            - WriteConfigData
   *            - WriteDeviceData
   *            - WriteGroupData
   *            - WriteTokenData
   *            - WriteUserData
   *
   * @param   array $bc_array string  'backup_file'    : name of the backup file to create
   *                          boolean 'return_content' : return content instead of saving in the file
   *                          string  'encryption_key' : encryption key
   *                          boolean 'encrypt_all'    : encrypt all attributes
   * @retval  string Content of the file to backup
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.2.6
   * @date    2016-10-25
   * @since   2016-05-03
   */
  function BackupConfiguration(
    $bc_array = array()
  ) {
    @set_time_limit(0); // It can take a lot of time...
    clearstatcache();
    $bc_array['backup_file']       = isset($bc_array['backup_file'])       ? $bc_array['backup_file']:"@";
    $bc_array['encryption_key']    = isset($bc_array['encryption_key'])    ? $bc_array['encryption_key']:'';
    $bc_array['return_content']    = isset($bc_array['return_content'])    ? $bc_array['return_content']:FALSE;
    $bc_array['encrypt_all']       = isset($bc_array['encrypt_all'])       ? $bc_array['encrypt_all']:TRUE;
    // $bc_array['ignore_attributes'] = isset($bc_array['ignore_attributes']) ? $bc_array['ignore_attributes']:array();
    $bc_array['config_only']       = isset($bc_array['config_only'])       ? $bc_array['config_only']:FALSE;

    // Do not create an encryption hash entry in the items of the backup
    $bc_array['no_encryption_hash'] = TRUE;

    // Do not update the last change date in the items of the backup
    $bc_array['update_last_change'] = FALSE;
    if (('' == $bc_array['backup_file']) || ('@' == $bc_array['backup_file'])) {
      $bc_array['backup_file'] = "@";
      $bc_array['return_content'] = TRUE;
    } else {
      if (file_exists($bc_array['backup_file'])) {
        unlink($bc_array['backup_file']);
      }
    }

    $backup_time = time();
    $backup_content = '';
    $result = TRUE;

    // Configuration
    $content = $this->WriteConfigData($bc_array);
    $result = $result && ($content !== FALSE);
    $backup_content.= (is_bool($content)?"":$content);

    if (!$bc_array['config_only']) {
      // Devices
      foreach (explode("\t", $this->GetDevicesList()) as $one_device) {
        if ('' != trim($one_device)) {
          if ($this->ReadDeviceData($one_device)) {
            $content = $this->WriteDeviceData(array_merge($bc_array,
                                                          array("with_radius_update" => FALSE)));
            $result = $result && ($content !== FALSE);
            $backup_content.= (is_bool($content)?"":$content);
          }
        }
      }
      // Groups
      foreach (explode("\t", $this->GetGroupsList()) as $one_group) {
        if ('' != trim($one_group)) {
          if ($this->ReadGroupData($one_group)) {
            $content = $this->WriteGroupData($bc_array);
            $result = $result && ($content !== FALSE);
            $backup_content.= (is_bool($content)?"":$content);
          }
        }
      }
      // Tokens
      foreach (explode("\t", $this->GetTokensList()) as $one_token) {
        if ('' != trim($one_token)) {
          if ($this->ReadTokenData($one_token)) {
            $content = $this->WriteTokenData($bc_array);
            $result = $result && ($content !== FALSE);
            $backup_content.= (is_bool($content)?"":$content);
          }
        }
      }
      // Users
      $user_array = $this->GetNextUserArray(TRUE);
      while (FALSE !== $user_array) {
        if (isset($user_array['user'])) {
          if ($this->ReadUserData($user_array['user'], FALSE, TRUE)) {
            $content = $this->WriteUserData($bc_array);
            $result = $result && ($content !== FALSE);
            $backup_content.= (is_bool($content)?"":$content);
          }
        }
        $user_array = $this->GetNextUserArray();
      }
    } // if (!$bc_array['config_only']) {

    return ($bc_array['return_content']?($result?$backup_content:FALSE):$result);
  }


  /**
   * @brief   Restore the whole configuration from the unified format
   *
   * @param   array $rc_array string  'backup_file'        name of the backup file to read
   *                          string  'restore_key'        encryption key
   *                          array   'ignore_attributes'  array of string of ignored attributes (can be a part of the start or the end of the name)
   *                          array   'ignore_files'       array of string of ignored files
   *                          array   'rename_files'       array of a renaming array(original => '', renamed => '')
   *                          boolean 'update_config'      Update the config file instead of replacing it
   * @retval  string Content of the file to backup
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.2.6
   * @date    2016-10-25
   * @since   2016-05-03
   */
  function RestoreConfiguration(
    $rc_array = array()
  ) {
    $backup_file       = isset($rc_array['backup_file'])       ? $rc_array['backup_file'] : '';
    $restore_key       = isset($rc_array['restore_key'])       ? $rc_array['restore_key'] : '';
    $ignore_attributes = isset($rc_array['ignore_attributes']) ? $rc_array['ignore_attributes'] : array();
    $ignore_files      = isset($rc_array['ignore_files'])      ? $rc_array['ignore_files'] : array();
    $rename_files      = isset($rc_array['rename_files'])      ? $rc_array['rename_files'] : array();
    $update_config     = isset($rc_array['update_config'])     ? (TRUE === $rc_array['update_config']) : FALSE;
    $ignore_config     = isset($rc_array['ignore_config'])     ? (TRUE === $rc_array['ignore_config']) : FALSE;
    $automatically     = isset($rc_array['automatically'])     ? (TRUE === $rc_array['automatically']) : FALSE;

    if (!is_array($ignore_attributes)) {
      $ignore_attributes = array('multiotp-database-format', 'actual_version', 'anonymous_');
    } else {
      $ignore_attributes = array_merge($ignore_attributes, array('multiotp-database-format', 'actual_version', 'anonymous_'));
    }
    if (!is_array($rename_files)) {
      $rename_files = array();
    }

    if ('*CLEAR*' == $restore_key) {
        $restore_key = '';
    } elseif ('' == $restore_key) {
        $restore_key = $this->GetEncryptionKey();
    }

    $type = '';
    $item = '';
    $id_value = '';
    $deleted = FALSE;
    $file_handler = FALSE;
    $data_array = array();
    $validity = TRUE;
    $result = TRUE;

    if (file_exists($backup_file)) {

      if ($backup_handler = @fopen($backup_file, "rt")) {
        $first_line = TRUE;
        $line = str_replace(chr(10), "", str_replace(chr(13), "", fgets($backup_handler)));

        if (0 === mb_strpos($line, '# CONFIGURATION')) {
          $first_line = FALSE;
          $validity_array = explode("/", $line."///");
          $check_validity = substr(md5($validity_array[1].$restore_key.$validity_array[1]),0,14);
          if ($validity_array[2] != $check_validity) {
            $this->WriteLog("Error: Bad restore configuration password", FALSE, FALSE, 59, 'System', '', 3);
            $result = FALSE;
          }
        }

        while ((!feof($backup_handler)) && $result) {
          if ($first_line) {
            $first_line = FALSE;
          } else {
            $line = str_replace(chr(10), "", str_replace(chr(13), "", fgets($backup_handler)));
          }
          if (0 === mb_strpos($line, '; #!#')) {
            // Headers and meta data
            $config_line = substr($line, 5);
            $config_line_array = explode("=",$config_line,2);
            $config_command = isset($config_line_array[0]) ? $config_line_array[0] : '';
            $config_parameter = isset($config_line_array[1]) ? $config_line_array[1] : '';
            if (0 === strpos ($config_command, 'element-start')) {
              $type = '';
              $item = '';
              $id_value = '';
              $deleted = FALSE;
              $file_handler = FALSE;
              $data_array = array();
            } elseif (0 === strpos ($config_command, 'type')) {
              $type = $config_parameter;
            } elseif (0 === strpos ($config_command, 'item')) {
              $item = $config_parameter;
              $item = str_replace("--config-@-folder--", $this->GetConfigFolder(), $item);
            } elseif (0 === strpos ($config_command, 'id_value')) {
              $id_value = $config_parameter;
              switch ($type) {
                case 'data':
                  switch ($item) {
                    case 'Configuration':
                      if ($ignore_config) {
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *System configuration data ignored", FALSE, FALSE, 8888, 'System', '');
                        }
                      } else {
                        if ($update_config) {
                          if ($this->GetVerboseFlag()) {
                            $this->WriteLog("Info: *Configuration update_config", FALSE, FALSE, 8888, 'System', '');
                          }
                          $this->ReadConfigData();
                        } else {
                          if ($this->GetVerboseFlag()) {
                            $this->WriteLog("Info: *Reset (update) the configuration array", FALSE, FALSE, 8888, 'System', '');
                          }
                          $this->ReadConfigData(); // No reset, please, always update
                          // $this->ResetConfigArray();
                        }
                      }
                      break;
                    case 'Device':
                      $this->ResetDeviceArray();
                      $this->_device = mb_strtolower($id_value);
                      break;
                    case 'Group':
                      $this->ResetGroupArray();
                      $this->_group = mb_strtolower($id_value);
                      break;
                    case 'Token':
                      $this->ResetTokenArray();
                      $this->_token = mb_strtolower($id_value);
                      break;
                    case 'User':
                      $this->ResetUserArray();
                      $this->_user = ($this->IsCaseSensitiveUsers()) ? $id_value : mb_strtolower($id_value);
                      break;
                  }
                  break;

                case 'file':
                  $ignore = FALSE;
                  foreach ($ignore_files as $one_ignore_file) {
                    if ($one_ignore_file == $id_value) {
                      $ignore = TRUE;
                      break;
                    }
                  }
                  if ((!$ignore) && ($id_value != '')) {
                    if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *File $id_value to restore", FALSE, FALSE, 8888, 'System', '');
                    }
                    foreach($rename_files as $one_file) {
                      if ($id_value == isset($one_file['original'])?$one_file['original']:'') {
                        if ('' != (isset($one_file['original'])?$one_file['original']:'')) {
                          $id_value = $one_file['renamed'];
                          break;
                        }
                      }
                    }
                    if (file_exists($item)) {
                      if (!($file_handler = @fopen($item.$id_value, "wb"))) {
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *File ".$id_value." cannot be created", FALSE, FALSE, 8888, 'System', '');
                        }
                      }
                    } elseif ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *File ".$id_value." not created, $item doesn't exist", FALSE, FALSE, 8888, 'System', '');
                    }
                  } elseif ($this->GetVerboseFlag()) {
                    $this->WriteLog("Info: *File ".$id_value." ignored", FALSE, FALSE, 8888, 'System', '');
                  }
                  break;
              }
            } elseif (0 === strpos ($config_command, 'deleted')) {
              $deleted = (1 == $config_parameter);
            } elseif (0 === strpos ($config_command, 'element-stop')) {
              switch ($type) {
                case 'data':
                  switch ($item) {
                    case 'Configuration':
                      if ($ignore_config) {
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *System configuration data ignored and not updated", FALSE, FALSE, 8888, 'System', '');
                        }
                      } else {
                        $this->WriteConfigData();
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Configuration updated", FALSE, FALSE, 8888, 'System', '');
                        }
                      }
                      break;
                    case 'Device':
                      if (!$deleted) {
                        $this->WriteDeviceData(array("automatically" => $automatically));
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Device ".$id_value." updated", FALSE, FALSE, 8888, 'System', '');
                        }
                      } else {
                        $this->DeleteDevice($id_value, TRUE);
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Device ".$id_value." deleted", FALSE, FALSE, 8888, 'System', '');
                        }
                      }
                      break;
                    case 'Group':
                      if (!$deleted) {
                        $this->WriteGroupData(array("automatically" => $automatically));
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Group ".$id_value." updated", FALSE, FALSE, 8888, 'System', '');
                        }
                      } else {
                        $this->DeleteGroup($id_value, TRUE);
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Group ".$id_value." deleted", FALSE, FALSE, 8888, 'System', '');
                        }
                      }
                      break;
                    case 'Token':
                      if (!$deleted) {
                        $this->WriteTokenData(array("automatically" => $automatically));
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Token ".$id_value." updated", FALSE, FALSE, 8888, 'System', '');
                        }
                      } else {
                        $this->DeleteToken($id_value, TRUE);
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *Token ".$id_value." deleted", FALSE, FALSE, 8888, 'System', '');
                        }
                      }
                      break;
                    case 'User':
                      if (!$deleted) {
                        $this->UserRestoreBeforeWrite();
                        $this->WriteUserData(array("automatically" => $automatically));
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *User ".$id_value." updated", FALSE, FALSE, 8888, 'System', '');
                        }
                      } else {
                        $this->DeleteUser($id_value, TRUE);
                        if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Info: *User ".$id_value." deleted", FALSE, FALSE, 8888, 'System', '');
                        }
                      }
                      break;
                  }
                  break;

                case 'file':
                  if (FALSE !== $file_handler) {
                    fclose($file_handler);
                    if ('' != $this->GetLinuxFileMode()) {
                      @chmod($item.$id_value, octdec($this->GetLinuxFileMode()));
                    }
                    if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *File ".$id_value." closed", FALSE, FALSE, 8888, 'System', '');
                    }                      
                  }
                  if ($deleted) {
                    unlink($item.$id_value);
                    if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *File ".$id_value." deleted", FALSE, FALSE, 8888, 'System', '');
                    }
                  } elseif ($this->GetVerboseFlag()) {
                    $this->WriteLog("Info: *File ".$id_value." updated", FALSE, FALSE, 8888, 'System', '');
                  }
                  break;
              }
              $type = '';
              $item = '';
              $id_value = '';
              $deleted = FALSE;
              $file_handler = FALSE;
              $data_array = array();
            }
          } else {
            // Content to restore
            $line_array = explode("=",$line,2);
            $key = isset($line_array[0]) ? $line_array[0] : '';
            $value = isset($line_array[1]) ? $line_array[1] : '';
            if (('#' != substr($line, 0, 1)) && (';' != substr($line, 0, 1)) && ("" != trim($line)) && (isset($line_array[1]))) {
              if (":" == substr($key, -1)) {
                $key = substr($key, 0, strlen($key) -1);
                $value = $this->Decrypt($key,$value,$restore_key);
              }
              if ('raw_data' == $key) {
                $value = hex2bin($value);
              }

              foreach ($ignore_attributes as $one_ignore_attribute) {
                if ((0 === mb_strpos($key, $one_ignore_attribute)) || (substr($key, -strlen($one_ignore_attribute)) == $one_ignore_attribute)) {
                  $key = "";
                  break;
                }
              }
              
              if ("" != $key) {
                if ('file' == $type) {
                  if ($file_handler && (!$deleted)) {
                    fwrite($file_handler, $value);
                  }
                } elseif ('data' == $type) {
                  switch ($item) {
                    case 'Configuration':
                      if (!$ignore_config) {
                        $this->_config_data[$key] = $value;
                      }
                      break;
                    case 'Device':
                      $this->_device_data[$key] = $value;
                      break;
                    case 'Group':
                      $this->_group_data[$key] = $value;
                      break;
                    case 'Token':
                      $this->_token_data[$key] = $value;
                      break;
                    case 'User':
                      $this->_user_data[$key] = $value;
                      break;
                  }
                }
              }
            }
          }
        } // while
        fclose($backup_handler);
      } // if fopen
    } else { // if file_exists
      $result = false; // File doesn't exist
    }
    return $result;
  }


  function SetConsoleAuthentication(
    $value
  ) {
      $this->_config_data['console_authentication'] = ((intval($value) > 0) ? 1 : 0);
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


  function GetLogFileName()
  {
      return $this->_log_file_name;
  }

  function SetLogHeaderWritten(
      $log_header_written
  ) {
      $this->_log_header_written = $log_header_written;
  }


  function GetLogHeaderWritten()
  {
      return $this->_log_header_written;
  }


  function SetLogFolder(
      $folder,
      $create = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_log_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing config folder ".$new_folder, true, false, 28, 'System', '', 3);
          }
      }
  }


  function GetLogFolder()
  {
      if ("" == $this->_log_folder) {
          $this->SetLogFolder($this->GetScriptFolder()."log/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_log_folder);
  }


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
  function WriteLog(
      $info,
      $file_only = FALSE,
      $hide_on_display = FALSE,
      $error_code = 9999,
      $category = '*DEFAULT*',
      $user = '*DEFAULT*',
      $overwrite_severity = -1,
      $no_syslog = FALSE
  ) {
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

      // 0000 notice         (5)
      // 8888-9999 debug     (7)
      // 0001-0019 info      (6)
      // 0020-0099 warning   (4)
      // 0100-0199 error     (3)
      // 0200-0299 critical  (2)
      // 0300-0399 alert     (1)
      // 0400-0499 emergency (0)
      if (0 == intval($error_code)) {
          $severity = 5;
      } elseif (8888 <= intval($error_code)) {
          $severity = 7;
      } elseif (20 > intval($error_code)) {
          $severity = 6;
      } elseif (100 > intval($error_code)) {
          $severity = 4;
      } elseif (200 > intval($error_code)) {
          $severity = 3;
      } elseif (300 > intval($error_code)) {
          $severity = 2;
      } elseif (400 > intval($error_code)) {
          $severity = 1;
      } elseif (500 > intval($error_code)) {
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

      $log_time = time();
      $log_datetime = date("Y-m-d H:i:s", $log_time);
      
      // In the logfile, we don't want to have several lines for one entry,
      // therefore we are replacing the "\n" with "; " (or <br /> if we want to debug in HTML mode
      
      $logfile_content = $log_datetime."\t".$severity_txt."\t".$user_log."\t".$category_log."\t".str_replace("\n", $this->IsDebugViaHtml()?"<br />":"; ", $log_info);

      if (($this->GetDisplayLogFlag()) && (!$hide_on_display) && (!$this->GetNoDisplayLogFlag())) {
          $display_text = "\nLOG ".$log_datetime.' '.$severity_txt.' '.(("" == $user_log)?"":'(user '.$user_log.') ').$category_log.' '.$log_info."\n";
          if ($this->IsDebugViaHtml()) {
              $display_text = str_replace("\n","<br />\n", $display_text);
          }
          echo $display_text;
      }

      if (("" != trim($this->GetSysLogServer())) && (!$this->IsSysLogServerBad()) && (!$no_syslog)) {
          if ($severity <= $this->GetSyslogLevel()) {
              $syslog_server = $this->GetSysLogServer();
              if (!is_valid_ipv4($syslog_server)) {
                  $syslog_server = gethostbyname($syslog_server);
              }
              if (is_valid_ipv4($syslog_server)) {
                  $syslog_month     = date("M", $log_time);
                  $syslog_day       = substr("  ".date("j", $log_time), -2);
                  $syslog_hhmmss    = date("H:i:s", $log_time);
                  $syslog_timestamp = $syslog_month." ".$syslog_day." ".$syslog_hhmmss;

                  $syslog_port = $this->GetSysLogPort();
                  $syslog_timeout = 3; // 3 seconds timeout for udp connection
                  $syslog_severity_facility = $severity + 8 * $this->GetSyslogFacility();
                  $syslog_hostname = $this->GetSystemName();
                  $syslog_process = 'multiOTP';
                  $syslog_ip_from = $this->GetLocalIpAddress();
                  $syslog_content = str_replace("\n", "; ", $log_info);
                  $syslog_fqdn = $this->GetSystemName().(("" != $this->GetDomainName())?'.'.$this->GetDomainName():"");

                  // Do an asynchronous SysLog if possible (Linux)
                  $cli_command = "";
                  if (file_exists('/bin/nc')) {
                      // https://nelsonslog.wordpress.com/2013/04/19/faking-out-remote-syslog-via-netcat/
                      $cli_command = "echo \"<$syslog_severity_facility>$syslog_timestamp $syslog_hostname $syslog_process: $syslog_fqdn $syslog_ip_from $syslog_content\" | /bin/nc $syslog_server -u $syslog_port -w $syslog_timeout > /dev/null 2>&1";
                      exec("nohup $cli_command &", $output);
                  }

                  // Otherwise, or in the verbose mode also, PHP SysLog class
                  if (("" == $cli_command) || ($this->GetVerboseFlag())) {
                      $duplicated = (("" == $cli_command) ? "" : "(duplicated using native syslog library for debug) ");
                      $syslog = new MultiotpSyslog();
                      $syslog->SetTimeout($syslog_timeout);
                      $syslog->SetFacility($this->GetSyslogFacility());
                      $syslog->SetSeverity($severity);
                      $syslog->SetHostname($syslog_hostname);
                      $syslog->SetFqdn($syslog_fqdn);
                      $syslog->SetIpFrom($syslog_ip_from);
                      $syslog->SetProcess($syslog_process);
                      $syslog->SetContent($duplicated.$syslog_content);
                      $syslog->SetServer($syslog_server);
                      $syslog->SetPort($syslog_port);
                      $syslog_result = $syslog->Send();

                      if ('ERROR' == substr($syslog_result, 0, 5)) {
                          $this->EnableBadSysLogServer();
                          if ($this->GetVerboseFlag()) {
                              $this->WriteLog("Warning: *Error with the Syslog server ".$this->GetSysLogServer().": $syslog_result", FALSE, FALSE, 99, 'System', '', -1, TRUE);
                          } else {
                              $this->WriteLog("Warning: Error with the Syslog server ".$this->GetSysLogServer(), FALSE, FALSE, 99, 'System', '', -1, TRUE);
                          }
                      }
                  }
              } else {
                  $this->EnableBadSysLogServer();
                  $this->WriteLog("Warning: resolution name error for the Syslog server ".$this->GetSysLogServer(), FALSE, FALSE, 99, 'System', '', -1, TRUE);
              }
          }
      }

      $log_link = NULL;
      if ($this->IsLogEnabled()) {
          if ((!$file_only) &&
            (('mysql' == $this->GetBackendType()) || ('pgsql' == $this->GetBackendType())) &&
            $this->GetBackendTypeValidated() && 
            ("" != $this->_config_data['sql_log_table'])
          ) {
              if ('mysql' == $this->GetBackendType()) {
                  if ($this->OpenMysqlDatabase()) {
                      $log_severity_escaped = escape_mysql_string($severity_txt);
                      $log_user_escaped = escape_mysql_string($user_log);
                      $log_category_escaped = escape_mysql_string($category_log);
                      $log_info_escaped = escape_mysql_string(substr($log_info,0,255));

                      $sQuery  = "INSERT INTO `".$this->_config_data['sql_log_table']."` (`datetime`,`severity`,`user`,`category`,`logentry`) VALUES ('".$log_datetime."','".$log_severity_escaped."','".$log_user_escaped."','".$log_category_escaped."','".$log_info_escaped."')";
                      
                      if (is_object($this->_mysqli)) {
                          if (!($rResult = $this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: SQL query error ($sQuery) : ".trim($this->_mysqli->error), TRUE, FALSE, 40, 'System', '', 3);
                          }
                      } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                          $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                      }
                  }
                  //mysql_close($log_link);
              } elseif ('pgsql' == $this->GetBackendType()) {
                  if ($this->OpenPGSQLDatabase()) {
                      $log_severity_escaped = pg_escape_string($severity_txt);
                      $log_user_escaped = pg_escape_string($user_log);
                      $log_category_escaped = pg_escape_string($category_log);
                      $log_info_escaped = pg_escape_string(substr($log_info,0,255));

                      $sQuery  = "INSERT INTO \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_log_table']."\" (\"datetime\",\"severity\",\"user\",\"category\",\"logentry\") VALUES ('".$log_datetime."','".$log_severity_escaped."','".$log_user_escaped."','".$log_category_escaped."','".$log_info_escaped."')";
                      
                      if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                      }
                  }
                  //pg_close($log_link);
              }
          } else {
              if (!file_exists($this->GetLogFolder())) {
                  @mkdir(
                      $this->GetLogFolder(),
                      ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                      true //recursive
                  );
              }
              $file_created = (!file_exists($this->GetLogFolder().$this->GetLogFileName()));
              if ($log_file_handle = @fopen($this->GetLogFolder().$this->GetLogFileName(),"ab+")) {
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
                      @chmod($this->GetLogFolder().$this->GetLogFileName(), octdec($this->GetLinuxFileMode()));
                  }
              }
          }
      }
  }


  function ShowLog(
      $as_result = FALSE
  ) {
      $result = "";
      if ('mysql' == $this->GetBackendType()) {
          if ($this->OpenMysqlDatabase()) {
              $sQuery  = "SELECT * FROM `".$this->_config_data['sql_log_table']."`";
              
              if (is_object($this->_mysqli)) {
                  if (!($rResult = $this->_mysqli->query($sQuery))) {
                      $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
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
                  $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
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
      } elseif ('pgsql' == $this->GetBackendType()) {
          if ($this->OpenPGSQLDatabase()) {
              $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_log_table']."\"";
              
              if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                  $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                  $result = FALSE;
              } else {
                  while ($aRow = pg_fetch_assoc($rResult)) {
                      if ($as_result) {
                          $result.= trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                      } else {
                          echo trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                      }
                  }                         
              }
          }
          //pg_close($log_link);
      } elseif (file_exists($this->GetLogFolder().$this->GetLogFileName())) {
          if ($log_file_handle = @fopen($this->GetLogFolder().$this->GetLogFileName(),"r")) {
              while (!feof($log_file_handle)) {
                  if ($as_result) {
                      $result.= trim(fgets($log_file_handle))."\n";
                  } else {
                      echo trim(fgets($log_file_handle))."\n";
                  }
              }
              fclose($log_file_handle);
          }
      }
      return $result;
  }


  function ClearLog()
  {
      $result = TRUE;
      if ('mysql' == $this->GetBackendType()) {
          if ($this->OpenMysqlDatabase()) {
              $sQuery  = "TRUNCATE `".$this->_config_data['sql_log_table']."`";
              
              if (is_object($this->_mysqli)) {
                  if (!($rResult = $this->_mysqli->query($sQuery))) {
                      $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                      $result = FALSE;
                  }
              } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                  $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                  $result = FALSE;
              }
          } else {
              $result = FALSE;
          }
      } elseif ('pgsql' == $this->GetBackendType()) {
          if ($this->OpenPGSQLDatabase()) {
              $sQuery  = "TRUNCATE \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_log_table']."\"";
              
              if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                  $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
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


  function EnableLog()
  {
      $this->_log_flag = TRUE;
      if ("" == $this->_log_folder) {
          $this->SetLogFolder($this->GetScriptFolder()."log/");
      }
  }


  function IsLogEnabled()
  {
      return (TRUE === $this->_log_flag);
  }


  function DisableLog()
  {
      $this->_log_flag = FALSE;
  }


  function EnableVerboseLog()
  {
      $this->EnableLog();
      $this->_log_verbose_flag = TRUE;
  }


  function DisableVerboseLog()
  {
      $this->_log_verbose_flag = FALSE;
  }


  function GetVerboseFlag()
  {
      return $this->_log_verbose_flag;
  }


  function ForceNoDisplayLog() {
      $this->_no_display_log = true;
  }

  
  function DisableNoDisplayLog() {
      $this->_no_display_log = false;
  }

  
  function GetNoDisplayLogFlag() {
      return $this->_no_display_log;
  }


  function EnableDisplayLog()
  {
      $this->_log_display_flag = TRUE;
  }


  function DisableDisplayLog()
  {
      $this->_log_display_flag = FALSE;
  }


  function GetDisplayLogFlag()
  {
      return $this->_log_display_flag;
  }


  function SetCliMode($value) {
      $this->_cli_mode = (true == $value);
  }


  function GetCliMode() {
      return (true == $this->_cli_mode);
  }


  function SetCliProxyMode($value) {
      $this->_cli_proxy_mode = (true == $value);
  }


  function GetCliProxyMode() {
      return (true == $this->_cli_proxy_mode);
  }


  function SetCredentialProviderMode($value) {
      $this->_cp_mode = (true == $value);
  }


  function GetCredentialProviderMode() {
      return (true == $this->_cp_mode);
  }


  function SetDemoMode($value) {
      $this->_config_data['demo_mode'] = ((intval($value) > 0)?1:0);
  }


  function EnableDemoMode() {
      $this->_config_data['demo_mode'] = 1;
  }


  function DisableDemoMode()
  {
      $this->_config_data['demo_mode'] = 0;
  }


  function IsDemoMode()
  {
      return (1 == ($this->_config_data['demo_mode']));
  }


  function SetCacheLdapHash(
      $value
  ) {
      $this->_config_data['cache_ldap_hash'] = ((intval($value) > 0)?1:0);
  }


  function EnableCacheLdapHash()
  {
      $this->_config_data['cache_ldap_hash'] = 1;
  }


  function DisableCacheLdapHash()
  {
      $this->_config_data['cache_ldap_hash'] = 0;
  }


  function IsCacheLdapHash()
  {
      return (1 == ($this->_config_data['cache_ldap_hash']));
  }


  function IsLdapServerReachable()
  {
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


  function GetEncryptionKeyFullPath()
  {
      return trim(isset($this->_config_data['encryption_key_full_path'])?$this->_config_data['encryption_key_full_path']:"");
  }


  function SetHashSaltFullPath(
      $full_path
  ) {
      $this->_config_data['hash_salt_full_path'] = $full_path;
  }


  function GetHashSaltFullPath()
  {
      return trim($this->_config_data['hash_salt_full_path']);
  }


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
   * @since   2013-05-13
   */
  function SetConfigFolder(
      $folder,
      $create = true,
      $read_config = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_config_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing config folder ".$new_folder, true, false, 28, 'System', '', 3);
          }
      }
      if ($read_config) {
          $this->ReadConfigData();
      }
  }


  /**
   * @brief   Get the configuration folder (for the config file).
   *
   * @param   boolean $create_if_not_exist Create the folder if it doesn't exists.
   * @retval  string                       Full path to the config folder.
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 4.0.0
   * @date    2013-05-13
   * @since   2013-05-13
   */
  function GetConfigFolder(
      $create_if_not_exist = false
  ) {
      $config_folder = $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
      if ("" == $config_folder) {
          $this->SetConfigFolder($this->GetScriptFolder()."config/", $create_if_not_exist);
      } elseif (!file_exists($config_folder)) {
          if ($create_if_not_exist) {
              if (!@mkdir(
                      $config_folder,
                      ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                      true //recursive
              )) {
                  $this->WriteLog("Error: Unable to create the missing config folder ".$config_folder, FALSE, FALSE, 28, 'System', '', 3);
              }
          }
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
  }


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
   * @since   2014-07-25
   */
  function SetCacheFolder(
      $folder,
      $create = true,
      $read_cache = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_cache_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing cache folder ".$new_folder, TRUE, FALSE, 28, 'System', '', 3);
          }
      }
      if ($read_cache) {
          $this->ReadCacheData();
      }
  }


  /**
   * @brief   Get the cache folder (for the cache file).
   *
   * @param   boolean $create_if_not_exist Create the folder if it doesn't exists.
   * @retval  string                       Full path to the cache folder.
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 4.2.5.0
   * @date    2014-07-25
   * @since   2014-07-25
   */
  function GetCacheFolder(
      $create_if_not_exist = false
  ) {
      $cache_folder = $this->ConvertToWindowsPathIfNeeded($this->_cache_folder);
      if ("" == $cache_folder) {
          $this->SetCacheFolder($this->GetScriptFolder()."cache/", $create_if_not_exist);
      } elseif (!file_exists($cache_folder)) {
          if ($create_if_not_exist) {
              if (!@mkdir(
                      $cache_folder,
                      ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                      true //recursive
              )) {
                  $this->WriteLog("Error: Unable to create the missing cache folder ".$cache_folder, FALSE, FALSE, 28, 'System', '', 3);
              }
          }
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_cache_folder);
  }


  function GetLocalIpAddress()
  {
      $ip = "";
      if (mb_strtolower(substr(PHP_OS, 0, 3)) === 'win') { // Windows
          $output = array();
          exec("ipconfig /all", $output);
          foreach($output as $line) {
              $line.= "  ";
              if (preg_match("/.*IPv4.*[^\.]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})[^\.]+/", $line)) {
                  preg_match_all("/[^\.[:xdigit:]]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})/", $line, $result_array, PREG_SET_ORDER);
                  if (isset($result_array[0][1])) {
                      $temp = trim($result_array[0][1]);
                      if ('0.0.0.0' != $temp) {
                          $ip = $temp;
                          break;
                      }
                  }
              }
          }
      } else { // Linux
          $output = array();
          exec("ifconfig eth0 | grep \"inet addr\" | grep -o -E '([[:xdigit:]]{1,3}\.){3}[[:xdigit:]]{1,3}'", $output);
          $ip = (isset($output[0])?$output[0]:'');
      }
      return $ip;
  }


  function GetNetworkInfo()
  {
      // These are demo values for the development
      $mode    = "";
      $ip      = "";
      $mask    = "";
      $gateway = "";
      $dns     = array();
      $dns[0]  = "";
      $dns[1]  = "";
      $interface_name = "";
      $fixed_gateway = false;
      
      if (mb_strtolower(substr(PHP_OS, 0, 3)) === 'win') {
          // Windows
          // The last route (without an interface address) is the default one
          $output = array();
          exec("route print | find \"0.0.0.0\"", $output);
          foreach($output as $line) {
              $line.= "  ";
              if (preg_match("/.*0.0.0.0.*[^\.]+0.0.0.0.*[^\.]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})[^\.]+/", $line)) {
                  $result_array = array();
                  preg_match_all("/[^\.[:xdigit:]]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})/", $line, $result_array, PREG_SET_ORDER);
                  if ((!$fixed_gateway) && (isset($result_array[2][1]))) {
                      $temp = trim($result_array[2][1]);
                      if ('0.0.0.0' != $temp) {
                          $gateway = $temp;
                          if (!isset($result_array[3][1])) {
                              $fixed_gateway = true;
                          }
                      }
                  }
              }
          }
          $output = array();
          exec("ipconfig /all", $output);
          $next_is_mask = false;
          foreach($output as $line) {
              $line.= "  ";
              if ($next_is_mask || preg_match("/.*IPv4.*[^\.]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})[^\.]+/", $line)) {
                  $result_array = array();
                  preg_match_all("/[^\.[:xdigit:]]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})/", $line, $result_array, PREG_SET_ORDER);
                  if (isset($result_array[0][1])) {
                      $temp = trim($result_array[0][1]);
                      if ($next_is_mask) {
                          $mask = $temp;
                          $cidr_mask = 32-log((ip2long($mask) ^ ip2long('255.255.255.255'))+1,2);
                          $gw_long_subnet = (ip2long($gateway) >> (32-$cidr_mask));
                          $ip_long_subnet = (ip2long($ip) >> (32-$cidr_mask));
                          if ($ip_long_subnet != $gw_long_subnet) {
                              $next_is_mask = false;
                          } else {
                              break;
                          }
                      } elseif ('0.0.0.0' != $temp) {
                          $ip = $temp;
                          $next_is_mask = true;
                      }
                  }
              }
          }

          $output = array();
          exec("netsh interface dump | find \"".$ip."\"", $output);
          foreach($output as $line) {
              $line.= "  ";
              $result_array = array();
              preg_match_all("/[^\"]+\"([^\"]*)\".*/", $line, $result_array, PREG_SET_ORDER);
              if (isset($result_array[0][1])) {
                  // We receive something back which is probably coded in CP850
                  $interface_name = mb_convert_encoding (trim($result_array[0][1]),"UTF-8","CP850");
                  $mode = "static";
                  break;
              }
          }
          if ("" == $interface_name) {
              $mode = "dhcp";
              $output = array();
              exec("netsh interface dump", $output);
              $ip4config = false;
              foreach($output as $line) {
                  $line.= "  ";
                  if (0 === mb_strpos(trim($line),"pushd interface ipv4")) {
                      $ip4config = true;
                  } elseif ($ip4config && (0 === mb_strpos(trim($line),"popd"))) {
                      $ip4config = false;
                  }
                  if ($ip4config) {
                      $result_array = array();
                      preg_match_all("/^set interface[^\"]+\"([^\"]*)\".*metric=1.*/", $line, $result_array, PREG_SET_ORDER);
                      if (isset($result_array[0][1])) {
                          // We receive something back which is probably coded in CP850
                          $interface_name = mb_convert_encoding (trim($result_array[0][1]),"UTF-8","CP850");
                          break;
                      }
                  }
              }
          }

          $dns_count = 0;
          $output = array();
          exec("netsh interface ip show dnsservers \"".mb_convert_encoding ($interface_name,"ISO-8859-15","UTF-8")."\"", $output);
          foreach($output as $line) {
              $line.= "  ";
              if (preg_match("/[^\.[:xdigit:]]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})[^\.]+/", $line)) {
                  $result_array = array();
                  preg_match_all("/[^\.[:xdigit:]]+([[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3}[\.][[:xdigit:]]{1,3})/", $line, $result_array, PREG_SET_ORDER);
                  if (($dns_count < 2) && isset($result_array[0][1])) {
                      $dns[$dns_count] = trim($result_array[0][1]);
                      $dns_count++;
                  }
              }
          }
      } else {
          // Linux
          $output = array();
          exec("grep -e \"^iface\seth0.*inet\s.*dhcp\" /etc/network/interfaces", $output);
          $mode = (false !== mb_strpos(mb_strtolower(isset($output[0])?$output[0]:''), "dhcp"))?"dhcp":"static";
          
          $output = array();
          exec("ifconfig eth0 | grep eth0 | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'", $output);
          $mac = mb_strtoupper(isset($output[0])?$output[0]:'');
      
          $output = array();
          exec("ifconfig eth0 | grep \"inet addr\" | grep -o -E '([[:xdigit:]]{1,3}\.){3}[[:xdigit:]]{1,3}'", $output);
          $ip = (isset($output[0])?$output[0]:'');
          $mask = (isset($output[2])?$output[2]:'');

          $output = array();
          exec("ip route show default | awk '/default/ {print $3}'", $output);
          $gateway = mb_strtoupper(isset($output[0])?$output[0]:'');

          $output = array();
          exec("cat /etc/resolv.conf | grep -o -E '([[:xdigit:]]{1,3}\.){3}[[:xdigit:]]{1,3}'", $output);
          $dns[0] = (isset($output[0])?$output[0]:'');
          $dns[1] = (isset($output[1])?$output[1]:'');
      }
      $network_info = $mode."\t".$ip."\t".$mask."\t".$gateway."\t".$dns[0]."\t".$dns[1]."\t";
      $network_array = explode("\t",$network_info);
      
      for ($i=count($network_array); $i <= 5; $i++) {
          $network_array[$i] = '';
      }
      return $network_array;
  }


  function SetNetworkInfo(
      $ip = '',
      $mask = '',
      $gateway = '',
      $dns1 = '',
      $dns2 = '',
      $write_config = true,
      $if_down_up = true
  ) {
      $result = false;
      if ('' != $ip) {
          $resolv_file = "/etc/resolv.conf";
          $resolv_tmp  = sys_get_temp_dir()."/multiotp_resolv_tmp";
          if (!($write = @fopen($resolv_tmp, "wt"))) {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Error: *Temporary DNS information cannot be created", FALSE, FALSE, 8888, 'System', '');
              }
          } else {
              $domain_name = $this->GetDomainName();
              if ('' != $domain_name) {
                  fwrite($write, "domain ".$domain_name."\n");
                  fwrite($write, "search ".$domain_name."\n");
              }
              if ('' != $dns1) {
                  fwrite($write, "nameserver ".$dns1."\n");
              }
              if ('' != $dns2) {
                  fwrite($write, "nameserver ".$dns2."\n");
              }
              fclose($write);
              if ('' != $this->GetLinuxFileMode()) {
                  @chmod($resolv_tmp, octdec($this->GetLinuxFileMode()));
              }

              // Do not change the DNS servers in demo mode!
              if (!$this->IsDemoMode()) {
                  if (mb_strtolower(substr(PHP_OS, 0, 3)) !== 'win') { // Currently only for non-windows machine
                      exec("sudo cp -f ".$resolv_tmp." ".$resolv_file, $output);
                  }
              }
          }
      }

      $interfaces_file = "/etc/network/interfaces";
      $interfaces_tmp  = sys_get_temp_dir()."/multiotp_interfaces_tmp";
      if (file_exists($interfaces_file)) {
          if (!($read = @fopen($interfaces_file, "rt"))) {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Error: *Interface configuration cannot be accessed", FALSE, FALSE, 8888, 'System', '');
              }
          } else {
              if (!($write = @fopen($interfaces_tmp, "wt"))) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Error: *Temporary interface configuration cannot be created", FALSE, FALSE, 8888, 'System', '');
                  }
              } else {
                  $direct_write = true;
                  $inet_eth0 = false;
                  while(!feof($read)) {
                  // "iface", "mapping", "auto",  "allow-" and "source" eth0 (iface eth0)
                      $one_line = fgets($read);
                      if (preg_match("/^iface\seth0(.*)/", $one_line)) {
                          $direct_write = false;
                          $inet_eth0    = true;
                          if ('' != $ip) {
                              fwrite($write, "iface eth0 inet static\n");
                              fwrite($write, "\taddress ".$ip."\n");
                              fwrite($write, "\tnetmask ".$mask."\n");
                              fwrite($write, "\tgateway ".$gateway."\n");
                              fwrite($write, "\n");
                          } else {
                              fwrite($write, "iface eth0 inet dhcp\n");
                              fwrite($write, "\n");
                          }
                      } elseif ((0 === mb_strpos(trim($one_line),"allow-")) || 
                                (0 === mb_strpos(trim($one_line),"auto")) || 
                                (0 === mb_strpos(trim($one_line),"iface")) || 
                                (0 === mb_strpos(trim($one_line),"mapping")) || 
                                (0 === mb_strpos(trim($one_line),"source"))) {
                          $direct_write = true;
                      }
                      if ($direct_write) {
                          fwrite($write, $one_line); // $one_line includes \n
                      }
                  }
                  fclose($read);
                  fclose($write);
                  if ('' != $this->GetLinuxFileMode()) {
                      @chmod($interfaces_tmp, octdec($this->GetLinuxFileMode()));
                  }

                  // Do not change the IP in demo mode!
                  if (!$this->IsDemoMode()) {
                      exec("sudo cp -f ".$interfaces_tmp." ".$interfaces_file, $output);
                      $result = true;
                      if ($if_down_up) {
                          exec("sudo /sbin/ifdown eth0 > /dev/null 2>&1", $output);
                          sleep(1);
                          exec("sudo /sbin/ifup eth0 > /dev/null 2>&1", $output);
                      }
                  }
              }
          }
      } else {
          if ($this->GetVerboseFlag()) {
              $this->WriteLog("Error: *Interface configuration file cannot be found", FALSE, FALSE, 8888, 'System', '');
          }
      }
      return $result;
  }


  function GetSystemName()
  {
      $system_name = trim(php_uname('n'));
      return $system_name;
  }


  function SetDomainName(
      $value
  ) {
      if (!$this->IsDemoMode()) {
          $this->_config_data['domain_name'] = ($value);
      }
  }


  function GetDomainName()
  {
      return $this->_config_data['domain_name'];
  }


  function SetEmailAdminAddress(
      $value
  ) {
      $this->_config_data['email_admin_address'] = ($value);
  }


  function GetEmailAdminAddress()
  {
      return $this->_config_data['email_admin_address'];
  }


  function SetHashSalt(
      $salt
  ) {
      $this->_hash_salt  = trim($salt);
  }


  function GetHashSalt()
  {
      $salt = $this->_hash_salt;
      if ((("" == $salt) ||
           ("MySalt" == $salt) ||
           ("AjaxH@shS@lt" == $salt)
          ) &&
          ("" != $this->GetHashSaltFullPath()) &&
          file_exists($this->GetHashSaltFullPath())) {
          if ($hash_salt_file_handler = @fopen($this->GetHashSaltFullPath(), "rt")) {
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


  function GetRandomSalt()
  {
      return trim($this->_random_salt);
  }


  function SetAdminPassword(
      $password
  ) {
      if (!$this->IsDemoMode()) {
          return $this->SetConfigAttribute('admin_password_hash',md5($this->GetHashSalt().$password.$this->GetHashSalt()));
      } else {
          return false;
      }
  }


  function SetAdminPasswordHash(
      $password_hash
  ) {
      if (!$this->IsDemoMode()) {
          return $this->SetConfigAttribute('admin_password_hash',$password_hash);
      } else {
          return false;
      }
  }

  // Weak security check: the client side must return password (for internal call only)
  function CheckAdminPassword(
      $password
  ) {
      return ($this->GetConfigAttribute('admin_password_hash') == md5($this->GetHashSalt().$password.$this->GetHashSalt()));
  }


  // Regular security check: the client side must return md5(hash_salt + password + hash_salt)
  function CheckAdminPasswordHash(
      $password_hash_with_salt
  ) {
      if (32 == strlen($password_hash_with_salt)) {
          return ($this->GetConfigAttribute('admin_password_hash') == $password_hash_with_salt);
      } else {
          return false;
      }
  }


  // Better security check: the client side must return md5(salt + md5(hash_salt + password + hash_salt) + salt)
  function CheckAdminPasswordHashWithRandomSalt(
      $password_hash_with_salt
  ) {
      if (32 == strlen($password_hash_with_salt)) {
          return (md5($this->GetRandomSalt().$this->GetConfigAttribute('admin_password_hash').$this->GetRandomSalt()) == $password_hash_with_salt);
      } else {
          return false;
      }
  }


  function EnableDebugViaHtml()
  {
      $this->_debug_via_html = TRUE;
  }


  function IsDebugViaHtml()
  {
      return ($this->_debug_via_html);
  }


  function EnableKeepLocal()
  {
      $this->_keep_local = TRUE;
  }


  function IsKeepLocal()
  {
      return ($this->_keep_local);
  }


  function SetLinuxFileMode(
      $mode
  ) {
      $this->_linux_file_mode = $mode;
  }


  function GetLinuxFileMode()
  {
      return ($this->_linux_file_mode);
  }


  function SetLinuxFolderMode(
      $mode
  ) {
      $this->_linux_folder_mode = $mode;
  }


  function GetLinuxFolderMode()
  {
      return ($this->_linux_folder_mode);
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


  function GetLogOption() {
      return intval($this->_config_data['log']);
  }


  function SetDebugOption(
      $value
  ) {
      $this->_config_data['debug'] = intval($value);
      if (1 == $this->_config_data['debug']) {
          $this->EnableVerboseLog();
      }
  }


  function GetDebugOption() {
      return $this->_config_data['debug'];
  }


  function SetDeveloperMode(
      $value
  ) {
      $this->_config_data['developer_mode'] = intval($value);
  }


  function GetDeveloperMode() {
      return $this->_config_data['developer_mode'];
  }


  function IsDeveloperMode() {
      return (1 == intval($this->_config_data['developer_mode']));
  }


  function SetDisplayLogOption(
      $value
  ) {
      $this->_config_data['display_log'] = $value;
      if (1 == intval($this->_config_data['display_log'])) {
          $this->EnableDisplayLog();
      }
  }


  function SetMigrationFromFile(
      $value
  ) {
      $this->_migration_from_file = ($value?TRUE:FALSE);
  }


  function GetMigrationFromFile()
  {
      return $this->_migration_from_file;
  }


  function SetBackendType(
      $type
  ) {
      $this->_config_data['backend_type'] = $type;
      $this->_config_data['backend_type_validated'] = 0;
  }


  function GetBackendType()
  {
      return $this->_config_data['backend_type'];
  }


  function SetBackendTypeValidated(
      $backend_type_validated,
      $value
  ) {
      if ("" != $backend_type_validated) {
          $this->_config_data['backend_type'] = $backend_type_validated;
      }
      $this->_config_data['backend_type_validated'] = ($value ? 1 : 0);
  }

  function GetBackendTypeValidated()
  {
      return (1 == (isset($this->_config_data['backend_type_validated'])?$this->_config_data['backend_type_validated']:0));
  }

  function SetScratchPasswordsDigits(
      $value
  ) {
      $this->_config_data['scratch_passwords_digits'] = $value;
  }


  function GetScratchPasswordsDigits()
  {
      return $this->_config_data['scratch_passwords_digits'];
  }


  function SetDefaultUserGroup(
      $value
  ) {
      $this->_config_data['default_user_group'] = $value;
  }


  function GetDefaultUserGroup()
  {
      return $this->_config_data['default_user_group'];
  }


  function SetGroupAttribute(
      $value
  ) {
      $this->_config_data['group_attribute'] = $value;
  }


  function GetGroupAttribute()
  {
      return $this->_config_data['group_attribute'];
  }


  function SetIssuer(
      $value
  ) {
      $this->_config_data['issuer'] = $value;
  }


  function GetIssuer()
  {
      if (isset($this->_config_data['issuer'])) {
          return $this->_config_data['issuer'];
      } else {
          return "";
      }
  }


  function SetClearOtpAttribute(
      $value
  ) {
      $this->_config_data['clear_otp_attribute'] = $value;
  }


  function GetClearOtpAttribute()
  {
      return $this->_config_data['clear_otp_attribute'];
  }


  function SetSqlServer(
      $server
  ) {
      $this->_config_data['sql_server'] = $server;
  }


  function GetSqlServer()
  {
      return trim($this->_config_data['sql_server']);
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


  function SetSqlSchema(
      $schema
  ) {
      $this->_config_data['sql_schema'] = $schema;
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


  function MySqlAddRowIfNeeded(
      $table,
      $row,
      $row_type,
      $is_an_index = FALSE
  ) {
      $result = FALSE;
      if (is_object($this->_mysqli)) {
          $sql_query = "SELECT `".$row."` FROM ".$table;
          if ($result = $this->_mysqli->query($sql_query)) {
              $result = TRUE;
              $sql_query = "ALTER TABLE ".$table." CHANGE `".$row."` `".$row."` ".$row_type;
              if (!$this->_mysqli->query($sql_query)) {
                  $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                  $result = FALSE;
              }
          } else { //$select_row = $result->fetch_assoc();
              $sql_query = "ALTER TABLE ".$table." ADD `".$row."` ".$row_type;
              if ($is_an_index) {
                  $sql_query.= " , ADD INDEX ( `".$row."` )";
              }
              if (!$this->_mysqli->query($sql_query)) {
                  $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                  $result = FALSE;
              }
          }
      } elseif (NULL != $this->_mysql_database_link) {
          $sql_query = "SELECT `".$row."` FROM ".$table;
          if (($select_row = mysql_query($sql_query, $this->_mysql_database_link))) {
              $result = TRUE;
              $sql_query = "ALTER TABLE ".$table." CHANGE `".$row."` `".$row."` ".$row_type;
              if (!mysql_query($sql_query, $this->_mysql_database_link)) {
                  $this->WriteLog("Error: ".mysql_error()." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                  $result = FALSE;
              }
          } elseif (!$select_row) {
              $sql_query = "ALTER TABLE ".$table." ADD `".$row."` ".$row_type;
              if ($is_an_index) {
                  $sql_query.= " , ADD INDEX ( `".$row."` )";
              }
              if (!mysql_query($sql_query, $this->_mysql_database_link)) {
                  $this->WriteLog("Error: ".mysql_error()." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                  $result = FALSE;
              }
          }
      } elseif ($this->GetVerboseFlag()) {
          $this->WriteLog("Error: *The database link is down!", TRUE, FALSE, 41, 'System', '', 3);
      }
      return $result;
  }


  function OpenMysqlDatabase()
  {
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
                      $this->WriteLog("Error: Bad SQL authentication parameters, ".$this->_mysqli->connect_errno.', '.trim($this->_mysqli->connect_error), TRUE, FALSE, 41, 'System', '', 3);
                      unset($this->_mysqli);
                      $this->_mysqli = NULL;
                  } else {
                      $result = TRUE;
                  }
              } elseif (!($this->_mysql_database_link = mysql_connect($this->_config_data['sql_server'],
                                                                      $this->_config_data['sql_username'],
                                                                      $this->_config_data['sql_password']))) {
                  $this->WriteLog("Error: Bad SQL authentication parameters, ".mysql_error(), TRUE, FALSE, 41, 'System', '', 3);
              } else {
                  if (!mysql_select_db($this->_config_data['sql_database'])) {
                      $this->WriteLog("Error: Bad SQL database", TRUE, FALSE, 41, 'System', '', 3);
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


  function PGSQLAddRowIfNeeded(
      $table,
      $column,
      $column_type,
      $column_default,
      $is_an_index = FALSE
  ) {
      $result = FALSE;
      if (NULL != $this->_pgsql_database_link) {
          $sql_query = "SELECT \"".$column."\" FROM \"".$this->_config_data['sql_schema']."\".\"".$table."\"";
          if (($select_row = @pg_query($this->_pgsql_database_link, $sql_query))) {
              $result = TRUE;
              $sql_query = "ALTER TABLE \"".$this->_config_data['sql_schema']."\".\"".$table."\" ALTER COLUMN \"".$column."\" TYPE ".$column_type;
              if (!@pg_query($this->_pgsql_database_link, $sql_query)) {
                  $this->WriteLog("Error: ".pg_last_error()." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                  $result = FALSE;
              } else {
                  if ($column_default != NULL) {
                      $sql_query = "ALTER TABLE \"".$this->_config_data['sql_schema']."\".\"".$table."\" ALTER COLUMN \"".$column."\" SET DEFAULT ".$column_default;
                  } else {
                      $sql_query = "ALTER TABLE \"".$this->_config_data['sql_schema']."\".\"".$table."\" ALTER COLUMN \"".$column."\" DROP DEFAULT";
                  }
                  if (!@pg_query($this->_pgsql_database_link, $sql_query)) {
                      $this->WriteLog("Error: ".pg_last_error()." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                      $result = FALSE;
                  }
              }
          } elseif (!$select_row) {
              $sql_query = "ALTER TABLE \"".$this->_config_data['sql_schema']."\".\"".$table."\" ADD COLUMN \"".$column."\" ".$column_type.($column_default != NULL ? " DEFAULT ".$column_default : "");
              if (!@pg_query($this->_pgsql_database_link, $sql_query)) {
                  $this->WriteLog("Error: ".pg_last_error()." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                  $result = FALSE;
              }
              if ($is_an_index) {
                  $sql_query = "CREATE INDEX \"".$table."_".$column."_idx\" ON \"".$this->_config_data['sql_schema']."\".\"".$table."\" ( \"".$column."\" )";
                  if(!@pg_query($this->_pgsql_database_link, $sql_query)) {
                      $this->WriteLog("Error: ".pg_last_error()." ".$sql_query, TRUE, FALSE, 40, 'System', '', 3);
                      $result = FALSE;
                  }
              }
          }
      } elseif ($this->GetVerboseFlag()) {
          $this->WriteLog("Error: *The database link is down!", TRUE, FALSE, 41, 'System', '', 3);
      }

      return $result;
  }


  function OpenPGSQLDatabase()
  {
      if (NULL != $this->_pgsql_database_link) {
          $result = TRUE;
      } else {
          $result = FALSE;
          if (("" != $this->_config_data['sql_server']) &&
              ("" != $this->_config_data['sql_username']) &&
              ("" != $this->_config_data['sql_password']) &&
              ("" != $this->_config_data['sql_database']) &&
              ("" != $this->_config_data['sql_schema'])) {
              $sql_server = $this->_config_data['sql_server'];
              if (FALSE !== ($pos = mb_strpos($this->_config_data['sql_server'], ":"))) {
                  $sql_server_array = explode(":", $sql_server, 2);
                  $sql_server = $sql_server_array[0];
                  $sql_port   = $sql_server_array[1];
              } else {
                  $sql_port = "5432";
              }
              $pgsql_connect_string = "host=$sql_server ";
              $pgsql_connect_string.= "port=$sql_port ";
              $pgsql_connect_string.= "dbname=".$this->_config_data['sql_database']." ";
              $pgsql_connect_string.= "user=".$this->_config_data['sql_username']." ";
              $pgsql_connect_string.= "password=".$this->_config_data['sql_password'];
              if (!($this->_pgsql_database_link = pg_connect($pgsql_connect_string))) {
                  $this->WriteLog("Error: Bad SQL authentication parameters, ".pg_last_error(), TRUE, FALSE, 41, 'System', '', 3);
              } else {
                  $result = TRUE;
              }
          }
      }
      return $result;
  }


  function InitializeBackend()
  {
      $write_config_data = false;
      $backend_type = $this->GetBackendType();
      if ('mysql' == $backend_type) {
          if ($this->OpenMysqlDatabase()) {
              foreach ($this->_sql_tables as $sql_table) {
                  if ("" != $this->_config_data['sql_'.$sql_table.'_table']) {
                      $sql_query = "CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_'.$sql_table.'_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));";
                      if (is_object($this->_mysqli)) {
                          if (!($result = $this->_mysqli->query($sql_query))) {
                              $this->WriteLog("Error: Bad SQL request ($sql_query), ".trim($this->_mysqli->error), TRUE, FALSE, 40, 'System', '', 3);
                              return 41;
                          }
                      } elseif (!mysql_query($sql_query, $this->_mysql_database_link)) {
                          $this->WriteLog("Error: Bad SQL request (CREATE TABLE ".$this->_config_data['sql_'.$sql_table.'_table']."), ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                          return 41;
                      }
                      reset($this->_sql_tables_schema[$sql_table]);

                      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema[$sql_table])) {
                          $this->MySqlAddRowIfNeeded($this->_config_data['sql_'.$sql_table.'_table'], $valid_key, $valid_format, (FALSE !== mb_strpos($this->_sql_tables_index[$sql_table], "*".$valid_key."*")));
                      }
                  }
              }
              $this->SetBackendTypeValidated($backend_type, TRUE);
              $write_config_data = true;
          }
      } elseif ('pgsql' == $backend_type) {
          if ($this->OpenPGSQLDatabase()) {
              foreach ($this->_sql_tables as $sql_table) {
                  if ("" != $this->_config_data['sql_'.$sql_table.'_table']) {
                      $sql_query = "CREATE TABLE IF NOT EXISTS \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_'.$sql_table.'_table']."\" (\"unique_id\" BIGSERIAL PRIMARY KEY);";
                      if (!pg_query($this->_pgsql_database_link, $sql_query)) {
                          $this->WriteLog("Error: Bad SQL request (CREATE TABLE ".$_config_data['sql_schema'].".".$this->_config_data['sql_'.$sql_table.'_table']."), ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                          return 41;
                      }
                      reset($this->_sql_tables_schema[$sql_table]);
                      
                      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema[$sql_table])) {
                          $row_format = $valid_format;
                          $row_default = NULL;
                          if (mb_strtolower(substr($row_format, 0, 4)) == "int(") {
                            $row_format = str_ireplace("int(", "numeric(", $row_format);
                          } elseif (mb_strtolower(substr($row_format, 0, 8)) == "datetime") {
                            $row_format = str_ireplace("datetime", "timestamp", $row_format);
                          }
                          $pos = mb_strpos(mb_strtoupper($row_format), 'DEFAULT');
                          if ($pos !== FALSE) {
                            $row_default = trim(substr($valid_format, $pos + strlen("DEFAULT")));
                            $row_format = trim(substr($row_format, 0, $pos));
                          }
                          $this->PGSQLAddRowIfNeeded($this->_config_data['sql_'.$sql_table.'_table'], $valid_key, $row_format, $row_default, (FALSE !== mb_strpos($this->_sql_tables_index[$sql_table], "*".$valid_key."*")));
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


  function IsOptionInSchema(
      $schema,
      $option
  ) {
      $in_the_schema = FALSE;
      if (isset($this->_sql_tables_schema[$schema])) {
          reset($this->_sql_tables_schema[$schema]);
          while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema[$schema])) {
              if ($valid_key == $option) {
                  $in_the_schema = TRUE;
                  break;
              }
          }
      }
      return $in_the_schema;
  }


  function ReadConfigData(
      $encryption_only = false,
      $encryption_key_param = ''
  ) {
      $result = FALSE;
     
      $encryption_key = $encryption_key_param ;

      if ('' == $encryption_key) {
          $encryption_key = $this->GetEncryptionKey();
      }

      // We initialize the encryption hash to empty
      $this->_config_data['encryption_hash'] = "";

      // First, we read the config file in any case
      $config_filename = 'multiotp.ini'; // File exists in v3 format only, we don't need any conversion
      if (file_exists($this->GetConfigFolder().$config_filename))
      {
          if ($file_handler = @fopen($this->GetConfigFolder().$config_filename, "rt")) {
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
                          $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$encryption_key);
                      }
                      if ("" != $line_array[0])
                      {
                          $this->_config_data[mb_strtolower($line_array[0])] = $line_array[1];
                      }
                  }
              }
              fclose($file_handler);
              $result = TRUE;
              if (("" != $this->_config_data['encryption_hash']) && (!$encryption_only))
              {
                  if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($encryption_key))
                  {
                      $this->_config_data['encryption_hash'] = "ERROR";
                      $this->WriteLog("Error: the configuration encryption key is not matching", FALSE, FALSE, 33, 'System', '', 3);
                      $result = FALSE;
                  }
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

                              if (is_object($this->_mysqli)) {
                                  if (!($result = @$this->_mysqli->query($sQuery))) {
                                      $this->WriteLog("Error: ".trim($this->_mysqli->error)." ".$sQuery, TRUE, FALSE, 41, 'System', '', 3);
                                      $result = FALSE;
                                  } else {
                                      $aRow = $result->fetch_assoc();
                                  }
                              } else {
                                  if (!($rResult = @mysql_query($sQuery, $this->_mysql_database_link))) {
                                      $this->WriteLog("Error: ".mysql_error()." ".$sQuery, TRUE, FALSE, 41, 'System', '', 3);
                                      $result = FALSE;
                                  } else {
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
                                              break;
                                          }
                                      }
                                      if ($in_the_schema) {
                                          if (FALSE === mb_strpos($this->_sql_tables_ignore['config'], "*".$valid_key."*")) {
                                              if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                                  $value = substr($value,4);
                                                  $value = substr($value,0,strlen($value)-4);
                                                  $this->_config_data[$key] = $this->Decrypt($key,$value,$encryption_key);
                                              } else {
                                                  $this->_config_data[$key] = $value;
                                              }
                                          }
                                      } elseif (('unique_id' != $key) && $this->GetVerboseFlag()) {
                                          $this->WriteLog("Warning: *the key ".$key." is not in the config database schema", FALSE, FALSE, 8888, 'System', '', 3);
                                      }
                                  }
                              }
                          }
                          if (("" != $this->_config_data['encryption_hash']) && ($this->_encryption_check)) {
                              if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($encryption_key)) {
                                  $this->_config_data['encryption_hash'] = "ERROR";
                                  $this->WriteLog("Error: the configuration mysql encryption key is not matching", FALSE, FALSE, 33, 'System', '', 3);
                                  $result = FALSE;
                              }
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          if ("" != $this->_config_data['sql_config_table']) {
                              $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_config_table']."\" ";
                              
                              $aRow = NULL;

                              if (!($rResult = @pg_query($this->_pgsql_database_link, $sQuery))) {
                                  $this->WriteLog("Error: ".pg_last_error()." ".$sQuery, TRUE, FALSE, 41, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = pg_fetch_assoc($rResult);
                              }

                              if (NULL != $aRow) {
                                  $result = TRUE;
                                  while(list($key, $value) = @each($aRow)) {
                                      $in_the_schema = FALSE;
                                      reset($this->_sql_tables_schema['config']);
                                      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['config'])) {
                                          if ($valid_key == $key) {
                                              $in_the_schema = TRUE;
                                              break;
                                          }
                                      }
                                      if ($in_the_schema) {
                                          if (FALSE === mb_strpos($this->_sql_tables_ignore['config'], "*".$valid_key."*")) {
                                              if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                                  $value = substr($value,4);
                                                  $value = substr($value,0,strlen($value)-4);
                                                  $this->_config_data[$key] = $this->Decrypt($key,$value,$encryption_key);
                                              } else {
                                                  $this->_config_data[$key] = $value;
                                              }
                                          }
                                      } elseif (('unique_id' != $key) && $this->GetVerboseFlag()) {
                                          $this->WriteLog("Warning: *the key ".$key." is not in the config database schema", FALSE, FALSE, 8888, 'System', '', 3);
                                      }
                                  }
                              }
                          }
                          if (("" != $this->_config_data['encryption_hash']) && ($this->_encryption_check)) {
                              if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($encryption_key)) {
                                  $this->_config_data['encryption_hash'] = "ERROR";
                                  $this->WriteLog("Error: the configuration pgsql encryption key is not matching", FALSE, FALSE, 33, 'System', '', 3);
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
          
          if (isset($this->_config_data['log']) && (1 == $this->_config_data['log'])) {
              $this->EnableLog();
          }

          if (isset($this->_config_data['debug']) && (1 == $this->_config_data['debug'])) {
              $this->EnableVerboseLog();
          }

          if (isset($this->_config_data['display_log']) && (1 == $this->_config_data['display_log'])) {
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


  function WriteConfigData(
      $write_config_data_array = array()
  ) {
      if ($this->IsDeveloperMode()) {
        $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
        foreach ($backtrace as $one_backtrace) {
          $file = isset($one_backtrace['file'])?$one_backtrace['file']:"";
          $line = isset($one_backtrace['line'])?$one_backtrace['line']:"";
          $class = isset($one_backtrace['class'])?$one_backtrace['class']."::":"";
          $function = isset($one_backtrace['function'])?$one_backtrace['function']:"";
          $this->WriteLog("Developer: *WriteConfigData $file:$line $class$function()", FALSE, FALSE, 8888, 'Debug', '');
        }
      }

      $result = $this->WriteData(array_merge(array('item'       => 'Configuration',
                                                   'table'      => 'config',
                                                   'folder'     => $this->GetConfigFolder(true),
                                                   'data_array' => $this->_config_data,
                                                   'force_file' => true
                                                  ), $write_config_data_array));
      return $result;
  }


  // Reset the temporary user array
  function ResetTempUserArray()
  {
      $temp_user_array = array();

      // First, we reset all values (we know the key based on the schema)
      reset($this->_sql_tables_schema['users']);
      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users'])) {
          $pos = mb_strpos(mb_strtoupper($valid_format), 'DEFAULT');
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
  function ResetUserArray()
  {
      $this->_user_data = array();
      $this->_user_data = $this->ResetTempUserArray();

      // The user data array is not read actually
      $this->SetUserDataReadFlag(false);
  }


  function ResetTokenArray()
  {
      // First, we reset all values (we know the key based on the schema)
      reset($this->_sql_tables_schema['tokens']);
      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens'])) {
          $pos = mb_strpos(mb_strtoupper($valid_format), 'DEFAULT');
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


  function ResetDeviceArray()
  {
      reset($this->_sql_tables_schema['devices']);
      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['devices'])) {
          $pos = mb_strpos(mb_strtoupper($valid_format), 'DEFAULT');
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


  function ResetGroupArray()
  {
      reset($this->_sql_tables_schema['groups']);
      while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['groups'])) {
          $pos = mb_strpos(mb_strtoupper($valid_format), 'DEFAULT');
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


  function GetClassName()
  {
      return $this->_class;
  }


  function GetVersion()
  {
      return $this->_version;
  }


  function GetDate()
  {
      return $this->_date;
  }


  function GetVersionDate()
  {
      return $this->_version." (".$this->_date.")";
  }


  function GetUptime(
      $text_output = true
  ) {
      $uptime = '';
      if (file_exists('/proc/uptime')) {
          $file = @fopen('/proc/uptime', 'r');
          if ($file) {
              $data = @fread($file, 128);
              if ($data !== false) {
                  $upsecs = (int)substr($data, 0, mb_strpos($data, ' '));
                  $days = floor($upsecs/60/60/24);
                  $hours = $upsecs/60/60%24;
                  $minutes = $upsecs/60%60;
                  $seconds = $upsecs%60;
                  // $uptime = Array ( 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds );
                  $uptime = $days." day".(($days>1)?'s':'').", ".substr('00'.$hours, -2).':'.substr('00'.$minutes, -2).':'.substr('00'.$seconds, -2);
              }
              fclose($file);
          }
      }
      else
      {
          $pagefile = 'C:\pagefile.sys';
          if (!is_file($pagefile)) {
              $pagefile = 'D:\pagefile.sys';
              if (!is_file($pagefile)) {
                  $pagefile = '';
              }
          }
          if ('' != $pagefile) {
              $gettime = (time() - filemtime($pagefile));
              $upsecs = $gettime;
              $days = floor($gettime / (24 * 3600));
              $gettime = $gettime - ($days * (24 * 3600));
              $hours = floor($gettime / (3600));
              $gettime = $gettime - ($hours * (3600));
              $minutes = floor($gettime / (60));
              $gettime = $gettime - ($minutes * 60);
              $seconds = $gettime; - ($seconds * 1);
              $uptime = $days." day".(($days>1)?'s':'').", ".substr('00'.$hours, -2).':'.substr('00'.$minutes, -2).':'.substr('00'.$seconds, -2);
          }
      }
      return ($text_output?$uptime:$upsecs);
  }


  function GetFullVersionInfo()
  {
      return $this->_class." ".$this->_version." (".$this->_date.")";
  }


  function GetCopyright()
  {
      return $this->_copyright;
  }


  function GetWebsite()
  {
      return $this->_website;
  }


  function SetSourceTag(
      $value = ""
  ) {
      $this->_source_tag = trim($value);
  }


  function GetSourceTag()
  {
      return trim($this->_source_tag);
  }


  function SetSourceIp(
      $value = ""
  ) {
      $this->_source_ip = $value;
  }


  function GetSourceIp()
  {
      return $this->_source_ip;
  }


  function SetSourceMac(
      $value
  ) {
      $this->_source_mac = $value;
  }


  function GetSourceMac()
  {
      return $this->_source_mac;
  }


  function SetCallingIp(
      $value
  ) {
      $this->_calling_ip = $value;
  }


  function GetCallingIp()
  {
      return $this->_calling_ip;
  }


  function SetCallingMac(
      $value
  ) {
      $this->_calling_mac = $value;
  }


  function GetCallingMac()
  {
      return $this->_calling_mac;
  }


  function SetChapChallenge(
      $hex_value
  ) {
      $pos = mb_strpos(mb_strtolower($hex_value), 'x');
      if (FALSE === $pos) {
          $temp = $hex_value;
      } else {
          $temp = substr($hex_value, $pos+1);
      }
      $this->_chap_challenge = mb_strtolower($temp);
  }


  function GetChapChallenge()
  {
      return mb_strtolower($this->_chap_challenge);
  }


  function SetChapPassword(
      $hex_value
  ) {
      $pos = mb_strpos(mb_strtolower($hex_value), 'x');
      if (FALSE === $pos) {
          $temp = $hex_value;
      } else {
          $temp = substr($hex_value, $pos+1);
      }
      
      if (32 < strlen($temp)) {
          $this->SetChapId(substr($temp, 0, 2));
          $temp = substr($temp, 2);
      }
      $this->_chap_password = mb_strtolower($temp);
  }


  function GetChapPassword()
  {
      return mb_strtolower($this->_chap_password);
  }


  function SetMsChapChallenge(
      $hex_value
  ) {
      $pos = mb_strpos(mb_strtolower($hex_value), 'x');
      if (FALSE === $pos) {
          $temp = $hex_value;
      } else {
          $temp = substr($hex_value, $pos+1);
      }
      $this->_ms_chap_challenge = mb_strtolower($temp);
  }


  function GetMsChapChallenge()
  {
      return mb_strtolower($this->_ms_chap_challenge);
  }


  function SetMsChapResponse(
      $hex_value
  ) {
      $pos = mb_strpos(mb_strtolower($hex_value), 'x');
      if (FALSE === $pos) {
          $temp = $hex_value;
      } else {
          $temp = substr($hex_value, $pos+1);
      }
      $this->_ms_chap_response = mb_strtolower($temp);
  }


  function GetMsChapResponse()
  {
      return mb_strtolower($this->_ms_chap_response);
  }


  function SetMsChap2Response(
      $hex_value
  ) {
      $pos = mb_strpos(mb_strtolower($hex_value), 'x');
      if (FALSE === $pos) {
          $temp = $hex_value;
      } else {
          $temp = substr($hex_value, $pos+1);
      }
      $this->_ms_chap2_response = mb_strtolower($temp);
  }


  function GetMsChap2Response()
  {
      return mb_strtolower($this->_ms_chap2_response);
  }


  function SetChapId(
      $hex_value
  ) {
      $pos = mb_strpos(mb_strtolower($hex_value), 'x');
      if (FALSE === $pos) {
          $temp = $hex_value;
      } else {
          $temp = substr($hex_value, $pos+1);
      }
      $this->_chap_id = mb_strtolower($temp);
  }


  function GetChapId()
  {
      return mb_strtolower($this->_chap_id);
  }


  function SetNtKey(
      $hex_value
  ) {
      $temp = $hex_value;
      if (16 == strlen($temp)) {
          $temp = bin2hex($temp);
      }
      $pos = mb_strpos(mb_strtolower($temp), 'x');
      if (FALSE !== $pos) {
          $temp = substr($temp, $pos+1);
      }
      if (32 != strlen($temp)) {
          $temp = '';
      }
      $this->_ms_nt_key = mb_strtoupper($temp);
  }


  function GetNtKey()
  {
      $temp = $this->_ms_nt_key;
      if (16 == strlen($temp)) {
          $temp = bin2hex($temp);
      } elseif (32 != strlen($temp)) {
          $temp = '';
      }
      return mb_strtoupper($temp);
  }


  function GetSmsProvidersArray()
  {
      return $this->_sms_providers_array;
  }


  function GetSmsProvidersList()
  {
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


  function GetSmsProvider()
  {
      return $this->_config_data['sms_provider'];
  }


  function SetSmsOriginator(
      $value
  ) {
      $this->_config_data['sms_originator'] = $value;
  }


  function GetSmsOriginator()
  {
      return $this->_config_data['sms_originator'];
  }


  function SetTelDefaultCountryCode(
      $value
  ) {
      $this->_config_data['tel_default_country_code'] = $value;
  }


  function GetTelDefaultCountryCode()
  {
      return $this->_config_data['tel_default_country_code'];
  }


  function SetSmsUserkey(
      $value
  ) {
      $this->_config_data['sms_userkey'] = $value;
  }


  function GetSmsUserkey()
  {
      return $this->_config_data['sms_userkey'];
  }


  function SetSmsPassword(
      $value
  ) {
      $this->_config_data['sms_password'] = $value;
  }


  function GetSmsPassword()
  {
      return $this->_config_data['sms_password'];
  }


  function SetSmsApiId(
      $value
  ) {
      $this->_config_data['sms_api_id'] = $value;
  }


  function GetSmsApiId()
  {
      return $this->_config_data['sms_api_id'];
  }


  function SetDefaultAlgorithm(
      $value
  ) {
      $this->_config_data['default_algorithm'] = ((intval($value) > 0)?1:0);
  }


  function GetDefaultAlgorithm()
  {
      return $this->_config_data['default_algorithm'];
  }

  function SetDefaultRequestLdapPwd(
      $value
  ) {
      $this->_config_data['default_request_ldap_pwd'] = ((intval($value) > 0)?1:0);
  }


  function GetDefaultRequestLdapPwd()
  {
      return $this->_config_data['default_request_ldap_pwd'];
  }


  function IsDefaultRequestLdapPwd()
  {
      return (1 == ($this->_config_data['default_request_ldap_pwd']));
  }


  function SetOverwriteRequestLdapPwd(
      $value
  ) {
      $this->_config_data['overwrite_request_ldap_pwd'] = ((intval($value) > 0)?1:0);
  }


  function GetOverwriteRequestLdapPwd()
  {
      return $this->_config_data['overwrite_request_ldap_pwd'];
  }


  function IsOverwriteRequestLdapPwd()
  {
      return (1 == ($this->_config_data['overwrite_request_ldap_pwd']));
  }


  function SetDefaultRequestPrefixPin(
      $value
  ) {
      $this->_config_data['default_request_prefix_pin'] = ((intval($value) > 0)?1:0);
  }


  function GetDefaultRequestPrefixPin()
  {
      return $this->_config_data['default_request_prefix_pin'];
  }


  function IsDefaultRequestPrefixPin()
  {
      return (1 == ($this->_config_data['default_request_prefix_pin']));
  }


  function EnableLdapError()
  {
      $this->_last_ldap_error = TRUE;
  }


  function DisableLdapError()
  {
      $this->_last_ldap_error = FALSE;
  }


  function IsLdapError()
  {
      return $this->_last_ldap_error;
  }


  function SetLdapActivated(
      $value
  ) {
      $this->_config_data['ldap_activated'] = ((intval($value) > 0) ? 1 : 0);
      if (1 > intval($value)) {
          $this->PurgeLdapCacheFolder();
      }
  }


  function EnableLdapActivated()
  {
      $this->SetLdapActivated(1);
  }


  function DisableLdapActivated()
  {
      $this->SetLdapActivated(0);
  }


  function IsLdapActivated()
  {
      return (1 == ($this->_config_data['ldap_activated']));
  }


  function SetLdapSsl(
      $value
  ) {
      $this->_config_data['ldap_ssl'] = ((intval($value) > 0)?1:0);
  }


  function EnableLdapSsl()
  {
      $this->_config_data['ldap_ssl'] = 1;
  }


  function DisableLdapSsl()
  {
      $this->_config_data['ldap_ssl'] = 0;
  }


  function IsLdapSsl()
  {
      return (1 == ($this->_config_data['ldap_ssl']));
  }


  function SetLdapLanguageAttribute(
      $value
  ) {
      if ('' != trim($value)) {
          $this->_config_data['ldap_language_attribute'] = trim($value);
      }
  }


  function GetLdapLanguageAttribute()
  {
      return ($this->_config_data['ldap_language_attribute']);
  }


  function SetLdapAccountSuffix(
      $value
  ) {
      $this->_config_data['ldap_account_suffix'] = $value;
  }


  function GetLdapAccountSuffix()
  {
      return $this->_config_data['ldap_account_suffix'];
  }


  function SetLdapCnIdentifier(
      $value
  ) {
    if ('' != trim($value)) {
        $this->_config_data['ldap_cn_identifier'] = trim($value);
    }
  }


  function GetLdapCnIdentifier()
  {
    return ($this->_config_data['ldap_cn_identifier']);
  }


  function SetLdapSyncedUserAttribute(
      $value
  ) {
    $this->_config_data['ldap_synced_user_attribute'] = trim($value);
  }


  function GetLdapSyncedUserAttribute()
  {
    $result = ($this->_config_data['ldap_synced_user_attribute']);
    if ("" == trim($result)) {
      $result = $this->GetLdapCnIdentifier();
    }
    return $result;
  }


  function SetLdapDefaultAlgorithm(
      $value
  ) {
      if ($this->IsValidAlgorithm($value)) {
        $this->_config_data['ldap_default_algorithm'] = $value;
      }
  }


  function GetLdapDefaultAlgorithm()
  {
      return $this->_config_data['ldap_default_algorithm'];
  }


  function SetLdapGroupCnIdentifier(
      $value
  ) {
      if ('' != trim($value)) {
          $this->_config_data['ldap_group_cn_identifier'] = trim($value);
      }
  }


  function GetLdapGroupCnIdentifier()
  {
      return ($this->_config_data['ldap_group_cn_identifier']);
  }


  function GetLdapFieldsArray()
  {
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
                               "distinguishedName",
                               "msRADIUSFramedIPAddress", // Static IP address to be assigned to the account
                               $this->GetLdapLanguageAttribute(),
                               $this->GetLdapSyncedUserAttribute(),
                               "userPrincipalName" // post-2000 Windows account
                              );
      } else { // Generic LDAP, no attribute like "msNPAllowDialin" or "msRADIUSFramedIPAddress"
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
                               "gecos", // general information about the account
                               "telephoneNumber",
                               "gidNumber",
                               "mobile",
                               "sambaAcctFlags",
                               "shadowExpire",
                               "distinguishedName",
                               "radiusFramedIPAddress",
                               "radiusFramedIPNetmask",
                               $this->GetLdapLanguageAttribute(),
                               $this->GetLdapSyncedUserAttribute()
                              );
      }
      return ($ldap_fields);
  }


  function SetLdapBaseDn(
      $value
  ) {
      $this->_config_data['ldap_base_dn'] = $value;
  }


  function GetLdapBaseDn()
  {
      return $this->_config_data['ldap_base_dn'];
  }


  function SetLdapGroupsDn(
      $value
  ) {
      $this->_config_data['ldap_groups_dn'] = $value;
  }


  function GetLdapGroupsDn()
  {
      return $this->_config_data['ldap_groups_dn'];
  }


  function SetLdapBindDn(
      $value
  ) {
      $this->_config_data['ldap_bind_dn'] = $value;
  }


  function GetLdapBindDn()
  {
      return encode_utf8_if_needed($this->_config_data['ldap_bind_dn']);
  }


  function SetLdapDomainControllers(
      $value
  ) {
      $this->_config_data['ldap_domain_controllers'] = trim($value);
  }


  function GetLdapDomainControllers()
  {
      return $this->_config_data['ldap_domain_controllers'];
  }


  function GetLdapPrimaryController()
  {
      $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()));
      $controllers_array = explode(" ",$this->GetLdapDomainControllers());
      return trim(isset($controllers_array[0])?$controllers_array[0]:'');
  }


  function GetLdapSecondaryController()
  {
      $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()));
      $controllers_array = explode(" ",$this->GetLdapDomainControllers());
      return trim(isset($controllers_array[1])?$controllers_array[1]:'');
  }


  function SetLdapInGroup(
      $value
  ) {
      $this->_config_data['ldap_in_group'] = $value;

      $ldap_in_group_array = explode("§",trim(str_replace(",","§",str_replace(";","§",$value))));

      $groups_array = array();
      $list = explode("\t", $this->GetGroupsList());
      $n = count($list);
      for($i = 0; $i < $n; $i++) {
          if($list[$i] != '') {
              $this->SetGroup($list[$i]);
              $groups_array[] = $this->GetGroupName();
          }
      }

      foreach($ldap_in_group_array as $one_group) {
          if (!in_array(trim($one_group), $groups_array)) {
              if ('' != trim($one_group)) {
                  $this->CreateGroup('', trim($one_group), trim($one_group));
              }
          }
      }
  }


  function GetLdapInGroup()
  {
      return $this->_config_data['ldap_in_group'];
  }


  function SetLdapGroupAttribute(
      $value
  ) {
      if ('' != trim($value)) {
          $this->_config_data['ldap_group_attribute'] = trim($value);
      }
  }


  function GetLdapGroupAttribute()
  {
      return ($this->_config_data['ldap_group_attribute']);
  }


  function SetLdapServerPassword(
      $value
  ) {
      $this->_config_data['ldap_server_password'] = $value;
  }


  function GetLdapServerPassword()
  {
      return encode_utf8_if_needed($this->_config_data['ldap_server_password']);
  }


  function SetLdapPort(
      $value
  ) {
      $this->_config_data['ldap_port'] = intval($value);
  }


  function GetLdapPort()
  {
      return $this->_config_data['ldap_port'];
  }


  function SetLdapServerType(
      $value,
      $default_parameters = false
  ) {
      $this->_config_data['ldap_server_type'] = intval($value);
      
      // These values are not in the options for now
      if (1 == $value) { // Active Directory
          $this->SetLdapGroupCnIdentifier('cn');
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


  function GetLdapServerType()
  {
      return $this->_config_data['ldap_server_type'];
  }


  function SetLdapTimeLimit(
      $value
  ) {
      $this->_config_data['ldap_time_limit'] = intval($value);
  }


  function GetLdapTimeLimit()
  {
      return $this->_config_data['ldap_time_limit'];
  }


  function SetLdapNetworkTimeout(
      $value
  ) {
      $this->_config_data['ldap_network_timeout'] = intval($value);
  }


  function GetLdapNetworkTimeout()
  {
      return $this->_config_data['ldap_network_timeout'];
  }


  function SetLdapHashCacheTime(
      $value
  ) {
      $this->_config_data['ldap_hash_cache_time'] = intval($value);
  }


  function GetLdapHashCacheTime()
  {
      return $this->_config_data['ldap_hash_cache_time'];
  }


  function SetLdapTlsReqcert(
      $value
  ) {
      $this->_config_data['ldaptls_reqcert'] = trim($value);
  }


  function GetLdapTlsReqcert()
  {
      return $this->_config_data['ldaptls_reqcert'];
  }


  function SetLdapTlsCipherSuite(
      $value
  ) {
      $this->_config_data['ldaptls_cipher_suite'] = trim($value);
  }


  function GetLdapTlsCipherSuite()
  {
      return $this->_config_data['ldaptls_cipher_suite'];
  }


  function SetSmsMessage(
      $value
  ) {
      $this->_config_data['sms_message_prefix'] = $value;
  }


  function GetSmsMessage()
  {
      return $this->_config_data['sms_message_prefix'];
  }


  function SetSmsDigits(
      $value
  ) {
      $this->_config_data['sms_digits'] = intval($value);
  }


  function GetSmsDigits()
  {
      return $this->_config_data['sms_digits'];
  }


  function SetSmsTimeout(
      $value
  ) {
      $this->_config_data['sms_timeout'] = intval($value);
  }


  function GetSmsTimeout()
  {
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


  function GetMaxTimeWindow()
  {
      return $this->_config_data['max_time_window'];
  }


  function SetMaxTimeResyncWindow(
      $time_resync_window
  ) {
      $this->_config_data['max_time_resync_window'] = intval($time_resync_window);
  }


  function GetMaxTimeResyncWindow()
  {
      return $this->_config_data['max_time_resync_window'];
  }


  function SetMaxEventWindow(
      $event_window
  ) {
      $this->_config_data['max_event_window'] = intval($event_window);
  }


  function GetMaxEventWindow()
  {
      return $this->_config_data['max_event_window'];
  }


  function SetMaxEventResyncWindow(
      $event_resync_window
  ) {
      $this->_config_data['max_event_resync_window'] = intval($event_resync_window);
  }


  function GetMaxEventResyncWindow()
  {
      return $this->_config_data['max_event_resync_window'];
  }


  function SetMaxBlockFailures(
      $max_failures
  ) {
      $this->_config_data['max_block_failures'] = $max_failures;
  }


  function GetMaxBlockFailures()
  {
      return $this->_config_data['max_block_failures'];
  }


  function SetServerCacheLevel(
      $value
  ) {
      $this->_config_data['server_cache_level'] = intval($value);
  }


  function GetServerCacheLevel()
  {
      return intval($this->_config_data['server_cache_level']);
  }


  function SetServerCacheLifetime(
      $value
  ) {
      $this->_config_data['server_cache_lifetime'] = intval($value);
  }


  function GetServerCacheLifetime()
  {
      return intval($this->_config_data['server_cache_lifetime']);
  }


  function SetServerChallenge(
      $value
  ) {
      $this->_server_challenge = $value;
  }


  function GetServerChallenge()
  {
      return $this->_server_challenge;
  }


  function SetServerSecret(
      $value
  ) {
      $this->_config_data['server_secret'] = $value;
  }


  function GetServerSecret($specific_ip = "") {
      return $this->_config_data['server_secret'];
  }


  function SetServerType(
      $value
  ) {
      $this->_config_data['server_type'] = $value;
  }


  function GetServerType()
  {
      return $this->_config_data['server_type'];
  }


  function SetServerTimeout(
      $value
  ) {
      $this->_config_data['server_timeout'] = intval($value);
  }


  function GetServerTimeout()
  {
      return intval($this->_config_data['server_timeout']);
  }


  function SetServerUrl(
      $value
  ) {
      $cleaned_server_url = trim(str_replace(",",";",str_replace(" ",";",$value)));
      $this->_config_data['server_url'] = $cleaned_server_url;
  }


  function GetServerUrl()
  {
      $cleaned_server_url = trim(str_replace(",",";",str_replace(" ",";",$this->_config_data['server_url'])));
      $this->_config_data['server_url'] = $cleaned_server_url;
      return $cleaned_server_url;
  }


  function SetSelfRegistration(
      $value
  ) {
      $this->_config_data['self_registration'] = ((intval($value) > 0)?1:0);
  }


  function EnableSelfRegistration()
  {
      $this->_config_data['self_registration'] = 1;
  }


  function DisableSelfRegistration()
  {
      $this->_config_data['self_registration'] = 0;
  }


  function IsSelfRegistrationEnabled()
  {
      return (1 == ($this->_config_data['self_registration']));
  }


  function SetAutoResync(
      $value
  ) {
      $this->_config_data['auto_resync'] = ((intval($value) > 0)?1:0);
  }


  function EnableAutoResync()
  {
      $this->_config_data['auto_resync'] = 1;
  }


  function DisableAutoResync()
  {
      $this->_config_data['auto_resync'] = 0;
  }


  function IsAutoResync()
  {
      return (1 == ($this->_config_data['auto_resync']));
  }


  function SetCacheData(
      $value
  ) {
      $this->_config_data['cache_data'] = ((intval($value) > 0)?1:0);
  }


  function EnableCacheData()
  {
      $this->_config_data['cache_data'] = 1;
  }


  function DisableCacheData()
  {
      $this->_config_data['cache_data'] = 0;
  }


  function IsCacheData()
  {
      return (1 == ($this->_config_data['cache_data']));
  }


  function SetCaseSensitiveUsers()
  {
      $this->_config_data['case_sensitive_users'] = ((intval($value) > 0)?1:0);
  }


  function EnableCaseSensitiveUsers()
  {
      $this->_config_data['case_sensitive_users'] = 1;
  }


  function DisableCaseSensitiveUsers()
  {
      $this->_config_data['case_sensitive_users'] = 0;
  }


  function IsCaseSensitiveUsers()
  {
      return (1 == ($this->_config_data['case_sensitive_users']));
  }


  function SetEncodeFileId()
  {
      $this->_config_data['encode_file_id'] = ((intval($value) > 0)?1:0);
  }


  function EnableEncodeFileId()
  {
      $this->_config_data['encode_file_id'] = 1;
  }


  function DisableEncodeFileId()
  {
      $this->_config_data['encode_file_id'] = 0;
  }


  function IsEncodeFileId()
  {
      return (1 == ($this->_config_data['encode_file_id']));
  }


  function EncodeFileId($id, $case_sensitive = FALSE, $force_regular = FALSE) {
      if ($this->IsEncodeFileId() && (!$force_regular)) {
          if ($case_sensitive) {
              return "id0x".bin2hex($id);
          } else {
              return "id0x".bin2hex(mb_strtolower($id));
          }
      } else {
          if ($case_sensitive) {
              return str_replace('/','',$id);
          } else {
              return mb_strtolower(str_replace('/','',$id));
          }
      }
  }


  function DecodeFileId($id) {
      if ("id0x" == substr($id."  ", 0, 4)) {
          return hex2bin(substr($id, 4));
      } else {
         return $id;
      }
  }


  function SetNtpServer(
      $ntp_server
  ) {
      $this->_config_data['ntp_server'] = $ntp_server;
  }


  function GetNtpServer()
  {
      return trim($this->_config_data['ntp_server']);
  }


  function SetRadiusReplyAttributor(
      $radius_reply_attributor
  ) {
      $this->_config_data['radius_reply_attributor'] = $radius_reply_attributor;
  }


  function GetRadiusReplyAttributor()
  {
      return ($this->_config_data['radius_reply_attributor']);
  }


  function SetRadiusReplySeparator(
      $radius_reply_separator
  ) {
      switch (mb_strtolower($radius_reply_separator)) {
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

  
  function SetRadiusErrorReplyMessage(
      $radius_error_reply_message
  ) {
      $this->_config_data['radius_error_reply_message'] = intval($radius_error_reply_message);
  }


  function GetRadiusErrorReplyMessage()
  {
      return intval($this->_config_data['radius_error_reply_message']);
  }


  function IsRadiusErrorReplyMessage() {
      return (1 == $this->_config_data['radius_error_reply_message']);
  }


  function SetTimezone(
      $timezone
  ) {
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


  function SetSmtpAuth(
      $value
  ) {
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


  function SetSmtpPassword(
      $value
  ) {
      $this->_config_data['smtp_password'] = $value;
  }


  function GetSmtpPassword()
  {
      return $this->_config_data['smtp_password'];
  }


  function SetSmtpPort(
      $value
  ) {
      $this->_config_data['smtp_port'] = intval($value);
  }


  function GetSmtpPort()
  {
      return intval($this->_config_data['smtp_port']);
  }


  function SetSmtpSender(
      $value
  ) {
      $this->_config_data['smtp_sender'] = $value;
  }


  function GetSmtpSenderName()
  {
      return $this->_config_data['smtp_sender_name'];
  }


  function SetSmtpSenderName(
      $value
  ) {
      $this->_config_data['smtp_sender_name'] = $value;
  }


  function GetSmtpSender()
  {
      return $this->_config_data['smtp_sender'];
  }


  function SetSmtpServer(
      $value
  ) {
      if (!$this->IsDemoMode()) {
          $this->_config_data['smtp_server'] = $value;
      }
  }


  function GetSmtpServer()
  {
      return $this->_config_data['smtp_server'];
  }


  function SetSmtpSsl(
      $value
  ) {
      $this->_config_data['smtp_ssl'] = ((intval($value) > 0)?1:0);
  }


  function GetSmtpSsl()
  {
      return (($this->_config_data['smtp_ssl'] > 0)?1:0);
  }


  function SetSmtpUsername(
      $value
  ) {
      $this->_config_data['smtp_username'] = $value;
  }


  function GetSmtpUsername()
  {
      return $this->_config_data['smtp_username'];
  }


  function SetSyslogFacility(
      $value
  ) {
      $this->_config_data['syslog_facility'] = $value;
  }


  function GetSyslogFacility()
  {
      return $this->_config_data['syslog_facility'];
  }


  function SetSyslogLevel(
      $value
  ) {
      $this->_config_data['syslog_level'] = intval($value);
  }


  function GetSyslogLevel()
  {
      return intval($this->_config_data['syslog_level']);
  }


  function SetSysLogPort(
      $value
  ) {
      $this->_config_data['syslog_port'] = intval($value);
  }


  function GetSysLogPort()
  {
      return intval($this->_config_data['syslog_port']);
  }


  function SetSysLogServer(
      $value
  ) {
      $this->_config_data['syslog_server'] = $value;
  }


  function GetSysLogServer()
  {
      return $this->_config_data['syslog_server'];
  }


  function IsSysLogServerBad()
  {
    return $this->_bad_syslog_server;
  }


  function EnableBadSysLogServer()
  {
    $this->_bad_syslog_server = TRUE;
  }


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
  function DefineMySqlConnection(
      $sql_server,
      $sql_user,
      $sql_passwd,
      $sql_db,
      $sql_log_table = NULL,
      $sql_users_table = NULL,
      $sql_tokens_table = NULL
  ) {
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
  function ComputeMotp(
      $seed_and_pin,
      $timestep,
      $token_size
  ) {
      return mb_strtolower(substr(md5($timestep.$seed_and_pin),0,$token_size));
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
  function GenerateOathHotp(
      $key,
      $counter,
      $length = 6,
      $hash_algo = 'HMAC-SHA1'
  ) {
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
  function ComputeOathHotp(
      $key,
      $counter,
      $hash_algo = 'HMAC-SHA1'
  ) {
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
      if ('HMAC-SHA512' == mb_strtoupper($hash_algo))
      {
          $hash = hash_hmac('sha512', $bin_counter, $key);
      }
      elseif ('HMAC-SHA256' == mb_strtoupper($hash_algo))
      {
          $hash = hash_hmac('sha256', $bin_counter, $key);
      }
      elseif ('HMAC-MD5' == mb_strtoupper($hash_algo))
      {
          $hash = hash_hmac('md5', $bin_counter, $key);
      }
      else // if ('HMAC-SHA1' == mb_strtoupper($hash_algo))
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
  function ComputeOathTruncate(
      $hash,
      $length = 6
  ) {
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


  function CalculateChapPassword(
      $secret,
      $hex_chap_id = '',
      $hex_chap_challenge = ''
  ) {
      
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


  function Convert2Unicode(
      $value
  ) {
      $unicode = '';
      $string = (string) $value;
      for ($i = 0; $i < strlen($string); $i++)
      {
          $asc = ord($string{$i}) << 8;
          $unicode .= sprintf("%X", $asc);
      }
      return pack('H*', $unicode);
  }


  function Padding7to8(
      $value
  ) {
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


  function DesHashEcb(
      $clear
  ) {
      $cipher = new Crypt_DES(CRYPT_DES_MODE_ECB);
      $cipher->setKey($this->Padding7to8($clear));
      return $cipher->encrypt('KGS!@#$%');
  }


  function LmPasswordHash(
      $clear
  ) {
      $clear = substr(mb_strtoupper($clear.str_repeat("\0",14)), 0, 14);
      return substr($this->DesHashEcb(substr($clear, 0, 7)),0,8).substr($this->DesHashEcb(substr($clear, 7, 7)),0,8);
  }


  function NtPasswordHash(
      $clear
  ) {
      return pack('H*',hash('md4', $this->Convert2Unicode($clear)));
  }


  function NtPasswordHashHash(
      $hash
  ) {
      return pack('H*',hash('md4', $hash));
  }


  function CalculateMsChapResponse(
      $secret,
      $hex_mschap_challenge = '',
      $hex_mschap_response = ''
  ) {
      $temp_challenge = ('' != $hex_mschap_challenge)?$hex_mschap_challenge:$this->GetMsChapChallenge();
      $pos = mb_strpos(mb_strtolower($temp_challenge), 'x');
      if (FALSE !== $pos)
      {
          $temp_challenge = substr($temp_challenge, $pos+1);
      }

      $temp_response  = ('' != $hex_mschap_response)?$hex_mschap_response:$this->GetMsChapResponse();
      $this->SetMsChapResponse($temp_response);
      $pos = mb_strpos(mb_strtolower($temp_response), 'x');
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
          $result = mb_strtolower(bin2hex($mschap_response));
      }
      else
      {
          $result = 'Error: '.bin2hex($calculated_response).' instead of '.bin2hex($nt_response);
      }
      return $result;
  }


  function CheckMsChapResponse(
      $secret,
      $hex_mschap_challenge = '',
      $hex_mschap_response = ''
  ) {
      $result = $this->CalculateMsChapResponse($secret, $hex_mschap_challenge, $hex_mschap_response);
      
      return ($this->GetMsChapResponse() == mb_strtolower($result));
  }


  function CalculateMsChap2Response(
      $user,
      $secret,
      $domain = "",
      $hex_mschap_challenge = '',
      $hex_mschap2_response = ''
  ) {
      $temp_challenge = ('' != $hex_mschap_challenge)?$hex_mschap_challenge:$this->GetMsChapChallenge();
      $pos = mb_strpos(mb_strtolower($temp_challenge), 'x');
      if (FALSE !== $pos)
      {
          $temp_challenge = substr($temp_challenge, $pos+1);
      }

      $temp_response  = ('' != $hex_mschap2_response)?$hex_mschap2_response:$this->GetMsChap2Response();
      $this->SetMsChap2Response($temp_response);
      $pos = mb_strpos(mb_strtolower($temp_response), 'x');
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
                      $this->Convert2Unicode(mb_strtoupper($user).$domain)
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
          $result = mb_strtolower(bin2hex($mschap2_response));
      }
      else
      {
          $result = 'Error: '.bin2hex($calculated_response).' instead of '.bin2hex($nt_response);
      }
      return $result;
  }


  function CheckMsChap2Response(
      $user,
      $secret,
      $domain = '',
      $hex_mschap_challenge = '',
      $hex_mschap2_response = ''
  ) {
      $result = $this->CalculateMsChap2Response($user, $secret, $domain, $hex_mschap_challenge, $hex_mschap2_response);
      
      return ($this->GetMsChap2Response() == mb_strtolower($result));
  }


  function SetEncryptionKey(
      $key,
      $read_config = TRUE
  ) {
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


  function CalculateControlHash(
      $value_to_hash
  ) {
      return mb_strtoupper(md5("CaLcUlAtE".$value_to_hash."cOnTrOlHaSh"));
  }


  function Encrypt(
      $key,
      $value,
      $encryption_key
  ) {
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
   * @since   2013-11-02
   */
  function Decrypt(
      $key,
      $value,
      $encryption_key
  ) {
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


  function SetMaxDelayedFailures(
      $failures
  ) {
      $this->_config_data['max_delayed_failures'] = $failures;
  }


  function GetMaxDelayedFailures()
  {
      return $this->_config_data['max_delayed_failures'];
  }


  function SetMaxDelayedTime(
      $seconds
  ) {
      $this->_config_data['failure_delayed_time'] = $seconds;
  }


  function GetMaxDelayedTime()
  {
      return $this->_config_data['failure_delayed_time'];
  }


  function SetActualVersion(
      $value
  ) {
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
   * @param   int     $ldap_pwd_needed [0|1]
   * @param   boolean $automatically
   * @return  boolean
   *
   *********************************************************************/
  function CreateUser($user_raw,
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
  ) {
      // A user cannot be created with a leading backslash
      $user = str_replace("\\", "", $user_raw);
      $result = FALSE;
      if ('' != trim($user)) {
          if ((intval($ldap_pwd_needed) < 0) && (1 == $synchronized)) {
              $request_ldap_pwd = $this->GetDefaultRequestLdapPwd();
          } else {
              $request_ldap_pwd = intval($ldap_pwd_needed);
          }
          if (intval($prefix_pin_needed) < 0) {
              $request_prefix_pin = $this->GetDefaultRequestPrefixPin();
          } else {
              $request_prefix_pin = intval($prefix_pin_needed);
          }
          if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user)) {
              $result = FALSE; // ERROR: User already exists, or user is not set
              if ('' == $user) {
                  $this->WriteLog("Error: User is not set", FALSE, FALSE, 21, 'User', '');
              } else {
                  $this->WriteLog("Error: User ".$user." already exists", FALSE, FALSE, 22, 'User', $user);
              }
          } else {
              $this->SetUser($user);
              $this->SetUserPrefixPin($request_prefix_pin);
              $this->SetUserRequestLdapPassword($request_ldap_pwd);
              $this->SetUserAlgorithm($algorithm);
              $this->SetUserTokenAlgoSuite($token_algo_suite);

              $the_pin = $pin;
              if ('' == $the_pin) {
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
              
              if (('hotp' == mb_strtolower($algorithm)) || ('yubicootp' == mb_strtolower($algorithm))) {
                  $next_event = ((-1 == $time_interval_or_next_event)?0:$time_interval_or_next_event);
                  $time_interval = 0;
              } else {
                  $next_event = 0;
                  $time_interval = ((-1 == $time_interval_or_next_event)?30:$time_interval_or_next_event);
                  if ("motp" == mb_strtolower($algorithm)) {
                      // $the_seed = (('' == $seed)?substr(md5(date("YmdHis").mt_rand(100000,999999)),0,16):$seed);
                      $time_interval = 10;
                      if ((strlen($the_pin) < 4) || (0 == intval($the_pin))) {
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
              if (('' == $this_email) && (FALSE !== mb_strpos($user, '@'))) {
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
   * Update 2017-11-04
   * @package multiotp
   * @version 5.0.5.6
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
  function CreateUserFromToken($user_raw,
                               $token,
                               $email = '',
                               $sms = '',
                               $pin = '',
                               $prefix_pin_needed = -1,
                               $description = '',
                               $group = ''
  ) {
      // A user cannot be created with a leading backslash
      $user = str_replace("\\", "", $user_raw);

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
              $this->SetUserTokenPrivateId($this->GetTokenPrivateId());
              $this->SetUserTokenNumberOfDigits($this->GetTokenNumberOfDigits());
              $this->SetUserTokenTimeInterval($this->GetTokenTimeInterval());
              $this->SetUserTokenLastEvent($this->GetTokenLastEvent());

              $the_pin = $pin;
              if ('' == $the_pin)
              {
                  $the_pin = mt_rand(1000,9999);
              }
              
              $this_email = trim($email);
              if (('' == $this_email) && (FALSE !== mb_strpos($user, '@')))
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
  function GetUserTokenQrCode(
      $user = '',
      $display_name = '',
      $file_name = 'binary',
      $qrcode_format = ''
  ) {
      $result = FALSE;
      if (!function_exists('ImageCreate')) {
          $this->WriteLog("Error: PHP GD library is not installed", FALSE, FALSE, 39, 'System', '', 3);
          return $result;
      } else {
          $data = $this->GetUserTokenUrlLink($user, $display_name, $qrcode_format);
          if($data) {
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
   * @version 5.0.5.1
   * @date    2017-09-29
   * @since   2013-02-18
   */
  function GetTokenQrCode(
      $token = '',
      $display_name = '',
      $file_name = 'binary',
      $qrcode_format = ''
  ) {
      $result = FALSE;
      if (!function_exists('ImageCreate'))
      {
          $this->WriteLog("Error: PHP GD library is not installed", FALSE, FALSE, 39, 'System', '', 3);
          return $result;
      }
      else
      {
          $data = $this->GetTokenUrlLink($token, $display_name, $qrcode_format);
          if($data)
          {
              $result = $this->qrcode($data, $file_name);
          }
          return $result;
      }
  }


  function GenerateHtmlQrCode(
      $select_user = '',
      $alternate_html_template = '',
      $keep_qrcode_tags = FALSE
  ) {
      $code_width=200;
      $code_height=200;

      if ('' != $select_user) {
          $user = $this->SetUser($select_user);
      } else {
        $user = $this->GetUser();
      }

      $user = encode_utf8_if_needed($user);
      
      $descr = encode_utf8_if_needed($this->GetUserDescription());
      $descr = encode_utf8_if_needed(empty($descr) ? $user : $descr);

      if ('' != trim($alternate_html_template)) {
          $html = $alternate_html_template;
      } else {
        //Get template file
        $file_name = "template";
        $file_to_show = $this->GetTemplatesFolder().$file_name.'.html';
        if(file_exists($file_to_show)) {
          $html = (file_get_contents($file_to_show));
        } else {
          $html = '';
        }
      }

      $html = encode_utf8_if_needed($html);
      
      $qrcode_format = '';
      $regex_size='/motp-qrcode-format=(.*?)\s-/';
      if(preg_match($regex_size, $html, $values)) {
          $qrcode_format = ('xml' == $values[1]) ? 'xml' : '';
      }

      // Keep or clean LDAP information if not used
      // if ($this->IsUserSynchronized() && ('LDAP' == $this->GetUserSynchronizedChannel()) && $this->IsUserRequestLdapPasswordEnabled())
      if ($this->IsUserRequestLdapPasswordEnabled()) {
          $request_ldap_pwd = TRUE;
          $html = preg_replace('/<!--\s*\{\/IfMultiotpUserLdapPwd\}\s*-->/i', '', $html);
          $html = preg_replace('/<!--\s*\{IfMultiotpUserLdapPwd\}\s*-->/i', '', $html);
      } else {
          $request_ldap_pwd = FALSE;
          $html = preg_replace('/<!--\s*\{\/IfMultiotpUserLdapPwd\}\s*-->/i', ' -- {/IfMultiotpUserLdapPwd} -->', $html);
          $html = preg_replace('/<!--\s*\{IfMultiotpUserLdapPwd\}\s*-->/i', '<!-- {/IfMultiotpUserLdapPwd} -- ', $html);
      }

      // Keep or clean pin information if not used
      if ($this->IsUserPrefixPin() && (!$request_ldap_pwd)) {
          $html = preg_replace('/<!--\s*\{\/IfMultiotpUserPin\}\s*-->/i', '', $html);
          $html = preg_replace('/<!--\s*\{IfMultiotpUserPin\}\s*-->/i', '', $html);
      } else {
          $html = preg_replace('/<!--\s*\{\/IfMultiotpUserPin\}\s*-->/i', ' -- {/IfMultiotpUserPin} -->', $html);
          $html = preg_replace('/<!--\s*\{IfMultiotpUserPin\}\s*-->/i', '<!-- {/IfMultiotpUserPin} -- ', $html);
      }
      
      $token_serial = trim($this->GetUserTokenSerialNumber());
      if (('' == $token_serial) || (1 > strlen($token_serial))) {
          $html = preg_replace('/<!--\s*\{\/IfMultiotpUserTokenSerial\}\s*-->/i', ' -- {/IfMultiotpUserTokenSerial} -->', $html);
          $html = preg_replace('/<!--\s*\{IfMultiotpUserTokenSerial\}\s*-->/i', '<!-- {/IfMultiotpUserTokenSerial} -- ', $html);
      } else {
          $html = preg_replace('/<!--\s*\{\/IfMultiotpUserTokenSerial\}\s*-->/i', '', $html);
          $html = preg_replace('/<!--\s*\{IfMultiotpUserTokenSerial\}\s*-->/i', '', $html);
      }

      // Simplify current algorithm info
      $html = preg_replace('/IfMultiotpUserAlgorithm="[BCHIMOPTUY,]*'.mb_strtoupper($this->GetUserAlgorithm()).'[BCHIMOPTUY,]*"}/i', 'IfMultiotpUserAlgorithm="'.mb_strtoupper($this->GetUserAlgorithm()).'"}', $html);

      // Clean other algorithms info
      foreach (explode("\t",$this->GetAlgorithmsList()) as $algorithm_one) {
          if (mb_strtoupper($algorithm_one) != mb_strtoupper($this->GetUserAlgorithm())) {
              $html = preg_replace('/<!--\s*\{\/IfMultiotpUserAlgorithm="[BCHIMOPTUY,]*'.mb_strtoupper($algorithm_one).'[BCHIMOPTUY,]*"\}\s*-->/i', ' -- {/IfMultiotpUserAlgorithm="DELETE"} -->', $html);
              $html = preg_replace('/<!--\s*\{IfMultiotpUserAlgorithm="[BCHIMOPTUY,]*'.mb_strtoupper($algorithm_one).'[BCHIMOPTUY,]*"\}\s*-->/i', '<!-- {IfMultiotpUserAlgorithm="DELETE"} -- ', $html);
          }
      }

      // Check if a specific language exists in the tags
      $specific_language = $this->GetUserLanguage();
      if (false === mb_stripos($html, '{IfMultiotpLanguage="'.$specific_language.'"')) {
        $specific_language = $this->GetLanguage();
        if (false === mb_stripos($html, '{IfMultiotpLanguage="'.$specific_language.'"')) {
          $specific_language = 'en';
        }
      }
      // Clean other languages info
      $html = preg_replace('/<!--\s*\{\/IfMultiotpLanguage="'.$specific_language.'"\}\s*-->/i', '', $html);
      $html = preg_replace('/<!--\s*\{IfMultiotpLanguage="'.$specific_language.'"\}\s*-->/i', '', $html);
      $html = preg_replace('/<!--\s*\{\/IfMultiotpLanguage="[a-z]*"\}\s*-->/i', ' -- {/IfMultiotpLanguage="other"} {ML} -->', $html);
      $html = preg_replace('/<!--\s*\{IfMultiotpLanguage="[a-z]*"\}\s*-->/i', '<!-- {ML} {IfMultiotpLanguage="other"} -- ', $html);

      // Clean language comments
      $html_cleaned = "";
      $html_slice = explode("{ML} -->",$html);
      foreach($html_slice as $one_slice) {
          $comment_pos = mb_strpos($one_slice,'<!-- {ML}');
          if(FALSE !== $comment_pos) {
            $html_cleaned.=substr($one_slice,0,$comment_pos);
          }
      }
      $html_cleaned .= end($html_slice);
      $html = $html_cleaned."\n";

      // Clean comments
      $html_cleaned = "";
      $html_slice = explode("-->",$html);
      foreach($html_slice as $one_slice) {
          $comment_pos = mb_strpos($one_slice,'<!--');
          if(FALSE !== $comment_pos) {
            $html_cleaned.=substr($one_slice,0,$comment_pos);
          }
      }
      $html_cleaned .= end($html_slice);
      $html = $html_cleaned."\n";

      $html = str_replace('{MultiotpUserDescriptionUC}', mb_strtoupper($descr, 'UTF-8'), $html);
      $html = str_replace('{MultiotpUserDescription}', $descr, $html);

      $html = str_replace('{MultiotpUserAccount}', $user, $html);                    
      $html = str_replace('{MultiotpUserPin}', $this->GetUserPin(), $html);
      $html = str_replace('{MultiotpUserAlgorithm}', mb_strtoupper($this->GetUserAlgorithm()), $html);
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
      if(preg_match_all($regex_tag, $html, $matches)) {
          foreach ($matches[0] as $item) {
              if(!empty($item)) {
                  if(preg_match($regex_format, $item, $values)) {
                      $format = $values[1];
                  }
                  $html = str_replace($item, date($format), $html);
              }
          }
      }

      if (!$keep_qrcode_tags) {
          // Smartphone apps qrcode
          $regex_tag='/\{MultiotpQrCodeUrl\s(.*?)\}/';
          if(preg_match_all($regex_tag, $html, $matches)) {
              foreach ($matches[0] as $item) {
                  $url = '';
                  $w = $code_width;
                  $h = $code_height;
                  if(!empty($item)) {
                      if(preg_match($regex_url, $item, $values)) {
                          $url= str_replace('"', '', explode('=', $values[0],2));
                          $url = $url[1];
                      }
                      if(preg_match($regex_w, $item, $values)) {
                          $w = str_replace('"', '', explode('=', $values[0],2));
                          $w = trim(str_replace('}', '', $w[1]));
                      }
                      if(preg_match($regex_h, $item, $values)) {
                          $h = str_replace('"', '', explode('=', $values[0],2));
                          $h = trim(str_replace('}', '', $h[1]));
                      }
                      $html = str_replace($item, "<a id=\"QrCodeUrl\" href=\"".$url."\" target=\"blank\"><img border=\"0\" width=\"".$w."\" height=\"".$h."\" src=\"data:image/png;base64,".base64_encode($this->qrcode($url, 'binary'))."\" /></a>", $html);
                  }
              }
          }
          // User token qrcode
          $regex_tag='/\{MultiotpQrCodeUserToken\s(.*?)\}/';

          if(preg_match_all($regex_tag, $html, $matches)) {
              foreach ($matches[0] as $item) {
                  $url = $this->GetUserTokenUrlLink($user, $descr, $qrcode_format);
                  $w = $code_width;
                  $h = $code_height;
                  if(!empty($item)) {
                      if(preg_match($regex_w, $item, $values)) {
                          $w = str_replace('"', '', explode('=', $values[0],2));
                          $w = trim(str_replace('}', '', $w[1]));
                      }
                      if(preg_match($regex_h, $item, $values)) {
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
   * Update 2017-09-29
   * @package multiotp
   * @version 1.1.0
   * @author SysCo/al
   *
   * @param   string  $user
   * @param   string  $display_name
   * @return  boolean (FALSE) or string
   *
   *********************************************************************/
  function GetUserTokenUrlLink(
      $user = '',
      $display_name = '',
      $qrcode_format = ''
  ) {
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
          switch (mb_strtolower($q_algorithm)) {
              case 'totp':
                  $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.rawurlencode($q_issuer);
                  break;
              case 'hotp':
                  $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&counter='.$q_counter.'&issuer='.rawurlencode($q_issuer);
                  break;
              case 'motp':
                  if ('xml' == $qrcode_format) {
                    $result = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><SSLOTPAuthenticator><mOTPProfile><ProfileName>!ProfileName!</ProfileName><PINType>!PINType!</PINType><PINSecurity>!PINSecurity!</PINSecurity><Secret>!Secret!</Secret><AlgorithmMode>!AlgorithmMode!</AlgorithmMode></mOTPProfile></SSLOTPAuthenticator>";
                    $result = str_replace(chr(13).chr(10),chr(10),$result);
                    $result = str_replace("!ProfileName!",$q_display_name,$result);
                    $result = str_replace("!PINType!","0",$result); // 0=numeric 4 digits, 1=alphanumeric
                    $result = str_replace("!PINSecurity!","0",$result); // 0=keep generating, 1=generate only one OTP
                    $result = str_replace("!Secret!",$q_seed,$result); // Hexadecimal secret
                    $result = str_replace("!AlgorithmMode!","0",$result); // 0=epochTime + secret + pin
                  } else {
                    $result = 'motp://'.rawurlencode($q_issuer).':'.rawurlencode($q_display_name).'?secret='.$q_seed;
                    // $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.rawurlencode($q_issuer);
                  }
                  break;
              case 'token2':
                    $result = 'motp://'.rawurlencode($q_issuer).':'.rawurlencode($q_display_name).'?secret='.$q_seed;
                  break;
              default:
                  // $result = FALSE;
                  $result = 'http://no.qrcode.available/no_qrcode_compatible_client_for_this_algorithm';
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
   * @version 5.0.5.1
   * @date    2019-09-29
   * @since   2014-01-19
   */
  function GetTokenUrlLink(
      $token = '',
      $display_name = '',
      $qrcode_format = ''
  ) {
      $the_token = mb_strtolower($token);
      $result = FALSE;

      if ('' != $the_token) {
          $this->SetToken($the_token);
      }

      if ($this->ReadTokenData()) {
          $the_token      = $this->GetToken();
          $q_algorithm    = $this->GetTokenAlgorithm();
          $q_algo_suite   = $this->GetTokenAlgoSuite();
          $q_period       = $this->GetTokenTimeInterval();
          $q_digits       = $this->GetTokenNumberOfDigits();
          $q_seed         = $this->GetTokenSeed();
          $q_counter      = $this->GetTokenLastEvent() + 1;
          $q_display_name = (('' != $display_name)?$display_name:$the_token);
          $q_issuer       = $this->GetTokenIssuer();

          switch (mb_strtolower($q_algorithm))
          {
              case 'totp':
                  $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.rawurlencode($q_issuer);
                  break;
              case 'hotp':
                  $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&counter='.$q_counter.'&issuer='.rawurlencode($q_issuer);
                  break;
              case 'motp':
                  if ('xml' == $qrcode_format) {
                    $result = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><SSLOTPAuthenticator><mOTPProfile><ProfileName>!ProfileName!</ProfileName><PINType>!PINType!</PINType><PINSecurity>!PINSecurity!</PINSecurity><Secret>!Secret!</Secret><AlgorithmMode>!AlgorithmMode!</AlgorithmMode></mOTPProfile></SSLOTPAuthenticator>";
                    $result = str_replace(chr(13).chr(10),chr(10),$result);
                    $result = str_replace("!ProfileName!",$q_display_name,$result);
                    $result = str_replace("!PINType!","0",$result); // 0=numeric 4 digits, 1=alphanumeric
                    $result = str_replace("!PINSecurity!","0",$result); // 0=keep generating, 1=generate only one OTP
                    $result = str_replace("!Secret!",$q_seed,$result); // Hexadecimal secret
                    $result = str_replace("!AlgorithmMode!","0",$result); // 0=epochTime + secret + pin
                  } else {
                    $result = 'motp://'.rawurlencode($q_issuer).':'.rawurlencode($q_display_name).'?secret='.$q_seed;
                    // $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?secret='.base32_encode(hex2bin($q_seed)).'&digits='.$q_digits.'&period='.$q_period.'&issuer='.rawurlencode($q_issuer);
                  }
                  break;
              case 'token2':
                    $result = 'motp://'.rawurlencode($q_issuer).':'.rawurlencode($q_display_name).'?secret='.$q_seed;
                  break;
              default:
                  // $result = FALSE;
                  $result = 'http://no.qrcode.available/no_qrcode_compatible_client_for_this_algorithm';
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
   * Update 2017-11-04
   * @package multiotp
   * @version 5.0.5.6
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
   * @param   string  $preferred_language
   * @param   string  $dialin_ip_address
   * @return  boolean
   *
   *********************************************************************/
  function FastCreateUser($user_raw,
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
                          $ldap_pwd_needed = -1,
                          $language = '',
                          $dialin_ip_address = ''
  ) {
      // A user cannot be created with a leading backslash
      $user = str_replace("\\", "", $user_raw);

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
              $this->SetUserLanguage($language);
              $this->SetUserGroup(('*DEFAULT*' == $group) ? $this->GetDefaultUserGroup() : $group);
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

              if ("totp" == mb_strtolower($algorithm))
              {
                  $time_interval = 30;
              }
              elseif ("motp" == mb_strtolower($algorithm))
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

              $this->SetUserDialinIpAddress($dialin_ip_address);

              $result = $this->WriteUserData($automatically); // WriteUserData write in the log file
          }
      }
      return $result;
  }


  function SetUser(
      $user,
      $auto_read_data = true
  ) {
      $result = TRUE;
      $user_encoded = $user;
      if ('' != $user_encoded) {
          if ($user_encoded != $this->GetUser()) {
              $this->ResetUserArray();
              $this->_user = $user_encoded;
              if (!$this->IsCaseSensitiveUsers()) {
                  $this->_user = mb_strtolower($this->_user);
              }
              if ($auto_read_data) {
                  $result = $this->ReadUserData('', false); // First parameter empty, otherwise it will loop with SetUser !
              }
          }
      }
      else
      {
          $this->ResetUserArray();
      }
      return ($result ? $user_encoded : FALSE);
  }


  function RenameCurrentUser(
      $new_user,
      $no_error_info = FALSE
  ) {
      if ($this->IsCaseSensitiveUsers()) {
          $the_new_user = $new_user;
      } else {
          $the_new_user = mb_strtolower($new_user);
      }
      $result = FALSE;
      if ($this->CheckUserExists($the_new_user)) // Check if the new user already exists
      {
          $this->WriteLog("Error: Unable to rename the current user ".$this->GetUser()." to $the_new_user because $the_new_user already exists", FALSE, FALSE, 22, 'User');
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
                          $esc_actual = escape_mysql_string($this->GetUser());
                          $esc_new    = escape_mysql_string($the_new_user);
                          if ($this->OpenMysqlDatabase())
                          {
                              if ('' != $this->_config_data['sql_users_table'])
                              {
                                  $sQuery = "UPDATE `".$this->_config_data['sql_users_table']."` SET user='".mb_strtolower($esc_new)."' WHERE `user`='".$esc_actual."'";
                                  
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
                                      $this->WriteLog("Info: User ".$this->GetUser()." successfully renamed to $the_new_user", FALSE, FALSE, 11, 'User');
                                      $result = TRUE;
                                  }
                              }
                          }
                          break;
                      case 'pgsql':
                          $esc_actual = pg_escape_string($this->GetUser());
                          $esc_new    = pg_escape_string($the_new_user);
                          if ($this->OpenPGSQLDatabase())
                          {
                              if ('' != $this->_config_data['sql_users_table'])
                              {
                                  $sQuery = "UPDATE \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" SET \"user\" = '".mb_strtolower($esc_new)."' WHERE \"user\" = '".$esc_actual."'";
                                  
                                  if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery)))
                                  {
                                      if (!$no_error_info)
                                      {
                                          $this->WriteLog("Error: Could not rename the user ".$this->GetUser().": ".pg_last_error(), FALSE, FALSE, 28, 'User');
                                      }
                                  }
                                  else
                                  {
                                      $num_rows = pg_affected_rows($rResult);
                                  }
                                  
                                  if (0 == $num_rows)
                                  {
                                      $this->WriteLog("Error: Could not rename the user ".$this->GetUser().". User does not exist", FALSE, FALSE, 21, 'User');
                                  }
                                  else
                                  {
                                      $this->WriteLog("Info: User ".$this->GetUser()." successfully renamed to $the_new_user", FALSE, FALSE, 11, 'User');
                                      $result = TRUE;
                                  }
                              }
                          }
                          break;
                      case 'files':
                      default:
                          $old_user_filename = $this->EncodeFileId($this->GetUser(), $this->IsCaseSensitiveUsers()).'.db';
                          if (!file_exists($this->GetUsersFolder().$old_user_filename)) {
                              $old_user_filename = $this->EncodeFileId($this->GetUser(), $this->IsCaseSensitiveUsers(), TRUE).'.db';
                          }
                          if (file_exists($this->GetUsersFolder().$old_user_filename)) {
                              $new_user_filename = $this->EncodeFileId($the_new_user, $this->IsCaseSensitiveUsers()).'.db';
                              rename($this->GetUsersFolder().$old_user_filename, $this->GetUsersFolder().$new_user_filename);
                              $result = TRUE;
                          }
                          break;
                  }
              }
          }
          if ($result)
          {
              $this->_user = mb_strtolower($the_new_user);
          }
      }
      return $result;
  }


  function GetUser()
  {
      return $this->_user;
  }


  // Check if user exists (locally only)
  function CheckUserExists(
      $user = '',
      $no_server_check = FALSE,
      $no_error = FALSE
  ) {
      $check_user = ('' != $user)?$user:$this->GetUser();
      $result = FALSE;

      if ('' != trim($check_user)) {
          $server_result = -1;
          if ((!$no_server_check) && ('' != $this->GetServerUrl())) {
              $server_result = $this->CheckUserExistsOnServer($check_user);
              if (22 == $server_result) {
                  // We return only if the user exists, so we check also the local one
                  $result = TRUE;
                  return $result;
              }
          }

          if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType())) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      if ($this->OpenMysqlDatabase()) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '{$check_user}'";
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              } else {
                                  $num_rows = $rResult->num_rows;
                              }
                          }
                          elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              $num_rows = mysql_num_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error) {
                                  $this->WriteLog("Error: User ".$check_user." does not exist", FALSE, FALSE, 21, 'System', '');
                              }
                              $result = FALSE;
                          } else {
                              $result = TRUE;
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE \"user\" = '{$check_user}';";
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              $num_rows = pg_num_rows($rResult);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error) {
                                  $this->WriteLog("Error: User ".$check_user." does not exist", FALSE, FALSE, 21, 'System', '');
                              }
                              $result = FALSE;
                          } else {
                              $result = TRUE;
                          }
                      }
                      break;
                  case 'files':
                  default:
                      $user_filename = $this->EncodeFileId($check_user, $this->IsCaseSensitiveUsers()).'.db';
                      if (!file_exists($this->GetUsersFolder().$user_filename)) {
                          $user_filename = $this->EncodeFileId($check_user, $this->IsCaseSensitiveUsers(), TRUE).'.db';
                      }
                      $result = file_exists($this->GetUsersFolder().$user_filename);
                      break;
              }
          }
      }
      return $result;
  }


  function LockUser(
      $user = ''
  ) {
      $result = FALSE;
      if ('' != $user) {
          $this->SetUser($user, false);
      }
      if ($this->ReadUserData('', FALSE, TRUE)) {
          // LOCALLY ONLY, not on the server if any
          $this->SetUserLocked(1);
          if ($this->GetVerboseFlag()) {
              $this->WriteLog("Info: *User ".$this->GetUser()." successfully locked", FALSE, FALSE, 19, 'User');
          }
          $this->WriteUserData();
          $result = TRUE;
      }
      return $result;
  }


  function UnlockUser(
      $user = ''
  ) {
      $result = FALSE;
      if ('' != $user) {
          $this->SetUser($user, false);
      }
      if ($this->ReadUserData('', FALSE, TRUE)) {
          // LOCALLY ONLY, not on the server if any
          $this->SetUserErrorCounter(0);
          $this->SetUserLocked(0);
          if ($this->GetVerboseFlag()) {
              $this->WriteLog("Info: *User ".$this->GetUser()." successfully unlocked", FALSE, FALSE, 19, 'User');
          }
          $this->WriteUserData();
          $result = TRUE;
      }
      return $result;
  }


  function DeleteUser(
      $user = '',
      $no_error_info = FALSE
  ) {
      if ('' != $user) {
          $this->SetUser($user, false);
      }
      
      $result = FALSE;
      
      // First, we delete the user file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {

          $user_filename = $this->EncodeFileId($this->GetUser(), $this->IsCaseSensitiveUsers()).'.db';
          if (!file_exists($this->GetUsersFolder().$user_filename)) {
              $user_filename = $this->EncodeFileId($this->GetUser(), $this->IsCaseSensitiveUsers(), TRUE).'.db';
          }
          if (!file_exists($this->GetUsersFolder().$user_filename)) {
              if (!$no_error_info) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Error: *Unable to delete user ".$this->GetUser().", the users database file ".$this->GetUsersFolder().$user_filename." does not exist", FALSE, FALSE, 21, 'User');
                  } else {
                      $this->WriteLog("Error: Unable to delete user ".$this->GetUser(), FALSE, FALSE, 29, 'User');
                  }
              }
          } else {
              $result = unlink($this->GetUsersFolder().$user_filename);
              if ($result) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *User ".$this->GetUser()." successfully deleted", FALSE, FALSE, 12, 'User');
                  }
              } elseif (!$this->GetMigrationFromFile()) {
                  if (!$no_error_info) {
                      $this->WriteLog("Error: Unable to delete user ".$this->GetUser(), FALSE, FALSE, 28, 'User');
                  }
              }
          }
      }

      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_users_table']) {
                          $sQuery  = "DELETE FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '".$this->_user."'";
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  if (!$no_error_info) {
                                      $this->WriteLog("Error: Could not delete user ".$this->GetUser().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'User');
                                  }
                              } else {
                                  $num_rows = $this->_mysqli->affected_rows;
                              }
                          } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete user ".$this->GetUser().": ".mysql_error(), FALSE, FALSE, 28, 'User');
                              }
                          } else {
                              $num_rows = mysql_affected_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete user ".$this->GetUser().". User does not exist", FALSE, FALSE, 21, 'User');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Info: *User ".$this->GetUser()." successfully deleted", FALSE, FALSE, 12, 'User');
                              }
                              $result = TRUE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_users_table']) {
                          $sQuery  = "DELETE FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE \"user\" = '".$this->_user."'";
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete user ".$this->GetUser().": ".pg_last_error(), FALSE, FALSE, 28, 'User');
                              }
                          } else {
                              $num_rows = pg_affected_rows($rResult);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete user ".$this->GetUser().". User does not exist", FALSE, FALSE, 21, 'User');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
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
      
      if ($result) {
          foreach(explode("\t", $this->GetTokensList()) as $one_token) {
              if ($this->RemoveTokenAttributedUsers($one_token, $this->GetUser())) {
                  $this->WriteTokenData();
              }
          }
          $this->TouchFolder('data',
                             'User',
                             $this->GetUser(),
                             TRUE,
                             "DeleteUser");
      }
      
      return $result;
  }


  function GetUsersCount($no_cache = FALSE)
  {
      if (($this->IsCacheData()) && (intval($this->ReadCacheValue('users_count')) >= 0) && (!$no_cache)) {
          $users_count = intval($this->ReadCacheValue('users_count'));
      } else {
          $users_count = 0;
          switch ($this->GetBackendType()) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase())
                  {
                      $sQuery  = "SELECT COUNT(user) AS counter FROM `".$this->_config_data['sql_users_table']."` ";
                      if (is_object($this->_mysqli)) {
                          if (!($result = $this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              if ($aRow = $result->fetch_assoc()) {
                                  $users_count = $aRow['counter'];
                              }
                          }
                      } else {
                          if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              if ($aRow = mysql_fetch_assoc($rResult)) {
                                  $users_count = $aRow['counter'];
                              }
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase())
                  {
                      $sQuery  = "SELECT COUNT(\"user\") AS \"counter\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" ";
                      if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                      } else {
                          if ($aRow = pg_fetch_assoc($rResult)) {
                              $users_count = $aRow['counter'];
                          }
                      }
                  }
                  break;
              case 'files':
              default:
                  if ($users_handle = @opendir($this->GetUsersFolder())) {
                      while ($file = readdir($users_handle)) {
                          if ((substr($file, -3) == ".db") && ($file != '.db')) {
                              $users_count++;
                          }
                      }
                      closedir($users_handle);
                  }
          }
          if (($this->IsCacheData()) && ($users_count >= 0)) {
              $this->WriteCacheValue('users_count', $users_count);
              $this->WriteCacheData();
          }
      }
      return $users_count;
  }


  function ReadUserDataArray(
      $user = '',
      $create = false,
      $no_server_check = false
  ) {
      $array_user = ('' != $user)?$user:$this->GetUser();
      $result = false;

      // We reset all values (we know the key based on the schema)
      $temp_user_array = $this->ResetTempUserArray();

      // First, we read the user file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {

          $user_filename = $this->EncodeFileId($array_user, $this->IsCaseSensitiveUsers()).'.db';
          if (!file_exists($this->GetUsersFolder().$user_filename)) {
              $user_filename = $this->EncodeFileId($array_user, $this->IsCaseSensitiveUsers(), TRUE).'.db';
          }

          if (!file_exists($this->GetUsersFolder().$user_filename)) {
              if (!$create) {
                  $this->WriteLog("Error: database file ".$this->GetUsersFolder().$user_filename." for user ".$array_user." does not exist", FALSE, FALSE, 21, 'System', '');
              }
          } else {
              $temp_user_array['multi_account'] = 0;
              $temp_user_array['time_interval'] = 0;
              
              if ($file_handler = @fopen($this->GetUsersFolder().$user_filename, "rt")) {
                  $first_line = trim(fgets($file_handler));
                  $v3 = (false !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format-v3"));
                  
                  // First version format support
                  if (false === mb_strpos(mb_strtolower($first_line),"multiotp-database-format")) {
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
                              if ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$line_array[0].'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt()))) {
                                  $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                              }
                          }
                          if ('' != trim($line_array[0])) {
                              $temp_user_array[mb_strtolower($line_array[0])] = $line_array[1];
                          }
                      }
                  }
                  fclose($file_handler);
                  $result = true;
              }
              if ('' != $temp_user_array['encryption_hash']) {
                  if ($temp_user_array['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                      $temp_user_array['encryption_hash'] = "ERROR";
                      $this->WriteLog("Error: the user information encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                      $result = false;
                  }
              }
          }
      }


      // And now, we override the values if another backend type is defined
      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_users_table']) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '".$array_user."'";
                          $aRow = NULL;
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 40, 'System', '', 3);
                                  $result = false;
                              } else {
                                  $aRow = $rResult->fetch_assoc();
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                                  $result = false;
                              } else {
                                  $aRow = mysql_fetch_assoc($rResult);
                              }
                          }

                          if (NULL != $aRow) {
                              $result = false;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['users']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'user')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $temp_user_array[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $temp_user_array[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the users table schema", FALSE, FALSE, 42, 'System', '', 3);
                                  }
                                  $result = true;
                              }
                              if(0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for user ".$array_user." does not exist", FALSE, FALSE, 21, 'System', '');
                              }
                          }
                      }
                      if ('' != $temp_user_array['encryption_hash']) {
                          if ($temp_user_array['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $temp_user_array['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the users mysql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                              $result = false;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_users_table']) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE \"user\" = '".$array_user."'";
                          $aRow = NULL;
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                              $result = false;
                          }
                          else {
                              $aRow = pg_fetch_assoc($rResult);
                          }

                          if (NULL != $aRow) {
                              $result = false;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['users']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'user')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $temp_user_array[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $temp_user_array[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the users table schema", FALSE, FALSE, 42, 'System', '', 3);
                                  }
                                  $result = true;
                              }
                              if(0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for user ".$array_user." does not exist", FALSE, FALSE, 21, 'System', '');
                              }
                          }
                      }
                      if ('' != $temp_user_array['encryption_hash']) {
                          if ($temp_user_array['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $temp_user_array['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the users pgsql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
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
                      if ('encryption_hash' != mb_strtolower($line_array[0]))
                      {
                          $temp_user_array[mb_strtolower($line_array[0])] = $line_array[1];
                      }
                  }
              }
              $result = true;
          }
      }

      $now_epoch = time();
      $temp_user_array['delayed_account'] = (($temp_user_array['error_counter'] >= $this->GetMaxDelayedFailures()) && ($now_epoch < ((isset($temp_user_array['last_error']) ? $temp_user_array['last_error'] : 0) + $this->GetMaxDelayedTime())));
      $temp_user_array['delayed_time'] = 0;
      $temp_user_array['delayed_finished'] = 0;
      if ($temp_user_array['delayed_account']) {
          $temp_user_array['delayed_time'] = ((isset($temp_user_array['last_error']) ? $temp_user_array['last_error'] : 0) + $this->GetMaxDelayedTime()) - $now_epoch;
          $temp_user_array['delayed_finished'] = ((isset($temp_user_array['last_error']) ? $temp_user_array['last_error'] : 0) + $this->GetMaxDelayedTime());
      }
      if (false !== $result) {
          return $temp_user_array;
      } else {
          return false;
      }
  }


  function ReadUserData(
      $user = '',
      $create = FALSE,
      $no_server_check = FALSE
  ) {
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
      $write_user_data_array    = false,
      $update_last_change_param = true
  ) {
      if (is_array($write_user_data_array)) {
        if (!isset($write_user_data_array['automatically'])) {
          $write_user_data_array['automatically'] = false;
        }
        if (!isset($write_user_data_array['update_last_change'])) {
          $write_user_data_array['update_last_change'] = true;
        }
      } else {
        $temp_array = array();
        $temp_array['automatically']      = $write_user_data_array;
        $temp_array['update_last_change'] = $update_last_change_param;
        $write_user_data_array = $temp_array;
      }

      if ('' == trim($this->GetUser())) {
          $result = false;
      } else {
          $result = $this->WriteData(array_merge(array('item'               => 'User',
                                                       'table'              => 'users',
                                                       'folder'             => $this->GetUsersFolder(),
                                                       'data_array'         => $this->_user_data,
                                                       'force_file'         => false,
                                                       'id_field'           => 'user',
                                                       'id_value'           => $this->GetUser(),
                                                       'encode_file_id'     => $this->IsEncodeFileId(),
                                                       'id_case_sensitive'  => $this->IsCaseSensitiveUsers()
                                                      ), $write_user_data_array));

      }
      return $result;
  }


  function GetUsersList()
  {
      return $this->GetList('user', 'sql_users_table', $this->GetUsersFolder());
  }


  /**
   * @brief   Get the list of delayed users
   *
   * Based on the request of Frank van der Aa, Vanboxtel BV (NL)
   *   The proposed implementation of Frank was using the cache feature, but
   *   this is not possible as the delayed information is based on the
   *   error_counter and on the last_error date, and the delayed information
   *   is not available in the database. We are not returning locked users.
   *
   * @param   int      $limit   Maximum number of users in thre returned list
   *
   * @return  string   List of delayed users
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.3.6
   * @date    2017-02-13
   * @since   2017-02-09
   */
  function GetDelayedUsersList(
      $limit = 0
  ) {
      $delayed_users_list = '';
      $delayed_users_count = 0;
      if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType())) {
          switch ($this->GetBackendType()) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      $sQuery  = "SELECT user, error_counter, last_error FROM `".$this->_config_data['sql_users_table']."` WHERE (`locked` = 0) ORDER BY user ASC";
                      if ($limit > 0) {
                          $sQuery.= " LIMIT 0,".$limit;
                      }
                      if (is_object($this->_mysqli)) {
                          if (!($result = $this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              while ($aRow = $result->fetch_assoc()) {
                                  if ('' != $aRow['user']) {
                                      $now_epoch = time();
                                      if (($aRow['error_counter'] >= $this->GetMaxDelayedFailures()) && ($now_epoch < ((isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime()))) {
                                          $delayed_time = ((isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime()) - $now_epoch;
                                          $delayed_finished = (isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime();
                                          $delayed_users_list.= (('' != $delayed_users_list)?"\t":'').$aRow['user'].'|'.$delayed_finished;
                                          $delayed_users_count++;
                                      }
                                  }
                              }
                          }
                      } else {
                          if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              while ($aRow = mysql_fetch_assoc($rResult)) {
                                  if ('' != $aRow['user']) {
                                      $now_epoch = time();
                                      if (($aRow['error_counter'] >= $this->GetMaxDelayedFailures()) && ($now_epoch < ((isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime()))) {
                                          $delayed_time = ((isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime()) - $now_epoch;
                                          $delayed_finished = (isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime();
                                          $delayed_users_list.= (('' != $delayed_users_list)?"\t":'').$aRow['user'].'|'.$delayed_finished;
                                          $delayed_users_count++;
                                      }
                                  }
                              }                         
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      $sQuery  = "SELECT \"user\", \"error_counter\", \"last_error\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE (\"locked\" = 0) ORDER BY \"user\" ASC";
                      if ($limit > 0) {
                          $sQuery.= " LIMIT 0,".$limit;
                      }
                      if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                      } else {
                          while ($aRow = pg_fetch_assoc($rResult)) {
                              if ('' != $aRow['user']) {
                                  $now_epoch = time();
                                  if (($aRow['error_counter'] >= $this->GetMaxDelayedFailures()) && ($now_epoch < ((isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime()))) {
                                      $delayed_time = ((isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime()) - $now_epoch;
                                      $delayed_finished = (isset($aRow['last_error']) ? $aRow['last_error'] : 0) + $this->GetMaxDelayedTime();
                                      $delayed_users_list.= (('' != $delayed_users_list)?"\t":'').$aRow['user'].'|'.$delayed_finished;
                                      $delayed_users_count++;
                                  }
                              }
                          }                         
                      }
                  }
                  break;
              case 'files':
              default:
                  $users_count = 0;
                  if ($users_handle = @opendir($this->GetUsersFolder()))
                  {
                      while ($file = readdir($users_handle))
                      {
                          $error_counter = 0;
                          $last_error = 0;
                          $desactivated = FALSE;
                          $locked = FALSE;
                          if ((substr($file, -3) == ".db") && ($file != '.db'))
                          {
                              $current_user = $this->DecodeFileId(substr($file,0,-3));
                              if ($file_handler = @fopen($this->GetUsersFolder().$file, "rt")) {
                                  $first_line = trim(fgets($file_handler));
                                  $v3 = (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format-v3"));
                                  if (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format")) // Format V3
                                  {
                                      while (!feof($file_handler))
                                      {
                                          $line = trim(fgets($file_handler));
                                          $line_array = explode("=",$line,2);
                                          if ($v3) {
                                              // v3 format, only tags followed by := instead of = are encrypted
                                              if (":" == substr($line_array[0], -1))
                                              {
                                                  $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                  $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                              }
                                          } else {
                                              // v2 format, only defined tags are encrypted
                                              if ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$line_array[0].'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt())))
                                              {
                                                  $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                              }
                                          }
                                          if ('error_counter' == trim($line_array[0]))
                                          {
                                              $error_counter = $line_array[1];
                                          }
                                          if ('last_error' == trim($line_array[0]))
                                          {
                                              $last_error = $line_array[1];
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
                                  
                                  $now_epoch = time();
                                  if (($error_counter >= $this->GetMaxDelayedFailures()) && ($now_epoch < ($last_error + $this->GetMaxDelayedTime()))) {
                                      $delayed_time = ($last_error + $this->GetMaxDelayedTime()) - $now_epoch;
                                      $delayed_finished = $last_error + $this->GetMaxDelayedTime();
                                      if (!$locked)
                                      {
                                          $delayed_users_list.= (('' != $delayed_users_list)?"\t":'').$current_user.'|'.$delayed_finished;
                                          $delayed_users_count++;
                                      }
                                  }
                              }
                          }
                          if (($limit > 0) && ($delayed_users_count >= $limit)) {
                              break;
                          }
                      }
                      closedir($users_handle);
                  }
          }
      }
      return $delayed_users_list;
  }


  function GetLockedUsersList(
      $limit = 0
  ) {
      if (($this->IsCacheData()) && (($this->ReadCacheValue('locked_users_list')) != '-1')) {
          $locked_users_list = ($this->ReadCacheValue('locked_users_list'));
      } else {
          $locked_users_list = '';
          if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType())) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      if ($this->OpenMysqlDatabase()) {
                          $sQuery  = "SELECT user FROM `".$this->_config_data['sql_users_table']."` WHERE (`locked` = 1) ORDER BY user ASC";
                          if ($limit > 0) {
                              $sQuery.= " LIMIT 0,".$limit;
                          }
                          if (is_object($this->_mysqli)) {
                              if (!($result = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  while ($aRow = $result->fetch_assoc()) {
                                      if ('' != $aRow['user']) {
                                          $locked_users_list.= (('' != $locked_users_list)?"\t":'').$aRow['user'];
                                      }
                                  }
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                              } else {
                                  while ($aRow = mysql_fetch_assoc($rResult)) {
                                      if ('' != $aRow['user']) {
                                          $locked_users_list.= (('' != $locked_users_list)?"\t":'').$aRow['user'];
                                      }
                                  }                         
                              }
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          $sQuery  = "SELECT \"user\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE (\"locked\" = 1) ORDER BY \"user\" ASC";
                          if ($limit > 0) {
                              $sQuery.= " LIMIT 0,".$limit;
                          }
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              while ($aRow = pg_fetch_assoc($rResult)) {
                                  if ('' != $aRow['user']) {
                                      $locked_users_list.= (('' != $locked_users_list)?"\t":'').$aRow['user'];
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
                                  $current_user = $this->DecodeFileId(substr($file,0,-3));
                                  if ($file_handler = @fopen($this->GetUsersFolder().$file, "rt")) {
                                      $first_line = trim(fgets($file_handler));
                                      $v3 = (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format-v3"));
                                      if (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format")) // Format V3
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
                                                  if ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$line_array[0].'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt())))
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
                              }
                              if (($limit > 0) && ($locked_users_count >= $limit)) {
                                  break;
                              }
                          }
                          closedir($users_handle);
                          
                          if (($limit <= 0) && ($this->IsCacheData())) {
                              $this->WriteCacheValue('locked_users_list', $locked_users_list);
                              if ($locked_users_count >= 0) {
                                  $this->WriteCacheValue('locked_users_count', $locked_users_count);
                              }
                              if ($active_users_count >= 0) {
                                  $this->WriteCacheValue('active_users_count', $active_users_count);
                              }
                              if ($users_count >= 0) {
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
                          if (is_object($this->_mysqli)) {
                              if (!($result = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  if ($aRow = $result->fetch_assoc()) {
                                      $locked_users_count = $aRow['counter'];
                                  }
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                              } else {
                                  if ($aRow = mysql_fetch_assoc($rResult)) {
                                      $locked_users_count = $aRow['counter'];
                                  }
                              }
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase())
                      {
                          $sQuery  = "SELECT COUNT(\"user\") AS \"counter\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE (\"locked\" = 1)";
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              if ($aRow = pg_fetch_assoc($rResult)) {
                                  $locked_users_count = $aRow['counter'];
                              }
                          }
                      }
                      break;
                  case 'files':
                  default:
                      $active_users_count = 0;
                      $users_count = 0;
                      if ($users_handle = @opendir($this->GetUsersFolder())) {
                          while ($file = readdir($users_handle)) {
                              $locked = FALSE;
                              $desactivated = FALSE;
                              if ((substr($file, -3) == ".db") && ($file != '.db')) {
                                  $current_user = $this->DecodeFileId(substr($file,0,-3));
                                  if ($file_handler = @fopen($this->GetUsersFolder().$file, "rt")) {
                                      $first_line = trim(fgets($file_handler));
                                      $v3 = (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format-v3"));
                                      if (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format")) { // Format V3
                                          while (!feof($file_handler)) {
                                              $line = trim(fgets($file_handler));
                                              $line_array = explode("=",$line,2);
                                              if ($v3) { // v3 format, only tags followed by := instead of = are encrypted
                                                  if (":" == substr($line_array[0], -1)) {
                                                      $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                      $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                  }
                                              } else { // v2 format, only defined tags are encrypted
                                                  if ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$line_array[0].'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt()))) {
                                                      $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                  }
                                              }
                                              if ('locked' == trim($line_array[0])) {
                                                  if (1 == (isset($line_array[1])?$line_array[1]:0)) {
                                                      $locked = TRUE;
                                                  }
                                              }
                                              if ('desactivated' == trim($line_array[0])) {
                                                  if (1 == (isset($line_array[1])?$line_array[1]:0)) {
                                                      $desactivated = TRUE;
                                                  }
                                              }
                                          }
                                      }
                                      fclose($file_handler);
                                      $users_count++;
                                      
                                      if ($locked) {
                                          $locked_users_count++;
                                      }
                                      if (!$desactivated) {
                                          $active_users_count++;
                                      }
                                  }
                              }
                          }
                          closedir($users_handle);
                      }
              }
          }
          if ($this->IsCacheData()) {
              if ($locked_users_count >= 0) {
                  $this->WriteCacheValue('locked_users_count', $locked_users_count);
              }
              if ($active_users_count >= 0) {
                  $this->WriteCacheValue('active_users_count', $active_users_count);
              }
              if ($users_count >= 0) {
                  $this->WriteCacheValue('users_count', $users_count);
              }
              $this->WriteCacheData();                
          }
      }
      return $locked_users_count;
  }


  function GetActiveUsersList(
      $limit = 0
  ) {
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
                              $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
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
                      } else {
                          if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              while ($aRow = mysql_fetch_assoc($rResult)) {
                                  if ('' != $aRow['user']) {
                                      $list.= (('' != $list)?"\t":'').$aRow['user'];
                                  }
                              }                         
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      $sQuery  = "SELECT \"user\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE (\"desactivated\" = 0) ORDER BY \"user\" ASC";
                      if ($limit > 0) {
                          $sQuery.= " LIMIT 0,".$limit;
                      }
                      if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                      } else {
                          while ($aRow = pg_fetch_assoc($rResult)) {
                              if ('' != $aRow['user']) {
                                  $list.= (('' != $list)?"\t":'').$aRow['user'];
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
                  if ($users_handle = @opendir($this->GetUsersFolder())) {
                      while ($file = readdir($users_handle)) {
                          $desactivated = FALSE;
                          $locked = FALSE;
                          if ((substr($file, -3) == ".db") && ($file != '.db')) {
                              $current_user = $this->DecodeFileId(substr($file,0,-3));
                              if ($file_handler = @fopen($this->GetUsersFolder().$file, "rt")) {
                                  $first_line = trim(fgets($file_handler));
                                  $v3 = (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format-v3"));
                                  if (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format")) { // Format V3
                                      while (!feof($file_handler)) {
                                          $line = trim(fgets($file_handler));
                                          $line_array = explode("=",$line,2);
                                          if ($v3) { // v3 format, only tags followed by := instead of = are encrypted
                                              if (":" == substr($line_array[0], -1)) {
                                                  $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                  $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                              }
                                          } else { // v2 format, only defined tags are encrypted
                                              if ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$line_array[0].'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt()))) {
                                                  $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                              }
                                          }
                                          if ('desactivated' == trim($line_array[0])) {
                                              if (1 == (isset($line_array[1])?$line_array[1]:0)) {
                                                  $desactivated = TRUE;
                                              }
                                          }
                                          if ('locked' == trim($line_array[0])) {
                                              if (1 == (isset($line_array[1])?$line_array[1]:0)) {
                                                  $locked = TRUE;
                                              }
                                          }
                                      }
                                  }
                                  fclose($file_handler);
                                  $users_count++;
                                  
                                  if (!$desactivated) {
                                      $list.= (('' != $list)?"\t":'').$current_user;
                                      $active_users_count++;
                                  }
                                  if ($locked) {
                                      $locked_users_count++;
                                  }
                              }
                          }
                          if (($limit > 0) && (active_users_count >= $limit)) {
                              break;
                          }
                      }
                      closedir($users_handle);
                      
                      if (($limit <= 0) && ($this->IsCacheData())) {
                          if ($locked_users_count >= 0) {
                              $this->WriteCacheValue('locked_users_count', $locked_users_count);
                          }
                          if ($active_users_count >= 0) {
                              $this->WriteCacheValue('active_users_count', $active_users_count);
                          }
                          if ($users_count >= 0) {
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
      if (($this->IsCacheData()) && (intval($this->ReadCacheValue('active_users_count')) >= 0)) {
          $active_users_count = intval($this->ReadCacheValue('active_users_count'));
      } else {
          $active_users_count = 0;
          $locked_users_count = -1;
          if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType())) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      if ($this->OpenMysqlDatabase()) {
                          $sQuery  = "SELECT COUNT(user) AS counter FROM `".$this->_config_data['sql_users_table']."` WHERE (`desactivated` = 0)";
                          if (is_object($this->_mysqli)) {
                              if (!($result = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  if ($aRow = $result->fetch_assoc()) {
                                      $active_users_count = $aRow['counter'];
                                  }
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                              } else {
                                  if ($aRow = mysql_fetch_assoc($rResult)) {
                                      $active_users_count = $aRow['counter'];
                                  }
                              }
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          $sQuery  = "SELECT COUNT(\"user\") AS \"counter\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_users_table']."\" WHERE (\"desactivated\" = 0)";
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              if ($aRow = pg_fetch_assoc($rResult)) {
                                  $active_users_count = $aRow['counter'];
                              }
                          }
                      }
                      break;
                  case 'files':
                  default:
                      $locked_users_count = 0;
                      $users_count = 0;
                      if ($users_handle = @opendir($this->GetUsersFolder())) {
                          while ($file = readdir($users_handle)) {
                              $desactivated = FALSE;
                              $locked = FALSE;
                              if ((substr($file, -3) == ".db") && ($file != '.db')) {
                                  $current_user = $this->DecodeFileId(substr($file,0,-3));
                                  if ($file_handler = @fopen($this->GetUsersFolder().$file, "rt")) {
                                      $first_line = trim(fgets($file_handler));
                                      $v3 = (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format-v3"));
                                      if (FALSE !== mb_strpos(mb_strtolower($first_line),"multiotp-database-format")) {
                                          // Format V3
                                          while (!feof($file_handler)) {
                                              $line = trim(fgets($file_handler));
                                              $line_array = explode("=",$line,2);
                                              if ($v3) {
                                                  // v3 format, only tags followed by := instead of = are encrypted
                                                  if (":" == substr($line_array[0], -1)) {
                                                      $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                                      $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                  }
                                              } else {
                                                  // v2 format, only defined tags are encrypted
                                                  if ((FALSE !== mb_strpos(mb_strtolower($this->GetAttributesToEncrypt()), mb_strtolower('*'.$line_array[0].'*'))) || ("*all*" == mb_strtolower($this->GetAttributesToEncrypt()))) {
                                                      $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                                                  }
                                              }
                                              if ('desactivated' == trim($line_array[0])) {
                                                  if (1 == (isset($line_array[1])?$line_array[1]:0)) {
                                                      $desactivated = TRUE;
                                                  }
                                              }
                                              if ('locked' == trim($line_array[0])) {
                                                  if (1 == (isset($line_array[1])?$line_array[1]:0)) {
                                                      $locked = TRUE;
                                                  }
                                              }
                                          }
                                      }
                                      fclose($file_handler);
                                      $users_count++;
                                      
                                      if (!$desactivated) {
                                          $active_users_count++;
                                      }
                                      if ($locked) {
                                          $locked_users_count++;
                                      }
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


  // Completely new edition 2014-07-21
  function GetDetailedUsersArray()
  {
      $users_array = array();
      $result = $this->GetNextUserArray(TRUE);
      if (isset($result['user'])) {
          $users_array[$result['user']] = $result;
      }
      do {
          if ($result = $this->GetNextUserArray()) {
              if (isset($result['user'])) {
                  $users_array[$result['user']] = $result;
              }
          }
      } while (FALSE !== $result);
      return $users_array;
  }


  function GetNextUserArray(
      $first = FALSE,
      $fields = NULL
  ) {
      if (NULL != $fields) {
          $fields_array = $fields;
      } else {
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
                                'synchronized_dn',
                                'error_counter',
                                'last_error'
                               );
      }

      $now_epoch = time();
          
      $raw_id = $fields_array[0];

      $fields_text = '';
      $fields_separator = '';
      
      $table_name = 'sql_users_table';
      $folder = $this->GetUsersFolder();
      $parser_id = 'GET_NEXT_USER_ARRAY';
      $user_array = false;

      if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data[$table_name])) || ('files' == $this->GetBackendType())) {
          if ($first) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      foreach($fields_array as $one_field) {
                          $fields_text.= $fields_separator.'`'.$one_field.'`';
                          $fields_separator = ',';
                      }
                      if ($this->OpenMysqlDatabase()) {
                          $sQuery = "SELECT ".$fields_text." FROM `".$this->_config_data[$table_name]."` ORDER BY user ASC";
                          if (is_object($this->_mysqli)) {
                              if (!($this->_parser_pointers[$parser_id] = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                                  $this->_parser_pointers[$parser_id] = FALSE;
                                  $result = FALSE;
                                  return $result;
                              }
                          } else {
                              if (!($this->_parser_pointers[$parser_id] = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                                  $this->_parser_pointers[$parser_id] = FALSE;
                                  $result = FALSE;
                                  return $result;
                              }
                          }
                      }
                      break;
                  case 'pgsql':
                      foreach($fields_array as $one_field) {
                          $fields_text.= $fields_separator.'"'.$one_field.'"';
                          $fields_separator = ',';
                      }
                      if ($this->OpenPGSQLDatabase()) {
                          $sQuery = "SELECT ".$fields_text." FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data[$table_name]."\" ORDER BY \"user\" ASC";
                          if (!($this->_parser_pointers[$parser_id] = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                              $this->_parser_pointers[$parser_id] = FALSE;
                              $result = FALSE;
                              return $result;
                          }
                      }
                      break;
                  case 'files':
                  default:
                      if (!($this->_parser_pointers[$parser_id] = @opendir($folder))) {
                          $result = FALSE;
                          return $result;
                      }
              }
          } // if ($first)
          
          if (isset($this->_parser_pointers[$parser_id]) && (FALSE !== $this->_parser_pointers[$parser_id])) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      if ($this->OpenMysqlDatabase()) {
                          if (is_object($this->_mysqli)) {
                              do {
                                  $aRow = $this->_parser_pointers[$parser_id]->fetch_assoc();
                              } while ((FALSE !== $aRow) && (NULL !== $aRow) && ('' == $aRow['user']));
                          } else {
                              do {
                                  $aRow = mysql_fetch_assoc($this->_parser_pointers[$parser_id]);
                              } while ((FALSE !== $aRow) && (NULL !== $aRow) && ('' == $aRow['user']));
                          }
                          if (isset($aRow['user'])) {
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
                                                  'token'                => (isset($aRow['token_serial']) ? $aRow['token_serial'] : ''),
                                                  'synchronized_dn'      => $aRow['synchronized_dn'],
                                                  'error_counter'        => $aRow['error_counter'],
                                                  'last_error'           => (isset($aRow['last_error']) ? $aRow['last_error'] : 0)
                                                  );
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          do {
                              $aRow = pg_fetch_assoc($this->_parser_pointers[$parser_id]);
                          } while ((FALSE !== $aRow) && (NULL !== $aRow) && ('' == $aRow['user']));
                          if (isset($aRow['user'])) {
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
                                                  'token'                => (isset($aRow['token_serial']) ? $aRow['token_serial'] : ''),
                                                  'synchronized_dn'      => $aRow['synchronized_dn'],
                                                  'error_counter'        => $aRow['error_counter'],
                                                  'last_error'           => (isset($aRow['last_error']) ? $aRow['last_error'] : 0)
                                                  );
                          }
                      }
                      break;
                  case 'files':
                  default:
                      do {
                          $file = readdir($this->_parser_pointers[$parser_id]);
                      } while ((FALSE !== $file) && ((substr($file, -3) != ".db") || ($file == '.db')));
                      if (FALSE !== $file) {
                          $user = $this->DecodeFileId(substr($file,0,-3));
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
                                              'synchronized_dn'      => $this->GetUserSynchronizedDn(),
                                              'error_counter'        => $this->GetUserErrorCounter(),
                                              'last_error'           => $this->GetUserTokenLastError()
                                             );
                      } else {
                          $user_array = FALSE;
                          closedir($this->_parser_pointers[$parser_id]);
                      }
              }
          }
          
      }
      if (FALSE === $user_array) {
          unset($this->_parser_pointers[$parser_id]);
      } else {
          $user_array['delayed_account'] = (($user_array['error_counter'] >= $this->GetMaxDelayedFailures()) && ($now_epoch < ((isset($user_array['last_error']) ? $user_array['last_error'] : 0) + $this->GetMaxDelayedTime())));
          $user_array['delayed_time'] = 0;
          $user_array['delayed_finished'] = 0;
          if ($user_array['delayed_account']) {
              $user_array['delayed_time'] = ((isset($user_array['last_error']) ? $user_array['last_error'] : 0) + $this->GetMaxDelayedTime()) - $now_epoch;
              $user_array['delayed_finished'] = ((isset($user_array['last_error']) ? $user_array['last_error'] : 0) + $this->GetMaxDelayedTime());
              $user_array['locked'] = TRUE;
          }
      }
      return $user_array;
  }


  function GetAlgorithmsList()
  {
      $algorithms_list = '';
      $algorithms_array = explode("*",$this->_valid_algorithms);
      foreach ($algorithms_array as $algorithm_one) {
          if ('' != trim($algorithm_one)) {
              $algorithms_list.= (('' != $algorithms_list)?"\t":'').trim($algorithm_one);
          }
      }
      return $algorithms_list;
  }


  function IsValidAlgorithm(
      $algo_to_check
  ) {
      return (FALSE !== mb_strpos(mb_strtolower($this->_valid_algorithms), mb_strtolower('*'.$algo_to_check.'*')));
  }


  function GetUserScratchPasswordsArray(
      $user = ''
  ) {
      if ('' != $user) {
          $this->SetUser($user);
      }
      if ($this->_user_data['scratch_passwords'] != '') {
          return (explode(",",$this->_user_data['scratch_passwords']));
      } else {
          return array();
      }
      return (explode(",",$this->_user_data['scratch_passwords']));
  }


  function RemoveUserUsedScratchPassword(
      $to_remove
  ) {
      $scratch_passwords = trim($this->_user_data['scratch_passwords']);
      if (FALSE !== ($pos = mb_strpos($scratch_passwords, $to_remove))) {
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


  function SetScratchPasswordsAmount(
      $value
  ) {
      // Must be between 3 and 400
      $amount = intval($value);
      $amount = ($amount < 3)?3:$amount;
      $amount = ($amount > 400)?400:$amount;
      $this->_config_data['scratch_passwords_amount'] = $amount;
      return TRUE;
  }


  function GetUserScratchPasswordsList(
      $user = ''
  ) {
      if ('' != $user) {
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
      if (($scratch_loop * (1+$digits) * 2.5) > 65535) {
          $scratch_loop = inval(65535 / ((1+$digits) * 2.5));
          $this->SetScratchPasswordsAmount($scratch_loop);
      }
      $scratch_passwords = trim($this->_user_data['scratch_passwords']);
      if (strlen($scratch_passwords) > ((1.5 * $scratch_loop) * (1 + $digits))) {
          $scratch_passwords = '';
      }
      $passwords_list = '';

      for ($i=0; $i<$scratch_loop; $i++) {
          $one_password = $this->GenerateOathHotp($seed,$i,$digits);
          $scratch_passwords.= (('' != $scratch_passwords)?",":'').$one_password;
          $passwords_list.= (('' != $passwords_list)?"\t":'').$one_password;
      }
      $this->_user_data['scratch_passwords'] = $scratch_passwords;
      $result = $this->WriteUserData();
      if (!$result) {
          $passwords_list = '';
      }
      return ($passwords_list);
  }


  function SetUserDataReadFlag(
      $flag
  ) {
      $this->_user_data_read_flag = $flag;
      return TRUE;
  }


  function GetUserDataReadFlag()
  {
      return $this->_user_data_read_flag;
  }


  function SetUserMultiAccount(
      $value
  ) {
      $this->_user_data['multi_account'] = $value;
      return TRUE;
  }


  function GetUserMultiAccount()
  {
      return $this->_user_data['multi_account'];
  }


  function SetUserAttribute(
      $first_param,
      $second_param,
      $third_param = "*-*"
  ) {
      $result = FALSE;
      if ($third_param == "*-*") {
          if ($this->IsOptionInSchema('users', $first_param)) {
              $this->_user_data[$first_param] = $second_param;
              $result = TRUE;
          }
      } else {
          if ($this->IsOptionInSchema('users', $second_param)) {
              $this->SetUser($first_param);
              $this->_user_data[$second_param] = $third_param;
              $result = TRUE;
          }
      }
      return $result;
  }


  function SetUserEmail(
      $first_param,
      $second_param = "*-*"
  ) {
      $valid = FALSE;
      $result = "";
      if ($second_param == "*-*") {
          if (('' == $first_param) || (FALSE !== mb_strpos($first_param, '@'))) {
              $result = $first_param;
              $valid = TRUE;
          }
      } else {
          $this->SetUser($first_param);
          if (('' == $second_param) || (FALSE !== mb_strpos($second_param, '@'))) {
              $result = $second_param;
              $valid = TRUE;
          }
      }
      $this->_user_data['email'] = $result;

      return $valid;
  }


  function GetUserEmail(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['email'];
  }


  function SetUserLastCachedCredential(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      if ($this->GetVerboseFlag()) {
          $this->WriteLog("Debug: *SetUserLastCachedCredential cached credential: ".str_repeat('x', (strlen($input) >= 6)?strlen($input)-6:0).substr($input, -6), FALSE, FALSE, 8888, 'Debug', '');
      }
      $this->_user_data['last_cached_credential'] = sha1('$+Cred'.$input.'!@#S');

      return true;
  }


  function CompareUserLastCachedCredential(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      if ($this->GetVerboseFlag()) {
          $this->WriteLog("Debug: *CompareUserLastCachedCredential cached credential: ".str_repeat('x', (strlen($input) >= 6)?strlen($input)-6:0).substr($input, -6), FALSE, FALSE, 8888, 'Debug', '');
      }
      return (sha1('$+Cred'.$input.'!@#S') == $this->_user_data['last_cached_credential']);
  }


  function SetUserLastFailedCredential(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      if (!$this->CompareUserLastFailedCredential($input)) {
        $this->_user_data['last_failed_time'] = time();
      }
      $this->_user_data['last_failed_credential'] = sha1('$+Cred'.$input.'!@#S');

      return true;
  }


  function CompareUserLastFailedCredential(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      if (($this->_user_data['last_failed_time'] + $this->GetLastFailedWhiteDelay()) > time()) {
          return (sha1('$+Cred'.$input.'!@#S') == $this->_user_data['last_failed_credential']);
      } else {
          return false;
      }
  }


  function SetUserLastSuccessCredential(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      $this->_user_data['last_success_credential'] = sha1('$+Cred'.$input.'!@#S');

      return true;
  }


  function CompareUserLastSuccessCredential(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      return (sha1('$+Cred'.$input.'!@#S') == $this->_user_data['last_success_credential']);
  }


  function SetUserLastLoginForCache(
      $first_param,
      $second_param = "*-*"
  ) {
      $input = "";
      if ($second_param == "*-*") {
          $input = $first_param;
      } else {
          $this->SetUser($first_param);
          $input = $second_param;
      }
      $this->_user_data['last_login_for_cache'] = $input;

      return true;
  }


  function GetUserLastLoginForCache(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['last_login_for_cache'];
  }


  function SetUserGroup(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetUser($first_param);
          $result = $second_param;
      }
      $this->_user_data['group'] = $result;

      return $result;
  }


  function GetUserGroup(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['group'];
  }


  function SetUserDescription(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetUser($first_param);
          $result = $second_param;
      }
      $result = $this->EncodeForBackend($result);
      $this->_user_data['description'] = $result;
      
      $this->SetUserMultiAccount((FALSE !== mb_strpos($result,'multi_account')) ? 1 : 0);

      return $result;
  }


  function GetUserDescription(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['description'];
  }


  function SetUserSeedPassword(
      $value
  ) {
      $this->_user_data['seed_password'] = $value;
  }


  function GetUserSeedPassword()
  {
      return $this->_user_data['seed_password'];
  }


  function SetUserSms(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetUser($first_param);
          $result = $second_param;
      }
      $this->_user_data['sms'] = $result;
      return TRUE;
  }


  function GetUserSms(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['sms'];
  }


  function SetUserPrefixPin(
      $value
  ) {
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


  function SetUserRequestLdapPassword(
      $value
  ) {
      $this->_user_data['request_ldap_pwd'] = ((intval($value) > 0)?1:0);
      return TRUE;
  }


  function GetUserRequestLdapPassword()
  {
      return ((intval($this->_user_data['request_ldap_pwd']) > 0) ? 1 : 0);
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


  function SetUserLdapHashCache(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetUser($first_param);
          $value = $second_param;
      }
      $this->_user_data['ldap_hash_cache'] = $value;
      $this->_user_data['ldap_hash_validity'] = time() + $this->GetLdapHashCacheTime();
      return TRUE;
  }
  
 
  function ResetUserLdapHashCache(
      $user = ''
  ) {
      if ('' != $user) {
          $this->SetUser($user);
      }
      $this->_user_data['ldap_hash_cache'] = '';
      $this->_user_data['ldap_hash_validity'] = 0;
      return TRUE;
  }


  function GetUserLdapHashCache(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      if ($this->_user_data['ldap_hash_validity'] >= time()) {
          $value = $this->_user_data['ldap_hash_cache'];
      } else {
          $this->_user_data['ldap_hash_cache'] = '';
          $value = '';
      }
      return $value;
  }


  function SetUserAlgorithm(
      $algorithm
  ) {
      $result = FALSE;
      if ($this->IsValidAlgorithm($algorithm)) {
          $this->_user_data['algorithm'] = mb_strtolower($algorithm);
          $result = TRUE;
      } else {
          $this->WriteLog("Error: ".$algorithm." algorithm is unknown", FALSE, FALSE, 23, 'User');
      }
      return $result;
  }


  function GetUserAlgorithm(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      $result = mb_strtolower($this->_user_data['algorithm']);
      if (FALSE === mb_strpos(mb_strtolower($this->_valid_algorithms), mb_strtolower('*'.$result.'*'))) {
          $result = '';
      }

      return $result;
  }


  function SetUserTokenAlgoSuite(
      $token_algo_suite
  ) {
      $this->_user_data['token_algo_suite'] = mb_strtoupper(('' == $token_algo_suite)?'HMAC-SHA1':$token_algo_suite);
      return TRUE;
  }


  function GetUserTokenAlgoSuite(
      $user = ''
  ) {
      return mb_strtoupper(('' == $this->_user_data['token_algo_suite'])?'HMAC-SHA1':$this->_user_data['token_algo_suite']);
  }


  function SetUserTokenPrivateId(
      $private_id
  ) {
      $this->_user_data['private_id'] = $private_id;
  }


  function GetUserTokenPrivateId()
  {
      return $this->_user_data['private_id'];
  }


  /**
   * @brief   Set the user token seed in hexadecimal, base32 or raw binary
   *
   * @param   string  $seed  Seed in hexadecimal, base32 or raw binary
   * @return  none
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.3.6
   * @date    2017-02-21
   * @since   2010-08-12
   */
  function SetUserTokenSeed(
      $seed
  ) {
      $the_seed = $seed;
      if (!ctype_xdigit($the_seed)) {
          if (FALSE !== base32_decode($seed)) {
              $the_seed = bin2hex(base32_decode($seed));
          } else {
              $the_seed = bin2hex($seed);
          }
      }
      $this->_user_data['token_seed'] = $the_seed;
  }


  function GetUserTokenSeed()
  {
      return $this->_user_data['token_seed'];
  }


  function SetUserSmsOtp(
      $value
  ) {
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


  function SetUserSmsValidity(
      $value
  ) {
      $this->_user_data['sms_validity'] = $value;
  }


  function GetUserSmsValidity()
  {
      return $this->_user_data['sms_validity'];
  }


  function SetUserPin(
      $pin
  ) {
      $this->_user_data['user_pin'] = $pin;
  }


  function GetUserPin(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['user_pin'];
  }


  function SetUserAutolockTime(
      $value
  ) {
      $this->_user_data['autolock_time'] = intval($value);
  }


  function GetUserAutolockTime()
  {
      return intval($this->_user_data['autolock_time']);
  }


  function SetUserTokenDeltaTime(
      $delta_time
  ) {
      $this->_user_data['delta_time'] = $delta_time;
  }


  function GetUserTokenDeltaTime()
  {
      return $this->_user_data['delta_time'];
  }


  function SetUserKeyId(
      $key_id
  ) {
      $this->_user_data['key_id'] = $key_id;
  }


  function GetUserKeyId()
  {
      return $this->_user_data['key_id'];
  }


  function SetUserTokenNumberOfDigits(
      $number_of_digits
  ) {
      $this->_user_data['number_of_digits'] = $number_of_digits;
  }


  function GetUserTokenNumberOfDigits()
  {
      return $this->_user_data['number_of_digits'];
  }


  function SetUserTokenTimeInterval(
      $interval
  ) {
      if (intval($interval) > 0) {
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


  function SetUserTokenSerialNumber(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetUser($first_param);
          $value = $second_param;
      }
      $this->_user_data['token_serial'] = mb_strtolower($value);

      return $value;
  }


  // TODO Add new method RemoveUserTokenSerialNumber/AddUserTokenSerialNumber like AddTokenAttributedUsers
  
  function GetUserTokenSerialNumber(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return mb_strtolower(isset($this->_user_data['token_serial']) ? $this->_user_data['token_serial'] : '');
  }


  function SetUserTokenLastEvent(
      $last_event
  ) {
      $this->_user_data['last_event'] = intval($last_event);
  }


  function GetUserTokenLastEvent()
  {
      return intval($this->_user_data['last_event']);
  }


  function SetUserTokenLastLogin(
      $timestamp
  ) {
      $this->_user_data['last_login'] = intval($timestamp);
  }


  function GetUserTokenLastLogin()
  {
      return intval($this->_user_data['last_login']);
  }


  function SetUserLastLogin(
      $timestamp
  ) {
      $this->_user_data['user_last_login'] = intval($timestamp);
  }


  function GetUserLastLogin()
  {
      return intval($this->_user_data['user_last_login']);
  }


  function SetUserTokenLastError(
      $timestamp
  ) {
      // CleanCacheData;
      if ($this->IsCacheData()) {
          $this->ResetCacheArray();
          $this->WriteCacheData();
      }
      
      $this->_user_data['last_error'] = intval($timestamp);
  }


  function GetUserTokenLastError()
  {
      return intval(isset($this->_user_data['last_error']) ? $this->_user_data['last_error'] : 0);
  }


  function SetUserLocked(
    $first_param,
    $second_param = "*-*"
  ) {
    $data = 0;
    if ($second_param == "*-*") {
      $data = $first_param;
    } else {
      $this->SetUser($first_param);
      $data = $second_param;
    }
    $this->_user_data['locked'] = $data;

    // CleanCacheData;
    if ($this->IsCacheData()) {
      $this->ResetCacheArray();
      $this->WriteCacheData();
    }
    
    return $data;
  }


  function GetUserLocked(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return intval($this->_user_data['locked']);
  }


  function GetUserDelayed(
      $user = ''
  ) {
      // delayed_account, delayed_time, delayed_finished
      if ($user != '') {
          $this->SetUser($user);
      }
      return (isset($this->_user_data['delayed_account']) ? $this->_user_data['delayed_account'] : FALSE);
  }


  function GetUserDelayedTime(
      $user = ''
  ) {
      if ($user != '') {
          $this->SetUser($user);
      }
      return intval(isset($this->_user_data['delayed_time']) ? $this->_user_data['delayed_time'] : 0);
  }


  function SetUserActivated(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = TRUE;
      $data = 0;
      if ($second_param == "*-*") {
          $data = $first_param;
      } else {
          $result = $this->SetUser($first_param);
          $data = $second_param;
      }
      $desactive = ($data > 0)?0:1;
      $this->_user_data['desactivated'] = $desactive;
      
      if (0 == $desactive) {
          $this->SetUserErrorCounter(0);
          $this->SetUserLocked(0);
      }

      // CleanCacheData;
      if ($this->IsCacheData()) {
          $this->ResetCacheArray();
          $this->WriteCacheData();
      }
      
      return $result;
  }


  function GetUserActivated(
      $user = ''
  ) {
      $result = TRUE;
      if($user != '') {
          $result = $this->SetUser($user);
      }
      if ($result) {
          $result = intval($this->_user_data['desactivated'] > 0)?0:1;
      }
      return $result;
  }


  function SetUserSynchronized(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = TRUE;
      $data = 0;
      if ($second_param == "*-*") {
          $data = $first_param;
      } else {
          $result = $this->SetUser($first_param);
          $data = $second_param;
      }
      $this->_user_data['synchronized'] = $data;

      return $result;
  }


  function GetUserSynchronized(
      $user = ''
  ) {
      if ($user != '') {
          $this->SetUser($user);
      }
      return intval($this->_user_data['synchronized']);
  }
  

  function IsUserSynchronized(
      $user = ''
  ) {
      return (1 == ($this->GetUserSynchronized($user)));
  }
  

  function SetUserSynchronizedChannel(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = 0;
      if ($second_param == "*-*") {
          $data = $first_param;
      } else {
          $this->SetUser($first_param);
          $data = $second_param;
      }
      $this->_user_data['synchronized_channel'] = $data;

      return $data;
  }


  function GetUserSynchronizedChannel(
      $user = ''
  ) {
      if ($user != '') {
          $this->SetUser($user);
      }
      return ($this->_user_data['synchronized_channel']);
  }
  

  function SetUserSynchronizedDn(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = 0;
      if ($second_param == "*-*") {
          $data = $first_param;
      } else {
          $this->SetUser($first_param);
          $data = $second_param;
      }
      $this->_user_data['synchronized_dn'] = $data;

      return $data;
  }


  function GetUserSynchronizedDn(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return ($this->_user_data['synchronized_dn']);
  }
  

  function SetUserSynchronizedServer(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = 0;
      if ($second_param == "*-*") {
          $data = $first_param;
      } else {
          $this->SetUser($first_param);
          $data = $second_param;
      }
      $this->_user_data['synchronized_server'] = $data;

      return $data;
  }


  function GetUserSynchronizedServer(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return ($this->_user_data['synchronized_server']);
  }
  

  function SetUserSynchronizedTime(
      $first_param = "*-*",
      $second_param = "*-*"
  ) {
      $data = 0;
      if ($second_param == "*-*") {
          if ($first_param == "*-*") {
              $data = time();
          } else {
              $data = $first_param;
          }
      } else {
          $this->SetUser($first_param);
          $data = $second_param;
      }
      $this->_user_data['synchronized_time'] = $data;

      return $data;
  }


  function GetUserSynchronizedTime(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return intval($this->_user_data['synchronized_time']);
  }


  function SetUserErrorCounter(
      $counter
  ) {
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
  function CreateToken(
      $serial = '',
      $algorithm = 'totp',
      $seed = '',
      $number_of_digits = 6,
      $time_interval_or_next_event = -1,
      $manufacturer = 'multiOTP',
      $issuer = '',
      $description = '',
      $token_algo_suite = ''
  ) {
      $the_serial = mb_strtolower($serial);
      if ('' == $the_serial) {
          $the_serial = mb_strtolower('mu'.bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999)));
      }
      $the_description = $description;
      if ('' == $the_description) {
          $the_description = trim($manufacturer.' '.$the_serial);
      }
      $the_token = mb_strtolower($the_serial);
      if ($this->ReadTokenData($the_token, TRUE)) {
          return FALSE; // ERROR: token already exists.
      } else {
          $this->SetToken($the_token);
          $this->SetTokenDescription($the_description);
          $this->SetTokenManufacturer(('' != $manufacturer)?$manufacturer:'multiOTP');
          $this->SetTokenIssuer(('' != $issuer)?$issuer:$this->GetIssuer());
          $this->SetTokenSerialNumber($the_serial);
          $this->SetTokenAlgorithm(mb_strtolower($algorithm));
          $this->SetTokenAlgoSuite(mb_strtolower($token_algo_suite));
          $this->SetTokenKeyAlgorithm(mb_strtolower($algorithm));
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

          if ('hotp' == mb_strtolower($algorithm)) {
              $next_event = ((-1 == $time_interval_or_next_event)?0:$time_interval_or_next_event);
              $time_interval = 0;
          } else {
              $next_event = 0;
              $time_interval = ((-1 == $time_interval_or_next_event)?30:$time_interval_or_next_event);
              if ("motp" == mb_strtolower($algorithm)) {
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


  function AssignTokenToUser(
    $user,
    $token
  ) {
    $the_token = mb_strtolower($token);
    $this->SetUser($user);
    
    // First, remove the old one (if any)
    $old_token = $this->GetUserTokenSerialNumber();
    if ($this->RemoveTokenAttributedUsers($old_token, $user)) {
        $this->WriteTokenData();
    }
    
    // Attribute the new one
    $this->SetUserTokenSerialNumber($the_token);
    $this->AddUserAttributedTokens($the_token);

    // Read the token attributes
    $this->SetToken($the_token);
    $this->AddTokenAttributedUsers($user);

    // Set the token attributes in the users attributes
    $this->SetUserKeyId($the_token);
    $this->SetUserAlgorithm($this->GetTokenAlgorithm());
    $this->SetUserTokenAlgoSuite($this->GetTokenAlgoSuite());
    $this->SetUserTokenSeed($this->GetTokenSeed());
    $this->SetUserTokenNumberOfDigits($this->GetTokenNumberOfDigits());
    $this->SetUserTokenTimeInterval($this->GetTokenTimeInterval());
    $this->SetUserTokenLastEvent($this->GetTokenLastEvent());

    return ($this->WriteUserData() && $this->WriteTokenData());
  }


  function RemoveTokenFromUser(
    $user
  ) {
    $this->SetUser($user);
    $old_token = $this->GetUserTokenSerialNumber();
    $this->SetUserTokenSerialNumber('');
    $this->RemoveUserAttributedTokens($old_token);
    $this->RemoveTokenAttributedUsers($old_token);
    return ($this->WriteUserData() && $this->WriteTokenData());
  }


  function SetTokenSerialNumberLength(
      $value
  ) {
      $this->_config_data['token_serial_number_length'] = trim($value);
  }
  
  
  function AddTokenSerialNumberLength(
      $length
  ) {
      if (intval($length) > 0) {
          $actual = trim($this->GetTokenSerialNumberLength());
          $length_exists = FALSE;

          // We add the serial number length only if it is not already attributed
          $token_serial_number_length_array = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$actual))));
          foreach($token_serial_number_length_array as $one_length) {
              if (intval($one_length) == intval($length)) {
                  $length_exists = TRUE;
                  break;
              }
          }
          if (!$length_exists) {
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
      if (FALSE === mb_strpos($token_serial_number_length, '12')) {
          // 12 is the RFC size of the serial number, we must have it and we add it if needed
          $token_serial_number_length.=' 12';
      }
      return $token_serial_number_length;
  }


  function SetTokenOtpListOfLength(
      $value
  ) {
      $this->_config_data['token_otp_list_of_length'] = trim($value);
  }
  
  
  function AddTokenOtpListOfLength(
      $length
  ) {
      if (intval($length) > 0) {
          $actual = trim($this->GetTokenOtpListOfLength());
          $length_exists = FALSE;

          // We add the OTP length only if it is not already attributed
          $token_otp_list_of_length_array = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$actual))));
          foreach($token_otp_list_of_length_array as $one_length) {
              if (intval($one_length) == intval($length)) {
                  $length_exists = TRUE;
                  break;
              }
          }
          if (!$length_exists) {
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
      if (FALSE === mb_strpos($token_otp_list_of_length, '6')) {
          // 6 is an RFC size of the OTP, we should have it and we add it if needed
          $token_otp_list_of_length.=' 6';
      }
      return $token_otp_list_of_length;
  }


  function SetTokenDescription(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetToken($first_param);
          $value = $second_param;
      }
      $value = $this->EncodeForBackend($value);
      $this->_token_data['description'] = $value;
      return $value;
  }


  function GetTokenDescription(
      $token = ''
  ) {
      $the_token = mb_strtolower($token);
      if($the_token != '') {
          $this->SetToken($the_token);
      }
      return $this->_token_data['description'];
  }


  function SetToken(
      $token,
      $create = TRUE
  ) {
      $the_token = mb_strtolower($token);
      $this->ResetTokenArray();
      $this->_token = $the_token;
      $result = $this->ReadTokenData('', $create); // First parameter empty, otherwise it will loop with SetToken !

      return ($create || $result);
  }


  function RenameCurrentToken(
      $new_token,
      $no_error_info = FALSE
  ) {
      $the_new_token = mb_strtolower($new_token);
      $result = FALSE;
      if ($this->CheckTokenExists($the_new_token, false)) { // Check if the new token already exists
          $this->WriteLog("Error: Unable to rename the current token ".$this->GetToken()." to ".$the_new_token." because it already exists", FALSE, FALSE, 28, 'Token', '');
      } else {
          if ($this->CheckTokenExists('', false)) { // Check if the current token already exists
              if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType())) {
                  switch ($this->GetBackendType()) {
                      case 'mysql':
                          $esc_actual = escape_mysql_string($this->GetToken());
                          $esc_new    = escape_mysql_string($the_new_token);
                          if ($this->OpenMysqlDatabase()) {
                              if ('' != $this->_config_data['sql_tokens_table']) {
                                  $sQuery = "UPDATE `".$this->_config_data['sql_tokens_table']."` SET token_id='".$esc_new."' WHERE `token_id`='".$esc_actual."'";
                                  
                                  if (is_object($this->_mysqli)) {
                                      if (!($rResult = $this->_mysqli->query($sQuery))) {
                                          if (!$no_error_info) {
                                              $this->WriteLog("Error: Could not rename the token ".$this->GetToken().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'Token');
                                          }
                                      } else {
                                          $num_rows = $this->_mysqli->affected_rows;
                                      }
                                  } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                      if (!$no_error_info) {
                                          $this->WriteLog("Error: Could not rename the token ".$this->GetToken().": ".mysql_error(), FALSE, FALSE, 28, 'Token');
                                      }
                                  } else {
                                      $num_rows = mysql_affected_rows($this->_mysql_database_link);
                                  }
                                  
                                  if (0 == $num_rows) {
                                      $this->WriteLog("Error: Could not rename the token ".$this->GetToken().". Token does not exist", FALSE, FALSE, 29, 'Token');
                                  } else {
                                      $this->WriteLog("Info: Token ".$this->GetToken()." successfully renamed to $the_new_token", FALSE, FALSE, 19, 'Token');
                                      $result = TRUE;
                                  }
                              }
                          }
                          break;
                      case 'pgsql':
                          $esc_actual = pg_escape_string($this->GetToken());
                          $esc_new    = pg_escape_string($the_new_token);
                          if ($this->OpenPGSQLDatabase()) {
                              if ('' != $this->_config_data['sql_tokens_table']) {
                                  $sQuery = "UPDATE \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_tokens_table']."\" SET \"token_id\" = '".$esc_new."' WHERE \"token_id\" = '".$esc_actual."'";
                                  
                                  if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                                      if (!$no_error_info) {
                                          $this->WriteLog("Error: Could not rename the token ".$this->GetToken().": ".pg_last_error(), FALSE, FALSE, 28, 'Token');
                                      }
                                  } else {
                                      $num_rows = pg_affected_rows($rResult);
                                  }

                                  if (0 == $num_rows) {
                                      $this->WriteLog("Error: Could not rename the token ".$this->GetToken().". Token does not exist", FALSE, FALSE, 29, 'Token');
                                  } else {
                                      $this->WriteLog("Info: Token ".$this->GetToken()." successfully renamed to $the_new_token", FALSE, FALSE, 19, 'Token');
                                      $result = TRUE;
                                  }
                              }
                          }
                          break;
                      case 'files':
                      default:
                          $old_token_filename = mb_strtolower($this->GetToken()).'.db';
                          $new_token_filename = mb_strtolower($the_new_token).'.db';
                          rename($this->GetTokensFolder().$old_token_filename, $this->GetTokensFolder().$new_token_filename);
                          $result = TRUE;
                          break;
                  }
              }
          }
          if ($result) {
              $this->_token = $the_new_token;
          }
      }
      return $result;
  }


  function GetToken()
  {
      return mb_strtolower($this->_token);
  }


  function CheckTokenExists(
      $token = '',
      $log_error = true
  ) {
      $the_token = mb_strtolower($token);
      $check_token = mb_strtolower('' != $the_token) ? $the_token : $this->GetToken();
      $result = FALSE;
      
      if ('' != trim($check_token)) {
          if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType())) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      if ($this->OpenMysqlDatabase()) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '{$check_token}'";
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              } else {
                                  $num_rows = $rResult->num_rows;
                              }
                          } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              $num_rows = mysql_num_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              if ($log_error) {
                                  $this->WriteLog("Error: Token ".$check_token.". does not exist", FALSE, FALSE, 41, 'System', '', 3);
                              }
                              $result = FALSE;
                          } else {
                              $result = TRUE;
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_tokens_table']."\" WHERE \"token_id\" = '{$check_token}'";
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              $num_rows = pg_num_rows($rResult);
                          }

                          if (0 == $num_rows) {
                              if ($log_error) {
                                  $this->WriteLog("Error: Token ".$check_token.". does not exist", FALSE, FALSE, 41, 'System', '', 3);
                              }
                              $result = FALSE;
                          } else {
                              $result = TRUE;
                          }
                      }
                      break;
                  case 'files':
                  default:
                      $token_filename = mb_strtolower($check_token).'.db';
                      $result = file_exists($this->GetTokensFolder().$token_filename);
                      if ($log_error && (false === $result)) {
                          $this->WriteLog("Error: Token ".$check_token.". does not exist", FALSE, FALSE, 41, 'System', '', 3);
                      }
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


  function AddLastImportedToken(
      $token
  ) {
      $the_token = mb_strtolower($token);
      $this->_last_imported_tokens[] = $the_token;
  }


  function GetLastImportedTokens()
  {
      return $this->_last_imported_tokens;
  }


  function SetTokenManufacturer(
      $manufacturer
  ) {
      $this->_token_data['manufacturer'] = $manufacturer;
  }


  function GetTokenManufacturer()
  {
      return $this->_token_data['manufacturer'];
  }


  function SetTokenModel(
      $model
  ) {
      $this->_token_data['model'] = $model;
  }


  function GetTokenModel()
  {
      return $this->_token_data['model'];
  }


  function SetTokenIssueNo(
      $issue_no
  ) {
      $this->_token_data['issue_no'] = trim($issue_no);
  }


  function GetTokenIssueNo()
  {
      return $this->_token_data['issue_no'];
  }


  function SetTokenKeyId(
      $key_id
  ) {
      $this->_token_data['key_id'] = trim($key_id);
  }


  function GetTokenKeyId()
  {
      return $this->_token_data['key_id'];
  }


  function SetTokenKeyUsage(
      $key_usage
  ) {
      $this->_token_data['key_usage'] = trim($key_usage);
  }


  function GetTokenKeyUsage()
  {
      return $this->_token_data['key_usage'];
  }


  function GetTokenEncryptionHash()
  {
      return $this->_token_data['encryption_hash'];
  }


  // This will (re)set only one user to the token
  function SetTokenAttributedUsers(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetToken($first_param);
          $result = $second_param;
      }
      $this->_token_data['attributed_users'] = $result;

      return $result;
  }


  function AddTokenAttributedUsers(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = "";
      $result = FALSE;
      if ($second_param == "*-*") {
          $data = $first_param;
          $token = mb_strtolower($this->GetToken());
      } else {
          $token = mb_strtolower($first_param);
          if ($this->CheckTokenExists($token, false)) {
              $this->SetToken($token);
          }
          $data = $second_param;
      }
      if ($this->CheckTokenExists($token, false)) {
          $actual = trim($this->GetTokenAttributedUsers());
          // We attribute the user only if it is not already attributed
          if (FALSE === mb_strpos(','.$actual.',', ','.$data.',')) {
              $this->SetTokenAttributedUsers($actual.(('' != $actual)?',':'').$data);
          }
          $result = TRUE;
      }
      return $result;
  }


  function RemoveTokenAttributedUsers(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = "";
      $result = FALSE;
      if ($second_param == "*-*") {
          $data = $first_param;
          $token = mb_strtolower($this->GetToken());
      } else {
          $token = mb_strtolower($first_param);
          if ($this->CheckTokenExists($token, false)) {
              $this->SetToken($token);
          }
          $data = $second_param;
      }
      if ($this->CheckTokenExists($token, false)) {
          if (FALSE !== mb_strpos(','.trim($this->GetTokenAttributedUsers()).',', ','.$data.',')) {
              $actual = str_replace(','.$data.',',',',','.trim($this->GetTokenAttributedUsers()).',');
              $this->SetTokenAttributedUsers(substr($actual,1, strlen($actual)-2));
              $result = TRUE;
          }
      }
      return $result;
  }


  function GetTokenAttributedUsers(
      $token = ''
  ) {
      if($token != '') {
          $the_token = mb_strtolower($token);
          $this->SetToken($the_token);
      }
      return $this->_token_data['attributed_users'];
  }


  // This will (re)set only one token to the user
  function SetUserAttributedTokens(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetUser($first_param);
          $result = $second_param;
      }
      $this->_user_data['attributed_tokens'] = $result;

      return $result;
  }


  function AddUserAttributedTokens(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = "";
      $result = FALSE;
      if ($second_param == "*-*") {
          $data = $first_param;
          $user = $this->GetUser();
      } else {
          $user = $first_param;
          if ($this->CheckUserExists($user)) {
              $this->SetUser($user);
          }
          $data = $second_param;
      }
      if ($this->CheckUserExists($user)) {
          $actual = trim($this->GetUserAttributedTokens());
          // We attribute the token only if it is not already attributed
          if (FALSE === mb_strpos(','.$actual.',', ','.$data.',')) {
              $this->SetUserAttributedTokens($actual.(('' != $actual)?',':'').$data);
          }
          $result = TRUE;
      }
      return $result;
  }


  function RemoveUserAttributedTokens(
      $first_param,
      $second_param = "*-*"
  ) {
      $data = "";
      $result = FALSE;
      if ($second_param == "*-*") {
          $data = $first_param;
          $user = $this->GetUser();
      } else {
          $user = $first_param;
          if ($this->CheckUserExists($user)) {
              $this->SetUser($user);
          }
          $data = $second_param;
      }
      if ($this->CheckUserExists($user)) {
          if (FALSE !== mb_strpos(','.trim($this->GetUserAttributedTokens()).',', ','.$data.',')) {
              $actual = str_replace(','.$data.',',',',','.trim($this->GetUserAttributedTokens()).',');
              $this->SetUserAttributedTokens(substr($actual,1, strlen($actual)-2));
              $result = TRUE;
          }
      }
      return $result;
  }


  function GetUserAttributedTokens(
      $user = ''
  ) {
      if($user != '') {
          $this->SetUser($user);
      }
      return $this->_user_data['attributed_tokens'];
  }


  // SerialNo is the original SerialNo of the key.
  function SetTokenSerialNo(
      $serial_no
  ) {
      $this->_token_data['serial_no'] = mb_strtolower($serial_no);
  }


  // SerialNo is the original SerialNo of the key.
  function GetTokenSerialNo()
  {
      return mb_strtolower($this->_token_data['serial_no']);
  }


  function SetTokenSerialNumber(
      $token_serial
  ) {
      $this->_token_data['token_serial'] = mb_strtolower($token_serial);
      $len_token_serial = strlen($token_serial);
      if ($len_token_serial > 0) {
          // We add this length automatically in the list of the existing serial number length
          $this->AddTokenSerialNumberLength($len_token_serial);
      }
  }


  function GetTokenSerialNumber()
  {
      return mb_strtolower(isset($this->_token_data['token_serial']) ? $this->_token_data['token_serial'] : '');
  }


  function SetTokenIssuer(
      $issuer
  ) {
      if ('' == $issuer) {
          $this->_token_data['issuer'] = $this->GetIssuer();
      } else {
          $this->_token_data['issuer'] = $issuer;
      }
  }


  function GetTokenIssuer()
  {
      return $this->_token_data['issuer'];
  }


  function SetTokenKeyAlgorithm(
      $key_algorithm
  ) {
      $this->_token_data['key_algorithm'] = $key_algorithm;
  }


  function GetTokenKeyAlgorithm()
  {
      return $this->_token_data['key_algorithm'];
  }


  function SetTokenAlgorithm(
      $algorithm
  ) {
      $result = FALSE;
      if (FALSE === mb_strpos(mb_strtolower($this->_valid_algorithms), mb_strtolower('*'.$algorithm.'*'))) {
          $this->WriteLog("Error: ".$algorithm." algorithm unknown for token ".$this->GetToken(), FALSE, FALSE, 23, 'Token');
      } else {
          $this->_token_data['algorithm'] = mb_strtolower($algorithm);
          $result = TRUE;
      }
      return $result;
  }


  function GetTokenAlgorithm()
  {
      $result = $this->_token_data['algorithm'];
      if (FALSE === mb_strpos(mb_strtolower($this->_valid_algorithms), mb_strtolower('*'.$result.'*'))) {
          $result = '';
      }

      return $result;
  }


  function SetTokenAlgoSuite(
      $token_algo_suite
  ) {
      $this->_token_data['token_algo_suite'] = mb_strtoupper(('' == $token_algo_suite)?'HMAC-SHA1':$token_algo_suite);
      return TRUE;
  }


  function GetTokenAlgoSuite()
  {
      return mb_strtoupper(('' == $this->_token_data['token_algo_suite'])?'HMAC-SHA1':$this->_token_data['token_algo_suite']);
  }


  function SetTokenOtp(
      $otp
  ) {
      $this->_token_data['otp'] = $otp;
  }


  function GetTokenOtp()
  {
      return $this->_token_data['otp'];
  }


  function SetTokenFormat(
      $format
  ) {
      $this->_token_data['format'] = $format;
  }


  function GetTokenFormat()
  {
      return $this->_token_data['format'];
  }


  function SetTokenNumberOfDigits(
      $number_of_digits
  ) {
      $this->_token_data['number_of_digits'] = $number_of_digits;
      // We add this number of digits automatically in the list of the existing list of length
      $this->AddTokenOtpListOfLength($number_of_digits);
  }


  function GetTokenNumberOfDigits()
  {
      return $this->_token_data['number_of_digits'];
  }


  function SetTokenLastEvent(
      $last_event
  ) {
      $this->_token_data['last_event'] = $last_event;
  }


  function GetTokenLastEvent()
  {
      return $this->_token_data['last_event'];
  }


  function SetTokenLastLogin(
      $timestamp
  ) {
      $this->_token_data['last_login'] = intval($timestamp);
  }


  function GetTokenLastLogin()
  {
      return intval($this->_token_data['last_login']);
  }


  function SetTokenErrorCounter(
      $counter
  ) {
      $this->_token_data['error_counter'] = $counter;
  }


  function GetTokenErrorCounter()
  {
      return $this->_token_data['error_counter'];
  }


  function SetTokenDeltaTime(
      $delta_time
  ) {
      $this->_token_data['delta_time'] = $delta_time;
  }


  function GetTokenDeltaTime()
  {
      return $this->_token_data['delta_time'];
  }


  function SetTokenTimeInterval(
      $time_interval
  ) {
      $this->_token_data['time_interval'] = $time_interval;
  }


  function GetTokenTimeInterval()
  {
      return $this->_token_data['time_interval'];
  }


  function SetTokenPrivateId(
      $private_id
  ) {
      $this->_token_data['private_id'] = $private_id;
  }


  function GetTokenPrivateId()
  {
      return $this->_token_data['private_id'];
  }


  /**
   * @brief   Set the token seed in hexadecimal, base32 or raw binary
   *
   * @param   string  $seed  Seed in hexadecimal, base32 or raw binary
   * @return  none
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.3.6
   * @date    2017-02-21
   * @since   2010-08-12
   */
  function SetTokenSeed(
      $seed
  ) {
      $the_seed = $seed;
      if (!ctype_xdigit($the_seed)) {
          if (FALSE !== base32_decode($seed)) {
              $the_seed = bin2hex(base32_decode($seed));
          } else {
              $the_seed = bin2hex($seed);
          }
      }
      $this->_token_data['token_seed'] = $the_seed;
  }


  function GetTokenSeed()
  {
      return $this->_token_data['token_seed'];
  }


  function SetTokensFolder(
      $folder,
      $create = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_tokens_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing tokens folder ".$new_folder, FALSE, FALSE, 28, 'System',  '');
          }
      }
  }


  function GetTokensFolder()
  {
      if ('' == $this->_tokens_folder) {
          $this->SetTokensFolder($this->GetScriptFolder()."tokens/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_tokens_folder);
  }


  function GetTokensList()
  {
      return $this->GetList('token_id', 'sql_tokens_table', $this->GetTokensFolder());
  }


  function GetTokensCount($no_cache = FALSE)
  {
      if (($this->IsCacheData()) && (intval($this->ReadCacheValue('tokens_count')) >= 0) && (!$no_cache)) {
          $tokens_count = intval($this->ReadCacheValue('tokens_count'));
      } else {
          $tokens_count = 0;
          switch ($this->GetBackendType()) {

              case 'mysql':
                  if ($this->OpenMysqlDatabase())
                  {
                      $sQuery  = "SELECT COUNT(token_id) AS counter FROM `".$this->_config_data['sql_tokens_table']."` ";
                      if (is_object($this->_mysqli)) {
                          if (!($result = $this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              if ($aRow = $result->fetch_assoc()) {
                                  $tokens_count = $aRow['counter'];
                              }
                          }
                      } else {
                          if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              if ($aRow = mysql_fetch_assoc($rResult)) {
                                  $tokens_count = $aRow['counter'];
                              }
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase())
                  {
                      $sQuery  = "SELECT COUNT(\"token_id\") AS \"counter\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_tokens_table']."\" ";
                      if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                      } else {
                          if ($aRow = pg_fetch_assoc($rResult)) {
                              $tokens_count = $aRow['counter'];
                          }
                      }
                  }
                  break;
              case 'files':
              default:
                  if ($tokens_handle = @opendir($this->GetTokensFolder())) {
                      while ($file = readdir($tokens_handle)) {
                          if ((substr($file, -3) == ".db") && ($file != '.db')) {
                              $tokens_count++;
                          }
                      }
                      closedir($tokens_handle);
                  }
          }
          if (($this->IsCacheData()) && ($tokens_count >= 0)) {
              $this->WriteCacheValue('tokens_count', $tokens_count);
              $this->WriteCacheData();
          }
      }
      return $tokens_count;
  }


  function DeleteToken(
      $token = '',
      $no_error_info = FALSE
  ) {
      $the_token = mb_strtolower($token);
      if ('' != $the_token) {
          $this->SetToken($the_token);
      }
      
      $result = FALSE;
      
      // First, we delete the token file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $token_filename = $this->GetToken().'.db';
          if (!file_exists($this->GetTokensFolder().$token_filename)) {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Error: *Unable to delete token ".$this->GetToken().", the tokens database file ".$this->GetTokensFolder().$token_filename." does not exist", FALSE, FALSE, 29, 'Token',  '');
              } else {
                  $this->WriteLog("Error: Unable to delete token ".$this->GetToken(), FALSE, FALSE, 29, 'Token',  '');
              }
          } else {
              $result = unlink($this->GetTokensFolder().$token_filename);
              if ($result) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *Token ".$this->GetToken()." successfully deleted", FALSE, FALSE, 19, 'Token', '');
                  }
              } else {
                  $this->WriteLog("Error: Unable to delete token ".$this->GetToken(), FALSE, FALSE, 28, 'Token',  '');
              }
          }
      }

      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_tokens_table']) {
                          $sQuery  = "DELETE FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '".$this->GetToken()."'";

                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  if (!$no_error_info) {
                                      $this->WriteLog("Error: Could not delete token ".$this->GetToken().": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'Token', '');
                                  }
                              } else {
                                  $num_rows = $this->_mysqli->affected_rows;
                              }
                          } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete token ".$this->GetToken().": ".mysql_error(), FALSE, FALSE, 28, 'Token', '');
                              }
                          } else {
                              $num_rows = mysql_affected_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete token ".$this->GetToken().". Token does not exist", FALSE, FALSE, 29, 'Token', '');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Info: *token ".$this->GetToken()." successfully deleted", FALSE, FALSE, 19, 'Token', '');
                              }
                              $result = TRUE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_tokens_table']) {
                          $sQuery  = "DELETE FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_tokens_table']."\" WHERE \"token_id\" = '".$this->GetToken()."'";

                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete token ".$this->GetToken().": ".pg_last_error(), FALSE, FALSE, 28, 'Token', '');
                              }
                          } else {
                              $num_rows = pg_affected_rows($rResult);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete token ".$this->GetToken().". Token does not exist", FALSE, FALSE, 29, 'Token', '');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Info: *token ".$this->GetToken()." successfully deleted", FALSE, FALSE, 19, 'Token', '');
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
      if ($result) {
          $this->TouchFolder('data',
                             'Token',
                             $this->GetToken(),
                             TRUE,
                             "DeleteToken");
      }
      return $result;
  }


  function ReadTokenData(
      $token = '',
      $create = FALSE
  ) {
      $the_token = mb_strtolower($token);
      if ('' != $the_token) {
          $this->SetToken($the_token);
      }
      $result = FALSE;
      
      // We initialize the encryption hash to empty
      $this->_token_data['encryption_hash'] = '';
      
      // First, we read the user file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $token_filename = mb_strtolower($this->GetToken()).'.db';
          if (!file_exists($this->GetTokensFolder().$token_filename)) {
              if (!$create) {
                  $this->WriteLog("Error: database file ".$this->GetTokensFolder().$token_filename." for token ".$this->_token." does not exist", FALSE, FALSE, 29, 'System', '');
              }
          } else {
              if ($file_handler = @fopen($this->GetTokensFolder().$token_filename, "rt")) {
                  $first_line = trim(fgets($file_handler));
                  
                  while (!feof($file_handler)) {
                      $line = trim(fgets($file_handler));
                      $line_array = explode("=",$line,2);
                      if (":" == substr($line_array[0], -1)) {
                          $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                          $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                      }
                      if ('' != trim($line_array[0])) {
                          $this->_token_data[mb_strtolower($line_array[0])] = $line_array[1];
                      }
                  }
                  
                  fclose($file_handler);
                  $result = TRUE;

                  if ('' != $this->_token_data['encryption_hash']) {
                      if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                          $this->_token_data['encryption_hash'] = "ERROR";
                          $this->WriteLog("Error: the token information encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                          $result = FALSE;
                      }
                  }
              }
          }
      }

      // And now, we override the values if another backend type is defined
      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_tokens_table']) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '".$this->_token."'";
                          $aRow = NULL;
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 40, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = $rResult->fetch_assoc();
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = mysql_fetch_assoc($rResult);
                              }
                          }

                          if (NULL != $aRow) {
                              $result = FALSE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['tokens']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'token_id')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $this->_token_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $this->_token_data[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the tokens database schema", FALSE, FALSE, 98, 'System', '');
                                  }
                                  $result = TRUE;
                              }
                              if(0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for token ".$this->_token." does not exist", FALSE, FALSE, 29, 'System', '');
                              }
                          }
                      }
                      if ('' != $this->_token_data['encryption_hash']) {
                          if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $this->_token_data['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the tokens mysql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                              $result = FALSE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_tokens_table']) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_tokens_table']."\" WHERE \"token_id\" = '".$this->_token."'";
                          $aRow = NULL;
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $aRow = pg_fetch_assoc($rResult);
                          }

                          if (NULL != $aRow) {
                              $result = FALSE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['tokens']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'token_id')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $this->_token_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $this->_token_data[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the tokens database schema", FALSE, FALSE, 98, 'System', '');
                                  }
                                  $result = TRUE;
                              }
                              if(0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for token ".$this->_token." does not exist", FALSE, FALSE, 29, 'System', '');
                              }
                          }
                      }
                      if ('' != $this->_token_data['encryption_hash']) {
                          if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $this->_token_data['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the tokens pgsql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
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


  function WriteTokenData(
      $write_token_data_array = array()
  ) {
      if ('' == trim($this->GetToken())) {
          $result = false;
      } else {
          $result = $this->WriteData(array_merge(array('item'               => 'Token',
                                                       'table'              => 'tokens',
                                                       'folder'             => $this->GetTokensFolder(),
                                                       'data_array'         => $this->_token_data,
                                                       'force_file'         => false,
                                                       'id_field'           => 'token_id',
                                                       'id_value'           => $this->GetToken()
                                                      ), $write_token_data_array));
      }
      return $result;
  }


  function SetLastClearOtpValue(
      $value = ''
  ) {
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


  function AddTemporaryBadServer(
      $server,
      $timestamp
  ) {
      $this->_servers_temp_bad_list[$server] = intval($timestamp);
  }


  function IsTemporaryBadServer(
    $server
  ) {
    $result = FALSE;
    foreach ($this->_servers_temp_bad_list as $badserver => $timestamp) {
      if ($badserver == $server) {
        if (($timestamp + (1 * 60)) <= time()) {
          $result = TRUE;
        }
        break;
      }
    }
  }


  function CheckUserLdapPassword(
      $ldap_username,
      $ldap_password
  ) {
      $this->SetLdapServerReachable(FALSE);
      $result = FALSE;

      // DistinguishedName must be encoded in UTF-8
      $ldap_bind_dn = encode_utf8_if_needed($ldap_username);
      
      if (('' != $ldap_username) && (FALSE === mb_strpos(mb_strtolower($ldap_bind_dn), 'cn='))) {
          $ldap_bind_dn = 'CN='.$ldap_bind_dn.','.$this->GetLdapBaseDn();
      }

      if (!function_exists('ldap_connect')) {
          $this->WriteLog("Error: LDAP library not installed", FALSE, FALSE, 39, 'System', '', 3);
          $this->EnableLdapError();
      } elseif (('' != $this->GetLdapDomainControllers()) && ('' != $ldap_username) && ('' != $ldap_password)) {
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
                                'recursive_groups'   => $this->IsLdapRecursiveGroups(),
                                'time_limit'         => $this->GetLdapTimeLimit(),
                                'use_ssl'            => $this->IsLdapSsl(),
                                'cache_support'      => $this->IsLdapCacheOn(),
                                'cache_folder'       => $this->GetLdapCacheFolder(),
                                'expired_password_valid' => $this->IsLdapExpiredPasswordValid()
                               );

          $ldap_connection=new MultiotpAdLdap($ldap_options);

          $this->SetLdapServerReachable($ldap_connection->IsServerReachable());

          $result = !$ldap_connection->IsError();

          if ((!$result) && $this->GetVerboseFlag()) {
              $this->WriteLog($ldap_connection->ErrorMessage(), FALSE, FALSE, 30, 'LDAP', '', 3);
          }
          unset($ldap_connection);
      }
      return $result;
  }


  function GetLdapUsersList(
      $user_filter = "*"
  ) {
      $this->DisableLdapError();
      $users_list = '';
      $in_groups_array = array();
      $users_in_groups = array();
      $result_array = array();
      
      if (!function_exists('ldap_connect')) {
          $result = FALSE;
          $this->WriteLog("Error: LDAP library not installed", FALSE, FALSE, 39, 'System', '', 3);
          $this->EnableLdapError();
      } elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword())) {
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
                                'recursive_groups'   => $this->IsLdapRecursiveGroups(),
                                'time_limit'         => $this->GetLdapTimeLimit(),
                                'use_ssl'            => $this->IsLdapSsl(),
                                'cache_support'      => $this->IsLdapCacheOn(),
                                'cache_folder'       => $this->GetLdapCacheFolder()
                               );

          $ldap_connection=new MultiotpAdLdap($ldap_options);
          if ($users_info = $ldap_connection->users_info($user_filter, $this->GetLdapFieldsArray())) {
              if ($ldap_connection->IsError()) {
                  $this->EnableLdapError();
              } else {
                  // We continue only if there is no error
                  // Prepare the array "users_in_groups" if we are using a generic LDAP and an LdapInGroup Filtering
                  if (1 != $this->GetLdapServerType()) { // Generic LDAP, eventually no memberOf function like in AD
                      $users_in_groups = array();
                      if ('' != trim($this->GetLdapInGroup())) {
                          $in_groups_array_raw = explode("§",trim(str_replace(",","§",str_replace(";","§",$this->GetLdapInGroup()))));
                          foreach($in_groups_array_raw as $one_group) {
                              $temp_array = $ldap_connection->group_users($one_group);
                              foreach($temp_array as $one_temp) {
                                  $one_user = $this->EncodeForBackend($one_temp);
                                  if (!isset($users_in_groups[$this->$one_user])) {
                                      $users_in_groups[$one_user] = $one_group;
                                  } else {
                                      $users_in_groups[$one_user] = $users_in_groups[$one_user].",".$one_group;
                                  }
                              }
                          }
                      }
                  }
                  $all_results = (isset($users_info['count'])?$users_info['count']:0);
                  for ($results=0; $results < $all_results; $results++) {
                      $accountdisable = FALSE;
                      $groups_array = array();
                      $in_groups_array = array();
                      $in_groups_lower_array = array();
                      $one_user = $users_info[$results];
                      $user = $this->EncodeForBackend(isset($one_user[mb_strtolower($this->GetLdapCnIdentifier())][0])?($one_user[mb_strtolower($this->GetLdapCnIdentifier())][0]):'');
                      $account = $this->EncodeForBackend(isset($one_user[mb_strtolower($this->GetLdapSyncedUserAttribute())][0])?($one_user[mb_strtolower($this->GetLdapSyncedUserAttribute())][0]):'');
                      if (!$this->IsCaseSensitiveUsers()) {
                          $user = mb_strtolower($user);
                          $account = mb_strtolower($account);
                      }

                      if (isset($one_user['useraccountcontrol'][0])) {
                          if (0 != ($one_user['useraccountcontrol'][0] & 2)) {
                              $accountdisable = TRUE;
                          }
                      }
                      if (isset($one_user['ms-ds-user-account-control-computed'][0])) {
                          if (0 != ($one_user['ms-ds-user-account-control-computed'][0] & 16)) {
                              $accountdisable = TRUE;
                          }
                      }
                      if (isset($one_user['accountexpires'][0])) {
                          if (($one_user['accountexpires'][0] > 0) && ((($one_user['accountexpires'][0] / 10000000) - 11644473600) < time())) {
                              $accountdisable = TRUE;
                          }
                      }
                      
                      if (isset($one_user['shadowexpire'][0])) {
                          if (($one_user['shadowexpire'][0] >= 0) && ((86400 * $one_user['shadowexpire'][0]) < time())) {
                              $accountdisable = TRUE;
                          }
                      }
                      if (isset($one_user['sambaacctflags'][0])) {
                          if ((FALSE !== mb_strpos($one_user['sambaacctflags'][0], "D")) || (FALSE !== mb_strpos($one_user['sambaacctflags'][0], "L"))) {
                              $accountdisable = TRUE;
                          }
                      }
                      
                      if (!$accountdisable) {
                          if ('' == trim($this->GetLdapInGroup())) {
                              $in_a_group = TRUE;
                          } else {
                              $in_a_group = FALSE;
                              $in_groups_array_raw = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapInGroup()))));
                              foreach($in_groups_array_raw as $one_group) {
                                  $in_groups_array[] = trim($one_group);
                                  $in_groups_lower_array[] = mb_strtolower(trim($one_group));
                              }
                          }

                          
                          // Generic LDAP, eventually no memberOf function like in AD
                          if (1 != $this->GetLdapServerType()) {
                              if (isset($users_in_groups[$user])) {
                                  $in_a_group = TRUE;
                              }
                          // AD
                          } else {
                              // $groups_array_raw = $ldap_connection->user_groups($user);
                              $groups_array_raw=$ldap_connection->nice_names($one_user[$ldap_connection->_group_attribute]); //presuming the entry returned is our guy (unique usernames)

                              if ($ldap_connection->_recursive_groups) {
                                  foreach ($groups_array_raw as $id => $group_name){
                                      $extra_groups=$ldap_connection->recursive_groups($group_name, $this->IsLdapRecursiveCacheOnly());
                                      if ('' != $ldap_connection->get_warning_message()) {
                                          $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                                      }
                                      if ($this->GetVerboseFlag() && ('' != $ldap_connection->get_debug_message())) {
                                          $this->WriteLog("Debug: *".$ldap_connection->get_debug_message(), FALSE, FALSE, 98, 'LDAP', '');
                                      }
                                      $groups_array_raw=array_merge($groups_array_raw,$extra_groups);
                                  }
                              }
                              
                              $groups_array = array();
                              foreach($groups_array_raw as $one_group) {
                                  $this_group = $this->EncodeForBackend($one_group);
                                  $groups_array[] = $this_group;
                                  if (in_array(mb_strtolower($this_group), $in_groups_lower_array)) {
                                      $in_a_group = TRUE;
                                  }
                              }
                          }

                          if ($in_a_group) {
                              $users_list.= (('' != $users_list)?"\t":'').$account;
                          }
                      }
                  }
              }
          } else {
              $this->EnableLdapError();
              $this->WriteLog("Error: no LDAP binding", FALSE, FALSE, 30, 'LDAP', '');
          }
      } else {
          $this->WriteLog("Error: No LDAP connection information", FALSE, FALSE, 30, 'LDAP', '');
      }
      return $users_list;
  }


  function GetLdapUsersInfoArray(
      $user_filter = "*",
      $include_disabled = TRUE,
      $ignore_in_group = FALSE
  ) {
      $this->DisableLdapError();
      $in_groups_array = array();
      $users_in_groups = array();
      $result_array = array();

      if (!function_exists('ldap_connect')) {
          $result = FALSE;
          $this->WriteLog("Error: LDAP library not installed", FALSE, FALSE, 39, 'System', '', 3);
          $this->EnableLdapError();
      } elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword())) {
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
                                'recursive_groups'   => $this->IsLdapRecursiveGroups(),
                                'time_limit'         => $this->GetLdapTimeLimit(),
                                'use_ssl'            => $this->IsLdapSsl(),
                                'cache_support'      => $this->IsLdapCacheOn(),
                                'cache_folder'       => $this->GetLdapCacheFolder()
                               );

          $ldap_connection=new MultiotpAdLdap($ldap_options);
          if ($this->GetVerboseFlag()) {
              $this->WriteLog("Debug: *AD/LDAP connection defined", FALSE, FALSE, 8888, 'Debug', '');
          }
          if (!$ldap_connection->_bind) {
            $this->WriteLog("Error: AD/LDAP not binded ".$ldap_connection->get_warning_message(), FALSE, FALSE, 30, 'LDAP', '');
          }
          if ($users_info = $ldap_connection->users_info($user_filter, $this->GetLdapFieldsArray())) {
              if ($ldap_connection->IsError()) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Debug: *AD/LDAP error during connection : ".$ldap_connection->ErrorMessage(), FALSE, FALSE, 8888, 'Debug', '');
                  }
                  $this->EnableLdapError();
              } else {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Debug: *AD/LDAP GetLdapUsersInfoArray processing", FALSE, FALSE, 8888, 'Debug', '');
                  }
                  // We continue only if there is no error
                  // Prepare the array "users_in_groups" if we are using a generic LDAP and an LdapInGroup Filtering
                  if (1 != $this->GetLdapServerType()) { // Generic LDAP, eventually no memberOf function like in AD
                      if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Debug: *AD/LDAP server is generic LDAP", FALSE, FALSE, 8888, 'Debug', '');
                      }
                      $users_in_groups = array();
                      if ('' != trim($this->GetLdapInGroup())) {
                          $in_groups_array_raw = explode("§",trim(str_replace(",","§",str_replace(";","§",$this->GetLdapInGroup()))));
                          foreach($in_groups_array_raw as $one_group) {
                              $temp_array = $ldap_connection->group_users($one_group);
                              foreach($temp_array as $one_temp) {
                                  $one_user = $this->EncodeForBackend($one_temp);
                                  if (!isset($users_in_groups[$one_user])) {
                                      $users_in_groups[$one_user] = $one_group;
                                  } else {
                                      $users_in_groups[$one_user] = $users_in_groups[$one_user].",".$one_group;
                                  }
                              }
                          }
                      }
                  } else {
                      if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Debug: *AD/LDAP server is Microsoft AD", FALSE, FALSE, 8888, 'Debug', '');
                      }
                  }
              
                  $all_results = (isset($users_info['count'])?$users_info['count']:0);
                  for ($results=0; $results < $all_results; $results++) {
                      $accountdisable = FALSE;
                      $groups_array = array();
                      $in_groups_array = array();
                      $in_groups_lower_array = array();
                      $one_user = $users_info[$results];
                      $user = $this->EncodeForBackend(isset($one_user[mb_strtolower($this->GetLdapCnIdentifier())][0])?($one_user[mb_strtolower($this->GetLdapCnIdentifier())][0]):'');
                      $account = $this->EncodeForBackend(isset($one_user[mb_strtolower($this->GetLdapSyncedUserAttribute())][0])?($one_user[mb_strtolower($this->GetLdapSyncedUserAttribute())][0]):'');
                      if (!$this->IsCaseSensitiveUsers()) {
                          $user = mb_strtolower($user);
                          $account = mb_strtolower($account);
                      }

                      $one_user['msradiusframedipaddress'][0] = (isset($one_user['msradiusframedipaddress'][0])) ? long2ip32bit($one_user['msradiusframedipaddress'][0]) : "---";
                      $one_user['radiusframedipaddress'][0] = (isset($one_user['radiusframedipaddress'][0])) ? ($one_user['radiusframedipaddress'][0]) : "---";
                      $one_user['radiusframedipnetmask'][0] = (isset($one_user['radiusframedipnetmask'][0])) ? ($one_user['radiusframedipnetmask'][0]) : "---";

                      if (isset($one_user['useraccountcontrol'][0])) {
                          if (0 != ($one_user['useraccountcontrol'][0] & 2)) {
                              $accountdisable = TRUE;
                          }
                      }
                      if (isset($one_user['ms-ds-user-account-control-computed'][0])) {
                          if (0 != ($one_user['ms-ds-user-account-control-computed'][0] & 16)) {
                              $accountdisable = TRUE;
                          }
                      }
                      if (isset($one_user['accountexpires'][0])) {
                          if (($one_user['accountexpires'][0] > 0) && ((($one_user['accountexpires'][0] / 10000000) - 11644473600) < time())) {
                              $accountdisable = TRUE;
                          }
                      }

                      if (isset($one_user['shadowexpire'][0])) {
                          if (($one_user['shadowexpire'][0] >= 0) && ((86400 * $one_user['shadowexpire'][0]) < time())) {
                              $accountdisable = TRUE;
                          }
                      }
                      if (isset($one_user['sambaacctflags'][0])) {
                          if ((FALSE !== mb_strpos($one_user['sambaacctflags'][0], "D")) || (FALSE !== mb_strpos($one_user['sambaacctflags'][0], "L"))) {
                              $accountdisable = TRUE;
                          }
                      }
                      
                      if ($include_disabled || (!$accountdisable)) {
                          if ('' == trim($this->GetLdapInGroup())) {
                              $in_a_group = TRUE;
                          } else {
                              $in_a_group = FALSE;
                              $in_groups_array_raw = explode("§",trim(str_replace(",","§",str_replace(";","§",$this->GetLdapInGroup()))));
                              foreach($in_groups_array_raw as $one_group) {
                                  $in_groups_array[] = trim($one_group);
                                  $in_groups_lower_array[] = mb_strtolower(trim($one_group));
                              }
                          }

                          // Generic LDAP, eventually no memberOf function like in AD
                          if (1 != $this->GetLdapServerType()) {
                              if (isset($users_in_groups[$user])) {
                                  $in_a_group = TRUE;
                              }
                          // AD
                          } else {
                              // $groups_array_raw = $ldap_connection->user_groups($user);
                              $groups_array_raw=$ldap_connection->nice_names($one_user[$ldap_connection->_group_attribute]); //presuming the entry returned is our guy (unique usernames)

                              if ($ldap_connection->_recursive_groups) {
                                  foreach ($groups_array_raw as $id => $group_name){
                                      $extra_groups=$ldap_connection->recursive_groups($group_name, $this->IsLdapRecursiveCacheOnly());
                                      if ('' != $ldap_connection->get_warning_message()) {
                                          $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                                      }
                                      if ($this->GetVerboseFlag() && ('' != $ldap_connection->get_debug_message())) {
                                          $this->WriteLog("Debug: *".$ldap_connection->get_debug_message(), FALSE, FALSE, 98, 'LDAP', '');
                                      }
                                      $groups_array_raw=array_merge($groups_array_raw,$extra_groups);
                                  }
                              }
                              
                              $groups_array = array();
                              foreach($groups_array_raw as $one_group) {
                                  $this_group = $this->EncodeForBackend($one_group);
                                  $groups_array[] = $this_group;
                                  if (in_array(mb_strtolower($this_group), $in_groups_lower_array)) {
                                      $in_a_group = TRUE;
                                  }
                              }
                          }

                          if ($ignore_in_group || $in_a_group) {
                              $result_array[$user]['user'] = $user;
                              $result_array[$user]['groups'] = $groups_array;
                              $result_array[$user]['accountdisable'] = $accountdisable;
                              $result_array[$user]['mail'] = (isset($one_user['mail'][0]) ? $this->EncodeForBackend($one_user['mail'][0]) : "");
                              $result_array[$user]['displayname'] = (isset($one_user['displayname'][0]) ? $this->EncodeForBackend($one_user['displayname'][0]) : "");
                              $result_array[$user]['description'] = (isset($one_user['description'][0]) ? $this->EncodeForBackend($one_user['description'][0]) : "");
                              $result_array[$user]['mobile'] = (isset($one_user['mobile'][0]) ? $this->EncodeForBackend($one_user['mobile'][0]) : "");
                              $result_array[$user]['msnpallowdialin'] = ("TRUE" == (isset($one_user['msnpallowdialin'][0]) ? ($one_user['msnpallowdialin'][0]) : "FALSE"));
                              if ("---" != $one_user['msradiusframedipaddress'][0]) {
                                  $result_array[$user]['msradiusframedipaddress'] = $one_user['msradiusframedipaddress'][0];
                              }
                              if ("---" != $one_user['radiusframedipaddress'][0]) {
                                  $result_array[$user]['radiusframedipaddress'] = $one_user['radiusframedipaddress'][0];
                              }
                              if ("---" != $one_user['radiusframedipnetmask'][0]) {
                                  $result_array[$user]['radiusframedipnetmask'] = $one_user['radiusframedipnetmask'][0];
                              }
                              $result_array[$user]['synchronized_dn'] = (isset($one_user['distinguishedname'][0]) ? $this->EncodeForBackend($one_user['distinguishedname'][0]) : "");
                              $result_array[$user]['language'] = (isset($one_user[mb_strtolower($this->GetLdapLanguageAttribute())][0]) ? $this->EncodeForBackend($one_user[mb_strtolower($this->GetLdapLanguageAttribute())][0]) : "");
                              $result_array[$user]['account'] = $account;
                          }
                      }
                  }
              }
          } else {
              $this->EnableLdapError();
              $this->WriteLog("Error: LDAP connection failed", FALSE, FALSE, 30, 'LDAP', '');
          }
      } else {
          $this->EnableLdapError();
          $this->WriteLog("Error: no LDAP connection information", FALSE, FALSE, 30, 'LDAP', '');
      }

      if ($this->GetVerboseFlag()) {
          $this->WriteLog("Debug: *AD/LDAP GetLdapUsersInfoArray done (".$ldap_connection->ErrorMessage().")", FALSE, FALSE, 8888, 'Debug', '');
      }

      return $result_array;
  }


  function GetUserInfo(
    $user
  ) {
    $crlf = "\n";
    $result = "";
    if ($this->SetUser($user)) {
      $result.= "   Information for user: ".$user.$crlf;
      $result.= "                 Locked: ".((1 == $this->GetUserLocked()) ? 'yes' : 'no').$crlf;
      $result.= "              Activated: ".((1 == $this->GetUserActivated()) ? 'yes' : 'no').$crlf;
      $result.= "   AD/LDAP synchronized: ".((1 == $this->GetUserSynchronized()) ? 'yes' : 'no').$crlf;
      $result.= "      Prefix pin needed: ".((1 == $this->GetUserPrefixPin()) ? 'yes' : 'no').$crlf;
      $result.= "            Description: ".$this->GetUserDescription().$crlf;
      $result.= "                  Email: ".$this->GetUserEmail().$crlf;
      $result.= "           Mobile phone: ".$this->GetUserSms().$crlf;
      $result.= "                  Group: ".$this->GetUserGroup().$crlf;
      if ("" != $this->GetUserLanguage(TRUE)) {
        $result.= "               Language: ".$this->GetUserLanguage(TRUE).$crlf;
      } else {
        $result.= "   Language (inherited): ".$this->GetLanguage().$crlf;
      }
      if ("" != $this->GetUserKeyId()) {
        $result.= "               Token id: ".$this->GetUserKeyId().$crlf;
      }
      if ("" != $this->GetUserTokenSerialNumber()) {
        $result.= "    Token serial number: ".$this->GetUserTokenSerialNumber().$crlf;
      }
      $result.= "              Algorithm: ".$this->GetUserAlgorithm().$crlf;
      $result.= "             OTP digits: ".$this->GetUserTokenNumberOfDigits().$crlf;
      if (('hotp' == $this->GetUserAlgorithm()) || ('yubicootp' == $this->GetUserAlgorithm())) {
        $result.= "    Next token position: ".($this->GetUserTokenLastEvent()+1).$crlf;
      } elseif (('totp' == $this->GetUserAlgorithm()) || ('motp' == $this->GetUserAlgorithm())) {
        $result.= "         Token timestep: ".$this->GetUserTokenTimeInterval().$crlf;
      }
      if (is_valid_ipv4($this->GetUserDialinIpAddress())) {
        $result.= "     Dial-In IP address: ".$this->GetUserDialinIpAddress().$crlf;
      }
      if (is_valid_ipv4($this->GetUserDialinIpMask())) {
        $result.= "        Dial-In IP mask: ".$this->GetUserDialinIpMask().$crlf;
      } elseif (is_valid_ipv4($this->GetDefaultDialinIpMask())) {
        $result.= "Default Dial-In IP mask: ".$this->GetDefaultDialinIpMask().$crlf;
      }
    }
    return $result;
  }


  function TestLdapUser(
      $value
  ) {
      $result = FALSE;
      $user_to_check = ($this->IsCaseSensitiveUsers()?$value:mb_strtolower($value));
      $ldap_users_array = $this->GetLdapUsersInfoArray();
      if (!$this->IsLdapError()) {
          foreach($ldap_users_array as $one_ldap_user) {
              // $user = $one_ldap_user['user'];
              // $user = ($this->IsCaseSensitiveUsers()?$user:mb_strtolower($user));
              $account = $one_ldap_user['account'];
              $account = ($this->IsCaseSensitiveUsers()?$user:mb_strtolower($account));
              if ($user_to_check == $account) {
                  $result = TRUE;
                  break;
              }
          }
      } elseif ($this->GetVerboseFlag()) {
          $this->WriteLog("Debug: *AD/LDAP error before testing the $value account", FALSE, FALSE, 8888, 'Debug', '');
      }
      return $result;
  }


  /**
   * @brief   Synchronize AD/LDAP users
   *
   * @param   string  $user_filter           User name filter (* by default)
   * @param   boolean $include_disabled      Disabled users will also be synced
   * @param   boolean $ignore_in_group       Don't check if the users are in the selected groups or not
   * @param   boolean $state_info_interval   Number of seconds before a new state is given in the lock file, added 2016-11-22
   *
   * @return  boolean                        Function has been successfully called
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 4.3.3.1
   * @date    2015-12-23
   * @since   2014-11-04 (completely redesigned)
   */
  function SyncLdapUsers(
      $user_filter = "*",
      $include_disabled = TRUE,
      $ignore_in_group = FALSE,
      $state_info_interval = 60
  ) {
      $result = FALSE;

      $ldap_sync_stop = FALSE;
      $ldap_sync_file_lock = $this->GetLockFolder().$this->GetLdapSyncLockFileName();
      $ldap_sync_file_stop = $this->GetLockFolder().$this->GetLdapSyncStopFileName();

      $info_interval = intval($state_info_interval);
      if ($info_interval < 1) {
          $info_interval = 0;
      }

      clearstatcache();
      
      if (!function_exists('ldap_connect')) {
          $this->WriteLog("Error: LDAP library not installed", FALSE, FALSE, 39, 'System', '', 3);
          $this->EnableLdapError();
      } elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword())) {

          // Check if an other sync process is already active yet
          if (file_exists($ldap_sync_file_lock)) {
              // If the process is running more than a specific amount of time
              //   without any update, something is probably wrong and we continue!
              if ((filemtime($ldap_sync_file_lock) + $this->GetLockTime()) >= time()) {
                  $additional_info = "";
                  // Take some info from the sync process
                  $additional_info = trim(@file_get_contents($ldap_sync_file_lock));
                  $this->WriteLog("Info: Previous AD/LDAP synchronization in progress... $additional_info", FALSE, FALSE, 19, 'LDAP', '');
                  return TRUE;
              }
          }

          // As the method is just called now, we remove the stop file if any
          // (the stop file is used to cancel the process during the loop)
          if (file_exists($ldap_sync_file_stop)) {
              unlink($ldap_sync_file_stop);
          }

          $start_sync_time = time();

          $last_touch = time();
          if ($lock_handle = @fopen($ldap_sync_file_lock, "wt")) {
              // $additional_info = "started for ".gmdate("H:i:s", time()-$start_sync_time);
              $additional_info = "started at ".date("H:i:s", $start_sync_time);
              if ($this->GetVerboseFlag()) {
                  $additional_info.= " / Memory used: ".(intval(10*memory_get_usage()/(1024*1024))/10)."MB / Peak: ".(intval(10*memory_get_peak_usage()/(1024*1024))/10)."MB";
                  $this->WriteLog("Debug: *AD/LDAP synchronization ".$additional_info, FALSE, FALSE, 8888, 'LDAP', '');
              }
              fwrite($lock_handle,$additional_info);
              fclose($lock_handle);
              if ('' != $this->GetLinuxFileMode()) {
                  @chmod($ldap_sync_file_lock, octdec($this->GetLinuxFileMode()));
              }
          } 
      
          $this->DisableLdapError();
          $in_groups_array = array();

          $this->WriteLog("Info: AD/LDAP synchronization started", FALSE, FALSE, 19, 'LDAP', '');

          // TODO: later, we could loop in several base-dn (semicolon separated)
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
                                'recursive_groups'   => $this->IsLdapRecursiveGroups(),
                                'time_limit'         => $this->GetLdapTimeLimit(),
                                'use_ssl'            => $this->IsLdapSsl(),
                                'cache_support'      => $this->IsLdapCacheOn(),
                                'cache_folder'       => $this->GetLdapCacheFolder()
                               );

          $ldap_connection = new MultiotpAdLdap($ldap_options);

          if ($ldap_connection->IsError()) {
              $this->WriteLog("Error: ".$ldap_connection->ErrorMessage(), FALSE, FALSE, 79, 'LDAP', '');
              $this->EnableLdapError();
          } else {
              // We continue only if there is no error
              // Put all group_cn in cache
              $ldap_connection->group_cn(1, FALSE, TRUE);

              // Put all recursive_groups in cache
              if ($ldap_connection->_recursive_groups) {
                  $all_groups = $ldap_connection->all_groups(FALSE, '*', TRUE, TRUE);
                  reset($all_groups);
                  while(list($key, $one_group) = each($all_groups)) {
                      $ldap_connection->recursive_groups($one_group);
                  }
              }

              $ldap_created_counter = 0;
              $modified_counter = 0;
              $ldap_total_counter = 0;
              $existing_ldap_users_counter = 0;

              $result = TRUE;

              $page_cookie = '';

              $users_in_groups = array();

              if ('' != trim($this->GetLdapInGroup())) {
                  $in_groups_array_raw = explode("§",trim(str_replace(",","§",str_replace(";","§",$this->GetLdapInGroup()))));

                  // Prepare the array "users_in_groups" if we are using a generic LDAP and an LdapInGroup Filtering
                  if (1 != $this->GetLdapServerType()) { // Generic LDAP, eventually no memberOf function like in AD
                      foreach($in_groups_array_raw as $one_group) {
                          $temp_array = $ldap_connection->group_users($one_group);
                          foreach($temp_array as $one_temp) {
                              $one_user = $this->EncodeForBackend($one_temp);
                              if (!isset($users_in_groups[$one_user])) {
                                  $users_in_groups[$one_user] = $one_group;
                              } else {
                                  $users_in_groups[$one_user] = $users_in_groups[$one_user].",".$one_group;
                              }
                          }
                      }
                  }
              } else {
                  $in_groups_array_raw = array();
              }

              do { // ldap pagination loop
                  if (function_exists('ldap_control_paged_result')) {
                      ldap_control_paged_result($ldap_connection->_conn, 1000, false, $page_cookie); // Page size of 1000
                  }

                  $one_user = $ldap_connection->one_user_info(true,
                                                              $user_filter,
                                                              $this->GetLdapFieldsArray(),
                                                              $this->IsLdapCacheOn()
                                                             );
                  if ($ldap_connection->IsError()) {
                      $this->EnableLdapError();
                      $this->WriteLog("Error: LDAP connection failed", false, false, 30, 'LDAP', '');
                      if (file_exists($ldap_sync_file_lock)) {
                          unlink($ldap_sync_file_lock);
                      }
                      return FALSE;
                  }
                  if ('' != $ldap_connection->get_warning_message()) {
                      $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                  }
                  if ($this->GetVerboseFlag() && ('' != $ldap_connection->get_debug_message())) {
                      $this->WriteLog("Debug: *".$ldap_connection->get_debug_message(), FALSE, FALSE, 98, 'LDAP', '');
                  }

                  do {
                      // We check also if we have to stop
                      if (file_exists($ldap_sync_file_stop)) {
                        unlink($ldap_sync_file_stop);
                        $ldap_sync_stop = TRUE;
                      }
                      // We also touch the sync lock every half of the lock time limit, or at each info_interval
                      if ((($last_touch + ($this->GetLockTime() / 2)) <= time()) || (($last_touch + $info_interval) <= time())) {
                          $last_touch = time();
                          if ($lock_handle = @fopen($ldap_sync_file_lock, "wt")) {
                              $additional_info = "started at ".date("H:i:s", $start_sync_time);
                              $additional_info.= ", LDAP account #".($ldap_total_counter+1)." at ".date("H:i:s");
                              if ($this->GetVerboseFlag()) {
                                  $additional_info.= " / Memory used: ".(intval(10*memory_get_usage()/(1024*1024))/10)."MB / Peak: ".(intval(10*memory_get_peak_usage()/(1024*1024))/10)."MB";
                                  $this->WriteLog("Debug: *AD/LDAP synchronization ".$additional_info, FALSE, FALSE, 8888, 'LDAP', '');
                                  print_r($one_user);
                              }
                              fwrite($lock_handle,$additional_info);
                              fclose($lock_handle);
                              if ('' != $this->GetLinuxFileMode()) {
                                  @chmod($ldap_sync_file_lock, octdec($this->GetLinuxFileMode()));
                              }
                          } 
                      }
                      $accountdisable = FALSE;
                      $groups_lower_array = array();
                      $in_groups_array = array();
                      $in_groups_lower_array = array();
                      $group = "";
                      $user_in_groups = '';

                      $ldap_total_counter++;

                      $user = $this->EncodeForBackend(isset($one_user[mb_strtolower($this->GetLdapCnIdentifier())][0])?($one_user[mb_strtolower($this->GetLdapCnIdentifier())][0]):'');
                      $account = $this->EncodeForBackend(isset($one_user[mb_strtolower($this->GetLdapSyncedUserAttribute())][0])?($one_user[mb_strtolower($this->GetLdapSyncedUserAttribute())][0]):'');
                      if (!$this->IsCaseSensitiveUsers()) {
                          $user = mb_strtolower($user);
                          $account = mb_strtolower($account);
                      }
                      if ($account != '') {

                          $one_user['msradiusframedipaddress'][0] = (isset($one_user['msradiusframedipaddress'][0])) ? long2ip32bit($one_user['msradiusframedipaddress'][0]) : "---";
                          $one_user['radiusframedipaddress'][0] = (isset($one_user['radiusframedipaddress'][0])) ? ($one_user['radiusframedipaddress'][0]) : "---";
                          $one_user['radiusframedipnetmask'][0] = (isset($one_user['radiusframedipnetmask'][0])) ? ($one_user['radiusframedipnetmask'][0]) : "---";

                          if (isset($one_user['useraccountcontrol'][0])) {
                              if (0 != ($one_user['useraccountcontrol'][0] & 2)) {
                                  $accountdisable = TRUE;
                              }
                          }
                          if (isset($one_user['ms-ds-user-account-control-computed'][0])) {
                              if (0 != ($one_user['ms-ds-user-account-control-computed'][0] & 16)) {
                                  $accountdisable = TRUE;
                              }
                          }
                          if (isset($one_user['accountexpires'][0])) {
                              if (($one_user['accountexpires'][0] > 0) && ((($one_user['accountexpires'][0] / 10000000) - 11644473600) < time())) {
                                  $accountdisable = TRUE;
                              }
                          }

                          if (isset($one_user['shadowexpire'][0])) {
                              if (($one_user['shadowexpire'][0] >= 0) && ((86400 * $one_user['shadowexpire'][0]) < time())) {
                                  $accountdisable = TRUE;
                              }
                          }
                          if (isset($one_user['sambaacctflags'][0])) {
                              if ((FALSE !== mb_strpos($one_user['sambaacctflags'][0], "D")) || (FALSE !== mb_strpos($one_user['sambaacctflags'][0], "L"))) {
                                  $accountdisable = TRUE;
                              }
                          }
                          if ($include_disabled || (!$accountdisable)) {
                              // TODO $in_a_group discovery
                              if ('' == trim($this->GetLdapInGroup())) {
                                  $in_a_group = TRUE;
                              } else {
                                  $in_a_group = FALSE;
                                  $in_groups_array_raw = explode("§",trim(str_replace(",","§",str_replace(";","§",$this->GetLdapInGroup()))));
                                  foreach($in_groups_array_raw as $one_group) {
                                      $in_groups_array[] = trim($one_group);
                                      $in_groups_lower_array[] = mb_strtolower(trim($one_group));
                                  }

                                  // Generic LDAP, eventually no memberOf function like in AD
                                  if (1 != $this->GetLdapServerType()) {

                                      // Prepare the array "users_in_groups" if we are using a generic LDAP and an LdapInGroup Filtering
                                      if (1 != $this->GetLdapServerType()) { // Generic LDAP, eventually no memberOf function like in AD
                                          foreach($in_groups_array_raw as $one_group) {
                                              $temp_array = $ldap_connection->group_users($one_group);
                                              foreach($temp_array as $one_temp) {
                                                  $one_user = $this->EncodeForBackend($one_temp);
                                                  if ($user == $one_user) {
                                                      $user_in_groups.= (('' != $user_in_groups) ? ',' : '') . $one_group;
                                                      $in_a_group = TRUE;
                                                  }
                                              }
                                          }
                                      }

                                      if ($in_a_group) {
                                          $temp_array = explode(",", $user_in_groups);
                                          $group = $temp_array[0];
                                      }
                                  // AD
                                  } else {
                                      // $groups_array_raw = $ldap_connection->user_groups($user);
                                      $groups_array_raw=$ldap_connection->nice_names($one_user[$ldap_connection->_group_attribute]); //presuming the entry returned is our guy (unique usernames)

                                      if ($ldap_connection->_recursive_groups) {
                                          foreach ($groups_array_raw as $id => $group_name){
                                              $extra_groups=$ldap_connection->recursive_groups($group_name, $this->IsLdapRecursiveCacheOnly());
                                              if ('' != $ldap_connection->get_warning_message()) {
                                                  $this->WriteLog("Warning: ".$ldap_connection->get_warning_message(), FALSE, FALSE, 98, 'LDAP', '');
                                              }
                                              $groups_array_raw=array_merge($groups_array_raw,$extra_groups);
                                          }
                                      }

                                      foreach($groups_array_raw as $one_group) {
                                          $this_group = $this->EncodeForBackend($one_group);
                                          $groups_lower_array[] = mb_strtolower($this_group);
                                      }
                                      
                                      foreach($in_groups_array as $one_filtered_group) {
                                          if (in_array(mb_strtolower($one_filtered_group), $groups_lower_array)) {
                                              $user_in_groups.= (('' != $user_in_groups) ? ',' : '') . $one_filtered_group;
                                              $in_a_group = TRUE;
                                              if ("" == $group) {
                                                  $group = $one_filtered_group;
                                              }
                                          }
                                      }
                                      
                                  }
                              }

                              if ($ignore_in_group || $in_a_group) {
                                  $description = '';
                                  if (isset($one_user['description'][0])) {
                                      $description = trim($one_user['description'][0]);
                                  }
                                  if (('' == $description) && (isset($one_user['gecos'][0]))) {
                                      $description = trim($one_user['gecos'][0]);
                                  }
                                  if (('' == $description) && (isset($one_user['displayname'][0]))) {
                                      $description = trim($one_user['displayname'][0]);
                                  }
                                  if ('' == $description) {
                                      $description = $account;
                                  }

                                  // $user;
                                  $ldap_email = trim(isset($one_user['mail'][0])?$this->EncodeForBackend($one_user['mail'][0]):"");
                                  $ldap_group = trim(('' != $group) ? $group : $this->GetDefaultUserGroup());
                                  if ($this->IsMultipleGroupsEnabled()) {
                                      $ldap_group = trim(('' != $user_in_groups) ? $user_in_groups : $this->GetDefaultUserGroup());
                                  }
                                  $ldap_description = $this->EncodeForBackend($description);
                                  $ldap_sms = (isset($one_user['mobile'][0])?$this->EncodeForBackend($one_user['mobile'][0]):"");
                                  $ldap_msnpallowdialin = ("TRUE" == (isset($one_user['msnpallowdialin'][0])?($one_user['msnpallowdialin'][0]):"FALSE"));
                                  $ldap_enabled = ((!$accountdisable)?1:0);
                                  // TODO CYR $ldap_synchronized_dn = trim(isset($one_user['distinguishedname'][0])?encode_utf8_if_needed($one_user['distinguishedname'][0]):"");
                                  $ldap_synchronized_dn = trim(isset($one_user['distinguishedname'][0])?$this->EncodeForBackend($one_user['distinguishedname'][0]):"");
                                  $ldap_language = mb_strtolower(substr(trim(isset($one_user[mb_strtolower($this->GetLdapLanguageAttribute())][0])?$this->EncodeForBackend($one_user[mb_strtolower($this->GetLdapLanguageAttribute())][0]):""), 0, 2));

                                  $ldap_framedipaddress = (isset($one_user['msradiusframedipaddress'][0]) ? ($one_user['msradiusframedipaddress'][0]) : "---");
                                  if ("---" == $ldap_framedipaddress) {
                                      $ldap_framedipaddress = (isset($one_user['radiusframedipaddress'][0]) ? ($one_user['radiusframedipaddress'][0]) : "---");
                                  }
                                  $ldap_framedipnetmask = (isset($one_user['radiusframedipnetmask'][0]) ? ($one_user['radiusframedipnetmask'][0]) : "---");

                                  if (!$this->CheckUserExists($account, true, true)) { // $no_server_check = TRUE; $no_error = TRUE
                                  // User doesn't exist yet
                                      if ('' == $ldap_description) {
                                          $ldap_description = $account;
                                      }
                                      $result = $this->FastCreateUser($account,
                                                                      $ldap_email,
                                                                      $ldap_sms,
                                                                      -1, // Prefix pin needed
                                                                      $this->GetLdapDefaultAlgorithm(),
                                                                      $ldap_enabled,
                                                                      $ldap_description,
                                                                      $ldap_group,
                                                                      1,  // Synchronized
                                                                      '', // Pin
                                                                      true, // Automatically
                                                                      'LDAP', // Synchronized channel
                                                                      $this->GetLdapDomainControllers(), // Synchronized server
                                                                      $ldap_synchronized_dn,
                                                                      -1, // Set to default value if the user is created  automatically
                                                                      $ldap_language,
                                                                      (("---" != $ldap_framedipaddress) ? $ldap_framedipaddress : "")
                                                                     );
                                      if ($result) {
                                          $this->SyncUserModified(TRUE, $account);
                                          $ldap_created_counter++;
                                      }
                                  } else {
                                  // User already exists
                                      $existing_ldap_users_counter++;
                                      $this->SetUser($account);
                                      if (1 == $this->GetUserSynchronized()) {
                                          $description = $this->GetUserDescription();
                                          $email = $this->GetUserEmail();
                                          $enabled = $this->GetUserActivated();
                                          $group = $this->GetUserGroup();
                                          $sms = $this->GetUserSms();
                                          $synchronized_channel = $this->GetUserSynchronizedChannel();
                                          $synchronized_dn = $this->GetUserSynchronizedDn();
                                          $synchronized_server = $this->GetUserSynchronizedServer();
                                          $language = $this->GetUserLanguage(TRUE);
                                          $dialin_ip_address = $this->GetUserDialinIpAddress();
                                          $dialin_ip_mask = $this->GetUserDialinIpMask();
                                          $modified = FALSE;
                                          $detailed_modif = "";

                                          if (('' != $ldap_description) && ($description != $ldap_description)) {
                                              $this->SetUserDescription($ldap_description);
                                              $modified = TRUE;
                                              $detailed_modif.= "$description-$ldap_description / ";
                                          }
                                          
                                          if (('' != $ldap_email) && ($email != $ldap_email)) {
                                              $this->SetUserEmail($ldap_email);
                                              $modified = TRUE;
                                              $detailed_modif.= "$email-$ldap_email / ";
                                          }

                                          if ($enabled != $ldap_enabled) {
                                              $this->SetUserActivated($ldap_enabled);
                                              $modified = TRUE;
                                              $detailed_modif.= "$enabled-$ldap_enabled / ";
                                          }

                                          if (('' != $ldap_group) && ($group != $ldap_group)) {
                                              $this->SetUserGroup($ldap_group);
                                              $modified = TRUE;
                                              $detailed_modif.= "$group-$ldap_group / ";
                                          }

                                          if (('' != $ldap_sms) && ($sms != $ldap_sms)) {
                                              $this->SetUserSms($ldap_sms);
                                              $modified = TRUE;
                                              $detailed_modif.= "$sms-$ldap_sms / ";
                                          }

                                          if ($synchronized_channel != 'LDAP') {
                                              $this->SetUserSynchronizedChannel('LDAP');
                                              $modified = TRUE;
                                              $detailed_modif.= "$synchronized_channel / ";
                                          }

                                          if ($synchronized_dn != $ldap_synchronized_dn) {
                                              $this->SetUserSynchronizedDn($ldap_synchronized_dn);
                                              $modified = TRUE;
                                              $detailed_modif.= "$synchronized_dn-$ldap_synchronized_dn / ";
                                          }

                                          if (('' != $this->GetLdapDomainControllers()) && ($synchronized_server != $this->GetLdapDomainControllers())) {
                                              $this->SetUserSynchronizedServer($this->GetLdapDomainControllers());
                                              $modified = TRUE;
                                              $detailed_modif.= "$synchronized_server-".$this->GetLdapDomainControllers()." / ";
                                          }

                                          if ($language != $ldap_language) {
                                              $this->SetUserLanguage($ldap_language);
                                              $modified = TRUE;
                                              $detailed_modif.= "$language-$ldap_language / ";
                                          }

                                          if ("---" != $ldap_framedipaddress) {
                                              if ($dialin_ip_address != $ldap_framedipaddress) {
                                                  $this->SetUserDialinIpAddress($ldap_framedipaddress);
                                                  $modified = TRUE;
                                                  $detailed_modif.= "$dialin_ip_address-$ldap_framedipaddress / ";
                                              }
                                          }

                                          if ("---" != $ldap_framedipnetmask) {
                                              if ($dialin_ip_mask != $ldap_framedipnetmask) {
                                                  $this->SetUserDialinIpNetmask($ldap_framedipnetmask);
                                                  $modified = TRUE;
                                                  $detailed_modif.= "$dialin_ip_address-$ldap_framedipnetmask / ";
                                              }
                                          }

                                          if ($this->IsOverwriteRequestLdapPwd()) {
                                            // We set to the default value for LDAP password if the user is updated by synchronization
                                            if ($this->GetUserRequestLdapPassword() != $this->GetDefaultRequestLdapPwd()) {
                                              $this->SetUserRequestLdapPassword($this->GetDefaultRequestLdapPwd());
                                              $modified = TRUE;
                                            }
                                          }

                                          $this->SetUserSynchronizedTime();
                                          
                                          $this->WriteUserData(TRUE, $modified); // $automatically = TRUE, $update_last_change = $modified
                                          if ($modified) {
                                              if ($this->GetVerboseFlag()) {
                                                  $this->WriteLog("Debug: *AD/LDAP $account modified: $detailed_modif", FALSE, FALSE, 8888, 'Debug', '');
                                              }
                                              $this->SyncUserModified(FALSE, $account);
                                              $modified_counter++;
                                          }
                                      }
                                  }
                              }
                          }
                      } // if ($account != '')
                  } while ((!$ldap_sync_stop) && ($one_user = $ldap_connection->one_user_info(FALSE, NULL, NULL, TRUE))); // $group_cn_cache_only = TRUE
                  // Loop of LDAP parsing and synchronization

                  if (function_exists('ldap_control_paged_result_response')) {
                      ldap_control_paged_result_response($ldap_connection->_conn, $ldap_connection->_oui_sr, $page_cookie);
                  }
              } while ((!$ldap_sync_stop) && ($page_cookie !== null) && ($page_cookie != ''));
              // ldap pagination loop

              if (function_exists('ldap_control_paged_result')) {
                  // Reset LDAP paged result
                  ldap_control_paged_result($ldap_connection->_conn, 1000, false);
              }

              if (!$ldap_sync_stop) {
                  // Loop on all existing users to disable the "not-synchronized-yet" synchronized users
                  // TODO: cached users information could be updated during this time !
                  $internal_users_loop = 0;
                  $one_user = $this->GetNextUserArray(TRUE);
                  do {
                      // We also touch the sync lock every half of the lock time limit
                      //   and we check also if we have to stop now
                      if (($last_touch + ($this->GetLockTime() / 2)) <= time()) {
                          $last_touch = time();
                          if ($lock_handle = @fopen($ldap_sync_file_lock, "wt")) {
                              $additional_info = "started at ".date("H:i:s", $start_sync_time);
                              $additional_info.= ", internal account #".($internal_users_loop+1)." at ".date("H:i:s");
                              if ($this->GetVerboseFlag()) {
                                  $additional_info.= " / Memory used: ".(intval(10*memory_get_usage()/(1024*1024))/10)."MB / Peak: ".(intval(10*memory_get_peak_usage()/(1024*1024))/10)."MB";
                                  $this->WriteLog("Debug: *AD/LDAP synchronization ".$additional_info, FALSE, FALSE, 8888, 'LDAP', '');
                              }
                              fwrite($lock_handle,$additional_info);
                              fclose($lock_handle);
                              if ('' != $this->GetLinuxFileMode()) {
                                  @chmod($ldap_sync_file_lock, octdec($this->GetLinuxFileMode()));
                              }
                          } 
                          if (file_exists($ldap_sync_file_stop)) {
                            unlink($ldap_sync_file_stop);
                            $ldap_sync_stop = TRUE;
                          }
                      }

                      if (isset($one_user['user'])) {
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

                          if (($modified_synchronized) && ($modified_synchronized_time < $start_sync_time)) {
                              $existing_ldap_users_counter++;
                              // The existing user is enabled and marked as synchronized but is not in the external database/LDAP
                              if ($modified_enabled) {
                                  $this->SetUser($modified_user);
                                  $this->SetUserActivated(0);
                                  $this->WriteUserData(TRUE);
                                  $modified_counter++;
                              }
                          }
                      }
                      $internal_users_loop++;
                  } while (($one_user = $this->GetNextUserArray()) && (!$ldap_sync_stop));
                  
                  if ($ldap_sync_stop) {
                      $this->WriteLog("Info: LDAP sync stopped", FALSE, FALSE, 19, 'LDAP', '');
                  } else {
                      $time_info = gmdate("H:i:s", time()-$start_sync_time);
                      $info_txt = '';
                      
                      if ($modified_counter > 0) {
                          $ldap_counter_suffix = ((1 < $modified_counter)?'s':'');
                          $info_txt.= $modified_counter." user$ldap_counter_suffix updated, based on $ldap_total_counter LDAP entries";
                      }
                      if ($ldap_created_counter > 0) {
                          if ('' != $info_txt) {
                              $info_txt.= ', ';
                          }
                          $ldap_counter_suffix = ((1 < $ldap_created_counter)?'s':'');
                          $info_txt.= $ldap_created_counter." user$ldap_counter_suffix created, based on $ldap_total_counter LDAP entries";
                      }
                      if ('' == $info_txt) {
                          $info_txt = "No update for the $existing_ldap_users_counter LDAP synced users, based on $ldap_total_counter LDAP entries";
                      }
                      $this->WriteLog("Info: $info_txt (processed in $time_info)", FALSE, FALSE, 19, 'LDAP', '');
                  }
              } else {
                  $this->WriteLog("Info: LDAP sync stopped", FALSE, FALSE, 19, 'LDAP', '');
              }
          } // We have done this loop only if there was no error before
      // End of successful LDAP parameters
      } else {
          $this->EnableLdapError();
          $this->WriteLog("Error: no LDAP connection information", FALSE, FALSE, 30, 'LDAP', '');
      }
      if (file_exists($ldap_sync_file_lock)) {
          unlink($ldap_sync_file_lock);
      }
      return $result;
  }


  // It's possible to overload this stub in order to do something when the current user is modified (or created)
  function SyncUserModified(
      $created = FALSE,
      $user = ''
  ) {
      return TRUE;
  }


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
  function CheckLdapAuthentication()
  {
      $result = FALSE;
      if (!function_exists('ldap_connect')) {
          $this->WriteLog("Error: LDAP library not installed", FALSE, FALSE, 39, 'System', '', 3);
      } elseif (('' != $this->GetLdapDomainControllers()) && ('' != $this->GetLdapBindDn()) && ('' != $this->GetLdapServerPassword())) {
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
                                'recursive_groups'   => $this->IsLdapRecursiveGroups(),
                                'time_limit'         => $this->GetLdapTimeLimit(),
                                'use_ssl'            => $this->IsLdapSsl()
                               );

          if (!defined('LDAP_OPT_DIAGNOSTIC_MESSAGE')) {
            define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
          }
          
          $domain_controllers = explode(" ",trim(str_replace(","," ",str_replace(";"," ",$this->GetLdapDomainControllers()))));
          mt_srand(doubleval(microtime()) * 100000000); // for older php versions
          $domain_controller = ($domain_controllers[array_rand($domain_controllers)]);

          foreach($domain_controllers as $dc) {
              $port = $this->GetLdapPort();
              $controller = $dc;
              $protocol = "ldap://";
              if ($this->IsLdapSsl()) {
                  $protocol = "ldaps://";
              }
              $pos = mb_strpos($dc, "://");
              if ($pos !== FALSE) {
                  $protocol = substr($dc, 0, $pos+3);
                  $dc = substr($dc, $pos+3);
              }
              $pos = mb_strpos($dc, ":");
              if ($pos !== FALSE) {
                  $port = substr($dc, $pos+1);
                  $dc = substr($dc, 0, $pos);
              }

              /* DEBUG
              echo "DEBUG PROTOCOL: ".$protocol.$dc.":".$port."\n";
              */
              
              /*
              if ($this->GetVerboseFlag()) {
                  ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
              }
              */
              
              if ($ldapconn = @ldap_connect($protocol.$dc.":".$port)) {
                  ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                  ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
                  if (@ldap_bind($ldapconn, ($this->GetLdapBindDn().$this->GetLdapAccountSuffix()), ($this->GetLdapServerPassword()))) {
                      /*
                      if ($this->GetVerboseFlag()) {
                          echo "DEBUG\n";
                          if (ldap_get_option($ldapconn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
                              echo "Error Binding to LDAP: $extended_error";
                          } else {
                              echo "Error Binding to LDAP: No additional information is available.";
                          }
                      }
                      */
                      $result = TRUE;
                  } else {
                      if ($this->GetVerboseFlag()) {
                          // echo "DEBUG LDAP: ".ldap_error($ldapconn);
                          $this->WriteLog("DEBUG: LDAP: ".ldap_error($ldapconn));
                      }
                  }
                  @ldap_unbind($ldapconn);
              }
              if ($result) {
                  break;
              }
          }
      }
      return $result;
  }


  function SetTokenDataReadFlag(
      $flag
  ) {
      $this->_token_data_read_flag = $flag;
  }


  function GetTokenDataReadFlag()
  {
      return $this->_token_data_read_flag;
  }


  function SetBaseDir(
      $base_dir
  ) {
      $this->_base_dir = $this->ConvertToUnixPath($base_dir);
  }


  function GetBaseDir()
  {
      return ($this->_base_dir);
  }


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
  function GetScriptFolder()
  {
      if ('' != $this->GetBaseDir()) {
          $current_script_folder_detected = $this->ConvertToUnixPath($this->GetBaseDir());
      } else {
          $current_script_folder_detected = $this->ConvertToUnixPath(dirname(__FILE__));
      }

      if (substr($current_script_folder_detected,-1) != "/") {
          $current_script_folder_detected.="/";
      }
      return $this->ConvertToWindowsPathIfNeeded($current_script_folder_detected);
  }


  function ConvertToUnixPath(
      $path
  ) {
      return str_replace("\\","/",$path);
  }


  function ConvertToWindowsPathIfNeeded(
      $path
  ) {
      $result = $path;
      if (FALSE !== mb_strpos($result,":")) {
          $result = str_replace("/","\\",$result);
      }
      return $result;
  }


  function GetReplyMessageForRadius()
  {
      return (isset($this->_reply_array_for_radius[0]) ? $this->_reply_array_for_radius[0] : '');
  }


  function SetReplyMessageForRadius(
      $value
  ) {
      $this->_reply_array_for_radius = array();
      $this->AddReplyArrayMessageForRadius($value);
  }


  function GetReplyArrayForRadius()
  {
      return $this->_reply_array_for_radius;
  }


  function AddReplyArrayForRadius(
      $value
  ) {
      $this->_reply_array_for_radius[] = $value;
  }


  // Adding extra information for the result (if any)
  function AddExtraRadiusInfo($options = array())
  {
    if (isset($options['multiple_groups'])) {
      $multiple_groups = (isset($options['multiple_groups']) ? (TRUE == $options['multiple_groups']) : FALSE);
    } else {
      $multiple_groups = $this->IsMultipleGroupsEnabled();
    }

    $group = trim($this->GetUserGroup());
    if (('' != $group) && ('' != $this->GetGroupAttribute())) {
      $group_array = explode("§",trim(str_replace(",","§",str_replace(";","§",$group))));
      if ($multiple_groups) {
        foreach($group_array as $one_group) {
          if ("" != trim($one_group)) {
            $this->AddReplyArrayForRadius($this->GetGroupAttribute().$this->GetRadiusReplyAttributor().'"'.$one_group.'"');
          }
        }
        if ($this->GetVerboseFlag()) {
          $this->AddReplyArrayForRadius($this->GetGroupAttribute().$this->GetRadiusReplyAttributor().'"'."multiotp-debug-group".'"');
        }
      } else {
        $this->AddReplyArrayForRadius($this->GetGroupAttribute().$this->GetRadiusReplyAttributor().'"'.$group_array[0].'"');
      }
    }

    if (('' != $this->GetLastClearOtpValue()) && ('' != $this->GetClearOtpAttribute())) {
      $this->AddReplyArrayForRadius($this->GetClearOtpAttribute().$this->GetRadiusReplyAttributor().'"'.$this->GetLastClearOtpValue().'"');
    }

    $dialin_ip_address = trim($this->GetUserDialinIpAddress());
    if (is_valid_ipv4($dialin_ip_address)) {
      $dialin_ip_mask = trim($this->GetUserDialinIpMask());
      if (!is_valid_ipv4($dialin_ip_mask)) {
        $dialin_ip_mask = $this->GetDefaultDialinIpMask();
      }
      // IP address and netmask
      $this->AddReplyArrayForRadius('Framed-IP-Address'.$this->GetRadiusReplyAttributor().''.$dialin_ip_address.'');
      if (is_valid_ipv4($dialin_ip_mask)) {
        $this->AddReplyArrayForRadius('Framed-IP-Netmask'.$this->GetRadiusReplyAttributor().''.$dialin_ip_mask.'');
      }
    }
  }


  function SetVerboseLogPrefix(
      $value
  ) {
      $this->_config_data['verbose_log_prefix'] = $value;
  }


  function GetVerboseLogPrefix()
  {
      return $this->_config_data['verbose_log_prefix'];
  }


  function SetAttributesToEncrypt(
      $attributes_to_encrypt
  ) {
      $attributes = trim($attributes_to_encrypt);
      if (('' != $attributes) && ('*' == substr($attributes,0,1)) && ('*' == substr($attributes,-1))) {
          $this->_attributes_to_encrypt = $attributes;
      }
  }


  function GetAttributesToEncrypt()
  {
      return $this->_attributes_to_encrypt;
  }



  function SetUsersFolder(
      $folder,
      $create = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_users_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing users folder ".$new_folder, FALSE, FALSE, 28, 'System', '');
          }
      }
  }


  function GetUsersFolder()
  {
      if ('' == $this->_users_folder) {
          $this->SetUsersFolder($this->GetScriptFolder()."users/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_users_folder);
  }


  function SetDevicesFolder(
      $folder,
      $create = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_devices_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing devices folder ".$new_folder, FALSE, FALSE, 28, 'System', '');
          }
      }
  }


  function GetDevicesFolder()
  {
      if ('' == $this->_devices_folder) {
          $this->SetDevicesFolder($this->GetScriptFolder()."devices/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_devices_folder);
  }


  function SetQrCodeFolder(
      $folder
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_qrcode_folder = $new_folder;
  }


  function GetQrCodeFolder()
  {
      if ('' == $this->_qrcode_folder) {
          $this->SetQrCodeFolder($this->GetScriptFolder()."qrcode/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_qrcode_folder);
  }


  function SetTemplatesFolder(
      $folder
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_templates_folder = $new_folder;
  }


  function GetTemplatesFolder()
  {
      if ('' == $this->_templates_folder) {
          $this->SetTemplatesFolder($this->GetScriptFolder()."templates/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_templates_folder);
  }


  function SetGroupsFolder(
      $folder,
      $create = true
  ) {
      $new_folder = $this->ConvertToUnixPath($folder);
      if (substr($new_folder,-1) != "/") {
          $new_folder.="/";
      }
      if ("/" == $new_folder) {
        $new_folder = "./";
      }
      $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
      $this->_groups_folder = $new_folder;
      if ($create && (!file_exists($new_folder))) {
          if (!@mkdir(
                  $new_folder,
                  ('' != $this->GetLinuxFolderMode()) ? octdec($this->GetLinuxFolderMode()) : 0777,
                  true //recursive
          )) {
              $this->WriteLog("Error: Unable to create the missing groups folder ".$new_folder, FALSE, FALSE, 28, 'System', '');
          }
      }
  }


  function GetGroupsFolder()
  {
      if ('' == $this->_groups_folder) {
          $this->SetGroupsFolder($this->GetScriptFolder()."groups/");
      }
      return $this->ConvertToWindowsPathIfNeeded($this->_groups_folder);
  }


  function SendSms(
      $sms_recipient,
      $sms_message_to_send,
      $real_user = '',
      $originator = '',
      $provider = '',
      $userkey = '',
      $password = '',
      $api_id = '',
      $write_log = TRUE,
      $source_tag = ''
  ) {
      $sms_number = $this->CleanPhoneNumber($sms_recipient);

      $result = 62; // ERROR: SMS provider not supported
      
      $sms_originator = (('' != $originator)?$originator:$this->GetSmsOriginator());
      $sms_provider = mb_strtolower((('' != $provider)?$provider:$this->GetSmsProvider()));
      $sms_userkey = (('' != $userkey)?$userkey:$this->GetSmsUserkey());
      $sms_password = (('' != $password)?$password:$this->GetSmsPassword());
      $sms_api_id = (('' != $api_id)?$api_id:$this->GetSmsApiId());
     
      if ("aspsms" == $sms_provider) {
          $sms_message = new MultiotpAspSms($sms_userkey, $sms_password);
          $sms_message->setOriginator($sms_originator);
          $sms_message->setRecipient($sms_number);
          $sms_message->setContent($sms_message_to_send); // Decoding to UTF8 if needed is done in the MultiotpAspSms class
          $sms_result = intval($sms_message->sendSMS());
          
          if (1 != $sms_result) {
              $result = 61; // ERROR: SMS code request received, but an error occurred during transmission
              if ($write_log) {
                  $this->WriteLog("Error: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"").", but the ".$sms_provider." error ".$sms_result." occurred during transmission to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
              }
          } else {
              $result = 18; // INFO: SMS code request received
              if ($write_log) {
                  $this->WriteLog("Info: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"")." and sent via ".$sms_provider." to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
              }
          }
      } elseif ("clickatell" == $sms_provider) {
          $sms_message = new MultiotpClickatell($sms_userkey, $sms_password, $sms_api_id);
          $sms_message->useRegularServer();
          $sms_message->setOriginator($sms_originator);
          $sms_message->setRecipient($sms_number);
          $sms_message->setContent(encode_utf8_if_needed($sms_message_to_send));
          $sms_result = intval($sms_message->sendSMS());
          
          if (1 != $sms_result) {
              $result = 61; // ERROR: SMS code request received, but an error occurred during transmission
              if ($write_log) {
                  $this->WriteLog("Error: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"").", but the ".$sms_provider." error ".$sms_result." occurred during transmission to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
      if ($this->GetVerboseFlag()) {
          $this->WriteLog("DEBUG: *Sent to server: ".encode_utf8_if_needed($sms_message_to_send));
          $this->WriteLog("DEBUG: *Received from server: ".$sms_message->getReply());
      }
              }
          } else {
              $result = 18; // INFO: SMS code request received
              if ($write_log) {
                  $this->WriteLog("Info: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"")." and sent via ".$sms_provider." to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
              }
          }
      } elseif ("intellisms" == $sms_provider) {
          $sms_message = new MultiotpIntelliSms($sms_userkey, $sms_password);
          $sms_message->useRegularServer();
          $sms_message->setOriginator($sms_originator);
          $sms_message->setRecipient($sms_number);
          $sms_message->setContent(encode_utf8_if_needed($sms_message_to_send));
          $sms_result = $sms_message->sendSMS();
          
          if ("ID" != substr($sms_result,0,2)) {
              $result = 61; // ERROR: SMS code request received, but an error occurred during transmission
              if ($write_log) {
                  $this->WriteLog("Error: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"").", but the ".$sms_provider." error ".$sms_result." occurred during transmission to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
              }
          } else {
              $result = 18; // INFO: SMS code request received
              if ($write_log) {
                  $this->WriteLog("Info: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"")." and sent via ".$sms_provider." to ".$sms_number, FALSE, FALSE, $result, 'SMS', $real_user);
              }
          }
      } elseif ("exec" == $sms_provider) {
          $exec_cmd = $sms_api_id;
          $exec_cmd = str_replace('%from', $sms_originator, $exec_cmd);
          $exec_cmd = str_replace('%to',  $sms_number,  $exec_cmd);
          $exec_cmd = str_replace('%msg',  encode_utf8_if_needed($sms_message_to_send),  $exec_cmd);
          exec($exec_cmd, $output);
          $result = 18; // INFO: SMS code request received
          if ($write_log) {
              $this->WriteLog("Info: SMS code request received for ".$real_user.(("" != $source_tag)?" for $source_tag":"")." and sent via ".$exec_cmd, FALSE, FALSE, $result, 'SMS', $real_user);
          }
      } else {
          $result = 62; // ERROR: SMS provider not supported
          if ($write_log) {
              $this->WriteLog("Error: SMS provider ".$sms_provider." not supported".(("" != $source_tag)?" for $source_tag":""), FALSE, FALSE, $result, 'SMS', $real_user);
          }
      }
      return $result;
  }


  function GenerateSmsToken(
      $user = ''
  ) {
      $result = 99;
      $now_epoch = time();
      if ('' != $user) {
          $this->SetUser($user);
      } else {
          $user = $this->GetUser();
      }
      $sms_number = $this->CleanPhoneNumber($this->GetUserSms());
      if ('' != $sms_number) {
          $sms_message_prefix = trim($this->GetSmsMessage());
          $sms_now_steps = $now_epoch;
          $sms_digits = $this->GetSmsDigits();
          $sms_seed_bin = hex2bin(md5('sMs'.$this->GetEncryptionKey().$this->GetUserTokenSeed().$user.$now_epoch));
          $sms_token = $this->GenerateOathHotp($sms_seed_bin,$sms_now_steps,$sms_digits);
          $this->SetUserSmsOtp($sms_token);
          $this->SetUserSmsValidity($now_epoch + $this->GetSmsTimeout());

          $sms_nice_token = $this->ConvertToNiceToken($sms_token);
          
          if (FALSE !== mb_strpos($sms_message_prefix, '%s')) {
              $sms_message_to_send = sprintf($sms_message_prefix, $sms_nice_token);
          } else {
              $sms_message_to_send = $sms_message_prefix.' '.$sms_nice_token;
          }

          $result = $this->SendSms($sms_number, $sms_message_to_send, $user);
      } else {
          $result = 60; // ERROR: no information on where to send SMS code
          $this->WriteLog("Error: no information on where to send SMS code for ".$real_user, FALSE, FALSE, $result, 'SMS', $real_user);
      }
      $this->WriteUserData();
      return $result;
  }


  function ConvertToNiceToken(
      $regular_token
  ) {
      $token_length = strlen($regular_token);
      if (9 <= $token_length) {
          $sms_nice_token = substr($regular_token,0,3).'-'.substr($regular_token,3,3).'-'.substr($regular_token,6,($token_length-6));
      } elseif (6 < $token_length) {
          $sms_nice_token = substr($regular_token,0,intval($token_length/2)).'-'.substr($regular_token,intval($token_length/2),$token_length);
      } else {
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
                           $hardware_tokens_list = ''
  ) {
      $the_hardware_tokens_list = $hardware_tokens_list;
      if ('' != $user) {
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
                       $hardware_tokens_list = ''
  ) {
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
  function CheckUserToken(
      $function_array = array('user' => ''),
      $input_param = '',
      $input_sync_param = '',
      $display_status_param = FALSE,
      $ignore_lock_param = FALSE,
      $resync_enc_pass_param = FALSE,
      $no_server_check_param = FALSE,
      $self_register_serial_param = '',
      $hardware_tokens_list_param = ''
  ) {
      if (is_array($function_array)) {
          $user = isset($function_array['user'])?$function_array['user']:'';
          $input = isset($function_array['input'])?$function_array['input']:'';
          $input_sync = isset($function_array['input_sync'])?$function_array['input_sync']:'';
          $display_status = isset($function_array['display_status'])?$function_array['display_status']:FALSE;
          $ignore_lock = isset($function_array['ignore_lock'])?$function_array['ignore_lock']:FALSE;
          $resync_enc_pass = isset($function_array['resync_enc_pass'])?$function_array['resync_enc_pass']:FALSE;
          $no_server_check = isset($function_array['no_server_check'])?$function_array['no_server_check']:FALSE;
          $self_register_serial = isset($function_array['self_register_serial'])?$function_array['self_register_serial']:'';
          $hardware_tokens_list = isset($function_array['hardware_tokens_list'])?$function_array['hardware_tokens_list']:'';
      } else { // backward compatibility
          $user = $function_array;
          $input = $input_param;
          $input_sync = $input_sync_param;
          $display_status = $display_status_param;
          $ignore_lock = $ignore_lock_param;
          $resync_enc_pass = $resync_enc_pass_param;
          $no_server_check = $no_server_check_param;
          $self_register_serial = $self_register_serial_param;
          $hardware_tokens_list = $hardware_tokens_list_param;
      }
  
      if ('' != $user) {
          $this->SetUser($user);
          $hardware_tokens_list = $this->GetUserTokenSerialNumber();
      }
      return $this->CheckToken(array('user' => $user,
                                     'input' => $input,
                                     'input_sync' => $input_sync,
                                     'display_status' => $display_status,
                                     'ignore_lock' => $ignore_lock,
                                     'resync_enc_pass' => $resync_enc_pass,
                                     'no_server_check' => $no_server_check,
                                     'self_register_serial' => $self_register_serial,
                                     'hardware_tokens_list' => $hardware_tokens_list));
  }


  /**
   * @brief   Check the token of the actual user and give the result, with resync options.
   *
   * @param   string  $input_array           Array with token to check and other fields, or token to check as a string
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
   * @version 4.3.4.3
   * @date    2016-06-08
   * @since   2010-06-07
   */
  function CheckToken(
      $input_array = array('input' => ''),
      $input_sync_param = '',
      $display_status_param = FALSE,
      $ignore_lock_param = FALSE,
      $resync_enc_pass_param = FALSE,
      $no_server_check_param = FALSE,
      $self_register_serial_param = '',
      $hardware_tokens_list_param = ''
  ) {
      $now_epoch = time();

      if (is_array($input_array)) {
          $input = isset($input_array['input'])?$input_array['input']:'';
          $input_sync = isset($input_array['input_sync'])?$input_array['input_sync']:'';
          $display_status = isset($input_array['display_status'])?$input_array['display_status']:FALSE;
          $ignore_lock = isset($input_array['ignore_lock'])?$input_array['ignore_lock']:FALSE;
          $resync_enc_pass = isset($input_array['resync_enc_pass'])?$input_array['resync_enc_pass']:FALSE;
          $no_server_check = isset($input_array['no_server_check'])?$input_array['no_server_check']:FALSE;
          $self_register_serial = isset($input_array['self_register_serial'])?$input_array['self_register_serial']:'';
          $hardware_tokens_list = isset($input_array['hardware_tokens_list'])?$input_array['hardware_tokens_list']:'';
          $no_increment_error = isset($input_array['no_increment_error'])?$input_array['no_increment_error']:FALSE;
      } else {
          $input = $input_array; // backward compatibility
          $input_sync = $input_sync_param;
          $display_status = $display_status_param;
          $ignore_lock = $ignore_lock_param;
          $resync_enc_pass = $resync_enc_pass_param;
          $no_server_check = $no_server_check_param;
          $self_register_serial = $self_register_serial_param;
          $hardware_tokens_list = $hardware_tokens_list_param;
          // New parameters, only available in the array
          $no_increment_error = FALSE;
      }
  
      $cache_result_enabled = false;
      $disable_error_counter = false;
      $force_no_prefix_pin = false;

      // Specific device detection, based on the source tag, to check if cache is enabled
      $source_tag = trim($this->GetSourceTag());
      if ('' != $source_tag) {
          if ("" != $this->GetRadiusTagPrefix()) {
              $device_id = $this->GetRadiusTagPrefix().$source_tag;
          } else {
              $device_id = substr($source_tag, strrpos('-'.$source_tag, '-'));
          }
          if ($this->ReadDeviceData($device_id)) {
              $cache_result_enabled = $this->IsDeviceCacheResultEnabled();
              $force_no_prefix_pin = $this->IsDeviceForceNoPrefixEnabled();
          }
      }

      $ldap_check_passed = FALSE;

      $this->SendWeeklyAnonymousStat();

      $this->SetLastClearOtpValue();
      $calculated_token = '';

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
          $this->WriteLog("Info: User ".$real_user." successfully logged in using an external server", FALSE, FALSE, $result, 'User');
      } elseif (18 == $server_result) {
          $result = 18; // ERROR: User doesn't exist. (on the server)
          $this->WriteLog("Info: SMS code request received and sent for ".$real_user." to ".$this->CleanPhoneNumber($this->GetUserSms()), FALSE, FALSE, $result, 'SMS', $real_user);
      } elseif ((21 == $server_result) && (!$this->IsKeepLocal())) {
          $this->DeleteUser($real_user, TRUE); // $no_error_info = TRUE
          $result = 21; // ERROR: User doesn't exist. (on the server)
          $this->WriteLog("Error: User ".$real_user." doesn't exist", FALSE, FALSE, $result, 'User');
      } elseif ((($server_result >= 0) && (22 <= $server_result) && (70 > $server_result)) || (90 <= $server_result)) {
          // We want to stop only if it's an error (but not -1), except if the user doesn't exist (>= 22), if it's a 7x (server) or 8x (cache) error
          $result = $server_result;
          // Already logged using CheckUserTokenOnServer
          // $this->WriteLog("Error: server sent back the error ".$server_result, FALSE, FALSE, $result, 'Server', '');
      } elseif (!$this->ReadUserData($real_user, FALSE, TRUE)) {
          // LOCALLY ONLY
          $result = 21; // ERROR: User doesn't exist.
          $this->WriteLog("Error: User ".$real_user." doesn't exist", FALSE, FALSE, $result, 'User');
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
                  if ($this->CheckTokenExists($self_register_serial, false)) {
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
                                  if ($this->CheckTokenExists($check_serial, false)) {
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
                  // Self registered access cannot be cached for special device
                  if (0 == $result) {
                      // Self registered access is not cached for cache enabled device
                      return $result;
                  }
              }
          }

          // From here now, we know already which user we are testing exactly,
          // and also if a serial number is defined (and the input to check has been recalculated).
          // TODO: Without serial number we have to check with all tokens attributed to this user
          
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
              $ldap_to_check = '!LDAP_FALSE!';
              
              // AD/LDAP case
              if (((1 == $this->GetUserPrefixPin()) && (!$force_no_prefix_pin)) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                  $code_confirmed = $this->GetUserSmsOtp();
                  $this->SetLastClearOtpValue($code_confirmed);
                  $code_to_check = substr($input_to_check, -strlen($code_confirmed));
                  $ldap_to_check = substr($input_to_check, 0, strlen($input_to_check) - strlen($code_to_check));
                  if ($code_to_check === $code_confirmed) {
                      if (('' != $ldap_to_check) && ($this->CheckUserLdapPassword($this->GetUserSynchronizedDn(), $ldap_to_check))) {
                          $ldap_check_passed = TRUE;
                          if ($this->IsCacheLdapHash()) {
                              // The LDAP password is stored in a cache
                              $this->SetUserLdapHashCache(bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check))));
                          }
                      } elseif ($this->IsCacheLdapHash()) {
                          if (!$this->IsLdapServerReachable()) {
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Debug: *user LDAP hash password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                              }
                              if ($this->GetUserLdapHashCache() === bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                  $ldap_check_passed = TRUE;
                                  if ($this->GetVerboseFlag()) {
                                      $this->WriteLog("Debug: *user LDAP hash password verified, based on cached hash password", FALSE, FALSE, 8888, 'Debug', '');
                                  }
                              } else {
                                  if ($this->GetVerboseFlag()) {
                                      $this->WriteLog("Debug: *user LDAP hash password verification failed", FALSE, FALSE, 8888, 'Debug', '');
                                  }
                              }
                          } else {
                              $ldap_check_passed = FALSE;
                              $ldap_to_check = '!LDAP_FALSE!';
                              $this->ResetUserLdapHashCache();
                              $this->WriteLog("Error: User $real_user verification failed, unreachable LDAP/AD server(s)", FALSE, FALSE, 99, 'User');
                          }
                      }
                  }
              } else {
                  // It is a real prefix pin, not an LDAP/AD prefix
                  $code_confirmed = (((1 == $this->GetUserPrefixPin()) && (!$force_no_prefix_pin))?$this->GetUserPin():'').$this->GetUserSmsOtp();
                  $this->SetLastClearOtpValue($code_confirmed);
                  if ('' != $this->GetChapPassword()) {
                      $code_confirmed = $this->CalculateChapPassword($code_confirmed);
                  } elseif ('' != $this->GetMsChapResponse()) {
                      $code_confirmed = $this->CalculateMsChapResponse($code_confirmed);
                  } elseif ('' != $this->GetMsChap2Response()) {
                      $clear_code_confirmed = $code_confirmed;
                      $code_confirmed = $this->CalculateMsChap2Response($real_user, $code_confirmed);
                      if ($this->GetVerboseFlag()) {
                        $this->WriteLog("Debug: *CalculateMsChap2Response($real_user, $clear_code_confirmed) for SMS: $code_confirmed", false, false, 19, 'Debug', '');
                      }
                  }
              }

              if ($ldap_check_passed || ($input_to_check === $code_confirmed)) {
                  $this->SetUserSmsOtp(md5($this->GetEncryptionKey().mt_rand(100000,999999).$this->GetUserTokenSeed().$now_epoch)); // Now SMS code is no more available, and the next one is difficult to guess ;-)
                  $this->SetUserSmsValidity($now_epoch); // And the validity time is set to the successful login time

                  // We are unlocking the user if needed
                  $this->SetUserErrorCounter(0);
                  $this->SetUserLocked(0);
                  // Finally, we update the last login of the user
                  $this->SetUserLastLogin($now_epoch);
                  $this->SetUserTokenLastLogin($now_epoch);
                  $result = 0; // OK: This is the correct SMS token

                  if ($cache_result_enabled) {
                      $this->SetUserLastCachedCredential(trim($input.' '.$input_sync));
                      $this->SetUserLastLoginForCache($now_epoch);
                  }
                  $this->SetUserLastSuccessCredential(trim($input.' '.$input_sync));

                  if (!$this->WriteUserData()) {
                      $result = 28; // ERROR: Unable to write the changes in the file
                      $this->WriteLog("Error: Unable to write the changes in the file for the user ".$real_user, FALSE, FALSE, $result, 'User');
                  } else {
                      $this->WriteLog("Ok: User ".$real_user." successfully logged in with SMS token", FALSE, FALSE, $result, 'User');
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
              if (((1 == $this->GetUserPrefixPin()) && (!$force_no_prefix_pin)) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                  $ldap_check_passed = FALSE;
                  $ldap_to_check = '!LDAP_FALSE!';

                  $code_confirmed = $one_password;
                  $this->SetLastClearOtpValue($code_confirmed);
                  $code_to_check = substr($input_to_check, -strlen($code_confirmed));
                  $ldap_to_check = substr($input_to_check, 0, strlen($input_to_check) - strlen($code_to_check));
                  
                  if ($code_to_check === $code_confirmed) {
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
                                  $this->WriteLog("Debug: *user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                              }
                              if ($this->GetUserLdapHashCache() === bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                  $ldap_check_passed = TRUE;
                                  // TODO Write a specific message in the log
                              }
                          } else {
                              $ldap_check_passed = FALSE;
                              $ldap_to_check = '!LDAP_FALSE!';
                              $this->ResetUserLdapHashCache();
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Debug: *user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                              }
                          }
                      }
                  }
              } else {
                  // It is a real prefix pin, not an LDAP/AD prefix
                  $code_confirmed = (((1 == $this->GetUserPrefixPin()) && (!$force_no_prefix_pin))?$this->GetUserPin():'').$one_password;
                  $this->SetLastClearOtpValue($code_confirmed);
                  if ('' != $this->GetChapPassword()) {
                      $code_confirmed = $this->CalculateChapPassword($code_confirmed);
                  } elseif ('' != $this->GetMsChapResponse()) {
                      $code_confirmed = $this->CalculateMsChapResponse($code_confirmed);
                  } elseif ('' != $this->GetMsChap2Response()) {
                      $clear_code_confirmed = $code_confirmed;
                      $code_confirmed = $this->CalculateMsChap2Response($real_user, $code_confirmed);
                      if ($this->GetVerboseFlag()) {
                        $this->WriteLog("Debug: *CalculateMsChap2Response($real_user, $clear_code_confirmed) for scratch password: $code_confirmed", false, false, 19, 'Debug', '');
                      }
                  }
              }
              
              if ($ldap_check_passed || ($input_to_check === $code_confirmed)) {
                  // We are unlocking the regular token if needed
                  $this->SetUserErrorCounter(0);
                  $this->SetUserLocked(0);
                  // Finally, we update the last login of the user
                  $this->SetUserLastLogin($now_epoch);
                  $this->SetUserTokenLastLogin($now_epoch);
                  $this->RemoveUserUsedScratchPassword($one_password);
                  $result = 0; // OK: This is the correct scratch token

                  if ($cache_result_enabled) {
                      $this->SetUserLastCachedCredential(trim($input.' '.$input_sync));
                      $this->SetUserLastLoginForCache($now_epoch);
                  }
                  $this->SetUserLastSuccessCredential(trim($input.' '.$input_sync));

                  if (!$this->WriteUserData()) {
                      $result = 28; // ERROR: Unable to write the changes in the file
                      $this->WriteLog("Error: Unable to write the changes in the file for the user ".$real_user, FALSE, FALSE, $result, 'User');
                  } else {
                      $this->WriteLog("Ok: User ".$real_user." successfully logged in with a scratch password", FALSE, FALSE, $result, 'User');
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
              $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
              $code_confirmed_upper = mb_strtoupper($this->CalculateChapPassword($code_confirmed_upper));
              $code_confirmed_camel = mb_strtoupper($this->CalculateChapPassword($code_confirmed_camel));
          } elseif ('' != $this->GetMsChapResponse()) {
              $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
              $code_confirmed_upper = mb_strtoupper($this->CalculateMsChapResponse($code_confirmed_upper));
              $code_confirmed_camel = mb_strtoupper($this->CalculateMsChapResponse($code_confirmed_camel));
          } elseif ('' != $this->GetMsChap2Response()) {
              $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
              $code_confirmed_upper = mb_strtoupper($this->CalculateMsChap2Response($real_user, $code_confirmed_upper));
              $code_confirmed_camel = mb_strtoupper($this->CalculateMsChap2Response($real_user, $code_confirmed_camel));
          }
          
          // If something like 'sms' or 'SMS' is detected, we generate an SMS token
          if ((mb_strtolower($input_to_check) === $code_confirmed) || (mb_strtoupper($input_to_check) === $code_confirmed_upper) || (mb_strtoupper($input_to_check) === $code_confirmed_camel)) {
              return $this->GenerateSmsToken();
          }

          // Cached result support
          if ($cache_result_enabled) {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Debug: *code to check: ".str_repeat('x', (strlen($input_to_check) >= 6)?strlen($input_to_check)-6:0).substr($input_to_check, -6), FALSE, FALSE, 8888, 'Debug', '');
              }
              if ($this->CompareUserLastCachedCredential(trim($input.' '.$input_sync))) {
                  if (($this->GetUserLastLoginForCache() + $this->GetDeviceCacheTimeout()) >= $now_epoch) {
                      $this->SetLastClearOtpValue(substr($input_to_check, 0, mb_strpos($input_to_check.' ', ' ')));
                      $result = 0; // OK: This is the correct token for cached access, no update of the user record
                      if ($this->GetVerboseFlag()) {
                          $this->WriteLog("Debug: *User ".$real_user." successfully confirmed for cached access", FALSE, FALSE, 8888, 'Debug', '');
                          $this->WriteLog("Debug: *checked code for cache access: ".str_repeat('x', (strlen($input_to_check) >= 6)?strlen($input_to_check)-6:0).substr($input_to_check, -6), FALSE, FALSE, 8888, 'Debug', '');
                      }
                      $this->AddExtraRadiusInfo();
                      return $result;
                  } elseif (($this->GetUserLastLoginForCache() + $this->GetDeviceCacheTimeout() + 86400) >= $now_epoch) {
                      // During one day, no increase of the error counter on a cache enabled device if code is reused
                      if ($this->CompareUserLastCachedCredential(trim($input.' '.$input_sync))) {
                          $disable_error_counter = true;
                      }
                  }
              }
          }


          // TODO check multiple tokens (loop)

          $pin               = $this->GetUserPin();
          $need_prefix       = (1 == $this->GetUserPrefixPin()) && (!$force_no_prefix_pin);
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
          $private_id        = $this->GetUserTokenPrivateId();
          $delta_time        = $this->GetUserTokenDeltaTime();
          $time_interval     = $this->GetUserTokenTimeInterval();
          $token_algo_suite  = $this->GetUserTokenAlgoSuite();

          $interval = (0 >= $time_interval)?1:$time_interval;

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
              } elseif ($prefix_pin === substr($input_to_check, 0, strlen($prefix_pin))) {
                      $separator_pos = strrpos($input_to_check, ' ');
                      $input_sync = str_replace($prefix_pin, '', substr($input_to_check, $separator_pos+1));
                      $input_to_check = substr($input_to_check, 0, $separator_pos);
              }
          }


          if ((1 == $this->GetUserLocked()) && ('' == $input_sync) && (!$resync_enc_pass) && (!$ignore_lock)) {
              $result = 24; // ERROR: User locked;
              $this->WriteLog("Error: User ".$real_user." locked after ".$this->GetUserErrorCounter()." failed authentications", FALSE, FALSE, $result, 'User');
          } elseif(($this->GetUserErrorCounter() >= $this->GetMaxDelayedFailures()) && ('' == $input_sync) && ($now_epoch < ($this->GetUserTokenLastError() + $this->GetMaxDelayedTime())) && (!$ignore_lock)) {
              $result = 25; // ERROR: User delayed;
              $delayed_time = ($this->GetUserTokenLastError() + $this->GetMaxDelayedTime()) - $now_epoch;
              $this->WriteLog("Error: User ".$real_user." still delayed for ".$delayed_time." seconds after ".$this->GetUserErrorCounter()." failed authentications", FALSE, FALSE, $result, 'User');
          } else {
              $ldap_check_passed = FALSE;
              $ldap_to_check = '!LDAP_FALSE!';
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
                                      $this->WriteLog("Debug: *user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                                  }
                                  if ($this->GetUserLdapHashCache() === bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                      $ldap_check_passed = TRUE;
                                      // TODO Write a specific message in the log
                                  }
                              } else {
                                  $ldap_check_passed = FALSE;
                                  $ldap_to_check = '!LDAP_FALSE!';
                                  $this->ResetUserLdapHashCache();
                                  if ($this->GetVerboseFlag()) {
                                      $this->WriteLog("Debug: *user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                                  }
                              }
                          }
                      }
                  }
                  if (!$ldap_check_passed) {
                      $input_to_check = "LDAP_FAILED_".$input_to_check.'_LDAP_FAILED';
                      $result = 99;
                  }
              }
              
              switch (mb_strtolower($this->GetUserAlgorithm())) {
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
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
                              } elseif ('' != $this->GetMsChapResponse()) {
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
                              } elseif ('' != $this->GetMsChap2Response()) {
                                  $clear_code_confirmed = $code_confirmed;
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                                  if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: *CalculateMsChap2Response($real_user, $clear_code_confirmed) for motp: $code_confirmed", false, false, 19, 'Debug', '');
                                  }
                              }
                          }
                          
                          if (('' == $input_sync) && (!$resync_enc_pass)) {
                              // With mOTP, the code should not be prefixed, so we accept of course always input without prefix!
                              if (($input_to_check === $code_confirmed) || ($input_to_check === $code_confirmed_without_pin)) {
                                  if ($input_to_check === $code_confirmed_without_pin) {
                                      $code_confirmed = $code_confirmed_without_pin;
                                  }
                                  if (($now_steps+$additional_step+$delta_step) > $last_login_step) {
                                      $this->SetUserLastLogin($now_epoch);
                                      $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                      $this->SetUserTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                      $this->SetUserErrorCounter(0);
                                      $result = 0; // OK: This is the correct token
                                      $this->WriteLog("Ok: User ".$real_user." successfully logged in with mOTP token", FALSE, FALSE, $result, 'User');
                                  } else {
                                      $result = 26; // ERROR: this token has already been used
                                      if ($this->CompareUserLastSuccessCredential(trim($input.' '.$input_sync))) {
                                          $disable_error_counter = true;
                                      }
                                      if (!$disable_error_counter) {
                                          $this->SetUserErrorCounter($error_counter+1);
                                      }
                                      $this->SetUserTokenLastError($now_epoch);
                                      $this->WriteLog("Error: token of user ".$real_user." already used", FALSE, FALSE, $result, 'User');
                                  }
                              } else {
                                  $check_step++;
                              }
                          } elseif (($input_to_check === $code_confirmed) || ($input_to_check === $code_confirmed_without_pin)) {
                              $pure_sync_calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step+1, $digits);
                              $sync_calculated_token = $pure_sync_calculated_token;
                              
                              if (($need_prefix) && ($input_sync != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                  $input_sync = substr($input_sync, -strlen($code_confirmed));                            
                              } elseif ($need_prefix) {
                                  $sync_calculated_token = $pin.$sync_calculated_token;
                              }
                              if ((($input_sync === $sync_calculated_token) || ($input_sync === $pure_sync_calculated_token)) && (($now_steps+$additional_step+$delta_step+1) > $last_login_step)) {
                                  $this->SetUserLastLogin($now_epoch);
                                  $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step+1) * $interval);
                                  $this->SetUserTokenDeltaTime(($additional_step+$delta_step+1) * $interval);
                                  $this->SetUserErrorCounter(0);
                                  $this->SetUserLocked(0);
                                  $result = 14; // INFO: token is now synchronized
                                  $this->WriteLog("Info: token for user ".$real_user." is now resynchronized with a delta of ".(($additional_step+$delta_step+1) * $interval). " seconds", FALSE, FALSE, $result, 'User');
                                  $result = 0; // INFO: authentication is successful, regardless of the PIN code if needed, as the PIN code is already used to generate the token
                              } else {
                                  $result = 27; // ERROR: resync failed
                                  $this->WriteLog("Error: resync for user ".$real_user." has failed", FALSE, FALSE, $result, 'User');
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
                          if ($this->CompareUserLastFailedCredential(trim($input.' '.$input_sync))) {
                              $disable_error_counter = true;
                          }
                          if (!$disable_error_counter) {
                              $this->SetUserErrorCounter($error_counter+1);
                          }
                          $this->SetUserTokenLastError($now_epoch);
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
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
                              } elseif ('' != $this->GetMsChapResponse()) {
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
                              } elseif ('' != $this->GetMsChap2Response()) {
                                  $clear_code_confirmed = $code_confirmed;
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                                  if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: *CalculateMsChap2Response($real_user, $clear_code_confirmed) for hotp: $code_confirmed", false, false, 19, 'Debug', '');
                                  }
                              }
                          }
                          
                          if (('' == $input_sync) && (!$resync_enc_pass)) {
                              if ($input_to_check === $code_confirmed) {
                                  if ($additional_step >= 1) {
                                      $this->SetUserLastLogin($now_epoch);
                                      $this->SetUserTokenLastLogin($now_epoch);
                                      $this->SetUserTokenLastEvent($last_event+$additional_step);
                                      $this->SetUserErrorCounter(0);
                                      $result = 0; // OK: This is the correct token
                                      $this->WriteLog("OK: User ".$real_user." successfully logged in with HOTP token", FALSE, FALSE, $result, 'User');
                                  } else {
                                      $result = 26; // ERROR: this token has already been used
                                      if ($this->CompareUserLastSuccessCredential(trim($input.' '.$input_sync))) {
                                          $disable_error_counter = true;
                                      }
                                      if (!$disable_error_counter) {
                                          $this->SetUserErrorCounter($error_counter+1);
                                      }
                                      $this->SetUserTokenLastError($now_epoch);
                                      $this->WriteLog("Error: token of user ".$real_user." already used", FALSE, FALSE, $result, 'User');
                                  }
                              } else {
                                  $check_step++;
                              }
                          } elseif (($input_to_check === $code_confirmed) || ($input_to_check === $code_confirmed_without_pin)) {
                              $pure_sync_calculated_token = $this->GenerateOathHotp($seed_bin, $last_event+$additional_step+1,$digits,$token_algo_suite);
                              $sync_calculated_token = $pure_sync_calculated_token;
                              
                              if (($need_prefix) && ($input_sync != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                  $input_sync = substr($input_sync, -strlen($code_confirmed));                            
                              } elseif ($need_prefix) {
                                  $sync_calculated_token = $pin.$sync_calculated_token;
                              }
                              if ((($input_sync === $sync_calculated_token) || ($input_sync === $pure_sync_calculated_token)) && ($additional_step >= 1)) {
                                  $this->SetUserLastLogin($now_epoch);
                                  $this->SetUserTokenLastLogin($now_epoch);
                                  $this->SetUserTokenLastEvent($last_event+$additional_step+1);
                                  $this->SetUserErrorCounter(0);
                                  $this->SetUserLocked(0);
                                  $result = 14; // INFO: token is now synchronized
                                  $this->WriteLog("Info: token for user ".$real_user." is now resynchronized with the last event ".($last_event+$additional_step+1), FALSE, FALSE, $result, 'User');
                                  if ($input_to_check === $code_confirmed) {
                                      $result = 0; // INFO: authentication is successful, as the prefix has also been typed (if any)
                                  }
                              } else {
                                  $result = 27; // ERROR: resync failed
                                  $this->WriteLog("Error: resync for user ".$real_user." has failed", FALSE, FALSE, $result, 'User');
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
                          if ($this->CompareUserLastFailedCredential(trim($input.' '.$input_sync))) {
                              $disable_error_counter = true;
                          }
                          if (!$disable_error_counter) {
                              $this->SetUserErrorCounter($error_counter+1);
                          }
                          $this->SetUserTokenLastError($now_epoch);
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
                          // $uid = bin2hex(substr($decrypted_part,  0, 6));
                          $result = $yubikey_class->CheckYubicoOtp(substr($input_to_check, -32),
                                                                   $seed,
                                                                   $last_event,
                                                                   $private_id);
                      }

                      if (0 == $result) {
                          $calculated_token = $input_to_check;
                          $this->SetUserLastLogin($now_epoch);
                          $this->SetUserTokenLastLogin($now_epoch);
                          $this->SetUserTokenLastEvent($yubikey_class->GetYubicoOtpLastCount());
                          $this->SetUserErrorCounter(0);
                          $result = 0; // OK: This is the correct token
                          $this->WriteLog("OK: User ".$real_user." successfully logged in with YubicoOTP token", FALSE, FALSE, $result, 'User');
                      } elseif (26 == $result) {
                          $result = 26; // ERROR: this token has already been used
                          if ($this->CompareUserLastSuccessCredential(trim($input.' '.$input_sync))) {
                              $disable_error_counter = true;
                          }
                          if (!$disable_error_counter) {
                              $this->SetUserErrorCounter($error_counter+1);
                              // Was previously simply $this->SetUserErrorCounter(1);
                          }
                          $this->SetUserTokenLastError($now_epoch);
                          $this->WriteLog("Error: token of user ".$real_user." already used", FALSE, FALSE, $result, 'User');
                      } else {
                          if ($this->CompareUserLastFailedCredential(trim($input.' '.$input_sync))) {
                              $disable_error_counter = true;
                          }
                          if (!$disable_error_counter) {
                              $this->SetUserErrorCounter($error_counter+1);
                          }
                          $this->SetUserTokenLastError($now_epoch);
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
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
                              } elseif ('' != $this->GetMsChapResponse()) {
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
                              } elseif ('' != $this->GetMsChap2Response()) {
                                  $clear_code_confirmed = $code_confirmed;
                                  $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed_without_pin));
                                  $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($real_user, $code_confirmed));
                                  if ($this->GetVerboseFlag()) {
                                    $this->WriteLog("Debug: *CalculateMsChap2Response($real_user, $clear_code_confirmed) for totp: $code_confirmed", false, false, 19, 'Debug', '');
                                  }
                              }
                          }
                          
                          if (('' == $input_sync) && (!$resync_enc_pass)) {
                              if ($input_to_check === $code_confirmed) {
                                  if (($now_steps+$additional_step+$delta_step) > $last_login_step) {
                                      $this->SetUserLastLogin($now_epoch);
                                      $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                      $this->SetUserTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                      $this->SetUserErrorCounter(0);
                                      $result = 0; // OK: This is the correct token
                                      $this->WriteLog("OK: User ".$real_user." successfully logged in with TOTP token", FALSE, FALSE, $result, 'User');
                                  } else {
                                      $result = 26; // ERROR: this token has already been used
                                      if ($this->CompareUserLastSuccessCredential(trim($input.' '.$input_sync))) {
                                          $disable_error_counter = true;
                                      }
                                      if (!$disable_error_counter) {
                                          $this->SetUserErrorCounter($error_counter+1);
                                      }
                                      $this->SetUserTokenLastError($now_epoch);
                                      $this->WriteLog("Error: token of user ".$real_user." already used", FALSE, FALSE, $result, 'User');
                                  }
                              } else {
                                  $check_step++;
                              }
                          } elseif (($input_to_check === $code_confirmed) || ($input_to_check === $code_confirmed_without_pin)) {
                              $pure_sync_calculated_token = $this->GenerateOathHotp($seed_bin,$now_steps+$additional_step+$delta_step+1,$digits,$token_algo_suite);
                              $sync_calculated_token = $pure_sync_calculated_token;
                              
                              if (($need_prefix) && ($input_sync != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                  $input_sync = substr($input_sync, -strlen($code_confirmed));                            
                              } elseif ($need_prefix) {
                                  $sync_calculated_token = $pin.$sync_calculated_token;
                              }
                              if ((($input_sync === $sync_calculated_token) || ($input_sync === $pure_sync_calculated_token)) && (($now_steps+$additional_step+$delta_step) > $last_login_step)) {
                                  $this->SetUserLastLogin($now_epoch);
                                  $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step+1) * $interval);
                                  $this->SetUserTokenDeltaTime(($additional_step+$delta_step+1) * $interval);
                                  $this->SetUserErrorCounter(0);
                                  $this->SetUserLocked(0);
                                  $result = 14; // INFO: token is now synchronized
                                  $this->WriteLog("Info: token for user ".$real_user." is now resynchronized with a delta of ".(($additional_step+$delta_step+1) * $interval). " seconds", FALSE, FALSE, $result, 'User');
                                  if ($input_to_check === $code_confirmed) {
                                      $result = 0; // INFO: authentication is successful, as the prefix has also been typed (if any)
                                  }
                              } else {
                                  $result = 27; // ERROR: resync failed
                                  $this->WriteLog("Error: resync for user ".$real_user." has failed", FALSE, FALSE, $result, 'User');
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
                          if ($this->CompareUserLastFailedCredential(trim($input.' '.$input_sync))) {
                              $disable_error_counter = true;
                          }
                          if (!$disable_error_counter) {
                              $this->SetUserErrorCounter($error_counter+1);
                          }
                          $this->SetUserTokenLastError($now_epoch);
                      }
                      break;
                  default:
                      $result = 23;
                      $this->WriteLog("Error: ".$this->GetUserAlgorithm()." algorithm is unknown", FALSE, FALSE, $result, 'User');
              }
              if (90 <= $result) {
                  if ($cache_result_enabled && ((strlen($input_to_check) != strlen($calculated_token)))) {
                      $disable_error_counter = true;
                  }
                  if (!$disable_error_counter) {
                      $this->SetUserErrorCounter($error_counter+1);
                  }
                  $this->SetUserTokenLastError($now_epoch);
              }
          }

          if (0 == $result) {
              $this->SetUserLocked(0);
          }
          
          if (90 <= $result) {
              $this->WriteLog("Error: authentication failed for user ".$real_user, FALSE, FALSE, $result, 'User');
              if ($this->GetVerboseFlag()) {
                  if ('' != $this->GetChapPassword()) {
                      $this->WriteLog("Info: *(authentication typed by the user is CHAP encrypted)", FALSE, FALSE, $result, 'User');
                  } elseif ('' != $this->GetMsChapResponse()) {
                      $this->WriteLog("Info: *(authentication typed by the user is MS-CHAP encrypted)", FALSE, FALSE, $result, 'User');
                  } elseif ('' != $this->GetMsChap2Response()) {
                      $this->WriteLog("Info: *(authentication typed by the user is MS-CHAP V2 encrypted)", FALSE, FALSE, $result, 'User');
                  } elseif ((strlen($input_to_check) === strlen($calculated_token))) {
                      $this->WriteLog("Info: *(authentication typed by the user: ".$input_to_check.")", FALSE, FALSE, $result, 'User');
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

          if (0 == $result) {
              if ($cache_result_enabled) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Debug: *checked code for future cache access: ".str_repeat('x', (strlen($input_to_check) >= 6)?strlen($input_to_check)-6:0).substr($input_to_check, -6), FALSE, FALSE, 8888, 'Debug', '');
                  }
                  $this->SetUserLastCachedCredential(trim($input.' '.$input_sync));
                  $this->SetUserLastLoginForCache($now_epoch);
              }
              $this->SetUserLastSuccessCredential(trim($input.' '.$input_sync));
          } else {
              $this->SetUserLastFailedCredential(trim($input.' '.$input_sync));
          }

          if (!$this->WriteUserData()) {
              $result = 28; // ERROR: Unable to write the changes in the file
              $this->WriteLog("Error: Unable to write the changes in the file for the user ".$real_user, FALSE, FALSE, $result, 'User');
          }
      } // end of the else block of the test: if (!$this->ReadUserData($real_user))

      if (0 == $result) {
          $this->AddExtraRadiusInfo();
      }
      return $result;
  }


  function SelfRegisterHardwareToken(
      $user,
      $serial,
      $input,
      $original_input = ''
  ) {
      // TODO the whole process has to be changed to support multi tokens
      $result = 99; // Unknown error
      
      $cache_result_enabled = false;
      $disable_error_counter = false;
      $force_no_prefix_pin = false;

      // Specific device detection, based on the source tag, to check if cache is enabled
      $source_tag = trim($this->GetSourceTag());
      if ('' != $source_tag) {
          if ("" != $this->GetRadiusTagPrefix()) {
              $device_id = $this->GetRadiusTagPrefix().$source_tag;
          } else {
              $device_id = substr($source_tag, strrpos('-'.$source_tag, '-'));
          }
          if ($this->ReadDeviceData($device_id)) {
              $cache_result_enabled = $this->IsDeviceCacheResultEnabled();
              $force_no_prefix_pin = $this->IsDeviceForceNoPrefixEnabled();
          }
      }

      $ldap_check_passed = FALSE;
      
      $calculated_token = '';
      if ('' == $original_input) {
          $original_input = $input;
      }
      $serial_number = mb_strtolower($serial);
      if ($this->ReadUserData($user)) {
          $pin = $this->GetUserPin();
          $need_prefix = (1 == $this->GetUserPrefixPin()) && (!$force_no_prefix_pin);

          if ($this->ReadTokenData($serial_number)) {
              $attributed_users = trim($this->GetTokenAttributedUsers());
              if ('' != trim($attributed_users)) {
                  if (FALSE === mb_strpos(','.$attributed_users.',', ','.$user.',')) {
                      $result = 37; // ERROR: Token already attributed
                      $this->WriteLog("Error: Token ".$this->GetToken()." already attributed", FALSE, FALSE, $result, 'Token', $user);
                  }
                  // else $result = 99; // The token is already attributed to this user, stay neutral with the error
              } else {
                  $algorithm = $this->GetTokenAlgorithm();
                  $token_algo_suite = $this->GetTokenAlgoSuite();

                  $seed = $this->GetTokenSeed();
                  $seed_bin = hex2bin($seed);
                  $private_id = $this->GetUserTokenPrivateId();
                  $digits = $this->GetTokenNumberOfDigits();
                  $time_interval = $this->GetTokenTimeInterval();
                  $last_event = $this->GetTokenLastEvent();
                  $delta_time = $this->GetTokenDeltaTime();
                  $last_login = $this->GetTokenLastLogin();
                  $error_counter = $this->GetTokenErrorCounter();

                  $now_epoch = time();

                  $input_to_check = $input;
                  $interval = (0 >= $time_interval)?1:$time_interval;

                  if (strlen($input_to_check) < 3) {
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
                  $ldap_to_check = '!LDAP_FALSE!';
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
                                          $this->WriteLog("Debug: *user LDAP password checked in the cache", FALSE, FALSE, 8888, 'Debug', '');
                                      }
                                      if ($this->GetUserLdapHashCache() === bin2hex($this->NtPasswordHashHash($this->NtPasswordHash($ldap_to_check)))) {
                                          $ldap_check_passed = TRUE;
                                          // TODO Write a specific message in the log
                                      }
                                  } else {
                                      $ldap_check_passed = FALSE;
                                      $ldap_to_check = '!LDAP_FALSE!';
                                      $this->ResetUserLdapHashCache();
                                      if ($this->GetVerboseFlag()) {
                                          $this->WriteLog("Debug: *user LDAP password false, hash cache cleared", FALSE, FALSE, 8888, 'Debug', '');
                                      }
                                  }
                              }
                          }
                      }
                      if (!$ldap_check_passed) {
                          $this->WriteLog("Error: authentication failed for user ".$this->GetUser(), FALSE, FALSE, $result, 'User');
                          $input_to_check = "LDAP_FAILED_".$input_to_check.'_LDAP_FAILED';
                          $result = 99;
                      }
                  }

                  switch (mb_strtolower($algorithm)) {
                      case 'motp':
                          $max_steps = 2 * $step_sync_window;
                          $check_step = 1;
                          do {
                              $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                              $pure_calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step, $digits);
                              $calculated_token = $pure_calculated_token;
                              
                              if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                  $code_confirmed_without_pin = $calculated_token;
                                  $code_confirmed = $calculated_token;
                                  $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                  if (!$ldap_check_passed) {
                                      $input_to_check.= '_BAD_LDAP_CHECK';
                                  }
                                  $this->SetLastClearOtpValue($original_input);
                              } else {
                                  if ($need_prefix) {
                                      $calculated_token = $pin.$calculated_token;
                                  }
                                  
                                  $code_confirmed_without_pin = $pure_calculated_token;
                                  $code_confirmed = $calculated_token;
                                  $this->SetLastClearOtpValue($original_input);
                                  if ('' != $this->GetChapPassword()) {
                                      $code_confirmed_without_pin = mb_strtolower($this->CalculateChapPassword($code_confirmed_without_pin));
                                      $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
                                  } elseif ('' != $this->GetMsChapResponse()) {
                                      $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChapResponse($code_confirmed_without_pin));
                                      $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
                                  } elseif ('' != $this->GetMsChap2Response()) {
                                      $code_confirmed_without_pin = mb_strtolower($this->CalculateMsChap2Response($user, $code_confirmed_without_pin));
                                      $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($user, $code_confirmed));
                                  }
                              }

                              if (($input_to_check === $code_confirmed) || ($input_to_check === $code_confirmed_without_pin)) {
                                  if (($now_steps+$additional_step+$delta_step) > $last_login_step) {
                                      $this->SetTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                      $this->SetTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                      $this->SetTokenErrorCounter(0);
                                      $result = 0; // OK: This is the correct token
                                  } else {
                                      $result = 26; // ERROR: this token has already been used
                                  }
                              } else {
                                  $check_step++;
                              }
                          } while (($check_step < $max_steps) && (90 <= $result));
                          break;
                      case 'hotp';
                          $max_steps = $event_sync_window;
                          $check_step = 1;
                          do {
                              $pure_calculated_token = $this->GenerateOathHotp($seed_bin,$last_event+$check_step,$digits,$token_algo_suite);
                              $calculated_token = $pure_calculated_token;
                              
                              if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                  $code_confirmed_without_pin = $calculated_token;
                                  $code_confirmed = $calculated_token;
                                  $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                  if (!$ldap_check_passed) {
                                      $input_to_check.= '_BAD_LDAP_CHECK';
                                  }
                                  $this->SetLastClearOtpValue($original_input);
                              } else {
                                  if ($need_prefix) {
                                      $calculated_token = $pin.$calculated_token;
                                  }
                                  
                                  $code_confirmed = $calculated_token;
                                  $this->SetLastClearOtpValue($original_input);
                                  if ('' != $this->GetChapPassword()) {
                                      $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
                                  } elseif ('' != $this->GetMsChapResponse()) {
                                      $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
                                  } elseif ('' != $this->GetMsChap2Response()) {
                                      $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($user, $code_confirmed));
                                  }
                              }

                              if ($input_to_check === $code_confirmed) {
                                  $this->SetTokenLastLogin($now_epoch);
                                  $this->SetTokenLastEvent($last_event+$check_step);
                                  $this->SetTokenErrorCounter(0);
                                  $result = 0; // OK: This is the correct token
                              } else {
                                  $check_step++;
                              }
                          } while (($check_step < $max_steps) && (90 <= $result));
                          break;
                      case 'yubicootp':
                          $yubikey_class = new MultiotpYubikey();
                          $bad_precheck = FALSE;
                          if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                              if (!$ldap_check_passed) {
                                  $input_to_check.= '_BAD_LDAP_CHECK';
                                  $bad_precheck = TRUE;
                              }
                              $this->SetLastClearOtpValue($original_input);
                          } else {
                              if ($need_prefix) {
                                  if ($pin != substr($input_to_check, 0, strlen($pin))) {
                                      $this->SetLastClearOtpValue($original_input);
                                      $input_to_check.= '_BAD_PREFIX';
                                      $bad_precheck = TRUE;
                                  }
                              }
                          }

                          if (!$bad_precheck) {
                              // Check only the last 32 digits, the first 12 are the serial number
                              // $uid = bin2hex(substr($decrypted_part,  0, 6));
                              $result = $yubikey_class->CheckYubicoOtp(substr($input_to_check, -32),
                                                                       $seed,
                                                                       $last_event,
                                                                       $private_id);
                          }
                          if (0 == $result) {
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
                          do {
                              $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                              $pure_calculated_token = $this->GenerateOathHotp($seed_bin,$now_steps+$additional_step+$delta_step,$digits,$token_algo_suite);
                              $calculated_token = $pure_calculated_token;
                              
                              if (($need_prefix) && ($input_to_check != '') && ($this->IsUserRequestLdapPasswordEnabled())) {
                                  $code_confirmed_without_pin = $calculated_token;
                                  $code_confirmed = $calculated_token;
                                  $input_to_check = substr($input_to_check, -strlen($code_confirmed));                            
                                  if (!$ldap_check_passed) {
                                      $input_to_check.= '_BAD_LDAP_CHECK';
                                  }
                                  $this->SetLastClearOtpValue($original_input);
                              } else {
                                  if ($need_prefix) {
                                      $calculated_token = $pin.$calculated_token;
                                  }

                                  $code_confirmed = $calculated_token;
                                  $this->SetLastClearOtpValue($original_input);
                                  if ('' != $this->GetChapPassword()) {
                                      $code_confirmed = mb_strtolower($this->CalculateChapPassword($code_confirmed));
                                  } elseif ('' != $this->GetMsChapResponse()) {
                                      $code_confirmed = mb_strtolower($this->CalculateMsChapResponse($code_confirmed));
                                  } elseif ('' != $this->GetMsChap2Response()) {
                                      $code_confirmed = mb_strtolower($this->CalculateMsChap2Response($user, $code_confirmed));
                                  }
                              }

                              if ($input_to_check === $code_confirmed) {
                                  if (($now_steps+$additional_step+$delta_step) > $last_login_step) {
                                      $this->SetTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                      $this->SetTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                      $this->SetTokenErrorCounter(0);
                                      $result = 0; // OK: This is the correct token
                                  } else {
                                      $result = 26; // ERROR: this token has already been used
                                  }
                              } else {
                                  $check_step++;
                              }
                          } while (($check_step < $max_steps) && (90 <= $result));
                          break;
                      default:
                          if ($this->GetVerboseFlag()) {
                            $this->WriteLog("Debug: *Invalid algorithm (SelfRegisterHardwareToken): $algorithm", FALSE, FALSE, 8888, 'Debug', '');
                          }
                          $result = 23; // ERROR: Invalid algorithm
                  }

                  if (90 <= $result) {
                      if ($this->GetVerboseFlag()) {
                          if ((strlen($input_to_check) === strlen($calculated_token))) {
                              $this->WriteLog("Info: *(authentication typed by the user: ".$input_to_check.")", FALSE, FALSE, $result, 'User', $user);
                          } else {
                              $result = 98;
                              $this->WriteLog("Info: *(authentication typed by the user is ".strlen($input_to_check)." chars long instead of ".strlen($calculated_token)." chars", FALSE, FALSE, $result, 'User', $user);
                          }
                      }
                  }
                  
                  if (0 == $result) {
                      $this->AddTokenAttributedUsers($user);
                      if (!$this->WriteTokenData()) {
                          $result = 28; // ERROR: Unable to write the changes in the file
                          $this->WriteLog("Error: Unable to write the changes in the file for the token ".$this->GetToken(), FALSE, FALSE, $result, 'Token', $user);
                      } else {
                          $this->SetUserTokenSerialNumber($serial_number);
                          $this->SetUserAlgorithm($this->GetTokenAlgorithm());
                          $this->SetUserTokenAlgoSuite($this->GetTokenAlgoSuite());
                          $this->SetUserTokenSeed($this->GetTokenSeed());
                          $this->SetUserTokenPrivateId($this->GetTokenPrivateId());
                          $this->SetUserTokenNumberOfDigits($this->GetTokenNumberOfDigits());
                          $this->SetUserTokenTimeInterval($this->GetTokenTimeInterval());
                          $this->SetUserTokenLastEvent($this->GetTokenLastEvent());
                          $this->SetUserTokenDeltaTime($this->GetTokenDeltaTime());
                          $this->SetUserTokenLastLogin($this->GetTokenLastLogin());
                          $this->SetUserErrorCounter(0);
                          if (!$this->WriteUserData()) {
                              $result = 28; // ERROR: Unable to write the changes in the file
                              $this->WriteLog("Error: Unable to write the changes in the file for the user ".$this->GetUser(), FALSE, FALSE, $result, 'System', '');
                          } else {
                              $this->WriteLog("OK: token ".$this->GetToken()." successfully attributed to user ".$this->GetUser(), FALSE, FALSE, 19, 'User');
                          }
                      }
                  }
              }
          } else {
              $result = 29; // ERROR: Token doesn't exist
              $this->WriteLog("Error: Token ".$this->GetToken()." does not exist", FALSE, FALSE, $result, 'Token');
          }
      } else {
          $result = 29; // ERROR: User doesn't exist
          $this->WriteLog("Error: User ".$this->GetUser()." does not exist", FALSE, FALSE, $result, 'User');
      }
      return $result;
  } // End of SelfRegisterHardwareToken


  function ImportTokensFile(
      $file,
      $original_name = '',
      $cipher_password = '',
      $key_mac = ""
  ) {
      if (!file_exists($file)) {
          $result = FALSE;
      } else {
          $data1000 = @file_get_contents($file, FALSE, NULL, 0, 1000);
          $file_name = ('' != $original_name)?$original_name:$file;
          if (FALSE !== mb_strpos(mb_strtolower($data1000), mb_strtolower('"urn:ietf:params:xml:ns:keyprov:pskc"'))) {
              $result = $this->ImportTokensFromPskc($file, $cipher_password, $key_mac);
          } elseif (FALSE !== mb_strpos(mb_strtolower($data1000), mb_strtolower('LOGGING START'))) {
              $result = $this->ImportYubikeyTraditional($file);
          } elseif ((FALSE !== mb_strpos(mb_strtolower($data1000), mb_strtolower('AUTHENEXDB'))) && ('.sql' == mb_strtolower(substr($file_name, -4)))) {
              $result = $this->ImportTokensFromAuthenexSql($file);
          } elseif ((FALSE !== mb_strpos(mb_strtolower($data1000), mb_strtolower('SafeWord Authenticator Records'))) && ('.dat' == mb_strtolower(substr($file_name, -4)))) {
              $result = $this->ImportTokensFromAlpineDat($file);
          } elseif (FALSE !== mb_strpos(mb_strtolower($data1000), mb_strtolower('<ProductName>eTPass'))) {
          // elseif (('.xml' == mb_strtolower(substr($file_name, -4))) && (FALSE !== mb_strpos(mb_strtolower($file_name), 'alpine')))
              $result = $this->ImportTokensFromAlpineXml($file);
          } elseif ('.xml' == mb_strtolower(substr($file_name, -4))) {
              $result = $this->ImportTokensFromXml($file);
          } else {
              $result = $this->ImportTokensFromCsv($file);
          }
      }
      return $result;
  }


  function DecodeCipherValue(
      $encrypted_tree,
      $cipher_array,
      $integer_value = FALSE
  ) {
      $passphrase = $cipher_array['Password'];
      $Secret = '';
      $cipher_aes = new Crypt_AES();

      $encryption_method_tag = (isset($encrypted_tree->{$cipher_array['xenc_ns'].'encryptionmethod'})?$cipher_array['xenc_ns']:'').'encryptionmethod';
      $encryption_method_algorithm_url = isset($encrypted_tree->{$encryption_method_tag}[0]->tagAttrs["algorithm"])?($encrypted_tree->{$encryption_method_tag}[0]->tagAttrs["algorithm"]):'';
      $encryption_method_algorithm = (FALSE !== mb_strpos($encryption_method_algorithm_url,'#aes128-cbc'))?'aes128':((FALSE !== mb_strpos($encryption_method_algorithm_url,'#kw-aes128'))?'kw-ases128':'');
      $cipher_data_tag = (isset($encrypted_tree->{$cipher_array['xenc_ns'].'cipherdata'})?$cipher_array['xenc_ns']:'').'cipherdata';
      $cipher_value_tag = (isset($encrypted_tree->{$cipher_data_tag}[0]->{$cipher_array['xenc_ns'].'ciphervalue'})?$cipher_array['xenc_ns']:'').'ciphervalue';
      $cipher_value = isset($encrypted_tree->{$cipher_data_tag}[0]->{$cipher_value_tag}[0]->tagData)?($encrypted_tree->{$cipher_data_tag}[0]->{$cipher_value_tag}[0]->tagData):'';

      if ('' != $passphrase) {
          for ($tries = 0; $tries < 3; $tries++) {
              if ('' == $cipher_array['KeyDerivationMethodAlgorithm']) {
                  $cipher_aes->setKey($passphrase);
                  $Secret = (substr($cipher_aes->decrypt(base64_decode($cipher_value)),16));
                  if ('' == $Secret) {
                      $cipher_aes->setKey(hex2bin(preg_replace("/[^A-Fa-f0-9]/", '', $passphrase)));
                      $Secret = (substr($cipher_aes->decrypt(base64_decode($cipher_value)),16));
                  }
              } elseif ('pkcs5' == $cipher_array['KeyDerivationMethodAlgorithm']) {
                  $cipher_aes->setPassword($passphrase, 'pbkdf2', 'sha1', $cipher_array['Salt'], $cipher_array['IterationCount'], $cipher_array['KeyLength']);
                  $Secret = (substr($cipher_aes->decrypt(base64_decode($cipher_value)),16));
              }
              if ('' != $Secret) {
                  break;
              } elseif (0 == $tries) {
                  $passphrase = trim($passphrase);
              } elseif (1 == $tries) {
                  if ((0 === mb_strpos($passphrase, '0x')) && (0 == (strlen($passphrase) % 2))) {
                      $passphrase = hex2bin(substr($passphrase, 2));
                  }
              }
          }
      }
      if (('' != $Secret) && ($integer_value)) {
          $value = 0;
          for( $i = 0; $i < strlen($Secret); $i++ ) {
              $value = ($value << 8) | ord($Secret[$i]);
          }
          $Secret = $value;
      }
      return $Secret;
  }

  
  function ImportTokensFromPskc(
      $pskc_file,
      $cipher_password = '',
      $keymac = ''
  ) {
      $this->ResetLastImportedTokensArray();
      $result = TRUE;
      if (!file_exists($pskc_file)) {
          $this->WriteLog("Error: Tokens definition file ".$pskc_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
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

          if (isset($xml->document)) {
              $keycontainer = $xml->document;
              reset($keycontainer->tagAttrs);
              while(list($attribute_key, $attribute_value) = each($keycontainer->tagAttrs)) {
                  if ('http://www.w3.org/2000/09/xmldsig#' == $attribute_value) {
                      $ds_ns = substr($attribute_key,mb_strpos($attribute_key,':')+1);
                      $ds_ns.= ('' != $ds_ns)?':':'';
                  }
                  if ('http://www.rsasecurity.com/rsalabs/pkcs/schemas/pkcs-5v2-0#' == $attribute_value) {
                      $pkcs5_ns = substr($attribute_key,mb_strpos($attribute_key,':')+1);
                      $pkcs5_ns.= ('' != $pkcs5_ns)?'_':'';
                  }
                  if ('urn:ietf:params:xml:ns:keyprov:pskc' == $attribute_value) {
                      $pskc_ns = substr($attribute_key,mb_strpos($attribute_key,':')+1);
                      $pskc_ns.= ('' != $pskc_ns)?'_':'';
                  }
                  if ('http://www.w3.org/2001/04/xmlenc#' == $attribute_value) {
                      $xenc_ns = substr($attribute_key,mb_strpos($attribute_key,':')+1);
                      $xenc_ns.= ('' != $xenc_ns)?'_':'';
                  }
                  if ('http://www.w3.org/2009/xmlenc11#' == $attribute_value) {
                      $xenc11_ns = substr($attribute_key,mb_strpos($attribute_key,':')+1);
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
              $CipherArray['KeyDerivationMethodAlgorithm'] = ((FALSE !== mb_strpos($KeyDerivationMethodAlgorithmUrl,'#pbkdf2'))?'pkcs5':'');
              // http://www.rsasecurity.com/rsalabs/pkcs/schemas/pkcs-5v2-0#pbkdf2
              // http://www.rsasecurity.com/rsalabs/pkcs/schemas/pkcs-5#pbkdf2
              $CipherArray['Salt'] = '';
              $CipherArray['IterationCount'] = 0;
              $CipherArray['KeyLength'] = 0;
              if ('pkcs5' == $CipherArray['KeyDerivationMethodAlgorithm']) {

                  $search_tag = 'pbkdf2_params';
                  if (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pkcs5_ns.$search_tag})) {
                      $search_tag = $pkcs5_ns.$search_tag;
                  } elseif (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$xenc11_ns.$search_tag})) {
                      $search_tag = $xenc11_ns.$search_tag;
                  }
                  $pbkdf2_params_tag = $search_tag;

                  $search_tag = 'salt';
                  if (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$pkcs5_ns.$search_tag})) {
                      $search_tag = $pkcs5_ns.$search_tag;
                  } elseif (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$xenc11_ns.$search_tag})) {
                      $search_tag = $xenc11_ns.$search_tag;
                  }
                  $salt_tag = $search_tag;
 
                  $search_tag = 'specified';
                  if (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$salt_tag}[0]->{$pkcs5_ns.$search_tag})) {
                      $search_tag = $pkcs5_ns.$search_tag;
                  } elseif (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$salt_tag}[0]->{$xenc11_ns.$search_tag})) {
                      $search_tag = $xenc11_ns.$search_tag;
                  }
                  $salt_specified_tag = $search_tag;

                  $search_tag = 'iterationcount';
                  if (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$pkcs5_ns.$search_tag})) {
                      $search_tag = $pkcs5_ns.$search_tag;
                  } elseif (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$xenc11_ns.$search_tag})) {
                      $search_tag = $xenc11_ns.$search_tag;
                  }
                  $iterationcount_tag = $search_tag;

                  $search_tag = 'keylength';
                  if (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$pkcs5_ns.$search_tag})) {
                      $search_tag = $pkcs5_ns.$search_tag;
                  } elseif (isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$xenc11_ns.$search_tag})) {
                      $search_tag = $xenc11_ns.$search_tag;
                  }
                  $keylength_tag = $search_tag;

                  $CipherArray['Salt'] = base64_decode(isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$salt_tag}[0]->{$salt_specified_tag}[0]->tagData)?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$salt_tag}[0]->{$salt_specified_tag}[0]->tagData):'');
                  $CipherArray['IterationCount'] = intval(isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$iterationcount_tag}[0]->tagData)?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$iterationcount_tag}[0]->tagData):0);
                  $CipherArray['KeyLength'] = intval(isset($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$keylength_tag}[0]->tagData)?($keycontainer->{$EncryptionKey_tag}[0]->{$DerivedKey_tag}[0]->{$KeyDerivationMethod_tag}[0]->{$pbkdf2_params_tag}[0]->{$keylength_tag}[0]->tagData):0);
              }
              
              $search_tag = 'keypackage';
              if (isset($keycontainer->{$pskc_ns.'keypackage'})) {
                  $search_tag = $pskc_ns.$search_tag;
              } elseif (isset($keycontainer->{$pkcs5_ns.'keypackage'})) {
                  $search_tag = $pkcs5_ns.$search_tag;
              } elseif (isset($keycontainer->{$xenc11_ns.'keypackage'})) {
                  $search_tag = $xenc11_ns.$search_tag;
              }
              $KeyPackage_tag = $search_tag;

              // Extract each key
              // foreach($keycontainer[0][$KeyPackage_tag] as $keypackage) // this is not working well in PHP4
              reset($keycontainer->{$KeyPackage_tag});
              while(list($keypackage_key, $keypackage) = each($keycontainer->{$KeyPackage_tag})) {
                  $DeviceInfo_tag = (isset($keypackage->{$pskc_ns.'deviceinfo'})?$pskc_ns:'').'deviceinfo';
                  
                  $Manufacturer_tag = (isset($keypackage->{$DeviceInfo_tag}[0]->{$pskc_ns.'manufacturer'})?$pskc_ns:'').'manufacturer';
                  $Manufacturer = (isset($keypackage->{$DeviceInfo_tag}[0]->{$Manufacturer_tag}[0]->tagData)?($keypackage->{$DeviceInfo_tag}[0]->{$Manufacturer_tag}[0]->tagData):'');
                  
                  $SerialNo_tag = (isset($keypackage->{$DeviceInfo_tag}[0]->{$pskc_ns.'serialno'})?$pskc_ns:'').'serialno';
                  $SerialNo = (isset($keypackage->{$DeviceInfo_tag}[0]->{$SerialNo_tag}[0]->tagData)?($keypackage->{$DeviceInfo_tag}[0]->{$SerialNo_tag}[0]->tagData):'');

                  $Model_tag = (isset($keypackage->{$DeviceInfo_tag}[0]->{$pskc_ns.'model'})?$pskc_ns:'').'model';
                  $Model = (isset($keypackage->{$DeviceInfo_tag}[0]->{$Model_tag}[0]->tagData)?($keypackage->{$DeviceInfo_tag}[0]->{$Model_tag}[0]->tagData):'');
                  
                  $IssueNo_tag = (isset($keypackage->{$DeviceInfo_tag}[0]->{$pskc_ns.'issueno'})?$pskc_ns:'').'issueno';
                  $IssueNo = (isset($keypackage->{$DeviceInfo_tag}[0]->{$IssueNo_tag}[0]->tagData)?($keypackage->{$DeviceInfo_tag}[0]->{$IssueNo_tag}[0]->tagData):'');
                  
                  $CryptoModuleInfo_tag = (isset($keypackage->{$pskc_ns.'cryptomoduleinfo'})?$pskc_ns:'').'cryptomoduleinfo';
                  
                  $CryptoId_tag = (isset($keypackage->{$CryptoModuleInfo_tag}[0]->{$pskc_ns.'id'})?$pskc_ns:'').'id';
                  $CryptoId = (isset($keypackage->{$CryptoModuleInfo_tag}[0]->{$CryptoId_tag}[0]->tagData)?($keypackage->{$CryptoModuleInfo_tag}[0]->{$CryptoId_tag}[0]->tagData):'');

                  $Key_tag = (isset($keypackage->{$pskc_ns.'key'})?$pskc_ns:'').'key';
                  
                  $AlgorithmUrl = isset($keypackage->{$Key_tag}[0]->tagAttrs["algorithm"])?($keypackage->{$Key_tag}[0]->tagAttrs["algorithm"]):'';
                  $Algorithm = (FALSE !== mb_strpos($AlgorithmUrl,'hotp'))?'hotp':((FALSE !== mb_strpos($AlgorithmUrl,'totp'))?'totp':'');
                  // $Algorithm = (FALSE !== mb_strpos($AlgorithmUrl,'hotp'))?'hotp':((FALSE !== mb_strpos($AlgorithmUrl,'totp'))?'totp':((FALSE !== mb_strpos($AlgorithmUrl,'ocra'))?'ocra':''));

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
                  if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$EncryptedValue_tag}[0])) {
                      $SecretEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Secret_tag}[0]->{$EncryptedValue_tag}[0];
                      $Secret = $this->DecodeCipherValue($SecretEncryptedPath, $CipherArray);
                  }
                  
                  $Counter_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'counter'})?$pskc_ns:'').'counter';
                  $CounterPlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                  $Counter = intval(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$CounterPlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$CounterPlainValue_tag}[0]->tagData):0);
                  $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                  if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$EncryptedValue_tag}[0])) {
                      $CounterEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Counter_tag}[0]->{$EncryptedValue_tag}[0];
                      $Counter = $this->DecodeCipherValue($CounterEncryptedPath, $CipherArray, TRUE);
                  }

                  $Time_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'time'})?$pskc_ns:'').'time';
                  $TimePlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                  $Time = intval(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$TimePlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$TimePlainValue_tag}[0]->tagData):'');
                  $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                  if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$EncryptedValue_tag}[0])) {
                      $TimeEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$Time_tag}[0]->{$EncryptedValue_tag}[0];
                      $Time = $this->DecodeCipherValue($TimeEncryptedPath, $CipherArray, TRUE);
                  }
                  
                  $TimeInterval_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$pskc_ns.'timeinterval'})?$pskc_ns:'').'timeinterval';
                  $TimeIntervalPlainValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$pskc_ns.'plainvalue'})?$pskc_ns:'').'plainvalue';
                  $TimeInterval = intval(isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$TimeIntervalPlainValue_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$TimeIntervalPlainValue_tag}[0]->tagData):30);
                  $EncryptedValue_tag = (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$pskc_ns.'encryptedvalue'})?$pskc_ns:'').'encryptedvalue';
                  if (isset($keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$EncryptedValue_tag}[0])) {
                      $TimeIntervalEncryptedPath = $keypackage->{$Key_tag}[0]->{$Data_tag}[0]->{$TimeInterval_tag}[0]->{$EncryptedValue_tag}[0];
                      $TimeInterval = $this->DecodeCipherValue($TimeIntervalEncryptedPath, $CipherArray, TRUE);
                  }

                  $Policy_tag = (isset($keypackage->{$Key_tag}[0]->{$pskc_ns.'policy'})?$pskc_ns:'').'policy';

                  $PINPolicy_tag = (isset($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$pskc_ns.'pinpolicy'})?$pskc_ns:'').'pinpolicy';
                  $PINPolicyAttributes = isset($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$PINPolicy_tag}[0]->tagAttrs[0])?($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$PINPolicy_tag}[0]->tagAttrs):'';

                  $keyusage_tag = (isset($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$pskc_ns.'keyusage'})?$pskc_ns:'').'keyusage';
                  $keyusage = isset($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$keyusage_tag}[0]->tagData)?($keypackage->{$Key_tag}[0]->{$Policy_tag}[0]->{$keyusage_tag}[0]->tagData):'';

                  $SerialNumber = (('' == $SerialNo)?$KeyId:$SerialNo);

                  /*
                  if ((FALSE !== mb_strpos($KeyId, $SerialNo)) && (strlen($KeyId) > strlen($SerialNo))) {
                      $SerialNo = $KeyId;
                  }
                  */

                  if (("" == $keyusage) || ("OTP" == $keyusage)) {
                      if ((('hotp' == $Algorithm) || ('totp' == $Algorithm)) && ('' != $SerialNo) && ('' != $Secret)) {
                          $this->SetToken($SerialNumber);
                          $this->SetTokenDescription(trim(trim($Manufacturer.' '.$Model).' '.$SerialNo));
                          $this->SetTokenManufacturer($Manufacturer);
                          $this->SetTokenModel($Model);
                          $this->SetTokenIssueNo($IssueNo);
                          $this->SetTokenIssuer($Issuer);
                          $this->SetTokenKeyId($keyusage);
                          $this->SetTokenKeyUsage($KeyId);
                          $this->SetTokenSerialNo($SerialNo);
                          $this->SetTokenSerialNumber($SerialNumber);
                          $this->SetTokenKeyAlgorithm($AlgorithmUrl);
                          $this->SetTokenAlgorithm($Algorithm);
                          $this->SetTokenAlgoSuite($Suite);
                          $this->SetTokenOtp("TRUE");
                          $this->SetTokenFormat($Encoding);
                          $this->SetTokenNumberOfDigits($Length);
                          if ($Counter >= 0) {
                              $this->SetTokenLastEvent($Counter-1);
                          } else {
                              $this->SetTokenLastEvent(0);
                          }
                          $this->SetTokenDeltaTime($Time);
                          $this->SetTokenTimeInterval($TimeInterval);
                          $this->SetTokenSeed(bin2hex($Secret));
                          
                          if ($this->CheckTokenExists('', false)) {
                              $this->WriteLog("Info: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                          } else {
                              $result = $this->WriteTokenData() && $result;
                              $this->AddLastImportedToken($this->GetToken());
                              $this->WriteLog("Info: Token with SerialNo ".$SerialNo." successfully imported", FALSE, FALSE, 15, 'Token', '');
                          }
                          if ($this->GetVerboseFlag()) {
                              $full_token_data = '';
                              reset($this->_token_data);
                              while(list($key, $value) = each($this->_token_data)) {
                                  if ('' != $value) {
                                      $full_token_data = $full_token_data."  Token ".$SerialNo." - ".$key.": ".$value."\n";
                                  }
                              }
                              $this->WriteLog("Debug: *".$full_token_data, FALSE, FALSE, 8888, 'Debug', '');
                          }
                      } elseif (('hotp' == $Algorithm) || ('totp' == $Algorithm)) {
                          $result = FALSE;
                          $this->WriteLog("Info: Token with SerialNo ".$SerialNo." failed during importation", FALSE, FALSE, 32, 'Token', '');
                      }
                  }
              }
          }
      }
      return $result;
  }


  /*
   * YubiKey traditional format log file (csv)
   * (https://github.com/Yubico/yubikey-personalization-gui/blob/master/src/yubikeylogger.cpp)
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
  function ImportYubikeyTraditional($yubikey_file) {
      $result = TRUE;
      $imported_tokens = 0;
      $this->ResetTokenArray();
      $this->ResetLastImportedTokensArray();
      if (!file_exists($yubikey_file)) {
          $this->WriteLog("Error: YubiKeys log file ".$yubikey_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
          //Get the document loaded into a variable
          if ($file_handler = @fopen($yubikey_file, "rt")) {

              $yubikey_class = new MultiotpYubikey();
              
              while (!feof($file_handler)) {
                  $line = trim(fgets($file_handler));

                  $line = str_replace(';',"\t", $line);
                  $line = str_replace(',',"\t", $line);

                  $line_array = explode("\t", $line);

                  if (count($line_array) >= 18) {
                      $token_algo_suite = 'AES-128';
                      $manufacturer = "Yubico";
                      switch (trim($line_array[0])) {
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
                      if (('hotp' == $algorithm) && (0 == intval($line_array[10]))) {
                          if (1 == intval($line_array[8])) {
                              $esn = substr(trim($line_array[3]),0,2).$yubikey_class->ModHexToHex(substr(trim($line_array[3]),2));
                          } elseif (1 == intval($line_array[9])) {
                              $esn = substr(trim($line_array[3]),0,4).$yubikey_class->ModHexToHex(substr(trim($line_array[3]),4));
                          } else {
                              $esn = $yubikey_class->ModHexToHex(trim($line_array[3]));
                          }
                      }
                      $private_id = "";
                      $seed = trim($line_array[5]);
                      $interval_or_event = intval($line_array[12]);

                      if ('hotp' == $algorithm) {
                          $digits = intval($line_array[11]);
                          $next_event = $interval_or_event;
                          $time_interval = 0;
                      } elseif ("yubicootp" == $algorithm) {
                          $private_id = trim($line_array[4]);
                          if ("000000000000" == $private_id) {
                              $private_id = "";
                          }
                          $digits = 32;
                          $next_event = 0;
                          $time_interval = 0;
                      }
                      
                      if ('' != $algorithm) {
                          $this->SetToken($esn);
                          $this->SetTokenPrivateId($private_id);
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
                          
                          if ('' == $esn) {
                              $this->WriteLog("Error: A token doesn't have any serial number", FALSE, FALSE, 32, 'Token', '');
                          } elseif (!$this->IsValidAlgorithm($algorithm)) {
                              $this->WriteLog("Error: The algorithm ".$algorithm." is not recognized", FALSE, FALSE, 32, 'Token', '');
                          } elseif ($this->CheckTokenExists('', false)) {
                              $this->WriteLog("Info: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                          } else {
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
      }
      if (0 == $imported_tokens) {
          $result = FALSE;
      }
      return $result;
  }


  function ImportTokensFromCsv(
      $csv_file
  ) {
      $result = TRUE;
      $imported_tokens = 0;
      $this->ResetTokenArray();
      $this->ResetLastImportedTokensArray();
      if (!file_exists($csv_file)) {
          $this->WriteLog("Error: Tokens definition file ".$csv_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
          //Get the document loaded into a variable
          if ($file_handler = @fopen($csv_file, "rt")) {
              while (!feof($file_handler)) {
                  $line = trim(fgets($file_handler));

                  $line = str_replace(';',"\t", $line);
                  $line = str_replace(',',"\t", $line);

                  $line_array = explode("\t", $line);

                  if (count($line_array) >= 6) {
                      $esn               = trim($line_array[0]);
                      $manufacturer      = $line_array[1];
                      $algorithm         = mb_strtolower($line_array[2]);
                      $seed              = $line_array[3];
                      $digits            = $line_array[4];
                      $interval_or_event = intval($line_array[5]);
                      
                      if ('hotp' == $algorithm) {
                          $next_event = $interval_or_event;
                          $time_interval = 0;
                      } else {
                          $next_event = 0;
                          $time_interval = $interval_or_event;
                          if ("motp" == $algorithm) {
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
                      
                      if ('' == $esn) {
                          $this->WriteLog("Error: A token doesn't have any serial number", FALSE, FALSE, 32, 'Token', '');
                      } elseif (!$this->IsValidAlgorithm($algorithm)) {
                          $this->WriteLog("Error: The algorithm ".$algorithm." is not recognized", FALSE, FALSE, 32, 'Token', '');
                      } elseif ($this->CheckTokenExists('', false)) {
                          $this->WriteLog("Info: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                      } else {
                          $result = $this->WriteTokenData() && $result;
                          $this->AddLastImportedToken($this->GetToken());
                          $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported", FALSE, FALSE, 15, 'Token', '');
                      }
                      $this->ResetTokenArray();
                  }
              }
              fclose($file_handler);
          } else {
              $this->WriteLog("Error: Tokens definition file ".$csv_file." cannot be read", FALSE, FALSE, 29, 'Token', '');
              $result = FALSE;
          }
      }
      if (0 == $imported_tokens) {
          $result = FALSE;
      }
      return $result;
  }


  function ImportTokensFromXml(
      $xml_file
  ) {
      $this->ResetLastImportedTokensArray();
      $result = TRUE;
      if (!file_exists($xml_file)) {
          $this->WriteLog("Error: Tokens definition file ".$xml_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
          // http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00
          
          //Get the XML document loaded into a variable
          $sXmlData = @file_get_contents($xml_file);

          //Set up the parser object
          $xml = new MultiotpXmlParser($sXmlData);

          //Parse it !
          $xml->Parse();

          // Array of key types
          $key_types = array();
          
          if (isset($xml->document->keyproperties)) {
              foreach ($xml->document->keyproperties as $keyproperty) {
                  $id = (isset($keyproperty->tagAttrs['xml:id'])?$keyproperty->tagAttrs['xml:id']:'');
                  
                  if ('' != $id) {
                      $key_types[$id]['id'] = $id;
                      $key_types[$id]['issuer'] = (isset($keyproperty->issuer[0]->tagData)?$keyproperty->issuer[0]->tagData:'');
                      $key_types[$id]['keyalgorithm'] = (isset($keyproperty->tagAttrs['keyalgorithm'])?$keyproperty->tagAttrs['keyalgorithm']:'');
                      $pos = strrpos($key_types[$id]['keyalgorithm'], "#");
                      $key_types[$id]['algorithm'] = (($pos === false)?'':mb_strtolower(substr($key_types[$id]['keyalgorithm'], $pos+1)));
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
          
          if (isset($xml->document->device)) {
              foreach ($xml->document->device as $device) {
                  $keyid = (isset($device->key[0]->tagAttrs['keyid'])?$device->key[0]->tagAttrs['keyid']:'');
                  if ('' != $keyid) {
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
                      
                      if (isset($device->key[0]->tagAttrs['keyproperties'])) {
                          $keyproperties = $device->key[0]->tagAttrs['keyproperties'];
                          if (isset($key_types[$keyproperties])) {
                              reset($key_types[$keyproperties]);
                              while(list($key, $value) = each($key_types[$keyproperties])) {
                                  $$key = $value;
                              }
                          }
                      }
                      
                      $manufacturer = (isset($device->deviceinfo[0]->manufacturer[0]->tagData)?$device->deviceinfo[0]->manufacturer[0]->tagData:$manufacturer);
                      $serialno = (isset($device->deviceinfo[0]->serialno[0]->tagData)?$device->deviceinfo[0]->serialno[0]->tagData:$serialno);

                      $issuer = (isset($device->key[0]->issuer[0]->tagData)?$device->key[0]->issuer[0]->tagData:$issuer);
                      
                      if (isset($device->key[0]->tagAttrs['keyalgorithm'])) {
                          $keyalgorithm = $device->key[0]->tagAttrs['keyalgorithm'];
                          $pos = strrpos($keyalgorithm, "#");
                          $algorithm = (($pos === false)?$algorithm:mb_strtolower(substr($keyalgorithm, $pos+1)));
                      }
                      
                      $otp = (isset($device->key[0]->usage[0]->tagAttrs['otp'])?$device->key[0]->usage[0]->tagAttrs['otp']:$otp);
                      $format = (isset($device->key[0]->usage[0]->responseformat[0]->tagAttrs['format'])?$device->key[0]->usage[0]->responseformat[0]->tagAttrs['format']:$format);
                      $length = (isset($device->key[0]->usage[0]->responseformat[0]->tagAttrs['length'])?$device->key[0]->usage[0]->responseformat[0]->tagAttrs['length']:$length);
                      $counter = (isset($device->key[0]->data[0]->counter[0])?$device->key[0]->data[0]->counter[0]->plainvalue[0]->tagData:$counter);
                      $time = (isset($device->key[0]->data[0]->time[0])?$device->key[0]->data[0]->time[0]->plainvalue[0]->tagData:$time);
                      $timeinterval = (isset($device->key[0]->data[0]->timeinterval[0])?$device->key[0]->data[0]->timeinterval[0]->plainvalue[0]->tagData:$timeinterval);
                      $suite = (isset($device->key[0]->data[0]->suite[0])?$device->key[0]->data[0]->suite[0]->plainvalue[0]->tagData:$suite);
                      
                      if (isset($device->key[0]->data[0]->secret[0]->plainvalue[0]->tagData)) {
                          $secret = bin2hex(base64_decode($device->key[0]->data[0]->secret[0]->plainvalue[0]->tagData));
                      }

                      if ('' == trim($serialno)) {
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
                      if ($counter >= 0) {
                          $this->SetTokenLastEvent($counter-1);
                      } else {
                          $this->SetTokenLastEvent(0);
                      }
                      $this->SetTokenDeltaTime($time);
                      $this->SetTokenTimeInterval($timeinterval);
                      $this->SetTokenSeed($secret);
                      
                      if ($this->CheckTokenExists('', false)) {
                          $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                      } else {
                          $result = $this->WriteTokenData() && $result;
                          $this->AddLastImportedToken($this->GetToken());
                          $this->WriteLog("Info: Token with keyid ".$keyid." successfully imported", FALSE, FALSE, 15, 'Token', '');
                      }
                      if ($this->GetVerboseFlag()) {
                          $full_token_data = '';
                          reset($this->_token_data);
                          while(list($key, $value) = each($this->_token_data)) {
                              if ('' != $value) {
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


  function ImportTokensFromAlpineXml(
      $xml_file
  ) {
      $this->ResetLastImportedTokensArray();
      $result = TRUE;
      if (!file_exists($xml_file)) {
          $this->WriteLog("Error: Tokens definition file ".$xml_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
          $sXmlData = @file_get_contents($xml_file);

          //Set up the parser object
          $xml = new MultiotpXmlParser($sXmlData);

          //Parse it !
          $xml->Parse();

          // Array of key types
          $key_types = array();
          if (isset($xml->document->token)) {
              foreach ($xml->document->token as $token) {
                  $serial = (isset($token->tagAttrs['serial'])?$token->tagAttrs['serial']:'');
                  if ('' != $serial) {
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
                      
                      if (isset($token->applications[0]->application[0]->seed[0]->tagData)) {
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
                      
                      if ($this->CheckTokenExists('', false)) {
                          $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                      } else {
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


  function ImportTokensFromAlpineDat(
      $data_file
  ) {
      $ProductName = "";
      $this->ResetTokenArray();
      $this->ResetLastImportedTokensArray();
      $result = TRUE;
      if (!file_exists($data_file)) {
          $this->WriteLog("Error: Tokens definition file ".$data_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
          // SafeWord Authenticator Records
          
          //Get the document loaded into a variable
          if ($file_handler = @fopen($data_file, "rt")) {

              $line = trim(fgets($file_handler));
              
              $reference_header       = "SafeWord Authenticator Records";
              $reference_manufacturer = "SafeWord";
              
              if (FALSE !== mb_strpos(mb_strtolower($line), mb_strtolower($reference_header))) {
                  $manufacturer = $reference_manufacturer;
              
                  while (!feof($file_handler)) {
                      $line = trim(fgets($file_handler));
                      $line_array = explode(":",$line,2);
                      $line_array[0] = trim($line_array[0]);
                      $line_array[1] = trim((isset($line_array[1])?$line_array[1]:''));

                      switch (mb_strtolower($line_array[0])) {
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
                              foreach ($data_array as $data_one) {
                                  $attribute_array = explode("=",$data_one,2);
                                  $attribute_array[0] = trim($attribute_array[0]);
                                  $attribute_array[1] = trim((isset($attribute_array[1])?$attribute_array[1]:''));
                                  switch (mb_strtolower($attribute_array[0])) {
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
                              if ($this->CheckTokenExists('', false)) {
                                  $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                              } else {
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
      }
      return $result;
  }


  function ImportTokensFromAuthenexSql(
      $data_file
  ) {
      $ProductName = "";
      $this->ResetTokenArray();
      $this->ResetLastImportedTokensArray();
      $result = TRUE;
      if (!file_exists($data_file)) {
          $this->WriteLog("Error: Tokens definition file ".$data_file." doesn't exist", FALSE, FALSE, 29, 'Token', '');
          $result = FALSE;
      } else {
          // Authenex Authenticator Records
          
          //Get the document loaded into a variable
          if ($file_handler = @fopen($data_file, "rt")) {
          
              $line = trim(fgets($file_handler));
              
              $reference_header       = "AUTHENEXDB";
              $reference_manufacturer = "Authenex";
              
              if (FALSE !== mb_strpos(mb_strtolower($line), mb_strtolower($reference_header))) {
                  $manufacturer = $reference_manufacturer;
                  
                  while (!feof($file_handler)) {
                      $line = trim(fgets($file_handler));

                      if (FALSE !== mb_strpos(mb_strtoupper($line), 'INSERT INTO OTP')) {
                          $token_array = array();
                          $line_array = explode("(",$line,3);
                          $token_line = str_replace(")",",",$line_array[2]);
                          $token_array = explode(",",$token_line);
                          if (isset($token_array[1])) {
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
                          if ($this->CheckTokenExists('', false)) {
                              $this->WriteLog("Error: Token ".$this->GetToken()." already exists", FALSE, FALSE, 32, 'Token', '');
                          } else {
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
  /**
   * @brief   Create a new device
   *
   * @param   array/string $id                           New unique array, or unique id of the device
   * @param   string  $description
   * @param   string  $device_secret
   * @param   string  $ip_or_fqdn
   * @param   string  $subnet
   * @param   string  $shortname
   * @param   booldan $with_radius_update
   * @param   int     $challenge_response_enabled
   * @param   string  $text_token_challenge
   * @param   int     $sms_challenge_enabled
   * @param   string  $text_sms_challenge
   * @param   int     $cache_result_enabled
   * @param   int     $cache_timeout
   *
   * New unique array parameter
   *   string  id                          Unique id of the device
   *   string  description
   *   string  device_secret
   *   string  ip_or_fqdn
   *   string  subnet
   *   string  shortname
   *   booelan with_radius_update
   *   int     challenge_response_enabled
   *   string  text_token_challenge
   *   int     sms_challenge_enabled
   *   string  text_sms_challenge
   *   int     cache_result_enabled
   *   int     cache_timeout
   *
   * @retval  boolean                      Result of the operation
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.2.5
   * @date    2016-10-03
   * @since   2013-12-23
   */
  function CreateDevice($id_array = array('id' => ''),
                        $description = '',
                        $device_secret = '',
                        $ip_or_fqdn = '',
                        $subnet = '',
                        $shortname = '',
                        $with_radius_update = TRUE,
                        $challenge_response_enabled = 0,
                        $text_token_challenge = '',
                        $sms_challenge_enabled = 0,
                        $text_sms_challenge = '',
                        $cache_result_enabled = 0,
                        $cache_timeout = 3600
  ) {
    $result = FALSE;
    $device_id = (is_array($id_array)?(isset($id_array['id'])?$id_array['id']:''):$id_array);
    if ((0 == $device_id) || ('' == $device_id)) {
        $device_id = bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999));
    }
    if (!$this->ReadDeviceData($device_id, TRUE)) {
      $this->SetDevice($device_id);
      if (is_array($id_array)) {
        if (isset($id_array['id'])) { unset($id_array['id']); }
        // foreach (array() as $key => $value) // this is not working well in PHP4
        reset($id_array);
        while(list($key, $value) = each($id_array)) {
          $this->_device_data[$key] = $value;
        }
      } else { // backward compatibility
        $this->_device_data['description'] = $description;
        $this->_device_data['device_secret'] = $device_secret;
        $this->_device_data['ip_or_fqdn'] = $ip_or_fqdn;
        $this->_device_data['subnet'] = $subnet;
        $this->_device_data['shortname'] = $shortname;
        $this->_device_data['with_radius_update'] = $with_radius_update;
        $this->_device_data['challenge_response_enabled'] = $challenge_response_enabled;
        $this->_device_data['text_token_challenge'] = $text_token_challenge;
        $this->_device_data['sms_challenge_enabled'] = $sms_challenge_enabled;
        $this->_device_data['text_sms_challenge'] = $text_sms_challenge;
        $this->_device_data['cache_result_enabled'] = $cache_result_enabled;
        $this->_device_data['cache_timeout'] = $cache_timeout;
      }
      $result = $this->WriteDeviceData($this->_device_data['with_radius_update']);
    }
    return $result;
  }    


  function ReadDeviceData(
      $device_id = '',
      $create = FALSE
  ) {
      if ('' != $device_id) {
          $this->SetDevice($device_id);
      }
      $result = FALSE;
      
      // We initialize the encryption hash to empty
      $this->_device_data['encryption_hash'] = '';
      
      // First, we read the user file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $device_filename = mb_strtolower($this->GetDevice()).'.db';
          if (!file_exists($this->GetDevicesFolder().$device_filename)) {
              if (!$create) {
                  $this->WriteLog("Error: database file ".$this->GetDevicesFolder().$device_filename." for device ".$this->_device." does not exist", FALSE, FALSE, 39, 'System', '', 3);
              }
          } else {
              if ($file_handler = @fopen($this->GetDevicesFolder().$device_filename, "rt")) {
                  $first_line = trim(fgets($file_handler));
                  
                  while (!feof($file_handler)) {
                      $line = trim(fgets($file_handler));
                      $line_array = explode("=",$line,2);
                      if (":" == substr($line_array[0], -1)) {
                          $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                          $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                      }
                      if ('' != trim($line_array[0])) {
                          $this->_device_data[mb_strtolower($line_array[0])] = $line_array[1];
                      }
                  }
                  
                  fclose($file_handler);
                  $result = TRUE;

                  if ('' != $this->_device_data['encryption_hash']) {
                      if ($this->_device_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                          $this->_device_data['encryption_hash'] = "ERROR";
                          $this->WriteLog("Error: the device information encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                          $result = FALSE;
                      }
                  }
              }
          }
      }

      // And now, we override the values if another backend type is defined
      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_devices_table']) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_devices_table']."` WHERE `device_id` = '".$this->_device."'";
                          $aRow = NULL;
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 40, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = $rResult->fetch_assoc();
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = mysql_fetch_assoc($rResult);
                              }
                          }

                          if (NULL != $aRow) {
                              $result = FALSE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['devices']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['devices'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'device_id')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $this->_device_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $this->_device_data[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the devices database schema", FALSE, FALSE, 42, 'System', '', 3);
                                  }
                                  $result = TRUE;
                              }
                              if (0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for device ".$this->_device." does not exist", FALSE, FALSE, 39, 'System', '', 3);
                              }
                          }
                      }
                      if ('' != $this->_device_data['encryption_hash']) {
                          if ($this->_device_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $this->_device_data['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the devices mysql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                              $result = FALSE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_devices_table']) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_devices_table']."\" WHERE \"device_id\" = '".$this->_device."'";
                          $aRow = NULL;
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $aRow = pg_fetch_assoc($rResult);
                          }

                          if (NULL != $aRow) {
                              $result = FALSE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['devices']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['devices'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'device_id')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $this->_device_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $this->_device_data[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the devices database schema", FALSE, FALSE, 42, 'System', '', 3);
                                  }
                                  $result = TRUE;
                              }
                              if (0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for device ".$this->_device." does not exist", FALSE, FALSE, 39, 'System', '', 3);
                              }
                          }
                      }
                      if ('' != $this->_device_data['encryption_hash']) {
                          if ($this->_device_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $this->_device_data['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the devices pgsql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
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
  } // ReadDeviceData


  // The first parameter is an array (new) with various options or a boolean with_radius_update (old)
  function WriteDeviceData(
      $write_device_data_array = true
  ) {
      if (is_array($write_device_data_array)) {
        if (!isset($write_device_data_array['with_radius_update'])) {
          $write_device_data_array['with_radius_update'] = TRUE;
        }
      } else {
        $temp_array = array();
        $temp_array['with_radius_update'] = $write_device_data_array;
        $write_device_data_array = $temp_array;
      }

      if ('' == trim($this->GetDevice())) {
          $result = false;
      } else {
          $result = $this->WriteData(array_merge(array('item'               => 'Device',
                                                       'table'              => 'devices',
                                                       'folder'             => $this->GetDevicesFolder(),
                                                       'data_array'         => $this->_device_data,
                                                       'force_file'         => false,
                                                       'id_field'           => 'device_id',
                                                       'id_value'           => $this->GetDevice()
                                                      ), $write_device_data_array));
      }
      return $result;
  }


  function SetDevice(
      $device
  ) {
      $this->ResetDeviceArray();
      $this->_device = mb_strtolower($device);
      $this->ReadDeviceData('', TRUE); // First parameter empty, otherwise it will loop with SetDevice !
  }


  function GetDevice() {
      return mb_strtolower($this->_device);
  }


  function IsDeviceCacheResultEnabled(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return (1 == (isset($this->_device_data['cache_result_enabled'])?$this->_device_data['cache_result_enabled']:false));
  }


  function SetDeviceCacheResultEnabled(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetDevice($first_param);
          $value = $second_param;
      }
      $this->_device_data['cache_result_enabled'] = intval($value);

      return $value;
  }


  function GetDeviceCacheResultEnabled(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return intval($this->_device_data['cache_result_enabled']);
  }


  function IsDeviceForceNoPrefixEnabled(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return (1 == (isset($this->_device_data['force_no_prefix_pin'])?$this->_device_data['force_no_prefix_pin']:false));
  }


  function SetDeviceForceNoPrefixEnabled(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetDevice($first_param);
          $value = $second_param;
      }
      $this->_device_data['force_no_prefix_pin'] = intval($value);

      return $value;
  }


  function GetDeviceForceNoPrefixEnabled(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return intval($this->_device_data['force_no_prefix_pin']);
  }


  function SetDeviceCacheTimeout(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetDevice($first_param);
          $result = $second_param;
      }
      $this->_device_data['cache_timeout'] = intval($result);

      return $result;
  }


  function GetDeviceCacheTimeout(
      $device = ''
  ) {
      if ($device != '') {
          $this->SetDevice($device);
      }
      return intval($this->_device_data['cache_timeout']);
  }


  function SetDeviceDescription(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetDevice($first_param);
          $result = $second_param;
      }
      $this->_device_data['description'] = $result;

      return $result;
  }


  function GetDeviceDescription(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['description'];
  }


  function SetDeviceShortname(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetDevice($first_param);
          $result = $second_param;
      }
      $this->_device_data['shortname'] = $result;

      return $result;
  }


  function GetDeviceShortname(
      $device = ''
  ) {
      if ($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['shortname'];
  }


  function SetDeviceIpOrFqdn(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetDevice($first_param);
          $result = $second_param;
      }
      $this->_device_data['ip_or_fqdn'] = $result;

      return $result;
  }


  function GetDeviceIpOrFqdn(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['ip_or_fqdn'];
  }


  function SetDeviceSubnet(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetDevice($first_param);
          $result = $second_param;
      }
      $this->_device_data['subnet'] = $result;

      return $result;
  }


  function GetDeviceSubnet(
      $device = ''
  ) {
      if ($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['subnet'];
  }


  function SetDeviceSecret(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetDevice($first_param);
          $result = $second_param;
      }
      $this->_device_data['device_secret'] = $result;

      return $result;
  }


  function GetDeviceSecret(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['device_secret'];
  }


  function SetDeviceChallengeEnabled(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetDevice($first_param);
          $value = $second_param;
      }
      $this->_device_data['challenge_response_enabled'] = intval($value);

      return $value;
  }


  function GetDeviceChallengeEnabled(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return intval($this->_device_data['challenge_response_enabled']);
  }


  function SetDeviceTextTokenChallenge(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetDevice($first_param);
          $value = $second_param;
      }
      $this->_device_data['text_token_challenge'] = $value;

      return $value;
  }


  function GetDeviceTextTokenChallenge(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['text_token_challenge'];
  }


  function SetDeviceSmsChallengeEnabled(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetDevice($first_param);
          $value = $second_param;
      }
      $this->_device_data['sms_challenge_enabled'] = intval($value);

      return $value;
  }


  function GetDeviceSmsChallengeEnabled(
      $device = ''
  ) {
      if($device != '') {
          $this->SetDevice($device);
      }
      return intval($this->_device_data['sms_challenge_enabled']);
  }


  function SetDeviceTextSmsChallenge(
      $first_param,
      $second_param = "*-*"
  ) {
      $value = "";
      if ($second_param == "*-*") {
          $value = $first_param;
      } else {
          $this->SetDevice($first_param);
          $value = $second_param;
      }
      $this->_device_data['text_sms_challenge'] = $value;

      return $value;
  }


  function GetDeviceTextSmsChallenge(
      $device = ''
  ) {
      if ($device != '') {
          $this->SetDevice($device);
      }
      return $this->_device_data['text_sms_challenge'];
  }


  function DeleteDevice(
      $device = '',
      $no_error_info = FALSE
  ) {
      if ('' != $device) {
          $this->SetDevice($device);
      }
      
      $result = FALSE;
      
      // First, we delete the device file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $device_filename = $this->GetDevice().'.db';
          if (!file_exists($this->GetDevicesFolder().$device_filename)) {
              if (!$no_error_info) {
                  $this->WriteLog("Error: Unable to delete device ".$this->_device.", database file ".$this->GetDevicesFolder().$device_filename." does not exist", FALSE, FALSE, 28, 'System', '');
              }
          } else {
              $result = unlink($this->GetDevicesFolder().$device_filename);
              if ($result) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *Device ".$this->_device." successfully deleted", FALSE, FALSE, 19, 'Device', '');
                  }
              } else {
                  if (!$no_error_info) {
                      $this->WriteLog("Error: Unable to delete device ".$this->_device, FALSE, FALSE, 28, 'Device', '');
                  }
              }
          }
      }

      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_devices_table']) {
                          $sQuery  = "DELETE FROM `".$this->_config_data['sql_devices_table']."` WHERE `device_id` = '".$this->_device."'";
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  if (!$no_error_info) {
                                      $this->WriteLog("Error: Could not delete device ".$this->_device.": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'System', '');
                                  }
                              } else {
                                  $num_rows = $this->_mysqli->affected_rows;
                              }
                          } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete device ".$this->_device.": ".mysql_error(), FALSE, FALSE, 28, 'System', '');
                              }
                          } else {
                              $num_rows = mysql_affected_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete device ".$this->_device.". Device does not exist", FALSE, FALSE, 28, 'System', '');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Info: *Device ".$this->_device." successfully deleted", FALSE, FALSE, 19, 'Device', '');
                              }
                              $result = TRUE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_devices_table']) {
                          $sQuery  = "DELETE FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_devices_table']."\" WHERE \"device_id\" = '".$this->_device."'";
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete device ".$this->_device.": ".pg_last_error(), FALSE, FALSE, 28, 'System', '');
                              }
                          } else {
                              $num_rows = pg_affected_rows($rResult);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete device ".$this->_device.". Device does not exist", FALSE, FALSE, 28, 'System', '');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
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
      if ($result) {
          $this->TouchFolder('data',
                             'Device',
                             $this->GetDevice(),
                             TRUE,
                             "DeleteDevice");
      }
      return $result;
  }


  function GetDevicesList()
  {
      return $this->GetList('device_id', 'sql_devices_table', $this->GetDevicesFolder());
  }
  
  
  function GetDevicesCount()
  {
      if (($this->IsCacheData()) && (intval($this->ReadCacheValue('devices_count')) >= 0))
      {
          $devices_count = intval($this->ReadCacheValue('devices_count'));
      }
      else
      {
          $devices_count = 0;
          switch ($this->GetBackendType())
          {
              case 'mysql':
                  if ($this->OpenMysqlDatabase())
                  {
                      $sQuery  = "SELECT device_id FROM `".$this->_config_data['sql_devices_table']."` ";
                      
                      if (is_object($this->_mysqli))
                      {
                          if (!($result = $this->_mysqli->query($sQuery)))
                          {
                              $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              $result = FALSE;
                          }
                          else
                          {
                              while ($aRow = $result->fetch_assoc())
                              {
                                  $devices_count++;
                              }
                          }
                      }
                      else
                      {
                          if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                          {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          }
                          else
                          {
                              while ($aRow = mysql_fetch_assoc($rResult))
                              {
                                  $devices_count++;
                              }                         
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase())
                  {
                      $sQuery  = "SELECT \"device_id\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_devices_table']."\" ";
                      
                      if (!($rResult = pg_query($sQuery, $this->_pgsql_database_link)))
                      {
                          $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                      }
                      else
                      {
                          while ($aRow = pg_fetch_assoc($rResult))
                          {
                              $devices_count++;
                          }                         
                      }
                  }
                  break;
              case 'files':
              default:
                  if ($devices_handle = @opendir($this->GetDevicesFolder()))
                  {
                      while ($file = readdir($devices_handle))
                      {
                          if ((substr($file, -3) == ".db") && ($file != '.db'))
                          {
                              $devices_count++;
                          }
                      }
                      closedir($devices_handle);
                  }
          }
          if (($this->IsCacheData()) && ($devices_count >= 0))
          {
              $this->WriteCacheValue('devices_count', $devices_count);
              $this->WriteCacheData();
          }
      }
      return $devices_count;
  }



  /***************************
   ***************************
   ***************************
   ***   GROUPS HANDLING   ***
   ***************************
   ***************************
   ***************************/

  function CreateGroup(
      $id = '',
      $name = '',
      $description = ''
  ) {
      if ("" != trim($name)) {
          $group_id = $id;
          if (('' == $group_id) || ('0' == $group_id)) {
              $group_id = bigdec2hex((time()-mktime(1,1,1,1,1,2000)).mt_rand(10000,99999));
          }
          if ($this->CheckGroupExists($group_id)) {
              return FALSE; // ERROR: group already exists.
          } else {
              $this->SetGroup($group_id);
              $this->SetGroupName(trim($name));
              $this->SetGroupDescription($description);
              return $this->WriteGroupData();
          }
      } else {
          return TRUE;
      }
  }    


  function ReadGroupData(
      $group_id = '',
      $create = FALSE
  ) {
      if ('' != $group_id) {
          $this->SetGroup($group_id);
      }
      $result = FALSE;
      
      // We initialize the encryption hash to empty
      $this->_group_data['encryption_hash'] = '';
      
      // First, we read the user file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $group_filename = $this->EncodeFileId($this->GetGroup(), FALSE).'.db';
          if (!file_exists($this->GetGroupsFolder().$group_filename)) {
              $group_filename = $this->EncodeFileId($this->GetGroup(), FALSE, TRUE).'.db';
          }
          if (!file_exists($this->GetGroupsFolder().$group_filename)) {
              if (!$create) {
                  $this->WriteLog("Error: database file ".$this->GetGroupsFolder().$group_filename." for group ".$this->_group." does not exist", FALSE, FALSE, 39, 'System', '', 3);
              }
          } else {
              if ($file_handler = @fopen($this->GetGroupsFolder().$group_filename, "rt")) {
                  $first_line = trim(fgets($file_handler));
                  
                  while (!feof($file_handler)) {
                      $line = trim(fgets($file_handler));
                      $line_array = explode("=",$line,2);
                      if (":" == substr($line_array[0], -1)) {
                          $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                          $line_array[1] = $this->Decrypt($line_array[0],$line_array[1],$this->GetEncryptionKey());
                      }
                      if ('' != trim($line_array[0])) {
                          $this->_group_data[mb_strtolower($line_array[0])] = $line_array[1];
                      }
                  }
                  
                  fclose($file_handler);
                  $result = TRUE;

                  if ('' != $this->_group_data['encryption_hash']) {
                      if ($this->_group_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                          $this->_group_data['encryption_hash'] = "ERROR";
                          $this->WriteLog("Error: the group information encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                          $result = FALSE;
                      }
                  }
              }
          }
      }

      // And now, we override the values if another backend type is defined
      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_groups_table']) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_groups_table']."` WHERE `group_id` = '".$this->_group."'";
                          $aRow = NULL;
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".trim($this->_mysqli->error).' ', TRUE, FALSE, 40, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = $rResult->fetch_assoc();
                              }
                          } else {
                              if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                                  $this->WriteLog("Error: SQL query error ($sQuery) : ".mysql_error(), TRUE, FALSE, 40, 'System', '', 3);
                                  $result = FALSE;
                              } else {
                                  $aRow = mysql_fetch_assoc($rResult);
                              }
                          }

                          if (NULL != $aRow) {
                              $result = FALSE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['groups']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['groups'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'group_id')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $this->_group_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $this->_group_data[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the groups database schema", FALSE, FALSE, 42, 'System', '', 3);
                                  }
                                  $result = TRUE;
                              }
                              if (0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for group ".$this->_group." does not exist", FALSE, FALSE, 39, 'System', '', 3);
                              }
                          }
                      }
                      if ('' != $this->_group_data['encryption_hash']) {
                          if ($this->_group_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $this->_group_data['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the groups mysql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
                              $result = FALSE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_groups_table']) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_groups_table']."\" WHERE \"group_id\" = '".$this->_group."'";
                          $aRow = NULL;
                      
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: SQL query error ($sQuery) : ".pg_last_error(), TRUE, FALSE, 40, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $aRow = pg_fetch_assoc($rResult);
                          }

                          if (NULL != $aRow) {
                              $result = FALSE;
                              while(list($key, $value) = @each($aRow)) {
                                  $in_the_schema = FALSE;
                                  reset($this->_sql_tables_schema['groups']);
                                  while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['groups'])) {
                                      if ($valid_key == $key) {
                                          $in_the_schema = TRUE;
                                          break;
                                      }
                                  }
                                  if (($in_the_schema) && ($key != 'group_id')) {
                                      if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4))) {
                                          $value = substr($value,4);
                                          $value = substr($value,0,strlen($value)-4);
                                          $this->_group_data[$key] = $this->Decrypt($key,$value,$this->GetEncryptionKey());
                                      } else {
                                          $this->_group_data[$key] = $value;
                                      }
                                  } elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag()) {
                                      $this->WriteLog("Warning: *The key ".$key." is not in the groups database schema", FALSE, FALSE, 42, 'System', '', 3);
                                  }
                                  $result = TRUE;
                              }
                              if (0 == count($aRow) && !$create) {
                                  $this->WriteLog("Error: SQL database entry for group ".$this->_group." does not exist", FALSE, FALSE, 39, 'System', '', 3);
                              }
                          }
                      }
                      if ('' != $this->_group_data['encryption_hash']) {
                          if ($this->_group_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey())) {
                              $this->_group_data['encryption_hash'] = "ERROR";
                              $this->WriteLog("Error: the groups pgsql encryption key is not matching", FALSE, FALSE, 33, 'System', '');
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


  function WriteGroupData(
    $write_group_data_array = array()
  ) {
      if ('' == trim($this->GetGroup())) {
          $result = false;
      } else {
          $result = $this->WriteData(array_merge(array('item'               => 'Group',
                                                       'table'              => 'groups',
                                                       'folder'             => $this->GetGroupsFolder(),
                                                       'data_array'         => $this->_group_data,
                                                       'force_file'         => false,
                                                       'id_field'           => 'group_id',
                                                       'id_value'           => $this->GetGroup()
                                                      ), $write_group_data_array));
      }
      return $result;
  }


  function SetGroup(
      $group
  ) {
      $this->ResetGroupArray();
      $this->_group = mb_strtolower($group);
      $this->ReadGroupData('', TRUE); // First parameter empty, otherwise it will loop with SetGroup !
  }


  function GetGroup()
  {
      return mb_strtolower($this->_group);
  }


  function SetGroupDescription(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = $first_param;
      } else {
          $this->SetGroup($first_param);
          $result = $second_param;
      }
      $result = $this->EncodeForBackend($result);
      $this->_group_data['description'] = $result;

      return $result;
  }


  function SetGroupName(
      $first_param,
      $second_param = "*-*"
  ) {
      $result = "";
      if ($second_param == "*-*") {
          $result = trim($first_param);
      } else {
          $this->SetGroup($first_param);
          $result = trim($second_param);
      }
      $this->_group_data['name'] = $result;

      return $result;
  }


  function GetGroupDescription(
      $group = ''
  ) {
      if($group != '') {
          $this->SetGroup($group);
      }
      return $this->_group_data['description'];
  }


  function GetGroupName(
      $group = ''
  ) {
      if($group != '') {
          $this->SetGroup($group);
      }
      return $this->_group_data['name'];
  }


  function DeleteGroup(
      $group = '',
      $no_error_info = FALSE
  ) {
      if ('' != $group) {
          $this->SetGroup($group);
      }
      
      $result = FALSE;
      
      // First, we delete the group file if the backend is files or when migration is enabled
      if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile())) {
          $group_filename = $this->EncodeFileId($this->GetGroup(), FALSE).'.db';
          if (!file_exists($this->GetGroupsFolder().$group_filename)) {
              $group_filename = $this->EncodeFileId($this->GetGroup(), FALSE, TRUE).'.db';
          }
          if (!file_exists($this->GetGroupsFolder().$group_filename)) {
              if (!$no_error_info) {
                  $this->WriteLog("Error: Unable to delete group ".$this->_group.", database file ".$this->GetGroupsFolder().$group_filename." does not exist", FALSE, FALSE, 28, 'System', '');
              }
          } else {
              $result = unlink($this->GetGroupsFolder().$group_filename);
              if ($result) {
                  if ($this->GetVerboseFlag()) {
                      $this->WriteLog("Info: *Group ".$this->_group." successfully deleted", FALSE, FALSE, 19, 'Group', '');
                  }
              } else {
                  if (!$no_error_info) {
                      $this->WriteLog("Error: Unable to delete group ".$this->_group, FALSE, FALSE, 28, 'Group', '');
                  }
              }
          }
      }

      if ($this->GetBackendTypeValidated()) {
          switch ($this->_config_data['backend_type']) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      if ('' != $this->_config_data['sql_groups_table']) {
                          $sQuery  = "DELETE FROM `".$this->_config_data['sql_groups_table']."` WHERE `group_id` = '".$this->_group."'";
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  if (!$no_error_info) {
                                      $this->WriteLog("Error: Could not delete group ".$this->_group.": ".trim($this->_mysqli->error), FALSE, FALSE, 28, 'System', '');
                                  }
                              } else {
                                  $num_rows = $this->_mysqli->affected_rows;
                              }
                          } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete group ".$this->_group.": ".mysql_error(), FALSE, FALSE, 28, 'System', '');
                              }
                          } else {
                              $num_rows = mysql_affected_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete group ".$this->_group.". Group does not exist", FALSE, FALSE, 28, 'Group', '');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
                                  $this->WriteLog("Info: *Group ".$this->_group." successfully deleted", FALSE, FALSE, 19, 'Group', '');
                              }
                              $result = TRUE;
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      if ('' != $this->_config_data['sql_groups_table']) {
                          $sQuery  = "DELETE FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_groups_table']."\" WHERE \"group_id\" = '".$this->_group."'";
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete group ".$this->_group.": ".pg_last_error(), FALSE, FALSE, 28, 'System', '');
                              }
                          } else {
                              $num_rows = pg_affected_rows($rResult);
                          }
                          
                          if (0 == $num_rows) {
                              if (!$no_error_info) {
                                  $this->WriteLog("Error: Could not delete group ".$this->_group.". Group does not exist", FALSE, FALSE, 28, 'Group', '');
                              }
                          } else {
                              if ($this->GetVerboseFlag()) {
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
      if ($result) {
          $this->TouchFolder('data',
                             'Group',
                             $this->GetGroup(),
                             TRUE,
                             "DeleteGroup");
      }
      return $result;
  }


  // Check if group exists
  function CheckGroupExists(
      $group = ''
  ) {
      $check_group = ('' != $group)?$group:$this->GetGroup();
      $result = FALSE;

      if ('' != trim($check_group)) {
          if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_groups_table'])) || ('files' == $this->GetBackendType())) {
              switch ($this->GetBackendType()) {
                  case 'mysql':
                      if ($this->OpenMysqlDatabase()) {
                          $sQuery  = "SELECT * FROM `".$this->_config_data['sql_groups_table']."` WHERE `group_id` = '{$check_group}'";
                          
                          if (is_object($this->_mysqli)) {
                              if (!($rResult = $this->_mysqli->query($sQuery))) {
                                  $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              } else {
                                  $num_rows = $rResult->num_rows;
                              }
                          } elseif (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              $num_rows = mysql_num_rows($this->_mysql_database_link);
                          }
                          
                          if (0 == $num_rows) {
                              $this->WriteLog("Error: Group ".$group.". does not exist", FALSE, FALSE, 39, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $result = TRUE;
                          }
                      }
                      break;
                  case 'pgsql':
                      if ($this->OpenPGSQLDatabase()) {
                          $sQuery  = "SELECT * FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data['sql_groups_table']."\" WHERE \"group_id\" = '{$check_group}'";
                          
                          if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              $num_rows = pg_num_rows($rResult);
                          }
                          
                          if (0 == $num_rows) {
                              $this->WriteLog("Error: Group ".$group.". does not exist", FALSE, FALSE, 39, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              $result = TRUE;
                          }
                      }
                      break;
                  case 'files':
                  default:
                      $group_filename = $this->EncodeFileId($check_group, FALSE).'.db';
                      if (!file_exists($this->GetGroupsFolder().$group_filename)) {
                          $group_filename = $this->EncodeFileId($check_group, FALSE, TRUE).'.db';
                      }
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


  function GetList(
      $raw_id,
      $table_name,
      $folder
  ) {
      $list = '';
      $list_array = array();
      if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data[$table_name])) || ('files' == $this->GetBackendType())) {
          switch ($this->GetBackendType()) {
              case 'mysql':
                  if ($this->OpenMysqlDatabase()) {
                      $sQuery = "SELECT `".$raw_id."` FROM `".$this->_config_data[$table_name]."`";
                      $sQuery.= " ORDER BY `".$raw_id."` ASC";
                      if (is_object($this->_mysqli)) {
                          if (!($result = $this->_mysqli->query($sQuery))) {
                              $this->WriteLog("Error: Unable to access the database: ".trim($this->_mysqli->error), FALSE, FALSE, 41, 'System', '', 3);
                              $result = FALSE;
                          } else {
                              while ($aRow = $result->fetch_assoc()) {
                                  if ('' != $aRow[$raw_id]) {
                                      $list.= (('' != $list)?"\t":'').$aRow[$raw_id];
                                  }
                              }
                          }
                      } else {
                          if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link))) {
                              $this->WriteLog("Error: Unable to access the database: ".mysql_error(), FALSE, FALSE, 41, 'System', '', 3);
                          } else {
                              while ($aRow = mysql_fetch_assoc($rResult)) {
                                  if ('' != $aRow[$raw_id]) {
                                      $list.= (('' != $list)?"\t":'').$aRow[$raw_id];
                                  }
                              }                         
                          }
                      }
                  }
                  break;
              case 'pgsql':
                  if ($this->OpenPGSQLDatabase()) {
                      $sQuery = "SELECT \"".$raw_id."\" FROM \"".$this->_config_data['sql_schema']."\".\"".$this->_config_data[$table_name]."\"";
                      $sQuery.= " ORDER BY \"".$raw_id."\" ASC";
                      if (!($rResult = pg_query($this->_pgsql_database_link, $sQuery))) {
                          $this->WriteLog("Error: Unable to access the database: ".pg_last_error(), FALSE, FALSE, 41, 'System', '', 3);
                      } else {
                          while ($aRow = pg_fetch_assoc($rResult)) {
                              if ('' != $aRow[$raw_id]) {
                                  $list.= (('' != $list)?"\t":'').$aRow[$raw_id];
                              }
                          }                         
                      }
                  }
                  break;
              case 'files':
              default:
                  if ($file_handle = @opendir($folder)) {
                      while ($file = readdir($file_handle)) {
                          if ((substr($file, -3) == ".db") && ($file != '.db')) {
                              array_push($list_array, substr($file,0,-3));
                          }
                      }
                      sort($list_array);
                      foreach($list_array as $one_list) {
                          $list.= (('' != $list)?"\t":'').$this->DecodeFileId($one_list);
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
      // Is it potentially a Raspberry Pi 2 or a BeagleBone Black ?
      if (false !== mb_strpos(mb_strtolower($os_running), 'armv8')) {
          $type = 'RP3'; // Raspberry Pi 3 (BCM2709)
      } elseif (FALSE !== mb_strpos(mb_strtolower($os_running), 'armv7l')) {
        $hardware = '';
        exec("cat /proc/cpuinfo | grep --color=never -i Hardware", $output);
        foreach($output as $line) {
          $line.= "  ";
          if (preg_match("/^Hardware\s*:\s*(.*)/", $line)) {
            preg_match_all("/^Hardware\s*:\s*(.*)/", $line, $result_array, PREG_SET_ORDER);
            if (isset($result_array[0][1])) {
              $hardware = mb_strtoupper(trim($result_array[0][1]));
              break;
            }
          }
        }
        if (FALSE !== mb_strpos(mb_strtolower($hardware), 'bcm27')) {
                  // Raspberry Pi (BCM 2709)
                  $lscpu = '';
                  exec("/usr/bin/lscpu | grep --color=never -i \"CPU max MHz\"", $output);
                  foreach($output as $line) {
                      $line.= "  ";
                      if (preg_match("/^CPU max MHz\s*:\s*(.*)/", $line)) {
                          preg_match_all("/^CPU max MHz\s*:\s*(.*)/", $line, $result_array, PREG_SET_ORDER);
                          if (isset($result_array[0][1])) {
                              $lscpu = mb_strtoupper(trim($result_array[0][1]));
                              break;
                          }
                      }
                  }
                  if (false !== mb_strpos(mb_strtolower($lscpu), '1200')) {
                      $type = 'RP3'; // Raspberry Pi 3
                  } else {
                      $type = 'RP2'; // Raspberry Pi 2
                  }
        } else {
          $type = 'BBB'; // Beaglebone Black (Generic AM33XX and others)
        }
      // Is it potentially a Raspberry Pi B/B+ ?
      } elseif (FALSE !== mb_strpos(mb_strtolower($os_running), 'armv6l')) {
          $type = 'RPI';
      // Is it potentially a Windows development platform ?
  } elseif (mb_strtolower(substr(PHP_OS, 0, 3)) === 'win') {
          $type = "DVP";
      // Is it a virtual appliance and/or a Linux Debian edition
      } elseif (FALSE !== mb_strpos(mb_strtolower($os_running), 'debian')) {
          $type = 'VAP';
      }
      return $type;
  }


  function GetRaspberryPiSerialNumber()
  {
      $serial = '';
      exec("cat /proc/cpuinfo | grep --color=never -i Serial", $output);
      foreach($output as $line) {
          $line.= "  ";
          if (preg_match("/^Serial\s*:\s*(.*)/", $line)) {
              preg_match_all("/^Serial\s*:\s*(.*)/", $line, $result_array, PREG_SET_ORDER);
              if (isset($result_array[0][1])) {
                  $serial = mb_strtoupper(trim($result_array[0][1]));
                  break;
              }
          }
      }
      return $serial;
  }


  function ReadUserDataOnServer(
      $user
  ) {
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
      $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>', $xml_data);
      $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
      $xml_data = str_replace('*UserId*', $user, $xml_data);
      
      $xml_urls = $this->GetServerUrl();
      $xml_timeout = $this->GetServerTimeout();
      $xml_data_encoded = urlencode($xml_data);
      
      $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

      if (FALSE !== $response) {
          if ($this->_xml_dump_in_log) {
              $this->WriteLog("Info: Host returned the following answer: $response", FALSE, FALSE, 8888, 'Debug', '');
          }
          
          if (FALSE !== mb_strpos($response,'<multiOTP')) {
              $error_code = 99;
              
              //Set up the parser object
              $xml = new MultiotpXmlParser($response);

              //Parse it !
              $xml->Parse();

              if (isset($xml->document->errorcode[0])) {
                  $server_password = (isset($xml->document->serverpassword[0]) ? ($xml->document->serverpassword[0]->tagData) : '');
                  
                  if ($server_password != md5('ReadUserData'.$this->GetServerSecret().$this->GetServerChallenge())) {
                      $error_code = 70;
                  } else {
                      $error_code = (isset($xml->document->errorcode[0]) ? intval($xml->document->errorcode[0]->tagData) : 99);
                  }
                  $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->GetErrorText($error_code));

                  if ($this->_xml_dump_in_log) {
                      $this->WriteLog("Info: Host returned the following result: $error_code ($error_description)", FALSE, FALSE, $error_code, 'Debug', '');
                  }
              }
              if ((19 == intval($error_code)) && (isset($xml->document->user[0]))) {
                  $result = (isset($xml->document->user[0]->userdata[0])?($xml->document->user[0]->userdata[0]->tagData):'');
              } else {
                  $this->WriteLog("Error: Host answers with the following error code: $error_code ($error_description)", FALSE, FALSE, 39, 'Client-Server', '', 3);
                  $result = intval($error_code);
              }
          } else {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Error: *Host sent an incorrect answer: $response", FALSE, FALSE, 39, 'Client-Server', '', 3);
              }
          }
      }
      return $result;
  }


  function CheckUserExistsOnServer(
      $user = ''
  ) {
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
      $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>', $xml_data);
      $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
      $xml_data = str_replace('*UserId*', $user, $xml_data);
      
      $xml_urls = $this->GetServerUrl();
      $xml_timeout = $this->GetServerTimeout();
      $xml_data_encoded = urlencode($xml_data);
      
      $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

      if (FALSE !== $response) {
          if ($this->_xml_dump_in_log) {
              $this->WriteLog("Info: Host returned the following answer: $response", FALSE, FALSE, 8888, 'Debug', '');
          }
          
          if (FALSE !== mb_strpos($response,'<multiOTP')) {
              $error_code = 99;
              
              //Set up the parser object
              $xml = new MultiotpXmlParser($response);

              //Parse it !
              $xml->Parse();

              if (isset($xml->document->errorcode[0])) {
                  $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                  
                  if ($server_password != md5('CheckUserExists'.$this->GetServerSecret().$this->GetServerChallenge())) {
                      $error_code = 70;
                  } else {
                      $error_code = (isset($xml->document->errorcode[0]) ? intval($xml->document->errorcode[0]->tagData) : 99);
                  }
                  $error_description = (isset($xml->document->errordescription[0]) ? ($xml->document->errordescription[0]->tagData) : $this->GetErrorText($error_code));

                  if ($this->_xml_dump_in_log) {
                      $this->WriteLog("Info: Host returned the following result: $error_code ($error_description).", FALSE, FALSE, $error_code, 'Debug', '');
                  }
              }
              // User doesn't exist: 21 - User exists = 22
              $result = intval($error_code);
          } else {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Error: *Host sent an incorrect answer: $response", FALSE, FALSE, 8888, 'Client-Server', '');
              }
          }
      }
      return $result;
  }


  function CheckUserTokenOnServer(
      $user,
      $password,
      $auth_method = "PAP",
      $id= '',
      $challenge = '',
      $response2 = ''
  ) {
      $result = 72;
      
      /* This option is too long
      if (function_exists('openssl_random_pseudo_bytes')) {
          $server_challenge = 'MOSH'.bin2hex(openssl_random_pseudo_bytes(16));
      } else {
      */
          $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().mt_rand(100000,999999));
      /* } */
      $this->SetServerChallenge($server_challenge);

      switch (mb_strtoupper($auth_method)) {
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
      $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>', $xml_data);
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

      if (FALSE !== $response) {
          if ($this->_xml_dump_in_log) {
              $this->WriteLog("Debug: Host returned the following answer: $response", FALSE, FALSE, 8888, 'Debug', '');
          }

          if (FALSE !== mb_strpos($response,'<multiOTP')) {
              $result = 99;
              $error_code = 99;
              
              //Set up the parser object
              $xml = new MultiotpXmlParser($response);

              //Parse it !
              $xml->Parse();

              if (isset($xml->document->errorcode[0])) {
                  $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                  
                  if ($server_password != md5('CheckUserToken'.$this->GetServerSecret().$this->GetServerChallenge())) {
                      $error_code = 70;
                  } else {
                      $error_code = (isset($xml->document->errorcode[0]) ? intval($xml->document->errorcode[0]->tagData) : 99);
                  }
                  $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->GetErrorText(intval($error_code)));
                  $result = intval($error_code);

                  if ($this->_xml_dump_in_log) {
                      $this->WriteLog("Info: Host returned the following result: $result ($error_description).", FALSE, FALSE, $result, 'Debug', '');
                  }
              }

              if ((intval(0) == intval($error_code)) && (isset($xml->document->cache[0]))) {
                  if (isset($xml->document->cache[0]->user[0])) {
                      foreach ($xml->document->cache[0]->user as $one_user) {
                          $current_user = isset($one_user->tagAttrs['userid'])?$one_user->tagAttrs['userid']:'';
                          if ('' != $current_user) {
                              $current_user_data = isset($one_user->userdata[0])?$one_user->userdata[0]->tagData:'';
                              if ('' != $current_user_data) {
                                  $this->SetUser($current_user);
                                  $this->_user_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
                                  $current_user_array = explode("\n",$current_user_data);

                                  foreach ($current_user_array as $one_line) {
                                      $line = trim($one_line);
                                      $line_array = explode("=",$line,2);
                                      if (":" == substr($line_array[0], -1)) {
                                          $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                          $line_array[1] = $this->Decrypt($line_array[0], $line_array[1], $this->GetServerSecret());
                                      }
                                      if ('' != trim($line_array[0])) {
                                          if ('encryption_hash' != mb_strtolower($line_array[0])) {
                                              $this->_user_data[mb_strtolower($line_array[0])] = $line_array[1];
                                          }
                                      }
                                  }
                                  $this->WriteUserData();
                              }
                          }
                      }
                  }
              }
          } else {
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Error: *Host sent an incorrect answer: $response", FALSE, FALSE, 8888, 'Client-Server', '');
              }
          }
      }
      $this->SetUser($user);
      return $result;
  }


  /**
   * @brief   Pure PHP standalone HTTP request function
   *
   * @param   string  $xml_data           Complete data to be posted
   *          string  $xml_urls           Urls where to post, post to the next one in case of an error (separated by ;)
   *          string  $xml_timeout        Timeout before changing to the next server
   *          string  $xml_urls_splitter  String splitter between two Urls (default is ;)
   * @retval  string  Content received from the server (must contain <multiOTP to be valid)
   *
   * @author  Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
   * @version 5.0.5.0
   * @date    2017-08-01
   * @since   2010-07-18
   *
   * Better SSL support starting with 5.0.2.5
   */
  function PostHttpDataXmlRequest(
      $xml_data,
      $xml_urls,
      $xml_timeout = 3,
      $xml_urls_splitter = ";",
      $no_multiotp_validity_check = FALSE
  ) {
      $result = FALSE;
      $content_to_post = 'data='.$xml_data;

      // Generic cleaner of multiple URLs
      $cleaned_xml_urls = trim(str_replace(" ",$xml_urls_splitter,str_replace(",",$xml_urls_splitter,str_replace(";",$xml_urls_splitter,$xml_urls))));
      $xml_url = explode($xml_urls_splitter,$cleaned_xml_urls);
      
      foreach ($xml_url as $xml_url_one) {
          $server_to_ban = substr($xml_url_one, 0, mb_strpos($xml_url_one."?", "?"));
          $skip = $this->IsTemporaryBadServer($server_to_ban);
          
          if (!$skip) {
              $port = 80;

              $pos = mb_strpos($xml_url_one, '://');
              if (FALSE === $pos) {
                  $protocol = '';
              } else {
                  switch (mb_strtolower(substr($xml_url_one,0,$pos))) {
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
              
              $pos = mb_strpos($xml_url_one, '/');
              if (FALSE === $pos) {
                  $host = $xml_url_one;
                  $url = '/';
              } else {
                  $host = substr($xml_url_one,0,$pos);
                  $url = substr($xml_url_one,$pos); // And not +1 as we want the / at the beginning
              }
              
              $pos = mb_strpos($host, ':');
              if (FALSE !== $pos) {
                  $port = substr($host,$pos+1);
                  $host = substr($host,0,$pos);
              }
              
              $errno = 0;
              $errdesc = 0;
              if (function_exists("stream_socket_client")) {
                  $sslContext = stream_context_create($this->_default_ssl_context);
                  $fp = @stream_socket_client($protocol.$host.":".$port, $errno, $errdesc, $xml_timeout, STREAM_CLIENT_CONNECT, $sslContext);
              } else {
                  $fp = @fsockopen($protocol.$host, $port, $errno, $errdesc, $xml_timeout);
              }
              if (FALSE !== $fp) {
                  $info['timed_out'] = FALSE;
                  fputs($fp, "POST ".$url." HTTP/1.0\r\n");
                  fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
                  fputs($fp, "Content-Length: ".strlen($content_to_post)."\r\n");
                  fputs($fp, "User-Agent: multiOTP\r\n");
                  fputs($fp, "Host: ".$host."\r\n");
                  fputs($fp, "\r\n");
                  fputs($fp, $content_to_post);
                  fputs($fp, "\r\n");

                  // At least 20 seconds before the stream timeout (the server can be reached if we are here)
                  $stream_timeout = $xml_timeout;
                  if ($stream_timeout < 20) {
                      $stream_timeout = 20;
                  }
                  stream_set_blocking($fp, TRUE);
                  stream_set_timeout($fp, $stream_timeout);
                  $info = stream_get_meta_data($fp); 
          
                  $reply = '';
                  $last_length = 0;
                  while ((!feof($fp)) && ((!$info['timed_out']) || ($last_length != strlen($reply)))) {
                      $last_length = strlen($reply);
                      $reply.= fgets($fp, 1024);
                      $info = stream_get_meta_data($fp);
                      @ob_flush(); // Avoid notice if any (if the buffer is empty and therefore cannot be flushed)
                      flush(); 
                  }
                  fclose($fp);

                  if ($info['timed_out']) {
                      $this->WriteLog("Warning: timeout after $xml_timeout seconds for $protocol$host:$port with a result code of $errno ($errdesc).", FALSE, FALSE, 8888, 'Client-Server', '');
                  } else {
                      $pos = mb_strpos(mb_strtolower($reply), "\r\n\r\n");
                      $header = substr($reply, 0, $pos);
                      $answer = substr($reply, $pos + 4);
                      $header_array = explode(" ", $header."   ");
                      $status = intval($header_array[1]);

                      $this->SetLastHttpStatus($status);

                      $result = $answer;
                      if ($errno > 0) {
                          $this->WriteLog("Info: $protocol$host:$port returns a resultcode of $errno ($errdesc).", FALSE, FALSE, 8888, 'Client-Server', '');
                      }
                      if ((FALSE !== mb_strpos($result, '<multiOTP')) || $no_multiotp_validity_check) {
                          break; // Break of the foreach loop
                      }
                  }
                  // If we are here, something was bad with the actual server
                  $this->AddTemporaryBadServer($server_to_ban, time());
                  $log_info = "Info: temporary adding $server_to_ban to the list of banned servers, content not recognized (".substr($result, 0, 80)."...)";
                  if ($this->_xml_dump_in_log) {
                      $log_info.= ": ".$result;
                  }

                  $this->WriteLog($log_info, FALSE, FALSE, 8888, 'Client-Server', '');
              } else {
                  $this->AddTemporaryBadServer($server_to_ban, time());
                  $this->WriteLog("Warning: Host $protocol$host on port $port not reached before a timeout of $xml_timeout seconds.", FALSE, FALSE, 8888, 'Client-Server', '');
              }
          } else {
              // This server has been skipped
              $this->WriteLog("Info: temporary skipping $xml_url_one due to previous timeout or inconsistent response.", FALSE, FALSE, 8888, 'Client-Server', '');
              $result = "";
          }
      } // foreach

      if (FALSE === mb_strpos($result,'<multiOTP')) {
          $this->_servers_last_timeout = time();

          if ($this->_xml_dump_in_log) {
              $this->WriteLog("Debug: timeout detected.", FALSE, FALSE, 8888, 'Debug', '');
          }
      }
      return $result;
  }


  // SOAP functions
  function SoapOpenotpNormalLogin(
      $username = "",
      $domain = "",
      $ldapPassword = "",
      $otpPassword = "",
      $client = "",
      $source = "",
      $settings = "",
      $options = ""
  ) {
      // First check if the user/device is challenge enabled (if yes, check only the ldapPassword, if correct, return 10)
      $error_code = intval($this->CheckUserToken($username, $ldapPassword.$otpPassword));
      switch ($error_code) {
          case 0:
              $result['code'] = 1; // success
              break;
          case 10:
              $result['code'] = 2; // challenge
              break;
          default:
              $result['code'] = 0; // failure
              break;
      }

      $result['message'] = $this->GetErrorText($error_code); // Server reply, or message of the challenge
      if ($error_code > 10) {
          $result['message'].= " (".$error_code.")";
      }
      $result['session'] = ""; // for challenge, session ID
      $result['data']    = ""; // contains the ReplyData set in the LDAP user or group settings
      $result['timeout'] = 0;  // for challenge, remaining timeout to send challenge response
      $result['otpChallenge'] = "";  // PIN,TOKEN,SMS
      $result['u2fChallenge'] = "";  // U2F challenges in JSON format
      return $result;
  }

  
  function SoapOpenotpSimpleLogin(
      $username = "",
      $domain = "",
      $anyPassword = "",
      $client = "",
      $source = "",
      $settings = ""
  ) {
      // First check if the user/device is challenge enabled (if yes, check the static password, and if correct, return 10)
      $error_code = intval($this->CheckUserToken($username, $anyPassword));
      switch ($error_code) {
          case 0:
              $result['code'] = 1; // success
              break;
          case 10:
              $result['code'] = 2; // challenge
              break;
          default:
              $result['code'] = 0; // failure
              break;
      }

      $result['message'] = $this->GetErrorText($error_code); // Server reply, or message of the challenge
      if ($error_code > 10) {
          $result['message'].= " (".$error_code.")";
      }
      $result['session'] = ""; // for challenge, session ID
      $result['data']    = ""; // contains the ReplyData set in the LDAP user or group settings
      $result['timeout'] = 0;  // for challenge, remaining timeout to send challenge response
      return $result;
  }


  function SoapOpenotpChallenge(
      $username = "",
      $domain = "",
      $session = "",
      $otpPassword = "",
      $u2fResponse = ""
  ) {
      $error_code = intval($this->CheckUserToken($username, $otpPassword));
      switch ($error_code) {
          case 0:
              $result['code'] = 1; // success
              break;
          default:
              $result['code'] = 0; // failure
              break;
      }

      $result['message'] = $this->GetErrorText($error_code); // Server reply, or message of the challenge
      if ($error_code > 0) {
          $result['message'].= " (".$error_code.")";
      }
      $result['data']    = ""; // contains the ReplyData set in the LDAP user or group settings
      return $result;
  }

  
  function SoapOpenotpStatus()
  {
      $eol = chr(13).chr(10);
      $result['status'] = true;
      $result['message'] = "Server: ".$this->GetFullVersionInfo().$eol;
      if (isset($_SERVER["SERVER_ADDR"])) {
          $result['message'].= "Listener: ".$_SERVER["SERVER_ADDR"].(isset($_SERVER["SERVER_PORT"])?":".$_SERVER["SERVER_PORT"]:"").$eol;
      }
      if (isset($_SERVER["SERVER_PROTOCOL"])) {
          $result['message'].= "Protocol: ".$_SERVER["SERVER_PROTOCOL"];
      }
      if ('' != (isset($_SERVER["HTTPS"])?$_SERVER["HTTPS"]:'')) {
          $result['message'].= " (SSL)";
      } else {
          $result['message'].= " (no SSL)";
      }
      $result['message'].= $eol;
      $result['message'].= "Uptime: ".$this->GetUptime(false).$eol;
      $memory_limit = ini_get("memory_limit");
      if ('M' == substr($memory_limit,-1)) {
          $memory_limit = intval(substr($memory_limit,0,strlen($memory_limit)-1)) * 1024 * 1024;
      } elseif ('K' == substr($memory_limit,-1)) {
          $memory_limit = intval(substr($memory_limit,0,strlen($memory_limit)-1)) * 1024;
      }
      $result['message'].= "Memory: ".$memory_limit.$eol;
      // $result['message'].= "Total Requests: ".$this->TotalRequests().$eol;
      // $result['message'].= "Active Requests: ".$this->ActiveRequests()." (unlimited)".$eol;
      return $result;
  }


  /*
   * Call a REST service, REST authentication is done as for Amazon services
   * (http://docs.aws.amazon.com/AWSECommerceService/latest/DG/RequestAuthenticationArticle.html)
   * (http://randomdrake.com/2009/07/27/amazon-aws-api-rest-authentication-for-php-5/)
   */

  function CallApi(
      $call_array = array("script_uri" => "",
                          "secret"     => "",
                          "post_data"  => "")
  ) {
      $script_uri = isset($call_array["script_uri"]) ? $call_array["script_uri"] : "";
      $secret     = isset($call_array["secret"])     ? $call_array["secret"]     : "";
      $post_data  = isset($call_array["post_data"])  ? $call_array["post_data"]  : "";

      // Get a nice array of elements to work with
      $uri_elements = parse_url($script_uri);

      // Grab our request elements
      $scheme  = isset($uri_elements['scheme']) ? $uri_elements['scheme'] : 'http';
      $port    = isset($uri_elements['port'])   ? $uri_elements['port']   : '';
      $request = isset($uri_elements['query'])  ? $uri_elements['query']  : '';
      $host    = isset($uri_elements['host'])   ? $uri_elements['host']   : '';
      $path    = isset($uri_elements['path'])   ? $uri_elements['path']   : '';
   
      // Throw them into an array
      parse_str($request, $parameters);
      // $parameters = $_GET;

      if (isset($parameters['Signature'])) {
          unset($parameters['Signature']);
      }
      if (isset($parameters['Timestamp'])) {
          unset($parameters['Timestamp']);
      }
      $parameters['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");

      ksort($parameters);

      $request_array = array();
      // Create our new request
      foreach ($parameters as $parameter => $value) {
          // We need to be sure we properly encode the value of our parameter
          $parameter = str_replace("%7E", "~", rawurlencode($parameter));
          $value = str_replace("%7E", "~", rawurlencode($value));
          $request_array[] = $parameter . '=' . $value;
      }   

      // Put our & symbol at the beginning of each of our request variables and put it in a string
      $new_request = implode('&', $request_array);

      // Create our signature string
      $signature_string = "GET\n{$host}\n{$path}\n{$new_request}";
   
      $secret_key = $secret;
      if ('' == $secret_key) {
          $secret_key = $this->GetServerSecret();
      }

      // Create our signature using hash_hmac
      $signature = urlencode(base64_encode(hash_hmac('sha256', $signature_string, $secret_key, TRUE)));

      // Return our new request
      $url_request = "{$scheme}://{$host}".(('' != $port) ? ":{$port}" : "")."{$path}?{$new_request}&Signature={$signature}";

      // echo "DEBUG: Signature: $signature ($signature_string) $new_request\n<br />";

      if ($this->GetVerboseFlag()) {
          $this->WriteLog("Debug: *CallApi $script_uri (secret: $secret_key, signature string: $signature_string, full URL: $url_request)", FALSE, FALSE, 8888, 'Debug', '');
      }

      $api_result = $this->PostHttpDataXmlRequest(
        $post_data,                // $xml_data
        $url_request,              // $xml_urls
        $this->GetServerTimeout(), // $xml_timeout
        "\t",                      // $xml_urls_splitter
        TRUE                       // No multiOTP validity check
      );

      // echo $api_result;
      // return (FALSE !== mb_strpos($api_result, 'result_code'));
      return $api_result;
  }


  function XmlServer($data)
  {
      // $this->WriteLog("Info: Host received the following request: $data", FALSE, FALSE, 8888, 'Debug', '');

      $remote_ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';

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
      $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>', $xml_data);
      
      if (FALSE !== mb_strpos($data,'<multiOTP')) {
          if ($this->_xml_dump_in_log) {
              $this->WriteLog("Info: Host answer is correctly formatted.", FALSE, FALSE, 8888, 'Debug', '');
              $this->WriteLog("Info: Host received the following request: $data", FALSE, FALSE, 8888, 'Debug', '');
          }
          
          //Set up the parser object
          $xml = new MultiotpXmlParser($data);

          //Parse it !
          $xml->Parse();

          $server_challenge = $this->Decrypt('ServerChallenge', (isset($xml->document->serverchallenge[0])?($xml->document->serverchallenge[0]->tagData):''),$this->GetServerSecret($remote_ip));

          if (isset($xml->document->checkusertoken[0])) {
              $command_name = 'CheckUserToken';
              if ($this->GetVerboseFlag()) {
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
              if ($cache_level > $this->GetServerCacheLevel()) {
                  $cache_level = $this->GetServerCacheLevel();
              }
              
              $error_code = 70;

              if ('MOSH' == substr($server_challenge, 0, 4)) {
              // Ok, the challenge is encoded with the correct server secret
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
                  
                  if (!$this->CheckUserExists($user_id)) {
                      $error_code = 21; // ERROR: User doesn't exist
                  } else {
                      $error_code = intval($this->CheckUserToken($user_id, $user_password, '', FALSE, FALSE, FALSE, TRUE)); // do_not_check_on_server = TRUE;
                      
                      $now_epoch = time();
                      $cache_lifetime = $this->GetServerCacheLifetime();

                      if ((0 < $cache_level) && (0 == intval($error_code))) {
                          if ($this->GetVerboseFlag()) {
                              $this->WriteLog("Info: *Cache level is set to $cache_level", FALSE, FALSE, 8888, 'Server-Client', '');
                          }
                          
                          reset($this->_user_data);
                          while(list($key, $value) = each($this->_user_data)) {
                              if ('' != trim($key)) {
                                  if ('encryption_hash' != $key) {
                                      $user_data.= mb_strtolower($key);
                                      if ('autolock_time' == $key) {
                                          if (0 < $cache_lifetime) {
                                              if (($value == 0) || ($value > ($now_epoch + $cache_lifetime))) {
                                                  $value = ($now_epoch + $cache_lifetime);
                                              }
                                          }
                                      }
                                      $value = $this->Encrypt($key, $value, $this->GetServerSecret($remote_ip));
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
          elseif (isset($xml->document->readuserdata[0])) {
              $command_name = 'ReadUserData';
              $user_id = (isset($xml->document->readuserdata[0]->userid[0])?($xml->document->readuserdata[0]->userid[0]->tagData):'NO_USER_DETECTED!');
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Info: *ReadUserData server request for $user_id", FALSE, FALSE, 8888, 'Server-Client', '');
              }

              $error_code = 70;

              if ('MOSH' == substr($server_challenge, 0, 4)) {
                  // Ok, the challenge is encoded with the correct server secret
                  $error_code = 21; // ERROR: User doesn't exist

                  if ($this->ReadUserData($user_id, FALSE, TRUE)) {
                      // $no_server_check = TRUE;
                      $error_code = 19;
                      reset($this->_user_data);
                      while(list($key, $value) = each($this->_user_data)) {
                          if ('' != trim($key)) {
                              if ('encryption_hash' != $key) {
                                  $user_data.= mb_strtolower($key);
                                  $value = $this->Encrypt($key, $value, $this->GetServerSecret($remote_ip));
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
          elseif (isset($xml->document->checkuserexists[0])) {
              $command_name = 'CheckUserExists';
              $user_id = (isset($xml->document->checkuserexists[0]->userid[0])?($xml->document->checkuserexists[0]->userid[0]->tagData):'NO_USER_DETECTED!');
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Info: *CheckUserExists server request for $user_id with challenge $server_challenge", FALSE, FALSE, 8888, 'Server-Client', '');
              }

              $error_code = 70;

              if ('MOSH' == substr($server_challenge, 0, 4)) {
                  // Ok, the challenge is encoded with the correct server secret
                  $error_code = 21; // ERROR: User doesn't exist

                  if ($this->CheckUserExists($user_id, TRUE)) {
                      // $no_server_check = TRUE;
                      $error_code = 22;
                  }
              }
              if ($this->GetVerboseFlag()) {
                  $this->WriteLog("Info: *CheckUserExists intermediate error code: $error_code", FALSE, FALSE, 8888, 'Server-Client', '');
              }
          } // End of CheckUserExists
          
          $server_password = md5($command_name.$this->GetServerSecret($remote_ip).$server_challenge);
      }elseif ($this->GetVerboseFlag()) {
          $this->WriteLog("Info: *Server received the following request: $data", FALSE, FALSE, 8888, 'Server-Client', '');
      }
      
      $error_description = $this->GetErrorText($error_code);
      
      $xml_data = str_replace('*Command*', $command_name, $xml_data);
      $xml_data = str_replace('*ServerPassword*', $server_password, $xml_data);
      $xml_data = str_replace('*ErrorCode*', intval($error_code), $xml_data);
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
      header("Content-type: text/xml");

      if ($this->_xml_dump_in_log) {
          $this->WriteLog("Info: Server sent the following answer: $xml_data", FALSE, FALSE, 8888, 'Debug', '');
      }

      echo $xml_data;
  }


  // This method is a stub that calls the MultiotpQrcode with the good pathes
  function qrcode(
      $data = '',
      $file_name = '',
      $image_type = "P",
      $ecc_level = "Q",
      $module_size = 4,
      $version = 0,
      $structure_m = 0,
      $structure_n = 0,
      $parity = 0,
      $original_data = ''
  ) {
      $result = '';

      $qrcode_folder = $this->GetQrCodeFolder();

      $path = $qrcode_folder.'data';
      $image_path = $qrcode_folder.'image';
      
      if (!(file_exists($path) && file_exists($image_path))) {
          $this->WriteLog("Error: QRcode files or folders are not available", FALSE, FALSE, 39, 'System', '', 3);
      } else {
          $result = MultiotpQrcode($data, $file_name, $image_type, $ecc_level, $module_size, $version, $structure_m, $structure_n, $parity, $original_data, $path, $image_path);

          $output_name = NULL;
          ob_start();
          
          if (('' != trim($file_name)) && ('binary' != trim($file_name)) && ('' != $this->GetLinuxFileMode())) {
              if (file_exists($file_name)) {
                  @chmod($file_name, octdec($this->GetLinuxFileMode()));
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
 * phpseclib 1.0.5 (MIT License)     *
 * MMVI Jim Wigginton                *
 * http://phpseclib.sourceforge.net/ *
 *************************************/
if (!function_exists('phpseclib_resolve_include_path')) {
    function phpseclib_resolve_include_path($filename)
    {
        return $filename;
    }
}

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
if (!class_exists('Crypt_Blowfish')) {
  require_once('contrib/Blowfish.php'); // External contribution
}
if (!class_exists('Crypt_Twofish')) {
  require_once('contrib/Twofish.php'); // External contribution
}
if (!class_exists('Crypt_RC4')) {
  require_once('contrib/RC4.php'); // External contribution
}
if (!class_exists('Net_SSH2')) {
  require_once('contrib/SSH2.php'); // External contribution
}
if (!class_exists('Net_SFTP')) {
  require_once('contrib/SFTP.php'); // External contribution
}
if (!class_exists('Net_SFTP_Stream')) {
  require_once('contrib/Stream.php'); // External contribution
}
if (!class_exists('File_ASN1')) {
  require_once('contrib/ASN1.php'); // External contribution
}
if (!class_exists('File_X509')) {
  require_once('contrib/X509.php'); // External contribution
}

/********************************************************
* XPertMailer package 4.0.5 (LGPLv2.1)                 *
* Tanase Laurentiu Iulian                              *
* http://xpertmailer.sourceforge.net/                  *
********************************************************/
if (!defined('DISPLAY_XPM4_ERRORS')) define('DISPLAY_XPM4_ERRORS', FALSE);
if (version_compare(phpversion(), '5', '>=')) {
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
} else {
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

/********************************************************
* NuSOAP - PHP Web Services Toolkit 1.123 (LGPLv2.1)   *
* NuSphere Corporation                                 *
* http://sourceforge.net/projects/nusoap/              *
********************************************************/
if (!class_exists('nusoap_base')) {
  require_once('contrib/nusoap.php'); // External contribution
}

?>