<?php
session_start();
if(isset($_POST['username'], $_POST['password'])){
    if($_POST['username']=='admin' && $_POST['password']=='admin123'){
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:       #0f1117;
            --surface:  #181c27;
            --border:   #252a38;
            --gold:     #c9a84c;
            --gold-dim: #8a6e2f;
            --text:     #e8eaf0;
            --muted:    #6b7280;
            --danger:   #e05252;
            --input-bg: #12151f;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Grid texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(201,168,76,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,168,76,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* Glow blob behind card */
        body::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(201,168,76,0.07) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        /* Top badge */
        .top-badge {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
        }

        .badge-inner {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(201,168,76,0.08);
            border: 1px solid rgba(201,168,76,0.2);
            border-radius: 99px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 42px 38px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
        }

        .card-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .card-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(201,168,76,0.2);
        }

        .card-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--text);
            margin-bottom: 6px;
        }

        .card-header p {
            font-size: 13px;
            color: var(--muted);
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            pointer-events: none;
            opacity: 0.4;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px 13px 42px;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input::placeholder { color: #3a4058; }

        .form-group input:focus {
            border-color: var(--gold-dim);
            box-shadow: 0 0 0 3px rgba(201,168,76,0.08);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border: none;
            border-radius: 10px;
            color: #0f1117;
            font-size: 15px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: 0.4px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .submit-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .submit-btn:active { transform: translateY(0); }

        /* Error */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(224,82,82,0.1);
            border: 1px solid rgba(224,82,82,0.3);
            color: var(--danger);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 18px;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 28px 0 18px;
        }

        .footer-note {
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }

        .footer-note a {
            color: var(--gold);
            text-decoration: none;
        }

        .footer-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="top-badge">
        <div class="badge-inner">🏫 School Voting System</div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-icon">🔐</div>
            <h2>Admin Login</h2>
            <p>Sign in to access the administrator panel</p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Admin username"
                        required
                        autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔑</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required>
                </div>
            </div>

            <button type="submit" class="submit-btn">Sign In to Dashboard →</button>
        </form>

        <?php if(isset($error)): ?>
            <div class="alert-error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <hr class="divider">
        <p class="footer-note">
            Not an admin? <a href="../index.php">Go to Student Voting</a>
        </p>
    </div>

</div>

</body>
</html>