multiOTP open source
====================
multiOTP open source is a GNU LGPL implementation of a strong two-factor authentication PHP class  
multiOTP open source is OATH certified for HOTP/TOTP

(c) 2010-2019 SysCo systemes de communication sa  
http://www.multiOTP.net/

Current build: 5.6.1.5 (2019-10-23)

Binary download: https://download.multiotp.net/ (including virtual appliance image)

Docker container available: **docker run --mount source=multiotp-data,target=/etc/multiotp -p 80:80 -p 443:443 -p 1812:1812/udp -p 1813:1813/udp -d multiotp/multiotp-open-source**

**A Dockerfile is included in the distribution ZIP file**

Binary download of the multiOTP open source Credential Provider V2 for Windows 7/8/8.1/10/2012(R2)/2016 with options like RDP only and UPN name support : https://download.multiotp.net/credential-provider/

[![Donate via PayPal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&currency_code=USD&business=paypal@sysco.ch&item_name=Donation%20for%20multiOTP%20project)
*Please consider supporting this project by making a donation via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&currency_code=USD&business=paypal@sysco.ch&item_name=Donation%20for%20multiOTP%20project)*

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

You can also have a look on on http://www.multiotp.com for multiOTP Pro and
multiOTP Enterprise, which are commercial editions of ready to use virtual
appliances including more features like:
 - Full Web GUI interface
 - Automated provisioning of new account (based on Active Directory)
 - High Availability master-slave support (Enterprise Edition)
 - Web API support (Enterprise Edition)
 - free virtual appliances available, including a free lifetime one user licence
 - online demo of the full Web GUI interface
 - ...


