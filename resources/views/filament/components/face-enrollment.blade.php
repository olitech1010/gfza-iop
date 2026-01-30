<div x-data="{
    stream: null,
    isCameraActive: false,
    descriptor: null,
    status: 'idle', // idle, detecting, success, error
    message: '',
    
    async init() {
        await this.loadModels();
    },

    async loadModels() {
        this.message = 'Loading models...';
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            this.message = 'Ready. Start camera to enroll.';
        } catch (err) {
            console.error(err);
            this.message = 'Error loading models.';
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
        const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
        
        if (detection) {
            // Found face!
            this.descriptor = Array.from(detection.descriptor);
            this.message = 'Face detected! Saving...';
            
            // Send to backend
            // In Filament action, we can use wire:click or call a method
             @this.set('face_descriptor_json', JSON.stringify(this.descriptor));
             @this.call('saveFaceDescriptor');
            
            this.status = 'success';
            this.message = 'Face enrolled successfully!';
            this.stopCamera();
        } else {
            this.status = 'error';
            this.message = 'No face detected. Try again.';
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

    <!-- Load Script if not already loaded -->
    <script>
        if (typeof faceapi === 'undefined') {
            const script = document.createElement('script');
            script.src = "{{ asset('js/face-api.min.js') }}";
            document.head.appendChild(script);
        }
    </script>

    <div class="relative bg-gray-900 rounded-lg overflow-hidden aspect-video flex items-center justify-center">
        <video x-ref="enrollVideo" class="w-full h-full object-cover" muted></video>
        
        <div x-show="!isCameraActive && status !== 'success'" class="absolute inset-0 flex items-center justify-center bg-gray-800 text-white">
            <p x-text="message"></p>
        </div>

        <div x-show="status === 'success'" class="absolute inset-0 flex flex-col items-center justify-center bg-green-900/90 text-white">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-xl font-bold">Enrolled!</p>
        </div>
    </div>

    <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg dark:bg-gray-800">
        <p class="text-sm font-medium" :class="{
            'text-gray-600': status === 'idle',
            'text-blue-600': status === 'detecting',
            'text-green-600': status === 'success',
            'text-red-600': status === 'error'
        }" x-text="message"></p>

        <div class="flex gap-2">
            <button x-show="!isCameraActive && status !== 'success'" @click="startCamera" type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Start Camera</button>
            <button x-show="isCameraActive" @click="captureAndEnroll" type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Capture</button>
        </div>
    </div>
</div>
