multiOTP open source
====================
multiOTP open source is a GNU LGPL implementation of a strong two-factor authentication PHP class  
multiOTP open source is OATH certified for HOTP/TOTP

(c) 2010-2015 SysCo systemes de communication sa  
http://www.multiOTP.net/

Current build: 4.3.2.6 (2015-07-18)

Visit http://forum.multiotp.net/ for additional support.

The multiOTP package is the lightest package available that provides so many
strong authentication functionalities and goodies, and best of all, for anyone
that is interested about security issues, it's a fully open source solution!

This package is the result of a *bunch* of work. If you are happy using this
package, [Donation] are always welcome to support this project.
Please check http://www.multiOTP.net/ and you will find the magic button ;-)

If you need some specific features in the open source edition of multiOTP,
please contact us in order to discuss about a sponsorship in order to
prioritize your needs.

The multiOTP class supports currently the following algorithms and RFC's:
- RFC4226 OATH/HOTP (HOTP: An HMAC-Based One-Time Password Algorithm)
- RFC6238 OATH/TOTP (TOTP: Time-Based One-Time Password Algorithm)
- Yubico OTP (https://www.yubico.com/)
- mOTP (http://motp.sourceforge.net)
- Google Authenticator (OATH/HOTP or OATH/TOTP, base32 seed, QRcode provisioning)
- SMS tokens (using aspsms, clickatell, intellisms, or even your own script)
- RFC6030 PSKC (Additional Portable Symmetric Key Container Algorithm Profiles)
- RFC1994 CHAP (Challenge Handshake Authentication Protocol)
- RFC2433 MS-CHAP (Microsoft PPP CHAP Extensions)
- RFC2759 MS-CHAPv2 (Microsoft PPP CHAP Extensions, Version 2)
- RFC5424 Syslog Protocol (client)
- RFC2821 SMTP (Simple Mail Transfer Protocol)
- RFC2487 SMTP Service Extension for Secure SMTP over TLS


TABLE OF CONTENTS
=================
 * Donations and sponsoring
 * Roadmap for futures releases
 * What's new in the releases
 * Change Log of released version
 * Content of the package
 * When and how can I use this package ?
 * What is the prefix PIN option ?
 * How the lockout of an account is working ?
 * How to debug ?
 * How to install the multiOTP web service under Windows ?
 * How to install the multiOTP radius server under Windows ?
 * Configuring multiOTP with TekRADIUS or TekRADIUS LT under Windows
 * How to install the multiOTP web service under Linux ?
 * Configuring multiOTP with FreeRADIUS under Linux
 * How to configure multiOTP to synchronized the users from an Active Directory ?
 * How to configure multiOTP to synchronized the users from a standard LDAP ?
 * How to configure multiOTP to use the client/server feature ?
 * How to build a Raspberry Pi strong authentication server ?
 * How to install a centralized strong authentication server
   for strong authentication on desktops ?
 * Compatible clients applications and devices
 * External packages used
 * multiOTP PHP class documentation
 * multiOTP command line tool


DONATIONS AND SPONSORING
========================
You can support our multiOTP open source project with donations and sponsoring.
Sponsorships are crucial for ongoing and future development of the project! 
If you'd like to support our work, then consider making a donation, any support
is always welcome even if it's as low as $1!
You can also sponsor the development of a specific feature. Please contact
us in order to discuss the detail of the implementation.

**[Donate via PayPal by clicking here][1].** [![Donate via PayPal][2]][1]
[1]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PRS3VDNYL58HJ
[2]: https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg

Thanks to our main donators and sponsors:  
Donator AB (SE)  
Henk van der Helm (NL)  
Hermann Wegener GmbH & Co. KG (DE)  
SerNet GmbH (DE)  


ROADMAP FOR NEXT RELEASES
=========================
- Radius challenge/response support
- Multiple hardware tokens support for one account
- Generic web based SMS provider support
- Doxygen documentation format
- Users CSV import
  (username;pin;prefix_pin_needed;email;sms;serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
- Radius gateway support
- YubiCloud support
- FIDO support
- SMS-revolution SMS provider support
- SOAP API
- PostgreSQL support
- AngularJS frontend


WHAT'S NEW IN THE RELEASES
==========================
# What's new in 4.3 releases
- Raspberry Pi edition has now a special proxy to speed up the command line (4.3.1)
- Generic LDAP support (no more only Microsoft AD compatible LDAP) (4.3.1)
- New AD/LDAP sync algorithm to support larger AD (4.3.0)
- If users are synced with an AD, it's now possible to use
  the AD/LDAP password instead of the PIN code (4.3.0)
- Yubico OTP support, including keys import using the log file in Traditional format (4.3.0)
- Resync during authentication (autoresync) is now better handled in the class directly

# What's new in 4.2 releases
- A new option -user-info is now available (4.2.4.1)
- Tokens CSV import (4.2.4.1)
- NT_KEY can be displayed for further handling by FreeRADIUS (4.2.4.1)
- Lot of new QA tests, more than 60 different tests (4.2.4)
- Better MySQL support with mysqli library support (4.2.4)
- If activated, prefix PIN is now also requested for SMS authentication (4.2.2)
- Web GUI is complete for a simple usage (4.2.2)
- Some values can now go back to TekRADIUS (4.2.2)
- AD/LDAP is now fully supported (4.2.1)
- MS-CHAP and MS-CHAPv2 authentication support

# What's new in 4.1 releases
- Syslog support
- Token resync only (without login) doesn't need prefix PIN anymore
- Specific parameters order in QRCode for Microsoft Authenticator support
- The open source edition of multiOTP is also OATH certified for HOTP and TOTP,
  which includes encrypted PSKC import support
- Instructions and files to build your own strong authentication server device
  on a Raspberry Pi nano-computer
- Self-registration of unattributed hardware tokens
- Automatic resync/unlock during authentication
- Default Linux file mode is now set by default to 0666 to avoid access problem
- Basic web GUI

# What's new in 4.0 releases
- Full client/server support with local cache
- CHAP authentication support
- Emergency scratch passwords list
  (providing a list of 10 emergency one-time-usage passwords)
- SMS code sending (with clickatell, aspsms, intellisms and custom exec support)
- integrated Google Authenticator support with integrated base 32 seed handling
- Conversion from hardware HOTP/TOTP tokens to software tokens
- QRcode generation for HOTP/TOTP automatic provisioning
- Integrated QRcode generator library (from Y. Swetake)
- Group attribute per user (sent back through the Radius attribute Filter-Id)
- A lot of new options, also available in command line
- Options are stored in an external configuration file (or in the database)
- Full MySQL support, including tables creation
- Fully automatic build chain (invisible for you, but very nice for me)
- (Parts of the) comments have been reformatted and enhanced,
  but still some work to do.

# What's new in 3.9 releases
- Support for account with multiple users
- Some bug fixes

# What's new in 3.2 releases
- Google Authenticator support. Special information to handle the base 32 seed.
- Better MySQL backend integration. Now it is possible to store all
  information in a MySQL backend instead of a flat file .


CHANGE LOG OF RELEASED VERSIONS
===============================
2015-07-18 4.3.2.6 SysCo/al New ResetTempUserArray method (as we want to move away from global array in the near future)
                            For _user_data, default values are now extracted from the definition array
                            QRcode generation for mOTP (motp://[SITENAME]:[USERNAME]?secret=[SECRET-KEY])
2015-07-15 4.3.2.5 SysCo/al Calling multiotp CLI without parameter returns now error code 30 (instead of 19)
2015-06-24 4.3.2.4 SysCo/al multi_account automatic support
                            Scratch password generation (UTF)
2015-06-10 4.3.2.3 SysCo/al Enhancements for the Dev(Talks): demo
2015-06-09 4.3.2.2 SysCo/al Empty users are refused
                            TOTP time interval of imported tokens is set by default to 30s
                            More accuracy in the logged information
                            Refactoring backend methods, sharing code
                            Refactoring some ugly parts (!)
                            Documentation update concerning lockout functions and prefix PIN prefix
                            Special token entry 'Sms' is now also accepted, like 'SMS' or 'sms', to send an SMS token
                            The minus (-) in the prefix password is now supported (it was filtered to fix some rare user issues)
                            The autoresync option is now enabled by default
                            Resync during authentication (autoresync) is now better handled in the class directly
                            The server_cache_level is now set to 1 by default (instead of 0)
                            If the token length is not correct, it's now written in the log
                            Some LDAP messages are now only logged in debug mode
2014-12-15 4.3.1.1 SysCo/al Better generic LDAP support
                              - description sync done in the following order: description, gecos, displayName
                              - memberOf is not always implemented, alternative method to sync users based on group names.
                              - disabled account synchronization using shadowExpire or sambaAcctFlags
                            Better Active Directory support
                              - accountExpires is now supported for synchronization
                              - ms-DS-User-Account-Control-Computed (to handle locked out accounts, available since Windows 2003)
2014-12-09 4.3.1.0 SysCo/al MULTIOTP_PATH environment variable support
                            CLI proxy added to speed up the command line
                            Scratch password need also the prefix PIN if it's activated
                            OTP with integrated serial numbers better supported (in PAP)
                            Generic LDAP support (instead of Microsoft AD support only)
                            Raspberry Pi edition has now a special proxy to speed up the command line
2014-11-04 4.3.0.0 SysCo/al It's now possible to use the AD/LDAP password instead of the PIN code
                            Yubico OTP support, including keys import using the log file in Traditional format
                            qrcode() stub enhanced to check if the required folders are available
                            SyncLdapUsers completely redesigned
                              - no more complete array in memory
                              - MultiotpAdLdap class also enhanced accordingly
                                - cached group_cn requests
                                - cached recursive_groups requests
                                - new "by element" functions
                            Demo mode support
                            Bug fix concerning the NT_KEY generation with enabled prefix PIN (thanks Adam)
                            ResyncToken() method added (instead of using CheckToken() method for synchronization)
2014-06-12 4.2.4.3 SysCo/al Bug fix concerning aspsms provider
2014-04-13 4.2.4.2 SysCo/al XML parsing consolidation, one library for the whole project
                            Fixed bug concerning tokens CSV import
2014-04-06 4.2.4.1 SysCo/al Fixed bug concerning LDAP handling
                            NT_KEY support added (for FreeRADIUS further handling)
                            Tokens CSV import (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
                            When a user is deleted, the token(s) attributed to this user is/are unassigned
                            New option -user-info added
2014-03-30 4.2.4   SysCo/al Fixed bug concerning MySQL handling and mysqli support added
                            Enhanced SetAttributesToEncrypt function
                            New implementation fo some external classes
                            Generated QRcode are better
                            LOT of new QA tests, more than 60 different tests (including PHP class and command line versions)
                            Enhanced documentation
2014-03-13 4.2.3   SysCo/al Fixed bug for clear text password going back to TekRADIUS (PIN was always prefixed for mOTP)
                            Fixed bug when client/server mode is activated, but not working well
2014-03-03 4.2.2   SysCo/al Better AD/LDAP integration
                            Web GUI is now complete for a simple usage, including hardware tokens import
                            Better template for provisioning information
                            Some values can now go back to TekRADIUS
                            If activated, prefix PIN is now also requested for SMS authentication
                            More information in the logs
                            Better list of the external packages used
2014-02-14 4.2.1   SysCo/al AD/LDAP is now fully supported in order to create users based on AD/LDAP content
                             (with groups filtering)
2014-02-07 4.2.0   SysCo/al MS-CHAP and MS-CHAPv2 are now supported
                             (md4 implementation added for PHP backward compatibility)
                            Enhanced LDAP configuration structure
                            Fixed bug during token attribution to users
                             (a "no name" token appeared sometimes)
2014-01-20 4.1.1   SysCo/al md5.js was missing in the public distribution
                            Alternate json_encode function is defined if the JSON extension is not loaded
                            Fixed possible image functions incompatibilities with some PHP versions
                             during QRcode generation
                            As suggested by Sylvain, token resync doesn't need prefix PIN anymore
                             (but still accepted)
                            More verbosity in the logs in debug mode
                            Specific parameters order in QRCode for Microsoft Authenticator support
                             (thanks to Erik Nylund)
2013-12-23 4.1.0   SysCo/al The open source edition of multiOTP is OATH certified ;-)
                             (that means full compatibility with any OATH tokens and encrypted PSKC import support)
                            Raspberry Pi nanocomputer is now fully supported
                            Basic web interface
                            Self-registration of hardware tokens is now possible
                             PAP mode: if self-registration is enabled, a user can register a non-attributed token by typing
                             [serial number][OTP] instead of [OTP]. If user has a prefix PIN, type [serial number][PIN][OTP])
                             PAP/CHAP mode: if self-registration is enabled, a user can register a non-attributed token by typing
                             [username:serialnumber] as the username and the [OTP] in the password field.
                             If user has a prefix PIN, [PIN][OTP] must be typed in the password field
                            Automatic resync/unlock option during authentication (PAP only). When the autoresync option
                             is enabled, any user can resync his token by typing [OTP1] [OTP2] in the password field. 
                             If user has a prefix PIN, he must type [PIN][OTP1] [PIN][OTP2].
                            Tokens with less than 3 characters are not accepted anymore in CheckToken()
                            Default Linux file mode is now set by default (0666 for created and changed files)
                            Error 28 is returned if the file is not writable, even after a successful login
                            Added GetUsersCount() function
                            Added GenerateSmsToken() function
                            Added Groups management functions
                            Added Tokens assignation functions
                            Added SetUserActivated(1|0) and GetUserActivated() function
                            Added SetUserSynchronized(1|0) and GetUserSynchronized() function
                            scratch_passwords is now a text field in the database
                            The third parameter of the Decrypt method is now mandatory
                            Some modifications in order to correctly handle the class methods
2013-09-22 4.0.9   SysCo/al Fixed a bug in GetUserScratchPasswordsArray. If a user had no scratch password
                             and the implementation accepted blank password, it was accepted
                            Fixed a bug where scratch passwords generation used odd numbers of characters for hex2bin()
2013-08-30 4.0.7   SysCo/al GetScriptFolder() was still buggy sometimes, thanks Frank for the feedback
                            File mode of the created QRcode file is also changed based on GetLinuxFileMode()
                            'sms' as the password to request an SMS token can now be sent in lower or uppercase
                            Added a description attribute for the tokens
2013-08-25 4.0.6   SysCo/al base32_encode() is now RFC compliant with uppercases
                            GetUserTokenQrCode() and GetTokenQrCode() where buggy
                            GetScriptFolder() use now __FILE__ if the full path is included
                            When doing a check in the CLI header, @... is automatically removed from the
                             username if the user doesn't exist, and the check is done on the clean name
                            Added a lot of tests to enhance release quality
2013-08-21 4.0.5   SysCo/al Fixed the check of the cache lifetime
                            Added a temporary server blacklist during the same instances
                            Default server timeout is now set to 1 second
2013-08-20 4.0.4   SysCo/al Added an optional group attribute for the user
                             (which will be send with the Radius Filter-Id option)
                            Added scratch passwords generation (if the token is lost)
                            Automatic database schema upgrade using method UpgradeSchemaIfNeeded()
                            Added client/server support with local cache
                            Added CHAP authentication support (PAP is of course still supported)
                            The encryption key is now a parameter of the class constructor
                            The method SetEncryptionKey('MyPersonalEncryptionKey') is DEPRECATED
                            The method DefineMySqlConnection is DEPRECATED
                            Full MySQL support, including tables creation (see example and SetSqlXXXX methods)
                            Added email, sms and seed_password to users attributes
                            Added sms support (aspsms, clickatell, intellisms, exec)
                            Added prefix support for debug mode (in order to send Reply-Message := to Radius)
                            Added a lot of new methods to handle easier the users and the tokens
                            General speedup by using available native functions for hash_hmac and others
                            Default max_time_window has been lowered to 600 seconds (thanks Stefan for suggestion)
                            Integrated Google Authenticator support with integrated base 32 seed handling
                            Integrated QRcode generator library (from Y. Swetake)
                            General options in an external configuration file
                            Comments have been reformatted and enhanced for automatic documentation
                            Development process enhanced, source code reorganized, external contributions are
                             added automatically at the end of the library after an internal build release
2011-10-25 3.9.2   SysCo/al Some quick fixes after intensive check
                            Improved get_script_dir() in CLI for Linux/Windows compatibility
2011-09-15 3.9.1   SysCo/al Some quick fixes concerning multiple users
2011-09-13 3.9.0   SysCo/al Added support for account with multiple users
2011-07-06 3.2.0   SysCo/al Encryption hash handling with additional error message 33
                             (if the key has changed)
                            Added more examples
                            Added generic user with multiple account
                             (Real account name is combined: "user" and "account password")
                            Added log options, now default doesn't log token value anymore
                            Debugging MySQL backend support for the token handling
                            Fixed automatic detection of \ or / for script path detection
2010-12-19 3.1.1   SysCo/al Better MySQL backend support, including in CLI version
2010-09-15 3.1.0   SysCo/al Removed bad extra spaces in the multiotp.php file for Linux
                            MySQL backend support
2010-09-02 3.0.0   SysCo/al Added tokens handling support
                             including importing XML tokens definition file
                             (http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00)
                            Enhanced flat database file format (multiotp is still compatible with old versions)
                            Internal method SetDataReadFlag renamed to SetUserDataReadFlag
                            Internal method GetDataReadFlag renamed to GetUserDataReadFlag
2010-08-21 2.0.4   SysCo/al Enhancement in order to use an alternate php "compiler" for Windows command line
                            Documentation enhancement
2010-08-18 2.0.3   SysCo/al Minor notice fix
2010-07-21 2.0.2   SysCo/al Fix to create correctly the folders "users" and "log" if needed
2010-07-19 2.0.1   SysCo/al Foreach was not working well in PHP4, replaced at some places
2010-07-19 2.0.0   SysCo/al New design using a class, mOTP support, cleaning of the code
2010-06-15 1.1.5   SysCo/al Added OATH/TOTP support
2010-06-15 1.1.4   SysCo/al Project renamed to multiotp to avoid overlapping
2010-06-08 1.1.3   SysCo/al Typo in script folder detection
2010-06-08 1.1.2   SysCo/al Typo in variable name
2010-06-08 1.1.1   SysCo/al Status bar during resynchronization
2010-06-08 1.1.0   SysCo/al Fix in the example, distribution not compressed
2010-06-07 1.0.0   SysCo/al Initial implementation


CONTENT OF THE PACKAGE
======================
In the linux folder:
- multiotp.php             : command line tool (merge of the header and the class, external files also included)
- multiotp.class.php       : the main file, it is the class itself, external files are already included
- multiotp.server.php      : the web service file (the class is already merged in the file, external files also included)
- check.multiotp.class.php : PHP script to validate some multiOTP functionalities
- md5.js                   : encryption JS library used by multiotp.server.php
- test-tokens.csv          : provisioning file of test tokens
+ oath subfolder           : contains provisioning files for oath test tokens
+ qrcode subfolder         : all necessary files to be able to generate QRcode
+ templates folder         : all templates files needed to generate the provisioning pages from the web GUI

In the raspberry folder:
- all necessary files to be able to create your own strong authentication device using a Raspberry Pi

In the sources folder:
- multiotp.class.php       : the main file, it is the class itself, which requires external files
- multiotp.cli.header.php  : header file to be merged with the class for a single file command line tool
- multiotp.server.php      : the web service file, which requires the class as external file
- check.multiotp.class.php : PHP script to validate some multiOTP functionalities
+ contrib subfolder        : contains all external files required by the multiotp.class.php file

In the windows folder:
- multiotp.exe             : command line tool for Windows (digitally signed) with embedded PHP 5.3.1
- multiotp.class.php       : the main file, it is the class itself, external files are already included
- multiotp.server.php      : the web service file (the class is already merged in the file, external files also included)
- check.multiotp.class.php : PHP script to validate some multiOTP functionalities
- md5.js                   : encryption JS library used by multiotp.server.php
- checkmultiotp.cmd        : Windows script to validate some multiOTP functionalities
- radius_debug.cmd         : Windows script to run the multiOTP radius web server in debug mode
- radius_install.cmd       : Windows script to install and start the multiOTP radius web server
- radius_uninstall.cmd     : Windows script to stop and uninstall the multiOTP radius web server
- webservice_install.cmd   : Windows script to install and start the multiOTP web service
- webservice_uninstall.cmd : Windows script to stop and uninstall the multiOTP web service
- test-tokens.csv          : provisioning file of test tokens
+ legacy subfolder         : contains a windows command line version with all needed files (not embedded in a mini VM)
+ oath subfolder           : contains provisioning files for oath test tokens
+ qrcode subfolder         : all necessary files to be able to generate QRcode
+ radius subfolder         : all necessary files to be able to install a Windows radius server already
                             configured with multiOTP support (using FreeRADIUS implementation for Windows)
+ templates subfolder      : all templates files needed to generate the provisioning pages from the web GUI
+ tools subfolder          : command line tools needed by some cmd scripts
+ webservice subfolder     : all necessary files to be able to install a Windows multiOTP web service
                             (using mongoose as the light web server on port 8112,
                              or as a secured SSL connection (https) on port 8113)


WHEN AND HOW CAN I USE THIS PACKAGE ? 
=====================================
If you decide to have strong two factor authentication inside your company,
this is definitely the package you need! You will be able to have strong
authentication for your VPN accesses, your SSL gateway, your private websites
and even your Windows login for desktops AND laptops!

The multiOTP class can be used alone (for example to have strong 
authentication for your PHP based web application), as a command line tool
(to handle users and have strong authentication using command line), as a web
service (to provide centralized authentication for a master/slave installation)
or finally coupled with a radius server like TekRADIUS or FreeRADIUS to be able
to have a strong two factor authentication through the RADIUS protocol for
external devices like for example firewalls or captive portals.

The default backend storage is done in flat files, but you can also defined a
MySQL server as the backend server. To use MySQL, you will only have to provide
the server, the username, the password and the database name. Tables will be
created/updated automatically by multiOTP. The schema is also upgraded
automatically if you install a new release of multiOTP.

Starting with version 4.x, you can also install a multiOTP web service
on a server, and this way some other multiOTP slave clients (like laptops)
can connect to the web service and caching the tokens information (if allowed).

Inside a company, you will probably use multiOTP with a radius server or as
a web service (see below on how to install these services).

If you are running under Windows, TekRADIUS or TekRADIUS LT will do the job 
(http:/www.tekradius.com/).
The difference is that TekRADIUS needs an MS-SQL SERVER (or MS-SQL Express) 
and TekRADIUS LT uses only an embedded SQLite database.

multiOTP is working fine under Windows with WinRADIUS, a port of FreeRADIUS
(http://winradius.eu/)

multiOTP is also working fine with another port of FreeRADIUS
for Windows (http://sourceforge.net/projects/freeradius/)

If you are running under Linux, FreeRADIUS will do the job.
(http://freeradius.org/)

Now, you can register your different devices like firewalls, SSL, etc.
in the radius server and provide the IP address(es) of the device(s)
(often called NAS) and their shared Secret.

If you want to have strong authentication on Windows logon, have a look at the
open source MultiOneTimePassword Credential Provider from Last Squirrel IT.
It works with Windows Vista/7/2008/8/2012 in both 32 and 64 bits.
The Credential Provider does not need any RADIUS connection! It uses instead a
local version of multiOTP which can be configured as a client of a
centralized server (with caching support).
(https://code.google.com/p/multi-one-time-password--credential-provider/)

LSE Experts provides a commercial Radius Credential Provider which can talk
directly with a radius server.
(http://www.lsexperts.de)

When the backend is set, it's time to create/define the tokens. You will have
to select hardware or software token generators for your users. Currently, the
library supports mOTP, TOTP, HOTP, SMS or scratch passwords (printed on paper).

mOTP is a free implementation of strong tokens that asks a PIN to generate a
code. This code depends of the time and the PIN typed by the user.

The easiest tokens to use are TOTP, they are time based and well supported by
a lot of implementations like Google Authenticator.
Provisioning will be done simply by flashing a QRcode.

# Software tokens with mOTP (Mobile-OTP) support
  - iPhone:    iOTP from PDTS (type iOTP in the Apple AppStore)
  - Android:   Mobile-OTP (http://motp.sf.net/Mobile-OTP.apk)
  - PalmOS:    Mobile-OTP (http://motp.sf.net/mobileotp_palm.zip)
  - Java J2ME (Nokia and other Java capable phones): MobileOTP
              (http://motp.sf.net/MobileOTP.jad)
  - WinPhone:  Token2 (https://token2.com/?content=mobileapp)
  
# Software tokens with OATH compliant HOTP or TOTP support
  Check the various markets of your devices, for examples:
  - Google Authenticator (Android/iPhone/iPad/BlackBerry)
  - oathtoken for iPhone/iPad: http://code.google.com/p/oathtoken/
  - androidtoken for Android: http://code.google.com/p/androidtoken/

# Hardware tokens
  - Any tokens that are OATH certified
    - Feitian provides OATH compliant HOTP and TOTP tokens
       (seed is provided in a standardized token definition PSKC xml file)
      - OTP c100: OATH/HOTP, 6 digits
      - OTP c200: OATH/TOTP, 6 digits, 60 seconds time interval
        (seed is provided in a standardized token definition PSKC xml file)
    - Gemalto provides OATH compliant HOTP and TOTP tokens
      - Gemalto Ezio Token
    - Seamoon provides OATH compliant TOTP tokens
      - Seamoon KingKey: OATH/TOTP, 6 digits, 60 seconds time interval
        (seed is provided in a specific smd file)
    - ZyXEL OTP provides HOTP OATH compliant tokens (v2 and old v1 tokens)
      - ZyWALL OTPv2 (rebranded SafeNet/Aladdin eToken PASS) : OATH/HOTP, 6 digits
        (seed is extracted from the importAlpine.dat downloaded file,
         the seed is the sccKey attribute)
      - ZyWALL OTPv1 (rebranded Authenex A-Key 3600): OATH/HOTP, 6 digits
        (seed is extracted from the OTP_data01_upgrade.sql SQL file,
         SEED field at the end of the file)
  - YubiKeys from Yubico (both in Yubico OTP or in OATH-HOTP format)
    - YubiKey standard
    - YubiKey Nano
    - YubiKey Neo
    - YubiKey Neo-N

If you want to use software tokens with Apps like Google Authenticator, you can
create a QRcode provisioning in two EASY steps with the command line tool:
 - create the token for the user (without prefix PIN request):
   multiotp -fastcreatenopin my_user
 - generate the provisioning QRcode: multiotp -qrcode my_user my_qrcode.png  
 
You can also create a user quickly with the prefix PIN request option based on
the default option set in your configuration: multiotp -fastcreate my_user  

And of course, you can also force to create a user quickly with a requested
prefix PIN: multiotp -fastcreatewithpin my_user


WHAT IS THE PREFIX PIN OPTION ?
===============================
The prefix PIN option is activated by default. Users will have to type their
PIN + the displayed token. The prefix PIN option has no effect for mOTP tokens,
and the users MUST NOT type their prefix PIN before the displayed token for
mOTP tokens, as the prefix PIN is already used by the algorithm in order to
generate the token.  

Starting with version 4.3, it's now possible to use the synchronized AD/LDAP
password as a prefix instead of the static PIN. Please note that even with the
AD/LDAP password as a prefix activated, the PIN used for mOTP tokens is still
the static PIN.  

To create a user quickly with the prefix PIN request option based on
the default option set in your configuration: multiotp -fastcreate my_user  

To create a user quickly without a prefix PIN request:
multiotp -fastcreatenopin my_user  
 
To create a user quickly with a requested prefix PIN:
multiotp -fastcreatewithpin my_user


HOW THE LOCKOUT OF AN ACCOUNT IS WORKING ?
==========================================
To prevent brute-force attack, an account is temporary locked for 300 seconds
after 3 unsuccessful trials.  
After 6 unsuccessful trials, the account is definitely locked.  

A user is unlocked by typing the following command line: 
multiotp -unlock user

A user can also unlock his account by typing two consecutive codes, 
separated by a space (don't forget the prefix PIN/password if enabled).  
If prefix PIN is enabled, your PIN is 1234 and the two consecutive tokens are
984501 and 348202, you will have to type "1234984501 1234348202" as the
password in order to unlock the account during authentication.  

The lockout parameters can be modified using these command lines:  
multiotp -config failure-delayed-time=60  
multiotp -config max-block-failures=12  
multiotp -config max-delayed-failures=10  


HOW TO DEBUG ?
==============
In order to have debug information, you can use the -debug option. With this
option, the debug information are saved in the file log/multiotp.log.  
If you want to see directly the debug information on screen, add the
-display-log and an output of the debug information will be done on screen too.

In order to enable the debug mode permanently without using the option, you can
do that like this: multiotp -config debug=1

The same thing can be done for a permanent display of the debug information on
the screen without using the option: multiotp -config display-log=1


HOW TO INSTALL THE MULTIOTP WEB SERVICE UNDER WINDOWS ?
=======================================================
Installing the multiOTP web service is VERY easy. Simply run the
webservice_install script. Mongoose configuration file will be created,
firewall rules will be adapted and the service will be installed and started.
The service is called multiOTPservice and is listening on port 8112 (http)
and on port 8113 (https).


HOW TO INSTALL THE MULTIOTP RADIUS SERVER UNDER WINDOWS ?
=========================================================
Installing the multiOTP radius service is VERY easy too. Simply run the
radius_install script. The etc/raddb/modules/multiotp file will be created,
firewall rules will be adapted and the service will be installed and started.
The service is called multiOTPradius and the secret is multiotpsecret for any
client including 127.0.0.1.


CONFIGURING MULTIOTP WITH TEKRADIUS OR TEKRADIUS LT UNDER WINDOWS
=================================================================
TekRADIUS supports a Default Username to be used when a matching user
profile cannot be found for an incoming RADIUS authentication request.
So a quick and easy way is to create in the TekRADIUS Manager a User
named 'Default' that belongs to the existing 'Default' Group.
Then add to this Default user the following attribute :
Check  External-Executable  C:\multitop\multiotp.exe %ietf|1% %ietf|2% -chap-challenge=%ietf|60% -chap-password=%ietf|3% -ms-chap-challenge=%msoft|11% -ms-chap-response=%msoft|1% -ms-chap2-response=%msoft|25%

Some values can go back to TekRADIUS:

a) Set the right format options for TekRADIUS:
   multiotp -config radius-reply-attributor="=" radius-reply-separator="crlf"
   
b) Set multiOTP to send back to TekRADIUS the clear (non encrypted) authentication:
   multiotp -config clear-otp-attribute="ietf|2"

c) Set multiOTP to send back to TekRADIUS the group of the authenticated user:
   multiotp -config group-attribute="ietf|11"


HOW TO INSTALL THE MULTIOTP WEB SERVICE UNDER LINUX ?
=====================================================
The multiOTP web service is a simple web site. If you are under Linux and you
are reading this document, you have for sure the necessary skill to configure
your favorite web server in order to have an URL that will launch the page
multiotp.server.php which is in the main folder of the multiOTP distribution.


CONFIGURING MULTIOTP WITH FREERADIUS UNDER LINUX
================================================
Using the -request-nt-key option, NT_KEY: XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX can
now be displayed (like with the same option used with ntlm_auth).

1) Create a new module file called "multiotp" in etc/raddb/modules/ containing:  
    
    # Exec module instance for multiOTP (http://www.multiotp.net/).  
    # for Linux  : replace '/path/to' with the actual path to the multiotp.php file.  
    # for Windows: replace '/path/to' with the actual path to the multiotp.exe file (also with /).  
    exec multiotp {  
        wait = yes  
        input_pairs = request  
        output_pairs = reply  
        program = "/path/to/multiotp '%{User-Name}' '%{User-Password}' -request-nt-key -src=%{Packet-Src-IP-Address} -chap-challenge=%{CHAP-Challenge} -chap-password=%{CHAP-Password} -ms-chap-challenge=%{MS-CHAP-Challenge} -ms-chap-response=%{MS-CHAP-Response} -ms-chap2-response=%{MS-CHAP2-Response}"  
        shell_escape = yes  
    }

2) In the configuration file called "default" in etc/raddb/sites-enabled/  
    
    a) Add the multiOTP handling  
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be add BEFORE the first "pap" entry found in the file.  
    multiotp  

    b) Add the multiOTP authentication handling  
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be add BEFORE the first "Auth-Type PAP" entry found in the file.  
    Auth-Type multiotp {  
        multiotp  
    }  

    c) Comment the first line containing only "chap"  
    #chap is now handled by multiOTP  

3) In the configuration file called "inner-tunnel" in etc/raddb/sites-enabled/  
    
    a) Add the multiOTP handling  
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be add BEFORE the first "pap" entry found in the file.  
    multiotp  

    b) Add the multiOTP authentication handling  
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be add BEFORE the first "Auth-Type PAP" entry found in the file.  
    Auth-Type multiotp {  
        multiotp  
    }  

    c) Comment the first line containing only "chap"  
    #chap is now handled by multiOTP  

4) In the configuration file called "policy.conf" in etc/raddb/  
   Add the multiOTP authorization policy  
    
    #  
    # Handle multiOTP (http://www.multiotp.net/) authorization policy.  
    # This must be add just before the last "}"  
    multiotp.authorize {  
        if (!control:Auth-Type) {  
            update control {  
                Auth-Type := multiotp  
            }  
        }  
    }  

