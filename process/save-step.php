<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'];
    
    // Initialize booking session if not exists
    if (!isset($_SESSION['booking'])) {
        $_SESSION['booking'] = [];
    }
    
    // Process step 1 - Branch selection
    if ($step == 1) {
        $_SESSION['booking']['branch_id'] = $_POST['branch_id'];
        header('Location: ../index.php?step=2');
        exit;
    }
    
    // Process step 2 - Service selection
    elseif ($step == 2) {
        $_SESSION['booking']['service_id'] = $_POST['service_id'];
        header('Location: ../index.php?step=3');
        exit;
    }
    
    // Process step 3 - Time selection and complete booking
    elseif ($step == 3) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
            header('Location: ../index.php?step=3');
            exit;
        }
        
        $_SESSION['booking']['date'] = $_POST['date'];
        $_SESSION['booking']['time_slot'] = $_POST['time_slot'];
        $_SESSION['booking']['notes'] = isset($_POST['notes']) ? $_POST['notes'] : '';
        
        // Create appointment in database
        $result = createAppointment(
            $_SESSION['user_id'],
            $_SESSION['booking']['branch_id'],
            $_SESSION['booking']['service_id'],
            $_SESSION['booking']['date'],
            $_SESSION['booking']['time_slot'],
            $_SESSION['booking']['notes']
        );
        
        if ($result) {
            header('Location: ../index.php?step=confirm');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt lịch. Vui lòng thử lại.';
            header('Location: ../index.php?step=3');
        }
        exit;
    }
}

// If not a valid request, redirect to homepage
header('Location: ../index.php');
exit;
