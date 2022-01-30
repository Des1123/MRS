<?php
	if (session_status() == 1) 
	{
  	session_start();
	}
	/*
		0 ----> PHP_SESSION_DISABLED if sessions are disabled.
		1 ----> PHP_SESSION_NONE if sessions are enabled, but none exists.
		2 ----> PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
	*/
		
	if(!(isset($_SESSION['username']) && isset($_SESSION['user_type'])) && basename($_SERVER['PHP_SELF']) != 'index.php')
	{
		echo '<script>
						alert("Please login first!");
						window.location.href = "index.php";
					</script>';
	}
	
?>