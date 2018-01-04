<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2018 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\TemplateLoader;

use Mindy\Template\Finder\ChainFinder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class PageTemplateLoader implements PageTemplateLoaderInterface
{
    /**
     * @var ChainFinder
     */
    protected $finder;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * PageTemplateLoader constructor.
     *
     * @param KernelInterface $kernel
     * @param ChainFinder     $chainFinder
     */
    public function __construct(KernelInterface $kernel, ChainFinder $chainFinder)
    {
        $this->basePath = $kernel->getRootDir();
        $this->finder = $chainFinder;
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected function formatPath($path): string
    {
        return sprintf('%s/page/templates', $path);
    }

    /**
     * @return array
     */
    protected function fetchCorrectPaths(): array
    {
        return array_filter($this->finder->getPaths(), function ($path) {
            return is_dir($this->formatPath($path));
        });
    }

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $templates = [];
        foreach ($this->fetchCorrectPaths() as $path) {
            $finder = (new Finder())
                ->ignoreUnreadableDirs()
                ->files()
                ->in($this->formatPath($path))
                ->name('*.html');

            foreach ($finder as $template) {
                /* @var $template \SplFileInfo */
                $key = substr($template->getRealPath(), strlen($this->basePath) + 1);

                $templates[$key] = $template->getBasename();
            }
        }

        return $templates;
    }
}
