<?
require_once('config.php');
session_start();
if(!isset($_SESSION['u_login'])){
	header( "refresh:3; url=admin.php" ); 
	die("Access denied!");
}
if(!isset($_REQUEST['id'])){
	header( "refresh:3; url=admin.php" ); 
	die("ERROR: Need source id!");
} else {
	$source_id = $_REQUEST['id'];
}
?>
<html>
<header>
<title>VK2Twitter Журнал обновлений паблика</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</header>
<body>
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="admin.php">VK2Twitter панель администрирования</a>
    </div>
      <ul class="nav navbar-nav navbar-right">
	  <? if (!isset($_REQUEST['notnull'])) { ?>
		<a href="?id=<? echo $source_id . '&notnull';?>"><button type="button" class="btn btn-default navbar-btn">Убрать нулевые результаты</button></a>
	  <? } else { ?>
		<a href="?id=<? echo $source_id;?>"><button type="button" class="btn btn-default navbar-btn">Показать все результаты</button></a>
	  <? } ?>
		<a href="admin.php?clearlog=<? echo $source_id;?>"><button type="button" class="btn btn-danger navbar-btn">Очистить весь журнал</button></a>
      </ul>

  </div><!-- /.container-fluid -->
</nav>
<div class="container">

<?
mb_internal_encoding("UTF-8");

$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

/* изменение набора символов на utf8 */
if (!$mysqli->set_charset("utf8")) {
    echo '<div class="alert alert-danger" role="alert">Ошибка при загрузке набора символов UTF8: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>';
}

// Получение списка пабликов
if ($source_info = $mysqli->query("SELECT id, name, description FROM source")) {
    while($row = $source_info->fetch_assoc()) {
        $sinfo[$row['id']] = array('name'=>$row['name'], 'description'=>$row['description']);
    }
    $source_info->close();
} else {
    echo '<div class="alert alert-danger" role="alert">Не удалось получить список пабликов: '.$mysqli->error.'</div>';
}

if (isset($_REQUEST['notnull'])) {
	$notnull = "AND counter>0";
} else {
	$notnull = "";
}
if ($source_id == 0) {
	$equal = ">";
} else {
	$equal = "=";
}

if (!($sel_log = $mysqli->prepare('SELECT * FROM updatelog WHERE source_id' . $equal . '? ' . $notnull . ' ORDER BY datetime DESC'))) {
    die('<div class="alert alert-dismissible alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error);
}
if (!$sel_log->bind_param("i", $source_id)) {
    die('<div class="alert alert-dismissible alert-danger" role="alert">Не удалось привязать параметры: (' . $sel_log->errno . ') ' . $sel_log->error);
}
if (!$sel_log->execute()) {
	die('<div class="alert alert-dismissible alert-danger" role="alert">Не удалось получить журнал обновлений: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
}

$result = $sel_log->get_result();

echo '<table class="table">';
echo '<thead><tr><th>Дата и время проверки</th><th>Название паблика</th><th>Кол-во нового</th><th>Подробности</th></tr></thead><tbody>';

while ($srow = $result->fetch_assoc()) {
	$uid = uniqid() . mt_rand();
	echo '<tr><td width="170"><strong>'.$srow['datetime'].'</strong></td><td width="300">';
    echo ' '.$sinfo[$srow['source_id']]['name'].' ('.$sinfo[$srow['source_id']]['description'].')';
	echo '</td><td width="20"><center><span class="badge">' . $srow['counter'] . '</span></center></td>';
    echo '<td>';
	if ($srow['counter'] > 0) {
		echo '<button type="button" class="btn btn-info btn-xs" data-toggle="collapse" data-target="#' . $uid . '">Подробности обновления</button>';
		echo '<div id="' . $uid . '" class="collapse">';
	    echo '<table class="table table-striped table-condensed">';
		echo $srow['text'];
		echo '</table>';
		echo '</div>'; 
	} else {
		echo '<span class="label label-default">Обновлений нет</span>';
	}
    echo '</td></tr>';
}

echo '</tbody></table>';

$sel_log->close();
$mysqli->close();

?>

</div>

</body>
</html>