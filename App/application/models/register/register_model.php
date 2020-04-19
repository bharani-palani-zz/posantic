<?php
class Register_model extends CI_Model
{
	public function register_count($acc)
	{
		$this->db->select('count(*)');
		$query = $this->db->get_where('pos_j1_registers',array('account_no' => $acc,'reg_stat' => 30));
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   $count = $row['count(*)'];
		   return $count;
		} else {
		   return 0;	
		}
	}
	public function other_register_count_not_outlet($outlet_id,$acc)
	{
		$this->db->select('count(*)');
		$this->db->where('location !=',$outlet_id);
		$query = $this->db->get_where('pos_j1_registers',array('account_no' => $acc,'reg_stat' => 30));
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   $count = $row['count(*)'];
		   return $count;
		} else {
		   return 0;	
		}
	}
	public function get_register_outlet_details($acc)
	{
		$this->db->select('*');
		$this->db->from('pos_j1_registers as a');
		$this->db->join('pos_i7_quickeys as b','b.quick_index = a.quickey_template');
		$this->db->join('pos_b_locations as c','c.loc_id = a.location');
		$this->db->where(array('a.account_no' => $acc,'a.reg_stat' => 30));
		$query = $this->db->get();
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
	public function get_register($outlet_id,$acc)
	{
		$this->db->select('pos_j1_registers.reg_id as reg_id');
		$this->db->select('pos_j1_registers.reg_code as reg_code');
		$this->db->select('pos_c_reciept_template.template_id as template_id');
		$this->db->select('pos_c_reciept_template.template_name as template_name');
		$this->db->from('pos_j1_registers');
		$this->db->join('pos_c_reciept_template', 'pos_c_reciept_template.template_id = pos_j1_registers.receipt_template');
		$this->db->where(array('pos_j1_registers.account_no' => $acc, 'pos_j1_registers.location' => $outlet_id, 'pos_j1_registers.reg_stat' => 30));
		$rows = $this->db->get();
		if($rows->num_rows() > 0)
		{	
			$array = array();
			foreach($rows->result() as $row){ // can be more than 1
				$array['registers'][] = array('reg_id' => $row->reg_id, 'reg_code' => $row->reg_code, 'template_name' => $row->template_name, 'template_id' => $row->template_id);		
			}
			return $array;
		} else {
			return array();
		}
	}
	public function show_view_register($register_id,$acc)
	{
		$this->db->select('*');
		$this->db->from('pos_j1_registers');
		$this->db->join('pos_b_locations','pos_b_locations.loc_id = pos_j1_registers.location');
		$this->db->where('pos_j1_registers.reg_stat = ',30);
		$this->db->where('pos_j1_registers.account_no = ',$acc);
		$this->db->where('pos_j1_registers.reg_id = ',$register_id);
		$query = $this->db->get();
		if($query!=false){
			$array = array();
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field] = $row[$field];
				}
			} 
			return $array;
		} else {
			return array();
		}		
	}
	public function get_register_wrt_outlet($outlet_id,$acc)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc, 'loc_id' => $outlet_id,'outlet_stat' => 30));
		if($query != false){
			$array = array();
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field] = $row[$field];
				}
			} 
			return $array;
		} else {
			return array();
		}		
	}
	public function get_all_registers_combo($acc)
	{
		$this->db->select('reg_id');
		$this->db->select('reg_code');
		$query = $this->db->get_where('pos_j1_registers',array('account_no' => $acc,'reg_stat' => 30));
		if($query!=false){
			$array = array();
			foreach($query->result() as $row){ 
				$array[$row->reg_id] = $row->reg_code;		
			}
			return $array;
		} else {
			return array();
		}
	}
	public function insert_register($data)
	{
		$register_id = $this->taxes_model->make_single_uuid();
		$insert = array(
					'reg_id' => $register_id,
					'reg_code' => $data['reg_name'],
					'reg_stat' => 30,
					'is_delete' => 10,
					'email_reciept' => $data['reg_email_rec'],
					'print_reciept' => $data['reg_print_rec'],
					'billno_sequence' => $data['reg_bill_seq'],
					'billno_prefix' => $data['reg_prefix'],
					'receipt_template' => $data['reg_rec_temp'],
					'quickey_template' => $data['reg_qt_temp'],
					'switch_users' => $data['ask_user_stat'],
					'ask_quotes' => $data['ask_quotes_stat'],
					'rounding_method' => $data['reg_bill_round'],
					'location' => $data['outlet_id'],
					'account_no' => $data['acc']					
					);
		if($this->db->insert('pos_j1_registers',$insert))
		{
			return 1;
		} else {
			return 0;	
		}
	}
	public function modify_register($data)
	{
		$update = array(
					'reg_code' => $data['reg_name'],
					'email_reciept' => $data['reg_email_rec'],
					'print_reciept' => $data['reg_print_rec'],
					'billno_sequence' => $data['reg_bill_seq'],
					'billno_prefix' => $data['reg_prefix'],
					'receipt_template' => $data['reg_rec_temp'],
					'quickey_template' => $data['reg_qt_temp'],
					'switch_users' => $data['ask_user_stat'],
					'ask_quotes' => $data['ask_quotes_stat'],
					'rounding_method' => $data['reg_bill_round'],
					);
		$this->db->where('reg_id', $data['reg_id']);
		$this->db->where('account_no', $data['acc']);
		if($this->db->update('pos_j1_registers',$update))
		{
			return 1;
		} else {
			return 0;	
		}
	}
	public function outlet_register_count($outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_j1_registers',array('account_no' => $acc,'location' => $outlet_id,'reg_stat' => 30));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'];
		} else {
			return false;
		}
	}
	public function delete_register($data)
	{
		$this->db->select('(select count(*) from pos_j1_registers where location = a.loc_id) as counted',false);
		$this->db->select('a.loc_id');
		$this->db->from('pos_b_locations as a ');
		$this->db->join('pos_j1_registers as b','a.loc_id = b.location');
		$this->db->where('b.reg_id',$data['reg_id']);
		$this->db->where('b.account_no',$data['acc']);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{	
			$outlet_count = $this->outlet_model->outlet_count($data['acc']);	
			$row = $query->row_array();
			list($reg_count,$outlet_id) = array($row['counted'],$row['loc_id']);
			if($reg_count == 1 && $outlet_count > 1) // if only one register found and outlets are greater than 1
			{
				//delete reg and outlet, 
				$this->db->select('user_id');
				$this->db->select('user_name');
				$query = $this->db->get_where('pos_e_login',array('account_no' => $data['acc'], 'location' => $outlet_id));
				if($query->num_rows() > 0) //before check users exist for outlet
				{
					foreach($query->result() as $row)
					{
						$text[] = anchor(base_url('users/'.$row->user_id),$row->user_name,'style="color:#03f"');
					}
					if(count($text) > 1)
					{
						$lastItem = array_pop($text);
						$text = implode(', ', $text); 
						$text .= ' and '.$lastItem;
					} else {
						$text = $text[0];
					}
					return array(
						'stat' => 0,
						'error_str' => 'Some user(s) like "'.$text.'" are associated to this outlet, so it can`t be deleted. 
									Please remove or change user(s) to some other outlet and try again.'
					);
				} else {
					$this->db->where(array('account_no' => $data['acc'],'loc_id' => $outlet_id));
					if($this->db->update('pos_b_locations', array('outlet_stat' => 120)))
					{
						$this->db->where('reg_id',$data['reg_id']);
						$this->db->where('account_no',$data['acc']);
						if($this->db->update('pos_j1_registers',array('reg_stat' => 120)))
						{
							return array(
								'stat' => 1,
								'error_str' => 'Outlet successfully deleted with its register.'
							);				
						}
					}
				}
			} else if($reg_count > 1 && $outlet_count >= 1){
				//delete reg only
				$this->db->where('reg_id',$data['reg_id']);
				$this->db->where('account_no',$data['acc']);
				$this->db->update('pos_j1_registers',array('reg_stat' => 120));
				return array(
					'stat' => 1,
					'error_str' => 'Register successfully deleted.'
				);
			} else if($reg_count == 1 && $outlet_count == 1){
				return array(
					'stat' => 2,
					'error_str' => 'You cant delete your only register with its only outlet'
				);
			}
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Oops.. Some thing Gone wrong. Please try again.'
			);		
		}
	}
	public function register_pricing($acc)
	{
		$this->db->select('if(count(a.account_no) > 1,(count(a.account_no) -1)* (select max(register_cost_bymonth) from pos_2_userplans),0) as price', false);
		$query = $this->db->get_where('pos_j1_registers as a',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['price'];
		} else {
			return 0;	
		}		
	}
}
?>