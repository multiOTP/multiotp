#!/bin/bash
##########################################################################
#
# @file   newvm.sh
# @brief  Reset multiOTP open source installation (Raspberry Pi / VM / Docker)
#
# multiOTP package - Strong two-factor authentication open source package
# https://www.multiotp.net/
#
# @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.10.4.2
# @date      2026-10-31
# @since     2013-09-22
# @copyright (c) 2013-2026 SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
# 2026-10-30 5.10.4.1 SysCo/al Update 013 to 013.07 / 013.07u
# 2026-04-03 5.10.2.2 SysCo/al Better rights handling in the Docker version
# 2026-03-06 5.10.2.0 SysCo/al Add initial AWS and Ubuntu support
# 2025-11-19 5.10.0.5 SysCo/al Removed old multiotp-temp folder trick
# 2025-10-31 5.10.0.2 SysCo/al Additional cleaning for Debian Trixie 13.0 support
# 2025-10-16 5.9.9.3 SysCo/al Add Debian Trixie 13.0 support
# 2023-11-23 5.9.7.0 SysCo/al Update Raspberry Pi detection 
# 2023-10-11 5.9.6.8 SysCo/al Add Debian Bookworm 12.0 support
# 2022-05-26 5.9.0.3 SysCo/al Issue with /run/php when a Docker container is restarted
# 2022-05-08 5.8.8.4 SysCo/al Better docker support (also for Synology)
# 2022-05-08 5.8.8.1 SysCo/al Add Raspberry Pi Bullseye 11.0 support
# 2021-09-14 5.8.3.0 SysCo/al VM version 011 support
#                             (Debian Bullseye 11.0, PHP 7.4, FreeRADIUS 3.0.21, Nginx 1.18.0)
# 2020-08-31 5.8.0.0 SysCo/al Raspberry Pi 4B support
#                             New unified distribution
#                             Debian Buster 10.5 support
#                             PHP 7.3 support
# 2019-10-23 5.6.1.5 SysCo/al Debian Buster support
# 2019-01-30 5.4.1.7 SysCo/al Support any source path for the installation
# 2019-01-07 5.4.1.1 SysCo/al VM version 008 support (Debian 9.x Stretch, PHP 7, FreeRADIUS 3.x)
# 2018-03-20 5.1.1.2 SysCo/al VM version 007 for Debian 8.x (PHP 5)
#                             Initial Docker support (Debian 8.x)
#                             OS version and ID detection
# 2017-05-16 5.0.4.4 SysCo/al VM upgraded to version 006
# 2016-11-19 5.0.3.1 SysCo/al Better support for Raspberry Pi, enhanced SSL support
# 2016-11-07 5.0.2.7 SysCo/al Better tuning depending on virtual family
#                              (blacklist i2c_piix4 and nsc_ircc)
# 2016-11-04 5.0.2.6 SysCo/al Better hardware detection
# 2016-03-18 5.0.0.0 SysCo/al Raspberry Pi support
# 2013-09-22 4.0.9.0 SysCo/al Initial release
##########################################################################

TEMPVERSION="@version   5.10.4.2"
MULTIOTPVERSION="$(echo -e "${TEMPVERSION:8}" | tr -d '[[:space:]]')"
IFS='.' read -ra MULTIOTPVERSIONARRAY <<< "$MULTIOTPVERSION"
MULTIOTPMAJORVERSION=${MULTIOTPVERSIONARRAY[0]}


