<?php
    /**
    * Generating random Unique MD5 String for user Api key
    */
    function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
    
    
    /**
     * Verify if a picture file is correct (size/type)
     * @param string $file
     * @return boolean
     */
    function verifyUploadPicture($file) {
        $allowedExtensions = array("gif", "jpeg", "jpg", "png");
        $temp = explode(".", $file['name']);
        $extension = end($temp);
        $allowedFormats = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png');
        if($file['error'] == 0) {
            if(in_array($file['type'], $allowedFormats) && in_array($extension, $allowedExtensions)) {
                if($file['size'] < MAX_SIZE_PICTURE) return true;
            }
        } return false;
    }
    
    /**
     * Save a picture file
     * @param array $file
     * @param string $output
     * @return boolean
     */
    function savingUploadedFile($file, $output) {
        if(file_exists(PATH_ROOT . "/" . PATH_IMG ."/" . $output)) return false;
        else {
          if(move_uploaded_file($file['tmp_name'], PATH_ROOT . "/" . PATH_IMG ."/" . $output)) return true;
        }
    }
    
    /**
     * Delete file from disk
     * @param string $name
     * @return boolean
     */
    function deletePictureFromDisk($name) {
        if(!file_exists(PATH_ROOT . "/" . PATH_IMG ."/" . $name)) return false;
        return unlink(PATH_ROOT . "/" . PATH_IMG ."/" . $name);
    }
    
    /**
     * Get the dif between a date and now in hour
     * @param DateTime $date
     * @return int (h)
     */
    function getDiffHour($date) {
        $current = new DateTime();
        $current->setTimestamp(time());
        $res = new DateTime($date);
        $diff = $current->diff($res);
        return $diff->h;
    }
    
    /**
     * Delete all the user's pictures
     * @param int $id
     */
    function deletePicturesFromDiskByUser($id) {
        foreach (glob(PATH_ROOT . "/" . PATH_IMG ."/" . $id . "_*") as $filename) {
            unlink($filename);
        }
    }

