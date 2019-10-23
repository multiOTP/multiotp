/**
 * @file  launcher.cpp
 * @brief Launcher for the multiOTP open source embedded CLI package
 *
 * multiOTP C++ launcher - Strong two-factor authentication solution
 * http://www.multiotp.net
 *
 * Visit http://forum.multiotp.net/ for additional support.
 *
 * Donation are always welcome! Please check http://www.multiotp.net
 * and you will find the magic button ;-)
 *
 * The multiOTP C++ launcher is simply used to launch PHP
 * and run multiotp.windows.php with the provided arguments.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
 * @version   5.6.1.5
 * @date      2019-10-23
 * @since     2016-12-08
 * @copyright (c) 2010-2019 SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2010-2019 SysCo systemes de communication sa
 *   SysCo (tm) is a trademark of SysCo systemes de communication sa
 *   (http://www.sysco.ch)
 *   All rights reserved.
 * 
 *   This file is part of the multiOTP open source project.
 *
 *   multiOTP open source project is free software; you can redistribute it
 *   and/or modify it under the terms of the GNU Lesser General Public License
 *   as published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.
 * 
 *   multiOTP open source project is distributed in the hope that it will be
 *   useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 * 
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with multiOTP open source project.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Change Log
 *
 *   2018-03-05 5.1.0.8 SysCo/al Adapt the php.exe path automatically
 *   2018-02-19 5.1.0.6 SysCo/al Quotes tested and debugged with Credential Provider
 *   2018-02-19 5.1.0.3 SysCo/al Comments cleaning
 *   2017-05-29 5.0.4.5 SysCo/al Quotes must by around the launch string
 *   2016-12-29 5.0.3.4 SysCo/al Initial implementation and distribution
 *
 *********************************************************************/

#include "stdafx.h"

#define WIN32_LEAN_AND_MEAN

#include <direct.h>
#include <fcntl.h>
#include <io.h>
#include <windows.h>
#include <string>
#include <iostream>

#define SOFTWARE    "LAUNCHPHPMULTIOTP"
#define VER_NUMBER  "5.6.1.5"
#define VER_DATE    "2019-10-23"

int _tmain(int argc, _TCHAR* argv[])
{
  std::string cli_arg = argv[0];
  cli_arg = cli_arg + "\\..\\";

  const char* cli_arg_char = cli_arg.c_str();

  char basePath[4096] = "";
  _fullpath(basePath, cli_arg_char, sizeof(basePath));

  std::string quote = "\"";
  
  std::string pathToPhp = quote + basePath + "php\\php.exe" + quote;
  std::string pathToMultiotp = quote + basePath + "php\\multiotp.windows.php" + quote;

  // std::string defaultPath = basePath + "php";
  // char defaultPath[4096] = "";
  // strcpy_s(defaultPath, basePath);
  // strcat_s(defaultPath, "php");

  std::string run_software = pathToPhp + " " + pathToMultiotp + " -base-dir=" + quote + basePath + "." + quote;

  for (int i = 1; i < argc; i = i + 1) {
      run_software = run_software + " " + quote + argv[i] + quote;
  }

  // Put the run software between quotes, because system use CMD /K
  run_software = quote + run_software + quote;

  const char* run_software_char = run_software.c_str();
  // std::cout << "Full RUN: " << run_software_char << std::endl;

  _chdir(basePath);

  return system(run_software_char);
}
