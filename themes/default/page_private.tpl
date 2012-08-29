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
	</header>
</div>
<nav class="main_menu">
	{module:menu method:mainMenu}
</nav>
<div class="site_content">
	<div id="content">
		{module:url_module method:url_method}
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
