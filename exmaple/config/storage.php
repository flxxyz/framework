<?php

return [
    'driver' => 'local',
    'local' => [
        'dateFormat' => 'Y-m-d H:i:s',  // 日期格式
        'rootPath' => '/',
        'dataPath' => 'data',  //默认网站根目录下的data文件夹
        'ignore' => [  // 忽略的文件或文件夹列表（.与..由程序自动忽略）
                       '.htaccess',
                       'Thumbs.db',
                       '.DS_Store',
                       '.user.ini',
                       '.gitignore',
                       'index.php',
                       'robots.txt',
                       '.babelrc',
                       '.idea',
                       '.git',
                       '$RECYCLE.BIN',
                       '.Spotlight-V100',
                       '.Trashes',
                       '.fseventsd',
                       'System Volume Information',
        ],
    ],
];
