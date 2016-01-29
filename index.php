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
      if (mb_strlen(rtrim($_POST['content'])) > 0) {
        $message_file = build_message_file_path($_POST['message_id']);
        $message = new Message();
        $message->load_file($message_file);
        $name = $_POST['name'];
        $name |= 'no name';
        $comment = new Comment($name, $_POST['content']);
        $message->add_comment($comment);
        $message->save($message_file);
      }
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
        $new_message_file = build_message_file_path($new_message_id);
        $name = $_POST['name'];
        $name |= 'no name';
        $subject = $_POST['subject'];
        $subject |= 'untitled';
        $message = new Message($new_message_id, $name, $subject, $_POST['content']);
        $message->save($new_message_file);
        array_push($files, $new_message_file);
      }
    }
  }

  $files = files_sort_by_mtime($files);
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $offset = ($page - 1) * $bbsMessagePerPage;
  } else {
    $page = 1;
    $offset = 0;
  }
  $total_pages = ceil(count($files) / $bbsMessagePerPage);
  $files = array_slice($files, $offset, $bbsMessagePerPage);
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
              <tr><td class="col1">Name:</td><td class="col2"><input type="text" name="name" placeholder="Your name" /></td></tr>
              <tr><td class="col1">Subject:</td><td class="col2"><input type="text" name="subject" placeholder="Some subject" /></td></tr>
              <tr><td class="col1">Message:</td><td class="col2"><textarea name="content" rows=8 cols=50 /></textarea></td></tr>
              <tr><td class="col1"></td><td><input type="submit" value="Post" /></td></tr>
            </table>
          </form>
        </div>

        <div id="messages">
          <?php foreach ($messages as $message) { ?>
          <div class="message">
            <span>[<?php echo $message->id; ?>]</span> <span><?php echo $message->subject; ?></span><br />
            <span><?php echo $message->name; ?></span><br />
            <p>
              <?php echo h($message->content); ?>
            </p>
            <div class="message_footer">
              <?php echo $message->timestamp; ?>
              <span><a href="comment.php?message_id=<?php echo $message->id; ?>">Reply</a></span>
            </div>
            <?php foreach ($message->comments as $comment) { ?>
              <div class="comment">
                <span><?php echo $comment->id; ?>.</span> <span><?php echo $comment->name; ?></span><br />
                <p>
                  <?php echo h($comment->content); ?>
                </p>
                <div class="comment_footer"><?php echo $comment->timestamp; ?></div>
              </div>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
      </div>

      <div id="page_navi">
        <?php if ($page > 1) { echo "<span><a href=\"index.php?page=", $page - 1, "\">&#171; Prev</a> </span>";} ?>
        <?php echo "<span>[ ", $page, " / ", $total_pages, " ]</span>"; ?>
        <?php if ($page < $total_pages) { echo "<span> <a href=\"index.php?page=", $page + 1, "\">Next &#187;</a></span>";} ?>
      </div>

      <div id="footer">
        <span>miniBBS <?php echo $bbsVersion; ?></span>
      </div>

    </div>
  </body>
</html>
