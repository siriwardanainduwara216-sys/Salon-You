<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin only
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_name = $_SESSION['user_name'];
$admin_id   = (int)$_SESSION['user_id'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$flash = $_SESSION['admin_msg_flash'] ?? null;
unset($_SESSION['admin_msg_flash']);

// Does the table exist yet?
$check = mysqli_query($conn, "SHOW TABLES LIKE 'contact_messages'");
$table_ready = $check && mysqli_num_rows($check) > 0;

$filter = $_GET['filter'] ?? 'all';
if (!in_array($filter, ['all', 'new', 'read', 'replied'], true)) {
    $filter = 'all';
}
$selected_id = (int)($_GET['id'] ?? 0);

$counts = ['all' => 0, 'new' => 0, 'read' => 0, 'replied' => 0];
$messages = [];
$selected = null;

if ($table_ready) {

    // ---------- HANDLE POST (reply / delete) ----------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $back = "admin-messages.php?filter=" . urlencode($filter);

        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_msg_flash'] = ['type' => 'error', 'text' => 'Session expired. Please try again.'];
            header("Location: $back");
            exit();
        }

        $action = $_POST['action'] ?? '';
        $msg_id = (int)($_POST['message_id'] ?? 0);

        if ($action === 'reply' && $msg_id > 0) {
            $reply = trim($_POST['reply'] ?? '');
            if ($reply === '' || mb_strlen($reply) > 2000) {
                $_SESSION['admin_msg_flash'] = ['type' => 'error', 'text' => 'Reply must be between 1 and 2000 characters.'];
            } else {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE contact_messages
                     SET admin_reply = ?, replied_by = ?, replied_at = NOW(), status = 'replied'
                     WHERE id = ?"
                );
                mysqli_stmt_bind_param($stmt, "sii", $reply, $admin_id, $msg_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['admin_msg_flash'] = ['type' => 'success', 'text' => 'Reply saved. The customer can now see it.'];
            }
            header("Location: $back&id=$msg_id");
            exit();
        }

        if ($action === 'delete' && $msg_id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM contact_messages WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $msg_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['admin_msg_flash'] = ['type' => 'success', 'text' => 'Message deleted.'];
            header("Location: $back");
            exit();
        }

        header("Location: $back");
        exit();
    }

    // ---------- LOAD SELECTED MESSAGE (mark as read) ----------
    if ($selected_id > 0) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM contact_messages WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $selected_id);
        mysqli_stmt_execute($stmt);
        $selected = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($selected && $selected['status'] === 'new') {
            $stmt = mysqli_prepare($conn, "UPDATE contact_messages SET status = 'read' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $selected_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $selected['status'] = 'read';
        }
    }

    // ---------- COUNTS ----------
    $res = mysqli_query($conn, "SELECT status, COUNT(*) AS c FROM contact_messages GROUP BY status");
    while ($row = mysqli_fetch_assoc($res)) {
        $counts[$row['status']] = (int)$row['c'];
        $counts['all'] += (int)$row['c'];
    }

    // ---------- LIST ----------
    if ($filter === 'all') {
        $res = mysqli_query($conn, "SELECT id, name, subject, status, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 200");
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, subject, status, created_at FROM contact_messages WHERE status = ? ORDER BY created_at DESC LIMIT 200");
        mysqli_stmt_bind_param($stmt, "s", $filter);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $messages[] = $row;
    }
}

