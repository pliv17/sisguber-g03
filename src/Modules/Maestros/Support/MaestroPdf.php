<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Support;

use Dompdf\Dompdf;
use Dompdf\Options;

final class MaestroPdf
{
    /**
     * @param list<string>                     $headers
     * @param list<list<string|int|float>> $rows
     */
    public static function stream(string $filename, string $title, array $headers, array $rows): never
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $html = self::buildHtml($title, $headers, $rows);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . str_replace(['"', "\n"], '', $filename) . '"');
        echo $dompdf->output();
        exit;
    }

    /**
     * @param list<string>                     $headers
     * @param list<list<string|int|float>> $rows
     */
    private static function buildHtml(string $title, array $headers, array $rows): string
    {
        $esc = static function (string $s): string {
            return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $th = '';
        foreach ($headers as $h) {
            $th .= '<th style="border:1px solid #ccc;padding:4px;background:#eee;">' . $esc((string) $h) . '</th>';
        }
        $body = '';
        foreach ($rows as $row) {
            $body .= '<tr>';
            foreach ($row as $cell) {
                $body .= '<td style="border:1px solid #ccc;padding:4px;">' . $esc((string) $cell) . '</td>';
            }
            $body .= '</tr>';
        }

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . $esc($title) . '</title></head><body>'
            . '<h2 style="font-family:DejaVu Sans;">' . $esc($title) . '</h2>'
            . '<table style="border-collapse:collapse;width:100%;font-size:10px;font-family:DejaVu Sans;">'
            . '<thead><tr>' . $th . '</tr></thead><tbody>' . $body . '</tbody></table>'
            . '</body></html>';
    }
}
