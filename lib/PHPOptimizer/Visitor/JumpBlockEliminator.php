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
use PHPCfg\Visitor;
use PHPTypes\Type;

class JumpBlockEliminator implements Visitor {
    
    public function beforeTraverse(Block $block) {}

    public function afterTraverse(Block $block) {}
    
    public function enterBlock(Block $block, Block $prior = null) {}

    public function enterOp(Op $op, Block $block) {}

    public function leaveOp(Op $op, Block $block) {}

    public function leaveBlock(Block $block, Block $prior = null) {
        if (count($block->children) !== 1 || !$block->children[0] instanceof Op\Stmt\Jump) {
            return;
        }
        $originalJump = $block->children[0];
        // It's a constant jump block!
        foreach ($block->parents as $parent) {
            $jump = end($parent->children);
            if ($jump instanceof Op\Stmt\Jump) {
                $jump->target = $originalJump->target;
            } elseif ($jump instanceof Op\Stmt\JumpIf) {
                if ($jump->if === $block) {
                    $jump->if = $originalJump->target;
                } else {
                    $jump->else = $originalJump->target;
                }
            } elseif ($jump instanceof Op\Stmt\Switch_) {
                // TODO: optimize switches
                return;
            } else {
                throw new \RuntimeException("Unknown parent jump type: " . get_class($jump));
            }
            $this->addParent($originalJump->target, $parent);
        }

        foreach ($block->phi as $phi) {
            // move phi nodes to target block
            $add = true;
            foreach ($originalJump->target->phi as $subPhi) {
                if ($subPhi->hasOperand($phi->result)) {
                    $add = false;
                    $subPhi->removeOperand($phi->result);
                    foreach ($phi->vars as $var) {
                        $subPhi->addOperand($var);
                    }
                }
            }
            if ($add) {
                $originalJump->target->phi[] = $phi;
            }
        }

        $this->removeParent($originalJump->target, $block);
        return $originalJump->target;
    }

    public function skipBlock(Block $block, Block $prior = null) {}

    protected function addParent(Block $block, Block $parent) {
        if (!in_array($parent, $block->parents, true)) {
            $block->parents[] = $parent;
        }
    }

    protected function removeParent(Block $block, Block $parent) {
        $k = array_search($parent, $block->parents, true);
        if ($k !== false) {
            unset($block->parents[$k]);
            $block->parents = array_values($block->parents);
        }
    }

}