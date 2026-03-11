<?php
session_start();
include 'db.php';
if(!isset($_SESSION['student_id'])){
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM candidates ORDER BY position, name ASC");

// Group candidates by position
$positions = [];
while($row = $result->fetch_assoc()){
    $positions[$row['position']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Now — Voting System</title>
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
            --blue:     #3b82f6;
            --blue-dim: #1e3a5f;
            --green:    #4caf84;
            --input-bg: #12151f;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            min-height: 100vh;
            padding: 40px 20px 80px;
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

        .page {
            position: relative;
            z-index: 1;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .top-badge {
            display: inline-flex;
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
            margin-bottom: 18px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 14px;
            color: var(--muted);
        }

        .student-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.25);
            border-radius: 99px;
            padding: 5px 14px;
            font-size: 12px;
            color: #7db4f7;
            margin-top: 12px;
        }

        /* Position section */
        .position-section {
            margin-bottom: 32px;
        }

        .position-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .position-label span {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold);
        }

        .position-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Candidate checkbox */
        .candidate-checkbox { display: none; }

        .candidate-item { margin-bottom: 10px; }

        .candidate-btn {
            display: flex;
            align-items: center;
            gap: 16px;
            width: 100%;
            padding: 16px 20px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s, transform 0.15s;
        }

        .candidate-btn:hover {
            border-color: #3a4058;
            transform: translateX(3px);
        }

        .candidate-checkbox:checked + .candidate-btn {
            border-color: var(--blue);
            background: rgba(59,130,246,0.07);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .check-circle {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.2s, background 0.2s;
        }

        .candidate-checkbox:checked + .candidate-btn .check-circle {
            border-color: var(--blue);
            background: var(--blue);
        }

        .check-circle::after {
            content: '✓';
            font-size: 12px;
            color: #fff;
            opacity: 0;
            transition: opacity 0.15s;
        }

        .candidate-checkbox:checked + .candidate-btn .check-circle::after {
            opacity: 1;
        }

        .candidate-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .candidate-info strong {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
        }

        .candidate-info small {
            font-size: 12px;
            color: var(--muted);
        }

        /* Submit */
        .submit-wrap {
            position: sticky;
            bottom: 24px;
            margin-top: 16px;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border: none;
            border-radius: 14px;
            color: #0f1117;
            font-size: 16px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 24px rgba(201,168,76,0.25);
            transition: opacity 0.2s, transform 0.15s;
        }

        .submit-btn:hover {
            opacity: 0.92;
            transform: translateY(-2px);
        }

        .submit-btn:active { transform: translateY(0); }

        .submit-note {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="page">

    <!-- Header -->
    <div class="page-header">
        <div class="top-badge">🗳️ Official Ballot</div>
        <h1>Cast Your Vote</h1>
        <p>Select one candidate per position, then submit your ballot.</p>
        <div class="student-chip">
            👤 Student ID: <?php echo htmlspecialchars($_SESSION['student_id']); ?>
        </div>
    </div>

    <form method="POST" action="submit_vote.php">

        <?php foreach($positions as $position => $candidates): ?>
        <div class="position-section">
            <div class="position-label">
                <span><?php echo htmlspecialchars($position); ?></span>
            </div>

            <?php foreach($candidates as $row): ?>
            <div class="candidate-item">
                <input
                    type="checkbox"
                    name="candidate_ids[]"
                    value="<?php echo $row['id']; ?>"
                    id="cand_<?php echo $row['id']; ?>"
                    class="candidate-checkbox">

                <label for="cand_<?php echo $row['id']; ?>" class="candidate-btn">
                    <div class="check-circle"></div>
                    <div class="candidate-avatar">🧑</div>
                    <div class="candidate-info">
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                        <small><?php echo htmlspecialchars($row['position']); ?></small>
                    </div>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <div class="submit-wrap">
            <button type="submit" class="submit-btn">🗳️ Cast All Votes</button>
            <p class="submit-note">⚠️ You can only vote once. This action cannot be undone.</p>
        </div>

    </form>

</div>

</body>
</html>