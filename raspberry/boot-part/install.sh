#!/bin/bash
##########################################################################
#
# @file   install.sh
# @brief  Installer file for an easy Raspberry Pi / VM / Docker implementation
#
# multiOTP package - Strong two-factor authentication open source package
# https://www.multiotp.net/
#
# The multiOTP package is the lightest package available that provides so many
# strong authentication functionalities and goodies, and best of all, for anyone
# that is interested about security issues, it's a fully open source solution!
#
# This package is the result of a *LOT* of work. If you are happy using this
# package, [Donation] are always welcome to support this project.
# Please check https://www.multiotp.net/ and you will find the magic button ;-)
#
# @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.8.7.0
# @date      2022-04-28
# @since     2013-11-29
# @copyright (c) 2013-2021 by SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
# 2021-09-14 5.8.3.0 SysCo/al VM version 011 support
#                             (Debian Bullseye 11.0, PHP 7.4, FreeRADIUS 3.0.21, Nginx 1.18.0)
# 2021-03-25 5.8.1.9 SysCo/al Fix some Nginx options
#                             Weak SSL ciphers disabled
# 2020-08-31 5.8.0.0 SysCo/al Raspberry Pi 4B support
#                             New unified distribution
#                             Debian Buster 10.5 support
#                             PHP 7.3 support
# 2019-10-23 5.6.1.5 SysCo/al Debian Buster support
#                             Debug mode
#                             Clean some unused commented old modifications
# 2019-03-29 5.4.1.8 SysCo/al Disable the dhcpcd service if we are in the Debian Stretch distribution
# 2019-01-30 5.4.1.7 SysCo/al Grouped configuration options
# 2019-01-25 5.4.1.6 SysCo/al Support any source path for the installation
# 2019-01-24 5.4.1.5 SysCo/al Fix for console support using VM with Debian Stretch
#                             Fix for legacy "eth0" name with Debian Stretch
#                             If any, clean specific NTP DHCP option at every reboot
#                             Fix rc.local (for console support), disabled by default in Stretch distribution
#                             Fix for some Stretch distributions
# 2019-01-07 5.4.1.1 SysCo/al VM upgraded to version 008 (Raspbian Stretch 9.6, PHP 7.x, FreeRADIUS 3.x)
#                             Raspberry Pi 3B+ support
# 2018-03-20 5.1.1.2 SysCo/al VM version 007 for Debian 8.x (PHP 5)
#                             Initial Docker support (Debian 8.x)
#                             OS version and ID detection
# 2017-06-05 5.0.4.7 SysCo/al VM upgraded to version 006
# 2013-11-29 4.0.9.0 SysCo/al Initial release
##########################################################################

# Default configuration options
DEBIAN_PASSWORD="raspberry"
PI_PASSWORD="raspberry"
ROOT_PASSWORD="raspberry"
MYSQL_PASSWORD="fJKGeztDF3456DvB"
MULTIOTP_SQL_PASSWORD="dfh45AReTZTxsdR"
SLAP_PASSWORD="rtzewrpiZRT753"
CERT_SUBJECT="/C=CH/ST=GPL/L=Open Source Edition/O=multiOTP/OU=strong authentication server/CN=multiOTP"
MULTIOTP_SH_SCRIPT="multiotp-service.sh"
HSTS_ENABLED="0"
RADIUS_SAMPLE_ENABLED="1"
SSH_PORT="22"
SSH_ROOT_LOGIN="1"
DEFAULT_IP="192.168.1.44"
REBOOT_AT_THE_END="1"

TEMPVERSION="@version   5.8.7.0"
MULTIOTPVERSION="$(echo -e "${TEMPVERSION:8}" | tr -d '[[:space:]]')"
IFS='.' read -ra MULTIOTPVERSIONARRAY <<< "$MULTIOTPVERSION"
MULTIOTPMAJORVERSION=${MULTIOTPVERSIONARRAY[0]}

if [[ "$2" == "debug" ]] || [[ "$3" == "debug" ]]; then
  DEBUGMODE="TRUE"
  echo "********************"
  echo "Debug mode activated"
  echo "********************"
  echo "********************"
else
  DEBUGMODE="FALSE"
fi

SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
SOURCEDIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"

# OS ID and version
# Architecture (for example x86_64)
OSID=$(cat /etc/os-release | grep "^ID=" | awk -F'=' '{print $2}')
OSVERSION=$(cat /etc/os-release | grep "VERSION_ID=" | awk -F'"' '{print $2}')
ARCHITECTURE=$(lscpu |grep "^Architecture" | awk -F':' '{print $2}' | awk '{$1=$1;print}')

BACKENDDB="mysql"
PHPFPM="php7.3-fpm"
PHPFPMSED="php\/php7.3-fpm"
PHPINSTALLPREFIX="php"
PHPINSTALLPREFIXVERSION="php7.3"
PHPMODULEPREFIX="php/7.3"
PHPMAJORVERSION="7"
SQLITEVERSION="sqlite3"
VMRELEASENUMBER="010"
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
fi


# Early docker detection
if grep -q docker /proc/1/cgroup; then 
    TYPE="DOCKER"
fi

if grep -q docker /proc/self/cgroup; then 
    TYPE="DOCKER"
fi

if [ -f /.dockerenv ]; then
    TYPE="DOCKER"
fi

UNAME=$(uname -a)
if [[ "${UNAME}" == *docker* ]]; then
    TYPE="DOCKER"
fi


if [[ "${TYPE}" != "DOCKER" ]]; then
    # apt-get install some basic packages
    apt-get -y install apt-utils
    apt-get -y install dialog
    apt-get -y install dmidecode
    apt-get -y install initramfs-tools
    apt-get -y install logrotate
    apt-get -y install nano
    apt-get -y install net-tools
    apt-get -y install ssh
    apt-get -y install wget
    if [[ "${TYPE}" == "HV" ]]; then
        apt-get -y install hyperv-daemons
        update-initramfs -u
    fi
fi


