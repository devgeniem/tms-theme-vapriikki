<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF;

use TMS\Theme\Vapriikki\ACF\Layouts\CustomHtmlLayout;
use TMS\Theme\Vapriikki\ACF\Layouts\ExhibitionsLayout;

/**
 * AlterComponents
 */
class AlterComponents {

    /**
     * Constructor
     */
    public function __construct() {
        \add_filter(
            'tms/acf/field/fg_onepager_components_components/layouts',
            [ $this, 'add_html_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_page_components_components/layouts',
            [ $this, 'add_html_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_front_page_components_components/layouts',
            [ $this, 'add_html_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_post_fields_components/layouts',
            [ $this, 'add_html_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_dynamic_event_fields_components/layouts',
            [ $this, 'add_html_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_onepager_components_components/layouts',
            [ $this, 'add_exhibitions_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_page_components_components/layouts',
            [ $this, 'add_exhibitions_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_front_page_components_components/layouts',
            [ $this, 'add_exhibitions_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_post_fields_components/layouts',
            [ $this, 'add_exhibitions_layout' ],
            10,
            2
        );

        \add_filter(
            'tms/acf/field/fg_dynamic_event_fields_components/layouts',
            [ $this, 'add_exhibitions_layout' ],
            10,
            2
        );
    }

    /**
     * Add HTML layout to components
     *
     * @param array  $layouts Array of ACF Layouts.
     * @param string $key     Field group key.
     *
     * @return array
     */
    public function add_html_layout( array $layouts, string $key ) : array {
        $layouts[] = new CustomHtmlLayout( $key );

        return $layouts;
    }

    /**
     * Add exhibitions layout to components
     *
     * @param array  $layouts Front page layouts.
     * @param string $key     Field group key.
     *
     * @return mixed
     */
    public function add_exhibitions_layout( array $layouts, string $key ) : array {
        $layouts[] = new ExhibitionsLayout( $key );

        return $layouts;
    }
}

( new AlterComponents() );
