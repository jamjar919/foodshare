<?php
define('__ROOT__',dirname(dirname(__FILE__)));
//TODO database connection file required
header('Content-Type: application/json');

    $uid = $_POST['uid'];
    $client_id = "dc753be426e679a";
    $c_url = curl_init();

    $file = file_get_contents($_FILES["imgupload"]["tmp_name"]);
    $filename = basename($_FILES["imgupload"]["name"]); // get file name
    $extension = pathinfo($filename, PATHINFO_EXTENSION);// get file extension

    // check file extension
    if($extension !== "jpg" && $extension !== "png" && $extension !== "jpeg") {
        echo "here";
        echo json_encode(array('error' => 'File must be jpg, jpeg or png'));
    }
    // if file is greater than 10Mb return error
    else if ($_FILES["imgupload"]["size"] > 100000000) {
        $upload = 0;
        echo json_encode(array('error' =>'file is too large'));
    }
    else {
        $url = 'https://api.imgur.com/3/image.json';
        $headers = array("Authorization: Client-ID $client_id");

        curl_setopt($c_url, CURLOPT_URL, $url);
        curl_setopt($c_url, CURLOPT_POST, TRUE);
        curl_setopt($c_url, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($c_url, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c_url, CURLOPT_POSTFIELDS, array('image' => base64_encode($file)));

        $result = curl_exec($c_url);
        if ($error = curl_error($c_url)) {
            die('cURL error:' . $error);
        }

        curl_close($c_url);
        $json_decode_array = json_decode($result, true);
        $data = $json_decode_array['data'];

        // store the image url the database
        update_profile_picture($uid, $data['link']);

    }


/**
 * Update a user's profile picture
 *
 * @param string $uid userid of the user which is changing their profile picture
 * @param string $url url of the image
 *
 * @return boolean True if the url is succesfully added and False if it does not
 */
function update_profile_picture($username, $url) {
        try {
            $stmt = db->prepare("UPDATE users SET image_url = :url WHERE username = :username");
            $stmt->bindValue(":url", $url, PDO::PARAM_STR);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
?>