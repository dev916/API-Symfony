<?php

namespace Acme\DemoBundle\Controller;



use Acme\DemoBundle\Entity\CalendarEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Acme\DemoBundle\Form\ContactType;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Entity\User;
use Symfony\Bridge\Swiftmailer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Acme\DemoBundle\Entity\UserServices;
use Doctrine\ORM\QueryBuilder;
use Acme\DemoBundle\Entity\UserFollow;
use Acme\DemoBundle\Entity\AlbumPost;
use Acme\DemoBundle\Entity\PostTags;
use Acme\DemoBundle\Entity\PostCategory;
use Acme\DemoBundle\Entity\UserCustomerRelation;
use Doctrine\ORM\Mapping\OrderBy;
use Acme\DemoBundle\Entity\UserRating;
use Doctrine\ORM\Mapping as ORM;
use Acme\DemoBundle\Entity\ReportProblem;
use Acme\DemoBundle\Entity\Notification;
use Doctrine\ORM\EntityManager;
use Acme\DemoBundle\Entity\NotificationMessage;
use Acme\DemoBundle\Entity\UserChat;
use Acme\DemoBundle\Entity\System;
use Acme\DemoBundle\Entity\Domain;
use Acme\DemoBundle\GlobalUrls;


class ApiController extends Controller
{
	/**no longer used - abstracted into global url class
	protected $_apiUrlDior = "http://webdior.co.in/apihaircut/Symfony/";
	//protected $_apiUrlLocal = "http://ws.herecut.net/";
	protected $_apiUrlLocal = "http://192.168.68.47/";
	protected $_apiUrlAws = "https://ws.herecut.com/";
	protected $_apiUrl = "";
	*/

	function __construct()
	{
		$GlobalUrls = new GlobalUrls();
		//$this->_apiUrl = $this->_apiUrlLocal;
		$this->_apiUrl = $GlobalUrls->apiUrlLocal;
	}

	/**
	 * @Route("/", name="_webservice")
	 * @Template()
	 */
	public function indexAction()
	{
		echo "HereCut";

		return array();
	}

//    function baseurl() {
//        $base_url = 'http://{{app.request.host}}/apihaircut/Symfony';
//        return array();
//    }

	function baseurl()
	{
		$url = $this->_apiUrl . "uploads/";

		//$ext = $this->getRequest()->getSchemeAndHttpHost();
		return $url;
	}

//for trim
	function ClearText($str)
	{
		$clean = trim($str, "\n");

		$clean = trim($str, " ");

		$clean = utf8_encode($str);

		$clean = utf8_decode($str);

		$clean = htmlentities($str);

		$clean = htmlspecialchars($str);

		return $clean;
	}

//image upload start
	function UploadFile($filename, $String)
	{
		//TODO add userid to front of image
		if (($String)) {
			$path = $filename;
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$filename = rand(111, 999) . time() . '.' . $ext;
			$imageFile = 'uploads/' . $filename;
			$downloadableLink = $imageFile;
			$imgString = base64_decode($String);
			file_put_contents($imageFile, $imgString);

			return $filename;
		} else {
			return "0";
		}
	}

//image upload end 

	function sendElasticEmail($to, $subject, $body_text, $body_html, $from, $fromName)
	{
		$res = "";

		$data = "username=" . urlencode("natashaoberoi213@gmail.com");
		$data .= "&api_key=" . urlencode("7a1db5f1-e38a-4b01-b51b-d3b625eb7167");
		$data .= "&from=" . urlencode($from);
		$data .= "&from_name=" . urlencode($fromName);
		$data .= "&to=" . urlencode($to);
		$data .= "&subject=" . urlencode($subject);
		if ($body_html) {
			$data .= "&body_html=" . urlencode($body_html);
		}
		if ($body_text) {
			$data .= "&body_text=" . urlencode($body_text);
		}

		$header = "POST /mailer/send HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
		$fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);

		if (!$fp) {
			return "ERROR. Could not open connection";
		} else {

			fputs($fp, $header . $data);

			while (!feof($fp)) {

				$res .= fread($fp, 1024);
				//  print_r($res);  die('dfgjdfkjgb');
			}
			fclose($fp);

		}

		// echo $res;
		return $res;
	}


	//////////ELASTIC MAIL ///////////////////////////////// 


	function smtpEmail($toid, $subject, $message)
	{
		//committed out of local
		$contents = $this->_apiUrl . '/mailservice.php?toid=' . $toid . '&subject=' . urlencode(
				$subject
			) . '&message=' . urlencode($message) . '';


		$user = file_get_contents($contents);

	}


	// Notification 
	public function chat_notification(
		$registatoin_ids,
		$msg,
		$chat_message,
		$msg_id,
		$chat_timestamp,
		$chat_delivery_status,
		$user_chat_type,
		$chat_user_id,
		$chat_from_id,
		$chat_from_name
	) {

		$url = 'https://fcm.googleapis.com/fcm/send';
		//$apiKey = 'AIzaSyAH_GcryTpZxZDM-XZ7eK9ZjUqGxxQhd4Y';
		$apiKey = 'AAAA1VLL9K4:APA91bHyloI0VuM3ZW3rKwoivW0HLunYa6rt0bx0a8WuBDvsHYJyK7lSpAN3H5UHFgFWl6MuLkudZ99gZ3PZJnLnYjCTmNoxTGKcciaDwN7oJHUd3hmACoBDv4bEqsNumIxsNqpUM2Xz';
		$id = array();
		$id[] = $registatoin_ids;
		$msgdata = array
		(
			'message' => $msg,
			'chat_message' => $chat_message,
			'chat_timestamp' => $chat_timestamp,
			'chat_delivery_status' => $chat_delivery_status,
			'user_chat_type' => $user_chat_type,
			'chat_user_id' => $chat_user_id,
			'chat_from_name' => $chat_from_name,
			'chat_from_id' => $chat_from_id,
			'msg_id' => $msg_id,
		);

		$notification = array
		(
			'title' => $msg,
			'text' => $chat_message,
			'sound' => 'default'
		);

		$fields = array
		(
			'registration_ids' => $id,
			'data' => $msgdata,
			'priority' => 'high',
			'notification' => $notification
		);
		
		$headers = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);
		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		// Execute post
		$result = curl_exec($ch);
		echo '<pre>';print_r($result);
		if ($result === false) {
			 die('ok1');
			die('Curl failed: ' . curl_error($ch));
		} else {

			return true;
		}
// Close connection
		curl_close($ch);
		echo $result;
		die;
	}

	public function send_notification($registatoin_ids, $msg, $IDs, $submsg)
	{

		$url = 'https://fcm.googleapis.com/fcm/send';
		//$apiKey = 'AIzaSyAH_GcryTpZxZDM-XZ7eK9ZjUqGxxQhd4Y';
		$apiKey = 'AAAA1VLL9K4:APA91bHyloI0VuM3ZW3rKwoivW0HLunYa6rt0bx0a8WuBDvsHYJyK7lSpAN3H5UHFgFWl6MuLkudZ99gZ3PZJnLnYjCTmNoxTGKcciaDwN7oJHUd3hmACoBDv4bEqsNumIxsNqpUM2Xz';
		$id = array();
		$id[] = $registatoin_ids;
//        $message = array('message' => $msg);
//        $subMsg = array('submessage' => $submsg);
//       // $ID =array('userID' => $IDs);
//        $fields = array(
//            'registration_ids' => $id,
//            'message' => $message,
//             'submessage' => $subMsg,
//            'ID' => $IDs,
//            
//        );

		$msgdata = array
		(
			'message' => $msg,
			'submessage' => $submsg,
			'id' => $IDs
		);
	
		$notification = array
		(
			'title' => $msg,
			'text' => $submsg,
			'sound' => 'default'
		);

		$fields = array
		(
			'registration_ids' => $id,
			'data' => $msgdata,
			'priority' => 'high',
			'notification' => $notification
		);

		$headers = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);
		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		// Execute post
		$result = curl_exec($ch);
		// print_r($result);
		// print_r($fields);
		if ($result === false) {
			// die('ok1');
			die('Curl failed: ' . curl_error($ch));
		} else {

			return true;
		}
// Close connection
		curl_close($ch);
		echo $result;
		die;
	}


//notification function end


	public function distance($lat1, $lon1, $lat2, $lon2)
	{

		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(
				deg2rad($theta)
			);
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
// $unit = strtoupper($unit);
// if ($unit == "K") {
//return ($miles * 1.609344);
// } else if ($unit == "N") {
//   return ($miles * 0.8684);
//  } else {
		return $miles;
// }
//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
//  echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
//  echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
	}

	/**
	 * @Route("/login", name="_login")
	 * @Template()
	 */
	/*     * *************************************************Login Begin************************************************* */
	public function loginAction(
		Request $Email,
		Request $userPassword,
		Request $device_id,
		Request $device_type,
		Request $imei
	) {
		$logger = $this->get('logger');
		$request = $this->getRequest();

		$userEmail = $this->ClearText($request->get('userEmail'));
		$stmt_pre = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['userEmail' => $userEmail]);

		if ($stmt_pre !== null) {
			$loginPassword = $this->ClearText($request->get('userPassword'));
			$password = $stmt_pre->getUserPassword();

			if (password_verify($loginPassword, $password)) {
				$logger->info('LoginPass: Valid password.');
				$stmt = $stmt_pre;
			} else {
				$logger->info('LoginPass: Invalid password.');
				$stmt = "";
			}

		}else{
			$logger->info('LoginUser: Invalid Username.');
			$stmt = "";

		}

		//$stmt = $this->getDoctrine()->getRepository("AcmeDemoBundle:User");
		//$stmt->findOneBy(
		//	['userEmail' => $userEmail, 'userPassword' => $loginPassword2]
		//);
		if ($stmt != '' && $stmt != null) {

			if (($stmt->getUserFirstName() || $stmt->getUserLastName()) != '') {
				$user_name = $stmt->getUserFirstName() . ' ' . $stmt->getUserLastName();

			} else {
				$user_name = '';

			}
			$userpassword = $stmt->getUserPassword();
			$userName = ucwords($user_name);
			$useremail = $userEmail;
			$password = $userpassword;
			$subject = 'HereCut WelCome Email';
//                $body_text = 'Welcome email from HereCut Team';
			//   $body_html = 'Hello '.$userName.',<br><br> Welcome to HereCut <br><br>Your login email is : '.$useremail.'<br>and Password : '.$password.'<br><br><br>Thanks <br>HereCut Team';
//               // $from = 'ankur@webdior.com';
//                $fromName = 'HereCut';
//$to = $useremail;
//$subject= 'HereCut WelCome Email';	
//$from= 'ankur@webdior.com';
//$headers = "From: " . $from . "\r\n"; 
//$headers .= "Reply-To: ". $from . "\r\n"; 
////$headers .= "CC: test@example.com\r\n"; 
//$headers .= "MIME-Version: 1.0\r\n"; 
//$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
// mail($to, $subject, $body_html, $headers); 
//                
//                
//                
//              //  mail($useremail, $body_html, $from);
//                 $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);

			if ($stmt->getUserType() == 1) {


				if ($stmt->getCompanyName() != '') {
					$company_name = $stmt->getCompanyName();
					$user_name = '';
				} else {
					$company_name = '';
					$user_name = '';
				}


			} else {

				if (($stmt->getUserFirstName() || $stmt->getUserLastName()) != '') {
					$user_name = $stmt->getUserFirstName() . ' ' . $stmt->getUserLastName();
					$company_name = '';
				} else {
					$user_name = '';
					$company_name = '';
				}
				// $postStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID'=>$stmt->getId(),'postStatus' => 1]);
				$flag = '';
				$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['userID' => $stmt->getId()],
					array('id' => 'desc')
				);

				if ($Consumer != '' && $Consumer != null) {
					foreach ($Consumer as $Consumer1val) {
						if ($Consumer1val->getUserID() != $Consumer1val->getUserTagID()) {


							$ConsumerRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $Consumer1val->getId()]
							);

							if (!empty($ConsumerRate)) {


								$flag = 1;
							} else {
								$flag = 2;

							}
						}
					}


				}
				if ($flag == 2) {
					// die('ok');
					$userName = ucwords($user_name);
					$useremail = $stmt->getUserEmail();

					$subject = 'You have pending SPPS for Rating/Review';
					$body_text = 'Welcome email from HereCut Team';
					$body_html = 'Hello ' . $userName . ',<br><br> You have Service Provider Picture Set (SPPS) for Rating/Review.<br><br>You can Rate/Review this SPPS by logging into the HereCut App and clicking the Home Icon > My Services.  <br><br><br>Thank You <br>HereCut Team';
					$from = 'anoop@webdior.com';
					$fromName = 'HereCut';

					$from = 'anoop@webdior.com';
					/*    $headers = "From: " . $from . "\r\n"; 
               $headers .= "Reply-To: ". $from . "\r\n"; 
             $headers .= "CC: test@example.com\r\n"; 
             $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                 $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/
					$this->smtpEmail($useremail, $subject, $body_html);
				}

			}


			/********************************EMAIL FUNCTION ******************************/
//             $email = (explode('@', $userEmail));
//           $message = \Swift_Message::newInstance()
//                    ->setSubject('Mail From HereCut App Team')
//                    ->setFrom('raj@webdior.com')
//                    ->setTo($userEmail)
//                    ->setBody("Hello " . strtoupper($email[0]) . ",
//       Your email is" . ' ' . ($userEmail) . "                  
//       for this random genrate password is"
//                    . ' ' . ($loginPassword) . ".
//                             
//       Use this password for Login in HereCut App
//                           
//       Thanks & Regards
//       HereCut App Team");
//            $this->get('mailer')->send($message);
			/*****************************************END************/
			$NotificationMdoel = $this->getDoctrine()->getRepository("AcmeDemoBundle:NotificationMessage")->findBy(
				['userID' => $stmt->getId()]
			);

			if ($NotificationMdoel != '' && $NotificationMdoel != null) {
				$counter = count($NotificationMdoel);
			} else {
				$counter = '';
			}

			/* notification code start */

			//if ($request->get('device_type') == '0'||'1') {
			//to prevent having to ask for permission will search by user id instead of imei
			//$notification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findOneBy(
			//	['imei' => ($request->get('imei'))]
			$notification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findOneBy(
				['userID' => $stmt->getId()]
			);
			if ($notification != '' && $notification != null) {
				$deviceID = $notification->getDeviceID();
				$notification->setUserID($stmt->getId());
				$notification->setDeviceID($this->ClearText($request->get('device_id')));
				$notification->setDeviceType($request->get('device_type'));
				$notification->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notification);
				$em->flush();
			} else {
				$notificationModel = new Notification();
				$notificationModel->setUserID($stmt->getId());
				$notificationModel->setDeviceID($this->ClearText($request->get('device_id')));
				$notificationModel->setDeviceType($request->get('device_type'));
				$notificationModel->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notificationModel);
				$em->flush();
			}
			// }

			/* notification code end */


			// print_r(str_replace("http", "https", 'https://dsjfhdkj'));die;
			echo json_encode(
				array(
					"success" => 1,
					"message" => $this->ClearText("success login"),
					'counter' => $counter,
					'user_id' => $stmt->getId(),
					'user_email' => $this->ClearText($userEmail),
					'user_type' => $stmt->getUserType(),
					'user_name' => $this->ClearText($user_name),
					'company_name' => $this->ClearText($company_name),
					'notify_counter' => $counter,
					'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
				)
			);

			return array();
		} else {
			echo json_encode(
				array("success" => 0, "message" => $this->ClearText("The email and password you entered don't match."))
			);

			return array();
		}
	}

	/*     * **************************************Login End**************************************************** */

	/**
	 * @Route("/signup", name="_signup")
	 * @method= post
	 * @Template()
	 */
	/*     * *****************************************SIGN UP Begin*********************************************************** */
	public function signupAction(
		Request $user_email,
		Request $user_type,
		Request $device_id,
		Request $device_type,
		Request $imei
	) {

		$request = $this->getRequest();
		$userEmail = $this->ClearText($request->get('user_email'));
		$email = (explode('@', $userEmail));

		$userType = $request->get('user_type');
		$stmt = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['userEmail' => $userEmail]);

		if ($stmt == '' && $stmt == null) {
			$userRegistration = new User();
			$encoder = $this->container->get('my_user.manager')->getEncoder($userRegistration);
			$userRegistration->setUserEmail($userEmail);
			$userRegistration->setUserType($userType);
			$userRegistration->setIsNotification('1');
			//$userRegistration->setUserPassword(mt_rand(999, 9999));
			$password = mt_rand(999, 9999);
			$userRegistration->setUserPassword($encoder->encodePassword($password, $userRegistration->getSalt()));
			$userRegistration->setSignupDate(new \DateTime());
			$em = $this->getDoctrine()->getManager();
			$em->persist($userRegistration);
			$em->flush();

			/* notification code start */

			// if ($request->get('device_type') == '0') {
			//to prevent having to ask for permission will search by user id instead of imei
			//$notification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findOneBy(
			//['imei' => $this->ClearText($request->get('imei'))]
			/*
			$notification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findOneBy(
				['userID' => $stmt->getId()]
			);
			if ($notification != '' && $notification != null) {
				$deviceID = $notification->getDeviceID();
				$notification->setUserID($userRegistration->getId());
				$notification->setDeviceID($this->ClearText($request->get('device_id')));
				$notification->setDeviceType($request->get('device_type'));
				$notification->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notification);
				$em->flush();
			} else {
			*/
				//die('ok');
				$notificationModel = new Notification();
				$notificationModel->setUserID($userRegistration->getId());
				$notificationModel->setDeviceID($this->ClearText($request->get('device_id')));
				$notificationModel->setDeviceType($request->get('device_type'));
				$notificationModel->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notificationModel);
				$em->flush();
			//}
			// }

			/* notification code end */
			$userName = ucwords(($email[0]));
			$useremail = $userRegistration->getUserEmail();
			//$password = $userRegistration->getUserPassword();
			$subject = 'Welcome Mail From HereCut Team';
			$body_text = 'Welcome email from HereCut Team';
			$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br>Your signup email/username is : ' . $useremail . '<br>and Password : ' . $password . '<br> <br>Use this password for Login in HereCut App. We recommend you reset your password once in the app by clicking Settings > Change Password. <br><br><br>Thank You <br>HereCut Team';
			$from = 'anoop@webdior.com';
			$fromName = 'HereCut';

			/*   $headers = "From: " . $from . "\r\n"; 
               $headers .= "Reply-To: ". $from . "\r\n"; 
             $headers .= "CC: test@example.com\r\n"; 
             $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; */
			/// mail($useremail, $subject, $body_html, $headers);
			//     $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
			$this->smtpEmail($useremail, $subject, $body_html);


			echo json_encode(
				array(
					'success' => 1,
					'message' => $this->ClearText('successfully Signup your password send on your mail'),
					'user_id' => $userRegistration->getId(),
					'userType' => $userRegistration->getUserType(),
					'userEmail' => $this->ClearText($userRegistration->getUserEmail()),
					//'user_password' => $this->ClearText($userRegistration->getUserPassword())
					'user_password' => $this->ClearText($password)
				)
			);

			return array();
		} else {
			echo json_encode(
				array('success' => 0, 'message' => $this->ClearText('failure User Email Already Register'))
			);
		}
	}

	/*     * *********************************************SIGN UP End******************************************************************************* */

	/**
	 * @Route("/spregistration", name="_spregistration")
	 * @Template()
	 */
	/*     * *****************************************SIGN UP Begin*********************************************************** */
	public function spregistrationAction(
		Request $service_id,
		Request $service_price,
		Request $top_service,
		Request $user_id,
		Request $sign_file,
		Request $sign_base,
		Request $user_profile,
		Request $profile_base,
		Request $userFName,
		Request $userLName,
		Request $userName,
		Request $userDOB,
		Request $user_email,
		Request $user_address,
		Request $companyname,
		Request $userwebsite,
		Request $userbio,
		Request $usercontact,
		Request $user_type,
		Request $lat,
		Request $long
	) {

		$request = $this->getRequest();
		$profile_Base = $this->ClearText($request->get('profile_base'));
		$password = $this->ClearText($request->get('password'));
		$userID = $this->ClearText($request->get('user_id'));
		$services[] = $request->get('service_id');

		$serviceprice[] = $request->get('service_price');

		$TopServices[] = $request->get('top_service');

		$spReg = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['id' => $request->get('user_id'), 'userEmail' => $this->ClearText($request->get('user_email'))]
		);
//echo '<pre>';print_r($spReg);die;
		if ($spReg != '' && $spReg != null) {
			$userOldEmail = $spReg->getUserEmail();
			$user_emails = explode('@', $userOldEmail);
			if ($userOldEmail != ($request->get('user_email'))) {

				$userName = ucwords($user_emails[0]);
				$useremail = $userOldEmail;
				//$password = $stmt->getUserPassword();
				$subject = 'Welcome Mail From HereCut Team';
				$body_text = 'Welcome email from HereCut Team';
				$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br>Your  email is : ' . $useremail . '<br>and Password : ' . $password . '<br> <br>Use this password to login to the HereCut App. We recommend you change your password once logged into the app by clicking Settings > Change Password <br><br><br>Thanks <br>HereCut Team';
				$from = 'anoop@webdior.com';
				$fromName = 'HereCut';
				/*      $headers = "From: " . $from . "\r\n"; 
               $headers .= "Reply-To: ". $from . "\r\n"; 
             //$headers .= "CC: test@example.com\r\n"; 
             $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
              */
				$this->smtpEmail($useremail, $subject, $body_html);

			}
			$spReg->setUserFirstName($this->ClearText($request->get('userFName')));
			$spReg->setUserLastName($this->ClearText($request->get('userLName')));
			$spReg->setUserName($this->ClearText($request->get('userName')));
			$spReg->setUserDOB($this->ClearText($request->get('userDOB')));
			$spReg->setUserAddress($this->ClearText($request->get('user_address')));
			$spReg->setUserEmail($this->ClearText($request->get('user_email')));
			$spReg->setLat($this->ClearText($request->get('lat')));
			$spReg->setLongitute($this->ClearText($request->get('long')));

			if ($request->get('user_profile') != '') {

				$profile = $this->UploadFile(
					$this->ClearText($request->get('user_profile')),
					$this->ClearText($request->get('profile_base'))
				);
				$spReg->setUserProfileImage($this->ClearText($profile));
			} else {
				if ($spReg->getUserProfileImage() == '') {
					$spReg->setUserProfileImage('');
				} else {
					$spReg->setUserProfileImage($this->ClearText($spReg->getUserProfileImage()));
				}
			}
			if ($request->get('sign_file')) {
				$sign = $this->UploadFile($request->get('sign_file'), $request->get('sign_base'));

				$spReg->setUserSignature($this->ClearText($sign));
			} else {
				$spReg->setUserSignature('');
			}
			$spReg->setCompanyName($this->ClearText($request->get('companyname')));
			$spReg->setUserWebsite($this->ClearText($request->get('userwebsite')));
			$spReg->setUserBIO($this->ClearText($request->get('userbio')));
			$spReg->setUserMobileNo($this->ClearText($request->get('usercontact')));

			$em = $this->getDoctrine()->getManager();
			$em->persist($spReg);
			$em->flush();
		}
		/* Delete old services on this service provider and new inserted */
		$spservices = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
			['userID' => $request->get('user_id')]
		);

		if ($spservices != '' && $spservices != null) {
			foreach ($spservices as $spservicesVal) {
				$servicesID[] = $spservicesVal->getServiceID();
				$servicesPrice[] = $spservicesVal->getServicePrice();
				$topService[] = $spservicesVal->getTopService();
				$em = $this->getDoctrine()->getEntityManager();
				$em->remove($spservicesVal);
				$em->flush();
			}
		} else {
			$servicesID = [];
			$servicesPrice = [];
			$topService = [];
		}
//
		if ($services[0] != '' && $services[0] != null) {
			//die('ok');
			//$services = array_unique($services[0]);
			$servicesimp = explode(',', $services[0]);
			//$servicesimp =  array_unique($servicesimp1);
			//  echo '<pre>';print_r($servicesimp);die;
		} else {
			// die('else');
			//echo '<pre>';print_r($servicesID);die;
			$servicesimp = '';
		}

		if (count($serviceprice[0]) > 0 && $serviceprice[0] != null) {
			$price = explode(',', $serviceprice[0]);
			// $price = array_unique($price1);
		} else {

			$price = '';
		}
		if (count($TopServices[0]) > 0 && $TopServices[0] != null) {
			$topservic = explode(',', $TopServices[0]);
			// $topservic = array_unique($topservic1);
		} else {

			$topservic = '';
		}

		if (count($servicesimp) > 0 && $servicesimp != null) {
			for ($i = 0; $i < count($servicesimp); $i++) {
				$serviceModel = new UserServices();
				$serviceModel->setServiceID($servicesimp[$i]);
				$serviceModel->setUserID($userID);
				$serviceModel->setServicePrice($price[$i]);
				$serviceModel->setTopService($topservic[$i]);
				$em = $this->getDoctrine()->getManager();
				$em->persist($serviceModel);
				$em->flush();
			}
			$serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
				['userID' => $request->get('user_id')]
			);
			// echo '<pre>';print_r($serviceName);die;
			if ($serviceName != '' && $serviceName != null) {
				foreach ($serviceName as $serviceNameVal) {

					$serviceId = $serviceNameVal->getServiceID();
					$topservice = $serviceNameVal->getTopService();

					$serviceprice = $serviceNameVal->getServicePrice();
					$masterService = $this->getDoctrine()->getRepository("AcmeDemoBundle:MasterServices")->findOneBy(
						['id' => $serviceId]
					);
					$service_name = $masterService->getServiceName();
					$servicedata[] = ([
						'service_Name' => $service_name,
						'service_Price' => $serviceprice,
						'top_service' => $topservice,
						'service_id' => $serviceId
					]);
				}
			} else {
				$servicedata = [];
				$serviceprice = '';
				$service_name = '';
			}
		} else {
			$servicedata = [];
			$serviceprice = '';
			$service_name = '';
		}
		// print_r($servicedata);die;
		$profile_img = $spReg->getUserProfileImage();
		if ($profile_img == '') {
			$image = $this->baseurl() . 'defaultprofile.png';
		} else {
			$image = $this->baseurl() . $profile_img;
		}
		$sign_img = $spReg->getUserSignature();
		if ($sign_img == '' && $sign_img > 0) {
			$imagesign = '';
		} else {
			$imagesign = $this->baseurl() . $sign_img;
		}
		if ($spReg->getUserFirstName() != '') {
			$userFName = $spReg->getUserFirstName();
		} else {
			$userFName = '';
		}
		if ($spReg->getUserLastName() != '') {
			$userLName = $spReg->getUserLastName();
		} else {
			$userLName = '';
		}
		if (($spReg->getUserName()) != '') {
			$userName = $spReg->getUserName();
		} else {
			$userName = '';
		}
		if ($spReg->getUserDOB() != '') {
			$userDOB = $spReg->getUserDOB();
		} else {
			$userDOB = '';
		}
		if ($spReg->getUserAddress() != '') {
			$userAddress = $spReg->getUserAddress();
		} else {
			$userAddress = '';
		}
		if ($spReg->getUserEmail() != '') {
			$userEmail = $spReg->getUserEmail();
		} else {
			$userEmail = '';
		}
		if ($spReg->getCompanyName() != '') {
			$userCompany = $spReg->getCompanyName();
		} else {
			$userCompany = '';
		}
		if ($spReg->getUserWebsite() != '') {
			$userWeb = 'http://' . $spReg->getUserWebsite();
		} else {
			$userWeb = '';
		}
		if ($spReg->getUserBIO() != '') {
			$userBIO = $spReg->getUserBIO();
		} else {
			$userBIO = '';
		}
		if ($spReg->getUserMobileNo() != '') {
			$userMobile = $spReg->getUserMobileNo();
		} else {
			$userMobile = '';
		}

		echo json_encode(
			array(
				'success' => 1,
				'message' => $this->ClearText('success'),
				'services' => $servicedata,
				'userFirstName' => $this->ClearText($userFName),
				'userLastname' => $this->ClearText($userLName),
				'userName' => $this->ClearText($userName),
				'userDOB' => $this->ClearText($userDOB),
				'user_address' => $this->ClearText($userAddress),
				'user_email' => $this->ClearText($userEmail),
				'companyName' => $this->ClearText($userCompany),
				'userwebsite' => $this->ClearText($userWeb),
				'userBIO' => $this->ClearText($userBIO),
				'userMobileNo' => $this->ClearText($userMobile),
				'user_type' => $spReg->getUserType(),
				'profile_image' => $this->ClearText($image),
				'sign_image' => $this->ClearText($imagesign)
			)
		);

		return array();
	}

//         else {
//            echo json_encode(array('success' => 0, 'message' => $this->ClearText('failure')));
//        }


	/*     * *********************************************SIGN UP End******************************************************************************* */


	/*     * *********************************************SIGN UP End******************************************************************************* */

	/**
	 * @Route("/forgot", name="_forgot")
	 * @Template()
	 */
	/*     * *****************************************Forgot Password Begin****************************************************************** */
	public function forgotAction(Request $user_email)
	{
		$request = $this->getRequest();
		$email = $this->ClearText($request->get('user_email'));
		$user_emails = (explode('@', $email));
		$stmt = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(array('userEmail' => $email));
// echo '<pre>';print_r($stmt);die;
		if ($stmt != '' && $stmt != null) {
			// $six_digit_random_number = mt_rand(100000, 999999);
			// $stmt->setUserPassword(rand(2, 9999999));
			$encoder = $this->container->get('my_user.manager')->getEncoder($stmt);
			//$stmt->setUserPassword(mt_rand(999, 9999));
			$password = mt_rand(999, 9999);
			$stmt->setUserPassword($encoder->encodePassword($password, $stmt->getSalt()));
			$em = $this->getDoctrine()->getManager();
			$em->persist($stmt);
			$em->flush();
			$userName = ucwords($user_emails[0]);
			$useremail = $email;
			//$password = $stmt->getUserPassword();
			$subject = 'Forgot password';
			$body_text = 'Forgot password email from HereCut Team';
			$body_html = 'Hello ' . $userName . ',<br><br>We have received your request for password reset.<br><br>Your Email id is: : ' . $useremail . '<br>and Password : ' . $password . '<br> <br>Use this password to login to the HereCut App. We recommend you change your password once logged into the app by clicking Settings > Change Password <br><br><br>Thank You <br>HereCut Team';
			//  $from = 'anoop@webdior.com';
			// $fromName = 'HereCut';
			//  $headers = "From: " . $from . "\r\n"; 
			// $headers .= "Reply-To: ". $from . "\r\n"; 
			//$headers .= "CC: test@example.com\r\n"; 
			// $headers .= "MIME-Version: 1.0\r\n"; 
			// $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
			//   mail($useremail, $subject, $body_html, $headers);
			//      $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
			$this->smtpEmail($useremail, $subject, $body_html);

			echo json_encode(
				array("success" => 1, "message" => $this->ClearText("link send successfully on your mail"))
			);

			return array();
		} else {
			echo json_encode(array("success" => 0, "message" => $this->ClearText("failure")));

			return array();
		}
	}

	/*     * ***********************************************Forgot Password End*************************************************************************** */

	/**
	 * @Route("/sociallogin", name="_sociallogin")
	 * @Template()
	 */
	/*     * ***********************************************Social Login Begin*************************************************************************** */
	public function socialloginAction(Request $user_email, Request $login_type, Request $user_type)
	{
		$request = $this->getRequest();
		$userEmail = $this->ClearText($request->get('user_email'));
		$userType = $this->ClearText($request->get('user_type'));
		$loginType = $this->ClearText($request->get('login_type'));

		$stmt = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['userEmail' => $userEmail, 'userType' => $userType, 'loginType' => $loginType]
		);

		if ($stmt != '' && $stmt != null) {
			echo json_encode(
				array(
					"success" => 1,
					"message" => $this->ClearText("Successfully login"),
					'user_id' => $stmt->getId(),
					'user_email' => ($stmt->getUserEmail()),
					'user_type' => $stmt->getUserType(),
					'login_type' => $stmt->getLoginType(),
					'account_type' => $this->ClearText('old')
				)
			);

			return array();
		} else {
			$social = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['userEmail' => $userEmail]
			);
			if ($social == '') {
				$socialLogin = new User();
				$socialLogin->setUserEmail($userEmail);
				$socialLogin->setUserType($userType);
				$socialLogin->setLoginType($loginType);
				$socialLogin->setIsNotification('1');
				$em = $this->getDoctrine()->getManager();
				$em->persist($socialLogin);
				$em->flush();

				$notificationModel = new Notification();
				$notificationModel->setUserID($socialLogin->getId());
				$notificationModel->setDeviceID($this->ClearText($request->get('device_id')));
				$notificationModel->setDeviceType($request->get('device_type'));
				$notificationModel->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notificationModel);
				$em->flush();

				echo json_encode(
					array(
						"success" => 1,
						"message" => $this->ClearText("Successfully sign up"),
						'user_id' => $socialLogin->getId(),
						'account_type' => $this->ClearText('new'),
						'user_email' => $this->ClearText($socialLogin->getUserEmail()),
						'user_type' => $socialLogin->getUserType(),
						'login_type' => $socialLogin->getLoginType(),
						'account_type' => $this->ClearText('new')
					)
				);

				return array();
			} else {
				if (($social->getUserType() == $userType)) {
					$social->setUserType($userType);
					$social->setLoginType($loginType);
					$social->setIsNotification('1');
					$em = $this->getDoctrine()->getManager();
					$em->persist($social);
					$em->flush();

					echo json_encode(
						array(
							"success" => 1,
							"message" => $this->ClearText("Successfully login"),
							'user_id' => $social->getId(),
							'user_email' => $this->ClearText($social->getUserEmail()),
							'user_type' => $social->getUserType(),
							'login_type' => $social->getLoginType(),
							'account_type' => $this->ClearText('old')
						)
					);
				} else {
					echo json_encode(
						array("success" => 0, "message" => $this->ClearText("This email_id already registered"))
					);
				}
			}
		}
	}

	/*     * ***********************************************Social Login End*************************************************************************** */

	/**
	 * @Route("/services", name="_services")
	 * @Template()
	 */
	/*     * ***********************************************SERVICES BEGIN*************************************************************************** */
	public function servicesAction()
	{
		$Masterservices = $this->getDoctrine()->getRepository("AcmeDemoBundle:MasterServices")->findAll();
		if ($Masterservices != '' && $Masterservices != null) {
			foreach ($Masterservices as $values) {
				$serviceName[] = array(
					'service_id' => $values->getId(),
					'service_name' => $this->ClearText($values->getServiceName())
				);
			}
			echo json_encode(
				array("success" => 1, "message" => $this->ClearText("success"), 'services' => $serviceName)
			);

			return array();
		} else {
			echo json_encode(array("success" => 0, "message" => $this->ClearText("failure")));

			return array();
		}
	}

	/*     * ***********************************************SERVICES END*************************************************************************** */

    /**************************************************************************company calendar event create Begin ******************************************* */
	/**
	 * @Route("/companycalendarcreate", name="_companycalendarcreate")
	 * @Template()
	 */

	public function companycalendarcreateAction(
		Request $user_id,
		Request $calendarID,
		Request $title,
		Request $description,
        Request $start_date,
        Request $end_date,
		Request $start_time,
		Request $end_time,
        Request $reminder
	) {
		$request = $this->getRequest();
		$userFind = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['id' => $request->get('user_id')]
		);
//echo '<pre>';print_r($userRegistration);die;
		if ($userFind != '' && $userFind != null)
		{
			$calendar = $this->getDoctrine()->getRepository("AcmeDemoBundle:CalendarEntry")->findOneBy(
				['id' => $request->get('calendarID')]
			);
			if ($calendar != '' && $calendar != null)
			{

				if ($calendar->getId() != '')
				{
					$calendarID = $calendar->getId();
				}
				else
					{
					$calendarID = '';
				}
                if ($request->get('user_id') != '') {
                    $user_id = $request->get('user_id');
                } else {
                    $user_id = '';
                }
				if ($calendar->getTitle() != '') {
					$title = $calendar->getTitle();
				} else {
					$title = '';
				}
				if ($calendar->getDescription() != '') {
					$description = $calendar->getDescription();
				} else {
					$description = '';
				}
                if (($calendar->getStartDate()) != '') {
                    $start_date = $calendar->getStartDate();
                } else {
                    $start_date = '';
                }
                if ($calendar->getEndDate() != '') {
                    $end_date = $calendar->getEndDate();
                } else {
                    $end_date = '';
                }
				if (($calendar->getStartTime()) != '') {
					$start_time = $calendar->getStartTime();
				} else {
					$start_time = '';
				}
				if ($calendar->getEndTime() != '') {
					$end_time = $calendar->getEndTime();
				} else {
					$end_time = '';
				}
                if ($calendar->getReminder() != '0') {
                    $reminder = $calendar->getReminder();
                } else {
                    $reminder = '0';
                }
			}
			else
				{
				$calendarModel = new CalendarEntry();
				$calendarModel->getId();
				$calendarModel->setUserID($this->ClearText($request->get('user_id')));
				$calendarModel->setTitle($request->get('title'));
				$calendarModel->setDescription($this->ClearText($request->get('description')));
				$calendarModel->setStartDate($this->ClearText($request->get('start_date')));
				$calendarModel->setEndDate($this->ClearText($request->get('end_date')));
				$calendarModel->setStartTime($this->ClearText($request->get('start_time')));
				$calendarModel->setEndTime($this->ClearText($request->get('end_time')));
                $calendarModel->setReminder($this->ClearText($request->get('reminder')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($calendarModel);
				$em->flush();

					if ($calendarModel->getId() != '') {
						$calendarID = $calendarModel->getId();
					} else {
						$calendarID = '';
					}
					if ($calendarModel->getTitle() != '') {
						$title = $calendarModel->getTitle();
					} else {
						$title = '';
					}
					if ($calendarModel->getDescription() != '') {
						$description = $calendarModel->getDescription();
					} else {
						$description = '';
					}
                    if (($calendarModel->getStartDate()) != '') {
                        $start_date = $calendarModel->getStartDate();
                    } else {
                        $start_date = '';
                    }
                    if ($calendarModel->getEndDate() != '') {
                        $end_date = $calendarModel->getEndDate();
                    } else {
                        $end_date = '';
                    }
					if (($calendarModel->getStartTime()) != '') {
						$start_time = $calendarModel->getStartTime();
					} else {
						$start_time = '';
					}
					if ($calendarModel->getEndTime() != '') {
						$end_time = $calendarModel->getEndTime();
					} else {
						$end_time = '';
					}
                    if ($calendarModel->getReminder() != '0') {
                        $reminder = $calendarModel->getReminder();
                    } else {
                        $reminder = '0';
                    }
			}

			echo json_encode(
				array(
					'success' => 1,
					'message' => $this->ClearText('successful'),
					'calendarID' => $calendarID,
					'user_id' => $user_id,
					'title' => $this->ClearText($title),
					'description' => $this->ClearText($description),
					'start_time' => $this->ClearText($start_time),
					'end_time' => $this->ClearText($end_time),
                    'start_date' => $this->ClearText($start_date),
                    'end_date' => $this->ClearText($end_date),
                    'reminder' => $this->ClearText($reminder)
				)
			);
		}
		else
			{
			echo json_encode(array('success' => 0, 'message' => $this->ClearText('failure')));
		}
	}

	/*     * *************************************************************************company calendar event create End******************************************* */

    /**************************************************************************company calendar appointments Begin ******************************************* */
    /**
     * @Route("/companycalendarappts", name="_companycalendarappts")
     * @Template()
     */

    public function companycalendarappointmentsAction(
        Request $user_id,
        Request $start_date

    ) {
        $request = $this->getRequest();
        $userFind = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
            ['id' => $request->get('user_id')]
        );
//echo '<pre>';print_r($userRegistration);die;
        if ($userFind != '' && $userFind != null)
        {
            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
            $userID = $request->get('user_id');
            $startDate = $request->get('start_date');

            //$calendar = $conn->query("select * from calendar_entry where userID=" . $userID . " AND start_date=" . $startDate . "")->fetchAll();
            $calendar = $this->getDoctrine()->getRepository("AcmeDemoBundle:CalendarEntry")->findBy(['userID' => $request->get('user_id'),'startDate' => $request->get('start_date')]);
            //echo '<pre>';print_r($calendar);die;
            if ($calendar != '' && $calendar != null)
            {
                foreach ($calendar as $calVal) {

                    $calendar_appt_details[] = array(

                            'calendarID' => $calVal->getId(),
                            'title' => $calVal->getTitle(),
                            'description' => $calVal->getDescription(),
                            'start_date' => $calVal->getStartDate(),
                            'end_date' => $calVal->getEndDate(),
                            'start_time' => $calVal->getStartTime(),
                            'end_time' => $calVal->getEndTime(),
                            'reminder' => $calVal->getReminder()

                  );
                }

                echo json_encode(
                    array(
                        'success' => 1,
                        'message' => $this->ClearText('successful'),
                        'calendar_appt_details' => $calendar_appt_details

                    )
                );
                return array();
            }
        }
        else
        {
            echo json_encode(array('success' => 0, 'message' => $this->ClearText('failure')));
        }
    }

    /** *************************************************************************company calendar appointments End******************************************* */


    /**************************************************************************company calendar event update or delete Begin ******************************************* */
    /**
     * @Route("/companycalendarupdate", name="_companycalendarupdate")
     * @Template()
     */

    public function companycalendarupdateAction(
        Request $user_id,
        Request $calendarID,
        Request $title,
        Request $description,
        Request $start_date,
        Request $end_date,
        Request $start_time,
        Request $end_time,
        Request $reminder
    ) {
        $request = $this->getRequest();
        $userFind = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
            ['id' => $request->get('user_id')]
        );
//echo '<pre>';print_r($userRegistration);die;
        if ($userFind != '' && $userFind != null)
        {
            $calendar = $this->getDoctrine()->getRepository("AcmeDemoBundle:CalendarEntry")->findOneBy(
                ['id' => $request->get('calendarID')]
            );
            if ($calendar != '' && $calendar != null)
            {
                if($request->get('delete_appt') != 'yes')
                {
                    $calendar->setTitle($request->get('title'));
                    $calendar->setDescription($this->ClearText($request->get('description')));
                    $calendar->setStartDate($this->ClearText($request->get('start_date')));
                    $calendar->setEndDate($this->ClearText($request->get('end_date')));
                    $calendar->setStartTime($this->ClearText($request->get('start_time')));
                    $calendar->setEndTime($this->ClearText($request->get('end_time')));
                    $calendar->setReminder($this->ClearText($request->get('reminder')));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($calendar);
                    $em->flush();

                    if ($calendar->getId() != '')
                    {
                        $calendarID = $calendar->getId();
                    }
                    else
                    {
                        $calendarID = '';
                    }
                    if ($request->get('user_id') != '')
                    {
                        $user_id = $request->get('user_id');
                    }
                    else
                    {
                        $user_id = '';
                    }
                    if ($calendar->getTitle() != '')
                    {
                        $title = $calendar->getTitle();
                    }
                    else
                    {
                        $title = '';
                    }
                    if ($calendar->getDescription() != '')
                    {
                        $description = $calendar->getDescription();
                    }
                    else
                    {
                        $description = '';
                    }
                    if (($calendar->getStartDate()) != '')
                    {
                        $start_date = $calendar->getStartDate();
                    }
                    else
                    {
                        $start_date = '';
                    }
                    if ($calendar->getEndDate() != '')
                    {
                        $end_date = $calendar->getEndDate();
                    }
                    else
                    {
                        $end_date = '';
                    }
                    if (($calendar->getStartTime()) != '')
                    {
                        $start_time = $calendar->getStartTime();
                    }
                    else
                    {
                        $start_time = '';
                    }
                    if ($calendar->getEndTime() != '')
                    {
                        $end_time = $calendar->getEndTime();
                    }
                    else
                    {
                        $end_time = '';
                    }
                    if ($calendar->getReminder() != '0')
                    {
                        $reminder = $calendar->getReminder();
                    }
                    else
                    {
                        $reminder = '0';
                    }
                }
                else
                {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->remove($calendar);
                    $em->flush();
                }
            }
            else
            {

            }

            echo json_encode(
                array(
                    'success' => 1,
                    'message' => $this->ClearText('successful'),
                    'calendarID' => $calendarID,
                    'user_id' => $user_id,
                    'title' => $this->ClearText($title),
                    'description' => $this->ClearText($description),
                    'start_time' => $this->ClearText($start_time),
                    'end_time' => $this->ClearText($end_time),
                    'start_date' => $this->ClearText($start_date),
                    'end_date' => $this->ClearText($end_date),
                    'reminder' => $this->ClearText($reminder)
                )
            );
        }
        else
        {
            echo json_encode(array('success' => 0, 'message' => $this->ClearText('failure')));
        }
    }

    /*     * *************************************************************************company calendar event update or delete  End******************************************* */

	/**
	 * @Route("/category", name="_category")
	 * @Template()
	 */
	/*     * ********************************************************************Master Category Begin**************************************************** */

	public function categoryAction()
	{

		$masterCategory = $this->getDoctrine()->getRepository("AcmeDemoBundle:MasterCategory")->findAll();
		if ($masterCategory != '' && $masterCategory != null) {
			foreach ($masterCategory as $catVal) {
				$categoryName[] = array(
					'cateegory_id' => $catVal->getId(),
					'category_name' => $this->ClearText($catVal->getCategoryName())
				);
			}
			echo json_encode(
				array("success" => 1, "message" => $this->ClearText("success"), 'categories' => $categoryName)
			);

			return array();
		} else {
			echo json_encode(array("success" => 0, "message" => $this->ClearText("failure")));

			return array();
		}
	}

	/*     * *************************************************************************Master Category End******************************************* */

	/**
	 * @Route("/profileupdate", name="_profileupdate")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function profileupdateAction(
		Request $user_id,
		Request $user_name,
		Request $user_contact,
		Request $profile_image,
		Request $profile_base,
		Request $user_fname,
		Request $user_type,
		Request $user_lname,
		Request $user_DOB,
		Request $user_email,
		Request $user_address,
		Request $user_gender,
		Request $is_notification,
		Request $lat,
		Request $long

	) {
		$request = $this->getRequest();
		$userRegistration = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['id' => $request->get('user_id')]
		);
//echo '<pre>';print_r($userRegistration);die;
		if ($userRegistration != '' && $userRegistration != null) {
			$userRegistration->setUserFirstName($this->ClearText($request->get('user_fname')));
			$userRegistration->setUserLastName($this->ClearText($request->get('user_lname')));
			$userRegistration->setUserName($this->ClearText($request->get('user_name')));
			$userRegistration->setUserDOB($this->ClearText($request->get('user_DOB')));
			$userRegistration->setUserEmail($this->ClearText($request->get('user_email')));
			$userRegistration->setLat($this->ClearText($request->get('lat')));
			$userRegistration->setLongitute($this->ClearText($request->get('long')));
//$userRegistration->setUserNote($request->get('user_notes'));
			$userRegistration->setUserGender($this->ClearText($request->get('user_gender')));
			$userRegistration->setUserMobileNo($this->ClearText($request->get('user_contact')));
			$userRegistration->setUserAddress($this->ClearText($request->get('user_address')));
			$userRegistration->setIsNotification('1');
			if ($request->get('profile_image') != '' && $request->get('profile_image') != null) {

				$profileImage = $this->UploadFile($request->get('profile_image'), $request->get('profile_base'));
				$userRegistration->setUserProfileImage($this->ClearText($profileImage));
			} else {
				if ($userRegistration->getUserProfileImage() == '') {
					$userRegistration->setUserProfileImage('');
				} else {
					$userRegistration->setUserProfileImage($this->ClearText($userRegistration->getUserProfileImage()));
				}
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist($userRegistration);
			$em->flush();
			$profile_img1 = $userRegistration->getUserProfileImage();
			if ($profile_img1 != '' && $profile_img1 != null) {
				$image1 = $this->baseurl() . $profile_img1;
			} else {
				$image1 = $this->baseurl() . 'defaultprofile.png';
			}
			if ($userRegistration->getId() != '') {
				$user_id = $userRegistration->getId();
			} else {
				$user_id = '';
			}
			if ($userRegistration->getUserFirstName() != '') {
				$userfname = $userRegistration->getUserFirstName();
			} else {
				$userfname = '';
			}
			if ($userRegistration->getUserLastName() != '') {
				$userlname = $userRegistration->getUserLastName();
			} else {
				$userlname = '';
			}
			if (($userRegistration->getUserName()) != '') {
				$username = $userRegistration->getUserName();
			} else {
				$username = '';
			}
			if ($userRegistration->getUserMobileNo() != '') {
				$user_contact = $userRegistration->getUserMobileNo();
			} else {
				$user_contact = '';
			}
			if ($userRegistration->getUserDOB() != '') {
				$user_dob = $userRegistration->getUserDOB();
			} else {
				$user_dob = '';
			}
			if ($userRegistration->getUserEmail() != '') {
				$user_email = $userRegistration->getUserEmail();
			} else {
				$user_email = '';
			}
			if ($userRegistration->getUserGender() != '') {
				$user_gender = $userRegistration->getUserGender();
			} else {
				$user_gender = '';
			}
			if ($userRegistration->getIsNotification() != '') {
				$user_notification = $userRegistration->getIsNotification();
			} else {
				$user_notification = '';
			}
			if ($userRegistration->getUserAddress() != '') {
				$user_address = $userRegistration->getUserAddress();
			} else {
				$user_address = '';
			}
			echo json_encode(
				array(
					'success' => 1,
					'message' => $this->ClearText('successfull'),
					'user_id' => $user_id,
					'user_fname' => $this->ClearText($userfname),
					'user_lname' => $this->ClearText($userlname),
					'user_name' => $this->ClearText($username),
					'user_contact' => $this->ClearText($user_contact),
					'user_DOB' => $this->ClearText($user_dob),
					'user_email' => $this->ClearText($user_email),
					'user_gender' => $this->ClearText($user_gender),
					'user_address' => $this->ClearText($user_address),
					'hide_info' => $this->ClearText($user_notification),
					'profile_Image' => $this->ClearText($image1)
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => $this->ClearText('failure')));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/normaluserprofile", name="_normaluserprofile")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */


	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/userservices", name="_userservices")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function userservicesAction(Request $user_id)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');

		$userServices = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
			['userID' => $userID]
		);

		if ($userServices != '' && $userServices != null) {
			foreach ($userServices as $serviceval) {
				$user_service = $serviceval->getServiceID();
				$service_price = $serviceval->getServicePrice();

				//echo '<pre>';print_r($service_price);die;


				$master_service = $this->getDoctrine()->getRepository("AcmeDemoBundle:MasterServices")->findOneBy(
					['id' => $this->ClearText($user_service)]
				);

				$getService = $master_service->getServiceName();


				$services[] = array(
					'service_Name' => $this->ClearText($getService),
					'service_Price' => $this->ClearText($service_price)
				);
			}


			echo json_encode(
				array('success' => 1, 'message' => $this->ClearText('successfull'), 'services' => $services)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => $this->ClearText('failure')));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/signupdoubleemail", name="_signupdoubleemail")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function signupdoubleemailAction(
		Request $sp_user_email,
		Request $user_email,
		Request $user_type,
		Request $sp_user_type
	) {
		$request = $this->getRequest();

		$UserSignup = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['userEmail' => $this->ClearText($request->get('sp_user_email')), 'userType' => '1']
		);

		$UserSignup1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['userEmail' => $this->ClearText($request->get('user_email')), 'userType' => '0']
		);


		if ($UserSignup != '') {
			if ($UserSignup1 != '') {
				$usercustomerRelation = $this->getDoctrine()->getRepository(
					"AcmeDemoBundle:UserCustomerRelation"
				)->findOneBy(['userID' => $UserSignup1->getId(), 'companyID' => $UserSignup->getId()]);

				if ($usercustomerRelation == '') {
					$users = new UserCustomerRelation();
					$users->setUserID($UserSignup1->getId());
					$users->setCompanyID($UserSignup->getId());
					$em = $this->getDoctrine()->getManager();
					$em->persist($users);
					$em->flush();
					$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
						['userID' => $UserSignup->getId(), 'toUserID' => $UserSignup1->getId()]
					);
					if ($userFOllows == '' && $userFOllows == null) {
						$USERfollow = new UserFollow();
						$USERfollow->setUserID($UserSignup1->getId());
						$USERfollow->setToUserID($UserSignup->getId());
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();

						$USERfollow = new UserFollow();
						$USERfollow->setUserID($UserSignup->getId());
						$USERfollow->setToUserID($UserSignup1->getId());
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();

					}
				}
			}
			echo json_encode(
				array('success' => 0, 'message' => $this->ClearText('Service provider email id already registered'))
			);
		} elseif (empty($UserSignup)) {
			if (!empty($UserSignup1)) {
				$usermodel2 = new User();
				$usermodel2->setUserEmail(($request->get('sp_user_email')));
				$usermodel2->setUserType($request->get('sp_user_type'));
				$usermodel2->setIsNotification('1');
				$encoder = $this->container->get('my_user.manager')->getEncoder($usermodel2);
				//$usermodel2->setUserPassword($this->ClearText(mt_rand(999, 9999)));
				$password = mt_rand(999, 9999);
				$usermodel2->setUserPassword($encoder->encodePassword($password, $usermodel2->getSalt()));
				$em = $this->getDoctrine()->getManager();
				$em->persist($usermodel2);
				$em->flush();
				$userEmail = explode('@', ($usermodel2->getUserEmail()));


				$userName = ucwords($userEmail[0]);
				$useremail = $usermodel2->getUserEmail();
				//$password = $usermodel2->getUserPassword();
				$subject = 'Welcome Mail';
				$body_text = 'Welcome email from HereCut Team';
				$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br>Your  email is : ' . $useremail . '<br>and Password : ' . $password . '<br> <br>Use this password to login to the HereCut App. We recommend you change your password once logged into the app by clicking Settings > Change Password <br><br><br>Thanks <br>HereCut Team';
				$from = 'anoop@webdior.com';
				$fromName = 'HereCut';
				/*   $headers = "From: " . $from . "\r\n"; 
               $headers .= "Reply-To: ". $from . "\r\n"; 
             //$headers .= "CC: test@example.com\r\n"; 
             $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
                */
				$this->smtpEmail($useremail, $subject, $body_html);


				echo json_encode(
					array(
						'success' => 1,
						'message' => $this->ClearText('successfull'),
						'user_info' => array(
							[
								'user_id' => $UserSignup1->getId(),
								'user_email' => $this->ClearText($UserSignup1->getUserEmail()),
								'user_type' => $UserSignup1->getUserType(),
								'account_type' => $this->ClearText('old')
							]
						),
						'sp_info' => array(
							[
								'sp_user_id' => $usermodel2->getId(),
								'sp_user_email' => $this->ClearText($usermodel2->getUserEmail()),
								'sp_user_type' => $usermodel2->getUserType()
							]
						)
					)
				);
				$usercustomerRelation = $this->getDoctrine()->getRepository(
					"AcmeDemoBundle:UserCustomerRelation"
				)->findOneBy(['userID' => $UserSignup1->getId(), 'companyID' => $usermodel2->getId()]);
// echo '<pre>';print_r($usercustomerRelation->getUserID());die;
				if ($usercustomerRelation == '') {
					// $userdata = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

					$users = new UserCustomerRelation();
					$users->setUserID($UserSignup1->getId());
					$users->setCompanyID($usermodel2->getId());
					$em = $this->getDoctrine()->getManager();
					$em->persist($users);
					$em->flush();
					$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
						['userID' => $users->getCompanyID(), 'toUserID' => $users->getUserID()]
					);
					if ($userFOllows == '' && $userFOllows == null) {
						$USERfollow = new UserFollow();
						$USERfollow->setUserID($users->getUserID());
						$USERfollow->setToUserID($users->getCompanyID());
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();
					}
				}
			} else {
				$Userdata = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
					['userEmail' => $this->ClearText($request->get('user_email'))]
				);
				if (empty($Userdata)) {
					$usermodel2 = new User();
					$usermodel2->setUserEmail($this->ClearText($request->get('sp_user_email')));
					$usermodel2->setUserType($request->get('sp_user_type'));
					$usermodel2->setIsNotification('1');
					$encoder = $this->container->get('my_user.manager')->getEncoder($usermodel2);
					//$usermodel2->setUserPassword($this->ClearText(mt_rand(999, 9999)));
					$password = mt_rand(999, 9999);
					$usermodel2->setUserPassword($encoder->encodePassword($password, $usermodel2->getSalt()));
					$em = $this->getDoctrine()->getManager();
					$em->persist($usermodel2);
					$em->flush();
					$userEmail = explode('@', ($usermodel2->getUserEmail()));
					$userName = ucwords($userEmail[0]);
					$useremail = $usermodel2->getUserEmail();
					//$password = $usermodel2->getUserPassword();
					$subject = 'Welcome Mail From HereCut Team';
					$body_text = 'Welcome email from HereCut Team';
					$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br>Your  email is : ' . $useremail . '<br>and Password : ' . $password . '<br> <br>Use this password to login to the HereCut App. We recommend you change your password once logged into the app by clicking Settings > Change Password <br><br><br>Thanks <br>HereCut Team';
					$from = 'info@herecut.net';
					$fromName = 'HereCut';
					$headers = "From: " . $from . "\r\n";
					$headers .= "Reply-To: " . $from . "\r\n";
					//$headers .= "CC: test@example.com\r\n"; 
					/*  $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
             */
					$this->smtpEmail($useremail, $subject, $body_html);


					$usermodel3 = new User();
					$usermodel3->setUserEmail(($request->get('user_email')));
					$usermodel3->setIsNotification('1');
					$usermodel3->setUserType($request->get('user_type'));
					$encoder = $this->container->get('my_user.manager')->getEncoder($usermodel3);
					//$usermodel3->setUserPassword(mt_rand(999, 9999));
					$password = mt_rand(999, 9999);
					$usermodel3->setUserPassword($encoder->encodePassword($password, $usermodel3->getSalt()));
					$em = $this->getDoctrine()->getManager();
					$em->persist($usermodel3);
					$em->flush();
					$userEmail = explode('@', ($usermodel3->getUserEmail()));
					$userName = ucwords($userEmail[0]);
					$useremail = $usermodel3->getUserEmail();
					//$password = $usermodel3->getUserPassword();
					$subject = 'Welcome Mail';
					$body_text = 'Welcome email from HereCut Team';
					$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br>Your  email is : ' . $useremail . '<br>and Password : ' . $password . '<br> <br>Use this password for Login in HereCut App <br><br><br>Thanks <br>HereCut Team';
					$from = 'info@herecut.net';
					$fromName = 'HereCut';
					$headers = "From: " . $from . "\r\n";
					$headers .= "Reply-To: " . $from . "\r\n";
					//$headers .= "CC: test@example.com\r\n"; 
					/* $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
              */
					$this->smtpEmail($useremail, $subject, $body_html);

					echo json_encode(
						array(
							'success' => 1,
							'message' => $this->ClearText('successfull'),
							'user_info' => array(
								[
									'user_id' => $usermodel3->getId(),
									'user_email' => $this->ClearText($usermodel3->getUserEmail()),
									'user_type' => $usermodel3->getUserType(),
									'account_type' => $this->ClearText('new')
								]
							),
							'sp_info' => array(
								[
									'sp_user_id' => $usermodel2->getId(),
									'sp_user_email' => $this->ClearText($usermodel2->getUserEmail()),
									'sp_user_type' => $usermodel2->getUserType()
								]
							)
						)
					);
					$usercustomerRelation = $this->getDoctrine()->getRepository(
						"AcmeDemoBundle:UserCustomerRelation"
					)->findOneBy(['userID' => $usermodel3->getId(), 'companyID' => $usermodel2->getId()]);
// echo '<pre>';print_r($usercustomerRelation->getUserID());die;
					if ($usercustomerRelation == '') {
						// $userdata = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

						$users = new UserCustomerRelation();
						$users->setUserID($usermodel3->getId());
						$users->setCompanyID($usermodel2->getId());
						$em = $this->getDoctrine()->getManager();
						$em->persist($users);
						$em->flush();

						$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
							['userID' => $users->getCompanyID(), 'toUserID' => $users->getUserID()]
						);
						if ($userFOllows == '' && $userFOllows == null) {


							$USERfollow = new UserFollow();
							$USERfollow->setUserID($users->getUserID());
							$USERfollow->setToUserID($users->getCompanyID());
							$USERfollow->setFollowStatus('1');
							$em = $this->getDoctrine()->getManager();
							$em->persist($USERfollow);
							$em->flush();

							$USERfollow = new UserFollow();
							$USERfollow->setUserID($users->getCompanyID());
							$USERfollow->setToUserID($users->getUserID());
							$USERfollow->setFollowStatus('1');
							$em = $this->getDoctrine()->getManager();
							$em->persist($USERfollow);
							$em->flush();

						}
					}
				} else {
					echo json_encode(
						array(
							'success' => 0,
							'message' => $this->ClearText('Consumer email id registered  as a Service Provider')
						)
					);
				}
			}
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/spprofiledetail", name="_spprofiledetail")
	 * @Template()
	 */
	/*     * *****************************************************************************Service provider profile Details Begin********** */
	public function spprofiledetailAction(Request $user_id, Request $customer_id, Request $login_type)
	{
//$albums_details = '';
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$customerID = $request->get('customer_id');
		$loginType = $request->get('login_type');
		if (($customerID == '' && $loginType == '') || ($customerID == 'null' && $loginType == 'null')) {
			$spProfileDetail = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $userID, 'userType' => '1']
			);
			// echo '<pre>';print_r($spProfileDetail);die;
			if ($spProfileDetail != '' && $spProfileDetail != null) {
				if ($spProfileDetail->getUserFirstName() && $spProfileDetail->getUserLastName() != '') {
					$userName = $spProfileDetail->getUserFirstName() . ' ' . $spProfileDetail->getUserLastName();
				} else {
					$userName = '';
				}
				if ($spProfileDetail->getCompanyName() != '') {
					$companyName = $spProfileDetail->getCompanyName();
				} else {
					$companyName = '';
				}
				if ($spProfileDetail->getUserProfileImage() != '' && count(
						$spProfileDetail->getUserProfileImage()
					) > 0
				) {

					$profileImg = $this->baseurl() . $spProfileDetail->getUserProfileImage();
				} else {
//                  
					$profileImg = $this->baseurl() . 'defaultprofile.png';
				}

				/*                 * ***********************GAUTAM SIR CHANGES 09-06-2016********************************************** */
				if ($spProfileDetail->getUserSignature() != '') {
					$imagesign = $this->baseurl() . $spProfileDetail->getUserSignature();
				} else {
					$imagesign = '';
				}
				if ($spProfileDetail->getUserFirstName() != '') {
					$userFName = $spProfileDetail->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($spProfileDetail->getUserLastName() != '') {
					$userLName = $spProfileDetail->getUserLastName();
				} else {
					$userLName = '';
				}

				if ($spProfileDetail->getUserDOB() != '') {
					$userDOB = $spProfileDetail->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($spProfileDetail->getUserAddress() != '') {
					$userAddress = $spProfileDetail->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($spProfileDetail->getUserEmail() != '') {
					$userEmail = $spProfileDetail->getUserEmail();
				} else {
					$userEmail = '';
				}

				if ($spProfileDetail->getUserWebsite() != '') {
					$userWeb = 'http://' . $spProfileDetail->getUserWebsite();
				} else {
					$userWeb = '';
				}
				if ($spProfileDetail->getUserBIO() != '') {
					$userBIO = $spProfileDetail->getUserBIO();
				} else {
					$userBIO = '';
				}
				if ($spProfileDetail->getUserMobileNo() != '') {
					$userMobile = $spProfileDetail->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($spProfileDetail->getUserType() != '') {
					$userType = $spProfileDetail->getUserType();
				} else {
					$userType = '';
				}


				$usercustomer = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findBy(
					['companyID' => $userID]
				);
				if ($usercustomer != '' && $usercustomer != null) {
					foreach ($usercustomer as $Customer1Val) {
						if ($Customer1Val->getUserID() != '') {
							$spID[] = $Customer1Val->getUserID();
						} else {
							$spID[] = '';
						}
					}
				} else {

					$spID = '';
				}

				if (count($spID) > 0 && $spID != '') {
					$countspID = count($spID);
				} else {
					$countspID = '';
				}


				/*                 * ***********************GAUTAM SIR CHANGES 09-06-2016********************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);

				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$followers[] = $user_followVal->getUserID();
						} else {
							$followers[] = '';
						}
					}
				} else {

					$followers = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {
					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$following[] = $user_followingVal->getToUserID();
						} else {
							$following[] = '';
						}
					}
				} else {
					$following = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['toUserID' => $userID]
				);

				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRatingVal) {
						if ($userRatingVal->getUserRating() != '') {
							$rating[] = $userRatingVal->getUserRating();
						} else {
							$rating[] = '';
						}
						if ($userRatingVal->getUserReviews() != '') {
							$user_reviews[] = $userRatingVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
						$count = count($rating);
					}


					$rating1 = array_sum($rating) / $count;
					$ratvalues = number_format((float)$rating1, 1, '.', '');

					//die;
				} else {
					$ratvalues = '';
					//$rating[] = '';
					$user_reviews = '';
				}

				$userServices = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
					['userID' => $userID]
				);

				if ($userServices != '' && $userServices != null) {
					foreach ($userServices as $serviceval) {
						if ($serviceval->getServiceID() != '') {

							$user_service = $serviceval->getServiceID();
							$master_service = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $user_service]);
							if ($master_service != '' && $master_service != null) {

								if ($master_service->getServiceName() != '') {
									$getService = $master_service->getServiceName();
								} else {
									$getService = '';
								}
							} else {
								$getService = '';
							}
						}
						if ($serviceval->getServicePrice() != '') {
							$service_price = $serviceval->getServicePrice();
						} else {
							$service_price = '';
						}
						$services[] = array('service_Name' => ($getService), 'service_Price' => $service_price);
					}
				} else {
					$services = [];
				}


				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();

				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();

				if ($relatedUser != '' && $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
						$post_id[] = $relatedUserVal['postID'];
					}
				} else {
					$post_id = '';
				}

//                $albumUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['id' => $post_id]);
//                if ($albumUser != '' && $albumUser != null) {
//                    foreach ($albumUser as $albumUserVal) {
//                        $spID1[] = $albumUserVal->getUserTagID();
//                        $post_id[] = $albumUserVal->getId();
//                    }
//
//
//                    $spID = array_unique($spID1);
//                    //echo '<pre>';print_r($spID);die;
//                    $spIDs = '';
//                    $users1 = '';
//                    for ($i = 0; $i < count($spID1); $i++) {
//                        if (($spID1[$i] != $customerID)) {
//                            $users1[] = $spID1[$i];
//                            $spIDs[] = $post_id[$i];
//                        }
//                    }
//                    if ($users1 != '' || $users1 != null) {
//                        $users = array_unique($users1);
//                    } else {
//                        $users = '';
//                    }
//                } else {
//                    $spID = '';
//                    $post_id = '';
//                    $users = '';
//                    $spIDs = '';
//                }
////echo '<pre>';print_r($spID);die('ok');
//                if (count($spID) < 1) {
//                    $spIDs = '';
//                    $users = '';
//                }
				if ($customerID != '' && $customerID != null) {

					$relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $post_id, 'postStatus' => '0', 'spPostStatus' => '0'],
						array('id' => 'desc'),
						6,
						0
					);

					if ($relatedUser1 != '' && $relatedUser1 != null) {
						foreach ($relatedUser1 as $relatedUserVal) {
							$album_id = $relatedUserVal->getId();
							$post_status = $relatedUserVal->getPostStatus();
							if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null
							) {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
							} elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
							} elseif ($relatedUserVal->getPostImageLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
							} elseif ($relatedUserVal->getPostImageBackLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
							} elseif ($relatedUserVal->getPostImageBack() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
							} elseif ($relatedUserVal->getPostImageBackRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
							} elseif ($relatedUserVal->getPostImageRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
							} elseif ($relatedUserVal->getPostImageFrontRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
							} else {
								$album_image = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $album_id]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							if ($relatedUserVal->getUserTagID() != '') {
								$user_ID = $relatedUserVal->getUserTagID();
								$user_ID1 = $relatedUserVal->getUserID();
								$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $user_ID, 'userType' => 0]
								);

								if (!empty($Users)) {
									$id = $Users->getId();
									if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

										$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID1, 'userType' => 0]
									);
									if ($Users != '' || $Users != null) {
										//echo '<pre>';print_r($Users);die;
										$id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}

									} else {
										$id = '';
										$user_name = '';
									}
								}
								$UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $relatedUserVal->getUserID()]
								);
								if (!empty($UserTag)) {
									if ($UserTag->getUserSignature() == '') {
										if ($UserTag->getCompanyName() != '') {
											$sign_image = $UserTag->getCompanyName();
										} else {
											$sign_image = '';
										}
									} else {
										$sign_image = $this->baseurl() . $UserTag->getUserSignature();
									}
								} else {
									$sign_image = '';
								}


								//die;
							} else {
								$user_ID = '';
							}
							$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $album_id]
							);
							if ($postRating != '' && $postRating != null) {

								if ($postRating->getUserRating() != '') {
									$Rating = $postRating->getUserRating();
								} else {
									$Rating = '';
								}
							} else {
								$Rating = '';
							}


							if (!empty($album_image)) {
								$albums_details[] = array(
									'user_id' => $id,
									'post_image' => ($album_image),
									'album_id' => $album_id,
									'user_Name' => ($user_name),
									'tag_status' => $tag_status,
									'avg_rating' => ($Rating),
									'post_sign' => ($sign_image),
									'post_status' => ($post_status)
								);
							}

							//$rate[] =  $Rating;
						}
					} else {
						$albums_details = [];
						$user_ID = '';
						$ratvalues = '';
						$relatedUser = '';
					}
				} else {
					// die('ok');
					$relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $post_id, 'postStatus' => '0', 'spPostStatus' => '0'],
						array('id' => 'desc'),
						6,
						0
					);
					//   echo '<pre>';print_r($relatedUser1);die;
					if ($relatedUser1 != '' && $relatedUser1 != null) {
						foreach ($relatedUser1 as $relatedUserVal) {
							$album_id = $relatedUserVal->getId();
							$post_status = $relatedUserVal->getPostStatus();
							if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null
							) {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
							} elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
							} elseif ($relatedUserVal->getPostImageLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
							} elseif ($relatedUserVal->getPostImageBackLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
							} elseif ($relatedUserVal->getPostImageBack() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
							} elseif ($relatedUserVal->getPostImageBackRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
							} elseif ($relatedUserVal->getPostImageRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
							} elseif ($relatedUserVal->getPostImageFrontRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
							} else {
								$album_image = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $album_id]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							if ($relatedUserVal->getUserTagID() != '') {
								$user_ID = $relatedUserVal->getUserTagID();
								$user_ID1 = $relatedUserVal->getUserID();
								$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $user_ID, 'userType' => 0]
								);

								if ($Users != '') {
									$id = $Users->getId();
									if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

										$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID1, 'userType' => 0]
									);
									if ($Users != '' || $Users != null) {
										//echo '<pre>';print_r($Users);die;
										$id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}

									} else {
										$id = '';
										$user_name = '';
									}
								}
								$UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $relatedUserVal->getUserID()]
								);
								if (isset($UserTag) && !empty($UserTag)) {
									if ($UserTag->getUserSignature() == '') {
										if ($UserTag->getCompanyName() != '') {
											$sign_image = $UserTag->getCompanyName();
										} else {
											$sign_image = '';
										}
									} else {
										$sign_image = $this->baseurl() . $UserTag->getUserSignature();
									}
								} else {
									$sign_image = '';
								}


								//die;
							} else {
								$user_ID = '';
							}
							$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $album_id]
							);
							if ($postRating != '' && $postRating != null) {

								if ($postRating->getUserRating() != '') {
									$Rating = $postRating->getUserRating();
								} else {
									$Rating = '';
								}
							} else {
								$Rating = '';
							}


							if (!empty($album_image)) {
								$albums_details[] = array(
									'user_id' => $id,
									'post_image' => ($album_image),
									'album_id' => $album_id,
									'user_Name' => ($user_name),
									'tag_status' => $tag_status,
									'avg_rating' => ($Rating),
									'post_sign' => ($sign_image),
									'post_status' => ($post_status)
								);
							}

							//$rate[] =  $Rating;
						}
					} else {
						$albums_details = [];
						$user_ID = '';
						$ratvalues = '';
						$relatedUser = '';
					}
				}

				if (count($user_reviews) > 0 && $user_reviews != '') {
					$countReview = count($user_reviews);
				} else {
					$countReview = 0;
				}
				if (count($followers) > 0 && $followers != '') {
					$countFollowers = count($followers);
				} else {
					$countFollowers = 0;
				}
				if (count($following) > 0 && $following != '') {
					$countFollowing = count($following);
				} else {
					$countFollowing = 0;
				}
				if (count($relatedUser) > 0 && $relatedUser != '') {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				//  $userrate
				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $customerID]
				);
				if ($user_status != '') {
					$userFollowStatus = '0';
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					}
				} else {
					$userFollowStatus = '0';
				}
				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID
					,
						'userFirstName' => $userFName,
						'userLastname' => $userLName,
						'userDOB' => $userDOB,
						'user_address' => $userAddress,
						'user_email' => $userEmail,
						'profile_image' => ($profileImg),
						'user_name' => ($userName),
						'company_name' => ($companyName),
						'userwebsite' => $userWeb,
						'userBIO' => $userBIO,
						'userSign' => $imagesign,
						'userMobileNo' => $userMobile,
						'user_type' => $userType,
						'total_post' => $countPost,
						'total_rating' => $ratvalues,
						'total_reviews' => $countReview,
						'total_follower' => $countFollowers,
						'total_following' => $countFollowing,
						'follow_status' => $userFollowStatus,
						'totalcustomer' => $countspID,
						'services' => $services,
						'albums' => $albums_details
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} elseif (($loginType == '1' && $customerID == '') || ($loginType == '1' && $customerID == 'null')) {
			$spProfileDetail = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $userID, 'userType' => '1']
			);

			if ($spProfileDetail != '' && $spProfileDetail != null) {
				if ($spProfileDetail->getUserFirstName() && $spProfileDetail->getUserLastName() != '') {
					$userName = $spProfileDetail->getUserFirstName() . ' ' . $spProfileDetail->getUserLastName();
				} else {
					$userName = '';
				}
				if ($spProfileDetail->getCompanyName() != '') {
					$companyName = $spProfileDetail->getCompanyName();
				} else {
					$companyName = '';
				}
				if ($spProfileDetail->getUserProfileImage() != '' && count(
						$spProfileDetail->getUserProfileImage()
					) > 0
				) {

					$profileImg = $this->baseurl() . $spProfileDetail->getUserProfileImage();
				} else {
//                  
					$profileImg = $this->baseurl() . 'defaultprofile.png';
				}

				/*                 * ***********************GAUTAM SIR CHANGES 09-06-2016********************************************** */
				if ($spProfileDetail->getUserSignature() != '') {
					$imagesign = $this->baseurl() . $spProfileDetail->getUserSignature();
				} else {
					$imagesign = '';
				}
				if ($spProfileDetail->getUserFirstName() != '') {
					$userFName = $spProfileDetail->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($spProfileDetail->getUserLastName() != '') {
					$userLName = $spProfileDetail->getUserLastName();
				} else {
					$userLName = '';
				}

				if ($spProfileDetail->getUserDOB() != '') {
					$userDOB = $spProfileDetail->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($spProfileDetail->getUserAddress() != '') {
					$userAddress = $spProfileDetail->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($spProfileDetail->getUserEmail() != '') {
					$userEmail = $spProfileDetail->getUserEmail();
				} else {
					$userEmail = '';
				}

				if ($spProfileDetail->getUserWebsite() != '') {
					$userWeb = 'http://' . $spProfileDetail->getUserWebsite();
				} else {
					$userWeb = '';
				}
				if ($spProfileDetail->getUserBIO() != '') {
					$userBIO = $spProfileDetail->getUserBIO();
				} else {
					$userBIO = '';
				}
				if ($spProfileDetail->getUserMobileNo() != '') {
					$userMobile = $spProfileDetail->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($spProfileDetail->getUserType() != '') {
					$userType = $spProfileDetail->getUserType();
				} else {
					$userType = '';
				}

				$usercustomer = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findBy(
					['companyID' => $userID]
				);
				if ($usercustomer != '' && $usercustomer != null) {
					foreach ($usercustomer as $Customer1Val) {
						if ($Customer1Val->getUserID() != '') {
							$spID[] = $Customer1Val->getUserID();
						} else {
							$spID[] = '';
						}
					}
				} else {

					$spID = '';
				}

				if (count($spID) > 0 && $spID != '') {
					$countspID = count($spID);
				} else {
					$countspID = '';
				}
				/*                 * ***********************GAUTAM SIR CHANGES 09-06-2016********************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);

				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$followers[] = $user_followVal->getUserID();
						} else {
							$followers[] = '';
						}
					}
				} else {

					$followers = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {
					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$following[] = $user_followingVal->getToUserID();
						} else {
							$following[] = '';
						}
					}
				} else {
					$following = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['toUserID' => $userID]
				);


				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRatingVal) {
						if ($userRatingVal->getUserRating() != '') {
							$rating[] = $userRatingVal->getUserRating();
						} else {
							$rating[] = '';
						}
						if ($userRatingVal->getUserReviews() != '') {
							$user_reviews[] = $userRatingVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
						$count = count($rating);
					}


					$rating1 = array_sum($rating) / $count;
					$ratvalues = number_format((float)$rating1, 1, '.', '');

					//die;
				} else {
					$ratvalues = '';
					//$rating[] = '';
					$user_reviews = '';
				}

				$userServices = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
					['userID' => $userID]
				);

				if ($userServices != '' && $userServices != null) {
					foreach ($userServices as $serviceval) {
						if ($serviceval->getServiceID() != '') {

							$user_service = $serviceval->getServiceID();
							$master_service = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $user_service]);
							if ($master_service != '' && $master_service != null) {

								if ($master_service->getServiceName() != '') {
									$getService = $master_service->getServiceName();
								} else {
									$getService = '';
								}
							} else {
								$getService = '';
							}
						}
						if ($serviceval->getServicePrice() != '') {
							$service_price = $serviceval->getServicePrice();
						} else {
							$service_price = '';
						}
						$services[] = array('service_Name' => ($getService), 'service_Price' => $service_price);
					}
				} else {
					$services = [];
				}


				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();

				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();
				//$relatedUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $userID ] || ['userTagID' => $userID ]);
				//  $relatedUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $userID]);

				if ($relatedUser != '' && $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
//                    $spID1[] = $relatedUserVal['userTagID'];
						$post_id[] = $relatedUserVal['postID'];
					}
				} else {
					$post_id = '';
				}


				if ($customerID != '' && $customerID != null) {

//                $userrate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['postID' => $spIDs], array('userRating' => 'desc'));
//            // echo '<pre>';print_r($userrate);
//                if ($userrate != '' && $userrate != null) {
//                    foreach ($userrate as $userrateVal) {
//                        $postData = $userrateVal->getPostID();
//                    }
//                }
					// $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userTagID' => $users], array('id' => 'desc'), 6, 0);
					$relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $post_id, 'spPostStatus' => '0'],
						array('id' => 'desc'),
						6,
						0
					);
// echo '<pre>';print_r($relatedUser1);die;
					if ($relatedUser1 != '' && $relatedUser1 != null) {
						foreach ($relatedUser1 as $relatedUserVal) {
							$album_id = $relatedUserVal->getId();
							$post_status = $relatedUserVal->getPostStatus();
							if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null
							) {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
							} elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
							} elseif ($relatedUserVal->getPostImageLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
							} elseif ($relatedUserVal->getPostImageBackLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
							} elseif ($relatedUserVal->getPostImageBack() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
							} elseif ($relatedUserVal->getPostImageBackRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
							} elseif ($relatedUserVal->getPostImageRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
							} elseif ($relatedUserVal->getPostImageFrontRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
							} else {
								$album_image = '';
							}

							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $album_id]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							if ($relatedUserVal->getUserTagID() != '') {
								$user_ID = $relatedUserVal->getUserTagID();
								$user_ID1 = $relatedUserVal->getUserID();
								$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $user_ID, 'userType' => 0]
								);

								if ($Users != '') {
									$id = $Users->getId();
									if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

										$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID1, 'userType' => 0]
									);
									if ($Users != '' || $Users != null) {
										//echo '<pre>';print_r($Users);die;
										$id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}

									} else {
										$id = '';
										$user_name = '';
									}
								}
								$UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $relatedUserVal->getUserID()]
								);
								if (isset($UserTag) && !empty($UserTag)) {
									if ($UserTag->getUserSignature() == '') {
										if ($UserTag->getCompanyName() != '') {
											$sign_image = $UserTag->getCompanyName();
										} else {
											$sign_image = '';
										}
									} else {
										$sign_image = $this->baseurl() . $UserTag->getUserSignature();
									}
								} else {
									$sign_image = '';
								}


								//die;
							} else {
								$user_ID = '';
							}
							$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $album_id]
							);
							if ($postRating != '' && $postRating != null) {

								if ($postRating->getUserRating() != '') {
									$Rating = $postRating->getUserRating();
								} else {
									$Rating = '';
								}
							} else {
								$Rating = '';
								$tag_status = '0';
							}


							if (!empty($album_image)) {
								$albums_details[] = array(
									'user_id' => $id,
									'post_image' => ($album_image),
									'album_id' => $album_id,
									'user_Name' => ($user_name),
									'tag_status' => $tag_status,
									'avg_rating' => ($Rating),
									'post_sign' => ($sign_image),
									'post_status' => ($post_status)
								);
							}

							//$rate[] =  $Rating;
						}
					} else {
						$albums_details = [];
						$user_ID = '';
						$ratvalues = '';
						$relatedUser = '';
					}
				} else {
					// die('ok');
					//  $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userTagID' => $spID, 'userID' => $userID, 'postStatus' => '0'], array('id' => 'desc'), 6, 0);
					$relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $post_id],
						array('id' => 'desc'),
						6,
						0
					);
					//   echo '<pre>';print_r($relatedUser1);die;
					if ($relatedUser1 != '' && $relatedUser1 != null) {
						foreach ($relatedUser1 as $relatedUserVal) {
							$album_id = $relatedUserVal->getId();
							$post_status = $relatedUserVal->getPostStatus();
							if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null
							) {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
							} elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
							} elseif ($relatedUserVal->getPostImageLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
							} elseif ($relatedUserVal->getPostImageBackLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
							} elseif ($relatedUserVal->getPostImageBack() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
							} elseif ($relatedUserVal->getPostImageBackRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
							} elseif ($relatedUserVal->getPostImageRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
							} elseif ($relatedUserVal->getPostImageFrontRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
							} else {
								$album_image = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $album_id]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							if ($relatedUserVal->getUserTagID() != '') {
								$user_ID = $relatedUserVal->getUserTagID();
								$user_ID1 = $relatedUserVal->getUserID();
								$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $user_ID, 'userType' => 0]
								);

								if ($Users != '') {
									$id = $Users->getId();
									if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

										$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID1, 'userType' => 0]
									);
									if ($Users != '' || $Users != null) {
										//echo '<pre>';print_r($Users);die;
										$id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}

									} else {
										$id = '';
										$user_name = '';
									}
								}
								$UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $relatedUserVal->getUserID()]
								);
								if ($UserTag->getUserSignature() == '') {
									if ($UserTag->getCompanyName() != '') {
										$sign_image = $UserTag->getCompanyName();
									} else {
										$sign_image = '';
									}
								} else {
									$sign_image = $this->baseurl() . $UserTag->getUserSignature();
								}


								//die;
							} else {
								$user_ID = '';
							}
							$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $album_id]
							);
							if ($postRating != '' && $postRating != null) {

								if ($postRating->getUserRating() != '') {
									$Rating = $postRating->getUserRating();
								} else {
									$Rating = '';
								}
							} else {
								$Rating = '';
							}


							if (!empty($album_image)) {
								$albums_details[] = array(
									'user_id' => $id,
									'post_image' => ($album_image),
									'album_id' => $album_id,
									'user_Name' => ($user_name),
									'tag_status' => $tag_status,
									'avg_rating' => ($Rating),
									'post_sign' => ($sign_image),
									'post_status' => ($post_status)
								);
							}

							//$rate[] =  $Rating;
						}
					} else {
						$albums_details = [];
						$user_ID = '';
						$ratvalues = '';
						$relatedUser = '';
					}
				}


				/*        if ($customerID != '' && $customerID != null) {

                     $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['id' => $post_id,'postStatus'=>'1'], array('id' => 'desc'), 6, 0);
                    if ($relatedUser1 != '' && $relatedUser1 != null) {
                        foreach ($relatedUser1 as $relatedUserVal) {
                            $album_id = $relatedUserVal->getId();
                            $post_status = $relatedUserVal->getPostStatus();
                            if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null) {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
                            } elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
                            } elseif ($relatedUserVal->getPostImageLeft() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
                            } elseif ($relatedUserVal->getPostImageBackLeft() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
                            } elseif ($relatedUserVal->getPostImageBack() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
                            } elseif ($relatedUserVal->getPostImageBackRight() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
                            } elseif ($relatedUserVal->getPostImageRight() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
                            } elseif ($relatedUserVal->getPostImageFrontRight() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
                            } else {
                                $album_image = '';
                            }
                            $PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(['postID' => $album_id]);
                            if ($PostTags != '') {
                                foreach ($PostTags as $PostTagsVal) {
                                    if ($PostTagsVal->getTags() != '') {
                                        $tag_status = '1';
                                    } else {
                                        $tag_status = '0';
                                    }
                                }
                            } else {
                                $tag_status = '0';
                            }
                            if ($relatedUserVal->getUserTagID() != '') {
                                $user_ID = $relatedUserVal->getUserTagID();
                                $user_ID1 = $relatedUserVal->getUserID();
                                $Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_ID, 'userType' => 0]);

                                if ($Users != '') {
                                    $id = $Users->getId();
                                    if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

                                        $user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                } else {
                                   $Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_ID1, 'userType' => 0]);
                                   if($Users != '' || $Users != null){
                                    //echo '<pre>';print_r($Users);die;
                                    $id = $Users->getId();
                                    if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

                                        $user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                
                                }
                                else{
                                  $id  = '';  
                                  $user_name = '';
                                }
                                }
                                $UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $relatedUserVal->getUserID()]);
                                if(isset($UserTag) && !empty($UserTag)){
                                if ($UserTag->getUserSignature() == '') {
                                    if ($UserTag->getCompanyName() != '') {
                                        $sign_image = $UserTag->getCompanyName();
                                    } else {
                                        $sign_image = '';
                                    }
                                } else {
                                    $sign_image = $this->baseurl() . $UserTag->getUserSignature();
                                }
                                }else{
                                    $sign_image = '';
                                }


                                //die;
                            } else {
                                $user_ID = '';
                            }
                            $postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(['postID' => $album_id]);
                            if ($postRating != '' && $postRating != null) {

                                if ($postRating->getUserRating() != '') {
                                    $Rating = $postRating->getUserRating();
                                } else {
                                    $Rating = '';
                                }
                            } else {
                                $Rating = '';
                            }



                            if (!empty($album_image)) {
                                $albums_details[] = array('user_id' => $id,
                                    'post_image' => ($album_image),
                                    'album_id' => $album_id,
                                    'user_Name' => ($user_name),
                                    'tag_status' => $tag_status,
                                    'avg_rating' => ($Rating),
                                    'post_sign' => ($sign_image),
                                    'post_status' => ($post_status));
                            }

                            //$rate[] =  $Rating;
                        }
                    } else {
                        $albums_details = [];
                        $user_ID = '';
                    //vivek change in rat
                        //   $ratvalues = '';
                        $relatedUser = '';
                    }
                } else {
                    // die('ok');
//                    $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userTagID' => $spID], array('id' => 'desc'), 6, 0);
                     $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['id' => $post_id,'postStatus'=>'1'], array('id' => 'desc'), 6, 0);
// echo '<pre>';print_r($relatedUser1);die;
                    if ($relatedUser1 != '' && $relatedUser1 != null) {
                        foreach ($relatedUser1 as $relatedUserVal) {
                            $album_id = $relatedUserVal->getId();
                            $post_status = $relatedUserVal->getPostStatus();
                            if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null) {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
                            } elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
                            } elseif ($relatedUserVal->getPostImageLeft() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
                            } elseif ($relatedUserVal->getPostImageBackLeft() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
                            } elseif ($relatedUserVal->getPostImageBack() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
                            } elseif ($relatedUserVal->getPostImageBackRight() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
                            } elseif ($relatedUserVal->getPostImageRight() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
                            } elseif ($relatedUserVal->getPostImageFrontRight() != '') {
                                $album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
                            } else {
                                $album_image = '';
                            }
                            $PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(['postID' => $album_id]);
                            if ($PostTags != '') {
                                foreach ($PostTags as $PostTagsVal) {
                                    if ($PostTagsVal->getTags() != '') {
                                        $tag_status = '1';
                                    } else {
                                        $tag_status = '0';
                                    }
                                }
                            } else {
                                $tag_status = '0';
                            }
                            if ($relatedUserVal->getUserTagID() != '') {
                                $user_ID = $relatedUserVal->getUserTagID();
                                $user_ID1 = $relatedUserVal->getUserID();
                                $Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_ID, 'userType' => 0]);

                                if ($Users != '') {
                                    $id = $Users->getId();
                                    if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

                                        $user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                } else {
                                    $Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_ID1, 'userType' => 0]);
                                   if($Users != '' || $Users != null){
                                    //echo '<pre>';print_r($Users);die;
                                    $id = $Users->getId();
                                    if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

                                        $user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                
                                }
                                else{
                                  $id  = '';  
                                  $user_name = '';
                                }
                                }
                                $UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $relatedUserVal->getUserID()]);
                                if(!empty($UserTag)){
                                if ($UserTag->getUserSignature() == '') {
                                    if ($UserTag->getCompanyName() != '') {
                                        $sign_image = $UserTag->getCompanyName();
                                    } else {
                                        $sign_image = '';
                                    }
                                } else {
                                    $sign_image = $this->baseurl() . $UserTag->getUserSignature();
                                }}else{
                                 $sign_image = '';
                            }


                                //die;
                            } else {
                                $user_ID = '';
                            }
                            $postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(['postID' => $album_id]);
                            if ($postRating != '' && $postRating != null) {

                                if ($postRating->getUserRating() != '') {
                                    $Rating = $postRating->getUserRating();
                                } else {
                                    $Rating = '';
                                }
                            } else {
                                $Rating = '';
                            }



                            if (!empty($album_image)) {
                                $albums_details[] = array('user_id' => $id,
                                    'post_image' => ($album_image),
                                    'album_id' => $album_id,
                                    'user_Name' => ($user_name),
                                    'tag_status' => $tag_status,
                                    'avg_rating' => ($Rating),
                                    'post_sign' => ($sign_image),
                                    'post_status' => ($post_status));
                            }

                            //$rate[] =  $Rating;
                        }
                    } else {
                        $albums_details = [];
                        $user_ID = '';
                       // $ratvalues = '';
                        $relatedUser = '';
                    }
                }*/

				if (count($user_reviews) > 0 && $user_reviews != '') {
					$countReview = count($user_reviews);
				} else {
					$countReview = 0;
				}
				if (count($followers) > 0 && $followers != '') {
					$countFollowers = count($followers);
				} else {
					$countFollowers = 0;
				}
				if (count($following) > 0 && $following != '') {
					$countFollowing = count($following);
				} else {
					$countFollowing = 0;
				}
				if (count($relatedUser) > 0 && $relatedUser != '') {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				//  $userrate
				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $customerID]
				);
				if ($user_status != '') {
					$userFollowStatus = '0';
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					}
				} else {
					$userFollowStatus = '0';
				}
				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID
					,
						'userFirstName' => $userFName,
						'userLastname' => $userLName,
						'userDOB' => $userDOB,
						'user_address' => $userAddress,
						'user_email' => $userEmail,
						'profile_image' => ($profileImg),
						'user_name' => ($userName),
						'company_name' => ($companyName),
						'userwebsite' => $userWeb,
						'userBIO' => $userBIO,
						'userSign' => $imagesign,
						'userMobileNo' => $userMobile,
						'user_type' => $userType,
						'total_post' => $countPost,
						'total_rating' => $ratvalues,
						'total_reviews' => $countReview,
						'total_follower' => $countFollowers,
						'total_following' => $countFollowing,
						'follow_status' => $userFollowStatus,
						'totalcustomer' => $countspID,
						'services' => $services,
						'albums' => $albums_details
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} elseif (($loginType == '0' && $customerID != '') || ($loginType == '0' && $customerID != 'null')) {
			$spProfileDetail = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $userID, 'userType' => '1']
			);

			if ($spProfileDetail != '' && $spProfileDetail != null) {

				if ($spProfileDetail->getUserFirstName() && $spProfileDetail->getUserLastName() != '') {
					$userName = $spProfileDetail->getUserFirstName() . ' ' . $spProfileDetail->getUserLastName();
				} else {
					$userName = '';
				}
				if ($spProfileDetail->getCompanyName() != '') {
					$companyName = $spProfileDetail->getCompanyName();
				} else {
					$companyName = '';
				}
				if ($spProfileDetail->getUserProfileImage() != '' && count(
						$spProfileDetail->getUserProfileImage()
					) > 0
				) {

					$profileImg = $this->baseurl() . $spProfileDetail->getUserProfileImage();
				} else {
//                  
					$profileImg = $this->baseurl() . 'defaultprofile.png';
				}

				/*                 * ***********************GAUTAM SIR CHANGES 09-06-2016********************************************** */
				if ($spProfileDetail->getUserSignature() != '') {
					$imagesign = $this->baseurl() . $spProfileDetail->getUserSignature();
				} else {
					$imagesign = '';
				}
				if ($spProfileDetail->getUserFirstName() != '') {
					$userFName = $spProfileDetail->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($spProfileDetail->getUserLastName() != '') {
					$userLName = $spProfileDetail->getUserLastName();
				} else {
					$userLName = '';
				}

				if ($spProfileDetail->getUserDOB() != '') {
					$userDOB = $spProfileDetail->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($spProfileDetail->getUserAddress() != '') {
					$userAddress = $spProfileDetail->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($spProfileDetail->getUserEmail() != '') {
					$userEmail = $spProfileDetail->getUserEmail();
				} else {
					$userEmail = '';
				}

				if ($spProfileDetail->getUserWebsite() != '') {
					$userWeb = 'http://' . $spProfileDetail->getUserWebsite();
				} else {
					$userWeb = '';
				}
				if ($spProfileDetail->getUserBIO() != '') {
					$userBIO = $spProfileDetail->getUserBIO();
				} else {
					$userBIO = '';
				}
				if ($spProfileDetail->getUserMobileNo() != '') {
					$userMobile = $spProfileDetail->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($spProfileDetail->getUserType() != '') {
					$userType = $spProfileDetail->getUserType();
				} else {
					$userType = '';
				}

				$usercustomer = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findBy(
					['companyID' => $userID]
				);
				if ($usercustomer != '' && $usercustomer != null) {
					foreach ($usercustomer as $Customer1Val) {
						if ($Customer1Val->getUserID() != '') {
							$spID[] = $Customer1Val->getUserID();
						} else {
							$spID[] = '';
						}
					}
				} else {

					$spID = '';
				}

				if (count($spID) > 0 && $spID != '') {
					$countspID = count($spID);
				} else {
					$countspID = '';
				}                /*                 * ***********************GAUTAM SIR CHANGES 09-06-2016********************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);

				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$followers[] = $user_followVal->getUserID();
						} else {
							$followers[] = '';
						}
					}
				} else {

					$followers = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {
					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$following[] = $user_followingVal->getToUserID();
						} else {
							$following[] = '';
						}
					}
				} else {
					$following = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['toUserID' => $userID]
				);

				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRatingVal) {
						if ($userRatingVal->getUserRating() != '') {
							$rating[] = $userRatingVal->getUserRating();
						} else {
							$rating[] = '';
						}
						if ($userRatingVal->getUserReviews() != '') {
							$user_reviews[] = $userRatingVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
						$count = count($rating);
					}


					$rating1 = array_sum($rating) / $count;
					$ratvalues = number_format((float)$rating1, 1, '.', '');

					//die;
				} else {
					$ratvalues = '';
					//$rating[] = '';
					$user_reviews = '';
				}

				$userServices = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
					['userID' => $userID]
				);

				if ($userServices != '' && $userServices != null) {
					foreach ($userServices as $serviceval) {
						if ($serviceval->getServiceID() != '') {

							$user_service = $serviceval->getServiceID();
							$master_service = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $user_service]);
							if ($master_service != '' && $master_service != null) {

								if ($master_service->getServiceName() != '') {
									$getService = $master_service->getServiceName();
								} else {
									$getService = '';
								}
							} else {
								$getService = '';
							}
						}
						if ($serviceval->getServicePrice() != '') {
							$service_price = $serviceval->getServicePrice();
						} else {
							$service_price = '';
						}
						$services[] = array('service_Name' => ($getService), 'service_Price' => $service_price);
					}
				} else {
					$services = [];
				}


				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();

				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();
				//$relatedUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $userID ] || ['userTagID' => $userID ]);
				//  $relatedUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $userID]);
				// echo '<pre>';print_r($relatedUser);die;
				if ($relatedUser != '' && $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
//                    $spID1[] = $relatedUserVal['userTagID'];
						$post_id[] = $relatedUserVal['postID'];
					}
				} else {
					$post_id = '';
				}
				// echo '<pre>';print_r($post_id);die;
//               $albumUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['id' => $post_id]);
//                if ($albumUser != '' && $albumUser != null) {
//                    foreach ($albumUser as $albumUserVal) {
//                        $spID1[] = $albumUserVal->getUserTagID();
//                        $post_id[] = $albumUserVal->getId();
//                    }
//
//
//                    $spID = array_unique($spID1);
//                    $spIDs = '';
//                    $users = '';
//                    for ($i = 0; $i < count($spID1); $i++) {
//                        if (($spID1[$i] != $customerID)) {
//                            $users[] = $spID1[$i];
//                            $spIDs[] = $post_id[$i];
//                        }
//                    }
//                } else {
//                    $spID1 = [];
//                    $post_id = '';
//                    $spIDs = '';
//                    $users = '';
//                    $spID = '';
//                }
////echo '<pre>';print_r($spIDs);die('ok');
//                if (count($spID1) < 1) {
//                    $spIDs = '';
//                    $users = '';
//                }
				if ($customerID != '' && $customerID != null) {

//                $userrate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['postID' => $spIDs], array('userRating' => 'desc'));
//            // echo '<pre>';print_r($userrate);
//                if ($userrate != '' && $userrate != null) {
//                    foreach ($userrate as $userrateVal) {
//                        $postData = $userrateVal->getPostID();
//                    }
//                }
					// $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userTagID' => $users], array('id' => 'desc'), 6, 0);
					$relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $post_id, 'spPostStatus' => '0'],
						array('id' => 'desc'),
						6,
						0
					);
// echo '<pre>';print_r($relatedUser1);die;
					if ($relatedUser1 != '' && $relatedUser1 != null) {
						foreach ($relatedUser1 as $relatedUserVal) {
							$album_id = $relatedUserVal->getId();
							$post_status = $relatedUserVal->getPostStatus();
							if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null
							) {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
							} elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
							} elseif ($relatedUserVal->getPostImageLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
							} elseif ($relatedUserVal->getPostImageBackLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
							} elseif ($relatedUserVal->getPostImageBack() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
							} elseif ($relatedUserVal->getPostImageBackRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
							} elseif ($relatedUserVal->getPostImageRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
							} elseif ($relatedUserVal->getPostImageFrontRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
							} else {
								$album_image = '';
							}

							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $album_id]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							if ($relatedUserVal->getUserTagID() != '') {
								$user_ID = $relatedUserVal->getUserTagID();
								$user_ID1 = $relatedUserVal->getUserID();
								$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $user_ID, 'userType' => 0]
								);

								if ($Users != '') {
									$id = $Users->getId();
									if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

										$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID1, 'userType' => 0]
									);
									if ($Users != '' || $Users != null) {
										//echo '<pre>';print_r($Users);die;
										$id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}

									} else {
										$id = '';
										$user_name = '';
									}
								}
								$UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $relatedUserVal->getUserID()]
								);
								if (isset($UserTag) && !empty($UserTag)) {
									if ($UserTag->getUserSignature() == '') {
										if ($UserTag->getCompanyName() != '') {
											$sign_image = $UserTag->getCompanyName();
										} else {
											$sign_image = '';
										}
									} else {
										$sign_image = $this->baseurl() . $UserTag->getUserSignature();
									}
								} else {
									$sign_image = '';
								}


								//die;
							} else {
								$user_ID = '';
							}
							$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $album_id]
							);
							if ($postRating != '' && $postRating != null) {

								if ($postRating->getUserRating() != '') {
									$Rating = $postRating->getUserRating();
								} else {
									$Rating = '';
								}
							} else {
								$Rating = '';
								$tag_status = '0';
							}


							if (!empty($album_image)) {
								$albums_details[] = array(
									'user_id' => $id,
									'post_image' => ($album_image),
									'album_id' => $album_id,
									'user_Name' => ($user_name),
									'tag_status' => $tag_status,
									'avg_rating' => ($Rating),
									'post_sign' => ($sign_image),
									'post_status' => ($post_status)
								);
							}

							//$rate[] =  $Rating;
						}
					} else {
						$albums_details = [];
						$user_ID = '';
						$ratvalues = '';
						$relatedUser = '';
					}
				} else {
					// die('ok');
					//  $relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userTagID' => $spID, 'userID' => $userID, 'postStatus' => '0'], array('id' => 'desc'), 6, 0);
					$relatedUser1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $post_id],
						array('id' => 'desc'),
						6,
						0
					);
					// echo '<pre>';print_r($relatedUser1);die;
					if ($relatedUser1 != '' && $relatedUser1 != null) {
						foreach ($relatedUser1 as $relatedUserVal) {
							$album_id = $relatedUserVal->getId();
							$post_status = $relatedUserVal->getPostStatus();
							if ($relatedUserVal->getPostImageFront() != '' && $relatedUserVal->getPostImageFront() != null
							) {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFront();
							} elseif ($relatedUserVal->getPostImageFrontLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontLeft();
							} elseif ($relatedUserVal->getPostImageLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageLeft();
							} elseif ($relatedUserVal->getPostImageBackLeft() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackLeft();
							} elseif ($relatedUserVal->getPostImageBack() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBack();
							} elseif ($relatedUserVal->getPostImageBackRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageBackRight();
							} elseif ($relatedUserVal->getPostImageRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageRight();
							} elseif ($relatedUserVal->getPostImageFrontRight() != '') {
								$album_image = $this->baseurl() . $relatedUserVal->getPostImageFrontRight();
							} else {
								$album_image = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $album_id]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							if ($relatedUserVal->getUserTagID() != '') {
								$user_ID = $relatedUserVal->getUserTagID();
								$user_ID1 = $relatedUserVal->getUserID();
								$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $user_ID, 'userType' => 0]
								);

								if ($Users != '') {
									$id = $Users->getId();
									if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

										$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID1, 'userType' => 0]
									);
									if ($Users != '' || $Users != null) {
										//echo '<pre>';print_r($Users);die;
										$id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}

									} else {
										$id = '';
										$user_name = '';
									}
								}
								$UserTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $relatedUserVal->getUserID()]
								);
								if ($UserTag->getUserSignature() == '') {
									if ($UserTag->getCompanyName() != '') {
										$sign_image = $UserTag->getCompanyName();
									} else {
										$sign_image = '';
									}
								} else {
									$sign_image = $this->baseurl() . $UserTag->getUserSignature();
								}


								//die;
							} else {
								$user_ID = '';
							}
							$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $album_id]
							);
							if ($postRating != '' && $postRating != null) {

								if ($postRating->getUserRating() != '') {
									$Rating = $postRating->getUserRating();
								} else {
									$Rating = '';
								}
							} else {
								$Rating = '';
							}


							if (!empty($album_image)) {
								$albums_details[] = array(
									'user_id' => $id,
									'post_image' => ($album_image),
									'album_id' => $album_id,
									'user_Name' => ($user_name),
									'tag_status' => $tag_status,
									'avg_rating' => ($Rating),
									'post_sign' => ($sign_image),
									'post_status' => ($post_status)
								);
							}

							//$rate[] =  $Rating;
						}
					} else {
						$albums_details = [];
						$user_ID = '';
						$ratvalues = '';
						$relatedUser = '';
					}
				}

				if (count($user_reviews) > 0 && $user_reviews != '') {
					$countReview = count($user_reviews);
				} else {
					$countReview = 0;
				}
				if (count($followers) > 0 && $followers != '') {
					$countFollowers = count($followers);
				} else {
					$countFollowers = 0;
				}
				if (count($following) > 0 && $following != '') {
					$countFollowing = count($following);
				} else {
					$countFollowing = 0;
				}
				if (count($relatedUser) > 0 && $relatedUser != '') {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				//  $userrate
				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $customerID]
				);
				if ($user_status != '') {
					$userFollowStatus = '0';
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					}
				} else {
					$userFollowStatus = '0';
				}
				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID
					,
						'userFirstName' => $userFName,
						'userLastname' => $userLName,
						'userDOB' => $userDOB,
						'user_address' => $userAddress,
						'user_email' => $userEmail,
						'profile_image' => ($profileImg),
						'user_name' => ($userName),
						'company_name' => ($companyName),
						'userwebsite' => $userWeb,
						'userBIO' => $userBIO,
						'userSign' => $imagesign,
						'userMobileNo' => $userMobile,
						'user_type' => $userType,
						'total_post' => $countPost,
						'total_rating' => $ratvalues,
						'total_reviews' => $countReview,
						'total_follower' => $countFollowers,
						'total_following' => $countFollowing,
						'follow_status' => $userFollowStatus,
						'totalcustomer' => $countspID,
						'services' => $services,
						'albums' => $albums_details
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		}
	}

	/*     * *****************************************************************************Service provider profile Details End********** */

	/**
	 * @Route("/customers", name="_customers")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function customerAction(Request $user_id, Request $customer_id)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$customerID = $request->get('customer_id');

		$usercustomer = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findBy(
			['companyID' => $userID]
		);

// $spID = array();
		if ($usercustomer != '' && $usercustomer != null) {
			foreach ($usercustomer as $Customer1Val) {
				$spID[] = $Customer1Val->getUserID();
			}


			$spIDs = '';

			for ($i = 0; $i < count($spID); $i++) {
				if (($spID[$i] != $customerID)) {

					$spIDs[] = $spID[$i];
				}
			}
//echo '<pre>';print_r($spIDs);die;

			if (count($spID) < 1) {
				$spIDs = '';
			}

			if ($customerID > 0) {

				$userDetail = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $spIDs, 'userType' => '0']
				);

				if ($userDetail != '' & $userDetail != null) {
					foreach ($userDetail as $userDetailVal) {
						if (($userDetailVal->getUserFirstName() || $userDetailVal->getUserLastName()) != '') {
							$userName = $userDetailVal->getUserFirstName() . ' ' . $userDetailVal->getUserLastName();
						} else {
							$userName = '';
						}
						if ($userDetailVal->getId() != '') {
							$userIDs = $userDetailVal->getId();
						} else {
							$userIDs = '';
						}

						if ($userDetailVal->getUserProfileImage() != '' && count(
								$userDetailVal->getUserProfileImage()
							) > 0
						) {
							$userprofile = $this->baseurl() . $userDetailVal->getUserProfileImage();
						} else {
							$userprofile = $this->baseurl() . 'defaultprofile.png';
						}


						$customer_details[] = array(
							'user_id' => $userIDs,
							'user_name' => ($userName),
							'user_profile' => ($userprofile),
							'service_name' => ''
						);
					}

					echo json_encode(
						array('success' => 1, 'message' => 'successfull', 'cutomers' => $customer_details)
					);
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {

				$userDetail = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $spID, 'userType' => '0']
				);
				//echo '<pre>';print_r($userDetail);die('ok');
				if ($userDetail != '' & $userDetail != null) {
					foreach ($userDetail as $userDetailVal) {
						if (($userDetailVal->getUserFirstName() || $userDetailVal->getUserLastName()) != '') {
							$userName = $userDetailVal->getUserFirstName() . ' ' . $userDetailVal->getUserLastName();
						} else {
							$userName = '';
						}
						if ($userDetailVal->getId() != '') {
							$userIDs = $userDetailVal->getId();
						} else {
							$userIDs = '';
						}

						if ($userDetailVal->getUserProfileImage() != '' && count(
								$userDetailVal->getUserProfileImage()
							) > 0
						) {
							$userprofile = $this->baseurl() . $userDetailVal->getUserProfileImage();
						} else {
							$userprofile = $this->baseurl() . 'defaultprofile.png';
						}


						$customer_details[] = array(
							'user_id' => $userIDs,
							'user_name' => ($userName),
							'user_profile' => ($userprofile),
							'service_name' => ''
						);
					}

					echo json_encode(
						array('success' => 1, 'message' => 'successfull', 'cutomers' => $customer_details)
					);
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/follow", name="_follow")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function followAction(Request $to_user_id, Request $from_user_id, Request $follow_status)
	{

		$request = $this->getRequest();
		$fromuserID = $request->get('from_user_id');
		$touserID = $request->get('to_user_id');

		$userFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
			['userID' => $fromuserID, 'toUserID' => $touserID]
		);

		if ($userFollow != '' && $userFollow != null) {

			$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $fromuserID]);
			if ($user != '' && $user != null) {
				if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
					$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
				} else {
					$userName = '';
				}
			} else {
				$userName = '';
			}


			if ($request->get('follow_status') == '1') {


				foreach ($userFollow as $followVal) {
					$followVal->setFollowStatus($request->get('follow_status'));
					$em = $this->getDoctrine()->getManager();
					$em->persist($followVal);
					$em->flush();
				}
				/* NOTIFICATION FUNCTION START */
				$UserModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $touserID]);
				if ($UserModel != '') {
					if ($UserModel->getIsNotification() == '1') {
						$msg = ('follow');
						$IDs = $fromuserID;
						$submsg = ($userName . ' ' . 'followed you');
						$usenotification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findBy(
							['userID' => $touserID]
						);

						if ($usenotification != '' && $usenotification != null) {
							//die('ok');
							foreach ($usenotification as $usenotificationVal) {
								if ($usenotificationVal->getDeviceType() == '0') {

									$registatoin_ids = ($usenotificationVal->getDeviceID());

								}
								$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
							}
						}

						$notificationMsg = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:NotificationMessage"
						)->findOneBy(
							['userID' => $fromuserID, 'toUserID' => $touserID, 'notificationTitle' => ('follow')]
						);
						// echo '<pre>';print_r('anoopsing');
						if ($notificationMsg == '') {
							//  die('anoopasa');
							$notifyMsg = new NotificationMessage();
							$notifyMsg->setNotificationTitle($msg);
							$notifyMsg->setNotificationMessage($submsg);
							$notifyMsg->setUserID($IDs);
							$notifyMsg->setToUserID($touserID);
							$em = $this->getDoctrine()->getManager();
							$em->persist($notifyMsg);
							$em->flush();
						} else {

							$em = $this->getDoctrine()->getEntityManager();
							$em->remove($notificationMsg);
							$em->flush();

							$notifyMsg = new NotificationMessage();
							$notifyMsg->setNotificationTitle($msg);
							$notifyMsg->setNotificationMessage($submsg);
							$notifyMsg->setUserID($IDs);
							$notifyMsg->setToUserID($touserID);
							$em = $this->getDoctrine()->getManager();
							$em->persist($notifyMsg);
							$em->flush();
						}

					}
				}

				/* NOTIFICATION FUNCTION END */

				echo json_encode(array('success' => 1, 'message' => 'successfully followed'));
			} else {
				if ($request->get('follow_status') == '0') {
					foreach ($userFollow as $followVal) {
						$followVal->setFollowStatus($request->get('follow_status'));
						$em = $this->getDoctrine()->getManager();
						$em->persist($followVal);
						$em->flush();
					}
					$NotificationModel = $this->getDoctrine()->getRepository(
						"AcmeDemoBundle:NotificationMessage"
					)->findOneBy(['userID' => $fromuserID, 'toUserID' => $touserID, 'notificationTitle' => ('follow')]);
					if ($NotificationModel != '' && $NotificationModel != null) {
						$em = $this->getDoctrine()->getManager();
						$em->remove($NotificationModel);
						$em->flush();
					}

					echo json_encode(array('success' => 1, 'message' => 'successfully unfollowed'));
				}
			}
		} else {
			if ($request->get('follow_status') == '0') {
				$user_follow = new UserFollow();
				$user_follow->setUserID($request->get('from_user_id'));
				$user_follow->setToUserID($request->get('to_user_id'));
				$user_follow->setFollowStatus($request->get('follow_status'));
				$em = $this->getDoctrine()->getManager();
				$em->persist($user_follow);
				$em->flush();
				$NotificationModel = $this->getDoctrine()->getRepository(
					"AcmeDemoBundle:NotificationMessage"
				)->findOneBy(['userID' => $fromuserID, 'toUserID' => $touserID, 'notificationTitle' => ('follow')]);
				if ($NotificationModel != '' && $NotificationModel != null) {
					$em = $this->getDoctrine()->getManager();
					$em->remove($NotificationModel);
					$em->flush();
				}
				echo json_encode(array('success' => 1, 'message' => 'successfully unfollowed'));
			} else {
				if ($request->get('follow_status') == '1') {
					$user_follow = new UserFollow();
					$user_follow->setUserID($request->get('from_user_id'));
					$user_follow->setToUserID($request->get('to_user_id'));
					$user_follow->setFollowStatus($request->get('follow_status'));
					$em = $this->getDoctrine()->getManager();
					$em->persist($user_follow);
					$em->flush();
					if ($request->get('follow_status') == '1') {
						foreach ($userFollow as $followVal) {
							$followVal->setFollowStatus($request->get('follow_status'));
							$em = $this->getDoctrine()->getManager();
							$em->persist($followVal);
							$em->flush();
						}
						/* NOTIFICATION FUNCTION START */
						$UserModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $touserID]
						);
						if ($UserModel != '') {
							if ($UserModel->getIsNotification() == '1') {

								$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $fromuserID]
								);
								if ($user != '' && $user != null) {
									if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
										$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
									} else {
										$userName = '';
									}
								} else {
									$userName = '';
								}

								$msg = ('follow');
								$IDs = $fromuserID;
								$submsg = ($userName . ' ' . 'followed you');
								$usenotification = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:Notification"
								)->findBy(['userID' => $touserID]);

								if ($usenotification != '' && $usenotification != null) {
									foreach ($usenotification as $usenotificationVal) {
										if ($usenotificationVal->getDeviceType() == '0') {

											$registatoin_ids = ($usenotificationVal->getDeviceID());

										}
										$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
									}
								}
								$notificationMsg = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:NotificationMessage"
								)->findOneBy(
									[
										'userID' => $fromuserID,
										'toUserID' => $touserID,
										'notificationTitle' => ('follow')
									]
								);

								if ($notificationMsg == '' && $notificationMsg == null) {

									$notifyMsg = new NotificationMessage();
									$notifyMsg->setNotificationTitle($msg);
									$notifyMsg->setNotificationMessage($submsg);
									$notifyMsg->setUserID($IDs);
									$notifyMsg->setToUserID($touserID);
									$em = $this->getDoctrine()->getManager();
									$em->persist($notifyMsg);
									$em->flush();
								} else {
									$em = $this->getDoctrine()->getEntityManager();
									$em->remove($notificationMsg);
									$em->flush();
									$notifyMsg = new NotificationMessage();
									$notifyMsg->setNotificationTitle($msg);
									$notifyMsg->setNotificationMessage($submsg);
									$notifyMsg->setUserID($IDs);
									$notifyMsg->setToUserID($touserID);
									$em = $this->getDoctrine()->getManager();
									$em->persist($notifyMsg);
									$em->flush();
								}

							}
						}
						/* NOTIFICATION FUNCTION END */
						echo json_encode(array('success' => 1, 'message' => 'successfully followed'));
					}
				}
			}
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/consumerprofile", name="_consumerprofile")
	 * @Template()
	 */
	/*     * ************************************************************************consumer Profile Begin ******************************************* */

	public function consumerprofileAction(Request $user_id, Request $sp_id, Request $login_type)
	{

		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$serviceID = $request->get('sp_id');
		$loginType = $request->get('login_type');
		if (($serviceID == '' && $loginType == '') || ($serviceID == 'null' && $loginType == 'null')) {
			$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

			if ($user != '' && $user != null && $user->getUserType() == 0) {

				if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
					$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
				} else {
					$userName = '';
				}
				if ($user->getUserProfileImage() == '') {
					$user_profile = $this->baseurl() . 'defaultprofile.png';
				} else {
					$user_profile = $this->baseurl() . $user->getUserProfileImage();
				}
				/*                 * *********************Gautam sir changes**********09-06-2016************************************ */
				if ($user->getUserFirstName() != '') {
					$userFName = $user->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($user->getUserLastName() != '') {
					$userLName = $user->getUserLastName();
				} else {
					$userLName = '';
				}
				if ($user->getUserMobileNo() != '') {
					$userMobile = $user->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($user->getUserDOB() != '') {
					$userDOB = $user->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($user->getUserEmail() != '') {
					$userEmail = $user->getUserEmail();
				} else {
					$userEmail = '';
				}
				if ($user->getUserGender() != '') {
					$userGender = $user->getUserGender();
				} else {
					$userGender = '';
				}
				if ($user->getUserAddress() != '') {
					$userAddress = $user->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($user->getIsNotification() != '') {
					$userNotification = $user->getIsNotification();
				} else {
					$userNotification = '';
				}
				/*                 * *********************Gautam sir changes***END**09-06-2016***************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);
//echo '<pre>';print_r($user_follow);die;
				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$userfollow[] = $user_followVal->getUserID();
						} else {
							$userfollow[] = '';
						}
					}
				} else {
					$userfollow = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {

					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$userfollowing[] = $user_followingVal->getToUserID();
						} else {
							$userfollowing[] = '';
						}
					}
				} else {
					$userfollowing = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['fromUserID' => $userID]
				);
				//echo '<pre>';print_r($userRating);die;
				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRateVal) {
						if ($userRateVal->getUserReviews() != '') {
							$user_reviews[] = $userRateVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
					}
				} else {

					$user_reviews = '';
				}
				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();

				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();
				if ($relatedUser != '' || $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
						$postIDs[] = $relatedUserVal['postID'];
					}
				} else {
					$postIDs = '';
				}
				$userAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $postIDs, 'postStatus' => '0', 'spPostStatus' => '0'],
					array('id' => 'DESC'),
					6,
					0
				);
//echo '<pre>';print_r($userAlbum);die;
				if ($userAlbum != '' && $userAlbum != null) {
					foreach ($userAlbum as $userAlbumVal) {
						$post_status = $userAlbumVal->getPostStatus();
						if ($userAlbumVal->getPostCaption() != '') {
							$serviceName = $userAlbumVal->getPostCaption();
						} else {
							$serviceName = '';
						}
						$user_id = $userAlbumVal->getUserID();
						$user_id1 = $userAlbumVal->getUserTagID();
						if ($userAlbumVal->getId() != '') {
							$userPost = $userAlbumVal->getId();
						} else {
							$userPost = '';
						}
						if ($user_id == $user_id1) {
							$user_iD = '';
							$user_name = '';
						} else {
							$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id]
							);
							if (!empty($users)) {
								if ($users->getUserType() == '1') {
									$user_iD = $users->getId();
									if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
										$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_id1]
									);
									if (!empty($users)) {
										if ($users->getUserType() == '1') {
											$user_iD = $users->getId();
											if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
												$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$user_iD = '';
											$user_name = '';
										}
									} else {
										$user_iD = '';
										$user_name = '';
									}
								}
							} else {
								$user_iD = '';
								$user_name = '';
							}
						}
						if ($userAlbumVal->getPostImageFront() != '' && $userAlbumVal->getPostImageFront() != null) {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFront();
						} elseif ($userAlbumVal->getPostImageFrontLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontLeft();
						} elseif ($userAlbumVal->getPostImageLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageLeft();
						} elseif ($userAlbumVal->getPostImageBackLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackLeft();
						} elseif ($userAlbumVal->getPostImageBack() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBack();
						} elseif ($userAlbumVal->getPostImageBackRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackRight();
						} elseif ($userAlbumVal->getPostImageRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageRight();
						} elseif ($userAlbumVal->getPostImageFrontRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontRight();
						} else {
							$userPostImage = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $userPost]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $userPost]
						);
//echo '<pre>';print_r($postRating);die;
						if ($postRating != '' && $postRating != null) {

							if ($postRating->getUserRating() != '') {
								$Rating = $postRating->getUserRating();
							} else {
								$Rating = '';
							}
						} else {
							$Rating = '';
						}

						$Album[] = array(
							'post_id' => $userPost,
							'tag_status' => $tag_status,
							'user_post' => ($userPostImage),
							'service_name' => ($serviceName),
							'avg_rating' => $Rating,
							'post_status' => ($post_status),
							'user_id' => $user_iD,
							'user_name' => $user_name
						);

					}
				} else {
					$Album = [];
					$userPost = '';
				}
				if ((count($userfollow)) > 0 && $userfollow != null) {
					$countFollowers = count($userfollow);
				} else {
					$countFollowers = 0;
				}
				if ((count($user_reviews)) > 0 && $user_reviews != null) {
					$countReviews = count($user_reviews);
				} else {
					$countReviews = 0;
				}
				if ((count($relatedUser)) > 0 && $relatedUser != null) {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				if ((count($userfollowing)) > 0 && $userfollowing != null) {
					$countFollowing = count($userfollowing);
				} else {
					$countFollowing = 0;
				}

				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $serviceID]
				);
				if ($user_status != '') {
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					} else {
						$userFollowStatus = '0';
					}
				} else {
					$userFollowStatus = '0';
				}
				//  echo '<pre>';print_r($Album);die;
				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID,
						'user_name' => ($userName),
						'user_fname' => $userFName,
						'user_lname' => $userLName,
						'user_contact' => $userMobile,
						'user_DOB' => $userDOB,
						'user_email' => $userEmail,
						'user_gender' => $userGender,
						'user_address' => $userAddress,
						'hide_info' => $userNotification,
						'user_profile_image' => ($user_profile),
						'user_reviews' => $countReviews,
						'user_followers' => $countFollowers,
						'follow_status' => $userFollowStatus,
						'total_post' => $countPost,
						'user_followings' => $countFollowing,
						'consumer_post' => $Album
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} elseif (($loginType == '0' && $serviceID == '') || ($loginType == '0' && $serviceID == 'null')) {
			$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
			if ($user != '' && $user != null && $user->getUserType() == 0) {

				if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
					$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
				} else {
					$userName = '';
				}
				if ($user->getUserProfileImage() == '') {
					$user_profile = $this->baseurl() . 'defaultprofile.png';
				} else {
					$user_profile = $this->baseurl() . $user->getUserProfileImage();
				}
				/*                 * *********************Gautam sir changes**********09-06-2016************************************ */
				if ($user->getUserFirstName() != '') {
					$userFName = $user->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($user->getUserLastName() != '') {
					$userLName = $user->getUserLastName();
				} else {
					$userLName = '';
				}
				if ($user->getUserMobileNo() != '') {
					$userMobile = $user->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($user->getUserDOB() != '') {
					$userDOB = $user->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($user->getUserEmail() != '') {
					$userEmail = $user->getUserEmail();
				} else {
					$userEmail = '';
				}
				if ($user->getUserGender() != '') {
					$userGender = $user->getUserGender();
				} else {
					$userGender = '';
				}
				if ($user->getUserAddress() != '') {
					$userAddress = $user->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($user->getIsNotification() != '') {
					$userNotification = $user->getIsNotification();
				} else {
					$userNotification = '';
				}
				/*                 * *********************Gautam sir changes***END**09-06-2016***************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);
//echo '<pre>';print_r($user_follow);die;
				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$userfollow[] = $user_followVal->getUserID();
						} else {
							$userfollow[] = '';
						}
					}
				} else {
					$userfollow = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {

					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$userfollowing[] = $user_followingVal->getToUserID();
						} else {
							$userfollowing[] = '';
						}
					}
				} else {
					$userfollowing = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['fromUserID' => $userID]
				);
				//echo '<pre>';print_r($userRating);die;
				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRateVal) {
						if ($userRateVal->getUserReviews() != '') {
							$user_reviews[] = $userRateVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
					}
				} else {

					$user_reviews = '';
				}

				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();
				$postIDs = '';
				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();
				if ($relatedUser != '' || $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
						$postIDs[] = $relatedUserVal['postID'];
					}
				} else {
					$postIDs = '';
				}
				$userAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $postIDs],
					array('id' => 'DESC'),
					6,
					0
				);
//echo '<pre>';print_R($userAlbum);
				if ($userAlbum != '' && $userAlbum != null) {
					foreach ($userAlbum as $userAlbumVal) {
						$post_status = $userAlbumVal->getPostStatus();
						if ($userAlbumVal->getPostCaption() != '') {
							$serviceName = $userAlbumVal->getPostCaption();
						} else {
							$serviceName = '';
						}
						$user_id = $userAlbumVal->getUserID();
						$user_id1 = $userAlbumVal->getUserTagID();
						if ($user_id == $user_id1) {
							$user_iD = '';
							$user_name = '';
						} else {
							$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id]
							);
							if (!empty($users)) {
								if ($users->getUserType() == '1') {
									$user_iD = $users->getId();
									if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
										$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_id1]
									);
									if (!empty($users)) {
										if ($users->getUserType() == '1') {
											$user_iD = $users->getId();
											if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
												$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$user_iD = '';
											$user_name = '';
										}
									} else {
										$user_iD = '';
										$user_name = '';
									}
								}
							} else {
								$user_iD = '';
								$user_name = '';
							}
						}
						if ($userAlbumVal->getId() != '') {
							$userPost = $userAlbumVal->getId();
						} else {
							$userPost = '';
						}
						if ($userAlbumVal->getPostImageFront() != '' && $userAlbumVal->getPostImageFront() != null) {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFront();
						} elseif ($userAlbumVal->getPostImageFrontLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontLeft();
						} elseif ($userAlbumVal->getPostImageLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageLeft();
						} elseif ($userAlbumVal->getPostImageBackLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackLeft();
						} elseif ($userAlbumVal->getPostImageBack() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBack();
						} elseif ($userAlbumVal->getPostImageBackRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackRight();
						} elseif ($userAlbumVal->getPostImageRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageRight();
						} elseif ($userAlbumVal->getPostImageFrontRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontRight();
						} else {
							$userPostImage = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $userPost]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $userPost]
						);
//echo '<pre>';print_r($postRating);die;
						if ($postRating != '' && $postRating != null) {

							if ($postRating->getUserRating() != '') {
								$Rating = $postRating->getUserRating();
							} else {
								$Rating = '';
							}
						} else {
							$Rating = '';
						}

						$Album[] = array(
							'post_id' => $userPost,
							'tag_status' => $tag_status,
							'user_post' => ($userPostImage),
							'service_name' => ($serviceName),
							'avg_rating' => $Rating,
							'post_status' => ($post_status),
							'user_id' => $user_iD,
							'user_name' => $user_name
						);

					}
				} else {
					$Album = [];
					$userPost = '';
				}
				if ((count($userfollow)) > 0 && $userfollow != null) {
					$countFollowers = count($userfollow);
				} else {
					$countFollowers = 0;
				}
				if ((count($user_reviews)) > 0 && $user_reviews != null) {
					$countReviews = count($user_reviews);
				} else {
					$countReviews = 0;
				}
				if ((count($relatedUser)) > 0 && $relatedUser != null) {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				if ((count($userfollowing)) > 0 && $userfollowing != null) {
					$countFollowing = count($userfollowing);
				} else {
					$countFollowing = 0;
				}

				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $serviceID]
				);
				if ($user_status != '') {
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					} else {
						$userFollowStatus = '0';
					}
				} else {
					$userFollowStatus = '0';
				}

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID,
						'user_name' => ($userName),
						'user_fname' => $userFName,
						'user_lname' => $userLName,
						'user_contact' => $userMobile,
						'user_DOB' => $userDOB,
						'user_email' => $userEmail,
						'user_gender' => $userGender,
						'user_address' => $userAddress,
						'hide_info' => $userNotification,
						'user_profile_image' => ($user_profile),
						'user_reviews' => $countReviews,
						'user_followers' => $countFollowers,
						'follow_status' => $userFollowStatus,
						'total_post' => $countPost,
						'user_followings' => $countFollowing,
						'consumer_post' => $Album
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} elseif (($loginType == '1' && $serviceID != '') || ($loginType == '1' && $serviceID != 'null')) {
			$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
			if ($user != '' && $user != null && $user->getUserType() == 0) {

				if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
					$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
				} else {
					$userName = '';
				}
				if ($user->getUserProfileImage() == '') {
					$user_profile = $this->baseurl() . 'defaultprofile.png';
				} else {
					$user_profile = $this->baseurl() . $user->getUserProfileImage();
				}
				/*                 * *********************Gautam sir changes**********09-06-2016************************************ */
				if ($user->getUserFirstName() != '') {
					$userFName = $user->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($user->getUserLastName() != '') {
					$userLName = $user->getUserLastName();
				} else {
					$userLName = '';
				}
				if ($user->getUserMobileNo() != '') {
					$userMobile = $user->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($user->getUserDOB() != '') {
					$userDOB = $user->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($user->getUserEmail() != '') {
					$userEmail = $user->getUserEmail();
				} else {
					$userEmail = '';
				}
				if ($user->getUserGender() != '') {
					$userGender = $user->getUserGender();
				} else {
					$userGender = '';
				}
				if ($user->getUserAddress() != '') {
					$userAddress = $user->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($user->getIsNotification() != '') {
					$userNotification = $user->getIsNotification();
				} else {
					$userNotification = '';
				}
				/*                 * *********************Gautam sir changes***END**09-06-2016***************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);
//echo '<pre>';print_r($user_follow);die;
				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$userfollow[] = $user_followVal->getUserID();
						} else {
							$userfollow[] = '';
						}
					}
				} else {
					$userfollow = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {

					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$userfollowing[] = $user_followingVal->getToUserID();
						} else {
							$userfollowing[] = '';
						}
					}
				} else {
					$userfollowing = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['fromUserID' => $userID]
				);
				//echo '<pre>';print_r($userRating);die;
				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRateVal) {
						if ($userRateVal->getUserReviews() != '') {
							$user_reviews[] = $userRateVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
					}
				} else {

					$user_reviews = '';
				}
				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();
				$postIDs = '';
				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();
				if ($relatedUser != '' || $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
						$postIDs[] = $relatedUserVal['postID'];
					}
				} else {
					$postIDs = '';
				}
				$userAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $postIDs],
					array('id' => 'DESC'),
					6,
					0
				);
//echo '<pre>';print_R($userAlbum);
				if ($userAlbum != '' && $userAlbum != null) {
					foreach ($userAlbum as $userAlbumVal) {
						$post_status = $userAlbumVal->getPostStatus();
						if ($userAlbumVal->getPostCaption() != '') {
							$serviceName = $userAlbumVal->getPostCaption();
						} else {
							$serviceName = '';
						}
						$user_id1 = $userAlbumVal->getUserTagID();
						$user_id = $userAlbumVal->getUserID();
						if ($userAlbumVal->getId() != '') {
							$userPost = $userAlbumVal->getId();
						} else {
							$userPost = '';
						}
						if ($user_id == $user_id1) {
							$user_iD = '';
							$user_name = '';
						} else {
							$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id]
							);
							if (!empty($users)) {
								if ($users->getUserType() == '1') {
									$user_iD = $users->getId();
									if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
										$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_id1]
									);
									if (!empty($users)) {
										if ($users->getUserType() == '1') {
											$user_iD = $users->getId();
											if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
												$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$user_iD = '';
											$user_name = '';
										}
									} else {
										$user_iD = '';
										$user_name = '';
									}
								}
							} else {
								$user_iD = '';
								$user_name = '';
							}
						}
						if ($userAlbumVal->getPostImageFront() != '' && $userAlbumVal->getPostImageFront() != null) {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFront();
						} elseif ($userAlbumVal->getPostImageFrontLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontLeft();
						} elseif ($userAlbumVal->getPostImageLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageLeft();
						} elseif ($userAlbumVal->getPostImageBackLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackLeft();
						} elseif ($userAlbumVal->getPostImageBack() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBack();
						} elseif ($userAlbumVal->getPostImageBackRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackRight();
						} elseif ($userAlbumVal->getPostImageRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageRight();
						} elseif ($userAlbumVal->getPostImageFrontRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontRight();
						} else {
							$userPostImage = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $userPost]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $userPost]
						);
//echo '<pre>';print_r($postRating);die;
						if ($postRating != '' && $postRating != null) {

							if ($postRating->getUserRating() != '') {
								$Rating = $postRating->getUserRating();
							} else {
								$Rating = '';
							}
						} else {
							$Rating = '';
						}

						$Album[] = array(
							'post_id' => $userPost,
							'tag_status' => $tag_status,
							'user_post' => ($userPostImage),
							'service_name' => ($serviceName),
							'avg_rating' => $Rating,
							'post_status' => ($post_status),
							'user_id' => $user_iD,
							'user_name' => $user_name
						);

					}
				} else {
					$Album = [];
					$userPost = '';
				}
				if ((count($userfollow)) > 0 && $userfollow != null) {
					$countFollowers = count($userfollow);
				} else {
					$countFollowers = 0;
				}
				if ((count($user_reviews)) > 0 && $user_reviews != null) {
					$countReviews = count($user_reviews);
				} else {
					$countReviews = 0;
				}
				if ((count($relatedUser)) > 0 && $relatedUser != null) {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				if ((count($userfollowing)) > 0 && $userfollowing != null) {
					$countFollowing = count($userfollowing);
				} else {
					$countFollowing = 0;
				}

				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $serviceID]
				);
				if ($user_status != '') {
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					} else {
						$userFollowStatus = '0';
					}
				} else {
					$userFollowStatus = '0';
				}

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID,
						'user_name' => ($userName),
						'user_fname' => $userFName,
						'user_lname' => $userLName,
						'user_contact' => $userMobile,
						'user_DOB' => $userDOB,
						'user_email' => $userEmail,
						'user_gender' => $userGender,
						'user_address' => $userAddress,
						'hide_info' => $userNotification,
						'user_profile_image' => ($user_profile),
						'user_reviews' => $countReviews,
						'user_followers' => $countFollowers,
						'follow_status' => $userFollowStatus,
						'total_post' => $countPost,
						'user_followings' => $countFollowing,
						'consumer_post' => $Album
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} elseif (($loginType == '0' && $serviceID != '') || ($loginType == '0' && $serviceID != 'null')) {
			$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
			if ($user != '' && $user != null && $user->getUserType() == 0) {

				if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
					$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
				} else {
					$userName = '';
				}
				if ($user->getUserProfileImage() == '') {
					$user_profile = $this->baseurl() . 'defaultprofile.png';
				} else {
					$user_profile = $this->baseurl() . $user->getUserProfileImage();
				}
				/*                 * *********************Gautam sir changes**********09-06-2016************************************ */
				if ($user->getUserFirstName() != '') {
					$userFName = $user->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($user->getUserLastName() != '') {
					$userLName = $user->getUserLastName();
				} else {
					$userLName = '';
				}
				if ($user->getUserMobileNo() != '') {
					$userMobile = $user->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($user->getUserDOB() != '') {
					$userDOB = $user->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($user->getUserEmail() != '') {
					$userEmail = $user->getUserEmail();
				} else {
					$userEmail = '';
				}
				if ($user->getUserGender() != '') {
					$userGender = $user->getUserGender();
				} else {
					$userGender = '';
				}
				if ($user->getUserAddress() != '') {
					$userAddress = $user->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($user->getIsNotification() != '') {
					$userNotification = $user->getIsNotification();
				} else {
					$userNotification = '';
				}
				/*                 * *********************Gautam sir changes***END**09-06-2016***************************************** */


				$user_follow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['toUserID' => $userID, 'followStatus' => '1']
				);
//echo '<pre>';print_r($user_follow);die;
				if ($user_follow != '' && $user_follow != null) {
					foreach ($user_follow as $user_followVal) {
						if ($user_followVal->getUserID() != '') {
							$userfollow[] = $user_followVal->getUserID();
						} else {
							$userfollow[] = '';
						}
					}
				} else {
					$userfollow = '';
				}

				$user_following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['userID' => $userID, 'followStatus' => '1']
				);

				if ($user_following != '' && $user_following != null) {

					foreach ($user_following as $user_followingVal) {
						if ($user_followingVal->getToUserID() != '') {
							$userfollowing[] = $user_followingVal->getToUserID();
						} else {
							$userfollowing[] = '';
						}
					}
				} else {
					$userfollowing = '';
				}

				$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
					['fromUserID' => $userID]
				);
				//echo '<pre>';print_r($userRating);die;
				if ($userRating != '' && $userRating != null) {
					foreach ($userRating as $userRateVal) {
						if ($userRateVal->getUserReviews() != '') {
							$user_reviews[] = $userRateVal->getUserReviews();
						} else {
							$user_reviews[] = '';
						}
					}
				} else {

					$user_reviews = '';
				}
				$manager = $this->getDoctrine()->getManager();
				$conn = $manager->getConnection();
				$postIDs = '';
				$relatedUser = $conn->query(
					"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
				)->fetchAll();
				if ($relatedUser != '' || $relatedUser != null) {
					foreach ($relatedUser as $relatedUserVal) {
						$postIDs[] = $relatedUserVal['postID'];
					}
				} else {
					$postIDs = '';
				}
				$userAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $postIDs, 'spPostStatus' => '0'],
					array('id' => 'DESC'),
					6,
					0
				);
//echo '<pre>';print_R($userAlbum);
				if ($userAlbum != '' && $userAlbum != null) {
					foreach ($userAlbum as $userAlbumVal) {
						$post_status = $userAlbumVal->getPostStatus();
						if ($userAlbumVal->getPostCaption() != '') {
							$serviceName = $userAlbumVal->getPostCaption();
						} else {
							$serviceName = '';
						}

						$user_id1 = $userAlbumVal->getUserTagID();
						$user_id = $userAlbumVal->getUserID();
						if ($userAlbumVal->getId() != '') {
							$userPost = $userAlbumVal->getId();
						} else {
							$userPost = '';
						}
						if ($user_id == $user_id1) {
							$user_iD = '';
							$user_name = '';
						} else {
							$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id]
							);
							if (!empty($users)) {
								if ($users->getUserType() == '1') {
									$user_iD = $users->getId();
									if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
										$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
									} else {
										$user_name = '';
									}
								} else {
									$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_id1]
									);
									if (!empty($users)) {
										if ($users->getUserType() == '1') {
											$user_iD = $users->getId();
											if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
												$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$user_iD = '';
											$user_name = '';
										}
									} else {
										$user_iD = '';
										$user_name = '';
									}
								}
							} else {
								$user_iD = '';
								$user_name = '';
							}
						}
						if ($userAlbumVal->getPostImageFront() != '' && $userAlbumVal->getPostImageFront() != null) {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFront();
						} elseif ($userAlbumVal->getPostImageFrontLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontLeft();
						} elseif ($userAlbumVal->getPostImageLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageLeft();
						} elseif ($userAlbumVal->getPostImageBackLeft() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackLeft();
						} elseif ($userAlbumVal->getPostImageBack() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBack();
						} elseif ($userAlbumVal->getPostImageBackRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageBackRight();
						} elseif ($userAlbumVal->getPostImageRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageRight();
						} elseif ($userAlbumVal->getPostImageFrontRight() != '') {
							$userPostImage = $this->baseurl() . $userAlbumVal->getPostImageFrontRight();
						} else {
							$userPostImage = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $userPost]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$postRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $userPost]
						);
//echo '<pre>';print_r($postRating);die;
						if ($postRating != '' && $postRating != null) {

							if ($postRating->getUserRating() != '') {
								$Rating = $postRating->getUserRating();
							} else {
								$Rating = '';
							}
						} else {
							$Rating = '';
						}

						$Album[] = array(
							'post_id' => $userPost,
							'tag_status' => $tag_status,
							'user_post' => ($userPostImage),
							'service_name' => ($serviceName),
							'avg_rating' => $Rating,
							'post_status' => ($post_status),
							'user_id' => $user_iD,
							'user_name' => $user_name
						);

					}
				} else {
					$Album = [];
					$userPost = '';
				}
				if ((count($userfollow)) > 0 && $userfollow != null) {
					$countFollowers = count($userfollow);
				} else {
					$countFollowers = 0;
				}
				if ((count($user_reviews)) > 0 && $user_reviews != null) {
					$countReviews = count($user_reviews);
				} else {
					$countReviews = 0;
				}
				if ((count($relatedUser)) > 0 && $relatedUser != null) {
					$countPost = count($relatedUser);
				} else {
					$countPost = 0;
				}
				if ((count($userfollowing)) > 0 && $userfollowing != null) {
					$countFollowing = count($userfollowing);
				} else {
					$countFollowing = 0;
				}

				$user_status = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
					['toUserID' => $userID, 'userID' => $serviceID]
				);
				if ($user_status != '') {
					if ($user_status->geFollowStatus() != '') {
						$userFollowStatus = $user_status->geFollowStatus();
					} else {
						$userFollowStatus = '0';
					}
				} else {
					$userFollowStatus = '0';
				}

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $userID,
						'user_name' => ($userName),
						'user_fname' => $userFName,
						'user_lname' => $userLName,
						'user_contact' => $userMobile,
						'user_DOB' => $userDOB,
						'user_email' => $userEmail,
						'user_gender' => $userGender,
						'user_address' => $userAddress,
						'hide_info' => $userNotification,
						'user_profile_image' => ($user_profile),
						'user_reviews' => $countReviews,
						'user_followers' => $countFollowers,
						'follow_status' => $userFollowStatus,
						'total_post' => $countPost,
						'user_followings' => $countFollowing,
						'consumer_post' => $Album
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/serviceprovider", name="_serviceprovider")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function serviceproviderAction(Request $user_id, Request $sp_user_id)
	{


		$request = $this->getRequest();
		$sp_id = $request->get('sp_user_id');
		$users = $request->get('user_id');
		$sps = '';
//$em = $this->getDoctrine()->getEntityManager();
// $userCustomer = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findBy(['userID' => $request->get('user_id')]);
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$userData = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $users]);

		if ($userData != '' && $userData != null) {
			$user_Type = $userData->getUserType();
			if ($user_Type == '0') {
				$userCustomer = $conn->query(
					"select * from user_customer_relation where userID=" . $users . ""
				)->fetchAll();

				if ($userCustomer != '' && $userCustomer != null) {

					foreach ($userCustomer as $value) {
						$spID[] = $value['companyID'];
					}
				} else {
					$spID = 0;
				}
				// $spIDs = '';
//                for ($i = 0; $i < count($spID); $i++) {
//                    if (($spID[$i] != $sp_id)) {
//
//                        $spIDs[] = $spID[$i];
//                    }
//                }
//
//
//                if (count($spID) < 1) {
//                    $spIDs = '';
//                }
//
//                if ($sp_id > 0) {
				$company = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $spID, 'userType' => '1']
				);
				if ($company != '' && $company != null) {
					foreach ($company as $companyval) {

						$user_Id = $companyval->getId();

						if (($companyval->getUserFirstName() || $companyval->getUserLastName()) != '') {
							$userName = ($companyval->getUserFirstName() . ' ' . $companyval->getUserLastName());
						} else {
							$userName = '';
						}
						if ($companyval->getCompanyName() != '') {
							$companyName = $companyval->getCompanyName();
						} else {
							$companyName = '';
						}

						if ($companyval->getUserProfileImage() != '' && count($companyval->getUserProfileImage()) > 0) {
							$compnayProfile = $this->baseurl() . $companyval->getUserProfileImage();
						} else {
							$compnayProfile = $this->baseurl() . 'defaultprofile.png';
						}


//                } else {
//                    $companyID = '';
//                }
						$customer = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:UserCustomerRelation"
						)->findOneBy(['companyID' => $user_Id]);

						if ($customer->getUserCustomerRelationDate() != '') {
							$date = date('Y-m-d', strtotime($customer->getUserCustomerRelationDate()));
						} else {
							$date = '';
						}
						$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $user_Id]
						);

						if ($userRating != '' && $userRating != null) {
							foreach ($userRating as $userRateVal) {
								if ($userRateVal->getUserRating() != '') {
									$user_rating[] = $userRateVal->getUserRating();
								} else {
									$user_rating[] = '';
								}
							}
							$count = count($user_rating);
							$rating = array_sum($user_rating) / $count;
							$ratvalues = number_format((float)$rating, 1, '.', '');
						} else {
							$ratvalues = '';
						}

						$provider[] = array(
							'user_id' => ($user_Id),
							'user_name' => ($userName),
							'company_name' => ($companyName),
							'user_profile' => ($compnayProfile),
							'user_rating' => ($ratvalues),
							'date' => $date
						);
					}
					echo json_encode(
						array('success' => 1, 'message' => 'successfull', 'service_Provider' => $provider)
					);
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
				//} 

			} else {
				$userCustomer = $conn->query(
					"select * from user_customer_relation where companyID=" . $users . ""
				)->fetchAll();
				//echo '<pre>';print_r($result);die;
				if ($userCustomer != '' && $userCustomer != null) {

					foreach ($userCustomer as $value) {
						$spID[] = $value['userID'];
					}
				} else {
					$spID = 0;
				}
				// $spID = array_unique($ids);
//                for ($i = 0; $i < count($spID); $i++) {
//                    if (($spID[$i] != $sp_id)) {
//                        //$sp= $spID;
//                        $spIDs[] = $spID[$i];
//                    } else {
//                        $spIDs = '';
//                    }
//                }
//                if (count($spID) < 1) {
//                    $spIDs = '';
//                }
//
//                if ($sp_id > 0) {
				$company = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $spID, 'userType' => '0']
				);
				if ($company != '' && $company != null) {
					foreach ($company as $companyval) {

						$user_Id = $companyval->getId();

						if (($companyval->getUserFirstName() || $companyval->getUserLastName()) != '') {
							$userName = ($companyval->getUserFirstName() . ' ' . $companyval->getUserLastName());
						} else {
							$userName = '';
						}
						if ($companyval->getCompanyName() != '') {
							$companyName = $companyval->getCompanyName();
						} else {
							$companyName = '';
						}

						if ($companyval->getUserProfileImage() != '' && count($companyval->getUserProfileImage()) > 0) {
							$compnayProfile = $this->baseurl() . $companyval->getUserProfileImage();
						} else {
							$compnayProfile = $this->baseurl() . 'defaultprofile.png';
						}


//                } else {
//                    $companyID = '';
//                }
						$customer = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:UserCustomerRelation"
						)->findOneBy(['userID' => $user_Id]);

						if ($customer->getUserCustomerRelationDate() != '') {
							$date = date('Y-m-d', strtotime($customer->getUserCustomerRelationDate()));
						} else {
							$date = '';
						}
						$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $user_Id]
						);

						if ($userRating != '' && $userRating != null) {
							foreach ($userRating as $userRateVal) {
								if ($userRateVal->getUserRating() != '') {
									$user_rating[] = $userRateVal->getUserRating();
								} else {
									$user_rating[] = '';
								}
							}
							$count = count($user_rating);
							$rating = array_sum($user_rating) / $count;
							$ratvalues = number_format((float)$rating, 1, '.', '');
						} else {
							$ratvalues = '';
						}

						$provider[] = array(
							'user_id' => ($user_Id),
							'user_name' => ($userName),
							'company_name' => ($companyName),
							'user_profile' => ($compnayProfile),
							'user_rating' => ($ratvalues),
							'date' => $date
						);
					}
					echo json_encode(
						array('success' => 1, 'message' => 'successfull', 'service_Provider' => $provider)
					);
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
				//} 

			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/spconsumerregistration", name="_spconsumerregistration")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function spconsumerregistrationAction(
		Request $user_id,
		Request $user_name,
		Request $user_contact,
		Request $profile_image,
		Request $profile_base,
		Request $user_fname,
		Request $user_type,
		Request $user_lname,
		Request $user_DOB,
		Request $user_email,
		Request $user_address,
		Request $user_gender,
		Request $user_note,
		Request $lat,
		Request $long
	) {
		$request = $this->getRequest();
		$userRegistration = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['id' => $request->get('user_id')]
		);

		if ($userRegistration != '' && $userRegistration != null) {
			if ($userRegistration->getUserType() == '0') {
				$userRegistration->setUserFirstName($request->get('user_fname'));
				$userRegistration->setUserLastName($request->get('user_lname'));
				$userRegistration->setUserName($request->get('user_name'));
				$userRegistration->setUserDOB($request->get('user_DOB'));
				$userRegistration->setUserEmail($request->get('user_email'));
				$userRegistration->setUserNote($request->get('user_note'));
				$userRegistration->setUserGender($request->get('user_gender'));
				$userRegistration->setUserMobileNo($request->get('user_contact'));
				$userRegistration->setUserAddress($request->get('user_address'));
				$userRegistration->setLat($this->ClearText($request->get('lat')));
				$userRegistration->setLongitute($this->ClearText($request->get('long')));
//                $userRegistration->setLat($request->get('lat'));
//                $userRegistration->setLongitute($request->get('long'));

				if ($request->get('profile_image') != '' && $request->get('profile_image') != null) {

					$profileImage = $this->UploadFile($request->get('profile_image'), $request->get('profile_base'));
					$userRegistration->setUserProfileImage($profileImage);
				} else {
					if ($userRegistration->getUserProfileImage() == '') {
						$userRegistration->setUserProfileImage('');
					} else {
						$userRegistration->setUserProfileImage($userRegistration->getUserProfileImage());
					}
				}

				$em = $this->getDoctrine()->getManager();
				$em->persist($userRegistration);
				$em->flush();

				if ($userRegistration->getUserProfileImage() == '' && count(
						$userRegistration->getUserProfileImage()
					) < 1
				) {
					$image1 = $this->baseurl() . 'defaultprofile.png';
				} else {
					$image1 = $this->baseurl() . $userRegistration->getUserProfileImage();
				}
				if ($userRegistration->getId() != '') {
					$user_ID = $userRegistration->getId();
				} else {
					$user_ID = '';
				}
				if ($userRegistration->getUserFirstName() != '') {
					$userFName = $userRegistration->getUserFirstName();
				} else {
					$userFName = '';
				}
				if ($userRegistration->getUserLastName() != '') {
					$userLName = $userRegistration->getUserLastName();
				} else {
					$userLName = '';
				}
				if (($userRegistration->getUserName()) != '') {
					$userName = $userRegistration->getUserName();
				} else {
					$userName = '';
				}
				if ($userRegistration->getUserMobileNo() != '') {
					$userMobile = $userRegistration->getUserMobileNo();
				} else {
					$userMobile = '';
				}
				if ($userRegistration->getUserDOB() != '') {
					$userDOB = $userRegistration->getUserDOB();
				} else {
					$userDOB = '';
				}
				if ($userRegistration->getUserEmail() != '') {
					$userEmail = $userRegistration->getUserEmail();
				} else {
					$userEmail = '';
				}
				if ($userRegistration->getUserGender() != '') {
					$userGender = $userRegistration->getUserGender();
				} else {
					$userGender = '';
				}
				if ($userRegistration->getUserAddress() != '') {
					$userAddress = $userRegistration->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($userRegistration->getIsNotification() != '') {
					$userNotification = $userRegistration->getIsNotification();
				} else {
					$userNotification = '';
				}
				if ($userRegistration->getUserNote() != '') {
					$usernote = $userRegistration->getUserNote();
				} else {
					$usernote = '';
				}
//                }if ($userRegistration->getLat() != '') {
//                    $userLat = $userRegistration->getLat();
//                } else {
//                    $userLat = '';
//                }
//                if ($userRegistration->getLongitute() != '') {
//                    $userlong = $userRegistration->getLongitute();
//                } else {
//                    $userlong = '';
//                }

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'user_id' => $user_ID,
						'user_fname' => $userFName,
						'user_lname' => $userLName,
						'user_name' => $userName,
						'user_contact' => $userMobile,
						'user_DOB' => $userDOB,
						'user_email' => $userEmail,
						'user_gender' => $userGender,
						'user_type' => $request->get('user_type'),
						'user_address' => $userAddress,
						'user_notes' => $usernote,
						'profile_Image' => $image1
						//,'lat'=>$userLat,'long'=>$userlong
					)
				);

				return array();
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/mycustomer", name="_mycustomer")
	 * @Template()
	 */
	/*     * ************************************************************************serviceprovider's customer Begin ******************************************* */
	public function mycustomerAction(Request $user_id, Request $user_type, Request $counter)
	{
//$limitset = 2;
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$limitset = 4;

		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$customersuser = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findBy(
			['companyID' => $userID]
		);

		if ($customersuser != '' && $customersuser != null) {
			foreach ($customersuser as $customersuserVal) {
				if ($customersuserVal->getUserID() != '') {
					$user_ID[] = $customersuserVal->getUserID();
				} else {
					$user_ID = '';
				}
			}
		} else {
			$user_ID = '';
		}
		$normalUser = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
			['id' => $user_ID],
			array('id' => 'asc'),
			$max_num,
			$min_num
		);
		if ($normalUser != '' && $normalUser != null) {
			foreach ($normalUser as $normalUserVal) {
				$id = $normalUserVal->getId();

				if (($normalUserVal->getUserFirstName() || $normalUserVal->getUserLastName()) != '') {
					$userName = $normalUserVal->getUserFirstName() . ' ' . $normalUserVal->getUserLastName();
				} else {
					$userName = '';
				}
				if ($normalUserVal->getUserAddress() != '') {
					$userAddress = $normalUserVal->getUserAddress();
				} else {
					$userAddress = '';
				}
				if ($normalUserVal->getUserMobileNo() != '') {
					$userContact = $normalUserVal->getUserMobileNo();
				} else {
					$userContact = '';
				}
				if ($normalUserVal->getUserProfileImage() != '' && count($normalUserVal->getUserProfileImage()) > 0) {
					$profileImage = $this->baseurl() . $normalUserVal->getUserProfileImage();
				} else {
					$profileImage = $this->baseurl() . 'defaultprofile.png';
				}
				$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
					['followStatus' => '1']
				);
				if ($UserFollow != '') {
					$follow_status = '0';
					foreach ($UserFollow as $UserFollowVal) {
						if (($UserFollowVal->getUserID() == $userID) && ($UserFollowVal->getToUserID() == $id)) {
							$follow_status = '1';
						}
					}
				} else {
					$follow_status = '0';
				}
//                    $CustomerAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $userID, 'userTagID' =>$user_ID]);
//                    if ($CustomerAlbum != '' && $CustomerAlbum != null) {
//                       foreach($CustomerAlbum as $CustomerAlbumVal){
//                            $postData[] = $CustomerAlbumVal->getId();
//                       }
//                        }else{
//                            $postData = '';
//                        }
//                  $userRates = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['postID' => $postData],array('userRating'=>'desc'));
//                //echo '<pre>';print_r($userRates);
//                  if ($userRates != '' && $userRates != null) {
//                        foreach ($userRates as $userRatesVal) { 


				$CustomerImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['userID' => $userID, 'userTagID' => $id]
				);

				if ($CustomerImage != '' && $CustomerImage != null) {
					foreach ($CustomerImage as $CustomerImageVal) {
						if ($CustomerImageVal->getPostCaption() != '') {
							$post_caption = $CustomerImageVal->getPostCaption();
						} else {
							$post_caption = '';
						}
						if ($CustomerImageVal->getId() != '') {
							$albumID = $CustomerImageVal->getId();
						} else {
							$albumID = '';
						}
						if ($CustomerImageVal->getPostImageFront() != '' && $CustomerImageVal->getPostImageFront() != null
						) {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFront();
						} elseif ($CustomerImageVal->getPostImageFrontLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontLeft();
						} elseif ($CustomerImageVal->getPostImageLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageLeft();
						} elseif ($CustomerImageVal->getPostImageBackLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBackLeft();
						} elseif ($CustomerImageVal->getPostImageBack() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBack();
						} elseif ($CustomerImageVal->getPostImageBackRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBackRight();
						} elseif ($CustomerImageVal->getPostImageRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageRight();
						} elseif ($CustomerImageVal->getPostImageFrontRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontRight();
						} else {
							$image = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $albumID]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$albumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $albumID]
						);
						if ($albumRate != '' && $albumRate != null) {

							if ($albumRate->getUserRating() != '') {
								$postRating = $albumRate->getUserRating();
							} else {
								$postRating = '';
							}
						} else {
							$postRating = '';
						}
						if (!empty($image)) {
							$album[] = array(
								'album_id' => $albumID,
								'tag_status' => $tag_status,
								'album_image' => $image,
								'rates' => $postRating,
								'album_service' => $post_caption
							);
						}
					}
				} else {
					$album = [];
				}
				// }
//                    } else {
//                        $album = [];
//                    }

				$details[] = array(
					'user_id' => $id,
					'follow_status' => $follow_status,
					'user_name' => $userName,
					'user_contact' => $userContact,
					'user_address' => $userAddress,
					'profile_image' => $profileImage,
					'albums' => $album
				);
				unset($album);
			}
			echo json_encode(
				array(
					'success' => 1,
					'message' => 'successfull',
					'mycustomers' => $details,
					'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
//echo '<pre>';print_r($customers);die;  
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/myservices", name="_myservices")
	 * @Template()
	 */
	/*     * ************************************************************************My Services  Begin ******************************************* */
	public function myservicesAction(Request $user_id, Request $counter)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		/********************************************  VIEW ALL CUSTOMERS***************************************************************************/
//        $loginType = $request->get('login_type');
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$limitset = 9;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;


		$relatedUser = $conn->query(
			"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
		)->fetchAll();
		$albumIDs = '';
		if ($relatedUser != '' || $relatedUser != null) {
			foreach ($relatedUser as $relatedUserval) {
				$albumIDs[] = $relatedUserval['postID'];
			}
		} else {
			$albumIDs = [];
		}


		$CustomerImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
			['id' => $albumIDs],
			array('id' => 'desc'),
			$max_num,
			$min_num
		);
//echo '<pre>';print_r($CustomerImage);die;
		if ($CustomerImage != '' && $CustomerImage != null) {
			foreach ($CustomerImage as $CustomerImageVal) {
				if ($CustomerImageVal->getPostCaption() != '') {
					$post_caption = $CustomerImageVal->getPostCaption();
				} else {
					$post_caption = '';
				}
				if ($CustomerImageVal->getId() != '') {
					$albumID = $CustomerImageVal->getId();
				} else {
					$albumID = '';
				}
				$date = $CustomerImageVal->getPostDate();
				$explodedate = explode(' ', $date);
				$datePost = $explodedate[0];

				if ($CustomerImageVal->getPostImageFront() != '' && $CustomerImageVal->getPostImageFront() != null) {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageFront();
				} elseif ($CustomerImageVal->getPostImageFrontLeft() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontLeft();
				} elseif ($CustomerImageVal->getPostImageLeft() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageLeft();
				} elseif ($CustomerImageVal->getPostImageBackLeft() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageBackLeft();
				} elseif ($CustomerImageVal->getPostImageBack() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageBack();
				} elseif ($CustomerImageVal->getPostImageBackRight() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageBackRight();
				} elseif ($CustomerImageVal->getPostImageRight() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageRight();
				} elseif ($CustomerImageVal->getPostImageFrontRight() != '') {
					$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontRight();
				} else {
					$image = '';
				}
				$user_id = $CustomerImageVal->getUserID();
				$usertag_id = $CustomerImageVal->getUserTagID();
				$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
					['postID' => $albumID]
				);

				if ($PostTags != '') {
					$tag_status = '0';
					foreach ($PostTags as $PostTagsVal) {
						if ($PostTagsVal->getTags() != '') {
							$tag_status = '1';
						}
					}
				} else {
					$tag_status = '0';
				}
				if ($user_id != $usertag_id) {
					$ReviewedStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
						['postID' => $CustomerImageVal->getId()]
					);
					if (!empty($ReviewedStatus)) {
						$reviewed = '1';
					} else {
						$reviewed = '0';
					}
				} else {
					$reviewed = '1';
				}

				//echo '<pre>';print_r($tag_status);
				$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
					['id' => $CustomerImageVal->getUserTagID(), 'userType' => 0]
				);
				if ($User != '' && $User != null) {
					$user_id = $User->getId();
					if ($User->getUserFirstName() || $User->getUserLastName() != '') {
						$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
					} else {
						$user_name = '';
					}
				} else {
					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $CustomerImageVal->getUserID(), 'userType' => 0]
					);
					if ($User != '' && $User != null) {
						$user_id = $User->getId();
						if ($User->getUserFirstName() || $User->getUserLastName() != '') {
							$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$user_name = '';
						}
					}
				}
				$albumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
					['postID' => $albumID]
				);
				if ($albumRate != '' && $albumRate != null) {

					if ($albumRate->getUserRating() != '') {
						$postRating = $albumRate->getUserRating();
					} else {
						$postRating = '';
					}
				} else {
					$postRating = '';
				}
				if (isset($image) && !empty($image)) {
					$album[] = array(
						'album_id' => $albumID,
						'user_id' => $user_id,
						'tag_status' => $tag_status,
						'album_image' => $image,
						'rates' => $postRating,
						'album_service' => $post_caption,
						'date' => $datePost,
						'reviewed' => $reviewed
					);
				}
				//echo '<pre>';print_r($album);
			}//die;
			echo json_encode(
				array(
					'success' => 1,
					'message' => 'success',
					'albums' => $album,
					'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}


		/********************************************  VIEW ALL CUSTOMERS***END************************************************************************/


//        $services = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $request->get('user_id'), 'userType' => '0']);
//
//        if ($services != '' && $services != null) {
//
//            $sp_user_id = $services->getId();
//
//            if ($services->getUserFirstName() || $services->getUserLastName() != '') {
//                $userName = $services->getUserFirstName() . ' ' . $services->getUserLastName();
//            } else {
//                $userName = '';
//            }
//            if ($services->getCompanyName() != '') {
//                $companyName = $services->getCompanyName();
//            } else {
//                $companyName = '';
//            } if ($services->getUserAddress() != '') {
//                $userAddres = $services->getUserAddress();
//            } else {
//                $userAddres = '';
//            }if ($services->getUserType() != '') {
//                $userType = $services->getUserType();
//            } else {
//                $userType = '';
//            }
//
//            if ($services->getUserMobileNo() != '') {
//                $userContact = $services->getUserMobileNo();
//            } else {
//                $userContact = '';
//            }
//            if ($services->getUserProfileImage() != '') {
//                $profileImage = $this->baseurl() . $services->getUserProfileImage();
//            } else {
//                $profileImage = $this->baseurl() . 'defaultprofile.png';
//            }
//            $serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);
//
//            if ($serviceName != '' && $serviceName != null) {
//                $serviceId = $serviceName->getServiceID();
//
//                $serviceprice = $serviceName->getServicePrice();
//                $masterService = $this->getDoctrine()->getRepository("AcmeDemoBundle:MasterServices")->findOneBy(['id' => $serviceId]);
//                $service_name = $masterService->getServiceName();
//            } else {
//                $serviceprice = '';
//                $service_name = '';
//            }
//
//            $customers1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userTagID' => $sp_user_id]);
////            echo '<pre>';print_r($customers1);die;
//            if ($customers1 != '' && $customers1 != null) {
//                foreach ($customers1 as $Customer1Val) {
//                    $spID[] = $Customer1Val->getUserID();
//                    $post_id[] = $Customer1Val->getId();
//                }
//            } else {
//                $post_id = [];
//                $spID = [];
//            }
//            $spIDs = '';
//            for ($i = 0; $i < count($spID); $i++) {
//                if ($spID[$i] != $userID) {
//                    $spIDs[] = $post_id[$i];
//                }
//            }
//            if (count($spIDs) < 1) {
//                $spIDs = '';
//            }
//
//
//            $customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['id' => $spIDs], array('id' => 'desc'));
//            // echo '<pre>';print_r($customers);die;
//            if ($customers != '' && $customers != null) {
//
//                foreach ($customers as $postModelval) {
//                    $services1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $postModelval->getUserID(), 'userType' => '1']);
//
//                    if ($services1 != '' && $services1 != null) {
//
//
//                        if ($postModelval->getPostImageFront() != '' && $postModelval->getPostImageFront() != null) {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageFront();
//                        } elseif ($postModelval->getPostImageFrontLeft() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageFrontLeft();
//                        } elseif ($postModelval->getPostImageLeft() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageLeft();
//                        } elseif ($postModelval->getPostImageBackLeft() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageBackLeft();
//                        } elseif ($postModelval->getPostImageBack() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageBack();
//                        } elseif ($postModelval->getPostImageBackRight() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageBackRight();
//                        } elseif ($postModelval->getPostImageRight() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageRight();
//                        } elseif ($postModelval->getPostImageFrontRight() != '') {
//                            $userprofile = $this->baseurl() . $postModelval->getPostImageFrontRight();
//                        } else {
//                            $userprofile = '';
//                        }
//                        $PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(['postID' => $postModelval->getId()]);
//                        if ($PostTags != '') {
//                            $tag_status = '0';
//                            foreach ($PostTags as $PostTagsVal) {
//                                if ($PostTagsVal->getTags() != '') {
//                                    $tag_status = '1';
//                                }
//                            }
//                        } else {
//                            $tag_status = '0';
//                        }
//                        $album_id = $postModelval->getId();
//                        if ($postModelval->getPostCaption() != '') {
//                            $post_caption = $postModelval->getPostCaption();
//                        } else {
//                            $post_caption = '';
//                        }
//
//                        if ($postModelval->getUserTagID() != '') {
//                            $tagedUser = $postModelval->getUserTagID();
//                        } else {
//                            $tagedUser = '';
//                        }
//
//                        $User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $postModelval->getUserID()]);
//
//                        if ($User != '' && $User != null) {
//                            //echo '<pre>';print_r($User);die('okko');
//                            $user_id = $User->getId();
//                            if ($User->getUserFirstName() || $User->getUserLastName() != '') {
//                                $user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
//                            } else {
//                                $user_name = '';
//                            }
//                        } else {
//
//                            $user_name = '';
//                        }
//
//                        $UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['toUserID' => $tagedUser]);
//                        if ($UserRating != '' && $UserRating != null) {
//                            foreach ($UserRating as $UserRatingVal) {
//                                if ($UserRatingVal->getUserRating() != '') {
//                                    $ratings[] = $UserRatingVal->getUserRating();
//                                } else {
//                                    $ratings[] = '';
//                                }
//                                $count = count($ratings);
//                                $rating = array_sum($ratings) / $count;
//                                $ratvalues = number_format((float) $rating, 1, '.', '');
//                            }
//                        } else {
//                            $ratvalues = '';
//                        }
//                        if (!empty($userprofile)) {
//                            $album_detail[] = array('album_id' => $album_id, 'album_service' => $post_caption, 'user_id' => $user_id,
//                                'album_image' => $userprofile, 'tag_status' => $tag_status,
//                                'user_name' => $user_name, 'rates' => $ratvalues);
//                        }
//                    }
//                }
//            } else {
//                $album_detail = [];
//            }
//
//            $UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['toUserID' => $sp_user_id]);
//            //echo '<pre>';print_r($UserRating);
//            if ($UserRating != '' && $UserRating != null) {
//                foreach ($UserRating as $UserRatingVal) {
//                    if ($UserRatingVal->getUserRating() != '') {
//                        $rating1[] = $UserRatingVal->getUserRating();
//                    } else {
//                        $rating1[] = '';
//                    }if ($UserRatingVal->getUserReviews() != '') {
//                        $reviews[] = $UserRatingVal->getUserReviews();
//                    } else {
//                        $reviews[] = '';
//                    }
//                    $count = count($rating1);
//                    $rating = array_sum($rating1) / $count;
//                    $ratvalues = number_format((float) $rating, 1, '.', '');
//                }
//            } else {
//                $ratvalues = '';
//                $reviews = '';
//            }
//            if (count($reviews) > 0 && $reviews != null) {
//                $countReview = count($reviews);
//            } else {
//                $countReview = 0;
//            }
//            if (count($ratvalues) > 0 && $ratvalues != null) {
//                $countRates = $ratvalues;
//            } else {
//                $countRates = 0;
//            }
//
//
//            $sp_detail = array(['user_name' => $userName, 'user_type' => $userType, 'sp_user_id' => $sp_user_id, 'contact' => $userContact, 'company_name' => $companyName, 'user_address' => $userAddres, 'profile_image' => $profileImage
//                    , 'service_name' => $service_name, 'service_price' => $serviceprice, 'user_chat' => 0,
//                    'total_reviews' => $countReview, 'total_rate' => $countRates, 'albums' => $album_detail]);
//            unset($album_detail);
//
//
//
//
//            echo json_encode(array('success' => 1, 'message' => 'successfull', 'my_services' => $sp_detail, 'thumbnail_image' => 'http://webdior.co.in/apihaircut/Symfony/timthumb.php?src='));
//        } else {
//            echo json_encode(array('success' => 0, 'message' => 'failure'));
//        }
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/serviceProviderprofileinfo", name="_serviceProviderprofileinfo")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User Registration Begin ******************************************* */
	public function serviceProviderprofileinfoAction(Request $user_id)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$spProfile = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

		if ($spProfile != '' && $spProfile != null && $spProfile->getUserType() == 1) {
			if ($spProfile->getUserProfileImage() != '' && $spProfile->getUserProfileImage() > 0) {
				$image = $this->baseurl() . $spProfile->getUserProfileImage();
			} else {
				$image = $this->baseurl() . 'defaultprofile.png';
			}
			if ($spProfile->getUserSignature() != '') {
				$imagesign = $this->baseurl() . $spProfile->getUserSignature();
			} else {
				$imagesign = '';
			}
			if ($spProfile->getUserFirstName() != '') {
				$userFName = $spProfile->getUserFirstName();
			} else {
				$userFName = '';
			}
			if ($spProfile->getUserLastName() != '') {
				$userLName = $spProfile->getUserLastName();
			} else {
				$userLName = '';
			}
			if (($spProfile->getUserFirstName() && $spProfile->getUserLastName()) != '') {
				$userName = $spProfile->getUserLastName();
			} else {
				$userName = '';
			}
			if ($spProfile->getUserDOB() != '') {
				$userDOB = $spProfile->getUserDOB();
			} else {
				$userDOB = '';
			}
			if ($spProfile->getUserAddress() != '') {
				$userAddress = $spProfile->getUserAddress();
			} else {
				$userAddress = '';
			}
			if ($spProfile->getUserEmail() != '') {
				$userEmail = $spProfile->getUserEmail();
			} else {
				$userEmail = '';
			}
			if ($spProfile->getCompanyName() != '') {
				$userCompany = $spProfile->getCompanyName();
			} else {
				$userCompany = '';
			}
			if ($spProfile->getUserWebsite() != '') {
				$userWeb = 'http://' . $spProfile->getUserWebsite();
			} else {
				$userWeb = '';
			}
			if ($spProfile->getUserBIO() != '') {
				$userBIO = $spProfile->getUserBIO();
			} else {
				$userBIO = '';
			}
			if ($spProfile->getUserMobileNo() != '') {
				$userMobile = $spProfile->getUserMobileNo();
			} else {
				$userMobile = '';
			}
			if ($spProfile->getUserType() != '') {
				$userType = $spProfile->getUserType();
			} else {
				$userType = '';
			}
			/* Service provider service details */
			$userServices = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findBy(
				['userID' => $userID]
			);
			if ($userServices != '' && $userServices != null) {
				foreach ($userServices as $serviceval) {
					if ($serviceval->getServiceID() != '') {
						$user_service = $serviceval->getServiceID();


						$master_service = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:MasterServices"
						)->findOneBy(['id' => $user_service]);
						if ($master_service->getServiceName() != '') {
							$getService = $master_service->getServiceName();
						} else {
							$getService = '';
						}
					} else {
						$user_service = '';
					}
					if ($serviceval->getServicePrice() != '') {
						$service_price = $serviceval->getServicePrice();
					} else {
						$service_price = '';
					}
					if ($serviceval->getTopService() != '') {
						$topServices = $serviceval->getTopService();
					} else {
						$topServices = '';
					}
					$serviceDetail[] = array(
						'service_Name' => $getService,
						'service_Price' => $service_price,
						'top_service' => $topServices,
						'service_id' => $user_service
					);
				}
			} else {
				$serviceDetail = [];
			}


			echo json_encode(
				array(
					'success' => 1,
					'message' => 'success',
					'userFirstName' => $userFName,
					'userLastname' => $userLName,
					'userName' => $userName,
					'userDOB' => $userDOB,
					'user_address' => $userAddress,
					'user_email' => $userEmail,
					'companyName' => $userCompany,
					'userwebsite' => $userWeb,
					'userBIO' => $userBIO,
					'userMobileNo' => $userMobile,
					'user_type' => $userType,
					'profile_image' => $image,
					'sign_image' => $imagesign,
					'services' => $serviceDetail
				)
			);

			return array();
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Service provider info End******************************************* */

	/**
	 * @Route("/consumerprofileinfo", name="_consumerprofileinfo")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User info Begin ******************************************* */
	public function consumerprofileinfoAction(Request $user_id)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$consumerProfile = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

		if ($consumerProfile != '') {
			if ($consumerProfile->getUserProfileImage() != '' && $consumerProfile->getUserProfileImage() > 0) {
				$image = $this->baseurl() . $consumerProfile->getUserProfileImage();
			} else {
				$image = $this->baseurl() . 'defaultprofile.png';
			}
			if ($consumerProfile->getId() != '') {
				$user_ID = $consumerProfile->getId();
			} else {
				$user_ID = '';
			}
			if ($consumerProfile->getUserFirstName() != '') {
				$userFName = $consumerProfile->getUserFirstName();
			} else {
				$userFName = '';
			}
			if ($consumerProfile->getUserLastName() != '') {
				$userLName = $consumerProfile->getUserLastName();
			} else {
				$userLName = '';
			}
			if (($consumerProfile->getUserFirstName() && $consumerProfile->getUserLastName()) != '') {
				$userName = $consumerProfile->getUserFirstName() . ' ' . $consumerProfile->getUserLastName();
			} else {
				$userName = '';
			}
			if ($consumerProfile->getUserMobileNo() != '') {
				$userMobile = $consumerProfile->getUserMobileNo();
			} else {
				$userMobile = '';
			}
			if ($consumerProfile->getUserDOB() != '') {
				$userDOB = $consumerProfile->getUserDOB();
			} else {
				$userDOB = '';
			}
			if ($consumerProfile->getUserEmail() != '') {
				$userEmail = $consumerProfile->getUserEmail();
			} else {
				$userEmail = '';
			}
			if ($consumerProfile->getUserGender() != '') {
				$userGender = $consumerProfile->getUserGender();
			} else {
				$userGender = '';
			}
			if ($consumerProfile->getUserAddress() != '') {
				$userAddress = $consumerProfile->getUserAddress();
			} else {
				$userAddress = '';
			}
			if ($consumerProfile->getIsNotification() != '') {
				$userNotification = $consumerProfile->getIsNotification();
			} else {
				$userNotification = '';
			}

			echo json_encode(
				array(
					'success' => 1,
					'message' => 'successfull',
					'user_id' => $user_ID,
					'user_fname' => $userFName,
					'user_lname' => $userLName,
					'user_name' => $userName,
					'user_contact' => $userMobile,
					'user_DOB' => $userDOB,
					'user_email' => $userEmail,
					'user_gender' => $userGender,
					'user_address' => $userAddress,
					'hide_info' => $userNotification,
					'profile_Image' => $image
				)
			);

			return array();
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/contactinfo", name="_contactinfo")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User info Begin ******************************************* */
	public function contactinfoAction(Request $user_id)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$contact = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

		if ($contact != '' && $contact != null) {
			if ($contact->getUserType() == 1) {
				if ($contact->getUserWebsite() != '') {
					$userweb = 'http://' . $contact->getUserWebsite();
				} else {
					$userweb = '';
				}
			} else {
				$userweb = '';
			}
			if ($contact->getUserAddress() != '') {
				$userAddress = $contact->getUserAddress();
			} else {
				$userAddress = '';
			}
			if ($contact->getUserMobileNo() != '') {
				$userMobile = $contact->getUserMobileNo();
			} else {
				$userMobile = '';
			}
			if ($contact->getUserEmail() != '') {
				$userEmail = $contact->getUserEmail();
			} else {
				$userEmail = '';
			}
			if ($contact->getUserFirstName() || $contact->getUserLastName()) {
				$user_name = $contact->getUserFirstName() . ' ' . $contact->getUserLastName();
			} else {
				$user_name = '';
			}


			echo json_encode(
				array(
					'success' => 1,
					'message' => 'successfull',
					'user_name' => $user_name,
					'userAddress' => $userAddress,
					'user_contactno' => $userMobile,
					'user_email' => $userEmail,
					'user_web' => $userweb
				)
			);

			return array();
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/latlong", name="_latlong")
	 * @Template()
	 */
	/*     * ************************************************************************Normal *User info Begin ******************************************* */
	public function latlongAction(Request $user_id, Request $lat, Request $long)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$userAddress = null;
//$userAddress = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
		if ($userAddress != '' && $userAddress != null) {

			if (($request->get('lat') && $request->get('long')) != '') {

				$userLat = $request->get('lat');
				$userLong = $request->get('long');
			} else {
				$userLat = '';
				$userLong = '';
			}
			$userAddress->setLat($userLat);
//echo '<pre>';print_r($userAddress->setLat($userLat));die;
			$userAddress->setLong($userLong);
			$em = $this->getDoctrine()->getManager();
			$em->persist($userAddress);

			$em->flush();
			echo json_encode(array('success' => 1, 'message' => 'successfull'));

			return array();
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************Normal User Registration End******************************************* */

	/**
	 * @Route("/imageupload", name="_imageupload")
	 * @Template()
	 */
	/*     * ************************************************************************Image *Upload Begin ******************************************* */
	public function imageuploadAction(
		Request $from_user_id,
		Request $to_user_id,
		Request $post_status,
		Request $post_caption,
		Request $post_note,
		Request $category
	) {


		$request = $this->getRequest();

		$userID = $request->get('from_user_id');

		$userTagID = $request->get('to_user_id');

		$postCaption = $request->get('post_caption');
		$postNote = $request->get('post_note');
		$category = $request->get('category');
		$UserData1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
		$UserData2 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userTagID]);
		// echo '<pre>';print_r($UserData1);die;
		$Post = new AlbumPost();
		if ($UserData1->getUserType() == '1' && $UserData2->getUserType() == '0') {
			$Post->setUserID($UserData2->getId());
			$Post->setUserTagID($UserData1->getId());
			$userName = ucwords($UserData2->getUserFirstName());
			$useremail = $UserData2->getUserEmail();
			// $password = $usermodel2->getUserPassword();
			$userFName = $UserData1->getUserFirstName();
			$subject = 'Uploaded SPPS';
			$body_text = 'Uploaded SPPS';
			$body_html = 'Hello ' . $userName . ', <br><br>' . $userFName . ' uploaded your Service Provider Picture Set (SPPS).<br>  You can Rate/Review this SPPS by logging into the HereCut App and clicking the Home Icon > My Services.  <br><br><br>Thank You <br>HereCut Team';
			$from = 'info@herecut.net';
			$fromName = 'HereCut';
			$headers = "From: " . $from . "\r\n";
			$headers .= "Reply-To: " . $from . "\r\n";
			//$headers .= "CC: test@example.com\r\n"; 
			/*    $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);			 
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/
			$this->smtpEmail($useremail, $subject, $body_html);
			$usercustomerRelation = $this->getDoctrine()->getRepository(
				"AcmeDemoBundle:UserCustomerRelation"
			)->findOneBy(['userID' => $userTagID, 'companyID' => $userID]);
// echo '<pre>';print_r($usercustomerRelation->getUserID());die;
			if ($usercustomerRelation == '') {
				$userdata = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);

				$users = new UserCustomerRelation();
				if ($userdata->getUserType() == '0') {
					$users->setUserID($userID);
					$users->setCompanyID($userTagID);
				} else {
					$users->setUserID($userTagID);
					$users->setCompanyID($userID);
				}
				$em = $this->getDoctrine()->getManager();
				$em->persist($users);
				$em->flush();
			}


// $email = (explode('@', $UserData2->getUserEmail()));

		} elseif ($UserData1->getUserType() == '0' && $UserData2->getUserType() == '1') {
			//this notice covers a Consumer account used to upload spps of the sp
			$Post->setUserID($UserData2->getId());
			$Post->setUserTagID($UserData1->getId());

			$userName = ucwords($UserData2->getUserFirstName());

			$consumerEmail = $UserData1->getUserEmail();
			$spEmail = $UserData2->getUserEmail();

			$consumerFName = ucwords($UserData1->getUserFirstName());
			$spFName = ucwords($UserData2->getUserFirstName());

			//***SPPS notify sent to Consumer
			$subject = 'Uploaded SPPS';
			$body_text = 'Uploaded SPPS';
			$body_html_1 = 'Hello ' . $consumerFName . ', <br><br>' . $spFName . ' uploaded your Service Provider Picture Set (SPPS).<br> Before you can Rate/Review this SPPS, ' . $spFName . ' must first approve the SPPS. You will be notified once approved then Rate/Review by logging into the HereCut App and clicking the Home Icon > My Services.  <br><br><br>Thank You <br>HereCut Team';
			$from = 'info@herecut.net';
			$fromName = 'HereCut';
			$headers = "From: " . $from . "\r\n";
			$headers .= "Reply-To: " . $from . "\r\n";

			$this->smtpEmail($consumerEmail, $subject, $body_html_1);

			//***SPPS notify sent to SP
			$body_html_2 = 'Hello ' . $spFName . ', <br><br>' . $consumerFName . ' uploaded your Service Provider Picture Set (SPPS).<br>  If this is one of your customers, please approve this SPPS by logging into the HereCut App and clicking the Profile Icon > click Posts on the dialog box > click the post > move slider to approve for each post you want to be linked to your account or delete if this is not a customer. You may also click the Profile Icon and find the pending SPPS listed until you approve or delete it. <br><br><br>Thank You <br>HereCut Team';

			//$headers .= "CC: test@example.com\r\n"; 
			/*    $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/

			$this->smtpEmail($spEmail, $subject, $body_html_2);

		} elseif ($UserData1->getUserType() == '0' && $UserData2->getUserType() == '0') {
			$Post->setUserID($UserData1->getId());
			$Post->setUserTagID($UserData2->getId());
		}
		$Post->setPostCaption($postCaption);
		$Post->setPostNote($postNote);
		$postDate = date('Y-m-d H:i:s');
		$Post->setPostDate($postDate);


		$em = $this->getDoctrine()->getManager();
		$em->persist($Post);
		$em->flush();
		$AlbumPost = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
			['id' => $Post->getId()]
		);
		if ($AlbumPost != '' && $AlbumPost != null) {
			$UserData = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $AlbumPost->getUserID()]
			);
			$UserModels = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $AlbumPost->getUserTagID()]
			);
			if ($UserData->getUserType() == '1' && $UserModels->getUserType() == '0') {
				$AlbumPost->setPostStatus('1');
				$AlbumPost->setSpPostStatus('1');
			} elseif ($UserData->getUserType() == '0' && $UserModels->getUserType() == '1') {
				$AlbumPost->setSpPostStatus('0');
				$AlbumPost->setPostStatus('0');
			} elseif ($UserData->getUserType() == '0' && $UserModels->getUserType() == '0') {
				$AlbumPost->setSpPostStatus('0');
				$AlbumPost->setPostStatus('0');
			}
			$em = $this->getDoctrine()->getManager();
			$em->persist($AlbumPost);
			$em->flush();
		}


		$cat = explode(',', $request->get('category'));
		foreach ($cat as $catVal) {
			$categories = new PostCategory();
			$categories->setPostID($Post->getId());
			$categories->setCategoryID($catVal);
			$em = $this->getDoctrine()->getManager();
			$em->persist($categories);
			$em->flush();
		}


		echo json_encode(array('success' => 1, 'message' => 'successfull', 'album_id' => $Post->getId()));
	}

	/**
	 * @Route("/imageupdate", name="_imageupdate")
	 * @Template()
	 */
	/*     * ************************************************************************Image *update Begin ******************************************* */
	public function imageupdateAction(
		Request $album_id,
		Request $image_height,
		Request $image_name,
		Request $image_base,
		Request $image_size,
		Request $image_x_axis,
		Request $image_y_axis,
		Request $image_tags,
		Request $image_notes,
		Request $image_type,
		Request $image_no
	) {
		$request = $this->getRequest();
		$image_number = $request->get('image_no');

		$ImageUpdate = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
			['id' => $request->get('album_id')]
		);
		// echo '<pre>';print_r($ImageUpdate);die;
		if ($ImageUpdate != '' && $ImageUpdate != null) {
			$tags = explode(',', $request->get('image_tags'));
			foreach ($tags as $tagsVal) {
				$tagsImage[] = $tagsVal;
			}
			$userID = $ImageUpdate->getUserID();
			$userTagID = $ImageUpdate->getUserTagID();
			//echo '<pre>';print_r($tags);die;

			if ($image_number == '0' && $image_number != '')//image_no 1 for first time notification 
			{

				/* NOTIFICATION FUNCTION START */
				$UserData = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
				$UserModels = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
					['id' => $userTagID]
				);
				if (!empty($UserModels)) {
					if (($UserModels->getUserFirstName() != '') || ($UserModels->getUserLastName() != '')) {
						$userName = $UserModels->getUserFirstName() . ' ' . $UserModels->getUserLastName();
					} else {
						$userName = '';
					}
					if ($UserData->getIsNotification() == '1') {
						$msg = 'Post';
						$IDs = $UserData->getId();
						if ($userID != $userTagID) {
							$submsg = $userName . ' ' . 'posted your album';
						} else {
							$submsg = 'You have added your Picture Set';
						}

						$usenotification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findBy(
							['userID' => $UserData->getId()]
						);
						if ($userID != $userTagID) {
							if ($usenotification != '' && $usenotification != null) {
								foreach ($usenotification as $usenotificationVal) {

									$registatoin_ids = $usenotificationVal->getDeviceID();

									$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
								}
							}
						}
						$notificationModel = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:NotificationMessage"
						)->findOneBy(['userID' => $userID, 'toUserID' => $userTagID, 'notificationTitle' => 'Post']);

						if ($notificationModel == '' && $notificationModel == null) {

							$notifyMsg = new NotificationMessage();
							$notifyMsg->setNotificationTitle($msg);
							$notifyMsg->setNotificationMessage($submsg);
							$notifyMsg->setNotificationStatus('0');
							$notifyMsg->setUserID($UserData->getId());
							$notifyMsg->setToUserID($UserModels->getId());
							$em = $this->getDoctrine()->getManager();
							$em->persist($notifyMsg);
							$em->flush();
						} else {
							//  $em = $this->getDoctrine()->getEntityManager(); //tetst
							//  $em->remove($notificationModel);
							//  $em->flush();
							$notifyMsg = new NotificationMessage();
							$notifyMsg->setNotificationTitle($msg);
							$notifyMsg->setNotificationMessage($submsg);
							$notifyMsg->setNotificationStatus('0');
							$notifyMsg->setUserID($UserData->getId());
							$notifyMsg->setToUserID($UserModels->getId());
							$em = $this->getDoctrine()->getManager();
							$em->persist($notifyMsg);
							$em->flush();
						}
					} else {
						$msg = 'Post';
						$IDs = $UserData->getId();
						// $submsg = $userName . ' ' . 'posted your album';

						if ($userID != $userTagID) {
							$submsg = $userName . ' ' . 'posted your album';
						} else {
							$submsg = 'You have added your Picture Set';
						}

						$notificationModel = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:NotificationMessage"
						)->findOneBy(['userID' => $userID, 'toUserID' => $userTagID, 'notificationTitle' => 'Post']);

						if ($notificationModel == '' && $notificationModel == null) {

							$notifyMsg = new NotificationMessage();
							$notifyMsg->setNotificationTitle($msg);
							$notifyMsg->setNotificationMessage($submsg);
							$notifyMsg->setNotificationStatus('0');
							$notifyMsg->setUserID($UserData->getId());
							$notifyMsg->setToUserID($UserModels->getId());
							$em = $this->getDoctrine()->getManager();
							$em->persist($notifyMsg);
							$em->flush();
						} else {
							//  $em = $this->getDoctrine()->getEntityManager();
							//  $em->remove($notificationModel);
							//  $em->flush();
							$notifyMsg = new NotificationMessage();
							$notifyMsg->setNotificationTitle($msg);
							$notifyMsg->setNotificationMessage($submsg);
							$notifyMsg->setNotificationStatus('0');
							$notifyMsg->setUserID($UserData->getId());
							$notifyMsg->setToUserID($UserModels->getId());
							$em = $this->getDoctrine()->getManager();
							$em->persist($notifyMsg);
							$em->flush();
						}


					}


				}
				/* NOTIFICATION FUNCTION END */
			}

			//echo '<pre>';print_R($tagsImage);
			$screenSize = $request->get('image_size');
			$ImageHeight = $request->get('image_height');
			/* FOR IMAGE RETURN CHECK BY ANKUR SIR */
//            $x_axis = explode(',', $request->get('image_x_axis'));
//            foreach ($x_axis as $x_axisVal) {
//                $x_axisImage[] = $x_axisVal * 100 / $screenSize;
//            }
//            $y_axis = explode(',', $request->get('image_y_axis'));
//            foreach ($y_axis as $y_axisVal) {
//                $y_axisImage[] = $y_axisVal * 100 / $screenSize;
//            }
			/* FOR IMAGE RETURN CHECK BY ANKUR SIR */
			$x_axis = explode(',', $request->get('image_x_axis'));
			foreach ($x_axis as $x_axisVal) {
				$x_axisImage[] = $x_axisVal;
			}
			$y_axis = explode(',', $request->get('image_y_axis'));
			foreach ($y_axis as $y_axisVal) {
				$y_axisImage[] = $y_axisVal;
			}

//            $notes = explode(',', $request->get('image_notes'));
//            foreach ($notes as $notesVal) {
//                $noteImage[] = $notesVal;
//            }
			if ($request->get('image_type') == 'Front') {
				$imageBase = ($request->get('image_base'));
				if ($imageBase != '' && $imageBase != null) {
					$Image = $this->UploadFile($request->get('image_name'), $imageBase);
				}
				$ImageUpdate->setPostImageFront($Image);
				$em = $this->getDoctrine()->getManager();
				$em->persist($ImageUpdate);
				$em->flush();
				if (count($tagsImage) > 0 && $tagsImage[0] != '') {
					$tagImages = count($tagsImage);
				} else {
					$tagImages = count($request->get('image_type'));
				}
				//echo '<pre>';print_r($tagImages);die;
				for ($i = 0; $i < $tagImages; $i++) {
					$ImageTages = new PostTags();
					$ImageTages->setImageSize($ImageHeight);
					$ImageTages->setImageWidth($screenSize);
					$ImageTages->setPostID($ImageUpdate->getId());
					$ImageTages->setImageType($request->get('image_type'));
					$ImageTages->setImageName($ImageUpdate->getPostImageFront());
					$ImageTages->setTags($tagsImage[$i]);
					$ImageTages->setTagNote('');
					$ImageTages->setX_Axis($x_axisImage[$i]);
					$ImageTages->setY_Axis($y_axisImage[$i]);
					$em = $this->getDoctrine()->getManager();
					$em->persist($ImageTages);
					$em->flush();
				}
				echo json_encode(array('success' => 1, 'message' => 'Successfull'));
			} else {
				if ($request->get('image_type') == 'Front Left') {
					$imageBase = ($request->get('image_base'));
					if ($imageBase != '' && $imageBase != null) {
						$Image = $this->UploadFile($request->get('image_name'), $imageBase);
					}
					$ImageUpdate->setPostImageFrontLeft($Image);
					$em = $this->getDoctrine()->getManager();
					$em->persist($ImageUpdate);
					$em->flush();
					if (count($tagsImage) > 0 && $tagsImage[0] != '') {
						$tagImages = count($tagsImage);
					} else {
						$tagImages = count($request->get('image_type'));
					}

					for ($i = 0; $i < $tagImages; $i++) {
						$ImageTages = new PostTags();
						$ImageTages->setImageSize($ImageHeight);
						$ImageTages->setImageWidth($screenSize);
						$ImageTages->setPostID($ImageUpdate->getId());
						$ImageTages->setImageType($request->get('image_type'));
						$ImageTages->setImageName($ImageUpdate->getPostImageFrontLeft());
						$ImageTages->setTags($tagsImage[$i]);
						$ImageTages->setTagNote('');
						$ImageTages->setX_Axis($x_axisImage[$i]);
						$ImageTages->setY_Axis($y_axisImage[$i]);
						$em = $this->getDoctrine()->getManager();
						$em->persist($ImageTages);
						$em->flush();
					}
					echo json_encode(array('success' => 1, 'message' => 'Successfull'));
				} else {
					if ($request->get('image_type') == 'Left') {
						$imageBase = ($request->get('image_base'));
						if ($imageBase != '' && $imageBase != null) {
							$Image = $this->UploadFile($request->get('image_name'), $imageBase);
						}
						$ImageUpdate->setPostImageLeft($Image);
						$em = $this->getDoctrine()->getManager();
						$em->persist($ImageUpdate);
						$em->flush();
						if (count($tagsImage) > 0 && $tagsImage[0] != '') {
							$tagImages = count($tagsImage);
						} else {
							$tagImages = count($request->get('image_type'));
						}

						for ($i = 0; $i < $tagImages; $i++) {
							$ImageTages = new PostTags();
							$ImageTages->setImageSize($ImageHeight);
							$ImageTages->setImageWidth($screenSize);
							$ImageTages->setPostID($ImageUpdate->getId());
							$ImageTages->setImageType($request->get('image_type'));
							$ImageTages->setImageName($ImageUpdate->getPostImageLeft());
							$ImageTages->setTags($tagsImage[$i]);
							$ImageTages->setTagNote('');
							$ImageTages->setX_Axis($x_axisImage[$i]);
							$ImageTages->setY_Axis($y_axisImage[$i]);
							$em = $this->getDoctrine()->getManager();
							$em->persist($ImageTages);
							$em->flush();
						}
						echo json_encode(array('success' => 1, 'message' => 'Successfull'));
					} else {
						if ($request->get('image_type') == 'Back Left') {

							$imageBase = ($request->get('image_base'));
							if ($imageBase != '' && $imageBase != null) {
								$Image = $this->UploadFile($request->get('image_name'), $imageBase);
							}
							$ImageUpdate->setPostImageBackLeft($Image);
							$em = $this->getDoctrine()->getManager();
							$em->persist($ImageUpdate);
							$em->flush();
							if (count($tagsImage) > 0 && $tagsImage[0] != '') {
								$tagImages = count($tagsImage);
							} else {
								$tagImages = count($request->get('image_type'));
							}

							for ($i = 0; $i < $tagImages; $i++) {
								$ImageTages = new PostTags();
								$ImageTages->setImageSize($ImageHeight);
								$ImageTages->setImageWidth($screenSize);
								$ImageTages->setPostID($ImageUpdate->getId());
								$ImageTages->setImageType($request->get('image_type'));
								$ImageTages->setImageName($ImageUpdate->getPostImageBackLeft());
								$ImageTages->setTags($tagsImage[$i]);
								$ImageTages->setTagNote('');
								$ImageTages->setX_Axis($x_axisImage[$i]);
								$ImageTages->setY_Axis($y_axisImage[$i]);
								$em = $this->getDoctrine()->getManager();
								$em->persist($ImageTages);
								$em->flush();
							}
							echo json_encode(array('success' => 1, 'message' => 'Successfull'));
						} else {
							if ($request->get('image_type') == 'Back') {
								$imageBase = ($request->get('image_base'));
								if ($imageBase != '' && $imageBase != null) {
									$Image = $this->UploadFile($request->get('image_name'), $imageBase);
								}
								$ImageUpdate->setPostImageBack($Image);
								$em = $this->getDoctrine()->getManager();
								$em->persist($ImageUpdate);
								$em->flush();
								if (count($tagsImage) > 0 && $tagsImage[0] != '') {
									$tagImages = count($tagsImage);
								} else {
									$tagImages = count($request->get('image_type'));
								}

								for ($i = 0; $i < $tagImages; $i++) {
									$ImageTages = new PostTags();
									$ImageTages->setImageSize($ImageHeight);
									$ImageTages->setImageWidth($screenSize);
									$ImageTages->setPostID($ImageUpdate->getId());
									$ImageTages->setImageType($request->get('image_type'));
									$ImageTages->setImageName($ImageUpdate->getPostImageBack());
									$ImageTages->setTags($tagsImage[$i]);
									$ImageTages->setTagNote('');
									$ImageTages->setX_Axis($x_axisImage[$i]);
									$ImageTages->setY_Axis($y_axisImage[$i]);
									$em = $this->getDoctrine()->getManager();
									$em->persist($ImageTages);
									$em->flush();
								}
								echo json_encode(array('success' => 1, 'message' => 'Successfull'));
							} else {
								if ($request->get('image_type') == 'Back Right') {
									$imageBase = ($request->get('image_base'));
									if ($imageBase != '' && $imageBase != null) {
										$Image = $this->UploadFile($request->get('image_name'), $imageBase);
									}
									$ImageUpdate->setPostImageBackRight($Image);
									$em = $this->getDoctrine()->getManager();
									$em->persist($ImageUpdate);
									$em->flush();
									if (count($tagsImage) > 0 && $tagsImage[0] != '') {
										$tagImages = count($tagsImage);
									} else {
										$tagImages = count($request->get('image_type'));
									}

									for ($i = 0; $i < $tagImages; $i++) {
										$ImageTages = new PostTags();
										$ImageTages->setImageSize($ImageHeight);
										$ImageTages->setImageWidth($screenSize);
										$ImageTages->setPostID($ImageUpdate->getId());
										$ImageTages->setImageType($request->get('image_type'));
										$ImageTages->setImageName($ImageUpdate->getPostImageBackRight());
										$ImageTages->setTags($tagsImage[$i]);
										$ImageTages->setTagNote('');
										$ImageTages->setX_Axis($x_axisImage[$i]);
										$ImageTages->setY_Axis($y_axisImage[$i]);
										$em = $this->getDoctrine()->getManager();
										$em->persist($ImageTages);
										$em->flush();
									}
									echo json_encode(array('success' => 1, 'message' => 'Successfull'));
								} else {
									if ($request->get('image_type') == 'Right') {
										$imageBase = ($request->get('image_base'));
										if ($imageBase != '' && $imageBase != null) {
											$Image = $this->UploadFile($request->get('image_name'), $imageBase);
										}
										$ImageUpdate->setPostImageRight($Image);
										$em = $this->getDoctrine()->getManager();
										$em->persist($ImageUpdate);
										$em->flush();
										if (count($tagsImage) > 0 && $tagsImage[0] != '') {
											$tagImages = count($tagsImage);
										} else {
											$tagImages = count($request->get('image_type'));
										}

										for ($i = 0; $i < $tagImages; $i++) {
											$ImageTages = new PostTags();
											$ImageTages->setImageSize($ImageHeight);
											$ImageTages->setImageWidth($screenSize);
											$ImageTages->setPostID($ImageUpdate->getId());
											$ImageTages->setImageType($request->get('image_type'));
											$ImageTages->setImageName($ImageUpdate->getPostImageRight());
											$ImageTages->setTags($tagsImage[$i]);
											$ImageTages->setTagNote('');
											$ImageTages->setX_Axis($x_axisImage[$i]);
											$ImageTages->setY_Axis($y_axisImage[$i]);
											$em = $this->getDoctrine()->getManager();
											$em->persist($ImageTages);
											$em->flush();
										}
										echo json_encode(array('success' => 1, 'message' => 'Successfull'));
									} else {
										if ($request->get('image_type') == 'Front Right') {
											$imageBase = ($request->get('image_base'));
											if ($imageBase != '' && $imageBase != null) {
												$Image = $this->UploadFile($request->get('image_name'), $imageBase);
											}
											$ImageUpdate->setPostImageFrontRight($Image);
											$em = $this->getDoctrine()->getManager();
											$em->persist($ImageUpdate);
											$em->flush();
											if (count($tagsImage) > 0 && $tagsImage[0] != '') {
												$tagImages = count($tagsImage);
											} else {
												$tagImages = count($request->get('image_type'));
											}

											for ($i = 0; $i < $tagImages; $i++) {
												$ImageTages = new PostTags();
												$ImageTages->setImageSize($ImageHeight);
												$ImageTages->setImageWidth($screenSize);
												$ImageTages->setPostID($ImageUpdate->getId());
												$ImageTages->setImageType($request->get('image_type'));
												$ImageTages->setImageName($ImageUpdate->getPostImageFrontRight());
												$ImageTages->setTags($tagsImage[$i]);
												$ImageTages->setTagNote('');
												$ImageTages->setX_Axis($x_axisImage[$i]);
												$ImageTages->setY_Axis($y_axisImage[$i]);
												$em = $this->getDoctrine()->getManager();
												$em->persist($ImageTages);
												$em->flush();
											}
											echo json_encode(array('success' => 1, 'message' => 'Successfull'));
										} else {
											echo json_encode(
												array(
													'success' => 0,
													'message' => 'failure no image type: ' . $request->get('image_type')
												)
											);
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			echo json_encode(
				array('success' => 0, 'message' => 'failure no image base or image name' . print_r($ImageUpdate))
			);
		}
//die('testing');
	}

	/*     * *************************************************************************Image *Update   End******************************************* */

	/**
	 * @Route("/consumerloginwithpic", name="_consumerloginwithpic")
	 * @Template()
	 */
	/*     * ************************************************************************consumer login with pic  Begin ******************************************* */
	public function consumerloginwithpicAction(Request $user_email, Request $user_type, Request $customer_id)
	{

		/* customerid(serviceprovider id)*/
		/* $user_email  -new customer email*/
		$request = $this->getRequest();
		$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['userEmail' => $request->get('user_email'), 'userType' => $request->get('user_type')]
		);

		if ($Consumer != '' && $Consumer != null) {
			if ($Consumer->getUserEmail() != '') {
				$userEmail = $Consumer->getUserEmail();
			} else {
				$userEmail = '';
			}
			if ($Consumer->getUserType() != '') {
				$userType = $Consumer->getUserType();
			} else {
				$userType = '';
			}
			$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
				['toUserID' => $request->get('customer_id'), 'userID' => $Consumer->getId()]
			);
			if (!isset($userFOllows) && empty($userFOllows)) {
				$USERfollow = new UserFollow();
				$USERfollow->setUserID($request->get('customer_id'));
				$USERfollow->setToUserID($Consumer->getId());
				$USERfollow->setFollowStatus('1');
				$em = $this->getDoctrine()->getManager();
				$em->persist($USERfollow);
				$em->flush();
				$USERfollow = new UserFollow();
				$USERfollow->setUserID($Consumer->getId());
				$USERfollow->setToUserID($request->get('customer_id'));
				$USERfollow->setFollowStatus('1');
				$em = $this->getDoctrine()->getManager();
				$em->persist($USERfollow);
				$em->flush();
			}
			echo json_encode(
				array(
					'success' => 1,
					'message' => 'Successfull',
					'user_detail' => array(
						[
							'user_id' => $Consumer->getId(),
							'user_email' => $userEmail,
							'user_type' => $userType,
							'account_type' => 'old'
						]
					)
				)
			);
		} else {
			$Consumer1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['userEmail' => $request->get('user_email')]
			);
			//echo '<pre>';print_r($Consumer1);die;
			if ($Consumer1 == '') {
				if ($request->get('user_type') == '0') {
					$Newconsumer = new User();
					$Newconsumer->setUserEmail($request->get('user_email'));
					$Newconsumer->setUserType($request->get('user_type'));
					$Newconsumer->setIsNotification('1');
					$encoder = $this->container->get('my_user.manager')->getEncoder($Newconsumer);
					//$Newconsumer->setUserPassword(rand(999, 9999));
					$password = mt_rand(999, 9999);
					$Newconsumer->setUserPassword($encoder->encodePassword($password, $Newconsumer->getSalt()));
					$em = $this->getDoctrine()->getManager();
					$em->persist($Newconsumer);
					$em->flush();
//                    $email = (explode('@', $request->get('user_email')));
//           $message = \Swift_Message::newInstance()
//                    ->setSubject('Mail From HereCut App Team')
//                    ->setFrom('raj@webdior.com')
//                    ->setTo($request->get('user_email'))
//                    ->setBody("Hello " . strtoupper($email[0]) . ",
//       Welcome in HereCut App.
//                             
//       Thanks & Regards
//       HereCut App Team");
//            $this->get('mailer')->send($message);
					$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
						['userID' => $request->get('customer_id'), 'toUserID' => $Newconsumer->getId()]
					);
					if ($userFOllows == '' && $userFOllows == null) {
						$USERfollow = new UserFollow();
						$USERfollow->setUserID($request->get('customer_id'));
						$USERfollow->setToUserID($Newconsumer->getId());
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();
						$USERfollow = new UserFollow();
						$USERfollow->setUserID($Newconsumer->getId());
						$USERfollow->setToUserID($request->get('customer_id'));
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();
					}
					$email = explode('@', $Newconsumer->getUserEmail());


					$userName = ucwords($email[0]);
					$useremail = $Newconsumer->getUserEmail();
					//$password = $Newconsumer->getUserPassword();
					// $userFName = $UserData1->getUserFirstName();
					$subject = 'Welcome email';
					$body_text = 'Welcome email from HereCut';
					$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br> Your email is :' . $useremail . ' <br>and passwrod : ' . $password . '<br>Use this password to login to the HereCut App. We recommend you change your password once logged into the app by clicking Settings > Change Password  <br><br><br>Thanks <br>HereCut Team';
					$from = 'info@herecut.net';
					$fromName = 'HereCut';
					$headers = "From: " . $from . "\r\n";
					$headers .= "Reply-To: " . $from . "\r\n";
					//$headers .= "CC: test@example.com\r\n"; 
					/*    $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
          */
					$this->smtpEmail($useremail, $subject, $body_html);


					echo json_encode(
						array(
							'success' => 1,
							'message' => 'Successfull',
							'user_detail' => array(
								[
									'user_id' => $Newconsumer->getId(),
									'user_email' => $Newconsumer->getUserEmail(),
									'user_type' => $Newconsumer->getUserType(),
									'account_type' => 'new'
								]
							)
						)
					);
				} else {
					echo json_encode(array("success" => 0, "message" => "This email_id already registered"));
				}
			} else {
				if ($Consumer1->getUserType() == $request->get('user_type')) {

					echo json_encode(
						array(
							'success' => 1,
							'message' => 'Successfull',
							'user_detail' => array(
								[
									'user_id' => $Consumer1->getId(),
									'user_email' => $Consumer1->getUserEmail(),
									'user_type' => $Consumer1->getUserType(),
									'account_type' => 'old'
								]
							)
						)
					);
				} else {
					echo json_encode(
						array("success" => 0, "message" => "This email_id already registered as a Service Provider")
					);
				}
			}
		}
	}

	/*     * *************************************************************************consumer login with pic   End******************************************* */

	/**
	 * @Route("/useralbum", name="_useralbum")
	 * @Template()
	 */
	/*     * ************************************************************************user album Begin ******************************************* */
	public function useralbumAction(Request $user_id)
	{
		$request = $this->getRequest();
		$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
			['userID' => $request->get('user_id'), 'spPostStatus' => 1],
			array('id' => 'desc')
		);
		//echo '<pre>';print_r($Consumer);die;
		if ($Consumer != '' && $Consumer != null) {
			foreach ($Consumer as $ConsumerVal) {
				$sp_user_id = $ConsumerVal->getUserTagID();
				if ($ConsumerVal->getId() != '') {
					$AlbumID = $ConsumerVal->getId();
				} else {
					$AlbumID = '';
				}
				if ($ConsumerVal->getPostImageFront() != '' && $ConsumerVal->getPostImageFront() != null) {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageFront();
				} elseif ($ConsumerVal->getPostImageFrontLeft() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageFrontLeft();
				} elseif ($ConsumerVal->getPostImageLeft() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageLeft();
				} elseif ($ConsumerVal->getPostImageBackLeft() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageBackLeft();
				} elseif ($ConsumerVal->getPostImageBack() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageBack();
				} elseif ($ConsumerVal->getPostImageBackRight() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageBackRight();
				} elseif ($ConsumerVal->getPostImageRight() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageRight();
				} elseif ($ConsumerVal->getPostImageFrontRight() != '') {
					$userImage = $this->baseurl() . $ConsumerVal->getPostImageFrontRight();
				} else {
					$userImage = '';
				}
				$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
					['postID' => $AlbumID]
				);
//                           echo '<pre>';print_r($PostTags);
//                           echo 'hello';
				if ($PostTags != '') {
					$tag_status = '0';
					foreach ($PostTags as $PostTagsVal) {
						if ($PostTagsVal->getTags() != '') {
							$tag_status = '1';
						}
					}
				} else {
					$tag_status = '0';
				}
				if ($ConsumerVal->getPostCaption() != '') {
					$postCaption = $ConsumerVal->getPostCaption();
				} else {
					$postCaption = '';
				}
				if ($ConsumerVal->getPostNote() != '') {
					$postNote = $ConsumerVal->getPostNote();
				} else {
					$postNote = '';
				}
				$spuser = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $sp_user_id]);
				if (!empty($spuser->getCompanyName()) && ($spuser->getCompanyName() != null)) {
					$companyname = $spuser->getCompanyName();
				} else {
					$companyname = '';
				}
				$albums[] = array(
					'album_id' => $AlbumID,
					'album_image' => $userImage,
					'post_caption' => $postCaption,
					'tag_status' => $tag_status,
					'post_note' => $postNote,
					'company_name' => $companyname,
					'sp_user_id' => $sp_user_id
				);
			}


			echo json_encode(array('success' => 1, 'message' => 'Successfull', 'album_details' => $albums));
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure', 'album_details' => []));
		}
	}

	/*     * *************************************************************************user album   End******************************************* */

	/**
	 * @Route("/albumstatus", name="_albumstatus")
	 * @Template()
	 */
	/*     * ************************************************************************album status Begin ******************************************* */
	public function albumstatusAction(Request $user_id, Request $album_id, Request $post_status)
	{
		$request = $this->getRequest();
		$user_id = $request->get('user_id');
		$userTypeModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_id]);
		if ($userTypeModel != '') {
			if ($userTypeModel->getUserType() == '1') {

				$AlbumStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
					['id' => $request->get('album_id')]
				);
				//echo '<pre>';print_r($AlbumStatus);die;
				if ($AlbumStatus != '' && $AlbumStatus != null) {
//            if ($AlbumStatus->getPostStatus() == 1) {
					if ($request->get('post_status') == '0') {
						$AlbumStatus->setPostStatus($request->get('post_status'));
						$AlbumStatus->setSpPostStatus('0');
						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status public',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					} elseif ($request->get('post_status') == '1') {

						$AlbumStatus->setPostStatus($request->get('post_status'));

						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status private',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					}
				} else {

					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {
				$AlbumStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
					['id' => $request->get('album_id')]
				);

				if ($AlbumStatus != '' && $AlbumStatus != null) {
//            if ($AlbumStatus->getPostStatus() == 1) {
					if ($request->get('post_status') == '0') {
						$AlbumStatus->setPostStatus($request->get('post_status'));
						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status public',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					} elseif ($request->get('post_status') == '1') {

						$AlbumStatus->setPostStatus($request->get('post_status'));
						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status private',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					}
				} else {

					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		}
	}

	/*     * *************************************************************************album status   End******************************************* */

	/**
	 * @Route("/albumimages", name="_albumimages")
	 * @Template()
	 */
	/*     * ************************************************************************album images Begin ******************************************* */
	public function albumimagesAction(Request $user_id, Request $album_id)
	{
		$imageType2 = '';
		$imageType3 = '';
		$imageType4 = '';
		$imageType5 = '';
		$imageType6 = '';
		$imageType7 = '';
		$imageType8 = '';
		$imageType1 = '';
		$request = $this->getRequest();

		$AlbumImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
			['id' => $request->get('album_id')]
		);


		if ($AlbumImage != '' && $AlbumImage != null) {
			$albumID = $AlbumImage->getId();

			//find tag status
			$tag_status = '';
			$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
				['postID' => $albumID]
			);
			if ($PostTags != '') {
				foreach ($PostTags as $PostTagsVal) {
					if ($PostTagsVal->getTags() != '') {
						$tag_status = '1';
					} else {
						$tag_status = '0';
					}
				}
			} else {
				$tag_status = '0';
			}

			$UserRated = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
				['postID' => $albumID]
			);
			if ($UserRated != '' && $UserRated != null) {
				if ($UserRated->getUserRating() != '' || $UserRated->getUserReviews() != '') {
					$ratestatus = '1';
						if ($UserRated->getUserReviews() != '' && $UserRated->getUserReviews() != null) {
							$reviews = $UserRated->getUserReviews();
						}else{
							$reviews = '';
						}

				} else {
					$ratestatus = '0';
				}
			} else {
				$ratestatus = '0';
			}
			if ($AlbumImage->getPostStatus() != '') {
				$post_status = $AlbumImage->getPostStatus();
			} else {
				$post_status = '';
			}

			if ($AlbumImage->getPostCaption() != '') {
				$postcaption = $AlbumImage->getPostCaption();
			} else {
				$postcaption = '';
			}
			if ($AlbumImage->getPostNote() != '') {
				$postnote = $AlbumImage->getPostNote();
			} else {
				$postnote = '';
			}


			$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $AlbumImage->getUserID()]
			);


			$User1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $AlbumImage->getUserTagID()]
			);

			if ($User->getUserType() == '0' && $User1->getUserType() == '1') {
				$spPostStatus = $AlbumImage->getSpPostStatus();
				if ($User->getCompanyName() != '' && $User->getCompanyName() != null) {
					$companyName = $User->getCompanyName();
				} else {
					$companyName = 'SELF';
				}
				if ($User->getUserAddress() != '') {
					$location = $User->getUserAddress();
				} else {
					$location = '';
				}
				$sp_id = $User1->getId();
			} else {
				if ($User->getUserType() == '1' && $User1->getUserType() == '0') {
					$spPostStatus = $AlbumImage->getSpPostStatus();
					if ($User1->getCompanyName() != '' && $User1->getCompanyName() != null) {
						$companyName = $User1->getCompanyName();
					} else {
						$companyName = 'SELF';
					}
					if ($User1->getUserAddress() != '') {
						$location = $User1->getUserAddress();
					} else {
						$location = '';
					}
					$sp_id = $User->getId();
				} else {
					if ($User->getUserType() == '0' && $User1->getUserType() == '0') {
						$spPostStatus = '';
						if ($User1->getCompanyName() != '' && $User1->getCompanyName() != null) {
							$companyName = $User1->getCompanyName();
						} else {
							$companyName = 'SELF';
						}
						if ($User1->getUserAddress() != '') {
							$location = $User1->getUserAddress();
						} else {
							$location = '';
						}
						$sp_id = $User->getId();
					}
				}
			}
			$userRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
				['postID' => $albumID]
			);

			if ($userRating != '' && $userRating != null) {
				if ($userRating->getUserRating() != '') {
					$rating = $userRating->getUserRating();
					$reviews = $userRating->getUserReviews();
				} else {
					$rating = '';
					$reviews = '';
				}
			} else {
				$rating = '';
				$reviews = '';
			}
			$Images = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
				['postID' => $request->get('album_id')]
			);

			if ($Images != '' && $Images != null) {
				foreach ($Images as $ImagesVal) {
					$imageType[] = $ImagesVal->getImageType();
					if ($ImagesVal->getImageType() == 'Front') {

						$imageType1[] = $ImagesVal->getImageType();
					}
					if ($ImagesVal->getImageType() == 'Front Left') {
						//die('ok');
						$imageType2[] = $ImagesVal->getImageType();
						//echo '<pre>';print_r($imageType2);
					}

					if ($ImagesVal->getImageType() == 'Back') {
						$imageType3[] = $ImagesVal->getImageType();
					}
					if ($ImagesVal->getImageType() == 'Left') {
						$imageType4[] = $ImagesVal->getImageType();
					}
					if ($ImagesVal->getImageType() == 'Back Left') {
						$imageType5[] = $ImagesVal->getImageType();
					}
					if ($ImagesVal->getImageType() == 'Back Right') {
						$imageType6[] = $ImagesVal->getImageType();
					}
					if ($ImagesVal->getImageType() == 'Right') {
						$imageType7[] = $ImagesVal->getImageType();
					}
					if ($ImagesVal->getImageType() == 'Front Right') {
						$imageType8[] = $ImagesVal->getImageType();
					}
				}
				if (count($imageType1) > 0 && $imageType1 != null) {
					$FImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType1, 'postID' => $albumID]
					);

					foreach ($FImage as $FImageVal) {
						$ImageName[] = $this->baseurl() . $FImageVal->getImageName();
						$ImageTags[] = $FImageVal->getTags();
						$Imagenotes[] = $FImageVal->getTagNote();
						$ImageXAxis[] = $FImageVal->getX_Axis();
						$ImageYAxis[] = $FImageVal->getY_Axis();
						$ImageHeight[] = $FImageVal->getImageSize();
						$ImageWidth[] = $FImageVal->getImageWidth();
						$ImageType = 'Front';
						$AlbumID = $FImageVal->getPostID();
					}
					for ($j = 0; $j < count($ImageName); $j++) {

						$images[] = array(
							'image_name' => $ImageName[$j],
							'image_tags' => $ImageTags[$j],
							'image_note' => $Imagenotes[$j],
							'image_xaxis' => $ImageXAxis[$j],
							'image_yaxis' => $ImageYAxis[$j],
							'image_height' => $ImageHeight[$j],
							'image_width' => $ImageWidth[$j],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images = [];
				}
				if (count($imageType2) > 0 && $imageType2 != null) {
					$FLImage1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType2, 'postID' => $albumID]
					);

					foreach ($FLImage1 as $FLImage1Val) {
						//echo '<pre>';print_r($FLImageVal->getPostID());
						$AlbumID = $FLImage1Val->getPostID();
						$ImageName2[] = $this->baseurl() . $FLImage1Val->getImageName();
						$ImageTags2[] = $FLImage1Val->getTags();
						$Imagenotes2[] = $FLImage1Val->getTagNote();
						$ImageXAxis2[] = $FLImage1Val->getX_Axis();
						$ImageYAxis2[] = $FLImage1Val->getY_Axis();
						$ImageHeight[] = $FLImage1Val->getImageSize();
						$ImageWidth[] = $FLImage1Val->getImageWidth();
						$ImageType = 'Front Left';
					}
					for ($k = 0; $k < count($imageType2); $k++) {

						$images1[] = array(
							'image_name' => $ImageName2[$k],
							'image_tags' => $ImageTags2[$k],
							'image_note' => $Imagenotes2[$k],
							'image_xaxis' => $ImageXAxis2[$k],
							'image_yaxis' => $ImageYAxis2[$k]
						,
							'image_height' => $ImageHeight[$k],
							'image_width' => $ImageWidth[$k],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {

					$images1 = [];
				}
				if (count($imageType3) > 0 && $imageType3 != null) {
					$BImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType3, 'postID' => $albumID]
					);

					foreach ($BImage as $BImageVal) {
						$ImageName3[] = $this->baseurl() . $BImageVal->getImageName();
						$ImageTags3[] = $BImageVal->getTags();
						$Imagenotes3[] = $BImageVal->getTagNote();
						$ImageXAxis3[] = $BImageVal->getX_Axis();
						$ImageYAxis3[] = $BImageVal->getY_Axis();
						$ImageHeight[] = $BImageVal->getImageSize();
						$ImageWidth[] = $BImageVal->getImageWidth();
						$ImageType = 'Back';
						$AlbumID = $BImageVal->getPostID();
					}//echo '<pre>';print_r($ImageName);die;
					for ($l = 0; $l < count($ImageName3); $l++) {
						$images2[] = array(
							'image_name' => $ImageName3[$l],
							'image_tags' => $ImageTags3[$l],
							'image_note' => $Imagenotes3[$l],
							'image_xaxis' => $ImageXAxis3[$l],
							'image_yaxis' => $ImageYAxis3[$l],
							'image_height' => $ImageHeight[$l],
							'image_width' => $ImageWidth[$l],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images2 = [];
				}
				if (count($imageType4) > 0 && $imageType4 != null) {
					$LImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType4, 'postID' => $albumID]
					);
					foreach ($LImage as $LImageVal) {
						$ImageName4[] = $this->baseurl() . $LImageVal->getImageName();
						$ImageTags4[] = $LImageVal->getTags();
						$Imagenotes4[] = $LImageVal->getTagNote();
						$ImageXAxis4[] = $LImageVal->getX_Axis();
						$ImageYAxis4[] = $LImageVal->getY_Axis();
						$ImageHeight[] = $LImageVal->getImageSize();
						$ImageWidth[] = $LImageVal->getImageWidth();
						$ImageType = 'Left';
						$AlbumID = $LImageVal->getPostID();
					}
					for ($m = 0; $m < count($ImageName4); $m++) {
						$images3[] = array(
							'image_name' => $ImageName4[$m],
							'image_tags' => $ImageTags4[$m],
							'image_note' => $Imagenotes4[$m],
							'image_xaxis' => $ImageXAxis4[$m],
							'image_yaxis' => $ImageYAxis4[$m],
							'image_height' => $ImageHeight[$m],
							'image_width' => $ImageWidth[$m],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images3 = [];
				}
				if (count($imageType5) > 0 && $imageType5 != null) {
					$BLImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType5, 'postID' => $albumID]
					);
					foreach ($BLImage as $BLImageVal) {
						$ImageName5[] = $this->baseurl() . $BLImageVal->getImageName();
						$ImageTags5[] = $BLImageVal->getTags();
						$Imagenotes5[] = $BLImageVal->getTagNote();
						$ImageXAxis5[] = $BLImageVal->getX_Axis();
						$ImageYAxis5[] = $BLImageVal->getY_Axis();
						$ImageHeight[] = $BLImageVal->getImageSize();
						$ImageWidth[] = $BLImageVal->getImageWidth();
						$ImageType = 'Back Left';
						$AlbumID = $BLImageVal->getPostID();
					}
					for ($n = 0; $n < count($ImageName5); $n++) {
						$images4[] = array(
							'image_name' => $ImageName5[$n],
							'image_tags' => $ImageTags5[$n],
							'image_note' => $Imagenotes5[$n],
							'image_xaxis' => $ImageXAxis5[$n],
							'image_yaxis' => $ImageYAxis5[$n],
							'image_height' => $ImageHeight[$n],
							'image_width' => $ImageWidth[$n],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images4 = [];
				}
				if (count($imageType6) > 0 && $imageType6 != null) {
					$BRImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType6, 'postID' => $albumID]
					);
					foreach ($BRImage as $BRImageVal) {
						$ImageName6[] = $this->baseurl() . $BRImageVal->getImageName();
						$ImageTags6[] = $BRImageVal->getTags();
						$Imagenotes6[] = $BRImageVal->getTagNote();
						$ImageXAxis6[] = $BRImageVal->getX_Axis();
						$ImageYAxis6[] = $BRImageVal->getY_Axis();
						$ImageHeight[] = $BRImageVal->getImageSize();
						$ImageWidth[] = $BRImageVal->getImageWidth();
						$ImageType = 'Back Right';
						$AlbumID = $BRImageVal->getPostID();
					}
					for ($o = 0; $o < count($ImageName6); $o++) {
						$images5[] = array(
							'image_name' => $ImageName6[$o],
							'image_tags' => $ImageTags6[$o],
							'image_note' => $Imagenotes6[$o],
							'image_xaxis' => $ImageXAxis6[$o],
							'image_yaxis' => $ImageYAxis6[$o],
							'image_height' => $ImageHeight[$o],
							'image_width' => $ImageWidth[$o],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images5 = [];
				}
				if (count($imageType7) > 0 && $imageType7 != null) {
					$BImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType7, 'postID' => $albumID]
					);
					foreach ($BImage as $BImageVal) {
						$ImageName7[] = $this->baseurl() . $BImageVal->getImageName();
						$ImageTags7[] = $BImageVal->getTags();
						$Imagenotes7[] = $BImageVal->getTagNote();
						$ImageXAxis7[] = $BImageVal->getX_Axis();
						$ImageYAxis7[] = $BImageVal->getY_Axis();
						$ImageHeight[] = $BImageVal->getImageSize();
						$ImageWidth[] = $BImageVal->getImageWidth();
						$ImageType = 'Right';
						$AlbumID = $BImageVal->getPostID();
					}
					for ($p = 0; $p < count($ImageName7); $p++) {
						$images6[] = array(
							'image_name' => $ImageName7[$p],
							'image_tags' => $ImageTags7[$p],
							'image_note' => $Imagenotes7[$p],
							'image_xaxis' => $ImageXAxis7[$p],
							'image_yaxis' => $ImageYAxis7[$p],
							'image_height' => $ImageHeight[$p],
							'image_width' => $ImageWidth[$p],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images6 = [];
				}
				if (count($imageType8) > 0 && $imageType8 != null) {
					$FRImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['imageType' => $imageType8, 'postID' => $albumID]
					);
					foreach ($FRImage as $FRImageVal) {
						$ImageName8[] = $this->baseurl() . $FRImageVal->getImageName();
						$ImageTags8[] = $FRImageVal->getTags();
						$Imagenotes8[] = $FRImageVal->getTagNote();
						$ImageXAxis8[] = $FRImageVal->getX_Axis();
						$ImageYAxis8[] = $FRImageVal->getY_Axis();
						$ImageHeight[] = $FRImageVal->getImageSize();
						$ImageWidth[] = $FRImageVal->getImageWidth();
						$ImageType = 'Front Right';
						$AlbumID = $FRImageVal->getPostID();
					}
					for ($q = 0; $q < count($ImageName8); $q++) {

						$images7[] = array(
							'image_name' => $ImageName8[$q],
							'image_tags' => $ImageTags8[$q],
							'image_note' => $Imagenotes8[$q],
							'image_xaxis' => $ImageXAxis8[$q],
							'image_yaxis' => $ImageYAxis8[$q],
							'image_height' => $ImageHeight[$q],
							'image_width' => $ImageWidth[$q],
							'image_type' => $ImageType,
							'album_id' => $AlbumID
						);
					}
				} else {
					$images7 = [];
				}
				//echo '<pre>';print_r($images);die;
				$image_set[] = array(
					'front' => $images,
					'front left' => $images1,
					'back' => $images2,
					'left' => $images3,
					'back left' => $images4,
					'back right' => $images5,
					'right' => $images6,
					'front right' => $images7
				);

//echo '<pre>';print_r($image_set);
			} else {
				$image_set = [];
			}
			$user_id = $AlbumImage->getUserID();
			$user_id1 = $AlbumImage->getUserTagID();
			if ($user_id == $user_id1) {
				$user_iD = '';
				$user_name = '';
			} else {
				$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_id]);
				if (!empty($users)) {
					if ($users->getUserType() == '1') {
						$user_iD = $users->getId();
						if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
							$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
						} else {
							$user_name = '';
						}
					} else {
						$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $user_id1]
						);
						if (!empty($users)) {
							if ($users->getUserType() == '1') {
								$user_iD = $users->getId();
								if (!empty($users->getUserFirstName() || $users->getUserLastName())) {
									$user_name = $users->getUserFirstName() . ' ' . $users->getUserLastName();
								} else {
									$user_name = '';
								}
							} else {
								$user_iD = '';
								$user_name = '';
							}
						} else {
							$user_iD = '';
							$user_name = '';
						}
					}
				} else {
					$user_iD = '';
					$user_name = '';
				}
			}
			echo json_encode(
				array(
					'success' => 1,
					'message' => 'Succesful',
					'user_id' => $user_iD,
					'user_name' => $user_name,
					'sp_post_status' => $spPostStatus,
					'company_name' => $companyName,
					'location' => $location,
					'album_id' => $albumID,
					'sp_id' => $sp_id,
					'post_caption' => $postcaption,
					'post_note' => $postnote,
					'image_set' => $image_set,
					'rating' => $rating,
					'post_status' => $post_status,
					'rate_status' => $ratestatus,
					'reviews' => $reviews,
					'tag_status' => $tag_status
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************album images   End******************************************* */

	/**
	 * @Route("/checkpost", name="_checkpost")
	 * @Template()
	 */
	/*     * ************************************************************************check post Begin ******************************************* */
	public function checkpostAction(Request $user_id)
	{
		$request = $this->getRequest();
		$ServiceProvider = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
			['id' => $request->get('user_id'), 'userType' => 1]
		);
		if (!empty($ServiceProvider)) {
			$postStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
				['userID' => $request->get('user_id'), 'postStatus' => 1]
			);

			if ($postStatus != '' && $postStatus != null) {


				echo json_encode(array('success' => 1, 'message' => 'succesfull'));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			$flag = '';
			$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
				['userID' => $request->get('user_id')],
				array('id' => 'desc')
			);
			//  echo '<pre>';print_r($Consumer);die;
			if ($Consumer != '' && $Consumer != null) {
				foreach ($Consumer as $Consumer1val) {
					if ($Consumer1val->getUserID() != $Consumer1val->getUserTagID()) {


						$ConsumerRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $Consumer1val->getId()]
						);

						if (!empty($ConsumerRate)) {


							$flag = 1;
						} else {
							$flag = 2;

						}
					}
				}


				if ($flag == '2') {
					echo json_encode(array('success' => 1, 'message' => 'succesfull'));

				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		}
	}

	/*     * *************************************************************************check post   End******************************************* */

	/**
	 * @Route("/userrate", name="_userrate")
	 * @Template()
	 */
	/*     * ************************************************************************user Rating Begin ******************************************* */
	public function userrateAction(
		Request $user_id,
		Request $sp_user_id,
		Request $rate,
		Request $reviews,
		Request $post_id
	) {
		$request = $this->getRequest();
		$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
			[
				'fromUserID' => $request->get('user_id'),
				'toUserID' => $request->get('sp_user_id'),
				'postID' => $request->get('post_id')
			]
		);

		if ($userRate != '' && $userRate != null) {

			echo json_encode(array('success' => 0, 'message' => 'consumer  rates already of this user'));
		} else {

			$userType = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $request->get('sp_user_id')]
			);
			if (!empty($userType)) {
				if ($userType->getUserType() == '1') {

					$rates = new UserRating();
					$rates->setFromUserID($request->get('user_id'));
					$rates->setToUserID($request->get('sp_user_id'));
					if ($request->get('rate') == '0.0' || '0') {
						$rates->setUserRating('');
					} else {
						$rates->setUserRating($request->get('rate'));
					}
					$rates->setUserReviews($request->get('reviews'));
					$rates->setPostID($request->get('post_id'));
					$em = $this->getDoctrine()->getManager();
					$em->persist($rates);
					$em->flush();

					$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $rates->getFromUserID()]
					);
					$user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $rates->getToUserID()]
					);

					if ($user != '' && $user != null) {
						if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
							$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
						} else {
							$userName = '';
						}
					} else {
						$userName = '';
					}

					$userName = ucwords($user1->getUserFirstName());
					$useremail = $user1->getUserEmail();
					// $password = $Newconsumer->getUserPassword();
					$userFName = ucwords($user->getUserFirstName());
					$subject = 'Rate/Reviews SPPS';
					$body_text = 'Rate/Reviews SPPS';
					$body_html = 'Hello ' . $userName . ', <br><br> ' . $userFName . ' has Reviewed your Service Provider Picture Set (SPPS). <br><br>Thank You <br>HereCut Team';
					$from = 'info@herecut.net';
					$fromName = 'HereCut';
					$headers = "From: " . $from . "\r\n";
					$headers .= "Reply-To: " . $from . "\r\n";
					//$headers .= "CC: test@example.com\r\n"; 
					/*  $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);
            */
					$this->smtpEmail($useremail, $subject, $body_html);


					/* NOTIFICATION FUNCTION START */
					// $user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $request->get('user_id')]);
					if (!empty($user1)) {
						//die('ok');
						if ($user1->getIsNotification() == '1') {
							$msg = 'Rate';
							$IDs = $request->get('sp_user_id');
							$submsg = $user->getUserFirstName() . ' ' . 'Reviewed your album';

							$usenotification = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:Notification"
							)->findBy(['userID' => $request->get('sp_user_id')]);
							if ($usenotification != '' && $usenotification != null) {
								foreach ($usenotification as $usenotificationVal) {
									if ($usenotificationVal->getDeviceType() == '0') {

										$registatoin_ids = $usenotificationVal->getDeviceID();


										$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
									}
								}
							}
							$notificationModel = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:NotificationMessage"
							)->findOneBy(
								[
									'userID' => $request->get('user_id'),
									'toUserID' => $request->get('sp_user_id'),
									'notificationTitle' => 'Rate'
								]
							);

							if ($notificationModel == '' && $notificationModel == null) {

								$notifyMsg = new NotificationMessage();
								$notifyMsg->setNotificationTitle($msg);
								$notifyMsg->setNotificationMessage($submsg);
								$notifyMsg->setUserID($request->get('user_id'));
								$notifyMsg->setToUserID($request->get('sp_user_id'));
								$em = $this->getDoctrine()->getManager();
								$em->persist($notifyMsg);
								$em->flush();
							} else {
								$em = $this->getDoctrine()->getEntityManager();
								$em->remove($notificationModel);
								$em->flush();
								$notifyMsg = new NotificationMessage();
								$notifyMsg->setNotificationTitle($msg);
								$notifyMsg->setNotificationMessage($submsg);
								$notifyMsg->setUserID($request->get('user_id'));
								$notifyMsg->setToUserID($request->get('sp_user_id'));
								$em = $this->getDoctrine()->getManager();
								$em->persist($notifyMsg);
								$em->flush();
							}

						}
					}

					/*  NOTIFICATION FUNCTION END  */

					echo json_encode(array('success' => 1, 'message' => 'successfull'));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		}
	}

	/*     * *************************************************************************User Rating   End******************************************* */

	/**
	 * @Route("/userreviewsrate", name="_userreviewsrate")
	 * @Template()
	 */
	/*     * ************************************************************************check post Begin ******************************************* */
	public function userreviewsrateAction(Request $user_id, Request $counter)
	{
		$request = $this->getRequest();
		$limitset = 10;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$userReviewsRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
			['toUserID' => $request->get('user_id')],
			array('id' => 'asc'),
			$max_num,
			$min_num
		);
//echo '<pre>';print_r($userReviewsRate);die;
		if ($userReviewsRate != '' && $userReviewsRate != null) {
			foreach ($userReviewsRate as $userReviewsRateVal) {
				$userID = $userReviewsRateVal->getFromUserID();
				$rate = $userReviewsRateVal->getUserRating();
				$reviews = $userReviewsRateVal->getUserReviews();
				$album_id = $userReviewsRateVal->getPostID();
				$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
				if ($user != '' && $user != null) {
					if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
						$user_name = $user->getUserFirstName() . ' ' . $user->getUserLastName();
					} else {
						$user_name = '';
					}
					if ($user->getUserProfileImage() != '' && $user->getUserProfileImage() > 0) {
						$user_profile = $this->baseurl() . $user->getUserProfileImage();
					} else {
						$user_profile = $this->baseurl() . 'defaultprofile.png';
					}
				} else {
					$user = '';
				}
				$userAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
					['id' => $album_id]
				);
				if ($userAlbum != '' && $userAlbum != null) {
					if ($userAlbum->getPostImageFront() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageFront();
					} elseif ($userAlbum->getPostImageFrontLeft() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontLeft();
					} elseif ($userAlbum->getPostImageLeft() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageLeft();
					} elseif ($userAlbum->getPostImageBackLeft() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageBackLeft();
					} elseif ($userAlbum->getPostImageBack() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageBack();
					} elseif ($userAlbum->getPostImageBackRight() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageBackRight();
					} elseif ($userAlbum->getPostImageRight() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageRight();
					} elseif ($userAlbum->getPostImageFrontRight() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontRight();
					} else {
						$userprofile = '';
					}
					$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['postID' => $userAlbum->getId()]
					);
					if ($PostTags != '') {
						foreach ($PostTags as $PostTagsVal) {
							if ($PostTagsVal->getTags() != '') {
								$tag_status = '1';
							} else {
								$tag_status = '0';
							}
						}
					} else {
						$tag_status = '0';
					}
					$Rating[] = $rate;
					$count = count($Rating);
					$rating = array_sum($Rating) / $count;
					$ratvalues = number_format((float)$rating, 1, '.', '');
					$userrateReview[] = array(
						'user_id' => $userID,
						'tag_status' => $tag_status,
						'user_name' => $user_name,
						'profile_image' => $user_profile,
						'user_rate' => $rate,
						'user_reviews' => $reviews
					,
						'album_id' => $album_id,
						'album_image' => $userprofile
					);
				} else {
					$userrateReview = [];
					$ratvalues = '';
				}
			}

			echo json_encode(
				array(
					'success' => 1,
					'message' => 'successfull',
					'avg_rate' => $ratvalues,
					'user_rateReviews' => $userrateReview
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************check post   End******************************************* */

//SELECT tags, count( tags )
//FROM `post_tags`
//GROUP BY tags
	/**
	 * @Route("/mostusedtags", name="_mostusedtags")
	 * @Template()
	 */
	/*     * ************************************************************************most used Tags Begin ******************************************* */
	public function mostusedtagsAction()
	{
		$tags = array();
		$request = $this->getRequest();
		$userID = $request->get('user_id');
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$result = $conn->query(
			//"select tags from post_tags  GROUP BY tags order by 'count(tags)' desc"
			//"select tags from post_tags left join post on post_tags.postID=post.postID where post.userTagID=$userID GROUP BY tags order by 'count(tags)' desc"
			//this query will get all tags from user id passed whether they were logged in as themselves at the time of tagging or not
			"select tags from post_tags left join post on post_tags.postID=post.postID where ((post.userTagID=$userID or post.userID=$userID) and (post_tags.tags != '' or post_tags.tags != ' ')) GROUP BY tags order by 'count(tags)' desc"
		)->fetchAll();

		if ($result != '' && $result != null) {
			foreach ($result as $resultVal) {
				if ($resultVal['tags'] != '') {
					$tags[] = (['tags_name' => $resultVal['tags']]);
				}
			}
			echo json_encode(array('success' => 1, 'message' => 'succesfull', 'tags' => $tags));
			return [];
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
			return [];
		}
	}

	/*     * ******************************************************************************Most Used Tag**************************************** */

	/**
	 * @Route("/termscondition", name="_termscondition")
	 * @Template()
	 */
	/*     * ************************************************************************Terms &condtions Begin ******************************************* */
	public function termsconditionAction()
	{

		$termcondtions = $this->getDoctrine()->getRepository("AcmeDemoBundle:Cms")->findBy(
			['cmsTitle' => 'Terms&Condtions']
		);


		if ($termcondtions != '' && $termcondtions != null) {
			foreach ($termcondtions as $termcondtionsVal) {
				$terms = $termcondtionsVal->geCmsDescription();
			}
			echo json_encode(array('success' => 1, 'message' => 'succesfull', 'terms&conditions' => $terms));

			return [];
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * **************************************************************************Terns & Conditions End******************************************** */

	/**
	 * @Route("/aboutus", name="_aboutus")
	 * @Template()
	 */
	/*     * ************************************************************************About US Begin ******************************************* */
	public function aboutusAction()
	{

		$Aboutus = $this->getDoctrine()->getRepository("AcmeDemoBundle:Cms")->findBy(['cmsTitle' => 'AboutUs']);


		if ($Aboutus != '' && $Aboutus != null) {
			foreach ($Aboutus as $AboutusVal) {
				$aboutus = $AboutusVal->geCmsDescription();
			}
			echo json_encode(array('success' => 1, 'message' => 'succesfull', 'about_us' => $aboutus));

			return [];
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * ***************************************************************About Us End*************************************************** */

	/**
	 * @Route("/privacypolicy", name="_privacypolicy")
	 * @Template()
	 */
	/*     * ************************************************************************Privacy Policy Begin ******************************************* */
	public function privacypolicyAction()
	{

		$Privacypolicy = $this->getDoctrine()->getRepository("AcmeDemoBundle:Cms")->findBy(
			['cmsTitle' => 'PrivacyPolicy']
		);


		if ($Privacypolicy != '' && $Privacypolicy != null) {
			foreach ($Privacypolicy as $PrivacypolicyVal) {
				$privacy_policy = $PrivacypolicyVal->geCmsDescription();
			}
			echo json_encode(array('success' => 1, 'message' => 'succesfull', 'privacy_policy' => $privacy_policy));

			return [];
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * **************************************************************************Privacy Policy End******************************************** */

	/**
	 * @Route("/reportproblem", name="_reportproblem")
	 * @Template()
	 */
	/*     * ************************************************************************Report problem Begin ******************************************* */
	public function reportproblemAction(Request $user_id, Request $report_desc)
	{
		$request = $this->getRequest();
		$report = new ReportProblem();
		$report->setUserID($request->get('user_id'));
		$report->setReportDescription($request->get('report_desc'));
		$em = $this->getDoctrine()->getManager();
		$em->persist($report);
		$em->flush();
		echo json_encode(array('success' => 1, 'message' => 'succesfull'));
		
		//send email to support
		$user_id = $request->get('user_id');
		$desc = $request->get('report_desc');
		$subject = 'Reported Problem From HereCut';
		$body_html = 'Reported Problem with app: ' . $desc . ',<br><br> From:' . $user_id . '<br><br>Thanks <br>HereCut Team';
		$from = 'support@herecut.net';
		$fromName = 'HereCut';
		$headers = "From: " . $from . "\r\n";
		$headers .= "Reply-To: " . $from . "\r\n";
		$this->smtpEmail($from, $subject, $body_html);

	}

	/*     * **************************************************************************Report problem End******************************************** */

	/**
	 * @Route("/resendpassword", name="_resendpassword")
	 * @Template()
	 */
	/*     * ************************************************************************Report problem Begin ******************************************* */
	public function resendpasswordAction(Request $user_email)
	{
		$request = $this->getRequest();
		$resendPassword = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['userEmail' => $request->get('user_email')]
		);
		if ($resendPassword != '' && $resendPassword != null) {
			$userEmail = explode('@', $request->get('user_email'));

			$userName = ucwords($userEmail[0]);
			$useremail = $resendPassword->getUserEmail();
			$password = $resendPassword->getUserPassword();
			$userFName = ucwords($resendPassword->getUserFirstName());
			$subject = 'Resend Password';
			$body_text = 'Resend Password from HereCut';
			$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br> Your email is:' . $useremail . '<br>and password :' . $password . '<br> Use this password for Login in HereCut App.<br><br><br>Thanks <br>HereCut Team';
			$from = 'info@herecut.net';
			$fromName = 'HereCut';
			$headers = "From: " . $from . "\r\n";
			$headers .= "Reply-To: " . $from . "\r\n";
			//$headers .= "CC: test@example.com\r\n"; 
			/*    $headers .= "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
                mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/
			$this->smtpEmail($useremail, $subject, $body_html);


			echo json_encode(array('success' => 1, 'message' => 'succesfull password send on your mail'));
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * **************************************************************************Report problem End******************************************** */

	/**
	 * @Route("/changepassword", name="_changepassword")
	 * @Template()
	 */
	/*     * ************************************************************************Report problem Begin ******************************************* */
	public function changepasswordAction(Request $user_email, Request $old_password, Request $new_password)
	{
		$logger = $this->get('logger');
		$request = $this->getRequest();
		//$changePassword = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
		//	['userEmail' => $request->get('user_email'), 'userPassword' => $request->get('old_password')]
		//);
		$changePassword_pre = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['userEmail' => $request->get('user_email')]);
		$old_password = $this->ClearText($request->get('old_password'));
		$password = $changePassword_pre->getUserPassword();

			if (password_verify($old_password, $password)) {
				$logger->info('LoginPass: Valid password.');
				$changePassword = $changePassword_pre;
			} else {
				$logger->info('LoginPass: Invalid password.');
				$changePassword = "";
			}

		if ($changePassword != '' && $changePassword != null) {
			$em = $this->getDoctrine()->getManager();
			$encoder = $this->container->get('my_user.manager')->getEncoder($changePassword);
			//$changePassword->setUserPassword($request->get('new_password'));
			$password = $request->get('new_password');
			$changePassword->setUserPassword($encoder->encodePassword($password, $changePassword->getSalt()));
			$em->persist($changePassword);
			$em->flush();
			$userEmail = explode('@', $request->get('user_email'));

			$userName = ucwords($userEmail[0]);
			$useremail = $changePassword->getUserEmail();
			//$password = $changePassword->getUserPassword();
			$userFName = ucwords($changePassword->getUserFirstName());
			$subject = 'Change Password';
			$body_text = 'Change Password from HereCut';
			$body_html = 'Hello ' . $userName . ',<br><br>Your email/username is:' . $useremail . '<br> This is an alert, that someone has changed your passsword. If this was not initiated by you, please submit a support request. <br><br><br>Thank you <br>HereCut Team';
			$from = 'info@herecut.net';
			$fromName = 'HereCut';
			$headers = "From: " . $from . "\r\n";
			$headers .= "Reply-To: " . $from . "\r\n";
			//$headers .= "CC: test@example.com\r\n"; 
			/* $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/
			$this->smtpEmail($useremail, $subject, $body_html);

			echo json_encode(array('success' => 1, 'message' => 'succesfull password send on your mail'));
		} else {
			echo json_encode(array('success' => 0, 'message' => 'Old password does not match'));
		}
	}

	/*     * **************************************************************************Report problem End******************************************** */

	/**
	 * @Route("/birthdaynotification", name="_birthdaynotification")
	 * @Template()
	 */
	/*     * ************************************************************************Report problem Begin ******************************************* */
	public function birthdaynotificationAction()
	{
		$request = $this->getRequest();
		/* BIRTHDAY  NOTIFICATION FUNCTION START */
		$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(['userType' => '0']);
		if ($User != '' && $User != null) {
			foreach ($User as $UserVal) {
				if (($UserVal->getUserFirstName() || $UserVal->getUserLastName()) != '') {
					$userName = $UserVal->getUserFirstName() . ' ' . $UserVal->getUserLastName();
				} else {
					$userName = '';
				}
				if ($UserVal->getUserDOB() != '') {
					$datetime = explode('-', $UserVal->getUserDOB());
					$Dob = ($datetime[2] . '-' . $datetime[1]);
				} else {
					$Dob = '';
				}
				$date = date('d-m');

				if ($Dob == $date) {

					$Usercust = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserCustomerRelation")->findOneBy(
						['userID' => $UserVal->getId()]
					);

					if ($Usercust != '' && $Usercust != null) {
						$serviceprovider = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
							['id' => $Usercust->getCompanyID(), 'userType' => '1']
						);
						if ($serviceprovider != '' && $serviceprovider != '') {
							foreach ($serviceprovider as $serviceproviderVal) {
								if ($serviceproviderVal->getIsNotification() == '1') {
									$usenotification = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:Notification"
									)->findBy(['userID' => $serviceproviderVal->getId()]);
									//echo '<pre>';print_r($usenotification);
									if ($usenotification != '' && $usenotification != null) {
										foreach ($usenotification as $usenotificationVal) {

											$registatoin_ids = $usenotificationVal->getDeviceID();
											$msg = 'Birthday';
											$IDs = $UserVal->getId();
											$submsg = 'Today  birthday of your customer' . ' ' . $userName;
										}
									}
									$notificationMsg = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:NotificationMessage"
									)->findOneBy(
										[
											'userID' => $serviceproviderVal->getId(),
											'toUserID' => $UserVal->getId(),
											'notificationTitle' => 'Birthday'
										]
									);

									if ($notificationMsg == '' && $notificationMsg == null) {

										$notifyMsg = new NotificationMessage();
										$notifyMsg->setNotificationTitle($msg);
										$notifyMsg->setNotificationMessage($submsg);
										$notifyMsg->setUserID($serviceproviderVal->getId());
										$notifyMsg->setToUserID($IDs);
										$em = $this->getDoctrine()->getManager();
										$em->persist($notifyMsg);
										$em->flush();
									} else {
										$em = $this->getDoctrine()->getEntityManager();
										$em->remove($notificationMsg);
										$em->flush();
										$notifyMsg = new NotificationMessage();
										$notifyMsg->setNotificationTitle($msg);
										$notifyMsg->setNotificationMessage($submsg);
										$notifyMsg->setUserID($serviceproviderVal->getId());
										$notifyMsg->setToUserID($IDs);
										$em = $this->getDoctrine()->getManager();
										$em->persist($notifyMsg);
										$em->flush();
									}
									$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
								}

							}
						}
					}
				}
			}
			echo json_encode(array('success' => 1, 'message' => 'successfull'));

			return array();
		}

		/* BIRTHDAY NOTIFICATION FUNCTION END */
	}

	/*     * **************************************************************************Report problem End******************************************** */

	/**
	 * @Route("/search", name="_search")
	 * @Template()
	 */
	/*     * ************************************************************************SEARCH Begin ******************************************* */
	public function searchAction(Request $text, Request $search_type, Request $counter, Request $user_id)
	{
		$request = $this->getRequest();
		$limitset = 10;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$searchterm = $request->get('text');
		$em = $this->getDoctrine()->getEntityManager();
		$sp_id = $request->get('user_id');
		if ($request->get('search_type') == 'post_tag') {
			$query = $em->createQuery("SELECT n FROM AcmeDemoBundle:PostTags n WHERE n.tags LIKE :text")->setParameter(
				'text',
				'%' . $searchterm . '%'
			)->setFirstResult($min_num)->setMaxResults($max_num);
			//echo '<pre>';print_r($query);die;
			$entities = $query->getResult();
			if ($entities != '' && $entities != null) {
				foreach ($entities as $entitiesVal) {
					$postID[] = $entitiesVal->getPostID();
				}
				$postModel1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $postID, 'postStatus' => '0']
				);
				if ($postModel1 != '' && $postModel1 != null) {
					foreach ($postModel1 as $postModelValues) {
						$userID[] = $postModelValues->getUserID();
						$post_ID[] = $postModelValues->getId();
					}
				}
				for ($i = 0; $i < count($userID); $i++) {
					if (($userID[$i] != $sp_id)) {
						//$sp= $spID;
						$spIDs[] = $post_ID[$i];
					} else {
						$spIDs = '';
					}
				}
				if (count($userID) < 1) {
					$spIDs = '';
				}

				if ($sp_id > 0) {
					$postModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $spIDs, 'postStatus' => '0']
					);
					if ($postModel != '' && $postModel != null) {
						foreach ($postModel as $postModelVal) {

							if ($postModelVal->getPostImageFront() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageFront();
							} elseif ($postModelVal->getPostImageFrontLeft() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageFrontLeft();
							} elseif ($postModelVal->getPostImageLeft() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageLeft();
							} elseif ($postModelVal->getPostImageBackLeft() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageBackLeft();
							} elseif ($postModelVal->getPostImageBack() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageBack();
							} elseif ($postModelVal->getPostImageBackRight() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageBackRight();
							} elseif ($postModelVal->getPostImageRight() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageRight();
							} elseif ($postModelVal->getPostImageFrontRight() != '') {
								$userprofile = $this->baseurl() . $postModelVal->getPostImageFrontRight();
							} else {
								$userprofile = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $postModelVal->getId()]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $postModelVal->getUserTagID()]
							);
							//echo '<pre>';print_r($user_id);die;
							if ($userModel != '' && $userModel != null) {
								if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
									$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
								} else {
									$username = '';
								}
								if (($userModel->getUserAddress()) != '') {
									$userAddress = $userModel->getUserAddress();
								} else {
									$userAddress = '';
								}

								$Search[] = ([
									'post_id' => ($postModelVal->getId()),
									'tag_status' => $tag_status,
									'user_id' => ($userModel->getId()),
									'user_address' => ($userAddress),
									'user_name' => ($username),
									'user_profile' => ($userprofile)
								]);
							}
						}


						echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
					} else {
						echo json_encode(array('success' => 0, 'message' => 'failure'));
					}
				} else {
					$postModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $postID, 'postStatus' => '0']
					);
					if ($postModel != '' && $postModel != null) {
						foreach ($postModel as $postModelValues) {

							if ($postModelValues->getPostImageFront() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageFront();
							} elseif ($postModelValues->getPostImageFrontLeft() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageFrontLeft();
							} elseif ($postModelValues->getPostImageLeft() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageLeft();
							} elseif ($postModelValues->getPostImageBackLeft() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageBackLeft();
							} elseif ($postModelValues->getPostImageBack() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageBack();
							} elseif ($postModelValues->getPostImageBackRight() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageBackRight();
							} elseif ($postModelValues->getPostImageRight() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageRight();
							} elseif ($postModelValues->getPostImageFrontRight() != '') {
								$userprofile = $this->baseurl() . $postModelValues->getPostImageFrontRight();
							} else {
								$userprofile = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $postModelValues->getId()]
							);
							if ($PostTags != '') {
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									} else {
										$tag_status = '0';
									}
								}
							} else {
								$tag_status = '0';
							}
							$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $postModelValues->getUserTagID()]
							);
							//echo '<pre>';print_r($user_id);die;
							if ($userModel != '' && $userModel != null) {
								if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
									$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
								} else {
									$username = '';
								}
								if (($userModel->getUserAddress()) != '') {
									$userAddress = $userModel->getUserAddress();
								} else {
									$userAddress = '';
								}

								$Search[] = ([
									'post_id' => ($postModelValues->getId()),
									'tag_status' => $tag_status,
									'user_id' => ($userModel->getId()),
									'user_address' => ($userAddress),
									'user_name' => ($username),
									'user_profile' => ($userprofile)
								]);
							}
						}


						echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
					} else {
						echo json_encode(array('success' => 0, 'message' => 'failure'));
					}
				}
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			if (($request->get('search_type') == 'post_caption')) {
				$query = $em->createQuery(
					"SELECT n FROM AcmeDemoBundle:AlbumPost n WHERE n.postCaption LIKE :text"
				)->setParameter('text', '%' . $searchterm . '%');
//->setFirstResult($min_num)->setMaxResults($max_num)
				$postModel = $query->getResult();

				if ($postModel != '' && $postModel != null) {
					foreach ($postModel as $entitiesValues) {
						$userID[] = $entitiesValues->getUserTagID();
						$post_id[] = $entitiesValues->getId();
					}

					for ($i = 0; $i < count($userID); $i++) {
						if (($userID[$i] != $sp_id)) {
							$terst[] = $userID[$i];
							$userIDs[] = $post_id[$i];
						} else {
							$userIDs = '';
						}
					}

					if (count($userID) < 1) {
						$userIDs = '';
					}

					if ($sp_id > 0) {
						$entity = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
							['id' => $userIDs, 'postStatus' => '0'],
							array('id' => 'desc'),
							$max_num,
							$min_num
						);

						if ($entity != '' && $entity != null) {
							foreach ($entity as $entitiesval) {

								if ($entitiesval->getPostImageFront() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageFront();
								} elseif ($entitiesval->getPostImageFrontLeft() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageFrontLeft();
								} elseif ($entitiesval->getPostImageLeft() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageLeft();
								} elseif ($entitiesval->getPostImageBackLeft() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageBackLeft();
								} elseif ($entitiesval->getPostImageBack() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageBack();
								} elseif ($entitiesval->getPostImageBackRight() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageBackRight();
								} elseif ($entitiesval->getPostImageRight() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageRight();
								} elseif ($entitiesval->getPostImageFrontRight() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageFrontRight();
								} else {
									$userprofile = '';
								}
								$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
									['postID' => $entitiesval->getId()]
								);
								if ($PostTags != '') {
									foreach ($PostTags as $PostTagsVal) {
										if ($PostTagsVal->getTags() != '') {
											$tag_status = '1';
										} else {
											$tag_status = '0';
										}
									}
								} else {
									$tag_status = '0';
								}
								$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $entitiesval->getUserTagID()]
								);

								if ($userModel != '' && $userModel != null) {
									if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
										$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
									} else {
										$username = '';
									}
									if (($userModel->getUserAddress()) != '') {
										$userAddress = $userModel->getUserAddress();
									} else {
										$userAddress = '';
									}

									$Search[] = ([
										'post_id' => ($entitiesval->getId()),
										'tag_status' => $tag_status,
										'user_id' => ($userModel->getId()),
										'user_address' => ($userAddress),
										'user_name' => ($username),
										'user_profile' => ($userprofile)
									]);
								}
							}
							echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
						} else {
							echo json_encode(array('success' => 0, 'message' => 'failure'));
						}
					} else {
						$entity = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
							['id' => $post_id, 'postStatus' => '0'],
							array('id' => 'desc'),
							$max_num,
							$min_num
						);
						if ($entity != '' && $entity != null) {
							foreach ($entity as $entitiesval) {

								if ($entitiesval->getPostImageFront() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageFront();
								} elseif ($entitiesval->getPostImageFrontLeft() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageFrontLeft();
								} elseif ($entitiesval->getPostImageLeft() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageLeft();
								} elseif ($entitiesval->getPostImageBackLeft() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageBackLeft();
								} elseif ($entitiesval->getPostImageBack() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageBack();
								} elseif ($entitiesval->getPostImageBackRight() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageBackRight();
								} elseif ($entitiesval->getPostImageRight() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageRight();
								} elseif ($entitiesval->getPostImageFrontRight() != '') {
									$userprofile = $this->baseurl() . $entitiesval->getPostImageFrontRight();
								} else {
									$userprofile = '';
								}
								$tag_status = '';
								$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
									['postID' => $entitiesval->getId()]
								);
								if ($PostTags != '') {
									foreach ($PostTags as $PostTagsVal) {
										if ($PostTagsVal->getTags() != '') {
											$tag_status = '1';
										} else {
											$tag_status = '0';
										}
									}
								} else {
									$tag_status = '0';
								}
								$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $entitiesval->getUserTagID()]
								);

								if ($userModel != '' && $userModel != null) {
									if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
										$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
									} else {
										$username = '';
									}
									if (($userModel->getUserAddress()) != '') {
										$userAddress = $userModel->getUserAddress();
									} else {
										$userAddress = '';
									}

									$Search[] = ([
										'post_id' => ($entitiesval->getId()),
										'tag_status' => $tag_status,
										'user_id' => ($userModel->getId()),
										'user_address' => ($userAddress),
										'user_name' => ($username),
										'user_profile' => ($userprofile)
									]);
								}
							}
							echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
						} else {
							echo json_encode(array('success' => 0, 'message' => 'failure'));
						}
					}
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} elseif (($request->get('search_type') == 'post_category')) {
				$query = $em->createQuery(
					"SELECT n FROM AcmeDemoBundle:MasterCategory n WHERE n.categoryName LIKE :text"
				)
					->setParameter('text', '%' . $searchterm . '%');
				$entities = $query->getResult();
//             echo '<pre>';print_r($entities);die;
				if ($entities != '' && $entities != null) {
					foreach ($entities as $entitiesVal) {
						$catID[] = $entitiesVal->getId();
					}
					$postcategoryModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostCategory")->findBy(
						['categoryID' => $catID]
					);

					if ($postcategoryModel != '' && $postcategoryModel != null) {
						foreach ($postcategoryModel as $postcategoryModelval) {
							$post[] = $postcategoryModelval->getPostID();
						}
					}
					$ids = array_unique($post);

					$postModel2 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $ids, 'postStatus' => '0']
					);
					if ($postModel2 != '' && $postModel2 != null) {
						foreach ($postModel2 as $postModel2val) {
							$userID[] = $postModel2val->getUserID();
						}
					}
					for ($i = 0; $i < count($userID); $i++) {
						if (($userID[$i] != $sp_id)) {
							//$sp= $spID;
							$spIDs[] = $userID[$i];
						}
					}
					if (count($userID) < 1) {
						$spIDs = '';
					}
//                echo '<pre>';print_r($spIDs);die;
					if ($sp_id > 0) {
						$postModel1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
							['userID' => $spIDs, 'postStatus' => '0'],
							array('id' => 'desc'),
							$max_num,
							$min_num
						);
//                   echo '<pre>';print_r($postModel1);die;
						if ($postModel1 != '' && $postModel1 != null) {
							foreach ($postModel1 as $postVal) {
								//echo '<pre>';print_r($postModelVal->getPostImageFront());
								if ($postVal->getPostImageFront() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageFront();
								} elseif ($postVal->getPostImageFrontLeft() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageFrontLeft();
								} elseif ($postVal->getPostImageLeft() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageLeft();
								} elseif ($postVal->getPostImageBackLeft() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageBackLeft();
								} elseif ($postVal->getPostImageBack() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageBack();
								} elseif ($postVal->getPostImageBackRight() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageBackRight();
								} elseif ($postVal->getPostImageRight() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageRight();
								} elseif ($postVal->getPostImageFrontRight() != '') {
									$userprofile = $this->baseurl() . $postVal->getPostImageFrontRight();
								} else {
									$userprofile = '';
								}
								$tag_status = '0';
								$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
									['postID' => $postVal->getId()]
								);
								if ($PostTags != '') {
									foreach ($PostTags as $PostTagsVal) {
										if ($PostTagsVal->getTags() != '') {
											$tag_status = '1';
										} else {
											$tag_status = '0';
										}
									}
								} else {
									$tag_status = '0';
								}
								$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $postVal->getUserTagID()]
								);

								if ($userModel != '' && $userModel != null) {
									if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
										$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
									} else {
										$username = '';
									}
									if (($userModel->getUserAddress()) != '') {
										$userAddress = $userModel->getUserAddress();
									} else {
										$userAddress = '';
									}
								}
								$Search[] = ([
									'post_id' => ($postVal->getId()),
									'tag_status' => $tag_status,
									'user_id' => ($userModel->getId()),
									'user_address' => ($userAddress),
									'user_name' => ($username),
									'user_profile' => ($userprofile)
								]);
							}

							echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
						} else {
							echo json_encode(array('success' => 0, 'message' => 'failure'));
						}
					} else {
						$postModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
							['id' => $ids, 'postStatus' => '0'],
							array('id' => 'asc'),
							$max_num,
							$min_num
						);
						if ($postModel != '' && $postModel != null) {
							foreach ($postModel as $postModelval) {
								if ($postModelval->getPostImageFront() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageFront();
								} elseif ($postModelval->getPostImageFrontLeft() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageFrontLeft();
								} elseif ($postModelval->getPostImageLeft() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageLeft();
								} elseif ($postModelval->getPostImageBackLeft() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageBackLeft();
								} elseif ($postModelval->getPostImageBack() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageBack();
								} elseif ($postModelval->getPostImageBackRight() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageBackRight();
								} elseif ($postModelval->getPostImageRight() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageRight();
								} elseif ($postModelval->getPostImageFrontRight() != '') {
									$userprofile = $this->baseurl() . $postModelval->getPostImageFrontRight();
								} else {
									$userprofile = '';
								}
								$tag_status = '0';
								$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
									['postID' => $postModelval->getId()]
								);
								if ($PostTags != '') {
									foreach ($PostTags as $PostTagsVal) {
										if ($PostTagsVal->getTags() != '') {
											$tag_status = '1';
										} else {
											$tag_status = '0';
										}
									}
								} else {
									$tag_status = '0';
								}
								$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $postModelval->getUserTagID()]
								);

								if ($userModel != '' && $userModel != null) {
									if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
										$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
									} else {
										$username = '';
									}
									if (($userModel->getUserAddress()) != '') {
										$userAddress = $userModel->getUserAddress();
									} else {
										$userAddress = '';
									}
								}
								$Search[] = ([
									'post_id' => ($postModelval->getId()),
									'tag_status' => $tag_status,
									'user_id' => ($userModel->getId()),
									'user_address' => ($userAddress),
									'user_name' => ($username),
									'user_profile' => ($userprofile)
								]);
							}
							echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
						} else {
							echo json_encode(array('success' => 0, 'message' => 'failure'));
						}
					}
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {
				if (($request->get('search_type') == 'users')) {
					$query = $em->createQuery(
						"SELECT n FROM AcmeDemoBundle:User n WHERE  CONCAT(CONCAT(n.userFirstName, ' '), n.userLastName) LIKE :text"
					)->setParameter('text', '%' . $searchterm . '%');

					$entities = $query->getResult();
					if ($entities != '' && $entities != null) {
						foreach ($entities as $entitiesVal) {
							$userID[] = $entitiesVal->getId();
						}
					} else {
						$userID = 0;
					}
					for ($i = 0; $i < count($userID); $i++) {
						if (($userID[$i] != $sp_id)) {
							//$sp= $spID;
							$spIDs[] = $userID[$i];
						} else {
							$spIDs = '';
						}
					}
					if (count($userID) < 1) {
						$spIDs = '';
					}
					if ($sp_id > 0) {
						$userValues = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
							['id' => $spIDs],
							array('id' => 'desc'),
							$max_num,
							$min_num
						);
						if ($userValues != '' && $userValues != null) {

							foreach ($userValues as $userValuesVal1) {

								if (($userValuesVal1->getUserFirstName() || $userValuesVal1->getUserLastName()) != '') {
									$username = $userValuesVal1->getUserFirstName() . ' ' . $userValuesVal1->getUserLastName();
								} else {
									$username = '';
								}
								if (($userValuesVal1->getUserProfileImage()) != '' && $userValuesVal1->getUserProfileImage() > 0
								) {
									$userprofile = $this->baseurl() . $userValuesVal1->getUserProfileImage();
								} else {
									$userprofile = $this->baseurl() . 'defaultprofile.png';
								}
								if (($userValuesVal1->getUserAddress()) != '') {
									$userAddress = $userValuesVal1->getUserAddress();
								} else {
									$userAddress = '';
								}

								$Search[] = ([
									'user_id' => ($userValuesVal1->getId()),
									'user_address' => ($userAddress),
									'user_name' => ($username),
									'user_type' => ($userValuesVal1->getUserType()),
									'user_profile' => ($userprofile)
								]);
							}

							echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
						} else {
							echo json_encode(array('success' => 0, 'message' => 'failure'));
						}
					} else {
						$userValues = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
							['id' => $userID],
							array('id' => 'desc'),
							$max_num,
							$min_num
						);
						if ($userValues != '' && $userValues != null) {
							foreach ($userValues as $userValuesVal) {

								if (($userValuesVal->getUserFirstName() || $userValuesVal->getUserLastName()) != '') {
									$username = $userValuesVal->getUserFirstName() . ' ' . $userValuesVal->getUserLastName();
								} else {
									$username = '';
								}
								if (($userValuesVal->getUserProfileImage()) != '' && $userValuesVal->getUserProfileImage() > 0
								) {
									$userprofile = $this->baseurl() . $userValuesVal->getUserProfileImage();
								} else {
									$userprofile = $this->baseurl() . 'defaultprofile.png';
								}
								if (($userValuesVal->getUserAddress()) != '') {
									$userAddress = $userValuesVal->getUserAddress();
								} else {
									$userAddress = '';
								}

								$Search[] = ([
									'user_id' => ($userValuesVal->getId()),
									'user_name' => ($username),
									'user_address' => ($userAddress),
									'user_type' => ($userValuesVal->getUserType()),
									'user_profile' => ($userprofile)
								]);
							}
							echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
						} else {
							echo json_encode(array('success' => 0, 'message' => 'failure'));
						}
					}
				} elseif (($request->get('search_type') == 'date')) {
					//$timestamp = strtotime('2016-05-13');

					$postModel3 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['postStatus' => '0'],
						array('id' => 'asc'),
						$max_num,
						$min_num
					);
					foreach ($postModel3 as $postModel3val) {
						$date = explode(' ', $postModel3val->getPostDate());
						$timestamp = strtotime($date[0]);


						if ($timestamp == $searchterm) {
							$postID[] = $postModel3val->getId();
						} else {
							$postID = [];
						}
					}


					if ($postID != '' && $postID != null) {
						$postModel2 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
							['id' => $postID, 'postStatus' => '0']
						);
						if ($postModel2 != '' && $postModel2 != null) {
							foreach ($postModel2 as $postModel2val) {
								$userID[] = $postModel2val->getUserID();
							}
						}
						for ($i = 0; $i < count($userID); $i++) {
							if (($userID[$i] != $sp_id)) {

								$spIDs[] = $userID[$i];
							}
						}
						if (count($userID) < 1) {
							$spIDs = '';
						}
						if ($sp_id > 0) {
							$postModel1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
								['userID' => $spIDs, 'postStatus' => '0'],
								array('id' => 'asc'),
								$max_num,
								$min_num
							);

							if ($postModel1 != '' && $postModel1 != null) {
								foreach ($postModel1 as $postVal) {
									//echo '<pre>';print_r($postModelVal->getPostImageFront());
									if ($postVal->getPostImageFront() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageFront();
									} elseif ($postVal->getPostImageFrontLeft() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageFrontLeft();
									} elseif ($postVal->getPostImageLeft() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageLeft();
									} elseif ($postVal->getPostImageBackLeft() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageBackLeft();
									} elseif ($postVal->getPostImageBack() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageBack();
									} elseif ($postVal->getPostImageBackRight() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageBackRight();
									} elseif ($postVal->getPostImageRight() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageRight();
									} elseif ($postVal->getPostImageFrontRight() != '') {
										$userprofile = $this->baseurl() . $postVal->getPostImageFrontRight();
									} else {
										$userprofile = '';
									}
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $postVal->getId()]
									);
									if ($PostTags != '') {
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											} else {
												$tag_status = '0';
											}
										}
									} else {
										$tag_status = '0';
									}
									$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $postVal->getUserTagID()]
									);

									if ($userModel != '' && $userModel != null) {
										if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
											$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
										} else {
											$username = '';
										}
										if (($userModel->getUserAddress()) != '') {
											$userAddress = $userModel->getUserAddress();
										} else {
											$userAddress = '';
										}

										$Search[] = ([
											'post_id' => ($postVal->getId()),
											'tag_status' => $tag_status,
											'user_id' => ($userModel->getId()),
											'user_address' => ($userAddress),
											'user_name' => ($username),
											'user_profile' => ($userprofile)
										]);
									}
								}

								echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
							} else {
								echo json_encode(array('success' => 0, 'message' => 'failure'));
							}
						} else {
							$postModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
								['id' => $postID, 'postStatus' => '0'],
								array('id' => 'asc'),
								$max_num,
								$min_num
							);
							if ($postModel != '' && $postModel != null) {
								foreach ($postModel as $postModelval) {
									if ($postModelval->getPostImageFront() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageFront();
									} elseif ($postModelval->getPostImageFrontLeft() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageFrontLeft();
									} elseif ($postModelval->getPostImageLeft() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageLeft();
									} elseif ($postModelval->getPostImageBackLeft() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageBackLeft();
									} elseif ($postModelval->getPostImageBack() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageBack();
									} elseif ($postModelval->getPostImageBackRight() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageBackRight();
									} elseif ($postModelval->getPostImageRight() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageRight();
									} elseif ($postModelval->getPostImageFrontRight() != '') {
										$userprofile = $this->baseurl() . $postModelval->getPostImageFrontRight();
									} else {
										$userprofile = '';
									}
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $postModelval->getId()]
									);
									if ($PostTags != '') {
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											} else {
												$tag_status = '0';
											}
										}
									} else {
										$tag_status = '0';
									}
									$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $postModelval->getUserTagID()]
									);

									if ($userModel != '' && $userModel != null) {
										if (($userModel->getUserFirstName() || $userModel->getUserLastName()) != '') {
											$username = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
										} else {
											$username = '';
										}
										if (($userModel->getUserAddress()) != '') {
											$userAddress = $userModel->getUserAddress();
										} else {
											$userAddress = '';
										}

										$Search[] = ([
											'post_id' => ($postModelval->getId()),
											'tag_status' => $tag_status,
											'user_id' => ($userModel->getId()),
											'user_address' => ($userAddress),
											'user_name' => ($username),
											'user_profile' => ($userprofile)
										]);
									}
								}
								echo json_encode(array('success' => 1, 'message' => 'succesfull', 'search' => $Search));
							} else {
								echo json_encode(array('success' => 0, 'message' => 'failure'));
							}
						}
					} else {
						echo json_encode(array('success' => 0, 'message' => 'failure'));
					}
				}
			}
		}
	}

	/*     * **************************************************************************SEARCH End******************************************** */

	/**
	 * @Route("/trending", name="_trending")
	 * @Template()
	 */
	/*     * ************************************************************************TRENDING Begin ******************************************* */
	public function trendingAction(
		Request $counter,
		Request $user_id,
		Request $device_id,
		Request $device_type,
		Request $imei
	) {
		$request = $this->getRequest();
		$limitset = 4;
		//$follow_status = '0';

		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$sp_id = $request->get('user_id');
		//$resultdata= $conn->query('select * from awn_user')->fetchAll();

		$result = $conn->query(
			"select * from (SELECT toUserID, AVG(userRating) AS rt
FROM user_rating
GROUP BY toUserID
UNION ALL
SELECT fromUserID, AVG(userRating) AS rt
FROM user_rating
GROUP BY fromUserID ) AS userrate ORDER BY rt DESC,toUserID DESC limit " . $min_num . "," . $max_num . ""
		)->fetchAll();

		//echo '<pre>';print_r($result);die;
		if (!empty($sp_id)) {
			/*********************************************Notification Crediential START  ************************/
			//this info should already be set - bug not sending device id
			/*
			$notification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findOneBy(
				['imei' => ($request->get('imei'))]
			);
			if ($notification != '' && $notification != null) {
				$deviceID = $notification->getDeviceID();
				$notification->setUserID($sp_id);
				$notification->setDeviceID($this->ClearText($request->get('device_id')));
				$notification->setDeviceType($request->get('device_type'));
				$notification->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notification);
				$em->flush();
			} else {
				$notificationModel = new Notification();
				$notificationModel->setUserID($sp_id);
				$notificationModel->setDeviceID($this->ClearText($request->get('device_id')));
				$notificationModel->setDeviceType($request->get('device_type'));
				$notificationModel->setImei($this->ClearText($request->get('imei')));
				$em = $this->getDoctrine()->getManager();
				$em->persist($notificationModel);
				$em->flush();
			}
			*/
			/***************************************Notification Crediential END********************/


			if ($result != '' && $result != null) {

				foreach ($result as $resultVal) {
					if ($resultVal['toUserID'] != $sp_id) {

						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $resultVal['toUserID']]
						);

						if ($User != '' && $User != null) {

							$user_type = $User->getUserType();
							$sp_user_id = $User->getId();

							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {

									if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
									) {

										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}

							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$userName = '';
							}
							if ($User->getCompanyName() != '') {
								$companyName = $User->getCompanyName();
							} else {
								$companyName = '';
							}
							if ($User->getUserAddress() != '') {
								$userAddres = $User->getUserAddress();
							} else {
								$userAddres = '';
							}
							if ($User->getUserMobileNo() != '') {
								$userContact = $User->getUserMobileNo();
							} else {
								$userContact = '';
							}
							if ($User->getUserProfileImage() != '' && $User->getUserProfileImage() > 0) {
								$profileImage = $this->baseurl() . $User->getUserProfileImage();
							} else {
								$profileImage = $this->baseurl() . 'defaultprofile.png';
							}
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}

//                         $manager = $this->getDoctrine()->getManager();
//        $conn = $manager->getConnection();
//
//        $relatedUser = $conn->query(
//                        "select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . ""
//                )->fetchAll();
//        if($relatedUser != ''){
//            foreach($relatedUser as $relatedUserval){
//             $postID[]  = $relatedUserval['postID'];
//            }
//        }else{
//            $postID  = '';
//        }
							$userrate = $conn->query(
								"select user_rating.userRating,post.postID as post_id from user_rating left join post on user_rating.postID = post.postID where   (post.postStatus = '0' and  user_rating.toUserID =" . $resultVal['toUserID'] . ") OR (post.postStatus = '0' and user_rating.fromUserID = " . $resultVal['toUserID'] . ")   order by post.postID desc limit 0,6"
							)->fetchAll();


							if ($userrate != '' && $userrate != null) {
								foreach ($userrate as $customers) {

									$customersValues = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customers['post_id']]);

									$album_id = $customersValues->getId();
									if ($customersValues->getPostCaption() != '') {
										$post_caption = $customersValues->getPostCaption();
									} else {
										$post_caption = '';
									}

									if ($customersValues->getUserTagID() != '') {
										$tagedUser = $customersValues->getUserTagID();
									} else {
										$tagedUser = '';
									}
									if ($customersValues->getUserID() != '') {
										$postedUser = $customersValues->getUserID();
									} else {
										$postedUser = '';
									}

									if ($customersValues->getPostImageFront() != '' && $customersValues->getPostImageFront() != null
									) {
										$album_image = $this->baseurl() . $customersValues->getPostImageFront();
									} elseif ($customersValues->getPostImageFrontLeft() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageFrontLeft();
									} elseif ($customersValues->getPostImageLeft() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageLeft();
									} elseif ($customersValues->getPostImageBackLeft() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageBackLeft();
									} elseif ($customersValues->getPostImageBack() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageBack();
									} elseif ($customersValues->getPostImageBackRight() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageBackRight();
									} elseif ($customersValues->getPostImageRight() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageRight();
									} elseif ($customersValues->getPostImageFrontRight() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageFrontRight();
									} else {
										$album_image = '';
									}
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $album_id]
									);
									if ($PostTags != '') {
										$tag_status = '0';
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											}
										}
									} else {
										$tag_status = '0';
									}


									$UserConsumer = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:User"
									)->findOneBy(['id' => $tagedUser]);
									$User_postConsumer = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:User"
									)->findOneBy(['id' => $postedUser]);

									if ($user_type == '1') {

										if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

											$user_id = $sp_user_id;
											$user_name = $userName;
											$post_usertype = $user_type;
										}

									} else {
										if ($user_type == '0') {
											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												if ($User_postConsumer->getId() == $UserConsumer->getId()) {
													$user_id = $sp_user_id;
													$user_name = $userName;
													$post_usertype = $user_type;
												} else {
													if ($UserConsumer->getUserType() == '1') {
														$user_id = $UserConsumer->getId();
														if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
														) {
															$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
														} else {
															$user_name = '';

														}
														$post_usertype = $UserConsumer->getUserType();
													} else {
														if ($User_postConsumer->getUserType() == '1') {
															$user_id = $User_postConsumer->getId();
															if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
															) {
																$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
															} else {
																$user_name = '';
															}
															$post_usertype = $User_postConsumer->getUserType();
														} else {
															$user_id = '';
															$user_name = '';
															$post_usertype = $user_type;
														}
													}

												}


											}
											/*  if ($UserConsumer != '' && $UserConsumer != null) {
                                   
                                    $user_id = $UserConsumer->getId();
                                    if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                        $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                    
                                } else {
                                    $user_name = '';
                                    }   
                                */

										}
									}


									$UserRating = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:UserRating"
									)->findOneBy(['postID' => $album_id]);
									// echo '<pre>';print_r($UserRating);
									if ($UserRating != '' && $UserRating != null) {

										if ($UserRating->getUserRating() != '') {
											$rating1 = $UserRating->getUserRating();
										} else {
											$rating1 = '';
										}
										if ($UserRating->getUserRating() != '') {
											$rating[] = $UserRating->getUserRating();
										} else {
											$rating[] = '';
										}
									} else {
										$rating1 = '';
										$rating = [];

									}

									$album_detail[] = array(
										'album_id' => ($album_id),
										'tag_status' => $tag_status,
										'album_service' => ($post_caption),
										'user_id' => ($user_id),
										'user_type' => ($post_usertype),
										'album_image' => ($album_image),
										'user_name' => ($user_name),
										'rates' => ($rating1)
									);
								}
							} else {

								$rating = [];
								$album_detail = [];
							}
							// $UserReviews = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['toUserID' => $sp_user_id]);
							//echo '<pre>';print_r(count($UserReviews));
							$UserReviews = $conn->query(
								"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $resultVal['toUserID'] . " OR fromUserID = " . $resultVal['toUserID'] . ""
							)->fetchAll();
							if ($UserReviews != '') {
								foreach ($UserReviews as $UserReviewsval) {

									// echo '<pre>';print_r($reviews);
									if ($UserReviewsval['rate'] != null) {
										//want full rate not rounded
										$rateavg = round($UserReviewsval['rate'],1);
									} else {
										$rateavg = '';
									}

									$reviews = $UserReviewsval['reviews'];
									unset($rating);
								}
							} else {
								$rateavg = '';
								$reviews = '';
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = $reviews;
							} else {
								$countReview = 0;
							}
							if (count($rateavg) > 0 && $rateavg != null) {
								$countRates = $rateavg;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = array(
								'user_name' => ($userName),
								'user_type' => $user_type,
								'sp_user_id' => ($sp_user_id),
								'contact' => ($userContact),
								'company_name' => ($companyName),
								'user_address' => ($userAddres),
								'profile_image' => ($profileImage)
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'follow_status' => $follow_status,
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
						}
					}

				}

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'trending' => $sp_detail,
						'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
					)
				);

			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {


			if ($result != '' && $result != null) {

				foreach ($result as $resultVal) {
					// if($resultVal['toUserID'] !=  $sp_id){

					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $resultVal['toUserID']]
					);

					if ($User != '' && $User != null) {

						$user_type = $User->getUserType();
						$sp_user_id = $User->getId();

						$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
							['followStatus' => '1']
						);

						if ($UserFollow != '') {
							$follow_status = '0';
							foreach ($UserFollow as $UserFollowVal) {

								if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
								) {

									$follow_status = '1';
								}
							}
						} else {
							$follow_status = '0';
						}

						if ($User->getUserFirstName() || $User->getUserLastName() != '') {
							$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$userName = '';
						}
						if ($User->getCompanyName() != '') {
							$companyName = $User->getCompanyName();
						} else {
							$companyName = '';
						}
						if ($User->getUserAddress() != '') {
							$userAddres = $User->getUserAddress();
						} else {
							$userAddres = '';
						}
						if ($User->getUserMobileNo() != '') {
							$userContact = $User->getUserMobileNo();
						} else {
							$userContact = '';
						}
						if ($User->getUserProfileImage() != '' && $User->getUserProfileImage() > 0) {
							$profileImage = $this->baseurl() . $User->getUserProfileImage();
						} else {
							$profileImage = $this->baseurl() . 'defaultprofile.png';
						}
						$serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findOneBy(
							['userID' => $sp_user_id, 'topService' => 1]
						);

						if ($serviceName != '' && $serviceName != null) {
							$serviceId = $serviceName->getServiceID();

							$serviceprice = $serviceName->getServicePrice();
							$masterService = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $serviceId]);
							$service_name = $masterService->getServiceName();
						} else {
							$serviceprice = '';
							$service_name = '';
						}

						$userrate = $conn->query(
							"select user_rating.userRating,post.postID as post_id from user_rating left join post on user_rating.postID = post.postID where   (post.postStatus = '0' and  user_rating.toUserID =" . $resultVal['toUserID'] . ") OR ( post.postStatus = '0' and user_rating.fromUserID = " . $resultVal['toUserID'] . ")   order by post.postID desc limit 0,6"
						)->fetchAll();


						if ($userrate != '' && $userrate != null) {
							foreach ($userrate as $customers) {
								$customersValues = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:AlbumPost"
								)->findOneBy(['id' => $customers['post_id']]);

								$album_id = $customersValues->getId();
								if ($customersValues->getPostCaption() != '') {
									$post_caption = $customersValues->getPostCaption();
								} else {
									$post_caption = '';
								}

								if ($customersValues->getUserTagID() != '') {
									$tagedUser = $customersValues->getUserTagID();
								} else {
									$tagedUser = '';
								}
								if ($customersValues->getUserID() != '') {
									$postedUser = $customersValues->getUserID();
								} else {
									$postedUser = '';
								}

								if ($customersValues->getPostImageFront() != '' && $customersValues->getPostImageFront() != null
								) {
									$album_image = $this->baseurl() . $customersValues->getPostImageFront();
								} elseif ($customersValues->getPostImageFrontLeft() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageFrontLeft();
								} elseif ($customersValues->getPostImageLeft() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageLeft();
								} elseif ($customersValues->getPostImageBackLeft() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageBackLeft();
								} elseif ($customersValues->getPostImageBack() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageBack();
								} elseif ($customersValues->getPostImageBackRight() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageBackRight();
								} elseif ($customersValues->getPostImageRight() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageRight();
								} elseif ($customersValues->getPostImageFrontRight() != '') {
									$album_image = $this->baseurl() . $customersValues->getPostImageFrontRight();
								} else {
									$album_image = '';
								}
								$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
									['postID' => $album_id]
								);
								if ($PostTags != '') {
									$tag_status = '0';
									foreach ($PostTags as $PostTagsVal) {
										if ($PostTagsVal->getTags() != '') {
											$tag_status = '1';
										}
									}
								} else {
									$tag_status = '0';
								}


								$UserConsumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $tagedUser]
								);
								$User_postConsumer = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:User"
								)->findOneBy(['id' => $postedUser]);


								if ($user_type == '1') {

									if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

										$user_id = $sp_user_id;
										$user_name = $userName;
										$post_usertype = $user_type;
									}

								} else {
									if ($user_type == '0') {

										if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

											if ($User_postConsumer->getId() == $UserConsumer->getId()) {
												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $user_type;
											} else {
												if ($UserConsumer->getUserType() == '1') {
													$user_id = $UserConsumer->getId();
													if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
													) {
														$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
													} else {
														$user_name = '';
													}
													$post_usertype = $UserConsumer->getUserType();
												} else {
													if ($User_postConsumer->getUserType() == '1') {
														$user_id = $User_postConsumer->getId();
														if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
														) {
															$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
														} else {
															$user_name = '';
														}
														$post_usertype = $User_postConsumer->getUserType();
													} else {
														$user_id = '';
														$user_name = '';
														$post_usertype = $user_type;
													}
												}

											}


										}


									}
								}
								/*   if ($UserConsumer != '' && $UserConsumer != null) {
                                    $user_id = $UserConsumer->getId();
                                    if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                        $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                } else {
                                    $user_name = '';
                                }
                               */


								$UserRating = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:UserRating"
								)->findOneBy(['postID' => $album_id]);
								// echo '<pre>';print_r($UserRating);
								if ($UserRating != '' && $UserRating != null) {

									if ($UserRating->getUserRating() != '') {
										$rating1 = $UserRating->getUserRating();
									} else {
										$rating1 = '';
									}
									if ($UserRating->getUserRating() != '') {
										$rating[] = $UserRating->getUserRating();
									} else {
										$rating[] = '';
									}
								} else {
									$rating1 = '';
									$rating = [];

								}

								$album_detail[] = array(
									'album_id' => ($album_id),
									'tag_status' => $tag_status,
									'album_service' => ($post_caption),
									'user_id' => ($user_id),
									'user_type' => $post_usertype,
									'album_image' => ($album_image),
									'user_name' => ($user_name),
									'rates' => ($rating1)
								);
							}
						} else {

							$rating = [];
							$album_detail = [];
						}

						$UserReviews = $conn->query(
							"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $resultVal['toUserID'] . " OR fromUserID = " . $resultVal['toUserID'] . ""
						)->fetchAll();
						if ($UserReviews != '') {
							foreach ($UserReviews as $UserReviewsval) {

								// echo '<pre>';print_r($reviews);
								if ($UserReviewsval['rate'] != null) {
									//want full rate not rounded
									$rateavg = round($UserReviewsval['rate'],1);
								} else {
									$rateavg = '';
								}
								$reviews = $UserReviewsval['reviews'];
								unset($rating);
							}
						} else {
							$rateavg = '';
							$reviews = '';
							$ratvalues = '';
						}


						if (count($reviews) > 0 && $reviews != null) {
							$countReview = $reviews;
						} else {
							$countReview = 0;
						}
						if (count($rateavg) > 0 && $rateavg != null) {
							$countRates = $rateavg;
						} else {
							$countRates = 0;
						}

						$sp_detail[] = array(
							'user_name' => ($userName),
							'user_type' => $user_type,
							'sp_user_id' => ($sp_user_id),
							'contact' => ($userContact),
							'company_name' => ($companyName),
							'user_address' => ($userAddres),
							'profile_image' => ($profileImage)
						,
							'service_name' => ($service_name),
							'service_price' => ($serviceprice),
							'follow_status' => $follow_status,
							'user_chat' => 0,
							'total_reviews' => ($countReview),
							'total_rate' => ($countRates),
							'albums' => $album_detail
						);
						unset($album_detail);
					}
				}


				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'trending' => $sp_detail,
						'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
					)
				);

			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		}


	}





	/*     * **************************************************************************TRENDING End******************************************** */

	/**
	 * @Route("/suggested", name="_suggested")
	 * @Template()
	 */
	/*     * **************************************************************************SUGGESTED Begin******************************************** */
	public function suggestedAction(Request $user_id)
	{
		//"select distinct(toUserID),userRating from user_rating group by toUserID desc";
		$request = $this->getRequest();

		$sp_id = $request->get('user_id');

		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();

		$result = $conn->query(
			"SELECT * 
FROM (
(

SELECT toUserID, AVG( userRating ) AS rt
FROM user_rating
GROUP BY toUserID
)
UNION (

SELECT fromUserID, AVG( userRating ) AS rt
FROM user_rating
GROUP BY fromUserID
)
) AS tt order by rt desc,toUserID desc limit 0,5"
		)->fetchAll();

//echo '<pre>';print_r($result);die;
		if (!empty($sp_id)) {
			if ($result != '' && $result != null) {

				foreach ($result as $resultVal) {
					if ($resultVal['toUserID'] != $sp_id) {
						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $resultVal['toUserID']]
						);
//echo '<pre>';print_r($User);
						if ($User != '' && $User != null) {
							//  foreach ($User as $UserVal) {
							$sp_user_id = $User->getId();
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
									) {
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}

							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$userName = '';
							}
//echo '<pre>';print_r($userName);
							if ($User->getCompanyName() != '') {
								$companyName = $User->getCompanyName();
							} else {
								$companyName = '';
							}
							if ($User->getUserAddress() != '') {
								$userAddres = $User->getUserAddress();
							} else {
								$userAddres = '';
							}
							if ($User->getUserMobileNo() != '') {
								$userContact = $User->getUserMobileNo();
							} else {
								$userContact = '';
							}
							if ($User->getUserProfileImage() != '' && $User->getUserProfileImage() > 0) {
								$profileImage = $this->baseurl() . $User->getUserProfileImage();
							} else {
								$profileImage = $this->baseurl() . 'defaultprofile.png';
							}
							$usertype = $User->getUserType();
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);
//echo '<pre>';print_r($serviceName);
							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}

							$userrate = $conn->query(
								"select user_rating.userRating,post.postID as post_id from user_rating left join post on user_rating.postID = post.postID where  post.postStatus = '0' and  user_rating.toUserID =" . $resultVal['toUserID'] . " OR post.postStatus = '0' and user_rating.fromUserID = " . $resultVal['toUserID'] . "   order by post.postID desc limit 0,6"
							)->fetchAll();


							if ($userrate != '' && $userrate != null) {
								foreach ($userrate as $customers) {
									$customersVal = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customers['post_id']]);
									$album_id = $customersVal->getId();
									if ($customersVal->getPostCaption() != '') {
										$post_caption = $customersVal->getPostCaption();
									} else {
										$post_caption = '';
									}

									if ($customersVal->getUserTagID() != '') {
										$tagedUser = $customersVal->getUserTagID();
									} else {
										$tagedUser = '';
									}
									if ($customersVal->getUserID() != '') {
										$postedUser = $customersVal->getUserID();
									} else {
										$postedUser = '';
									}

									if ($customersVal->getPostImageFront() != '' && $customersVal->getPostImageFront() != null
									) {
										$album_image = $this->baseurl() . $customersVal->getPostImageFront();
									} elseif ($customersVal->getPostImageFrontLeft() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageFrontLeft();
									} elseif ($customersVal->getPostImageLeft() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageLeft();
									} elseif ($customersVal->getPostImageBackLeft() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageBackLeft();
									} elseif ($customersVal->getPostImageBack() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageBack();
									} elseif ($customersVal->getPostImageBackRight() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageBackRight();
									} elseif ($customersVal->getPostImageRight() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageRight();
									} elseif ($customersVal->getPostImageFrontRight() != '') {
										$album_image = $this->baseurl() . $customersVal->getPostImageFrontRight();
									} else {
										$album_image = '';
									}
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $album_id]
									);
									if ($PostTags != '') {
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											} else {
												$tag_status = '0';
											}
										}
									} else {
										$tag_status = '0';
									}
//echo '<pre>';print_r($tagedUser);die;

									$UserConsumer = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:User"
									)->findOneBy(['id' => $tagedUser]);
									$User_postConsumer = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:User"
									)->findOneBy(['id' => $postedUser]);

									if ($usertype == '1') {

										if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

											$user_id = $sp_user_id;
											$user_name = $userName;
											$post_usertype = $usertype;
										}

									} else {
										if ($usertype == '0') {
											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												if ($User_postConsumer->getId() == $UserConsumer->getId()) {
													$user_id = $sp_user_id;
													$user_name = $userName;
													$post_usertype = $usertype;
												} else {
													if ($UserConsumer->getUserType() == '1') {
														$user_id = $UserConsumer->getId();
														if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
														) {
															$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
														} else {
															$user_name = '';

														}
														$post_usertype = $UserConsumer->getUserType();
													} else {
														if ($User_postConsumer->getUserType() == '1') {
															$user_id = $User_postConsumer->getId();
															if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
															) {
																$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
															} else {
																$user_name = '';
															}
															$post_usertype = $User_postConsumer->getUserType();
														} else {
															$user_id = '';
															$user_name = '';
															$post_usertype = $usertype;
														}
													}

												}


											}


										}
									}


									/*
                                if ($UserConsumer != '' && $UserConsumer != null) {
                                    $user_id = $UserConsumer->getId();
                                    if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                        $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                } else {
                                    $user_name = '';
                                }
                                */


									$UserReviews = $conn->query(
										"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $tagedUser . ""
									)->fetchAll();
									if ($UserReviews != '') {
										foreach ($UserReviews as $UserReviewsval) {

											// echo '<pre>';print_r($reviews);
											if ($UserReviewsval['rate'] != null) {
												//want full rating not rounded
												$rateavg = round($UserReviewsval['rate'],1);

											} else {
												$rateavg = '';
											}
											$reviews = $UserReviewsval['reviews'];
											unset($rating);
										}
									} else {
										$rateavg = '';
										$reviews = '';
										$ratvalues = '';
									}


									$album_detail[] = array(
										'album_id' => $album_id,
										'tag_status' => $tag_status,
										'album_service' => $post_caption,
										'user_id' => $user_id,
										'user_type' => $post_usertype,
										'album_image' => $album_image,
										'user_name' => $user_name,
										'rates' => $rateavg
									);
								}
							} else {
								$album_detail = [];
								$ratvalues = '';
							}

							$UserReviews = $conn->query(
								"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $resultVal['toUserID'] . " "
							)->fetchAll();
							if ($UserReviews != '') {
								foreach ($UserReviews as $UserReviewsval) {

									if ($UserReviewsval['rate'] != null) {
										//Dont want rounded want full rate
										$rateavg = round($UserReviewsval['rate'],1);
									} else {
										$rateavg = '';
									}
									$reviews = $UserReviewsval['reviews'];
									unset($rating);
								}
							} else {
								$rateavg = '';
								$reviews = '';
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = $reviews;
							} else {
								$countReview = 0;
							}
							if (count($rateavg) > 0 && $rateavg != null) {
								$countRates = $rateavg;
							} else {
								$countRates = 0;
							}


							$sp_detail[] = array(
								'user_name' => ($userName),
								'user_type' => $usertype,
								'follow_status' => $follow_status,
								'sp_user_id' => ($sp_user_id),
								'contact' => ($userContact),
								'company_name' => ($companyName),
								'user_address' => ($userAddres),
								'profile_image' => $profileImage
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
							//echo '<pre>';print_r($sp_detail);
						}
					}
				}

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'suggested' => $sp_detail,
						'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
					)
				);

			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}

		} else {
			if ($result != '' && $result != null) {

				foreach ($result as $resultVal) {

					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $resultVal['toUserID']]
					);
//echo '<pre>';print_r($User);
					if ($User != '' && $User != null) {
						//  foreach ($User as $UserVal) {
						$sp_user_id = $User->getId();
						$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
							['followStatus' => '1']
						);

						if ($UserFollow != '') {
							$follow_status = '0';
							foreach ($UserFollow as $UserFollowVal) {
								if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
								) {
									$follow_status = '1';
								}
							}
						} else {
							$follow_status = '0';
						}

						if ($User->getUserFirstName() || $User->getUserLastName() != '') {
							$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$userName = '';
						}
//echo '<pre>';print_r($userName);
						if ($User->getCompanyName() != '') {
							$companyName = $User->getCompanyName();
						} else {
							$companyName = '';
						}
						if ($User->getUserAddress() != '') {
							$userAddres = $User->getUserAddress();
						} else {
							$userAddres = '';
						}
						if ($User->getUserMobileNo() != '') {
							$userContact = $User->getUserMobileNo();
						} else {
							$userContact = '';
						}
						if ($User->getUserProfileImage() != '' && $User->getUserProfileImage() > 0) {
							$profileImage = $this->baseurl() . $User->getUserProfileImage();
						} else {
							$profileImage = $this->baseurl() . 'defaultprofile.png';
						}
						$usertype = $User->getUserType();
						$serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findOneBy(
							['userID' => $sp_user_id, 'topService' => 1]
						);
//echo '<pre>';print_r($serviceName);
						if ($serviceName != '' && $serviceName != null) {
							$serviceId = $serviceName->getServiceID();

							$serviceprice = $serviceName->getServicePrice();
							$masterService = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $serviceId]);
							$service_name = $masterService->getServiceName();
						} else {
							$serviceprice = '';
							$service_name = '';
						}

						$userrate = $conn->query(
							"select user_rating.userRating,post.postID as post_id from user_rating left join post on user_rating.postID = post.postID where ( post.postStatus = '0' and  user_rating.toUserID =" . $resultVal['toUserID'] . ") OR (post.postStatus = '0' and user_rating.fromUserID = " . $resultVal['toUserID'] . ")   order by post.postID desc limit 0,6"
						)->fetchAll();


						if ($userrate != '' && $userrate != null) {
							foreach ($userrate as $customers) {
								$customersVal = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:AlbumPost"
								)->findOneBy(['id' => $customers['post_id']]);
								$album_id = $customersVal->getId();
								if ($customersVal->getPostCaption() != '') {
									$post_caption = $customersVal->getPostCaption();
								} else {
									$post_caption = '';
								}

								if ($customersVal->getUserTagID() != '') {
									$tagedUser = $customersVal->getUserTagID();
								} else {
									$tagedUser = '';
								}
								if ($customersVal->getUserID() != '') {
									$postedUser = $customersVal->getUserID();
								} else {
									$postedUser = '';
								}


								if ($customersVal->getPostImageFront() != '' && $customersVal->getPostImageFront() != null
								) {
									$album_image = $this->baseurl() . $customersVal->getPostImageFront();
								} elseif ($customersVal->getPostImageFrontLeft() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageFrontLeft();
								} elseif ($customersVal->getPostImageLeft() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageLeft();
								} elseif ($customersVal->getPostImageBackLeft() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageBackLeft();
								} elseif ($customersVal->getPostImageBack() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageBack();
								} elseif ($customersVal->getPostImageBackRight() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageBackRight();
								} elseif ($customersVal->getPostImageRight() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageRight();
								} elseif ($customersVal->getPostImageFrontRight() != '') {
									$album_image = $this->baseurl() . $customersVal->getPostImageFrontRight();
								} else {
									$album_image = '';
								}
								$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
									['postID' => $album_id]
								);
								if ($PostTags != '') {
									foreach ($PostTags as $PostTagsVal) {
										if ($PostTagsVal->getTags() != '') {
											$tag_status = '1';
										} else {
											$tag_status = '0';
										}
									}
								} else {
									$tag_status = '0';
								}


								$UserConsumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
									['id' => $tagedUser]
								);
								$User_postConsumer = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:User"
								)->findOneBy(['id' => $postedUser]);

								if ($usertype == '1') {

									if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

										$user_id = $sp_user_id;
										$user_name = $userName;
										$post_usertype = $usertype;
									}

								} else {
									if ($usertype == '0') {
										if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

											if ($User_postConsumer->getId() == $UserConsumer->getId()) {
												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $usertype;
											} else {
												if ($UserConsumer->getUserType() == '1') {
													$user_id = $UserConsumer->getId();
													if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
													) {
														$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
													} else {
														$user_name = '';

													}
													$post_usertype = $UserConsumer->getUserType();
												} else {
													if ($User_postConsumer->getUserType() == '1') {
														$user_id = $User_postConsumer->getId();
														if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
														) {
															$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
														} else {
															$user_name = '';
														}
														$post_usertype = $User_postConsumer->getUserType();
													} else {
														$user_id = '';
														$user_name = '';
														$post_usertype = $usertype;
													}
												}

											}


										}


									}
								}


								/*  if ($UserConsumer != '' && $UserConsumer != null) {
                                    $user_id = $UserConsumer->getId();
                                    if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                        $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                } else {
                                    $user_name = '';
                                }
                               */


								$UserReviews = $conn->query(
									"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $tagedUser . ""
								)->fetchAll();
								if ($UserReviews != '') {
									foreach ($UserReviews as $UserReviewsval) {

										// echo '<pre>';print_r($reviews);
										if ($UserReviewsval['rate'] != null) {
											//want full rating not rounded
											$rateavg = round($UserReviewsval['rate'],1);
										} else {
											$rateavg = '';
										}
										$reviews = $UserReviewsval['reviews'];
										unset($rating);
									}
								} else {
									$rateavg = '';
									$reviews = '';
									$ratvalues = '';
								}


								$album_detail[] = array(
									'album_id' => $album_id,
									'tag_status' => $tag_status,
									'album_service' => $post_caption,
									'user_id' => $user_id,
									'user_type' => $post_usertype,
									'album_image' => $album_image,
									'user_name' => $user_name,
									'rates' => $rateavg
								);
							}
						} else {
							$album_detail = [];
							$ratvalues = '';
						}

						$UserReviews = $conn->query(
							"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $resultVal['toUserID'] . " "
						)->fetchAll();
						if ($UserReviews != '') {
							foreach ($UserReviews as $UserReviewsval) {

								if ($UserReviewsval['rate'] != null) {
									//want full rate not rounded
									$rateavg = round($UserReviewsval['rate'],1);
								} else {
									$rateavg = '';
								}
								$reviews = $UserReviewsval['reviews'];
								unset($rating);
							}
						} else {
							$rateavg = '';
							$reviews = '';
							$ratvalues = '';
						}


						if (count($reviews) > 0 && $reviews != null) {
							$countReview = $reviews;
						} else {
							$countReview = 0;
						}
						if (count($rateavg) > 0 && $rateavg != null) {
							$countRates = $rateavg;
						} else {
							$countRates = 0;
						}

						$sp_detail[] = array(
							'user_name' => ($userName),
							'user_type' => $usertype,
							'follow_status' => $follow_status,
							'sp_user_id' => ($sp_user_id),
							'contact' => ($userContact),
							'company_name' => ($companyName),
							'user_address' => ($userAddres),
							'profile_image' => $profileImage
						,
							'service_name' => ($service_name),
							'service_price' => ($serviceprice),
							'user_chat' => 0,
							'total_reviews' => ($countReview),
							'total_rate' => ($countRates),
							'albums' => $album_detail
						);
						unset($album_detail);
						//echo '<pre>';print_r($sp_detail);
					}
				}

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'successfull',
						'suggested' => $sp_detail,
						'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
					)
				);

			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		}
	}

	/*     * **************************************************************************SUGGESTED End******************************************** */

	/**
	 * @Route("/viewallservices", name="_viewallservices")
	 * @Template()
	 */
	/*     * *************************************************************************************VIEW MY SERVICES BEGIN ********************************************* */
	public function viewallservicesAction(Request $user_id, Request $counter, Request $customer_id, Request $login_type)
	{
		$request = $this->getRequest();
		$customerID = $request->get('customer_id');
		$userID = $request->get('user_id');
		$loginType = $request->get('login_type');
		$limitset = 9;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;


		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();

		$relatedUser = $conn->query(
			"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
		)->fetchAll();
		if ($relatedUser != '' || $relatedUser != null) {
			foreach ($relatedUser as $relatedUserval) {
				$albumID[] = $relatedUserval['postID'];
			}
		} else {
			$albumID = '';
		}

		if (($customerID == '' && $loginType == '') || ($customerID == 'null' && $loginType == 'null')) {
			$customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
				['id' => $albumID, 'postStatus' => '0'],
				array('id' => 'desc'),
				$max_num,
				$min_num
			);
//echo '<pre>';print_r($customers);die;
			if ($customers != '' && $customers != null) {
				foreach ($customers as $customersVal) {
					$album_id = $customersVal->getId();
					if ($customersVal->getPostCaption() != '') {
						$post_caption = $customersVal->getPostCaption();
					} else {
						$post_caption = '';
					}

					if ($customersVal->getUserTagID() != '') {
						$tagedUser = $customersVal->getUserTagID();
					} else {
						$tagedUser = '';
					}
					$date = $customersVal->getPostDate();
					$explodedate = explode(' ', $date);
					$datePost = $explodedate[0];
					//echo '<pre>';print_r($datePost);die;
					if ($customersVal->getPostImageFront() != '' && $customersVal->getPostImageFront() != null) {
						$image = $this->baseurl() . $customersVal->getPostImageFront();
					} elseif ($customersVal->getPostImageFrontLeft() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageFrontLeft();
					} elseif ($customersVal->getPostImageLeft() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageLeft();
					} elseif ($customersVal->getPostImageBackLeft() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageBackLeft();
					} elseif ($customersVal->getPostImageBack() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageBack();
					} elseif ($customersVal->getPostImageBackRight() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageBackRight();
					} elseif ($customersVal->getPostImageRight() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageRight();
					} elseif ($customersVal->getPostImageFrontRight() != '') {
						$image = $this->baseurl() . $customersVal->getPostImageFrontRight();
					} else {
						$image = '';
					}
					$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['postID' => $album_id]
					);
					if ($PostTags != '') {
						$tag_status = '0';
						foreach ($PostTags as $PostTagsVal) {
							if ($PostTagsVal->getTags() != '') {
								$tag_status = '1';
							}
						}
					} else {
						$tag_status = '0';
					}
					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $customersVal->getUserID()]
					);
					if ($User != '' && $User != null) {
						$user_id = $User->getId();
						if ($User->getUserFirstName() || $User->getUserLastName() != '') {
							$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$user_name = '';
						}
					} else {
						$user_name = '';
					}
					$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
						['toUserID' => $tagedUser]
					);
					if ($UserRating != '' && $UserRating != null) {
						foreach ($UserRating as $UserRatingVal) {
							if ($UserRatingVal->getUserRating() != '') {
								$ratings[] = $UserRatingVal->getUserRating();
							} else {
								$ratings[] = '';
							}
							$count = count($ratings);
							$rating = array_sum($ratings) / $count;
							$ratvalues = number_format((float)$rating, 1, '.', '');
						}
					} else {
						$ratvalues = '';
					}
					if (!empty($image)) {
						$album_detail[] = array(
							'album_id' => ($album_id),
							'tag_status' => $tag_status,
							'album_service' => ($post_caption),
							'user_id' => ($user_id),
							'album_image' => ($image),
							'user_id' => ($user_id),
							'user_name' => ($user_name),
							'rates' => ($ratvalues),
							'date' => $datePost
						);
					}
				}
				echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album_detail));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			if (($customerID == '' && $loginType == '1') || ($customerID == 'null' && $loginType == '1')) {
				$customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $albumID],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);

				if ($customers != '' && $customers != null) {
					foreach ($customers as $customersVal) {
						$album_id = $customersVal->getId();
						if ($customersVal->getPostCaption() != '') {
							$post_caption = $customersVal->getPostCaption();
						} else {
							$post_caption = '';
						}

						if ($customersVal->getUserTagID() != '') {
							$tagedUser = $customersVal->getUserTagID();
						} else {
							$tagedUser = '';
						}
						$date = $customersVal->getPostDate();
						$explodedate = explode(' ', $date);
						$datePost = $explodedate[0];
						//echo '<pre>';print_r($datePost);die;
						if ($customersVal->getPostImageFront() != '' && $customersVal->getPostImageFront() != null) {
							$image = $this->baseurl() . $customersVal->getPostImageFront();
						} elseif ($customersVal->getPostImageFrontLeft() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageFrontLeft();
						} elseif ($customersVal->getPostImageLeft() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageLeft();
						} elseif ($customersVal->getPostImageBackLeft() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageBackLeft();
						} elseif ($customersVal->getPostImageBack() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageBack();
						} elseif ($customersVal->getPostImageBackRight() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageBackRight();
						} elseif ($customersVal->getPostImageRight() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageRight();
						} elseif ($customersVal->getPostImageFrontRight() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageFrontRight();
						} else {
							$image = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $album_id]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $tagedUser]
						);
						if ($User != '' && $User != null) {
							$user_id = $User->getId();
							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$user_name = '';
							}
						} else {
							$user_name = '';
						}
						$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $tagedUser]
						);
						if ($UserRating != '' && $UserRating != null) {
							foreach ($UserRating as $UserRatingVal) {
								if ($UserRatingVal->getUserRating() != '') {
									$ratings[] = $UserRatingVal->getUserRating();
								} else {
									$ratings[] = '';
								}
								$count = count($ratings);
								$rating = array_sum($ratings) / $count;
								$ratvalues = number_format((float)$rating, 1, '.', '');
							}
						} else {
							$ratvalues = '';
						}
						if (!empty($image)) {
							$album_detail[] = array(
								'album_id' => ($album_id),
								'tag_status' => $tag_status,
								'album_service' => ($post_caption),
								'user_id' => ($user_id),
								'album_image' => ($image),
								'user_id' => ($user_id),
								'user_name' => ($user_name),
								'rates' => ($ratvalues),
								'date' => $datePost
							);
						}
					}
					echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album_detail));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} elseif (($customerID != '' && $loginType == '0') || ($customerID != 'null' && $loginType == '0')) {
				$customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $albumID],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);

				if ($customers != '' && $customers != null) {
					foreach ($customers as $customersVal) {
						$album_id = $customersVal->getId();
						if ($customersVal->getPostCaption() != '') {
							$post_caption = $customersVal->getPostCaption();
						} else {
							$post_caption = '';
						}

						if ($customersVal->getUserTagID() != '') {
							$tagedUser = $customersVal->getUserTagID();
						} else {
							$tagedUser = '';
						}
						$date = $customersVal->getPostDate();
						$explodedate = explode(' ', $date);
						$datePost = $explodedate[0];
						//echo '<pre>';print_r($datePost);die;
						if ($customersVal->getPostImageFront() != '' && $customersVal->getPostImageFront() != null) {
							$image = $this->baseurl() . $customersVal->getPostImageFront();
						} elseif ($customersVal->getPostImageFrontLeft() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageFrontLeft();
						} elseif ($customersVal->getPostImageLeft() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageLeft();
						} elseif ($customersVal->getPostImageBackLeft() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageBackLeft();
						} elseif ($customersVal->getPostImageBack() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageBack();
						} elseif ($customersVal->getPostImageBackRight() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageBackRight();
						} elseif ($customersVal->getPostImageRight() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageRight();
						} elseif ($customersVal->getPostImageFrontRight() != '') {
							$image = $this->baseurl() . $customersVal->getPostImageFrontRight();
						} else {
							$image = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $album_id]
						);
						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $tagedUser]
						);
						if ($User != '' && $User != null) {
							$user_id = $User->getId();
							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$user_name = '';
							}
						} else {
							$user_name = '';
						}
						$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $tagedUser]
						);
						if ($UserRating != '' && $UserRating != null) {
							foreach ($UserRating as $UserRatingVal) {
								if ($UserRatingVal->getUserRating() != '') {
									$ratings[] = $UserRatingVal->getUserRating();
								} else {
									$ratings[] = '';
								}
								$count = count($ratings);
								$rating = array_sum($ratings) / $count;
								$ratvalues = number_format((float)$rating, 1, '.', '');
							}
						} else {
							$ratvalues = '';
						}
						if (!empty($image)) {
							$album_detail[] = array(
								'album_id' => ($album_id),
								'tag_status' => $tag_status,
								'album_service' => ($post_caption),
								'user_id' => ($user_id),
								'album_image' => ($image),
								'user_id' => ($user_id),
								'user_name' => ($user_name),
								'rates' => ($ratvalues),
								'date' => $datePost
							);
						}
					}
					echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album_detail));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		}
	}

	/*     * **************************************************************************VIEW ALL MY SERVCIES END ********************************** */

	/**
	 * @Route("/viewallcustomer", name="_viewallcustomer")
	 * @Template()
	 */
	/*     * *************************************************************************************VIEW MY SERVICES BEGIN ********************************************* */
	public function viewallcustomerAction(Request $user_id, Request $counter, Request $customer_id, Request $login_type)
	{
		$request = $this->getRequest();
		$customerID = $request->get('customer_id');
		$userID = $request->get('user_id');
		$loginType = $request->get('login_type');
		$limitset = 9;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();

		$relatedUser = $conn->query(
			"select * from post where userID=" . $userID . " || userTagID=" . $userID . ""
		)->fetchAll();
		if ($relatedUser != '' || $relatedUser != null) {
			foreach ($relatedUser as $relatedUserval) {
				$albumIDs[] = $relatedUserval['postID'];
			}
		} else {
			$albumIDs = '';
		}

		if (($customerID == '' && $loginType == '') || ($customerID == 'null' && $loginType == 'null')) {
			$CustomerImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
				['id' => $albumIDs, 'postStatus' => '0'],
				array('id' => 'desc'),
				$max_num,
				$min_num
			);
//echo '<pre>';print_r($CustomerImage);die;
			if ($CustomerImage != '' && $CustomerImage != null) {
				foreach ($CustomerImage as $CustomerImageVal) {
					if ($CustomerImageVal->getPostCaption() != '') {
						$post_caption = $CustomerImageVal->getPostCaption();
					} else {
						$post_caption = '';
					}
					if ($CustomerImageVal->getId() != '') {
						$albumID = $CustomerImageVal->getId();
					} else {
						$albumID = '';
					}
					$date = $CustomerImageVal->getPostDate();
					$explodedate = explode(' ', $date);
					$datePost = $explodedate[0];

					if ($CustomerImageVal->getPostImageFront() != '' && $CustomerImageVal->getPostImageFront() != null
					) {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageFront();
					} elseif ($CustomerImageVal->getPostImageFrontLeft() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontLeft();
					} elseif ($CustomerImageVal->getPostImageLeft() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageLeft();
					} elseif ($CustomerImageVal->getPostImageBackLeft() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageBackLeft();
					} elseif ($CustomerImageVal->getPostImageBack() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageBack();
					} elseif ($CustomerImageVal->getPostImageBackRight() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageBackRight();
					} elseif ($CustomerImageVal->getPostImageRight() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageRight();
					} elseif ($CustomerImageVal->getPostImageFrontRight() != '') {
						$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontRight();
					} else {
						$image = '';
					}
					$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['postID' => $albumID]
					);

					if ($PostTags != '') {
						$tag_status = '0';
						foreach ($PostTags as $PostTagsVal) {
							if ($PostTagsVal->getTags() != '') {
								$tag_status = '1';
							}
						}
					} else {
						$tag_status = '0';
					}
					//echo '<pre>';print_r($tag_status);
					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $CustomerImageVal->getUserTagID()]
					);
					if ($User != '' && $User != null) {
						$user_id = $User->getId();
						if ($User->getUserFirstName() || $User->getUserLastName() != '') {
							$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$user_name = '';
						}
					} else {
						$user_name = '';
					}
					$albumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
						['postID' => $albumID]
					);
					if ($albumRate != '' && $albumRate != null) {

						if ($albumRate->getUserRating() != '') {
							$postRating = $albumRate->getUserRating();
						} else {
							$postRating = '';
						}
					} else {
						$postRating = '';
					}
					if (isset($image) && !empty($image)) {
						$album[] = array(
							'album_id' => $albumID,
							'user_id' => $user_id,
							'tag_status' => $tag_status,
							'album_image' => $image,
							'rates' => $postRating,
							'album_service' => $post_caption,
							'date' => $datePost
						);
					}
					//echo '<pre>';print_r($album);
				}//die;
				echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			if (($customerID == '' && $loginType == '0') || ($customerID == 'null' && $loginType == '0')) {
				$CustomerImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $albumIDs],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);
//echo '<pre>';print_r($CustomerImage);die;
				if ($CustomerImage != '' && $CustomerImage != null) {
					foreach ($CustomerImage as $CustomerImageVal) {
						if ($CustomerImageVal->getPostCaption() != '') {
							$post_caption = $CustomerImageVal->getPostCaption();
						} else {
							$post_caption = '';
						}
						if ($CustomerImageVal->getId() != '') {
							$albumID = $CustomerImageVal->getId();
						} else {
							$albumID = '';
						}
						$date = $CustomerImageVal->getPostDate();
						$explodedate = explode(' ', $date);
						$datePost = $explodedate[0];

						if ($CustomerImageVal->getPostImageFront() != '' && $CustomerImageVal->getPostImageFront() != null
						) {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFront();
						} elseif ($CustomerImageVal->getPostImageFrontLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontLeft();
						} elseif ($CustomerImageVal->getPostImageLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageLeft();
						} elseif ($CustomerImageVal->getPostImageBackLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBackLeft();
						} elseif ($CustomerImageVal->getPostImageBack() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBack();
						} elseif ($CustomerImageVal->getPostImageBackRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBackRight();
						} elseif ($CustomerImageVal->getPostImageRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageRight();
						} elseif ($CustomerImageVal->getPostImageFrontRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontRight();
						} else {
							$image = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $albumID]
						);

						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						//echo '<pre>';print_r($tag_status);
						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $CustomerImageVal->getUserTagID()]
						);
						if ($User != '' && $User != null) {
							$user_id = $User->getId();
							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$user_name = '';
							}
						} else {
							$user_name = '';
						}
						$albumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $albumID]
						);
						if ($albumRate != '' && $albumRate != null) {

							if ($albumRate->getUserRating() != '') {
								$postRating = $albumRate->getUserRating();
							} else {
								$postRating = '';
							}
						} else {
							$postRating = '';
						}
						if (isset($image) && !empty($image)) {
							$album[] = array(
								'album_id' => $albumID,
								'user_id' => $user_id,
								'tag_status' => $tag_status,
								'album_image' => $image,
								'rates' => $postRating,
								'album_service' => $post_caption,
								'date' => $datePost
							);
						}
						//echo '<pre>';print_r($album);
					}//die;
					echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} elseif (($customerID != '' && $loginType == '1') || ($customerID != 'null' && $loginType == '1')) {
				$CustomerImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
					['id' => $albumIDs],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);
//echo '<pre>';print_r($CustomerImage);die;
				if ($CustomerImage != '' && $CustomerImage != null) {
					foreach ($CustomerImage as $CustomerImageVal) {
						if ($CustomerImageVal->getPostCaption() != '') {
							$post_caption = $CustomerImageVal->getPostCaption();
						} else {
							$post_caption = '';
						}
						if ($CustomerImageVal->getId() != '') {
							$albumID = $CustomerImageVal->getId();
						} else {
							$albumID = '';
						}
						$date = $CustomerImageVal->getPostDate();
						$explodedate = explode(' ', $date);
						$datePost = $explodedate[0];

						if ($CustomerImageVal->getPostImageFront() != '' && $CustomerImageVal->getPostImageFront() != null
						) {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFront();
						} elseif ($CustomerImageVal->getPostImageFrontLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontLeft();
						} elseif ($CustomerImageVal->getPostImageLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageLeft();
						} elseif ($CustomerImageVal->getPostImageBackLeft() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBackLeft();
						} elseif ($CustomerImageVal->getPostImageBack() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBack();
						} elseif ($CustomerImageVal->getPostImageBackRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageBackRight();
						} elseif ($CustomerImageVal->getPostImageRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageRight();
						} elseif ($CustomerImageVal->getPostImageFrontRight() != '') {
							$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontRight();
						} else {
							$image = '';
						}
						$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
							['postID' => $albumID]
						);

						if ($PostTags != '') {
							$tag_status = '0';
							foreach ($PostTags as $PostTagsVal) {
								if ($PostTagsVal->getTags() != '') {
									$tag_status = '1';
								}
							}
						} else {
							$tag_status = '0';
						}
						//echo '<pre>';print_r($tag_status);
						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $CustomerImageVal->getUserTagID()]
						);
						if ($User != '' && $User != null) {
							$user_id = $User->getId();
							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$user_name = '';
							}
						} else {
							$user_name = '';
						}
						$albumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $albumID]
						);
						if ($albumRate != '' && $albumRate != null) {

							if ($albumRate->getUserRating() != '') {
								$postRating = $albumRate->getUserRating();
							} else {
								$postRating = '';
							}
						} else {
							$postRating = '';
						}
						if (isset($image) && !empty($image)) {
							$album[] = array(
								'album_id' => $albumID,
								'user_id' => $user_id,
								'tag_status' => $tag_status,
								'album_image' => $image,
								'rates' => $postRating,
								'album_service' => $post_caption,
								'date' => $datePost
							);
						}
						//echo '<pre>';print_r($album);
					}//die;
					echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {
				if (($customerID != '' && $loginType == '0') || ($customerID != 'null' && $loginType == '0')) {
					$CustomerImage = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
						['id' => $albumIDs],
						array('id' => 'desc'),
						$max_num,
						$min_num
					);
//echo '<pre>';print_r($CustomerImage);die;
					if ($CustomerImage != '' && $CustomerImage != null) {
						foreach ($CustomerImage as $CustomerImageVal) {
							if ($CustomerImageVal->getPostCaption() != '') {
								$post_caption = $CustomerImageVal->getPostCaption();
							} else {
								$post_caption = '';
							}
							if ($CustomerImageVal->getId() != '') {
								$albumID = $CustomerImageVal->getId();
							} else {
								$albumID = '';
							}
							$date = $CustomerImageVal->getPostDate();
							$explodedate = explode(' ', $date);
							$datePost = $explodedate[0];

							if ($CustomerImageVal->getPostImageFront() != '' && $CustomerImageVal->getPostImageFront() != null
							) {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageFront();
							} elseif ($CustomerImageVal->getPostImageFrontLeft() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontLeft();
							} elseif ($CustomerImageVal->getPostImageLeft() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageLeft();
							} elseif ($CustomerImageVal->getPostImageBackLeft() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageBackLeft();
							} elseif ($CustomerImageVal->getPostImageBack() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageBack();
							} elseif ($CustomerImageVal->getPostImageBackRight() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageBackRight();
							} elseif ($CustomerImageVal->getPostImageRight() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageRight();
							} elseif ($CustomerImageVal->getPostImageFrontRight() != '') {
								$image = $this->baseurl() . $CustomerImageVal->getPostImageFrontRight();
							} else {
								$image = '';
							}
							$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
								['postID' => $albumID]
							);

							if ($PostTags != '') {
								$tag_status = '0';
								foreach ($PostTags as $PostTagsVal) {
									if ($PostTagsVal->getTags() != '') {
										$tag_status = '1';
									}
								}
							} else {
								$tag_status = '0';
							}
							//echo '<pre>';print_r($tag_status);
							$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $CustomerImageVal->getUserTagID()]
							);
							if ($User != '' && $User != null) {
								$user_id = $User->getId();
								if ($User->getUserFirstName() || $User->getUserLastName() != '') {
									$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
								} else {
									$user_name = '';
								}
							} else {
								$user_name = '';
							}
							$albumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
								['postID' => $albumID]
							);
							if ($albumRate != '' && $albumRate != null) {

								if ($albumRate->getUserRating() != '') {
									$postRating = $albumRate->getUserRating();
								} else {
									$postRating = '';
								}
							} else {
								$postRating = '';
							}
							if (isset($image) && !empty($image)) {
								$album[] = array(
									'album_id' => $albumID,
									'user_id' => $user_id,
									'tag_status' => $tag_status,
									'album_image' => $image,
									'rates' => $postRating,
									'album_service' => $post_caption,
									'date' => $datePost
								);
							}
							//echo '<pre>';print_r($album);
						}//die;
						echo json_encode(array('success' => 1, 'message' => 'success', 'albums' => $album));
					} else {
						echo json_encode(array('success' => 0, 'message' => 'failure'));
					}
				}
			}
		}
	}

	/*     * ************************************************************************VIEW ALL CUSTOMER END ************************ */

	/**
	 * @Route("/local", name="_local")
	 * @Template()
	 */
	/*     * *************************************************************************************LOCAL BEGIN ********************************************* */
	public function localAction(Request $user_id, Request $lat, Request $long, Request $counter)
	{
		$request = $this->getRequest();
		$lat = $request->get('lat');
		$long = $request->get('long');
		$limitset = 4;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$em = $this->getDoctrine()->getEntityManager();
		$sp_id = $request->get('user_id');
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$result = $conn->query(
			"SELECT  Distinct ROUND((((acos(sin((" . $lat . "*pi()/180)) * 
          sin((`lat`*pi()/180))+cos((" . $lat . "*pi()/180)) * 
          cos((`lat`*pi()/180)) * cos(((" . $long . "- `longitute`)* 
          pi()/180))))*180/pi())*60*1.1515
      ),0) as distance,userID  FROM awn_user WHERE lat !=0
      HAVING 
      distance<=20  limit " . $min_num . "," . $max_num . ""
		)->fetchAll();
		// echo '<pre>';print_r($result);die;
		if ($result != '' && $result != null) {
			foreach ($result as $resultVal) {
				if ($resultVal['userID'] != $sp_id) {


					$users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $resultVal['userID']]
					);
//        limit ".$min_num.",".$max_num."
//        echo '<pre>';print_r($users);die; 
					if ($users != '' && $users != null) {
						// foreach ($users as $Values) {
						//echo '<pre>';print_r($Values->getId());
						$id = $users->getId();

						$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
							['followStatus' => '1']
						);
						//echo '<pre>';print_r($UserFollow);
						if ($UserFollow != '') {
							$follow_status = '0';
							foreach ($UserFollow as $UserFollowVal) {
								if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $id)) {
									$follow_status = '1';
								}
							}
						} else {
							$follow_status = '0';
						}

						if ($users->getUserFirstName() || $users->getUserLastName() != '') {
							$userName = $users->getUserFirstName() . ' ' . $users->getUserLastName();
						} else {
							$userName = '';
						}
						if ($users->getCompanyName() != '') {
							$companyName = $users->getCompanyName();
						} else {
							$companyName = '';
						}
						if ($users->getUserAddress() != '') {
							$userAddres = $users->getUserAddress();
						} else {
							$userAddres = '';
						}
						if ($users->getUserMobileNo() != '') {
							$userContact = $users->getUserMobileNo();
						} else {
							$userContact = '';
						}
						$userType = $users->getUserType();
						if ($users->getUserProfileImage() != '' && $users->getUserProfileImage() > 0) {
							$profileImage = $this->baseurl() . $users->getUserProfileImage();
						} else {
							$profileImage = $this->baseurl() . 'defaultprofile.png';
						}
						$serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findOneBy(
							['userID' => $id, 'topService' => 1]
						);

						if ($serviceName != '' && $serviceName != null) {
							$serviceId = $serviceName->getServiceID();

							$serviceprice = $serviceName->getServicePrice();
							$masterService = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $serviceId]);
							if ($masterService != '') {
								$service_name = $masterService->getServiceName();
							} else {
								$service_name = '';
							}
						} else {
							$serviceprice = '';
							$service_name = '';
						}

//                    $customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(['userID' => $id, 'postStatus' => 0]);
//                    //echo '<pre>';print_r($customers);
//                    if ($customers != '' && $customers != null) {
//                        foreach ($customers as $customersVal) {
//                            $spID[] = $customersVal->getUserTagID();
//                        }
//                    } else {
//                        $spID = [];
//                    }
//                    for ($i = 0; $i < count($spID); $i++) {
//                        if (($spID[$i] != $sp_id)) {
//
//                            $albumsID[] = $spID[$i];
//                        }
//                    }
//                    if (count($spID) < 1) {
//                        $albumsID = '';
//                    }
						//$manager = $this->getDoctrine()->getManager();
						//$conn = $manager->getConnection();

						$AlbumsModel = $conn->query(
							"SELECT postID FROM `post` where (postStatus = '0' and userID = " . $resultVal['userID'] . ") or (postStatus = '0' and userTagID = " . $resultVal['userID'] . ") order by postID desc  limit 0,6"
						)->fetchAll();
						if (isset($AlbumsModel) && !empty($AlbumsModel)) {
							foreach ($AlbumsModel as $customersAlbum) {

								$customersAlbumVal = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:AlbumPost"
								)->findOneBy(['id' => $customersAlbum['postID']]);

								if ($customersAlbumVal != '' && $customersAlbumVal != null) {

									$album_id = $customersAlbumVal->getId();
									if ($customersAlbumVal->getPostCaption() != '') {
										$post_caption = $customersAlbumVal->getPostCaption();
									} else {
										$post_caption = '';
									}

									if ($customersAlbumVal->getUserTagID() != '') {
										$tagedUser = $customersAlbumVal->getUserTagID();
									} else {
										$tagedUser = '';
									}
									if ($customersAlbumVal->getUserID() != '') {
										$postedUser = $customersAlbumVal->getUserID();
									} else {
										$postedUser = '';
									}

									if ($customersAlbumVal->getPostImageFront() != '' && $customersAlbumVal->getPostImageFront() != null
									) {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageFront();
									} elseif ($customersAlbumVal->getPostImageFrontLeft() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageFrontLeft();
									} elseif ($customersAlbumVal->getPostImageLeft() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageLeft();
									} elseif ($customersAlbumVal->getPostImageBackLeft() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageBackLeft();
									} elseif ($customersAlbumVal->getPostImageBack() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageBack();
									} elseif ($customersAlbumVal->getPostImageBackRight() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageBackRight();
									} elseif ($customersAlbumVal->getPostImageRight() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageRight();
									} elseif ($customersAlbumVal->getPostImageFrontRight() != '') {
										$album_image = $this->baseurl() . $customersAlbumVal->getPostImageFrontRight();
									} else {
										$album_image = '';
									}
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $album_id]
									);
									if ($PostTags != '') {
										$tag_status = '0';
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											}
										}
									} else {
										$tag_status = '0';
									}
									$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $tagedUser]
									);
									$User_postConsumer = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:User"
									)->findOneBy(['id' => $postedUser]);

									if ($userType == '1') {

										if ($User != '' && $User != null && $User_postConsumer != '' && $User_postConsumer != null) {

											$user_id = $id;
											$user_name = $userName;
											$post_usertype = $userType;
										}

									} else {
										if ($userType == '0') {
											if ($User != '' && $User != null && $User_postConsumer != '' && $User_postConsumer != null) {

												if ($User_postConsumer->getId() == $User->getId()) {
													$user_id = $id;
													$user_name = $userName;
													$post_usertype = $userType;
												} else {
													if ($User->getUserType() == '1') {
														$user_id = $User->getId();
														if ($User->getUserFirstName() || $User->getUserLastName() != ''
														) {
															$user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
														} else {
															$user_name = '';

														}
														$post_usertype = $User->getUserType();
													} else {
														if ($User_postConsumer->getUserType() == '1') {
															$user_id = $User_postConsumer->getId();
															if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
															) {
																$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
															} else {
																$user_name = '';
															}
															$post_usertype = $User_postConsumer->getUserType();
														} else {
															$user_id = '';
															$user_name = '';
															$post_usertype = $userType;
														}
													}

												}


											}


										}
									}

									/* if ($User != '' && $User != null) {
                            $user_id = $User->getId();
                            if ($User->getUserFirstName() || $User->getUserLastName() != '') {
                                $user_name = $User->getUserFirstName() . ' ' . $User->getUserLastName();
                            } else {
                                $user_name = '';
                            }
                        } else {
                            $user_name = '';
                        }
                        */


									$UserRating = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:UserRating"
									)->findBy(['toUserID' => $tagedUser]);
									if ($UserRating != '' && $UserRating != null) {
										foreach ($UserRating as $UserRatingVal) {
											if ($UserRatingVal->getUserRating() != '') {
												$ratings[] = $this->baseurl() . $UserRatingVal->getUserRating();
											} else {
												$ratings[] = '';
											}
											$count = count($ratings);
											$rating = array_sum($ratings) / $count;
											$ratvalues = number_format((float)$rating, 1, '.', '');
										}
									} else {
										$ratvalues = '';
									}
									$album_detail[] = array(
										'album_id' => ($album_id),
										'tag_status' => $tag_status,
										'album_service' => ($post_caption),
										'user_id' => ($user_id),
										'user_type' => $post_usertype,
										'album_image' => ($album_image),
										'user_name' => ($user_name),
										'rates' => ($ratvalues)
									);
								}
							}
						} else {
							$album_detail = [];
						}


						$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $users->getId()]
						);

						if ($UserRating != '' && $UserRating != null) {
							foreach ($UserRating as $UserRatingVal) {
								if ($UserRatingVal->getUserRating() != '') {
									$rating1[] = $UserRatingVal->getUserRating();
								} else {
									$rating1[] = '';
								}
								if ($UserRatingVal->getUserReviews() != '') {
									$reviews[] = $UserRatingVal->getUserReviews();
								} else {
									$reviews[] = '';
								}
								$count = count($rating1);
								$rating = array_sum($rating1) / $count;
								$ratvalues = number_format((float)$rating, 1, '.', '');
							}
						} else {
							$ratvalues = '';
							$reviews = '';
						}
						if (count($reviews) > 0 && $reviews != null) {
							$countReview = count($reviews);
						} else {
							$countReview = 0;
						}
						if (count($ratvalues) > 0 && $ratvalues != null) {
							$countRates = $ratvalues;
						} else {
							$countRates = 0;
						}

						$sp_detail[] = ([
							'user_name' => ($userName),
							'user_type' => $userType,
							'follow_status' => $follow_status,
							'sp_user_id' => ($users->getId()),
							'contact' => ($userContact),
							'company_name' => ($companyName),
							'user_address' => ($userAddres),
							'profile_image' => ($profileImage)
							,
							'service_name' => ($service_name),
							'service_price' => ($serviceprice),
							'user_chat' => 0,
							'total_reviews' => ($countReview),
							'total_rate' => ($countRates),
							'albums' => $album_detail
						]);
						unset($album_detail);
					}
				}
			}
			echo json_encode(array('success' => 1, 'message' => 'successfull', 'my_services' => $sp_detail));

			return array();


		} else {

			$result = $conn->query(
				"select * from (SELECT toUserID, AVG(userRating) AS rt
FROM user_rating
GROUP BY toUserID
UNION ALL
SELECT fromUserID, AVG(userRating) AS rt
FROM user_rating
GROUP BY fromUserID )AS userrate ORDER BY rt DESC limit " . $min_num . "," . $max_num . ""
			)->fetchAll();

//echo '<pre>';print_r($result);die;
			if (!empty($sp_id)) {

				if ($result != '' && $result != null) {

					foreach ($result as $resultVal) {
						if ($resultVal['toUserID'] != $sp_id) {

							$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $resultVal['toUserID']]
							);

							if ($User != '' && $User != null) {

								$user_type = $User->getUserType();
								$sp_user_id = $User->getId();

								$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
									['followStatus' => '1']
								);

								if ($UserFollow != '') {
									$follow_status = '0';
									foreach ($UserFollow as $UserFollowVal) {

										if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
										) {

											$follow_status = '1';
										}
									}
								} else {
									$follow_status = '0';
								}

								if ($User->getUserFirstName() || $User->getUserLastName() != '') {
									$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
								} else {
									$userName = '';
								}
								if ($User->getCompanyName() != '') {
									$companyName = $User->getCompanyName();
								} else {
									$companyName = '';
								}
								if ($User->getUserAddress() != '') {
									$userAddres = $User->getUserAddress();
								} else {
									$userAddres = '';
								}
								if ($User->getUserMobileNo() != '') {
									$userContact = $User->getUserMobileNo();
								} else {
									$userContact = '';
								}
								if ($User->getUserProfileImage() != '' && $User->getUserProfileImage() > 0) {
									$profileImage = $this->baseurl() . $User->getUserProfileImage();
								} else {
									$profileImage = $this->baseurl() . 'defaultprofile.png';
								}
								$serviceName = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:UserServices"
								)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

								if ($serviceName != '' && $serviceName != null) {
									$serviceId = $serviceName->getServiceID();

									$serviceprice = $serviceName->getServicePrice();
									$masterService = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:MasterServices"
									)->findOneBy(['id' => $serviceId]);
									$service_name = $masterService->getServiceName();
								} else {
									$serviceprice = '';
									$service_name = '';
								}

//                         $manager = $this->getDoctrine()->getManager();
//        $conn = $manager->getConnection();
//
//        $relatedUser = $conn->query(
//                        "select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . ""
//                )->fetchAll();
//        if($relatedUser != ''){
//            foreach($relatedUser as $relatedUserval){
//             $postID[]  = $relatedUserval['postID'];
//            }
//        }else{
//            $postID  = '';
//        }
								$userrate = $conn->query(
									"select user_rating.userRating,post.postID as post_id from user_rating left join post on user_rating.postID = post.postID where   post.postStatus = '0' and  user_rating.toUserID =" . $resultVal['toUserID'] . " OR post.postStatus = '0' and user_rating.fromUserID = " . $resultVal['toUserID'] . "   order by post.postID desc limit 0,6"
								)->fetchAll();


								if ($userrate != '' && $userrate != null) {
									foreach ($userrate as $customers) {
										$customersValues = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:AlbumPost"
										)->findOneBy(['id' => $customers['post_id']]);
										$album_id = $customersValues->getId();
										if ($customersValues->getPostCaption() != '') {
											$post_caption = $customersValues->getPostCaption();
										} else {
											$post_caption = '';
										}

										if ($customersValues->getUserTagID() != '') {
											$tagedUser = $customersValues->getUserTagID();
										} else {
											$tagedUser = '';
										}
										if ($customersValues->getUserID() != '') {
											$postedUser = $customersValues->getUserID();
										} else {
											$postedUser = '';
										}


										if ($customersValues->getPostImageFront() != '' && $customersValues->getPostImageFront() != null
										) {
											$album_image = $this->baseurl() . $customersValues->getPostImageFront();
										} elseif ($customersValues->getPostImageFrontLeft() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageFrontLeft();
										} elseif ($customersValues->getPostImageLeft() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageLeft();
										} elseif ($customersValues->getPostImageBackLeft() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageBackLeft();
										} elseif ($customersValues->getPostImageBack() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageBack();
										} elseif ($customersValues->getPostImageBackRight() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageBackRight();
										} elseif ($customersValues->getPostImageRight() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageRight();
										} elseif ($customersValues->getPostImageFrontRight() != '') {
											$album_image = $this->baseurl() . $customersValues->getPostImageFrontRight();
										} else {
											$album_image = '';
										}
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $album_id]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}


										$UserConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $tagedUser]);
										$User_postConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $postedUser]);

										if ($user_type == '1') {


											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $user_type;
											}

										} else {
											if ($user_type == '0') {
												if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

													if ($User_postConsumer->getId() == $UserConsumer->getId()) {
														$user_id = $sp_user_id;
														$user_name = $userName;
														$post_usertype = $user_type;
													} else {
														if ($UserConsumer->getUserType() == '1') {
															$user_id = $UserConsumer->getId();
															if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
															) {
																$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
															} else {
																$user_name = '';

															}
															$post_usertype = $UserConsumer->getUserType();
														} else {
															if ($User_postConsumer->getUserType() == '1') {
																$user_id = $User_postConsumer->getId();
																if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
																) {
																	$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
																} else {
																	$user_name = '';
																}
																$post_usertype = $User_postConsumer->getUserType();
															} else {
																$user_id = '';
																$user_name = '';
																$post_usertype = $user_type;
															}
														}

													}


												}


											}
										}


										/* 
                                if ($UserConsumer != '' && $UserConsumer != null) {
                                    $user_id = $UserConsumer->getId();
                                    if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                        $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                    } else {
                                        $user_name = '';
                                    }
                                } else {
                                    $user_name = '';
                                }
                                */


										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findOneBy(['postID' => $album_id]);
										// echo '<pre>';print_r($UserRating);
										if ($UserRating != '' && $UserRating != null) {

											if ($UserRating->getUserRating() != '') {
												$rating1 = $UserRating->getUserRating();
											} else {
												$rating1 = '';
											}
											if ($UserRating->getUserRating() != '') {
												$rating[] = $UserRating->getUserRating();
											} else {
												$rating[] = '';
											}
										} else {
											$rating1 = '';
											$rating = [];

										}

										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											'$user_type' => $post_usertype,
											'album_image' => ($album_image),
											'user_name' => ($user_name),
											'rates' => ($rating1)
										);
									}
								} else {

									$rating = [];
									$album_detail = [];
								}
								// $UserReviews = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(['toUserID' => $sp_user_id]);
								//echo '<pre>';print_r(count($UserReviews));
								$UserReviews = $conn->query(
									"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $resultVal['toUserID'] . " OR fromUserID = " . $resultVal['toUserID'] . ""
								)->fetchAll();
								if ($UserReviews != '') {
									foreach ($UserReviews as $UserReviewsval) {

										// echo '<pre>';print_r($reviews);
										if ($UserReviewsval['rate'] != null) {
											//want full rate not rounded
											$rateavg = round($UserReviewsval['rate'],1);
										} else {
											$rateavg = '';
										}

										$reviews = $UserReviewsval['reviews'];
										unset($rating);
									}
								} else {
									$rateavg = '';
									$reviews = '';
									$ratvalues = '';
								}


								if (count($reviews) > 0 && $reviews != null) {
									$countReview = $reviews;
								} else {
									$countReview = 0;
								}
								if (count($rateavg) > 0 && $rateavg != null) {
									$countRates = $rateavg;
								} else {
									$countRates = 0;
								}

								$sp_detail[] = array(
									'user_name' => ($userName),
									'user_type' => $user_type,
									'sp_user_id' => ($sp_user_id),
									'contact' => ($userContact),
									'company_name' => ($companyName),
									'user_address' => ($userAddres),
									'profile_image' => ($profileImage)
								,
									'service_name' => ($service_name),
									'service_price' => ($serviceprice),
									'follow_status' => $follow_status,
									'user_chat' => 0,
									'total_reviews' => ($countReview),
									'total_rate' => ($countRates),
									'albums' => $album_detail
								);
								unset($album_detail);
							}
						}

					}

					echo json_encode(
						array(
							'success' => 1,
							'message' => 'successfull',
							'trending' => $sp_detail,
							'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
						)
					);

				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {
				if ($result != '' && $result != null) {

					foreach ($result as $resultVal) {
						// if($resultVal['toUserID'] !=  $sp_id){

						$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $resultVal['toUserID']]
						);

						if ($User != '' && $User != null) {

							$user_type = $User->getUserType();
							$sp_user_id = $User->getId();

							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {

									if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
									) {

										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}

							if ($User->getUserFirstName() || $User->getUserLastName() != '') {
								$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
							} else {
								$userName = '';
							}
							if ($User->getCompanyName() != '') {
								$companyName = $User->getCompanyName();
							} else {
								$companyName = '';
							}
							if ($User->getUserAddress() != '') {
								$userAddres = $User->getUserAddress();
							} else {
								$userAddres = '';
							}
							if ($User->getUserMobileNo() != '') {
								$userContact = $User->getUserMobileNo();
							} else {
								$userContact = '';
							}
							if ($User->getUserProfileImage() != '' && $User->getUserProfileImage() > 0) {
								$profileImage = $this->baseurl() . $User->getUserProfileImage();
							} else {
								$profileImage = $this->baseurl() . 'defaultprofile.png';
							}
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}

							$userrate = $conn->query(
								"select user_rating.userRating,post.postID as post_id from user_rating left join post on user_rating.postID = post.postID where   post.postStatus = '0' and  user_rating.toUserID =" . $resultVal['toUserID'] . " OR post.postStatus = '0' and user_rating.fromUserID = " . $resultVal['toUserID'] . "   order by post.postID desc limit 0,6"
							)->fetchAll();


							if ($userrate != '' && $userrate != null) {
								foreach ($userrate as $customers) {
									$customersValues = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customers['post_id']]);

									$album_id = $customersValues->getId();
									if ($customersValues->getPostCaption() != '') {
										$post_caption = $customersValues->getPostCaption();
									} else {
										$post_caption = '';
									}

									if ($customersValues->getUserTagID() != '') {
										$tagedUser = $customersValues->getUserTagID();
									} else {
										$tagedUser = '';
									}
									if ($customersValues->getPostImageFront() != '' && $customersValues->getPostImageFront() != null
									) {
										$album_image = $this->baseurl() . $customersValues->getPostImageFront();
									} elseif ($customersValues->getPostImageFrontLeft() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageFrontLeft();
									} elseif ($customersValues->getPostImageLeft() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageLeft();
									} elseif ($customersValues->getPostImageBackLeft() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageBackLeft();
									} elseif ($customersValues->getPostImageBack() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageBack();
									} elseif ($customersValues->getPostImageBackRight() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageBackRight();
									} elseif ($customersValues->getPostImageRight() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageRight();
									} elseif ($customersValues->getPostImageFrontRight() != '') {
										$album_image = $this->baseurl() . $customersValues->getPostImageFrontRight();
									} else {
										$album_image = '';
									}
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $album_id]
									);
									if ($PostTags != '') {
										$tag_status = '0';
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											}
										}
									} else {
										$tag_status = '0';
									}


									$UserConsumer = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:User"
									)->findOneBy(['id' => $tagedUser]);

									if ($UserConsumer != '' && $UserConsumer != null) {
										$user_id = $UserConsumer->getId();
										if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
										) {
											$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
										} else {
											$user_name = '';
										}
									} else {
										$user_name = '';
									}
									$UserRating = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:UserRating"
									)->findOneBy(['postID' => $album_id]);
									// echo '<pre>';print_r($UserRating);
									if ($UserRating != '' && $UserRating != null) {

										if ($UserRating->getUserRating() != '') {
											$rating1 = $UserRating->getUserRating();
										} else {
											$rating1 = '';
										}
										if ($UserRating->getUserRating() != '') {
											$rating[] = $UserRating->getUserRating();
										} else {
											$rating[] = '';
										}
									} else {
										$rating1 = '';
										$rating = [];

									}

									$album_detail[] = array(
										'album_id' => ($album_id),
										'tag_status' => $tag_status,
										'album_service' => ($post_caption),
										'user_id' => ($user_id),
										'album_image' => ($album_image),
										'user_name' => ($user_name),
										'rates' => ($rating1)
									);
								}
							} else {

								$rating = [];
								$album_detail = [];
							}

							$UserReviews = $conn->query(
								"select *,avg(userRating) as rate,count(userReviews) as reviews from user_rating where toUserID =" . $resultVal['toUserID'] . " OR fromUserID = " . $resultVal['toUserID'] . ""
							)->fetchAll();
							if ($UserReviews != '') {
								foreach ($UserReviews as $UserReviewsval) {

									// echo '<pre>';print_r($reviews);
									if ($UserReviewsval['rate'] != null) {
										//want full rate not rounded
										$rateavg = round($UserReviewsval['rate'],1);
									} else {
										$rateavg = '';
									}
									$reviews = $UserReviewsval['reviews'];
									unset($rating);
								}
							} else {
								$rateavg = '';
								$reviews = '';
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = $reviews;
							} else {
								$countReview = 0;
							}
							if (count($rateavg) > 0 && $rateavg != null) {
								$countRates = $rateavg;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = array(
								'user_name' => ($userName),
								'user_type' => $user_type,
								'sp_user_id' => ($sp_user_id),
								'contact' => ($userContact),
								'company_name' => ($companyName),
								'user_address' => ($userAddres),
								'profile_image' => ($profileImage)
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'follow_status' => $follow_status,
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
						}
					}


					echo json_encode(
						array(
							'success' => 1,
							'message' => 'successfull',
							'my_services' => $sp_detail,
							'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
						)
					);

				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		}

	}

	/*     * ************************************************************************LOCAL END ************************ */


	/**
	 * @Route("/follower", name="_follower")
	 * @Template()
	 */

	/*     * **********************************************************************************FOLLOWER END ******************************** */

	public function followerAction(Request $user_id, Request $following_type, Request $counter)
	{
		$request = $this->getRequest();
		$sp_id = $request->get('user_id');
		$limitset = 4;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$FollowerModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
			['userID' => $sp_id, 'followStatus' => '1']
		);
		//   	echo '<pre>';print_r($FollowerModel);die;
		if ($FollowerModel != '' && $FollowerModel != null) {

			foreach ($FollowerModel as $FollowerModelVal) {
				$spID[] = $FollowerModelVal->getToUserID();
			}

			for ($i = 0; $i < count($spID); $i++) {
				if (($spID[$i] != $sp_id)) {
					//$sp= $spID;
					$spIDs[] = $spID[$i];
				}
			}
			//Start of USER DETAIL
			$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
				['id' => $spIDs],
				array('id' => 'desc'),
				$max_num,
				$min_num
			);
			if ($Users != '' && $Users != null) {
				$flag = '';
				foreach ($Users as $UsersValues) {
					//start of usertype 1  
					if ($UsersValues->getuserType() == '1') {
						$flag = '1';
						$sp_user_id = $UsersValues->getId();

						$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
							['followStatus' => '1']
						);
						if ($UserFollow != '') {
							$follow_status = '0';
							foreach ($UserFollow as $UserFollowVal) {
								if (($UserFollowVal->getToUserID() == $sp_id) && ($UserFollowVal->getUserID() == $sp_user_id)
								) {
									$follow_status = '1';
								}
							}
						} else {
							$follow_status = '0';
						}
						if ($UsersValues->getUserFirstName() || $UsersValues->getUserLastName() != '') {
							$userName = $UsersValues->getUserFirstName() . ' ' . $UsersValues->getUserLastName();
						} else {
							$userName = '';
						}
						$user_type = $UsersValues->getUserType();
						if ($UsersValues->getCompanyName() != '') {
							$companyName = $UsersValues->getCompanyName();
						} else {
							$companyName = '';
						}
						if ($UsersValues->getUserAddress() != '') {
							$userAddres = $UsersValues->getUserAddress();
						} else {
							$userAddres = '';
						}
						if ($UsersValues->getUserMobileNo() != '') {
							$userContact = $UsersValues->getUserMobileNo();
						} else {
							$userContact = '';
						}
						if ($UsersValues->getUserProfileImage() != '' && $UsersValues->getUserProfileImage() > 0) {
							$profileImage = $this->baseurl() . $UsersValues->getUserProfileImage();
						} else {
							$profileImage = $this->baseurl() . 'defaultprofile.png';
						}

						$serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findOneBy(
							['userID' => $sp_user_id, 'topService' => 1]
						);

						if ($serviceName != '' && $serviceName != null) {
							$serviceId = $serviceName->getServiceID();

							$serviceprice = $serviceName->getServicePrice();
							$masterService = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $serviceId]);
							$service_name = $masterService->getServiceName();
						} else {
							$serviceprice = '';
							$service_name = '';
						}


						$manager = $this->getDoctrine()->getManager();
						$conn = $manager->getConnection();
						$postData = $conn->query(
							"select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . "  order by postID desc limit 0,6"
						)->fetchAll();

						if (!empty($postData)) {
							foreach ($postData as $postDataVal) {

								$customersImage = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:AlbumPost"
								)->findOneBy(['id' => $postDataVal['postID']]);

								if ($customersImage != '' && $customersImage != null) {
//                            foreach ($customersImage as $postModelval) {
									if ($customersImage->getPostImageFront() != '' && $customersImage->getPostImageFront() != null
									) {
										$userprofile = $this->baseurl() . $customersImage->getPostImageFront();
									} elseif ($customersImage->getPostImageFrontLeft() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageFrontLeft();
									} elseif ($customersImage->getPostImageLeft() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageLeft();
									} elseif ($customersImage->getPostImageBackLeft() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageBackLeft();
									} elseif ($customersImage->getPostImageBack() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageBack();
									} elseif ($customersImage->getPostImageBackRight() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageBackRight();
									} elseif ($customersImage->getPostImageRight() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageRight();
									} elseif ($customersImage->getPostImageFrontRight() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageFrontRight();
									} else {
										$userprofile = '';
									}
									$album_id = $customersImage->getId();
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $album_id]
									);
									if ($PostTags != '') {
										$tag_status = '0';
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											}
										}
									} else {
										$tag_status = '0';
									}
									if ($customersImage->getPostCaption() != '') {
										$post_caption = $customersImage->getPostCaption();
									} else {
										$post_caption = '';
									}

									if ($customersImage->getUserTagID() != '') {
										$tagedUser = $customersImage->getUserTagID();
									} else {
										$tagedUser = '';
									}


									// die('ok');
									$user_ID = $customersImage->getUserTagID();
									$user_ID1 = $customersImage->getUserID();
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID, 'userType' => 0]
									);

									if ($Users != '') {
										$user_id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}
									} else {
										$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
											['id' => $user_ID1, 'userType' => 0]
										);
										if ($Users != '' || $Users != null) {
											//echo '<pre>';print_r($Users);die;
											$user_id = $Users->getId();
											if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

												$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
											} else {
												$user_name = '';
											}

										} else {
											$user_id = '';
											$user_name = '';
										}
									}

									$UserRating = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:UserRating"
									)->findBy(['toUserID' => $tagedUser]);
									if ($UserRating != '' && $UserRating != null) {
										foreach ($UserRating as $UserRatingVal) {
											if ($UserRatingVal->getUserRating() != '') {
												$ratings[] = $UserRatingVal->getUserRating();
											} else {
												$ratings[] = '';
											}
											$count = count($ratings);
											$rating = array_sum($ratings) / $count;
											$ratvalues = number_format((float)$rating, 1, '.', '');
										}
									} else {
										$ratvalues = '';
									}
									if (!empty($userprofile)) {
										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											'album_image' => ($userprofile),
											'user_name' => ($user_name),
											'rates' => ($ratvalues)
										);
									}

								} else {
									$album_detail = [];
								}

							}
						} else {
							$album_detail = [];
						}

						$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $sp_user_id]
						);

						if ($UserRating != '' && $UserRating != null) {
							foreach ($UserRating as $UserRatingVal) {
								if ($UserRatingVal->getUserRating() != '') {
									$rating1[] = $UserRatingVal->getUserRating();
								} else {
									$rating1[] = '';
								}
								if ($UserRatingVal->getUserReviews() != '') {
									$reviews[] = $UserRatingVal->getUserReviews();
								} else {
									$reviews[] = '';
								}
								$count = count($rating1);
								$rating = array_sum($rating1) / $count;
								$ratvalues = number_format((float)$rating, 1, '.', '');
							}
						} else {
							$ratvalues = '';
							$reviews = '';
						}
						if (count($reviews) > 0 && $reviews != null) {
							$countReview = count($reviews);
						} else {
							$countReview = 0;
						}
						if (count($ratvalues) > 0 && $ratvalues != null) {
							$countRates = $ratvalues;
						} else {
							$countRates = 0;
						}

						$sp_detail[] = ([
							'user_name' => ($userName),
							'user_type' => $user_type,
							'follow_status' => $follow_status,
							'sp_user_id' => ($sp_user_id),
							'contact' => ($userContact),
							'company_name' => ($companyName),
							'user_address' => ($userAddres),
							'profile_image' => ($profileImage)

							,
							'service_name' => ($service_name),
							'service_price' => ($serviceprice),
							'user_chat' => 0,
							'total_reviews' => ($countReview),
							'total_rate' => ($countRates),
							'albums' => $album_detail
						]);
						unset($album_detail);
						//     echo '<pre>';print_r($sp_detail);


					} else {
						if ($UsersValues->getuserType() == '0') {
							$flag = '1';
							$sp_user_id = $UsersValues->getId();
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);
							//   echo '<pre>';print_r($UserFollow);
							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									if (($UserFollowVal->getToUserID() == $sp_id) && ($UserFollowVal->getUserID() == $sp_user_id)
									) {
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							if ($UsersValues->getUserFirstName() || $UsersValues->getUserLastName() != '') {
								$userName = $UsersValues->getUserFirstName() . ' ' . $UsersValues->getUserLastName();
							} else {
								$userName = '';
							}
							if ($UsersValues->getCompanyName() != '') {
								$companyName = $UsersValues->getCompanyName();
							} else {
								$companyName = '';
							}
							if ($UsersValues->getUserAddress() != '') {
								$userAddres = $UsersValues->getUserAddress();
							} else {
								$userAddres = '';
							}
							$user_type = $UsersValues->getUserType();
							if ($UsersValues->getUserMobileNo() != '') {
								$userContact = $UsersValues->getUserMobileNo();
							} else {
								$userContact = '';
							}
							if ($UsersValues->getUserProfileImage() != '' && $UsersValues->getUserProfileImage() > 0) {
								$profileImage = $this->baseurl() . $UsersValues->getUserProfileImage();
							} else {
								$profileImage = $this->baseurl() . 'defaultprofile.png';
							}
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}


							$manager = $this->getDoctrine()->getManager();
							$conn = $manager->getConnection();

							$postData = $conn->query(
								"select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . "  order by postID desc limit 0,6"
							)->fetchAll();
							if (!empty($postData)) {
								foreach ($postData as $postDataVal) {
									$customersImage = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $postDataVal['postID']]);

									if ($customersImage != '' && $customersImage != null) {
//                            foreach ($customersImage as $postModelval) {
										if ($customersImage->getPostImageFront() != '' && $customersImage->getPostImageFront() != null
										) {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFront();
										} elseif ($customersImage->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFrontLeft();
										} elseif ($customersImage->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageLeft();
										} elseif ($customersImage->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBackLeft();
										} elseif ($customersImage->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBack();
										} elseif ($customersImage->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBackRight();
										} elseif ($customersImage->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageRight();
										} elseif ($customersImage->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}
										$album_id = $customersImage->getId();
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $album_id]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										if ($customersImage->getPostCaption() != '') {
											$post_caption = $customersImage->getPostCaption();
										} else {
											$post_caption = '';
										}

										if ($customersImage->getUserTagID() != '') {
											$tagedUser = $customersImage->getUserTagID();
										} else {
											$tagedUser = '';
										}


										// die('ok');
										$user_ID = $customersImage->getUserTagID();
										$user_ID1 = $customersImage->getUserID();
										$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
											['id' => $user_ID, 'userType' => 0]
										);

										if ($Users != '') {
											$user_id = $Users->getId();
											if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

												$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$Users = $this->getDoctrine()->getRepository(
												"AcmeDemoBundle:User"
											)->findOneBy(['id' => $user_ID1, 'userType' => 0]);
											if ($Users != '' || $Users != null) {
												//echo '<pre>';print_r($Users);die;
												$user_id = $Users->getId();
												if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {


													$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
												} else {
													$user_name = '';
												}

											} else {
												$user_id = '';
												$user_name = '';
											}
										}

										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findBy(['toUserID' => $tagedUser]);
										if ($UserRating != '' && $UserRating != null) {
											foreach ($UserRating as $UserRatingVal) {
												if ($UserRatingVal->getUserRating() != '') {
													$ratings[] = $UserRatingVal->getUserRating();
												} else {
													$ratings[] = '';
												}
												$count = count($ratings);
												$rating = array_sum($ratings) / $count;
												$ratvalues = number_format((float)$rating, 1, '.', '');
											}
										} else {
											$ratvalues = '';
										}
										if (!empty($userprofile)) {
											$album_detail[] = array(
												'album_id' => ($album_id),
												'tag_status' => $tag_status,
												'album_service' => ($post_caption),
												'user_id' => ($user_id),
												'album_image' => ($userprofile),
												'user_name' => ($user_name),
												'rates' => ($ratvalues)
											);
										}

									} else {
										$album_detail = [];
									}

								}
							} else {
								$album_detail = [];
							}

							$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
								['toUserID' => $sp_user_id]
							);

							if ($UserRating != '' && $UserRating != null) {
								foreach ($UserRating as $UserRatingVal) {
									if ($UserRatingVal->getUserRating() != '') {
										$rating1[] = $UserRatingVal->getUserRating();
									} else {
										$rating1[] = '';
									}
									if ($UserRatingVal->getUserReviews() != '') {
										$reviews[] = $UserRatingVal->getUserReviews();
									} else {
										$reviews[] = '';
									}
									$count = count($rating1);
									$rating = array_sum($rating1) / $count;
									$ratvalues = number_format((float)$rating, 1, '.', '');
								}
							} else {
								$ratvalues = '';
								$reviews = '';
							}
							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = ([
								'user_name' => ($userName),
								'user_type' => $user_type,
								'follow_status' => $follow_status,
								'sp_user_id' => ($sp_user_id),
								'contact' => ($userContact),
								'company_name' => ($companyName),
								'user_address' => ($userAddres),
								'profile_image' => ($profileImage)
								,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							]);
							unset($album_detail);
							// echo '<pre>';print_r($sp_detail);
						}
					}//End of usertype
				}
				if ($flag == '1') {
					echo json_encode(array('success' => 1, 'message' => 'successfull', 'follower' => $sp_detail));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}


			} else {//end of USER DETAIL
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}


		}


	}



	/**
	 * @Route("/following", name="_following")
	 * @Template()
	 */
	/*     * **********************************************************************************FOLLOWING END ******************************** */
	public function followingAction(Request $user_id, Request $following_type, Request $counter)
	{
		$request = $this->getRequest();
		$sp_id = $request->get('user_id');
		$limitset = 4;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		//$FollowModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
		//	['toUserID' => $sp_id, 'followStatus' => '1']
		$FollowModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
			['userID' => $sp_id, 'followStatus' => '1']
		);
		//echo '*******HERE:*************************';
		//print_r($FollowModel);die;
		if ($FollowModel != '' && $FollowModel != null) {

			foreach ($FollowModel as $FollowModelVal) {

				$spID[] = $FollowModelVal->getToUserID();
				//$spID[] = $FollowModelVal['toUserID']->getUserID();
			}

			for ($i = 0; $i < count($spID); $i++) {
				if (($spID[$i] != $sp_id)) {
					//$sp= $spID;
					$spIDs[] = $spID[$i];
				}
			}
			$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
				['id' => $spIDs],
				array('id' => 'desc'),
				$max_num,
				$min_num
			);
//            echo '<pre>';
//            print_r($Users);
//            die;
			if ($Users != '' && $Users != null) {
				$flag = '';
				foreach ($Users as $UsersValues) {
					if ($UsersValues->getuserType() == '1') {
						$flag = '1';
						$sp_user_id = $UsersValues->getId();
						$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
							['followStatus' => '1']
						);
						//echo '<pre>';print_r($UserFollow);
						if ($UserFollow != '') {
							$follow_status = '0';
							foreach ($UserFollow as $UserFollowVal) {
								if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
								) {
									$follow_status = '1';
								}
							}
						} else {
							$follow_status = '0';
						}
						if ($UsersValues->getUserFirstName() || $UsersValues->getUserLastName() != '') {
							$userName = $UsersValues->getUserFirstName() . ' ' . $UsersValues->getUserLastName();
						} else {
							$userName = '';
						}
						$user_type = $UsersValues->getUserType();
						if ($UsersValues->getCompanyName() != '') {
							$companyName = $UsersValues->getCompanyName();
						} else {
							$companyName = '';
						}
						if ($UsersValues->getUserAddress() != '') {
							$userAddres = $UsersValues->getUserAddress();
						} else {
							$userAddres = '';
						}
						if ($UsersValues->getUserMobileNo() != '') {
							$userContact = $UsersValues->getUserMobileNo();
						} else {
							$userContact = '';
						}
						if ($UsersValues->getUserProfileImage() != '' && $UsersValues->getUserProfileImage() > 0) {
							$profileImage = $this->baseurl() . $UsersValues->getUserProfileImage();
						} else {
							$profileImage = $this->baseurl() . 'defaultprofile.png';
						}
						$serviceName = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserServices")->findOneBy(
							['userID' => $sp_user_id, 'topService' => 1]
						);

						if ($serviceName != '' && $serviceName != null) {
							$serviceId = $serviceName->getServiceID();

							$serviceprice = $serviceName->getServicePrice();
							$masterService = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:MasterServices"
							)->findOneBy(['id' => $serviceId]);
							$service_name = $masterService->getServiceName();
						} else {
							$serviceprice = '';
							$service_name = '';
						}
//                        $customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $sp_user_id]);
//
//                        if ($customers != '' && $customers != null) {
//                            foreach ($customers as $postModelval) {
//
//
//
//                                $spID[] = $postModelval->getUserTagID();
//                            }
//                        } else {
//                            $spID = [];
//                        }
//                        for ($i = 0; $i < count($spID); $i++) {
//                            if (($spID[$i] != $sp_id)) {
//                                //$sp= $spID;
//                                $spIDs[] = $spID[$i];
//                            }
//                        }
						$manager = $this->getDoctrine()->getManager();
						$conn = $manager->getConnection();

						$postData = $conn->query(
							"select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . "  order by postID desc limit 0,6"
						)->fetchAll();
						if (!empty($postData)) {
							foreach ($postData as $postDataVal) {
								$customersImage = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:AlbumPost"
								)->findOneBy(['id' => $postDataVal['postID']]);

								if ($customersImage != '' && $customersImage != null) {
//                            foreach ($customersImage as $postModelval) {
									if ($customersImage->getPostImageFront() != '' && $customersImage->getPostImageFront() != null
									) {
										$userprofile = $this->baseurl() . $customersImage->getPostImageFront();
									} elseif ($customersImage->getPostImageFrontLeft() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageFrontLeft();
									} elseif ($customersImage->getPostImageLeft() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageLeft();
									} elseif ($customersImage->getPostImageBackLeft() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageBackLeft();
									} elseif ($customersImage->getPostImageBack() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageBack();
									} elseif ($customersImage->getPostImageBackRight() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageBackRight();
									} elseif ($customersImage->getPostImageRight() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageRight();
									} elseif ($customersImage->getPostImageFrontRight() != '') {
										$userprofile = $this->baseurl() . $customersImage->getPostImageFrontRight();
									} else {
										$userprofile = '';
									}
									$album_id = $customersImage->getId();
									$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
										['postID' => $album_id]
									);
									if ($PostTags != '') {
										$tag_status = '0';
										foreach ($PostTags as $PostTagsVal) {
											if ($PostTagsVal->getTags() != '') {
												$tag_status = '1';
											}
										}
									} else {
										$tag_status = '0';
									}
									if ($customersImage->getPostCaption() != '') {
										$post_caption = $customersImage->getPostCaption();
									} else {
										$post_caption = '';
									}

									if ($customersImage->getUserTagID() != '') {
										$tagedUser = $customersImage->getUserTagID();
									} else {
										$tagedUser = '';
									}


									// die('ok');
									$user_ID = $customersImage->getUserTagID();
									$user_ID1 = $customersImage->getUserID();
									$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
										['id' => $user_ID, 'userType' => 0]
									);

									if ($Users != '') {
										$user_id = $Users->getId();
										if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

											$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
										} else {
											$user_name = '';
										}
									} else {
										$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
											['id' => $user_ID1, 'userType' => 0]
										);
										if ($Users != '' || $Users != null) {
											//echo '<pre>';print_r($Users);die;
											$user_id = $Users->getId();
											if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

												$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
											} else {
												$user_name = '';
											}

										} else {
											$user_id = '';
											$user_name = '';
										}
									}

									$UserRating = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:UserRating"
									)->findBy(['toUserID' => $tagedUser]);
									if ($UserRating != '' && $UserRating != null) {
										foreach ($UserRating as $UserRatingVal) {
											if ($UserRatingVal->getUserRating() != '') {
												$ratings[] = $UserRatingVal->getUserRating();
											} else {
												$ratings[] = '';
											}
											$count = count($ratings);
											$rating = array_sum($ratings) / $count;
											$ratvalues = number_format((float)$rating, 1, '.', '');
										}
									} else {
										$ratvalues = '';
									}
									if (!empty($userprofile)) {
										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											'album_image' => ($userprofile),
											'user_name' => ($user_name),
											'rates' => ($ratvalues)
										);
									}

								} else {
									$album_detail = [];
								}

							}
						} else {
							$album_detail = [];
						}

						$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
							['toUserID' => $sp_user_id]
						);

						if ($UserRating != '' && $UserRating != null) {
							foreach ($UserRating as $UserRatingVal) {
								if ($UserRatingVal->getUserRating() != '') {
									$rating1[] = $UserRatingVal->getUserRating();
								} else {
									$rating1[] = '';
								}
								if ($UserRatingVal->getUserReviews() != '') {
									$reviews[] = $UserRatingVal->getUserReviews();
								} else {
									$reviews[] = '';
								}
								$count = count($rating1);
								$rating = array_sum($rating1) / $count;
								$ratvalues = number_format((float)$rating, 1, '.', '');
							}
						} else {
							$ratvalues = '';
							$reviews = '';
						}
						if (count($reviews) > 0 && $reviews != null) {
							$countReview = count($reviews);
						} else {
							$countReview = 0;
						}
						if (count($ratvalues) > 0 && $ratvalues != null) {
							$countRates = $ratvalues;
						} else {
							$countRates = 0;
						}

						$sp_detail[] = ([
							'user_name' => ($userName),
							'user_type' => $user_type,
							'follow_status' => $follow_status,
							'sp_user_id' => ($sp_user_id),
							'contact' => ($userContact),
							'company_name' => ($companyName),
							'user_address' => ($userAddres),
							'profile_image' => ($profileImage)
							,
							'service_name' => ($service_name),
							'service_price' => ($serviceprice),
							'user_chat' => 0,
							'total_reviews' => ($countReview),
							'total_rate' => ($countRates),
							'albums' => $album_detail
						]);
						unset($album_detail);
						// echo '<pre>';print_r($sp_detail);
					} else {
						if ($UsersValues->getuserType() == '0') {
							$flag = '1';
							$sp_user_id = $UsersValues->getId();
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);
							//echo '<pre>';print_r($UserFollow);
							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
									) {
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							if ($UsersValues->getUserFirstName() || $UsersValues->getUserLastName() != '') {
								$userName = $UsersValues->getUserFirstName() . ' ' . $UsersValues->getUserLastName();
							} else {
								$userName = '';
							}
							if ($UsersValues->getCompanyName() != '') {
								$companyName = $UsersValues->getCompanyName();
							} else {
								$companyName = '';
							}
							if ($UsersValues->getUserAddress() != '') {
								$userAddres = $UsersValues->getUserAddress();
							} else {
								$userAddres = '';
							}
							$user_type = $UsersValues->getUserType();
							if ($UsersValues->getUserMobileNo() != '') {
								$userContact = $UsersValues->getUserMobileNo();
							} else {
								$userContact = '';
							}
							if ($UsersValues->getUserProfileImage() != '' && $UsersValues->getUserProfileImage() > 0) {
								$profileImage = $this->baseurl() . $UsersValues->getUserProfileImage();
							} else {
								$profileImage = $this->baseurl() . 'defaultprofile.png';
							}
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}
//                        $customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $sp_user_id]);
//
//                        if ($customers != '' && $customers != null) {
//                            foreach ($customers as $postModelval) {
//
//
//
//                                $spID[] = $postModelval->getUserTagID();
//                            }
//                        } else {
//                            $spID = [];
//                        }
//                        for ($i = 0; $i < count($spID); $i++) {
//                            if (($spID[$i] != $sp_id)) {
//                                //$sp= $spID;
//                                $spIDs[] = $spID[$i];
//                            }
//                        }
							$manager = $this->getDoctrine()->getManager();
							$conn = $manager->getConnection();

							$postData = $conn->query(
								"select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . "  order by postID desc limit 0,6"
							)->fetchAll();
							if (!empty($postData)) {
								foreach ($postData as $postDataVal) {
									$customersImage = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $postDataVal['postID']]);

									if ($customersImage != '' && $customersImage != null) {
//                            foreach ($customersImage as $postModelval) {
										if ($customersImage->getPostImageFront() != '' && $customersImage->getPostImageFront() != null
										) {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFront();
										} elseif ($customersImage->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFrontLeft();
										} elseif ($customersImage->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageLeft();
										} elseif ($customersImage->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBackLeft();
										} elseif ($customersImage->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBack();
										} elseif ($customersImage->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBackRight();
										} elseif ($customersImage->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageRight();
										} elseif ($customersImage->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}
										$album_id = $customersImage->getId();
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $album_id]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										if ($customersImage->getPostCaption() != '') {
											$post_caption = $customersImage->getPostCaption();
										} else {
											$post_caption = '';
										}

										if ($customersImage->getUserTagID() != '') {
											$tagedUser = $customersImage->getUserTagID();
										} else {
											$tagedUser = '';
										}


										// die('ok');
										$user_ID = $customersImage->getUserTagID();
										$user_ID1 = $customersImage->getUserID();
										$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
											['id' => $user_ID, 'userType' => 0]
										);

										if ($Users != '') {
											$user_id = $Users->getId();
											if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

												$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$Users = $this->getDoctrine()->getRepository(
												"AcmeDemoBundle:User"
											)->findOneBy(['id' => $user_ID1, 'userType' => 0]);
											if ($Users != '' || $Users != null) {
												//echo '<pre>';print_r($Users);die;
												$user_id = $Users->getId();
												if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

													$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
												} else {
													$user_name = '';
												}

											} else {
												$user_id = '';
												$user_name = '';
											}
										}

										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findBy(['toUserID' => $tagedUser]);
										if ($UserRating != '' && $UserRating != null) {
											foreach ($UserRating as $UserRatingVal) {
												if ($UserRatingVal->getUserRating() != '') {
													$ratings[] = $UserRatingVal->getUserRating();
												} else {
													$ratings[] = '';
												}
												$count = count($ratings);
												$rating = array_sum($ratings) / $count;
												$ratvalues = number_format((float)$rating, 1, '.', '');
											}
										} else {
											$ratvalues = '';
										}
										if (!empty($userprofile)) {
											$album_detail[] = array(
												'album_id' => ($album_id),
												'tag_status' => $tag_status,
												'album_service' => ($post_caption),
												'user_id' => ($user_id),
												'album_image' => ($userprofile),
												'user_name' => ($user_name),
												'rates' => ($ratvalues)
											);
										}

									} else {
										$album_detail = [];
									}

								}
							} else {
								$album_detail = [];
							}

							$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
								['toUserID' => $sp_user_id]
							);

							if ($UserRating != '' && $UserRating != null) {
								foreach ($UserRating as $UserRatingVal) {
									if ($UserRatingVal->getUserRating() != '') {
										$rating1[] = $UserRatingVal->getUserRating();
									} else {
										$rating1[] = '';
									}
									if ($UserRatingVal->getUserReviews() != '') {
										$reviews[] = $UserRatingVal->getUserReviews();
									} else {
										$reviews[] = '';
									}
									$count = count($rating1);
									$rating = array_sum($rating1) / $count;
									$ratvalues = number_format((float)$rating, 1, '.', '');
								}
							} else {
								$ratvalues = '';
								$reviews = '';
							}
							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = ([
								'user_name' => ($userName),
								'user_type' => $user_type,
								'follow_status' => $follow_status,
								'sp_user_id' => ($sp_user_id),
								'contact' => ($userContact),
								'company_name' => ($companyName),
								'user_address' => ($userAddres),
								'profile_image' => ($profileImage)
								,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							]);
							unset($album_detail);
							// echo '<pre>';print_r($sp_detail);
						}
					}
				}
				if ($flag == '1') {
					echo json_encode(array('success' => 1, 'message' => 'successfull', 'following' => $sp_detail));
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} //end of first follow me if statement null check
		else {
			$FollowModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
				['userID' => $sp_id, 'followStatus' => '1']
			);

			if ($FollowModel != '' && $FollowModel != null) {
				foreach ($FollowModel as $FollowModelVal) {


					$spID[] = $FollowModelVal->getToUserID();
				}

				for ($i = 0; $i < count($spID); $i++) {
					if (($spID[$i] != $sp_id)) {
						//$sp= $spID;
						$spIDs[] = $spID[$i];
					}
				}


				$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $spIDs],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);
//            echo '<pre>';
//            print_r($Users);
//            die;
				if ($Users != '' && $Users != null) {
					$flag = '';
					foreach ($Users as $UsersValues) {
						if ($UsersValues->getuserType() == '1') {
							$flag = '1';
							$sp_user_id = $UsersValues->getId();
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);
							//echo '<pre>';print_r($UserFollow);
							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
									) {
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							if ($UsersValues->getUserFirstName() || $UsersValues->getUserLastName() != '') {
								$userName = $UsersValues->getUserFirstName() . ' ' . $UsersValues->getUserLastName();
							} else {
								$userName = '';
							}
							$user_type = $UsersValues->getUserType();
							if ($UsersValues->getCompanyName() != '') {
								$companyName = $UsersValues->getCompanyName();
							} else {
								$companyName = '';
							}
							if ($UsersValues->getUserAddress() != '') {
								$userAddres = $UsersValues->getUserAddress();
							} else {
								$userAddres = '';
							}
							if ($UsersValues->getUserMobileNo() != '') {
								$userContact = $UsersValues->getUserMobileNo();
							} else {
								$userContact = '';
							}
							if ($UsersValues->getUserProfileImage() != '' && $UsersValues->getUserProfileImage() > 0) {
								$profileImage = $this->baseurl() . $UsersValues->getUserProfileImage();
							} else {
								$profileImage = $this->baseurl() . 'defaultprofile.png';
							}
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}
//                        $customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $sp_user_id]);
//
//                        if ($customers != '' && $customers != null) {
//                            foreach ($customers as $postModelval) {
//
//
//
//                                $spID[] = $postModelval->getUserTagID();
//                            }
//                        } else {
//                            $spID = [];
//                        }
//                        for ($i = 0; $i < count($spID); $i++) {
//                            if (($spID[$i] != $sp_id)) {
//                                //$sp= $spID;
//                                $spIDs[] = $spID[$i];
//                            }
//                        }
							$manager = $this->getDoctrine()->getManager();
							$conn = $manager->getConnection();

							$postData = $conn->query(
								"select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . "  order by postID desc limit 0,6"
							)->fetchAll();
							if (!empty($postData)) {
								foreach ($postData as $postDataVal) {
									$customersImage = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $postDataVal['postID']]);

									if ($customersImage != '' && $customersImage != null) {
//                            foreach ($customersImage as $postModelval) {
										if ($customersImage->getPostImageFront() != '' && $customersImage->getPostImageFront() != null
										) {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFront();
										} elseif ($customersImage->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFrontLeft();
										} elseif ($customersImage->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageLeft();
										} elseif ($customersImage->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBackLeft();
										} elseif ($customersImage->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBack();
										} elseif ($customersImage->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageBackRight();
										} elseif ($customersImage->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageRight();
										} elseif ($customersImage->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $customersImage->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}
										$album_id = $customersImage->getId();
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $album_id]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										if ($customersImage->getPostCaption() != '') {
											$post_caption = $customersImage->getPostCaption();
										} else {
											$post_caption = '';
										}

										if ($customersImage->getUserTagID() != '') {
											$tagedUser = $customersImage->getUserTagID();
										} else {
											$tagedUser = '';
										}


										// die('ok');
										$user_ID = $customersImage->getUserTagID();
										$user_ID1 = $customersImage->getUserID();
										$Users = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
											['id' => $user_ID, 'userType' => 0]
										);

										if ($Users != '') {
											$user_id = $Users->getId();
											if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

												$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
											} else {
												$user_name = '';
											}
										} else {
											$Users = $this->getDoctrine()->getRepository(
												"AcmeDemoBundle:User"
											)->findOneBy(['id' => $user_ID1, 'userType' => 0]);
											if ($Users != '' || $Users != null) {
												//echo '<pre>';print_r($Users);die;
												$user_id = $Users->getId();
												if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

													$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
												} else {
													$user_name = '';
												}

											} else {
												$user_id = '';
												$user_name = '';
											}
										}

										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findBy(['toUserID' => $tagedUser]);
										if ($UserRating != '' && $UserRating != null) {
											foreach ($UserRating as $UserRatingVal) {
												if ($UserRatingVal->getUserRating() != '') {
													$ratings[] = $UserRatingVal->getUserRating();
												} else {
													$ratings[] = '';
												}
												$count = count($ratings);
												$rating = array_sum($ratings) / $count;
												$ratvalues = number_format((float)$rating, 1, '.', '');
											}
										} else {
											$ratvalues = '';
										}
										if (!empty($userprofile)) {
											$album_detail[] = array(
												'album_id' => ($album_id),
												'tag_status' => $tag_status,
												'album_service' => ($post_caption),
												'user_id' => ($user_id),
												'album_image' => ($userprofile),
												'user_name' => ($user_name),
												'rates' => ($ratvalues)
											);
										}

									} else {
										$album_detail = [];
									}

								}
							} else {
								$album_detail = [];
							}

							$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
								['toUserID' => $sp_user_id]
							);

							if ($UserRating != '' && $UserRating != null) {
								foreach ($UserRating as $UserRatingVal) {
									if ($UserRatingVal->getUserRating() != '') {
										$rating1[] = $UserRatingVal->getUserRating();
									} else {
										$rating1[] = '';
									}
									if ($UserRatingVal->getUserReviews() != '') {
										$reviews[] = $UserRatingVal->getUserReviews();
									} else {
										$reviews[] = '';
									}
									$count = count($rating1);
									$rating = array_sum($rating1) / $count;
									$ratvalues = number_format((float)$rating, 1, '.', '');
								}
							} else {
								$ratvalues = '';
								$reviews = '';
							}
							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = ([
								'user_name' => ($userName),
								'user_type' => $user_type,
								'follow_status' => $follow_status,
								'sp_user_id' => ($sp_user_id),
								'contact' => ($userContact),
								'company_name' => ($companyName),
								'user_address' => ($userAddres),
								'profile_image' => ($profileImage)
								,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							]);
							unset($album_detail);
							// echo '<pre>';print_r($sp_detail);
						} else {
							if ($UsersValues->getuserType() == '0') {
								$flag = '1';
								$sp_user_id = $UsersValues->getId();
								$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
									['followStatus' => '1']
								);
								//echo '<pre>';print_r($UserFollow);
								if ($UserFollow != '') {
									$follow_status = '0';
									foreach ($UserFollow as $UserFollowVal) {
										if (($UserFollowVal->getUserID() == $sp_id) && ($UserFollowVal->getToUserID() == $sp_user_id)
										) {
											$follow_status = '1';
										}
									}
								} else {
									$follow_status = '0';
								}
								if ($UsersValues->getUserFirstName() || $UsersValues->getUserLastName() != '') {
									$userName = $UsersValues->getUserFirstName() . ' ' . $UsersValues->getUserLastName();
								} else {
									$userName = '';
								}
								if ($UsersValues->getCompanyName() != '') {
									$companyName = $UsersValues->getCompanyName();
								} else {
									$companyName = '';
								}
								if ($UsersValues->getUserAddress() != '') {
									$userAddres = $UsersValues->getUserAddress();
								} else {
									$userAddres = '';
								}
								$user_type = $UsersValues->getUserType();
								if ($UsersValues->getUserMobileNo() != '') {
									$userContact = $UsersValues->getUserMobileNo();
								} else {
									$userContact = '';
								}
								if ($UsersValues->getUserProfileImage() != '' && $UsersValues->getUserProfileImage() > 0
								) {
									$profileImage = $this->baseurl() . $UsersValues->getUserProfileImage();
								} else {
									$profileImage = $this->baseurl() . 'defaultprofile.png';
								}
								$serviceName = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:UserServices"
								)->findOneBy(['userID' => $sp_user_id, 'topService' => 1]);

								if ($serviceName != '' && $serviceName != null) {
									$serviceId = $serviceName->getServiceID();

									$serviceprice = $serviceName->getServicePrice();
									$masterService = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:MasterServices"
									)->findOneBy(['id' => $serviceId]);
									$service_name = $masterService->getServiceName();
								} else {
									$serviceprice = '';
									$service_name = '';
								}
//                        $customers = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['userID' => $sp_user_id]);
//
//                        if ($customers != '' && $customers != null) {
//                            foreach ($customers as $postModelval) {
//
//
//
//                                $spID[] = $postModelval->getUserTagID();
//                            }
//                        } else {
//                            $spID = [];
//                        }
//                        for ($i = 0; $i < count($spID); $i++) {
//                            if (($spID[$i] != $sp_id)) {
//                                //$sp= $spID;
//                                $spIDs[] = $spID[$i];
//                            }
//                        }
								$manager = $this->getDoctrine()->getManager();
								$conn = $manager->getConnection();

								$postData = $conn->query(
									"select * from post where userID=" . $sp_user_id . " || userTagID=" . $sp_user_id . "  order by postID desc limit 0,6"
								)->fetchAll();
								if (!empty($postData)) {
									foreach ($postData as $postDataVal) {
										$customersImage = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:AlbumPost"
										)->findOneBy(['id' => $postDataVal['postID']]);

										if ($customersImage != '' && $customersImage != null) {
//                            foreach ($customersImage as $postModelval) {
											if ($customersImage->getPostImageFront() != '' && $customersImage->getPostImageFront() != null
											) {
												$userprofile = $this->baseurl() . $customersImage->getPostImageFront();
											} elseif ($customersImage->getPostImageFrontLeft() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageFrontLeft();
											} elseif ($customersImage->getPostImageLeft() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageLeft();
											} elseif ($customersImage->getPostImageBackLeft() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageBackLeft();
											} elseif ($customersImage->getPostImageBack() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageBack();
											} elseif ($customersImage->getPostImageBackRight() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageBackRight();
											} elseif ($customersImage->getPostImageRight() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageRight();
											} elseif ($customersImage->getPostImageFrontRight() != '') {
												$userprofile = $this->baseurl() . $customersImage->getPostImageFrontRight();
											} else {
												$userprofile = '';
											}
											$album_id = $customersImage->getId();
											$PostTags = $this->getDoctrine()->getRepository(
												"AcmeDemoBundle:PostTags"
											)->findBy(['postID' => $album_id]);
											if ($PostTags != '') {
												$tag_status = '0';
												foreach ($PostTags as $PostTagsVal) {
													if ($PostTagsVal->getTags() != '') {
														$tag_status = '1';
													}
												}
											} else {
												$tag_status = '0';
											}
											if ($customersImage->getPostCaption() != '') {
												$post_caption = $customersImage->getPostCaption();
											} else {
												$post_caption = '';
											}

											if ($customersImage->getUserTagID() != '') {
												$tagedUser = $customersImage->getUserTagID();
											} else {
												$tagedUser = '';
											}


											// die('ok');
											$user_ID = $customersImage->getUserTagID();
											$user_ID1 = $customersImage->getUserID();
											$Users = $this->getDoctrine()->getRepository(
												"AcmeDemoBundle:User"
											)->findOneBy(['id' => $user_ID, 'userType' => 0]);

											if ($Users != '') {
												$user_id = $Users->getId();
												if (($Users->getUserFirstName() || $Users->getUserLastName()) != '') {

													$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
												} else {
													$user_name = '';
												}
											} else {
												$Users = $this->getDoctrine()->getRepository(
													"AcmeDemoBundle:User"
												)->findOneBy(['id' => $user_ID1, 'userType' => 0]);
												if ($Users != '' || $Users != null) {
													//echo '<pre>';print_r($Users);die;
													$user_id = $Users->getId();
													if (($Users->getUserFirstName() || $Users->getUserLastName()) != ''
													) {

														$user_name = $Users->getUserFirstName() . ' ' . $Users->getUserLastName();
													} else {
														$user_name = '';
													}

												} else {
													$user_id = '';
													$user_name = '';
												}
											}

											$UserRating = $this->getDoctrine()->getRepository(
												"AcmeDemoBundle:UserRating"
											)->findBy(['toUserID' => $tagedUser]);
											if ($UserRating != '' && $UserRating != null) {
												foreach ($UserRating as $UserRatingVal) {
													if ($UserRatingVal->getUserRating() != '') {
														$ratings[] = $UserRatingVal->getUserRating();
													} else {
														$ratings[] = '';
													}
													$count = count($ratings);
													$rating = array_sum($ratings) / $count;
													$ratvalues = number_format((float)$rating, 1, '.', '');
												}
											} else {
												$ratvalues = '';
											}
											if (!empty($userprofile)) {
												$album_detail[] = array(
													'album_id' => ($album_id),
													'tag_status' => $tag_status,
													'album_service' => ($post_caption),
													'user_id' => ($user_id),
													'album_image' => ($userprofile),
													'user_name' => ($user_name),
													'rates' => ($ratvalues)
												);
											}

										} else {
											$album_detail = [];
										}

									}
								} else {
									$album_detail = [];
								}

								$UserRating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
									['toUserID' => $sp_user_id]
								);

								if ($UserRating != '' && $UserRating != null) {
									foreach ($UserRating as $UserRatingVal) {
										if ($UserRatingVal->getUserRating() != '') {
											$rating1[] = $UserRatingVal->getUserRating();
										} else {
											$rating1[] = '';
										}
										if ($UserRatingVal->getUserReviews() != '') {
											$reviews[] = $UserRatingVal->getUserReviews();
										} else {
											$reviews[] = '';
										}
										$count = count($rating1);
										$rating = array_sum($rating1) / $count;
										$ratvalues = number_format((float)$rating, 1, '.', '');
									}
								} else {
									$ratvalues = '';
									$reviews = '';
								}
								if (count($reviews) > 0 && $reviews != null) {
									$countReview = count($reviews);
								} else {
									$countReview = 0;
								}
								if (count($ratvalues) > 0 && $ratvalues != null) {
									$countRates = $ratvalues;
								} else {
									$countRates = 0;
								}

								$sp_detail[] = ([
									'user_name' => ($userName),
									'user_type' => $user_type,
									'follow_status' => $follow_status,
									'sp_user_id' => ($sp_user_id),
									'contact' => ($userContact),
									'company_name' => ($companyName),
									'user_address' => ($userAddres),
									'profile_image' => ($profileImage)
									,
									'service_name' => ($service_name),
									'service_price' => ($serviceprice),
									'user_chat' => 0,
									'total_reviews' => ($countReview),
									'total_rate' => ($countRates),
									'albums' => $album_detail
								]);
								unset($album_detail);
								// echo '<pre>';print_r($sp_detail);
							}
						}
					}
					if ($flag == '1') {
						echo json_encode(array('success' => 1, 'message' => 'successfull', 'following' => $sp_detail));
					} else {
						echo json_encode(array('success' => 0, 'message' => 'failure'));
					}
				} else {
					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}//end of 2nd follow me null check
			else {
				echo json_encode(array('success' => 0, 'message' => 'NoData'));
			}
		}
	}

	//}


	/*     * ************************************************************************FOLLOWING END *************************** */

	/**
	 * @Route("/sploginwithpic", name="_sploginwithpic")
	 * @Template()
	 */
	/*     * ************************************************************************SP login with pic  Begin ******************************************* */
	public function sploginwithpicAction(Request $user_email, Request $user_type, Request $customer_id)
	{

		//user_email(service provider)
		//customer id(userid)
		$request = $this->getRequest();
		$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
			['userEmail' => ($request->get('user_email')), 'userType' => $request->get('user_type')]
		);

		if ($Consumer != '' && $Consumer != null) {
			if ($Consumer->getUserEmail() != '') {
				$userEmail = $Consumer->getUserEmail();
			} else {
				$userEmail = '';
			}
			if ($Consumer->getUserType() != '') {
				$userType = $Consumer->getUserType();
			} else {
				$userType = '';
			}
			$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
				['userID' => $request->get('customer_id'), 'toUserID' => $Consumer->getId()]
			);
			if ($userFOllows == '' && $userFOllows == null) {
				$USERfollow = new UserFollow();
				$USERfollow->setUserID($request->get('customer_id'));
				$USERfollow->setToUserID($Consumer->getId());
				$USERfollow->setFollowStatus('1');
				$em = $this->getDoctrine()->getManager();
				$em->persist($USERfollow);
				$em->flush();
				$USERfollow = new UserFollow();
				$USERfollow->setUserID($Consumer->getId());
				$USERfollow->setToUserID($request->get('customer_id'));
				$USERfollow->setFollowStatus('1');
				$em = $this->getDoctrine()->getManager();
				$em->persist($USERfollow);
				$em->flush();
			}
			echo json_encode(
				array(
					'success' => 1,
					'message' => 'Successfull',
					'user_detail' => array(
						[
							'user_id' => $Consumer->getId(),
							'user_email' => ($userEmail),
							'user_type' => $userType,
							'account_type' => ('old')
						]
					)
				)
			);
		} else {
			$Consumer1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['userEmail' => ($request->get('user_email'))]
			);
			if ($Consumer1 == '') {
				if ($request->get('user_type') == '1') {
					$Newconsumer = new User();
					$Newconsumer->setUserEmail(($request->get('user_email')));
					$Newconsumer->setUserType($request->get('user_type'));
					$Newconsumer->setIsNotification('1');
					$encoder = $this->container->get('my_user.manager')->getEncoder($Newconsumer);
					//$Newconsumer->setUserPassword(mt_rand(999, 9999));
					$password = mt_rand(999, 9999);
					$Newconsumer->setUserPassword($encoder->encodePassword($password, $Newconsumer->getSalt()));
					$em = $this->getDoctrine()->getManager();
					$em->persist($Newconsumer);
					$em->flush();

					$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
						['userID' => $request->get('customer_id'), 'toUserID' => $Newconsumer->getId()]
					);
					if ($userFOllows == '' && $userFOllows == null) {
						$USERfollow = new UserFollow();
						$USERfollow->setUserID($request->get('customer_id'));
						$USERfollow->setToUserID($Newconsumer->getId());
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();
						$USERfollow = new UserFollow();
						$USERfollow->setUserID($Newconsumer->getId());
						$USERfollow->setToUserID($request->get('customer_id'));
						$USERfollow->setFollowStatus('1');
						$em = $this->getDoctrine()->getManager();
						$em->persist($USERfollow);
						$em->flush();
					}
					$email = explode('@', ($Newconsumer->getUserEmail()));
					$userName = ucwords($email[0]);
					$useremail = $Newconsumer->getUserEmail();
					//$password = $Newconsumer->getUserPassword();
					//$userFName = ucwords($user->getUserFirstName());
					$subject = 'Welcome Mail From HereCut Team';
					$body_text = 'SP login with Pic mail from HereCut';
					$body_html = 'Hello ' . $userName . ',<br><br> Welcome to HereCut <br><br> Your email is:' . $useremail . '<br>and password :' . $password . '<br> Use this password to login to the HereCut App. We recommend you change your password once logged into the app by clicking Settings > Change Password<br><br><br>Thanks <br>HereCut Team';
					$from = 'info@herecut.net';
					$fromName = 'HereCut';
					$headers = "From: " . $from . "\r\n";
					$headers .= "Reply-To: " . $from . "\r\n";
					//$headers .= "CC: test@example.com\r\n"; 
					/*$headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/
					$this->smtpEmail($useremail, $subject, $body_html);


					echo json_encode(
						array(
							'success' => 1,
							'message' => 'Successfull',
							'user_detail' => array(
								[
									'user_id' => $Newconsumer->getId(),
									'user_email' => ($Newconsumer->getUserEmail()),
									'user_type' => $Newconsumer->getUserType(),
									'account_type' => ('new')
								]
							)
						)
					);
				} else {
					echo json_encode(array("success" => 0, "message" => "This email_id already registered"));
				}
			} else {
				if ($Consumer1->getUserType() == $request->get('user_type')) {

					echo json_encode(
						array(
							'success' => 1,
							'message' => 'Successfull',
							'user_detail' => array(
								[
									'user_id' => $Consumer1->getId(),
									'user_email' => ($Consumer1->getUserEmail()),
									'user_type' => $Consumer1->getUserType(),
									'account_type' => ('old')
								]
							)
						)
					);
				} else {
					echo json_encode(
						array("success" => 0, "message" => "This email_id already registered as a Consumer")
					);
				}
			}
		}
	}

	/*     * *************************************************************************SP login with pic   End******************************************* */

	/**
	 * @Route("/logout", name="_logout")
	 * @Template()
	 */
	/*     * ************************************************************************LOGOUT  Begin ******************************************* */
	public function logoutAction(Request $user_id, Request $imei)
	{
		$request = $this->getRequest();
		$NotificationMdoel = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findBy(
			['userID' => $request->get('user_id'), 'imei' => ($request->get('imei'))]
		);
//echo '<pre>';print_r($NotificationMdoel);die;
		if ($NotificationMdoel != '' && $NotificationMdoel != null) {
			foreach ($NotificationMdoel as $NotificationMdoelVal) {
				$em = $this->getDoctrine()->getEntityManager();
				$em->remove($NotificationMdoelVal);
				$em->flush();
			}
			echo json_encode(array('success' => 1, 'message' => 'sucessfull'));
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * ************************************************************************LOGOUT  END ******************************************* */

	/**
	 * @Route("/servicenotification", name="_servicenotification")
	 * @Template()
	 */
	/*     * ************************************************************************SERVICE NOTIFICATION  Begin ******************************************* */
	public function servicenotificationAction()
	{
		$request = $this->getRequest();
		/* NOTIFICATION FUNCTION START */

		$postModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findAll();

		foreach ($postModel as $postModelVal) {
			if ($postModelVal->getPostDate() != '') {
				$datetime = $postModelVal->getPostDate();
				$date = date('Y-m-d', strtotime($datetime));
				$datetime1 = date_create($date);
				$datetime2 = date_create(date('Y-m-d'));
				$interval = date_diff($datetime1, $datetime2);
				if ($interval > '30') {
					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $postModelVal->getUserID(), 'userType' => '1']
					);
					$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $postModelVal->getUserTagID(), 'userType' => '0']
					);
					if ($Consumer != '' && $User != '') {
						if (($User->getUserFirstName() || $User->getUserLastName()) != '') {
							$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$userName = '';
						}
						if ($Consumer->getIsNotification() == '1') {
							$msg = ('Service');
							$IDs = $Consumer->getId();
							$submsg = ('Hope you liked the service provided by' . ' ' . $userName);
							$usenotification = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:Notification"
							)->findBy(['userID' => $Consumer->getId()]);
							//echo '<pre>';print_r($usenotification);
							if ($usenotification != '' && $usenotification != null) {
								foreach ($usenotification as $usenotificationVal) {

									$registatoin_ids = ($usenotificationVal->getDeviceID());

									$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
								}
								$notificationMsg = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:NotificationMessage"
								)->findOneBy(
									[
										'userID' => $postModelVal->getUserID(),
										'toUserID' => $postModelVal->getUserTagID(),
										'notificationTitle' => $msg
									]
								);

								if ($notificationMsg == '' && $notificationMsg == null) {

									$notifyMsg = new NotificationMessage();
									$notifyMsg->setNotificationTitle($msg);
									$notifyMsg->setNotificationMessage($submsg);
									$notifyMsg->setUserID($User->getId());
									$notifyMsg->setToUserID($IDs);
									$em = $this->getDoctrine()->getManager();
									$em->persist($notifyMsg);
									$em->flush();
								} else {
									$em = $this->getDoctrine()->getEntityManager();
									$em->remove($notificationMsg);
									$em->flush();
									$notifyMsg = new NotificationMessage();
									$notifyMsg->setNotificationTitle($msg);
									$notifyMsg->setNotificationMessage($submsg);
									$notifyMsg->setUserID($User->getId());
									$notifyMsg->setToUserID($IDs);
									$em = $this->getDoctrine()->getManager();
									$em->persist($notifyMsg);
									$em->flush();
								}

							}
						}
					}
				}
			}
		}


		return array();
		/* NOTIFICATION FUNCTION END */
	}

	/*     * ************************************************************************SERVICE NOTIFICATION   END ******************************************* */

	/**
	 * @Route("/notificationlisting", name="_notificationlisting")
	 * @Template()
	 */
	/*     * ************************************************************************NOTIFICATION LISTING Begin ******************************************* */

	/**************************VIVEK CHANGE*************************************************/
	public function notificationlistingAction(Request $user_id, Request $counter)
	{
		$request = $this->getRequest();
		$limitset = 10;
		$userID = $request->get('user_id');
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();

		$relatedUser = $conn->query(
			"select * from notification_message where userID=" . $userID . " || toUserID=" . $userID . " order by notificationMessageID desc LIMIT " . $min_num . ", " . $max_num . ""

		)->fetchAll();
		// echo '<pre>';print_r($relatedUser);die; 
		$notiIds = '';
		if ($relatedUser != '' || $relatedUser != null) {
			foreach ($relatedUser as $relatedNotiVal) {
				$notiIds[] = $relatedNotiVal['notificationMessageID'];
			}
		} else {
			$notiIds = '';
		}
		// echo '<pre>';print_r($notiIds);die; 
		$NotificationList = $this->getDoctrine()->getRepository("AcmeDemoBundle:NotificationMessage")->findBy(
			['id' => $notiIds],
			array('id' => 'desc'),
			$max_num,
			$min_num
		);
		//   echo '<pre>';print_r($NotificationList);die;
		if (!empty($NotificationList)) {
			foreach ($NotificationList as $NotificationMdoelVal) {

				if ($NotificationMdoelVal->getNotificationTitle() == 'Rate') {
					if ($NotificationMdoelVal->getUserID() == $userID) {
						$user_id_display = $NotificationMdoelVal->getToUserID();

						$user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $user_id_display]
						);
						if ($user1 != '' && $user1 != null) {
							if (($user1->getUserFirstName() || $user1->getUserLastName()) != '') {
								$userName1 = $user1->getUserFirstName() . ' ' . $user1->getUserLastName();
							} else {
								$userName1 = '';
							}
						}

						$msg = $NotificationMdoelVal->getNotificationMessage();
						$notificationMsg = str_replace("your", $userName1, $msg);
						//  $notificationMsg = $NotificationMdoelVal->getNotificationMessage();
						// $notificationMsg ="user";

					} else {
						$user_id_display = $NotificationMdoelVal->getUserID();

						$user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $user_id_display]
						);
						if ($user1 != '' && $user1 != null) {
							if (($user1->getUserFirstName() || $user1->getUserLastName()) != '') {
								$userName1 = $user1->getUserFirstName() . ' ' . $user1->getUserLastName();
							} else {
								$userName1 = '';
							}
						}

						$msg = $NotificationMdoelVal->getNotificationMessage();
						$notificationMsg = $NotificationMdoelVal->getNotificationMessage();
						//  $notificationMsg="sp";

					}

				} else {
					if ($NotificationMdoelVal->getNotificationTitle() == 'follow') {
						if ($NotificationMdoelVal->getUserID() == $userID) {
							$user_id_display = $NotificationMdoelVal->getToUserID();
							$user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id_display]
							);
							if ($user1 != '' && $user1 != null) {
								if (($user1->getUserFirstName() || $user1->getUserLastName()) != '') {
									$userName1 = $user1->getUserFirstName() . ' ' . $user1->getUserLastName();
								} else {
									$userName1 = '';
								}
							}
							$msg = $NotificationMdoelVal->getNotificationMessage();
							$notificationMsg = str_replace("you", $userName1, $msg);

						} else {
							$user_id_display = $NotificationMdoelVal->getUserID();

							$user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id_display]
							);
							if ($user1 != '' && $user1 != null) {
								if (($user1->getUserFirstName() || $user1->getUserLastName()) != '') {
									$userName1 = $user1->getUserFirstName() . ' ' . $user1->getUserLastName();
								} else {
									$userName1 = '';
								}
							}

							$msg = $NotificationMdoelVal->getNotificationMessage();
							$notificationMsg = $NotificationMdoelVal->getNotificationMessage();

						}
					} else {
						if ($NotificationMdoelVal->getUserID() == $userID) {
							$user_id_display = $NotificationMdoelVal->getToUserID();
							$notificationMsg = $NotificationMdoelVal->getNotificationMessage();

						} else {
							$user_id_display = $NotificationMdoelVal->getUserID();

							$user1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $user_id_display]
							);
							if ($user1 != '' && $user1 != null) {
								if (($user1->getUserFirstName() || $user1->getUserLastName()) != '') {
									$userName1 = $user1->getUserFirstName() . ' ' . $user1->getUserLastName();
								} else {
									$userName1 = '';
								}
							}

							$msg = $NotificationMdoelVal->getNotificationMessage();
							$notificationMsg = str_replace("your", $userName1, $msg);
						}


					}
				}


				$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
					['id' => $user_id_display]
				);
				if ($user != '' && $user != null) {
					if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
						$userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
					} else {
						$userName = '';
					}
					if ($user->getUserProfileImage() != '') {
						$userProfile = $this->baseurl() . $user->getUserProfileImage();
					} else {
						$userProfile = $this->baseurl() . 'defaultprofile.png';
					}
				}


				$listing[] = array(
					'user_id' => $user->getId(),
					'notify_id' => $NotificationMdoelVal->getId(),
					'user_name' => ($userName),
					'user_profile' => ($userProfile),
					'notify_msg' => ($notificationMsg),
					'user_type' => $user->getUserType()
				);

			}
			echo json_encode(array('success' => 1, 'message' => 'sucessfull', 'listing' => $listing));
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}
	/**************************VIVEK CHANGE*************************************************/


	/*public function notificationlistingAction(Request $user_id, Request $counter) {
        $request = $this->getRequest();
        $limitset = 10;
        $userID = $request->get('user_id');
        $min_num = (($request->get('counter') * $limitset) - $limitset);
        $max_num = $limitset;
         $manager = $this->getDoctrine()->getManager();
                $conn = $manager->getConnection();

                $relatedUser = $conn->query(
               "select * from notification_message where userID=" . $userID . " || toUserID=" . $userID . " LIMIT ".$min_num.", ".$max_num." "
                       
                        )->fetchAll();
                 $userIds = '';
                if($relatedUser != '' || $relatedUser != null){
                    foreach($relatedUser as $relatedUserVal){
                    $userIds[]   = $relatedUserVal['userID'];
                    }
                }else{
                    $userIds = '';
                }
          //echo '<pre>';print_r($userIds);die; 
        $NotificationList = $this->getDoctrine()->getRepository("AcmeDemoBundle:NotificationMessage")->findBy(['userID' => $userIds], array('id' => 'desc'), $max_num, $min_num);
       // echo '<pre>';print_r($NotificationList);die;
        if (!empty($NotificationList)) {
            foreach ($NotificationList as $NotificationMdoelVal) {
                $user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $NotificationMdoelVal->getUserID()]);
                if ($user != '' && $user != null) {
                    if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
                        $userName = $user->getUserFirstName() . ' ' . $user->getUserLastName();
                    } else {
                        $userName = '';
                    } if ($user->getUserProfileImage() != '') {
                        $userProfile = $this->baseurl() . $user->getUserProfileImage();
                    } else {
                        $userProfile = $this->baseurl() . 'defaultprofile.png';
                    }
                }
                $notificationMsg = $NotificationMdoelVal->getNotificationMessage();
                $listing[] = array('user_id' => $user->getId(), 'notify_id' => $NotificationMdoelVal->getId(), 'user_name' => ($userName), 'user_profile' => ($userProfile), 'notify_msg' => ($notificationMsg), 'user_type' => $user->getUserType());
            }
            echo json_encode(array('success' => 1, 'message' => 'sucessfull', 'listing' => $listing));
        } else {
            echo json_encode(array('success' => 0, 'message' => 'failure'));
        }
    }*/

	/*     * ************************************************************************NOTIFICATION LISTING  END ******************************************* */

	/**
	 * @Route("/counter", name="_counter")
	 * @Template()
	 */
	/*     * ************************************************************************COUNTER Begin ******************************************* */
	public function counterAction(Request $user_id)
	{
		$request = $this->getRequest();
		// $NotificationMdoel = $this->getDoctrine()->getRepository("AcmeDemoBundle:NotificationMessage")->findBy(['toUserID' => $request->get('user_id'),'notificationStatus'=>'0']);
		//  echo '<pre>';print_r($NotificationMdoel);die;
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();

		$NotificationMdoel = $conn->query(
			"select * from notification_message where ( userID=" . $request->get(
				'user_id'
			) . " || toUserID=" . $request->get('user_id') . ") and  notificationStatus='0'"

		)->fetchAll();


		if ($NotificationMdoel != '' && $NotificationMdoel != null) {
			$counter = count($NotificationMdoel);

			echo json_encode(
				array(
					'success' => 1,
					'message' => 'sucessfull',
					'counter' => $counter,
					'thumbnail_image' => $this->_apiUrl . 'timthumb.php?src='
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * ************************************************************************COUNTER  END ******************************************* */

	/**
	 * @Route("/notificationstatus", name="_notificationstatus")
	 * @Template()
	 */
	/*     * ************************************************************************NOTIFICATION STATUS BEGIN ******************************************* */
	public function notificationstatusAction(Request $notification_id, Request $notify_status, Request $user_id)
	{
		$request = $this->getRequest();
		$NotificationMdoel = $this->getDoctrine()->getRepository("AcmeDemoBundle:NotificationMessage")->findOneBy(
			['id' => $request->get('notification_id')]
		);

		if ($NotificationMdoel != '' && $NotificationMdoel != null) {
			$NotificationMdoel->setNotificationStatus($request->get('notify_status'));
			$em = $this->getDoctrine()->getManager();
			$em->persist($NotificationMdoel);
			$em->flush();
			//  $NotificationMdoel1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:NotificationMessage")->findBy(['toUserID' => $request->get('user_id'), 'notificationStatus' => '0']);


			$manager = $this->getDoctrine()->getManager();
			$conn = $manager->getConnection();
			$NotificationMdoel1 = $conn->query(
				"select * from notification_message where ( userID=" . $request->get(
					'user_id'
				) . " || toUserID=" . $request->get('user_id') . ") and  notificationStatus='0'"

			)->fetchAll();
			if ($NotificationMdoel1 != '' && $NotificationMdoel1 != null) {
				$counter = count($NotificationMdoel1);
			} else {
				$counter = '0';
			}


			echo json_encode(
				array('success' => 1, 'message' => 'status update sucessfully', 'counter' => count($NotificationMdoel1))
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * ************************************************************************NOTIFICATION STATUS  END ******************************************* */

	/**
	 * @Route("/chating", name="_chating")
	 * @Template()
	 */
	/*     * ************************************************************************CHATING Begin ******************************************* */
	public function chatingAction(Request $from_user_id, Request $to_user_id, Request $message, Request $date_time)
	{

		$request = $this->getRequest();
		 //$val = NOW();
		//echo '<pre>';print_r(($request->get('message')));die;
		$chatmodel = new UserChat();

		$chatmodel->setFromUserID($request->get('from_user_id'));
		$chatmodel->setToUserID($request->get('to_user_id'));
		$chatmodel->setUserMessage(($request->get('message')));
		$chatmodel->setDeliveryStatus(('SENT'));
		$chatmodel->setUserChatType(('SELF'));
		$chatmodel->setUserChatDate(time());
		$chatmodel->setDateTime($request->get('date_time'));
		$em = $this->getDoctrine()->getManager();
		$em->persist($chatmodel);
		$em->flush();
		/* NOTIFICATION CHAT START */
		$usenotification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findBy(
			['userID' => $request->get('to_user_id')]
		);

		if ($usenotification != '' && $usenotification != null) {
			foreach ($usenotification as $usenotificationVal) {
				$userModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
					['id' => $chatmodel->getFromUserID()]
				);
				if ($userModel != '') {
					if ($userModel->getUserFirstName() || $userModel->getUserLastName()) {
						$userName = $userModel->getUserFirstName() . ' ' . $userModel->getUserLastName();
					} else {
						$userName = '';
					}
				}
				if ($usenotificationVal->getDeviceType() == '0') {

					$registatoin_ids = $usenotificationVal->getDeviceID();
					$msg = 'chat';
					$chat_message = $chatmodel->getUserMessage();
					$chat_timestamp = $chatmodel->getUserChatDate();
					$chat_delivery_status = 'DELIVERED';
					// $user_chat_type = 'OTHER';
					$user_chat_type = $chatmodel->getUserChatType();
					$chat_user_id = $chatmodel->getToUserID();
					$chat_from_id = $chatmodel->getFromUserID();
					$chat_from_name = $userName;
					$msg_id = $chatmodel->getId();
					//$submsg = $userName . ' ' . 'chat you';
				}
				echo $registatoin_ids . " msg: " . $msg;
				$this->chat_notification(
					($registatoin_ids),
					($msg),
					($chat_message),
					$msg_id,
					($chat_timestamp),
					($chat_delivery_status),
					($user_chat_type),
					$chat_user_id,
					$chat_from_id,
					($chat_from_name)
				);
			}
			//echo '<pre>';print_r($chat_user_id);die;
		}
		$ChatModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserChat")->findOneBy(
			[
				'id' => $chatmodel->getId(),
				'fromUserID' => $chatmodel->getFromUserID(),
				'toUserID' => $chatmodel->getToUserID()
			]
		);
		if ($ChatModel != '' && $ChatModel != null) {
			$ChatModel->setDeliveryStatus(('DELIVERED'));
			$em = $this->getDoctrine()->getManager();
			$em->persist($ChatModel);
			$em->flush();
		}
		/* NOTIFICATION CHAT END */
		echo json_encode(
			array(
				'success' => 1,
				'message' => 'successfull',
				'delivery_status' => ('DELIVERED'),
				'msg_id' => $chatmodel->getId()
			)
		);
	}

	/*     * ************************************************************************CHATING  END ******************************************* */

	/**
	 * @Route("/chatdelete", name="_chatdelete")
	 * @Template()
	 */
	/*     * ************************************************************************CHATING Delete Begin ******************************************* */
	public function chatdeleteAction(Request $user_id, Request $to_user_id, Request $chat_id)
	{

		$request = $this->getRequest();
		// $val = NOW();
		$chatID = $request->get('chat_id');

		if ($chatID != '') {
			$chating_id = explode(",", $chatID);
		} else {
			$chating_id = '';
		}

		foreach ($chating_id as $chating_idVal) {
			$chatingData[] = $chating_idVal;
		}


		$Chating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserChat")->findBy(
			['id' => $chatingData, 'fromUserID' => $request->get('user_id'), 'toUserID' => $request->get('to_user_id')]
		);

		if ($Chating != '' && $Chating != null) {
			foreach ($Chating as $ChatingVal) {
				$em = $this->getDoctrine()->getManager();
				$em->remove($ChatingVal);
				$em->flush();
			}


			echo json_encode(array('success' => 1, 'message' => 'successfully deleted'));
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * ************************************************************************CHATING  Delete END ******************************************* */

	/**
	 * @Route("/chatlisting", name="_chatlisting")
	 * @Template()
	 */
	/*     * ************************************************************************CHATING Delete Begin ******************************************* */
	public function chatlistingAction(Request $user_id, Request $to_user_id, Request $counter)
	{

		$request = $this->getRequest();
		$Send = $request->get('user_id');
		$revice = $request->get('to_user_id');
		$limitset = 20;

		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		//$Chating = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserChat")->findBy(['deliveryStatus' => 'DELIVERED'], array('id' => 'asc'), $max_num, $min_num);
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$result = $conn->query(
			"select * from (
    select * from user_chat where (fromUserID='" . $Send . "' && toUserID='" . $revice . "') |(fromUserID='" . $revice . "' && toUserID='" . $Send . "')  order by userChatID desc limit " . $min_num . "," . $max_num . "
) tmp order by tmp.userChatID asc"
		)->fetchAll();

//echo '<pre>';print_r($result);die;
		$flag = '';
		if ($result != '' && $result != null) {
			//
			foreach ($result as $ChatingVal) {
				$sender = $ChatingVal['fromUserID'];
				$Reciver = $ChatingVal['toUserID'];
				$chat_msg = $ChatingVal['userMessage'];
				$chat_id = $ChatingVal['userChatID'];
				if ($Reciver == $revice) {
					$chat_type = 'OTHER';
				} else {
					$chat_type = 'SELF';
				}
				$chat_date = $ChatingVal['userChatDate'];
				$date_time = $ChatingVal['date_time'];
				$chat_status = $ChatingVal['deliveryStatus'];


				$flag = '1';
				$chat_listing[] = array(
					'id' => $chat_id,
					'chat_msg' => ($chat_msg),
					'chat_type' => ($chat_type),
					'chat_status' => ($chat_status),
					'chat_date' => $chat_date,
					'date_time' => $date_time
				);

				//echo '<pre>';print_r($chat_listing);
			}
			//echo '<pre>';print_r($chat_listing);die('ok');
			if ($flag == '1') {
				echo json_encode(array('success' => 1, 'message' => 'successfull', 'chat_listing' => $chat_listing));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * ************************************************************************CHATING  Delete END ******************************************* */

	/**
	 * @Route("/notificationsetting", name="_notificationsetting")
	 * @Template()
	 */
	/*     * ************************************************************************NOTIFICATION SETTING Begin ******************************************* */
	public function notificationsettingAction(Request $user_id, Request $imei, Request $isnotification)
	{

		$request = $this->getRequest();

		$usenotification = $this->getDoctrine()->getRepository("AcmeDemoBundle:Notification")->findOneBy(
			['userID' => $request->get('user_id'), 'imei' => $request->get('imei')]
		);

		if ($usenotification != '' && $usenotification != null) {
			$usenotification = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
				['id' => $usenotification->getUserID()]
			);
			if ($usenotification != '' && $usenotification != null) {

				$usenotification->setIsNotification($request->get('isnotification'));
				$em = $this->getDoctrine()->getManager();
				$em->persist($usenotification);
				$em->flush();

				echo json_encode(
					array(
						'success' => 1,
						'message' => 'status change successfully',
						'status' => $usenotification->getIsNotification()
					)
				);
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************NOTIFICATION SETTING END ******************************************* */

	/**
	 * @Route("/followerfollowing", name="_followerfollowing")
	 * @Template()
	 */
	/*     * ************************************************************************FOLLOWER FOLLOWING  Begin ******************************************* */
	public function followerfollowingAction(Request $user_id, Request $type, Request $counter)
	{

		$request = $this->getRequest();
		$limitset = 10;

		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		if ($request->get('type') == 'Following') {
			$Followers = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
				['userID' => $request->get('user_id'), 'followStatus' => '1'],
				array('id' => 'desc'),
				$max_num,
				$min_num
			);
//echo '<pre>';print_r($Followers);die;
			if ($Followers != '' && $Followers != null) {
				foreach ($Followers as $FollowersVal) {
					$userID = $FollowersVal->getToUserID();
					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
					if ($User != '') {
						if (($User->getUserFirstName() || $User->getUserLastName()) != '') {
							$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$userName = '';
						}
						if ($User->getUserProfileImage() != '') {
							$userProfile = $this->baseurl() . $User->getUserProfileImage();
						} else {
							$userProfile = $this->baseurl() . 'defaultprofile.png';
						}
						$userId = $User->getId();
						$userType = $User->getUserType();
					}
					$listing[] = ([
						'user_id' => $userId,
						'user_name' => $userName,
						'user_profile' => $userProfile,
						'user_type' => $userType
					]);
				}
				echo json_encode(array('success' => 1, 'message' => 'successful', 'listing' => $listing));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		} elseif ($request->get('type') == 'Followers') {
			$Following = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
				['toUserID' => $request->get('user_id'), 'followStatus' => '1'],
				array('id' => 'desc'),
				$max_num,
				$min_num
			);

			if ($Following != '' && $Following != null) {
				foreach ($Following as $FollowingVal) {
					$userID = $FollowingVal->getUserID();
					$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
					if ($User != '') {
						if (($User->getUserFirstName() || $User->getUserLastName()) != '') {
							$userName = $User->getUserFirstName() . ' ' . $User->getUserLastName();
						} else {
							$userName = '';
						}
						if ($User->getUserProfileImage() != '') {
							$userProfile = $this->baseurl() . $User->getUserProfileImage();
						} else {
							$userProfile = $this->baseurl() . 'defaultprofile.png';
						}
						$userId = $User->getId();
						$userType = $User->getUserType();
					}
					$listing[] = ([
						'user_id' => $userId,
						'user_name' => $userName,
						'user_profile' => $userProfile,
						'user_type' => $userType
					]);
				}
				echo json_encode(array('success' => 1, 'message' => 'successful', 'listing' => $listing));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure'));
			}
		}
	}

	/*     * *************************************************************************FOLLOWER FOLLOWING  END ******************************************* */

	/**
	 * @Route("/ontaglisting", name="_ontaglisting")
	 * @Template()
	 */
	/*     * ************************************************************************ON TAG LISTING Begin ******************************************* */
	public function ontaglistingAction(Request $tag_name, Request $user_id, Request $counter)
	{

		$request = $this->getRequest();
		$limitset = 10;

		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$tags = $request->get('tag_name');
		$userID = $request->get('user_id');
		$tagListing = $conn->query(
			"SELECT * 
FROM post_tags
WHERE tags = '" . $tags . "'
"
		)->fetchAll();

		if ($tagListing != '' && $tagListing != null) {
			foreach ($tagListing as $tagListingVal) {
				$post_Id[] = $tagListingVal['postID'];
			}

			$post = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(['id' => $post_Id]);

			if ($post != '' && $post != null) {
				//$Sp_user = '';
				foreach ($post as $postVal) {
					$fromUserID[] = $postVal->getUserID();
				}
				//[] = array_unique($User);
			} else {
				$fromUserID = [];
			}
			$userdata = '';
			for ($i = 0; $i < count($fromUserID); $i++) {
				if (($fromUserID[$i] != $userID)) {

					$userdata[] = $fromUserID[$i];
				}
			}

			if (count($fromUserID) < 1) {
				$userdata = '';
			}
			// echo '<pre>';print_r($userdata);die;
			if ($userID > 1) {
				$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $fromUserID],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);

				if ($User != '' && $User != null) {
					foreach ($User as $UserVal) {
						if ($UserVal->getUserType() == '1') {

							if (($UserVal->getUserFirstName() || $UserVal->getUserLastName()) != '') {
								$userName = $UserVal->getUserFirstName() . ' ' . $UserVal->getUserLastName();
							} else {
								$userName = '';
							}
							if ($UserVal->getUserProfileImage() != '' && $UserVal->getUserProfileImage() > 0) {
								$user_profile = $this->baseurl() . $UserVal->getUserProfileImage();
							} else {
								$user_profile = $this->baseurl() . 'defaultprofile.png';
							}
							if ($UserVal->getUserAddress() != '') {
								$userAddress = $UserVal->getUserAddress();
							} else {
								$userAddress = '';
							}
							if ($UserVal->getUserMobileNo() != '') {
								$usercontact = $UserVal->getUserMobileNo();
							} else {
								$usercontact = '';
							}
							if ($UserVal->getLat() != '') {
								$userLat = $UserVal->getLat();
							} else {
								$userLat = '';
							}
							if ($UserVal->getLongitute() != '') {
								$userLong = $UserVal->getLongitute();
							} else {
								$userLong = '';
							}
							if ($UserVal->getCompanyName() != '') {
								$usercompany = $UserVal->getCompanyName();
							} else {
								$usercompany = '';
							}
							$sp_user_id = $UserVal->getId();
							$userType = $UserVal->getUserType();
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $UserVal->getId(), 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									//echo '<pre>';print_r($UserFollowVal->getUserID());
									if (($UserFollowVal->getUserID() == $userID) && ($UserFollowVal->getToUserID() == $UserVal->getId())
									) {
										// die('ok');
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							$AlbumsModel = $conn->query(
								"SELECT postID FROM `post` where userID = " . $UserVal->getId() . " or userTagID = " . $UserVal->getId() . " order by postID desc  limit 0,6"
							)->fetchAll();
							if (isset($AlbumsModel) && !empty($AlbumsModel)) {
								foreach ($AlbumsModel as $customersAlbum) {

									$userAlbum = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customersAlbum['postID']]);

									if ($userAlbum != '' && $userAlbum != null) {
										$album_id = $userAlbum->getId();
										if ($userAlbum->getPostCaption() != '') {
											$post_caption = $userAlbum->getPostCaption();
										} else {
											$post_caption = '';
										}
										if ($userAlbum->getPostImageFront() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFront();
										} elseif ($userAlbum->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontLeft();
										} elseif ($userAlbum->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageLeft();
										} elseif ($userAlbum->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackLeft();
										} elseif ($userAlbum->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBack();
										} elseif ($userAlbum->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackRight();
										} elseif ($userAlbum->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageRight();
										} elseif ($userAlbum->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}
//                                            $AlbumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(['postID' => $userAlbum->getId()]);
//                                            if ($AlbumRate != '') {
//                                                $rate = $AlbumRate->getUserRating();
//                                            } else {
//                                                $rate = '';
//                                            }
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $userAlbum->getId()]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										$UserConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserTagID()]);
										$User_postConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserID()]);

										if ($userType == '1') {

											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $userType;
											}

										} else {
											if ($userType == '0') {
												if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

													if ($User_postConsumer->getId() == $UserConsumer->getId()) {
														$user_id = $sp_user_id;
														$user_name = $userName;
														$post_usertype = $userType;
													} else {
														if ($UserConsumer->getUserType() == '1') {
															$user_id = $UserConsumer->getId();
															if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
															) {
																$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
															} else {
																$user_name = '';

															}
															$post_usertype = $UserConsumer->getUserType();
														} else {
															if ($User_postConsumer->getUserType() == '1') {
																$user_id = $User_postConsumer->getId();
																if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
																) {
																	$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
																} else {
																	$user_name = '';
																}
																$post_usertype = $User_postConsumer->getUserType();
															} else {
																$user_id = '';
																$user_name = '';
																$post_usertype = $userType;
															}
														}

													}


												}

											}
										}


										/*     if ($UserConsumer != '' && $UserConsumer != null) {
                                                $user_id = $UserConsumer->getId();
                                                if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                                    $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                                } else {
                                                    $user_name = '';
                                                }
                                            } else {
                                                $user_name = '';
                                            }
                                         */


										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findOneBy(['postID' => $album_id]);
//echo '<pre>';print_r($UserRating1);die;
										if ($UserRating != '' && $UserRating != null) {

											if ($UserRating->getUserRating() != '') {
												$rating1 = $UserRating->getUserRating();
											} else {
												$rating1 = '';
											}
											if ($UserRating->getUserReviews() != '') {
												$reviews[] = $UserRating->getUserReviews();
											} else {
												$reviews[] = '';
											}
											if ($UserRating->getUserRating() != '') {
												$rating[] = $UserRating->getUserRating();
											} else {
												$rating[] = '';
											}
										} else {
											$rating1 = '';
											$rating = [];
											$reviews = '';
										}

										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											"user_type" => $post_usertype,
											'album_image' => ($userprofile),
											'user_name' => ($user_name),
											'rates' => ($rating1)
										);
									}

								}
							} else {
								$reviews = '';
								$rating = [];
								$album_detail = [];
							}
							if (count($rating) > 0) {
								$count = count($rating);
								$rating1 = array_sum($rating) / $count;
								$ratvalues = number_format((float)$rating1, 1, '.', '');
								unset($rating);
							} else {
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = array(
								'user_name' => ($userName),
								'sp_user_id' => ($UserVal->getId()),
								'contact' => ($usercontact),
								'company_name' => ($usercompany),
								'user_type' => $userType,
								'lat' => $userLat,
								'long' => $userLong,
								'user_address' => ($userAddress),
								'profile_image' => ($user_profile)
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'follow_status' => $follow_status,
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
						} elseif ($UserVal->getUserType() == '0') {

							if (($UserVal->getUserFirstName() || $UserVal->getUserLastName()) != '') {
								$userName = $UserVal->getUserFirstName() . ' ' . $UserVal->getUserLastName();
							} else {
								$userName = '';
							}
							if ($UserVal->getUserProfileImage() != '' && $UserVal->getUserProfileImage() > 0) {
								$user_profile = $this->baseurl() . $UserVal->getUserProfileImage();
							} else {
								$user_profile = $this->baseurl() . 'defaultprofile.png';
							}
							if ($UserVal->getUserAddress() != '') {
								$userAddress = $UserVal->getUserAddress();
							} else {
								$userAddress = '';
							}
							if ($UserVal->getUserMobileNo() != '') {
								$usercontact = $UserVal->getUserMobileNo();
							} else {
								$usercontact = '';
							}
							if ($UserVal->getLat() != '') {
								$userLat = $UserVal->getLat();
							} else {
								$userLat = '';
							}
							if ($UserVal->getLongitute() != '') {
								$userLong = $UserVal->getLongitute();
							} else {
								$userLong = '';
							}
							$sp_user_id = $UserVal->getId();
							$userType = $UserVal->getUserType();
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									//echo '<pre>';print_r($UserFollowVal->getUserID());
									if (($UserFollowVal->getUserID() == $userID) && ($UserFollowVal->getToUserID() == $UserVal->getId())
									) {
										// die('ok');
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							$AlbumsModel = $conn->query(
								"SELECT postID FROM `post` where userID = " . $UserVal->getId() . " or userTagID = " . $UserVal->getId() . " order by postID desc  limit 0,6"
							)->fetchAll();
							if (isset($AlbumsModel) && !empty($AlbumsModel)) {
								foreach ($AlbumsModel as $customersAlbum) {

									$userAlbum = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customersAlbum['postID']]);

									if ($userAlbum != '' && $userAlbum != null) {
										$album_id = $userAlbum->getId();
										if ($userAlbum->getPostCaption() != '') {
											$post_caption = $userAlbum->getPostCaption();
										} else {
											$post_caption = '';
										}
										if ($userAlbum->getPostImageFront() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFront();
										} elseif ($userAlbum->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontLeft();
										} elseif ($userAlbum->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageLeft();
										} elseif ($userAlbum->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackLeft();
										} elseif ($userAlbum->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBack();
										} elseif ($userAlbum->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackRight();
										} elseif ($userAlbum->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageRight();
										} elseif ($userAlbum->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}

//                                            $AlbumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(['postID' => $userAlbum->getId()]);
//                                            if ($AlbumRate != '') {
//                                                $rate = $AlbumRate->getUserRating();
//                                            } else {
//                                                $rate = '';
//                                            }
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $userAlbum->getId()]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										$UserConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserTagID()]);
										$User_postConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserID()]);

										if ($userType == '1') {

											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $userType;
											}

										} else {
											if ($userType == '0') {
												if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

													if ($User_postConsumer->getId() == $UserConsumer->getId()) {
														$user_id = $sp_user_id;
														$user_name = $userName;
														$post_usertype = $userType;
													} else {
														if ($UserConsumer->getUserType() == '1') {
															$user_id = $UserConsumer->getId();
															if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
															) {
																$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
															} else {
																$user_name = '';

															}
															$post_usertype = $UserConsumer->getUserType();
														} else {
															if ($User_postConsumer->getUserType() == '1') {
																$user_id = $User_postConsumer->getId();
																if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
																) {
																	$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
																} else {
																	$user_name = '';
																}
																$post_usertype = $User_postConsumer->getUserType();
															} else {
																$user_id = '';
																$user_name = '';
																$post_usertype = $userType;
															}
														}

													}


												}

											}
										}
										/*if ($UserConsumer != '' && $UserConsumer != null) {
                                                $user_id = $UserConsumer->getId();
                                                if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                                    $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                                } else {
                                                    $user_name = '';
                                                }
                                            } else {
                                                $user_name = '';
                                            }
                                            */

										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findOneBy(['postID' => $album_id]);
//echo '<pre>';print_r($UserRating);die;
										if ($UserRating != '' && $UserRating != null) {

											if ($UserRating->getUserRating() != '') {
												$rating1 = $UserRating->getUserRating();
											} else {
												$rating1 = '';
											}
											if ($UserRating->getUserReviews() != '') {
												$reviews[] = $UserRating->getUserReviews();
											} else {
												$reviews[] = '';
											}
											if ($UserRating->getUserRating() != '') {
												$rating[] = $UserRating->getUserRating();
											} else {
												$rating[] = '';
											}
										} else {
											$rating1 = '';
											$rating = [];
											$reviews = '';
										}
										//echo '<pre>';print_R($UserRating);
										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											'user_type' => $post_usertype,
											'album_image' => ($userprofile),
											'user_name' => ($user_name),
											'rates' => ($rating1)
										);
									}

								}
							} else {
								$reviews = '';
								$rating = [];
								$album_detail = [];
							}
							if (count($rating) > 0) {
								$count = count($rating);
								$rating1 = array_sum($rating) / $count;
								$ratvalues = number_format((float)$rating1, 1, '.', '');
								unset($rating);
							} else {
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}
							$companyName = '';
							$service_name = '';
							$serviceprice = '';
							$sp_detail[] = array(
								'user_name' => ($userName),
								'sp_user_id' => ($UserVal->getId()),
								'contact' => ($usercontact),
								'company_name' => ($companyName),
								'user_type' => $userType,
								'lat' => $userLat,
								'long' => $userLong,
								'user_address' => ($userAddress),
								'profile_image' => ($user_profile)
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'follow_status' => $follow_status,
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
						}
					}
					echo json_encode(array('success' => '1', 'message' => 'successsfull', 'user_detail' => $sp_detail));
				} else {
					echo json_encode(array('success' => '0', 'message' => 'failure'));
				}
			} else {
				$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(
					['id' => $fromUserID],
					array('id' => 'desc'),
					$max_num,
					$min_num
				);

				//$User = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findBy(['id' => $userdata]);

				if ($User != '' && $User != null) {
					foreach ($User as $UserVal) {
						if ($UserVal->getUserType() == '1') {

							if (($UserVal->getUserFirstName() || $UserVal->getUserLastName()) != '') {
								$userName = $UserVal->getUserFirstName() . ' ' . $UserVal->getUserLastName();
							} else {
								$userName = '';
							}
							if ($UserVal->getUserProfileImage() != '' && $UserVal->getUserProfileImage() > 0) {
								$user_profile = $this->baseurl() . $UserVal->getUserProfileImage();
							} else {
								$user_profile = $this->baseurl() . 'defaultprofile.png';
							}
							if ($UserVal->getUserAddress() != '') {
								$userAddress = $UserVal->getUserAddress();
							} else {
								$userAddress = '';
							}
							if ($UserVal->getUserMobileNo() != '') {
								$usercontact = $UserVal->getUserMobileNo();
							} else {
								$usercontact = '';
							}
							if ($UserVal->getLat() != '') {
								$userLat = $UserVal->getLat();
							} else {
								$userLat = '';
							}
							if ($UserVal->getLongitute() != '') {
								$userLong = $UserVal->getLongitute();
							} else {
								$userLong = '';
							}
							if ($UserVal->getCompanyName() != '') {
								$usercompany = $UserVal->getCompanyName();
							} else {
								$usercompany = '';
							}
							$sp_user_id = $UserVal->getId();
							$userType = $UserVal->getUserType();
							$serviceName = $this->getDoctrine()->getRepository(
								"AcmeDemoBundle:UserServices"
							)->findOneBy(['userID' => $UserVal->getId(), 'topService' => 1]);

							if ($serviceName != '' && $serviceName != null) {
								$serviceId = $serviceName->getServiceID();

								$serviceprice = $serviceName->getServicePrice();
								$masterService = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:MasterServices"
								)->findOneBy(['id' => $serviceId]);
								$service_name = $masterService->getServiceName();
							} else {
								$serviceprice = '';
								$service_name = '';
							}
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									//echo '<pre>';print_r($UserFollowVal->getUserID());
									if (($UserFollowVal->getUserID() == $userID) && ($UserFollowVal->getToUserID() == $UserVal->getId())
									) {
										// die('ok');
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							$AlbumsModel = $conn->query(
								"SELECT postID FROM `post` where userID = " . $UserVal->getId() . " or userTagID = " . $UserVal->getId() . " order by postID desc  limit 0,6"
							)->fetchAll();
							if (isset($AlbumsModel) && !empty($AlbumsModel)) {
								foreach ($AlbumsModel as $customersAlbum) {

									$userAlbum = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customersAlbum['postID']]);

									if ($userAlbum != '' && $userAlbum != null) {
										$album_id = $userAlbum->getId();
										if ($userAlbum->getPostCaption() != '') {
											$post_caption = $userAlbum->getPostCaption();
										} else {
											$post_caption = '';
										}
										if ($userAlbum->getPostImageFront() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFront();
										} elseif ($userAlbum->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontLeft();
										} elseif ($userAlbum->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageLeft();
										} elseif ($userAlbum->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackLeft();
										} elseif ($userAlbum->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBack();
										} elseif ($userAlbum->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackRight();
										} elseif ($userAlbum->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageRight();
										} elseif ($userAlbum->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}
//                                            $AlbumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(['postID' => $userAlbum->getId()]);
//                                            if ($AlbumRate != '') {
//                                                $rate = $AlbumRate->getUserRating();
//                                            } else {
//                                                $rate = '';
//                                            }
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $userAlbum->getId()]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										$UserConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserTagID()]);
										$User_postConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserID()]);

										if ($userType == '1') {

											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $userType;
											}

										} else {
											if ($userType == '0') {
												if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

													if ($User_postConsumer->getId() == $UserConsumer->getId()) {
														$user_id = $sp_user_id;
														$user_name = $userName;
														$post_usertype = $userType;
													} else {
														if ($UserConsumer->getUserType() == '1') {
															$user_id = $UserConsumer->getId();
															if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
															) {
																$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
															} else {
																$user_name = '';

															}
															$post_usertype = $UserConsumer->getUserType();
														} else {
															if ($User_postConsumer->getUserType() == '1') {
																$user_id = $User_postConsumer->getId();
																if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
																) {
																	$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
																} else {
																	$user_name = '';
																}
																$post_usertype = $User_postConsumer->getUserType();
															} else {
																$user_id = '';
																$user_name = '';
																$post_usertype = $userType;
															}
														}

													}


												}

											}
										}
										/* if ($UserConsumer != '' && $UserConsumer != null) {
                                                $user_id = $UserConsumer->getId();
                                                if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                                    $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                                } else {
                                                    $user_name = '';
                                                }
                                            } else {
                                                $user_name = '';
                                            }
                                            */

										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findOneBy(['postID' => $album_id]);
//echo '<pre>';print_r($UserRating1);die;
										if ($UserRating != '' && $UserRating != null) {

											if ($UserRating->getUserRating() != '') {
												$rating1 = $UserRating->getUserRating();
											} else {
												$rating1 = '';
											}
											if ($UserRating->getUserReviews() != '') {
												$reviews[] = $UserRating->getUserReviews();
											} else {
												$reviews[] = '';
											}
											if ($UserRating->getUserRating() != '') {
												$rating[] = $UserRating->getUserRating();
											} else {
												$rating[] = '';
											}
										} else {
											$rating1 = '';
											$rating = [];
											$reviews = '';
										}

										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											'user_type' => $post_usertype,
											'album_image' => ($userprofile),
											'user_name' => ($user_name),
											'rates' => ($rating1)
										);
									}

								}
							} else {
								$reviews = '';
								$rating = [];
								$album_detail = [];
							}
							if (count($rating) > 0) {
								$count = count($rating);
								$rating1 = array_sum($rating) / $count;
								$ratvalues = number_format((float)$rating1, 1, '.', '');
								unset($rating);
							} else {
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}

							$sp_detail[] = array(
								'user_name' => ($userName),
								'sp_user_id' => ($UserVal->getId()),
								'contact' => ($usercontact),
								'company_name' => ($usercompany),
								'user_type' => $userType,
								'lat' => $userLat,
								'long' => $userLong,
								'user_address' => ($userAddress),
								'profile_image' => ($user_profile)
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'follow_status' => $follow_status,
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
						} elseif ($UserVal->getUserType() == '0') {

							if (($UserVal->getUserFirstName() || $UserVal->getUserLastName()) != '') {
								$userName = $UserVal->getUserFirstName() . ' ' . $UserVal->getUserLastName();
							} else {
								$userName = '';
							}
							if ($UserVal->getUserProfileImage() != '' && $UserVal->getUserProfileImage() > 0) {
								$user_profile = $this->baseurl() . $UserVal->getUserProfileImage();
							} else {
								$user_profile = $this->baseurl() . 'defaultprofile.png';
							}
							if ($UserVal->getUserAddress() != '') {
								$userAddress = $UserVal->getUserAddress();
							} else {
								$userAddress = '';
							}
							if ($UserVal->getUserMobileNo() != '') {
								$usercontact = $UserVal->getUserMobileNo();
							} else {
								$usercontact = '';
							}
							if ($UserVal->getLat() != '') {
								$userLat = $UserVal->getLat();
							} else {
								$userLat = '';
							}
							if ($UserVal->getLongitute() != '') {
								$userLong = $UserVal->getLongitute();
							} else {
								$userLong = '';
							}
							$sp_user_id = $UserVal->getId();
							$userType = $UserVal->getUserType();
							$UserFollow = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findBy(
								['followStatus' => '1']
							);

							if ($UserFollow != '') {
								$follow_status = '0';
								foreach ($UserFollow as $UserFollowVal) {
									//echo '<pre>';print_r($UserFollowVal->getUserID());
									if (($UserFollowVal->getUserID() == $userID) && ($UserFollowVal->getToUserID() == $UserVal->getId())
									) {
										// die('ok');
										$follow_status = '1';
									}
								}
							} else {
								$follow_status = '0';
							}
							$AlbumsModel = $conn->query(
								"SELECT postID FROM `post` where userID = " . $UserVal->getId() . " or userTagID = " . $UserVal->getId() . " order by postID desc  limit 0,6"
							)->fetchAll();
							if (isset($AlbumsModel) && !empty($AlbumsModel)) {
								foreach ($AlbumsModel as $customersAlbum) {

									$userAlbum = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:AlbumPost"
									)->findOneBy(['id' => $customersAlbum['postID']]);

									if ($userAlbum != '' && $userAlbum != null) {
										$album_id = $userAlbum->getId();
										if ($userAlbum->getPostCaption() != '') {
											$post_caption = $userAlbum->getPostCaption();
										} else {
											$post_caption = '';
										}
										if ($userAlbum->getPostImageFront() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFront();
										} elseif ($userAlbum->getPostImageFrontLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontLeft();
										} elseif ($userAlbum->getPostImageLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageLeft();
										} elseif ($userAlbum->getPostImageBackLeft() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackLeft();
										} elseif ($userAlbum->getPostImageBack() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBack();
										} elseif ($userAlbum->getPostImageBackRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageBackRight();
										} elseif ($userAlbum->getPostImageRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageRight();
										} elseif ($userAlbum->getPostImageFrontRight() != '') {
											$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontRight();
										} else {
											$userprofile = '';
										}

//                                            $AlbumRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(['postID' => $userAlbum->getId()]);
//                                            if ($AlbumRate != '') {
//                                                $rate = $AlbumRate->getUserRating();
//                                            } else {
//                                                $rate = '';
//                                            }
										$PostTags = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:PostTags"
										)->findBy(['postID' => $userAlbum->getId()]);
										if ($PostTags != '') {
											$tag_status = '0';
											foreach ($PostTags as $PostTagsVal) {
												if ($PostTagsVal->getTags() != '') {
													$tag_status = '1';
												}
											}
										} else {
											$tag_status = '0';
										}
										$UserConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserTagID()]);
										$User_postConsumer = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:User"
										)->findOneBy(['id' => $userAlbum->getUserID()]);

										if ($userType == '1') {

											if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

												$user_id = $sp_user_id;
												$user_name = $userName;
												$post_usertype = $userType;
											}

										} else {
											if ($userType == '0') {
												if ($UserConsumer != '' && $UserConsumer != null && $User_postConsumer != '' && $User_postConsumer != null) {

													if ($User_postConsumer->getId() == $UserConsumer->getId()) {
														$user_id = $sp_user_id;
														$user_name = $userName;
														$post_usertype = $userType;
													} else {
														if ($UserConsumer->getUserType() == '1') {
															$user_id = $UserConsumer->getId();
															if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != ''
															) {
																$user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
															} else {
																$user_name = '';

															}
															$post_usertype = $UserConsumer->getUserType();
														} else {
															if ($User_postConsumer->getUserType() == '1') {
																$user_id = $User_postConsumer->getId();
																if ($User_postConsumer->getUserFirstName() || $User_postConsumer->getUserLastName() != ''
																) {
																	$user_name = $User_postConsumer->getUserFirstName() . ' ' . $User_postConsumer->getUserLastName();
																} else {
																	$user_name = '';
																}
																$post_usertype = $User_postConsumer->getUserType();
															} else {
																$user_id = '';
																$user_name = '';
																$post_usertype = $userType;
															}
														}

													}


												}

											}
										}
										/* if ($UserConsumer != '' && $UserConsumer != null) {
                                                $user_id = $UserConsumer->getId();
                                                if ($UserConsumer->getUserFirstName() || $UserConsumer->getUserLastName() != '') {
                                                    $user_name = $UserConsumer->getUserFirstName() . ' ' . $UserConsumer->getUserLastName();
                                                } else {
                                                    $user_name = '';
                                                }
                                            } else {
                                                $user_name = '';
                                            }
                                            */

										$UserRating = $this->getDoctrine()->getRepository(
											"AcmeDemoBundle:UserRating"
										)->findOneBy(['postID' => $album_id]);

										if ($UserRating != '' && $UserRating != null) {

											if ($UserRating->getUserRating() != '') {
												$rating1 = $UserRating->getUserRating();
											} else {
												$rating1 = '';
											}
											if ($UserRating->getUserReviews() != '') {
												$reviews[] = $UserRating->getUserReviews();
											} else {
												$reviews[] = '';
											}
											if ($UserRating->getUserRating() != '') {
												$rating[] = $UserRating->getUserRating();
											} else {
												$rating[] = '';
											}
										} else {
											$rating1 = '';
											$rating = [];
											$reviews = '';
										}
										//echo '<pre>';print_R($UserRating);
										$album_detail[] = array(
											'album_id' => ($album_id),
											'tag_status' => $tag_status,
											'album_service' => ($post_caption),
											'user_id' => ($user_id),
											'user_type' => $post_usertype,
											'album_image' => ($userprofile),
											'user_name' => ($user_name),
											'rates' => ($rating1)
										);
									}

								}
							} else {
								$reviews = '';
								$rating = [];
								$album_detail = [];
							}
							if (count($rating) > 0) {
								$count = count($rating);
								$rating1 = array_sum($rating) / $count;
								$ratvalues = number_format((float)$rating1, 1, '.', '');
								unset($rating);
							} else {
								$ratvalues = '';
							}


							if (count($reviews) > 0 && $reviews != null) {
								$countReview = count($reviews);
							} else {
								$countReview = 0;
							}
							if (count($ratvalues) > 0 && $ratvalues != null) {
								$countRates = $ratvalues;
							} else {
								$countRates = 0;
							}
							$companyName = '';
							$service_name = '';
							$serviceprice = '';
							$sp_detail[] = array(
								'user_name' => ($userName),
								'sp_user_id' => ($UserVal->getId()),
								'contact' => ($usercontact),
								'company_name' => ($companyName),
								'user_type' => $userType,
								'lat' => $userLat,
								'long' => $userLong,
								'user_address' => ($userAddress),
								'profile_image' => ($user_profile)
							,
								'service_name' => ($service_name),
								'service_price' => ($serviceprice),
								'follow_status' => $follow_status,
								'user_chat' => 0,
								'total_reviews' => ($countReview),
								'total_rate' => ($countRates),
								'albums' => $album_detail
							);
							unset($album_detail);
						}
					}
					echo json_encode(array('success' => '1', 'message' => 'successsfull', 'user_detail' => $sp_detail));
				} else {
					echo json_encode(array('success' => '0', 'message' => 'failure'));
				}
			}
		}
	}

	/*     * *************************************************************************ON TAG LISTING END ******************************************* */

	/**
	 * @Route("/customerreviews", name="_customerreviews")
	 * @Template()
	 */
	/*     * ************************************************************************CUSTOMER REVIEWS Begin ******************************************* */
	public function customerreviewsAction(Request $user_id, Request $counter)
	{
		$request = $this->getRequest();
		$limitset = 10;
		$min_num = (($request->get('counter') * $limitset) - $limitset);
		$max_num = $limitset;
		$userReviewsRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findBy(
			['fromUserID' => $request->get('user_id')],
			array('id' => 'asc'),
			$max_num,
			$min_num
		);
//echo '<pre>';print_r($userReviewsRate);die;
		if ($userReviewsRate != '' && $userReviewsRate != null) {

			foreach ($userReviewsRate as $userReviewsRateVal) {
				$userID = $userReviewsRateVal->getToUserID();
				$rate = $userReviewsRateVal->getUserRating();
				$reviews = $userReviewsRateVal->getUserReviews();
				$album_id = $userReviewsRateVal->getPostID();
				$user = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $userID]);
				if (!empty($user)) {
					if (($user->getUserFirstName() || $user->getUserLastName()) != '') {
						$user_name = $user->getUserFirstName() . ' ' . $user->getUserLastName();
					} else {
						$user_name = '';
					}
					if ($user->getUserProfileImage() != '' && $user->getUserProfileImage() > 0) {
						$user_profile = $this->baseurl() . $user->getUserProfileImage();
					} else {
						$user_profile = $this->baseurl() . 'defaultprofile.png';
					}
				} else {
					$user = '';
					$user_name = '';
					$user_profile = $this->baseurl() . 'defaultprofile.png';
				}
				$userAlbum = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
					['id' => $album_id]
				);
				if ($userAlbum != '' && $userAlbum != null) {
					if ($userAlbum->getPostImageFront() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageFront();
					} elseif ($userAlbum->getPostImageFrontLeft() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontLeft();
					} elseif ($userAlbum->getPostImageLeft() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageLeft();
					} elseif ($userAlbum->getPostImageBackLeft() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageBackLeft();
					} elseif ($userAlbum->getPostImageBack() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageBack();
					} elseif ($userAlbum->getPostImageBackRight() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageBackRight();
					} elseif ($userAlbum->getPostImageRight() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageRight();
					} elseif ($userAlbum->getPostImageFrontRight() != '') {
						$userprofile = $this->baseurl() . $userAlbum->getPostImageFrontRight();
					} else {
						$userprofile = '';
					}
					$tag_status = '0';
					$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['postID' => $userAlbum->getId()]
					);
					if ($PostTags != '') {
						foreach ($PostTags as $PostTagsVal) {
							if ($PostTagsVal->getTags() != '') {
								$tag_status = '1';
							} else {
								$tag_status = '0';
							}
						}
					} else {
						$tag_status = '0';
					}
					$Rating[] = $rate;
					$count = count($Rating);
					$rating = array_sum($Rating) / $count;
					$ratvalues = number_format((float)$rating, 1, '.', '');
					$userrateReview[] = array(
						'user_id' => $userID,
						'tag_status' => $tag_status,
						'user_name' => $user_name,
						'profile_image' => $user_profile,
						'user_rate' => $rate,
						'user_reviews' => $reviews
					,
						'album_id' => $album_id,
						'album_image' => $userprofile
					);
				} else {
					$userrateReview = [];
					$ratvalues = '';
				}
			}

			echo json_encode(
				array(
					'success' => 1,
					'message' => 'successfull',
					'reviewer_id' => $request->get('user_id'),
					'avg_rate' => $ratvalues,
					'user_rateReviews' => $userrateReview
				)
			);
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************CUSTOMER REVIEWS  End******************************************* */

	/**
	 * @Route("/editalbum", name="_editalbum")
	 * @Template()
	 */
	/*     * ************************************************************************CUSTOMER REVIEWS Begin ******************************************* */
	public function editalbumAction(
		Request $album_id,
		Request $image_name,
		Request $image_base,
		Request $image_tag,
		Request $image_note,
		Request $x_axis,
		Request $y_axis,
		Request $image_height,
		Request $image_width,
		Request $image_type
	) {
		$request = $this->getRequest();
		$albumID = $request->get('album_id');
		$image = $request->get('image_name');
		$imageBase = $request->get('image_base');
		$tag = $request->get('image_tag');
		$notes = $request->get('');
		$xaxis = $request->get('x_axis');
		$yaxis = $request->get('y_axis');
		$imageheight = $request->get('image_height');
		$imageWidth = $request->get('image_width');
		$imagetype = $request->get('image_type');

		$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
			['postID' => $albumID, 'imageType' => $imagetype]
		);

		if ($postTag != '' && $postTag != null) {
			if ($request->get('image_base') != '' && $request->get('image_base') != null) {

				foreach ($postTag as $postTagVal) {

					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTagVal);
					$em->flush();
				}
				$tags = explode(',', $tag);
				foreach ($tags as $tagsVal) {
					$tagsImage[] = $tagsVal;
				}

				$x_axis = explode(',', $xaxis);
				foreach ($x_axis as $x_axisVal) {
					$x_axisImage[] = $x_axisVal;
				}
				$y_axis = explode(',', $yaxis);
				foreach ($y_axis as $y_axisVal) {
					$y_axisImage[] = $y_axisVal;
				}

//                $notes = explode(',', $notes);
//                foreach ($notes as $notesVal) {
//                    $noteImage[] = $notesVal;
//                }
				$post = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(['id' => $albumID]);

				if ($request->get('image_type') == 'Front') {
					$imageBase = ($request->get('image_base'));
					if ($imageBase != '' && $imageBase != null) {
						$Image = $this->UploadFile($image, $imageBase);
					}
					$post->setPostImageFront($Image);
					$em = $this->getDoctrine()->getManager();
					$em->persist($post);
					$em->flush();
					if (count($tagsImage) > 0 && $tagsImage[0] != '') {
						$tagImages = count($tagsImage);
					} else {
						$tagImages = count($request->get('image_type'));
					}

					for ($i = 0; $i < $tagImages; $i++) {
						$ImageTages = new PostTags();
						if ($imageheight != '0' && $imageWidth != '0') {
							$ImageTages->setImageSize($imageheight);
							$ImageTages->setImageWidth($imageWidth);
						} else {
							$ImageTages->setImageSize('');
							$ImageTages->setImageWidth('');
						}
						$ImageTages->setPostID($post->getId());
						$ImageTages->setImageType($request->get('image_type'));
						$ImageTages->setImageName($post->getPostImageFront());
						if ($x_axisImage[$i] != '0' && $y_axisImage[$i] != '0') {
							$ImageTages->setTags($tagsImage[$i]);

							$ImageTages->setTagNote('');
							$ImageTages->setX_Axis($x_axisImage[$i]);
							$ImageTages->setY_Axis($y_axisImage[$i]);
						} else {
							$ImageTages->setTags('');

							$ImageTages->setTagNote('');
							$ImageTages->setX_Axis('');
							$ImageTages->setY_Axis('');
						}
						$em = $this->getDoctrine()->getManager();
						$em->persist($ImageTages);
						$em->flush();
					}
					echo json_encode(array('success' => 1, 'message' => 'Successfull'));
				} else {
					if ($request->get('image_type') == 'Front Left') {
						$imageBase = ($request->get('image_base'));
						if ($imageBase != '' && $imageBase != null) {
							$Image = $this->UploadFile($image, $imageBase);
						}
						$post->setPostImageFrontLeft($Image);
						$em = $this->getDoctrine()->getManager();
						$em->persist($post);
						$em->flush();
						if (count($tagsImage) > 0 && $tagsImage[0] != '') {
							$tagImages = count($tagsImage);
						} else {
							$tagImages = count($request->get('image_type'));
						}

						for ($i = 0; $i < $tagImages; $i++) {
							$ImageTages = new PostTags();
							$ImageTages->setImageSize($imageheight);
							$ImageTages->setImageWidth($imageWidth);
							$ImageTages->setPostID($post->getId());
							$ImageTages->setImageType($request->get('image_type'));
							$ImageTages->setImageName($post->getPostImageFrontLeft());
							$ImageTages->setTags($tagsImage[$i]);
							$ImageTages->setTagNote('');
							$ImageTages->setX_Axis($x_axisImage[$i]);
							$ImageTages->setY_Axis($y_axisImage[$i]);
							$em = $this->getDoctrine()->getManager();
							$em->persist($ImageTages);
							$em->flush();
						}
						echo json_encode(array('success' => 1, 'message' => 'Successfull'));
					} else {
						if ($request->get('image_type') == 'Left') {
							$imageBase = ($request->get('image_base'));
							if ($imageBase != '' && $imageBase != null) {
								$Image = $this->UploadFile($image, $imageBase);
							}
							$post->setPostImageLeft($Image);
							$em = $this->getDoctrine()->getManager();
							$em->persist($post);
							$em->flush();
							if (count($tagsImage) > 0 && $tagsImage[0] != '') {
								$tagImages = count($tagsImage);
							} else {
								$tagImages = count($request->get('image_type'));
							}

							for ($i = 0; $i < $tagImages; $i++) {
								$ImageTages = new PostTags();
								$ImageTages->setImageSize($imageheight);
								$ImageTages->setImageWidth($imageWidth);
								$ImageTages->setPostID($post->getId());
								$ImageTages->setImageType($request->get('image_type'));
								$ImageTages->setImageName($post->getPostImageLeft());
								$ImageTages->setTags($tagsImage[$i]);
								$ImageTages->setTagNote('');
								$ImageTages->setX_Axis($x_axisImage[$i]);
								$ImageTages->setY_Axis($y_axisImage[$i]);
								$em = $this->getDoctrine()->getManager();
								$em->persist($ImageTages);
								$em->flush();
							}
							echo json_encode(array('success' => 1, 'message' => 'Successfull'));
						} else {
							if ($request->get('image_type') == 'Back Left') {
								$imageBase = ($request->get('image_base'));
								if ($imageBase != '' && $imageBase != null) {
									$Image = $this->UploadFile($image, $imageBase);
								}
								$post->setPostImageBackLeft($Image);
								$em = $this->getDoctrine()->getManager();
								$em->persist($post);
								$em->flush();
								if (count($tagsImage) > 0 && $tagsImage[0] != '') {
									$tagImages = count($tagsImage);
								} else {
									$tagImages = count($request->get('image_type'));
								}

								for ($i = 0; $i < $tagImages; $i++) {
									$ImageTages = new PostTags();
									$ImageTages->setImageSize($imageheight);
									$ImageTages->setImageWidth($imageWidth);
									$ImageTages->setPostID($post->getId());
									$ImageTages->setImageType($request->get('image_type'));
									$ImageTages->setImageName($post->getPostImageBackLeft());
									$ImageTages->setTags($tagsImage[$i]);
									$ImageTages->setTagNote('');
									$ImageTages->setX_Axis($x_axisImage[$i]);
									$ImageTages->setY_Axis($y_axisImage[$i]);
									$em = $this->getDoctrine()->getManager();
									$em->persist($ImageTages);
									$em->flush();
								}
								echo json_encode(array('success' => 1, 'message' => 'Successfull'));
							} else {
								if ($request->get('image_type') == 'Back') {
									if ($imageBase != '' && $imageBase != null) {
										$Image = $this->UploadFile($image, $imageBase);
									}
									$post->setPostImageBack($Image);
									$em = $this->getDoctrine()->getManager();
									$em->persist($post);
									$em->flush();
									if (count($tagsImage) > 0 && $tagsImage[0] != '') {
										$tagImages = count($tagsImage);
									} else {
										$tagImages = count($request->get('image_type'));
									}

									for ($i = 0; $i < $tagImages; $i++) {
										$ImageTages = new PostTags();
										$ImageTages->setImageSize($imageheight);
										$ImageTages->setImageWidth($imageWidth);
										$ImageTages->setPostID($post->getId());
										$ImageTages->setImageType($request->get('image_type'));
										$ImageTages->setImageName($post->getPostImageBack());
										$ImageTages->setTags($tagsImage[$i]);
										$ImageTages->setTagNote('');
										$ImageTages->setX_Axis($x_axisImage[$i]);
										$ImageTages->setY_Axis($y_axisImage[$i]);
										$em = $this->getDoctrine()->getManager();
										$em->persist($ImageTages);
										$em->flush();
									}
									echo json_encode(array('success' => 1, 'message' => 'Successfull'));
								} else {
									if ($request->get('image_type') == 'Back Right') {
										if ($imageBase != '' && $imageBase != null) {
											$Image = $this->UploadFile($image, $imageBase);
										}
										$post->setPostImageBackRight($Image);
										$em = $this->getDoctrine()->getManager();
										$em->persist($post);
										$em->flush();
										if (count($tagsImage) > 0 && $tagsImage[0] != '') {
											$tagImages = count($tagsImage);
										} else {
											$tagImages = count($request->get('image_type'));
										}

										for ($i = 0; $i < $tagImages; $i++) {
											$ImageTages = new PostTags();
											$ImageTages->setImageSize($imageheight);
											$ImageTages->setImageWidth($imageWidth);
											$ImageTages->setPostID($post->getId());
											$ImageTages->setImageType($request->get('image_type'));
											$ImageTages->setImageName($post->getPostImageBackRight());
											$ImageTages->setTags($tagsImage[$i]);
											$ImageTages->setTagNote('');
											$ImageTages->setX_Axis($x_axisImage[$i]);
											$ImageTages->setY_Axis($y_axisImage[$i]);
											$em = $this->getDoctrine()->getManager();
											$em->persist($ImageTages);
											$em->flush();
										}
										echo json_encode(array('success' => 1, 'message' => 'Successfull'));
									} else {
										if ($request->get('image_type') == 'Right') {
											if ($imageBase != '' && $imageBase != null) {
												$Image = $this->UploadFile($image, $imageBase);
											}
											$post->setPostImageRight($Image);
											$em = $this->getDoctrine()->getManager();
											$em->persist($post);
											$em->flush();

											if (count($tagsImage) > 0 && $tagsImage[0] != '') {
												$tagImages = count($tagsImage);
											} else {
												$tagImages = count($request->get('image_type'));
											}

											for ($i = 0; $i < $tagImages; $i++) {
												$ImageTages = new PostTags();
												$ImageTages->setImageSize($imageheight);
												$ImageTages->setImageWidth($imageWidth);
												$ImageTages->setPostID($post->getId());
												$ImageTages->setImageType($request->get('image_type'));
												$ImageTages->setImageName($post->getPostImageRight());
												$ImageTages->setTags($tagsImage[$i]);
												$ImageTages->setTagNote('');
												$ImageTages->setX_Axis($x_axisImage[$i]);
												$ImageTages->setY_Axis($y_axisImage[$i]);
												$em = $this->getDoctrine()->getManager();
												$em->persist($ImageTages);
												$em->flush();
											}
											echo json_encode(array('success' => 1, 'message' => 'Successfull'));
										} else {
											if ($request->get('image_type') == 'Front Right') {
												$imageBase = ($request->get('image_base'));
												if ($imageBase != '' && $imageBase != null) {
													$Image = $this->UploadFile($image, $imageBase);
												}
												$post->setPostImageFrontRight($Image);
												$em = $this->getDoctrine()->getManager();
												$em->persist($post);
												$em->flush();
												if (count($tagsImage) > 0 && $tagsImage[0] != '') {
													$tagImages = count($tagsImage);
												} else {
													$tagImages = count($request->get('image_type'));
												}

												for ($i = 0; $i < $tagImages; $i++) {
													$ImageTages = new PostTags();
													$ImageTages->setImageSize($imageheight);
													$ImageTages->setImageWidth($imageWidth);
													$ImageTages->setPostID($post->getId());
													$ImageTages->setImageType($request->get('image_type'));
													$ImageTages->setImageName($post->getPostImageFrontRight());
													$ImageTages->setTags($tagsImage[$i]);
													$ImageTages->setTagNote('');
													$ImageTages->setX_Axis($x_axisImage[$i]);
													$ImageTages->setY_Axis($y_axisImage[$i]);
													$em = $this->getDoctrine()->getManager();
													$em->persist($ImageTages);
													$em->flush();
												}
												echo json_encode(array('success' => 1, 'message' => 'Successfull'));
											} else {
												echo json_encode(array('success' => 0, 'message' => 'failure'));
											}
										}
									}
								}
							}
						}
					}
				}
			} else {
				foreach ($postTag as $postTagValue) {

					$imageName = $postTagValue->getImageName();
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTagValue);
					$em->flush();
				}
				// echo '<pre>';print_R($imageName);die;
				$tags = explode(',', $tag);
				foreach ($tags as $tagsVal) {
					$tagsImage[] = $tagsVal;
				}

				$x_axis = explode(',', $xaxis);
				foreach ($x_axis as $x_axisVal) {
					$x_axisImage[] = $x_axisVal;
				}
				$y_axis = explode(',', $yaxis);
				foreach ($y_axis as $y_axisVal) {
					$y_axisImage[] = $y_axisVal;
				}

//                $notes = explode(',', $notes);
//                foreach ($notes as $notesVal) {
//                    $noteImage[] = $notesVal;
//                }
				if ($request->get('image_type') == 'Front') {
					$imageBase = ($request->get('image_base'));

					if (count($tagsImage) > 0 && $tagsImage[0] != '') {
						$tagImages = count($tagsImage);
					} else {
						$tagImages = count($request->get('image_type'));
					}
					//echo '<pre>';print_r($tagImages);die;
					for ($i = 0; $i < $tagImages; $i++) {
						$ImageTages = new PostTags();
						$ImageTages->setImageSize($imageheight);
						$ImageTages->setImageWidth($imageWidth);
						$ImageTages->setPostID($albumID);
						$ImageTages->setImageType($request->get('image_type'));
						$ImageTages->setImageName($imageName);
						$ImageTages->setTags($tagsImage[$i]);
						$ImageTages->setTagNote('');
						$ImageTages->setX_Axis($x_axisImage[$i]);
						$ImageTages->setY_Axis($y_axisImage[$i]);
						$em = $this->getDoctrine()->getManager();
						$em->persist($ImageTages);
						$em->flush();
					}
					echo json_encode(array('success' => 1, 'message' => 'Successfull'));
				} else {
					if ($request->get('image_type') == 'Front Left') {


						if (count($tagsImage) > 0 && $tagsImage[0] != '') {
							$tagImages = count($tagsImage);
						} else {
							$tagImages = count($request->get('image_type'));
						}
						//echo '<pre>';print_r($tagImages);die;
						for ($i = 0; $i < $tagImages; $i++) {
							$ImageTages = new PostTags();
							$ImageTages->setImageSize($imageheight);
							$ImageTages->setImageWidth($imageWidth);
							$ImageTages->setPostID($albumID);
							$ImageTages->setImageType($request->get('image_type'));
							$ImageTages->setImageName($imageName);
							$ImageTages->setTags($tagsImage[$i]);
							$ImageTages->setTagNote('');
							$ImageTages->setX_Axis($x_axisImage[$i]);
							$ImageTages->setY_Axis($y_axisImage[$i]);
							$em = $this->getDoctrine()->getManager();
							$em->persist($ImageTages);
							$em->flush();
						}
						echo json_encode(array('success' => 1, 'message' => 'Successfull'));
					} else {
						if ($request->get('image_type') == 'Left') {
							$imageBase = ($request->get('image_base'));

							if (count($tagsImage) > 0 && $tagsImage[0] != '') {
								$tagImages = count($tagsImage);
							} else {
								$tagImages = count($request->get('image_type'));
							}
							//echo '<pre>';print_r($tagImages);die;
							for ($i = 0; $i < $tagImages; $i++) {
								$ImageTages = new PostTags();
								$ImageTages->setImageSize($imageheight);
								$ImageTages->setImageWidth($imageWidth);
								$ImageTages->setPostID($albumID);
								$ImageTages->setImageType($request->get('image_type'));
								$ImageTages->setImageName($imageName);
								$ImageTages->setTags($tagsImage[$i]);
								$ImageTages->setTagNote('');
								$ImageTages->setX_Axis($x_axisImage[$i]);
								$ImageTages->setY_Axis($y_axisImage[$i]);
								$em = $this->getDoctrine()->getManager();
								$em->persist($ImageTages);
								$em->flush();
							}
							echo json_encode(array('success' => 1, 'message' => 'Successfull'));
						} else {
							if ($request->get('image_type') == 'Back Left') {

								if (count($tagsImage) > 0 && $tagsImage[0] != '') {
									$tagImages = count($tagsImage);
								} else {
									$tagImages = count($request->get('image_type'));
								}
								//echo '<pre>';print_r($tagImages);die;
								for ($i = 0; $i < $tagImages; $i++) {
									$ImageTages = new PostTags();
									$ImageTages->setImageSize($imageheight);
									$ImageTages->setImageWidth($imageWidth);
									$ImageTages->setPostID($albumID);
									$ImageTages->setImageType($request->get('image_type'));
									$ImageTages->setImageName($imageName);
									$ImageTages->setTags($tagsImage[$i]);
									$ImageTages->setTagNote('');
									$ImageTages->setX_Axis($x_axisImage[$i]);
									$ImageTages->setY_Axis($y_axisImage[$i]);
									$em = $this->getDoctrine()->getManager();
									$em->persist($ImageTages);
									$em->flush();
								}
								echo json_encode(array('success' => 1, 'message' => 'Successfull'));
							} else {
								if ($request->get('image_type') == 'Back') {

									if (count($tagsImage) > 0 && $tagsImage[0] != '') {
										$tagImages = count($tagsImage);
									} else {
										$tagImages = count($request->get('image_type'));
									}
									//echo '<pre>';print_r($tagImages);die;
									for ($i = 0; $i < $tagImages; $i++) {
										$ImageTages = new PostTags();
										$ImageTages->setImageSize($imageheight);
										$ImageTages->setImageWidth($imageWidth);
										$ImageTages->setPostID($albumID);
										$ImageTages->setImageType($request->get('image_type'));
										$ImageTages->setImageName($imageName);
										$ImageTages->setTags($tagsImage[$i]);
										$ImageTages->setTagNote('');
										$ImageTages->setX_Axis($x_axisImage[$i]);
										$ImageTages->setY_Axis($y_axisImage[$i]);
										$em = $this->getDoctrine()->getManager();
										$em->persist($ImageTages);
										$em->flush();
									}
									echo json_encode(array('success' => 1, 'message' => 'Successfull'));
								} else {
									if ($request->get('image_type') == 'Back Right') {

										if (count($tagsImage) > 0 && $tagsImage[0] != '') {
											$tagImages = count($tagsImage);
										} else {
											$tagImages = count($request->get('image_type'));
										}
										//echo '<pre>';print_r($tagImages);die;
										for ($i = 0; $i < $tagImages; $i++) {
											$ImageTages = new PostTags();
											$ImageTages->setImageSize($imageheight);
											$ImageTages->setImageWidth($imageWidth);
											$ImageTages->setPostID($albumID);
											$ImageTages->setImageType($request->get('image_type'));
											$ImageTages->setImageName($imageName);
											$ImageTages->setTags($tagsImage[$i]);
											$ImageTages->setTagNote('');
											$ImageTages->setX_Axis($x_axisImage[$i]);
											$ImageTages->setY_Axis($y_axisImage[$i]);
											$em = $this->getDoctrine()->getManager();
											$em->persist($ImageTages);
											$em->flush();
										}
										echo json_encode(array('success' => 1, 'message' => 'Successfull'));
									} else {
										if ($request->get('image_type') == 'Right') {

											if (count($tagsImage) > 0 && $tagsImage[0] != '') {
												$tagImages = count($tagsImage);
											} else {
												$tagImages = count($request->get('image_type'));
											}
											//echo '<pre>';print_r($tagImages);die;
											for ($i = 0; $i < $tagImages; $i++) {
												$ImageTages = new PostTags();
												$ImageTages->setImageSize($imageheight);
												$ImageTages->setImageWidth($imageWidth);
												$ImageTages->setPostID($albumID);
												$ImageTages->setImageType($request->get('image_type'));
												$ImageTages->setImageName($imageName);
												$ImageTages->setTags($tagsImage[$i]);
												$ImageTages->setTagNote('');
												$ImageTages->setX_Axis($x_axisImage[$i]);
												$ImageTages->setY_Axis($y_axisImage[$i]);
												$em = $this->getDoctrine()->getManager();
												$em->persist($ImageTages);
												$em->flush();
											}
											echo json_encode(array('success' => 1, 'message' => 'Successfull'));
										} else {
											if ($request->get('image_type') == 'Front Right') {

												if (count($tagsImage) > 0 && $tagsImage[0] != '') {
													$tagImages = count($tagsImage);
												} else {
													$tagImages = count($request->get('image_type'));
												}
												//echo '<pre>';print_r($tagImages);die;
												for ($i = 0; $i < $tagImages; $i++) {
													$ImageTages = new PostTags();
													$ImageTages->setImageSize($imageheight);
													$ImageTages->setImageWidth($imageWidth);
													$ImageTages->setPostID($albumID);
													$ImageTages->setImageType($request->get('image_type'));
													$ImageTages->setImageName($imageName);
													$ImageTages->setTags($tagsImage[$i]);
													$ImageTages->setTagNote('');
													$ImageTages->setX_Axis($x_axisImage[$i]);
													$ImageTages->setY_Axis($y_axisImage[$i]);
													$em = $this->getDoctrine()->getManager();
													$em->persist($ImageTages);
													$em->flush();
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************EDIT ALBUM END********************************************* */

	/**
	 * @Route("/systeminfo", name="_systeminfo")
	 * @Template()
	 */
	/*     * *************************************************Login Begin************************************************* */
	public function systeminfoAction(
		Request $machineName,
		Request $machineAddress,
		Request $IPAddress,
		Request $userName,
		Request $timeZone,
		Request $Country
	) {
		$request = $this->getRequest();


		$SystemData = $this->getDoctrine()->getRepository("AcmeDemoBundle:System")->findOneBy(
			['machineAddress' => $request->get('machineAddress')]
		);
		if (empty($SystemData) && !isset($SystemData)) {
			$systemVal = new System();
			$systemVal->setMachineName($request->get('machineName'));
			$systemVal->setMachineAddress($request->get('machineAddress'));
			$systemVal->setIPAddress($request->get('IPAddress'));
			$systemVal->setUserName($request->get('userName'));
			$systemVal->setTimeZone($request->get('timeZone'));
			$systemVal->setCountry($request->get('Country'));
			$systemVal->setStatus('0');
			$em = $this->getDoctrine()->getManager();
			$em->persist($systemVal);
			$em->flush();

			echo json_encode(array("success" => 1, "message" => "successful", 'status' => $systemVal->getStatus()));

			return array();
		} else {
			$SystemData->setMachineName($request->get('machineName'));
			$SystemData->setMachineAddress($request->get('machineAddress'));
			$SystemData->setIPAddress($request->get('IPAddress'));
			$SystemData->setUserName($request->get('userName'));
			$SystemData->setTimeZone($request->get('timeZone'));
			$SystemData->setCountry($request->get('Country'));
			//$systemVal->setStatus($request->get('Country'));
			$em = $this->getDoctrine()->getManager();
			$em->persist($SystemData);
			$em->flush();
			echo json_encode(array("success" => 1, "message" => "update successfully"));

			return array();
		}
	}

	/*     * **************************************Login End**************************************************** */

	/**
	 * @Route("/domain", name="_domain")
	 * @Template()
	 */
	/*     * *************************************************Login Begin************************************************* */
	public function domainAction()
	{
		$request = $this->getRequest();


		$System = $this->getDoctrine()->getRepository("AcmeDemoBundle:Domain")->findBy(['status' => '1']);
		if (!empty($System) && isset($System)) {
			foreach ($System as $Systemval) {
				$domainName = $Systemval->getDomainName();
				$status = $Systemval->getStatus();
				$domaininfo[] = array('domain_name' => $domainName, 'status' => $status);
			}
			echo json_encode(array("success" => 1, "message" => "successful", 'domaininfo' => $domaininfo));

			return array();
		} else {
			echo json_encode(array("success" => 0, "message" => "failure"));

			return array();
		}
	}

	/*     * **************************************Login End**************************************************** */
	/*     * **********************************************DELETE IMAGE******************************************** */

	/**
	 * @Route("/deleteimage", name="_deleteimage")
	 * @Template()
	 */
	public function deleteimageAction(Request $user_id, Request $album_id, Request $image_type)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');

		$post = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
			['id' => $request->get('album_id')]
		);
		if ($post != '' && $post != null) {

			if ($request->get('image_type') == 'Front') {

				$post->setPostImageFront('');
				// echo '<pre>';print_r($post->setPostImageFront);die;
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Front']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Front Left') {
				$post->setPostImageFrontLeft('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Front']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Left') {
				$post->setPostImageLeft('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Left']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Back Left') {
				$post->setPostImageBackLeft('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Back Left']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Back') {
				$post->setPostImageBack('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Back']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Back Right') {
				$post->setPostImageBackRight('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Back Right']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Right') {
				$post->setPostImageRight('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Right']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			} elseif ($request->get('image_type') == 'Front Right') {
				$post->setPostImageFrontRight('');
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();

				$postTag = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findOneBy(
					['postID' => $request->get('album_id'), 'imageType' => 'Front Right']
				);
				if ($postTag != '' && $postTag != null) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTag);
					$em->flush();
				}
			}
			echo json_encode(array('success' => 1, 'message' => 'successfully deleted'));

			return array();
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure'));

			return array();
		}
	}

	/*     * ************************************************************************DELETE IMAGE END ******************************************* */

	/**
	 * @Route("/deletepictureset", name="_deletepictureset")
	 * @Template()
	 */
	/*     * ************************************************************************album status Begin ******************************************* */
	public function deletepicturesetAction(Request $user_id, Request $album_id)
	{
		$request = $this->getRequest();
		$userID = $request->get('user_id');


		$Album = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
			['id' => $request->get('album_id')]
		);

		if ($Album != '' && $Album != null) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->remove($Album);
			$em->flush();
			$postTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
				['postID' => $request->get('album_id')]
			);
			if (isset($postTags) && !empty($postTags)) {
				foreach ($postTags as $postTagsVal) {
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($postTagsVal);
					$em->flush();
				}
			}
			echo json_encode(array('success' => 1, 'message' => 'post removed successfully'));
		} else {

			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************album status   End******************************************* */

	/**
	 * @Route("/userchatlist", name="_userchatlist")
	 * @Template()
	 */
	/*     * ************************************************************************album status Begin ******************************************* */
	public function userchatlistAction(Request $user_id, Request $counter)
	{
		$request = $this->getRequest();
		$manager = $this->getDoctrine()->getManager();
		$conn = $manager->getConnection();
		$userID = $request->get('user_id');
		$UserChat = $conn->query(
			"select distinct(fromUserID) from user_chat where toUserID=" . $userID . " ORDER BY userChatID desc "
		)->fetchAll();
		//$UserChat = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserChat")->findBy(['fromUserID' => $request->get('user_id')]);

		if ($UserChat != '' && $UserChat != null) {
			foreach ($UserChat as $UserChatval) {
				$fromUserID = $UserChatval['fromUserID'];

//echo '<pre>';print_r($fromUserID);die;
				$UserModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
					['id' => $fromUserID]
				);
				//echo '<pre>';print_r($UserModel);die;
				if (isset($UserModel) && !empty($UserModel)) {
					//foreach ($UserModel as $UserModelVal) {
					$userId = $UserModel->getId();
					$profile_img = $UserModel->getUserProfileImage();
					if ($profile_img == '') {
						$image = $this->baseurl() . 'defaultprofile.png';
					} else {
						$image = $this->baseurl() . $profile_img;
					}
					if (($UserModel->getUserFirstName() || $UserModel->getUserLastName()) != '' && ($UserModel->getUserFirstName() || $UserModel->getUserLastName()) != null
					) {
						$userName = $UserModel->getUserFirstName() . ' ' . $UserModel->getUserLastName();
					} else {
						$userName = '';
					}
					$Chat = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserChat")->findBy(
						['fromUserID' => $userId],
						array('id' => 'desc'),
						1,
						0
					);
					if ($Chat != '' && $Chat != null) {
						foreach ($Chat as $Chatval) {
							$chatMsg = $Chatval->getUserMessage();
						}
					} else {
						$chatMsg = '';
					}
					$userChatArr[] = array(
						'user_id' => $userId,
						'user_profile' => $image,
						'user_name' => $userName,
						'chat_msg' => $chatMsg
					);
				}
			}


			echo json_encode(array('success' => 1, 'message' => 'success', 'user_chat' => $userChatArr));
		} else {

			echo json_encode(array('success' => 0, 'message' => 'failure'));
		}
	}

	/*     * *************************************************************************album status   End******************************************* */
	/**
	 * @Route("/spalbumstatus", name="_spalbumstatus")
	 * @Template()
	 */
	/*     * ************************************************************************album status Begin ******************************************* */
	public function spalbumstatusAction(Request $user_id, Request $album_id, Request $post_status)
	{
		$request = $this->getRequest();
		$user_id = $request->get('user_id');
		$userTypeModel = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(['id' => $user_id]);
		if ($userTypeModel != '') {
			if ($userTypeModel->getUserType() == '1') {

				$AlbumStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
					['id' => $request->get('album_id')]
				);
				//echo '<pre>';print_r($AlbumStatus);die;
				if ($AlbumStatus != '' && $AlbumStatus != null) {
//            if ($AlbumStatus->getPostStatus() == 1) {
					$userID = $AlbumStatus->getUserID();
					$userTagID = $AlbumStatus->getUserTagID();
					if ($request->get('post_status') == '0') {
						// die('ok');
						$AlbumStatus->setPostStatus($request->get('post_status'));
						$AlbumStatus->setSpPostStatus('0');
						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userFOllows = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserFollow")->findOneBy(
							['userID' => $userTagID, 'toUserID' => $userID]
						);
						if ($userFOllows == '' && $userFOllows == null) {
							$USERfollow = new UserFollow();
							$USERfollow->setUserID($userTagID);
							$USERfollow->setToUserID($userID);
							$USERfollow->setFollowStatus('1');
							$em = $this->getDoctrine()->getManager();
							$em->persist($USERfollow);
							$em->flush();
						}

						/************NOtification FUNCTION START *********************/
						$UserData = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $userTagID, 'userType' => '0']
						);
						$UserData1 = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
							['id' => $userID, 'userType' => '1']
						);

						if (!empty($UserData)) {


							$userName = ucwords($UserData->getUserFirstName());
							$useremail = $UserData->getUserEmail();
							// $password = $UserData->getUserPassword();
							$userFName = ucwords($UserData1->getUserFirstName());
							$subject = 'SPPS Status Change';
							$body_text = 'SPPS status change mail from HereCut';
							$body_html = 'Hello ' . $userName . ',<br><br>' . $userFName . ' approved your Service Provider Picture Set (SPPS).<br><br>  You can Rate/Review this SPPS by logging into the HereCut App and clicking the Home Icon > My Services.  <br><br><br>Thank You <br>HereCut Team';
							$from = 'info@herecut.net';
							$fromName = 'HereCut';
							$headers = "From: " . $from . "\r\n";
							$headers .= "Reply-To: " . $from . "\r\n";
							//$headers .= "CC: test@example.com\r\n"; 
							/* $headers .= "MIME-Version: 1.0\r\n"; 
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
             mail($useremail, $subject, $body_html, $headers);
                $this->sendElasticEmail($useremail, $subject, $body_text, $body_html, $from, $fromName);*/
							$this->smtpEmail($useremail, $subject, $body_html);


							if ($UserData1->getIsNotification() == '1') {
								$msg = 'Post';
								$IDs = $UserData1->getId();
								$submsg = $UserData1->getUserFirstName() . ' ' . 'has changed status of SPPS';
								$usenotification = $this->getDoctrine()->getRepository(
									"AcmeDemoBundle:Notification"
								)->findBy(['userID' => $UserData->getId()]);

								if ($usenotification != '' && $usenotification != null) {
									foreach ($usenotification as $usenotificationVal) {

										$registatoin_ids = $usenotificationVal->getDeviceID();

										$this->send_notification($registatoin_ids, $msg, $IDs, $submsg);
									}
									$notificationModel = $this->getDoctrine()->getRepository(
										"AcmeDemoBundle:NotificationMessage"
									)->findOneBy(
										['userID' => $userID, 'toUserID' => $userTagID, 'notificationTitle' => 'Post']
									);

									if ($notificationModel == '' && $notificationModel == null) {

										$notifyMsg = new NotificationMessage();
										$notifyMsg->setNotificationTitle($msg);
										$notifyMsg->setNotificationMessage($submsg);
										$notifyMsg->setUserID($IDs);
										$notifyMsg->setToUserID($UserData->getId());
										$em = $this->getDoctrine()->getManager();
										$em->persist($notifyMsg);
										$em->flush();
									} else {
										$em = $this->getDoctrine()->getEntityManager();
										$em->remove($notificationModel);
										$em->flush();
										$notifyMsg = new NotificationMessage();
										$notifyMsg->setNotificationTitle($msg);
										$notifyMsg->setNotificationMessage($submsg);
										$notifyMsg->setUserID($IDs);
										$notifyMsg->setToUserID($UserData->getId());
										$em = $this->getDoctrine()->getManager();
										$em->persist($notifyMsg);
										$em->flush();
									}
								}
							}
						}
						/*******************************NOTIFICATION END ************/
						$usercustomerRelation = $this->getDoctrine()->getRepository(
							"AcmeDemoBundle:UserCustomerRelation"
						)->findOneBy(['userID' => $userTagID, 'companyID' => $userID]);
// echo '<pre>';print_r($usercustomerRelation->getUserID());die;
						if ($usercustomerRelation == '') {
							$userdata = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
								['id' => $userID]
							);

							$users = new UserCustomerRelation();
							if ($userdata->getUserType() == '0') {
								$users->setUserID($userID);
								$users->setCompanyID($userTagID);
							} else {
								$users->setUserID($userTagID);
								$users->setCompanyID($userID);
							}
							$em = $this->getDoctrine()->getManager();
							$em->persist($users);
							$em->flush();
						}
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status public',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					} elseif ($request->get('post_status') == '1') {
						// die('ok');
						$AlbumStatus->setPostStatus($request->get('post_status'));

						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();

						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status private',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					}
				} else {

					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			} else {
				$AlbumStatus = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findOneBy(
					['id' => $request->get('album_id')]
				);

				if ($AlbumStatus != '' && $AlbumStatus != null) {
//            if ($AlbumStatus->getPostStatus() == 1) {
					if ($request->get('post_status') == '0') {
						$AlbumStatus->setPostStatus($request->get('post_status'));
						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status public',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					} elseif ($request->get('post_status') == '1') {

						$AlbumStatus->setPostStatus($request->get('post_status'));
						$em = $this->getDoctrine()->getManager();
						$em->persist($AlbumStatus);
						$em->flush();
						$userRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
							['postID' => $request->get('album_id')]
						);
						if ($userRate != '' && $userRate != null) {
							if ($userRate->getUserReviews() != '' && $userRate->getUserReviews() != null) {
								$userReview = 'true';
							} else {
								$userReview = 'false';
							}
							if ($userRate->getUserRating() != '' && $userRate->getUserRating() != null) {
								$userRate = 'true';
							} else {
								$userRate = 'false';
							}
						} else {
							$userRate = 'false';
							$userReview = 'false';
						}
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'album status private',
								'review_status' => $userReview,
								'rate_status' => $userRate
							)
						);
					}
				} else {

					echo json_encode(array('success' => 0, 'message' => 'failure'));
				}
			}
		}
	}

	/*     * *************************************************************************album status   End******************************************* */
	/**
	 * @Route("/custuseralbum", name="_custuseralbum")
	 * @Template()
	 */
	/*     * ************************************************************************user album Begin ******************************************* */
	public function custuseralbumAction(Request $user_id)
	{
		$request = $this->getRequest();
		$flag = '';
		$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
			['userID' => $request->get('user_id')],
			array('id' => 'desc')
		);
		//  echo '<pre>';print_r($Consumer);die;
		if ($Consumer != '' && $Consumer != null) {
			foreach ($Consumer as $Consumer1val) {
				if ($Consumer1val->getUserID() != $Consumer1val->getUserTagID()) {


					$ConsumerRate = $this->getDoctrine()->getRepository("AcmeDemoBundle:UserRating")->findOneBy(
						['postID' => $Consumer1val->getId()]
					);

					if (!empty($ConsumerRate)) {


						$flag = 1;
					} else {
						$albums[] = $Consumer1val->getId();
						$flag = 2;

					}
				}
			}
		}


		if ($flag == 2) {
			$Consumer = $this->getDoctrine()->getRepository("AcmeDemoBundle:AlbumPost")->findBy(
				['id' => $albums],
				array('id' => 'desc')
			);
			if ($Consumer != '' && $Consumer != null) {
				foreach ($Consumer as $ConsumerVal) {
					$sp_user_id = $ConsumerVal->getUserID();
					if ($ConsumerVal->getId() != '') {
						$AlbumID = $ConsumerVal->getId();
					} else {
						$AlbumID = '';
					}
					if ($ConsumerVal->getPostImageFront() != '' && $ConsumerVal->getPostImageFront() != null) {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageFront();
					} elseif ($ConsumerVal->getPostImageFrontLeft() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageFrontLeft();
					} elseif ($ConsumerVal->getPostImageLeft() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageLeft();
					} elseif ($ConsumerVal->getPostImageBackLeft() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageBackLeft();
					} elseif ($ConsumerVal->getPostImageBack() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageBack();
					} elseif ($ConsumerVal->getPostImageBackRight() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageBackRight();
					} elseif ($ConsumerVal->getPostImageRight() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageRight();
					} elseif ($ConsumerVal->getPostImageFrontRight() != '') {
						$userImage = $this->baseurl() . $ConsumerVal->getPostImageFrontRight();
					} else {
						$userImage = '';
					}
					$PostTags = $this->getDoctrine()->getRepository("AcmeDemoBundle:PostTags")->findBy(
						['postID' => $AlbumID]
					);
//                           echo '<pre>';print_r($PostTags);
//                           echo 'hello';
					if ($PostTags != '') {
						$tag_status = '0';
						foreach ($PostTags as $PostTagsVal) {
							if ($PostTagsVal->getTags() != '') {
								$tag_status = '1';
							}
						}
					} else {
						$tag_status = '0';
					}
					if ($ConsumerVal->getPostCaption() != '') {
						$postCaption = $ConsumerVal->getPostCaption();
					} else {
						$postCaption = '';
					}
					if ($ConsumerVal->getPostNote() != '') {
						$postNote = $ConsumerVal->getPostNote();
					} else {
						$postNote = '';
					}
					$spuser = $this->getDoctrine()->getRepository("AcmeDemoBundle:User")->findOneBy(
						['id' => $sp_user_id]
					);
					if (!empty($spuser->getCompanyName()) && ($spuser->getCompanyName() != null)) {
						$companyname = $spuser->getCompanyName();
					} else {
						$companyname = '';
					}
					$albums1[] = array(
						'album_id' => $AlbumID,
						'album_image' => $userImage,
						'post_caption' => $postCaption,
						'tag_status' => $tag_status,
						'post_note' => $postNote,
						'company_name' => $companyname,
						'sp_user_id' => $sp_user_id
					);
				}


				echo json_encode(array('success' => 1, 'message' => 'Successfull', 'album_details' => $albums1));
			} else {
				echo json_encode(array('success' => 0, 'message' => 'failure', 'album_details' => []));
			}
		} else {
			echo json_encode(array('success' => 0, 'message' => 'failure', 'album_details' => []));
		}
	}

	/*     * *************************************************************************user album   End******************************************* */


}
