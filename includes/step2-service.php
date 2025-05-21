<div class="booking-step active" id="step2">
    <div class="step-header">
        <div class="step-number active">2</div>
        <h3>Chọn dịch vụ</h3>
    </div>
    
    <form action="process/save-step.php" method="POST">
        <input type="hidden" name="step" value="2">
        
        <div class="service-categories">
            <?php
            $categories = getServiceCategories();
            $services = getServices();
            $selectedService = isset($_SESSION['booking']['service_id']) ? $_SESSION['booking']['service_id'] : null;
            
            foreach ($categories as $category) {
                echo '<div class="service-category">';
                echo '<h4>' . $category['name'] . '</h4>';
                echo '<div class="service-list">';
                
                foreach ($services as $service) {
                    if ($service['category_id'] == $category['id']) {
                        $isSelected = $selectedService == $service['id'];
                        ?>
                        <div class="service-item">
                            <label class="radio-container">
                                <input type="radio" name="service_id" value="<?php echo $service['id']; ?>" <?php echo $isSelected ? 'checked' : ''; ?> required>
                                <span class="radio-checkmark"></span>
                                <div class="service-info">
                                    <div class="service-name"><?php echo $service['name']; ?></div>
                                    <div class="service-price"><?php echo number_format($service['price'], 0, ',', '.'); ?> đ</div>
                                    <div class="service-duration"><?php echo $service['duration']; ?> phút</div>
                                </div>
                            </label>
                        </div>
                        <?php
                    }
                }
                
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="step-actions">
            <a href="index.php?step=1" class="btn-secondary btn-back">Quay lại</a>
            <button type="submit" class="btn-primary btn-next">Tiếp tục</button>
        </div>
    </form>
</div>
