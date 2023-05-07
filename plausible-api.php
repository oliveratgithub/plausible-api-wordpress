<?php
/*
 * Plugin Name:			Plausible Analytics API
 * Plugin URI:			https://plausible.io/docs/stats-api
 * Description:			This plugin adds direct access to the Plausible Analytics API.
 * Version:				1.0.0
 * Requires at least:	6.2
 * Requires PHP:		8.0
 * Author:				oliveratgithub
 * Author URI:			https://github.com/oliveratgithub/
 * Text Domain:			plausible-api
 * Domain Path:			/languages
 */

namespace Plausible\Analytics\WP\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define some constants
 *
 * @const PLAUSIBLE_API_PLUGIN_DIR This Plugin's root directory
 * @const PLAUSIBLE_API_PLUGIN_FILE The Plugin's main plugin file
 * @const PLAUSIBLE_API_VERSION Current plugin version using SemVer - https://semver.org
 * @const PLAUSIBLE_API_PLUGIN_BASENAME The Plugin's basename
 * @const PLAUSIBLE_API_PLUGIN_URL The Plugin's directory URL
 * @const PLAUSIBLE_API_APIBASEURL The Base URL to the Plausible Analytics API
 * @const PLAUSIBLE_API_APIVERSION The API version to use
 * @const PLAUSIBLE_API_APIENDPOINTS Supported API endpoints (simplified name to access their API path)
 * @const PLAUSIBLE_API_APITIMEOUT Request Header timeout
 * @const PLAUSIBLE_API_APITOKEN The API Bearer Token to use from the `token.php` file
 */
if ( ! defined( 'PLAUSIBLE_API_PLUGIN_DIR' ) ) define( 'PLAUSIBLE_API_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'PLAUSIBLE_API_PLUGIN_FILE' ) ) define( 'PLAUSIBLE_API_PLUGIN_FILE', plugin_dir_path( __FILE__ ) . 'plausible-api.php' );
if ( ! defined( 'PLAUSIBLE_API_VERSION' ) ) define( 'PLAUSIBLE_API_VERSION', '1.0.0' );
if ( ! defined( 'PLAUSIBLE_API_PLUGIN_BASENAME' ) ) define( 'PLAUSIBLE_API_PLUGIN_BASENAME', plugin_basename( PLAUSIBLE_API_PLUGIN_FILE ) );
if ( ! defined( 'PLAUSIBLE_API_PLUGIN_URL' ) ) define( 'PLAUSIBLE_API_PLUGIN_URL', plugin_dir_url( PLAUSIBLE_API_PLUGIN_FILE ) );
if ( ! defined( 'PLAUSIBLE_API_APIBASEURL' ) ) define( 'PLAUSIBLE_API_APIBASEURL', 'https://plausible.io/api' );
if ( ! defined( 'PLAUSIBLE_API_APIVERSION' ) ) define( 'PLAUSIBLE_API_APIVERSION', 1 );
if ( ! defined( 'PLAUSIBLE_API_APIENDPOINTS' ) ) define( 'PLAUSIBLE_API_APIENDPOINTS', [
									                                             'realtime' => '/stats/realtime/visitors'
									                                            ,'aggregate' => '/stats/aggregate'
									                                            ,'series' => '/stats/timeseries'
									                                            ,'breakdown' => '/stats/breakdown'
									                                            ,'event' => '/event'
									                                            ,'links' => '/sites/shared-links'
									                                            ,'goals' => '/sites/goals'
								                                            ] );
if ( ! defined( 'PLAUSIBLE_API_APITIMEOUT' ) ) define( 'PLAUSIBLE_API_APITIMEOUT', 5 );
if ( ! defined( 'PLAUSIBLE_API_APITOKEN' ) ) define( 'PLAUSIBLE_API_APITOKEN', '' );


/**
 * Stats API
 *
 * @since 1.0
 */
//require_once PLAUSIBLE_API_PLUGIN_DIR . 'stats.php';
require_once __DIR__ . '/stats.php';
$PlausibleStatsAPI = new Stats();
