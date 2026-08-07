<?php

$pager->setSurroundCount(2);

$pageCount = $pager->getPageCount();

if ($pageCount <= 1) {
    return;
}

$currentPage = $pager->getCurrentPageNumber();
$firstItem = $pager->getPerPageStart();
$lastItem = $pager->getPerPageEnd();
$total = $pager->getTotal();
?>

<nav class="pager-shell" aria-label="Paginação">
    <div class="pager-summary">
        <?php if ($firstItem !== null && $lastItem !== null && $total !== null): ?>
            Mostrando <?= esc($firstItem) ?>-<?= esc($lastItem) ?> de <?= esc($total) ?>
        <?php else: ?>
            Página <?= esc($currentPage) ?> de <?= esc($pageCount) ?>
        <?php endif; ?>
    </div>

    <ul class="pagination mb-0">
        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <?php if ($pager->hasPreviousPage()): ?>
                <a class="page-link pager-control" href="<?= $pager->getPreviousPage() ?>" aria-label="Página anterior">
                    &lsaquo;
                </a>
            <?php else: ?>
                <span class="page-link pager-control" aria-hidden="true">&lsaquo;</span>
            <?php endif; ?>
        </li>

        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>">1</a>
            </li>
            <li class="page-item disabled">
                <span class="page-link pager-ellipsis" aria-hidden="true">...</span>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <?php if ($link['active']): ?>
                    <span class="page-link" aria-current="page"><?= esc($link['title']) ?></span>
                <?php else: ?>
                    <a class="page-link" href="<?= $link['uri'] ?>"><?= esc($link['title']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item disabled">
                <span class="page-link pager-ellipsis" aria-hidden="true">...</span>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>"><?= esc($pageCount) ?></a>
            </li>
        <?php endif; ?>

        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <?php if ($pager->hasNextPage()): ?>
                <a class="page-link pager-control" href="<?= $pager->getNextPage() ?>" aria-label="Próxima página">
                    &rsaquo;
                </a>
            <?php else: ?>
                <span class="page-link pager-control" aria-hidden="true">&rsaquo;</span>
            <?php endif; ?>
        </li>
    </ul>
</nav>
