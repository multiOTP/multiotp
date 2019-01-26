<?php
/*****************************************************
 * Package ZypioSNMP
 * Description ZypioSNMP is used to datafill SNMP OID's
 *             using SNMP's pass functionality
 * Author Mike Mackintosh < m@zyp.io >
 * (https://github.com/mikemackintosh/Zypio-SNMP-OID)
 *
 * Date 20130708
 * Version 1.3
 *
 * Requires >= PHP5.4
 *
 ****************************************************/

/**
 * 
 */
class ZypioSNMP{

	private $oid,
			$tree = [];

	const INTEGER = "integer";
	const STRING = "string";
	const IPADDR = "ipaddress";
	const NETADDR = "NetworkAddress";
	const GAUGE = "gauge";
	const COUNTER = "counter64";
	const TIME = "timeticks";
	const OBJ = "objectid";
	const OPAQUE = "opaque";


	/**
	 * Create Class with Base OID
	 * 
	 * @param [type] $oid [description]
	 */
	public function __construct( $oid ){

		if(strpos($oid, ".") !== 0){
			$oid = ".{$oid}";
		}

		// Store base OID locally
		$this->oid = $oid;

	}

	/**
	 * Add an OID to your base OID
	 * 
	 * @param string $oid   The OID you wish to store data for
	 * @param string $type  Your OID type, STRING,Integer, etc
	 * @param multiple $value The value of your OID
	 */
	public function addOid( $oid, $type = STRING, $value= NULL, $allowed = [] ){

		$this->tree[$oid] = [ 'type' => $type, 'value' => $value, 'allowed' => $allowed ];

		return $this;
	}

	/**
	 * [getNextOid description]
	 * 
	 * @param  string $requested_oid
	 * 
	 * @return NULL
	 */
	public function getNextOid( $requested_oid ){

		// Sort Response
		$tree = array_keys($this->tree);
		natsort($tree);
		$this->tree = array_merge($tree, $this->tree);

		// 
		$local_oids = $tree;
		$last = array_shift($local_oids);
		array_unshift($local_oids, $last);

		// Loop Through now
		for($i=0;$i<sizeof($local_oids);$i++){

			/*
			echo "Looking for OID: $local_oids[$i]\n";
			//*/

			// If no sub-oid was provided, return the first
			if( $requested_oid == $this->oid ){
				
				echo "{$this->oid}{$last}".PHP_EOL;
				echo $this->tree[ $last ]['type'] .PHP_EOL;
				echo $this->tree[ $last ]['value'] .PHP_EOL;
				exit(0);

			}
			else if( version_compare( $requested_oid, $this->oid . $local_oids[$i], "<")) {
			
				echo "{$this->oid}{$local_oids[$i]}".PHP_EOL;
				echo $this->tree[ $local_oids[$i] ]['type'] .PHP_EOL;
				echo $this->tree[ $local_oids[$i] ]['value'] .PHP_EOL;
				exit(0);

			}

		}

		// Per RFC, if there is nothing left, respond with NONE
		echo $this->oid . PHP_EOL;
		echo "NONE".PHP_EOL;
		exit(0);

	}

	public function getOid( $requested_oid ){

		// Get remainder
		preg_match("`{$this->oid}(.*)`", $requested_oid , $matches);
		
		// Set relative OID
		$oid = $matches[1];

		// Check if it exists
		if( array_key_exists( $oid, $this->tree )){
	
				echo "{$requested_oid}".PHP_EOL;
				echo $this->tree[ $oid ]['type'] .PHP_EOL;
				echo $this->tree[ $oid ]['value'] .PHP_EOL;
				exit(0);

		}

		// Per RFC, if there is nothing left, respond with NONE
		echo "NONE".PHP_EOL;
		exit(0);

	}

	public function respond(){

		// This checks for a GET/GETNEXT or SET
		// NOTE: Only GET/GETNEXT is support ATM
		if( array_key_exists(1, $_SERVER['argv'])) {

			// Look for getnext
			if( $_SERVER['argv'][1] == "-n" ){

				/*
				file_put_contents("/var/log/zypiosnmp.log", "Requesting an OID GETNEXT with -n --{$_SERVER['argv'][2]}\n", FILE_APPEND);
				//*/

				$this->getNextOid( $_SERVER['argv'][2] );

			}
			// look for GET
			else if( $_SERVER['argv'][1] == "-g" ){

				/*
				file_put_contents("/var/log/zypiosnmp.log", "Requesting an OID GET with -g --{$_SERVER['argv'][2]}\n", FILE_APPEND);
				//*/

				$this->getOid( $_SERVER['argv'][2] );

			}
		
		}
		// PASS_PERSIST 
		else{
			
			// Set input blocking to false
			stream_set_blocking(STDIN, false);
			while(true){
				// STDIN
				$stdin = trim(stream_get_contents( STDIN ));

				// If PING is received, respond with PONG
				if( stristr($stdin, "PING") ) {

					echo "PONG\n";

				}

				// If get is received, respond with oid
				else if( stristr($stdin, "get") ) {

					$this->getNextOid(explode("\n", $stdin)[1]);

				}

				// If getnext is received, respond with getnext oid
				else if( stristr($stdin, "getnext") ) {

					$this->getOid(explode("\n", $stdin)[1]);

				}
			}

		}

	}

}

/**
 * @CHANGELOG
 *
 * Version 1.3
 * 		Added support for pass_persist
 * 		Added STDIN capture for pass_persist
 * 
 * Version 1.2
 * 		Fixed issue with get next and comparing OID's
 * 		Added changelog to end of file
 * 		Fixed mispelled Gauge (from Guage)
 * 		Added opaque and netaddr as obj def
 * 		
 * Version 1.1
 * 		Moved SNMP cmd to respond method
 *
 * Version 1.0
 *		Initial
 * 
 */
