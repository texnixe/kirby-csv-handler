<?php

kirby()->routes(array(
	array(
		'pattern' => 'csv-handler/createpages/(:all)',
		'action' => function ($uri) {

			if(kirby()->site()->user()) {

				$filePath = c::get('csv-handler.filepath');
				$titleField = c::get('csv-handler.titlefield');
				$template = c::get('csv-handler.template', 'default');

				$delimiter = c::get('csv-handler.delimiter', ',');
				$update = c::get('csv-handler.page.update', false);

				try {
					$csvFile = new SonjaBroda\CsvHandler($filePath, true, $delimiter);

				} catch (Exception $e) {
					echo $e->getMessage();

				}

				if(isset($csvFile)) {

					try {

					  $csvFile->createPages(page($uri), $titleField, $template, $update);

					} catch(Exception $e) {

					  echo $e->getMessage();

					}

				}
			} else {
				echo "You must be logged in to use this function.";

			}
		}
 )
));
