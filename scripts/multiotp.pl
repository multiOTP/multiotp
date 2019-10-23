########################################
# @file  multiotp.pl
# @brief Perl script used to add challenge-response mechanism to multiOTP open source.
#
# multiOTP package - Strong two-factor authentication open source package
# multiOTP is OATH certified for TOTP/HOTP
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
# @author    SysCo/yj, SysCo/al, SysCo systemes de communication sa, <info@multiotp.net>
# @version   5.6.1.5
# @date      2019-10-23
# @since     2014-08-14
# @copyright (c) 2014-2018 by SysCo systemes de communication sa
# @copyright (c) 2002 by Boian Jordanov <bjordanov@orbitel.bg>
# @copyright (c) 2002 by The FreeRADIUS server project
# @copyright GNU Lesser General Public License
#
#  This file is part of the multiOTP project.
#
#  multiotp.pl is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
#  multiotp.pl is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Lesser General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.
#  If not, see <http://www.gnu.org/licenses/>.
#
# Based on the Example code for use with rlm_perl
#
#
# Change Log
#   2019-01-24 5.4.1.5 SysCo/al All parameters are now between ''
#   2019-01-07 5.4.1.1 SysCo/al FreeRADIUS 3 support
#   2016-10-16 5.0.2.5 SysCo/al Unified version number
#   2015-07-28 4.3.2.6 SysCo/al Clean comments
#   2014-12-10 4.3.1.0 SysCo/yj Initial implementation
########################################


=head1 NAME

multiotp.pl - Perl module for use with FreeRADIUS rlm_perl, to authenticate against 
 multiOTP open source   http://www.multiOTP.net

=head1 SYNOPSIS

   use with freeradius:  
   
   Configure rlm_perl to work with multiOTP:
   in /etc/freeradius/users 
    set:
     DEFAULT Auth-type = Perl

  in /etc/freeradius/modules/perl, change
     perl {
         module = 
  to call this file

  in /etc/freeradius/sites-enabled/<yoursite>
  set
  authenticate{
    perl
    [....]

=head1 DESCRIPTION

This script enables FreeRADIUS to authenticate using multiOTP.

=head2 Methods

   * authenticate

=head1 AUTHOR

SysCo systemes de communication sa (info@multiotp.net)

=head1 COPYRIGHT

Copyright 2014-2018

This library is free software; you can redistribute it 
under the GPLv2.

=head1 SEE ALSO

perl(1).

=cut

use strict;
# use ...
# This is very important ! Without this script will not get the filled hashesh from main.
use vars qw(%RAD_REQUEST %RAD_CONFIG %RAD_REPLY %RAD_CHECK $URL);
use Data::Dumper;

# This is hash wich hold original request from radius
#my %RAD_REQUEST;
# In this hash you add values that will be returned to NAS.
#my %RAD_REPLY;
#This is for check items
#my %RAD_CHECK;

#
# This the remapping of return values
#
       use constant    RLM_MODULE_REJECT=>    0;#  /* immediately reject the request */
       use constant    RLM_MODULE_FAIL=>      1;#  /* module failed, don't reply */
       use constant    RLM_MODULE_OK=>        2;#  /* the module is OK, continue */
       use constant    RLM_MODULE_HANDLED=>   3;#  /* the module handled the request, so stop. */
       use constant    RLM_MODULE_INVALID=>   4;#  /* the module considers the request invalid. */
       use constant    RLM_MODULE_USERLOCK=>  5;#  /* reject the request (user is locked out) */
       use constant    RLM_MODULE_NOTFOUND=>  6;#  /* user not found */
       use constant    RLM_MODULE_NOOP=>      7;#  /* module succeeded without doing anything */
       use constant    RLM_MODULE_UPDATED=>   8;#  /* OK (pairs modified) */
       use constant    RLM_MODULE_NUMCODES=>  9;#  /* How many return codes there are */

# Function to handle authorize
sub authorize {
        $RAD_CHECK{'Auth-Type'} = "Perl";
        $RAD_CHECK{'Fall-Through'} = "yes";
        return RLM_MODULE_OK;
}

# Function to handle authenticate
sub authenticate {

        # print Dumper(%RAD_REQUEST);
        # print Dumper(%RAD_CONFIG);

        my $output=`/usr/local/bin/multiotp/multiotp.php -base-dir='/usr/local/bin/multiotp/' '$RAD_REQUEST{'User-Name'}' '$RAD_REQUEST{'User-Password'}' -src='$RAD_CONFIG{'Packet-Src-IP-Address'}' -tag='$RAD_CONFIG{'Client-Shortname'}' -mac='$RAD_REQUEST{'Called-Station-Id'}' -calling-ip='$RAD_REQUEST{'Framed-IP-Address'}' -calling-mac='$RAD_REQUEST{'Calling-Station-Id'}' -chap-challenge='$RAD_REQUEST{'CHAP-Challenge'}' -chap-password='$RAD_REQUEST{'CHAP-Password'}' -ms-chap-challenge='$RAD_REQUEST{'MS-CHAP-Challenge'}' -ms-chap-response='$RAD_REQUEST{'MS-CHAP-Response'}' -ms-chap2-response='$RAD_REQUEST{'MS-CHAP2-Response'}' -state='$RAD_REQUEST{'State'}'`;
        
        # Clean the \r and \n
        $output =~ s/\r$|\n$//ig;
 
        # Take the exit value of the external script call
        my $exit_val = $? >> 8;

        # Split the output with ; multiOTP echoes a string of the form : option1=value;option2=value
        my @multiotpReturnedValues = split(',', $output);
	 
        foreach my $val (@multiotpReturnedValues) {
          # Split the option and the value in a maximum of two parts
          my @params = split('=', $val,2);
          # How many items are in the array
          my $arraySize = scalar (@params);
          # If there are two elements in the array, put them in the radius reply ?
          if($arraySize == 2) {
            # Clean the ':' before the split (param0)
            $params[0] =~ s/:$//ig;
            # Clean the '+' before the split (param0)
            $params[0] =~ s/\+$//ig;
            # Trim the string (param0)
            $params[0] =~ s/^\s+|\s+$//g;
            # Trim the string (param1)
            $params[1] =~ s/^\s+|\s+$//g;
            # remove the external "" (param1)
            $params[1] =~ s/^"+|\"$//g;
            # Clean the \r and \n (param1)
            $params[1] =~ s/\r$|\n$//ig;
            $RAD_REPLY{$params[0]} = $params[1];
          }
        }

        if ($exit_val == 0)
        {
          $RAD_CHECK{'Response-Packet-Type'} = "Access-Accept";
        }        
        elsif (($exit_val == 9) || ($exit_val == 10))
        {
          $RAD_CHECK{'Response-Packet-Type'} = "Access-Challenge";
        }
        else
        {
          $RAD_CHECK{'Response-Packet-Type'} = "Access-Reject";
        }
        return RLM_MODULE_HANDLED;
}

# Function to handle preacct
sub preacct {
       return RLM_MODULE_OK;
}

# Function to handle accounting
sub accounting {
       return RLM_MODULE_OK;
}

# Function to handle checksimul
sub checksimul {
       return RLM_MODULE_OK;
}

# Function to handle pre_proxy
sub pre_proxy {
       return RLM_MODULE_OK;
}

# Function to handle post_proxy
sub post_proxy {
       return RLM_MODULE_OK;
}

# Function to handle post_auth
sub post_auth {
       return RLM_MODULE_OK;
}
