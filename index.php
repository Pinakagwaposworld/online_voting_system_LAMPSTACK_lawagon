<?php
session_start();
include 'db.php';

if(isset($_POST['student_id'])){
    $student_id = $_POST['student_id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id=? AND has_voted=0");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $_SESSION['student_id'] = $student_id;
        header("Location: vote.php");
    } else {
        $error = "Invalid ID or already voted!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Voting Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            /* =============================================
               BACKGROUND IMAGE — replace 'bg.jpg' with
               your uploaded image filename
            ============================================= */
            background-image: url('logo.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Dark overlay on top of background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 45px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .logo-icon {
            font-size: 52px;
            margin-bottom: 10px;
            display: block;
        }

        .login-card h1 {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }

        .login-card p.subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            margin-bottom: 32px;
        }

        .input-group {
            position: relative;
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group label {
            display: block;
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .input-group input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 10px;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: border 0.25s, background 0.25s;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .input-group input:focus {
            border-color: #5dade2;
            background: rgba(255, 255, 255, 0.18);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #2e86c1, #1abc9c);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid rgba(231, 76, 60, 0.5);
            color: #f1948a;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 16px;
            text-align: left;
        }

        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.12);
            margin: 28px 0 20px;
        }

        .footer-note {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <span class="logo-icon">🗳️</span>
        <h1>Student Voting Portal</h1>
        <p class="subtitle">Enter your Student ID to cast your vote</p>

        <form method="POST">
            <div class="input-group">
                <label for="student_id">Student ID</label>
                <input 
                    type="text" 
                    id="student_id"
                    name="student_id" 
                    placeholder="e.g. 2024-00123" 
                    required
                    autocomplete="off">
            </div>

            <button type="submit" class="btn-login">Proceed to Vote →</button>
        </form>

        <?php if(isset($error)): ?>
            <div class="error-msg">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <hr class="divider">
        <p class="footer-note">Each student may only vote once. Contact your administrator if you have issues.</p>

    </div>
</div>

</body>
</html>