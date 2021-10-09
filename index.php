<?php
$page=1;
$showVid=0;
if(is_array($_GET)&&count($_GET)>0)//判断是否有Get参数
{
    if(isset($_GET["page"]))//判断所需要的参数是否存在，isset用来检测变量是否设置，返回true or false
    {
        $page=$_GET["page"];//存在
    }
    if(isset($_GET["showVid"]))//判断所需要的参数是否存在，isset用来检测变量是否设置，返回true or false
    {
        $showVid=$_GET["showVid"];//存在
    }
}
$picPerPage=15;
$picPerLine=5;
$picWidth=320;
$picHigh=240;
$totalPicCnt=0;

$vidPerPage=12;
$vidPerLine=4;
$vidWidth=320;
$vidHigh=240;
$totalVidCnt=0;

$picDir="./";

function isPicFile($name)
{
    // 要执行的代码
    list($filesname,$kzm)=explode(".",$name);
    if($kzm=="gif" or $kzm=="jpg" or $kzm=="png") {
        return TRUE;
    }
    return FALSE;
}

function isVidFile($name)
{
    // 要执行的代码
    list($filesname,$kzm)=explode(".",$name);
    if($kzm=="avi" or $kzm=="mp4" or $kzm=="mkv"or $kzm=="MP4" ) {
        return TRUE;
    }
    return FALSE;
}
function showChangeVidPicHerf() {
    global $showVid;
    if($showVid==1){
        echo "<a href=?page=1&showVid=0>浏览图片</a> ";
    } else {
        echo "<a href=?page=1&showVid=1>浏览视频</a> ";
    }
}
function showPageNumHerf($totalPage, $curPage){
    //显示页码超链接
    global $showVid;
    $begPage=floor(($curPage-1)/10)*10 + 1;
    $endPage=($begPage+9) < $totalPage ? ($begPage+9) : $totalPage;
    for($i=$begPage;$i<=$endPage;$i++) {
        if ($i==$curPage) {
            echo "<strong><span>$i</span></strong> ";
        } else {
            echo "<a href=?page=$i&showVid=$showVid>$i</a> ";
        }
    }
}

function showPageHerf($totalPage, $curPage){
    global $showVid;
    showChangeVidPicHerf();
    $Previous_page=$curPage-1;
    $next_page=$curPage+1;
    if ($Previous_page<1){
        echo "上页 ";
    } else {
        echo "<a href=?page=$Previous_page&showVid=$showVid>上页</a> ";
    }
    showPageNumHerf($totalPage, $curPage);
    if ($next_page>$totalPage){
        echo " 下页";
    } else {
        echo " <a href=?page=$next_page&showVid=$showVid>下页</a>";
    }
    
    echo " <button onclick=\"checkboxed()\">全选/取消全选</button> ";
}

function showOnePic($picName)
{
    global $picWidth, $picHigh;
    echo "<input id=\"checkbox\" type=\"checkbox\" name=\"checkbox[]\" value=\"$picName\"><img width=\"$picWidth\" height=\"$picHigh\" src=\"$picName\"> ";
}

function showOnePagePic($curPage, $arr, $cnt, $picPerPage)
{
    global $page,$picPerLine,$showVid;
    $picPageNum=ceil($cnt/$picPerPage);
    if ($curPage > $picPageNum or $curPage<1) {
        //echo "pic page overflow, totalpage[$picPageNum] curPage[$curPage]";
        $curPage=1;
        $page=1;
    }
    showPageHerf($picPageNum, $curPage);
    
    echo "<br>";
    echo "<form action=\"index.php?page=$curPage&showVid=$showVid\" method=\"post\">";
    $j=0;
    for ($i=($curPage-1)*$picPerPage; ($i<$curPage*$picPerPage) and ($i<$cnt); $i++) {
        showOnePic($arr[$i]);
        if(++$j%$picPerLine==0) {
            echo "<br>";
        }
    }
    echo "<br>";
    echo "<input type=\"submit\" value=\"删除选中\">
    </form>";
        
    showPageHerf($picPageNum, $curPage);
}


