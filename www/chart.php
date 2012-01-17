<? require_once 'header.php'; ?>
<? require_once 'functions.php'; ?>

<h1>MyPublisher Chess Ratings Chart</h1>
<a href="index.php">Back to Home</a>

<?

$sql = "select id, name, rating from players where active order by rating desc";
$query = mysql_query($sql);
$players = array();
while($fetch = mysql_fetch_row($query)){
    list($id, $name, $rating) = $fetch;
    
    $from = date('Y-m-d', time()-(3600*24*10));
    $players[$name] = get_player_ratings($id);
}

?>

<div id="chartdiv" style="width:1024px;height:768px;"></div>
<? generate_jqplot("chartdiv", $players); ?>
