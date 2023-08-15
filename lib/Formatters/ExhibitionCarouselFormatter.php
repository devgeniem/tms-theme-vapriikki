<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\Formatters;

use TMS\Theme\Vapriikki\PostType\Exhibition;
use TMS\Theme\Vapriikki\Taxonomy\ExhibitionCategory;
use \TMS\Theme\Base\Interfaces\Formatter;

/**
 * Class ExhibitionCarouselFormatter
 *
 * @package TMS\Theme\Vapriikki\Formatters
 */
class ExhibitionCarouselFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Exhibition Carousel';

    /**
     * Hooks
     */
    public function hooks() : void {
        \add_filter(
            'tms/acf/layout/exhibition_carousel/data',
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
        if ( ! empty( $data['rows'] ) ) {
            foreach ( $data['rows'] as $repeater_row ) {
                if ( empty( $repeater_row['exhibition'] ) ) {
                    continue;
                }

                $post_item = $repeater_row['exhibition'];

                if ( has_post_thumbnail( $post_item->ID ) ) {
                    $post_item->image_id  = get_post_thumbnail_id( $post_item->ID, 'medium_large' );
                    $post_item->image_alt = get_post_meta( $post_item->image_id, '_wp_attachment_image_alt', true );
                }

                $post_item->post_url = get_permalink($post_item->ID);
            }
        }

        return $data;
    }
}
