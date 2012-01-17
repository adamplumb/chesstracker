<?
require_once 'sql.php';
$dblink = mysql_connect($host, $login, $password) or die("Unable to connect to DB");
mysql_select_db($db) or die("Unable to select db: " . mysql_error());
?>
<html>
<head>
    <title>MyPublisher Chess Ratings</title>
    <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery.jqplot.js"></script>
    <script type="text/javascript" src="js/jqplot.dateAxisRenderer.min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/jquery.jqplot.css" />
</head>
<body>

<?


$ranks = array(
    "Class J"   => array(100, 200),
    "Class I"   => array(200, 399),
    "Class H"   => array(400, 599),
    "Class G"   => array(600, 799),
    "Class F"   => array(800, 999),
    "Class E"   => array(1000, 1199),
    "Class D"   => array(1200, 1399),
    "Class C"   => array(1400, 1599),
    "Class B"   => array(1600, 1799),
    "Class A"   => array(1800, 1999),
    "Expert"    => array(2000, 2199),
    "National Master"   => array(2200, 2399),
    "Senior Master"     => array(2400, 9999)
);

function rating_to_rank($rating){
    global $ranks;
    foreach($ranks as $key => $range){
        list($low, $high) = $range;
        
        if($rating >= $low && $rating <= $high)
            return $key;
    }
    
    return "";
}

function rank_to_css_class($rank){
    return str_replace(' ', '', strtolower($rank));
}

?>
