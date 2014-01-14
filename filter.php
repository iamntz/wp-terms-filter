<?php
/*
Plugin Name:Terms Filter
Description: Add a dropdown selector to post listing page to quickly filter existing entries
Author: Ionut Staicu
Author URI: http://iamntz.com
Version: 0.1.0
*/


class NtzTermFilters {
  function __construct() {
    add_action('restrict_manage_posts', array( &$this, 'filter_posts_by_taxonomy' ) );
  }


  public function filter_posts_by_taxonomy(){
   if ( !isset($_GET['post_type'])) { return; }
    $post_type = $_GET['post_type'];
    $taxonomies = get_object_taxonomies( $post_type, 'objects' );


    foreach ( $taxonomies as $taxonomy_name => $taxonomy_properties ) {
      $this->taxonomy_name = $taxonomy_name;
      $this->taxonomy_properties = $taxonomy_properties;

      printf( '<select name="%s">', $taxonomy_name );
      printf( '<option value="">&mdash; %s &mdash;</option>', $taxonomy_properties->labels->name );
        $terms = get_terms( $taxonomy_name, "hide_empty=0&parent=0" );

        $this->loop_terms( $terms );
      echo '</select>';
    }
  }


  protected function loop_terms( $terms, $prefix = '' ){
    if( is_array( $terms ) && count( $terms ) ){
      foreach ( $terms as $term ) {
        printf( '<option value="%s">%s</option>', $term->slug, $prefix.$term->name );
        $this->get_children( $term->term_id );
      }
    }
  }


  protected function get_children( $parent_id ){
    $children = get_term_children( $parent_id, $this->taxonomy_name );
    if( empty( $children ) ){ return; }

    $terms = get_terms( $this->taxonomy_name, array(
      'hide_empty' => false,
      'include' => $children
    ) );

    //  TODO: make it play nice with multiple levels
    $this->loop_terms( $terms, '&ndash; ' );
  }
}

new NtzTermFilters();