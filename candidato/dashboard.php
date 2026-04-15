<?php
session_start();
require_once '../config/db.php';

// Dados do perfil logado
$stmt = $pdo->prepare("SELECT id, nome_completo FROM candidatos WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$candidato = $stmt->fetch();

if (!$candidato) {
    die("Perfil de candidato não encontrado. Entre em contato com o suporte.");
}
$candidato_id = $candidato->id;

// Estatísticas
$stmtStats1 = $pdo->prepare("SELECT COUNT(*) as total FROM candidaturas WHERE candidato_id = :id");
$stmtStats1->execute([':id' => $candidato_id]);
$total_candidaturas = $stmtStats1->fetch()->total;

$stmtStats2 = $pdo->prepare("SELECT COUNT(*) as total FROM favoritos WHERE candidato_id = :id");
$stmtStats2->execute([':id' => $candidato_id]);
$total_favoritos = $stmtStats2->fetch()->total;

// Vagas recentes (5 vagas)
$stmtVagas = $pdo->query("
    SELECT v.*, e.razao_social, e.nome_fantasia 
    FROM vagas v 
    JOIN empresas e ON v.empresa_id = e.id 
    WHERE v.status = 'aberta' 
    ORDER BY v.created_at DESC 
    LIMIT 4
");
$vagas_recentes = $stmtVagas->fetchAll();

include 'includes/header.php';
?>

<!-- Identificação e Boas vindas -->
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800">Olá, <span class="text-blue-600"><?= htmlspecialchars(explode(' ', trim($candidato->nome_completo))[0]) ?></span>! 👋</h1>
    <p class="text-slate-500 mt-1 text-lg">Bem-vindo(a) de volta. Confira o panorama da sua jornada profissional hoje.</p>
</div>

<!-- Resumo -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-4 bg-blue-100 text-blue-600 rounded-xl mr-5">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Candidaturas</p>
            <p class="text-3xl font-bold text-slate-800"><?= $total_candidaturas ?></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-4 bg-rose-100 text-rose-500 rounded-xl mr-5">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Vagas Favoritas</p>
            <p class="text-3xl font-bold text-slate-800"><?= $total_favoritos ?></p>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-md p-6 flex flex-col justify-center items-start text-white sm:col-span-2 lg:col-span-1">
        <h3 class="font-bold text-xl mb-2">Complete seu Perfil</h3>
        <p class="text-blue-100 text-sm mb-4">Candidatos com perfil completo têm 70% mais chances de serem chamados.</p>
        <a href="perfil.php" class="bg-white text-blue-700 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-slate-50 transition-colors">Atualizar agora</a>
    </div>
</div>

<!-- Listagem Rápida -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h2 class="text-lg font-bold text-slate-800">Oportunidades Recentes</h2>
        <a href="vagas.php" class="text-sm font-medium text-blue-600 hover:text-blue-800">Ver todas &rarr;</a>
    </div>
    <ul class="divide-y divide-slate-100">
        <?php if (count($vagas_recentes) > 0): ?>
            <?php foreach($vagas_recentes as $vaga): ?>
            <li class="p-6 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($vaga->titulo) ?></h3>
                    <p class="text-slate-500 text-sm mt-1">
                        <span class="font-medium text-indigo-600"><?= htmlspecialchars($vaga->nome_fantasia ?: $vaga->razao_social) ?></span> 
                        &bull; <?= htmlspecialchars($vaga->modalidade) ?> &bull; <?= htmlspecialchars($vaga->cidade) ?>/<?= htmlspecialchars($vaga->estado) ?>
                    </p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="vagas.php?vaga_id=<?= $vaga->id ?>" class="w-full sm:w-auto text-center px-4 py-2 border border-slate-200 text-slate-600 font-medium rounded-lg hover:bg-slate-100 transition-colors">Ver Detalhes</a>
                </div>
            </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="p-8 text-center text-slate-500">Nenhuma vaga aberta no momento, volte mais tarde!</li>
        <?php endif; ?>
    </ul>
</div>

    </main>
</body>
</html>