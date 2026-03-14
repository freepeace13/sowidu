setup_local:
	cp env.local .env
	cp phpunit.xml.example phpunit.xml
	cp laravel-echo-server-local.json laravel-echo-server.json

sail_install:
	docker run --rm \
		-u "$(id -u):$(id -g)" \
		-v $(pwd):/var/www/html \
		-v "$(pwd)/../packages:/var/packages" \
		-w /var/www/html \
		laravelsail/php81-composer:latest \
		composer install --ignore-platform-reqs --no-autoloader

setup_staging:
	cp .env.staging .env

clear_cache:
	php artisan clear-compiled
	php artisan optimize:clear
	php artisan cache:clear

setup:
	sudo chmod -R 777 ./storage
	composer install
	composer dump-autoload
	php artisan migrate --seed
	npm install
	php artisan storage:link
	php artisan passport:install

update:
	composer install
	composer dump-autoload
	php artisan clear-compiled
	php artisan optimize:clear
	php artisan cache:clear
	php artisan migrate
	php artisan app:sync-permissions
	php artisan app:diagnostics
	make ide
	make local_sync_db
	. ${HOME}/.nvm/nvm.sh && nvm use && npm install && npm run hot

refresh:
	composer install
	php artisan cache:clear
	php artisan config:clear
	php artisan view:clear
	php artisan route:clear
	php artisan optimize:clear
	composer dump-autoload

permission:
	sudo chmod -R 777 ./storage
	sudo chmod -R a+rwx ./storage
	sudo chmod -R ugo+rw ./storage

hot:
	make ide
	php artisan app:diagnostics
	. ${HOME}/.nvm/nvm.sh && nvm use && npm install && npm run hot

ide:
	php artisan clear-compiled
	php artisan ide-helper:models --nowrite
	php artisan ide-helper:generate

qa:
	composer install
	php artisan optimize
	php artisan migrate --force
	. ${HOME}/.nvm/nvm.sh && nvm use && npm install && npm run prod

quick_lint:
	php ./vendor/bin/php-cs-fixer fix -vvv --show-progress=dots --rules=no_unused_imports

deploy:
	php artisan down
	git fetch origin deploy
	git reset --hard origin/deploy
	composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
	php artisan migrate --force
	php artisan optimize:clear
	php artisan auth:clear-resets
	php artisan queue:restart
	php artisan websocket:restart
	php artisan optimize
	php artisan event:cache
	php artisan app:sync-permissions
	php artisan app:diagnostics
	php artisan up

sync_permissions:
	php artisan app:sync-permissions

# Put artisan commands here...for updating/inserting new DB columns
local_sync_db:
	php artisan delivery-ticket:fill-purchasing-and-selling-price
	php artisan app:move-invoice-deductions-values
	php artisan invoice:save-summaries
	php artisan app:add_delivery_ticket_totals

build:
	. ${HOME}/.nvm/nvm.sh && nvm use && npm install && npm run build

watch_invoice:
	ls /home/goper/Desktop/kin/sowidu/app-web/resources/css/views/invoice-pdf.css | entr npm run prod

watch_offer:
	ls /home/goper/Desktop/kin/sowidu/app-web/app/Modules/Offer/resources/css/pdf.css | entr npm run prod


queue_clear:
	php artisan queue:clear --queue=default
	php artisan queue:clear --queue=high
	php artisan queue:clear --queue=broadcasts
	php artisan queue:clear --queue=emails
	php artisan queue:clear --queue=low

test:
	./vendor/bin/phpunit $(ARGS)

analyze:
	./vendor/bin/phpstan analyze $(filter-out $@,$(MAKECMDGOALS))

%:
	@:
