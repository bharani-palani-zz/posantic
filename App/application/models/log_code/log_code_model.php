<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Log_code_model extends CI_Model
{
	public function get_all_log_codes_dropdown()
	{
		$this->db->order_by('log_name');
		$this->db->where_in('sector',array('sales','inventory','product'));
		$query = $this->db->get('pos_1f_log_codes');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[strtoupper(str_replace(array("_")," ",$row->sector))][$row->log_index] = $row->log_name;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_sale_only_log_codes_dropdown()
	{
		$this->db->order_by('log_name');
		$this->db->where_in('sector',array('sales'));
		$query = $this->db->get('pos_1f_log_codes');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[strtoupper(str_replace(array("_")," ",$row->sector))][$row->log_index] = $row->log_name;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_log_codes_wrt_sector($sector)
	{
		$this->db->order_by('log_name');
		$this->db->where_in('sector',$sector);
		$query = $this->db->get('pos_1f_log_codes');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[strtoupper(str_replace(array("_")," ",$row->sector))][$row->log_index] = $row->log_name;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_variant_logs($product_id,$limit,$start,$get,$acc)
	{
		$this->db->select('variant_index as indexed',false);
		$this->db->select('e.updated_at as updated_at',false); //waiting here to add sale transaction time
		$this->db->select('GROUP_CONCAT(distinct c.attribute_val order by cc.cust_var_value asc separator " / ") as prod_name',false);
		$this->db->select('f.log_name as log');						
		$this->db->select('g.display_name as user',false);
		$this->db->select('h.location');
		$this->db->select('e.feed as feed',false); // waiting here to add transaction sale code
		
		$this->db->from('pos_i1_products as a');						
		$this->db->join('pos_i1_products_1_variants as b','b.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as c','c.variant_id = b.variant_index','left');
		$this->db->join('pos_i1_0_cust_variant_types as cc','cc.cust_var_id = c.attribute_id','left');
		$this->db->join('pos_i1_products_log_variants as e','e.master_product = a.product_id and e.variant_id = b.variant_index','left');
		$this->db->join('pos_1f_log_codes as f','f.log_index = e.log_code','left');
		$this->db->join('pos_e_login as g','g.user_id = e.user_id','left');
		$this->db->join('pos_b_locations as h','h.loc_id = e.location','left');
		
		// waiting to join transaction table
		if($get['date_start'] != "" && $get['date_end'] != "")
		{
			$this->db->where('e.updated_at >=', $get['date_start']);
			$this->db->where('e.updated_at <=', $get['date_end']);
		}
		if(is_numeric($get['log_code']))
		{
			$this->db->where('e.log_code', $get['log_code']);
		}
		if(strlen($get['users']) > 0)
		{
			$this->db->where('g.user_id', $get['users']);
		}
		if(strlen($get['outlet']) > 0)
		{
			$this->db->where('h.loc_id', $get['outlet']);
		}
		$this->db->where('a.product_id', $product_id);
		$this->db->where('a.account_no',$acc);
		if(is_numeric($limit) && is_numeric($start)){$this->db->limit($start,$limit);} else {$this->db->limit(1000,0);}
		$this->db->group_by(array('indexed', 'loc_id', 'log','updated_at'));
		$this->db->order_by("updated_at","desc");
		
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['updated_at'][] = $row->updated_at;
				$array['prod_name'][] = $row->prod_name;
				$array['user'][] = $row->user;
				$array['log'][] = $row->log;
				$array['feed'][] = $row->feed;
				$array['location'][] = $row->location;
			}
			return (is_numeric($limit) && is_numeric($start)) ? $array : $query->num_rows();
		} else {
			return NULL;	
		}
		
	}

	public function get_single_variant_logs($variant_id,$limit,$start,$get,$acc)
	{
		$this->db->select('b.variant_index as indexed',false);
		$this->db->select('e.updated_at as updated_at',false); //waiting here to add sale transaction time
		$this->db->select('f.log_name as log');						
		$this->db->select('g.display_name as user',false);
		$this->db->select('h.location');
		$this->db->select('e.feed as feed',false); // waiting here to add transaction sale code
		
		$this->db->from('pos_i1_products as a');						
		$this->db->join('pos_i1_products_1_variants as b','b.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_log_variants as e','e.variant_id = b.variant_index','left');
		$this->db->join('pos_1f_log_codes as f','f.log_index = e.log_code','left');
		$this->db->join('pos_e_login as g','g.user_id = e.user_id','left');
		$this->db->join('pos_b_locations as h','h.loc_id = e.location','left');
		
		// waiting to join transaction table
		if($get['date_start'] != "" && $get['date_end'] != "")
		{
			$this->db->where('e.updated_at >=', $get['date_start']);
			$this->db->where('e.updated_at <=', $get['date_end']);
		}
		if(is_numeric($get['log_code']))
		{
			$this->db->where('e.log_code', $get['log_code']);
		}
		if(strlen($get['users']) > 0)
		{
			$this->db->where('g.user_id', $get['users']);
		}
		if(strlen($get['outlet']) > 0)
		{
			$this->db->where('h.loc_id', $get['outlet']);
		}
		$this->db->where('b.variant_index', $variant_id);
		$this->db->where('a.account_no',$acc);
		if(is_numeric($limit) && is_numeric($start)){$this->db->limit($start,$limit);} else {$this->db->limit(1000,0);}
		$this->db->group_by(array('indexed', 'location', 'log','updated_at'));
		$this->db->order_by("updated_at","desc");
		
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['updated_at'][] = $row->updated_at;
				$array['user'][] = $row->user;
				$array['log'][] = $row->log;
				$array['feed'][] = $row->feed;
				$array['location'][] = $row->location;
			}
			return (is_numeric($limit) && is_numeric($start)) ? $array : $query->num_rows();
		} else {
			return NULL;	
		}
		
	}
	public function get_product_logs($product_id,$limit,$start,$get,$acc)
	{
		$this->db->select('a.product_id as indexed',false);
		$this->db->select('e.updated_at as updated_at',false); //waiting here to add sale trx time
		$this->db->select('f.log_name as log');						
		$this->db->select('g.display_name as user',false);
		$this->db->select('h.location');
		$this->db->select('e.feed as feed',false);
		
		$this->db->from('pos_i1_products as a');						
		$this->db->join('pos_i1_products_log as e','e.master_product = a.product_id','left');
		$this->db->join('pos_1f_log_codes as f','f.log_index = e.log_code','left');
		$this->db->join('pos_e_login as g','g.user_id = e.user_id','left');
		$this->db->join('pos_b_locations as h','h.loc_id = e.location','left');
		// waiting to join transaction table
		if($get['date_start'] != "" && $get['date_end'] != "")
		{
			$this->db->where('e.updated_at >=', $get['date_start']);
			$this->db->where('e.updated_at <=', $get['date_end']);
		}
		if(is_numeric($get['log_code']))
		{
			$this->db->where('e.log_code', $get['log_code']);
		}
		if(strlen($get['users']) > 0)
		{
			$this->db->where('g.user_id', $get['users']);
		}
		if(strlen($get['outlet']) > 0)
		{
			$this->db->where('h.loc_id', $get['outlet']);
		}
		$this->db->where('a.product_id', $product_id);
		$this->db->where('a.account_no',$acc);
		if(is_numeric($limit) && is_numeric($start)){$this->db->limit($start,$limit);} else {$this->db->limit(1000,0);}
		$this->db->group_by(array('indexed', 'loc_id', 'log','updated_at'));
		$this->db->order_by("updated_at","desc");
		$this->db->order_by("location","asc");
		
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['updated_at'][] = $row->updated_at;
				$array['user'][] = $row->user;
				$array['log'][] = $row->log;
				$array['feed'][] = $row->feed;
				$array['location'][] = $row->location;
			}
			return (is_numeric($limit) && is_numeric($start)) ? $array : $query->num_rows();
		} else {
			return NULL;	
		}
		
	}

}