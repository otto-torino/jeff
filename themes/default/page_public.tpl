<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="{LANGUAGE}" xml:lang="{LANGUAGE}">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="description" content="{DESCRIPTION}" />
<meta name="keywords" content="{KEYWORDS}" />
<title>{TITLE}</title>
<link rel="shortcut icon" href="{FAVICON}" />
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
			<section>
				{module:url_module method:url_method}
			</section>
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
