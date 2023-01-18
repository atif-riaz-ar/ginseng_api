<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends CI_Controller
{
	public function ledger()
	{
		ini_set("max_execution_time", 600);
		if (count($_POST) == 0) {
			$data['form']["type"] = isset($_GET['type']) ? $_GET['type'] : "";
			$data['form']["currency"] = isset($_GET['currency']) ? $_GET['currency'] : "";
			$data['form']["selyrfrom"] = isset($_GET['selyrfrom']) ? $_GET['selyrfrom'] : "";
			$data['form']["selmonfrom"] = isset($_GET['selmonfrom']) ? $_GET['selmonfrom'] : "";
			$data['form']["selyrto"] = isset($_GET['selyrto']) ? $_GET['selyrto'] : "";
			$data['form']["selmonto"] = isset($_GET['selmonto']) ? $_GET['selmonto'] : "";
			$data['form']["member_id"] = isset($_GET['member_id']) ? $_GET['member_id'] : "";
			$data['report'] = "ledger";
			return $this->load->view("loader", $data);
			exit;
		}
		$this->load->library('ledgersheet');
		echo $this->ledgersheet($_POST);
	}

	public function order()
	{
		ini_set("max_execution_time", 600);
		if (count($_POST) == 0) {
			$data['form']["userid"] = isset($_GET['userid']) ? $_GET['userid'] : "";
			$data['report'] = "order";
			return $this->load->view("loader", $data);
			exit;
		}
		$this->load->library('ordersheet');
		echo $this->ordersheet($_POST);
	}

	public function member()
	{
		ini_set("max_execution_time", 600);
		if (count($_POST) == 0) {
			$data['form']["userid"] = isset($_GET['userid']) ? $_GET['userid'] : "";
			$data['report'] = "member";
			return $this->load->view("loader", $data);
			exit;
		}
		$this->load->library('membersheet');
		echo $this->membersheet($_POST);
	}

	public function star()
	{
		ini_set("max_execution_time", 600);
		if (count($_POST) == 0) {
			$data['form']["userid"] = isset($_GET['userid']) ? $_GET['userid'] : "";
			$data['form']['fl_rank'] = isset($_GET['fl_rank']) ? $_GET['fl_rank'] : 'any';
			$data['form']['period'] = isset($_GET['period']) ? $_GET['period'] : date("Y-m");
			$data['report'] = "star";
			return $this->load->view("loader", $data);
			exit;
		}
		echo $this->starsheet($_POST);
	}


	public function starsheet($post)
	{
		$lang_model = $this->LanguageModel;
		$report = $this->StarModel->exportStarReport($post);

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
		return API_URL . $path;
	}

	public function membersheet($post)
	{
		$lang_model = $this->LanguageModel;
		$report = $this->MemberModel->exportAllMembers($post);

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
		return API_URL . $path;
	}

	public function ordersheet($post)
	{
		$report = $this->OrderModel->exportAllOrders($post);

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
		return API_URL . $path;
	}

	public function ledgersheet($post)
	{
		$lang_model = $this->LanguageModel;
		$report = $this->LedgerModel->exportUniversalLedger($post);

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
			$description = isset($trans['en']) ? $trans['en'] : $val['description'];
			$sheet->setCellValue('D' . $rows, $description);
			$sheet->setCellValue('E' . $rows, $val['credit']);
			$sheet->setCellValue('F' . $rows, $val['debit']);
			$sheet->setCellValue('G' . $rows, $val['balance']);
			$rows++;
		}
		$writer = new Xlsx($spreadsheet);
		$dir = 'assets/exports/ledgers/' . $post['type'];
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		$path = $dir . '/' . getDownloadDate() . '.xlsx';
		$writer->save($path);
		return API_URL . $path;
	}

}

