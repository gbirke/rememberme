<?php

function rememberAutoload($class) {
  require dirname(__FILE__).'/../src/'.strtr($class,'_',DIRECTORY_SEPARATOR).".php";
}
spl_autoload_register("rememberAutoload");