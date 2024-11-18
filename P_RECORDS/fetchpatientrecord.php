<?php
include '../connect.php'; // Include your database connection

header('Content-Type: application/json'); // Ensure the response is JSON formatted

if (isset($_GET['patientId'])) {
    $patientId = $_GET['patientId'];

    // Prepare the SQL query to fetch patient records
    $stmt = $conn->prepare("SELECT * FROM patient_records WHERE patient_id = ?");
    $stmt->bind_param("i", $patientId);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $records = array();

        // Fetch all records and store in the array
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }

        // Return the records in JSON format
        echo json_encode($records);
    } else {
        // In case of an error, return an error message
        echo json_encode(array('error' => 'Error fetching patient records.'));
    }

    // Close the statement
    $stmt->close();
} else {
    // If patientId is not set, return an error
    echo json_encode(array('error' => 'No patient ID provided.'));
}

// Close the database connection
$conn->close();
?>
