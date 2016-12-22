<?php

header( 'Content-Type: application/json' );
header( 'Pragma: no-cache' );
header( 'Cache-Control: no-cache, no-store, max-age=0' );
header( 'Expires: 1L' );

$arr = [
	"status" => 1,
	"response" =>[
		"msg" => "test",
		"test" => 1
	]
];

echo json_encode($arr);