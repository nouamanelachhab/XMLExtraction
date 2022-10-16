<?php 

if(isset($_POST["submit"])) {
    $filename = $_FILES["fileToUpload"]["name"];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if ($ext == 'xml') {
        require 'generatemails.php';
    }
    elseif($ext == 'txt'){
        require 'filtermails.php';
    }

}

?>