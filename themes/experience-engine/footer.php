					</div>
				<?php
					get_template_part( 'partials/ads/footer' );
				?>
			</main><?php

			if ( ! ee_is_common_mobile() ) :
				get_template_part( 'partials/footer' );
				get_template_part( 'partials/modals' );
				get_template_part( 'partials/configurable-iframe' );
				get_template_part( 'partials/live-player' );
			endif;
		?></div>

		<?php wp_footer(); ?>
	</body>
</html>
