<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_kyleoreilly');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'b<^R-G+Om8q8h[suSh@I5#;{b81U?<m6^-ysQtgsHv9*G%J#o<G,=!^>jiED.GZc');
define('SECURE_AUTH_KEY',  'v-}{*.:SdF;e-#3B+c`!#_9n0[cl~M+9t+8`sw$]!)lr:5EO+|w+,HQm9 nc)]RS');
define('LOGGED_IN_KEY',    'N9Q@p0F:,VgOU|D!-k}YL|,4+J-_FT>pP4x2PT5&:c8,XnJ$tp`jR8l]J4-t!f:-');
define('NONCE_KEY',        'LE267K9dWYa&d)#T@gBpD8LNNoqDJ+GoGV%,955.0P=a~ts)Ts5yXKP pXk40F-[');
define('AUTH_SALT',        'q<#M}~Cf_-^qBcJ0H>9mnYB8nCO+09r_W)Q!|$8GV/>E.)%87MR;3#41=RDvVo4$');
define('SECURE_AUTH_SALT', '=2`YF9yb_tQe8B8JI;67rKuU- n*%K.oBNXJK` P?)B5]tPe+x:`kAPm}:rnc[hG');
define('LOGGED_IN_SALT',   '|I_-zFG,} vvTg,n+pFK64A]+KVusEo_/+Q^8hxU|=XD>:T(8}jE+=,=i>s<?Nu~');
define('NONCE_SALT',       'tZ~=6751][:!nz]uCGs%R]-`EU<p9(}[QVAN0A+:UmzC$Z>i&gC@3+j;D.WEcmzZ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
