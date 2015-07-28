<?php

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

$astTraverser = new PhpParser\NodeTraverser;
$astTraverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);
$parser = new PHPCfg\Parser((new ParserFactory)->create(ParserFactory::PREFER_PHP7), $astTraverser);

$traverser = new PHPCfg\Traverser;
$traverser->addVisitor(new PHPCfg\Visitor\Simplifier);

$typeReconstructor = new PHPTypes\TypeReconstructor;
$dumper = new PHPCfg\Printer\Text();

$block = $parser->parse($code, __FILE__);
$traverser->traverse($block);
$state = new PHPTypes\State([$block]);
$typeReconstructor->resolve($state);

echo $dumper->printCFG($state->blocks);

echo "\nOptimizing\n";
$optimizer = new PHPOptimizer\Optimizer;

$blocks = $optimizer->optimize($state->blocks);


echo $dumper->printCFG($blocks);