<?php
class Inventoryjson_model extends CI_model
{
    public function __construct() 
    {
        parent::__construct();
    }
	public function get_ST_products($take_id,$acc)
	{
		$this->db->select('a.product_scale as scale');
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.track_inventory
						   when 2 then c.track_inventory
						   when 1 then b.track_inventory
						END as track_inventory
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.created_at
						   when 2 then c.created_at
						   when 1 then b.created_at
						END as created_at
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.updated_at
						   when 2 then c.updated_at
						   when 1 then b.updated_at
						END as updated_at
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.sku
						   when 2 then c.sku
						   when 1 then b.sku
						END as sku		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.price
						   when 2 then c.price
						   when 1 then b.price
						END as cost_price		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.retail_price
						   when 2 then c.retail_price
						   when 1 then b.retail_price
						END as retail_price		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.status
						   when 2 then c.status
						   when 1 then b.status
						END as status_code
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then ifnull((select current_stock from pos_i2_a_inventory_variant where location = h.loc_id and account_no = h.account_no and variant_id = if(a.product_scale = 3,d.variant_index,a.product_id)),0)
						   when 2 then ifnull((select current_stock from pos_i2_a_inventory where location = h.loc_id and account_no = h.account_no and product_id = if(a.product_scale = 3,d.variant_index,a.product_id)),0)
						   when 1 then ifnull((select current_stock from pos_i2_a_inventory where location = h.loc_id and account_no = h.account_no and product_id = if(a.product_scale = 3,d.variant_index,a.product_id)),0)
						END as expected
						',false);
		$this->db->select('a.handle');
		$this->db->select('if(a.product_scale = 3,d.variant_index,a.product_id) as indexed',false);
		$this->db->select('if(a.product_scale = 3,"true","false") as is_variant_product',false);
		$this->db->select('if(a.product_scale = 3,
							concat_ws("\n / ",a.product_name,GROUP_CONCAT(distinct f.attribute_val order by dd.cust_var_value separator " / ")),
								if(a.product_scale = 2,a.product_name,if(a.product_scale = 1,a.product_name,null))) as prod_name',false);
		$this->db->from('pos_i1_products as a , pos_j3_stock_take as g');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');		
		$this->db->join('pos_i1_products_1_variants_attributes as f','d.variant_index = f.variant_id','left');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = f.attribute_id','left');
		$this->db->join('pos_b_locations as h','h.loc_id = g.location','left');

		$this->db->where('g.stocktake_index',$take_id);
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.product_scale !=',4);
		$this->db->where('h.outlet_stat',30);
		$or_where = '(b.status = 30 or c.status = 30 or d.status = 30)';		
		$this->db->where($or_where,NULL,FALSE);		
		
		
		$this->db->group_by('indexed');
        $query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->indexed]['id'] = $row->indexed;
				$array[$row->indexed]['is_variant_product'] = $row->is_variant_product;
				$array[$row->indexed]['name'] = $row->prod_name;
				$array[$row->indexed]['handle'] = $row->handle;
				$array[$row->indexed]['sku'] = $row->sku;
				$array[$row->indexed]['scale'] = $row->scale;
				$array[$row->indexed]['created_at'] = $row->created_at;
				$array[$row->indexed]['track_inventory'] = $row->track_inventory;
				$array[$row->indexed]['cost_price'] = $row->cost_price;
				$array[$row->indexed]['retail_price'] = $row->retail_price;
				$array[$row->indexed]['status_code'] = $row->status_code;
				$array[$row->indexed]['updated_at'] = $row->updated_at;
				$array[$row->indexed]['expected'] = $row->expected;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_ST_id_main_data($acc)
	{
		$this->db->select('a.stocktake_index');
		$this->db->select('a.stocktake_name');
		$this->db->select('a.created_at');
		$this->db->select('c.status_code');
		$this->db->select('c.status_id');
		$this->db->select('b.location');
		$this->db->select('b.loc_id');
		$this->db->from('pos_j3_stock_take as a');
		$this->db->join('pos_b_locations as b','a.location = b.loc_id','left');
		$this->db->join('pos_1e_status_codes as c','c.status_id = a.take_stat','left');
		$this->db->where('a.take_stat',50);
		$this->db->where('a.account_no',$acc);
		$this->db->limit(1000);
        $query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->stocktake_index]['id'] = $row->stocktake_index;
				$array[$row->stocktake_index]['name'] = $row->stocktake_name;
				$array[$row->stocktake_index]['created_at'] = $row->created_at;
				$array[$row->stocktake_index]['status_code'] = $row->status_code;
				$array[$row->stocktake_index]['status_id'] = $row->status_id;
				$array[$row->stocktake_index]['outlet_name'] = $row->location;
				$array[$row->stocktake_index]['outlet_id'] = $row->loc_id;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_ST_id_sub_data($acc)
	{
		$this->db->select('a.st_prd_index as indexed');
		$this->db->select('a.stock_take_id as stock_take_id');
		$this->db->select('a.product_id');
		$this->db->select('a.variant_id');
		$this->db->select('a.expected');
		$this->db->select('a.counted');
		$this->db->select('a.cost_gain');
		$this->db->select('a.cost_loss');
		$this->db->select('a.count_gain');
		$this->db->select('a.count_loss');
		$this->db->from('pos_j3_stock_take_products as a');
		$this->db->join('pos_j3_stock_take as b','b.stocktake_index = a.stock_take_id and b.account_no = a.account_no','left');
		$this->db->where('a.account_no',$acc);
		$this->db->where('b.take_stat',50);
        $query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->indexed]['id'] = $row->indexed;
				$array[$row->indexed]['stock_take_id'] = $row->stock_take_id;
				$array[$row->indexed]['product_id'] = $row->product_id;
				$array[$row->indexed]['variant_id'] = $row->variant_id;
				$array[$row->indexed]['expected'] = $row->expected;
				$array[$row->indexed]['counted'] = $row->counted;
				$array[$row->indexed]['cost_gain'] = $row->cost_gain;
				$array[$row->indexed]['cost_loss'] = $row->cost_loss;
				$array[$row->indexed]['count_gain'] = $row->count_gain;
				$array[$row->indexed]['count_loss'] = $row->count_loss;
				
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_ST_id_products($take_id,$acc)
	{
		$this->db->select('ifnull(a.variant_id,a.product_id) as product_id',false);
		$this->db->select('a.st_prd_index as id',false);
		$this->db->select('if(c.product_scale = 3,concat_ws(" / ",c.product_name,GROUP_CONCAT(distinct f.attribute_val order by dd.cust_var_value separator " / ")),cc.product_name) as name',false);
		$this->db->select('a.cost_gain');
		$this->db->select('a.cost_loss');
		$this->db->select('a.count_gain');
		$this->db->select('a.count_loss');
		$this->db->select('
						if(c.product_scale = 3,
							ifnull((select current_stock from pos_i2_a_inventory_variant where location = aa.location and account_no = aa.account_no and variant_id = b.variant_index),0),
							ifnull((select current_stock from pos_i2_a_inventory where location = aa.location and account_no = aa.account_no and product_id = cc.product_id),0)
						) as expected',false);
		$this->db->select('a.counted');		
		$this->db->from('pos_j3_stock_take_products as a');
		$this->db->join('pos_j3_stock_take as aa','aa.stocktake_index = a.stock_take_id','left');
		$this->db->join('pos_i1_products_1_variants as b','b.variant_index = a.variant_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as f','b.variant_index = f.variant_id','left');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = f.attribute_id','left');
		$this->db->join('pos_i1_products as c','c.product_id = b.product_id','left');
		$this->db->join('pos_i1_products as cc','cc.product_id = a.product_id','left');
		$this->db->join('pos_b_locations as h','h.loc_id = aa.location','left');

		$this->db->where('a.stock_take_id',$take_id);
		$this->db->where('a.account_no',$acc);
		$this->db->group_by('a.st_prd_index');
        $query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$array = array();
			foreach($query->result() as $row)
			{
				$array[$row->id]['id'] = $row->id;
				$array[$row->id]['product_id'] = $row->product_id;
				$array[$row->id]['name'] = $row->name;
				$array[$row->id]['cost_gain'] = $row->cost_gain;
				$array[$row->id]['cost_loss'] = $row->cost_loss;
				$array[$row->id]['cost_gain'] = $row->cost_gain;
				$array[$row->id]['count_gain'] = $row->count_gain;
				$array[$row->id]['count_loss'] = $row->count_loss;
				$array[$row->id]['expected'] = $row->expected;
				$array[$row->id]['counted'] = $row->counted;
			}
			return $array;
		} else {
			return array();	
		}

	}
	public function post_ST_products($post_data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$query = $this->db->get_where('pos_j3_stock_take_products',array('st_prd_index' => $post_data['index'],'account_no' => $post_data['acc']));	
		if($query->num_rows() < 1)
		{
			//insert
			$insert = array(
						'st_prd_index' => $post_data['index'],
						'stock_take_id' => $post_data['take_id'],
						'product_id' => $post_data['product_id'],
						'variant_id' => $post_data['variant_id'],
						'expected' => $post_data['expected'],
						'counted' => $post_data['counted'],
						'cost_gain' => $post_data['cost_gain'],
						'cost_loss' => $post_data['cost_loss'],
						'count_gain' => $post_data['count_gain'],
						'count_loss' => $post_data['count_loss'],
						'account_no' => $post_data['acc']		
						);	
			$this->db->insert('pos_j3_stock_take_products',$insert);						
		} else {
			// update	
			$update = array(
						'expected' => $post_data['expected'],
						'counted' => $post_data['counted'],
						'cost_gain' => $post_data['cost_gain'],
						'cost_loss' => $post_data['cost_loss'],
						'count_gain' => $post_data['count_gain'],
						'count_loss' => $post_data['count_loss'],
						);
			$this->db->where('account_no',$post_data['acc']);							
			$this->db->where('st_prd_index',$post_data['index']);							
			$this->db->where('stock_take_id',$post_data['take_id']);							
			$this->db->update('pos_j3_stock_take_products',$update);									
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return array('status' => 'failed','id' => $post_data['take_id']);
		} else {
			return array('status' => 'success','id' => $post_data['take_id']);
		}

	}
	public function update_ST_products($post_data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$query = $this->db->get_where('pos_j3_stock_take_products',array('st_prd_index' => $post_data['index'],'account_no' => $post_data['acc']));
		if($query->num_rows() > 0)
		{
			$update = array(
						'expected' => $post_data['expected'],
						'counted' => $post_data['counted'],
						'cost_gain' => $post_data['cost_gain'],
						'cost_loss' => $post_data['cost_loss'],
						'count_gain' => $post_data['count_gain'],
						'count_loss' => $post_data['count_loss'],
						);
			$this->db->where('st_prd_index',$post_data['index']);							
			$this->db->where('stock_take_id',$post_data['take_id']);							
			$this->db->where('account_no',$post_data['acc']);							
			$this->db->update('pos_j3_stock_take_products',$update);									
		} else {
			$insert = array(
						'st_prd_index' => $post_data['index'],
						'stock_take_id' => $post_data['take_id'],
						'product_id' => $post_data['product_id'],
						'variant_id' => $post_data['variant_id'],
						'expected' => $post_data['expected'],
						'counted' => $post_data['counted'],
						'cost_gain' => $post_data['cost_gain'],
						'cost_loss' => $post_data['cost_loss'],
						'count_gain' => $post_data['count_gain'],
						'count_loss' => $post_data['count_loss'],
						'account_no' => $post_data['acc']
						);
			$this->db->insert('pos_j3_stock_take_products',$insert);									
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return array('status' => 'failed','id' => $post_data['take_id']);
		} else {
			return array('status' => 'success','id' => $post_data['take_id']);
		}
	}
}
?>