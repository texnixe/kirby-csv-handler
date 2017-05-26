<?php

/**
 * Kirby CSV Handler Plugin
 *
 * @author Sonja Broda <sonja@texniq.de>
 */

include __DIR__ . DS . 'routes.php';

require_once(__DIR__ . DS . 'lib' . DS . 'csv-handler.php');

function csv($filepath, $parse_header = false, $delimiter = ',', $length = 8000) {
  return new SonjaBroda\CsvHandler($filepath, $parse_header, $delimiter, $length);
}

if(c::get('csv-handler.createpages.widget', false)) {
  kirby()->set('widget', 'csv-handler.createpages', __DIR__ . DS . 'widgets' . DS . 'csv-handler.createpages');
}
