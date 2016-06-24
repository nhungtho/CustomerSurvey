<?php
require_once("functions.php");
$cust_id = mysql_real_escape_string($_GET[
"cust_id"]);
///////////////////CHECK CUSTOMER ID
$sql = "SELECT `CustomerID` FROM `CustomerSurveyInformation` WHERE `CustomerID` = '$cust_id'";
$result = mysql_query($sql);

if(mysql_num_rows($result) >0){
	$sql = "SELECT `Date` FROM `SurveyResults` WHERE CustomerId='$cust_id'";
   $result = mysql_query($sql);
   if(mysql_num_rows($result) >0){
		echo "It appears you have already completed the survey. Thank you!";
   }
   else {

//Query the customer information	
$SQL1 = "SELECT * FROM `CustomerSurveyInformation` WHERE `CustomerID` = '$cust_id' ";
$result1 = mysql_query($SQL1)
	or die("Invalid query: " . mysql_error());
while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC)) 
{	
	$DealerID=$row1["DealerID"];
	$CustomerName=$row1["CustomerName"];
	$RO=$row1["RO"];
	$CustomerAddress=$row1["CustomerAddress"];
	$CustomerCity=$row1["CustomerCity"];
	$CustomerState=$row1["CustomerState"];
	$CustomerZip=$row1["CustomerZip"];
	$CustomerEmail=$row1["CustomerEmail"];
	$CustomerPhone1=$row1["CustomerPhone1"];
	$CustomerPhone2=$row1["CustomerPhone2"];
	$CustomerVIN=$row1["CustomerVIN"];
	$EmailedCustomerSurveyDate=$row1["EmailedCustomerSurveyDate"];
	$EmailedSurveyResultsDate=$row1["EmailedSurveyResultsDate"];
	$RecordEntryDate=$row1["RecordEntryDate"];
	$CustomerCarMake=$row1["CustomerCarMake"];
	$LastVisitDate=$row1["LastVisitDate"];
	//echo $DealerID.$CustomerName;
}
//save to database
$start = array_search("_wp_http_referer",array_keys($_POST))+1;
$end = array_search("submit",array_keys($_POST))-1;
$newArr=array_slice($_POST, $start, $end);
foreach($newArr as $k=>$v)
{
    // $k is the key name and $v is the value of that key
    $val=mysql_real_escape_string($v);
	$k = str_replace('_', ' ', $k);
	$sql="INSERT INTO `SurveyResults` (`CustomerID`,`DealerID`, `Question`, `Answer` ) VALUES ('$cust_id', '$DealerID', '$k', '$val')";
    mysql_query($sql);
}
//Select Dealer's Name:
$SQL2 = "SELECT `DealerName` FROM `DealerInformation` WHERE `DealerID` = '$DealerID' ";
$result2 = mysql_query($SQL2)
	or die("Invalid query: " . mysql_error());
