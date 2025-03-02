<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommandContainer;

class CommandContainer extends AbstractCommandContainer
{
    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AnnotationsGameCommand());
        $this->obj->attach(new AutocompleteEventCommand());
        $this->obj->attach(new AutocompletePlayerCommand());
        $this->obj->attach(new SearchCommand());
        $this->obj->attach(new StatsEventCommand());
        $this->obj->attach(new StatsOpeningCommand());
        $this->obj->attach(new StatsPlayerCommand());
    }
}
