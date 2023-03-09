					</div>
				<?php
					get_template_part( 'partials/ads/footer' );

				$mparticle_implementation = sprintf(
			'<script>
						document.body.addEventListener(\'click\', (e) => {
							const ev = window.event||e;
                        	if (ev.srcElement.tagName === \'A\') {
                        		window.beasleyanalytics.setAnalyticsForMParticle(\'container_id\', \'%s\');
                        		window.beasleyanalytics.setAnalyticsForMParticle(\'link_name\', ev.srcElement.ariaLabel);
								window.beasleyanalytics.setAnalyticsForMParticle(\'link_text\', ev.srcElement.innerText);
								window.beasleyanalytics.setAnalyticsForMParticle(\'link_url\', ev.srcElement.href);

								window.beasleyanalytics.sendMParticleEvent(
									BeasleyAnalyticsMParticleProvider.mparticleEventNames.linkClicked,
								);
							}
						});
					</script>',
						'container_id?',
				);
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
