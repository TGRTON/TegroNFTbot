<?php 
include "config.php";

$data = file_get_contents('php://input');
$data = json_decode($data, true);
 
if (empty($data['message']['chat']['id']) AND empty($data['callback_query']['message']['chat']['id']))
{
	#exit();
}

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

#################################

if (isset($data['message']['chat']['id']))
{
	$chat_id = $data['message']['chat']['id'];
}
elseif (isset($data['callback_query']['message']['chat']['id']))
{
	$chat_id = $data['callback_query']['message']['chat']['id'];
}
elseif(isset($data['inline_query']['from']['id']))
{
	$chat_id = $data['inline_query']['from']['id'];
}

// Register new user in DB
if(isset($data['callback_query']['message']['chat']['username']) && $data['callback_query']['message']['chat']['username'] != ''){
	$fname = $data['callback_query']['message']['chat']['first_name'];
	$lname = $data['callback_query']['message']['chat']['last_name'];
	$uname = $data['callback_query']['message']['chat']['username'];
} else{
	$fname = $data['message']['from']['first_name'];
	$lname = $data['message']['from']['last_name'];
	$uname = $data['message']['from']['username'];	
}
$time = time();
if($chat_id != ''){
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result) == 0){
		$str2ins = "INSERT INTO `users` (`chatid`,`fname`,`lname`,`username`) VALUES ('$chat_id','".addslashes($fname)."','".addslashes($lname)."','$uname')";
		mysqli_query($link, $str2ins);	
		$result = mysqli_query($link, $str2select);
	}
	$row = @mysqli_fetch_object($result);	
}
// Register new user in DB