5) In the configuration file called "radiusd.conf" in etc/raddb/  
   Depending which port(s) and/or ip address(es) you want to listen, change
   the corresponding ipaddr and port parameters  

6) In the configuration file called "clients.conf" in etc/raddb/  
   Add the clients IP, mask and secret that you want to authorize.  
    
    #  
    # Handle multiOTP (http://www.multiotp.net/) for some clients.  
    client 0.0.0.0 {  
        netmask = 0  
        secret = multiotpsecret  
    }  
   
7) Now, to see what's going on, you can:
   - stop the service : /etc/init.d/freeradius stop
   - launch the FreeRADIUS server in debug mode : /usr/sbin/freeradius -X
   - try to make some authentication requests

8) When you have checked that everything works well:
   - stop the debug mode (CTRL + C)
   - restart the service /etc/init.d/freeradius restart

Some values can go back to FreeRADIUS:

a) Set the right format options for FreeRADIUS:  
   multiotp -config radius-reply-attributor=" = " radius-reply-separator=","
   
b) Set multiOTP to send back to FreeRADIUS the group of the authenticated user:  
   multiotp -config group-attribute="Filter-Id"  


HOW TO CONFIGURE MULTIOTP TO SYNCHRONIZED THE USERS FROM AN ACTIVE DIRECTORY ?
==============================================================================
1) Decide if you want that by default, created users need to type a prefix PIN (1|0):
   multiotp -config default-request-prefix-pin=1
   
