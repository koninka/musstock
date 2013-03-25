<!DOCTYPE html>
<html>
	{include file='head.tpl'}
<body>
	<div id="wrapper">
		<div id="wrapper_in">
			{include file='header.tpl'}
			{if isset($sb_components)}
				<aside id="left_sb">
					{foreach from=$sb_components item=item}
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