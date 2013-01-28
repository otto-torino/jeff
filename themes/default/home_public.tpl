<!DOCTYPE html>
<html lang="{LANGUAGE}">
<head>
<meta charset="utf-8" />
<meta name="description" content="{DESCRIPTION}" />
<meta name="keywords" content="{KEYWORDS}" />
{META}
<title>{TITLE}</title>
<link rel="shortcut icon" href="{FAVICON}" />
{HEAD_LINKS}
{CSS}
{JAVASCRIPT}
</head>
<body>
<div id="top_site">
	<header>
		<div class="header_logo"></div>
		{module:language method:choose}
	</header>
</div>
{module:menu method:mainMenu}
<div class="site_content">
	<div id="content">
		<div class="col1 left">
			{module:index method:index}
		</div>
		<div class="col2 right">
			{module:login method:login}
		</div>
		<div class="clear"></div>
	</div>
</div>
<div id="site_bottom">
	<footer>
		{module:page method:view params:credits}
	</footer>
</div>
<div>{ERRORS}</div>
</body>
</html>
