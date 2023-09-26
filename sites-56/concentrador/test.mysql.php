<?php
 /* Does our class exist? */
 if (file_exists('class.mysql.php')) {
  include 'class.mysql.php';

  /* User defined connection settings
     PDO DSN created dynamically
  */
  $settings = array('server'=>'localhost',
                    'username'=>'root',
                    'password'=>'admin',
                    'database'=>'concentramf_db');

  /* Singleton object (PDO/MySQLi/MySQL)
     Gracefully degrades access method based on
     MySQL extensions loaded.
  */
  $db = dbconn::instance($settings);

  /* Safe and sanitized dynamic SQL statement */
  $string = 'todos';
  $integer = 10;
  $sql = sprintf('SELECT * FROM `tbl_admin` WHERE `idcomercio` LIKE "%s" AND `idrol`
                 LIKE "%d" LIMIT 1', $db->sanitize($string), $db->sanitize($integer));

  /* Results of SQL statement */
  $results = $db->query($sql);

  /* Error? */
  if (!$results) {
   echo $db->error;
  }

  $count = $db->affected($db);
  if ($count>=1) {
   $results = $db->results($results);
  }
  echo 'Records found: '.$count.'<pre>'; print_r($results); echo '</pre><hr/>';

  /* No need to close or flush connection, the
     __destruct() will repair, optimize and
     re-index entire database prior to removing
     singleton object
  */
}
?>
