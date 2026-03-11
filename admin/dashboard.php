<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #0f1117;
            --surface:   #181c27;
            --border:    #252a38;
            --gold:      #c9a84c;
            --gold-dim:  #8a6e2f;
            --text:      #e8eaf0;
            --muted:     #6b7280;
            --danger:    #e05252;
            --green:     #4caf84;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            min-height: 100vh;
        }

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

        .page {
            position: relative;
            z-index: 1;
            max-width: 960px;
            margin: 0 auto;
            padding: 48px 24px 80px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 52px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .brand-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--text);
            line-height: 1.1;
        }

        .brand-text p {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .logout-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.2s, color 0.2s;
        }

        .logout-btn:hover {
            border-color: var(--danger);
            color: var(--danger);
        }

        .section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 24px;
            text-decoration: none;
            color: var(--text);
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
        }

        .card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(201,168,76,0.06), transparent 60%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: var(--gold-dim);
            box-shadow: 0 12px 32px rgba(0,0,0,0.35);
        }

        .card:hover::after { opacity: 1; }

        .card-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .icon-blue   { background: rgba(59,130,246,0.15); }
        .icon-green  { background: rgba(76,175,132,0.15); }
        .icon-gold   { background: rgba(201,168,76,0.15); }
        .icon-purple { background: rgba(139,92,246,0.15); }

        .card-body h3 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .card-body p {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        .card-arrow {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--border);
            font-size: 18px;
            transition: color 0.2s, right 0.2s;
        }

        .card:hover .card-arrow {
            color: var(--gold);
            right: 16px;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 40px 0;
        }

        .status-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 0 3px rgba(76,175,132,0.2);
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(76,175,132,0.2); }
            50%       { box-shadow: 0 0 0 6px rgba(76,175,132,0.08); }
        }

        .status-bar strong { color: var(--green); }

        @media (max-width: 480px) {
            .topbar { flex-direction: column; align-items: flex-start; gap: 16px; }
            .cards  { grid-template-columns: 1fr; }
        }
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
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </div>

    <p class="section-label">Quick Actions</p>
    <div class="cards">

        <a href="add_student.php" class="card">
            <div class="card-icon icon-blue">👤</div>
            <div class="card-body">
                <h3>Add Student</h3>
                <p>Register a new student ID for voting access.</p>
            </div>
            <span class="card-arrow">›</span>
        </a>

        <a href="view_students.php" class="card">
            <div class="card-icon icon-blue">🗂️</div>
            <div class="card-body">
                <h3>View Students</h3>
                <p>Browse and remove registered student IDs.</p>
            </div>
            <span class="card-arrow">›</span>
        </a>

        <a href="add_candidate.php" class="card">
            <div class="card-icon icon-green">🎖️</div>
            <div class="card-body">
                <h3>Add Candidate</h3>
                <p>Add a new candidate and assign their position.</p>
            </div>
            <span class="card-arrow">›</span>
        </a>

        <a href="view_candidates.php" class="card">
            <div class="card-icon icon-gold">📋</div>
            <div class="card-body">
                <h3>View Candidates</h3>
                <p>Browse, manage, or remove existing candidates.</p>
            </div>
            <span class="card-arrow">›</span>
        </a>

        <a href="results.php" class="card">
            <div class="card-icon icon-purple">📊</div>
            <div class="card-body">
                <h3>View Results</h3>
                <p>See live vote counts ordered by position.</p>
            </div>
            <span class="card-arrow">›</span>
        </a>

    </div>

    <hr class="divider">

    <div class="status-bar">
        <div class="status-dot"></div>
        <span>System is <strong>online</strong> — Voting is currently active.</span>
    </div>

</div>
</body>
</html>