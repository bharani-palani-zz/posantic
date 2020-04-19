<?php
class Inventory_model extends CI_Model
{
    public function __construct() 
    {
        parent::__construct();
    }
	public function get_num_inv_details($product_id,$acc)
	{
		$sql = "select 
					a.loc_id,
					a.location,
					(
						ifnull(
						(select current_stock from pos_i2_a_inventory 
						where product_id = ?
						and location = a.loc_id)
						,0)
					) as stock
				from
					pos_b_locations as a
				JOIN `pos_a_master` as b ON b.account_no = a.account_no
				WHERE 
					a.account_no = ? and
					a.outlet_stat = 30
				group by loc_id
				order by location
				";
		$params = array($product_id,$acc);
		$query = $this->db->query($sql, $params); 
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['loc_id'][] = $row->loc_id;
				$array['location'][] = $row->location;
				$array['stock'][] = $row->stock;
			} 
			return $array;				
		} else {
			return array();	
		}
	}
	public function get_variant_inv_details($product_id,$acc)
	{
		$this->db->select('a.variant_index');
		$this->db->select('aaa.cust_var_value');
		$this->db->select('aa.attribute_val');
		$this->db->select('aa.attribute_id');
		$this->db->select('a.retail_price');
		$this->db->select('a.sku');
		$this->db->select('b.current_stock as stock',false);
		$this->db->select('c.location as location',false);
		$this->db->select('c.loc_id as loc_id');
		$this->db->from('pos_i1_products_1_variants as a');
		$this->db->join('pos_i1_products_1_variants_attributes as aa','aa.product_id = a.product_id and aa.variant_id = a.variant_index','left');
		$this->db->join('pos_i1_0_cust_variant_types as aaa','aaa.cust_var_id = aa.attribute_id','left');
		$this->db->join('pos_i2_a_inventory_variant as b','b.parent_product = a.product_id and b.variant_id = a.variant_index','left');
		$this->db->join('pos_b_locations as c','c.loc_id = b.location','left');
		$this->db->where(array('a.status != ' => 120,'a.account_no' => $acc,'a.product_id' => $product_id,'c.outlet_stat' => 30));
		$this->db->group_by(array('variant_index', 'attribute_id', 'loc_id'));
		$this->db->order_by('a.position asc');
		$this->db->order_by('cust_var_value asc');
		$this->db->order_by('location asc');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->variant_index]['attribute_name'][$row->attribute_id] = $row->cust_var_value;
				$array[$row->variant_index]['attribute_value'][$row->attribute_id] = $row->attribute_val;
				$array[$row->variant_index]['retail_price'] = $row->retail_price;
				$array[$row->variant_index]['sku'] = $row->sku;
				$array[$row->variant_index]['stock'][$row->loc_id] = $row->stock;
				$array[$row->variant_index]['outlets'][$row->loc_id] = $row->location;
			} 			
			return $array;				
		} else {
			return array();	
		}
	}
	public function get_single_variant_inv_details($variant_id,$acc)
	{
		$sql = "select 
					a.loc_id,
					a.location,
					(
						ifnull(
						(select current_stock from pos_i2_a_inventory_variant 
						where variant_id = ?
						and location = a.loc_id)
						,0)
					) as stock
				from
					pos_b_locations as a
				JOIN `pos_a_master` as b ON b.account_no = a.account_no
				WHERE 
					a.account_no = ? and
					a.outlet_stat = 30
				group by loc_id
				order by location
				";
		$params = array($variant_id,$acc);
		$query = $this->db->query($sql, $params); 
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['loc_id'][] = $row->loc_id;
				$array['location'][] = $row->location;
				$array['stock'][] = $row->stock;
			} 
			return $array;				
		} else {
			return array();	
		}
	}
	public function get_blend_inv_details($product_id,$acc)
	{
		$sql = 'select 
				h.product_id as indexed,
				(select product_name from pos_i1_products where product_id = h.product_id) as prod_name,
				case h.product_id
					when e.product_id then e.sku
					when f.product_id then f.sku
				end as sku,				
				h.current_stock,
				c.parent_qty as parent_qty,
				j1.loc_id,
				j1.location				
					from pos_i1_products as a
				left join pos_i1_products_0_blend as b on b.blend_product_id = a.product_id
				left join pos_i1_products_4_blend as c on c.blend_product = b.blend_product_id
				left join pos_i1_products_2_num as e on e.product_id = c.parent_product
				left join pos_i1_products_3_kilo as f on f.product_id = c.parent_product
				left join pos_i2_a_inventory as h on h.product_id = c.parent_product
				left join pos_b_locations as j1 on j1.loc_id = h.location
				WHERE
				a.product_id =  ? and
				a.account_no = ? and
				h.product_id is not null
				group by h.product_id, j1.loc_id
				
					union all
				
				select
				i.variant_id as indexed,
				(select 
				concat_ws(" / ",product_name,GROUP_CONCAT(distinct gg.attribute_val order by ggg.cust_var_value separator " / "))
				from pos_i1_products where product_id = d.product_id
				) as prod_name,
				g.sku as sku,
				i.current_stock,
				d.variant_qty as parent_qty,
				j2.loc_id,
				j2.location
				
					from pos_i1_products as a
				left join pos_i1_products_0_blend as b on b.blend_product_id = a.product_id
				left join pos_i1_products_4_blend_variant as d on d.blend_product = b.blend_product_id
				left join pos_i1_products_1_variants as g on g.variant_index = d.variant_id 
				left join pos_i1_products_1_variants_attributes as gg on gg.variant_id = d.variant_id
				left join pos_i1_0_cust_variant_types as ggg on ggg.cust_var_id = gg.attribute_id
				left join pos_i2_a_inventory_variant as i on i.variant_id = d.variant_id
				left join pos_b_locations as j2 on j2.loc_id = i.location
				WHERE
				a.product_id =  ? and
				a.account_no = ? and
				i.variant_id is not null
				group by i.variant_id, j2.loc_id
				order by location';
		$query = $this->db->query($sql, array($product_id, $acc, $product_id, $acc));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['calculate'][$row->indexed]['outlets'][$row->loc_id] = $row->location;
				$array['calculate'][$row->indexed]['stock'][$row->loc_id] = $row->current_stock;
				$array['calculate'][$row->indexed]['sku'] = $row->sku;
				$array['calculate'][$row->indexed]['prod_name'] = $row->prod_name;
				$array['calculate'][$row->indexed]['parent_qty'] = $row->parent_qty;

			}
			return $array;
		} else {
			return array();	
		}
	}
	public function add_stock_transfer($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		switch ($data['inventory_case']) {
			case 17: //stock transfer
				$supplier_id = NULL;
				$job_str = 17;
				$source_id = $data['source_outlet'];
				$dest_id = $data['dest_outlet'];
				break;
			case 18: // stock order
				$supplier_id = $data['supplier'];
				$job_str = 18;
				$source_id = NULL;
				$dest_id = $data['dest_outlet'];
				break;
			case 19: // stock return
				$supplier_id = $data['supplier'];
				$job_str = 19;
				$source_id = NULL;
				$dest_id = $data['dest_outlet'];
				break;
		}
		if(strlen($supplier_id) > 0) // if supplier is present bring only supplier associated products
		{
			$sql_supp_join = 'left join pos_i1_products_8_supplier as e on e.product_id = b.product_id';
			$sql_supp_and = 'and e.supplier_id = "'.$supplier_id.'"';
		} else {
			$sql_supp_join = '';
			$sql_supp_and = '';
		}
		if(count($data['import_data']) > 0)
		{
			$sku_where_std_clause = 'and (c.sku in ('.$data['import_data']['sku_str'].') or d.sku in ('.$data['import_data']['sku_str'].'))';
			$sku_where_var_clause = 'and (c.sku in ('.$data['import_data']['sku_str'].'))';
			$check_current_stock = '';
		} else {
			$sku_where_std_clause = '';
			$sku_where_var_clause = '';
			$check_current_stock = 'and `a`.`current_stock` <= a.reorder_stock';
		}

		$insert = array(
					'transfer_index' => $data['transfer_id'],
					'transfer_name' => $data['transfer_name'],
					'supplier_id' => $supplier_id,
					'source_outlet' => $source_id,
					'dest_outlet' => $dest_id,
					'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'recieved_at' => 0,
					'transfer_stat' => 5,
					'job_str' => $job_str,
					'account_no' => $data['acc']				
						);
		$this->db->insert('pos_j2_stock_transfer',$insert);						
		if($data['reorder_stat'] == 30 or count($data['import_data']) > 0)
		{
			$sql = "select * from (
						SELECT 
						`b`.`product_id` as product_id,
						CASE b.product_scale  
						when 2 then d.sku 
						when 1 then c.sku 
						END as sku,
						CASE b.product_scale  
						when 2 then d.price 
						when 1 then c.price 
						END as supplier_price,
						a.reorder_qty
						FROM `pos_i2_a_inventory` as a
						LEFT JOIN `pos_i1_products` as b ON `a`.`product_id` = `b`.`product_id` and a.account_no = b.account_no
						LEFT JOIN `pos_i1_products_2_num` as c ON `c`.`product_id` = `b`.`product_id` and c.account_no = a.account_no
						LEFT JOIN `pos_i1_products_3_kilo` as d ON `d`.`product_id` = `b`.`product_id` and d.account_no = a.account_no
						".$sql_supp_join."
						WHERE 
						(c.status = 30 or d.status = 30)
						AND `a`.`account_no` =  ?
						AND `a`.`location` =  ?
						".$sql_supp_and."
						".$sku_where_std_clause."
						".$check_current_stock."

						union
						
						SELECT 
						c.variant_index as product_id,
						c.sku,
						c.price as supplier_price,
						a.reorder_qty
						FROM `pos_i2_a_inventory_variant` as a
						LEFT JOIN `pos_i1_products` as b ON `a`.`parent_product` = `b`.`product_id` and a.account_no = b.account_no
						LEFT JOIN `pos_i1_products_1_variants` as c ON `c`.`product_id` = `b`.`product_id` and c.variant_index = a.variant_id and c.account_no = a.account_no
						".$sql_supp_join."
						WHERE 
						c.status = 30
						AND `a`.`account_no` =  ?
						AND `a`.`location` =  ?
						".$sql_supp_and."	
						".$sku_where_var_clause."				
						".$check_current_stock."
					
					) as stock_tbl limit 500";
					
			$query = $this->db->query($sql, array($data['acc'], $data['dest_outlet'],$data['acc'], $data['dest_outlet']));				
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$scale = $this->product_model->check_scale($row->product_id,$data['acc']);
					$supplier_price = isset($data['import_data']['supplier_price'][$row->sku]) ? $data['import_data']['supplier_price'][$row->sku] : $row->supplier_price;
					$supplier_price = is_numeric($supplier_price) ? $supplier_price : $row->supplier_price;
					$ordered = isset($data['import_data']['quantity'][$row->sku]) ? $data['import_data']['quantity'][$row->sku] : $row->reorder_qty;
					$ordered = is_numeric($ordered) ? $ordered : $row->reorder_qty;
					if($scale == 3)
					{
						$insert['VAR'][] = array(
									  'prd_var_tf_id' => $this->taxes_model->make_single_uuid(),
									  'transfer_id' => $data['transfer_id'],
									  'variant_id' => $row->product_id,
									  'supplier_price' => $supplier_price,
									  'ordered' => $ordered,
									  'recieved' => 0,
									  'account_no' => $data['acc']				
										);
					} else if($scale == 1 or $scale == 2){
						$insert['STD'][] = array(
									  'prd_tf_id' => $this->taxes_model->make_single_uuid(),
									  'transfer_id' => $data['transfer_id'],
									  'product_id' => $row->product_id,
									  'supplier_price' => $supplier_price,
									  'ordered' => $ordered,
									  'recieved' => 0,
									  'account_no' => $data['acc']				
										);
					}
				}
				if(array_key_exists('STD',$insert))
				{
					$this->db->insert_batch('pos_j2_stock_transfer_products',$insert['STD']);
				}
				if(array_key_exists('VAR',$insert))
				{
					$this->db->insert_batch('pos_j2_stock_transfer_variants',$insert['VAR']);
				}
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return false;
		} else {
			return true;	
		}
	}
	public function transfer_supplier_details($transfer_id,$acc)
	{
		$this->db->select('ifnull(b.auth_pers,c.location) as auth_pers',false);
		$this->db->select('ifnull(b.email,c.guest_email) as email',false);
		$this->db->from('pos_j2_stock_transfer as a');
		$this->db->join('pos_e_suppliers as b','b.supp_id = a.supplier_id','left');
		$this->db->join('pos_b_locations as c','c.loc_id = a.dest_outlet','left');
		$this->db->where(array('a.transfer_index' => $transfer_id,'a.account_no' => $acc));
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
	public function transfer_main_details($transfer_id,$acc)
	{
		$this->db->select('a.transfer_index');
		$this->db->select('a.transfer_name');
		$this->db->select('a.created_at');
		$this->db->select('a.recieved_at');
		$this->db->select('a.transfer_stat');
		$this->db->select('a.source_outlet as source_outlet_id');
		$this->db->select('a.dest_outlet as dest_outlet_id');
		$this->db->select('c.log_name');
		$this->db->select('e.cmp_name as supplier_name');
		$this->db->select('f.log_name as towards');
		$this->db->select('a.job_str as towards_id');
		$this->db->select('(select location from pos_b_locations where loc_id = a.dest_outlet) as dest_outlet',false);
		$this->db->select('(select location from pos_b_locations where loc_id = a.source_outlet) as source_outlet',false);
		$this->db->from('pos_j2_stock_transfer as a');
		$this->db->join('pos_1f_log_codes as c','c.log_index = a.transfer_stat','left');
		$this->db->join('pos_b_locations as d','d.loc_id = a.source_outlet or d.loc_id = a.dest_outlet and d.account_no = a.account_no','left');
		$this->db->join('pos_e_suppliers as e','e.supp_id = a.supplier_id','left');
		$this->db->join('pos_1f_log_codes as f','f.log_index = a.job_str','left');
		$this->db->where(array('a.transfer_index' => $transfer_id,'a.account_no' => $acc));
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
	public function transfer_GetAutocomplete($options = array(),$outlet_id,$acc)
    {					
		$this->db->select('if(a.product_scale = 3,d.variant_index,a.product_id) as product_id',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.price
						   when 2 then c.price
						   when 1 then b.price
						END as supplier_price		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 3 then d.sku
						   when 2 then c.sku
						   when 1 then b.sku
						END as sku		
						',false);
		
		$this->db->select('if(a.product_scale = 3,
							ifnull((select current_stock from pos_i2_a_inventory_variant where variant_id = d.variant_index and location = "'.$outlet_id.'"),0),
							ifnull((select current_stock from pos_i2_a_inventory where product_id = a.product_id and location = "'.$outlet_id.'"),0)
						) as source_stock',false);
		$this->db->select('if(a.product_scale = 3,
							ifnull((select reorder_qty from pos_i2_a_inventory_variant where variant_id = d.variant_index and location = "'.$outlet_id.'"),0),
							ifnull((select reorder_qty from pos_i2_a_inventory where product_id = a.product_id and location = "'.$outlet_id.'"),0)
						) as reorder_qty',false);
		
		$this->db->select('if(a.product_scale = 3,concat_ws(" / ",a.product_name,GROUP_CONCAT(distinct f.attribute_val order by g.cust_var_value separator " / ")),if(a.product_scale = 1,a.product_name,if(a.product_scale = 2,a.product_name,null))) as prod_name',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		
		$this->db->join('pos_i1_products_1_variants_attributes as f','a.account_no = f.account_no and f.variant_id = d.variant_index','left');
		$this->db->join('pos_i1_0_cust_variant_types as g','g.cust_var_id = f.attribute_id','left');		
		
		$this->db->join('pos_i2_a_inventory as i','i.product_id = a.product_id and a.account_no = i.account_no','left');
		$this->db->join('pos_i2_a_inventory_variant as ii','ii.parent_product = a.product_id and ii.variant_id = d.variant_index and a.account_no = ii.account_no','left');
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.product_scale != ',4);
		$this->db->where("
						(a.product_name  LIKE '%".$options['keyword']."%'
						OR  a.handle  LIKE '%".$options['keyword']."%'
						OR  b.sku  LIKE '%".$options['keyword']."%'
						OR  c.sku  LIKE '%".$options['keyword']."%'
						OR  d.sku  LIKE '%".$options['keyword']."%')",
						NULL, false);
		$this->db->where("(b.status != 120 or c.status != 120 or d.status != 120)");
		$this->db->group_by('product_id');
		$this->db->limit(10);
        $query = $this->db->get();
		return $query->result();
    }
	public function insert_ajax_transfer($data)
	{
		if(isset($data['params']))
		{
			$this->db->select('count(distinct b.product_id) + count(distinct c.variant_id) as counted',false);
			$this->db->from('pos_j2_stock_transfer as a');
			$this->db->join('pos_j2_stock_transfer_products as b','b.transfer_id = a.transfer_index and a.account_no = b.account_no','left');
			$this->db->join('pos_j2_stock_transfer_variants as c','c.transfer_id = a.transfer_index and a.account_no = c.account_no','left');
			$this->db->where('a.transfer_index',$data['params'][2]);
			$this->db->where('a.account_no',$data['acc']);
			$query = $this->db->get();
			$row = $query->row_array();
			if($row['counted'] < 500)
			{
				$this->db->select('*'); //check product exists already for transfer
				$this->db->from('pos_j2_stock_transfer as a');
				$this->db->join('pos_j2_stock_transfer_products as b','b.transfer_id = a.transfer_index and a.account_no = b.account_no','left');
				$this->db->join('pos_j2_stock_transfer_variants as c','c.transfer_id = a.transfer_index and a.account_no = c.account_no','left');
				$this->db->where('a.transfer_index',$data['params'][2]);
				$this->db->where('a.account_no',$data['acc']);
				$this->db->where('(b.product_id = "'.$data['params'][0].'" or c.variant_id = "'.$data['params'][0].'")',NULL,false);
				$query = $this->db->get();
				if($query->num_rows() < 1)
				{
					$scale = $this->product_model->check_scale($data['params'][0],$data['acc']);
					if($scale == 3)
					{
						$trans_id = $this->taxes_model->make_single_uuid();
						$insert['VAR'] = array(
									  'prd_var_tf_id' => $trans_id,
									  'transfer_id' => $data['params'][1],
									  'variant_id' => $data['params'][0],
									  'supplier_price' => $data['params'][2],
									  'ordered' => $data['params'][3],
									  'recieved' => 0,
									  'account_no' => $data['acc']				
										);
						if($this->db->insert('pos_j2_stock_transfer_variants',$insert['VAR']))
						{
							return $trans_id;	
						} else {
							return 0;	
						}
					} else {
						$trans_id = $this->taxes_model->make_single_uuid();
						$insert['STD'] = array(
									  'prd_tf_id' => $trans_id,
									  'transfer_id' => $data['params'][1],
									  'product_id' => $data['params'][0],
									  'supplier_price' => $data['params'][2],
									  'ordered' => $data['params'][3],
									  'recieved' => 0,
									  'account_no' => $data['acc']				
										);
						if($this->db->insert('pos_j2_stock_transfer_products',$insert['STD']))
						{
							return $trans_id;	
						} else {
							return 0;	
						}
					}
					
				} else {
					return 2;	
				}
			} else {
				return 3;	
			}
		} else {
			die('Illegal input');		
		}
	}
	public function transfer_subproducts_sql($limit,$start,$transfer_id,$outlet_id,$acc)
	{
		if(is_numeric($limit))
		{
			$start = is_numeric($start) ? $start : 0;
			$limit_str = 'limit '.$start.','.$limit;
		} else {
			$limit_str = 'limit 1000';	
		}
		$sql = "select * from (
				SELECT 
				`a`.`prd_tf_id` as child_id,`a`.`product_id`,a.supplier_price as supplier_price,a.ordered as ordered,a.recieved as recieved,
				ifnull((select current_stock from pos_i2_a_inventory where product_id = d.product_id and location = '".$outlet_id."'),0) as source_stock,
				d.product_name as prod_name,
				if(d.product_scale = 1,e.retail_price,if(d.product_scale = 2,f.retail_price,null)) as retail_price,CASE d.product_scale 
				when 2 then f.sku 
				when 1 then e.sku 
				END as sku
				FROM `pos_j2_stock_transfer_products` as a
				LEFT JOIN `pos_j2_stock_transfer` as b ON `b`.`transfer_index` = `a`.`transfer_id` and a.account_no = b.account_no
				LEFT JOIN `pos_i1_products` as d ON `a`.`account_no` = `d`.`account_no` and d.product_id = a.product_id
				LEFT JOIN `pos_i1_products_2_num` as e ON `a`.`product_id` = `e`.`product_id` and a.account_no = e.account_no
				LEFT JOIN `pos_i1_products_3_kilo` as f ON `a`.`product_id` = `f`.`product_id` and a.account_no = f.account_no
				LEFT JOIN `pos_i2_a_inventory` as i ON `i`.`product_id` = `a`.`product_id` and a.account_no = i.account_no
				WHERE `b`.`transfer_index` =  ?
				AND `a`.`account_no` =  ?
				
				union
				
				SELECT 
				`a`.`prd_var_tf_id` as child_id,
				`a`.`variant_id` as product_id,
				a.supplier_price as supplier_price,
				a.ordered as ordered,a.recieved as recieved,
				ifnull((select current_stock from pos_i2_a_inventory_variant where variant_id = e.variant_index and location = '".$outlet_id."'),0) as source_stock,
				concat_ws(' / ',d.product_name,GROUP_CONCAT(distinct f.attribute_val order by g.cust_var_value separator ' / ')) as prod_name,
				e.retail_price as retail_price,
				e.sku as sku 
				FROM `pos_j2_stock_transfer_variants` as a
				LEFT JOIN `pos_j2_stock_transfer` as b ON `b`.`transfer_index` = `a`.`transfer_id` and a.account_no = b.account_no 
				LEFT JOIN pos_i1_products_1_variants as e on e.variant_index = a.variant_id and e.account_no = a.account_no
				LEFT JOIN `pos_i1_products` as d ON `a`.`account_no` = `d`.`account_no` and d.product_id = e.product_id
				LEFT JOIN `pos_i1_products_1_variants_attributes` as f ON `a`.`account_no` = `f`.`account_no` and f.variant_id = a.variant_id
				LEFT JOIN `pos_i1_0_cust_variant_types` as g ON `g`.`cust_var_id` = `f`.`attribute_id`
				LEFT JOIN `pos_i2_a_inventory_variant` as i ON i.variant_id = e.variant_index and a.account_no = i.account_no 
				WHERE `b`.`transfer_index` =  ?
				AND `a`.`account_no` =  ?
				GROUP BY e.variant_index 
				ORDER BY `prod_name` asc
				
				) as subprd_tbl ".$limit_str." ";
		$query = $this->db->query($sql, array($transfer_id, $acc, $transfer_id, $acc));				
		return $query;
	}
	public function get_transfer_subproducts_count($limit,$start,$transfer_id,$outlet_id,$acc)
	{
		$query = $this->transfer_subproducts_sql($limit,$start,$transfer_id,$outlet_id,$acc);
		return $query->num_rows();
	}
	public function get_transfer_subproducts_data($limit,$start,$transfer_id,$outlet_id,$acc)
	{
		$query = $this->transfer_subproducts_sql($limit,$start,$transfer_id,$outlet_id,$acc);
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
	public function delete_transfer_single_product($child_id,$acc)
	{
		$query = $this->db->get_where('pos_j2_stock_transfer_products',array('prd_tf_id' => $child_id, 'account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$this->db->where('prd_tf_id',$child_id);	
			$this->db->where('account_no',$acc);	
			if($this->db->delete('pos_j2_stock_transfer_products'))
			{
				return 1;	
			} else {
				return 0;
			}	
		}
		$query = $this->db->get_where('pos_j2_stock_transfer_variants',array('prd_var_tf_id' => $child_id, 'account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$this->db->where('prd_var_tf_id',$child_id);	
			$this->db->where('account_no',$acc);	
			if($this->db->delete('pos_j2_stock_transfer_variants'))
			{
				return 1;	
			} else {
				return 0;
			}	
		}
	}
	public function update_stock_transfer($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$this->db->where(array('transfer_index' => $data['transfer_id'],'account_no' => $data['acc']));
		$this->db->update('pos_j2_stock_transfer',array('transfer_name' => $data['transfer_name']));
		if(isset($data['transfer']['child_id']))
		{
			foreach($data['transfer']['child_id'] as $key => $value)
			{
				$query = $this->db->get_where('pos_j2_stock_transfer_products',array('prd_tf_id' => $value, 'account_no' => $data['acc']));
				if($query->num_rows() > 0)
				{
					$update['STD'][] = array(
									'prd_tf_id' => $data['transfer']['child_id'][$key],
									'supplier_price' => $data['transfer']['supp_price'][$key],
									'ordered' => $data['transfer']['ordered'][$key],
									);
				}
				$query = $this->db->get_where('pos_j2_stock_transfer_variants',array('prd_var_tf_id' => $value, 'account_no' => $data['acc']));
				if($query->num_rows() > 0)
				{
					$update['VAR'][] = array(
									'prd_var_tf_id' => $data['transfer']['child_id'][$key],
									'supplier_price' => $data['transfer']['supp_price'][$key],
									'ordered' => $data['transfer']['ordered'][$key],
									);
				}
			}
			if(array_key_exists('STD',$update))
			{
				$this->db->update_batch('pos_j2_stock_transfer_products', $update['STD'], 'prd_tf_id'); 
			}
			if(array_key_exists('VAR',$update))
			{
				$this->db->update_batch('pos_j2_stock_transfer_variants', $update['VAR'], 'prd_var_tf_id'); 
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return false;
		} else {
			return true;	
		}
			
	}
	public function all_activity_sql($limit,$start,$where_array,$acc)
	{
		$this->db->select('a.transfer_index');
		$this->db->select('a.transfer_name');
		$this->db->select('a.transfer_stat');
		$this->db->select('a.created_at');
		$this->db->select('f.log_name as towards');
		$this->db->select('c.log_name as status');
		$this->db->select('a.transfer_stat as stat_id');
		$this->db->select('(
								(select coalesce(sum(ordered),0) from pos_j2_stock_transfer_products where transfer_id = a.transfer_index)
								+
								(select coalesce(sum(ordered),0) from pos_j2_stock_transfer_variants where transfer_id = a.transfer_index)
							) as ordered',false);
		$this->db->select('(select location from pos_b_locations where loc_id = a.dest_outlet) as dest_outlet_str',false);
		$this->db->select('(select location from pos_b_locations where loc_id = a.source_outlet) as source_outlet_str',false);
		$this->db->from('pos_j2_stock_transfer as a');
		$this->db->join('pos_j2_stock_transfer_products as b','a.transfer_index = b.transfer_id and a.account_no = b.account_no','left');
		$this->db->join('pos_j2_stock_transfer_variants as bb','a.transfer_index = bb.transfer_id and a.account_no = bb.account_no','left');
		$this->db->join('pos_1f_log_codes as c','c.log_index = a.transfer_stat','left');
		$this->db->join('pos_b_locations as d','d.loc_id = a.source_outlet and d.loc_id = a.dest_outlet and d.account_no = a.account_no','left');
		$this->db->join('pos_e_suppliers as e','e.supp_id = a.supplier_id and e.account_no = a.account_no','left');
		$this->db->join('pos_1f_log_codes as f','f.log_index = a.job_str','left');

		$this->db->where('a.account_no' , $acc);
		if(isset($where_array['log_stat']))
		{
			$this->db->where('c.log_index',$where_array['log_stat']);
		}
		if(isset($where_array['transfer_stat']))
		{
			if(is_numeric($where_array['transfer_stat']))
			{
				$this->db->where('a.transfer_stat',$where_array['transfer_stat']);
				$this->db->or_where('a.job_str', $where_array['transfer_stat']); 
			}
		}
		if(isset($where_array['transfer_name']))
		{
			if(strlen($where_array['transfer_name']) > 0)
			{
				$this->db->like('a.transfer_name',$where_array['transfer_name']);
			}
		}
		if(isset($where_array['from_date']))
		{
			if(strlen($where_array['from_date']) > 0)
			{
				$this->db->where('a.created_at >=',$where_array['from_date']);
			}
		}
		if(isset($where_array['to_date']))
		{
			if(strlen($where_array['to_date']) > 0)
			{
				$this->db->where('a.created_at <=',$where_array['to_date']);
			}
		}
		if(isset($where_array['source_outlet']))
		{
			if(strlen($where_array['source_outlet']) > 0)
			{
				$this->db->where('a.source_outlet',$where_array['source_outlet']);
			}
		}
		if(isset($where_array['dest_outlet']))
		{
			if(strlen($where_array['dest_outlet']) > 0)
			{
				$this->db->where('a.dest_outlet',$where_array['dest_outlet']);
			}
		}
		if(isset($where_array['supplier']))
		{
			if(strlen($where_array['supplier']) > 0)
			{
				$this->db->where('a.supplier_id',$where_array['supplier']);
			}
		}
		if(isset($where_array['sort']))
		{
			if(strlen($where_array['sort']) > 0)
			{
				$flow = $where_array['flow'] ? $where_array['flow'] : "desc";
				$this->db->order_by($where_array['sort'], $flow); 
			}
		}
		$this->db->group_by('a.transfer_index');
		if($limit > 0)
		{
			$this->db->limit($limit, $start);
		} else {
			$this->db->limit(1000,0);			
		}
		$query = $this->db->get();
		return $query;
	}
	public function all_activity_tot_rows($limit,$start,$where_array,$acc)
	{
		$query = $this->all_activity_sql($limit,$start,$where_array,$acc);
		return $query->num_rows();
	}
	public function all_activity_page_limit($limit,$start,$where_array,$acc)
	{
		$query = $this->all_activity_sql($limit,$start,$where_array,$acc);
		if($query->num_rows() > 0)
		{
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field][] = $row[$field];
				}
			} 
		} else {
			$array = array('' => '');
		}	
		return $array;
	}
	public function cancel_transfer($transfer_id,$acc)
	{
		$this->db->where(array('account_no' => $acc,'transfer_index' => $transfer_id));
		if($this->db->update('pos_j2_stock_transfer',array('transfer_stat' => 7)))
		{
			return 1;
		} else {
			return 0;	
		}
	}
	public function send_transfer($transfer_id,$acc)
	{
		$this->db->where(array('account_no' => $acc,'transfer_index' => $transfer_id));
		if($this->db->update('pos_j2_stock_transfer',array('transfer_stat' => 8)))
		{
			return 1;
		} else {
			return 0;	
		}
	}
	public function do_transfer($transfer_id,$data,$session,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();

		$sql = "SELECT 
				b.prd_tf_id as row_id,
				b.product_id as product_id,
				b.product_id as master_product,
				b.ordered
				FROM `pos_j2_stock_transfer` as a
				left join pos_j2_stock_transfer_products as b on b.transfer_id = a.transfer_index and a.account_no = b.account_no
				where a.transfer_index = ? and a.account_no = ?
				and product_id is not null
				
				union
		
				SELECT 
				b.prd_var_tf_id as row_id,
				b.variant_id as product_id,
				c.product_id as master_product,				
				b.ordered
				FROM pos_j2_stock_transfer as a
				left join pos_j2_stock_transfer_variants as b on b.transfer_id = a.transfer_index and a.account_no = b.account_no
				left join pos_i1_products_1_variants as c on c.variant_index = b.variant_id and c.account_no = a.account_no
				where a.transfer_index = ? and a.account_no = ?
				and product_id is not null
				";
		$query = $this->db->query($sql,array($transfer_id,$acc,$transfer_id,$acc));
		$insert = array();
		$update = array();
		$log = array();
		foreach($query->result() as $row)
		{
			switch ($data['details']['towards_id']) {
				case 17:  //stock transfer 
					//reduce source outlet inventory
					$scale = $this->product_model->check_scale($row->product_id,$acc);
					if($scale == 1 or $scale == 2) // check if STD/KILO product
					{
						$check = $this->db->get_where('pos_i2_a_inventory',array('location' => $data['details']['source_outlet_id'], 'product_id' => $row->product_id, 'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							$this->db->where(array('location' => $data['details']['source_outlet_id'], 'product_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock-'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory');
						} else {
							$insert['STD_1'][] = array(
										'inv_indx' => $this->taxes_model->make_single_uuid(),
										'product_id' => $row->product_id,
										'current_stock' => -$row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['source_outlet_id'],
										'account_no' => $acc,
										);
						}
						$log['STD_1'][] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $session['user_id'],	
										'master_product' => $row->product_id,
										'log_code' => 8,	
										'feed' => -$row->ordered,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $data['details']['source_outlet_id'],	
										'account_no' => $acc
										);										
																					
					} else if($scale == 3) {
						$check = $this->db->get_where('pos_i2_a_inventory_variant',array('location' => $data['details']['source_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							$this->db->where(array('location' => $data['details']['source_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock-'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory_variant');
						} else {
							$insert['VAR_1'][] = array(
										'inv_var_indx' => $this->taxes_model->make_single_uuid(),
										'parent_product' => $row->master_product,
										'variant_id' => $row->product_id,
										'current_stock' => -$row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['source_outlet_id'],
										'account_no' => $acc
										);
						}
						$log['VAR_1'][] = array(
										'log_var_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $session['user_id'],	
										'master_product' => $row->master_product,
										'variant_id' => $row->product_id,
										'log_code' => 8,	
										'feed' => -$row->ordered,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $data['details']['source_outlet_id'],	
										'account_no' => $acc
										);										
					}
					//gain destination outlet inventory
					$scale = $this->product_model->check_scale($row->product_id,$acc);
					if($scale == 1 or $scale == 2) // check if STD//KILO product
					{
						$check = $this->db->get_where('pos_i2_a_inventory',array('location' => $data['details']['dest_outlet_id'], 'product_id' => $row->product_id, 'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							$this->db->where(array('location' => $data['details']['dest_outlet_id'], 'product_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock+'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory');
						} else {
							$insert['STD_2'][] = array(
										'inv_indx' => $this->taxes_model->make_single_uuid(),
										'product_id' => $row->product_id,
										'current_stock' => $row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['dest_outlet_id'],
										'account_no' => $acc,
										);
						}
						$log['STD_2'][] = array(
											'log_index' => $this->taxes_model->make_single_uuid(),
											'user_id' => $session['user_id'],	
											'master_product' => $row->master_product,																																																			
											'log_code' => 6,	
											'feed' => $row->ordered,	
											'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
											'location' => $data['details']['dest_outlet_id'],	
											'account_no' => $acc
											);														
					} else if($scale == 3) {
						$check = $this->db->get_where('pos_i2_a_inventory_variant',array('location' => $data['details']['source_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							$this->db->where(array('location' => $data['details']['dest_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock+'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory_variant');
						} else {
							$insert['VAR_2'][] = array(
										'inv_var_indx' => $this->taxes_model->make_single_uuid(),
										'parent_product' => $row->master_product,
										'variant_id' => $row->product_id,
										'current_stock' => $row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['dest_outlet_id'],
										'account_no' => $acc
										);
						}
						$log['VAR_2'][] = array(
										'log_var_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $session['user_id'],	
										'master_product' => $row->master_product,
										'variant_id' => $row->product_id,
										'log_code' => 6,	
										'feed' => $row->ordered,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $data['details']['dest_outlet_id'],	
										'account_no' => $acc
										);										
					}
				break;	
				case 18: //stock supplier order
					$scale = $this->product_model->check_scale($row->product_id,$acc);
					if($scale == 1 or $scale == 2) // check if STD/KILO product
					{
						$check = $this->db->get_where('pos_i2_a_inventory',array('product_id' => $row->product_id, 'location' => $data['details']['dest_outlet_id'],'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							//update std dest
							$this->db->where(array('location' => $data['details']['dest_outlet_id'], 'product_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock+'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory');
						} else {
							$insert['STD_1'][] = array(
										'inv_indx' => $this->taxes_model->make_single_uuid(),
										'product_id' => $row->product_id,
										'current_stock' => $row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['dest_outlet_id'],
										'account_no' => $acc,
										);
						}
						$log['STD_1'][] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $session['user_id'],	
										'master_product' => $row->master_product,																																																			
										'log_code' => 9,	
										'feed' => $row->ordered,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $data['details']['dest_outlet_id'],	
										'account_no' => $acc
										);														
					} else if($scale == 3) {
						$check = $this->db->get_where('pos_i2_a_inventory_variant',array('location' => $data['details']['dest_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							//update var dest
							$this->db->where(array('location' => $data['details']['dest_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock+'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory_variant');
						} else {
							$insert['VAR_1'][] = array(
										'inv_var_indx' => $this->taxes_model->make_single_uuid(),
										'parent_product' => $row->master_product,
										'variant_id' => $row->product_id,
										'current_stock' => $row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['dest_outlet_id'],
										'account_no' => $acc
										);
						}
						$log['VAR_1'][] = array(
									'log_var_index' => $this->taxes_model->make_single_uuid(),
									'user_id' => $session['user_id'],	
									'master_product' => $row->master_product,
									'variant_id' => $row->product_id,
									'log_code' => 9,	
									'feed' => $row->ordered,	
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
									'location' => $data['details']['dest_outlet_id'],	
									'account_no' => $acc
									);										
						
					}
				break;	
				case 19: //stock return reduce destination outlet inventory
					$scale = $this->product_model->check_scale($row->product_id,$acc);
					if($scale == 1 or $scale == 2) // check if STD/KILO product
					{
						$check = $this->db->get_where('pos_i2_a_inventory',array('product_id' => $row->product_id, 'location' => $data['details']['dest_outlet_id'],'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							$this->db->where(array('location' => $data['details']['dest_outlet_id'], 'product_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock-'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory');
						} else {
							$insert['STD_1'][] = array(
										'inv_indx' => $this->taxes_model->make_single_uuid(),
										'product_id' => $row->product_id,
										'current_stock' => -$row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['dest_outlet_id'],
										'account_no' => $acc,
										);
						}
						$log['STD_1'][] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $session['user_id'],	
										'master_product' => $row->master_product,																																																			
										'log_code' => 20,	
										'feed' => -$row->ordered,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $data['details']['dest_outlet_id'],	
										'account_no' => $acc
										);														
					} else if($scale == 3) {
						$check = $this->db->get_where('pos_i2_a_inventory_variant',array('location' => $data['details']['dest_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
						if($check->num_rows() > 0)
						{
							$this->db->where(array('location' => $data['details']['dest_outlet_id'], 'variant_id' => $row->product_id, 'account_no' => $acc));
							$this->db->set('current_stock', 'current_stock-'.$row->ordered, FALSE);
							$this->db->set('updated_at',mdate('%Y-%m-%d %H:%i:%s', now()));
							$this->db->update('pos_i2_a_inventory_variant');
						} else {
							$insert['VAR_1'][] = array(
										'inv_var_indx' => $this->taxes_model->make_single_uuid(),
										'parent_product' => $row->master_product,
										'variant_id' => $row->product_id,
										'current_stock' => -$row->ordered,
										'reorder_stock' => NULL,
										'reorder_qty' => NULL,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'location' => $data['details']['dest_outlet_id'],
										'account_no' => $acc
										);
						}
						$log['VAR_1'][] = array(
									'log_var_index' => $this->taxes_model->make_single_uuid(),
									'user_id' => $session['user_id'],	
									'master_product' => $row->master_product,
									'variant_id' => $row->product_id,
									'log_code' => 20,	
									'feed' => -$row->ordered,	
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
									'location' => $data['details']['dest_outlet_id'],	
									'account_no' => $acc
									);										
					}
				break;	
			}		
			//update transfer stock table
			$query = $this->db->get_where('pos_j2_stock_transfer_products',array('prd_tf_id' => $row->row_id, 'account_no' => $acc));
			if($query->num_rows() > 0)
			{								
				$update['ST_STD'][] = array(
										'prd_tf_id' => $row->row_id,
										'recieved' => $row->ordered
										);
				
			}
			$query = $this->db->get_where('pos_j2_stock_transfer_variants',array('prd_var_tf_id' => $row->row_id, 'account_no' => $acc));
			if($query->num_rows() > 0)
			{				
				$update['ST_VAR'][] = array(
										'prd_var_tf_id' => $row->row_id,
										'recieved' => $row->ordered
										);
			}				
		}
		// insert and update table arrays wrt index
		//update inventory table
		if(array_key_exists('ST_STD',$update))
		{
			$this->db->update_batch('pos_j2_stock_transfer_products', $update['ST_STD'], 'prd_tf_id'); 
		}
		if(array_key_exists('ST_VAR',$update))
		{
			$this->db->update_batch('pos_j2_stock_transfer_variants', $update['ST_VAR'], 'prd_var_tf_id'); 
		}
		// standards
		if(array_key_exists('STD_1',$insert))
		{
			$this->db->insert_batch('pos_i2_a_inventory',$insert['STD_1']);
		}
		if(array_key_exists('STD_2',$insert))
		{
			$this->db->insert_batch('pos_i2_a_inventory',$insert['STD_2']);
		}
		if(array_key_exists('STD_1',$log))
		{
			$this->db->insert_batch('pos_i1_products_log',$log['STD_1']);
		}
		if(array_key_exists('STD_2',$log))
		{
			$this->db->insert_batch('pos_i1_products_log',$log['STD_2']);
		}
		// variants
		if(array_key_exists('VAR_1',$insert))
		{
			$this->db->insert_batch('pos_i2_a_inventory_variant',$insert['VAR_1']);
		}
		if(array_key_exists('VAR_2',$insert))
		{
			$this->db->insert_batch('pos_i2_a_inventory_variant',$insert['VAR_2']);
		}
		if(array_key_exists('VAR_1',$log))
		{
			$this->db->insert_batch('pos_i1_products_log_variants',$log['VAR_1']);
		}
		if(array_key_exists('VAR_2',$log))
		{
			$this->db->insert_batch('pos_i1_products_log_variants',$log['VAR_2']);
		}

		//update transfer fields
		$this->db->where('transfer_index',$transfer_id);
		$this->db->where('account_no',$acc);
		$this->db->update('pos_j2_stock_transfer',array('recieved_at' => mdate('%Y-%m-%d %H:%i:%s', now()), 'transfer_stat' => 21));

		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function download_transfers($transfer_id,$acc)
	{
		$sql = "select * from (
				SELECT `a`.`prd_tf_id` as id,
				d.product_name as `product name`,
				d.handle,
				CASE d.product_scale 
				when 2 then f.sku 
				when 1 then e.sku 
				END as sku,
				i.current_stock,
				a.ordered as ordered,
				a.recieved as recieved,
				a.supplier_price as `supplier price`,
				a.ordered * a.supplier_price as `total supplier price`,
				if(d.product_scale = 1,e.retail_price,if(d.product_scale = 2,f.retail_price,null)) as `retail price`,
				a.ordered * if(d.product_scale = 1,e.retail_price,if(d.product_scale = 2,f.retail_price,null)) as `total retail price`
				FROM `pos_j2_stock_transfer_products` as a
				LEFT JOIN `pos_j2_stock_transfer` as b ON `b`.`transfer_index` = `a`.`transfer_id` and a.account_no = b.account_no
				LEFT JOIN `pos_i1_products` as d ON `a`.`account_no` = `d`.`account_no` and d.product_id = a.product_id
				LEFT JOIN `pos_i1_products_2_num` as e ON `a`.`product_id` = `e`.`product_id` and a.account_no = e.account_no
				LEFT JOIN `pos_i1_products_3_kilo` as f ON `a`.`product_id` = `f`.`product_id` and a.account_no = f.account_no
				LEFT JOIN `pos_i2_a_inventory` as i ON `i`.`product_id` = `a`.`product_id` and i.location = b.dest_outlet and a.account_no = i.account_no
				WHERE `b`.`transfer_index` =  ?
				AND `a`.`account_no` =  ?
				group by id				
				
				union
				
				SELECT 
				`a`.`prd_var_tf_id` as id,
				concat_ws(' / ',d.product_name,GROUP_CONCAT(distinct f.attribute_val order by g.cust_var_value separator ' / ')) as `product name`,
				d.handle,
				e.sku as sku,
				i.current_stock,
				a.ordered as ordered,
				a.recieved as recieved,
				a.supplier_price as `supplier price`,
				a.ordered * a.supplier_price as `total supplier price`,
				e.retail_price as `retail price`,
				a.ordered * e.retail_price as `total retail price`
				FROM `pos_j2_stock_transfer_variants` as a
				LEFT JOIN `pos_j2_stock_transfer` as b ON `b`.`transfer_index` = `a`.`transfer_id` and a.account_no = b.account_no 
				LEFT JOIN pos_i1_products_1_variants as e on e.variant_index = a.variant_id and e.account_no = a.account_no
				LEFT JOIN `pos_i1_products` as d ON d.product_id = e.product_id and d.account_no = a.account_no
				LEFT JOIN `pos_i1_products_1_variants_attributes` as f ON `a`.`account_no` = `f`.`account_no` and f.variant_id = a.variant_id
				LEFT JOIN `pos_i1_0_cust_variant_types` as g ON `g`.`cust_var_id` = `f`.`attribute_id`
				LEFT JOIN `pos_i2_a_inventory_variant` as i ON i.variant_id = e.variant_index and i.location = b.dest_outlet and a.account_no = i.account_no 
				WHERE `b`.`transfer_index` =  ?
				AND `a`.`account_no` =  ?
				group by id				
				
				) as export_tbl ORDER BY `product name` asc";
		$query = $this->db->query($sql, array($transfer_id, $acc, $transfer_id, $acc));				
		return $query;
	}
	public function add_stock_take($data)
	{
		$insert = array(
					'stocktake_index' => $data['take_id'],
					'stocktake_name' => $data['take_name'],
					'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'take_stat' => 50,
					'location' => $data['outlet'],
					'account_no' => $data['acc']
						);
		if($this->db->insert('pos_j3_stock_take',$insert))
		{
			return true;	
		} else {
			return false;
		}
	}
	public function stock_take_all_details($acc)
	{
		$this->db->select('stocktake_index');
		$this->db->select('stocktake_name');
		$this->db->select('take_stat');
		$this->db->select('loc_id');
		$this->db->select('b.location');
		$this->db->select('status_code');
		$this->db->select('created_at');
		$this->db->from('pos_j3_stock_take as a');
		$this->db->join('pos_b_locations as b','b.loc_id = a.location');
		$this->db->join('pos_1e_status_codes as c','c.status_id = a.take_stat');
		$this->db->where('a.account_no',$acc);
		$this->db->order_by('a.created_at','desc');
		$this->db->limit(1000);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$all_details = array();
			foreach($query->result() as $row)
			{
				$all_details[$row->take_stat]['id'][] = $row->stocktake_index;
				$all_details[$row->take_stat]['name'][] = $row->stocktake_name;
				$all_details[$row->take_stat]['loc_id'][] = $row->loc_id;
				$all_details[$row->take_stat]['location'][] = $row->location;
				$all_details[$row->take_stat]['status_code'][] = $row->status_code;
				$all_details[$row->take_stat]['created_at'][] = $row->created_at;
			}
			return $all_details;
		} else {
			return array();	
		}
	}
	public function stock_take_main_details($take_id,$acc)
	{
		$this->db->select('*');
		$this->db->from('pos_j3_stock_take as a');
		$this->db->join('pos_b_locations as b','a.location = b.loc_id');
		$this->db->join('pos_1e_status_codes as c','c.status_id = a.take_stat');
		$this->db->where('a.stocktake_index',$take_id);
		$this->db->where('a.account_no',$acc);
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
	public function get_stocktake_countables($take_id,$acc)
	{
		$this->db->select('if(
					a.product_id is not null,
					c.product_name,
					if(a.variant_id is not null,
						concat_ws(" / ",e.product_name,GROUP_CONCAT(distinct f.attribute_val order by dd.cust_var_value separator " / ")),
						null)
				) as product_name',false);
		$this->db->select('if(
					  a.product_id is not null,
					  a.product_id,
					  if(a.variant_id is not null,a.variant_id,null)
				  ) as id',false);				
		$this->db->select('if(
					  a.product_id is not null,
					  "false",
					  if(a.variant_id is not null,"true",null)
				  ) as is_variant_product',false);				
		$this->db->select('e.product_id as master_product',false);				
		$this->db->select('a.expected');				
		$this->db->select('a.counted');				
		$this->db->select('a.cost_gain');				
		$this->db->select('a.cost_loss');				
		$this->db->select('a.count_gain');				
		$this->db->select('a.count_loss');				
		$this->db->from('pos_j3_stock_take_products as a');
		$this->db->join('pos_j3_stock_take as b','b.stocktake_index = a.stock_take_id','left');
		$this->db->join('pos_i1_products as c','a.product_id = c.product_id','left');
		$this->db->join('pos_i1_products_1_variants as d','d.variant_index = a.variant_id','left');
		$this->db->join('pos_i1_products as e','e.product_id = d.product_id or e.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as f','d.variant_index = f.variant_id','left');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = f.attribute_id','left');
		$this->db->where('a.stock_take_id',$take_id);
		$this->db->where('a.account_no',$acc);
		$this->db->group_by('id');
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
			return NULL;
		}	
		
	}
	public function delete_stock_take($take_id,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$update = array(
					'take_stat' => 120
					);
		$this->db->where('stocktake_index',$take_id);			
		$this->db->where('account_no',$acc);			
		$this->db->update('pos_j3_stock_take',$update);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return false;
		} else {
			return true;	
		}
	}
	public function complete_stock_take($take_id,$selected_product,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		
		$this->db->select('location');
		$query = $this->db->get_where('pos_j3_stock_take',array('account_no' => $acc, 'stocktake_index' => $take_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$outlet = $row['location'];
			$countables = $this->get_stocktake_countables($take_id,$acc);
			foreach($countables['id'] as $key => $value)
			{
				if(in_array($countables['id'][$key],$selected_product))
				{
					if($countables['is_variant_product'][$key] == "true")
					{
						$query = $this->db->get_where('pos_i2_a_inventory_variant',array('variant_id' => $countables['id'][$key],'location' => $outlet,'account_no' => $acc));
						if($query->num_rows() > 0)
						{
							$this->db->where('variant_id',$countables['id'][$key]);
							$this->db->where('location',$outlet);
							$this->db->where('account_no',$acc);
							$this->db->update('pos_i2_a_inventory_variant',array('current_stock' => $countables['counted'][$key],'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now())));
						} else {
							$insert = array(
										'inv_var_indx' => $this->taxes_model->make_single_uuid(),
										'parent_product' => $countables['master_product'][$key],
										'variant_id' => $countables['id'][$key],
										'current_stock' => $countables['counted'][$key],
										'reorder_stock' => 0,
										'reorder_qty' => 0,
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $outlet,
										'account_no' => $acc
										);	
							$this->db->insert('pos_i2_a_inventory_variant',$insert);									
						}
						
						// update log
						$insert_log['variant'][] = array(
									'log_var_index' => $this->taxes_model->make_single_uuid(),
									'user_id' => $this->session->userdata('user_id'),	
									'master_product' => $countables['master_product'][$key],	
									'variant_id' => $countables['id'][$key],
									'log_code' => 16,	
									'feed' => $countables['counted'][$key],	
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
									'location' => $outlet,	
									'account_no' => $acc,								
									);
	
					} else {
						$query = $this->db->get_where('pos_i2_a_inventory',array('product_id' => $countables['id'][$key],'location' => $outlet,'account_no' => $acc));
						if($query->num_rows() > 0)
						{					
							$this->db->where('product_id',$countables['id'][$key]);
							$this->db->where('location',$outlet);
							$this->db->where('account_no',$acc);
							$this->db->update('pos_i2_a_inventory',array('current_stock' => $countables['counted'][$key],'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now())));
						} else {
							$insert = array(
									'inv_indx' => $this->taxes_model->make_single_uuid(),
									'product_id' => $countables['id'][$key],	
									'current_stock' => $countables['counted'][$key],	
									'reorder_stock' => 0,
									'reorder_qty' => 0,
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
									'location' => $outlet,	
									'account_no' => $acc
										);	
							$this->db->insert('pos_i2_a_inventory',$insert);									
						}
						//update log
						$insert_log['main'][] = array(
									'log_index' => $this->taxes_model->make_single_uuid(),
									'user_id' => $this->session->userdata('user_id'),	
									'master_product' => $countables['id'][$key],	
									'log_code' => 16,	
									'feed' => $countables['counted'][$key],
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
									'location' => $outlet,	
									'account_no' => $acc,								
									);
									
					}
				}
			}
			if(array_key_exists('variant',$insert_log))
			{
				$this->db->insert_batch('pos_i1_products_log_variants', $insert_log['variant']); 
			}
			if(array_key_exists('main',$insert_log))
			{
				$this->db->insert_batch('pos_i1_products_log', $insert_log['main']); 
			}
			$this->db->where('stocktake_index',$take_id);
			$this->db->where('account_no',$acc);
			$this->db->update('pos_j3_stock_take',array('take_stat' => 60));
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return false;
		} else {
			return true;	
		}
	}
	public function download_stocktake($take_id,$acc)
	{
		$this->db->select('if(
					  a.product_id is not null,
					  a.product_id,
					  if(a.variant_id is not null,a.variant_id,null)
				  ) as id',false);				
		$this->db->select('if(
					a.product_id is not null,
					c.product_name,
					if(a.variant_id is not null,
						concat_ws(" / ",e.product_name,GROUP_CONCAT(distinct f.attribute_val order by dd.cust_var_value separator " / ")),
						null)
				) as product_name',false);
		$this->db->select('e.handle');			
		$this->db->select('ss.cmp_name as supplier',false);	
		$this->db->select('if(
					  a.product_id is not null,
					  if(e.product_scale = 1,d2.sku,if(e.product_scale = 2,d3.sku,null)),
					  if(a.variant_id is not null,d.sku,null)
				  ) as sku',false);				
		$this->db->select('a.expected');				
		$this->db->select('a.counted');				
		$this->db->select('a.cost_gain');				
		$this->db->select('a.cost_loss');				
		$this->db->select('a.count_gain');				
		$this->db->select('a.count_loss');				
		$this->db->from('pos_j3_stock_take_products as a');
		$this->db->join('pos_j3_stock_take as b','b.stocktake_index = a.stock_take_id','left');
		$this->db->join('pos_i1_products as c','a.product_id = c.product_id','left');
		$this->db->join('pos_i1_products_1_variants as d','d.variant_index = a.variant_id','left');
		$this->db->join('pos_i1_products_2_num as d2','d2.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_3_kilo as d3','d3.product_id = a.product_id','left');
		$this->db->join('pos_i1_products as e','e.product_id = d.product_id or e.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_8_supplier as s','s.product_id = e.product_id','left');
		$this->db->join('pos_e_suppliers as ss','ss.supp_id = s.supplier_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as f','d.variant_index = f.variant_id','left');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = f.attribute_id','left');
		$this->db->where('a.stock_take_id',$take_id);
		$this->db->where('a.account_no',$acc);
		$this->db->group_by('id');
		$query = $this->db->get();
		return $query;
	}
}
?>