<?php
class Admin_model extends CI_Model
{
	public function settings_model()
	{
		$params = array(9884856788);
		$array = array();
		$rows = $this->db->query("select * from pos_1a_settings where setting_id = ?", $params); 
		if($rows!=false){
			foreach($rows->result() as $row){
				$array = array($row->pos_hotline,$row->pos_email, $row->pos_web, $row->pos_cmp, $row->pos_version, $row->pos_year, $row->pos_currency_code, $row->pos_currency_symbol);
			}
		}
		return $array;
		$this->db->close();		
	}
	public function logged_as($subdomain)
	{
		$this->db->select('account_no');
		$this->db->select('cmp_name');
		$this->db->select('account_stat');
		$query = $this->db->get_where('pos_a_master',array('subdomain' => $subdomain));
        if($query->num_rows() > 0) {
			$row = $query->row_array();
			$val =  array($row['account_no'],$row['cmp_name'],$row['account_stat']);
			return $val;
		} else {
			return NULL;	
		}
	}
}
?>