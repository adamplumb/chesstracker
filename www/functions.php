<?

function get_player_ratings($id, $from=''){
    $from_sql = '';
    if($from)
        $from_sql = " and game_date >= '{$from}'";

    $ratings = array();
    $sql = "select game_date, winner, winner_rating, loser, loser_rating from games where (winner = '" . mysql_real_escape_string($id) . "' or loser = '" . mysql_real_escape_string($id) . "'){$from_sql} order by created asc";
    $query = mysql_query($sql) or die($sql . "<br />" . mysql_error());
    $player_min = 1200;
    $player_max = 1200;
    $first_game_date = 0;
    while($fetch = mysql_fetch_row($query)){
        list($game_date, $winner, $winner_rating, $loser, $loser_rating) = $fetch;
        
        $player = '';
        if($winner == $id){
            $rating = $winner_rating;
        }
        else if($loser == $id){
            $rating = $loser_rating;
        }
        
        if($player_min > $rating)
            $player_min = $rating;
        if($player_max < $rating)
            $player_max = $rating;
        
        if(!$first_game_date)
            $first_game_date = $game_date;
        
        $ratings[$game_date] = $rating;
    }
    
    $first_game_date = date('Y-m-d', strtotime($first_game_date)-86400);

    $ratings[$first_game_date] = 1200;
    
    ksort($ratings);
    return array(
        'ratings'   => $ratings,
        'max'       => $player_max,
        'min'       => $player_min
    );
}

function generate_jqplot($div_id, $all_ratings)
{
    $all_plots = array();
    $series = array();
    foreach($all_ratings as $player => $player_ratings){
        $plots = array();
        foreach($player_ratings['ratings'] as $game_date => $rating){
            $plots[] = '["' . $game_date . '",' . $rating . ']';
        }
        $all_plots[] = '[' . implode(",", $plots) . ']';

        $series[] = "{label:'{$player}'}"; 
    }

    $all_plots = '[' . implode(",", $all_plots) . ']';
    $series = implode(",", $series);
?>
<script type="text/javascript">
$.jqplot("<?= $div_id; ?>", <?= $all_plots; ?>, {
    title: 'Ratings over time',
    axes:{xaxis:{renderer:$.jqplot.DateAxisRenderer}},
    series:[<?= $series; ?>],
    legend:{show:true, location:'nw'}
});
</script>
<?
}

?>
