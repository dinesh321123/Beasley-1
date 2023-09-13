<?php

/**
 * OutbrainWidget Class
 *
 * This class defines a WordPress shortcode for displaying an Outbrain widget.
 *
 * @package YourThemeOrPlugin
 */
class OutbrainWidget {

	/**
	 * Constructor method for the OutbrainWidget class.
	 *
	 * This method registers the 'outbrain_widget' shortcode and associates it
	 * with the 'outbrain_widget_shortcode' method of this class.
	 */
	function __construct(){
        add_shortcode('outbrain_widget', array( $this, 'outbrain_widget_shortcode' ));
	}

    /**
     * Shortcode callback method for 'outbrain_widget'.
     *
     * This method generates and returns the HTML for the Outbrain widget.
     *
     * @return string The HTML content for the Outbrain widget.
     */
    public function outbrain_widget_shortcode() {
        
        // Start output buffering to capture the HTML output.
        ob_start();

        if (ee_is_whiz()) {

            echo '<div class="OUTBRAIN" data-src="DROP_PERMALINK_HERE" data-widget-id="AR_1"></div>';
            echo '<script type="text/javascript" async="async" src="//widgets.outbrain.com/outbrain.js"></script>';

        } else {

            // Echo the HTML for the Outbrain widget. In this case, it's a <div> element with the id 'outbrain-react-widget'.
            echo '<div id="outbrain-react-widget" class="outbrain-widget"></div>';
        
        }

        // Get the contents of the output buffer and store it in the $outbrain_widget variable.
        $outbrain_widget = ob_get_clean();

        return $outbrain_widget;
    }
    
}
new OutbrainWidget();