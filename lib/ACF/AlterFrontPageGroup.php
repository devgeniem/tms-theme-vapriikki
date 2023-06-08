<?php

namespace TMS\Theme\Vapriikki\ACF;

use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Layouts\HeroLayout as BaseThemeHeroLayout;
use TMS\Theme\Vapriikki\ACF\Layouts\HeroLayout;
use TMS\Theme\Vapriikki\ACF\Layouts\ExhibitionsLayout;

/**
 * Class AlterPageFrontPageGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class AlterPageFrontPageGroup {

    /**
     * PageGroup constructor.
     */
    public function __construct() {
        add_filter( 'tms/acf/field/fg_front_page_components_components/layouts',
            [ $this, 'replace_base_theme_hero' ],
            10,
            1
        );

        add_filter( 'tms/acf/field/fg_front_page_components_components/layouts',
            [ $this, 'add_exhibitions_layout' ],
            10,
            1
        );
    }

    /**
     * Replace base theme hero with Vapriikki hero.
     *
     * @param array $layouts Front page layouts.
     *
     * @return mixed
     */
    public function add_exhibitions_layout( $layouts ) {
        $layouts[] = ExhibitionsLayout::class;

        return $layouts;
    }

    /**
     * Replace base theme hero with Vapriikki hero.
     *
     * @param array $layouts Front page layouts.
     *
     * @return mixed
     */
    public function replace_base_theme_hero( $layouts ) {
        $layouts = array_filter( $layouts, function ( $layout ) {
            return $layout !== BaseThemeHeroLayout::class;
        } );

        $layouts[] = HeroLayout::class;

        return $layouts;
    }
}

( new AlterPageFrontPageGroup() );
