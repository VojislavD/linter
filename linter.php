<?php

use Symfony\Component\Finder\Finder;

require 'vendor/autoload.php';

$finder = new Finder();
// find all files in the current directory
$finder->files()
	->in(__DIR__)
	->exclude('vendor')
	->name('*.php');

// check if there are any search results
if (! $finder->hasResults()) {
    exit;
}

$failure = false;
foreach ($finder as $file) {
    $absoluteFilePath = $file->getRealPath();
    $fileNameWithExtension = $file->getRelativePathname();
	
    $output = [];
    $exit_code = 0;
    exec('php -l ' . $absoluteFilePath . ' 2>&1', $output, $exit_code);
	
    if ($exit_code !== 0) {
	    $failure = true;
    }
    // var_dump($exit_code);
    // print_r($output);
}

exit($failure ? 1 : 0);

