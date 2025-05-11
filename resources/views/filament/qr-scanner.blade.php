<div x-data="{
    scanner: null,
    isScanning: false,
    lastScanned: null,
    workdayId: {{ $workdayId }},
    showConfirmationModal: false,
    beneficiaryInfo: null,
    restartingScanner: false, // Nueva bandera para controlar el reinicio
    cameraReady: true, // Nuevo estado para controlar la disponibilidad de la cámara

    // Reinicio optimizado
    async cleanUpAndRestart() {
        // Ocultar modal y limpiar datos
        this.showConfirmationModal = false;
        this.beneficiaryInfo = null;
        this.lastScanned = null;
        this.cameraReady = false; // Bloquear temporalmente
        
        // Detener scanner existente
        await this.stopScan();
        
        // Limpiar completamente el contenedor
        const qrReader = document.getElementById('qr-reader');
        if (qrReader) qrReader.innerHTML = '';
        
        // Pequeña pausa para liberar recursos
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Restablecer estados y permitir nuevo escaneo
        this.scanner = null;
        this.cameraReady = true;
    },

    // Inicializar scanner
    initScanner() {
        if (this.scanner) return;
        this.scanner = new Html5QrcodeScanner('qr-reader', {
            qrbox: 250,
            fps: 10,
            rememberLastUsedCamera: true
        });
    },

    // Iniciar escaneo
    async startScan() {
        if (this.isScanning || !this.cameraReady) return;
        
        try {
            this.initScanner();
            await this.scanner.render(
                decodedText => this.handleScan(decodedText),
                error => {
                    console.error(error);
                    this.cameraReady = false;
                }
            );
            this.isScanning = true;
        } catch (error) {
            console.error('Error starting scanner:', error);
            this.cameraReady = false;
        }
    },

    // Detener escaneo
    async stopScan() {
        if (!this.scanner || !this.isScanning) return;
        
        try {
            await this.scanner.clear();
            this.isScanning = false;
        } catch (error) {
            console.error('Error stopping scanner:', error);
        }
    },

    // Manejar código escaneado
    handleScan(decodedText) {
        if (this.lastScanned === decodedText) return;
        this.lastScanned = decodedText;
        
        try {
            const data = JSON.parse(decodedText);
            if (data.id && data.name && data.dni) {
                this.stopScan().then(() => {
                    this.beneficiaryInfo = data;
                    this.showConfirmationModal = true;
                });
            }
        } catch (e) {
            console.error('Error parsing QR:', e);
            this.lastScanned = null;
        }
    },

    async confirmAttendance() {
        await this.$wire.call('confirmAttendance', this.beneficiaryInfo.id);
        this.showConfirmationModal = false;
        this.beneficiaryInfo = null;
        this.cleanUpAndRestart();
        setTimeout(() => this.startScan(), 500);
    },


    cancelAttendance() {
        this.showConfirmationModal = false;
        this.beneficiaryInfo = null;
        this.$wire.dispatch('notify'); // Notificar a Livewire para reiniciar
        setTimeout(() => this.startScan(), 500);
    },

    // Mostrar botón de inicio solo cuando sea apropiado
    shouldShowStartButton() {
        return !this.isScanning && this.cameraReady;
    },
}"
    x-on:notify.window="startScan()"
    x-on:reset-scanner.window="cleanUpAndRestart()">
    <div class="flex flex-col items-center space-y-4">
        <div id="qr-reader" class="w-full max-w-md"></div>
        <div class="flex space-x-4">
            <!-- Botón de inicio solo visible cuando sea apropiado -->
            <button x-on:click="startScan" x-show="!isScanning"
                class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">
                Iniciar Escaneo
            </button>

            <button x-on:click="stopScan" x-show="isScanning"
                class="px-4 py-2 bg-danger-500 text-white rounded hover:bg-danger-600">
                Detener Escaneo
            </button>
        </div>
    </div>

    <template x-if="showConfirmationModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-700 sm:mx-0 sm:h-10 sm:w-10">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    Confirmar Asistencia
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ¿Registrar la asistencia de este beneficiario?
                                    </p>
                                    <p class="mt-4 text-base text-gray-700 dark:text-gray-200">
                                        <strong>Nombre:</strong> <span x-text="beneficiaryInfo.name"></span>
                                    </p>
                                    <p class="mt-2 text-base text-gray-700 dark:text-gray-200">
                                        <strong>DNI:</strong> <span x-text="beneficiaryInfo.dni"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                            x-on:click="async () => {
                                await $wire.call('confirmAttendance', beneficiaryInfo.id);
                                await cleanUpAndRestart();
                            }">
                            Aceptar
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            x-on:click="cancelAttendance()">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>


    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    @endpush
</div>