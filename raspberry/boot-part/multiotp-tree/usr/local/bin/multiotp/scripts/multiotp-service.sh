#!/bin/bash
########################################
#
# @file   multiotp-service.sh
# @brief  Bash helper for multiOTP service
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
# This script does several things in bash for multiOTP open source edition
#  get-iface-info
#  get-network-config (deprecated, use get-iface-info)
#  help
#  reset-config
#  set-ip
#  start-multiotp
#  stop-multiotp
#
# @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.10.4.2
# @date      2026-10-31
# @since     2013-11-29
# @copyright (c) 2013-2026 SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
##########################################################################################

# Stop on error, stop on undeclared variable, output error is the first error code in the chain
#set -euo pipefail

# OS ID and version (2026-01-29)
# Architecture (for example x86_64)
OSID=$(cat /etc/os-release | grep "^ID=" | awk -F'=' '{print $2}')
OSVERSION=$(awk -F'"' '/VERSION_ID/ {split($2,a,"."); print a[1]}' /etc/os-release)
ARCHITECTURE=$(lscpu |grep "^Architecture" | awk -F':' '{print $2}' | awk '{$1=$1;print}')
IFNAME=$(ip -o link show | awk -F': ' '{print $2}' | grep -v '^lo$')

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


###############################
# Network commands (2026-02-27)
###############################

# Network manager detection: netplan|ifupdown
detect_net_tool() {
  if [ -d /etc/netplan ] && ls /etc/netplan/*.yaml 1> /dev/null 2>&1; then
    echo "netplan"
  else
    echo "ifupdown"
  fi
}

# Ethernet interfaces list (without the local lo): eth0|eth1|ens18|192|...
get_all_ethernet_interfaces() {
  for iface_path in /sys/class/net/*; do
    iface=$(basename "$iface_path")
    if [ -f $iface_path/type ] && [ "$(cat $iface_path/type)" -eq 1 ]; then
      echo "$iface"
    fi
  done
}

# First available ethernet interface (without the local lo): eth0|eth1|ens18|192|...
get_ethernet_interface() {
  for iface_path in /sys/class/net/*; do
    iface=$(basename "$iface_path")
    if [ -f $iface_path/type ] && [ "$(cat $iface_path/type)" -eq 1 ]; then
      echo "$iface"
      break
    fi
  done
}

# IP mode: dhcp|static
get_ip_mode() {
  local NET_TOOL
  NET_TOOL=$(detect_net_tool)
  local iface=$1
  local mode="unknown"

  if [ "$NET_TOOL" = "netplan" ]; then
    # Search the dhcp4 parameter
    yaml_file=$(ls /etc/netplan/*.yaml | grep -v 'cloud-init' 2>/dev/null | head -n1)
    if [ -f "$yaml_file" ]; then
      dhcp_val=$(grep -A3 "^\s*$iface:" "$yaml_file" | grep "dhcp4:" | awk '{print $2}')
      if [ "$dhcp_val" = "yes" ]; then
        mode="dhcp"
      elif [ "$dhcp_val" = "no" ]; then
        mode="static"
      fi
    fi
  fi
  if [ "$mode" = "unknown" ]; then # ifupdown or mixed
    iface_line=$(grep -E "iface\s+$iface\s+inet\s+" /etc/network/interfaces /etc/network/interfaces.d/* 2>/dev/null | head -n1)
    if [[ $iface_line == *"dhcp"* ]]; then
      mode="dhcp"
    elif [[ $iface_line == *"static"* ]]; then
      mode="static"
    fi
  fi

  echo "$mode"
}

# prefix /CIDR to subnetconversion
cidr_to_netmask() {
  local prefix=$1
  local mask=$((0xffffffff << (32 - prefix) & 0xffffffff))
  printf "%d.%d.%d.%d\n" \
    $(( (mask >> 24) & 0xff )) \
    $(( (mask >> 16) & 0xff )) \
    $(( (mask >> 8) & 0xff )) \
    $(( mask & 0xff ))
}

# Get MAC address
get_mac_address() {
  local iface=$1
  if [ -f "/sys/class/net/$iface/address" ]; then
    cat "/sys/class/net/$iface/address"
  else
    echo "00:00:00:00:00:00"
    return 1
  fi
}

# Get DNS entries for a specific interface
get_dns() {
  local iface="$1"
  local dns=""

  # Check if resolvectl or systemd-resolve is available
  if command -v resolvectl >/dev/null 2>&1; then
    dns=$(resolvectl status "$iface" 2>/dev/null | grep 'DNS Servers:' | sed 's/^.*DNS Servers:[[:space:]]*//' | tr ' ' ',')
  elif command -v systemd-resolve >/dev/null 2>&1; then
    dns=$(systemd-resolve --status "$iface" 2>/dev/null | grep 'DNS Servers:' | sed 's/^.*DNS Servers:[[:space:]]*//' | tr ' ' ',')
  fi

  # If empty, check resolv.conf
  if [ -z "$dns" ]; then
    dns=$(grep '^nameserver' /etc/resolv.conf | awk '{print $2}' | tr '\n' ',' | sed 's/,$//')
  fi

  echo "$dns"
}

