<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dbase extends CI_Model {
	function __construct(){
		$this->load->database();
	}
	function sqlResult($sql){
		$data	= $this->db->query($sql);
		return $data->result();
	}
	function sqlRow($sql){
		$data	= $this->db->query($sql);
		return $data->row();
	}
	function dataResultArray($table,$data=FALSE,$select=FALSE,$order=FALSE,$sort=FALSE,$limit=FALSE,$offset=FALSE){
		if ($select){ $this->db->select($select); }
		if ($sort) { $sort = $sort; } else { $sort = 'desc'; }
		if ($order){ $this->db->order_by($order, $sort);  }
		if (!$limit) { $limit = 1000000; }
//		if ($distinct){ $this->db->distinct($distinct); }
		if ($offset){ $offset = $offset; }
		if ($data){
			$query		= $this->db->get_where($table,$data,$limit,$offset);
		} else {
			$query		= $this->db->get($table,$limit, $offset);
		}
		return $query->result_array();
	}
	function dataResult($table,$data=FALSE,$select=FALSE,$order=FALSE,$sort=FALSE,$limit=FALSE,$offset=FALSE){
		if ($select){ $this->db->select($select); }
		if ($sort) { $sort = $sort; } else { $sort = 'desc'; }
		if ($order){ $this->db->order_by($order, $sort);  }
		if (!$limit) { $limit = 1000000; }
//		if ($distinct){ $this->db->distinct($distinct); }
		if ($offset){ $offset = $offset; }
		if ($data){
			$query		= $this->db->get_where($table,$data,$limit, $offset);
		} else {
			$query		= $this->db->get($table,$limit, $offset);
		}
		return $query->result();
	}
	function dataResultLike($table,$like,$select=FALSE,$order=FALSE,$direction=FALSE,$limit=FALSE,$offset=FALSE){
		if (!$direction){ $direction = 'ASC'; }
		if (!$limit) { $limit = 1000000; }
		if (!$offset){ $offset = 0; }

		if ($select){ $this->db->select($select); }
		if ($order){ $this->db->order_by($order,$direction); }
		$this->db->like($like);
		$query		= $this->db->get($table,$limit, $offset);
		return $query->result();
	}
	function dataRow($table,$data=FALSE,$select=FALSE){
		if ($select){ $this->db->select($select); }
		if ($data){
			$query		= $this->db->get_where($table,$data);
		} else {
			$query		= $this->db->get($table);
		}
		return $query->row();
	}
	function dataRowLike($table,$like,$select=FALSE){
		if ($select){ $this->db->select($select); }
		$this->db->like($like);
		$query		= $this->db->get($table);
		return $query->row();
	}
	function dataDelete($table,$where){
		$this->db->delete($table,$where);
	}
	function dataUpdate($table,$where,$data){
		$this->db->where($where);
		$this->db->update($table, $data); 
	}
	function dataInsert($table,$data){
		$this->db->insert($table,$data);
		return $this->db->insert_id();
	}
	function last_id(){
		return $this->db->insert_id();
	}
	function dataCreate($sql){
		if ($this->db->query($sql)){
			return 1;
		} else {
			return 0;
		}
	}
}
