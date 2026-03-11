<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])) header("Location: login.php");

$result = $conn->query("SELECT name, position, votes FROM candidates ORDER BY position, votes DESC");
$candidates = [];
$maxVotes = 0;
while($row = $result->fetch_assoc()) {
    $candidates[] = $row;
    if($row['votes'] > $maxVotes) $maxVotes = $row['votes'];
}

// Group by position
$grouped = [];
foreach($candidates as $c) {
    $grouped[$c['position']][] = $c;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results — Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0b0f1a;
            --surface:  #111827;
            --card:     #161e2e;
            --border:   #1f2d45;
            --gold:     #c9a84c;
            --gold-dim: #7a6228;
            --silver:   #8ba3c7;
            --text:     #e8edf5;
            --muted:    #5a6f8a;
            --win:      #c9a84c;
            --bar-bg:   #1a2540;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 0 0 60px;
            overflow-x: hidden;
        }

        /* ── HEADER ── */
        header {
            background: linear-gradient(135deg, #0d1320 0%, #0b1628 60%, #0f1a2e 100%);
            border-bottom: 1px solid var(--border);
            padding: 36px 48px 32px;
            position: relative;
            overflow: hidden;
        }
        header::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(201,168,76,.12) 0%, transparent 70%);
            pointer-events: none;
        }
        header::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .header-inner {
            max-width: 960px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(201,168,76,.1);
            border: 1px solid rgba(201,168,76,.25);
            color: var(--gold);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 999px;
            margin-bottom: 12px;
        }
        .header-badge span { width: 6px; height: 6px; background: var(--gold); border-radius: 50%; animation: pulse 2s infinite; }

        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.4; transform:scale(1.4); }
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 900;
            letter-spacing: -.01em;
            line-height: 1.1;
            color: var(--text);
        }
        h1 em {
            font-style: normal;
            color: var(--gold);
        }

        .header-meta {
            font-size: 13px;
            color: var(--muted);
            margin-top: 6px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--silver);
            font-size: 13px;
            font-weight: 500;
            border: 1px solid var(--border);
            padding: 9px 18px;
            border-radius: 8px;
            transition: all .2s;
            background: rgba(255,255,255,.03);
            white-space: nowrap;
        }
        .back-btn:hover {
            border-color: var(--gold-dim);
            color: var(--gold);
            background: rgba(201,168,76,.07);
        }
        .back-btn svg { width:14px; height:14px; flex-shrink:0; }

        /* ── MAIN ── */
        main {
            max-width: 960px;
            margin: 48px auto 0;
            padding: 0 24px;
        }

        /* ── SUMMARY STRIP ── */
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 48px;
        }
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
            animation: fadeUp .5s both;
        }
        .stat-card:nth-child(2) { animation-delay: .08s; }
        .stat-card:nth-child(3) { animation-delay: .16s; }
        .stat-card::before {
            content:'';
            position:absolute; top:0; left:0; right:0; height:2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity:.5;
        }
        .stat-label { font-size:11px; font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:8px; }
        .stat-value { font-family:'Playfair Display',serif; font-size:32px; font-weight:700; color:var(--gold); line-height:1; }
        .stat-sub   { font-size:12px; color:var(--muted); margin-top:4px; }

        /* ── POSITION GROUP ── */
        .position-group {
            margin-bottom: 44px;
            animation: fadeUp .5s both;
        }

        .position-title {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }
        .position-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }
        .position-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .position-count {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 4px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }

        /* ── CANDIDATE CARD ── */
        .candidate-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 26px;
            margin-bottom: 14px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            align-items: center;
            transition: border-color .2s, transform .2s;
            position: relative;
            overflow: hidden;
        }
        .candidate-card:hover {
            border-color: var(--gold-dim);
            transform: translateY(-2px);
        }

        /* Winner glow */
        .candidate-card.winner {
            border-color: rgba(201,168,76,.35);
            background: linear-gradient(135deg, #1a1e2e 0%, #1c1a0e 100%);
        }
        .candidate-card.winner::before {
            content:'';
            position:absolute; inset:0;
            background: radial-gradient(ellipse at top left, rgba(201,168,76,.06) 0%, transparent 60%);
            pointer-events:none;
        }

        .winner-crown {
            position: absolute;
            top: 14px; right: 14px;
            font-size: 13px;
            opacity: .7;
        }

        .rank {
            position: absolute;
            top: 0; left: 0;
            background: var(--border);
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            padding: 3px 10px 3px 14px;
            border-radius: 0 0 10px 0;
        }
        .candidate-card.winner .rank {
            background: rgba(201,168,76,.2);
            color: var(--gold);
        }

        .candidate-info { padding-top: 6px; }

        .candidate-name {
            font-size: 17px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 10px;
        }
        .candidate-card.winner .candidate-name { color: #f0d98a; }

        /* Vote bar */
        .bar-wrap { position: relative; }
        .bar-track {
            height: 6px;
            background: var(--bar-bg);
            border-radius: 999px;
            overflow: hidden;
        }
        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--gold-dim), var(--gold));
            width: 0;
            transition: width 1s cubic-bezier(.4,0,.2,1);
        }
        .candidate-card.winner .bar-fill {
            background: linear-gradient(90deg, #a8831e, #f0d060, #c9a84c);
            box-shadow: 0 0 10px rgba(201,168,76,.4);
        }
        .bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--muted);
            margin-top: 6px;
        }
        .bar-pct { color: var(--gold); font-weight: 600; }

        /* Vote count bubble */
        .vote-count {
            text-align: right;
            flex-shrink: 0;
        }
        .vote-num {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            font-weight: 700;
            color: var(--silver);
            line-height: 1;
        }
        .candidate-card.winner .vote-num { color: var(--gold); }
        .vote-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-top: 2px;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── FOOTER ── */
        footer {
            max-width: 960px;
            margin: 60px auto 0;
            padding: 24px 24px 0;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        footer span { font-size: 12px; color: var(--muted); }

        @media (max-width: 600px) {
            header { padding: 28px 20px 24px; }
            main   { padding: 0 16px; }
            .candidate-card { grid-template-columns: 1fr; }
            .vote-count { text-align: left; }
        }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <div>
            <div class="header-badge"><span></span> Admin View · Confidential</div>
            <h1>Voting <em>Results</em></h1>
            <p class="header-meta">Live tally · <?php echo count($candidates); ?> candidates across <?php echo count($grouped); ?> position<?php echo count($grouped) !== 1 ? 's' : ''; ?></p>
        </div>
        <a href="dashboard.php" class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to Dashboard
        </a>
    </div>
</header>

<main>

    <!-- Summary strip -->
    <?php
        $totalVotes = array_sum(array_column($candidates, 'votes'));
        $positions  = count($grouped);
        $totalCands = count($candidates);
    ?>
    <div class="summary">
        <div class="stat-card">
            <div class="stat-label">Total Votes Cast</div>
            <div class="stat-value"><?php echo number_format($totalVotes); ?></div>
            <div class="stat-sub">across all positions</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Positions</div>
            <div class="stat-value"><?php echo $positions; ?></div>
            <div class="stat-sub">open seat<?php echo $positions !== 1 ? 's' : ''; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Candidates</div>
            <div class="stat-value"><?php echo $totalCands; ?></div>
            <div class="stat-sub">total running</div>
        </div>
    </div>

    <!-- Per-position groups -->
    <?php foreach($grouped as $position => $cands):
        $posTotal = array_sum(array_column($cands, 'votes'));
    ?>
    <div class="position-group">
        <div class="position-title">
            <h2><?php echo htmlspecialchars($position); ?></h2>
            <div class="position-line"></div>
            <span class="position-count"><?php echo $posTotal; ?> votes · <?php echo count($cands); ?> candidates</span>
        </div>

        <?php foreach($cands as $i => $c):
            $isWinner = ($i === 0);
            $pct = $posTotal > 0 ? round(($c['votes'] / $posTotal) * 100, 1) : 0;
            $barWidth = $maxVotes > 0 ? round(($c['votes'] / $maxVotes) * 100) : 0;
            $rank = $i + 1;
        ?>
        <div class="candidate-card <?php echo $isWinner ? 'winner' : ''; ?>"
             data-bar="<?php echo $barWidth; ?>">
            <span class="rank"><?php echo $rank === 1 ? '1st' : ($rank === 2 ? '2nd' : ($rank === 3 ? '3rd' : $rank.'th')); ?></span>
            <?php if($isWinner): ?><span class="winner-crown">♛</span><?php endif; ?>

            <div class="candidate-info">
                <div class="candidate-name"><?php echo htmlspecialchars($c['name']); ?></div>
                <div class="bar-wrap">
                    <div class="bar-track">
                        <div class="bar-fill" style="width:0%" data-width="<?php echo $barWidth; ?>%"></div>
                    </div>
                    <div class="bar-label">
                        <span><?php echo $c['votes']; ?> of <?php echo $posTotal; ?> votes in position</span>
                        <span class="bar-pct"><?php echo $pct; ?>%</span>
                    </div>
                </div>
            </div>

            <div class="vote-count">
                <div class="vote-num"><?php echo number_format($c['votes']); ?></div>
                <div class="vote-label">votes</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</main>

<footer>
    <span>Voting Results · Admin Panel</span>
    <span><?php echo date('F j, Y · g:i A'); ?></span>
</footer>

<script>
    // Animate bars on load
    document.addEventListener('DOMContentLoaded', () => {
        const bars = document.querySelectorAll('.bar-fill');
        setTimeout(() => {
            bars.forEach((bar, i) => {
                setTimeout(() => {
                    bar.style.width = bar.dataset.width;
                }, i * 80);
            });
        }, 300);
    });
</script>

</body>
</html>