<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>视图输出，传递变量</title>
</head>
<body>
<p>
    <form action="" method="get">
    <input type="text" name="param" placeholder="请输出模版变量">
    <button type="submit">传递！</button>
    </form>
</p>
<p>输出的模版变量是: <?=$test_str?></p>
</body>
</html>