<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Data\Pdo;
use ChessServer\Socket\ChesslaBlabSocket;

class StatsOpeningCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/stats_opening';
        $this->description = 'Stats for chess openings.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
    {
        $sql = "SELECT ECO, COUNT(*) AS total
          FROM games
          WHERE Result = '1/2-1/2'
          GROUP BY ECO
          HAVING total >= 100
          ORDER BY total DESC
          LIMIT 50";

        $drawRate = Pdo::getInstance($this->conf()['database'])
          ->query($sql)
          ->fetchAll(\PDO::FETCH_ASSOC);

        $sql = "SELECT ECO, COUNT(*) AS total
          FROM games
          WHERE Result = '1-0'
          GROUP BY ECO
          HAVING total >= 100
          ORDER BY total DESC
          LIMIT 50";

        $winRateForWhite = Pdo::getInstance($this->conf()['database'])
          ->query($sql)
          ->fetchAll(\PDO::FETCH_ASSOC);

        $sql = "SELECT ECO, COUNT(*) AS total
          FROM games
          WHERE Result = '0-1'
          GROUP BY ECO
          HAVING total >= 100
          ORDER BY total DESC
          LIMIT 50";

        $winRateForBlack = Pdo::getInstance($this->conf()['database'])
          ->query($sql)
          ->fetchAll(\PDO::FETCH_ASSOC);

        $arr = [
            $this->name => [
              'drawRate' => $drawRate,
              'winRateForWhite' => $winRateForWhite,
              'winRateForBlack' => $winRateForBlack
            ],
        ];

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
