<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])) header("Location: login.php");

$msg = '';
$msgType = '';

if(isset($_POST['student_id'])){
    $student_id = trim($_POST['student_id']);
    $stmt = $conn->prepare("INSERT INTO students(student_id) VALUES(?)");
    $stmt->bind_param("s", $student_id);
    if($stmt->execute()){
        $msg = "Student <strong>" . htmlspecialchars($student_id) . "</strong> added successfully!";
        $msgType = 'success';
    } else {
        $msg = "Error: Student ID <strong>" . htmlspecialchars($student_id) . "</strong> might already exist.";
        $msgType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student — Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#0f1117;--surface:#181c27;--border:#252a38;
            --gold:#c9a84c;--gold-dim:#8a6e2f;--text:#e8eaf0;
            --muted:#6b7280;--danger:#e05252;--green:#4caf84;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:var(--bg);font-family:'DM Sans',sans-serif;color:var(--text);min-height:100vh;}
        body::before{content:'';position:fixed;inset:0;
            background-image:linear-gradient(rgba(201,168,76,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,.03) 1px,transparent 1px);
            background-size:40px 40px;pointer-events:none;z-index:0;}
        .page{position:relative;z-index:1;max-width:960px;margin:0 auto;padding:48px 24px 80px;}
        .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:52px;}
        .brand{display:flex;align-items:center;gap:14px;}
        .brand-icon{width:44px;height:44px;background:linear-gradient(135deg,var(--gold),var(--gold-dim));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;}
        .brand-text h1{font-family:'Playfair Display',serif;font-size:20px;color:var(--text);line-height:1.1;}
        .brand-text p{font-size:12px;color:var(--muted);margin-top:2px;}
        .back-btn{background:transparent;border:1px solid var(--border);color:var(--muted);padding:8px 16px;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:border-color .2s,color .2s;}
        .back-btn:hover{border-color:var(--gold-dim);color:var(--gold);}
        .section-label{font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:6px;}
        .page-title{font-family:'Playfair Display',serif;font-size:28px;margin-bottom:32px;}
        .form-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:36px 32px;max-width:480px;}
        label{display:block;font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;}
        .input-wrap{position:relative;margin-bottom:20px;}
        .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:15px;pointer-events:none;}
        input[type="text"]{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:12px 14px 12px 42px;font-size:14px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;transition:border-color .2s,box-shadow .2s;}
        input[type="text"]::placeholder{color:var(--muted);}
        input[type="text"]:focus{border-color:var(--gold-dim);box-shadow:0 0 0 3px rgba(201,168,76,.1);}
        .submit-btn{width:100%;background:linear-gradient(135deg,var(--gold),var(--gold-dim));border:none;color:#0f1117;padding:13px;border-radius:10px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .2s,transform .1s;}
        .submit-btn:hover{opacity:.9;}
        .submit-btn:active{transform:scale(.99);}
        .alert{display:flex;align-items:flex-start;gap:10px;border-radius:10px;padding:14px 16px;font-size:13px;line-height:1.5;margin-bottom:24px;animation:fadeIn .3s ease;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:none;}}
        .alert-success{background:rgba(76,175,132,.1);border:1px solid rgba(76,175,132,.3);color:var(--green);}
        .alert-error{background:rgba(224,82,82,.08);border:1px solid rgba(224,82,82,.25);color:var(--danger);}
        .divider{border:none;border-top:1px solid var(--border);margin:24px 0;}
        .view-link{display:inline-flex;align-items:center;gap:6px;color:var(--gold);font-size:13px;text-decoration:none;transition:opacity .2s;}
        .view-link:hover{opacity:.75;}
        @media(max-width:480px){.topbar{flex-direction:column;align-items:flex-start;gap:16px;}.form-card{padding:24px 20px;}}
    </style>
</head>
<body>
<div class="page">
    <div class="topbar">
        <div class="brand">
            <div class="brand-icon">🏫</div>
            <div class="brand-text">
                <h1>Voting System</h1>
                <p>Administrator Panel</p>
            </div>
        </div>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <p class="section-label">Registry</p>
    <h2 class="page-title">Add Student</h2>

    <div class="form-card">
        <?php if($msg): ?>
        <div class="alert alert-<?= $msgType ?>">
            <?= $msgType === 'success' ? '✓' : '✕' ?> &nbsp;<?= $msg ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <label for="student_id">Student ID</label>
            <div class="input-wrap">
                <span class="input-icon">👤</span>
                <input type="text" id="student_id" name="student_id"
                       placeholder="e.g. 2024-00123" required
                       value="<?= isset($_POST['student_id']) && $msgType==='error' ? htmlspecialchars($_POST['student_id']) : '' ?>">
            </div>
            <button type="submit" class="submit-btn">Add Student</button>
        </form>

        <hr class="divider">
        <a href="view_students.php" class="view-link">🗂️ View all registered students →</a>
    </div>
</div>
</body>
</html>