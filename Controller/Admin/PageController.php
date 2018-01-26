<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Controller\Admin;

use Mindy\Bundle\MindyBundle\Controller\Controller;
use Mindy\Bundle\PageBundle\Form\PageForm;
use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Bundle\SeoBundle\EventListener\SeoEvent;
use Mindy\Bundle\SeoBundle\EventListener\SeoRemoveEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function listAction(Request $request)
    {
        $pages = Page::objects()->order(['root', 'lft'])->all();

        return $this->render('admin/page/page/list.html', [
            'pages' => $pages,
        ]);
    }

    public function createAction(Request $request)
    {
        $page = new Page();

        $form = $this->createForm(PageForm::class, $page, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_page_page_create'),
        ]);

        if ($form->handleRequest($request) && $form->isValid()) {
            if (false === $page->save()) {
                throw new \RuntimeException('Error while save menu');
            }

            $this->get('event_dispatcher')->dispatch(
                SeoEvent::EVENT_NAME,
                new SeoEvent($page)
            );

            $this->addFlash('success', 'Страница успешно сохранена');

            return $this->redirectToRoute('admin_page_page_list');
        }

        return $this->render('admin/page/page/create.html', [
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, $id)
    {
        $page = Page::objects()->get(['id' => $id]);
        if (null === $page) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(PageForm::class, $page, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_page_page_update', ['id' => $id]),
        ]);

        if ($form->handleRequest($request) && $form->isValid()) {
            $page = $form->getData();
            if (false === $page->save()) {
                throw new \RuntimeException('Error while save menu');
            }

            $this->get('event_dispatcher')->dispatch(
                SeoEvent::EVENT_NAME,
                new SeoEvent($page)
            );

            $this->addFlash('success', 'Страница успешно сохранена');

            return $this->redirectToRoute('admin_page_page_list');
        }

        return $this->render('admin/page/page/update.html', [
            'form' => $form->createView(),
            'page' => $page,
        ]);
    }

    public function removeAction(Request $request, $id)
    {
        $page = Page::objects()->get(['id' => $id]);
        if (null === $page) {
            throw new NotFoundHttpException();
        }

        $page->delete();

        $this->get('event_dispatcher')->dispatch(
            SeoRemoveEvent::EVENT_NAME,
            new SeoRemoveEvent($page)
        );

        $this->addFlash('success', 'Страница успешно удалена');

        return $this->redirectToRoute('admin_page_page_list');
    }
}
