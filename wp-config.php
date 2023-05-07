<?php
define( 'WP_CACHE', true );
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u389210813_xKKew' );

/** Database username */
define( 'DB_USER', 'u389210813_GkPni' );

/** Database password */
define( 'DB_PASSWORD', 'V7n3lCN2Oe' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'N>%^PxG]5k!HJ~W^`z>N7=y3?#R` ]s_9!L7**@W3R2^os_j=G!>1YM(q7z/=%uK' );
define( 'SECURE_AUTH_KEY',   '{:AE|B0Xs>!DJ+fCeP=*d$UA6lXE2%|$),,b.*zdHTH,}/GZP~&hE14PKtd:+0-i' );
define( 'LOGGED_IN_KEY',     '/iX/0d3fsYK=H8Uu.4THM$5-RgznJfn(1 :E/Vx`$:ufiLt$|0VA@!B|f|k2r=DB' );
define( 'NONCE_KEY',         '_ZY1-JXJf$hNstT `NE?fN11<A0=fk@`WRO-5U!b4!PB{a#2{Q ){B8E)<doU2>B' );
define( 'AUTH_SALT',         '^m&KWK~Asmm){ewCG#FL&Im0O8daH (|&0jQA1A:VBq|+uk8Z35]S/y<ffX $x2%' );
define( 'SECURE_AUTH_SALT',  '[yF+y<I6[`j{47lHSq29|8iKxEH;CWpn7<8UG3qy{/i-=VHaN4HjITPH;-@ vi_h' );
define( 'LOGGED_IN_SALT',    ']!60{E=-pB9U<)XeG_?Hsv=k`9uDa;_Yj<$ni4whGb:Hp% pe$-V{(u#=E(I$6x?' );
define( 'NONCE_SALT',        'VFS<apt?a*isA3wjyGJ#,nm&{z$p*-<_Y_4F4h7-.F/;Msm=8w^]:`|;NI$B~n|Y' );
define( 'WP_CACHE_KEY_SALT', 'E2d:+#TTo`+aq&(Vv?~a?z~cmf?VL(NWr0 k3:IF^Ok8j;TjGH$cW;ZY3d/=D7os' );


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



define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'FS_METHOD', 'direct' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
