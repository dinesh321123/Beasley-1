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

function bbgi_select_field( $args ) {
	$args = wp_parse_args( $args, array(
		'name'    => '',
		'default' => '',
		'class'   => 'regular-text',
		'desc'    => '',
		'options' => array(),
	) );

	$value = get_option( $args['name'], $args['default'] );

	printf( '<select name="%s" class="%s">', esc_attr( $args['name'] ), esc_attr( $args['class'] ) );
		foreach ( $args['options'] as $key => $label ) :
			printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), esc_html( $label ) );
		endforeach;
	print( '</select>' );

	if ( ! empty( $args['desc'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
	}
}

function bbgi_image_field( $args ) {
	static $js_rendered = false;

	$args = wp_parse_args( $args, array(
		'name' => '',
	) );

	$image_src = false;
	$image_id = get_option( $args['name'] );
	if ( $image_id ) {
		$image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$image_src = is_array( $image_src ) ? reset( $image_src ): '';
	}

	?><div class="image-select-parent">
		<input type="hidden" class="image-id-input" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo intval( $image_id ); ?>">
		<div>
			<img src="<?php echo esc_url( $image_src ); ?>"/>
		</div>
		<div>
			<div class="button select-image">Select Image</div>
			<div class="button remove-image">Remove Image</div>
		</div>
	</div><?php

	if ( ! $js_rendered ) {
		$js_rendered = true;
		wp_enqueue_media();

		?><script>
			(function($) {
				var $body = $('body');

				$body.on( 'click', '.select-image', function(e) {
					var $this = $(this),
						$parent = $this.parents('.image-select-parent').first(),
						$image = $parent.find('img'),
						$field = $parent.find('.image-id-input'),
						frame;

					e.preventDefault();

					frame = wp.media.frames.chooseImage = wp.media({
						title: 'Choose an Image',
						library: { type: 'image' },
						button: { text: 'Select Image' }
					});

					frame.on( 'select', function() {
						var attachment = frame.state().get('selection').first(),
							sizes = attachment.get('sizes'),
							imageUrl = attachment.get('url');

						if ( "undefined" !== typeof sizes.thumbnail ) {
							imageUrl = sizes.thumbnail.url;
						}

						$field.attr('value', attachment.id);
						$image.attr('src', imageUrl);
					});

					frame.open();
				});

				$body.on( 'click', '.remove-image', function(e) {
					var $this = $(this),
						$parent = $this.parents('.image-select-parent').first(),
						$image = $parent.find('img'),
						$field = $parent.find('.image-id-input');

					e.preventDefault();

					$image.attr('src', '');
					$field.attr('value', '');
				});
			})(jQuery);
		</script><?php
	}
}

function bbgi_settings_section_info( $message ) {
	return function() use ( $message ) {
		echo '<p>' . esc_html( $message ) . '</p>';
	};
}
