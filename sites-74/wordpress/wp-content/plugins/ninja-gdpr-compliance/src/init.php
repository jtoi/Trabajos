<?php

class NjtGdpr
{
    private static $_instance = null;
    private $hook_suffix;

    public function __construct()
    {
        $this->hook_suffix = array(
            'cookie_popup'
        );
        /*
         * Language
         */
        add_action('plugins_loaded', array($this, 'loadTextDomain'));

        
        /*
         * Plugin actived
         */
        register_activation_hook(NJT_GDPR_FILE, array($this, 'pluginActived'));

        add_filter('wp_mail_content_type', array($this, 'mailContentType'));
        //start session
        // add_action('init', array($this, 'startSession'), 1);
        //register menu
        add_action('admin_menu', array($this, 'registerMenu'));

        add_action('admin_enqueue_scripts', array($this, 'registerAdminEnqueue'));

        add_action('admin_head', array($this, 'adminHead'));
        add_action('admin_footer', array($this, 'adminFooter'));
        //admin ajax
        add_action('wp_ajax_njt_gdpr_get_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_settings', array($this, 'ajaxUpdateSettings'));

        add_action('wp_ajax_njt_gdpr_recheck_cookie', array($this, 'ajaxRecheckCookie'));
        add_action('wp_ajax_nopriv_njt_gdpr_recheck_cookie', array($this, 'ajaxRecheckCookie'));

        //frontend ajax
        add_action('wp_ajax_njt_gdpr_allow_cookie', array($this, 'ajaxAllowCookie'));
        add_action('wp_ajax_nopriv_njt_gdpr_allow_cookie', array($this, 'ajaxAllowCookie'));

        add_action('wp_enqueue_scripts', array($this, 'registerWpEnqueue'), 999);

        $eu = njt_eu_settings();
        if(($eu['status'] == 'block-all') || ($eu['status'] == 'work' && $eu['country_is_blocked'])) {
            //block cookie
            if (!$this->canUseCookie() && apply_filters('njt_gdpr_block_cookie_in_php', true)) {
                $dirty = false;
                foreach (headers_list() as $header) {
                    if ($dirty) {
                        continue;
                    }
                    if (preg_match('/Set-Cookie/', $header)) {
                        $dirty = true;
                    }
                }
                if ($dirty) {
                    $phpversion = explode('.', phpversion());
                    if ($phpversion[1] >= 3) {
                        header_remove('Set-Cookie'); // php 5.3
                    } else {
                        header('Set-Cookie:'); // php 5.2
                    }
                }
            }
        }
    }
    public function adminHead()
    {
        ?>
        <style>
            .toplevel_page_njt-gdpr .wp-menu-image img {
                width: 18px;
            }
        </style>
        <?php
    }
    public function adminFooter()
    {
        ?>
        <script>
        jQuery(document).ready(function () {
            jQuery('a[href="admin.php?page=njt-gdpr"]').attr('href', jQuery('a[href="admin.php?page=njt-gdpr"]').attr('href')  +  '#/');
        });
        </script>
        <?php
    }
    public function mailContentType()
    {
        return "text/html";
    }

