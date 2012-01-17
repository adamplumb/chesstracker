<? require_once 'header.php'; ?>
<? require_once 'functions.php'; ?>

<h1>MyPublisher Chess Ratings</h1>
<a href="addform.php">Add a game</a> | <a href="addtournamentform.php">Add a tournament</a>
<br /><br />

<table>

<tr>

<td valign="top" width="125">

    <table border="1">
    <?
    $rranks = $ranks;
    $rranks = array_reverse($rranks);

    foreach($rranks as $key => $range)
    {
        $class = rank_to_css_class($key);
    ?>
        <tr><td class="<?= $class; ?>" align="center"><?= $key; ?><br /><?= $range[0]; ?> to <?= $range[1] ; ?></td></tr>
    <?
    }
    ?>
    </table>


</td>

<td valign="top">

    <h3>Players (<a href="chart.php">Chart</a>)</h3>
    <table border="1">
    <tr>
        <th>Player</th>
        <th>Rating</th>
        <th>Rank</th>
        <th>Games</th>
        <th>W-D-L</th>
    </tr>
<?
$sql = "select id, name, rating from players where active order by rating desc";
$query = mysql_query($sql);
$all_ratings = array();
while($fetch = mysql_fetch_row($query)){
    list($id, $name, $rating) = $fetch;
    
    $sql = "select winner, loser, draw from games where winner = '{$id}' or loser = '{$id}'";
    $query2 = mysql_query($sql) or die(mysql_error());
    $won = $lost = $draw = $games = 0;
    while($fetch2 = mysql_fetch_row($query2)){
        list($winner, $loser, $drawn) = $fetch2;
        
        if($drawn)
        {
            $draw++;
        }
        else{
            if($winner == $id)
                $won++;
            if($loser == $id)
                $lost++;
        }
        $games++;
    }
    
    $rank = rating_to_rank($rating);
    $class = rank_to_css_class($rank);
?>
    <tr>
        <td class="<?= $class; ?>"><a href="playerdetail.php?id=<?= $id; ?>"><?= $name; ?></a></td>
        <td class="<?= $class; ?>"><?= $rating; ?></td>
        <td class="<?= $class; ?>"><?= $rank; ?></td>
        <td class="<?= $class; ?>"><?= $games; ?></td>
        <td class="<?= $class; ?>"><?= $won . '-' . $draw . '-' . $lost; ?></td>
    </tr>
<?
}
?>
    </table>
    <br />

<?


$sql = "select * from games where white = winner";
$query = mysql_query($sql) or die(mysql_error());
$white_wins = mysql_num_rows($query);

$sql = "select * from games where white = loser";
$query = mysql_query($sql) or die(mysql_error());
$black_wins = mysql_num_rows($query);

$sql = "select * from games where draw = 1";
$query = mysql_query($sql) or die(mysql_error());
$total_draws = mysql_num_rows($query);


?>


    <h3>Games</h3>
    
    <b>White: <?= $white_wins; ?>, Draw: <?= $total_draws; ?>, Black: <?= $black_wins; ?></b><br /><Br />
    
    <table border="1">
    <tr>
        <th>Game Date</th>
        <th>Draw?</th>
        <th>Winner</th>
        <th>Winner Rating</th>
        <th>Loser</th>
        <th>Loser Rating</th>
    </tr>
