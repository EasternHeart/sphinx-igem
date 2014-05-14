<?php

require('config.inc.php');

$names = array('part_id', 'part_name', 'part_short_name', 'part_short_desc', 'part_type', 'release_status', 'sample_status', 'part_results', 'part_nickname', 'part_rating', 'part_url', 'part_entered', 'part_author');

include 'sphinxapi.php';  // 加载Sphinx API

$sc = new SphinxClient(); // 实例化Api

$sc->setServer($spsrv, $spport); // 设置服务端，第一个参数sphinx服务器地址，第二个sphinx监听端口

//$sc->SetMatchMode(SPH_MATCH_FULLSCAN);
$sc->SetMatchMode(SPH_MATCH_EXTENDED2);

$con = mysql_connect($sqlconn,$sqlusern,$sqlpassw);

mysql_select_db($sqldb, $con);

if(!$con)
{
	$json["status"] = 500;
	goto done;
}

//print($_GET["search"]);

if(array_key_exists("start",$_GET) && array_key_exists("end",$_GET))
{
$start = $_GET["start"];
$end = $_GET["end"];
$part = false;
if(is_numeric($start) && is_numeric($end))
{
if($start<=$end)
{
	$sc->SetLimits($start,$end-$start+1);
//	$json["part"] = "true";
}
}
}
else
{
	$part = false;
}


$res = $sc->query($_GET["search"], 'test1'); // 执行查询，第一个参数查询的关键字，第二个查询的索引名称，mysql索引名称（这个也是在配置文件中定义的），多个索引名称以,分开，也可以用*表示所有索引。

if(!$res)
{
	$json["status"] = 500;
	goto done;
}

if($res['total_found'] == 0)
{
	//echo '<h1> Not Found! </h1>';
	$json["status"] = 404;
}
else {

$json["status"] = 200;
$json["total_found"] = $res['total_found'];

$ids = array_keys($res['matches']);
$i = 0;
$json["result_length"] = count($ids);
foreach($ids as $id)
{
	{
		$json["result"][$i] = $id;
		$result = mysql_query("SELECT * FROM details WHERE id = " . $id);
		if($result)
		{
			$row = mysql_fetch_array($result);
			if($row)
			{
				$json["title"][$i] = $row["title"];
				$json["details"][$i] = json_decode($row["details"]);
			}
		}

	}
	$i++;
}

}

//echo json_encode($json);

mysql_close($con);

done:;

function writeitem($subp)
{
?>
<div class="row" style="padding: 0 15px;">
<div class="media">
  <a class="pull-left" href="#">
    <img class="media-object" src="brick.png" alt="Brick">
  </a>
  <div class="media-body">
    <h4 class="media-heading"><?php echo $subp->part_name; ?></h4>
    <pre><?php
	echo "Nickname: " . $subp->part_nickname . "\n";
	echo "Short Name: " . $subp->part_short_name . "\n";
	echo "Short Description: " . $subp->part_short_desc . "\n";
?></pre>
  </div>
</div>
</div>

<?php	
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Result page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <h1> Search result for <?php echo $_GET['search'] ?> </h1>
    <?php
	if($json["status"] == 500)
		echo "<h2>Internal Server Error!</h2>";
	else if($json["status"] == 404)
		echo "<h2>Not Found:-(</h2>";
	else
	{
		foreach($json["details"] as $det)
		{
			writeitem($det);
		}
	}
    ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
  </body>
</html>
