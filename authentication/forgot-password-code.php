<?php 
session_start();
include '../database/config.php';
include './api/api_key.php';

//Import PHP Mailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';


// Create an instance of PHPMailer


function sendPasswordToken($email, $token, $phpmailer_email, $phpmailer_password) {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $phpmailer_email;
    $mail->Password   = $phpmailer_password;
    $mail->SMTPSecure = "tls";
    $mail->Port       = "587";

    // Sender and recipient settings
    $mail->setFrom($phpmailer_email, "GrowCalendar System");
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = "Password Reset Request";

    $email_template = "
        <html>
        <head>
            <style>
                .container {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                    border-radius: 5px;
                }
                .header {
                    font-size: 18px;
                    font-weight: bold;
                    color: #333;
                }
                .otp {
                    font-size: 22px;
                    font-weight: bold;
                    color: #d9534f;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 14px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <p class='header'>Hello</p>
                <p>We received a request to reset your password. Please use the following One-Time Password (OTP) to complete your email verification process:</p>
                <p class='otp'>$token</p>
                <p>If you did not request a password reset, please ignore this email or contact support immediately.</p>
                <p class='footer'>Best regards,<br><strong>GrowCalendar System</strong></p>
            </div>
        </body>
        </html>
    ";

    $mail->Body = $email_template;
    $mail->send();
    echo 'Verification code has been sent successfully.';
}

function resetPasswordSuccess($email, $phpmailer_email, $phpmailer_password) {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $phpmailer_email;
    $mail->Password   = $phpmailer_password;
    $mail->SMTPSecure = "tls";
    $mail->Port       = "587";

    // Sender and recipient settings
    $mail->setFrom($phpmailer_email, "GrowCalendar System");
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = "Password Reset Successful";

    $email_template = "
        <html>
        <head>
            <style>
                .container {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                    border-radius: 5px;
                }
                .header {
                    font-size: 18px;
                    font-weight: bold;
                    color: #333;
                }
                .message {
                    font-size: 16px;
                    color: #555;
                }
                .success {
                    font-size: 22px;
                    font-weight: bold;
                    color: #28a745;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 14px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <p class='header'>Hello</p>
                <p class='message'>Your password has been successfully reset. You can now log in to your account using your new password.</p>
                <p class='success'>If you did not request this change, please contact our support team immediately.</p>
                <p class='message'>For any questions or further assistance, feel free to reach out to our support team.</p>
                <p class='footer'>Best regards,<br><strong>GrowCalendar System Team</strong></p>
            </div>
        </body>
        </html>
    ";

    $mail->Body = $email_template;
    $mail->send();
}

// Helper: generate numeric OTP
function generate_otp($length = 6) {
	$digits = '0123456789';
	$otp = '';
	for ($i = 0; $i < $length; $i++) {
		$otp .= $digits[random_int(0, strlen($digits) - 1)];
	}
	return $otp;
}

if (isset($_POST['send_OTP']) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = rand(100000, 999999);

    $stmt = $conn->prepare("UPDATE users SET password_token = ? WHERE email = ? LIMIT 1");
    $stmt->bind_param("ss", $token, $email);

    if ($stmt->execute()) {
        sendPasswordToken($email, $token, $phpmailer_email, $phpmailer_password);
        
        $_SESSION['entered_email'] = $email;
        $_SESSION['codeSent'] = true;
        $_SESSION['status'] = "Success!";
        $_SESSION['status_text'] = "Reset code sent! Please Check your email.";
        $_SESSION['status_code'] = "success";
        $_SESSION['status_btn'] = "Done";
    }else {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "Error: " . $stmt->error;
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Try Again";
    }

    $stmt->close();
    // Redirect
    header("Location: verify_otp.php?email=$email");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmCode'])) {
    
    $code = mysqli_real_escape_string($conn, $_POST['otp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
         

    // Prepare and execute the query
    $checkCode = "SELECT password_token FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $checkCode);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $dbPasswordToken);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Check if the verification code matches
        if ($dbPasswordToken) {
            if ($code === $dbPasswordToken) {

                    // Update the password token to NULL after successful verification
                    $updateQuery = "UPDATE users SET password_token = NULL WHERE email = ? LIMIT 1";
                    $updateStmt = mysqli_prepare($conn, $updateQuery);
                    if ($updateStmt) {
                        mysqli_stmt_bind_param($updateStmt, "s", $email);
                        mysqli_stmt_execute($updateStmt);
                        mysqli_stmt_close($updateStmt);

                    unset($_SESSION['entered_email']);
                    unset($_SESSION['codeSent']);

                    header("Location: reset-password.php?email=$email&token=$code");
                    }else{
                        $_SESSION['status'] = "Error!";
                        $_SESSION['status_text'] = "Failed to update the password token.";
                        $_SESSION['status_code'] = "error";
                        $_SESSION['status_btn'] = "Try Again";
                        header("Location: {$_SERVER['HTTP_REFERER']}");
                    }
            } else {
                $_SESSION['status'] = "Invalid Code!";
                $_SESSION['status_text'] = "The verification code you entered is incorrect.";
                $_SESSION['status_code'] = "error";
                $_SESSION['status_btn'] = "Retry";
                header("Location: {$_SERVER['HTTP_REFERER']}");

            }
        } else {
            $_SESSION['status'] = "Error!";
            $_SESSION['status_text'] = "No verification code found for this account.";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Retry";
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
    } else {
        $_SESSION['status'] = "Database Error!";
        $_SESSION['status_text'] = "Failed to execute the query.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Try Again";
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }

    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resetPassword'])) {
    
    $newPassword = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if ($newPassword === $confirmPassword) {

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? LIMIT 1");
        $stmt->bind_param("ss", $hashedPassword, $email);
        if ($stmt->execute()) {
            resetPasswordSuccess($email, $phpmailer_email, $phpmailer_password);
            $_SESSION['status'] = "Success!";
            $_SESSION['status_text'] = "Your password has been successfully reset.";
            $_SESSION['status_code'] = "success";
            $_SESSION['status_btn'] = "Done";
            header("Location: ../index.php");
            
    }else{
            $_SESSION['status'] = "Error!";
            $_SESSION['status_text'] = "Failed to reset the password.";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Try Again";
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
    }
    exit();
}

// Default fallback
header('Location: forgot_password.php');
exit;
?>