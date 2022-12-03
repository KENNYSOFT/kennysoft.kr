<?php
require("./include/parsedown/Parsedown.php");
require("./include/parsedown-extra/ParsedownExtra.php");
require("./include/ParsedownExtended/ParsedownExtended.php");
require("./include/simplehtmldom/simple_html_dom.php");
$url=((@$_SERVER["HTTPS"]==="on")?"https":"http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$fp=fopen($_GET["md"],"r");
$md=fread($fp,filesize($_GET["md"]));
fclose($fp);
$fp=fopen("common/FOOTER.html","r");
$footer=fread($fp,filesize("common/FOOTER.html"));
fclose($fp);
$curl=curl_init();
curl_setopt($curl,CURLOPT_URL,"cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.1.0/github-markdown-light.min.css");
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
$css=str_replace("!important","",curl_exec($curl));
curl_close($curl);
$Parsedown=new ParsedownExtended([
	'tables'=>[
		'tablespan'=>true
	]
]);
$Parsedown->setBreaksEnabled(true);
$html=$Parsedown->text($md);
$html=str_replace("<summary>","<details><summary>",$html);
$html=str_replace("<p></details></p>","</details>",$html);
$dom=new simple_html_dom();
$dom->load($html,true,false);
$idx=0;
foreach($dom->find("p img") as $img)
{
	if($img->parent->tag!=="p")continue;
	$img->parent->tag="figure";
	$img->parent->style="text-align: center;";
	if($img->alt!==null)
	{
		$idx++;
		$img->parent->innertext=$img->parent->innertext."<figcaption><b>그림 ".$idx."</b>".(strlen($img->alt)>0?" ".$img->alt:"")."</figcaption>";
	}
}
foreach($dom->find("date") as $date)
{
	$date->tag="div";
	$date->class="date";
}
foreach($dom->find("comment") as $comment)
{
	$comment->outertext="";
}
$dom->load($dom->save(),true,false);
foreach($dom->find("img") as $img)
{
	$img->tag="amp-img";
	//if(preg_match("/^(https?:)?\/\//",$img->src))$size=getimagesize($img->src);
	$size=getimagesize(realpath(dirname($_GET["md"]).DIRECTORY_SEPARATOR.$img->src));
	if(!isset($img->width))$img->width=$size[0];
	if(!isset($img->height))$img->height=$size[1];
	$img->layout="intrinsic";
	$img->outertext=preg_replace("/(\/ *)?>/","></amp-img>",$img->outertext);
}
$mathml=false;
foreach($dom->find("text") as $text)
{
	if(preg_match("/\\\\\\((.*?)\\\\\\)/",$text->innertext))
	{
		$mathml=true;
		$text->innertext=preg_replace("/\\\\\\((.*?)\\\\\\)/","<amp-mathml layout=\"container\" inline data-formula=\"\\\($1\\\)\"></amp-mathml>",$text->innertext);
	}
	else if(preg_match("/\\\\\\[.*?\\\\\\]/",$text->innertext))
	{
		$mathml=true;
		$text->innertext=preg_replace("/\\\\\\[(.*?)\\\\\\]/","<amp-mathml layout=\"container\" data-formula=\"\\\[$1\\\]\"></amp-mathml>",$text->innertext);
	}
}
$h1=$dom->find("h1",0);
?>
<!doctype html>
<html ⚡>
<head>
	<meta charset="UTF-8">
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<title><?php echo(is_null($h1)?"Markdown":$h1->plaintext); ?></title>
	<link rel="canonical" href="<?php echo((strrpos($_SERVER["REQUEST_URI"],"index.amp.html",strlen($_SERVER["REQUEST_URI"])-14)!==false)?substr($url,0,strlen($url)-15):substr($url,0,strlen($url)-9).".html"); ?>">
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
	<style amp-custom><?php echo($css."\n"); ?>
		.markdown-body {
			box-sizing: border-box;
			min-width: 200px;
			max-width: 980px;
			margin: 0 auto;
			padding: 45px;
		}

		.markdown-body pre > code.wrap {
			word-break: break-all;
			white-space: normal;
		}

		.markdown-body table pre {
			margin-bottom: 0;
		}

		.markdown-body ul blockquote ul {
			margin-bottom: inherit;
		}

		.markdown-body details {
			margin-bottom: 16px;
		}

		.markdown-body sup > a::before, .markdown-body sup > a::after {
			content: "";
		}

		.date {
			display: block;
			text-align: right;
		}

		@media (max-width: 767px) {
			.markdown-body {
				padding: 15px;
			}
		}
	</style>
	<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
<?php
if($mathml)
{
?>
	<script async custom-element="amp-mathml" src="https://cdn.ampproject.org/v0/amp-mathml-0.1.js"></script>
<?php
}
?>
</head>
<body>
<amp-analytics type="gtag" data-credentials="include">
<script type="application/json">
{
"vars" : {
	"gtag_id": "UA-155107438-1",
	"config" : {
	"UA-155107438-1": { "groups": "default" }
	}
}
}
</script>
</amp-analytics>
<div class="markdown-body">
<?php echo($dom."\n"); ?>
<footer><?php echo($footer); ?></footer>
</div>
</body>
</html>