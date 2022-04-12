<?php
error_reporting(0);
require "cfg.php";

const
b = "\033[1;34m",
c = "\033[1;36m",
d = "\033[0m",
h = "\033[1;32m",
k = "\033[1;33m",
m = "\033[1;31m",
n = "\n",
p = "\033[1;37m",
u = "\033[1;35m";

$server = "https://pastebin.com/raw/eqzh1nDL";
$getserver = file_get_contents($server);
$dataserver = trim(explode('#',explode('#bifaucet:',$getserver)[1])[0]);
if($dataserver == "off"){
	exit(m."Script off".c);
}
if($dataserver == "mt"){
	exit(m."Script sedang dalam pemeliharaan");
}


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
$h[] = "cookie: ".$cookie;
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
