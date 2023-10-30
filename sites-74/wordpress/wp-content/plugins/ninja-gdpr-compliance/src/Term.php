<?php

class NjtGdprTerm
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_term_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_term_settings', array($this, 'ajaxUpdateSettings'));

        add_action('wp_ajax_njt_gdpr_accept_term', array($this, 'ajaxAcceptTerm'));
        add_action('wp_ajax_nopriv_njt_gdpr_accept_term', array($this, 'ajaxAcceptTerm'));

        add_shortcode('njt_gdpr_term', array($this, 'shortcodeTerm'));

        add_action('wp_enqueue_scripts', array($this, 'registerWpEnqueue'));
    }
    public function getSettings()
    {
        $defaults = array(
            'term_page' => '',
            'redirect_page_after_accepted' => '',
            'redirect_logged_users' => '0',
            'redirect_not_logged_users' => '0',
            'consent_expire' => '365'
        );
        $settings = get_option('njt_gdpr_term', array());
        $settings = wp_parse_args($settings, $defaults);

        $settings['redirect_logged_users'] = (($settings['redirect_logged_users'] == '1') ? true : false);
        $settings['redirect_not_logged_users'] = (($settings['redirect_not_logged_users'] == '1') ? true : false);
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
        update_option('njt_gdpr_term', $settings);
        wp_send_json_success();
    }
    public function ajaxAcceptTerm()
    {
        check_ajax_referer('njt_gdpr_term', 'nonce', true);
        $settings = $this->getSettings();
        $allow = ((isset($_POST['allow'])) ? $_POST['allow'] : '1');
        $allow = sanitize_text_field($allow);
        
        $current_user_id = get_current_user_id();
        if ($current_user_id === 0) {//guest
            // $_SESSION['njt_gdpr_acceped_term'] = $allow;
            set_transient(njt_get_transient_key('njt_gdpr_acceped_term'), $allow, DAY_IN_SECONDS);
        } else {
            update_user_meta($current_user_id, 'njt_gdpr_acceped_term', $allow);
            update_user_meta($current_user_id, 'njt_gdpr_acceped_term_at', current_time('timestamp', 0));
        }
        if ($allow == '1') {
            $redirect = get_the_permalink($settings['redirect_page_after_accepted']);
        } else {
            $redirect = get_the_permalink($settings['term_page']);
        }
        if (!$redirect) {
            $redirect = home_url('/');
        }
        wp_send_json_success(array('redirect_url' => $redirect));
    }
    public function shortcodeTerm($atts)
    {
        ob_start();
        $atts = shortcode_atts(array(), $atts, 'njt_gdpr_term');
        echo '<form action="" method="POST">';
        if ($this->isAcceptedTerm()) {
            echo '<button type="button" class="njt_gdpr_term_decline_btn">'.esc_html(__('Decline', NJT_GDPR_I18N)).'</button>';
        } else {
            echo '<button type="button" class="njt_gdpr_term_accept_btn">'.esc_html(__('Accept', NJT_GDPR_I18N)).'</button>';
        }
        echo '</form>';
        return ob_get_clean();
    }
    public function registerWpEnqueue()
    {
        wp_register_script('njt-gdpr-term', NJT_GDPR_URL . '/assets/home/js/term.js', array('jquery'), '1.0', false);
        wp_enqueue_script('njt-gdpr-term');
        wp_localize_script('njt-gdpr-term', 'njt_gdpr_term', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt_gdpr_term')
        ));
    }
    public function isAcceptedTerm()
    {
        $current_user_id = get_current_user_id();
        if ($current_user_id === 0) {//guest
            // return ((isset($_SESSION['njt_gdpr_acceped_term'])) && ($_SESSION['njt_gdpr_acceped_term'] == '1'));
            return get_transient(njt_get_transient_key('njt_gdpr_acceped_term')) == '1';
        } else {
            $settings = $this->getSettings();
            if ((get_user_meta($current_user_id, 'njt_gdpr_acceped_term', true) == '1')) {
                $is_expired = (current_time('timestamp', 0) - get_user_meta($current_user_id, 'njt_gdpr_acceped_term_at', true)) > ($settings['consent_expire'] * 86400);
                return !$is_expired;
            } else {
                return false;
            }
        }
    }
}
