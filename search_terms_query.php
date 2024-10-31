<?php
/*
Plugin Name: Search_terms_cloud
Plugin URI: http://github.com/Whyounes/search_terms_cloud
Description: Create a search terms cloud widget on your website sidebar
Version: 0.1.0
Author: RAFIE Younes
Author URI: http://younesrafie.com
License: GPLv2 or later
*/

namespace RY\search_terms;

//Debuging
ini_set("display_errors",1);
error_reporting(E_ALL);


class SearchTermsCloudQuery{
    private $table_name;
    
    public function __construct(){
        global $wpdb;
        $this->table_name = $wpdb->prefix."search_terms";
    }//construct
    
    public function clearTable(){
        global $wpdb;
        
        $wpdb->query( "DELETE FROM $this->table_name WHERE 1" );
    }//clearTable
    
    /**
     * Create search terms table on the database if not exists
     */
    public function createTable(){
        global $wpdb;
                
        $query="CREATE TABLE IF NOT EXISTS `$this->table_name` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
                `term` text COLLATE utf8_unicode_ci NOT NULL,
                  `count` int(11) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
        $wpdb->query( $query );
    }//createTable
    
    /**
     * Delete the search table from the database
     */
    public function deleteTable(){
        $query="DROP TABLE $this->table_name";
        $wpdb->query( $query );
    }//deleteTable
    
    /**
     * Update the database or insert the new term
     *
     * @param string $term the new term to be added
     */
    public function updateTerm( $term ){
        global $wpdb;
        //if the term already exists we just increment the 'count' else we add a new record
        $query = $wpdb->prepare( "SELECT id FROM $this->table_name WHERE term='%s' LIMIT 1", $term );
        $term_id = $wpdb->get_var( $query );
        if( $term_id ){
            $query = $wpdb->query( "UPDATE $this->table_name SET count=count+1 WHERE id='$term_id'" );
        }else{
            $res=$wpdb->insert(
                $this->table_name,
                array(
                    'term'  => $term,
                    'count' => 1
                ),
                array(
                    '%s',
                    '%d'
                )
            );
        }//else    
    }//end function

    /**
     * Get the list of available terms
     *
     * @param $count limit the return result, 0 to get all records
     * @param $min (optional) set the minimum of times thata term must have to be returned,
     *
     * @return array(Object) return an array of objects containing the term and how many time it was searched
     */
    public function getTerms( $count, $min = 0 , $sortby = '' , $order){
        global $wpdb;
        
        $query_count = ( (int)$count > 0 ) ? ("LIMIT $count") : "";
        $query_min = ( (int)$min > 0 ) ? ("WHERE count>=$min") : "";
        $query_order = ( $order == "" || ( $order != "asc" && $order != "desc") ) ? "" : $order;
        $query_sortby = ( $sortby == "" || ($sortby != "count" && $sortby != "term") ) ? "" : " ORDER BY $sortby $query_order";
        
        $query="SELECT term,count FROM $this->table_name $query_min $query_sortby $query_count";
        $terms=$wpdb->get_results($query);
        
        return $terms;
    }//end function
}//end class

include_once("search_terms_settings.php");
include_once("search_terms_widget.php");


//update terms table every time we do a search
add_action( "pre_get_posts" , function( $query ){
    if( ! $query->is_search ) return;
    $search_term=$query->query_vars["s"];
    
    if( ! empty( $search_term ) ){
        $stc=new SearchTermsCloudQuery();
        $stc->updateTerm( $search_term );
    }//if
});

//activation hook
register_activation_hook( __FILE__ , function(){
    $options = get_option( 'search_terms_settings' );
    if( ! $options ){
        $options = Utils::defaultOptions();
    }

    $stc=new SearchTermsCloudQuery();
    $stc->createTable();
});

//uninstall hook
/*register_uninstall_hook( __FILE__, "uninstall");

function uninstall(){
    $stc=new SearchTermsCloudQuery();
    $stc->deleteTable();
}//uninstall
 */
