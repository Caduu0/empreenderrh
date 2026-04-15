<?php
session_start();
require_once '../config/db.php';

// Obtém ID da empresa
$stmtE = $pdo->prepare("SELECT id FROM empresas WHERE user_id = :user_id");
$stmtE->execute([':user_id' => $_SESSION['user_id']]);
$empresa_id = $stmtE->fetch()->id ?? 0;

$success = '';
$error = '';

// Exclusão / Alteração de Status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
        $del_id = (int)$_POST['delete_id'];
        
        // Verifica titularidade antes de deletar
        $stmtCheck = $pdo->prepare("SELECT id FROM vagas WHERE id = :id AND empresa_id = :e_id");
        $stmtCheck->execute([':id'=>$del_id, ':e_id'=>$empresa_id]);
        if ($stmtCheck->fetch()) {
            $stmtDel = $pdo->prepare("DELETE FROM vagas WHERE id = :id");
            $stmtDel->execute([':id'=>$del_id]);
            $success = "A vaga foi completamente apagada da plataforma.";
        } else {
            $error = "Ação não permitida.";
        }
    }
}

// Lista Vagas da Empresa
$sql = "SELECT v.*, 
        (SELECT COUNT(*) FROM candidaturas WHERE vaga_id = v.id) as qtd_candidatos 
        FROM vagas v 
        WHERE v.empresa_id = :e_id 
        ORDER BY v.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':e_id' => $empresa_id]);
$vagas = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800">Gerenciar <span class="text-blue-600">Vagas</span></h1>
        <p class="text-slate-500 mt-1">Acesse e gerencie suas oportunidades de carreira publicadas.</p>
    </div>
    <a href="postar-vaga.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-lg shadow-blue-600/30 text-center whitespace-nowrap inline-flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nova Vaga
    </a>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 text-red-700 p-4 mb-6 rounded-lg shadow-sm font-medium"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-emerald-50 text-emerald-700 p-4 mb-6 rounded-lg shadow-sm font-medium"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-semibold border-b border-slate-200">
                    <th class="px-6 py-4">Status & Título</th>
                    <th class="px-6 py-4">Contrato</th>
                    <th class="px-6 py-4">Localização</th>
                    <th class="px-6 py-4 text-center">Candidatos</th>
                    <th class="px-6 py-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <?php if(count($vagas) > 0): ?>
                    <?php foreach($vagas as $vaga): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <?php if($vaga->status === 'aberta'): ?>
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 mr-3 shrink-0"></span>
                                <?php else: ?>
                                    <span class="h-2.5 w-2.5 rounded-full bg-slate-400 mr-3 shrink-0"></span>
                                <?php endif; ?>
                                <div>
                                    <p class="font-bold text-slate-900 text-base"><?= htmlspecialchars($vaga->titulo) ?></p>
                                    <p class="text-xs text-slate-400">Criada: <?= date('d/m/Y', strtotime($vaga->created_at)) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 border border-blue-100 px-2 py-1 rounded text-xs font-semibold mr-1"><?= htmlspecialchars($vaga->tipo_contrato) ?></span>
                            <span class="text-xs text-slate-500"><?= htmlspecialchars($vaga->modalidade) ?></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <?= htmlspecialchars($vaga->cidade) ?> - <?= htmlspecialchars($vaga->estado) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <?php if($vaga->qtd_candidatos > 0): ?>
                                    <a href="candidatos.php?vaga_id=<?= $vaga->id ?>" class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full text-sm font-bold hover:bg-indigo-100 transition-colors cursor-pointer">
                                        <?= $vaga->qtd_candidatos ?> Perfis <span class="ml-1">&rarr;</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400 text-sm font-medium">0 Perfis</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="candidatos.php?vaga_id=<?= $vaga->id ?>" class="text-blue-600 hover:text-blue-900 font-medium text-sm p-2 hover:bg-blue-50 rounded transition-colors" title="Ver Candidatos">Ver Candidatos</a>
                                
                                <form method="POST" onsubmit="return confirm('Tem certeza absoluta que deseja excluir esta vaga permanentemente? Todo o histórico de candidaturas ligadas a ela será perdido!');" class="inline-block">
                                    <input type="hidden" name="delete_id" value="<?= $vaga->id ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm p-2 hover:bg-red-50 rounded transition-colors">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                            Nenhuma vaga postada ainda. Que tal criar a primeira e buscar os maiores talentos?
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    </main>
</body>
</html>