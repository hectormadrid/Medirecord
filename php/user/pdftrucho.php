<?php
$data = $_POST['data'];

if (!empty($_POST['data'])) {

    session_start();

    $_SESSION['PDFdata'] = $data;

    $response = array('success' => true, 'data' => $_SESSION['PDFdata']);
} else {
    $response = array('success' => false, 'message' => 'Usuario no encontrado');
}
echo json_encode($response);
