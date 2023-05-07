<?php
/**
 * Plausible Analytics API | Helpers.
 *
 * @since 1.0
 *
 * @package    WordPress
 * @subpackage Plausible Analytics API
 */

namespace Plausible\Analytics\WP\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Helper Functions
 */
class Helpers {
	/**
	 * Get Plain Domain.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string
	 */
	private function get_domain() {
		$site_url = str_replace( '.local', '.ch', site_url() );

		return preg_replace( '/^http(s?)\:\/\/(www\.)?/i', '', $site_url );
	}

	/**
	 * Format Page path to use with Plausible API requests.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string Starting with a "/" slash and URL encoded
	 */
	private function format_path( $path ) {
		return rawurlencode( ( mb_substr( $path, 0, 1 ) === '/' ? $path : '/'.$path ) );
	}

	/**
	 * Build API Request Header.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @uses PLAUSIBLE_API_APITIMEOUT, PLAUSIBLE_API_APITOKEN
	 * @return array
	 */
	private function get_apiheader(  ) {
		$header = [
					 'timeout' => PLAUSIBLE_API_APITIMEOUT
					,'headers' => [
						'Authorization' => 'Bearer '.PLAUSIBLE_API_APITOKEN
					]
				];
		return $header;
	}

	/**
	* Build API Request Base-URL
	*
	* Example: https://plausible.io/api/v1/stats/aggregate?site_id=mydomain.com
	*
	 * @since  1.0
	 * @access private
	 *
	 * @uses PLAUSIBLE_API_APIBASEURL, PLAUSIBLE_API_APIVERSION, PLAUSIBLE_API_APIENDPOINTS
	 * @uses self::get_domain()
	 * @param string $endpoint Key of desired API-endpoint from PLAUSIBLE_API_APIENDPOINTS
	 * @return string
	 */
	private function get_api_baseurl( $endpoint ) {
		# Example: https://plausible.io/api/v1/stats/aggregate?site_id=mydomain.com
		$baseurl = sprintf( '%s/v%d%s?site_id=%s', PLAUSIBLE_API_APIBASEURL, PLAUSIBLE_API_APIVERSION, PLAUSIBLE_API_APIENDPOINTS[$endpoint], $this->get_domain() );

		return $baseurl;
	}

	/**
	 * Execute a GET request to the API
	 *
	 * Example:
	 * 		https://plausible.io/api/v1/stats/aggregate?site_id=mydomain.com&period=6mo&compare=previous_period&filters=event:page%3D%3D%2Forder%2Fconfirmation
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses self::get_api_baseurl(), self::get_apiheader()
	 * @param array $requestparams Key-value pairs for URL-parameters to use on self::get_api_baseurl()
	 * @return object|bool Response body retrieved, or FALSE if request failed.
	 */
	public function get_apirequest( $requestparams ) {
		$urlparams = http_build_query( $requestparams );
		$requesturl = sprintf( "%s&%s", $this->get_api_baseurl( 'aggregate' ), $urlparams );
		$apirequest = wp_remote_get( $requesturl, $this->get_apiheader() );
		if ( ! is_wp_error( $apirequest ) ) {
			$responsebody = json_decode( wp_remote_retrieve_body( $apirequest ) );
			$dataobj = $responsebody->results;
			return $dataobj;
		} else {
			return false;
		}
	}
}