// LANGUAGE
$str3select = "SELECT `lang` FROM `users` WHERE `chatid`='$chat_id'";
$result3 = mysqli_query($link, $str3select);
$row3 = @mysqli_fetch_object($result3);
if($row3->lang != ''){
	$langcode = $row3->lang;
}else{
	$langcode = 0;	
}
###################
$langcode = langCode($langcode);
###################
require "lang.php";
for ($i = 0; $i < count($text); $i++) {
	for ($k = 0; $k < count($text[$i]); $k++) {
		$text[$i][$k] = str_replace("&#13;&#10;", "
", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#9;", "", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#60;", "<", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#62;", ">", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#39;", "'", $text[$i][$k]);
		$text[$i][$k] = str_replace("", "", $text[$i][$k]);						
	} // end FOR
} // end FOR
// LANGUAGE

checkInlineQuery();

############### START ###############
if( preg_match("/\/start/i", $data['message']['text'] )){

//register subscriber
$newrecord = $chat_id."|".addslashes($data['message']['from']['first_name'])." ".addslashes($data['message']['from']['last_name'])."|".addslashes($data['message']['from']['username']);
if(file_exists('subscribers.php')) include 'subscribers.php';
if(isset($user) && count($user) > 0){
	if(!in_array($newrecord, $user)){
		$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
		
	}
}else{
	$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
} // end IF-ELSE count($user) > 0

if(isset($towrite) && $towrite != ''){
	if($file = fopen("subscribers.php", "a+")){
		fputs($file,$towrite);
		fclose($file);
	} // end frite to file
}
//register subscriber

// record referral
$ref = trim(str_replace("/start", "", $data['message']['text']));
if($ref != ''){
	if($ref != $chat_id){
		$str2select = "SELECT `ref` FROM `users` WHERE `chatid`='$chat_id'";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);
		if($row->ref < 10){
			$str2upd = "UPDATE `users` SET `ref`='$ref' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			$reftxt = str_replace("%ref%", $ref, $text[$langcode][7]);
			
			$response = array(
					'chat_id' => $ref,
					'text' => hex2bin('F09F92B0').' '.$data['message']['from']['first_name'].' '.$data['message']['from']['last_name'].$reftxt);
			sendit($response, 'sendMessage');			
		}
	}
}
// record referral

#mainMenu();
chooseLang();

}
elseif( preg_match("/8Seu8SwemYdn6SmdYdf/", $data['message']['text'] )){
	
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $chat_id,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');	
	
}
elseif( preg_match("/".$text[$langcode][0]."/", $data['message']['text'] )){

	//Buy NFT
		clean_temp_sess();
		$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','walletfor_nft|cat')";
		mysqli_query($link, $str4ins);		

		$arInfo["inline_keyboard"][0][0]["callback_data"] = "buy";
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][29];
		$arInfo["inline_keyboard"][0][1]["callback_data"] = "win";
		$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][30]; 
		send($chat_id, $text[$langcode][28], $arInfo); 

}
elseif( preg_match("/".$text[$langcode][1]."/", $data['message']['text'] )){
	
	//Buy NFT
		clean_temp_sess();
		$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','walletfor_nft|dog')";
		mysqli_query($link, $str4ins);		

		$arInfo["inline_keyboard"][0][0]["callback_data"] = "buy";
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][29];
		$arInfo["inline_keyboard"][0][1]["callback_data"] = "win";
		$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][30]; 
		send($chat_id, $text[$langcode][28], $arInfo); 	

}
elseif( preg_match("/".$text[$langcode][2]."/", $data['message']['text'] )){

	$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$numOfReferals = mysqli_num_rows($result12);
	
	$refbalance = ($row->refbalance > 0) ? $row->refbalance : "0.00";
	
	$tomessage = str_replace("%NFTRefPercent%", $NFTRefPercent, $text[$langcode][9]);
	$tomessage = str_replace("%numOfReferals%", $numOfReferals, $tomessage);
	$tomessage = str_replace("%refbalance%", $refbalance, $tomessage);		
	$tomessage = str_replace("%chat_id%", $chat_id, $tomessage);			
	
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $tomessage,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
		
		send2('sendMessage',
			[
				'chat_id' => $chat_id,
				'text' => $text[$langcode][53],
				'reply_markup' =>
				[
					'inline_keyboard' =>
					[
						[
							[
								'text' => $text[$langcode][54],
								'switch_inline_query' => ''
							]
						]
					]
				]
			]);	
	
}
elseif( preg_match("/".$text[$langcode][3]."/", $data['message']['text'] )){

	$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$numOfReferals = mysqli_num_rows($result12);

	$tomessage = str_replace("%numOfReferals%", $numOfReferals, $text[$langcode][10]);
	$tomessage = str_replace("%chat_id%", $chat_id, $tomessage);
	if($row->verified == 1){
		
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $tomessage,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
		
	}else{

		$url = 'https://t.me/TonNFTcat';
		$arInfo["inline_keyboard"][0][0]["text"] = "TON NFT Tegro Cat 🐈";
		$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);	
		$url2 = 'https://t.me/TonNFTdog';
		$arInfo["inline_keyboard"][1][0]["text"] = "TON NFT Tegro Dog 🐕";
		$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url2);	
		$arInfo["inline_keyboard"][2][0]["callback_data"] = 1;
		$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][11]." ✅";		
		send($chat_id, $tomessage, $arInfo); 
	}
}
elseif( preg_match("/".$text[$langcode][4]."/", $data['message']['text'] )){

		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][12],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
	
}
elseif( preg_match("/".$text[$langcode][5]."/", $data['message']['text'] )){			

		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][13],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
		
		chooseLang();

}
elseif( preg_match("/".$text[$langcode][59]."/", $data['message']['text'] )){
	
	subMenu();

}
elseif( preg_match("/Mystery Box/", $data['message']['text'] )){

	chooseCase2();

}
elseif( preg_match("/".$text[$langcode][26]."/", $data['message']['text'] )){
	
	mainMenu();
	
}
elseif( preg_match("/Blogger NFT|Blogger 3D|NFT Nude/", $data['message']['text'] )){
	
	if(preg_match("/Blogger NFT/", $data['message']['text'] )){
		processWallet("blogger");
	}
	elseif(preg_match("/Blogger 3D/", $data['message']['text'] )){
		processWallet("custom");
	}
	elseif(preg_match("/NFT Nude/", $data['message']['text'] )){
		processWallet("nude");
	}	
		
}
else{
	if(isset($data['callback_query']['data']) && $data['callback_query']['data'] != ''){

		if( preg_match("/checkpay4nft/", $data['callback_query']['data']) ){	

			// Check payment for NFT
			$senderid = str_replace("checkpay4nft", "", $data['callback_query']['data']);
			$parts = explode("|", $senderid);
			$senderid = $parts[0];
			$nfttype = $parts[1];

			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			#curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));
						
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$str17select = "SELECT `nftcode` FROM `users` WHERE `chatid`='$chat_id'";
			$result17 = mysqli_query($link, $str17select);
			$row17 = @mysqli_fetch_object($result17);
			
			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $NFTwallet && $res["result"][$i]["out_msgs"][0]["message"] == $row17->nftcode){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
						$nftcodeORIG = $row17->nftcode;						
				}
			} // end FOR
			
			if($verified == 1){
				
				#clean_temp_sess();
				delMessage("", $data['callback_query']['message']['message_id']);
				
				$nftcode = rand_string(25);
				$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);				
				
				if($nfttype == "cat") {$rate = $nftCatRate;}
				elseif($nfttype == "dog") {$rate = $nftDogRate;}
				
				$ssum = $paidSumForNFT/$rate;
				$gotNFT = number_format($ssum, 2, '.', ''); 
				
				$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result16 = mysqli_query($link, $str16select);
				if(mysqli_num_rows($result16) == 0){
					$str2ins = "INSERT INTO `nft` (`chatid`,`".$nfttype."`) VALUES ('$chat_id','$gotNFT')";
					mysqli_query($link, $str2ins);
				}else{
					$row16 = @mysqli_fetch_object($result16);
					if($nfttype == "cat"){
						$oldsum = $row16->cat;
					}elseif($nfttype == "dog"){
						$oldsum = $row16->dog;	
					}
					$newsum = $oldsum + $gotNFT;					
					$str11upd = "UPDATE `nft` SET `".$nfttype."`='".$newsum."' WHERE `chatid`='$chat_id'";
					mysqli_query($link, $str11upd);
									
				}
				clean_temp_sess();
				
				########## REF FEE ##########
				$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
				$result12 = mysqli_query($link, $str12select);
				$row12 = @mysqli_fetch_object($result12);	
				
				$earnRefNFT = $gotNFT / 100 * $NFTRefPercent * $rate;
				
				if($row12->ref > 1){
					$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earnRefNFT WHERE `chatid`='".$row12->ref."'";
					mysqli_query($link, $str10upd);	
				}
				########## REF FEE ##########		
				
				$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result16 = mysqli_query($link, $str16select);
				$row16 = @mysqli_fetch_object($result16);
				
				$tomessage = str_replace("%gotNFT%", $gotNFT, $text[$langcode][14]);
				$tomessage = str_replace("%nft_balance%", $row16->nft_balance, $tomessage);
				$tomessage = str_replace("%nfttype%", $nfttype, $tomessage);				
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				######## SAVE TRANSACTION ###########
				if($nfttype == "cat"){
					$cat = $gotNFT;
					$dog = 0;
				}elseif($nfttype == "dog"){
					$cat = 0;
					$dog = $gotNFT;					
				}
				$date_time = date("j-m-Y G:i");
				$str2ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`nftcat`,`nftdog`) VALUES ('$chat_id','$senderid','$date_time','$cat','$dog')";
				mysqli_query($link, $str2ins);
				######## SAVE TRANSACTION ###########											
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][15],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}		
			// Check payment for NFT	

		}
		elseif( $data['callback_query']['data'] > 99  && $data['callback_query']['data'] < 103){
			
			$langcode = $data['callback_query']['data'] - 100;
		
			$str2upd = "UPDATE `users` SET `lang`='".$langcode."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			###################
			$langcode = langCode($langcode);
			###################
			
			mainMenu();
		}
		elseif($data['callback_query']['data'] == "buy"){

			$response = array(
				'chat_id' => $chat_id, 
				'text' => $text[$langcode][8],
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');
			
		}
		elseif($data['callback_query']['data'] == "win"){
			
			chooseCase();			

		}
		elseif($data['callback_query']['data'] > 13 && $data['callback_query']['data'] < 18){
		
			if($data['callback_query']['data'] == 14){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][31],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}
			elseif($data['callback_query']['data'] == 15){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][32],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}
			elseif($data['callback_query']['data'] == 16){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][33],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}								
			elseif($data['callback_query']['data'] == 17){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][34],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}					
		
			processWallet($data['callback_query']['data']);

		}
		elseif( $data['callback_query']['data']  == 3){
		
			addSum();
			#choosePayMethod();
			
		}
		elseif( $data['callback_query']['data']  == 4){			
		
			processWallet2();

		}
		elseif( $data['callback_query']['data']  == 33){
		
			#addSum();
			choosePayMethodWin();

		}
		elseif( $data['callback_query']['data'] == 150){

			$str22select = "SELECT `ccase` FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
			$result22 = mysqli_query($link, $str22select);
			$row22 = @mysqli_fetch_object($result22);			
			$case = $row22->ccase;	
			
			switch ($case) {
				case 14:
				$sum = 10;
				break;
				case 15:
				$sum = 20;
				break;
				case 16:
				$sum = 30;
				break;
				case 17:
				$sum = 40;
				break;						
			}

			$paylink = makelink($sum, $case);
			
			$tomessage = str_replace("%sumtopay%", $sum, $text[$langcode][45]);				
			$url = $paylink;
			$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3').$tomessage;
			$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
			send($chat_id, $text[$langcode][46], $arInfo);	
				
		}
		elseif( $data['callback_query']['data'] == 151){						

			messageIfPayByTON();

		}
		elseif( $data['callback_query']['data'] == 160){

			$str22select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
			$result22 = mysqli_query($link, $str22select);
			$row22 = @mysqli_fetch_object($result22);
			
			if($row22->ccase != 0){
				$case = $row22->ccase;	
				
				switch ($case) {
					case 114:
					$sum = 5;
					break;
					case 115:
					$sum = 15;
					break;
					case 116:
					$sum = 25;
					break;
					case 117:
					$sum = 45;
					break;						
				}
	
				$paylink = makelink($sum, $case);
				
				$tomessage = str_replace("%sumtopay%", $sum, $text[$langcode][63]);				
				$url = $paylink;
				$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3').$tomessage;
				$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
				send($chat_id, $text[$langcode][64], $arInfo);	
									
			}else{
				$coin = $row22->coin;	
				
				$str23select = "SELECT `sum` FROM `sums` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
				$result23 = mysqli_query($link, $str23select);
				$row23 = @mysqli_fetch_object($result23);			
				$sumnft = $row23->sum;				
				
				if($coin == "blogger"){
					$sum = $sumnft * $BloggerNFT;
				}
				elseif($coin == "custom"){
					$sum = $sumnft * $Blogger3D;
				}
				elseif($coin == "nude"){
					$sum = $sumnft * $NFTNude;									
				}
	
				$paylink = makelink2($sum, $coin);
				
				$tomessage = str_replace("%sumtopay%", $data['message']['text'], $text[$langcode][63]);				
				$url = $paylink;
				$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3').$tomessage;
				$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
				send($chat_id, $text[$langcode][64], $arInfo);	
			}
				
		}
		elseif( $data['callback_query']['data'] == 161){						

			messageIfPayByTON2();
			
		}
		elseif( $data['callback_query']['data'] == 171){						

			messageIfPayByTONWin();					
							
		}
		elseif($data['callback_query']['data'] == 251){
			
			delMessage("", $data['callback_query']['message']['message_id']);			
			$arInfo["inline_keyboard"][0][0]["callback_data"] = "buy";
			$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][29];
			$arInfo["inline_keyboard"][0][1]["callback_data"] = "win";
			$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][30]; 
			send($chat_id, $text[$langcode][28], $arInfo); 			

		}
		elseif($data['callback_query']['data'] == 351){
			
			mainMenu();

		}
		elseif( preg_match("/chkp/", $data['callback_query']['data']) ){	
			
			// Check payment for NFT
			$senderid = str_replace("chkp", "", $data['callback_query']['data']);
			$parts = explode("|", $senderid);
			$senderid = $parts[0];
			$nfttype = $parts[1];

			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			#curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$str17select = "SELECT `nftcode` FROM `users` WHERE `chatid`='$chat_id'";
			$result17 = mysqli_query($link, $str17select);
			$row17 = @mysqli_fetch_object($result17);
			
			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $NFTwallet && $res["result"][$i]["out_msgs"][0]["message"] == $row17->nftcode){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
						$nftcodeORIG = $row17->nftcode;						
				}
			} // end FOR
			
			if($verified == 1){
				
				#clean_temp_sess();
				delMessage("", $data['callback_query']['message']['message_id']);
				
				$nftcode = rand_string(20);
				$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);				
				
				if($nfttype == "blogger") {$rate = $BloggerNFT;}
				elseif($nfttype == "custom") {$rate = $Blogger3D;}
				elseif($nfttype == "nude") {$rate = $NFTNude;}
				
				$ssum = $paidSumForNFT/$rate;
				$gotNFT = number_format($ssum, 2, '.', ''); 
				
				$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result16 = mysqli_query($link, $str16select);
				if(mysqli_num_rows($result16) == 0){
					if($nfttype == "blogger"){
						$nfttype2 = "blogger";
					}elseif($nfttype == "custom"){
						$nfttype2 = "custom3d";						
					}elseif($nfttype == "nude"){
						$nfttype2 = "nude";														
					}					
					$str2ins = "INSERT INTO `nft` (`chatid`,`".$nfttype2."`) VALUES ('$chat_id','$gotNFT')";
					mysqli_query($link, $str2ins);
				}else{
					$row16 = @mysqli_fetch_object($result16);
					if($nfttype == "blogger"){
						$oldsum = $row16->blogger;
						$nfttype2 = "blogger";
					}elseif($nfttype == "custom"){
						$oldsum = $row16->custom3d;
						$nfttype2 = "custom3d";						
					}elseif($nfttype == "nude"){
						$oldsum = $row16->nude;	
						$nfttype2 = "nude";														
					}
					
										$newsum = $oldsum + $gotNFT;					
					$str11upd = "UPDATE `nft` SET `".$nfttype2."`='".$newsum."' WHERE `chatid`='$chat_id'";
					mysqli_query($link, $str11upd);
									
				}
				clean_temp_sess();
				
				########## REF FEE ##########
				$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
				$result12 = mysqli_query($link, $str12select);
				$row12 = @mysqli_fetch_object($result12);	
				
				$earnRefNFT = $gotNFT / 100 * $NFTToncoinRefPercent * $rate;
				
				if($row12->ref > 1){
					$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earnRefNFT WHERE `chatid`='".$row12->ref."'";
					mysqli_query($link, $str10upd);	
				}
				########## REF FEE ##########		
				
				$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result16 = mysqli_query($link, $str16select);
				$row16 = @mysqli_fetch_object($result16);
				
				$tomessage = str_replace("%gotNFT%", $gotNFT, $text[$langcode][14]);
				$tomessage = str_replace("%nft_balance%", $row16->nft_balance, $tomessage);
				$tomessage = str_replace("%nfttype%", $nfttype, $tomessage);				
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				######## SAVE TRANSACTION ###########
				if($nfttype == "blogger"){
					$cat = $gotNFT;
					$dog = 0;
					$nude = 0;
				}elseif($nfttype == "custom"){
					$cat = 0;
					$dog = $gotNFT;	
					$nude = 0;					
				}elseif($nfttype == "nude"){
					$cat = 0;
					$dog = 0;										
					$nude = $gotNFT;					
				}
				$date_time = date("j-m-Y G:i");
				$str2ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`blogger`,`custom`,`nude`) VALUES ('$chat_id','$senderid','$date_time','$cat','$dog','$nude')";
				mysqli_query($link, $str2ins);
				######## SAVE TRANSACTION ###########											
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][51],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}		
			// Check payment for NFT	
		}
		elseif( preg_match("/chw/", $data['callback_query']['data']) ){	

			// Check payment for NFT
			$senderid = str_replace("chw", "", $data['callback_query']['data']);
			$parts = explode("|", $senderid);
			$senderid = $parts[0];
			$sum = $parts[1];

			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$str17select = "SELECT `nftcode` FROM `users` WHERE `chatid`='$chat_id'";
			$result17 = mysqli_query($link, $str17select);
			$row17 = @mysqli_fetch_object($result17);
			
			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $NFTwallet && $res["result"][$i]["out_msgs"][0]["message"] == $row17->nftcode){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
						$nftcodeORIG = $row17->nftcode;						
				}
			} // end FOR
			
			if($verified == 1){
				
				// sum validation
				$nc = explode(";", $row17->nftcode);
				switch ($nc[0]) {
					case "silver":
					$storedsum = 5;
					$case = 114;
					break;
					case "gold":
					$storedsum = 15;
					$case = 115;					
					break;
					case "platinum":
					$storedsum = 25;
					$case = 116;					
					break;
					case "diamond":
					$storedsum = 45;
					$case = 117;					
					break;						
				}	
				
				if($storedsum != (int)$paidSumForNFT){
				
					$response = array(
						'chat_id' => $chat_id, 
						'text' => $text[$langcode][50],
						'parse_mode' => 'HTML');	
					sendit($response, 'sendMessage');

				}else{
				
					#clean_temp_sess();
					delMessage("", $data['callback_query']['message']['message_id']);
					
					$nftcode = rand_string(20);
					$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
					mysqli_query($link, $str2upd);				
					
					$gotTON = (int)$paidSumForNFT;
					
					include "roulette.php";
					roulette($case, $gotTON, $senderid);
				}
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][51],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');	
			}

		}
		elseif($data['callback_query']['data'] > 113 && $data['callback_query']['data'] < 118){
		
			if($data['callback_query']['data'] == 114){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][74],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}
			elseif($data['callback_query']['data'] == 115){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][75],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}
			elseif($data['callback_query']['data'] == 116){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][76],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}								
			elseif($data['callback_query']['data'] == 117){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][77],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}					
		
			processWallet($data['callback_query']['data']);
						
		}
		elseif($data['callback_query']['data'] == 1){
			
			$channel_id1 = "@TonNFTcat";
			$channel_id2 = "@TonNFTdog";
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id1, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			$res = json_decode($res, true);
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id2, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res2 = curl_exec($ch);
			curl_close($ch);
			$res2 = json_decode($res2, true);			
			
			if ($res['ok'] == true && $res['result']['status'] != "left" && $res2['ok'] == true && $res2['result']['status'] != "left") {
		
				$str2upd = "UPDATE `users` SET `verified`='1' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "👍 ".$text[$langcode][16],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');					
		
			}
			elseif($res['result']['status'] == "left"){
		
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => $text[$langcode][17], 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
		
			} else {
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => $text[$langcode][17], 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			
			}		
		
		}

	}else{

		if(!isset($data['inline_query']['from']['id'])){

		$str5select = "SELECT `action` FROM `temp_sess` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
		$result5 = mysqli_query($link, $str5select);
		$row5 = @mysqli_fetch_object($result5);

		if(preg_match("/wait4wallet/", $row5->action)){

			if(strlen(trim($data['message']['text'])) < 20){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][43],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
				
			}else{
			
			//Wallet verify
			$walletno = trim($data['message']['text']);
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][44],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$str2upd = "UPDATE `users` SET `wallet`='$walletno' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);	
				
				#choosePayMethod2();
				addSum();
			
			}
		
		}

		}
		elseif(preg_match("/wait4sum/", $row5->action)){
			
			$sum = trim($data['message']['text']);
			if(preg_match("/^[0-9]+$/", $sum)){
			
				choosePayMethod2($sum);			
			
			}else{
				$response = array(
					'chat_id' => $chat_id,
					'text' => "❌ ".$text[$langcode][62]);
				sendit($response, 'sendMessage');				
			}				
		}
		elseif(preg_match("/walletfor_nft/", $row5->action)){	
			
			$walletno = trim($data['message']['text']);
			

			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);
			
			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][18],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$nfttype = str_replace("walletfor_nft|", "", $row5->action);
				
				$str15select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result15 = mysqli_query($link, $str15select);
				if(mysqli_num_rows($result15) == 0){
					$nftbalance = 0;
					$nftcat = 0;
					$nftdog = 0;
				}else{
					$row15 = @mysqli_fetch_object($result15);
					$nftbalance = $row15->nft_balance;
					$nftcat = $row15->cat;
					$nftdog = $row15->dog;					
				}
				
				
				$nftcode = $nfttype.";".rand_string(25);
				$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);				
				
				if($nfttype == "cat"){

				$tomessage = str_replace("%nftCatRate%", $nftCatRate, $text[$langcode][19]);
				$tomessage = str_replace("%nftcat%", $nftcat, $tomessage);

				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "<code>".$nftcode."</code>",
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');

				$tomessage = str_replace("%nftCatRate%", $nftCatRate, $text[$langcode][20]);
				$tomessage = str_replace("%2nftCatRate%", $nftCatRate*2, $tomessage);									
				$tomessage = str_replace("%3nftCatRate%", $nftCatRate*3, $tomessage);									
				$tomessage = str_replace("%10nftCatRate%", $nftCatRate*10, $tomessage);																	
				$coins = $tomessage;
				}
				elseif($nfttype == "dog"){
					
				$tomessage = str_replace("%nftDogRate%", $nftDogRate, $text[$langcode][21]);
				$tomessage = str_replace("%nftdog%", $nftdog, $tomessage);					
					
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "<code>".$nftcode."</code>",
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');					
					
				$tomessage = str_replace("%nftDogRate%", $nftDogRate, $text[$langcode][22]);
				$tomessage = str_replace("%2nftDogRate%", $nftDogRate*2, $tomessage);									
				$tomessage = str_replace("%3nftDogRate%", $nftDogRate*3, $tomessage);									
				$tomessage = str_replace("%10nftDogRate%", $nftDogRate*10, $tomessage);																	
				$coins = $tomessage;
				}
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $coins,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "<code>".$NFTwallet."</code>",
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');	
				
				$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][24]);
				$tomessage = str_replace("%nftcode%", $nftcode, $tomessage);				
				
				$arInfo["inline_keyboard"][0][0]["callback_data"] = "checkpay4nft".$walletno."|".$nfttype;
				$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][23];
				send($chat_id, $tomessage, $arInfo); 									
				
			}			
			
		}
			
		}
	}

} // if-else /start
 
exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл

function sendit($response, $restype){
	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/'.$restype);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_exec($ch);
	curl_close($ch);	
}

function send($id, $message, $keyboard) {   
		
		//Удаление клавы
		if($keyboard == "DEL"){		
			$keyboard = array(
				'remove_keyboard' => true
			);
		}
		if($keyboard){
			//Отправка клавиатуры
			$encodedMarkup = json_encode($keyboard);
			
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'reply_markup' => $encodedMarkup,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True
			);
		}else{
			//Отправка сообщения
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True				
			);
		}
       
        $out = sendit($data, 'sendMessage');       
        return $out;
}     

function mainMenu(){
	global $chat_id, $link, $langcode, $text;
	
	$str15select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result15 = mysqli_query($link, $str15select);
	if(mysqli_num_rows($result15) == 0){
		$nftbalance = 0;
		$nftcat = 0;
		$nftdog = 0;		
	}else{
		$row15 = @mysqli_fetch_object($result15);
		$nftbalance = $row15->nft_balance;
		$nftcat = $row15->cat;
		$nftdog = $row15->dog;				
	}	
	
	$toButton = str_replace("%nftdog%", $nftdog, $text[$langcode][6]);
	$toButton = str_replace("%nftcat%", $nftcat, $toButton);
	$toButton = str_replace("%chat_id%", $chat_id, $toButton);	
	
	$arInfo["keyboard"][0][0]["text"] = "👸 ".$text[$langcode][59];
	$arInfo["keyboard"][0][1]["text"] = "🧚‍♀️ Mystery Box";	
	$arInfo["keyboard"][1][0]["text"] = "🎁 ".$text[$langcode][2];
	$arInfo["keyboard"][1][1]["text"] = "🔥 ".$text[$langcode][3];
	$arInfo["keyboard"][2][0]["text"] = "🏵 ".$text[$langcode][4];
	$arInfo["keyboard"][2][1]["text"] = "📝 ".$text[$langcode][5];		
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $toButton.'👇', $arInfo); 	
}

