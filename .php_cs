<?php

$header = <<<'EOF'
TimeParser (https://wapmorgan.github.io/TimeParser/)

@link      https://github.com/wapmorgan/TimeParser
@copyright Copyright (c) 2014-2019 wapmorgan
@license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
EOF;

// $header = <<<'EOF'
// This file is part of wapmorgan TimeParser.

// (c) wapmorgan <wapmorgan@gmail.com>

// This source file is subject to the MIT license that is bundled
// with this source code in the file LICENSE.
// EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.DIRECTORY_SEPARATOR.'src')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(false)
    ->setRules([
        '@Symfony'       => true,
        '@Symfony:risky' => false,
        'array_syntax'   => ['syntax' => 'short'],
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc'],
        'ordered_class_elements' => true,
        'ordered_imports'        => true,
        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
        ],
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
