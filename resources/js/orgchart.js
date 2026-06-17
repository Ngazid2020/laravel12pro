/**
 * Org Chart — Arbre de mentorat
 * Initialisation via window.initOrgChart(containerEl)
 * Appelé depuis Alpine x-init après que Livewire a mis à jour le DOM.
 */
import domtoimage from 'dom-to-image-more';

(function () {

    function initOrgChart(containerEl) {
        if (!containerEl) return;

        if (typeof containerEl._ocCleanup === 'function') {
            containerEl._ocCleanup();
        }

        var chartEl = containerEl.querySelector('#oc-chart');
        if (!chartEl) return;

        var raw = chartEl.getAttribute('data-tree');
        if (!raw) return;

        var DATA;
        try { DATA = JSON.parse(raw); } catch (e) { return; }
        if (!DATA || typeof DATA !== 'object') return;

        chartEl.innerHTML = '';

        var scale = 1, px = 0, py = 0;
        var panning = false, sx = 0, sy = 0;
        var rootUl = document.createElement('ul');

        /* ── Création d'un nœud ── */
        function makeNode(person) {
            var li   = document.createElement('li');
            var wrap = document.createElement('div');
            wrap.className = 'oc-wrap' + (person.children && person.children.length ? '' : ' no-ch');

            var img = document.createElement('img');
            img.src           = person.imageUrl || '';
            img.alt           = person.name     || '';
            img.className     = 'oc-avatar';
            img.style.borderColor = person.color || '#6366f1';

            var card = document.createElement('div');
            card.className        = 'oc-card';
            card.style.borderColor = person.color || '#6366f1';

            var nm = document.createElement('div');
            nm.className   = 'oc-name';
            nm.textContent = person.name || '—';

            var rl = document.createElement('div');
            rl.className   = 'oc-role';
            rl.textContent = person.role || '';

            card.appendChild(nm);
            card.appendChild(rl);

            if (person.isSelf || person.isMentor) {
                var bd = document.createElement('span');
                bd.className   = 'oc-badge ' + (person.isMentor ? 'oc-b-mentor' : 'oc-b-self');
                bd.textContent = person.isMentor ? 'Mentor' : 'Vous';
                card.appendChild(bd);
            }

            wrap.appendChild(img);
            wrap.appendChild(card);

            if (person.children && person.children.length) {
                var btn = document.createElement('span');
                btn.className   = 'oc-toggle';
                btn.textContent = '−';
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    li.classList.toggle('oc-collapsed');
                    btn.textContent = li.classList.contains('oc-collapsed') ? '+' : '−';
                });
                wrap.appendChild(btn);
            }

            li.appendChild(wrap);
            return li;
        }

        /* ── Construction récursive ── */
        function build(person, parent) {
            var node = makeNode(person);
            parent.appendChild(node);
            if (person.children && person.children.length) {
                var ul = document.createElement('ul');
                node.appendChild(ul);
                person.children.forEach(function (c) { build(c, ul); });
            }
        }

        chartEl.appendChild(rootUl);
        build(DATA, rootUl);

        /* ── Centrage ── */
        function applyTransform() {
            rootUl.style.transformOrigin = '0 0';
            rootUl.style.transform = 'translate(' + px + 'px,' + py + 'px) scale(' + scale + ')';
        }

        function center() {
            var cw = containerEl.getBoundingClientRect().width || 600;
            var rw = rootUl.getBoundingClientRect().width || 0;
            px = rw > 0 ? Math.round((cw - rw) / 2) : Math.round(cw / 2) - 90;
            py = 40;
            applyTransform();
        }

        requestAnimationFrame(function () { requestAnimationFrame(center); });

        /* ── Export PNG ──
         *  dom-to-image-more délègue le rendu au moteur SVG natif du navigateur :
         *  il n'analyse jamais les fonctions CSS (oklch, etc.), contrairement à html2canvas.
         *  On clone rootUl sans transform dans un wrapper hors-écran, on capture, on nettoie.
         */
        function downloadPng(btnEl) {
            if (btnEl) { btnEl.disabled = true; }

            // Wrapper hors-écran avec le fond et le padding voulus
            var wrap = document.createElement('div');
            wrap.style.cssText = [
                'display:inline-block',
                'padding:40px',
                'background:#f8fafc',
                'position:absolute',
                'left:-99999px',
                'top:0',
            ].join(';');

            // Clone de l'arbre complet, sans transform
            var clone = rootUl.cloneNode(true);
            clone.style.transform       = 'none';
            clone.style.transformOrigin = 'initial';
            wrap.appendChild(clone);
            document.body.appendChild(wrap);

            domtoimage.toPng(wrap, { bgcolor: '#f8fafc', scale: 2 })
                .then(function (dataUrl) {
                    document.body.removeChild(wrap);
                    if (btnEl) { btnEl.disabled = false; }

                    var a = document.createElement('a');
                    a.download = 'organigramme-mentorat.png';
                    a.href     = dataUrl;
                    a.click();
                })
                .catch(function (err) {
                    if (document.body.contains(wrap)) { document.body.removeChild(wrap); }
                    if (btnEl) { btnEl.disabled = false; }
                    console.error('[OC Export]', err);
                    alert('Export échoué : ' + (err && err.message ? err.message : err));
                });
        }

        /* ── API stockée sur l'élément ── */
        containerEl._ocApi = {
            zoom:     function (d) { scale = Math.max(.3, Math.min(scale + d, 2.5)); applyTransform(); },
            reset:    function ()  { scale = 1; center(); },
            download: downloadPng,
        };

        /* ── Pan souris ── */
        var onUp   = function () { panning = false; containerEl.style.cursor = 'grab'; };
        var onMove = function (e) {
            if (!panning) return;
            px = e.clientX - sx;
            py = e.clientY - sy;
            applyTransform();
        };

        containerEl.addEventListener('mousedown', function (e) {
            if (e.target.closest('.oc-toggle')) return;
            panning = true;
            sx = e.clientX - px;
            sy = e.clientY - py;
            containerEl.style.cursor = 'grabbing';
        });

        window.addEventListener('mouseup',   onUp);
        window.addEventListener('mousemove', onMove);

        /* ── Zoom molette ── */
        containerEl.addEventListener('wheel', function (e) {
            e.preventDefault();
            var rect = containerEl.getBoundingClientRect();
            var xs = (e.clientX - rect.left - px) / scale;
            var ys = (e.clientY - rect.top  - py) / scale;
            var d  = e.deltaY < 0 ? 1.1 : 1 / 1.1;
            scale  = Math.max(.3, Math.min(scale * d, 2.5));
            px = e.clientX - rect.left - xs * scale;
            py = e.clientY - rect.top  - ys * scale;
            applyTransform();
        }, { passive: false });

        /* ── Nettoyage ── */
        containerEl._ocCleanup = function () {
            window.removeEventListener('mouseup',   onUp);
            window.removeEventListener('mousemove', onMove);
            delete containerEl._ocApi;
            delete containerEl._ocCleanup;
        };
    }

    /* ── Globaux appelés depuis les boutons HTML ── */
    window.initOrgChart = initOrgChart;

    window.ocZoom = function (d) {
        var c = document.getElementById('oc-container');
        if (c && c._ocApi) c._ocApi.zoom(d);
    };
    window.ocReset = function () {
        var c = document.getElementById('oc-container');
        if (c && c._ocApi) c._ocApi.reset();
    };
    window.ocExport = function (btnEl) {
        var c = document.getElementById('oc-container');
        if (c && c._ocApi) c._ocApi.download(btnEl);
        else alert('Le chart n\'est pas encore initialisé.');
    };

}());
