<?php
require_once 'config/database.php';

echo "<h2>Testing Admin Login</h2>";

// Test 1: Check if admin exists
$sql = "SELECT * FROM admins WHERE username = 'admin'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
    echo "<h3>✅ Admin Found:</h3>";
    echo "<pre>";
    echo "ID: " . $admin['id'] . "\n";
    echo "Username: " . $admin['username'] . "\n";
    echo "Email: " . $admin['email'] . "\n";
    echo "Full Name: " . $admin['full_name'] . "\n";
    echo "Password Hash: " . $admin['password'] . "\n";
    echo "</pre>";
    
    // Test 2: Verify password
    $test_password = 'admin123';
    echo "<h3>Testing Password: '$test_password'</h3>";
    
    if (password_verify($test_password, $admin['password'])) {
        echo "<p style='color: green;'>✅ Password verification SUCCESS!</p>";
    } else {
        echo "<p style='color: red;'>❌ Password verification FAILED!</p>";
        
        // Generate correct hash
        $correct_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "<h3>Creating New Hash:</h3>";
        echo "<p>Copy this hash to your database.sql file:</p>";
        echo "<input type='text' value='$correct_hash' style='width: 100%; padding: 10px;' onclick='this.select();'>";
        
        // Update database directly
        $update_sql = "UPDATE admins SET password = '$correct_hash' WHERE username = 'admin'";
        if (mysqli_query($conn, $update_sql)) {
            echo "<p style='color: green;'>✅ Database password UPDATED! Try logging in again.</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Admin user not found in database!</p>";
    echo "<p>Please run the database installer: <a href='config/install.php'>config/install.php</a></p>";
}

mysqli_close($conn);
?>
