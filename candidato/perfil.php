<?php
// candidato/perfil.php
session_start();
require_once '../config/db.php';

// Garantir que as colunas extras de currículo existam no MySQL graciosamente (Setup inicial on-the-fly)
try {
    $pdo->exec("ALTER TABLE candidatos ADD COLUMN foto_perfil VARCHAR(255) AFTER resumo_profissional");
} catch(Exception $e) {}
try {
    $pdo->exec("ALTER TABLE candidatos ADD COLUMN formacao TEXT AFTER resumo_profissional");
} catch(Exception $e) {}
try {
    $pdo->exec("ALTER TABLE candidatos ADD COLUMN experiencia_profissional TEXT AFTER resumo_profissional");
} catch(Exception $e) {}

$error = '';
$success = '';

// Buscar dados atuais
$stmt = $pdo->prepare("SELECT * FROM candidatos WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$perfil = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
    $experiencia = $_POST['experiencia_profissional'] ?? '';
    $formacao = $_POST['formacao'] ?? '';
    $linkedin = $_POST['linkedin_url'] ?? '';
    $portfolio = $_POST['portfolio_url'] ?? '';

    // Manejo passivo de Foto de Perfil (base64 estático ou upload real se quiser avançar)
    // Para simplificar, vou permitir uma URL de imagem ou salvar diretamente
    $foto_url = $perfil->foto_perfil ?? '';
    if (isset($_POST['foto_url']) && filter_var($_POST['foto_url'], FILTER_VALIDATE_URL)) {
        $foto_url = $_POST['foto_url'];
    }

    try {
        $sql = "UPDATE candidatos SET 
                telefone = :telefone,
                experiencia_profissional = :experiencia,
                formacao = :formacao,
                linkedin_url = :linkedin,
                portfolio_url = :portfolio,
                foto_perfil = :foto_perfil,
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = :user_id";
        
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute([
            ':telefone' => $telefone,
            ':experiencia' => $experiencia,
            ':formacao' => $formacao,
            ':linkedin' => $linkedin,
            ':portfolio' => $portfolio,
            ':foto_perfil' => $foto_url,
            ':user_id' => $_SESSION['user_id']
        ]);
        
        $success = "Perfil atualizado com sucesso!";
        
        // Atualiza a variável local para mostrar na view
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $perfil = $stmt->fetch();
        
    } catch (PDOException $e) {
        $error = "Erro ao atualizar perfil. " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-extrabold text-slate-800">Meu <span class="text-blue-600">Perfil</span></h1>
        <span class="bg-green-100 text-green-700 font-semibold px-3 py-1 rounded-full text-sm">Candidato</span>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-sm">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- HEADER DO FORMULÁRIO -->
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
            <div class="relative group">
                <div class="h-24 w-24 rounded-full border-4 border-white shadow-md overflow-hidden bg-slate-200 flex items-center justify-center">
                    <?php if(!empty($perfil->foto_perfil)): ?>
                        <img src="<?= htmlspecialchars($perfil->foto_perfil) ?>" alt="Foto" class="h-24 w-24 object-cover">
                    <?php else: ?>
                        <span class="text-slate-400 font-bold text-3xl"><?= substr($perfil->nome_completo, 0, 1) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($perfil->nome_completo) ?></h2>
                <p class="text-slate-500">CPF: <?= htmlspecialchars($perfil->cpf) ?> <span class="mx-2">|</span> <?= htmlspecialchars($_SESSION['email']) ?></p>
            </div>
        </div>

        <div class="p-8 space-y-8">
            
            <!-- SEC INFO BASICA -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Informações de Contato</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" value="<?= htmlspecialchars($perfil->telefone ?? '') ?>" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Ex: (11) 99999-9999">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">URL da Foto de Perfil</label>
                        <input type="url" name="foto_url" value="<?= htmlspecialchars($perfil->foto_perfil ?? '') ?>" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="https://link-da-imagem.com/foto.jpg">
                    </div>
                </div>
            </div>

            <!-- SEC PROFISSIONAL -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Resumo Profissional</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Experiência Profissional</label>
                        <textarea name="experiencia_profissional" rows="4" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Onde você já trabalhou? Quais eram suas responsabilidades?"><?= htmlspecialchars($perfil->experiencia_profissional ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Formação Acadêmica</label>
                        <textarea name="formacao" rows="3" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Cursos, graduações, certificações..."><?= htmlspecialchars($perfil->formacao ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEC LINKS -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Redes Profissionais e Portfólio</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="<?= htmlspecialchars($perfil->linkedin_url ?? '') ?>" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="https://linkedin.com/in/seu-perfil">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Site / Portfólio</label>
                        <input type="url" name="portfolio_url" value="<?= htmlspecialchars($perfil->portfolio_url ?? '') ?>" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="https://seu-portfolio.com">
                    </div>
                </div>
            </div>

        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-blue-600/30">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

    </main>
</body>
</html>
