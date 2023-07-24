<?php
// Original code copied from w3schools.
// Adapted for CST8805 project by Yvan Perron November 27, 2021
//Do not change any of the code below this line and up to the "End of do not modify comment below"
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$target_fileSig = $target_dir . basename($_FILES["fileToUploadSig"]["name"]);
$target_fileX509 = $target_dir . basename($_FILES["fileToUploadX509"]["name"]);


$uploadOk = 1;

// Check if file already exists
//if (file_exists($target_file) || file_exists($target_fileSig) || file_exists($target_fileX509)) {
//  echo " <p>Files already exists - they will be overwritten. </p> ";
//  //$uploadOk = 0;
//}

echo " <p style='color:blue; font-size:1.5em;'> File Upload Results</p> ";

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "&nbsp &nbsp &nbsp Sorry, your file is too large.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "&nbsp &nbsp &nbsp Sorry, your file was not uploaded. ";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) && move_uploaded_file($_FILES["fileToUploadSig"]["tmp_name"], $target_fileSig) && move_uploaded_file($_FILES["fileToUploadX509"]["tmp_name"], $target_fileX509)) {
//    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " and its digital signature have been uploaded. <br>";
      echo "&nbsp &nbsp &nbsp Files successfully uploaded to Bank Payroll Application <br>";
  } else {
    echo "&nbsp &nbsp &nbsp  Sorry, there was an error uploading your file. ";
    $uploadOk = 0;
  }
}
//End of do NOT modify any of the code above this line

//The code below this line is student customizable
//Important variables
//  $target_fileSig    path to payroll signature file
//  $target_file       path to payroll file
//  $target_fileX509   path to signer x509 certificate
//  $target_filePub  path to signer public key

$carootFile = "/etc/pki/tls/certs/CARootCert-Group4.cer";
$target_filePubKey = $target_fileX509 . "_Pub.pem";
if  ($uploadOk !== 0) {
//	$cmd = "openssl dgst -sha3-512 -verify rsa_pub_prof_sign.pem -signature ./uploads/Task1-msg-1-digsig.bin ./uploads/Task1-msg-1.txt ";

//The openssl x509 command extracts the public key from the X509 certificate which is required in the signature verification step
	$cmd = "openssl x509 -inform pem -in $target_fileX509 -pubkey -out $target_filePubKey";
	$valRes = exec( $cmd );
	$cmd = "openssl dgst -sha256 -verify $target_filePubKey -signature $target_fileSig $target_file ";
	echo " <p style='color:blue; font-size:1.5em;'> Payroll Signature Validation Results</p> ";
	$valRes = shell_exec( $cmd );
	echo "&nbsp &nbsp &nbsp $valRes";

// Command to validate that the signers certificate is issued by the trusted Bank's CA
	$cmd = "openssl x509 -noout -issuer -in $target_fileX509";
	$trustValue = exec ($cmd);
	$newString = substr($trustValue,60,7);
	$fixedString = " Group4";
	echo " <p style='color:blue; font-size:1.5em;'> Who Issued?</p> ";
	if ($newString === $fixedString){
		echo("&nbsp &nbsp &nbsp This Certificate is issued by Trusted CA");
	}
	else 
	{
		echo("&nbsp &nbsp &nbsp This Certificate is not issued by Trusted CA");
	}

// Command to validate that the signers certificate is not expired
    $cmd = "openssl x509 -noout -checkend 0 -in $target_fileX509";
        $datevalue = exec($cmd);
	echo " <p style='color:blue; font-size:1.5em;'> Certificate Validity</p> ";
	if($datevalue == "Certificate will not expire")
	{
		 echo ("&nbsp &nbsp &nbsp Certificate is not Expired");
    }	else{
      	echo("&nbsp &nbsp &nbsp Certificate is Expired");
	}
	
// crl check
	$crlFile = 'https://www.bankcsa.csa/CAGroup4.crl';
	$cmd = "x509 -noout -serial -in $carootFile -text";
	$cmd = "verify -verbose -crl_check -crl_download -trusted $carootFile $target_fileX509";
	$res = exec( $cmd );
	echo($res);
}
?>