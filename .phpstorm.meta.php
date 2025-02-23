<?php
/*
 * This file is part of Aplus Framework Pagination Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPSTORM_META;

registerArgumentsSet(
    'views',
    'head',
    'header',
    'pager',
    'pagination',
    'pagination-short',
    'bootstrap',
    'bootstrap-short',
    'bootstrap4',
    'bootstrap4-short',
    'bootstrap5',
    'bootstrap5-short',
    'bulma',
    'bulma-short',
    'foundation',
    'foundation-short',
    'foundation6',
    'foundation6-short',
    'semantic-ui',
    'semantic-ui-short',
    'semantic-ui2',
    'semantic-ui2-short',
    'tailwind',
    'tailwind-short',
    'tailwind2',
    'tailwind2-short',
);
expectedArguments(
    \Framework\Pagination\Pager::getView(),
    0,
    argumentsSet('views')
);
expectedArguments(
    \Framework\Pagination\Pager::render(),
    0,
    argumentsSet('views')
);
expectedArguments(
    \Framework\Pagination\Pager::setDefaultView(),
    0,
    argumentsSet('views')
);
expectedArguments(
    \Framework\Pagination\Pager::setView(),
    0,
    argumentsSet('views')
);
