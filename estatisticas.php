<?php
include 'config.php';

// Consulta para contar aniverssariantes por mês (tabela pessoas)
$sql_por_mes = "SELECT MONTH(data_nascimento) as mes, COUNT(*) as total 
               FROM pessoas 
               GROUP BY MONTH(data_nascimento) 
               ORDER BY MONTH(data_nascimento)";
$result_por_mes = $conn->query($sql_por_mes);

// Array para armazenar os dados por mês
$dados_por_mes = array_fill(1, 12, 0); // Inicializa com zero para todos os meses

// Preenche o array com os dados reais (com checagem de erro)
if ($result_por_mes) {
    while ($row = $result_por_mes->fetch_assoc()) {
        $dados_por_mes[$row['mes']] = $row['total'];
    }
}

// Array com nomes dos meses
$meses_nomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
    4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
    10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Consulta para total de aniverssariantes
$sql_total = "SELECT COUNT(*) as total FROM pessoas";
$result_total = $conn->query($sql_total);
$total_aniverssariantes = $result_total ? $result_total->fetch_assoc()['total'] : 0;

// Consulta para próximos aniversários
$sql_proximos = "SELECT nome_completo AS nome, data_nascimento, 
                DATEDIFF(
                    DATE_ADD(
                        data_nascimento,
                        INTERVAL YEAR(CURDATE())-YEAR(data_nascimento) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(data_nascimento), 1, 0) YEAR
                    ),
                    CURDATE()
                ) as dias_restantes
                FROM pessoas
                WHERE DATEDIFF(
                    DATE_ADD(
                        data_nascimento,
                        INTERVAL YEAR(CURDATE())-YEAR(data_nascimento) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(data_nascimento), 1, 0) YEAR
                    ),
                    CURDATE()
                ) BETWEEN 0 AND 30
                ORDER BY dias_restantes";
$result_proximos = $conn->query($sql_proximos);
$proximos_aniversarios = $result_proximos ? $result_proximos->fetch_all(MYSQLI_ASSOC) : [];

// Consulta para distribuição por dia da semana
$sql_dia_semana = "SELECT WEEKDAY(data_nascimento) as dia_semana, COUNT(*) as total 
                  FROM pessoas 
                  GROUP BY WEEKDAY(data_nascimento) 
                  ORDER BY WEEKDAY(data_nascimento)";
$result_dia_semana = $conn->query($sql_dia_semana);

// Array para armazenar os dados por dia da semana
$dados_dia_semana = array_fill(0, 7, 0); // Inicializa com zero para todos os dias

// Preenche o array com os dados reais (com checagem de erro)
if ($result_dia_semana) {
    while ($row = $result_dia_semana->fetch_assoc()) {
        $dados_dia_semana[$row['dia_semana']] = $row['total'];
    }
}

// Array com nomes dos dias da semana
$dias_semana_nomes = [
    0 => 'Segunda', 1 => 'Terça', 2 => 'Quarta',
    3 => 'Quinta', 4 => 'Sexta', 5 => 'Sábado', 6 => 'Domingo'
];

// Lista completa para edição
$sql_pessoas = "SELECT id, nome_completo AS nome, setor, foto, DATE_FORMAT(data_nascimento, '%Y-%m-%d') AS data_iso
                FROM pessoas
                ORDER BY nome_completo";
$result_pessoas = $conn->query($sql_pessoas);
$lista_pessoas = $result_pessoas ? $result_pessoas->fetch_all(MYSQLI_ASSOC) : [];

