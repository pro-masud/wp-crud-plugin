<?php
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_data = null;

if ($edit_id) {
    $edit_data = $wpdb->get_row("SELECT * FROM $table WHERE id = $edit_id", ARRAY_A);
}
?>

<div class="mj-container">
    <h2>MJ Custom CRUD</h2>

    <form method="post" action="<?= admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="mj_save_data">
        <input type="hidden" name="id" value="<?= esc_attr($edit_data['id'] ?? '') ?>">

        <input type="text" name="name" placeholder="Name" required
               value="<?= esc_attr($edit_data['name'] ?? '') ?>">

        <input type="email" name="email" placeholder="Email" required
               value="<?= esc_attr($edit_data['email'] ?? '') ?>">

        <button type="submit"><?= $edit_data ? 'Update' : 'Save' ?></button>
    </form>

    <hr>

    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= esc_html($row['id']) ?></td>
                    <td><?= esc_html($row['name']) ?></td>
                    <td><?= esc_html($row['email']) ?></td>
                    <td>
                        <a href="?page=mj-crud&edit=<?= $row['id'] ?>">Edit</a> |
                        <a href="<?= admin_url('admin-post.php?action=mj_delete_data&id=' . $row['id']) ?>" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