while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) 
{ 
	 $dealername = $row2["DealerName"];
}
//Select email for dealer
$emails = array();
$result = mysql_query("SELECT DISTINCT `Email` FROM `EMAIL_SHARE` WHERE `DealerID` = '$DealerID' ");
while($row = mysql_fetch_array($result)) {
	$emails[] = $row[0];
}
		foreach ($emails as $email) 
			{
				//echo "EMAIL:".$email;
				$to = "$email";
							$subject = "New Survey Result for $dealername";
								$from = "TVI MP3<site_admin@tvi-mp3.com>";
								$headers = "From: TVI MP3 <site_admin@tvi-mp3.com>" . "\r\n" .
								//"BCC: wallen@tvi-mp3.com,  nho@tvi-mp3.com" . "\r\n" .
		"Reply-To: site_admin@tvi-mp3.com" . "\r\n" .
		"X-Mailer: PHP/" . phpversion() . "\r\n" .
		"MIME-Version: 1.0" . "\r\n" .
	    "Content-type: text/html; charset=ISO-8859-1". "\r\n";
	$message = "
    <div align='center'>
        <br>
        <table height='397' border='0' align='center' cellpadding='10' cellspacing='0' bgcolor='#FFFFFF' style='font-family: Arial, Helvetica, sans-serif; font-size: 12px; width: 100%; border: 10px solid #fafafa;'>
            <tbody>
                <tr>
                    <td style='font-size: 26px; border-top: 1px dashed #CCCCCC; border-bottom: 1px dashed #014F66; color: #000; '>Repair Order $RO</td><td style='font-size: 28px; border-top: 1px dashed #CCCCCC; border-bottom: 1px dashed #014F66;' width='150' align='right'><img  width = '200'src='http://tvi-mp3.com/wp-content/themes/education/images/tvilogo.jpg'></td>
                </tr><tr style='color: #666666;'>
                    <td colspan='2' height='40' style='color: #666666;'>
                        MarketPro3 has collected a customer survey response, below if the feedback provided.
                    </td></tr>
                 <tr>
                    <td height='76' colspan='2' style='color: #666666;'>                     <p style='font-size:14px'><strong>Customer Information</strong></p>
                    <ul>
	<li>Customer's name: <strong>$CustomerName </strong></li>
	<li>RO:  <Strong>$RO</Strong></li>
    <li>VIN: <Strong>$CustomerVIN</Strong></li>
	<li>Address: <strong>$CustomerAddress, $CustomerCity $CustomerState, $CustomerZip</Strong></li>
	<li>Email: <strong>$CustomerEmail </Strong></li>
	<li>Phone 1: <strong>$CustomerPhone1 </Strong></li>
	<li>Phone 2: <strong>$CustomerPhone2 </Strong></li>
	</ul>
	</p></td></tr>
	<tr>
    <td colspan='2' style='color: #666666;'><p style='font-size:14px'>
    <p style='font-size:14px'><strong>Survey Results:</strong></p><ul>";
	foreach($newArr as $k=>$v)
{
	$k = str_replace('_', ' ', $k);
    $message .= " 
	<li>$k: ". "<strong>". "$v</strong></li>";  
}
	$message .= "</ul>
                    </p></td>
                </tr> 
	<tr><td colspan='2' style='color: #666666;'><p style='font-size:12px'>
    Thanks and feel free to contact our office or your local TVI MarketPro3 Sales Rep with any questions,
<br><br>TVI MarketPro3<br>
9440 Kirby Drive<br>
Houston, TX 77054<br>
800.884.0844</p></td></tr></tbody>
   </table> </div>";
if (isset($_POST["submit"]))
{	
	mail($to,$subject,$message,$headers);
}
			}		
	$date = new DateTime();
	//echo $date->format('Y-m-d H:i:s');
	$maildate = $date->format('Y-m-d H:i:s') ;
	mysql_query("UPDATE `SurveyResults` SET `Date` = '$maildate' WHERE `DealerID` = '$DealerID'");	
	mysql_query("UPDATE `CustomerSurveyInformation` SET `EmailedSurveyResultsDate` = '$maildate' WHERE `CustomerID` = '$cust_id'");		
	
//////////////////////////////Begin the form

?>
<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title>Customer Survey</title>
	<link rel="stylesheet" type="text/css" href="css/fonts.css"/> 
  <!-- Included CSS Files (Compressed) -->
  <link rel="stylesheet" href="stylesheets/foundation.min.css">
   <link rel="stylesheet" href="form.css">
   <link rel="stylesheet" href="stylesheets/form.css">
	 <!-- JS files for XML populated-->
<script type="text/javascript" src="jquery.js"></script>
  <!-- IE Fix for HTML5 Tags -->
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <!-- Initialize JS Plugins -->
  <script src="javascripts/app.js"></script>
  <!-- Require jQuery / Anyversion TO OBTAIN IPADDRESS--><script type="text/javascript" language="Javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

<!---JQUERY------------------>

