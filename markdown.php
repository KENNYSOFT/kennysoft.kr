<?php
require("./include/parsedown-1.8.0-beta-5/Parsedown.php");
require("./include/parsedown-extra-0.8.0-beta-1/ParsedownExtra.php");
require("./include/parsedown-extreme-0.1.2/ParsedownExtreme.php");
require("./include/parsedown-tablespan-1.1.0/ParsedownTablespan.php");
require("./include/simplehtmldom_1_7/simple_html_dom.php");
ob_start();
require("./include/emoji-shortname-to-hex/emoji_unicode.php");
eval(preg_replace("/<\?([^\\0]*)\?>/m","$1",ob_get_clean()));
ob_end_clean();
class ParsedownFinal extends ParsedownExtreme
{
	private $tablespan_object,$tablespan_method;
	public function __construct()
	{
		$this->tablespan_object=new ParsedownTablespan();
		$this->tablespan_method=new ReflectionMethod('ParsedownTablespan','blockTableComplete');
		$this->tablespan_method->setAccessible(true);
	}
	protected function blockTableComplete(array $Block)
	{
		return $this->tablespan_method->invoke($this->tablespan_object,$Block);
	}
}
$url=((@$_SERVER["HTTPS"]==="on")?"https":"http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$fp=fopen($_GET["md"],"r");
$md=fread($fp,filesize($_GET["md"]));
fclose($fp);
$fp=fopen("common/FOOTER.html","r");
$footer=fread($fp,filesize("common/FOOTER.html"));
fclose($fp);
$Parsedown=new ParsedownFinal();
$Parsedown->setBreaksEnabled(true);
$md=preg_replace("/<br *\/?>(\r?\n)/i","$1",$md);
preg_match_all("/:([a-zA-Z0-9'_+-]+):/",$md,$emojis);
foreach($emojis[1] as $emojiname)if(isset($emoji_unicode[$emojiname]))$md=str_replace(":".$emojiname.":","&#x".$emoji_unicode[$emojiname].";",$md);
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
	$img->setAttribute("data-action","zoom");
	if($img->alt!==null)
	{
		$idx++;
		$img->parent->innertext=$img->parent->innertext."<figcaption><b>그림 ".$idx."</b>".(strlen($img->alt)>0?" ".$img->alt:"")."</figcaption>";
	}
}
foreach($dom->find("comment") as $comment)
{
	$comment->outertext="";
}
$h1=$dom->find("h1",0);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title><?php if(strpos($_SERVER["HTTP_HOST"],"localhost")!==false)echo("[DEV] ");echo(is_null($h1)?"Markdown":$h1->plaintext); ?></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/4.0.0/github-markdown.min.css">
	<link rel="stylesheet" href="https://fat.github.io/zoom.js/css/zoom.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/styles/vs.min.css">
	<link rel="stylesheet" href="https://unicodey.com/js-emoji/demo/emoji.css">
	<style>
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

		.utterances {
			max-width: unset;
		}

		date {
			display: block;
			text-align: right;
		}

		@media (max-width: 767px) {
			.markdown-body {
				padding: 15px;
			}
		}

		@media print {
			.utterences-outer, footer {
				display: none !important;
			}
		}
	</style>
<?php
if(strrpos($_SERVER["REQUEST_URI"],"/",strlen($_SERVER["REQUEST_URI"])-1)!==false)echo("	<link rel=\"amphtml\" href=\"".$url."index.amp.html\">\n");
else if(strrpos($_SERVER["REQUEST_URI"],".html",strlen($_SERVER["REQUEST_URI"])-5)!==false)echo("	<link rel=\"amphtml\" href=\"".substr($url,0,strlen($url)-5).".amp.html\">\n");
?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-155107438-1"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-155107438-1');
	</script>
	<meta property="fb:pages" content="181502375251183" />
</head>
<body>
<div class="markdown-body">
<?php
echo($dom."\n");
if(strpos($_SERVER["HTTP_HOST"],"localhost")===false)
{
?>
<div class="utterences-outer">
<hr>
<h1>댓글</h1>
<script src="https://utteranc.es/client.js"
        repo="KENNYSOFT/markdown-comments"
        issue-term="url"
        theme="github-light"
        crossorigin="anonymous"
        async>
</script>
</div>
<?php
}
?>
<footer><?php echo($footer); ?></footer>
</div>
<?php
if(strpos($_SERVER["HTTP_HOST"],"localhost")!==false)
{
?>
<script>
const pInnerText = (p) => {
	let innerText = "";
	let child = p.firstChild;
	while (child) {
		// <span class="mjx-chtml">
		if (child.nodeType === Node.ELEMENT_NODE) {
			if (child.tagName === "SPAN" && child.classList.contains("mjx-chtml")) innerText += child.innerText.split("\n").pop();
			else if (child.tagName !== "SCRIPT") innerText += child.innerText;
		}
		else if (child.nodeType === Node.TEXT_NODE) innerText += child.data;
		child = child.nextSibling;
	}
	return innerText;
};
const speller = () => {
	let text1 = ""; //document.getElementsByClassName("markdown-body")[0].innerText.replace(/ /g," ");
	let child = document.getElementsByClassName("markdown-body")[0].firstChild;
	while (child) {
		// <pre><code> <details><summary>
		if (child.nodeType === Node.ELEMENT_NODE) {
			if (child.tagName === "P" || child.tagName.match(/H\d/)) text1 += pInnerText(child);
			else if (child.tagName !== "PRE" && child.tagName !== "DETAILS" && child.tagName !== "TABLE") text1 += child.innerText;
		}
		else if (child.nodeType===Node.TEXT_NODE) text1 += child.data;
		child = child.nextSibling;
	}
	text1 = text1.substr(0, text1.lastIndexOf(text1.lastIndexOf("돌아가기") === -1 ? "Copyright © 2017-2021 KENNYSOFT. All Rights Reserved." : "돌아가기"));
	document.getElementById("text1").value = text1;
	document.getElementById("speller").submit();
};
const printmode = () => {
	document.getElementById("speller").remove();
	document.getElementsByTagName("footer")[0].remove();
	const as = document.getElementsByTagName("a");
	for (let i = 0; i < as.length; ++i) if (as[i].hostname === "localhost") as[i].hostname = "kennysoft.kr";
	document.title = document.title.replace("[DEV] ", "");
};
</script>
<form id="speller" method="post" action="https://speller.cs.pusan.ac.kr/results" target="_blank" style="position: fixed; top: 5px; right: 5px;">
<input id="text1" name="text1" type="hidden" value=""><a href="javascript:speller();">맞춤법 검사</a>
<a href="javascript:printmode();">프린트 모드</a>
<a href="<?php echo("https://kennysoft.kr".$_SERVER["REQUEST_URI"]); ?>" target="_blank">프로덕션 보기</a>
</form>
<?php
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="https://fat.github.io/zoom.js/js/zoom.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/highlight.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/languages/dos.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/languages/shell.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML" async></script>
</body>
</html>