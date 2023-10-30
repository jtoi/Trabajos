<?php

class NjtGdprPolicy
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_policy_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_policy_settings', array($this, 'ajaxUpdateSettings'));

        add_action('wp_ajax_njt_gdpr_accept_policy', array($this, 'ajaxAcceptPolicy'));
        add_action('wp_ajax_nopriv_njt_gdpr_accept_policy', array($this, 'ajaxAcceptPolicy'));

        add_shortcode('njt_gdpr_policy', array($this, 'shortcodePolicy'));

        add_action('wp_enqueue_scripts', array($this, 'registerWpEnqueue'));

        add_action('wp_ajax_njt_gdpr_recheck_policy', array($this, 'ajaxRecheckPolicy'));
        add_action('wp_ajax_nopriv_njt_gdpr_recheck_policy', array($this, 'ajaxRecheckPolicy'));
    }
    public function getSettings()
    {
        $defaults = array(
            'policy_page' => null,
            'redirect_page_after_accepted' => '-1',
            'redirect_logged_users' => '0',
            'redirect_not_logged_users' => '0',
            'redirect_to_policy_first' => '1',
            'consent_expire' => '365',
            'accepted_text' => __('You\'ve accepted the Privacy Policy.', NJT_GDPR_I18N)
        );
        $settings = get_option('njt_gdpr_policy', array());
        $settings = wp_parse_args($settings, $defaults);

        $settings['redirect_logged_users'] = (($settings['redirect_logged_users'] == '1') ? true : false);
        $settings['redirect_not_logged_users'] = (($settings['redirect_not_logged_users'] == '1') ? true : false);
        $settings['redirect_to_policy_first'] = (($settings['redirect_to_policy_first'] == '1') ? true : false);
        $settings = array_map('stripslashes_deep', $settings);
        return $settings;
    }
    public function ajaxGetSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        wp_send_json_success(array('settings' => $this->getSettings()));
    }
    public function ajaxUpdateSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = ((isset($_POST['settings'])) ? (array)$_POST['settings']: array());
        $settings = njt_gdpr_maybe_sanitize_array($settings);
        if (isset($settings['redirect_logged_users'])) {
            if ($settings['redirect_logged_users'] == 'true') {
                $settings['redirect_logged_users'] = '1';
            } else {
                $settings['redirect_logged_users'] = '0';
            }
        }

        if (isset($settings['redirect_not_logged_users'])) {
            if ($settings['redirect_not_logged_users'] == 'true') {
                $settings['redirect_not_logged_users'] = '1';
            } else {
                $settings['redirect_not_logged_users'] = '0';
            }
        }

        if (isset($settings['redirect_to_policy_first'])) {
            if ($settings['redirect_to_policy_first'] == 'true') {
                $settings['redirect_to_policy_first'] = '1';
            } else {
                $settings['redirect_to_policy_first'] = '0';
            }
        }
        update_option('njt_gdpr_policy', $settings);
        wp_send_json_success();
    }
    public function shortcodePolicy($atts)
    {
        ob_start();
        $atts = shortcode_atts(array(), $atts, 'njt_gdpr_policy');
        $settings = $this->getSettings();
        echo '<form action="" method="POST">';
        if ($this->isAcceptedPolicy()) {
            echo '<p>'.esc_html($settings['accepted_text']).'</p>';
            echo '<button type="button" class="njt_gdpr_policy_decline_btn">'.esc_html(__('Decline', NJT_GDPR_I18N)).'</button>';
        } else {
            echo '<button type="button" class="njt_gdpr_policy_accept_btn">'.esc_html(__('Accept', NJT_GDPR_I18N)).'</button>';
        }
        echo '</form>';
        return ob_get_clean();
    }
    public function registerWpEnqueue()
    {
        wp_register_script('njt-gdpr-policy', NJT_GDPR_URL . '/assets/home/js/policy.js', array('jquery'), '1.0', false);
        wp_enqueue_script('njt-gdpr-policy');
        wp_localize_script('njt-gdpr-policy', 'njt_gdpr_policy', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt_gdpr_policy'),
            'plugin_url' => NJT_GDPR_URL
        ));
    }
    public function isAcceptedPolicy()
    {
        $current_user_id = get_current_user_id();
        if ($current_user_id === 0) {//guest
            // return ((isset($_SESSION['njt_gdpr_acceped_policy'])) && ($_SESSION['njt_gdpr_acceped_policy'] == '1'));
            return get_transient(njt_get_transient_key('njt_gdpr_acceped_policy')) == '1';
        } else {
            $settings = $this->getSettings();
            if ((get_user_meta($current_user_id, 'njt_gdpr_acceped_policy', true) == '1')) {
                $is_expired = (current_time('timestamp', 0) - get_user_meta($current_user_id, 'njt_gdpr_acceped_policy_at', true)) > ($settings['consent_expire'] * 86400);
                return !$is_expired;
            } else {
                return false;
            }
        }
    }
    public function ajaxAcceptPolicy()
    {
        check_ajax_referer('njt_gdpr_policy', 'nonce', true);
        $settings = $this->getSettings();
        $allow = ((isset($_POST['allow'])) ? njt_gdpr_maybe_sanitize_array($_POST['allow']) : '1');
        $current_user_id = get_current_user_id();
        if ($current_user_id === 0) {//guest
            // $_SESSION['njt_gdpr_acceped_policy'] = $allow;
            set_transient(njt_get_transient_key('njt_gdpr_acceped_policy'), $allow, DAY_IN_SECONDS);
        } else {
            update_user_meta($current_user_id, 'njt_gdpr_acceped_policy', $allow);
            update_user_meta($current_user_id, 'njt_gdpr_acceped_policy_at', current_time('timestamp', 0));
        }
        $redirect = '';
        if ($allow == '1') {
            if ($settings['redirect_page_after_accepted'] != '-1') {
                $redirect = get_the_permalink($settings['redirect_page_after_accepted']);
            }
        }
        wp_send_json_success(array('redirect_url' => $redirect, 'mess' => __('Success', NJT_GDPR_I18N)));
    }
    public function ajaxRecheckPolicy()
    {
        check_ajax_referer('njt_gdpr_policy', 'nonce', true);
        $page_id = ((isset($_POST['page_id'])) ? (int)$_POST['page_id'] : '');
        $redirect_url = '';
        $eu = njt_eu_settings();
        if(
            (($eu['status'] == 'block') && (!empty($eu['redirect_to']))) 
            || 
            ($eu['status'] == 'work' && $eu['country_is_blocked']) 
            ||
            ($eu['status'] == 'block-all')
        ) {
            $policy_settings = $this->getSettings();
            if (!class_exists('NjtGdprTerm')) {
                require_once NJT_GDPR_DIR . '/src/Term.php';
            }
            $term = new NjtGdprTerm();
            $term_settings = $term->getSettings();

            if($eu['status'] == 'block') {
                $r_p = $eu['redirect_to'];
                $p_p = $policy_settings['policy_page'];
                $t_p = $term_settings['term_page'];
                $c_page = $page_id;
                if ($c_page == '') {
                    $redirect_url = get_the_permalink($r_p);
                } else {
                    if(($c_page != $r_p) && ($c_page != $p_p) && ($c_page != $t_p)) {
                        $redirect_url = get_the_permalink($r_p);
                    }
                }
            } else {//work or block-all
                if((!$this->isAcceptedPolicy() || !$term->isAcceptedTerm())) {
                    $current_user_id = get_current_user_id();
                    $will_redirect_to = null;
                    if (($current_user_id === 0) && ($policy_settings['redirect_not_logged_users'] === true)) {//guest
                        if ($policy_settings['redirect_to_policy_first'] === true) {
                            $will_redirect_to = $policy_settings['policy_page'];
                        } elseif (($policy_settings['redirect_to_policy_first'] === false) && (($term_settings['redirect_not_logged_users'] === true))) {
                            $will_redirect_to = $term_settings['term_page'];
                        }
                    } else {
                        if ($policy_settings['redirect_logged_users'] === true) {
                            if ($policy_settings['redirect_to_policy_first'] === true) {
                                $will_redirect_to = $policy_settings['policy_page'];
                            } elseif (($policy_settings['redirect_to_policy_first'] === false) && ($term_settings['redirect_logged_users'] === true)) {
                                $will_redirect_to = $term_settings['term_page'];
                            }
                        }
                    }
                    if (!is_null($will_redirect_to) && ($will_redirect_to != '')) {
                        if ($this->isAcceptedPolicy()) {
                            $will_redirect_to = $term_settings['term_page'];
                        }
                        if ($term->isAcceptedTerm()) {
                            $will_redirect_to = $policy_settings['policy_page'];
                        }
                        $_redirect_url = get_the_permalink($will_redirect_to);
                        if(trim($_redirect_url, '/') != trim(home_url('/'), '/')) {
                            $term_settings['term_page'] = (int)$term_settings['term_page'];
                            $policy_settings['policy_page'] = (int)$policy_settings['policy_page'];
                            $p_p = $policy_settings['policy_page'];
                            $t_p = $term_settings['term_page'];
                            $c_page = $page_id;
                            if(intval($c_page) > 0) {
                                if($c_page != $p_p && $c_page != $t_p) {
                                    $redirect_url = $_redirect_url;
                                } else {
                                    $redirect_url = '';
                                }
                            } else {
                                $redirect_url = $_redirect_url;
                            }
                        }
                    }
                }
            }
        }
        wp_send_json_success(array('redirect_url' => $redirect_url));
    }
}