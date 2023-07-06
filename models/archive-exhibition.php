<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\Traits\Pagination;
use TMS\Theme\Vapriikki\PostType\Exhibition;
use TMS\Theme\Base\Settings;

/**
 * Archive for Exhibition CPT
 */
class ArchiveExhibition extends BaseModel {

    use Pagination;

    /**
     * Search input name.
     */
    const SEARCH_QUERY_VAR = 'exhibition-search';

    /**
     * Year filter name.
     */
    const YEAR_QUERY_VAR = 'exhibition-year';

    /**
     * Year filter name for digital exhibitions.
     */
    const DIGITAL_QUERY_VAR = 'digital-exhibition';

    /**
     * Exhibition archive filter name.
     */
    const PAST_QUERY_VAR = 'archive';

    /**
     * Upcoming exhibition filter name.
     */
    const UPCOMING_QUERY_VAR = 'upcoming';

    /**
     * Number of past items to show per page.
     */
    const PAST_ITEMS_PER_PAGE = '9';

    /**
     * Number of ongoing items to show per page.
     */
    const ONGOING_ITEMS_PER_PAGE = '100';

    /**
     * Number of upcoming items to show per page.
     */
    const UPCOMING_ITEMS_PER_PAGE = '100';

    /**
     * Pagination data.
     *
     * @var object
     */
    protected object $pagination;

    /**
     * Results data.
     *
     * @var object
     */
    protected object $results;

    /**
     * ArchiveExhibition constructor.
     *
     * @param array $args   Arguments.
     * @param null  $parent Parent.
     */
    public function __construct( $args = [], $parent = null ) {
        parent::__construct( $args, $parent );

        if ( is_post_type_archive( Exhibition::SLUG ) ) {
            $this->results = new stdClass();

            $args = [
                'post_type'      => Exhibition::SLUG,
                'posts_per_page' => self::ONGOING_ITEMS_PER_PAGE,
                'post_status'    => 'publish',
                'orderby'        => [ 'start_date' => 'ASC', 'title' => 'ASC' ],
                'meta_key'       => 'start_date',
            ];

            $query = new WP_Query( $args );

            $this->results->all      = $query->have_posts() ? $query->posts : [];
            $this->results->upcoming = $query->have_posts()
                ? array_filter( $query->posts, [ $this, 'is_upcoming' ] )
                : [];
        }
    }

    /**
     * Hooks
     */
    public static function hooks() : void {
        add_action(
            'pre_get_posts',
            [ __CLASS__, 'modify_query' ]
        );
    }

    /**
     * Get search query var value
     *
     * @return mixed
     */
    protected static function get_search_query_var() {
        return get_query_var( self::SEARCH_QUERY_VAR, false );
    }

    /**
     * Get filter query var value
     *
     * @return string|null
     */
    protected static function get_year_query_var() : ?string {
        $value = get_query_var( self::YEAR_QUERY_VAR, false );

        return ! $value
            ? null
            : sanitize_text_field( $value );
    }

    /**
     * Get current tab query var value
     *
     * @return bool
     */
    public function is_past_archive() : bool {
        return ! is_null( get_query_var( self::PAST_QUERY_VAR, null ) );
    }

    /**
     * Get current tab query var value
     *
     * @return bool
     */
    public function is_digital_exhibitions() : bool {
        return ! is_null( \get_query_var( self::DIGITAL_QUERY_VAR, null ) );
    }

    /**
     * Get current tab query var value
     *
     * @return bool
     */
    public function is_upcoming_archive() : bool {
        return ! is_null( \get_query_var( self::UPCOMING_QUERY_VAR, null ) );
    }

    /**
     * Page title
     *
     * @return string
     */
    public function page_title() : string {
        return post_type_archive_title( '', false );
    }

    /**
     * Return translated strings.
     *
     * @return array[]
     */
    public function strings() : array {
        return [
            'search'           => [
                'label'             => __( 'Search from archive', 'tms-theme-vapriikki' ),
                'submit_value'      => __( 'Search', 'tms-theme-vapriikki' ),
                'input_placeholder' => __( 'Type a search word', 'tms-theme-vapriikki' ),
            ],
            'no_results'       => __( 'No results', 'tms-theme-vapriikki' ),
            'year_label'       => __( 'Year', 'tms-theme-vapriikki' ),
            'year_filter_info' => __( 'Selecting the year filter limits the exhibition view.', 'tms-theme-vapriikki' ),
            'upcoming_badge'   => __( 'Upcoming', 'tms-theme-vapriikki' ),
        ];
    }

