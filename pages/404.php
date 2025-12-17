<?php
// Página 404
http_response_code(404);
$page_title = "Página não encontrada";
require_once '../includes/header.php';
?>

<div class="error-page">
    <div class="container">
        <div class="error-content">
            <h1>404</h1>
            <h2>Página não encontrada</h2>
            <p>A página que você está procurando não existe ou foi movida.</p>
            <a href="/" class="btn btn-primary">Voltar ao início</a>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 100px 0;
    text-align: center;
    background: #f8f9fa;
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.error-content h1 {
    font-size: 6rem;
    color: #700420;
    margin-bottom: 20px;
    font-weight: 700;
}

.error-content h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #333;
}

.error-content p {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
}

.btn-primary {
    background: #700420;
    color: white;
    padding: 15px 30px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #5a0319;
    transform: translateY(-2px);
}
</style>

<?php require_once '../includes/footer.php'; ?>



