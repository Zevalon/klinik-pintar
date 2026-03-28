<?php
class SimplePdf {
    protected static function esc($text) {
        $text = (string)$text;
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    protected static function estimateWidth($text, $size) {
        return max(0, mb_strlen((string)$text) * ($size * 0.48));
    }

    protected static function wrapText($text, $maxChars) {
        $text = trim((string)$text);
        if ($text === '') {
            return ['-'];
        }
        $words = preg_split('/\s+/', $text);
        $lines = [];
        $current = '';
        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if (mb_strlen($candidate) > $maxChars && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }
        return $lines ?: ['-'];
    }

    protected static function add(&$content, $command) {
        $content[] = $command;
    }

    protected static function text(&$content, $x, $y, $size, $text, $font = 'F1', $color = [0.11, 0.16, 0.24]) {
        list($r, $g, $b) = $color;
        self::add($content, "BT /{$font} {$size} Tf {$r} {$g} {$b} rg 1 0 0 1 {$x} {$y} Tm (" . self::esc($text) . ") Tj ET");
    }

    protected static function cellText(&$content, $x, $y, $width, $size, $text, $align = 'left', $font = 'F1', $color = [0.11, 0.16, 0.24]) {
        $text = (string)$text;
        if ($align === 'right') {
            $textX = $x + $width - 8 - self::estimateWidth($text, $size);
        } elseif ($align === 'center') {
            $textX = $x + max(8, ($width - self::estimateWidth($text, $size)) / 2);
        } else {
            $textX = $x + 8;
        }
        self::text($content, $textX, $y, $size, $text, $font, $color);
    }

    protected static function line(&$content, $x1, $y1, $x2, $y2, $color = [0.86, 0.89, 0.94], $width = 1) {
        list($r, $g, $b) = $color;
        self::add($content, "q {$width} w {$r} {$g} {$b} RG {$x1} {$y1} m {$x2} {$y2} l S Q");
    }

    protected static function rectFill(&$content, $x, $y, $width, $height, $color) {
        list($r, $g, $b) = $color;
        self::add($content, "q {$r} {$g} {$b} rg {$x} {$y} {$width} {$height} re f Q");
    }

    protected static function rectStroke(&$content, $x, $y, $width, $height, $color = [0.86, 0.89, 0.94], $lineWidth = 1) {
        list($r, $g, $b) = $color;
        self::add($content, "q {$lineWidth} w {$r} {$g} {$b} RG {$x} {$y} {$width} {$height} re S Q");
    }

    protected static function buildPdf(array $objects) {
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj . "\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
        return $pdf;
    }

    protected static function buildPdfFromStreams(array $streams, $width = 595, $height = 842) {
        $objects = [];
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj";
        $kids = [];
        $pageCount = count($streams);
        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjNo = 5 + ($i * 2);
            $kids[] = $pageObjNo . ' 0 R';
        }
        $objects[] = "2 0 obj << /Type /Pages /Kids [" . implode(' ', $kids) . "] /Count {$pageCount} >> endobj";
        $objects[] = "3 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj";
        $objects[] = "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> endobj";
        foreach ($streams as $i => $stream) {
            $pageObjNo = 5 + ($i * 2);
            $contentObjNo = 6 + ($i * 2);
            $objects[] = "{$pageObjNo} 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 {$width} {$height}] /Contents {$contentObjNo} 0 R /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> >> endobj";
            $objects[] = "{$contentObjNo} 0 obj << /Length " . strlen($stream) . " >> stream\n{$stream}\nendstream endobj";
        }
        return self::buildPdf($objects);
    }