2) Decide if you want that by default, created users need to type their
   Active Directory password instead of PIN (1|0):
   multiotp -config default-request-ldap-pwd=1

3) Set the AD/LDAP server type (1=Active Directory | 2=standard LDAP):
   multiotp -config ldap-server-type=1

4) Set the user CN identifier (sAMAccountName, eventually userPrincipalName):
   multiotp -config ldap-cn-identifier="sAMAccountName"

5) Set the group CN identifier (sAMAccountName for Active Directory):
   multiotp -config ldap-group-cn-identifier="sAMAccountName"

6) Set the group attribute:
   multiotp -config ldap-group-attribute="memberOf"

7) Decide if you want to use by default an SSL connection or not (0|1):
   multiotp -config ldap-ssl=0
   
8) Set the default port (389=regular | 636=SSL connection):
   multiotp -config ldap-port=389
   
9) Set the Active Directory server(s), comma separated:
   multiotp -config ldap-domain-controllers=my.srv.com,ldaps://12.13.14.15:636
   (you can define more than one server, and you can also use a SSL connection
    only for one server, on a specific port)
   
10) Set the Base DN:
    multiotp -config ldap-base-dn="DC=demo,DC=multiotp,DC=net"
    (on a Microsoft Windows Server, the different values of the base DN of the
     domain can be displayed using the command ECHO %USERDNSDOMAIN%, and the
     result will be something like DEMO.MULTIOTP.NET)

