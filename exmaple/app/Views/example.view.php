<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Col</title>
</head>
<body>
<ul>
    <?php foreach ($urls as $url => $title): ?>
        <li><a href="<?=$url?>"><?=$title?></a></li>
    <?php endforeach; ?>
</ul>
</body>
</html>
