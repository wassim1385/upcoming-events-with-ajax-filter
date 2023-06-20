<?php get_header(); ?>

    <div class="events_container">
        <h2><?php the_title(); ?></h2>
        <div class="js-filter">
            <?php if( $terms = get_terms( array( 'taxonomy' => 'event_cat' ) ) ) : ?>
                <select id="cat" name="cat">
                    <option value="">Select Category</option>
                    <?php foreach( $terms as $term ) : ?>
                    <option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                    <?php endforeach; ?>
                    </select>
            <?php endif; ?>
        </div>
        <?php
            $today = date("Y-m-d");
            $events = new WP_Query(
                array(
                    'post_type' => 'events',
                    'post_status' => 'publish',
                    'meta_key' => 'wassim_events_sdate',
                    'orderby' => 'meta_value',
                    //'meta_type' => 'DATE',
                    'order' => 'ASC',
                    'meta_query' => array(
                        array(
                            'key' => 'wassim_events_sdate',
                            'compare' => '>=',
                            'value' => $today,
                            'type' => 'DATE'
                        )
                    )
                )
            ); ?>
            <div class="js-events">
                <?php if ( $events->have_posts() ) :
                    while ( $events->have_posts() ) : $events->the_post();
                    $start_date = get_post_meta( get_the_ID(), 'wassim_events_sdate', true );
                    $end_date = get_post_meta( get_the_ID(), 'wassim_events_edate', true );
                ?>
                <article class="event_block">
                    <?php if( has_post_thumbnail() ) {
                        the_post_thumbnail( array( 230, 230 ) );
                    }
                    ?>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php
                        $terms_list = get_the_terms( get_the_ID(), 'event_cat' );
                        echo join( ', ', wp_list_pluck( $terms_list, 'name' ) );
                    ?>
                    <ul class="dates">
                        <li><b>Start date:</b> <?php echo $start_date; ?></li>
                        <li><b>End date:</b> <?php echo $end_date; ?></li>
                    </ul>
                    <?php the_content(); ?>
                </article>
                <?php
                endwhile;
                wp_reset_postdata();
                endif;
                ?>
            </div>
    </div>


<?php get_footer(); ?>