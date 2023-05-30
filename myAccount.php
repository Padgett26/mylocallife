<?php
if ($myId != '0') {
	$myInfoBlock = "none";

	// Resend the verification email
	if (filter_input ( INPUT_GET, 'verify', FILTER_SANITIZE_STRING ) == "resend") {
		include "formProcessing/myAccount/verify.php";
	}

	// My Information processing
	if (filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_NUMBER_INT ) >= 1) {
		include "formProcessing/myAccount/myInfoUp.php";
	}
	if ($myAccess != '0') {
		// My Directory processing
		if (filter_input ( INPUT_POST, 'myDirectoryUp', FILTER_SANITIZE_NUMBER_INT )) {
			include "formProcessing/myAccount/myDirectoryUp.php";
		}

		// My Business processing
		if (filter_input ( INPUT_POST, 'myBusinessUp', FILTER_SANITIZE_NUMBER_INT )) {
			include "formProcessing/myAccount/myBusinessUp.php";
		}

		// My Survey processing
		if (filter_input ( INPUT_POST, 'surveyId', FILTER_SANITIZE_STRING )) {
			include "formProcessing/myAccount/surveyId.php";
		}

		// Edited article processing
		if (filter_input ( INPUT_POST, 'editedArticle', FILTER_SANITIZE_STRING )) {
			include "formProcessing/myAccount/editedArticle.php";
		}

		// Edited classified processing
		if (filter_input ( INPUT_POST, 'editedClassified', FILTER_SANITIZE_STRING )) {
			include "formProcessing/myAccount/editedClassified.php";
		}

		// Edited comic strip processing
		if (filter_input ( INPUT_POST, 'editedStrip', FILTER_SANITIZE_STRING )) {
			include "formProcessing/myAccount/editedStrip.php";
		}

		// Process the main blog title and description
		if (filter_input ( INPUT_POST, 'blogDescriptions', FILTER_SANITIZE_NUMBER_INT ) == $myId) {
			include "formProcessing/myAccount/blogDescriptions.php";
		}

		// Delete entire blogs
		if (filter_input ( INPUT_POST, 'blogDel', FILTER_SANITIZE_NUMBER_INT ) == '1') {
			include "formProcessing/myAccount/blogDel.php";
		}

		// Process blog entries
		if (filter_input ( INPUT_POST, 'blogId', FILTER_SANITIZE_STRING )) {
			include "formProcessing/myAccount/blogId.php";
		}

		if (filter_input ( INPUT_POST, 'getAdvertising', FILTER_SANITIZE_STRING )) {
			include "formProcessing/myAccount/getAdvertising.php";
		}

		if (filter_input ( INPUT_POST, 'delAdvert', FILTER_SANITIZE_NUMBER_INT )) {
			include "formProcessing/myAccount/delAdvert.php";
		}

		if (filter_input ( INPUT_POST, 'eUpdate', FILTER_SANITIZE_NUMBER_INT )) {
			include "formProcessing/myAccount/eUpdate.php";
		}

		if (filter_input ( INPUT_POST, 'photoUp', FILTER_SANITIZE_NUMBER_INT )) {
			include "formProcessing/myAccount/photoUp.php";
		}
		if (filter_input ( INPUT_POST, 'signUpDel', FILTER_SANITIZE_NUMBER_INT ) >= 1) {
			include "formProcessing/myAccount/signUp.php";
		}
		if (filter_input ( INPUT_POST, 'signUpNew', FILTER_SANITIZE_NUMBER_INT ) == 1) {
			include "formProcessing/myAccount/signUp.php";
		}
	}
	?>
