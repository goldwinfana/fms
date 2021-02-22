<?php
include 'includes/session.php';

session_start();
$conn = $pdo->open();

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    try{

        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows1 FROM admin WHERE email = :email");
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch();

        if($row['numrows1'] > 0){
            if($password == $row['password']){
                $_SESSION['user'] = 'admin';
                $_SESSION['name'] = $row['name'];
                $_SESSION['admin'] = $row['id'];
                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $row['email'];
            }
            else{
                $_SESSION['error'] = 'Incorrect Password';
                header('location: login.php');
            }
        }

        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows1 FROM farmer WHERE email = :email");
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch();

        if($row['numrows1'] > 0){
            if($password == $row['password']){
                $_SESSION['user'] = 'farmer';
                $_SESSION['admin'] = $row['id'];
                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $row['email'];
                $_SESSION['name'] = $row['firstName'];
                $_SESSION['surname'] = $row['lastName'];
            }
            else{
                $_SESSION['error'] = 'Incorrect Password';
                header('location: login.php');
            }
        }
        else{
             $_SESSION['error'] = 'Username Does Not Exist';
             header('location: login.php');
        }



    }
    catch(PDOException $e){
        echo "There is some problem in connection: " . $e->getMessage();
    }

}



if(isset($_POST['message'])){

//    $to_email = 'tut@gmail.com';
//    $subject = 'Truber Query Message';
//    $body ='Name: '.$_POST['name'].'<br> Message: '. $_POST['message'];
//    $header = 'From: '.$_POST['email']. "\r\n" .
//        'MIME-Version: 1.0' . "\r\n" .
//        'Content-type: text/html; charset=utf-8';
//
//    if(mail($to_email,$subject,$body,$header)){
//        $_SESSION['success'] = 'Message Successfully Submitted, We Will Respond To Your Query ASAP ...';
//    }else{
//        $_SESSION['error'] = 'Failed To Send Query ...';
//    }

    $to_email = $_POST['email'];
    $subject = 'FMS Query Message';

    $body = "
                <a href='https://fmstut.000webhostapp.com/' style='color: #fed136;font-family: Kaushan Script,Helvetica Neue,Helvetica,Arial,cursive;'>FMS</a><br/>
                <h3>Hi ".$_POST['name'].".</h3>
                <h3>Your query was received and will be attended by one of our agency ASAP...</h3>
                <br/>
                
             ";

    $header = 'From: fmstut@gmail.com'. "\r\n" .
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=utf-8';


    if(mail($to_email,$subject,$body,$header)){
        $_SESSION['success'] = 'Message Successfully Submitted, We Will Respond To Your Query ASAP ...';
    }else{
        $_SESSION['error'] = 'Failed To Send Query ...';
    }

    header('location: '.$_SERVER['HTTP_REFERER']);
    exit(0);

}


$pdo->close();

header('location: login.php');

?>