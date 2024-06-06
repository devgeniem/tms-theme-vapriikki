<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

/**
 * Alter Contacts block, layout
 */
class AlterContacts {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter(
            'tms/acf/block/contacts/data',
            [ $this, 'alter_format' ],
            20
        );
        add_filter(
            'tms/acf/layout/contacts/data',
            [ $this, 'alter_format' ],
            20
        );
    }

    /**
     * Format layout data. Change column class
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function alter_format( array $layout ) : array {
        $layout['column_class'] = 'is-12-mobile is-6-tablet is-6-desktop';
        return $layout;
    }
}

( new AlterContacts() );
