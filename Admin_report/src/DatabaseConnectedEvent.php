<?php

namespace PHPMaker2026\Project2;

use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\DBAL\Driver\Connection;

/**
 * Database Connected Event
 */
class DatabaseConnectedEvent extends GenericEvent
{

    public function getConnection(): Connection
    {
        return $this->subject;
    }
}
