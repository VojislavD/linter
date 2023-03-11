<?php

use Symfony\Component\Finder\Finder;

require 'vendor/autoload.php';

array_shift($argv);

$files = find_files();

if (empty($files)) {
    exit;
}

$failure = false;

foreach ($files as $file) {
    $contents = file_get_contents($file);

    $found = preg_match_all('/\b(print_r
    |var_dump|var_export|dd)\(/', $contents, $matches);

    if ($found) {
        $failure = true;

        display_error($file, $matches[1]);
    }
}

exit($failure ? 1 : 0);

function display_error(string $path, array $calls)
{
    echo $path;
    echo PHP_EOL;
    echo ' - Contains calls to: ', implode(', ', array_unique($calls));
    echo PHP_EOL;
    echo PHP_EOL;
}

function find_files()
{
    global $argv;

    if ($argv) {
        return $argv;
    }

    $finder = new Finder();

    $finder->files()
        ->in(dirname(__DIR__))
        ->exclude('vendor')
        ->name('*.php');

    return array_map(fn ($file) => $file->getRelativePathname(), iterator_to_array($finder, false));
}