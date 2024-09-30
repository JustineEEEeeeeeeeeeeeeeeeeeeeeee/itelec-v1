<?php
require_once __DIR__ . '/../../../Database/dbconnection.php';
include_once __DIR__ . '/../../../config/setting-configuration.php';


class ADMIN
{
    private $conn;
    public function __construct()
    {
        $database = new Database();
        $this->conn =$database->dbconnection();
    }

    public function addAdmin ($csrf_token,$username, $email, $password)
    {
    $stmt = $this->conn->prepare("SELECT * FROM user WHERE  email = :email");
    $stmt->execute(array(":email" => $email));

    if($stmt->rowCount() > 0){
        echo"<script>alert('Email already use'); window.location.href = '../../../';</script>";
        exit;
    }

    if(!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)){
        echo"<script> alert('Invalid CSRF token'); window.location.href = '../../../'; </script>";
        exit;
    }
    unset($_SESSION['csrf_token']);

    $hash_password = md5($password);

    $stmt = $this->runquery('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
    $exec =$stmt->execute(array(
        ":username"=> $username,
        ":email"=>$email,
        ":password"=>$hash_password
    ));

     if($exec){
        echo"<script>alert('Admin Added Succes'); window.location.href = '../../../';</script>";
        exit;
     }else{
        echo"<script>alert('Error adding Admin'); window.location.href = '../../../';</script>";
        exit;
     }

    }

    public function adminSignin($email, $password, $csrf_token){

    }

    public function adminSignout()
    {

    }

    public function logs($activity,$user_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO logs (user_id, activity) VALUES (:user_id, :activity)");
        $stmt->execute(array(":user_id" => $user_id, ":activity" => $activity));
    }
    public function runquery($sql)
    {
        $stmt = $this->conn->prepare($sql);
     return $stmt;
    }
}

if(!isset($_POST['btn-signup'])){
   $csrf_token = trim($_POST['csrf_token']);
   $username = trim($_POST['username']);
   $email = trim($_POST['email']);
   $password = trim($_POST['password']);

   $addAdmin = new ADMIN();
   $addAdmin->addAdmin($csrf_token, $username, $email, $password);
}
?>