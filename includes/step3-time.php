<div class="booking-step active" id="step3">
    <div class="step-header">
        <div class="step-number active">3</div>
        <h3>Chọn thời gian</h3>
    </div>
    
    <form action="process/save-step.php" method="POST">
        <input type="hidden" name="step" value="3">
        
        <div class="date-selector">
            <h4>Chọn ngày</h4>
            <div class="date-list">
                <?php
                $selectedDate = isset($_SESSION['booking']['date']) ? $_SESSION['booking']['date'] : date('Y-m-d');
                
                // Show dates for the next 7 days
                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', strtotime("+$i days"));
                    $displayDate = date('d/m', strtotime($date));
                    $dayName = date('D', strtotime($date));
                    
                    // Translate day name to Vietnamese
                    $dayTranslations = [
                        'Mon' => 'T2',
                        'Tue' => 'T3',
                        'Wed' => 'T4',
                        'Thu' => 'T5',
                        'Fri' => 'T6',
                        'Sat' => 'T7',
                        'Sun' => 'CN'
                    ];
                    
                    $dayVi = $dayTranslations[$dayName];
                    $isSelected = $selectedDate == $date;
                    ?>
                    <label class="date-item <?php echo $isSelected ? 'selected' : ''; ?>">
                        <input type="radio" name="date" value="<?php echo $date; ?>" <?php echo $isSelected ? 'checked' : ''; ?> required>
                        <div class="date-day"><?php echo $dayVi; ?></div>
                        <div class="date-number"><?php echo $displayDate; ?></div>
                    </label>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <div class="time-selector">
            <h4>Chọn giờ</h4>
            <div class="time-list">
                <?php
                $branchId = $_SESSION['booking']['branch_id'];
                $timeSlots = getTimeSlots($selectedDate, $branchId);
                $selectedTime = isset($_SESSION['booking']['time_slot']) ? $_SESSION['booking']['time_slot'] : null;
                
                foreach ($timeSlots as $slot) {
                    $isSelected = $selectedTime == $slot['time'];
                    $isAvailable = $slot['available'];
                    ?>
                    <label class="time-item <?php echo !$isAvailable ? 'disabled' : ($isSelected ? 'selected' : ''); ?>">
                        <input type="radio" name="time_slot" value="<?php echo $slot['time']; ?>" 
                               <?php echo $isSelected ? 'checked' : ''; ?> 
                               <?php echo !$isAvailable ? 'disabled' : ''; ?> required>
                        <div class="time-value"><?php echo $slot['time']; ?></div>
                    </label>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <div class="notes-section">
            <h4>Ghi chú</h4>
            <textarea name="notes" placeholder="Nhập ghi chú nếu có"><?php echo isset($_SESSION['booking']['notes']) ? $_SESSION['booking']['notes'] : ''; ?></textarea>
        </div>
        
        <div class="step-actions">
            <a href="index.php?step=2" class="btn-secondary btn-back">Quay lại</a>
            <button type="submit" class="btn-primary btn-next">Đặt lịch</button>
        </div>
    </form>
</div>
