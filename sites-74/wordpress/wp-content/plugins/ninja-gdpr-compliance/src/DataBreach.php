<?php

class NjtGdprDataBreach
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_data_breach_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_data_breach_settings', array($this, 'ajaxUpdateSettings'));
    }
    public function getSettings()
    {
        $defaults = array(
            'email_subject' => '',
            'email_body' => ''
        );
        $settings = get_option('njt_gdpr_data_breach', array());
        $settings = wp_parse_args($settings, $defaults);
        $settings = array_map('stripslashes_deep', $settings);
        return $settings;
    }
    public function ajaxGetSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = $this->getSettings();

        wp_send_json_success(array(
            'settings' => $settings
        ));
    }
    public function ajaxUpdateSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = ((isset($_POST['settings'])) ? (array)$_POST['settings']: array());
        $settings = njt_gdpr_maybe_sanitize_array($settings);
        
        if (empty($_POST['settings']['email_subject']) || empty($_POST['settings']['email_body'])) {
            wp_send_json_error(array('mess' => __('Please complete all required fields.', NJT_GDPR_I18N)));
            exit();
        }
        update_option('njt_gdpr_data_breach', $settings);
        if(apply_filters('njt_gdpr_can_action_data_breach', true)){
            $settings = $this->getSettings();
            //get users and send
            $users = get_users();
            $sent = $fail = 0;
            foreach ($users as $k => $user) {
                $t = wp_mail($user->data->user_email, $settings['email_subject'], $settings['email_body']);
                if ($t) {
                    $sent ++;
                } else {
                    $fail ++;
                }
            }
            wp_send_json_success(array('mess' => sprintf(__('Sent to %1$d users. Success: %2$d. Fail: %3$d', NJT_GDPR_I18N), count($users), $sent, $fail)));
        } else {
            wp_send_json_success(array('mess' => __('Permission denied.', NJT_GDPR_I18N)));
        }
    }
}
