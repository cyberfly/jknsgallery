<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'jknsgallery');

/** MySQL database username */
define('DB_USER', 'jknsgallery');

/** MySQL database password */
define('DB_PASSWORD', 'jknsgallery1234');

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
define('AUTH_KEY',         'v].>jqqMyY. Zm+P&;3yr&i{i] 1ek^R/OG%]{T[Gcu(qL9Nv1fEH?*sI|{|N^:5');
define('SECURE_AUTH_KEY',  '(l0l+]y}5fVz$U(Di:4jr9Y s:GPm4{O (.()(ke>Q7(:@9srl-+fR+eyY#pp3Op');
define('LOGGED_IN_KEY',    'xe=R@C>E^x od<;.zy8h%<t7.HoQL&4xLfS:VM8|,@4_0/?~ww/~m5q8aJ,zF%pu');
define('NONCE_KEY',        'xH*7!^]gk|{/G#N-fU{*rQnJ)#K`T@ F>+Dt:UWZV`Ov N=-pZ^(X(rU[#_X/-.]');
define('AUTH_SALT',        'iGb,_N6t| HF.%nzW`W#5SP8N;~pKddccA:cM=I#^n5uf)t$gwb6ug3bZCp$l)f!');
define('SECURE_AUTH_SALT', '+7KhUj{}u]?loydrNC)DB!:ukt?b/8-Bw/uvHZ|vuqLd2&?nU:NoF*9iE=lL7}v*');
define('LOGGED_IN_SALT',   '|=t$%cUP+kiSln,Rk(Z|I4b$P(Z]t9.B[UCUe-PN/-]ivv}`$=k|{8!qNO/hi+Gf');
define('NONCE_SALT',       ';4bQRq)+e%t$z$IiMdfv3S;KF-~vP*_/Ig/&X3CqieF4kB$g3[UJgdz^hP=H] 2j');

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
