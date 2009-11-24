<?php
require_once __DIR__ . '/../application/bootstrap.php';

$cli = new \Doctrine\ORM\Tools\Cli();
$cli->run($_SERVER['argv']);
