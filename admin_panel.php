<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// Define categories
$categories = ['Bracelets','Earrings','Rings','Pendants'];

// Check if columns exist in orders table
$ordersColumns = [];
$resCols = $conn->query("SHOW COLUMNS FROM orders");
while($col = $resCols->fetch_assoc()){
    $ordersColumns[] = $col['Field'];
}

// Handle POST updates (status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Product status update
    if (isset($_POST['product_id'], $_POST['status'])) {
        $stmt = $conn->prepare("UPDATE products SET status=? WHERE id=?");
        $stmt->bind_param("si", $_POST['status'], $_POST['product_id']);
        $stmt->execute();
        $stmt->close();
        echo "<script>window.location='?page=products';</script>";
        exit;
    }
    // Order status update
    if (isset($_POST['order_id'], $_POST['order_status'])) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $_POST['order_status'], $_POST['order_id']);
        $stmt->execute();
        $stmt->close();
        echo "<script>window.location='?page=orders';</script>";
        exit;
    }
}

// Handle GET actions (delete, edit)
$page = $_GET['page'] ?? 'dashboard';

// DELETE PRODUCT
if ($page === 'delete_product' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Delete related cart items first
    $stmt_cart = $conn->prepare("DELETE FROM cart WHERE product_id=?");
    $stmt_cart->bind_param("i", $product_id);
    $stmt_cart->execute();
    $stmt_cart->close();

    // Delete product image
    $res = $conn->prepare("SELECT image,gallery_images FROM products WHERE id=?");
    $res->bind_param("i", $product_id);
    $res->execute();
    $res_result = $res->get_result();
    if($res_result->num_rows > 0){
        $row = $res_result->fetch_assoc();
        if(!empty($row['image']) && file_exists("images/".$row['image'])){
            unlink("images/".$row['image']);
        }
        if(!empty($row['gallery_images'])){
            $gals = json_decode($row['gallery_images'], true);
            foreach($gals as $gimg){
                if(file_exists("images/".$gimg)) unlink("images/".$gimg);
            }
        }
    }
    $res->close();

    // Delete product from DB
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Product deleted successfully!');window.location='?page=products';</script>";
    exit;
}

