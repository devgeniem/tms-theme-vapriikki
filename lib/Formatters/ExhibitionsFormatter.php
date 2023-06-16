<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\Formatters;

use TMS\Theme\Vapriikki\PostType\Exhibition;
use TMS\Theme\Vapriikki\Taxonomy\ExhibitionCategory;
use \TMS\Theme\Base\Interfaces\Formatter;

/**
 * Class ExhibitionsFormatter
 *
 * @package TMS\Theme\Vapriikki\Formatters
 */
class ExhibitionsFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Exhibitions';

    /**
     * Hooks
     */
    public function hooks() : void {
        \add_filter(
            'tms/acf/layout/exhibitions/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        $args = [
            'post_type'              => Exhibition::SLUG,
            'posts_per_page'         => $data['number'] ?? 12,
            'update_post_meta_cache' => false,
            'no_found_rows'          => true,
        ];

        $is_manual_feed = 'manual' === $data['feed_type'];
        $manual_posts   = [];

        if ( $is_manual_feed && ! empty( $data['exhibition_repeater'] ) ) {

            foreach ( $data['exhibition_repeater'] as $repeater_row ) {
                if ( empty( $repeater_row['exhibition_item']['exhibition'] ) ) {
                    continue;
                }

                $manual_posts[ $repeater_row['exhibition_item']['exhibition'] ] = $repeater_row;
            }

            if ( empty( $manual_posts ) ) {
                return $data;
            }

            $args['post__in'] = array_keys( $manual_posts );
            $args['orderby']  = 'post__in';
        }

        if ( ! $is_manual_feed && ! empty( $data['category'] ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => ExhibitionCategory::SLUG,
                    'terms'    => $data['category'],
                ],
            ];
        }

        $wp_query = new \WP_Query( $args );

        if ( $wp_query->have_posts() ) {
            foreach ( $wp_query->posts as $post_item ) {

                $start_date = strtotime( trim( \get_field( 'start_date', $post_item->ID ) ?? '' ) );
                $dates      = '';

                if ( ! empty( $start_date ) ) {
                    $dates    = date( 'd.m.Y', $start_date );
                    $end_date = strtotime( trim( \get_field( 'end_date', $post_item->ID ) ?? '' ) );

                    if ( ! empty( $end_date ) ) {
                        $dates .= ' - ' . date( 'd.m.Y', $end_date );
                    }
                }

                $post_item->dates          = $dates;
                $post_item->is_upcoming    = \get_field( 'is_upcoming', $post_item->ID );
                $post_item->upcoming_badge = __( 'Upcoming', 'tms-theme-vapriikki' );
                $data['posts'][]  = Exhibition::enrich_post( $post_item, true, true );
            }
        }

        return $data;
    }
}
