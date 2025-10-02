// Cambia esto por el nombre real del archivo PHP (el de tu switch action)
const API_URL = '../back-end/admin/admin.php';

const listDiv = document.getElementById('list'); // Div donde se carga el listado
const btnRefresh = document.getElementById('btn-refresh');  // Botón de recarga

const formCreate = document.getElementById('form-create'); // Formulario de creación
const createAnswersDiv = document.getElementById('create-answers'); // Div donde van las respuestas del form de crear
const btnAddAnswer = document.getElementById('btn-add-answer'); // Botón para añadir respuesta en el form de crear
const createStatus = document.getElementById('create-status'); // Span para mensajes de estado del form de crear

// Estado local para el formulario de creación
let createAnswers = [];

// Escapa HTML para evitar XSS
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, s => (
    {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]
    ));
}

// Añade una fila de respuesta al form de crear
function addCreateAnswerRow(text = '', isCorrect = false) {
    const idx = createAnswers.length;
    createAnswers.push({ text, is_correct: isCorrect ? 1 : 0 });

    // Crear elementos
    const row = document.createElement('div');
    row.className = 'row';
    row.innerHTML = `
    <input type="text" placeholder="Text de la resposta" data-idx="${idx}" value="${escapeHtml(text)}">
    <label class="small">
        <input type="checkbox" class="chk-correct" data-idx="${idx}" ${isCorrect ? 'checked' : ''}>
        Correcta
    </label>
    <button type="button" class="btn-del small" data-idx="${idx}">Eliminar</button>
    `;

    // Eventos
    row.querySelector('input[type="text"]').addEventListener('input', (e) => {
    const i = Number(e.target.dataset.idx);
    createAnswers[i].text = e.target.value;
    });
    row.querySelector('.chk-correct').addEventListener('change', (e) => {
    const i = Number(e.target.dataset.idx);
    createAnswers[i].is_correct = e.target.checked ? 1 : 0;
    });
    row.querySelector('.btn-del').addEventListener('click', (e) => {
    const i = Number(e.target.dataset.idx);
    createAnswers[i] = null; // marcar a eliminar
    row.remove();
    });

    createAnswersDiv.appendChild(row);
}

// Setup inicial: al menos dos respuestas
addCreateAnswerRow('', false);
addCreateAnswerRow('', false);
btnAddAnswer.addEventListener('click', () => addCreateAnswerRow('', false));

