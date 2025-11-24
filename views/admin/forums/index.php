<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Forum Management</h1>
        <div class="flex gap-2">
            <a href="/admin/forums/sections" class="btn btn-secondary">Manage Sections</a>
            <a href="/admin/forums/create" class="btn btn-primary">Create Forum</a>
        </div>
    </div>

    <?php if (empty($forums) && empty($sections)): ?>
        <div class="card">
            <p class="text-gray-600 text-center py-8">No forums found. Create a section first, then create forums.</p>
        </div>
    <?php else: ?>
        <!-- Sections Summary -->
        <?php if (!empty($sections)): ?>
            <div class="card mb-6">
                <h2 class="text-xl font-bold mb-4">Sections</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Sort Order</th>
                                <th>Min Read Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sections as $section): ?>
                                <tr>
                                    <td><?= $section['id'] ?></td>
                                    <td class="font-semibold"><?= htmlspecialchars($section['name']) ?></td>
                                    <td><?= htmlspecialchars($section['description'] ?? '') ?></td>
                                    <td><?= $section['sort_order'] ?></td>
                                    <td><?= $section['minclassread'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Forums List -->
        <div class="card">
            <h2 class="text-xl font-bold mb-4">Forums</h2>
            <?php if (empty($forums)): ?>
                <p class="text-gray-600 text-center py-8">No forums found.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Section</th>
                                <th>Parent</th>
                                <th>Description</th>
                                <th>Min Read</th>
                                <th>Min Write</th>
                                <th>Min Create</th>
                                <th>Sort</th>
                                <th>Topics</th>
                                <th>Posts</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forums as $forum): ?>
                                <tr>
                                    <td><?= $forum['id'] ?></td>
                                    <td class="font-semibold">
                                        <?php if ($forum['parent_id']): ?>
                                            <span class="text-gray-500">└─ </span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($forum['name']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($forum['section_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($forum['parent_name'] ?? '-') ?></td>
                                    <td class="text-sm"><?= htmlspecialchars(substr($forum['description'] ?? '', 0, 50)) ?><?= strlen($forum['description'] ?? '') > 50 ? '...' : '' ?></td>
                                    <td><?= $forum['minclassread'] ?></td>
                                    <td><?= $forum['minclasswrite'] ?></td>
                                    <td><?= $forum['minclasscreate'] ?></td>
                                    <td><?= $forum['sort'] ?></td>
                                    <td><?= number_format($forum['topiccount'] ?? 0) ?></td>
                                    <td><?= number_format($forum['postcount'] ?? 0) ?></td>
                                    <td>
                                        <a href="/admin/forums/<?= $forum['id'] ?>/edit" class="link text-sm mr-2">Edit</a>
                                        <a href="/admin/forums/<?= $forum['id'] ?>/delete" 
                                           class="link text-red-600 hover:text-red-800 text-sm"
                                           onclick="return confirm('Delete this forum? This will also delete all topics and posts in it.')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
