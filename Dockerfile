##########################################################################
#
# @file   Dockerfile
# @brief  multiOTP open source docker image creator (based on Debian 8)
# 
# multiOTP package - Strong two-factor authentication open source package
# https://www\.multiOTP.net/
#
# The multiOTP package is the lightest package available that provides so many
# strong authentication functionalities and goodies, and best of all, for anyone
# that is interested about security issues, it's a fully open source solution!
#
# This package is the result of a *LOT* of work. If you are happy using this
# package, [Donation] are always welcome to support this project.
# Please check https://www\.multiOTP.net/ and you will find the magic button ;-)
#
# @author    Andre Liechti, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.9.1.0
# @date      2022-06-17
# @since     2013-11-29
# @copyright (c) 2013-2022 SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
# docker build .
# docker run -v [PATH/TO/MULTIOTP/DATA/VOLUME]:/etc/multiotp -v [PATH/TO/FREERADIUS/CONFIG/VOLUME]:/etc/freeradius -v [PATH/TO/MULTIOTP/LOG/VOLUME]:/var/log/multiotp -v [PATH/TO/FREERADIUS/LOG/VOLUME]:/var/log/freeradius -p [HOST WWW PORT NUMBER]:80 -p [HOST SSL PORT NUMBER]:443 -p [HOST RADIUS-AUTH PORT NUMBER]:1812/udp -p [HOST RADIUS-ACCNT PORT NUMBER]:1813/udp -d xxxxxxxxxxxx
#
# 2022-05-08 5.8.8.4 SysCo/al Better docker support (also for Synology)
# 2022-05-08 5.8.8.1 SysCo/al Add Raspberry Pi Bullseye 11.0 support
# 2021-09-14 5.8.3.0 SysCo/al Debian Bullseye 11.0 support
# 2021-05-19 5.8.2.3 SysCo/al Added php-bcmath
# 2021-03-25 5.8.1.9 SysCo/al Remove apt-offline, which is not used
# 2020-08-31 5.8.0.0 SysCo/al Debian Buster 10.5 support
# 2019-10-22 5.6.1.3 SysCo/al Debian 10 support
# 2019-01-07 5.4.1.1 SysCo/al Debian 9 support
# 2018-03-20 5.1.1.2 SysCo/al Initial public Dockerfile release
##########################################################################

FROM debian:11
ENV DEBIAN 11
ENV PHPINSTALLPREFIX php
ENV PHPINSTALLPREFIXVERSION php7.4
ENV PHPVERSION 7.4
ENV SQLITEVERSION sqlite3

MAINTAINER Andre Liechti <andre.liechti@multiotp.net>
LABEL Description="multiOTP open source, running on Debian ${DEBIAN} with PHP${PHPVERSION}." \
      License="LGPL-3.0" \
      Usage="docker run -v [PATH/TO/MULTIOTP/DATA/VOLUME]:/etc/multiotp -v [PATH/TO/FREERADIUS/CONFIG/VOLUME]:/etc/freeradius -v [PATH/TO/MULTIOTP/LOG/VOLUME]:/var/log/multiotp -v [PATH/TO/FREERADIUS/LOG/VOLUME]:/var/log/freeradius -p [HOST WWW PORT NUMBER]:80 -p [HOST SSL PORT NUMBER]:443 -p [HOST RADIUS-AUTH PORT NUMBER]:1812/udp -p [HOST RADIUS-ACCNT PORT NUMBER]:1813/udp -d multiotp-open-source" \
      Version="5.9.1.0"

ARG DEBIAN_FRONTEND=noninteractive

RUN echo slapd slapd/internal/adminpw password rtzewrpiZRT753 | debconf-set-selections; \
    echo slapd slapd/internal/generated_adminpw password rtzewrpiZRT753 | debconf-set-selections; \
    echo slapd slapd/password2 password rtzewrpiZRT753 | debconf-set-selections; \
    echo slapd slapd/password1 password rtzewrpiZRT753 | debconf-set-selections;

# Make sure you run apt-get update in the same line with
# all the packages to ensure all are updated correctly.
# (https://runnable.com/blog/9-common-dockerfile-mistakes)
RUN apt-get update && \
    apt-get install -y \
    apache2-utils \
    apt-utils \
    build-essential \
    bzip2 \
    dialog \
    dselect \
    freeradius \
    initramfs-tools \
    ldap-utils \
    libbz2-dev \
    logrotate \
    nano \
    net-tools \
    nginx-extras \
    ntp \
    p7zip-full \
    php-pear \
    ${PHPINSTALLPREFIX}-bcmath \
    ${PHPINSTALLPREFIX}-cgi \
    ${PHPINSTALLPREFIX}-dev \
    ${PHPINSTALLPREFIX}-fpm \
    ${PHPINSTALLPREFIX}-gd \
    ${PHPINSTALLPREFIX}-gmp \
    ${PHPINSTALLPREFIX}-ldap \
    ${PHPINSTALLPREFIXVERSION}-${SQLITEVERSION} \
    slapd \
    snmp \
    snmpd \
    ${SQLITEVERSION} \
    subversion \
    sudo \
    unzip \
    wget \
    ${PHPINSTALLPREFIX}-mbstring


############################################################
# Offline local docker image creation
############################################################
COPY raspberry/boot-part/*.sh /boot/
COPY raspberry/boot-part/multiotp-tree /boot/multiotp-tree/


############################################################
# Take online the latest version of multiOTP open source
# (if you want to build an image with the latest
#  available version instead of the local one)
#
# RUN wget -q https://download.multiotp.net/multiotp.zip -O /tmp/multiotp.zip && \
#     unzip -q -o /tmp/multiotp.zip -d /tmp/multiotp
# 
# RUN mv /tmp/multiotp/raspberry/boot-part/* /boot && \
#     rm -rf /tmp/multiotp
############################################################


WORKDIR /

RUN chmod 777 /boot/*.sh && \
    /boot/install.sh && \
    /boot/newvm.sh INIT

EXPOSE 80/tcp 443/tcp 1812/udp 1813/udp

VOLUME /etc/multiotp /etc/freeradius /var/log/multiotp /var/log/freeradius

ENTRYPOINT /boot/newvm.sh RUNDOCKER
