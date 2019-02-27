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

class ConstConstFetchResolver extends AbstractVisitor
{
    public function leaveOp(Op $op, Block $block)
    {
        if (! $op instanceof Op\Expr\ConstFetch) {
            return;
        }
        if (! $op->name instanceof Operand\Literal) {
            // Non-constant op
            return;
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
                return;
        }

        $value = new Operand\Literal($value);
        $value->type = Type::fromValue($value->value);

        Helper::replaceVar($op->result, $value);

        return Visitor::REMOVE_OP;
    }
}
