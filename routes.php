<?php

kirby()->routes(array(
  array(
    'pattern' => 'csv-handler/createpages/(:all)',
    'action' => function ($uri) {

      $messages = array();

      // check if user is logged in
      // @TODO: check permissions for Kirby 2.4.0
      if(kirby()->site()->user()) {

        // get variable values from config settings, else use defaults
        $filePath = c::get('csv-handler.filepath', __DIR__ . DS . 'examples/products.csv');
        $delimiter = c::get('csv-handler.delimiter', ',');

        $titleField = c::get('csv-handler.titlefield', 'ArticleNo');
        $template = c::get('csv-handler.template', 'default');


        $update = c::get('csv-handler.page.update', false);

        try {

          $csvFile = new SonjaBroda\CsvHandler($filePath, true, $delimiter);

        } catch (Exception $e) {

          $messages[] = $e->getMessage();

        }

        if(isset($csvFile)) {

          try {

            $csvFile->createPages(page($uri), $titleField, $template, $update);

          } catch(Exception $e) {

            $messages[] = $e->getMessage();

          }

        }
      } else {

        $messages[] = "You must be logged in to use this function.";

      }

      if(!empty($messages)) {
        $html = '';
        foreach($messages as $message) {
          $html .= '<div>' . $message . '</div>';
        }
        echo $html;
      }

    }
  ),
  array(
    'pattern' => 'csv-handler/createstructure/(:all)',
    'action' => function ($uri) {

      $messages = array();

      // check if user is logged in
      // @TODO: check permissions for Kirby 2.4.0
      if(kirby()->site()->user()) {

        // get variable values from config settings, else use defaults
        $filePath = c::get('csv-handler.filepath', __DIR__ . DS . 'examples/products.csv');
        $delimiter = c::get('csv-handler.delimiter', ',');

        $field = c::get('csv-handler.structure.field', 'items');
        $update = c::get('csv-handler.page.append', false);

        // try to instatiate an CsvHandler object
        try {
          $csvFile = new SonjaBroda\CsvHandler($filePath, true, $delimiter);

        } catch (Exception $e) {

          $messages[] = $e->getMessage();

        }

        if(isset($csvFile)) {

          // try to create/update the structure field
          try {

            $csvFile->createStructure(page($uri), $field, $append = false);

          } catch(Exception $e) {

            $messages[] = $e->getMessage();

          }

        }
      } else {

        $messages[] = "You must be logged in to use this function.";

      }

      if(!empty($messages)) {
        $html = '';
        foreach($messages as $message) {
          $html .= '<div>' . $message . '</div>';
        }
        echo $html;
      }


    }
    )
  ));
