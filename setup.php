<?php

$IS_SETUP = true;

$me = __FILE__;
$repo = __DIR__ . '/.git';
$binDir = __DIR__ . '/bin';
$composerPhar = $binDir . '/composer.phar';
$composerJson = __DIR__ . '/composer.json';

include "install.php";

if (!file_exists($composerJson)) {
    echo "Generating composer.json" . PHP_EOL;

    $matches = [];
    preg_match('#/home/([^/]+).*/([^/]+)$#', __DIR__, $matches);
    list(, $vendor, $project) = $matches;

    file_put_contents($composerJson, json_encode([
        "name" => "$vendor/$project",
        "minimum-stability" => "dev",
        "require" => [
            "rtens/udity" => "*"
        ],
        "require-dev" => [
            'rtens/scrut' => "*"
        ],
        "autoload" => [
            "psr-4" => [
                "$vendor\\$project\\" => "src/"
            ]
        ],
        "autoload-dev" => [
            "psr-4" => [
                "$vendor\\$project\\" => "spec/"
            ]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $replaceNamespaces = function($ins) use (&$replaceNamespaces, $vendor, $project) {
        foreach ($ins as $in) {
            if (is_dir($in)) {
                $replaceNamespaces(glob($in . '/*'));
            } else if (is_file($in) && substr($in, -4) == '.php') {
                $contents = file_get_contents($in);
                $contents = str_replace("vendor\\project", "$vendor\\$project", $contents);
                file_put_contents($in, $contents);
            }
        }
    };
    $replaceNamespaces(['index.php', 'src', 'spec']);

    echo "Installing dependencies (takes a while)" . PHP_EOL;
    exec("php \"$composerPhar\" install 2>&1");

    echo "Setting up readme.md";
    $readme = file_get_contents('readme_project.md');
    $readme = str_replace(['$vendor$', '$project$'], [$vendor, $project], $readme);
    file_put_contents('readme_project.md', $readme);
    exec("rm readme.md; mv readme_project.md readme.md");

    echo "Cleaning up";
    exec("rm \"$me\"");

    echo "Resetting git" . PHP_EOL;
    exec("rm -rf \"$repo\"; git init; git add .gitignore .travis.yml; git add *.*; git commit -m \"Project skeleton cloned from https://github.com/rtens/proto\"");

} else {

    echo "Installing dependencies (takes a while)" . PHP_EOL;
    exec("php \"$composerPhar\" install 2>&1");
}

echo "Done. Execute \n\tsh runspec.sh\nto run the tests and \n\tsh rundev.sh\nto run the application" . PHP_EOL;