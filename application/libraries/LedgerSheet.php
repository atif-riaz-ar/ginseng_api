<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LedgerSheet
{
	public function ledgersheet($post)
	{
		$CI = ci();
		$lang_model = $CI->LanguageModel;
		$report = $CI->LedgerModel->exportUniversalLedger($post);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// set columns merge
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:G1');

		// set border
		$sheet->getStyle("A1:G1")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK)->setColor(new Color('0000ff'));

		// title font changes
		$sheet->getStyle(1)->getFont()->setBold(true);
		$sheet->getStyle(1)->getFont()->setSize(24);
		$sheet->getStyle(1)->getFont()->getColor()->setARGB("0000ff");
		$sheet->getStyle(1)->getAlignment()->setHorizontal("center");
		// title set background
		$sheet->getStyle(1)->getFill()->setFillType(Fill::FILL_NONE)->getStartColor()->setARGB('ffffff');

		// header font changes
		$sheet->getStyle("A3:G3")->getFont()->setSize(12);
		$sheet->getStyle("A3:G3")->getFont()->getColor()->setARGB("ffffff");
		// header set background
		$sheet->getStyle("A3:G3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0000ff');

		$sheet->setCellValue('A1', 'Ginseng ' . ucfirst($post['type']) . ' Report');

		$sheet->setCellValue('A3', '#');
		$sheet->setCellValue('B3', 'UserId');
		$sheet->setCellValue('C3', 'Date');
		$sheet->setCellValue('D3', 'Description');
		$sheet->setCellValue('E3', 'Credit');
		$sheet->setCellValue('F3', 'Debit');
		$sheet->setCellValue('G3', 'Total');

		$rows = 4;
		$n = 1;
		foreach ($report as $val) {
			// header set background
			$bgcolor = $rows % 2 == 1 ? 'c9c9c9' : 'ffffff';
			$sheet->getStyle("A" . $rows . ":G" . $rows)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgcolor);
			$sheet->getStyle("A" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("B" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("C" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("D" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("E" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("F" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("G" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));

			$sheet->setCellValue('A' . $rows, $n++);
			$sheet->setCellValue('B' . $rows, $val['userid']);
			$sheet->setCellValue('C' . $rows, $val['insert_time']);
			$description = $val['description'];
			$trans = $lang_model->replace("en", $description);
			$description = isset($trans['en'])? $trans['en'] : $val['description'];
			$sheet->setCellValue('D' . $rows, $description);
			$sheet->setCellValue('E' . $rows, $val['credit']);
			$sheet->setCellValue('F' . $rows, $val['debit']);
			$sheet->setCellValue('G' . $rows, $val['balance']);
			$rows++;
		}
		$writer = new Xlsx($spreadsheet);
		$dir = 'assets/exports/ledgers/'.$post['type'];
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		$path = $dir . '/' . getDownloadDate() . '.xlsx';
		$writer->save($path);
		return base_url($path);
	}

}
