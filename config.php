<?php
// Список файлов конфигурации по приоритету
$config_files = ["gs://vk2twitter.appspot.com/config.json", "config.json"];
error_reporting(E_ERROR);
mb_internal_encoding("UTF-8");
$memcache = new Memcache;

function load_config()
{
    global $memcache, $config_files;
    // Пробуем загрузить конфигурацию из кеша
    $json = $memcache->get("config");
    if (empty($json)) {
        // Если не удалось, то пробуем загрузить конфиг из указанных файлов
        foreach ($config_files as $file) {
            $json = file_get_contents($file);
            if ($json != false) {
                break;
            }
        }
        // Если ничего не получилось то загружаем пустой конфиг
        if ($json == false) {
            echo('<div class="alert alert-danger" role="alert"><strong>Ошибка чтения конфигурации!</strong> Будет использована пустая конфигурация.</div>');
            $json = '{"twitters": {}, "common": {"admin_pass": "", "tw_consumer_secret": "", "tw_consumer_key": "", "vk_access_token": ""}}';
        }
        // Если все ОК то сохраняем конфиг в кеше
        $memcache->set("config", $json);
    }
    return json_decode($json);
}

$config = load_config();

function save_config($ok_msg)
{
    global $config_files, $config, $memcache;
    $json = json_encode($config, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
    // Сохраняем конфигурацию в одном из указанных файлов
    foreach ($config_files as $file) {
        if (file_put_contents($file, $json) != false) {
            echo '<div class="alert alert-success" role="alert">' . $ok_msg . '</div>';
            // В случаи удачи кешируем конфигурацию
            $memcache->set("config", $json);
            break;
        } else {
            die('<div class="alert alert-danger" role="alert">Не удалось сохранить изменения!</div>');
        }
    }
}

?>