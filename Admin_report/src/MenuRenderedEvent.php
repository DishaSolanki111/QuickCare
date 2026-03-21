<?php

namespace PHPMaker2026\Project2;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Menu Rendered Event
 */
class MenuRenderedEvent extends GenericEvent
{

    public function getMenu(): Menu
    {
        return $this->subject;
    }
}
