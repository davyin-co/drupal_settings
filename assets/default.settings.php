<?php
/**
 * @file
 * Platform.sh example settings.php file for Drupal 8.
 */

// Default Drupal 8 settings.
//
// These are already explained with detailed comments in Drupal's
// default.settings.php file.
//
// See https://api.drupal.org/api/drupal/sites!default!default.settings.php/8

$config['system.performance']['fast_404']['exclude_paths'] = '/\/(?:styles)|(?:system\/files)\//';
$config['system.performance']['fast_404']['paths'] = '/\/Template|\/Uploadfiles|\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp|aspx)$/i';
$config['system.performance']['fast_404']['html'] = '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this   server.</p></body></html>';
$databases = [];
$config_directories = [];
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];

// The hash_salt should be a unique random value for each application.
// If left unset, the settings.platformsh.php file will attempt to provide one.
// You can also provide a specific value here if you prefer and it will be used
// instead. In most cases it's best to leave this blank on Platform.sh. You
// can configure a separate hash_salt in your settings.local.php file for
// local development.
// $settings['hash_salt'] = 'change_me';

// Set up a config sync directory.
//
// This is defined inside the read-only "config" directory, deployed via Git.
$settings['config_sync_directory'] = '../config/sync';
$settings['config_exclude_modules'] = [
  'devel',
  'devel_php',
  'stage_file_proxy',
];
// Automatic Platform.sh settings.
if (file_exists($app_root . '/' . $site_path . '/settings.platform.php')) {
  include $app_root . '/' . $site_path . '/settings.platform.php';
}

// Local settings. These come last so that they can override anything.
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
//avoid dynamic config path for different drupal project.
if (file_exists('/var/config/drupal/settings.local.php')) {
  include '/var/config/drupal/settings.local.php';
}

// Support Drupal subdir with env:DRUPAL_SUBDIR, see
// https://blog.rebootr.nl/drupal-8-in-a-subdirectory-with-nginx/
if (!(getenv('HTTP_HOST') == getenv('SUBDIR_HOST') && str_starts_with(getenv('REQUEST_URI'), DIRECTORY_SEPARATOR . getenv('DRUPAL_SUBDIR')))) {
  //echo "not allow url" . getenv('HTTP_HOST') . getenv('REQUEST_URI');
  return;
}
if (getenv('DRUPAL_SUBDIR') && substr(getenv('REQUEST_URI'), 1, strlen(getenv('DRUPAL_SUBDIR'))) === getenv('DRUPAL_SUBDIR') && isset($GLOBALS['request'])) {
  $subdir = getenv('DRUPAL_SUBDIR');
  $scriptName = $GLOBALS['request']->server->get('SCRIPT_NAME');
  $scriptName = preg_match("#^/$subdir/#", $scriptName) ? : "/$subdir$scriptName";
  $GLOBALS['request']->server->set('SCRIPT_NAME', $scriptName);
}
