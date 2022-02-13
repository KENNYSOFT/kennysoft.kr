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
class ParsedownTablespan2 extends ParsedownTablespan
{
	protected $EmRegex = array(
		'*' => '/^[*]((?:\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s',
		'_' => '/^_([^\s](?:\\\\_|[^_]|__[^_]*__)+?[^\s])_(?!_)\b/us',
	);
};
$Parsedown=new ParsedownTablespan2();
$Parsedown->setBreaksEnabled(true);
$md=preg_replace("/<br *\/?>(\r?\n)/i","$1",$md);
//$md=preg_replace("/\\$([^$]*)\\$/i","\\\\\\($1\\\\\\)",$md);
$html=$Parsedown->text($md);
$dom=new simple_html_dom();
$dom->load($html,true,false);
foreach($dom->find("p > img") as $img)
{
	$img->parent->setAttribute("style","text-align: center;");
	$img->setAttribute("data-action","zoom");
}
$h1=$dom->find("h1",0);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title><?php echo(is_null($h1)?"Markdown":$h1->plaintext); ?></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/2.10.0/github-markdown.min.css">
	<link rel="stylesheet" href="https://fat.github.io/zoom.js/css/zoom.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/vs.min.css">
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

		.utterances-outer {
			box-sizing: border-box;
			min-width: 200px;
			max-width: 980px;
			margin: -90px auto;
			padding: 45px;
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

			.utterances-outer {
				margin: -30px auto;
				padding: 15px;
			}
		}
	</style>
<?php
if(strrpos($_SERVER["REQUEST_URI"],"/",strlen($_SERVER["REQUEST_URI"])-1)!==false)echo("	<link rel=\"amphtml\" href=\"".$url."index.amp.html\">\n");
else if(strrpos($_SERVER["REQUEST_URI"],".html",strlen($_SERVER["REQUEST_URI"])-5)!==false)echo("	<link rel=\"amphtml\" href=\"".substr($url,0,strlen($url)-5).".amp.html\">\n");
?>
	<meta property="fb:pages" content="181502375251183" />
</head>
<body>
<div class="markdown-body">
<?php echo($dom."\n"); ?>
<hr>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="https://unicodey.com/js-emoji/lib/emoji.js" type="text/javascript"></script>
<script>
$.fn.emoji=function()
{
	var emoji=new EmojiConvertor();
	emoji.replace_mode="unified";
	return this.each(function()
	{
		$(this).html(function(i,oldHtml)
		{
			return emoji.replace_colons(oldHtml);
		});
	});
};
$(".markdown-body").emoji();
</script>
<script src="https://fat.github.io/zoom.js/js/zoom.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    tex2jax: {
      inlineMath: [ ['$','$'], ["\\(","\\)"] ],
      processEscapes: true
    }
  });
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML" async></script>
</body>
</html>