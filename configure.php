<?php
	// enter your DB_SERVER DB_USER DB_PASSWORD & DB_DATABASE here
	define('DB_SERVER', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASSWORD', 'test');
	define('DB_DATABASE', 'database_operations');
	define('DB_DOCKER_RESOURCE', 'db');

	//connect to mysql database (This doesnt work with my version of either php or mariadb)
    // $connect = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	$connect = mysqli_connect(DB_DOCKER_RESOURCE, DB_USER, DB_PASSWORD, DB_DATABASE);

	if(mysqli_connect_error()){
		echo DB_DATABASE . " database connection failed.<br>";
        die('Connect Error ('.mysqli_connect_errno().') '. mysqli_connect_error());
    }
    else{
        echo DB_DATABASE . " database connection successful!<br>";
    }
