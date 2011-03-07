<html><body>
<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);

require_once 'Github/Autoloader.php';
Github_Autoloader::register();

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);

$user = "tiefpunkt";
$repo = "Moodlamp";
$branch = "china_remote";

$github = new Github_Client();
$commits = $github->getCommitApi()->getBranchCommits($user, $repo, $branch);

echo "<img src=\"http://www.gravatar.com/avatar/".md5($commits[0]["author"]["email"])."?s=60\"><br />";
echo "Author: " . $commits[0]["author"]["name"] . "<br />";
echo "Login: " . $commits[0]["author"]["login"] . "<br />";
echo "eMail: " . $commits[0]["author"]["email"] . "<br />";
echo "Message: " . $commits[0]["message"] . "<br />";

$commit = $github->getCommitApi()->getCommit($user, $repo, $commits[0]["id"]);
echo "Modified: " . (array_key_exists("modified", $commit)?count($commit["modified"]):"0") . "<br />";
echo "Added: " . (array_key_exists("added", $commit)?count($commit["added"]):"0") . "<br />";
echo "Removed: " . (array_key_exists("removed", $commit)?count($commit["removed"]):"0") . "<br />";
/*
echo "<pre>";
var_dump($commit);
echo "</pre>";
*/
?>
</body></html>
