<?php
/*
 * This file is part of Aplus Framework Pagination Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * @var Framework\Pagination\Pager $pager
 */
?>
<ul class="pagination">
    <?php if ($pager->getPreviousPage() > 0) : ?>
        <li>
            <a rel="prev" href="<?= $pager->getPreviousPageUrl() ?>" title="<?= $pager->getLanguage()
        ->render('pagination', 'previous') ?>">
                &laquo; <?= $pager->getLanguage()->render('pagination', 'previous') ?>
            </a>
        </li>
    <?php endif ?>

    <?php if ($pager->getNextPage()) : ?>
        <li>
            <a rel="next" href="<?= $pager->getNextPageUrl() ?>" title="<?= $pager->getLanguage()
        ->render('pagination', 'next') ?>">
                <?= $pager->getLanguage()->render('pagination', 'next') ?> &raquo; </a>
        </li>
    <?php endif ?>
</ul>
