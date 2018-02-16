<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>VK2Twitter Панель администрирования</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("div.alert-success").delay(3000).fadeOut(400);
            $("div.alert-warning").delay(5000).fadeOut(400);
        });

        function triggerAddModal(id) {
            document.getElementById('public_insert').value = id;
            $('#AddPublicModal').modal();
        }

        function triggerEditModal(twitter, name, description) {
            document.getElementById('twitter_edit').value = twitter;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-description').value = description;
            document.getElementById('public_edit').value = name;
            $('#EditPublicModal').modal();
        }
    </script>
</head>
<body>
<div class="container">
    <?php
    require_once('config.php');
    // Проверка пароля
    if (isset($_POST['passwd'])) {
        if (md5($_POST['passwd']) == $config->common->admin_pass) {
            $_SESSION['u_login'] = 'YES';
        } else {
            unset($_SESSION['u_login']);
            session_destroy();
        }
    }

    if (!isset($_SESSION['u_login']) && !empty($config->common->admin_pass)){
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
<?php
exit;
}
?>
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
            <button class="btn btn-default navbar-btn" data-toggle="modal" data-target="#SettingsModal"><span
                        class="glyphicon glyphicon-wrench"></span> Настройки
            </button>
            <a href="update_post.php">
                <button type="button" class="btn btn-default navbar-btn"><span
                            class="glyphicon glyphicon-refresh"></span> Обновить все посты
                </button>
            </a>
            <a class="btn btn-success" href="showlog.php?id=0&notnull"><span class="glyphicon glyphicon-list"></span>
                Общий журнал обновлений</a>
        </ul>
    </div><!-- /.container-fluid -->
</nav>

<?php
// Сохранение настроек
if (isset($_POST['vk_access_key'])) {
    if ($_POST['password'] !== '**********') {
        $config->common->admin_pass = md5($_POST['password']);
    }
    $config->common->vk_access_token = $_POST['vk_access_key'];
    $config->common->tw_consumer_key = $_POST['tw_consumer_key'];
    $config->common->tw_consumer_secret = $_POST['tw_consumer_secret'];
    save_config("Настройки удачно сохранены");
}
?>

