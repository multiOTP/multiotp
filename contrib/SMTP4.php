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

if (!class_exists('MIME4')) require_once 'MIME4.php';

$_RESULT = array();

class SMTP4 {

	var $CRLF = "\r\n";
	var $PORT = 25;
	var $TOUT = 30;
	var $COUT = 5;
	var $BLEN = 1024;

	function _cres($conn = null, &$resp, $code1 = null, $code2 = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		$err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!(is_int($code1) && $code1 > 99 && $code1 < 1000)) $err[] = 'invalid 1 code value';
		if ($code2 != null) {
			if (!(is_int($code2) && $code2 > 99 && $code2 < 1000)) $err[] = 'invalid 2 code value';
		}
		if (count($err) > 0) return FUNC4::trace($debug, implode(', ', $err), 1);
		else {
			$_smtp = new SMTP4;
			$ret = true;
			do {
				if ($result = fgets($conn, $_smtp->BLEN)) {
					$resp[] = $result;
					$rescode = substr($result, 0, 3);
					if (!($rescode == $code1 || $rescode == $code2)) {
						$ret = false;
						break;
					}
				} else {
					$resp[] = 'can not read';
					$ret = false;
					break;
				}
			} while ($result[3] == '-');
			return $ret;
		}
	}

	function mxconnect($host = null, $port = null, $tout = null, $name = null, $context = null, $debug = null) {
		global $_RESULT;
		$_RESULT = array();
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		if (!is_string($host)) FUNC4::trace($debug, 'invalid host type');
		else {
			$host = strtolower(trim($host));
			if (!($host != '' && FUNC4::is_hostname($host, true, $debug))) FUNC4::trace($debug, 'invalid host value');
		}
		$res = FUNC4::is_win() ? FUNC4::getmxrr_win($host, $arr, $debug) : getmxrr($host, $arr);
		$con = false;
		if ($res) {
			foreach ($arr as $mx) {
				if ($con = SMTP4::connect($mx, $port, null, null, null, $tout, $name, $context, null, $debug)) break;
			}
		}
		if (!$con) $con = SMTP4::connect($host, $port, null, null, null, $tout, $name, $context, null, $debug);
		return $con;
	}
	
	function connect($host = null, $port = null, $user = null, $pass = null, $vssl = null, $tout = null, $name = null, $context = null, $login = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		$_smtp = new SMTP4;
		if ($port == null) $port = $_smtp->PORT;
		if ($tout == null) $tout = $_smtp->TOUT;
		if (!is_string($host)) $err[] = 'invalid host type';
		else {
			$host = strtolower(trim($host));
			if (!($host != '' && ($host == 'localhost' || FUNC4::is_ipv4($host) || FUNC4::is_hostname($host, true, $debug)))) $err[] = 'invalid host value';
		}
		if (!(is_int($port) && $port > 0)) $err[] = 'invalid port value';
		if ($user != null) {
			if (!is_string($user)) $err[] = 'invalid username type';
			else if (($user = FUNC4::str_clear($user)) == '') $err[] = 'invalid username value';
		}
		if ($pass != null) {
			if (!is_string($pass)) $err[] = 'invalid password type';
			else if (($pass = FUNC4::str_clear($pass)) == '') $err[] = 'invalid password value';
		}
		if (($user != null && $pass == null) || ($user == null && $pass != null)) $err[] = 'invalid username/password combination';
		if ($vssl != null) {
			if (!is_string($vssl)) $err[] = 'invalid ssl version type';
			else {
				$vssl = strtolower($vssl);
				if (!($vssl == 'tls' || $vssl == 'ssl' || $vssl == 'sslv2' || $vssl == 'sslv3')) $err[] = 'invalid ssl version value';
			}
		}
		if (!(is_int($tout) && $tout > 0)) $err[] = 'invalid timeout value';
		if ($name != null) {
			if (!is_string($name)) $err[] = 'invalid name type';
			else {
				$name = strtolower(trim($name));
				if (!($name != '' && ($name == 'localhost' || FUNC4::is_ipv4($name) || FUNC4::is_hostname($name, true, $debug)))) $err[] = 'invalid name value';
			}
		} else $name = '127.0.0.1';
		if ($context != null && !is_resource($context)) $err[] = 'invalid context type';
		if ($login != null) {
			$login = strtolower(trim($login));
			if (!($login == 'login' || $login == 'plain' || $login == 'cram-md5')) $err[] = 'invalid authentication type value';
		}
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$prt = ($vssl == null) ? 'tcp' : $vssl;
			$conn = ($context == null) ? fsockopen($prt.'://'.$host, $port, $errno, $errstr, $tout) : fsockopen($prt.'://'.$host, $port, $errno, $errstr, $tout, $context);
			if (!$conn) $_RESULT[101] = $errstr;
			else if (!socket_set_timeout($conn, $_smtp->COUT)) $_RESULT[102] = 'could not set socket timeout';
			else if (!SMTP4::_cres($conn, $resp, 220, null, $debug)) $_RESULT[103] = $resp;
			else {
				$continue = true;
				if (!SMTP4::ehlo($conn, $name, $debug)) $continue = SMTP4::helo($conn, $name, $debug);
				if ($continue) {
					if ($user == null) $ret = true;
					else if ($login != null) $ret = SMTP4::auth($conn, $user, $pass, $login, $debug);
					else {
						list($code, $arr) = each($_RESULT);
						$auth['default'] = $auth['login'] = $auth['plain'] = $auth['cram-md5'] = false;
						foreach ($arr as $line) {
							if (substr($line, 0, strlen('250-AUTH ')) == '250-AUTH ') {
								foreach (explode(' ', substr($line, strlen('250-AUTH '))) as $type) {
									$type = strtolower(trim($type));
									if ($type == 'login' || $type == 'plain' || $type == 'cram-md5') $auth[$type] = true;
								}
							} else if (substr($line, 0, strlen('250 AUTH=')) == '250 AUTH=') {
								$expl = explode(' ', strtolower(trim(substr($line, strlen('250 AUTH=')))), 2);
								if ($expl[0] == 'login' || $expl[0] == 'plain' || $expl[0] == 'cram-md5') $auth['default'] = $expl[0];
							}
						}
						if ($auth['default']) $ret = SMTP4::auth($conn, $user, $pass, $auth['default'], $debug);
						if (!$ret && $auth['login'] && $auth['default'] != 'login') $ret = SMTP4::auth($conn, $user, $pass, 'login', $debug);
						if (!$ret && $auth['plain'] && $auth['default'] != 'plain') $ret = SMTP4::auth($conn, $user, $pass, 'plain', $debug);
						if (!$ret && $auth['cram-md5'] && $auth['default'] != 'cram-md5') $ret = SMTP4::auth($conn, $user, $pass, 'cram-md5', $debug);
						if (!$ret && !$auth['login'] && $auth['default'] != 'login') $ret = SMTP4::auth($conn, $user, $pass, 'login', $debug);
						if (!$ret && !$auth['plain'] && $auth['default'] != 'plain') $ret = SMTP4::auth($conn, $user, $pass, 'plain', $debug);
						if (!$ret && !$auth['cram-md5'] && $auth['default'] != 'cram-md5') $ret = SMTP4::auth($conn, $user, $pass, 'cram-md5', $debug);
					}
				}
			}
			if (!$ret) {
				if (is_resource($conn)) SMTP4::disconnect($conn, $debug);
				$conn = false;
			}
			return $conn;
		}
	}

	function send($conn = null, $addrs = null, $mess = null, $from = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!is_array($addrs)) $err[] = 'invalid to address type';
		else {
			$aver = true;
			if (count($addrs) > 0) {
				foreach ($addrs as $addr) {
					if (!FUNC4::is_mail($addr)) {
						$aver = false;
						break;
					}
				}
			} else $aver = false;
			if (!$aver) $err[] = 'invalid to address value';
		}
		if (!is_string($mess)) $err[] = 'invalid message value';
		if ($from == null) {
			$from = @ini_get('sendmail_from');
			if ($from == '' || !FUNC4::is_mail($from)) $from = (isset($_SERVER['SERVER_ADMIN']) && FUNC4::is_mail($_SERVER['SERVER_ADMIN'])) ? $_SERVER['SERVER_ADMIN'] : 'postmaster@localhost';
		} else {
			if (!is_string($from)) $err[] = 'invalid from address type';
			else if (!($from != '' && FUNC4::is_mail($from))) $err[] = 'invalid from address value';
		}
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			if (SMTP4::from($conn, $from, $debug)) {
				$continue = true;
				foreach ($addrs as $dest) {
					if (!SMTP4::to($conn, $dest, $debug)) {
						$continue = false;
						break;
					}
				}
				if ($continue) {
					if (SMTP4::data($conn, $mess, $debug)) $ret = SMTP4::rset($conn, $debug);
				}
			}
			return $ret;
		}
	}

	function disconnect($conn = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = array();
		if (!is_resource($conn)) return FUNC4::trace($debug, 'invalid resource connection', 1);
		else {
			$_smtp = new SMTP4;
			if (!fwrite($conn, 'QUIT'.$_smtp->CRLF)) $_RESULT[300] = 'can not write';
			else $_RESULT[301] = 'Send QUIT';
			return @fclose($conn);
		}
	}

	function quit($conn = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = array();
		$ret = false;
		$_smtp = new SMTP4;
		if (!is_resource($conn)) FUNC4::trace($debug, 'invalid resource connection');
		else if (!fwrite($conn, 'QUIT'.$_smtp->CRLF)) $_RESULT[302] = 'can not write';
		else {
			$_RESULT[303] = ($vget = @fgets($conn, $_smtp->BLEN)) ? $vget : 'can not read';
			$ret = true;
		}
		return $ret;
	}

	function helo($conn = null, $host = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!is_string($host)) $err[] = 'invalid host type';
		else {
			$host = strtolower(trim($host));
			if (!($host != '' && ($host == 'localhost' || FUNC4::is_ipv4($host) || FUNC4::is_hostname($host, true, $debug)))) $err[] = 'invalid host value';
		}
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$_smtp = new SMTP4;
			if (!fwrite($conn, 'HELO '.$host.$_smtp->CRLF)) $_RESULT[304] = 'can not write';
			else if (!SMTP4::_cres($conn, $resp, 250, null, $debug)) $_RESULT[305] = $resp;
			else {
				$_RESULT[306] = $resp;
				$ret = true;
			}
			return $ret;
		}
	}

	function ehlo($conn = null, $host = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!is_string($host)) $err[] = 'invalid host type';
		else {
			$host = strtolower(trim($host));
			if (!($host != '' && ($host == 'localhost' || FUNC4::is_ipv4($host) || FUNC4::is_hostname($host, true, $debug)))) $err[] = 'invalid host value';
		}
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$_smtp = new SMTP4;
			if (!fwrite($conn, 'EHLO '.$host.$_smtp->CRLF)) $_RESULT[307] = 'can not write';
			else if (!SMTP4::_cres($conn, $resp, 250, null, $debug)) $_RESULT[308] = $resp;
			else {
				$_RESULT[309] = $resp;
				$ret = true;
			}
			return $ret;
		}
	}

	function auth($conn = null, $user = null, $pass = null, $type = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!is_string($user)) $err[] = 'invalid username type';
		else if (($user = FUNC4::str_clear($user)) == '') $err[] = 'invalid username value';
		if (!is_string($pass)) $err[] = 'invalid password type';
		else if (($pass = FUNC4::str_clear($pass)) == '') $err[] = 'invalid password value';
		if ($type == null) $type = 'login';
		if (!is_string($type)) $err[] = 'invalid authentication type';
		else {
			$type = strtolower(trim($type));
			if (!($type == 'login' || $type == 'plain' || $type == 'cram-md5')) $err[] = 'invalid authentication type value';
		}
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$_smtp = new SMTP4;
			if ($type == 'login') {
				if (!fwrite($conn, 'AUTH LOGIN'.$_smtp->CRLF)) $_RESULT[310] = 'can not write';
				else if (!SMTP4::_cres($conn, $resp, 334, null, $debug)) $_RESULT[311] = $resp;
				else if (!fwrite($conn, base64_encode($user).$_smtp->CRLF)) $_RESULT[312] = 'can not write';
				else if (!SMTP4::_cres($conn, $resp, 334, null, $debug)) $_RESULT[313] = $resp;
				else if (!fwrite($conn, base64_encode($pass).$_smtp->CRLF)) $_RESULT[314] = 'can not write';
				else if (!SMTP4::_cres($conn, $resp, 235, null, $debug)) $_RESULT[315] = $resp;
				else {
					$_RESULT[316] = $resp;
					$ret = true;
				}
			} else if ($type == 'plain') {
				if (!fwrite($conn, 'AUTH PLAIN '.base64_encode($user.chr(0).$user.chr(0).$pass).$_smtp->CRLF)) $_RESULT[317] = 'can not write';
				else if (!SMTP4::_cres($conn, $resp, 235, null, $debug)) $_RESULT[318] = $resp;
				else {
					$_RESULT[319] = $resp;
					$ret = true;
				}
			} else if ($type == 'cram-md5') {
				if (!fwrite($conn, 'AUTH CRAM-MD5'.$_smtp->CRLF)) $_RESULT[200] = 'can not write';
				else if (!SMTP4::_cres($conn, $resp, 334, null, $debug)) $_RESULT[201] = $resp;
				else {
					if (strlen($pass) > 64) $pass = pack('H32', md5($pass));
					if (strlen($pass) < 64) $pass = str_pad($pass, 64, chr(0));
					$pad1 = substr($pass, 0, 64) ^ str_repeat(chr(0x36), 64);
					$pad2 = substr($pass, 0, 64) ^ str_repeat(chr(0x5C), 64);
					$chal = substr($resp[count($resp)-1], 4);
					$innr = pack('H32', md5($pad1.base64_decode($chal)));
					if (!fwrite($conn, base64_encode($user.' '.md5($pad2.$innr)).$_smtp->CRLF)) $_RESULT[202] = 'can not write';
					else if (!SMTP4::_cres($conn, $resp, 235, null, $debug)) $_RESULT[203] = $resp;
					else {
						$_RESULT[204] = $resp;
						$ret = true;
					}
				}
			}
			return $ret;
		}
	}

	function from($conn = null, $addr = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!is_string($addr)) $err[] = 'invalid from address type';
		else if (!($addr != '' && FUNC4::is_mail($addr))) $err[] = 'invalid from address value';
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$_smtp = new SMTP4;
			if (!fwrite($conn, 'MAIL FROM:<'.$addr.'>'.$_smtp->CRLF)) $_RESULT[320] = 'can not write';
			else if (!SMTP4::_cres($conn, $resp, 250, null, $debug)) $_RESULT[321] = $resp;
			else {
				$_RESULT[322] = $resp;
				$ret = true;
			}
			return $ret;
		}
	}

	function to($conn = null, $addr = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!is_string($addr)) $err[] = 'invalid to address type';
		else if (!($addr != '' && FUNC4::is_mail($addr))) $err[] = 'invalid to address value';
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$_smtp = new SMTP4;
			if (!fwrite($conn, 'RCPT TO:<'.$addr.'>'.$_smtp->CRLF)) $_RESULT[323] = 'can not write';
			else if (!SMTP4::_cres($conn, $resp, 250, 251, $debug)) $_RESULT[324] = $resp;
			else {
				$_RESULT[325] = $resp;
				$ret = true;
			}
			return $ret;
		}
	}

	function data($conn = null, $mess = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = $err = array();
		if (!is_resource($conn)) $err[] = 'invalid resource connection';
		if (!(is_string($mess) && $mess != '')) $err[] = 'invalid message value';
		if (count($err) > 0) FUNC4::trace($debug, implode(', ', $err));
		else {
			$ret = false;
			$_smtp = new SMTP4;
			if (!fwrite($conn, 'DATA'.$_smtp->CRLF)) $_RESULT[326] = 'can not write';
			else if (!SMTP4::_cres($conn, $resp, 354, null, $debug)) $_RESULT[327] = $resp;
			else {
				$continue = true;
				foreach (explode($_smtp->CRLF, $mess) as $line) {
					if ($line != '' && $line[0] == '.') $line = '.'.$line;
					if (!fwrite($conn, $line.$_smtp->CRLF)) {
						$_RESULT[328] = 'can not write';
						$continue = false;
						break;
					}
				}
				if ($continue) {
					if (!fwrite($conn, '.'.$_smtp->CRLF)) $_RESULT[329] = 'can not write';
					else if (!SMTP4::_cres($conn, $resp, 250, null, $debug)) $_RESULT[330] = $resp;
					else {
						$_RESULT[331] = $resp;
						$ret = true;
					}
				}
			}
			return $ret;
		}
	}

	function rset($conn = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = array();
		$ret = false;
		$_smtp = new SMTP4;
		if (!is_resource($conn)) FUNC4::trace($debug, 'invalid resource connection');
		else if (!fwrite($conn, 'RSET'.$_smtp->CRLF)) $_RESULT[332] = 'can not write';
		else if (!SMTP4::_cres($conn, $resp, 250, null, $debug)) $_RESULT[333] = $resp;
		else {
			$_RESULT[334] = $resp;
			$ret = true;
		}
		return $ret;
	}

	function recv($conn = null, $code1 = null, $code2 = null, $debug = null) {
		if (!FUNC4::is_debug($debug)) $debug = debug_backtrace();
		global $_RESULT;
		$_RESULT = array();
		$ret = false;
		if (!SMTP4::_cres($conn, $resp, $code1, $code2, $debug)) $_RESULT[335] = $resp;
		else {
			$_RESULT[336] = $resp;
			$ret = true;
		}
		return $ret;
	}

}

?>