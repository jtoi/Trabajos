<?php

class NjtGdprDataRectification
{
    private $post_type = 'njt-gdpr-rectific';

    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_data_rectification_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_data_rectification_settings', array($this, 'ajaxUpdateSettings'));
        add_action('wp_ajax_njt_gdpr_data_rectification_request_action', array($this, 'ajaxRequestAction'));

        add_action('wp_ajax_njt_gdpr_data_rectification', array($this, 'ajaxDataRectification'));
        add_action('wp_ajax_nopriv_njt_gdpr_data_rectification', array($this, 'ajaxDataRectification'));

        add_shortcode('njt_gdpr_data_rectification', array($this, 'shortcodeData'));

        add_action('wp_enqueue_scripts', array($this, 'registerWpEnqueue'));

        add_action('init', array($this, 'registerCustomPostType'));
    }
    public function registerCustomPostType()
    {
        $labels = array(
            'name'               => __('NJT Data Rectification', NJT_GDPR_I18N),
        );
        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => false,
            'rewrite'            => array('slug' => $this->post_type),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'can_export'         => false,
            'supports'           => array('title'),//array('title', 'custom-fields'),
            'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5
            ),
            'map_meta_cap' => true,// Set to `false`, if users are not allowed to edit/delete existing posts
        );
        register_post_type($this->post_type, $args);
    }
    public function getSettings()
    {
        $defaults = array(
            'email_subject' => '',
            'email_body' => '',
            'success_mess' => __('Success', NJT_GDPR_I18N)
        );
        $settings = get_option('njt_gdpr_data_rectification', array());
        $settings = wp_parse_args($settings, $defaults);
        $settings = array_map('stripslashes_deep', $settings);
        return $settings;
    }
    public function getRequests()
    {
        $requests = array();
        $_requests = get_posts(array(
            'post_type' => $this->post_type,
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));
        foreach ($_requests as $k => $v) {
            $user_id = get_post_meta($v->ID, '_user_id', true);
            
            $requests[] = array(
                'id' => $v->ID,
                'user_id' => $user_id,
                'email' => $v->post_title,
                'request_date' => $v->post_date,
                'old' => get_post_meta($v->ID, '_old', true),
                'new' => get_post_meta($v->ID, '_new', true),
                'sent_date' => get_post_meta($v->ID, '_date_mail_sent', true),
            );
        }
        return $requests;
    }
    public function ajaxGetSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = $this->getSettings();

        wp_send_json_success(array(
            'settings' => $settings,
            'requests' => $this->getRequests()
        ));
    }
    public function ajaxUpdateSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = ((isset($_POST['settings'])) ? (array)$_POST['settings']: array());
        $settings = njt_gdpr_maybe_sanitize_array($settings);
        if (empty($settings['email_subject']) || empty($settings['email_body'])) {
            wp_send_json_error(array('mess' => __('Please complete all required field', NJT_GDPR_I18N)));
        } else {
            update_option('njt_gdpr_data_rectification', $settings);
            wp_send_json_success();
        }
    }
    public function ajaxRequestAction()
    {
        if(!apply_filters('njt_gdpr_can_action_data_rectification', true)){
            wp_send_json_error(array('mess' => __('Permission denied.', NJT_GDPR_I18N)));
        }
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $ids = ((isset($_POST['ids'])) ? (array)$_POST['ids'] : array());
        $ids = array_map('intval', $ids);
        $request_action = ((isset($_POST['request_action'])) ? njt_gdpr_maybe_sanitize_array($_POST['request_action']) : '');

        $settings = $this->getSettings();
        if (count($ids) > 0) {
            if ($request_action == 'remove') {
                foreach ($ids as $k => $v) {
                    $check_post = get_post($v);
                    if ($check_post->post_type == $this->post_type) {
                        wp_delete_post($v, true);
                    }
                }
                wp_send_json_success(array('mess' => __('Success', NJT_GDPR_I18N), 'requests' => $this->getRequests()));
            } elseif ($request_action == 'send-email') {
                $error = null;
                foreach ($ids as $k => $v) {
                    $request_info = get_post($v);
                    if (empty($settings['email_subject']) || empty($settings['email_body'])) {
                        $error = __('Error, please enter Email Subject and Email Body first, then save and try again.', NJT_GDPR_I18N);
                        break;
                    } else {
                        wp_mail($request_info->post_title, $settings['email_subject'], $settings['email_body']);
                        update_post_meta($v, '_date_mail_sent', date('Y-m-d H:i:s', current_time('timestamp', 0)));
                    }
                }
                if (is_null($error)) {
                    wp_send_json_success(array('mess' => __('Success', NJT_GDPR_I18N), 'requests' => $this->getRequests()));
                } else {
                    wp_send_json_error(array('mess' => $error));
                }
            }
        } else {
            wp_send_json_error(array('mess' => __('No requests choosed.', NJT_GDPR_I18N)));
        }
    }

    /*
     * Frontend
     */
    public function ajaxDataRectification()
    {
        check_ajax_referer('njt_gdpr_data_rectification', 'nonce', true);
        $email = ((isset($_POST['email'])) ? njt_gdpr_maybe_sanitize_array($_POST['email']): '');
        $old = ((isset($_POST['old'])) ? njt_gdpr_maybe_sanitize_array($_POST['old']): '');
        $new = ((isset($_POST['new'])) ? njt_gdpr_maybe_sanitize_array($_POST['new']): '');
        if (empty($email)) {
            wp_send_json_error(array('mess' => __('Email is required.', NJT_GDPR_I18N)));
        }
        if (empty($old) || empty($new)) {
            wp_send_json_error(array('mess' => __('Please complete all required fields.', NJT_GDPR_I18N)));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            wp_send_json_error(array('mess' => __('Invalid email format.', NJT_GDPR_I18N)));
        }
        $current_user = wp_get_current_user();
        if ($current_user->ID != 0) {// Logged in.
            $email = $current_user->user_email;
        }
        $check = get_page_by_title($email);
        if (is_null($check) || $check->post_type != $this->post_type) {
            $insert_id = wp_insert_post(array(
                'post_type' => $this->post_type,
                'post_title' => $email
            ));
            update_post_meta($insert_id, '_user_id', $current_user->ID);
            update_post_meta($insert_id, '_old', $old);
            update_post_meta($insert_id, '_new', $new);

            if(apply_filters('njt_gdpr_email_admin_when_new_data_rectification', true)) {
                $to = get_bloginfo('admin_email');
                $subject = __('Data Rectification New Request', NJT_GDPR_I18N);
                $message = __("Hi,<br />", NJT_GDPR_I18N);
                $message .= __("You have new Data Rectification request, here is the detail:<br />", NJT_GDPR_I18N);
                $message .= __("Email: ", NJT_GDPR_I18N) . $email . '<br />';
                if($current_user->ID) {
                    $message .= __("User ID:", NJT_GDPR_I18N) .$current_user->ID .  '<br />';
                }
                wp_mail($to, $subject, $message);
            }
        }
        $settings = $this->getSettings();
        wp_send_json_success(array('mess' => $settings['success_mess']));
    }
    public function shortcodeData($atts)
    {
        $atts = shortcode_atts( array(
            'email_text' => __('Your Email', NJT_GDPR_I18N),
            'old_information_text' => __('Enter your information as it currently exists in our system', NJT_GDPR_I18N),
            'new_information_text' => __('Enter the correct version of your information', NJT_GDPR_I18N),
            'btn_text' => __('Submit', NJT_GDPR_I18N),
        ), $atts, 'njt_gdpr_data_rectification' );

        $email = '';
        $current_user = wp_get_current_user();
        if ($current_user->ID != 0) {
            $email = $current_user->user_email;
        }
        ob_start();
        ?>
        <form action="" method="POST" class="njt_gdpr_data_rectification_form">
            <p>
                <label for="njt_gdpr_data_rectification_form_email"><?php echo esc_html($atts['email_text']); ?></label>
                <input type="text" name="email" id="njt_gdpr_data_rectification_form_email" class="njt_gdpr_data_rectification_form_email" value="<?php echo esc_attr($email); ?>" required <?php echo ((!empty($email)) ? 'readonly' : ''); ?> />
            </p>
            <p>
                <label for="your-old-information"><?php echo esc_html($atts['old_information_text']); ?></label>
                <textarea name="your-old-information" id="your-old-information" cols="30" rows="10" required></textarea>
            </p>
            <p>
                <label for="your-new-information"><?php echo esc_html($atts['new_information_text']); ?></label>
                <textarea name="your-new-information" id="your-new-information" cols="30" rows="10" required></textarea>
            </p>
            <p>
                <button type="button" class="njt_gdpr_data_rectification_btn"><?php echo esc_html($atts['btn_text']); ?></button>
            </p>
        </form>
        <?php
        return ob_get_clean();
    }
    public function registerWpEnqueue()
    {
        wp_register_script('njt-gdpr-data-rectification', NJT_GDPR_URL . '/assets/home/js/data-rectification.js', array('jquery'), '1.0', false);
        wp_enqueue_script('njt-gdpr-data-rectification');
        wp_localize_script('njt-gdpr-data-rectification', 'njt_gdpr_data_rectification', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt_gdpr_data_rectification')
        ));
    }
}
