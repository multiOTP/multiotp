#!/bin/bash
########################################
#
# @file   multiotp-service.sh
# @brief  Bash helper for multiOTP service
#
# multiOTP package - Strong two-factor authentication open source package
# http://www.multiOTP.net/
#
# The multiOTP package is the lightest package available that provides so many
# strong authentication functionalities and goodies, and best of all, for anyone
# that is interested about security issues, it's a fully open source solution!
#
# This package is the result of a *LOT* of work. If you are happy using this
# package, [Donation] are always welcome to support this project.
# Please check http://www.multiOTP.net/ and you will find the magic button ;-)
#
# @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.6.1.5
# @date      2019-10-23
# @since     2013-11-29
# @copyright (c) 2013-2019 by SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
##########################################################################################


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


if [ $# -ge 1 ]; then
    COMMAND="$1"
else
    COMMAND="help"
fi

if [ $# -ge 2 ]; then
    PARAM1="$2"
else
    PARAM1=""
fi

if [ $# -ge 3 ]; then
    PARAM2="$3"
else
    PARAM2=""
fi

if [ $# -ge 4 ]; then
    PARAM3="$4"
else
    PARAM3=""
fi

if [ $# -ge 5 ]; then
    PARAM4="$5"
else
    PARAM4=""
fi

if [ $# -ge 6 ]; then
    PARAM5="$6"
else
    PARAM5=""
fi


if [[ "${COMMAND}" == "reset-config" ]]; then
    # Reset the network interface
    echo auto lo > /etc/network/interfaces
    echo iface lo inet loopback >> /etc/network/interfaces
    echo >> /etc/network/interfaces
    echo auto eth0 >> /etc/network/interfaces
    echo iface eth0 inet static >> /etc/network/interfaces
    echo     address 192.168.1.44 >> /etc/network/interfaces
    echo     netmask 255.255.255.0 >> /etc/network/interfaces
    echo     network 192.168.1.0 >> /etc/network/interfaces
    echo     gateway 192.168.1.1 >> /etc/network/interfaces

    # Reset the DNS resolver
    echo domain multiotp.local > /etc/resolv.conf
    echo search multiotp.local >> /etc/resolv.conf
    echo nameserver 8.8.8.8 >> /etc/resolv.conf
    echo nameserver 8.8.4.4 >> /etc/resolv.conf
elif [[ "${COMMAND}" == "start-multiotp" ]]; then
    # Clean all PHP sessions
    if [ -e /var/lib/php5/sess_* ] ; then
        rm -f /var/lib/php5/sess_*
    fi
    if [ -e /var/lib/php/sessions/* ] ; then
        rm -f /var/lib/php/sessions/*
    fi

    # If any, clean DHCP option for NTP
    # http://support.ntp.org/bin/view/Support/ConfiguringNTP#Section_6.12
    if [ -e /var/lib/ntp/ntp.conf.dhcp ] ; then
        rm -f /var/lib/ntp/ntp.conf.dhcp
    fi

    # Create specific SSL certificate if needed
    if [ -e /etc/multiotp/certificates/multiotp.generic ] || [ ! -e /etc/multiotp/certificates/multiotp.key ] ; then
        /etc/init.d/nginx stop
        openssl genrsa -out /etc/multiotp/certificates/multiotp.key 2048
        openssl req -new -key /etc/multiotp/certificates/multiotp.key -out /etc/multiotp/certificates/multiotp.csr -subj "/C=CH/ST=GPL/L=Open Source Edition/O=multiOTP/OU=strong authentication server/CN=multiOTP"
        openssl x509 -req -days 7305 -in /etc/multiotp/certificates/multiotp.csr -signkey /etc/multiotp/certificates/multiotp.key -out /etc/multiotp/certificates/multiotp.crt
        if [ -e /etc/multiotp/certificates/multiotp.generic ] ; then
            rm -f /etc/multiotp/certificates/multiotp.generic
        fi
        if [ -e /etc/init.d/nginx ] ; then
            /etc/init.d/nginx restart
        else
            service nginx restart
        fi
    fi
    
    # Create specific SSH key if needed
    if [ -e /etc/ssh/ssh.generic ] || [ ! -e /etc/ssh/ssh_host_rsa_key ] ; then
        echo -e "\n\n\n" | ssh-keygen -f /etc/ssh/ssh_host_rsa_key -N '' -t rsa
        echo -e "\n\n\n" | ssh-keygen -f /etc/ssh/ssh_host_dsa_key -N '' -t dsa
        rm -f /etc/ssh/ssh.generic
    fi

    i2cdetect -y 1 81 81 | grep -E "51|UU" > /dev/null
    if [ $? == 0 ]; then
        # Declare the Afterthought Software RasClock device (and other PCF212x compatible RTC clock) on a Rev. 2 board
        echo pcf2127a 0x51 > /sys/class/i2c-adapter/i2c-1/new_device
        # Set the system time from the hardware clock
        ( sleep 2; hwclock -s ) &
    else
        # Declare the CJE Micro’s RTC clock device (and other DSxxxx compatible RTC clock) on a Rev. 2 Board
        i2cdetect -y 1 104 104 | grep -E "68|UU" > /dev/null    
        if [ $? == 0 ]; then
            echo ds1307 0x68 > /sys/class/i2c-adapter/i2c-1/new_device
            # Set the system time from the hardware clock
            ( sleep 2; hwclock -s ) &
        else
            i2cdetect -y 0 81 81 | grep -E "51|UU" > /dev/null
            if [ $? == 0 ]; then
            # Declare the Afterthought Software RasClock device (and other PCF212x compatible RTC clock) on a Rev. 1 board
                echo pcf2127a 0x51 > /sys/class/i2c-adapter/i2c-0/new_device
                # Set the system time from the hardware clock
                ( sleep 2; hwclock -s ) &
            else
                i2cdetect -y 0 104 104 | grep -E "68|UU" > /dev/null    
                if [ $? == 0 ]; then
                    # Declare the CJE Micro’s RTC clock device (and other DSxxxx compatible RTC clock) on a Rev. 1 Board
                    echo ds1307 0x68 > /sys/class/i2c-adapter/i2c-0/new_device
                    # Set the system time from the hardware clock
                    ( sleep 2; hwclock -s ) &
                fi
            fi
        fi
    fi
    
    # Write the last start time in a file
    date -R > /root/starttime.txt
    exit 0

elif [[ "${COMMAND}" == "stop-multiotp" ]]; then
    # Set the hardware clock from the current system time if hardware device
    if [[ "${FAMILY}" != "VAP" ]]; then
        hwclock -w
    fi

    # Write the last stop time in a file
    date -R > /root/stoptime.txt
    exit 0
fi
