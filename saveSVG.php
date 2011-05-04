<HTML>
<HEAD>
<?php
$svgArray = array();
$svgArray = explode(';;', $_GET['svgArray']);
$_GET['pid'] = 2;
$name = "shape";
$server = "javasaurcom.fatcowmysql.com";
$username = "javasaur";
$password = "password";
$db = "javasaur";
$conn = mysql_connect($server, $username, $password, $db);
mysql_select_db($db);


foreach($svgArray as $i => $svg){
    if($svg != ""){
        $query = "INSERT INTO elements (pid, name, url) VALUES (".$_GET['pid'].", \"".str_replace (" " , "", $_GET[$i])."\", \"".$svg."\");";
        print "<p>".$query."<p>";
        mysql_query($query);
    }
}

?>

</HEAD>
<BODY>
</BODY>
</HTML>
