<!DOCTYPE html>
<html>
<head>
  <title>{block name='title'}Musstock - the Best!{/block}</title>
{block name='links'}
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="keywords" content="Musshop misha-pedik">
  <meta name="description" content="Musshop - it's your dream">
  <link href="/css/style.css" media="screen, projection" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="/js/jquery.js"></script>
{/block}
</head>
<body>
	<div id="wrapper">
		<div id="wrapper_in">
			{include file='header.tpl'}
      {block name='content'}{/block}
		</div>
	</div>
</body>
</html>