function clean_temp_sess(){
	global $chat_id, $link;
	
	$str2del = "DELETE FROM `temp_sess` WHERE `chatid` = '$chat_id'";
	mysqli_query($link, $str2del);
}

function rand_string( $length ) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

}

function delMessage($mid, $cid){
	global $chat_id;
		if($mid != ''){
			$message_id = $mid-1;
		}
		elseif($cid != ''){
			$message_id = $cid;
		}

		$ch2 = curl_init('https://api.telegram.org/bot' . TOKEN . '/deleteMessage');  
		curl_setopt($ch2, CURLOPT_POST, 1);  
		curl_setopt($ch2, CURLOPT_POSTFIELDS, array('chat_id' => $chat_id, 'message_id' => $message_id));
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_HEADER, false);
		$res2 = curl_exec($ch2);
		curl_close($ch2);		
}

function langCode($langcode){
	if($langcode > 12) $langcode = 0;
	return $langcode;
}

function chooseLang(){
	global $chat_id, $link, $langcode, $text, $lang;
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 100;
	$arInfo["inline_keyboard"][0][0]["text"] = $lang[0];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 101;
	$arInfo["inline_keyboard"][0][1]["text"] = $lang[1]; 
	$arInfo["inline_keyboard"][0][2]["callback_data"] = 102;
	$arInfo["inline_keyboard"][0][2]["text"] = $lang[2]; 
	send($chat_id, hex2bin('F09F92AD')." ".$text[$langcode][25], $arInfo); 	
}

