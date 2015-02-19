<?php
include __DIR__ . '/../vendor/autoload.php';

$api = new \Rekonekt\RekonektApi;

// If no API key is set, login user
if(!$api->getApiKey()){
	$email = 'user@email';
	$password = 'user-password';

	$employment = $api->getEmployment($email, $password);

	echo "<pre>" . var_export($employment, true) . "</pre>";

	// Get first employment from list
	$firstEmployment = reset($employment['employees']);

	// Log in (set Api key in the session)
	$userData = $api->loginEmployee($email, $password, $firstEmployment['employeeId']);

	echo "<pre>" . var_export($userData, true) . "</pre>";
}

echo 'Current employees API key: ' . $api->getApiKey();

// Log out (clear api key form session)
$api->logoutEmployee();
