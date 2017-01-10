<?php

$binDir = __DIR__ . '/bin';
$sigFile = $binDir . '/composer-installer.sig';
$setupFile = $binDir . '/composer-setup.php';
$composerPhar = $binDir . '/composer.phar';

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

if (!isset($IS_SETUP)) {
    passthru("php \"$composerPhar\" install");
    echo PHP_EOL . "Done. Execute \n\tsh runspec.sh\nto run the tests and \n\tsh rundev.sh\nto run the application" . PHP_EOL;
}