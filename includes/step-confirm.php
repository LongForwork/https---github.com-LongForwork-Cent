<div class="booking-step active" id="step-confirm">
    <div class="confirmation-message">
        <div class="confirmation-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <h2>Đặt lịch thành công!</h2>
        <p>Cảm ơn bạn đã đặt lịch tại Beauty Cent'</p>
        
        <div class="booking-details">
            <h3>Chi tiết lịch hẹn</h3>
            
            <?php
            // Get booking details from session
            $branchId = $_SESSION['booking']['branch_id'];
            $serviceId = $_SESSION['booking']['service_id'];
            $date = $_SESSION['booking']['date'];
            $timeSlot = $_SESSION['booking']['time_slot'];
            
            // Get branch and service information
            $stmt = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
            $stmt->execute([$branchId]);
            $branch = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([$serviceId]);
            $service = $stmt->fetch();
            
            // Format date for display
            $displayDate = date('d/m/Y', strtotime($date));
            ?>
            
            <div class="detail-item">
                <div class="detail-label">Chi nhánh:</div>
                <div class="detail-value"><?php echo $branch['address']; ?>, <?php echo $branch['district']; ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Dịch vụ:</div>
                <div class="detail-value"><?php echo $service['name']; ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Ngày:</div>
                <div class="detail-value"><?php echo $displayDate; ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Giờ:</div>
                <div class="detail-value"><?php echo $timeSlot; ?></div>
            </div>
            
            <?php if (!empty($_SESSION['booking']['notes'])): ?>
            <div class="detail-item">
                <div class="detail-label">Ghi chú:</div>
                <div class="detail-value"><?php echo $_SESSION['booking']['notes']; ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="confirmation-actions">
            <a href="index.php?step=1" class="btn-primary">Đặt lịch mới</a>
            <a href="user/appointments.php" class="btn-secondary">Xem lịch hẹn của tôi</a>
        </div>
    </div>
</div>
