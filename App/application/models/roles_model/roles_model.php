<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Roles_model extends CI_Model
{
	public function get_roles($priv)
	{
		$this->db->select('priv_acronym');
		$this->db->select('priv_name');
		$query = $this->db->get_where('pos_1a_privileges',array('priv_id' => $priv));
		if($query->num_rows() > 0){
			$row = $query->row_array();
			return array($row['priv_acronym'],$row['priv_name']);
		} else {
			return NULL;	
		}
	}
}
?>