<?php
// input.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Transaction Input</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <h2>Simple Transaction System (PHP Forms & Functions)</h2> <br>
  <!-- <p class="hint">Fill out the form then submit. The processing page computes totals using PHP functions.<br>
  <em>Note: Names are auto-formatted to title case. Minimum 2 characters required.</em></p> -->

  <form action="process.php" method="POST">
    <label for="customer">Customer Name</label>
    <input type="text" name="customer" id="customer" placeholder="e.g., Juan Dela Cruz" minlength="2" required>

    <label for="product">Product Name</label>
    <input type="text" name="product" id="product" placeholder="e.g., Burger Meal" minlength="2" required>

    <label for="price">Unit Price (PHP)</label>
    <input type="number" name="price" id="price" step="0.01" min="0" placeholder="e.g., 99.50" required>

    <label for="qty">Quantity</label>
    <input type="number" name="qty" id="qty" min="1" placeholder="e.g., 2" required>

    <label for="discount">Discount Type</label>
    <select name="discount" id="discount" required>
      <option value="none">No Discount</option>
      <option value="student">Student (10%)</option>
      <option value="senior">Senior Citizen (20%)</option>
    </select>

    <label for="payment">Cash Payment (PHP)</label>
    <input type="number" name="payment" id="payment" step="0.01" min="0" placeholder="e.g., 300" required>

    <button type="submit">Process Transaction</button>
  </form>

</body>
</html>