<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Optimizer, a PHP CFG Optimizer for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPOptimizer;

use PHPCfg\Op;
use PHPCfg\Operand;

abstract class Helper
{
    public static function replaceVar(Operand $from, Operand $to)
    {
        foreach ($from->usages as $usage) {
            foreach ($usage->getVariableNames() as $varName) {
                $vars = $usage->{$varName};
                $newVars = [];
                if (! is_array($vars)) {
                    $vars = [$vars];
                }
                foreach ($vars as $key => $value) {
                    if ($value === $from) {
                        $newVars[$key] = $to;
                        $to->addUsage($usage);
                    } else {
                        $newVars[$key] = $value;
                    }
                }

                if (! is_array($usage->{$varName})) {
                    $usage->{$varName} = array_shift($newVars);
                } else {
                    $usage->{$varName} = $newVars;
                }
            }
        }
    }

    public static function removeUsage(Operand $var, Op $op)
    {
        foreach ($op->getVariableNames() as $varName) {
            $vars = $op->{$varName};
            $newVars = [];
            if (! is_array($vars)) {
                $vars = [$vars];
            }
            foreach ($vars as $key => $value) {
                if ($value !== $var) {
                    $newVars[$key] = $value;
                }
            }

            if (! is_array($op->{$varName})) {
                $op->{$varName} = array_shift($newVars);
            } else {
                $op->{$varName} = array_keys($newVars);
            }
        }
    }
}
