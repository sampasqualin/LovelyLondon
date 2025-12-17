<?php
/**
 * =========================================================================
 * ORÇAMENTO HANDLER - Processamento e envio de solicitações de orçamento
 * =========================================================================
 */

// Configurações de email
define('EMAIL_DESTINO', 'contato@lovelylondonbycarol.com'); // Atualizar com o email real
define('EMAIL_ASSUNTO', 'Nova Solicitação de Orçamento - Lovely London');

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orcamento.php?erro=metodo_invalido');
    exit;
}

// Função para sanitizar dados
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Coletar dados do formulário
$tipo_servico = sanitize($_POST['tipo_servico'] ?? '');
$categoria_tour = sanitize($_POST['categoria_tour'] ?? '');

// Processar seleções múltiplas (checkboxes)
$tours_escolhidos = [];
if (isset($_POST['tours_escolhidos']) && is_array($_POST['tours_escolhidos'])) {
    foreach ($_POST['tours_escolhidos'] as $tour) {
        $tours_escolhidos[] = sanitize($tour);
    }
}

$servicos_escolhidos = [];
if (isset($_POST['servicos_escolhidos']) && is_array($_POST['servicos_escolhidos'])) {
    foreach ($_POST['servicos_escolhidos'] as $servico) {
        $servicos_escolhidos[] = sanitize($servico);
    }
}

// Dados adicionais
$num_pessoas = sanitize($_POST['num_pessoas'] ?? '');
$data_preferida = sanitize($_POST['data_preferida'] ?? '');
$periodo = sanitize($_POST['periodo'] ?? '');

// Dados pessoais
$nome = sanitize($_POST['nome'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$telefone = sanitize($_POST['telefone'] ?? '');
$observacoes = sanitize($_POST['observacoes'] ?? '');

// Validar campos obrigatórios
$erros = [];

if (empty($tipo_servico)) {
    $erros[] = 'Tipo de serviço é obrigatório';
}

if ($tipo_servico === 'tour' && empty($tours_escolhidos)) {
    $erros[] = 'Selecione pelo menos um tour';
}

if ($tipo_servico === 'servico' && empty($servicos_escolhidos)) {
    $erros[] = 'Selecione pelo menos um serviço';
}

if (empty($nome)) {
    $erros[] = 'Nome é obrigatório';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Email válido é obrigatório';
}

if (empty($telefone)) {
    $erros[] = 'Telefone é obrigatório';
}

// Se houver erros, redirecionar de volta
if (!empty($erros)) {
    $erro_msg = implode(', ', $erros);
    header('Location: orcamento.php?erro=' . urlencode($erro_msg));
    exit;
}

// Montar corpo do email
$corpo_email = "
═══════════════════════════════════════════════════════════
     NOVA SOLICITAÇÃO DE ORÇAMENTO - LOVELY LONDON
═══════════════════════════════════════════════════════════

📋 INFORMAÇÕES DO CLIENTE
────────────────────────────────────────────────────────────
Nome: {$nome}
Email: {$email}
Telefone: {$telefone}

🎯 TIPO DE SERVIÇO
────────────────────────────────────────────────────────────
Tipo: " . ($tipo_servico === 'tour' ? 'Tours Guiados' : 'Serviços Personalizados') . "
";

// Adicionar tours escolhidos
if (!empty($tours_escolhidos)) {
    $corpo_email .= "\n🗺️ TOURS SELECIONADOS\n";
    $corpo_email .= "────────────────────────────────────────────────────────────\n";
    $corpo_email .= "Categoria: " . ($categoria_tour === 'classico' ? 'Tours Clássicos' : 'Tours Exclusivos') . "\n\n";
    foreach ($tours_escolhidos as $index => $tour) {
        $corpo_email .= ($index + 1) . ". {$tour}\n";
    }
}

// Adicionar serviços escolhidos
if (!empty($servicos_escolhidos)) {
    $corpo_email .= "\n💼 SERVIÇOS SELECIONADOS\n";
    $corpo_email .= "────────────────────────────────────────────────────────────\n";
    foreach ($servicos_escolhidos as $index => $servico) {
        $corpo_email .= ($index + 1) . ". {$servico}\n";
    }
}

// Adicionar detalhes da reserva
$corpo_email .= "
📅 DETALHES DA EXPERIÊNCIA
────────────────────────────────────────────────────────────
Número de Pessoas: {$num_pessoas}
Data Preferida: " . ($data_preferida ? date('d/m/Y', strtotime($data_preferida)) : 'Não informado') . "
Período: {$periodo}
";

// Adicionar observações se houver
if (!empty($observacoes)) {
    $corpo_email .= "
💬 OBSERVAÇÕES DO CLIENTE
────────────────────────────────────────────────────────────
{$observacoes}
";
}

$corpo_email .= "
═══════════════════════════════════════════════════════════
📧 Esta solicitação foi enviada em: " . date('d/m/Y H:i:s') . "
═══════════════════════════════════════════════════════════
";

// Configurar headers do email
$headers = [
    'From: Lovely London Website <noreply@lovelylondonbycarol.com>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8'
];

// Enviar email
$enviado = mail(
    EMAIL_DESTINO,
    EMAIL_ASSUNTO,
    $corpo_email,
    implode("\r\n", $headers)
);

// Email de confirmação para o cliente
if ($enviado) {
    $corpo_confirmacao = "
Olá {$nome},

Recebemos sua solicitação de orçamento para:
" . ($tipo_servico === 'tour' ? '• Tours: ' . implode(', ', $tours_escolhidos) : '• Serviços: ' . implode(', ', $servicos_escolhidos)) . "

Em breve entraremos em contato para fornecer um orçamento personalizado!

Obrigada pelo interesse,
Carol
Lovely London by Carol

═══════════════════════════════════════════════════════════
lovelylondonbycarol.com
";

    $headers_confirmacao = [
        'From: Carol - Lovely London <noreply@lovelylondonbycarol.com>',
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8'
    ];

    mail(
        $email,
        'Confirmação de Solicitação - Lovely London',
        $corpo_confirmacao,
        implode("\r\n", $headers_confirmacao)
    );
}

// Redirecionar com mensagem de sucesso
if ($enviado) {
    header('Location: orcamento.php?sucesso=1');
} else {
    header('Location: orcamento.php?erro=erro_envio');
}
exit;
?>
