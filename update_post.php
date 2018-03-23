<!DOCTYPE html>
<html>
<head>
    <title>VK2Twitter Панель администрирования</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- <meta http-equiv="refresh" content="300"> -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
</head>
<body>
<nav class="navbar navbar-default navbar-static-top" role="navigation">
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
<div class="container">

    <?php
    error_reporting(E_ERROR);
    mb_internal_encoding("UTF-8");
    require_once('tmhOAuth.php');
    require_once('config.php');
    $updated = false;

    foreach ($config->twitters as $twitter_name => $twitter) {
        $tmhOAuth = new tmhOAuth(array(
            'consumer_key' => $config->common->tw_consumer_key,
            'consumer_secret' => $config->common->tw_consumer_secret,
            'user_token' => $twitter->oauth_token,
            'user_secret' => $twitter->oauth_token_secret,
        ));
        $tmhOAuth->config['curl_timeout'] = 30;

        foreach ($twitter->sources as $source_name => $source) {
            $counter = 0;
            $logtext = "";
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div>Паблик <img src="//vk.com/favicon.ico"/>
                        <a href="http://vk.com/<?php echo $source_name . '">' . $source->description; ?></a>
                        <span class=" glyphicon glyphicon-arrow-right"></span>
                        <img src="//twitter.com/favicon.ico"/>
                        <a href="http://twitter.com/<?php echo $twitter_name; ?>"
                           target="_blank"><?php echo $twitter->display_name; ?></a>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <?php

                        $wall = file_get_contents("https://api.vk.com/method/wall.get?v=5&photo_sizes=1&domain=" . $source_name . "&access_token=" . $config->common->vk_access_token);
                        if ($wall != false) {
                            $wall = json_decode($wall); // Преобразуем JSON-строку в массив
                            if (isset($wall->error)) {
                                $logtext = '<div class="alert alert-danger" role="alert"><span style="font-size: 1.5em;" class="glyphicon glyphicon-warning-sign"></span> Ошибка API! code: '.$wall->error->error_code. '<br>'
                                 . $wall->error->error_msg . '</div>';
                            } else {
                                $wall = $wall->response->items; // Получаем массив постов
                                for ($i = count($wall); $i > 0; $i--) {
                                    if ($wall[$i]->date > $source->last_update && $wall[$i]->marked_as_ads == 0) {
                                        $counter++;
                                        $status = trim($wall[$i]->text);
                                        // Запоминаем дату последней новости
                                        $config->twitters->$twitter_name->sources->$source_name->last_update = $wall[$i]->date;
                                        if (isset($wall[$i]->copy_history[0])) {
                                            $status = $wall[$i]->copy_history[0]->text . ' ' . $wall[$i]->text;
                                            $attachments = $wall[$i]->copy_history[0]->attachments;
                                        } else {
                                            $attachments = $wall[$i]->attachments;
                                        };
                                        $attach_type = $attachments[0]->type;
                                        if ($attach_type == 'video') {
                                            $status = $wall[$i]->description;
                                            $image = $attachments[0]->video->photo_320;
                                        };
                                        if ($attach_type == 'photo') {
                                            // Проходимся по списку изображений выбирая ссылку с максимальным разрешением
                                            $max_resolution = 0;
                                            unset($image);
                                            foreach ($attachments[0]->photo->sizes as $size) {
                                                if ($size->width > $max_resolution) {
                                                    $max_resolution = $size->width;
                                                    $image = $size->src;
                                                }
                                            }
                                        };
                                        if ($attach_type == 'doc' && $attachments[0]->doc->ext == 'gif') {
                                            $image = $attachments[0]->doc->url;
                                        };
                                        $status = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $status)); // Заменяем переводы строк
                                        $status = preg_replace("/\[(club|id)\d+\|(.+)]/U", "$2", $status); // Удаляем метатеги
                                        $status = preg_replace('/\s+/', ' ', $status); // Удаляем повторяющиеся пробелы
                                        // Определение более точной длины будущего твита
                                        $short_url_length = 23;
                                        $url_regex = "/(?:((?:[^-\/" . '"' . "':!=a-z0-9_@＠]|^|\:))(((?:https?:\/\/|www\.)?)((?:[^\p{P}\p{Lo}\s][\.-](?=[^\p{P}\p{Lo}\s])|[^\p{P}\p{Lo}\s])+\.[a-z]{2,}(?::[0-9]+)?)(\/(?:(?:\([a-z0-9!\*';:=\+\$\/%#\[\]\-_,~]+\))|@[a-z0-9!\*';:=\+\$\/%#\[\]\-_,~]+\/|[\.\,]?(?:[a-z0-9!\*';:=\+\$\/%#\[\]\-_~]|,(?!\s)))*[a-z0-9=#\/]?)?(\?[a-z0-9!\*'\(\);:&=\+\$\/%#\[\]\-_\.,~]*[a-z0-9_&=#\/])?))/iux";
                                        // Определяем максимальную длину обычного текста
                                        $totalchars = 140 - $short_url_length - 2; // Отнимаем от максимально возможной длины твита длину сокращенного url и "… "
                                        if (isset($image)) $totalchars -= $short_url_length; // Отнимаем длину ссылки на картинку если она есть
                                        // Прикидываем реальный размер твита, попутно его обрезая до максимально возможного
                                        $words = preg_split('/\s+/', $status);
                                        $status_truncated = '';
                                        $tweetLength = 0;
                                        foreach ($words as $word) {
                                            // Если слово ссылка то добавляем длину короткой ссылки, иначе длину слова
                                            if (preg_match($url_regex, $word)) {
                                                $tweetLength += $short_url_length;
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
                                        if (isset($image)) $tweetLength += $short_url_length;
                                        // Если твит получается слишком длинным или есть продолжение то усекаем его
                                        if (count($attachments) > 1 || $tweetLength > 140 || $attach_type == 'video' || $attach_type == 'poll') {
                                            $status = $status_truncated . "… https://vk.com/wall" . $wall[$i]->from_id . "_" . $wall[$i]->id;
                                        }
                                        $logtext = $logtext . "<tr><td>Lenght: " . mb_strlen($status) . "<br>  <i>" . $status . "</i><br />";
                                        // Постим в Твиттер если не localhost
                                        If (isset($image)) {
                                            $logtext = $logtext . "<img src=" . $image . "><br />";
                                            if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
                                                $image = file_get_contents($image, NULL, NULL, 0, 204800);
                                                $response = $tmhOAuth->request('POST', 'https://api.twitter.com/1.1/statuses/update_with_media.json',
                                                    array(
                                                        'media[]' => $image,
                                                        'status' => $status
                                                    ),
                                                    true, // use auth
                                                    true  // multipart
                                                );
                                            } else {
                                                $response = 200;
                                            }
                                            unset($image);
                                        } else {
                                            if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
                                                $response = $tmhOAuth->request('POST', 'https://api.twitter.com/1.1/statuses/update.json',
                                                    array(
                                                        'status' => $status
                                                    ),
                                                    true // use auth
                                                );
                                            } else {
                                                $response = 200;
                                            }
                                        }
                                        if ($response <> 200) {
                                            $error = json_decode($tmhOAuth->response['response']);
                                            $logtext = $logtext . '<div class="alert alert-danger" role="alert">Ошибка размещения статуса в Twitter: ' . $error->errors[0]->message . '</div>';
                                        };
                                        $logtext = $logtext . '</td></tr>';
                                        $updated = true;
                                    }
                                }
                            }
                        } else {
                            $logtext = '<div class="alert alert-danger" role="alert"><span style="font-size: 1.5em;" class="glyphicon glyphicon-warning-sign"></span> Ошибка загрузки паблика!</div>';
                        }
                        $current_log = $memcache->get("log." . $source_name);
                        if (empty($current_log)) {
                            $current_log = [];
                        } else {
                            $current_log = json_decode($current_log, JSON_OBJECT_AS_ARRAY);
                        }
                        array_push($current_log, ["timestamp" => time(), "public" => $source->description, "counter" => $counter, "message" => $logtext]);
                        asort($current_log);
                        $memcache->set("log." . $source_name, json_encode(array_slice($current_log, -50)), MEMCACHE_COMPRESSED, 86400);
                        echo $logtext;
                        ?>
                    </table>
                    <?php echo 'Всего новых статей: <span class="badge">' . $counter . '</span>'; ?>
                </div>
            </div>
            <?php
        }
    }
    // Сохраняем конфиг в случаи удачного размещения поста
    if ($updated) save_config("Последние обновления  сохранены");
    ?>
</div>

</body>
</html>