function chooseCase(){
	global $chat_id, $langcode, $text;	
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 14;
	$arInfo["inline_keyboard"][0][0]["text"] = "🪙 Silver (10 TON)";
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 16;
	$arInfo["inline_keyboard"][0][1]["text"] = "💍 Platinum (30 TON)"; 
	$arInfo["inline_keyboard"][1][0]["callback_data"] = 15;
	$arInfo["inline_keyboard"][1][0]["text"] = "⚜️ Gold (20 TON)"; 
	$arInfo["inline_keyboard"][1][1]["callback_data"] = 17;
	$arInfo["inline_keyboard"][1][1]["text"] = "💠 Diamond (40 TON)"; 	
	$arInfo["inline_keyboard"][2][0]["callback_data"] = 251;
	$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][26]; 			
	send($chat_id, $text[$langcode][27], $arInfo); 	
}

function processWallet($coin){
	global $chat_id, $link, $langcode, $text;

	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if (preg_match("/^([0-9])+$/", $coin)) {                                                

			if($coin != ""){
				$str2del = "DELETE FROM `temp_coin` WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2del);
				$str2ins = "INSERT INTO `temp_coin` (`chatid`,`ccase`) VALUES ('$chat_id','$coin')";
				mysqli_query($link, $str2ins);
				
				if(strlen($row->wallet) > 10){
					$toButton = str_replace("%walletno%", $row->wallet, $text[$langcode][35]);	
					
					$arInfo["inline_keyboard"][0][0]["callback_data"] = 33;
					$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][36];
					$arInfo["inline_keyboard"][0][1]["callback_data"] = 4;
					$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][37];				
					send($chat_id, $toButton, $arInfo);
							
				}else{
					
					processWallet2();
					
				}					
			}
			
		}else{
	
			if($coin != ""){
				$str2del = "DELETE FROM `temp_coin` WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2del);
				$str2ins = "INSERT INTO `temp_coin` (`chatid`,`coin`) VALUES ('$chat_id','$coin')";
				mysqli_query($link, $str2ins);
				
				if(strlen($row->wallet) > 10){
					$toButton = str_replace("%walletno%", $row->wallet, $text[$langcode][35]);	
					
					$arInfo["inline_keyboard"][0][0]["callback_data"] = 3;
					$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][36];
					$arInfo["inline_keyboard"][0][1]["callback_data"] = 4;
					$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][37];				
					send($chat_id, $toButton, $arInfo);
							
				}else{
					
					processWallet2();
					
				}					
				
			}
		
	}

}

