<? require_once 'header.php'; ?>

<h1>MyPublisher Chess Ratings : Add Tournament</h1>


<form method="POST" action="addtournament.php">

<dl>
    <dt>Name</dt>
    <dd><input type="text" name="name" value="" /></dd>
    
    <dt>Background Color (Format like #123XYZ)</dt>
    <dd><input type="text" name="bgcolor" value="#" /></dd>

    <dt>Font Color (Format like #123XYZ)</dt>
    <dd><input type="text" name="fontcolor" value="#" /></dd>
    
    <br />
    <dt></dt>
    <dd><input type="submit" value="Add" /></dd>
</dl>

</form>

<? require_once 'footer.php'; ?>
