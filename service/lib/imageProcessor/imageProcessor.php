<?php

class imageProcessor {
    
    public $defaultImageName; // original name of file
    public $defaultImageFileSize; // original image file size
    public $defaultImageType; // default image type (jpg/jpeg)
    public $defaultImageTempLocation; // default temporary file location
    public $defaultImageExtension; // default image extension
    public $providedImageName; // the name provided by the calling file that will be the name of the image to be saved in database
    public $uploadPath; // the path that the image will be uploaded;
    public $imageWidth;
    public $imageHeight;
    public $newNameCount = 0;
    
    public function uploadFile ($mainImageName, $imageTempLocation)
    {
        if (!is_dir($this -> uploadPath)) mkdir($this -> uploadPath, 0777, true); // this will create directory
        if (opendir($this -> uploadPath) == true) 
        {
            $uploadDir = $this->uploadPath."/".$mainImageName;
            if (move_uploaded_file($imageTempLocation, $uploadDir) == true){
                unset($imageTempLocation);
                return true;
            } else {
                return false;
            }
        }
        
    }
    
    public function imageResize($targerImageLocation, $resizedFileLocation, $widthMax, $heightMax)
    {
        ini_set('memory_limit', '128M');
        $targerImage = $targerImageLocation;
        $resizedFile = $resizedFileLocation;
        list($originalImageWidth, $originalImageHeight) = getimagesize($targerImage);
        $scaleRatio = $originalImageWidth / $originalImageHeight;
        
        if (($widthMax / $heightMax) > $scaleRatio) {
            $widthMax = $heightMax * $scaleRatio;
        } else {
            $heightMax = $widthMax / $scaleRatio;
        }
        
        $image = "";
        
        $imageExtension = $this  -> defaultImageExtension;
        
        if ($imageExtension == "gif" || $imageExtension == "GIF") {
            $image = imagecreatefromgif($targerImage);
        } else if ($imageExtension == "png" || $imageExtension == "PNG") {
            $image = imagecreatefrompng($targerImage);
        } else $image = imagecreatefromjpeg($targerImage);
        
        $tci = imagecreatetruecolor($widthMax, $heightMax);
        imagecopyresampled($tci, $image, 0, 0, 0, 0, $widthMax, $heightMax, $originalImageWidth, $originalImageHeight);
        imagejpeg($tci, $resizedFile, 80);
        
     }
    
    public function createNewName()
    { 
        /* 
            this function appends the value of $newNameCount to 
            $this -> mainImageName and calls back the fileExistence 
            Function with the new name as a parameter
        */
        $newNameCount = $this -> newNameCount + 1;
        $this -> providedImageName = $newNameCount.$this -> providedImageName;
        $this -> fileExistence();
    }
    
    public function fileExistence()
    {
        $mainImagePath = $this -> uploadPath.$this -> providedImageName;
    
        if (file_exists($mainImagePath) == true) {
            $this -> createNewName();
        } else $this -> defaultIMageName = $this -> providedImageName; return $this -> providedImageName;//true means it the file doesn't exist        
    }
    
    public function getImagePixel ()
    {
        if ($this -> defaultImageTempLocation == true)
        {
            $image_pixel = getimagesize($this -> defaultImageTempLocation); // this is to get image pixel
            $this -> imageWidth = $image_pixel[0];// this is to get image width from  $image_pixel
            $this -> imageHeight = $image_pixel[1];// this is to get image height from  $image_pixel    
        }
        
    }
    
    public function checkImageSizeKB($expectedImageSize)
    {
        // $expectedImageSize is the maximum file size to be uploaded;
        // checking the size of the image in kilo byte
        $imageFileSize = $this -> defaultImageFileSize;
        $kikibyte = round($imageFileSize / 1024); // measuring in kilobyte
        
        if ($kikibyte > $expectedImageSize) {
            return false;
        } else return true;
        
    }
    
    public function checkImagesizePx ($lowerBound, $higherBound)
    {
        // checking the size of the image in pixel(image width)
        // $expectedWidthSize is the maximum width size to the image be uploaded;
        if ($this -> imageWidth < $lowerBound || $this -> imageWidth > $higherBound)
        {
            return false;
        } else return true;  
    }
    
    function imageDetails ($imageArray, $uploadPath)
    {
        $this -> defaultImageName = $imageArray[0];
        $this -> defaultImageFileSize = $imageArray[1];
        $this -> defaultImageType = $imageArray[2];
        $this -> defaultImageTempLocation = $imageArray[3];
        $this -> defaultImageExtension = $imageArray[4];
        $this -> providedImageName = $imageArray[5].".".$imageArray[4]; // this is the name of the image created by external file(caller script)
        $this -> uploadPath = $uploadPath;
        $this -> getImagePixel (); // this is to get the image Pixel
    }
    

    public function imageProcessorEntry($imageDataArray, $uploadPath, $customerObject)
    {
        # code...
         // pass in image details
        $this -> imageDetails ($imageDataArray, $uploadPath);

        // check image size
        $imageSizeCheck = $this -> checkImageSizeKB(500);

        $customerObject -> profilePicture = $this -> fileExistence();
        $imageTempLoc = $this -> defaultImageTempLocation;
        $imageExtension = $this -> defaultImageExtension;

        // upload file 
        $uploadDpImage = $this -> uploadFile ($customerObject -> profilePicture, $imageTempLoc);

        if ($uploadDpImage) {

            if ($imageSizeCheck == false) {
                $widthMax = 700;
                $heightMax = 500;
                $targetImage = $uploadPath.$customerObject -> profilePicture;

                $resizedFileLocation = $uploadPath."dpimage".$customerObject -> profilePicture;
                    
                $customerObject -> profilePicture = "dpimage".$customerObject -> profilePicture;
                
                $this -> imageResize($targetImage, $resizedFileLocation, $widthMax, $heightMax);
                unlink($targetImage);
            }

        } else {
            http_response_code(200);
            return json_encode(array(
                "message" => "An error occured uploading your profile picture",
                "status" => "false"
            ));
            
        }

    }

    function __construct ()
    {
        ini_set('memory_limit', '128M');
    }
    
}

?>