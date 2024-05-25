<?php

use Castor\Attribute\AsTask;

use function Castor\capture;
use function Castor\run;

$_ENV['DOCKER_COMPOSE'] = 'docker compose';
$_ENV['APP_VERSION'] = capture('git rev-parse --verify --short=8 HEAD');

function docker_run(): void
{
    run('APP_VERSION='.$_ENV['APP_VERSION'].' '.$_ENV['DOCKER_COMPOSE'].' -f docker-compose.prod.yaml ');
}


#[AsTask(description: 'Build the application.')]
function build(): void
{
    docker_run('build');
    docker_run('build php');
}

#[AsTask(description: 'Install the application.')]
function install(): void
{
    run('run --rm --no-deps php composer install');
}

#[AsTask(description: 'Start the application.')]
function start(): void
{
    run('docker network create kemeter || true');
    run('docker compose up -d');
}

#[AsTask(description: 'Start the application.')]
function shell(string $container = 'php'): void
{
    run('docker compose exec '.$container.' bash');
}

#[AsTask(description: 'Start the application.')]
function logs(string $container = 'php'): void
{
    run('docker compose logs '.$container);
}

#[AsTask(description: 'Fix permissions')]
function permissions(): void
{
    run('exec php chown -R www-data:www-data var/cache');
    run($_ENV['DOCKER_COMPOSE'].' exec php chown -R www-data:www-data var/log');
}

#[AsTask()]
function phpstan(): void
{
    run($_ENV['DOCKER_COMPOSE'].' run --rm --no-deps php vendor/bin/phpstan analyse -l 8 src');
}

#[AsTask(description: 'Push the application.')]
function push(): void
{
    docker_run('APP_VERSION='.$_ENV['APP_VERSION'].' '.$_ENV['DOCKER_COMPOSE'].' -f docker-compose.prod.yaml push');
    docker_run('APP_VERSION='.$_ENV['APP_VERSION'].' '.$_ENV['DOCKER_COMPOSE'].' -f docker-compose.yaml push');
}

#[AsTask(description: 'PHP cs fixer.')]
function csfixer(): void
{
    run($_ENV['DOCKER_COMPOSE'].' run --rm --no-deps php vendor/bin/php-cs-fixer fix');
}

#[AsTask(description: 'Run cs-fixer.')]
function fixer(): void
{
    run($_ENV['DOCKER_COMPOSE'].' run --rm --no-deps php vendor/bin/rector');
    csfixer();
}

#[AsTask(description: 'Load database fixture.')]
function database_fixture_load(): void
{
    run($_ENV['DOCKER_COMPOSE'].' run --rm --no-deps php bin/console doctrine:fixture:load -n');
}

#[AsTask(description: 'Reset database.')]
function database_reset(): void
{
    run('exec php php bin/console doctrine:database:drop --force');
    run('exec php php bin/console doctrine:database:create');
    run('exec php php bin/console doctrine:migration:migrate --no-interaction');
}

#[AsTask(description: 'Run functional tests.')]
function test_functional(): void
{
    database_fixture_load();

    run('exec php bin/console --env=test doctrine:database:create --if-not-exists');
    run('exec php bin/console --env=test doctrine:migration:migrate --no-interaction');
    run('exec php vendor/bin/behat');
}

#[AsTask(description: 'Run unit tests.')]
function test_unit(): void
{
    run('exec php bin/phpunit tests/Unit/');
}

#[AsTask(description: 'Split')]
function split(): void
{
    run('docker run --rm -v $(PWD):/srv -v ~/.ssh:/root/.ssh jderusse/gitsplit');
}