11) Set the Bind DN (which is the account used to connect to the AD/LDAP):
    multiotp -config ldap-bind-dn="CN=sync,CN=Users,DC=demo,DC=multiotp,DC=net"
    (on a Microsoft Windows Server, the bind DN of the user can be displayed
     using the command dsquery user -name sync, and the result will be
     something like "CN=sync,CN=Users,DC=demo,DC=multiotp,DC=net")
   
12) Set the password of the user used to search in the Active Directory:
    multiotp -config ldap-server-password="password_of_my_ldap_user"
   
13) In which groups users must be in the Active Directory in order to be added:
    multiotp -config ldap-in-group="VPNuser,dialin"
   
14) Set the network timeout
    multiotp -config ldap-network-timeout=10
   
15) Set the transaction time limit
    multiotp -config ldap-time-limit=30

16) Activate the AD/LDAP support (0|1):
    multiotp -config ldap-activated=1
   
17) Let's go for an AD/LDAP users synchronisation !
    (users removed or desactivated in the AD/LDAP are desactivated in multiOTP)
    multiotp -debug -display-log -ldap-users-sync
    
DON'T FORGET TO SCHEDULE A SCRIPT THAT WILL DO THE USERS SYNCHRONIZATION REGULARY!


HOW TO CONFIGURE MULTIOTP TO SYNCHRONIZED THE USERS FROM A STANDARD LDAP ?
==========================================================================
1) Decide if you want that by default, created users need to type a prefix PIN (1|0):
   multiotp -config default-request-prefix-pin=1
   
2) Decide if you want that by default, created users need to type their
   LDAP password instead of PIN (1|0):
   multiotp -config default-request-ldap-pwd=1

3) Set the AD/LDAP server type (1=Active Directory | 2=standard LDAP):
   multiotp -config ldap-server-type=2

4) Set the user CN identifier (uid for standard LDAP):
   multiotp -config ldap-cn-identifier="uid"

5) Set the group CN identifier (cn for standard LDAP):
   multiotp -config ldap-group-cn-identifier="cn"

6) Set the group attribute:
   multiotp -config ldap-group-attribute="memberOf"

7) Decide if you want to use by default an SSL connection or not (0|1):
   multiotp -config ldap-ssl=0
   
8) Set the default port (389=regular | 636=SSL connection):
   multiotp -config ldap-port=389
   
9) Set the LDAP server(s), comma separated:
   multiotp -config ldap-domain-controllers=my.srv.com,ldaps://12.13.14.15:636
   (you can define more than one server, and you can also use a SSL connection
    only for one server, on a specific port)
   
10) Set the Base DN:
    multiotp -config ldap-base-dn="dc=demo,dc=multiotp,dc=net"

11) Set the Bind DN (which is the account used to connect to the AD/LDAP):
    multiotp -config ldap-bind-dn="uid=sync,cn=users,dc=demo,dc=multiotp,dc=net"
   
12) Set the password of the user used to search in the LDAP directory:
    multiotp -config ldap-server-password="password_of_my_ldap_user"
   
13) In which groups users must be in LDAP directory in order to be added:
    multiotp -config ldap-in-group="VPNuser,dialin"
   
14) Set the network timeout
    multiotp -config ldap-network-timeout=10
   
15) Set the transaction time limit
    multiotp -config ldap-time-limit=30

16) Activate the AD/LDAP support (0|1):
    multiotp -config ldap-activated=1
   
17) Let's go for an AD/LDAP users synchronisation !
    (users removed or desactivated in the AD/LDAP are desactivated in multiOTP)
    multiotp -debug -display-log -ldap-users-sync

DON'T FORGET TO SCHEDULE A SCRIPT THAT WILL DO THE USERS SYNCHRONIZATION REGULARY!


HOW TO CONFIGURE MULTIOTP TO USE THE CLIENT/SERVER FEATURE ?
============================================================
A) On the server
1) Install the multiOTP web service on the server side. If you are
   using the unmodified included installer to install it under Windows, the
   URL for the multiOTP web service is http://ip.address.of.server:8112
   The web service script installer is called webservice_install.cmd.
2) Set the shared secret key you will use to encode the data between the
   server and the client: multiotp -config server-secret=MySharedSecret
   (this command line will change the configuration file config/multiotp.ini)
