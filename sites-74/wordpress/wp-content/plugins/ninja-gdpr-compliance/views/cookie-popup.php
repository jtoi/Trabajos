<div class="wrap">
    <h1><?php _e('Cookie Popup', NJT_GDPR_I18N); ?></h1>
    <form action="options.php" method="post">
        <?php settings_fields('njt_gdpr_cookie_popup'); ?>
        <?php do_settings_sections('njt_gdpr_cookie_popup'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="njt_gdpr_cookie_popup_content"><?php _e('Content', NJT_GDPR_I18N); ?></label>
                </th>
                <td>
                    <?php wp_editor(get_option('njt_gdpr_cookie_popup_content', ''), 'njt_gdpr_cookie_popup_content', array('editor_height' => 150)); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="njt_gdpr_cookie_popup_agree_text"><?php _e('Agree Text', NJT_GDPR_I18N); ?></label>
                </th>
                <td>
                    <input type="text" name="njt_gdpr_cookie_popup_agree_text" id="njt_gdpr_cookie_popup_agree_text" class="regular-text" value="<?php echo esc_attr(get_option('njt_gdpr_cookie_popup_agree_text', '')); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="njt_gdpr_cookie_popup_decline_text"><?php _e('Decline Text', NJT_GDPR_I18N); ?></label>
                </th>
                <td>
                    <input type="text" name="njt_gdpr_cookie_popup_decline_text" id="njt_gdpr_cookie_popup_decline_text" class="regular-text" value="<?php echo esc_attr(get_option('njt_gdpr_cookie_popup_decline_text', '')); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="njt_gdpr_cookie_popup_display_type">
                        <?php _e('Display', NJT_GDPR_I18N); ?>
                    </label>
                </th>
                <td>
                    <select name="njt_gdpr_cookie_popup_display_type" id="njt_gdpr_cookie_popup_display_type">
                        <option value="all_pages" <?php selected(get_option('njt_gdpr_cookie_popup_display_type', 'all_pages'), 'all_pages') ?>><?php _e('Display all pages but except', NJT_GDPR_I18N); ?></option>
                        <option value="for_pages" <?php selected(get_option('njt_gdpr_cookie_popup_display_type', 'all_pages'), 'for_pages') ?>><?php _e('Display for pages...', NJT_GDPR_I18N); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="njt-tab" id="njt-tab-all_pages" style="<?php echo ((get_option('njt_gdpr_cookie_popup_display_type', 'all_pages') == 'for_pages') ? 'display: none' : ''); ?>">
                <th scope="row">
                    <label>
                        <?php _e('Display all pages but except', NJT_GDPR_I18N); ?>
                    </label>
                </th>
                <td>
                    <?php
                    $pages = get_posts(array('posts_per_page' => -1, 'post_type' => 'page'));
                    $array_hide = get_option('njt_gdpr_cookie_popup_hide_pages', array());
                    if (!$array_hide) {
                        $array_hide = array();
                    }
                    ?>
                    <input type="checkbox" id="njt-gdpr-checkall-all_pages" <?php checked(count($pages), count($array_hide)); ?> />
                    <label for="njt-gdpr-checkall-all_pages"><?php _e('All', NJT_GDPR_I18N); ?></label>
                    <ul class="njt-ul-pages">
                    <?php
                    foreach ($pages as $k => $v) {
                        ?>
                        <li>
                            <input <?php if (in_array($v->ID, $array_hide)) { echo 'checked="checked"'; }
                        ?> name="njt_gdpr_cookie_popup_hide_pages[]" class="njt_gdpr_cookie_popup_hide_pages" type="checkbox" value="<?php echo esc_attr($v->ID); ?>" id="njt_gdpr_cookie_popup_hide_page_<?php echo esc_html($v->ID); ?>" />
                            <label for="njt_gdpr_cookie_popup_hide_page_<?php echo esc_html($v->ID); ?>">
                                <?php echo esc_html($v->post_title); ?>
                            </label>
                        </li>
                        <?php
                    }
                        ?>
                    </ul>
                </td>
            </tr>
            <tr class="njt-tab" id="njt-tab-for_pages" style="<?php echo ((get_option('njt_gdpr_cookie_popup_display_type', 'all_pages') == 'all_pages') ? 'display: none' : ''); ?>">
                <th scope="row">
                    <label>
                        <?php _e('Where you want to display', NJT_GDPR_I18N); ?>
                    </label>
                </th>
                <td>
                    <?php
                    $pages = get_posts(array('posts_per_page' => -1, 'post_type' => 'page'));
                    $array_show = get_option('njt_gdpr_cookie_popup_show_pages', array());
                    if (!$array_show) {
                        $array_show = array();
                    }
                    ?>
                    <input type="checkbox" id="njt-gdpr-checkall-for_pages" <?php checked(count($pages), count($array_show)); ?> />
                    <label for="njt-gdpr-checkall-for_pages"><?php _e('All', NJT_GDPR_I18N); ?></label>
                    <ul class="njt-ul-pages">
                    <?php
                    foreach ($pages as $k => $v) {
                        ?>
                        <li>
                            <input <?php if (in_array($v->ID, $array_show)) { echo 'checked="checked"'; }
                        ?> name="njt_gdpr_cookie_popup_show_pages[]" class="njt_gdpr_cookie_popup_show_pages" type="checkbox" value="<?php echo esc_attr($v->ID); ?>" id="njt_gdpr_cookie_popup_show_page_<?php echo esc_html($v->ID); ?>" />
                            <label for="njt_gdpr_cookie_popup_show_page_<?php echo esc_html($v->ID); ?>">
                                <?php echo esc_html($v->post_title); ?>
                            </label>
                        </li>
                        <?php
                    }
                        ?>
                    </ul>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>