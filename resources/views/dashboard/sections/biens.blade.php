<script>
    window.bienSection = {
        deleteTargetId: null,

        openModal: function(mode, bien = null) {
            const wrapper = document.getElementById('bien-modal-wrapper');
            const overlay = document.getElementById('bien-modal-overlay');
            const container = document.getElementById('bien-modal-container');
            const form = document.getElementById('bien-main-form');
            const title = document.getElementById('bien-modal-title');

            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay?.classList.remove('opacity-0');
                container?.classList.remove('scale-95', 'opacity-0');
            }, 10);

            if (form) form.reset();
            document.getElementById('bien-input-id').value = '';
            const display = document.getElementById('file-name-display');
            if (display) display.textContent = '';

            if(mode === 'edit' && bien) {
                title.innerText = 'Modifier le Bien';
                document.getElementById('bien-input-id').value = bien.id;
                document.getElementById('bien-input-nom').value = bien.nom;
                document.getElementById('bien-input-type').value = bien.type;
                document.getElementById('bien-input-loyer').value = bien.loyer_mensuel;
                document.getElementById('bien-input-adresse').value = bien.adresse || '';
                document.getElementById('bien-input-pieces').value = bien.nombre_pieces || '';
                document.getElementById('bien-input-meuble').checked = !!bien.meuble;
            } else {
                title.innerText = 'Nouveau Bien';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('bien-modal-wrapper');
            const overlay = document.getElementById('bien-modal-overlay');
            const container = document.getElementById('bien-modal-container');

            if (!wrapper) return;
            overlay?.classList.add('opacity-0');
            container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        submitForm: async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('bien-submit-btn');
            if (!btn || btn.disabled) return;

            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement...';
            btn.disabled = true;

            const formData = new FormData(form);
            const id = document.getElementById('bien-input-id').value;
            const url = id ? `/dashboard/biens/${id}` : `{{ route('dashboard.biens.store') }}`;
            
            if (id) {
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if(response.ok && data.success) {
                    showToast(data.message || 'Succès', 'success');
                    this.closeModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur de validation', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch(e) {
                console.error(e);
                showToast('Erreur serveur', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        },

        confirmDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('bien-delete-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('bien-delete-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            setTimeout(() => { 
                modal.classList.add('hidden');
                this.deleteTargetId = null;
            }, 300);
        },

        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('bien-confirm-delete-btn');
            const originalText = btn.innerText;
            btn.innerText = 'Suppression...';
            btn.disabled = true;

            try {
                const response = await fetch(`/dashboard/biens/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if(data.success) {
                    showToast('Bien supprimé avec succès', 'success');
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur lors de la suppression', 'error');
                }
            } catch(e) {
                showToast('Erreur serveur', 'error');
            } finally {
                 btn.innerText = originalText;
                 btn.disabled = false;
                 this.closeDeleteModal();
            }
        }
    };

    // File Input UX (Using delegation for SPA)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'bien-input-images') {
            const display = document.getElementById('file-name-display');
            if (!display) return;
            const files = e.target.files;
            if(files && files.length > 0) {
                display.innerText = files.length > 1
                    ? files.length + ' photos sélectionnées'
                    : files[0].name;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        }
    });
</script>
