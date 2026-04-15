<?php
session_start();
require_once '../config/db.php';

// Obtém o ID da empresa
$stmt = $pdo->prepare("SELECT id FROM empresas WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$empresa = $stmt->fetch();
$empresa_id = $empresa->id ?? null;

if (!$empresa_id) {
    die("Erro grave. Sua conta não possui vinculo de empresa ativo.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $requisitos = filter_input(INPUT_POST, 'requisitos', FILTER_SANITIZE_STRING);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    $salario = filter_input(INPUT_POST, 'salario', FILTER_VALIDATE_FLOAT) ?: null;
    $tipo_contrato = $_POST['tipo_contrato'] ?? 'CLT';
    $modalidade = $_POST['modalidade'] ?? 'Presencial';

    if (empty($titulo) || empty($descricao) || empty($requisitos) || empty($cidade) || empty($estado)) {
        $error = "Preencha todos os campos obrigatórios!";
    } else {
        try {
            $sql = "INSERT INTO vagas (empresa_id, titulo, descricao, requisitos, salario, tipo_contrato, modalidade, cidade, estado, status) 
                    VALUES (:empresa_id, :titulo, :descricao, :requisitos, :salario, :tipo, :modalidade, :cidade, :estado, 'aberta')";
            $stmtInsert = $pdo->prepare($sql);
            $stmtInsert->execute([
                ':empresa_id' => $empresa_id,
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':requisitos' => $requisitos,
                ':salario' => $salario,
                ':tipo' => $tipo_contrato,
                ':modalidade' => $modalidade,
                ':cidade' => $cidade,
                ':estado' => $estado
            ]);
            $success = "Vaga publicada com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro no servidor ao publicar a vaga. Tente novamente.";
            error_log($e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-800">Publicar <span class="text-blue-600">Nova Vaga</span></h1>
        <p class="text-slate-500 mt-1">Preencha os dados com clareza para atrair os melhores talentos para sua equipe.</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-sm flex flex-col sm:flex-row sm:items-center justify-between">
            <span><?= htmlspecialchars($success) ?></span>
            <a href="vagas.php" class="mt-2 sm:mt-0 font-bold text-emerald-800 hover:text-emerald-900 underline text-sm">Ver Minhas Vagas</a>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Título da Vaga *</label>
                    <input type="text" name="titulo" required placeholder="Ex: Desenvolvedor Front-end Pleno" 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tipo de Contrato *</label>
                    <select name="tipo_contrato" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                        <option value="CLT">CLT (Registro em Carteira)</option>
                        <option value="PJ">Pessoa Jurídica (PJ)</option>
                        <option value="Estagio">Estágio</option>
                        <option value="Freelancer">Freelancer</option>
                        <option value="Temporario">Temporário</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Modalidade do Formato *</label>
                    <select name="modalidade" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                        <option value="Presencial">Presencial</option>
                        <option value="Remoto">100% Remoto</option>
                        <option value="Híbrido">Híbrido</option>
                    </select>
                </div>
            </div>

            <div class="pb-6 border-b border-slate-100 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Descrição Completa da Posição *</label>
                    <textarea name="descricao" rows="5" required placeholder="O que esse profissional irá fazer no dia a dia da empresa?" 
                              class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quais são os Requisitos? *</label>
                    <textarea name="requisitos" rows="4" required placeholder="Habilidades, anos de experiência e formações necessárias..." 
                              class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Cidade Base *</label>
                    <input type="text" name="cidade" required placeholder="Ex: São Paulo" 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Estado (UF) *</label>
                    <input type="text" name="estado" required placeholder="Ex: SP" maxlength="2"
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Salário <span class="font-normal text-slate-400 text-xs">(Opcional)</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-medium">R$</span>
                        </div>
                        <input type="number" step="0.01" name="salario" placeholder="0.00" 
                               class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="reset" class="px-6 py-3 font-semibold text-slate-600 hover:text-slate-800 transition-colors">Limpar</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-blue-600/30">
                Publicar Vaga
            </button>
        </div>
    </form>
</div>

    </main>
</body>
</html>