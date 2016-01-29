<?php

/*
 *  Version
 */

  $bbsVersion = "v1.1.0";

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

  function h($str) {
    return str_replace("\n", "<br />", $str);
  }

  function build_message_file_path($message_id) {
    global $bbsDataDir;
    return "$bbsDataDir/" . sprintf("%04d", $message_id) . ".mes";
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
      $this->timestamp = date('Y-m-d H:i:s');
      $this->content = $content;
      $this->comments = array();
    }

    function save($file) {
      $fp = fopen($file, 'wb');
      if ($fp) {
        if (flock($fp, LOCK_EX)) {
          fwrite($fp, "==== message\n");
          fwrite($fp, "id: " . $this->id . "\n");
          fwrite($fp, "name: " . $this->name . "\n");
          fwrite($fp, "subject: " . $this->subject . "\n");
          fwrite($fp, "timestamp: " . $this->timestamp . "\n");
          fwrite($fp, $this->content);

          foreach ($this->comments as $comment) {
            fwrite($fp, "\n\n---- comment\n");
            fwrite($fp, "id: " . $comment->id . "\n");
            fwrite($fp, "name: " . $comment->name . "\n");
            fwrite($fp, "timestamp: " . $comment->timestamp . "\n");
            fwrite($fp, $comment->content . "\n");
          }

          flock($fp, LOCK_UN);
        }
      }
      fclose($fp);
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
      $timestamp = array_shift($lines);
      $timestamp = mb_ereg_replace("timestamp: ", "", $timestamp);
      $this->timestamp = $timestamp;
      foreach ($lines as $line) {
        $this->content = $this->content . $line . "\n";
      }
      $comments = array_map('comment_parse', $items);
      $this->comments = $comments;
    }

    function add_comment($comment) {
      $c = count($this->comments);
      if ($c > 0) {
        $last_comment_id = $this->comments[$c - 1]->id;
        $comment_id = $last_comment_id + 1;
      } else {
        $comment_id = 1;
      }
      $comment->id = $comment_id;
      array_push($this->comments, $comment);
    }
  }   // end of class Message

  function message_load($file) {
    $message = new Message();
    $message->load_file($file);
    return $message;
  }


  // Comment class
  class Comment {
    public $id;
    public $name;
    public $timestamp;
    public $content = "";

    function __construct($name = '', $content = '') {
      $this->name = $name;
      $this->timestamp = date('Y-m-d H:i:s');
      $this->content = $content;
    }

    function parse($str) {
      $str = rtrim($str);
      $lines = explode("\n", $str);
      $id = array_shift($lines);
      $id = mb_ereg_replace("id: ", "", $id);
      $this->id = $id;
      $name = array_shift($lines);
      $name = mb_ereg_replace("name: ", "", $name);
      $this->name = $name;
      $timestamp = array_shift($lines);
      $timestamp = mb_ereg_replace("timestamp: ", "", $timestamp);
      $this->timestamp = $timestamp;
      foreach ($lines as $line) {
        $this->content = $this->content . $line . "\n";
      }
    }
  }   // end of class Comment

  function comment_parse($str) {
    $comment = new Comment();
    $comment->parse($str);
    return $comment;
  }

?>