if [ $# -ge 1 ]; then
    BACKEND="$1"
    if [[ "${BACKEND}" != "mysql" ]] && [[ "$2" != "mysql" ]]; then
        BACKEND="files"
    else
        BACKEND="mysql"
    fi
else
    BACKEND="files"
fi


# Docker backend is forced to be files
if [[ "${TYPE}" == "DOCKER" ]]; then
    BACKEND="files"
fi


# Hardware detection
FAMILY=""
UNAME=$(uname -a)
if [[ "${UNAME}" == *armv8* ]]; then
    HARDWARE=$(cat /proc/cpuinfo | grep "Hardware" | awk -F': ' '{print $2}')
    if [[ "${HARDWARE}" == *BCM27* ]]; then
        # Raspberry Pi 3 B
        FAMILY="RPI"
        TYPE="RP3"
    elif [[ "${HARDWARE}" == *BCM28* ]]; then
        # Raspberry Pi 3 B+
        FAMILY="RPI"
        TYPE="RP3B+"
    else
        # Nothing else yet !
        FAMILY="RPI"
        TYPE="RP3"
    fi
elif [[ "${UNAME}" == *armv7l* ]]; then
    HARDWARE=$(cat /proc/cpuinfo | grep "Hardware" | awk -F': ' '{print $2}')
    if [[ "${HARDWARE}" == *BCM27* ]]; then
        LSCPU=$(/usr/bin/lscpu | grep "CPU max MHz" | awk -F': ' '{print $2}')
        if [[ "${LSCPU}" == *1200* ]]; then
            # Raspberry Pi 3
            FAMILY="RPI"
            TYPE="RP3"
        else
            # Raspberry Pi 2
            FAMILY="RPI"
            TYPE="RP2"
        fi
    elif [[ "${HARDWARE}" == *BCM28* ]]; then
        # Raspberry Pi 3 B+
        FAMILY="RPI"
        TYPE="RP3B+"
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
else
    # others (Virtual Appliance)
    FAMILY="VAP"
    TYPE="VA"
    DMIDECODE=$(dmidecode -s system-product-name)
    if [[ "${DMIDECODE}" == *VMware* ]]; then
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


# Stop some services if they are existing
if [ -e /etc/init.d/nginx ] ; then
    /etc/init.d/nginx stop
else
    service nginx stop
fi
if [ -e /etc/init.d/freeradius ] ; then
    /etc/init.d/freeradius stop
else
    service freeradius stop
fi
if [ -e /etc/init.d/xrdp ] ; then
    /etc/init.d/xrdp stop
else
    service xrdp stop
fi
if [ -e /etc/init.d/xrdp ] ; then
    /etc/init.d/lightdm stop
else
    service lightdm stop
fi


# Stop some services if they are existing
if [ -e /etc/init.d/nginx ] ; then
    /etc/init.d/nginx stop
else
    service nginx stop
fi
if [ -e /etc/init.d/freeradius ] ; then
    /etc/init.d/freeradius stop
else
    service freeradius stop
fi
if [ -e /etc/init.d/xrdp ] ; then
    /etc/init.d/xrdp stop
else
    service xrdp stop
fi
if [ -e /etc/init.d/xrdp ] ; then
    /etc/init.d/lightdm stop
else
    service lightdm stop
fi


if [[ "${TYPE}" != "DOCKER" ]]; then
    # Purge unused packages
    apt-get -y purge *-ttf
    apt-get -y purge alsa*
    apt-get -y purge dbus-x11
    apt-get -y purge desktop-base
    apt-get -y purge desktop-file-utils
    apt-get -y purge dillo
    apt-get -y purge galculator
    apt-get -y purge gnome-themes-standard
    apt-get -y purge gpicview
    apt-get -y purge gtk2-engines
    apt-get -y purge hicolor-icon-theme
    apt-get -y purge leafpad
    apt-get -y purge midori
    apt-get -y purge netsurf-gtk
    apt-get -y purge omxplayer
    apt-get -y purge openbox
    apt-get -y purge penguinspuzzle
    apt-get -y purge python-pygame
    apt-get -y purge raspberrypi-artwork
    apt-get -y purge scratch
    apt-get -y purge ttf*
    apt-get -y purge wpagui
    apt-get -y purge x11-common
    apt-get -y purge xarchiver
    apt-get -y purge xauth
    apt-get -y purge xdg-utils
    apt-get -y purge xpdf

    # Remove some unused packets

    # Remove Apache, as we will work with Nginx
    apt-get -y autoremove apache2*

    apt-get -y autoremove avahi*
    apt-get -y autoremove exim4*
    apt-get -y autoremove fontconfig*
    apt-get -y autoremove libmenu-cache1
    apt-get -y autoremove lightdm*
    apt-get -y autoremove lx*

    # Remove ntpdate as ntp will do the job
    apt-get -y autoremove ntpdate

    # Remove X11 related packages as we don't need any X Window System
    apt-get -y autoremove x11-common

    apt-get -y autoremove xrdp*
fi


# For Nano-computer, remove various unused packages
if [[ "${FAMILY}" == "ARM" ]] || [[ "${FAMILY}" == "RPI" ]]; then
    apt-get -y autoremove ${BACKENDDB}-server
    apt-get -y autoremove ${BACKENDDB}-common
    apt-get -y autoremove ${PHPINSTALLPREFIX}-mysql
fi


# Cleaning dpkg
dpkg --configure -a


if [[ "${TYPE}" != "DOCKER" ]]; then
    # apt-get update and upgrade
    apt-get -y update
    apt-get -y upgrade

    # Prepare automatic answers for IP table
    echo iptables-persistent iptables-persistent/autosave_v4 boolean false | debconf-set-selections
    echo iptables-persistent iptables-persistent/autosave_v6 boolean false | debconf-set-selections

    # apt-get additional packages installation
    apt-get -y install apache2-utils
    apt-get -y install apt-offline
    apt-get -y install apt-zip
    apt-get -y install build-essential
    apt-get -y install bzip2
    apt-get -y install dselect
    apt-get -y install freeradius
    apt-get -y install iptables-persistent
    apt-get -y install insserv
    apt-get -y install ldap-utils
    apt-get -y install libbz2-dev
    apt-get -y install nginx-extras
    apt-get -y install ntp
    apt-get -y install p7zip-full
    apt-get -y install php-pear
    apt-get -y install ${PHPINSTALLPREFIX}-bcmath
    apt-get -y install ${PHPINSTALLPREFIX}-cgi
    apt-get -y install ${PHPINSTALLPREFIX}-dev
    apt-get -y install ${PHPINSTALLPREFIX}-fpm
    apt-get -y install ${PHPINSTALLPREFIX}-gd
    apt-get -y install ${PHPINSTALLPREFIX}-gmp
    apt-get -y install ${PHPINSTALLPREFIX}-ldap
    apt-get -y install ${PHPINSTALLPREFIXVERSION}-${SQLITEVERSION}
    
    # mcrypt is removed in PHP 7.2 (Debian 10 integrates PHP 7.3, Debian 11 integrates PHP 7.4)
    if [[ "${OSVERSION}" != "10" ]] && [[ "${OSVERSION}" != "11" ]]; then
        apt-get -y install ${PHPINSTALLPREFIX}-mcrypt
    fi
fi

# Add mbstring support for PHP 7 (no more embedded like in PHP 5)
if [[ "${PHPMAJORVERSION}" == "7" ]]; then
    apt-get -y install ${PHPINSTALLPREFIXVERSION}-mbstring
fi


if [[ "${TYPE}" != "DOCKER" ]]; then
    # Since 5.0.4.4 (hardware platform 006)
    echo slapd slapd/internal/adminpw password ${SLAP_PASSWORD} | debconf-set-selections
    echo slapd slapd/internal/generated_adminpw password ${SLAP_PASSWORD} | debconf-set-selections
    echo slapd slapd/password2 password ${SLAP_PASSWORD} | debconf-set-selections
    echo slapd slapd/password1 password ${SLAP_PASSWORD} | debconf-set-selections
    apt-get -y install slapd

    # Since 5.0.4.4 (hardware platform 006)
    apt-get -y install snmp
    apt-get -y install snmpd
    # https://geekpeek.net/extend-snmp-run-bash-scripts-via-snmp/
    # http://net-snmp.sourceforge.net/wiki/index.php/Tut:Extending_snmpd_using_shell_scripts
    # http://blog.gamb.fr/index.php?post/2012/10/23/Installation-et-configuration-de-snmpd

    apt-get -y install ${SQLITEVERSION}
    apt-get -y install subversion
    apt-get -y install sudo
fi


if [[ "${BACKEND}" == "files" ]]; then
	echo "Files backend"
elif [[ "${FAMILY}" == "VAP" ]] || [[ "${BACKEND}" == "mysql" ]]; then
	# Install some additional packages on Virtual Appliance
	# Prepare automatic answers for MariaDB / MySQL
	echo "MySQL backend"
    
    if [[ "${BACKENDDB}" == "mariadb" ]]; then
        echo mariadb-server-10.3 mariadb-server/root_password password ${MYSQL_PASSWORD} | debconf-set-selections
        echo mariadb-server-10.3 mariadb-server/root_password_again password ${MYSQL_PASSWORD} | debconf-set-selections
    else
	    echo mysql-server-5.1 mysql-server/root_password password ${MYSQL_PASSWORD} | debconf-set-selections
	    echo mysql-server-5.1 mysql-server/root_password_again password ${MYSQL_PASSWORD} | debconf-set-selections
    fi
    

	apt-get -y install ${BACKENDDB}-server
	apt-get -y install ${BACKENDDB}-common
	apt-get -y install ${PHPINSTALLPREFIX}-mysql
fi


if [[ "${FAMILY}" == "ARM" ]] || [[ "${FAMILY}" == "RPI" ]]; then
    apt-get -y install device-tree-compiler
    apt-get -y install fake-hwclock
    apt-get -y install fbi
    apt-get -y install i2c-tools

    if [ ! -e /usr/local/bin/dtc ] ; then
        if [ ! -e /tmp/dtc ] ; then
            mkdir /tmp/dtc
        fi
        cd /tmp/dtc
        wget -c https://raw.github.com/RobertCNelson/tools/master/pkgs/dtc.sh
        chmod +x dtc.sh
        ./dtc.sh    
        # Installed in /usr/local/bin/dtc
        # /usr/local/bin/dtc -O dtb -o BB-RTC-AND-RESET-0101.dtbo -b -o -@ BB-RTC-AND-RESET-0101.dts
        # cp BB-RTC-AND-RESET-0101.dtbo /lib/firmware
        cd /tmp
        rm -f -R /tmp/dtc
    fi
fi


if [[ "${TYPE}" != "DOCKER" ]]; then
    # apt-get update and upgrade (final one, at the end of the whole installation process)
    apt-get -y update
    apt-get -y upgrade

    # Clean unused packages
    apt-get -y autoremove

    # More cleaning
    apt-get autoclean
    # apt-get -y install localepurge

    # List of all installed packages
    # dpkg -l


    # Clean all unnecessary files
    # localepurge
    apt-get clean
fi


# Change the password of root account
echo "root:${ROOT_PASSWORD}"|chpasswd

# Change the password of pi account
echo "pi:${PI_PASSWORD}"|chpasswd

# Change the password of debian account
echo "debian:${DEBIAN_PASSWORD}"|chpasswd


# Create multiOTP folders
mkdir /etc/multiotp
mkdir /etc/multiotp/config
mkdir /etc/multiotp/devices
mkdir /etc/multiotp/groups
mkdir /etc/multiotp/tokens
mkdir /etc/multiotp/users
mkdir /usr/local/bin/multiotp
mkdir /usr/local/bin/multiotp/scripts
mkdir /var/log/multiotp


# Copy the preconfigured files for multiOTP
cp -f -R ${SOURCEDIR}/multiotp-tree/* /


if [[ "${DEBUGMODE}" != "TRUE" ]]; then
    rm -f -R ${SOURCEDIR}/multiotp-tree
fi


# 5.4.1.2
# For Strech/Buster/Bullseye, reactivate traditional eth0 support (except for Raspbian)
# https://unix.stackexchange.com/questions/396382/how-can-i-show-the-old-eth0-names-and-also-rename-network-interfaces-in-debian-9
if [[ "${OSID}" == "debian" ]] && ( [[ "${OSVERSION}" == "9" ]] || [[ "${OSVERSION}" == "10" ]] || [[ "${OSVERSION}" == "11" ]] ); then
    IFNAME=$(ifconfig | grep -o -E '(^e[a-zA-Z0-9]*)')
    if [[ "${IFNAME}" != "eth0" ]]; then
        sed -i 's/.*GRUB_CMDLINE_LINUX=.*/GRUB_CMDLINE_LINUX="net.ifnames=0 biosdevname=0"/' /etc/default/grub
        update-grub2
    fi
fi


# 5.4.1.8 Disable the dhcpcd service if we are in the Debian Stretch/Buster/Bullseye distribution
if [[ "${OSVERSION}" == "9" ]] || [[ "${OSVERSION}" == "10" ]] || [[ "${OSVERSION}" == "11" ]]; then
    systemctl stop dhcpcd.service
    systemctl disable dhcpcd.service
fi


if [[ "${FAMILY}" == "VAP" ]]; then
    # No config.txt file for Virtual Appliance
    rm -f /boot/config.txt
fi

if [[ "${FAMILY}" == "VAP" ]] || [[ "${FAMILY}" == "RPI" ]]; then
    # IP address definition
    echo auto lo > /etc/network/interfaces
    echo  >> /etc/network/interfaces
    echo iface lo inet loopback >> /etc/network/interfaces
    echo allow-hotplug eth0 >> /etc/network/interfaces
    echo iface eth0 inet static >> /etc/network/interfaces
    echo address ${DEFAULT_IP} >> /etc/network/interfaces
    echo netmask 255.255.255.0 >> /etc/network/interfaces
    echo network 192.168.1.0 >> /etc/network/interfaces
    echo gateway 192.168.1.1 >> /etc/network/interfaces
fi


# If not on a device, set the VM release file
if [[ "${FAMILY}" == "VAP" ]]; then
    echo ${TYPE}-${VMRELEASENUMBER} > /etc/multiotp/config/vmrelease.ini
else
    echo ${FAMILY}-${VMRELEASENUMBER} > /etc/multiotp/config/hwrelease.ini
fi


# Create a generic SSL certificate
mkdir /etc/multiotp/certificates
rm -f /etc/multiotp/certificates/*
openssl genrsa -out /etc/multiotp/certificates/multiotp.key 2048 > /dev/null 2>&1
openssl req -new -key /etc/multiotp/certificates/multiotp.key -out /etc/multiotp/certificates/multiotp.csr -subj "${CERT_SUBJECT}" > /dev/null 2>&1
openssl x509 -req -days 7305 -in /etc/multiotp/certificates/multiotp.csr -signkey /etc/multiotp/certificates/multiotp.key -out /etc/multiotp/certificates/multiotp.crt > /dev/null 2>&1
touch /etc/multiotp/certificates/multiotp.generic


# Set some multiOTP options if never used
if [ ! -e /etc/multiotp/config/multiotp.ini ] ; then
    echo Creating a new multiotp.ini file
    touch /etc/multiotp/config/multiotp.ini
    chmod 777 -R /etc/multiotp
    chmod 777 -R /usr/local/bin/multiotp
    chown -R www-data:www-data /etc/multiotp
    chown -R www-data:www-data /usr/local/bin/multiotp
    echo multiotp-database-format-v3 > /etc/multiotp/config/multiotp.ini
    echo  >> /etc/multiotp/config/multiotp.ini
    echo log=1 >> /etc/multiotp/config/multiotp.ini

    #MySQL backbone configuration
    if [[ "${BACKEND}" == "mysql" ]]; then
        echo "Add SQL configuration to multiotp.ini file"
        /usr/bin/mysql -u root -p${MYSQL_PASSWORD} -e "DROP DATABASE IF EXISTS multiotp; CREATE DATABASE multiotp; GRANT ALL PRIVILEGES ON multiotp.* TO multiotp@localhost IDENTIFIED BY '${MULTIOTP_SQL_PASSWORD}';"
        sed -i '/^sql_server/d' /etc/multiotp/config/multiotp.ini
        echo sql_server=127.0.0.1 >> /etc/multiotp/config/multiotp.ini
        sed -i '/^sql_username/d' /etc/multiotp/config/multiotp.ini
        echo sql_username=multiotp >> /etc/multiotp/config/multiotp.ini
        sed -i '/^sql_password/d' /etc/multiotp/config/multiotp.ini
        echo sql_password=${MULTIOTP_SQL_PASSWORD} >> /etc/multiotp/config/multiotp.ini
        sed -i '/^sql_database/d' /etc/multiotp/config/multiotp.ini
        echo sql_database=multiotp >> /etc/multiotp/config/multiotp.ini
        sed -i '/^backend_type/d' /etc/multiotp/config/multiotp.ini
        echo backend_type=mysql >> /etc/multiotp/config/multiotp.ini
        /usr/local/bin/multiotp/multiotp.php -initialize-backend
    else
        echo "Drop MySQL database if exists"
        if [ -e /usr/bin/mysql ] ; then
            /usr/bin/mysql -u root -p${MYSQL_PASSWORD} -e "DROP DATABASE IF EXISTS multiotp;"
        fi
    fi
fi


# Touch some files to give them the necessary rights
if [ -e /etc/freeradius/3.0/ ] ; then
    touch /etc/freeradius/3.0/clients.conf.bkp
else
    touch /etc/freeradius/clients.conf.bkp
fi
touch /var/log/multiotp/multiotp.log
touch /etc/multiotp/config/multiotp.ini


# Change various rights
if [ -e /etc/freeradius/3.0/ ] ; then
    chmod g+rw   /etc/freeradius/3.0/clients.conf
    chmod 777    /etc/freeradius/3.0/clients.conf.bkp
    chmod g+rw   /etc/freeradius/3.0/users
else
    chmod g+rw   /etc/freeradius/clients.conf
    chmod 777    /etc/freeradius/clients.conf.bkp
    chmod g+rw   /etc/freeradius/users
fi

chmod 666    /etc/hostname
chmod 666    /etc/hosts
chmod 755    /etc/init.d/multiotp
chmod 644    /etc/logrotate.d/multiotp
chmod 777 -R /etc/multiotp
chmod 440    /etc/sudoers.d/www-data-authorized
chmod 777 -R /usr/local/bin/multiotp
chmod 777 -R /var/log/multiotp
chmod 755 -R /var/www


# Change some owners
chown -R www-data:www-data /etc/multiotp
chown -R www-data:www-data /usr/local/bin/multiotp
chown -R www-data:www-data /var/log/multiotp
chown -R www-data:www-data /var/www
chown root:root /etc/logrotate.d/multiotp
chown root:root /etc/sudoers.d/www-data-authorized


# Since 5.8.1.9, Disable CBC Ciphers and weak MAC Algorithms for SSH
sed -i '/^Ciphers/d' /etc/ssh/sshd_config
sed -i '/^MACs/d' /etc/ssh/sshd_config
echo Ciphers aes256-ctr,aes192-ctr,aes128-ctr >> /etc/ssh/sshd_config
echo MACs hmac-sha1,umac-64@openssh.com >> /etc/ssh/sshd_config

if [ -e /etc/init.d/ssh ] ; then
    /etc/init.d/ssh restart
else
    service ssh restart
fi


# Adapting nginx configuration
rm -f /etc/nginx/sites-enabled/default
ln -s ../sites-available/multiotp /etc/nginx/sites-enabled/multiotp
ln -s ../sites-available/multiotp-proxy /etc/nginx/sites-enabled/multiotp-proxy
sed -i 's/# multi_accept on;/multi_accept on;/' /etc/nginx/nginx.conf

# Remove SSLv3 protocol if needed due to the POODLE attack (CVE-2014-3566) (replaced by the next one)
# sed -i 's/SSLv3 //' /etc/nginx/sites-available/multiotp

# Since 5.0.3.1, TLSv1.2 first
# Since 5.8.1.9, TLSv1.1 and TLSv1 are now removed
sed -i 's/ssl_protocols.*/ssl_protocols TLSv1.2;/' /etc/nginx/sites-available/multiotp

# Since 5.0.2.7, remove RC4 cipher if needed (replaced by the next one)
# sed -i 's/:RC4:/:!RC4:/' /etc/nginx/sites-available/multiotp

# Since 5.0.2.7, change the whole cipher module if needed (checked on https://www.ssllabs.com/))
# Since 5.8.1.9, TLS_ECDHE_RSA_WITH_3DES_EDE_CBC_SHA REMOVED, !EXPORT40 to !EXPORT, !SEED and !3DES added
sed -i 's/ssl_ciphers.*/ssl_ciphers TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA:TLS_ECDHE_RSA_WITH_AES_128_CBC_SHA:ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDH-RSA-AES256-SHA384:ECDH-ECDSA-AES256-SHA384:ALL:!RC4:HIGH:!IDEA:!MD5:!aNULL:!eNULL:!EDH:!SSLv2:!ADH:!EXPORT:!EXP:!LOW:!ADH:!AECDH:!DSS:@STRENGTH:!SEED:!3DES;/' /etc/nginx/sites-available/multiotp

if [[ "${HSTS_ENABLED}" == "1" ]] ; then
    # https://www.ssllabs.com/downloads/SSL_Server_Rating_Guide.pdf
    sed -i '/Strict-Transport-Security/d' /etc/nginx/sites-available/multiotp
    sed -i "/server_tokens/a \    add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains';" /etc/nginx/sites-available/multiotp
fi

# Update Nginx configuration if needed for PHP FPM
sed -i "s/run\/php5-fpm.sock;/run\/${PHPFPMSED}.sock;/" /etc/nginx/sites-available/multiotp
sed -i "s/run\/php5-fpm.sock;/run\/${PHPFPMSED}.sock;/" /etc/nginx/sites-available/multiotp-proxy

# Since 5.4.1.1, increase upload size
# https://www.ssllabs.com/downloads/SSL_Server_Rating_Guide.pdf
sed -i '/client_max_body_size/d' /etc/nginx/sites-available/multiotp
sed -i '/server {/a \    client_max_body_size 1000M;' /etc/nginx/sites-available/multiotp

# Since 5.8.0.0, cookie hardening (proxy_cookie_path)
sed -i '/proxy_cookie_path/d' /etc/nginx/sites-available/multiotp
# Since 5.8.1.9, cookie hardening is done in the application level
# sed -i '/server_tokens/a \    proxy_cookie_path / "/; HTTPOnly";' /etc/nginx/sites-available/multiotp


if [ -e /etc/init.d/nginx ] ; then
    /etc/init.d/nginx restart
else
    service nginx restart
fi


# Since 5.0.3.4
# Adapting general PHP and FPM configuration (timeout)
if [ -e /etc/${PHPMODULEPREFIX}/cgi/php.ini ] ; then
    sed -i 's/.*max_execution_time.*/max_execution_time = 86400/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/cli/php.ini ] ; then
    sed -i 's/.*max_execution_time.*/max_execution_time = 86400/' /etc/${PHPMODULEPREFIX}/cli/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/fpm/php.ini ] ; then
    sed -i 's/.*max_execution_time.*/max_execution_time = 86400/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/fpm/pool.d/www.conf ] ; then
    sed -i 's/.*request_terminate_timeout.*/request_terminate_timeout = 0/' /etc/${PHPMODULEPREFIX}/fpm/pool.d/www.conf
