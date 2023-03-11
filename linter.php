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
    $path = $file->getRelativePathname();

    $output = [];
    $exit_code = 0;
    exec('php -l '.$path.' 2>&1', $output, $exit_code);

    if ($exit_code !== 0) {
        [$line, $error] = parse_error($output);
        display_error($path, $line, $error);
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
