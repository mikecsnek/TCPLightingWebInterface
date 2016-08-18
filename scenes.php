<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Script - By Brendon Irwin
	 * 
	 */

	include "include.php";

	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		//run scene
		if( isset($_POST['scene']) && $_POST['scene'] != "" ){
			$sceneID = $_POST['scene'];
			$action = $_POST['action'];
			if( $action == "on"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid><val>1</val></gip>"; //val 1 is on 0 is off
			}else if( $action == "off"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid><val>0</val></gip>"; //val 1 is on 0 is off
			}else if($action == "delete"){
				$CMD = "cmd=SceneDelete&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid></gip>"; 
			}
			
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			echo json_encode( array("scene" => $sceneID, "return" => $array) );
		}
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>TCP Control Script</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="favicons/manifest.json">
	<link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="favicons/favicon.ico">
	<meta name="apple-mobile-web-app-title" content="TCP Lighting">
	<meta name="application-name" content="TCP Lighting">
	<meta name="msapplication-config" content="favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="style.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="scripts.js"></script>
</head>
<body>
<div id="toolBar"><a href="index.php">Lighting Controls</a> | <a href="scheduler.php">Lighting Scheduler</a> | <a href="apitest.php">API Test Zone</a></div>
<script>
	$(function(){
		$('.activateScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			$.post( "scenes.php", { scene: sceneID, action: 'on' })
			  .done(function( data ) {
				console.log( "Response " + data );
			});
		});
		
		$('.deactivateScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			$.post( "scenes.php", { scene: sceneID, action: 'off' })
			  .done(function( data ) {
				console.log( "Response " + data );
			});
		});
		
		$('.deleteScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			if (window.confirm("Are you sure?")) {
				$.post( "scenes.php", { scene: sceneID, action: 'delete' })
				  .done(function( data ) {
					console.log( "Response " + data );
					location.reload();
				});
			}
		});
		
		$('.editScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			window.location = "scenescreatedit.php?SID=" + sceneID;
		});
		
	});
</script>	
<?php
	echo '<h2>Scenes / Smart Control</h2>';
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	$scenes = $array["scene"];
	if( is_array($scenes) ){
		for($x = 0; $x < sizeof($scenes); $x++){
			?>
			<div class="" id="scene-id-<?php echo $scenes[$x]["sid"]; ?>">
				<h3><?php echo $scenes[$x]["name"]; ?></h3>
				<img src="<?php echo IMAGE_PATH.$scenes[$x]["icon"]; ?>" />
				<p>
					Active: <?php echo $scenes[$x]["active"];  ?><br />
					Devices: <?php echo is_array($scenes[$x]["device"]) ? sizeof($scenes[$x]["device"]) : ""; ?>
					<?php
					/* Type R = Room, D = device*/
					?>
					
				</p>
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="activateScene">Activate</button>
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="deactivateScene">De-Activate</button>
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="editScene">Edit</button> 
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="deleteScene">Delete</button>
			</div>
			<?php
		}
		
	}
	
	//pa($array);
?>
<br />
<a href="scenescreatedit.php">Create Scene</a>
</body>
</html>