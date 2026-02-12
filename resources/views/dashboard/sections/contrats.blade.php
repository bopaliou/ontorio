<script>
    window.conSection = {
        deleteTargetId: null,

        openModal: function(mode, data = null) {
            const wrapper = document.getElementById('con-modal-wrapper');
            const overlay = document.getElementById('con-modal-overlay');
            const container = document.getElementById('con-modal-container');
            const form = document.getElementById('con-main-form');
            const btn = document.getElementById('con-submit-btn');
            const title = document.getElementById('con-modal-title');

            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay?.classList.remove('opacity-0');
                container?.classList.remove('scale-95', 'opacity-0');
            }, 10);

            if (form) form.reset();
            document.getElementById('con-input-id').value = '';

            if(mode === 'edit' && data) {
                title.innerText = 'Modifier le Contrat';
                if (btn) btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mettre à jour';
                document.getElementById('con-input-id').value = data.id;
                document.getElementById('con-input-bien').value = data.bien_id;
                document.getElementById('con-input-locataire').value = data.locataire_id;
                document.getElementById('con-input-loyer').value = data.loyer_montant;
                document.getElementById('con-input-date').value = data.date_debut;
                document.getElementById('con-input-date-fin').value = data.date_fin || '';
                document.getElementById('con-input-type-bail').value = data.type_bail || 'habitation';
                document.getElementById('con-input-caution').value = data.caution || '';
                document.getElementById('con-input-frais').value = data.frais_dossier || '';
            } else {
                title.innerText = 'Nouveau Contrat';
                if (btn) btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Valider';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('con-modal-wrapper');
            const overlay = document.getElementById('con-modal-overlay');
            const container = document.getElementById('con-modal-container');

            if (!wrapper) return;
            overlay?.classList.add('opacity-0');
            container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        submitForm: async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('con-submit-btn');
            if (!btn || btn.disabled) return;

            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Patientez...';
            btn.disabled = true;

            const formData = new FormData(form);
            const id = document.getElementById('con-input-id').value;
            const url = id ? `/contrats/${id}` : '{{ route('contrats.store') }}';

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
                    showToast('Contrat enregistré', 'success');
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

        requestDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('con-delete-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('con-delete-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            setTimeout(() => { 
                modal.classList.add('hidden');
                this.deleteTargetId = null;
            }, 300);
        },

        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('con-confirm-delete-btn');
            if (!btn) return;
            const originalText = btn.innerText;
            btn.innerText = 'Résiliation...';
            btn.disabled = true;

            try {
                const response = await fetch(`/contrats/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if(data.success) {
                    showToast('Bail résilié avec succès', 'success');
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

    // Auto-fill rent when property is selected
    document.getElementById('con-input-bien')?.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const rent = option.dataset.loyer;
        if(rent) {
            document.getElementById('con-input-loyer').value = rent;
            document.getElementById('con-input-caution').value = rent * 2;
        }
    });
</script>
