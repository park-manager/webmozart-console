<?php

$header = <<<EOF
This file is part of the webmozart/console package.

(c) Bernhard Schussek <bschussek@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

/** @var \Symfony\Component\Finder\Finder $finder */
$finder = PhpCsFixer\Finder::create();
$finder
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP84Migration' => true,
    ])
    ->setFinder($finder);

return $config;
