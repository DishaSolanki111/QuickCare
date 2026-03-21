<?php

namespace PHPMaker2026\Project2;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Page Rendering Event
 */
class PageRenderingEvent extends GenericEvent
{

    public function getPage(): mixed
    {
        return $this->subject;
    }
}
