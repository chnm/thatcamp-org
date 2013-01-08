<?php 

if( isset($_GET['id']) && is_numeric($_GET['id']) ){
	$id = $_GET['id'];
	
	if( !strpos( $_GET['src'],"/avatars/".$id."/" ) )
		die();
} else {
	die();
}
// this is the standard set up with wp-content living 3 levels down
define ('WP_CONTENT_DIR', dirname(dirname(dirname(__FILE__)))); 
define ('AVATARS_DIR',WP_CONTENT_DIR.'/uploads/avatars/');
// cache the file inside the avatar directory 
// please modify this to your hearts content
if(! defined('FILE_CACHE_DIRECTORY') ) 		define ('FILE_CACHE_DIRECTORY', AVATARS_DIR.$id.'/cache');


// this are pretty much the standard settings
if(! defined( 'DEBUG_ON' ) ) 			define ('DEBUG_ON', false);				// Enable debug logging to web server error log (STDERR)
if(! defined('DEBUG_LEVEL') ) 			define ('DEBUG_LEVEL', 1);				// Debug level 1 is less noisy and 3 is the most noisy
if(! defined('MEMORY_LIMIT') ) 			define ('MEMORY_LIMIT', '30M');				// Set PHP memory limit
if(! defined('BLOCK_EXTERNAL_LEECHERS') ) 	define ('BLOCK_EXTERNAL_LEECHERS', true);		// If the image or webshot is being loaded on an external site, display a red "No Hotlinking" gif.

//Image fetching and caching
if(! defined('ALLOW_EXTERNAL') ) 		define ('ALLOW_EXTERNAL', false);			// Allow image fetching from external websites. Will check against ALLOWED_SITES if ALLOW_ALL_EXTERNAL_SITES is false
if(! defined('ALLOW_ALL_EXTERNAL_SITES') ) 	define ('ALLOW_ALL_EXTERNAL_SITES', false);		// Less secure. 
if(! defined('FILE_CACHE_ENABLED') ) 		define ('FILE_CACHE_ENABLED', TRUE);			// Should we store resized/modified images on disk to speed things up?
if(! defined('FILE_CACHE_TIME_BETWEEN_CLEANS'))	define ('FILE_CACHE_TIME_BETWEEN_CLEANS', 86400);	// How often the cache is cleaned 
if(! defined('FILE_CACHE_MAX_FILE_AGE') ) 	define ('FILE_CACHE_MAX_FILE_AGE', 86400);		// How old does a file have to be to be deleted from the cache
if(! defined('FILE_CACHE_SUFFIX') ) 		define ('FILE_CACHE_SUFFIX', '.timthumb.txt');		// What to put at the end of all files in the cache directory so we can identify them
		// Directory where images are cached. Left blank it will use the system temporary directory (which is better for security)
if(! defined('MAX_FILE_SIZE') ) 		define ('MAX_FILE_SIZE', 10485760);			// 10 Megs is 10485760. This is the max internal or external file size that we'll process.  
if(! defined('CURL_TIMEOUT') ) 			define ('CURL_TIMEOUT', 20);				// Timeout duration for Curl. This only applies if you have Curl installed and aren't using PHP's default URL fetching mechanism.
if(! defined('WAIT_BETWEEN_FETCH_ERRORS') ) 	define ('WAIT_BETWEEN_FETCH_ERRORS', 3600);		//Time to wait between errors fetching remote file
//Browser caching
if(! defined('BROWSER_CACHE_MAX_AGE') ) 	define ('BROWSER_CACHE_MAX_AGE', 864000);		// Time to cache in the browser
if(! defined('BROWSER_CACHE_DISABLE') ) 	define ('BROWSER_CACHE_DISABLE', false);		// Use for testing if you want to disable all browser caching

//Image size and defaults
if(! defined('MAX_WIDTH') ) 			define ('MAX_WIDTH', 1500);				// Maximum image width
if(! defined('MAX_HEIGHT') ) 			define ('MAX_HEIGHT', 1500);			// Maximum image height
if(! defined('NOT_FOUND_IMAGE') )		define ('NOT_FOUND_IMAGE', '');			//Image to serve if any 404 occurs 
if(! defined('ERROR_IMAGE') )			define ('ERROR_IMAGE', '');				//Image to serve if an error occurs instead of showing error message 