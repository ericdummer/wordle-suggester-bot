# docker-php-composer
To launch the container:

```docker compose up -d --build```

To rebuild it after a Docker change:

```docker compose up -d --build --remove-orphans```

To connect to the container (using bash)

```docker exec -it docker-php-composer-core-1 /bin/bash```

## tests
To run tests (when connected to the container)

```composer install``` (first time only)

```composer test```