3) If you want to allow the client to cache the data on its side, set the
   options accordingly (enable the cache and define the lifetime of the cache):
   multiotp -config server-cache-level=1 server-cache-lifetime=15552000
   (this command line will change the configuration file config/multiotp.ini)
4) Create your users on the server using the web GUI interface. If you are
   using the unmodified included installer to install it under Windows, the
   URL for the multiOTP web service is http://ip.address.of.server:8112

B) On the client(s)
1) Set the shared secret key you will use to encode the data between the
   client and the server: multiotp -config server-secret=MySharedSecret
   (this command line will change the configuration file config/multiotp.ini)
2) If you want to have cache support (if allowed by the multiOTP web service),
   set the option accordingly: multiotp -config server-cache-level=1
   (this command line will change the configuration file config/multiotp.ini)
3) Define the timeout after which you will switch to the next server(s), and
   on the local cache if no server available: multiotp -config server-timeout=3
   (this command line will change the configuration file config/multiotp.ini)
4) Last but not least, define the server(s) you want to connect with:
   multiotp -config server-url=http://ip.address.of.server:8112;http://url2
   (this command line will change the configuration file config/multiotp.ini)
5) Check your installation on the client by typing
   multiotp -display-log -log -debug "user" "token", where "user" is an
   existing user and "token" is the generated token for this user.
   If you have created a user with a prefix PIN, don't forget to type the prefix
   PIN before the displayed token.
   Example without a prefix PIN: multiotp test 457863
   Example with the "1234" prefix PIN: multiotp test 1234457863

   
HOW TO INSTALL A CENTRALIZED STRONG AUTHENTICATION SERVER
FOR STRONG AUTHENTICATION ON DESKTOPS ?
=========================================================
1) Install a client/server multiOTP environment like explained above.
2) On each client, install MultiOneTimePassword Credential Provider (mOTP-CP)
   (https://github.com/LastSquirrelIT/MultiOneTimePassword-CredentialProvider).
   It works with Windows Vista/7/2008/8/2012 in both 32 and 64 bits.
3) During the installation, specify the folder on the client where the
   multiotp.exe file is installed and configured.


HOW TO BUILD A RASPBERRY PI STRONG AUTHENTICATION SERVER ?
==========================================================
0) If you want to download a multiOTP Raspberry Pi image ready to use, follow this URL:
   http://download.multiotp.net/raspberry/
   
   Nano-computer name: multiotp
   IP address: 192.168.1.44 (netmask: 255.255.255.0, default gateway: 192.168.1.1)
   Username: pi
   Password: raspberry
   
   You can now flash the SD (check point 3) and 4) if needed), put the SD Card
   into the Raspberry Pi and boot it. You can go directly to point 15)
   
1) If you want to use a battery backed up Real Time Clock, install it now in your
   Raspberry Pi, the drivers for these models are included in the package:
     http://afterthoughtsoftware.com/products/rasclock
     http://www.cjemicros.co.uk/micros/products/rpirtc.shtml
     http://www.robotshop.com/ca/en/mini-real-time-clock-rtc-module.html
     http://nicegear.co.nz/raspberry-pi/high-precision-real-time-clock-for-raspberry-pi/
   
2) Download the last image of Raspbian to be flashed
   http://downloads.raspberrypi.org/raspbian_latest (currently 2014-09-09-wheezy-raspbian.zip)

3) Format your SD Card using the SD Card Association’s formatting tool
   https://www.sdcard.org/downloads/formatter_4/

4) Flash the raw image using UNIX tool dd or Win32DiskImager for Windows
   (http://sourceforge.net/projects/win32diskimager/files/latest/download).
   This should take about 10 minutes.

5) Copy all files from multiotp/raspberry/boot-part to the root of the SD Card
   (it could overwrite some files like config.txt)

6) When copy is done, eject the SD Card

7) Connect the Raspberry Pi to the local network

8) Put the SD card into the Raspberry Pi and boot it

9) Login directly on your Raspberry Pi, or using SSH, with the default username "pi" and the password "raspberry"

10) Launch the initial configuration by typing sudo raspi-config

11) Choose the following options
    1) Expand Filesystem
    2) Change User Password
    4) Internationalisation Options (if needed)
    8) Advanced Options
       A2 Hostname (change the hostname to your favorite name, for example "multiotp")

12) Select Finish and answer "<Yes>" to reboot, ore type "sudo reboot"

13) Login again directly (after about 30 seconds) on your Raspberry Pi, or using SSH, with the default username "pi" and your new password

14) Type "sudo /boot/install.sh"
    Everything is done automatically (it will take about 35 minutes) and the Raspberry Pi is rebooted automatically

15) The fixed IP address is set to 192.168.1.44, with a default gateway at 192.168.1.1
    To adapt the network configuration, edit the file /etc/network/interfaces

16) Congratulations! You have now an open source and fully OATH compliant
    strong two factors authentication server!
    Surf on http(s)://192.168.1.44 to use the basic interface (admin / 1234)

17) The default radius secret is set to myfirstpass for the subnet 192.168.0.0/16.
    To adapt the freeradius configuration, edit the file /etc/freeradius/clients.conf.


