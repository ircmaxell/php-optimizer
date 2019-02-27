<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Optimizer, a PHP CFG Optimizer for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

use PhpParser\ParserFactory;

set_time_limit(2);

$code = <<<'EOF'
<?php

$a = 1;

for ($i = 0; false; $i++) {
	$a = $a + 1;
}

return $a;

EOF;

require 'vendor/autoload.php';

$astTraverser = new PhpParser\NodeTraverser();
$astTraverser->addVisitor(new PhpParser\NodeVisitor\NameResolver());
$parser = new PHPCfg\Parser((new ParserFactory())->create(ParserFactory::PREFER_PHP7), $astTraverser);

$traverser = new PHPCfg\Traverser();
$traverser->addVisitor(new PHPCfg\Visitor\Simplifier());

$typeReconstructor = new PHPTypes\TypeReconstructor();
$dumper = new PHPCfg\Printer\Text();

$script = $parser->parse($code, __FILE__);
$traverser->traverse($script);
$state = new PHPTypes\State($script);
$typeReconstructor->resolve($state);

echo $dumper->printScript($script);

echo "\nOptimizing\n";
$optimizer = new PHPOptimizer\Optimizer();

$optimizer->optimize($script);

echo $dumper->printScript($script);
