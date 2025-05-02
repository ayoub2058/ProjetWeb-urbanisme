<?php
// Read the file
$contactFile = file_get_contents('contact.php');

// Remove style blocks
$pattern = '/<style>.*?<\/style>/s';
$contactFile = preg_replace($pattern, '', $contactFile);

// Write the modified file back
file_put_contents('contact.php', $contactFile);

echo "Style blocks removed successfully.\n";
?> 