fi


# Adapting general PHP configuration for Virtual Appliance
if [[ "${FAMILY}" == "VAP" ]]; then
    if [ -e /etc/${PHPMODULEPREFIX}/cgi/php.ini ] ; then
        sed -i 's/.*memory_limit.*/memory_limit = 1024M/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    fi
    if [ -e /etc/${PHPMODULEPREFIX}/cli/php.ini ] ; then
        sed -i 's/.*memory_limit.*/memory_limit = 1024M/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    fi
    if [ -e /etc/${PHPMODULEPREFIX}/fpm/php.ini ] ; then
        sed -i 's/.*memory_limit.*/memory_limit = 1024M/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    fi
fi


# Adapting OPcache configuration
# http://php.net/manual/opcache.configuration.php
if [ -e /etc/${PHPMODULEPREFIX}/cgi/php.ini ] ; then
    # sed -i '/^opcache.enable=/d' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    # sed -i '1i opcache.enable=1' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.enable=.*/opcache.enable=1/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.enable_cli=.*/opcache.enable_cli=1/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.memory_consumption=.*/opcache.memory_consumption=128/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.interned_strings_buffer=.*/opcache.interned_strings_buffer=8/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.max_file_size=.*/opcache.max_file_size=0/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.fast_shutdown=.*/opcache.fast_shutdown=1/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.validate_timestamps=.*/opcache.validate_timestamps=0/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*opcache.revalidate_freq=.*/opcache.revalidate_freq=60/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/cli/php.ini ] ; then
    sed -i 's/.*opcache.enable=.*/opcache.enable=1/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.enable_cli=.*/opcache.enable_cli=1/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.memory_consumption=.*/opcache.memory_consumption=128/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.interned_strings_buffer=.*/opcache.interned_strings_buffer=8/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.max_file_size=.*/opcache.max_file_size=0/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.fast_shutdown=.*/opcache.fast_shutdown=1/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.validate_timestamps=.*/opcache.validate_timestamps=0/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*opcache.revalidate_freq=.*/opcache.revalidate_freq=60/' /etc/${PHPMODULEPREFIX}/cli/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/fpm/php.ini ] ; then
    sed -i 's/.*opcache.enable=.*/opcache.enable=1/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.enable_cli=.*/opcache.enable_cli=1/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.memory_consumption=.*/opcache.memory_consumption=128/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.interned_strings_buffer=.*/opcache.interned_strings_buffer=8/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.max_file_size=.*/opcache.max_file_size=0/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.fast_shutdown=.*/opcache.fast_shutdown=1/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.validate_timestamps=.*/opcache.validate_timestamps=0/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*opcache.revalidate_freq=.*/opcache.revalidate_freq=60/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
