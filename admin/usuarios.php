<?php
session_start();
require_once '../config/db.php';

$success = '';
$error = '';

// Controle de Usuários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $target_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'];
    
    // Bloqueia que o admin se exclua, caso tente
    if ($target_id == $_SESSION['user_id']) {
        $error = "Você não pode suspender ou eliminar a si mesmo (sua própria conta ativa).";
    } else {
        if ($action === 'suspend') {
            try {
                $stmt = $pdo->prepare("UPDATE users SET status = 'banido' WHERE id = :id");
                $stmt->execute([':id' => $target_id]);
                $success = "Conta suspensa! Usuário banido do acesso imediato.";
            } catch (PDOException $e) { $error = "Erro ao suspender."; }
            
        } elseif ($action === 'reactivate') {
            try {
                $stmt = $pdo->prepare("UPDATE users SET status = 'ativo' WHERE id = :id");
                $stmt->execute([':id' => $target_id]);
                $success = "Conta perdoada e reativada com sucesso.";
            } catch (PDOException $e) { $error = "Erro ao reativar."; }

        } elseif ($action === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'admin'");
                $stmt->execute([':id' => $target_id]);
                $success = "Usuário e perfil associado (bem como candidaturas atreladas) limpos com vaporização total!";
            } catch (PDOException $e) { 
                $error = "Falha crítica na exclusão: " . $e->getMessage(); 
            }
        }
    }
}

// Usuários
$sql = "SELECT u.id, u.email, u.role, u.status, u.created_at, 
        COALESCE(c.nome_completo, e.razao_social, 'Administrador Central') as nome 
        FROM users u 
        LEFT JOIN candidatos c ON u.id = c.user_id 
        LEFT JOIN empresas e ON u.id = e.user_id 
        ORDER BY u.created_at DESC";
$usuarios = $pdo->query($sql)->fetchAll();

// Traz a validação de sessão (admin) e o Sidebar
include 'includes/sidebar.php';
?>

<div class="mb-10">
    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Gestão de <span class="text-blue-600">Usuários</span></h1>
    <p class="text-slate-500 mt-2 text-lg">Diretório completo de contas. Intervenha com sanções, suspenda perfis indevidos ou expurgue-os permanentemente.</p>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 text-red-700 p-4 mb-8 rounded-lg shadow-sm border border-red-200 font-medium flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-emerald-50 text-emerald-700 p-4 mb-8 rounded-lg shadow-sm border border-emerald-200 font-medium flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
    <div class="overflow-x-auto min-h-[500px]">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-semibold border-b border-slate-200">
                    <th class="px-6 py-5">#ID</th>
                    <th class="px-6 py-5">Perfil (Nome Fantasia / Completo)</th>
                    <th class="px-6 py-5">Contato Registrado</th>
                    <th class="px-6 py-5">Status Operacional</th>
                    <th class="px-6 py-5 text-right">Direitos de Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                <?php foreach($usuarios as $u): ?>
                    <tr class="hover:bg-slate-50 transition-colors <?= $u->status === 'banido' ? 'bg-red-50/30' : '' ?>">
                        <td class="px-6 py-4 text-sm text-slate-400 font-bold w-12">#<?= $u->id ?></td>
                        
                        <td class="px-6 py-4 flex items-center">
                            <div class="text-xs font-bold uppercase tracking-wider px-2 py-1 rounded inline-block mr-3 w-10 text-center shrink-0
                                <?= $u->role === 'candidato' ? 'bg-blue-100 text-blue-700' : ($u->role === 'empresa' ? 'bg-purple-100 text-purple-700' : 'bg-slate-800 text-white') ?>">
                                <?= substr($u->role, 0, 3) ?>
                            </div>
                            <span class="<?= $u->status === 'banido' ? 'text-slate-400 line-through' : 'text-slate-900 font-bold' ?>">
                                <?= htmlspecialchars($u->nome) ?>
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-sm text-slate-500"><?= htmlspecialchars($u->email) ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php if($u->status === 'ativo'): ?>
                                <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full text-xs">Acessível</span>
                            <?php else: ?>
                                <span class="bg-rose-100 text-rose-700 font-bold px-3 py-1 rounded-full text-xs box-shadow border border-rose-200">Banido/Suspenso</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if($u->role !== 'admin'): ?>
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Bloqueio -->
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="user_id" value="<?= $u->id ?>">
                                        <?php if($u->status === 'ativo'): ?>
                                            <input type="hidden" name="action" value="suspend">
                                            <button type="submit" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-bold text-xs uppercase px-3 py-1.5 rounded transition shadow-sm border border-yellow-200" title="Revogar o acesso deste usuário">
                                                Suspender Conta
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="reactivate">
                                            <button type="submit" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold text-xs uppercase px-3 py-1.5 rounded transition shadow-sm border border-indigo-200">
                                                Ativar Conta
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                    <!-- Exclusão -->
                                    <form method="POST" onclick="return confirm('ATENÇÃO ADMIN: Ações destrutivas como [Eliminar] apagam permanentemente histórico de Banco de Dados de um Candidato/Empresa (incluindo vagas abertas). Deseja vaporizar os vestígios da ID <?= $u->id ?>?');" class="inline">
                                        <input type="hidden" name="user_id" value="<?= $u->id ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase px-3 py-1.5 rounded transition shadow-sm border border-red-700">
                                            Eliminar Database
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-xs uppercase text-slate-400 font-bold tracking-widest bg-slate-100 py-1 px-3 rounded-md">Intocável</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

            </div> <!--Fecha max-w da view iniciada no sidebar -->
        </main> <!-- Fecha o MAIN iniciado no sidebar -->
    </div> <!-- Fecha o FLEX HEIGHT iniciado no sidebar -->
</body>
</html>
