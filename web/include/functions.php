<?php

function rndstr($length = 6){
    $str = "";
    while($length > 0){
        $str .= (rand() % 10);
        $length--;
    }
    return $str;
}

function encodeObject(&$a, $quote = ENT_COMPAT){
    foreach($a as &$v){
        $v = htmlspecialchars($v, $quote, "utf-8");
    }
}

function msgbox($msg, $htmlencode = true){
    global $installDir;
    @ob_clean();
    @ob_clean();
    if($htmlencode){
        $msg = htmlspecialchars($msg);
    }
    echo <<<eot
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
    a{color:blue;text-decoration:none;}
    a:hover{color:red;text-decoration:underline;}
    .msgbox{
        width: 500px;
        margin-top: 120px;
        border: 1px solid #00a7dd; 
        border-top: 1px solid #00a7dd; 
        text-align: center; 
    }
    .msgbox-title{
        font-weight: bold; 
        border-bottom: 1px solid #00a7dd; 
        background-color: #00a7dd;
        color: #fff;
        padding: 5px;
    }
    .msgbox-content{
        padding: 10px; 
        padding-bottom: 20px; 
        text-align: left;
    }
    .msgbox-bottom{
        border-top: 1px dashed #00a7dd; 
        padding: 5px;
    }
    </style>
</head>
<body align="center">
<center>

<div class="msgbox" align="center">
    <div class="msgbox-title">提示</div>
    <div class="msgbox-content">{$msg}</div>
    <div class="msgbox-bottom">
        <a href="{$_SERVER['HTTP_REFERER']}">返回上一页</a> | 
        <a href="{$installDir}/team_logout.php">注销并重新登录</a>
    </div>
</div>
</center>
</body>
</html>
eot;
    exit();
}

function select_school($school_id = 0, $type = -1, $force_none = 0, $add_high = 0){
    global $conn;
    $query = "SELECT * FROM `{tblprefix}_schools`";
    switch($type){
    case 1: //高校
        $query .= " WHERE `school_type` & 4 = 4 ";
        break;
    case 2: //非本校高校
        $query .= " WHERE `school_type` & 1 = 0 AND `school_type` & 4 = 4 ";
        break;
    case 3: //高中
        $query .= " WHERE `school_type` & 4 = 0 ";
        break;
    default:
        ;
    }
    $query .= " ORDER BY `school_id` ASC";
    $res = getQuery($conn, $query);
    $out =  <<<eot
<script language="javascript">
function select_other(){
    var t = document.getElementById("other_high");
    if(t) t.checked = true;
}
</script>
<select name="school_id" onchange="javascript:select_other()">
eot;
    if($force_none == 1 || $school_id <= 0){
        if($school_id <= 0) $selected = "selected=\"selected\"";
        else $selected = "";
        $out .= "<option $selected value=\"-1\">请选择学校(拼音顺序)</option>\n";
    }
    while($row = $res->fetch_assoc()){
        $id = $row['school_id'];
        $name = htmlspecialchars($row['school_name_cn']);
        if($id == $school_id) $selected = "selected=\"selected\"";
        else $selected = "";
        $out .= "<option $selected value=\"$id\">$name</option>\n";
    }
    if($addhigh != 0){
        $out .= "<option value=\"-1\">高中队伍</option>\n";
    }
    $out .= "</select>\n";
    return $out;
}

function time2str($timestamp = -1, $format = "Y-m-d H:i"){
    if($timestamp == -1) $timestamp = time();
    return date($format, $timestamp);
}

function str2time($str){
    list($Y, $m, $d, $H, $i, $s) = split(" |-|:", $str);
    return mktime($H, $i, $s, $m, $d, $Y);
}

function timestr2int($str){
    list($h, $m, $s) = split(":", $str);
    return ($h*3600 + $m*60 + $s);
}

function int2timestr($tm){
    $h = (int)($tm / 3600);
    $tm = $tm % 3600;
    $m = (int)($tm / 60);
    $s = $tm % 60;
    return "$h:$m:$s";
}

