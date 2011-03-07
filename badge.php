<?php
require_once 'Github/Autoloader.php';
Github_Autoloader::register();

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);

function insert_image_png( &$image, $url, $posx, $posy ) {
	$src_image = imagecreatefrompng( $url );
	$src_width = imagesx( $src_image );
	$src_height = imagesy( $src_image );
	imagecopymerge( $image, $src_image, $posx, $posy, 0, 0, $src_width, $src_height  , 100);
}
function insert_image_jpeg( &$image, $url, $posx, $posy ) {
	$src_image = imagecreatefromjpeg( $url );
	$src_width = imagesx( $src_image );
	$src_height = imagesy( $src_image );
	imagecopy( $image, $src_image, $posx, $posy, 0, 0, $src_width, $src_height );
}
function truncate($string, $width) {
	if (strlen($string) <= $width) {
		$string = $string; 
	}
	else {
		$width = $width - 5;
		$string = wordwrap($string, $width);
		$string = substr($string, 0, strpos($string, "\n")) . "[...]";
	}
	return $string;
}

if (!isset($_GET["user"]) || !isset($_GET["repo"])) {
	$user = "tiefpunkt";
	$repo = "github-img-badge";
} else {
	$user = $_GET["user"];
	$repo = $_GET["repo"];
}

if (!isset($_GET["branch"])) {
	$branch = "master";
} else {
	$branch = $_GET["branch"];
}

$github = new Github_Client();
$commits = $github->getCommitApi()->getBranchCommits($user, $repo, $branch);
$commit = $github->getCommitApi()->getCommit($user, $repo, $commits[0]["id"]);

$author = $commits[0]['author']['name'];
$message = "\"".truncate($commits[0]["message"],120)."\"";
$avatar_url = "http://www.gravatar.com/avatar/".md5(strtolower( trim( $commits[0]["author"]["email"])))."?s=60&d=mm";
$datetime = strtotime($commits[0]["committed_date"]);
$datestr = date("D, M jS Y", $datetime);

$added = (array_key_exists("added", $commit)?count($commit["added"]):"0");
$modified = (array_key_exists("modified", $commit)?count($commit["modified"]):"0") ;
$removed = (array_key_exists("removed", $commit)?count($commit["removed"]):"0");
$changes = $added." added, ".$modified." modified, ".$removed." removed";

$my_img = imagecreatetruecolor( 500, 100 );
$background = imagecolorallocate( $my_img, 0xdd, 0xff, 0xff );
$color_black = imagecolorallocate( $my_img, 0x00, 0x00, 0x00 );
$color_grey = imagecolorallocate( $my_img, 0x66, 0x66, 0x66 );
$color_blue = imagecolorallocate( $my_img, 0x33, 0x99, 0xFF );

imagesetthickness( $my_img, 1 );
imagefilledrectangle( $my_img, 0, 0, 499, 99, $background );
imagerectangle( $my_img, 0, 0, 499, 99, $color_grey );

insert_image_png( $my_img, './img/public.png', 9, 6 );
insert_image_png( $my_img, './img/commit.png', 79, 50 );
insert_image_png( $my_img, './github.png', 420, 62 );
insert_image_jpeg( $my_img, $avatar_url, 10, 28 );

//imagestring( $my_img, 5, 10, 5, $user . "/" . $repo, $color_black );
imagefttext( $my_img, 12, 0, 32, 20, $color_blue, './fonts/DejaVuSans-Bold.ttf', $user . "/" . $repo );
imagefttext( $my_img, 11, 0, 80, 42, $color_black, './fonts/DejaVuSans.ttf', $author . " made a commit on ".$datestr );
imagefttext( $my_img, 10, 0, 100, 62, $color_black, './fonts/DejaVuSans-Oblique.ttf', $message );
imagefttext( $my_img, 10, 0, 80, 85, $color_black, './fonts/DejaVuSans.ttf', $changes );


header( "Content-type: image/jpeg" );
imagejpeg( $my_img );
/*
imagecolordeallocate( $color_black );
imagecolordeallocate( $background );
imagedestroy( $my_img );
*/
?>
