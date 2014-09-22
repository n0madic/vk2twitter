<?
require_once('config.php');
session_start();
?>
<html>
<header>
<title>VK2Twitter Панель администрирования</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("div.alert-success").delay(3000).fadeOut(400);
	$("div.alert-warning").delay(9000).fadeOut(400);
});
function triggerAddModal(id) {
    document.getElementById('public_insert').value = id;
    $('#AddPublicModal').modal();
}
function triggerEditModal(name, description, id) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-description').value = description;
    document.getElementById('public_edit').value = id;
    $('#EditPublicModal').modal();
}
</script>
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
		<a href="update_post.php"><button type="button" class="btn btn-default navbar-btn">Обновить все посты</button></a>
		<a class="btn btn-success" href="showlog.php?id=0&notnull">Общий журнал обновлений</a>
      </ul>

  </div><!-- /.container-fluid -->
</nav>
<div class="container">
<?
// Проверка пароля
if(isset($_POST['passwd'])){
    if(md5($_POST['passwd']) == ADMIN_PASS){
        $_SESSION['u_login']='YES';
    } else {
		unset($_SESSION['u_login']);
		session_destroy();
	}
}

if(!isset($_SESSION['u_login'])){
    ?>
	<label for="InputPassword1">Please enter password for access:</label>
	<form class="form-inline" role="form" method="post">
	<div class="form-group">
		<label class="sr-only" for="InputPassword2">Password</label>
		<input type="password" name="passwd" class="form-control" id="InputPassword2" placeholder="Password">
	</div>
	<button type="submit" class="btn btn-large btn-primary">Sign in</button>
	</form>
	</div>
	
	</body>
	</html>
<?
	exit;
}
?>

<!-- Add Twitter account -->
<div class="modal fade" id="AddTwitterModal" tabindex="-1" role="dialog" aria-labelledby="AddTwitterLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <form class="form-horizontal" role="form" action="admin.php" method="POST">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="AddModalLabel">Добавить Twitter аккаунт</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="twitter_name" class="col-sm-4 control-label">Имя аккаунта (http://twitter.com/...)</label>
			<div class="col-sm-6">
			<input class="form-control" name="twitter_name" id="twitter_name">
			</div>
		</div>
		<div class="form-group">
			<label for="display_name" class="col-sm-4 control-label">Имя для отображения</label>
			<div class="col-sm-6">
			<input class="form-control" name="display_name" id="display_name">
			</div>	
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary btn-confirm">Добавить</button>
      </div>
	</form>
    </div>
  </div>
</div>
<!-- Add Public Modal -->
<div class="modal fade" id="AddPublicModal" tabindex="-1" role="dialog" aria-labelledby="AddModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	  <form class="form-horizontal" role="form" action="admin.php" method="POST">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="AddModalLabel">Добавить паблик Vkontakte</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="name" class="col-sm-3 control-label">Имя паблика</label>
			<div class="col-sm-7">
			<input class="form-control" name="name" id="name">
			</div>
		</div>
		<div class="form-group">
			<label for="description" class="col-sm-3 control-label">Описание</label>
			<div class="col-sm-7">
			<input class="form-control" name="description" id="description">
			<input class="form-control hidden" name="public_insert" id="public_insert">
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary btn-confirm">Добавить</button>
      </div>
	</form>
    </div>
  </div>
</div>
<!-- Edit Public Modal -->
<div class="modal fade" id="EditPublicModal" tabindex="-1" role="dialog" aria-labelledby="EditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	  <form class="form-horizontal" role="form" action="admin.php" method="POST">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="EditModalLabel">Изменить паблик Vkontakte</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="name" class="col-sm-3 control-label">Имя паблика</label>
			<div class="col-sm-7">
			<input class="form-control" name="edit-name" id="edit-name">
			</div>
		</div>
		<div class="form-group">
			<label for="description" class="col-sm-3 control-label">Описание</label>
			<div class="col-sm-7">
			<input class="form-control" name="edit-description" id="edit-description">
			<input class="form-control hidden" name="public_edit" id="public_edit">
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary btn-confirm">Изменить</button>
      </div>
	</form>
    </div>
  </div>
</div>

<?
require_once('tmhOAuth.php');

mb_internal_encoding("UTF-8");

$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

/* изменение набора символов на utf8 */
if (!$mysqli->set_charset("utf8")) {
    echo '<div class="alert alert-danger" role="alert">Ошибка при загрузке набора символов UTF8: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>';
}


// Добавление твиттера

