<?php
/*
 *  File containing some utils functions.
 */


namespace RY\search_terms;

class Utils{
    /*
     *   Truncate a string and add a specified ellipsis at the end.
     *   
     *   @param string The string to work with
     *   @param length Number of chars to truncate
     *   @param ellipsis string to add at the end, default "..."
     *
     *   @return Resulted string
     */
    function truncate( $string, $length, $ellipsis = "" ){
        if( strlen($string) <= $length )
            return $string;
        else
            return substr( $string, 0, $length).$ellipsis;
    }//truncate

    /*
     *   Convert an Array of stdObject to an associative Array
     *   
     *   @param array Array of sdtObjects. 
     *
     *   @return An associative Array
     */
    function objectToArray($array) {
        if (is_object($array)) {
            $array = get_object_vars($array);
        }

        if (is_array($array)) {
            return array_map("RY\search_terms\Utils::objectToArray", $array);
        }
        else {
            return $array;
        }
    }//objectToArray
    
    /*
     * Default options if not found 
     * 
     * @return options array
     */

    function defaultOptions(){
        $options = array( 
            "stc_count" => 15, 
            "stc_min_search" => 1, 
            "stc_min_font" => 12, 
            "stc_max_font" => 30, 
            "stc_color" => "#0F3754", 
            "stc_display_count" => "n", 
            "stc_sortby" => "term", 
            "stc_sortby_order" => "desc" 
        );
        update_option( 'search_terms_settings', $options );
        
        return $options;
    }//defaultOptions
}//class
