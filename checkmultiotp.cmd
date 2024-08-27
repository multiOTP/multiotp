@ECHO OFF
REM ************************************************************
REM @file  checkmultiotp.cmd
REM @brief Test file for the multiOTP package.
REM
REM multiOTP - Strong two-factor authentication PHP class package
REM https://www\.multiOTP.net
REM 
REM The Readme file contains additional information.
REM
REM Windows batch file for Windows 2K/XP/2003/7/2008/8/2012/10/2019
REM
REM @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
REM @version   5.9.8.0
REM @date      2024-08-26
REM @since     2010-07-10
REM @copyright (c) 2010-2024 SysCo systemes de communication sa
REM @copyright GNU Lesser General Public License
REM
REM
REM Description
REM
REM   checkmultiotp is a script that will check some functionalities and
REM   multiotp compliance with RFC4226. It must be launched in the same
REM   directory as the multiotp.exe file.
REM
REM
REM Usage
REM  
REM   The script must be launched in the same directory as multiotp.exe.
REM
REM
REM External files needed
REM
REM   multiotp.exe and all files available in the compressed distribution file
REM   all folders available in the compressed distribution file
REM
REM
REM Licence
REM
REM   Copyright (c) 2010-2024 SysCo systemes de communication sa
REM   SysCo (tm) is a trademark of SysCo systemes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM   multiOTP project is free software; you can redistribute it and/or
REM   modify it under the terms of the GNU Lesser General Public License as
REM   published by the Free Software Foundation, either version 3 of the License,
REM   or (at your option) any later version.
REM
REM   multiOTP project is distributed in the hope that it will be useful,
REM   but WITHOUT ANY WARRANTY; without even the implied warranty of
REM   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
REM   GNU Lesser General Public License for more details.
REM
REM   You should have received a copy of the GNU Lesser General Public
REM   License along with multiOTP PHP class.
REM   If not, see <http://www.gnu.org/licenses/>.
REM
REM
REM Change Log
REM
REM   2022-08-05 5.9.2.0 SysCo/al Tests added for user without 2FA token
REM   2020-12-11 5.8.0.6 SysCo/al Do an automatic "Run as administrator" if needed
REM                               Tests added for PostGreSQL backend (set the _check_pgsql_xxx parameters below)
REM   2018-11-13 5.4.0.2 SysCo/al Calls to nircmd tool removed
REM   2018-08-26 5.3.0.3 SysCo/al Tests adapted for user without 2FA token
REM   2017-05-29 5.0.4.5 SysCo/al Tests adapted to the new services
REM   2016-12-08 5.0.3.4 SysCo/al Tests improved (MS-CHAP was wrongly tested)
REM   2016-10-28 5.0.2.6 SysCo/al Some tests improved
REM   2016-08-02 5.0.1.4 SysCo/al Some tests improved
REM   2015-07-15 4.3.2.5 SysCo/al Some tests improved
REM   2015-06-09 4.3.2.2 SysCo/al Some tests improved
REM   2014-12-07 4.3.1.0 SysCo/al Some tests improved
REM   2014-03-30 4.2.4.2 SysCo/al Version synchronization
REM   2014-03-30 4.2.4.1 SysCo/al Typo to come back to the default encryption at the end of the test
REM                               -request-nt-key added for MS-CHAP(v2) tests
REM   2014-03-30 4.2.4   SysCo/al Tests added for MySQL backend (set the _check_sql_xxx parameters below)
REM                               List of attributes to encrypt in the backend is set to null during the tests
REM   2014-02-07 4.2.0   SysCo/al Tests added for MS-CHAPv2, MS-CHAP and CHAP
REM   2013-01-15 4.1.1   SysCo/al Changing error level handling which could give false test result
REM                               Testing with and without prefix pin, and alphanumeric PIN too
REM   2013-12-23 4.1.0   SysCo/al Adding comments
REM   2013-08-30 4.0.7   SysCo/al Adding no web display parameter
REM   2013-08-25 4.0.6   SysCo/al Additional tests
REM   2013-08-21 4.0.5   SysCo/al Supporting alternate authentication port for the radius test
REM   2013-08-20 4.0.4   SysCo/al Testing new options of the multiOTP library
REM   2010-09-02 3.0.0   SysCo/al More flexible variable definition to launch multiotp
REM   2010-08-21 2.0.4   SysCo/al More documentation, tests results resume
REM   2010-07-19 2.0.1   SysCo/al More documentation
REM   2010-07-19 2.0.0   SysCo/al New version for the new multiotp implementation
REM   2010-06-08 1.1.0   SysCo/al Project renamed to multiotp to avoid overlapping
REM   2010-06-08 1.0.0   SysCo/al Initial release
REM
REM ************************************************************

