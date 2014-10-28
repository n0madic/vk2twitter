﻿<!DOCTYPE html>
<html>
<head>
<title>VK2Twitter Панель администрирования</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- <meta http-equiv="refresh" content="300"> -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script language="javascript"> 
$(document).ready(function() {
	function read_cookie(k,r){return(r=RegExp('(^|; )'+encodeURIComponent(k)+'=([^;]*)').exec(document.cookie))?r[2]:null;};
	delay = read_cookie("delay");
	if (delay > 0) {
		document.getElementById('timeout').value = delay;
		setTimeout("location.reload(true);", delay*1000);
	}
});
function RefreshTimeout() { 
	var delay = document.getElementById("timeout").value; 
	if (delay > 0) { 
		var timeout = setTimeout("location.reload(true);", delay*1000);
		document.cookie = "delay="+delay;
	} else {
		clearTimeout(timeout);
		document.cookie = "delay=0";
	}
} 
</script> 
</head>
<body>
<nav class="navbar navbar-default navbar-static-top" role="navigation">
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
		<input type="text" class="navbar-btn" placeholder="Секунды" id="timeout">
		<button class="btn btn-default navbar-btn" onClick="RefreshTimeout()"><span class="glyphicon glyphicon-time"></span> <span class="glyphicon glyphicon-repeat"></span> Интервал автообновления</button>
      </ul>

  </div><!-- /.container-fluid -->
</nav>
<div class="container">

<?
error_reporting(E_ERROR);
require_once('config.php');
require_once('tmhOAuth.php');

$dest_id = 0;
$source_list = $mysqli->query("SELECT * FROM source");

if (!($sel_dest = $mysqli->prepare("SELECT * FROM destination WHERE id=?"))) {
    die("Не удалось подготовить запрос: (" . $mysqli->errno . ") " . $mysqli->error);
}
if (!$sel_dest->bind_param("i", $dest_id)) {
    die("Не удалось привязать параметры: (" . $sel_dest->errno . ") " . $sel_dest->error);
}

if (!($upd_time = $mysqli->prepare("UPDATE source SET last_update = ? WHERE id = ?"))) {
    die("Не удалось подготовить запрос: (" . $mysqli->errno . ") " . $mysqli->error);
}
if (!$upd_time->bind_param("ii", $last_update, $source_id)) {
    die("Не удалось привязать параметры: (" . $upd_time->errno . ") " . $upd_time->error);
}

if (!($upd_log = $mysqli->prepare("INSERT INTO updatelog (source_id, counter, text) VALUES (?,?,?)"))) {
    die("Не удалось подготовить запрос: (" . $mysqli->errno . ") " . $mysqli->error);
}
if (!$upd_log->bind_param("iis", $source_id, $counter, $logtext)) {
    die("Не удалось привязать параметры: (" . $upd_log->errno . ") " . $upd_log->error);
}

