<?php
include("include/header.php");
include("include/classes.php");
include("include/config.php");
echo <<<eot
<table width="1024" align="center">
<tr>
<td>
<div id="header">
title, or images.
    <div id="menu">
        <ul>
<li><a href="index.php">首页</a></li>

eot;

if(!isset($_SESSION['team_id'])){
    echo '<li><a href="team/register.php">注册</a></li>';
}else{
    echo '<li><a href="team/index.php">管理</a></li>';
}

echo <<<eot

<li><a href="http://acm.whu.edu.cn/" target="_blank">WHUACM</a></li>
<li><a href="http://acm.whu.edu.cn/woj" target="_blank">WOJ</a></li>

        </ul>
    </div>
</div>
<div id="wrapper">
    <div id="mainWrapper">

eot;
//文章

$itemsperpage = 5;
$nowtime = time();
$query = "SELECT COUNT(*) FROM `{tblprefix}_articles` WHERE `pub_time` < $nowtime";
$res = getQuery($conn, $query);
$row = $res->fetch_array();

$articles_c = (int)$row[0];
$pages_c = ceil($articles_c / $itemsperpage);

$page = (int)$_GET['page'];
if($page < 1) $page = 1;
else if($page > $pages_c) $page = $pages_c;

$start = ($page - 1) * $itemsperpage;

ob_start();
echo <<<eot
<div class="listbar">
<a href="index.php">首页</a>
eot;
if($page > 1){
    $pre_page = $page - 1;
    echo " <a href=\"index.php?page=$pre_page\">&lt;&lt;</a> \n";
}else{
    echo " &lt;&lt; \n";
}
for($i = 1; $i <= $pages_c; $i++){
    if($page == $i)
        echo "$i ";
    else
        echo "<a href=\"index.php?page=$i\">$i</a>\n";
}
if($page < $pages_c){
    $next_page = $page + 1;
    echo " <a href=\"index.php?page=$next_page\">&gt;&gt;</a> \n";
}else{
    echo " &gt;&gt; \n";
}
echo "<a href=\"index.php?page=$pages_c\">末页</a>\n";
echo "共{$pages_c}页{$articles_c}篇\n</div>\n";

$bar = ob_get_contents();


$query = "SELECT * FROM `{tblprefix}_articles` "
        ."  WHERE `pub_time` < $nowtime"
        ."  ORDER BY `priority` DESC, `pub_time` DESC"
        ."  LIMIT $start, $itemsperpage";
$res = getQuery($conn, $query);

while($row = $res->fetch_assoc()){
    extract($row, EXTR_OVERWRITE);
    $title = htmlspecialchars($title);
    $pubtime = time2str($pub_time, "Y-m-d H:i");
    date_default_timezone_set("PRC");
    if($pub_time > time()) continue; //定时发布文章, 不显示
    if(time() - $pub_time < 3 * 86400) $newsign = '<span style="color:red;">[NEW!]</span>';
    else $newsign = "";
    if($permission == 0 && !isset($_SESSION['team_id']))
        $content = 'Forbidden: <i>本文需要登陆后才可查看</i>';
    else{
        switch($content_type){
        case 0: //PLAIN
            $content = htmlspecialchars($content);
            $content = "<pre class=\"pre_content\">$content</pre>";
            break;
        case 1: //HTML
            break;
        case 2: //UBB
        default:
            $content = ubb2html($content);
        }
    }
    echo <<<eot
<div class="textbox">
    <div class="textbox-title">[$pubtime] {$title} $newsign</div>
    <div class="textbox-content">$content</div>
</div>

eot;
}

echo $bar;

echo <<<eot
        </div>
    </div>

    <div style="display: block;" id="sidebar" class="sidebar">
        <div id="innerSidebar">

eot;

$notice = @file_get_contents("include/notice.txt");
echo <<<eot
<div class="textbox">
<div class="textbox-title" style="text-align:center;">公告</div>
<div class="textbox-content">$notice</div>
</div>

eot;

//右列表
if(isset($_SESSION['team_id'])){
    include("team_index.php");
}else{
    echo <<<eot
        <div class="textbox">
            <div class="textbox-title">队伍登录</div>
            <div class="textbox-content">
                <form action="login.do.php" method="post" style="display:inline;">
                <table style="display:inline;">
                <tr>
                    <td>队名</td>
                    <td><input type="text" name="team_name" style="width:120px;"/></td>
                </tr>
                <tr>
                    <td>密码</td>
                    <td><input type="password" name="password" style="width:120px;"/></td>
                </tr>
                <tr>
                    <td colspan="2">
                    <input type="submit" value="登陆"/>
                    <a href="team/register.php">注册新队伍!</a>
                    </td>
                </tr>
                </table>
                </form>
                <div><a href="admin/">管理入口</a></div>
            </div>
        </div>
eot;
}

include("include/links.html");

echo <<<eot
        </div>
    </div>
</div>
</td>
</tr>
</table>
<hr width="1010"/>
<script src="include/hl.js"></script>
<script>
highlighter();
</script>
eot;
include("include/footer.php");
?>