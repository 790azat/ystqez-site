<?php

return [
    // Local media (public/media/...) is NOT deployed to Vercel; it is served from a CDN
    // (jsDelivr mirror of the GitHub repo by default). Set MEDIA_CDN_URL to empty to serve locally.
    'cdn_url' => env('MEDIA_CDN_URL', 'https://cdn.jsdelivr.net/gh/790azat/ystqez-site@main/public'),
];
