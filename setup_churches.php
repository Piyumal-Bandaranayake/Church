<?php
include 'includes/db.php';

try {
    // 1. Create churches table
    $pdo->exec("CREATE TABLE IF NOT EXISTS churches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. The list of churches
    $churches = [
        "Anglican Church (Church of Ceylon)",
        "Antioch Fellowship Churches",
        "Apostolic Church",
        "Assemblies of God Sri Lanka (AOG)",
        "Baptist Church",
        "Believers' Church (GFA)",
        "Bethany Christian Life Centre",
        "Bethel Church",
        "Bethesda Hall Assembly Church",
        "Calvary Church",
        "Canaan Fellowship International Church",
        "Christian Fellowship Centre",
        "Christian Pentacostal Mission",
        "Christian Reformed Church",
        "Christ's Gospel Church (CGC)",
        "Church of Christ",
        "Colombo Gospel Tabernacle",
        "Cornerstone Church",
        "Emmanuel Church",
        "Faith Church",
        "Foursquare Gospel Churches",
        "Gethsemane Gospel Church",
        "Gethsemane Prayer Centre (GPC)",
        "Glorious Church",
        "Grace Evangelical Church",
        "Harvest Church",
        "House of Prayer Church",
        "House of Prayer Revival Church",
        "Jehovah's Witnesses",
        "Kingdom Hall Church",
        "Kings Revival Church",
        "Lighthouse Church",
        "Living Word Church",
        "Lutheran Church",
        "Methodist Church",
        "Miracle-centre churches",
        "Mount Zion Church",
        "New Covenant Church",
        "New Hope Church",
        "New Life Church",
        "People’s Church Assembly of God",
        "Prayer Tower Church",
        "Redeemed Churches",
        "Seventh-day Adventist Church",
        "The Christian Centre",
        "The Church of Jesus Christ of Latter-day Saints",
        "The Grace Evangelical Church",
        "The Salvation Army",
        "Trumpet of Revival Church",
        "Trumpet Sound Church",
        "Victory Life Church",
        "Worldwide Church of God",
        "Zion Christian Church",
        "Zion Christian Community Centre",
        "Zion Christian Fellowship Church",
        "Zion Church of God",
        "Zion Fountain Church",
        "INDEPENDENT / FREE CHURCH",
        "HOUSE - CHURCH"
    ];

    // 3. Insert churches
    $stmt = $pdo->prepare("INSERT IGNORE INTO churches (name) VALUES (?)");
    foreach ($churches as $church) {
        $stmt->execute([trim($church)]);
    }

    echo "<h1>Churches Table Setup Complete!</h1>";
    echo "<p>Table created and " . count($churches) . " churches added to the database.</p>";
    echo "<a href='register.php' style='padding: 10px 20px; background: #004aad; color: white; text-decoration: none; border-radius: 10px; font-weight: bold;'>Back to Registration</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