if (isset($_REQUEST['twitter_name'])) {
	if (empty($_REQUEST['twitter_name']) || empty($_REQUEST['display_name']))  {
		echo '<div class="alert alert-danger" role="alert">Не указаны все необходимые данные для добавления!</div>';
	} else {
		$name = trim($_REQUEST['twitter_name']);
		$display_name = trim($_REQUEST['display_name']);
		if (!($insert_dest = $mysqli->prepare("INSERT INTO destination (name, display_name) VALUES (?,?)"))) {
			die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
		}
		if (!$insert_dest->bind_param("ss", $name, $display_name)) {
			die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $insert_dest->errno . ') ' . $insert_dest->error.'</div>');
		}
		if (!$insert_dest->execute()) {
			die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $insert_dest->errno . ') ' . $insert_dest->error.'</div>');
		} else {
			echo '<div class="alert alert-success" role="alert">Новый аккаунт Твиттера успешно добавлен!</div>';
		}
		$insert_dest->close();
	}
}

// Удаление твиттера

if (isset($_REQUEST['delete'])) {
	$dest_id = $_REQUEST['delete'];
	if (!($delete_destination = $mysqli->prepare("DELETE FROM destination WHERE id=?"))) {
		die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
	}
	if (!$delete_destination->bind_param("i", $dest_id)) {
		die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $delete_destination->errno . ') ' . $delete_destination->error.'</div>');
	}
	if (!($delete_dsource = $mysqli->prepare("DELETE source, updatelog FROM source LEFT JOIN updatelog ON updatelog.source_id = source.id WHERE source.destination_id=?"))) {
		die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
	}
	if (!$delete_dsource->bind_param("i", $dest_id)) {
		die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $delete_dsource->errno . ') ' . $delete_dsource->error.'</div>');
	}
	if (!$delete_dsource->execute()) {
		die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $delete_dsource->errno . ') ' . $delete_dsource->error.'</div>');
	}
	if (!$delete_destination->execute()) {
		die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $delete_destination->errno . ') ' . $delete_destination->error.'</div>');
	} else {
		echo '<div class="alert alert-warning" role="alert">Аккаунт Твиттера успешно удален!</div>';
	}
	$delete_dsource->close;
	$delete_destination->close;
}

// Добавление паблика

if (isset($_REQUEST['public_insert'])) {
	if (empty($_REQUEST['name']))  {
		echo '<div class="alert alert-danger" role="alert">Не указаны все необходимые данные для добавления!</div>';
	} else {
		$name = trim($_REQUEST['name']);
		$description = trim($_REQUEST['description']);
		$destination_id = trim($_REQUEST['public_insert']);
		if (!($insert_source = $mysqli->prepare("INSERT INTO source (name, description, destination_id) VALUES (?,?,?)"))) {
			die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
		}
		if (!$insert_source->bind_param("ssi", $name, $description, $destination_id)) {
			die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $insert_source->errno . ') ' . $insert_source->error.'</div>');
		}
		if (!$insert_source->execute()) {
			die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $insert_source->errno . ') ' . $insert_source->error.'</div>');
		} else {
			echo '<div class="alert alert-success" role="alert">Паблик успешно добавлен!</div>';
		}
		$insert_source->close();
	}
}

// Удаление паблика

if (isset($_REQUEST['public_delete'])) {
	$public_id = $_REQUEST['public_delete'];
	if (!($delete_source = $mysqli->prepare("DELETE source, updatelog FROM source LEFT JOIN updatelog ON updatelog.source_id = source.id WHERE source.id = ?"))) {
		die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
	}
	if (!$delete_source->bind_param("i", $public_id)) {
		die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $delete_source->errno . ') ' . $delete_source->error.'</div>');
	}
	if (!$delete_source->execute()) {
		die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $delete_source->errno . ') ' . $delete_source->error.'</div>');
	} else {
		echo '<div class="alert alert-warning" role="alert">Паблик успешно удален!</div>';
	}
	$delete_source->close;
}

// Изменение паблика

if (isset($_REQUEST['public_edit'])) {
	if (empty($_REQUEST['edit-name']))  {
		echo '<div class="alert alert-danger" role="alert">Не указано имя паблика!</div>';
	} else {
		$name = trim($_REQUEST['edit-name']);
		$description = trim($_REQUEST['edit-description']);
		$public_id = trim($_REQUEST['public_edit']);
		if (!($edit_source = $mysqli->prepare("UPDATE source SET name = ?, description = ? WHERE id = ?"))) {
			die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
		}
		if (!$edit_source->bind_param("ssi", $name, $description, $public_id)) {
			die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $edit_source->errno . ') ' . $edit_source->error.'</div>');
		}
		if (!$edit_source->execute()) {
			die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $edit_source->errno . ') ' . $edit_source->error.'</div>');
		} else {
			echo '<div class="alert alert-success" role="alert">Паблик успешно изменен!</div>';
		}
		$edit_source->close;
	}
}

