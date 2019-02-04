<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @param string $url
 * @param array  $opt
 * @return array
 */
function curld($url, $opt = array())
{
	$ch = curl_init($url);
	$optf = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false
	);
	foreach ($opt as $k => $v) {
		$optf[$k] = $v;
	}
	unset($opt, $k, $v);
	curl_setopt_array($ch, $optf);
	$out = curl_exec($ch);
	$info = curl_getinfo($ch);
	$err = curl_error($ch);
	$ern = curl_errno($ch);
	curl_close($ch);
	return array(
		"out" => $out,
		"info" => $info,
		"err" => $err,
		"ern" => $ern
	);
}

$url = "http://propana.otoreport.com/harga.js.php?id=2c28bad23c1588072628a0f2aa5f22bf1dd47c8cf09c3a13dd65cc3e4039d2f90ddab2f0eed50b8334c4944d83d43a96-15";

$o = curld($url);
if ($o["err"]) {
	printf("An error occured: (%d) %s\n", $o["ern"], $o["err"]);
	exit(1);
}

/**
 * @param string $str
 * @return string
 */
function clean($str)
{
	return html_entity_decode($str, ENT_QUOTES, "UTF-8");
}

preg_match_all("/(?:<td colspan=\"6\".+>)(.+)(?:<\/td>.+<tr class=\"head\">)(.+)(?:<\/table>)/Usi", $o["out"], $m);
unset($o, $url, $m[0]);
$data = array();
foreach ($m[1] as $k => &$v) {
	if (preg_match_all("/<td class=\"center\">(.+)<\/td>.+<td class=\"center\">(.+)<\/td>.+<td class=\"center\">(.+)<\/td>.+<td class=\"center last\">.+<span class=\".+\">(.+)<\/span>.+<\/td>/Usi", $m[2][$k], $b)) {
		unset($b[0]);
		$dd = array();
		foreach($b[1] as $k => $c) {
			$dd[] = array(
				"kode" => clean($c),
				"keterangan" => clean($b[2][$k]),
				"harga" =>  clean($b[3][$k]),
				"status" =>  clean($b[4][$k])
			);
		}		
		$data[] = array(
			"kategori" => clean($v),
			"data" => $dd
		);
		unset($b, $k, $c, $dd);
	}	
}
unset($v, $m);

printf("<pre>\n");
print_r($data);
printf("\n</pre>");