    /**
     * Modify query
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    public static function modify_query( WP_Query $wp_query ) : void {
        if ( is_admin() || ( ! $wp_query->is_main_query() || ! $wp_query->is_post_type_archive( Exhibition::SLUG ) ) ) {
            return;
        }

        $posts_per_page   = self::UPCOMING_ITEMS_PER_PAGE;
        $start_date_order = 'ASC';
        $instance         = new ArchiveExhibition();

        if ( $instance->is_past_archive() ) {
            if ( ! empty( $instance->results->upcoming ) ) {
                $wp_query->set( 'post__not_in', wp_list_pluck( $instance->results->upcoming, 'ID' ) );
            }

            $posts_per_page   = self::PAST_ITEMS_PER_PAGE;
            $start_date_order = 'DESC';

            $meta_query[] = [
                'key'     => 'end_date',
                'compare' => '<',
                'value'   => date( 'Ymd' ),
            ];

            $year = self::get_year_query_var();
            $s    = self::get_search_query_var();

            if ( ! empty( $year ) ) {
                $meta_query[] = [
                    'key'     => 'exhibition_year',
                    'compare' => '=',
                    'value'   => $year,
                ];
            }

            if ( ! empty( $s ) ) {
                $meta_query[] = [
                    'key'     => 'title',
                    'compare' => 'LIKE',
                    'value'   => $s,
                ];
            }

            $wp_query->set( 'meta_query', $meta_query );
        }

        $wp_query->set( 'orderby', [ 'start_date' => $start_date_order, 'title' => 'ASC' ] );
        $wp_query->set( 'meta_key', 'start_date' );
        $wp_query->set( 'posts_per_page', $posts_per_page );
    }

    /**
     * Return current search data.
     *
     * @return string[]
     */
    public function search() : array {
        $this->search_data        = new stdClass();
        $this->search_data->query = get_query_var( self::SEARCH_QUERY_VAR );

        return [
            'input_search_name' => self::SEARCH_QUERY_VAR,
            'current_search'    => $this->search_data->query,
            'action'            => add_query_arg(
                self::PAST_QUERY_VAR,
                '',
                \get_post_type_archive_link( Exhibition::SLUG )
            ),
        ];
    }

    /**
     * Return current search data.
     *
     * @return string[]
     */
    public function tabs() : array {
        $base_url            = get_post_type_archive_link( Exhibition::SLUG );
        $past_tab_active     = self::is_past_archive();
        $upcoming_tab_active = self::is_upcoming_archive();
        $digital_tab_active  = self::is_digital_exhibitions();

        return [
            'upcoming' => [
                'text'      => __( 'Upcoming exhibitions', 'tms-theme-vapriikki' ),
                'link'      => add_query_arg(
                    self::UPCOMING_QUERY_VAR,
                    '',
                    $base_url
                ),
                'is_active' => $upcoming_tab_active,
            ],
            'ongoing' => [
                'text'      => __( 'Current exhibitions', 'tms-theme-vapriikki' ),
                'link'      => $base_url,
                'is_active' => ! $past_tab_active && ! $upcoming_tab_active && ! $digital_tab_active,
            ],
            'past'     => [
                'text'      => __( 'Past exhibitions', 'tms-theme-vapriikki' ),
                'link'      => add_query_arg(
                    self::PAST_QUERY_VAR,
                    '',
                    $base_url
                ),
                'is_active' => $past_tab_active,
            ],
            'digital' => [
                'text'      => __( 'Digital exhibitions', 'tms-theme-vapriikki' ),
                'link'      => add_query_arg(
                    self::DIGITAL_QUERY_VAR,
                    '',
                    $base_url
                ),
                'is_active' => $digital_tab_active,
            ],
        ];
    }

