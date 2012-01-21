<?php
/*

  All code in this file will be executed before EVERY controller. 
  Config/Helpers/layout can be accessed this way:
  $config  = YS_Config::Load();
  $helpers = YS_Helpers::Load();
  $layout  = YS_Layout::Load();

*/



/* I can, for example, set the default title of my website. Any new call to this function in a controller will overrule this. */
$layout = YS_Layout::Load();

$layout->setTitle('Testing the YAY!Scripting Framework.');