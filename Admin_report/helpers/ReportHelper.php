<?php

namespace PHPMaker2026\Project2;

/**
 * ReportHelper.php
 * Save to: C:\xampp\htdocs\QuickCare\QuickCare\Admin_report\helpers\ReportHelper.php
 *
 * IMPORTANT: No @page CSS anywhere in this file.
 * Paper size and orientation are controlled solely by ExportPdf.php setPaper().
 */

if (class_exists(__NAMESPACE__ . '\ReportHelper')) {
    return;
}

class ReportHelper
{
    // ================================================================
    // PRIVATE: Current date/time in IST (Asia/Kolkata)
    // Change timezone string if needed: https://www.php.net/manual/en/timezones.php
    // ================================================================
    private static function now(): string
    {
        try {
            return (new \DateTime("now", new \DateTimeZone("Asia/Kolkata")))->format("d M Y, h:i A");
        } catch (\Exception $e) {
            return date("d M Y, h:i A");
        }
    }

    // ================================================================
    // PRIVATE: Get current logged-in PHPMaker user name
    // ================================================================
    private static function currentUser(): string
    {
        if (function_exists('PHPMaker2026\Project2\CurrentUserName')) {
            return CurrentUserName() ?: "Admin";
        }
        return "Admin";
    }

    // ================================================================
    // PRIVATE: Resolve report title — 4 fallback levels:
    //   1. Explicit $title passed by caller
    //   2. $page->pageHeading()  — Caption set in PHPMaker General tab
    //   3. $page->TableName      — Raw table/view name
    //   4. $page->TableVar       — Strips vw_ prefix, formats with spaces
    // ================================================================
    private static function resolveTitle(object $page, string $title): string
    {
        if ($title !== "") {
            return $title;
        }
        if (method_exists($page, 'pageHeading') && trim($page->pageHeading()) !== "") {
            return trim($page->pageHeading());
        }
        if (isset($page->TableName) && trim($page->TableName) !== "") {
            return trim($page->TableName);
        }
        if (isset($page->TableVar) && trim($page->TableVar) !== "") {
            $name = $page->TableVar;
            if (stripos($name, 'vw_') === 0) {
                $name = substr($name, 3);
            }
            return ucwords(str_replace('_', ' ', $name));
        }
        return "Report";
    }

    // ================================================================
    // PUBLIC: SCREEN HEADER (browser view)
    //
    // Paste in DataRendering event:
    //   $this->setHeader(ReportHelper::screenHeader($this));
    //
    // With custom title:
    //   $this->setHeader(ReportHelper::screenHeader($this, "Patient Summary"));
    //
    // With specific filter fields shown:
    //   $this->setHeader(ReportHelper::screenHeader($this, "", [
    //       "Date"   => "appointment_date",
    //       "Doctor" => "doctor_name",
    //   ]));
    // ================================================================
    public static function screenHeader(object $page, string $title = "", array $fields = []): string
    {
        $title   = self::resolveTitle($page, $title);
        $user    = self::currentUser();
        $gen     = self::now();
        $filters = self::screenFilters($page, $fields);

        return '
<center>
<div style="font-family:Arial,sans-serif;background:#fff;border:2px solid #0d6efd;
    border-radius:8px;padding:20px 28px 16px;margin-bottom:20px;width:96%;
    text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.07);">

    <div style="margin-bottom:12px;">
        <img src="images/logo.png" height="60px" alt="QuickCare"
             onerror="this.style.display=\'none\'">
    </div>

    <hr style="border:0;border-top:2px solid #0d6efd;margin:8px 0;">

    <div style="font-size:24px;font-weight:bold;color:#0d6efd;margin-bottom:4px;">
        <h2 style="margin:0;">' . htmlspecialchars($title) . '</h2>
    </div>

    <hr style="border:0;border-top:2px solid #0d6efd;margin:8px 0;">

    <div style="font-size:10px;color:#999;text-transform:uppercase;letter-spacing:3px;margin-bottom:8px;">
        QuickCare &mdash; Analytics &amp; Reports
    </div>

    <div style="font-size:13px;color:#333;margin-bottom:6px;">
        <strong>User:</strong> ' . htmlspecialchars($user) . '
        &nbsp;|&nbsp;
        <strong>Generated:</strong> ' . $gen . '
    </div>

