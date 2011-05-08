<!--
Created by Ryan Mitchell 2011
At Olin College of Engineering
Creative Commons, non-commercial, share-alike
-->

<HTML>
<HEAD>
<TITLE>Render Animations</TITLE>
<?php
	$server = "javasaurcom.fatcowmysql.com";
	$username = "javasaur";
	$password = "i<3tyler";
	$db = "javasaur";
	$conn = mysql_connect($server, $username, $password, $db);
	mysql_select_db($db);
	$allAnimations = mysql_query("SELECT * FROM animations WHERE pid=".$_GET['pid'].";");
//Should only have "afters" that we need to worry about for the recursive function
	$beforeAnimations = mysql_query("SELECT * FROM animations WHERE pid=".$_GET['pid']." AND actionTrigger = 'after'");
	$beforeAnimationsList = array();
	$allAnimationsList = array();

while($allAnimation = mysql_fetch_assoc($allAnimations)){
	$allAnimationsList[$allAnimation['aid']] = $allAnimation;
}

$afterArray = array();
foreach($allAnimationsList as $animation){
	if($animation['actionTrigger'] == "before"){
		$afterArray[$animation['triggerID']] = $animation['aid'];
	}elseif($animation['actionTrigger'] == "after"){
		$afterArray[$animation['aid']] = $animation['triggerID'];
	}
}
$allBranches = branchAfters($afterArray);

$renderedAfters = renderAfters($allBranches, $allAnimationsList);
//Original placement of rendered afters

$animationTree = array();
//function printAnimation($animationArray, $functionText, $makeFunction){
foreach($allAnimationsList as $animation){
	if($animation['actionTrigger'] == "onclick"){
		$sql = "SELECT * FROM elements WHERE eid = ".$animation['triggerID']." AND pid = ".$animation['pid'].";";	
		$result = mysql_query($sql);
		$triggerElem = mysql_fetch_assoc($result);

		if(isset($renderedAfters[$animation['triggerID']])){
			$animationTree[] = "$('#".str_replace(" ", "", $triggerElem['name'])."').click(){".printAnimation($animation, str_replace(" ", "", $animation['name'])."()", False)."};";
		}else{
			$animationTree[] = "$('#".str_replace(" ", "", $triggerElem['name'])."').click(){".printAnimation($animation, "", False)."};";	
		}
	}elseif($animation['actionTrigger'] == "onhover"){
		$sql = "SELECT * FROM elements WHERE eid = ".$animation['triggerID']." AND pid = ".$animation['pid'].";";
		$result = mysql_query($sql);
		$triggerElem = mysql_fetch_assoc($result);
		if(isset($renderedAfters[$animation['aid']])){
			$animationTree[] = "$('#".str_replace(" ", "", $triggerElem['name'])."').mouseover(){".printAnimation($animation, str_replace(" ", "", $animation['name'])."()", False)."};";
		}else{
			$animationTree[] = "$('#".str_replace(" ", "", $triggerElem['name'])."').mouseover(){".printAnimation($animation, "", False)."};";	
		}
	}elseif($animation['actionTrigger'] == "onload"){
		//If this has a sequence that follows it
		if(isset($renderedAfters[$animation['aid']])){
			$animationTree[] = "$(document).ready(){".printAnimation($animation, str_replace(" ", "", $animation['name'])."()", False)."};";
		}else{
			$animationTree[] = "$(document).ready(){".printAnimation($animation, "", False)."};";	
		}
	}
}




print "<h2>Copy and paste the following code into your website:</h2>";
print "<div style=\"width:600px;height:400px;overflow:auto;\" contentEditable=True>";
print htmlspecialchars("<script type=\"text/javascript\">")."<p>";
foreach($renderedAfters as $after){
	print $after."<br>";
}
//print_r($animationTree);
foreach($animationTree as $animation){
print $animation."<br>";
}
print htmlspecialchars("</script>");
print "<P>";
$elementsResult = mysql_query("SELECT * FROM elements WHERE pid=".$_GET['pid']);
while($nextElem = mysql_fetch_assoc($elementsResult)){
print htmlspecialchars("<img src=\"".$nextElem['url']."\" id=\"".$nextElem['name']."\">");
print "<p>";
}
print "</div>";


