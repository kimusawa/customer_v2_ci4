<?php $pager->setSurroundCount(1)  ?>

<?php
$current = 1;
$active_find = false;
$previous_URL = '';
$previous = 1;
$next_URL = '';
$next = 1;

foreach ($pager->links() as $link) {
    if ($link['active']) {
        $current = (int)$link['title'];  // ← 現在のページ番号
        if ($current > 1) {
            $next_URL = $link['uri']; // ← 前のページのURL
            $next = (int)$link['title'];
        } else {
            $previous_URL = ''; // 最初のページなので前のページはない
            $previous = 1;
            $next_URL = $link['uri']; // ← 前のページのURL
            $next = (int)$link['title'];
        }
        $active_find = true; // 現在のページが見つかった
    } else {
        if($active_find){
            $next_URL = $link['uri']; // ← 前のページのURL
            $next = (int)$link['title'];
            break;
        } else {
            $previous_URL = $link['uri']; // ← 前のページのURL
            $previous = (int)$link['title'];
            $next_URL = ''; // 次のページがない場合
            $next = $current;
        }
    }
}
?>

    <nav aria-label="Page navigation">
    <ul class="my-pagination">
        <!-- 最初へ -->
        <?php if ($pager->getFirst() !== $pager->getCurrent()): ?>
            <li><a href="<?= $pager->getFirst() ?>" aria-label="First">&lsaquo;&lsaquo;</a></li>
        <?php else: ?>
            <li class="disabled"><span>&lsaquo;&lsaquo;</span></li>
        <?php endif ?>        

        <!-- 前へ -->
        <?php if ($current > $previous): ?>
            <li><a href="<?= $previous_URL ?>" aria-label="Previous">&lsaquo;</a></li>
        <?php else: ?>
            <li class="disabled"><span>&lsaquo;</span></li>
        <?php endif ?>

        <!-- ページ番号 -->
        <?php foreach ($pager->links() as $link): ?>
            <?php if ($link['active']): ?>
                <li class="active"><span><?= $link['title'] ?></span></li>
            <?php else: ?>
                <li><a href="<?= $link['uri'] ?>"><?= $link['title'] ?></a></li>
            <?php endif ?>
        <?php endforeach ?>

        <!-- 次へ -->
        <!-- <?= '$next=' . $next ?> -->
        <!-- <?= '$current=' . $current ?> -->
        <!-- <?= '$previous=' . $previous ?> -->
        <?php if ($current < $next): ?>
            <li><a href="<?= $next_URL ?>" aria-label="Next">&rsaquo;</a></li>
        <?php else: ?>
            <li class="disabled"><span>&rsaquo;</span></li>
        <?php endif ?>
        
        <!-- 最後へ -->
        <?php if ($pager->getLast() !== $pager->getCurrent()): ?>
            <li><a href="<?= $pager->getLast() ?>" aria-label="Last">&rsaquo;&rsaquo;</a></li>
        <?php else: ?>
            <li class="disabled"><span>&rsaquo;&rsaquo;</span></li>
        <?php endif ?>
        </ul>
</nav>

