<?php
/*
CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license
*/
if (!defined('CCADMIN')) {echo 'NO DICE';exit;}

function index() {
    global $body, $ts;

    $query = sql_query('admin_getAnnouncements');
    if (defined('DEV_MODE') && DEV_MODE == '1') {
        echo sql_error($GLOBALS['dbh']);
    }

    $announcementlist = '';
    while ($announcement = sql_fetch_assoc($query)) {
        $time = datify($announcement['time']);
        $announcement['announcement'] = utf8_decode($announcement['announcement']);
        $announcement['announcement'] = str_replace('\r\n', '<br> ', $announcement['announcement']);
        $announcementlist .= '<tr><td>'.($announcement['announcement']).'</td><td>'.$time[0].'</td><td align="center"><a style="color:red;" data-toggle="tooltip" title="Delete Announcement" href="?module=announcements&action=deleteannouncement&data='.$announcement['id'].'&amp;ts={$ts}"><i class="fa fa-lg fa-minus-circle"></i></a></td></tr>';
    }

    $errormessage = '';

    if (!$announcementlist) {
        $errormessage = '<tr><td colspan="3">You do not have any announcements at the moment!</td></tr>';
    }

$body = <<<EOD
  <div class="row">
  <div class="col-sm-8 col-lg-8">
    <div class="card">
      <div class="card-header">
        Announcements
      </div>
      <div class="card-block">
        <table class="table">
          <thead>
            <tr>
              <th width="70%">Announcement</th>
              <th width="30%">Time</th>
              <th width="10%">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            {$errormessage} {$announcementlist}
          </tbody>
        </table>
    </div>
    </div>
  </div>
  <div class="col-sm-4 col-lg-4">
    <div class="card">
      <div class="card-header">
        Add New Announcement
      </div>
      <div class="card-block">
        <form action="?module=announcements&action=newannouncementprocess&ts={$ts}" method="post" enctype="multipart/form-data">
          <div class="form-group row">
            <div class="col-md-12">
              <textarea required="true" id="textarea-input" name="announcement" rows="9" class="form-control" placeholder="Type your announcement here..."></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-8 form-control-label">
              Show only to logged-in users?
            </label>
            <label class="">
              <div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" id="inline-radio1" name="sli" value="1" checked></div><span style="padding-left:21px;">Yes</span>
            </label>
             <label class="">
              <div style="position:relative;"><input style="position:absolute;top:4px;left:4px;" type="radio" id="inline-radio2" name="sli" value="0"></div><span style="padding-left:24px;">No</span>
            </label>
          </div>
          <div class="form-actions">
            <input type="submit" value="Add Announcement" class="btn btn-primary">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
EOD;

    template();
}

function deleteannouncement()
{
    global $ts;

    if (!empty($_GET['data'])) {
        $query = sql_query('admin_deleteAnnouncements',array('id'=>sanitize_core($_GET['data'])));
        removeCache('latest_announcement');
    }
    $_SESSION['cometchat']['error'] = 'Announcement deleted successfully!';
    header("Location:?module=announcements&ts={$ts}");
}

function newannouncementprocess()
{
    global $ts;

    $zero = '0';

    if ($_POST['sli'] == 0) {
        $zero = '-1';
    }

    $message = sql_real_escape_string($_POST['announcement']);
    $message = str_replace(
        array('\r\n','\\'),
        array('<br>', ''),
        $message
    );
    $sent = sql_real_escape_string(getTimeStamp());
    $zero = sql_real_escape_string($zero);
    $message = preg_replace('/<a(.*?)>/', '<a$1 target="_blank">', $message);

    if (strpos($message, '<img') === false) {
        $img = strpos($message, '<img') + strlen('<img');
        $img = strpos(substr($message, $img, strpos($message, '>', $img) - $img), 'http');
        if ($img === false) {
            $reg_exUrl = "/<a.*?<\/a>(*SKIP)(*F)|(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            if (preg_match($reg_exUrl, $message, $url)) {
                $message = preg_replace($reg_exUrl, '<a href='.$url[0]." target=\"_blank\">$url[0]</a>", $message);
            }
        }
    }

    $message = utf8_encode($message);
    $query = sql_query('admin_insertAnnouncements',array('announcement'=>$message, 'time'=>$sent, 'to'=>$zero));
    $insertedid = sql_insert_id('cometchat_announcements');
    pushMobileAnnouncement($zero, $sent, $message, 1, $insertedid);

    removeCache('latest_announcement');
    $_SESSION['cometchat']['error'] = 'Announcement added successfully!';
    header("Location: ?module=announcements&ts={$ts}");
}
