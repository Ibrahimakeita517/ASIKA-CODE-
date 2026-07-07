<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

// Récupération des vrais étudiants depuis la base de données
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'learner' ORDER BY created_at DESC");
$students = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-black text-slate-800">Gestion des Étudiants</h2>
        <p class="text-xs text-gray-400">Liste réelle des apprenants inscrits</p>
    </div>
    <div class="flex gap-3">
        <a href="add_student.php" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-lg shadow-orange-600/20 transition-all flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Inscrire un élève
        </a>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                <th class="px-8 py-4">Étudiant</th>
                <th class="px-8 py-4 text-center">XP</th>
                <th class="px-8 py-4 text-center">Niveau</th>
                <th class="px-8 py-4 text-center">Inscription</th>
                <th class="px-8 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php if (empty($students)): ?>
            <tr>
                <td colspan="5" class="px-8 py-20 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="users" class="w-8 h-8 text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 font-medium">Aucun étudiant n'est encore inscrit.</p>
                        <a href="add_student.php" class="text-orange-600 font-bold text-sm mt-2 underline">Ajouter le premier élève</a>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach($students as $s): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center font-bold text-xs uppercase">
                            <?php
                                $parts = explode(' ', $s['full_name']);
                                echo substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '');
                            ?>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($s['full_name']); ?></p>
                            <p class="text-[10px] text-gray-400 font-medium"><?php echo htmlspecialchars($s['email']); ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-8 py-5 text-center text-sm font-bold text-slate-700">
                    <div class="flex items-center justify-center gap-1">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                        <?php echo $s['xp']; ?>
                    </div>
                </td>
                <td class="px-8 py-5 text-center">
                    <span class="text-xs font-bold bg-blue-50 text-blue-600 px-3 py-1 rounded-full">Lvl <?php echo $s['level']; ?></span>
                </td>
                <td class="px-8 py-5 text-center text-[10px] font-bold text-slate-500">
                    <?php echo date('d/m/Y', strtotime($s['created_at'])); ?>
                </td>
                <td class="px-8 py-5">
                    <div class="flex justify-end gap-2">
                        <a href="student_activities.php?id=<?php echo $s['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all" title="Voir les activités">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="login_as.php?id=<?php echo $s['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-all" title="Se connecter en tant que">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </a>
                        <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-orange-50 hover:text-orange-600 transition-all" title="Modifier">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <a href="delete_student.php?id=<?php echo $s['id']; ?>"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')"
                           class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all" title="Supprimer">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>