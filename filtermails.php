<?php 
if(isset($_POST["submit"])) {
$filename = basename($_FILES["fileToUpload"]["name"], ".txt"); 
$string		=	file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
$pattern	=	"/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";

$emails_found = [];

$myfile;
$txt = "";

preg_match_all($pattern, $string, $matches);

//Finding email domains contained in the text file

foreach($matches[0] as $email){
    $val = explode ("@", $email);
    
    array_push($emails_found,$val[1]);

  /*  if (str_contains($val[1],'gmail')){
        echo $email."\t";
    }
    else echo "N\A"."\t";	 */
}

echo "<br>-------------------------------------------------<br>";

$domains = array_unique($emails_found);
//print_r($domains);

// writing files

foreach ($domains as $domain){

    $txt = "";
    $myfile =  fopen("./output/".$domain.".txt", "w") or die("Unable to open file!");
    foreach($matches[0] as $email){
        $val = explode ("@", $email);
        if (str_contains($val[1],$domain)){
            $txt.=$email."\n";
        }
    }
    fwrite($myfile, $txt);
    fclose($myfile);
}



// Get real path for our folder
$rootPath = realpath('./output/');

/// Initialize archive object
$zip = new ZipArchive();
$zip->open('emails.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

/// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

/// Zip archive will be created only after closing object
$zip->close();


/// Downloading the Zip 

$filedownload = "emails.zip";

header('Content-type: application/zip');
header("Content-Type: ".mime_content_type($filedownload));
header("Content-Disposition: attachment; filename=".$filedownload);
while (ob_get_level()) {
    ob_end_clean();
}
readfile($filedownload);
unlink($filedownload);

//Deleting the files in output folder
if(file_exists($rootPath)){

    
    $di = new RecursiveDirectoryIterator($rootPath, FilesystemIterator::SKIP_DOTS);
    $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ( $ri as $file ) {
        $file->isDir() ?  rmdir($file) : unlink($file);
    }
}
}
else echo "<script> alert('not work')</script>";
?>