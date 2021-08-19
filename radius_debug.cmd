@ECHO OFF
REM ************************************************************
REM @file  radius_debug.cmd
REM @brief Script to launch the debug version of the radius service.
REM
REM multiOTP - Strong two-factor authentication PHP class package
REM https://www\.multiOTP.net
REM 
REM Windows batch file for Windows 2K/XP/2003/7/2008/8/2012/10
REM
REM @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
REM @version   5.8.2.9
REM @date      2021-08-19
REM @since     2014-04-22
REM @copyright (c) 2014-2021 SysCo systemes de communication sa
REM @copyright GNU Lesser General Public License
REM
REM
REM Description
REM
REM   radius_debug is a small script that will launch the debug version
REM   of the radius server of multiOTP under Windows using freeRADIUS.
REM   (http://sourceforge.net/projects/freeradius/)
REM
REM
REM Usage
REM  
REM   The script must be launched in the top folder of multiOTP.
REM   Default ports are 1812 and 1813
REM
REM
REM Licence
REM
REM   Copyright (c) 2014-2021 SysCo systemes de communication sa
REM   SysCo (tm) is a trademark of SysCo systemes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM
REM Users feedbacks and comments
REM
REM
REM Change Log
REM
REM   2020-12-11 5.8.0.6 SysCo/al Do an automatic "Run as administrator" if needed
REM   2016-11-04 5.0.2.7 SysCo/al Unified file header
REM   2014-04-22 4.2.4.3 SysCo/al Initial release
REM
REM ************************************************************

NET SESSION >NUL 2>&1
IF NOT %ERRORLEVEL% == 0 (
    ECHO WARNING! Please run this script as an administrator, otherwise it will fail.
    ECHO Elevating privileges...
    REM PING 127.0.0.1 > NUL 2>&1
    CD /d %~dp0
    MSHTA "javascript: var shell = new ActiveXObject('shell.application'); shell.ShellExecute('%~nx0', '', '', 'runas', 1);close();"
    EXIT
    REM PAUSE
    REM EXIT /B 1
)
:NoWarning

SET _radius_secret=multiotpsecret

REM Ports variables are not overwritten if already defined
IF "%_auth_port%"=="" SET _auth_port=1812
IF "%_account_port%"=="" SET _account_port=1813

REM Define the service tag and the service name
SET _service_tag=multiOTPradius
SET _service_name=multiOTP Radius server

REM Ports and service information can be overwritten if passing parameters
IF NOT "%1"=="" SET _auth_port=%1
IF NOT "%2"=="" SET _account_port=%2
IF NOT "%3"=="" SET _service_tag=%3
IF NOT "%4"=="" SET _service_name=%4
IF NOT "%5"=="" SET _service_name=%_service_name% %5
IF NOT "%6"=="" SET _service_name=%_service_name% %6
IF NOT "%7"=="" SET _service_name=%_service_name% %7
IF NOT "%8"=="" SET _service_name=%_service_name% %8
IF NOT "%9"=="" SET _service_name=%_service_name% %9


REM Define the current folder
SET _folder=%~d0%~p0
SET _radius_folder=%~d0%~p0
SET _tools_folder=%~d0%~p0
IF NOT EXIST %_radius_folder%radius SET _radius_folder=%~d0%~p0..\
IF NOT EXIST %_tools_folder%tools SET _tools_folder=%~d0%~p0..\

REM Create the multiotp module for the radius server
ECHO # Exec module instance for multiOTP (https://www\.multiOTP.net/).>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO exec multiotp {>>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO         wait = yes>>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO         input_pairs = request>>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO         output_pairs = reply>>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO         program = "../../multiotp.exe -base-dir=%_folder% -keep-local -log -debug **"%%{User-Name}**" **"%%{User-Password}**" -src=%%{Packet-Src-IP-Address} -chap-challenge=%%{CHAP-Challenge} -chap-password=%%{CHAP-Password} -ms-chap-challenge=%%{MS-CHAP-Challenge} -ms-chap-response=%%{MS-CHAP-Response} -ms-chap2-response=%%{MS-CHAP2-Response}">>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO         shell_escape = yes>>%_radius_folder%radius\etc\raddb\modules\multiotp
ECHO }>>%_radius_folder%radius\etc\raddb\modules\multiotp

REM Sorry, this is an *ugly* trick to change "\" to "/" with the FART tool
%_tools_folder%tools\FART "%_radius_folder%radius\etc\raddb\modules\multiotp" "\\" "!!!/!!!" >NUL
%_tools_folder%tools\FART --remove "%_radius_folder%radius\etc\raddb\modules\multiotp" "!!!" >NUL
%_tools_folder%tools\FART "%_radius_folder%radius\etc\raddb\modules\multiotp" "**" "\\" >NUL

REM Customize the etc/raddb/radiusd.conf configuration file
COPY "%_radius_folder%radius\etc\raddb\radiusd.template.conf" "%_radius_folder%radius\etc\raddb\radiusd.conf" /Y >NUL
%_tools_folder%tools\FART "%_radius_folder%radius\etc\raddb\radiusd.conf" "_auth_port" "%_auth_port%" >NUL
%_tools_folder%tools\FART "%_radius_folder%radius\etc\raddb\radiusd.conf" "_account_port" "%_account_port%" >NUL

REM Customize the etc/raddb/clients.conf configuration file
COPY "%_radius_folder%radius\etc\raddb\clients.template.conf" "%_radius_folder%radius\etc\raddb\clients.conf" /Y >NUL
%_tools_folder%tools\FART "%_radius_folder%radius\etc\raddb\clients.conf" "_radius_secret" "%_radius_secret%" >NUL

REM Basic firewall rules for the radius server
netsh firewall delete allowedprogram "%_radius_folder%radius\sbin\radiusd.exe" >NUL
netsh firewall add allowedprogram "%_radius_folder%radius\sbin\radiusd.exe" "%_service_name%" ENABLE >NUL

REM Enhanced firewall rules for the service
netsh advfirewall firewall delete rule name="%_service_name%" >NUL
netsh advfirewall firewall add rule name="%_service_name%" dir=in action=allow program="%_radius_folder%radius\sbin\radiusd.exe" enable=yes >NUL

CD %_radius_folder%radius\sbin
%_radius_folder%radius\sbin\radiusd.exe -X -d %_radius_folder%radius\etc\raddb

REM Clean the environment variables
SET _account_port=
SET _auth_port=
SET _folder=
SET _radius_folder=
SET _tools_folder=
SET _radius_secret=
SET _service_name=
SET _service_tag=
