<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use Geniem\ACF\Field\Tab;
use TMS\Theme\Base\Logger;

/**
 * Class DigitalExhibitionsSettingsTab
 *
 * @package TMS\Theme\Muumimuseo\ACF\Tab
 */
class DigitalExhibitionsSettingsTab extends Tab {

    /**
     * Where should the tab switcher be located
     *
     * @var string
     */
    protected $placement = 'left';

    /**
     * Tab strings.
     *
     * @var array
     */
    protected $strings = [
        'tab'               => 'Digitaaliset näyttelyt',
        'page_button'       => 'Lisää sivu',
        'exhibition_button' => 'Lisää näyttely',
        'exhibitions'       => [
            'title'          => 'Näyttelyt',
            'instructions'   => '',
            'repeater_title' => 'Näyttelyiden sivu',
            'single_page'    => [
                'title'        => 'Näyttelyiden sivun nimi',
                'instructions' => '',
            ],
            'name'           => [
                'title'        => 'Näyttelyn nimi',
                'instructions' => '',
            ],
            'description'    => [
                'title'        => 'Kuvaus',
                'instructions' => 'Näyttelyn lyhyt kuvausteksti',
            ],
            'date_start'     => [
                'title'        => 'Alkuajankohta',
                'instructions' => '',
            ],
            'date_end'       => [
                'title'        => 'Loppuajankohta',
                'instructions' => '',
            ],
            'link'           => [
                'title'        => 'Linkki',
                'instructions' => '',
            ],
        ],
    ];

    /**
     * The constructor for tab.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) { // phpcs:ignore
        $label = $this->strings['tab'];

        parent::__construct( $label );

        $this->sub_fields( $key );
    }

    /**
     * Register sub fields.
     *
     * @param string $key Field tab key.
     */
    public function sub_fields( $key ) {
        $strings = $this->strings;

        try {
            $exhibition_repeater_field = ( new Field\Repeater( $strings['exhibitions']['title'] ) )
                ->set_key( "{$key}_digital_exhibitions" )
                ->set_name( 'digital_exhibitions' )
                ->set_button_label( $strings['page_button'] )
                ->set_layout( 'block' )
                ->set_instructions( $strings['exhibitions']['instructions'] );

            $exhibition_page_repeater = ( new Field\Repeater( $strings['exhibitions']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_page_repeater" )
                ->set_name( 'digital_exhibition_page_repeater' )
                ->set_button_label( $strings['exhibition_button'] )
                ->set_layout( 'block' )
                ->set_instructions( $strings['exhibitions']['instructions'] );

            $exhibition_page_name = ( new Field\Text( $strings['exhibitions']['single_page']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_page_name" )
                ->set_name( 'digital_exhibition_page_name' )
                ->set_required()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['exhibitions']['single_page']['instructions'] );

            $exhibition_name = ( new Field\Text( $strings['exhibitions']['name']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_name" )
                ->set_name( 'digital_exhibition_name' )
                ->set_required()
                ->set_instructions( $strings['exhibitions']['name']['instructions'] );

            $exhibition_description = ( new Field\TextArea( $strings['exhibitions']['description']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_description" )
                ->set_name( 'digital_exhibition_description' )
                ->set_instructions( $strings['exhibitions']['description']['instructions'] );

            $exhibition_link = ( new Field\Link( $strings['exhibitions']['link']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_link" )
                ->set_name( 'digital_exhibition_link' )
                ->set_instructions( $strings['exhibitions']['link']['instructions'] );

            $exhibition_date_start = ( new Field\DatePicker( $strings['exhibitions']['date_start']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_date_start" )
                ->set_name( 'digital_exhibition_date_start' )
                ->set_return_format( 'd.m.Y' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['exhibitions']['date_start']['instructions'] );

            $exhibition_date_end = ( new Field\DatePicker( $strings['exhibitions']['date_end']['title'] ) )
                ->set_key( "{$key}_digital_exhibition_date_end" )
                ->set_name( 'digital_exhibition_date_end' )
                ->set_return_format( 'd.m.Y' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['exhibitions']['date_end']['instructions'] );

            $exhibition_repeater_field->add_fields( [ $exhibition_page_name, $exhibition_page_repeater ] );
            $exhibition_page_repeater->add_fields( [ $exhibition_name, $exhibition_description, $exhibition_link, $exhibition_date_start, $exhibition_date_end ] );

            $this->add_fields( [
                $exhibition_repeater_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
