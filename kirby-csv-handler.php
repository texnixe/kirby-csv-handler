<?php

/**
 * Kirby Modules Plugin
 *
 * @author Sonja Broda <sonja@texniq.de>
 */

include __DIR__ . DS . 'routes.php';

require_once(__DIR__ . DS . 'lib' . DS . 'csv-handler.php');

if (c::get('csv-handler.createpages.widget', false)) {
      kirby()->set('widget', 'csv-handler.createpages', __DIR__ . DS . 'widgets' . DS . 'csv-handler.createpages');
}