# Docker detection and installation set (2026-02-27)
RUNDOCKER="FALSE"
if [ $# -ge 1 ]; then
  if [[ "$1" == "RUNDOCKER" ]] || [[ "$2" == "RUNDOCKER" ]] || [[ "$3" == "RUNDOCKER" ]]; then
    echo "Docker installation"
    RUNDOCKER="TRUE"
  else
    RUNDOCKER="FALSE"
  fi
fi
UNAME=$(uname -a)
if [[ "${RUNDOCKER}" == "TRUE" ]]; then
  FAMILY="VAP"
  TYPE="DOCKER"
elif [[ "${UNAME}" == *docker* ]]; then
  FAMILY="VAP"
  TYPE="DOCKER"
elif grep -q docker /proc/1/cgroup; then 
  FAMILY="VAP"
  TYPE="DOCKER"
elif grep -q docker /proc/self/cgroup; then 
  FAMILY="VAP"
  TYPE="DOCKER"
elif [ -f /.dockerenv ]; then
  FAMILY="VAP"
  TYPE="DOCKER"
fi
if [[ "${TYPE}" == "DOCKER" ]]; then
  touch /usr/local/games/docker.flag
fi
# End of docker detection and installation set (2026-02-27)


# Populate the multiotp and freeradius config and log if not existing
if [[ "${RUNDOCKER}" == "TRUE" ]]; then
  if [ -d /etc/multiotp ]; then
    chmod 777 -R /etc/multiotp
  fi
  if [ -d /usr/local/bin/multiotp ]; then
    chmod 777 -R /usr/local/bin/multiotp
  fi
  if [ -d /var/log/multiotp ]; then
    chmod 777 -R /var/log/multiotp
  fi
  if [ -d /var/log/freeradius ]; then
    chmod 777 -R /var/log/freeradius
  fi
  if [ ! -f /etc/multiotp/config/multiotp.ini ] && [ -d /var/multiotp-temp/etc/multiotp ] ; then
    echo "Retrieve multiOTP config files"
    cp -an /var/multiotp-temp/etc/multiotp/* /etc/multiotp
    cp -an /var/multiotp-temp/log/multiotp/* /var/log/multiotp
  fi
  if [ ! -d /etc/freeradius/3.0/mods-available/multiotp ] && [ -f /var/multiotp-temp/etc/freeradius/3.0/mods-available/multiotp ] ; then
    echo "Retrieve FreeRADIUS config files"
    cp -an /var/multiotp-temp/etc/freeradius/* /etc/freeradius
    cp -an /var/multiotp-temp/log/freeradius/* /var/log/freeradius
  fi
fi


SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
SOURCEDIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"

# OS ID and version (2026-01-29)
# Architecture (for example x86_64)
OSID=$(cat /etc/os-release | grep "^ID=" | awk -F'=' '{print $2}')
OSVERSION=$(awk -F'"' '/VERSION_ID/ {split($2,a,"."); print a[1]}' /etc/os-release)
ARCHITECTURE=$(lscpu |grep "^Architecture" | awk -F':' '{print $2}' | awk '{$1=$1;print}')
IFNAME=$(ip -o link show | awk -F': ' '{print $2}' | grep -v '^lo$')

BACKENDDB="mariadb"
PHPFPM="php8.4-fpm"
PHPFPMSED="php\/php8.4-fpm"
PHPINSTALLPREFIX="php"
PHPINSTALLPREFIXVERSION="php8.4"
PHPMODULEPREFIX="php/8.4"
PHPMAJORVERSION="8"
SQLITEVERSION="sqlite3"
VMRELEASENUMBER="013.07"
if [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "7" ]]; then
  BACKENDDB="mysql"
  PHPFPM="php5-fpm"
  PHPFPMSED="php5-fpm"
  PHPINSTALLPREFIX="php5"
  PHPINSTALLPREFIXVERSION="php5"
  PHPMODULEPREFIX="php5"
  PHPMAJORVERSION="5"
  SQLITEVERSION="sqlite"
  VMRELEASENUMBER="007"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "8" ]]; then
  BACKENDDB="mysql"
  PHPFPM="php5-fpm"
  PHPFPMSED="php5-fpm"
  PHPINSTALLPREFIX="php5"
  PHPINSTALLPREFIXVERSION="php5"
  PHPMODULEPREFIX="php5"
  PHPMAJORVERSION="5"
  SQLITEVERSION="sqlite"
  VMRELEASENUMBER="007"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "9" ]]; then
  BACKENDDB="mysql"
  PHPFPM="php7.0-fpm"
  PHPFPMSED="php\/php7.0-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php7.0"
  PHPMODULEPREFIX="php/7.0"
  PHPMAJORVERSION="7"
  SQLITEVERSION="sqlite"
  VMRELEASENUMBER="008"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "10" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php7.3-fpm"
  PHPFPMSED="php\/php7.3-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php7.3"
  PHPMODULEPREFIX="php/7.3"
  PHPMAJORVERSION="7"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="010"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "11" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php7.4-fpm"
  PHPFPMSED="php\/php7.4-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php7.4"
  PHPMODULEPREFIX="php/7.4"
  PHPMAJORVERSION="7"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="011"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "12" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php8.2-fpm"
  PHPFPMSED="php\/php8.2-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php8.2"
  PHPMODULEPREFIX="php/8.2"
  PHPMAJORVERSION="8"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="012"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "13" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php8.4-fpm"
  PHPFPMSED="php\/php8.4-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php8.4"
  PHPMODULEPREFIX="php/8.4"
  PHPMAJORVERSION="8"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="013.07"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "7" ]]; then
  BACKENDDB="mysql"
  PHPFPM="php5-fpm"
  PHPFPMSED="php5-fpm"
  PHPINSTALLPREFIX="php5"
  PHPINSTALLPREFIXVERSION="php5"
  PHPMODULEPREFIX="php5"
  PHPMAJORVERSION="5"
  SQLITEVERSION="sqlite"
  VMRELEASENUMBER="007"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "8" ]]; then
  BACKENDDB="mysql"
  PHPFPM="php5-fpm"
  PHPFPMSED="php5-fpm"
  PHPINSTALLPREFIX="php5"
  PHPINSTALLPREFIXVERSION="php5"
  PHPMODULEPREFIX="php5"
  PHPMAJORVERSION="5"
  SQLITEVERSION="sqlite"
  VMRELEASENUMBER="007"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "9" ]]; then
  BACKENDDB="mysql"
  PHPFPM="php7.0-fpm"
  PHPFPMSED="php\/php7.0-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php7.0"
  PHPMODULEPREFIX="php/7.0"
  PHPMAJORVERSION="7"
  SQLITEVERSION="sqlite"
  VMRELEASENUMBER="008"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "10" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php7.3-fpm"
  PHPFPMSED="php\/php7.3-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php7.3"
  PHPMODULEPREFIX="php/7.3"
  PHPMAJORVERSION="7"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="010"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "11" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php7.4-fpm"
  PHPFPMSED="php\/php7.4-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php7.4"
  PHPMODULEPREFIX="php/7.4"
  PHPMAJORVERSION="7"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="011"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "12" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php8.2-fpm"
  PHPFPMSED="php\/php8.2-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php8.2"
  PHPMODULEPREFIX="php/8.2"
  PHPMAJORVERSION="8"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="012"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "13" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php8.4-fpm"
  PHPFPMSED="php\/php8.4-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php8.4"
  PHPMODULEPREFIX="php/8.4"
  PHPMAJORVERSION="8"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="013.07"
elif [[ "${OSID}" == "ubuntu" ]] && [[ "${OSVERSION}" == "24" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php8.3-fpm"
  PHPFPMSED="php\/php8.3-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php8.3"
  PHPMODULEPREFIX="php/8.3"
  PHPMAJORVERSION="8"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="013.07u"
elif [[ "${OSID}" == "ubuntu" ]] && [[ "${OSVERSION}" == "26" ]]; then
  BACKENDDB="mariadb"
  PHPFPM="php8.5-fpm"
  PHPFPMSED="php\/php8.5-fpm"
  PHPINSTALLPREFIX="php"
  PHPINSTALLPREFIXVERSION="php8.5"
  PHPMODULEPREFIX="php/8.5"
  PHPMAJORVERSION="8"
  SQLITEVERSION="sqlite3"
  VMRELEASENUMBER="013.07u"
fi
# End of OS ID and version (2026-01-29)


# Hardware detection (2026-02-27)
FAMILY=""
UNAME=$(uname -a)
MODEL=$(cat /proc/cpuinfo | grep "Model" | awk -F': ' '{print $2}')
if [ -e /usr/local/games/docker.flag ] ; then
  FAMILY="VAP"
  TYPE="DOCKER"
elif [[ "${UNAME}" == *docker* ]]; then
    # Docker
    FAMILY="VAP"
    TYPE="DOCKER"
elif grep -q docker /proc/1/cgroup; then 
    FAMILY="VAP"
    TYPE="DOCKER"
elif grep -q docker /proc/self/cgroup; then 
    FAMILY="VAP"
    TYPE="DOCKER"
elif [ -f /.dockerenv ]; then
    FAMILY="VAP"
    TYPE="DOCKER"
elif [[ "${MODEL}" == *"Raspberry Pi 5"* ]]; then
    # Raspberry Pi 5
    FAMILY="RPI"
    TYPE="RP5"
elif [[ "${MODEL}" == *"Raspberry Pi 4"* ]]; then
    # Raspberry Pi 4
    FAMILY="RPI"
    TYPE="RP4"
elif [[ "${MODEL}" == *"Raspberry Pi 3 Model B Plus"* ]]; then
    # Raspberry Pi 3 B+
    FAMILY="RPI"
    TYPE="RP3+"
elif [[ "${MODEL}" == *"Raspberry Pi 3"* ]]; then
    # Raspberry Pi 3
    FAMILY="RPI"
    TYPE="RP3"
elif [[ "${MODEL}" == *"Raspberry Pi 2"* ]]; then
    # Raspberry Pi 2
    FAMILY="RPI"
    TYPE="RP2"
elif [[ "${MODEL}" == *"Raspberry Pi"* ]]; then
    # Raspberry Pi generic
    FAMILY="RPI"
    TYPE="RP"
elif [[ "${UNAME}" == *armv8* ]] || [[ "${UNAME}" == *aarch64* ]]; then
    HARDWARE=$(cat /proc/cpuinfo | grep "Hardware" | awk -F': ' '{print $2}')
    if [[ "${HARDWARE}" == *BCM27* ]] || [[ "${HARDWARE}" == *BCM28* ]]; then
        LSCPU=$(/usr/bin/lscpu | grep "CPU max MHz" | awk -F': ' '{print $2}')
        if [[ "${LSCPU}" == *1500* ]]; then
            # Raspberry Pi 4
            FAMILY="RPI"
            TYPE="RP4"
        elif [[ "${LSCPU}" == *1400* ]]; then
            # Raspberry Pi 3 B+
            FAMILY="RPI"
            TYPE="RP3+"
        else
            # Raspberry Pi 3
            FAMILY="RPI"
            TYPE="RP3"
        fi
    else
        FAMILY="ARM"
        TYPE="ARM"
    fi
elif [[ "${UNAME}" == *armv7l* ]]; then
    HARDWARE=$(cat /proc/cpuinfo | grep "Hardware" | awk -F': ' '{print $2}')
    if [[ "${HARDWARE}" == *BCM27* ]] || [[ "${HARDWARE}" == *BCM28* ]]; then
        LSCPU=$(/usr/bin/lscpu | grep "CPU max MHz" | awk -F': ' '{print $2}')
        if [[ "${LSCPU}" == *1500* ]]; then
            # Raspberry Pi 4
            FAMILY="RPI"
            TYPE="RP4"
        elif [[ "${LSCPU}" == *1400* ]]; then
            # Raspberry Pi 3 B+
            FAMILY="RPI"
            TYPE="RP3+"
        elif [[ "${LSCPU}" == *1200* ]]; then
            # Raspberry Pi 3
            FAMILY="RPI"
            TYPE="RP3"
        else
            # Raspberry Pi 2
            FAMILY="RPI"
            TYPE="RP2"
        fi
    else
        # Beaglebone Black or similar
        FAMILY="ARM"
        if [ -e /sys/class/leds/beaglebone:green:usr0/trigger ] ; then
            TYPE="BBB"
        else
            TYPE="ARM"
        fi
    fi
elif [[ "${UNAME}" == *armv6l* ]]; then
    # Raspberry Pi B/B+
    FAMILY="RPI"
    TYPE="RPI"
else
    # others (Virtual Appliance)
    FAMILY="VAP"
    TYPE="VA"
    DMIDECODE=$(dmidecode -s system-product-name)

    if systemd-detect-virt -q && dmidecode -s system-manufacturer 2>/dev/null | grep -qi "QEMU"; then
        TYPE="PX"
    elif [[ "${DMIDECODE}" == *VMware* ]]; then
        VMTOOLS=$(which vmtoolsd)
        if [[ "${VMTOOLS}" == *vmtoolsd* ]]; then
            TYPE="VM"
        else
            TYPE="VA"
        fi
    elif [[ "${DMIDECODE}" == *Virtual\ Machine* ]]; then
        TYPE="HV"
    elif [[ "${DMIDECODE}" == *VirtualBox* ]]; then
        TYPE="VB"
    fi
fi
# End of hardware detection (2026-02-27)


# Backend detection (2026-02-27)
if [ -e /usr/local/games/backend.mysql ] ; then
  BACKEND="mysql"
else
  BACKEND="files"
fi
# Docker backend is forced to be files for now
if [[ "${TYPE}" == "DOCKER" ]]; then
  BACKEND="files"
fi
# End of backend detection (2026-02-27)


if [[ "${FAMILY}" == "RPI" ]]; then
    # Kill all processes which are running with pi user
    ps -ef | grep pi | awk '{ print $2 }' | xargs kill -9 > /dev/null 2>&1

    # Remove the initial user named pi
    userdel -r pi > /dev/null 2>&1
fi


if [[ "${TYPE}" == "DOCKER" ]]; then
  touch /usr/local/bin/docker.flag
fi


if [[ "${TYPE}" != "DOCKER" ]]; then

  # Kill all processes which are running with debian user
  ps -ef | grep debian | awk '{ print $2 }' | xargs kill -9 > /dev/null 2>&1

  # Remove the demo user named debian
  userdel -r debian > /dev/null 2>&1


  # Remove multiotp.php crontab entries, if any
  sed -i '/.*multiotp.php.*/d' /etc/crontab

  #dmidecode -s system-product-name
  #VMware Virtual Platform
  #apt-get -y install open-vm-tools
  #apt-get -y remove open-vm-tools


  # Clean VM distribution
  # Stop Nginx
  if [ -e /etc/init.d/nginx ] ; then
      /etc/init.d/nginx stop
  else
      service nginx stop
  fi
  # Backup the plateform release
  if [ -e /etc/multiotp/config/vmrelease.ini ] ; then
      cp -f /etc/multiotp/config/vmrelease.ini /dev/shm/vmrelease.ini
  fi
  if [ -e /etc/multiotp/config/hwrelease.ini ] ; then
      cp -f /etc/multiotp/config/hwrelease.ini /dev/shm/hwrelease.ini
  fi

  # Stop Freeradius
  if [ -e /etc/init.d/freeradius ] ; then
      /etc/init.d/freeradius stop
  else
      service freeradius stop
  fi

  # Remove the start file for fake-hwclock
  rm -R /etc/*/*fake-hwclock > /dev/null 2>&1

  # Blacklist speaker support to avoid error during boot
  /bin/grep "pcspkr" /etc/modprobe.d/blacklist.conf > /dev/null 2>&1
  if [ $? != 0 ]; then
      echo 'blacklist pcspkr' >> /etc/modprobe.d/blacklist.conf
      echo 'blacklist snd_pcsp' >> /etc/modprobe.d/blacklist.conf
  fi

  # Blacklist i2c_piix4 support to avoid error during boot
  /bin/grep "i2c_piix4" /etc/modprobe.d/blacklist.conf > /dev/null 2>&1
  if [ $? != 0 ]; then
      echo 'blacklist i2c_piix4' >> /etc/modprobe.d/blacklist.conf
  fi

  # Blacklist nsc_ircc support to avoid error during boot
  /bin/grep "nsc_ircc" /etc/modprobe.d/blacklist.conf > /dev/null 2>&1
  if [ $? != 0 ]; then
      echo 'blacklist nsc_ircc' >> /etc/modprobe.d/blacklist.conf
  fi

  # Blacklist intel_rapl support to avoid error during boot (5.0.3.2)
  /bin/grep "intel_rapl" /etc/modprobe.d/blacklist.conf > /dev/null 2>&1
  if [ $? != 0 ]; then
      echo 'blacklist intel_rapl' >> /etc/modprobe.d/blacklist.conf
  fi

  if [[ "${FAMILY}" == "VAP" ]]; then
      update-initramfs -u -k all
  fi

  if [[ "${TYPE}" != "DOCKER" ]]; then
      # Since 5.0.3.1, fix iptable if necessary

      # autorizing PING
      iptables -A INPUT -p icmp -j ACCEPT > /dev/null 2>&1

      # authorized ports
      iptables -A INPUT -p tcp --dport 22 -j ACCEPT > /dev/null 2>&1
      iptables -A INPUT -p tcp --dport 80 -j ACCEPT > /dev/null 2>&1
      iptables -A INPUT -p udp --dport 161 -j ACCEPT > /dev/null 2>&1
      iptables -A INPUT -p tcp --dport 443 -j ACCEPT > /dev/null 2>&1
      iptables -A INPUT -p udp --dport 1812 -j ACCEPT > /dev/null 2>&1
      iptables -A INPUT -p udp --dport 1813 -j ACCEPT > /dev/null 2>&1

      # no firewall on the local loop (127.x.x.x)
      iptables -A INPUT -i lo -j ACCEPT > /dev/null 2>&1
      iptables -A OUTPUT -o lo -j ACCEPT > /dev/null 2>&1

      # existing connections receive their traffic
      iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT > /dev/null 2>&1

      # refused by default
      iptables -P INPUT DROP > /dev/null 2>&1

      iptables-save > /etc/iptables/rules.v4 > /dev/null 2>&1
      # ip6 can be swapped if not used
      ip6tables-save > /etc/iptables/rules.v6 > /dev/null 2>&1

      # Clean history and other files
      # Ideas: http://lonesysadmin.net/2013/03/26/preparing-linux-template-vms/
      /usr/sbin/logrotate -f /etc/logrotate.conf
      /bin/rm -f /var/log/*-???????? /var/log/*.gz
      /bin/rm -rf /tmp/*
      /bin/rm -rf /var/tmp/*
  fi

  /bin/rm -f ~root/.bash_history
  unset HISTFILE

  # Clean the history
  history -c


  # Docker must be mounted with existing configuration
  if [[ "${TYPE}" != "DOCKER" ]]; then
      rm -f /var/log/multiotp/*
      rm -f /etc/multiotp/config/*
      rm -f /etc/multiotp/devices/*
      rm -f /etc/multiotp/groups/*
      rm -f /etc/multiotp/tokens/*
      rm -f /etc/multiotp/touch/*
      rm -f /etc/multiotp/users/*
  fi

  if [ ! -e /etc/multiotp ] ; then
      mkdir /etc/multiotp
  fi
  if [ ! -e /etc/multiotp/config ] ; then
      mkdir /etc/multiotp/config
  fi


  if [ ! -e /etc/multiotp/config/multiotp.ini ] ; then
      # Touch config file to give the necessary right
      touch /etc/multiotp/config/multiotp.ini

      # Change various rights
      chmod 777 -R /etc/multiotp

      # Change some owners
      chown -R www-data:www-data /etc/multiotp

      echo Creating a new multiotp.ini file
      touch /etc/multiotp/config/multiotp.ini
      chmod 777 -R /etc/multiotp
      chown -R www-data:www-data /etc/multiotp
      echo multiotp-database-format-v3 > /etc/multiotp/config/multiotp.ini
      echo  >> /etc/multiotp/config/multiotp.ini
      echo log=1 >> /etc/multiotp/config/multiotp.ini

      #MySQL backbone configuration
      if [[ "${BACKEND}" == "mysql" ]]; then
          echo Add SQL configuration to multiotp.ini file
          sed -i '/^sql_server/d' /etc/multiotp/config/multiotp.ini
          echo sql_server=127.0.0.1 >> /etc/multiotp/config/multiotp.ini
          sed -i '/^sql_username/d' /etc/multiotp/config/multiotp.ini
          echo sql_username=multiotp >> /etc/multiotp/config/multiotp.ini
          sed -i '/^sql_password/d' /etc/multiotp/config/multiotp.ini
          echo sql_password=dfh45AReTZTxsdR >> /etc/multiotp/config/multiotp.ini
          sed -i '/^sql_database/d' /etc/multiotp/config/multiotp.ini
          echo sql_database=multiotp >> /etc/multiotp/config/multiotp.ini
          sed -i '/^backend_type/d' /etc/multiotp/config/multiotp.ini
          echo backend_type=mysql >> /etc/multiotp/config/multiotp.ini
          echo backend_type_validated=1 >> /etc/multiotp/config/multiotp.ini
      fi
  fi


  # Cleaning space, than
  #  VMware CLI: vmkfstools --punchzero  multiOTP-xxx.vmdk
  #  Hyper-V GUI: "Compact" option in the settings of the virtual machine
  #  VirtualBox CLI: VBoxManage modifyhd ?compact /path/to/multiOTP-xxx.vdi?
  if [[ "$1" == "zero" ]]; then
    echo "Zeroing disk space..."
    dd if=/dev/zero of=/zeroes bs=4096
    rm -f /zeroes
  fi

  if [ -e /dev/shm/vmrelease.ini ] ; then
      # Retrieve the version release
      cp -f /dev/shm/vmrelease.ini /etc/multiotp/config/vmrelease.ini
  fi
  if [ -e /dev/shm/hwrelease.ini ] ; then
      # Retrieve the version release
      cp -f /dev/shm/hwrelease.ini /etc/multiotp/config/hwrelease.ini
  fi


  if [[ "${TYPE}" != "DOCKER" ]]; then
    touch /etc/multiotp/certificates/multiotp.generic
    touch /etc/ssh/ssh.generic

    # Remove this file
    if [ -e ${BASH_SOURCE} ] ; then
        rm -f ${BASH_SOURCE}
    fi
  fi

  echo The device is now halted.

  #Stop the VM
  if [ -d /run/systemd/system ]; then
    systemctl poweroff
  else
    if [ -e /usr/sbin/shutdown ] ; then
      shutdown now -h &
    fi
  fi
  exit 0

else

  if [ -e /etc/init.d/freeradius ] ; then
    /etc/init.d/freeradius start
  else
    service freeradius start
  fi
  if [ -e /etc/init.d/nginx ] ; then
    /etc/init.d/nginx start
  else
    service nginx start
  fi
  
  # Stop NTP, update date/time, restart NTP and check configuration
  if [ "${OSVERSION}" -lt "12" ]; then
    if [ -e /etc/init.d/ntp ] ; then
        /etc/init.d/ntp stop
        /etc/init.d/ntp start
    else
        service ntp stop
        service ntp start
    fi

    ntpq -p
  else
    if [ -d /run/systemd/system ]; then
      systemctl restart systemd-timesyncd
    fi
  fi

  if [ -e /etc/init.d/${PHPFPM} ] ; then
    if [ -e /run/php ] ; then
      /etc/init.d/${PHPFPM} restart
    else
      /etc/init.d/${PHPFPM} start
    fi
  else
    if [ -e /run/php ] ; then
      service ${PHPFPM} restart
    else
      service ${PHPFPM} start
    fi
  fi
  
  # In DOCKER mode, populate the temporary folders with the config at the last startup
  # (if somebody want to mount externally the /etc/multiotp and /etc/freeradius somewhere else)
  if [ ! -e /var/multiotp-temp/log/freeradius ] ; then
    mkdir /var/multiotp-temp
    mkdir /var/multiotp-temp/etc
    mkdir /var/multiotp-temp/etc/multiotp
    mkdir /var/multiotp-temp/etc/freeradius
    mkdir /var/multiotp-temp/log
    mkdir /var/multiotp-temp/log/multiotp
    mkdir /var/multiotp-temp/log/freeradius
  fi

  cp -af /etc/multiotp/* /var/multiotp-temp/etc/multiotp
  cp -af /etc/freeradius/* /var/multiotp-temp/etc/freeradius
  cp -af /var/log/multiotp/* /var/multiotp-temp/log/multiotp
  cp -af /var/log/freeradius/* /var/multiotp-temp/log/freeradius

  # Keep container running
  while true;
    do sleep 30;
  done;

fi
