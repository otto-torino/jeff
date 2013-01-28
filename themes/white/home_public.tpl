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
		<div class="header_lng">
			{module:language method:choose}
		</div>
		<nav class="menu">
			{module:menu method:mainMenu}
		</nav>
	</div>
	<div class="docbody">
		
		<div class="col1 left">
			{module:index method:index}
		</div>
		<div class="col2 right">
			{module:login method:login}
		</div>
		<div class="clear"></div>
	</div>
	<div class="docbottom">
	</div>
</div>
<div class="credits">
	{module:page method:view params:credits}
</div>
<div>{ERRORS}</div>
</body>
</html>