REM These are the various ports used for the tests.
REM They are different from the default production ports.

IF "%_multiotp_ni%"=="1" GOTO NoWarning
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

REM UTF-8 mode
CHCP 65001 >NUL

IF EXIST "%TEMP%\multiotp_error.log" DEL "%TEMP%\multiotp_error.log" /Q

REM No web display of the webservice installation
SET _no_web_display=1

REM MySQL server test parameters
IF "%_check_sql_server%"==""   SET _check_sql_server=
IF "%_check_sql_username%"=="" SET _check_sql_username=
IF "%_check_sql_password%"=="" SET _check_sql_password=
IF "%_check_sql_database%"=="" SET _check_sql_database=

REM PostGreSQL server test parameters
IF "%_check_pgsql_server%"==""   SET _check_pgsql_server=
IF "%_check_pgsql_username%"=="" SET _check_pgsql_username=
IF "%_check_pgsql_password%"=="" SET _check_pgsql_password=
IF "%_check_pgsql_database%"=="" SET _check_pgsql_database=
IF "%_check_pgsql_schema%"==""   SET _check_pgsql_schema=

REM Radius server test ports
IF "%_check_r_auth_port%"=="" SET _check_r_auth_port=41812
IF "%_check_r_acct_port%"=="" SET _check_r_acct_port=41813

REM Web service test ports
IF "%_check_web_port%"=="" SET _check_web_port=58112
IF "%_check_ssl_port%"=="" SET _check_ssl_port=58113

REM Ports can also be defined as parameters
IF NOT "%1"=="" SET _check_r_auth_port=%1
IF NOT "%2"=="" SET _check_r_acct_port=%2
IF NOT "%3"=="" SET _check_web_port=%3
IF NOT "%4"=="" SET _check_ssl_port=%4

REM Set initial backend
SET _backend=files

IF "%_check_backend%"=="" SET _check_backend=
IF NOT "%_check_backend%"=="" SET _backend=%_check_backend%

REM Detection of the script folder IN SHORT PATH NOTATION
SET _check_dir=%~d0%~sp0
SET _radius_dir=%_check_dir%radius\
SET _tools_dir=%_check_dir%tools\
IF NOT EXIST %_radius_dir%bin SET _radius_dir=%~d0%~p0..\radius\
IF NOT EXIST %_tools_dir%wget.exe SET _tools_dir=%~d0%~p0..\tools\

REM Full path to the multiotp.exe file
SET _multiotp="%_check_dir%multiotp.exe"
IF NOT "%_check_multiotp%"=="" SET _multiotp=%_check_multiotp%
IF NOT "%_multiotp_alternate%"=="" SET _multiotp=%_multiotp_alternate%

SET _multiotp_class_check=check.multiotp.class.php
IF NOT "%_multiotp_class_check_alternate%"=="" SET _multiotp_class_check=%_multiotp_class_check_alternate%


REM Initializing the test counters
SET SUCCESSES=0
SET TOTAL_TESTS=0

REM SET initial backend to files
%_multiotp% -config backend-type=files

