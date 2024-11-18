<?php
include '../connect.php'; // Include database connection

header('Content-Type: application/json'); // Set content type to JSON

$response = array(); // Initialize response array

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $patientId = $_POST['patientId'] ?? null;
    $patientAge = $_POST['patientAge'] ?? null;
    $patientHeight = $_POST['patientHeight'] ?? null;
    $patientWeight = $_POST['patientWeight'] ?? null;
    $recordType = $_POST['recordType'] ?? null;
    $recordDate = $_POST['recordDate'] ?? null;

    // Medical history
    $personalHistory = $_POST['personalHistory'] ?? null;
    $allergyHistory = $_POST['allergyHistory'] ?? null;
    $surgeriesHistory = $_POST['surgeriesHistory'] ?? null;
    $currentMedications = $_POST['currentMedications'] ?? null;
    $immunizationRecords = $_POST['immunizationRecords'] ?? null;

    // Present illness
    $currentSymptoms = $_POST['currentSymptoms'] ?? null;
    $onsetDate = $_POST['onsetDate'] ?? null;
    $recentVisits = $_POST['recentVisits'] ?? null;

    // Check if vitalSigns is set and handle the JSON
    $vitalSigns = isset($_POST['vitalSigns']) ? $_POST['vitalSigns'] : null;

    // Physical Examination
    $physicalFindings = $_POST['physicalFindings'] ?? null;

    // Lab results
    $labResults = $_POST['labResults'] ?? null;

    // Diagnosis and treatment
    $finalDiagnosis = $_POST['finalDiagnosis'] ?? null;
    $treatmentPlan = $_POST['treatmentPlan'] ?? null;
    $procedures = $_POST['procedures'] ?? null;
    $followUpSchedule = $_POST['followUpSchedule'] ?? null;  // Handle this carefully as it's a date field
    $referrals = $_POST['referrals'] ?? null;

    // Progress Notes
    $progressNotes = $_POST['progressNotes'] ?? null;

    // Prepare and execute the insert query
    $sql = "INSERT INTO patient_records 
        (patient_id, p_age, p_height, p_weight, record_type, record_date, personal_history, allergy_history, surgeries_history, current_medications, immunization_records, 
        current_symptoms, onset_date, recent_visits, vital_signs, physical_findings, lab_results, diagnosis, treatment_plan, procedures, follow_up_schedule, referrals, progress_notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    // Bind the values, ensuring you have the correct number of parameters
    $stmt->bind_param("iiiisssssssssssssssssss", 
        $patientId, $patientAge, $patientHeight, $patientWeight, 
        $recordType, $recordDate, $personalHistory, $allergyHistory, 
        $surgeriesHistory, $currentMedications, $immunizationRecords, 
        $currentSymptoms, $onsetDate, $recentVisits, $vitalSigns, 
        $physicalFindings, $labResults, $finalDiagnosis, 
        $treatmentPlan, $procedures, $followUpSchedule, 
        $referrals, $progressNotes);

    if ($stmt->execute()) {
        // Record added successfully
        $response['success'] = true;
        $response['message'] = 'Patient record added successfully';

     

        // Prepare to fetch the newly added record
        $fetchSql = "SELECT * FROM patient_records WHERE patient_id = ?";
        $fetchStmt = $conn->prepare($fetchSql);
        $fetchStmt->bind_param("i", $patientId);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();

        // Check if the record was found
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
            $response['record'] = $record; // Add the record to the response
        } else {
            $response['error'] = 'Error fetching the newly added record.';
        }

        // Close the fetch statement
        $fetchStmt->close();
    } else {
        if ($conn->errno == 1062) {
            $response['error'] = 'The data already exists!';
        } else {
            $response['error'] = 'An error occurred while processing your request. Please try again later.';
        }
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
?>
