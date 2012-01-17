<? require_once 'header.php'; ?>
<? require_once 'functions.php'; ?>
<?
$id = $_GET['id'];

$sql = "select created, active, name, rating from players where id = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$fetch = mysql_fetch_row($query);
list($created, $active, $name, $rating) = $fetch;

$ratings = get_player_ratings($id);


// Wins
$sql = "select * from games where  winner = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$total_wins = mysql_num_rows($query);

// Losses
$sql = "select * from games where loser = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$total_losses = mysql_num_rows($query);

// Draws
$sql = "select * from games where draw = 1 and (winner = '" . mysql_real_escape_string($id) . "' or loser = '" . mysql_real_escape_string($id) . "')";
$query = mysql_query($sql) or die(mysql_error());
$total_draws = mysql_num_rows($query);

// Wins as White
$sql = "select * from games where white = winner and winner = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$white_wins = mysql_num_rows($query);

// Losses as White
$sql = "select * from games where white = loser and loser = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$white_losses = mysql_num_rows($query);

// Draws as White
$sql = "select * from games where draw = 1 and white = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$white_draws = mysql_num_rows($query);

// Wins as Black
$sql = "select * from games where white != winner and white != 0 and winner = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$black_wins = mysql_num_rows($query);

// Losses as Black
$sql = "select * from games where white != loser and white != 0 and loser = '" . mysql_real_escape_string($id) . "'";
$query = mysql_query($sql) or die(mysql_error());
$black_losses = mysql_num_rows($query);

// Draws as Black
$sql = "select * from games where draw = 1 and white != '" . mysql_real_escape_string($id) . "' and white != 0 and (winner = '" . mysql_real_escape_string($id) . "' or loser = '" . mysql_real_escape_string($id) . "')";
$query = mysql_query($sql) or die(mysql_error());
$black_draws = mysql_num_rows($query);

?>


<h1>MyPublisher Chess Ratings</h1>
<a href="index.php">Back to Home</a>
<p></p>
<dl>
    <dt>Player</dt>
    <dd><?= $name; ?></dd>

    <dt>Active?</dt>
    <dd><?= ($active ? 'Yes' : 'No'); ?></dd>

    <dt>Rating</dt>
    <dd><?= $rating; ?></dd>

    <dt>Total Record</dt>
    <dd>Wins: <?= $total_wins; ?> - Draws: <?= $total_draws; ?> - Losses: <?= $total_losses; ?></dd> 
    
    <dt>White Record</dt>
    <dd>Wins: <?= $white_wins; ?> - Draws: <?= $white_draws; ?> - Losses: <?= $white_losses; ?></dd> 

    <dt>Black Record</dt>
    <dd>Wins: <?= $black_wins; ?> - Draws: <?= $black_draws; ?> - Losses: <?= $black_losses; ?></dd> 
</dl>

<div id="chartdiv" style="height:400px;width:800px;"></div>
<? generate_jqplot("chartdiv", array($name => $ratings)); ?>

<br />
<h2>Record Against Other Players</h2>
<table border="1">
    <tr>
        <th>Player</th>
        <th>Rating</th>
        <th>Rank</th>
        <th>Games</th>
        <th>Record</th>
    </tr>
<?
$sql = "select id, name, rating from players where id != '{$id}' and active order by rating desc";
$query = mysql_query($sql);
$all_ratings = array();
while($fetch = mysql_fetch_row($query)){
    list($opponent_id, $opponent_name, $opponent_rating) = $fetch;
    
    $sql = "select winner, loser, draw from games where (winner = '{$id}' and loser = '{$opponent_id}') or (winner = '{$opponent_id}' and loser = '{$id}')";
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
    
    $opponent_rank = rating_to_rank($opponent_rating);
    $opponent_class = rank_to_css_class($opponent_rank);
?>
    <tr>
        <td class="<?= $opponent_class; ?>"><a href="playerdetail.php?id=<?= $opponent_id; ?>"><?= $opponent_name; ?></a></td>
        <td class="<?= $opponent_class; ?>"><?= $opponent_rating; ?></td>
        <td class="<?= $opponent_class; ?>"><?= $opponent_rank; ?></td>
        <td class="<?= $opponent_class; ?>"><?= $games; ?></td>
        <td class="<?= $opponent_class; ?>"><?= $won . '-' . $draw . '-' . $lost; ?></td>
    </tr>
<?
}
?>
    </table>
    <br />



    <h3>Games</h3>

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
$sql = "select games.game_date, games.draw, winners.id, winners.name, games.winner_rating, losers.id, losers.name, games.loser_rating, tournaments.bgcolor, tournaments.fontcolor, games.white from (games, players as winners, players as losers) left join tournaments on (games.tournament_id = tournaments.id) where (games.winner = winners.id) and (games.loser = losers.id) and (games.winner = '" . mysql_real_escape_string($id) . "' or games.loser = '" . mysql_real_escape_string($id) . "') order by game_date asc";
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


<? require_once 'footer.php'; ?>
