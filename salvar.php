<?php
include 'config.php';

// Verifica se os campos obrigatórios foram enviados
if(empty($_POST['nome']) || empty($_POST['data_nascimento'])) {
    die("Nome e data de nascimento são obrigatórios");
}

$nome = $conn->real_escape_string($_POST['nome']);
$setor = isset($_POST['setor']) ? $conn->real_escape_string($_POST['setor']) : null;
$data_nascimento = $conn->real_escape_string($_POST['data_nascimento']);
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Tratamento da foto
$foto = null;

// Se está editando, pega a foto atual para manter caso não mude
if($id > 0) {
    $stmt = $conn->prepare("SELECT foto FROM pessoas WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $foto = $row['foto'];
}

// Se enviou nova foto
if(isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    // Verifica se é uma imagem
    $check = getimagesize($_FILES['foto']['tmp_name']);
    if($check !== false) {
        // Remove a foto antiga se existir
        if($foto && file_exists($foto)) {
            unlink($foto);
        }
        
        // Gera um nome único para o arquivo
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . '.' . $extensao;
        $diretorio = "uploads/";
        
        // Cria o diretório se não existir
        if(!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }
        
        $foto = $diretorio . $nome_arquivo;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }
} 
// Se marcou para remover a foto
elseif(isset($_POST['remover_foto'])) {
    if($foto && file_exists($foto)) {
        unlink($foto);
    }
    $foto = null;
}

// Prepara a query SQL
if($id > 0) {
    // Atualizar registro existente na tabela pessoas
    $sql = "UPDATE pessoas SET nome_completo=?, setor=?, data_nascimento=?, foto=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $setor, $data_nascimento, $foto, $id);
} else {
    // Inserir novo registro na tabela pessoas
    $sql = "INSERT INTO pessoas (nome_completo, setor, data_nascimento, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $setor, $data_nascimento, $foto);
}

if($stmt->execute()) {
    // Redireciona de volta ao painel se informado, senão para cadastro
    $redirect = isset($_POST['return_to']) && $_POST['return_to'] ? $_POST['return_to'] : 'cadastro.php?success=1';
    header("Location: " . $redirect);
} else {
    die("Erro ao salvar: " . $conn->error);
}

exit();
?>