fi


#Reconfigure PHP max size options
if [ -e /etc/${PHPMODULEPREFIX}/cgi/php.ini ] ; then
    sed -i 's/.*upload_max_filesize.*/upload_max_filesize=400M/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
    sed -i 's/.*post_max_size.*/post_max_size=800M/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/cli/php.ini ] ; then
    sed -i 's/.*upload_max_filesize.*/upload_max_filesize=400M/' /etc/${PHPMODULEPREFIX}/cli/php.ini
    sed -i 's/.*post_max_size.*/post_max_size=800M/' /etc/${PHPMODULEPREFIX}/cli/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/fpm/php.ini ] ; then
    sed -i 's/.*upload_max_filesize.*/upload_max_filesize=400M/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
    sed -i 's/.*post_max_size.*/post_max_size=800M/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
fi


#Secure cookie (since 5.8.0.0)
if [ -e /etc/${PHPMODULEPREFIX}/cgi/php.ini ] ; then
    sed -i 's/.*session.cookie_httponly.*/session.cookie_httponly = 1/' /etc/${PHPMODULEPREFIX}/cgi/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/cli/php.ini ] ; then
    sed -i 's/.*session.cookie_httponly.*/session.cookie_httponly = 1/' /etc/${PHPMODULEPREFIX}/cli/php.ini
