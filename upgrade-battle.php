<?php

	# URL List
	include './misskey-instance-list.php';

	# 投稿の有無 (投稿: true)
	$posting = true;

	# 設定情報読み込み
	include './config.php';

	# URL List の数だけ処理
	$count_url = count($url);
	for ($i = 0; $i < $count_url; $i++) {

		# 各鯖からデータを取得
		$geturl = 'https://' . $url[$i] . '/api/meta';
		$ch = curl_init($geturl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("detail" => true)));
		$response=curl_exec($ch);
		curl_close($ch);
		$json = mb_convert_encoding($response, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
		$result = json_decode($json, true);

		# 書き込むデータの定義
		$req_date = date("Y-m-d H:i T", $_SERVER['REQUEST_TIME']);
		$now_version[$i] = $result['version'];
		$wcsv = array($req_date, $now_version[$i]);

		# ファイルの有無を確認
		$filename = $upgrade_battle_folder . $url[$i] . '.csv';
		if (file_exists($filename)) {

			#ある場合は、ファイルを読み込み
			$new_instance[$i] = false;
			$filer = fopen($filename, 'r');
			$rcsv = fgetcsv($filer);
			$past_version[$i] = $rcsv[1];
			fclose($filer);

			# 現在のバージョンが空白でない場合に、ファイルに書き込み
			if ($now_version[$i] != "") {

				$filew = fopen($filename, 'w');
				fputcsv($filew, $wcsv);
				fclose($filew);

			}

		} else {

			#ない場合 (新規) は、ファイルに書き込み
			$new_instance[$i] = true;
			$filew = fopen($filename, 'w');
			fputcsv($filew, $wcsv);
			fclose($filew);

		}

		# 投稿するかの判定
		if ($posting == true){

			# 新規の場合
			if ($new_instance[$i] == true) {

				# 投稿用のデータを作成
				$post_arr = array();
				$post_arr[] = "【🎉 New Misskey Instances Added! ㊗】\n\nHost：[";
				$post_arr[] = $url[$i];
				$post_arr[] = "](https://";
				$post_arr[] = $url[$i];
				$post_arr[] = ")\nVersion：";
				$post_arr[] = $now_version[$i];
				$post_arr[] = "\n#Misskey_Upgrade_Battle";

				# 配列要素連結処理
				$post_data = implode($post_arr);

			# 公式の場合
			} else if ($url[$i] == "misskey.io" xor $url[$i] == "co.misskey.io") {

				# 現在のバージョンが空白の場合は何もしない
				if ($now_version[$i] == "") {

				    $post_data = "";

				# バージョンに変化があった場合
				} else if (($now_version[$i] != $past_version[$i])) {

					# 投稿用のデータを作成
					$post_arr = array();
					$post_arr[] = "【🎉 Official Misskey Version Updated! ㊗】\n\nHost：[";
					$post_arr[] = $url[$i];
					$post_arr[] = "](https://";
					$post_arr[] = $url[$i];
					$post_arr[] = ")\nVersion：";
					$post_arr[] = $past_version[$i];
					$post_arr[] = " → ";
					$post_arr[] = $now_version[$i];
					$post_arr[] = "\n#Misskey_Upgrade_Battle";

					# 配列要素連結処理
					$post_data = implode($post_arr);

				# バージョンに変化がない場合は何もしない
				} else {

				    $post_data = "";

				}

			# 公式以外の場合
			} else {

				# 現在のバージョンが空白の場合は何もしない
				if ($now_version[$i] == "") {

				    $post_data = "";

				# バージョンに変化があった場合
				} else if (($now_version[$i] != $past_version[$i])) {

					# 投稿用のデータを作成
					$post_arr = array();
					$post_arr[] = "【🎉 Misskey Version Updated! ㊗】\n\nHost：[";
					$post_arr[] = $url[$i];
					$post_arr[] = "](https://";
					$post_arr[] = $url[$i];
					$post_arr[] = ")\nVersion：";
					$post_arr[] = $past_version[$i];
					$post_arr[] = " → ";
					$post_arr[] = $now_version[$i];
					$post_arr[] = "\n#Misskey_Upgrade_Battle";

					# 配列要素連結処理
					$post_data = implode($post_arr);

				# バージョンに変化がない場合は何もしない
				} else {

				    $post_data = "";

				}

			}

			# 投稿データがある場合に投稿
			if (!empty($post_data)){

				# 投稿処理
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
	}

?>