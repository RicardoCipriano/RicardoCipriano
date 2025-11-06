<?php
include 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$aniversariante = null;
$titulo = "Cadastrar Aniversariante";
$mensagem = '';

// Verifica se há uma mensagem de sucesso
if(isset($_GET['success'])) {
    $mensagem = '<div class="success-message">Dados salvos com sucesso! Você já pode cadastrar um novo aniversariante.</div>';
}

// Se tem ID, carrega os dados para edição (mas não mostra mensagem de sucesso)
if($id > 0) {
    $stmt = $conn->prepare("SELECT id, nome_completo AS nome, setor, data_nascimento, foto FROM pessoas WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $aniversariante = $result->fetch_assoc();
    
    if($aniversariante) {
        $titulo = "Editar Aniversariante";
        $data_formatada = date('Y-m-d', strtotime($aniversariante['data_nascimento']));
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], 
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .foto-atual {
            max-width: 200px;
            margin: 10px 0;
            display: block;
            border-radius: 4px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .voltar {
            color: #333;
            text-decoration: none;
        }
        .checkbox-group {
            margin: 10px 0;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #d6e9c6;
        }
        .btn-novo {
            background-color: #2196F3;
            margin-left: 10px;
        }
        .btn-novo:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><?= $titulo ?></h2>
        <?= $mensagem ?>
        <form action="salvar.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required 
                       value="<?= isset($aniversariante['nome']) ? htmlspecialchars($aniversariante['nome']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="setor">Setor:</label>
                <input type="text" id="setor" name="setor"
                       value="<?= isset($aniversariante['setor']) ? htmlspecialchars($aniversariante['setor']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required
                       value="<?= isset($data_formatada) ? $data_formatada : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="foto">Foto:</label>
                <input type="file" id="foto" name="foto" accept="image/*">
                
                <?php if(isset($aniversariante['foto']) && !empty($aniversariante['foto'])): ?>
                    <img src="<?= $aniversariante['foto'] ?>" class="foto-atual" alt="Foto atual">
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="remover_foto"> Remover foto atual
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="actions">
                <button type="submit" class="btn">Salvar</button>
                <div>
                    <a href="http://localhost:3000/" class="voltar">Voltar para o painel</a>
                    <?php if($id > 0): ?>
                        <a href="cadastro.php" class="btn btn-novo">Novo Cadastro</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</body>
</html>