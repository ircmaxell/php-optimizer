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
use PHPTypes\Type;

class ConstConstFetchResolver implements Visitor {
    
    public function beforeTraverse(Block $block) {}

    public function afterTraverse(Block $block) {}
    
    public function enterBlock(Block $block, Block $prior = null) {}

    public function enterOp(Op $op, Block $block) {}

    public function leaveOp(Op $op, Block $block) {
        if (!$op instanceof Op\Expr\ConstFetch) {
            return null;
        }
        if (!$op->name instanceof Operand\Literal) {
            // Non-constant op
            return null;
        }

        $value = null;
        switch (strtolower($op->name->value)) {
            case 'true':
                $value = true;
                break;
            case 'false':
                $value = false;
                break;
            case 'null':
                $value = null;
                break;
            default:
                // TODO: try to lookup other constants at runtime
                return null;
        }

        $value = new Operand\Literal($value);
        $value->type = Type::fromValue($value->value);

        Helper::replaceVar($op->result, $value);

        return Visitor::REMOVE_OP;
    }

    public function leaveBlock(Block $block, Block $prior = null) {}

    public function skipBlock(Block $block, Block $prior = null) {}

}