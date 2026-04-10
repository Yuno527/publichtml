<?php

$prompt = $_POST["prompt"];

// 1) Llamar al servidor Flask
$ch = curl_init("http://localhost:5000/api/ai");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["prompt" => $prompt]));

$response = curl_exec($ch);
curl_close($ch);

// Convertir en array
$ia = json_decode($response, true);

// Manejar la respuesta según formato
if (isset($ia["text"])) {
    $texto_generado = $ia["text"]; // formato Flask Opción 1
} elseif (isset($ia[0]["generated_text"])) {
    $texto_generado = $ia[0]["generated_text"]; // formato HuggingFace
} else {
    $texto_generado = "No se pudo generar análisis con IA.";
}

// 2) Guardar en la tabla correcta
include "conexion.php";

$stmt = $conn->prepare("
    INSERT INTO tbl_resultados (Id_historiaLFK, puntaje_total, resultado_final, fecha_registro) 
    VALUES (?, ?, ?, NOW())
");

$stmt->bind_param("iis", $historialId, $totalScore, $texto_generado); 
$stmt->execute();

// 3) Devolver al frontend
echo json_encode([
    "ok" => true,
    "analysis" => $texto_generado
]);
