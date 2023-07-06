<?php
/**
 * Copyright (c) 2023. Geniem Oy
 * Template Name: Lobby display
 */

/**
 * The PageLobbyDisplay class.
 */
class PageLobbyDisplay extends BaseModel {

    /**
     * Get font directory url.
     *
     * @return string
     */
    public function font() {
        return get_stylesheet_directory_uri() . '/assets/fonts/BebasNeue-Regular.ttf';
    }

    /**
     * Get images.
     *
     * @return array
     */
    public function lobby_images() {
        $images = [
            'map_3rd_floor'   => get_stylesheet_directory_uri() . '/assets/images/aula/map_3rd_floor.png',
            'third'           => get_stylesheet_directory_uri() . '/assets/images/aula/3rd.png',
            'map_2nd_floor'   => get_stylesheet_directory_uri() . '/assets/images/aula/map_2nd_floor.png',
            'second'          => get_stylesheet_directory_uri() . '/assets/images/aula/2nd.png',
            'map_groundfloor' => get_stylesheet_directory_uri() . '/assets/images/aula/map_groundfloor.png',
            'first'           => get_stylesheet_directory_uri() . '/assets/images/aula/1st.png',
            'map_basement'    => get_stylesheet_directory_uri() . '/assets/images/aula/map_basement.png',
            'basement'        => get_stylesheet_directory_uri() . '/assets/images/aula/basement.png',
            'hallway'         => get_stylesheet_directory_uri() . '/assets/images/aula/hallway.png',
            'food'            => get_stylesheet_directory_uri() . '/assets/images/aula/food.png',
            'shop'            => get_stylesheet_directory_uri() . '/assets/images/aula/shop.png',
            'loc_white'       => get_stylesheet_directory_uri() . '/assets/images/aula/loc_white.png',
            'press'           => get_stylesheet_directory_uri() . '/assets/images/aula/press.png',
        ];

        return $images;
    }

    /**
     * Get language versions.
     *
     * @return array
     */
    public function language_versions() {
        $language_versions = [
            'en_url'     => get_the_permalink( pll_get_post( get_the_ID(), 'en' ) ),
            'fi_url'     => get_the_permalink( pll_get_post( get_the_ID(), 'fi' ) ),
            'current_fi' => pll_current_language() === 'fi' ? 'active' : '',
            'current_en' => pll_current_language() === 'en' ? 'active' : '',
        ];

        return $language_versions;
    }

    /**
     * Get exhibitions.
     *
     * @return array|array[]|false
     */
    public function lobby_exhibitions() {
        $args = [
            'post_type'      => 'exhibition',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'tax_query'      => [
                [
                    'taxonomy' => 'exhibition-status',
                    'field'    => 'slug',
                    'terms'    => [ 'arkisto', 'tulossa' ],
                    'operator' => 'NOT IN',
                ],
            ],
        ];

        $query = new WP_Query($args);
        
        return array_map( function ( $post ) {
            $exhibition                = (object) get_fields( $post->ID );
            $exhibition->title         = get_the_title( $post->ID );
            $exhibition->image         = has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'medium_large' ) : null;
            $exhibition->upcoming      = has_term( 'tulossa', 'exhibition-status', $post );
            $exhibition->upcoming_text = __( 'Upcoming', 'tms-theme-vapriikki' );
            $term_obj_list             = get_the_terms( $post->ID, 'exhibition-status' );
            $terms_string              = join( '', wp_list_pluck( $term_obj_list, 'slug' ) );
            $exhibition->terms_string  = str_replace( ["tulossa", "arkisto", "vaihtuvat", "pysyvat"], "", $terms_string );

            return $exhibition;

        }, $query->posts );
    }
}
