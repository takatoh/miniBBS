<?php
  require_once 'bbs.php';
  require_once 'config.php';

  $message_file = build_message_file_path($_GET['message_id']);
  $message = new Message();
  $message->load_file($message_file);
?>
<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?php echo $bbsTitle; ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1><a href="index.php"><?php echo $bbsTitle; ?></a></h1>
      </div>

      <div id="main">
        <div class="message">
          <span>[<?php echo $message->id; ?>]</span> <span><?php echo $message->subject; ?></span><br />
          <span><?php echo $message->name; ?></span><br />
          <p>
            <?php echo h($message->content); ?>
          </p>
          <div class="message_footer">
            <?php echo $message->timestamp; ?>
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

        <div id="comment_form">
          <form method="POST" action="index.php">
            <input type="hidden" name="message_id" value="<?php echo $message->id; ?>"/>
            <table>
              <tr><td class="col1">Name:</td><td class="col2"><input type="text" name="name" placeholder="Your name" /></td></tr>
              <tr><td class="col1">Comment:</td><td class="col2"><textarea name="content" rows=8 cols=50 /></textarea></td></tr>
              <tr><td class="col1"></td><td><input type="submit" value="Post" /></td></tr>
            </table>
          </form>
        </div>
      </div>

      <div id="footer">
        <span>miniBBS <?php echo $bbsVersion; ?></span>
      </div>

    </div>
  </body>
</html>
