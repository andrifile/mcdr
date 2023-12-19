<?php

require_once('nusoap/nusoap.php'); require_once('mysql.php');

class MCDR {
	var $link;
	var $crm_soapclient;
	var $crm_username;
	var $crm_password;
	
	public function __construct($link) {
		$this->link = $link;
		$this->crm_soapclient='http://127.0.0.1/crm/soap.php';
		$this->crm_username='user';
		$this->crm_password='pass';
	}

	public function __destruct() {
		mysql_close($this->link);
	}

	public function getRates() {
		$cmim = mysql_query("select * from cmimet", $this->link);
		while($row=mysql_fetch_row($cmim))
			$operator[]=array($row[1], $row[2], $row[5]);
		return $operator;
	}

	function getName($ext) {
		$user_name =$this->crm_username;
		$user_password = $this->crm_password;
		$soapclient = new nusoap_client($this->crm_soapclient);  //define the SOAP Client an

		$auth = array(
			'user_name'=>$user_name,
			'password'=>md5($user_password),
			'version'=>'.01');
		
		$result = $soapclient->call('login',array('user_auth'=>$auth, 'application_name'=>'SoapCDR'));
		$session = $result['id'];
		
		$package = array(
			'session'=>$session,
			'module_name'=>'Contacts',
			'query'=>"mc_extension_c like '%$ext'",
			'order_by'=>'contacts.last_name asc',
			'offset'=>'0',
			'select_fields'=>array('first_name', 'last_name', 'mc_extension_c'),
			'max_results'=>'500');

		$result = $soapclient->call('get_entry_list',$package);

		//$result = $soapclient->call('logout',array('session'=>$session));
		$soapclient->call('logout',array('session'=>$session));

		if (isset($result['entry_list'][0]))
			return $result['entry_list'][0]['name_value_list'][0]['value'].' '.$result['entry_list'][0]['name_value_list'][1]['value'];
		else
			return "Abonent i paregjistruar";
	}

	public function getDistinctSources($ext) {
		$query = "select distinct(src) from cdr where src like '".$ext."%'";
		$result = mysql_query($query);
		while ($row = mysql_fetch_row($result))
			$ret[] = $row[0];
		return $ret;
	}

	//get rate for called number
	public function getDstRate($dst) {
		$dst = preg_replace("@^[+|++|00]355.+@i", "0", $dst);
		$op = $this->getRates();

		$prefix = substr($dst, 0, 3);
		if (substr($prefix,1,1)=="4" || $opname=='ALBTELEKOM') {
			return $op[0];
		} else if ($prefix=="069" || $opname=='VODAFONE') { 
			return $op[1];
		} else if ($prefix=="068" || $opname=='AMC') { 
			return $op[2];
		} else if ($prefix=="067" || $opname=='EAGLE') {
			return $op[3];
		} else if ($prefix=="066" || $opname=='PLUS') {
			return $op[4];
		} else {
			return array("Internal", "", 0, 0, 0);
		}
	}

	public function CRMConnect() {	}
	
	public function CRMDisconnect() { }
}

$m = new MCDR($link);
print_r($m->getDstRate('0695624436'));
?>