// Crear pregunta
formCreate.addEventListener('submit', async (e) => {
    e.preventDefault();
    createStatus.textContent = '';
    const text = formCreate.pregunta_text.value.trim();
    const img = formCreate.pregunta_img.value.trim();

    // Filtrar respuestas válidas (no vacías)
    const validAnswers = createAnswers
    .map((r) => r)
    .filter(r => r && r.text && r.text.trim().length > 0);

    // Validaciones
    if (!text) {
    createStatus.textContent = 'Falta el text de la pregunta.';
    createStatus.className = 'small err';
    return;
    }
    if (validAnswers.length < 2) {
    createStatus.textContent = 'Calen almenys 2 respostes.';
    createStatus.className = 'small err';
    return;
    }

    // Almenys una correcta
    const fd = new FormData();
    fd.append('action', 'create');
    fd.append('pregunta_text', text);
    fd.append('pregunta_img', img);

    // Añadir respuestas
    validAnswers.forEach((r, pos) => {
    fd.append(`respostes[${pos}][text]`, r.text.trim());
    fd.append(`respostes[${pos}][is_correct]`, r.is_correct);
    });

    // Enviar
    try {
    const res = await fetch(API_URL, { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
        createStatus.textContent = `Creada (ID ${json.pregunta_id}).`;
        createStatus.className = 'small ok';
        formCreate.reset();
        createAnswersDiv.innerHTML = '';
        createAnswers = [];
        addCreateAnswerRow('', false);
        addCreateAnswerRow('', false);
        loadList();
    } else {
        createStatus.textContent = json.msg || 'Error creant.';
        createStatus.className = 'small err';
    }
    } catch (err) {
    createStatus.textContent = 'Error de xarxa.';
    createStatus.className = 'small err';
    console.error(err);
    }
});

// Cargar listado
async function loadList() {
    listDiv.textContent = 'Carregant...';
    try {
    const res = await fetch(`${API_URL}?action=read`);
    const data = await res.json();
    renderList(data);
    } catch (err) {
    listDiv.innerHTML = '<p class="err">Error carregant preguntes.</p>';
    console.error(err);
    }
}
// Renderizar listado
function renderList(preguntes) {
    if (!Array.isArray(preguntes) || preguntes.length === 0) {
    listDiv.innerHTML = '<p class="muted">No hi ha preguntes.</p>';
    return;
    }
    let html = `
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Pregunta</th>
            <th>Respostes</th>
            <th>Accions</th>
        </tr>
        </thead>
        <tbody>
    `;
    preguntes.forEach(p => {
    const answers = Array.isArray(p.respostes) ? p.respostes : [];
    const resHTML = answers.map(r => {
        const mark = r.is_correct ? ' ✅' : '';
        return `<span class="answer-badge">${escapeHtml(r.text)}${mark}</span>`;
    }).join(' ');
    html += `
        <tr data-pid="${p.id}">
        <td>${p.id}</td>
        <td>
            <div>${escapeHtml(p.text)}</div>
            ${p.imatge ? `<div class="small muted">${escapeHtml(p.imatge)}</div>` : ''}
        </td>
        <td>${resHTML || '<span class="muted small">Sense respostes</span>'}</td>
        <td class="controls">
            <button class="btn-edit">Editar</button>
            <button class="btn-delete">Eliminar</button>
        </td>
        </tr>
        <tr class="inline-form" data-edit="${p.id}" style="display:none;">
        <td colspan="4">
            ${renderEditForm(p)}
        </td>
        </tr>
    `;
    });
    html += `</tbody></table>`;
    listDiv.innerHTML = html;

    // Toggle edición
    listDiv.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const tr = e.target.closest('tr');
        const pid = Number(tr.dataset.pid);
        const formRow = listDiv.querySelector(`tr[data-edit="${pid}"]`);
        formRow.style.display = formRow.style.display === 'none' ? '' : 'none';
    });
    });

    // Eliminar
    listDiv.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        const tr = e.target.closest('tr');
        const pid = Number(tr.dataset.pid);
        if (!confirm(`Eliminar pregunta ${pid}?`)) return;
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('pregunta_id', pid);
        try {
        const res = await fetch(API_URL, { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            loadList();
        } else {
            alert(json.msg || 'Error eliminant');
        }
        } catch (err) {
        alert('Error de xarxa eliminant');
        console.error(err);
        }
    });
    });

    // Guardar edición
    listDiv.querySelectorAll('form.edit-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const pid = Number(form.dataset.pid);
        const fd = new FormData();
        fd.append('action', 'update');
        fd.append('pregunta_id', pid);
        fd.append('pregunta_text', form.querySelector('input[name="pregunta_text"]').value.trim());
        fd.append('pregunta_img', form.querySelector('input[name="pregunta_img"]').value.trim());

        // Recoger respuestas
        const rows = form.querySelectorAll('.answers .row');
        let pos = 0;
        rows.forEach(row => {
        const rid = row.querySelector('input[name="rid"]').value.trim();
        const txt = row.querySelector('input[name="rtext"]').value.trim();
        const chk = row.querySelector('input[name="rcorrect"]').checked ? 1 : 0;

        // Solo si tiene texto
        if (txt.length > 0) {
            if (rid) fd.append(`respostes[${pos}][id]`, Number(rid));
            fd.append(`respostes[${pos}][text]`, txt);
            fd.append(`respostes[${pos}][is_correct]`, chk);
            pos++;
        }
        });

        // Validaciones básicas
        try {
        const res = await fetch(API_URL, { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            alert('Guardat');
            loadList();
        } else {
            alert(json.msg || 'Error guardant');
        }
        } catch (err) {
        alert('Error de xarxa guardant');
        console.error(err);
        }
    });
    });
}

// Renderizar formulario de edición
function renderEditForm(p) {
    const imgVal = p.imatge || '';
    const answers = Array.isArray(p.respostes) ? p.respostes : [];
    const rows = answers.map(a => `
    <div class="row">
        <input type="hidden" name="rid" value="${a.id}">
        <input type="text" name="rtext" value="${escapeHtml(a.text)}" placeholder="Text resposta">
        <label class="small">
        <input type="checkbox" name="rcorrect" ${a.is_correct ? 'checked' : ''}> Correcta
        </label>
    </div>
    `).join('');
    const newRow = `
    <div class="row">
        <input type="hidden" name="rid" value="">
        <input type="text" name="rtext" value="" placeholder="Nova resposta">
        <label class="small">
        <input type="checkbox" name="rcorrect"> Correcta
        </label>
    </div>
    `;
    return `
    <form class="edit-form" data-pid="${p.id}">
        <div class="row">
        <label style="width:130px">Text:</label>
        <input type="text" name="pregunta_text" value="${escapeHtml(p.text)}" required>
        </div>
        <div class="row">
        <label style="width:130px">Imatge (URL):</label>
        <input type="text" name="pregunta_img" value="${escapeHtml(imgVal)}">
        </div>
        <div class="answers">
        ${rows}
        ${newRow}
        </div>
        <div class="row">
        <button type="submit">Guardar</button>
        </div>
    </form>
    `;
}

// Inicializar
btnRefresh.addEventListener('click', loadList);
loadList();