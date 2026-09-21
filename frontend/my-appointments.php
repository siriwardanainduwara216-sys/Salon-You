<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

$success_msg = isset($_GET['cancelled']) ? 'Appointment cancelled successfully.' : '';
if (isset($_GET['waitlisted'])) {
    $success_msg = "You've been added to the waitlist! We'll email you if this slot opens up.";
}

require_once __DIR__ . '/../backend/loyalty-helper.php';

// ---- Fetch this customer's loyalty points ----
$sql = "SELECT loyalty_points FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$loyalty_points = (int) (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['loyalty_points'] ?? 0);
mysqli_stmt_close($stmt);

$tier = calculate_loyalty_tier($loyalty_points);

// Progress bar percentage toward the next tier
$prev_threshold = $loyalty_points - (($loyalty_points < 100) ? $loyalty_points : (100 + floor(($loyalty_points - 100) / 50) * 50));
$tier_range = $loyalty_points < 100 ? 100 : 50;
$progress_pct = $tier_range > 0 ? max(0, min(100, (($tier_range - $tier['points_to_next']) / $tier_range) * 100)) : 100;

require_once __DIR__ . '/../backend/my-appointments-data.php';

// ---- Auto-cancel today's appointments whose time has fully passed without being served ----
foreach ($my_appointments as &$appt) {
    if ($appt['appointment_date'] === date('Y-m-d')
        && in_array($appt['status'], ['pending', 'confirmed'])
        && strtotime($appt['appointment_date'] . ' ' . $appt['appointment_time']) < time()) {

        // Check whether the customer was actually served (queue_status = done)
        $sql = "SELECT queue_status, staff_id FROM appointments WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $appt['id']);
        mysqli_stmt_execute($stmt);
        $info_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        $qstatus = $info_row['queue_status'] ?? 'waiting';
        $appt_staff_id = $info_row['staff_id'] ?? null;
        mysqli_stmt_close($stmt);

        if ($qstatus !== 'done') {
            // Not served and the time has passed - mark as cancelled (no-show)
            $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = ? AND status IN ('pending', 'confirmed')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $appt['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $appt['status'] = 'cancelled'; // reflect immediately on this page load
            $appt['auto_cancelled'] = true;

            // Free slot - check if anyone is waiting for it
            if ($appt_staff_id) {
                require_once __DIR__ . '/../waitlist-helper.php';
                notify_waitlist_if_available($conn, $appt_staff_id, $appt['appointment_date'], $appt['appointment_time']);
            }
        }
    }
}
unset($appt);

// ---- For any of today's appointments, fetch the FULL queue for that stylist ----
foreach ($my_appointments as &$appt) {
    $appt['queue_position'] = null;
    $appt['full_queue'] = [];

    if ($appt['appointment_date'] === date('Y-m-d') && in_array($appt['status'], ['pending', 'confirmed'])) {
        // The customer's own position number
        $sql = "SELECT COUNT(*) AS position FROM appointments
                WHERE staff_id = (SELECT staff_id FROM appointments WHERE id = ?)
                AND appointment_date = CURDATE()
                AND status IN ('pending', 'confirmed')
                AND queue_status IN ('waiting', 'in_progress')
                AND appointment_time <= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $appt['id'], $appt['appointment_time']);
        mysqli_stmt_execute($stmt);
        $appt['queue_position'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['position'] ?? null;
        mysqli_stmt_close($stmt);

        // The FULL queue for this stylist today (everyone in line, in order)
        $sql = "SELECT a.id, a.appointment_time, a.queue_status, u.name AS customer_name, s.service_name
                FROM appointments a
                JOIN users u ON a.user_id = u.id
                JOIN services s ON a.service_id = s.id
                WHERE a.staff_id = (SELECT staff_id FROM appointments WHERE id = ?)
                AND a.appointment_date = CURDATE()
                AND a.status IN ('pending', 'confirmed')
                ORDER BY a.appointment_time ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $appt['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $appt['full_queue'][] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
unset($appt);

// ---- Fetch this customer's waitlist entries ----
$my_waitlist = [];
$sql = "SELECT w.id, w.waitlist_date, w.waitlist_time, w.status,
               s.service_name, st.name AS staff_name
        FROM appointment_waitlist w
        JOIN services s ON w.service_id = s.id
        JOIN users st ON w.staff_id = st.id
        WHERE w.user_id = ?
        ORDER BY w.waitlist_date ASC, w.waitlist_time ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $my_waitlist[] = $row;
}
mysqli_stmt_close($stmt);

// Statuses that are still allowed to be cancelled by the customer
$cancellable_statuses = ['pending', 'confirmed'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/my-appointments.css">
</head>
<body>

    <header class="site-header">
        <a href="index.php" class="logo-container">
            <img src="../logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>
        <div class="header-actions">
            <div class="user-profile">
                <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                <a href="index.php" class="btn-register" style="margin-right: 8px;">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
                <a href="../backend/logout.php" class="btn-logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="appt-page-wrap">
        <h1>My Appointments</h1>
        <p class="subtitle">Welcome, <?php echo htmlspecialchars($user_name); ?> - here's your booking history.</p>

        <?php if ($success_msg): ?>
            <div class="alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <!-- Loyalty Rewards Card -->
        <div class="loyalty-card">
            <div class="loyalty-top">
                <div class="loyalty-tier-badge">
                    <i class="fas <?php echo $tier['tier_icon']; ?>" style="color: <?php echo $tier['tier_color']; ?>;"></i>
                    <div class="tier-info">
                        <h3 style="color: <?php echo $tier['tier_color']; ?>;"><?php echo $tier['tier_name']; ?> Member</h3>
                        <p>Loyalty Rewards Program</p>
                    </div>
                </div>
                <div class="loyalty-points-num">
                    <div class="num"><?php echo $loyalty_points; ?></div>
                    <div class="label">Total Points</div>
                </div>
            </div>

            <?php if ($tier['discount'] > 0): ?>
                <div class="loyalty-offer">
                    <i class="fas fa-gift"></i>
                    You've unlocked a <strong><?php echo $tier['discount']; ?>% OFF</strong> discount on your next service! Show this to our staff when you visit.
                </div>
            <?php endif; ?>

            <div class="loyalty-progress-track">
                <div class="loyalty-progress-fill" style="width: <?php echo $progress_pct; ?>%;"></div>
            </div>
            <div class="loyalty-progress-label">
                <?php echo $tier['points_to_next']; ?> more points to reach <strong><?php echo $tier['next_tier_name']; ?></strong>
                <?php
                    // Show what reward the next tier unlocks
                    $next_tier_preview = calculate_loyalty_tier($loyalty_points + $tier['points_to_next']);
                ?>
                (unlocks <?php echo $next_tier_preview['discount']; ?>% OFF)
            </div>
        </div>

        <?php if (empty($my_appointments)): ?>
            <div class="empty-state">
                <p>You haven't booked any appointments yet.</p>
                <p><a href="services.php">Browse our services →</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($my_appointments as $appt): ?>
                <div class="appt-card">
                    <div class="appt-info">
                        <h3><?php echo htmlspecialchars($appt['service_name']); ?></h3>
                        <p>Stylist: <strong><?php echo $appt['staff_name'] ? htmlspecialchars($appt['staff_name']) : 'Unassigned'; ?></strong></p>
                        <p><?php echo $appt['appointment_date']; ?> at <?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></p>
                        <p>Price: Rs. <?php echo number_format($appt['price'], 2); ?></p>
                        <?php if (!empty($appt['full_queue'])): ?>
                            <button type="button" class="btn-view-queue" onclick="toggleQueue(<?php echo $appt['id']; ?>)">
                                <i class="fas fa-list-ol"></i> View Today's Queue
                            </button>
                            <div class="today-queue-box" id="queue-box-<?php echo $appt['id']; ?>" style="display:none;">
                                <p class="queue-box-title"><i class="fas fa-list-ol"></i> Today's Queue - <?php echo htmlspecialchars($appt['staff_name']); ?></p>
                                <?php foreach ($appt['full_queue'] as $index => $q): ?>
                                    <div class="queue-list-row <?php echo $q['id'] == $appt['id'] ? 'is-you' : ''; ?>">
                                        <span class="queue-list-num">#<?php echo $index + 1; ?></span>
                                        <span class="queue-list-name">
                                            <?php if ($q['id'] == $appt['id']): ?>
                                                <strong>You</strong> - <?php echo htmlspecialchars($q['customer_name']); ?>
                                                <small>(<?php echo htmlspecialchars($q['service_name']); ?>)</small>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($q['customer_name']); ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="queue-list-time"><?php echo date('h:i A', strtotime($q['appointment_time'])); ?></span>
                                        <span class="status-pill-sm <?php echo $q['queue_status']; ?>"><?php echo str_replace('_', ' ', $q['queue_status']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($appt['appointment_date'] === date('Y-m-d') && in_array($appt['status'], ['pending', 'confirmed'])): ?>
                            <p class="countdown-timer" data-target="<?php echo $appt['appointment_date'] . 'T' . $appt['appointment_time']; ?>">
                                <i class="fas fa-hourglass-half"></i> Calculating...
                            </p>
                        <?php elseif (!empty($appt['auto_cancelled'])): ?>
                            <p class="cancelled-notice">
                                <i class="fas fa-times-circle"></i> Your appointment is cancelled
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="appt-actions">
                        <span class="appt-status <?php echo $appt['status']; ?>"><?php echo $appt['status']; ?></span>
                        <?php if (in_array($appt['status'], $cancellable_statuses)): ?>
                            <a href="cancel-appointment.php?id=<?php echo $appt['id']; ?>" class="btn-cancel-appt"
                               onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($my_waitlist)): ?>
            <h1 style="font-size:22px; margin-top:40px;">My Waitlist</h1>
            <p class="subtitle" style="margin-bottom:20px;">Slots you're waiting for - we'll email you if one opens up.</p>
            <?php foreach ($my_waitlist as $w): ?>
                <div class="appt-card">
                    <div class="appt-info">
                        <h3><?php echo htmlspecialchars($w['service_name']); ?></h3>
                        <p>Stylist: <strong><?php echo htmlspecialchars($w['staff_name']); ?></strong></p>
                        <p><?php echo $w['waitlist_date']; ?> at <?php echo date('h:i A', strtotime($w['waitlist_time'])); ?></p>
                    </div>
                    <div class="appt-actions">
                        <?php if ($w['status'] === 'waiting'): ?>
                            <span class="appt-status pending"><i class="fas fa-hourglass-half"></i> Waiting</span>
                        <?php elseif ($w['status'] === 'notified'): ?>
                            <span class="appt-status confirmed"><i class="fas fa-bell"></i> Notified - Book Now!</span>
                        <?php else: ?>
                            <span class="appt-status cancelled">Expired</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    // Live reverse countdown timer for today's upcoming appointments
    function updateCountdowns() {
        document.querySelectorAll('.countdown-timer').forEach(function(el) {
            const target = new Date(el.getAttribute('data-target'));
            const now = new Date();
            let diff = target - now;

            if (diff <= 0) {
                el.innerHTML = '<i class="fas fa-bell"></i> It\'s time for your appointment!';
                el.classList.add('overdue');
                return;
            }

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const pad = (n) => String(n).padStart(2, '0');
            el.innerHTML = '<i class="fas fa-hourglass-half"></i> Time remaining: ' + pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    // Toggle the full queue list open/closed when the button is clicked
    function toggleQueue(appointmentId) {
        const box = document.getElementById('queue-box-' + appointmentId);
        if (box.style.display === 'none') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
    </script>

</body>
</html>