<script>
function paymentSettings() {
    return {
        // Ahora usamos $methods en lugar de $paymentConfigs
        methods: @json($methods),
        selectedIdx: null,
        showAll: false,
        openAddModal: false,
        toast: { show: false, msg: '', error: false },

        confirmDeleteIdx: null, // Modal: índice de variante a eliminar
        showDeleteModal: false, // Modal: visibilidad

        baseConfigs: {
            tran_bancaria_nacional: {
                label: 'Transferencia Nacional',
                icon: "<i class='fas fa-university text-indigo-500'></i>"
            },
            pago_efectivo: {
                label: 'Pago Efectivo',
                icon: "<i class='fas fa-money-bill-wave text-green-600'></i>"
            },
            pago_movil: {
                label: 'Pago Móvil',
                icon: "<i class='fas fa-mobile-alt text-orange-500'></i>"
            },
            tran_bancaria_internacional: {
                label: 'Transferencia Internacional',
                icon: "<i class='fas fa-globe text-indigo-400'></i>"
            },
            zelle: {
    label: 'Zelle',
    // Utiliza una etiqueta <img> para el ícono SVG
    icon: "<img src='/images/zelle.svg' class='w-8 h-8 inline-block' alt='Zelle' />"
},

        },
        methodDesc: {
            tran_bancaria_nacional: "Para transferencias en bancos nacionales.",
            pago_efectivo: "Para cobros en efectivo en taquilla o punto físico.",
            pago_movil: "Para pagos vía Pago Móvil nacional.",
            tran_bancaria_internacional: "Para transferencias internacionales (IBAN/SWIFT).",
            zelle: "Para pagos a través de Zelle (email/titular)."
        },
        get uniqueKeys() {
            return Object.keys(this.baseConfigs);
        },
        get selectedMethod() {
            return this.selectedIdx !== null ? this.methods[this.selectedIdx] : null;
        },
        selectMethod(idx) {
            this.selectedIdx = idx;
        },
        addNewMethod(type) {
            if (!this.baseConfigs[type]) {
                this.showToast('Tipo de método no válido.', true);
                return;
            }
            let count = this.methods.filter(m => m.key === type).length + 1;
            let base = this.baseConfigs[type];
            let conf = {
                id: null,
                key: type,
                name: base.label,
                alias: count > 1 ? `Variante ${count}` : '',
                enabled: true,
                icon: base.icon,
                desc: this.methodDesc[type] || '',
                fields: [],
            };
            let template = this.methods.find(m => m.key === type);
            if (template) {
                conf.fields = template.fields.map(f => ({
                    ...f, value: ''
                }));
            }
            conf._key = 'tmp_' + Math.random().toString(36).substring(2,10);
            this.methods.push(conf);
            this.selectedIdx = this.methods.length - 1;
            this.showAll = false;
            this.openAddModal = false;
        },
        duplicateMethod(type) {
            this.addNewMethod(type);
        },
        // Lanzar modal para confirmar eliminación
        askDeleteVariant(idx) {
            this.confirmDeleteIdx = idx;
            this.showDeleteModal = true;
        },
        // Confirmar eliminación real
        confirmDeleteVariant() {
            let idx = this.confirmDeleteIdx;
            if (idx === null) return;
            const method = this.methods[idx];
            if (!method.id) {
                this.methods.splice(idx, 1);
                this.selectedIdx = null;
                this.showDeleteModal = false;
                this.showToast('Variante eliminada correctamente.', false);
                return;
            }
            fetch(`/admin/settings/payments/delete/${method.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(async res => {
                if (res.ok) {
                    this.showToast("Variante eliminada correctamente.", false);
                    this.methods.splice(idx, 1);
                    this.selectedIdx = null;
                    this.showDeleteModal = false;
                } else {
                    let json = {};
                    try { json = await res.json(); } catch {}
                    this.showToast(json.message ?? "Error al eliminar la variante.", true);
                    this.showDeleteModal = false;
                }
            })
            .catch(() => {
                this.showToast("Error de conexión al eliminar.", true);
                this.showDeleteModal = false;
            });
        },
        // Cancelar modal
        cancelDeleteVariant() {
            this.confirmDeleteIdx = null;
            this.showDeleteModal = false;
        },
        /*** Guardado instantáneo de activación/desactivación ***/
        toggleEnabled(idx) {
            let method = this.methods[idx];
            let currentValue = method.enabled;
            fetch("{{ route('admin.settings.payments.save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    methods: this.methods.map(m => ({
                        id: m.id,
                        key: m.key,
                        name: m.name,
                        alias: m.alias,
                        enabled: m.enabled ? 1 : 0,
                        details: Object.fromEntries((m.fields || []).map(f => [f.key, f.value || '']))
                    }))
                })
            })
            .then(async res => {
                // Siempre resetea antes de mostrar el toast (para Alpine)
                this.toast.show = false;
                let msg = '';
                if (res.ok) {
                    msg = currentValue
                        ? `Método de pago "${method.name}${method.alias ? ' ('+method.alias+')' : ''}" activado exitosamente.`
                        : `Método de pago "${method.name}${method.alias ? ' ('+method.alias+')' : ''}" desactivado exitosamente.`;
                    setTimeout(() => this.showToast(msg, false), 50);
                } else {
                    msg = 'Error al actualizar el método de pago.';
                    setTimeout(() => this.showToast(msg, true), 50);
                    method.enabled = !currentValue;
                }
            })
            .catch(e => {
                this.toast.show = false;
                setTimeout(() => this.showToast('Error al actualizar el método de pago.', true), 50);
                method.enabled = !currentValue;
            });
        },
        /*** Toast profesional ***/
        showToast(msg, error = false) {
            this.toast.msg = msg;
            this.toast.error = error;
            this.toast.show = false;
            setTimeout(() => {
                this.toast.show = true;
                setTimeout(() => this.toast.show = false, 3000);
            }, 10);
        }
    }
}
</script>
