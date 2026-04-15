<?php
session_start();
require_once '../config/db.php';

// Recebe o ID da vaga
$vaga_id = filter_input(INPUT_GET, 'vaga_id', FILTER_VALIDATE_INT);

if (!$vaga_id) {
    die("Id da vaga inválido.");
}

// Recebe o ID da empresa
$stmtE = $pdo->prepare("SELECT id FROM empresas WHERE user_id = :user_id");
$stmtE->execute([':user_id' => $_SESSION['user_id']]);
$empresa_id = $stmtE->fetch()->id ?? 0;

// Verifica se a vaga pertence a empresa para impedir ataque IDOR
$stmtVaga = $pdo->prepare("SELECT titulo FROM vagas WHERE id = :v_id AND empresa_id = :e_id");
$stmtVaga->execute([':v_id' => $vaga_id, ':e_id' => $empresa_id]);
$vagaInfo = $stmtVaga->fetch();

if (!$vagaInfo) {
    die("Acesso negado. Vaga inexistente ou sem permissões.");
}

$success = '';
$error = '';

// Altera o status do candidato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $cand_id = filter_input(INPUT_POST, 'candidatura_id', FILTER_VALIDATE_INT);
    $new_status = filter_input(INPUT_POST, 'novo_status', FILTER_SANITIZE_STRING);
    
    $allowed_status = ['pendente', 'em_analise', 'entrevista', 'aprovado', 'reprovado'];
    
    if ($cand_id && in_array($new_status, $allowed_status)) {
        try {
            // Verifica a segurança na atualização (se a candidatura é da vaga atrelada a uma empresa)
            $stmtUpd = $pdo->prepare("UPDATE candidaturas SET status = :status WHERE id = :id AND vaga_id = :v_id");
            $stmtUpd->execute([':status' => $new_status, ':id' => $cand_id, ':v_id' => $vaga_id]);
            $success = "Status do candidato atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Falha ao mudar o status.";
        }
    }
}

// Lista de Candidaturas
$sql = "SELECT c.id as candidatura_id, c.status, c.data_candidatura, 
        cand.id as cand_id, cand.nome_completo, cand.cidade, cand.estado, cand.experiencia_profissional, cand.telefone
        FROM candidaturas c 
        JOIN candidatos cand ON c.candidato_id = cand.id 
        WHERE c.vaga_id = :vaga_id 
        ORDER BY c.data_candidatura DESC";
$stmtCand = $pdo->prepare($sql);
$stmtCand->execute([':vaga_id' => $vaga_id]);
$candidaturas = $stmtCand->fetchAll();

include 'includes/header.php';
?>

<div class="mb-4 flex items-center gap-2">
    <a href="vagas.php" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">&larr; Voltar às Vagas</a>
</div>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800">Candidatos: <span class="text-blue-600"><?= htmlspecialchars($vagaInfo->titulo) ?></span></h1>
    <p class="text-slate-500 mt-1">Gerencie os talentos que demonstraram interesse na vaga apontada e altere seu status no funil.</p>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 text-red-700 p-4 mb-6 rounded-lg shadow-sm font-medium"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-emerald-50 text-emerald-700 p-4 mb-6 rounded-lg shadow-sm font-medium"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="space-y-4">
    <?php if(count($candidaturas) > 0): ?>
        <?php foreach($candidaturas as $c): ?>
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow relative overflow-hidden">
                <!-- Status colorido -->
                <?php 
                    $borderColor = match($c->status) {
                        'pendente' => 'bg-yellow-400',
                        'em_analise' => 'bg-blue-400',
                        'entrevista' => 'bg-purple-400',
                        'aprovado' => 'bg-emerald-400',
                        'reprovado' => 'bg-rose-400',
                        default => 'bg-slate-400'
                    };
                ?>
                <div class="absolute top-0 left-0 w-full h-1 <?= $borderColor ?>"></div>
                
                <div class="flex flex-col lg:flex-row gap-6 justify-between items-start lg:items-center">
                    
                    <!-- Perfil -->
                    <div class="flex items-center gap-5 w-full lg:w-auto">
                        <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center border-2 border-white shadow-sm shrink-0">
                            <span class="text-2xl font-bold text-slate-400"><?= substr($c->nome_completo, 0, 1) ?></span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($c->nome_completo) ?></h3>
                            <p class="text-sm text-slate-500 tracking-wide mt-0.5">
                                <?= htmlspecialchars($c->cidade) ?> - <?= htmlspecialchars($c->estado) ?> &bull; 
                                Inscrito dia <?= date('d/m/Y', strtotime($c->data_candidatura)) ?>
                            </p>
                            <p class="text-sm font-medium text-slate-600 mt-2 truncate w-full max-w-sm">
                                📞 <?= htmlspecialchars($c->telefone ?? '—') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Botão Perfil -->
                    <div class="shrink-0 flex gap-2 w-full sm:w-auto">
                        <button onclick="alert('Currículo / Resumo do Candidato:\n\n<?= addslashes(preg_replace('/\s+/', ' ', $c->experiencia_profissional ?? 'Nenhum dado informado ainda.')) ?>')" class="w-full sm:w-auto px-4 py-2 border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg font-medium text-sm transition-colors text-center">
                            Inspecionar Perfil
                        </button>
                    </div>

                    <!-- Alteração dos Status -->
                    <div class="w-full lg:w-auto lg:min-w-[280px]">
                        <form method="POST" class="flex flex-col sm:flex-row items-center gap-2 bg-slate-50 p-2 rounded-lg border border-slate-100">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="candidatura_id" value="<?= $c->candidatura_id ?>">
                            <select name="novo_status" class="w-full sm:w-auto flex-grow px-3 py-2 rounded border border-slate-200 text-sm font-medium text-slate-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" onchange="this.form.submit()">
                                <option value="pendente" <?= $c->status === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="em_analise" <?= $c->status === 'em_analise' ? 'selected' : '' ?>>Em Análise</option>
                                <option value="entrevista" <?= $c->status === 'entrevista' ? 'selected' : '' ?>>Na Entrevista</option>
                                <option value="aprovado" <?= $c->status === 'aprovado' ? 'selected' : '' ?>>Aprovado (Hire!)</option>
                                <option value="reprovado" <?= $c->status === 'reprovado' ? 'selected' : '' ?>>Reprovado</option>
                            </select>
                            <span class="text-xs text-slate-400 whitespace-nowrap hidden sm:block px-2">Atualiza auto.</span>
                        </form>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="p-10 text-center bg-white rounded-2xl border border-slate-200 shadow-sm">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-slate-900">Aguardando aplicações</h3>
            <p class="mt-1 text-slate-500">Divulgue seu link externamente. Por enquanto não há candidatos filtrados por essa vaga.</p>
        </div>
    <?php endif; ?>
</div>

    </main>
</body>
</html>