<?php
class Promotion_model extends CI_Model
{
    public function __construct() 
    {
        parent::__construct();
    }
	public function get_product_id($sku,$acc)
	{
		$this->db->select('
						CASE a.product_scale
						   when 4 then e.blend_product_id
						   when 3 then d.variant_index
						   when 2 then c.product_id
						   when 1 then b.product_id
						END as product_id		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 4 then "STD"
						   when 3 then "VAR"
						   when 2 then "STD"
						   when 1 then "STD"
						END as product_scale		
						',false);
		$this->db->select('a.product_id as related_product');
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->or_like('b.sku', $sku); 
		$this->db->or_like('c.sku', $sku); 
		$this->db->or_like('d.sku', $sku); 
		$this->db->or_like('e.sku', $sku); 				
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();						
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return array($row['product_scale'],$row['product_id'],$row['related_product']);
		} else {
			return false;	
		}
	}
	public function insert_prom_group($data,$acc)
	{
		$prom_id = $this->taxes_model->make_single_uuid();
		$insert = array(
					'promotion_index' => $prom_id,
					'promo_name' => $data['promo_name'],
					'promo_start' => $data['promo_start'],
					'promo_end' => $data['promo_end'],
					'for_store' => $data['promo_outlet'],
					'customer_group' => $data['promo_cust_group'],
					'prom_updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'account_no' => $acc,		
						);
		if($this->db->insert('pos_i6_promgroup',$insert))
		{
			return $prom_id;	
		} else {
			return false;	
		}
	}
	public function import_promotion($data,$acc)
	{
		$array = $this->get_product_id($data['sku'],$acc);
		if($array != false)
		{
			list($scale,$product_id,$related_product) = $array;
			if($scale == "STD")
			{
				$insert['STD'] = array(
							'prom_index_id' => $this->taxes_model->make_single_uuid(),
							'promotion_id' => $data['prom_id'],
							'main_product_id' => $related_product,
							'margin' => $data['margin'],
							'discount' => $data['discount'],
							'retail_price' => $data['retail_price'],
							'loyalty' => $data['loyalty_set'],
							'min_qty' => $data['min_units'],
							'max_qty' => $data['max_units'],
							'account_no' => $acc,
								);	
				
			} else {
				$insert['VAR'] = array(
							'prom_var_index_id' => $this->taxes_model->make_single_uuid(),
							'promotion_id' => $data['prom_id'],
							'parent_product' => $related_product,
							'variant_id' => $product_id,
							'margin' => $data['margin'],
							'discount' => $data['discount'],
							'retail_price' => $data['retail_price'],
							'loyalty' => $data['loyalty_set'],
							'min_qty' => $data['min_units'],
							'max_qty' => $data['max_units'],
							'account_no' => $acc,
								);	
			}
			if(array_key_exists('STD',$insert))
			{
				if($this->db->insert('pos_i6_promotion',$insert['STD']))
				{
					return 1;	
				} else {
					return 0;	
				}				
			}
			if(array_key_exists('VAR',$insert))
			{
				if($this->db->insert('pos_i6_promotion_variant',$insert['VAR']))
				{
					return 1;	
				} else {
					return 0;	
				}				
			}
		} else {
			return 3;	
		}
	}
	public function get_promotions($acc)
	{
		$this->db->select('a.promotion_index');
		$this->db->select('a.promo_name');
		$this->db->select('a.promo_start');
		$this->db->select('a.promo_end');
		$this->db->select('a.for_store');
		$this->db->select('b.group_name');
		$this->db->select('c.location');
		$this->db->select('a.prom_updated_at');
		$this->db->from('pos_i6_promgroup as a');
		$this->db->join('pos_i2_b_customer_group as b','b.grp_index = a.customer_group');
		$this->db->join('pos_b_locations as c','c.loc_id = a.for_store','left');
		$this->db->where('a.account_no',$acc);
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
	public function promotion_detail($prom_id,$acc)
	{
		$this->db->select('*');
		$this->db->from('pos_i6_promgroup as b');
		$this->db->join('pos_i2_b_customer_group as c','c.grp_index = b.customer_group');
		$this->db->join('pos_b_locations as d','d.loc_id = b.for_store','left');
		$this->db->where('b.promotion_index',$prom_id);
		$this->db->where('b.account_no',$acc);
		$query = $this->db->get();
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
			return NULL;	
		}		
	}
	public function get_promotion_subproducts_sql($limit,$start,$prom_id,$acc)
	{
		if($limit > 0)
		{
			$limit_str = 'limit '.$start.','.$limit;
		} else {
			$limit_str = 'limit 1000';			
		}
		$sql = 'select * from (
					SELECT `a`.`prom_index_id` as child_id,d.product_id as product_id,`a`.`margin` as margin,`a`.`discount` as discount,`a`.`retail_price` as retail_price,CASE d.product_scale 
					 when 4 then g.price 
					 when 2 then f.price 
					 when 1 then e.price 
					 END as supplier_price,`a`.`min_qty`,`a`.`max_qty`,`a`.`loyalty`,d.product_name as product_name
					FROM `pos_i6_promotion` as a
					LEFT JOIN `pos_i6_promgroup` as b ON `b`.`promotion_index` = `a`.`promotion_id` and a.account_no = b.account_no
					LEFT JOIN `pos_i1_products` as d ON `a`.`account_no` = `d`.`account_no` and d.product_id = a.main_product_id
					LEFT JOIN `pos_i1_products_2_num` as e ON `a`.`main_product_id` = `e`.`product_id` and a.account_no = e.account_no
					LEFT JOIN `pos_i1_products_3_kilo` as f ON `a`.`main_product_id` = `f`.`product_id` and a.account_no = f.account_no
					LEFT JOIN `pos_i1_products_0_blend` as g ON `a`.`main_product_id` = `g`.`blend_product_id` and a.account_no = g.account_no
					WHERE `a`.`account_no` =  ?
					AND `b`.`promotion_index` =  ?
					
					union 
					
					SELECT `a`.`prom_var_index_id` as child_id,e.variant_index as product_id,`a`.`margin` as margin,`a`.`discount` as discount,`a`.`retail_price` as retail_price,e.price as supplier_price,`a`.`min_qty`,`a`.`max_qty`,`a`.`loyalty`,concat_ws(" / ",d.product_name,GROUP_CONCAT(distinct f.attribute_val order by g.cust_var_value separator " / ")) as product_name
					FROM `pos_i6_promotion_variant` as a
					LEFT JOIN `pos_i6_promgroup` as b ON `b`.`promotion_index` = `a`.`promotion_id` and a.account_no = b.account_no
					LEFT JOIN `pos_i1_products` as d ON `a`.`account_no` = `d`.`account_no` and d.product_id = a.parent_product
					LEFT JOIN `pos_i1_products_1_variants` as e ON `a`.`parent_product` = `e`.`product_id` and a.variant_id = e.variant_index and a.account_no = e.account_no
					LEFT JOIN `pos_i1_products_1_variants_attributes` as f ON `a`.`account_no` = `f`.`account_no` and f.variant_id = a.variant_id
					LEFT JOIN `pos_i1_0_cust_variant_types` as g ON `g`.`cust_var_id` = `f`.`attribute_id`
					WHERE `a`.`account_no` =  ?
					AND `b`.`promotion_index` =  ?
					group by child_id
					ORDER BY `product_name` asc ) as joined_table '.$limit_str ;
		$query = $this->db->query($sql, array($acc,$prom_id,$acc,$prom_id));				
		
