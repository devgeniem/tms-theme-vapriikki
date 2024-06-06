/**
 * Copyright (c) 2021. Geniem Oy
 */

import '@accessible360/accessible-slick';
import Common from '../../../tms-theme-base/assets/scripts/common';

// Use jQuery as $ within this file scope.
const $ = jQuery;

export default class ExhibitionCarousel {

    cache() {
        this.exhibition_carousels = $( '.exhibition-carousel' );
    }

    initCarousels() {
        // Translations are defined in models/strings.php,
        // and loaded to windows.s in lib/Assets.php.
        const translations = window.s.gallery || {
            next: 'Next',
            previous: 'Previous',
            close: 'Close',
            goto: 'Go to slide',
        };

        const icons = {
            prev: Common.makeIcon( 'chevron-left' ),
            next: Common.makeIcon( 'chevron-right' ),
        };

        const prevSrText = `<span class="is-sr-only">${ translations.previous }</span>`;
        const nextSrText = `<span class="is-sr-only">${ translations.next }</span>`;

        const arrowClass = 'button button--icon exhibition-carousel__modal-control';

        const buttons = {
            prevArrow: Common.makeButton( icons.prev + prevSrText, `${ arrowClass } slick-prev` ),
            nextArrow: Common.makeButton( icons.next + nextSrText, `${ arrowClass } slick-next` ),
        };

        $( this.exhibition_carousels ).each( ( n, element ) => {
            this.constructCarousel( element, buttons, translations );
        } );
    }

    /**
     * Constructs the carousel, or two if we have sync defined.
     *
     * @param {HTMLElement} container    Main carousel element.
     * @param {Object}      translations Translations.
     * @return {*|jQuery|HTMLElement} Constructed main carousel.
     */
    constructCarousel( container = undefined, translations = {} ) {
        const $container = $( container );
        const carousel = $container.find( '.exhibition-carousel__items--primary' );

        const carouselOptions = {
            prevArrow: $container.find( '.slick-prev' ),
            nextArrow: $container.find( '.slick-next' ),
            customPaging( slider, i ) {
                const dotIcon = '<span class="slick-dot-icon" aria-hidden="true"></span>';
                const srLabel = `<span class="is-sr-only">${ translations.goto } ${ i + 1 }</span>`;
                return $( Common.makeButton( dotIcon + srLabel ) );
            },
            centerMode: true,
            centerPadding: '1rem',
            slidesToShow: 3,
            arrowsPlacement: 'afterSlides',
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                    },
                },
                {
                    breakpoint: 768,
                    centerMode: true,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    },
                },
            ],
        };

        // Start the main carousel.
        carousel.slick( carouselOptions );

        carousel.on( 'setPosition', ( event, slick ) => {
            // Transalate Slick Slider stuff
            this.translateCarousels( translations );
        } );

        return carousel;
    }

    translateCarousels( translations ) {
        $( '.slick-track' ).find( '.slick-slide' ).each( function() {
            const thisElem = $( this );
            let newStr = thisElem.attr( 'aria-label' ).replace( 'slide', translations.slide );

            if ( newStr.includes( 'centered' ) ) {
                newStr = newStr.replace( /\((.*?)\)/g, '' ).trim() + ' (' + translations.centered + ')';
                thisElem.attr( 'aria-label', newStr );
            }

            // Clean up other than .slick-current slide
            if ( ! thisElem.hasClass( 'slick-current' ) ) {
                newStr = newStr.replace( /\((.*?)\)/g, '' ).trim();
            }

            thisElem.attr( 'aria-label', newStr );

        } );

    }

    docReady() {
        this.cache();
        this.initCarousels();
    }
}