The multiOTP class supports currently the following algorithms and RFC's:
- RFC1994 CHAP (Challenge Handshake Authentication Protocol)
- RFC2433 MS-CHAP (Microsoft PPP CHAP Extensions)
- RFC2487 SMTP Service Extension for Secure SMTP over TLS
- RFC2759 MS-CHAPv2 (Microsoft PPP CHAP Extensions, Version 2)
- RFC2821 SMTP (Simple Mail Transfer Protocol)
- RFC4226 OATH/HOTP (HOTP: An HMAC-Based One-Time Password Algorithm)
- RFC5424 Syslog Protocol (client)
- RFC6030 PSKC (Additional Portable Symmetric Key Container Algorithm Profiles)
- RFC6238 OATH/TOTP (TOTP: Time-Based One-Time Password Algorithm)
- Yubico OTP (http://yubico.com/yubikey)
- mOTP (http://motp.sourceforge.net)
- OATH/HOTP or OATH/TOTP, base32/hex/raw seed, QRcode provisioning
  (FreeOTP, Google Authenticator, ...)
- SMS tokens (using Afilnet, aspsms, Clickatell, eCall, IntelliSMS, Nexmo,
  NowSMS, SMSEagle, Swisscom LA REST, any custom provider, your own script)
- TAN (emergency scratch passwords)

This package was initially published here : http://syscoal.users.phpclasses.org/package/6373.html
For more PHP classes, have a look on [PHPclasses.org](http://syscoal.users.phpclasses.org/browse/), where a lot of authors are sharing their classes for free.

TABLE OF CONTENTS
=================
 * Donations and sponsoring
 * Wishlist for future releases
 * How can I upgrade from a previous version ?
 * What's new in the releases
 * Change Log of released version
 * Content of the package
 * Typical tree of a working Linux installation
 * How can I create myself the different versions ?
 * When and how can I use this package ?
 * What is the prefix PIN option ?
 * How the lockout of an account is working ?
 * How to debug ?
 * How to install the multiOTP web service under Windows ?
 * How to install the multiOTP radius server under Windows ?
 * Configuring multiOTP with TekRADIUS or TekRADIUS LT under Windows
 * How to install the multiOTP web service under Linux ?
 * Configuring multiOTP with FreeRADIUS 2.x under Linux
 * Configuring multiOTP with FreeRADIUS 3.x under Linux
 * How to configure multiOTP to synchronized the users from an Active Directory ?
 * How to configure multiOTP to synchronized the users from a standard LDAP ?
 * How to configure multiOTP to use the client/server feature ?
 * How to build a Raspberry Pi strong authentication server ?
 * How to install a local only strong authentication on a Windows machine ?
 * How to install a centralized strong authentication server
   for strong authentication on Windows desktops or RDP ?
 * OpenSSL options for LDAPS
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

Thanks to our main donators and sponsors:  
CSG De Lage Waard (NL)
Donator AB (SE)  
Henk van der Helm (NL)  
Hermann Wegener GmbH & Co. KG (DE)  
SerNet GmbH (DE)  
SKB Kontur (RU)


WISHLIST FOR FUTURE RELEASES
============================
- RADIUS challenge/response support
- Multiple hardware tokens support for one account
- Generic web based SMS provider support
- Radius gateway support
- YubiCloud support
- FIDO support (SOAP service)
- Bootstrap/VueJS frontend
- SMS-revolution SMS provider support
- Doxygen documentation format
- Users CSV import
  (username;pin;prefix_pin_needed;email;sms;serial_number;manufacturer;algorithm;seed;digits;interval_or_event)


HOW CAN I UPGRADE FROM A PREVIOUS VERSION ?
===========================================
!!! Be careful when you upgrade your multiOTP open source Virtual Appliance !!!
The multiOTP open source Virtual Appliance is using the files in
raspberry/boot-part/multiotp-tree/usr/local/bin/multiotp, with
config and backend folders defined to be located in /etc/multiotp/

If you are currently using the multiOTP open source Virtual Appliance, you can upgrade
the multiOTP version by copying the extracted content of the folder and subfolders from
raspberry/boot-part/multiotp-tree/usr/local/bin/multiotp to /usr/local/bin/multiotp  
An update through the web interface should be available in the future

If you are currently using the multiOTP open source linux files, you can
upgrade your installation by copying the extracted content of the folder and
subfolders from linux to your current multiOTP folder

!!! since 5.0.4.6 under Linux, the config, devices, groups, tokens and users folders are now
always located in /etc/multiotp/. Please be sure to make the move when you are upgrading !!!
(before 5.0.4.6, theses subfolders where located just below the main multiOTP folder)

If you are currently using the multiOTP open source windows files, you can
upgrade your installation by copying the extracted content of the folder and
subfolders from windows to your current multiOTP folder


WHAT'S NEW IN THE RELEASES
==========================
# What's new in 5.6 releases
- Better PHP 7.3 support

# What's new in 5.5 releases
- Debian 10.x (buster) binary images support (64 bits)
- Out of sync detection with specific error message (for example hardware tokens not used for a long time)

# What's new in 5.4 releases
- SMS providers added (Swisscom LA REST, Afilnet, Clickatell2, eCall, Nexmo, NowSMS, SMSEagle)
- Generic SMS custom provider
- Debian 9.x (stretch) binary images support
- New Raspberry images support for Raspberry Pi 1B/1B+/2B/3B/3B+

# What's new in 5.3 releases
- Multiple semicolon separated "Users DN" supported for AD/LDAP synchronization
- New windows executable build process, using PHP 7.2.8
- Special all-in-one-file created with the updated Enigma Virtual Box version 9.00
- without2FA algorithm now available (useful to do 2FA only for some accounts and not for others)

# What's new in 5.2 releases
- Multiple semicolon separated Users DN supported (since 5.2.0.3)
- Active Directory nested groups support
  (user1 in groupA, groupA in groupB, setting the OTP groups to "groupB" will add user1)
- Enhanced AD/LDAP support for huge Microsoft Active Directory
- Base DN and Users DN are now two different parameters (Users DN optional)

# What's new in 5.1 releases
- Dockerfile available (5.1.1.2)
- Credential Provider registry entries are always used when calling multiOTP.exe (5.1.0.6)
- Expired AD/LDAP password support
- multiOTP Credential Provider (for Windows) improvements
 (user@domain.name UPN support, default domain name supported and displayed, SMS request link)
- Better unicode handling, multibyte fonctions used when needed (mb_strtolower(), ...)

# What's new in 5.0 releases
- Better FreeRADIUS 3.x documentation
- New QRCode provisioning format for mOTP (compatible with OTP Authenticator) (5.0.5.2)
- Important, under Linux, the config, devices, groups, tokens and users folders are now always
  located in /etc/multiotp/. Please be sure to make the move when you are upgrading (5.0.4.6)
- PostgreSQL support, based on source code provided by Frank van der Aa (5.0.4.5)
- Restore configuration added in Web GUI (5.0.4.5)
- New GetDelayedUsersList() method (5.0.3.6)
- SetUserTokenSeed() and SetTokenSeed() methods accept now also base32 and raw binary (5.0.3.6)
- Multiple groups per user is now supported (not all devices support multiple groups) (5.0.3.4)
- Using AD/LDAP password instead of PIN code can be overwritten or not for all synchronized users
- New windows executable build process, using PHP 7.x (5.0.3.4)
- It's now possible to do several commands at once with the CLI edition (5.0.3.4)
- The default TOTP/HOTP generator for Android/iOS is now FreeOTP Authenticator
- EXE files are now signed in SHA256 (5.0.3.4)
- New LDAP cache management to support huge AD/LDAP, with cache on disk (5.0.3.4)
- New PurgeLockFolder() and PurgeLdapCacheFolder() methods (5.0.3.4)
- If the user dialin IP address is defined, Framed-IP-Address and Framed-IP-Mask
  are delivered in the RADIUS answer (5.0.3.0)
- The user dialin IP address is synchronized from the Active Directory msRADIUSFramedIPAddress
  attribute (5.0.3.0)
- The first matching group defined in AD/LDAP group(s) filtering is now defined for the user
  (this group is returned as the Filter-Id (11) option in a successful RADIUS answer) (5.0.1.0)
- SOAP service available (compatible with OpenOTP SOAP service)
- It's now possible to select a specific LDAP/AD attribute used as the synchronised account name
  SetLdapSyncedUserAttribute(), GetLdapSyncedUserAttribute()
- Cached requests supported (cached during a specific amount of time, useful for WebDAV,
  device option cache_result_enabled)
- A try on the previous password is rejected, but the error counter is not incremented
- ForceNoDisplayLog() method added, in order to be able to disable log on display in server mode
- YubicoOTP private id check is now implemented
- SSL AD/LDAP also supported with Windows 2012 server
- SyncLdapUsers is now using a semaphore file in order to avoid concurrent process for large AD/LDAP sync
  (tested with 1'000 groups, 100'000 users, 1'000 users in the LDAP sync group)
- AD/LDAP additional log information
- Special chars support enhanced in LDAP class (as described in RFC4515)
- The default ldap_group_cn_identifier is now cn instead of sAMAccountName
- Enhanced SMS support for Clickatell, SSL is now also working
- Bug fix concerning QRcode generation for mOTP
- Weekly anonymized stats added (can be disabled)

# What's new in 4.3 releases
- Virtual Appliances are now available (VMware, Hyper-V, generic OVA) (4.3.2.5)
- Raspberry Pi edition has now a special proxy to speed up the command line (4.3.1)
- Generic LDAP support (in addition to Microsoft Active Directory support) (4.3.1)
- New AD/LDAP faster sync algorithm to support larger AD (4.3.0)
- If users are synced using AD/LDAP, it's now possible to use
  the AD/LDAP password instead of the PIN code (4.3.0)
- Yubico OTP support, including keys import using the log file in Traditional format (4.3.0)
- Resync during authentication (autoresync) is now better handled in the class directly
- QRCode generation for mOTP (compatible with Token2 App for iOS, Android and Windows Phone)

# What's new in 4.2 releases
- A new option -user-info is now available (4.2.4.1)
- Tokens CSV import (4.2.4.1)
- NT_KEY can be displayed for further handling by FreeRADIUS (4.2.4.1)
- Lot of new QA tests, more than 60 different tests (4.2.4.0)
- Better MySQL support with mysqli library support (4.2.4.0)
- If activated, prefix PIN is now also requested for SMS authentication (4.2.2.0)
- Web GUI is complete for a simple usage (4.2.2.0)
- Some values can now go back to TekRADIUS (4.2.2.0)
- AD/LDAP is now fully supported (4.2.1.0)
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
  information in a MySQL backend instead of flat files.


CHANGE LOG OF RELEASED VERSIONS
===============================
```
2019-10-23 5.6.1.5 FIX: Separated configuration/statistics storage handling
2019-10-22 5.6.1.3 ENH: Better PHP 7.3 support
                   ENH: Base32 encoder/decoder new implementation
                   ENH: During WriteConfigData, loop on the current values, and check with the old values
                   ENH: Enhanced internal tests
2019-09-02 5.5.0.3 ENH: Give an info if time based token is probably out of sync (in a window 10 time bigger)
                        (for example for hardware tokens not used for a long time)
                   ENH: Modifications for Debian 10.x (buster) binary images support (64 bits)
2019-03-29 5.4.1.8 ENH: Enhanced error messages, more log information
                   ENH: In debug mode, display an error if logfile cannot be written
                   ENH: Global Access-Challenge support
2019-01-30 5.4.1.7 FIX: IsTemporaryBadServer function (thanks to brownowski on GitHub)
                   ENH: New QRcode library used (without external files dependency)
                   ENH: New Raspberry images support for Raspberry Pi 1B/1B+/2B/3B/3B+
2019-01-25 5.4.1.6 FIX: If any, clean specific NTP DHCP option at every reboot
2019-01-18 5.4.1.4 ENH: Modifications for Debian 9.x (stretch) binary images support
2019-01-07 5.4.1.1 ENH: Raspberry Pi 3B+ support
2018-11-13 5.4.0.2 ENH: Import of PSKC definition files with binary decoding key file
                   ENH: Added Swisscom LA REST, Afilnet, Clickatell2, eCall, Nexmo, NowSMS, SMSEagle and custom SMS provider support
2018-09-14 5.4.0.1 FIX: Values of SetUserCacheLevel(), GetUserCacheLevel(), SetUserCacheLifetime()
                        and GetUserCacheLifetime() are not correctly initialized
                   ENH: Enigma Virtual Box updated to version 9.10 (to create the special all-in-one-file)
                   ENH: PHP 7.1.22 used in the one single file (only PHP < 7.2 is still compatible with Windows 7/2008)
                   ENH: Compatibility mode to Windows 7 automatically added for radiusd.exe during radius service installation
                   ENH: PHP display error flag is now set to off by default in the webservice under Windows
2018-08-26 5.3.0.3 FIX: Better without2FA algorithm support
                   FIX: Restore configuration has been fixed in the command line edition
                   ENH: Cache-level and cache-lifetime can be set separately for each user
                   ENH: In client/server mode, only unencrypted user attributes are sent back to a successful client request
                   ENH: Enhanced monitoring
2018-08-22 5.3.0.1 ENH: Monitoring fields added (create_host, create_time, last_update_host)
2018-08-21 5.3.0.0 FIX: stream_timeout is no more pushed to 20 seconds in PostHttpDataXmlRequest if we are in Credential Provider mode
                   FIX: RemoveTokenFromUser() method corrected. Token administrative information corrected,
                        new software token created for the user
                   ENH: Multiple semicolon separated "Users DN" supported for AD/LDAP synchronization
                   ENH: Additional debug messages for disabled users during synchronization
                   ENH: Enigma Virtual Box updated to version 9.00 (to create the special all-in-one-file)
                   ENH: PHP 7.2.8 used in the one single file
                   ENH: without2FA algorithm now available (useful to do 2FA only for some accounts and not for others)
2018-07-16 5.2.0.2 ENH: Active Directory nested groups support
                        (user1 in groupA, groupA in groupB, setting the OTP groups to "groupB" will add user1)
                   ENH: Enhanced AD/LDAP support for huge Microsoft Active Directory
                   ENH: "Base DN" and "Users DN" are now two different parameters ("Users DN" optional)
2018-03-20 5.1.1.2 FIX: typo in the source code of the command line option for ldap-pwd and prefix-pin
                   ENH: Dockerfile available
2018-03-05 5.1.0.8 FIX: Enigma Virtual Box updated to version 8.10 (to create the special all-in-one-file)
2018-02-27 5.1.0.7 FIX: [Receive an OTP by SMS] link is now fixed for Windows 10
2018-02-26 5.1.0.6 ENH: Credential Provider registry entries are now always used when calling multiOTP.exe
2018-02-21 5.1.0.5 FIX: To avoid virus false positive alert, multiOTP.exe is NO more packaged in one single file
                        using Enigma, a php folder is now included in the multiOTP folder
                   FIX: multiOTPOptions registry entry is now useless
2018-02-21 5.1.0.4 ENH: Credential Provider registry entries are used if available
2018-02-19 5.1.0.3 FIX: Better unicode handling, multibyte fonctions used when needed (mb_strtolower(), ...)
                   ENH: Expired AD/LDAP password support
                   ENH: multiOTP Credential Provider (for Windows) improvements
                         (user@domain.name UPN support, default domain name supported and displayed, SMS request link)
                   ENH: "force_no_prefix_pin" option for devices (for example if the device is a
                        computer with multiOTP credential Provider and AD/LDAP synced password)
2017-11-04 5.0.5.6 Better FreeRADIUS 3.x documentation
                   New radius tag prefix configuration option
                   New multiple groups device option
                   Some notice corrections (if the array element doesn't exist)
                   A user cannot be created with a leading backslash (fixed in FastCreateUser and CreateUserFromToken)
2017-09-29 5.0.5.2 The proposed mOTP generator for Android/iOS is now OTP Authenticator
                   New QRCode provisioning format for mOTP (compatible with OTP Authenticator)
2017-09-08 5.0.5.0 NirSoft nircmd.exe tool removed from the distribution (false virus detection)
                   Multiple URLs separator for client/server config is still ";", but [space] and "," are accepted
                   New developer mode for some specific detailed logs during development process only
2017-07-07 5.0.4.9 New methods: SetLdapTlsReqcert, GetLdapTlsReqcert, SetLdapTlsCipherSuite, GetLdapTlsCipherSuite
                    to change config parameters, instead of hard coded parameters (for SSL/TLS LDAP connection)
                   Fixed too much detailed information in the log when trying
                    to detect a token serial number for self-registration
2017-06-06 5.0.4.8 Fixed SSL/TLS LDAP failed connection for PHP 7.x (GnuTLS TLS1.2 restriction removed for PHP 7.x)
2017-06-02 5.0.4.6 Fixed a typo in the ReadCacheData method for PostgreSQL support (thanks Frank for the feedback)
                   Fixed default folder detection for the multiotp.exe file
                   Important, under Linux, the config, devices, groups, tokens and users folders are now always
                    located in /etc/multiotp/. Please be sure to make the move when you are upgrading
                   Cleaned some ugly PHP warnings when the backend is not initialized
2017-05-29 5.0.4.5 Restore configuration added in Web GUI
                   Fixed configuration file directory under Windows in Web GUI
                   Fixed path with spaces handling for the command line edition (thanks Scott for the feedback)
                   PostgreSQL support, based on source code provided by Frank van der Aa
2017-05-16 5.0.4.4 GetList() is now sorted with files backend
                   A replay during a defined delay (default 60 seconds) of the previous refused password is rejected,
                    but the error counter is not incremented (SetLastFailedWhiteDelay and GetLastFailedWhiteDelay)
                   A user cannot be created with a leading backslash
2017-02-23 5.0.3.7 Group names are now always trimed to avoid blank spaces
                   SetLinuxFolderMode() and GetLinuxFolderMode() methods added
2017-02-21 5.0.3.6 GetDelayedUsersList() method added
                   GetList() return now a sorted list
                   RestoreConfiguration() method updated, system configuration data can be ignored
                   SetUserTokenSeed() and SetTokenSeed() methods accept now also base32 and raw binary
                   The full windows package has been fixed and cleaned
2017-02-03 5.0.3.5 GetUserInfo method added
                   ImportTokensFromCsv fixed when the file is not readable
                   Fix possible endless loop when opening a file that exists but without the right to read it
2017-01-26 5.0.3.4 It's now possible to do several commands at once with the CLI edition
                   New overwrite_request_ldap_pwd option (enabled by default).
                    If overwrite is enabled, default_request_ldap_pwd value is forced during synchronization
                   Multiple groups per user is now supported (not all devices support multiple groups).
                    (radius reply attributor has been changed to += by default)
                   multiotp -delete-token command has been added in the CLI
                   -lock and -unlock command return now 19 (instead of 99) in the CLI
                   Better support of DialinIp functions in command line usage
                   New LDAP cache management to support huge AD/LDAP, with cache on disk (system temporary folder)
                   New PurgeLockFolder() and PurgeLdapCacheFolder() method
                   The default proposed TOTP/HOTP generator for Android/iOS is now FreeOTP Authenticator
                   Better Eastern European languages support
                   Multiple purpose tokens provisioning format PSKCV10,
                    like Gemalto e3050cL and t1050 tokens, is now supported.
                   Various bug fixes and enhancements when using the proxy mode.
2016-11-14 5.0.3.0 Log messages are better categorized
                   The user dialin IP address is synchronized from the
                    Active Directory msRADIUSFramedIPAddress attribute
                   New IP dialin methods : SetUserDialinIpAddress(), SetUserDialinIpMask(),
                    SetDefaultDialinIpMask(), GetUserDialinIpAddress(), GetUserDialinIpMask(),
                    GetDefaultDialinIpMask()
                   If the user dialin IP address is defined, Framed-IP-Address
                    and Framed-IP-Mask are delivered in the RADIUS answer
                   Enhanced token importation process (to support binary encryption key
                    in hexadecimal 0xAABBCC format)
2016-11-04 5.0.2.6 Better log message for automatically or manually created objects
                   External packages update
                   New GetUserLastLogin() and SetUserLastLogin() methods
                   Backup configuration file can now be restored in commercial
                    version without any changes
2016-10-16 5.0.2.5 Better SSL support using context if available (for PHP >= 5.3)
                   New methods SetTouchFolder(), GetTouchFolder(), TouchFolder(),
                    FolderTouched() to offer asynchronous implementation capabilities
                   New methods added for SOAP service
                   Weekly anonymized stats added (can be disabled).
                    Anonymized stats include the following information:
                     backend type, AD/LDAP used or not, OS version, PHP version,
                     library version, number of accounts defined, number of tokens defined.
                     They are sent on the stats.multiotp.net FQDN which is hosted in Switzerland.
                   It's possible to select a specific LDAP/AD attribute used as the synchronised
                    account name: SetLdapSyncedUserAttribute(), GetLdapSyncedUserAttribute()
                   An account can be tested from the dashboard
                   Unified configuration backup and restore format (BackupConfiguration)
                   Better support of MS-CHAPv2 in the provided appliances
                   Cached requests supported (cached during a specific amount of time,
                    useful for WebDAV authentication) (device option cache_result_enabled)
                   A try on the previous password is rejected,
                    but the error counter is not incremented
                   ForceNoDisplayLog() method added to disable log on display in server mode
                   XML parsing error are more verbose
                   XmlServer is now sending XML response with the specific Content-type: text/xml
                   YubicoOTP private id check is now implemented
                   SSL AD/LDAP also supported with Windows 2012 server
                   SyncLdapUsers is now using a semaphore file to avoid
                    concurrent process for large AD/LDAP sync
                    (tested with 1'000 groups, 100'000 users, 1'000 users in the LDAP sync group)
                   AD/LDAP additional log information
                   New GetNetworkInfo and SetNetworkInfo methods
                   Special chars support enhanced in LDAP class (as described in RFC4515)
                   The default ldap_group_cn_identifier is now cn instead of sAMAccountName
                   The first matching group defined in AD/LDAP group(s) filtering is now
                    defined for the user (this group is returned as the Filter-Id (11) option
                    in a successful RADIUS answer)
                   Enhanced SMS support for Clickatell, SSL is now also working
                   Bug fix concerning QRcode generation for mOTP
                   Code fixes
                   New AssignTokenToUser() and RemoveTokenFromUser() methods
2015-07-18 4.3.2.6 New ResetTempUserArray method (as we want to move away from global array in the near future)
                   For _user_data, default values are now extracted from the definition array
                   QRcode generation for mOTP (motp://[SITENAME]:[USERNAME]?secret=[SECRET-KEY])
2015-07-15 4.3.2.5 Calling multiotp CLI without parameter returns now error code 30 (instead of 19)
2015-06-24 4.3.2.4 multi_account automatic support
                   Scratch password generation (UTF)
2015-06-10 4.3.2.3 Enhancements for the Dev(Talks): demo
2015-06-09 4.3.2.2 Empty users are refused
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
2014-12-15 4.3.1.1 Better generic LDAP support
                     - description sync done in the following order: description, gecos, displayName
                     - memberOf is not always implemented, alternative method to sync users based on group names.
                     - disabled account synchronization using shadowExpire or sambaAcctFlags
                   Better Active Directory support
                     - accountExpires is now supported for synchronization
                     - ms-DS-User-Account-Control-Computed (to handle locked out accounts, available since Windows 2003)
2014-12-09 4.3.1.0 MULTIOTP_PATH environment variable support
                   CLI proxy added to speed up the command line
                   Scratch password need also the prefix PIN if it's activated
                   OTP with integrated serial numbers better supported (in PAP)
                   Generic LDAP support (instead of Microsoft AD support only)
                   Raspberry Pi edition has now a special proxy to speed up the command line
2014-11-04 4.3.0.0 It's now possible to use the AD/LDAP password instead of the PIN code
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
2014-06-12 4.2.4.3 Bug fix concerning aspsms provider
2014-04-13 4.2.4.2 XML parsing consolidation, one library for the whole project
                   Fixed bug concerning tokens CSV import
2014-04-06 4.2.4.1 Fixed bug concerning LDAP handling
                   NT_KEY support added (for FreeRADIUS further handling)
                   Tokens CSV import (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
                   When a user is deleted, the token(s) attributed to this user is/are unassigned
                   New option -user-info added
2014-03-30 4.2.4   Fixed bug concerning MySQL handling and mysqli support added
                   Enhanced SetAttributesToEncrypt function
                   New implementation fo some external classes
                   Generated QRcode are better
                   LOT of new QA tests, more than 60 different tests (including PHP class and command line versions)
                   Enhanced documentation
2014-03-13 4.2.3   Fixed bug for clear text password going back to TekRADIUS (PIN was always prefixed for mOTP)
                   Fixed bug when client/server mode is activated, but not working well
2014-03-03 4.2.2   Better AD/LDAP integration
                   Web GUI is now complete for a simple usage, including hardware tokens import
                   Better template for provisioning information
                   Some values can now go back to TekRADIUS
                   If activated, prefix PIN is now also requested for SMS authentication
                   More information in the logs
                   Better list of the external packages used
2014-02-14 4.2.1   AD/LDAP is now fully supported in order to create users based on AD/LDAP content
                    (with groups filtering)
2014-02-07 4.2.0   MS-CHAP and MS-CHAPv2 are now supported
                    (md4 implementation added for PHP backward compatibility)
                   Enhanced LDAP configuration structure
                   Fixed bug during token attribution to users
                    (a "no name" token appeared sometimes)
2014-01-20 4.1.1   md5.js was missing in the public distribution
                   Alternate json_encode function is defined if the JSON extension is not loaded
                   Fixed possible image functions incompatibilities with some PHP versions
                    during QRcode generation
                   As suggested by Sylvain, token resync doesn't need prefix PIN anymore
                    (but still accepted)
                   More verbosity in the logs in debug mode
                   Specific parameters order in QRCode for Microsoft Authenticator support
                    (thanks to Erik Nylund)
2013-12-23 4.1.0   The open source edition of multiOTP is OATH certified ;-)
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
2013-09-22 4.0.9   Fixed a bug in GetUserScratchPasswordsArray. If a user had no scratch password
                    and the implementation accepted blank password, it was accepted
                   Fixed a bug where scratch passwords generation used odd numbers of characters for hex2bin()
2013-08-30 4.0.7   GetScriptFolder() was still buggy sometimes, thanks Frank for the feedback
                   File mode of the created QRcode file is also changed based on GetLinuxFileMode()
                   'sms' as the password to request an SMS token can now be sent in lower or uppercase
                   Added a description attribute for the tokens
2013-08-25 4.0.6   base32_encode() is now RFC compliant with uppercases
                   GetUserTokenQrCode() and GetTokenQrCode() where buggy
                   GetScriptFolder() use now __FILE__ if the full path is included
                   When doing a check in the CLI header, @... is automatically removed from the
                    username if the user doesn't exist, and the check is done on the clean name
                   Added a lot of tests to enhance release quality
2013-08-21 4.0.5   Fixed the check of the cache lifetime
                   Added a temporary server blacklist during the same instances
                   Default server timeout is now set to 1 second
2013-08-20 4.0.4   Added an optional group attribute for the user
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
2011-10-25 3.9.2   Some quick fixes after intensive check
                   Improved get_script_dir() in CLI for Linux/Windows compatibility
2011-09-15 3.9.1   Some quick fixes concerning multiple users
2011-09-13 3.9.0   Added support for account with multiple users
2011-07-06 3.2.0   Encryption hash handling with additional error message 33
                    (if the key has changed)
                   Added more examples
                   Added generic user with multiple account
                    (Real account name is combined: "user" and "account password")
                   Added log options, now default doesn't log token value anymore
                   Debugging MySQL backend support for the token handling
                   Fixed automatic detection of \ or / for script path detection
2010-12-19 3.1.1   Better MySQL backend support, including in CLI version
2010-09-15 3.1.0   Removed bad extra spaces in the multiotp.php file for Linux
                   MySQL backend support
2010-09-02 3.0.0   Added tokens handling support
                    including importing XML tokens definition file
                    (http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00)
                   Enhanced flat database file format (multiotp is still compatible with old versions)
                   Internal method SetDataReadFlag renamed to SetUserDataReadFlag
                   Internal method GetDataReadFlag renamed to GetUserDataReadFlag
2010-08-21 2.0.4   Enhancement in order to use an alternate php "compiler" for Windows command line
                   Documentation enhancement
2010-08-18 2.0.3   Minor notice fix
2010-07-21 2.0.2   Fix to create correctly the folders "users" and "log" if needed
2010-07-19 2.0.1   Foreach was not working well in PHP4, replaced at some places
2010-07-19 2.0.0   New design using a class, mOTP support, cleaning of the code
2010-06-15 1.1.5   Added OATH/TOTP support
2010-06-15 1.1.4   Project renamed to multiotp to avoid overlapping
2010-06-08 1.1.3   Typo in script folder detection
2010-06-08 1.1.2   Typo in variable name
2010-06-08 1.1.1   Status bar during resynchronization
2010-06-08 1.1.0   Fix in the example, distribution not compressed
2010-06-07 1.0.0   Initial implementation
```

CONTENT OF THE PACKAGE
======================
In the credential-provider:
- the installer of multiOTP Credential Provider for Windows 7/8/8.1/10/2012(R2)/2016

In the linux folder:
- multiotp.php             : command line tool (merge of the header and the class, external files also included)
- multiotp.class.php       : the main file, it is the class itself, external files are already included
- multiotp.server.php      : the web service file (the class is already merged in the file, external files also included)
- check.multiotp.class.php : PHP script to validate some multiOTP functionalities
- md5.js                   : encryption JS library used by multiotp.server.php
- test-tokens.csv          : provisioning file of test tokens
+ oath subfolder           : contains provisioning files for oath test tokens
+ templates folder         : all templates files needed to generate the provisioning pages from the web GUI
```
*******************************************************************************
***  FOR THESE PHP FILES, THE BACKEND IS FILE BASED AND THE CONFIG AND      ***
***  BACKEND FOLDERS ARE RELATIVE AND JUST BELOW THE MAIN MULTIOTP FOLDER   ***
*******************************************************************************
```

In the raspberry folder:
- all necessary files to be able to create your own strong authentication device using a Raspberry Pi
```
*******************************************************************************
***  FOR THESE PHP FILES, THE BACKEND IS SET BY DEFAULT AS FILE BASED AND   ***
***  THE CONFIG AND BACKEND FOLDERS DEFINED TO BE LOCATED IN /etc/multiotp/ ***
***  FURTHERMORE, THIS VERSION USES THE ADVANCED WEB PROXY IMPLEMENTATION   ***
*******************************************************************************
*** !!! Be careful when you update your open source virtual appliance !!!   ***
*** The multiOTP open source Virtual Appliance is also using the            ***
*** files in raspberry/boot-part/multiotp-tree/usr/local/bin/multiotp,      ***
*** with config and backend folders defined to be located in /etc/multiotp/ ***
*******************************************************************************
```

In the sources folder:
- multiotp.class.php       : the main file, it is the class itself, which requires external files
- multiotp.cli.header.php  : header file to be merged with the class for a single file command line tool
- multiotp.server.php      : the web service file, which requires the class as external file
- check.multiotp.class.php : PHP script to validate some multiOTP functionalities
+ contrib subfolder        : contains all external files required by the multiotp.class.php file
```
*******************************************************************************
***  FOR THESE PHP FILES, THE BACKEND IS FILE BASED AND THE CONFIG AND      ***
***  BACKEND FOLDERS ARE RELATIVE AND JUST BELOW THE MAIN MULTIOTP FOLDER   ***
*******************************************************************************
```

In the windows folder:
- multiotp.exe             : command line tool for Windows (digitally signed) with embedded PHP 7.x
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
+ legacy subfolder         : contains a windows command line version with all needed files
                             (not embedded in a mini VM). This version is used by the multiOTP web service.
+ oath subfolder           : contains provisioning files for oath test tokens
+ radius subfolder         : all necessary files to be able to install a Windows radius server already
                             configured with multiOTP support (using FreeRADIUS implementation for Windows)
+ templates subfolder      : all templates files needed to generate the provisioning pages from the web GUI
+ tools subfolder          : command line tools needed by some cmd scripts
+ webservice subfolder     : all necessary files to be able to install a Windows multiOTP web service
                             (using Nginx as the light web server on port 8112,
                              or as a secured SSL connection (https) on port 8113)
```
*******************************************************************************
***  FOR THESE PHP FILES, THE BACKEND IS FILE BASED AND THE CONFIG AND      ***
***  BACKEND FOLDERS ARE RELATIVE AND JUST BELOW THE MAIN MULTIOTP FOLDER   ***
*******************************************************************************
```


TYPICAL TREE OF A WORKING LINUX INSTALLATION
============================================
A typical installation has the following tree organization:

**Software files**
```
/usr/local/bin/
/usr/local/bin/multiotp (contains multiotp.php)
/usr/local/bin/multiotp/templates
```

**Data files**
```
/etc/multiotp/
/etc/multiotp/config (contains multiotp.ini)
/etc/multiotp/devices
/etc/multiotp/groups
/etc/multiotp/tokens
/etc/multiotp/users (contains my_user1.db, my_user2.db, ...)
```


HOW CAN I CREATE MYSELF THE DIFFERENT VERSIONS ?
================================================
The multiotp.php file is a copy of the multiotp.cli.header.php including
the copy of all files that are included in the PHP code, which are
multiotp.class.php and the whole contrib subfolder content.

For the virtual appliance and the Raspberry Pi edition, the multiotp.php file
is a copy of the multiotp.cli.proxy.php file.
(the proxy version calls the multiotp.proxy.php using the web server,
and the web server has a PHP cache to improve the speed of the whole process).

For the virtual appliance and the Raspberry Pi edition, the multiotp.proxy.php file is
a copy of the multiotp.cli.header.php including the copy of all files that are included
in the PHP code, which are multiotp.class.php and the whole contrib subfolder content.

For the virtual appliance and the Raspberry Pi edition, the following line in multiotp.class.php
```
$multiotp = new Multiotp('DefaultCliEncryptionKey', $initialize_backend, $folder_path);
```
is replaced by
```
$multiotp = new Multiotp('DefaultCliEncryptionKey', $initialize_backend, $folder_path);
if (false !== strpos(getcwd(), '/')) {
  $multiotp->SetConfigFolder('/etc/multiotp/config/');
  $multiotp->SetCacheFolder('/tmp/cache/');
  $multiotp->SetDevicesFolder('/etc/multiotp/devices/');
  $multiotp->SetGroupsFolder('/etc/multiotp/groups/');
  $multiotp->SetTokensFolder('/etc/multiotp/tokens/');
  $multiotp->SetUsersFolder('/etc/multiotp/users/');
  $multiotp->SetLogFolder('/var/log/multiotp/');
  $multiotp->ReadConfigData();
}
$multiotp->SetLinuxFileMode('0666');
```

The multiotp.exe is a stub launcher running a PHP interpreter.
The necessary files of the PHP distribution are included in the php subfolder.  
PHP download : http://php.net/downloads.php

The source files of the Credential provider are available on GitHub and needs
the free Visual Studio Community to be compiled.  
Source files : https://github.com/multiOTP/multiOTPCredentialProvider  
Visual Studio Community : https://www.visualstudio.com/vs/community/


WHEN AND HOW CAN I USE THIS PACKAGE ? 
=====================================
If you decide to have strong two factor authentication inside your company,
this is definitely the package you need! You will be able to have strong
authentication for your VPN accesses, your SSL gateway, your private websites
and even your Windows login for desktops AND laptops!

The multiOTP class can be used alone (for example to have strong 
authentication for your PHP based web application), as a command line tool
(to handle users and have strong authentication using command line), as a web
service (to provide centralized authentication for a client/server installation)
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
open source multiOTPCredentialProvider which is based on MultiotpCPV2RDP from
arcadejust and MultiOneTimePassword Credential Provider from Last Squirrel IT.
It works with Windows 7/8/8.1/10/2012(R2)/2016 in both 32 and 64 bits.  
The Credential Provider does not need any RADIUS connection! It uses instead a
local version of multiOTP which can be configured as a client of a
centralized server (with caching support).
(http://download.multiotp.net/credential-provider/)

LSE Experts provides a commercial Radius Credential Provider which can talk
directly with a radius server.
(http://www.lsexperts.de)

When the backend is set, it's time to create/define the tokens. You will have
to select hardware or software token generators for your users. Currently, the
library supports mOTP, TOTP, HOTP, SMS or scratch passwords (printed on paper).

mOTP is a free implementation of strong tokens that asks a PIN to generate a
code. This code depends of the time and the PIN typed by the user.

A special without2FA token type is also available. It could be useful to do 2FA
only for some accounts and not for others.

The easiest tokens to use are TOTP, they are time based and well supported by
a lot of implementations like Google Authenticator.
Provisioning will be done simply by flashing a QRcode.

# Software tokens with mOTP (Mobile-OTP) support
  - Swiss SafeLab OTP authenticator (iOS and Android)
  - iPhone:    iOTP from PDTS (type iOTP in the Apple AppStore)
  - Android:   Mobile-OTP (http://motp.sf.net/Mobile-OTP.apk)
  - PalmOS:    Mobile-OTP (http://motp.sf.net/mobileotp_palm.zip)
  - Java J2ME (Nokia and other Java capable phones): MobileOTP
              (http://motp.sf.net/MobileOTP.jad)
  - WinPhone:  Token2 (https://token2.com/?content=mobileapp)
  
# Software tokens with OATH compliant HOTP or TOTP support
  Check the various markets of your devices, for examples:
  - FreeOTP (open source): https://freeotp.github.io/
  - oathtoken for iPhone/iPad: https://github.com/archiecobbs/oathtoken
  - androidtoken for Android: https://github.com/markmcavoy/androidtoken
  - Google Authenticator (Android/iPhone/iPad/BlackBerry)

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
webservice_install script. Nginx configuration file will be created,
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

Please check carefully the rights of the folders, as the multiOTP web service
has to write in the various subfolders.


CONFIGURING MULTIOTP WITH FREERADIUS 2.X UNDER LINUX
====================================================
Using the -request-nt-key option, NT_KEY: XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX can
now be displayed (like with the same option used with ntlm_auth).

1) Create a new module file called "multiotp" in etc/raddb/modules/ containing:  
```
    # Exec module instance for multiOTP (http://www.multiotp.net/).  
    # for Linux  : replace '/path/to/multiotp' with the actual path to the multiotp.php file, including the full file name.
    # for Windows: replace '/path/to' with the actual path to the multiotp.exe file (also with /), including the fulle file name.
    exec multiotp {  
        wait = yes  
        input_pairs = request  
        output_pairs = reply  
        program = "/path/to/multiotp '%{User-Name}' '%{User-Password}' -request-nt-key -src=%{Packet-Src-IP-Address} -chap-challenge=%{CHAP-Challenge} -chap-password=%{CHAP-Password} -ms-chap-challenge=%{MS-CHAP-Challenge} -ms-chap-response=%{MS-CHAP-Response} -ms-chap2-response=%{MS-CHAP2-Response}"  
        shell_escape = yes  
    }
```

2) In the configuration file called "default" in etc/raddb/sites-enabled/  
    a) Add the multiOTP handling  
```
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be added BEFORE the first "pap" entry found in the file.  
    multiotp  
```

    b) Add the multiOTP authentication handling  
```
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be added BEFORE the first "Auth-Type PAP" entry found in the file.  
    Auth-Type multiotp {  
        multiotp  
    }  
```

    c) Comment the first line containing only "chap"  
```
    #chap is now handled by multiOTP  
```

    d) Comment the first line containing only "mschap"  
```
    #mschap is now handled by multiOTP  
```

3) In the configuration file called "inner-tunnel" in etc/raddb/sites-enabled/  
    
    a) Add the multiOTP handling  
```
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be added BEFORE the first "pap" entry found in the file.  
    multiotp  
```

    b) Add the multiOTP authentication handling  
```
    #  
    # Handle multiOTP (http://www.multiotp.net/) authentication.  
    # This must be added BEFORE the first "Auth-Type PAP" entry found in the file.  
    Auth-Type multiotp {  
        multiotp  
    }  
```

    c) Comment the first line containing only "chap"  
