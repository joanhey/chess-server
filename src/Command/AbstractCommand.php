<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlabSocket;

abstract class AbstractCommand
{
    protected $name;

    protected $description;

    protected $params;

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function conf()
    {
        $conf = [
            'database' => include(__DIR__.'/../../config/database.php'),
        ];

        return $conf;
    }

    abstract public function validate(array $command);

    abstract public function run(ChesslaBlabSocket $socket, array $argv, int $id);
}
