<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vapriikki\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Layouts\BaseLayout;
use TMS\Theme\Vapriikki\ACF\Fields\ExhibitionCarouselFields;
use TMS\Theme\Base\Logger;

/**
 * Class ExhibitionCarousel
 *
 * @package TMS\Theme\Vapriikki\ACF\Layouts
 */
class ExhibitionCarouselLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_exhibition_carousel';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Näyttelykaruselli',
            $key . self::KEY,
            'exhibition_carousel'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new ExhibitionCarouselFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $this->add_fields(
                $this->filter_layout_fields( $fields->get_fields(), $this->get_key(), self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
