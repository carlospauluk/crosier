php bin/console fos:js-routing:dump --format=json --target=assets/static/fos_js_routes.json
php bin/console cache:clear --env=prod
yarn encore production
