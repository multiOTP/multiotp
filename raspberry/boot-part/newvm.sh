#!/bin/bash
##########################################################################
#
# @file   newvm.sh
# @brief  Reset multiOTP open source installation (Raspberry Pi / VM / Docker)
#
# multiOTP package - Strong two-factor authentication open source package
# https://www\.multiOTP.net/
#
# @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.8.2.1
# @date      2021-04-08
# @since     2013-09-22
# @copyright (c) 2013-2021 SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
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

TEMPVERSION="@version   5.8.2.1"
MULTIOTPVERSION="$(echo -e "${TEMPVERSION:8}" | tr -d '[[:space:]]')"
IFS='.' read -ra MULTIOTPVERSIONARRAY <<< "$MULTIOTPVERSION"
MULTIOTPMAJORVERSION=${MULTIOTPVERSIONARRAY[0]}

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
VMRELEASENUMBER="010"
if [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "7" ]]; then
    BACKENDDB="mysql"
    PHPFPM="php5-fpm"
    PHPFPMSED="php5-fpm"
    PHPINSTALLPREFIX="php5"
    PHPINSTALLPREFIXVERSION="php5"
    PHPMODULEPREFIX="php5"
    PHPMAJORVERSION="5"
    VMRELEASENUMBER="007"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "8" ]]; then
    BACKENDDB="mysql"
    PHPFPM="php5-fpm"
    PHPFPMSED="php5-fpm"
    PHPINSTALLPREFIX="php5"
    PHPINSTALLPREFIXVERSION="php5"
    PHPMODULEPREFIX="php5"
    PHPMAJORVERSION="5"
    VMRELEASENUMBER="007"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "9" ]]; then
    BACKENDDB="mysql"
    PHPFPM="php7.0-fpm"
    PHPFPMSED="php\/php7.0-fpm"
    PHPINSTALLPREFIX="php"
    PHPINSTALLPREFIXVERSION="php7.0"
    PHPMODULEPREFIX="php/7.0"
    PHPMAJORVERSION="7"
    VMRELEASENUMBER="008"
elif [[ "${OSID}" == "debian" ]] && [[ "${OSVERSION}" == "10" ]]; then
    BACKENDDB="mariadb"
    PHPFPM="php7.3-fpm"
    PHPFPMSED="php\/php7.3-fpm"
    PHPINSTALLPREFIX="php"
    PHPINSTALLPREFIXVERSION="php7.3"
    PHPMODULEPREFIX="php/7.3"
    PHPMAJORVERSION="7"
    VMRELEASENUMBER="010"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "7" ]]; then
    BACKENDDB="mysql"
    PHPFPM="php5-fpm"
    PHPFPMSED="php5-fpm"
    PHPINSTALLPREFIX="php5"
    PHPINSTALLPREFIXVERSION="php5"
    PHPMODULEPREFIX="php5"
    PHPMAJORVERSION="5"
    VMRELEASENUMBER="007"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "8" ]]; then
    BACKENDDB="mysql"
    PHPFPM="php5-fpm"
    PHPFPMSED="php5-fpm"
    PHPINSTALLPREFIX="php5"
    PHPINSTALLPREFIXVERSION="php5"
    PHPMODULEPREFIX="php5"
    PHPMAJORVERSION="5"
    VMRELEASENUMBER="007"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "9" ]]; then
    BACKENDDB="mysql"
    PHPFPM="php7.0-fpm"
    PHPFPMSED="php\/php7.0-fpm"
    PHPINSTALLPREFIX="php"
    PHPINSTALLPREFIXVERSION="php7.0"
    PHPMODULEPREFIX="php/7.0"
    PHPMAJORVERSION="7"
    VMRELEASENUMBER="008"
elif [[ "${OSID}" == "raspbian" ]] && [[ "${OSVERSION}" == "10" ]]; then
    BACKENDDB="mariadb"
    PHPFPM="php7.3-fpm"
    PHPFPMSED="php\/php7.3-fpm"
    PHPINSTALLPREFIX="php"
    PHPINSTALLPREFIXVERSION="php7.3"
    PHPMODULEPREFIX="php/7.3"
    PHPMAJORVERSION="7"
    VMRELEASENUMBER="010"
fi


# Early docker detection
is_running_in_container() {
  awk -F: '$3 ~ /^\/$/{ c=1 } END { exit c }' /proc/self/cgroup
}

if is_running_in_container; then
    TYPE="DOCKER"
fi

if [ -f /.dockerenv ]; then
    TYPE="DOCKER"
fi

UNAME=$(uname -a)
if [[ "${UNAME}" == *docker* ]]; then
    TYPE="DOCKER"
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
elif is_running_in_container; then
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
        VMTOOLS=$(dpkg-query -l | grep "open-vm-tools")
        if [[ "${VMTOOLS}" == *open-vm-tools* ]]; then
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


if [[ "${FAMILY}" == "RPI" ]]; then
    # Kill all processes which are running with pi user
    ps -ef | grep pi | awk '{ print $2 }' | xargs kill -9 > /dev/null 2>&1

    # Remove the initial user named pi
    userdel -r pi > /dev/null 2>&1
fi


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


# Docker could be mounted with existing configuration
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
if [[ "${TYPE}" != "DOCKER" ]]; then
    if [[ ! "$1" == "nozero" ]]; then
        echo "Zeroing disk space..."
        dd if=/dev/zero of=/zeroes bs=4096
        rm -f /zeroes
    fi
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

    echo The device is now halted.

    #Stop the VM
    shutdown now -h &
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
    if [ -e /etc/init.d/ntp ] ; then
        /etc/init.d/ntp start
    else
        service ntp start
    fi
    if [ -e /etc/init.d/${PHPFPM} ] ; then
        /etc/init.d/${PHPFPM} start
    else
        service ${PHPFPM} start
    fi
    # Keep container running
    while true;
        do sleep 30;
    done;
fi
