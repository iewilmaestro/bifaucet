<?php
error_reporting(0);
//CLASS MODUL
function Run($url, $head = 0, $post = 0){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_COOKIE,TRUE);
	if($post){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if($head){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
	}
	curl_setopt($ch, CURLOPT_HEADER, true);
	$r = curl_exec($ch);
	$c = curl_getinfo($ch);
	if(!$c) return "Curl Error : ".curl_error($ch); else{
		$hd = substr($r, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		$bd = substr($r, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		curl_close($ch);
		return array($hd,$bd)[1];
	}
}
$line = str_repeat('~',50)."\n";
$h[] = "cookie: _ga=GA1.1.2135199477.1649241076; bitmedia_fid=eyJmaWQiOiJhM2Q0MjA1OTA1MDc5MjEyZDUzNGFkMjJmMDQ3MDYwYiIsImZpZG5vdWEiOiJhOGRjZGFmMDBiYTY4ZjllZTliNzBjZGJiNmVkYTU1NSJ9; __gads=ID=ee6a559115b45305-220c69a1edd10050:T=1649754583:RT=1649754583:S=ALNI_MbRXsb86Y-W1440f4jZYpx-eJGG4Q; _coinzilla_fp_=%7B%22backed%22%3A%5B%7B%22cap%22%3A10%2C%22lastCall%22%3A1649772268183%7D%5D%7D; ci_session=a05394414077fe445f071ec0c6104250ef65d31e; ads_300x250-0=seen; ads_300x250-1=seen; cf_clearance=PdVi0V0DbgCACX1sR4mgnjavZ2fIHAVrcQVVexphmRE-1649777630-0-150; _data_cpc=2-1-1649779415_286-1-1649781332_360-1-1649781236_361-1-1649779326_411-1-1649779861; csrf_cookie_name=884165f62636570aaa786118496606ba; ads_468x600-0=seen; ads_728x90-0=seen; _ga_58PCYERW4T=GS1.1.1649770866.3.1.1649777757.0; _ga_HF8YJ4L2V3=GS1.1.1649774816.4.1.1649777757.0";
$h[] = "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36";

$res = Run('https://bifaucet.com/dashboard', $h);
//<h4 class="mb-0" style="text-transform:capitalize;">
//iewilmaestro</h4>
$username = trim(explode('</h4>',explode('<h4 class="mb-0" style="text-transform:capitalize;">',$res)[1])[0]);
//<h5 class="mb-0">155</h5>
$balance = explode('</h5>',explode('<h5 class="mb-0">',$res)[1])[0];//155
print "Username: ".$username."\n";
print "Balance : ".$balance."\n";
print $line;

menu:
print "1 > ptc \n";
print "2 > faucet \n";
$pilih = readline("Input Number : ");
print $line;
if($pilih == 1){ goto ptc;}
elseif($pilih == 2){ goto faucet;}

ptc:
while(true){
	$res = Run('https://bifaucet.com/ptc',$h);
	if(preg_match('/Cloudflare/',$res)){
		print "Cloudflare detect\n";
		die();
	}
	//https://bifaucet.com/ptc/view/5'
	$id = explode("'",explode('https://bifaucet.com/ptc/view/',$res)[1])[0];
	if($id){
		$res = Run('https://bifaucet.com/ptc/view/'.$id, $h);
		//var timer = 15;
		$time = explode(';',explode('var timer = ',$res)[1])[0];
		for($i=$time;$i>0;$i--){
			print "\r   \r";
			print $i;
			sleep(1);
			print "\r   \r";
		}
		//<input type="hidden" name="csrf_token_name" value="158818702c850e19c79d983c2fdb0e02">
		//<input type="hidden" name="token" value="Hh61JxIeCKMsYTz3WG2jgUc5SyktuX">
		$csrf = explode('">',explode('<input type="hidden" name="csrf_token_name" value="',$res)[1])[0];
		$token =explode('">',explode('<input type="hidden" name="token" value="',$res)[1])[0];

		$data = "captcha=recaptchav2&hewle=786&csrf_token_name=".$csrf."&token=".$token;
		$res = Run('https://bifaucet.com/ptc/verify/'.$id, $h, $data);
		//<script> Swal.fire('Good job!', '30 has been added to your balance', 'success')</script>
		$sukses = explode("', '",explode("Swal.fire('Good job!', '",$res)[1])[0];
		print $sukses."\n";
		$res = Run('https://bifaucet.com/dashboard', $h);
		$balance = explode('</h5>',explode('<h5 class="mb-0">',$res)[1])[0];//155
		print "Balance : ".$balance."\n";
		print $line;
	}else{
		print "ptc habis\n";
		print $line;
		goto menu;
	}

}

faucet:
while(true){
	$res = Run("https://bifaucet.com/faucet", $h);
	if(preg_match('/Firewall/',$res)){
		print "Firewall detect\n";
		die();
	}
	if(preg_match('/Cloudflare/',$res)){
		print "Cloudflare detect\n";
		die();
	}
	//<h5 class="mb-0">45/50</h5>
	$left = explode('</h5>',explode('<h5 class="mb-0">',$res)[1])[0];
	if($left == "0/50"){
		print "Jatah claim habis\n";
		print $line;
		goto menu;
	}
	//var wait = 200 - 1;
	$time = trim(explode('-',explode('var wait = ',$res)[1])[0]);
	if($time){
		for($i=$time;$i>0;$i--){
			print "\r   \r";
			print $i;
			sleep(1);
			print "\r   \r";
		}
		goto faucet;
	}

	//rel=\"5352\">
	//rel=\"8877\">
	//rel=\"4262\">
	$bot = explode('rel=\"',$res);
	$bot1 = explode('\">',$bot[1])[0];
	$bot2 = explode('\">',$bot[2])[0];
	$bot3 = explode('\">',$bot[3])[0];

	//<input type="hidden" name="csrf_token_name" id="token" value="d0409cd4537d5a0e55c155735ff77ad3">
	//<input type="hidden" name="token" value="ry0hWVNSjuMm2z1eiKcvYnUabLoDJ8">
	$csrf = explode('">',explode('<input type="hidden" name="csrf_token_name" id="token" value="',$res)[1])[0];
	$token = explode('">',explode('<input type="hidden" name="token" value="',$res)[1])[0];

	$data = "antibotlinks=+".$bot1."+".$bot2."+".$bot3."&csrf_token_name=".$csrf."&token=".$token."&captcha=recaptchav2&hewle=786";
	$res = Run('https://bifaucet.com/faucet/verify', $h, $data);
	//<script> Swal.fire('Good job!', '50 has been added to your balance', 'success')</script>
	$sukses = explode("', '",explode("Swal.fire('Good job!', '",$res)[1])[0];
	if($sukses){
		print $sukses."\n";
		$res = Run('https://bifaucet.com/dashboard', $h);
		$balance = explode('</h5>',explode('<h5 class="mb-0">',$res)[1])[0];//155
		print "Balance : ".$balance."\n";
		print $line;
	}else{
		print "Invalid antibot";
		sleep(5);
		print "\r                 \r";
	}
}
