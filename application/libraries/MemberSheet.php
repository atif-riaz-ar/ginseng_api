<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MemberSheet
{
	public function membersheet($post)
	{
		$CI = ci();
		$lang_model = $CI->LanguageModel;
		$report = $CI->MemberModel->exportAllMembers($post);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// set columns merge
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:H1');

		// set border
		$sheet->getStyle("A1:H1")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK)->setColor(new Color('0000ff'));

		// title font changes
		$sheet->getStyle(1)->getFont()->setBold(true);
		$sheet->getStyle(1)->getFont()->setSize(24);
		$sheet->getStyle(1)->getFont()->getColor()->setARGB("0000ff");
		$sheet->getStyle(1)->getAlignment()->setHorizontal("center");
		// title set background
		$sheet->getStyle(1)->getFill()->setFillType(Fill::FILL_NONE)->getStartColor()->setARGB('ffffff');

		// header font changes
		$sheet->getStyle("A3:H3")->getFont()->setSize(12);
		$sheet->getStyle("A3:H3")->getFont()->getColor()->setARGB("ffffff");
		// header set background
		$sheet->getStyle("A3:H3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0000ff');

		$sheet->setCellValue('A1', 'Ginseng Members Report');

		$sheet->setCellValue('A3', '#');
		$sheet->setCellValue('B3', 'WhatsApp');
		$sheet->setCellValue('C3', 'UserId');
		$sheet->setCellValue('D3', 'Name');
		$sheet->setCellValue('E3', 'Rank');
		$sheet->setCellValue('F3', 'Matrix');
		$sheet->setCellValue('G3', 'Sponsor');
		$sheet->setCellValue('H3', 'Package');

		$rows = 4;
		$n = 1;
		foreach ($report as $val) {
			// header set background
			$bgcolor = $rows % 2 == 1 ? 'c9c9c9' : 'ffffff';
			$sheet->getStyle("A" . $rows . ":H" . $rows)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgcolor);
			$sheet->getStyle("A" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("B" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("C" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("D" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("E" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("F" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("G" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("H" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));

			$sheet->setCellValue('A' . $rows, $n++);
			$sheet->setCellValue('B' . $rows, $val['mobile']);
			$sheet->setCellValue('C' . $rows, $val['userid']);
			$sheet->setCellValue('D' . $rows, $val['f_name'] . " " . $val['l_name']);
			$rnk = $val['rank_name'];
			$rnk = $lang_model->replace("en", $rnk)['en'];
			$sheet->setCellValue('E' . $rows, $rnk);
			$sheet->setCellValue('F' . $rows, !empty($val['matrix_name']) ? $val['matrix_name'] : "N/A");
			$sheet->setCellValue('G' . $rows, !empty($val['sponsor_name']) ? $val['sponsor_name'] : "N/A");
			$sheet->setCellValue('H' . $rows, $val['package_name']);
			$rows++;
		}
		$writer = new Xlsx($spreadsheet);
		$path = 'assets/exports/members/' . getDownloadDate() . '.xlsx';
		$writer->save($path);
		return base_url($path);
	}

}
