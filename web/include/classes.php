<?php
/*
 * 包含配置文件
 */
$relpath = dirname(__FILE__);
include($relpath."/def.php");

include_once(APP_ROOT."include/config.php");

date_default_timezone_set("PRC");

/*
 * 连接数据库
 */

unset($conn);
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if(!$conn){
    ob_clean();
    die("Unable to connect to MySQL Server");
}

/*
 * getQuery(Resource MySQLi, String SQLcommand)
 * 进行SQL查询，自动替换表前缀，查询后进行错误检测
 * 如无误则返回查询结果集
 */
function getQuery($conn, $query){
    global $tblprefix;
    $query = str_replace("{tblprefix}", "$tblprefix", $query);
    $result = $conn->query($query);
    if($conn->errno){
        ob_clean();
        //msgbox($conn->error);
        msgbox($conn->error . "\n" . $query);
    }
    return $result;
}

/*
 * 抽象表类，包含4个成员
 */
abstract class table{
    abstract protected function getById($id);
    abstract protected static function delById($id);
    abstract protected function insert();
    abstract protected function update();
}

// 学校表
include_once("$relpath/class.school.php");
// 队伍表
include_once("$relpath/class.team.php");
// 成员表
include_once("$relpath/class.member.php");
// 文章表
include_once("$relpath/class.article.php");
// 消息表
include_once("$relpath/class.message.php");
// 住宿表
include_once("$relpath/class.hotel.php");

//邮件发送类
include_once("$relpath/phpmailer/class.mailer.php");

//其他函数
$relpath = dirname(__FILE__);
include_once("$relpath/functions.php");
?>
