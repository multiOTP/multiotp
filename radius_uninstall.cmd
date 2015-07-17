@ECHO OFF
REM ************************************************************
REM
REM multiOTP - Strong two-factor authentication radius server
REM http://www.multiotp.net
REM
REM      Filename: radius_uninstall.cmd
REM       Version: 4.3.2.5
REM      Language: Windows batch file for Windows 2K/XP/2003/7/2008/8/2012
REM     Copyright: SysCo systèmes de communication sa
REM Last modified: 2015-07-15 SysCo/al
REM       Created: 2013-08-20 SysCo/al
REM      Web site: http://developer.sysco.ch/multiotp/
REM         Email: developer@sysco.ch
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
REM   Copyright (c) 2010-2015 SysCo systemes de communication sa
REM   SysCo (tm) is a trademark of SysCo systèmes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM
REM Change Log
REM
REM   2015-07-15 4.3.2.5 SysCo/al Version synchronisation
REM   2014-03-27 4.2.4   SysCo/al More generic usage
REM   2013-08-23 4.0.6   SysCo/al Enhanced options
REM   2013-08-21 4.0.5   SysCo/al Service name can be given as a parameter
REM   2013-08-20 4.0.4   SysCo/al Initial release
REM
REM ************************************************************

SET _service_tag=multiOTPradius

IF NOT "%1"=="" SET _service_tag=%1

SET _folder=%~d0%~p0
SET _radius_folder=%~d0%~p0
IF NOT EXIST %_radius_folder%radius SET _radius_folder=%~d0%~p0..\

netsh firewall delete allowedprogram "%_radius_folder%radius\sbin\radiusd.exe" >NUL
netsh advfirewall firewall delete rule name="multiOTP Radius server" >NUL

SC stop %_service_tag% >NUL
SC delete %_service_tag% >NUL

SET _folder=
SET _radius_folder=