function processWallet2(){
	global $chat_id, $link, $langcode, $text;

	clean_temp_sess();
	save2temp("action", "wait4wallet");
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][42],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	

}

/*function processWallet($case){
	global $chat_id, $link, $langcode, $text;
	
	if($case != ""){
		$str2del = "DELETE FROM `temp_coin` WHERE `chatid`='$chat_id'";
		mysqli_query($link, $str2del);
		$str2ins = "INSERT INTO `temp_coin` (`chatid`,`ccase`) VALUES ('$chat_id','$case')";
		mysqli_query($link, $str2ins);
	}
	
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if(strlen($row->wallet) > 10){
		$toButton = str_replace("%walletno%", $row->wallet, $text[$langcode][35]);	
		
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 3;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][36];
		$arInfo["inline_keyboard"][0][1]["callback_data"] = 4;
		$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][37];				
		send($chat_id, $toButton, $arInfo);
		 		
	}else{
		
		processWallet2();
		
	}
	
}*/

/*function processWallet2(){
	global $chat_id, $link, $langcode, $text;

	clean_temp_sess();
	save2temp("action", "wait4wallet");
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][42],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	

}*/

function save2temp($field, $val){
	global $link, $chat_id;
	$curtime = time();
	
	$str2ins = "INSERT INTO `temp_sess` (`chatid`,`$field`) VALUES ('$chat_id','$val')";
	mysqli_query($link, $str2ins);	

}

function choosePayMethod(){
	global $chat_id, $link, $langcode, $text,$CryptoPayAPIToken;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$case = $row->ccase;
	
################# PREPARE FOR CRYPTO BOT #######################
	switch ($case) {
		case 14:
		$sum = 10;
		break;
		case 15:
		$sum = 20;
		break;
		case 16:
		$sum = 30;
		break;
		case 17:
		$sum = 40;
		break;						
	}
	
	$ctime = time();
	$payload = $chat_id.":".$case;
	$data = array("asset"=>"TON", "amount"=>$sum, "payload"=>$payload, "paid_btn_name"=>"callback", "paid_btn_url"=>"https://t.me/TegroNFTbot");
	
	$prop = http_build_query($data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://pay.crypt.bot/api/createInvoice?".$prop);
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'Crypto-Pay-API-Token: '.$CryptoPayAPIToken));
	
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	$res = json_decode($server_output, true);		
################# PREPARE FOR CRYPTO BOT #######################
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 150;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][38];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 151;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][39]; 
	$url22 = $res['result']['pay_url'];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url22);	
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][40]; 	
	send($chat_id, $text[$langcode][41], $arInfo); 	
}

function makelink($sum, $case){
	global $link, $chat_id, $roskassa_publickey, $roskassa_secretkey;
	
	$curtime = time();
	$str2ins = "INSERT INTO `paylinks` (`chatid`,`times`,`status`,`sum`) VALUES ('$chat_id','$curtime','0','$sum')";
	mysqli_query($link, $str2ins);
	$last_id = mysqli_insert_id($link);
	
	$secret = $roskassa_secretkey;
	$data = array(
		'shop_id'=>$roskassa_publickey,
		'amount'=>$sum,
		'currency'=>'TON',
		'order_id'=>$chat_id."|".$case
		#'test'=>1
	);
	ksort($data);
	$str = http_build_query($data);
	$sign = md5($str . $secret);
	
	return 'https://tegro.money/pay/?'.$str.'&sign='.$sign;
	
}

