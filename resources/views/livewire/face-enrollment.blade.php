<div x-data="{
    stream: null,
    isCameraActive: false,
    status: 'idle', // idle, detecting, success, error
    message: '',
    
    async init() {
        if (!window.faceapi || !faceapi.nets.ssdMobilenetv1.params) {
            await this.loadModels();
        } else {
            this.message = 'Ready. Start camera to enroll.';
        }
    },

    async loadModels() {
        this.message = 'Loading models... (approx. 6MB)';
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            this.message = 'Ready. Start camera to enroll.';
        } catch (err) {
            console.error(err);
            this.message = 'Error loading models. Check connection.';
            this.status = 'error';
        }
    },

    async startCamera() {
        if (this.stream) return;
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            const video = this.$refs.enrollVideo;
            video.srcObject = this.stream;
            video.play();
            this.isCameraActive = true;
            this.message = 'Position face in view...';
        } catch (err) {
            this.message = 'Camera access denied.';
            this.status = 'error';
        }
    },

    async captureAndEnroll() {
        if (!this.isCameraActive) return;
        
        this.status = 'detecting';
        this.message = 'Processing...';
        
        const video = this.$refs.enrollVideo;
        // Detect single face
        const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
        
        if (detection) {
            this.message = 'Face detected! Saving...';
            
            // Send to Livewire backend
            // Note: descriptor is Float32Array, convert to normal array for JSON
            const descriptorArray = Array.from(detection.descriptor);
            @this.saveFaceDescriptor(JSON.stringify(descriptorArray));
            
            this.status = 'success';
            this.message = 'Face enrolled successfully!';
            this.stopCamera();
        } else {
            this.status = 'error';
            this.message = 'No face detected. Ensure good lighting and look at camera.';
            setTimeout(() => { if(this.status === 'error') this.status = 'idle'; this.message = 'Ready. Try again.'; }, 3000);
        }
    },

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        this.isCameraActive = false;
    }
}"
x-init="init"
class="space-y-4">

    <!-- Load Script locally if not present -->
    <script>
        if (typeof faceapi === 'undefined') {
            const script = document.createElement('script');
            script.src = "{{ asset('js/face-api.min.js') }}";
            document.head.appendChild(script);
        }
    </script>
    
    @if($enrolled)
        <div class="bg-green-50 p-4 rounded-lg border border-green-200 flex items-center gap-3 mb-4">
            <div class="bg-green-100 p-2 rounded-full text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-green-800">Enrolled</h3>
                <p class="text-sm text-green-700">User Face ID is active. Can be updated by re-enrolling.</p>
            </div>
        </div>
    @endif

    <div class="relative bg-gray-900 rounded-lg overflow-hidden aspect-video flex items-center justify-center border-2 border-dashed border-gray-600">
        <video x-ref="enrollVideo" class="w-full h-full object-cover" muted></video>
        
        <div x-show="!isCameraActive && status !== 'success'" class="absolute inset-0 flex items-center justify-center bg-gray-800/80 text-white">
            <p x-text="message" class="font-medium"></p>
        </div>

        <div x-show="status === 'success'" class="absolute inset-0 flex flex-col items-center justify-center bg-green-900/90 text-white">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-xl font-bold">Success!</p>
        </div>
        
        <!-- Loading Overlay -->
        <div x-show="status === 'detecting' || message.includes('Loading')" class="absolute inset-0 flex items-center justify-center bg-black/50 text-white z-10">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-white mr-3"></div>
            <span x-text="message"></span>
        </div>
    </div>

    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-sm font-medium" :class="{
            'text-gray-600': status === 'idle',
            'text-blue-600': status === 'detecting',
            'text-green-600': status === 'success',
            'text-red-600': status === 'error'
        }" x-text="message"></p>

        <div class="flex gap-2">
            <button x-show="!isCameraActive && status !== 'success'" @click="startCamera" type="button" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Start Camera
            </button>
            <button x-show="isCameraActive" @click="captureAndEnroll" type="button" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-lg">
                Capture & Enroll
            </button>
            <button x-show="isCameraActive" @click="stopCamera" type="button" class="px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                Cancel
            </button>
        </div>
    </div>
</div>