```
    #chap is now handled by multiOTP  
```

    d) Comment the first line containing only "mschap"  
```
    #mschap is now handled by multiOTP  
```

4) In the configuration file called "policy.conf" in etc/raddb/  
   Add the multiOTP authorization policy  
```
    #  
    # Handle multiOTP (http://www.multiotp.net/) authorization policy.  
    # This must be added just before the last "}"  
    multiotp.authorize {  
        if (!control:Auth-Type) {  
            update control {  
                Auth-Type := multiotp  
            }  
        }  
    }  
```

5) In the configuration file called "radiusd.conf" in etc/raddb/  
   Depending which port(s) and/or ip address(es) you want to listen, change
   the corresponding ipaddr and port parameters  

6) In the configuration file called "clients.conf" in etc/raddb/  
   Add the clients IP, mask and secret that you want to authorize.  
```
    #  
    # Handle multiOTP (http://www.multiotp.net/) for some clients.  
    client 0.0.0.0 {  
        netmask = 0  
        secret = multiotpsecret  
    }  
```
   
7) Now, to see what's going on, you can:
   - stop the service : /etc/init.d/freeradius stop
   - launch the FreeRADIUS server in debug mode : /usr/sbin/freeradius -X
   - try to make some authentication requests

8) When you have checked that everything works well:
   - stop the debug mode (CTRL + C)
   - restart the service /etc/init.d/freeradius restart

