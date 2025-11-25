document.addEventListener('DOMContentLoaded', function() {
    const path = document.querySelector('#cable-path');
    const glowPath = document.querySelector('#cable-glow');

    if (!path || !glowPath) {
        console.warn("Elementos SVG #cable-path ou #cable-glow não encontrados.");
        return; 
    }

    const pathLength = path.getTotalLength();

    path.style.strokeDasharray = pathLength;
    path.style.strokeDashoffset = pathLength;
    glowPath.style.strokeDasharray = pathLength;
    glowPath.style.strokeDashoffset = pathLength;

    const handleScroll = () => {
        const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        const scrollHeight = (document.documentElement.scrollHeight || document.body.scrollHeight) - document.documentElement.clientHeight;
        const scrollPercentage = scrollHeight > 0 ? scrollTop / scrollHeight : 0;
        const drawLength = pathLength * scrollPercentage;

        path.style.strokeDashoffset = Math.max(0, pathLength - drawLength);
        glowPath.style.strokeDashoffset = Math.max(0, pathLength - drawLength);
    };

    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
});
