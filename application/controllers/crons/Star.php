<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Star extends CI_Controller
{

	public function refine()
	{
		$this->findRankTwo();
		$this->findRankThree();
	}

	public function findRankFive()
	{
		ini_set('max_execution_time', 0);
		$this->db->where('rank', 4);
		$this->db->where('(lbv+rbv) >=', 240000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		foreach ($result as $record) {
			$left_team = $record['left_downline'];
			$right_team = $record['right_downline'];
			$left_count = $this->find_target($left_team, 4);
			$right_count = $this->find_target($right_team, 4);
			if ($left_count > 0 and $right_count > 0) {
				$this->updateFive($record);
			}
		}
	}

	public function updateFive($star)
	{
		ini_set('max_execution_time', 0);
		$period = $p[] = $star['period'];
		$p[] = date('Y-m', strtotime("+1 month", strtotime($period)));
		$p[] = date('Y-m', strtotime("+2 month", strtotime($period)));
		$this->db->where('member_id', $star['member_id']);
		$this->db->where_in('period', $p);
		$this->db->where('rank', 4);
		$this->db->where('(lbv+rbv) >=', 240000);
		$query = $this->db->get('star_helper');
		$result = $query->num_rows();
		if ($result >= 3) {
			$this->updateRank(5, array($star['member_id']));
		}
	}

	public function findRankSix()
	{
		ini_set('max_execution_time', 0);
		$this->db->where('rank', 5);
		$this->db->where('(lbv+rbv) >=', 480000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		foreach ($result as $record) {
			$team = $record['left_downline'] . "," . $record['right_downline'];
			$count = $this->find_target($team, 5);
			if ($count > 1) {
				$this->updateSix($record);
			}
		}
	}

	public function updateSix($star)
	{
		ini_set('max_execution_time', 0);
		$period = $p[] = $star['period'];
		$p[] = date('Y-m', strtotime("+1 month", strtotime($period)));
		$p[] = date('Y-m', strtotime("+2 month", strtotime($period)));
		$this->db->where('member_id', $star['member_id']);
		$this->db->where_in('period', $p);
		$this->db->where('rank', 5);
		$this->db->where('(lbv+rbv) >=', 480000);
		$query = $this->db->get('star_helper');
		$result = $query->num_rows();
		if ($result >= 3) {
			$this->updateRank(6, array($star['member_id']));
		}
	}

	public function findRankFour()
	{
		ini_set('max_execution_time', 0);
//		$this->db->where('member_id', $member['id']);
//		$this->db->where('period', $period);
		$this->db->where('rank', 3);
		$this->db->where('(lbv+rbv) >=', 80000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		$to_update = array();
		foreach ($result as $record) {
			$left_team = $record['left_downline'];
			$right_team = $record['right_downline'];
			$left_count = $this->find_target($left_team, 3);
			$right_count = $this->find_target($right_team, 3);
			if ($left_count > 0 and $right_count > 0) {
				$to_update[] = $record['id'];
			}
		}
		$this->updateRank(4, $to_update);
	}

	public function findRankThree()
	{
		ini_set('max_execution_time', 0);
//		$this->db->where('member_id', $member['id']);
//		$this->db->where('period', $period);
		$this->db->where('rank', 2);
		$this->db->where('(lbv+rbv) >=', 40000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		$to_update = array();
		foreach ($result as $record) {
			$left_team = $record['left_downline'];
			$right_team = $record['right_downline'];
			$left_count = $this->find_target($left_team, 2);
			$right_count = $this->find_target($right_team, 2);
			if ($left_count > 0 and $right_count > 0) {
				$to_update[] = $record['id'];
			}
		}
		$this->updateRank(3, $to_update);
	}

	public function findRankTwo()
	{
//		$this->db->where('member_id', $member['id']);
//		$this->db->where('period', $period);
		$this->db->where('lbv >=', 10000);
		$this->db->where('rbv >=', 10000);
		$this->db->update('star_helper', array(
			"rank" => 2
		));
	}

	public function findRankOne($period)
	{
//		$period = date("Y-m");
		$entry_date = date("Y-m-t", strtotime($period)) . " 23:59:59";
		$members = $this->members();

		foreach ($members as $member) {
			$RightTeam = array();
			$LeftTeam = array();

			$Left = array();
			$Right = array();
			$downlines = $this->getNode($member['id'], $entry_date);
			foreach ($downlines as $downline) {
				if ($downline['matrix_side'] == "L") {
					$Left['id'] = $downline['id'];
				}
				if ($downline['matrix_side'] == "R") {
					$Right['id'] = $downline['id'];
				}
			}

			if (count($downlines) > 0) {
				if (count($Left) > 0) {
					$LeftTeam = $this->getDownlineMemberWithRanks($Left['id'], 'L', $entry_date);
				}
				if (count($Right) > 0) {
					$RightTeam = $this->getDownlineMemberWithRanks($Right['id'], 'R', $entry_date);
				}

				$LeftIds = array_column($LeftTeam, 'id');
				$RightIds = array_column($RightTeam, 'id');

				if (count($Left) > 0) {
					$LeftIds[] = $Left['id'];
				}
				if (count($Right) > 0) {
					$RightIds[] = $Right['id'];
				}

				$lbv = 0;
				$rbv = 0;

				if (count($LeftIds) > 0) {
					$lbv = (float)$this->getSalesofDesiredMonth($period, $LeftIds);
				}

				if (count($RightIds) > 0) {
					$rbv = (float)$this->getSalesofDesiredMonth($period, $RightIds);
				}

				$post['member_id'] = $member['id'];
				$post['rank'] = 1;
				$post['period'] = $period;
				$post['lbv'] = $lbv;
				$post['rbv'] = $rbv;
				$post['left_downline'] = implode(",", $LeftIds);
				$post['right_downline'] = implode(",", $RightIds);

				$this->db->where('member_id', $member['id']);
				$this->db->where('period', $period);
				$query = $this->db->get('star_helper');
				$star_helper = $query->row_array();

				if (isset($star_helper['id'])) {
					$this->db->update('star_helper', $post, array(
						'member_id' => $member['id'],
						'period' => $period
					));
				} else {
					$this->db->insert('star_helper', $post);
				}
			}
		}
	}

	public function members()
	{
		$this->db->select("m.id, m.email, m.userid, m.f_name, m.l_name, m.rank, m.join_date");
		$query = $this->db->get('member m');
		return $query->result_array();
	}

	public function getNode($id, $entry_date)
	{
		$this->db->select("id, matrixid, matrix_side");
		$this->db->where("matrixid", $id);
		$this->db->where("join_date <=", $entry_date);
		$this->db->order_by('matrixid', 'asc');
		$query = $this->db->get('member');
		return $query->result_array();
	}

	public function getSalesofDesiredMonth($period, $members)
	{
		$this->db->select("SUM(personal_sales) as BV");
		$this->db->group_start();
		$member_ids_chunk = array_chunk($members, 25);
		foreach ($member_ids_chunk as $member_ids) {
			$this->db->or_where_in("member_id", $member_ids);
		}
		$this->db->group_end();
		$this->db->like("period", $period);
		$query = $this->db->get("member_sales_daily");
		$result = $query->row_array();
		return isset($result['BV']) ? $result['BV'] : 0;
	}

	public function getDownlineMemberWithRanks($matrix_id, $position, $entry_date)
	{
		$groups = array();
		$this->db->select("m.id, m.rank");
		$this->db->where("m.matrixid", $matrix_id);
		$this->db->where("m.join_date <=", $entry_date);
		$query = $this->db->get('member m');
		$members = $query->result_array();
		foreach ($members as $member) {
			$downlines = $this->getDownlineMemberWithRanks($member['id'], $position, $entry_date);
			$groups = array_merge($groups, $downlines);
		}
		return array_merge($members, $groups);
	}

	public function find_target($members, $rank)
	{
		$query = $this->db->query("SELECT * FROM star_helper WHERE rank ='$rank' AND member_id in ($members)");
		return $query->num_rows();
		$this->db->where('rank', $rank);
		$this->db->where_in("member_id", $members);
		$query = $this->db->get('star_helper');
		return $query->num_rows();
	}

	public function updateRank($rank, $to_update)
	{
		if (count($to_update) < 1) {
			return false;
		}
		$this->db->where_in("id", $to_update);
		$this->db->update('star_helper', array("rank" => $rank));
	}

}
