<?php
/**
 * Created by Eduard
 * Date: 20.11.2014
 */

$options = self::get_keyword_options( $this::$plugin_slug . '_option_name' );
?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
				<table class="form-table keywords widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th scope="col" class="manage-column sortable desc">
								<span>Keyword</span>
								<th scope="col" class="manage-column sortable desc">
									<span>Linked Content</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if( !empty( $options ) ):
								$i = 0;
							foreach ( $options as $key => $value ): ?>

							<tr <?php echo $i%2 == 0 ? 'class="alternate"' : ''; $i++; ?> >
								<td>
									<?php echo esc_html( $value['keyword'] ); ?>
									<div class="row-actions">
										<span class="delete">
											<a data-postid="<?php echo esc_attr( $value['post_id'] ); ?>" class="submitdelete" href="#">delete</a>
										</span>
									</div>
								</td>
								<td><?php echo esc_html( get_the_title( $value['post_id'] ) ); ?></td>
							</tr>
							<?php endforeach;
							endif;
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="col-left">
				<div class="col-wrap">
					<div class="form-wrap">
						<form method="post" action="">
							<?php wp_nonce_field( 'save_new_keyword', 'save_new_keyword' ); ?>

							<h3 class="title">Add new keyword</h3>
							<div class="form-field form-required term-name-wrap">
								<label for="tag-name">Keyword</label>
								<input type="text" id="keyword" name="keyword" size="25" class="regular-text" aria-required="true"/>
							</div>
							<div class="form-field form-required term-name-wrap">
								<label for="tag-name">Linked Post</label>
								<p>
								<div class="post-search">
									<p>Search for content or choose from the most recent</p>
										<input type="text" id="linked_content_search" size="25" autocomplete="off" class="post-search__search-field regular-text" />
										<span class="spinner"></span>

									<ul class="post-search__list regular-text"></ul>

									<script type="text/template" class="post-search__list-item-template">
									<li class="post-search__list-item">
									<input class="post-search__list-item-input" type="radio" name="linked_content" value="<%= id %>" id="linked_content_item_<%= id %>" />
									<label for="linked_content_item_<%= id %>">
									<span class="linked_content_label"><%= title %></span>
									<span class="post_type"><%= post_type %></span>
									</label>
									</li>
									</script>

								</div>
								</p>
							</div>
								<p class="submit">
									<input type="submit" value="Add Keyword" class="button button-primary"/>
									<input type="hidden" name="save_keyword_settings" value="Y" />
								</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>