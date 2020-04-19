<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Inventory_json extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $pos_display_user;
	public $loc_id;
    public function __construct() 
    {
        parent::__construct();
		$this->load->model('inventory/inventoryjson_model');
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->pos_display_user = $this->session->userdata('pos_display_user');
		$this->loc_id = $this->session->userdata('loc_id');
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}
    }

	public function get_ST_products($take_id)
	{
		$this->load->view('session/pos_session');
		$prd = $this->inventoryjson_model->get_ST_products($take_id,$this->acc);
		print_r(json_encode($prd,JSON_PRETTY_PRINT));
		//echo $this->output->enable_profiler(TRUE);
	}
	public function get_ST_id_main_data()
	{
		$this->load->view('session/pos_session');
		$st = $this->inventoryjson_model->get_ST_id_main_data($this->acc);
		print_r(json_encode($st,JSON_PRETTY_PRINT));
	}
	
	public function get_ST_id_sub_data()
	{
		$this->load->view('session/pos_session');
		$st = $this->inventoryjson_model->get_ST_id_sub_data($this->acc);
		print_r(json_encode($st,JSON_PRETTY_PRINT));
	}
	public function get_ST_id_products($take_id)
	{
		$this->load->view('session/pos_session');
		$st = $this->inventoryjson_model->get_ST_id_products($take_id,$this->acc);
		print_r(json_encode($st,JSON_PRETTY_PRINT));
				
	}
	public function post_ST_products()
	{
		$this->load->view('session/pos_session');
		$post_data['index'] = $this->input->post('index');
		$post_data['take_id'] = $this->input->post('take_id');
		if($this->input->post('is_variant_product') == "false")
		{
			$post_data['product_id'] = $this->input->post('product_id');
			$post_data['variant_id'] = NULL;
		} else {
			$post_data['variant_id'] = $this->input->post('product_id');
			$post_data['product_id'] = NULL;
		}
		$post_data['expected'] = $this->input->post('expected');
		$post_data['counted'] = $this->input->post('counted');
		$post_data['cost_gain'] = $this->input->post('cost_gain');
		$post_data['cost_loss'] = $this->input->post('cost_loss');
		$post_data['count_gain'] = $this->input->post('count_gain');
		$post_data['count_loss'] = $this->input->post('count_loss');
		$post_data['acc'] = $this->acc;
		$response = $this->inventoryjson_model->post_ST_products($post_data);
		print_r(json_encode($response,JSON_PRETTY_PRINT));
	}
	public function post_ST_product_count()
	{
		$this->load->view('session/pos_session');
		$post_data['index'] = $this->input->post('index');
		$post_data['take_id'] = $this->input->post('take_id');
		if($this->input->post('is_variant_product') == "false")
		{
			$post_data['product_id'] = $this->input->post('product_id');
			$post_data['variant_id'] = NULL;
		} else {
			$post_data['variant_id'] = $this->input->post('product_id');
			$post_data['product_id'] = NULL;
		}
		$post_data['expected'] = $this->input->post('expected');
		$post_data['counted'] = $this->input->post('counted');
		$post_data['cost_gain'] = $this->input->post('cost_gain');
		$post_data['cost_loss'] = $this->input->post('cost_loss');
		$post_data['count_gain'] = $this->input->post('count_gain');
		$post_data['count_loss'] = $this->input->post('count_loss');
		$post_data['acc'] = $this->acc;
		$response = $this->inventoryjson_model->update_ST_products($post_data);
		print_r(json_encode($response,JSON_PRETTY_PRINT));
	}
}