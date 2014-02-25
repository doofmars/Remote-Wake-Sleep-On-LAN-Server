<?PHP /*
Remote Wake/Sleep-On-LAN Server
https://github.com/sciguy14/Remote-Wake-Sleep-On-LAN-Server
Author: Jeremy E. Blum (http://www.jeremyblum.com)
License: GPL v3 (http://www.gnu.org/licenses/gpl.html)
*/ 

//You should not need to edit this file. Adjust Parameters in the config file:
require_once('config.php');

//Set default computer
if (empty($_GET)) { header('Location: '. "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "?computer=0"); exit; }

//Uncomment to report PHP errors.
error_reporting(E_ALL);
ini_set('display_errors', '1');
			
// Enable flushing
ini_set('implicit_flush', true);
ob_implicit_flush(true);
ob_end_flush();

?>

<!DOCTYPE html>
<html lang="en" >
  <head>
    <title>Remote Wake/Sleep-On-LAN</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A utility for remotely waking/sleeping a Windows computer via a Raspberry Pi">
    <meta name="author" content="Jeremy Blum">

    <!-- Le styles -->
    <link href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px !important;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 600px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
    <link href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/ico/favicon.png">
  </head>

  <body>

    <div class="container">
    	<form class="form-signin" method="post">
        	<h3 class="form-signin-heading">
			<?php
				//print_r($_POST); //Useful for POST Debugging
				$approved_wake = false;
				$approved_sleep = false;
				if (isset($_POST['submitbutton'])){
					if ($_POST['submitbutton'] == "Wake Up!"){
						$approved_wake = true;
					} elseif ($_POST['submitbutton'] == "Sleep!") {
						$approved_sleep = true;
					}
				}
				

				$selectedComputer = $_GET['computer'];

			 	echo "Remote Wake/Sleep-On-LAN</h3>";
				if ($approved_wake) {
					echo "Waking Up!";
				} elseif ($approved_sleep) {
					echo "Going to Sleep!";
				} else {?>
                    <select name="computer" onchange="if (this.value) window.location.href='?computer=' + this.value">
                    <?php
                        for ($i = 0; $i < count($COMPUTER_NAME); $i++)
                        {
                            echo "<option value='" . $i;
                            if( $selectedComputer == $i)
							{
								echo "' selected>";
							}
                            else
							{
								echo "'>";
							}
							echo $COMPUTER_NAME[$i] . "</option>";
                
                        }
                    ?>
                    </select>

				<?php } ?>
			
           
            <?php

				
				if (!isset($_POST['submitbutton']) || (isset($_POST['submitbutton']) && !$approved_wake && !$approved_sleep))
				{
					echo "<h5 id='wait'>Querying Computer State. Please Wait...</h5>";
					$pinginfo = exec("ping -c 1 " . $COMPUTER_LOCAL_IP[$selectedComputer]);
	    				?>
	    				<script>
						document.getElementById('wait').style.display = 'none';
				        </script>
	   					<?php
					if ($pinginfo == "")
					{
						$asleep = true;
						echo "<h5>" . $COMPUTER_NAME[$selectedComputer] . " is presently asleep.</h5>";
					}
					else
					{
						$asleep = false;
						echo "<h5>" . $COMPUTER_NAME[$selectedComputer] . " is presently awake.</h5>";
					}
				}
				                
                $show_form = true;
                
                if ($approved_wake)
                {
                	echo "<p>Sending WOL Command...</p>";
					exec ('wakeonlan ' . $COMPUTER_MAC[$selectedComputer]);
					exec ('touch wakeupflag');
					echo "<p>Command Sent. Waiting for " . $COMPUTER_NAME[$selectedComputer] . " to wake up...</p><p>";
					$count = 1;
					$down = true;
					while ($count <= $MAX_PINGS && $down == true)
					{
						echo "Ping " . $count . "...";
						$pinginfo = exec("ping -c 1 " . $COMPUTER_LOCAL_IP[$selectedComputer]);
						$count++;
						if ($pinginfo != "")
						{
							$down = false;
							echo "<span style='color:#00CC00;'><b>It's Alive!</b></span><br />";
							echo "<p><a href='?computer=" . $selectedComputer . "'>Return to the Wake/Sleep Control Home</a></p>";
							$show_form = false;
						}
						else
						{
							echo "<span style='color:#CC0000;'><b>Still Down.</b></span><br />";
						}
						sleep($SLEEP_TIME);
					}
					echo "</p>";
					if ($down == true)
					{
						echo "<p style='color:#CC0000;'><b>FAILED!</b> " . $COMPUTER_NAME[$selectedComputer] . " doesn't seem to be waking up... Try again?</p><p>(Or <a href='?computer=" . $selectedComputer . "'>Return to the Wake/Sleep Control Home</a>.)</p>";
					}
				}
				elseif ($approved_sleep)
				{
					echo "<p>Sending Sleep Command...</p>";
					if (file_exists ( "wakeupflag")){
						exec("rm wakeupflag");
						echo "<p><span style='color:#00CC00;'><b>Command Succeeded!</b></span> " . $COMPUTER_NAME[$selectedComputer] . " should go to sleep now</p><p>";
						echo "<p><a href='?computer=" . $selectedComputer . "'>Return to the Wake/Sleep Control Home</a></p>";
						$show_form = false;
					} else {
						echo "<p style='color:#CC0000;'><b>FAILED!</b> cant shutdown " . $COMPUTER_NAME[$selectedComputer] . " it is controlled by other source.</p><p>(Or <a href='?computer=" . $selectedComputer . "'>Return to the Wake/Sleep Control Home</a>.)</p>";
						$show_form = false;
					}
					
					
				}		
                
                if ($show_form)
                {
            ?>
                    <?php if ( (isset($_POST['submitbutton']) && $_POST['submitbutton'] == "Wake Up!") || (!isset($_POST['submitbutton']) && $asleep) ) {?>
        				<input class="btn btn-large btn-primary" type="submit" name="submitbutton" value="Wake Up!"/>
						<input type="hidden" name="submitbutton" value="Wake Up!"/>  <!-- handle if IE used and enter button pressed instead of wake up button -->
                    <?php } else { ?>
		                <input class="btn btn-large btn-primary" type="submit" name="submitbutton" value="Sleep!"/>
						<input type="hidden" name="submitbutton" value="Sleep!" />  <!-- handle if IE used and enter button pressed instead of sleep button -->
                    <?php } ?>	
	
			<?php
				}
			?>
		</form>
    </div> <!-- /container -->
    <script src="<?php echo $BOOTSTRAP_LOCATION_PREFIX; ?>bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
