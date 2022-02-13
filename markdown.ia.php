<?php
require "./include/parsedown-1.7.1/Parsedown.php";
require "./include/parsedown-extra-0.7.1/ParsedownExtra.php";
require "./include/parsedown-tablespan-1.0.0/ParsedownTablespan.php";
require "./include/simplehtmldom_1_7/simple_html_dom.php";
$url=((@$_SERVER["HTTPS"]==="on")?"https":"http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$fp=fopen($_GET["md"],"r");
$md=fread($fp,filesize($_GET["md"]));
fclose($fp);
$fp=fopen("common/FOOTER.html","r");
$footer=fread($fp,filesize("common/FOOTER.html"));
fclose($fp);
$Parsedown=new ParsedownTablespan();
$Parsedown->setBreaksEnabled(true);
$md=preg_replace("/<br *\/?>(\r?\n)/i","$1",$md);
$html=$Parsedown->text($md);
$dom=new simple_html_dom();
$dom->load($html,true,false);
$h1=$dom->find("h1",0);
?>
<!DOCTYPE html>
<html lang="ko" prefix="op: http://media.facebook.com/op#">
<head>
	<meta charset="UTF-8">
	<meta property="op:markup_version" content="v1.0">
	<link rel="canonical" href="<?php echo((strrpos($_SERVER["REQUEST_URI"],"index.ia.html",strlen($_SERVER["REQUEST_URI"])-13)!==false)?substr($url,0,strlen($url)-14):substr($url,0,strlen($url)-8).".html"); ?>">
	<title><?php echo(is_null($h1)?"Markdown":$h1->plaintext); ?></title>
	<meta property="fb:pages" content="181502375251183" />
	<meta property="og:locale" content="ko_KR"/>
	<meta property="og:site_name" content="KENNYSOFT"/>
	<meta property="og:title" content="KENNYSOFT"/>
	<meta property="og:url" content="http://kennysoft.kr"/>
	<meta property="og:type" content="website"/>
	<meta property="og:description" content="KENNYSOFT"/>
</head>
<body>
<article>
<header>
	<figure>
		<img src="//kennysoft.kr/KENNYSOFT.png" />
		<figcaption>KENNYSOFT Logo</figcaption>
	</figure>
	<h3 class="op-kicker">KENNYBLOG</h3>
	<h1><?php echo(is_null($h1)?"Markdown":$h1->plaintext); ?></h1>
	<h2>Subtitle</h2>
	<address>
		<a rel="facebook" href="http://facebook.com/525hm">Hyeonmin Park</a>
		Hyeonmin Park is CEO & Founder of KENNYSOFT.
	</address>
	<time class="op-published" dateTime="2018-11-13T00:00:00Z">2018-11-13</time>
	<time class="op-modified" dateTime="2018-11-13T00:00:00Z">2018-11-13</time>
</header>
<?php echo($dom."\n"); ?>
<footer><?php echo($footer); ?></footer>
</article>
</body>
</html>