while ($row = $source_list->fetch_assoc()) {

	$source_id = $row['id'];
	$last_update = $row['last_update'];
	$dest_id = $row['destination_id'];
	$counter = 0; $logtext = "";
	if (!$sel_dest->execute()) {
		die("Destination not found: (" . $sel_dest->errno . ") " . $sel_dest->error);
	}
	$res = $sel_dest->get_result();
	$drow = $res->fetch_assoc();
?>	
	<div class="panel panel-default">
	<div class="panel-heading">
		<font size="4">Паблик <b><a href="http://vk.com/<? echo $row['name'].'">'.$row['name'];?></a></b> (<? echo $row['description'];?>) <span class="glyphicon glyphicon-arrow-right"></span>
			<a href="http://twitter.com/<? echo $drow['name'];?>" target="_blank"><? echo $drow['display_name'];?></a></font>
		<a class="btn btn-success btn-xs pull-right" href="showlog.php?id=<? echo $row['id'];?>"><span class="glyphicon glyphicon-list"></span> Журнал обновлений</a>
	</div>
	<div class="panel-body">
	<table class="table table-striped table-bordered">
<?

	$tmhOAuth = new tmhOAuth(array(
		'consumer_key' => CONSUMER_KEY,
		'consumer_secret' => CONSUMER_SECRET,
		'user_token' => $drow['oauth_token'],
		'user_secret' => $drow['oauth_token_secret'],
	));
	$tmhOAuth->config['curl_timeout'] = 30;

	$wall = file_get_contents("http://api.vk.com/method/wall.get?domain=".$row['name']);
	$wall = json_decode($wall); // Преобразуем JSON-строку в массив
	$wall = $wall->response; // Получаем массив постов
	for ($i = count($wall); $i > 0 ; $i--) {
		if ($wall[$i]->date > $last_update) {
			$counter++;
			$status = trim($wall[$i]->text); 
			// Обновляем дату последней новости
			$last_update = $wall[$i]->date;
			if (!$upd_time->execute()) {
				echo("Last update error: (" . $upd_time->errno . ") " . $upd_time->error);
			}
			$attach_type = $wall[$i]->attachment->type;
			if ($attach_type == 'video') {
				$status = $wall[$i]->copy_text;
				$image = $wall[$i]->attachment->video->image_big;
			};
			if ($attach_type == 'photo') {
				$image = $wall[$i]->attachment->photo->src_big;
			};
			if ($attach_type == 'doc' && $wall[$i]->attachment->doc->ext == 'gif') {
				$image = $wall[$i]->attachment->doc->url;
			};
			if ($wall[$i]->post_type == 'copy') {
				$status = $wall[$i]->copy_text.' '.$wall[$i]->text;
			};
			//$status=Normalizer::normalize($status,Normalizer::FORM_C);
			$status = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $status)); // Заменяем переводы строк
			$status = preg_replace("/\[(club|id)\d+\|(.+)]/U", "$2",$status); // Удаляем метатеги
			$status = preg_replace('/\s+/', ' ',$status); // Удаляем повторяющиеся пробелы
			// Определение более точной длины будущего твита
			$tcoLengthHttp = 22;
			$tcoLengthHttps = 23;
			$twitterPicLength = 23;
			$url_regex = "/(?:((?:[^-\/".'"'."':!=a-z0-9_@＠]|^|\:))(((?:https?:\/\/|www\.)?)((?:[^\p{P}\p{Lo}\s][\.-](?=[^\p{P}\p{Lo}\s])|[^\p{P}\p{Lo}\s])+\.[a-z]{2,}(?::[0-9]+)?)(\/(?:(?:\([a-z0-9!\*';:=\+\$\/%#\[\]\-_,~]+\))|@[a-z0-9!\*';:=\+\$\/%#\[\]\-_,~]+\/|[\.\,]?(?:[a-z0-9!\*';:=\+\$\/%#\[\]\-_~]|,(?!\s)))*[a-z0-9=#\/]?)?(\?[a-z0-9!\*'\(\);:&=\+\$\/%#\[\]\-_\.,~]*[a-z0-9_&=#\/])?))/iux";
			// Определяем максимальную длину обычного текста
			$totalchars = 140 - $tcoLengthHttps - 2; // Отнимаем от максимально возможной длины твита длину сокращенного url и "… "
			if (isset($image)) $totalchars -= $twitterPicLength; // Отнимаем длину ссылки на картинку если она есть
			// Прикидываем реальный размер твита, попутно его обрезая до максимально возможного
			$words = preg_split('/\s+/', $status);
			$status_truncated = '';
			$tweetLength = 0;
			foreach ($words as $word) {
				// Если слово ссылка то добавляем длину короткой ссылки, иначе длину слова
				if (preg_match($url_regex, $word)) {
					$tweetLength += mb_stristr($word, 'https') !== FALSE ? tcoLengthHttps : tcoLengthHttp;
				} else {
					$tweetLength += mb_strlen($word);
				}
				// Заодно подготавливаем короткую версию твита
				if ($tweetLength < $totalchars) {
					$status_truncated .= $word . ' ';
				}
				$tweetLength++; // добавляем длину пробела
			}
			// Отбросим лишний последний пробел
			$tweetLength--;
			// Добавим длину ссылки на картинку если она есть
			if (isset($image)) $tweetLength += $twitterPicLength;
			// Если твит получается слишком длинным или есть продолжение то усекаем его
			if (count($wall[$i]->attachments) > 1 || $tweetLength > 140 || $attach_type == 'video' || $attach_type == 'poll') {
				$status = $status_truncated . "… https://vk.com/wall".$wall[$i]->from_id."_".$wall[$i]->id;
			}
			$logtext = $logtext . "<tr><td>Lenght: ".mb_strlen($status)."<br>  <i>".$status."</i><br />";
			// Постим в Твиттер если не localhost
			If (isset($image)) {
				$logtext = $logtext . "<img src=".$image."><br />";
				if ($_SERVER['HTTP_HOST'] <> 'localhost') {
					$image = file_get_contents($image);
					$response = $tmhOAuth->request('POST', 'https://api.twitter.com/1.1/statuses/update_with_media.json',
						array(
							'media[]'  => $image,
							'status'   => $status
						),
						true, // use auth
						true  // multipart
					); 
				} else { $response = 200; }
				unset($image);
			} else { 
				if ($_SERVER['HTTP_HOST'] <> 'localhost') {
					$response = $tmhOAuth->request('POST', 'https://api.twitter.com/1.1/statuses/update.json',
						array(
							'status'   => $status
						),
						true // use auth
					); 
				} else { $response = 200; }
			}
			if ($response <> 200) {
				$error = json_decode($tmhOAuth->response['response']);
				$logtext = $logtext . '<div class="alert alert-danger" role="alert">Ошибка размещения статуса в Twitter: ' . $error->errors[0]->message . '</div>';
			}; 
			$logtext = $logtext . '</td></tr>';
		}
	}
	echo $logtext;
?>
	</table>
<? echo 'Всего новых статей: <span class="badge">' . $counter . '</span>'; ?>
	</div>
	</div>
<?
	if (!$upd_log->execute()) {
		echo("LOG doesn't updated: (" . $upd_log->errno . ") " . $upd_log->error);
	}

}

$sel_dest->close();
$upd_log->close();
$mysqli->close();	

?>
</div>

</body>
</html>
