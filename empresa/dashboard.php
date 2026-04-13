<?php
// empresa/dashboard.php
session_start();
require_once '../config/db.php';

// Busca ID da empresa
$stmt = $pdo->prepare("SELECT id, razao_social, nome_fantasia FROM empresas WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$empresa = $stmt->fetch();

if (!$empresa) {
    die("Perfil de empresa não encontrado.");
}
$empresa_id = $empresa->id;

// Busca estatísticas
// 1. Vagas ativas
$stmtVagasAtivas = $pdo->prepare("SELECT COUNT(*) as total FROM vagas WHERE empresa_id = :id AND status = 'aberta'");
$stmtVagasAtivas->execute([':id' => $empresa_id]);
$vagas_ativas = $stmtVagasAtivas->fetch()->total;

// 2. Total candidaturas recebidas
$stmtTotalCand = $pdo->prepare("SELECT COUNT(*) as total FROM candidaturas c JOIN vagas v ON c.vaga_id = v.id WHERE v.empresa_id = :id");
$stmtTotalCand->execute([':id' => $empresa_id]);
$total_candidaturas = $stmtTotalCand->fetch()->total;

// 3. Novas candidaturas (nas últimas 24h)
$stmtNovasCand = $pdo->prepare("SELECT COUNT(*) as total FROM candidaturas c JOIN vagas v ON c.vaga_id = v.id WHERE v.empresa_id = :id AND c.data_candidatura >= NOW() - INTERVAL 1 DAY");
$stmtNovasCand->execute([':id' => $empresa_id]);
$novas_candidaturas = $stmtNovasCand->fetch()->total;

// Busca as 5 candidaturas mais recentes
$sqlRecents = "SELECT c.id as candidatura_id, c.status, c.data_candidatura, 
               cand.nome_completo, v.titulo as vaga_titulo 
               FROM candidaturas c 
               JOIN vagas v ON c.vaga_id = v.id 
               JOIN candidatos cand ON c.candidato_id = cand.id 
               WHERE v.empresa_id = :id 
               ORDER BY c.data_candidatura DESC LIMIT 5";
$stmtRecents = $pdo->prepare($sqlRecents);
$stmtRecents->execute([':id' => $empresa_id]);
$recentes = $stmtRecents->fetchAll();

include 'includes/header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800">Painel de <span class="text-blue-600">Recrutamento</span></h1>
    <p class="text-slate-500 mt-1 text-lg">Bem-vindo(a), <span class="font-semibold text-slate-700"><?= htmlspecialchars($empresa->nome_fantasia ?: $empresa->razao_social) ?></span>. Acompanhe a tração das suas vagas.</p>
</div>

<!-- Cards Resumo -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">Ativo</span>
        </div>
        <h3 class="text-3xl font-bold text-slate-800 mb-1"><?= $vagas_ativas ?></h3>
        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Vagas Publicadas Aberta</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-slate-800 mb-1"><?= $total_candidaturas ?></h3>
        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total de Currículos</p>
    </div>

    <div class="bg-slate-800 rounded-2xl shadow-md p-6 flex flex-col text-white">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-slate-700 text-blue-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <?php if($novas_candidaturas > 0): ?>
                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded shadow animate-pulse px-3">&uarr; Novo!</span>
            <?php endif; ?>
        </div>
        <h3 class="text-3xl font-bold mb-1"><?= $novas_candidaturas ?></h3>
        <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Candidaturas nas Últimas 24h</p>
    </div>
</div>

<!-- Últimas Candidaturas -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <h2 class="text-lg font-bold text-slate-800">Candidaturas Mais Recentes</h2>
        <a href="vagas.php" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">Gerenciar Vagas &rarr;</a>
    </div>
    
    <?php if(count($recentes) > 0): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white text-slate-500 text-sm uppercase tracking-wider border-b border-slate-100 hidden sm:table-row">
                    <th class="p-4 font-semibold">Candidato</th>
                    <th class="p-4 font-semibold">Vaga Pretendida</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold text-right">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <?php foreach($recentes as $cand): ?>
                <tr class="hover:bg-slate-50 transition-colors flex flex-col sm:table-row">
                    <td class="p-4 flex items-center">
                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold mr-3 shrink-0">
                            <?= substr($cand->nome_completo, 0, 1) ?>
                        </div>
                        <span class="font-medium text-slate-900"><?= htmlspecialchars($cand->nome_completo) ?></span>
                    </td>
                    <td class="p-4 sm:font-normal font-medium text-slate-600">
                        <?= htmlspecialchars($cand->vaga_titulo) ?>
                    </td>
                    <td class="p-4">
                        <?php 
                            $statusColors = [
                                'pendente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'em_analise' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'entrevista' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'aprovado' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'reprovado' => 'bg-rose-100 text-rose-800 border-rose-200'
                            ];
                            $colorClass = $statusColors[$cand->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                            $labelStatus = str_replace('_', ' ', $cand->status);
                        ?>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize border <?= $colorClass ?>">
                            <?= $labelStatus ?>
                        </span>
                    </td>
                    <td class="p-4 text-slate-400 text-sm sm:text-right">
                        <?= date('d/m/Y H:i', strtotime($cand->data_candidatura)) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="p-8 text-center text-slate-500">Nenhum talento se candidatou às suas vagas ainda. Mantenha os anúncios ativos!</div>
    <?php endif; ?>
</div>

    </main>
</body>
</html>
