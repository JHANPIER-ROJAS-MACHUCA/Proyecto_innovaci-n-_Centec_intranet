export const API_BASE = '/Centecp_Intranet/backend/public/index.php';

async function request(path, { method = 'GET', body, timeout = 8000 } = {}) {
  const ctrl = new AbortController();
  const t = setTimeout(() => ctrl.abort(), timeout);
  try {
    const res = await fetch(`${API_BASE}${path}`, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: body ? JSON.stringify(body) : undefined,
      signal: ctrl.signal,
      credentials: 'same-origin',
    });
    if (!res.ok) throw new Error(`API ${res.status} en ${path}`);
    const ct = res.headers.get('content-type') || '';
    return ct.includes('json') ? res.json() : res.text();
  } catch (e) {
    if (e.name === 'AbortError') throw new Error(`Timeout (${timeout}ms) en ${path}`);
    throw e;
  } finally {
    clearTimeout(t);
  }
}

export const api = {
  get: (p, o) => request(p, { ...o, method: 'GET' }),
  post: (p, b, o) => request(p, { ...o, method: 'POST', body: b }),
};