fi
if [ -e /etc/${PHPMODULEPREFIX}/fpm/php.ini ] ; then
    sed -i 's/.*session.cookie_httponly.*/session.cookie_httponly = 1/' /etc/${PHPMODULEPREFIX}/fpm/php.ini
fi


# Adapting php-apc configuration (backward compatibility)
if [ -e /etc/${PHPMODULEPREFIX}/conf.d/20-apc.ini ] ; then
  sed -i '/^apc.shm_size=/d' /etc/${PHPMODULEPREFIX}/conf.d/20-apc.ini
  sed -i '/^apc.max_file_size=/d' /etc/${PHPMODULEPREFIX}/conf.d/20-apc.ini
  sed -i '1i apc.shm_size=48M' /etc/${PHPMODULEPREFIX}/conf.d/20-apc.ini
  sed -i '1i apc.max_file_size=48M' /etc/${PHPMODULEPREFIX}/conf.d/20-apc.ini
fi


if [ -e /etc/init.d/${PHPFPM} ] ; then
    /etc/init.d/${PHPFPM} restart
else
    service ${PHPFPM} restart
fi


# Since 5.0.3.4
# // Optimize performances
# Temporary, either of the following commands:
# sysctl -w vm.swappiness=1
# echo 1 >/proc/sys/vm/swappiness
# Check: cat /proc/sys/vm/swappiness
echo >> /etc/sysctl.conf
sed -i '/^vm.swappiness/d' /etc/sysctl.conf
echo vm.swappiness = 1 >> /etc/sysctl.conf
sed -i '/^vm.vfs_cache_pressure/d' /etc/sysctl.conf
echo vm.vfs_cache_pressure = 50 >> /etc/sysctl.conf

if [[ "${FAMILY}" == "RPI" ]]; then
    # // Optimize performances for Raspberry Pi
    # https://lonesysadmin.net/2013/12/22/better-linux-disk-caching-performance-vm-dirty_ratio/
    # sysctl -a | grep dirty
    # http://www.databasesql.info/article/9727204867/
    sed -i '/^vm.dirty_background_ratio/d' /etc/sysctl.conf
    echo vm.dirty_background_ratio = 5 >> /etc/sysctl.conf
    sed -i '/^vm.dirty_ratio/d' /etc/sysctl.conf
    echo vm.dirty_ratio = 70 >> /etc/sysctl.conf
fi


# Since 5.8.1.9, fix TCP timestamps vulnerability 
sed -i '/^net.ipv4.tcp_timestamps/d' /etc/sysctl.conf
echo net.ipv4.tcp_timestamps = 0 >> /etc/sysctl.conf


# If any, clean DHCP option for NTP
# http://support.ntp.org/bin/view/Support/ConfiguringNTP#Section_6.12
if [ -e /var/lib/ntp/ntp.conf.dhcp ] ; then
    rm -f /var/lib/ntp/ntp.conf.dhcp
fi


# Timezone definition
echo "Europe/Zurich" > /etc/timezone
rm -f /etc/localtime && cp -f /usr/share/zoneinfo/Europe/Zurich /etc/localtime


# Stop NTP, update date/time, restart NTP and check configuration
if [ -e /etc/init.d/ntp ] ; then
    /etc/init.d/ntp stop
    /etc/init.d/ntp start
else
    service ntp stop
    service ntp start
fi

ntpq -p
date -R
# date --set="2018-07-06 05:04:03"


