<!DOCTYPE html>
<?php
include ('include/dbconn.php');
?>
<!-- =========================================== -->
<!-- Welcome to CheckIt, your personal portfolio -->
<!-- Flaherty			Bowditch			Chu  -->
<!-- =========================================== -->


<html lang="en">
<head>
     <meta charset="utf-8" />
     <title>CheckIt</title>
</head>
<body>
	<?php
		init();
		stockInfo("GOOG");
	?>
</body>
</html>
<?php
	function init(){
		$dbc = connectToDB();
// 		print_r ($_POST);		
		$email = $_POST['email'];
		$password = $_POST['password'];
		if (validProfile($password,$email,$dbc)) {
			echo "Valid profile";
 			displayProfile($dbc,$email,$password);
 		}
		else {
			echo "<a href='http://cscilab.bc.edu/~oconnonx/CheckIt/checkit_signin.php?signin=Sign+In'>Invalid Password back to Login Page</a>";
		}
	}
	
	function validProfile($password,$email,$dbc){
		$sha_password = sha1($password);
		$email_query = "select email,password from checkit where email = '$email' and password = '$sha_password'";
		// and password = '$sha_password'";
		$result = performQuery($dbc, $email_query);
		$rows = mysqli_num_rows($result);
		if($rows == 0)
			return false;
		else 
			return true;
	}
	function stockInfo($stock_name) {
  		$page = 'http://finance.yahoo.com/q?s=' . $stock_name;
      	$content = file_get_contents($page);
      	$stocklower = strtolower($stock_name);
      	$value_pattern = "!yfs_l84_$stocklower\">([0-9,]+\.[0-9]*)!";
      	$change_pattern = "!yfs_p43_$stocklower\">\\([0-9]{1,2}\\.[0-9]{2}%\\)!";
      
    	preg_match_all($value_pattern, $content, $value_res);
    	preg_match_all($change_pattern, $content, $change_res);
      
      	echo "The entire match for price is: " . htmlentities($value_res[0][0]) . "<br>\n";
      	echo "Price is: " . htmlentities($value_res[1][0]) . "<br />\n";

      	echo "The entire match for % change is: " . htmlentities($change_res[0][0]) . "<br>\n";
      	$change =  htmlentities($change_res[0][0]);
      	$change1 = substr($change,-7);
		$x = strpos($change1,"(");
      	if($x===FALSE) {
      		$change1 = "(" . $change1;
      	}

      	//$change2 = substr($change1,1,6)
      	echo "Percent change is: $change1";
    }
    
      function displayProfile($dbc,$email,$password) {
      	$sha_password = sha1($password);
        $profile_query = "select * from checkit where email = '$email' and password = '$sha_password'";
        $result = performQuery($dbc,$profile_query);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        
        $first = $row['first'];
        $last = $row['last'];
        $cash = $row['cash'];
        $stocks = $row['stocks'];
        $stock_array = explode(" ",$stocks);
        $stock_name = array();
        $stock_amount = array();
        
        $i = 1;
        foreach($stock_array as $value){
          if($i%2==0){
            $stock_price[] = $value;
          }
          else{
            $stock_name[] = $value;
          }
          $i= $i+1;
        }
        foreach($stock_name as $value){
          echo "$value";
        }
        
        $email = $row['email'];
       // echo "$first $last $cash $stocks $email";
        
        
      }
?>