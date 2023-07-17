<?php
/**
 * Plausible Analytics API | Post Insights.
 *
 * @since 1.0
 *
 * @package WordPress/Plausible Analytics API/Post Insights
 */

namespace Plausible\Analytics\WP\API\PostInsights;

/**
 * The Class.
 */
class plausibleAnalyticsPostInsights {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		// add_action( 'save_post', array( $this, 'save' ) ); // Save could be leveraged to "cache" retrieved data…
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {

		$post_types = array('post', 'page'); // Limit meta box to post types "post" & "page"
		$plugin_data = get_plugin_data( __DIR__ . '/plausible-analytics-post-insights.php' );

		/** Execute only if logged-in Users with Post/Page «Edit Permissions»  */
		if ( in_array( $post_type, $post_types ) && current_user_can( 'edit_posts' ) ) {
			add_meta_box(
				'post_insights_meta_box'
				,__( $plugin_data['Name'], $plugin_data['TextDomain'] )
				,array( $this, 'render_meta_box_content' )
				,$post_type
				,'advanced'
				,'high'
			);
        }
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Plug-in Data
		$plugin_data = get_plugin_data( __DIR__ . '/plausible-analytics-post-insights.php' );

		// Post / Page Data
		$today_date = date('Y-m-d');
		$publish_date = get_the_date('Y-m-d', $post->id);

		if ( !empty($publish_date) ) {
			// Post Slug to URL-Path conversion
			$path = '/' . $post->post_name . '/';

			// (UNSUPPORTED) Get chached Views via get_post_meta from the database.
			//wp_nonce_field( 'post_insights_meta_box_inner', 'post_insights_meta_box_inner_nonce' ); // Add an nonce field for save()
			//$value = get_post_meta( $post->ID, '_plausible_post_insights_value_key', true );

			// Get Views via Plausible Analytics API
			if ( false !== stream_resolve_include_path( get_stylesheet_directory() . '/assets/plausible-api/plausible-api.php' ) ) {
				require_once( get_stylesheet_directory() . '/assets/plausible-api/plausible-api.php' );
				$pageviews = intval( $PlausibleStatsAPI->aggregate(['pageviews'], 'custom',  $publish_date, $today_date, false, ['page' => $path]) );
				$visitors = intval( $PlausibleStatsAPI->aggregate(['visitors'], 'custom',  $publish_date, $today_date, false, ['page' => $path]) );
				$visit_duration = intval( $PlausibleStatsAPI->aggregate(['visit_duration'], 'custom',  $publish_date, $today_date, false, ['page' => $path]) );
				$bounce_rate = floatval( $PlausibleStatsAPI->aggregate(['bounce_rate'], 'custom',  $publish_date, $today_date, false, ['page' => $path]) );
			} else {
				$pageviews = 0;
			}

			// Display the meta box content.
			echo '<div class="activity-block" id="' . $plugin_data['TextDomain'] . '">';
			echo '<h3>' . $publish_date . ' &mdash; ' . $today_date . '</h3>';
			echo '<ul>';
			if ( !empty($pageviews) ) {
				echo '<li><span>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="gridicon" aria-hidden="true" focusable="false"><path d="m4 13 .67.336.003-.005a2.42 2.42 0 0 1 .094-.17c.071-.122.18-.302.329-.52.298-.435.749-1.017 1.359-1.598C7.673 9.883 9.498 8.75 12 8.75s4.326 1.132 5.545 2.293c.61.581 1.061 1.163 1.36 1.599a8.29 8.29 0 0 1 .422.689l.002.005L20 13l.67-.336v-.003l-.003-.005-.008-.015-.028-.052a9.752 9.752 0 0 0-.489-.794 11.6 11.6 0 0 0-1.562-1.838C17.174 8.617 14.998 7.25 12 7.25S6.827 8.618 5.42 9.957c-.702.669-1.22 1.337-1.563 1.839a9.77 9.77 0 0 0-.516.845l-.008.015-.002.005-.001.002v.001L4 13Zm8 3a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"></path></svg>Pageviews:
					</span>' . esc_attr( $pageviews ) . '</li>';
				echo '<li><span>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="gridicon" aria-hidden="true" focusable="false"><path d="M15.5 9.5a1 1 0 100-2 1 1 0 000 2zm0 1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm-2.25 6v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM9.5 8.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" fill-rule="evenodd"></path></svg>Visitors:
					</span>' . esc_attr( $visitors ) . '</li>';
				echo '<li><span>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="gridicon" aria-hidden="true" focusable="false"><path d="M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"></path></svg>Visit Duration: ø
					</span>' . esc_attr( $visit_duration ).' Seconds' . '</li>';
				echo '<li><span>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="gridicon" aria-hidden="true" focusable="false"><path d="M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"></path></svg>Bounce Rate:
					</span>' . esc_attr( number_format($bounce_rate, 1) ).'%' . '</li>';
			} else {
				echo '<li>No data</li>';
			}
			echo '</ul></div>';
		}
	}

	/**
	 * (UNSUPPORTED) Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		// if ( ! isset( $_POST['myplugin_inner_custom_box_nonce'] ) )
		// 	return $post_id;

		// $nonce = $_POST['myplugin_inner_custom_box_nonce'];

		// // Verify that the nonce is valid.
		// if ( ! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box' ) )
		// 	return $post_id;

		// // If this is an autosave, our form has not been submitted,
        //         //     so we don't want to do anything.
		// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		// 	return $post_id;

		// // Check the user's permissions.
		// if ( 'page' == $_POST['post_type'] ) {

		// 	if ( ! current_user_can( 'edit_page', $post_id ) )
		// 		return $post_id;

		// } else {

		// 	if ( ! current_user_can( 'edit_post', $post_id ) )
		// 		return $post_id;
		// }

		// /* OK, its safe for us to save the data now. */

		// // Sanitize the user input.
		// $mydata = sanitize_text_field( $_POST['plausible_post_insights_pageviews'] );

		// // Update the meta field.
		// update_post_meta( $post_id, '_plausible_post_insights_value_key', $mydata );
	}
}
