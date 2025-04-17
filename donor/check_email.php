<?php
header('Content-Type: application/json');

function verifyEmail($email) {
    // Check MX records
    list($user, $domain) = explode('@', $email);
    if (!checkdnsrr($domain, 'MX')) {
        return false;
    }

    // Get MX records
    getmxrr($domain, $mxhosts);
    
    // Try to connect to mail server
    $port = 25;
    $timeout = 5;
    $sock = @fsockopen($mxhosts[0], $port, $errno, $errstr, $timeout);
    
    if (!$sock) {
        return false;
    }
    
    // SMTP conversation
    $response = fgets($sock);
    fputs($sock, "HELO " . $_SERVER['HTTP_HOST'] . "\r\n");
    $response = fgets($sock);
    fputs($sock, "MAIL FROM: <verify@" . $_SERVER['HTTP_HOST'] . ">\r\n");
    $response = fgets($sock);
    fputs($sock, "RCPT TO: <$email>\r\n");
    $response = fgets($sock);
    fputs($sock, "QUIT\r\n");
    fclose($sock);
    
    // Check if email exists
    return (strpos($response, '250') !== false);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // First check if it's a valid email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['exists' => false, 'error' => 'Invalid email format']);
        exit;
    }
    
    // Check if domain is allowed
    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
    $domain = explode('@', $email)[1];
    if (!in_array($domain, $allowedDomains)) {
        echo json_encode(['exists' => false, 'error' => 'Domain not allowed']);
        exit;
    }
    
    // Verify if email exists
    $exists = verifyEmail($email);
    
    echo json_encode(['exists' => $exists]);
} else {
    echo json_encode(['exists' => false, 'error' => 'Invalid request']);
} 