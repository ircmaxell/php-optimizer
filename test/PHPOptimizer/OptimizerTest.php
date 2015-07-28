<?php

/*
 * This file is part of PHP-Optimizer, a PHP CFG Optimizerf or PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPOptimizer;

use PHPCfg\Parser;
use PHPCfg\Printer;
use PHPCfg\Traverser;
use PHPCfg\Visitor;
use PhpParser;
use PhpParser\ParserFactory;
use PHPTypes\State;
use PHPTypes\TypeReconstructor;

class OptimizerTest extends \PHPUnit_Framework_TestCase {

    /** @dataProvider provideTestParseAndDump */
    public function testParseAndDump($name, $code, $expectedDump) {
        $astTraverser = new PhpParser\NodeTraverser;
        $astTraverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);
        $parser = new Parser((new ParserFactory)->create(ParserFactory::PREFER_PHP7), $astTraverser);
        $block = $parser->parse($code, 'foo.php');

        $traverser = new Traverser();
        $traverser->addVisitor(new Visitor\Simplifier());
        $block = $traverser->traverse($block);

        $reconstructor = new TypeReconstructor;
        $state = new State([$block]);
        $reconstructor->resolve($state);

        $optimizer = new Optimizer;
        $blocks = $optimizer->optimize($state->blocks);

        $printer = new Printer\Text();
        $this->assertEquals(
            $this->canonicalize($expectedDump),
            $this->canonicalize($printer->printCfg($blocks))
        );
    }

    public function provideTestParseAndDump() {
        $dir = __DIR__ . '/../code';
        
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iter as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $contents = file_get_contents($file);
            yield array_merge([$file->getBasename()], explode('-----', $contents));
        }
    }

    protected function canonicalize($str) {
        // trim from both sides
        $str = trim($str);

        // normalize EOL to \n
        $str = str_replace(["\r\n", "\r"], "\n", $str);

        // trim right side of all lines
        return implode("\n", array_map('rtrim', explode("\n", $str)));
    }
}
