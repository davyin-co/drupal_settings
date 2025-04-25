<?php
use Drupal\Core\Installer\InstallerKernel;
### Database connection
if (!empty(getenv('DB_HOST'))) {
  $databases['default']['default'] = [
    'driver' => getenv('DB_TYPE') ?: 'mysql',
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    'prefix' => '',
    'collation' => 'utf8mb4_general_ci',
  ];
}

### Redis config
## ref: https://github.com/platformsh-templates/drupal9/blob/master/web/sites/default/settings.platformsh.php
if (
  !empty(getenv('REDIS_HOST')) &&
  !empty(getenv('REDIS_PORT')) &&
  !InstallerKernel::installationAttempted() &&
  extension_loaded('redis') &&
  class_exists('Drupal\redis\ClientFactory')
) {
  $settings['cache']['default'] = 'cache.backend.redis';
  $settings['cache_prefix'] = getenv('APP_ID');

  $settings['redis.connection'] = [
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
    //'password' => getenv('REDIS_PASSWORD'),
    //'persistent' => TRUE,
  ];
  if (!empty(getenv('REDIS_PASSWORD'))) {
    $settings['redis.connection']['password'] = getenv('REDIS_PASSWORD');
  }

  $settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';
  $settings['container_yamls'][] = 'modules/contrib/redis/redis.services.yml';

  $class_loader->addPsr4('Drupal\\redis\\', 'modules/contrib/redis/src');

  $settings['bootstrap_container_definition'] = [
    'parameters' => [],
    'services' => [
      'redis.factory' => [
        'class' => 'Drupal\redis\ClientFactory',
      ],
      'cache.backend.redis' => [
        'class' => 'Drupal\redis\Cache\CacheBackendFactory',
        'arguments' => ['@redis.factory', '@cache_tags_provider.container', '@serialization.phpserialize'],
      ],
      'cache.container' => [
        'class' => '\Drupal\redis\Cache\PhpRedis',
        'factory' => ['@cache.backend.redis', 'get'],
        'arguments' => ['container'],
      ],
      'cache_tags_provider.container' => [
        'class' => 'Drupal\redis\Cache\RedisCacheTagsChecksum',
        'arguments' => ['@redis.factory'],
      ],
      'serialization.phpserialize' => [
        'class' => 'Drupal\Component\Serialization\PhpSerialize',
      ],
    ],
  ];
}

### ElasticSearch Config
if (!empty(getenv('ES_URL'))) {
  $config['elasticsearch_connector.cluster.es01'] = [
    'url' => getenv('ES_URL'),
    'options' => [
      'username' => getenv('ES_USERNAME'),
      'password' => getenv('ES_PASSWORD'),
    ],
  ];
}

### Reverse proxy settings
if (!empty(getenv('HTTP_X_FORWARDED_FOR'))) {
  $settings['reverse_proxy'] = TRUE;
  $settings['reverse_proxy_trusted_headers'] = 
    \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR |
    \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST |
    \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO |
    \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT |
    \Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED;

  $settings['reverse_proxy_addresses'] = [
    $_SERVER['REMOTE_ADDR'],
    '127.0.0.1',
  ];
}

### Trusted Host Patterns
$host = getenv('HTTP_HOST') ?: 'localhost';
$host = strpos($host, ':') === FALSE ? $host : explode(':', $host)[0];
$settings['trusted_host_patterns'] = [
  '^' . preg_quote($host) . '$',
  '127.0.0.1',
  'localhost',
];

### Temp directory
if (!empty(getenv('TMP_PATH'))) {
  $config['system.file']['path']['temporary'] = getenv('TMP_PATH');
}

### Hash Salt
if (!empty(getenv('HASH_SALT'))) {
  $settings['hash_salt'] = getenv('HASH_SALT');
}

### Environment-specific settings
$environment = getenv('SITE_ENVIRONMENT');
if ($environment === 'prod') {
  $config['system.logging']['error_level'] = 'hide';
  $config['system.performance']['cache']['page']['max_age'] = 900;
  $config['system.performance']['css']['preprocess'] = 1;
  $config['system.performance']['js']['preprocess'] = 1;
  $config['stage_file_proxy.settings']['origin'] = FALSE;
  $config['dblog.settings']['row_limit'] = 100000;
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
} elseif ($environment === 'dev') {
  $config['system.logging']['error_level'] = 'verbose';
  $config['system.performance']['css']['preprocess'] = 0;
  $config['system.performance']['js']['preprocess'] = 0;
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
}
