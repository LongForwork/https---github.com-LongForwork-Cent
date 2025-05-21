<?php
header('Content-Type: application/json');
require_once '../models/Service.php';
require_once '../utils/helpers.php';

// Kiểm tra tham số
if (!isset($_GET['branch_id']) || empty($_GET['branch_id'])) {
    echo json_encode(createError('Vui lòng chọn chi nhánh'));
    exit;
}

$branch_id = $_GET['branch_id'];

// Lấy danh sách dịch vụ
$serviceModel = new Service();
$services = $serviceModel->getServicesByBranchId($branch_id);

echo json_encode($services);
