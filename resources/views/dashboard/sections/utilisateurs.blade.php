<script>
    window.userSection = {
        deleteTargetId: null,

        openModal: function(mode, user = null) {
            const wrapper = document.getElementById('user-modal-wrapper');
            const overlay = document.getElementById('user-modal-overlay');
            const container = document.getElementById('user-modal-container');
            const form = document.getElementById('user-main-form');
            const title = document.getElementById('user-modal-title');
            const btn = document.getElementById('user-submit-btn');

            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay?.classList.remove('opacity-0');
                container?.classList.remove('opacity-0', 'scale-95');
                container?.classList.add('opacity-100', 'scale-100');
            }, 10);

            if (form) form.reset();
            document.getElementById('user-input-id').value = '';
            document.getElementById('user-pwd-hint').classList.add('hidden');

            if(mode === 'edit' && user) {
                title.innerText = 'Modifier le Membre';
                if (btn) btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mettre à jour';
                document.getElementById('user-input-id').value = user.id;
                document.getElementById('user-input-name').value = user.name;
                document.getElementById('user-input-email').value = user.email;
                document.getElementById('user-input-role').value = user.role;
                document.getElementById('user-pwd-hint').classList.remove('hidden');
            } else {
                title.innerText = 'Nouveau Membre';
                if (btn) btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('user-modal-wrapper');
            const overlay = document.getElementById('user-modal-overlay');
            const container = document.getElementById('user-modal-container');

            if (!wrapper) return;
            overlay?.classList.add('opacity-0');
            container?.classList.remove('opacity-100', 'scale-100');
            container?.classList.add('opacity-0', 'scale-95');
            window.modalUX?.deactivate(wrapper);

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        submitForm: async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('user-submit-btn');
            if (!btn || btn.disabled) return;

            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            btn.disabled = true;

            const formData = new FormData(form);
            const id = document.getElementById('user-input-id').value;
            const url = id ? `/users/${id}` : '{{ route('users.store') }}';

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
                    showToast('Profil mis à jour', 'success');
                    this.closeModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur lors de la mise à jour', 'error');
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
            const modal = document.getElementById('user-delete-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('user-delete-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            setTimeout(() => { 
                modal.classList.add('hidden');
                this.deleteTargetId = null;
            }, 300);
        },

        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('user-confirm-delete-btn');
            if (!btn) return;
            const originalText = btn.innerHTML;
            btn.innerText = 'Suppression...';
            btn.disabled = true;

            try {
                const response = await fetch(`/users/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if(data.success) {
                    showToast('Membre supprimé', 'success');
                    this.closeDeleteModal();
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
        }
    };
</script>