ECHO multiotp functionalities and HOTP implementation check
ECHO (RFC 4226, http://www.ietf.org/rfc/rfc4226.txt)
ECHO ------------------------------------------------------

REM Display the multiOTP package version
ECHO.
%_multiotp% -version
%_multiotp% -php-version
%_multiotp% -config log=1 debug=1 >NUL


REM List of attributes to encrypt is set to none during the tests
%_multiotp% -config attributes-to-encrypt=**


REM Define server-secret to default value
%_multiotp% -config server-secret=""

:BackendLoop

REM Set the backend
ECHO.
ECHO Backend is set to %_backend%
%_multiotp% -config backend-type=%_backend%
%_multiotp% -config log-forced-in-file=0

ECHO Initialize backend
IF "mysql"=="%_backend%" %_multiotp% -display-log -initialize-backend
IF "pgsql"=="%_backend%" %_multiotp% -display-log -initialize-backend


REM Delete the test_stéphane (if existing)
%_multiotp% -log -delete test_stéphane
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_stéphane successfully deleted

ECHO.
ECHO Create user test_stéphane with the RFC test values HOTP token and a big alpha PIN
%_multiotp% -log -create -prefix-pin test_stéphane HOTP 3132333435363738393031323334353637383930 "ThisIsALongNonDigitPinCode!" 6 0
IF NOT ERRORLEVEL 12 ECHO - OK! User test_stéphane successfully created
IF NOT ERRORLEVEL 12 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_stéphane
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_stéphane (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_stéphane with the first token of the RFC test values, no prefix
%_multiotp% -keep-local -log test_st\351phane 755224
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_stéphane successfully accepted without prefix
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_stéphane successfully accepted without prefix (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 1 GOTO ErrorNoPrefix
IF ERRORLEVEL 1 ECHO - OK! Token of the user test_stéphane successfully REJECTED (no prefix)
IF ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
:ErrorNoPrefix
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_stéphane with the first token of the RFC test values, with prefix
%_multiotp% -keep-local -log test_st\351phane "ThisIsALongNonDigitPinCode!755224"
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_stéphane successfully accepted
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_stéphane with the first token
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_stéphane with the first token (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

REM Delete the test_user (if existing)
%_multiotp% -log -delete test_user
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user successfully deleted

ECHO.
ECHO Create user test_user with the RFC test values HOTP token and a big alpha PIN
%_multiotp% -log -create -prefix-pin test_user HOTP 3132333435363738393031323334353637383930 "ThisIsALongNonDigitPinCode!" 6 0
IF NOT ERRORLEVEL 12 ECHO - OK! User test_user successfully created
IF NOT ERRORLEVEL 12 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with the first token of the RFC test values, no prefix
%_multiotp% -keep-local -log test_user 755224
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_user successfully accepted without prefix
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_user successfully accepted without prefix (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 1 GOTO ErrorNoPrefix
IF ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully REJECTED (no prefix)
IF ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
:ErrorNoPrefix
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with the first token of the RFC test values, bad prefix
%_multiotp% -keep-local -log test_user "ThisIsNotMyLongPinCode755224"
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_user successfully accepted with a bad prefix
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_user successfully accepted with a bad prefix (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 1 GOTO ErrorFalsePrefix
IF ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully REJECTED (bad prefix)
IF ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
:ErrorFalsePrefix
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with the first token of the RFC test values, with prefix
%_multiotp% -keep-local -log test_user "ThisIsALongNonDigitPinCode!755224"
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully accepted
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user with the first token
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user with the first token (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Test replay rejection for user test_user
%_multiotp% -keep-local -log test_user "ThisIsALongNonDigitPinCode!755224"
IF NOT ERRORLEVEL 26 ECHO - KO! Replayed token *WRONGLY* accepted
IF NOT ERRORLEVEL 26 ECHO - KO! Replayed token *WRONGLY* accepted (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 26 GOTO ErrorReplay
ECHO - OK! Token of the user test_user successfully REJECTED (replay)
SET /A SUCCESSES=SUCCESSES+1
:ErrorReplay
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Resynchronize the key for user test_user (with prefix)
%_multiotp% -keep-local -log -resync -status test_user "ThisIsALongNonDigitPinCode!287082" "ThisIsALongNonDigitPinCode!359152"
IF NOT ERRORLEVEL 15 ECHO - OK! Token of the user test_user successfully resynchronized
IF NOT ERRORLEVEL 15 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 15 ECHO - KO! Token of the user test_user NOT resynchronized
IF ERRORLEVEL 15 ECHO - KO! Token of the user test_user NOT resynchronized (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Resynchronize the key for user test_user (without prefix, even if needed)
%_multiotp% -keep-local -log -resync -status test_user 338314 254676
IF NOT ERRORLEVEL 15 ECHO - OK! Token of the user test_user successfully resynchronized
IF NOT ERRORLEVEL 15 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 15 ECHO - KO! Token of the user test_user NOT resynchronized
IF ERRORLEVEL 15 ECHO - KO! Token of the user test_user NOT resynchronized (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Check the automatic cleaning of a user name with a @my.domain suffix
%_multiotp% -keep-local -log test_user@my.domain "ThisIsALongNonDigitPinCode!287922"
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the cleaned user test_user@my.domain.test successfully accepted
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the cleaned user test_user
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the cleaned user test_user (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Test false resynchronisation (in the past, may take some time)
%_multiotp% -keep-local -log -resync -status test_user 287082 359152
IF NOT ERRORLEVEL 20 ECHO - KO! Token of user test_user *WRONGLY* resynchronized
IF NOT ERRORLEVEL 20 ECHO - KO! Token of user test_user *WRONGLY* resynchronized (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 20 GOTO ErrorSynchro
IF ERRORLEVEL 20 ECHO - OK! Token of test_user successfully NOT resynchronized (in the past)
IF ERRORLEVEL 20 SET /A SUCCESSES=SUCCESSES+1
:ErrorSynchro
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with next valid token 162583 with prefix using MS-CHAPv2
REM user test_user and password "ThisIsALongNonDigitPinCode!162583"
%_multiotp% -keep-local -log test_user -request-nt-key -ms-chap-challenge=0xc5356d83125a36b655c59a05b2245d68 -ms-chap2-response=0x00006cea45ad4f3e3a6af414cc09619aeb1e00000000000000004dd32ee9f3b898cf4fcd665ba167a303ce2c1266e7a26f10
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully accepted using MS-CHAPv2
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user using MS-CHAPv2
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user using MS-CHAPv2 (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with replayed token 162583 with prefix using MS-CHAPv2
REM user test_user and password "ThisIsALongNonDigitPinCode!162583"
%_multiotp% -keep-local -log test_user -ms-chap-challenge=0xc5356d83125a36b655c59a05b2245d68 -ms-chap2-response=0x00006cea45ad4f3e3a6af414cc09619aeb1e00000000000000004dd32ee9f3b898cf4fcd665ba167a303ce2c1266e7a26f10
IF NOT ERRORLEVEL 26 ECHO - KO! Replayed token of the user test_user wrongly accepted
IF NOT ERRORLEVEL 26 ECHO - KO! Replayed token of the user test_user wrongly accepted (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 26 GOTO ErrorReplayedMsChapV2
IF ERRORLEVEL 26 ECHO - OK! Replayed Token of the test_user successfully REJECTED
IF ERRORLEVEL 26 SET /A SUCCESSES=SUCCESSES+1
:ErrorReplayedMsChapV2
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with next token 399871 with prefix 1234 using MS-CHAP
REM user test_user and password 1234399871
%_multiotp% -keep-local -log -set test_user pin=1234
%_multiotp% -keep-local -log test_user -request-nt-key -ms-chap-challenge=0x29c9fd75e57a83b778ed911258c35bab -ms-chap-response=0x0001dcbf446a704793383684c8ee1cde8b3130e5b788fa878f668e688cff12d7f0049cbc30d7cd88d33321d641ae1bffd830
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully accepted using MS-CHAP
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user using MS-CHAP
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user using MS-CHAP (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user with next token 520489 with prefix 1234 using CHAP
REM user test_user and password 1234520489
%_multiotp% -keep-local -log test_user -chap-challenge=0xb20cd9303226db8f79c9c5c581ca90d9 -chap-password=0x127c6ce2ac656c3f6eafcea416ecb59f9e
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully accepted using CHAP
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user using CHAP
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user using CHAP (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

REM Delete the user test_user@one.domain (if existing)
%_multiotp% -log -delete test_user@one.domain
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user@one.domain successfully deleted

ECHO.
ECHO Create user test_user@one.domain with the RFC test values HOTP token
%_multiotp% -log -create -no-prefix-pin test_user@one.domain HOTP 3132333435363738393031323334353637383930 1234 6 0
IF NOT ERRORLEVEL 12 ECHO - OK! User test_user@one.domain successfully created
IF NOT ERRORLEVEL 12 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user@one.domain
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user@one.domain (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user@one.domain with the first token of the RFC test values
%_multiotp% -keep-local -log test_user@one.domain 755224
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user@one.domain successfully accepted
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user@one.domain with the first token
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user@one.domain with the first token (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

REM Delete the test_user2 (if existing)
%_multiotp% -log -delete test_user2
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user2 successfully deleted

ECHO.
ECHO Create user test_user2 with the RFC test values HOTP token and a big PIN prefix
ECHO (like Authenex / ZyXEL / Billion is doing for their OTP solution)
%_multiotp% -log -create -prefix-pin test_user2 HOTP 3132333435363738393031323334353637383930 "ThisIsAnOtherBigAlphaNumericPrefixPinWith-Minus And Space And ^&" 6 0 -display-log -debug -param
IF NOT ERRORLEVEL 12 ECHO - OK! User test_user2 successfully created
IF NOT ERRORLEVEL 12 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user2
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user2 (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user2 with the first token of the RFC test value with big PIN
%_multiotp% -keep-local -log test_user2 "ThisIsAnOtherBigAlphaNumericPrefixPinWith-Minus And Space And ^&755224" -display-log -debug -param
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user2 (with prefix PIN) successfully accepted
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user2 with the first token and PIN prefix
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user2 with the first token and PIN prefix (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1


IF NOT EXIST %_radius_dir%bin GOTO NoRadiusCheck

ECHO.
ECHO - Install and start the RADIUS server (wait 5 seconds)
CALL %_check_dir%radius_install.cmd %_check_r_auth_port% %_check_r_acct_port% multiOTPradiusTest multiOTPradiusTest
PING 127.0.0.1 -n 5 >NUL

ECHO.
ECHO Authenticate test_user2 with the second token through the RADIUS server
ECHO User-Name = "test_user2">"%TEMP%\radiustest.conf"
ECHO User-Password = "ThisIsAnOtherBigAlphaNumericPrefixPinWith-Minus And Space And ^&287082">>"%TEMP%\radiustest.conf"
ECHO NAS-IP-Address = 127.0.0.1>>"%TEMP%\radiustest.conf"
ECHO NAS-Port = %_check_r_auth_port%>>"%TEMP%\radiustest.conf"

%_radius_dir%bin\radclient.exe -c 1 -d %_radius_dir%etc\raddb -f "%TEMP%\radiustest.conf" -r 1 -t 5 127.0.0.1:%_check_r_auth_port% auth multiotpsecret
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user2 successfully accepted by RADIUS server
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user2 with by the RADIUS server
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user2 with by the RADIUS server (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1
DEL "%TEMP%\radiustest.conf" /Q

ECHO.
ECHO - Stop and uninstall the RADIUS server
CALL %_check_dir%radius_uninstall.cmd multiOTPradiusTest

:NoRadiusCheck


ECHO.
ECHO - Install and start the multiOTP web service (wait 5 seconds)
%_tools_dir%wget http://127.0.0.1:%_check_web_port% --quiet --output-document="%TEMP%\multiOTPwebservice.check" --timeout=300 --tries=2
FIND /C "Web service is ready" "%TEMP%\multiOTPwebservice.check" >NUL
IF NOT ERRORLEVEL 1 GOTO WebServiceAlreadyHere
%_multiotp% -config server-secret=""
CALL %_check_dir%webservice_install.cmd %_check_web_port% %_check_ssl_port% multiOTPserverTest multiOTPserverTest
PING 127.0.0.1 -n 5 >NUL 
:WebServiceAlreadyHere

ECHO.
ECHO Check the default multiOTP web service page
%_tools_dir%wget http://127.0.0.1:%_check_web_port% --quiet --output-document="%TEMP%\multiOTPwebservice.check" --timeout=300 --tries=2
FIND "Web service is ready" "%TEMP%\multiOTPwebservice.check" | FIND "Web service is ready" > "%TEMP%\multiOTPwebservice.ready"
IF NOT ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.ready"
IF NOT ERRORLEVEL 1 ECHO - OK! multiOTP web service is responding correctly
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on http://127.0.0.1:%_check_web_port%
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on http://127.0.0.1:%_check_web_port% (%_backend%) >>"%TEMP%\multiotp_error.log"
IF ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.check"
IF ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.check" (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1
DEL "%TEMP%\multiOTPwebservice.check" /Q
DEL "%TEMP%\multiOTPwebservice.ready" /Q

ECHO.
ECHO Check the https default multiOTP web service page
%_tools_dir%wget https://127.0.0.1:%_check_ssl_port% --no-check-certificate --quiet --output-document="%TEMP%\multiOTPwebservice.check" --timeout=300 --tries=2
FIND "Web service is ready" "%TEMP%\multiOTPwebservice.check" | FIND "Web service is ready" > "%TEMP%\multiOTPwebservice.ready"
IF NOT ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.ready"
IF NOT ERRORLEVEL 1 ECHO - OK! multiOTP web service is responding correctly
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on https://127.0.0.1:%_check_ssl_port%
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on https://127.0.0.1:%_check_ssl_port% (%_backend%) >>"%TEMP%\multiotp_error.log"
IF ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.check"
IF ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.check" (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1
 DEL "%TEMP%\multiOTPwebservice.check" /Q
DEL "%TEMP%\multiOTPwebservice.ready" /Q

ECHO.
ECHO Authenticate test_user2 through web service using default secret
REM Default secret is ClientServerSecret, full token is 1234359152
%_multiotp% -log -set test_user2 pin=1234
SET _server_challenge=XUZIW25kIz53KDB1BTAwF2U/V2x9FzB0Xjp1IDEiNmMgZjI/
SET _chap_id=34
SET _chap_challenge=4af06915f7cbdfd018f5c60047dc8a2f
SET _chap_password=936660d3d0bef545c63e73fa7ee30bd1
ECHO data=^<?xml version="1.0" encoding="UTF-8"?^>^<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp"^>^<ServerChallenge^>%_server_challenge%^</ServerChallenge^>^<CheckUserToken^>^<UserId^>test_user2^</UserId^>^<Chap^>^<ChapId^>%_chap_id%^</ChapId^>^<ChapChallenge^>%_chap_challenge%^</ChapChallenge^>^<ChapPassword^>%_chap_password%^</ChapPassword^>^</Chap^>^<CacheLevel^>1^</CacheLevel^>^</CheckUserToken^>^</multiOTP^> >"%TEMP%\multiOTPwebservice.post"
TYPE "%TEMP%\multiOTPwebservice.post"
%_tools_dir%wget --post-file "%TEMP%\multiOTPwebservice.post" http://127.0.0.1:%_check_web_port% --quiet --output-document="%TEMP%\multiOTPwebservice.check" --timeout=300 --tries=2
FIND /C "OK: Token accepted" "%TEMP%\multiOTPwebservice.check" >NUL
IF NOT ERRORLEVEL 1 ECHO - OK! multiOTP web service is responding correctly
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on http://127.0.0.1:%_check_web_port%
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on http://127.0.0.1:%_check_web_port% (%_backend%) >>"%TEMP%\multiotp_error.log"
IF ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.check"
IF ERRORLEVEL 1 TYPE "%TEMP%\multiOTPwebservice.check" (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1
DEL "%TEMP%\multiOTPwebservice.post" /Q
DEL "%TEMP%\multiOTPwebservice.check" /Q
SET _server_challenge=
SET _chap_id=
SET _chap_challenge=
SET _chap_password=

ECHO.
ECHO Generate scratch passwords for test_user2
FOR /f "tokens=1*" %%a, in ('%_multiotp% -keep-local -scratchlist test_user2') DO (
SET _password=%%a
ECHO %%a
)
IF NOT ERRORLEVEL 20 ECHO - OK! Scratch list for test_user2 successfully created
IF NOT ERRORLEVEL 20 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 20 ECHO - KO! Scratch list for test_user2 NOT successfully created
IF ERRORLEVEL 20 ECHO - KO! Scratch list for test_user2 NOT successfully created (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Test the last scratch password (%_password%) for test_user2 with prefix
%_multiotp% -keep-local -log test_user2 1234%_password%
IF NOT ERRORLEVEL 1 ECHO - OK! Scratch password accepted for test_user2
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Scratch password NOT accepted for test_user2
IF ERRORLEVEL 1 ECHO - KO! Scratch password NOT accepted for test_user2 (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Test again the last scratch password (%_password%) for test_user2 with prefix
%_multiotp% -keep-local -log test_user2 1234%_password%
IF NOT ERRORLEVEL 1 ECHO - KO! Scratch password IS WRONGLY accepted a second time for test_user2
IF NOT ERRORLEVEL 1 ECHO - KO! Scratch password IS WRONGLY accepted a second time for test_user2 (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 1 GOTO ErrorScratch
ECHO - OK! Scratch password is not accepted a second time for test_user2
SET /A SUCCESSES=SUCCESSES+1
:ErrorScratch
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Create user test_user_no_2fa without 2FA token and without prefix
%_multiotp% -log -create -no-prefix-pin test_user_no_2fa WITHOUT2FA
IF NOT ERRORLEVEL 12 ECHO - OK! User test_user_no_2fa successfully created
IF NOT ERRORLEVEL 12 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user_no_2fa
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user_no_2fa (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user_no_2fa without 2FA token and without prefix
%_multiotp% -keep-local -log test_user_no_2fa ""
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user_no_2fa successfully accepted
IF NOT ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user_no_2fa with the first token
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user_no_2fa with the first token (%_backend%) >>"%TEMP%\multiotp_error.log"
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticate test_user_no_2fa without 2FA token and without prefix with a bad value
%_multiotp% -keep-local -log test_user_no_2fa "badvalue"
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_user test_user_no_2fa accepted with bad value
IF NOT ERRORLEVEL 1 ECHO - KO! Token of the user test_user test_user_no_2fa accepted with bad value (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 1 GOTO ErrorBadValue2FA
IF ERRORLEVEL 1 ECHO - OK! Token of the user test_user_no_2fa successfully REJECTED (bad value)
IF ERRORLEVEL 1 SET /A SUCCESSES=SUCCESSES+1
:ErrorBadValue2FA
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Check test_user_no_2fa to see if it is a without 2FA token
%_multiotp% -iswithout2fa -keep-local -log test_user_no_2fa ""
IF NOT ERRORLEVEL 8 ECHO - KO! Error checking the user test_user_no_2fa token type
IF NOT ERRORLEVEL 8 ECHO - KO! Error checking the user test_user_no_2fa token type (%_backend%) >>"%TEMP%\multiotp_error.log"
IF ERRORLEVEL 8 ECHO - OK! Token of the user test_user_no_2fa is a without 2FA token
IF ERRORLEVEL 8 SET /A SUCCESSES=SUCCESSES+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Check test_user2 to see if it is not a without 2FA token
%_multiotp% -iswithout2fa -keep-local -log test_user2 ""
IF ERRORLEVEL 8 ECHO - KO! Error 8 checking the user test_user2 token type
IF ERRORLEVEL 8 ECHO - KO! Error 8 checking the user test_user2 token type (%_backend%) >>"%TEMP%\multiotp_error.log"
IF ERRORLEVEL 8 GOTO CheckIsNotWithout2FA
IF NOT ERRORLEVEL 7 ECHO - KO! Error checking the user test_user2 token type
IF NOT ERRORLEVEL 7 ECHO - KO! Error checking the user test_user2 token type (%_backend%) >>"%TEMP%\multiotp_error.log"
IF NOT ERRORLEVEL 7 GOTO CheckIsNotWithout2FA
IF ERRORLEVEL 7 ECHO - OK! Token of the user test_user2 is a without 2FA token
IF ERRORLEVEL 7 SET /A SUCCESSES=SUCCESSES+1
:CheckIsNotWithout2FA
SET /A TOTAL_TESTS=TOTAL_TESTS+1

REM GOTO DelTestUserSkip

ECHO.
ECHO And now, delete old users...
REM Delete the test_user2 (if existing)
ECHO  - test_user2
%_multiotp% -log -delete test_user2
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user2 successfully deleted

REM Delete the test_user
ECHO  - test_user
%_multiotp% -log -delete test_user
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user successfully deleted

REM Delete the test_user@one.domain
ECHO  - test_user@one.domain
%_multiotp% -log -delete test_user@one.domain
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user@one.domain successfully deleted

REM Delete the test_user_no_2fa
ECHO  - test_user_no_2fa
%_multiotp% -log -delete test_user_no_2fa
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_user2 successfully deleted

REM Delete the test_stéphane
ECHO  - test_stéphane
%_multiotp% -log -delete test_stéphane
IF NOT ERRORLEVEL 13 ECHO.
IF NOT ERRORLEVEL 13 ECHO - User test_stéphane successfully deleted

:DelTestUserSkip

REM Show Log
REM %_multiotp% -showlog


IF "mysql"=="%_backend%" GOTO EndMySqlLoop
IF "pgsql"=="%_backend%" GOTO EndPgSqlLoop


REM Do all the tests a second time for the MySQL server backend if all parameters are there
:MySqlLoop
IF ""=="%_check_sql_server%" GOTO EndMySqlLoop
IF ""=="%_check_sql_username%" GOTO EndMySqlLoop
IF ""=="%_check_sql_password%" GOTO EndMySqlLoop
IF ""=="%_check_sql_database%" GOTO EndMySqlLoop
SET _backend=mysql
ECHO.
ECHO Set the backend parameters for %_backend%
%_multiotp% -config backend-type=files
%_multiotp% -config sql-server=%_check_sql_server% sql-username=%_check_sql_username% sql-password=%_check_sql_password% sql-database=%_check_sql_database%
GOTO BackendLoop
:EndMySqlLoop


REM Do all the tests a third time for the PostgreSQL server backend if all parameters are there
:PgSqlLoop
IF ""=="%_check_pgsql_server%" GOTO EndPgSqlLoop
IF ""=="%_check_pgsql_username%" GOTO EndPgSqlLoop
IF ""=="%_check_pgsql_password%" GOTO EndPgSqlLoop
IF ""=="%_check_pgsql_database%" GOTO EndPgSqlLoop
IF ""=="%_check_pgsql_schema%" GOTO EndPgSqlLoop
SET _backend=pgsql
ECHO.
ECHO Set the backend parameters for %_backend%
%_multiotp% -config backend-type=files
%_multiotp% -config sql-server=%_check_pgsql_server% sql-username=%_check_pgsql_username% sql-password=%_check_pgsql_password% sql-database=%_check_pgsql_database% sql-schema=%_check_pgsql_schema%
GOTO BackendLoop
:EndPgSqlLoop


SET _backend=files
%_multiotp% -config backend-type=%_backend%


REM List of attributes to encrypt is set to default value
%_multiotp% -config attributes-to-encrypt=


ECHO.
ECHO End of the CLI multiOTP tests
IF %SUCCESSES% EQU %TOTAL_TESTS% ECHO (all %TOTAL_TESTS% tests are OK so far...)
ECHO.


ECHO.
ECHO Check the PHP multiOTP class using the %_multiotp_class_check% file.
%_tools_dir%wget http://127.0.0.1:%_check_web_port%/check/?minima=1^&keeplog=1 --quiet --output-document="%TEMP%\check.multiOTP.class.check" --timeout=300 --tries=2
FIND /C "KO!" "%TEMP%\check.multiOTP.class.check" >NUL
TYPE "%TEMP%\check.multiOTP.class.check"
IF ERRORLEVEL 1 GOTO CheckClassError

:CheckClassOk
ECHO - OK! multiOTP class tests successful
SET /A SUCCESSES=SUCCESSES+1
GOTO CheckClassEnd

:CheckClassError
ECHO - KO! multiOTP class tests failed (http://127.0.0.1:%_check_web_port%/check/?minima=1)
ECHO - KO! multiOTP class tests failed (http://127.0.0.1:%_check_web_port%/check/?minima=1) (%_backend%) >>"%TEMP%\multiotp_error.log"
CHOICE /T 5 /C ny /D n /M "Type [y] in the next 5 seconds to pause the process"
ECHO.
IF ERRORLEVEL 2 PAUSE
ECHO.

:CheckClassEnd
DEL "%TEMP%\check.multiOTP.class.check" /Q
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO - Stop and uninstall the multiOTP web service
CALL %_check_dir%webservice_uninstall.cmd multiOTPserverTest


ECHO.
ECHO.

IF "%_multiotp_ni%"=="1" GOTO NoResultSummary
IF %SUCCESSES% EQU %TOTAL_TESTS% ECHO OK! ALL %SUCCESSES% TESTS HAVE PASSED SUCCESSFULLY !
IF %SUCCESSES% NEQ %TOTAL_TESTS% ECHO KO! ONLY %SUCCESSES%/%TOTAL_TESTS% TESTS HAVE PASSED SUCCESSFULLY !
IF %SUCCESSES% NEQ %TOTAL_TESTS% TYPE "%TEMP%\multiotp_error.log"
:NoResultSummary

ECHO.

SET _backend=
SET _check_dir=
SET _radius_dir=
SET _tools_dir=
SET _multiotp=
SET _multiotp_class_check=

SET _check_r_auth_port=
SET _check_r_acct_port=
SET _check_web_port=
SET _check_ssl_port=

SET _no_web_display=

IF "%_multiotp_ni%"=="1" Goto NoPause

PAUSE

:NoPause