    <div style="font-size:12px;color:#555;margin-top:6px;padding-top:6px;border-top:1px solid #dee2e6;">
        ' . $filters . '
    </div>
</div>
</center>';
    }

    // ================================================================
    // PUBLIC: SCREEN FOOTER (browser view)
    //
    // Paste in DataRendered event:
    //   $this->setFooter(ReportHelper::screenFooter($this));
    // ================================================================
    public static function screenFooter(object $page, string $title = ""): string
    {
        $title = self::resolveTitle($page, $title);

        return '
<center>
<div style="font-family:Arial,sans-serif;border-top:2px solid #0d6efd;margin-top:20px;
    padding-top:10px;width:96%;font-size:11px;color:#555;
    display:flex;justify-content:space-between;">
    <span><strong>QuickCare</strong> &mdash; ' . htmlspecialchars($title) . '</span>
    <span>Generated: ' . self::now() . '</span>
</div>
</center>';
    }

    // ================================================================
    // PUBLIC: PDF HEADER
    //
    // Paste in DataRendering event (inside if $this->Export == "pdf"):
    //   $this->setHeader(ReportHelper::pdfHeader($this));
    //
    // NOTE: NO @page CSS — orientation set via ExportPdf.php setPaper() only
    // ================================================================
    public static function pdfHeader(object $page, string $title = "", array $fields = []): string
    {
        $title   = self::resolveTitle($page, $title);
        $user    = self::currentUser();
        $gen     = self::now();
        $filters = self::pdfFilters($page, $fields);

        // Absolute server path required so dompdf can load the image from disk
        $logo = ServerMapPath("images/logo.png");

        $filterRow = $filters !== ''
            ? '<tr><td align="center" style="font-size:9px;color:#555;padding:3px 0 5px;font-style:italic;">'
                . htmlspecialchars($filters) . '</td></tr>'
            : '<tr><td align="center" style="font-size:9px;color:#999;padding:3px 0 5px;font-style:italic;">'
                . 'No filters applied &mdash; showing all records</td></tr>';

        // Body-level CSS only — absolutely NO @page block here
        $css = '
<style>
    body {
        font-family : Arial, sans-serif;
        font-size   : 10px;
        color       : #222;
        margin      : 0;
        padding     : 0;
    }
    table.ew-table {
        width           : 100%;
        border-collapse : collapse;
        table-layout    : auto;
    }
    table.ew-table th {
        background-color : #0d6efd;
        color            : #ffffff;
        padding          : 5px 6px;
        font-size        : 9px;
        text-align       : left;
    }
    table.ew-table td {
        padding       : 4px 6px;
        font-size     : 9px;
        border-bottom : 1px solid #e0e0e0;
    }
    table.ew-table tr.ew-table-alt-row td {
        background-color : #f0f5ff;
    }
</style>';

        $header = '
<center>
<table width="100%" cellpadding="0" cellspacing="0"
    style="font-family:Arial,sans-serif;border:2px solid #0d6efd;margin-bottom:12px;">

    <tr>
        <td align="center" style="padding:10px 0 8px;background:#ffffff;">
            <img src="' . htmlspecialchars($logo) . '"
                 style="height:50px;width:auto;display:block;margin:0 auto;"
                 alt="QuickCare">
        </td>
    </tr>

    <tr>
        <td align="center"
            style="background:#0d6efd;padding:10px 16px;font-size:18px;
                   font-weight:bold;color:#ffffff;letter-spacing:0.5px;">
            ' . htmlspecialchars($title) . '
        </td>
    </tr>

    <tr>
        <td align="center"
            style="background:#e8f0fe;padding:5px 0;font-size:8px;color:#333;
                   text-transform:uppercase;letter-spacing:2px;border-bottom:1px solid #ccc;">
            QuickCare &mdash; Analytics &amp; Reports
        </td>
    </tr>

    <tr>
        <td align="center" style="padding:6px 0;font-size:10px;color:#333;">
            <strong>User:</strong> ' . htmlspecialchars($user) . '
            &nbsp;|&nbsp;
            <strong>Generated:</strong> ' . $gen . '
        </td>
    </tr>

    ' . $filterRow . '

</table>
</center>';

        return $css . $header;
    }

    // ================================================================
    // PUBLIC: PDF FOOTER
    // Returns a tiny spacer only.
    // The real footer (QuickCare · Report · Page X of Y) is drawn by
    // ExportPdf.php via dompdf canvas API on every page automatically.
    // ================================================================
    public static function pdfFooter(object $page, string $title = ""): string
    {
        return '<div style="height:2px;"></div>';
    }

    // ================================================================
    // PRIVATE: Extract filter value from a single PHPMaker field object
    // ================================================================
    private static function getFieldFilter(object $fld): array
    {
        $v        = '';
        $v2       = '';
        $operator = '=';

        if (isset($fld->AdvancedSearch) && is_object($fld->AdvancedSearch)) {
            $v        = (string)($fld->AdvancedSearch->SearchValue  ?? '');
            $v2       = (string)($fld->AdvancedSearch->SearchValue2 ?? '');
            $operator = (string)($fld->AdvancedSearch->SearchOperator ?? '=');
        }

        // Pipe-separated multi-value → comma-separated display
        if ($v !== '' && str_contains($v, '|')) {
            $v = implode(', ', array_map('trim', explode('|', $v)));
        }

        return compact('v', 'v2', 'operator');
    }

    // ================================================================
    // PRIVATE: Auto-discover all fields that currently have a filter set
    // ================================================================
    private static function discoverFields(object $page): array
    {
        $fields = [];
        foreach (get_object_vars($page) as $name => $fld) {
            if (!is_object($fld) || !isset($fld->AdvancedSearch)) {
                continue;
            }
            $v  = (string)($fld->AdvancedSearch->SearchValue  ?? '');
            $v2 = (string)($fld->AdvancedSearch->SearchValue2 ?? '');
            if ($v !== '' || $v2 !== '') {
                $label          = ucwords(str_replace('_', ' ', $name));
                $fields[$label] = $name;
            }
        }
        return $fields;
    }

    // ================================================================
    // PRIVATE: Filter badges for screen view (styled HTML)
    // ================================================================
    private static function screenFilters(object $page, array $fields): string
    {
        $out = [];

        if (isset($page->BasicSearch) && is_object($page->BasicSearch)) {
            $kw = (string)($page->BasicSearch->Keyword ?? '');
            if ($kw !== '') {
                $out[] = '<strong>Search:</strong> '
                    . '<span style="background:#fff3cd;border:1px solid #ffc107;border-radius:3px;padding:1px 6px;">'
                    . htmlspecialchars($kw) . '</span>';
            }
        }

        $resolved = empty($fields) ? self::discoverFields($page) : $fields;

        foreach ($resolved as $label => $name) {
            if (!isset($page->$name)) continue;
            ['v' => $v, 'v2' => $v2, 'operator' => $op] = self::getFieldFilter($page->$name);
            if ($v !== '' && $v2 !== '') {
                $out[] = '<strong>' . htmlspecialchars($label) . ':</strong> '
                    . '<span style="background:#e8f0fe;border:1px solid #c5d5f5;border-radius:3px;padding:1px 6px;">'
                    . htmlspecialchars($v) . '</span>'
                    . ' <strong>to</strong> '
                    . '<span style="background:#e8f0fe;border:1px solid #c5d5f5;border-radius:3px;padding:1px 6px;">'
                    . htmlspecialchars($v2) . '</span>';
            } elseif ($v !== '') {
                $out[] = '<strong>' . htmlspecialchars($label) . '</strong>'
                    . ' <em style="color:#666;">' . htmlspecialchars($op) . '</em> '
                    . '<span style="background:#e8f0fe;border:1px solid #c5d5f5;border-radius:3px;padding:1px 6px;">'
                    . htmlspecialchars($v) . '</span>';
            }
        }

        return count($out)
            ? '<strong>Filters:</strong> &nbsp;' . implode(' &nbsp;|&nbsp; ', $out)
            : '<em style="color:#999;">No filters applied &mdash; showing all records</em>';
    }

    // ================================================================
    // PRIVATE: Filter text for PDF (plain text, no HTML tags)
    // ================================================================
    private static function pdfFilters(object $page, array $fields): string
    {
        $out = [];

        if (isset($page->BasicSearch) && is_object($page->BasicSearch)) {
            $kw = (string)($page->BasicSearch->Keyword ?? '');
            if ($kw !== '') {
                $out[] = 'Search: ' . $kw;
            }
        }

        $resolved = empty($fields) ? self::discoverFields($page) : $fields;

        foreach ($resolved as $label => $name) {
            if (!isset($page->$name)) continue;
            ['v' => $v, 'v2' => $v2, 'operator' => $op] = self::getFieldFilter($page->$name);
            if ($v !== '' && $v2 !== '') {
                $out[] = $label . ': ' . $v . ' to ' . $v2;
            } elseif ($v !== '') {
                $out[] = $label . ' ' . $op . ' ' . $v;
            }
        }

        return count($out) ? 'Filters: ' . implode(' | ', $out) : '';
    }
}