		return $query;
	}
	public function get_promotion_subproducts_count($limit,$start,$prom_id,$acc)
	{
		$query = $this->get_promotion_subproducts_sql($limit,$start,$prom_id,$acc);
		return $query->num_rows();
	}
	public function get_promotion_subproducts_data($limit,$start,$prom_id,$acc)
	{
		$query = $this->get_promotion_subproducts_sql($limit,$start,$prom_id,$acc);
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
	public function delete_single_product($child_id,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$this->db->where('prom_index_id',$child_id);	
		$this->db->where('account_no',$acc);	
		$this->db->delete('pos_i6_promotion');
		
		$this->db->where('prom_var_index_id',$child_id);	
		$this->db->where('account_no',$acc);	
		$this->db->delete('pos_i6_promotion_variant');
		
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function get_autocomplete_promo($search,$acc)
	{
		$this->db->select('CASE a.product_scale 
								when 4 then e.loyalty 
								when 3 then d.loyalty
								when 2 then c.loyalty 
								when 1 then b.loyalty
							END as loyalty',false);
		$this->db->select('CASE a.product_scale 
								when 4 then e.price 
								when 3 then d.price
								when 2 then c.price 
								when 1 then b.price
							END as price',false);							
		$this->db->select('CASE a.product_scale 
								when 4 then e.retail_price 
								when 3 then d.retail_price
								when 2 then c.retail_price 
								when 1 then b.retail_price
							END as retail_price',false);							
		$this->db->select('CASE a.product_scale 
								when 4 then e.sku 
								when 3 then d.sku
								when 2 then c.sku 
								when 1 then b.sku
							END as sku',false);							
		$this->db->select('CASE a.product_scale 
								when 4 then e.margin 
								when 3 then d.margin
								when 2 then c.margin 
								when 1 then b.margin
							END as margin',false);							
		$this->db->select('if(a.product_scale = 3,d.variant_index,a.product_id) as product_id',false);							
		$this->db->select('a.product_id as related_product');							
		$this->db->select('if(a.product_scale = 3,concat_ws(" / ",a.product_name,GROUP_CONCAT(distinct f.attribute_val order by g.cust_var_value separator " / ")),a.product_name) as product_name',false);							
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->join('pos_i1_products_1_variants_attributes as f','a.account_no = f.account_no and f.variant_id = d.variant_index','left');
		$this->db->join('pos_i1_0_cust_variant_types as g','g.cust_var_id = f.attribute_id','left');
		
		$this->db->where("
						(a.product_name  LIKE '%".$search."%'
						OR  a.handle  LIKE '%".$search."%'
						OR  b.sku  LIKE '%".$search."%'
						OR  c.sku  LIKE '%".$search."%'
						OR  d.sku  LIKE '%".$search."%'
						OR  e.sku  LIKE '%".$search."%')",
						NULL, false);		
		$this->db->where('a.account_no',$acc);
		$this->db->where("(b.status = 30 or c.status = 30 or d.status = 30 or e.status = 30)"); 
		$this->db->order_by('product_name');
		$this->db->group_by('product_id');
		$this->db->limit(100);
		$query = $this->db->get();
		return $query->result();
	}
	public function insert_ajax_promotion($data)
	{
		if(isset($data['params']))
		{
			$scale = $this->product_model->check_scale($data['params'][0],$data['acc']);
			if(!is_null($scale)) // check belongs to variant or standard
			{
				if($scale == 3) //if variant do this
				{
					$this->db->select('count(*) as counted');
					$query = $this->db->get_where('pos_i6_promotion_variant',array('account_no' => $data['acc'],'variant_id' => $data['params'][0],'promotion_id' => $data['params'][5]));
					if($query->num_rows() > 0)
					{
						$row = $query->row_array();
						$count = $row['counted'];
						if($count < 1)
						{
							$prom_id = $this->taxes_model->make_single_uuid();
							$insert = array(
										'prom_var_index_id' => $prom_id,
										'promotion_id' => $data['params'][5],
										'parent_product' => $data['params'][6],
										'variant_id' => $data['params'][0],
										'margin' => $data['params'][1],
										'discount' => $data['params'][2],
										'retail_price' => $data['params'][3],
										'loyalty' => $data['params'][4],
										'min_qty' => 0,
										'max_qty' => 0,
										'account_no' => $data['acc']
										  );
							if($this->db->insert('pos_i6_promotion_variant',$insert))
							{
								return $prom_id;
							} else {
								return 0;
							}										  
						} else {
							return 2;	
						}
					} else {
						return 0;	
					}
				} else { //if standard product do this
					$this->db->select('count(*) as counted');
					$query = $this->db->get_where('pos_i6_promotion',array('account_no' => $data['acc'],'main_product_id' => $data['params'][0],'promotion_id' => $data['params'][5]));
					if($query->num_rows() > 0)
					{
						$row = $query->row_array();
						$count = $row['counted'];
						if($count < 1)
						{
							$prom_id = $this->taxes_model->make_single_uuid();
							$insert = array(
										'prom_index_id' => $prom_id,
										'promotion_id' => $data['params'][5],
										'main_product_id' => $data['params'][0],
										'margin' => $data['params'][1],
										'discount' => $data['params'][2],
										'retail_price' => $data['params'][3],
										'loyalty' => $data['params'][4],
										'min_qty' => 0,
										'max_qty' => 0,
										'account_no' => $data['acc']
										  );
							if($this->db->insert('pos_i6_promotion',$insert))
							{
								return $prom_id;
							} else {
								return 0;
							}										  
						} else {
							return 2;	
						}

					} else {
						return 0;	
					}
				}
			} else {
				return 0;	
			}
		} else {
			die('Illegal input');	
		}
	}
	public function update_promotion($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$main_array = array(
						'promo_name' => $data['promo_name'],
						'promo_start' => $data['promo_start'],
						'promo_end' => $data['promo_end'],
						'for_store' => $data['promo_outlet'] == "" ? NULL : $data['promo_outlet'],
						'customer_group' => $data['promo_cust_group'],
						'prom_updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							);
		$this->db->where('promotion_index', $data['main_prom_id']);							
		$this->db->update('pos_i6_promgroup',$main_array);	

		if(isset($data['promotions']['child_id']))
		{
			foreach($data['promotions']['child_id'] as $key => $value)
			{
				$this->db->select('count(*) as counted');
				$query = $this->db->get_where('pos_i6_promotion',array('prom_index_id' => $value, 'account_no' => $data['acc']));
				$rows = $query->row_array();
				if($rows['counted'] > 0)
				{
					$update['standard'][] = array(
									'prom_index_id' => $data['promotions']['child_id'][$key],
									'margin' => $data['promotions']['promo_margin'][$key],
									'discount' => $data['promotions']['promo_disc'][$key],
									'retail_price' => $data['promotions']['promo_mrp'][$key],
									'loyalty' => $data['promotions']['promo_loyalty'][$key],
									'min_qty' => $data['promotions']['promo_min_units'][$key],
									'max_qty' => $data['promotions']['promo_max_units'][$key],
									);
				}
				$this->db->select('count(*) as counted');
				$query = $this->db->get_where('pos_i6_promotion_variant',array('prom_var_index_id' => $value, 'account_no' => $data['acc']));
				$rows = $query->row_array();
				if($rows['counted'] > 0)
				{
					$update['variant'][] = array(
									'prom_var_index_id' => $data['promotions']['child_id'][$key],
									'margin' => $data['promotions']['promo_margin'][$key],
									'discount' => $data['promotions']['promo_disc'][$key],
									'retail_price' => $data['promotions']['promo_mrp'][$key],
									'loyalty' => $data['promotions']['promo_loyalty'][$key],
									'min_qty' => $data['promotions']['promo_min_units'][$key],
									'max_qty' => $data['promotions']['promo_max_units'][$key],
									);
				}

			}
		} else {
			return 2;
		}
		if(array_key_exists('standard',$update))
		{
			$this->db->where('account_no', $data['acc']);							
			$this->db->update_batch('pos_i6_promotion', $update['standard'], 'prom_index_id'); 
		}
		if(array_key_exists('variant',$update))
		{
			$this->db->where('account_no', $data['acc']);							
			$this->db->update_batch('pos_i6_promotion_variant', $update['variant'], 'prom_var_index_id'); 
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function delete_promotion($prom_id,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$this->db->where('promotion_index',$prom_id);
		$this->db->where('account_no',$acc);
		$this->db->delete('pos_i6_promgroup');
		
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
}