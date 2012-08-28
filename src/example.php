<?php
require_once 'oaipmh/OAIPMHClient.php';

// This function can be used to suppress warnings from the simplexml parser.
// The client will throw exceptions on errors anyway.
libxml_use_internal_errors(true);

// This is the base-url of the OAI-PMH handler.
$baseUrl = 'https://www.kulturarv.dk/repox/OAIHandler';

// Creating a new OAI-PMH Client, using the verbose option.
// Verbose will make the client print the requests.
$client = new \oaipmh\OAIPMHClient($baseUrl, true);

// Call the Identify method.
$identityResponse = $client->Identify();

// Print the repository name.
printf("Connected to %s", $identityResponse->Identify->repositoryName);
