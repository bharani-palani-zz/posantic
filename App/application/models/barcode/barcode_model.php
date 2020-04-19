<?php
class Barcode_model extends CI_Model
{
	public function barcode_types()
	{
		$types = array(
					'code25' => 'CODE 25: Allowed Characters: "0123456789" | Length: Variable',
					'code39' => 'CODE 39:  Allowed characters: "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ -.$/+%" | Length: Variable',
					'code128' => 'CODE 128: Allowed Characters: Complete ASCII-character set | Length: Variable',
					'ean8' => 'EAN-8: Allowed Characters: "0123456789" | Length: 8',
					'ean13' => 'EAN-13: Allowed Characters: "0123456789" | Length: 13',
					);	
		return $types;
	}
	public function pad_embed_string($string,$max_length)
	{
		if(strlen($string) > $max_length)	
		{
			return false;	
		} else {
			return str_pad($string, $max_length, '0', STR_PAD_LEFT);
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
	
}
?>