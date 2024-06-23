<?php

function custom_search_shortcode($atts)
{

    $options = get_option('custom_search_plugin_options');
    $default_post_types = isset($options['custom_search_plugin_field_post_types']) ? $options['custom_search_plugin_field_post_types'] : 'post';
    $default_element_count = isset($options['custom_search_results_per_page']) ? intval($options['custom_search_results_per_page']) : 4;
    $default_display_mode = isset($options['custom_search_display_mode']) ? $options['custom_search_display_mode'] : 'list';

    $atts = shortcode_atts([
        'post-types' => $default_post_types,
        'element-count' => $default_element_count,
        'layout' => $default_display_mode,
    ], $atts, 'custom_search');

    $post_types = is_string($atts['post-types']) ? explode(', ', $atts['post-types']) : (array) $atts['post-types'];
    $element_count = intval($atts['element-count']);
    $display_mode = $atts['layout'];
    $display_options_form = '';
    $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

    if (!empty($_GET['display_mode'])) {
        $display_mode = $_GET['display_mode'];
    }

    $search_form = '
        <form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="get" class="custom-search-form">
            <input type="text" name="custom_search_query" placeholder="Search...">
            <input type="hidden" name="display_mode" value="' . esc_attr($display_mode) . '">
            <input type="submit" value="">
        </form>
    ';

    if (!empty($_GET['custom_search_query'])) {
        $display_options_form = '
            <form id="search-display-options" method="get">
                <input type="hidden" name="custom_search_query" value="' . esc_attr($_GET['custom_search_query']) . '">
                <label><input type="radio" name="display_mode" value="list"' . ($display_mode == 'list' ? ' checked' : '') . '> List</label>
                <label><input type="radio" name="display_mode" value="grid"' . ($display_mode == 'grid' ? ' checked' : '') . '> Grid</label>
                <input type="submit" value="Apply">
            </form>
        ';
    }

    $search_results = $display_options_form;
    if (!empty($_GET['custom_search_query'])) {
        $search_query = sanitize_text_field($_GET['custom_search_query']);

        $query_args = [
            'post_type' => $post_types,
            'posts_per_page' => $element_count,
            'paged' => $paged,
            's' => $search_query,
        ];

        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
            $search_results .= '<div class="custom-search-results ' . esc_attr($display_mode) . '">';

            while ($query->have_posts()) {
                $query->the_post();
                $post_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
                $post_date = get_the_date();
                $categories = get_the_category();
                $category_list = array();

                foreach ($categories as $category) {
                    $category_list[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                }

                $categories_string = join(', ', $category_list);
                $post_excerpt = wp_trim_words(get_the_excerpt(), 25, '...');
                $read_more_link = '<a class="readmore" href="' . get_permalink() . '">Read more...</a>';

                if ($display_mode == 'grid') {
                    $search_results .= "
                      <div class='custom-search-result'>
                        <a href='" . get_permalink() . "'><img src='$post_image' alt='" . get_the_title() . "'></a>
                        <h2><a href='" . get_permalink() . "'>" . get_the_title() . "</a></h2>
                        <div class='wcsp_flexed'>
                          <p class='wcsp_date'>$post_date</p>
                          <p class='wcsp_category'>$categories_string</p>
                        </div>  
                        <p class='wcsp_excerpt'>$post_excerpt</p>
                        $read_more_link
                    </div>";
                } else {
                    $search_results .= "
                    <div class='custom-search-result list'>
                        <div class='search-result-image'>
                            <a href='" . get_permalink() . "'><img src='$post_image' alt='" . get_the_title() . "'></a>
                        </div>
                        <div class='search-result-content'>
                            <h2><a href='" . get_permalink() . "'>" . get_the_title() . "</a></h2>
                            <div class='wcsp_flexed'>
                              <p class='wcsp_date'>$post_date</p>
                              <p class='wcsp_category'>$categories_string</p>
                            </div>
                            <p class='wcsp_excerpt'>$post_excerpt</p>
                            $read_more_link
                        </div>
                    </div>";
                }
            }

            $search_results .= '</div>';

            $total_pages = $query->max_num_pages;

            $base_url = home_url('/') . '?custom_search_query=' . urlencode($search_query) . '&display_mode=' . urlencode($display_mode);


            $search_results .= '<nav class="pagination">';

            if ($paged > 1) {
                $prev_page = $paged - 1;
                $search_results .= '<a href="' . esc_url($base_url . '&paged=' . $prev_page) . '"><< Back</a> ';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                $page_link = $base_url . '&paged=' . $i;
                $search_results .= ($i === $paged) ? '<span>' . $i . '</span> ' : '<a href="' . esc_url($page_link) . '">' . $i . '</a> ';
            }

            if ($paged < $total_pages) {
                $next_page = $paged + 1;
                $search_results .= '<a href="' . esc_url($base_url . '&paged=' . $next_page) . '">Next >></a>';
            }

            $search_results .= '</nav>';

            wp_reset_postdata();
        } else {
            $search_results .= '<p>No posts found.</p>';
        }
    }

    $output = $search_form . $search_results;
    return $output;
}

add_shortcode('custom_search', 'custom_search_shortcode');