$unread_count = $counts['new'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Customer Messages</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="frontend-css/dashboard.css">
<style>
    .msg-flash { padding: 12px 16px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
    .msg-flash.success { background: #dcfce7; color: #166534; }
    .msg-flash.error   { background: #fee2e2; color: #991b1b; }

    .msg-filters { display: flex; gap: 8px; flex-wrap: wrap; margin: 15px 0; }
    .msg-filters a {
        padding: 7px 16px; border-radius: 20px; font-size: 13px; text-decoration: none;
        border: 1px solid rgba(128,128,128,0.35); color: inherit;
    }
    .msg-filters a.active { background: var(--accent-purple); color: #fff; border-color: transparent; }

    .msg-layout { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .msg-layout { grid-template-columns: 1fr; } }

    .msg-list { max-height: 70vh; overflow-y: auto; }
    .msg-row {
        display: block; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit;
        border-bottom: 1px solid rgba(128,128,128,0.2);
    }
    .msg-row:hover, .msg-row.selected { background: rgba(128,128,128,0.12); }
    .msg-row .top { display: flex; justify-content: space-between; gap: 8px; align-items: center; }
    .msg-row .subject { font-size: 13px; margin-top: 4px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .msg-row .date { font-size: 11px; color: var(--text-muted); margin-top: 3px; }
    .msg-row.unread .name { font-weight: 700; }

    .detail-meta { display: flex; flex-wrap: wrap; gap: 8px 22px; font-size: 13px; color: var(--text-muted); margin: 10px 0 18px; }
    .detail-meta a { color: inherit; }
    .msg-body { padding: 14px 16px; border-radius: 8px; background: rgba(128,128,128,0.1); line-height: 1.6; margin-bottom: 18px; }

    .reply-form textarea {
        width: 100%; min-height: 130px; padding: 12px; border-radius: 8px; resize: vertical;
        border: 1px solid rgba(128,128,128,0.4); font-family: inherit; font-size: 14px; box-sizing: border-box;
    }
    .btn-reply, .btn-delete {
        border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; cursor: pointer; color: #fff;
    }
    .btn-reply  { background: var(--accent-purple); }
    .btn-delete { background: #dc2626; }
    .actions-row { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; gap: 10px; }
    .nav-badge { margin-left: 6px; }
</style>
</head>
<body>

<div class="admin-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Salon You</h2>
            <p>Admin Panel</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-chart-line"></i><span> Dashboard</span></a></li>
            <li><a href="admin-staff.php"><i class="fas fa-users"></i><span> Staff Management</span></a></li>
            <li><a href="admin-customers.php"><i class="fas fa-user-friends"></i><span> Customer Management</span></a></li>
            <li><a href="admin-appointments.php"><i class="fas fa-calendar-check"></i><span> Appointments</span></a></li>
            <li><a href="admin-queue.php"><i class="fas fa-list-ol"></i><span> Today's Queue</span></a></li>
            <li><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
            <li><a href="admin-products.php"><i class="fas fa-pump-soap"></i><span> Product</span></a></li>
            <li><a href="admin-product-orders.php"><i class="fas fa-shopping-basket"></i><span> Product Orders</span></a></li>
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-closed-dates.php"><i class="fas fa-calendar-times"></i><span> Closed Dates</span></a></li>
            <li class="active">
                <a href="admin-messages.php"><i class="fas fa-envelope"></i><span> Messages</span>
                    <?php if ($unread_count > 0): ?><span class="badge low nav-badge"><?php echo $unread_count; ?></span><?php endif; ?>
                </a>
            </li>
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">

        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
            <div class="topbar-right">
                <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
            </div>
        </div>

        <div class="dashboard-content">
            <h2>Customer Messages</h2>

            <?php if (!$table_ready): ?>
                <div class="msg-flash error">
                    The <strong>contact_messages</strong> table does not exist yet. Run <em>database/contact_messages.sql</em> in phpMyAdmin first.
                </div>
            <?php else: ?>

                <?php if ($flash): ?>
                    <div class="msg-flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($flash['text']); ?>
                    </div>
                <?php endif; ?>

                <div class="msg-filters">
                    <a href="?filter=all"     class="<?php echo $filter === 'all' ? 'active' : ''; ?>">All (<?php echo $counts['all']; ?>)</a>
                    <a href="?filter=new"     class="<?php echo $filter === 'new' ? 'active' : ''; ?>">New (<?php echo $counts['new']; ?>)</a>
                    <a href="?filter=read"    class="<?php echo $filter === 'read' ? 'active' : ''; ?>">Read (<?php echo $counts['read']; ?>)</a>
                    <a href="?filter=replied" class="<?php echo $filter === 'replied' ? 'active' : ''; ?>">Replied (<?php echo $counts['replied']; ?>)</a>
                </div>

                <div class="msg-layout">

                    <!-- LEFT: message list -->
                    <div class="panel msg-list">
                        <?php if (empty($messages)): ?>
                            <p style="color: var(--text-muted); font-size: 13px;">No messages found.</p>
                        <?php else: ?>
                            <?php foreach ($messages as $m): ?>
                                <a class="msg-row <?php echo $m['status'] === 'new' ? 'unread' : ''; ?> <?php echo $selected_id === (int)$m['id'] ? 'selected' : ''; ?>"
                                   href="?filter=<?php echo urlencode($filter); ?>&id=<?php echo (int)$m['id']; ?>">
                                    <div class="top">
                                        <span class="name"><?php echo htmlspecialchars($m['name']); ?></span>
                                        <?php if ($m['status'] === 'new'): ?>
                                            <span class="badge low">New</span>
                                        <?php elseif ($m['status'] === 'replied'): ?>
                                            <span class="badge ok">Replied</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="subject"><?php echo htmlspecialchars($m['subject']); ?></div>
                                    <div class="date"><?php echo date('M d, Y h:i A', strtotime($m['created_at'])); ?></div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- RIGHT: message detail + reply -->
                    <div class="panel">
                        <?php if (!$selected): ?>
                            <p style="color: var(--text-muted); font-size: 14px;">Select a message from the list to read and reply.</p>
                        <?php else: ?>
                            <h4 style="font-size:18px;"><?php echo htmlspecialchars($selected['subject']); ?></h4>

                            <div class="detail-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($selected['name']); ?></span>
                                <span><i class="fas fa-envelope"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($selected['email']); ?>"><?php echo htmlspecialchars($selected['email']); ?></a>
                                </span>
                                <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($selected['phone']); ?></span>
                                <span><i class="far fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($selected['created_at'])); ?></span>
                            </div>

                            <?php if (empty($selected['user_id'])): ?>
                                <p style="font-size:12px; color: var(--text-muted); margin-bottom:12px;">
                                    <i class="fas fa-info-circle"></i> Guest message: the sender has no account, so they cannot see your reply on the website. Contact them by email or phone too.
                                </p>
                            <?php endif; ?>

                            <div class="msg-body"><?php echo nl2br(htmlspecialchars($selected['message'])); ?></div>

                            <form method="POST" class="reply-form">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="message_id" value="<?php echo (int)$selected['id']; ?>">

                                <label style="font-size:13px; font-weight:600;">
                                    <?php echo $selected['status'] === 'replied' ? 'Your reply (you can edit it)' : 'Write a reply'; ?>
                                </label>
                                <textarea name="reply" maxlength="2000" required placeholder="Type your reply to the customer..."><?php echo htmlspecialchars($selected['admin_reply'] ?? ''); ?></textarea>

                                <div class="actions-row">
                                    <button type="submit" name="action" value="reply" class="btn-reply">
                                        <i class="fas fa-paper-plane"></i> <?php echo $selected['status'] === 'replied' ? 'Update Reply' : 'Send Reply'; ?>
                                    </button>
                                    <button type="submit" name="action" value="delete" class="btn-delete" formnovalidate
                                            onclick="return confirm('Delete this message permanently?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </form>

                            <?php if ($selected['status'] === 'replied' && !empty($selected['replied_at'])): ?>
                                <p style="font-size:12px; color: var(--text-muted); margin-top:12px;">
                                    Last replied: <?php echo date('M d, Y h:i A', strtotime($selected['replied_at'])); ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>