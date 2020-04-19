<?php
class Supplier_model extends CI_Model
{
	public function suppliers_count($acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where("pos_e_suppliers", array('account_no' => $acc, 'supp_stat' => 30)); 
		$row = $query->row_array();
		return $row['counted']; 
	}
	public function M_get_supplier($acc)
	{
		$array = array();
		$rows = $this->db->get_where("pos_e_suppliers", array('account_no' => $acc,'supp_stat' => 30)); 
		if($rows->num_rows() > 0){
			$array = array(NULL => '');
			foreach($rows->result() as $row){
				$array[$row->supp_id] = $row->cmp_name;				
			}
		}
		return $array;
	}
	public function get_all_suppliers($acc)
	{
		$query = $this->db->get_where('pos_e_suppliers',array('account_no' => $acc,'supp_stat' => 30));
		if($query->num_rows() > 0)
		{
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field][] = $row[$field];
				}
			} 
			return $array;				
		} else {
			return array();	
		}
	}
	public function get_supplier_details($supp_id,$acc)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_e_suppliers',array('account_no' => $acc,'supp_id' => $supp_id,'supp_stat' => 30));
		if($query->num_rows() > 0)
		{
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field] = $row[$field];
				}
			} 
			return $array;				
		} else {
			return null;	
		}		
	}
	public function insert_supplier($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$supp_id = $this->taxes_model->make_single_uuid();
		$insert = array(
						'supp_id' => $supp_id,
						'cmp_name' => $data['cmp_name'],
						'supp_description' => $data['supp_desc'],
						'auth_pers' => $data['contact_name'],
						'mobile' => $data['contact_mobile'],
						'll' => $data['contact_phone'],
						'email' => $data['contact_email'],
						'web' => $data['contact_web'],
						'addrr1' => $data['contact_addr1'],
						'addrr2' => $data['contact_addr2'],
						'city' => $data['contact_city'],
						'postal_code' => $data['contact_postalcode'],
						'state' => $data['contact_state'],
						'country' => $data['contact_country'],
						'fax' => $data['contact_fax'],
						'is_delete' => 30,
						'supp_stat' => 30,
						'account_no' => $data['acc'],
						);		
		$this->db->insert('pos_e_suppliers', $insert);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function update_supplier($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$update = array(
						'cmp_name' => $data['cmp_name'],
						'supp_description' => $data['supp_desc'],
						'auth_pers' => $data['contact_name'],
						'mobile' => $data['contact_mobile'],
						'll' => $data['contact_phone'],
						'email' => $data['contact_email'],
						'web' => $data['contact_web'],
						'addrr1' => $data['contact_addr1'],
						'addrr2' => $data['contact_addr2'],
						'city' => $data['contact_city'],
						'postal_code' => $data['contact_postalcode'],
						'state' => $data['contact_state'],
						'country' => $data['contact_country'],
						'fax' => $data['contact_fax']
						);		
		$this->db->where('supp_id', $data['supp_id']);
		$this->db->where('account_no', $data['acc']);
		$this->db->update('pos_e_suppliers', $update);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function delete_supplier($supp_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_8_supplier',array('account_no' => $acc,'supplier_id' => $supp_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$count = $row['counted'];
			if($count < 1)
			{
				$this->db->where(array('account_no' => $acc,'supp_id' => $supp_id,'is_delete' => 30));
				if($this->db->update('pos_e_suppliers',array('supp_stat' => 120)))
				{
					return array(
						'stat' => 1,
						'error_str' => 'Supplier Successfully deleted.'
					);
				} else {
					return array(
						'stat' => 0,
						'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
					);
				}
			} else {
				return array(
					'stat' => 2,
					'error_str' => $count.' Product(s) associated to this supplier. 
								Please remove or change product(s) to some other supplier and try again.'
				);
			}
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
			);
		}
	}
	public function get_supplier_id_if_like($str,$acc)
	{
		$this->db->select('supp_id');
		$this->db->where('cmp_name', $str); 
		$query = $this->db->get_where('pos_e_suppliers',array('account_no' => $acc,'supp_stat' => 30));
		if($query->num_rows() > 0) { 
			$row = $query->row_array();
			return $row['supp_id'] == "" ? NULL : $row['supp_id'];
		} else {
			return NULL;
		}				
	}
}
?>
