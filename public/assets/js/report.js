document.addEventListener('DOMContentLoaded', function () {
    const formWizard = document.getElementById('formWizard');
    if (!formWizard) return; // Garante que o script só rode onde o form existe

    const submissaoId = formWizard.dataset.submissaoId;
    const mapInstances = []; // Armazena todas as instâncias de mapas para correção de tamanho

    // Inicializa os módulos
    initWizardNavigation();
    // initAutoSave(submissaoId, formWizard);
    initMapsAndLocations(mapInstances);
    initMapResizeObserver(mapInstances);
});

/**
 * 1. Lógica de navegação das abas do Wizard
 */
function initWizardNavigation() {
    const abas = document.querySelectorAll('#wizardTabs .nav-link');
    const conteudos = document.querySelectorAll('.tab-pane');

    document.querySelectorAll('.btn-proximo').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            abas[index].classList.remove('active');
            conteudos[index].classList.remove('show', 'active');

            abas[index + 1].removeAttribute('disabled');
            abas[index + 1].classList.add('active');
            conteudos[index + 1].classList.add('show', 'active');
        });
    });

    document.querySelectorAll('.btn-anterior').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            let abaAtual = index + 1;

            abas[abaAtual].classList.remove('active');
            conteudos[abaAtual].classList.remove('show', 'active');

            abas[abaAtual - 1].classList.add('active');
            conteudos[abaAtual - 1].classList.add('show', 'active');
        });
    });
}

/**
 * 2. Lógica de auto-save (Local Storage)
 */
// function initAutoSave(submissaoId, formWizard) {
//     if (!submissaoId) return;

//     const chaveStorage = `rascunho_form_${submissaoId}`;
//     const inputsEstado = document.querySelectorAll('.form-salvar-estado');
//     const dadosSalvos = JSON.parse(localStorage.getItem(chaveStorage)) || {};

//     // inputsEstado.forEach(input => {
//     //     // Preenche dados recuperados
//     //     if (dadosSalvos[input.name]) {
//     //         if (input.type === 'checkbox' || input.type === 'radio') {
//     //             input.checked = (input.value === dadosSalvos[input.name]);
//     //         } else {
//     //             input.value = dadosSalvos[input.name];
//     //         }
//     //     }

//     //     // Escuta mudanças
//     //     input.addEventListener('change', (e) => {
//     //         dadosSalvos[e.target.name] = e.target.value;
//     //         localStorage.setItem(chaveStorage, JSON.stringify(dadosSalvos));
//     //     });
//     // });

//     // Limpa storage ao enviar o form
//     // formWizard.addEventListener('submit', () => {
//     //     localStorage.removeItem(chaveStorage);
//     // });
// }

/**
 * 3. Inicialização dos mapas (Leaflet + Nominatim) e TomSelect
 */
