<?php
set_time_limit(120);

$fn = fopen("Sent","r");
$result = array();
while(! feof($fn))  {
	$line = fgets($fn);
	if(substr($line, 0, 3) == 'To:') {
		$reg = preg_match("/To\:\s([^\<]*)\s\<([a-zA-Z0-9\.\-_@]*)\>/", $line, $matches);
		if($reg && count($matches)==3) {
			if(strpos($matches[2], ':') === false) {
				$result[strtolower($matches[2])] = $matches[1];
			}
		}
	}
}
fclose($fn);

file_put_contents('parse_email_adresses.csv', '"Email","Name"' . "\r\n");
foreach($result As $email=>$name) {
	$email = trim($email, '\'" ');
	$name = trim($name, '\'" ');
	$name = fix_encoding ($name);
	file_put_contents('parse_email_adresses.csv', '"' . $email . '","' . $name . '"' . "\r\n", FILE_APPEND);
}

echo(count($result) . " Email Adressen exportiert");

function fix_encoding($text) {
	$result = '';
    $elements = imap_mime_header_decode($text);
	for ($i=0; $i<count($elements); $i++) {
		//echo "Charset: {$elements[$i]->charset}\n";
		$result .= $elements[$i]->text;
	}
	return trim($result);
}