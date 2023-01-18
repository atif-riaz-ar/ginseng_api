<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderSheet
{
	public function ordersheet($post)
	{
		$CI = ci();
		$lang_model = $CI->LanguageModel;
		$report = $CI->OrderModel->exportAllOrders($post);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// set columns merge
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:K1');

		// set border
		$sheet->getStyle("A1:K1")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK)->setColor(new Color('0000ff'));

		// title font changes
		$sheet->getStyle(1)->getFont()->setBold(true);
		$sheet->getStyle(1)->getFont()->setSize(24);
		$sheet->getStyle(1)->getFont()->getColor()->setARGB("0000ff");
		$sheet->getStyle(1)->getAlignment()->setHorizontal("center");
		// title set background
		$sheet->getStyle(1)->getFill()->setFillType(Fill::FILL_NONE)->getStartColor()->setARGB('ffffff');

		// header font changes
		$sheet->getStyle("A3:K3")->getFont()->setSize(12);
		$sheet->getStyle("A3:K3")->getFont()->getColor()->setARGB("ffffff");
		// header set background
		$sheet->getStyle("A3:K3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0000ff');

		$sheet->setCellValue('A1', 'Ginseng Members Report');

		$sheet->setCellValue('A3', '#');
		$sheet->setCellValue('B3', 'Action');
		$sheet->setCellValue('C3', 'OrderDate');
		$sheet->setCellValue('D3', 'RejectionDate');
		$sheet->setCellValue('E3', 'ApprovalDate');
		$sheet->setCellValue('F3', 'DeliverDate');
		$sheet->setCellValue('G3', 'OrderBy');
		$sheet->setCellValue('H3', 'OrderNumber');
		$sheet->setCellValue('I3', 'Type');
		$sheet->setCellValue('J3', 'Amount');
		$sheet->setCellValue('K3', 'Payment');

		$rows = 4;
		$n = 1;
		foreach ($report as $val) {
			// header set background
			$bgcolor = $rows % 2 == 1 ? 'c9c9c9' : 'ffffff';
			$sheet->getStyle("A" . $rows . ":K" . $rows)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgcolor);
			$sheet->getStyle("A" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("B" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("C" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("D" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("E" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("F" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("G" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("H" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("I" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("J" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));
			$sheet->getStyle("K" . $rows)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000'));

			$sheet->setCellValue('A' . $rows, $n++);
			$sheet->setCellValue('B' . $rows, $val['status']);
			$sheet->setCellValue('C' . $rows, $val['order_date']);
			$sheet->setCellValue('D' . $rows, $val['rejected_date']);
			$sheet->setCellValue('E' . $rows, $val['approved_date']);
			$sheet->setCellValue('F' . $rows, $val['received_date']);
			$sheet->setCellValue('G' . $rows, $val['userid'] . "(" . $val['f_name'] . " " . $val['l_name'] . ")");
			$sheet->setCellValue('H' . $rows, $val['order_num']);
			$sheet->setCellValue('I' . $rows, $val['order_type']);
			$sheet->setCellValue('J' . $rows, $val['total_amount']);
			$sheet->setCellValue('K' . $rows, $val['payment_type']);
			$rows++;
		}
		$writer = new Xlsx($spreadsheet);
		$path = 'assets/exports/orders/' . getDownloadDate() . '.xlsx';
		$writer->save($path);
		return base_url($path);
	}

}
