<?php

class NjtGdprDataAccess
{
    private $post_type = 'njt-gdpr-data-access';

    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_dataaccess_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_dataaccess_settings', array($this, 'ajaxUpdateSettings'));
        add_action('wp_ajax_njt_gdpr_dataaccess_request_action', array($this, 'ajaxRequestAction'));

        add_action('wp_ajax_njt_gdpr_dataaccess', array($this, 'ajaxDataAccess'));
        add_action('wp_ajax_nopriv_njt_gdpr_dataaccess', array($this, 'ajaxDataAccess'));

        add_shortcode('njt_gdpr_data_access', array($this, 'shortcodeDataAccess'));

        add_action('wp_enqueue_scripts', array($this, 'registerWpEnqueue'));

        add_action('init', array($this, 'registerCustomPostType'));
    }
    public function registerCustomPostType()
    {
        $labels = array(
            'name'               => __('NJT Data Access', NJT_GDPR_I18N),
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
        $settings = get_option('njt_gdpr_dataaccess', array());
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
            $email = '';
            if ($user_id != '0') {
                $userdata = get_userdata($user_id);
                $email = $userdata->user_email;
            }
            $requests[] = array(
                'id' => $v->ID,
                'user_id' => $user_id,
                'email' => $email,
                'email_request' => $v->post_title,
                'request_date' => $v->post_date,
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
        update_option('njt_gdpr_dataaccess', njt_gdpr_maybe_sanitize_array($settings));
        wp_send_json_success();
    }
    public function ajaxRequestAction()
    {
        if(!apply_filters('njt_gdpr_can_action_data_access', true)){
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
            } elseif ($request_action == 'send-email') {
                foreach ($ids as $k => $v) {
                    $this->sendDataAccessMail($v);
                    update_post_meta($v, '_date_mail_sent', date('Y-m-d H:i:s', current_time('timestamp', 0)));
                }
            }
            wp_send_json_success(array('mess' => __('Success', NJT_GDPR_I18N), 'requests' => $this->getRequests()));
        } else {
            wp_send_json_error(array('mess' => __('No requests choosed', NJT_GDPR_I18N)));
        }
    }
    /*
     * Frontend
     */
    public function ajaxDataAccess()
    {
        check_ajax_referer('njt_gdpr_dataaccess', 'nonce', true);
        $email = ((isset($_POST['email'])) ? sanitize_email($_POST['email']) : '');
        if (empty($email)) {
            wp_send_json_error(array('mess' => __('Email is required.', NJT_GDPR_I18N)));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            wp_send_json_error(array('mess' => __('Invalid email format.', NJT_GDPR_I18N)));
        }
        $check = get_page_by_title($email);
        if (is_null($check) || ($check->post_type != $this->post_type)) {
            $insert_id = wp_insert_post(array(
                'post_type' => $this->post_type,
                'post_title' => $email
            ));
            $current_user_id = get_current_user_id();
            update_post_meta($insert_id, '_user_id', $current_user_id);
            //update_post_meta($insert_id, '_date_mail_sent', '');
            if(apply_filters('njt_gdpr_email_admin_when_new_dataaccess', true)) {
                $to = get_bloginfo('admin_email');
                $subject = __('Data Access New Request', NJT_GDPR_I18N);
                $message = __("Hi,<br />", NJT_GDPR_I18N);
                $message .= __("You have new Data Access request, here is the detail:<br />", NJT_GDPR_I18N);
                $message .= __("Email: ", NJT_GDPR_I18N) . $email . '<br />';
                if($current_user_id > 0) {
                    $message .= __("User ID:", NJT_GDPR_I18N) . $current_user_id .  '<br />';
                }
                wp_mail($to, $subject, $message);
            }
        }
        $settings = $this->getSettings();
        wp_send_json_success(array('mess' => $settings['success_mess']));
    }
    public function shortcodeDataAccess($atts)
    {
        ob_start();
        ?>
        <form action="" method="POST" class="njt_gdpr_dataaccess_form">
            <p>
                <label for="njt_gdpr_dataaccess_form_email"><?php _e('Email', NJT_GDPR_I18N); ?></label>
                <input type="text" name="email" id="njt_gdpr_dataaccess_form_email" class="njt_gdpr_dataaccess_form_email" value="" required />
            </p>
            <p>
                <button type="button" class="njt_gdpr_btn njt_gdpr_dataaccess_btn">
                    <?php _e('Submit', NJT_GDPR_I18N); ?>
                </button>
            </p>
        </form>
        <?php
        return ob_get_clean();
    }
    public function registerWpEnqueue()
    {
        wp_register_script('njt-gdpr-data-access', NJT_GDPR_URL . '/assets/home/js/data-access.js', array('jquery'), '1.0', false);
        wp_enqueue_script('njt-gdpr-data-access');
        wp_localize_script('njt-gdpr-data-access', 'njt_gdpr_dataaccess', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt_gdpr_dataaccess')
        ));
    }
    private function sendDataAccessMail($request_id)
    {
        $settings = $this->getSettings();

        $request_info = get_post($request_id);
        $email_to_send = $request_info->post_title;
        $data_access_services = array(
            'User' => '',
            'Comment' => '',
            'Post' => '',
            'GravityForm' => ''
        );
        foreach ($data_access_services as $k => $v) {
            $data_access_services[$k] = call_user_func(array($this, 'service' . $k), $email_to_send);
        }
        $data_access_services = apply_filters('njt_gdpr_data_access_services', $data_access_services);
        $attachments = array();
        //write to file
        $user = get_user_by('email', $email_to_send);
        if ($user === false) {
            return;
        }
        foreach ($data_access_services as $k => $v) {
            @mkdir(NJT_GDPR_DIR . '/data-access');
            if(defined('WC_VERSION') && ($k == 'User')) {
                $k .= ' - Woocomerce';
            }
            $file_name = NJT_GDPR_DIR . '/data-access/'.$user->ID.'-' . $k . '.txt';
            file_put_contents($file_name, $v);
            $attachments[] = $file_name;
        }
        if (!empty($settings['email_subject']) && !empty($settings['email_body'])) {
            wp_mail($email_to_send, $settings['email_subject'], $settings['email_body'], '', $attachments);
            if (apply_filters('njt_gdpr_delete_dataaccess_att', true)) {
                foreach ($attachments as $k => $v) {
                    @unlink($v);
                }
            }
        }
    }
    private function serviceUser($email)
    {
        $user = get_user_by('email', $email);
        $title = __(' User ', NJT_GDPR_I18N);
        if(defined('WC_VERSION')) {
            $title .= __('- Woocomerece ', NJT_GDPR_I18N);
        }
        $return = "========= ". $title." =========\n";
        if ($user !== false) {
            $user_meta = get_user_meta($user->ID);
            foreach ($user_meta as $k => $meta) {
                $return .= $k . " : " . $meta[0] . "\n";
            }
        }
        return $return;
    }
    private function serviceComment($email)
    {
        $comments = get_comments(array('author_email' => $email));
        $return = __("========= Comment =========\n", NJT_GDPR_I18N);
        foreach ($comments as $k => $v) {
            foreach ($v as $k2 => $v2) {
                $return .= $k2 . " : " . $v2 . "\n";
            }
            $return .= "------------------------------------\n";
        }
        return $return;
    }
    private function servicePost($email)
    {
        $return = __("========= Posts =========\n", NJT_GDPR_I18N);
        $user = get_user_by('email', $email);
        $posts = get_posts(array('post_type' => 'any', 'posts_per_page' => -1, 'author' => $user->ID, 'post_status' => 'any'));
        foreach ($posts as $k => $post) {
            $return .= "Post ID: " . $post->ID . "\n";
            $return .= "Post Title: " . $post->post_title . "\n";
            $return .= "------------------------------------\n";
        }
        return $return;
    }
    private function serviceGravityForm($email)
    {
        $fields = array(
            'field_filters' => array(
                array('value' => $email)
            )
        );
        $return = __("========= Gravity Form Entries =========\n", NJT_GDPR_I18N);
        $entries = class_exists('GFAPI') ? GFAPI::get_entries(0, $fields) : array();
        foreach($entries as $k => $v) {
            unset($v['id']);
            unset($v['form_id']);
            unset($v['is_read']);
            unset($v['is_starred']);
            foreach($v as $k2 => $v2) {
                $return .= $k2 . ": " . $v2 . "\n";
            }
            $return .= "------------------------------------\n";
        }
        return $return;
    }
}
