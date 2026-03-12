<?php
// process.php
//
// PHP Built-in Functions Used:
//  1. strlen()        – validates minimum character length of text inputs
//  2. ucwords()       – formats the customer name to title case
//  3. round()         – rounds monetary values to 2 decimal places
//  4. strtolower()    – normalises discount type for matching
//  5. strtoupper()    – displays discount type in uppercase on the receipt
//  6. number_format() – formats amounts to 2 decimal places with commas
//  7. date()          – generates the transaction timestamp

function sanitize_text(string $value): string {
  return trim(strip_tags($value)); // built-in: trim, strip_tags
}

// built-in: ucwords — converts the first letter of each word to uppercase
function format_name(string $value): string {
  return ucwords(strtolower($value));
}

function compute_subtotal(float $price, int $qty): float {
  return $price * $qty;
}

function discount_rate(string $type): float {
  $type = strtolower($type); // built-in: strtolower

  return match ($type) {
    'student' => 0.10,
    'senior'  => 0.20,
    default   => 0.00
  };
}

function compute_discount(float $subtotal, float $rate): float {
  return $subtotal * $rate;
}

function compute_total(float $subtotal, float $discountAmount): float {
  return round($subtotal - $discountAmount, 2); // built-in: round — ensures exact 2-decimal precision
}

function compute_change(float $payment, float $total): float {
  return round($payment - $total, 2); // built-in: round — avoids floating-point drift on change
}

function format_money(float $amount): string {
  return "₱" . number_format($amount, 2); // built-in: number_format
}

// -------------------- POST GUARD --------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: input.php');
  exit;
}

// -------------------- INPUTS --------------------
$customer     = format_name(sanitize_text($_POST['customer'] ?? ''));   // ucwords applied
$product      = sanitize_text($_POST['product'] ?? '');
$discountType = sanitize_text($_POST['discount'] ?? 'none');

$price   = (float)($_POST['price'] ?? 0);
$qty     = (int)($_POST['qty'] ?? 0);
$payment = (float)($_POST['payment'] ?? 0);

// -------------------- VALIDATION --------------------
$errors = [];

// built-in: strlen — checks that names are not too short after sanitizing
if (strlen($customer) < 2) $errors[] = 'Customer name must be at least 2 characters.';
if (strlen($product)  < 2) $errors[] = 'Product name must be at least 2 characters.';
if ($price <= 0) $errors[] = 'Unit price must be greater than 0.';
if ($qty <= 0)   $errors[] = 'Quantity must be at least 1.';
if ($payment < 0) $errors[] = 'Payment cannot be negative.';

// -------------------- COMPUTATIONS --------------------
$subtotal       = compute_subtotal($price, $qty);
$rate           = discount_rate($discountType);
$discountAmount = compute_discount($subtotal, $rate);
$total          = compute_total($subtotal, $discountAmount);
$change         = compute_change($payment, $total);

if ($payment < $total) {
  $errors[] = 'Insufficient payment. Please enter an amount equal to or greater than the total.';
}

$transactionDate = date("F j, Y, g:i a"); // built-in: date
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Transaction Result</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <h2>Transaction Processing Output</h2>
  <p class="hint"><em>Transaction Date:</em> <?php echo htmlspecialchars($transactionDate); ?></p>

  <?php if (!empty($errors)): ?>
    <div class="error">
      <strong>Transaction Failed:</strong>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <a class="button-link" href="input.php">Go Back</a>

  <?php else: ?>
    <div class="card">
      <div class="row"><strong>Customer</strong><span><?php echo htmlspecialchars($customer); ?></span></div>
      <div class="row"><strong>Product</strong><span><?php echo htmlspecialchars($product); ?></span></div>
      <div class="row"><strong>Unit Price</strong><span><?php echo format_money($price); ?></span></div>
      <div class="row"><strong>Quantity</strong><span><?php echo htmlspecialchars((string)$qty); ?></span></div>

      <hr>

      <div class="row"><strong>Subtotal</strong><span><?php echo format_money($subtotal); ?></span></div>
      <div class="row">
        <strong>Discount</strong>
        <span><?php echo htmlspecialchars(strtoupper($discountType)); ?> (<?php echo (int)($rate * 100); ?>%)</span>
      </div>
      <div class="row"><strong>Discount Amount</strong><span><?php echo format_money($discountAmount); ?></span></div>

      <hr>

      <div class="row"><strong>Total Due</strong><span><?php echo format_money($total); ?></span></div>
      <div class="row"><strong>Cash Payment</strong><span><?php echo format_money($payment); ?></span></div>
      <div class="row"><strong>Change</strong><span><?php echo format_money($change); ?></span></div>
    </div>

    <a class="button-link" href="input.php">New Transaction</a>
  <?php endif; ?>

</body>
</html>