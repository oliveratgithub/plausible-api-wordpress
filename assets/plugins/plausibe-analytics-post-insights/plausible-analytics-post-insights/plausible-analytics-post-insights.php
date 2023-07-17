<?php
/*
 * Plugin Name:			Plausible Analytics API - Post Insights
 * Plugin URI:			https://github.com/oliveratgithub/plausible-api-wordpress
 * Description:			This plugin displays analytics data from Plausible Analytics for a specific post.
 * Version:				1.0.0
 * Requires at least:	6.2
 * Requires PHP:		7.4
 * Author:				oliveratgithub
 * Author URI:			https://github.com/oliveratgithub/
 * Text Domain:			plausible-api
 */

namespace Plausible\Analytics\WP\API\PostInsights;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include 'class-plausible-post-insights.php';

/**
 * Calls the required class.
 */
function init_plausibleAnalyticsPostInsights() {
    new plausibleAnalyticsPostInsights();
}

/** Execute only if logged-in User in the Backend */
if ( is_admin() ) {
    add_action( 'load-post.php', 'Plausible\Analytics\WP\API\PostInsights\init_plausibleAnalyticsPostInsights' );
    // add_action( 'load-post-new.php', 'Plausible\Analytics\WP\API\PostInsights\init_plausibleAnalyticsPostInsights' ); // There won't be any data for a new Post
}
