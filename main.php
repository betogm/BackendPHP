<?php

require_once __DIR__ . '/vendor/autoload.php';

use Reweb\Job\Backend;

$caixa = new Backend\Caixa;

echo $caixa->caixa();
