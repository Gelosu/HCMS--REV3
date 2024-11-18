<?php
include '../connect.php'; // Include your database connection

header('Content-Type: application/json'); // Ensure the response is JSON formatted

// Prepare the SQL query to fetch all patient records
$sql = "SELECT * FROM patient";
$result = $conn->query($sql);

if ($result) {
    $patients = array();

    // Fetch all records and store in the array
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }

    // Return the records in JSON format
    echo json_encode($patients);
} else {
    // In case of an error, return an error message
    echo json_encode(array('error' => 'Error fetching patient records.'));
}

// Close the database connection
$conn->close();
?>
