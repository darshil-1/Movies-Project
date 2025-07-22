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

                    <div class="movie-images">
                        <?php
                        $movie_images = get_post_meta(get_the_ID(), '_movie_images', true);

                        if ($movie_images) {

                            if (is_array($movie_images)) {
                                $image_ids = $movie_images;
                            } else {

                                $image_ids = explode(',', $movie_images);
                            }

                            echo '<div class="movie-images-gallery">';
                            foreach ($image_ids as $image_id) {

                                if (!empty($image_id)) {
                                    $image_html = wp_get_attachment_image($image_id, 'medium');
                                    echo '<div class="movie-image-item">';
                                    echo $image_html;
                                    echo '</div>';
                                }
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>

                    <div class="movie-custom-fields">
                        <?php

                        $movie_author = get_post_meta(get_the_ID(), '_movie_author', true);
                        if ($movie_author) {
                            echo '<p><strong>Author: </strong>' . esc_html($movie_author) . '</p>';
                        }

                        $movie_cast = get_post_meta(get_the_ID(), '_movie_cast', true);
                        if ($movie_cast) {
                            echo '<p><strong>Cast: </strong>' . esc_html($movie_cast) . '</p>';
                        }

                        $movie_director = get_post_meta(get_the_ID(), '_movie_director', true);
                        if ($movie_director) {
                            echo '<p><strong>Director: </strong>' . esc_html($movie_director) . '</p>';
                        }

                        $movie_date = get_post_meta(get_the_ID(), '_movie_date', true);
                        if ($movie_date) {
                            echo '<p><strong>Release Date: </strong>' . esc_html($movie_date) . '</p>';
                        }

                        $movie_rating = get_post_meta(get_the_ID(), '_movie_rating', true);
                        if ($movie_rating) {
                            echo '<p><strong>Rating: </strong>' . esc_html($movie_rating) . '</p>';
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

<?php endwhile;
endif; ?>

<?php get_footer(); ?>