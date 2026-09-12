<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
require '../FPDF/fpdf.php';

$customer_id = $_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Only allow a customer to download a receipt for their OWN booking
$booking = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT b.id, b.booking_date, b.booking_time, b.status, b.created_at,
            s.service_name, s.price, s.duration,
            u.name as beautician_name, u.email as beautician_email,
            c.name as customer_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     JOIN users u ON bp.user_id = u.id
     JOIN users c ON b.customer_id = c.id
     WHERE b.id='$booking_id' AND b.customer_id='$customer_id'"));

if (!$booking) {
    die("Receipt not found or you do not have permission to view it.");
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header bar
$pdf->SetFillColor(26, 26, 46); // navy
$pdf->Rect(0, 0, 210, 30, 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 20);
$pdf->SetXY(15, 9);
$pdf->Cell(0, 12, 'BookMyBeauty', 0, 1);
$pdf->SetFont('Helvetica', '', 11);
$pdf->SetXY(15, 20);
$pdf->Cell(0, 6, 'Appointment Receipt', 0, 1);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetY(40);

$pdf->SetFont('Helvetica', 'B', 13);
$pdf->Cell(0, 8, 'Booking #' . $booking['id'], 0, 1);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(0, 6, 'Booked on: ' . date('d M Y, h:i A', strtotime($booking['created_at'])), 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(6);

// Status badge text
$status = ucfirst($booking['status']);
$pdf->SetFont('Helvetica', 'B', 10);
if ($booking['status'] == 'confirmed') { $pdf->SetTextColor(46, 125, 50); }
elseif ($booking['status'] == 'cancelled') { $pdf->SetTextColor(198, 40, 40); }
else { $pdf->SetTextColor(230, 81, 0); }
$pdf->Cell(0, 8, 'Status: ' . $status, 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(4);

// Details table
$rows = [
    ['Customer', $booking['customer_name']],
    ['Beautician', $booking['beautician_name']],
    ['Service', $booking['service_name']],
    ['Duration', $booking['duration'] . ' minutes'],
    ['Date', date('d M Y', strtotime($booking['booking_date']))],
    ['Time', date('h:i A', strtotime($booking['booking_time']))],
    ['Price', 'Rs. ' . number_format($booking['price'], 0)],
];

$pdf->SetFont('Helvetica', '', 11);
foreach ($rows as $row) {
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(50, 9, $row[0], 0, 0);
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->Cell(0, 9, $row[1], 0, 1);
}

$pdf->Ln(10);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(6);

$pdf->SetFont('Helvetica', 'I', 9);
$pdf->SetTextColor(130, 130, 130);
$pdf->MultiCell(0, 5, "This receipt confirms your appointment details on BookMyBeauty. Bookings can be cancelled within 2 hours of booking from the 'My Bookings' page. Payment is made in person at the time of your appointment.");

$pdf->Output('D', 'Receipt_Booking' . $booking['id'] . '.pdf');
?>