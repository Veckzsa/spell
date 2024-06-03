<?php
/* Bot spell by @RiyanCoday */
error_reporting(0);
function getUser($headers){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://wapi.spell.club/user");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
return curl_exec($ch);
curl_close($ch);
}
function claimBalance($headers){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://wapi.spell.club/claim?batch_mode=true");
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
while(true){
    $data = file("data.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($data === false) {
        echo "Error reading data.txt\n";
		exit();
    }
    foreach ($data as $acc => $datax) {
	$acc = $acc + 1;
		$headers = [
		"accept: application/json, text/plain, */*",
		"authorization: tma ".$datax,
		"origin: https://wallet.spell.club",
		"referer: https://wallet.spell.club/"
		];
		$jsUser = json_decode(getUser($headers),true);
		echo "\033[34m[".tanggal(round(microtime(true) * 1000))."] Account $acc: Address ".$jsUser['address'].", Balance ".number_format($jsUser['balance'] / 1000000, 2, '.', '').", Referral ".$jsUser['invited_users']." \033[0m\n";
		$jsClaim = json_decode(claimBalance($headers),true);
			if($jsClaim['id']){
			echo "\033[32m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Success claim: ".$jsClaim['id']."\033[0m\n";	
			sleep(10);			
			}else{
			echo "\033[31m[".tanggal(round(microtime(true) * 1000))."] Account $acc => Message: ".$jsClaim['message']."\033[0m\n";		
			}
	}
	echo "\033[35m======== Please wait 5minutes ========\033[0m\n";
	sleep(300); //please wait 5menit
}