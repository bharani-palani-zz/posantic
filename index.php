<?php
echo $_SERVER['HTTP_HOST'].'<br>';
echo $_SERVER['REMOTE_ADDR'].'<br>';
$pieces = explode(".",$_SERVER['HTTP_HOST']);
//header('Location:Web');
$system_folder_name = "system_ci_2.2.4";
if($_SERVER['REMOTE_ADDR'] == "127.0.0.1" && substr_count($_SERVER['HTTP_HOST'],".") == 2)
{	
  $system_path = '../'.$system_folder_name;
} else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && substr_count($_SERVER['HTTP_HOST'],".") == 2){	// remote having posantic.co.in 
  $system_path = $system_folder_name;
} else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && substr_count($_SERVER['HTTP_HOST'],".") == 1){	// remote having posantic.com
  $system_path = $system_folder_name;
} else if($_SERVER['HTTP_HOST'] == "192.168.1.9"){
  $system_path = '../'.$system_folder_name;
} else if($_SERVER['HTTP_HOST'] == "localhost"){
  $system_path = '../'.$system_folder_name;
} else {
  $system_path = $system_folder_name;
}
echo $system_path.'<br>';
echo substr_count($_SERVER['HTTP_HOST'],".")
?>