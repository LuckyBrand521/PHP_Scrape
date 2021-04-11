<?php 

$time_start = microtime(true); 

$count = 200;
$slot =0; while($slot <$count) {
$urls[$slot] = 'http:///////';
$slot++;
}



$multiCurl = array(); $result = array(); $mh = curl_multi_init();

foreach ($urls as $i => $url) { 
  $multiCurl[$i] = curl_init();
  //$proxy_host = 'us-wa.proxymesh.com:31280'; 
  curl_setopt($multiCurl[$i], CURLOPT_URL,$url);
  curl_setopt($multiCurl[$i], CURLOPT_HEADER,0);
 // curl_setopt($multiCurl[$i], CURLOPT_PROXY, $proxy_host);
  curl_setopt($multiCurl[$i], CURLOPT_RETURNTRANSFER,1);
  curl_multi_add_handle($mh, $multiCurl[$i]);
}
$index=null;

do { 
  curl_multi_exec($mh,$index); 
} while($index > 0);

foreach($multiCurl as $k => $ch) {
	
	
  $result[$k] = curl_multi_getcontent($ch);
  curl_multi_remove_handle($mh, $ch);
  

}

curl_multi_close($mh);

print_r($result);

  $time_end = microtime(true);

$execution_time = ($time_end - $time_start);

echo "Full Execution time $execution_time";

sleep(5);
?>