    /**
     * View results
     *
     * @return array
     */
    public function results() {
        global $wp_query;

        $is_past_archive        = $this->is_past_archive();
        $is_digital_exhibitions = $this->is_digital_exhibitions();
        $is_upcoming_archive    = $this->is_upcoming_archive();
        $is_ongoing_archive     = ! $is_past_archive && ! $is_upcoming_archive && ! $is_digital_exhibitions;
        $per_page               = ( $is_past_archive ) ? self::PAST_ITEMS_PER_PAGE : ( ( $is_upcoming_archive ) ? self::UPCOMING_ITEMS_PER_PAGE : self::ONGOING_ITEMS_PER_PAGE );
        $current_exhibitions    = array_filter( $this->results->all, [ $this, 'is_current' ] );
        $upcoming_exhibitions   = $this->results->upcoming;

        $unfiltered_past_exhibitions = array_filter( $this->results->all, [ $this, 'is_past' ] );
        $past_exhibitions            = $wp_query->posts;
        $this->results->past         = $past_exhibitions;

        $digital_exhibitions = Settings::get_setting( 'digital_exhibitions' ) ?: [];

        $results = $is_past_archive ? $past_exhibitions : $upcoming_exhibitions;
        $this->set_pagination_data( count( $results ), $per_page );

        return [
            'result_count'                 => count( $current_exhibitions ),
            'past_results_count'           => count( $unfiltered_past_exhibitions ),
            'upcoming_results_count'       => count( $upcoming_exhibitions ),
            'digital_results_count'        => $this->count_digital_exhibitions( $digital_exhibitions ),
            'show_digital'                 => $is_digital_exhibitions,
            'show_past'                    => $is_past_archive,
            'show_ongoing'                 => $is_ongoing_archive,
            'show_upcoming'                => $is_upcoming_archive,
            'current_exhibitions'          => $this->reorder_main_exhibitions( $this->format_posts( $current_exhibitions ) ),
            'upcoming_exhibitions'         => $this->reorder_main_exhibitions( $this->format_posts( $upcoming_exhibitions ) ),
            'posts'                        => $this->reorder_main_exhibitions( $this->format_posts( $results ) ),
            'digital_exhibition_pages'     => $this->digital_exhibitions_pages( $digital_exhibitions ),
            'digital_exhibitions'          => $this->format_digital_exhibitions( $digital_exhibitions ),
            'summary'                      => $this->results_summary( count( $results ) ),
            'have_posts'                   => ! empty( $results ) || ! empty( $current_exhibitions ),
            'partial'                      => $is_past_archive ? 'shared/exhibition-item-simple' : 'shared/exhibition-item',
            'digital_exhibitions_partial'  => 'shared/exhibition-item-digital',
        ];
    }

    /**
     * Years
     *
     * @return array
     */
    public function years() {
        if ( ! $this->is_past_archive() ) {
            return;
        }

        $choices = [];
        $items   = $this->results->past;

        if ( empty( $items ) ) {
            return $choices;
        }

        $selected_year = self::get_year_query_var();

        foreach ( $items as $exhibition ) {
            $year = get_post_meta( $exhibition->ID, 'exhibition_year', true );

            if ( in_array( $year, array_column( $choices, 'value' ), true ) ) {
                continue;
            }

            if ( ! empty( $year ) ) {
                $choices[] = [
                    'value'       => $year,
                    'label'       => $year,
                    'is_selected' => $year === $selected_year ? 'selected' : '',
                ];
            }
        }

        return $choices;
    }

    /**
     * Format digital exhibitions for view
     *
     * @param array $posts Array of digital_exhibitions repeater items.
     *
     * @return array|bool
     */
    protected function digital_exhibitions_pages( array $posts ) {
        if ( empty( $posts ) ) {
            return false;
        }

        return array_map( function ( $item ) {
            $base_url    = get_post_type_archive_link( Exhibition::SLUG );
            $page        = (object) $item;
            $page->years = str_replace( ' ', '', $page->digital_exhibition_page_name );
            $page->link  = \add_query_arg(
                self::DIGITAL_QUERY_VAR,
                $page->years,
                $base_url
            );
            $current_filter = \get_query_var( self::DIGITAL_QUERY_VAR );
            $page->current  = ! empty( $current_filter ) & $current_filter === str_replace( ' ', '', $page->digital_exhibition_page_name ) ? 'selected' : '';

            return $page;
        }, $posts );
    }

