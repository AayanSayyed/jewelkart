<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/setasign/fpdf/fpdf.php';

// DB Connection
$conn = new mysqli("localhost", "root", "", "jewelkartt");
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

// Require login
if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

// Validate order
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid Order ID.");
}
$order_id = (int) $_GET['order_id'];

// Fetch order + user info
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
if (!$order) die("Order not found");

$user = $conn->query("SELECT * FROM user_info WHERE user_id={$order['user_id']}")->fetch_assoc();
$items = $conn->query("SELECT oi.*, p.name 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id=p.id 
                       WHERE oi.order_id=$order_id");

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Company Logo
if (file_exists("images/gm_logo_jk.png")) {
    $pdf->Image("images/gm_logo_jk.png",10,8,30); 
}

// Company Info
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"JewelKart",0,1,"R");
$pdf->SetFont("Arial","",10);
$pdf->Cell(0,5,"123 Fashion Street, Pune, India",0,1,"R");
$pdf->Cell(0,5,"Email: support@jewelkart.com | +91 98765 43210",0,1,"R");
$pdf->Ln(15);

// Invoice Title
$pdf->SetFont("Arial","B",16);
$pdf->Cell(0,10,"INVOICE",0,1,"C");
$pdf->Ln(5);

// Invoice Info
$pdf->SetFont("Arial","",12);
$pdf->Cell(100,8,"Invoice #: JK-" . str_pad($order_id, 4, "0", STR_PAD_LEFT),0,0);
$pdf->Cell(90,8,"Date: " . date("d M Y", strtotime($order['created_at'])),0,1);
$pdf->Ln(5);

// Customer Info
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,8,"Billing Details",0,1);
$pdf->SetFont("Arial","",11);
$pdf->MultiCell(0,6,
    $user['full_name']."\n".
    $user['address']."\n".
    $user['city'].", ".$user['state']." - ".$user['pincode']
);
$pdf->Ln(5);

// Table Header
$pdf->SetFont("Arial","B",11);
$pdf->Cell(90,8,"Product",1);
$pdf->Cell(30,8,"Qty",1,0,"C");
$pdf->Cell(30,8,"Price",1,0,"R");
$pdf->Cell(40,8,"Total",1,1,"R");

// Table Rows
$pdf->SetFont("Arial","",11);
$grand_total = 0;
while ($row = $items->fetch_assoc()) {
    $total = $row['price'] * $row['quantity'];
    $grand_total += $total;
    $pdf->Cell(90,8,$row['name'],1);
    $pdf->Cell(30,8,$row['quantity'],1,0,"C");
    $pdf->Cell(30,8,"Rs.".number_format($row['price'],2),1,0,"R");
    $pdf->Cell(40,8,"Rs.".number_format($total,2),1,1,"R");
}

// Grand Total
$pdf->SetFont("Arial","B",12);
$pdf->Cell(150,10,"Grand Total",1);
$pdf->Cell(40,10,"Rs.".number_format($grand_total,2),1,1,"R");

$pdf->Ln(10);
$pdf->SetFont("Arial","I",10);
$pdf->Cell(0,10,"Thank you for shopping with JewelKart!",0,1,"C");

// Output PDF
$pdf->Output("I", "Invoice_JK$order_id.pdf");
?>
