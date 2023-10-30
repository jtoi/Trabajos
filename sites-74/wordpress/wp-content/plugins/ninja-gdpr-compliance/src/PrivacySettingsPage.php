<?php

class NjtGdprPrivacySettingsPage
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_pvsettings_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_pvsettings_settings', array($this, 'ajaxUpdateSettings'));
        add_shortcode('njt_gdpr_privacy_settings', array($this, 'privacySettingsPageShortcode'));
    }
    public function getSettings()
    {
        $defaults = array(
            'choosed_page' => '',
            'cookie_des' =>  __('We use cookies to provide a personalised experience for our users.', NJT_GDPR_I18N),
            'fb_des' => __('Third party service for managing advertisement activities related to the resource via Facebook', NJT_GDPR_I18N),
            'gg_des' => __('Used for aggregated statistics to improve user experience on the resource.', NJT_GDPR_I18N),
        );
        $settings = wp_parse_args(get_option('njt_gdpr_privacy_settings_page', array()), $defaults);
        return array_map('stripslashes_deep', $settings);
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
        
        update_option('njt_gdpr_privacy_settings_page', $settings);
        wp_send_json_success();
    }
    public function privacySettingsPageShortcode()
    {
        ob_start();
        $permission = njt_gdpr_get_permission();
        $settings = $this->getSettings();
        ?>
        <form action="" method="POST" class="njt-gdpr-privacy-settings-frm">
            <table>
                <thead>
                    <tr>
                        <th><?php _e('Name', NJT_GDPR_I18N); ?></th>
                        <th><?php _e('Description', NJT_GDPR_I18N); ?></th>
                        <th><?php _e('Enable ?', NJT_GDPR_I18N); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Cookie', NJT_GDPR_I18N); ?></td>
                        <td><?php echo esc_html($settings['cookie_des']); ?></td>
                        <td><input type="checkbox" name="s[]" value="cookie" <?php checked($permission['cookie'], '1'); ?> /></td>
                    </tr>
                    <tr>
                        <td><?php _e('Facebook Pixel', NJT_GDPR_I18N); ?></td>
                        <td><?php echo esc_html($settings['fb_des']); ?></td>
                        <td><input type="checkbox" name="s[]" value="fb" <?php checked($permission['fb'], '1'); ?> /></td>
                    </tr>
                    <tr>
                        <td><?php _e('Google Analytics', NJT_GDPR_I18N); ?></td>
                        <td><?php echo esc_html($settings['gg_des']); ?></td>
                        <td><input type="checkbox" name="s[]" value="gg" <?php checked($permission['gg'], '1'); ?> /></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="njt-gdpr-privacy-settings-btn"><?php _e('Save Changes', NJT_GDPR_I18N); ?></button>
        </form>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.njt-gdpr-privacy-settings-btn').on('click', function(event) {
                    var frm = $(this).closest('form');
                    var s = [];
                    $.each(frm.find('input[name^="s"]'), function(index, el) {
                        if ($(el).prop('checked')) {
                            s.push($(el).val());
                        }
                    });
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'njt_gdpr_save_privacy_settings',
                            s: s
                        },
                    })
                    .done(function(json) {
                        if (json.success) {
                            alert('Success');
                            location.reload();
                        } else {
                            alert(json.data.mess);
                        }
                    })
                    .fail(function() {
                        alert('<?php _e('Please refresh and try again.', NJT_GDPR_I18N); ?>');
                    });
                    return false;
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
