<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class SimpleXlsx
{
    public static function create(array $headers, array $rows = [], array $options = []): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx-template-');

        if ($tempPath === false) {
            throw new RuntimeException('Gagal menyiapkan file template Excel.');
        }

        $zip = new ZipArchive();

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tempPath);

            throw new RuntimeException('Gagal membuat file Excel.');
        }

        $zip->addFromString('[Content_Types].xml', self::contentTypesXml());
        $zip->addFromString('_rels/.rels', self::rootRelsXml());
        $zip->addFromString('xl/workbook.xml', self::workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRelsXml());
        $zip->addFromString('xl/styles.xml', self::stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::worksheetXml($headers, $rows, $options));
        $zip->close();

        $content = file_get_contents($tempPath);
        @unlink($tempPath);

        if ($content === false) {
            throw new RuntimeException('Gagal membaca file Excel yang baru dibuat.');
        }

        return $content;
    }

    public static function readRows(UploadedFile $file): array
    {
        $zip = new ZipArchive();

        if ($zip->open($file->getRealPath()) !== true) {
            throw new RuntimeException('File Excel gagal dibuka.');
        }

        $sharedStrings = self::parseSharedStrings(
            $zip->getFromName('xl/sharedStrings.xml') ?: null
        );

        $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($worksheet === false) {
            throw new RuntimeException('Sheet utama pada file Excel tidak ditemukan.');
        }

        return self::parseWorksheetRows($worksheet, $sharedStrings);
    }

    private static function parseSharedStrings(?string $xml): array
    {
        if (! $xml) {
            return [];
        }

        $document = simplexml_load_string($xml);

        if (! $document instanceof SimpleXMLElement) {
            return [];
        }

        $strings = [];

        foreach ($document->xpath('//*[local-name()="si"]') ?: [] as $item) {
            $parts = $item->xpath('.//*[local-name()="t"]') ?: [];
            $value = '';

            foreach ($parts as $part) {
                $value .= (string) $part;
            }

            $strings[] = $value;
        }

        return $strings;
    }

    private static function parseWorksheetRows(string $xml, array $sharedStrings): array
    {
        $document = simplexml_load_string($xml);

        if (! $document instanceof SimpleXMLElement) {
            throw new RuntimeException('Format sheet Excel tidak bisa dibaca.');
        }

        $rows = [];

        foreach ($document->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [] as $row) {
            $cells = [];

            foreach ($row->xpath('./*[local-name()="c"]') ?: [] as $cell) {
                $reference = (string) ($cell['r'] ?? '');
                $column = self::columnIndexFromReference($reference);
                $type = (string) ($cell['t'] ?? '');
                $valueNode = $cell->xpath('./*[local-name()="v"]');
                $inlineNode = $cell->xpath('./*[local-name()="is"]/*[local-name()="t"]');

                if ($type === 's') {
                    $sharedIndex = (int) ((string) ($valueNode[0] ?? '-1'));
                    $value = $sharedStrings[$sharedIndex] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = isset($inlineNode[0]) ? (string) $inlineNode[0] : '';
                } else {
                    $value = isset($valueNode[0]) ? (string) $valueNode[0] : '';
                }

                $cells[$column] = trim($value);
            }

            if ($cells === []) {
                continue;
            }

            ksort($cells);
            $maxColumn = max(array_keys($cells));
            $rowValues = [];

            for ($column = 0; $column <= $maxColumn; $column++) {
                $rowValues[] = $cells[$column] ?? '';
            }

            $rows[] = $rowValues;
        }

        return $rows;
    }

    private static function worksheetXml(array $headers, array $rows, array $options = []): string
    {
        $allRows = array_merge([$headers], $rows);
        $rowXml = [];
        $textColumns = self::normalizeColumnIndexes($options['text_columns'] ?? [], count($headers));

        foreach ($allRows as $rowNumber => $row) {
            $cells = [];

            foreach (array_values($row) as $columnIndex => $value) {
                $reference = self::columnLetter($columnIndex) . ($rowNumber + 1);
                $escapedValue = htmlspecialchars((string) $value, ENT_XML1);
                $styleId = null;

                if ($rowNumber === 0) {
                    $styleId = 1;
                } elseif (in_array($columnIndex, $textColumns, true)) {
                    $styleId = 2;
                }

                $style = $styleId === null ? '' : sprintf(' s="%d"', $styleId);

                $cells[] = sprintf(
                    '<c r="%s" t="inlineStr"%s><is><t>%s</t></is></c>',
                    $reference,
                    $style,
                    $escapedValue
                );
            }

            $rowXml[] = sprintf('<row r="%d">%s</row>', $rowNumber + 1, implode('', $cells));
        }

        $lastColumn = self::columnLetter(max(count($headers) - 1, 0));
        $lastRow = count($allRows);
        $dimension = sprintf('A1:%s%d', $lastColumn, $lastRow);
        $columnsXml = self::columnsXml($textColumns);

        return sprintf(
            <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="%s"/>
  <sheetViews>
    <sheetView workbookViewId="0"/>
  </sheetViews>
  <sheetFormatPr defaultRowHeight="15"/>
  %s
  <sheetData>
    %s
  </sheetData>
</worksheet>
XML,
            $dimension,
            $columnsXml,
            implode('', $rowXml)
        );
    }

    private static function contentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>
XML;
    }

    private static function rootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML;
    }

    private static function workbookXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Template Dosen" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML;
    }

    private static function workbookRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private static function stylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font>
      <sz val="11"/>
      <name val="Calibri"/>
    </font>
    <font>
      <b/>
      <sz val="11"/>
      <name val="Calibri"/>
    </font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/><diagonal/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="3">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    <xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>
  </cellXfs>
  <cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0"/>
  </cellStyles>
</styleSheet>
XML;
    }

    private static function columnsXml(array $textColumns): string
    {
        if ($textColumns === []) {
            return '';
        }

        $columns = array_map(function (int $columnIndex) {
            $excelColumn = $columnIndex + 1;

            return sprintf(
                '<col min="%d" max="%d" width="15" style="2" customWidth="1"/>',
                $excelColumn,
                $excelColumn
            );
        }, $textColumns);

        return sprintf('<cols>%s</cols>', implode('', $columns));
    }

    private static function normalizeColumnIndexes(array $columns, int $headerCount): array
    {
        return collect($columns)
            ->filter(fn ($column) => is_int($column) || ctype_digit((string) $column))
            ->map(fn ($column) => (int) $column)
            ->filter(fn (int $column) => $column >= 0 && $column < $headerCount)
            ->unique()
            ->values()
            ->all();
    }

    private static function columnIndexFromReference(string $reference): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($reference));
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max($index - 1, 0);
    }

    private static function columnLetter(int $index): string
    {
        $index++;
        $letters = '';

        while ($index > 0) {
            $modulo = ($index - 1) % 26;
            $letters = chr(65 + $modulo) . $letters;
            $index = intdiv($index - $modulo - 1, 26);
        }

        return Str::upper($letters);
    }
}
