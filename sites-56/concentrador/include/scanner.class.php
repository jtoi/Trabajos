<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
/**
** Copyright © Larry Wakeman - 2013
**
** All rights reserved. No part of this publication may be reproduced, stored in a retrieval system, 
** or transmitted in any form or by any means without the prior written permission of the copyright
** owner and must contain the avove copyright notice.
**
** Permission is granted to anyone but this copyright noticemust be included.
*/
class scanner {
    
    private $config = array();    // Configuration parameters
    private $array = array();    // Scan Results
    private $filename; // filename root for this file
    private $dirname; // filename directory for this file
    private $dblink; // database link
	private $includeType = "php,js,htm,html,css,tpl,ini,txt";
    public $direct = '';
    
/**
 ** Class Construtor
 */
    public function __construct()
    {
        $this->filename = str_replace('.php', '', str_replace('.class', '', basename(__file__)));
        $this->dirname = dirname(__file__);
        // load the config
        if (file_exists($this->dirname.'/'.$this->filename.'.ini')) {
            $fh = fopen($this->dirname.'/'.$this->filename.'.ini', 'r');
            $this->config = unserialize(base64_decode(fgets($fh)));
            fclose($fh);
        }
        //initialize the database
        if (count($this->config) != 0) $this->dbConnect();
    }
    
/**
 ** initialize the database
 */
    function dbConnect() {
        global $user, $pass;
        $this->dblink = mysql_connect('localhost',$user,$pass);
        if (!$this->dblink) {
            die ('Could not connect: ' . mysql_error());
        }
        // select the  database
        $this->db = mysql_select_db('concentramf_db', $this->dblink);
        if (!$this->db) {
            die ('Can\'t use concentramf_db : ' . mysql_error());
        }
    }
    
/**
 ** Execute a sql query
 */
    private function query($query, $line=0) {
        $result = mysql_query($query);
        if (!$result) {
            die('Invalid query: ' . mysql_error().'<br>'.$query.'<br>'.basename(__FILE__).' '.$line.'<br></div>');
        }
        // returndata id appropriate
        if (substr(strtoupper ($query) , 0, 6) == 'SELECT' || substr(strtoupper ($query) , 0, 4) == 'SHOW') {
            if (mysql_num_rows($result) != 0) {
                $return = array();
                for ($i = 0; $i < mysql_num_rows($result); $i++) {
                    $return[] = mysql_fetch_assoc($result);
                }
                return $return;
            }
        }
        if (substr(strtoupper ($query) , 0, 6) == 'INSERT') {
            return mysql_insert_id();
        }
    }
    
/**
 ** Get the configuration
 */
    public function get_config() {
        return $this->config;
    }
    
/**
 ** Set the configuration
 */
    public function set_config($config = null) {
        // save the new config
//        $this->config = $config;
        // write the ini file
//        $saveConfig = base64_encode(serialize($config));
//        $fh = fopen($this->dirname.'/'.$this->filename.'.ini', 'w');
//        fwrite($fh, $saveConfig."\n");
//        fclose ($fh);
        // create the tables, if needed
        $this->dbConnect();
        $query = "SHOW TABLES LIKE 'tbl_verifica'";
        $data = $this->query($query, __LINE__);
        if (is_null($data)) {
            $query = "CREATE TABLE `tbl_verifica` (
                `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `filename` VARCHAR( 255 ) NOT NULL ,
                `scan_id` BIGINT NOT NULL,
                INDEX ( `filename` )
                ) ENGINE = MYISAM   DEFAULT CHARSET=utf8;";
            $data = $this->query($query, __LINE__);
        }
        $query = "SHOW TABLES LIKE 'tbl_verifica_run'";
        $data = $this->query($query, __LINE__);
        if (is_null($data)) {
            $query = "CREATE TABLE `tbl_verifica_run` (
                `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `file_id` BIGINT NOT NULL ,
                `hash` VARCHAR( 33 ) NOT NULL ,
                `run_id` BIGINT NOT NULL  ,
                 `status` enum('1 - Initial','2 - Unchanged','3 - Changed','4 - Added','5 - Deleted') NOT NULL DEFAULT '1 - Initial',
                INDEX ( `file_id` )
                ) ENGINE = MYISAM   DEFAULT CHARSET=utf8;";
            $data = $this->query($query, __LINE__);
        }
        $query = "SHOW TABLES LIKE 'tbl_verifica_scan'";
        $data = $this->query($query, __LINE__);
        if (is_null($data)) {
            $query = "CREATE TABLE `tbl_verifica_scan` (
                `scan_id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `scantime` TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
                ) ENGINE = MYISAM   DEFAULT CHARSET=utf8;";
            $data = $this->query($query, __LINE__);
        }
    }
    
/**
 ** Perform initial scan
 */
    public function initial() {
		$q = "show tables like 'tbl_verifica'";
		if (is_null($this->query($q, __LINE__))) $this->set_config();
        $query = "TRUNCATE TABLE `tbl_verifica`";
        $data = $this->query($query, __LINE__);
        $query = "TRUNCATE TABLE `tbl_verifica_run`";   
        $data = $this->query($query, __LINE__);
        $query = "TRUNCATE TABLE `tbl_verifica_scan`";   
        $data = $this->query($query, __LINE__);
        $query = "INSERT INTO `tbl_verifica_scan` VALUES (NULL, NULL)";
        $scan = $this->query($query, __LINE__);
        if ($this->doTree($this->direct)) {
            foreach ($this->array as $dir => $files) {
                foreach ($files as $filename => $hash) {
                    $query = "INSERT INTO `tbl_verifica` VALUES (null, '".str_replace('//', '/', $dir.'/'.$filename)."', '".$scan."')";
                    $file_id = $this->query($query, __LINE__);
                    $query = "INSERT INTO `tbl_verifica_run` VALUES (null, ".$file_id.", '".$hash."', '".$scan."', '1 - Initial')";
                    $data = $this->query($query, __LINE__);
                }
            }
            return true;
        }
        return false;
    }
    
/**
 ** Perform scan
 */ 
    public function scan() {
        
        $query = "SHOW TABLES LIKE 'tbl_verifica'";
        $data = $this->query($query, __LINE__);
        if (is_null($data)) $this->initial ();
        $query = "SELECT MAX(scan_id ) as lastscan FROM `tbl_verifica_scan`";
        $data = $this->query($query, __LINE__);
        $lastscan = $data['0']['lastscan'];
        $query = "INSERT INTO `tbl_verifica_scan` VALUES (NULL, NULL)";
        $scan = $this->query($query, __LINE__);
        $return = array('Changed' => array(), 'Added' => array(), 'Deleted' => array());
        $temp = array();
        if ($this->doTree($this->direct)) {
            foreach ($this->array as $dir => $files) {
                foreach ($files as $filename => $hash) {
                    $temp[str_replace('//', '/', $dir.'/'.$filename)] = $hash;
                    $query  = "SELECT * FROM `tbl_verifica` s ".
                                "INNER JOIN `tbl_verifica_run` r ON s.id = r.file_id ".
                            "WHERE filename = '".str_replace('//', '/', $dir.'/'.$filename)."' ".
                            "AND run_id = '".$lastscan."'";
                    $data = $this->query($query, __LINE__);
                    $file_id = $data['0']['file_id'];
                    $sourcehash = $data['0']['hash'];
                    if (count($data) == 0) {//el fichero no esta en la BD se agrega y se pone el hash
                        // added file
                        $query = "INSERT INTO `tbl_verifica` VALUES (null, '".str_replace('//', '/', $dir.'/'.$filename)."', '".$scan."')";
//                        echo "$query<br>";
                        $file_id = $this->query($query, __LINE__);
                        $query = "INSERT INTO `tbl_verifica_run` VALUES (null, ".$file_id.", '".$hash."', '".$scan."', '4 - Added')";
//                        echo "$query<br>";
                        $data = $this->query($query, __LINE__);
                        $return['Added'][] = str_replace('//', '/', $dir.'/'.$filename);
                    } else { //el fichero está en la BD se hace el update del status
                        $status = '2 - Unchanged';
                        if ($hash != $sourcehash) { //el fichero no tiene el mismo hash, se inserta 
                            $status = '3 - Changed';
                            $query = "insert into tbl_verifica VALUES (null, '".str_replace('//', '/', $dir.'/'.$filename)."', '".$scan."')";
                            $data = $this->query($query, __LINE__);
                            $query = "INSERT INTO `tbl_verifica_run` VALUES (null, ".$file_id.", '".$hash."', '".$scan."', '$status')";
                            $data = $this->query($query, __LINE__);
                            $return['Changed'][] = str_replace('//', '/', $dir.'/'.$filename);
                        } else { //el fichero está y con el mismo hash
                            $query = "update tbl_verifica set scan_id = '$scan' where filename = '".str_replace('//', '/', $dir.'/'.$filename)."' and scan_id = '$lastscan'";
                            $this->query($query, __LINE__);
                            $query = "update tbl_verifica_run set run_id = '$scan', status = '$status' where file_id = '$file_id'";
                            $this->query($query, __LINE__);
                        }
                    }
                }
            }
            $query  = "SELECT s.* ";
            $query .= "FROM `tbl_verifica` s ";
            $query .= "INNER JOIN `tbl_verifica_run` r ON s.id = r.file_id ";
            $query .= "WHERE run_id = '".$lastscan."'";
//            echo $query;
            $data = $this->query($query, __LINE__);
//			print_r($data);
            foreach ($data as $entry) {
                if (!isset($temp[$entry['filename']])) {
                    $query = "INSERT INTO `tbl_verifica_run` VALUES (null, ".$data['0']['id'].", 'Deleted', '".$scan."','5 - Deleted')";
                    $data = $this->query($query, __LINE__);
                    $return['Deleted'][] = $entry['filename'];
                }
            }
            return $return;
        }
        return false;
    }
    
/**
 ** Calculate the checksum
 */
	private function checksum($file) {
	    $ignores = Array(10, 13);
	    $fh = fopen($file, 'r');
	    $buffer = '';
//		echo "<br>!!$file";
		if (is_object($fh)){
			while (!feof($fh)) {
				$buffer .= fgets($fh);
			}
			fclose ($fh);
			foreach ($ignores as $ignore) {
				while (strpos($buffer, chr($ignore))) {
					$buffer = str_replace(chr($ignore), '', $buffer);
				}
			}
			return hash('crc32', $buffer).hash('crc32b', $buffer);
		}
//			echo md5_file($file, false); 
			return md5_file($file, false);
	}
    
/**
 ** scan the site
 */
	private function doTree($dir) {
//		echo $dir;
    	$dir = str_replace('//', '/', $dir);
//		echo $this->includeType."<br>";
	    $dirs = explode(',', $this->includeType);
//	    foreach ($dirs as $entry) if (stripos($dir, trim($entry)) !== false)   {echo "sale";return true;}
	    if (isset($this->includeType)) $filetypes = explode(',', $this->includeType); else $filetypes = array();
	    if ($dh = opendir($dir)) {
	        while ($file = readdir($dh)) {
	            if ($file != '.' && $file != '..') {
	                if (is_dir($dir.'/'.$file)) {
	                    if (count($this->array) == 0) $this->array[0] = 'Temp';
	                    if (!$this->doTree($dir.'/'.$file)) {
	                        return false;
	                    }
	                } else {
	                    if (filesize($dir.'/'.$file)) {
//							echo $dir."/".$file."<br>";
	                        foreach ($filetypes as $type) {
//								echo "<br>$type - $file";
	                            if (strpos($file, '.'.trim($type)) !== false ) {
//	                                set_time_limit(30);
	                                $this->array[$dir][$file] = $this->checksum( $dir.'/'.$file );
	                            }
	                        }
	                    }
	                }
	            }
	        }
//			print_r($this->array);
	        if (count($this->array) > 1  && isset($this->array['0'])) unset($this->array['0']);
	        return true;
	    } else {
	        echo 'error opening '.$dir.'</h3>';
	        return false;
	    }
	}

}
