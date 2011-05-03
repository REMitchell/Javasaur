<HTML>
<HEAD>
<?php
$_POST['svgArray'];
$svgArray = array();
$svgArray = explode(';;', $_POST['svgArray']);
$_POST['pid'] = 2;
$name = "shape";
$server = "javasaurcom.fatcowmysql.com";
$username = "javasaur";
$password = "i<3tyler";
$db = "javasaur";
$conn = mysql_connect($server, $username, $password, $db);
mysql_select_db($db);

foreach($svgArray as $svg){
    $query = "INSERT INTO elements (pid, name, url) VALUES (".$_POST['pid'].", ".$name.", ".$svg.");";
    print $query;
    mysql_query($sql);
}

 header( 'Location: file:///home/rmitchell/Javasaur/svg-edit-2.5.1.2/drawingPanel2.html' ) ;
?>

</HEAD>
<BODY>



</BODY>
</HTML>
