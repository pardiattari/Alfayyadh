// Cloudflare Worker for Alfayyadh.ID
// Deploy via Cloudflare Dashboard > Workers & Pages > Create Worker
// Route: alfayyadh.id/*

export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);
    const hostname = url.hostname;
    
    // Security headers for all responses
    const securityHeaders = {
      'X-Content-Type-Options': 'nosniff',
      'X-Frame-Options': 'SAMEORIGIN',
      'Referrer-Policy': 'strict-origin-when-cross-origin',
      'Permissions-Policy': 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()',
    };

    // Handle preflight CORS
    if (request.method === 'OPTIONS') {
      return new Response(null, {
        headers: {
          'Access-Control-Allow-Origin': 'https://alfayyadh.id',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
          'Access-Control-Allow-Headers': 'Content-Type',
          'Access-Control-Max-Age': '86400',
          ...securityHeaders
        }
      });
    }

    // Fetch from origin
    const response = await fetch(request);
    
    // Create new response with security headers
    const newHeaders = new Headers(response.headers);
    
    // Add security headers
    Object.entries(securityHeaders).forEach(([key, value]) => {
      newHeaders.set(key, value);
    });

    // Cache static assets aggressively
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|webp|svg|woff|woff2|ttf|eot|ico)$/i)) {
      newHeaders.set('Cache-Control', 'public, max-age=31536000, immutable');
    }
    
    // Cache HTML pages for shorter time
    else if (url.pathname.endsWith('.php') || url.pathname === '/' || url.pathname.endsWith('/')) {
      // Don't cache admin pages
      if (!url.pathname.startsWith('/admin/')) {
        newHeaders.set('Cache-Control', 'public, max-age=300, stale-while-revalidate=600');
      } else {
        newHeaders.set('Cache-Control', 'no-store, private');
      }
    }

    // Remove server header for security
    newHeaders.delete('Server');
    newHeaders.delete('X-Powered-By');

    return new Response(response.body, {
      status: response.status,
      statusText: response.statusText,
      headers: newHeaders
    });
  }
};

// Additional: Scheduled job for cache purge (optional)
// Add via Cloudflare Dashboard > Workers > Triggers > Cron Triggers
// Cron: "0 3 * * *" (daily at 3 AM UTC)
/*
export default {
  async scheduled(event, env, ctx) {
    if (event.cron === "0 3 * * *") {
      // Purge cache for homepage and key pages
      await fetch('https://api.cloudflare.com/client/v4/zones/:zone_identifier/purge_cache', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${env.CF_API_TOKEN}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          files: [
            'https://alfayyadh.id/',
            'https://alfayyadh.id/kategori.php',
            'https://alfayyadh.id/blog.php'
          ]
        })
      });
    }
  }
};
*/
