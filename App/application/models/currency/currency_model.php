<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Currency_model extends CI_Model
{
	public function getsymbol($country_code)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_1a_currency',array('country_code' => $country_code));
		$row = $query->row_array();
		return $row['symbol'];
	}
	public function  moneyFormat($num,$locale,$def_dec_length = 2)
	{
		$explrestunits = "" ;
		$num = is_float($num) == true ? number_format($num,$def_dec_length) : number_format($num,0);
		$num = str_replace(",","",$num);
		if(strpos($num, ".") === false)
		{
			$num = $num;
			$postfix = "";	
		} else {
			$num = explode(".",$num);
			$postfix = ".".$num[1];
			$num = $num[0];
		}
		$offset = ($locale == "INR") ? 2 : 3;
		if(strlen($num) > 3){
			$lastthree = substr($num, strlen($num)-3, strlen($num));
			$restunits = substr($num, 0, strlen($num)-3);
			$restunits = (strlen($restunits) %$offset == 1) ? "0".$restunits : $restunits;
			$expunit = str_split($restunits, $offset);
			for($i=0; $i<count($expunit); $i++)
			{
				if($i==0)
				{
					$explrestunits .= (int)$expunit[$i].",";
				}else{
					$explrestunits .= $expunit[$i].",";
				}
			}
			$thecash = $explrestunits.$lastthree.$postfix;
		} else {
			$thecash = $num.$postfix;
		}
		if(substr($thecash, 0,2) == "0,")
		{
			$thecash = "-".substr($thecash, 2);			
		}
		return $thecash;

	}

}
?>