<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StarModel extends CI_Model
{
	public function getStarReport($post)
	{
		if (!empty($post['period'])) {
			$this->db->like('month1', $post['period']);
		}

		if (!empty($post['userid'])) {
			$this->db->where('userid', $post['userid']);
		}

		if ($post['fl_rank'] == "any") {
			$this->db->group_start();
			$this->db->where("(rank1 like '%star%' or rank2 like '%star%' or rank3 like '%star%' or rank4 like '%star%' or rank5 like '%star%' or rank6 like '%star%')");
			$this->db->group_end();
		}

		$this->db->from("star_report");
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

	public function exportStarReport($post)
	{
		if (!empty($post['period'])) {
			$this->db->like('month1', $post['period']);
		}

		if (!empty($post['userid'])) {
			$this->db->where('userid', $post['userid']);
		}

		if ($post['fl_rank'] == "any") {
			$this->db->group_start();
			$this->db->where("(rank1 like '%star%' or rank2 like '%star%' or rank3 like '%star%' or rank4 like '%star%' or rank5 like '%star%' or rank6 like '%star%')");
			$this->db->group_end();
		}

		$this->db->from("star_report");
		$query = $this->db->get();
		return $query->result_array();
	}
}