function showOneVid($vidName, $id)
{
    global $vidWidth, $vidHigh;
    //echo "<video width=\"$vidWidth\" height=\"$vidHigh\" src=\"$vidName\"> ";
    echo "
    <div class=\"div-inline\"> 
        <video id=\"$id\" width=\"$vidWidth\" height=\"$vidHigh\">
            <source src=\"$vidName\" type=\"video/mp4\">
            您的浏览器不支持 HTML5 video 标签。
        </video><br>
        <input id=\"checkbox\" type=\"checkbox\" name=\"checkbox[]\" value=\"$vidName\">
        <input type=\"button\" onclick=\"playPause$id()\" value=\"播放/暂停\"></input><br>
    </div>
    <script type=\"text/javascript\"> 
        var myVideo$id=document.getElementById(\"$id\"); 
        function playPause$id()
        { 
            if (myVideo$id.paused) 
            myVideo$id.play(); 
            else 
            myVideo$id.pause(); 
        } 
    </script> 
    ";
}

function showOnePageVid($curPage, $arr, $cnt, $vidPerPage)
{
    global $page,$vidPerLine,$showVid;
    $picPageNum=ceil($cnt/$vidPerPage);
    //echo "$cnt $picPageNum ";
    if ($curPage > $picPageNum or $curPage<1) {
        //echo "pic page overflow, totalpage[$picPageNum] curPage[$curPage]";
        $curPage=1;
        $page=1;
    }
    showPageHerf($picPageNum, $curPage);
    
    echo "<br>";
    echo "<form action=\"index.php?page=$curPage&showVid=$showVid\" method=\"post\">";
    echo "<div class=\"container\">";
    $j=0;
    for ($i=($curPage-1)*$vidPerPage; ($i<$curPage*$vidPerPage) and ($i<$cnt); $i++) {
        showOneVid($arr[$i], $j);
        if(++$j%$vidPerLine==0) {
            //echo "<br>";
        }
    }
    echo "</div>";
    echo "<br>";
    echo "<input type=\"submit\" value=\"删除选中\">
    </form>";
    showPageHerf($picPageNum, $curPage);
}

function deleteFiles()
{
    
    if(is_array($_POST)&&count($_POST)>0)//判断是否有Get参数
    {
        if(isset($_POST['checkbox']))
        {
            $files=$_POST['checkbox'];
        }
    } else {
        return;
    }
    for($i=0;$i<count($files);$i++)
    {
        
        if(file_exists($files[$i])){
            unlink($files[$i]);
        }
    }
}

/****** mian *****/
deleteFiles();
if ($showVid==0) {
    $title="图片";
} else {
    $title="视频";
}
echo "<html>
<head>
<title>$title</title>
<style>
.container{
    display: grid;
    border: blue 1px solid;
    grid-template-columns: repeat(4,1fr);

    /*grid-template-rows: 20rem 20rem ;*/
    grid-auto-rows: minmax(30px,auto);

    grid-row-gap: 5rem;
    grid-column-gap: 5rem;
    text-align: center;

} 
</style></head>
<body><center><font size=5 color=red>";

$picArr=array();
$vidArr=array();
$dirHandle=opendir($picDir);
while($file = readdir($dirHandle)) {
    $fullname=$picDir.$file;
    if (is_dir($fullname)) {
        continue;
    }
    if (isPicFile($file)) {
        $picArr[]=$fullname;
        $totalPicCnt++;
    }
    if (isVidFile($file)) {
        $vidArr[]=$fullname;
        $totalVidCnt++;
    }
}
if ($showVid==0) {
    showOnePagePic($page, $picArr, $totalPicCnt, $picPerPage);
} else {
    showOnePageVid($page, $vidArr, $totalVidCnt, $vidPerPage);
}

//全选按钮点击事件
echo "
<script type=\"text/javascript\">
var isCheckAll = false;
function checkboxed(){
    var CheckBox=document.getElementsByTagName('input');
    isCheckAll = !isCheckAll;
    for(i=0;i<CheckBox.length;i++){
        CheckBox[i].checked=isCheckAll;
    };
}
</script>
";
echo "</center></body></html>";

/****** mian *****/
?>
