const https = require('https');
const http = require('http');

function fetchURL(url) {
  return new Promise((resolve, reject) => {
    const protocol = url.startsWith('https') ? https : http;
    protocol.get(url, { 
      headers: { 'User-Agent': 'Mozilla/5.0' }
    }, (res) => {
      let data = '';
      res.on('data', chunk => { data += chunk; });
      res.on('end', () => { 
        resolve({ 
          status: res.statusCode,
          headers: res.headers,
          body: data 
        });
      });
    }).on('error', reject);
  });
}

async function diagnose() {
  try {
    console.log('Fetching: https://nountutor.com.ng/admin/courses/3/lessons/create\n');
    const result = await fetchURL('https://nountutor.com.ng/admin/courses/3/lessons/create');
    
    console.log('Response Status:', result.status);
    console.log('Response Headers:', result.headers);
    console.log('\nResponse Body (first 500 chars):');
    console.log(result.body.substring(0, 500));
    
    if (result.status === 302 || result.status === 301) {
      console.log('\n→ Page redirects to:', result.headers.location);
    }
    
    if (result.body.includes('login') || result.body.includes('not authenticated')) {
      console.log('\n⚠️ Requires authentication - expected without session cookie');
    }
    
  } catch (error) {
    console.error('Error:', error.message);
  }
}

diagnose();
