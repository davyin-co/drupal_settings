<?php
if (getenv('DB_HOST')) {
  $databases['default']['default'] = array(
    'driver' => getenv('DB_TYPE') ?? 'mysql',
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    'prefix' => '',
  );
}

$update_free_access = false;

if (getenv('HASH_SALT')) {
  $settings['hash_salt'] = getenv('HASH_SALT');
}
else {
  $drupal_hash_salt = 'Zqk779FTx3YNqiU-o2DdqJ3k-pTeU6a5kjO6enfrrDk';
}
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);
$conf['404_fast_paths_exclude'] = '/\/(?:styles)|(?:system\/files)\//';
$conf['404_fast_paths'] = '/\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';
$conf['404_fast_html'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/             xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';

// Environment specific settings files.
if (getenv('SITE_ENVIRONMENT')) {
  if (getenv('SITE_ENVIRONMENT') == 'prod') {
    $conf['error_level'] = 0;
    $conf['cache'] = 1;
    $conf['block_cache'] = 1;
    $conf['page_cache_maximum_age'] = 900;
    $conf['preprocess_css'] = 1;
    $conf['preprocess_js'] = 1;
    $conf['stage_file_proxy_origin'] = false;
    $conf['stage_file_proxy_origin_dir'] = false;
    ini_set('display_errors', false);
    ini_set('display_startup_errors', false);
  }
  if (getenv('SITE_ENVIRONMENT') == 'dev') {
    $conf['error_level'] = 2;
    $conf['cache'] = 0;
    $conf['block_cache'] = 0;
    $conf['page_cache_maximum_age'] = 0;
    $conf['preprocess_css'] = 0;
    $conf['preprocess_js'] = 0;
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
  }
}

// Minimum cache lifetime should be always 0, therefore no automatic cache purging
$conf['cache_lifetime'] = 0;
// Pages will be compressed by nginx, no need for Drupal to do that
$conf['page_compression'] = 0;

### Base URL
if (getenv('BASE_URL')) {
  $base_url = getenv('BASE_URL');
}

### Temp directory
if (getenv('TMP_PATH')) {
  $conf['file_temporary_path'] = getenv('TMP_PATH') ?? '/tmp';
}


// Last: this servers specific settings files.
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
