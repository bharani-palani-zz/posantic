<?php
class Status_codes extends CI_Model
{
	public function get_status_code()
	{
		$rows = $this->db->get('pos_1e_status_codes');
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->status_id] = $row->status_code;
			}
			return $array;
		} else {
			return array('NULL');
		}
	}
}
?>