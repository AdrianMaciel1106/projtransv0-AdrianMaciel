import data from "./data.js";
//alert("hola");

let contenidor = document.getElementById("questionari");
let htmlString="";

for (let i = 0; i < data.preguntes.length; i++) {
    const pregunta = data.preguntes[i];
  
    htmlString += `<h3>${pregunta.pregunta}</h3>`;
    htmlString += `<img src="${pregunta.imatge}" alt="imatge pregunta ${i + 1}" style="max-width:200px;display:block;margin:1rem auto;">`;
  
    for (let j = 0; j < pregunta.respostes.length; j++) {
      htmlString += `
        <button id="btn-${i}-${j}" onclick="marcarResposta(${i},${j})">
          ${pregunta.respostes[j].resposta}
        </button>
      `;
    }
  
    htmlString += "<hr>";
  }

contenidor.innerHTML = htmlString;