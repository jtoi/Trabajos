<?php

class NjtGdprUnsubscribe
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_unsub_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_unsub_settings', array($this, 'ajaxUpdateSettings'));
    }
    public function getSettings()
    {
        $defaults = array(
            'unsubscribe_page' => '',
            'is_using_mailchimp' => '1',
            'mailchimp_url' => '',
            'mailchimp_ifrm' => '',
        );
        $settings = wp_parse_args(get_option('njt_gdpr_unsub', array()), $defaults);

        $settings['is_using_mailchimp'] = (($settings['is_using_mailchimp'] == '1') ? true : false);
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
        
        if (isset($settings['is_using_mailchimp'])) {
            if ($settings['is_using_mailchimp'] == 'true') {
                $settings['is_using_mailchimp'] = '1';
            } else {
                $settings['is_using_mailchimp'] = '0';
            }
        }
        update_option('njt_gdpr_unsub', $settings);
        wp_send_json_success();
    }
}
