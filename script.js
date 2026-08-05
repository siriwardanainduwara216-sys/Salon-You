// =========================================================================
// AI Hairstyle Recommender Engine (Standard Vanilla CSS Version)
// =========================================================================

async function analyzeFaceShape() {
    const photoInput = document.getElementById('ai-photo-input');
    if (!photoInput || photoInput.files.length === 0) {
        alert("Please select a valid face profile photograph first.");
        return;
    }

    const cardContainer = document.getElementById('ai-recommender-card');
    cardContainer.innerHTML = `
        <div class="ai-loader-container">
            <div class="spinner"></div>
            <div>
                <p style="font-weight: 700; font-size: 0.9rem; color: #fff;">Running Rule-Based Geometric Vector Scan...</p>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Calculating head contour coordinates dynamically.</p>
            </div>
        </div>
    `;

    const formData = new FormData();
    formData.append('image', photoInput.files[0]);

    try {
        const response = await fetch('generate_ai.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (!result.success) {
            alert("Analysis Engine Interrupted: " + result.message);
            resetAIRecommender();
            return;
        }
        renderMirrorOutput(result);
    } catch (error) {
        console.error("AJAX Error:", error);
        alert("Critical backend communication breakdown with generate_ai.php.");
        resetAIRecommender();
    }
}

