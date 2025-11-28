const theme = localStorage.getItem('theme') || 'dark';
if (theme === 'light') document.documentElement.classList.add('light-theme');
function exportPDF() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando...';
    btn.disabled = true;
    const exportBtn = document.querySelector('.export-actions');
    const shareBtn = document.querySelector('.share-actions');
    exportBtn.style.display = 'none';
    shareBtn.style.display = 'none';
    const element = document.querySelector('.presskit');
    const opt = {
        margin: [10, 10, 10, 10],
        filename: window.presskitSlug + '.pdf',
        image: { type: 'jpeg', quality: 0.95 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            logging: false, 
            backgroundColor: '#0a0a0f',
            letterRendering: true,
            allowTaint: true,
            scrollY: -window.scrollY,
            scrollX: 0
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['avoid-all', 'css'] }
    };
    html2pdf().set(opt).from(element).save().then(() => {
        exportBtn.style.display = 'flex';
        shareBtn.style.display = 'flex';
        btn.innerHTML = originalText;
        btn.disabled = false;
    }).catch(() => {
        exportBtn.style.display = 'flex';
        shareBtn.style.display = 'flex';
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Erro ao gerar PDF');
    });
}
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('copyBtn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Link Copiado!';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('copied');
        }, 2000);
    }).catch(() => {
        alert('Erro ao copiar link');
    });
}
let artists = [window.artistName || 'Artista'];
function addArtist() {
    const name = prompt('Nome do artista:');
    if (name && name.trim()) {
        artists.push(name.trim());
        updateArtistsDisplay();
    }
}
function removeArtist(index) {
    artists.splice(index, 1);
    updateArtistsDisplay();
}
function updateArtistsDisplay() {
    const display = document.getElementById('artists-display');
    display.innerHTML = artists.map((artist, index) => 
        `<span style="display: inline-block; margin: 5px; padding: 5px 10px; background: rgba(255,255,255,0.1); border-radius: 5px;">
            ${artist}
            ${index > 0 ? `<i class="fas fa-times" onclick="removeArtist(${index})" style="margin-left: 8px; cursor: pointer; color: #dc3545;"></i>` : ''}
        </span>`
    ).join('');
}
let currentImageIndex = 0;
const mediaGallery = window.mediaGallery || [];
function openImageModal(src) {
    currentImageIndex = mediaGallery.findIndex(m => m.path === src);
    const modal = document.getElementById('imageModal');
    displayMedia(src, mediaGallery[currentImageIndex].type);
    modal.classList.add('show');
    updateNavButtons();
}
function displayMedia(src, type) {
    const container = document.getElementById('modalMediaContainer');
    if (type === 'image') {
        container.innerHTML = `<img src="${src}" alt="Preview" onclick="event.stopPropagation()">`;
    } else if (type === 'video') {
        container.innerHTML = `<video src="${src}" controls onclick="event.stopPropagation()"></video>`;
    } else {
        container.innerHTML = `<audio src="${src}" controls onclick="event.stopPropagation()"></audio>`;
    }
}
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('show');
}
function navigateImage(direction) {
    currentImageIndex += direction;
    if (currentImageIndex < 0) currentImageIndex = mediaGallery.length - 1;
    if (currentImageIndex >= mediaGallery.length) currentImageIndex = 0;
    displayMedia(mediaGallery[currentImageIndex].path, mediaGallery[currentImageIndex].type);
    updateNavButtons();
}
function updateNavButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    if (mediaGallery.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }
}
