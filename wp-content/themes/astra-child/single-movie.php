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
