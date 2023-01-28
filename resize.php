<style>
.mybtn {
  
  background-color: black;
  color: white;
  text-align: center;
  padding: 5px 5px;
  border-radius:5px;
  text-decoration: none;
  cursor:pointer;
}

.mybtn:hover {
  background-color: white;
  color: black;
}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Resize Image</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  box-sizing: border-box;
}

body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
}

.navbar {
  overflow: hidden;
  background-color: #333;
  position: sticky;
  position: -webkit-sticky;
  top: 0;
}

/* Style the navigation bar links */
.navbar a {
  float: left;
  display: block;
  color: white;
  text-align: center;
  padding: 14px 20px;
  text-decoration: none;
}

.mybtn {
  
  background-color: black;
  color: white;
  text-align: center;
  padding: 5px 5px;
  border-radius:5px;
  text-decoration: none;
  cursor:pointer;
}

.mybtn:hover {
  background-color: white;
  color: black;
}

.navbar a.right {
  float: right;
}

.navbar a:hover {
  background-color: #ddd;
  color: black;
}

.navbar a.active {
  background-color: #666;
  color: white;
}

/* Column container */
.row {  
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
}
.main {   
  -ms-flex: 70%; /* IE10 */
  flex: 70%;
  background-color: white;
  padding: 20px;
  align-items:center;
  display:flex;
  flex-direction:column;
  justify-content:center;
}

form {   
  
  align-items:left;
  display:flex;
  flex-direction:column;
  justify-content:center;
}

.footer {
  padding: 20px;
  text-align: center;
  background: #ddd;
}
@media screen and (max-width: 700px) {
  .row {   
    flex-direction: column;
  }
}
@media screen and (max-width: 400px) {
  .navbar a {
    float: none;
    width: 100%;
  }
}
</style>
</head>
<body>



<div class="navbar">
  <a href="https://compressimages.000webhostapp.com/" class="active">Home</a>
</div>

<div class="row">
  <div class="main">
    <h2>Image Resize</h2>
    <h5>Upload images and resize instantly.</h5>
    
    <?php
//you can set this as per your requirements
        $qualityJpg = !empty($_POST['percentage']) &&  $_POST['percentage'] > 10 ? $_POST['percentage'] : 50;
        $qualityPng = !empty($_POST['percentage']) &&  $_POST['percentage'] > 10 ? $_POST['percentage']/10 : 5;
        $target_width = 1024;
        
        
        $filename =  "resized_".pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME). '.' . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        
        $tempname = $_FILES["image"]["tmp_name"];

        $target_file = "images/".$filename;
        
        if (move_uploaded_file($tempname, $target_file)) {
            echo "Image uploaded successfully: " . $target_file."<br>";
        } else {
           echo "Failed to upload image: " . $target_file."<br>";
        }
        
        //source_folder + file_name (string)
        $source = $tempname;
        //targete_folder + file_name (string)
        $target = $target_file;
        
        //If PNG image create resource / object using imagecreatefrompng
        if ((exif_imagetype($target_file) == IMAGETYPE_PNG)) {
            $imageObj = imagecreatefrompng($target_file);
        } elseif((exif_imagetype($target_file) == IMAGETYPE_JPEG)) { //If JPG image create resource / object using imagecreatefromjpeg
            $imageObj = imagecreatefromjpeg($target_file);
        } else {
            die;
        }
        
        if (!$imageObj)
        {
            echo "resizeImage: $target_file - Unable to resize!"."<br>";
            die;
        }
        
        list($origWidth, $origHeight) = getimagesize($target);
        
        if ($origWidth <= $target_width) {
            die;
        }
        
        $maxWidth = $target_width;
        $maxHeight = 0;
        if ($maxWidth == 0)
        {
            $maxWidth  = $origWidth;
        }
        if ($maxHeight == 0)
        {
            $maxHeight = $origHeight;
        }
        
        $widthRatio = $maxWidth / $origWidth;
        $heightRatio = $maxHeight / $origHeight;
        $ratio = min($widthRatio, $heightRatio);
        $newWidth  = (int)$origWidth  * $ratio;
        $newHeight = (int)$origHeight * $ratio;
        
        //creation of new image
        if ((exif_imagetype($target) == IMAGETYPE_PNG)) {
            $newImage = imagecreatetruecolor((int)$newWidth, (int)$newHeight);
            $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $transparent);
            imagesavealpha($newImage, true);
            imagecopyresampled($newImage, $imageObj, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            $return_result = imagepng($newImage, $target, $qualityPng);
        } elseif((exif_imagetype($target) == IMAGETYPE_JPEG)) {
            $newImage = imagecreatetruecolor((int)$newWidth, (int)$newHeight);
            imagecopyresampled($newImage, $imageObj, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            $return_result = imagejpeg($newImage, $target, $qualityJpg);
        } else {
            die;
        }
        
        if (TRUE == $return_result) {
            echo "Resized SUCCESSFULLY!"."<br>";
        } else {
            echo "NOT resized!"."<br>";
        }
        
        imagedestroy($imageObj);
        imagedestroy($newImage);
        
        echo "Completed"."<br>";
        if (file_exists($target)) {
            ?>
            	<a href="<?php echo $target;?>" download class="mybtn"> Download </a>
            <?php
        }
?>

  </div>
</div>

<div class="footer" style="position:fixed;bottom:0px;width:100%;background:black;color:white">
  Free to use. We don't store anything.
</div>

</body>
</html>
