<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Récupérer les modules pour le sélecteur, groupés par parcours
$stmt = $pdo->query("
    SELECT m.id, m.title as module_title, p.title as path_title
    FROM modules m
    JOIN paths p ON m.path_id = p.id
    ORDER BY p.title, m.order_index
");
$modules = $stmt->fetchAll();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = $_POST['module_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $xp_reward = $_POST['xp_reward'] ?? 10;
    $duration = $_POST['duration'] ?? 10;
    $order_index = $_POST['order_index'] ?? 0;

    $audio_url = '';
    $video_url = '';

    // Gestion de l'upload Audio
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === 0) {
        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'webm'];
        $filename = $_FILES['audio_file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $newName = 'audio_' . time() . '_' . uniqid() . '.' . $ext;
            $uploadDir = '../assets/audio/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $uploadDir . $newName)) {
                $audio_url = 'assets/audio/' . $newName;
            }
        }
    }

    // Gestion de l'upload Vidéo
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === 0) {
        $allowed = ['mp4', 'webm', 'ogg', 'mov'];
        $filename = $_FILES['video_file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $newName = 'video_' . time() . '_' . uniqid() . '.' . $ext;
            $uploadDir = '../assets/video/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadDir . $newName)) {
                $video_url = 'assets/video/' . $newName;
            }
        }
    }

    if (!empty($module_id) && !empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, audio_bambara_url, video_url, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$module_id, $title, $content, $audio_url, $video_url, $xp_reward, $duration, $order_index]);

        if ($result) {
            log_activity($pdo, $_SESSION['user_id'], 'CREATION_LECON', "Nouvelle leçon créée : $title");
            header('Location: courses.php?tab=lecons&success=lesson_added');
            exit;
        } else {
            $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Erreur lors de l'ajout de la leçon.</div>";
        }
    } else {
        $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Veuillez remplir les champs obligatoires.</div>";
    }
}

require_once 'header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="courses.php?tab=lecons" class="text-sm font-bold text-gray-400 hover:text-orange-600 flex items-center gap-2 mb-4 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour aux leçons
        </a>
        <h2 class="text-3xl font-black text-slate-800">Nouvelle Leçon</h2>
        <p class="text-gray-500">Configurez votre contenu multimédia (Vidéo, Audio ou Texte).</p>
    </div>

    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 space-y-8">
        <!-- Section Titre et Module -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Module Parent</label>
                <div class="relative">
                    <select name="module_id" required
                            class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all appearance-none">
                        <option value="">Sélectionner un module...</option>
                        <?php
                        $current_path = '';
                        foreach($modules as $m):
                            if ($current_path != $m['path_title']):
                                if ($current_path != '') echo '</optgroup>';
                                $current_path = $m['path_title'];
                                echo '<optgroup label="'.htmlspecialchars($current_path).'">';
                            endif;
                        ?>
                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['module_title']); ?></option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Titre de la leçon</label>
                <input type="text" name="title" required placeholder="ex: Introduction aux variables"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
        </div>

        <!-- Section Import Multimédia -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-slate-50 rounded-[2rem] border border-dashed border-slate-200">
            <div>
                <label class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-500 mb-4">
                    <i data-lucide="video" class="w-4 h-4 text-orange-600"></i>
                    Importer une Vidéo
                </label>
                <div class="relative">
                    <input type="file" name="video_file" accept="video/*" class="hidden" id="video_input" onchange="updateFileName(this, 'video_name')">
                    <label for="video_input" class="flex flex-col items-center justify-center w-full h-32 bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-orange-500 hover:bg-orange-50/30 cursor-pointer transition-all group">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-300 group-hover:text-orange-500 mb-2"></i>
                        <span id="video_name" class="text-[10px] font-bold text-gray-400 group-hover:text-orange-600 uppercase">Choisir le fichier MP4</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-500 mb-4">
                    <i data-lucide="mic" class="w-4 h-4 text-orange-600"></i>
                    Audio (Bambara)
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Upload classique -->
                    <div class="relative">
                        <input type="file" name="audio_file" accept="audio/*" class="hidden" id="audio_input" onchange="updateFileName(this, 'audio_name')">
                        <label for="audio_input" class="flex flex-col items-center justify-center w-full h-32 bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-orange-500 hover:bg-orange-50/30 cursor-pointer transition-all group">
                            <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-300 group-hover:text-orange-500 mb-2"></i>
                            <span id="audio_name" class="text-[10px] font-bold text-gray-400 group-hover:text-orange-600 uppercase text-center px-4">Choisir MP3/WAV</span>
                        </label>
                    </div>

                    <!-- Enregistrement Direct -->
                    <div class="relative h-32">
                        <div id="record_btn" class="flex flex-col items-center justify-center w-full h-full bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-red-500 hover:bg-red-50/30 cursor-pointer transition-all group">
                            <i data-lucide="mic" class="w-6 h-6 text-gray-300 group-hover:text-red-500 mb-2"></i>
                            <span id="record_status" class="text-[10px] font-bold text-gray-400 group-hover:text-red-600 uppercase">Enregistrer Direct</span>
                        </div>

                        <!-- UI d'enregistrement active -->
                        <div id="recording_ui" class="hidden absolute inset-0 bg-red-600 rounded-2xl flex flex-col items-center justify-center text-white z-10 animate-pulse">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                <span id="record_timer" class="font-mono font-black text-xl">00:00</span>
                            </div>
                            <button type="button" id="stop_btn" class="bg-white text-red-600 px-4 py-1 rounded-full text-[10px] font-black uppercase">Arrêter</button>
                        </div>
                    </div>
                </div>
                <audio id="audio_preview" controls class="hidden w-full h-10 mt-4 bg-gray-50 rounded-xl"></audio>
            </div>
        </div>

        <!-- Section Contenu -->
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Explications / Contenu Texte</label>
            <textarea name="content" rows="6" placeholder="Rédigez ici les explications qui accompagneront le média..."
                      class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all font-sans"></textarea>
        </div>

        <!-- Section Params -->
        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Récompense XP</label>
                <input type="number" name="xp_reward" value="15"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all text-center font-bold">
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Durée (min)</label>
                <input type="number" name="duration" value="10"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all text-center font-bold">
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Ordre</label>
                <input type="number" name="order_index" value="1"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all text-center font-bold">
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-orange-600/20 transition-all uppercase tracking-widest text-sm flex items-center justify-center gap-3">
                <i data-lucide="send" class="w-5 h-5"></i>
                Publier la leçon
            </button>
        </div>
    </form>
