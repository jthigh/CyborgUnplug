<?php include 'header.php';?>

	<h1 id="headline">Configure Little Snipper</h1>
	<div id="container_general">
		<form method="get" id="start" action="cgi-bin/config.cgi">
			<input name="access point" type="submit" value="ap" class='button'>
            <br><br>
			<input name="updates" type="submit" value="update" class='button'>
            <br><br>
			<input name="alerts" type="submit" value="alerts" class='button'>
            <br><br>
        </form>
	</div>
<?php include 'footer.php';?>
