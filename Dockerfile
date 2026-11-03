##########################################################################
#
# @file   Dockerfile
# @brief  multiOTP open source docker image creator
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
# @version   5.10.4.2
# @date      2026-10-31
# @since     2013-11-29
# @copyright (c) 2013-2026 SysCo systemes de communication sa
# @copyright GNU Lesser General Public License
#
# docker build .
# docker run -v [PATH/TO/MULTIOTP/DATA/VOLUME]:/etc/multiotp -v [PATH/TO/FREERADIUS/CONFIG/VOLUME]:/etc/freeradius -v [PATH/TO/MULTIOTP/LOG/VOLUME]:/var/log/multiotp -v [PATH/TO/FREERADIUS/LOG/VOLUME]:/var/log/freeradius -p [HOST WWW PORT NUMBER]:80 -p [HOST SSL PORT NUMBER]:443 -p [HOST RADIUS-AUTH PORT NUMBER]:1812/udp -p [HOST RADIUS-ACCNT PORT NUMBER]:1813/udp -d xxxxxxxxxxxx
#
# Hint: If you want to sync regularly the AD/LDAP users, you can launch the following task using a Task Scheduler:
#       docker exec multiotp-open-source /usr/local/bin/multiotp/multiotp.php -ldap-users-sync
#
# Change Log
#
# Please check the readme file for the whole change log since 2010
#
##########################################################################

FROM --platform=$BUILDPLATFORM debian:trixie-slim
ENV DEBIAN=13
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Europe/Zurich
ENV PHPINSTALLPREFIX=php
ENV PHPINSTALLPREFIXVERSION=php8.4
ENV PHPVERSION=8.4
ENV SQLITEVERSION=sqlite3

LABEL org.opencontainers.image.title="multiOTP open source"
LABEL org.opencontainers.image.description="multiOTP open source, running on Debian ${DEBIAN} with PHP${PHPVERSION}." \
      License="LGPL-3.0" \
      Usage="docker run -v [PATH/TO/MULTIOTP/DATA/VOLUME]:/etc/multiotp -v [PATH/TO/FREERADIUS/CONFIG/VOLUME]:/etc/freeradius -v [PATH/TO/MULTIOTP/LOG/VOLUME]:/var/log/multiotp -v [PATH/TO/FREERADIUS/LOG/VOLUME]:/var/log/freeradius -p [HOST WWW PORT NUMBER]:80 -p [HOST SSL PORT NUMBER]:443 -p [HOST RADIUS-AUTH PORT NUMBER]:1812/udp -p [HOST RADIUS-ACCNT PORT NUMBER]:1813/udp -d multiotp-open-source" \
      Version="5.10.4.2"
LABEL org.opencontainers.image.Version="5.10.4.2"
LABEL org.opencontainers.image.authors="Andre Liechti <andre.liechti@multiotp.net>"
LABEL org.opencontainers.image.url="https://www.multiotp.net"
LABEL org.opencontainers.image.source="https://github.com/multiOTP/multiotp"

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
    tcpdump \
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
    /boot/install.sh RUNDOCKER

EXPOSE 80/tcp 443/tcp 1812/udp 1813/udp

VOLUME /etc/multiotp /etc/freeradius /var/log/multiotp /var/log/freeradius

ENTRYPOINT ["/boot/newvm.sh", "RUNDOCKER"]
