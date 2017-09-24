<?php
require_once __DIR__.'/../vendor/autoload.php';

use Psr\Container\ContainerInterface;

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions([
    // Configure Application base path
    'base_path' => realpath(__DIR__.'/../'),

// Configure Configuration Factory
    \App\Services\Configuration\Configuration::class => function (ContainerInterface $c) {
        return \App\Services\Configuration\ConfigurationFactory::buildFromFilesystem($c->get('base_path') . '/config');
    },

    // Configure PSR-3 Logger Interface Implementation
    \Psr\Log\LoggerInterface::class => function (ContainerInterface $c) {
        $config = $c->get(App\Services\Configuration\Configuration::class);
        $config = $config->get('app');
        $logger = new \Monolog\Logger($config['app.name']);
        $fileHandler = new \Monolog\Handler\StreamHandler(
            $config['logs.path'],
            $config['logs.level']
        );

        $fileHandler->setFormatter(new \Monolog\Formatter\LineFormatter());
        $logger->pushHandler($fileHandler);

        return $logger;
    },
]);

$app = $containerBuilder->build();
