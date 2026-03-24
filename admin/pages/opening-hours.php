<?php
$pageTitle = 'Opening Hours';
$currentPage = 'hours';
require_once __DIR__ . '/../includes/header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = $_POST['day'] ?? [];
    foreach ($days as $id => $data) {
        $isClosed = isset($data['is_closed']) ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE opening_hours SET collection_open = ?, collection_close = ?, delivery_open = ?, delivery_close = ?, is_closed = ? WHERE id = ?");
        $stmt->execute([
            $data['collection_open'],
            $data['collection_close'],
            $data['delivery_open'],
            $data['delivery_close'],
            $isClosed,
            (int)$id
        ]);
    }
    $success = 'Opening hours saved!';
}

$hours = $pdo->query("SELECT * FROM opening_hours ORDER BY sort_order ASC")->fetchAll();
?>

<h1>Opening Hours</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Collection Open</th>
                    <th>Collection Close</th>
                    <th>Delivery Open</th>
                    <th>Delivery Close</th>
                    <th>Closed</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hours as $h): ?>
                <tr>
                    <td><strong><?= sanitize($h['day_name']) ?></strong></td>
                    <td><input type="time" name="day[<?= $h['id'] ?>][collection_open]" value="<?= substr($h['collection_open'],0,5) ?>" class="form-control-sm"></td>
                    <td><input type="time" name="day[<?= $h['id'] ?>][collection_close]" value="<?= substr($h['collection_close'],0,5) ?>" class="form-control-sm"></td>
                    <td><input type="time" name="day[<?= $h['id'] ?>][delivery_open]" value="<?= substr($h['delivery_open'],0,5) ?>" class="form-control-sm"></td>
                    <td><input type="time" name="day[<?= $h['id'] ?>][delivery_close]" value="<?= substr($h['delivery_close'],0,5) ?>" class="form-control-sm"></td>
                    <td><input type="checkbox" name="day[<?= $h['id'] ?>][is_closed]" <?= $h['is_closed'] ? 'checked' : '' ?>></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-save">Save</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
