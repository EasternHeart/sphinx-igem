<?php


error_reporting(E_ERROR);

header('Content-Type:application/json;charset=utf-8'); 

require('config.inc.php'); 

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
	echo json_encode($json);
	exit(1);
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
}

/*echo '<pre>';
print_r($res);
echo '</pre>';*/

//echo json_encode($res);

if($res['total_found'] == 0)
{
	//echo '<h1> Not Found! </h1>';
	$json["status"] = 404;
		if (!strrchr($_GET["search"],"*"))
		{
			if (array_key_exists("start",$_GET) && array_key_exists("end",$_GET))
				header("Location: $thisurl/dosearch.php?search=" . $_GET["search"] . "*&start=" . $_GET["start"] . "&end=" . $_GET["end"]);
			else
				header("Location: $thisurl/dosearch.php?search=" . $_GET["search"] . "*");
		}
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

echo json_encode($json);

mysql_close($con);

?>
