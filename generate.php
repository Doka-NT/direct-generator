<?php
use skobka\dg\AdGenerator;
use skobka\dg\DgParser;
use skobka\dg\Input;

require 'vendor/autoload.php';

$input = Input::getInstance();
$file = $input->getFile();
$output = $input->getOutput();

$parser = new DgParser($file);
$generator = new AdGenerator($parser, $output);

if (pathinfo($output, PATHINFO_EXTENSION) === 'csv') {
	$generator->setCellDelimiter(",");
}

$generator->generate();
