<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'dronezonewp');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'dronezql');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', '1VIRYT0hPUpzjwK');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */


define('AUTH_KEY',         '`*Tb2uPz!/ 8Q8`/5Tl+1e=y9pE=OIUqzv/m91FB;4j9m%hG^S$P.m/RCS4x&~!<');
define('SECURE_AUTH_KEY',  's7j!%[+3_b{PAp}u9eSnxRX4=em:@Uq~>NpGG!D1a}Ek^HW!E,8F5XwA8`86N3H]');
define('LOGGED_IN_KEY',    '7UYDX9-Kc<)m-|&+(MT>f$Gz_!4?PZZNAIwc?G@,~t9s30s5aDK}{fN-QbYH5eU!');
define('NONCE_KEY',        '-!1U-m-<#h^TS<P4#Yxy[{mFwJWigR+6~e?71n&*Va$+-0S2{&wanY:=YZW)X<Fr');
define('AUTH_SALT',        'fo,T .nZ!@/]|z2CM)*Ij9s&WU;d%`;3k[g.|n4D|B&)}=)WowiCjXe#6+n%H231');
define('SECURE_AUTH_SALT', 'FJ1iUy}>N6cNLmBn-@YcNf.V7V]BYWLm!+-gbAdTr,W1Zj|q|wH){kljSJ_]+pi|');
define('LOGGED_IN_SALT',   'aB5 x|+IL},L6%X-d&u0wL>{~bKw9CSyiLF@)+k^_/YL^f4Fm^O&vV@=tMaV!yJ9');
define('NONCE_SALT',       'KC!E%Jwt2P08nv1<O=r&pa#i)9fqEwj!:|Kw.M<oPK7C#yx+LO#.yI@9pG3/7B:9');


/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


/*FTP*/

define('FTP_HOST', 'localhost');
define('FTP_USER', 'dronezone');
define('FTP_PASS', '59A5dOo29190TIX');
define('FTP_BASE' , 'www/');

/*/FTP*/
