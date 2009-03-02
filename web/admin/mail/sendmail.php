<?php
set_time_limit(0);
include("def.php");

include(APP_ROOT."admin/inc.php");

if (get_magic_quotes_gpc()){
    foreach($_POST as &$v){
        $v = stripslashes($v);
    }
}
extract($_POST, EXTR_SKIP);

$query = "SELECT `team_id`, `team_name`, `email` FROM `{tblprefix}_teams` ";
switch($team_type){
case 'verified':
    $option = "  WHERE `vcode`=''";
    break;
case 'notverified':
    $option = "  WHERE `vcode`!=''";
    break;
case 'nomember':
    $query = <<<eot
select a.`team_id` as team_id, a.`team_name` as `team_name`, a.email as email, count(b.member_id) as num 
  from {tblprefix}_teams a left join {tblprefix}_members b 
    on a.team_id=b.team_id 
  group by a.team_id 
  having num=0 
  order by a.team_id asc
eot;
    $option = "";
    break;
case 'final':
    $option = "  WHERE `final_id`>=0";
    break;
case 'all':
    $option = "";
    break;
}
$query .= $option;

$res = getQuery($conn, $query);
if($conn->affected_rows == 0){
    msgbox("没有符合指定条件的队伍");
}

$title_l = mb_strlen($title, "UTF-8");
if($title_l > 15){
    msgbox("邮件标题太长（不能超过15字）");
}

$m = new mailer;
$template = $content;
while($row = $res->fetch_assoc()){
    $team_id = $row['team_id'];
    $content = symbol2value($template, $team_id);
    switch($content_type){
    case 0: // plain
        $content = "<pre>" . htmlspecialchars($content) . "</pre>\n";
        break;
    case 1: // html
        $content = fixurl($content);
        break;
    case 2: // UBB
    default:
        $content = ubb2html($content);
        $content = fixurl($content);
        break;
    }
    $email = $row['email'];
    echo "<div style=\"text-align:left;\">\n";
    echo "Send to {$row['team_name']}($email): ";
    if($m->email($row['team_name'], $email, $title, $content)){
        echo "OK<br/>\n";
    }else{
        echo "Fail{$m->ErrorInfo}<br/>\n";
    }
}

include(APP_ROOT."include/footer.php");
?>
