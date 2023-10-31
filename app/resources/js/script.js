document.addEventListener("DOMContentLoaded", function () {
    const inputData = document.getElementById("dataEvento");
    const detalhesEvento = document.getElementById("detalhesEvento");

    inputData.addEventListener("change", function () {
        const dataSelecionada = new Date(inputData.value);
        const diaSelecionado = dataSelecionada.getDate();

        exibirDetalhesEvento(diaSelecionado);
    });

    function exibirDetalhesEvento(diaSelecionado) {
        // Simulação: obter detalhes do evento para o dia selecionado
        const detalhes = obterDetalhesEvento(diaSelecionado);

        // Atualiza o conteúdo dos detalhes do evento
        detalhesEvento.innerHTML = `
            <h2>Detalhes do Evento - Dia ${diaSelecionado}</h2>
            <p><strong>Evento:</strong> ${detalhes.evento}</p>
            <p><strong>Horário:</strong> ${detalhes.horario}</p>
            <p><strong>Local:</strong> ${detalhes.local}</p>
        `;

        // Exibe os detalhes do evento
        detalhesEvento.style.display = "block";
    }

    function obterDetalhesEvento(dia) {
        // Simulação: substitua isso com lógica real para obter detalhes do evento
        return {
            evento: "Reunião de Equipe",
            horario: "14:00 - 16:00",
            local: "Sala de Conferência"
        };
    }
});

/*document.addEventListener("DOMContentLoaded", function () {
    const calendario = document.getElementById("calendario");
    const listaHorarios = document.getElementById("listaHorarios");

    // Simulação: gerar dias do mês
    for (let i = 1; i <= 31; i++) {
        const dia = document.createElement("div");
        dia.innerText = i;
        dia.addEventListener("click", function () {
            exibirListaHorarios(i);
        });
        calendario.appendChild(dia);
    }

    function exibirListaHorarios(diaSelecionado) {
        // Simulação: gerar uma lista de horários para o dia selecionado
        listaHorarios.innerHTML = ""; // Limpa a lista
        for (let hora = 8; hora <= 17; hora++) {
            const horario = document.createElement("div");
            horario.innerText = `Dia ${diaSelecionado}, Hora ${hora}:00`;
            listaHorarios.appendChild(horario);
        }

        // Exibe a lista de horários
        listaHorarios.style.display = "block";
    }
});



ambos sao legais


document.addEventListener("DOMContentLoaded", function () {
    const calendario = document.getElementById("calendario");
    const detalhesEvento = document.getElementById("detalhesEvento");

    // Simulação: gerar dias do mês
    for (let i = 1; i <= 31; i++) {
        const dia = document.createElement("div");
        dia.innerText = i;
        dia.addEventListener("click", function () {
            exibirDetalhesEvento(i);
        });
        calendario.appendChild(dia);
    }

    function exibirDetalhesEvento(diaSelecionado) {
        // Simulação: obter detalhes do evento para o dia selecionado
        const detalhes = obterDetalhesEvento(diaSelecionado);

        // Atualiza o conteúdo dos detalhes do evento
        detalhesEvento.innerHTML = `
            <h2>Detalhes do Evento - Dia ${diaSelecionado}</h2>
            <p><strong>Evento:</strong> ${detalhes.evento}</p>
            <p><strong>Horário:</strong> ${detalhes.horario}</p>
            <p><strong>Local:</strong> ${detalhes.local}</p>
        `;

        // Exibe os detalhes do evento
        detalhesEvento.style.display = "block";
    }

    function obterDetalhesEvento(dia) {
        // Simulação: substitua isso com lógica real para obter detalhes do evento
        return {
            evento: "Reunião de Equipe",
            horario: "14:00 - 16:00",
            local: "Sala de Conferência"
        };
    }
});*/