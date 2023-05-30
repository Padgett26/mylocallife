<?php

$myDirectoryUp = filter_input(INPUT_POST, 'myDirectoryUp', FILTER_SANITIZE_NUMBER_INT);
$firstName = trim(filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING));
$lastName = trim(filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING));
$businessName = trim(filter_input(INPUT_POST, 'businessName', FILTER_SANITIZE_STRING));
$phone1 = trim(filter_input(INPUT_POST, 'phone1', FILTER_SANITIZE_STRING));
$phone1Desc = filter_input(INPUT_POST, 'phone1Desc', FILTER_SANITIZE_STRING);
$phone2 = trim(filter_input(INPUT_POST, 'phone2', FILTER_SANITIZE_STRING));
$phone2Desc = filter_input(INPUT_POST, 'phone2Desc', FILTER_SANITIZE_STRING);
$email = trim(strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
$address1 = trim(filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING));
$address2 = trim(filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING));
$showListing = (filter_input(INPUT_POST, 'showListing', FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";

$stmt = $db->prepare("UPDATE directory SET firstName=?, lastName=?, businessName=?, phone1=?, phone1Desc=?, phone2=?, phone2Desc=?, email=?, address1=?, address2=?, showListing=? WHERE userId=?");
$stmt->execute(array($firstName, $lastName, $businessName, $phone1, $phone1Desc, $phone2, $phone2Desc, $email, $address1, $address2, $showListing, $myDirectoryUp));
