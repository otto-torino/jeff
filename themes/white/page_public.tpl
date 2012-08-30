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
<div class="container">
	<div class="doctop">
		<div class="header_logo">
		</div>
		<div class="header_bottom">
		</div>
	</div>
	<div class="docbody">
		<nav class="menu">
			{module:menu method:mainMenu}
		</nav>
		<div class="col">
			{module:url_module method:url_method}
		</div>
	</div>
	<div class="docbottom">
		<footer id="footer"></footer>
	</div>
	<div>{ERRORS}</div>
</div>
<div class="credits">
	{module:page method:view params:credits}	
</div>
</body>
</html>
