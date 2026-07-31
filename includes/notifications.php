<?php

if (!function_exists('notification_log_dir')) {
    function notification_log_dir()
    {
        return __DIR__ . '/../storage/private/mail';
    }
}

if (!function_exists('notification_write_log')) {
    function notification_write_log(array $payload)
    {
        $dir = notification_log_dir();

        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            error_log('Notification log directory cannot be created.');
            return false;
        }

        $file = $dir . '/' . date('Y-m-d') . '.log';
        $line = json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL;

        return file_put_contents($file, $line, FILE_APPEND | LOCK_EX) !== false;
    }
}

if (!function_exists('notification_send_booking_status')) {
    function notification_send_booking_status(array $booking, $event)
    {
        $email = trim((string) ($booking['customer_email'] ?? ''));
        $code = trim((string) ($booking['booking_code'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $code === '') {
            return false;
        }

        $subject = 'Update booking ' . $code . ' - Bali Paradise';
        $message = "Halo " . trim((string) ($booking['customer_name'] ?? 'Customer')) . ",\n\n"
            . "Status booking Anda telah diperbarui.\n"
            . "Kode booking: " . $code . "\n"
            . "Status booking: " . (string) ($booking['booking_status'] ?? '-') . "\n"
            . "Status pembayaran: " . (string) ($booking['payment_status'] ?? '-') . "\n\n"
            . "Terima kasih.";

        $payload = [
            'time' => date('c'),
            'event' => (string) $event,
            'to' => $email,
            'booking_code' => $code,
            'subject' => $subject,
        ];

        if (getenv('BALI_MAIL_ENABLED') === 'true') {
            $headers = "From: Bali Paradise <no-reply@localhost>\r\n";
            $sent = @mail($email, $subject, $message, $headers);
            $payload['sent'] = $sent;
            notification_write_log($payload);

            return $sent;
        }

        $payload['sent'] = false;
        $payload['mode'] = 'log-only';

        return notification_write_log($payload);
    }
}
