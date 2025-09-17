import data from './data.js';

const contenidor = document.getElementById("questionari");
let current = 13; // índice de la pregunta actual

// Función para mostrar una pregunta
function mostrarPregunta() {
  const pregunta = data.preguntes[current];

  let html = `<h2>${pregunta.pregunta}</h2>`;

  pregunta.respostes.forEach((r, index) => {
    html += `
      <button class="resposta" onclick="marcarResposta(${index})">
        <img src="${r.imatge}" alt="${r.text}" style="max-width:100px; display:block; margin:auto;">
        <span>${r.text}</span>
      </button>
    `;
  });

  html += `<br><button id="següent" style="margin-top:20px;">Següent</button>`;

  contenidor.innerHTML = html;

  // Asignamos el evento del botón "Següent"
  document.getElementById("següent").addEventListener("click", () => {
    current++;
    if (current < data.preguntes.length) {
      mostrarPregunta();
    } else {
      contenidor.innerHTML = `<h2>Has acabat el joc!</h2>`;
    }
  });
}

// Función para marcar respuesta
window.marcarResposta = function(index) {
  const pregunta = data.preguntes[current];
  if (pregunta.respostes[index].correcta) {
    alert("Correcte!");
  } else {
    alert("Incorrecte!");
  }
};

// Iniciamos el juego
mostrarPregunta();
