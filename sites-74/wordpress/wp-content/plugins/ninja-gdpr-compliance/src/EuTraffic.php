<?php

class NjtGdprEuTraffic
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_eu_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_eu_settings', array($this, 'ajaxUpdateSettings'));
    }
    public function getSettings()
    {
        $defaults = array(
            'is_enable' => '1',
            'options' => 'block',//block or work
            'conuntries_to_block' => array('AT','BE','BG','HR','CZ','DK','EE','FI','FR','DE','GR','HU','IE','IT','LV','LT','LU','MT','NL','PL','PT','RO','SK','SI','SE','GB', 'ES'),
            'page_redirect' => ''
        );
        $settings = get_option('njt_gdpr_eu', array());
        $settings = wp_parse_args($settings, $defaults);

        $settings['is_enable'] = (($settings['is_enable'] == '1') ? true : false);
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

        if (isset($settings['is_enable'])) {
            if ($settings['is_enable'] == 'true') {
                $settings['is_enable'] = '1';
            } else {
                $settings['is_enable'] = '0';
            }
        }
        update_option('njt_gdpr_eu', $settings);
        wp_send_json_success();
    }
}