    protected static function emitPdf($filename, $pdf) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $pdf;
        exit;
    }

    protected static function drawReportHeader(&$content, $title, array $meta, $pageNo) {
        self::rectFill($content, 0, 760, 595, 82, [0.04, 0.12, 0.24]);
        self::text($content, 40, 806, 20, $title, 'F2', [1, 1, 1]);
        self::text($content, 40, 788, 9, 'Klinik Pintar · Smart Clinic Management', 'F1', [0.83, 0.92, 0.99]);
        self::text($content, 495, 806, 9, 'PDF', 'F2', [0.74, 0.88, 1]);
        self::text($content, 470, 788, 9, 'Halaman ' . $pageNo, 'F1', [1, 1, 1]);

        self::rectFill($content, 40, 705, 515, 34, [0.97, 0.98, 1]);
        self::rectStroke($content, 40, 705, 515, 34);
        $items = array_slice($meta, 0, 3, true);
        $col = 0;
        foreach ($items as $label => $value) {
            $x = 52 + ($col * 165);
            self::text($content, $x, 726, 7, strtoupper((string)$label), 'F2', [0.39, 0.49, 0.61]);
            self::text($content, $x, 713, 9, (string)$value, 'F1', [0.11, 0.16, 0.24]);
            $col++;
        }
    }

    protected static function drawFooter(&$content, $noteLeft = '') {
        self::line($content, 40, 54, 555, 54, [0.88, 0.91, 0.95], 0.8);
        if ($noteLeft !== '') {
            self::text($content, 40, 36, 8, $noteLeft, 'F1', [0.42, 0.49, 0.57]);
        }
        self::text($content, 430, 36, 8, 'Dicetak ' . date('d M Y H:i'), 'F1', [0.42, 0.49, 0.57]);
    }

    protected static function drawStatCards(&$content, array $cards, $topY = 680) {
        if (empty($cards)) {
            return $topY;
        }
        $cardCount = count($cards);
        $gap = 12;
        $width = ($cardCount >= 3) ? 163 : floor((515 - (($cardCount - 1) * $gap)) / max(1, $cardCount));
        $height = 68;
        foreach ($cards as $index => $card) {
            $x = 40 + ($index * ($width + $gap));
            $bg = !empty($card['bg']) ? $card['bg'] : [0.97, 0.98, 1];
            $accent = !empty($card['accent']) ? $card['accent'] : [0.04, 0.47, 0.78];
            self::rectFill($content, $x, $topY - $height, $width, $height, $bg);
            self::rectStroke($content, $x, $topY - $height, $width, $height);
            self::text($content, $x + 14, $topY - 22, 8, strtoupper((string)$card['label']), 'F2', [0.39, 0.49, 0.61]);
            self::text($content, $x + 14, $topY - 48, 16, (string)$card['value'], 'F2', $accent);
        }
        return $topY - $height - 18;
    }

    protected static function drawTableSection(&$content, $title, array $columns, array $rows, $topY, $badgeText = '') {
        $rowHeight = 28;
        $displayRows = empty($rows) ? [['__empty' => true]] : $rows;
        $cardBottom = $topY - 56 - (count($displayRows) * $rowHeight) - 18;
        self::rectFill($content, 40, $cardBottom, 515, $topY - $cardBottom, [1, 1, 1]);
        self::rectStroke($content, 40, $cardBottom, 515, $topY - $cardBottom);
        self::text($content, 52, $topY - 24, 11, $title, 'F2', [0.11, 0.16, 0.24]);
        if ($badgeText !== '') {
            self::text($content, 470, $topY - 24, 8, $badgeText, 'F2', [0.04, 0.47, 0.78]);
        }

        $headerY = $topY - 54;
        self::rectFill($content, 40, $headerY, 515, 26, [0.94, 0.97, 1]);
        self::line($content, 40, $headerY, 555, $headerY, [0.86, 0.89, 0.94], 0.8);
        $x = 40;
        foreach ($columns as $column) {
            self::cellText($content, $x, $headerY + 9, $column['width'], 8, $column['label'], !empty($column['align']) ? $column['align'] : 'left', 'F2', [0.35, 0.44, 0.56]);
            $x += $column['width'];
        }

        $currentTop = $headerY;
        foreach ($displayRows as $index => $row) {
            $rowBottom = $currentTop - $rowHeight;
            if ($index % 2 === 0) {
                self::rectFill($content, 40, $rowBottom, 515, $rowHeight, [0.99, 0.995, 1]);
            }
            self::line($content, 40, $rowBottom, 555, $rowBottom, [0.93, 0.95, 0.98], 0.5);
            if (!isset($row['__empty'])) {
                $x = 40;
                foreach ($columns as $column) {
                    $key = $column['key'];
                    $align = !empty($column['align']) ? $column['align'] : 'left';
                    self::cellText($content, $x, $rowBottom + 10, $column['width'], 9, isset($row[$key]) ? $row[$key] : '-', $align, 'F1', [0.11, 0.16, 0.24]);
                    $x += $column['width'];
                }
            } else {
                self::text($content, 52, $rowBottom + 10, 9, 'Belum ada data pada periode ini.', 'F1', [0.44, 0.52, 0.61]);
            }
            $currentTop = $rowBottom;
        }
        return $cardBottom;
    }

    protected static function sectionPages($title, array $meta, array $summaryCards, $sectionTitle, array $columns, array $rows, $noteLeft = '', $pageOffset = 0) {
        $pages = [];
        $chunkSize = !empty($summaryCards) ? 11 : 15;
        $chunks = array_chunk($rows, $chunkSize);
        if (empty($chunks)) {
            $chunks = [[]];
        }
        foreach ($chunks as $index => $chunk) {
            $content = [];
            self::drawReportHeader($content, $title, $meta, $pageOffset + $index + 1);
            $topY = 680;
            if ($index === 0 && !empty($summaryCards)) {
                $topY = self::drawStatCards($content, $summaryCards, 680);
            }
            self::drawTableSection($content, $sectionTitle . (count($chunks) > 1 ? ' · Bagian ' . ($index + 1) : ''), $columns, $chunk, $topY, count($rows) . ' baris');
            self::drawFooter($content, $noteLeft);
            $pages[] = implode("\n", $content);
        }
        return $pages;
    }

    public static function download($title, $lines, $filename = 'laporan.pdf') {
        $lines = is_array($lines) ? $lines : [(string)$lines];
        $pages = [];
        $chunks = array_chunk($lines, 28);
        if (empty($chunks)) {
            $chunks = [[]];
        }
        foreach ($chunks as $index => $chunk) {
            $content = [];
            self::drawReportHeader($content, $title, ['Tanggal Cetak' => date('d-m-Y'), 'Jenis' => 'Dokumen', 'Bagian' => ($index + 1) . '/' . count($chunks)], $index + 1);
            $y = 670;
            self::rectFill($content, 40, 90, 515, 575, [1, 1, 1]);
            self::rectStroke($content, 40, 90, 515, 575);
            foreach ($chunk as $line) {
                if ($y < 110) {
                    break;
                }
                self::text($content, 52, $y, 10, (string)$line, 'F1', [0.11, 0.16, 0.24]);
                $y -= 18;
            }
            self::drawFooter($content, 'Dokumen dihasilkan otomatis oleh sistem Klinik Pintar.');
            $pages[] = implode("\n", $content);
        }
        self::emitPdf($filename, self::buildPdfFromStreams($pages));
    }

    public static function downloadInvoice(array $invoice, array $items, array $payments, $filename = 'invoice.pdf') {
        $content = [];
        self::rectFill($content, 0, 760, 595, 82, [0.04, 0.12, 0.24]);
        self::text($content, 40, 806, 22, 'Invoice Pembayaran', 'F2', [1, 1, 1]);
        self::text($content, 40, 788, 9, 'Klinik Pintar · Smart Clinic Management', 'F1', [0.83, 0.92, 0.99]);
        self::text($content, 430, 806, 10, 'No. Invoice', 'F2', [0.74, 0.88, 1]);
        self::text($content, 430, 788, 12, $invoice['invoice_no'], 'F2', [1, 1, 1]);

        self::rectFill($content, 40, 705, 515, 34, [0.97, 0.98, 1]);
        self::rectStroke($content, 40, 705, 515, 34);
        self::text($content, 52, 726, 7, 'CABANG', 'F2', [0.39, 0.49, 0.61]);
        self::text($content, 52, 713, 9, (string)($invoice['branch_name'] ?: '-'), 'F1');
        self::text($content, 235, 726, 7, 'TANGGAL KUNJUNGAN', 'F2', [0.39, 0.49, 0.61]);
        self::text($content, 235, 713, 9, format_datetime_id($invoice['visit_date']), 'F1');
        self::text($content, 418, 726, 7, 'STATUS', 'F2', [0.39, 0.49, 0.61]);
        self::text($content, 418, 713, 9, status_label($invoice['status']), 'F2', [0.04, 0.47, 0.78]);

        self::rectFill($content, 40, 610, 248, 78, [0.98, 0.99, 1]);
        self::rectStroke($content, 40, 610, 248, 78);
        self::text($content, 52, 670, 10, 'Informasi Pasien', 'F2');
        self::text($content, 52, 650, 11, (string)$invoice['patient_name'], 'F2');
        self::text($content, 52, 634, 9, 'No. RM: ' . ($invoice['medical_record_no'] ?: '-'));
        self::text($content, 52, 620, 9, 'NIK: ' . ($invoice['nik'] ?: '-') . ' · ' . gender_label($invoice['gender']));

        self::rectFill($content, 307, 610, 248, 78, [0.99, 0.99, 0.99]);
        self::rectStroke($content, 307, 610, 248, 78);
        self::text($content, 319, 670, 10, 'Ringkasan Kunjungan', 'F2');
        self::text($content, 319, 650, 9, 'Poli: ' . ($invoice['clinic_name'] ?: '-'));
        self::text($content, 319, 636, 9, 'Jenis: ' . patient_type_label($invoice['visit_type'] ?: 'umum'));
        $complaint = self::wrapText('Keluhan: ' . ($invoice['complaint'] ?: '-'), 34);
        self::text($content, 319, 622, 9, $complaint[0]);
        if (!empty($complaint[1])) {
            self::text($content, 319, 608, 8, $complaint[1], 'F1', [0.42, 0.49, 0.57]);
        }

        $invoiceItems = $items;
        if (count($invoiceItems) > 8) {
            $remaining = count($invoiceItems) - 7;
            $invoiceItems = array_slice($invoiceItems, 0, 7);
            $invoiceItems[] = [
                'description' => '+' . $remaining . ' item lainnya',
                'qty' => '',
                'unit_price' => '',
                'subtotal' => '',
            ];
        }
        $itemRows = [];
        foreach ($invoiceItems as $item) {
            $itemRows[] = [
                'description' => (string)$item['description'],
                'qty' => $item['qty'] === '' ? '' : number_format((float)$item['qty'], 0, ',', '.'),
                'unit_price' => $item['unit_price'] === '' ? '' : currency($item['unit_price']),
                'subtotal' => $item['subtotal'] === '' ? '' : currency($item['subtotal']),
            ];
        }
        self::drawTableSection($content, 'Rincian Tagihan', [
            ['label' => 'Deskripsi', 'key' => 'description', 'width' => 285],
            ['label' => 'Qty', 'key' => 'qty', 'width' => 60, 'align' => 'center'],
            ['label' => 'Harga', 'key' => 'unit_price', 'width' => 85, 'align' => 'right'],
            ['label' => 'Subtotal', 'key' => 'subtotal', 'width' => 85, 'align' => 'right'],
        ], $itemRows, 590, count($items) . ' item');

        self::rectFill($content, 325, 150, 230, 110, [0.98, 0.99, 1]);
        self::rectStroke($content, 325, 150, 230, 110);
        self::text($content, 338, 238, 10, 'Ringkasan Pembayaran', 'F2');
        self::text($content, 338, 214, 9, 'Subtotal');
        self::text($content, 470, 214, 10, currency($invoice['subtotal']), 'F2');
        self::text($content, 338, 196, 9, 'Diskon');
        self::text($content, 470, 196, 10, currency($invoice['discount']), 'F2');
        self::line($content, 338, 188, 542, 188, [0.86, 0.89, 0.94], 0.8);
        self::text($content, 338, 168, 10, 'Total Tagihan', 'F2');
        self::text($content, 446, 168, 13, currency($invoice['grand_total']), 'F2', [0.03, 0.28, 0.48]);

        self::rectFill($content, 40, 150, 270, 110, [1, 1, 1]);
        self::rectStroke($content, 40, 150, 270, 110);
        self::text($content, 52, 238, 10, 'Riwayat Pembayaran', 'F2');
        if (!empty($payments)) {
            $payY = 214;
            foreach (array_slice($payments, 0, 4) as $pay) {
                $paymentLine = format_datetime_id($pay['paid_at']) . ' · ' . strtoupper((string)$pay['payment_method']) . ' · ' . currency($pay['amount']);
                if (($pay['payment_method'] ?? '') === 'cash' && (float)$pay['amount'] > (float)$invoice['grand_total']) {
                    $paymentLine .= ' · Kembalian ' . currency((float)$pay['amount'] - (float)$invoice['grand_total']);
                }
                self::text($content, 52, $payY, 8, $paymentLine, 'F1', [0.11, 0.16, 0.24]);
                $payY -= 16;
            }
        } else {
            self::text($content, 52, 214, 9, 'Belum ada pembayaran tercatat.', 'F1', [0.42, 0.49, 0.57]);
        }

        self::drawFooter($content, 'Invoice ini dibuat otomatis dan berlaku sebagai bukti pembayaran resmi.');
        self::emitPdf($filename, self::buildPdfFromStreams([implode("\n", $content)]));
    }

    public static function downloadFinanceReport(array $meta, array $incomeRows, array $expenseRows, $filename = 'laporan-keuangan.pdf') {
        $incomeTotal = 0;
        foreach ($incomeRows as $row) {
            $incomeTotal += (float)$row['total_amount'];
        }
        $expenseTotal = 0;
        foreach ($expenseRows as $row) {
            $expenseTotal += (float)$row['total_amount'];
        }
        $summaryCards = [
            ['label' => 'Total Pendapatan', 'value' => currency($incomeTotal), 'bg' => [0.94, 0.98, 1], 'accent' => [0.04, 0.47, 0.78]],
            ['label' => 'Total Pengeluaran', 'value' => currency($expenseTotal), 'bg' => [1, 0.96, 0.97], 'accent' => [0.82, 0.13, 0.28]],
            ['label' => 'Saldo Bersih', 'value' => currency($incomeTotal - $expenseTotal), 'bg' => [0.95, 1, 0.97], 'accent' => [0.02, 0.55, 0.33]],
        ];
        $meta = [
            'Cabang' => $meta['branch_name'],
            'Periode' => $meta['period_text'],
            'Mode' => $meta['group_label'],
        ];
        $incomeMapped = [];
        foreach ($incomeRows as $row) {
            $incomeMapped[] = [
                'period' => $row['period_label'],
                'transactions' => (string)$row['transaction_count'],
                'amount' => currency($row['total_amount']),
            ];
        }
        $expenseMapped = [];
        foreach ($expenseRows as $row) {
            $expenseMapped[] = [
                'period' => $row['period_label'],
                'transactions' => (string)$row['transaction_count'],
                'amount' => currency($row['total_amount']),
            ];
        }
        $pages = [];
        $pages = array_merge($pages, self::sectionPages('Laporan Keuangan', $meta, $summaryCards, 'Pendapatan', [
            ['label' => 'Periode', 'key' => 'period', 'width' => 195],
            ['label' => 'Jumlah Transaksi', 'key' => 'transactions', 'width' => 150, 'align' => 'center'],
            ['label' => 'Total', 'key' => 'amount', 'width' => 170, 'align' => 'right'],
        ], $incomeMapped, 'Bagian ini menampilkan rekap pembayaran yang sudah diterima klinik.', 0));
        $pages = array_merge($pages, self::sectionPages('Laporan Keuangan', $meta, [], 'Pengeluaran', [
            ['label' => 'Periode', 'key' => 'period', 'width' => 195],
            ['label' => 'Jumlah Transaksi', 'key' => 'transactions', 'width' => 150, 'align' => 'center'],
            ['label' => 'Total', 'key' => 'amount', 'width' => 170, 'align' => 'right'],
        ], $expenseMapped, 'Bagian ini menampilkan rekap pengeluaran operasional cabang.', count($pages)));
        self::emitPdf($filename, self::buildPdfFromStreams($pages));
    }

    public static function downloadStockReport(array $meta, array $stockInRows, array $stockOutRows, $filename = 'laporan-stok.pdf') {
        $qtyIn = 0;
        $qtyOut = 0;
        $valueIn = 0;
        $valueOut = 0;
        foreach ($stockInRows as $row) {
            $qtyIn += (float)$row['total_qty'];
            $valueIn += (float)$row['total_value'];
        }
        foreach ($stockOutRows as $row) {
            $qtyOut += (float)$row['total_qty'];
            $valueOut += (float)$row['total_value'];
        }
        $summaryCards = [
            ['label' => 'Qty Barang Masuk', 'value' => rtrim(rtrim(number_format($qtyIn, 2, ',', '.'), '0'), ','), 'bg' => [0.94, 0.98, 1], 'accent' => [0.04, 0.47, 0.78]],
            ['label' => 'Qty Barang Keluar', 'value' => rtrim(rtrim(number_format($qtyOut, 2, ',', '.'), '0'), ','), 'bg' => [1, 0.96, 0.97], 'accent' => [0.82, 0.13, 0.28]],
            ['label' => 'Nilai Persediaan', 'value' => currency($valueIn - $valueOut), 'bg' => [0.95, 1, 0.97], 'accent' => [0.02, 0.55, 0.33]],
        ];
        $meta = [
            'Cabang' => $meta['branch_name'],
            'Periode' => $meta['period_text'],
            'Mode' => $meta['group_label'],
        ];
        $map = function($rows) {
            $mapped = [];
            foreach ($rows as $row) {
                $mapped[] = [
                    'period' => $row['period_label'],
                    'qty' => rtrim(rtrim(number_format((float)$row['total_qty'], 2, ',', '.'), '0'), ','),
                    'transactions' => (string)$row['transaction_count'],
                    'value' => currency($row['total_value']),
                ];
            }
            return $mapped;
        };
        $pages = [];
        $pages = array_merge($pages, self::sectionPages('Laporan Stok', $meta, $summaryCards, 'Barang Masuk', [
            ['label' => 'Periode', 'key' => 'period', 'width' => 145],
            ['label' => 'Qty', 'key' => 'qty', 'width' => 100, 'align' => 'center'],
            ['label' => 'Jumlah Transaksi', 'key' => 'transactions', 'width' => 120, 'align' => 'center'],
            ['label' => 'Nilai', 'key' => 'value', 'width' => 150, 'align' => 'right'],
        ], $map($stockInRows), 'Bagian ini menampilkan pergerakan stok masuk pada periode yang dipilih.', 0));
        $pages = array_merge($pages, self::sectionPages('Laporan Stok', $meta, [], 'Barang Keluar', [
            ['label' => 'Periode', 'key' => 'period', 'width' => 145],
            ['label' => 'Qty', 'key' => 'qty', 'width' => 100, 'align' => 'center'],
            ['label' => 'Jumlah Transaksi', 'key' => 'transactions', 'width' => 120, 'align' => 'center'],
            ['label' => 'Nilai', 'key' => 'value', 'width' => 150, 'align' => 'right'],
        ], $map($stockOutRows), 'Bagian ini menampilkan pergerakan stok keluar pada periode yang dipilih.', count($pages)));
        self::emitPdf($filename, self::buildPdfFromStreams($pages));
    }

    public static function downloadExpenseReport(array $meta, array $expenseRows, array $expenseItems, $filename = 'laporan-pengeluaran.pdf') {
        $expenseTotal = 0;
        $transactionTotal = 0;
        foreach ($expenseRows as $row) {
            $expenseTotal += (float)$row['total_amount'];
            $transactionTotal += (int)$row['transaction_count'];
        }
        $summaryCards = [
            ['label' => 'Total Pengeluaran', 'value' => currency($expenseTotal), 'bg' => [1, 0.96, 0.97], 'accent' => [0.82, 0.13, 0.28]],
            ['label' => 'Jumlah Transaksi', 'value' => (string)$transactionTotal, 'bg' => [0.94, 0.98, 1], 'accent' => [0.04, 0.47, 0.78]],
            ['label' => 'Detail Tercatat', 'value' => (string)count($expenseItems), 'bg' => [0.95, 1, 0.97], 'accent' => [0.02, 0.55, 0.33]],
        ];
        $meta = [
            'Cabang' => $meta['branch_name'],
            'Periode' => $meta['period_text'],
            'Mode' => $meta['group_label'],
        ];
        $expenseMapped = [];
        foreach ($expenseRows as $row) {
            $expenseMapped[] = [
                'period' => $row['period_label'],
                'transactions' => (string)$row['transaction_count'],
                'amount' => currency($row['total_amount']),
            ];
        }
        $detailMapped = [];
        foreach ($expenseItems as $item) {
            $detailMapped[] = [
                'date' => date('d-m-Y', strtotime($item['expense_date'])),
                'category' => $item['category'] ?: '-',
                'description' => trim((string)$item['description']) !== '' ? self::wrapText($item['description'], 20)[0] : '-',
                'amount' => currency($item['amount']),
            ];
        }
        $pages = [];
        $pages = array_merge($pages, self::sectionPages('Pengeluaran Cabang', $meta, $summaryCards, 'Rekap Pengeluaran', [
            ['label' => 'Periode', 'key' => 'period', 'width' => 195],
            ['label' => 'Jumlah Transaksi', 'key' => 'transactions', 'width' => 150, 'align' => 'center'],
            ['label' => 'Total', 'key' => 'amount', 'width' => 170, 'align' => 'right'],
        ], $expenseMapped, 'Rekap ini merangkum pengeluaran operasional sesuai filter yang dipilih.', 0));
        $pages = array_merge($pages, self::sectionPages('Pengeluaran Cabang', $meta, [], 'Detail Transaksi', [
            ['label' => 'Tanggal', 'key' => 'date', 'width' => 100],
            ['label' => 'Kategori', 'key' => 'category', 'width' => 110],
            ['label' => 'Deskripsi', 'key' => 'description', 'width' => 195],
            ['label' => 'Nominal', 'key' => 'amount', 'width' => 110, 'align' => 'right'],
        ], $detailMapped, 'Bagian ini menampilkan rincian transaksi pengeluaran yang tercatat.', count($pages)));
        self::emitPdf($filename, self::buildPdfFromStreams($pages));
    }
}
