<?php

/*
 * This file is part of PHP-Optimizer, a PHP CFG Optimizerf or PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPOptimizer\Visitor;

use PHPCfg\Block;
use PHPCfg\Op;
use PHPCfg\Operand;
use PHPCfg\Visitor;

class ConstJumpIfResolver implements Visitor {
    
    public function beforeTraverse(Block $block) {}

    public function afterTraverse(Block $block) {}
    
    public function enterBlock(Block $block, Block $prior = null) {}

    public function enterOp(Op $op, Block $block) {}

    public function leaveOp(Op $op, Block $block) {
        if (!$op instanceof Op\Stmt\JumpIf) {
            return null;
        }
        if (!$op->cond instanceof Operand\Literal) {
            // Non-constant op
            return null;
        }

        // TODO: Figure out how to eliminate redundant Phi vars (eliminated phi vars)

        if ($op->cond->value) {
            return new Op\Stmt\Jump($op->if, $op->getAttributes());
        }
        return new Op\Stmt\Jump($op->else, $op->getAttributes());
    }

    public function leaveBlock(Block $block, Block $prior = null) {}

    public function skipBlock(Block $block, Block $prior = null) {}

}