function initMapsAndLocations(mapInstances) {
    const cearaViewbox = "-41.4,-2.7,-37.2,-7.8";

    document.querySelectorAll('.select-location-tom').forEach(function (selectEl) {
        const id = selectEl.getAttribute('data-map-id');
        const mapContainer = document.getElementById('map-' + id);
        if (!mapContainer) return;

        // Pega as coordenadas salvas (se existirem)
        let savedLat = document.getElementById("latitude-" + id).value;
        let savedLng = document.getElementById("longitude-" + id).value;
        
        var map = L.map('map-' + id);
        var marker;

        // Se já tiver latitude e longitude, centraliza lá e bota o pino. Se não, vai pro padrão.
        if (savedLat && savedLng) {
            map.setView([savedLat, savedLng], 13);
            marker = L.marker([savedLat, savedLng]).addTo(map);
        } else {
            map.setView([-7.2287, -39.3126], 13);
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        mapInstances.push(map);

        function updateMap(lat, lon) {
            const latlng = [lat, lon];
            map.setView(latlng, 13);
            if (marker) map.removeLayer(marker);
            marker = L.marker(latlng).addTo(map);
        }

        const tom = new TomSelect(selectEl, {
            valueField: "display_name",
            labelField: "display_name",
            searchField: "display_name",
            maxOptions: 10,
            loadThrottle: 500,
            preload: false,
            load: function (query, callback) {
                if (!query.length) return callback();
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&viewbox=${cearaViewbox}`)
                    .then(res => res.json())
                    .then(json => {
                        json.forEach(item => item.display_name = resumirNome(item.display_name));
                        callback(json);
                    })
                    .catch(() => callback());
            },
            onChange: function (value) {
                const selected = this.options[value];
                if (selected) {
                    document.getElementById("latitude-" + id).value = selected.lat;
                    document.getElementById("longitude-" + id).value = selected.lon;
                    updateMap(selected.lat, selected.lon);
                }
            }
        });

        // Clique no mapa: Atualiza coordenadas, bota o pino e avisa o AutoSave
        map.on('click', function (e) {
            const lat = e.latlng.lat;
            const lon = e.latlng.lng;

            document.getElementById("latitude-" + id).value = lat;
            document.getElementById("longitude-" + id).value = lon;
            updateMap(lat, lon);

            const divSelect = document.getElementById('div-select-local-' + id);
            const inputManual = document.getElementById('input-local-' + id);

            if (!divSelect.classList.contains('d-none')) {
                // Modo Automático
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            const displayName = resumirNome(data.display_name);
                            tom.addOption({ value: displayName, display_name: displayName, lat: lat, lon: lon });
                            tom.addItem(displayName);
                            // O TomSelect já dispara o evento 'change' por conta própria ao adicionar o item
                        }
                    });
            } else {
                // Modo Manual: Força o trigger 'change' no input de texto para o AutoSave perceber que o mapa foi clicado
                $(inputManual).trigger('change');
            }
        });
    });
}
function resumirNome(nomeCompleto) {
    let partes = nomeCompleto.split(',');
    if (partes.length > 3) return partes.slice(0, 3).join(',').trim();
    return nomeCompleto.trim();
}

/**
 * 4. Correção Definitiva do "Mapa Cinza"
 * Observa as transições do Bootstrap para recalcular o tamanho dos mapas
 */
function initMapResizeObserver(mapInstances) {
    if (mapInstances.length === 0) return;

    // Listener para o evento nativo de tabs do Bootstrap
    document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function () {
            mapInstances.forEach(m => m.invalidateSize());
        });
    });

    // Observer como "fallback" robusto para os botões "Anterior/Próximo"
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === "class") {
                const aba = mutation.target;
                if (aba.classList.contains("active") && aba.classList.contains("show")) {
                    setTimeout(() => {
                        mapInstances.forEach(m => m.invalidateSize());
                    }, 150); // Delay mínimo para a animação CSS (fade) do Bootstrap concluir
                }
            }
        });
    });

    document.querySelectorAll('.tab-pane').forEach(pane => {
        observer.observe(pane, { attributes: true, attributeFilter: ['class'] });
    });

    // Força renderização na aba inicial ao carregar a página
    setTimeout(() => {
        mapInstances.forEach(m => m.invalidateSize());
    }, 200);
}

/**
 * 5. Alternância entre Mapa / Input Manual
 */
window.toggleLocationInput = function (id) {
    const divSelect = document.getElementById('div-select-local-' + id);
    const select = document.getElementById('select-local-' + id);
    const divInput = document.getElementById('div-chose-' + id);
    const input = document.getElementById('input-local-' + id);

    // Removemos a manipulação do mapContainer daqui para ele NUNCA sumir

    if (divSelect.classList.contains('d-none')) {
        // Voltar a usar o dropdown API
        divSelect.classList.remove('d-none');
        divInput.classList.add('d-none');

        input.removeAttribute('name');
        select.setAttribute('name', 'respostas[' + id + ']');

        if (select.hasAttribute('data-required')) {
            select.setAttribute('required', '');
            input.removeAttribute('required');
        }
    } else {
        // Mudar para digitação manual (Mapa continua aberto embaixo)
        divSelect.classList.add('d-none');
        divInput.classList.remove('d-none');

        select.removeAttribute('name');
        input.setAttribute('name', 'respostas[' + id + ']');

        if (select.hasAttribute('required')) {
            select.removeAttribute('required');
            select.setAttribute('data-required', 'true');
            input.setAttribute('required', '');
        }
    }
};

function adicionarLinhaTabela(perguntaPaiId) {
    const container = document.getElementById('linhas-' + perguntaPaiId);
    const linhasAtuais = container.querySelectorAll('.linha-item');
    
    // Pega o maior índice atual para gerar o próximo (+1)
    let maiorIndice = -1;
    linhasAtuais.forEach(linha => {
        let idx = parseInt(linha.getAttribute('data-indice'));
        if (idx > maiorIndice) maiorIndice = idx;
    });
    const novoIndice = maiorIndice + 1;

    // Clona o HTML da primeira linha (índice base)
    const linhaBase = linhasAtuais[0];
    const novaLinha = linhaBase.cloneNode(true);

    // Atualiza os atributos da nova linha
    novaLinha.setAttribute('data-indice', novoIndice);
    novaLinha.id = 'linha-' + perguntaPaiId + '-' + novoIndice;
    
    // Atualiza a numeração visual (Item #2, Item #3)
    novaLinha.querySelector('.numero-item').innerText = linhasAtuais.length + 1;

    // Ajusta o botão de remover
    const btnRemover = novaLinha.querySelector('.btn-outline-danger');
    btnRemover.setAttribute('onclick', `removerLinhaTabela('${perguntaPaiId}', '${novoIndice}')`);

    // Limpa os valores e atualiza o data-indice dos inputs para o AutoSave
    const inputs = novaLinha.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.setAttribute('data-indice', novoIndice);
        
        // Limpa o valor (exceto checkboxes/radios que devem ser desmarcados)
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }

        // Se você tiver LOCATION (Mapas), precisará atualizar o ID da div do mapa e re-iniciar o Leaflet/TomSelect aqui, 
        // substituindo a parte velha do ID pelo 'novoIndice'.
    });

    // Anexa a nova linha na tela
    container.appendChild(novaLinha);

    // Re-inicia as máscaras jQuery para os novos inputs gerados
    $(novaLinha).find('input[data-mascara]').each(function() {
        $(this).mask($(this).attr('data-mascara'));
    });
}

function removerLinhaTabela(perguntaPaiId, indice) {
    const container = document.getElementById('linhas-' + perguntaPaiId);
    const linhas = container.querySelectorAll('.linha-item');

    // Não deixa remover se for a última linha que sobrou
    if (linhas.length <= 1) {
        alert("Você precisa manter pelo menos um item preenchido.");
        return;
    }

    if(confirm('Tem certeza que deseja remover este item? Ele será apagado do relatório.')) {
        // Remove do HTML
        const linhaParaRemover = document.getElementById('linha-' + perguntaPaiId + '-' + indice);
        linhaParaRemover.remove();

        // 🚀 AQUI VAI UMA CHAMADA AJAX PARA DELETAR NO BANCO (Opcional, mas recomendado)
        // Como o Auto-Save salva on-change, se o usuário apagar a linha no front, precisamos apagar as respostas no banco vinculadas àquele 'indice_grupo'.
        $.ajax({
            url: '/api/respostas/remover-grupo', // Crie essa rota no Laravel
            type: 'POST',
            data: {
                id_submissao: $('#formWizard').data('submissao-id'),
                id_pergunta_pai: perguntaPaiId,
                indice_grupo: indice
            }
        });

        // Reorganiza a numeração visual (Item #1, Item #2...)
        const linhasRestantes = container.querySelectorAll('.linha-item');
        linhasRestantes.forEach((linha, i) => {
            linha.querySelector('.numero-item').innerText = i + 1;
        });
    }
}

// Usamos $(document).on para garantir que o botão seja escutado mesmo se for renderizado dinamicamente
$(document).on('click', '#btn-finalizar-fake', function(e) {
    e.preventDefault();
    let formValido = true;
    let primeiraAbaComErro = null;

    // Varre todos os campos required que NÃO estão desabilitados
    $('#formWizard').find('[required]:not(:disabled)').each(function() {
        let elemento = $(this);
        let valido = true;

        if (elemento.is(':checkbox, :radio')) {
            let name = elemento.attr('name');
            if ($(`input[name="${name}"]:checked`).length === 0) {
                valido = false;
            }
        } else if (!elemento.val() || elemento.val().trim() === '') {
            valido = false;
        }

        if (!valido) {
            formValido = false;
            elemento.addClass('is-invalid');
            
            let tabPane = elemento.closest('.tab-pane');
            if(tabPane.length && !primeiraAbaComErro) {
                primeiraAbaComErro = tabPane.attr('id');
            }
        } else {
            elemento.removeClass('is-invalid');
        }
    });

    if (formValido) {
        if(confirm('Deseja realmente finalizar e enviar esse relatório para análise? As informações não poderão ser alteradas.')) {
            // Força o submit nativo do formulário
            document.getElementById('form-finalizar').submit(); 
        }
    } else {
        alert('Atenção: Existem campos obrigatórios vazios. Verifique o formulário (campos destacados em vermelho).');
        
        if (primeiraAbaComErro) {
            let tabTrigger = document.querySelector(`button[data-bs-target="#${primeiraAbaComErro}"]`);
            if(tabTrigger) {
                let tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    }
});