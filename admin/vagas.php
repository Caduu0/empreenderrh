<?php
session_start();
require_once '../config/db.php';

$success = '';
$error = '';

// Moderação de Vagas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $vaga_id = filter_input(INPUT_POST, 'vaga_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'];
    
    if ($vaga_id) {
        if ($action === 'approve') {
            try {
                $stmt = $pdo->prepare("UPDATE vagas SET status = 'aberta' WHERE id = :id");
                $stmt->execute([':id' => $vaga_id]);
                $success = "A vaga foi re-aprovada para listagem pública com sucesso.";
            } catch(PDOException $e) { $error = "Erro."; }
        } elseif ($action === 'suspend') {
            try {
                $stmt = $pdo->prepare("UPDATE vagas SET status = 'cancelada' WHERE id = :id");
                $stmt->execute([':id' => $vaga_id]);
                $success = "A vaga foi cancelada pelo Moderador (Tirada do Ar).";
            } catch(PDOException $e) { $error = "Erro."; }
        } elseif ($action === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM vagas WHERE id = :id");
                $stmt->execute([':id' => $vaga_id]);
                $success = "A vaga (SPAM) foi obliterada cirurgicamente do sistema.";
            } catch(PDOException $e) { $error = "Falha ao remover."; }
        }
    }
}

// Vagas globais
$sql = "SELECT v.id, v.titulo, v.status, v.created_at, e.razao_social, e.nome_fantasia 
        FROM vagas v 
        JOIN empresas e ON v.empresa_id = e.id 
        ORDER BY v.created_at DESC";
$vagas = $pdo->query($sql)->fetchAll();

// Traz a validação de sessão (admin) e o Sidebar
include 'includes/sidebar.php';
?>

<div class="mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Moderação de <span class="text-blue-600">Vagas</span></h1>
        <p class="text-slate-500 mt-2 text-lg">Centralize a garantia de qualidade blindando a home de anúncios enganosos e fake-jobs.</p>
    </div>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 text-red-700 p-4 mb-8 rounded-lg shadow-sm border border-red-200 font-medium"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-emerald-50 text-emerald-700 p-4 mb-8 rounded-lg shadow-sm border border-emerald-200 font-medium"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
    <div class="overflow-x-auto min-h-[500px]">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-semibold border-b border-slate-200">
                    <th class="px-6 py-5">Vaga Anunciada</th>
                    <th class="px-6 py-5">Autor (Empresa Pública)</th>
                    <th class="px-6 py-5 text-center">Status Flag</th>
                    <th class="px-6 py-5 text-right w-1/4">Ação Moderativa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                <?php if(count($vagas) > 0): ?>
                    <?php foreach($vagas as $v): ?>
                        <tr class="transition-colors hover:bg-slate-50 <?= $v->status === 'cancelada' ? 'bg-red-50/20' : '' ?>">
                            <td class="px-6 py-4">
                                <h3 class="text-base font-bold text-slate-900 <?= $v->status === 'cancelada' ? 'line-through text-slate-400' : '' ?>"><?= htmlspecialchars($v->titulo) ?></h3>
                                <p class="text-xs text-slate-400 flex items-center mt-1">ID Universal: <strong>#<?= $v->id ?></strong><span class="mx-2">•</span><?= date('d M Y - H:i', strtotime($v->created_at)) ?></p>
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-blue-700">
                                <?= htmlspecialchars($v->nome_fantasia ?: $v->razao_social) ?>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <?php if($v->status === 'aberta'): ?>
                                    <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full text-xs shadow-sm shadow-emerald-100">Live (Pública)</span>
                                <?php elseif($v->status === 'cancelada'): ?>
                                    <span class="bg-rose-100 text-rose-700 font-bold px-3 py-1 rounded-full text-xs shadow-sm shadow-rose-100">Censurada</span>
                                <?php else: ?>
                                    <span class="bg-slate-200 text-slate-700 font-bold px-3 py-1 rounded-full text-xs"><?= strtoupper($v->status) ?></span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="vaga_id" value="<?= $v->id ?>">
                                        <?php if($v->status === 'aberta'): ?>
                                            <input type="hidden" name="action" value="suspend">
                                            <button type="submit" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-bold text-xs px-3 py-1.5 rounded transition shadow-sm border border-yellow-200 w-24">
                                                Ocultar
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase px-3 py-1.5 rounded transition shadow shadow-emerald-500/50 border border-emerald-700 w-24">
                                                Aprovar
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                    <!-- Exclusão total -->
                                    <form method="POST" onclick="return confirm('Deseja excluir sumariamente (DELETE) esse anuncio falso?');" class="inline">
                                        <input type="hidden" name="vaga_id" value="<?= $v->id ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded font-bold text-xs uppercase transition border border-rose-200">
                                            Apagar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">O sistema está limpo. Não foram postadas vagas em nosso ecossistema.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

            </div> <!--Fecha max-w da view iniciada no sidebar -->
        </main> <!-- Fecha o MAIN iniciado no sidebar -->
    </div> <!-- Fecha o FLEX HEIGHT iniciado no sidebar -->
</body>
</html>