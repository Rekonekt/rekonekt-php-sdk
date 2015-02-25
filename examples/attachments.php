<?php
include __DIR__ . '/../vendor/autoload.php';

header("Content-Type:text/plain");

$api = new \Rekonekt\RekonektApi;

$api->setApiKey('...');

$testUploadFile = realpath(__DIR__ . '/../README.md');

$created = $api->createAttachment('readme.md', filesize($testUploadFile));

print_r($created);

$uploaded = $api->uploadAttachment(
	$created['attachment']['attachmentId'],
	$testUploadFile,
	'text/plain',
	'readme.md'
);

print_r($uploaded);