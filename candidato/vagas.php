<?php
// candidato/vagas.php
session_start();
require_once '../config/db.php';

// Busca perfil para obter ID
$stmtC = $pdo->prepare("SELECT id FROM candidatos WHERE user_id = :user_id");
$stmtC->execute([':user_id' => $_SESSION['user_id']]);
$candidato = $stmtC->fetch();
$candidato_id = $candidato->id ?? 0;

$search = $_GET['q'] ?? '';
$success = '';
$error = '';

// Lógica de Candidatura Rápida (Se botão "Candidatar-me" foi pressionado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'candidatar') {
    $vaga_id = (int)$_POST['vaga_id'];
    try {
        $stmtCand = $pdo->prepare("INSERT INTO candidaturas (vaga_id, candidato_id, status) VALUES (:v_id, :c_id, 'pendente')");
        $stmtCand->execute([':v_id' => $vaga_id, ':c_id' => $candidato_id]);
        $success = "Candidatura enviada com sucesso!";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $error = "Você já se candidatou à esta vaga anteriormente.";
        } else {
            $error = "Erro processando candidatura.";
        }
    }
}

// Queries de busca
$sql = "SELECT v.*, e.razao_social, e.nome_fantasia, e.logo 
        FROM vagas v 
        JOIN empresas e ON v.empresa_id = e.id 
        WHERE v.status = 'aberta'";
$params = [];

if ($search) {
    $sql .= " AND (v.titulo LIKE :search OR v.cidade LIKE :search OR e.nome_fantasia LIKE :search)";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY v.created_at DESC";

$stmtVagas = $pdo->prepare($sql);
$stmtVagas->execute($params);
$vagas = $stmtVagas->fetchAll();

include 'includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-800">Explorar <span class="text-blue-600">Vagas</span></h1>
    <p class="text-slate-500 mt-1">Encontre sua próxima grande oportunidade de carreira.</p>
</div>

<!-- Search Bar -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-8">
    <form method="GET" action="vagas.php" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-grow relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Busque por cargo, empresa ou cidade..." 
                   class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-slate-50">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors whitespace-nowrap">
            Pesquisar Vagas
        </button>
    </form>
</div>

<!-- Alerts -->
<?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-sm"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Results -->
<div class="space-y-4">
    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Mostrando <span class="text-slate-800"><?= count($vagas) ?></span> oportunidades abertas</p>

    <?php if (count($vagas) > 0): ?>
        <?php foreach($vagas as $vaga): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row gap-6 items-start hover:border-blue-300 hover:shadow-md transition-all">
                
                <!-- Info Section -->
                <div class="flex-grow">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-indigo-50 text-indigo-700 font-semibold px-2 py-1 rounded text-xs"><?= htmlspecialchars($vaga->modalidade) ?></span>
                        <span class="bg-emerald-50 text-emerald-700 font-semibold px-2 py-1 rounded text-xs"><?= htmlspecialchars($vaga->tipo_contrato) ?></span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800 mb-1"><?= htmlspecialchars($vaga->titulo) ?></h2>
                    <p class="text-slate-600 text-sm flex items-center mb-4">
                        <svg class="h-4 w-4 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="font-medium mr-3"><?= htmlspecialchars($vaga->nome_fantasia ?: $vaga->razao_social) ?></span>
                        <svg class="h-4 w-4 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <?= htmlspecialchars($vaga->cidade) ?> - <?= htmlspecialchars($vaga->estado) ?>
                    </p>
                    <p class="text-slate-500 text-sm line-clamp-2">
                        <?= htmlspecialchars($vaga->descricao) ?>
                    </p>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-col gap-3 w-full md:w-auto md:min-w-[180px] shrink-0">
                    <?php if ($vaga->mostrar_salario && $vaga->salario > 0): ?>
                        <div class="text-center mb-2 hidden md:block">
                            <p class="text-sm text-slate-500">Salário / Benefício</p>
                            <p class="text-lg font-bold text-slate-800">R$ <?= number_format($vaga->salario, 2, ',', '.') ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="vagas.php" class="w-full">
                        <input type="hidden" name="action" value="candidatar">
                        <input type="hidden" name="vaga_id" value="<?= $vaga->id ?>">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors flex justify-center items-center">
                            Candidatar-me
                        </button>
                    </form>
                    <button class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold py-2.5 px-4 rounded-lg transition-colors flex justify-center items-center" onclick="alert('Detalhes da Vaga: Implementação de Modal/Página não solicitada neste recorte. Simulação de Detalhes ok.')">
                        Ver Detalhes
                    </button>
                </div>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-slate-900">Nenhuma vaga encontrada</h3>
            <p class="mt-1 text-sm text-slate-500">Tente ajustar seus termos de busca.</p>
        </div>
    <?php endif; ?>
</div>

    </main>
</body>
</html>
