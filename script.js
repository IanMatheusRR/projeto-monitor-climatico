// ===================================================================================
// SCRIPT COMPLETO PARA O DASHBOARD DE MONITORAMENTO
// Versão: Gráficos Históricos com Filtros (sem atualização em tempo real nos gráficos)
// ===================================================================================

// Variáveis globais para guardar as instâncias dos gráficos
let tempChartInstance = null;
let humidityChartInstance = null;

/**
 * Inicializa os dois gráficos com uma estrutura base.
 * É chamada uma vez quando a página carrega.
 */
function initializeCharts() {
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    const humidityCtx = document.getElementById('humidityChart').getContext('2d');

    const commonChartOptions = {
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'hour',
                    tooltipFormat: 'dd MMM yyyy, HH:mm',
                    displayFormats: {
                        hour: 'dd/MM HH:mm',
                        day: 'dd/MM/yyyy'
                    }
                },
                title: {
                    display: true,
                    text: 'Data'
                }
            },
            y: {
                beginAtZero: false,
                title: {
                    display: true,
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false, // Essencial para o controle de tamanho via CSS
        interaction: {
            intersect: false,
            mode: 'index',
        },
    };

    // Cria o gráfico de Temperatura
    tempChartInstance = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Temperatura (°C)',
                data: [],
                borderColor: '#cf4b00',
                backgroundColor: 'rgba(207, 75, 0, 0.1)',
                fill: true,
                tension: 0.2
            }]
        },
        options: {
            ...commonChartOptions,
            scales: {
                ...commonChartOptions.scales,
                y: { ...commonChartOptions.scales.y, title: { ...commonChartOptions.scales.y.title, text: 'Temperatura (°C)' } }
            }
        }
    });

    // Cria o gráfico de Umidade
    humidityChartInstance = new Chart(humidityCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Umidade (%)',
                data: [],
                borderColor: '#02787d',
                backgroundColor: 'rgba(2, 120, 125, 0.1)',
                fill: true,
                tension: 0.2
            }]
        },
        options: {
            ...commonChartOptions,
            scales: {
                ...commonChartOptions.scales,
                y: { ...commonChartOptions.scales.y, title: { ...commonChartOptions.scales.y.title, text: 'Umidade (%)' } }
            }
        }
    });
}

/**
 * Busca os 5 dados mais recentes para os cards e a tabela superior.
 */
async function updateLatestData() {
    console.log('[DEBUG] Tentando buscar dados de api_latest.php...');
    try {
        const response = await fetch('api_latest.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const leituras = await response.json();
        
        // --- PONTO CRÍTICO DE DEBUG ---
        console.log('[DEBUG] Dados recebidos com SUCESSO de api_latest.php:', leituras);
        
        atualizarCards(leituras);
        atualizarTabela(leituras);
    } catch (error) {
        console.error('Falha ao buscar dados recentes:', error);
    }
}

/**
 * Busca os dados históricos para preencher os gráficos, usando os filtros de data.
 */
async function fetchChartData() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    let apiUrl = 'api_dados.php';
    if (startDate && endDate) { apiUrl += `?start=${startDate}&end=${endDate}`; }
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        const labels = data.map(item => item.data_hora);
        const tempData = data.map(item => item.temperatura);
        const humidityData = data.map(item => item.umidade);
        updateChartData(tempChartInstance, labels, tempData);
        updateChartData(humidityChartInstance, labels, humidityData);
    } catch (error) { console.error('Falha ao buscar dados para os gráficos:', error); }
}


/**
 * Substitui todos os dados de um gráfico.
 * @param {Chart} chart - A instância do gráfico.
 * @param {Array} labels - O novo array de legendas (datas).
 * @param {Array} data - O novo array de dados (valores).
 */
function updateChartData(chart, labels, data) {
    if (chart) { chart.data.labels = labels; chart.data.datasets[0].data = data; chart.update(); }
}

