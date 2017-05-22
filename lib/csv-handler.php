<?php
namespace SonjaBroda;

use Str;
use Exception;
use Yaml;


class CsvHandler {

  private $items = array();
  private $file;
  private $parse_header;
  private $header;
  private $delimiter;
  private $length;

  function __construct($filepath, $parse_header = false, $delimiter = ',', $length='8000') {

    if(file_exists($filepath)) {

      $this->file = fopen($filepath, "r");
      $this->parse_header = $parse_header;
      $this->delimiter = $delimiter;
      $this->length = $length;

      if ($this->parse_header) {
        $this->header = fgetcsv($this->file, $this->length, $this->delimiter);
      }
    } else {

      throw new Exception('The file does not exist');

    }

  }


  function getHeader() {
    if($this->header) {
      return $this->header;
    } else {
      return false;
    }
  }

  function __destruct() {
    if ($this->file) {
      fclose($this->file);
    }
  }

  function getItems($maxLines=0) {

    //if $maxLines is set to 0, then get all the data

    $data = array();

    if ($maxLines > 0)
    $lineCount = 0;
    else
    $lineCount = -1; // so loop limit is ignored

    while ($lineCount < $maxLines && ($row = fgetcsv($this->file, $this->length, $this->delimiter)) !== false) {

      if ($this->parse_header) {
        foreach ($this->header as $i => $heading_i) {
          $row_new[$heading_i] = $row[$i];
        }
        $data[] = $row_new;
      } else {
        $data[] = $row;
      }

      if ($maxLines > 0)
      $lineCount++;
    }
    return $data;
  }


  public function createPages($parent, $UIDKey, $template = 'default', $update = false) {

    $messages = array();

    if(is_a($parent, 'Page')) {
      $page = $parent;
    } else {
      $page = page($parent);
    }

    if($page) {
      // fetch items from CSV file
      $items = $this->getItems();

      foreach($items as $item) {

        $data = $item;
        // check if the index $UIDKey exists

        if(isset($item[$UIDKey])) {

          // Check if $UIDKey starts with a number
          if(ctype_digit(substr($UIDKey, 0, 1))) {
            $UIDKey = '_' . $UIDKey;
          }
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
            if(c::get('csv-handler.page.sort')) {
              try {
                $newPage->sort('last');
              } catch(Exception $e) {
                $messages[] = 'Error: The ' . $folderName . ' could not be sorted';
              }
            }
            $messages[] = 'Success: ' . $folderName . ' was created';

          } catch(Exception $e) {

            $messages[] = 'Error: ' . $folderName . ' ' . $e->getMessage();

          }
        }

      }

    } else {

      throw new Exception("The parent page does not exist.");

    }
    if(!empty($messages)) {
      $html = '';
      foreach($messages as $message) {
        $html .= '<div>' . $message . '</div>';
      }
      echo $html;
    }

  }

  public function createStructure($uri, $field, $append = false) {

    if(is_a($uri, 'Page')) {

      $page = $uri;

    } else {

      $page = page($uri);

    }

    if($page) {

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
          $messages[] = 'Success: The field "' . $field . '" was created/updated';

        } catch(Exception $e) {

          $messages[] = 'Error: The field "' . $field . '" could not be created/updated';

        }
    } else {

        $messages[] = " Error: The page does not exist";

    }
    if(!empty($messages)) {
      $html = '';
      foreach($messages as $message) {
        $html .= '<div>' . $message . '</div>';
      }
      echo $html;
    }
  }

  public function createUsers() {

    $_users = $this->getItems();

    $users = array();
    foreach($_users as $user) {
      $userName =  str::lower($user['Firstname'] . '-' . $user['Lastname']);
      $user['firstName'] = $user['Firstname'];
      $user['emails'] = $user['Email'];
      $user['username'] = $userName;
      $user['password'] = str_rot13($userName);
      $users[] = $user;
    }
    foreach($users as $key => $user) {

      try {

        $newUser = kirby()->site()->users()->create($user);

        $messages[] = 'User “'. $user['username'] .'” has been created.';
        //$response['counterSuccess'] ++;
      } catch(Exception $e) {

        try {

          $isUser = kirby()->site()->user($user['username'])->update($user);
          $messages[] = 'User “'. $user['username'] .'” has been updated.';
          //$response['counterUpdate'] ++;

        } catch(Exception $e) {

          $messages[] = 'User “'. $user['username'] .'” could not be created nor updated:' . "\n" . $e->getMessage();
          //$response['counterFailure'] ++;
        }

      }

    }

    if(!empty($messages)) {
      $html = '';
      foreach($messages as $message) {
        $html .= '<div>' . $message . '</div>';
      }
      echo $html;
    }
  }

}
