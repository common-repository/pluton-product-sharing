/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	wp.customize( 'pps_sharing_borders_color', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce div.product .entry-share,.woocommerce div.product .entry-share ul li' ).css( 'border-color', to );
		} );
	} );
	wp.customize( 'pps_sharing_icons_bg', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce div.product .entry-share ul li a .fa' ).css( 'background-color', to );
		} );
	} );
	wp.customize( 'pps_sharing_icons_color', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce div.product .entry-share ul li a .fa' ).css( 'color', to );
		} );
	} );
} )( jQuery );
