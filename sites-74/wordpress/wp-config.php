<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'admin' );

/** Database hostname */
define( 'DB_HOST', 'mysql' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':3l*}. X@vRk*pRFP!u@2G[07hNVk4B6FU8iul@%+DX$ef%.%7n=M2 JmhDEF8xv' );
define( 'SECURE_AUTH_KEY',  ',.bBF(&.f*H_9cq!7z+uV1fl2*TDUI3PQ.~kdo4I^t3X{%`i4iW!@WT) rekbzjo' );
define( 'LOGGED_IN_KEY',    'n=MqlsuGEZ(xB=7W$Qz&>TqB8 c5/2p#2@J#2}YLg:6rde(%gd;HbJ^,0`Fsc9[p' );
define( 'NONCE_KEY',        'mDpDKrW/g_)4LITz.,Lqbd]T8wWmq,=T1Ny_mb-f=,1j&mC?S|OA%( [5Oa$BMKE' );
define( 'AUTH_SALT',        '.GvOL;RJOF[nR_?ng#;equEd{!d_w}`~?q?M*[sqhP!-&G?{cj0NrB!,RHxDKe,^' );
define( 'SECURE_AUTH_SALT', '$E:D,UDvR`,K$wo MOJd%<GDm{~}=P2E2VKe<=0fwnp44?DKRmm/4}(+jN BZLg0' );
define( 'LOGGED_IN_SALT',   'S?aCQ;/r0ib A-=kz8w26c_~B+itRql~O%-sl)ejjw;9{?69uUJ^5mL(7VTmFau:' );
define( 'NONCE_SALT',       'CRdxx89|me`5o1=BX?1UR-~tVHL(GAc0!+6kO2{^T2e)j_@3Z.$M<X%.K~NV~8HI' );

define('FS_METHOD', 'direct');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
