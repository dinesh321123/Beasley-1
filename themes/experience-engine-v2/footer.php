				<?php
					$mparticle_register_embeds_in_observer = sprintf(
					'<script class="mparticle_implementation">
						if (window.beasleyanalytics) {
							setTimeout(() => {
								const contentElement = document.getElementById(\'content\');
								if (contentElement) {
									console.log(\'Calling mparticle_implementation of Embeds \');
									window.beasleyanalytics.fireLazyMParticlePageViewsForElementsWithMeta(contentElement.getElementsByTagName(\'mparticle-meta\'));
								}
							}, 2500);
						}
					</script>'
					);
					echo $mparticle_register_embeds_in_observer;
				?>
					</div>
				<?php
					get_template_part( 'partials/ads/footer' );

				$mparticle_implementation = '<script>
						if (window.beasleyanalytics) {
							document.body.addEventListener(\'click\', (e) => {
								const ev = window.event||e;
								window.beasleyanalytics.sendMParticleLinkClickEvent(ev.target);
							});
                        }
					</script>';
				echo $mparticle_implementation;
				?>
			</main><?php

			if ( ! ee_is_common_mobile() ) :
				get_template_part( 'partials/footer' );
				get_template_part( 'partials/modals' );
				get_template_part( 'partials/configurable-iframe' );
			endif;
		?></div>

		<?php wp_footer(); ?>
	</body>
</html>
