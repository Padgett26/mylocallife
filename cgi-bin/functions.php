<?php
function delTree($dir)
{
	$files = array_diff(scandir($dir), array(
		'.',
		'..'
	));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}
function sendVerificationEmail($toId, $firstName, $email, $verifyCode)
{
	$link = hash('sha512', ($verifyCode . $firstName . $email), FALSE);
	$mess = "$firstName,\n
        As a layer of security, we ask that you verify your email address before being allowed to post on the My Local Life webpage.  The easiest way to do this is to click on the link below, this will update your status on the webpage.  If clicking on the link doesn't work, you can also highlight the link below, copy it (ctrl + c), and then paste it (ctrl + v) in the address field of your web browser, and then hit enter.\n
        https://mylocal.life/index.php?page=Register&id=$toId&ver=$link\n
        Thank you,\nAdmin\nMy Local Life\n";
	$message = wordwrap($mess, 70);
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$headers .= "From: My Local Life <admin@mylocal.life>" . "\r\n";
	mail($email, 'Please verify your email address to access My Local Life', $message, $headers);
}
function sendArticleEmail($msgToAuthor, $firstName, $email, $subject)
{
	$msg = nl2br($msgToAuthor);
	$mess = "$firstName,\n\n
        $msg\n\n
        Thank you,\nAdmin\nMy Local Life";
	$message = wordwrap($mess, 70);
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$headers .= "From: My Local Life <admin@mylocal.life>" . "\r\n";
	mail($email, "About your article entitled $subject", $message, $headers);
}
function sendPWResetEmail($toId, $firstName, $email, $verifyCode)
{
	$link = hash('sha512', ($verifyCode . $firstName . $email), FALSE);
	$mess = "$firstName,\n\n
        There has been a request on the My Local Life website for a password reset for this account.  If you initiated this request, click the link below, and you will be sent to a page where you will be able enter a new password. If you did not initiate this password reset request, simple ignore this email, and your password will not be changed.\n\n
        https://mylocal.life/index.php?page=PWReset&id=$toId&ver=$link\n\n
        Thank you,\nAdmin\nMy Local Life";
	$message = wordwrap($mess, 70);
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$headers .= "From: My Local Life <admin@mylocal.life>" . "\r\n";
	mail($email, 'My Local Life password reset request', $message, $headers);
}
function getPicType($imageType)
{
	switch ($imageType) {
		case "image/gif":
			$picExt = "gif";
			break;
		case "image/jpeg":
			$picExt = "jpg";
			break;
		case "image/pjpeg":
			$picExt = "jpg";
			break;
		case "image/png":
			$picExt = "png";
			break;
		default:
			$picExt = "xxx";
			break;
	}
	return $picExt;
}
function processPic($userId, $imageName, $imageWidth, $imageHeight, $tmpFile, $picExt)
{
	$folder = "userPics/$userId";
	if (!is_dir("$folder")) {
		mkdir("$folder", 0777, true);
	}

	$saveto = "$folder/$imageName.$picExt";

	list($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize($tmpFile) : null;
	if ($width != null && $height != null) {
		$image = new Imagick($tmpFile);
		$image->thumbnailImage($imageWidth, $imageHeight, true);
		$image->writeImage($saveto);
	}
}
function processThumbPic($userId, $imageName, $imageWidth, $imageHeight, $tmpFile, $picExt)
{
	$folder = "userPics/$userId/thumb";
	if (!is_dir("$folder")) {
		mkdir("$folder", 0777, true);
	}

	$saveto = "$folder/$imageName.$picExt";

	list($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize($tmpFile) : null;
	if ($width != null && $height != null) {
		$image = new Imagick($tmpFile);
		$image->thumbnailImage(150, 150, true);
		$image->writeImage($saveto);
	}
}
function processPdf($userId, $time, $pdf1or2, $file, $artId, $db)
{
	$pdfName = $time + $pdf1or2;
	$saveto = "userPics/$userId/$pdfName.pdf";
	move_uploaded_file($file, $saveto);
	if (filesize("../userPics/$userId/$pdfName.pdf") <= 1000) {
		unlink("../userPics/$userId/$pdfName.pdf");
	}
	if (file_exists("userPics/$userId/$pdfName.pdf")) {
		$pdfstmt = $db->prepare("UPDATE articles SET pdf" . $pdf1or2 . "=? WHERE id=?");
		$pdfstmt->execute(array(
			$pdfName,
			$artId
		));
	}
}
function deletePdf($userId, $pdf1or2, $artId, $db)
{
	$stmt = $db->prepare("SELECT pdf" . $pdf1or2 . " FROM articles WHERE id=?");
	$stmt->execute(array(
		$artId
	));
	$row = $stmt->fetch();
	if (file_exists("userPics/$userId/" . $row[0] . ".pdf")) {
		unlink("userPics/$userId/" . $row[0] . ".pdf");
	}
	$stmt2 = $db->prepare("UPDATE articles SET pdf" . $pdf1or2 . "='0' WHERE id=?");
	$stmt2->execute(array(
		$artId
	));
}
class functions
{
	var $lat;
	var $lng;
	function LatLong($lat, $lng)
	{
		$this->lat = $lat;
		$this->lng = $lng;
	}
	function distance($to)
	{
		$er = 6366.707;

		$latFrom = deg2rad($this->lat);
		$latTo = deg2rad($to->lat);
		$lngFrom = deg2rad($this->lng);
		$lngTo = deg2rad($to->lng);

		$x1 = $er * cos($lngFrom) * sin($latFrom);
		$y1 = $er * sin($lngFrom) * sin($latFrom);
		$z1 = $er * cos($latFrom);

		$x2 = $er * cos($lngTo) * sin($latTo);
		$y2 = $er * sin($lngTo) * sin($latTo);
		$z2 = $er * cos($latTo);

		$d = acos(sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lngTo - $lngFrom)) * $er;
		return $d;
	}
}
function getZipAreas($myZip, $db)
{
	$zipcode1 = $db->prepare("SELECT longitude,latitude FROM zipCodes WHERE zipCode=?");
	$zipcode1->execute(array(
		$myZip
	));
	$ziprow1 = $zipcode1->fetch();
	$lat1 = $ziprow1['latitude'];
	$lon1 = $ziprow1['longitude'];

	$lld1 = new functions($lat1, $lon1);

	$getZipCodes1 = array();
	$getZipCodes2 = array();
	$getZipCodes3 = array();

	$zipcode2 = $db->prepare("SELECT zipCode,longitude,latitude FROM zipCodes");
	$zipcode2->execute(array(
		$myZip
	));
	while ($ziprow2 = $zipcode2->fetch()) {
		$zipC = $ziprow2['zipCode'];
		$lat2 = $ziprow2['latitude'];
		$lon2 = $ziprow2['longitude'];
		$lld2 = new functions($lat2, $lon2);
		$d = $lld1->distance($lld2);
		if ($d >= 0 && $d <= 161) { // 0 to 100 miles
			$getZipCodes1[] = $zipC;
		} elseif ($d > 161 && $d <= 403) { // 100 to 250 miles
			$getZipCodes2[] = $zipC;
		} elseif ($d > 403) {
			$getZipCodes3[] = $zipC; // over 250 miles
		}
	}
	return array(
		$getZipCodes1,
		$getZipCodes2,
		$getZipCodes3
	);
}
function make_links_clickable($text, $highlightColor)
{
	return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-ZÐ°-Ñ�Ð�-Ð¯()0-9@:%_+.~#?&;//=]+)!i', "<a href='$1' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$1</a>", $text);
}
function displayArticle($getId, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT articleTitle,articleText,pic1Name,pic1Ext,authorId,youtube,slug FROM articles WHERE id = ?");
	$stmt->execute(array(
		$getId
	));
	$row = $stmt->fetch();
	$articleTitle = $row['articleTitle'];
	$a1 = html_entity_decode($row['articleText'], ENT_QUOTES);
	$a2 = htmlspecialchars(trim($a1));
	$articleText = nl2br(substr($a2, 0, 500));
	$pic1Name = $row['pic1Name'];
	$pic1Ext = $row['pic1Ext'];
	$authorId = $row['authorId'];
	$yt = $row['youtube'];
	$slug = $row['slug'];
	// echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='/Articles::$slug'>";
	echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='index.php?page=Articles&artId=$getId'>";
	echo "<header style='font-weight:bold; text-align:center; margin-bottom:10px; font-size:1.25em;'>$articleTitle</header>\n";
	if ($yt != '0') {
		echo "<div style='margin:auto; width:150px; float:left;'><img src='images/video.png' alt='' style='width:150px;' /></div>";
	}
	if (file_exists("userPics/$authorId/thumb/$pic1Name.$pic1Ext")) {
		echo "<img src='userPics/$authorId/thumb/$pic1Name.$pic1Ext' alt='' style='margin:0px 10px 10px 0px; float:left;' />";
	}
	echo "<article style='text-align:justify;'>$articleText</article></a></article>\n";
}
function displayRecentArticle($getId, $catText, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT articleTitle,articleText,pic1Name,pic1Ext,authorId,youtube,slug FROM articles WHERE id = ?");
	$stmt->execute(array(
		$getId
	));
	$row = $stmt->fetch();
	$articleTitle = $row['articleTitle'];
	$a1 = html_entity_decode($row['articleText'], ENT_QUOTES);
	$a2 = htmlspecialchars(trim($a1));
	$articleText = nl2br(substr($a2, 0, 500));
	$pic1Name = $row['pic1Name'];
	$pic1Ext = $row['pic1Ext'];
	$authorId = $row['authorId'];
	$yt = $row['youtube'];
	$slug = $row['slug'];
	// echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='/Articles::$slug'>";
	echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='index.php?page=Articles&artId=$getId'>";
	echo "<div style='font-size:.75em; color:#666;'>$catText</div><header style='font-weight:bold; text-align:center; margin-bottom:10px; font-size:1.25em;'>$articleTitle</header>\n";
	if ($yt != '0') {
		echo "<div style='margin:auto; width:150px; float:left;'><img src='images/video.png' alt='' style='width:150px;' /></div>";
	}
	if (file_exists("userPics/$authorId/thumb/$pic1Name.$pic1Ext")) {
		echo "<img src='userPics/$authorId/thumb/$pic1Name.$pic1Ext' alt='' style='margin:0px 10px 10px 0px; float:left;' />";
	}
	echo "<article style='text-align:justify;'>$articleText</article></a></article>\n";
}
function displayPhoto($getId, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT photoTitle,photoText,authorId FROM photoJournalism WHERE id = ?");
	$stmt->execute(array(
		$getId
	));
	while ($row = $stmt->fetch()) {
		$photoTitle = $row['photoTitle'];
		$a1 = html_entity_decode($row['photoText'], ENT_QUOTES);
		$a2 = htmlspecialchars(trim($a1));
		$photoText = nl2br(substr($a2, 0, 500));
		$authorId = $row['authorId'];
		$stmt2 = $db->prepare("SELECT photoName,photoExt FROM photoList WHERE photoId = ? ORDER BY photoOrder LIMIT 1");
		$stmt2->execute(array(
			$getId
		));
		$row2 = $stmt2->fetch();
		$photoName = $row2['photoName'];
		$photoExt = $row2['photoExt'];
		echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='index.php?page=Photo&photoShow=$getId'>";
		echo "<header style='font-weight:bold; text-align:center; margin-bottom:10px; font-size:1.25em;'>$photoTitle</header>\n";
		if (file_exists("userPics/$authorId/thumb/$photoName.$photoExt")) {
			echo "<img src='userPics/$authorId/thumb/$photoName.$photoExt' alt='' style='margin:0px 10px 10px 0px; float:left;' />";
		}
		echo "<article style='text-align:justify;'>$photoText</article></a></article>\n";
	}
}
function displayBlog($getId, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT userId, blogTitle, blogDesc, blogPic, blogPicExt FROM blogDescriptions WHERE id = ?");
	$stmt->execute(array(
		$getId
	));
	while ($row = $stmt->fetch()) {
		$a1 = html_entity_decode($row['blogDesc'], ENT_QUOTES);
		$a2 = htmlspecialchars(trim($a1));
		$articleText = nl2br(substr($a2, 0, 500));
		$id = $row['userId'];
		$blogTitle = $row['blogTitle'];
		$blogPic = $row['blogPic'];
		$blogPicExt = $row['blogPicExt'];
		echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='index.php?page=Blog&blogUserId=$id'>";
		echo "<header style='font-weight:bold; text-align:center; margin-bottom:10px; font-size:1.25em;'>$blogTitle</header>\n";
		if (file_exists("userPics/$id/thumb/$blogPic.$blogPicExt")) {
			echo "<img src='userPics/$id/thumb/$blogPic.$blogPicExt' alt='' style='margin:0px 10px 10px 0px; float:left;' />";
		}
		echo "<article style='text-align:justify;'>$articleText</article></a></article>\n";
	}
}
function displayWriting($bookId, $authorId, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT title FROM myWritings WHERE bookId = ? AND authorId = ?");
	$stmt->execute(array(
		$bookId,
		$authorId
	));
	$row = $stmt->fetch();
	$title = $row['title'];
	echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='index.php?page=Writings&author=$authorId&book=$bookId'>";
	echo "<header style='color:#222222; text-align:center; margin-bottom:10px; font-size:.75em;'>Story</header>\n";
	echo "<article style='text-align:justify; font-weight:bold;'>$title</article></a></article>\n";
}
function displayBusiness($getId, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT userId, busiName, busiPhone, busiPic1, busiPicExt1 FROM busiListing WHERE id = ?");
	$stmt->execute(array(
		$getId
	));
	while ($row = $stmt->fetch()) {
		$buserId = $row['userId'];
		$busiName = $row['busiName'];
		$busiPhone = $row['busiPhone'];
		$busiPic1 = $row['busiPic1'];
		$busiPicExt1 = $row['busiPicExt1'];
		echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; box-shadow: 5px 5px 5px grey;'>\n";
		echo "<a style='color:black;' href='index.php?page=BusinessDetail&business=$buserId'><div style='text-align:center; font-weight:bold; padding:10px;'><span style='font-size:1.5em;'>$busiName</span><br /><span style='font-size:1.25em;'>$busiPhone</span>";
		if (file_exists("userPics/$buserId/$busiPic1.$busiPicExt1")) {
			echo "<br /><br /><img src='userPics/$buserId/$busiPic1.$busiPicExt1' alt='' style='max-width:199px; max-height:199px; padding:2px; border:1px solid $highlightColor; margin:5px;'/>";
		}
		echo "</div></article>\n";
	}
}
function displayClassified($getId, $db, $highlightColor)
{
	$stmt = $db->prepare("SELECT * FROM classifieds WHERE id=?");
	$stmt->execute(array(
		$getId
	));
	$row = $stmt->fetch();
	$userId = $row['userId'];
	$classifiedTitle = $row['classifiedTitle'];
	$classifiedText = nl2br(make_links_clickable(html_entity_decode($row['classifiedText'], ENT_QUOTES), $highlightColor));
	$chrlen = $row['classifiedTextLength'];
	$picName = $row['picName'];
	$picExt = $row['picExt'];
	$catId = $row['catId'];
	$stmt3 = $db->prepare("SELECT category FROM classifiedCategories WHERE id=?");
	$stmt3->execute(array(
		$catId
	));
	$row3 = $stmt3->fetch();
	$category = $row3['category'];
	echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; box-shadow: 5px 5px 5px grey;'>\n";
	echo "<div style='font-size:0.75em; text-align:center;'>" . $category . "</div><br />";
	echo "<header style='font-weight:bold; text-align:center; margin-bottom:10px;'>$classifiedTitle</header>";
	if ($chrlen >= 251) {
		if (file_exists("userPics/$userId/$picName.$picExt")) {
			echo "<div style='margin:auto; width:150px; float:left;'><img src='userPics/$userId/$picName.$picExt' alt='' style='max-width:150px; max-height:150px;' /></div><br />";
		}
	}
	echo "<article style='text-align:justify;'>$classifiedText</article></article>\n";
}
function displayDirectory($getId, $db, $highlightColor)
{
	$statement = $db->prepare("SELECT userId, firstName, lastName, businessName, phone1, phone1Desc, phone2, phone2Desc, email, address1, address2 FROM directory WHERE showListing=? && id=? ORDER BY lastName, businessName");
	$statement->execute(array(
		'1',
		$getId
	));
	$row6 = $statement->fetch();
	$userId = $row6['userId'];
	$firstName = $row6['firstName'];
	$lastName = $row6['lastName'];
	$businessName = $row6['businessName'];
	$phone1 = $row6['phone1'];
	$phone1Desc = $row6['phone1Desc'];
	$phone2 = $row6['phone2'];
	$phone2Desc = $row6['phone2Desc'];
	$email = $row6['email'];
	$address1 = $row6['address1'];
	$address2 = $row6['address2'];

	echo "<article class='pageBoxesThird' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:220px; box-shadow: 5px 5px 5px grey;'>\n";
	echo "<div style='cursor:pointer;' onclick='toggleview(\"dir$getId\")'>";
	if ($row6['firstName'] || $row6['lastName']) {
		echo "$lastName, $firstName";
	}
	if (($row6['firstName'] || $row6['lastName']) && $row6['businessName']) {
		echo "<div style='height:3px; width:50px; background-color:#dddddd; border:1px solid $highlightColor; margin:5px auto;'></div>";
	}
	if ($row6['businessName']) {
		$busiL = $db->prepare("SELECT businessListing FROM users WHERE id=?");
		$busiL->execute(array(
			$userId
		));
		$busiLrow = $busiL->fetch();
		echo ($busiLrow['businessListing'] == '1') ? "</div><div><a href='index.php?page=BusinessDetail&business=$userId'>$businessName</a>" : $businessName;
	}
	echo "</div>\n<div style='text-align:left; font-size:1.0em; display:none; padding:5px;' id='dir$getId'>\n<div style='margin:auto; width:100px; height:2px; background-color:#555555;'></div><br />";
	if ($row6['phone1']) {
		echo "$phone1 $phone1Desc<br />";
	}
	if ($row6['phone2']) {
		echo "$phone2 $phone2Desc<br />";
	}
	if ($row6['email']) {
		echo "<br /><a href='mailto:$email'>$email</a><br />";
	}
	if ($row6['address1']) {
		echo "<br />$address1<br />";
	}
	if ($row6['address2']) {
		echo "$address2<br />";
	}
	echo "</div></article>\n";
}
function isArticleReported($checkId, $db)
{
	$stmt = $db->prepare("SELECT COUNT(*) FROM reported WHERE reportedBy != '0' && articleId = ? && reportedTime >= '1' && clearedTime = '0'");
	$stmt->execute(array(
		$checkId
	));
	$row = $stmt->fetch();
	if ($row[0] >= 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}
function getReplies($getId, $db)
{
	$gr = $db->prepare("SELECT t1.id, t1.articleTitle, t1.postedDate, t2.firstName, t2.lastName FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.inReplyTo = ? ORDER BY t1.postedDate");
	$gr->execute(array(
		$getId
	));
	while ($grow = $gr->fetch()) {
		$Aid = $grow['id'];
		$artTitle = $grow['articleTitle'];
		$artPosted = $grow['postedDate'];
		$artFN = $grow['firstName'];
		$artLN = $grow['lastName'];
		echo (isArticleReported($Aid, $db)) ? "<div style='margin:10px 0px 0px 10px;'>- *REPORTED* $artTitle<br />\t<span style='font-size:.75em;'>" . date("M j, Y", $artPosted) . "\t by $artFN $artLN</span><br />" : "<div style='margin:10px 0px 0px 10px;'><a href='index.php?page=Articles&articleDetail=$Aid'>- $artTitle<br />\t<span style='font-size:.75em;'>" . date("M j, Y", $artPosted) . "\t by $artFN $artLN</span></a><br />";
		getReplies($Aid, $db);
		echo "</div>";
	}
}
function cashOut($userId, $db)
{
	$co1 = $db->prepare("SELECT coinQty, cashOut FROM HHUsers WHERE userId = ?");
	$co1->execute(array(
		$userId
	));
	$co1R = $co1->fetch();
	$cq = $co1R['coinQty'];
	$co = $co1R['cashOut'];
	$tot = ($cq + $co);

	$co2 = $db->prepare("UPDATE HHUsers SET coinQty = ?, cashOut = ? WHERE userId = ?");
	$co2->execute(array(
		'0',
		$tot
	));
}
function money($amt)
{
	settype($amt, "float");
	$fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
	return $fmt->formatCurrency($amt, "USD");
}

function slugify($urlString)
{
	$url = html_entity_decode($urlString, ENT_QUOTES);
	$search = ['Ș', 'Ț', 'ş', 'ţ', 'Ş', 'Ţ', 'ș', 'ț', 'î', 'â', 'ă', 'Î', ' ', 'Ă', 'ë', 'Ë'];
	$replace = ['s', 't', 's', 't', 's', 't', 's', 't', 'i', 'a', 'a', 'i', ' ', 'a', 'e', 'e'];
	$str = str_ireplace($search, $replace, strtolower(trim($url)));
	$str = preg_replace('/[^\w\d\-\ ]/', '', $str);
	$str = str_replace(' ', '-', $str);
	return preg_replace('/\-{2,}/', '-', $str);
}
