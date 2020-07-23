<?php
/**
 * Created by PhpStorm.
 * User: K1ne
 * Date: 11/4/2014
 * Time: 11:14 AM
 */

namespace Acme\DemoBundle\Form\Type;


    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	use Symfony\Component\Form\Extension\Core\Type\TextType;
	use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
			->add('userFirstName',TextType::class, array('label' => 'form.first_name','translation_domain' => 'forms','required' => false))
			->add('userLastName',TextType::class, array('label' => 'form.last_name','translation_domain' => 'forms','required' => false))
			->add('userAddress',TextareaType::class, array('label' => 'form.address','translation_domain' => 'forms','required' => false))
			->add('userMobileNo',TextType::class, array('label' => 'form.mphone','translation_domain' => 'forms','required' => false))
			->add('companyName',TextType::class, array('label' => 'form.company_name','translation_domain' => 'forms','required' => false))
			->add('userWebsite',TextType::class, array('label' => 'form.company_web','translation_domain' => 'forms','required' => false))
			->add('userBIO',TextareaType::class, array('label' => 'form.desc','translation_domain' => 'forms','required' => false))
			->add('Update', 'submit', array('label' => 'form.update','translation_domain' => 'forms'))
		;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Acme\DemoBundle\Entity\User'
            ));
    }

    public function getName()
    {
        return 'user';
    }

}