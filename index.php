<?php
session_start();
require_once 'config/db.php';

// Busca até 6 vagas em destaque (recentes)
$sqlDestaques = "SELECT v.*, e.razao_social, e.nome_fantasia 
                 FROM vagas v 
                 JOIN empresas e ON v.empresa_id = e.id 
                 WHERE v.status = 'aberta' 
                 ORDER BY v.created_at DESC 
                 LIMIT 6";
$destaques = $pdo->query($sqlDestaques)->fetchAll();

include 'includes/header.php';
?>

<section class="relative bg-white overflow-hidden pb-16 lg:pb-24 pt-20">
    <div class="absolute inset-y-0 w-full h-full bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-50 via-white to-white opacity-60 z-0 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        
        <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
            Encontre o seu próximo <br class="hidden lg:block"/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-500">Desafio Profissional.</span>
        </h1>
        
        <p class="mt-4 text-lg md:text-xl text-slate-500 max-w-3xl mx-auto mb-10 leading-relaxed">
            Nossa plataforma une mentes brilhantes a empresas inovadoras. Construa sua carreira ou recrute o talento perfeito de maneira rápida, segura e escalável.
        </p>
        
        <!-- Botões CTA -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-16">
            <a href="signup.php" onclick="document.cookie='role_choice=candidato; path=/;'" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-brand-600/30 transition-all duration-300 hover:-translate-y-1 transform text-lg">
                Sou Candidato <span class="ml-2">&rarr;</span>
            </a>
            <a href="signup.php" onclick="document.cookie='role_choice=empresa; path=/;'" class="bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 font-bold py-4 px-8 rounded-xl shadow-sm transition-all duration-300 hover:-translate-y-1 transform text-lg hover:border-slate-300">
                Sou Empresa
            </a>
        </div>

        <!-- Search Bar Rapida -->
        <div class="max-w-4xl mx-auto bg-white p-2 rounded-2xl shadow-xl border border-slate-100 flex flex-col md:flex-row gap-2 relative z-20">
            <div class="flex-grow flex items-center px-4 bg-slate-50 rounded-xl">
                <svg class="h-6 w-6 text-slate-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Qual cargo ou empresa você busca?" class="w-full bg-transparent py-4 focus:outline-none text-slate-800 font-medium placeholder-slate-400">
            </div>
            <a href="login.php" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold py-4 px-8 rounded-xl transition-colors duration-300 flex-shrink-0 text-center flex items-center justify-center">
                Pesquisar Vagas
            </a>
        </div>
    </div>
</section>

<section id="solucoes" class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-slate-800 mb-4">Por que escolher a EmpreenderRH?</h2>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg">Uma arquitetura projetada do zero para mitigar burocracias, com foco absoluto em Conversão de talentos e Segurança.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6 text-brand-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Rapidez no Match</h3>
                <p class="text-slate-500 leading-relaxed text-sm">Esqueça processos densos. Os perfis e as vagas cruzam-se instantaneamente graças à listagem algorítmica veloz de nosso banco de dados.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group mt-0 md:mt-8">
                <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-6 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Extrema Facilidade</h3>
                <p class="text-slate-500 leading-relaxed text-sm">Tanto interfaces empresariais quanto submissão de currículos foram desenhadas num painel com base UX Clean-slate sem fricção e totalmente Mobile.</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center mb-6 text-slate-800 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Auditoria & Segurança</h3>
                <p class="text-slate-500 leading-relaxed text-sm">Dados sigilosos repousam através da blindagem PDO Hash. Autenticações em tokens robustos impedem interceptações nocivas.</p>
            </div>
            
        </div>
    </div>
</section>

<section id="vagas_destaque" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 mb-2">Vagas em Destaque</h2>
                <p class="text-slate-500">As oportunidades mais frescas esperando sua aplicação.</p>
            </div>
            <a href="login.php" class="text-brand-600 font-semibold hover:text-brand-800 flex items-center transition-colors">
                Ver todas as vagas <span class="ml-2">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if(count($destaques) > 0): ?>
                <?php foreach($destaques as $v): ?>
                    <a href="login.php" class="block bg-slate-50 border border-slate-100 rounded-2xl p-6 hover:shadow-lg hover:border-brand-200 transition-all duration-300 group flex flex-col justify-between h-full">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full"><?= htmlspecialchars($v->tipo_contrato) ?></span>
                                <span class="text-slate-400 text-xs font-medium"><?= date('d M', strtotime($v->created_at)) ?></span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-600 transition-colors mb-2 line-clamp-2">
                                <?= htmlspecialchars($v->titulo) ?>
                            </h3>
                            
                            <p class="text-sm font-medium text-slate-600 mb-4 truncate w-full flex items-center">
                                <svg class="h-4 w-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <?= htmlspecialchars($v->nome_fantasia ?: $v->razao_social) ?>
                            </p>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-200/60 flex items-center text-sm text-slate-500">
                            <svg class="h-4 w-4 mr-1.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?= htmlspecialchars($v->cidade) ?> - <?= htmlspecialchars($v->estado) ?> <span class="mx-2">•</span> <?= htmlspecialchars($v->modalidade) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-16 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                    <p class="text-slate-500 font-medium text-lg">As vagas abrirão em breve! Registe-se para ser o primeiro a saber.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>