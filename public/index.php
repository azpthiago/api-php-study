<!DOCTYPE html>
<!-- Interface web simples para consumir a API REST construída em PHP puro -->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API PHP - CRUD de Items</title>
    <link rel="stylesheet" href="./styles/main.css">
</head>
<body>
    <header>
        <h1>API PHP - CRUD de Items</h1>
        <p>Interface simples para consumir os endpoints definidos em <code>create_item.php</code>, <code>read_item.php</code>, <code>update_item.php</code> e <code>delete_item.php</code>.</p>
    </header>

    <main>
        <section id="alerts" hidden></section>

        <section>
            <h2>Novo Item</h2>
            <form id="form-create">
                <label>Nome
                    <input type="text" name="nome" required placeholder="Informe o nome do item">
                </label>
                <label>Descrição
                    <textarea name="descricao" rows="3" required placeholder="Descreva o item"></textarea>
                </label>
                <button type="submit">Salvar</button>
            </form>
        </section>

        <section>
            <h2>Itens cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    <tr><td colspan="5">Carregando itens...</td></tr>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        const alertsSection = document.getElementById('alerts');
        const itemsBody = document.getElementById('items-body');
        const formCreate = document.getElementById('form-create');

        function showAlert(message, type = 'success') {
            alertsSection.hidden = false;
            alertsSection.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
            alertsSection.textContent = message;
            setTimeout(() => {
                alertsSection.hidden = true;
                alertsSection.textContent = '';
                alertsSection.className = '';
            }, 4000);
        }

        async function fetchItems() {
            try {
                const response = await fetch('read_item.php');
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Falha ao carregar itens.');
                }

                renderItems(data.data);
            } catch (error) {
                itemsBody.innerHTML = '<tr><td colspan="5">Erro: ' + error.message + '</td></tr>';
            }
        }

        function renderItems(items) {
            if (!items.length) {
                itemsBody.innerHTML = '<tr><td colspan="5">Nenhum item cadastrado.</td></tr>';
                return;
            }

            itemsBody.innerHTML = '';
            for (const item of items) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.id}</td>
                    <td>${escapeHtml(item.nome)}</td>
                    <td>${escapeHtml(item.descricao ?? '')}</td>
                    <td>${item.created_at ?? '-'}</td>
                    <td class="actions">
                        <button data-action="edit" data-id="${item.id}">Editar</button>
                        <button data-action="delete" data-id="${item.id}">Excluir</button>
                    </td>
                `;
                itemsBody.appendChild(tr);
            }
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        formCreate.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(formCreate);

            try {
                const response = await fetch('create_item.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Erro ao criar item.');
                }

                formCreate.reset();
                showAlert('Item criado com sucesso!');
                fetchItems();
            } catch (error) {
                showAlert(error.message, 'error');
            }
        });

        itemsBody.addEventListener('click', async (event) => {
            const button = event.target.closest('button');
            if (!button) return;

            const action = button.dataset.action;
            const id = button.dataset.id;

            if (action === 'delete') {
                if (!confirm('Deseja realmente excluir este item?')) {
                    return;
                }

                try {
                    const response = await fetch('delete_item.php', {
                        method: 'POST',
                        body: new URLSearchParams({ id })
                    });
                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.error || 'Erro ao excluir item.');
                    }

                    showAlert('Item excluído com sucesso!');
                    fetchItems();
                } catch (error) {
                    showAlert(error.message, 'error');
                }
            }

            if (action === 'edit') {
                const novoNome = prompt('Informe o novo nome:');
                if (novoNome === null) return;

                const novaDescricao = prompt('Informe a nova descrição:');
                if (novaDescricao === null) return;

                try {
                    const response = await fetch('update_item.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            id,
                            nome: novoNome,
                            descricao: novaDescricao
                        })
                    });
                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.error || 'Erro ao atualizar item.');
                    }

                    showAlert('Item atualizado com sucesso!');
                    fetchItems();
                } catch (error) {
                    showAlert(error.message, 'error');
                }
            }
        });

        fetchItems();
    </script>
</body>
</html>