<?
$sql = "select games.game_date, games.draw, winners.id, winners.name, games.winner_rating, losers.id, losers.name, games.loser_rating, tournaments.bgcolor, tournaments.fontcolor, games.white from (games, players as winners, players as losers) left join tournaments on (games.tournament_id = tournaments.id) where (games.winner = winners.id) and (games.loser = losers.id) order by game_date asc";
$query = mysql_query($sql);
while($fetch = mysql_fetch_row($query)){
    list($game_date, $draw, $winner_id, $winner, $winner_rating, $loser_id, $loser, $loser_rating, $tournament_bgcolor, $tournament_fontcolor, $white_id) = $fetch;
    $winner_color = '';
    $loser_color = '';
    if($white_id)
    {
        if($white_id == $winner_id)
        {
            $winner_color = ' (W)';
            $loser_color = ' (B)';
        }
        else
        {
            $winner_color = ' (B)';
            $loser_color = ' (W)';
        }
    }
?>
    <tr>
        <td style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>"><?= $game_date; ?></td>
        <td style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>"><?= $draw; ?></td>
        <td style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>"><a href="playerdetail.php?id=<?= $winner_id; ?>" style="color:<?= $tournament_fontcolor; ?>"><?= $winner . $winner_color; ?></a></td>
        <td style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>"><?= $winner_rating; ?></td>
        <td style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>"><a href="playerdetail.php?id=<?= $loser_id; ?>" style="color:<?= $tournament_fontcolor; ?>"><?= $loser . $loser_color; ?></a></td>
        <td style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>"><?= $loser_rating; ?></td>
    </tr>
<?
}
?>
    </table>

    <br />

    <h3>Tournaments</h3>
<?
$records = array();

$sql = "select id, name, bgcolor, fontcolor from tournaments where active order by created desc";
$query = mysql_query($sql);
while($fetch = mysql_fetch_row($query)){
    list($tournament_id, $tournament_name, $tournament_bgcolor, $tournament_fontcolor) = $fetch;

    $sql2 = "select * from games where white = winner and tournament_id = '{$tournament_id}'";
    $query2 = mysql_query($sql2) or die(mysql_error());
    $white_wins = mysql_num_rows($query2);

    $sql2 = "select * from games where white = loser and tournament_id = '{$tournament_id}'";
    $query2 = mysql_query($sql2) or die(mysql_error());
    $black_wins = mysql_num_rows($query2);

    $sql2 = "select * from games where draw = 1 and tournament_id = '{$tournament_id}'";
    $query2 = mysql_query($sql2) or die(mysql_error());
    $total_draws = mysql_num_rows($query2);

?>
    <table border="1" style="background-color:<?= $tournament_bgcolor; ?>;color:<?= $tournament_fontcolor; ?>">
    <tr><th colspan="3"><b><?= $tournament_name; ?></b></th></tr>
    <tr><th colspan="3">White: <?= $white_wins; ?>, Draw: <?= $total_draws; ?>, Black: <?= $black_wins; ?></th></tr>
    <tr>
        <th>Player</th>
        <th>Points</th>
        <th>W-D-L</th>
    </tr>
<?
    $sql2 = "select id, name, rating from players where active order by name asc";
    $query2 = mysql_query($sql2);
    $points_arr = array();
    $points = 0;
    while($fetch2 = mysql_fetch_row($query2)){
        list($id, $name, $rating) = $fetch2;
                
        $sql3 = "select * from games where draw = 0 and winner = '{$id}' and tournament_id = '{$tournament_id}'";
        $query3 = mysql_query($sql3) or die(mysql_error());
        $wins = mysql_num_rows($query3);
        
        $sql3 = "select * from games where draw = 0 and loser = '{$id}' and tournament_id = '{$tournament_id}'";
        $query3 = mysql_query($sql3) or die(mysql_error());
        $losses = mysql_num_rows($query3);
        
        $sql3 = "select * from games where draw = 1 and (winner = '{$id}' or loser = '{$id}') and tournament_id = '{$tournament_id}'";
        $query3 = mysql_query($sql3) or die(mysql_error());
        $draws = mysql_num_rows($query3);
        
        $points_arr[$name] = $wins + $draws/2;
        $records[$name] = "{$wins}-{$draws}-{$losses}";
    }
    
    arsort($points_arr);
    foreach($points_arr as $person => $loop_points)
    {
?>
    <tr>
        <td><a href="playerdetail.php?id=<?= $id; ?>" style="color:<?= $tournament_fontcolor; ?>"><?= $person; ?></a></td>
        <td><?= $loop_points; ?></td>
        <td><?= $records[$person]; ?></td>
    </tr>
<?
    }
?>
    </table>
    <br />
<?
}
?>

</td></tr></table>

<? require_once 'footer.php'; ?>
