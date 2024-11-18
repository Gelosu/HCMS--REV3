<?php
// Include your database connection
include '../connect.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = "%{$query}%"; // Wildcard search

    // Prepare SQL to search for patients by name
    $sql = $conn->prepare("SELECT p_id, p_name FROM patient WHERE p_name LIKE ?");
    $sql->bind_param("s", $query);
    $sql->execute();
    $result = $sql->get_result();

    $patients = [];

    // Fetch all matching patients
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = [
                'p_id' => $row['p_id'],
                'p_name' => $row['p_name']
            ];
        }
    }

    // Return JSON response
    echo json_encode($patients);
}

$conn->close();
?>
