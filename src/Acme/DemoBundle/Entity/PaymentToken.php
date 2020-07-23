<?php
/**
 * Created by PhpStorm.
 * User: harx
 * Date: 7/10/2017
 * Time: 2:06 PM
 */

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class PaymentToken extends Token
{

}