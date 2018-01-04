<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2018 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Sitemap;

use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Sitemap\AbstractSitemapProvider;
use Mindy\Sitemap\Entity\LocationEntity;

class PageSitemapProvider extends AbstractSitemapProvider
{
    /**
     * {@inheritdoc}
     */
    public function build($hostWithScheme)
    {
        foreach (Page::objects()->asArray()->batch(100) as $chunk) {
            foreach ($chunk as $object) {
                yield (new LocationEntity())
                    ->setLastmod(new \DateTime($object['updated_at']))
                    ->setLocation($this->generateLoc($hostWithScheme, 'page_view', [
                        'url' => $object['url'],
                    ]));
            }
        }
    }
}
