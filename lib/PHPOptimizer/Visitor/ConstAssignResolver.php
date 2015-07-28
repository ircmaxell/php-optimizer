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
use PHPOptimizer\Helper;

class ConstAssignResolver implements Visitor {
    
    public function beforeTraverse(Block $block) {}

    public function afterTraverse(Block $block) {}
    
    public function enterBlock(Block $block, Block $prior = null) {}

    public function enterOp(Op $op, Block $block) {}

    public function leaveOp(Op $op, Block $block) {
        if (!$op instanceof Op\Expr\Assign) {
            return null;
        }
        if (!$op->expr instanceof Operand\Literal) {
            // Non-constant op
            return null;
        }

        Helper::replaceVar($op->var, $op->expr);
        Helper::replaceVar($op->result, $op->expr);
        Helper::removeUsage($op->expr, $op);

        return Visitor::REMOVE_OP;
    }

    public function leaveBlock(Block $block, Block $prior = null) {}

    public function skipBlock(Block $block, Block $prior = null) {}

}