function branchAfters($afterArray){
	$allBranches = array();
	$tempOrder = array();
	foreach ($afterArray as $before => $after) {
		if(!in_array($before, $afterArray)){
			//$before is a root, the first two values are set automatically
			$tempOrder[] = $before;
			$tempOrder[] = $after;
			//If "after" has something that comes after *it*
			while(isset($afterArray[$afterArray[$before]])){
					$tempOrder[] = $afterArray[$afterArray[$before]];					
					//Moving forward a level...
					$before = $afterArray[$before];
			}
			//Nothing that comes after this, the last one in the array
			//adds to $tempOrder, keyed on the aid of the "root"
//$branch = array_reverse($branch);
			$allBranches[$tempOrder[count($tempOrder)-1]] = array_reverse($tempOrder);
			//Kind of a pain in the butt to reset $tempOrder			
			foreach($tempOrder as $i => $temp){
				unset($tempOrder[$i]);
			}
		}
	} 
	return $allBranches;
}
/* This function takes in a branched array of consecutive animations
* Renders them in text form, as functions labeled by their user-given name
* Adds "simultaneous" animations at the same time
* Returns array of animation functions, keyed on the first/root animation aid
*/
function renderAfters($allBranches, $allAnimationsList){
	$renderedBranches = array();
	//Get all "simultaneous" animations
	$simulteanous = array();
	foreach($allAnimationsList as $animation){
		if($animation['actionTrigger'] == "simultaneous"){
			//Do something ridiculously, insanely clever, because order doesn't matter, unlike "before/after" list
			$simultaneous[$animations['aid']] = $animations['triggerID'];
			$simultaneous[$animations['triggerID']] = $animations['aid'];
		}
	}
	foreach($allBranches as $branch){
		$rootAid = $branch[0];
		//Reverse the order of the branch in order to support recursion
		
		for($i=0; $i < (count($branch)-1); $i++){
			$animationText = printAnimation($allAnimationsList[$branch[$i]], ", function(){".$animationText."}", False);
			//If there exists a simultaneous animation that needs to go along with this, concatenate it.
			if(isset($simultaneous[$aid])){
				$animationText = $animationText.printAnimation($allAnimationsList[$simultaneous[$aid]], "", False);
			}
		}
		//The very LAST element is special, make a function out of it
		$lastElemKey = count($branch)-1;
		$animationText = printAnimation($allAnimationsList[$branch[$lastElemKey]], $animationText, str_replace(" ", "", $allAnimationsList[$branch[0]]['name']));
		$renderedBranches[$rootAid] = $animationText;
	}
	return $renderedBranches;
}

/*
$("#animate").click(function() {
    $("#content").animate(
            {"height": "80px"},
            "fast");
});
*/
function printAnimation($animationArray, $functionText, $makeFunction){
	if($functionText != ""){
		$functionText = "".$functionText."";
	}
	$params = getParams($animationArray['animationType'], $animationArray['attribute1'], 		$animationArray['attribute2']);
	$milliseconds = $animationArray['duration']*1000;
	$animationArray['name'] = str_replace(" " , "", $animationArray['name']);
	$returnText = "$('#".aidToEid($animationArray)."').animate({".$params."}, ".$milliseconds."".$functionText.");";
	if($makeFunction){
		$returnText = "function ".$animationArray['name']."(){".$returnText."}";
	}
	return $returnText;
}


function getParams($animationType, $attributeOne, $attributeTwo){
	//move, grow, shrink, fade, rotate
	if($animationType == "Move"){
		$params = "left: ".$attributeOne."px;top:".$attributeTwo."px;";
	}
	if($animationType == "Grow"){
		$percent = "1.".$attributeOne;
		$params = "scale:".$percent.";";
	}
	if($animationType == "Shrink"){
		$percent = 100-$attributeOne;
		$params = "scale:.".$percent.";";
	}
	if($animationType == "fade"){
		$fade = 100-$attributeOne;
		$params = "opacity:".$fade.";";
	}
	if($animationType == "rotate"){
		$params = "rotate:".$attributeOne.";";
	}
	return $params;

}


function createImage($svg){
	//Pseudocode for now. Takes in string of svg code, returns array with
	//elemID, image url
	$image = array();
	//ElemID, in this case, is 5
	array_push($image, 5);
	//urls are of the form pid_eid.png
	array_push($image, "1_5.png");
	return $image;
}


function aidToEid($animation){
	$sql = "SELECT elements.name FROM elements, animations WHERE elements.eid =".$animation['eid']."  AND animations.eid = elements.eid";
	//print "Query is : ".$sql;
	$result = mysql_query($sql);
	$eidArray = mysql_fetch_assoc($result);
	return str_replace(" ", "", $eidArray['name']);
}

?>
<p>&nbsp;<p>

<!--foreach loop iterates through all animations
 $('#book').animate({
    opacity: 0.25,
    left: '+=50',
    height: 'toggle'
  }, 5000, function() {
     Animation complete.
  });-->
