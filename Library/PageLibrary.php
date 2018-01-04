<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2018 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Library;

use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Template\Library\AbstractLibrary;

class PageLibrary extends AbstractLibrary
{
    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'get_page' => function ($id) {
                return Page::objects()->get(['id' => $id]);
            },
            'get_page_children' => function ($id, $limit = 10) {
                return Page::objects()
                    ->filter(['parent_id' => $id])
                    ->limit($limit)
                    ->order(['-published_at']);
            },
        ];
    }
}
