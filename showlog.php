<?php
require_once('config.php');
session_start();
if (!isset($_SESSION['u_login'])) {
    header("refresh:3; url=admin.php");
    die("Access denied!");
}
if (!isset($_REQUEST['id'])) {
    header("refresh:3; url=admin.php");
    die("ERROR: Need source id!");
} else {
    $source_id = $_REQUEST['id'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>VK2Twitter Журнал обновлений паблика</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap-theme.min.css">
    <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/latest/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="admin.php">VK2Twitter панель администрирования</a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <a href="admin.php?clearlog=<?php echo $source_id; ?>">
                <button type="button" class="btn btn-danger navbar-btn"><span class="glyphicon glyphicon-remove"></span>
                    Очистить весь журнал
                </button>
            </a>
            <?php if (!isset($_REQUEST['notnull'])) { ?>
                <a href="?id=<?php echo $source_id . '&notnull'; ?>">
                    <button type="button" class="btn btn-default navbar-btn"><span
                                class="glyphicon glyphicon-filter"></span> Убрать нулевые результаты
                    </button>
                </a>
            <?php } else { ?>
                <a href="?id=<?php echo $source_id; ?>">
                    <button type="button" class="btn btn-default navbar-btn"><span
                                class="glyphicon glyphicon-filter"></span> Показать все результаты
                    </button>
                </a>
            <?php } ?>
        </ul>

    </div><!-- /.container-fluid -->
</nav>
<div class="container">

    <?php
    if ($source_id == '0') {
        // Если надо показать общий лог то перебираем все паблики
        $logs = [];
        foreach ($config->twitters as $twitter_name => $twitter) {
            foreach ($twitter->sources as $source_name => $source) {
                $json = $memcache->get("log." . $source_name);
                if ($json != false) {
                    // Сливаем все логи в один массив
                    $logs = array_merge($logs, json_decode($json, JSON_OBJECT_AS_ARRAY));
                }
            }
        }
        arsort($logs);
    } else {
        // Или загружаем только лог указанного паблика
        $logs = $memcache->get("log." . $source_id);
        $logs = $logs ? json_decode($logs, JSON_OBJECT_AS_ARRAY) : [];
    }
    echo '<table class="table">';
    echo '<thead><tr><th>Дата и время проверки</th><th>Название паблика</th><th>Кол-во нового</th><th>Подробности</th></tr></thead><tbody>';

    foreach ($logs as $row) {
        if (isset($_REQUEST['notnull']) && $row["counter"] == 0) {
            continue;
        }
        // Генерируем уникальные id для раскрываемых блоков с подробностями
        $uid = uniqid() . mt_rand();
        echo '<tr><td width="200"><strong>' . date('Y-m-d H:i:s', $row["timestamp"]) . '</strong></td><td width="150">';
        echo ' ' . $row["public"] . ' ';
        echo '</td><td width="20"><center><span class="badge">' . $row["counter"] . '</span></center></td>';
        echo '<td>';
        if ($row["counter"] > 0) {
            echo '<button type="button" class="btn ';
            // Помечаем строку лога с ошибками
            if (strpos($row["message"], 'alert-danger') !== false) {
                echo 'btn-warning';
            } else {
                echo 'btn-info';
            }
            echo ' btn-xs" data-toggle="collapse" data-target="#' . $uid . '">Подробности обновления</button>';
            echo '<div id="' . $uid . '" class="collapse">';
            echo '<table class="table table-striped table-condensed">';
            echo $row["message"];
            echo '</table>';
            echo '</div>';
        } else {
            echo '<span class="label label-default">Обновлений нет</span>';
        }
        echo '</td></tr>';
    }
    echo '</tbody></table>';
    ?>
</div>

</body>
</html>