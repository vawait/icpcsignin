<?php
include("def.php");

include(APP_ROOT."team/inc.php");
include(APP_ROOT."team/verifiedmail.php");

$team_id = (int)$_SESSION['team_id'];

$m = new member;

if(get_magic_quotes_gpc()){
    foreach($_POST as &$value){
        $value = stripslashes($value);
    }
}
extract($_POST, EXTR_SKIP);

$type = (int) $_GET['type'];

$query = "SELECT * FROM `{tblprefix}_members` WHERE `team_id`={$_SESSION['team_id']} AND `type`={$type}";
$res = getQuery($conn, $query);
if($type == 0 || $type == 2){
    if($conn->affected_rows >= 1)
        msgbox("已达到上限");
}else if($type == 1){
    if($conn->affected_rows >= 2)
        msgbox("已达到上限");
}else{
    msgbox("错误的人员类型！请不要恶意提交，谢谢合作！");
}


$m->type = $type;
$m->team_id = $_SESSION['team_id'];
$m->stu_number = $stu_number;
$m->member_name = $member_name;
$m->member_name_pinyin = $member_name_pinyin;
$m->gender = $gender;
$m->school_id = $school_id;
$m->faculty_major = $faculty_major;
$m->grade_class = $grade_class;
$m->email = $email;
$m->telephone = $telephone;
$m->remark = $remark;

if($m->insert()){
    msgbox("添加成功!");
}else{
    msgbox("添加失败: " . $m->error);
}

include(APP_ROOT."include/footer.php");
?>
