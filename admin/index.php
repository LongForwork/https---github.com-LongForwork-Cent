<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get statistics
$stats = [
    'total_appointments' => 0,
    'pending_appointments' => 0,
    'today_appointments' => 0,
    'total_users' => 0
];

// Total appointments
$stmt = $pdo->query("SELECT COUNT(*) FROM appointments");
$stats['total_appointments'] = $stmt->fetchColumn();

// Pending appointments
$stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
$stats['pending_appointments'] = $stmt->fetchColumn();

// Today's appointments
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE date = CURDATE()");
$stmt->execute();
$stats['today_appointments'] = $stmt->fetchColumn();

// Total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$stats['total_users'] = $stmt->fetchColumn();

// Get recent appointments
$stmt = $pdo->query("
    SELECT a.*, u.phone, u.name as user_name, b.name as branch_name, s.name as service_name 
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    JOIN branches b ON a.branch_id = b.id
    JOIN services s ON a.service_id = s.id
    ORDER BY a.created_at DESC
    LIMIT 10
");
$recentAppointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Beauty Cent'</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <span><?php echo $_SESSION['admin_name']; ?></span>
                    <a href="logout.php" class="logout-btn">Đăng xuất</a>
                </div>
            </header>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_appointments']; ?></div>
                    <div class="stat-label">Tổng số lịch hẹn</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['pending_appointments']; ?></div>
                    <div class="stat-label">Lịch hẹn chờ xác nhận</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['today_appointments']; ?></div>
                    <div class="stat-label">Lịch hẹn hôm nay</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-label">Tổng số khách hàng</div>
                </div>
            </div>
            
            <div class="recent-appointments">
                <h2>Lịch hẹn gần đây</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Dịch vụ</th>
                            <th>Chi nhánh</th>
                            <th>Ngày</th>
                            <th>Giờ</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAppointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['id']; ?></td>
                                <td>
                                    <?php echo $appointment['user_name'] ? $appointment['user_name'] : 'Khách hàng mới'; ?>
                                    <div class="small-text"><?php echo $appointment['phone']; ?></div>
                                </td>
                                <td><?php echo $appointment['service_name']; ?></td>
                                <td><?php echo $appointment['branch_name']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($appointment['date'])); ?></td>
                                <td><?php echo $appointment['time_slot']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php 
                                        $statusLabels = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $statusLabels[$appointment['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="appointment-detail.php?id=<?php echo $appointment['id']; ?>" class="btn-view">Xem</a>
                                        <?php if ($appointment['status'] == 'pending'): ?>
                                            <a href="process/update-appointment.php?id=<?php echo $appointment['id']; ?>&status=confirmed" class="btn-confirm">Xác nhận</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recentAppointments)): ?>
                            <tr>
                                <td colspan="8" class="no-data">Không có lịch hẹn nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="view-all">
                    <a href="appointments.php" class="btn-view-all">Xem tất cả lịch hẹn</a>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
