<?php
function custom_search_plugin_settings_page()
{
    add_options_page(
        'Search settings',
        'WCSP Advanced',
        'manage_options',
        'wordpress-custom-search-plugin-advanced',
        'custom_search_plugin_settings_page_html'
    );
}
add_action('admin_menu', 'custom_search_plugin_settings_page');

function custom_search_plugin_settings_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <p>Use the following shortcode to add a custom search form to your posts or pages:</p>
        <pre>[custom_search post-types="post,page" element-count="6" layout="grid"]</pre>
        <p>Parameters:</p>
        <ul>
            <li><strong>post-types</strong>: Specify the post types to search, separated by commas (default: "post").</li>
            <li><strong>element-count</strong>: Number of results per page (default: 4).</li>
            <li><strong>layout</strong>: Display mode of results, either "list" or "grid" (default: "list").</li>
        </ul>
        <form action="options.php" method="post">
            <?php
            settings_fields('custom_search_plugin_options');
            do_settings_sections('wordpress-custom-search-plugin-advanced');
            submit_button('Save changes');
            ?>
        </form>
    </div>
<?php
}

function custom_search_plugin_settings_init()
{
    register_setting('custom_search_plugin_options', 'custom_search_plugin_options');

    add_settings_section(
        'custom_search_plugin_section_developers',
        'Search settings',
        'custom_search_plugin_section_developers_cb',
        'wordpress-custom-search-plugin-advanced'
    );

    add_settings_field(
        'custom_search_plugin_field_post_types',
        'Post types',
        'custom_search_plugin_field_post_types_cb',
        'wordpress-custom-search-plugin-advanced',
        'custom_search_plugin_section_developers',
        [
            'label_for' => 'custom_search_plugin_field_post_types',
            'class' => 'custom_search_plugin_row',
        ]
    );

    add_settings_field(
        'custom_search_results_per_page',
        'Results per page',
        'custom_search_plugin_field_results_per_page_cb',
        'wordpress-custom-search-plugin-advanced',
        'custom_search_plugin_section_developers',
        [
            'label_for' => 'custom_search_results_per_page',
        ]
    );

    add_settings_field(
        'custom_search_display_mode',
        'Display Mode',
        'custom_search_plugin_field_display_mode_cb',
        'wordpress-custom-search-plugin-advanced',
        'custom_search_plugin_section_developers',
        [
            'label_for' => 'custom_search_display_mode',
        ]
    );
}

add_action('admin_init', 'custom_search_plugin_settings_init');

function custom_search_plugin_section_developers_cb($args)
{
    echo '<p>Configure plugin search parameters.</p>';
}

function custom_search_plugin_field_post_types_cb($args)
{
    $options = get_option('custom_search_plugin_options');
?>
    <input type="text" id="<?= esc_attr($args['label_for']); ?>" name="custom_search_plugin_options[<?= esc_attr($args['label_for']); ?>]" value="<?= esc_attr($options[$args['label_for']] ?? ''); ?>">
    <p class="description">Specify the post types to search, separated by commas (for example, post, page).</p>
<?php
}

function custom_search_plugin_field_results_per_page_cb($args)
{
    $options = get_option('custom_search_plugin_options');
?>
    <input type="number" id="<?= esc_attr($args['label_for']); ?>" name="custom_search_plugin_options[<?= esc_attr($args['label_for']); ?>]" value="<?= esc_attr($options[$args['label_for']] ?? '10'); ?>" min="1">
<?php
}

function custom_search_plugin_field_display_mode_cb($args)
{
    $options = get_option('custom_search_plugin_options');
?>
    <select id="<?= esc_attr($args['label_for']); ?>" name="custom_search_plugin_options[<?= esc_attr($args['label_for']); ?>]">
        <option value="list" <?= isset($options[$args['label_for']]) && $options[$args['label_for']] === 'list' ? 'selected' : ''; ?>>List</option>
        <option value="grid" <?= isset($options[$args['label_for']]) && $options[$args['label_for']] === 'grid' ? 'selected' : ''; ?>>Grid</option>
    </select>
<?php
}
?>