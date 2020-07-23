<?php
/**
 * Created by PhpStorm.
 * User: harx
 * Date: 7/14/2017
 * Time: 10:54 AM
 */

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileController extends Controller
{

	public function profileupdateAction(Request $request)
	{

		$em = $this->getDoctrine()->getEntityManager();
		$editprofile = $em->getRepository('AcmeDemoBundle:User')->find($user = $this->get('security.context')->getToken()->getUser()->getId());

		$form = $this->createForm(new UserType(), $editprofile);


		/*$form = $this -> createFormBuilder($user)
			->add('userLastName', TextType::class)
			->add('save', SubmitType::class, array('label' => 'Update Profile'))
			->getForm();*/

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			// $form->getData() holds the submitted values
			// but, the original `$task` variable has also been updated
			$user = $form->getData();

			// ... perform some action, such as saving the task to the database
			// for example, if Task is a Doctrine entity, save it!
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			return $this->render('AcmeDemoBundle:Demo:HomePage/homepage.html.twig', array('form'=> $form->createView(),));

		}

		return $this->render('AcmeDemoBundle:Demo:ProfilePage/update_profile.html.twig', array('form'=> $form->createView(),));
	}



}