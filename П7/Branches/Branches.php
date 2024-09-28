<?php
/*
Plugin Name: Branches Plugin
Description: Плагин для добавления типа записи "Филиалы" с таксономией "Города" и мета полями.
Version: 1.0
Author: Ваше Имя
*/

// Регистрируем новый тип записи "Филиалы"
function register_branches_post_type() {
    $labels = array(
        'name'               => 'Филиалы',
        'singular_name'      => 'Филиал',
        'menu_name'          => 'Филиалы',
        'name_admin_bar'     => 'Филиал',
        'add_new'            => 'Добавить новый',
        'add_new_item'       => 'Добавить новый филиал',
        'new_item'           => 'Новый филиал',
        'edit_item'          => 'Редактировать филиал',
        'view_item'          => 'Просмотреть филиал',
        'all_items'          => 'Все филиалы',
        'search_items'       => 'Искать филиалы',
        'not_found'          => 'Филиалы не найдены.',
        'not_found_in_trash' => 'Филиалы в корзине не найдены.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'branch'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('branch', $args);
}
add_action('init', 'register_branches_post_type');

// Регистрируем таксономию "Города"
function register_city_taxonomy() {
    $labels = array(
        'name'              => 'Города',
        'singular_name'     => 'Город',
        'search_items'      => 'Искать города',
        'all_items'         => 'Все города',
        'parent_item'       => 'Родительский город',
        'parent_item_colon' => 'Родительский город:',
        'edit_item'         => 'Редактировать город',
        'update_item'       => 'Обновить город',
        'add_new_item'      => 'Добавить новый город',
        'new_item_name'     => 'Новое имя города',
        'menu_name'         => 'Города',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'city'),
    );

    register_taxonomy('city', array('branch'), $args);
}
add_action('init', 'register_city_taxonomy');

// Добавляем мета поля для филиалов
function add_branch_meta_boxes() {
    add_meta_box(
        'branch_meta_box',
        'Информация о филиале',
        'display_branch_meta_box',
        'branch',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_branch_meta_boxes');

function display_branch_meta_box($post) {
    $address = get_post_meta($post->ID, '_branch_address', true);
    $phone = get_post_meta($post->ID, '_branch_phone', true);
    $email = get_post_meta($post->ID, '_branch_email', true);
    ?>
    <label for="branch_address">Адрес:</label>
    <input type="text" name="branch_address" value="<?php echo esc_attr($address); ?>" size="25" />
    <br>
    <label for="branch_phone">Телефон:</label>
    <input type="text" name="branch_phone" value="<?php echo esc_attr($phone); ?>" size="25" />
    <br>
    <label for="branch_email">Email:</label>
    <input type="email" name="branch_email" value="<?php echo esc_attr($email); ?>" size="25" />
    <?php
}

function save_branch_meta($post_id) {
    // Проверка на автосохранение
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Проверка прав пользователя
    if (isset($_POST['post_type']) && 'branch' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Сохранение данных
    if (isset($_POST['branch_address'])) {
        update_post_meta($post_id, '_branch_address', sanitize_text_field($_POST['branch_address']));
    }
    if (isset($_POST['branch_phone'])) {
        update_post_meta($post_id, '_branch_phone', sanitize_text_field($_POST['branch_phone']));
    }
    if (isset($_POST['branch_email'])) {
        update_post_meta($post_id, '_branch_email', sanitize_email($_POST['branch_email']));
    }
}
add_action('save_post', 'save_branch_meta');

// Шорткод для вывода всех городов и филиалов
function cities_and_branches_shortcode() {
    $cities = get_terms(array(
        'taxonomy' => 'city',
        'hide_empty' => false,
    ));

    $output = '<div class="cities-list">';
    foreach ($cities as $city) {
        $output .= '<h2>' . esc_html($city->name) . '</h2>';

        $branches = new WP_Query(array(
            'post_type' => 'branch',
            'tax_query' => array(
                array(
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city->term_id,
                ),
            ),
        ));

        if ($branches->have_posts()) {
            $output .= '<ul>';
            while ($branches->have_posts()) {
                $branches->the_post();
                $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<p>Филиалы не найдены.</p>';
        }

        wp_reset_postdata();
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('cities_and_branches', 'cities_and_branches_shortcode');
// [cities_and_branches]

// Шорткод для вывода мета полей на странице детального просмотра филиала
function branch_details_shortcode() {
    global $post;
    if (is_singular('branch')) {
        $address = get_post_meta($post->ID, '_branch_address', true);
        $phone = get_post_meta($post->ID, '_branch_phone', true);
        $email = get_post_meta($post->ID, '_branch_email', true);

        $output = '<div class="branch-details">';
        $output .= '<p><strong>Адрес:</strong> ' . esc_html($address) . '</p>';
        $output .= '<p><strong>Телефон:</strong> ' . esc_html($phone) . '</p>';
        $output .= '<p><strong>Email:</strong> ' . esc_html($email) . '</p>';
        $output .= '</div>';

        return $output;
    }

    return '';
}
add_shortcode('branch_details', 'branch_details_shortcode');
// [branch_details]
?>
