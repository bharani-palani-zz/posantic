<?php
class Variant_model extends CI_Model
{
	public function M_get_variants($acc)
	{
		$this->db->select('cust_var_id');
		$this->db->select('cust_var_value');
		$this->db->order_by('cust_var_name');
		$query = $this->db->get_where('pos_i1_0_cust_variant_types',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[] = array('code' => $row->cust_var_id, 'named' => $row->cust_var_value);
			}
		} else {
			$array = array(array('code' => NULL,'named' => NULL));	
		}
		return $array;
	}
	public function variant_dropdown($acc)
	{
		$this->db->select('cust_var_id');
		$this->db->select('cust_var_value');
		$this->db->order_by('cust_var_name');
		$query = $this->db->get_where('pos_i1_0_cust_variant_types',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->cust_var_id] = $row->cust_var_value;
			}
		} else {
			$array = array('' => NULL);	
		}
		return $array;
	}
	public function add_custom_variant($data)
	{
		$key = $this->taxes_model->make_single_uuid();
		$insert = array(
					'cust_var_id' => $key,
					'cust_var_name' => $data['cust_var_key'],
					'cust_var_value' => $data['cust_var_value'],
					'account_no' => $data['acc']
				);		
		if($this->db->insert('pos_i1_0_cust_variant_types',$insert))
		{
			$ajax_array = array('status' => 'success','var_key' => $key,'var_value' => $data['cust_var_value']);
		} else {
			$ajax_array = array('status' => 'fail','var_key' => NULL,'var_value' => NULL);
		}
		return $ajax_array;
	}
	public function get_variant_attr_id_wrt_name($name,$acc)
	{
		$this->db->select('cust_var_id');
		$this->db->like('cust_var_value',$name);
		$this->db->from('pos_i1_0_cust_variant_types');
		$this->db->where('account_no',$acc);
		$query = $this->db->get();
		$row = $query->row_array();	
		if(!empty($row['cust_var_id']))
		{
			return $row['cust_var_id'];
		} else {
			return NULL;	
		}	
	}
}