<?php
// Example: Fetch orders with user contact number
$order_result = $conn->query("
    SELECT o.*, u.FName, u.ContactNo
    FROM tblorders o
    LEFT JOIN tblUser u ON o.user_name = u.User_name
    ORDER BY o.order_date DESC
");
?>
<!-- ...existing HTML... -->
<table id="ordersTable">
  <thead>
    <tr>
      <th>Order ID</th>
      <th>User Name</th>
      <th>Full Name</th>
      <th>Contact Number</th>
      <th>Service</th>
      <th>Quantity</th>
      <th>Amount</th>
      <th>Order Date</th>
      <th>Status</th>
      <!-- ...other columns if any... -->
    </tr>
  </thead>
  <tbody>
    <?php while ($order = $order_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($order['order_id']); ?></td>
      <td><?php echo htmlspecialchars($order['user_name']); ?></td>
      <td><?php echo htmlspecialchars($order['FName'] ?? ''); ?></td>
      <td><?php echo htmlspecialchars($order['ContactNo'] ?? '-'); ?></td>
      <td><?php echo htmlspecialchars($order['service_name'] ?? ''); ?></td>
      <td><?php echo htmlspecialchars($order['quantity'] ?? ''); ?></td>
      <td>
        <?php
          if (isset($order['total_amount']) && is_numeric($order['total_amount'])) {
            echo 'Rs.' . number_format((float)$order['total_amount'], 2);
          } else {
            echo '-';
          }
        ?>
      </td>
      <td><?php echo htmlspecialchars($order['order_date']); ?></td>
      <td><?php echo htmlspecialchars($order['status']); ?></td>
      <!-- ...other columns if any... -->
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
