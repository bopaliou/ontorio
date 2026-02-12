<script>
    window.locSection = {
        deleteTargetId: null,

        openModal: function(mode, loc = null) {
            const wrapper = document.getElementById('loc-modal-wrapper');
            const overlay = document.getElementById('loc-modal-overlay');
            const container = document.getElementById('loc-modal-container');
            const form = document.getElementById('loc-main-form');
            const title = document.getElementById('loc-modal-title');

            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay?.classList.remove('opacity-0');
                container?.classList.remove('scale-95', 'opacity-0');
            }, 10);

            if (form) form.reset();
            document.getElementById('loc-input-id').value = '';

            if(mode === 'edit' && loc) {
                title.innerText = 'Modifier le Locataire';
                document.getElementById('loc-input-id').value = loc.id;
                document.getElementById('loc-input-nom').value = loc.nom;
                document.getElementById('loc-input-email').value = loc.email || '';
                document.getElementById('loc-input-tel').value = loc.telephone || '';
                document.getElementById('loc-input-cni').value = loc.cni || '';
                document.getElementById('loc-input-adresse').value = loc.adresse || '';
            } else {
                title.innerText = 'Nouveau Locataire';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('loc-modal-wrapper');
            const overlay = document.getElementById('loc-modal-overlay');
            const container = document.getElementById('loc-modal-container');

            if (!wrapper) return;
            overlay?.classList.add('opacity-0');
            container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        submitForm: async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('loc-submit-btn');
            if (!btn || btn.disabled) return;

            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Patientez...';
            btn.disabled = true;

            const formData = new FormData(form);
            const id = document.getElementById('loc-input-id').value;
            const url = id ? `/locataires/${id}` : '{{ route('locataires.store') }}';

            try {
                const response = await fetch(url, {
                    method: id ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                const data = await response.json();

                if(response.ok) {
                    showToast('Locataire enregistré', 'success');
                    this.closeModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur lors de l\'enregistrement', 'error');
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

        openDocumentModal: function(id) {
            this.currentLocId = id;
            document.getElementById('doc-locataire-id').value = id;
            const modal = document.getElementById('loc-doc-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);
        },

        closeDocumentModal: function() {
            const modal = document.getElementById('loc-doc-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        },

        submitDocForm: async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            if (!btn || btn.disabled) return;

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Envoi...';

            const formData = new FormData(form);
            const url = `/locataires/${this.currentLocId}/documents`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });

                const data = await response.json();

                if(response.ok) {
                    showToast('Document ajouté', 'success');
                    this.closeDocumentModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch(e) {
                showToast('Erreur serveur', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        },

        confirmDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('loc-delete-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('loc-delete-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            setTimeout(() => { 
                modal.classList.add('hidden');
                this.deleteTargetId = null;
            }, 300);
        },

        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('loc-confirm-delete-btn');
            if (!btn) return;
            const originalText = btn.innerText;
            btn.innerText = 'Suppression...';
            btn.disabled = true;

            try {
                const response = await fetch(`/locataires/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if(data.success) {
                    showToast('Locataire supprimé', 'success');
                    this.closeDeleteModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur', 'error');
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            } catch(e) {
                showToast('Erreur serveur', 'error');
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
    };
</script>
