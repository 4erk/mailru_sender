<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 03.07.2018
 * Time: 12:06
 */
function response($success, array $data = []) {
	$result = [
		'success' => $success,
		'data' => $data,
	];
	echo json_encode($result);
	exit;
}