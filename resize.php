<?php
//you can set this as per your requirements
        $qualityJpg = 80;
        $qualityPng = 8;
        $target_width = 1024;
        
        
        $filename =  "resized_".pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME). '.' . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        
        $tempname = $_FILES["image"]["tmp_name"];

        $target_file = "images/".$filename;
        
        if (move_uploaded_file($tempname, $target_file)) {
            echo "Image uploaded successfully: " . $target_file;
        } else {
           echo "Failed to upload image: " . $target_file;
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
            echo "resizeImage: $target_file - Unable to resize!\r\n";
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
            echo "Resized SUCCESSFULLY!\r\n";
        } else {
            echo "NOT resized!\r\n";
        }
        
        imagedestroy($imageObj);
        imagedestroy($newImage);
        
        echo "resizeImage: Completed\r\n";
        if (file_exists($target)) {
            ?>
            	<a href="<?php echo $target;?>" download> Download </a>
            <?php
        }
?>