# Display all interfaces with full info
# eth0 - dhcp : 192.168.1.2 / 255.255.255.0 > 192.168.1.1 | "1.1.1.1,8.8.8.8" @ 12:34:56:78:90:ab
# ...
get_ifaces_info() {
  for iface in $(get_all_ethernet_interfaces); do
    ip_info=$(ip -4 addr show "$iface" | grep -oP '\d+(\.\d+){3}/\d+')
    mac=$(get_mac_address $iface)
    dns=$(get_dns $iface)
    if [ -n "$ip_info" ]; then
      ip_addr=$(echo $ip_info | cut -d/ -f1)
      prefix=$(echo $ip_info | cut -d/ -f2)
      mask=$(cidr_to_netmask $prefix)
      mode=$(get_ip_mode $iface)
      default_gateway=$(ip route | grep '^default' | awk '{print $3}')
      echo "$iface - $mode : $ip_addr / $mask > $default_gateway | \"$dns\" @ $mac"
    else
      echo "$iface - $mode : 0.0.0.0 / 0.0.0.0 > 0.0.0.0 | \"$dns\" @ $mac"
    fi
  done
}

# Display first interface with full info
# eth0 - dhcp : 192.168.1.2 / 255.255.255.0 > 192.168.1.1 | "1.1.1.1,8.8.8.8" @ 12:34:56:78:90:ab
get_iface_info() {
  iface=$(get_ethernet_interface)
  ip_info=$(ip -4 addr show "$iface" | grep -oP '\d+(\.\d+){3}/\d+')
  mac=$(get_mac_address $iface)
  dns=$(get_dns $iface)
  if [ -n "$ip_info" ]; then
    ip_addr=$(echo $ip_info | cut -d/ -f1)
    prefix=$(echo $ip_info | cut -d/ -f2)
    mask=$(cidr_to_netmask $prefix)
    mode=$(get_ip_mode $iface)
    default_gateway=$(ip route | grep '^default' | awk '{print $3}')
    echo "$iface - $mode : $ip_addr / $mask > $default_gateway | \"$dns\" @ $mac"
  else
    echo "$iface - $mode : 0.0.0.0 / 0.0.0.0 > 0.0.0.0 | \"$dns\" @ $mac"
  fi
}

# Legacy get_network_config (please prefer get_iface_info)
get_network_config() {
  iface=$(get_ethernet_interface)
  ip_info=$(ip -4 addr show "$iface" | grep -oP '\d+(\.\d+){3}/\d+')
  mac=$(get_mac_address $iface)
  dns=$(get_dns $iface)
  local dns1=""
  local dns2=""
  IFS=',' read -r dns1 dns2 _ <<< "$dns"
  dns1="${dns1:-}"
  dns2="${dns2:-}"
  if [ -n "$ip_info" ]; then
    ip_addr=$(echo $ip_info | cut -d/ -f1)
    prefix=$(echo $ip_info | cut -d/ -f2)
    mask=$(cidr_to_netmask $prefix)
    mode=$(get_ip_mode $iface)
    mode_fixed="${mode/static/fixed}"
    default_gateway=$(ip route | grep '^default' | awk '{print $3}')
    echo "${mode_fixed};${mac};${ip_addr};${mask};${default_gateway};${dns1};${dns2}"
  else
    echo "${mode_fixed};${mac};0.0.0.0;0.0.0.0;0.0.0.0;${dns1};${dns2}"
  fi
}

# Set static or dynamic IP for a specific interface
#  set_iface_ip eth0 static 192.168.1.2 255.255.255.0 192.168.1.1 "8.8.8.8,8.8.4.4" domain
#  set_iface_ip eth0 dhcp
set_iface_ip() {
  local NET_TOOL
  NET_TOOL=$(detect_net_tool)
  local iface=$1
  local mode=$2 # dhcp|static
  local ip_addr=$3
  local netmask=$4
  local gateway=$5
  local nameservers=$6
  local domain=$7

  if [ "$mode" = "dhcp" ]; then

    if [ "$NET_TOOL" = "netplan" ]; then
      cfg_file="/etc/netplan/01-on-premises.yaml"
      cat <<EOF | tee "$cfg_file" > /dev/null
network:
  version: 2
  ethernets:
    $iface:
      dhcp4: yes
EOF
      netplan apply
    else # ifupdown
      # TODO interfaces only
      # /etc/network/interfaces only, and not /etc/network/interfaces.d/$iface
      iface_file="/etc/network/interfaces"
      cat <<EOF | tee "$iface_file" > /dev/null
echo auto lo
iface lo inet loopback

auto $iface
iface $iface inet dhcp
EOF
      ifdown "$iface" || true
      ifup "$iface"
    fi

  elif [ "$mode" = "static" ]; then

    if [ "$NET_TOOL" = "netplan" ]; then
      # subnet to CIDR conversion
      local mask=$netmask
      local IFS=.
      local -a octets=($mask)
      local prefix=0
      
      for octet in "${octets[@]}"; do
        case $octet in
          255) ((prefix+=8)) ;;
          254) ((prefix+=7)) ;;
          252) ((prefix+=6)) ;;
          248) ((prefix+=5)) ;;
          240) ((prefix+=4)) ;;
          224) ((prefix+=3)) ;;
          192) ((prefix+=2)) ;;
          128) ((prefix+=1)) ;;
          0) ;;
          *) echo "Invalid netmask" >&2; return 1 ;;
        esac
      done
      
      cfg_file="/etc/netplan/01-on-premises.yaml"
      cat <<EOF | tee "$cfg_file" > /dev/null
