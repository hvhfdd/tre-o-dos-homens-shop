<?<?php
session_start();

// Inclui a conexão com o banco de dados
require 'db.php';

// Senha do painel administrativo
$senha_admin = 'admin123';

// Verifica se o usuário está logado
$exibir_painel_admin = false;
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    $exibir_painel_admin = true;
}

// Verifica se o usuário está tentando acessar o painel administrativo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acessar_painel'])) {
    if ($_POST['senha'] === $senha_admin) {
        $_SESSION['logado'] = true;
        $exibir_painel_admin = true;
    } else {
        echo "<p class='error'>Senha incorreta!</p>";
    }
}

// Adicionar produto (painel admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    if ($exibir_painel_admin) {
        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        
        if (!empty($nome) && !empty($preco) && isset($_FILES['foto'])) {
            $foto = $_FILES['foto'];
            $foto_nome = time() . '_' . basename($foto['name']);
            $foto_caminho = 'uploads/' . $foto_nome;
            
            // Verifica se o diretório de uploads existe
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true); // Cria o diretório se não existir
            }

            if (move_uploaded_file($foto['tmp_name'], $foto_caminho)) {
                $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, foto) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $preco, $foto_caminho]);
                echo "<p class='success'>Produto adicionado com sucesso!</p>";
            } else {
                echo "<p class='error'>Erro ao fazer upload da imagem!</p>";
            }
        } else {
            echo "<p class='error'>Preencha todos os campos!</p>";
        }
    } else {
        echo "<p class='error'>Acesso negado!</p>";
    }
}

// Apagar produto (painel admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar'])) {
    if ($exibir_painel_admin) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        echo "<p class='success'>Produto apagado com sucesso!</p>";
    } else {
        echo "<p class='error'>Acesso negado!</p>";
    }
}

// Buscar produtos no banco de dados
$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Loja</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3e8e41;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .produtos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            width: 100%;
        }

        .produto {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: calc(33.333% - 20px);
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        .produto:hover {
            transform: translateY(-5px);
        }

        .produto img {
            max-width: 100%;
            border-radius: 8px;
        }

        .produto h2 {
            margin: 10px 0;
            font-size: 20px;
        }

        .produto p {
            font-size: 18px;
            font-weight: bold;
            color: #e94e77;
        }

        .btn {
            background-color: #3e8e41;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2f6c32;
        }

        .carrinho {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
        }

        .carrinho h2 {
            text-align: center;
            color: #333;
        }

        .carrinho ul {
            list-style: none;
            padding: 0;
        }

        .carrinho ul li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .carrinho ul li:last-child {
            border-bottom: none;
        }

        .carrinho ul li button {
            background-color: #e94e77;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .carrinho ul li button:hover {
            background-color: #d43f5e;
        }

        .total-carrinho {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .error {
            color: red;
            text-align: center;
        }

        .success {
            color: green;
            text-align: center;
        }

        .form-admin {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin-top: 20px;
        }

        .form-admin input, .form-admin button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-admin button {
            background-color: #3e8e41;
            color: white;
        }

        .form-admin button:hover {
            background-color: #2f6c32;
        }

        .senha-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .senha-container form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<header><h1>BEM VINDO A LOJA</h1></header>

<div class="container">
    <div class="produtos">
        <?php foreach ($produtos as $produto): ?>
            <div class="produto">
                <img src="<?= htmlspecialchars($produto['foto']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                <h2><?= htmlspecialchars($produto['nome']) ?></h2>
                <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                <button class="btn adicionar-carrinho" data-id="<?= $produto['id'] ?>" data-nome="<?= htmlspecialchars($produto['nome']) ?>" data-preco="<?= $produto['preco'] ?>">Adicionar ao Carrinho</button>
                <?php if ($exibir_painel_admin): ?>
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                        <button type="submit" name="apagar">Apagar Produto</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Carrinho de Compras -->
    <div class="carrinho">
        <h2>Carrinho de Compras</h2>
        <ul id="carrinho-itens"></ul>
        <p class="total-carrinho">Total: R$ <span id="total-carrinho">0.00</span></p>
        <button class="btn" id="finalizar-compra">Finalizar Compra</button>
    </div>

    <!-- Painel Administrativo -->
    <?php if (!$exibir_painel_admin): ?>
        <div class="senha-container">
            <form method="POST">
                <input type="password" name="senha" placeholder="Digite a senha" required>
                <button type="submit" name="acessar_painel">Acessar Painel Admin</button>
            </form>
        </div>
    <?php else: ?>
        <div class="form-admin">
            <h2>Painel Administrativo</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Nome do Produto" required>
                <input type="number" name="preco" step="0.01" placeholder="Preço" required>
                <input type="file" name="foto" required>
                <button type="submit" name="adicionar">Adicionar Produto</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    let carrinho = [];

    function atualizarCarrinho() {
        const carrinhoItens = document.getElementById("carrinho-itens");
        const totalCarrinho = document.getElementById("total-carrinho");
        
        carrinhoItens.innerHTML = '';
        let total = 0;
        
        carrinho.forEach((item, index) => {
            const li = document.createElement("li");
            li.innerHTML = `
                ${item.nome} - R$ ${item.preco.toFixed(2).replace('.', ',')}
                <button onclick="removerDoCarrinho(${index})">Remover</button>
            `;
            carrinhoItens.appendChild(li);
            total += item.preco;
        });

        totalCarrinho.textContent = total.toFixed(2).replace('.', ',');
    }

    function removerDoCarrinho(index) {
        carrinho.splice(index, 1);
        atualizarCarrinho();
    }

    document.querySelectorAll(".adicionar-carrinho").forEach(button => {
        button.addEventListener("click", function() {
            const produto = {
                id: this.getAttribute("data-id"),
                nome: this.getAttribute("data-nome"),
                preco: parseFloat(this.getAttribute("data-preco"))
            };
            
            carrinho.push(produto);
            atualizarCarrinho();
        });
    });

    document.getElementById("finalizar-compra").addEventListener("click", function() {
        // Cria a mensagem do carrinho
        let mensagem = "Compra finalizada!\nItens no carrinho:\n";
        carrinho.forEach(item => {
            mensagem += `${item.nome} - R$ ${item.preco.toFixed(2).replace('.', ',')}\n`;
        });
        const total = carrinho.reduce((total, item) => total + item.preco, 0).toFixed(2).replace('.', ',');
        mensagem += `Total: R$ ${total}\n`;

        // Link do WhatsApp para enviar a mensagem
        const numero = "73981526839";
        const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
        
        // Redireciona para o WhatsApp
        window.open(url, "_blank");

        // Limpa o carrinho após a finalização
        carrinho = [];
        atualizarCarrinho();
    });
</script>

</body>
</html>
     