@if(config('features.website_enabled'))
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary Meta Tags -->
    <title>سند الطالب - الملجأ الأول لطالب البكالوريا | +17,500 سؤال مؤتمت</title>
    <meta name="title" content="سند الطالب - الملجأ الأول لطالب البكالوريا | +17,000 سؤال مؤتمت">
    <meta name="description" content="راجع منهاجك كاملاً واختبر نفسك مع أكثر من 17,000 سؤال مؤتمت. تطبيق سند الطالب يوفر أسئلة عشوائية، شرح مفصل، وميزة الأوفلاين للعلوم، الرياضيات، الفيزياء، الكيمياء والديانة.">
    <meta name="keywords" content="سند الطالب, تطبيق سند الطالب, البكالوريا سوريا, أسئلة البكالوريا, مراجعة البكالوريا, أسئلة مؤتمتة, علوم بكالوريا, رياضيات بكالوريا, فيزياء بكالوريا, كيمياء بكالوريا, ديانة بكالوريا, تحميل تطبيق دراسي, التعليم في سوريا">
    <meta name="author" content="فريق سند الطالب">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Arabic">
    <meta name="revisit-after" content="7 days">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="سند الطالب - الملجأ الأول لطالب البكالوريا | +17,000 سؤال مؤتمت">
    <meta property="og:description" content="راجع منهاجك كاملاً واختبر نفسك مع أكثر من 17,000 سؤال مؤتمت. تطبيق سند الطالب - الأوفلاين، أسئلة عشوائية، شرح مفصل، ومؤقت ذكي.">
    <meta property="og:image" content="{{ asset('assets/img/brand/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="سند الطالب">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="سند الطالب - الملجأ الأول لطالب البكالوريا">
    <meta property="twitter:description" content="راجع منهاجك كاملاً واختبر نفسك مع أكثر من 10,000 سؤال مؤتمت. تطبيق سند الطالب للبكالوريا.">
    <meta property="twitter:image" content="{{ asset('assets/img/brand/logo.png') }}">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/brand/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/brand/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/brand/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/brand/logo.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Theme Color -->
    <meta name="theme-color" content="#0071bc">
    <meta name="msapplication-TileColor" content="#0071bc">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/brand/logo.png') }}">

    <!-- Schema.org markup for Google -->
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "EducationalOrganization",
          "name": "سند الطالب",
          "description": "تطبيق تعليمي يوفر أكثر من 10,000 سؤال مؤتمت لطلاب البكالوريا في سوريا",
          "url": "{{ url('/') }}",
      "logo": "{{ asset('assets/img/brand/logo.png') }}",
      "image": "{{ asset('assets/img/brand/logo.png') }}",
      "sameAs": [
        "https://t.me/SanadAlTaleb"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "Customer Support",
        "availableLanguage": ["Arabic"]
      }
    }
    </script>

    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "MobileApplication",
          "name": "تطبيق سند الطالب",
          "operatingSystem": "Android",
          "applicationCategory": "EducationalApplication",
          "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "ratingCount": "1000"
          }
        }
    </script>

    <!-- Google Analytics (Replace GA_MEASUREMENT_ID with your actual ID) -->
    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'GA_MEASUREMENT_ID');
    </script> -->

    <!-- Google Search Console Verification (Add your verification code) -->
    <!-- <meta name="google-site-verification" content="YOUR_VERIFICATION_CODE" /> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0071bc 0%, #005a94 100%);
            color: #333;
            overflow-x: hidden;
        }

        .header {
            background: rgba(255, 255, 255, 0.98);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            height: 60px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #0071bc;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 0;
            height: 2px;
            background: #0071bc;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .hero {
            padding: 100px 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="%23ffffff" opacity="0.1"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .hero-logo {
            max-width: 200px;
            margin: 0 auto 30px;
            animation: fadeInDown 0.8s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease;
        }

        .hero p {
            font-size: 22px;
            margin-bottom: 40px;
            animation: fadeInUp 1s ease;
            opacity: 0.95;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cta-button {
            display: inline-block;
            padding: 18px 45px;
            background: white;
            color: #0071bc;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.2s ease;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: #f0f1fc;
        }

        .stats {
            background: white;
            padding: 60px 20px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }

        .stat-item {
            padding: 30px;
            border-radius: 15px;
            background: linear-gradient(135deg, #0071bc 0%, #005a94 100%);
            color: white;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-10px);
        }

        .stat-number {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 18px;
        }

        .features {
            padding: 80px 20px;
            background: linear-gradient(135deg, #0071bc 0%, #005a94 100%);
            color: white;
        }

        .section-title {
            text-align: center;
            font-size: 42px;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            right: 50%;
            transform: translateX(50%);
            width: 80px;
            height: 4px;
            background: white;
            border-radius: 2px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .feature-desc {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .subjects {
            background: white;
            padding: 80px 20px;
        }

        .subjects .section-title::after {
            background: #0071bc;
        }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .subject-card {
            background: linear-gradient(135deg, #0071bc 0%, #005a94 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .subject-card:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 113, 188, 0.4);
        }

        .download-section {
            background: linear-gradient(135deg, #005a94 0%, #0071bc 100%);
            padding: 100px 20px;
            text-align: center;
            color: white;
        }

        .download-section h2 {
            font-size: 42px;
            margin-bottom: 30px;
        }

        .download-section p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .download-button {
            display: inline-block;
            padding: 20px 50px;
            background: white;
            color: #0071bc;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .download-button:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            background: #f0f1fc;
        }

        .download-button.primary {
            background: #0071bc;
            color: white;
            border: 2px solid white;
        }

        .download-button.primary:hover {
            background: #005a94;
        }

        .download-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer {
            background: #1a1a2e;
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .footer-content {
            margin-bottom: 20px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #0071bc;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 18px;
            }

            .nav-links {
                display: none;
            }

            .section-title {
                font-size: 32px;
            }

            .logo-img {
                height: 50px;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body>
<header class="header">
    <div class="container">
        <nav class="nav">
            <div class="logo-container">
                <img src="{{ asset('assets/img/brand/sanad.jpg') }}" alt="سند الطالب" class="logo-img">
            </div>
            <ul class="nav-links">
                <li><a href="#home">الرئيسية</a></li>
                <li><a href="#features">المميزات</a></li>
                <li><a href="#subjects">المواد</a></li>
                <li><a href="#download">التحميل</a></li>
            </ul>
        </nav>
    </div>
</header>

<section id="home" class="hero">
    <div class="container">
        <img src="{{ asset('assets/img/brand/logowhite.png') }}" alt="شعار سند الطالب" class="hero-logo">
        <h1>الملجأ الأول لطالب البكالوريا 🎯</h1>
        <h2>الموقع الرسمي لتطبيق سند الطالب</h2>
        <p>
            تطبيق سند الطالب الذي يحوي آلاف الأسئلة المؤتمتة والتحريرية مع استخدام تقنية flashcards التي أثبتت جدارتها عالمياً مما يتيح للطالب اختبار نفسه بأفضل الطرق، دون الحاجة للإنترنت مع تصميم أنيق وسلاسة استخدام
        </p>
        <a href="#download" class="cta-button pulse">جرب التطبيق مجاناً</a>
    </div>
</section>

<section class="stats">
    <div class="container">
        <h2 class="section-title" style="color: #0071bc;">إحصائيات التطبيق</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">+17500</div>
                <div class="stat-label">سؤال مؤتمت + تحريري</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">8</div>
                <div class="stat-label">مادة دراسية</div>
            </div>
            {{-- <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">مجاني</div>
            </div> --}}
        </div>
    </div>
</section>

<section id="features" class="features">
    <div class="container">
        <h2 class="section-title">مميزات التطبيق الخارقة 🔥</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <div class="feature-title">الأوفلاين</div>
                <div class="feature-desc">حل واختبر دراستك بدون الحاجة للإنترنت - صار فيك تحل بدون حجج!</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎲</div>
                <div class="feature-title">أسئلة عشوائية</div>
                <div class="feature-desc">حدد الدروس يلي بدك ياها والتطبيق بيعطيك اختبار شامل</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <div class="feature-title">تصنيف الإجابات</div>
                <div class="feature-desc">صنف إجاباتك الخاطئة والمفضلة لمراجعة أفضل</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">✍️</div>
                <div class="feature-title">شرح مفصل</div>
                <div class="feature-desc">توضيح وشرح لكيفية حل الأسئلة بطريقة سهلة ومفهومة</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⏰</div>
                <div class="feature-title">مؤقت ذكي</div>
                <div class="feature-desc">احسب سرعتك بالحل - الوقت مهم جداً بالنسبة لطالب البكلوريا</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👨‍🏫</div>
                <div class="feature-title">إشراف نخبة</div>
                <div class="feature-desc">بإشراف نخبة من أساتذة البكالوريا المميزين</div>
            </div>
        </div>
    </div>
</section>

<section id="subjects" class="subjects">
    <div class="container">
        <h2 class="section-title" style="color: #0071bc;">المواد المتوفرة 📚</h2>
        @php
            $types = App\Models\Type::where('is_active',1)->orderBy('order')->get();
        @endphp
        @foreach($types as $type)
            @if($type->id != 1 && $type->id != 2)
                @continue
            @endif
            <div class="type-header" style="display: flex; align-items: center; gap: 12px; margin: 40px 0 15px 0;">
                <span style="display: inline-block; width: 38px; height: 38px; background: linear-gradient(135deg, #0071bc 60%, #00c6ff 100%); border-radius: 50%; color: #fff; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,113,188,0.10);">
                    <i class="fas fa-certificate"></i>
                </span>
                <h3 style="margin: 0; font-size: 1.6rem; font-weight: 700; color: #0071bc; letter-spacing: 1px;">
                    {{ $type->name }}
                </h3>
            </div>
            <div class="subjects-grid">
                @php
                    $subjects = $type->subjects->where('is_active',1);
                @endphp
                @foreach($subjects as $subject)
                    <div class="subject-card">{{ $subject->name }}</div>
                @endforeach
            </div>
        @endforeach
        <p style="text-align: center; margin-top: 30px; color: #0071bc; font-weight: bold; font-size: 18px;">
            يتم العمل على تطوير ما تبقى من المواد...
        </p>
    </div>
</section>

<section id="download" class="download-section">
    <div class="container">
        <h2>لساتك عم تفكّر وما حمّلت التطبيق؟</h2>
        <p>حمّل التطبيق الآن مجاناً وتمتع بميزات التطبيق الفريدة من نوعها</p>
        <p>الإصدار الأخير: V1.5.2</p>
        <div class="download-buttons">
            <a href="{{ asset('assets/apps/Sanad Al Taleb V1.5.2.apk') }}" class="download-button" download>
                <i class="fas fa-download"></i> تحميل مباشر (APK)
            </a>
            <a href="https://t.me/SanadAlTaleb/1800" target="_blank" class="download-button">
                <i class="fab fa-telegram"></i> تحميل عبر التلجرام
            </a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <h3>سند الطالب - فريق متخصص في خدمة طلاب البكالوريا</h3>
            <div class="footer-links">
                <a href="https://t.me/SanadAlTaleb/1800" target="_blank">تليجرام</a>
                <a href="#features">المميزات</a>
                <a href="#subjects">المواد</a>
                <a href="#download">التحميل</a>
            </div>
        </div>
        <p>&copy; 2025 سند الطالب. جميع الحقوق محفوظة.</p>
        <p style="margin-top: 10px; opacity: 0.8;">#تطبيق_سند_الطالب #الملجأ_الأول_لطالب_البكلوريا</p>
    </div>
</footer>

<script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all feature cards and stat items
    document.querySelectorAll('.feature-card, .stat-item, .subject-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });

    // Add hover effect to subject cards
    document.querySelectorAll('.subject-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.animation = 'pulse 0.5s';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    });
</script>

<!-- Structured Data for Breadcrumbs -->
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "الرئيسية",
        "item": "{{ url('/') }}"
      }]
    }
</script>
</body>
</html>
@else
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>سند الطالب - قيد الصيانة</title>
        <style>
            body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f8f9fa; color: #333; }
            .container { text-align: center; padding: 20px; }
            h1 { color: #0071bc; }
            a { color: #0071bc; text-decoration: none; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>الموقع قيد الصيانة</h1>
        </div>
    </body>
    </html>
@endif
