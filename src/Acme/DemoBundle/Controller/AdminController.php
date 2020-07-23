<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Acme\DemoBundle\Form\ContactType;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Entity\User;
use Acme\DemoBundle\Entity\Admin;

class AdminController extends Controller {

    /**
     * @Route("/", name="_admpanel")
     * @Template()
     */
    public function indexAction() {
        //echo '<pre>';print_r($_POST);die;
        $userName = $_POST['username'];
        $password = $_POST['password'];
        return array();
    }

    /**
     * @Route("/hello/{name}", name="_admpanel_hello")
     * @Template()
     */
    public function helloAction($name) {
        echo '<pre>';
        print_r($_POST);
        die;
        $val = array('name' => $name);
        return $val;
    }

    /**
     * @Route("/contact", name="_admpanel_contact")
     * @Template()
     */
    public function contactAction() {
        $form = $this->get('form.factory')->create(new ContactType());

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $mailer = $this->get('mailer');
                // .. setup a message and send it
                // http://symfony.com/doc/current/cookbook/email.html

                $this->get('session')->getFlashBag()->set('notice', 'Message sent!');

                return new RedirectResponse($this->generateUrl('_demo'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/login", name="_admpanel_login")
     * @Template()
     */
    public function loginAction() {
      
        //$flash_message = 'login';
        if (isset($_POST['username']) && isset($_POST['password'])) {
           
            $AdminEmail = $_POST['username'];

            $AdminPassword = $_POST['password'];
            $Admin = $this->getDoctrine()->getRepository("AcmeDemoBundle:Admin")->findOneBy(['adminEmail' => $AdminEmail, 'adminPassword' => $AdminPassword]);
          // echo '<pre>';print_r($Admin);die;
            if ($Admin != '' && $Admin != null) {
               return $this->render('_demo_admin');
                // $this->get('session')->getFlashBag()->set('notice', 'Message sent!');
            } else {

                return $this->render('AcmeDemoBundle:Demo:layout.html.twig');
            }
        } else {
            return $this->render('AcmeDemoBundle:Demo:layout.html.twig');
        }
    }
    /**
     * @Route("/dashboard", name="_dashboard")
     * @Template()
     */
      public function dashboardAction() {
          die('ok');
      }
      

}
