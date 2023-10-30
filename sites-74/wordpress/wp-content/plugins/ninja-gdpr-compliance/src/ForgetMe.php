<?php

class NjtGdprForgetMe
{
    private $post_type = 'njt-gdpr-forget-me';

    public function init()
    {
        add_action('wp_ajax_njt_gdpr_get_forgetme_settings', array($this, 'ajaxGetSettings'));
        add_action('wp_ajax_njt_gdpr_update_forgetme_settings', array($this, 'ajaxUpdateSettings'));
        add_action('wp_ajax_njt_gdpr_forget_me_request_action', array($this, 'ajaxRequestAction'));

        add_action('wp_ajax_njt_gdpr_forgetme', array($this, 'ajaxForgetMe'));
        add_action('wp_ajax_nopriv_njt_gdpr_forgetme', array($this, 'ajaxForgetMe'));

        add_shortcode('njt_gdpr_forgetme', array($this, 'shortcodeForgetMe'));

        add_action('wp_enqueue_scripts', array($this, 'registerWpEnqueue'));

        add_action('init', array($this, 'registerCustomPostType'));
    }
    public function registerCustomPostType()
    {
        $labels = array(
            'name'               => __('NJT Forget Me', NJT_GDPR_I18N),
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
            'with_posts' => 'delete',//or assign
            'assign_to_user' => '',
            'users' => array(),
            'success_mess' => __('Success', NJT_GDPR_I18N),
        );
        $settings = get_option('njt_gdpr_forget_me', array());
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

        $_users = get_users();
        $settings['users'] = array();
        foreach ($_users as $k => $v) {
            $settings['users'][] = array('id' => $v->ID, 'name' => $v->data->user_nicename);
        }
        wp_send_json_success(array('settings' => $settings, 'users' => $users, 'requests' => $this->getRequests()));
    }
    public function ajaxUpdateSettings()
    {
        check_ajax_referer('njt_gdpr', 'nonce', true);
        $settings = ((isset($_POST['settings'])) ? (array)$_POST['settings']: array());
        $settings = njt_gdpr_maybe_sanitize_array($settings);
        update_option('njt_gdpr_forget_me', $settings);
        wp_send_json_success();
    }
    public function ajaxRequestAction()
    {
        if(!apply_filters('njt_gdpr_can_action_forget_me', true)){
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
            } elseif ($request_action == 'forget-send-email') {
                foreach ($ids as $k => $v) {
                    $user_id = (int)get_post_meta($v, '_user_id', true);
                    $request_info = get_post($v);
                    if ($user_id > 0) {
                        //delete posts
                        $posts_by_users = get_posts(array(
                            'post_type' => apply_filters('njt_gdpr_forget_me_posttype_remove', 'post'),
                            'posts_per_page' => -1,
                            'author' => $user_id,
                            'post_status' => 'any'
                        ));
                        if ($settings['with_posts'] == 'delete') {
                            foreach ($posts_by_users as $k2 => $v2) {
                                wp_delete_post($v2->ID, true);
                            }
                        } elseif ($settings['with_posts'] == 'assign') {
                            $assign_to_user = $settings['assign_to_user'];
                            if (!empty($assign_to_user)) {
                                foreach ($posts_by_users as $k2 => $v2) {
                                    wp_update_post(array(
                                        'ID' => $v2->ID,
                                        'author' => $assign_to_user
                                    ));
                                }
                            }
                        }
                        //delete comments
                        $email_will_be_delete = $request_info->post_title;
                        $comments = get_comments(array('author_email' => $email_will_be_delete));
                        foreach ($comments as $k2 => $v2) {
                            wp_delete_comment($v2->comment_ID);
                        }
                        //delete user account
                        $user = get_user_by('id', $user_id);
                        if(!in_array('administrator', $user->roles)) {
                            wp_delete_user($user_id);
                        } else {
                            exit('cant delete');
                        }
                    } else {
                        //just delete comments by email
                        $email_will_be_delete = $request_info->post_title;
                        $comments = get_comments(array('author_email' => $email_will_be_delete));
                        foreach ($comments as $k2 => $v2) {
                            wp_delete_comment($v2->comment_ID);
                        }
                    }
                    update_post_meta($v, '_date_mail_sent', date('Y-m-d H:i:s', current_time('timestamp', 0)));
                    wp_mail($request_info->post_title, $settings['email_subject'], $settings['email_body']);
                }
            }
            wp_send_json_success(array('requests' => $this->getRequests()));
        } else {
            wp_send_json_error(array('mess' => 'No requests choosed'));
        }
    }

    /*
     * Frontend
     */
    public function ajaxForgetMe()
    {
        check_ajax_referer('njt_gdpr_forget_me', 'nonce', true);
        $email = ((isset($_POST['email'])) ? sanitize_email($_POST['email']): '');
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
            if(apply_filters('njt_gdpr_email_admin_when_new_forget', true)) {
                $to = get_bloginfo('admin_email');
                $subject = __('Forget Me New Request', NJT_GDPR_I18N);
                $message = __("Hi,<br />", NJT_GDPR_I18N);
                $message .= __("You have new ForgetMe request, here is the detail:<br />", NJT_GDPR_I18N);
                $message .= __("Email: ", NJT_GDPR_I18N) . $email . '<br />';
                
                if($current_user_id > 0) {
                    $message .= __("User ID: ", NJT_GDPR_I18N) . $current_user_id . '<br />';
                }
                wp_mail($to, $subject, $message);
            }
        }
        $settings = $this->getSettings();
        wp_send_json_success(array('mess' => $settings['success_mess']));
    }
    public function shortcodeForgetMe($atts)
    {
        ob_start();
        ?>
        <form action="" method="POST" class="njt_gdpr_forget_me_form">
            <p>
                <label for="njt_gdpr_forget_me_form_email"><?php _e('Email', NJT_GDPR_I18N); ?></label>
                <input type="text" name="email" id="njt_gdpr_forget_me_form_email" class="njt_gdpr_forget_me_form_email" value="" required />
            </p>
            <p>
                <button type="button" class="njt_gdpr_forget_me_btn"><?php _e('Submit', NJT_GDPR_I18N); ?></button>
            </p>
        </form>
        <?php
        return ob_get_clean();
    }
    public function registerWpEnqueue()
    {
        wp_register_script('njt-gdpr-forget-me', NJT_GDPR_URL . '/assets/home/js/forget-me.js', array('jquery'), '1.0', false);
        wp_enqueue_script('njt-gdpr-forget-me');
        wp_localize_script('njt-gdpr-forget-me', 'njt_gdpr_forget_me', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt_gdpr_forget_me')
        ));
    }
}
