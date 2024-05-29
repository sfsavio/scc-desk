<?php
session_start(); // Start a new session or resume an existing session

// Function to load requests from the JSON file
function loadRequests() {
    $filename = 'requests.json';
    if (!file_exists($filename)) {
        return []; // Return an empty array if the file does not exist
    }
    $data = file_get_contents($filename); // Read the file contents
    return json_decode($data, true); // Decode the JSON data into a PHP array
}

// Function to save requests to the JSON file
function saveRequests($data) {
    $filename = 'requests.json';
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT)); // Encode and save the data
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php'); // Redirect to index.php if not logged in or not an admin
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id']) && isset($_POST['status'])) {
    $requests = loadRequests(); // Load existing requests
    $requestId = (int) $_POST['request_id'];
    $status = $_POST['status'];

    // Update the status of the specified request
    if (isset($requests[$requestId])) {
        $requests[$requestId]['status'] = $status;
        saveRequests($requests); // Save the updated requests array
        header('Location: manage_requests.php?success=Status atualizado com sucesso!');
        exit;
    } else {
        header('Location: manage_requests.php?error=Invalid request ID!');
        exit;
    }
}

$requests = loadRequests(); // Load existing requests
// $requests = array_reverse($requests);
 // Inverte a ordem do array para exibir os últimos itens no topo
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests</title>
</head>
<body>
    <h2>Gerenciar Chamados</h2>

    <?php
    if (isset($_GET['error'])) {
        echo '<p style="color: red;">' . htmlspecialchars($_GET['error']) . '</p>';
    } elseif (isset($_GET['success'])) {
        echo '<p style="color: green;">' . htmlspecialchars($_GET['success']) . '</p>';
    }
    ?>
    <?php if (empty($requests)): ?>
        <p>No requests available.</p>
    <?php else: ?>
        <table border="1" style="text-align: center; border: 1px solid black;">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Assunto</th>
                    <th>Setor</th>
                    <th>Descrição</th>
                    <th>Data e Hora</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $index => $request): ?>
                    <tr>
                        <td><?php echo $index; ?></td>
                        <td><?php echo ($request['assunto']); ?></td>
                        <!-- Not necessary in this case - htmlspecialchars() -->
                        <td><?php echo htmlspecialchars($request['setor']); ?></td> 
                        <td><?php echo htmlspecialchars($request['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($request['datetime']); ?></td>
                        <td><?php echo htmlspecialchars($request['username']); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                        <td>
                            <form action="manage_requests.php" method="post" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $index; ?>">
                                <select name="status">
                                    <option value="pendente" <?php if ($request['status'] == 'pendente') echo 'selected'; ?>>Pendente</option>
                                    <option value="solucionado" <?php if ($request['status'] == 'solucionado') echo 'selected'; ?>>Solucionado</option>
                                </select>
                                <input type="submit" value="Update">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <br>
    <a href="home.php">Home</a> <br>
    <a href="logout.php">Logout</a>
    <p> Chamados criados:
        <?php 
            echo $index+1;
         ?>
    </p>

    
</body>
</html>