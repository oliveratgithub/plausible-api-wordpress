<?php
/**
 * Plausible Analytics API | Stats API.
 *
 * @since 1.0
 *
 * @package    WordPress
 * @subpackage Plausible Analytics API
 */

namespace Plausible\Analytics\WP\API;

//require_once PLAUSIBLE_API_PLUGIN_DIR . 'helpers.php';
require_once __DIR__ . '/helpers.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Stats API
 */
class Stats {

	/**
	 * Define some Class constants
	 *
	 * @const PROPERTIES Supported filtering properties for the API /stats endpoint https://plausible.io/docs/stats-api#properties
	 */
	private const PROPERTIES = [
						 	'event' => 'event:name'
							,'page' => 'event:page'
							,'entrypage' => 'visit:entry_page'
							,'exitpage' => 'visit:exit_page'
							,'source' => 'visit:source'
							,'referrer' => 'visit:referrer'
							,'campaignmedium' => 'visit:utm_medium'
							,'campaignsource' => 'visit:utm_source'
							,'campaign' => 'visit:utm_campaign'
							,'campaigncontent' => 'visit:utm_content'
							,'campaignterm' => 'visit:utm_term'
							,'device' => 'visit:device'
							,'browser' => 'visit:browser'
							,'browserversion' => 'visit:browser_version'
							,'os' => 'visit:os'
							,'osversion' => 'visit:os_version'
							,'country' => 'visit:country'
							,'region' => 'visit:region'
							,'city' => 'visit:city'
						];

	/**
	 * Class Vars
	 */
	private ?object $Helpers;

	/**
	 * Class Constructor
	 */
	public function __construct(  )
	{
		$this->Helpers = new Helpers();
	}

	/**
	 * Formats a list of Key-Value pair to use with API filtering
	 *
	 * Example: Stats->format_filter( [ 'browser' => 'Firefox', 'country' => 'FR|DE', country' => '!FR' ] )
	 * Required for API URL Parameter: &filters=visit:browser%3D%3DFirefox;visit:country%3D%3DFR|DE
	 *
	 * @link https://plausible.io/docs/stats-api#filtering
	 * @uses self::PROPERTIES
	 * @param string $filterset An Array with key-value pairs where key = Filter property and value = Filter criterion
	 * @return string An URL-Parameter like: "visit:browser%3D%3DFirefox;visit:country%3D%3DFR|DE"
	 */
	private function format_filter( $filterset ) {
		if ( is_array($filterset) && is_array(self::PROPERTIES) ) {
			foreach ( $filterset as $filterfor => $filtervalue ) {
				/** Only Keys from self::PROPERTIES are allowed */
				if ( array_key_exists($filterfor, self::PROPERTIES) ) {
					/** Check and set comparison type: "!=" (%21%3D) or "==" (%3D%3D) */
					$comparison = ( mb_substr( $filtervalue, 0, 1 ) === '!' ? '!=' : '==' );

					$filtering[] = self::PROPERTIES[$filterfor] . $comparison . $filtervalue;
				}
			}

			/** Make API Filtering compatible URL-Parameter: &filters=visit:browser%3D%3DFirefox;visit:country%3D%3DFR|DE */
			$filterurlparam = implode( ';', $filtering );
			return $filterurlparam;
		} else {
			/** Return empty string if $filterset is not of type Array */
			return '';
		}
	}

	/**
	 * Formats a date range to use with API custom period
	 *
	 * Example: Stats->format_daterange( '2023-01-01', '2023-05-07' )
	 * Required for API URL Parameter: &date=2023-01-01,2023-05-07
	 *
	 * @link
	 * @param string $startdate The date range start.
	 * @param string $enddate The date range end.
	 * @return string An URL-Parameter like: "2023-01-01,2023-05-07"
	 */
	private function format_daterange( $startdate, $enddate ) {
		$dateurlparam = sprintf( '%s,%s', $startdate, $enddate );
		return $dateurlparam;
	}

	/**
	 * The API /stats/aggregate endpoint
	 *
	 * @link https://plausible.io/docs/stats-api#get-apiv1statsaggregate
	 * @uses self::format_daterange(), self::format_filter(), Helpers::get_apirequest()
	 * @param array $metrics The metrics to query: visitors, visits, pageviews, views_per_visit, bounce_rate, visit_duration, events
	 * @param array $timeperiod The date range to query for. Default: 30d (30 Days)
	 * @param array $datefrom The start date to query from. Default: null.
	 * @param array $dateto The end date to query to. Required if $datefrom is used!
	 * @param bool $compare Whether data of previous period should be included. Default: FALSE
	 * @param array $filters Any additional data Filter criteria. E.g. [ 'browser' => 'Firefox', 'country' => 'FR|DE' ]
	 * @return float|array A single number if only 1 $metrics, or array of $metrics - from "results:"-object of the API request response body.
	 */
	public function aggregate( $metrics = ['visitors'], $timeperiod = '30d', $datefrom = null, $dateto = null, $compare = false, $filters = [] )
	{
		/** Build URL-Parameters for the API Request */
		$urlparams['metrics'] = implode(',', $metrics);
		$urlparams['period'] = $timeperiod;
		if ( $timeperiod === 'custom' && !empty($datefrom) && !empty($dateto) ) $urlparams['date'] = $this->format_daterange($datefrom, $dateto);
		if ( $compare !== false ) $urlparams['compare'] = 'previous_period';
		if ( !empty($filters) ) $urlparams['filters'] =  $this->format_filter($filters);

		/** Retrieve data from the API Request */
		$apirequest = $this->Helpers->get_apirequest( $urlparams );

		/** Return the data per defined $metrics */
		$nummetrics = count( $metrics );
		if ( $nummetrics > 1 ) {
			/** Multiple $metrics */
			for ( $i=0; $i<$nummetrics; $i++ ) {
				/** If $compare is TRUE */
				if ( $compare !== false ) {
					$data[$metrics[$i]] = [ $apirequest->{$metrics[$i]}->value, $apirequest->{$metrics[$i]}->change ];
				} else {
					$data[$metrics[$i]] = $apirequest->{$metrics[$i]}->value;
				}
			}
		} else {
			/** Only 1 single $metrics */
			$data = $apirequest->{$metrics[0]}->value;
		}
		return $data;
	}
}
