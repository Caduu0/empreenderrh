<?php
$is_logged_in = isset($_SESSION['user_id']);
$dash_url = '#';

if ($is_logged_in) {
    if ($_SESSION['role'] === 'admin') {
        $dash_url = 'admin/dashboard.php';
    } elseif ($_SESSION['role'] === 'empresa') {
        $dash_url = 'empresa/dashboard.php';
    } else {
        $dash_url = 'candidato/dashboard.php';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmpreenderRH - Conectando Talentos e Oportunidades</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 flex flex-col min-h-screen font-sans text-slate-800 antialiased">
    
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="text-2xl font-extrabold tracking-tight text-slate-800 hover:opacity-80 transition-opacity">
                        Empreender<span class="text-brand-600">RH</span>
                    </a>
                </div>

                <!-- Menu (Desktop) -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors duration-300">Início</a>
                    <a href="#vagas_destaque" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors duration-300">Vagas</a>
                    <a href="#solucoes" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors duration-300">Soluções</a>
                    
                    <div class="flex items-center space-x-4 border-l border-slate-200 pl-8 ml-8">
                        <?php if($is_logged_in): ?>
                            <a href="<?= $dash_url ?>" class="text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors duration-300">Meu Painel</a>
                            <a href="logout.php" class="text-sm font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 px-4 py-2 rounded-lg transition-all duration-300">Sair</a>
                        <?php else: ?>
                            <a href="login.php" class="text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors duration-300">Entrar</a>
                            <a href="signup.php" class="text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-lg shadow-md shadow-brand-500/30 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 pulse-hover">Registar-se</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Botão menu (Mobile) -->
                <div class="flex items-center md:hidden">
                    <button type="button" class="text-slate-500 hover:text-brand-600 focus:outline-none transition-colors" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
                
            </div>
        </div>

        <!-- Menu (Mobile) -->
        <div class="md:hidden hidden bg-white border-t border-slate-100 absolute w-full shadow-lg" id="mobile-menu">
            <div class="px-4 py-3 space-y-1 sm:px-3 flex flex-col">
                <a href="index.php" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-brand-600 hover:bg-slate-50">Início</a>
                <a href="#vagas_destaque" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-brand-600 hover:bg-slate-50">Vagas</a>
                
                <hr class="my-2 border-slate-100">
                
                <?php if($is_logged_in): ?>
                    <a href="<?= $dash_url ?>" class="block px-3 py-3 rounded-md text-base font-bold text-brand-600 hover:bg-brand-50">Acessar Meu Painel</a>
                    <a href="logout.php" class="block px-3 py-3 rounded-md text-base font-medium text-rose-600 hover:bg-rose-50">Desconectar</a>
                <?php else: ?>
                    <a href="login.php" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-brand-600 hover:bg-slate-50">Fazer Login</a>
                    <a href="signup.php" class="block px-3 py-3 mt-2 rounded-md text-base font-bold bg-brand-600 text-white text-center shadow-md">Criar Conta Grátis</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="flex-grow w-full">

<!-- TAGS FECHADAS EM OUTRO ARQUIVO -->