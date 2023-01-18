<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrderModel extends CI_Model
{
	function getAllOrders($post = null)
	{
		$this->db->select("
			member.f_name, member.l_name, member.userid,
			order_master.id, order_master.approved_date, order_master.received_date, order_master.order_num, 
			order_master.order_type, order_master.total_amount, order_master.payment_type, order_master.status, 
			order_master.order_date, order_master.rejected_date
			");
		$this->db->join('member', 'member.id = order_master.member_id');
		$this->db->order_by('order_master.order_date', 'desc');
		if (!empty($post['userid'])) {
			$this->db->like("member.userid", $post['userid']);
		}
		$this->db->from("order_master");
		$tempdb = clone $this->db;
		if (isset($post['per_page'])) {
			$start = ($post['page'] - 1) * $post['per_page'];
			$this->db->limit($post['per_page'], $start);
		}
		$data['counter'] = $tempdb->count_all_results("", false);
		$query = $this->db->get();
		$data['result'] = $query->result_array();
		return $data;
	}

	function exportAllOrders($post = null)
	{
		$this->db->select("
			member.f_name, member.l_name, member.userid,
			order_master.id, order_master.approved_date, order_master.received_date, order_master.order_num, 
			order_master.order_type, order_master.total_amount, order_master.payment_type, order_master.status, 
			order_master.order_date, order_master.rejected_date
			");
		$this->db->join('member', 'member.id = order_master.member_id');
		$this->db->order_by('order_master.order_date', 'desc');
		if (!empty($post['userid'])) {
			$this->db->like("member.userid", $post['userid']);
		}
		$this->db->from("order_master");
		$query = $this->db->get();
		return $query->result_array();
	}

	function details($id)
	{
		$order['master'] = $this->getOrder($id);
		$order['details'] = $this->getDetails($id);
		return $order;
	}

	function getOrder($id)
	{
		$this->db->select("order_master.*, member.f_name, member.l_name, member.userid, member.email, member.mobile");
		$this->db->join('member', 'member.id = order_master.member_id');
		$this->db->where('order_master.id', $id);
		$this->db->order_by('order_master.order_date', 'desc');
		$query = $this->db->get('order_master');
		return $query->row_array();
	}

	function getDetails($id)
	{
		$this->db->where('order_detail.order_master_id', $id);
		$query = $this->db->get('order_detail');
		return $query->result_array();
	}

	function action($action, $order_id)
	{
		if ($action == "approved") {
			$post['status'] = 'PAID';
			$post['approved_date'] = getFullDate();
		}
		if ($action == "cancelled") {
			$post['status'] = 'CANCELLED';
			$post['rejected_date'] = getFullDate();
		}
		if ($action == "received") {
			$post['status'] = 'COMPLETED';
			$post['received_date'] = getFullDate();
		}
		$this->db->update('order_master', $post, array('id' => $order_id));
	}
}
