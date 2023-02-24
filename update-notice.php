<?php

	# 投稿の有無 (投稿: true)
	$posting = true;

	# GitHub
	$github = 'https://api.github.com/repos/misskey-dev/misskey/releases/latest';

	# 設定情報読み込み
	include './config.php';

    # 取得
	$ch = curl_init($github);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt($ch, CURLOPT_USERAGENT, 'h3zjp_Misskey_Update_Notice');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response=curl_exec($ch);
	curl_close($ch);
	$json = mb_convert_encoding($response, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
	$result = json_decode($json, true);

	$filename = $notice_folder . 'update-notice.csv';
	$filer = fopen($filename, 'r');
	$rcsv = fgetcsv($filer);
	$past_version[$i] = $rcsv[1];
	fclose($filer);

	$req_date = date("Y-m-d H:i T", $_SERVER['REQUEST_TIME']);
	$now_version[$i] = $result['tag_name'];
	$wcsv = array($req_date, $now_version[$i]);

	if (($now_version[$i] != "")) {

		$filew = fopen($filename, 'w');
		fputcsv($filew, $wcsv);
		fclose($filew);

	}


    # 判定
	if ($now_version[$i] == "") {

		$post_data = "";

	} else if (($now_version[$i] != $past_version[$i])) {

		$post_arr = array();
		$post_arr[] = "【✨🎉 Misskey New Version Released! ㊗✨】\n\n";
		$post_arr[] = "Version：";
		$post_arr[] = $past_version[$i];
		$post_arr[] = " → ";
		$post_arr[] = $now_version[$i];
		$post_arr[] = "\nRelease detail：[https://github.com/misskey-dev/misskey/blob/master/CHANGELOG.md](https://github.com/misskey-dev/misskey/blob/master/CHANGELOG.md)";
		$post_arr[] = "\n#Misskey_Upgrade_Battle";

		$post_data = implode($post_arr);

	} else {

		$post_data = "";

	}
	
	
	# 投稿
	if($posting == true){

		if($post_data !== ""){

		$data = [
				'i' => $api_key,
				'text' => $post_data,
				'visibility' => 'public',
		];

		$json_data = json_encode($data);

		$ch = curl_init($put_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_exec($ch);
		curl_close($ch);

		}
	}

?>