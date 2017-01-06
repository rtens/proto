<?php

$repo = __DIR__ . '/.git';
$binDir = __DIR__ . '/bin';
$sigFile = $binDir . '/composer-installer.sig';
$setupFile = $binDir . '/composer-setup.php';
$composerPhar = $binDir . '/composer.phar';
$composerJson = __DIR__ . '/composer.json';

if (!file_exists($composerPhar)) {
    @mkdir($binDir);
    copy('https://composer.github.io/installer.sig', $sigFile);
    copy('https://getcomposer.org/installer', $setupFile);

    if (hash_file('SHA384', $setupFile) === trim(file_get_contents($sigFile))) {
        echo 'Installing composer to bin/composer.phar';
    } else {
        echo 'Installer corrupt';
        unlink($setupFile);
        exit(1);
    }
    echo PHP_EOL;

    exec('php ' . $setupFile);

    unlink($setupFile);
    unlink($sigFile);

    rename(__DIR__ . '/composer.phar', $composerPhar);
}

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
}

echo "Installing dependencies (takes a while)" . PHP_EOL;

exec("php bin/composer.phar install");

$replaceNamespaceIn = [
    'index.php',
    'src',
    'spec'
];

// TODO replace vendor\sample with $vendor\$project
// TODO Delete setup.php
// TODO Reset .git