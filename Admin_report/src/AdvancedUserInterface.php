<?php

namespace PHPMaker2026\Project2;
interface AdvancedUserInterface
{
    /**
     * Get user name
     *
     * @return string
     */
    public function userName(): string;

    /**
     * Get user ID
     *
     * @return mixed
     */
    public function userId(): mixed;

    /**
     * Get parent user ID
     *
     * @return mixed
     */
    public function parentUserId(): mixed;

    /**
     * Get user level
     *
     * @return int|string
     */
    public function userLevel(): int|string;
}
