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
    <body class="font-sans antialiased bg-gray-50">
        <!-- Skip to content -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[200] focus:bg-white focus:text-[#cb2d2d] focus:px-4 focus:py-2 focus:rounded-xl focus:shadow-2xl focus:font-bold focus:outline-none focus:ring-2 focus:ring-[#cb2d2d]">
            Passer au contenu principal
        </a>

        <!-- Barre de chargement globale -->
        <div id="page-loader-bar"></div>

        <div class="min-h-screen flex">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col lg:pl-72 transition-all duration-300">
                <!-- Topbar -->
                @include('layouts.topbar')

                <!-- Main Content -->
                <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-24 right-6 z-[100] flex flex-col gap-3 pointer-events-none"></div>

        <!-- Document Preview Modal Globale -->
        <div id="global-doc-preview-modal" role="dialog" aria-modal="true" aria-labelledby="global-preview-doc-name" onclick="if(event.target === this) closeGlobalPreview()" class="fixed inset-0 z-[150] hidden bg-gray-900/80 backdrop-blur-md flex flex-col items-center justify-center p-4 transition-opacity opacity-0 duration-300">
            <div class="w-full max-w-5xl h-[90vh] bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col transform scale-95 transition-all duration-300" id="global-doc-preview-container">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                    <div class="flex items-center gap-4">
                        <div id="global-preview-icon" class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-50 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h3 id="global-preview-doc-name" class="text-base font-bold text-gray-900 truncate max-w-md">Document</h3>
                            <p id="global-preview-doc-info" class="text-[10px] text-gray-500 font-medium uppercase tracking-widest">Aperçu du document</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a id="global-preview-download-btn" href="#" download class="bg-[#cb2d2d] text-white px-5 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Télécharger
                        </a>
                        <button onclick="closeGlobalPreview()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition" aria-label="Fermer la prévisualisation">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 bg-gray-50 overflow-auto flex items-center justify-center p-4">
                    <div id="global-preview-img-cont" class="hidden h-full">
                        <img id="global-preview-img" src="" alt="Aperçu" class="max-w-full max-h-full shadow-lg rounded-lg object-contain">
                    </div>
                    <div id="global-preview-frame-cont" class="hidden w-full h-full">
                        <iframe id="global-preview-frame" src="" class="w-full h-full border-0 rounded-lg shadow-sm"></iframe>
                    </div>
                    <div id="global-preview-unsupported" class="hidden text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900 mb-2">Aperçu non disponible</p>
                        <p class="text-xs text-gray-500 mb-6">Veuillez télécharger le document pour le consulter.</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `pointer-events-auto flex items-center gap-3 px-6 py-4 rounded-2xl shadow-xl transform transition-all duration-300 translate-x-10 opacity-0 ${
                    type === 'success' ? 'bg-[#274256] text-white' :
                    type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
                }`;

                const icon = type === 'success' ?
                    '<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :
                    (type === 'error' ? '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' : '');

                toast.innerHTML = `
                    ${icon}
                    <div class="font-bold text-sm tracking-wide">${message}</div>
                `;

                container.appendChild(toast);

                // Animation Entrée
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-10', 'opacity-0');
                });

                // Auto Close
                setTimeout(() => {
                    toast.classList.add('translate-x-10', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            };

            window.previewDoc = function(doc) {
                if (!doc || !doc.url) {
                    showToast('Document invalide', 'error');
                    return;
                }

                const url = doc.url.toLowerCase();
                const ext = doc.nom_original ? doc.nom_original.split('.').pop().toLowerCase() : url.split('.').pop().split('?')[0];

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

                // Reset visibility
                imgCont.classList.add('hidden');
                frameCont.classList.add('hidden');
                unsuppCont.classList.add('hidden');
                img.src = '';
                frame.src = '';

                // Detect if it's an image or PDF
                const isImg = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
                const isPdf = ext === 'pdf' || url.includes('/quittance') || url.includes('/loyer') || url.includes('/contrat');

                if (isImg) {
                    imgCont.classList.remove('hidden');
                    img.src = doc.url;
                } else if (isPdf) {
                    frameCont.classList.remove('hidden');
                    frame.src = doc.url;
                } else {
                    unsuppCont.classList.remove('hidden');
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    container.classList.remove('scale-95');
                    container.classList.add('scale-100');
                }, 10);
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
                if (!form.id || !form.id.endsWith('-main-form')) return;

                form.querySelectorAll('.field-invalid').forEach(el => el.classList.remove('field-invalid'));
                form.querySelectorAll('.form-error-message').forEach(el => el.remove());
            });

            document.addEventListener('invalid', function(event) {
                const field = event.target;
                if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
                    return;
                }

                const form = field.form;
                if (!form || !form.id || !form.id.endsWith('-main-form')) return;

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
    </body>
</html>
