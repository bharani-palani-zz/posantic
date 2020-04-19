<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Barcode_controller extends CI_Controller
{
	public $acc;
	public $privelage;
	public $loc_id;
	public $pos_user;
	public $user_id;
    public function __construct() 
    {
        parent::__construct();
		$this->load->library('zend');
		$this->load->helper('text');
		$this->load->helper('download');
		$this->load->model('product_model');
		$this->loc_id = $this->session->userdata('loc_id');
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');

		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);

		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function embed_string($string,$max_length)
	{
		if(strlen($string) > $max_length)	
		{
			return false;	
		} else {
			return str_pad($string, $max_length, '0', STR_PAD_LEFT);
		}
	}
	public function checksum($digits){
		$digits =(string)$digits;
		if(strlen($digits) == 12)
		{
			$even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
			$even_sum_three = $even_sum * 3;
			$odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
			$total_sum = $even_sum_three + $odd_sum;
			$next_ten = (ceil($total_sum/10))*10;
			$check_digit = $next_ten - $total_sum;
			return $digits . $check_digit;
		} else {
			return false;
		}
	}
	public function make_barcode_scale($val)
	{
		if(is_numeric($val))
		{
			if(!($val > 999.99))
			{
				$val = round($val, 2) * 100;
				$final = str_pad($val, 5,"0",STR_PAD_LEFT);  
				return $final;
			} else {
				return false;	
			}
		} else {
			return false;	
		}
	}
	public function make_barcode()
	{
		$data['bcode_val'] = $this->input->post('bcode_val');
		$data['codetype'] = $this->input->post('codetype');
		$data['bcode_count'] = $this->input->post('bcode_count');
		$data['bcode_height'] = $this->input->post('bcode_height');
		$data['bcode_font'] = $this->input->post('bcode_font');
		$data['product_name'] = $this->input->post('product_name');
		$data['variant_name'] = $this->input->post('variant_name');
		$data['product_scale'] = $this->input->post('product_scale');
		$data['retail_price'] = $this->input->post('retail_price');
		$data['kilo_val'] = $this->input->post('kilo_val');
		$data['printertype'] = $this->input->post('printertype');
		$data['fit_page'] = $this->input->post('fitpage');
		$data['pos_id'] = $this->input->post('pos_id');
		$data['barcode_prefix'] = $this->input->post('barcode_prefix');
		$data['product_id'] = $this->input->post('product_id');
		$data['curr'] = $this->session->userdata('currency');
		$data['outlet'] = $this->input->post('outlet');
		$data['tax_switch'] = $this->input->post('tax_switch');
		if($data['tax_switch'] == 30)
		{
			$data['retail_price'] = $this->product_model->get_retail_price_with_tax($data['product_id'],$data['retail_price'],$data['outlet'],$this->acc);
		}
		
		$code_data = '';
		if($data['product_scale'] == 1 || $data['product_scale'] == 4)
		{
			$caption = array(
							$data['product_name'],
							number_format($data['retail_price'],2)." ".$data['curr']
							);
			$code_data = $data['bcode_val'];
		} else if($data['product_scale'] == 2) {				
			$data_item = $this->embed_string($data['pos_id'],5);
			$variant_item = $this->make_barcode_scale($data['kilo_val']);
			$var_text = number_format($variant_item/100,2);
			$kilo = $data['barcode_prefix'].$data_item.$variant_item;
			$code_data = $this->checksum($kilo); //digits made here
			$code_data = (strlen($code_data) == 13 && $data['codetype'] == "ean13") ? substr($code_data, 0, -1) : $code_data;
			$price = number_format($data['retail_price'] * $data['kilo_val'],2)." ".$data['curr'];
			$caption = array(
							$data['product_name'],
							$var_text.' - KILO(s)',
							'Price - '. $price
							);			
		} else if($data['product_scale'] == 3) {				
			$var_name = implode(" / ",array_filter(explode(" / ",$data['variant_name'])));	
			$caption = array(
							$data['product_name'],
							$var_name,
							number_format($data['retail_price'],2)." ".$data['curr']
							);
			$code_data = $data['bcode_val'];
			if(is_numeric($code_data) && strlen($code_data) == 13) 
			{ 
				$data['codetype'] = 'ean13'; 
				$code_data = substr($code_data, 0, -1) ;
			}	
		}
		if($this->input->post('cap_switch') == 30)
		{
			$caption = $caption;	
		} else {
			$caption = array();	
		}
		if($this->input->post('bcode_sub') == "raw")
		{
			$barcodeOptions = array(
									'text' => $code_data,
									'barHeight' => $data['bcode_height'],
									'font' => $data['bcode_font'],
									'codetype' => $data['codetype'],
									'count' => $data['bcode_count'],
									'printer' => $data['printertype'],
									'caption' => $caption
									);
			$this->load->view("products/print_barcodes",$barcodeOptions);
		} 
		if($this->input->post('bcode_down') == "down"){
			$this->form_validation->set_error_delimiters('<p class="form_errors">', '</p>');		
			$this->form_validation->set_rules('bcode_count', 'barcode count', 'required|numeric|xss_clean|less_than[2]|greater_than[0]');
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('form_errors', 'Error: Try maximum of 1 barcode count. Multiple PDF Prints can be done changing your printer options');				
				redirect(base_url().'products/make_barcode/'.$data['product_id']);
			} else {
				$pdfOptions = array(
										'text' => $code_data,
										'barHeight' => $data['bcode_height'],
										'font' => $data['bcode_font'],
										'codetype' => $data['codetype'],
										'count' => $data['bcode_count'],
										'fit_page' => $data['fit_page'],
										'caption' => $caption
										);
				$this->download_pdf($pdfOptions,$this->input->post('cap_switch'));		
			}
		}
	}
	public function download_pdf($bcopts,$capswitch)
	{
		// Create Pdf definition
		$this->zend->load('Zend/Barcode');
		$this->zend->load('Zend/Pdf');
		$pdf = new Zend_Pdf();
				
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_ROMAN);
		Zend_Barcode::setBarcodeFont(APPPATH . 'fonts/arial.ttf');
		$last_page_index = $bcopts['count'];
		$caption_arr = $bcopts['caption'];
		$cap_font_size = 55;
				
		for ($page_index = 0; $page_index < $last_page_index; $page_index++)
		{
			$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
			$pdf->pages[] = $page;
			$page->setFont($font,$cap_font_size);
			$yaxis = 20;
			for($i=0;$i<count($caption_arr);$i++)
			{
				$caption = html_entity_decode(ellipsize(utf8_decode($caption_arr[$i]),30,.5));
				$textWidth = $page->getTextWidth($caption,$font,$cap_font_size);
				$page->drawText($caption,($page->getWidth() / 2) - ($textWidth / 2),$yaxis,"UTF-8");	
				$yaxis += 55;
			}
		}
		$barcodeOptions = array('text' => $bcopts['text'],'barHeight'=> $bcopts['barHeight'], 'factor' => $bcopts['fit_page']);
		$rendererOptions = ($capswitch == 30) ? array('horizontalPosition' => 'center', 'verticalPosition' => 'top') : array('horizontalPosition' => 'center', 'verticalPosition' => 'middle'); 
		for ($page_index = 0; $page_index < $last_page_index; $page_index++)
		{
			Zend_Barcode::factory($bcopts['codetype'], 'pdf',$barcodeOptions, $rendererOptions)->setResource($pdf, $page_index)->draw();
		}
		header ('Content-Type:', 'application/pdf');
		header ('Content-Disposition:', 'inline;');
		echo $pdf->render();
	}
	public function render_barcode($text,$barHeight,$font,$codetype)
	{
		$barcodeOptions = array(
								'text' => $text, 
								'barHeight' => $barHeight,
								'font' => $font
								);
		$this->zend->load('Zend/Barcode');
		$rendererOptions = array();
		Zend_Barcode::render($codetype, 'image', $barcodeOptions, $rendererOptions);	
	}
}