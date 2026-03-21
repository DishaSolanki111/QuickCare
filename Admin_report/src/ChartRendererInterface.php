<?php

namespace PHPMaker2026\Project2;

/**
 * Chart renderer interface
 */
interface ChartRendererInterface
{

    public function setChart(DbChart $chart): static;

    public function getContainer(int $width, int $height): string;

    public function getScript(int $width, int $height): string;
}