if [[ "${FAMILY}" == "RPI" ]]; then
    /bin/grep "multiOTP - beginning" /boot/config.txt > /dev/null 2>&1
    if [ $? != 0 ]; then
        sed -i '1i #MULTIOTP#' /boot/config.txt
        sed -i '/#MULTIOTP#/i ################################################' /boot/config.txt
        sed -i '/#MULTIOTP#/i # multiOTP - beginning of specific configuration' /boot/config.txt
        sed -i '/#MULTIOTP#/i #' /boot/config.txt
        sed -i '/dtparam=i2c_arm=/d' /boot/config.txt
        sed -i '/#MULTIOTP#/i Enable I2C (for RTC clock support)' /boot/config.txt
        sed -i '/#MULTIOTP#/i dtparam=i2c_arm=on' /boot/config.txt
        sed -i '/#MULTIOTP#/i #' /boot/config.txt
        sed -i '/gpu_mem=/d' /boot/config.txt
        sed -i '/#MULTIOTP#/i # GPU memory in megabyte. Sets the memory split between the ARM and GPU' /boot/config.txt
        sed -i '/#MULTIOTP#/i # ARM gets the remaining memory. Min 16. Default 64' /boot/config.txt
        sed -i '/#MULTIOTP#/i gpu_mem=16' /boot/config.txt
        sed -i '/#MULTIOTP#/i #' /boot/config.txt
        sed -i 's/.*dtparam=audio=.*/# dtparam=audio=on/' /boot/config.txt
        sed -i '/#MULTIOTP#/i # Disable audio (loads snd_bcm2835)' /boot/config.txt
        sed -i '/#MULTIOTP#/i dtparam=audio=off' /boot/config.txt
        sed -i '/#MULTIOTP#/i #' /boot/config.txt
        sed -i '/dtparam=act_led_trigger=/d' /boot/config.txt
        sed -i '/#MULTIOTP#/i # Enable heartbeat' /boot/config.txt
        sed -i '/#MULTIOTP#/i dtparam=act_led_trigger=heartbeat' /boot/config.txt
        # RTC clock
        # https://afterthoughtsoftware.com/products/rasclock
        # https://www.raspberrypi.org/forums/viewtopic.php?f=28&t=97314
        # dtoverlay=i2c-rtc,<model> / <model> is one of: ds1307, ds3231, pcf2127, pcf8523, pcf8563
        if [ -e /boot/overlays/i2c-rtc-overlay.dtb ] ; then
            sed -i '/#MULTIOTP#/i #' /boot/config.txt
            sed -i '/dtoverlay.*pcf2127/d' /boot/config.txt
            sed -i '/#MULTIOTP#/i # Enable Afterthought Software RasClock (and other PCF212x compatible RTC clock)' /boot/config.txt
            sed -i '/#MULTIOTP#/i dtoverlay=i2c-rtc,pcf2127' /boot/config.txt
            sed -i '/#MULTIOTP#/i #' /boot/config.txt
            sed -i '/dtoverlay.*ds1307/d' /boot/config.txt
            sed -i '/#MULTIOTP#/i # Enable CJE Micro RTC clock module (and other DSxxxx compatible RTC clock)' /boot/config.txt
            sed -i '/#MULTIOTP#/i dtoverlay=i2c-rtc,ds1307' /boot/config.txt
            # alternate insert at the end of the file
            # sed -i '/dtoverlay.*ds1307/d' /boot/config.txt
            # sed -i -e '$adtoverlay=i2c-rtc,ds1307' /boot/config.txt
        elif [ -e /boot/overlays/pcf2127-rtc-overlay.dtb ] ; then
            sed -i '/#MULTIOTP#/i #' /boot/config.txt
            sed -i '/dtoverlay=pcf2127-rtc/d' /boot/config.txt
            sed -i '/#MULTIOTP#/i # Enable Afterthought Software RasClock (and other PCF212x compatible RTC clock)' /boot/config.txt
            sed -i '/#MULTIOTP#/i dtoverlay=pcf2127-rtc' /boot/config.txt
            sed -i '/#MULTIOTP#/i #' /boot/config.txt
            sed -i '/dtoverlay=ds1307/d' /boot/config.txt
            sed -i '/#MULTIOTP#/i # Enable CJE Micro RTC clock module (and other DSxxxx compatible RTC clock)' /boot/config.txt
            sed -i '/#MULTIOTP#/i dtoverlay=i2c-rtc,ds1307' /boot/config.txt

            # Enable CJE Micro RTC clock module (and other DSxxxx compatible RTC clock)
            /bin/grep "rtc-ds1307" /etc/modules > /dev/null 2>&1
            if [ $? != 0 ]; then
                echo rtc-ds1307 >> /etc/modules
            fi
        fi

        sed -i '/#MULTIOTP#/i #' /boot/config.txt
        sed -i '/#MULTIOTP#/i # multiOTP - end of specific configuration' /boot/config.txt
        sed -i '/#MULTIOTP#/i ##########################################' /boot/config.txt
        sed -i '/#MULTIOTP#/i #' /boot/config.txt
        sed -i '/#MULTIOTP#/d' /boot/config.txt

        #Raspbian Jessie cleaning for RTC clock
        if [ -e /lib/udev/hwclock-set ] ; then
            sed -i 's/systemd/system-d/g' /lib/udev/hwclock-set
            sed -i '/--systz/d' /lib/udev/hwclock-set
        fi
    fi
fi


# Since 5.4.1.5
# Create multiotp service if needed
if [ -e /etc/systemd/system/ ] ; then
    cat >/etc/systemd/system/multiotp.service <<EOL
[Unit]
Description=Initialize the multiOTP functionalities
After=local-fs.target network.target
Documentation=https://www.multiOTP.com/

[Service]
Type=simple
WorkingDirectory=/usr/local/bin/multiotp/
ExecStart=/usr/local/bin/multiotp/scripts/${MULTIOTP_SH_SCRIPT} start-multiotp
ExecStop=/usr/local/bin/multiotp/scripts/${MULTIOTP_SH_SCRIPT} stop-multiotp
Restart=no

[Install]
WantedBy=multi-user.target
EOL

    systemctl enable multiotp.service
fi


# Install multiotp service
insserv multiotp


# Since 5.8.3.x, disable multicast support
ifconfig eth0 -multicast


