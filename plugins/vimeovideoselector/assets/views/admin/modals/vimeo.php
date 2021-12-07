<?php
/**
 * Modal view/template for the Vimeo.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
require_once( __DIR__ . '/../../../../vendor/autoload.php' );
use Vimeo\Vimeo;
use VimeoVideoSelector\Models\Settings; ?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">
<script type="text/html" id="tmpl-vimeovideoselector-vimeo">
<!--    <header id="vimeovideoselector-header">
        <div class="vimeovideoselector-container">
            <div class="headerImage">
                <img class="vimeovideoselector-header-background-image" src="<?php echo VVPS_PLAYER_SELECTOR_URL; ?>/assets/images/header.jpg"/>
            </div>
            <div class="headerImage overlay">
                <h1>Vimeo Video<br/></h1>
                <span class="vimeovideoselector-header-subtext">Digital Video Solutions</span>
            </div>
        </div>
    </header> -->
    <section id="vimeovideoselector-content">
    	<div>
			<div class="content search-form">
				<img class="vimeovideoselector-header-background-image" src="<?php echo VVPS_PLAYER_SELECTOR_URL; ?>/assets/images/vimeo-header-img.png"/>
				<form>

					<input type="text" id="vimeo_search_input" autocomplete="off" name="search">
					<button type="submit" onclick="vimeo_search_callback('search'); return false;" id="search_btn" name="submit" value="Submit">
					<i class="fa fa-search" aria-hidden="true"></i>
					</button>
					<?php /* ?><input type="submit" onclick="vimeo_search_callback('search'); return false;" id="search_btn" name="submit" value="Submit">
					<input type="submit" style="display: none;" onclick="vimeo_search_callback('clear'); return false;" id="search_btn_reset" name="search_btn_reset" value="Clear">
					<?php */?>

					<button type="submit" style="display: none;" onclick="vimeo_search_callback('clear'); return false;" id="search_btn_reset" name="search_btn_reset" value="Clear">
					<i class="fa fa-fw fa-times text-gray-dark"></i>
					</button>
				</form>
			</div>
        </div>
        <div id="stories-scroll-container" style="display:none;">
			<div id="stories-container"></div>
			<div id="stories-load-more" class="stories-load-more load-more-shown">
				<div id="stories-load-more-spinner" class="spinner-container">
					<div class="stories-spinner"></div>
				</div>
			</div>
		</div>
        <div class="vimeo_video_list">
        <?php
        $settings = Settings::find();
        $client_id = $settings->client_id;
        $client_secret = $settings->client_secret;
        $access_token = $settings->access_token;

        // Check settings exist.
        if ( ! empty( $settings ) && !empty($client_id) && !empty($client_secret))
        {
            // Retrieve CLIENT ID , CLIENT SECRET & ACCESS TOKEN from settings.
            $channel = $settings->channel;
            $client = new Vimeo($client_id,$client_secret,$access_token);
            $per_page = '12';
            $page = '1';
            $response = $client->request("/channels/$channel/videos", array('per_page' => $per_page, 'page' => $page));
            $response_body = wp_remote_retrieve_body( $response );
            if (empty($response_body['error']))
            {
                $total_record = $response_body['total'];
                $total_page = ceil($total_record / $per_page);
                echo '<input type="hidden" max_page="'.$total_page.'" current_page="1" name="pagination_data">';
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
                echo '</div>';
                echo '<ul class="pagination">';
                for($i=1; $i <= $total_page; $i++){
                    echo '<li><a class="nav_page '.(($page == $i) ? 'active' : '').'" data-page="all" data-page_id="'.$i.'">'.$i.'</a></li>';
                }
                echo '</ul>';
            }
            else
            {
                _e($response_body['developer_message']);
            }
        } ?>
        </div>
    </section>
</script>
