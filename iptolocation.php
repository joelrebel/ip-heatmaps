#!/usr/bin/php -q
<?php

include("geoipcity.inc");
include("geoipregionvars.php");

// uncomment for Shared Memory support
// geoip_load_shared_mem("/usr/local/share/GeoIP/GeoIPCity.dat");
// $gi = geoip_open("/usr/local/share/GeoIP/GeoIPCity.dat",GEOIP_SHARED_MEMORY);

$geo_db='GeoLiteCity.dat';
//http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
if (file_exists($geo_db)) {
}else{
        echo "$geo_db not found, downloading..\n";
        shell_exec("sh get_geoip_dat.sh &>/dev/null");
        exit;
}
$gi = geoip_open("$geo_db",GEOIP_STANDARD);

$view = $_GET['view'];
#CLOSE_WAIT
#ESTABLISHED
#FIN_WAIT1
#FIN_WAIT2
#LAST_ACK
#SYN_RECV
#TIME_WAIT


switch ($view) {
	case "t_syn_recv" :
		$cmd = 'netstat -tanp | awk \'/SYN_RECV/{print $5}\' | sed -e \'s/:.*//g\'';
		break;
	case "t_established" : 
		$cmd = 'netstat -tanp | awk \'/ESTABLISHED/{print $5}\' | sed -e \'s/:.*//g\'';
		break;	
	case "t_both" :
		$cmd = 'netstat -tanp | awk \'/ESTABLISHED|SYN_RECV/{print $5}\' | sed -e \'s/:.*//g\'';	
		break;	 	
	case "t_close_wait" :
		$cmd = 'netstat -tanp | awk \'/CLOSE_WAIT/{print $5}\' | sed -e \'s/:.*//g\''	;
		break;	
	case "t_time_wait" :
		$cmd = 'netstat -tanp | awk \'/TIME_WAIT/{print $5}\' | sed -e \'s/:.*//g\''	;
		break;	 
	case "t_fin_wait" :
		$cmd = 'netstat -tanp | awk \'/FIN_WAIT1|FIN_WAIT2/{print $5}\' | sed -e \'s/:.*//g\''	;
		break;	 	
	case "t_last_ack" :
		$cmd = 'netstat -tanp | awk \'/LAST_ACK/{print $5}\' | sed -e \'s/:.*//g\''	;
		break;	 	
	case "udp_53":
		$cmd = 'ruby grab_packets.rb';

	default : 

	//	$cmd = 'netstat -tanp | awk \'/ESTABLISHED/{print $5}\' | sed -e \'s/:.*//g\'';
		$cmd = 'netstat -tanp | awk \'/SYN_RECV/{print $5}\' | sed -e \'s/:.*//g\'';
		//$cmd = 'ruby grab_packets.rb';
		break;	
}
//$cmd = 'netstat -tanp | awk \'/SYN_RECV/{print $5}\' | sed -e \'s/:.*//g\'';
$op = rtrim(shell_exec($cmd));
if($op){
$ips = array_count_values(explode("\n", rtrim($op)));
//print $op;
//$ips = explode("\n", rtrim($op));
//print_r ($ips);
//print count($ips);
}else{
	exit;
}

$idx=0;
while (list($ip, $count) =  each($ips)) {
	if( preg_match("/(172\.16|192\.168)/", $ip) ) { continue; }
	$record = geoip_record_by_addr($gi,"$ip");
	$data[$idx]['lng'] = $record->longitude;
	$data[$idx]['lat'] = $record->latitude ;
	$data[$idx]['count'] = $count;
	$idx++;
		
}
geoip_close($gi);
//data: [{lat: 33.5363, lng:-117.044, count: 1}] */
//print_r ($data);
print json_encode($data);



?>
