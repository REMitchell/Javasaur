<HTML>
<HEAD>
<?php
	$server = "javasaurcom.fatcowmysql.com";
	$username = "javasaur";
	$password = "i<3tyler";
	$db = "javasaur";
	$conn = mysql_connect($server, $username, $password, $db);
	mysql_select_db($db);
	$animations = mysql_query("SELECT * FROM animations WHERE pid=".$_GET['pid'].";");
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$params = $_POST;
	if($params['title'] == ""){
		$params['title'] = "untitled";
	}
	if($params['attribute2'] == ""){
		$params['attribute2'] = "0";
	}



	$sql = "INSERT INTO animations (eid, pid, name, actionTrigger, triggerID, animationType, duration, attribute1, attribute2) VALUES (".$params['elemID'].", ".$params['projectID'].", '".$params['title']."', '".$params['postOrPre']."', ".$params['triggerID'].", '".$params['animationType']."', ".$params['time'].", '".$params['attribute1']."', '".$params['attribute2']."')";
print $sql;
mysql_query($sql);


	if($params['postOrPre'] == "before"){
		$sql = "SELECT * FROM animations WHERE pid = ".$_GET['pid']." AND name='".$params['title']."';";
		print "<p>Equals before stuff<p>";
		print $sql;
		$result = mysql_query($sql);
		//gets the newly created aid
		print "the result is<p>";
		$result = mysql_fetch_assoc($result);
		print_r($result);

		//creates a system composed entirely of "afters" rather than "befores" which makes it easier to process
		$sql = "UPDATE animations SET actionTrigger = 'after', triggerID = '".$result['aid']."' WHERE aid =".$params['triggerID'].";";
		mysql_query($sql);		
	}
//

}

$sql = "SELECT * FROM elements WHERE pid = ".$_GET['pid'].";";
$elements = mysql_query($sql);

?>

<script src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="animation.css" media="screen"/> 

</HEAD>
<BODY>
<div type="animationPane">
<form action="<?= $_SERVER['PHP_SELF'] ?>?pid=<?php print $_GET['pid'] ?>" method="POST">
Animation Name:&nbsp;<input type="text" name="title"></input>
<p>
<!-- Add jquery code to set value to the hidden element value -->
<div style="display:inline">Animate</div> <div class="selectedElem">SQUARE1</div> <select id="animationType" name="animationType">
  <option>Move</option>
  <option>Grow</option>
  <option>Shrink</option>
  <option>Fade</option>
  <option>Rotate</option>
</select>
for 
<input type="text" name="time" id="time"></input> seconds.
<p>
<div id="typeSpecific">
Move across by <input type='text' name='attribute1' id="attribute1"></input> pixels and down by <input type='text' name='attribute2' id="attribute2"></input>
</div>

<!-- Add jquery code to set value to the hidden element value -->
<input type="hidden" name="selectedElem" value="square1">
<input type="hidden" name="elemID" value="3">
<input type="hidden" name="projectID" value="1">

<script>
$('#animationType').blur(function() {
	n = $('#animationType').val();
	var formElements = "";
	switch(n)
	{
//TODO: Add question mark icons next to formElements text and elaborate on the actions
	case "Move":
	  formElements = "Move across by <input type='text' name='attribute1' id='attribute1'></input> pixels and down by <input type='text' name='attribute2' id='attribute2'></input>";
	  break;
	case "Grow":
	  formElements = "Grow by <input type='text' name='attribute1' id='attribute1'></input> percent";
	  break;
	case "Shrink":
	  formElements = "Shrink by <input type='text' name='attribute1' id='attribute1'></input> percent";
	  break;
	case "Fade":
	  formElements = "Fade to <input type='text' name='attribute1' id='attribute1'></input> percent opacity";
	  break;
	case "Rotate":
	  formElements = "Rotate by <input type='text' name='attribute1' id='attribute1'></input> degrees";
	  break;
	default:
	  formElements = "ERROR: incorrect animation type. Please try again or contact the site administrator."
	}
  $('#typeSpecific').replaceWith(formElements);
});
</script>
<p>
Start animation <select id="postOrPre" name="postOrPre">
  <option value="after">After</option>
  <option value="before">Before</option>
  <option value="simultaneous">At the same time as</option>
  <option value="onload">When the page loads</option>
  <option value="onclick">When the following is clicked</option>
  <option value="onhover">When the cursor moves over</option>
</select>
the <div id="trigType">animation</div>

<select name="triggerID" id="triggerID">
<?php
$animationsSelect = "";
while ($animation = mysql_fetch_assoc($animations)){
	$animationsSelect = $animationsSelect."<option value=\"".$animation['aid']."\">".$animation['name']."</option>";
}
print $animationsSelect;
?>
</select>

<script>
$('#postOrPre').blur(function() {
	if($('#postOrPre').val() == "onhover" || $('#postOrPre').val() == "onclick"){
		var elementText = '<?php while ($element = mysql_fetch_assoc($elements)){ print "<option value=\"".$element['eid']."\">".$element['name']."</option>";} ?>';
		$('#triggerID').html(elementText);
		$('#trigType').html('image');
	}


	if($('#postOrPre').val() == "before" || $('#postOrPre').val() == "after" || $('#postOrPre').val() == "simultaneous"){
		var animationText = '<?php print $animationsSelect; ?>';
		$('#triggerID').html(elementText);
		$('#trigType').html('animation');
	}

});
</script>
<p>
<input type="submit" value="Add Animation" id="submit">
</form>

</div>
</BODY>
</HEAD>

</HTML>
