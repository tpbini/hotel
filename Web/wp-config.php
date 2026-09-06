<?php
/**
 * The base configuration for WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'thecochin_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 */
define( 'AUTH_KEY',         'r45#b&K9xL!zPqW21$vN8eRt7YuiO0mB' );
define( 'SECURE_AUTH_KEY',  'u89@vB1xM$zNqW34*eRt5YuiO6pA7dC8' );
define( 'LOGGED_IN_KEY',    'p12%cM4xN#zBqW56&rTy7UioP8qB9eD0' );
define( 'NONCE_KEY',        'k34^dN7xO!zCqW78(tYu9IopA0rC1fE2' );
define( 'AUTH_SALT',        's56&eP9xP$zDqW90)uIo1PasB2sD3gF4' );
define( 'SECURE_AUTH_SALT', 'd78*fR1xQ#zEqW12_vPa3SdfC4tE5hG6' );
define( 'LOGGED_IN_SALT',   'f90(gS3xR!zFqW34+wSd5FghD6uF7jH8' );
define( 'NONCE_SALT',       'g12)hT5xS$zGqW56=xDf7GhjE8vG9kI0' );

/**#@-*/

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';

/**
 * Performance & Memory settings
 */
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

/**
 * For developers: WordPress debugging mode.
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
