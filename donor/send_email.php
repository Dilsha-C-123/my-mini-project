<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendDonationConfirmation($to_email, $donor_name, $food_name, $quantity) {
    $mail = new PHPMailer(true);

    try {
        // Enable debug output
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer: $str");
        };

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sandwfmanagement@gmail.com'; // Your Gmail address
        $mail->Password = 'xdnb gsht eqob kjur';  // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sandwfmanagement@gmail.com', 'Food Donation System');
        $mail->addAddress($to_email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Food Donation Confirmation';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2 style='color: #28a745;'>Thank You for Your Donation!</h2>
                <p>Dear $donor_name,</p>
                <p>We have received your donation details:</p>
                <ul>
                    <li>Food Type: $food_name</li>
                    <li>Quantity: $quantity kg</li>
                </ul>
                <p>Our team will contact you shortly for the food pickup.</p>
                <p>Thank you for your generosity!</p>
                <br>
                <p>Best regards,</p>
                <p>swf management system</p>
            </div>
        ";

        $mail->send();
        error_log("Email sent successfully to: " . $to_email);
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendRejectionEmail($to_email, $donor_name) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sandwfmanagement@gmail.com'; // Your Gmail
        $mail->Password = 'xdnb gsht eqob kjur'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sandwfmanagement@gmail.com', 'Food Donation System');
        $mail->addAddress($to_email, $donor_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Donation Rejection Notice';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Donation Rejection Notice</h2>
                <p>Dear {$donor_name},</p>
                <p>We regret to inform you that your waste food donation has been rejected. This could be due to one of the following reasons:</p>
                <ul>
                    <li>Invalid or unclear payment proof</li>
                    <li>Mismatch in payment details</li>
                    <li>Technical issues with the uploaded proof</li>
                </ul>
                <p>Please feel free to submit a new donation request with the correct payment proof.</p>
                <p>Thank you for your understanding.</p>
                <p>Best regards,<br>Food Donation System Team</p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendClaimConfirmationEmail($to_email, $donor_name, $food_name, $quantity) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sandwfmanagement@gmail.com';
        $mail->Password = 'xdnb gsht eqob kjur';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sandwfmanagement@gmail.com', 'Food Donation System');
        $mail->addAddress($to_email, $donor_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Donation Claimed Successfully';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Donation Claimed Successfully</h2>
                <p>Dear {$donor_name},</p>
                <p>We are pleased to inform you that your donation has been successfully claimed by our team.</p>
                <p>Donation Details:</p>
                <ul>
                    <li>Food Item: {$food_name}</li>
                    <li>Quantity: {$quantity} kg</li>
                </ul>
                <p>Thank you for your generous contribution to our food donation system.</p>
                <p>Best regards,<br>Food Donation System Team</p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendWasteClaimConfirmationEmail($to_email, $donor_name, $food_type, $quantity, $charges) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sandwfmanagement@gmail.com';
        $mail->Password = 'xdnb gsht eqob kjur';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sandwfmanagement@gmail.com', 'Food Donation System');
        $mail->addAddress($to_email, $donor_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Waste Food Donation Claimed Successfully';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Waste Food Donation Claimed Successfully</h2>
                <p>Dear {$donor_name},</p>
                <p>We are pleased to inform you that your waste food donation has been successfully claimed by our team.</p>
                <p>Donation Details:</p>
                <ul>
                    <li>Food Type: {$food_type}</li>
                    <li>Quantity: {$quantity} kg</li>
                    <li>Total Charges: ₹{$charges}</li>
                </ul>
                <p>Thank you for using our waste food management system.</p>
                <p>Best regards,<br>Food Donation System Team</p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}
?> 