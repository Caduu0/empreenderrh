<?php
session_start();
require_once 'config/db.php';

// Redireciona se já estiver logado
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header('Location: admin/dashboard.php');
    elseif ($_SESSION['role'] === 'empresa') header('Location: empresa/dashboard.php');
    else header('Location: candidato/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user->password_hash)) {
                if ($user->status !== 'ativo') {
                    $error = "Sua conta está inativa ou foi banida. Contate o suporte.";
                } else {
                    // Regenerar ID da sessão por segurança para evitar Session Fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['role'] = $user->role;
                    $_SESSION['email'] = $user->email;

                    // Redirecionamento baseado na role
                    if ($user->role === 'admin') header('Location: admin/dashboard.php');
                    elseif ($user->role === 'empresa') header('Location: empresa/dashboard.php');
                    else header('Location: candidato/dashboard.php');
                    exit;
                }
            } else {
                $error = "E-mail ou senha inválidos.";
            }
        } catch (PDOException $e) {
            $error = "Erro no servidor. Tente novamente.";
            error_log($e->getMessage());
        }
    } else {
        $error = "Preencha todos os campos de e-mail e senha.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EmpreenderRH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex h-screen items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden drop-shadow-md">
        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-blue-600 mb-2">Empreender<span class="text-slate-800">RH</span></h1>
                <p class="text-slate-500">Bem-vindo de volta! Faça login na sua conta.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm font-medium border border-red-100 text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">E-mail</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                           placeholder="seu@email.com">
                </div>
                
                <div>
                    <div class="flex justify-between mb-2">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Senha</label>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">Esqueceu a senha?</a>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                           placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-colors shadow-lg shadow-blue-600/30">
                    Entrar na Plataforma
                </button>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm">Ainda não tem uma conta? 
                    <a href="signup.php" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">Cadastre-se grátis</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