/**
 * Atualiza os cards de resumo com a leitura mais recente.
 * @param {Array} leituras - Array de dados, onde o primeiro item é o mais recente.
 */
function atualizarCards(leituras) {
    console.log('[DEBUG] Função atualizarCards chamada com os seguintes dados:', leituras);
    const tempElement = document.getElementById('latest-temp');
    const humidityElement = document.getElementById('latest-humidity');

    if (!tempElement || !humidityElement) {
        console.error('[DEBUG] ERRO: Não foi possível encontrar os elementos dos cards no HTML!');
        return;
    }

    if (leituras && leituras.length > 0) {
        console.log('[DEBUG] Há dados. Atualizando cards com valores.');
        const maisRecente = leituras[0];
        tempElement.innerText = `${parseFloat(maisRecente.temperatura).toFixed(1)} °C`;
        humidityElement.innerText = `${parseFloat(maisRecente.umidade).toFixed(1)} %`;
    } else {
        console.log('[DEBUG] Não há dados. Exibindo estado de "vazio" nos cards.');
        tempElement.innerText = '-- °C';
        humidityElement.innerText = '-- %';
    }
}

/**
 * Atualiza a tabela de histórico recente com as últimas 5 leituras.
 * @param {Array} leituras - Array com as 5 leituras mais recentes.
 */
function atualizarTabela(leituras) {
    console.log('[DEBUG] Função atualizarTabela chamada com os seguintes dados:', leituras);
    const tbody = document.getElementById('dados-tabela');

    if (!tbody) {
        console.error('[DEBUG] ERRO: Não foi possível encontrar o corpo da tabela (tbody) no HTML!');
        return;
    }

    tbody.innerHTML = '';
    if (!leituras || leituras.length === 0) {
        console.log('[DEBUG] Não há dados. Exibindo mensagem de "Nenhuma medição encontrada" na tabela.');
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Nenhuma medição encontrada.</td></tr>';
        return;
    }

    console.log('[DEBUG] Há dados. Preenchendo a tabela.');
    leituras.forEach(leitura => {
        const tr = document.createElement('tr');
        const dataHora = new Date(leitura.data_hora);
        const dataFormatada = dataHora.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const temperatura = parseFloat(leitura.temperatura).toFixed(2);
        const umidade = parseFloat(leitura.umidade).toFixed(2);
        tr.innerHTML = `<td>${dataFormatada}</td><td>${temperatura} °C</td><td>${umidade} %</td>`;
        tbody.appendChild(tr);
    });
}

// ===================================================================================
// PONTO DE ENTRADA PRINCIPAL - Roda quando a página HTML está pronta
// ===================================================================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('[DEBUG] Página carregada. Iniciando script.');
    
    initializeCharts();
    
    updateLatestData();
    fetchChartData();

    // Configura o intervalo para atualizar APENAS os cards e a tabela superior
    setInterval(updateLatestData, 5000);

    // Configura os listeners de evento para os botões de filtro
    document.getElementById('filterBtn').addEventListener('click', fetchChartData);
    document.getElementById('resetBtn').addEventListener('click', () => {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        fetchChartData();
    });

    // Configura o listener de evento para o botão de exclusão
    const deleteBtn = document.getElementById('delete-all-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', async () => {
            const confirmado = confirm('Você tem certeza que deseja excluir TODAS as medições?\n\nEsta ação não pode ser desfeita.');
            if (confirmado) {
                try {
                    const response = await fetch('delete_all.php', { method: 'POST' });
                    const result = await response.json();
                    if (response.ok && result.success) {
                        alert('Dados excluídos com sucesso!');
                        // Atualiza todas as seções da página para refletir o estado vazio
                        updateLatestData();
                        fetchChartData();
                    } else {
                        throw new Error(result.message || 'Falha ao excluir os dados.');
                    }
                } catch (error) {
                    console.error('Erro ao excluir os dados:', error);
                    alert(`Erro: ${error.message}`);
                }
            }
        });
    }
});