    public function startSession()
    {
        if (!session_id()) {
            session_start();
        }
    }
    public function loadTextDomain()
    {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            $locale = is_admin() ? get_user_locale() : get_locale();
        }
        unload_textdomain(NJT_GDPR_I18N);
        load_textdomain(NJT_GDPR_I18N, NJT_GDPR_DIR . '/languages/' . $locale . '.mo');
        load_plugin_textdomain(NJT_GDPR_I18N, false, NJT_GDPR_DIR . '/languages/');
        $this->runCross();
    }
    public function pluginActived()
    {
        global $wpdb;
        if(!count((array)get_option('njt_gdpr_privacy_settings_page', array())) > 0) {
            $privacy_settings_page = wp_insert_post(array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_title' => __('Privacy Settings Page', NJT_GDPR_I18N),
                'post_content' => '[njt_gdpr_privacy_settings]'
            ));
            update_option('njt_gdpr_privacy_settings_page', array('choosed_page' => $privacy_settings_page));
        }

        $this->runCross();
    }

    public function runCross(){
        $currentVersion = get_option('njt_gdpr_version');
        if ( version_compare(NJT_GDPR_VERSION, $currentVersion, '>' ) ) { 
          $filebirdCross = FileBirdCross::get_instance('filebird', 'filebird+ninjateam', NJT_GDPR_URL, array('filebird/filebird.php', 'filebird-pro/filebird.php'));
          $filebirdCross->need_update_option();
          update_option('njt_gdpr_version', NJT_GDPR_VERSION);
        }
    }

    public function registerMenu()
    {
        global $submenu;

        $this->hook_suffix['cookie_popup'] = add_menu_page(
            __('Ninja GDPR', NJT_GDPR_I18N),
            __('Ninja GDPR', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr',
            array($this, 'mainPage'),
            NJT_GDPR_URL . '/assets/admin/img/ninja-gdpr.svg'
        );
        
        add_submenu_page(
            'njt-gdpr',
            __('Cookie Popup', NJT_GDPR_I18N),
            __('Cookie Popup', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr',
            array($this, 'mainPage')
        );
        //$submenu['njt-gdpr'][0][2] = esc_url(add_query_arg(array('page' => 'njt-gdpr'), admin_url('admin.php')) . '#/');
        add_submenu_page(
            'njt-gdpr',
            __('Privacy Policy', NJT_GDPR_I18N),
            __('Privacy Policy', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/policy',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Terms and Conditions', NJT_GDPR_I18N),
            __('Terms and Conditions', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/terms-and-conditions',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Forget Me', NJT_GDPR_I18N),
            __('Forget Me', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/forget-me',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Data Access', NJT_GDPR_I18N),
            __('Data Access', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/data-access',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Data Breach', NJT_GDPR_I18N),
            __('Data Breach', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/data-breach',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Data Rectification', NJT_GDPR_I18N),
            __('Data Rectification', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/data-rectification',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Integrations', NJT_GDPR_I18N),
            __('Integrations', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/integrations',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Privacy Settings Page', NJT_GDPR_I18N),
            __('Privacy Settings Page', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/privacy-settings-page',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('EU Traffic', NJT_GDPR_I18N),
            __('EU Traffic', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/eu-traffic',
            array($this, 'mainPage')
        );
        add_submenu_page(
            'njt-gdpr',
            __('Unsubscribe', NJT_GDPR_I18N),
            __('Unsubscribe', NJT_GDPR_I18N),
            'manage_options',
            'njt-gdpr#/unsubscribe',
            array($this, 'mainPage')
        );
    }

    public function getSettings()
    {
        $defaults = array(
            'display_on' => 'all_pages',
            'display_type' => 'popup',
            'content' => __('We use cookies to give you the best online experience. By agreeing you accept the use of cookies in accordance with our cookie policy.', NJT_GDPR_I18N),
            'accept' => array(
                'text' => __('Accept', NJT_GDPR_I18N),
                'bg_color' => '#e26d38',
                'text_color' => '#fff',
            ),
            'decline' => array(
                'text' => __('Decline', NJT_GDPR_I18N),
                'bg_color' => '#827e7e',
                'text_color' => '#fff',
            ),
            'is_enable_decline_btn' => '1',
            'block_cookie' => '0',//block cookie until user consents
            'full_width' => array(
                'position' => 'top'
            ),
            'popup' => array(
                'position' => 'top_left',
                'border_radius' => '3',
                'max_width' => '430',
            ),
            'is_enable_custom_btn' => '1',
            'custom_btn' => array(
                'text' => __('Read More', NJT_GDPR_I18N),
                'url' => '#',
                'bg_color' => '#827e7e',
                'text_color' => '#fff',
            ),
            'show_pages' => array(),
            'hide_pages' => array(),
            'custom_css' => '',
            'bg_color' => 'rgba(17,17,17,1)',
            'text_color' => 'rgba(255,255,255,1)',
        );
        $settings = get_option('njt_gdpr', array());
        $settings = wp_parse_args($settings, $defaults);

        $settings['is_enable_decline_btn'] = (($settings['is_enable_decline_btn'] == '1') ? true : false);
        $settings['is_enable_custom_btn'] = (($settings['is_enable_custom_btn'] == '1') ? true : false);
        $settings['block_cookie'] = (($settings['block_cookie'] == '1') ? true : false);
        $settings = array_map('stripslashes_deep', $settings);
        return $settings;
    }
    public function userClickedBtn()
    {
        $current_user_id = get_current_user_id();
        if ($current_user_id == 0) {//guest
            if(isset($_COOKIE['njt_gdpr_allow_permissions'])){
                $cookie = json_decode(base64_decode($_COOKIE['njt_gdpr_allow_permissions']), true);
                return isset($cookie['cookie']);
                //return (isset($cookie['cookie']) && $cookie['cookie'] == '1') || (isset($cookie['fb']) && $cookie['fb'] == '1') || (isset($cookie['gg']) && $cookie['gg'] == '1');
            }
            return false;
        } else {
            $arr = get_user_meta($current_user_id, 'njt_gdpr_allow_permissions', true);
            return is_array($arr);
        }
    }
    public function canUseCookie()
    {
        if ($this->userClickedBtn()) {
            $current_user_id = get_current_user_id();
            if ($current_user_id == 0) {//guest
                if(isset($_COOKIE['njt_gdpr_allow_permissions'])) {
                    $cookie = json_decode(base64_decode($_COOKIE['njt_gdpr_allow_permissions']), true);
                    return (isset($cookie['cookie']) && ($cookie['cookie'] == '1'));
                }
                return false;
            } else {
                $arr = get_user_meta($current_user_id, 'njt_gdpr_allow_permissions', true);
                if (is_array($arr) && isset($arr['cookie']) && ($arr['cookie'] == '1')) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            $settings = $this->getSettings();
            return ($settings['block_cookie'] == '0');
        }
    }
    public function ajaxAllowCookie()
    {
        //check_ajax_referer('njt_gdpr', 'nonce');
        $allow = ((isset($_POST['allow'])) ? sanitize_text_field($_POST['allow']) : '1');
        if (!in_array($allow, array('0', '1'))) {
            $allow = '1';
        }
        $current_user_id = get_current_user_id();
        if ($current_user_id == 0) {//guest
            $cookie_value =  array('cookie' => $allow, 'fb' => $allow, 'gg' => $allow);
           setcookie('njt_gdpr_allow_permissions', base64_encode(json_encode($cookie_value)), time() + (86400 * 30 * 365), "/");
        } else {
            update_user_meta($current_user_id, 'njt_gdpr_allow_permissions', array('cookie' => $allow, 'fb' => $allow, 'gg' => $allow));
        }
        $js = '';
        if($allow == '1') {
            ob_start();
            require_once NJT_GDPR_DIR . '/assets/home/js/enable-cookie.js';
            $js = ob_get_clean();
        } else {
            ob_start();
            require_once NJT_GDPR_DIR . '/assets/home/js/disable-cookie.js';
            $js = ob_get_clean();
        }
        wp_send_json_success(array('js' => $js));
    }
    public function ajaxGetSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        wp_send_json_success(array('settings' => $this->getSettings()));
    }
    public function ajaxRecheckCookie()
    {
        //check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = $this->getSettings();
        $data = array('html' => '', 'js' => '');

        ob_start();
        require_once NJT_GDPR_DIR . '/assets/home/js/disable-cookie.js';
        $js = ob_get_clean();

        $eu = njt_eu_settings();
  
        if(($eu['status'] == 'block-all') || ($eu['status'] == 'work' && $eu['country_is_blocked'])) {
            if ($this->userClickedBtn()) {
                if (!$this->canUseCookie()) {
                    $data['js'] = $js;
                }
            } else {
                if ($settings['block_cookie']) {
                    $data['js'] = $js;
                }
                
                $current_page_id = (isset($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : '');
                $show_popup = true;
                if ($settings['display_on'] == 'all_pages') {
                    if (in_array($current_page_id, $settings['hide_pages'])) {
                        $show_popup = false;
                    }
                } elseif ($settings['display_on'] == 'selected_pages') {
                    if (!in_array($current_page_id, $settings['show_pages'])) {
                        $show_popup = false;
                    }
                }
                if ($show_popup === true) {
                    $data['html'] = $this->getPopupHtml($settings);
                }
             
            }
        }
        wp_send_json_success($data);
    }
    private function getPopupHtml($settings)
    {
        $m_class = '';
        $m_style = '';
        
        $m_class .= 'njt-gdpr-' . $settings['display_type'] . ' ';
        //position
        if ($settings['display_type'] == 'full_width') {
            $m_class .= $settings['full_width']['position'] . ' ';
        } else if($settings['display_type'] == 'popup') {
            $m_class .= $settings['popup']['position'] . ' ';
        }
        //border-radius
        if($settings['display_type'] == 'popup') {
            $m_style .= 'border-radius: ' . intval($settings['popup']['border_radius']) . 'px;';
        }
        //bg color
        $m_style .= 'background-color:' . $settings['bg_color'] . ';';
        //text color
        $m_style .= 'color: ' . $settings['text_color'] . ';';
        //max_width
        if($settings['display_type'] == 'popup' && isset($settings['popup']['max_width'])) {
            $m_style .= 'max-width: ' . intval($settings['popup']['max_width']) . 'px;';
        }

        $accept_btn = '<div class="accept-btn" style="color: '.$settings['accept']['text_color'].'; background-color: '.$settings['accept']['bg_color'].'">';
        $accept_btn .= $settings['accept']['text'];
        $accept_btn .= '<img src="' . NJT_GDPR_URL . '/assets/home/img/loader.svg" class="njt-gdpr-loading-icon" />';
        $accept_btn .= '</div>';
        //add decline btn
        $decline_btn = '';
        if ($settings['is_enable_decline_btn']) {
            $decline_btn .= '<div class="decline-btn" style="color: '.$settings['decline']['text_color'].'; background-color: '.$settings['decline']['bg_color'].'">';
            $decline_btn .= $settings['decline']['text'];
            $decline_btn .= '<img src="' . NJT_GDPR_URL . '/assets/home/img/loader.svg" class="njt-gdpr-loading-icon" />';
            $decline_btn .= '</div>';
        }

        //add custom btn
        $custom_btn = '';
        if ($settings['is_enable_custom_btn']) {
            $custom_btn .= '<div class="custom-btn" style="color: '.$settings['custom_btn']['text_color'].'; background-color: '.$settings['custom_btn']['bg_color'].'">';
            $custom_btn .= ((!empty($settings['custom_btn']['url'])) ? '<a style="color: '.$settings['custom_btn']['text_color'].';" target="_blank" href="'.esc_url($settings['custom_btn']['url']).'">'.esc_html($settings['custom_btn']['text']).'</a>' : $settings['custom_btn']['text']);
            $custom_btn .= '</div>';
        }
        $html = '<div class="njt-gdpr '.$m_class.'" style="'.$m_style.'">';
        $html .= '<div class="njt-gdpr-content">'.$settings['content'].'</div>';
        $html .= '<div class="njt-gdpr-btns">'.$accept_btn.$decline_btn.$custom_btn.'</div>';
        $html .= '</div>';

        return $html;
    }
    public function ajaxUpdateSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = ((isset($_POST['settings'])) ? (array)$_POST['settings']: array());
        $content = $settings['content'];
        $settings = njt_gdpr_maybe_sanitize_array($settings);
        $settings['content'] = $content;
        if (isset($settings['is_enable_decline_btn'])) {
            if ($settings['is_enable_decline_btn'] == 'true') {
                $settings['is_enable_decline_btn'] = '1';
            } else {
                $settings['is_enable_decline_btn'] = '0';
            }
        }

        if (isset($settings['is_enable_custom_btn'])) {
            if ($settings['is_enable_custom_btn'] == 'true') {
                $settings['is_enable_custom_btn'] = '1';
            } else {
                $settings['is_enable_custom_btn'] = '0';
            }
        }

        if (isset($settings['block_cookie'])) {
            if ($settings['block_cookie'] == 'true') {
                $settings['block_cookie'] = '1';
            } else {
                $settings['block_cookie'] = '0';
            }
        }
        update_option('njt_gdpr', $settings);
        wp_send_json_success();
    }

    public function registerAdminEnqueue($hook_suffix)
    {
        global $post;
        if ($hook_suffix == $this->hook_suffix['cookie_popup']) {
            $_pages = get_posts(array(
                'post_type' => 'page',
                'posts_per_page' => -1
            ));
            $pages = array();
            //$pages[] = array('ID' => 1, 'title' => __('Home Page (Wp Default)', NJT_GDPR_I18N));
            foreach ($_pages as $k => $v) {
                $pages[] = array(
                    'ID' => $v->ID,
                    'title' => $v->post_title
                );
            }

            wp_register_style('njt-gdpr', NJT_GDPR_URL . '/assets/admin/css/app.css');
            wp_enqueue_style('njt-gdpr');

            wp_register_style('njt-gdpr-th', NJT_GDPR_URL . '/assets/admin/css/th.css');
            wp_enqueue_style('njt-gdpr-th');

            wp_register_script('njt-gdpr', NJT_GDPR_URL . '/assets/admin/js/app.js', array(), '2.0', true);
            wp_enqueue_script('njt-gdpr');
            wp_localize_script('njt-gdpr', 'njt_gdpr', array(
                'nonce' => wp_create_nonce('njt_gdpr'),
                'pages' => $pages,
                'countries' => njt_gdpr_all_countries('array'),
                'i18n' => array(
                    'error_nonce' => __('Please refresh and try again.', NJT_GDPR_I18N),
                    'cookie' => array(
                        'h1' => __('Cookie Popup', NJT_GDPR_I18N),
                        'page_des' => __('', NJT_GDPR_I18N),
                        'is_block_cookie' => __('Block cookie until user consents ?', NJT_GDPR_I18N),
                        'display_type' => __('Display type', NJT_GDPR_I18N),
                        'full_width' => __('Full Width', NJT_GDPR_I18N),
                        'popup' => __('Popup', NJT_GDPR_I18N),
                        'position' => __('Position', NJT_GDPR_I18N),
                        'top' => __('Top', NJT_GDPR_I18N),
                        'bottom' => __('Bottom', NJT_GDPR_I18N),
                        'popup_position' => __('Position', NJT_GDPR_I18N),
                        'top_left' => __('Top Left', NJT_GDPR_I18N),
                        'top_right' => __('Top Right', NJT_GDPR_I18N),
                        'bottom_left' => __('Bottom Left', NJT_GDPR_I18N),
                        'bottom_right' => __('Bottom Right', NJT_GDPR_I18N),
                        'border_radius' => __('Border Radius', NJT_GDPR_I18N),
                        'max_width' => __('Max Width (px)', NJT_GDPR_I18N),
                        'popup_color' => __('Popup Colors', NJT_GDPR_I18N),
                        'bg' => __('Background', NJT_GDPR_I18N),
                        'txt' => __('Text', NJT_GDPR_I18N),
                        'agreebtncolor' => __('Agree Button Colors', NJT_GDPR_I18N),
                        'bg2' => __('Background', NJT_GDPR_I18N),
                        'txt2' => __('Text', NJT_GDPR_I18N),
                        'is_enable_decline_btn' => __('Enable Decline Button ?', NJT_GDPR_I18N),
                        'on' => __('On', NJT_GDPR_I18N),
                        'off' => __('Off', NJT_GDPR_I18N),
                        'decline_btn_color' => __('Decline Button Colors', NJT_GDPR_I18N),
                        'bg3' => __('Background', NJT_GDPR_I18N),
                        'txt3' => __('Text', NJT_GDPR_I18N),
                        'is_enable_custom_btn' => __('Enable Custom Button ?', NJT_GDPR_I18N),
                        'custom_btn_txt' => __('Custom Button Text', NJT_GDPR_I18N),
                        'custom_btn_url' => __('Custom Button URL', NJT_GDPR_I18N),
                        'colors' => __('Colors', NJT_GDPR_I18N),
                        'bg4' => __('Background', NJT_GDPR_I18N),
                        'txt4' => __('Text', NJT_GDPR_I18N),
                        'display_on' => __('Display On:', NJT_GDPR_I18N),
                        'allpages' => __('All Pages But Except:', NJT_GDPR_I18N),
                        'selectedpages' => __('Selected Pages:', NJT_GDPR_I18N),
                        'all' => __('All', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'tabs' => array(
                            'popup_style' => __('Popup Style', NJT_GDPR_I18N),
                            'display' => __('Display', NJT_GDPR_I18N),
                        )
                    ),
                    'data_access' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'data_access' => __('Data Access', NJT_GDPR_I18N),
                        'shortcode' => __('Shortcode', NJT_GDPR_I18N),
                        'shortcode_des' => __('Place shortcode on your existing Data Access page.', NJT_GDPR_I18N),
                        'notification_email_subject' => __('Notification email subject', NJT_GDPR_I18N),
                        'notification_email_body' => __('Notification email body', NJT_GDPR_I18N),
                        'success_message' => __('Success Message', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'data_access_list' => __('Data Access List', NJT_GDPR_I18N),
                        'user_id' => __('User ID', NJT_GDPR_I18N),
                        'email' => __('Email', NJT_GDPR_I18N),
                        'email_request' => __('Email Request', NJT_GDPR_I18N),
                        'request_date' => __('Request Date', NJT_GDPR_I18N),
                        'mail_sent_date' => __('Mail Sent Date', NJT_GDPR_I18N),
                        'actions' => __('Actions', NJT_GDPR_I18N),
                        'send_email' => __('Send Email', NJT_GDPR_I18N),
                        'remove' => __('Remove', NJT_GDPR_I18N),
                        'apply' => __('Apply', NJT_GDPR_I18N)
                    ),
                    'data_breach' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'data_breach' => __('Data Breach', NJT_GDPR_I18N),
                        'notification_email_subject' => __('Notification email subject', NJT_GDPR_I18N),
                        'notification_email_body' => __('Notification email body', NJT_GDPR_I18N),
                        'send_to_all_user' => __('Save Changes And Send To All User', NJT_GDPR_I18N)
                    ),
                    'data_rectification' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'data_rectification' => __('Data Rectification', NJT_GDPR_I18N),
                        'shortcode' => __('Shortcode', NJT_GDPR_I18N),
                        'shortcode_des' => __('Place shortcode on your existing Data Rectification page.', NJT_GDPR_I18N),
                        'notification_email_subject' => __('Notification email subject', NJT_GDPR_I18N),
                        'notification_email_body' => __('Notification email body', NJT_GDPR_I18N),
                        'success_message' => __('Success Message', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'data_rectification_list' => __('Data Rectification List', NJT_GDPR_I18N),
                        'user_id' => __('User ID', NJT_GDPR_I18N),
                        'email' => __('Email', NJT_GDPR_I18N),
                        'request_date' => __('Request Date', NJT_GDPR_I18N),
                        'mail_sent_date' => __('Mail Sent Date', NJT_GDPR_I18N),
                        'actions' => __('Actions', NJT_GDPR_I18N),
                        'send_email' => __('Send Email', NJT_GDPR_I18N),
                        'remove' => __('Remove', NJT_GDPR_I18N),
                        'apply' => __('Apply', NJT_GDPR_I18N),
                        'old_info_label' => __('Current information', NJT_GDPR_I18N),
                        'new_info_label' => __('New information', NJT_GDPR_I18N),
                    ),
                    'forget_me' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'forget_me' => __('Right to be Forgotten', NJT_GDPR_I18N),
                        'shortcode' => __('Shortcode', NJT_GDPR_I18N),
                        'shortcode_des' => __('Place shortcode on your existing Forget Me page.', NJT_GDPR_I18N),
                        'notification_email_subject' => __('Notification email subject', NJT_GDPR_I18N),
                        'notification_email_body' => __('Notification email body', NJT_GDPR_I18N),
                        'with_posts' => __('With posts: ', NJT_GDPR_I18N),
                        'delete' => __('Delete', NJT_GDPR_I18N),
                        'assign_to_another_user' => __('Assign To Another User', NJT_GDPR_I18N),
                        'assign_posts_to_user' => __('Assign posts to user: ', NJT_GDPR_I18N),
                        'success_message' => __('Success Message', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'forget_me_list' => __('Forget Me List', NJT_GDPR_I18N),
                        'user_id' => __('User ID', NJT_GDPR_I18N),
                        'email' => __('Email', NJT_GDPR_I18N),
                        'email_request' => __('Email Request', NJT_GDPR_I18N),
                        'request_date' => __('Request Date', NJT_GDPR_I18N),
                        'mail_sent_date' => __('Mail Sent Date', NJT_GDPR_I18N),
                        'actions' => __('Actions', NJT_GDPR_I18N),
                        'forget_and_send_email' => __('Forget And Send Email', NJT_GDPR_I18N),
                        'remove' => __('Remove', NJT_GDPR_I18N),
                        'apply' => __('Apply', NJT_GDPR_I18N),
                        'are_you_sure' => __('User account will be deleted. Are you use?', NJT_GDPR_I18N)
                    ),
                    'integrations' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'integrations' => __('Integrations', NJT_GDPR_I18N),
                        'woocommerce' => __('WooCommerce', NJT_GDPR_I18N),
                        'inject_consent' => __('Inject consent checkbox to order form?', NJT_GDPR_I18N),
                        'description' => __('Description', NJT_GDPR_I18N),
                        'comments' => __('Comments', NJT_GDPR_I18N),
                        'inject_consent_cmt' => __('Inject consent checkbox to comments form?', NJT_GDPR_I18N),
                        'user_data' => __('User Data', NJT_GDPR_I18N),
                        'it_will_appear_in_registion_form' => __('This description will appear in WordPress Registion Form', NJT_GDPR_I18N),
                        'contact_form_7' => __('Contact Form 7', NJT_GDPR_I18N),
                        'inject_consent_cf7' => __('Inject consent checkbox to CF7 form?', NJT_GDPR_I18N),
                        'facebook_pixel' => __('Facebook Pixel', NJT_GDPR_I18N),
                        'block_facebook' => __('Block Facebook Pixel cookies until user accepts cookies?', NJT_GDPR_I18N),
                        'google_analytics' => __('Google Analytics', NJT_GDPR_I18N),
                        'block_google' => __('Block Google Analytics cookies until user accepts cookies?', NJT_GDPR_I18N),
                        'google_analytics_tracking_id' => __('Google Analytics Tracking ID: ', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'gravity_form' => __('Gravity Form', NJT_GDPR_I18N),
                        'inject_consent_gf' => __('Inject consent checkbox to Gravity Form?', NJT_GDPR_I18N),
                        'label' => __('Label', NJT_GDPR_I18N),
                        'checkbox_text' => __('Checkbox Text', NJT_GDPR_I18N),
                        'validation_text' => __('Validation Text', NJT_GDPR_I18N),
                        'addthis' => __('Addthis', NJT_GDPR_I18N),
                        'block_addthis_label' => __('Block Addthis until user consents ?', NJT_GDPR_I18N),
                        'bp' => __('BuddyPress', NJT_GDPR_I18N),
                        'inject_bp' => __('Inject consent checkbox to BuddyPress forms?', NJT_GDPR_I18N),
                        'tabs' => array(
                            'addthis' => __('Addthis', NJT_GDPR_I18N),
                            'woocommerce' => __('WooCommerce', NJT_GDPR_I18N),
                            'comments' => __('Comments', NJT_GDPR_I18N),
                            'user_data' => __('User Data', NJT_GDPR_I18N),
                            'contact_form_7' => __('Contact Form 7', NJT_GDPR_I18N),
                            'bp' => __('BuddyPress', NJT_GDPR_I18N),
                            'gravity_form' => __('Gravity Form', NJT_GDPR_I18N),
                            'facebook_pixel' => __('Facebook Pixel', NJT_GDPR_I18N),
                            'google_analytics' => __('Google Analytics', NJT_GDPR_I18N)
                        )
                    ),
                    'privacy_policy' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'privacy_policy' => __('Privacy Policy', NJT_GDPR_I18N),
                        'shortcode' => __('Shortcode', NJT_GDPR_I18N),
                        'shortcode_des' => __('Place shortcode on your existing Privacy Policy page to add an accept button.', NJT_GDPR_I18N),
                        'none' => __('None', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'page_to_redirect' => __('Page to redirect to after Privacy Policy is accepted:', NJT_GDPR_I18N),
                        'consent_expire' => __('Set consent expire time (days)', NJT_GDPR_I18N),
                        'require_loggedin' => __('Require logged in users to accept Privacy Policy (redirect) ?', NJT_GDPR_I18N),
                        'require_guest' => __('Require not logged in users/guests to accept Privacy Policy (redirect) ?', NJT_GDPR_I18N),
                        'redirect_first' => __('Redirect to Privacy Policy first ? (if Terms and Conditions also redirect)', NJT_GDPR_I18N),
                        'choose_page' => __('Privacy Policy page:', NJT_GDPR_I18N),
                        'accepted_text' => __('Accepted Message: ', NJT_GDPR_I18N)
                    ),
                    'privacy_settings_page' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'cookie_des' => __('Cookie Description', NJT_GDPR_I18N),
                        'fb_des' => __('Facebook Pixel', NJT_GDPR_I18N),
                        'gg_des' => __('Google Analytics	', NJT_GDPR_I18N),
                        'privacy_settings_page' => __('Privacy Settings Page', NJT_GDPR_I18N),
                        'shortcode' => __('Shortcode', NJT_GDPR_I18N),
                        'shortcode_des' => __('Place shortcode on your existing Privacy Settings page.', NJT_GDPR_I18N),
                        'choose_page' => __('Choose your Privacy Settings page', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                    ),
                    'term' => array(
                        'page_des' => __('', NJT_GDPR_I18N),
                        'term_page' => __('Term page:', NJT_GDPR_I18N),
                        'page_to_redirect' => __('Page to redirect to after Term is accepted:', NJT_GDPR_I18N),
                        'expire_time' => __('Set consent expire time (days)', NJT_GDPR_I18N),
                        'required_logged_in' => __('Require logged in users to accept Term (redirect) ?', NJT_GDPR_I18N),
                        'required_not_logged_in' => __('Require not logged in users/guests to accept Term (redirect) ?', NJT_GDPR_I18N),
                        'terms_and_conditions' => __('Terms and Conditions', NJT_GDPR_I18N),
                        'shortcode' => __('Shortcode', NJT_GDPR_I18N),
                        'shortcode_des' => __('Place shortcode on your existing Term page to add an accept button.', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                        'none' => __('None', NJT_GDPR_I18N)
                    ),
                    'eutraffic' => array(
                        'eutraffic' => __('EU Traffic', NJT_GDPR_I18N),
                        'choose_countries' => __('Select countries', NJT_GDPR_I18N),
                        'page_des' => __(' ', NJT_GDPR_I18N),
                        'block_all_contries' => __('Refuse all countries?', NJT_GDPR_I18N),
                        'save_changes' => __('Save changes', NJT_GDPR_I18N),
                        'options' => __('Options', NJT_GDPR_I18N),
                        'block' => __('Select countries to refuse', NJT_GDPR_I18N),
                        'work' => __('Only apply GDPR for EU Countries (E.g: Cookie Popup displays on countries you select below only)', NJT_GDPR_I18N),
                        'redirect_label' => __('Redirect those countries to this page (works with non-admin accounts): ', NJT_GDPR_I18N),
                        'none' => __('None', NJT_GDPR_I18N)
                    ),
                    'unsubscribe' => array(
                        'unsubscribe' => __('Unsubscribe', NJT_GDPR_I18N),
                        'page_des' => __(' ', NJT_GDPR_I18N),
                        'unsubscribe_page' => __('Unsubscribe Page', NJT_GDPR_I18N),
                        'none' => __('None', NJT_GDPR_I18N),
                        'are_you_using' => __('Are you using Mailchimp?', NJT_GDPR_I18N),
                        'mailchimp_url' => __('Mailchimp Unsubscribe URL', NJT_GDPR_I18N),
                        'mailchimp_url_des' => __('Enter your MailChimp Unsubscribe URL here. <a href="https://kb.mailchimp.com/lists/signup-forms/find-the-unsubscribe-link-for-your-list" target="_blank">How to get Mailchimp Unsubscribe URL?</a>', NJT_GDPR_I18N),
                        'create_ifrm' => __('Create Iframe', NJT_GDPR_I18N),
                        'mailchimp_ifrm' => __('Mailchimp Iframe Code', NJT_GDPR_I18N),
                        'mailchimp_ifrm_des' => __('Copy this iframe code and insert to your Unsubscribe page', NJT_GDPR_I18N),
                        'save_changes' => __('Save Changes', NJT_GDPR_I18N),
                    )
                )
            ));
        }
    }

    /*
     * Frontend
     */
    public function registerWpEnqueue()
    {
        //remove addthis
        $addthis_settings = (array)get_option('njt_gdpr_integrations', array());
        if(isset($addthis_settings['addthis']) && isset($addthis_settings['addthis']['is_enable']) && ($addthis_settings['addthis']['is_enable'] == '1')) {
            if(!$this->canUseCookie()) {
                wp_deregister_script('addthis_widget');
            }
        }
        //add css
        wp_register_style('njt-gdpr', NJT_GDPR_URL . '/assets/home/css/app.css');
        wp_enqueue_style('njt-gdpr');

        wp_register_style('njt-gdpr-th', NJT_GDPR_URL . '/assets/home/css/th.css');
        wp_enqueue_style('njt-gdpr-th');
        wp_register_script('njt-gdpr', NJT_GDPR_URL . '/assets/home/js/app.js', array('jquery'), '1.0.1', false);
        wp_enqueue_script('njt-gdpr');
        
        wp_localize_script('njt-gdpr', 'njt_gdpr', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt_gdpr'),
            //'settings' => $settings,
            'plugin_url' => NJT_GDPR_URL,
            'current_lang' => apply_filters('wpml_current_language', null)
        ));
    }
    /*
     * Cookie Popup Page
     */
    
    public function mainPage()
    {
        ?>
        <div id="app">
            <router-view></router-view>
        </div>
        <?php
    }

    public function registerSettingsCookiePoup()
    {

    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            $_instance = new self();
        }
        return $_instance;
    }
}
