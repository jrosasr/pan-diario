<div x-data="{
    scanner: null,
    isScanning: false,
    lastScanned: null,
    workdayId: {{ $workdayId }},
    showConfirmationModal: false,
    beneficiaryInfo: null,
    restartingScanner: false,
    cameraReady: true,
    beneficiaryPhoto: null,
    isActive: true,

    async cleanUpAndRestart() {
        this.showConfirmationModal = false;
        this.beneficiaryInfo = null;
        this.beneficiaryPhoto = null;
        this.lastScanned = null;
        this.cameraReady = false;
        
        await this.stopScan();
        
        const qrReader = document.getElementById('qr-reader');
        if (qrReader) qrReader.innerHTML = '';
        
        await new Promise(resolve => setTimeout(resolve, 300));
        
        this.scanner = null;
        this.cameraReady = true;
    },

    initScanner() {
        if (this.scanner) return;
        this.scanner = new Html5QrcodeScanner('qr-reader', {
            qrbox: 250,
            fps: 10,
            rememberLastUsedCamera: true
        });
    },

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

    async stopScan() {
        if (!this.scanner || !this.isScanning) return;
        
        try {
            await this.scanner.clear();
            this.isScanning = false;
        } catch (error) {
            console.error('Error stopping scanner:', error);
        }
    },

    async handleScan(decodedText) {
            if (this.lastScanned === decodedText) return;
            this.lastScanned = decodedText;
            
            try {
                const data = JSON.parse(decodedText);
                if (data.id && data.name && data.dni) {
                    // Hacer petición para obtener datos completos del beneficiario
                    this.$wire.call('getBeneficiaryInfo', data.id).then((response) => {
                        this.stopScan().then(() => {
                            this.beneficiaryInfo = {
                                ...data,
                                ...response, // Agregar los datos adicionales del backend
                                photoUrl: response.photo ? '/storage/' + response.photo : null,
                                isActive: response.active // Asumiento que 'active' es el campo booleano
                            };
                            this.showConfirmationModal = true;
                        });
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
        this.$wire.dispatch('notify');
        setTimeout(() => this.startScan(), 500);
    },

    shouldShowStartButton() {
        return !this.isScanning && this.cameraReady;
    },
}"
    x-on:notify.window="startScan()"
    x-on:reset-scanner.window="cleanUpAndRestart()">
    <div class="flex flex-col items-center space-y-4">
        <div id="qr-reader" class="w-full max-w-md"></div>
        <div class="flex space-x-4">
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
                            <div class="flex flex-col items-center mr-4">
                                <!-- Foto del beneficiario -->
                                <template x-if="beneficiaryInfo.photoUrl">
                                    <img x-bind:src="beneficiaryInfo.photoUrl" 
                                         class="h-8 w-8 rounded-full object-cover mb-2" 
                                         alt="Foto del beneficiario" style='width: 200px; height:200px;'>
                                </template>
                                <template x-if="!beneficiaryInfo.photoUrl">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mb-2" style='width: 200px; height:200px;'>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style='width: 200px; height:200px;'>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </template>
                                <!-- Estado -->
                                <span x-text="beneficiaryInfo.isActive ? 'Activo' : 'Suspendido'" 
                                      class="px-2 py-1 text-xs font-semibold rounded-full"
                                      x-bind:class="beneficiaryInfo.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                </span>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    Confirmar Asistencia
                                </h3>
                                <div class="mt-2">
                                    <template x-if="!beneficiaryInfo.isActive">
                                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-red-700">
                                                        Este beneficiario está suspendido y no puede marcar asistencia.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
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
                        <template x-if="beneficiaryInfo.isActive">
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-500 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                                x-on:click="async () => {
                                    await $wire.call('confirmAttendance', beneficiaryInfo.id);
                                    await cleanUpAndRestart();
                                }">
                                Aceptar
                            </button>
                        </template>
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