# Do the initial FreeRADIUS 3.x configuration job
if [ -e /etc/freeradius/3.0/ ] ; then

    # Clean old FreeRADIUS 2.x configuration
    if [ -e /etc/freeradius/clients.conf ] ; then
        rm -f /etc/freeradius/clients.conf
    fi
    if [ -e /etc/freeradius/clients.conf.bkp ] ; then
        rm -f /etc/freeradius/clients.conf.bkp
    fi
    if [ -e /etc/freeradius/policy.conf ] ; then
        rm -f /etc/freeradius/policy.conf
    fi
    if [ -e /etc/freeradius/modules ] ; then
        rm -R -f /etc/freeradius/modules
    fi
    if [ -e /etc/freeradius/sites-available ] ; then
        rm -R -f /etc/freeradius/sites-available
    fi

    # Create /etc/freeradius/3.0/mods-available/multiotp
    echo "Create /etc/freeradius/3.0/mods-available/multiotp"
    echo "# Exec module instance for multiOTP" > /etc/freeradius/3.0/mods-available/multiotp
    echo "exec multiotp {" >> /etc/freeradius/3.0/mods-available/multiotp
    echo "    wait = yes" >> /etc/freeradius/3.0/mods-available/multiotp
    echo "    input_pairs = request" >> /etc/freeradius/3.0/mods-available/multiotp
    echo "    output_pairs = reply" >> /etc/freeradius/3.0/mods-available/multiotp
    echo "    program = \"/usr/local/bin/multiotp/multiotp.php -base-dir='/usr/local/bin/multiotp/' '%{User-Name}' '%{User-Password}' -request-nt-key -src='%{Packet-Src-IP-Address}' -tag='%{Client-Shortname}' -mac='%{Called-Station-Id}' -calling-ip='%{Framed-IP-Address}' -calling-mac='%{Calling-Station-Id}' -chap-challenge='%{CHAP-Challenge}' -chap-password='%{CHAP-Password}' -ms-chap-challenge='%{MS-CHAP-Challenge}' -ms-chap-response='%{MS-CHAP-Response}' -ms-chap2-response='%{MS-CHAP2-Response}' -state='%{State}'\"" >> /etc/freeradius/3.0/mods-available/multiotp
    echo "    shell_escape = yes" >> /etc/freeradius/3.0/mods-available/multiotp
    echo "}" >> /etc/freeradius/3.0/mods-available/multiotp

    # Enable multiotp module
    echo "Enable multiotp module"
    if [ ! -e /etc/freeradius/3.0/mods-enabled/multiotp ] ; then
        ln -s ../mods-available/multiotp /etc/freeradius/3.0/mods-enabled/multiotp
    fi

    # Create /etc/freeradius/3.0/mods-available/multiotpmschap
    echo "Create /etc/freeradius/3.0/mods-available/multiotpmschap"
    cp -f /etc/freeradius/3.0/mods-available/mschap /etc/freeradius/3.0/mods-available/multiotpmschap
    sed -i "s/mschap {/mschap multiotpmschap {/" /etc/freeradius/3.0/mods-available/multiotpmschap
    sed -i "s/.*ntlm_auth = .*/        ntlm_auth = \"\/usr\/local\/bin\/multiotp\/multiotp.php -base-dir='\/usr\/local\/bin\/multiotp\/' '%{User-Name}' '%{User-Password}' -request-nt-key -src='%{Packet-Src-IP-Address}' -tag='%{Client-Shortname}' -mac='%{Called-Station-Id}' -calling-ip='%{Framed-IP-Address}' -calling-mac='%{Calling-Station-Id}' -chap-challenge='%{CHAP-Challenge}' -chap-password='%{CHAP-Password}' -ms-chap-challenge='%{MS-CHAP-Challenge}' -ms-chap-response='%{MS-CHAP-Response}' -ms-chap2-response='%{MS-CHAP2-Response}' -state='%{State}'\"/" /etc/freeradius/3.0/mods-available/multiotpmschap

    # Enable multiotpmschap module
    echo "Enable multiotpmschap module"
    if [ ! -e /etc/freeradius/3.0/mods-enabled/multiotpmschap ] ; then
        ln -s ../mods-available/multiotpmschap /etc/freeradius/3.0/mods-enabled/multiotpmschap
    fi

    # Edit /etc/freeradius/3.0/mods-available/perl
    echo "Edit /etc/freeradius/3.0/mods-available/perl"
    sed -i "s/.*filename = .*/        filename = \/usr\/local\/bin\/multiotp\/scripts\/multiotp.pl/" /etc/freeradius/3.0/mods-available/perl

    # Enable perl module
    echo "Enable perl module"
    if [ ! -e /etc/freeradius/3.0/mods-enabled/perl ] ; then
        ln -s ../mods-available/perl /etc/freeradius/3.0/mods-enabled/perl
    fi

    # Create /etc/freeradius/3.0/policy.d/multiotp
    echo "Create /etc/freeradius/3.0/policy.d/multiotp"
    echo "# Change to a specific prefix if you want to deal with normal PAP authentication as well as OTP" > /etc/freeradius/3.0/policy.d/multiotp
    echo "# e.g. \"multiotp_prefix = 'otp:'\"" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "multiotp_prefix = ''" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "multiotp.authorize {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "   # This test force multiOTP for any MS-CHAP(v2),CHAP and PAP attempt" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "    if (control:Auth-Type == mschap) {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "          update control {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "                  Auth-Type := multiotpmschap" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "          }" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "    }" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "    elsif (control:Auth-Type == chap) {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "          update control {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "                  Auth-Type := multiotp" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "          }" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "    }" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "    elsif (!control:Auth-Type) {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "        update control {" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "            Auth-Type := multiotp" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "        }" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "    }" >> /etc/freeradius/3.0/policy.d/multiotp
    echo "}" >> /etc/freeradius/3.0/policy.d/multiotp

    # Clean multiotp lines in /etc/freeradius/3.0/sites-available/default
    echo "Clean multiotp lines in /etc/freeradius/3.0/sites-available/default"
    sed -i '/multiotp/d' /etc/freeradius/3.0/sites-available/default

    # Enable files and add perl just after (in the authorize section)
    echo "Enable files and add perl just after"
    sed -i '/raddb\/mods-config\/files\/authorize/{n;d}' /etc/freeradius/3.0/sites-available/default
    sed -i '/raddb\/mods-config\/files/a\        files\n        perl #multiotp\n        if (ok || updated) { #multiotp\n            update control { #multiotp\n                Auth-Type := Perl #multiotp\n                Client-Shortname = "%{Client-Shortname}" #multiotp\n                Packet-Src-IP-Address = "%{Packet-Src-IP-Address}" #multiotp\n            } #multiotp\n        } #multiotp' /etc/freeradius/3.0/sites-available/default    
    
    # Add some lines before the pap module (after logintime) in authorize section of default
    echo "Add some lines before the pap module in authorize section of default"
    sed -i '/logintime/a\\n        # Handle multiotp authentication\n        multiotp' /etc/freeradius/3.0/sites-available/default

    # Add some lines in the authenticate section of default
    echo "Add some lines in the authenticate section of default"
    sed -i '/authenticate {/a\        Auth-Type multiotp {\n                multiotp\n        } #multiotp\n        Auth-Type multiotpmschap {\n                multiotpmschap\n        } #multiotpmschap\n        Auth-Type Perl { #multiotp\n                perl #multiotp\n        } #multiotp\n' /etc/freeradius/3.0/sites-available/default

    # Add some lines in the accounting section of default
    echo "Add some lines in the accounting section of default"
    sed -i '/accounting {/a\        perl #multiotp\n' /etc/freeradius/3.0/sites-available/default

    # Clean multiotp lines in /etc/freeradius/3.0/sites-available/inner-tunnel
    echo "Clean multiotp lines in /etc/freeradius/3.0/sites-available/inner-tunnel"
    sed -i '/multiotp/d' /etc/freeradius/3.0/sites-available/inner-tunnel

    # Enable files and add perl just after (in the authorize section)
    echo "Enable files and add perl just after"
    sed -i '/raddb\/mods-config\/files\/authorize/{n;d}' /etc/freeradius/3.0/sites-available/inner-tunnel
    sed -i '/raddb\/mods-config\/files/a\        files\n        perl #multiotp\n        if (ok || updated) { #multiotp\n            update control { #multiotp\n                Auth-Type := Perl #multiotp\n                Client-Shortname = "%{Client-Shortname}" #multiotp\n                Packet-Src-IP-Address = "%{Packet-Src-IP-Address}" #multiotp\n            } #multiotp\n        } #multiotp' /etc/freeradius/3.0/sites-available/inner-tunnel    
    

    # Add some lines before the pap module (after logintime) in authorize section of inner-tunnel
    echo "Add some lines before the pap module in authorize section of inner-tunnel"
    sed -i '/logintime/a\\n        # Handle multiotp authentication\n        multiotp' /etc/freeradius/3.0/sites-available/inner-tunnel

    # Add some lines in the authenticate section of inner-tunnel
    echo "Add some lines in the authenticate section of inner-tunnel"
    sed -i '/authenticate {/a\        Auth-Type multiotp {\n                multiotp\n        } #multiotp\n        Auth-Type multiotpmschap {\n                multiotpmschap\n        } #multiotpmschap\n        Auth-Type Perl { #multiotp\n                perl #multiotp\n        } #multiotp\n' /etc/freeradius/3.0/sites-available/inner-tunnel

    # Add some lines in the accounting section of inner-tunnel
    echo "Add some lines in the accounting section of inner-tunnel"
    sed -i '/accounting {/a\        perl #multiotp\n' /etc/freeradius/3.0/sites-available/inner-tunnel

    if [[ "${RADIUS_SAMPLE_ENABLED}" == "1" ]] ; then
        # Check and add freeradius config if needed (only for previous community edition)
        /bin/grep "multiotp my-first-network" /etc/freeradius/3.0/clients.conf > /dev/null 2>&1
        if [ $? != 0 ]; then
            echo "# multiotp my-first-network BEGIN" >> /etc/freeradius/3.0/clients.conf
            echo "client 192.168.0.0/16 { # multiotp my-first-network" >> /etc/freeradius/3.0/clients.conf
            echo "secret = myfirstpass # multiotp my-first-network" >> /etc/freeradius/3.0/clients.conf
            echo "shortname = my-first-network # multiotp my-first-network" >> /etc/freeradius/3.0/clients.conf
            echo "} # multiotp my-first-network" >> /etc/freeradius/3.0/clients.conf
            echo "# multiotp my-first-network END" >> /etc/freeradius/3.0/clients.conf
        fi
    fi
    
# End of the initial FreeRADIUS 3.x configuration job
else

    echo "FreeRADIUS legacy configuration"

    # Edit /etc/freeradius/modules/perl
    echo "Edit /etc/freeradius/modules/perl"
    sed -i "s/.*module = .*/        module = \/usr\/local\/bin\/multiotp\/scripts\/multiotp.pl/" /etc/freeradius/modules/perl
    # Since 5.8.3.0 and FreeRADIUS 3.0.18, set the perl flags
    sed -i "s/.*perl_flags = .*/        perl_flags = \"-U\"/" /etc/freeradius/3.0/mods-available/perl

    # Since 5.8.3.0 and FreeRADIUS 3.0.18, comment disable_tlsv1_2, disable_tlsv1_1 and disable_tlsv1
    sed -i "s/.*disable_tlsv1_2 = /\t\t#disable_tlsv1_2 = /" /etc/freeradius/3.0/mods-available/eap
    sed -i "s/.*disable_tlsv1_1 = /\t\t#disable_tlsv1_2 = /" /etc/freeradius/3.0/mods-available/eap
    sed -i "s/.*disable_tlsv1 = /\t\t#disable_tlsv1 = /" /etc/freeradius/3.0/mods-available/eap

    if [[ "${RADIUS_SAMPLE_ENABLED}" == "1" ]] ; then
        # Check and add freeradius config if needed (only for previous community edition)
        /bin/grep "multiotp my-first-network" /etc/freeradius/clients.conf > /dev/null 2>&1
        if [ $? != 0 ]; then
            echo "# multiotp my-first-network BEGIN" >> /etc/freeradius/clients.conf
            echo "client 192.168.0.0/16 { # multiotp my-first-network" >> /etc/freeradius/clients.conf
            echo "secret = myfirstpass # multiotp my-first-network" >> /etc/freeradius/clients.conf
            echo "shortname = my-first-network # multiotp my-first-network" >> /etc/freeradius/clients.conf
            echo "} # multiotp my-first-network" >> /etc/freeradius/clients.conf
            echo "# multiotp my-first-network END" >> /etc/freeradius/clients.conf
        fi
    fi

