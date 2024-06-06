<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Vapriikki\ACF\Fields\ExhibitionsFields;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Layouts\BaseLayout;
use TMS\Theme\Vapriikki\PostType\Exhibition;
use TMS\Theme\Vapriikki\Taxonomy\ExhibitionCategory;
use Geniem\ACF\ConditionalLogicGroup;
use TMS\Theme\Base\Logger;

/**
 * Class ExhibitionsLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class ExhibitionsLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_exhibitions';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Näyttelynostot',
            $key . self::KEY,
            'exhibitions'
        );

        $this->add_layout_fields();
    }

    /**
     * Add Repeater selection: Relationship.
     *
     * @return \Geniem\ACF\Field\Group
     * @throws \Geniem\ACF\Exception Thrown if mandatory fields have not been set.
     */
    protected function exhibition_item_field_group() : Field\Group {

        $strings = [
            'exhibition' => [
                'label'        => 'Näyttely',
                'instructions' => '',
            ],
        ];

        $group = ( new Field\Group( $strings['exhibition']['label'] ) )
            ->hide_label()
            ->set_key( $this->get_key() . '_exhibition_item' )
            ->set_name( 'exhibition_item' );

        $exhibition_field = ( new Field\PostObject( $strings['exhibition']['label'] ) )
            ->set_key( $this->get_key() . '_exhibition' )
            ->set_name( 'exhibition' )
            ->set_return_format( 'id' )
            ->set_required()
            ->set_post_types( [ Exhibition::SLUG ] )
            ->set_instructions( $strings['exhibition']['instructions'] );

        $group->add_field( $exhibition_field );

        return $group;
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $key = $this->get_key();

        $strings = [
            'title' => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description' => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'limit' => [
                'label'        => 'Lukumäärä',
                'instructions' => 'Valitse väliltä 4-12',
            ],
            'exhibition' => [
                'label'        => 'Näyttely',
                'instructions' => '',
            ],
            'feed_type' => [
                'label'          => 'Listauksen tyyppi',
                'instructions'   => '',
                'type_automatic' => 'Automaattinen',
                'type_manual'    => 'Manuaalinen',
            ],
            'category' => [
                'label'        => 'Kategoriat',
                'instructions' => 'Esitä artikkeleja valituista kategorioista',
            ],
            'exhibition_repeater' => [
                'label'        => 'Näyttelyt',
                'instructions' => 'Valitse 4-12 näyttelyä',
                'button'       => 'Lisää näyttely',
            ],
            'link' => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
        ];

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_wrapper_width( 50 )
            ->set_rows( 4 )
            ->set_instructions( $strings['description']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( $this->get_key() . '_link' )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $feed_type_field = ( new Field\Radio( $strings['feed_type']['label'] ) )
            ->set_key( "${key}_feed_type" )
            ->set_name( 'feed_type' )
            ->set_choices( [
                'automatic' => $strings['feed_type']['type_automatic'],
                'manual'    => $strings['feed_type']['type_manual'],
            ] )
            ->set_instructions( $strings['feed_type']['instructions'] );

        $rule_group_automatic = ( new ConditionalLogicGroup() )
            ->add_rule( $feed_type_field, '==', 'automatic' );
        $rule_group_manual    = ( new ConditionalLogicGroup() )
            ->add_rule( $feed_type_field, '==', 'manual' );

        $category_field = ( new Field\Taxonomy( $strings['category']['label'] ) )
            ->set_key( "${key}_category" )
            ->set_name( 'category' )
            ->set_taxonomy( ExhibitionCategory::SLUG )
            ->set_return_format( 'id' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['category']['instructions'] );

        $limit_field = ( new Field\Number( $strings['limit']['label'] ) )
            ->set_key( "${key}_number" )
            ->set_name( 'number' )
            ->set_min( 4 )
            ->set_max( 12 )
            ->set_default_value( 12 )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['limit']['instructions'] );

        $category_field->add_conditional_logic( $rule_group_automatic );
        $limit_field->add_conditional_logic( $rule_group_automatic );

        $exhibition_item_manual_field_group = $this->exhibition_item_field_group();

        $exhibitions_repeater_field = ( new Field\Repeater( $strings['exhibition_repeater']['label'] ) )
            ->set_key( $this->get_key() . '_exhibition_repeater' )
            ->set_name( 'exhibition_repeater' )
            ->set_layout( 'block' )
            ->set_min( 1 )
            ->set_max( 12 )
            ->set_button_label( $strings['exhibition_repeater']['button'] )
            ->set_instructions( $strings['exhibition_repeater']['instructions'] );

        $exhibitions_repeater_field->add_field( $exhibition_item_manual_field_group );
        $exhibitions_repeater_field->add_conditional_logic( $rule_group_manual );

        $fields = [
            $title_field,
            $description_field,
            $feed_type_field,
            $exhibitions_repeater_field,
            $category_field,
            $limit_field,
            $link_field,
        ];

        try {
            $this->add_fields(
                $this->filter_layout_fields( $fields, $this->get_key(), self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