network:
  version: 2
  ethernets:
    $iface:
      dhcp4: no
      addresses:
        - $ip_addr/$prefix
      routes:
        - to: default
          via: $gateway
      nameservers:
        addresses: [$nameservers]
EOF
      chmod 600 "$cfg_file"
      chown root:root "$cfg_file"
      netplan apply

    else # ifupdown
      # /etc/network/interfaces only, and not /etc/network/interfaces.d/$iface
      iface_file="/etc/network/interfaces"
      mkdir -p /etc/network/interfaces.d
      cat <<EOF | tee "$iface_file" > /dev/null
echo auto lo
iface lo inet loopback

auto $iface
iface $iface inet static
  address $ip_addr
  netmask $netmask
  gateway $gateway
  dns-nameservers $nameservers
EOF

      if ! command -v resolvconf >/dev/null 2>&1; then
        > /etc/resolv.conf
        for server in $(echo "$nameservers" | tr ',' ' '); do
          echo "nameserver $server" >> /etc/resolv.conf
        done
      fi
      
      # Restart interface
      ifdown "$iface" || true
      ifup "$iface"
    fi

  fi
}

# Set static or dynamic IP for the first interface
#  set_ip static 192.168.1.2 255.255.255.0 192.168.1.1 "8.8.8.8,8.8.4.4" domain
#  set_ip dhcp
set_ip() {
  local iface=$(get_ethernet_interface)
  set_iface_ip $iface $1 $2 $3 $4 "$5" "$6"
}

######################################
# End of network commands (2026-02-27)
######################################


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

if [ $# -ge 7 ]; then
  PARAM6="$7"
else
  PARAM6=""
fi

if [ $# -ge 8 ]; then
  PARAM7="$8"
else
  PARAM7=""
fi


##################
# START commands #
##################

if [[ "${COMMAND}" == "get-network-config" ]]; then
  # 5.10.2.1 deprecated, compatibility mode, please use get-iface-info
  echo $(date) DEBUG: get-network-config >> /var/log/multiotp.log
  get_network_config
  get_network_config >> /var/log/multiotp.log
  exit 0
elif [[ "${COMMAND}" == "get-iface-info" ]]; then
  get_iface_info
  exit 0
if [[ "${COMMAND}" == "set-ip" ]] && [[ ! "${PARAM1}" == "" ]]; then
  #  set_ip static 192.168.1.2 255.255.255.0 192.168.1.1 "8.8.8.8,8.8.4.4" domain
  #  set_ip dhcp
  if [[ ! "${PARAM1}" == "static" ]] && [[ ! "${PARAM1}" == "dhcp" ]];  then
    echo "Error. Must be 'dhcp' or 'static'"
    exit 1
  elif [[ "${PARAM1}" == "static" ]] && [[ "${PARAM5}" == "" ]];  then
    echo "Error. Not enough parameters"
    exit 1
  fi
  set_ip ${PARAM1} ${PARAM2} ${PARAM3} ${PARAM4} ${PARAM5} ${PARAM6}
  exit 0
elif [[ "${COMMAND}" == "reset-config" ]]; then
  # Reset the network interface (VM or RASPBERRY) and the DNS resolver
  set_ip static 192.168.1.44 255.255.255.0 192.168.1.1 "8.8.8.8,8.8.4.4" multiotp.local
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
    # Declare the CJE Micro?s RTC clock device (and other DSxxxx compatible RTC clock) on a Rev. 2 Board
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
          # Declare the CJE Micro?s RTC clock device (and other DSxxxx compatible RTC clock) on a Rev. 1 Board
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
else
  echo "This is a bash helper for multiOTP"
  echo "(family ${FAMILY}, type ${TYPE})"
  echo ""
  echo "Please call a supported command with valid argument(s)"
  echo " get-iface-info"
  echo " get-network-config (deprecated, use get-iface-info)"
  echo " help"
  echo " reset-config"
  echo " set-ip dhcp|static ip_addr subnet gateway \"dns1,dns2\" domain"
  echo " start-multiotp"
  echo " stop-multiotp"
  if [[ "${COMMAND}" == "help" ]]; then
    exit 0
  else
    exit 1
  fi
fi
