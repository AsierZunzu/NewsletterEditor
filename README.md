# Newsletter Editor

## Running the app

Follow instructions at [symfony-docker project's README](https://github.com/dunglas/symfony-docker/blob/main/README.md):

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up -d` to start the containers
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)

## Load test data

1. Run `bin/console doctrine:fixtures:load` to load data fixtures. More info at `src\DataFixtures`.

## Running the linter

Once installed (`composer install --working-dir=tools/linter`) run the linter by executing `tools/linter/vendor/bin/php-cs-fixer fix src`

You can also run it on a pre-build Docker image wih `docker run -it --rm -v $(pwd):/code ghcr.io/php-cs-fixer/php-cs-fixer:${FIXER_VERSION:-3-php8.3} fix src` (on Powershell/Windows replace `$(pwd)` with `${pwd}`)

## License

WIP

## Credits

Created by [Asier Zunzunegui](https://github.com/AsierZunzu)
