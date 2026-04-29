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

    // Check if RapidAPI Key is provided
    if (!process.env.RAPIDAPI_KEY || process.env.RAPIDAPI_KEY === 'your_rapidapi_key_here') {
        console.log("Using Simulation Mode (No API key provided)");
        // Add a delay to simulate network request
        await new Promise(resolve => setTimeout(resolve, 1500));
        return res.json(simulationData);
    }

    try {
        // Extraer el shortcode de la URL de Instagram
        // Formatos comunes: /p/SHORTCODE/ o /reel/SHORTCODE/
        const urlParts = url.split('/');
        const shortcode = urlParts.find((part, index) => 
            index > 0 && (urlParts[index-1] === 'p' || urlParts[index-1] === 'reel' || urlParts[index-1] === 'reels')
        ) || urlParts[urlParts.length - 1] || urlParts[urlParts.length - 2];

        console.log("Analizando shortcode:", shortcode);

        const options = {
            method: 'GET',
            url: `https://${process.env.RAPIDAPI_HOST}/instagram/v3/media/post/details`,
            params: { 
                shortcode: shortcode,
                renderableFormats: '720p,highres'
            },
            headers: {
                'X-RapidAPI-Key': process.env.RAPIDAPI_KEY,
                'X-RapidAPI-Host': process.env.RAPIDAPI_HOST
            }
        };

        console.log("Llamando a:", options.url);
        const response = await axios.request(options);
        
        // --- LOG DE DEPURACIÓN ---
        // console.log("Respuesta de la API:", JSON.stringify(response.data, null, 2));
        // -------------------------

        // Mapeo exacto según tu respuesta de terminal
        const contents = response.data.contents && response.data.contents[0];
        const videos = contents && contents.videos;
        const metadata = response.data.metadata;

        // Buscamos el video de mayor calidad (usualmente el primero en la lista de 'videos')
        const rawVideoLink = (videos && videos.length > 0) ? videos[0].url : null;
        const rawThumbLink = metadata ? metadata.thumbnailUrl : (contents && contents.thumbnailUrl);

        // Usamos nuestro propio servidor como proxy para evitar bloqueos y forzar descarga
        const videoLink = rawVideoLink ? `http://localhost:${PORT}/api/download?url=${encodeURIComponent(rawVideoLink)}&filename=SnapSave_${shortcode}.mp4` : null;
        const thumbLink = rawThumbLink ? `http://localhost:${PORT}/api/download?url=${encodeURIComponent(rawThumbLink)}&isImage=true` : null;

        console.log("Video procesado para descarga directa:", videoLink ? "SÍ" : "NO");

        const formattedResponse = {
            status: true,
            result: [
                {
                    url: videoLink,
                    thumb: thumbLink,
                    type: "video/mp4"
                }
            ]
        };

        res.json(formattedResponse);
    } catch (error) {
        if (error.response) {
            console.error("Error de la API (Status):", error.response.status);
        } else {
            console.error("Error local:", error.message);
        }
        res.status(500).json({ 
            error: "Failed to process video link", 
            message: error.message 
        });
    }
});

// NUEVA RUTA: Proxy para forzar descarga y evitar bloqueos de Instagram
app.get('/api/download', async (req, res) => {
    const { url, filename, isImage } = req.query;

    if (!url) return res.status(400).send("URL is required");

    try {
        const response = await axios({
            method: 'get',
            url: url,
            responseType: 'stream',
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            }
        });

        if (isImage) {
            res.setHeader('Content-Type', 'image/jpeg');
        } else {
            res.setHeader('Content-Disposition', `attachment; filename="${filename || 'video.mp4'}"`);
            res.setHeader('Content-Type', 'video/mp4');
        }

        response.data.pipe(res);
    } catch (error) {
        console.error("Download Proxy Error:", error.message);
        res.status(500).send("Error al procesar el archivo");
    }
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
    console.log(`Simulation Mode: ${(!process.env.RAPIDAPI_KEY || process.env.RAPIDAPI_KEY === 'your_rapidapi_key_here') ? 'ON' : 'OFF'}`);
});
