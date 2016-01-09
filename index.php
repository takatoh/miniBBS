<?php
  require_once 'bbs.php';
  require_once 'config.php';

  $dir = opendir($bbsDataDir);
  $files = array();

  while (false !== ($f = readdir($dir))) {
    $f = "$bbsDataDir/" . $f;
    if (is_file($f)) {
      array_push($files, $f);
    }
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['message_id'])) {
      $message_file = "$bbsDataDir/" . sprintf("%04d", $_POST['message_id']) . ".mes";
      $message = new Message();
      $message->load_file($message_file);
      $comment = new Comment($_POST['name'], $_POST['content']);
      $message->add_comment($comment);
      $message->save($message_file);
    } else {
      if (mb_strlen(rtrim($_POST['content'])) > 0) {
        if (count($files) > 0) {
          rsort($files);
          $matches = array();
          preg_match("!$bbsDataDir/(.+)\.mes!", $files[0], $matches);
          $last_message_id = $matches[1];
          $new_message_id = $last_message_id + 1;
        } else {
          $new_message_id = 1;
        }
        $new_message_file = "$bbsDataDir/" . sprintf("%04d", $new_message_id) . ".mes";

        $message = new Message($new_message_id, $_POST['name'], $_POST['subject'], $_POST['content']);
        $message->save($new_message_file);

        array_push($files, $new_message_file);
      }
    }
  }

  $files = files_sort_by_mtime($files);
  if (isset($_GET['page'])) {
    $offset = ($_GET['page'] - 1) * $bssMessagePerPage;
  } else {
    $offset = 0;
  }
  $files = array_slice($files, $offset, $bssMessagePerPage);
  $messages = array_map("message_load", $files);
?>
<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?php echo $bbsTitle ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1><a href="index.php"><?php echo $bbsTitle ?></a></h1>
      </div>

      <div id="main">
        <div id="post_form">
          <form method="POST" action="index.php">
            <table>
              <tr><td class="col1">Name:</td><td class="col2"><input type="text" name="name" value="no name" /></td></tr>
              <tr><td class="col1">Subject:</td><td class="col2"><input type="text" name="subject" value="untitled" /></td></tr>
              <tr><td class="col1">Message:</td><td class="col2"><textarea name="content" rows=8 cols=50 /></textarea></td></tr>
              <tr><td class="col1"></td><td><input type="submit" value="Post" /></td></tr>
            </table>
          </form>
        </div>

        <div id="messages">
            <?php foreach ($messages as $message) { ?>
            <div class="message">
              <span>[<?php echo $message->id ?>]</span> <span><?php echo $message->subject ?></span><br />
              <span><?php echo $message->name ?></span><br />
              <p>
                <?php echo h($message->content) ?>
              </p>
              <div class="message_footer">
                <?php echo $message->timestamp ?>
                <span><a href="comment.php?message_id=<?php echo $message->id ?>">Reply</a></span>
              </div>
              <?php foreach ($message->comments as $comment) { ?>
                <div class="comment">
                  <span><?php echo $comment->id ?>.</span> <span><?php echo $comment->name ?></span><br />
                  <p>
                    <?php echo h($comment->content) ?>
                  </p>
                  <div class="comment_footer"><?php echo $comment->timestamp ?></div>
                </div>
              <?php } ?>
            </div>
            <?php } ?>
          </div>
        </div>

        <div id="footer">
          <span>mini BBS</span>
        </div>

      </div>
    </dib>
  </body>
</html>
