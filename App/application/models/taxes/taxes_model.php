<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Taxes_model extends CI_Model
{
	public function get_all_single_taxes($acc)
	{
		$this->db->select('*');	
		$this->db->where('is_group = ',20);	
		$this->db->where('tax_stat',30);	
		$this->db->group_by('tax_name');	
		$this->db->where_in('is_delete',array(10,20));	
		$this->db->order_by('tax_name','asc');	
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query != false){
			$array = array();
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
	public function get_single_taxes_combo($acc)
	{
		$this->db->select('*');	
		$this->db->where('is_group = ',20);	
		$this->db->where('tax_stat',30);	
		$this->db->group_by('tax_name');	
		$this->db->where_in('is_delete',array(10));	
		$this->db->order_by('is_delete','desc');	
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query != false){
			$array = array();
			foreach($query->result_array() as $row)
			{
				$array['Single Taxes To Group'][$row['tax_id']] = $row['tax_name'];
			}
			return $array;
		} else {
			return array(0 => 'NULL');
		}				
	}
	public function get_single_group_taxes_combo($acc)
	{
		$sql = "select 
				a.tax_id as tax_id,
				concat(
					a.tax_name,' (',if(
					a.is_group = 20, a.tax_val,
					(
					SELECT 
						sum(pos_a_taxes.tax_val)
					FROM 
						pos_a_taxes_group
					join pos_a_taxes on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
					where
						pos_a_taxes_group.parent_id = a.tax_id            
					)    	
				),'%)') as tax_value,
				if(a.is_group = 20,'Single Taxes', 'Group Taxes') as caption
				from pos_a_taxes a
				where
				a.tax_stat = 30 and
				a.account_no = ?
				order by caption desc";
		$params = array($acc);			
		$query = $this->db->query($sql,$params);
		if($query != false){
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->caption][$row->tax_id] = $row->tax_value;
			}
			return $array;
		} else {
			return array();
		}				
	}
	public function get_group_taxes($acc)
	{
		$this->db->select('a.group_name as group_name');	
		$this->db->select('a.parent_id as parent_id');	
		$this->db->select('a.assoc_tax_id as tax_id');	
		$this->db->select('b.tax_name as tax_names');	
		$this->db->select('b.tax_val as tax_val');	
		$this->db->from('pos_a_taxes_group as a');
		$this->db->join('pos_a_taxes as b','b.tax_id = a.assoc_tax_id and a.account_no = b.account_no and b.tax_id = a.assoc_tax_id');
		$this->db->where('a.group_stat',30);	
		$this->db->where('a.account_no = ',$acc);	
		$this->db->order_by('group_name');
		$query = $this->db->get();
		if($query!=false){
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->parent_id]['group_name'] = $row->group_name;
				$array[$row->parent_id]['parent_id'] = $row->parent_id;
				$array[$row->parent_id]['tax_id'][] = $row->tax_id;
				$array[$row->parent_id]['tax_names'][] = $row->tax_names;
				$array[$row->parent_id]['tax_val'][] = $row->tax_val;
			}
			return $array;
		} else {
			return array();
		}
	}
	public function get_all_outlet_taxes($acc) /// big idea to get grouping values
	{
		$sql = 'select 
				b.loc_id as loc_id,
				b.location as location,
				a.tax_name as tax_name,
				a.tax_id as tax_id,
				if(a.is_group = 20,a.tax_val,
				(
					SELECT 
						sum(pos_a_taxes.tax_val)
					FROM 
						pos_a_taxes_group
					join pos_a_taxes on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
					where
						pos_a_taxes_group.parent_id = a.tax_id)
				) as tax_val
				from pos_a_taxes a
				join pos_b_locations b on b.outlet_tax = a.tax_id
				where
					b.outlet_stat = 30 and
					b.account_no = ?
				order by location';
		$params = array($acc);			
		$query = $this->db->query($sql,$params);
		if($query != false){
			$array = array();
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
	public function make_single_uuid()
	{
		$this->db->select('uuid() as uuid');	
		$query = $this->db->get();
		$row = $query->row_array();
		$str = str_replace("-","",$row['uuid']);
		$first = substr($str,20,8);
		$sec = substr($str,28,4);
		$third = substr($str,12,4);
		$forth = substr($str,16,4);
		$fifth = substr($str,8,4).substr($str,0,8);
		return sprintf('%s-%s-%s-%s-%s',$first,$sec,$third,$forth,$fifth);

	}
	public function add_tax($tax_name,$tax_rate,$acc)
	{
		$this->db->select('tax_name');
		$this->db->where('tax_stat',30);
		$this->db->like('tax_name', $tax_name); 
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query->num_rows() < 1) { 
			$data = array(
							'tax_id' => $this->make_single_uuid(),
							'tax_name' => $tax_name,
							'is_group' => 20,
							'tax_val' => $tax_rate,
							'is_delete' => 10,
							'tax_stat' => 30,
							'account_no' => $acc
							); 
			if($this->db->insert('pos_a_taxes', $data))
			{
				return 1;
			} else {
				return 0;
			}
		} else {
			return 2;	
		}
	}
	public function add_group($tax_group_name,$tax_group_array,$acc)
	{
		$this->db->select('tax_name'); //check name exist
		$this->db->like('tax_name', $tax_group_name); 
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query->num_rows() < 1) { 
			$parent_id = $this->make_single_uuid();
			$data1 = array(
							'tax_id' => $parent_id,
							'tax_name' => $tax_group_name,
							'is_group' => 10,
							'tax_val' => 0,
							'is_delete' => 10,
							'tax_stat' => 30,
							'account_no' => $acc
							); 		
			foreach($tax_group_array as $grp_val)
			{
				$data2[] = array(
							'group_index' => $this->make_single_uuid(),
							'group_name' => $tax_group_name,
							'parent_id' => $parent_id,
							'assoc_tax_id' => $grp_val,
							'is_delete' => 10,
							'group_stat' => 30,
							'account_no' => $acc
							); 
			}
			if($this->db->insert('pos_a_taxes', $data1))
			{
				if($this->db->insert_batch('pos_a_taxes_group', $data2))
				{
					return 1;
				} else {
					return 0;	
				}
			} else {
				return 0;
			}
		} else {
			return 2;	
		}
	}
	public function get_single_tax_data($tax_id,$acc)
	{
		$this->db->select('tax_name');	
		$this->db->select('tax_val');	
		$query = $this->db->get_where('pos_a_taxes',array('tax_id' => $tax_id,'account_no' => $acc));
		if($query->num_rows() > 0) { //pile up resolved
			$row = $query->row_array();
			return array($row['tax_name'],$row['tax_val']);
		} else {
			return array('','');
		}				
	}
	public function get_group_tax_data($tax_id,$acc)
	{
		$sql = 'SELECT 
					pos_a_taxes.tax_name as parent_name,
					pos_a_taxes_group.assoc_tax_id as ass_tax_id,
					(
						select tax_name from pos_a_taxes where pos_a_taxes_group.assoc_tax_id = pos_a_taxes.tax_id
					) as tax_name
				FROM (`pos_a_taxes`)
				JOIN `pos_a_taxes_group` ON `pos_a_taxes`.`tax_id` = `pos_a_taxes_group`.`parent_id`
				WHERE `pos_a_taxes`.`tax_id` =  ?
				AND `pos_a_taxes_group`.`account_no` =  ?';
		$query = $this->db->query($sql,array($tax_id,$acc));
		if($query != false){
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->ass_tax_id] = $row->tax_name;
			}
			return array('group_tax_id_name' => $array,'parent_name' => $row->parent_name);
		} else {
			return array();
		}						
	}
	public function delete_group_tax($grp_tax_id,$acc)
	{
		$this->db->select('location');
		$this->db->select('loc_id');
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc, 'outlet_tax' => $grp_tax_id,'outlet_stat' => 30));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$outlets[] = anchor(base_url('outlet/'.$row->loc_id.'/edit'),$row->location,'class="btn btn-xs btn-info"');
			}
			if(count($outlets) > 1)
			{
				$lastItem = array_pop($outlets);
				$text = implode(', ', $outlets); 
				$text .= ' and '.$lastItem;
			} else {
				$text = $outlets[0];	
			}
			return array(
						'stat' => 0,
						'error_str' => 'This tax is the default locale tax for '.$text.' ... so it can`t be deleted. 
									Please change outlets`s default locale tax to some other tax and try again.'
					);
		}
		$this->db->select('count(distinct b.main_product) + count(distinct c.variant_id) as counted',false);
		$this->db->from('pos_a_taxes as a');
		$this->db->join('pos_i1_products_tax as b','b.tax_id = a.tax_id','left');
		$this->db->join('pos_i1_products_tax_variant as c','c.tax_id = a.tax_id','left');
		$this->db->where('a.tax_id',$grp_tax_id);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		$row = $query->row_array();
		if($row['counted'] > 0)
		{
			return array(
						'stat' => 0,
						'error_str' => 'This tax is associated to "'.$row['counted'].'" products, so it can`t be deleted. 
									Please change those product tax to some other tax and try again.'
					);
		}
		$this->db->where(array('account_no' => $acc,'parent_id' => $grp_tax_id));
		$this->db->update('pos_a_taxes_group', array('group_stat' => 120));
		$this->db->where(array('account_no' => $acc,'tax_id' => $grp_tax_id));
		if($this->db->update('pos_a_taxes', array('tax_stat' => 120)))
		{
			return array(
						'stat' => 1,
						'error_str' => 'Group tax successfully deleted.'
					);
		}
	}
	public function delete_single_tax($single_tax_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$this->db->where('assoc_tax_id',$single_tax_id);	
		$this->db->where('group_stat',30);	
		$query = $this->db->get_where('pos_a_taxes_group',array('account_no' => $acc));
		$row = $query->row_array();
		if($row['counted'] > 0)
		{
			$this->db->select('pos_a_taxes_group.group_name as tax_name');
			$this->db->from('pos_a_taxes');
			$this->db->join('pos_a_taxes_group','pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id');
			$this->db->where(array('pos_a_taxes.account_no' => $acc, 'pos_a_taxes.tax_id' => $single_tax_id,'pos_a_taxes.tax_stat' => 30));
			$query = $this->db->get();
			foreach($query->result() as $row)
			{
				$associates[] = $row->tax_name;
			}
			$count = count($associates);
			if($count > 1)
			{
				$lastItem = array_pop($associates);
				$text = implode(', ', $associates); 
				$text .= ' and '.$lastItem;
			} else {
				$text = $associates[0];	
			}
			$text_str = 'This tax is associated to '.$count.' group tax(es), like "'.$text.'", so it cant be deleted. Please remove it from group(s) and try again.';
			return array(
						'stat' => 2,
						'error_str' => $text_str
						);
		}
		$this->db->select('loc_id');
		$this->db->select('location');
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc, 'outlet_tax' => $single_tax_id,'outlet_stat' => 30));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$outlets[] = anchor(base_url('outlet/'.$row->loc_id.'/edit'),$row->location,'class="btn btn-xs btn-primary"');
			}
			if(count($outlets) > 1)
			{
				$lastItem = array_pop($outlets);
				$text = implode(', ', $outlets); 
				$text .= ' and '.$lastItem;
			} else {
				$text = $outlets[0];	
			}
			return array(
						'stat' => 0,
						'error_str' => 'This tax is the default locale tax for "'.$text.'", so it can`t be deleted. 
									Please change outlets default locale tax to some other tax and try again.'
					);
		}
		$this->db->select('count(distinct b.main_product) + count(distinct c.variant_id) as counted',false);
		$this->db->from('pos_a_taxes as a');
		$this->db->join('pos_i1_products_tax as b','b.tax_id = a.tax_id','left');
		$this->db->join('pos_i1_products_tax_variant as c','c.tax_id = a.tax_id','left');
		$this->db->where('a.tax_id',$single_tax_id);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		$row = $query->row_array();
		if($row['counted'] > 0)
		{
			return array(
						'stat' => 0,
						'error_str' => 'This tax is associated to "'.$row['counted'].'" products, so it can`t be deleted. 
									Please change those product tax to some other tax and try again.'
					);
		}
		$this->db->where(array('account_no' => $acc,'tax_id' => $single_tax_id));
		if($this->db->update('pos_a_taxes', array('tax_stat' => 120)))
		{
			return array(
						'stat' => 1,
						'error_str' => 'Tax successfully deleted.'
					);
		}
		
	}
	public function update_group_tax($grp_tax_id,$parent_name,$insert,$delete,$acc)
	{
		$this->db->select('tax_name'); //check name exists
		$this->db->like('tax_name', $parent_name); 
		$this->db->where('tax_id !=',$grp_tax_id);
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query->num_rows() < 1) { 
			$this->db->trans_begin();
			$this->db->trans_start();
			// update group name
			$this->db->set('a.tax_name', $parent_name);
			$this->db->set('b.group_name', $parent_name);
			
			$this->db->where('a.tax_id', $grp_tax_id);
			$this->db->where('a.tax_id = b.parent_id');
			$this->db->update('pos_a_taxes as a, pos_a_taxes_group as b');
			
			if(count($insert) > 0)
			{
				$data = array();
				foreach($insert as $value)
				{
					$data[] = array(
									'group_index' => $this->make_single_uuid(),
									'group_name' => $parent_name,
									'parent_id' => $grp_tax_id,
									'assoc_tax_id' => $value,
									'is_delete' => 10,
									'group_stat' => 30,
									'account_no' => $acc	
								);
				}
				$this->db->insert_batch('pos_a_taxes_group', $data);
			}
			if(count($delete) > 0)
			{
				//delete groups
				$this->db->where_in('assoc_tax_id',$delete);	
				$this->db->where('parent_id', $grp_tax_id);
				$this->db->where('account_no', $acc);
				$this->db->delete('pos_a_taxes_group');			
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE)
			{			
				return 0;
			} else {
				return 1;	
			}
		} else {
			return 2;	
		}
	}
	public function update_single_tax($single_tax_id,$single_tax_name,$single_tax_rate,$acc)
	{
		$this->db->select('tax_name'); //check name exists
		$this->db->like('tax_name', $single_tax_name); 
		$this->db->where('tax_id !=',$single_tax_id);
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query->num_rows() < 1) { 
			$data = array(
						   'tax_name' => $single_tax_name,
						   'tax_val' => $single_tax_rate
						);
			
			$this->db->where(array('tax_id' => $single_tax_id, 'account_no' => $acc));
			if($this->db->update('pos_a_taxes', $data))
			{
				return 1;	
			} else {
				return 0;
			}
		} else {
			return 2;	
		}
	}
	public function get_tax_id_if_like($str,$acc)
	{
		$this->db->select('tax_id');
		$this->db->where('tax_name', $str); 
		$query = $this->db->get_where('pos_a_taxes',array('account_no' => $acc));
		if($query->num_rows() > 0) { 
			$row = $query->row_array();
			return $row['tax_id'] == "" ? NULL : $row['tax_id'];
		} else {
			return NULL;
		}				
	}
}
?>