    /**
     * Reorder main exhibitions to top of other exhibitions with the same dates
     *
     * @param array $items Array of WP_Post instances.
     *
     * @return array
     */
    protected function reorder_main_exhibitions( $items ) {

        // Return original $items array if search or year filter is used
        if ( self::get_search_query_var() || self::get_year_query_var() ) {
            return $items;
        }

        // Return null if there are no items in array
        if ( $items === false ) {
            return;
        }

        $items  = array_values( $items ); // reset array keys to start from 0
        $length = count( $items );

        // Loop through exhibitions and get main exhibitions to an array
        for ( $i = 0; $i < $length; $i++ ) {
            // Check if the main exhibition true/false field is checked & the meta-value exists
            if ( ! empty( $items[ $i ]->main_exhibition ) && $items[ $i ]->main_exhibition === '1' ) {
                // Get main exhibitions original position for buggy situations
                $items[ $i ]->original_position = $i;

                // Make an array for the main exhibitions
                $main_exhibitions[] = $items[ $i ];

                // Remove the main exhibition from the original $items array
                unset( $items[ $i ] );
            }
        }

        $items  = array_values( $items ); // reset array keys to start from 0 again
        $length = count( $items );

        // Loop through exhibitions and compare main exhibition dates with other exhibitions
        if ( isset( $main_exhibitions ) ) {
            // Loop main exhibitions
            foreach ( $main_exhibitions as $main ) {
                // Loop normal exhibitions
                for ( $i = 0; $i <= $length; $i++ ) {
                    // Check if item dates exists
                    if ( ! empty( $items[ $i ]->dates ) ) {
                        // Compare main exhibitions dates with each normal exhibitions dates and get the first matches position
                        if ( array_intersect( $items[ $i ]->dates, $main->dates ) && $items[ $i ]->ID !== $main->ID
                        && ( empty( $items[ $i ]->main_exhibition ) || $items[ $i ]->main_exhibition === '0' ) ) {
                            // Set the position as a variable for the main exhibition and break the loop
                            $main->position = $i;
                            // Break the loop when a match is found
                            break;
                        }
                        else {
                            // Set original position for the main exhibition if there are no matches
                            $main->position = $main->original_position;
                        }
                    }
                }
            }

            unset( $main );

            // Set each main exhibition back to the $items array to their new positions
            foreach ( $main_exhibitions as $main ) {
                array_splice( $items, intval( $main->position ), 0, [ $main ] );
            }
        }

        return $items;
    }

    /**
     * Is the items' end_date in the past?
     *
     * @param WP_Post $item Item object.
     *
     * @return bool
     */
    protected function is_past( $item ) {
        $format = 'Ymd';
        $today  = new DateTime( 'now' );
        $today->setTime( '0', '0' );

        $end_date = DateTime::createFromFormat( $format, get_post_meta( $item->ID, 'end_date', true ) );
        $end_date->setTime( '23', '59' );

        return $today > $end_date;
    }

    /**
     * Is the item currently running?
     *
     * @param WP_Post $item Item object.
     *
     * @return bool
     */
    protected function is_current( $item ) {
        $format = 'Ymd';
        $today  = new DateTime( 'now' );
        $today->setTime( '0', '0' );

        $start_date = DateTime::createFromFormat( $format, get_post_meta( $item->ID, 'start_date', true ) );
        $start_date->setTime( '0', '0' );

        $end_date = DateTime::createFromFormat( $format, get_post_meta( $item->ID, 'end_date', true ) );
        $end_date->setTime( '23', '59' );

        return $today >= $start_date && $today <= $end_date;
    }

    /**
     * Is the items' start date in the future?
     *
     * @param WP_Post $item Item object.
     *
     * @return bool
     */
    protected function is_upcoming( $item ) {
        $format = 'Ymd';
        $today  = new DateTime( 'now' );

        $start_date = DateTime::createFromFormat( $format, get_post_meta( $item->ID, 'start_date', true ) );

        return $start_date >= $today;
    }

    /**
     * Format posts for view
     *
     * @param array $posts Array of WP_Post instances.
     *
     * @return array|bool
     */
    protected function format_posts( array $posts ) {
        if ( empty( $posts ) ) {
            return false;
        }

        return array_map( function ( $item ) {
            $item->permalink   = get_the_permalink( $item->ID );
            $additional_fields = get_fields( $item->ID );
            $item->post_title  = $additional_fields['title'] ?: $item->post_title;
            $item->fields      = $additional_fields;
            $date              = SingleExhibition::get_date( $item->ID );

            if ( ! empty( $date ) ) {
                $item->date = $date;
            }

            if ( has_post_thumbnail( $item->ID ) ) {
                $item->image = get_post_thumbnail_id( $item->ID );
            }

            // Get single dates between start_date and end_date for the exhibitions
            $start = new DateTime( $additional_fields['start_date'] );
            $end   = clone $start;
            $end->modify( $additional_fields['end_date'] );
            $end->setTime( 0, 0, 1 ); // Add time to the end_day so it will be included
            $interval    = new DateInterval( 'P1D' ); // Interval period 1 day
            $date_period = new DatePeriod( $start, $interval, $end );
            foreach ( $date_period as $date ) {
                $date_array[] = $date->format( 'Y-m-d' );
            }

            $item->dates = $date_array;

            return $item;
        }, $posts );
    }

