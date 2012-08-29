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
				<div class="header_logo"></div>
				<nav class="menu">
				{module:menu method:adminMenu}
				</nav>
			</div>
			<div class="docbody">
			{module:url_module method:url_method}
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
