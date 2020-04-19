<?php
class Brand_and_tag_model extends CI_Model
{
	public function brand_combo($acc)
	{
		$rows = $this->db->get_where('pos_i1_product_brand',array('account_no' => $acc)); 
		if($rows!=false)
		{
			$array = array(NULL => '');
			foreach($rows->result() as $row)
			{
				$array[$row->brand_index] = $row->brand_name;	
			}
			return $array;
		} else {
			return array();
		}
	}
	public function get_brands($acc)
	{
		$query = $this->db->get_where('pos_i1_product_brand',array('account_no' => $acc));
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
			return null;	
		}
		
	}
	public function get_brand_details($brand_id,$acc)
	{
		$query = $this->db->get_where('pos_i1_product_brand',array('account_no' => $acc, 'brand_index' => $brand_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return array($row['brand_index'],$row['brand_name']);
		} else {
			return false;	
		}
	}
	public function update_brand($brand_id,$brand_name,$acc)
	{
		$update = array(
						'brand_name' => $brand_name,
						);		
		$this->db->where('brand_index', $brand_id);
		$this->db->where('account_no', $acc);
		if($this->db->update('pos_i1_product_brand', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function delete_brand($brand_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_6_brand',array('account_no' => $acc,'brand_id' => $brand_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$count = $row['counted'];
			return array(
				'stat' => 2,
				'error_str' => $count.' Product(s) associated to this brand. 
							Please remove or change product(s) to some other brand and try again.'
			);
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
			);
		}	
		if($this->db->delete('pos_i1_product_brand', array('brand_index' => $brand_id,'account_no' => $acc)))
		{
			return array(
				'stat' => 1,
				'error_str' => 'Brand Successfully deleted.'
			);
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
			);
		}
			

	}
	public function insert_brand($brand_name,$acc)
	{
		$insert = array(
			'brand_index' => $this->taxes_model->make_single_uuid(),
			'brand_name' => $brand_name,
			'account_no' => $acc
		); 
		if($this->db->insert('pos_i1_product_brand', $insert))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function get_cats($acc)
	{
		$query = $this->db->get_where('pos_i1_product_category',array('account_no' => $acc));
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
			return null;	
		}
	}
	public function insert_cat($cat_name,$acc)
	{
		$insert = array(
			'cat_id' => $this->taxes_model->make_single_uuid(),
			'cat_name' => $cat_name,
			'account_no' => $acc
		); 
		if($this->db->insert('pos_i1_product_category', $insert))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function delete_cat($cat_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_7_category',array('account_no' => $acc,'category_id' => $cat_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$count = $row['counted'];
			if($count > 0)
			{
				return array(
					'stat' => 2,
					'error_str' => $count.' Product(s) associated to this category. Please remove or change product(s) to some other category and try again.'
				);
			}
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
			);
		}	
		if($this->db->delete('pos_i1_product_category', array('cat_id' => $cat_id,'account_no' => $acc)))
		{
			return array(
				'stat' => 1,
				'error_str' => 'Category Successfully deleted.'
			);			
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
			);
		}
			
	}
	public function update_cat($cat_id,$cat_name,$acc)
	{
		$update = array(
						'cat_name' => $cat_name,
						);		
		$this->db->where('cat_id', $cat_id);
		$this->db->where('account_no', $acc);
		if($this->db->update('pos_i1_product_category', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function get_tags($acc)
	{
		$query = $this->db->order_by('tag_name')->get_where('pos_i1_product_tag',array('account_no' => $acc));
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
			return null;	
		}
	}
	public function insert_main_tag($tag_name,$acc)
	{
		$insert = array(
			'tag_id' => $this->taxes_model->make_single_uuid(),
			'tag_name' => $tag_name,
			'account_no' => $acc
		); 
		if($this->db->insert('pos_i1_product_tag', $insert))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function get_tag_details($tag_id,$acc)
	{
		$query = $this->db->get_where('pos_i1_product_tag',array('account_no' => $acc, 'tag_id' => $tag_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return array($row['tag_id'],$row['tag_name']);
		} else {
			return false;	
		}
	}
	public function update_tag($tag_id,$tag_name,$acc)
	{
		$update = array(
						'tag_name' => $tag_name,
						);		
		$this->db->where('tag_id', $tag_id);
		$this->db->where('account_no', $acc);
		if($this->db->update('pos_i1_product_tag', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function delete_main_tag($tag_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_5_tags',array('account_no' => $acc,'tagged_id' => $tag_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$count = $row['counted'];
			if($count < 1)			
			{
				if($this->db->delete('pos_i1_product_tag', array('tag_id' => $tag_id,'account_no' => $acc)))
				{
					return array(
						'stat' => 1,
						'error_str' => 'Tag Successfully deleted.'
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
					'error_str' => $count.' Product(s) associated to this tag. 
								Please remove or change product(s) to some other tag and try again.'
				);
			}
		} else {
			return array(
				'stat' => 0,
				'error_str' => 'Error: Oops! Something Went Wrong! please Try Again'
			);
		}
	}
	public function tag_GetAutocomplete($options = array(),$acc)
    {					
		$this->db->select('*');
		$this->db->like('tag_name', $options['keyword']); 
		$this->db->order_by("tag_name", "asc"); 
		$this->db->limit(10);
		$query = $this->db->get_where('pos_i1_product_tag',array('account_no' => $acc));
		return $query->result();
    }
	public function insert_tag($tag_name,$prd_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$this->db->select('tag_id');
		$this->db->select('tag_name');
		$query = $this->db->get_where('pos_i1_product_tag',array('tag_name' => $tag_name, 'account_no' => $acc));
		$row = $query->row_array();
		if($row['counted'] < 1)
		{
			$tag_id = $this->taxes_model->make_single_uuid();
			$insert = array(
						'tag_id' => $tag_id,
						'tag_name' => $tag_name,
						'account_no' => $acc		
						);	
			if($this->db->insert('pos_i1_product_tag',$insert))
			{
				if(!empty($prd_id))
				{
					$array = array(
								'prd_tag_index' => $this->taxes_model->make_single_uuid(),
								'tagged_id' => $tag_id,
								'product_id' => $prd_id,
								'account_no' => $acc
							);
					$this->db->insert('pos_i1_products_5_tags',$array);
				}
				return array($tag_id,$tag_name);	
			} else {
				return false;	
			}
		} else {
			return array($row['tag_id'],$row['tag_name']);	
		}
	}
	public function get_prd_tags($product_id,$acc)
	{
		$this->db->select('c.tag_id');
		$this->db->select('c.tag_name');
		$this->db->from('pos_i1_products_5_tags as a');
		$this->db->join('pos_i1_products as b','b.product_id = a.product_id and a.account_no = b.account_no');
		$this->db->join('pos_i1_product_tag as c','c.tag_id = a.tagged_id and b.account_no = c.account_no');
		$this->db->where('b.account_no',$acc);
		$this->db->where('b.product_id',$product_id);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->tag_id] = $row->tag_name;
			}
			return $array;				
		} else {
			return false;	
		}
	}
	public function insert_product_tag($tag_id,$prd_id,$acc)
	{
		$insert = array(
					'prd_tag_index' => $this->taxes_model->make_single_uuid(),
					'tagged_id' => $tag_id,
					'product_id' => $prd_id,
					'account_no' => $acc		
					);
		if($this->db->insert('pos_i1_products_5_tags',$insert))
		{
			return true;	
		} else {
			return false;
		}
	}
	public function delete_tag($product_id,$tag_id,$acc)
	{
		if($this->db->delete('pos_i1_products_5_tags', array('product_id' => $product_id,'tagged_id' => $tag_id,'account_no' => $acc)))
		{
			return true;
		} else {
			return false;	
		}
	}
	public function sanitise_bulk_tag($tag_name_array,$acc)
	{
		if(is_array($tag_name_array))
		{
			$id_array = array();
			foreach($tag_name_array as $tag_name)
			{
				$this->db->select('count(*) as counted');
				$this->db->select('tag_id');
				$query = $this->db->get_where('pos_i1_product_tag',array('tag_name' => $tag_name, 'account_no' => $acc));
				$row = $query->row_array();
				if($row['counted'] > 0)
				{
					$id_array[] = $row['tag_id'];
				} else {
					$id = $this->taxes_model->make_single_uuid();
					$insert = array(
								'tag_id' => $id,
								'tag_name' => $tag_name,
								'account_no' => $acc		
								);	
					$this->db->insert('pos_i1_product_tag',$insert);
					$id_array[] = $id;
				}
			}
			return $id_array;
		}
	}
	public function get_cat_id_wrt_name($cat_name,$acc)
	{
		$this->db->select('cat_id');	
		$query = $this->db->get_where('pos_i1_product_category',array('account_no' => $acc, 'cat_name' => $cat_name));
		if($query->num_rows() > 0) 
		{ 
			$row = $query->row_array();
			return !empty($row['cat_id']) ? $row['cat_id'] : NULL;
		} else {
			return NULL;	
		}
	}
	public function get_brand_id_wrt_name($brand_name,$acc)
	{
		$this->db->select('brand_index');	
		$query = $this->db->get_where('pos_i1_product_brand',array('account_no' => $acc, 'brand_name' => $brand_name));
		if($query->num_rows() > 0) 
		{ 
			$row = $query->row_array();
			return !empty($row['brand_index']) ? $row['brand_index'] : NULL;
		} else {
			return NULL;	
		}
	}
	public function get_tag_names_if_ids($id_array,$acc)
	{
		$this->db->select('tag_id');	
		$this->db->select('tag_name');	
		$this->db->where_in('tag_id',$id_array);
		$query = $this->db->get_where('pos_i1_product_tag',array('account_no' => $acc));
		if($query->num_rows() > 0) 
		{ 
			foreach($query->result() as $row)
			{
				$array[$row->tag_id] = $row->tag_name;
			}
			return $array;					
		} else {
			return false;
		}
	}
	public function get_cat_details($cat_id,$acc)
	{
		$query = $this->db->get_where('pos_i1_product_category',array('account_no' => $acc, 'cat_id' => $cat_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return array($row['cat_id'],$row['cat_name']);
		} else {
			return false;	
		}
	}
	public function update_batch_tags($tag_array,$product_id,$acc)
	{
		if(count($tag_array) > 0)
		{
			$this->db->delete('pos_i1_products_5_tags',array('product_id' => $product_id, 'account_no' => $acc));			
			foreach($tag_array as $tag_id)
			{
				$insert[] = array(
							'prd_tag_index' => $this->taxes_model->make_single_uuid(),
							'tagged_id' => $tag_id,
							'product_id' => $product_id,
							'account_no' => $acc			
							);
			}
			$this->db->insert_batch('pos_i1_products_5_tags', $insert); 
		} else {
			$this->db->delete('pos_i1_products_5_tags',array('product_id' => $product_id, 'account_no' => $acc));			
		}

	}
}
?>