// EDIT PRODUCT
if ($page === 'edit_product' && isset($_GET['id'])) {
    $edit_id = (int)$_GET['id'];
    $res = $conn->prepare("SELECT * FROM products WHERE id=?");
    $res->bind_param("i", $edit_id);
    $res->execute();
    $edit_result = $res->get_result();
    $product = $edit_result->fetch_assoc();
    $res->close();

    // Handle edit POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['price'], $_POST['category'])) {
        $name = trim($_POST['name']);
        $price = (float) $_POST['price'];
        $status = $_POST['status'] ?? 'Available';
        $category = $_POST['category'] ?? 'Uncategorized';
        $description = $_POST['description'] ?? null;

        // Handle main image update
        $image = $product['image'];
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
            if(!empty($image) && file_exists("images/".$image)){
                unlink("images/".$image);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'prod_'.time().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'images/'.$image);
        }

        // Handle gallery images update (append new ones)
        $gallery_images = [];
        if (!empty($product['gallery_images'])) {
            $gallery_images = json_decode($product['gallery_images'], true);
        }

        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['name'] as $key => $filename) {
                if ($_FILES['gallery_images']['error'][$key] === 0) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $gname = 'gallery_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], 'images/' . $gname)) {
                        $gallery_images[] = $gname;
                    }
                }
            }
        }

        $gallery_json = !empty($gallery_images) ? json_encode($gallery_images) : null;

        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image=?, status=?, category=?, description=?, gallery_images=? WHERE id=?");
        $stmt->bind_param("sdsssssi", $name, $price, $image, $status, $category, $description, $gallery_json, $edit_id);
        if($stmt->execute()){
            echo "<script>alert('Product updated successfully!');window.location='?page=products';</script>";
        } else {
            echo "<p class='w3-text-red'>❌ Error: ".$stmt->error."</p>";
        }
        $stmt->close();
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Jewelkart</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body { background-color: #000; color: #fff; }
        table img { max-width: 50px; }
        .w3-card { background-color: #fff; color: #000; }
        .w3-select { width: 100%; }
    </style>
</head>
<body>

<div class="w3-container w3-padding-16">
    <h2 class="w3-center">⚡ Jewelkart Admin Panel</h2>

   <div class="w3-bar w3-margin-bottom">
    <a href="?page=products" class="w3-button w3-white w3-text-black w3-margin-right">Products</a>
    <a href="?page=orders" class="w3-button w3-white w3-text-black w3-margin-right">Orders</a>
    <a href="?page=users" class="w3-button w3-white w3-text-black w3-margin-right">Users</a>
    <a href="?page=messages" class="w3-button w3-white w3-text-black w3-margin-right">Query</a>
    <a href="admin_logout.php" class="w3-button w3-red w3-right">Logout</a>
</div>

<div class="w3-container w3-white w3-text-black w3-padding w3-round-large">
<?php
// ADD PRODUCT
if ($page === 'add_product') {
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['price'], $_POST['category'], $_FILES['image'])) {
        $name = trim($_POST['name']);
        $price = (float) $_POST['price'];
        $status = $_POST['status'] ?? 'Available';
        $category = $_POST['category'] ?? 'Uncategorized';
        $description = $_POST['description'] ?? null;
        $image = null;
        $gallery_images = [];

        // Main image
        if ($_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'images/' . $image);
        }

        // Gallery images (multiple)
        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['name'] as $key => $filename) {
                if ($_FILES['gallery_images']['error'][$key] === 0) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $gname = 'gallery_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], 'images/' . $gname)) {
                        $gallery_images[] = $gname;
                    }
                }
            }
        }

        $gallery_json = !empty($gallery_images) ? json_encode($gallery_images) : null;

        $stmt = $conn->prepare("INSERT INTO products (name, price, image, status, category, description, gallery_images) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsssss", $name, $price, $image, $status, $category, $description, $gallery_json);
        if ($stmt->execute()) {
            $msg = "<p class='w3-text-green'>✅ Product added successfully!</p>";
        } else {
            $msg = "<p class='w3-text-red'>❌ Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
?>
<h3>Add New Product</h3>
<?php if ($msg) echo $msg; ?>
<form method="POST" enctype="multipart/form-data" class="w3-container">
    <p><label>Name</label><input class="w3-input w3-border" type="text" name="name" required></p>
    <p><label>Price</label><input class="w3-input w3-border" type="number" step="0.01" name="price" required></p>
    <p><label>Main Image</label><input class="w3-input w3-border" type="file" name="image" required></p>
    <p><label>Gallery Images (Multiple)</label><input class="w3-input w3-border" type="file" name="gallery_images[]" multiple></p>
    <p><label>Description</label><textarea class="w3-input w3-border" name="description" rows="5"></textarea></p>
    <p><label>Status</label>
        <select class="w3-select w3-border" name="status">
            <option value="Available">Available</option>
            <option value="Out of Stock">Out of Stock</option>
        </select>
    </p>
    <p><label>Category</label>
        <select class="w3-select w3-border" name="category">
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><button type="submit" class="w3-button w3-green">Add Product</button></p>
</form>
<?php
}

