
jQuery(document).ready(function ($) {


    function resetForm(formId, submitBtnId, submitBtnText = 'Submit') {
        $(formId)[0].reset();
        $(formId + ' #user_id').val('');
        $(submitBtnId).text(submitBtnText);
        $(formId + ' #cancelEditBtn').hide();
    }


    function loadStudentData() {
        $.post(ajax_object.ajaxurl, { action: 'show_user_data', nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                $('#studentTable').html(response.data);
            } else {
                $('#studentTable').html('<tr><td colspan="8">No data found.</td></tr>');
            }
        }).fail(function () {
            alert('Error loading student data.');
        });
    }


    function loadSchoolData() {
        $.post(ajax_object.ajaxurl, { action: 'show_school_data', nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                $('#schoolTable tbody').html(response.data);
            } else {
                $('#schoolTable tbody').html('<tr><td colspan="4">No data found.</td></tr>');
            }
        }).fail(function () {
            alert('Error loading school data.');
        });
    }


    $(document).on('click', '.editBtn', function () {
        var row = $(this).closest('tr');

        $('#user_id').val(row.find('.col-id').text().trim());
        $('#fname').val(row.find('.col-fname').text().trim());
        $('#lname').val(row.find('.col-lname').text().trim());

        var gender = row.find('.col-gender').text().trim().toLowerCase();
        $("input[name='gender'][value='" + gender + "']").prop("checked", true);

        $('#age').val(row.find('.col-age').text().trim());
        $('#address').val(row.find('.col-address').text().trim());
        $('#email').val(row.find('.col-email').text().trim());

        $('#submitBtn').text('Update Student');
    });


    $(document).on('click', '.editSchoolBtn', function () {
        var row = $(this).closest('tr');

        $('#schoolid').val(row.find('.col-id').text().trim());
        $('#schoolname').val(row.find('.col-schoolname').text().trim());
        $('#address').val(row.find('.col-address').text().trim());

        $('#saveSchoolBtn').text('Update');
        $('#cancelEditBtn').show();
    });


    $('#cancelEditBtn').click(function () {
        resetForm('#schoolForm', '#saveSchoolBtn', 'Save');
    });


    $('#mySchool').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serializeArray();
        var userId = $('#user_id').val();
        formData.push({ name: 'action', value: userId ? 'update_user_data' : 'insert_user_data' });
        formData.push({ name: 'nonce', value: ajax_object.nonce });

        $.post(ajax_object.ajaxurl, formData, function (response) {
            if (response.success) {
                alert(response.data);
                loadStudentData();
                resetForm('#mySchool', '#submitBtn', 'Add Student');
            } else {
                alert(response.data || 'An error occurred.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    });


    $('#schoolForm').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serializeArray();
        var schoolId = $('#schoolid').val();
        var action = schoolId ? 'update_school_data' : 'insert_school_data';

        formData.push({ name: 'action', value: action });
        formData.push({ name: 'nonce', value: ajax_object.nonce });

        $.post(ajax_object.ajaxurl, formData, function (response) {
            if (response.success) {
                alert(response.data);
                resetForm('#schoolForm', '#saveSchoolBtn', 'Save');
                loadSchoolData();
            } else {
                alert(response.data || 'An error occurred.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    });

    $(document).on('click', '.deleteSchoolBtn', function () {
        if (!confirm('Are you sure you want to delete this user?')) return;

        var id = $(this).data('id');

        $.post(ajax_object.ajaxurl, { action: 'delete_school_data', schoolid: id, nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                alert(response.data);
                loadSchoolData();
            } else {
                alert(response.data || 'Failed to delete user.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    });


    $(document).on('click', '.deleteBtn', function () {
        if (!confirm('Are you sure you want to delete this user?')) return;

        var id = $(this).data('id');

        $.post(ajax_object.ajaxurl, { action: 'delete_user_data', id: id, nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                alert(response.data);
                loadStudentData();
                resetForm('#mySchool', '#submitBtn', 'Add Student');
            } else {
                alert(response.data || 'Failed to delete user.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    });


    loadStudentData();
    loadSchoolData();

});















<?php
/*
Template Name:  Moviess
*/

get_header();
?>

<?php

$categories = get_terms(array(
    'taxonomy' => 'movie_category',
    'orderby'  => 'name',
    'order'    => 'ASC',
    'hide_empty' => false,
));

$selected_category = isset($_GET['movie_category']) ? sanitize_text_field($_GET['movie_category']) : '';

?>

<div class="heading">
    <div class="title">
        <h1>All Movies</h1>
    </div>
    <div class="category-filter">
        <form action="" method="GET">
            <label for="movie-category">Select By Category</label>
            <select name="movie_category" id="movie-category" onchange="this.form.submit()">
                <option value="">Select Category</option>
                <?php
                foreach ($categories as $category) {
                    $selected = ($category->term_id == $selected_category) ? 'selected' : '';
                    echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
        </form>
    </div>
</div>

<?php

$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$args = array(
    'post_type'      => 'movie',
    'posts_per_page' => -1,
    'paged'          => $paged,
);

if ($selected_category) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'movie_category',
            'field'    => 'term_id',
            'terms'    => $selected_category,
            'operator' => 'IN',
        ),
    );
}

$query = new WP_Query($args);

if ($query->have_posts()) :
    echo '<div class="all-movies">';
    while ($query->have_posts()) : $query->the_post();

        $desc = get_post_meta(get_the_ID(), '_movie_description', true);
        $casting = get_post_meta(get_the_ID(), '_movie_cast', true);
        $director = get_post_meta(get_the_ID(), '_movie_director', true);
        $date = get_post_meta(get_the_ID(), '_movie_date', true);
        $rating = get_post_meta(get_the_ID(), '_movie_rating', true);

        $categories = get_the_terms(get_the_ID(), 'movie_category');
        $category_list = '';
        if ($categories && !is_wp_error($categories)) {
            $category_names = wp_list_pluck($categories, 'name');
            $category_list = implode(', ', $category_names);
        }
        ?>
        <a href="<?php echo esc_url(get_permalink()); ?>">
            <div class="movie-item">
                <?php if (has_post_thumbnail()) {
                    echo get_the_post_thumbnail(get_the_ID(), 'medium_large', array('class' => 'movie'));
                } ?>

                <h2><?php the_title(); ?></h2>
                <p><?php the_excerpt(); ?></p>

                <?php if ($desc): ?>
                    <p><strong>Description: </strong> <?php echo esc_html($desc); ?></p>
                <?php endif; ?>

                <?php if ($casting): ?>
                    <p><strong>Cast: </strong> <?php echo esc_html($casting); ?> </p>
                <?php endif; ?>

                <?php if ($director): ?>
                    <p><strong>Director: </strong> <?php echo esc_html($director); ?></p>
                <?php endif; ?>

                <?php if ($date): ?>
                    <p><strong>Release Date: </strong> <?php echo esc_html($date); ?></p>
                <?php endif; ?>

                <?php if ($rating): ?>
                    <p><strong>Rating: </strong> <?php echo esc_html($rating); ?></p>
                <?php endif; ?>

                <?php if ($category_list): ?>
                    <p><strong>Categories: </strong> <?php echo esc_html($category_list); ?></p>
                <?php endif; ?>

            </div>
        </a>
        <?php
    endwhile;
    echo '</div>';
    echo '<div class="pagination">';
    echo paginate_links(array(
        'total'   => $query->max_num_pages,
        'current' => $paged,
        'prev_text' => __('« Prev'),
        'next_text' => __('Next »'),
    ));
    echo '</div>';

    wp_reset_postdata();
else :
    echo '<p>No movies found</p>';
endif;
?>

<?php get_footer(); ?>













function render_movie_custom_fields($post) {
    $desc = get_post_meta($post->ID, '_movie_description', true);
    $casting = get_post_meta($post->ID, '_movie_cast', true);
    $director = get_post_meta($post->ID, '_movie_director', true);
    $date = get_post_meta($post->ID, '_movie_date', true);
    $rating = get_post_meta($post->ID, '_movie_rating', true);
    $images = get_post_meta($post->ID, '_movie_images', true); // Get saved images

    // Convert images to a comma-separated string for easy handling
    if ($images && is_array($images)) {
        $image_urls = implode(',', $images);
    } else {
        $image_urls = '';
    }

    ?>
    <p><label>Description: <textarea name="movie_desc"><?php echo esc_textarea($desc); ?></textarea></label></p>
    <p><label>Casting: <input type="text" name="movie_cast" value="<?php echo esc_attr($casting); ?>" /></label></p>
    <p><label>Director: <input type="text" name="movie_director" value="<?php echo esc_attr($director); ?>" /></label></p>
    <p><label>Release Date: <input type="date" name="movie_date" value="<?php echo esc_attr($date); ?>" /></label></p>
    <p><label>Rating: <input type="text" name="movie_rating" value="<?php echo esc_attr($rating); ?>" /></label></p>

    <p>
        <label>Images: <input type="text" name="movie_images" id="movie_images" value="<?php echo esc_attr($image_urls); ?>" /></label>
        <button type="button" id="upload_images_button" class="button">Upload Images</button>
    </p>
    <div id="movie_images_preview">
        <?php
            if (!empty($image_urls)) {
                $image_ids = explode(',', $image_urls);
                foreach ($image_ids as $image_id) {
                    echo wp_get_attachment_image($image_id, 'thumbnail');
                }
            }
        ?>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            var image_frame;
            $('#upload_images_button').on('click', function(e) {
                e.preventDefault();
                
                // If the media frame already exists, reopen it.
                if (image_frame) {
                    image_frame.open();
                    return;
                }

                // Create a new media frame
                image_frame = wp.media({
                    title: 'Select Images',
                    button: {
                        text: 'Add Images'
                    },
                    multiple: true // Allow multiple images to be selected
                });

                image_frame.on('select', function() {
                    var selection = image_frame.state().get('selection');
                    var selected_images = [];
                    selection.each(function(attachment) {
                        selected_images.push(attachment.id);
                    });

                    // Update the input field with the selected image IDs
                    $('#movie_images').val(selected_images.join(','));

                    // Preview the selected images
                    var preview = $('#movie_images_preview');
                    preview.html('');
                    selected_images.forEach(function(image_id) {
                        preview.append('<img src="' + wp.media.attachment(image_id).get('url') + '" class="thumbnail" />');
                    });
                });

                // Open the media frame
                image_frame.open();
            });
        });
    </script>
    <?php
}



function save_movie_custom_fields($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['movie_desc'])) {
        update_post_meta($post_id, '_movie_description', sanitize_textarea_field($_POST['movie_desc']));
    }
    if (isset($_POST['movie_cast'])) {
        update_post_meta($post_id, '_movie_cast', sanitize_text_field($_POST['movie_cast']));
    }
    if (isset($_POST['movie_director'])) {
        update_post_meta($post_id, '_movie_director', sanitize_text_field($_POST['movie_director']));
    }
    if (isset($_POST['movie_date'])) {
        update_post_meta($post_id, '_movie_date', sanitize_text_field($_POST['movie_date']));
    }
    if (isset($_POST['movie_rating'])) {
        update_post_meta($post_id, '_movie_rating', sanitize_text_field($_POST['movie_rating']));
    }

    // Save multiple images
    if (isset($_POST['movie_images'])) {
        $image_ids = explode(',', sanitize_text_field($_POST['movie_images']));
        update_post_meta($post_id, '_movie_images', $image_ids);
    }
}






<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="movie-container">
    <h1 class="movie-title"><?php the_title(); ?></h1>

    <div class="movie-meta">
        <?php if (has_post_thumbnail()) : ?>
            <div class="movie-featured-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>

        <div class="movie-details">
            <div class="movie-description">
                <?php the_content(); ?>
            </div>

            <div class="movie-custom-fields">
                <?php 
                // Display custom fields
                $movie_description = get_post_meta(get_the_ID(), '_movie_description', true);
                if ($movie_description) {
                    echo '<p><strong>Description: </strong>' . esc_html($movie_description) . '</p>';
                }

                $movie_director = get_post_meta(get_the_ID(), '_movie_director', true);
                if ($movie_director) {
                    echo '<p><strong>Director: </strong>' . esc_html($movie_director) . '</p>';
                }

                $movie_year = get_post_meta(get_the_ID(), '_movie_year', true);
                if ($movie_year) {
                    echo '<p><strong>Release Year: </strong>' . esc_html($movie_year) . '</p>';
                }

                $movie_rating = get_post_meta(get_the_ID(), '_movie_rating', true);
                if ($movie_rating) {
                    echo '<p><strong>Rating: </strong>' . esc_html($movie_rating) . '</p>';
                }

                $movie_date = get_post_meta(get_the_ID(), '_movie_date', true);
                if ($movie_date) {
                    echo '<p><strong>Release Date: </strong>' . esc_html($movie_date) . '</p>';
                }
                ?>
            </div>

            <div class="movie-images">
                <?php
                // Get the movie images from the custom field
                $movie_images = get_post_meta(get_the_ID(), '_movie_images', true);
                if ($movie_images) {
                    $image_ids = explode(',', $movie_images);
                    echo '<div class="movie-images-gallery">';
                    foreach ($image_ids as $image_id) {
                        $image_url = wp_get_attachment_url($image_id);
                        $image_html = wp_get_attachment_image($image_id, 'medium');
                        echo '<div class="movie-image-item">';
                        echo $image_html;
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>

        <div class="movie-categories">
            <?php 
                $categories = get_the_terms(get_the_ID(), 'movie_category');
                if ($categories && !is_wp_error($categories)) :
            ?>
                <strong>Categories:</strong> 
                <?php the_terms(get_the_ID(), 'movie_category'); ?>
            <?php endif; ?>
        </div>

        </div>
    </div>
</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>







$movie_images = get_post_meta(get_the_ID(), '_movie_images', true);
if ($movie_images) {
    $image_ids = explode(',', $movie_images);
    echo '<div class="movie-images-gallery">';
    foreach ($image_ids as $image_id) {
        $image_html = wp_get_attachment_image($image_id, 'medium'); // Display image as thumbnail
        echo '<div class="movie-image-item">';
        echo $image_html;
        echo '</div>';
    }
    echo '</div>';
}
