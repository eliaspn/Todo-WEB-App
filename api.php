<?php
require 'common.php';

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/')); 
$input = json_decode(file_get_contents('php://input'),true);

// connect to the mysql database

$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
mysqli_set_charset($link,'utf8');
 
// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($link) {
  if ($value===null) return null;
  return mysqli_real_escape_string($link,(string)$value);
},array_values($input));

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
// build the SET part of the SQL command
    $sql = "select * from `$table`".($key?" WHERE id=$key":'');
     break;
  case 'PUT':
    $sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
   $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete `$table` where id=$key"; break;
}
 
// excecute SQL statement
$result = mysqli_query($link,$sql);
 
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
 // (mysqli_error($myConnection));
}
 
// print results, insert id or affected row count
if ($method == 'GET') {
  if (!$key) echo '[';
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
    echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
  }
  if (!$key) echo ']';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}
 
// close mysql connection
mysqli_close($link);
