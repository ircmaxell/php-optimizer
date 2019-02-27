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
use PHPCfg\Visitor;

use PHPOptimizer\Helper;
use PHPTypes\Type;

class ConstBooleanNotResolver extends AbstractVisitor
{
    public function leaveOp(Op $op, Block $block)
    {
        if (! $op instanceof Op\Expr\BooleanNot) {
            return;
        }
        if (! $op->expr instanceof Operand\Literal) {
            // Non-constant op
            return;
        }

        $value = new Operand\Literal(! $op->expr->value);
        $value->type = Type::fromValue($newValue->value);

        Helper::replaceVar($op->result, $value);
        Helper::removeUsage($op->expr, $op);

        return Visitor::REMOVE_OP;
    }
}
