<?php
$relpath = dirname(__FILE__);
include($relpath."/def.php");

include(APP_ROOT."admin/inc.php");
?>
<script language="javascript">
function delschool(id, name){
    if(confirm("确定要删除学校 ["+name+"] 吗?")){
        window.location = "delschool.php?school_id=" + id;
    }
}
</script>
<?

$school_name = htmlspecialchars($_GET['school_name']);
echo <<<eot
<div class="msg" style="margin:25px;text-align:center;font-size:24px;font-weight:bold;">学校管理</div>
<form method="get">
学校名称(中/英文) <input type="text" name="school_name" value="$school_name"/>
<input type="submit" value="筛选"/>
</form>
eot;

$query = "SELECT * FROM `{tblprefix}_schools` ";
if(isset($_GET['school_name'])){
    if(get_magic_quotes_gpc()) 
        $school_name = stripslashes($_GET['school_name']);
    if(!empty($school_name)) {
        $school_name = $conn->real_escape_string($school_name);
        $query .= " WHERE `school_name_cn` LIKE '%$school_name%' "
                 ."    OR `school_name_en` LIKE '%$school_name%' ";
    }
}
$query .= " ORDER BY `school_id` ASC";

//echo $query;//exit();
$res = getQuery($conn, $query);

echo <<<eot
<p>符合条件的学校数量: {$conn->affected_rows}</p>
<table align="center">
<tr class="tblhead">
<td>编号</td>
<td>学校名称(中文)</td>
<td style="width:310px;">学校名称(英文)</td>
<td>学校类型</td>
<td>操作</td>
</tr>
eot;

$i = 0;
while($row = $res->fetch_assoc()){
    encodeObject($row);
    extract($row);
    if($i & 1) $trclass = "tre";
    else $trclass = "tro";
    $i++;
    $isOurSchool = $school_type & 1 ? "checked" : "";
    $isOurCity = $school_type & 2 ? "checked" : "";
    $isUniversity = $school_type & 4 ? "checked" : "";
    $school_name_cn_slash = str_replace("'", "\\'", $school_name_cn);
    echo <<<eot
<form action="updateschool.php" method="post">
<tr class="$trclass">
<td>$school_id<input type="hidden" name="school_id" value="$school_id"/></td>
<td><input type="text" size="15" name="school_name_cn" value="$school_name_cn"/></td>
<td><input type="text" style="width:300px;" name="school_name_en" value="$school_name_en"/></td>
<td>
<input type="checkbox" name="isOurSchool" $isOurSchool value="1"/>本校
<input type="checkbox" name="isOurCity" $isOurCity value="2"/>本市
<input type="checkbox" name="isUniversity" $isUniversity value="4"/>高校
</td>
<td>
<input type="submit" name="modify" value="修改"/>
<input type="button" onclick="javascript:delschool($school_id,'$school_name_cn_slash')" value="删除"/>
</td>
</tr>
</form>
eot;
}

$trclass = $i & 1 ? "tre" : "tro";
echo <<<eot
<form action="updateschool.php" method="post">
<tr class="$trclass">
<td>新增</td>
<td><input type="text" size="15" name="school_name_cn"/></td>
<td><input type="text" style="width:300px;" name="school_name_en"/></td>
<td>
<input type="checkbox" name="isOurSchool" value="1"/>本校
<input type="checkbox" name="isOurCity" value="2"/>本市
<input type="checkbox" checked="checked" name="isUniversity" value="4"/>高校
</td>
<td><input type="submit" name="add" value="新增"/></td>
</tr>
</form>
</table>
eot;

include(APP_ROOT."admin/footer.php");

?>
