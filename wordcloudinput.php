<?php
$host='localhost';
$db = 'dotamap';
$username = 'postgres';
$password = 'admin';
$dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";
try{
// create a PostgreSQL database connection
$conn = new PDO($dsn);
// display a message if connected to the PostgreSQL successfully

$region = $_GET("region");
//determine which table to use
$meme = $_GET("meme");
$role = $_GET("role");
$hero = $_GET("hero");
//combine role+hero
$meta = $_GET("meta");
//user tags, can be empty
}?>
