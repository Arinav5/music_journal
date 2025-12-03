<?php
require 'app/db.php';

$search_term = '';
$results = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_term = $_GET['q'];

    $stmt = $mysqli->prepare("
        SELECT 'Genre' AS type, name AS result FROM genres WHERE name LIKE CONCAT('%', ?, '%')
        UNION
        SELECT 'Artist' AS type, name AS result FROM artists WHERE name LIKE CONCAT('%', ?, '%')
        UNION
        SELECT 'User' AS type, username AS result FROM users WHERE username LIKE CONCAT('%', ?, '%')
    ");
    $stmt->bind_param('sss', $search_term, $search_term, $search_term);
    $stmt->execute();
    $query_result = $stmt->get_result();

    while ($row = $query_result->fetch_assoc()) {
        $results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discovery Page</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        input[type="text"] { width: 300px; padding: 5px; }
        button { padding: 5px 10px; }
        ul { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Discovery Page</h1>

    <form method="GET" action="">
        <input type="text" name="q" placeholder="Search genres, artists, users..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit">Search</button>
    </form>

    <h2>Results:</h2>
    <ul>
        <?php
        if (!empty($results)) {
            foreach ($results as $row) {
                echo '<li>' . $row['type'] . ': ' . htmlspecialchars($row['result']) . '</li>';
            }
        } else if ($search_term !== '') {
            echo '<li>No results found for "' . htmlspecialchars($search_term) . '"</li>';
        }
        ?>
    </ul>
</body>
</html>