COMPATIBLE CLIENTS APPLICATIONS AND DEVICES
===========================================
MultiOneTimePassword Credential Provider (mOTP-CP)
If you want to have strong authentication on Windows logon, have a look at the
open source MultiOneTimePassword Credential Provider from Last Squirrel IT.
It works with Windows Vista/7/2008/8/2012 in both 32 and 64 bits.
The Credential Provider is using directly a local version of multiOTP which
can be configured as a client of a centralized multiOTP server (with caching support)
(https://github.com/LastSquirrelIT/MultiOneTimePassword-CredentialProvider)

LSE Experts is providing a commercial Radius Credential Provider which can talk
directly with any radius server to check the token. multiOTP will work with it.
(http://www.lsexperts.de)

Any firewall can connect with the Radius protocol to a multiOTP radius server.
On advanced firewalls like the ZyXEL ZyWALL USG series, you can do some advanced
things like:
- receiving a specific group for each multiOTP user (using the Filter-Id
option). This is very useful to allow specific rules for some groups.
- VPN connections can be set-up to have a strong authentication (X-Auth).
- Strong Web authentication can be combined with specific firewall rules.


EXTERNAL PACKAGES AND SOFTWARE USED

    CryptoJS 3.1 (BSD New)
    This product contains software provided by Jeff Mott
    https://code.google.com/p/crypto-js/
    
    FreeRADIUS 2.2.3 for Windows (BSD)
    This product contains software provided by FreeRADIUS team, sfreschi and its contributors.
    http://sourceforge.net/projects/freeradius/

    Mongoose Web Server 3.7 for Windows (GPLv2)
    Cesanta Software
    http://mongoose.googlecode.com/files/mongoose_php_bundle_3.7.zip
    
    phpseclib 0.3.8 (MIT License)
    MMVI Jim Wigginton
    http://phpseclib.sourceforge.net/

    PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY 2.1 (LGPLv2.1)
    Scott Barnett
    http://adldap.sourceforge.net/

    PHP radius class 1.2.2 (LGPLv3)
    André Liechti
    http://developer.sysco.ch/php/

    PHP Syslog class 1.1.2 (FREE "AS IS")
    André Liechti
    http://developer.sysco.ch/php/

    QRcode image PHP scripts 0.50j (FREE "AS IS")
    Y. Swetake
    http://www.swetake.com/qr/index-e.html

    status_bar.php (2010) (FREE "AS IS")
    dealnews.com, Inc.
    http://brian.moonspot.net/status_bar.php.txt

    TCPDF 6.0.061 (LGPLv3)
    Nicola Asuni
    http://www.tcpdf.org/

    XML Parser Class 1.3.0 (LGPLv3)
    Adam A. Flynn
    http://www.criticaldevelopment.net/xml/

    XPertMailer package 4.0.5 (LGPLv2.1)
    Tanase Laurentiu Iulian
    http://xpertmailer.sourceforge.net/
    
    The source files can be downloaded at http://download.multiotp.net/multiotp.zip

 
MULTIOTP PHP CLASS DOCUMENTATION
================================
Have a look into the source code if you want to know how to use it,
and you may also check multiotp.cli.header.php which implements the class.


MULTIOTP COMMAND LINE TOOL
==========================

multiOTP 4.3.2.6 (2015-07-18)
(c) 2010-2015 SysCo systemes de communication sa
http://www.multiOTP.net   (you can try the [Donate] button ;-)

Script folder: D:\Data\projects\multiotp\core\

multiotp will check if the token of a user is correct, based on a specified
algorithm (currently Mobile-OTP (http://motp.sf.net), OATH/HOTP (RFC 4226) 
and OATH/TOTP (RFC 6238) are implemented). PSKC format supported (RFC 6030).
Supported encryption methods are PAP and CHAP.
Yubico OTP format supported (44 bytes long, with prefixed serial number).
SMS-code are supported (current providers: aspsms,clickatell,intellisms).
Customized SMS sender program supported by specifying exec as SMS provider.

Google Authenticator base32_seed tokens must be of n*8 characters.
Google Authenticator TOTP tokens must have a 30 seconds interval.
Available characters in base32 are only ABCDEFGHIJKLMNOPQRSTUVWXYZ234567

To quickly create a user, use the -fastcreate option with the name of the user.
A quickly created user is compatible with Google Auth (30 seconds, 6 digits).
Depending on the prefix PIN option (WHICH IS ENABLED BY DEFAULT), a prefix PIN
will be requested or not before the displayed token.
If the PIN is not given, it is generated randomly.

To quickly create a user without a prefix PIN request, use -fastcreatenopin

To quickly create a user with a prefix PIN request, use -fastecreatewithpin

If a token is locked (return code 24), you have to resync the token to unlock.
Requesting an SMS token (put sms as the password), and typing the received
 token correctly will also unlock the token.

The check will return 0 for a correct token, and the other return code means:

Return codes:

 0 OK: Token accepted  
10 INFO: Access Challenge returned back to the client  
11 INFO: User successfully created or updated  
12 INFO: User successfully deleted  
13 INFO: User PIN code successfully changed  
14 INFO: Token has been resynchronized successfully  
15 INFO: Tokens definition file successfully imported  
16 INFO: QRcode successfully created  
17 INFO: UrlLink successfully created  
18 INFO: SMS code request received  
19 INFO: Requested operation successfully done  
21 ERROR: User doesn't exist  
22 ERROR: User already exists  
23 ERROR: Invalid algorithm  
24 ERROR: User locked (too many tries)  
25 ERROR: User delayed (too many tries, but still a hope in a few minutes)  
26 ERROR: The token has already been used  
27 ERROR: Resynchronization of the token has failed  
28 ERROR: Unable to write the changes in the file  
29 ERROR: Token doesn't exist  
30 ERROR: At least one parameter is missing  
31 ERROR: Tokens definition file doesn't exist  
32 ERROR: Tokens definition file not successfully imported  
33 ERROR: Encryption hash error, encryption key is not matching  
34 ERROR: Linked user doesn't exist  
35 ERROR: User not created  
37 ERROR: Token already attributed  
38 ERROR: User is desactivated  
39 ERROR: Requested operation aborted  
41 ERROR: SQL error  
50 ERROR: QRcode not created  
51 ERROR: UrlLink not created (no provisionable client for this protocol)  
60 ERROR: No information on where to send SMS code  
61 ERROR: SMS code request received, but an error occurred during transmission  
62 ERROR: SMS provider not supported  
70 ERROR: Server authentication error  
71 ERROR: Server request is not correctly formatted  
72 ERROR: Server answer is not correctly formatted  
80 ERROR: Server cache error  
81 ERROR: Cache too old for this user, account autolocked  
98 ERROR: Authentication failed (wrong token length)  
99 ERROR: Authentication failed (and other possible unknown errors)  


Usage:

 PLEASE NOT THAT BY DEFAULT, A PREFIX PIN IS REQUIRED.

 multiotp user token (to check if the token is accepted)
 multiotp -checkpam (to check with pam-script, using PAM_USER and PAM_AUTHTOK)

 multiotp -requiresms user (generate and send an SMS token to the user)
 multiotp user sms (send an SMS token to the user)

 multiotp user [-chap-id=0x..] -chap-challenge=0x... -chap-password=0x...
   (the first byte of the chap-password value can contain the chap-id value)

 multiotp -fastcreate user [pin] (create a Google Auth compatible token)
 multiotp -fastcreatenopin user [pin] (create a user without a prefix PIN)
 multiotp -fastecreatewithpin user [pin] (create a user with a prefix PIN)
 multiotp -createga user base32_seed [pin] (create Google Authenticator user)
 multiotp -create [-no-prefix-pin] user algo seed pin digits [pos|interval]
 multiotp -create -token-id [-no-prefix-pin] [-prefix-pin] user token-id pin

  token-id: id of the previously imported token to attribute to the user
      user: name of the user (should be the account name)
      algo: available algorithms are mOTP, HOTP and TOTP
      seed: hexadecimal seed of the token
       pin: private pin code of the user
    digits: number of digits given by the token
       pos: for HOTP algorithm, position of the next awaited event
  interval: for mOTP and TOTP algorithms, token interval time in seconds

 multiotp -import tokens_definition_file [key|pass] (auto-detect format)
 multiotp -import-csv csv_tokens_file.csv (tokens definition in a file)
   (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
 multiotp -import-pskc pskc_tokens_file.pskc [key|pass] (PSKC format, RFC 6030)
 multiotp -import-yubikey yubikey_traditional_format_log.csv (YubiKey)
 multiotp -import-dat importAlpine.dat (SafeWord/Aladdin/SafeNet tokens)
 multiotp -import-alpine-xml alpineXml.xml (SafeWord/Aladdin/SafeNet)
 multiotp -import-xml xml_tokens_definition_file.xml (old Feitian)
 multiotp -import-sql tokens_definition_file.sql (ZyXEL/Authenex)

 multiotp -qrcode user png_file_name.png (only for TOTP and HOTP)
 multiotp -urllink user (only for TOTP and HOTP, generate provisioning URL)

 multiotp -scratchlist user (generate & display scratch passwords for the user)

 multiotp -resync [-status] user token1 token2 (two consecutive tokens)
 multiotp -update-pin user pin

 multiotp -[des]activate user
 multiotp -[un]lock user

 multiotp -delete user

 multiotp -user-info user

 multiotp -config option1=value1 option2=value2 ... optionN=valueN
  options are    autoresync: [0|1] enable/disable autoresync during login
      attributes-to-encrypt: specific attributes list to encrypt, must be
                             surrounded by *, like '*token_seed*user_pin*'
               backend-type: backend storage type (files|mysql)
        clear-otp-attribute: attribute to return for the clear OTP
                             (for example 'ietf|2' for TekRADIUS)
                      debug: [0|1] enable/disable enhanced log information
                             (code result are also displayed on the console)
               debug-prefix: add a prefix when using the debug mode
                             (for example 'Reply-Message := ' for FreeRADIUS)
 default-request-prefix-pin: [0|1] prefix PIN enabled/disabled by default
   default-request-ldap-pwd: [0|1] LDAP/AD password enabled/disabled by default
                display-log: [0|1] enable/disable log display on the console
            group-attribute: attribute to return for the group membership
                             (for example 'Filter-Id' for FreeRADIUS)
                     issuer: default name of the issuer of the (soft) token
        ldap-account-suffix: LDAP/AD account suffix
             ldap-activated: [0|1] enable/disable LDAP/AD support
               ldap-base-dn: LDAP/AD base
               ldap-bind-dn: LDAP/AD bind 
         ldap-cn-identifier: LDAP/AD cn identifier (default is sAMAccountName)
    ldap-domain-controllers: LDAP/AD domain controller(s), comma separated
       ldap-group-attribute: LDAP/AD group attribute (default is memberOf)
   ldap-group-cn-identifier: LDAP/AD group cn identifier
                             (default is sAMAccountName for AD, cn for LDAP)
              ldap-in-group: LDAP/AD group(s) in which users should be in
       ldap-network-timeout: LDAP/AD network timeout (in seconds)
                  ldap-port: LDAP/AD port (default is set to 389)
       ldap-server-password: LDAP/AD server password
           ldap-server-type: [1|2] LDAP/AD server type (1=AD, 2=standard LDAP)
                   ldap-ssl: [0|1] enable/disable LDAP/AD SSL connection
            ldap-time-limit: LDAP/AD number of sec. to wait for search results
                        log: [0|1] enable/disable log permanently
     radius-reply-attributor: [ = |=] how to attribute a value
                             ('=' for TekRADIUS, ' = ' for FreeRADIUS)
     radius-reply-separator: [,|:|;|cr|crlf] returned attributes separator
                             ('crlf' for TekRADIUS, ',' for FreeRADIUS)
          self-registration: [1|0] enable/disable self-registration of tokens
         server-cache-level: [0|1] enable/allow cache from server to client
      server-cache-lifetime: lifetime in seconds of the cached information
              server-secret: shared secret used for client/server operation
             server-timeout: timeout value for the connection to the server
                server-type: [xml] type of the server
                             (only xml server are able to do caching)
                 server-url: full url of the server for client/server mode
                             (server_url_1;server_url_2 is accepted)
                 sms-api-id: SMS API id (clickatell only, give your XML API id)
                             with exec as provider, define the script to call
                             (available variables: %from, %to, %msg)
                sms-message: SMS message to display before the OTP
             sms-originator: SMS sender (if authorized by provider)
               sms-password: SMS account password
               sms-provider: SMS provider (aspsms,clickatell,intellisms,exec)
                sms-userkey: SMS account username or userkey
                 sql-server: SQL server (FQDN or IP)
               sql-username: SQL username
               sql-password: SQL password
               sql-database: SQL database
           sql-config-table: SQL config table, default is multiotp_config
          sql-devices-table: SQL devices table, default is multiotp_devices
              sql-log-table: SQL log table, default is multiotp_log
           sql-tokens-table: SQL tokens table, default is multiotp_tokens
            sql-users-table: SQL users table, default is multiotp_users
   tel-default-country-code: Default country code for phone number
 token-serial-number-length: Length of the serial number of the tokens
                             (used for self-registration)

 multiotp -initialize-backend (when all options are set, it will initialize
                               the backend, including creating the tables)

 multiotp -set user option1=value1 option2=value2 ... optionN=valueN
  options are  email: update the email of the user
         description: set a description to the user, used for example during
                      the QRcode generation as the description of the account
               group: set/update the group of the user
            ldap-pwd: [0|1] the LDAP/AD password is used instead of the pin
                 pin: set/update the private pin code of the user
          prefix-pin: [0|1] the pin and the token must by merged by the user
                      (if your pin is 1234 and your token displays 5556677,
                      you will have to type 1234556677)
                 sms: set/update the sms phone number of the user


LDAP/AD integration:

 multiotp -ldap-check
 multiotp -ldap-user-info user
 multiotp -ldap-users-list
 multiotp -ldap-users-sync


Other commands:

 multiotp -phpinfo
 multiotp -showlog
 multiotp -tokenslist
 multiotp -userslist
 multiotp -lockeduserslist


Other parameters:

 -base-dir=/full/path/to/the/main/folder/of/multiotp/
           (if the script folder is wrongly detected, this will fix the issue)


Switches:

 -debug          Enhanced log information activated and code result on console
                 (the permanent state of debug can be set with -config debug=1)
 -display-log    Log information will also be displayed on the console
                 (the permanent state can be set with -config display-log=1)
 -help           Display this help page
 -keep-local     Keep local user even if the server doesn't have it
                 (if the server doesn't have it, the local one will be checked)
 -log            Log operation in the log subdirectory or in the database
                 (the permanent state of log can be set with -config log=1)
 -mysql          MySQL connection information, comma separated (server,user,
                 password,database[,log_table[,users_table[,tokens_table]]])
                 (this switch is DEPRECATED, use the -config switch instead)
 -no-prefix-pin  No prefix pin must be merged with the token by the user
                 (this switch is DEPRECATED, use the -set switch instead)
 -param          All parameters are logged for debugging purposes
 -prefix-pin     The pin and the token must be typed merged by the user
                 (if you pin is 1234 and your token displays 5556677,
                  you will have to type 1234556677)
                 (this switch is DEPRECATED, use the -set switch instead)
 -request-nt-key This will return the NT_KEY to the radius server
 -status         Display a status bar during resynchronization
 -version        Display the current version of the library
 -php-version    Display the current version of the running PHP interpreter


Examples:

 multiotp -display-log -log -debug jimmy ea2315
 multiotp -display-log -log anna 546078
 multiotp -display-log -log -checkpam
 multiotp john 5678124578

 multiotp jimmy sms

 multiotp -fastcreate gademo
 multiotp -debug -createga gauser 2233445566777733
 multiotp -debug -create -prefix-pin alan TOTP 3683453456769abc3452 2233 6 60
 multiotp -debug -create -prefix-pin anna TOTP 56821bac24fbd2343393 4455 6 30
 multiotp -debug -create -prefix-pin john HOTP 31323334353637383930 5678 6 137
 multiotp -debug -create -token-id -prefix-pin rick 2010090201901 2345
 multiotp -log -create jimmy mOTP 004f5a158bca13984d349a7f23 1234 6 10

 multiotp -scratchlist gademo

 multiotp -set gademo description="VPN code for gademo"
 multiotp -set gademo sms=41791234567

 multiotp -debug -import tokens.pskc "1234 5678 9012 3456 7890 1234 5678 9012"
 multiotp -debug -import-pskc tokens.pskc "qwerty"
 multiotp -debug -import 10OTP_data01_upgrade.sql
 multiotp -debug -import-dat importAlpine.dat

 multiotp -debug -qrcode gademo gademo.png
 multiotp -debug -urllink john

 multiotp -resync john 5678456789 5678345231
 multiotp -resync -status anna 4455487352 4455983513
 multiotp -update-pin alan 4417

 multiotp -config debug-prefix="Reply-Message := "

 multiotp -config server-cache-level=1 server-cache-lifetime=15552000
 multiotp -config server-secret=MySharedSecret server-type=xml
 multiotp -config server-timeout=3
 multiotp -config server-url=http://my.server/multiotp/;my.server2:8112/secure/

 multiotp -config sms-provider=clickatell sms-userkey=CL1 sms-password=PASS
 multiotp -config sms-api-id=1234567
 multiotp -config sms-message="Your SMS-code is:" sms-originator=Company
 multiotp -config sms-message="Type %s as code" sms-originator=0041797654321

 multiotp -config sms-provider=exec sms-api-id="/path/to/app %from %to "%msg""

 multiotp -config token-serial-number-length=10,12

 multiotp -config backend-type=mysql sql-server=fqdn.or.ip sql-database=dbname
 multiotp -config sql-username=user sql-password=pass
 multiotp -initialize-backend


multiOTP web service is working fine with any web server supporting PHP.
 - nginx is a light one under Linux (http://nginx.org/)
 - Mongoose is a light one under Windows (http://code.google.com/p/mongoose/)
 - and many others like Apache HTTP Server (http://httpd.apache.org/)

multiOTP is working fine with FreeRADIUS under Linux (http://freeradius.org/)

multiOTP is working fine under Windows with WinRADIUS, a port of FreeRADIUS
(http://winradius.eu/)

multiOTP is also working fine with another port of FreeRADIUS
for Windows (http://sourceforge.net/projects/freeradius/)

multiOTP can be combined with a Raspberry Pi (http://www.raspberrypi.org/) in
order to have a very low budget strong authentication device. Please look at
the readme file in order to learn how to set it up in a few steps.

When used with TekRADIUS (http://www.tekradius.com) the External-Executable
must be called like this: C:\multiotp\multiotp.exe %ietf|1% %ietf|2%

Some of other products and services based on multiOTP
 - multiOTP Pro 405V  Pro version with full web GUI in a tiny virtual appliance
                      (http://www.multiOTP.com)
 - multiOTP Pro 420B  Pro version with full web GUI in a tiny hardware device
                      (http://www.multiOTP.com)
 - secuPASS.net       simple SMS trusting service for free WLAN Hotspot
                      (http://www.secuPASS.net)
 - mOTP-CP            an Open-Source Credential Provider for the Windows Logon
                      (https://goo.gl/Y8g4ON)
 - ownCloud OTP       One Time Password app for ownCloud (http://owncloud.org)
                      (http://goo.gl/mKjt43)

Visit http://forum.multiotp.net/ for additional support


