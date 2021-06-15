<?php
/*
Plugin Name: JES Eventbrite CSV To Users
Plugin URI:  
Description: Add a file called "new_users.csv" to the "csv" folder in this plugins directory and then, while logged in as an administrator, visit "https://SITE_URL/eventbrite_csv_to_users to trigger the function that will create all the users listed in the Eventbrite generated CSV file. The file gets renamed when the functions are complete to avoid any issues that could be caused with running the same CSV twice.
Version:     1.0
Author:      Jesse Sugden
License:     GPL2 etc
*/

function custom_url_handler() {
	// config for group user creation - all users with same email
	$group_ticket = false;
	$logs = '';

	if ($_SERVER["REQUEST_URI"] == 'eventbrite_csv_to_users') {
		if (current_user_can('administrator')) {	// if is admin
			$array = $fields = array();
			$i = 0;
			$handle = @fopen(plugin_dir_path(__FILE__) . 'csv/new_users.csv', 'r'); // open the file
			if ($handle) {
				while (($row = fgetcsv($handle, 4096)) !== false) { // create associative array from csv
					if (empty($fields)) {
						$fields = $row;
						continue;
					}
					foreach ($row as $k => $value) {
						$array[$i][$fields[$k]] = $value;
					}
					$i++;
				}
				if (!feof($handle)) {
					echo "Error: unexpected fgets() fail\n";
				}
				fclose($handle);
			} else {
				echo "Error: The file cannot be found. Add a file called 'new_users.csv' to the 'csv' folder of this plugin.";
			}
			// now create the users!
			if (sizeof($array) > 0) {
				$i = 0;
				foreach ($array as $item) {
					$i++;
					$password = wp_generate_password(25, false);

					if ($group_ticket == false) {
						$user_login = username_exists($item['Email']) ? $item['Email'] . $i : $item['Email'];
					} elseif ($group_ticket == true) {
						$user_login = $item['Username'];
					}

					$userdata = array(
						'user_login' 						=> $user_login,
						'display_name'          => $item['First Name'] . ' ' . $item['Last Name'],
						'first_name'            => $item['First Name'],
						'last_name'             => $item['Last Name'],
						'user_email'            => $user_login,
						'user_pass'  						=> $password,
					);

					$user_id = wp_insert_user($userdata);

					// On success.
					if (!is_wp_error($user_id)) {
						// set role to user
						wp_update_user(array('ID' => $user_id, 'role' => 'um_user'));
						// update acf field associated with ticket type
						update_field('register_plan', $item['Ticket Type'], 'user_' . $user_id);
						if ($group_ticket == false) {
							// output confirmation
							echo "index " . $i . " - User created : " . $user_id . " - " . $user_login . " - " . $password . "<br>";
							$logs .= "index " . $i . " - User created : " . $user_id . " - " . $user_login . " - " . $password . "\r\n";
							$to = $item['Email'];
							$subject = '[Asia Blockchain Summit 2020] Login Details';
							$body = '';
							if ($item['Ticket Type'] == 'moon_pass' || $item['Ticket Type'] == 'mars_pass') {
								$body = "
									Hi " . $item['First Name'] . ", <br><br>
									Username: " . $user_login . " <br><br>
									Password: " . $password . " <br><br>
									To login to our site, visit the following address: <br><br>"
									. get_home_url() . "/sign-in <br><br><br>
									Brella details: <br><br>
									Sign up here: https://next.brella.io/join/ABS2020 <br><br>
									Join code: ABS2020 <br><br><br>
									Feel free to write to us your thoughts on the ABS2020 platform at tickets@abasummit.io :)
								";
							} else {
								$body = "
									Hi " . $item['First Name'] . ", <br><br>
									Username: " . $user_login . " <br><br>
									Password: " . $password . " <br><br>
									To login to our site, visit the following address: <br><br>"
									. get_home_url() . "/sign-in <br><br><br>
									Feel free to write to us your thoughts on ABS2020 platform :)
								";
							}

							$headers = array('Content-Type: text/html; charset=UTF-8');
							wp_mail($to, $subject, $body, $headers);
							// sleep to prevent overload of smtp server
							// usleep(90000);
						} elseif ($group_ticket == true) {
							echo "Username: " . $user_login . " <br><br>";
							echo "Password: " . $password . " <br><br><br>";
							$logs .= "Username: " . $user_login . "\r\n Password: " . $password . " \r\n";
						}
					} else {
						echo $user_login . " Error: " . array_key_first($user_id->errors) . "<br><br>" . $user_id->errors[$error_code][0];
						$logs .= $user_login . " Error: " . array_key_first($user_id->errors) . "\r\n" . $user_id->errors[$error_code][0];
					}
				}
			}

			$fp = fopen(plugin_dir_path(__FILE__) . 'logs.txt', 'a'); //opens file in append mode
			fwrite($fp, $logs);
			fclose($fp);

			$date = new DateTime();
			@rename(plugin_dir_path(__FILE__) . 'csv/new_users.csv', plugin_dir_path(__FILE__) . 'csv/x_new_users-' . $date->getTimestamp() . '.csv');
			exit();
		} else {
			echo 'This page is allowed for admins only.';
		}
	}
}

add_action('parse_request', 'custom_url_handler');


// function custom_url_handler() {

// 	if($_SERVER["REQUEST_URI"] == '/artkai-abs/eventbrite_csv_to_users') { 
// 		if(current_user_can('administrator')) {	// if is admin
				
// 			$i = 0;
// 			// 1471
// 			for($user_id = 1301; $user_id <= 1471; $user_id++) {
// 				$i++;
// 				$user = get_user_by( 'id', $user_id ); 
// 				$password = wp_generate_password( 25, true );
// 				wp_set_password( $password, $user_id );
			
// 				echo "index " . $i . " - User updated : ". $user_id . " - " . $user->first_name . " - " . $user->user_login . " - " . $password . "<br>";

// 				$to = $user->user_email;
// 				$subject = '[Asia Blockchain Summit 2020] Login Details';
// 				$body = "
// 					Hi " . $user->first_name . ", <br><br>
// 					Username: " . $user->user_login . " <br><br>
// 					Password: " . $password . " <br><br>
// 					To login to our beta site, visit the following address: <br><br>"
// 					. get_home_url() . "/sign-in <br><br>
// 					Feel free to write to us your thoughts on ABS2020 BETA platform :)
// 				";
// 				$headers = array('Content-Type: text/html; charset=UTF-8');
// 				wp_mail( $to, $subject, $body, $headers );
//         usleep(250000);
// 			}
	 
// 			exit();

// 		} else {
// 			echo 'This page is allowed for admins only.';
// 		}
// 	}
// }
