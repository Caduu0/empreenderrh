<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'empresa') {
    header('Location: ../login.php');
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Empresa - EmpreenderRH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-slate-800 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Botão (Mobile) -->
                <div class="flex items-center md:hidden">
                    <button type="button" class="text-slate-300 hover:text-white focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
                
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="../index.php"><span class="text-xl font-bold tracking-tight">Empreender<span class="text-blue-400">RH</span> <span class="text-xs text-slate-400 ml-1 uppercase">Empresas</span></span></a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="dashboard.php" class="px-3 py-2 rounded-md font-medium transition-colors <?= $current_page == 'dashboard.php' ? 'bg-slate-900 text-white shadow-inner' : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">Dashboard</a>
                            <a href="vagas.php" class="px-3 py-2 rounded-md font-medium transition-colors <?= $current_page == 'vagas.php' ? 'bg-slate-900 text-white shadow-inner' : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">Gerenciar Vagas</a>
                            <a href="postar-vaga.php" class="px-3 py-2 rounded-md font-medium transition-colors <?= $current_page == 'postar-vaga.php' ? 'bg-slate-900 text-white shadow-inner' : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">Nova Vaga</a>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        <span class="text-sm font-medium text-slate-300"><?= htmlspecialchars($_SESSION['email']) ?></span>
                        <a href="../logout.php" class="border border-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors">Sair</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu (Mobile) -->
        <div class="md:hidden hidden bg-slate-900" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-700">Dashboard</a>
                <a href="vagas.php" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-700 hover:text-white">Gerenciar Vagas</a>
                <a href="postar-vaga.php" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-700 hover:text-white">Nova Vaga</a>
                <a href="../logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-red-400 hover:bg-slate-800 mt-4 border-t border-slate-800 pt-4">Sair</a>
            </div>
        </div>
    </nav>
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">

<!-- TAGS FECHADAS EM OUTRO ARQUIVO -->