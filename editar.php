<?php
include 'config.php';
if(!isset($_GET['id'])) exit("ID não informado");

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT id, nome_completo AS nome, setor, data_nascimento, foto FROM pessoas WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$aniversariante = $result->fetch_assoc();

if(!$aniversariante) exit("Aniversariante não encontrado");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Aniversariante</title>
</head>
<body>
<h1>Editar Aniversariante</h1>
<form action="salvar.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $aniversariante['id'] ?>">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($aniversariante['nome']) ?>" required>
    <label>Setor:</label>
    <input type="text" name="setor" value="<?= htmlspecialchars($aniversariante['setor']) ?>">
    <label>Data de Nascimento:</label>
    <input type="date" name="data_nascimento" value="<?= $aniversariante['data_nascimento'] ?>" required>
    <label>Foto atual:</label>
    <?php if($aniversariante['foto']): ?>
        <img src="<?= $aniversariante['foto'] ?>" width="100" alt="">
        <div>
            <label>
                <input type="checkbox" name="remover_foto"> Remover foto atual
            </label>
        </div>
    <?php endif; ?>
    <label>Alterar Foto:</label>
    <input type="file" name="foto" accept="image/*">
    <input type="submit" value="Atualizar">
</form>
<a href="painel.php">Voltar</a>
</body>
</html>
