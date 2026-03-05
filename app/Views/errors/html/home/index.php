<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris ATK - Fakultas Ilmu Komputer</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google Fonts - Accent Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Home Page CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">

    <style>
        :root {
            --primary-color: #3B5BDB;
            --primary-light: #5C7CFA;
            --primary-dark: #2B4ACB;
            --secondary-color: #6B7A99;
            --accent-color: #4263EB;
            --dark-color: #1a1c23;
            --light-color: #EDF2FF;
            --bg-gradient: linear-gradient(135deg, #3B5BDB 0%, #5C7CFA 50%, #748FFC 100%);
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

        .text-highlight-green::after {
            background: rgba(81, 207, 102, 0.3);
        }

        .text-highlight-purple::after {
            background: rgba(132, 94, 247, 0.3);
        }

        .text-glow {
            background: linear-gradient(135deg, #5C7CFA, #748FFC);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Navbar Styles */
        .navbar-home {
            background: transparent;
            backdrop-filter: none;
            box-shadow: none;
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
            transition: all 0.3s ease;
        }

        .navbar-home.scrolled .navbar-brand {
            color: var(--primary-color) !important;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(59, 91, 219, 0.4);
        }

        /* Hero Section */
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

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%235C7CFA" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
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
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {

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

        .hero-title .font-accent {
            font-size: 1.05em;
            font-weight: 700;
            letter-spacing: 0;
        }

        .hero-title .text-highlight::after {
            background: rgba(92, 124, 250, 0.4);
            bottom: 2px;
            height: 40%;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 35px;
            line-height: 1.8;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .hero-image {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .btn-hero {
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
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
            background: linear-gradient(135deg, #ffffff 0%, #f0f5ff 100%);
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
            border-color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.3);
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #E7F0FF 0%, #F0F5FF 50%, #E7F5FF 100%);
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 91, 219, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 15s ease-in-out infinite;
        }

        .features-section::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(92, 124, 250, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 12s ease-in-out infinite reverse;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
            letter-spacing: -0.5px;
        }

        .section-title .font-accent {
            font-weight: 700;
            letter-spacing: 0;
        }

        .section-subtitle {
            color: var(--secondary-color);
            font-size: 1.1rem;
            margin-bottom: 50px;
            line-height: 1.8;
        }

        .section-subtitle .font-accent {
            color: var(--primary-dark);
            font-size: 1.05em;
        }

        .badge {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge.bg-primary {
            background: linear-gradient(135deg, #3B5BDB 0%, #5C7CFA 100%) !important;
        }

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
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(59, 91, 219, 0.15);
            border-color: rgba(59, 91, 219, 0.15);
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
            transition: all 0.4s ease;
            position: relative;
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

        /* About Section */
        .about-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 50%, #eef5ff 100%);
            position: relative;
            overflow: hidden;
        }

        .about-section::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(59, 91, 219, 0.08) 0%, rgba(92, 124, 250, 0.08) 100%);
            border-radius: 50%;
            top: 50px;
            left: -150px;
            z-index: 0;
        }

        .about-image {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .about-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 20px;
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

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #1B2865 0%, #2B4ACB 25%, #3B5BDB 50%, #5C7CFA 75%, #748FFC 100%);
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,160L48,144C96,128,192,96,288,90.7C384,85,480,107,576,128C672,149,768,171,864,165.3C960,160,1056,128,1152,122.7C1248,117,1344,139,1392,149.3L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            background-size: cover;
            top: 0;
            left: 0;
        }

        .stats-section::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,160L48,176C96,192,192,224,288,218.7C384,213,480,171,576,165.3C672,160,768,192,864,197.3C960,203,1056,181,1152,165.3C1248,149,1344,139,1392,133.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            bottom: 0;
            left: 0;
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
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* How It Works Section */
        .how-section {
            padding: 100px 0;
            background: linear-gradient(180deg, #F8FAFF 0%, #EDF2FF 100%);
            position: relative;
        }

        .how-section::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(92, 124, 250, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: 50px;
            right: 100px;
        }

        .step-card {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .step-card h5 {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .step-card p {
            color: var(--secondary-color);
        }

        /* Contact Section */
        .contact-section {
            padding: 100px 0;
            background: white;
        }

        .contact-info-card {
            background: var(--light-color);
            border-radius: 20px;
            padding: 40px;
            height: 100%;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .contact-item:last-child {
            margin-bottom: 0;
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
            background: linear-gradient(135deg, #1B2865 0%, #2B4ACB 50%, #3B5BDB 100%);
            padding: 60px 0 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.03" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,208C672,213,768,203,864,186.7C960,171,1056,149,1152,154.7C1248,160,1344,192,1392,208L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            bottom: 0;
            left: 0;
            opacity: 0.5;
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
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
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

        /* Login Modal */
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
            letter-spacing: -0.3px;
        }

        .modal-title .font-accent {
            color: var(--primary-color);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
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

        /* Scroll to top */
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
            transition: all 0.3s ease;
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

        /* Peminjaman Section */
        .peminjaman-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #E7F0FF 0%, #D6E6FF 50%, #C7DBFF 100%);
            position: relative;
            overflow: hidden;
        }

        .peminjaman-section::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 91, 219, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -150px;
            left: -150px;
        }

        .peminjaman-section::after {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(92, 124, 250, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            right: -150px;
        }

        .peminjaman-card {
            background: white;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 15px 50px rgba(59, 91, 219, 0.12);
            border: 1px solid rgba(59, 91, 219, 0.08);
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
        }

        .peminjaman-form .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(59, 91, 219, 0.4);
        }

        .peminjaman-info {
            background: linear-gradient(135deg, #1B2865 0%, #2B4ACB 50%, #3B5BDB 80%, #5C7CFA 100%);
            border-radius: 24px;
            padding: 45px;
            color: white;
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(59, 91, 219, 0.3);
        }

        .peminjaman-info::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -50px;
        }

        .peminjaman-info::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -50px;
            left: -30px;
        }

        .peminjaman-info h4 {
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .peminjaman-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
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

        .barang-kategori {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .barang-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .feature-card h4 {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 12px;
            color: var(--dark-color);
        }

        .feature-card h4 .font-accent {
            color: var(--primary-color);
        }

        .feature-card p .font-accent {
            color: var(--primary-dark);
            font-size: 0.95em;
        }

        .about-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .about-content h2 .font-accent {
            color: var(--primary-color);
            font-size: 1.05em;
        }

        .about-content p .font-accent {
            color: var(--primary-dark);
        }

        .stat-label {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            opacity: 0.85;
            font-size: 1rem;
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

        /* Responsive */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-image {
                margin-top: 50px;
            }

            .peminjaman-info {
                margin-top: 30px;
            }
        }

        @media (max-width: 768px) {
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
    <nav class="navbar navbar-expand-lg navbar-home fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                <i class="bi bi-box-seam-fill me-2" style="font-size: 1.8rem;"></i>
                <span>SIMA<span class="font-accent">TIK</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#peminjaman">Permintaan ATK</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <a href="#" class="btn btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 col-xl-7" data-aos="fade-up" data-aos-duration="1000">
                    <div class="hero-content text-center">
                        <h1 class="hero-title">
                            Sistem <span class="font-accent text-highlight">Inventaris</span> ATK<br>
                            <span style="color: rgba(255,255,255,0.9);">Fakultas <span class="font-accent">Ilmu Komputer</span></span>
                        </h1>
                        <p class="hero-subtitle">
                            Kelola alat tulis kantor dengan <span class="font-accent">mudah, efisien,</span> dan terintegrasi.<br>
                            Pantau stok, lacak penggunaan, dan buat laporan secara <span class="font-accent">real-time.</span>
                        </p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="#peminjaman" class="btn btn-hero btn-hero-primary">
                                <i class="bi bi-rocket-takeoff me-2"></i>Mulai Sekarang
                            </a>
                            <a href="#features" class="btn btn-hero btn-hero-outline">
                                <i class="bi bi-info-circle me-2"></i>Pelajari Lebih
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill" style="font-size:1.05rem;font-weight:600;">
                    Fitur Sistem
                </span>
                <h2 class="section-title">Fitur <span class="font-accent">Unggulan</span></h2>
                <p class="section-subtitle">Sistem inventaris <span class="font-accent">modern</span> dengan berbagai fitur canggih untuk <span class="font-accent text-highlight">kemudahan pengelolaan</span></p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon purple">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h4>Manajemen <span class="font-accent">Stok</span></h4>
                        <p>Kelola stok ATK dengan <span class="font-accent">mudah.</span> Catat barang masuk dan keluar secara real-time dengan sistem yang terintegrasi.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4>Laporan & <span class="font-accent">Analitik</span></h4>
                        <p>Dapatkan laporan lengkap dan <span class="font-accent">analisis penggunaan</span> ATK dalam berbagai format yang mudah dipahami.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h4><span class="font-accent">Notifikasi</span> Stok</h4>
                        <p>Terima <span class="font-accent">peringatan otomatis</span> ketika stok barang mencapai batas minimum untuk antisipasi kebutuhan.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h4 class="font-accent-normal">Permintaan ATK</h4>
                        <p>Ajukan permintaan ATK dengan sistem <span class="font-accent">tracking</span> yang terorganisir dan mudah dilacak statusnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Peminjaman Section -->
    <section class="peminjaman-section" id="peminjaman">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="section-title">Form <span class="font-accent">Permintaan</span> ATK</h2>
                <p class="section-subtitle">Ajukan permintaan ATK dan Barang Habis Pakai dengan <span class="font-accent">mudah</span></p>
            </div>

            <div class="row g-4">
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="peminjaman-card">
                        <form class="peminjaman-form" action="<?= base_url('loans/store') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_peminjam" class="form-label">
                                        <i class="bi bi-person me-1"></i>Nama Pemohon
                                    </label>
                                    <input type="text" class="form-control" id="nama_peminjam" name="borrower_name" placeholder="Masukkan nama lengkap" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="unit_kerja" class="form-label">
                                        <i class="bi bi-building me-1"></i>Unit Kerja / Prodi
                                    </label>
                                    <select class="form-select" id="unit_kerja" name="department" required>
                                        <option value="">Pilih Unit Kerja</option>
                                        <option value="Sistem Informasi">Sistem Informasi</option>
                                        <option value="Informatika">Informatika</option>
                                        <option value="TU Fakultas">TU Fakultas</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_telepon" class="form-label">
                                        <i class="bi bi-phone me-1"></i>No. Telepon / WA
                                    </label>
                                    <input type="tel" class="form-control" id="no_telepon" name="phone" placeholder="08xxxxxxxxxx" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@fasilkom.ac.id">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="kategori_barang" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Kategori Barang
                                </label>
                                <select class="form-select" id="kategori_barang" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="ATK">Alat Tulis Kantor (ATK)</option>
                                    <option value="Habis Pakai">Barang Habis Pakai</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="barang_dipinjam" class="form-label">
                                    <i class="bi bi-box-seam me-1"></i>Barang yang Diminta
                                </label>
                                <select class="form-select" id="barang_dipinjam" name="product_id" required>
                                    <option value="">Pilih Barang</option>
                                    <optgroup label="Alat Tulis Kantor">
                                        <option value="1">Pulpen</option>
                                        <option value="2">Pensil 2B</option>
                                        <option value="3">Penghapus</option>
                                        <option value="4">Penggaris 30cm</option>
                                        <option value="5">Stapler</option>
                                        <option value="6">Gunting</option>
                                        <option value="7">Lem Kertas</option>
                                        <option value="8">Spidol Whiteboard</option>
                                    </optgroup>
                                    <optgroup label="Barang Habis Pakai">
                                        <option value="9">Kertas HVS A4 (Rim)</option>
                                        <option value="10">Kertas F4 (Rim)</option>
                                        <option value="11">Tinta Printer</option>
                                        <option value="12">Amplop Coklat</option>
                                        <option value="13">Map Plastik</option>
                                        <option value="14">Buku Tulis</option>
                                        <option value="15">Isi Stapler</option>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah" class="form-label">
                                        <i class="bi bi-123 me-1"></i>Jumlah
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="number" class="form-control" id="jumlah" name="quantity" min="1" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pinjam" class="form-label">
                                        <i class="bi bi-calendar me-1"></i>Tanggal Permintaan
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_pinjam" name="borrow_date" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="keperluan" class="form-label">
                                    <i class="bi bi-card-text me-1"></i>Keperluan / Keterangan
                                </label>
                                <textarea class="form-control" id="keperluan" name="purpose" rows="3" placeholder="Jelaskan keperluan permintaan barang..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-submit w-100">
                                <i class="bi bi-send me-2"></i>Ajukan Permintaan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5" data-aos="fade-left">
                    <div class="peminjaman-info">
                        <h4><i class="bi bi-info-circle me-2"></i>Informasi Permintaan</h4>

                        <div class="peminjaman-info-item">
                            <i class="bi bi-check-circle"></i>
                            <div>
                                <h6>Barang yang Tersedia</h6>
                                <p>Permintaan terbatas untuk ATK dan Barang Habis Pakai yang tersedia di inventaris fakultas.</p>
                            </div>
                        </div>

                        <div class="peminjaman-info-item">
                            <i class="bi bi-clock-history"></i>
                            <div>
                                <h6>Proses Persetujuan</h6>
                                <p>Permintaan akan diproses dalam 1x24 jam kerja setelah pengajuan.</p>
                            </div>
                        </div>

                        <div class="peminjaman-info-item">
                            <i class="bi bi-truck"></i>
                            <div>
                                <h6>Distribusi Barang</h6>
                                <p>Setelah disetujui, barang akan didistribusikan ke unit kerja pemohon.</p>
                            </div>
                        </div>

                        <div class="peminjaman-info-item">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <h6>Butuh Bantuan?</h6>
                                <p>Hubungi TU Fakultas di ext. 1234 atau WhatsApp 08xx-xxxx-xxxx</p>
                            </div>
                        </div>

                        <hr style="opacity: 0.2;">

                        <h6 class="mb-3"><i class="bi bi-tags me-2"></i>Kategori Barang Tersedia:</h6>
                        <div class="barang-kategori">
                            <span class="barang-badge"><i class="bi bi-pencil me-1"></i>ATK</span>
                            <span class="barang-badge"><i class="bi bi-file-earmark me-1"></i>Kertas</span>
                            <span class="barang-badge"><i class="bi bi-droplet me-1"></i>Tinta</span>
                            <span class="barang-badge"><i class="bi bi-folder me-1"></i>Map & Amplop</span>
                            <span class="barang-badge"><i class="bi bi-journal me-1"></i>Buku</span>
                            <span class="barang-badge"><i class="bi bi-paperclip me-1"></i>Perlengkapan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="about-image-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400" style="max-width: 100%; height: auto;">
                            <defs>
                                <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3B5BDB" />
                                    <stop offset="100%" style="stop-color:#5C7CFA" />
                                </linearGradient>
                            </defs>

                            <!-- Background -->
                            <rect x="50" y="50" width="400" height="300" rx="20" fill="url(#bgGrad)" opacity="0.1" />

                            <!-- Computer Monitor -->
                            <rect x="150" y="80" width="200" height="150" rx="10" fill="#1a1c23" />
                            <rect x="160" y="90" width="180" height="120" rx="5" fill="#3B5BDB" />

                            <!-- Screen Content -->
                            <rect x="170" y="100" width="160" height="20" rx="3" fill="rgba(255,255,255,0.3)" />
                            <rect x="170" y="130" width="70" height="70" rx="5" fill="rgba(255,255,255,0.4)" />
                            <rect x="250" y="130" width="80" height="30" rx="3" fill="rgba(255,255,255,0.3)" />
                            <rect x="250" y="170" width="80" height="30" rx="3" fill="rgba(255,255,255,0.3)" />

                            <!-- Monitor Stand -->
                            <rect x="225" y="230" width="50" height="20" rx="3" fill="#1a1c23" />
                            <rect x="200" y="250" width="100" height="10" rx="5" fill="#1a1c23" />

                            <!-- Keyboard -->
                            <rect x="160" y="280" width="180" height="40" rx="5" fill="#2d2d2d" />
                            <rect x="170" y="290" width="160" height="20" rx="3" fill="#3d3d3d" />

                            <!-- Mouse -->
                            <ellipse cx="380" cy="300" rx="20" ry="25" fill="#2d2d2d" />

                            <!-- Office Supplies -->
                            <rect x="70" y="180" width="50" height="80" rx="5" fill="#5C7CFA" opacity="0.8" />
                            <rect x="380" y="150" width="60" height="100" rx="5" fill="#748FFC" opacity="0.8" />

                            <!-- Floating Icons -->
                            <circle cx="100" cy="120" r="25" fill="#3B5BDB" opacity="0.3" />
                            <circle cx="400" cy="100" r="20" fill="#5C7CFA" opacity="0.3" />

                            <!-- Chart bars in monitor -->
                            <rect x="180" y="170" width="10" height="25" rx="2" fill="rgba(255,255,255,0.8)" />
                            <rect x="195" y="160" width="10" height="35" rx="2" fill="rgba(255,255,255,0.8)" />
                            <rect x="210" y="150" width="10" height="45" rx="2" fill="rgba(255,255,255,0.8)" />
                        </svg>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="about-content">
                        <h2>Tentang <span class="font-accent">SIMATIK</span></h2>
                        <p>
                            <strong>SIMATIK</strong> (<span class="font-accent">Sistem Informasi Manajemen Alat Tulis Kantor</span>) adalah solusi digital
                            untuk pengelolaan inventaris alat tulis kantor di <span class="font-accent">Fakultas Ilmu Komputer.</span>
                        </p>
                        <p>
                            Sistem ini dirancang untuk memudahkan pengelolaan stok barang, pencatatan
                            transaksi, permintaan ATK, dan pembuatan laporan secara <span class="font-accent">efisien</span> dan <span class="font-accent">terintegrasi.</span>
                        </p>
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

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="500">0</span>
                        <span class="stat-label">Jenis Barang</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="1000">0</span>
                        <span class="stat-label">Transaksi/Bulan</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="50">0</span>
                        <span class="stat-label">Kategori</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="24">0</span>
                        <span class="stat-label">Jam Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
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
                                    <div class="contact-icon">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6>Alamat</h6>
                                        <p>Fakultas Ilmu Komputer<br>Universitas XYZ<br>Jl. Pendidikan No. 123</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="bi bi-telephone"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6>Telepon</h6>
                                        <p>(021) 123-4567<br>Ext. 1234</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6>Email</h6>
                                        <p>inventaris@fasilkom.ac.id<br>support@fasilkom.ac.id</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6>Jam Operasional</h6>
                                        <p>Senin - Jumat: 08:00 - 16:00<br>Sabtu: 08:00 - 12:00</p>
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
                    <div class="footer-brand">
                        <i class="bi bi-box-seam-fill me-2"></i>SIMA<span class="font-accent" style="color: rgba(255,255,255,0.9);">TIK</span>
                    </div>
                    <p class="footer-text">
                        Sistem Informasi Manajemen Alat Tulis Kantor untuk <span class="font-accent">Fakultas Ilmu Komputer.</span>
                        Membantu pengelolaan inventaris dengan lebih <span class="font-accent">efisien</span> dan terorganisir.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="footer-title">Menu</h5>
                    <ul class="footer-links">
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#about">Tentang</a></li>
                        <li><a href="#contact">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="footer-title">Layanan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Manajemen Stok</a></li>
                        <li><a href="#">Laporan</a></li>
                        <li><a href="#">Permintaan ATK</a></li>
                        <li><a href="#">Dukungan</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="footer-title">Fakultas Ilmu Komputer</h5>
                    <ul class="footer-links">
                        <li><a href="#">Website Fakultas</a></li>
                        <li><a href="#">Portal Akademik</a></li>
                        <li><a href="#">E-Learning</a></li>
                        <li><a href="#">Perpustakaan</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> SIMATIK - Fakultas Ilmu Komputer. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="bi bi-box-seam-fill me-2" style="color: var(--primary-color);"></i>Masuk ke <span class="font-accent">SIMATIK</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('dashboard') ?>" method="get" id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>
                            <a href="#" class="text-decoration-none" style="color: var(--primary-color); font-size: 0.9rem;">Lupa password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login-submit">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </button>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                            Belum punya akun? <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Hubungi Admin</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512" fill="white">
            <path d="M256 16C234.1 16 192 96 192 192h128C320 96 277.9 16 256 16z" />
            <path d="M320 192c0 64-16 128-16 192H208c0-64-16-128-16-192H320z" />
            <path d="M208 384l-16 96h32l32-48 32 48h32l-16-96H208z" />
            <path d="M176 256c-48 32-80 96-80 96l48 16s16-48 48-80l-16-32z" />
            <path d="M336 256c48 32 80 96 80 96l-48 16s-16-48-48-80l16-32z" />
            <circle cx="256" cy="160" r="32" fill="rgba(59,91,219,0.9)" />
        </svg>
    </button>

    <!-- JavaScript -->
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            once: true,
            offset: 100
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const navbarHeight = document.getElementById('mainNavbar').offsetHeight;
                    const targetPosition = target.offsetTop - navbarHeight;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Counter animation
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;

        const animateCounter = (counter) => {
            const target = +counter.getAttribute('data-count');
            const count = +counter.innerText;
            const increment = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(() => animateCounter(counter), 1);
            } else {
                counter.innerText = target + '+';
            }
        };

        // Intersection Observer for counter animation
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    counters.forEach(counter => {
                        animateCounter(counter);
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Set minimum date for peminjaman to today
        const tanggalPinjam = document.getElementById('tanggal_pinjam');
        if (tanggalPinjam) {
            const today = new Date().toISOString().split('T')[0];
            tanggalPinjam.setAttribute('min', today);
            tanggalPinjam.value = today;
        }

        // Form peminjaman validation
        const peminjamanForm = document.querySelector('.peminjaman-form');
        if (peminjamanForm) {
            peminjamanForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form values
                const nama = document.getElementById('nama_peminjam').value.trim();
                const unit = document.getElementById('unit_kerja').value;
                const telp = document.getElementById('no_telepon').value.trim();
                const barang = document.getElementById('barang_dipinjam').value;
                const jumlah = document.getElementById('jumlah').value;

                // Basic validation
                if (!nama || !unit || !telp || !barang || !jumlah) {
                    alert('Mohon lengkapi semua field yang wajib diisi!');
                    return;
                }

                // Phone validation
                const phoneRegex = /^(08|\+62)[0-9]{8,12}$/;
                if (!phoneRegex.test(telp.replace(/\s/g, ''))) {
                    alert('Format nomor telepon tidak valid!');
                    return;
                }

                // Quantity validation
                if (parseInt(jumlah) < 1) {
                    alert('Jumlah minimal adalah 1!');
                    return;
                }

                // Show success message (in real app, this would submit to server)
                alert('Permintaan ATK berhasil diajukan!\n\nNama: ' + nama + '\nPermintaan akan diproses dalam 1x24 jam kerja.');

                // Reset form
                peminjamanForm.reset();
                tanggalPinjam.value = today;
            });
        }

        // Filter barang berdasarkan kategori
        const kategoriSelect = document.getElementById('kategori_barang');
        const barangSelect = document.getElementById('barang_dipinjam');

        if (kategoriSelect && barangSelect) {
            kategoriSelect.addEventListener('change', function() {
                const kategori = this.value;
                const optgroups = barangSelect.querySelectorAll('optgroup');

                // Reset selection
                barangSelect.value = '';

                optgroups.forEach(group => {
                    if (kategori === '') {
                        group.style.display = '';
                    } else if (kategori === 'ATK' && group.label === 'Alat Tulis Kantor') {
                        group.style.display = '';
                    } else if (kategori === 'Habis Pakai' && group.label === 'Barang Habis Pakai') {
                        group.style.display = '';
                    } else {
                        group.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>

</html>