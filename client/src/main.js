const urlInput = document.getElementById('urlInput');
const analyzeBtn = document.getElementById('analyzeBtn');
const loading = document.getElementById('loading');
const resultCard = document.getElementById('resultCard');
const videoThumb = document.getElementById('videoThumb');
const downloadLink = document.getElementById('downloadLink');

// Configuration
const API_BASE_URL = 'http://localhost:5000/api';

/**
 * Trigger Adsterra Popunder placeholder
 */
const triggerPopunder = () => {
    console.log("ADSTERRA: Popunder triggered on action.");
};

/**
 * Show/Hide loading state
 */
const setLoading = (isLoading) => {
    if (isLoading) {
        loading.classList.remove('hidden');
    } else {
        loading.classList.add('hidden');
    }
};

/**
 * Reset result view
 */
const resetView = () => {
    resultCard.classList.add('hidden');
};

/**
 * Analyze Instagram URL
 */
const analyzeUrl = async () => {
    const url = urlInput.value.trim();

    if (!url) {
        alert("Por favor, ingresa un link válido de TikTok, Facebook o Instagram.");
        return;
    }

    // Trigger Popunder on "Analyze" click
    triggerPopunder();

    setLoading(true);
    resetView();

    try {
        const response = await fetch(`${API_BASE_URL}/analyze`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url }),
        });

        const data = await response.json();

        if (data.error) {
            throw new Error(data.message || data.error);
        }

        const result = data.result[0];
        videoThumb.src = result.thumb || 'https://via.placeholder.com/640x360.png?text=Preview+Not+Available';
        downloadLink.href = result.url;
        
        resultCard.classList.remove('hidden');
        resultCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } catch (error) {
        console.error("Error analyzing URL:", error);
        alert(`Error: ${error.message}`);
    } finally {
        setLoading(false);
    }
};

// Event Listeners
analyzeBtn.addEventListener('click', analyzeUrl);

urlInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        analyzeUrl();
    }
});

downloadLink.addEventListener('click', () => {
    triggerPopunder();
});

console.log("SnapSave Frontend Initialized");
