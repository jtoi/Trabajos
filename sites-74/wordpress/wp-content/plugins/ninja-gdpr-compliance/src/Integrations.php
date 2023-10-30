<?php

class NjtGdprIntegrations
{
    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_integrations_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_integrations_settings', array($this, 'ajaxUpdateSettings'));

        $eu = njt_eu_settings();
        add_action('wp_ajax_njt_gdpr_save_privacy_settings', array($this, 'ajaxSavePrivacySettings'));
        add_action('wp_ajax_nopriv_njt_gdpr_save_privacy_settings', array($this, 'ajaxSavePrivacySettings'));

        if(($eu['status'] == 'block-all') || ($eu['status'] == 'work' && $eu['country_is_blocked'])) {
            add_filter('wpcf7_form_elements', array($this, 'wpcf7FormElements'), 1000);

            add_filter('gform_pre_render', array($this, 'gformPreRender'), 1000);
            add_filter('gform_validation', array($this, 'gformValidation'), 1000);

            add_filter('comment_form_submit_field', array($this, 'commentFormSubmitField'), 1000, 2);
            add_action('wp_footer', array($this, 'wpFooter'));

            add_action('register_form', array($this, 'registerForm'));
            add_action('woocommerce_review_order_before_submit', array($this, 'wooBeforeSubmit'), 1000);

            add_action('groups_forum_new_reply_after', array($this, 'budypressFilter'), 999 );
            add_action('bp_after_message_reply_box', array($this, 'budypressFilter'), 999 );
            add_action('bp_after_group_forum_post_new', array($this, 'budypressFilter'), 999 );
            add_action('bp_after_messages_compose_content', array($this, 'budypressFilter'), 999 );
            add_action('groups_forum_new_topic_after', array($this, 'budypressFilter'), 999 );
            add_action('bp_activity_post_form_options', array($this, 'budypressFilter'), 999 );
        }
    }

    public function budypressFilter()
    {
        $settings = $this->getSettings();
        if($settings['bp']['is_enable']) {
            echo '<div class="njt_gdpr_bp_checkbox_wrap">
            <label for="njt_gdpr_bp_checkbox">
                '.esc_html($settings['bp']['des']).'
                <input type="checkbox" name="njt_gdpr_bp_checkbox" id="njt_gdpr_bp_checkbox" required="required" />
            </label></div>';
        }
    }
    public function wpcf7FormElements($code)
    {
        $settings = $this->getSettings();
        if ($settings['cf7']['is_enable']) {
            $code .= '<p class="njt-gdpr-cf7-p"><label>'.esc_html($settings['cf7']['des']).'<span class="wpcf7-form-control-wrap njt-gdpr-accept"><span class="wpcf7-form-control wpcf7-acceptance"><span class="wpcf7-list-item"><input type="checkbox" name="njt-gdpr-accept" value="1" aria-invalid="false"></span></span></span></label></p>';
        }
        return $code;
    }
    public function gformValidation($validation_result)
    {
        if(! apply_filters('njt_gdpr_gform', true)) return $validation_result;
        $settings = $this->getSettings();

        //$_POST['input_699.1']
        if ($settings['gf']['is_enable'] && !isset($_POST['input_699_1'])) {
            $validation_result['is_valid'] = false;
        }

        return $validation_result;
    }
    
    public function gformPreRender($form)
    {
        if(! apply_filters('njt_gdpr_gform', true)) return $form;
        $settings = $this->getSettings();
        
        if ($settings['gf']['is_enable'] && isset($form['fields'])) {
            $checkbox_field = array(
                'id' => 699,
                'type' => 'checkbox',
                'label' => $settings['gf']['label'],
                //'cssClass' => 'njt_gdpr_gf',
                'adminLabel'  => '',
                'size'        => 'medium',
                'isRequired'  => true,
                'description' => $settings['gf']['des'],
                'visibility'  => 'visible',
                'choices'     => array(
					array(
						'text'       => $settings['gf']['checkbox_text'],
						'value'      => '1',
                        'isSelected' => false,
                        'price'      => '',
					)
                )
            );
            if((count($_POST) > 0) && !isset($_POST["input_699.1"])) {
				$checkbox_field['failed_validation'] = true;
				$checkbox_field['validation_message'] = $settings['gf']['validation_text'];
            }
            $form['fields'][] = GF_Fields::create($checkbox_field);

        }
        return $form;
    }
    public function commentFormSubmitField($submit_field, $args)
    {
        $settings = $this->getSettings();
        if ($settings['comment']['is_enable']) {
            $submit_field = '<p><label>'.esc_html($settings['comment']['des']).'<input type="checkbox" name="njt-gdpr-comment-accept" id="njt-gdpr-comment-accept" value="1" aria-invalid="false"></label></p>' . $submit_field;
        }
        return $submit_field;
    }
    public function wpFooter()
    {
        $settings = $this->getSettings();
        $comment = $woo = $fb = $gg = false;
        if ($settings['comment']['is_enable']) {
            $comment = true;
        }

        $permission = njt_gdpr_get_permission();

        if ($settings['woo']['is_enable'] && !empty($settings['woo']['des'])) {
            $woo = true;
        }
        if ($settings['fb']['is_enable'] && ($permission['fb'] == '0')) {
            $fb = true;
        }
        if ($settings['gg']['is_enable'] && ($permission['gg'] == '0')) {
            $gg = true;
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                <?php if($comment): ?>
                $('#commentform').on('submit', function(event) {
                    if (!$('#njt-gdpr-comment-accept').prop('checked')) {
                        alert('<?php _e('Please complete all required fields.'); ?>');
                        return false;
                    } else {
                        return true;
                    }
                    return false;
                });
                <?php endif; ?>
                <?php if($woo): ?>
                    $(document).on('click', '[name="woocommerce_checkout_place_order"]', function(event) {
                        if (!$('#njt-gdpr-accept-woo').prop('checked')) {
                            alert('<?php _e('Please complete all required fields.', NJT_GDPR_I18N); ?>');
                            return false;
                        } else {
                            return true;
                        }
                        return false;
                    });
                <?php endif; ?>
            });
            <?php if($fb): //https://developers.facebook.com/docs/facebook-pixel/events-advanced-use-cases/v3.0 ?>
                if (typeof fbq != 'undefined') { fbq('consent', 'revoke'); }
            <?php endif; ?>

            <?php if($gg): //https://developers.google.com/analytics/devguides/collection/gtagjs/user-opt-out ?>
                window['ga-disable-<?php echo esc_html($settings['gg']['tracking_id']); ?>'] = true;
            <?php endif; ?>
        </script>
        <?php
    }
    public function registerForm()
    {
        $settings = $this->getSettings();
        if (!empty($settings['user']['des'])) {
            echo '<p>'.esc_html($settings['user']['des']).'</p>';
        }
    }
    public function wooBeforeSubmit()
    {
        $settings = $this->getSettings();
        if ($settings['woo']['is_enable'] && !empty($settings['woo']['des'])) {
            echo '<p><label>'.esc_html($settings['woo']['des']).' <input type="checkbox" id="njt-gdpr-accept-woo" value="1" /></label></p>';
        }
    }
    public function getSettings()
    {
        $defaults = array(
            'addthis' =>  array(
                'is_enable' => '1'
            ),
            'woo' =>  array(
                'is_enable' => '1',
                'des' => __('By submitting this form, you hereby agree that we may collect, store and process your data that you provided.', NJT_GDPR_I18N),
            ),
            'comment' => array(
                'is_enable' => '1',
                'des' => __('By submitting this form, you hereby agree that we may collect, store and process your data that you provided.', NJT_GDPR_I18N),
            ),
            'user' => array(
                'des' => __('By submitting this form, you hereby agree that we may collect, store and process your data that you provided.', NJT_GDPR_I18N)
            ),
            'cf7' => array(
                'is_enable' => '1',
                'des' => __('By submitting this form, you hereby agree that we may collect, store and process your data that you provided.', NJT_GDPR_I18N),
            ),
            'bp' => array(
                'is_enable' => '1',
                'des' => __('', NJT_GDPR_I18N),
            ),
            'gf' => array(
                'is_enable' => '1',
                'label' => __('Do you agree to storage of your data?', NJT_GDPR_I18N),
                'checkbox_text' => __('Yes', NJT_GDPR_I18N),
                'validation_message' => __('You have to agree with permision', NJT_GDPR_I18N),
                'des' => 'By submitting this form, you hereby agree that we may collect, store and process your data that you provided.',
            ),
            'fb' => array(
                'is_enable' => '1',
            ),
            'gg' => array(
                'is_enable' => '1',
                'tracking_id' => ''
            ),
        );
        $settings = get_option('njt_gdpr_integrations', array());

        $settings = wp_parse_args($settings, $defaults);

        $settings['addthis']['is_enable'] = (boolean)$settings['addthis']['is_enable'];
        $settings['woo']['is_enable'] = (boolean)$settings['woo']['is_enable'];
        $settings['comment']['is_enable'] = (boolean)$settings['comment']['is_enable'];
        $settings['cf7']['is_enable'] = (boolean)$settings['cf7']['is_enable'];
        $settings['bp']['is_enable'] = (boolean)$settings['bp']['is_enable'];
        $settings['gf']['is_enable'] = (boolean)$settings['gf']['is_enable'];
        $settings['fb']['is_enable'] = (boolean)$settings['fb']['is_enable'];
        $settings['gg']['is_enable'] = (boolean)$settings['gg']['is_enable'];
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

        $settings['addthis']['is_enable'] = (($settings['addthis']['is_enable'] == 'true') ? '1' : '0');
        $settings['woo']['is_enable'] = (($settings['woo']['is_enable'] == 'true') ? '1' : '0');
        $settings['comment']['is_enable'] = (($settings['comment']['is_enable'] == 'true') ? '1' : '0');
        $settings['cf7']['is_enable'] = (($settings['cf7']['is_enable'] == 'true') ? '1' : '0');
        $settings['bp']['is_enable'] = (($settings['bp']['is_enable'] == 'true') ? '1' : '0');
        $settings['gf']['is_enable'] = (($settings['gf']['is_enable'] == 'true') ? '1' : '0');
        $settings['fb']['is_enable'] = (($settings['fb']['is_enable'] == 'true') ? '1' : '0');
        $settings['gg']['is_enable'] = (($settings['gg']['is_enable'] == 'true') ? '1' : '0');

        update_option('njt_gdpr_integrations', $settings);
        wp_send_json_success();
    }
    public function ajaxSavePrivacySettings()
    {
        $s = ((isset($_POST['s'])) ? (array)$_POST['s'] : array());
        $s = njt_gdpr_maybe_sanitize_array($s);
        $arr = array('cookie' => '0', 'fb' => '0', 'gg' => '0');
        foreach ($s as $k => $v) {
            $arr[$v] = '1';
        }

        $current_user_id = get_current_user_id();
        if ($current_user_id == 0) {
            setcookie('njt_gdpr_allow_permissions', base64_encode(json_encode($arr)), time() + (86400 * 30 * 365), "/");
        } else {
            update_user_meta($current_user_id, 'njt_gdpr_allow_permissions', $arr);
        }

        wp_send_json_success();
        
    }
}
