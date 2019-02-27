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

class ConstBinaryOpResolver extends AbstractVisitor
{
    public function leaveOp(Op $op, Block $block)
    {
        if (! $op instanceof Op\Expr\BinaryOp) {
            return;
        }
        if (! $op->left instanceof Operand\Literal || ! $op->right instanceof Operand\Literal) {
            // Non-constant op
            return;
        }
        switch ($op->getType()) {
            case 'Expr_BinaryOp_BitwiseAnd':
                $newValue = new Operand\Literal($op->left->value & $op->right->value);

                break;
            case 'Expr_BinaryOp_BitwiseOr':
                $newValue = new Operand\Literal($op->left->value | $op->right->value);

                break;
            case 'Expr_BinaryOp_BitwiseXor':
                $newValue = new Operand\Literal($op->left->value ^ $op->right->value);

                break;
            case 'Expr_BinaryOp_Coalesce':
                if ($op->left->value === null) {
                    throw new \RuntimeException('Not possible yet');
                }
                $newValue = new Operand\Literal($op->left->value);

                break;
            case 'Expr_BinaryOp_Concat':
                $newValue = new Operand\Literal($op->left->value.$op->right->value);

                break;
            case 'Expr_BinaryOp_Div':
                $newValue = new Operand\Literal($op->left->value / $op->right->value);

                break;
            case 'Expr_BinaryOp_Equal':
                $newValue = new Operand\Literal($op->left->value === $op->right->value);

                break;
            case 'Expr_BinaryOp_Greater':
                $newValue = new Operand\Literal($op->left->value > $op->right->value);

                break;
            case 'Expr_BinaryOp_GreaterOrEqual':
                $newValue = new Operand\Literal($op->left->value >= $op->right->value);

                break;
            case 'Expr_BinaryOp_Identical':
                $newValue = new Operand\Literal($op->left->value === $op->right->value);

                break;
            case 'Expr_BinaryOp_LogicalXor':
                $newValue = new Operand\Literal($op->left->value xor $op->right->value);

                break;
            case 'Expr_BinaryOp_Minus':
                $newValue = new Operand\Literal($op->left->value - $op->right->value);

                break;
            case 'Expr_BinaryOp_Mod':
                $newValue = new Operand\Literal($op->left->value % $op->right->value);

                break;
            case 'Expr_BinaryOp_Mul':
                $newValue = new Operand\Literal($op->left->value * $op->right->value);

                break;
            case 'Expr_BinaryOp_NotEqual':
                $newValue = new Operand\Literal($op->left->value !== $op->right->value);

                break;
            case 'Expr_BinaryOp_NotIdentical':
                $newValue = new Operand\Literal($op->left->value !== $op->right->value);

                break;
            case 'Expr_BinaryOp_Plus':
                $newValue = new Operand\Literal($op->left->value + $op->right->value);

                break;
            case 'Expr_BinaryOp_Pow':
                $newValue = new Operand\Literal(pow($op->left->value, $op->right->value));

                break;
            case 'Expr_BinaryOp_ShiftLeft':
                $newValue = new Operand\Literal($op->left->value << $op->right->value);

                break;
            case 'Expr_BinaryOp_ShiftRight':
                $newValue = new Operand\Literal($op->left->value >> $op->right->value);

                break;
            case 'Expr_BinaryOp_Smaller':
                $newValue = new Operand\Literal($op->left->value < $op->right->value);

                break;
            case 'Expr_BinaryOp_SmallerOrEqual':
                $newValue = new Operand\Literal($op->left->value <= $op->right->value);

                break;
            case 'Expr_BinaryOp_Spaceship':
                $value = 0;
                if ($op->left->value < $op->right->value) {
                    $value = -1;
                } elseif ($op->left->value > $op->right->value) {
                    $value = 1;
                }
                $newValue = new Operand\Literal($value);

                break;
            default:
                throw new \RuntimeException('Unknown constant op found: '.$op->getType());
        }
        $newValue->type = Type::fromValue($newValue->value);

        Helper::replaceVar($op->result, $newValue);

        return Visitor::REMOVE_OP;
    }
}
