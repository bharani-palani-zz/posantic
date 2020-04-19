<?php
class Product_model extends CI_Model
{
	public $max_variants = 3;
	public $account_stk_handle = null;
	public $acc;
    public function __construct() 
    {
        parent::__construct();
		$this->load->library('upload');
		$this->load->library('image_lib');
		$this->acc = $this->session->userdata('acc_no');		
		$bonafide = $this->master_model->plan_bonafide($this->acc);
		$this->account_stk_handle = $bonafide['stock_limit'];
    }
	public function M_get_product_scale_assoc()
	{
		$rows = $this->db->query("SELECT * FROM pos_1d_product_scale"); 
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->scale_code] = 'By '.$row->scale_name.'('.$row->scale_units.')';				
			}
		}
		return $array;
	}
	public function M_product_scale_drop()
	{
		$rows = $this->db->get('pos_1d_product_scale'); 
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->scale_code] = 'Billed by '.$row->scale_units;				
			}
		}
		return $array;
	}
	public function get_barcode_prefix($acc)
	{
		$rows = $this->db->get_where('pos_i1_kilo_product_prefix',array('account_no' => $acc));
		if($rows->num_rows() > 0)
		{
			$array = $rows->row_array();	
			return $array['prefix_val'];
		} else {
			return NULL;	
		}
	}
	public function prd_category_combo($acc)
	{
		$rows = $this->db->get_where('pos_i1_product_category',array('account_no' => $acc));
		if($rows->num_rows() > 0)
		{
			$array = array(NULL => '');
			foreach($rows->result() as $row)
			{
				$array[$row->cat_id] = $row->cat_name;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function insert_category($cat_name,$acc)
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
	public function change_barcode_prefix($prefix,$acc)
	{
		$search = $this->db->escape_like_str($prefix);
		$this->db->select('count(b.product_id) + count(c.product_id) + count(d.product_id) + count(e.blend_product_id) as count',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where('a.account_no',$acc);
		$this->db->or_like('b.sku',$search,'after');
		$this->db->or_like('c.sku',$search,'after');
		$this->db->or_like('d.sku',$search,'after');
		$this->db->or_like('e.sku',$search,'after');
		$query = $this->db->get();
		$row = $query->row_array();
		if($row['count'] < 1)
		{
			$data = array(
						   'prefix_val' => $prefix
						);
			$this->db->where(array('account_no' => $acc));
			if($this->db->update('pos_i1_kilo_product_prefix', $data))
			{
				return 1;
			} else {
				return 0;	
			}
		} else {
			return 2;	
		}
	}
	public function check_barcode_prefix($sku,$acc)
	{
		$this->db->select('prefix_val');
		$rows = $this->db->get_where('pos_i1_kilo_product_prefix',array('account_no' => $acc));
		if($rows->num_rows() > 0)
		{
			$array = $rows->row_array();	
			if($array['prefix_val'] != substr($sku,0,2))
			{
				return true;	
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function get_single_product_details($product_id,$acc)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_i1_products',array('account_no' => $acc,'product_id' => $product_id));
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
	
	public function make_pos_id($acc)
	{
		$this->db->select('pos_id',false);
		$this->db->where('status !=',120);
		$rows = $this->db->get_where('pos_i1_products_3_kilo',array('account_no' => $acc));
		if($rows->num_rows() > 0){
			foreach($rows->result() as $row){
				$array1[] = $row->pos_id;				
			}
		} else {
			$array1 = array(0);	
		}
		$array2 = range(1,max($array1)+1);
		$bal = min(array_diff($array2,$array1));
		return $bal;
	}
	public function check_sku_not_this_product($sku,$product_id,$acc)
	{
		$this->db->select('sum(
        if(a.product_scale = 1 and b.status != 120 and b.product_id !=  "'.$product_id.'" and b.sku = "'.$sku.'",1,
            if(a.product_scale = 2 and c.status != 120 and c.product_id !=  "'.$product_id.'" and c.sku = "'.$sku.'",1,
                if(a.product_scale = 3 and d.status != 120 and d.variant_index !=  "'.$product_id.'" and d.sku = "'.$sku.'",1,
                    if(a.product_scale = 4 and e.status != 120 and e.blend_product_id !=  "'.$product_id.'" and e.sku = "'.$sku.'",1,0
		))))) as summed',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		$row = $query->row_array();
		return $row['summed'] > 0 ? false : true; 
		
	}
	public function generate_incremented_sku($acc)
	{
		$this->db->select('concat_ws(",",max(CONVERT(b.sku, SIGNED INTEGER)),max(CONVERT(c.sku, SIGNED INTEGER)),max(CONVERT(d.sku, SIGNED INTEGER)),max(CONVERT(e.sku, SIGNED INTEGER))) as csv',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$csv = $row['csv'];
			if(!empty($csv))
			{
				$csv_array = explode(",",$csv);
				$csv_max = max($csv_array);
				return is_numeric($csv_max) ? ++$csv_max : max($csv_array)."[1]";
			} else {
				return 10000;	
			}
		} else {
			return 10000;	
		}
	}
	public function check_sku_exists($sku,$acc)
	{
		$this->db->select('sum(
        if(a.product_scale = 1 and b.status != 120 and b.sku = "'.$sku.'",1,
            if(a.product_scale = 2 and c.status != 120 and c.sku = "'.$sku.'",1,
                if(a.product_scale = 3 and d.status != 120 and d.sku = "'.$sku.'",1,
                    if(a.product_scale = 4 and e.status != 120 and e.sku = "'.$sku.'",1,0
		))))) as summed',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		$row = $query->row_array();
		return $row['summed'] > 0 ? false : true; 
	}
	public function product_count($acc)
	{
		//$this->db->select('count(b.product_id) + count(c.product_id) + count(d.product_id) + count(e.blend_product_id) as grand_tot',false);
		$this->db->select('sum(if(b.status != 120,1,0)) + sum(if(c.status != 120,1,0)) + sum(if(d.status != 120,1,0)) + sum(if(e.status != 120,1,0)) as grand_tot',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where('a.account_no',$acc);
		$rows = $this->db->get();
		if($rows->num_rows() > 0)
		{
			$row = $rows->row_array();
			return $row['grand_tot'] > 0 ? array('grand_total' => $row['grand_tot']) : array('grand_total' => 0);
		} else {
			return array('');	
		}
	}
	public function product_status_count($acc)
	{
		$this->db->select('sum(if(b.status = 30,1,0)) + sum(if(c.status = 30,1,0)) + sum(if(d.status = 30,1,0)) + sum(if(e.status = 30,1,0)) as visible',false);
		$this->db->select('sum(if(b.status = 40,1,0)) + sum(if(c.status = 40,1,0)) + sum(if(d.status = 40,1,0)) + sum(if(e.status = 40,1,0)) as hidden',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where('a.account_no',$acc);
		$rows = $this->db->get();
		if($rows->num_rows() > 0)
		{
			$row = $rows->row_array();
			return array($row['visible'],$row['hidden']);
		} else {
			return array('');	
		}
	}
	
	public function unlink_file($id)
	{
		$avatar_dir = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/products';
		$file_pattern = './'.$avatar_dir.'/'.$id.'_thumb.*';			
		array_map( "unlink", glob( $file_pattern ) );
	}
	public function do_upload($id)
	{
		//image upload
		$avatar_dir = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/products';
		if(!is_dir($avatar_dir)){mkdir($avatar_dir,0777,true);}		
		$path = $avatar_dir;
		$config['upload_path'] = './'.$path.'/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size']	= '3072';
		$config['max_width']  = '3264';
		$config['max_height']  = '2448';
		$this->upload->initialize($config);
		if(!$this->upload->do_upload())
		{
			return false;
		} else {			
			$file_pattern = './'.$path.'/'.$id.'_thumb.*';			
			array_map( "unlink", glob( $file_pattern ) );

			$data = array('upload_data' => $this->upload->data());
			$file = $data['upload_data']['file_name'];
			$file_ext = $data['upload_data']['file_ext'];
			$filename = $id;			
			$old_name = './'.$path.'/'.$file;
			$new_name = './'.$path.'/'.$filename.$file_ext;			

			rename($old_name,$new_name);

			$config['image_library'] = 'gd2';
			$config['source_image'] = $path.'/'.$filename.$file_ext;
			$config['new_image'] = $path.'/'.$filename.$file_ext;
			$config['create_thumb'] = TRUE;
			$config['maintain_ratio'] = FALSE;
			$config['width'] = 150;
			$config['height'] = 150;			
			$this->load->library('image_lib', $config);
			$this->image_lib->initialize($config);				
			if($this->image_lib->resize())
			{
				unlink($new_name);
				return true;
			}		
		}
	}
	public function kilo_prd_count_ls_99999($acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_3_kilo',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] < 99999 ? true : false;
		} else {
			return false;	
		}
				
	}
	public function check_child_variant_exist($variant_value_array,$product_id,$acc)
	{
		$mystring = implode(",",$variant_value_array);
		$this->db->select('group_concat(attribute_val) as stringed',false);
		$this->db->from('pos_i1_products_1_variants_attributes as a');
		$this->db->join('pos_i1_0_cust_variant_types as b','b.cust_var_id = a.attribute_id');
		$this->db->where(array('a.product_id' => $product_id,'a.account_no' => $acc));
		$this->db->group_by('a.variant_id');
		$this->db->order_by('b.cust_var_value');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$i=0;
			foreach ($query->result() as $row) {
				if($row->stringed === $mystring)
				{
					$i++;
				}
			}
			return $i > 0 ? false : true;
		} else {
			return false;	
		}
		
	}
	public function blend_GetAutocomplete($options = array(),$acc)
    {					
		$this->db->select('if(a.product_scale = 3,d.variant_index,a.product_id) as indexed',false);
		$this->db->select('if(a.product_scale = 3,
							concat_ws(" / ",a.product_name,GROUP_CONCAT(distinct f.attribute_val order by dd.cust_var_value separator " / ")),
								if(a.product_scale = 2,a.product_name,if(a.product_scale = 1,a.product_name,null))) as prod_name',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');		
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->join('pos_i1_products_1_variants_attributes as f','d.variant_index = f.variant_id','left');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = f.attribute_id','left');
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.product_scale != ',4);
		$or_where = '(a.product_name like "%'.$options['keyword'].'%" or b.sku like "%'.$options['keyword'].'%" or c.sku like "%'.$options['keyword'].'%" or d.sku like "%'.$options['keyword'].'%")';		
		$this->db->where($or_where,NULL,FALSE);		
		$this->db->group_by('indexed');
		$this->db->limit(10);
        $query = $this->db->get();
		return $query->result();
    }
	public function get_min_blend_qty($product_id_array,$prd_qty_array,$acc,$loc) //active record not working here coz of from clause bracket issue
	{
		$prd_comma = implode('","',$product_id_array);
		$sql = 'SELECT current_stock as stock,product_id as product 
				FROM pos_i2_a_inventory
				WHERE product_id IN 
				("'.$prd_comma.'") 
				AND account_no = "'.$acc.'" 
				AND location = "'.$loc.'" 
				GROUP BY product
				
				UNION 
				
				SELECT 
					current_stock as stock, variant_id as product 
				FROM pos_i2_a_inventory_variant
				WHERE variant_id IN 
				("'.$prd_comma.'") 
				AND `account_no` = "'.$acc.'" 
				AND `location` = "'.$loc.'"' ;
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$stock[$row->product] = $row->stock;
				foreach($product_id_array as $key => $prd_val)
				{
					if($row->product == $prd_val)
					{
						$inv[] = $row->stock / $prd_qty_array[$key];
					}
				}
			}
			return floor(min($inv));
		} else {
			return 0;	
		}
	}
	public function all_products_sql($search,$limit, $start, $acc, $get_array=array())
	{
		$start = is_numeric($start) ? $start : 0;
		$limit = is_numeric($limit) ? $limit : 0;
		$this->db->select('a.product_id');
		$this->db->select('
						CASE a.product_scale
						   when 4 then a.product_id
						   when 3 then d.variant_index
						   when 2 then a.product_id
						   when 1 then a.product_id
						END as update_id		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 4 then e.updated_at
						   when 3 then (select max(updated_at) from pos_i1_products_1_variants where product_id = a.product_id)
						   when 2 then c.updated_at
						   when 1 then b.updated_at
						END as updated_at		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 4 then e.created_at
						   when 3 then (select max(created_at) from pos_i1_products_1_variants where product_id = a.product_id)
						   when 2 then c.created_at
						   when 1 then b.created_at
						END as created_at		
						',false);
		$this->db->select('j.scale_code as product_scale');
		$this->db->select('a.product_name');
		$this->db->select('
						CASE a.product_scale
						   when 4 then ""
						   when 3 then ""
						   when 2 then c.pos_id
						   when 1 then ""
						END as pos_id		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 4 then e.sku
						   when 3 then d.sku
						   when 2 then c.sku
						   when 1 then b.sku
						END as sku		
						',false);
		$this->db->select('h.cmp_name as supplier',false);
		$this->db->select('
						CASE a.product_scale
						   when 4 then e.retail_price
						   when 3 then min(d.retail_price)
						   when 2 then c.retail_price
						   when 1 then b.retail_price
						END as price		
						',false);
		$this->db->select('
						CASE a.product_scale
						   when 4 then e.status
						   when 3 then d.status
						   when 2 then c.status
						   when 1 then b.status
						END as status		
						',false);
		if(strlen($get_array['product_stat']) > 0)
		{
			$stat = $get_array['product_stat'] == "VISIBLE" ? 30 : 40;		
		} else {
			$stat = 30;	
		}
		$this->db->select('if(a.product_scale = 3,(select count(*) from pos_i1_products_1_variants where product_id = a.product_id and status = '.$stat.'),0) as variants',false);
		$this->db->select(
						'CASE a.product_scale
						   when 4 then 
						   (
								select sum(minned) from
								(
									select product_id, min(divi) as minned from
									(
											select
												aa.product_id,
												floor(min(cc.current_stock / bb.parent_qty)) as divi,
												dd.location
											from pos_i1_products as aa
											left join pos_i1_products_4_blend as bb on bb.blend_product = aa.product_id
											left join pos_i2_a_inventory as cc on cc.product_id = bb.parent_product
											left join pos_b_locations as dd on dd.loc_id = cc.location
											where
												dd.outlet_stat = 30 and
												bb.blend_index is not null
											group by location, product_id
									
											union
									
											select
												aaa.product_id,
												floor(min(ccc.current_stock / bbb.variant_qty)) as divi,
												ddd.location
											from pos_i1_products as aaa
											left join pos_i1_products_4_blend_variant as bbb on bbb.blend_product = aaa.product_id
											left join pos_i2_a_inventory_variant as ccc on ccc.variant_id = bbb.variant_id
											left join pos_b_locations as ddd on ddd.loc_id = ccc.location
											where
												ddd.outlet_stat = 30 and
												bbb.blend_var_index is not null
											group by location, product_id
									
									) as sub1
									group by product_id,location
								) as sub2 
								where product_id = a.product_id
								group by product_id
							)
						   when 3 then ifnull(sum(i2.current_stock),0) 
						   when 2 then ifnull(sum(i.current_stock),0)
						   when 1 then ifnull(sum(i.current_stock),0)
						END as stock',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		
		$this->db->join('pos_i2_a_inventory as i','i.product_id = a.product_id and a.account_no = i.account_no','left');
		$this->db->join('pos_i2_a_inventory_variant as i2','i2.variant_id = d.variant_index  and i2.parent_product = a.product_id','left');

		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');		
		
		$this->db->join('pos_1d_product_scale as j','j.scale_id = a.product_scale','left');
		$this->db->join('pos_i1_products_8_supplier as k','k.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as h','h.supp_id = k.supplier_id','left');	
		$this->db->where('a.account_no',$acc);

		if(!empty($search))
		{			
			$this->db->where("
							(a.product_name  LIKE '%".$search."%'
							OR  a.handle  LIKE '%".$search."%'
							OR  b.sku  LIKE '%".$search."%'
							OR  c.sku  LIKE '%".$search."%'
							OR  d.sku  LIKE '%".$search."%'
							OR  e.sku  LIKE '%".$search."%')",
							NULL, false);
		}
		if(count($get_array) > 0)
		{
			if(strlen($get_array['product_stat']) > 0) // if status is visible or hidden
			{
				$bool = $get_array['product_stat'] == "VISIBLE" ? 30 : 40;
				$this->db->where("(b.status = ".$bool." or c.status = ".$bool." or d.status = ".$bool." or e.status = ".$bool.")"); 
			} else {
				$this->db->where("(b.status = 30 or c.status = 30 or d.status = 30 or e.status = 30)");
			}
			if(isset($get_array['supplier_id']))
			{
				if(strlen($get_array['supplier_id']) > 0)
				{
					$this->db->where('h.supp_id', $get_array['supplier_id']); 
				}
			}
			if(isset($get_array['product_brand']))
			{
				if(strlen($get_array['product_brand']) > 0)
				{
					$this->db->join('pos_i1_products_6_brand as jjj','jjj.product_id = a.product_id','left');
					$this->db->join('pos_i1_product_brand as jj','jj.brand_index = jjj.brand_id','left');
					$this->db->where('jj.brand_index', $get_array['product_brand']); 
				}
			}
			if(isset($get_array['product_cat']))
			{
				if(strlen($get_array['product_cat']) > 0)
				{
					$this->db->join('pos_i1_products_7_category as kkk','kkk.product_id = a.product_id','left');
					$this->db->join('pos_i1_product_category as kk','kk.cat_id = kkk.category_id','left');
					$this->db->where('kk.cat_id', $get_array['product_cat']); 
				}
			}
			if(isset($get_array['tag_id']))
			{
				if($get_array['tag_id'])
				{				
					$this->db->join('pos_i1_products_5_tags as zzz','zzz.product_id = a.product_id','left');
					$this->db->join('pos_i1_product_tag as zz','zz.tag_id = zzz.tagged_id','left');
					$this->db->where_in('zzz.tagged_id', $get_array['tag_id']); 
				}
			}
		}
		$this->db->group_by('product_id');		
		if($get_array['sort'])
		{
			$flow = $get_array['flow'] ? $get_array['flow'] : "desc";
			$this->db->order_by($get_array['sort'], $flow); 
		}
		if($limit > 0)
		{
			$this->db->limit($limit, $start);
		} else {
			$this->db->limit(1000,0);			
		}
		
		$query = $this->db->get();						
		return $query;
	}
	public function all_products_tot_rows($search,$limit, $start, $acc, $get)
	{
		$query = $this->all_products_sql($search,$limit, $start, $acc,$get);
		return $query->num_rows();
	}
	public function all_products_page_limit($search,$limit, $start, $acc,$get)
	{
		$rows = $this->all_products_sql($search,$limit, $start, $acc, $get);
		if($rows->num_rows() > 0)
		{
			$array = array();
			foreach($rows->result() as $row)
			{
				$array[$row->product_id]['updated_at'] = $row->updated_at;
				$array[$row->product_id]['created_at'] = $row->created_at;
				$array[$row->product_id]['product_id'] = $row->product_id;
				$array[$row->product_id]['update_id'] = $row->update_id;
				$array[$row->product_id]['product_scale'] = $row->product_scale;
				$array[$row->product_id]['product_name'] = $row->product_name;
				$array[$row->product_id]['sku'] = $row->sku;
				$array[$row->product_id]['pos_id'] = $row->pos_id;
				$array[$row->product_id]['supplier'] = $row->supplier;
				$array[$row->product_id]['price'] = $row->price;
				$array[$row->product_id]['stock'] = $row->stock;
				$array[$row->product_id]['status'] = $row->status;
				$array[$row->product_id]['variants'] = $row->variants;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function check_parent_product($product_id,$acc)
	{
		$this->db->select('count(*) as prd_count');
		$rows = $this->db->get_where('pos_i1_products',array('product_id' => $product_id,'account_no' => $acc));
		if($rows->num_rows() > 0)
		{
			$row = $rows->row_array(); 
			if($row['prd_count'] > 0)
			{
				return true;
			} else {
				return false;	
			}
		} else {
			return false;	
		}
	}
	public function check_product($product_id,$acc)
	{
		$this->db->select('if(a.product_scale = 1,count(b.product_id),if(a.product_scale = 2,count(c.product_id),if(a.product_scale = 3,count(d.variant_index),if(a.product_scale = 4,count(e.blend_product_id),0)))) as prd_count',false);	
		$this->db->select('if(a.product_scale = 1,"NUM",if(a.product_scale = 2,"KILO",if(a.product_scale = 3,"VARIANTS",if(a.product_scale = 4,"BLEND","")))) as scale',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->join('pos_i1_products_4_blend as f','a.product_id = f.blend_product and a.account_no = e.account_no','left');		
		$this->db->or_where('a.product_id',$product_id);
		$this->db->or_where('b.product_id',$product_id);
		$this->db->or_where('c.product_id',$product_id);
		$this->db->or_where('d.variant_index',$product_id);
		$this->db->or_where('e.blend_product_id',$product_id);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['prd_count'] > 0 ? array('type' => $row['scale']) : NULL;
		} else {
			return NULL;	
		}
	}
	public function check_scale($product_id,$acc)
	{
		$this->db->select('a.product_scale as scale');
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->or_where('a.product_id',$product_id);
		$this->db->or_where('b.product_id',$product_id);
		$this->db->or_where('c.product_id',$product_id);
		$this->db->or_where('d.variant_index',$product_id);
		$this->db->or_where('e.blend_product_id',$product_id);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['scale'];
		} else {
			return NULL;	
		}

	}
	public function check_valid_scale($product_or_variant_id,$acc)
	{
		$this->db->select('if(b.product_id = "'.$product_or_variant_id.'",1,
						  if(c.product_id = "'.$product_or_variant_id.'",2,
						  if(d.product_id = "'.$product_or_variant_id.'",3,
						  if(d.variant_index = "'.$product_or_variant_id.'",3.5,
						  if(e.blend_product_id = "'.$product_or_variant_id.'",4,0)
					   )
					   )
					   )
					   ) as scale',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->or_where('a.product_id',$product_or_variant_id);
		$this->db->or_where('b.product_id',$product_or_variant_id);
		$this->db->or_where('c.product_id',$product_or_variant_id);
		$this->db->or_where('d.variant_index',$product_or_variant_id);
		$this->db->or_where('e.blend_product_id',$product_or_variant_id);
		$this->db->where('a.account_no',$acc);
		$this->db->group_by('scale');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['scale'];
		} else {
			return NULL;	
		}

	}
	public function product_taxes($product_id,$acc)
	{
		$this->db->select('tax_id');
		$this->db->select('location');
		$query = $this->db->get_where('pos_i1_products_tax',array('account_no' => $acc, 'main_product' => $product_id));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->location] = $row->tax_id;
			}
			return $array;
		} else {
			return array('' => '');
		}
	}

	public function variant_taxes($variant_id,$acc)
	{
		$this->db->select('tax_id');
		$this->db->select('location');
		$query = $this->db->get_where('pos_i1_products_tax_variant',array('account_no' => $acc, 'variant_id' => $variant_id));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->location] = $row->tax_id;
			}
			return $array;
		} else {
			return array('' => '');
		}
	}
	public function product_inventory($product_id,$acc)
	{
		$this->db->select('concat_ws(",",ifnull(current_stock,0),ifnull(reorder_stock,0),ifnull(reorder_qty,0)) as stock',false);
		$this->db->select('location');
		$query = $this->db->get_where('pos_i2_a_inventory',array('account_no' => $acc, 'product_id' => $product_id));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->location] = $row->stock;
			}
			return $array;
		} else {
			return array();
		}
	}
	public function variant_inventory($variant_id,$acc)
	{
		$this->db->select('concat_ws(",",current_stock,reorder_stock,reorder_qty) as stock',false);
		$this->db->select('location');
		$query = $this->db->get_where('pos_i2_a_inventory_variant',array('account_no' => $acc, 'variant_id' => $variant_id));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->location] = $row->stock;
			}
			return $array;
		} else {
			return array();
		}
	}
	public function get_num_product_details($product_id,$acc)
	{
		$this->db->select('a.product_id as main_product_id');
		$this->db->select('a.product_scale');
		$this->db->select('a.product_name');
		$this->db->select('a.handle');
		$this->db->select('b.status');
		$this->db->select('a.description');
		$this->db->select('c.supplier_id');
		$this->db->select('dd.category_id');
		$this->db->select('d.cat_name');
		$this->db->select('cc.cmp_name');
		$this->db->select('e.brand_name');
		$this->db->select('ee.brand_id');
		$this->db->select('b.product_weight');
		$this->db->select('b.wearhouse_id as prd_wh_id');
		$this->db->select('b.purchase_id as prd_pur_id');
		$this->db->select('b.sku');
		$this->db->select('b.price');
		$this->db->select('b.margin');
		$this->db->select('b.retail_price');
		$this->db->select('a.account_no');
		$this->db->select('b.loyalty');
		$this->db->select('b.track_inventory');
		$this->db->select('b.ship_stat');
		$this->db->select('b.is_shopping_cart');

		$this->db->select('group_concat(distinct concat(l.tag_name,"/",l.tag_id) SEPARATOR ";" ) as product_tag',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id');
		
		$this->db->join('pos_i1_products_8_supplier as c','c.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as cc','c.supplier_id = cc.supp_id','left');
		
		$this->db->join('pos_i1_products_7_category as dd','dd.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as d','d.cat_id = dd.category_id','left');
		
		$this->db->join('pos_i1_products_6_brand as ee','ee.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as e','e.brand_index = ee.brand_id','left');
		
		$this->db->join('pos_i1_products_5_tags as k','k.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_tag as l','l.tag_id = k.tagged_id','left');
		
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.product_id',$product_id);
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
			return null;	
		}
	}
	public function get_kilo_product_details($product_id,$acc)
	{
		$this->db->select('a.product_id as main_product_id');
		$this->db->select('a.product_scale');
		$this->db->select('a.product_name');
		$this->db->select('a.handle');
		$this->db->select('b.status');
		$this->db->select('b.pos_id');
		$this->db->select('a.description');
		$this->db->select('c.supplier_id');
		$this->db->select('cc.cmp_name');
		$this->db->select('dd.category_id');
		$this->db->select('d.cat_name');
		$this->db->select('ee.brand_id');
		$this->db->select('e.brand_name');
		$this->db->select('b.product_weight');
		$this->db->select('b.wearhouse_id as prd_wh_id');
		$this->db->select('b.purchase_id as prd_pur_id');
		$this->db->select('b.sku');
		$this->db->select('b.price');
		$this->db->select('b.margin');
		$this->db->select('b.retail_price');
		$this->db->select('a.account_no');
		$this->db->select('b.loyalty');
		$this->db->select('b.track_inventory');
		$this->db->select('b.ship_stat');
		$this->db->select('b.is_shopping_cart');
		
		$this->db->select('group_concat(distinct concat(l.tag_name,"/",l.tag_id) SEPARATOR ";" ) as product_tag',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_3_kilo as b','a.product_id = b.product_id');

		$this->db->join('pos_i1_products_8_supplier as c','c.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as cc','c.supplier_id = cc.supp_id','left');
		
		$this->db->join('pos_i1_products_7_category as dd','dd.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as d','d.cat_id = dd.category_id','left');
		
		$this->db->join('pos_i1_products_6_brand as ee','ee.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as e','e.brand_index = ee.brand_id','left');

		$this->db->join('pos_i1_products_5_tags as k','k.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_tag as l','l.tag_id = k.tagged_id','left');
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.product_id',$product_id);
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
			return null;	
		}
	}
	public function get_main_variant_product_details($product_id,$acc)
	{
		$this->db->select('a.product_id as main_product_id');
		$this->db->select('a.product_scale');
		$this->db->select('a.product_name');
		$this->db->select('a.handle');
		$this->db->select('b.status');
		$this->db->select('a.description');
		$this->db->select('c.supplier_id');
		$this->db->select('cc.cmp_name');
		$this->db->select('dd.category_id');
		$this->db->select('d.cat_name');
		$this->db->select('ee.brand_id');
		$this->db->select('e.brand_name');
		$this->db->select('b.product_weight');
		$this->db->select('b.wearhouse_id as prd_wh_id');
		$this->db->select('b.purchase_id as prd_pur_id');
		$this->db->select('b.sku');
		$this->db->select('b.price');
		$this->db->select('b.margin');
		$this->db->select('b.retail_price');
		$this->db->select('a.account_no');
		$this->db->select('b.loyalty');
		$this->db->select('b.track_inventory');
		$this->db->select('b.ship_stat');
		$this->db->select('b.is_shopping_cart');

		$this->db->select('group_concat(distinct concat(l.tag_name,"/",l.tag_id) SEPARATOR ";" ) as product_tag',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_1_variants as b','a.product_id = b.product_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as bb','bb.product_id = b.product_id','left');
		$this->db->join('pos_i1_products_8_supplier as c','c.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as cc','c.supplier_id = cc.supp_id','left');
		
		$this->db->join('pos_i1_products_7_category as dd','dd.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as d','d.cat_id = dd.category_id','left');
		
		$this->db->join('pos_i1_products_6_brand as ee','ee.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as e','e.brand_index = ee.brand_id','left');

		$this->db->join('pos_i1_products_5_tags as k','k.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_tag as l','l.tag_id = k.tagged_id','left');
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.product_id',$product_id);
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
			return null;	
		}
	}
	public function get_single_variant_product_details($variant_id,$acc)
	{

		$this->db->select('a.product_id as main_product_id');
		$this->db->select('"1" as product_scale',false); // we are using scale as 1 for variant sub product to enhance in view as a standard product
		$this->db->select('concat_ws(" / ",a.product_name,GROUP_CONCAT(distinct bf.attribute_val order by bd.cust_var_value separator " / ")) as product_name',false);
		$this->db->select('a.handle');
		$this->db->select('b.status');
		$this->db->select('a.description');
		$this->db->select('c.supplier_id');
		$this->db->select('cc.cmp_name');
		$this->db->select('dd.category_id');
		$this->db->select('d.cat_name');
		$this->db->select('ee.brand_id');
		$this->db->select('e.brand_name');
		$this->db->select('b.product_weight');
		$this->db->select('b.wearhouse_id as prd_wh_id');
		$this->db->select('b.purchase_id as prd_pur_id');
		$this->db->select('b.sku');
		$this->db->select('b.price');
		$this->db->select('b.margin');
		$this->db->select('b.retail_price');
		$this->db->select('a.account_no');
		$this->db->select('b.loyalty');
		$this->db->select('b.track_inventory');
		$this->db->select('b.ship_stat');
		$this->db->select('b.is_shopping_cart');

		$this->db->select('group_concat(distinct concat(l.tag_name,"/",l.tag_id) SEPARATOR ";" ) as product_tag',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_1_variants as b','a.product_id = b.product_id','left');
		//
		$this->db->join('pos_i1_products_1_variants_attributes as bf','b.variant_index = bf.variant_id','left');
		$this->db->join('pos_i1_0_cust_variant_types as bd','bd.cust_var_id = bf.attribute_id','left');
		//		
		$this->db->join('pos_i1_products_8_supplier as c','c.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as cc','c.supplier_id = cc.supp_id','left');
		
		$this->db->join('pos_i1_products_7_category as dd','dd.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as d','d.cat_id = dd.category_id','left');
		
		$this->db->join('pos_i1_products_6_brand as ee','ee.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as e','e.brand_index = ee.brand_id','left');

		$this->db->join('pos_i1_products_5_tags as k','k.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_tag as l','l.tag_id = k.tagged_id','left');
		$this->db->where('a.account_no',$acc);
		$this->db->where('b.variant_index',$variant_id);
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
			return null;	
		}
	}
	public function get_variant_product_details($product_id,$acc)
	{
		$this->db->select('a.product_id as main_product_id');
		$this->db->select('a.product_name');
		$this->db->select('a.handle');
		$this->db->select('b.status');
		$this->db->select('a.description');
		$this->db->select('c.supplier_id');
		$this->db->select('dd.category_id');
		$this->db->select('ee.brand_id');
		$this->db->select('b.product_weight');
		$this->db->select('b.wearhouse_id as prd_wh_id');
		$this->db->select('b.purchase_id as prd_pur_id');
		$this->db->select('b.sku');
		$this->db->select('b.price');
		$this->db->select('b.margin');
		$this->db->select('b.retail_price');
		$this->db->select('a.account_no');
		$this->db->select('b.loyalty');
		$this->db->select('b.track_inventory');
		$this->db->select('b.ship_stat');
		$this->db->select('b.is_shopping_cart');

		$this->db->select('b.variant_index');
		$this->db->select('f.attribute_id');
		$this->db->select('f.attribute_val');
		$this->db->select('g.cust_var_value');

		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_1_variants as b','a.product_id = b.product_id');

		$this->db->join('pos_i1_products_8_supplier as c','c.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as cc','c.supplier_id = cc.supp_id','left');

		$this->db->join('pos_i1_products_7_category as dd','dd.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as d','d.cat_id = dd.category_id','left');
		
		$this->db->join('pos_i1_products_6_brand as ee','ee.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as e','e.brand_index = ee.brand_id','left');

		$this->db->join('pos_i1_products_1_variants_attributes as f','f.variant_id = b.variant_index','left');
		$this->db->join('pos_i1_0_cust_variant_types as g','g.cust_var_id = f.attribute_id');
		$this->db->where('a.account_no',$acc);
		$this->db->where('b.variant_index',$product_id);
		$this->db->order_by('b.position');
		$this->db->order_by('g.cust_var_value');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['main_product_id'] = $row->main_product_id;
				$array['variant_index'] = $row->variant_index;
				$array['product_name'] = $row->product_name;
				$array['handle'] = $row->handle;
				$array['brand_id'] = $row->brand_id;
				$array['category_id'] = $row->category_id;
				$array['supplier_id'] = $row->supplier_id;
				$array['description'] = $row->description;
				$array['sku'] = $row->sku;
				$array['prd_wh_id'] = $row->prd_wh_id;
				$array['prd_pur_id'] = $row->prd_pur_id;
				$array['ship_stat'] = $row->ship_stat;
				
				$array['account_no'] = $row->account_no;
				$array['price'] = $row->price;
				$array['margin'] = $row->margin;
				$array['retail_price'] = $row->retail_price;
				$array['product_weight'] = $row->product_weight;
				$array['loyalty'] = $row->loyalty;
				$array['is_shopping_cart'] = $row->is_shopping_cart;
				$array['status'] = $row->status;
				$array['track_inventory'] = $row->track_inventory;
				$array['attribute_id'][] = $row->attribute_id;
				$array['attribute_val'][] = $row->attribute_val;
				$array['attribute_name'][] = $row->cust_var_value;
			}
			return $array;				
		} else {
			return NULL;	
		}
	}
	public function get_blend_product_details($product_id,$acc)
	{
		$this->db->select('a.product_id as main_product_id');
		$this->db->select('a.product_scale');
		$this->db->select('a.product_name');
		$this->db->select('a.handle');
		$this->db->select('b.status');
		$this->db->select('a.description');
		$this->db->select('c.supplier_id');
		$this->db->select('cc.cmp_name');
		$this->db->select('dd.category_id');
		$this->db->select('d.cat_name');
		$this->db->select('ee.brand_id');
		$this->db->select('e.brand_name');
		$this->db->select('b.product_weight');
		$this->db->select('b.wearhouse_id as prd_wh_id');
		$this->db->select('b.purchase_id as prd_pur_id');
		$this->db->select('b.sku');
		$this->db->select('b.price');
		$this->db->select('b.margin');
		$this->db->select('b.retail_price');
		$this->db->select('a.account_no');
		$this->db->select('b.loyalty');
		$this->db->select('b.ship_stat');
		$this->db->select('b.is_shopping_cart');

		$this->db->select('group_concat(distinct concat(l.tag_name,"/",l.tag_id) SEPARATOR ";" ) as product_tag',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_0_blend as b','a.product_id = b.blend_product_id and a.account_no = b.account_no','left');

		$this->db->join('pos_i1_products_8_supplier as c','c.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as cc','c.supplier_id = cc.supp_id','left');

		$this->db->join('pos_i1_products_7_category as dd','dd.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as d','d.cat_id = dd.category_id','left');
		
		$this->db->join('pos_i1_products_6_brand as ee','ee.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as e','e.brand_index = ee.brand_id','left');

		$this->db->join('pos_i1_products_5_tags as k','k.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_tag as l','l.tag_id = k.tagged_id','left');		
		$this->db->where(array('a.product_id' => $product_id, 'a.account_no' => $acc));
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
			return null;	
		}
	}
	public function check_variant_parrent_id($prd_handle,$acc)
	{
		$this->db->select('product_id');
		$this->db->where('handle', $prd_handle); 
		$query = $this->db->get_where('pos_i1_products',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			if($row['product_id'] != "")
			{
				$main_prd_id = $row['product_id'];
				$this->db->select('V1K,V2K,V3K');
				$this->db->where('product_id', $row['product_id']); 
				$query = $this->db->get_where('pos_i1_products_1_variants_attributes',array('account_no' => $acc));
				if($query->num_rows() > 0)
				{
					$row = $query->row_array();
					return array($main_prd_id,$row['V1K'],$row['V2K'],$row['V3K']);
				} else {
					return NULL;	
				}
			} else {
				return NULL;	
			}
			return $row['product_id'] == '' ? NULL : $row['product_id'];
		} else {
			return NULL;	
		}
	}
	public function get_blend_sub_products($product_id,$acc)
	{
		$sql = "select 
				c.parent_product as product_id,
				(select product_name from pos_i1_products where product_id = c.parent_product) as product_name,
				c.parent_qty
				from pos_i1_products as a
				left join pos_i1_products_0_blend as b on b.blend_product_id = a.product_id
				left join pos_i1_products_4_blend as c on c.blend_product = b.blend_product_id
				left join pos_i1_products_2_num as e on e.product_id = c.parent_product
				left join pos_i1_products_3_kilo as f on f.product_id = c.parent_product
				WHERE
				a.product_id =  ? and
				a.account_no = ? and
				c.parent_product is not null
				
				union
				
				select
				d.variant_id as product_id,
				(select 
				concat_ws(' / ',product_name,GROUP_CONCAT(distinct gg.attribute_val order by ggg.cust_var_value separator ' / '))
				from pos_i1_products where product_id = d.product_id
				) as product_name,
				d.variant_qty as parent_qty
				
				from pos_i1_products as a
				left join pos_i1_products_0_blend as b on b.blend_product_id = a.product_id
				left join pos_i1_products_4_blend_variant as d on d.blend_product = b.blend_product_id
				left join pos_i1_products_1_variants as g on g.variant_index = d.variant_id 
				left join pos_i1_products_1_variants_attributes as gg on gg.variant_id = d.variant_id
				left join pos_i1_0_cust_variant_types as ggg on ggg.cust_var_id = gg.attribute_id
				WHERE
				a.product_id =  ? and
				a.account_no = ? and
				d.variant_id is not null
				group by d.variant_id
				";
		$query = $this->db->query($sql, array($product_id, $acc, $product_id, $acc));				
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
	public function check_product_tax_exist($product_id,$outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_tax',array('account_no' => $acc,'location' => $outlet_id,'main_product' => $product_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		}
	}
	public function check_variant_tax_exist($variant_id,$outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_tax_variant',array('account_no' => $acc,'location' => $outlet_id,'variant_id' => $variant_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		}
	}
	public function check_inventory_exist($product_id,$outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i2_a_inventory',array('account_no' => $acc,'location' => $outlet_id,'product_id' => $product_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		}
	}
	public function check_inventory_variant_exist($variant_id,$outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i2_a_inventory_variant',array('account_no' => $acc,'location' => $outlet_id,'variant_id' => $variant_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		}
	}
	public function check_inventory_stock_exist($stock,$product_id,$outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i2_a_inventory',array('current_stock' => $stock,'account_no' => $acc,'location' => $outlet_id,'product_id' => $product_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		} else {
			return false;	
		}
	}
	public function check_inventory_variant_stock_exist($stock,$variant_id,$outlet_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i2_a_inventory_variant',array('current_stock' => $stock,'account_no' => $acc,'location' => $outlet_id,'variant_id' => $variant_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		} else {
			return false;	
		}
	}
	public function check_blend_inv_stock($blend_product_id,$blend_prd_qty,$acc,$outlet)
	{
		$comma_blend_id = '"'.implode('","',$blend_product_id).'"';
		$sql = "select
					product_id as id,
					current_stock as stock
				from pos_i2_a_inventory
				where
					product_id in (".$comma_blend_id.") and
					location = ? and
					account_no = ? and
					product_id is not null
					
				union
				
				select
					variant_id as product_id,
					current_stock as stock
				from pos_i2_a_inventory_variant
				where
					variant_id in (".$comma_blend_id.") and
					location = ? and
					account_no = ? and
					variant_id is not null
					";
		$query = $this->db->query($sql,array($outlet,$acc,$outlet,$acc));					
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				if(in_array($row->id,$blend_product_id))
				{
					$key = array_search($row->id,$blend_product_id);
					$final[] = floor($row->stock / $blend_prd_qty[$key]);		
				}
			}
			return min($final);
		} else {
			return 0;	
		}
	}
	public function check_blend_exist_table($table,$assoc_product_field,$assoc_prod_id,$blend_field,$blend_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where($table,array($assoc_product_field => $assoc_prod_id, $blend_field => $blend_id,'account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$check = $row['counted'];
		} else {
			$check = 0;
		}
		return $check > 0 ? true : false;		
	}
	public function check_eav_exist($table,$field, $product_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where($table,array('account_no' => $acc, $field => $product_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;	
		} else {
			return false;	
		}
	}
	public function check_blend_added($blend_id,$product_id,$acc)
	{	
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_4_blend',array('account_no' => $acc, 'parent_product' => $product_id,'blend_product' => $blend_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'] > 0 ? true : false;
		} else {
			return false;	
		}
	}
	public function get_blend_id_from_child_array($array,$acc)
	{
		$this->db->select('blend_product');
		$this->db->where_in('blend_index', $array);
		$query = $this->db->get_where('pos_i1_products_4_blend',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['blend_product'];
		} else {
			return NULL;	
		}

	}
	public function get_sub_variants($var_product_id,$product_stat,$acc)
	{
		$product_stat = $product_stat == "VISIBLE" ? 30 : 40;		
		$this->db->select('concat_ws(" / ", b.product_name, GROUP_CONCAT(distinct d.attribute_val order by dd.cust_var_value separator " / ")) as prod_name',false);
		$this->db->select('a.retail_price');
		$this->db->select('(select ifnull(sum(current_stock),0) from pos_i2_a_inventory_variant where variant_id = a.variant_index) as stock',false);
		$this->db->select('a.status');
		$this->db->select('a.variant_index');
		$this->db->from('pos_i1_products_1_variants as a');
		$this->db->join('pos_i1_products as b','b.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as d','a.variant_index = d.variant_id');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = d.attribute_id','left');

		$this->db->where(array('a.status = ' => $product_stat ,'a.account_no' => $acc,'a.product_id' => $var_product_id));
		$this->db->group_by('variant_index');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return $query->result();				
		} else {
			return array();	
		}
	}
	public function get_unique_variant_details($var_product_id,$acc)
	{
		$this->db->select('b.product_name as product_name',false);
		$this->db->select('GROUP_CONCAT(distinct d.attribute_val order by dd.cust_var_value separator " / ") as variant_name',false);
		$this->db->select('b.product_scale as product_scale');
		$this->db->select('a.retail_price');
		$this->db->select('a.sku');
		$this->db->select('a.variant_index as product_id');
		$this->db->from('pos_i1_products_1_variants as a');
		$this->db->join('pos_i1_products as b','b.product_id = a.product_id','left');
		$this->db->join('pos_i1_products_1_variants_attributes as d','a.variant_index = d.variant_id');
		$this->db->join('pos_i1_0_cust_variant_types as dd','dd.cust_var_id = d.attribute_id','left');
		$this->db->where(array('a.account_no' => $acc,'a.variant_index' => $var_product_id));
		$this->db->group_by('a.variant_index');
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
			return array();	
		}

	}
	public function is_parent_variant($parent_variant_id,$acc)
	{
		$this->db->select('*');
		$this->db->from('pos_i1_products_1_variants as a');
		$this->db->join('pos_i1_products as b','b.product_id = a.product_id');
		$this->db->where(array('b.account_no' => $acc,'b.product_id' => $parent_variant_id));
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
			return array();	
		}
	}
	public function is_child_variant($child_variant_id,$acc)
	{
		$this->db->select('b.product_id');
		$this->db->from('pos_i1_products_1_variants as a');
		$this->db->join('pos_i1_products as b','b.product_id = a.product_id');
		$this->db->where(array('b.account_no' => $acc,'a.variant_index' => $child_variant_id));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['product_id'];
		} else {
			return NULL;	
		}
	}
	public function get_variant_parent_id_wrt_handle($handle,$acc) //for import product variants
	{
		$this->db->select('product_id');
		$this->db->from('pos_i1_products');
		$this->db->where(array('account_no' => $acc,'handle' => $handle));
		$query = $this->db->get();
		$row = $query->row_array();
		if(!empty($row['product_id']))
		{
			return $row['product_id'];
		} else {
			return NULL;
		}
			
	}
	public function delete_product($scale,$product_id,$acc)
	{		
		if($scale == 1){
			return $this->delete_num_kilo_product($scale,$product_id,$acc);	
		} else if($scale == 2 ){
			return $this->delete_num_kilo_product($scale,$product_id,$acc);	
		} else if($scale == 3){
			return $this->delete_variant_product($product_id,$acc);				
		} else if($scale == 4){
			return $this->delete_blend_product($product_id,$acc);				
		}
	}
	public function delete_hidden($acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();		
		$this->db->select('product_id');
		$query = $this->db->get_where('pos_i1_products_2_num',array('account_no' => $acc, 'status' => 40));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $rows)
			{
				$this->delete_product(1,$rows->product_id,$acc);	
			}
		}
		$this->db->select('product_id');
		$query = $this->db->get_where('pos_i1_products_3_kilo',array('account_no' => $acc, 'status' => 40));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $rows)
			{
				$this->delete_product(2,$rows->product_id,$acc);	
			}
		}
		$this->db->select('variant_index');
		$query = $this->db->get_where('pos_i1_products_1_variants',array('account_no' => $acc, 'status' => 40));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $rows)
			{
				$this->delete_product(3,$rows->variant_index,$acc);	
			}
		}
		$this->db->select('blend_product_id');
		$query = $this->db->get_where('pos_i1_products_0_blend',array('account_no' => $acc, 'status' => 40));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $rows)
			{
				$this->delete_product(4,$rows->blend_product_id,$acc);	
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function delete_num_kilo_product($scale,$product_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_4_blend',array('account_no' => $acc,'parent_product' => $product_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$assoc = $row['counted'] > 0 ? false : true;
			if($assoc)
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				$this->db->delete('pos_i1_products_tax',array('main_product' => $product_id,'account_no' => $acc));
				$this->db->delete('pos_i2_a_inventory',array('product_id' => $product_id,'account_no' => $acc));
				$this->db->delete('pos_i9_quickey_child',array('product_id' => $product_id,'account_no' => $acc));
				if($scale == 1)
				{
					$this->db->where(array('product_id' => $product_id,'account_no' => $acc));
					$this->db->where('status !=',25);
					$this->db->update('pos_i1_products_2_num', array('status' => 120)); 
				} else {
					$this->db->where(array('product_id' => $product_id,'account_no' => $acc));
					$this->db->update('pos_i1_products_3_kilo', array('status' => 120)); 
				}
				//outlet based
				$outlets = $this->outlet_model->outlet_assoc_id($acc);
				foreach($outlets as $o_key => $outlet)
				{
					$data[] = array(
								'log_index' => $this->taxes_model->make_single_uuid(),
								'user_id' => $this->session->userdata('user_id'),	
								'master_product' => $product_id,	
								'log_code' => 4,	
								'feed' => 0,
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
								'location' => $o_key,	
								'account_no' => $acc,								
								);
				}
				$this->db->insert_batch('pos_i1_products_log', $data); 
				//waiting dont delete if product is holded in parked, layby sale
				$this->unlink_file($product_id);
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
		} else {
			return 0;
		}
	}
	public function delete_blend_product($product_id,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$outlets = $this->outlet_model->outlet_assoc_id($acc);
		foreach($outlets as $o_key => $outlet)
		{
			$data[] = array(
						'log_index' => $this->taxes_model->make_single_uuid(),
						'user_id' => $this->session->userdata('user_id'),	
						'master_product' => $product_id,	
						'log_code' => 4,	
						'feed' => 0,	
						'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
						'location' => $o_key,	
						'account_no' => $acc,								
						);
		}
		$this->db->insert_batch('pos_i1_products_log', $data); 
		$this->db->delete('pos_i1_products_tax',array('main_product' => $product_id,'account_no' => $acc));
		$this->db->delete('pos_i9_quickey_child',array('product_id' => $product_id,'account_no' => $acc));
		$this->db->where(array('blend_product_id' => $product_id,'account_no' => $acc));
		$this->db->update('pos_i1_products_0_blend', array('status' => 120)); 
		$this->unlink_file($product_id);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}		
	}
	public function delete_variant_product($product_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_4_blend_variant',array('account_no' => $acc,'variant_id' => $product_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$assoc = $row['counted'] > 0 ? false : true;
			if($assoc)
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				///waiting - check for pending sale
				$this->db->select('b.product_id as master_product');
				$this->db->from('pos_i1_products_1_variants as a');
				$this->db->join('pos_i1_products as b','a.product_id = b.product_id');
				$this->db->where(array('a.variant_index' => $product_id,'a.account_no' => $acc));
				$query = $this->db->get();
				$row = $query->row_array();
				$master_product = $row['master_product'];
				
				$outlets = $this->outlet_model->outlet_assoc_id($acc);
				foreach($outlets as $o_key => $outlet)
				{
					$data[] = array(
								'log_var_index' => $this->taxes_model->make_single_uuid(),
								'user_id' => $this->session->userdata('user_id'),	
								'master_product' => $master_product,	
								'variant_id' => $product_id,
								'log_code' => 4,	
								'feed' => 0,	
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
								'location' => $o_key,	
								'account_no' => $acc,								
								);
				}
				$this->db->insert_batch('pos_i1_products_log_variants', $data); 
				$this->db->delete('pos_i1_products_tax_variant',array('product_id' => $master_product, 'variant_id' => $product_id,'account_no' => $acc));
				$this->db->delete('pos_i2_a_inventory_variant',array('parent_product' => $master_product,'variant_id' => $product_id,'account_no' => $acc));
				$this->db->where(array('variant_index' => $product_id,'account_no' => $acc));
				$this->db->update('pos_i1_products_1_variants', array('status' => 120)); 
				$this->unlink_file($product_id);
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
		} else {
			return 0;
		}
	}
	public function delete_all_variant($product_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i1_products_4_blend_variant',array('product_id' => $product_id,'account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$assoc = $row['counted'] > 0 ? false : true;
			if($assoc)
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				$this->db->select('variant_index');
				$this->db->select('b.product_id as master_product');
				$this->db->from('pos_i1_products_1_variants as a');
				$this->db->join('pos_i1_products as b','a.product_id = b.product_id');
				$this->db->where(array('a.product_id' => $product_id,'a.account_no' => $acc));
				$query = $this->db->get();
				$outlets = $this->outlet_model->outlet_assoc_id($acc);
				foreach ($query->result() as $row) 
				{
					$var_array[] = $row->variant_index;
					$this->unlink_file($row->variant_index);
					foreach($outlets as $o_key => $outlet)
					{
						$data[] = array(
									'log_var_index' => $this->taxes_model->make_single_uuid(),
									'user_id' => $this->session->userdata('user_id'),	
									'master_product' => $product_id,	
									'variant_id' => $row->variant_index,	
									'log_code' => 4,	
									'feed' => '',	
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
									'location' => $o_key,	
									'account_no' => $acc,								
									);
					}
				}
				$this->db->insert_batch('pos_i1_products_log_variants', $data); 
				$this->db->where_in('variant_id',$var_array);
				$this->db->where('product_id',$product_id);
				$this->db->where('account_no',$acc);
				$this->db->delete('pos_i1_products_tax_variant');

				$this->db->delete('pos_i2_a_inventory_variant',array('parent_product' => $product_id,'account_no' => $acc));
				$this->db->delete('pos_i9_quickey_child',array('product_id' => $product_id,'account_no' => $acc));

				$this->db->where(array('product_id' => $product_id,'account_no' => $acc));
				$this->db->update('pos_i1_products_1_variants', array('status' => 120)); 
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE)
				{
					return 0;
				} else {
					return 1;	
				}
			} else {
				return 3;
			}
		} else {
			return 0;
		}		
	}
	
	public function get_id_wrt_sku_if_blend($sku,$acc)
	{
		$this->db->select(
					'CASE a.product_scale 
						 when 3 then d.variant_index
						 when 2 then c.product_id
						 when 1 then b.product_id
					 END as indexed
					',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->or_where('b.sku',$sku);
		$this->db->or_where('c.sku',$sku);
		$this->db->or_where('d.sku',$sku);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();	
			return $row['indexed'];
		} else {
			return NULL;	
		}
	}
	public function get_main_id_wrt_variant_id($variant_id,$acc)
	{
		$this->db->select('product_id');
		$query = $this->db->get_where('pos_i1_products_1_variants',array('variant_index' => $variant_id, 'account_no' => $acc));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();	
			return $row['product_id'];
		} else {
			return NULL;	
		}
	}
	public function ajax_product_status($data,$acc)
	{
		switch ($data['scale']) {
			case "BLEND":		
				$table = 'pos_i1_products_0_blend';
				$column = 'blend_product_id';
			break;
			case "NUM":
				$table = 'pos_i1_products_2_num';
				$column = 'product_id';
			break;
			case "KILO":
				$table = 'pos_i1_products_3_kilo';
				$column = 'product_id';
			break;
			case "VARIANTS":
				$table = 'pos_i1_products_1_variants';
				$column = 'variant_index';
			break;
		}
		$stat = $data['clause'] == 1 ? 30 : 40;
		$update = array('status' => $stat);
		$this->db->where($column,$data['id']);
		$this->db->where(array('account_no' => $acc));
		if($this->db->update($table, $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function update_variant_pos($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		foreach($data['prd_params'] as $key => $value)
		{
			$update = array('position' => $key);
			$this->db->where('variant_index',$data['prd_params'][$key]);	
			$this->db->where('account_no',$data['merchant_id']);	
			$this->db->update('pos_i1_products_1_variants',$update);
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}		
	}
	public function download_kilo_products($acc)
	{
		$this->db->query('SET SESSION group_concat_max_len = 1000000;');
		$sql = "SELECT GROUP_CONCAT( 
							CONCAT( 
								'
								if(
									d.tax_id is not null,
									case (select is_group from pos_a_taxes where tax_id = (select tax_id from pos_i1_products_tax where main_product = a.product_id and location = ''',
									z.loc_id,'''
									))
									when 20 then (select tax_val from pos_a_taxes where tax_id = (select tax_id from pos_i1_products_tax where main_product = a.product_id and location = ''',
									z.loc_id,'''
									))
									when 10 then 
										(					
											SELECT 
												sum(pos_a_taxes.tax_val)
											FROM 
												pos_a_taxes_group
											join pos_a_taxes on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
											where pos_a_taxes_group.parent_id = (select tax_id from pos_i1_products_tax where main_product = a.product_id and location = ''',
									z.loc_id,'''
									)
									)        
										end,
										case (select is_group from pos_a_taxes where tax_id = (select outlet_tax from pos_b_locations where loc_id = ''',
									z.loc_id,'''
									))
									when 20 then 
											(select tax_val from pos_a_taxes where tax_id = 
													(select outlet_tax from pos_b_locations where loc_id = ''',
									z.loc_id,'''
									)
									)        
											when 10 then 
											(					
												SELECT 
													sum(pos_a_taxes.tax_val)
												FROM 
													pos_a_taxes_group
												join pos_a_taxes on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
												where pos_a_taxes_group.parent_id = (select outlet_tax from pos_b_locations where loc_id = ''',
									z.loc_id,'''
									)
									)        
										end
									) AS ',																																			
									concat('`',z.location,'_tax_value%','`' )	
								) order by z.location 
							) as tax_sql from pos_b_locations as z where z.outlet_stat = 30 and z.account_no = '".$acc."';";
							
		$query = $this->db->query($sql);
		$rows = $query->row_array();
		$dyn_fields['tax_sql'] = $rows['tax_sql'];
		
		$this->db->select('a.product_id');
		$this->db->select('a.product_name');
		$this->db->select('a.handle');
		$this->db->select('a.description');
		$this->db->select("c.pos_id as weighing_scale_id",false);
		$this->db->select('b.prefix_val as weighing_scale_prefix');
		$this->db->select('c.price as supplier_or_operated_price');
		$this->db->select('c.retail_price');
		$this->db->select($dyn_fields['tax_sql'],false);
		$this->db->select('c.sku');
		$this->db->select('c.product_weight');
		$this->db->select('c.loyalty');
		$this->db->select('if(c.is_shopping_cart = 30,1,0) as show_in_shopping_cart',false);
		$this->db->select('if(c.status = 30,1,0) as visibility',false);
		$this->db->select('if(c.track_inventory = 30,1,0) as trace_inventory',false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_kilo_product_prefix as b','a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_tax as d','d.main_product = c.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_a_taxes as e','e.tax_id = d.tax_id and a.account_no = e.account_no','left');
		$this->db->where('c.status != ',120);
		$this->db->where('a.product_scale',2);
		$this->db->where('a.account_no',$acc);
		$this->db->group_by('product_id');
		$this->db->order_by('product_name');
		$query = $this->db->get();
		return $query;
	}
	public function insert_ajax_cat($data)
	{
		$key = $this->taxes_model->make_single_uuid();
		$insert = array(
					'cat_id' => $key,
					'cat_name' => $data['cat_name'],
					'account_no' => $data['acc']
				);		
		if($this->db->insert('pos_i1_product_category',$insert))
		{
			$ajax_array = array('status' => 'success','key' => $key,'value' => $data['cat_name']);
		} else {
			$ajax_array = array('status' => 'fail','key' => NULL,'value' => NULL);
		}
		return $ajax_array;
	}
	public function insert_ajax_supplier($data)
	{
		$key = $this->taxes_model->make_single_uuid();
		$insert = array(
					'supp_id' => $key,
					'cmp_name' => $data['supp_name'],
					'is_delete' => 30,
					'supp_stat' => 30,					
					'account_no' => $data['acc']
				);		
		if($this->db->insert('pos_e_suppliers',$insert))
		{
			$ajax_array = array('status' => 'success','key' => $key,'value' => $data['supp_name']);
		} else {
			$ajax_array = array('status' => 'fail','key' => NULL,'value' => NULL);
		}
		return $ajax_array;
	}
	public function insert_ajax_brand($data)
	{
		$key = $this->taxes_model->make_single_uuid();
		$insert = array(
					'brand_index' => $key,
					'brand_name' => $data['brand_name'],
					'account_no' => $data['acc']
				);		
		if($this->db->insert('pos_i1_product_brand',$insert))
		{
			$ajax_array = array('status' => 'success','key' => $key,'value' => $data['brand_name']);
		} else {
			$ajax_array = array('status' => 'fail','key' => NULL,'value' => NULL);
		}
		return $ajax_array;
	}
	public function insert_ajax_tax($data)
	{
		$key = $this->taxes_model->make_single_uuid();
		$insert = array(
					'tax_id' => $key,
					'tax_name' => $data['tax_name'],
					'is_group' => 20,
					'tax_val' => $data['tax_val'],
					'is_delete' => 10,
					'tax_stat' => 30,
					'account_no' => $data['acc']
				);		
		if($this->db->insert('pos_a_taxes',$insert))
		{
			$ajax_array = array('status' => 'success','key' => $key,'value' => $data['tax_name']." (".$data['tax_val']."%)");
		} else {
			$ajax_array = array('status' => 'fail','key' => NULL,'value' => NULL);
		}
		return $ajax_array;
	}
	public function get_variant_attributes($product_id,$acc)
	{
		$this->db->select('b.cust_var_id');	
		$this->db->select('b.cust_var_value');	
		$this->db->from('pos_i1_products_1_variants_attributes as a');
		$this->db->join('pos_i1_0_cust_variant_types as b','b.cust_var_id = a.attribute_id');
		$this->db->where(array('a.product_id' => $product_id, 'a.account_no' => $acc));
		$this->db->order_by('cust_var_value');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->cust_var_id] = $row->cust_var_value;
			}
			return $array;				
		} else {
			return null;	
		}
	}
	public function get_retail_price_with_tax($product_id,$retail_price,$outlet_id,$acc)
	{
		$scale = $this->check_scale($product_id,$acc);
		$prd_tax_tbl = $scale == 3 ? 'pos_i1_products_tax_variant' : 'pos_i1_products_tax';		
		$where_field = $scale == 3 ? 'a.variant_id' : 'a.main_product';		
		$this->db->select('b.tax_id');
		$this->db->select('b.tax_val');
		$this->db->select('b.is_group');
		$this->db->from($prd_tax_tbl. ' as a');
		$this->db->join('pos_a_taxes as b','b.tax_id = a.tax_id and a.account_no = b.account_no','left');
		$this->db->where(array($where_field => $product_id,'a.location' => $outlet_id,'a.account_no' => $acc));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			if($row['is_group'] == 10)
			{
				$sql = "SELECT 
							sum(pos_a_taxes.tax_val) as sum_tax_val
						FROM 
							pos_a_taxes_group
						join pos_a_taxes on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
						where pos_a_taxes_group.parent_id = '".$row['tax_id']."'
						and pos_a_taxes_group.account_no = '".$acc."'";	
				$query = $this->db->query($sql);
				$tax_row = $query->row_array();
				$final_tax_val = $tax_row['sum_tax_val'];
			} else {
				$final_tax_val = $row['tax_val'];
			}
		} else { //get outlet tax
			$this->db->select('b.tax_id');
			$this->db->select('b.tax_val');
			$this->db->select('b.is_group');
			$this->db->from('pos_b_locations as a');
			$this->db->join('pos_a_taxes as b','b.tax_id = a.outlet_tax and a.account_no = b.account_no','left');
			$this->db->where(array('a.loc_id' => $outlet_id,'a.account_no' => $acc));
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				$row = $query->row_array();
				if($row['is_group'] == 10)
				{
					$sql = "SELECT 
								sum(pos_a_taxes.tax_val) as sum_tax_val
							FROM 
								pos_a_taxes_group
							join pos_a_taxes on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
							where pos_a_taxes_group.parent_id = '".$row['tax_id']."'
							and pos_a_taxes_group.account_no = '".$acc."'";	
					$query = $this->db->query($sql);
					$tax_row = $query->row_array();
					$final_tax_val = $tax_row['sum_tax_val'];
				} else {
					$final_tax_val = $row['tax_val'];
				}
			}
		}
		return (($final_tax_val/100) * $retail_price) + $retail_price;
	}
}
?>