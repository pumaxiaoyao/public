<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="/js/jquery.js"></script>
    <title>Games</title>
    <style type="text/css">
        body {
            background-color: #000;
            color:#FF0;
            font-size:14px;
        }
    </style>
</head>
<body>
<p>&nbsp;</p>
<p align="center" id="msg">
    <img src="lg2.gif"><br><br>载入游戏大概需要花几秒钟时间，请耐心等待...</p>
<p style="visibility: hidden;">&nbsp;<img src="lg.gif"></p>
<?php
//ini_set('display_errors',1);            //错误信息
//ini_set('display_startup_errors',1);    //php启动错误信息
//error_reporting(-1);                    //打印出所有的 错误信息
if(!isset($_SESSION)){ session_start();}
include_once($_SERVER['DOCUMENT_ROOT']."/app/member/class/user.php");
include_once("../app/member/class/auto_transfer.php");
include_once("mgapi.php");
$uid = $_SESSION['userid'];
$username = $_SESSION['username'];
$userinfo=user::getinfo($uid);
//auto_transfer::beforeGameOut($uid,auto_transfer::MG);
$gameId = isset($_GET['id'])?$_GET['id']:'';

$mgapi = new mgapi();



$qGuid='';
//{'time':guid}
$guids = $_SESSION["userGUID"];
if('' == $guids){
    $mgguid = $mgapi->getGUID();
    $qGuid =$mgguid['Data'];
    $guids = array(time(),$qGuid);
    $_SESSION["userGUID"]= $guids;
}else{
    $keys = array_keys($guids);
    $curTime = time();
    $guidTime = $guids[$keys[0]];
    if($curTime-$guidTime > 3000){
        $mgguid = $mgapi->getGUID();
        $qGuid = $mgguid['Data'];
        $guids = array(time(),$qGuid);
        $_SESSION["userGUID"]= $guids;
    }
    $qGuid = $guids[$keys[1]];
}

$mgRet = $mgapi->balance($username,$qGuid);
if($mgRet['Success'] == false){
    if($mgRet['Code'] == 6){
        $mgguid = $mgapi->getGUID();
        $qGuid =$mgguid['Data'];
        $guids = array(time(),$qGuid);
        $_SESSION["userGUID"]= $guids;
        $mgRet = $mgapi->balance($username,$qGuid);
        $currAmt = $mgRet['Data'];
    }else{
        $mgapi->create($username,$qGuid);
    }

}else{
    $currAmt = $mgRet['Data'];
}



$mgRet = $mgapi->loginLive($username);
if($mgRet['Code'] == 4){
    $qGuid='';
    //{'time':guid}
    $guids = $_SESSION["userGUID"];
    if('' == $guids){
        $mgguid = $mgapi->getGUID();
        $qGuid =$mgguid['Data'];
        $guids = array(time(),$qGuid);
        $_SESSION["userGUID"]= $guids;
    }else{
        $keys = array_keys($guids);
        $curTime = time();
        $guidTime = $guids[$keys[0]];
        if($curTime-$guidTime > 3000){
            $mgguid = $mgapi->getGUID();
            $qGuid = $mgguid['Data'];
            $guids = array(time(),$qGuid);
            $_SESSION["userGUID"]= $guids;
        }
        $qGuid = $guids[$keys[1]];
    }
    $mgapi->create($username,$qGuid);
    $mgRet = $mgapi->loginLive($username);
    if($mgRet['Code'] == 6){
        $mgguid = $mgapi->getGUID();
        $qGuid =$mgguid['Data'];
        $guids = array(time(),$qGuid);
        $_SESSION["userGUID"]= $guids;
        $mgRet = $mgapi->loginSlot($username,$gameId);
    }

}
$url = $mgRet['Data'];
//print_r($mgRet);die;
echo("<script> window.location.href= '{$url}' </script> ");


?>
</body>
</html>