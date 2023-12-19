<?php
require_once('nusoap/nusoap.php');

function get_sugar_name($ext) {

$user_name ='sugar_user';
$user_password = 'sugar_pass';
$soapclient = new nusoap_client('http://soap-api-ip/crm/soap.php');  //define the SOAP Client an

$result = $soapclient->call('login',array('user_auth'=>array('user_name'=>$user_name,'password'=>md5($user_password), 'version'=>'.01'), 'application_name'=>'SoapTest'));
$session = $result['id'];

$result = $soapclient->call('get_entry_list',array('session'=>$session,'module_name'=>'Contacts','query'=>"mc_extension_c like '%$ext'",'order_by'=>'contacts.last_name asc','offset'=>'0', 'select_fields'=>array('first_name', 'last_name', 'mc_extension_c'), 'max_results'=>'500'));

//$result = $soapclient->call('logout',array('session'=>$session));
$soapclient->call('logout',array('session'=>$session));

if (isset($result['entry_list'][0]))
return $result['entry_list'][0]['name_value_list'][0]['value'].' '.$result['entry_list'][0]['name_value_list'][1]['value'];
else
return "Abonent i paregjistruar";



}
?>