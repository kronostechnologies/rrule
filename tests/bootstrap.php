<?php
//Files to ensure exist
$checkFiles['autoload'] = __DIR__.'/../vendor/autoload.php';

foreach($checkFiles as $file) {
	if ( ! file_exists($file)) {
		throw new RuntimeException('Install development dependencies to run test suite.');
	}
}

$autoload = require_once $checkFiles['autoload'];