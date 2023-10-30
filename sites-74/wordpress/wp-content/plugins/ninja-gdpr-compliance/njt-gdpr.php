<?php
/*
 * Plugin Name: Ninja GDPR Compliance
 * Plugin URI: http://ninjateam.org
 * Description: GDPR Compliance 2021 for WordPress
 * Version: 2.6.1
 * Author: NinjaTeam
 * Author URI: http://ninjateam.org
 */
define('NJT_GDPR_FILE', __FILE__);
define('NJT_GDPR_DIR', realpath(plugin_dir_path(NJT_GDPR_FILE)));
define('NJT_GDPR_URL', plugins_url('', NJT_GDPR_FILE));
define('NJT_GDPR_I18N', 'njt_gdpr');
define('NJT_GDPR_VERSION', '2.6.1');


if ( file_exists(dirname(__FILE__) . '/vendor/autoload.php') ) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

require_once NJT_GDPR_DIR . '/src/Cross.php';

require_once NJT_GDPR_DIR . '/src/functions.php';

require_once NJT_GDPR_DIR . '/src/init.php';
NjtGdpr::instance();

require_once NJT_GDPR_DIR . '/src/Policy.php';
$policy = new NjtGdprPolicy;
$policy->init();

require_once NJT_GDPR_DIR . '/src/Term.php';
$term = new NjtGdprTerm();
$term->init();

require_once NJT_GDPR_DIR . '/src/ForgetMe.php';
$forget_me = new NjtGdprForgetMe();
$forget_me->init();

require_once NJT_GDPR_DIR . '/src/DataAccess.php';
$data_access = new NjtGdprDataAccess();
$data_access->init();

require_once NJT_GDPR_DIR . '/src/DataBreach.php';
$data_breach = new NjtGdprDataBreach();
$data_breach->init();

require_once NJT_GDPR_DIR . '/src/DataRectification.php';
$data_rectification = new NjtGdprDataRectification();
$data_rectification->init();

require_once NJT_GDPR_DIR . '/src/Integrations.php';
$integrations = new NjtGdprIntegrations();
$integrations->init();

require_once NJT_GDPR_DIR . '/src/EuTraffic.php';
$eu_traffic = new NjtGdprEuTraffic();
$eu_traffic->init();

require_once NJT_GDPR_DIR . '/src/PrivacySettingsPage.php';
$privacy_settings_page = new NjtGdprPrivacySettingsPage();
$privacy_settings_page->init();

require_once NJT_GDPR_DIR . '/src/Unsubscribe.php';
$unsubscribe = new NjtGdprUnsubscribe();
$unsubscribe->init();