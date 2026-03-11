<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])) header("Location: login.php");

// Handle delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    header("Location: view_students.php?deleted=1");
    exit();
}

// Fetch all students
$result = $conn->query("SELECT id, student_id, has_voted FROM students");
$students = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total = count($students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students — Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #0f1117;
            --surface:   #181c27;
            --surface2:  #1e2333;
            --border:    #252a38;
            --gold:      #c9a84c;
            --gold-dim:  #8a6e2f;
            --text:      #e8eaf0;
            --muted:     #6b7280;
            --danger:    #e05252;
            --danger-bg: rgba(224,82,82,0.08);
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

        /* ── Top bar ── */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 52px;
        }

        .brand { display: flex; align-items: center; gap: 14px; }

        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        .brand-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 20px; color: var(--text); line-height: 1.1;
        }
        .brand-text p { font-size: 12px; color: var(--muted); margin-top: 2px; }

        .back-btn {
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
            display: flex; align-items: center; gap: 6px;
        }
        .back-btn:hover { border-color: var(--gold-dim); color: var(--gold); }

        /* ── Page header ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 28px;
        }

        .section-label {
            font-size: 11px; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase;
            color: var(--gold); margin-bottom: 6px;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px; color: var(--text);
        }

        .badge {
            background: rgba(201,168,76,0.12);
            border: 1px solid var(--gold-dim);
            color: var(--gold);
            font-size: 12px; font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* ── Search bar ── */
        .search-wrap {
            position: relative;
            margin-bottom: 20px;
        }
        .search-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted); font-size: 15px; pointer-events: none;
        }
        .search-input {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px 11px 40px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s;
        }
        .search-input::placeholder { color: var(--muted); }
        .search-input:focus { border-color: var(--gold-dim); }

        /* ── Toast ── */
        .toast {
            display: flex; align-items: center; gap: 10px;
            background: rgba(76,175,132,0.1);
            border: 1px solid rgba(76,175,132,0.3);
            color: var(--green);
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 13px;
            margin-bottom: 24px;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }

        /* ── Table ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--surface2);
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
        }

        th:last-child { text-align: right; }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        tbody tr:last-child { border-bottom: none; }

        tbody tr:hover { background: rgba(201,168,76,0.04); }

        td {
            padding: 16px 20px;
            font-size: 14px;
            vertical-align: middle;
        }

        td:last-child { text-align: right; }

        .student-cell {
            display: flex; align-items: center; gap: 12px;
        }

        .avatar {
            width: 36px; height: 36px;
            background: rgba(201,168,76,0.12);
            border: 1px solid var(--gold-dim);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600; color: var(--gold);
            flex-shrink: 0;
        }

        .student-id-text {
            font-weight: 500;
            font-family: 'DM Sans', monospace;
            letter-spacing: 0.5px;
        }

        .date-text { color: var(--muted); font-size: 13px; }

        /* Delete button */
        .del-btn {
            background: transparent;
            border: 1px solid transparent;
            color: var(--muted);
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 13px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
        }

        .del-btn:hover {
            background: var(--danger-bg);
            border-color: rgba(224,82,82,0.3);
            color: var(--danger);
        }

        /* Empty state */
        .empty {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon { font-size: 40px; margin-bottom: 16px; opacity: 0.4; }
        .empty p { color: var(--muted); font-size: 14px; }

        /* No-results row */
        .no-results td {
            text-align: center;
            padding: 40px;
            color: var(--muted);
            font-size: 14px;
        }

        /* Modal overlay */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px 28px;
            max-width: 380px; width: 100%;
            margin: 24px;
            animation: slideUp 0.2s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: none; }
        }

        .modal-icon {
            width: 48px; height: 48px;
            background: var(--danger-bg);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 20px;
        }

        .modal h3 {
            font-size: 17px; font-weight: 600;
            margin-bottom: 8px;
        }

        .modal p {
            color: var(--muted); font-size: 13px;
            line-height: 1.6; margin-bottom: 24px;
        }

        .modal p strong { color: var(--text); }

        .modal-actions { display: flex; gap: 10px; }

        .btn-cancel {
            flex: 1;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-cancel:hover { border-color: var(--text); color: var(--text); }

        .btn-delete {
            flex: 1;
            background: var(--danger);
            border: none;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: opacity 0.2s;
        }
        .btn-delete:hover { opacity: 0.88; }

        @media (max-width: 520px) {
            .topbar { flex-direction: column; align-items: flex-start; gap: 16px; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            th.hide-sm, td.hide-sm { display: none; }
        }
    </style>
</head>
<body>
<div class="page">

    <!-- Top bar -->
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

    <!-- Page header -->
    <div class="page-header">
        <div>
            <p class="section-label">Registry</p>
            <h2 class="page-title">Student IDs</h2>
        </div>
        <span class="badge"><?= $total ?> student<?= $total !== 1 ? 's' : '' ?></span>
    </div>

    <?php if(isset($_GET['deleted'])): ?>
    <div class="toast">✓ &nbsp;Student removed successfully.</div>
    <?php endif; ?>

    <!-- Search -->
    <div class="search-wrap">
        <span class="search-icon">🔍</span>
        <input class="search-input" id="searchInput" type="text"
               placeholder="Search by Student ID…" oninput="filterTable()">
    </div>

    <?php if($total === 0): ?>
    <div class="table-wrap">
        <div class="empty">
            <div class="empty-icon">👤</div>
            <p>No students registered yet.<br>
               <a href="add_student.php" style="color:var(--gold);text-decoration:none;">Add the first student →</a>
            </p>
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table id="studentsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th class="hide-sm">Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $i => $s): ?>
                <tr>
                    <td style="color:var(--muted);font-size:13px;"><?= $i + 1 ?></td>
                    <td>
                        <div class="student-cell">
                            <div class="avatar"><?= strtoupper(substr($s['student_id'], 0, 1)) ?></div>
                            <span class="student-id-text"><?= htmlspecialchars($s['student_id']) ?></span>
                        </div>
                    </td>
                    <td class="hide-sm date-text">
                        <?= isset($s['created_at']) ? date('M d, Y', strtotime($s['created_at'])) : '—' ?>
                    </td>
                    <td>
                        <button class="del-btn"
                            onclick="confirmDelete('<?= htmlspecialchars($s['student_id']) ?>')">
                            🗑 Remove
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="no-results" id="noResults" style="display:none;">
                    <td colspan="4">No students match your search.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- Confirm-delete modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-icon">🗑️</div>
        <h3>Remove Student?</h3>
        <p>You're about to permanently delete student <strong id="modalStudentId"></strong>. This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <a class="btn-delete" id="confirmDeleteBtn" href="#">Delete</a>
        </div>
    </div>
</div>

<script>
    function confirmDelete(studentId) {
        document.getElementById('modalStudentId').textContent = studentId;
        document.getElementById('confirmDeleteBtn').href = 'view_students.php?delete=' + encodeURIComponent(studentId);
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    // Close modal on overlay click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if(e.target === this) closeModal();
    });

    // Live search
    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#studentsTable tbody tr:not(#noResults)');
        let visible = 0;
        rows.forEach(row => {
            const id = row.querySelector('.student-id-text');
            if(!id) return;
            const match = id.textContent.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
            if(match) visible++;
        });
        document.getElementById('noResults').style.display = visible === 0 ? '' : 'none';
    }
</script>
</body>
</html>