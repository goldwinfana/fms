<?php
include './../includes/session.php';

$conn = $pdo->open();

if (isset($_POST['stasus_id'])) {

    $stmt = $conn->prepare("SELECT * FROM lessor WHERE id=:id");
    $stmt->execute(['id' => $_SESSION['admin']]);
    $row = $stmt->fetch();

    echo json_encode($row);
}

if (isset($_POST['userid'])) {
    $id = $_POST['userid'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM lessor WHERE email=:email AND id <>:id");
    $stmt->execute(['email'=>$email, 'id'=>$id]);
    $row = $stmt->fetch();
    if($row['numrows'] > 0){
        $_SESSION['error'] = 'Email already exits';
    }
    else {

        $stmt = $conn->prepare("UPDATE lessor SET email=:email,
    password=:password, first_name=:first_name, last_name=:last_name WHERE id=:id");
        $stmt->execute(['email' => $email, 'password' => $password, 'first_name' =>
            $firstname, 'last_name' => $lastname, 'id' => $id]);

        $_SESSION['success'] = 'Venue updated successfully';
    }
    header('location: welcome.php');
}

if (isset($_POST['course_id'])) {

    $stmt = $conn->prepare("SELECT * FROM course");
    $stmt->execute();
    $row = $stmt->fetch();

    echo json_encode($row);
}

if(isset($_POST['edit'])) {
    $address = $_POST['address'];
    $type = $_POST['type'];
    $size = $_POST['size'];
    $salary = $_POST['salary'];


    try{
        $stmt = $conn->prepare("INSERT INTO space(address, image,type,size,salary,lessor_id,status) VALUES(:address, :image, :type_id,:size,:salary,:lessor_id,:status)");
        $stmt->execute(['address' => $address, 'image' => $image, 'type' =>
            $type, 'size' => $size, 'salary' => $salary,'lessor_id'=>$_SESSION['admin'],'status'=>0]);
        $_SESSION['success'] = 'Space updated successfully';
    }
    catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
    }


    header('Location: '.$_SERVER['HTTP_REFERER']);
}


if(isset($_POST['lease'])) {
    $address = $_POST['address'];
    $type = $_POST['type'];
    $size = $_POST['size'];
    $salary = $_POST['salary'];


    $photo = $_FILES['photo']['name'];

    $target_dir = "C:/xampp/htdocs/HotDesk/venues/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $_SESSION['error'] = "File is not an image.";
            $uploadOk = 0;
            header('location:' .$_SERVER['HTTP_REFERER']);
        }
    }

// Check if file already exists
    if (file_exists($target_file)) {
        $_SESSION['error'] = "Sorry, file already exists.";
        $uploadOk = 0;
        header('location:' . $_SERVER['HTTP_REFERER']);
    }

// Check file size
    if ($_FILES["photo"]["size"] > 5000000) {
        $_SESSION['error'] = "Sorry, your file is too large.";
        $uploadOk = 0;
        header('location:' . $_SERVER['HTTP_REFERER']);
    }

// Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
        header('location:' . $_SERVER['HTTP_REFERER']);
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['error'] = "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["photo"]["name"])) . " has been uploaded.";
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header('location:' . $_SERVER['HTTP_REFERER']);
        }
    }

    try{
        $stmt = $conn->prepare("INSERT INTO space(address, image,type_id,size,price,lessor_id,status_id) VALUES(:address, :image, :type,:size,:salary,:lessor_id,:status_id)");
        $stmt->execute(['address' => $address, 'image' => $photo, 'type' =>
            $type, 'size' => $size, 'salary' => $salary,'lessor_id'=>$_SESSION['admin'],'status_id'=>0]);
        $_SESSION['success'] = 'Space updated successfully';
    }
    catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
    }


    header('Location: '.$_SERVER['HTTP_REFERER']);
}

if(isset($_POST['decline'])){
    $id = $_POST['decline'];

    try{
        $stmt = $conn->prepare("UPDATE booking SET status_id=2 WHERE book_id=:id");
        $stmt->execute(['booking_id'=>$id]);

        $_SESSION['success'] = 'Application removed successfully';
    }
    catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: '.$_SERVER['HTTP_REFERER']);

}

if(isset($_POST['approve'])){
    $id = $_POST['approve'];

    try{
        $stmt = $conn->prepare("UPDATE booking SET status_id=1 WHERE book_id=:id");
        $stmt->execute(['booking_id'=>$id]);

        $_SESSION['success'] = 'Application accepted successfully';
    }
    catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
    }
    return 0;

}


if(isset($_POST['id_delete'])){
    $id = $_POST['id_delete'];

    try{
        $stmt = $conn->prepare("DELETE FROM lessor WHERE id=:id");
        $stmt->execute(['id'=>$id]);

        $_SESSION['success'] = 'Student deleted successfully';
    }
    catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: students.php');

}
$pdo->close();

?>