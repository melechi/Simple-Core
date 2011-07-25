<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Simple Core Exception</title>
		<link rel="stylesheet" type="text/css" href="{$:PUBLICROOT}css/SyntaxHighlighter.css" />
		<style type="text/css">
		body
		{
			margin: 0px;
			padding: 0px;
			background-color: #FFF;
			font-family: Arial;
		}
		blockquote
		{
			font-style: italic;
			font-size: 14px;
		}
		h2
		{
			text-decoration: underline;
		}
		#wrapper
		{
			width: 1000px;
		}
		#header
		{
			clear: both;
			width: 1000px;
			height: 70px;
			color: #000;
		}
		#header .left
		{
			float: left;
		}
		#header .right
		{
			float: left;
			margin-top: -17px;
		}
		#body
		{
			clear: both;
			width: 1000px;
			margin: 10px;
		}
		</style>
		<script type="text/javascript" charset="ISO-8859-1" src="{$:PUBLICROOT}js/shCore.js"></script>
		<script type="text/javascript" charset="ISO-8859-1" src="{$:PUBLICROOT}js/shBrushPhp.js"></script>
		<script type="text/javascript" charset="ISO-8859-1" src="{$:PUBLICROOT}js/shBrushXml.js"></script>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<h1>Simple Core Exception</h1>
			</div>
			<div id="body">
				<h2>Message</h2>
					<blockquote style="color:red;">{$:MESSAGE}</blockquote>
				<hr />
				<h2>File</h2>
					<b>Filename: </b>{$:FILE_FILENAME}<br />
					<b>Class: </b>{$:FILE_CLASS}<br />
					<b>Function: </b>{$:FILE_FUNCTION}()<br />
					<b>Line: </b><a href="#{$:FILE_LINE}" style="color: blue;">{$:FILE_LINE}</a><br />
					<b>Source: </b>
					<pre name="source" class="php:firstline[{$:FILE_STARTLINE}]">{$:FILE_SOURCE}</pre>
				<hr />
				<!--
				<h2>Output Buffer</h2>
					<pre name="source" class="xhtml">{$:BUFFER}</div>
				<hr />
				-->
				<h2>Debug Information</h2>
					<pre style="width: 980px; height: 600px; overflow: auto;">{$:DEBUG}</pre>
			</div>
		</div>
		<script type="text/javascript">
		//<![CDATA[
		dp.SyntaxHighlighter.ClipboardSwf='/singleSignon/web/flash/clipboard.swf';
		dp.SyntaxHighlighter.HighlightAll('source');
		//]]>
		</script>
	</body>
</html>