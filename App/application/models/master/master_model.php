<?php
class Master_model extends CI_Model
{
	public function plan_bonafide($acc)
	{
		$this->db->select('*');
		$this->db->from('pos_a_master as a');
		$this->db->join('pos_2_userplans as b','b.plan_id = a.plan_code');
		$this->db->join('pos_1a_account_type as c','c.index_id = a.account_type');
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$array = array();
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field] = defined($row[$field]) ? constant($row[$field]) : $row[$field];
				}
			} 
			return $array;
		} else {
			//die('Unable to find this merchant');
			return false;
		}
	}
	public function master_plan($sel_plan)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_2_userplans',array('plan_id' => $sel_plan));
		if($query->num_rows() > 0){
			$array = array();
			foreach ($query->list_fields() as $field)
			{
				$i=0;
				foreach($query->result_array() as $row)
				{
					$array[$field][$i] = $row[$field];
					$i++;
				}
			} 
			return $array;
		} else {
			return array();	
		}
	}
	public function outlet_check($current_outlet_count,$sel_outlet_str)
	{
		if($current_outlet_count == 1 && $sel_outlet_str == 'Single')
		{
			return true;	
		} else if($current_outlet_count > 1 && $sel_outlet_str == 'Multiple') {
			return true;	
		} else if($current_outlet_count == 1 && $sel_outlet_str == 'Multiple') {
			return true;	
		} else {
			return false;	
		}
	}
	public function warrant($host,$sel_plan,$cur_plan)
	{
		if($sel_plan != 1234) // user plan is deleted
		{
			$this->db->select('account_no');
			$rows = $this->db->get_where('pos_a_master',array('subdomain' => $host)); //get acc_no
			if($rows->num_rows() > 0){
				$row = $rows->row_array();
				$acc =  $row['account_no'];
				$current_outlet_count = $this->outlet_model->outlet_count($acc);
				if($current_outlet_count != false){
					$all_array = $this->master_plan($sel_plan);
					$sel_outlet_str = $all_array['stores_handle'][0];
					if($this->outlet_check($current_outlet_count,$sel_outlet_str))
					{
						$prd_count = $this->product_model->product_count($acc);
						if(isset($prd_count['grand_total'])){
							$current_stocks = $prd_count['grand_total'];
							$sel_stocks = $all_array['stock_limit'][0];
							if($current_stocks <= $sel_stocks)
							{
								$current_users = $this->user_model->user_count($acc); // user count
								if(is_numeric($current_users)){
									$sel_users = $all_array['users_limit'][0];
									if($current_users <= $sel_users)
									{
										$current_db = $this->customer_model->customer_count(); //get DB count
										if(is_numeric($current_db)){
											$sel_db = $all_array['customer_db_count'][0];
											if($current_db <= $sel_db)
											{
												return 1;										
											} else {
												return 10;
											}
										} else {
											return 9;	
										}
									} else {
										return 8;	
									}
								} else {
									return 7;
								}
							} else {
								return 5;	
							}
						} else {
							return 4;	
						}
					} else {
						return 3;	
					}
				} else {
					return 2;	
				}
			} else {
				return 0;	
			}
		} else {
			return 6;	
		}
	}	

}
?>