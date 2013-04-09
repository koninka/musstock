<!DOCTYPE html>
<html>
<head>
	<title>{$title|default:'Musstock - the Best!'}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="keywords" content="Musshop misha-pedik">
  <meta name="description" content="Musshop - it's your dream">
  <link href="/css/style.css" media="screen, projection" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="/js/jquery.js"></script>
  {foreach from=$links|default:array() item=item}
    {include file="$item"}
  {/foreach}
</head>
<body>
	<div id="wrapper">
		<div id="wrapper_in">
			{include file='header.tpl'}
			{if isset($sb_components)}
				<aside id="left_sb">
					{foreach from=$sb_components|default:array() item=item}
						{include file="$item"}
					{/foreach}
				</aside>
				<div id="container" style="width: 904px;">
			{else}
				<div id="container" style="">
			{/if}
				<h1>{$caption}</h1>
				{foreach from=$container|default:array() item=item}
					{include file="$item"}
				{/foreach}
				</div>
			</div>
		</div>
	</div>
</body>
</html>