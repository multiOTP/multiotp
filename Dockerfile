##########################################################################
#
# @file   Dockerfile
# @brief  multiOTP open source docker image creator (based on Debian 8)
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
# @author    Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
# @version   5.2.0.2
# @date      2018-07-16
# @since     2013-11-29
# @copyright (c) 2013-2018 by SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
# docker build .
# docker run --mount source=multiotp-data,target=/etc/multiotp -p 80:80 -p 443:443 -p 1812:1812/udp -p 1813:1813/udp -d xxxxxxxxxxxx
#
# 2018-03-20 5.1.1.2 SysCo/al Initial public Dockerfile release
##########################################################################

# Debian 9 is NOT yet supported (FreeRADIUS 3.x adaptation in progress)

FROM debian:8
ENV DEBIAN 8
ENV PHPINSTALLPREFIX php5
ENV PHPINSTALLPREFIXSQLITE php5
ENV PHPVERSION 5

MAINTAINER Andre Liechti <andre.liechti@multiotp.net>
LABEL Description="multiOTP open source, running on Debian ${DEBIAN} with PHP${PHPVERSION}." \
      License="LGPLG-3.0" \
      Usage="docker run --mount source=[SOURCE PERSISTENT VOLUME],target=/etc/multiotp -p [HOST WWW PORT NUMBER]:80 -p [HOST SSL PORT NUMBER]:443 -p [HOST RADIUS-AUTH PORT NUMBER]:1812/udp -p [HOST RADIUS-ACCNT PORT NUMBER]:1813/udp -d multiotp-open-source" \
      Version="5.2.0.2"

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
    apt-offline \
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
    ${PHPINSTALLPREFIX}-cgi \
    ${PHPINSTALLPREFIX}-dev \
    ${PHPINSTALLPREFIX}-fpm \
    ${PHPINSTALLPREFIX}-gd \
    ${PHPINSTALLPREFIX}-gmp \
    ${PHPINSTALLPREFIX}-ldap \
    ${PHPINSTALLPREFIX}-mcrypt \
    ${PHPINSTALLPREFIXSQLITE}-sqlite \
    slapd \
    snmp \
    snmpd \
    sqlite \
    subversion \
    sudo \
    unzip \
    wget


############################################################
# Take online the latest version of multiOTP open source
# (if you want to build an image with the latest
#  available version instead of the local one)

RUN wget -q http://download.multiotp.net/multiotp.zip -O /tmp/multiotp.zip && \
    unzip -q -o /tmp/multiotp.zip -d /tmp/multiotp

RUN mv /tmp/multiotp/raspberry/boot-part/* /boot && \
    rm -rf /tmp/multiotp
############################################################


WORKDIR /

RUN chmod 777 /boot/*.sh && \
    /boot/install.sh

EXPOSE 80/tcp 443/tcp 1812/udp 1813/udp 

ENTRYPOINT /boot/newvm.sh
