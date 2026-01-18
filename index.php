<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand fw-800 fs-3 text-primary" href="#">
                <i class="fas fa-layer-group me-2"></i>EduFlow
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-dark" href="#features">About</a></li>
                    <li class="nav-item ms-lg-4">
                        <a class="btn btn-pro" href="views/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1 class="fw-800 mb-4">Manage your <span class="text-primary">College</span> with intelligence.</h1>
                    <p class="lead text-muted mb-5">A unified platform for students and administrators to track academic growth, attendance, and digital identity in one workspace.</p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-pro shadow">Get Started Free</button>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left">
                    <img src="https://illustrations.popsy.co/gray/managing-projects.svg" class="img-fluid float-element" alt="Hero Illustration">
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="pro-card p-5">
                        <div class="icon-circle"><i class="fas fa-bolt"></i></div>
                        <h4 class="fw-bold">Real-time Analytics</h4>
                        <p class="text-muted">Instant insights into student performance using our pre-populated 200+ data sets.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="pro-card p-5">
                        <div class="icon-circle"><i class="fas fa-shield-halved"></i></div>
                        <h4 class="fw-bold">Secure Access</h4>
                        <p class="text-muted">Simplified direct-verification system for individual and institutional security.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="pro-card p-5">
                        <div class="icon-circle"><i class="fas fa-fingerprint"></i></div>
                        <h4 class="fw-bold">Digital Identity</h4>
                        <p class="text-muted">Every student gets a professional digital profile and unique identification.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000 });
        
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>