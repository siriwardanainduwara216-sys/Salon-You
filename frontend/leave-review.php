<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$customer_name = $_SESSION['user_name'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

$success_msg = '';
$error_msg = '';

// ============================================================
// REVIEW SUBMIT
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {

    $rating       = (int) $_POST['rating'];
    $comment      = trim($_POST['comment']);
    $appointment_choice = $_POST['appointment_id']; // 'general' or an actual appointment id

    if ($rating < 1 || $rating > 5) {
        $error_msg = "Star rating ekak select karanna.";
    } elseif (empty($comment)) {
        $error_msg = "Comment ekak liyanna.";
    } else {
        $appointment_id = ($appointment_choice === 'general') ? null : (int) $appointment_choice;

        if ($appointment_id === null) {
            $sql = "INSERT INTO reviews (user_id, appointment_id, rating, comment) VALUES (?, NULL, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iis", $customer_id, $rating, $comment);
        } else {
            $sql = "INSERT INTO reviews (user_id, appointment_id, rating, comment) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiis", $customer_id, $appointment_id, $rating, $comment);
        }

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Bohoma sthuthi! Oyage review eka submit wela ivarai.";
        } else {
            $error_msg = "Error ekak: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// ---- Fetch customer's completed bookings (for the dropdown) ----
$completed_bookings = [];
$sql = "SELECT a.id, a.appointment_date, s.service_name
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.user_id = ? AND a.status = 'completed'
        ORDER BY a.appointment_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $completed_bookings[] = $row;
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Leave a Review</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/review.css">
</head>
<body>

<div class="review-card">
    <h2>Leave a Review</h2>
    <p class="subtitle">Oyage feedback eka apata bohoma vagakiyala thiyenawa, <?php echo htmlspecialchars($customer_name); ?>!</p>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form method="POST" action="leave-review.php">
        <div class="form-group">
            <label>Meka kumana booking ekakata idada, general feedback ekakada?</label>
            <select name="appointment_id" required>
                <option value="general">General Feedback (Salon eka gæna)</option>
                <?php foreach ($completed_bookings as $b): ?>
                    <option value="<?php echo $b['id']; ?>">
                        <?php echo htmlspecialchars($b['service_name']) . ' - ' . $b['appointment_date']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Rating eka</label>
            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
            </div>
        </div>

        <div class="form-group">
            <label>Comment eka</label>
            <textarea name="comment" placeholder="Oyage experience eka apata kiyanna..." required></textarea>
        </div>

        <button type="submit" name="submit_review" class="btn">
            <i class="fas fa-paper-plane"></i> Submit Review
        </button>
    </form>

    <a href="index.php" class="back-link">← Home ekata yanna</a>
</div>

</body>
</html>