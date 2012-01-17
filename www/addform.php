<? require_once 'header.php'; ?>

<h1>MyPublisher Chess Ratings : Add Game</h1>

<?
$players = array();
$tournaments = array();

$sql = "select id, name from players order by name asc";
$query = mysql_query($sql);
while($fetch = mysql_fetch_row($query)){
    $players[$fetch[0]] = $fetch[1];
}

$sql = "select id, name from tournaments where active order by name asc";
$query = mysql_query($sql);
while($fetch = mysql_fetch_row($query)){
    $tournaments[$fetch[0]] = $fetch[1];
}
?>

<form method="POST" action="add.php">

<dl>
    <dt>White</dt>
    <dd>
        <select name="white">
            <option value="">Select</option>
<?
foreach($players as $id => $name)
{
?>
            <option value="<?= $id; ?>"><?= $name; ?></option>
<?
}
?>
        </select>
    </dd>
    <br />
    <dt>Black</dt>
    <dd>
        <select name="black">
            <option value="">Select</option>
<?
foreach($players as $id => $name)
{
?>
            <option value="<?= $id; ?>"><?= $name; ?></option>
<?
}
?>
        </select>
    </dd>
        <br />

    <dt>Winner</dt>
    <dd>
        <input type="radio" name="winner" value="white" /> White
        <input type="radio" name="winner" value="black" /> Black
        <input type="radio" name="winner" value="draw" checked="true" /> Draw
    </dd>
    <br />

    <dt>Tournament</dt>
    <dd>
        <select name="tournament">
            <option value="0">Not part of a tournament</option>
<?
foreach($tournaments as $id => $name)
{
?>
            <option value="<?= $id; ?>"><?= $name; ?></option>
<?
}
?>
        </select>
    </dd>
        <br />

    <dt>Game Date (YYYY-mm-dd)</dt>
    <dd><input type="text" name="date" /></dd>
    
    <br />
    <dt>Forfeit?</dt>
    <dd><input type="checkbox" name="forfeit" value="1" /></dd>
    
    <br />
    <dt></dt>
    <dd><input type="submit" value="Add" /></dd>
</dl>

</form>

<? require_once 'footer.php'; ?>
