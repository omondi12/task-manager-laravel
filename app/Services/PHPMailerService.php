<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = env('MAIL_USERNAME');
            $this->mailer->Password = env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;

            // Default sender
            $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        } catch (Exception $e) {
            \Log::error('PHPMailer configuration failed: ' . $e->getMessage());
        }
    }

    public function sendPasswordResetEmail($email, $resetUrl, $userName = null)
    {
        try {
            // Recipients
            $this->mailer->addAddress($email, $userName);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Reset Your Task Manager Password';
            
            $this->mailer->Body = $this->getPasswordResetEmailTemplate($resetUrl, $userName);
            $this->mailer->AltBody = "Click the following link to reset your password: {$resetUrl}";

            $result = $this->mailer->send();
            
            // Clear addresses for next use
            $this->mailer->clearAddresses();
            
            return $result;
            
        } catch (Exception $e) {
            \Log::error('PHPMailer send failed: ' . $e->getMessage());
            return false;
        }
    }

    private function getPasswordResetEmailTemplate($resetUrl, $userName)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <title>Reset Your Password</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #4f46e5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Task Manager</h1>
                    <p>Password Reset Request</p>
                </div>
                <div class='content'>
                    <h2>Hello" . ($userName ? " {$userName}" : "") . "!</h2>
                    <p>You are receiving this email because we received a password reset request for your account.</p>
                    <p>Click the button below to reset your password:</p>
                    <a href='{$resetUrl}' class='button'>Reset Password</a>
                    <p>This password reset link will expire in 60 minutes.</p>
                    <p>If you did not request a password reset, no further action is required.</p>
                </div>
                <div class='footer'>
                    <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
                    <p>{$resetUrl}</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
