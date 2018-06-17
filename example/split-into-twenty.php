<?php

require_once("../vendor/autoload.php");

$input = __DIR__ . "/example-wpxml.xml";
$output = __DIR__ . "/output";
$arguments = array(
  "perFile" => 20, // Default is 500
  "quiet" => false, // Default is true
);

$processor = new Magpie\WPXML\Splitter($input, $output, $arguments);
$processor->process();