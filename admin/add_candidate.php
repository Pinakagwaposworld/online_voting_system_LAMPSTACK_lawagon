<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])) header("Location: login.php");

$msg = '';
$msg_type = '';

if(isset($_POST['name'], $_POST['position'])){
    $name = $_POST['name'];
    $position = $_POST['position'];
    $stmt = $conn->prepare("INSERT INTO candidates(name, position) VALUES(?, ?)");
    $stmt->bind_param("ss", $name, $position);
    if($stmt->execute()){
        $msg = "Candidate added successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error adding candidate. Please try again.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate — Voting System</title>
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
            --green:    #4caf84;
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

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 24px;
            transition: color 0.2s;
        }

        .back-link:hover { color: var(--gold); }
        .back-link span { font-size: 16px; }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .card-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--text);
            line-height: 1.2;
        }

        .card-header p {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .form-group input::placeholder { color: #3a4058; }

        .form-group input:focus {
            border-color: var(--gold-dim);
            background: #14172200;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.08);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border: none;
            border-radius: 10px;
            color: #0f1117;
            font-size: 15px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .submit-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .submit-btn:active { transform: translateY(0); }

        /* Alert */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 20px;
        }

        .alert-success {
            background: rgba(76,175,132,0.1);
            border: 1px solid rgba(76,175,132,0.3);
            color: #4caf84;
        }

        .alert-error {
            background: rgba(224,82,82,0.1);
            border: 1px solid rgba(224,82,82,0.3);
            color: var(--danger);
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 28px 0 20px;
        }

        .footer-note {
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="wrapper">

    <a href="dashboard.php" class="back-link">
        <span>←</span> Back to Dashboard
    </a>

    <div class="card">
        <div class="card-header">
            <div class="card-icon">🎖️</div>
            <div>
                <h2>Add Candidate</h2>
                <p>Fill in the details to register a new candidate</p>
            </div>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="name">Candidate Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="e.g. Juan Dela Cruz"
                    required
                    autocomplete="off">
            </div>

            <div class="form-group">
                <label for="position">Position</label>
                <input
                    type="text"
                    id="position"
                    name="position"
                    placeholder="e.g. President, Vice President"
                    required
                    autocomplete="off">
            </div>

            <button type="submit" class="submit-btn">Add Candidate</button>
        </form>

        <?php if($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?>">
                <?php echo $msg_type === 'success' ? '✅' : '⚠️'; ?>
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <hr class="divider">
        <p class="footer-note">Candidates will appear on the voting ballot immediately after being added.</p>
    </div>

</div>

</body>
</html>