function sendvcode($team_id, &$info = NULL){
    $t = new team($team_id);
    if($t->errno)msgbox($t->error);
    $name = $t->team_name;
    $email = $t->email;
    $title = "acm/icpc比赛注册系统 - 邮箱验证";
    $content = <<<eot
{$name} 队:
    感谢注册我们的报名系统，贵队的邮箱验证码是 {$t->vcode}，请登录报名系统输入验证码。

--
此邮件为系统自动发出，不必回复；有疑问可直接回复。
若此邮件被投递至"垃圾箱"，请将此邮件地址加入通讯录(或白名单)，以免后续邮件被忽略。
eot;
    $content = nl2br(htmlspecialchars($content));
    $m = new mailer;
    if($m->email($name, $email, $title, $content)){
        $info = $m->ErrorInfo;
        return true;
    }else{
        $info = $m->ErrorInfo;
        return false;
    }
}

function ubb2html($str){
    $str = htmlspecialchars($str, ENT_NOQUOTES);
    $pattern = array(
        "/\[b\](.+?)\[\/b\]/is", //1
        "/\[i\](.+?)\[\/i\]/is", //2
        "/\[u\](.+?)\[\/u\]/is", //3
        "/\[sup\](.+?)\[\/sup\]/is", //4
        "/\[sub\](.+?)\[\/sub\]/is", //5
        "/\[center\](.+?)\[\/center\]/is", //6
        "/\[code\](.+?)\[\/code\]/is", //7
        "/\[quote\](.+?)\[\/quote\]/is", //8
        "/\[span style=\"(.*?)\"\](.+?)\[\/span\]/is", //9
        '/\[img\\s+src=\"(.+?)\"\]/is', //10
        '/\[img\\s+src=\"(.+?)\"\\s+width=\"(.+?)\"\]/is', //11
        '/\[img\\s+src=\"(.+?)\"\\s+height=\"(.+?)\"\]/is', //12
        '/\[img\\s+src=\"(.+?)\"\\s+width=\"(.+?)\"\\s+height=\"(.+?)\"\]/is', //13
        '/\[img\\s+src=\"(.+?)\"\\s+height=\"(.+?)\"\\s+width=\"(.+?)\"\]/is', //14
        '/\[a\\s+href=\"(.+?)\"\](.+?)\[\/a\]/is', //15
        );
    $replace = array(
        "<b>\\1</b>", //1
        "<i>\\1</i>", //2
        "<u>\\1</u>", //3
        "<sup>\\1</sup>", //4
        "<sub>\\1</sub>", //5
        "<center>\\1</center>", //6
        "<div class=\"code\">\\1</div>", //7
        "<div class=\"quote\">\\1</div>", //8
        "<span style=\"\\1\">\\2</span>", //9
        "<img border=\"0\" src=\"\\1\"/>", //10
        "<img border=\"0\" src=\"\\1\" width=\"\\2\"/>", //11
        "<img border=\"0\" src=\"\\1\" height=\"\\2\"/>", //12
        "<img border=\"0\" src=\"\\1\" width=\"\\2\" height=\"\\3\"/>", //13
        "<img border=\"0\" src=\"\\1\" width=\"\\3\" height=\"\\2\"/>", //14
        "<a href=\"\\1\" target=\"_blank\">\\2</a>", //15
        );
    $str = preg_replace($pattern, $replace, $str);
    $str = str_replace("  ", "&nbsp; ", $str);
    $str = str_replace("  ", " &nbsp;", $str);
    return nl2br($str);
}

function symbol2value($str, $team_id = -1){
    if($team_id == -1)
    {
        $team_id = 0;
        if (isset($_SESSION['team_id']))
            $team_id = (int)$_SESSION['team_id'];
    }
    $t = new team($team_id);
    if($t->errno){
        if(ereg("\{(team_id|vcode|team_name|password|telephone|school|final_id)\}", $str))
            return "Forbidden: 此日志需要登录后才能查看。";
        else
            return $str;
    }
    $str = str_replace('{team_id}', $t->team_id, $str);
    $str = str_replace('{team_name}', $t->team_name, $str);
    $str = str_replace('{vcode}', $t->vcode, $str);
    $str = str_replace('{password}', $t->password, $str);
    $str = str_replace('{telephone}', $t->telephone, $str);
    $str = str_replace('{final_id}', $t->final_id, $str);
    $sch = school::getNameByTeamId($t->team_id);
    $str = str_replace('{school}', $sch, $str);

    return $str;
}

