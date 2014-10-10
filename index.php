<!DOCTYPE html>
<html>
    <head>
        <title>Vkontakte to Twitter</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="keywords" content="RSS, Atom, feed, full, full text, full content, full article">
        <link href="favicon.ico" rel="icon" type="image/x-icon" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
    <div class="page-header">
        <h1>Vkontakte to Twitter translation<small> by Nomadic</small></h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
    	<h3 class="panel-title"> Available translation:
    	</h3>
        </div>
        <div class="panel-body">
		<table class="table table-striped table-hover col-md-4" style="font-size:15px;">
			<thead>
				<tr><th>Имя паблика Vkontakte</th><th>Twitter аккаунт</th></tr>
			</thead>
			<tbody>
				<?php
				error_reporting(E_ERROR);
				require_once('config.php');

				$list = $mysqli->query("SELECT destination.name as dname, destination.display_name, source.name as sname, source.description 
											FROM source, destination 
										WHERE source.destination_id = destination.id
										ORDER BY source.description");
				while ($row = $list->fetch_assoc()) {
					echo('<tr><td><img src="https://vk.com/favicon.ico"> <a href="https://vk.com/' . $row['sname'] . '" target="_blank">' . $row['description'] . '</a></td><td><img src="http://g.twimg.com/Twitter_logo_blue.png" alt="Twitter" width="16" height="13"> <a href="https://twitter.com/' . $row['dname'] . '" target="_blank">' . $row['display_name'] . '</a></td></tr>'.PHP_EOL);
				}
				?>
			</tbody>
		</table>
        </div> <!-- panel-body -->
    </div> <!-- panel -->
	<footer class="navbar-fixed-bottom">
		<div style="text-align: center;"><p><a href="https://github.com/n0madic/vk2twitter">GitHub</a> &copy; Nomadic 2014</p></div>
	</footer>
</div> <!-- container -->
</body>
</html>