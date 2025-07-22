
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
