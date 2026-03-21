<?php

namespace PHPMaker2026\Project2;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Page Loading Event
 */
class PageLoadingEvent extends GenericEvent
{

    public function getPage(): mixed
    {
        return $this->subject;
    }
}
