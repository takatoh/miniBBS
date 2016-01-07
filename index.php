<?php
  $dir = opendir('data');
  $files = array();

  while (false !== ($f = readdir($dir))) {
    $f = 'data/' . $f;
    if (is_file($f)) {
      array_push($files, $f);
    }
  }

  $files = files_sort_by_mtime($files);
  $messages = array_map("message_new", $files);


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


  // Message class
  class Message {
    public $id;
    public $name;
    public $subject;
    public $content = "";

    function __construct($file) {
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

  function message_new($file) {
    return new Message($file);
  }
?>
<html>
  <head>
    <title>mini BBS</title>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1>mini BBS</h1>
      </div>

      <div id="main">
        <div id="post_form">
          <form method="POST" action="index.php">
            Name: <input type="text" name="name" value="no name" /><br />
            Subject: <input type="text" name="subject" value="untitled" /><br />
            Content: <textarea name="content" rows=8 cols=50 /></textarea><br />
            <input type="submit" value="Post" />
          </form>
        </div>

        <div id="messages">
            <?php foreach ($messages as $message) { ?>
            <div class="message">
              <span>[<?php echo $message->id ?>]</span> <span><?php echo $message->subject ?></span><br />
              <span><?php echo $message->name ?></span><br />
              <p>
                <?php echo $message->content ?>
              </p>
              <hr />
            </div>
            <?php } ?>
          </div>
        </div>

      </div>
    </dib>
  </body>
</html>
