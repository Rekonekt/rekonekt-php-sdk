<?php
include __DIR__ . '/../vendor/autoload.php';

header("Content-Type:text/plain");


$api = new \Rekonekt\RekonektApi;

// If no API key is set, login user
if(!$api->getApiKey()){
	$email = 'user@email';
	$password = 'user-password';

	$employment = $api->getEmployment($email, $password);

	print_r($employment);

	// Get first employment from list
	$firstEmployment = reset($employment['employees']);

	// Log in (set Api key in the session)
	$userData = $api->loginEmployee($email, $password, $firstEmployment['employeeId']);

	print_r($userData);
}

echo "\n\nCurrent employees API key: " . $api->getApiKey();

// Log out (clear api key form session)
$api->logoutEmployee();
