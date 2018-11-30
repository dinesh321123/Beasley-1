<div id="post-<?php the_ID(); ?>" <?php post_class( array( 'type-contest', 'contest-tile', '-horizontal', '-full-width' ) ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<?php get_template_part( 'partials/tile/title' ); ?>
	<div class="meta">
		<?php ee_the_contest_dates(); ?>
	</div>
	<p class="type">
		<svg width="14" height="11" viewBox="0 0 14 11" xmlns="http://www.w3.org/2000/svg" fill="var(--brand-primary)">
			<path d="M11.222 0.680142C11.2311 0.265229 11.2148 0.00306369 11.2148 0.00306369L7.02128 0H7H6.97872L2.78479 0.00306369C2.78479 0.00306369 2.76895 0.265229 2.778 0.680142H0V1.12307C0 1.22417 0.0176539 3.60904 1.53996 4.91549C2.17505 5.46039 2.96721 5.73437 3.90106 5.73481C4.04229 5.73481 4.1876 5.72562 4.33471 5.71336C4.86524 6.41583 5.47814 6.90777 6.16755 7.15287V9.09832H4.14369V10.3168H3.4751V11H6.97872H7.02128H10.5249V10.3172H9.85586V9.09875H7.832V7.1533C8.52095 6.90821 9.13431 6.41627 9.66483 5.7138C9.81286 5.72606 9.95816 5.73481 10.0994 5.73481C11.0328 5.73394 11.8249 5.46039 12.46 4.91505C13.9823 3.6086 14 1.22373 14 1.12263V0.680142H11.222ZM2.15106 4.25504C1.28149 3.511 1.02438 2.23738 0.948332 1.56599H2.83051C2.91018 2.40238 3.09758 3.44447 3.52988 4.36052C3.60909 4.52859 3.69238 4.6879 3.77748 4.84371C3.12882 4.81964 2.5829 4.624 2.15106 4.25504ZM11.8489 4.25504C11.4176 4.62444 10.8712 4.81964 10.223 4.84371C10.3081 4.68834 10.3914 4.52859 10.4706 4.36052C10.9029 3.44447 11.0903 2.40238 11.1695 1.56599H13.0517C12.9756 2.23694 12.719 3.51056 11.8489 4.25504Z" />
		</svg>
		contest
	</p>
</div>
