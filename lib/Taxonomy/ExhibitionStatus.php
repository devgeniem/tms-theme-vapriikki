<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\Taxonomy;

use TMS\Theme\Base\Interfaces\Taxonomy;
use TMS\Theme\Vapriikki\PostType\Exhibition;
use TMS\Theme\Base\Traits\Categories;

/**
 * This class defines the taxonomy.
 *
 * @package TMS\Theme\Vapriikki\Taxonomy
 */
class ExhibitionStatus implements Taxonomy {

    use Categories;

    /**
     * This defines the slug of this taxonomy.
     */
    const SLUG = 'exhibition-status';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        \add_action( 'init', \Closure::fromCallable( [ $this, 'register' ] ), 15 );
    }

    /**
     * This registers the post type.
     *
     * @return void
     */
    private function register() {
        $labels = [
            'name'                       => 'Statukset',
            'singular_name'              => 'Status',
            'menu_name'                  => 'Statukset',
            'all_items'                  => 'Kaikki statukset',
            'new_item_name'              => 'Lisää uusi status',
            'add_new_item'               => 'Lisää uusi status',
            'edit_item'                  => 'Muokkaa statusta',
            'update_item'                => 'Päivitä status',
            'view_item'                  => 'Näytä status',
            'separate_items_with_commas' => \__( 'Erottele statukset pilkulla', 'tms-theme-base' ),
            'add_or_remove_items'        => \__( 'Lisää tai poista status', 'tms-theme-base' ),
            'choose_from_most_used'      => \__( 'Suositut statukset', 'tms-theme-base' ),
            'popular_items'              => \__( 'Suositut statukset', 'tms-theme-base' ),
            'search_items'               => 'Etsi status',
            'not_found'                  => 'Ei tuloksia',
            'no_terms'                   => 'Ei tuloksia',
            'items_list'                 => 'Statukset',
            'items_list_navigation'      => 'Statukset',
        ];

        $args = [
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud'     => false,
            'show_in_rest'      => true,
        ];

        register_taxonomy( self::SLUG, [ Exhibition::SLUG ], $args );
    }
}
