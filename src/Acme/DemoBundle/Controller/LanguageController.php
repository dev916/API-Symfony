<?php
/**
 * Created by PhpStorm.
 * User: harx
 * Date: 4/23/2015
 * Time: 12:26 PM
 */

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class LanguageController extends Controller {

    public function chooseLanguageAction(Request $request)
    {

        $locale = $request->request->get('langPick');
        $this->get('session')->set('_locale', $locale);

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }

}

