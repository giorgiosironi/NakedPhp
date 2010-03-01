<?php
$classLoader = new \Doctrine\Common\IsolatedClassLoader('Proxies');
$classLoader->setBasePath(__DIR__ . '/../application/');
$classLoader->register();

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');

$connectionOptions = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../database/database.sqlite'
);

// These are required named variables (names can't change!)
$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$globalArguments = array(
    'class-dir' => __DIR__ . '/../application/models'
);
$cliConfiguration = new \Doctrine\Common\Cli\Configuration();
$cliConfiguration->setAttribute('em', $em);
$cliConfiguration->setAttribute('globalArguments', $globalArguments);
