<div class="booking-step active" id="step1">
    <div class="step-header">
        <div class="step-number active">1</div>
        <h3>Chọn chi nhánh</h3>
    </div>
    
    <form action="process/save-step.php" method="POST">
        <input type="hidden" name="step" value="1">
        
        <div class="branch-list">
            <?php
            $branches = getBranches();
            $selectedBranch = isset($_SESSION['booking']['branch_id']) ? $_SESSION['booking']['branch_id'] : null;
            
            foreach ($branches as $branch) {
                $isSelected = $selectedBranch == $branch['id'];
            ?>
                <div class="branch-item">
                    <label class="radio-container">
                        <input type="radio" name="branch_id" value="<?php echo $branch['id']; ?>" <?php echo $isSelected ? 'checked' : ''; ?> required>
                        <span class="radio-checkmark"></span>
                        <div class="branch-info">
                            <div class="branch-address"><?php echo $branch['address']; ?></div>
                            <div class="branch-district"><?php echo $branch['district']; ?>, <?php echo $branch['city']; ?></div>
                        </div>
                    </label>
                </div>
            <?php } ?>
        </div>
        
        <div class="step-actions">
            <button type="submit" class="btn-primary btn-next">Tiếp tục</button>
        </div>
    </form>
</div>
