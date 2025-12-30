<?php
session_start();

// Initialize tasks array
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Add task
if (isset($_POST['add_task']) && !empty($_POST['task'])) {
    $_SESSION['tasks'][] = [
        'id' => uniqid(),
        'title' => htmlspecialchars($_POST['task']),
        'priority' => $_POST['priority'] ?? 'medium',
        'completed' => false,
        'created_at' => date('Y-m-d H:i:s')
    ];
    header('Location: index.php');
    exit;
}

// Toggle task completion
if (isset($_GET['toggle'])) {
    foreach ($_SESSION['tasks'] as &$task) {
        if ($task['id'] == $_GET['toggle']) {
            $task['completed'] = !$task['completed'];
            break;
        }
    }
    header('Location: index.php');
    exit;
}

// Delete task
if (isset($_GET['delete'])) {
    $_SESSION['tasks'] = array_filter($_SESSION['tasks'], function ($task) {
        return $task['id'] != $_GET['delete'];
    });
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List App</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>📝 My Todo List</h1>

        <!-- Form Add Task -->
        <form method="POST" class="add-form">
            <input
                type="text"
                name="task"
                placeholder="Tambah task baru..."
                required
                autocomplete="off">
            <select name="priority" class="priority-select">
                <option value="high">🔴 High</option>
                <option value="medium" selected>🟡 Medium</option>
                <option value="low">🟢 Low</option>
            </select>
            <button type="submit" name="add_task">Tambah</button>
        </form>

        <!-- Task List -->
        <div class="task-list">
            <?php if (empty($_SESSION['tasks'])): ?>
                <p class="empty-state">Belum ada task. Yuk tambah task pertama! 🚀</p>
            <?php else: ?>
                <?php foreach ($_SESSION['tasks'] as $task): ?>
                    <div class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                        <div class="task-info">
                            <input
                                type="checkbox"
                                <?php echo $task['completed'] ? 'checked' : ''; ?>
                                onchange="window.location.href='?toggle=<?php echo $task['id']; ?>'">
                            <span class="task-title"><?php echo $task['title']; ?></span>
                            <span class="priority-badge priority-<?php echo $task['priority'] ?? 'medium'; ?>">
                                <?php
                                $icons = ['high' => '🔴', 'medium' => '🟡', 'low' => '🟢'];
                                echo $icons[$task['priority'] ?? 'medium'];
                                ?>
                            </span>
                            <span class="task-date"><?php echo date('d M Y, H:i', strtotime($task['created_at'])); ?></span>
                        </div>
                        <a href="?delete=<?php echo $task['id']; ?>" class="delete-btn" onclick="return confirm('Hapus task ini?')">
                            🗑️
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Stats -->
        <?php if (!empty($_SESSION['tasks'])): ?>
            <?php
            $total = count($_SESSION['tasks']);
            $completed = count(array_filter($_SESSION['tasks'], fn($t) => $t['completed']));
            $pending = $total - $completed;
            ?>
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-label">Total:</span>
                    <span class="stat-value"><?php echo $total; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Selesai:</span>
                    <span class="stat-value"><?php echo $completed; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pending:</span>
                    <span class="stat-value"><?php echo $pending; ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>