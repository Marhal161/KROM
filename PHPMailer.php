<?php
/**
 * PHPMailer - упрощённая версия для отправки через SMTP
 * Это облегчённая версия библиотеки PHPMailer
 */

class PHPMailer {
    public $Host = '';
    public $Port = 465;
    public $SMTPAuth = true;
    public $Username = '';
    public $Password = '';
    public $SMTPSecure = 'ssl';
    public $CharSet = 'UTF-8';
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    private $to = array();
    private $replyTo = array();
    public $ErrorInfo = '';

    public function isSMTP() {
        // Устанавливает режим SMTP
    }

    public function addAddress($email, $name = '') {
        $this->to[] = array('email' => $email, 'name' => $name);
    }

    public function addReplyTo($email, $name = '') {
        $this->replyTo[] = array('email' => $email, 'name' => $name);
    }

    public function send() {
        try {
            // Подключение к SMTP серверу
            $socket = @fsockopen(
                ($this->SMTPSecure === 'ssl' ? 'ssl://' : '') . $this->Host,
                $this->Port,
                $errno,
                $errstr,
                10
            );

            if (!$socket) {
                $this->ErrorInfo = "Не удалось подключиться к SMTP серверу: $errstr ($errno)";
                return false;
            }

            // Установка таймаута
            stream_set_timeout($socket, 10);

            // Чтение приветствия сервера
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                $this->ErrorInfo = "Неожиданный ответ сервера: $response";
                fclose($socket);
                return false;
            }

            // EHLO
            fputs($socket, "EHLO " . $this->Host . "\r\n");
            $response = $this->getResponse($socket);

            // Если используется TLS (не SSL)
            if ($this->SMTPSecure === 'tls') {
                fputs($socket, "STARTTLS\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '220') {
                    $this->ErrorInfo = "STARTTLS не поддерживается: $response";
                    fclose($socket);
                    return false;
                }
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($socket, "EHLO " . $this->Host . "\r\n");
                $response = $this->getResponse($socket);
            }

            // AUTH LOGIN
            if ($this->SMTPAuth) {
                fputs($socket, "AUTH LOGIN\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '334') {
                    $this->ErrorInfo = "AUTH LOGIN не поддерживается: $response";
                    fclose($socket);
                    return false;
                }

                fputs($socket, base64_encode($this->Username) . "\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '334') {
                    $this->ErrorInfo = "Неверный логин: $response";
                    fclose($socket);
                    return false;
                }

                fputs($socket, base64_encode($this->Password) . "\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '235') {
                    $this->ErrorInfo = "Неверный пароль: $response";
                    fclose($socket);
                    return false;
                }
            }

            // MAIL FROM
            fputs($socket, "MAIL FROM: <" . $this->From . ">\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->ErrorInfo = "MAIL FROM ошибка: $response";
                fclose($socket);
                return false;
            }

            // RCPT TO
            foreach ($this->to as $recipient) {
                fputs($socket, "RCPT TO: <" . $recipient['email'] . ">\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '250') {
                    $this->ErrorInfo = "RCPT TO ошибка: $response";
                    fclose($socket);
                    return false;
                }
            }

            // DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '354') {
                $this->ErrorInfo = "DATA ошибка: $response";
                fclose($socket);
                return false;
            }

            // Заголовки и тело письма

            // Кодируем отображаемое имя отправителя, если есть не-ASCII символы
            $fromName = $this->FromName;
            if ($this->CharSet && preg_match('/[^\x20-\x7E]/u', $fromName)) {
                $fromName = '=?' . $this->CharSet . '?B?' . base64_encode($fromName) . '?=';
            }

            // Кодируем тему письма по RFC 2047, если есть не-ASCII символы
            $subject = $this->Subject;
            if ($this->CharSet && preg_match('/[^\x20-\x7E]/u', $subject)) {
                $subject = '=?' . $this->CharSet . '?B?' . base64_encode($subject) . '?=';
            }

            $headers = "From: " . $fromName . " <" . $this->From . ">\r\n";
            foreach ($this->to as $recipient) {
                $headers .= "To: " . $recipient['email'] . "\r\n";
            }
            if (!empty($this->replyTo)) {
                $headers .= "Reply-To: " . $this->replyTo[0]['email'] . "\r\n";
            }
            $headers .= "Subject: " . $subject . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=" . $this->CharSet . "\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "\r\n";

            fputs($socket, $headers . $this->Body . "\r\n.\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->ErrorInfo = "Ошибка отправки: $response";
                fclose($socket);
                return false;
            }

            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return true;

        } catch (Exception $e) {
            $this->ErrorInfo = $e->getMessage();
            return false;
        }
    }

    private function getResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }
}

