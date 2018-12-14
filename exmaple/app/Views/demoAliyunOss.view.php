<ul>
    <?php foreach ($list as $item): ?>
      <li><?=$item['size']?>
          | <?=$item['type']?>
          | <a href="<?=$item['url']?>"><?=$item['name']?></a>
          | <?=$item['time']?>
      </li>
    <?php endforeach; ?>
</ul>
