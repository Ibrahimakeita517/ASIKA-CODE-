<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

// Récupération des logs avec pagination simple
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("
    SELECT al.*, u.full_name, u.role
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();

// Nombre total pour la pagination
$totalLogs = $pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
$totalPages = ceil($totalLogs / $limit);

include 'header.php';
?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Journal d'activité</h3>
        <p class="text-gray-500 text-sm">Suivez toutes les actions effectuées sur la plateforme.</p>
    </div>
    <div class="flex gap-3">
        <button onclick="window.location.reload()" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            Actualiser
        </button>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Utilisateur</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Action</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Détails</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">IP Address</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Date & Heure</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-gray-400">
                            <i data-lucide="info" class="w-8 h-8 mx-auto mb-3 opacity-20"></i>
                            <p>Aucune activité enregistrée pour le moment.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg <?php echo $log['role'] === 'admin' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600'; ?> flex items-center justify-center font-bold text-xs">
                                        <?php echo strtoupper(substr($log['full_name'] ?? '?', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($log['full_name'] ?? 'Système'); ?></p>
                                        <p class="text-[10px] text-gray-400 uppercase font-medium"><?php echo $log['role'] ?? 'N/A'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[11px] font-bold border border-gray-200 uppercase tracking-wide">
                                    <?php echo htmlspecialchars($log['action']); ?>
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm text-gray-600 max-w-xs truncate" title="<?php echo htmlspecialchars($log['details']); ?>">
                                    <?php echo htmlspecialchars($log['details']); ?>
                                </p>
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-400 font-mono">
                                <?php echo $log['ip_address'] ?? '0.0.0.0'; ?>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <p class="text-sm font-semibold text-slate-700"><?php echo date('d/m/Y', strtotime($log['created_at'])); ?></p>
                                <p class="text-[10px] text-gray-400"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-500 font-medium">Affichage de <?php echo count($logs); ?> logs sur <?php echo $totalLogs; ?></p>
            <div class="flex gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold transition-all <?php echo $page == $i ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'bg-white text-gray-400 hover:text-slate-700 border border-gray-100'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>