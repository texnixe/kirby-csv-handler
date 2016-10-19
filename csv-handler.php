<?php
namespace SonjaBroda;

use CsvImporter;
use Str;
use Exception;
use Yaml;

include __DIR__ . DS . 'csvimporter' . DS . 'csvimporter.php';

class CsvHandler {

  private $items = array();
  private $file;

  public function __construct($filepath, $parse_header = false, $delimiter = ',', $length='8000', $itemNo=0) {

    $this->file = new CsvImporter($filepath, $parse_header, $delimiter, $length);
    $this->items = $this->file->get($itemNo);

  }

  public function getItems() {

    return $this->items;

  }

  public function getLabels() {

    return $this->file->getHeader();

  }

  public function createPages($parent, $UIDKey, $template = 'default', $update = false) {
    $messages = array();

    // @TODO: Check if $titleKey starts with a number

    // check if $parent exists
    if(page($parent)) {

      // fetch items from CSV file
      $items = $this->getItems();

      foreach($items as $item) {

        $data = $item;


        // check if the index $titleKey exists
        if(isset($item[$UIDKey])) {
            $folderName = str::slug($item[$UIDKey]);
        } else {
          throw new Exception("The index does not exists");
        }

        if(page($parent)->children()->findBy('uid', $folderName)) {

          if($update) {

            try {

              page($parent)->children()->findBy('uid', $folderName)->update($data);
              $messages[] = 'Success: ' . $folderName . ' was updated';

              } catch(Exception $e) {

                $messages[] = 'Error: ' . $folderName . ' ' . $e->getMessage();

              }

          } else {

            $messages[] = "The page " . $folderName . " already exists and may not be updated";

          }

        } else {

          // otherwise, create a new page
          try {

            $newPage = page($parent)->children()->create($folderName, $template, $data);
            $messages[] = 'Success: ' . $folderName . ' was created';

          } catch(Exception $e) {

            $messages[] = 'Error: ' . $folderName . ' ' . $e->getMessage();

          }
        }

      }

    } else {

      throw new Exception("The parent page does not exist.");

    }
    var_dump($messages);

  }

  public function createStructure($page, $field, $append = false) {
    //todo check if string given
    $page = page($page);
    if(is_a($page, 'Page')) {

      $items = $this->getItems();

      if($append === false) {

        $data = yaml::encode($items);

      } else {

        $data = $page->$field()->yaml();

        foreach($items as $item) {

          $data[] = $item;

        }

        $data = yaml::encode($data);

      }
        try {

          page($page)->update(array($field => $data));

        } catch(Exception $e) {

          return 'Error: ' . $e->getMessage();

        }
    } else {

        throw new Exception("Not a page given");

    }

  }

}