function messageIfPayByTON(){
	global $chat_id, $link, $langcode, $text, $NFTwallet;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$case = $row->ccase;
	
	$str20select = "SELECT `wallet` FROM `users` WHERE `chatid`='$chat_id'";
	$result20 = mysqli_query($link, $str20select);
	$row20 = @mysqli_fetch_object($result20);
	$walletno = $row20->wallet;
	
	switch ($case) {
		case 14:
		$casetext = "silver";
		break;
		case 15:
		$casetext = "gold";
		break;
		case 16:
		$casetext = "platinum";
		break;
		case 17:
		$casetext = "diamond";
		break;						
	}	
	
	$nftcode = $casetext.";".rand_string(20);
	$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);				

	switch ($case) {
		case 14:
		$sum = 10;
		$casename = "🪙 Silver";
		break;
		case 15:
		$sum = 20;
		$casename = "⚜️ Gold";
		break;
		case 16:
		$sum = 30;
		$casename = "💍 Platinum";		
		break;
		case 17:
		$sum = 40;
		$casename = "💠 Diamond";		
		break;						
	}			
	$suminnanoton = $sum * 1000000000;
	$suminton = $sum;

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][47]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%casename%", $casename, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][48]);
	$tomessage = str_replace("%nftcode%", $nftcode, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminnanoton, $tomessage);				
	
	unset($arInfo);
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "chkp".$walletno."|".$sum;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][49];
	send($chat_id, $tomessage, $arInfo); 									
				
}
function send2($method, $request)
{

	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);
	curl_setopt_array($ch,
		[
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($request),
			CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			CURLOPT_SSL_VERIFYPEER => false,
		]
	);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}
	
function uuid()
{
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}	

function checkInlineQuery()
{
	global $langcode, $text;	
	$request = json_decode(file_get_contents('php://input'));

	if (isset($request->inline_query))
	{
		
		$chatid = $request->inline_query->from->id;
		
		#file_put_contents('debug', print_r($request, true) . PHP_EOL . json_encode($request) . PHP_EOL . $result . PHP_EOL, FILE_APPEND);
		
		// https://core.telegram.org/bots/api#answerinlinequery
		send2('answerInlineQuery',
			[
				'inline_query_id' => $request->inline_query->id,

				// InlineQueryResult https://core.telegram.org/bots/api#inlinequeryresult
				'results' =>
				[
					[
						// InlineQueryResultArticle https://core.telegram.org/bots/api#inlinequeryresultarticle
						'type' => 'article',
						'id' => uuid(),
						// 'id' => 0,
						'title' => $text[$langcode][55],
						'description' => $text[$langcode][58],
						'thumb_url' => 'https://tonmarketplacebot.ru/TegroNFTbot/avatar100.jpg',

						// InputMessageContent https://core.telegram.org/bots/api#inputmessagecontent
						'input_message_content' =>
						[
							// InputTextMessageContent https://core.telegram.org/bots/api#inputtextmessagecontent
							'message_text' => $text[$langcode][56],
						],

						// InlineKeyboardMarkup https://core.telegram.org/bots/api#inlinekeyboardmarkup
						'reply_markup' =>
						[
							'inline_keyboard' =>
							[
								// InlineKeyboardButton https://core.telegram.org/bots/api#inlinekeyboardbutton
								[
									[
										'text' => $text[$langcode][57],
										'url' => 'https://t.me/TegroNFTbot?start='.$chatid,
									],
								],
							],
						],
					],
				],
			]
		);
	}
}

function subMenu(){
	global $chat_id, $langcode, $text;
	
	$arInfo["keyboard"][0][0]["text"] = "👸 Blogger NFT";
	$arInfo["keyboard"][0][1]["text"] = "👑 Blogger 3D";
	$arInfo["keyboard"][1][0]["text"] = "👩‍🎤 NFT Nude";
	$arInfo["keyboard"][1][1]["text"] = $text[$langcode][26];		
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $text[$langcode][60].'👇', $arInfo); 	
}

function addSum(){
	global $chat_id, $link, $langcode, $text, $BloggerNFT, $Blogger3D, $NFTNude;
	
	clean_temp_sess();
	save2temp("action", "wait4sum");
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if($row->coin == "blogger"){$rate = $BloggerNFT;}
	elseif($row->coin == "custom"){$rate = $Blogger3D;}	
	elseif($row->coin == "nude"){$rate = $NFTNude;}	
	
	$tomsg = str_replace("%coin%", $row->coin, $text[$langcode][61]);	
	$tomsg = str_replace("%coinrate%", $rate, $tomsg);		
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomsg,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	
	
}

function choosePayMethod2($sum){
	global $chat_id, $link, $langcode, $text, $BloggerNFT, $Blogger3D, $NFTNude;
	
	$str2del = "DELETE FROM `sums` WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2del);
	$str2ins = "INSERT INTO `sums` (`chatid`,`sum`) VALUES ('$chat_id','$sum')";
	mysqli_query($link, $str2ins);	

################# PREPARE FOR CRYPTO BOT #######################
	$str22select = "SELECT `coin` FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result22 = mysqli_query($link, $str22select);
	$row22 = @mysqli_fetch_object($result22);			
	$coin = $row22->coin;	

	if($coin == "blogger"){
		$sum = $sum * $BloggerNFT;
	}
	elseif($coin == "custom"){
		$sum = $sum * $Blogger3D;
	}
	elseif($coin == "nude"){
		$sum = $sum * $NFTNude;									
	}
	
	$ctime = time();
	$payload = $chat_id.":".$coin;
	$data = array("asset"=>"TON", "amount"=>$sum, "payload"=>$payload, "paid_btn_name"=>"callback", "paid_btn_url"=>"https://t.me/TegroNFTbot");
	
	$prop = http_build_query($data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://pay.crypt.bot/api/createInvoice?".$prop);
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'Crypto-Pay-API-Token: 17573:AAk353BqE27I2M5XKU0TjpbinOZVW5BZkW4'));
	
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	$res = json_decode($server_output, true);		
################# PREPARE FOR CRYPTO BOT #######################
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 160;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][38];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 161;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][39]; 
	$url22 = $res['result']['pay_url'];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url22);	
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][40]; 	
	send($chat_id, $text[$langcode][41], $arInfo); 	
}

function makelink2($sum, $coin){
	global $link, $chat_id, $roskassa_publickey, $roskassa_secretkey;
	
	$curtime = time();
	$str2ins = "INSERT INTO `paylinks` (`chatid`,`times`,`status`,`sum`) VALUES ('$chat_id','$curtime','0','$sum')";
	mysqli_query($link, $str2ins);
	$last_id = mysqli_insert_id($link);
	
	$secret = $roskassa_secretkey;
	$data = array(
		'shop_id'=>$roskassa_publickey,
		'amount'=>$sum,
		'currency'=>'TON',
		'order_id'=>$chat_id."|".$coin
		#'test'=>1
	);
	ksort($data);
	$str = http_build_query($data);
	$sign = md5($str . $secret);
	
	return 'https://tegro.money/pay/?'.$str.'&sign='.$sign;
	
}

