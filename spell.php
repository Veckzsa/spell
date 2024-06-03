<?php
/* Bot spell by @RiyanCoday */
error_reporting(0);
function getCoday($url,$headers){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
return curl_exec($ch);
curl_close($ch);
}
function postCoday($url,$headers){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
return curl_exec($ch);
curl_close($ch);
}
function tanggal($timestamp_ms) {
    if ($timestamp_ms <= 1) {
        return "-";
    }
    $timestamp = (int)($timestamp_ms / 1000);
    $date = new DateTime("@$timestamp");
    $date->setTimezone(new DateTimeZone('Asia/Jakarta'));

    return $date->format('d-m-Y H:i:s');
}

	
echo "============================================================================\n";
echo "1. Complete all task\n";
echo "2. Claim task\n";
echo "3. Claim balance\n";
echo "4. Check User\n";
echo "============================================================================\n";
$pilih = readline("Masukan Pilihan : ");
    $data = file("data.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($data === false) {
        echo "Error reading data.txt\n";
		exit();
    }
    foreach ($data as $acc => $datax) {
		$acc = $acc+1;
	$headers = [
    "accept: application/json, text/plain, */*",
    "authorization: tma ".$datax,
    "origin: https://wallet.spell.club",
    "referer: https://wallet.spell.club/"
	];
	if($pilih == 1){
			$getId = getCoday("https://wapi.spell.club/quest/1", $headers);
			$jsI = json_decode($getId, true);
			$ids = [];
			foreach ($jsI['steps'] as $step) {
				if ($step['is_passed'] == null) {
					$ids[] = $step['id'];
				}
				foreach ($ids as $id) {
					$cmplte = postCoday("https://wapi.spell.club/quest/step/".$id."/complete", $headers);
					echo "\033[32m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Task:".$id." - Complete\033[0m\n";
				}
		}
	}else if($pilih == 2){
		$jsClaim = json_decode(postCoday("https://wapi.spell.club/quest/1/claim?batch_mode=true",$headers),true);
		if($jsClaim['id']){
		echo "\033[32m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Success claim: ".$jsClaim['id']."\033[0m\n";		
		}else{
		echo "\033[31m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Message: ".$jsClaim['message']."\033[0m\n";		
		}
	}else if($pilih == 3){
			$jsClaim = json_decode(postCoday("https://wapi.spell.club/claim?batch_mode=true",$headers),true);
				if($jsClaim['id']){
				echo "\033[32m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Success claim: ".$jsClaim['id']."\033[0m\n";		
				sleep(10);
				}else{
				echo "\033[31m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Message: ".$jsClaim['message']."\033[0m\n";		
				}
	}else if ($pilih == 4){
	$jsUser = json_decode(getCoday("https://wapi.spell.club/user",$headers),true);
			echo "\033[34m[".tanggal(round(microtime(true) * 1000))."] Account $acc: Address ".$jsUser['address'].", Balance ".number_format($jsUser['balance'] / 1000000, 2, '.', '').", Referral ".$jsUser['invited_users']." \033[0m\n";
	}else{
		echo 'pilih yg bener';
		exit();
	}
}