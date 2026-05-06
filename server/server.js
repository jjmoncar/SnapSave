const express = require('express');
const cors = require('cors');
const axios = require('axios');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 5000;

app.use(cors());
app.use(express.json());

// Simulation data for when no API key is provided
const simulationData = {
    status: true,
    result: [
        {
            url: "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video/mp4",
            thumb: "https://via.placeholder.com/640x360.png?text=SnapSave+Simulation",
            size: "1048576"
        }
    ]
};

app.post('/api/analyze', async (req, res) => {
    const { url } = req.body;

    if (!url) {
        return res.status(400).json({ error: "URL is required" });
    }

    try {
        let endpoint = '';
        let params = {};
        let platform = '';

        // DETECCIÓN DE PLATAFORMA
        if (url.includes('instagram.com')) {
            platform = 'instagram';
            const urlParts = url.split('?')[0].split('/');
            const shortcode = urlParts.find((part, index) => 
                index > 0 && (urlParts[index-1] === 'p' || urlParts[index-1] === 'reel' || urlParts[index-1] === 'reels')
            ) || urlParts[urlParts.length - 1] || urlParts[urlParts.length - 2];
            
            endpoint = '/instagram/v3/media/post/details';
            params = { shortcode, renderableFormats: '720p,highres' };
        } 

        else if (url.includes('tiktok.com')) {
            platform = 'tiktok';
            endpoint = '/tiktok/v3/post/details';
            params = { url: url.split('?')[0] };
        }
        else if (url.includes('facebook.com') || url.includes('fb.watch')) {
            platform = 'facebook';
            endpoint = '/facebook/v3/post/details';
            params = { url: url.split('?')[0] };
        }
        else {
            return res.status(400).json({ error: "Plataforma no soportada aún." });
        }

        console.log(`Analizando ${platform}:`, url);

        const options = {
            method: 'GET',
            url: `https://${process.env.RAPIDAPI_HOST}${endpoint}`,
            params: params,
            headers: {
                'X-RapidAPI-Key': process.env.RAPIDAPI_KEY,
                'X-RapidAPI-Host': process.env.RAPIDAPI_HOST
            }
        };

        const response = await axios.request(options);
        
        console.log(`--- RESPUESTA DE LA API (${platform.toUpperCase()}) ---`);
        const data = response.data;
        // console.log(JSON.stringify(data, null, 2)); // Descomentar para debug profundo
        let rawVideo = '';
        let rawThumb = '';

        // FUNCIÓN DE BÚSQUEDA EXHAUSTIVA
        function findVideoUrl(obj) {
            if (!obj) return null;
            // Si es un string y parece un video de red social o CDN
            if (typeof obj === 'string' && (obj.includes('video') || obj.includes('.mp4') || obj.includes('fbcdn') || obj.includes('tiktokv'))) {
                if (obj.startsWith('http')) return obj;
            }
            // Si es un objeto o array, seguimos buscando
            if (typeof obj === 'object') {
                for (let key in obj) {
                    const found = findVideoUrl(obj[key]);
                    if (found) return found;
                }
            }
            return null;
        }

        // 1. Intentamos el mapeo estándar
        if (platform === 'instagram' || platform === 'tiktok' || platform === 'facebook') {
            const contents = data.contents && data.contents[0];
            const videos = contents && contents.videos;
            rawVideo = (videos && videos.length > 0) ? videos[0].url : null;
            rawThumb = data.metadata ? data.metadata.thumbnailUrl : (contents && contents.thumbnailUrl);
        }
        if (!rawVideo) {
            console.log("Mapeo estándar falló. Iniciando búsqueda agresiva...");
            rawVideo = findVideoUrl(data);
        }

        // 3. Buscar miniatura si no hay
        if (!rawThumb) {
            if (data.thumbnail) rawThumb = data.thumbnail;
            else if (data.picture) rawThumb = data.picture;
        }

        console.log(`Video Final Encontrado:`, rawVideo ? "SÍ" : "NO");

        if (!rawVideo) {
            return res.status(404).json({ 
                error: "No se encontró un link de descarga.",
                debug: "La API respondió pero no se detectó un video compatible."
            });
        }

        const randomName = Math.random().toString(36).substring(2, 14) + Math.random().toString(36).substring(2, 14);
        const fileName = randomName.substring(0, 12);
        let videoLink = `http://localhost:${PORT}/api/download?url=${encodeURIComponent(rawVideo)}&filename=${fileName}.mp4&platform=${platform}`;
        let thumbLink = rawThumb ? `http://localhost:${PORT}/api/download?url=${encodeURIComponent(rawThumb)}&isImage=true` : null;

        res.json({
            status: true,
            result: [{ url: videoLink, thumb: thumbLink, type: "video/mp4" }]
        });

    } catch (error) {
        if (error.response) {
            console.error("Error de la API (Status):", error.response.status);
            console.error("Detalle del error:", JSON.stringify(error.response.data, null, 2));
        } else {
            console.error("Error local:", error.message);
        }
        res.status(500).json({ 
            error: "Failed to process video link", 
            message: error.message 
        });
    }
});

// NUEVA RUTA: Proxy para forzar descarga y evitar bloqueos de Instagram/TikTok/Facebook
app.get('/api/download', async (req, res) => {
    const { url, filename, isImage, platform } = req.query;

    if (!url) return res.status(400).send("URL is required");

    try {
        const headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
            'Accept': '*/*',
            'Accept-Language': 'en-US,en;q=0.9',
            'Referer': 'https://www.google.com/',
            'Origin': 'https://www.google.com/'
        };

        // Intentamos una técnica de "streaming" directo
        const response = await axios({
            method: 'get',
            url: url,
            responseType: 'stream',
            headers: headers,
            timeout: 15000
        });

        if (isImage) {
            res.setHeader('Content-Type', 'image/jpeg');
            res.setHeader('Cache-Control', 'public, max-age=86400');
        } else {
            res.setHeader('Content-Disposition', `attachment; filename="${filename || 'video.mp4'}"`);
            res.setHeader('Content-Type', 'video/mp4');
        }

        response.data.pipe(res);
    } catch (error) {
        console.error("Download Proxy Error:", error.message);
        
        // --- EL TRUCO FINAL: REDIRECCIÓN ---
        // Si el proxy falla con 403, redirigimos al usuario al link directo. 
        // A veces el link directo funciona en el navegador del usuario aunque falle en el servidor.
        if (error.response && error.response.status === 403) {
            console.log("Detectado 403. Redirigiendo a link directo como fallback...");
            return res.redirect(url);
        }

        res.status(500).send("No se pudo procesar la descarga. El enlace ha expirado o es privado.");
    }
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
    console.log(`Simulation Mode: ${(!process.env.RAPIDAPI_KEY || process.env.RAPIDAPI_KEY === 'your_rapidapi_key_here') ? 'ON' : 'OFF'}`);
});