<!-- Add Twitter account -->
<div class="modal fade" id="AddTwitterModal" tabindex="-1" role="dialog" aria-labelledby="AddTwitterLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal" role="form" action="admin.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="AddModalLabel">Добавить Twitter аккаунт</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="twitter_name" class="col-sm-4 control-label">Имя аккаунта
                            (http://twitter.com/...)</label>
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
<div class="modal fade" id="AddPublicModal" tabindex="-1" role="dialog" aria-labelledby="AddModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" role="form" action="admin.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
<div class="modal fade" id="EditPublicModal" tabindex="-1" role="dialog" aria-labelledby="EditModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" role="form" action="admin.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
                            <input class="form-control hidden" name="twitter_edit" id="twitter_edit">
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
<!-- Settings Modal -->
<div class="modal fade" id="SettingsModal" tabindex="-1" role="dialog" aria-labelledby="SettingsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal" role="form" action="?config" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title" id="SettingsModalLabel">Настройки</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="password" class="col-sm-4 control-label">Пароль</label>
                        <div class="col-sm-6">
                            <input name="password" id="name" type="password" class="form-control" value="**********"
                                   onfocus="this.value=''" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="locale" class="col-sm-4 control-label">VKontakte access key</label>
                        <div class="col-sm-6">
                            <input name="vk_access_key" id="vk_access_key" class="form-control"
                                   value="<?php echo $config->common->vk_access_token; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="locale" class="col-sm-4 control-label">Twitter consumer key</label>
                        <div class="col-sm-6">
                            <input name="tw_consumer_key" id="tw_consumer_key" class="form-control"
                                   value="<?php echo $config->common->tw_consumer_key; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="locale" class="col-sm-4 control-label">Twitter consumer secret</label>
                        <div class="col-sm-6">
                            <input name="tw_consumer_secret" id="tw_consumer_secret" class="form-control"
                                   value="<?php echo $config->common->tw_consumer_secret; ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span
                                class="glyphicon glyphicon-remove"></span> Закрыть
                    </button>
                    <button type="submit" class="btn btn-primary btn-confirm"><span
                                class="glyphicon glyphicon-floppy-save"></span> Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

// Добавление твиттера

if (isset($_REQUEST['twitter_name'])) {
    if (empty($_REQUEST['twitter_name']) || empty($_REQUEST['display_name'])) {
        echo '<div class="alert alert-danger" role="alert">Не указаны все необходимые данные для добавления!</div>';
    } else {
        $name = trim($_REQUEST['twitter_name']);
        $display_name = trim($_REQUEST['display_name']);
        $config->twitters->$name = json_decode('{"display_name": "' . $display_name . '", "sources": {}}');
        save_config('Новый аккаунт Твиттера успешно добавлен!');
    }
}

// Удаление твиттера

if (isset($_REQUEST['delete'])) {
    $del_id = $_REQUEST['delete'];
    foreach ($config->twitters->$del_id->sources as $source_name => $source) {
        $memcache->delete("log." . $source_name);
    }
    unset($config->twitters->$del_id);
    save_config('Аккаунт Твиттера успешно удален!');
}

// Добавление паблика

if (isset($_REQUEST['public_insert'])) {
    if (empty($_REQUEST['name'])) {
        echo '<div class="alert alert-danger" role="alert">Не указаны все необходимые данные для добавления!</div>';
    } else {
        $name = trim($_REQUEST['name']);
        $description = trim($_REQUEST['description']);
        $twitter_name = trim($_REQUEST['public_insert']);
        $config->twitters->$twitter_name->sources->$name = json_decode('{"description": "' . $description . '","last_update":0}');
        save_config('Паблик успешно добавлен!');
    }
}

// Удаление паблика

if (isset($_REQUEST['public_delete'])) {
    $public_id = $_REQUEST['public_delete'];
    $twitter_name = $_REQUEST['twitter'];
    unset($config->twitters->$twitter_name->sources->$public_id);
    $memcache->delete("log." . $public_id);
    save_config('Паблик успешно удален!');
}

// Изменение паблика

if (isset($_REQUEST['public_edit'])) {
    if (empty($_REQUEST['edit-name'])) {
        echo '<div class="alert alert-danger" role="alert">Не указано имя паблика!</div>';
    } else {
        $name = trim($_REQUEST['edit-name']);
        $description = trim($_REQUEST['edit-description']);
        $public_id = trim($_REQUEST['public_edit']);
        $twitter_name = $_REQUEST['twitter_edit'];
        $last_update = $config->twitters->$twitter_name->sources->$public_id->last_update;
        unset($config->twitters->$twitter_name->sources->$public_id);
        $config->twitters->$twitter_name->sources->$name = json_decode('{"description": "' . $description . '","last_update": ' . $last_update . '}');
        save_config('Паблик успешно изменен!');
    }
}

// Удаление лога паблика

if (isset($_REQUEST['clearlog'])) {
    $public_id = $_REQUEST['clearlog'];
    if ($public_id == '0') {
        foreach ($config->twitters as $twitter_name => $twitter) {
            foreach ($twitter->sources as $source_name => $source) {
                $memcache->delete("log." . $source_name);
            }
        }
    } else {
        $memcache->delete("log." . $public_id) ? $failed : $failed + 1;
    }
    echo '<div class="alert alert-success" role="alert">Журнал обновлений успешно удален!</div>';
}

foreach ($config->twitters as $twitter_name => $twitter) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <img src="http://g.twimg.com/Twitter_logo_blue.png" alt="Twitter" height="20" width="25">
            <a style="font-size: 1.3em;" href="http://twitter.com/<?php echo $twitter_name; ?>"
               target="_blank"><?php echo $twitter->display_name; ?></a>
            <?php
            if (empty($twitter->oauth_token) || empty($twitter->oauth_token_secret)) {
                echo '<a class="btn btn-warning btn-sm" href=get_access_token.php?id=' . $twitter_name . '><span class="glyphicon glyphicon-log-in"></span> Требуется OAuth авторизация в Твиттере</a>';
            } ?>
            <a name="DeleteTwitter" class="btn btn-danger btn-sm pull-right" href="?delete=<?php echo $twitter_name ?>"
               onClick="return confirm('Уверены что хотите это удалить?');"><span
                        class="glyphicon glyphicon-remove"></span> Удалить аккаунт</a>
        </div>
        <div class="panel-body">
            <?php
            if (empty(get_object_vars($twitter->sources))) {
                echo('<div class="alert alert-warning" role="alert">Не удалось найти список пабликов</div>');
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
                    <?php
                    $index = 1;
                    foreach ($twitter->sources as $source_name => $source) {
                        echo '<tr><td>' . $index . '</td><td><a href=https://vk.com/' . $source_name . ' target="_blank">' .
                            $source_name . '</a></td><td>' . $source->description . '</td>';
                        ?>
                        <td>
                            <a class="btn btn-success btn-xs"
                               href="showlog.php?id=<?php echo $source_name; ?>&notnull"><span
                                        class="glyphicon glyphicon-list"></span> Журнал</a>
                            <button class="btn btn-info btn-xs" data-toggle="modal"
                                    onClick="triggerEditModal('<?php echo $twitter_name ?>','<?php echo $source_name; ?>','<?php echo $source->description; ?>')">
                                <span class="glyphicon glyphicon-edit"></span> Изменить
                            </button>
                            <a class="btn btn-danger btn-xs"
                               href="?public_delete=<?php echo $source_name; ?>&twitter=<?php echo $twitter_name; ?>"
                               onClick="return confirm('Уверены что хотите это удалить?');">
                                <span class="glyphicon glyphicon-remove"></span> Удалить</a>
                        </td>
                        </tr>
                        <?php
                        $index++;
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } ?>
            <button class="btn btn-primary btn-sm" data-toggle="modal"
                    onClick="triggerAddModal('<?php echo $twitter_name ?>')">
                <span class="glyphicon glyphicon-plus"></span> Добавить паблик
            </button>

        </div>
    </div>
    <?php
}
?>
<button class="btn btn-primary" data-toggle="modal" data-target="#AddTwitterModal">
    <span class="glyphicon glyphicon-plus"></span>
    Добавить Twitter учетку
</button>
</div>

</body>
</html>