    /**
     * Format digital exhibitions for view
     *
     * @param array $posts Array of digital_exhibitions repeater items.
     *
     * @return array|bool
     */
    protected function format_digital_exhibitions( array $posts ) {
        if ( empty( $posts ) ) {
            return false;
        }

        $digital_exhibitions = [];
        $current_filter       = \get_query_var( self::DIGITAL_QUERY_VAR );

        foreach ( $posts as $exhibitions ) {
            $exhibition_repeater                   = $exhibitions['digital_exhibition_page_repeater'];
            $exhibition_page_name                  = $exhibitions['digital_exhibition_page_name'];
            $exhibition_page_active                = empty( $current_filter ) || $current_filter === str_replace( ' ', '', $exhibition_page_name ) ? 'selected' : '';
            $exhibitions['exhibition_page_active'] = $exhibition_page_active;

            if ( ( empty( $current_filter ) || $current_filter === str_replace( ' ', '', $exhibition_page_name ) ) && ! empty( $exhibition_repeater ) ) {
                foreach ( $exhibition_repeater as $exhibition ) {
                    $exhibition['post_title'] = $exhibition['digital_exhibition_name'];
                    $exhibition['link']       = $exhibition['digital_exhibition_link'];
                    $start_date               = $exhibition['digital_exhibition_date_start'];
                    $dates                    = '';

                    if ( ! empty( $start_date ) ) {
                        $dates    = $start_date;
                        $end_date = $exhibition['digital_exhibition_date_end'];

                        if ( ! empty( $end_date ) ) {
                            $dates .= ' - ' . $end_date;
                        }
                    }

                    $exhibition['date'] = $dates;

                    $digital_exhibitions[] = $exhibition;
                }
            }
        }

        return $digital_exhibitions;
    }

    /**
     * Count digital exhibitions
     *
     * @param array $posts Array of digital_exhibitions repeater items.
     *
     * @return array|bool
     */
    protected function count_digital_exhibitions( array $posts ) {
        $total_count['empty'] = true;

        if ( empty( $posts ) || ! isset( $posts ) ) {
            return $total_count;
        }

        $exhibition_count = 0;

        foreach ( $posts as $exhibitions ) {
            $exhibition_repeater = $exhibitions['digital_exhibition_page_repeater'];
            if ( empty( $exhibition_repeater ) || ! isset( $exhibition_repeater ) ) {
                return $total_count;
            }

            $exhibition_count += count( $exhibition_repeater );
        }

        if ( $exhibition_count !== 0 ) {
            $total_count['empty'] = false;
        }


        $total_count['number'] = $exhibition_count;

        return $total_count;
    }

    /**
     * Set pagination data
     *
     * @param int    $count    Total result count.
     * @param string $per_page Number of items per page.
     */
    protected function set_pagination_data( $count, $per_page ) : void {
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $this->pagination           = new stdClass();
        $this->pagination->page     = $paged;
        $this->pagination->per_page = $per_page;
        $this->pagination->items    = $count;
        $this->pagination->max_page = (int) ceil( $count / $per_page );
    }

    /**
     * Get results summary text.
     *
     * @param int $result_count Result count.
     *
     * @return string|bool
     */
    protected function results_summary( $result_count ) {

        $search_clause = self::get_search_query_var();
        $is_filtered   = $search_clause || self::get_year_query_var();

        if ( ! $is_filtered ) {
            return false;
        }

        if ( ! empty( $search_clause ) ) {
            $results_text = sprintf(
            // translators: 1. placeholder is number of search results, 2. placeholder contains the search term(s).
                _nx(
                    '%1$1s result found for "%2$2s"',
                    '%1$1s results found for "%2$2s"',
                    $result_count,
                    'filter with search clause results summary',
                    'tms-theme-vapriikki'
                ),
                $result_count,
                $search_clause
            );
        }
        else {
            $results_text = sprintf(
            // translators: 1. placeholder is number of search results
                _nx(
                    '%1$1s result found',
                    '%1$1s results found',
                    $result_count,
                    'filter results summary',
                    'tms-theme-vapriikki'
                ),
                $result_count,
                $search_clause
            );
        }

        return $results_text;
    }
}
