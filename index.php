<!DOCTYPE html>
<html>
<head>
    <title>Vkontakte to Twitter</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="RSS, Atom, feed, full, full text, full content, full article">
    <link href="favicon.ico" rel="icon" type="image/x-icon"/>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>Vkontakte <span class=" glyphicon glyphicon-arrow-right"></span> Twitter
            <small> by Nomadic</small>
        </h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"> Доступные трансляции:
            </h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-hover col-md-4" style="font-size:15px;">
                <thead>
                <tr>
                    <th>Имя паблика Vkontakte</th>
                    <th>Twitter аккаунт</th>
                </tr>
                </thead>
                <tbody>
                <?php
                require_once('config.php');
                foreach ($config->twitters as $twitter_name => $twitter) {
                    foreach ($twitter->sources as $source_name => $source) {
                        echo('<tr><td><img src="https://vk.com/favicon.ico"> <a href="https://vk.com/' . $source_name . '" target="_blank">' .
                            $source->description . '</a></td><td><img src="http://g.twimg.com/Twitter_logo_blue.png" alt="Twitter" width="16" height="13">
                             <a href="https://twitter.com/' . $twitter_name . '" target="_blank">' . $twitter->display_name . '</a></td></tr>' . PHP_EOL);
                    }
                }
                ?>
                </tbody>
            </table>
        </div> <!-- panel-body -->
    </div> <!-- panel -->
    <footer class="navbar-fixed-bottom">
        <div style="text-align: center;"><p><a href="https://github.com/n0madic/vk2twitter">GitHub</a> &copy; Nomadic
                2014-2017</p></div>
    </footer>
</div> <!-- container -->
</body>
</html>