function renderMirrorOutput(data) {
    const cardContainer = document.getElementById('ai-recommender-card');

    const hairStyleMap = {
        'crop':      { front: 'crop_front.png',  side: 'crop_side.png',  back: 'crop_back.png' },
        'pompadour': { front: 'pf.png',          side: 'ps.png',         back: 'pb.png' },
        'quiff':     { front: 'quiff_front.png', side: 'quiff_side.png', back: 'quiff_back.png' },
        'side_part': { front: 'sf.png',          side: 'ss.png',         back: 'sb.png' },
        'fade':      { front: 'fade_front.png',  side: 'fade_side.png',  back: 'fade_back.png' },
        'buzz':      { front: 'bf.png',          side: 'bs.png',         back: 'bb.png' }
    };

    const base = 'uploads/images/hairstyles/';
    const style = hairStyleMap[data.filter_key] || hairStyleMap['crop'];
    const compatibilityScores = {
        'crop': 96, 'pompadour': 88, 'quiff': 91,
        'side_part': 94, 'fade': 89, 'buzz': 85
    };
    const score = compatibilityScores[data.filter_key] || 90;

    let viewportHTML = `
        <div class="ai-output-wrapper">

            <!-- Face Shape Badge Bar -->
            <div class="ai-badge-bar">
                <span class="badge-purple">FACE SHAPE: ${data.face_shape.toUpperCase()}</span>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">AI Best Match:</span>
                    <span class="badge-amber">${data.recommendation}</span>
                    <span class="badge-green">${score}% Match</span>
                </div>
            </div>

            <!-- MAIN GRID: Left = User Photo | Right = 4 Views -->
            <div class="ai-main-grid">

                <!-- LEFT: User Uploaded Photo -->
                <div class="ai-box">
                    <div class="ai-box-header">
                        <span class="dot-green"></span> Your Uploaded Photo
                    </div>
                    <div style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                        <div class="ai-uploaded-img-wrapper">
                            <img src="${data.uploaded_image}" alt="Your Original Photo">
                        </div>
                        <div class="ai-details-card">
                            <div class="detail-row">
                                <span>Face Shape</span>
                                <strong style="color: var(--you-amber);">${data.face_shape}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Best Match</span>
                                <strong style="color: var(--you-purple);">${data.recommendation}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Match Score</span>
                                <strong style="color: #34d399;">${score}%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: AI Hairstyle 4 Views -->
                <div class="ai-box">
                    <div class="ai-box-header">
                        <span class="dot-purple"></span> AI Recommended — ${data.recommendation}
                    </div>
                    <div class="ai-four-views-grid">
                        
                        <!-- Front View -->
                        <div class="ai-view-card">
                            <div class="view-label">1. Front View</div>
                            <div class="view-img-box">
                                <img src="${base}${style.front}" alt="Front View"
                                     onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                            </div>
                            <div class="view-sub">Front Render</div>
                        </div>

                        <!-- Left Profile -->
                        <div class="ai-view-card">
                            <div class="view-label">2. Left Profile</div>
                            <div class="view-img-box">
                                <img src="${base}${style.side}" alt="Left Profile"
                                     onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                            </div>
                            <div class="view-sub">Left Profile</div>
                        </div>

                        <!-- Right Profile (Flipped) -->
                        <div class="ai-view-card">
                            <div class="view-label">3. Right Profile</div>
                            <div class="view-img-box">
                                <img src="${base}${style.side}" class="flip-horizontal" alt="Right Profile"
                                     onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                            </div>
                            <div class="view-sub">Right Profile</div>
                        </div>

                        <!-- Rear View -->
                        <div class="ai-view-card">
                            <div class="view-label">4. Rear View</div>
                            <div class="view-img-box">
                                <img src="${base}${style.back}" alt="Rear View"
                                     onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                            </div>
                            <div class="view-sub">Rear Taper</div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Alternative Catalog Styles -->
            <div class="ai-catalog-container">
                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <h5 style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                        <i class="fas fa-th-large" style="color: var(--you-amber);"></i> Explore Alternative Hairstyle Options
                    </h5>
                    <p style="font-size: 0.75rem; color: var(--text-muted);">Click any style below to update the 4-view preview.</p>
                </div>
                <div class="ai-catalog-grid">
                    ${Object.entries(data.full_catalog).map(([shape, layout]) => {
                        const isActive = layout.key === data.filter_key;
                        const s = hairStyleMap[layout.key] || hairStyleMap['crop'];
                        return `
                            <div onclick="swapActiveFilter('${layout.key}', '${layout.name}', '${shape}', '${data.uploaded_image}');"
                                 class="catalog-item ${isActive ? 'active' : ''}">
                                <div class="catalog-img">
                                    <img src="${base}${s.front}" alt="${layout.name}"
                                         onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center\'><i class=\'fas fa-cut\' style=\'color:#475569\'></i></div>'">
                                </div>
                                <div class="catalog-info">
                                    <p>${layout.name}</p>
                                    <small>${shape}</small>
                                    ${isActive ? '<small style="color: var(--you-amber); font-weight: bold;">✓ Active</small>' : ''}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>

            <!-- Reset Action Button -->
            <div style="display: flex; justify-content: flex-end;">
                <button onclick="resetAIRecommender();" class="btn-secondary">
                    <i class="fas fa-redo"></i> Clear & Re-Upload
                </button>
            </div>

        </div>
    `;

    cardContainer.innerHTML = viewportHTML;
}

function swapActiveFilter(filterKey, styleName, faceShape, imgPath) {
    const virtualDataStruct = {
        success: true,
        face_shape: faceShape,
        recommendation: styleName,
        filter_key: filterKey,
        uploaded_image: imgPath,
        full_catalog: {
            'Oval':    { name: 'Modern Textured Crop', key: 'crop' },
            'Square':  { name: 'Pompadour',            key: 'pompadour' },
            'Round':   { name: 'Quiff',                key: 'quiff' },
            'Oblong':  { name: 'Side Part',            key: 'side_part' },
            'Diamond': { name: 'Low Fade',             key: 'fade' },
            'Heart':   { name: 'Buzz Cut',             key: 'buzz' }
        }
    };
    renderMirrorOutput(virtualDataStruct);
}

function resetAIRecommender() {
    const cardContainer = document.getElementById('ai-recommender-card');
    document.getElementById('ai-photo-input').value = "";
    cardContainer.innerHTML = `
        <div class="ai-idle-state">
            <div class="ai-idle-icon">
                <i class="fas fa-user-astronaut"></i>
            </div>
            <div class="ai-idle-text">
                <h4>Interactive Mirror System Idle</h4>
                <p>Please select a clear portrait selfie to apply 360° haircut configurations over your facial structure.</p>
            </div>
            <button onclick="document.getElementById('ai-photo-input').click()" class="btn-primary">
                <i class="fas fa-camera"></i> Upload Selfie
            </button>
        </div>
    `;
}