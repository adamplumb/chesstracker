<?

require_once 'sql.php';
$dblink = mysql_connect($host, $login, $password) or die("Unable to connect to DB");
mysql_select_db($db) or die("Unable to select db");

if($_POST){
    $name = $_POST['name'];
    $bgcolor = $_POST['bgcolor'];
    $fontcolor = $_POST['fontcolor'];
    if(!$name)
        die("No tournament name provided");
    
    $sql = "insert into tournaments set name = '{$name}', bgcolor = '{$bgcolor}', fontcolor = '{$fontcolor}', created = NOW()";
    mysql_query($sql) or die(mysql_error());
    
?>
<script type="text/javascript">
<!--
window.location = "index.php";
//-->
</script>

<?
}

?>
