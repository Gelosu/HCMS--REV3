<?php
// Include database connection file
include '../connect.php';

header('Content-Type: application/json'); // Set content type to JSON

// Get the record ID from the request
$recordId = isset($_GET['recordId']) ? intval($_GET['recordId']) : 0;

if ($recordId <= 0) {
    echo json_encode(['error' => 'Invalid record ID']);
    exit;
}

// Prepare and execute the SQL query to fetch the record details from patient_records
$sql = "SELECT * FROM patient_records WHERE record_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recordId);
$stmt->execute();
$result = $stmt->get_result();

// Check if a record was found
if ($result->num_rows > 0) {
    $record = $result->fetch_assoc();

    // Fetch patient name based on patient_id in patient_records
    $patientId = $record['patient_id'];
    
    $patientSql = "SELECT p_name FROM patient WHERE p_id = ?";
    $patientStmt = $conn->prepare($patientSql);
    $patientStmt->bind_param("i", $patientId);
    $patientStmt->execute();
    $patientResult = $patientStmt->get_result();
    
    if ($patientResult->num_rows > 0) {
        $patient = $patientResult->fetch_assoc();
        $record['p_name'] = $patient['p_name']; // Add the patient's name to the record array
    } else {
        $record['p_name'] = 'Unknown'; // Handle case where patient name is not found
    }

    echo json_encode($record); // Send the combined record and patient name
} else {
    echo json_encode(['error' => 'Record not found']);
}

// Close the database connections
$stmt->close();
$patientStmt->close();
$conn->close();
?>
