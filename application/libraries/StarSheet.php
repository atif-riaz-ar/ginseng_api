<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StarSheet
{
	public function starsheet($post)
	{
		$CI = ci();
		$lang_model = $CI->LanguageModel;
		$report = $CI->StarModel->exportStarReport($post);

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

		$sheet->setCellValue('A1', 'Ginseng Star Report');

		$sheet->setCellValue('A3', 'User');
		$sheet->setCellValue('B3', 'FirstName');
		$sheet->setCellValue('C3', 'LastName');
		$sheet->setCellValue('D3', 'Email');
		$sheet->setCellValue('E3', 'Period');
		$sheet->setCellValue('F3', 'LeftBV');
		$sheet->setCellValue('G3', 'RightBV');
		$sheet->setCellValue('H3', 'Rank');

		$rows = 4;
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

			$sheet->setCellValue('A' . $rows, $val['userid']);
			$sheet->setCellValue('B' . $rows, $val['f_name']);
			$sheet->setCellValue('C' . $rows, $val['l_name']);
			$sheet->setCellValue('D' . $rows, $val['email']);
			$arr = array(1, 2, 3, 4, 5, 6);
			foreach ($arr as $a) {
				$d = explode("__", $val['month' . $a]);
				$sheet->setCellValue('E' . $rows, $d[0]);
				$sheet->setCellValue('F' . $rows, $d[1]);
				$sheet->setCellValue('G' . $rows, $d[2]);
				$rnk = $val['rank' . $a];
				$rnk = $lang_model->replace("en", $rnk)['en'];
				$sheet->setCellValue('H' . $rows, $rnk);
				$rows++;
			}
		}
		$writer = new Xlsx($spreadsheet);
		$path = 'assets/exports/stars/' . getDownloadDate() . '.xlsx';
		$writer->save($path);
		return base_url($path);
	}

}
