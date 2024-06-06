<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class DividerLayout
 *
 * @package TMS\Theme\Vapriikki\ACF\Layouts
 */
class DividerLayout extends Layout {

    /**
     * Layout key
     */
    const KEY = '_divider';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Erotin',
            $key . self::KEY,
            'divider'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $key = $this->get_key();

        $strings = [
            'divider' => [
                'label' => 'Erotin',
            ],
        ];

        $divider_field = ( new Field\Message( $strings['divider']['label'] ) )
            ->set_key( "{$key}_divider" )
            ->set_name( 'divider' );

        try {
            $this->add_fields(
                apply_filters(
                    'tms/acf/layout/' . $this->get_key() . '/fields',
                    [ $divider_field ]
                )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
