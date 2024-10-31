<?php

namespace RY\search_terms;

include_once("includes/array_column.php");
include_once("includes/base.php");

class SearchTermsWidget extends \WP_Widget {
    private $options;

    public function __construct(){
        $this->options = get_option( 'search_terms_settings' );
        //localization
        add_action( "init", function(){
            load_plugin_textdomain( "search_terms_widget", false, plugin_dir_path(__FILE__)."/lang/" );
        });

        parent::__construct(
            'search_terms_widget',
            __( 'Search Terms Cloud', 'search_terms_widget' ),
            array(
                'classname'    => 'search_terms_widget',
                'description'  => 'Create a search terms cloud widget on your website sidebar'
            )
        );
    }//construct

    public function form( $instance ){
        extract( (array)$instance );
        //include_once( plugin_dir_path(__FILE__)."/views/admin.php" );
        ?> 
        <div class="search_terms_cloud">
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title");?></label>
            <input 
            type="text" 
            class="widefat" 
            id="<?php echo $this->get_field_id('title'); ?>" 
            name="<?php echo $this->get_field_name('title'); ?>" 
            value="<?php echo (isset($title) ? esc_attr($title) : ''); ?>"
            />
            <b><?php _e( 'You can costumize the plugin by using the settings panel (Settings > Search terms cloud)' ); ?></b>
        </p>
        </div>
        <?php
    }//form

    public function widget( $args, $instance ){
        if( ! $this->options ){
            $this->options = Utils::defaultOptions();
        }

        extract( $args );
        extract( $instance );
        extract( $this->options );
        
        $stq = new SearchTermsCloudQuery();
        $terms = $stq->getTerms( $stc_count, $stc_min_search, $stc_sortby, $stc_sortby_order );//count=0 mean all
        //test for an empty terms set
        if( count($terms) == 0 ){
            $print = "<div>";
            $print .= "<b>". __( "No search terms available" ) ."</b>";
            $print .= "</div>";
            echo $before_widget;
            echo $before_title . $title . $after_title;
            echo $print;
            echo $after_widget;

            return;
        }//if
        
        $counts = array_column( Utils::objectToArray( $terms ), "count" );//get an array of counts
        $max_count = max( $counts );
        $min_count = min( $counts );
        
        $print = "<div>";
        //display count search term or not
        foreach( $terms as $term ) {
            //calculate font_size
            $font_size = ( ( $stc_max_font * $term->count ) /  $max_count );
            //test if we passed the minimum font_size specified in settings
            $font_size = ( $font_size < $stc_min_font ) ? $stc_min_font : $font_size;
            $url = site_url() . "/?s=" . $term->term;
            //truncate the string if it's too long
            $truncate = Utils::truncate( $term->term, 15, "...");
            $print .= "<a href='$url' title='$term->term' style='color:#$stc_color;font-size:".$font_size."px'>$truncate</a>". ( ($stc_display_count == 'y')? ( "<span>(". $term->count .")</span>" ) : "") ;
        }//foreach

        $print .= "</div>";

        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo $print;
        echo $after_widget;
    }//widget

    public function enqueue_files(){
        wp_enqueue_style( 'stc-css', plugins_url( "css/style.css", __FILE__ ));
    }//enqueue_file 
}//class

add_action( "widgets_init", function(){
    register_widget("RY\search_terms\SearchTermsWidget");
});

add_action( 'wp_enqueue_scripts', array( "RY\search_terms\SearchTermsWidget", "enqueue_files") );
