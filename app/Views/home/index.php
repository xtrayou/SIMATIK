<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMATIK | Inventory System - Fakultas Ilmu Komputer</title>

    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #3B5BDB;
            --primary-light: #5C7CFA;
            --primary-dark: #2B4ACB;
            --secondary-color: #6B7A99;
            --accent-color: #4263EB;
            --dark-color: #1a1c23;
            --light-color: #EDF2FF;
        }

        * {
            font-family: Arial, sans-serif;
        }

        .font-accent {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
        }

        .font-accent-normal {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: normal;
        }

        .text-highlight {
            position: relative;
            display: inline-block;
        }

        .text-highlight::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: -4px;
            right: -4px;
            height: 35%;
            background: rgba(92, 124, 250, 0.25);
            border-radius: 4px;
            z-index: -1;
            transform: skewX(-3deg);
        }

        /* ── Navbar ── */
        .navbar-home {
            background: transparent;
            padding: 20px 0;
            transition: all 0.4s ease;
        }

        .navbar-home.scrolled {
            padding: 10px 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            transition: all 0.3s;
        }

        .navbar-home.scrolled .navbar-brand {
            color: var(--primary-color) !important;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s;
        }

        .navbar-home.scrolled .nav-link {
            color: var(--dark-color) !important;
        }

        .nav-link:hover {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .navbar-home.scrolled .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            color: white !important;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(59, 91, 219, 0.4);
        }

        /* ── Hero ── */
        .hero-section {
            background: linear-gradient(135deg, rgba(27, 40, 101, 0.75) 0%, rgba(43, 74, 203, 0.65) 40%, rgba(59, 91, 219, 0.55) 100%),
                url('<?= base_url("img/hero.jpg") ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(92, 124, 250, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -150px;
            animation: pulseHero 8s ease-in-out infinite;
        }

        @keyframes pulseHero {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.5;
            }
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            line-height: 1.3;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 35px;
            line-height: 1.8;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-hero {
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary-color);
            border: none;
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.3);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.4);
            color: var(--primary-color);
        }

        .btn-hero-outline {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .btn-hero-outline:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-3px) scale(1.05);
        }

        /* ── Sections ── */
        .features-section,
        .how-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #E7F0FF, #F0F5FF, #E7F5FF);
            position: relative;
            overflow: hidden;
        }

        .about-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #ffffff, #f8faff, #eef5ff);
        }

        .contact-section {
            padding: 100px 0;
            background: white;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            color: var(--secondary-color);
            font-size: 1.1rem;
            margin-bottom: 50px;
            line-height: 1.8;
        }

        /* Feature cards */
        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 45px 35px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(59, 91, 219, 0.08);
            box-shadow: 0 10px 40px rgba(59, 91, 219, 0.08);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(59, 91, 219, 0.15);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 30px;
            transition: all 0.4s;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-icon.purple {
            background: linear-gradient(135deg, #3B5BDB, #5C7CFA);
            color: white;
            box-shadow: 0 10px 30px rgba(59, 91, 219, 0.3);
        }

        .feature-icon.blue {
            background: linear-gradient(135deg, #4263EB, #748FFC);
            color: white;
            box-shadow: 0 10px 30px rgba(66, 99, 235, 0.3);
        }

        .feature-icon.green {
            background: linear-gradient(135deg, #364FC7, #5C7CFA);
            color: white;
            box-shadow: 0 10px 30px rgba(54, 79, 199, 0.3);
        }

        .feature-icon.orange {
            background: linear-gradient(135deg, #4C6EF5, #91A7FF);
            color: white;
            box-shadow: 0 10px 30px rgba(76, 110, 245, 0.3);
        }

        .feature-card h4 {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--dark-color);
            margin-bottom: 15px;
        }

        .feature-card p {
            color: var(--secondary-color);
            margin-bottom: 0;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* Stats */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #1B2865, #2B4ACB, #3B5BDB, #5C7CFA, #748FFC);
            position: relative;
            overflow: hidden;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            display: block;
            margin-bottom: 10px;
        }

        .stat-label {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            opacity: 0.85;
            font-size: 1rem;
        }

        /* Peminjaman */
        .peminjaman-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #E7F0FF, #D6E6FF, #C7DBFF);
            position: relative;
            overflow: hidden;
        }

        .peminjaman-card {
            background: white;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 15px 50px rgba(59, 91, 219, 0.12);
            border: 1px solid rgba(59, 91, 219, 0.08);
            transition: all 0.3s;
        }

        .peminjaman-card:hover {
            box-shadow: 0 20px 60px rgba(59, 91, 219, 0.18);
            transform: translateY(-5px);
        }

        .peminjaman-form .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .peminjaman-form .form-control,
        .peminjaman-form .form-select {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e9ecef;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .peminjaman-form .form-control:focus,
        .peminjaman-form .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(59, 91, 219, 0.15);
        }

        .peminjaman-form .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 14px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .peminjaman-form .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(59, 91, 219, 0.4);
        }

        .peminjaman-info {
            background: linear-gradient(135deg, #1B2865, #2B4ACB, #3B5BDB, #5C7CFA);
            border-radius: 24px;
            padding: 45px;
            color: white;
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(59, 91, 219, 0.3);
        }

        .peminjaman-info h4 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .peminjaman-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .peminjaman-info-item i {
            font-size: 1.5rem;
            margin-right: 15px;
            opacity: 0.9;
        }

        .peminjaman-info-item h6 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .peminjaman-info-item p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .barang-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* About */
        .about-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .about-content p {
            color: var(--secondary-color);
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .about-list {
            list-style: none;
            padding: 0;
        }

        .about-list li {
            padding: 10px 0;
            color: var(--dark-color);
        }

        .about-list li i {
            color: var(--accent-color);
            margin-right: 10px;
        }

        /* Contact */
        .contact-info-card {
            background: var(--light-color);
            border-radius: 20px;
            padding: 40px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .contact-text h6 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .contact-text p {
            color: var(--secondary-color);
            margin-bottom: 0;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1B2865, #2B4ACB, #3B5BDB);
            padding: 60px 0 30px;
            color: white;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 15px;
        }

        .footer-text {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.8;
        }

        .footer-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            padding-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Modal Login */
        .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-header {
            border-bottom: none;
            padding: 30px 30px 0;
        }

        .modal-body {
            padding: 30px;
        }

        .modal-title {
            font-weight: 700;
            color: var(--dark-color);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(59, 91, 219, 0.1);
        }

        .btn-login-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(59, 91, 219, 0.4);
        }

        /* Scroll top */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 1000;
        }

        .scroll-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(59, 91, 219, 0.4);
        }

        .input-group-text {
            background: var(--light-color);
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 12px 0 0 12px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        @media (max-width:992px) {
            .hero-title {
                font-size: 2.5rem;
            }
        }

        @media (max-width:768px) {
            .hero-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .peminjaman-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-home fixed-top" id="navbarUtama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                <i class="bi bi-box-seam-fill me-2" style="font-size:1.8rem;"></i>
                <span>SIMA<span class="font-accent">TIK</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#permintaan">Permintaan ATK</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-login">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn btn-login" data-bs-toggle="modal" data-bs-target="#modalMasuk">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section" id="beranda">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 col-xl-7" data-aos="fade-up" data-aos-duration="1000">
                    <div class="hero-content text-center">
                        <h1 class="hero-title">
                            Sistem <span class="font-accent text-highlight">Inventaris</span> ATK<br>
                            <span style="color:rgba(255,255,255,0.9);">Fakultas <span class="font-accent">Ilmu Komputer</span></span>
                        </h1>
                        <p class="hero-subtitle">
                            Kelola alat tulis kantor dengan <span class="font-accent">mudah, efisien,</span> dan terintegrasi.<br>
                            Pantau stok, lacak penggunaan, dan buat laporan secara <span class="font-accent">real-time.</span>
                        </p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="#permintaan" class="btn btn-hero btn-hero-primary">
                                <i class="bi bi-rocket-takeoff me-2"></i>Mulai Sekarang
                            </a>
                            <a href="#fitur" class="btn btn-hero btn-hero-outline">
                                <i class="bi bi-info-circle me-2"></i>Pelajari Lebih
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur -->
    <section class="features-section" id="fitur">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill" style="font-size:1.05rem;font-weight:600;">Fitur Sistem</span>
                <h2 class="section-title mt-2">Fitur <span class="font-accent">Unggulan</span></h2>
                <p class="section-subtitle">Sistem inventaris <span class="font-accent">modern</span> untuk kemudahan pengelolaan</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon purple"><i class="bi bi-box-seam"></i></div>
                        <h4>Manajemen <span class="font-accent">Stok</span></h4>
                        <p>Kelola stok ATK dengan <span class="font-accent">mudah.</span> Catat barang masuk dan keluar secara real-time.</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon blue"><i class="bi bi-graph-up-arrow"></i></div>
                        <h4>Laporan & <span class="font-accent">Analitik</span></h4>
                        <p>Dapatkan laporan lengkap dan <span class="font-accent">analisis penggunaan</span> ATK dalam berbagai format.</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon green"><i class="bi bi-bell"></i></div>
                        <h4><span class="font-accent">Notifikasi</span> Stok</h4>
                        <p>Terima <span class="font-accent">peringatan otomatis</span> ketika stok mencapai batas minimum.</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon orange"><i class="bi bi-clipboard-check"></i></div>
                        <h4 class="font-accent-normal">Permintaan ATK</h4>
                        <p>Ajukan permintaan dengan sistem <span class="font-accent">tracking</span> yang terorganisir.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Permintaan ATK -->
    <section class="peminjaman-section" id="permintaan">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="section-title">Form <span class="font-accent">Permintaan</span> ATK</h2>
                <p class="section-subtitle">Ajukan permintaan ATK dan Barang Habis Pakai dengan <span class="font-accent">mudah</span></p>
            </div>

            <?php if (session('sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i> <?= session('sukses') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif ?>

            <?php if (session('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif ?>

            <div class="row g-4">
                <!-- Form -->
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="peminjaman-card">
                        <form class="peminjaman-form" action="<?= base_url('requests/store') ?>" method="post" id="formPermintaan">
                            <?= csrf_field() ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="namaPemohon" class="form-label">
                                        <i class="bi bi-person me-1"></i>Nama Pemohon <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="namaPemohon" name="borrower_name"
                                        value="<?= old('borrower_name') ?>"
                                        placeholder="Masukkan nama lengkap" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="unitKerja" class="form-label">
                                        <i class="bi bi-building me-1"></i>Unit Kerja / Prodi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="unitKerja" name="borrower_unit" required>
                                        <option value="">Pilih Unit Kerja</option>
                                        <?php foreach ($unitKerja as $unit): ?>
                                            <option value="<?= esc($unit) ?>" <?= old('borrower_unit') == $unit ? 'selected' : '' ?>>
                                                <?= esc($unit) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="emailPemohon" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="emailPemohon" name="email"
                                        value="<?= old('email') ?>"
                                        placeholder="nama@fasilkom.ac.id" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nimNipPemohon" class="form-label">
                                        <i class="bi bi-card-text me-1"></i>NIM / NIP
                                    </label>
                                    <input type="text" class="form-control" id="nimNipPemohon" name="borrower_identifier"
                                        value="<?= old('borrower_identifier') ?>"
                                        placeholder="Opsional">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="filterKategori" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Filter Kategori Barang
                                </label>
                                <select class="form-select" id="filterKategori">
                                    <option value="">Tampilkan Semua</option>
                                    <?php foreach ($daftarKategori as $kat): ?>
                                        <option value="<?= $kat['id'] ?>"><?= esc($kat['name']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="barangDiminta" class="form-label">
                                    <i class="bi bi-box-seam me-1"></i>Barang yang Diminta <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="barangDiminta" name="product_id" required>
                                    <option value="">Pilih Barang</option>
                                    <?php foreach ($daftarProduk as $produk): ?>
                                        <option value="<?= $produk['id'] ?>"
                                            data-kategori="<?= $produk['category_id'] ?>"
                                            data-stok="<?= $produk['current_stock'] ?>"
                                            data-satuan="<?= esc($produk['unit']) ?>"
                                            <?= old('product_id') == $produk['id'] ? 'selected' : '' ?>
                                            <?= $produk['current_stock'] <= 0 ? 'disabled' : '' ?>>
                                            <?= esc($produk['name']) ?>
                                            (Stok: <?= $produk['current_stock'] ?> <?= esc($produk['unit']) ?>)
                                            <?= $produk['current_stock'] <= 0 ? '— Stok Habis' : '' ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <div id="infoStok" class="form-text text-muted mt-1"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jumlahDiminta" class="form-label">
                                        <i class="bi bi-123 me-1"></i>Jumlah <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="number" class="form-control" id="jumlahDiminta" name="quantity"
                                            value="<?= old('quantity', 1) ?>" min="1" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggalPermintaan" class="form-label">
                                        <i class="bi bi-calendar me-1"></i>Tanggal Permintaan <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggalPermintaan" name="request_date"
                                        value="<?= old('request_date', date('Y-m-d')) ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="keteranganPermintaan" class="form-label">
                                    <i class="bi bi-card-text me-1"></i>Keperluan / Keterangan
                                </label>
                                <textarea class="form-control" id="keteranganPermintaan" name="notes"
                                    rows="3" placeholder="Jelaskan keperluan permintaan barang..."><?= old('notes') ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-submit w-100" id="btnAjukan">
                                <i class="bi bi-send me-2"></i>Ajukan Permintaan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Panel info -->
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="peminjaman-info">
                        <h4><i class="bi bi-info-circle me-2"></i>Informasi Permintaan</h4>
                        <div class="peminjaman-info-item">
                            <i class="bi bi-check-circle"></i>
                            <div>
                                <h6>Barang yang Tersedia</h6>
                                <p>Permintaan terbatas untuk ATK dan Barang Habis Pakai yang tersedia di inventaris.</p>
                            </div>
                        </div>
                        <div class="peminjaman-info-item">
                            <i class="bi bi-clock-history"></i>
                            <div>
                                <h6>Proses Persetujuan</h6>
                                <p>Permintaan diproses dalam 1×24 jam kerja setelah pengajuan.</p>
                            </div>
                        </div>
                        <div class="peminjaman-info-item">
                            <i class="bi bi-truck"></i>
                            <div>
                                <h6>Distribusi Barang</h6>
                                <p>Setelah disetujui, barang didistribusikan ke unit kerja pemohon.</p>
                            </div>
                        </div>
                        <div class="peminjaman-info-item">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <h6>Butuh Bantuan?</h6>
                                <p>Hubungi TU Fakultas di ext. 1234 atau WhatsApp 08xx-xxxx-xxxx</p>
                            </div>
                        </div>
                        <hr style="opacity:0.2;">
                        <h6 class="mb-3"><i class="bi bi-tags me-2"></i>Kategori Tersedia:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($daftarKategori as $kat): ?>
                                <span class="barang-badge"><?= esc($kat['name']) ?></span>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang -->
    <section class="about-section" id="tentang">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400" style="max-width:100%;height:auto;">
                        <defs>
                            <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#3B5BDB" />
                                <stop offset="100%" style="stop-color:#5C7CFA" />
                            </linearGradient>
                        </defs>
                        <rect x="50" y="50" width="400" height="300" rx="20" fill="url(#bgGrad)" opacity="0.1" />
                        <rect x="150" y="80" width="200" height="150" rx="10" fill="#1a1c23" />
                        <rect x="160" y="90" width="180" height="120" rx="5" fill="#3B5BDB" />
                        <rect x="170" y="100" width="160" height="20" rx="3" fill="rgba(255,255,255,0.3)" />
                        <rect x="170" y="130" width="70" height="70" rx="5" fill="rgba(255,255,255,0.4)" />
                        <rect x="250" y="130" width="80" height="30" rx="3" fill="rgba(255,255,255,0.3)" />
                        <rect x="250" y="170" width="80" height="30" rx="3" fill="rgba(255,255,255,0.3)" />
                        <rect x="225" y="230" width="50" height="20" rx="3" fill="#1a1c23" />
                        <rect x="200" y="250" width="100" height="10" rx="5" fill="#1a1c23" />
                        <rect x="160" y="280" width="180" height="40" rx="5" fill="#2d2d2d" />
                        <rect x="170" y="290" width="160" height="20" rx="3" fill="#3d3d3d" />
                        <rect x="180" y="170" width="10" height="25" rx="2" fill="rgba(255,255,255,0.8)" />
                        <rect x="195" y="160" width="10" height="35" rx="2" fill="rgba(255,255,255,0.8)" />
                        <rect x="210" y="150" width="10" height="45" rx="2" fill="rgba(255,255,255,0.8)" />
                    </svg>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="about-content">
                        <h2>Tentang <span class="font-accent">SIMATIK</span></h2>
                        <p><strong>SIMATIK</strong> (<span class="font-accent">Sistem Inventaris ATK</span>) adalah solusi digital untuk pengelolaan inventaris ATK di <span class="font-accent">Fakultas Ilmu Komputer.</span></p>
                        <ul class="about-list">
                            <li><i class="bi bi-check-circle-fill"></i> Pencatatan barang masuk dan keluar otomatis</li>
                            <li><i class="bi bi-check-circle-fill"></i> Manajemen kategori dan produk yang fleksibel</li>
                            <li><i class="bi bi-check-circle-fill"></i> Sistem peringatan stok minimum</li>
                            <li><i class="bi bi-check-circle-fill"></i> Laporan ekspor ke Excel dan PDF</li>
                            <li><i class="bi bi-check-circle-fill"></i> Antarmuka yang mudah digunakan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-md-3 col-6">
                    <div class="stat-item"><span class="stat-number" data-count="500">0</span><span class="stat-label">Jenis Barang</span></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item"><span class="stat-number" data-count="1000">0</span><span class="stat-label">Transaksi/Bulan</span></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item"><span class="stat-number" data-count="50">0</span><span class="stat-label">Kategori</span></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item"><span class="stat-number" data-count="24">0</span><span class="stat-label">Jam Aktif</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section class="contact-section" id="kontak">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="section-title">Hubungi <span class="font-accent">Kami</span></h2>
                <p class="section-subtitle">Ada pertanyaan? <span class="font-accent">Jangan ragu</span> untuk menghubungi kami</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="fade-up">
                    <div class="contact-info-card">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                                    <div class="contact-text">
                                        <h6>Alamat</h6>
                                        <p>Fakultas Ilmu Komputer<br>Universitas XYZ<br>Jl. Pendidikan No. 123</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                                    <div class="contact-text">
                                        <h6>Telepon</h6>
                                        <p>(021) 123-4567<br>Ext. 1234</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                                    <div class="contact-text">
                                        <h6>Email</h6>
                                        <p>inventaris@fasilkom.ac.id</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon"><i class="bi bi-clock"></i></div>
                                    <div class="contact-text">
                                        <h6>Jam Operasional</h6>
                                        <p>Senin–Jumat: 08:00–16:00<br>Sabtu: 08:00–12:00</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand"><i class="bi bi-box-seam-fill me-2"></i>SIMA<span class="font-accent" style="color:rgba(255,255,255,0.9);">TIK</span></div>
                    <p class="footer-text">Sistem Informasi Manajemen ATK untuk <span class="font-accent">Fakultas Ilmu Komputer.</span></p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="footer-title">Menu</h5>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#fitur">Fitur</a></li>
                        <li><a href="#tentang">Tentang</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="footer-title">Layanan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Manajemen Stok</a></li>
                        <li><a href="#">Laporan</a></li>
                        <li><a href="#">Permintaan ATK</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="footer-title">Fakultas Ilmu Komputer</h5>
                    <ul class="footer-links">
                        <li><a href="#">Website Fakultas</a></li>
                        <li><a href="#">Portal Akademik</a></li>
                        <li><a href="#">Perpustakaan</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> SIMATIK - Fakultas Ilmu Komputer. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Login -->
    <div class="modal fade" id="modalMasuk" tabindex="-1" aria-labelledby="judulModalMasuk" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulModalMasuk">
                        <i class="bi bi-box-seam-fill me-2" style="color:var(--primary-color);"></i>Masuk ke <span class="font-accent">SIMATIK</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <?php if (session('loginError')): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i><?= session('loginError') ?>
                        </div>
                    <?php endif ?>

                    <!-- ✅ POST ke /auth/login bukan GET ke /dashboard -->
                    <form action="<?= base_url('auth/login') ?>" method="POST" id="formLogin">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="inputUsername" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="inputUsername" name="username"
                                    value="<?= old('username') ?>" placeholder="Masukkan username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="inputPassword" name="password"
                                    placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" id="btnTogglePassword"
                                    onclick="togglePassword()">
                                    <i class="bi bi-eye" id="ikonPassword"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ingatSaya" name="remember">
                                <label class="form-check-label" for="ingatSaya">Ingat saya</label>
                            </div>
                            <a href="#" class="text-decoration-none" style="color:var(--primary-color);font-size:0.9rem;">Lupa password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login-submit">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </button>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <p class="mb-0 text-muted" style="font-size:0.9rem;">
                            Belum punya akun? <a href="#" class="text-decoration-none" style="color:var(--primary-color);">Hubungi Admin</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top -->
    <button class="scroll-top" id="tombolScrollAtas">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // ── Init AOS ────────────────────────────────────────────────
        AOS.init({
            once: true,
            offset: 100
        });

        // ── Navbar scroll ────────────────────────────────────────────
        const navbar = document.getElementById('navbarUtama');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // ── Scroll to top ────────────────────────────────────────────
        const tombolScrollAtas = document.getElementById('tombolScrollAtas');
        window.addEventListener('scroll', () => {
            tombolScrollAtas.classList.toggle('show', window.scrollY > 300);
        });
        tombolScrollAtas.addEventListener('click', () => window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));

        // ── Smooth scroll navigasi ───────────────────────────────────
        document.querySelectorAll('a[href^="#"]').forEach(tautan => {
            tautan.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    const offsetNavbar = navbar.offsetHeight;
                    window.scrollTo({
                        top: target.offsetTop - offsetNavbar,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // ── Counter animasi ──────────────────────────────────────────
        function animasiCounter(el) {
            const target = +el.getAttribute('data-count');
            const increment = target / 200;
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    el.textContent = target + '+';
                    clearInterval(timer);
                } else {
                    el.textContent = Math.ceil(current);
                }
            }, 8);
        }

        const observerCounter = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    document.querySelectorAll('.stat-number').forEach(animasiCounter);
                    observerCounter.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        const seksiStats = document.querySelector('.stats-section');
        if (seksiStats) observerCounter.observe(seksiStats);

        // ── Set tanggal minimal ke hari ini ──────────────────────────
        const inputTanggal = document.getElementById('tanggalPermintaan');
        if (inputTanggal && !inputTanggal.value) {
            inputTanggal.value = new Date().toISOString().split('T')[0];
        }
        if (inputTanggal) {
            inputTanggal.setAttribute('min', new Date().toISOString().split('T')[0]);
        }

        // ── Filter barang berdasarkan kategori ───────────────────────
        const selectKategori = document.getElementById('filterKategori');
        const selectBarang = document.getElementById('barangDiminta');
        const infoStok = document.getElementById('infoStok');

        if (selectKategori && selectBarang) {
            selectKategori.addEventListener('change', function() {
                const idKategori = this.value;
                selectBarang.value = '';
                infoStok.textContent = '';

                Array.from(selectBarang.options).forEach(opt => {
                    if (!opt.value) return; // skip placeholder
                    if (!idKategori || opt.dataset.kategori === idKategori) {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                });
            });
        }

        // ── Info stok saat barang dipilih ────────────────────────────
        if (selectBarang) {
            selectBarang.addEventListener('change', function() {
                const opsi = this.options[this.selectedIndex];
                if (opsi && opsi.value) {
                    const stok = opsi.dataset.stok;
                    const satuan = opsi.dataset.satuan;
                    infoStok.textContent = `Stok tersedia: ${stok} ${satuan}`;
                    infoStok.className = stok <= 0 ? 'form-text text-danger' : 'form-text text-success';
                } else {
                    infoStok.textContent = '';
                }
            });
        }

        // ── Submit: disable tombol agar tidak double-submit ──────────
        const formPermintaan = document.getElementById('formPermintaan');
        const btnAjukan = document.getElementById('btnAjukan');
        if (formPermintaan) {
            formPermintaan.addEventListener('submit', function() {
                btnAjukan.disabled = true;
                btnAjukan.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });
        }

        // ── Toggle password visibility ───────────────────────────────
        function togglePassword() {
            const inputPw = document.getElementById('inputPassword');
            const ikon = document.getElementById('ikonPassword');
            if (inputPw.type === 'password') {
                inputPw.type = 'text';
                ikon.className = 'bi bi-eye-slash';
            } else {
                inputPw.type = 'password';
                ikon.className = 'bi bi-eye';
            }
        }
    </script>
</body>

</html>