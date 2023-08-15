<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class ExhibitionCarouselFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class ExhibitionCarouselFields extends Field\Group {

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
     * @throws Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'title'       => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description' => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'rows'        => [
                'label'        => 'Kuvakaruselli',
                'instructions' => 'Valitse vähintään 4 näyttelyä (muuten kuvakarusellia ei näytetä).',
                'button'       => 'Lisää rivi',
            ],
            'exhibition' => [
                'label'        => 'Näyttely',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\ExtendedWysiwyg( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_tabs( 'visual' )
            ->set_toolbar(
                [
                    'bold',
                    'italic',
                    'link',
                    'pastetext',
                    'removeformat',
                ]
            )
            ->disable_media_upload()
            ->set_height( 100 )
            ->set_instructions( $strings['description']['instructions'] );

        $rows_field = ( new Field\Repeater( $strings['rows']['label'] ) )
            ->set_key( "{$key}_rows" )
            ->set_name( 'rows' )
            ->set_min( 4 )
            ->set_max( 20 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['rows']['button'] )
            ->set_instructions( $strings['rows']['instructions'] );

        $exhibition_field = ( new Field\PostObject( $strings['exhibition']['label'] ) )
            ->set_key( "{$key}_exhibition" )
            ->set_name( 'exhibition' )
            ->set_required()
            ->set_post_types( [ 'exhibition' ] )
            ->set_instructions( $strings['exhibition']['instructions'] );

        $rows_field->add_fields( [
            $exhibition_field,
        ] );

        return [
            $title_field,
            $description_field,
            $rows_field,
        ];
    }
}
