<?php namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
Class Cors implements FilterInterface
{
 public function before(RequestInterface $request, $arguments = null)
 {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-Window");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "OPTIONS") {
    die();
 }
}
 public function after(RequestInterface $request, ResponseInterface $respons, $arguments = NULL)
 {
 // Do something here
 } }
