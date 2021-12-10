<?php

namespace VimeoVideoSelector\Controllers;

require_once( __DIR__ . '/../../vendor/autoload.php' );
use Vimeo\Vimeo;
use WPMVC\MVC\Controller;
use VimeoVideoSelector\Models\Settings;

/**
 * ModalController controller to handle the Vimeo Video Player Selector popup modal.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
class ModalController extends Controller
{
    /**
     * Passes a toggle via localize to enable the Video Library view.
     * @since 0.6.0
     *
     * @hook admin_enqueue_scripts
     */
    public function enable_library()
    {
        // Use localize to enable the Video Library.
        wp_localize_script( 'vimeovideoselector-modal', 'vimeovideoselector_model_i18n', array( 'enable_library' => true ) );
    }

    /**
     * Enqueues the necessary modal scripts/styles.
     * @since 0.2.0
     * @since 0.3.0 Replaced Thickbox completely with the Media Modal.
     *
     * @hook admin_enqueue_scripts
     */
    public function enqueue()
    {
        // Enqueue media scripts and widgets.
        wp_enqueue_media();
        wp_enqueue_script( 'media-widgets' );
        wp_enqueue_style( 'vimeovideoselector-stn' );
        wp_enqueue_style( 'vimeovideoselector-stories' );
        wp_enqueue_script( 'vimeovideoselector-modal' );
    }

    /**
     * Renders the Vimeo template.
     * @since 0.3.0
     *
     * @hook admin_footer-{context}
     */
    public function render_vimeo_template()
    {
        // Render the popup modal skeleton for the Vimeo tab.
        $this->view->show( 'admin.modals.vimeo', [ 'enable_library' => true ] );
    }

    /**
     * Renders the help template.
     * @since 0.3.0
     *
     * @hook admin_footer-{context}
     */
    public function render_help_template()
    {
        // Render the popup modal skeleton for the Help tab.
        $this->view->show( 'admin.modals.help' );
    }

    public function render_vimeo_search()
    {
        $settings = Settings::find();
        // Check settings exist.
        if ( ! empty( $settings ) )
        {
            // Retrieve CLIENT ID , CLIENT SECRET & ACCESS TOKEN from settings.
            $client_id = $settings->client_id;
            $client_secret = $settings->client_secret;
            $access_token = $settings->access_token;
            $channel = $settings->channel;
            $client = new Vimeo($client_id,$client_secret,$access_token);
            $per_page = '12';
            $page = (isset($_POST['pg'])) ? sanitize_text_field($_POST['pg']) : '1';
            $args = array('per_page' => $per_page, 'page' => $page);
            if(!empty($_POST['search'])){
                $args['query'] = sanitize_text_field($_POST['search']);
            }
            // $response = $client->request("/channels/$channel/videos", $args, 'GET');
            $response = $client->request("/users/$channel/videos", $args, 'GET');
            $response_body = wp_remote_retrieve_body( $response );
            if (empty($response_body['error']))
            {
                $total_record = $response_body['total'];
                if($total_record > 0){
					$total_page = ceil($total_record / $per_page);
					//  Baymen Project Gulbransen
					echo '<div class="embed_aria video-row">';
					foreach($response_body['data'] as $vimeoVideo){
						echo '<div class="video-col">';
						$video_id = (int) substr(parse_url($vimeoVideo['link'], PHP_URL_PATH), 1);
						echo $vimeoVideo['embed']['html'];
						echo '<div class="vimeo-embed-data">';
						echo '<div class="data"><p class="title">'.$vimeoVideo['name'].'</p><p class="meta">from <strong>'.$vimeoVideo['user']['name'].'</strong></p></div>';
						// echo '<span class="insert-video" data-video_id="'.$video_id.'">Insert Video <i class="fa fa-code" style="opacity: 0.8"></i></span>';
						// echo '<i data-video_id="'.$video_id.'" class="insert-video fa fa-code" style="opacity: 0.8"></i>';
						echo '<img data-video_id="'.$video_id.'" class="insert-video vimeovideoselector-header-background-image" src="'.VVPS_PLAYER_SELECTOR_URL.'/assets/images/embed-vimeo-video-download-icon.png"/>';

						echo '</div>';
						echo '</div>';
					}
					echo '</div>';	// End embed_aria video-row
					echo '<input type="hidden" max_page="'.$total_page.'" current_page="'.$page.'" name="pagination_data">';
					echo '<ul class="pagination">';
					for($i=1; $i <= $total_page; $i++){
						echo '<li><a class="nav_page '.(($page == $i) ? 'active' : '').'" data-page="all" data-page_id="'.$i.'">'.$i.'</a></li>';
					}
					echo '</ul>';
                } else {
                	echo '<p class="no-video-found" style="padding: 1em;">No results found'.((!empty($_POST['search'])) ? ' for <b>'.$args['query'].'</b>' : '').'</p>';
                }
            }
            else
            {
            	echo '<p class="no-video-found" style="padding: 1em;">'.__($response_body['error']).'</p>';
            }
        }
        exit();
    }
}
