<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

require(__DIR__ . '/../assets/fpdf/fpdf.php');
include __DIR__ . '/../config/dbcon.php';

$order_id = intval($_GET['order_id'] ?? 0);
if (!$order_id) exit("Invalid order ID");

// --- Fetch order ---
$stmt = $conn->prepare("SELECT * FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) exit("Order not found");

// --- Fetch customer ---
$stmt = $conn->prepare("SELECT * FROM customers WHERE id=?");
$stmt->bind_param("i", $order['customer_id']);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
if (!$customer) exit("Customer not found");

// --- Fetch order items ---
$stmt = $conn->prepare("
    SELECT oi.*, i.name 
    FROM order_items oi 
    JOIN items i ON oi.item_id = i.id 
    WHERE oi.order_id=?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

// Calculate subtotal (before GST)
$subtotal = 0;
$itemsData = [];
while($row = $items->fetch_assoc()){
    $itemsData[] = $row;
    $subtotal += $row['total'];
}

// Calculate GST
$gstRate = 0.18; // 18% total GST
$cgst = $subtotal * ($gstRate / 2); // 9%
$sgst = $subtotal * ($gstRate / 2); // 9%
$grandTotal = $subtotal + $cgst + $sgst;

// --- Generate PDF ---
$pdf = new FPDF('P', 'mm', 'A4'); // Standard A4 size
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetMargins(20, 20, 20);

// Define colors matching the design system
$burgundy = array(99, 1, 22);      // #630116
$pink = array(175, 91, 115);       // #AF5B73
$cream = array(251, 249, 245);     // #FBF9F5
$grey = array(206, 195, 193);      // #CEC3C1
$darkText = array(42, 42, 42);     // #2a2a2a
$mutedText = array(106, 106, 106); // #6a6a6a

// --- HEADER SECTION ---
$pdf->SetFillColor($cream[0], $cream[1], $cream[2]);
$pdf->Rect(0, 0, 210, 50, 'F');

// Logo area (checkmark icon representation with text)
$pdf->SetXY(20, 20);
$pdf->SetFont('Helvetica', 'B', 24);
$pdf->SetTextColor($burgundy[0], $burgundy[1], $burgundy[2]);
$pdf->Cell(0, 10, 'Point of Sale', 0, 1, 'L');

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
$pdf->SetX(20);
$pdf->Cell(0, 6, 'Cloud Kitchen POS System', 0, 1, 'L');

// Invoice title and details (right side)
$pdf->SetXY(120, 20);
$pdf->SetFont('Helvetica', 'B', 20);
$pdf->SetTextColor($burgundy[0], $burgundy[1], $burgundy[2]);
$pdf->Cell(70, 10, 'INVOICE', 0, 1, 'R');

$pdf->SetXY(120, 32);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
$pdf->Cell(70, 5, 'Invoice #' . str_pad($order['id'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');

$pdf->SetXY(120, 38);
$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
$pdf->Cell(70, 5, 'Date: ' . date('d M Y', strtotime($order['order_time'])), 0, 1, 'R');

$pdf->Ln(10);

// --- CUSTOMER INFORMATION SECTION ---
$pdf->SetXY(20, 60);
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->SetTextColor($burgundy[0], $burgundy[1], $burgundy[2]);
$pdf->Cell(0, 7, 'BILL TO', 0, 1, 'L');

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
$pdf->SetX(20);
$pdf->Cell(0, 6, $customer['name'], 0, 1, 'L');

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
$pdf->SetX(20);
$pdf->Cell(0, 5, 'Email: ' . $customer['email'], 0, 1, 'L');

$pdf->SetX(20);
$pdf->Cell(0, 5, 'Mobile: ' . $customer['mobile'], 0, 1, 'L');

$pdf->Ln(8);

// --- ITEMS TABLE ---
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor($burgundy[0], $burgundy[1], $burgundy[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor($grey[0], $grey[1], $grey[2]);

// Table header - adjusted widths (total 170mm to fit in page)
$pdf->Cell(70, 8, 'ITEM', 1, 0, 'L', true);
$pdf->Cell(20, 8, 'QTY', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'PRICE', 1, 0, 'R', true);
$pdf->Cell(40, 8, 'TOTAL', 1, 1, 'R', true);

// Table rows
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
$fill = false;

foreach($itemsData as $item){
    if($fill){
        $pdf->SetFillColor($cream[0], $cream[1], $cream[2]);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }
    
    $pdf->Cell(70, 7, $item['name'], 1, 0, 'L', true);
    $pdf->Cell(20, 7, $item['quantity'], 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Rs. ' . number_format($item['price'], 2), 1, 0, 'R', true);
    $pdf->Cell(40, 7, 'Rs. ' . number_format($item['total'], 2), 1, 1, 'R', true);
    
    $fill = !$fill;
}

$pdf->Ln(3);

// --- TOTALS SECTION ---
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);

$rightColX = 130; // Start position for right column

// Subtotal
$pdf->SetX($rightColX);
$pdf->Cell(30, 6, 'Subtotal:', 0, 0, 'L');
$pdf->Cell(30, 6, 'Rs. ' . number_format($subtotal, 2), 0, 1, 'R');

// CGST
$pdf->SetX($rightColX);
$pdf->Cell(30, 6, 'CGST (9%):', 0, 0, 'L');
$pdf->Cell(30, 6, 'Rs. ' . number_format($cgst, 2), 0, 1, 'R');

// SGST
$pdf->SetX($rightColX);
$pdf->Cell(30, 6, 'SGST (9%):', 0, 0, 'L');
$pdf->Cell(30, 6, 'Rs. ' . number_format($sgst, 2), 0, 1, 'R');

$pdf->Ln(2);

// Grand Total with background
$pdf->SetFillColor($cream[0], $cream[1], $cream[2]);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetTextColor($burgundy[0], $burgundy[1], $burgundy[2]);
$pdf->SetX($rightColX);
$pdf->Cell(30, 8, 'TOTAL:', 0, 0, 'L', true);
$pdf->Cell(30, 8, 'Rs. ' . number_format($grandTotal, 2), 0, 1, 'R', true);

$pdf->Ln(5);

// --- PAYMENT DETAILS ---
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);

$pdf->SetX($rightColX);
$pdf->Cell(30, 6, 'Amount Paid:', 0, 0, 'L');
$pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
$pdf->Cell(30, 6, 'Rs. ' . number_format($order['amount_taken'], 2), 0, 1, 'R');

$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
$pdf->SetX($rightColX);
$pdf->Cell(30, 6, 'Change:', 0, 0, 'L');
$pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
$pdf->Cell(30, 6, 'Rs. ' . number_format($order['change_amount'], 2), 0, 1, 'R');

$pdf->Ln(15);

// --- FOOTER SECTION ---
$pdf->SetDrawColor($grey[0], $grey[1], $grey[2]);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
$pdf->Cell(0, 5, 'Thank you for your business!', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(0, 4, 'For any queries, please contact us at support@possystem.com', 0, 1, 'C');

$pdf->Ln(3);

// Company details footer
$pdf->SetFont('Helvetica', '', 8);
$pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
$pdf->Cell(0, 4, '123 Cloud Street, Tech City, India | +91 98765 43210', 0, 1, 'C');

// --- Output PDF ---
$pdf->Output('I', 'Invoice_' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . '.pdf');
?>