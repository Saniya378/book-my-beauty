<?php

namespace App\Services;

use App\Models\Booking;

class ReceiptPdfService
{
    public function generateReceipt(Booking $booking): string
    {
        require_once __DIR__ . '/FPDF/fpdf.php';

        $pdf = new \FPDF('P', 'mm', 'A4');
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
        $pdf->Cell(0, 8, 'Booking #' . $booking->id, 0, 1);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(120, 120, 120);
        $bookingCreatedAt = $booking->created_at ? $booking->created_at->format('d M Y, h:i A') : 'N/A';
        $pdf->Cell(0, 6, 'Booked on: ' . $bookingCreatedAt, 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(6);

        // Status badge text
        $status = ucfirst($booking->status);
        $pdf->SetFont('Helvetica', 'B', 10);
        if ($booking->status === 'confirmed') {
            $pdf->SetTextColor(46, 125, 50);
        } elseif ($booking->status === 'cancelled') {
            $pdf->SetTextColor(198, 40, 40);
        } else {
            $pdf->SetTextColor(230, 81, 0);
        }
        $pdf->Cell(0, 8, 'Status: ' . $status, 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(4);

        $customerName = $booking->customer->name ?? 'Customer';
        $beauticianName = $booking->service->beautician->user->name ?? 'Beautician';
        $serviceName = $booking->service->service_name ?? 'Service';
        $duration = ($booking->service->duration ?? 0) . ' minutes';
        $date = $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A';
        $time = $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : 'N/A';
        $price = 'Rs. ' . number_format($booking->service->price ?? 0, 0);

        // Details table
        $rows = [
            ['Customer', $customerName],
            ['Beautician', $beauticianName],
            ['Service', $serviceName],
            ['Duration', $duration],
            ['Date', $date],
            ['Time', $time],
            ['Price', $price],
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

        return $pdf->Output('S');
    }
}
