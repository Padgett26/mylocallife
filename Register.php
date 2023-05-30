<div id='mainTableBox' style="padding:10px;">
    <?php
				$msg = "";
				$errorMsg = "";
				$myInfoUp = "";
				$firstName = "";
				$lastName = "";
				$pwd1 = "";
				$pwd2 = "";
				$zip = "";
				$newEmail = "";
				$emailErrorBox = "";
				$pwdErrorBox = "";
				if (filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING )) {
					$ver = filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING );
					$rId = filter_input ( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
					$stmt = $db->prepare ( "SELECT firstName, lastName, email, verifyCode FROM users WHERE id=?" );
					$stmt->execute ( array (
							$rId
					) );
					$row = $stmt->fetch ();
					$firstName = $row ['firstName'];
					$lastName = $row ['lastName'];
					$verifyCode = $row ['verifyCode'];
					$email = $row ['email'];
					$link = hash ( 'sha512', ($verifyCode . $firstName . $email), FALSE );
					if ($ver == $link) {
						$stmt2 = $db->prepare ( "UPDATE users SET verifyCode=?, accessLevel=? WHERE id=?" );
						$stmt2->execute ( array (
								'0',
								'1',
								$rId
						) );
						$stmt3 = $db->prepare ( "INSERT INTO directory VALUES" . "(NULL, ?, ?, ?, '', '', '', '', '', '', '', '', '0', '0')" );
						$stmt3->execute ( array (
								$rId,
								$firstName,
								$lastName
						) );
						$stmt4 = $db->prepare ( "INSERT INTO gameLog VALUES" . "(?, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')" );
						$stmt4->execute ( array (
								$rId
						) );
						$msg .= "Thank you for verifying your email address.<br /><br />Please sign in above.<br /><br />You can now post articles and classifieds on the site.";
					} else {
						$msg .= "The link you followed in the verification email is no longer valid.  Please sign in and go to your account page to resend the verification email, if needed.";
					}
				}

				// My Information processing
				if (filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_STRING ) == "new") {
					$myInfoUp = filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_STRING );
					$firstName = filter_input ( INPUT_POST, 'firstName', FILTER_SANITIZE_STRING );
					$lastName = filter_input ( INPUT_POST, 'lastName', FILTER_SANITIZE_STRING );
					$pwd1 = filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING );
					$pwd2 = filter_input ( INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING );
					$zip = filter_input ( INPUT_POST, 'zip', FILTER_SANITIZE_NUMBER_INT );

					if (filter_input ( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL )) {
						$newEmail = strtolower ( filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL ) );
						$stmt = $db->prepare ( "SELECT COUNT(*) FROM users WHERE email=?" );
						$stmt->execute ( array (
								$newEmail
						) );
						$row = $stmt->fetch ();
						$email = ($row [0] >= 1) ? '0' : $newEmail;
						if ($email == '0') {
							$emailErrorBox = "border:1px solid red;";
							$errorMsg = "The email you entered seems to already be in use.";
						} else {
							if ($pwd1 != "" && $pwd1 != " " && $pwd1 === $pwd2) {
								$salt = mt_rand ( 100000, 999999 );
								$hidepwd = hash ( 'sha512', ($salt . $pwd1), FALSE );
								$stmt = $db->prepare ( "INSERT INTO users VALUES" . "(NULL, ?, ?, ?, ?, ?, '0', 'default', ?, '0', ?, ?, '0', '0', '0')" );
								$stmt->execute ( array (
										$firstName,
										$lastName,
										$email,
										$hidepwd,
										$time,
										$zip,
										$salt,
										$time
								) );
								$stmt2 = $db->prepare ( "SELECT id FROM users WHERE email=? && password=? ORDER BY id DESC LIMIT 1" );
								$stmt2->execute ( array (
										$email,
										$hidepwd
								) );
								$row2 = $stmt2->fetch ();
								$myInfoUp = $row2 ['id'];
								sendVerificationEmail ( $myInfoUp, $firstName, $email, $time );
								$msg .= "A verification email has been sent to the address you provided<br>$email<br /><br />In it is a link for you to click on, this will verify your email address, and will allow you to post articles and classifieds on this site.";
							} else {
								$pwdErrorBox = "border:1px solid red;";
								$errorMsg = "There was either no password entered, or your passwords did not match.";
							}
						}
					} else {
						$emailErrorBox = "border:1px solid red;";
						$errorMsg = "Please enter a valid email address.";
					}
				}
				?>
<header style="font-size:2em; text-align:center;">
    Register for access to My Local Life
</header>
<article style="">
    <?php
				if ($msg != "") {
					echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-top:20px;'>$msg</div>";
				} else {
					?>
        <div style="">
            <form action="index.php?page=Register" method="post">
                <table cellspacing='5px'>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;" colspan="2"><div style="text-align:center;">This information will not be visible on the webpage<br />(except, if you write an article, your first and last names will be used as the author's name).</div></td>
                    </tr>
                    <?php
					if ($errorMsg != "") {
						?>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;" colspan="2"><div style="text-align:center; color:red;"><?php

						echo $errorMsg;
						?></div></td>
                        </tr>
                    <?php
					}
					?>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;">First Name</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="firstName" value="<?php

					echo $firstName;
					?>" max-length="30" size="30" required /></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;">Last Name</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="lastName" value="<?php

					echo $lastName;
					?>" max-length="30" size="30" required /></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;">Email (used as your log in)</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="email" name="email" value="<?php

					echo $newEmail;
					?>" max-length="50" size="30" style="<?php

					echo $emailErrorBox;
					?>" required /><br /></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;">Password</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="password" name="pwd1" value="" max-length="50" style="<?php

					echo $pwdErrorBox;
					?>" size="30" required /> Enter once<br /><br /><input type="password" name="pwd2" value="" max-length="50" style="<?php

					echo $pwdErrorBox;
					?>" size="30" required />and enter again</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;">Zip (used for localizing articles)</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="zip" value="<?php

					echo $zip;
					?>" max-length="5" size="6" required /></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa; padding:10px;" colspan="2"><div style="text-align:center;"><input type="hidden" name="myInfoUp" value="new" /><input type="submit" value=" Save " /></div></td>
                    </tr>
                </table>
            </form>
        </div>
    <?php
				}
				?>
</article>
</div>