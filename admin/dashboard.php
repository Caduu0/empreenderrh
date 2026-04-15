<?php
session_start();
require_once '../config/db.php';

// Busca dados analíticos
$stmtCandidatos = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'candidato'");
$dash_candidatos = $stmtCandidatos->fetch()->total;

$stmtEmpresas = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'empresa'");
$dash_empresas = $stmtEmpresas->fetch()->total;

$stmtVagasAtivas = $pdo->query("SELECT COUNT(*) as total FROM vagas WHERE status = 'aberta'");
$dash_vagas_ativas = $stmtVagasAtivas->fetch()->total;

$stmtCandidaturas = $pdo->query("SELECT COUNT(*) as total FROM candidaturas");
$dash_candidaturas = $stmtCandidaturas->fetch()->total;

// Busca Atividade Recente (Últimos 10 usuários cadatrados na plataforma)
$sqlRecentes = "SELECT u.id, u.email, u.role, u.status, u.created_at, 
                COALESCE(c.nome_completo, e.razao_social, 'Administrador') as nome 
                FROM users u 
                LEFT JOIN candidatos c ON u.id = c.user_id 
                LEFT JOIN empresas e ON u.id = e.user_id 
                ORDER BY u.created_at DESC 
                LIMIT 10";
$recentes = $pdo->query($sqlRecentes)->fetchAll();

// Traz validação de sessão embutida, o Sidebar e abre as tags de main content
include 'includes/sidebar.php';
?>

<div class="mb-10">
    <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-800 tracking-tight">Centro de <span class="text-blue-600">Comando</span></h1>
    <p class="text-slate-500 mt-2 text-lg">Visão holística sobre a tração, volume e métricas de toda plataforma EmpreenderRH.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden group hover:shadow-md transition duration-300">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-widest">Candidatos</h3>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:scale-110 transition-transform"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            </div>
            <p class="text-4xl font-black text-slate-800"><?= number_format($dash_candidatos, 0, ',', '.') ?></p>
            <p class="text-xs text-blue-600 font-medium mt-2">&rarr; perfis ativos buscando vagas</p>
        </div>
        <div class="h-1 w-full bg-blue-600"></div>
    </div>
    
    <!-- Empresas -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden group hover:shadow-md transition duration-300">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-widest">Empresas</h3>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg group-hover:scale-110 transition-transform"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
            </div>
            <p class="text-4xl font-black text-slate-800"><?= number_format($dash_empresas, 0, ',', '.') ?></p>
            <p class="text-xs text-purple-600 font-medium mt-2">&rarr; corporações recrutando</p>
        </div>
        <div class="h-1 w-full bg-purple-600"></div>
    </div>
    
    <!-- Vagas -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden group hover:shadow-md transition duration-300">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-widest">Vagas Abertas</h3>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg group-hover:scale-110 transition-transform"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            </div>
            <p class="text-4xl font-black text-slate-800"><?= number_format($dash_vagas_ativas, 0, ',', '.') ?></p>
            <p class="text-xs text-emerald-600 font-medium mt-2">&rarr; oportunidades globais livres</p>
        </div>
        <div class="h-1 w-full bg-emerald-500"></div>
    </div>
    
    <!-- Movimento (Candidaturas) -->
    <div class="bg-slate-900 rounded-2xl shadow-xl overflow-hidden group">
        <div class="p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-10">
                <svg class="h-32 w-32 text-indigo-100" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">Interações (Aplicações)</h3>
                <p class="text-4xl font-black text-white"><?= number_format($dash_candidaturas, 0, ',', '.') ?></p>
                <p class="text-xs text-indigo-300 font-medium mt-4 border-t border-slate-700 pt-2 block">Total de matches criados via sistema</p>
            </div>
        </div>
    </div>
</div>

<!-- Atividade Recente -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">Atividade Recente (Novas Contas)</h2>
        <a href="usuarios.php" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Visualizar Todos &rarr;</a>
    </div>
    <div class="p-0">
        <?php if(count($recentes) > 0): ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach($recentes as $u): ?>
                    <li class="p-6 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center">
                            <?php if($u->role === 'candidato'): ?>
                                <span class="h-12 w-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold mr-4 shrink-0 shadow-sm border border-blue-200">C</span>
                            <?php elseif($u->role === 'empresa'): ?>
                                <span class="h-12 w-12 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold mr-4 shrink-0 shadow-sm border border-purple-200">E</span>
                            <?php else: ?>
                                <span class="h-12 w-12 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold mr-4 shrink-0 shadow-sm border border-slate-700">A</span>
                            <?php endif; ?>
                            
                            <div>
                                <p class="text-base font-bold text-slate-800 mb-0.5"><?= htmlspecialchars($u->nome) ?></p>
                                <p class="text-sm text-slate-500 font-medium"><?= htmlspecialchars($u->email) ?> &bull; 
                                    <span class="inline-flex items-center capitalize <?php echo $u->status === 'banido' ? 'text-red-500 line-through' : 'text-emerald-600'; ?>"><?= $u->status ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-slate-600 bg-slate-100 px-3 py-1 rounded-full capitalize block mb-1"><?= htmlspecialchars($u->role) ?></span>
                            <span class="text-xs text-slate-400 font-medium"><?= date('d/m/Y', strtotime($u->created_at)) ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="p-10 text-center text-slate-500">Nenhuma conta encontrada na base.</div>
        <?php endif; ?>
    </div>
</div>

            </div> <!--Fecha max-w da view iniciada no sidebar -->
        </main> <!-- Fecha o MAIN iniciado no sidebar -->
    </div> <!-- Fecha o FLEX HEIGHT iniciado no sidebar -->
</body>
</html>
