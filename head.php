<?php
require 'vendor/autoload.php';

if($_SERVER['SERVER_NAME'] == 'localhost') {
    SassCompiler::run("scss/", "css/");
}
?>