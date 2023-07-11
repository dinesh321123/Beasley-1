				<?php
					$mparticle_register_embeds_in_observer = sprintf(
					'<script class="mparticle_implementation">
						const handleOnLoadForMParticle = () => {
							const contentElement = document.getElementById(\'content\');
							if (contentElement) {
								window.beasleyanalytics.setNewsletterControlForMParticleAccount(contentElement);
								window.beasleyanalytics.fireLazyMParticlePageViewsForElementsWithMeta(contentElement.getElementsByTagName(\'mparticle-meta\'));
							}
                            removeEventListener("DOMContentLoaded", handleOnLoadForMParticle);
						}

						if (document.readyState !== \'complete\') {
							addEventListener(\'DOMContentLoaded\', handleOnLoadForMParticle);
						} else {
							handleOnLoadForMParticle();
						}
					</script>'
					);
					echo $mparticle_register_embeds_in_observer;
				?>
					</div>
				<?php
					get_template_part( 'partials/ads/footer' );

				$mparticle_implementation = '<script>
						document.body.addEventListener(\'click\', (e) => {
							const ev = window.event||e;
                        	window.beasleyanalytics.sendMParticleLinkClickEvent(ev.target);
						});
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