function messageIfPayByTON2(){
	global $chat_id, $link, $langcode, $text, $BloggerNFT, $Blogger3D, $NFTwallet, $NFTNude;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$nfttype = $row->coin;
	
	$str20select = "SELECT `wallet` FROM `users` WHERE `chatid`='$chat_id'";
	$result20 = mysqli_query($link, $str20select);
	$row20 = @mysqli_fetch_object($result20);
	$walletno = $row20->wallet;
	
	$str15select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result15 = mysqli_query($link, $str15select);
	if(mysqli_num_rows($result15) == 0){
		$nftbalance = 0;
		$nftcat = 0;
		$nftdog = 0;
		$nftnude = 0;
	}else{
		$row15 = @mysqli_fetch_object($result15);
		$nftbalance = $row15->nft_balance;
		$nftcat = $row15->blogger;
		$nftdog = $row15->custom3d;					
		$nftnude = $row15->nude;							
	}
	
	
	$nftcode = $nfttype.";".rand_string(20);
	$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);				

	if($nfttype == "blogger") {$rate = $BloggerNFT;}
	elseif($nfttype == "custom") {$rate = $Blogger3D;}
	elseif($nfttype == "nude") {$rate = $NFTNude;}
	$str23select = "SELECT `sum` FROM `sums` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result23 = mysqli_query($link, $str23select);
	$row23 = @mysqli_fetch_object($result23);			
	$suminnanoton = $row23->sum * $rate * 1000000000;
	$suminton = $row23->sum * $rate;
	
	if($nfttype == "blogger"){

	$tomessage = str_replace("%nftCatRate%", $BloggerNFT, $text[$langcode][65]);
	$tomessage = str_replace("%nftcat%", $nftcat, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	

	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][66]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	}
	elseif($nfttype == "custom"){

	$tomessage = str_replace("%nftDogRate%", $Blogger3D, $text[$langcode][67]);
	$tomessage = str_replace("%nftdog%", $nftdog, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	

	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][68]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');
	
########		
		
	}
	elseif($nfttype == "nude"){

	$tomessage = str_replace("%nftNudeRate%", $NFTNude, $text[$langcode][69]);
	$tomessage = str_replace("%nftnude%", $nftnude, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	

	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][70]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

#######		
		
	}
	
	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][48]);
	$tomessage = str_replace("%nftcode%", $nftcode, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminnanoton, $tomessage);				
	
	unset($arInfo);
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "chkp".$walletno."|".$nfttype;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][49];
	send($chat_id, $tomessage, $arInfo); 									
				
}

function chooseCase2(){
	global $chat_id, $langcode, $text;	
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 114;
	$arInfo["inline_keyboard"][0][0]["text"] = "🪙 Silver (5 TON)";
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 116;
	$arInfo["inline_keyboard"][0][1]["text"] = "💍 Platinum (25 TON)"; 
	$arInfo["inline_keyboard"][1][0]["callback_data"] = 115;
	$arInfo["inline_keyboard"][1][0]["text"] = "⚜️ Gold (15 TON)"; 
	$arInfo["inline_keyboard"][1][1]["callback_data"] = 117;
	$arInfo["inline_keyboard"][1][1]["text"] = "💠 Diamond (45 TON)"; 	
	$arInfo["inline_keyboard"][2][0]["callback_data"] = 351;
	$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][26]; 			
	send($chat_id, $text[$langcode][73], $arInfo); 	
}

function choosePayMethodWin(){
	global $chat_id, $link, $langcode, $text,$CryptoPayAPIToken;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$case = $row->ccase;
	
################# PREPARE FOR CRYPTO BOT #######################
	switch ($case) {
		case 114:
		$sum = 5;
		break;
		case 115:
		$sum = 15;
		break;
		case 116:
		$sum = 25;
		break;
		case 117:
		$sum = 45;
		break;						
	}
	
	$ctime = time();
	$payload = $chat_id.":".$case;
	$data = array("asset"=>"TON", "amount"=>$sum, "payload"=>$payload, "paid_btn_name"=>"callback", "paid_btn_url"=>"https://t.me/TegroNFTbot");
	
	$prop = http_build_query($data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://pay.crypt.bot/api/createInvoice?".$prop);
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'Crypto-Pay-API-Token: '.$CryptoPayAPIToken));
	
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	$res = json_decode($server_output, true);		
################# PREPARE FOR CRYPTO BOT #######################
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 160;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][38];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 171;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][39]; 
	$url22 = $res['result']['pay_url'];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url22);	
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][40]; 	
	send($chat_id, $text[$langcode][41], $arInfo);  	
}

function messageIfPayByTONWin(){
	global $chat_id, $link, $langcode, $text, $NFTwallet;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$case = $row->ccase;
	
	$str20select = "SELECT `wallet` FROM `users` WHERE `chatid`='$chat_id'";
	$result20 = mysqli_query($link, $str20select);
	$row20 = @mysqli_fetch_object($result20);
	$walletno = $row20->wallet;
	
	switch ($case) {
		case 114:
		$casetext = "silver";
		break;
		case 115:
		$casetext = "gold";
		break;
		case 116:
		$casetext = "platinum";
		break;
		case 117:
		$casetext = "diamond";
		break;						
	}	
	
	$nftcode = $casetext.";".rand_string(20);
	$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);				

	switch ($case) {
		case 114:
		$sum = 5;
		$casename = "🪙 Silver";
		break;
		case 115:
		$sum = 15;
		$casename = "⚜️ Gold";
		break;
		case 116:
		$sum = 25;
		$casename = "💍 Platinum";		
		break;
		case 117:
		$sum = 45;
		$casename = "💠 Diamond";		
		break;						
	}			
	$suminnanoton = $sum * 1000000000;
	$suminton = $sum;
	
	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][47]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%casename%", $casename, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][48]);
	$tomessage = str_replace("%nftcode%", $nftcode, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminnanoton, $tomessage);				
	
	unset($arInfo);
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "chw".$walletno."|".$sum;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][49];
	send($chat_id, $tomessage, $arInfo); 									
				
}