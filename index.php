<?php
	// Check for the basics
	if (!isset($_POST['cmdEncode']) && !isset($_POST['cmdDecode'])) {
		// User has not yet submitted
		$_POST['chkBasics']= true;
	}
?><!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	<title>Decoder - Encoder: UTF8, UTF16, ...</title>
	<link rel="stylesheet" href="css/styles.css" />
</head>
<body>
	<div id="main">
		<header>	
			<h1><span class="title">De</span>code, <span class="title">En</span>code or <span class="title">Ob</span>fuscate your string</h1>
			<p>This is used to obfuscate your string or code, to encode or decode a certain value. For more information on possible XSS/XSRF implementations, see <a href="http://ha.ckers.org/xss.html" title="More information on possible XSS/XSRF implementations">http://ha.ckers.org/xss.html</a>.</p>
			<hr />
		</header>
		<section>
			<p>Enter your string in the textarea below. Am I missing a popular technique? <a href="mailto:m@ttias.be" title="Mail me a missing technique!">Let me know!</a></p>
			<form method="post" action="index.php">
				<textarea name="txtCode" cols="80" rows="6"><?=isset($_POST['txtCode']) ? $_POST['txtCode'] : '' ?></textarea>
				<p><input type="checkbox" name="chkBasics" id="chkBasics" <?=isset($_POST['chkBasics']) ? 'checked' : '' ?> /> <label for="chkBasics">Include basic encoding/decoding (HTML, UTF-8, base64, URL encode, ...)</label><br />
				<input type="checkbox" name="chkOneWay" id="chkOneWay" <?=isset($_POST['chkOneWay']) ? 'checked' : '' ?> /> <label for="chkOneWay">Include one-way encryption (MD5, SHA1, RipeMD, Adler, Haval...)</label><br />
				<input type="checkbox" name="chkObfuscate" id="chkObfuscate" <?=isset($_POST['chkObfuscate']) ? 'checked' : '' ?> /> <label for="chkObfuscate">Include code obfuscation (Javascript, SQL, HTML)</label></p>
				<p class="txtcenter"><input type="submit" name="cmdEncode" value="Encode string" class="submit_button" /> <input type="submit" name="cmdDecode" value="Decode string" class="submit_button" /></p>
			</form>
			<?php
				if (isset($_POST['cmdEncode']) && strlen($_POST['txtCode']) > 0) {
					// Encode this string
					$txtCode = $_POST['txtCode'];
					
					$arrCharCode = array();
					$arrCharCodeSQL = array();
					$arrCharCodeHexHtml = array();
					$arrCharCodeDecHtml = array();
					$arrCharCodeHexShortHtml = array();
					for ($i = 0; $i < strlen($txtCode); $i++) {
						$arrCharCode[] 		= ord($txtCode[$i]);
						$arrCharCodeSQL[] 	= "CHAR(". ord($txtCode[$i]) .")";
						$arrCharCodeHexHtml[]	= "&#x". dechex(ord($txtCode[$i]));
						$arrCharCodeDecHtml[]	= "&#". ord($txtCode[$i]);
						$arrCharCodeHexShortHtml[]	= "%". dechex(ord($txtCode[$i]));
					}
					
					echo "<h1>Encoding results</h1>\n\n";
					
					if (isset($_POST['chkBasics'])) {
						echo "<h2>Basic encoding</h2>\n";
						// UTF-7
						echo "<h3>UTF-7 encode</h3>\n";
						echo "<xmp>". imap_utf7_encode($txtCode) ."</xmp>\n\n";
						
						// UTF-8
						echo "<h3>UTF-8 encode</h3>\n";
						echo "<xmp>". utf8_encode($txtCode) ."</xmp>\n\n";
						
						// UTF-16
						echo "<h3>UTF-16 encode</h3>\n";
						echo "<xmp>". mb_convert_encoding($txtCode, "UTF-16", "auto") ."</xmp>\n\n";
						
						// UTF-32
						echo "<h3>UTF-32 encode</h3>\n";
						echo "<xmp>". mb_convert_encoding($txtCode, "UTF-32", "auto") ."</xmp>\n\n";
										
						// rawurlencode  
						echo "<h3>RAW URL encode</h3>\n";
						echo "<xmp>". rawurlencode($txtCode) ."</xmp>\n\n";
						
						// urlencode  
						echo "<h3>URL encode simple</h3>\n";
						echo "<xmp>". urlencode($txtCode) ."</xmp>\n\n";
						
						// urlencode  
						echo "<h3>URL encode full</h3>\n";
						echo "<xmp>". implode("", $arrCharCodeHexShortHtml) ."</xmp>\n\n";
						
						// HTML
						echo "<h3>HTML encode</h3>\n";
						echo "<xmp>". htmlentities($txtCode) ."</xmp>\n\n";
						
						// base64  
						echo "<h3>Base64 encode</h3>\n";
						echo "<xmp>". base64_encode($txtCode) ."</xmp>\n\n";
						
						// uuencode  
						echo "<h3>UUencode</h3>\n";
						echo "<xmp>". convert_uuencode($txtCode) ."</xmp>\n\n";
					}
					
					if (isset($_POST['chkOneWay'])) {
						echo "<h2>One way encoding</h2>\n";
						foreach (hash_algos() as $hash_algo) {
							echo "<h3>Hash: ". $hash_algo ."</h3>\n";
							echo "<xmp>". hash($hash_algo, $txtCode) ."</xmp>\n\n";
						}					
					}
					
					if (isset($_POST['chkObfuscate'])) {					
						echo "<h2>Obfuscation: JavaScript</h2>\n";
						// String.fromCharCode() in Javascript					
						echo "<h3>fromCharCode()</h3>\n";
						echo "<xmp>document.write(String.fromCharCode(". implode(",", $arrCharCode) ."));</xmp>\n\n";
						
						// unescape() in Javascript					
						echo "<h3>unescape()</h3>\n";
						echo "<xmp>document.write(unescape(\"". implode("", $arrCharCodeHexShortHtml) ."\"));</xmp>\n\n";
						
						echo "<h2>Obfuscation: SQL</h2>\n";
						// concat() char's				
						echo "<h3>CONTACT of CHAR()'s</h3>\n";
						echo "<xmp>CONCAT(". implode(",", $arrCharCodeSQL) .")</xmp>\n\n";
						
						// char()			
						echo "<h3>CHAR()</h3>\n";
						echo "<xmp>CHAR(". implode(",", $arrCharCode) .")</xmp>\n\n";
						
						echo "<h2>Obfuscation: HTML</h2>\n";
						// hexadecimal
						echo "<h3>HTML Hexadecimal with optional semicolons</h3>\n";
						echo "<xmp>". implode(";", $arrCharCodeHexHtml) ."</xmp>\n\n";
						
						// decimal
						echo "<h3>HTML Decimal with optional semicolons</h3>\n";
						echo "<xmp>". implode(";", $arrCharCodeDecHtml) ."</xmp>\n\n";
					}
				} elseif (isset($_POST['cmdDecode']) && strlen($_POST['txtCode']) > 0) {
					// Decode this string
					$txtCode = $_POST['txtCode'];
					
					if (isset($_POST['chkBasics'])) {
						echo "<h2>Basic encoding</h2>\n";
						// UTF-7
						echo "<h3>UTF-7 decoded</h3>\n";
						echo "<xmp>". imap_utf7_decode($txtCode) ."</xmp>\n\n";
						
						// UTF-8
						echo "<h3>UTF-8 decoded</h3>\n";
						echo "<xmp>". utf8_decode($txtCode) ."</xmp>\n\n";
						
						// UTF-16
						echo "<h3>UTF-16 decoded to UTF-8</h3>\n";
						echo "<xmp>". mb_convert_encoding($txtCode, "UTF-8", array("UTF-16")) ."</xmp>\n\n";
						
						// UTF-32
						echo "<h3>UTF-32 decoded to UTF-8</h3>\n";
						echo "<xmp>". mb_convert_encoding($txtCode, "UTF-8", array("UTF-16")) ."</xmp>\n\n";
										
						// rawurlencode  
						echo "<h3>RAW URL decoded</h3>\n";
						echo "<xmp>". rawurldecode($txtCode) ."</xmp>\n\n";
						
						// urlencode  
						echo "<h3>URL encode</h3>\n";
						echo "<xmp>". urlencode($txtCode) ."</xmp>\n\n";
						
						// HTML
						echo "<h3>HTML entities decoded</h3>\n";
						echo "<xmp>". html_entity_decode($txtCode) ."</xmp>\n\n";
						
						// base64  
						echo "<h3>Base64 decoded</h3>\n";
						echo "<xmp>". base64_decode($txtCode) ."</xmp>\n\n";
						
						// uuencode  
						echo "<h3>UUdecoded</h3>\n";
						echo "<xmp>". convert_uudecode($txtCode) ."</xmp>\n\n";
					}
				}
			?>
		</section>
	</div>
	<footer>
		<p>String decoder &amp; encoder | Created by <a href="http://mattiasgeniar.be" title="Mattias Geniar website">Mattias Geniar</a> | Source on <a href="https://github.com/mattiasgeniar/Encoder" title="String decoder &amp; encoder on Github">Github</a></p>
	</footer>
</body>
</html>