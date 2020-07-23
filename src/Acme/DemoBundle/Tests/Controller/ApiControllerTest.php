<?php

namespace Acme\DemoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ApiControllerTest extends WebTestCase
{

	/*
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/demo/hello/Fabien');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Hello Fabien")')->count());
    }
	*/

	/*
	public function testCalendarEventCreate(){
		$client = static::createClient();

		//$client->request('GET', '/webservice/companycalendarcreate?user_id=2&title=this is a title&description=this is description&start_time=20180730&end_time=20180731');

		//$this->assertEquals(200, $client->getResponse()->getStatusCode());
$start_time = date("Ymd");
$end_time = date("Ymd");
		$client->request(
			'POST',
			'/webservice/companycalendarcreate?',
			array('calendarID' => 1,'user_id' => '852','title' => 'hereis the title update','description' => 'hereis the description update','start_time' => $start_time,'end_time' => $end_time)
		);


		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$request = $client->getRequest();

		$user_id = $request->get('user_id');
		$title = $request->get('title');
		$description = $request->get('description');
		$start_time = $request->get('start_time');
		$end_time = $request->get('end_time');

		echo  $user_id . "</br>";
		echo  $title . "</br>";
		echo  $description . "</br>";
		echo  $start_time . "</br>";
		echo  $end_time . "</br>";
	}

*/

/*
	public function testCalendarEventList(){
        $client = static::createClient();

        //$client->request('GET', '/webservice/companycalendarcreate?user_id=2&title=this is a title&description=this is description&start_time=20180730&end_time=20180731');

        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $start_date = "2018-08-10";

        $client->request(
            'POST',
            '/webservice/companycalendarappts?',
            array('user_id' => '852','start_date' => $start_date)
        );


        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $request = $client->getRequest();

        $calendar_appt_details = $request->get('calendar_appt_details');


        echo  var_dump($calendar_appt_details) . "</br>";

    }
*/

    public function testCalendarEvent(){
        $client = static::createClient();

        //$client->request('GET', '/webservice/companycalendarcreate?user_id=2&title=this is a title&description=this is description&start_time=20180730&end_time=20180731');

        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $calID = '18';

        $client->request(
            'POST',
            '/webservice/companycalendarcreate?',
            array('user_id' => '852','calendarID' => $calID)
        );


        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $request = $client->getRequest();

        $title = $request->get('title');


        echo  $title . "</br>";

    }


}
