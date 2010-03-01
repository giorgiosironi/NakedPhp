<?php
require_once __DIR__ . '/../application/bootstrap.php';
require __DIR__ . '/cli-config.php';

$cli = new \Doctrine\Common\Cli\CliController($cliConfiguration);
$cli->run($_SERVER['argv']);
