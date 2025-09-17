import data from './data.js';

window.addEventListener('DOMContentLoaded', (event) => {
  const contenidor = document.getElementById("questionari");
  let current = 0;

  //fetch('http://a22adrmacfir.daw.inspedralbes.cat/data.json')
  //.then(response => response.json())
  //.then(pregunta => console.log(pregunta));
  
  data.preguntes.forEach(p => {
    p.respostes.sort(() => Math.random() - 0.5);
  });

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
    html += `<br><button id="enrere" style="margin-top:20px;">Enrere</button>`;
  
    contenidor.innerHTML = html;
  
    document.getElementById("següent").addEventListener("click", () => {
      current++;
      if (current < data.preguntes.length) {
        mostrarPregunta();
      } else {
        contenidor.innerHTML = `<h2>Has acabat el joc!</h2>`;
      }
    });

    document.getElementById("enrere").addEventListener("click", () => {
      if (current > 0) {
        current--;
        mostrarPregunta();
      }
    });
  }
  
  window.marcarResposta = function(index) {
    const pregunta = data.preguntes[current];
    if (pregunta.respostes[index].correcta) {
      alert("Correcte!");
    } else {
      alert("Incorrecte!");
    }
  };
  
  mostrarPregunta();
}
)