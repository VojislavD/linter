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
    $output = [];
    $exit_code = 0;
    exec('php -l '.$file.' 2>&1', $output, $exit_code);

    if ($exit_code !== 0) {
        [$line, $error] = parse_error($output);
        display_error($file, $line, $error);
        $failure = true;
    }
}

exit($failure ? 1 : 0);

function parse_error(array $lines): array
{
    preg_match('/PHP Parse error:\s+(?:syntax error, )?(.+?)\s+in\s+.+?\.php\s+on\s+line\s+(\d+)/', $lines[0], $matches);

    return [$matches[2], $matches[1]];
}

function display_error(string $path, int $line, string $error)
{
    echo $path;
    echo PHP_EOL;
    echo ' - Line ', $line, ': ', $error;
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
        ->in(__DIR__)
        ->exclude('vendor')
        ->name('*.php');

    return array_map(fn ($file) => $file->getRelativePathname(), iterator_to_array($finder, false));
}