<?php

namespace RY\search_terms;

include_once("includes/base.php");

class SearchTermsSettings{
    private $options;
    
    public function __construct(){
        Utils::defaultOptions();
        $this->options = get_option( "search_terms_settings" );
        $this->register_settings();
        add_action( "admin_head", array( $this, "enqueue_files") );
    }//construct
    
    public function display_page(){
    ?>
    <div class="wrap">
        <h2><?php _e("Search terms cloud settings:"); ?></h2>
        <form method="POST" action="options.php">
            <?php 
                settings_fields( 'search_terms_settings' );
                do_settings_sections(__FILE__);
            ?>
            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="<?php _e( 'Save changes' ); ?>" />
            </p>
        </form>
    </div>
    <?php
    }//display_page
    
    public function register_settings(){
        register_setting( 'search_terms_settings', 'search_terms_settings' );
        add_settings_section( 'search_terms_main_settings', __("Main settings"), function(){}, __FILE__ );
        add_settings_field( 'stc_count', __("Number of terms to display"), array( $this, 'stc_count'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_min_search', __("Min. Number of search"), array( $this, 'stc_min_search'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_min_font', __("Smallest font size"), array( $this, 'stc_min_font'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_max_font', __("Largest font size"), array( $this, 'stc_max_font'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_color', __("Terms color"), array( $this, 'stc_color'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_display_count', __("Display terms count"), array( $this, 'stc_display_count'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_sortby', __("Sort By"), array( $this, 'stc_sortby'), __FILE__, 'search_terms_main_settings' );
        add_settings_field( 'stc_sortby_order', __("Sort order"), array( $this, 'stc_sortby_order'), __FILE__, 'search_terms_main_settings' );

    }//register_settings

    // INPUTS
    public function stc_count(){
         echo "<input type='text' name='search_terms_settings[stc_count]' class='regular-text' value='{$this->options['stc_count']}' />";
    }//stc_count
    
    public function stc_min_search(){
         echo "<input type='text' name='search_terms_settings[stc_min_search]' class='regular-text' value='{$this->options['stc_min_search']}' />";
    }//stc_min_posts
    
    public function stc_min_font(){
         echo "<input type='text' name='search_terms_settings[stc_min_font]' class='regular-text' value='{$this->options['stc_min_font']}' />";
    }//stc_min_font
   
    public function stc_max_font(){
         echo "<input type='text' name='search_terms_settings[stc_max_font]' class='regular-text' value='{$this->options['stc_max_font']}' />";
    }//stc_max_font
    
    public function stc_color(){
         echo "<input type='text' id='terms_color' name='search_terms_settings[stc_color]' class='regular-text' value='{$this->options['stc_color']}' />";
    }//stc_color
    
    public function stc_display_count(){
        $radio = "<input type='radio' name='search_terms_settings[stc_display_count]' value='y' ". checked( $this->options['stc_display_count'],'y', 0) ." />". __("Yes");
        $radio .= "&nbsp;&nbsp;<input type='radio' name='search_terms_settings[stc_display_count]' value='n' ". checked( $this->options['stc_display_count'],'n', 0) ." />". __("No");
        
        echo $radio;
    }//stc_display_count
    
    public function stc_sortby(){
        $select = "<select name='search_terms_settings[stc_sortby]'>";
        $select .= "<option value='term' ". selected( $this->options['stc_sortby'], 'term', 0 ) .">". __("Term") ."</option>";
        $select .= "<option value='count' ". selected( $this->options['stc_sortby'], 'count', 0 ) .">". __("Count") ."</option>";
        $select .= "</select>";
        
        echo $select;
    }//sortby

    public function stc_sortby_order(){
        $radio = "<input type='radio' name='search_terms_settings[stc_sortby_order]' value='asc' ". checked( $this->options['stc_sortby_order'],'asc', 0) ." />". __("Ascending");
        $radio .= "&nbsp;&nbsp;<input type='radio' name='search_terms_settings[stc_sortby_order]' value='desc' ". checked( $this->options['stc_sortby_order'],'desc', 0) ." />". __("Descending");
        
        echo $radio;

    }//sortby_order

    public function enqueue_files(){
        wp_enqueue_script( 'colorpicker-js', plugins_url( "js/colpick.js", __FILE__ ), array( 'jquery-core' ), '', true );
        wp_enqueue_script( 'stc_settings', plugins_url( "js/settings.js", __FILE__ ), array( 'jquery-core', 'colorpicker-js' ), '', true );
        wp_enqueue_style( 'colorpicker-css', plugins_url( "css/colpick.css", __FILE__ ) );
        
    }//enqueu_files
}//class


add_action( "admin_menu",function(){
    add_options_page( __("Search terms cloud"), __("Search terms cloud"), "manage_options", __FILE__, 
        array( 
            "RY\search_terms\SearchTermsSettings", 
            "display_page" 
        ) 
    );
});

add_action( 'admin_init', function(){
    $st=new SearchTermsSettings();
});
