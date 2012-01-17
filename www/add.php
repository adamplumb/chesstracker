<?

require_once 'sql.php';
$dblink = mysql_connect($host, $login, $password) or die("Unable to connect to DB");
mysql_select_db($db) or die("Unable to select db");

if($_POST){
    $winner = $_POST['winner'];
    $white_id = $_POST['white'];
    $black_id = $_POST['black'];
    
    $winner_id = $white_id;
    $loser_id = $black_id;
    if($winner == 'black'){
        $winner_id = $black_id;
        $loser_id = $white_id;
    }
    else if($winner == 'draw')
    {
        $draw = 1;
    }
    
    if(!$white_id || !$black_id)
        die("You must select players");
        
    if(!$winner)
        die("No victor was selected");
    
    $gamedate = $_POST['date'];
    $tournament_id = $_POST['tournament'];
    $forfeit = $_POST['forfeit'];

    $sql = "select rating, name from players where id = '{$winner_id}'";
    $query = mysql_query($sql) or die(mysql_error());
    $fetch = mysql_fetch_row($query);
    $winner_rating = $fetch[0];
    $winner = $fetch[1];

    $sql = "select rating, name from players where id = '{$loser_id}'";
    $query = mysql_query($sql);
    $fetch = mysql_fetch_row($query);
    $loser_rating = $fetch[0];
    $loser = $fetch[1];    
    
    $sql = "select * from games where winner = '{$winner_id}' or loser = '{$winner_id}'";
    $query = mysql_query($sql);
    $winner_games = mysql_num_rows($query);

    $sql = "select * from games where winner = '{$loser_id}' or loser = '{$loser_id}'";
    $query = mysql_query($sql);
    $loser_games = mysql_num_rows($query);
    
    $winner_score = 1;
    $loser_score = 0;
    if($draw)
    {
        $winner_score = $loser_score = .5;
    }
    
    $draw_value = ($draw ? 1 : 0);
    if($forfeit && !$draw){
        $sql = "insert into games set created = NOW(), draw = '{$draw_value}', game_date = '{$gamedate}', winner = '{$winner_id}', winner_rating = '{$winner_rating}', loser = '{$loser_id}', loser_rating = '{$loser_rating}', tournament_id = '{$tournament_id}', forfeit = '{$loser_id}', white = '{$white_id}'";
        mysql_query($sql) or die(mysql_error());
    }
    else{
        $K_w = 32;
        if($winner_rating >= 2100 && $winner_rating <= 2400)
            $K_w = 24;
        else if($winner_rating > 2400)
            $K_w = 16;
        $E_w = 1 / ( 1 + pow(10, (($loser_rating - $winner_rating)/400)));
        $winner_new_rating = $winner_rating + ($K_w * ($winner_score - $E_w));
        $winner_new_rating = round($winner_new_rating);

        $K_l = 32;
        if($loser_rating >= 2100 && $loser_rating <= 2400)
            $K_l = 24;
        else if($loser_rating > 2400)
            $K_l = 16;
        $E_l = 1 / ( 1 + pow(10, (($winner_rating - $loser_rating)/400)));
        $loser_new_rating = $loser_rating + ($K_l  * ($loser_score - $E_l));
        $loser_new_rating = round($loser_new_rating);    


        $sql = "insert into games set created = NOW(), draw = '{$draw_value}', game_date = '{$gamedate}', winner = '{$winner_id}', winner_rating = '{$winner_new_rating}', loser = '{$loser_id}', loser_rating = '{$loser_new_rating}', tournament_id = '{$tournament_id}', white = '{$white_id}'";
        mysql_query($sql) or die(mysql_error());
        
        $sql = "update players set rating = '{$winner_new_rating}' where id = '{$winner_id}'";
        mysql_query($sql) or die(mysql_error());

        $sql = "update players set rating = '{$loser_new_rating}' where id = '{$loser_id}'";
        mysql_query($sql) or die(mysql_error());
    }
?>
<script type="text/javascript">
<!--
window.location = "index.php";
//-->
</script>

<?
}
?>


<? require_once 'footer.php'; ?>
