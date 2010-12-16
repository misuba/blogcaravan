<?php
/*
Plugin Name: Blogcaravan
Plugin URI: http://gibberish.com/hacks/wp/blogcaravan/
Description: Show the posts on your home page, and date and category archives, in the order in which they've been commented on (newest first). Posts with no comments will be merged into the order by their post date.
Version: 0.1
Author: Mike Sugarbaker
Author URI: http://gibberish.com/
License: http://creativecommons.org/licenses/GPL/2.0/
*/

function bcv_test($wp_q) {
  print_r($wp_q);
  return $wp_q;
}


function bcv_fields($fields) {
  global $wpdb;
  if (is_home() || is_archive() || is_category()) {
    return "$wpdb->posts.*
      , cd.max_comment_date
      , cast((case when cd.comment_post_ID is null then $wpdb->posts.post_date else cd.max_comment_date end) as datetime) as order_by_date";
  }
  return $fields;
}

function bcv_join($join) {
  global $wpdb;
  if (is_home() || is_archive() || is_category()) {
    return "left outer join 
	  (
        select c.comment_post_ID
          , max(c.comment_date) as max_comment_date
        from $wpdb->comments c 
        group by c.comment_post_ID
      ) cd
      on $wpdb->posts.id = cd.comment_post_ID
    ";
  }
  return $join;
}

function bcv_order($orderby) {
  if (is_home() || is_archive() || is_category()) {
    return "order_by_date desc";
  }
  return $orderby;
}

add_filter('posts_fields', 'bcv_fields');
add_filter('posts_join', 'bcv_join');
add_filter('posts_orderby', 'bcv_order');

// add_filter('posts_request','bcv_test');

?>