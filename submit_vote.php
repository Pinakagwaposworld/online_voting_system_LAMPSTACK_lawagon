<?php
session_start();
include 'db.php';

// 1. Security Check: Is the student logged in?
if(!isset($_SESSION['student_id'])){
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];
// We changed this to 'candidate_ids' (plural) to match the checkbox names
$candidate_ids = $_POST['candidate_ids'] ?? null;

if($candidate_ids && is_array($candidate_ids)){

    // 2. Loop through every candidate the user selected
    foreach($candidate_ids as $candidate_id){
        
        // Insert individual vote into the votes table
        $stmt = $conn->prepare("INSERT INTO votes(student_id, candidate_id) VALUES(?, ?)");
        $stmt->bind_param("si", $student_id, $candidate_id);
        $stmt->execute();

        // Increment the vote count for that specific candidate
        $stmt = $conn->prepare("UPDATE candidates SET votes = votes + 1 WHERE id=?");
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
    }

    // 3. Mark student as voted (once the loop is finished)
    $stmt = $conn->prepare("UPDATE students SET has_voted = 1 WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    echo "<div style='text-align:center; margin-top:50px;'>";
    echo "<h2>✅ Success!</h2>";
    echo "<h3>Thank you for voting. Your choices have been recorded.</h3>";
    echo "<a href='index.php' style='text-decoration:none; color:blue;'>Logout</a>";
    echo "</div>";

} else {
    // If they clicked "Vote" without selecting anyone
    echo "<h3>Error: Please select at least one candidate.</h3>";
    echo "<a href='vote.php'>Go back</a>";
}
?>