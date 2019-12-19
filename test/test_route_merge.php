<?php

$httpMethods = [
    'any'    => [],
    'get'    => ['GET', 'HEAD'],
    'post'   => ['POST'],
    'put'    => ['PUT', 'PATCH'],
    'delete' => ['DELETE'],
];

foreach ($httpMethods as $method) {
    $httpMethods['any'] = array_merge($httpMethods['any'], $method);
}

var_dump($httpMethods);