// EDIT PRODUCT
elseif ($page === 'edit_product' && isset($product)) { ?>
<h3>Edit Product</h3>
<form method="POST" enctype="multipart/form-data" class="w3-container">
    <p><label>Name</label><input class="w3-input w3-border" type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required></p>
    <p><label>Price</label><input class="w3-input w3-border" type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required></p>

    <p><label>Main Image</label>
        <?php if (!empty($product['image'])): ?>
            <img src="images/<?php echo $product['image']; ?>" style="max-width:100px;"><br>
        <?php endif; ?>
        <input class="w3-input w3-border" type="file" name="image">
    </p>

    <p><label>Gallery Images (Multiple)</label><br>
        <?php if (!empty($product['gallery_images'])):
            $gallery = json_decode($product['gallery_images'], true);
            foreach ($gallery as $img): ?>
                <img src="images/<?php echo $img; ?>" style="max-width:80px; margin:5px;">
            <?php endforeach;
        endif; ?>
        <input class="w3-input w3-border" type="file" name="gallery_images[]" multiple>
    </p>

    <p><label>Description</label>
        <textarea class="w3-input w3-border" name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
    </p>

    <p><label>Status</label>
        <select class="w3-select w3-border" name="status">
            <option value="Available" <?php echo ($product['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
            <option value="Out of Stock" <?php echo ($product['status'] == 'Out of Stock') ? 'selected' : ''; ?>>Out of Stock</option>
        </select>
    </p>

    <p><label>Category</label>
        <select class="w3-select w3-border" name="category">
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo ($product['category'] == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><button type="submit" class="w3-button w3-blue">Update Product</button></p>
</form>
<?php }

// PRODUCTS LIST
elseif ($page === 'products') {
    echo "<h3>Products</h3>";
    echo "<a href='?page=add_product' class='w3-button w3-green w3-small w3-margin-bottom'>Add New Product</a>";
    $res = $conn->query("SELECT * FROM products");
    echo "<table class='w3-table w3-bordered w3-striped'>
            <tr class='w3-black'>
                <th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Status</th><th>Category</th><th>Actions</th>
            </tr>";
    while($row = $res->fetch_assoc()) {
        $status = $row['status'] ?? 'Available';
        $category = $row['category'] ?? 'Uncategorized';
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>₹{$row['price']}</td>
                <td><img src='images/{$row['image']}'></td>
                <td>
                    <form method='POST'>
                        <input type='hidden' name='product_id' value='{$row['id']}'>
                        <select name='status' onchange='this.form.submit()'>
                            <option value='Available' ".($status=='Available'?'selected':'').">Available</option>
                            <option value='Out of Stock' ".($status=='Out of Stock'?'selected':'').">Out of Stock</option>
                        </select>
                    </form>
                </td>
                <td>{$category}</td>
                <td>
                    <a href='?page=edit_product&id={$row['id']}' class='w3-button w3-blue w3-small'>Edit</a>
                    <a href='?page=delete_product&id={$row['id']}' class='w3-button w3-red w3-small' onclick=\"return confirm('Are you sure?');\">Delete</a>
                </td>
              </tr>";
    }
    echo "</table>";
}
// ORDERS
// ORDERS
elseif ($page === 'orders') {
    echo "<h3>Orders</h3>";

    // Handle order deletion
    if (isset($_GET['delete_order'])) {
        $order_id = (int) $_GET['delete_order'];

        // Delete order items first if table exists
        if ($conn->query("SHOW TABLES LIKE 'order_items'")->num_rows) {
            $conn->query("DELETE FROM order_items WHERE order_id=$order_id");
        }

        // Delete the order
        $conn->query("DELETE FROM orders WHERE id=$order_id");

        echo "<script>alert('Order deleted successfully!'); window.location='?page=orders';</script>";
        exit;
    }

    // Fetch orders with user info
    $res = $conn->query("
        SELECT o.id, o.user_id, o.total, o.status, o.created_at, 
               u.username, ui.address, ui.city, ui.state, ui.pincode, ui.payment_method
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN user_info ui ON o.user_id = ui.user_id
        ORDER BY o.id DESC
    ");

    if($res->num_rows === 0) {
        echo "<p>No orders yet.</p>";
    } else {
        echo "<table class='w3-table w3-bordered w3-striped'>
                <tr class='w3-black'>
                    <th>ID</th>
                    <th>User</th>
                    <th>Total (₹)</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Address</th>
                    <th>Placed On</th>
                    <th>Actions</th>
                </tr>";

        while($row = $res->fetch_assoc()){
            $order_status = $row['status'] ?? 'Pending';
            $full_address = trim("{$row['address']}, {$row['city']}, {$row['state']} - {$row['pincode']}");

            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>₹{$row['total']}</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='order_id' value='{$row['id']}'>
                            <select name='order_status' onchange='this.form.submit()'>
                                <option value='Pending' ".($order_status=='Pending'?'selected':'').">Pending</option>
                                <option value='Processing' ".($order_status=='Processing'?'selected':'').">Processing</option>
                                <option value='Shipped' ".($order_status=='Shipped'?'selected':'').">Shipped</option>
                                <option value='Delivered' ".($order_status=='Delivered'?'selected':'').">Delivered</option>
                                <option value='Cancelled' ".($order_status=='Cancelled'?'selected':'').">Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td>{$row['payment_method']}</td>
                    <td>{$full_address}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <a href='?page=orders&delete_order={$row['id']}' 
                           class='w3-button w3-red w3-small' 
                           onclick=\"return confirm('Are you sure you want to delete this order?')\">Delete</a>
                    </td>
                  </tr>";
        }

        echo "</table>";
    }
}
// USERS
elseif ($page === 'users') {
    echo "<h3>Users</h3>";

    if (isset($_GET['delete_user'])) {
        $uid = (int) $_GET['delete_user'];
        $conn->query("DELETE FROM cart WHERE user_id=$uid");
        $conn->query("DELETE oi FROM order_items oi INNER JOIN orders o ON oi.order_id=o.id WHERE o.user_id=$uid");
        $conn->query("DELETE FROM orders WHERE user_id=$uid");
        $conn->query("DELETE FROM users WHERE id=$uid");
        echo "<script>alert('User deleted successfully!'); window.location.href='admin_panel.php?page=users';</script>";
    }

    if (isset($_POST['update_user'])) {
        $uid = (int) $_POST['id'];
        $username = trim($conn->real_escape_string($_POST['username']));
        $email = trim($conn->real_escape_string($_POST['email']));

        $old = $conn->query("SELECT username, email FROM users WHERE id=$uid")->fetch_assoc();

        if ($old['username'] !== $username || $old['email'] !== $email) {
            $conn->query("UPDATE users SET username='$username', email='$email' WHERE id=$uid");
            echo "<script>alert('User updated successfully!'); window.location.href='admin_panel.php?page=users';</script>";
        } else {
            echo "<script>alert('No changes detected!'); window.location.href='admin_panel.php?page=users';</script>";
        }
    }

    $res = $conn->query("SELECT * FROM users");
    echo "<table class='w3-table w3-bordered w3-striped'>
            <tr class='w3-black'>
              <th>ID</th><th>Username</th><th>Email</th><th>Actions</th>
            </tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr>
                <form method='POST'>
                  <td>{$row['id']}<input type='hidden' name='id' value='{$row['id']}'></td>
                  <td><input class='w3-input w3-border' type='text' name='username' value='{$row['username']}' required></td>
                  <td><input class='w3-input w3-border' type='email' name='email' value='{$row['email']}' required></td>
                  <td>
                    <button type='submit' name='update_user' class='w3-button w3-blue w3-small'>Update</button>
                    <a href='admin_panel.php?page=users&delete_user={$row['id']}' class='w3-button w3-red w3-small' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                  </td>
                </form>
              </tr>";
    }
    echo "</table>";
}

// MESSAGES
elseif ($page === 'messages') {
    echo "<h3>Customer Queries & Complaints</h3>";
    $res = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");

    if ($res->num_rows > 0) {
        echo "<table class='w3-table w3-bordered w3-striped'>
                <tr class='w3-black'>
                  <th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th>
                </tr>";
        while ($row = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['message']}</td>
                    <td>{$row['created_at']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No messages received yet.</p>";
    }
}

// DASHBOARD
else {
    echo "<h3>Welcome, Admin!</h3>";
    echo "<p>Use the menu above to manage products, orders, and users.</p>";
}
?>
</div>
</div>
</body>
</html>
