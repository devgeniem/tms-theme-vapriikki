<?php
/**
 * Copyright (c) 2023. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class DividerFields
 *
 * @package TMS\Theme\Vapriikki\ACF\Fields
 */
class DividerFields extends \Geniem\ACF\Field\Group {

    /**
     * The constructor for field.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        parent::__construct( $label, $key, $name );

        try {
            $this->add_fields( $this->sub_fields() );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'divider'       => [
                'label' => 'Erotin',
            ],
        ];

        $key = $this->get_key();

        $divider_field = ( new Field\Message( $strings['divider']['label'] ) )
            ->set_key( "{$key}_divider_field" )
            ->set_name( 'divider_field' );

        return [
            $divider_field,
        ];
    }
}
