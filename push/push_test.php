<?php
/**
  *
  * ===========================================
  * @Author Ryan
  * @Filename: push_test.php
  * @Description: (用一句话描述该文件做什么)
  * @Creation 2014-6-9 下午12:10:16
  * @Modify
  * @version V1.0
  * -----------------------------------------------------------
*/

$deviceToken = '64c3b99e61a88ab017698f8293a2fdb3b55b5222845314b5141f12cd4d6fa0a0';

//$apnsHost = 'gateway.push.apple.com';
$apnsHost = 'gateway.sandbox.push.apple.com';
$apnsPort = 2195;
//$apnsCert = dirname(__FILE__) . '/' . 'apns-dist.pem';
$apnsCert = dirname(__FILE__) . '/' . 'apns-dev.pem';

$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
$apns = stream_socket_client('ssl://'.$apnsHost.':'.$apnsPort,$error,$errstr,60,STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,$streamContext);

$payload['aps'] = array('alert' => '推送推送，啦啦啦~~~', 'badge' => 1, 'sound' => 'default');
$payload['server'] = array('serverId' => '1', 'name' => 'Ryan');
$payload = json_encode($payload);

$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
fwrite($apns, $apnsMessage);
socket_close($apns);
fclose($apns);