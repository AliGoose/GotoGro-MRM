<?php
//placed on "locked content", in order to block content access from unregistered users.
//classic componentization approach enforced. (setting $redirect var on GET, setting sessVar 'Message' for failure info)
session_start();

//set array assocs for specially enforced pages (filename only, no dirs, no ext). needs to be manually updated as site is expanded on
//variable variable ($$session_acctype) points back to here
//serves as a whitelist of sorts
$staff= array('remove_member', 'add_member', 'add_sales');


//set a fixed redir loc for any redirect events
$redirloc= './index.php';
$dialogLocation = './io/dialog-message.php';

//phase 1 check: check if sessVar 'user' is NOT present. lightweight option before segmentation check 
//completely blocks out parts of the site not meant for unregistered users
//this entire script is optional, so this check is a simple #require on pages that only need it
if (!isset($_SESSION["user"])) {
    $_SESSION["message"]= "Restricted content. <br>You have to log-in before you could proceed.";
    $redirect= "../login.php";
    header("Location: $dialogLocation?redirect={$redirect}");
}

//phase 2 check: enforce user privilege segmentation

//regexp to pick out the filename (no extension) to prepare for the filter comparison
//php_self when #required, returns filename of the current page
//this can be took advantage of
preg_match('/[ \w-]+?(?=\.)/', $_SERVER['PHP_SELF'], $currentpage);


//switches var session_acctype according to id
if($_SESSION["usrtype"]== 1) {$session_acctype= 'manager';}
    elseif ($_SESSION["usrtype"]== 2) {$session_acctype= 'customer';}

?>



