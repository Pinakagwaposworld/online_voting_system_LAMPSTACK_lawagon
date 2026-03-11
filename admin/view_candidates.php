<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])) header("Location: login.php");

// Handle delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $msg = "Candidate deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error deleting candidate.";
        $msg_type = "error";
    }
}

// Fetch all candidates
$result = $conn->query("SELECT id, name, position, votes FROM candidates ORDER BY position, name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Candidates</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        h2 {
            font-size: 26px;
            color: #2c3e50;
        }

        .btn {
            display: inline-block;
            padding: 9px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background 0.2s ease;
        }

        .btn-primary {
            background-color: #3498db;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
            padding: 6px 14px;
            font-size: 13px;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #2c3e50;
            color: #fff;
        }

        thead th {
            padding: 14px 18px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.15s ease;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tbody td {
            padding: 13px 18px;
            font-size: 14px;
        }

        .position-badge {
            display: inline-block;
            background-color: #eaf4fb;
            color: #2980b9;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .votes-count {
            font-weight: 700;
            color: #27ae60;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #95a5a6;
        }

        .empty-state p {
            font-size: 16px;
            margin-bottom: 16px;
        }

        /* Confirmation modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .modal p {
            font-size: 14px;
            color: #666;
            margin-bottom: 24px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-secondary {
            background-color: #ecf0f1;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #dfe6e9;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🗳️ Candidates List</h2>
        <a href="add_candidate.php" class="btn btn-primary">+ Add Candidate</a>
    </div>

    <?php if(isset($msg)): ?>
        <div class="alert alert-<?php echo $msg_type; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <?php if($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Candidate Name</th>
                    <th>Position</th>
                    <th>Votes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><span class="position-badge"><?php echo htmlspecialchars($row['position']); ?></span></td>
                    <td><span class="votes-count"><?php echo intval($row['votes']); ?></span></td>
                    <td>
                        <button 
                            class="btn btn-danger" 
                            onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['name'])); ?>')">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>No candidates found.</p>
            <a href="add_candidate.php" class="btn btn-primary">Add First Candidate</a>
        </div>
        <?php endif; ?>
    </div>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Delete Candidate?</h3>
        <p id="modalText">Are you sure you want to delete this candidate? This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete</a>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('modalText').textContent =
            'Are you sure you want to delete "' + name + '"? This action cannot be undone.';
        document.getElementById('confirmDeleteBtn').href = 'view_candidates.php?delete=' + id;
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    // Close modal on overlay click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if(e.target === this) closeModal();
    });
</script>

</body>
</html>