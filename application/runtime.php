<?php
/*

  All code in this file will be executed before EVERY controller. 
  Config/Helpers can be accessed with $this->config/helpers (You are still in the maincontroller-object scope).
  
  Unfortionally accessing the layout has still need to go dirty (global $_layout; $_layout->assign()).

*/



/* I can, for example, set the default title of my website. Any new call to this function in a controller will overrule this. */
global $_layout;

$_layout->setTitle('Testing the YAY!Scripting Framework.');