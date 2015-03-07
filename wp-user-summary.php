<?php
/*
Plugin Name: WP User Summary
Plugin URI: http://www.learn24bd.com/
Version: 1.01
Author: Harun
Description: WP User summary is a useful plugin for your site. This plugin will help every member/user of your site to see their summary of post,user information and more.
License:GPL2
*/
add_action('wp_dashboard_setup','register_wus_dashboard_widget');
function register_wus_dashboard_widget() {
    wp_add_dashboard_widget('wus_dashboard_widget', 'User Status', 'wus_dashboard_widget_display');
}

function wus_totalCommentFromUser($userID) {
    global $wpdb;
    $where = 'WHERE comment_approved = 1 AND user_id = ' . $userID;
    $comment_count = $wpdb->get_var("SELECT COUNT( * ) AS total FROM {$wpdb->comments}{$where}");
    return $comment_count;
}

function wus_Get_Date_Difference($start_date, $end_date)
{

    $diff = abs(strtotime($end_date) - strtotime($start_date));
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
 	return $years.' Years '.$months.' Month '.$days.' Days';
}
 
function wus_userPosts($userID) {
    query_posts('author=' . $userID . '&showposts=5');
    while (have_posts()):
        the_post();
        echo "<a href=" . get_permalink() . ">" . get_the_title() . "</a>   In ";
        foreach ((get_the_category()) as $cat) {
            echo "<i>" . $cat->cat_name . ' ' . "</i></br>";
        };
    endwhile;
}

function wus_userPopularPosts($userID) {
    global $wpdb;
    $pop = $wpdb->get_results("SELECT id, post_title, comment_count FROM {$wpdb->prefix}posts WHERE post_type='post' AND post_author={$userID} ORDER BY comment_count DESC LIMIT 5");
    foreach ($pop as $post):
        echo "<a href=" . get_permalink($post->id) . ">" . $post->post_title . "</a> (" . $post->comment_count . ")   In ";
        foreach ((get_the_category($post->id)) as $cat) {
            echo "<i>" . $cat->cat_name . ' ' . "</i></br>";
        };
    endforeach;
}

function wus_dashboard_widget_display() {
    $current_user = wp_get_current_user();
    
    //var_dump($current_user);
    $userID = $current_user->ID;
    $output = "<p><span class='dashicons dashicons-welcome-learn-more'></span> Hello,<strong>" . strtoupper($current_user->user_nicename) . "</strong></p>";
    $output.= "<span class='dashicons dashicons-admin-users'></span> " . implode(', ', $current_user->roles);
    $output.= "<table class='widefat fixed'><tr><th><strong><span class='dashicons dashicons-admin-post'></span> Post</strong></th><th><strong><span class='dashicons dashicons-admin-comments'></span> Comment</strong></th><th><strong><span class='dashicons dashicons-awards'></span> Experience</strong></th></tr>";
    $output.= "<tr>";
    $output.= "<td>";
    $output.= "<p>" . count_user_posts($userID, 'post');
    $output.= "</td>";
    $output.= "<td>";
    $output.= "<p>" . wus_totalCommentFromUser($userID) . "</p>";
    $output.= "</td>";
    $output.= "<td>";
    $output.= "<p>" . wus_Get_Date_Difference(date("d-m-Y", strtotime($current_user->user_registered)),date("d-m-Y")). '' . "</p>";
    $output.= "</td>";
    $output.= "<p><span class='dashicons dashicons-clock'></span> Join at : " . date("d-m-Y", strtotime($current_user->user_registered)). "</p>";
    $output.= "<tr>";
    $output.= "</table>";
    echo $output;
    echo "<table class='widefat fixed'><tr><th><span class='dashicons dashicons-format-aside'></span> Recent Post</th><th><span class='dashicons dashicons-chart-area'></span> Top Post</th></tr>";
    echo "<tr>";
    echo "<td>";
    wus_userPosts($userID);
    echo "</td>";
    echo "<td>";
    wus_userPopularPosts($userID);
    echo "</td>";
    echo "</tr>";
    echo "</table>";
}?>