<!DOCTYPE HTML>
<html lang="nl">
	<head>
		
		<title>{$title}</title>
		
		<link rel="stylesheet" href="/application/resources/style/normalize.css" />
		<link rel="stylesheet" href="/application/resources/style/default.css" />
		
		<link rel="shortcut icon" href="/favicon.ico" />
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=7.5" />
		<meta name="description" content="" />
		<meta name="author" content="YAY!Scripting" />
		<meta name="robots" content="index, nofollow" />
		
		
{$headers|indent:2:"\t"}
	</head>
	<body>
	
		{$debug}
	
		<div class="container" id="container">
		
			{$content}
			
		</div>
		
	</body>
</html>