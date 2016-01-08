<?php
  require_once 'bbs.php';

  $message_file = "data/" . sprintf("%04d", $_GET['message_id']) . ".mes";
  $message = new Message();
  $message->load_file($message_file);
?>
<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>mini BBS</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1><a href="index.php">mini BBS</a></h1>
      </div>

      <div id="main">
        <div class="message">
          <span>[<?php echo $message->id ?>]</span> <span><?php echo $message->subject ?></span><br />
          <span><?php echo $message->name ?></span><br />
          <p>
            <?php echo $message->content ?>
          </p>
          <div class="message_footer">
            <?php echo $message->timestamp ?>
          </div>
        </div>

        <div id="post_form">
          <form method="POST" action="index.php">
            <input type="hidden" name="message_id" value="<?php echo $message->id ?>"/>
            <table>
              <tr><td class="col1">Name:</td><td class="col2"><input type="text" name="name" value="no name" /></td></tr>
              <tr><td class="col1">Comment:</td><td class="col2"><textarea name="content" rows=8 cols=50 /></textarea></td></tr>
              <tr><td class="col1"></td><td><input type="submit" value="Post" /></td></tr>
            </table>
          </form>
        </div>
      </div>


        <div id="footer">
          <span>mini BBS</span>
        </div>

      </div>
    </dib>
  </body>
</html>
