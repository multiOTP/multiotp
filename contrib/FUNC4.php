<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                                         *
 *  XPertMailer is a PHP Mail Class that can send and read messages in MIME format.        *
 *  This file is part of the XPertMailer package (http://xpertmailer.sourceforge.net/)     *
 *  Copyright (C) 2007 Tanase Laurentiu Iulian                                             *
 *                                                                                         *
 *  This library is free software; you can redistribute it and/or modify it under the      *
 *  terms of the GNU Lesser General Public License as published by the Free Software       *
 *  Foundation; either version 2.1 of the License, or (at your option) any later version.  *
 *                                                                                         *
 *  This library is distributed in the hope that it will be useful, but WITHOUT ANY        *
 *  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A        *
 *  PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.        *
 *                                                                                         *
 *  You should have received a copy of the GNU Lesser General Public License along with    *
 *  this library; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, *
 *  Fifth Floor, Boston, MA 02110-1301, USA                                                *
 *                                                                                         *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

if (!defined('DISPLAY_XPM4_ERRORS')) define('DISPLAY_XPM4_ERRORS', true);

if (!function_exists('debug_backtrace')) {
	function debug_backtrace() {
		return array(0 => array('class' => 'unknown', 'type' => 'unknown', 'function' => 'unknown', 'file' => __FILE__, 'line' => __LINE__));
	}
}

class FUNC4 {

	function is_debug($debug) {
		return (is_array($debug) && isset($debug[0]['class'], $debug[0]['type'], $debug[0]['function'], $debug[0]['file'], $debug[0]['line']));
	}

	function microtime_float() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	function is_win() {
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}

	function log_errors($msg = null, $strip = false) {
		if (defined('LOG_XPM4_ERRORS')) {
			if (is_string(LOG_XPM4_ERRORS) && is_string($msg) && is_bool($strip)) {
				if (is_array($arr = unserialize(LOG_XPM4_ERRORS)) && isset($arr['type']) && is_int($arr['type']) && ($arr['type'] == 0 || $arr['type'] == 1 || $arr['type'] == 3)) {
					$msg = "\r\n".'['.date('m-d-Y H:i:s').'] XPM4 '.($strip ? str_replace(array('<br />', '<b>', '</b>', "\r\n"), '', $msg) : $msg);
					if ($arr['type'] == 0) error_log($msg);
					else if ($arr['type'] == 1 && isset($arr['destination'], $arr['headers']) && 
						is_string($arr['destination']) && strlen(trim($arr['destination'])) > 5 && count(explode('@', $arr['destination'])) == 2 && 
						is_string($arr['headers']) && strlen(trim($arr['headers'])) > 3) {
						error_log($msg, 1, trim($arr['destination']), trim($arr['headers']));
					} else if ($arr['type'] == 3 && isset($arr['destination']) && is_string($arr['destination']) && strlen(trim($arr['destination'])) > 1) {
						error_log($msg, 3, trim($arr['destination']));
					} else if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) trigger_error('invalid LOG_XPM4_ERRORS constant value', E_USER_WARNING);
				} else if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) trigger_error('invalid LOG_XPM4_ERRORS constant type', E_USER_WARNING);
			} else if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) trigger_error('invalid parameter(s) type', E_USER_WARNING);
		}
	}

	function trace($debug, $message = null, $level = 0, $ret = false) {
		if (FUNC4::is_debug($debug) && is_string($message) && ($level == 0 || $level == 1 || $level == 2)) {
			if ($level == 0) $mess = 'Error';
			else if ($level == 1) $mess = 'Warning';
			else if ($level == 2) $mess = 'Notice';
			$emsg = '<br /><b>'.$mess.'</b>: '.$message.
				' on '.strtoupper($debug[0]['class']).$debug[0]['type'].$debug[0]['function'].'()'.
				' in <b>'.$debug[0]['file'].'</b> on line <b>'.$debug[0]['line'].'</b><br />'."\r\n";
			FUNC4::log_errors($emsg, true);
			if ($level == 0) {
				if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) die($emsg);
				else exit;
			} else if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) echo $emsg;
		} else {
			$emsg = 'invalid debug parameters';
			FUNC4::log_errors(': '.$emsg, true);
			if ($level == 0) {
				if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) trigger_error($emsg, E_USER_ERROR);
				else exit;
			} else if (defined('DISPLAY_XPM4_ERRORS') && DISPLAY_XPM4_ERRORS == true) trigger_error($emsg, E_USER_WARNING);
		}
		return $ret;
	}

	function str_clear($str = null, $addrep = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		$err = array();
		$rep = array("\r", "\n", "\t");
		if (!is_string($str)) $err[] = 'invalid argument type';
		if ($addrep == null) $addrep = array();
		if (is_array($addrep)) {
			if (count($addrep) > 0) {
				foreach ($addrep as $strrep) {
					if (is_string($strrep) && $strrep != '') $rep[] = $strrep;
					else {
						$err[] = 'invalid array value';
						break;
					}
				}
			}
		} else $err[] = 'invalid array type';
		if (count($err) == 0) return ($str == '') ? '' : str_replace($rep, '', $str);
		else FUNC4::trace($debug, implode(', ', $err));
	}

	function is_alpha($str = null, $num = true, $add = '', $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		$err = array();
		if (!is_string($str)) $err[] = 'invalid argument type';
		if (!is_bool($num)) $err[] = 'invalid numeric type';
		if (!is_string($add)) $err[] = 'invalid additional type';
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			if ($str != '') {
				$lst = 'abcdefghijklmnoqprstuvwxyzABCDEFGHIJKLMNOQPRSTUVWXYZ'.$add;
				if ($num) $lst .= '1234567890';
				$len1 = strlen($str);
				$len2 = strlen($lst);
				$match = true;
				for ($i = 0; $i < $len1; $i++) {
					$found = false;
					for ($j = 0; $j < $len2; $j++) {
						if ($lst{$j} == $str{$i}) {
							$found = true;
							break;
						}
					}
					if (!$found) {
						$match = false;
						break;
					}
				}
				return $match;
			} else return false;
		}
	}

	function is_hostname($str = null, $addr = false, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		$err = array();
		if (!is_string($str)) $err[] = 'invalid hostname type';
		if (!is_bool($addr)) $err[] = 'invalid address type';
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			if (trim($str) != '' && FUNC4::is_alpha($str, true, '-.')) {
				if (count($exphost1 = explode('.', $str)) > 1 && !(strstr($str, '.-') || strstr($str, '-.'))) {
					$set = true;
					foreach ($exphost1 as $expstr1) {
						if ($expstr1 == '') {
							$set = false;
							break;
						}
					}
					if ($set) {
						foreach (($exphost2 = explode('-', $str)) as $expstr2) {
							if ($expstr2 == '') {
								$set = false;
								break;
							}
						}
					}
					$ext = $exphost1[count($exphost1)-1];
					$len = strlen($ext);
					if ($set && $len >= 2 && $len <= 6 && FUNC4::is_alpha($ext, false)) $ret = true;
				}
			}
			return ($ret && $addr && gethostbyname($str) == $str) ? false : $ret;
		}
	}

	function is_ipv4($str = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		if (is_string($str)) return (trim($str) != '' && ip2long($str) && count(explode('.', $str)) === 4);
		else FUNC4::trace($debug, 'invalid argument type');
	}

	function getmxrr_win($hostname = null, &$mxhosts, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		$mxhosts = array();
		if (!is_string($hostname)) FUNC4::trace($debug, 'invalid hostname type');
		else {
			$hostname = strtolower($hostname);
			if (FUNC4::is_hostname($hostname, true, $debug)) {
				$retstr = exec('nslookup -type=mx '.$hostname, $retarr);
				if ($retstr && count($retarr) > 0) {
					foreach ($retarr as $line) {
						if (preg_match('/.*mail exchanger = (.*)/', $line, $matches)) $mxhosts[] = $matches[1];
					}
				}
			} else FUNC4::trace($debug, 'invalid hostname value', 1);
			return (count($mxhosts) > 0);
		}
	}

	function is_mail($addr = null, $vermx = false, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		$err = array();
		if (!is_string($addr)) $err[] = 'invalid address type';
		if (!is_bool($vermx)) $err[] = 'invalid MX type';
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = (count($exp = explode('@', $addr)) === 2 && $exp[0] != '' && $exp[1] != '' && FUNC4::is_alpha($exp[0], true, '_-.+') && (FUNC4::is_hostname($exp[1]) || FUNC4::is_ipv4($exp[1])));
			if ($ret && $vermx) {
				if (FUNC4::is_ipv4($exp[1])) $ret = false;
				else $ret = FUNC4::is_win() ? FUNC4::getmxrr_win($exp[1], $mxh, $debug) : getmxrr($exp[1], $mxh);
			}
			return $ret;
		}
	}

	function mime_type($name = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		if (!is_string($name)) FUNC4::trace($debug, 'invalid filename type');
		else {
			$name = FUNC4::str_clear($name);
			$name = trim($name);
			if ($name == '') return FUNC4::trace($debug, 'invalid filename value', 1);
			else {
				$ret = 'application/octet-stream';
				$arr = array(
					'z'    => 'application/x-compress', 
					'xls'  => 'application/x-excel', 
					'gtar' => 'application/x-gtar', 
					'gz'   => 'application/x-gzip', 
					'cgi'  => 'application/x-httpd-cgi', 
					'php'  => 'application/x-httpd-php', 
					'js'   => 'application/x-javascript', 
					'swf'  => 'application/x-shockwave-flash', 
					'tar'  => 'application/x-tar', 
					'tgz'  => 'application/x-tar', 
					'tcl'  => 'application/x-tcl', 
					'src'  => 'application/x-wais-source', 
					'zip'  => 'application/zip', 
					'kar'  => 'audio/midi', 
					'mid'  => 'audio/midi', 
					'midi' => 'audio/midi', 
					'mp2'  => 'audio/mpeg', 
					'mp3'  => 'audio/mpeg', 
					'mpga' => 'audio/mpeg', 
					'ram'  => 'audio/x-pn-realaudio', 
					'rm'   => 'audio/x-pn-realaudio', 
					'rpm'  => 'audio/x-pn-realaudio-plugin', 
					'wav'  => 'audio/x-wav', 
					'bmp'  => 'image/bmp', 
					'fif'  => 'image/fif', 
					'gif'  => 'image/gif', 
					'ief'  => 'image/ief', 
					'jpe'  => 'image/jpeg', 
					'jpeg' => 'image/jpeg', 
					'jpg'  => 'image/jpeg', 
					'png'  => 'image/png', 
					'tif'  => 'image/tiff', 
					'tiff' => 'image/tiff', 
					'css'  => 'text/css', 
					'htm'  => 'text/html', 
					'html' => 'text/html', 
					'txt'  => 'text/plain', 
					'rtx'  => 'text/richtext', 
					'vcf'  => 'text/x-vcard', 
					'xml'  => 'text/xml', 
					'xsl'  => 'text/xsl', 
					'mpe'  => 'video/mpeg', 
					'mpeg' => 'video/mpeg', 
					'mpg'  => 'video/mpeg', 
					'mov'  => 'video/quicktime', 
					'qt'   => 'video/quicktime', 
					'asf'  => 'video/x-ms-asf', 
					'asx'  => 'video/x-ms-asf', 
					'avi'  => 'video/x-msvideo', 
					'vrml' => 'x-world/x-vrml', 
					'wrl'  => 'x-world/x-vrml');
				if (count($exp = explode('.', $name)) >= 2) {
					$ext = strtolower($exp[count($exp)-1]);
					if (trim($exp[count($exp)-2]) != '' && isset($arr[$ext])) $ret = $arr[$ext];
				}
				return $ret;
			}
		}
	}

}

?>