// Удаление лога паблика

if (isset($_REQUEST['clearlog'])) {
	$public_id = $_REQUEST['clearlog'];
	if ($_REQUEST['clearlog'] == 0) {
		$whereclear = 'WHERE source_id>?';
	} else {
		$whereclear = 'WHERE source_id=?';
	}
	if (!($delete_log = $mysqli->prepare("DELETE FROM updatelog " . $whereclear))) {
		die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
	}
	if (!$delete_log->bind_param("i", $public_id)) {
		die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $delete_log->errno . ') ' . $delete_log->error.'</div>');
	}
	if (!$delete_log->execute()) {
		die('<div class="alert alert-danger" role="alert">Запрос не выполнен: (' . $delete_log->errno . ') ' . $delete_log->error.'</div>');
	} else {
		echo '<div class="alert alert-success" role="alert">Журнал обновлений паблика успешно удален!</div>';
	}
	$delete_log->close;
}


$dest_id = 0;
$destination_list = $mysqli->query("SELECT * FROM destination");

if (!($sel_source = $mysqli->prepare("SELECT * FROM source WHERE destination_id=?"))) {
    die('<div class="alert alert-danger" role="alert">Не удалось подготовить запрос: (' . $mysqli->errno . ') ' . $mysqli->error.'</div>');
}
if (!$sel_source->bind_param("i", $dest_id)) {
    die('<div class="alert alert-danger" role="alert">Не удалось привязать параметры: (' . $sel_source->errno . ') ' . $sel_source->error.'</div>');
}

while ($row = $destination_list->fetch_assoc()) {

	$dest_id = $row['id'];
?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<font size="5"><a href="http://twitter.com/<? echo $row['name'];?>" target="_blank"><? echo $row['display_name'];?></a></font>
			<?if (empty($row['oauth_token']) || empty($row['oauth_token_secret']))  {
				echo '<a class="btn btn-warning btn-sm" href=get_access_token.php?id='.$dest_id.'>Требуется OAuth авторизация в Твиттере</a>';
			}?>
			<a class="btn btn-danger btn-sm pull-right" href="?delete=<? echo $dest_id ?>">Удалить аккаунт</a>
		</div>
		<div class="panel-body">
<?
	if (!$sel_source->execute()) {
		echo('<div class="alert alert-danger" role="alert">Не удалось найти список пабликов: (' . $sel_source->errno . ') ' . $sel_source->error.'</div>');
	} else {
?>

		<table class="table table-striped">
		<thead>
		<tr>
          <th>#</th>
          <th>Имя паблика Vkontakte</th>
          <th>Описание</th>
          <th>Действие</th>
        </tr>
		</thead>
		<tbody>
<?
        $result = $sel_source->get_result();
		$index = 0;
		while ($srow = $result->fetch_assoc()) {
			$index++;
			echo '<tr><td>'.$index.'</td><td><a href=https://vk.com/'.$srow['name'].' target="_blank">'.$srow['name'].'</a></td><td>'.$srow['description'].'</td>';
?>
			<td>
			<a class="btn btn-success btn-xs" href="showlog.php?id=<? echo $srow['id'];?>&notnull">Журнал обновлений</a>
			<button class="btn btn-info btn-xs" data-toggle="modal" onClick="triggerEditModal('<? echo $srow['name'];?>','<? echo $srow['description'];?>','<? echo $srow['id'];?>')">Изменить</button>
			<a class="btn btn-danger btn-xs" href="?public_delete=<? echo $srow['id'];?>">Удалить</a>
			</td>
		</tr>
<?			
			$result->free;
		} 
?>
		</tbody>
		</table>
		<button class="btn btn-primary btn-sm" data-toggle="modal" onClick="triggerAddModal('<? echo $dest_id ?>')">Добавить паблик</button>

	  </div>
	</div>

<?
	}
}

$sel_source->close();
$mysqli->close;	

?>
<button class="btn btn-primary" data-toggle="modal" data-target="#AddTwitterModal">
Добавить Twitter учетку
</button>
</div>

</body>
</html>