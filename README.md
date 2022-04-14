## Intruduction
1. best practise for drupal settings config, try to make drupal following [12 factor apps](https://12factor.net/zh_cn/).
2. base on [drupal core scaffold](https://github.com/drupal/core-composer-scaffold)

## usage
1. composer require davyin/drupal_settings
2. edit composer.json, add extra config like below:
```
    "extra": {
        "drupal-scaffold": {
            "file-mapping": {
                "[web-root]/sites/default/default.settings.php": {
                    "mode": "replace",
                    "overwrite": false,
                    "path": "vendor/davyin/drupal_settings/assets/default.settings.php"
                },
                "[web-root]/sites/default/dev.services.yml": {
                    "mode": "replace",
                    "overwrite": false,
                    "path": "vendor/davyin/drupal_settings/assets/dev.services.yml"
                },
                "[web-root]/sites/default/local.settings.php": {
                    "mode": "replace",
                    "overwrite": false,
                    "path": "vendor/davyin/drupal_settings/assets/settings.local.php"
                },
                "[web-root]/sites/default/prod.services.yml": {
                    "mode": "replace",
                    "overwrite": false,
                    "path": "vendor/davyin/drupal_settings/assets/prod.services.yml"
                },
                "[web-root]/sites/default/settings.platform.php": {
                    "mode": "replace",
                    "overwrite": false,
                    "path": "vendor/davyin/drupal_settings/assets/settings.platform.php"
                },
                "[web-root]/sites/default/settings.php": {
                    "mode": "replace",
                    "overwrite": false,
                    "path": "vendor/davyin/drupal_settings/assets/default.settings.php"
                }
            },
            "locations": {
                "web-root": "docroot/"
            }
        },
    }
```
3. using it with docker, here is example docker-compose.yml
```
version: "3"
services:
  example:
    image: sparkpos/docker-nginx-php:7.4-alpine
    #image: sparkpos/docker-nginx-php.slim
    #image: davyinsa/cloudrup-webserver.slim
    container_name: example
    hostname: example.docker
    restart: always
    volumes:
      - ~/www/example:/var/www/html
    environment:
      - VIRTUAL_HOST=example.docker
      - DRUPAL8_WEB_DIR=docroot
      - MAX_FILE_UPLOAD_SIZE=300
      - TIMEOUT=300
      - DB_TYPE=mysql
      - DB_HOST=mysql8
      - DB_PORT=3306
      - DB_USERNAME=root
      - DBPASSWORD=password
      - DB_NAME=example_db
      - SITE_ENVIRONMENT=dev
      - HASH_SALT=change_me
      - ES_URL=http://es01:9200
      - ES_USERNAME=elastic
      - ES_PASSWORD=password
```

## similar project
* [drupal settings](https://github.com/pog-vupar/drupal-settings)
* [amazeeio drupal settings files](https://github.com/amazeeio/drupal-setting-files)
* [platformsh drupal8 templates](https://github.com/platformsh-templates/drupal8/tree/master/web/sites/default)
* [platform fwp-drupal](https://github.com/platformsh/fwp-drupal/tree/master/web/sites/default)
