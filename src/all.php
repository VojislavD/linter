<?php

$foo = [1, 2, 3];
$bar = 'bar';

var_dump($foo);
var_dump($foo, $bar);

print_r($foo);
$bar = print_r($foo, true);

var_export($foo);
$bar = var_export($foo, true);

dd($foo);
