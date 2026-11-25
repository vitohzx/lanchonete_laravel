const baseUrl = 'http://127.0.0.1:8000';
const tokenKey = 'pw3_token';

function getToken(){
  return localStorage.getItem(tokenKey);
}

function setToken(t){
  if(t) localStorage.setItem(tokenKey, t);
}

function clearToken(){
  localStorage.removeItem(tokenKey);
}

async function api(path, options = {}){
  const headers = options.headers || {};
  headers['Accept'] = 'application/json';

  const token = getToken();
  if(token){
    headers['Authorization'] = 'Bearer ' + token;
  }

  const resp = await fetch(baseUrl + path, { ...options, headers });
  const text = await resp.text();
  let data;
  try { data = text ? JSON.parse(text) : null; } catch { data = { raw: text }; }

  if(!resp.ok){
    const msg = (data && data.message) ? data.message : ('HTTP ' + resp.status);
    throw new Error(msg);
  }

  return data;
}

document.getElementById('btnLogin').addEventListener('click', async () => {
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  try {
    const data = await api('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password, device_name: 'cliente-js' })
    });

    setToken(data.token);
    alert('Login OK!');
  } catch (e){
    alert('Erro: ' + e.message);
  }
});

document.getElementById('btnLoad').addEventListener('click', async () => {
  try {
    const data = await api('/api/produtos?sort=nome&dir=asc');
    document.getElementById('out').textContent = JSON.stringify(data, null, 2);
  } catch (e){
    alert('Erro: ' + e.message);
  }
});

document.getElementById('btnLogout').addEventListener('click', async () => {
  try {
    await api('/api/logout', { method: 'POST' });
  } catch(e){
    // se token estiver inválido, ainda assim limpe
  }
  clearToken();
  alert('Logout OK');
});