<main id='mainTableBox'
	style="position: relative; top: 0px; left: 0px; padding: 10px;">
	<section>
		<header onclick="toggleview('accMyInfo')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Information </header>
		<article id="accMyInfo" style="display:<?php

	echo $myInfoBlock;
	?>; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
                <?php
	$stmt1 = $db->prepare ( "SELECT * FROM users WHERE id=?" );
	$stmt1->execute ( array (
			$myId
	) );
	$row1 = $stmt1->fetch ();
	$firstName = $row1 ['firstName'];
	$lastName = $row1 ['lastName'];
	$email = $row1 ['email'];
	$createdDate = $row1 ['createdDate'];
	$accessLevel = $row1 ['accessLevel'];
	$theme = $row1 ['theme'];
	$zip = $row1 ['zip'];
	switch ($accessLevel) {
		case 0 :
			$dispLvl = "Unverified";
			break;
		case 1 :
			$dispLvl = "Verified User";
			break;
		case 2 :
			$dispLvl = "Local Admin";
			break;
		case 3 :
			$dispLvl = "Global Admin";
			break;
	}
	?>
                <form action="index.php?page=myAccount" method="post">
				<table cellspacing='5px'>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center;">This information will not be visible
								on the webpage (if you write an article, your first and last
								names will be used as the author's name).</div></td>
					</tr>
                        <?php
	if ($errorMsg != "") {
		?>
                            <tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center; color: red;"><?php

		echo $errorMsg;
		?></div></td>
					</tr>
                        <?php
	}
	?>
                        <tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">First Name</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="firstName"
							value="<?php
	echo $firstName;
	?>" max-length="30"
							size="30" required /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Last Name</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="lastName"
							value="<?php
	echo $lastName;
	?>" max-length="30"
							size="30" required /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Email (used
							as your log in)</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="email" name="email" value="<?php
	echo $email;
	?>"
							max-length="50" size="30" required /><br /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Password
							(Only fill in if you want to change your password)</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="password" name="pwd1" value="" max-length="50" size="30"
							autocomplete="off" /> Enter once<br /> <br /> <input
							type="password" name="pwd2" value="" max-length="50" size="30"
							style="" autocomplete="off" />and enter again</td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Zip (used
							for localizing articles)</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="zip" value="<?php
	echo $zip;
	?>"
							max-length="5" size="6" required /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">My Local
							Life inception date</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><?php
	echo date ( "l jS \of F Y", $createdDate );
	?></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">My Theme</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><select
							name="theme" size="1">
                                    <?php
	$i1 = $db->prepare ( "SELECT themeName FROM themes" );
	$i1->execute ();
	while ( $i1row = $i1->fetch () ) {
		echo "<option value='" . $i1row ['themeName'] . "'";
		if ($theme == $i1row ['themeName']) {
			echo " selected='selected'";
		}
		echo ">" . $i1row ['themeName'] . "</option>\n";
	}
	?>
                                </select></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Access Level</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">
                                <?php
	echo $dispLvl;
	if ($accessLevel == 0) {
		echo " <a href='index.php?page=myAccount&verify=resend&verId=$myId'> * Resend verification email * </a>";
	}
	?>
                            </td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center;">
								Delete my account on My Local Life <input type="checkbox"
									name="deleteMe" value="1" /> This will delete all of my info,
								articles, classifieds, directory listing from the site,
								permanently.
							</div></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center;">
								<input type="hidden" name="myInfoUp"
									value="<?php
	echo $myId;
	?>" /><input type="submit" id="userSubmit" value=" Save " />
							</div></td>
					</tr>
				</table>
			</form>
		</article>
	</section>
        <?php
	if ($accessLevel != 0) {
		if ($showDirectory == 1) {
			?>
            <section>
		<header onclick="toggleview('accMyDirect')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Directory Listing </header>
		<article id="accMyDirect"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                    <?php
			$stmt2 = $db->prepare ( "SELECT * FROM directory WHERE userId=?" );
			$stmt2->execute ( array (
					$myId
			) );
			$row2 = $stmt2->fetch ();
			$firstName = $row2 ['firstName'];
			$lastName = $row2 ['lastName'];
			$businessName = $row2 ['businessName'];
			$phone1 = $row2 ['phone1'];
			$phone1Desc = $row2 ['phone1Desc'];
			$phone2 = $row2 ['phone2'];
			$phone2Desc = $row2 ['phone2Desc'];
			$email = $row2 ['email'];
			$address1 = $row2 ['address1'];
			$address2 = $row2 ['address2'];
			$showListing = $row2 ['showListing'];
			?>
                    <form action="index.php?page=myAccount"
				method="post">
				<table cellspacing='5px'>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center;">This information will be visible on
								the directory page, so only add information you are comfortable
								with being public.</div></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Show my
							information on the directory page</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">
                                    <?php
			echo "<input type='radio' name='showListing' value='1'";
			if ($showListing == '1') {
				echo " checked ";
			}
			echo " />&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='showListing' value='0'";
			if ($showListing == '0') {
				echo " checked";
			}
			echo " />&nbsp;No";
			?>
                                </td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">First Name</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="firstName"
							value="<?php
			echo $firstName;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Last Name</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="lastName"
							value="<?php
			echo $lastName;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Business
							Name</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="businessName"
							value="<?php
			echo $businessName;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Phone #1</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="phone1"
							value="<?php
			echo $phone1;
			?>"
							max-length="15" size="15" /> <select name='phone1Desc' size='1'>
								<option value='home'
									<?php
			if ($phone1Desc == "home") {
				echo " selected='selected'";
			}
			?>>home</option>
								<option value='cell'
									<?php
			if ($phone1Desc == "cell") {
				echo " selected='selected'";
			}
			?>>cell</option>
								<option value='work'
									<?php
			if ($phone1Desc == "work") {
				echo " selected='selected'";
			}
			?>>work</option>
						</select></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Phone #2</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="phone2"
							value="<?php
			echo $phone2;
			?>"
							max-length="15" size="15" /> <select name='phone2Desc' size='1'>
								<option value='home'
									<?php
			if ($phone2Desc == "home") {
				echo " selected='selected'";
			}
			?>>home</option>
								<option value='cell'
									<?php
			if ($phone2Desc == "cell") {
				echo " selected='selected'";
			}
			?>>cell</option>
								<option value='work'
									<?php
			if ($phone2Desc == "work") {
				echo " selected='selected'";
			}
			?>>work</option>
						</select></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Email</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="email" name="email"
							value="<?php
			echo $email;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Address line
							1</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="address1"
							value="<?php
			echo $address1;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Address line
							2</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="address2"
							value="<?php
			echo $address2;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center;">
								<input type="hidden" name="myDirectoryUp"
									value="<?php
			echo $myId;
			?>" /><input type="submit" value=" Save " />
							</div></td>
					</tr>
				</table>
			</form>
		</article>
	</section>
            <?php
		}
		?>
        <section>
		<header onclick="toggleview('accMyBusiness')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Business page </header>
		<article id="accMyBusiness"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		$stmt4 = $db->prepare ( "SELECT businessListing FROM users WHERE id=?" );
		$stmt4->execute ( array (
				$myId
		) );
		$row4 = $stmt4->fetch ();
		if ($row4 ['businessListing'] == '0') {
			?>
                    Here you can get a full page space to promote your business, organization, or group. The page will have space for your contact information, hours of operation, 2 pictures, and your descriptive write up.<br />
			<br /> There will be a link to your dedicated page on the Directory
			page. Plus randomly, a business will be picked to be displayed on the
			main page of the site as the featured business.<br /> <br /> This can
			be yours for a one-time price of $40.<br /> <br />
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post"
				target="_top">
				<input type="hidden" name="cmd" value="_s-xclick"> <input
					type="hidden" name="hosted_button_id" value="8DDR55JXV4KLA"> <input
					type="hidden" name="custom"
					value="<?php
			echo $myId;
			?>,0,<?php
			echo $myId;
			?>,0,0" /> <input type="image"
					src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif"
					border="0" name="submit"
					alt="PayPal - The safer, easier way to pay online!"> <img alt=""
					border="0"
					src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1"
					height="1">
			</form>
                    <?php
		} elseif ($row4 ['businessListing'] == '1') {
			$stmt5 = $db->prepare ( "SELECT * FROM busiListing WHERE userId=?" );
			$stmt5->execute ( array (
					$myId
			) );
			$row5 = $stmt5->fetch ();
			$busiName = $row5 ['busiName'];
			$busiPhone = $row5 ['busiPhone'];
			$hoursOfOperation = $row5 ['hoursOfOperation'];
			$busiAddress1 = $row5 ['busiAddress1'];
			$busiAddress2 = $row5 ['busiAddress2'];
			$busiPic1 = $row5 ['busiPic1'];
			$busiPicExt1 = $row5 ['busiPicExt1'];
			$busiPic2 = $row5 ['busiPic2'];
			$busiPicExt2 = $row5 ['busiPicExt2'];
			$busiDescText = nl2br ( make_links_clickable ( html_entity_decode ( $row5 ['busiDescText'], ENT_QUOTES ), $highlightColor ) );
			$busiEmail = $row5 ['busiEmail'];
			$busiCategory = $row5 ['category'];
			?>
                    <form action="index.php?page=myAccount"
				method="post" enctype='multipart/form-data'>
				<table cellspacing='5px'>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2"><div
								style="text-align: center;">This information will be publicly
								visible, so only add information you are comfortable with being
								public.</div></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Business
							Name</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="busiName"
							value="<?php

			echo $busiName;
			?>"
							maxlength="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Category</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><select
							name="category" size='1'>
                                        <?php
			$getC = $db->prepare ( "SELECT * FROM busiCategories ORDER BY busiCatName" );
			$getC->execute ();
			while ( $gcrow = $getC->fetch () ) {
				$Cid = $gcrow ['id'];
				$CName = $gcrow ['busiCatName'];
				echo "<option value='$Cid'";
				if ($Cid == $busiCategory) {
					echo " selected='selected'";
				}
				echo ">$CName</option>\n";
			}
			?>
                                    </select></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Business
							Phone</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="busiPhone"
							value="<?php
			echo $busiPhone;
			?>"
							maxlength="15" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Hours of
							Operation</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><textarea
								name="hoursOfOperation" cols="40" rows="6" maxlength="200"><?php
			echo $hoursOfOperation;
			?></textarea></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Address line
							1</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="busiAddress1"
							value="<?php
			echo $busiAddress1;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Address line
							2</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="text" name="busiAddress2"
							value="<?php
			echo $busiAddress2;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Email</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><input
							type="email" name="busiEmail"
							value="<?php
			echo $busiEmail;
			?>"
							max-length="50" size="30" /></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Descriptive
							Write Up</td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;"><textarea
								name="busiDescText" cols="40" rows="15" maxlength='65500'><?php
			echo $busiDescText;
			?></textarea></td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">
                                    <?php
			if (file_exists ( "userPics/$myId/$busiPic1.$busiPicExt1" )) {
				echo "<img src='userPics/$myId/$busiPic1.$busiPicExt1' alt='' style='max-width:400px; max-height:400px' /><br />";
			}
			?>
                                </td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Upload pic
							1: <input type="file" name="image1"
							style="background-color: #dddddd;" />
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">
                                    <?php
			if (file_exists ( "userPics/$myId/$busiPic2.$busiPicExt2" )) {
				echo "<img src='userPics/$myId/$busiPic2.$busiPicExt2' alt='' style='max-width:400px; max-height:400px' /><br />";
			}
			?>
                                </td>
						<td style="border: 1px solid #aaaaaa; padding: 10px;">Upload pic
							2: <input type="file" name="image2"
							style="background-color: #dddddd;" />
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid #aaaaaa; padding: 10px;" colspan="2">
							<div style="text-align: center;">
								<input type='hidden' name='imageWidth' value='800' /> <input
									type='hidden' name='imageHeight' value='800' /> <input
									type="hidden" name="myBusinessUp"
									value="<?php

			echo $myId;
			?>" /> <input type="submit" value=" Save " />
							</div>
						</td>
					</tr>
				</table>
			</form>
                <?php
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyArticles')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Articles </header>
		<article id="accMyArticles"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<a href='index.php?page=editArticle&artId=new' style='font-weight:bold'>Upload a new article</a><br /><br />";
		$stmt = $db->prepare ( "SELECT DISTINCT catId FROM articles WHERE authorId=?" );
		$stmt->execute ( array (
				$myId
		) );
		while ( $row = $stmt->fetch () ) {
			$catId = $row ['catId'];
			$substmt1 = $db->prepare ( "SELECT category FROM articleCategories WHERE id=?" );
			$substmt1->execute ( array (
					$catId
			) );
			$subrow1 = $substmt1->fetch ();
			$catName = ($subrow1) ? $subrow1 ['category'] : "***";
			echo "<header style='font-weight:bold; font-size:1.5em; margin:20px 0px; background-color:#dddddd; border:1px solid #aaaaaa; cursor:pointer;' onclick='toggleview(\"art$catId\")'>$catName</header>";
			echo "<article id='art$catId' style='display:none;'>";
			$t = 0;
			$substmt2 = $db->prepare ( "SELECT * FROM articles WHERE authorId=? && catId=? ORDER BY postedDate DESC" );
			$substmt2->execute ( array (
					$myId,
					$catId
			) );
			while ( $row = $substmt2->fetch () ) {
				$artId = $row ['id'];
				$articleTitle = $row ['articleTitle'];
				$articleText = nl2br ( make_links_clickable ( html_entity_decode ( $row ['articleText'], ENT_QUOTES ), $highlightColor ) );
				$pic1Name = $row ['pic1Name'];
				$pic1Ext = $row ['pic1Ext'];
				$pic1Caption = make_links_clickable ( $row ['pic1Caption'], $highlightColor );
				$pic2Name = $row ['pic2Name'];
				$pic2Ext = $row ['pic2Ext'];
				$pic2Caption = make_links_clickable ( $row ['pic2Caption'], $highlightColor );
				$postedDate = $row ['postedDate'];
				$editedDate = $row ['editedDate'];
				$articleScope = $row ['articleScope'];
				$authorId = $row ['authorId'];
				$inReplyTo = $row ['inReplyTo'];
				$youtube = $row ['youtube'];
				$pdf1 = $row ['pdf1'];
				$pdfText1 = $row ['pdfText1'];
				$pdf2 = $row ['pdf2'];
				$pdfText2 = $row ['pdfText2'];
				$embedCode1 = html_entity_decode ( $row ['embedCode1'], ENT_QUOTES );
				if ($embedCode1 != "" && $embedCode1 != " ") {
					$articleText = str_replace ( "<*embedCode1*>", $embedCode1, $articleText );
				} else {
					$articleText = str_replace ( "<*embedCode1*>", "", $articleText );
				}
				$textLen = strlen ( $articleText );
				$offset1 = $textLen / 10;
				$offset2 = $offset1 * 7;
				$pos1 = strpos ( $articleText, "<br />", $offset1 ) + 6;
				$pos2 = strpos ( $articleText, "<br />", $offset2 ) + 6;

				if ($inReplyTo != '0') {
					$gr = $db->prepare ( "SELECT t1.articleTitle, t1.postedDate, t2.firstName, t2.lastName FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.id=?" );
					$gr->execute ( array (
							$inReplyTo
					) );
					$grow = $gr->fetch ();
					$artTitle = $grow ['articleTitle'];
					$artPosted = $grow ['postedDate'];
					$artFN = $grow ['firstName'];
					$artLN = $grow ['lastName'];
					echo "<div style='margin:20px 0px;'><a href='index.php?page=Articles&articleDetail=$inReplyTo'>This article is a reply to:<br />- $artTitle<br />\t<span style='font-size:.75em;'>" . date ( "M j, Y", $artPosted ) . "\t by $artFN $artLN</span></a></div>";
				}
				echo "<header style='text-align:center; margin:20px; font-weight:bold; font-size:1.5em;'>$articleTitle</header>\n";
				echo "Posted date: " . date ( "M j, Y", $postedDate ) . "<br />";
				if ($editedDate != '0') {
					echo "Last edit date: " . date ( "M j, Y", $editedDate ) . "<br />";
				}
				echo "<br />by: $firstName $lastName<br /><br />";
				echo "<a href='index.php?page=editArticle&artId=$artId'>Edit this article</a><br /><br />";
				echo "<article style='text-align:justify; min-height:500px;'>";
				if ($youtube != '0') {
					echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/$youtube' frameborder='0' allowfullscreen></iframe><br /><br />";
				}
				if (file_exists ( "userPics/$authorId/$pic1Name.$pic1Ext" ) || file_exists ( "userPics/$authorId/$pic2Name.$pic2Ext" )) {
					if ($textLen >= 1000) {
						echo substr ( $articleText, 0, $pos1 );
						if (file_exists ( "userPics/$authorId/$pic1Name.$pic1Ext" )) {
							echo "<div style='margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; width:390px; float:right;'><img src='userPics/$authorId/$pic1Name.$pic1Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic1Caption</figcaption></div>";
						}
						if ($pos2 > $pos1) {
							echo substr ( $articleText, $pos1 + 1, $pos2 - $pos1 - 1 );
							if (file_exists ( "userPics/$authorId/$pic2Name.$pic2Ext" )) {
								echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
							}
							echo substr ( $articleText, $pos2 + 1 );
						} else {
							echo substr ( $articleText, $pos1 + 1 );
							if (file_exists ( "userPics/$authorId/$pic2Name.$pic2Ext" )) {
								echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
							}
						}
					} else {
						if (file_exists ( "userPics/$authorId/$pic1Name.$pic1Ext" )) {
							echo "<div style='margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; width:390px; float:right;'><img src='userPics/$authorId/$pic1Name.$pic1Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic1Caption</figcaption></div>";
						}
						echo $articleText;
						if (file_exists ( "userPics/$authorId/$pic2Name.$pic2Ext" )) {
							echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
						}
					}
				} else {
					echo $articleText;
				}
				if (file_exists ( "userPics/$authorId/$pdf1.pdf" ) || file_exists ( "userPics/$authorId/$pdf2.pdf" )) {
					echo "<div style='margin-top:60px;'>";
					echo "** PDF's available with this article **";
					echo "</div>";
				}
				if (file_exists ( "userPics/$authorId/$pdf1.pdf" )) {
					$pt1 = ($pdfText1 != "") ? $pdfText1 : "PDF 1";
					echo "<div style='margin-top:20px;'>";
					echo "<a href='userPics/$authorId/$pdf1.pdf' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$pt1</a>";
					echo "</div>";
				}
				if (file_exists ( "userPics/$authorId/$pdf2.pdf" )) {
					$pt2 = ($pdfText2 != "") ? $pdfText2 : "PDF 2";
					echo "<div style='margin-top:20px;'>";
					echo "<a href='userPics/$authorId/$pdf2.pdf' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$pt2</a>";
					echo "</div>";
				}
				echo "</article>";
				echo "<div style='width:50%; margin:30px 25%; height:5px; background-color:#dddddd; border:1px solid $highlightColor;'></div>";
				$t ++;
			}
			echo "</article>";
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyWritings')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Writings </header>
		<article id="accMyWritings"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
            <?php
		echo "<a href='index.php?page=editWriting&writeId=new' style='font-weight:bold'>Upload new Writings</a><br /><br />";
		$wr1 = $db->prepare ( "SELECT DISTINCT bookId FROM myWritings WHERE authorId = ? ORDER BY title" );
		$wr1->execute ( array (
				$myId
		) );
		while ( $wr1R = $wr1->fetch () ) {
			$wrId = $wr1R ['bookId'];
			$wr2 = $db->prepare ( "SELECT title, approved FROM myWritings WHERE authorId = ? AND bookId = ? LIMIT 1" );
			$wr2->execute ( array (
					$myId,
					$wrId
			) );
			$wr2R = $wr2->fetch ();
			$wrTitle = $wr2R ['title'];
			$approved = ($wr2R ['approved'] == 1) ? "approved" : "pending approval";
			echo "<a href='index.php?page=editWriting&writeId=$wrId' style='font-weight:bold'>$wrTitle <span style='font-size:.75em;'>$approved</span></a><br />";
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyPhotoJournalism')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Photo Show </header>
		<article id="accMyPhotoJournalism"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<a href='index.php?page=editPhoto&photoId=new' style='font-weight:bold'>Upload a new Photo Gallery</a><br /><br />";
		$t = 0;
		$pst1 = $db->prepare ( "SELECT * FROM photoJournalism WHERE authorId=? ORDER BY postedDate DESC" );
		$pst1->execute ( array (
				$myId
		) );
		while ( $prow1 = $pst1->fetch () ) {
			$pId = $prow1 ['id'];
			$photoTitle = $prow1 ['photoTitle'];
			$photoText = nl2br ( make_links_clickable ( html_entity_decode ( $prow1 ['photoText'], ENT_QUOTES ), $highlightColor ) );
			$postedDate = $prow1 ['postedDate'];
			$editedDate = $prow1 ['editedDate'];

			echo "<div>";
			if ($t != 0) {
				echo "<div style='text-align:center; margin:20px;'><hr style='width:75%;' /></div>";
			}
			echo "<header style='text-align:center; margin:20px; font-weight:bold; font-size:1.5em;'>$photoTitle</header>";
			echo "Posted date: " . date ( "M j, Y", $postedDate ) . "<br />";
			if ($editedDate != '0') {
				echo "Last edit date: " . date ( "M j, Y", $editedDate ) . "<br /><br />";
			}
			echo "<a href='index.php?page=editPhoto&photoId=$pId'>Edit this Photo Gallery</a><br /><br />";
			echo "<article style='text-align:justify;'>$photoText</article><br /><br />";
			$pst2 = $db->prepare ( "SELECT * FROM photoList WHERE photoId = ? ORDER BY photoOrder" );
			$pst2->execute ( array (
					$pId
			) );
			while ( $prow2 = $pst2->fetch () ) {
				$photoName = $prow2 ['photoName'];
				$photoExt = $prow2 ['photoExt'];
				$photoCaption = nl2br ( make_links_clickable ( $prow2 ['photoCaption'], $highlightColor ) );

				if (file_exists ( "userPics/$myId/$photoName.$photoExt" )) {
					echo "<div style='width:100%; text-align:justify; margin-bottom:30px;'>";
					echo "<img src='userPics/$myId/$photoName.$photoExt' alt='' style='margin:10px 5%; border:1px solid $highlightColor; padding:5px; width:90%;' /><br />";
					echo "$photoCaption</div>";
				}
			}
			echo "</div>";
			$t ++;
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('e')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Calendar Events </header>
		<article id="e"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<table cellspacing='0' cellpadding='10'>";
		echo "<tr>";
		echo "<td style='border:1px solid $highlightColor;'>Event</td>";
		echo "<td style='border:1px solid $highlightColor;'>Delete</td>";
		echo "<td style='border:1px solid $highlightColor;'>Submit</td>";
		echo "</tr>";
		$stmt29 = $db->prepare ( "SELECT * FROM calendar WHERE userId = ?" );
		$stmt29->execute ( array (
				$myId
		) );
		while ( $row29 = $stmt29->fetch () ) {
			$eId = $row29 ['id'];
			$title = html_entity_decode ( $row29 ['title'], ENT_QUOTES );
			$writeUp = html_entity_decode ( $row29 ['writeUp'], ENT_QUOTES );
			$startTime = $row29 ['startTime'];
			$startHour = date ( "G", $startTime );
			$startMinute = date ( "i", $startTime );
			$startMonth = date ( "n", $startTime );
			$startDay = date ( "j", $startTime );
			$startYear = date ( "Y", $startTime );
			$picture = $row29 ['picture'];
			$eUserId = $row29 ['userId'];
			$approved = $row29 ['approved'];

			echo "<tr>";
			echo "<td style='border:1px solid $highlightColor;'><form action='index.php?page=$page' method='post' enctype='multipart/form-data'>";
			echo "<input type='text' name='title' value='$title' size='75' /><br /><br />";
			if (file_exists ( "userPics/$eUserId/thumbs/$picture" )) {
				echo "<img src='userPics/$eUserId/thumbs/$picture' alt='' /><br />";
			}
			?>
                    Upload a new Picture for you event: <input
				type="file" name="image" /><br /> <br /> From:<br />
			<table cellspacing="0">
				<tr>
					<td>
						<div style="text-align: center;">h</div>
					</td>
					<td>
						<div style="text-align: center;">m</div>
					</td>
					<td>
						<div style="text-align: center;">M</div>
					</td>
					<td>
						<div style="text-align: center;">D</div>
					</td>
					<td>
						<div style="text-align: center;">Y</div>
					</td>
				</tr>
				<tr>
					<td><select size="1" name="startHour">
                                    <?php
			for($a = 0; $a <= 23; $a ++) {
				echo "<option value='$a'";
				if ($a == $startHour) {
					echo " selected";
				}
				echo ">$a</option>\n";
			}
			?>
                                </select></td>
					<td><select size="1" name="startMinute">
                                    <?php
			for($b = 00; $b <= 45; $b = $b + 15) {
				echo "<option value='$b'";
				if ($b == $startMinute) {
					echo " selected";
				}
				echo ">$b</option>\n";
			}
			?>
                                </select></td>
					<td><select size="1" name="startMonth">
                                    <?php
			for($c = 1; $c <= 12; $c ++) {
				echo "<option value='$c'";
				if ($c == $startMonth) {
					echo " selected";
				}
				echo ">$c</option>\n";
			}
			?>
                                </select></td>
					<td><select size="1" name="startDay">
                                    <?php
			for($d = 1; $d <= 31; $d ++) {
				echo "<option value='$d'";
				if ($d == $startDay) {
					echo " selected";
				}
				echo ">$d</option>\n";
			}
			?>
                                </select></td>
					<td><select size="1" name="startYear">
                                    <?php
			$thisY = date ( "Y" );
			$sy = ($startYear > $thisY) ? $thisY : $startYear;
			for($e = $sy; $e <= $thisY + 1; $e ++) {
				echo "<option value='$e'";
				if ($e == $startYear) {
					echo " selected";
				}
				echo ">$e</option>\n";
			}
			?>
                                </select></td>
				</tr>
			</table>
			<br /> <br />
			<textarea name="writeUp" cols="60" rows="10"><?php

			echo $writeUp;
			?></textarea>
                    <?php
			echo "</td>";
			echo "<td style='border:1px solid $highlightColor;'>Delete?<br /><input type='checkbox' name='delEvent' value='1' /></td>";
			echo "<td style='border:1px solid $highlightColor;'><input type='hidden' name='eUpdate' value='$eId' /><input type='submit' value=' Update ' /></form></td>";
			echo "</tr>";
		}
		echo "</table>";
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyClassifieds')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Classifieds </header>
		<article id="accMyClassifieds"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<a href='index.php?page=editClassified&clsId=new' style='font-weight:bold'>Upload a new classified</a><br /><br />";
		$stmt = $db->prepare ( "SELECT DISTINCT catId FROM classifieds WHERE userId=?" );
		$stmt->execute ( array (
				$myId
		) );
		while ( $row = $stmt->fetch () ) {
			$catId = $row ['catId'];
			$substmt1 = $db->prepare ( "SELECT category FROM classifiedCategories WHERE id=?" );
			$substmt1->execute ( array (
					$catId
			) );
			$subrow1 = $substmt1->fetch ();
			$catName = ($subrow1 ['category']) ? $subrow1 ['category'] : "No category set";
			echo "<header style='font-weight:bold; font-size:1.5em; margin:20px 0px; background-color:#dddddd; border:1px solid #aaaaaa; cursor:pointer;' onclick='toggleview(\"cls$catId\")'>$catName</header>";
			echo "<article id='cls$catId' style='display:none;'>";
			$t = 0;
			$substmt2 = $db->prepare ( "SELECT * FROM classifieds WHERE userId=? && catId=? ORDER BY displayUntil" );
			$substmt2->execute ( array (
					$myId,
					$catId
			) );
			while ( $subrow2 = $substmt2->fetch () ) {
				$clsId = $subrow2 ['id'];
				$classifiedTitle = $subrow2 ['classifiedTitle'];
				$classifiedText = nl2br ( make_links_clickable ( html_entity_decode ( $subrow2 ['classifiedText'], ENT_QUOTES ), $highlightColor ) );
				$displayUntil = $subrow2 ['displayUntil'];
				if ($t != 0) {
					echo "<div style='text-align:center; margin:20px;'><hr style='width:75%;' /></div>";
				}
				echo "<header style='text-align:center; margin:20px;'>$classifiedTitle</header>";
				if ($displayUntil > 1) {
					echo "Display until: " . date ( "M j, Y", $displayUntil ) . "<br /><br />";
				}
				echo "<a href='index.php?page=editClassified&clsId=$clsId'>Edit this classified</a><br /><br />";
				echo "<article style=''>$classifiedText</article>";
				$t ++;
			}
			echo "</article>";
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyBlog')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Blog </header>
		<article id="accMyBlog"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<a href='index.php?page=editBlog&blogUserId=$myId&showM=new&showY=new' style='font-weight:bold'>Click here to upload a new blog entry</a><br /><br />";

		$bdstmt = $db->prepare ( "SELECT * FROM blogDescriptions WHERE userId=?" );
		$bdstmt->execute ( array (
				$myId
		) );
		$bdrow = $bdstmt->fetch ();
		if ($bdrow) {
			$blogTitle = $bdrow ['blogTitle'];
			$blogDesc = html_entity_decode ( $bdrow ['blogDesc'], ENT_QUOTES );
			$blogPic = $bdrow ['blogPic'];
			$blogPicExt = $bdrow ['blogPicExt'];
		} else {
			$blogTitle = "";
			$blogDesc = "";
			$blogPic = "x";
			$blogPicExt = "png";
		}
		echo "<form action='index.php?page=myAccount' method='post' enctype='multipart/form-data'>";
		echo "The title of your blog:<br /><input type='text' name='blogTitle' value='$blogTitle' size='70' /><br /><br />";
		if (file_exists ( "userPics/$myId/$blogPic.$blogPicExt" )) {
			echo "The currently installed picture for your blog:<br /><img src='userPics/$myId/$blogPic.$blogPicExt' alt='' style='max-width:200px; max-height:200px;' /><br /><br />";
		}
		echo "Install a new pic for your blog: <input type='file' name='image' /><br /><br />";
		echo "The about, or description for your blog:<br /><textarea name='blogDesc' cols='80' rows='10' maxlength='65500'>$blogDesc</textarea><br /><br />";
		echo "Delete my blog <input type='checkbox' name='delBlog' value='1' /> This will delete your entire blog, and every picture associated with it.<br /><br />";
		echo "<input type='hidden' name='blogDescriptions' value='$myId' /><input type='submit' value=' Save ' /></form>";

		$dateY = $db->prepare ( "SELECT DISTINCT postedYear FROM blog WHERE userId=? ORDER BY postedDate" );
		$dateY->execute ( array (
				$myId
		) );
		while ( $dateYrow = $dateY->fetch () ) {
			$getY = $dateYrow ['postedYear'];
			$dateM = $db->prepare ( "SELECT DISTINCT postedMonth FROM blog WHERE userId=? && postedYear=? ORDER BY postedDate" );
			$dateM->execute ( array (
					$myId,
					$getY
			) );
			while ( $dateMrow = $dateM->fetch () ) {
				$getM = $dateMrow ['postedMonth'];
				echo "<div style='padding:10px;'>";
				echo "<a href='index.php?page=editBlog&blogUserId=$myId&showM=$getM&showY=$getY'>Edit blog entries from " . $months [$getM] . " " . $getY . "</a>";
				echo "</div>";
			}
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyAds')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Advertisements </header>
            <?php
		$showAds = (filter_input ( INPUT_GET, 'showAds', FILTER_SANITIZE_NUMBER_INT ) == '1') ? "block" : "none";
		?>
            <article id="accMyAds" style="display:<?php

		echo $showAds;
		?>; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
                <?php
		echo "<form action='index.php?page=myAccount&showAds=1' method='post' enctype='multipart/form-data'><table style='border:1px solid $highlightColor;' cellspacing='5px'>\n";
		echo "<tr><td style='border:1px solid $highlightColor; padding:5px;' colspan='3'>";
		echo "Upload a new graphic:<br />For the top ad use a max size of 1100px X 100px.<br />";
		echo "For the side ads use a max size of 200px X 300px.<br /><br />";
		echo "<input type='file' name='adImage' /></td></tr>\n";
		echo "<tr><td style='border:1px solid $highlightColor; padding:5px;'>Location<br /><br /><a id='adPlacementLink' onclick='showMenu(\"adPlacementBox\", \"adPlacementLink\")' style='text-decoration:underline; color:$highlightColor;'>Ad locations</a><br /><br /><select name='slot' size='1' onchange='showAdPrices(this.value)'><option value='0'>Pick a spot</option><option value='top'>Top</option><option value='side1'>Side1</option><option value='side2'>Side2</option><option value='side3'>Side3</option></select><br /><br /><div id='showPrice'></div></td>\n";
		echo "<td style='border:1px solid $highlightColor; padding:5px;'>Link address<br /><br />Use web address:<br /><input type='radio' name='linkLocal' value='0' /> <input type='text' name='linkText' value='' /><br /><br />Or use business page on My Local Life<br /><input type='radio' name='linkLocal' value='0' /></td>\n";
		echo "<td style='border:1px solid $highlightColor; padding:5px;'>Did a sales rep help you with your ad? If so, who was it?<br /><select name='salesRepId' size='1'><option value='0'>none</option>";
		$sa = $db->prepare ( "SELECT id, firstName, lastName FROM users WHERE accessLevel >= ? ORDER BY lastName" );
		$sa->execute ( array (
				'2'
		) );
		while ( $sarow = $sa->fetch () ) {
			echo "<option value='" . $sarow ['id'] . "'>" . $sarow ['lastName'] . ", " . $sarow ['firstName'] . "</option>";
		}
		echo "</select><br /><br /><input type='hidden' name='getAdvertising' value='new' /><input type='submit' value=' Submit ' /></td></tr></table></form>\n";
		echo "<div style='height:20px; width:100%; background-color:#cccccc;'></div>";

		$stmta = $db->prepare ( "SELECT * FROM advertising WHERE userId=?" );
		$stmta->execute ( array (
				$myId
		) );
		while ( $rowa = $stmta->fetch () ) {
			$Aid = $rowa ['id'];
			$slot = $rowa ['slot'];
			$activeUntil = $rowa ['activeUntil'];
			$adName = $rowa ['adName'];
			$adExt = $rowa ['adExt'];
			$linkText = $rowa ['linkText'];
			$linkLocal = ($rowa ['linkLocal'] == '0') ? '0' : '1';
			$salesRepId = $rowa ['salesRepId'];

			echo "<form action='index.php?page=myAccount&showAds=1' method='post' enctype='multipart/form-data'><table style='border:1px solid $highlightColor;' cellspacing='5px'>\n";
			echo "<tr><td style='border:1px solid $highlightColor; padding:5px;' colspan='4'>";
			if (file_exists ( "userPics/$myId/" . $adName . "." . $adExt )) {
				echo "<img src='userPics/$myId/" . $adName . "." . $adExt . "' alt='' style='max-height:200px; max-width:500px;' /><br />";
			}
			echo "Upload a new graphic:<br />For the top ad use a max size of 1100px X 100px.<br />";
			echo "For the side ads use a max size of 200px X 300px.<br /><br />";
			echo "<input type='file' name='adImage' /></td></tr>\n";
			echo "<tr><td style='border:1px solid $highlightColor; padding:5px;'>Location<br /><br />$slot</td>";
			echo "<td style='border:1px solid $highlightColor; padding:5px;'>Ad visible on the site until:<br />" . date ( "M j, Y", $activeUntil ) . "<br /><br />Purchase more time:<br />";
			switch ($slot) {
				case 'top' :
					include "includes/ppButtons/adsTop.php";
					break;
				case 'side1' :
					include "includes/ppButtons/adsSide1.php";
					break;
				case 'side2' :
					include "includes/ppButtons/adsSide2.php";
					break;
				case 'side3' :
					include "includes/ppButtons/adsSide3.php";
					break;
			}
			echo "</td>\n";
			echo "<td style='border:1px solid $highlightColor; padding:5px;'>Link address<br /><br />Use web address:<br /><input type='radio' name='linkLocal' value='0'";
			if ($linkLocal == '0') {
				echo " checked";
			}
			echo " /> <input type='text' name='linkText' value='$linkText' /><br /><br />Or use business page on My Local Life<br /><input type='radio' name='linkLocal' value='1'";
			if ($linkLocal == '1') {
				echo " checked";
			}
			echo " /></td>\n";
			echo "<td style='border:1px solid $highlightColor; padding:5px;'>Did a sales rep help you with your ad? If so, who was it?<br /><select name='salesRepId' size='1'><option value='0'>none</option>";
			$sa = $db->prepare ( "SELECT id, firstName, lastName FROM users WHERE accessLevel >= ? ORDER BY lastName" );
			$sa->execute ( array (
					'2'
			) );
			while ( $sarow = $sa->fetch () ) {
				echo "<option value='" . $sarow ['id'] . "'";
				if ($salesRepId == $sarow ['id']) {
					echo " selected='selected'";
				}
				echo ">" . $sarow ['lastName'] . ", " . $sarow ['firstName'] . "</option>";
			}
			echo "</select><br /><br />Delete my ad: <input type='checkbox' name='delAd' value='1' /> This will delete your ad, and any credit you may have associated with it.<br /><br /><input type='hidden' name='getAdvertising' value='$Aid' /><input type='hidden' name='slot' value='$slot' /><input type='submit' value=' Submit ' /></td></tr></table></form>";
			echo "<div style='height:20px; width:100%; background-color:#cccccc;'></div>";
		}
		?>
            </article>
		<div id='adPlacementBox' style='display:none; position:absolute; top:0px; left:0px; background-color: #ffffff; border:1px solid <?php

		echo $highlightColor;
		?>; padding:10px; z-index:999; box-shadow: 5px 5px 5px grey;'>
			<img src="images/adPlacement.png" alt="" /> <img
				src="images/close_pop.png" alt="Close"
				style="z-index: 1000; position: absolute; top: -10px; right: -10px; cursor: pointer;"
				onclick='toggleview("adPlacementBox")' />
		</div>
	</section>
	<section>
		<header onclick="toggleview('accMySurvey')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Surveys </header>
		<article id="accMySurvey"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<a href='index.php?page=editSurvey&surveyId=new' style='font-weight:bold'>Start a new survey</a><br /><br />";
		$stmt = $db->prepare ( "SELECT t1.id, t1.question FROM surveyQuestions AS t1 INNER JOIN survey AS t2 ON t1.surveyId = t2.id WHERE t2.userId = ? ORDER BY t2.startDate DESC" );
		$stmt->execute ( array (
				$myId
		) );
		while ( $r = $stmt->fetch () ) {
			$surId = $r [0];
			$surTitle = $r [1];
			echo "<div style='cursor:pointer; background-color:#eeeeee; border:1px solid #aaaaaa; padding:10px; margin:10px 0px;'><a href='index.php?page=editSurvey&surveyId=$surId' style='font-weight:bold; font-size:1.25em;'>$surTitle</a></div>";
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accMyStrip')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Strip </header>
		<article id="accMyStrip"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<a href='index.php?page=editStrip&stripId=new' style='font-weight:bold'>Upload a new comic strip</a><br /><br />";
		$stmt3 = $db->prepare ( "SELECT DISTINCT stripTitle FROM strips WHERE userId=?" );
		$stmt3->execute ( array (
				$myId
		) );
		while ( $row3 = $stmt3->fetch () ) {
			$stripTitle = $row3 ['stripTitle'];
			echo "<header style='font-weight:bold; font-size:1.5em; margin:20px 0px; background-color:#dddddd; border:1px solid #aaaaaa; cursor:pointer;' onclick='toggleview(\"s$stripTitle\")'>$stripTitle</header>";
			echo "<article id='s$stripTitle' style='display:none;'>";
			$t = 0;
			$stmt4 = $db->prepare ( "SELECT * FROM strips WHERE userId=? && stripTitle=? ORDER BY displayDayStart DESC" );
			$stmt4->execute ( array (
					$myId,
					$stripTitle
			) );
			while ( $row4 = $stmt4->fetch () ) {
				$stripId = $row4 ['id'];
				$picName = $row4 ['picName'];
				$picExt = nl2br ( $row4 ['picExt'] );
				$displayDayStart = $row4 ['displayDayStart'];
				$displayDayEnd = $row4 ['displayDayEnd'];
				if (date ( "M j, Y", $displayDayStart ) == date ( "M j, Y", $displayDayEnd )) {
					$textDays = date ( "M j, Y", $displayDayStart );
				} else {
					$textDays = date ( "M j, Y", $displayDayStart ) . " - " . date ( "M j, Y", $displayDayEnd );
				}
				if ($t != 0) {
					echo "<div style='text-align:center; margin:20px;'><hr style='width:75%;' /></div>";
				}
				echo "<header style='text-align:center; margin:20px;'>$stripTitle</header>";
				echo "Display: $textDays<br /><br />";
				echo "<a href='index.php?page=editStrip&stripId=$stripId'>Edit this strip</a><br /><br />";
				echo "<img src='userPics/$myId/$picName.$picExt' alt='' style='margin:auto;' />";
				$t ++;
			}
			echo "</article>";
		}
		?>
            </article>
	</section>
	<section>
		<header onclick="toggleview('accSignUp')"
			style="cursor: pointer; background-color: #dddddd; border: 1px solid #aaaaaa; font-weight: bold; font-size: 1.5em; padding: 10px; margin: 10px 0px;">
			My Sign Ups </header>
		<article id="accSignUp"
			style="display: none; padding: 10px; margin: 10px 0px; border: 1px solid #aaaaaa;">
                <?php
		echo "<div style='margin:5px; padding:10px; border:1px solid black; cursor:pointer;' onclick='toggleview(\"signUp0\")'>Create a new sign up page</div>";
		echo "<div style='margin:5px; padding:10px; border:1px solid black; display:none;' id='signUp0'>";
		echo "<form action='index.php?page=myAccount' method='post' enctype='multipart/form-data'><table cellspacing='0px' style='border:1px solid black;'>";
		echo "<tr><td id='signuptd'>Title:</td><td id='signuptd'><input type='text' name='title' value=''></td></tr>";
		echo "<tr><td id='signuptd'>Description:</td><td id='signuptd'><textarea name='description' style='width:90%; height:40px;'></textarea></td></tr>";
		echo "<tr><td id='signuptd'>Picture:</td><td id='signuptd'>Upload a new image<br><input type='file' name='image'></td></tr>";
		echo "<tr><td id='signuptd'>Open sign up:</td><td id='signuptd'><input type='date' name='startSignUp' value='" . date ( "Y-m-d", $time ) . "'></td></tr>";
		echo "<tr><td id='signuptd'>End sign up:</td><td id='signuptd'><input type='date' name='endSignUp' value='" . date ( "Y-m-d", ($time + 604800) ) . "'></td></tr>";
		for($i = 1; $i <= 3; ++ $i) {
			echo "<tr><td colspan='2' id='signuptd'>";
			echo ($i == 1) ? "<input type='hidden' name='useTS$i' value='1'>Event timespan $i" : "<input type='checkbox' name='useTS$i' value='1'> Use event timespan $i";
			echo "</td></tr>";
			echo "<tr><td id='signuptd'>Start date and time</td><td id='signuptd'><input type='date' name='startDate$i' value='" . date ( "Y-m-d", ($time + 604800) ) . "'> <input type='number' name='startHour$i' value='12' step='1' min='0' max='23'> : <input type='number' name='startMin$i' value='0' step='1' min='0' max='59'></td></tr>";
			echo "<tr><td id='signuptd'>End date and time</td><td id='signuptd'><input type='date' name='endDate$i' value='" . date ( "Y-m-d", ($time + 604800) ) . "'> <input type='number' name='endHour$i' value='12' step='1' min='0' max='23'> : <input type='number' name='endMin$i' value='0' step='1' min='0' max='59'></td></tr>";
			echo "<tr><td id='signuptd'>Sub-timespans</td><td id='signuptd'>If your timespan is broken in to segments, say, a string of 15 minute meetings through the timespan, you can set the number of minutes each sub-timespan should be here. If the timespan is not broken in to segments, leave this setting at zero.<br><input type='number' name='subMin$i' value='0' step='5' min='0' max='120'></td></tr>";
			echo "<tr><td id='signuptd'>Registration limit</td><td id='signuptd'>You can limit the number of registrations per timespan. For no limit, leave at zero. A number here will limit the number registrations per sub-timespans. For example, if you have a string of 15 minute meetings through a timespan, putting a 1 here will only allow 1 registration per 15 minute meeting. If your event isn't broken up in to smaller meetings, this setting will be for the whole event.<input type='number' name='limit$i' value='0' step='1' min='0' max='1000'></td></tr>";
		}
		echo "</table><input type='submit' value=' Create Sign Up Form '><input type='hidden' name='signUpNew' value='1'></form></div>";
		$g = $db->prepare ( "SELECT * FROM signUp WHERE userId = ? ORDER BY endSignUp DESC" );
		$g->execute ( array (
				$myId
		) );
		while ( $gR = $g->fetch () ) {
			$id = $gR ['id'];
			$title = html_entity_decode ( $gR ['title'], ENT_QUOTES );
			$description = nl2br ( html_entity_decode ( $gR ['description'], ENT_QUOTES ) );
			$pic = $gR ['pic'];
			$startSignUp = $gR ['startSignUp'];
			$endSignUp = $gR ['endSignUp'];
			echo "<div style='margin:5px; padding:10px; border:1px solid black; cursor:pointer;' onclick='toggleview(\"signUp$id\")'>$title</div>";
			echo "<div style='margin:5px; padding:10px; border:1px solid black; display:none;' id='signUp$id'>";
			echo "<form action='index.php?page=myAccount' method='post'><table cellspacing='0px' style='border:1px solid black;'>";
			echo "<tr><td id='signuptd'>Delete this sign up form:</td>";
			echo "<td id='signuptd'><input type='checkbox' name='delSU' value='1'><br>This will delete the sign up page and all associated information, including registration data.<br><input type='submit' value=' Delete Form '><input type='hidden' name='signUpDel' value='$id'></td>";
			echo "</tr>";
			echo "<tr><td id='signuptd'>Title:</td><td id='signuptd'>$title</td></tr>";
			echo "<tr><td id='signuptd'>Description:</td><td id='signuptd'>$description</td></tr>";
			echo "<tr><td id='signuptd'>Picture:</td><td id='signuptd'>";
			if (file_exists ( "userPics/$myId/$pic" )) {
				echo "<img src='userPics/$myId/$pic' style='margin:10px; float:left;'>";
			}
			echo "</td></tr>";
			echo "<tr><td id='signuptd'>Open sign up:</td><td id='signuptd'>" . date ( "Y-m-d", $startSignUp ) . "</td></tr>";
			echo "<tr><td id='signuptd'>End sign up:</td><td id='signuptd'>" . date ( "Y-m-d", $endSignUp ) . "</td></tr>";
			echo "<tr><td colspan='2' id='signuptd'>Event times:</td></tr>";
			$g2 = $db->prepare ( "SELECT * FROM signUpTimes WHERE subOfId = ? ORDER BY start" );
			$g2->execute ( array (
					$id
			) );
			while ( $g2R = $g2->fetch () ) {
				$tId = $g2R ['id'];
				$start = $g2R ['start'];
				$end = $g2R ['end'];
				echo "<tr><td id='signuptd'>Start: " . date ( "H:i", $start ) . "<br>End: " . date ( "H:i", $end ) . "<br>" . date ( "Y-m-d", $start ) . "</td><td id='signuptd'>";
				$g3 = $db->prepare ( "SELECT * FROM signUpRegistry WHERE signUpId = ? ORDER BY registerTime" );
				$g3->execute ( array (
						$tId
				) );
				while ( $g3R = $g3->fetch () ) {
					$rId = $g3R ['id'];
					$name = $g3R ['name'];
					$email = $g3R ['email'];
					$phone = $g3R ['phone'];
					$registerTime = $g3R ['registerTime'];
					echo "<div style='font-weight:bold; cursor:pointer; padding:10px;' onclick='toggleview(\"reg$rId\")'>$name</div>";
					echo "<div style='display:none; padding:10px 20px;' id='reg$rId'>$email<br>$phone<br>Registered on: " . date ( "Y-m-d H:i", $registerTime ) . "</div>";
				}
				echo "</td></tr>";
			}
			echo "</table></div>";
		}
		?>
            </article>
	</section>
        <?php
	}
	?>
    </main>
<?php
} else {
	echo "<div style='text-align:center' font-weight:bold; font-size:1.5em;>Please sign in above.</div>";
}