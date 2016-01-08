<?php

/*
 *  Utilities
 */

  function files_sort_by_mtime($files) {
    usort($files, "order_by_mtime");
    return $files;
  }

  function order_by_mtime($a, $b) {
    $mtime_a = filemtime($a);
    $mtime_b = filemtime($b);
    if ($mtime_a > $mtime_b) {
      return -1;
    } else if ($mtime_a < $mtime_b) {
      return 1;
    } else {
      return 0;
    }
  }


/*
 *  Classes
 */

  // Message class
  class Message {
    public $id;
    public $name;
    public $subject;
    public $content = "";

    function __construct($id = 0, $name = '', $subject = '', $content = '') {
      $this->id = $id;
      $this->name = $name;
      $this->subject = $subject;
//      $this->timestamp = date('Y-m-d H:i:s');
      $this->content = $content;
      $this->comments = array();
    }

    function load_file($file) {
      $contents = file_get_contents($file);
      $items = explode("---- comment\n", $contents);
      $mes = rtrim(array_shift($items));
      $lines = explode("\n", $mes);
      array_shift($lines);
      $id = array_shift($lines);
      $id = mb_ereg_replace("id: ", "", $id);
      $this->id = $id;
      $name = array_shift($lines);
      $name = mb_ereg_replace("name: ", "", $name);
      $this->name = $name;
      $subject = array_shift($lines);
      $subject = mb_ereg_replace("subject: ", "", $subject);
      $this->subject = $subject;
      foreach ($lines as $line) {
        $this->content = $this->content . $line . "<br>";
      }
    }
  }

  function message_load($file) {
    $message = new Message();
    $message->load_file($file);
    return $message;
  }

?>
