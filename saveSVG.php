<HTML>
<HEAD>
<?php
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


foreach($svgArray as $i => $svg){
    if($svg != ""){
        $query = "INSERT INTO elements (pid, name, url) VALUES (".$_POST['pid'].", \"".str_replace (" " , "", $_POST[$i])."\", \"".$svg."\");";
        print "<p>".$query."<p>";
        mysql_query($query);
    }
}

?>

</HEAD>
<BODY>
</BODY>
</HTML>
