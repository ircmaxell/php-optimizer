<?php

/*
 * This file is part of PHP-Optimizer, a PHP CFG Optimizerf or PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPOptimizer;

use PHPCfg\Traverser;

class Optimizer {
    
    protected $traverser;

    public function __construct() {
        $this->traverser = new Traverser;
        $this->traverser->addVisitor(new Visitor\ConstAssignResolver);
        $this->traverser->addVisitor(new Visitor\ConstBinaryOpResolver);
        $this->traverser->addVisitor(new Visitor\ConstBitwiseNotResolver);
        $this->traverser->addVisitor(new Visitor\ConstBooleanNotResolver);
        $this->traverser->addVisitor(new Visitor\ConstConstFetchResolver);
        $this->traverser->addVisitor(new Visitor\ConstJumpIfResolver);
        $this->traverser->addVisitor(new Visitor\ConstUnaryMinusResolver);
        $this->traverser->addVisitor(new Visitor\ConstUnaryPlusResolver);
        $this->traverser->addVisitor(new Visitor\JumpBlockEliminator);
    }

    public function optimize(array $blocks) {
        for ($i = 0; $i < count($blocks); $i++) {
            $blocks[$i] = $this->traverser->traverse($blocks[$i]);
        }
        return $blocks;
    }

}