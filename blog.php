<?php
$pageTitle = 'Блог KROM Marketing — статьи о сайтах, приложениях и digital-стратегии';
$pageDescription = 'Блог KROM Marketing: экспертные статьи о разработке сайтов и приложений, digital-стратегии, окупаемости и росте бизнеса.';
$pageCanonical = 'https://krommarketing.ru/blog.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="media/logo_icon.png" type="image/png" />

  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="<?= htmlspecialchars($pageCanonical) ?>" />

  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="KROM Marketing" />
  <meta property="og:url" content="<?= htmlspecialchars($pageCanonical) ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <meta property="og:image" content="https://krommarketing.ru/media/og-cover.jpg" />

  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <meta name="twitter:image" content="https://krommarketing.ru/media/og-cover.jpg" />

  <link rel="stylesheet" href="style-base.css" />
  <link rel="stylesheet" href="style-responsive.css" />
  <link rel="stylesheet" href="style-sections.css" />
  <link rel="stylesheet" href="blog-pages.css" />

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Blog",
    "name": "Блог KROM Marketing",
    "url": "https://krommarketing.ru/blog.php",
    "publisher": {
      "@type": "Organization",
      "name": "KROM Marketing",
      "url": "https://krommarketing.ru/"
    }
  }
  </script>
  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Главная",
      "item": "https://krommarketing.ru/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Блог",
      "item": "https://krommarketing.ru/blog.php"
    }
  ]
}
</script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]function(){(m[i].a=m[i].a[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=107710091', 'ym');
        ym(107710091, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/107710091" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</head>
<body>

<header class="header">
  <div class="header-top">
    <a href="/" class="logo-section">
      <img src="media/logo_krom_gif.gif" alt="KROM Logo" class="logo-image">
      <span class="logo-text">КРОМ</span>
    </a>

    <nav class="navigation" aria-label="Основная навигация">
      <a href="/" class="nav-link">Главная</a>
      <a href="/services.php" class="nav-link">Услуги</a>
      <a href="/blog.php" class="nav-link">Блог</a>
      <a href="/#contacts" class="nav-link">Контакты</a>
    </nav>

    <button class="menu-toggle" type="button" aria-label="Открыть меню">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</header>

<nav class="breadcrumbs" aria-label="Хлебные крошки">
  <ol class="breadcrumbs__list">
    <li class="breadcrumbs__item"><a href="/">Главная</a></li>
    <li class="breadcrumbs__item breadcrumbs__item--current"><span>Блог</span></li>
  </ol>
</nav>

<section class="blog-hero">
  <div class="blog-hero__inner">
    <span class="blog-hero__eyebrow">Экспертиза KROM Marketing</span>
    <h1>Блог KROM Marketing</h1>
    <p>Публикуем материалы для бизнеса о сайтах, приложениях, digital-стратегии и решениях, которые влияют не только на внешний вид проекта, но и на заявки, повторные продажи и окупаемость.</p>
  </div>
</section>

<section class="blog-list">
  <article class="post-card">
    <a class="post-card__image" href="article-app-investment.php">
      <img src="media/webapp.jpg" alt="Разработка приложения как инвестиция" loading="lazy">
    </a>
    <div class="post-card__content">
      <div class="post-card__meta">
        <span>Статья</span>
        <span>Разработка приложений</span>
        <time datetime="2026-03-09">09 марта 2026</time>
      </div>

      <h2>
        <a href="article-app-investment.php" style="color:inherit;text-decoration:none;">
          Разработка приложения как инвестиция: где бизнес зарабатывает, а где теряет деньги
        </a>
      </h2>

      <p>Разбираем, когда приложение действительно окупается: за счет повторных продаж, автоматизации, подписки и роста LTV. Внутри — логика расчета, MVP и частые ошибки, которые сжигают бюджет.</p>

      <div class="post-card__tags">
        <span>MVP</span>
        <span>Unit-экономика</span>
        <span>Окупаемость</span>
      </div>

      <div class="post-card__actions">
        <a class="btn-neon" href="article-app-investment.php">Читать статью</a>
        <a class="btn-secondary" href="https://t.me/krommarketing" target="_blank" rel="noopener">Обсудить проект</a>
      </div>
    </div>
  </article>

  <article class="post-card">
    <a class="post-card__image" href="article-site-or-app.php">
      <img src="media/2.jpg" alt="Сайт или приложение для бизнеса" loading="lazy">
    </a>
    <div class="post-card__content">
      <div class="post-card__meta">
        <span>Статья</span>
        <span>Стратегия</span>
        <time datetime="2026-03-08">08 марта 2026</time>
      </div>

      <h2>
        <a href="article-site-or-app.php" style="color:inherit;text-decoration:none;">
          Разработка сайта или приложения: что выбрать бизнесу и почему иногда нужны оба варианта
        </a>
      </h2>

      <p>Показываем разницу без громких лозунгов: сайт приводит клиентов и закрывает доверие, приложение возвращает и автоматизирует повторные действия. В статье — признаки, когда нужен один инструмент, а когда связка.</p>

      <div class="post-card__tags">
        <span>Сайт</span>
        <span>Приложение</span>
        <span>Digital-стратегия</span>
      </div>

      <div class="post-card__actions">
        <a class="btn-neon" href="article-site-or-app.php">Читать статью</a>
        <a class="btn-secondary" href="/services.php">Посмотреть услуги</a>
      </div>
    </div>
  </article>
</section>

<footer class="site-footer">
        <div class="site-footer__inner">

            <div class="site-footer__brand">
                <a href="/" class="site-footer__logo">
                    <img src="media/logo_krom_gif.gif" alt="KROM Logo" class="site-footer__logo-img">
                    <span class="site-footer__logo-text">КРОМ</span>
                </a>
                <p class="site-footer__tagline">Маркетинговое и рекламное агентство полного цикла в Москве.</p>
                <div class="site-footer__socials">
                    <a href="https://vk.ru/krommarketing" class="site-footer__social" aria-label="VK" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C5.144 10.716 4.55 8.466 4.55 7.992c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V9.721c-.068-1.186-.695-1.287-.695-1.71 0-.204.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.169.508.271.508.22 0 .407-.136.813-.542 1.253-1.406 2.151-3.574 2.151-3.574.119-.254.322-.491.762-.491h1.744c.525 0 .643.271.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.202 1.253.745.847 1.32 1.558 1.473 2.05.17.49-.085.745-.576.745z"/></svg>
                    </a>
                    <a href="https://t.me/krommarketing" class="site-footer__social" aria-label="Telegram" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </a>
                    <a href="https://wa.me/79152564826" class="site-footer__social" aria-label="WhatsApp" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                    </a>
                    <a href="https://www.youtube.com/channel/UC1jp1VNG1wv8Z4ym2JAeiTw" class="site-footer__social" aria-label="YouTube" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            <nav class="site-footer__nav" aria-label="Навигация в футере">
                <h3 class="site-footer__col-title">Навигация</h3>
                <ul class="site-footer__nav-list">
                    <li><a href="/">Главная</a></li>
                    <li><a href="/services.php">Услуги</a></li>
                    <li><a href="/blog.php">Блог</a></li>
                    <li><a href="/#contacts">Контакты</a></li>
                </ul>
            </nav>

            <div class="site-footer__contacts">
                <h3 class="site-footer__col-title">Контакты</h3>
                <ul class="site-footer__contact-list">
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.06 6.06l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <a href="tel:+79152564826">+7 915 256-48-26</a>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:info@krommarketing.ru">info@krommarketing.ru</a>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>107143, г. Москва,<br>ш. Открытое, д. 24, корп. 5В</span>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span>ИНН: 9718271067</span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="site-footer__bottom">
            <span class="site-footer__copy">© 2025 KROM Marketing. Все права защищены.</span>
            <a href="/#policy" class="site-footer__policy-link">Политика конфиденциальности</a>
        </div>
    </footer>

<script src="scripts.js"></script>
</body>
</html>