function fixurl_callback($matches){
    global $installDir;
    $url = "";
    if(!ereg("(http|ftp)://", $matches[2])){
        $scheme_host = "http://" . $_SERVER['SERVER_NAME'];
        if(strpos($matches[2], $installDir) === 0){
            $url = $scheme_host . $matches[2];
        }else{
            $url = $scheme_host . $installDir ."/". $matches[2];
        }
    }
    return "{$matches[1]}=\"$url\"";
}

function fixurl($str){
    $pattern = "/(src|href)=\"(.*?)\"/is";
    $str = preg_replace_callback($pattern, "fixurl_callback", $str);
    return $str;
}

function upload_judge($name){
    $forbidden_exts = array("php", "php3", "asp", "jsp", "aspx");
    $pos = strrpos($name, ".");
    if($pos === false) return true;
    $ext = substr($name, $pos+1);
    if(in_array($ext, $forbidden_exts)) return false;
    return true;
}

function upload_file($postfile, &$res){
    if(isset($_FILES[$postfile])){
        $file = $_FILES[$postfile];
        $filename = $file['name'];
        $target = dirname(dirname(__FILE__));
        $target .= "/attachments/$filename";
        $res[0] = false;
        $res[1] = $target;
        $res[2] = $filename;
        if(upload_judge($filename) == false){
            $res[3] = "文件扩展名非法!";
            return false;
        }
        if(file_exists($target)){ 
            $res[3] = "文件已经存在!";
            return false;
        }
        if(move_uploaded_file($file['tmp_name'], $target) == false){
            $res[3] = "文件上传失败!";
            return false;
        }else{
            $res[0] = true;
            $res[3] = "文件上传成功";
            return true;
        }
    }else{
            $res[1] = "";
            $res[2] = "";
            $res[4] = "请指定上传文件";
            return false;
    }
}

function cutstr($str, $length = 60){
    if(strlen($str) < $length) return $str;
    $length -= 4;
    $pos = 0;
    $cutlen = 0;
    while (1){
        $char = substr($str, $pos, 1);
        if(ord($char) > 127){
            $cutlen += 2;
            $pos += 3;
            if($cutlen > $length){
                $pos -= 3;
                break;
            }
        }else{
            $cutlen += 1;
            $pos += 1;
            if($cutlen > $length){
                $pos -= 1;
                break;
            }
        } 
    }
    $t = substr($str, 0, $pos) . " ...";
    return $t;

}

function get_listbar(&$page, $items, $items_per_page, $href, $query = ""){
    $pages_c = ceil($items / $items_per_page);
    if($page < 1) $page = 1;
    else if($page > $pages_c) $page = $pages_c;
    $start = ($page - 1) * $items_per_page;
    $out = <<<eot
<span class="listbar">
<a href="{$href}?{$query}">首页</a>
eot;
    if($page > 1){
        $pre_page = $page - 1;
        $out .= " <a href=\"{$href}?page={$pre_page}&{$query}\">&lt;&lt;</a> \n";
    }else{
        $out .= " &lt;&lt; \n";
    }
    for($i = 1; $i <= $pages_c; $i++){
        if($page == $i)
            $out .= "$i ";
        else
            $out .= "<a href=\"{$href}?page=$i&{$query}\">$i</a>\n";
    }
    if($page < $pages_c){
        $next_page = $page + 1;
        $out .= " <a href=\"{$href}?page={$next_page}&{$query}\">&gt;&gt;</a> \n";
    }else{
        $out .= " &gt;&gt; \n";
    }
    $out .= "<a href=\"{$href}?page=$pages_c&{$query}\">末页</a>\n</span>\n";
    return $out;
}


function select_priority($p = 3){
    $s[$p] = "selected=\"selected\"";
    return <<<eot
<select name="priority">
<option {$s[5]} value="5">最高</option>
<option {$s[4]} value="4">高</option>
<option {$s[3]} value="3">普通</option>
<option {$s[2]} value="2">低</option>
<option {$s[1]} value="1">最低</option>
</select>

eot;
}
?>