Some values can go back to FreeRADIUS:

a) Set the right format options for FreeRADIUS:  
   multiotp -config radius-reply-attributor=" += " radius-reply-separator=","
   
b) Set multiOTP to send back to FreeRADIUS the group of the authenticated user:  
   multiotp -config group-attribute="Filter-Id"  


CONFIGURING MULTIOTP WITH FREERADIUS 3.X UNDER LINUX
====================================================
Source: https://wiki.freeradius.org/guide/multiOTP-HOWTO

multiOTP tokens will work with any type of PAP/CHAP/MS-CHAP/MS-CHAPv2 based authentication, including EAP-TTLS-PAP. With the correct OS/Supplicant software tokens can be used for 802.1X authentication and well as for standard PAP/CHAP/MS-CHAP/MS-CHAPv2 authentication (just make the changes described in the inner server). This guide closely follows the NTLM Auth with PAP HOWTO but with a little extra validation.

NT_KEY generation is also supported using the -request-nt-key option (like for ntlm_auth --request-nt-key option), which is needed in order to enable VPN (PPTP + MPPE) with MS-CHAP/MS-CHAPv2.

Before starting or asking for help
- Make sure the otp script is executable chmod +x /path/to/multiotp.php
- Verify multiotp is setup correctly by calling the script from the commandline with the appropriate arguments

