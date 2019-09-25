<?php
	session_start();
	require_once 'dbconnect.php';
	
	// if session is not set this will redirect to login page
	if( !isset($_SESSION['user']) ) {
		header("Location: index.php");
		exit;
	}
	// select loggedin users detail
	$id = $_SESSION['user'];
	$res=mysql_query("SELECT * FROM users WHERE userId=".$id);
	$userRow=mysql_fetch_array($res);
	$id = $userRow['userId'];
	//$file = "Select filename from uploads WHERE userId=".$id;
	//$result = mysqli_query($con, $file);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome - <?php echo $userRow['userEmail']; ?></title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>

	<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://localhost/mycloud">My Cloud</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">

          <ul class="nav navbar-nav navbar-right">
            
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
			  <span class="glyphicon glyphicon-user"></span>Hi' <?php echo $userRow['userEmail']; ?>&nbsp;<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="logout.php?logout"><span class="glyphicon glyphicon-log-out"></span>Sign Out</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav> 

	<div id="wrapper">

	<div class="container">

<!--Start-->
	
<?php

$path_name = pathinfo($_SERVER['PHP_SELF']);
$this_script = $path_name['basename'];
$path = "\\".$id."\\";

?>
<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
Please choose a file: <input name="file" type="file" /><br />
<input type="submit" value="Upload" /></form>


<?php 

$uploads_dir = "uploads\\".$id."\\";
if (!empty($_FILES["file"]))
{
    if ($_FILES["file"]["error"] > 0)
       {echo "Error: " . $_FILES["file"]["error"] . "<br>";}
    else
       {echo "Stored file:".$_FILES["file"]["name"]."<br/>Size:".($_FILES["file"]["size"]/1024)." kB<br/>";
       move_uploaded_file($_FILES["file"]["tmp_name"],$uploads_dir.$_FILES["file"]["name"]);
       }
}
    // open this directory 
    $myDirectory = opendir(".\\uploads\\".$id."\\");
    // get each entry
    while($entryName = readdir($myDirectory)) {$dirArray[] = $entryName;} closedir($myDirectory);
    $indexCount = $dirArray;
        echo "Files Uploaded to the user<br/>";
    sort($dirArray);

    echo "<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks><TR><TH>id&nbsp&nbsp&nbsp&nbsp&nbsp;</TH><TH>Filename&emsp;&emsp;&emsp;&emsp;</TH><TH>Delete&emsp;&emsp;</TH></TR>\n";

        for($index=0; $index < count($indexCount); $index++) 
        {
			$i = $index - 1;
            if (substr("$dirArray[$index]", 0, 1) != ".")
            {
			//Filepath and filename stored to variable
			
			$filepath = "uploads\\".$id."\\";
			$filename = $dirArray[$index];
			$file = $filepath.$filename;
			
			//Delete File
					if ($_GET['action'] == 'delete') {
					echo $_GET['filename'];
					unlink($filepath."\\".$_GET['filename']);
					header("Location: home.php");
					}
				
				
            echo "<TR>
			<td>
			$i
			</td>
            <td>
			<a href='$file'>$dirArray[$index]</a>
			</td>
			<td>
			<a href = \" ?action=delete&filename=$filename\">
			<img src=\"assets\delete.png\" height=16 weight=16> 
			</a>
				
			</td>
				</TR>";
			echo "<TR></TR>";
            }
		 }
	
    ?>        


<!--End-->
    </div>
    
    </div>
    
    <script src="assets/jquery-1.11.3-jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
</body>
</html>