<?php

/* @var $db \Library\DB */
$db->create('users', [
    'id' => ['INTEGER', 'AUTO_INCREMENT', 'PRIMARY KEY'],
    'email' => ['VARCHAR(255)', 'UNIQUE'],
    'password' => ['TEXT'],
    'roles' => ['TEXT'],
    'token' => ['TEXT']
]);

$db->create('packages', [
    'id' => ['INTEGER', 'AUTO_INCREMENT', 'PRIMARY KEY'],
    'name' => ['VARCHAR(255)', 'UNIQUE'],
    'version' => ['VARCHAR(255)'],
    'content' => ['TEXT'],
    'owner' => ['INT']
]);