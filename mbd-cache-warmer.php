<?php
/*
 * Plugin Name: Cache Warmer Plugin
 * Plugin URI:        https://mightybigdata.com/
 * Description:       Adds a cache warmer that runs every X amount of time and warms pages based on the sitemap
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      8.1
 * Author:            Alexander Glover
 * Author URI:        https://mightybigdata.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       mbd-cache-warmer-plugin
 * Domain Path:       /languages
 */


require_once __DIR__ . '/vendor/autoload.php';


$cacheWarmer = new \Mbd\CacheWarmerWordpress\MbdCacheWarmerSetup();
$cacheWarmer->install();

register_deactivation_hook( __FILE__, 'deactivateMbdCacheWarmerCron');

function deactivateMbdCacheWarmerCron(): void
{
    $helper = new \Mbd\CacheWarmerWordpress\MbdCacheWarmerHelper();

    $helper->writeToLog('deactivate worked');
    $timestamp = wp_next_scheduled('mbd_cache_warmer');
    wp_unschedule_event( $timestamp,'mbd_cache_warmer');
    wp_clear_scheduled_hook('mbd_cache_warmer');
}
