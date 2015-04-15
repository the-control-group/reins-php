<?php

require('vendor/autoload.php');
require('reins.php');

$client = new Predis\Client();

$reins = new \TCG\Reins($client);

var_dump($reins->grab('foo', 1000));
var_dump($reins->grab('foo', 1000));
var_dump($reins->grab('foo', 1000));
var_dump($reins->grab('foo', 1000));
var_dump($reins->grab('foo', 1000));