<?php

namespace ChessServer\Command;

class HeuristicsBarByFenStringCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics_bar_fen';
        $this->description = "Takes an expanded heuristic picture of the current position.";
        $this->params = [
            'fen' => 'string',
        ];
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