fi
# End of the initial FreeRADIUS configuration job


# Add some users in groups (usermod -a -G groupName userName)
usermod -a -G freerad www-data
usermod -a -G users freerad
usermod -a -G users www-data
usermod -a -G www-data freerad

chown -R freerad:freerad /etc/freeradius

# Restart freeradius service
if [ -e /etc/init.d/freeradius ] ; then
    /etc/init.d/freeradius restart
else
    service freeradius restart
fi

# 5.4.1.5 Enable Freeradius service
systemctl enable freeradius.service


# Don't touch SSH if we test only the installation
if [[ "$1" != "test" ]] && [[ "$2" != "test" ]] && [[ "${TYPE}" != "DOCKER" ]] ; then
    chmod 0700 /root/.ssh
    chmod 0600 /root/.ssh/authorized_keys
    if [[ "${SSH_ROOT_LOGIN}" == "1" ]] ; then
        sed -i 's/.*PermitRootLogin .*/PermitRootLogin yes/' /etc/ssh/sshd_config
    fi
    systemctl enable ssh
    systemctl start ssh
    if [ -e /etc/init.d/ssh ] ; then
        /etc/init.d/ssh restart
    else
        service ssh restart
    fi
    # Remove ssh flag file (to start SSH the first time)
    if [ -e /boot/ssh ] ; then
        rm -f /boot/ssh
    fi
fi


if [[ "${TYPE}" != "DOCKER" ]]; then
    #iptable

    # Since 5.4.1.0
    # Authorize PING
    iptables -A INPUT -p icmp -j ACCEPT

    # authorized ports
    iptables -A INPUT -p tcp --dport ${SSH_PORT} -j ACCEPT
    iptables -A INPUT -p tcp --dport 80 -j ACCEPT
    iptables -A INPUT -p udp --dport 161 -j ACCEPT
    iptables -A INPUT -p tcp --dport 443 -j ACCEPT
    iptables -A INPUT -p udp --dport 1812 -j ACCEPT
    iptables -A INPUT -p udp --dport 1813 -j ACCEPT

    # no firewall on the local loop (127.x.x.x)
    iptables -A INPUT -i lo -j ACCEPT
    iptables -A OUTPUT -o lo -j ACCEPT

    # existing connections receive their traffic
    iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

    # refused by default
    iptables -P INPUT DROP

    iptables-save > /etc/iptables/rules.v4

    # ip6 can be swapped if not used
    ip6tables-save > /etc/iptables/rules.v6
fi


# Enable UDP support for syslog
if [ -e /etc/rsyslog.conf ] ; then
    sed -i 's/#.*$ModLoad.*imudp/$ModLoad imudp/' /etc/rsyslog.conf
    sed -i 's/#.*$UDPServerRun.*514/$UDPServerRun 514/' /etc/rsyslog.conf
fi


# Add multiotp aliases for everybody
# if [ ! -e /etc/profile.d/00-multiotp-aliases.sh ] ; then
echo alias multiotp='/usr/local/bin/multiotp/multiotp.php' > /etc/profile.d/00-multiotp-aliases.sh
# fi

# Add multiotp aliases for root (for Docker)
sed -i "/^alias multiotp=*/d" /root/.bashrc
echo alias multiotp='/usr/local/bin/multiotp/multiotp.php' >> /root/.bashrc


# Reset the admin password (if config file exists)
if [ -e /etc/multiotp/config/multiotp.ini ] ; then
    sed -i 's/^admin_password_hash.*/admin_password_hash=/' /etc/multiotp/config/multiotp.ini
fi


# Clean history and other files
# Ideas: http://lonesysadmin.net/2013/03/26/preparing-linux-template-vms/
/usr/sbin/logrotate -f /etc/logrotate.conf
/bin/rm -f /var/log/*-???????? /var/log/*.gz
/bin/rm -f /var/log/auth*
/bin/rm -f /var/log/boot*
/bin/rm -f /var/log/btmp*
/bin/rm -f /var/log/daemon*
/bin/rm -f /var/log/debug*
/bin/rm -f /var/log/faillog*
/bin/rm -f /var/log/kern*
/bin/rm -f /var/log/lastlog*
/bin/rm -f /var/log/messages*
/bin/rm -f /var/log/php*
/bin/rm -f /var/log/syslog*
/bin/rm -f /var/log/user*
/bin/rm -f /var/log/wtmp*
if [[ "${DEBUGMODE}" != "TRUE" ]]; then
    /bin/rm -rf /tmp/*
fi
/bin/rm -rf /var/tmp/*
/bin/rm -f ~root/.bash_history
unset HISTFILE

# Clean the history
history -c


if [[ "${TYPE}" != "DOCKER" ]]; then
    # localepurge
    apt-get clean
fi


#Clean all unnecessary files
if [ -e ${SOURCEDIR}/Desktop ] ; then
  rm -r ${SOURCEDIR}/Desktop
fi
if [ -e ${SOURCEDIR}/python_games ] ; then
  rm -r ${SOURCEDIR}/python_games
fi
if [ -e ${SOURCEDIR}/ocr_pi.png ] ; then
  rm -r ${SOURCEDIR}/ocr_pi.png
fi

# Be sure that we will create a new SSL certificate at the reboot
touch /etc/multiotp/certificates/multiotp.generic


# Be sure that we will create new SSH keys at the reboot
touch /etc/ssh/ssh.generic


# (Re)initialize the network interface and the configuration without rebooting
/usr/local/bin/multiotp/scripts/${MULTIOTP_SH_SCRIPT} reset-config noreboot


if [ -e /usr/local/bin/multiotp/multiotp.cli.php ] ; then
    rm -f /usr/local/bin/multiotp/multiotp.cli.php
fi

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


# Stop all services if we are a Docker container
if [[ "${TYPE}" == "DOCKER" ]]; then
    if [ -e /etc/init.d/freeradius ] ; then
        /etc/init.d/freeradius stop
    else
        service freeradius stop
    fi
    if [ -e /etc/init.d/nginx ] ; then
        /etc/init.d/nginx stop
    else
        service nginx stop
    fi
    if [ -e /etc/init.d/ntp ] ; then
        /etc/init.d/ntp stop
    else
        service ntp stop
    fi
    if [ -e /etc/init.d/${PHPFPM} ] ; then
        /etc/init.d/${PHPFPM} stop
    else
        service ${PHPFPM} stop
    fi
fi


# Installation information
echo "VM version: ${VMRELEASENUMBER}"
echo "PHP MAJOR version: ${PHPMAJORVERSION}"
echo "OS ID: ${OSID}"
echo "OS Version: ${OSVERSION}"
echo "Hardware family: ${FAMILY}"
echo "Hardware type: ${TYPE}"
echo "Backend: ${BACKEND}"
echo "Current script: ${BASH_SOURCE}"
echo "Source directory: ${SOURCEDIR}"


# Check the error log
# tail -n 1000 -f /var/log/nginx/error.log


# Remove this file
if [[ "${DEBUGMODE}" != "TRUE" ]]; then
    if [ -e ${BASH_SOURCE} ] ; then
        rm -f ${BASH_SOURCE}
    fi
fi

# And finally reboot the device if we are not updating and we are not a docker
if [[ ! "$1" == "update" ]] && [[ ! "${TYPE}" == "DOCKER" ]]; then
    if [[ "${REBOOT_AT_THE_END}" == "1" ]]; then
        /sbin/reboot
    else
        echo Please reboot now!
    fi
fi

exit 0
