<?php
### Database connection
if(getenv('DB_HOST')){
  $databases['default']['default'] = array(
    'driver' => getenv('DB_TYPE') ? getenv('DB_TYPE') : 'mysql',
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    //'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'prefix' => '',
    'collation' => 'utf8mb4_general_ci',
  );
}

### ElasticSearch Config
if (getenv('ES_URL')) {
  $config['elasticsearch_connector.cluster.es01']['url'] = getenv('ES_URL');
  $config['elasticsearch_connector.cluster.es01']['options']['username'] = getenv('ES_USERNAME');
  $config['elasticsearch_connector.cluster.es01']['options']['password'] = getenv('ES_PASSWORD');
}

### Reverse proxy settings
## https://www.drupal.org/node/425990
if (getenv('HTTP_X_FORWARDED_FOR')) {
  $settings['reverse_proxy'] = TRUE;
  $settings['reverse_proxy_trusted_headers'] = \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT |  \Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED;
  $settings['reverse_proxy_addresses'] = [
    //getenv('HTTP_X_FORWARDED_FOR'),
    $_SERVER['REMOTE_ADDR'],
    '127.0.0.1',
  ];
}

### Trusted Host Patterns, see https://www.drupal.org/node/2410395 for more information.
### If your site runs on multiple domains, you need to add these domains here
### match example.com and example.com:8080
if (strpos(getenv('HTTP_HOST'), ':') === FALSE) {
  $host = getenv('HTTP_HOST');
  $port = 80;
}
else {
  list($host, $port) = explode(':', getenv('HTTP_HOST'));
}
$settings['trusted_host_patterns'] = array(
  '^' . str_replace('.', '\.', $host) . '$',
  '127.0.0.1',
  'localhost',
);

### Temp directory
if (getenv('TMP_PATH')) {
  $config['system.file']['path']['temporary'] = getenv('TMP_PATH');
}

### Hash Salt
if (getenv('HASH_SALT')) {
  $settings['hash_salt'] = getenv('HASH_SALT');
}

// Environment specific settings files.
if(getenv('SITE_ENVIRONMENT')){
  if (getenv('SITE_ENVIRONMENT') == 'prod') {
    $config['system.logging']['error_level'] = 'hide';
    $config['system.performance']['cache']['page']['max_age'] = 900;
    $config['system.performance']['css']['preprocess'] = 1;
    $config['system.performance']['js']['preprocess'] = 1;
    $config['stage_file_proxy.settings']['origin'] = false;
    $config['dblog.settings']['row_limit'] = 100000;
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
  }
  if (getenv('SITE_ENVIRONMENT') == 'dev') {
    $config['system.logging']['error_level'] = 'verbose';
    //$config['system.performance']['cache']['page']['max_age'] = 900;
    $config['system.performance']['css']['preprocess'] = 0;
    $config['system.performance']['js']['preprocess'] = 0;
    //$config['stage_file_proxy.settings']['origin'] = false;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
  }
  // Environment specific services files.
  if (file_exists(__DIR__ . '/' . getenv('SITE_ENVIRONMENT') . '.services.yml')) {
    $settings['container_yamls'][] = __DIR__ . '/' . getenv('SITE_ENVIRONMENT') . '.services.yml';
  }
}