1) Create 'raddb/modules/multiotp' and add the following, this will create a new instance of the exec module:
```
# Exec module instance for multiOTP
# Replace '/path/to' with the actual path to the multiotp.php file
exec multiotp {
        wait = yes
        input_pairs = request
        output_pairs = reply
        program = "/path/to/multiotp.php %{User-Name} %{User-Password} -request-nt-key -src=%{Packet-Src-IP-Address} -chap-challenge=%{CHAP-Challenge} -chap-password=%{CHAP-Password} -ms-chap-challenge=%{MS-CHAP-Challenge} -ms-chap-response=%{MS-CHAP-Response} -ms-chap2-response=%{MS-CHAP2-Response}"
        shell_escape = yes
}
```

2) Copy module/mschap to module/multiotpmschap. Change the following line in multiotpmschap:
```
"mschap {"
```
to
```
"mschap multiotpmschap {"
```

Also change ntlm_auth variable:
```
ntlm_auth = "/path/to/multiotp.php %{User-Name} %{User-Password}
-request-nt-key -src=%{Packet-Src-IP-Address}
-chap-challenge=%{CHAP-Challenge} -chap-password=%{CHAP-Password}
-ms-chap-challenge=%{MS-CHAP-Challenge}
-ms-chap-response=%{MS-CHAP-Response}
-ms-chap2-response=%{MS-CHAP2-Response}"
```

