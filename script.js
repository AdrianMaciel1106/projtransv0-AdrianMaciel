import data from './data.js';

const contenidor = document.getElementById("questionari");

const pregunta = data.preguntes[0];

let htmlString = `
  <h2>${pregunta.pregunta}</h2>
  <img src="${pregunta.imatge}" alt="Imatge de la pregunta">
`;

pregunta.respostes.forEach((resposta, index) => {
  htmlString += `
    <button onclick="marcarResposta(${index})">
      ${resposta.resposta}
    </button>
  `;
});

contenidor.innerHTML = htmlString;

// Funció per comprovar la resposta
window.marcarResposta = function(index) {
  const correcta = pregunta.respostes[index].correcta;
  if (correcta) {
    alert("Correcte!");
  } else {
    alert("Incorrecte. Torna-ho a provar!");
  }
};
