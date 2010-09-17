<?php

/**
 * Used in other files to simplify includes:
 * <code>
 * include PATH_ROOT . 'pages/example.php';
 * </code>
 */
define( 'PATH_ROOT', rtrim( realpath( '..' ), '/' ) );

date_default_timezone_set( 'Europe/Bucharest' );

/**
 * When an unknown (no file exists) URL is requested, this function
 * will be called.
 */
function notFound()
{
    if( ob_get_level() )
    {
        ob_end_clean();
    }
    
    header( 'HTTP/1.0 404 Not Found' );
    require '../pages/404.php';
    exit;
}

/**
 * Useful to show debugging info only to the administrator:
 * <code>
 * if (dev()) {
 *     var_dump($_SERVER);
 * }
 * </code>
 */
function dev()
{
    // edit the line below with your home ip
    return $_SERVER['REMOTE_ADDR'] === '11.12.221.125' ? true : false;
}

/**
 * Determining which file should be loaded, based on the request URL.
 * If not page is requested, index will be used as the default.
 */
$page = ( empty( $_SERVER['REDIRECT_URL'] ) || $_SERVER['REDIRECT_URL'] == '/' ) ? 'index' : $_SERVER['REDIRECT_URL'];
$page = preg_replace( '/[^a-zA-Z0-9_\/\-]/', '', trim( $page, '/' ) );
$file = '../pages/' .  $page . '.php';

if( ! file_exists(  $file ) )
{
    notFound();
}

/**
 * Loading the configuration file and initializing all variables
 * in the global scope (ie. they can be used in the header, footer
 * and the page itself as $varname).
 */
$config = parse_ini_file( '../config/config.ini', true );

foreach( $config['default'] as $k => $v )
{
    $$k = empty( $config[$page][$k] ) ? $v : $config[$page][$k];
}

/**
 * Output the HTML.
 */
ob_start();

require '../pages/_header.php';
require $file;
require '../pages/_footer.php';

ob_end_flush();

?>
