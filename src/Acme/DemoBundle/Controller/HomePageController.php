<?php
/**
 * Created by PhpStorm.
 * User: harx
 * Date: 6/15/2017
 * Time: 3:54 PM
 */

namespace Acme\DemoBundle\Controller;


//use Harx\WebsiteBundle\Form\Type\LanguageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//use Harx\WebsiteBundle\Form\Type\RegistrationType;
//use Harx\WebsiteBundle\Form\Model\Registration;

class HomePageController extends Controller {

	public function indexAction(Request $request){

		//$user = $this->container->get('fos_user.user_manager')->findUserByUsername('info@appsysinc.com');
		//var_dump($user);die;

		$securityContext = $this->container->get('security.context');
		if ( ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) || ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) ) {
			// authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)

				return $this->render('AcmeDemoBundle:Demo:HomePage/homepage.html.twig',array());
		}else{

			///$registration = new Registration();
			///$form = $this->createForm(new RegistrationType(), $registration, array(
			///	'action' => $this->generateUrl('account_create'),
			///));


			//return $this->render('AcmeDemoBundle:Demo:HomePage:logout_homepage.html.twig',array('form' => $form->createView()));
			return $this->render('AcmeDemoBundle:Demo:HomePage/logout_homepage.html.twig',array());

		}

	}





}