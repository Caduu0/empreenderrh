<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
    <title>Super Admin - EmpreenderRH</title>
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
<body class="bg-gray-100 font-sans antialiased text-slate-800">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Admin -->
        <aside class="w-72 bg-slate-900 text-slate-300 flex flex-col shadow-2xl shrink-0 z-20">
            <div class="h-20 flex items-center px-8 border-b border-slate-800 bg-slate-950">
                <a href="../index.php"><span class="text-2xl font-bold text-white tracking-tight">Empreender<span class="text-blue-500">RH</span></span></a>
                <span class="ml-2 text-[10px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded border border-emerald-400/20">Admin</span>
            </div>
            
            <div class="flex-1 overflow-y-auto py-6">
                <nav class="space-y-2 px-4">
                    <a href="dashboard.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?= $current_page == 'dashboard.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="h-5 w-5 mr-3 <?= $current_page == 'dashboard.php' ? 'text-blue-200' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Dashboard
                    </a>
                    
                    <a href="usuarios.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?= $current_page == 'usuarios.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="h-5 w-5 mr-3 <?= $current_page == 'usuarios.php' ? 'text-blue-200' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Gestão de Usuários
                    </a>
                    
                    <a href="vagas.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?= $current_page == 'vagas.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="h-5 w-5 mr-3 <?= $current_page == 'vagas.php' ? 'text-blue-200' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Moderação de Vagas
                    </a>
                </nav>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900 flex justify-between items-center">
                <div class="truncate">
                    <p class="text-xs text-slate-500 uppercase font-semibold">Logado como</p>
                    <p class="text-sm font-bold text-white truncate max-w-[150px]"><?= htmlspecialchars($_SESSION['email']) ?></p>
                </div>
                <a href="../logout.php" title="Encerrar Sessão" class="bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white p-2 rounded-lg transition-colors shadow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col bg-slate-50 relative overflow-y-auto w-full">
            <div class="p-8 lg:p-12 max-w-screen-2xl mx-auto w-full">

<!-- TAGS FECHADAS EM OUTRO ARQUIVO -->