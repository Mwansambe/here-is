<script>
const API_BASE = "http://127.0.0.1:8000/api";

async function fetchProducts() {
  const res = await fetch(`${API_BASE}/products`);
  const data = await res.json();
  console.log(data);
}

async function login(login, password) {
  const res = await fetch(`${API_BASE}/auth/login`, {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({login,password})
  });
  const data = await res.json();
  if (data.status === 'success') {
    localStorage.setItem('token', data.token);
    alert('Login successful');
  } else {
    alert('Login failed');
  }
}
</script>