<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ontorio-logo.png') }}">


        <!-- Fonts loaded via CSS @font-face / system fallback -->

        <!-- Scripts -->
        <!-- Chart.js & ApexCharts bundled via Vite (app.js) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 overflow-x-hidden">
        <!-- Skip to content -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[200] focus:bg-white focus:text-[#cb2d2d] focus:px-4 focus:py-2 focus:rounded-xl focus:shadow-2xl focus:font-bold focus:outline-none focus:ring-2 focus:ring-[#cb2d2d]">
            Passer au contenu principal
        </a>

        <!-- Barre de chargement globale -->
        <div id="page-loader-bar"></div>

        <div class="min-h-screen flex overflow-x-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col lg:pl-72 transition-all duration-300 min-w-0">
                <!-- Topbar -->
                @include('layouts.topbar')

                <!-- Main Content -->
                <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Document Preview Modal Globale -->
        <div id="global-doc-preview-modal" role="dialog" aria-modal="true" aria-labelledby="global-preview-doc-name" onclick="if(event.target === this) closeGlobalPreview()" class="fixed inset-0 z-[50000] hidden bg-gray-900/90 backdrop-blur-md flex flex-col items-center justify-center p-2 md:p-8 transition-opacity opacity-0 duration-300">
            <div class="w-full max-w-6xl h-full max-h-full bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col transform scale-95 transition-all duration-300" id="global-doc-preview-container">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                    <div class="flex items-center gap-4 text-left">
                        <div id="global-preview-icon" class="w-10 h-10 rounded-xl flex items-center justify-center bg-red-50 text-[#cb2d2d]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 id="global-preview-doc-name" class="text-base font-black text-gray-900 truncate max-w-[200px] md:max-w-md">Document</h3>
                            <p id="global-preview-doc-info" class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Aperçu sécurisé</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 md:gap-3">
                        @if(App\Helpers\PermissionHelper::can('documents.delete'))
                        <button id="global-preview-delete-btn" onclick="deleteCurrentDocument()" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Supprimer le document">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        @endif
                        <a id="global-preview-download-btn" href="#" download class="bg-[#274256] text-white px-4 py-2 md:px-5 md:py-2.5 rounded-xl font-bold hover:bg-[#1a2e3d] transition shadow-lg text-[10px] md:text-[11px] uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span class="hidden sm:inline">Télécharger</span>
                        </a>
                        <button onclick="closeGlobalPreview()" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-[#cb2d2d] hover:bg-red-50 rounded-xl transition" aria-label="Fermer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 bg-gray-100 overflow-hidden flex items-center justify-center p-0 md:p-4">
                    <div id="global-preview-img-cont" class="hidden w-full h-full flex items-center justify-center p-4">
                        <img id="global-preview-img" src="" alt="Aperçu" class="max-w-full max-h-full shadow-2xl rounded-lg object-contain bg-white">
                    </div>
                    <div id="global-preview-frame-cont" class="hidden w-full h-full">
                        <iframe id="global-preview-frame" src="" class="w-full h-full border-0 bg-white" allow="autoplay"></iframe>
                    </div>
                    <div id="global-preview-unsupported" class="hidden text-center p-12">
                        <div class="w-24 h-24 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-lg font-black text-gray-900 mb-2">Aperçu non disponible</h4>
                        <p class="text-sm text-gray-500 mb-8 max-w-xs mx-auto">Le format de ce fichier ne permet pas une lecture directe dans le navigateur.</p>
                        <a href="#" id="global-preview-unsupported-dl" download class="inline-flex items-center gap-2 px-8 py-4 bg-[#cb2d2d] text-white font-black rounded-2xl shadow-xl hover:bg-[#a82020] transition-all text-xs uppercase tracking-widest">Télécharger le fichier</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                
                // Design ultra-net, haute visibilité et au premier plan absolu
                const baseClass = "pointer-events-auto flex items-center gap-4 px-6 py-4 rounded-2xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.4)] border-2 transition-all duration-500 ease-in-out transform translate-x-20 opacity-0";
                const typeClass = type === 'success' 
                    ? 'bg-[#1a2e3d] text-white border-emerald-500/50' 
                    : (type === 'error' ? 'bg-[#cb2d2d] text-white border-white/30' : 'bg-[#274256] text-white border-blue-400/30');

                toast.className = `${baseClass} ${typeClass}`;
                toast.style.backdropFilter = "none"; // Évite tout flou hérité
                
                const icon = type === 'success' ?
                    '<div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>' :
                    '<div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>';

                toast.innerHTML = `
                    ${icon}
                    <div class="font-black text-sm tracking-wide leading-tight uppercase">${message}</div>
                `;

                container.appendChild(toast);

                // Déclenchement de l'animation
                setTimeout(() => {
                    toast.classList.remove('translate-x-20', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                }, 10);

                // Fermeture automatique
                setTimeout(() => {
                    toast.classList.replace('translate-x-0', 'translate-x-full');
                    toast.classList.replace('opacity-100', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            };

            window.previewDoc = function(doc) {
                console.log('Opening preview for:', doc);
                if (!doc || !doc.url) {
                    showToast('Document invalide', 'error');
                    return;
                }

                window.currentPreviewDoc = doc; // Store globally for deletion
                const url = doc.url.toLowerCase();
                const nomOriginal = (doc.nom_original || '').toLowerCase();
                
                // Gestion du bouton supprimer
                const delBtn = document.getElementById('global-preview-delete-btn');
                if (delBtn) {
                    // Cacher le bouton si c'est une quittance ou un contrat généré (pas un document uploadé)
                    if (doc.id) {
                        delBtn.classList.remove('hidden');
                    } else {
                        delBtn.classList.add('hidden');
                    }
                }

                // Détection de l'extension
                let ext = (doc.extension || '').toLowerCase();
                
                if (!ext && nomOriginal.includes('.')) {
                    ext = nomOriginal.split('.').pop().toLowerCase();
                } else if (!ext) {
                    const cleanUrl = url.split('?')[0];
                    if (cleanUrl.includes('.')) {
                        ext = cleanUrl.split('.').pop().toLowerCase();
                    }
                }
                
                console.log('Detected extension:', ext);

                const modal = document.getElementById('global-doc-preview-modal');
                const container = document.getElementById('global-doc-preview-container');
                const imgCont = document.getElementById('global-preview-img-cont');
                const frameCont = document.getElementById('global-preview-frame-cont');
                const unsuppCont = document.getElementById('global-preview-unsupported');
                const img = document.getElementById('global-preview-img');
                const frame = document.getElementById('global-preview-frame');
                const dlBtn = document.getElementById('global-preview-download-btn');

                document.getElementById('global-preview-doc-name').innerText = doc.nom_original || 'Document';
                document.getElementById('global-preview-doc-info').innerText = (doc.type_label || 'Fichier') + (doc.created_at ? ' • ' + doc.created_at : '');
                dlBtn.href = doc.url;
                
                const unsuppDl = document.getElementById('global-preview-unsupported-dl');
                if (unsuppDl) unsuppDl.href = doc.url;

                // Reset visibility
                imgCont.classList.add('hidden');
                frameCont.classList.add('hidden');
                unsuppCont.classList.add('hidden');
                img.src = '';
                frame.src = '';

                // Classification du type
                const isImg = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'].includes(ext);
                const isPdf = ext === 'pdf' || 
                              url.includes('/quittance') || 
                              url.includes('/loyer') || 
                              url.includes('/contrat') || 
                              url.includes('/bilan');

                if (isImg) {
                    console.log('Treating as image');
                    imgCont.classList.remove('hidden');
                    
                    // Reset error handler
                    img.onload = function() {
                        console.log('Image loaded successfully');
                    };
                    
                    img.onerror = function() {
                        console.error('Image failed to load:', this.src);
                        imgCont.classList.add('hidden');
                        unsuppCont.classList.remove('hidden');
                        document.querySelector('#global-preview-unsupported h4').innerText = 'Erreur de chargement';
                        document.querySelector('#global-preview-unsupported p').innerText = 'Impossible d\'afficher l\'image. Le lien est peut-être expiré ou invalide.';
                    };
                    
                    img.src = doc.url;
                } else if (isPdf) {
                    console.log('Treating as PDF/iframe');
                    frameCont.classList.remove('hidden');
                    frame.src = doc.url;
                } else {
                    // Fallback: si c'est un lien sécurisé sans extension claire, on tente l'iframe (souvent PDF)
                    if (url.includes('/secure-access')) {
                        console.log('Fallback to iframe for secure-access');
                        frameCont.classList.remove('hidden');
                        frame.src = doc.url;
                    } else {
                        console.log('Unsupported format');
                        unsuppCont.classList.remove('hidden');
                    }
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    container.classList.remove('scale-95');
                    container.classList.add('scale-100');
                }, 10);
            };

            window.deleteCurrentDocument = async function() {
                const doc = window.currentPreviewDoc;
                if (!doc || !doc.id) return;

                if (!confirm('Voulez-vous vraiment supprimer ce document définitivement ?')) return;

                try {
                    const res = await fetch(`/documents/${doc.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        showToast('Document supprimé', 'success');
                        closeGlobalPreview();
                        
                        // Mise à jour locale immédiate pour la section locataire (si active)
                        if (window.locSection && window.locSection.currentLoc && window.locSection.currentLoc.documents) {
                            window.locSection.currentLoc.documents = window.locSection.currentLoc.documents.filter(d => d.id !== doc.id);
                            window.locSection.showDetails(window.locSection.currentLoc);
                        }
                        
                        // Rafraîchir les données globales en arrière-plan
                        if (window.dashboard) window.dashboard.refresh();
                    } else {
                        showToast(data.message || 'Erreur lors de la suppression', 'error');
                    }
                } catch (e) {
                    showToast('Erreur serveur', 'error');
                }
            };

            window.closeGlobalPreview = function() {
                const modal = document.getElementById('global-doc-preview-modal');
                const container = document.getElementById('global-doc-preview-container');

                modal.classList.add('opacity-0');
                container.classList.remove('scale-100');
                container.classList.add('scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.getElementById('global-preview-img').src = '';
                    document.getElementById('global-preview-frame').src = '';
                }, 300);
            };
            window.toggleSidebar = function() {
                const sidebar = document.getElementById('main-sidebar');
                const overlay = document.getElementById('sidebar-overlay');

                if (sidebar.classList.contains('-translate-x-full')) {
                    // Open
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.add('opacity-100'), 10);
                } else {
                    // Close
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            };

            // Gestion accessibilité/UX des modals (focus trap + retour focus)
            window.modalUX = (function() {
                const stack = [];
                const selector = 'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';

                function getFocusable(container) {
                    if (!container) return [];
                    return Array.from(container.querySelectorAll(selector)).filter(el => el.offsetParent !== null);
                }

                function onKeydown(event) {
                    if (event.key !== 'Tab') return;
                    const current = stack[stack.length - 1];
                    if (!current) return;

                    const focusable = getFocusable(current.container);
                    if (!focusable.length) return;

                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];

                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                }

                document.addEventListener('keydown', onKeydown);

                return {
                    activate: function(root, container) {
                        if (!root || !container) return;
                        const trigger = document.activeElement;
                        stack.push({ root, container, trigger });

                        requestAnimationFrame(() => {
                            const focusable = getFocusable(container);
                            (focusable[0] || container).focus?.();
                        });
                    },
                    deactivate: function(root) {
                        if (!root) return;
                        const idx = stack.map(s => s.root).lastIndexOf(root);
                        if (idx === -1) return;

                        const [entry] = stack.splice(idx, 1);
                        if (entry?.trigger && document.contains(entry.trigger)) {
                            requestAnimationFrame(() => entry.trigger.focus());
                        }
                    },
                };
            })();

            // Validation inline uniforme pour formulaires modaux
            // IMPORTANT: ne pas bloquer le flux des submit handlers métier existants
            document.addEventListener('submit', function(event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;
                const formId = form.getAttribute('id');
                if (!formId || !formId.endsWith('-main-form')) return;

                form.querySelectorAll('.field-invalid').forEach(el => el.classList.remove('field-invalid'));
                form.querySelectorAll('.form-error-message').forEach(el => el.remove());
            });

            document.addEventListener('invalid', function(event) {
                const field = event.target;
                if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
                    return;
                }

                const form = field.form;
                const formId = form ? form.getAttribute('id') : null;
                if (!formId || !formId.endsWith('-main-form')) return;

                field.classList.add('field-invalid');
                field.setAttribute('aria-invalid', 'true');

                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('form-error-message')) {
                    const hint = document.createElement('p');
                    hint.className = 'form-error-message';
                    hint.innerHTML = '<span aria-hidden="true">⚠️</span><span>' + (field.validationMessage || 'Champ invalide.') + '</span>';
                    field.insertAdjacentElement('afterend', hint);
                }
            }, true);

            document.addEventListener('input', function(event) {
                const field = event.target;
                if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
                    return;
                }

                if (field.checkValidity()) {
                    field.classList.remove('field-invalid');
                    if (field.nextElementSibling?.classList.contains('form-error-message')) {
                        field.nextElementSibling.remove();
                    }
                }
            });

            // Fermer les modaux avec la touche Échap
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    // Chercher tous les modaux ouverts (ceux qui n'ont pas la classe 'hidden')
                    // et appeler leur fonction de fermeture respective

                    // Global Preview
                    if (!document.getElementById('global-doc-preview-modal').classList.contains('hidden')) {
                        closeGlobalPreview();
                    }

                    // Biens
                    if (window.bienSection && !document.getElementById('bien-modal-wrapper').classList.contains('hidden')) {
                        bienSection.closeModal();
                    }
                    if (window.bienSection && !document.getElementById('bien-delete-modal').classList.contains('hidden')) {
                        bienSection.closeDeleteModal();
                    }

                    // Locataires
                    if (window.locSection) {
                        if (!document.getElementById('loc-modal-wrapper').classList.contains('hidden')) locSection.closeModal();
                        if (!document.getElementById('loc-delete-modal').classList.contains('hidden')) locSection.closeDeleteModal();
                        if (!document.getElementById('loc-doc-modal').classList.contains('hidden')) locSection.closeDocumentModal();
                        if (!document.getElementById('loc-doc-delete-modal').classList.contains('hidden')) locSection.closeDocDeleteModal();
                    }

                    // Loyers / Paiements
                    if (window.loySection) {
                        if (!document.getElementById('loy-payment-modal').classList.contains('hidden')) loySection.closePaymentModal();
                        if (!document.getElementById('loy-edit-modal').classList.contains('hidden')) loySection.closeEditModal();
                    }

                    if (window.paiSection) {
                        if (!document.getElementById('pai-modal-overlay').classList.contains('hidden')) paiSection.closeModal();
                        if (!document.getElementById('pai-delete-modal').classList.contains('hidden')) paiSection.closeDeleteModal();
                    }

                    // Dépenses
                    if (window.depSection) {
                        if (!document.getElementById('dep-modal-wrapper').classList.contains('hidden')) depSection.closeModal();
                        if (!document.getElementById('dep-delete-modal').classList.contains('hidden')) depSection.closeDeleteModal();
                    }

                    // Contrats
                    if (window.conSection) {
                        if (!document.getElementById('con-modal-wrapper').classList.contains('hidden')) conSection.closeModal();
                        if (!document.getElementById('con-delete-modal').classList.contains('hidden')) conSection.closeDeleteModal();
                    }

                    // Propriétaires / Agence
                    if (window.propSection) {
                        if (!document.getElementById('prop-modal-wrapper').classList.contains('hidden')) propSection.closeModal();
                    }

                    // Utilisateurs
                    if (window.userSection) {
                        if (!document.getElementById('user-modal-wrapper').classList.contains('hidden')) userSection.closeModal();
                        if (!document.getElementById('user-delete-modal').classList.contains('hidden')) userSection.closeDeleteModal();
                    }
                }
            });
        </script>
        @stack('modals')
        <!-- Toast Container (Always on top) -->
        <div id="toast-container" class="fixed top-6 right-6 z-[999999] flex flex-col gap-3 pointer-events-none"></div>
    </body>
</html>
