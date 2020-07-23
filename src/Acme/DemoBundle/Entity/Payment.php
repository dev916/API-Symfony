<?php
/**
 * Created by PhpStorm.
 * User: harx
 * Date: 7/10/2017
 * Time: 2:08 PM
 */

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Payment extends BasePayment
{
	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 *
	 * @var integer $id
	 */
	protected $id;

}