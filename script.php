<?php
if(isset($_POST["submit"])) {
echo "<script> alert('works')</script>";
$filename = basename($_FILES["fileToUpload"]["name"], ".xml"); 
$myfile = fopen($filename.".txt", "w") or die("Unable to open file!");
$txt = "";
$xml=simplexml_load_file($_FILES["fileToUpload"]['tmp_name']) or die("Error: Cannot create object");

foreach ($xml->data->children() as $data)
{
    //add each sub element in xml file
    $txt .= $data->rcpt ."\n";
}

fwrite($myfile, $txt);
fclose($myfile);

//$myfile = fopen($filename.".txt", "r") or die("Unable to open file!");

//echo fread($myfile,filesize($filename.".txt"));

//fclose($myfile);


$filedownload = $filename.".txt";


header('Content-type: application/octet-stream');
header("Content-Type: ".mime_content_type($filedownload));
header("Content-Disposition: attachment; filename=".$filedownload);
while (ob_get_level()) {
    ob_end_clean();
}
readfile($filedownload);
unlink($filename.".txt");
}
else echo "<script> alert('not work')</script>";
?>