</div>

<script>
function updateFileName(input, targetId) {
    if (input.files && input.files[0]) {
        document.getElementById(targetId).textContent = input.files[0].name;
        document.getElementById(targetId).classList.remove('text-gray-400');
        document.getElementById(targetId).classList.add('text-orange-600');
    }
}

// Logique d'enregistrement audio
let mediaRecorder;
let audioChunks = [];
let startTime;
let timerInterval;

const recordBtn = document.getElementById('record_btn');
const stopBtn = document.getElementById('stop_btn');
const recordingUI = document.getElementById('recording_ui');
const recordTimer = document.getElementById('record_timer');
const audioInput = document.getElementById('audio_input');
const audioName = document.getElementById('audio_name');
const audioPreview = document.getElementById('audio_preview');

if (recordBtn) {
    recordBtn.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPreview.src = audioUrl;
                audioPreview.classList.remove('hidden');

                // Créer un fichier à partir du blob pour l'input
                const file = new File([audioBlob], "enregistrement_" + Date.now() + ".webm", { type: 'audio/webm' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                audioInput.files = dataTransfer.files;

                audioName.textContent = "ENREGISTREMENT PRÊT";
                audioName.classList.remove('text-gray-400');
                audioName.classList.add('text-orange-600');

                // Arrêter les pistes du micro
                stream.getTracks().forEach(track => track.stop());
            };

            mediaRecorder.start();
            recordingUI.classList.remove('hidden');
            startTime = Date.now();
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);

        } catch (err) {
            console.error("Erreur micro:", err);
            alert("Impossible d'accéder au micro. Vérifiez les permissions.");
        }
    });
}

if (stopBtn) {
    stopBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            recordingUI.classList.add('hidden');
            clearInterval(timerInterval);
        }
    });
}

function updateTimer() {
    const now = Date.now();
    const diff = Math.floor((now - startTime) / 1000);
    const mins = Math.floor(diff / 60).toString().padStart(2, '0');
    const secs = (diff % 60).toString().padStart(2, '0');
    recordTimer.textContent = `${mins}:${secs}`;
}
</script>

<?php require_once 'footer.php'; ?>