// Consulta para distribuição por setor
$sql_por_setor = "SELECT COALESCE(setor, '(Sem setor)') AS setor, COUNT(*) AS total\n                 FROM pessoas\n                 GROUP BY setor\n                 ORDER BY total DESC, setor ASC";
$result_por_setor = $conn->query($sql_por_setor);
$dados_por_setor = $result_por_setor ? $result_por_setor->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas de aniverssariantes</title>
    <link rel="stylesheet" href="style.css">
    <!-- Biblioteca Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-y: auto;
            background-attachment: fixed;
        }
        
        .stats-container {
            max-width: 1200px;
            margin: 10px auto;
            padding: 15px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            color: #fff;
            height: calc(100vh - 50px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .stats-header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .stats-header h1 {
            margin: 0 0 5px 0;
            font-size: 1.8rem;
        }
        
        .stats-header p {
            margin: 0;
            font-size: 0.9rem;
        }
        
        .stats-content {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .stats-card {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
            height: auto;
            display: flex;
            flex-direction: column;
        }
        
        .stats-card h3 {
            color: #FFD700;
            margin-top: 0;
            margin-bottom: 5px;
            border-bottom: 1px solid #FFD700;
            padding-bottom: 5px;
            font-size: 1rem;
        }
        
        .chart-container {
            position: relative;
            height: 180px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .big-number {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .big-number p {
            margin: 5px 0 0 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        
        .data-table th, .data-table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .data-table th {
            color: #FFD700;
        }
        
        .highlight {
            color: #FFD700;
            font-weight: bold;
        }
        
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .btn {
            background-color: #FFD700;
            color: #333;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .btn:hover {
            background-color: #FFC000;
            transform: translateY(-2px);
        }
        
        /* Barra de rolagem personalizada */
        .stats-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .stats-content::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }
        
        .stats-content::-webkit-scrollbar-thumb {
            background: rgba(255, 215, 0, 0.5);
            border-radius: 10px;
        }
        
        .stats-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 215, 0, 0.7);
        }
        
        /* Ajuste para tabela de detalhamento por mês */
        .month-detail {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .month-column {
            display: flex;
            flex-direction: column;
        }
        
        .month-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        }
        
        .month-name {
            font-weight: bold;
        }
        
        /* Garantir que os gráficos sejam exibidos corretamente */
        canvas {
            max-width: 100%;
            max-height: 100%;
            width: auto !important;
            height: auto !important;
        }
    </style>
</head>
<body>
    <div class="stats-container">
        <div class="stats-header">
            <h1>Estatísticas de aniverssariantes</h1>
            <p>Visualize dados e tendências sobre os aniverssariantes cadastrados</p>
        </div>
        
        <div class="stats-content">
            <!-- Primeira linha: 2 cards lado a lado -->
            <div class="stats-grid">
                <div class="stats-card">
                    <h3>Total de aniverssariantes</h3>
                    <div class="big-number">
                        <span class="highlight" style="font-size: 2.5rem;"><?= $total_aniverssariantes ?></span>
                        <p>pessoas cadastradas</p>
                    </div>
                </div>
                
                <div class="stats-card">
                    <h3>Distribuição por Mês</h3>
                    <div class="chart-container" style="position: relative; height: 100%; width: 100%;">
                        <canvas id="chartMeses"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Segunda linha: 2 cards lado a lado -->
            <div class="stats-grid">
                <div class="stats-card">
                    <h3>Distribuição por Setor</h3>
                    <div style="display:grid;grid-template-columns: repeat(2, 1fr);gap:10px;">
                        <?php if(count($dados_por_setor) > 0): ?>
                            <?php foreach($dados_por_setor as $s): ?>
                                <div style="display:flex;justify-content:space-between;padding:6px 8px;border-bottom:1px solid rgba(255,215,0,0.2);">
                                    <span class="month-name"><?= htmlspecialchars($s['setor']) ?></span>
                                    <span>
                                        <?= (int)$s['total'] ?>
                                        <?php if ($total_aniverssariantes > 0): ?>
                                            (<?= number_format(($s['total'] / $total_aniverssariantes) * 100, 1) ?>%)
                                        <?php else: ?>
                                            (0%)
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Não há setores cadastrados.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="stats-card">
                    <h3>Próximos Aniversários (30 dias)</h3>
                    <div style="overflow-y: auto; flex: 1;">
                        <?php if (count($proximos_aniversarios) > 0): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Data</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($proximos_aniversarios as $aniv): ?>
                                        <tr>
                                            <td><?= $aniv['nome'] ?></td>
                                            <td><?= date('d/m', strtotime($aniv['data_nascimento'])) ?></td>
                                            <td>
                                                <?php if ($aniv['dias_restantes'] == 0): ?>
                                                    <span class="highlight">Hoje!</span>
                                                <?php else: ?>
                                                    <?= $aniv['dias_restantes'] ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Não há aniversários nos próximos 30 dias.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Detalhamento por mês em uma linha separada -->
            <div class="stats-card">
                <h3>Detalhamento por Mês</h3>
                <div class="month-detail">
                    <div class="month-column">
                        <?php $count = 0; ?>
                        <?php foreach ($dados_por_mes as $mes => $total): ?>
                            <?php if ($count < 4): ?>
                                <div class="month-item">
                                    <span class="month-name"><?= $meses_nomes[$mes] ?></span>
                                    <span>
                                        <?= $total ?> 
                                        <?php if ($total_aniverssariantes > 0): ?>
                                            (<?= number_format(($total / $total_aniverssariantes) * 100, 1) ?>%)
                                        <?php else: ?>
                                            (0%)
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="month-column">
                        <?php $count = 0; ?>
                        <?php foreach ($dados_por_mes as $mes => $total): ?>
                            <?php if ($count >= 4 && $count < 8): ?>
                                <div class="month-item">
                                    <span class="month-name"><?= $meses_nomes[$mes] ?></span>
                                    <span>
                                        <?= $total ?> 
                                        <?php if ($total_aniverssariantes > 0): ?>
                                            (<?= number_format(($total / $total_aniverssariantes) * 100, 1) ?>%)
                                        <?php else: ?>
                                            (0%)
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="month-column">
                        <?php $count = 0; ?>
                        <?php foreach ($dados_por_mes as $mes => $total): ?>
                            <?php if ($count >= 8): ?>
                                <div class="month-item">
                                    <span class="month-name"><?= $meses_nomes[$mes] ?></span>
                                    <span>
                                        <?= $total ?> 
                                        <?php if ($total_aniverssariantes > 0): ?>
                                            (<?= number_format(($total / $total_aniverssariantes) * 100, 1) ?>%)
                                        <?php else: ?>
                                            (0%)
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Seção de Edição de Funcionário -->
        <div class="stats-card" style="margin-top:10px;">
            <h3>Editar Funcionário</h3>
            <p style="margin:6px 0 10px 0;">Selecione uma pessoa para editar os dados cadastrais.</p>
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <label for="select-editar" style="font-size:0.9rem;">Pessoa</label>
                <select id="select-editar" style="padding:6px;border-radius:6px;border:1px solid #475569;background:#0f172a;color:#fff;min-width:260px;">
                    <option value="">-- selecione --</option>
                    <?php foreach($lista_pessoas as $p): ?>
                        <option value="<?= $p['id'] ?>" data-nome="<?= htmlspecialchars($p['nome']) ?>" data-setor="<?= htmlspecialchars($p['setor']) ?>" data-data="<?= $p['data_iso'] ?>" data-foto="<?= htmlspecialchars($p['foto']) ?>">
                            <?= htmlspecialchars($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <form id="form-editar-pessoa" action="salvar.php" method="post" enctype="multipart/form-data" style="display:none;margin-top:12px;background:rgba(255,255,255,0.06);padding:10px;border-radius:8px;">
                <input type="hidden" name="id" id="edit-id" value="">
                <input type="hidden" name="return_to" value="estatisticas.php">
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <div>
                        <label style="display:block;font-size:0.85em;">Nome</label>
                        <input type="text" name="nome" id="edit-nome" style="padding:6px;border-radius:6px;border:1px solid #475569;background:#0f172a;color:#fff;min-width:240px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85em;">Setor</label>
                        <input type="text" name="setor" id="edit-setor" style="padding:6px;border-radius:6px;border:1px solid #475569;background:#0f172a;color:#fff;min-width:200px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85em;">Data</label>
                        <input type="date" name="data_nascimento" id="edit-data" style="padding:6px;border-radius:6px;border:1px solid #475569;background:#0f172a;color:#fff;">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.85em;">Foto</label>
                        <input type="file" name="foto" accept="image/*" style="padding:6px;border-radius:6px;border:1px solid #475569;background:#0f172a;color:#fff;">
                    </div>
                </div>
                <div style="margin-top:10px;">
                    <label style="font-size:0.85em;"><input type="checkbox" name="remover_foto"> Remover foto atual</label>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit" class="btn">Salvar</button>
                </div>
            </form>
        </div>

        <div class="nav-buttons">
            <a href="http://localhost:3000/" class="btn">Voltar ao Painel</a>
            <a href="cadastro.php" class="btn">Cadastrar Novo</a>
        </div>
    </div>
    
    <script>
        // Configuração do gráfico de meses
        const ctxMeses = document.getElementById('chartMeses').getContext('2d');
        const chartMeses = new Chart(ctxMeses, {
            type: 'bar',
            data: {
                labels: [<?php echo "'" . implode("', '", $meses_nomes) . "'"; ?>],
                datasets: [{
                    label: 'aniverssariantes por Mês',
                    data: [<?php echo implode(", ", $dados_por_mes); ?>],
                    backgroundColor: 'rgba(255, 215, 0, 0.7)',
                    borderColor: 'rgba(255, 215, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 500
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                            font: {
                                size: 10
                            },
                            maxTicksLimit: 5
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#fff',
                            font: {
                                size: 8
                            },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 11
                        },
                        padding: 6
                    }
                }
            }
        });
        
        // Ajustar tamanho dos gráficos quando a janela for redimensionada
        window.addEventListener('resize', function() {
            chartMeses.resize();
        });
    </script>
    <script>
        // Preencher formulário de edição ao selecionar pessoa
        const selectEditar = document.getElementById('select-editar');
        const formEditar = document.getElementById('form-editar-pessoa');
        const editId = document.getElementById('edit-id');
        const editNome = document.getElementById('edit-nome');
        const editSetor = document.getElementById('edit-setor');
        const editData = document.getElementById('edit-data');
        selectEditar && selectEditar.addEventListener('change', () => {
            const opt = selectEditar.selectedOptions[0];
            if(!opt || !opt.value){
                formEditar.style.display = 'none';
                editId.value = '';
                editNome.value = '';
                editSetor.value = '';
                editData.value = '';
                return;
            }
            editId.value = opt.value;
            editNome.value = opt.dataset.nome || '';
            editSetor.value = opt.dataset.setor || '';
            editData.value = opt.dataset.data || '';
            formEditar.style.display = 'block';
        });
    </script>
</body>
</html>