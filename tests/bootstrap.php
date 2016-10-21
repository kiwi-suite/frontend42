<?php
chdir(__DIR__);

date_default_timezone_set("UTC");

$loader = null;
if (file_exists('../vendor/autoload.php')) {
    include '../vendor/autoload.php';
} elseif (file_exists('../../../../vendor/autoload.php')) {
    include '../../../../vendor/autoload.php';
}
