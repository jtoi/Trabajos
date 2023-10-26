<?php
function adminer_object() {

	 class AdminerSoftware extends Adminer {

    function name() {
      // custom name in title and heading
      return 'Software';
    }

//    function permanentLogin() {
//      // key used for permanent login
//      return "98d8685392930541c06752ba1a0653b4";
//    }

    function credentials() {
      // server, username and password for connecting to database
      return array('localhost', 'root', 'admin');
    }

    function database() {
      // database name, will be escaped by Adminer
      return 'concentramf_db';
    }

	function selectLimitProcess() {
		// números de records a mostrar
		return 300;
	}

//    function login($login, $password) {
      // validate user submitted credentials
//      return ($login == 'root' && $password == 'admin');
//    }

   // function tableName($tableStatus) {
      // tables without comments would return empty string and will be ignored by Adminer
    //  return h($tableStatus["Comment"]);
   // }

//    function fieldName($field, $order = 0) {
      // only columns with comments will be displayed and only the first five in select
//      return ($order <= 5 && !preg_match('~_(md5|sha1)$~', $field["field"]) ? h($field["comment"]) : "");
//    }

  }

  return new AdminerSoftware;

    // required to run any plugin
    include_once "./plugins/plugin.php";

    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }

    $plugins = array(
        // specify enabled plugins here
        new AdminerDumpXml,
        new AdminerTinymce,
        new AdminerFileUpload("data/"),
        new AdminerSlugify,
        new AdminerTranslation,
        new AdminerForeignSystem,
        new AdminerDumpBz2,
        new AdminerDumpZip,
        new AdminerEditCalendar,
        new AdminerSqlLog,
        new AdminerWymeditor,
        new AdminerTinymce,
    );

    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */

    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer.php";
?>
