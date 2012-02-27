<?php /* Smarty version Smarty-3.0.7, created on 2012-02-27 11:42:24
         compiled from "application/views/nl/molds/layout.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20659010324f4b5e10596679-47787502%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4ea8818f155028008ca2e13f417f5d0a9fc96c59' => 
    array (
      0 => 'application/views/nl/molds/layout.tpl',
      1 => 1329926851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20659010324f4b5e10596679-47787502',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE HTML>
<html lang="nl">
	<head>
		
		<title><?php echo $_smarty_tpl->getVariable('title')->value;?>
</title>
		
		<link rel="stylesheet" href="/application/resources/style/normalize.css" />
		<link rel="stylesheet" href="/application/resources/style/default.css" />
		
		<link rel="shortcut icon" href="/favicon.ico" />
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=7.5" />
		<meta name="description" content="" />
		<meta name="author" content="YAY!Scripting" />
		<meta name="robots" content="index, nofollow" />
		
		
<?php echo preg_replace('!^!m',str_repeat("\t",2),$_smarty_tpl->getVariable('headers')->value);?>

	</head>
	<body>
	
		<?php echo $_smarty_tpl->getVariable('debug')->value;?>

	
		<div class="container" id="container">
		
			<?php echo $_smarty_tpl->getVariable('content')->value;?>

			
		</div>
		
	</body>
</html>