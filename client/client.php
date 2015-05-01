<?php
//项目名称, 就是编译出的二进制文件名称
$project_name = 'Puzzoku';
$server = trim(file_get_contents('./url.conf'));
if (strlen($server) < 3) {
	  echo "please copy url.conf.sample to url.conf and change the url to your api.";
		exit();
}

$get_ids_list = $server . 'get_ids_list';
$get_report_by_id_api = $server . 'get_report_by_id';
$upload_api = $server . 'upload_by_id';



$ids = json_decode(file_get_contents($get_ids_list));
if (count($ids) < 1) {
	echo 'NO LOGS NEED TO TRANSLATE';
	exit();
}

foreach ($ids as $id) {
	get_log_by_id($id);
}

function get_log_by_id($id) {
	echo "get_log_by_id: $id \n";
	get_analyse($id);
}

echo "FINISHED ::";



function get_analyse($id) {
	global $get_report_by_id_api, $project_name;
	$url = $get_report_by_id_api . '?id=' . $id;
	$file = 'log_' . $id . '.crash';
	echo "get log file:" . $url . "\n";
	$command = 'wget "' . $get_report_by_id_api . '?id=' . $id . '" -O'.$file . ' --no-check-certificate';
	exec($command);
	echo "log download::" . $file;
	$tt = 'export DEVELOPER_DIR=/Applications/Xcode.app/Contents/Developer';
	//translate
	$app = '/Applications/Xcode.app/Contents/Developer/Platforms/iPhoneOS.platform/Developer/Library/PrivateFrameworks/DTDeviceKitBase.framework/Versions/A/Resources/symbolicatecrash';
	$xcode6 = '/Applications/Xcode.app/Contents/SharedFrameworks/DTDeviceKitBase.framework/Versions/A/Resources/symbolicatecrash';
	$tr = $tt . ';' . $xcode6 . ' -o'.$file.'.txt ' . $file . ' | more | grep Pu ';
	echo "=================\n";
	echo $tr . "\n";
	echo "start:\n";
	$output = shell_exec($tr);
	echo "=================\n";

	//analyse result
	$result = check_result($file.'.txt');
	echo ">>>>>>>>>>>>>>>>>>>>\n";
	echo "CHECK RESULT:" . ($result ? 'PASS' : 'FAILED') . ":" .$file.'.txt' . "\n";
	echo "<<<<<<<<<<<<<<<<<<<<\n";

	if (!$result) {
		return;
	}

	global $upload_api;
	$data = array('id' => $id, 'content' => file_get_contents($file.'.txt'));

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($upload_api, false, $context);

	var_dump($result);
	echo "=====================\nUPLOAD SUCCESS\n=====================\n";
}



function check_result($dir) {
	global $project_name;
	$test_preg1 = "/^\d{1,}\s{1,}" . $project_name ."\s{1,}0x\w{1,}\s{1,}0x\w{1,}\s{1,}\+\s{1,}\d{1,}$/";
	$test_preg2 = "/^\d{1,}\s{1,}" . $project_name ."/";
	$lines = file($dir);
	$start = false;
	foreach ($lines as $line) {
		if (!$start) {
			if (preg_match('/^Thread \d{1,} Crashed:$/', $line)) {
			echo "$line\n";
				$start = true;
			}
			continue;
		}
		// check:
		if (preg_match($test_preg1, $line) && preg_match($test_preg2, $line)) {
			echo $line . "\n";
			return false;
		}
		if ($start) {
			if (preg_match('/^Binary Images:$/', $line)) {
				return true;
			}

		}
	}
	return true;
}
