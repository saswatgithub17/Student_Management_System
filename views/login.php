<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: radial-gradient(circle at center, #e0e7ff, #ffffff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card p-5 shadow-lg mx-auto" data-aos="zoom-in">
        <div class="text-center mb-4">
            <h3 class="fw-800 text-primary">Welcome Back</h3>
            <p class="text-muted">Enter credentials to access your portal</p>
        </div>
        
        <form action="../modules/auth_logic.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control rounded-3" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control rounded-3" required>
            </div>
            <button type="submit" name="login_btn" class="btn btn-pro w-100 mb-3">Login to Dashboard</button>
            <div class="text-center">
                <a href="../index.php" class="text-decoration-none small text-muted">← Back to Home</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>