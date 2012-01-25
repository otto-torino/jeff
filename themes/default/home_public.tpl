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
		<div class="header_lng">
			{module:language method:choose}
		</div>
		<nav class="menu">
			{module:menu method:mainMenu}
		</nav>
	</div>
	<div class="docbody">
		
		<div class="col1 left">
			<section>
				{module:index method:index}
			</section>
		</div>
		<div class="col2 right">
			<section class="login box">
				{module:login method:login}
			</section>
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


