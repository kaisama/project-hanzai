<?php

class Controller_Hanzai extends Controller
{
	public function action_police()
	{
		// Set configuration options for our pagination instance.
		$config = array(
		    'pagination_url' => 'http://hanzai.dev/police',
		    'total_items'    => 1000,
		    'per_page'       => 24,
		    'uri_segment'    => 2,
		);

		// Create a pagination instance named 'mypagination'.
		$pagination = Pagination::forge('mypagination', $config);
		

		// Request a Request_Curl object
		// Note that this only creates the object, it does not execute the request!
		$curl = Request::forge('http://data.seattle.gov/resource/7ais-f98f.json', 'curl');
		$curl->set_method('get');
		$curl->set_params(array(
			'$limit' => $pagination->per_page,
			'$offset' => $pagination->offset,
			'$order' => 'date_reported DESC'
		));

		// Execute the request.
		$curl->execute();

		// Store the response in our $result variable.
		$result = $curl->response();

		// Since the response is returned as a JSON object, we'll format it to a PHP
		// array using the format class provided by FuelPHP.
		$result = Format::forge($result->body, 'json')->to_array();

		// Create our map markers array
		$markers = array();
		foreach($result as $key => $value)
		{
			// The Police incident API returns the date_reported field in the following
			// format:
			// 2013-08-04T09:19:00
			// So let's break this down and generate a UNIX timestamp that we can work with.
			$datetime = explode("T", $value['date_reported']);
			
			$date = explode("-", $datetime[0]);
			$time = explode(":", $datetime[1]);

			$datetime = new DateTime();
			$datetime->setDate($date[0], $date[1], $date[2]);
			$datetime->setTime($time[0], $time[1], $time[2]);

			$result[$key]['date_reported'] = $datetime->format('U');

			// Add basic information to our markers array.
			$markers[] = array($value["latitude"], $value['longitude'], $value['summarized_offense_description'], $datetime->format('U'));
		}

		// Finally, we're going to assign the data we wish to pass to our template/view file
		// within a $data array.
		$data['pagination'] 	= $pagination->render();
		$data['result'] 		= $result;
		$data['markers'] 		= $markers;

		// Pass our $data array and render the view (fuel/app/views/police.php)
		return View::forge('police', $data);
	}
}