3) Edit 'raddb/policy.conf' and add the following to override the authorize method of the exec module: (or add a new multiotp file in the policy.d folder)
policy {
```
    # Change to a specific prefix if you want to deal with normal PAP authentication as well as OTP
    # e.g. "multiotp_prefix = 'otp:'"
    multiotp_prefix = ''
    multiotp.authorize {
        # This test force multiOTP for any MS-CHAP(v2) attempt
        if (control:Auth-Type == MS-CHAP) {
            update control {
                Auth-Type := multiotpmschap
            }
        }
        # This test force multiOTP for any MS-CHAP(v2) attempt
        elsif (control:Auth-Type == mschap) {
            update control {
                Auth-Type := multiotpmschap
            }
        }
        # This test force multiOTP for any CHAP attempt
        elsif (control:Auth-Type == chap) {
            update control {
                Auth-Type := multiotp
            }
        }
        # This test is for decimal OTP code only, otherwise you will have to change it
        #  elsif (!control:Auth-Type && User-Password =~ /^${policy.multiotp_prefix}([0-9]{10})$/) {
        #
        # Use this simple test for non decimal only OTP code: elsif (!control:Auth-Type) {
        #
        # This test force multiOTP for any other attempt like PAP
        elsif (!control:Auth-Type) {
            update control {
                Auth-Type := multiotp
            }
        }
    }
}
```

4) Edit your virtual server file, the default for the outer server is 'raddb/sites-available/default'

5) Add a call to multiotp before the pap module in authorize:
```
authorize {
    ...
    # Handle multiotp authentication
    multiotp

    # Handle other PAP authentication
    pap
    ...
}
```

6) Create the multiotp sub-section in authenticate:
```
authenticate {
    Auth-Type multiotp {
        multiotp
    }
    Auth-Type multiotpmschap {
        multiotpmschap
    }
}
```

7) Start the server up in debug mode radiusd -X and test authentication


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
    (users removed or deactivated in the AD/LDAP are deactivated in multiOTP)
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
    (users removed or deactivated in the AD/LDAP are deactivated in multiOTP)
    multiotp -debug -display-log -ldap-users-sync

DON'T FORGET TO SCHEDULE A SCRIPT THAT WILL DO THE USERS SYNCHRONIZATION REGULARY!


HOW TO CONFIGURE MULTIOTP TO USE THE CLIENT/SERVER FEATURE ?
============================================================
A) On the server
1) Install the multiOTP web service on the authentication server side. If you
   are using the unmodified included installer to install it under Windows,
   the URL for the multiOTP web service is http://ip.address.of.server:8112
   The web service script installer is called webservice_install.cmd.
2) Set the shared secret key you will use to encode the data between the
   server and the client: multiotp -config server-secret=MySharedSecret
   (this command line will change the configuration file config/multiotp.ini)
3) If you want to allow the client to cache the data on its side, set the
   options accordingly (enable the cache and define the lifetime of the cache):
   multiotp -config server-cache-level=1 server-cache-lifetime=15552000
   (this command line will change the configuration file config/multiotp.ini)
4) Create your users on the server using the CLI or the web GUI interface. If
   you are using the unmodified included installer to install it under Windows,
   the URL for the multiOTP web service is http://ip.address.of.server:8112

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
   If you want to connect with a commercial multiOTP appliance, the URL is
   https://ip.address.of.commercial.multiotp.server
5) Check your installation on the client by typing
   multiotp -display-log -log -debug "user" "token", where "user" is an
   existing user and "token" is the generated token for this user.
   If you have created a user with a prefix PIN, don't forget to type the prefix
   PIN before the displayed token.
   Example without a prefix PIN: multiotp test 457863
   Example with the "1234" prefix PIN: multiotp test 1234457863

   
HOW TO INSTALL A LOCAL ONLY STRONG AUTHENTICATION ON A WINDOWS MACHINE ?
========================================================================
1) Install multiOTPCredentialProvider, which contains also multiOTP inside.
   It works with Windows 7/8/8.1/10/2012(R2)/2016 in both 32 and 64 bits.
   (http://download.multiotp.net/credential-provider/)
2) During the installation, specify the folder on the client where the
   multiotp.exe file and folders must be installed and configured.
3) In the wizard, leave the URL of the multiOTP server(s) empty.
4) You can also choose to require a strong authentication only for RDP.
5) When you are on the test page, open a command prompt in the folder where
   multiOTP is now installed and create a new local user as explained above.
6) If the test is successful, the Credential Provider is installed.
7) To disable the Credential Provider, uninstall it from Windows,
   or execute multiOTPCredentialProvider-unregister.reg

HOW TO INSTALL A CENTRALIZED STRONG AUTHENTICATION SERVER
FOR STRONG AUTHENTICATION ON WINDOWS DESKTOPS OR RDP ?
=========================================================
1) Install a client/server multiOTP environment like explained above.
2) On each client, install multiOTPCredentialProvider .
   It works with Windows 7/8/8.1/10/2012(R2)/2016 in both 32 and 64 bits.
   (http://download.multiotp.net/credential-provider/)
3) During the installation, specify the folder on the client where the
   multiotp.exe file and folders must be installed and configured.
4) In the wizard, type the URL of the multiOTP server(s).
5) You can also choose to require a strong authentication only for RDP.
6) On the test page, test your account to be sure that everything works.
7) If the test is successful, the Credential Provider is installed.
8) To disable the Credential Provider, uninstall it from Windows,
   or execute multiOTPCredentialProvider-unregister.reg


HOW TO BUILD A RASPBERRY PI STRONG AUTHENTICATION SERVER ?
==========================================================
0) If you want to download a multiOTP Raspberry Pi image ready to use, follow this URL:  
   http://download.multiotp.net/raspberry/
   
```
   Nano-computer name: multiotp
   IP address: 192.168.1.44 (netmask: 255.255.255.0, default gateway: 192.168.1.1)
   Username: pi
   Password: raspberry
```
   
   You can now flash the SD (check point 3) and 4) if needed), put the SD Card
   into the Raspberry Pi and boot it. You can go directly to point 15)
   
1) If you want to use a battery backed up Real Time Clock, install it now in your
   Raspberry Pi, the drivers for these models are included in the package:
   - http://afterthoughtsoftware.com/products/rasclock
   - http://www.cjemicros.co.uk/micros/products/rpirtc.shtml
   - http://www.robotshop.com/ca/en/mini-real-time-clock-rtc-module.html
   - http://nicegear.co.nz/raspberry-pi/high-precision-real-time-clock-for-raspberry-pi/
   
2) Download the last image of Raspbian to be flashed  
   http://downloads.raspberrypi.org/raspbian_latest (currently 2017-09-07-raspbian-stretch.zip)

3) Format your SD Card using the SD Card Association's formatting tool  
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


OPENSSL OPTIONS FOR LDAPS
=========================
You can define how the certificate bundle is handled with the ldaptls_reqcert option.
You can define a custom cipher suite with the ldaptls_cipher_suite option.


COMPATIBLE CLIENTS APPLICATIONS AND DEVICES
===========================================
Open source multiOTPCredentialProvider, based on MultiotpCPV2RDP and mOTP-CP
If you want to have strong authentication on Windows logon, have a look at the
open source multiOTPCredentialProvider.  
It works with Windows 7/8/8.1/10/2012(R2)/2016 in both 32 and 64 bits.  
The Credential Provider is using directly a local version of multiOTP which
can be configured as a client of a centralized multiOTP server (with caching support)
(https://github.com/multiOTP/multiOTPCredentialProvider)

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
```
    barcode (MIT License)
    Kreative Software
    https://github.com/kreativekorp/barcode

    CryptoJS (BSD New)
    This product contains software provided by Jeff Mott
    https://code.google.com/p/crypto-js/

    FreeRADIUS / WinRADIUS for Windows (GPLv2)
    This product contains software provided by FreeRADIUS team and its contributors.
    http://freeradius.org/ - http://winradius.eu/

    md5 JavaScript 2010 algorithm (BSD)
    Joseph Myers, Paul Johnston, Greg Holt, Will Bond
    http://www.myersdaily.org/joseph/javascript/md5-text.html

    multiOTPCredentialProvider, based on MultiotpCPV2RDP (Apache License)
    Credential Provider (32 and 64 bits) supporting Windows 7/8/8.1/10/2012(R2)/2016
    SysCo / ArcadeJust / LastSquirrelIT 
    https://github.com/multiOTP/multiOTPCredentialProvider

    Nginx (BSD)
    This product contains software provided by Nginx, Inc. and its contributors.
    http://nginx.org/
    
    nssm service helper (public domain)
    http://nssm.cc/

    NuSOAP - PHP Web Services Toolkit (LGPLv2.1)
    NuSphere Corporation
    http://sourceforge.net/projects/nusoap/

    PHP (PHP License)
    Voluntary contributions made by many individuals on behalf of the PHP Group.    
    http://www.php.net/
    
    phpseclib (MIT License)
    MMVI Jim Wigginton
    http://phpseclib.sourceforge.net/

    PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY (LGPLv2.1)
    Scott Barnett - enhanced by SysCo
    http://adldap.sourceforge.net/

    PHP radius class (LGPLv3)
    Andre Liechti
    http://developer.sysco.ch/php/

    PHP Syslog class (FREE "AS IS")
    Andre Liechti
    http://developer.sysco.ch/php/

    status_bar.php (2010) (FREE "AS IS")
    dealnews.com, Inc.
    http://brian.moonspot.net/status_bar.php.txt or http://snipplr.com/view/29548/

    TCPDF (LGPLv3)
    Nicola Asuni
    http://www.tcpdf.org/

    XML Parser Class (LGPLv3)
    Adam A. Flynn - enhanced by SysCo
    http://www.criticaldevelopment.net/xml/

    XPertMailer package (LGPLv2.1)
    Tanase Laurentiu Iulian
    http://xpertmailer.sourceforge.net/

    The source files can be downloaded at http://download.multiotp.net/multiotp.zip
```

 
MULTIOTP PHP CLASS DOCUMENTATION
================================
Have a look into the source code if you want to know how to use it,
and you may also check multiotp.cli.header.php which implements the class.


MULTIOTP COMMAND LINE TOOL
==========================

``` 
multiOTP 5.6.1.5 (2019-10-23)
(c) 2010-2019 SysCo systemes de communication sa
http://www.multiOTP.net   (you can try the [Donate] button ;-)

multiotp will check if the token of a user is correct, based on a specified
algorithm (currently Mobile-OTP (http://motp.sf.net), OATH/HOTP (RFC 4226) 
and OATH/TOTP (RFC 6238) are implemented). PSKC format supported (RFC 6030).
Supported encryption methods are PAP and CHAP.
Yubico OTP format supported (44 bytes long, with prefixed serial number).
SMS-code are supported (current providers: aspsms,clickatell,clickatell2,
                        intellisms,nexmo,nowsms,smseagle,swisscom,custom,exec).
Specific SMS sender program supported by specifying exec as SMS provider.

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
 9 INFO: Access Challenge returned back to the client 
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
20 ERROR: User blacklisted 
21 ERROR: User doesn't exist 
22 ERROR: User already exists 
23 ERROR: Invalid algorithm 
24 ERROR: User locked (too many tries) 
25 ERROR: User delayed (too many tries, but still a hope in a few minutes) 
26 ERROR: This token has already been used 
27 ERROR: Resynchronization of the token has failed 
28 ERROR: Unable to write the changes in the file 
29 ERROR: Token doesn't exist 
30 ERROR: At least one parameter is missing 
31 ERROR: Tokens definition file doesn't exist 
32 ERROR: Tokens definition file not successfully imported 
33 ERROR: Encryption hash error, encryption key is not matching 
34 ERROR: Linked user doesn't exist 
35 ERROR: User not created 
36 ERROR: Token doesn't exist 
37 ERROR: Token already attributed 
38 ERROR: User is desactivated 
39 ERROR: Requested operation aborted 
40 ERROR: SQL query error 
41 ERROR: SQL error 
42 ERROR: They key is not in the table schema 
43 ERROR: SQL entry cannot be updated 
50 ERROR: QRcode not created 
51 ERROR: UrlLink not created (no provisionable client for this protocol) 
58 ERROR: File is missing 
59 ERROR: Bad restore configuration password 
60 ERROR: No information on where to send SMS code 
61 ERROR: SMS code request received, but an error occurred during transmission 
62 ERROR: SMS provider not supported 
63 ERROR: This SMS code has expired 
64 ERROR: Cannot resent an SMS code right now 
69 ERROR: Failed to send email 
70 ERROR: Server authentication error 
71 ERROR: Server request is not correctly formatted 
72 ERROR: Server answer is not correctly formatted 
79 ERROR: AD/LDAP connection error 
80 ERROR: Server cache error 
81 ERROR: Cache too old for this user, account autolocked 
82 ERROR: User not allowed for this device 
88 ERROR: Device is not defined as a HA slave 
89 ERROR: Device is not defined as a HA master 
93 ERROR: Authentication failed (time based token probably out of sync) 
94 ERROR: API request error 
95 ERROR: API authentication failed 
96 ERROR: Authentication failed (CRC error) 
97 ERROR: Authentication failed (wrong private id) 
98 ERROR: Authentication failed (wrong token length) 
99 ERROR: Authentication failed (and other possible unknown errors) 


Usage:

 PLEASE NOT THAT BY DEFAULT, A PREFIX PIN IS REQUIRED.

 multiotp user [prefix PIN]OTP (check the OTP (with prefix PIN) of the user)
 multiotp -checkpam (to check with pam-script, using PAM_USER and PAM_AUTHTOK)

 multiotp -requiresms user (generate and send an SMS token to the user)
 multiotp user sms (send an SMS token to the user)

 multiotp user [-chap-id=0x..] -chap-challenge=0x... -chap-password=0x...
   (the first byte of the chap-password value can contain the chap-id value)

 multiotp -fastcreate user [pin] (create a Google Auth compatible token)
 multiotp -fastcreatenopin user [pin] (create a user without a prefix PIN)
 multiotp -fastecreatewithpin user [pin] (create a user with a prefix PIN)
 multiotp -createga user base32_seed [pin] (create Google Auth user with TOTP)
 multiotp -create user algo seed pin digits [pos|interval]
 multiotp -create -token-id user token-id pin

  token-id: id of the previously imported token to attribute to the user
      user: name of the user (should be the account name)
      algo: available algorithms are mOTP, HOTP, TOTP, YubicoOTP and without2FA
      seed: hexadecimal or base32 seed of the token
       pin: private pin code of the user
    digits: number of digits given by the token
       pos: for HOTP algorithm, position of the next awaited event
  interval: for mOTP and TOTP algorithms, token interval time in seconds

 multiotp -import tokens_definition_file [key|pass|key_file]
   (auto-detect format)
 multiotp -import-csv csv_tokens_file.csv (tokens definition in a file)
   (serial_number;manufacturer;algorithm;seed;digits;interval_or_event)
 multiotp -import-pskc pskc_tokens_file.pskc [key|pass|key_file]
   (PSKC format, RFC 6030)
 multiotp -import-yubikey yubikey_traditional_format_log.csv (YubiKey)
 multiotp -import-dat importAlpine.dat (SafeWord/Aladdin/SafeNet tokens)
 multiotp -import-alpine-xml alpineXml.xml (SafeWord/Aladdin/SafeNet)
 multiotp -import-xml xml_tokens_definition_file.xml (old Feitian)
 multiotp -import-sql tokens_definition_file.sql (ZyXEL/Authenex)

 multiotp -delete-token token

 multiotp -qrcode user png_file_name.png (only for TOTP and HOTP)
 multiotp -urllink user (only for TOTP and HOTP, generate provisioning URL)

 multiotp -scratchlist user (generate & display scratch passwords for the user)

 multiotp -resync [-status] user token1 token2 (two consecutive tokens)
 multiotp -update-pin user pin

 multiotp -assign-token user token-id (assign the token to the user)
 multiotp -remove-token user (remove the token assigned to the user)

 multiotp -default-dialin-ip-mask (set the default dialin IP mask)
 multiotp -dialin-ip-address user ip-address (set the user dialin IP address)
 multiotp -dialin-ip-mask user ip-address (set the user dialin IP mask)

 multiotp -[des]activate user
 multiotp -[un]lock user

 multiotp -delete user

 multiotp -user-info user

 multiotp -config option1=value1 option2=value2 ... optionN=valueN
  options are    autoresync: [0|1] enable/disable autoresync during login
      attributes-to-encrypt: specific attributes list to encrypt, must be
                             surrounded by *, like '*token_seed*user_pin*'
               backend-type: backend storage type (files|mysql|pgsql)
 challenge-response-enabled: [0|1] enable/disable Challenge-Response
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
     ldap-default-algorithm: [totp|hotp|motp|without2fa] default algorithm
                             for new LDAP/AD users
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
 ldap-synced-user-attribute: LDAP/AD attribute used as the account name
            ldap-time-limit: LDAP/AD number of sec. to wait for search results
              ldap-users-dn: LDAP/AD users DN (optional, use base-dn if empty)
                             (you can put several DN separated by semicolons)
            ldaptls_reqcert: ['auto'|'never'|''|...] how to perform the LDAP TLS
                             server certificate checks (LDAPTLS_REQCERT)
                             'auto' means 'never' for Windows and '' for Linux
       ldaptls_cipher_suite: ['auto'|''|...] which cipher suite is used for the
                             LDAP TLS connection (LDAPTLS_CIPHER_SUITE)
                             'auto' means '' for PHP higher than 5.x and
                             'NORMAL:!VERS-TLS1.2' for PHP 5.x and before
                        log: [0|1] enable/disable log permanently
            multiple-groups: [0|1] enable/disable multiple groups per user
    radius-reply-attributor: [ += |=] how to attribute a value
                             ('=' for TekRADIUS, ' += ' for FreeRADIUS)
     radius-reply-separator: [,|:|;|cr|crlf] returned attributes separator
                             ('crlf' for TekRADIUS, ',' for FreeRADIUS)
          self-registration: [1|0] enable/disable self-registration of tokens
         server-cache-level: [1|0] enable/allow cache from server to client
      server-cache-lifetime: lifetime in seconds of the cached information
              server-secret: shared secret used for client/server operation
             server-timeout: timeout value for the connection to the server
                server-type: [xml] type of the server
                             (only xml server type is able to do caching)
                 server-url: full url of the server(s) for client/server mode
                             (server_url_1;server_url_2 is accepted)
                 sms-api-id: SMS API id (if any, give your REST/XML API id)
                             with exec as provider, define the script to call
                               (available variables: %from, %to, %msg)
                     sms-ip: IP address of the SMS server (for inhouse server)
      sms-challenge-enabled: [0|1] enable/disable SMS challenge
                sms-message: SMS message to display before the OTP
             sms-originator: SMS sender (if authorized by provider)
               sms-password: SMS account password
                   sms-port: Port of the SMS server (for inhouse server)
               sms-provider: SMS provider (aspsms,clickatell,clickatell2,
                             intellisms,nexmo,nowsms,smseagle,swisscom,custom,
                             exec)
                sms-userkey: SMS account username or userkey

Custom SMS provider only
                    sms-url: URL(s) of the custom SMS provider
                               (multiple URLs can be separated by [space],
                                supported variables : %api_id,%username,
                                %password,%from,%to,%msg,%ip,%url)
          sms-send-template: POST template content for custom SMS provider
                               (supported variables : %api_id,%username,
                                %password,%from,%to,%msg)
                 sms-method: [GET|POST|POST-JSON|POST-XML] send method
               sms-encoding: [ISO|UTF] characters encoding
         sms-status-success: status result if successful (partial supported)
                               (example: 20, for any 20x result)
        sms-content-success: content result if successful (partial supported)
                               (example: "status": "0")
       sms-content-encoding: [''|'HTML'|'URL'|'QUOTES'] Special content encoding
         sms-no-double-zero: [0|1] Remove double zero for international numbers
             sms-basic-auth: [0|1] Enable basic HTTP authentication
                               (sms-userkey:sms-password)

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
         text-sms-challenge: Text displayed for the SMS challenge
       text-token-challenge: Text displayed for the challenge
 token-serial-number-length: Length of the serial number of the tokens
                             (used for self-registration)

 multiotp -initialize-backend (when all options are set, it will initialize
                               the backend, including creating the tables)

 multiotp -set user option1=value1 option2=value2 ... optionN=valueN
  options are  email: update the email of the user
         cache-level: [1|0] enable/allow cache for this user on the client
      cache-lifetime: set/update lifetime in seconds of cached information
         description: set a description to the user, used for example during
                      the QRcode generation as the description of the account
               group: set/update the group of the user
            ldap-pwd: [0|1] the LDAP/AD password is used instead of the pin
                 pin: set/update the private pin code of the user
          prefix-pin: [0|1] the pin and the token must by merged by the user
                      (if your pin is 1234 and your token displays 5556677,
                      you will have to type 1234556677)
                 sms: set/update the sms phone number of the user


Authentication parameters:

 -calling-ip=Framed-IP-Address
 -calling-mac=Calling-Station-Id
 -chap-challenge=0x... CHAP-Challenge
 -chap-id=0x... Optional CHAP-Id
          (the first byte of the chap-password value should contain this value)
 -chap-password=0x... CHAP-Password
 -mac=Called-Station-Id
 -ms-chap-challenge=0x... MS-CHAP-Challenge
 -ms-chap-response=0x... MS-CHAP-Response
 -ms-chap2-response=0x... MS-CHAP2-Response
 -src=Packet-Src-IP-Address
 -state=State
 -tag=Client-Shortname


Client/server inline parameters:

 -server-cache-level=[1|0] enable/allow cache from server to client
 -server-secret=shared secret used for client/server operation
 -server-timeout=timeout value for the connection to the server
 -server-url=full url of the server(s) for client/server mode
             (-server-url=server_url_1;server_url_2 is accepted)


AD/LDAP integration:

 multiotp -ldap-check          : check the AD/LDAP connection
 multiotp -ldap-user-info user : print the AD/LDAP information for this user
 multiotp -ldap-users-list     : print the list of selected the AD/LDAP users
 multiotp -ldap-users-sync     : launch the AD/LDAP synchronization
                                 (will check first if a lock file is present)


Backup/restore commands:

 multiotp -backup-config  password [file-name]
 multiotp -restore-config password [file-name]
   By default, the file name is multiotp.cfg in the current folder.


Other information commands:

 multiotp -phpinfo         : print the current PHP version
 multiotp -showlog         : print the log file
 multiotp -tokenslist      : print the list of the tokens
 multiotp -userslist       : print the list of the users
 multiotp -lockeduserslist : print the list of the locked users


Special commands: 

 multiotp -purge-lock-folder
   This will delete the .lock files in the lock folder.
   .lock files are used to handle multiple instances.
   They are valid by default for 5 minutes.

 multiotp -purge-ldap-cache-folder
   This will delete the .cache files in the AD/LDAP cache folder.
   .cache files are used to speed up the AD/LDAP synchronizsation process.
   They are valid by default for 60 minutes.


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
 -network-info   Display network info (mode, ip, mask, gateway, dns1, dns2)
 -param          All parameters are logged for debugging purposes
 -php-version    Display the current version of the running PHP interpreter
 -request-nt-key This will return the NT_KEY to the radius server
 -status         Display a status bar during resynchronization
 -version        Display the current version of the library


Examples:

 multiotp -fastcreate gademo
 multiotp -debug -createga gauser 2233445566777733
 multiotp -debug -create alan TOTP 3683453456769abc3452 2233 6 60
 multiotp -debug -set alan prefix-pin=1
 multiotp -debug -create anna TOTP 56821bac24fbd2343393 4455 6 30
 multiotp -debug -set anna prefix-pin=0
 multiotp -debug -create john HOTP 31323334353637383930 5678 6 137
 multiotp -debug -create -token-id rick 2010090201901 2345
 multiotp -log -create jimmy mOTP 004f5a158bca13984d349a7f23 1234 6 10

 multiotp -set gademo description="VPN code for gademo"
 multiotp -set jimmy sms=41791234567

 multiotp jimmy sms

 multiotp -scratchlist gademo

 multiotp -display-log -log -debug jimmy ea2315
 multiotp -display-log -log anna 546078
 multiotp -display-log -log -checkpam
 multiotp john 5678124578

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

 multiotp -config backend-type=pgsql sql-server=fqdn.or.ip sql-database=dbname
 multiotp -config sql-schema=schemaname sql-username=user sql-password=pass
 multiotp -initialize-backend


multiOTP can be combined with a Raspberry Pi (http://www.raspberrypi.org/) in
order to have a very low budget strong authentication device. Please look at
the readme file in order to learn how to set it up in a few steps.
The distribution is already optimized with an HTTP proxy to speed up the CLI.
A ready to use binary image can be downloaded at http://download.multiotp.net/

multiOTP open source is also available as a ready to use virtual appliance in
standard OVA, VMware optimized or Hyper-V formats.
Virtual appliance images can be downloaded at http://download.multiotp.net/

multiOTP web service is working fine with any web server supporting PHP.
 - nginx is a light one under Linux and Windows (http://nginx.org/)
 - Mongoose is a light one under Windows (http://code.google.com/p/mongoose/)
 - and many others like Apache HTTP Server (http://httpd.apache.org/)

multiOTP is working fine with FreeRADIUS under Linux (http://freeradius.org/)

multiOTP is working fine under Windows with WinRADIUS, a port of FreeRADIUS
(http://winradius.eu/)

When used with TekRADIUS (http://www.tekradius.com) the External-Executable
must be called like this: C:\multiotp\multiotp.exe %ietf|1% %ietf|2%
Check the readme file for more information


Some of other products and services based on multiOTP:
 multiOTP Credential Provider (https://download.multiotp.net/)
  Open-source Credential Provider for Windows Logon, based on MultiotpCPV2RDP
 MultiotpCPV2RDP (https://github.com/arcadejust/MultiotpCPV2RDP)
  Open-source Credential Provider for Windows Logon, by arcadejust
 mOTP-CP (https://goo.gl/Y8g4ON)
  Open-source Credential Provider for Windows Logon, by Last Squirrel IT
 ownCloud OTP (https://goo.gl/mKjt43)
  Open-source One Time Password app for ownCloud (http://owncloud.org)
 UserCredential (https://github.com/cymapgt/UserCredential)
  Open-source authentication PHP library by Cyril Ogana
 multiOTP Pro 501V (https://www.multiOTP.com)
  Pro version virtual appliance, with full web GUI, 1 free user licence
 multiOTP Pro 420B (https://www.multiOTP.com)
  Pro version tiny hardware device (BeagleBone Black), with full web GUI
 multiOTP Enterprise (http:s//firmware.multiotp.com/enterprise/)
  Enterprise version virtual appliance, with HA master-slave support,
   also available as a Raspberry Pi image file
 secuPASS.net (https://www.secuPASS.net)
  simple SMS trusting service for free WLAN Hotspot

Don't hesitate to send us an email if your product uses our multiOTP library.

Visit https://forum.multiotp.net/ for additional support


``` 
 
``` 
Hash verification for multiotp_5.6.1.5.zip 
SHA256:9087aab3ac87a8eb31abea435b23108aaa436022e6329011d10a11c2659f8971 
SHA1:6993c5c93bf39e73b9ae529c43fb6b3156c300ec 
MD5:cf9e48e8ff30844a220f8c258440e404 
``` 
