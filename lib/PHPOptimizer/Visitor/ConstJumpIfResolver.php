<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Optimizer, a PHP CFG Optimizer for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPOptimizer\Visitor;

use PHPCfg\AbstractVisitor;
use PHPCfg\Block;
use PHPCfg\Op;
use PHPCfg\Operand;

class ConstJumpIfResolver extends AbstractVisitor
{
    public function leaveOp(Op $op, Block $block)
    {
        if (! $op instanceof Op\Stmt\JumpIf) {
            return;
        }
        if (! $op->cond instanceof Operand\Literal) {
            // Non-constant op
            return;
        }

        // TODO: Figure out how to eliminate redundant Phi vars (eliminated phi vars)

        if ($op->cond->value) {
            return new Op\Stmt\Jump($op->if, $op->getAttributes());
        }

        return new Op\Stmt\Jump($op->else, $op->getAttributes());
    }
}
