<html>
<head>
<script type="text/javascript" src="jq.js"> </script>
</head>
<body style='padding:0px; margin:0px;'>
<?

function make_path($path){
	$path_t="";
	$p_part=explode("/",$path);
	foreach($p_part as $n) {
		$path_t=$path_t.$n."/";
		if(!is_dir($path_t))
			if(!mkdir($path_t)) echo "Не удалось создать папку ".$path_t;
	}
}

if($_SERVER['REQUEST_METHOD']==="POST") {
	$iden=explode(" ",$_POST['date'][0]);
	$iden=explode("_",$iden[0]);
	if($_COOKIE['admin_debug']==="yes"){
		var_dump($iden);
	}
	$path="logs/".$iden[0];
	if($_COOKIE['admin_debug']==="yes"){
		var_dump($path);
	}
	make_path($path);
	file_put_contents($path."/".$iden[1].".php","\$date_user=".var_export($_POST['date'],true).";");
} elseif($_GET['action']==="view_page" and isset($_GET['iden'])) {

$iden=$_GET['iden'];
$dir=opendir("logs/".$iden);
echo "<a href='index.php'>список пользователей</a> <br/>";
while($f=readdir($dir)) {
	if(preg_match("#([0-9]+)\.php#",$f,$mf))
		echo "<a href='index.php?action=view_usage&iden=$iden&page=".$mf[1]."'> Посмотреть страницу ".$mf[1]." </a><br/>";
}

}elseif($_GET['action']==="view_usage" and isset($_GET['iden']) and isset($_GET['page'])) {


$iden=$_GET['iden'];
$page=(int) $_GET['page'];

$date_user=file_get_contents("logs/".$iden."/".$page.".php");

eval($date_user);

$meta=explode(" ",$date_user[0]);

$width=$meta[3];
$height=$meta[4];


?>
<div style='position:fixed; background:#FFF; border: 1px solid #f00; padding:10px;'>  пользователь : <a href='index.php?action=view_page&iden=<?=$iden?>'><?=$iden?></a> страница : <?=$page?> <a href='javascript:void(0)' onclick='next_step()'>Запустить просмотр действии</a> <div style='height:20px; width:100px; background:#ccc; '><div style='height:20px; width:0px;background:#007700; ' id='timebar'></div></div></div>
<div width='<?=$width?>' height='<?=$height?>' >
<iframe id='userpagefr' width='<?=$width?>' height='<?=$height?>' src='<?=$meta[2]?>' ></iframe>
<div id='mouse' style='position:absolute; background-image:url("cursor_arrow.png"); width:25px; height:25px; backgroud:#000; '></div>
</div>

<script> 

var mouse_date=Array();

<? foreach($date_user as $n=>$v ) {
	if($n>0) {
		$v=explode(" ",$v);

		if($v[0]!=="click") {
			echo "mouse_date[".$n."]=Array()\n";
			echo "mouse_date[".$n."]['sc']=".$v[0]."\n";
			echo "mouse_date[".$n."]['time']=".$v[1]."\n";
			echo "mouse_date[".$n."]['x']=".$v[2]."\n";
			echo "mouse_date[".$n."]['y']=".$v[3]."\n";
			$last_time="\n var time_len=".$v[1]."; \n";
		} else {
			echo "mouse_date[".$n."]='click'\n";
		}
		
		$last_id="var lastid=$n \n";
	}

} 

echo $last_time;
echo $last_id;
?>

var mi=2;
var last_time=0;
var scinter;
var scrol_step=0;
var cur_scroll=0;
var count_sec=0;
var timeline_interval;

function scrollingFr(xsc){
	
	var myIframe = document.getElementById('userpagefr');

	if(scrol_step==0)
		scrol_step=Math.round((xsc-myIframe.contentWindow.pageYOffset)/10);
			
	myIframe.contentWindow.scrollTo(0,myIframe.contentWindow.pageYOffset+scrol_step);
	if(Math.abs(myIframe.contentWindow.pageYOffset-xsc) < Math.abs(scrol_step) ){
		clearInterval(scinter);
		scrol_step=0;
	}

	console.log(Math.abs(myIframe.contentWindow.pageYOffset-xsc));
	console.log(Math.abs(scrol_step));
}

function timeline(){
	$("#timebar").width(count_sec/time_len*100);
	count_sec++;
}

function next_step(){
	
	
	if(count_sec==0)
		timeline_interval=setInterval('timeline()',1000);


	if(mouse_date[mi]=='click') {
		$(mob).css('border',"1px solid #f00");
		$(mob).delay(300).css('border',"1px solid #f00");		
		setTimeout("$(mob).delay(300).css('border','none'); mi++; console.log('ddddd'); next_step();  ", 300);
		return true;
	}

	if(cur_scroll!=mouse_date[mi]['sc']){
		clearInterval(scinter);
		scinter=setInterval('scrollingFr('+mouse_date[mi]['sc']+')',200);
		cur_scroll=mouse_date[mi]['sc'];
	}

	$(mob).animate({'left':mouse_date[mi]['x']+'px','top':mouse_date[mi]['y']+'px'},(mouse_date[mi]['time']-last_time)*1000,
	function(){
		last_time=mouse_date[mi]['time'];
		mi++;
		if(mi<lastid)
			next_step();
		else  {
			mi=1;
			count_sec=0;
			clearInterval(timeline_interval);
			$("#timebar").width(0);
			clearInterval(scinter);
		}
	})
}

var mob=document.getElementById('mouse');

mob.style.left=mouse_date[1]['x'];
mob.style.top=mouse_date[1]['y'];

</script>
<? } else {
	echo "Список пользователей";
	$dir=opendir("logs/");
	while($f=readdir($dir)) {
		if(is_dir("logs/".$f) and strlen($f)>5) {
			
			echo "<a href='index.php?action=view_page&iden=$f'> Посмотреть пользователя ".$f." </a><br/>";
		}
	}
}?>
</body>
</html>
