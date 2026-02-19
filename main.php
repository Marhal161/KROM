<?php
// Подключаем конфигурацию SMTP и библиотеку PHPMailer
define('SMTP_CONFIG_LOADED', true); // Разрешаем загрузку smtp_config.php
require_once __DIR__ . '/smtp_config.php';
require_once __DIR__ . '/PHPMailer.php';

// Стартуем сессию для flash-сообщений и защиты от повторной отправки по F5
if (function_exists('session_status') && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Статус формы (успех/ошибка), в т.ч. после redirect
$contactStatus = $_SESSION['contactStatus'] ?? null;
if (isset($_SESSION['contactStatus'])) {
    unset($_SESSION['contactStatus']);
}

// Данные формы для повторного вывода при ошибке
$contactData = [
    'name' => '',
    'phone' => '',
    'email' => '',
    'message' => '',
    'consent' => false,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    // Поле-ловушка от ботов (скрытое в верстке)
    $honeypot = trim($_POST['company'] ?? '');
    if ($honeypot !== '') {
        // Считаем спамом: не отправляем письмо, но имитируем успешный сценарий
        $_SESSION['contactStatus'] = [
            'type' => 'success',
            'text' => 'Сообщение успешно отправлено. Мы свяжемся с вами в ближайшее время.',
        ];
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }

    $contactData['name'] = trim($_POST['name'] ?? '');
    $contactData['phone'] = trim($_POST['phone'] ?? '');
    $contactData['email'] = trim($_POST['email'] ?? '');
    $contactData['message'] = trim($_POST['message'] ?? '');
    $contactData['consent'] = isset($_POST['consent']) && $_POST['consent'] === '1';

    if (
        $contactData['name'] === '' ||
        $contactData['phone'] === '' ||
        $contactData['email'] === '' ||
        $contactData['message'] === ''
    ) {
        $contactStatus = ['type' => 'error', 'text' => 'Пожалуйста, заполните все обязательные поля.'];
    } elseif (!$contactData['consent']) {
        $contactStatus = ['type' => 'error', 'text' => 'Поставьте галочку согласия на обработку персональных данных.'];
    } elseif (!filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
        $contactStatus = ['type' => 'error', 'text' => 'Укажите корректный e-mail.'];
    } else {
        // Формируем тело письма
        $subject = 'Новая заявка с сайта KROM';
        $body = "Имя: {$contactData['name']}\n";
        $body .= "Телефон: {$contactData['phone']}\n";
        $body .= "Email: {$contactData['email']}\n\n";
        $body .= "Комментарий:\n{$contactData['message']}\n\n";
        $body .= "---\n";
        $body .= "Отправлено: " . date('d.m.Y H:i:s') . "\n";
        $body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Неизвестен');

        // Создаем экземпляр PHPMailer
        $mail = new PHPMailer();
        
        try {
            // Настройки SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->SMTPAuth = SMTP_AUTH;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->CharSet = 'UTF-8';

            // Отправитель и получатель
            $mail->From = SMTP_FROM_EMAIL;
            $mail->FromName = SMTP_FROM_NAME;
            $mail->addAddress(MAIL_TO);
            $mail->addReplyTo($contactData['email'], $contactData['name']);

            // Содержимое письма
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Отправляем
            if ($mail->send()) {
                // Логируем заявку в файл
                $logDir = __DIR__ . '/logs';
                if (!is_dir($logDir)) {
                    @mkdir($logDir, 0755, true);
                }
                $logFile = $logDir . '/contacts.log';
                $logEntry = "========================\n" . date('Y-m-d H:i:s') . "\n" . $body . "\n\n";
                @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

                // Flash-сообщение об успехе и redirect (PRG), чтобы не дублировать отправку по F5
                $_SESSION['contactStatus'] = [
                    'type' => 'success',
                    'text' => 'Сообщение успешно отправлено. Мы свяжемся с вами в ближайшее время.',
                ];
                header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
                exit;
            } else {
                $contactStatus = ['type' => 'error', 'text' => 'Ошибка отправки: ' . $mail->ErrorInfo];
            }
        } catch (Exception $e) {
            $contactStatus = ['type' => 'error', 'text' => 'Не удалось отправить сообщение. Попробуйте позже.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon -->
  <link rel="icon" href="media/logo_icon.png" type="image/png" />

  <!-- SEO -->
  <title>KROM Marketing — маркетинговое и рекламное агентство в Москве: сайты и реклама</title>
  <meta name="description" content="KROM Marketing — маркетинговое и рекламное агентство в Москве. Разработка сайтов и веб-приложений, Telegram Ads, наружная реклама, электронная почта и SMS. Оставьте заявку — ответим в день обращения." />
  <meta name="keywords" content="маркетинговое агентство, рекламное агентство, цифровое агентство, услуги маркетинга, рекламные услуги, комплексный маркетинг, продвижение бизнеса, лидогенерация, интернет реклама, Telegram Ads, реклама у блогеров, SMS рассылка, электронная почта маркетинг, наружная реклама, разработка сайтов, веб-приложения" />
  <meta name="robots" content="index, follow" />

  <!-- Canonical -->
  <link rel="canonical" href="https://krommarketing.ru/" />

  <!-- Open Graph -->
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="KROM Marketing" />
  <meta property="og:url" content="https://krommarketing.ru/" />
  <meta property="og:title" content="KROM Marketing — маркетинговое и рекламное агентство в Москве" />
  <meta property="og:description" content="Разработка сайтов и веб-приложений, Telegram Ads, наружная реклама, электронная почта и SMS. Оставьте заявку — ответим в день обращения." />
  <meta property="og:image" content="https://krommarketing.ru/media/og-cover.jpg" />
  <meta property="og:image:alt" content="KROM Marketing — агентство полного цикла" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="KROM Marketing — маркетинговое и рекламное агентство в Москве" />
  <meta name="twitter:description" content="Разработка сайтов, Telegram Ads, наружная реклама, электронная почта и SMS. Оставьте заявку — ответим в день обращения." />
  <meta name="twitter:image" content="https://krommarketing.ru/media/og-cover.jpg" />

  <!-- Styles -->
  <link rel="stylesheet" href="style-base.css" />
  <link rel="stylesheet" href="style-sections.css" />
  <link rel="stylesheet" href="style-responsive.css" />

  <!-- Structured data: Organization -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "KROM Marketing",
    "url": "https://krommarketing.ru/",
    "logo": "https://krommarketing.ru/media/logo_krom_gif.gif",
    "sameAs": [
      "https://t.me/krommarketing",
      "https://vk.ru/krommarketing",
      "https://wa.me/79152564826",
      "https://www.youtube.com/channel/UC1jp1VNG1wv8Z4ym2JAeiTw"
    ],
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+7 915 256-48-26",
      "contactType": "sales",
      "areaServed": "RU",
      "availableLanguage": ["Russian"]
    },
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "RU",
      "addressLocality": "Москва",
      "postalCode": "107143",
      "streetAddress": "г. Москва, ш. Открытое, д. 24, корп. 5В, кв. 14"
    }
  }
  </script>
</head>
 <body>
    <header class="header">
        <div class="header-top">
            <a href="/" class="logo-section">
                <img src="media/logo_krom_gif.gif" alt="KROM Logo" class="logo-image">
                <span class="logo-text">КРОМ</span>
            </a>

            <nav class="navigation" aria-label="Основная навигация">
                <a href="#choose" class="nav-link">О нас</a>
                <a href="#services" class="nav-link">Услуги</a>
                <a href="#reviews" class="nav-link">Отзывы</a>
                <a href="#contacts" class="nav-link">Контакты</a>
            </nav>

            <button class="menu-toggle" type="button" aria-label="Открыть меню">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="contact-icons">
            <a href="tel:+79152564826" class="contact-icon" title="Телефон" aria-label="Телефон">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAElklEQVR4nO1ZW4gcVRC98Y3ig/iMUYMx6Dpd1ZswHyaKiCL+iIKEKBhC3KAoGPVT/NoPyVcM2Zl7Z9aBiKB/UdzEiI8PSfDD14Y8ZNJVPVFWRUUwRhMV18c6Ut33zs7q9GQmzM7ehT3QX123+1bfqlOnqpVawALmDprwfsM4bggnNeFhHcEaNZ8wXM2dowlKhrE+4yL4aTQeXKrmAypf5C82BO/LxjXh7yaCzRXOX2YYdltn3la+w1RzVxnCQ+kJwLdlwvyMe4zHrDOblK8o1FZcZAgO2JOIzJGbl/3XxhCsT+/DzyPV3HXKNxRqK841BB8km2So6mjg0ixbQzjmbYhphi02ob8q1PCadrY2xH5MnI5wo/IJmvG4bKxEwZ2d2BsONqSnh8cr0cAS5QsM4aRsbPvE4CWdrtGEb6UhhmPKF2jGo7KpchRCp2skBCXpZV2Rg4eUDzDTCby+q3UxPmapOlY+wESw2dLuK92sSwtlQsd/1+tqkZprFGrBDZrwH814cgfddGGn6wzhI5btxpUvMA1ZAk90Yv/yxLLzNOOX3tGwYXjQxfvw3jvOOpW9juA5G44Hh+vqDOULdtbXnZlIkzTpH29nW/48vMIQnkgYK8a7lW/QFK61he67yv78+Vl2hrFiT2+38hH1ulpkCD6xCby1TW78Yh1+VPmKQi1cpRn+NIRTxRhvb2VjCJ52jVaRw+uVr9AMz7vEbxViyckx7rHJ/mm7MPSg1cXDlo5LrWxElzly0AS7hCyUjzBxbmXS6qYbHcoupPCDzamKF9W9FcSBRu8eh7e0tIlgjUt+Q7Bd+QrDWHSUnDVBKXJ4lzs9Q7hN+YjK/vzZhnGvDZ8PsxK7ROF9Cdul7fIW5SMKtZWXa4YJmy/vCBm0stOM65wzEmZe5oyOwhsN4/f2ZHZmsVRyMo4kGF/0ks2KcTAoRdDmwo6sL57kjKv+BLtahWOq7WBIRKchnDIMNRGuql8oUXCrYfj1VIldiHB1EzWPN8/JRGi6OvX/C14f+Qyu7I8zUXCPYfzDOZN1MuUYlzeKpkxq0tY4UQSWFCakl6lIpyn3rKKWiaYmfLgvzugYHnDOaMaXsnIhUQAMb84chuMJzfisCNBm29LR4Fohkya7MZmj9elk0jDTDK9lsZmcmCZ4yubNqPQz7Z5rCDY1cjEZBgYb1GyjyMFt0y+Fd9sJSBnNdvrc0XhwaWN2ll57NK26Ws22LnPUrBk+6uX0UUe40U1Ckw+WIZV6WmcawwiCb3r5wko0sKSpbTio+qMAcJ9N1Mks1Xw62FbNLbYn/pvqlzbTjLpBsQTlXjRemvAZp/d6s9OOXwxDbjguVVtI4XSfVYhwtaP6Ugz3qjlqztIKTjilGV/Yeii8oJtnlGNcPq3xYETNaduczADgL9fXyNyskyFgIVXdbNe9J2Gr5hryY1UTfNykqWIZz2bNmktJlW8MDA90M5OedaQVPlzrvrL90icN4avyS0P+z8jvcdtxfu3otm8isltIWMnPIRmcy18A01IFJ07uE8fUfECikBmfNIRvCLvZpuyY5FU3kmYBC1C9xb9TK7gwe2Lp5wAAAABJRU5ErkJggg==" alt="Телефон">
            </a>
            <div class="icon-divider"></div>
            <a href="mailto:info@krommarketing.ru" class="contact-icon" title="Email" aria-label="Email">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAACt0lEQVR4nO2Yy2sUQRDGyxc+0ICKigcRyYoxU7UGghhRDHrzoigqigdP6k2J/gFRLzkFMts1gYjiRRAVRPDmRRFfSE4hmaqZ+MIH+BYEjRJ1ZXY7ssgak7jZnV3mBw1zqJ7ur7vr6wdAQkJCQkIlYKVsLRSoOSFQpXDNCnFDpx6qDDd06ovlyHvW9EaoEjhIrzOCr4snu9BXE6T3QswxAe4wip+LJrsR9KyYH6zUBjGFldpsH6M+dxdNdiN0tCDodPuN1ukQEy5ld09jpUy+b/TTCJ0Y1bU839nJgl+smKs9vc1zKqoAAM49WT7LCF0cWf7s074x2a/rUwsLvrHqH3T14ZKKKAAA4zcsZKHbvw0poE3j2kciazOKmh8FfOwFzuqyKgAA7m9MsWJoB/RRRrFhQhtiZ3/jAha8lfuR0gfPx9byzQSuN4Jv7RK/3/0wvfi/dnY3TM1kwQs27hsL7p9UBQDAQruM0JDNiSuj5em4jijZLEyJXOJPx5gMTKFzKrntWZha8rMWB3SQFYdtI2d7eptnlNJejd3LjOB3FjwyqYdGVtpesKted8NUXQmSei4rXbMzPsSKe8py+vXUWctKr/J1sc8bdJZNSAEA9PgNS41Sr+3Hu4w6G8p6jM9oeoUR8q2Ylxw0No33HzywxjFKT+1SHewaaFpZkftIdx/NN0o3bUc+ZZS2jrVuRtNbWPCjbf+uGzYtqujFKrJno3TezsywETr0rzrGpwM5K88NAF7ufNYyOxY3xGzenjtG7JmFThazzbwz0alcTD62I6obu6suCx4usOc7RmhbtGSiEn1HS2hk5qLYWN/ZPXE2G8EXf33xEHwexVTF44MbpupY8Hh0Ror2HFvuGcVjZ2TVvFK2xTX7igJVCidCYgYnQmIGJ0JiBtesEK7yAjUjJCEhISEBKsAvCef8actuQ0QAAAAASUVORK5CYII=" alt="filled-message">
            </a>
            <div class="icon-divider"></div>
            <a href="https://t.me/krommarketing" target="_blank" rel="noopener" class="contact-icon" title="Telegram" aria-label="Telegram">
                <img src="data:image/png;base64,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" alt="paper-plane--v1">
            </a>
            <div class="icon-divider"></div>
            <a href="https://wa.me/79152564826" target="_blank" rel="noopener" class="contact-icon" title="WhatsApp" aria-label="WhatsApp">
                <img src="data:image/png;base64,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" alt="whatsapp--v1">
            </a>
        </div>
    </header>

    <section class="gallery-carousel" aria-label="Фотогалерея">
        <h1>Маркетинговое и рекламное агентство KROM Marketing в Москве</h1>
        <div class="carousel-viewport">
            <div class="carousel-track-wrapper">
                <ul class="carousel-track">
                    <li class="carousel-slide">
                        <div class="carousel-content">
                            <h2>Инновационное рекламное агентство</h2>
                            <p>Помогаем бизнесу расти и развиваться в цифровую эпоху</p>
                        </div>
                        <img src="media/1.jpg" alt="Фото 1" loading="lazy">
                    </li>
                    <li class="carousel-slide">
                        <div class="carousel-content">
                            <h2>Используем актуальные инструменты</h2>
                            <p>Внедряем продающие рекламные материалы, рекламные кампании, виджеты</p>
                        </div>
                        <img src="media/2.jpg" alt="Фото 2" loading="lazy">
                    </li>
                    <li class="carousel-slide">
                        <div class="carousel-content">
                            <h2>Креативная команда</h2>
                            <p>Поможет раскрыть потенциал вашего бизнеса, используя стратегический маркетинг</p>
                        </div>
                        <img src="media/3.jpg" alt="Фото 3" loading="lazy">
                    </li>
                </ul>
            </div>
        </div>
        <div class="carousel-dots" role="tablist" aria-label="Навигация по слайдам">
            <button class="carousel-dot active" type="button" role="tab" aria-label="Слайд 1"></button>
            <button class="carousel-dot" type="button" role="tab" aria-label="Слайд 2"></button>
            <button class="carousel-dot" type="button" role="tab" aria-label="Слайд 3"></button>
        </div>
    </section>

    <div class="cta-wrapper">
        <a href="tel:+79152564826" class="cta-button" aria-label="Позвонить в КРОМ">Позвонить</a>
    </div>

    <section id="services" class="services">
        <div class="services-header">
            <h2>Услуги</h2>
            <span class="services-accent"></span>
        </div>

        <div class="services-grid">
            <article class="service-card">
                <div class="service-image">
                    <img src="media/webapp.jpg" alt="Разработка веб-приложений" loading="lazy">
                </div>
                <div class="service-body">
                    <div class="service-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.06A1.65 1.65 0 0 0 9 3.09V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.06a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    <h3>Разработка веб-приложений</h3>
                    <p class="service-desc">Быстрый минимальный продукт за 48 часов, адаптивный дизайн, интеграции с CRM, рекламными кабинетами и аналитикой.</p>
                    <div class="service-price">от 99 990₽</div>
                    <ul class="service-features">
                        <li>Бриф, планирование, концепция</li>
                        <li>Прототипирование и дизайн интерфейса</li>
                        <li>клиентская часть, серверная часть, интерфейс взаимодействия</li>
                        <li>Интеграция, аналитика, тестирование</li>
                    </ul>
                    <a href="#contacts" class="service-btn">Выбрать</a>
                </div>
            </article>

            <article class="service-card">
                <div class="service-image">
                    <img src="media/website.jpg" alt="Разработка веб-сайтов" loading="lazy">
                </div>
                <div class="service-body">
                    <div class="service-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="14" rx="2" ry="2"></rect>
                            <path d="M8 21h8"></path>
                            <path d="M12 17v4"></path>
                        </svg>
                    </div>
                    <h3>Разработка веб-сайтов</h3>
                    <p class="service-desc">Создадим сайты, которые продают: креативный дизайн, оптимизация под воронку, интеграции с рекламой и системой управления клиентами.</p>
                    <div class="service-price">от 99 990₽</div>
                    <ul class="service-features">
                        <li>Бриф, планирование, концепция</li>
                        <li>Прототипирование и UX/UI-дизайн</li>
                        <li>Верстка, адаптивность, интеграции</li>
                        <li>Аналитика, тестирование, поддержка</li>
                    </ul>
                    <a href="#contacts" class="service-btn">Выбрать</a>
                </div>
            </article>

            <article class="service-card">
                <div class="service-image">
                    <img src="media/paket.jpg" alt="Пакетные услуги" loading="lazy">
                </div>
                <div class="service-body">
                    <div class="service-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            <line x1="9" y1="7" x2="15" y2="7"></line>
                            <line x1="9" y1="11" x2="15" y2="11"></line>
                            <line x1="9" y1="15" x2="13" y2="15"></line>
                        </svg>
                    </div>
                    <h3>Пакетные услуги</h3>
                    <p class="service-desc">Комплексные решения «под ключ»: креативы, запуск рекламы, план размещения и сквозная аналитика — быстрый старт и прозрачные показатели эффективности.</p>
                    <div class="service-price">от 29 990₽</div>
                    <ul class="service-features">
                        <li>Создание концепции, согласование</li>
                        <li>Креативный дизайн</li>
                        <li>Тесты, оптимизация</li>
                        <li>Рост узнаваемости и реальных клиентов</li>
                    </ul>
                    <a href="#contacts" class="service-btn">Выбрать</a>
                </div>
            </article>
        </div>

        <div class="services-cta">
            <a href="services.php" class="services-cta-button">Выбрать услугу</a>
        </div>
    </section>

    <section class="choose-us" id="choose">
        <div class="choose-us-header">
            <h2>Выбирайте нас</h2>
            <span class="choose-us-accent"></span>
        </div>
        <div class="choose-us-body">
            <div class="choose-us-media">
                <img src="media/pickme.jpg" alt="Команда KROM за работой" loading="lazy">
            </div>
            <div class="choose-us-features">
                <div class="choose-us-item">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEMklEQVR4nO2ZS4gcZRCAf41v8RUREVFBV7NOV/UurJBFQUEQldyEqCjxEL1ExUgQxFzWIJiDrNmZ+mfioKhHWd94UaMYI0iEBOI621U9MQQTFQm6JqLie6S6/85GcefRM+7f4n7Qt67qqv9RrzZmiSX+W9hGacgy3GkZN5FgmRietIwbKxJeP/HedSeYIjPdWr3MMqwlgYYVbC34MH5qY7zWFJF6NHyBFfxw3mD4wgpOW4YtJLAhfbBsBeLUGfiRGKpVDq42RWHL/pGzjxoo0KzGsKrVMsf907sTLXO8ZZz82w69UN81dprxjRV43jmxu75v7KxO76sz5WapRIIPWoZDzplXjE+mGqWLieE3K/BrJQ5GepWvxXgpCc6lCxHeZHxRFbxXjSDBl/PqIMZHsyNmfGEZX1UjKhLcnVdHNQ6uTBaDcZ/xBTHsVCPKEY73F7bxJ2L8w9ulJ8G96Y7AcD96LOMR1aMRcHDW9WYAqwE1xhX96CGBH5KdbQ6dOTjrejGA4f3kovaRqScbpeUufH9vfKGRJrmocXh7bh1xuNI5MjNY63oxgnGjizib8+ogDh5yWX7rYK3ryYjwRlc7vZtXhxV8wy3GHcYXFcGbXUL8Lk95PtEonUQMh1VHNYZVxhckuN058vBChWI7VIYieCTdEXjH+CILv3nqrAyVdSXKrPEFCbzo7sja/DrwHnfZp40vqnF4lzPiM22eepVXGZVNdQRrjC+SOkngJa2TtMzQXqNbWddkHcmq5+nW6mXGN5ZxT7KqsyNBtzLEiO5usCkKlPTjyRF7vGsZxs1uN54yRaHGOOaMmis3R8/rsr76xvUy15giQQyvOWe2UzR87kLvPcMrziDBt9yx2maKxtZ45EIr+FWnRkt7F5cAD5ebwWWmiFiB3Z0c0QlKNqgzRYUEPunkSNajW4aDpqiQa31rUQjtRkjOkUOmiEzNwPna5SWV7N7gooXe0yFekkAFf1anTJEgRiTGj12n92an95O5sEuGxHDV4ljZBs3k2tmRwC/Z7FcH2qYDGp6zqX06qcRnbVwaNYuJNlDVKLhFu0J3RHRlf9cZcC/jHM0nluHpVDYdaBPDB1bg1vqusRP/NQee23/JKTaC++erVU1+yRin3u5yd0KjGDHUsvvlHPqcGNdPHhg/dWAOaBen/Yb755E50LAMD9Rm8JxBfaeuQUDwvvm7llQJX2q/kqf7/AtP7AlPtwKvH7NSOxdjak4R3HDsjyMtf9SWXMrKzaGTLcMOp+zrigS3mUXGSrDm6JFj2KHDihxKYCrLwBSFVxhPkPYtDAddUJnsSXhqdvRy/XmTRJQ4XGk8U45wXG1JwnyjNNS1IAk85o7U26YgWMZtblc29SAEH7nfYetMQbASrssCTvdCDN+2/Vfu8SHBua6c0CTk21jb4dHk3MfmLrHE/44/AXQ41tTxpyqyAAAAAElFTkSuQmCC" alt="user--v1">
                    <p><strong>Опыт работы наших специалистов от 1,5 лет. У каждого своя специализация</p>
                </div>
                <div class="choose-us-item">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAACZElEQVR4nO2Zz2sTURDHn6KgoifFgwe9qC3ZmaDkIoi0goJ/gx4siEcv4r0g1opYiJlpNSC00KJ/geeCeOxBNM3MakHxJiJWI2oONrKtyb780rIburvyvvAIZJI338+8eW83WWOcnJycUikWnCLBH6Rw12RRrFhnxUbwarIo3jC/PkwWxSkAYMXbpFgjwcnMAVAVzjfzk8BqpgCm3p/azYIroQeYyxQAKUxY+T/NrOQPZgZgRnCIBX82c5fUuxJpoiQAGg2zjRUXW7kFngXvpQZg/VQRWGXBOz3jPl618tbvv87l4iQbKEDJxzMkuBZWF8fseFkLB0jgY3jy4M1YCQcJML44soMVXtpzssI3u8KkuNAyr/hm9u2RXakBIPFutJtvGoVKeamwh8QbtVen5OO5uDk3DVBcPnGMFXxS0JLCcLf5k4dI8as134Og+la1F4KKW601H9v8ZgHGG2Y7Cz632sJ/JEP72uYReGKZWy4vFXaS4MVeK8JRz/yoACRwrUdbPO63cafFO9v6rsJs53cjn/lRAIqV3OGO1giH4Fjnxg1apcftwouBnPlRAEjwqVX1V0Hv2icMKT60gL4Ee6HPVVeC/cOV3NGBmf8XAAtcssz9Kql3uqui7W11faDm4gBQdXg/CXywql0MY/njXW31Z+OatACwwpzV1++4kttrx6mKl62r6dp0FUa23PxfV0CxFsbyF+xY6zOCkyzwmRVumaTE/QHuscD34MbMpFmcgt/EseQAkhY7gITFDiBhsQNIWPw/AXCCgzYeskzEecCR+CDFWtQ/WOuZXQEnJycnsxX6DaP38yKo9YAgAAAAAElFTkSuQmCC" alt="graph--v1">
                    <p><strong>Доступ к нужным платным сервисам: Adobe Stock, Freepik, Midjourney, Dalle-3, ChatGPT, RunwayGen-3, Hailuo MiniMax, Pika, FlipHTML5 и многие другие</p>
                </div>
                <div class="choose-us-item">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEzklEQVR4nO1ZW2icVRA+XqvYekNfvBVpauzuzJ/UCPZBWvFJ8UGqxMYKvgkVFaUo3pCgT1Ubm92ZjUSiPiiiVVGo1XpXEK0a7cXdnfm3hkC1VkXEaqw32pXZ/2yyJNlmk+xu/kI++B92z/nPmTlnZv6Zb5ybxzzm0RRk9iTPZ4UbSOExUnyVBHeQ4BAJ/BQ9OMQCX9sYCzxKip2pAp7n4gDOJlpI8BFS+IYVi5M9JPCbPdXGWWE3Cz5sa7mmKxDiSlbYxoKHImHxMCkOssAmCoOuVCFYvmm47fTx79l/lMdLOI83skKv3dCoQoKHSOAt1uDyxiuQTbSwwJujp624hyR5d282ccGM18wtW8yC97Dgt2O3iG+kCskl9ZXeOVcsumNIYT0LHPSnl6MQVtv/9dqju+iOJQmuJ8F8dEjwJwneWbc9OJtYyAqvjC6usH5zsfM41yB0f7jq+OiGokMjhZdNhlkt2pNNnMkCn5dvoU+w1TUJmTC5jAXFB4ztfbvxjBktlCq0nGoh1NvtOwPSusg1GQPSuogE3vPR7atpy2CmE0WQsuO1LHBzhGeHF59UIcvWaZk1Kzzko9Lg4zuDUxoqaS3yZBMLy9ZBebi/tpfCRDsp/MuCB9IaXOhiglQhuYQUf2fFf0gQp3xh1CZDvMXFDKzBrWWfPeJEkuQV3hZ3NTLEzhSbzXcVsv6gV1adWEroFItpTa5xMQUJrvWH/dKkE/qHOk4z3yDF/f2DHSe4mKI7mzjRsmnzlUnDsaXV/uPzlIs5SPFpkzWTT143cVDgCR9yO13MkdbkGi/rxgmDLPhuFKeDi1zMkVa42Cvy9oTBcl5Ti39kQriGBL5nge/Silc3a16ln3iHz7vxIMUfWGHE1QDbsKJ22NuseZWwTJwV9k0ciE7koDvqFdGo7rYkbapF7Pptc9uUNbiqWfNqMq2ys/fm2pe6mCNjtcoRnH2D13KtizkoDLqqhl+rwb2d9ruYgxWfKSkSwuoJg1Z3kOIfrPiL2aCLKVKFlgUs8DML/l21YiTF5yNNgy4XU7DATZ5DeLHqJCPPSmSbYD6OaXx3xLDkIheYgshjwde8I93mYgbOw+2eiNg25WQrb81XShxWjPKu3lz7Uss8LH3nXFuyppdIYJ0nx7KTcbjNRiqipnZ5S7l3Wi+z4ICvTz6eSyalZ++Kk61G95+GLUatTmsBy4LH+CTY3q8dZ7m5uAnFD7wSX86YOrWYXa7jS2x5GFzmmgTOtSVJQb2JfzZrE4+YC9xoYZkV/iPBB12jQ6zCfST4V5lkmDWJXQlWvNZf8Y+uASi1FULLoSD0IXbEwm3dNyJZfo43sU/qua6F+9INKAxXtOtebxjLOconKaYmG2fBB1iwJxMGNxvtam2A8U0aa1OQwKU+zUiz4M7K1hsrbknlcUVDFKhQZGtE3gVXjrdpU65qM9QSUcED1ZulRk5Dd1N45ifDtnNLjq4wXBnHrfFDih95gff7JqeZyQsWaaI2Guwzv4pa1fhFqQMluME4KTNX10yQInnbXWe/U4X2s40HK7H2vuRsZjdrRujLBxAJDCPWPzRHLOU7Y7b9XF3DYyNg3xBW/HQSuz7MAu9n8rDKHQ0ggTtI8VfPcOww+ybFu44GgmIebo7xP9x+HMucIukpAAAAAElFTkSuQmCC" alt="speech-bubble-with-dots--v1">
                    <p><strong>Регулярная отчетность и связь с менеджером проекта</p>
                </div>
            </div>
        </div>
    </section>

    <section class="reviews" id="reviews">
        <div class="reviews-header">
            <h2>Отзывы клиентов</h2>
            <span class="reviews-accent"></span>
        </div>
        <div class="reviews-grid">
            <div>
                <div class="review-item">
                    <img src="media/otziv1.jpg" alt="Отзыв Интервестник" loading="lazy">
                </div>
                <div class="review-item">
                    <img src="media/otzyv_mosmerch_1x.jpg" alt="Отзыв Мосмерч" loading="lazy">
                </div>
                <div class="review-item">
                    <img src="media/otzyv_yarkie_znaki_1x.jpg" alt="Отзыв Яркие Знаки" loading="lazy">
                </div>
                <div class="review-item">
                    <img src="media/blank_krom_montazhnaya_oblast_1_kopiya_1x.jpg" alt="Отзыв КРОМ" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <section class="faq" id="faq">
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-line">
                    <span class="faq-number">1</span>
                    <h3>С чего начать?</h3>
                </div>
                <p>Выберите услугу — мы поможем с любыми вопросами.</p>
            </div>

            <div class="faq-item">
                <div class="faq-line">
                    <span class="faq-number">2</span>
                    <h3>Как сделать заявку?</h3>
                </div>
                <p>Заявку можно сделать в форме на сайте, отправить на почту, написать в мессенджере или позвонить.</p>
            </div>

            <div class="faq-item">
                <div class="faq-line">
                    <span class="faq-number">3</span>
                    <h3>Как составить ТЗ?</h3>
                </div>
                <p>При любой форме обращения с вами свяжется менеджер, выяснит все необходимые вопросы, поможет составить ТЗ.</p>
            </div>

            <div class="faq-item">
                <div class="faq-line">
                    <span class="faq-number">4</span>
                    <h3>Как оплатить?</h3>
                </div>
                <p>Оплата производится по счету, на услуги составляется договор.</p>
            </div>

            <div class="faq-item">
                <div class="faq-line">
                    <span class="faq-number">5</span>
                    <h3>Когда дедлайн?</h3>
                </div>
                <p>Все сроки заранее оговариваются, прописываются в ТЗ, в Договоре. Все дедлайны строго соблюдаются.</p>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contacts">
        <div class="contact-wrapper">
            <div class="contact-info">
                <p class="contact-label">Свяжитесь с нами</p>
                <h2>Готовы обсудить проект</h2>
                <p>Расскажите нам о своих целях — мы подготовим индивидуальное предложение и предложим лучшие решения.</p>
                <p>Работаем с клиентами по всей стране и за её пределами.</p>

                <div class="contact-details">
                    <span>Контактная информация</span>
                    <a href="tel:+79152564826">+7 915 256-48-26</a>
                    <a href="mailto:info@krommarketing.ru">info@krommarketing.ru</a>
                </div>
            </div>

            <form class="contact-form" method="post">
                <input type="hidden" name="contact_form" value="1">
                <!-- Поле-ловушка для защиты от спама (боты заполнят, пользователи не увидят) -->
                <div class="contact-honeypot" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0;pointer-events:none;">
                    <label>
                        Ваш сайт
                        <input type="text" name="company" autocomplete="off" tabindex="-1">
                    </label>
                </div>
                <div class="contact-form-title">Представьтесь, пожалуйста</div>

                <?php if ($contactStatus): ?>
                    <div class="contact-form-status <?php echo $contactStatus['type']; ?>">
                        <?php echo htmlspecialchars($contactStatus['text']); ?>
                    </div>
                <?php endif; ?>

                <label class="contact-field">
                    <span>Имя *</span>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($contactData['name']); ?>">
                </label>

                <label class="contact-field">
                    <span>Телефон *</span>
                    <input type="tel" name="phone" required value="<?php echo htmlspecialchars($contactData['phone']); ?>">
                </label>

                <label class="contact-field">
                    <span>E-mail *</span>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($contactData['email']); ?>">
                </label>

                <label class="contact-field">
                    <span>Комментарий</span>
                    <textarea name="message" rows="3" placeholder="Ваше сообщение"><?php echo htmlspecialchars($contactData['message']); ?></textarea>
                </label>

                <label class="contact-field contact-consent">
                    <input
                        type="checkbox"
                        name="consent"
                        value="1"
                        <?php echo $contactData['consent'] ? 'checked' : ''; ?>
                    >
                    <span>
                        Я согласен(а) на обработку своих персональных данных в соответствии
                        с Федеральным законом №152-ФЗ и
                        <a href="#policy">Политикой обработки и защиты персональных данных</a>.
                    </span>
                </label>

                <button type="submit" class="contact-submit">Отправить</button>
            </form>
        </div>
    </section>

    <section class="policy-section" id="policy">
        <div class="policy-header">
            <h2>Политика обработки и защиты персональных данных</h2>
            <span class="policy-accent"></span>
        </div>
        <div class="policy-body">
            <div class="policy-image">
                <img src="media/foot.png" alt="KROM creative mockup" loading="lazy">
            </div>
            <div class="policy-text">
                <p>Настоящая Политика разработана в соответствии с Федеральным законом от 27.07.2006 №152-ФЗ «О персональных данных» и регулирует порядок обработки и защиты персональных данных, осуществляемых Обществом с ограниченной ответственностью «КРОМ Маркетинг». Она описывает цели обработки, категории данных, меры безопасности и права пользователей.</p>
                <p>ИНН 9718271067, юридический адрес: 107143, г. Москва, ш. Открытое, д. 24, корп. 5В, кв. 14, сайт: www.krommarketing.ru, e-mail: info@krommarketing.ru.</p>
                <a href="media/policy.pdf" class="policy-btn">Читать</a>
            </div>
        </div>
        <div class="policy-footer">
            <div class="policy-meta">
                <div class="footer-contacts">
                    <p><strong>Контакты:</strong></p>
                    <p>Телефон: <a href="tel:+79152564826">+7 915 256-48-26</a></p>
                    <p>Email: <a href="mailto:info@krommarketing.ru">info@krommarketing.ru</a></p>
                    <p>Адрес: 107143, г. Москва, ш. Открытое, д. 24, корп. 5В, кв. 14</p>
                    <p>ИНН: 9718271067</p>
                </div>
                <div class="footer-links">
                    <p><a href="#policy">Политика конфиденциальности</a></p>
                    <p>© 2025 KROM Marketing. Все права защищены.</p>
                </div>
                <div class="policy-socials">
                    <a href="https://vk.ru/krommarketing" aria-label="VK" target="_blank" rel="noopener">vk</a>
                    <a href="https://t.me/krommarketing" aria-label="Telegram" target="_blank" rel="noopener">tg</a>
                    <a href="https://wa.me/79152564826" aria-label="WhatsApp" target="_blank" rel="noopener">wa</a>
                    <a href="https://www.youtube.com/channel/UC1jp1VNG1wv8Z4ym2JAeiTw" aria-label="YouTube" target="_blank" rel="noopener">yt</a>
                </div>
            </div>
        </div>
    </section>

    <div class="cookie-banner" role="dialog" aria-live="polite" aria-label="Уведомление об использовании файлов cookie">
        <div class="cookie-banner__content">
            <p class="cookie-banner__text">
                Мы используем файлы cookie, чтобы сайт работал корректно и помогал нам улучшать сервис.
                Продолжая пользоваться сайтом, вы соглашаетесь с их использованием.
            </p>
            <div class="cookie-banner__actions">
                <button type="button" class="cookie-banner__btn cookie-banner__btn--accept">Согласен</button>
                <a class="cookie-banner__link" href="#policy">Подробнее</a>
            </div>
        </div>
    </div>

     <script src="scripts.js"></script>
</body>
</html>