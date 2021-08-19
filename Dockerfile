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
# @version   5.8.2.9
# @date      2021-08-19
# @since     2013-11-29
# @copyright (c) 2013-2021 SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
# docker build .
# docker run --mount source=multiotp-data,target=/etc/multiotp -p 80:80 -p 443:443 -p 1812:1812/udp -p 1813:1813/udp -d xxxxxxxxxxxx
#
# 2021-05-19 5.8.2.3 SysCo/al Added php-bcmath
# 2021-03-25 5.8.1.9 SysCo/al Remove apt-offline, which is not used
# 2020-08-31 5.8.0.0 SysCo/al Debian Buster 10.5 support
# 2019-10-22 5.6.1.3 SysCo/al Debian 10 support
# 2019-01-07 5.4.1.1 SysCo/al Debian 9 support
# 2018-03-20 5.1.1.2 SysCo/al Initial public Dockerfile release
##########################################################################

FROM debian:10
ENV DEBIAN 10
ENV PHPINSTALLPREFIX php
ENV PHPINSTALLPREFIXSQLITE php7.3
ENV PHPVERSION 7.3

MAINTAINER Andre Liechti <andre.liechti@multiotp.net>
LABEL Description="multiOTP open source, running on Debian ${DEBIAN} with PHP${PHPVERSION}." \
      License="LGPL-3.0" \
      Usage="docker run --mount source=[SOURCE PERSISTENT VOLUME],target=/etc/multiotp -p [HOST WWW PORT NUMBER]:80 -p [HOST SSL PORT NUMBER]:443 -p [HOST RADIUS-AUTH PORT NUMBER]:1812/udp -p [HOST RADIUS-ACCNT PORT NUMBER]:1813/udp -d multiotp-open-source" \
      Version="5.8.2.9"

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
    ${PHPINSTALLPREFIXSQLITE}-sqlite \
    slapd \
    snmp \
    snmpd \
    sqlite \
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
    /boot/install.sh

EXPOSE 80/tcp 443/tcp 1812/udp 1813/udp 

ENTRYPOINT /boot/newvm.sh
