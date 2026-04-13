<?php
session_start();
require_once 'config/db.php';

// Redireciona se já estiver logado
if (isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'candidato';
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Campos Candidato
    $nome_completo = $_POST['nome_completo'] ?? '';
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');

    // Campos Empresa
    $razao_social = $_POST['razao_social'] ?? '';
    $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj'] ?? '');

    // Validação básica
    if (empty($email) || empty($password) || empty($password_confirm)) {
        $error = "Preencha os campos obrigatórios (E-mail e Senhas).";
    } elseif ($password !== $password_confirm) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter no mínimo 6 caracteres.";
    } else {
        if ($role === 'candidato' && (empty($nome_completo) || empty($cpf))) {
            $error = "Nome e CPF são obrigatórios para candidatos.";
        } elseif ($role === 'empresa' && (empty($razao_social) || empty($cnpj))) {
            $error = "Razão Social e CNPJ são obrigatórios para empresas.";
        } else {
            try {
                // Iniciar transação, já que criaremos em duas tabelas
                $pdo->beginTransaction();

                // Verificar se o e-mail já existe
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute([':email' => $email]);
                if ($stmt->fetch()) {
                    throw new Exception("Este e-mail já está em uso.");
                }

                // Inserir usuário na tabela users
                $pwd_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (:email, :hash, :role)");
                $stmt->execute([
                    ':email' => $email,
                    ':hash' => $pwd_hash,
                    ':role' => $role
                ]);
                
                $user_id = $pdo->lastInsertId();

                // Inserir perfil associado
                if ($role === 'candidato') {
                    $stmtProfile = $pdo->prepare("INSERT INTO candidatos (user_id, nome_completo, cpf) VALUES (:user_id, :nome, :cpf)");
                    $stmtProfile->execute([
                        ':user_id' => $user_id,
                        ':nome' => $nome_completo,
                        ':cpf' => $cpf
                    ]);
                } else if ($role === 'empresa') {
                    $stmtProfile = $pdo->prepare("INSERT INTO empresas (user_id, razao_social, cnpj) VALUES (:user_id, :razao, :cnpj)");
                    $stmtProfile->execute([
                        ':user_id' => $user_id,
                        ':razao' => $razao_social,
                        ':cnpj' => $cnpj
                    ]);
                }

                // Confirma as inserções
                $pdo->commit();
                $success = "Conta criada com sucesso! Você já pode fazer login.";

            } catch (Exception $e) {
                // Reverte transação em caso de erro
                $pdo->rollBack();
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    if (strpos($e->getMessage(), 'cpf') !== false) $error = "Este CPF já está cadastrado.";
                    elseif (strpos($e->getMessage(), 'cnpj') !== false) $error = "Este CNPJ já está cadastrado.";
                    else $error = "Erro ao cadastrar. Verifique se seus dados já não estão em uso.";
                } else {
                    $error = $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - EmpreenderRH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-indigo-50 min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden drop-shadow-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-600 mb-2">Empreender<span class="text-slate-800">RH</span></h1>
            <p class="text-slate-500">Crie sua conta para encontrar a oportunidade perfeita ou o candidato ideal.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm font-medium border border-red-100 text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm font-medium border border-green-100 text-center">
                <?php echo htmlspecialchars($success); ?>
                <br>
                <a href="login.php" class="inline-block mt-3 text-white bg-green-600 hover:bg-green-700 px-6 py-2 rounded-lg font-semibold transition-colors">Ir para o Login</a>
            </div>
        <?php else: ?>

        <form method="POST" action="signup.php" class="space-y-6">
            
            <!-- Type Selector -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <input type="radio" id="role_candidato" name="role" value="candidato" class="peer hidden" checked onchange="toggleForm()">
                    <label for="role_candidato" class="block cursor-pointer text-center px-4 py-3 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 font-semibold text-slate-600 peer-checked:text-blue-700 transition-all">
                        Sou Candidato
                    </label>
                </div>
                <div>
                    <input type="radio" id="role_empresa" name="role" value="empresa" class="peer hidden" onchange="toggleForm()">
                    <label for="role_empresa" class="block cursor-pointer text-center px-4 py-3 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 font-semibold text-slate-600 peer-checked:text-blue-700 transition-all">
                        Sou Empresa
                    </label>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Credentials -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">E-mail</label>
                    <input type="email" name="email" required 
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                </div>
                <div class="hidden md:block"></div> <!-- spacer -->
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Senha</label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Confirme a Senha</label>
                    <input type="password" name="password_confirm" required 
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Candidato Fields -->
            <div id="candidato_fields" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nome Completo</label>
                        <input type="text" name="nome_completo" id="input_nome"
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">CPF</label>
                        <input type="text" name="cpf" id="input_cpf" placeholder="000.000.000-00"
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                    </div>
                </div>
            </div>

            <!-- Empresa Fields -->
            <div id="empresa_fields" class="space-y-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Razão Social</label>
                        <input type="text" name="razao_social" id="input_razao"
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">CNPJ</label>
                        <input type="text" name="cnpj" id="input_cnpj" placeholder="00.000.000/0000-00"
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-400">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 mt-4 rounded-lg transition-colors shadow-lg shadow-blue-600/30">
                Finalizar Cadastro
            </button>
        </form>
        <?php endif; ?>
        
        <div class="mt-8 text-center">
            <p class="text-slate-500 text-sm">Já possui uma conta? 
                <a href="login.php" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">Faça login</a>
            </p>
        </div>
    </div>

    <script>
        function toggleForm() {
            const isEmpresa = document.getElementById('role_empresa').checked;
            
            const candidatoFields = document.getElementById('candidato_fields');
            const empresaFields = document.getElementById('empresa_fields');
            
            if (isEmpresa) {
                candidatoFields.classList.add('hidden');
                empresaFields.classList.remove('hidden');
                
                document.getElementById('input_nome').removeAttribute('required');
                document.getElementById('input_cpf').removeAttribute('required');
                document.getElementById('input_razao').setAttribute('required', 'required');
                document.getElementById('input_cnpj').setAttribute('required', 'required');
            } else {
                candidatoFields.classList.remove('hidden');
                empresaFields.classList.add('hidden');
                
                document.getElementById('input_nome').setAttribute('required', 'required');
                document.getElementById('input_cpf').setAttribute('required', 'required');
                document.getElementById('input_razao').removeAttribute('required');
                document.getElementById('input_cnpj').removeAttribute('required');
            }
        }
        
        // Initialize state on load
        window.addEventListener('DOMContentLoaded', toggleForm);
    </script>
</body>
</html>
