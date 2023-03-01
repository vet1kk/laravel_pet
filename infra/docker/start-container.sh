#!/bin/bash

set -e

role=${CONTAINER_ROLE:-app}
type=${CONTAINER_TYPE:-master}
SUPERVISOR_DIR=/var/www/infra/docker/supervisor/conf.d

chmod -R ugo+rw /var/www/bootstrap/cache /var/www/storage

# Install composer dependencies
if [ "$role" = "app" ]; then
    if [ ! -f /var/www/vendor/autoload.php ]; then
        composer install --no-scripts --no-autoloader

        composer dump-autoload --optimize
        composer run-script post-install-cmd
    fi
else
    until [ -s /var/www/vendor/autoload.php ]; do
        sleep 1
    done
fi

if [ "$role" = "app" ]; then
    echo "Running the app..."

    # Run migration and seed for master container
    if [ "$type" = "master" ]; then
        if [ "$MIGRATE" != "false" ]; then
            php artisan migrate --force
        fi

        if [ "$SEED" = "true" ]; then
            php artisan db:seed --force
        fi
    fi

    # Link local storage
    php artisan storage:link

    # Generate Swagger API doc
#    php artisan l5-swagger:generate

    # Setup supervisor for app container
    cp $SUPERVISOR_DIR/app.conf /etc/supervisor/conf.d/
    cp $SUPERVISOR_DIR/nginx.conf /etc/supervisor/conf.d/

elif [ "$role" = "worker" ]; then

    echo "Running the worker..."
    # Setup supervisor for worker container
    cp $SUPERVISOR_DIR/worker.conf /etc/supervisor/conf.d/

else
    echo "Could not match the container role \"$role\""
    exit 1
fi

# Start supervisor service
/usr/bin/supervisord --nodaemon -c /etc/supervisor/supervisord.conf
