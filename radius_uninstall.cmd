@ECHO OFF
REM ************************************************************
REM @file  radius_uninstall.cmd
REM @brief Script to uninstall the radius service.
REM
REM multiOTP - Strong two-factor authentication PHP class package
REM http://www.multiotp.net
REM 
REM Windows batch file for Windows 2K/XP/2003/7/2008/8/2012/10
REM
REM @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
REM @version   5.1.1.2
REM @date      2018-03-20
REM @since     2013-08-20
REM @copyright (c) 2013-2018 SysCo systemes de communication sa
REM @copyright GNU Lesser General Public License
REM
REM
REM Description
REM
REM   radius_uninstall is a small script that will uninstall
REM   the radius server of multiOTP under Windows using freeradius.
REM   (http://sourceforge.net/projects/freeradius/)
REM
REM
REM Usage
REM  
REM   The script must be launched in the top folder of multiOTP.
REM
REM
REM Licence
REM
REM   Copyright (c) 2013-2018 SysCo systemes de communication sa
REM   SysCo (tm) is a trademark of SysCo systemes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM
REM Change Log
REM
REM   2017-05-29 5.0.4.5 SysCo/al Unified script with some bug fixes
REM   2016-11-04 5.0.2.7 SysCo/al Unified file header
REM   2016-10-16 5.0.2.5 SysCo/al Version synchronisation
REM   2015-07-15 4.3.2.5 SysCo/al Version synchronisation
REM   2014-03-27 4.2.4   SysCo/al More generic usage
REM   2013-08-23 4.0.6   SysCo/al Enhanced options
REM   2013-08-21 4.0.5   SysCo/al Service name can be given as a parameter
REM   2013-08-20 4.0.4   SysCo/al Initial release
REM
REM ************************************************************

SET _service_tag=multiOTPradius

IF NOT "%1"=="" SET _service_tag=%1

IF "%_service_tag%"=="multiOTPradiusTest" GOTO NoWarning
ECHO WARNING! Please run this script as an administrator, otherwise it will fail.
PAUSE
:NoWarning

SET _folder=%~d0%~p0
SET _radius_folder=%~d0%~p0
IF NOT EXIST %_radius_folder%radius SET _radius_folder=%~d0%~p0..\

netsh firewall delete allowedprogram "%_radius_folder%radius\sbin\radiusd.exe" >NUL
netsh advfirewall firewall delete rule name="multiOTP Radius server" >NUL

SC stop %_service_tag% >NUL
SC delete %_service_tag% >NUL

SET _folder=
SET _radius_folder=
