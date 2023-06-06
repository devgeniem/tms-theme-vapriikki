<?php
/**
 *  Copyright (c) 2022. Geniem Oy
 */

namespace TMS\Theme\Vapriikki;

use Geniem\Role;
use TMS\Theme\Base\Interfaces\Controller;

/**
 * RolesController
 */
class RolesController implements Controller {

    /**
     * Exhibition / exhibition-cpt
     *
     * @var string[]
     */
    private $exhibition_all_capabilities = [
        'delete_exhibition',
        'delete_exhibitions',
        'delete_others_exhibitions',
        'delete_private_exhibitions',
        'delete_published_exhibitions',
        'edit_exhibition',
        'edit_exhibitions',
        'edit_others_exhibitions',
        'edit_private_exhibitions',
        'edit_published_exhibitions',
        'publish_exhibitions',
        'read',
        'read_exhibition',
        'read_private_exhibitions',
    ];

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter( 'tms/roles/super_administrator', [ $this, 'modify_super_administrator_caps' ] );
        add_filter( 'tms/roles/admin', [ $this, 'modify_admin_caps' ] );
        add_filter( 'tms/roles/editor', [ $this, 'modify_editor_caps' ] );
        add_filter( 'tms/roles/author', [ $this, 'modify_author_caps' ] );
        add_filter( 'tms/roles/contributor', [ $this, 'modify_contributor_caps' ] );
    }

    /**
     * Modify Super Administrator caps
     *
     * @param Role $role Instance of \Geniem\Role.
     */
    public function modify_super_administrator_caps( Role $role ) {
        $role->add_caps( $this->exhibition_all_capabilities );

        return $role;
    }

    /**
     * Modify Administrator caps
     *
     * @param Role $role Instance of \Geniem\Role.
     */
    public function modify_admin_caps( Role $role ) {
        $role->add_caps( $this->exhibition_all_capabilities );

        return $role;
    }

    /**
     * Modify Editor caps
     *
     * @param Role $role Instance of \Geniem\Role.
     */
    public function modify_editor_caps( Role $role ) {
        $role->add_caps( $this->exhibition_all_capabilities );

        return $role;
    }

    /**
     * Modify Author caps
     *
     * @param Role $role Instance of \Geniem\Role.
     */
    public function modify_author_caps( Role $role ) {
        $role->add_caps( $this->exhibition_all_capabilities );

        return $role;
    }

    /**
     * Modify Contributor caps
     *
     * @param Role $role Instance of \Geniem\Role.
     */
    public function modify_contributor_caps( Role $role ) {
        $role->add_caps( $this->exhibition_all_capabilities );

        return $role;
    }
}
