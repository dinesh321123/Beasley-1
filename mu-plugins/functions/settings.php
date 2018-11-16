<?php

function bbgi_input_field( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'type'    => 'text',
		'name'    => '',
		'default' => '',
		'class'   => 'regular-text',
		'desc'    => '',
	) );

	$value = get_option( $args['name'], $args['default'] );

	printf(
		'<input type="%s" name="%s" class="%s" value="%s">',
		esc_attr( $args['type'] ),
		esc_attr( $args['name'] ),
		esc_attr( $args['class'] ),
		esc_attr( $value )
	);

	if ( ! empty( $args['desc'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
	}
}
