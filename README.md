Plausible Analytics API - WordPress Plugin POC
===

**This is only a POC!** Goal is to have a plugin which adds direct access to the [Plausible Analytics API](https://plausible.io/docs/stats-api) within a WordPress site.

_NOTE_: only the `/stats/aggregate` API endpoint is implemented so far.

## Usage

* Place the `/plausible-api`-directory to your (Child-)Theme folder.
* Require the main Plugin file to get access to the Plausible Analytics API endpoints within your Theme.

### Examples:

#### Number of visitors to a specific page

```php
<?php
#...

if ( false !== stream_resolve_include_path( get_stylesheet_directory() . '/assets/plausible-api/plausible-api.php' ) )
{
	// Include the plausible-api Plugin
	require_once( get_stylesheet_directory() . '/assets/plausible-api/plausible-api.php' );

	// Get current Page's URL-Path (aka "slug") and add Slashes
	$pagepath = sprintf( '/%s/', basename( get_permalink() ) );

	// Query the Plausible Stats API
	$pageviews = $PlausibleStatsAPI->aggregate(
							 ['pageviews'] // metric(s)
							,'custom' // date period
							,get_the_date('Y-m-d', $post_id) // date period - start date
							,date('Y-m-d') // date period - end date
							,false // compare to previous period
							,['page' => $pagepath] // filter criteria
						);

	// Show the API request results to the given $pagepath
	echo $pageviews;
}

#...
```
