<script>
    function aplicarZoom(img) {
        if (img.dataset.zoomInit) return;
        img.dataset.zoomInit = '1';

        let scale = 1;
        let isDragging = false;
        let startX, startY, translateX = 0, translateY = 0;
        let lastTouchDist = null;

        img.style.cursor = 'zoom-in';
        img.style.transition = 'transform 0.1s ease';
        img.style.transformOrigin = 'center center';

        const applyTransform = () => {
            img.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
            img.dataset.zoomScale = String(scale);
        };

        const reset = () => {
            scale = 1; translateX = 0; translateY = 0;
            img.style.transform = 'scale(1) translate(0, 0)';
            img.style.cursor = 'zoom-in';
            img.dataset.zoomScale = '1';
        };

        img.addEventListener('wheel', (e) => {
            e.preventDefault();
            scale += e.deltaY * -0.002;
            scale = Math.min(Math.max(1, scale), 4);
            img.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
            applyTransform();
        }, { passive: false });

        img.addEventListener('mousedown', (e) => {
            if (scale <= 1) return;
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            img.style.cursor = 'grabbing';
        });
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            applyTransform();
        });
        document.addEventListener('mouseup', () => {
            isDragging = false;
            img.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
        });

        img.addEventListener('touchstart', (e) => {
            if (e.touches.length === 2) {
                lastTouchDist = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
            } else if (e.touches.length === 1 && scale > 1) {
                isDragging = true;
                startX = e.touches[0].clientX - translateX;
                startY = e.touches[0].clientY - translateY;
            }
        }, { passive: true });

        img.addEventListener('touchmove', (e) => {
            if (e.touches.length === 2 && lastTouchDist) {
                e.preventDefault();
                const dist = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                scale *= dist / lastTouchDist;
                scale = Math.min(Math.max(1, scale), 4);
                lastTouchDist = dist;
                applyTransform();
            } else if (e.touches.length === 1 && isDragging) {
                translateX = e.touches[0].clientX - startX;
                translateY = e.touches[0].clientY - startY;
                applyTransform();
            }
        }, { passive: false });

        img.addEventListener('touchend', () => {
            isDragging = false;
            lastTouchDist = null;
        });

        img.addEventListener('dblclick', reset);
    }

    function iniciarLightboxConZoom(selector = '.glightbox') {
        return GLightbox({
            selector,
            touchNavigation: true,
            onOpen: () => {
                const waitForImage = setInterval(() => {
                    const imgs = document.querySelectorAll('.gslide-image img');
                    const img = imgs[imgs.length - 1];
                    if (img && img.src.startsWith('http') && img.complete) {
                        clearInterval(waitForImage);
                        aplicarZoom(img);
                    } else if (img && img.src.startsWith('http')) {
                        clearInterval(waitForImage);
                        img.addEventListener('load', () => aplicarZoom(img));
                    }
                }, 100);
                setTimeout(() => clearInterval(waitForImage), 5000);
            },
            onSlideChanged: () => {
                const waitForImage = setInterval(() => {
                    const imgs = document.querySelectorAll('.gslide-image img');
                    const img = imgs[imgs.length - 1];
                    if (img && img.src.startsWith('http') && !img.dataset.zoomInit) {
                        clearInterval(waitForImage);
                        if (img.complete) aplicarZoom(img);
                        else img.addEventListener('load', () => aplicarZoom(img));
                    }
                }, 100);
                setTimeout(() => clearInterval(waitForImage), 5000);
            },
        });
    }
</script>
