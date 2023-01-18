<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LedgerModel extends CI_Model
{
	public function getBalance($currency, $user_id)
	{
		$this->db->select("sum(debit) as dr, sum(credit) as cr", false);
		$this->db->where("currency", $currency);
		$this->db->where("trans_source_type !=", 'COMMISSION_WITHDRAWAL_VOID');
		$this->db->where("member_id", $user_id);
		$query = $this->db->get('member_ledger');
		return $query->row_array();
	}

	public function getLedger($user_id, $currency, $start_time, $end_time)
	{
		if($user_id != "") {
			$this->db->where("member_id", $user_id);
		}
		$this->db->where("currency", $currency);
		$this->db->where('insert_time >=', $start_time);
		$this->db->where('insert_time <=', $end_time);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('member_ledger');
		return $query->result_array();
	}

	public function getUniversalLedger($post = null)
	{

		$currency = isset($post['currency']) ? $post['currency'] : "CC";
		$start_time = isset($post['selyrfrom']) && isset($post['selmonfrom']) ? $post['selyrfrom'] . '-' . $post['selmonfrom'] . '-01 00:00:00' : date("Y-m-01 00:00:00");
		$end_time = isset($post['selyrto']) && isset($post['selmonto']) ? date('Y-m-t', strtotime($post['selyrto'] . '-' . $post['selmonto'] . '-01')) . " 23:59:59" : date("Y-m-t 23:59:59");
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($post['member_id']);
		$type = $post['type'];
		if($type == 'commissions') {
			$currency = $post['bonus'];
		}
		$user_id = isset($member['id']) ? $member['id'] : "";

		$this->db->select("ml.*, m.userid");
		if($user_id != "") {
			$this->db->where("ml.member_id", $user_id);
		}
		if($type == 'sales'){
			$this->db->where_in("ml.trans_source_type", array("ORDER", "UPGRADE", "ADD_ACCOUNT"));
		}
		if($type == 'withdrawals'){
			$this->db->like("ml.trans_source_type", "WITHDRAW");
		}
		if($type == 'transfers'){
			$this->db->like("ml.trans_source_type", "transfer");
		}
		if($type == 'converts'){
			$this->db->like("ml.trans_source_type", "convert");
		}
		if($type == 'commissions'){
			if($currency == 'ALL_BONUS') {
				$this->db->like("ml.trans_source_type", "_BONUS");
			} else {
				$this->db->like("ml.trans_source_type", $currency);
			}
		}
		if($type == 'topups'){
			$this->db->like("ml.trans_source_type", "PURCHASE_CREDIT");
		}
		if($type != 'commissions') {
			$this->db->where("ml.currency", $currency);
		}
		$this->db->where('ml.insert_time >=', $start_time);
		$this->db->where('ml.insert_time <=', $end_time);
		$this->db->join('member m', 'm.id = ml.member_id');
		$this->db->order_by('ml.id', 'desc');


		$this->db->from("member_ledger ml");
		$tempdb = clone $this->db;
		if (isset($post['per_page'])) {
			$start = ($post['page'] - 1) * $post['per_page'];
			$this->db->limit($post['per_page'], $start);
		}
		$data['counter'] = $tempdb->count_all_results("", false);
		$query = $this->db->get();
		$data['result'] =  $query->result_array();
		return $data;
	}

	public function exportUniversalLedger($post = null)
	{

		$currency = isset($post['currency']) ? $post['currency'] : "CC";
		$start_time = isset($post['selyrfrom']) && isset($post['selmonfrom']) ? $post['selyrfrom'] . '-' . $post['selmonfrom'] . '-01 00:00:00' : date("Y-m-01 00:00:00");
		$end_time = isset($post['selyrto']) && isset($post['selmonto']) ? date('Y-m-t', strtotime($post['selyrto'] . '-' . $post['selmonto'] . '-01')) . " 23:59:59" : date("Y-m-t 23:59:59");
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($post['member_id']);
		$type = $post['type'];
		if($type == 'commissions') {
			$currency = $post['bonus'];
		}
		$user_id = isset($member['id']) ? $member['id'] : "";

		$this->db->select("ml.*, m.userid");
		if($user_id != "") {
			$this->db->where("ml.member_id", $user_id);
		}
		if($type == 'sales'){
			$this->db->where_in("ml.trans_source_type", array("ORDER", "UPGRADE", "ADD_ACCOUNT"));
		}
		if($type == 'withdrawals'){
			$this->db->like("ml.trans_source_type", "WITHDRAW");
		}
		if($type == 'transfers'){
			$this->db->like("ml.trans_source_type", "transfer");
		}
		if($type == 'converts'){
			$this->db->like("ml.trans_source_type", "convert");
		}
		if($type == 'commissions'){
			if($currency == 'ALL_BONUS') {
				$this->db->like("ml.trans_source_type", "_BONUS");
			} else {
				$this->db->like("ml.trans_source_type", $currency);
			}
		}
		if($type == 'topups'){
			$this->db->like("ml.trans_source_type", "PURCHASE_CREDIT");
		}
		if($type != 'commissions') {
			$this->db->where("ml.currency", $currency);
		}
		$this->db->where('ml.insert_time >=', $start_time);
		$this->db->where('ml.insert_time <=', $end_time);
		$this->db->join('member m', 'm.id = ml.member_id');
		$this->db->order_by('ml.id', 'desc');
		$this->db->from("member_ledger ml");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function insertLedger($ledger)
	{
		$return = $this->db->insert('member_ledger', $ledger);
		$this->refine_ledger($ledger['member_id'], $ledger['currency']);
		return $return;
	}

	public function refine_ledger($user_id, $currency)
	{
		$period = getMonth();
		$this->db->select("member_ledger.id, member.id as member_id, member_ledger.debit, member_ledger.credit, member_ledger.balance");
		$this->db->join('member', 'member.id = member_ledger.member_id');
		$this->db->where("member.id", $user_id);
		$this->db->where("member_ledger.trans_source_type !=", 'COMMISSION_WITHDRAWAL_VOID');
		$this->db->where("member_ledger.currency", $currency);
		$query = $this->db->get("member_ledger");
		$result = $query->result_array();
		$this->db->reset_query();

		$balance = 0;
		$user_id = 0;
		foreach ($result as $row) {
			$current = $row['credit'] - $row['debit'];
			$balance = $balance + $current;
			$this->db->update('member_ledger', array('balance' => $balance), array('id' => $row['id']));
		}
	}

	public function getLedgerSummary($period)
	{
		$this->db->select('sum(debit) as dr, sum(credit) as cr, trans_source_type, currency');
		$this->db->where('period', $period);
		$this->db->group_by("trans_source_type, currency");
		$query = $this->db->get('member_ledger');
		$ledgers = $query->result_array();
		return $ledgers;
	}

	public function getTransferHistory($user_id)
	{
		$this->db->select("member_ledger.*, member.id as member_id, member.userid");
		$this->db->join('member', 'member.id = member_ledger.member_id');
		$this->db->where("member.id", $user_id);
		$this->db->where("member_ledger.trans_source_type", 'TRANSFER');
		$query = $this->db->get("member_ledger");
		$result = $query->result_array();
		return $result;
	}

}
