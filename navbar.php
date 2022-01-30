<?php 
    include_once('conn.php');

    if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST['btnLogin']))
    {
			$sql = "SELECT COUNT(*)
							FROM MR_USER_INFO A 
							WHERE
								USERNAME = '".$_POST['txtUsername']."'";
			
			$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
			$num_rows = sqlsrv_num_rows($qry);
			
			if($num_rows != 0)
			{
				$sql = "SELECT *
							FROM MR_USER_INFO A 
							WHERE
								USERNAME = '".$_POST['txtUsername']."' AND
								PASSWORD = '".$_POST['txtPassword']."'";
				
				$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
				$num_rows_2 = sqlsrv_num_rows($qry);
				
				if($num_rows_2 != 0)
				{
					$row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC);
					$_SESSION['username'] = $row['USERNAME'];
					$_SESSION['user_type'] = $row['USER_TYPE'];	
				}
				else
				{
					echo "<script language='javascript'>alert('Invalid Credentials!');</script>";
				}
			}
			else
			{
				echo "<script language='javascript'>alert('Invalid Credentials!');</script>";
			}
		}
		else if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST['btnLogout']))
		{
			session_unset();
			session_destroy();
			
			if(basename($_SERVER['PHP_SELF']) != 'index.php')
			{
				echo '<script>window.location.href = "index.php";</script>';
			}
		}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">MRS</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' ?  ' active' : '');?>">
        <a class="nav-link" href="index.php">Home</a>
      </li>
		
		<?php
			if(isset($_SESSION['username']) && $_SESSION['user_type'] == 'A')
			{
				echo '
					<li class="nav-item'.(basename($_SERVER['PHP_SELF']) == 'history.php' ? ' active' : '').'">
						<a class="nav-link" href="history.php">History</a>
					</li>
					<li class="nav-item'.(basename($_SERVER['PHP_SELF']) == 'manage.php' ? ' active' : '').'">
						<a class="nav-link" href="manage.php">Manage</a>
					</li>';
			}
			
			
			echo '</ul>';
    
			if(!isset($_SESSION['username']))
			{
				echo '
				<form method="post" action="#" class="form-inline my-2 my-lg-0">
					<input id="txtUsername" name="txtUsername" class="form-control mr-sm-1" type="input" placeholder="Username" aria-label="Username">
					<input id="txtPassword" name="txtPassword" class="form-control mr-sm-1" type="password" placeholder="Password" aria-label="Password">
					<button id="btnLogin" name="btnLogin" class="btn btn-outline-info btn-primary my-2 my-sm-0" type="submit">Login</button>
				</form>';
			}
			else
			{
				echo '
				<div class="btn-group mr-sm-1 dropleft">
					<button type="button" class="btn btn-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Login as <strong>'.$_SESSION['username'].'</strong>
					</button>
					<div class="dropdown-menu">
						<form method="post" action="#">
							<button id="btnLogout" name="btnLogout" type="submit" class="dropdown-item">Log out</button>
						</form>
					</div>
				</div>
				';
			}
		?>
  </div>
</nav>