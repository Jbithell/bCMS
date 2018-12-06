<?php
	require_once __DIR__ . '/../apiHead.php';

	header('Content-Type:text/plain');
	if (!isset($_GET['code'])) {
        header('Location: ' . $CONFIG['ROOTURL']); //If it fails we may as well just assume they have tried to click it a second time.
        exit;
	}

	$DBLIB->where('emailVerificationCodes_code', $bCMS->sanitizeString($_GET['code']));
	$code = $DBLIB->getOne('emailVerificationCodes');
	if (isset($code) and $code['emailVerificationCodes_valid'] == '1') {
		if (strtotime($code['emailVerificationCodes_timestamp']) < (time()-(60*60*48))) {
			//Code has expired - send another
            if ($AUTH->verifyEmail()) die("Sorry - Your code has expired - we've sent you another one instead");
            else die("Sorry - Your code has expired - please contact support");
        }
		$DBLIB->where ('users_userid', $code['users_userid']);
		$DBLIB->update ('users', ["users_emailVerified" => "1"]); //Verify E-Mail

		$DBLIB->where ('emailVerificationCodes_id', $code['emailVerificationCodes_id']);
		$DBLIB->update ('emailVerificationCodes', ["emailVerificationCodes_valid" => "0", "emailVerificationCodes_used" => "1"]); //Verify E-Mail

		sendemail($code['users_userid'], "Email address verified for " . $CONFIG['PROJECT_NAME'], '
			<center>
				<h1>Thanks for verifying your email address</h1>
				<h3>Welcome to ' . $CONFIG['PROJECT_NAME'] . ' - We are delighted to have you onboard!</h3></center>
			');
		header('Location: ' . $CONFIG['ROOTURL']); exit;
	} else {
		header('Location: ' . $CONFIG['ROOTURL']); //If it fails we may as well just assume they have tried to click it a second time.
        exit;
    }
?>