<?php
// 4.  Use the code below when you want to SELECT (or retrieve) information from a table.
// $SQL is a variable we use to contain the SQL command.
$SQL = "SELECT DISTINCT `DealerID`, `DealerName`, `DealerAddress`, `DealerCity`, `DealerState`, `DealerZip`, `DealerState`, `DealerPhone1`, `DealerPhone2`, `Text1`, `Text2`, `Text3`, `Text4`, `Text5`, `Graphic1`, `Graphic2`, `Graphic3`, `Graphic4`, `Graphic5` FROM `SURVEY_FORM` WHERE `CustomerID` = '$cust_id' ORDER BY `QuestionOrder` ASC";

// 5.  Execute the SQL command
// This will turn the $result variable into an Array
$result = mysql_query($SQL)
	or die("Invalid query: " . mysql_error());	
			
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
{ 
			$DealerName= $row["DealerName"];
			$Text1= $row["Text1"];
			$Text3= $row["Text3"];
			$Graphic1= $row["Graphic1"];
}
?>	
</head>
<body>
<?php $DealerID = strtolower($DealerID); ?>
<div class="container">
  <div class="row">
	<div class="twelve columns">
<a href="<?php echo $Text3;?>" title="<?php echo $DealerName;?>" target="_blank"><img style="border;none; width:100%; magin:auto;" src="http://www.espview.com/tvi/images/<?php echo $DealerID;?>_bnr.jpg" alt="TVI MP3"></a>
	  </div>
   </div>

   <!--Start body-->  

  <div class="row">

	
<!--starts content-->
    
    <div class="twelve columns">		
<!-- centering -->
<div class="row">
<div class="twelve columns">
<div style="padding: 20px 0px;">

<form id="survey" name="surveyform" method="post" action="">
<input type="hidden" name="token" value="SAVEEDIT"/>
<table>


<?php	
if (isset($_POST["submit"]))
{	
	echo "Thank you for completing our survey. Have a nice day!";
}
else {
?>
<tr><td colspan="2"><?php echo "Hello ".$CustomerName.",<br>".$Text1;?></td><td colspan="3"><?php echo $Graphic1; ?></td></tr>
<?php
$SQL3 = "SELECT `QuestionOrder`, `Question`, `Answer` FROM `SURVEY_FORM` WHERE `CustomerID` = '$cust_id' ORDER BY `QuestionOrder` ASC";
$result3 = mysql_query($SQL3)
	or die("Invalid query: " . mysql_error());	
	while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC)) 
{ 		
$Question= $row3["Question"];
$Answer= $row3["Answer"];	
				echo "<tr><td colspan='3'>$Question</td>
				<td colspan='2'><select name='$Question'><option value='No Answer'></option>";
				$SQL2 = "SELECT `AnswerValue`, `PositionOrder` FROM `AnswerTable` WHERE `Answer` = '$Answer' ORDER BY `PositionOrder` ASC";
				$result2 = mysql_query($SQL2)
				or die("Invalid query: " . mysql_error());	
				while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) 
				{ 	
					$AnswerValue= $row2["AnswerValue"];
					echo "<option>$AnswerValue</option>";
				}
				echo "</select></td></tr>";
 }
 ?>
 <tr>
 <table><tr>
 <td colspan="1">Comment:</td><td colspan="4"><textarea name="Comment" rows="4"></textarea></td></tr>
 </table>
 </tr>
</table>

  
  <div id="myDiv">&nbsp;</div>
 
<div class="row">
<div class="six columns">

</div>
<div class="six columns">
<div style="text-align:right;margin-right:10%;">

<div style="margin-top: 40px;"><button class="btn" type="submit" name="submit" value="Supersize Your Savings" >Submit</button></div>
</div>
</div>
</div>
<?php
}
?>
</form>
</div>
<!--End form 8 columns-->
</div>

		
</div>
<!--End content-->
</div>

<!--end body-->
  </div>
  
  </div>
  
  
  <!-- Included JS Files (Compressed) -->
  <script src="/javascripts/jquery.js"></script>
  <script src="/javascripts/foundation.min.js"></script>
  

</body>
</html>
<?php
   }
}
else{
   echo "Sorry! Your survey is not ready. Please check back with us later. Thank you!";
}
?>