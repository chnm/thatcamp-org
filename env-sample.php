<?php
/**
* INSTRUCTIONS:
*
* 1) Copy me to env.php
* 2) Replace the DB-specific stuff with the appropriate creds for your environment
* 3) The WPCOM_API_KEY is not kept under version control for security reasons. Get it from the
*    production or staging env.php if you need it locally
* 4) ???
* 5) Profit
*/

/**
* WordPress DB constants
*/
define( 'DB_NAME', 'foo' );
define( 'DB_USER', 'bar' );
define( 'DB_PASSWORD', 'baz' );
define( 'DB_HOST', 'localhost' );

/**
* Other environment specific constants
*/
define( 'ENV_TYPE', 'local' );

define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', false );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', true );

/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY',         'foo');
define('SECURE_AUTH_KEY',  'foo');
define('LOGGED_IN_KEY',    'foo');
define('NONCE_KEY',        'foo');
define('AUTH_SALT',        'foo');
define('SECURE_AUTH_SALT', 'foo');
define('LOGGED_IN_SALT',   'foo');
define('NONCE_SALT',       'foo');

?>

