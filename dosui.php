<?php

error_reporting(E_ERROR);


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

if(array_key_exists("page",$_GET))
{
//$start = $_GET["start"];
//$end = $_GET["end"];
$page = $_GET["page"];
$part = false;
//if(is_numeric($start) && is_numeric($end))
if(is_numeric($page))
{
//if($start<=$end)
if($page >= 0)
{
	$sc->SetLimits($page*20,20);
//	$json["part"] = "true";
}
}
}
else
{
	$page = 0;
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
	
		if (!strrchr($_GET["search"],"*"))
		{
			header("Location: $thisurl/dosui.php?search=" . $_GET["search"] . "*&redir=1");
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

//echo json_encode($json);

mysql_close($con);

done:;

function writeitem($subp)
{
if(!$subp)return;
?>
<div class="row" style="padding: 0 15px;">
<div class="media">
  <a class="pull-left" href=<?php echo '"' . $subp->part_url . '"'; ?> >
    <img class="media-object" src=<?php 
if($subp->part_results == "Works")
	echo '"brick_ok.png"';
else
	echo '"brick_notwork.png"';
$picon = "http://parts.igem.org/images/partbypart/icon_" . strtolower($subp->part_type) . ".png";
echo '"' . $picon . '"'
?> alt="Brick">
  </a>
  <div class="media-body">
    <h4 class="media-heading">
      <a href=<?php echo '"' . $subp->part_url . '"'; ?> ><?php echo $subp->part_name; ?></a>
      <img src=<?php 
$picon = "http://parts.igem.org/images/partbypart/icon_" . strtolower($subp->part_type) . ".png";
echo '"' . $picon . '"'
?> alt=<?php echo $subp->part_type; ?> ></img>
</h4>
    <pre><?php
	echo "<clbold>Nickname:</clbold> " . $subp->part_nickname . "\n";
	echo "<clbold>Short Name:</clbold> " . $subp->part_short_name . "\n";
	echo "<clbold>Short Description:</clbold> " . $subp->part_short_desc . "\n";
	echo "<clbold>Type:</clbold> " . $subp->part_type . "\n";
	echo "<clbold>Date Entered:</clbold> " . $subp->part_entered . "\n";
	echo "<clbold>Author:</clbold> " . $subp->part_author . "\n";
	echo "<clbold>Release Status:</clbold> " . $subp->release_status . "\n";
	echo "<clbold>Sample Status:</clbold> " . $subp->sample_status . "\n";
        echo "<clbold>Rating:</clbold> ";
	for($i = 0;$i<$subp->part_rating;$i++)
	{
		echo "<img src=\"star.svg\" alt=\"★\" />";
	}
	echo "\n";
	echo "<clbold>Results:</clbold> " . $subp->part_results . "\n";
?></pre>
  </div>
</div>
</div>

<?php	
}

function pageurl($p)
{
	echo '"dosui.php?search=' . $_GET["search"] . '&page=' . $p . '"';
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Result page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="search.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class="ctr"><img src="igem_logo.png" alt="iGEM SKLBC_HS" style="height:20%;" class="img-thmubnail ctr"></img></div>

  
<!--  <h2><ctr><img src="igem_logo.png" alt="iGEM SKLBC_HS" style="height:20%;" class="img-thmubnail"></img></ctr></h2>-->
  <h3 class="ctr">
    <?php
	if($_GET["redir"] == "1")
	{ ?>
	 Nothing found for the previous search, but this results for "<?php echo $_GET['search'] ?>" may be needed by you. 
	<?php
	}
	else {
	?>
	Search result for "<?php echo $_GET['search'] ?>" 
	<?php } ?>
	</h3>
    <?php
	if($json["status"] == 500)
		echo "<h2>Internal Server Error!</h2>";
	else if($json["status"] == 404)
	{
		echo "<h2>Not Found:-( ";
//		if (!strrchr($_GET["search"],"*"))
//		{
//                    echo "<a href='dosui.php?search=" . $_GET["search"] . "*'>You can try clicking here.</a>";
//		}
		echo "</h2>";
	}
	else if($json["total_found"] <= ($page)*20)
		echo "<h2> Page error: You've fallen into the outer space!</h2>";
	else
	{
		foreach($json["details"] as $det)
		{
			writeitem($det);
		}
		$total_page = (int)(($json["total_found"]+19)/20);
		?>
<div class="ctr"><ul class="pagination">
<?php
if($page > 0)
{
?>
  <li><a href=<?php pageurl($page-1); ?> >&laquo;</a></li>
<?php
}else{
?>
  <li class="disabled"><a href="#">&laquo;</a></li>
<?php
}
?>

<?php
$final_page = $total_page-2;
$start_page = 0;
$startpage = $endpage = false;
if($final_page > 8)
{
	if($page <= $total_page-1-4)
	{
		$endpage = true;
		$final_page = $page+4;
	}
	if($page >= 0+5)
	{
		$startpage = true;
		$start_page = $page-4;
	}
}
if($startpage)
{
?>
<li><a href=<?php pageurl(0); ?> >1...</a></li>
<?php
}
for($a = $start_page;$a<=$final_page;$a++)
{
	if($a == $page)
	{
?>
<li class="active"><a href="#"><?php echo $a+1; ?> <span class="sr-only">(current)</span></a></li>
<?php
	}
	else
	{
?>
  <li><a href=<?php pageurl($a); ?> ><?php echo $a+1; ?></a></li>
<?php
	}
}
?>
<?php

if($endpage)
{
?>
<li><a href=<?php pageurl($total_page-2); ?> >...<?php echo $total_page-1;?></a></li>
<?php
}if($page < $total_page-2)
{
?>
  <li><a href=<?php pageurl($page+1); ?> >&raquo;</a></li>
<?php
}else{
?>
  <li class="disabled"><a href="#">&raquo;</a></li>
<?php
}
?></ul></div>
<?php
	}
?>
	<div class="ctr">By <a href="http://2014hs.igem.org/Team:SKLBC-China">iGEM SKLBC-China 2014.</a></div>
   <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- <script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script> -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- <script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script> -->
  </body>
</html>
