<?php
/**
 * Created by PhpStorm.
 * User: K1ne
 * Date: 11/16/2014
 * Time: 6:29 PM
 */

namespace Acme\DemoBundle\DependencyInjection;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Acme\DemoBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager {

    protected $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function getEncoder(User $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }
/*
    public function updateUser(User $user)
    {
        $plainPassword = $user->getPassword();

        if (!empty($plainPassword)) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($plainPassword, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
*/
/* this was part of the password encoder code but not needed??
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $user = $event->getEntity();

        if (!($user instanceof \Harx\WebsiteBundle\Entity\User)) {
            return;
        }

        $this->updateUser($user);
        $event->setNewValue('password', $user->getPassword());
        //die($event->getOldValue('password') . ' ' . $event->getNewValue('password') . ' ' . $event->hasChangedField('password') ? 'Y' : 'N');
    }
*/

    public function prePersist(LifecycleEventArgs $event)
    {
        $user = $event->getEntity();

        if (!($user instanceof \Acme\DemoBundle\Entity\User)) {
            return;
        }

        //$this->updateUser($user);
    }


}