<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

function child_enqueue_styles() {
    wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

function enqueue_ajax_script_with_var() {
    wp_enqueue_script('ajax-script', get_stylesheet_directory_uri() . '/school.js', array('jquery'), null, true);

    wp_localize_script('ajax-script', 'ajax_object', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('school_nonce')  
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script_with_var');

// function enqueue_school_scripts() {
//     wp_enqueue_script('school-script', get_stylesheet_directory_uri() . '/js/school.js', array('jquery'), null, true);

//     wp_localize_script('school-script', 'ajax_object', array(
//         'ajaxurl' => admin_url('admin-ajax.php'),
//         'nonce'   => wp_create_nonce('school_nonce')
//     ));
// }
// add_action('wp_enqueue_scripts', 'enqueue_school_scripts');



function add_data() {
    check_ajax_referer('school_nonce', 'nonce');  

    global $wpdb;
    $table = 'student_tbl';

    $firstname = sanitize_text_field($_POST['fname']);
    $lastname  = sanitize_text_field($_POST['lname']);
    $gender    = sanitize_text_field($_POST['gender']);
    $age       = intval($_POST['age']);
    $address   = sanitize_textarea_field($_POST['address']);
    $email     = sanitize_email($_POST['email']);
    $schoolid  = isset($_POST['schoolid']) ? intval($_POST['schoolid']) : null;

    if (empty($firstname) || empty($lastname) || empty($gender) || empty($age) || empty($email)) {
        wp_send_json_error("Please fill all required fields.");
    }

    if (!$schoolid) {
        wp_send_json_error("Please select a valid school.");
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'gender'    => $gender,
            'age'       => $age,
            'address'   => $address,
            'email'     => $email,
            'schoolid'  => $schoolid,
        ],
        ['%s', '%s', '%s', '%d', '%s', '%s','%d']
    );

    if ($inserted) {
        wp_send_json_success("Student data inserted successfully.");
    } else {
        wp_send_json_error("Failed to insert data.");
    }

    wp_die();
}
add_action('wp_ajax_insert_user_data', 'add_data');
add_action('wp_ajax_nopriv_insert_user_data', 'add_data');  


function show_data(){
    check_ajax_referer('school_nonce', 'nonce');

    global $wpdb;
    $table = 'student_tbl';

    $results = $wpdb->get_results("SELECT * FROM $table");

    if ($results) {
        $html = '';
        foreach ($results as $row) {
            $html .= '<tr>';
            $html .= '<td class="col-id">' . esc_html($row->id) . '</td>';
            $html .= '<td class="col-fname" >' . esc_html($row->firstname) . '</td>';
            $html .= '<td class="col-lname">' . esc_html($row->lastname) . '</td>';
            $html .= '<td class="col-gender" >' . esc_html($row->gender) . '</td>';
            $html .= '<td class="col-age">' . esc_html($row->age) . '</td>';
            $html .= '<td class="col-email" >' . esc_html($row->email) . '</td>';
            $html .= '<td>
             <a href="' . esc_url(home_url('/school')) . '?id=' . esc_attr($row->id) . '&fname=' . urlencode($row->firstname) . '&lname=' . urlencode($row->lastname) . '&gender=' . urlencode($row->gender) . '&age=' . urlencode($row->age) . '&address=' . urlencode($row->address) . '&email=' . urlencode($row->email) . '&schoolid=' . esc_attr($row->schoolid) . '" class="btn btn-primary btn-sm editBtn">Edit</a>
             <button class="btn btn-danger btn-sm deleteBtn text-white" data-id="' . esc_attr($row->id) . '">Delete</button>
                    </td>';
            $html .= '</tr>';
        }
        wp_send_json_success($html);
    } else {
        wp_send_json_error('No data found.');
    }

    wp_die();
}
add_action('wp_ajax_show_user_data', 'show_data');
add_action('wp_ajax_nopriv_show_user_data', 'show_data');


function delete_user(){
    check_ajax_referer('school_nonce', 'nonce');

    global $wpdb;
    $table = 'student_tbl';
    $id = intval($_POST['id']);

    $deleted = $wpdb->delete($table, ['id' => $id], ['%d']);

    if ($deleted) {
        wp_send_json_success("Deleted successfully.");
    } else {
        wp_send_json_error("Delete failed.");
    }

    wp_die();
}
add_action('wp_ajax_delete_user_data','delete_user');
add_action('wp_ajax_nopriv_delete_user_data','delete_user');

add_action('wp_ajax_update_user_data', 'update_user');
add_action('wp_ajax_nopriv_update_user_data', 'update_user');

function update_user() {
    check_ajax_referer('school_nonce', 'nonce'); 

    global $wpdb;
    $table = 'student_tbl';

    $id = intval($_POST['id']);
    $firstname = sanitize_text_field($_POST['fname']);
    $lastname  = sanitize_text_field($_POST['lname']);
    $gender    = sanitize_text_field($_POST['gender']);
    $age       = sanitize_text_field($_POST['age']);
    $address   = sanitize_textarea_field($_POST['address']);
    $email     = sanitize_email($_POST['email']);

    if (!$id) {
        wp_send_json_error('Invalid student ID.');
        wp_die();
    }

    $updated = $wpdb->update(
        $table,
        [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'gender'    => $gender,
            'age'       => $age,
            'address'   => $address,
            'email'     => $email,
        ],
        ['id' => $id],
        ['%s', '%s', '%s', '%s', '%s', '%s'],
        ['%d']
    );

    if ($updated !== false) {
        wp_send_json_success('Student updated successfully.');
    } else {
        wp_send_json_error('Failed to update student.');
    }

    wp_die();
}


function add_school_data() {
    global $wpdb;
    $table = 'school_tbl';

    $schoolname = sanitize_text_field($_POST['schoolname']);
    $address    = sanitize_text_field($_POST['address']);

    $data = $wpdb->insert(
        $table,
        [
            'schoolname' => $schoolname,
            'address'    => $address,
        ],
        ['%s', '%s']
    );

    if ($data) {
        wp_send_json_success("School data inserted successfully.");
    } else {
        wp_send_json_error("Failed to insert data.");
    }

    wp_die();
}
add_action('wp_ajax_insert_school_data', 'add_school_data');

function update_school_data() {
    global $wpdb;
    $schoolid = intval($_POST['schoolid']);
    $schoolname = sanitize_text_field($_POST['schoolname']);
    $address = sanitize_text_field($_POST['address']);

    $table = 'school_tbl';

    $result = $wpdb->update(
        $table,
        [
            'schoolname' => $schoolname,
            'address'    => $address,
        ],
        ['schoolid' => $schoolid],
        ['%s', '%s'],
        ['%d']
    );

    if ($result !== false) {
        wp_send_json_success("School updated successfully.");
    } else {
        wp_send_json_error("Update failed.");
    }

    wp_die();
}
add_action('wp_ajax_update_school_data', 'update_school_data');

function delete_school_data() {
    global $wpdb;
    $table = 'school_tbl';
    $id = intval($_POST['schoolid']);

    $deleted = $wpdb->delete($table, ['schoolid' => $id], ['%d']);

    if ($deleted) {
        wp_send_json_success("Deleted successfully.");
    } else {
        wp_send_json_error("Delete failed.");
    }

    wp_die();
}
add_action('wp_ajax_delete_school_data', 'delete_school_data');

function show_school_data() {
    global $wpdb;
    $table = 'school_tbl';
    $results = $wpdb->get_results("SELECT * FROM $table");

    if ($results) {
        $html = '';
        foreach ($results as $row) {
            $edit_url = esc_url(home_url('/add-school') . '?schoolid=' . esc_attr($row->schoolid) .
                '&schoolname=' . urlencode($row->schoolname) .
                '&address=' . urlencode($row->address)
            );

            $html .= '<tr>';
            $html .= '<td class="col-id">' . esc_html($row->schoolid) . '</td>';
            $html .= '<td class="col-schoolname">' . esc_html($row->schoolname) . '</td>';
            $html .= '<td class="col-address">' . esc_html($row->address) . '</td>';
            $html .= '<td>';
            $html .= '<a href="' . $edit_url . '" class="btn btn-primary btn-sm editBtn">Edit</a> ';
            $html .= '<button class="btn btn-danger btn-sm deleteSchoolBtn small text-white" data-id="' . esc_attr($row->schoolid) . '">Delete</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        wp_send_json_success($html);
    } else {
        wp_send_json_error('No data found.');
    }

    wp_die();
}
add_action('wp_ajax_show_school_data', 'show_school_data');



add_action('wp_ajax_get_school_address', 'get_school_address_callback');
add_action('wp_ajax_nopriv_get_school_address', 'get_school_address_callback');

function get_school_address_callback() {
    global $wpdb;
    $schoolid = isset($_POST['schoolid']) ? intval($_POST['schoolid']) : 0;
    if(!$schoolid) {
        wp_send_json_error('No school ID provided');
    }

    $school_table = $wpdb->prefix . 'school_tbl';
    $address = $wpdb->get_var($wpdb->prepare("SELECT address FROM $school_table WHERE schoolid = %d", $schoolid));

    if($address) {
        wp_send_json_success(['address' => $address]);
    } else {
        wp_send_json_error('No address found');
    }
}






function enqueue_movie_styles() {
    if (!is_admin()) {
        wp_enqueue_style('movie-custom-style', get_stylesheet_directory_uri() . '/movie-style.css', array());
    }
}
add_action('wp_enqueue_scripts', 'enqueue_movie_styles');



function register_movies_post_type() {
    $labels = array(
        'name' => 'Movies',
        'singular_name' => 'Movie',
        'menu_name' => 'Movies',
        'name_admin_bar' => 'Movie',
        'add_new' => 'Add New',
        'add_new_item' => 'Add Movie',
        'new_item' => 'New Movie',
        'edit_item' => 'Edit Movie',
        'view_item' => 'View Movie',
        'all_items' => 'All Movies',
        'search_items' => 'Search Movies',
        'not_found' => 'No movies found',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-video-alt2',
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'movies'),
        'show_in_rest' => true, 
    );
    register_post_type('movie', $args);
}

add_action('init', 'register_movies_post_type');

function register_movie_categories_taxonomy() {
    $labels = array(
        'name'              => 'Movie Categories',
        'singular_name'     => 'Movie Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Categories',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'rewrite'           => array('slug' => 'movie-category'),
        'show_in_rest'      => true,
    );
  
    register_taxonomy('movie_category', array('movie'), $args);
}


add_action('init', 'register_movie_categories_taxonomy');

function add_movie_custom_fields() {
    add_meta_box(
                'movie_details_box', 
                'Movie Details', 
                'render_movie_custom_fields', 
                'movie',  
                'normal', 
                'default',  
            );
        }

add_action('add_meta_boxes', 'add_movie_custom_fields');


function render_movie_custom_fields($post) {
    $desc = get_post_meta($post->ID, '_movie_description', true);
    $casting = get_post_meta($post->ID, '_movie_cast', true);
    $director = get_post_meta($post->ID, '_movie_director', true);
    $date = get_post_meta($post->ID, '_movie_date', true);
    $rating = get_post_meta($post->ID, '_movie_rating', true);

    ?>
    <p><label>Description: <textarea name="movie_desc"><?php echo esc_textarea($desc); ?></textarea></label></p>
    <p><label>Casting: <input type="text" name="movie_cast" value="<?php echo esc_attr($casting); ?>" /></label></p>
    <p><label>Director: <input type="text" name="movie_director" value="<?php echo esc_attr($director); ?>" /></label></p>
    <p><label>Release Date: <input type="date" name="movie_date" value="<?php echo esc_attr($date); ?>" /></label></p>
    <p><label>Rating: <input type="text" name="movie_rating" value="<?php echo esc_attr($rating); ?>" /></label></p>
    <?php
}

function save_movie_custom_fields($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

     if (isset($_POST['movie_desc']))
        update_post_meta($post_id, '_movie_description', sanitize_textarea_field($_POST['movie_desc']));
    if (isset($_POST['movie_cast']))
        update_post_meta($post_id, '_movie_cast', sanitize_text_field($_POST['movie_cast']));
    if (isset($_POST['movie_director']))
        update_post_meta($post_id, '_movie_director', sanitize_text_field($_POST['movie_director']));
    if (isset($_POST['movie_date']))
        update_post_meta($post_id, '_movie_date', sanitize_text_field($_POST['movie_date']));
    if (isset($_POST['movie_rating']))
        update_post_meta($post_id, '_movie_rating', sanitize_text_field($_POST['movie_rating']));
}

add_action('save_post', 'save_movie_custom_fields');