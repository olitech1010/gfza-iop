<div class="kiosk-container" border-red-500
     x-data="{ 
         currentMode: 'face', // 'face' or 'qr'
         html5QrcodeScanner: null,
         isVideoPlaying: false,
         faceMatcher: null,
         recognitionInterval: null,
         isLoadingModels: true,
         feedbackMessage: 'Loading...',

         async init() {
            await this.loadModels();
            if (this.currentMode === 'face') {
                this.startFaceVideo();
            }
         },

         // --- Face Recognition Logic ---
         async loadModels() {
             try {
                 this.feedbackMessage = 'Loading models...';
                 await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                 await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                 await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                 
                 this.feedbackMessage = 'Loading user data...';
                 await this.loadDescriptors();
                 
                 this.isLoadingModels = false;
                 this.feedbackMessage = '';
             } catch (err) {
                 console.error('Model loading error:', err);
                 this.feedbackMessage = 'Error loading models.';
             }
         },

         async loadDescriptors() {
            try {
                const response = await fetch('/api/face/descriptors');
                const users = await response.json();
                
                const labeledDescriptors = users.map(user => {
                    const descriptor = new Float32Array(user.descriptor);
                    return new faceapi.LabeledFaceDescriptors(user.id.toString(), [descriptor]);
                });
                
                if (labeledDescriptors.length > 0) {
                    this.faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);
                }
            } catch (err) {
                console.error('Error loading descriptors:', err);
            }
         },

         async startFaceVideo() {
             await this.stopQrScanner();
             
             const video = document.getElementById('face-video');
             if (!video) return;

             try {
                 const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
                 video.srcObject = stream;
                 this.isVideoPlaying = true;
             } catch (err) {
                 console.error('Camera error:', err);
                 this.feedbackMessage = 'Camera error.';
             }
         },

         async stopFaceVideo() {
             const video = document.getElementById('face-video');
             if (video && video.srcObject) {
                 video.srcObject.getTracks().forEach(track => track.stop());
                 video.srcObject = null;
             }
             this.isVideoPlaying = false;
             if (this.recognitionInterval) clearInterval(this.recognitionInterval);
         },

         onPlay() {
             const video = document.getElementById('face-video');
             const canvas = document.getElementById('face-canvas');
             if (!video || !canvas) return;

             const displaySize = { width: video.clientWidth, height: video.clientHeight };
             faceapi.matchDimensions(canvas, displaySize);

             this.recognitionInterval = setInterval(async () => {
                 if (!this.isVideoPlaying || !this.faceMatcher || this.currentMode !== 'face') return;

                 const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
                 const resizedDetections = faceapi.resizeResults(detections, displaySize);
                 
                 canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                 if (resizedDetections.length > 0) {
                     const bestMatch = this.faceMatcher.findBestMatch(resizedDetections[0].descriptor);
                     
                     if (bestMatch.label !== 'unknown') {
                         clearInterval(this.recognitionInterval);
                         @this.processFaceLogin(parseInt(bestMatch.label));
                         
                         // Pause briefly after success
                         setTimeout(() => { 
                             if(this.isVideoPlaying && this.currentMode === 'face') this.onPlay(); 
                         }, 4000);
                     }
                 }
             }, 500);
         },

         // --- QR Scanner Logic ---
         async startQrScanner() {
             await this.stopFaceVideo();
             
             await this.$nextTick(); // Wait for DOM
             if (this.html5QrcodeScanner) return; // Already running

             try {
                 this.html5QrcodeScanner = new Html5Qrcode('qr-reader');
                 await this.html5QrcodeScanner.start(
                     { facingMode: 'environment' },
                     { fps: 10, qrbox: { width: 220, height: 220 } },
                     (decodedText) => { @this.processQrCode(decodedText); },
                     () => {}
                 );
             } catch (err) { console.error(err); }
         },

         async stopQrScanner() {
             if (this.html5QrcodeScanner) {
                 try { await this.html5QrcodeScanner.stop(); } catch (e) {}
                 this.html5QrcodeScanner = null;
             }
         },

         async switchMode(mode) {
             this.currentMode = mode;
             if (mode === 'face') {
                 await this.startFaceVideo();
             } else {
                 await this.startQrScanner();
             }
         }
     }"
     x-init="init"
     @reset-state.window="if(currentMode === 'face') onPlay();">

    <script src="{{ asset('js/face-api.min.js') }}"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        .kiosk-container {
            display: flex; flex-direction: column; height: 100vh;
            background: linear-gradient(135deg, #f8fdf9 0%, #ecfdf5 100%);
            font-family: 'Poppins', sans-serif;
        }
        /* Header */
        .kiosk-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 1.5rem; background: white; border-bottom: 3px solid #00c73f;
            box-shadow: 0 2px 10px rgba(0, 199, 63, 0.1);
        }
        .header-brand { display: flex; align-items: center; gap: 1rem; }
        .header-brand img { height: 45px; width: auto; }
        .header-brand h1 { font-size: 1.5rem; font-weight: 700; color: #166534; }
        .header-time { text-align: right; }
        .header-time .time { font-size: 2rem; font-weight: 700; color: #166534; }
        .header-time .date { font-size: 0.9rem; color: #6b7280; }

        /* Main */
        .kiosk-main { flex: 1; display: flex; gap: 1.5rem; padding: 1.5rem; min-height: 0; }
        .panel {
            flex: 1; background: white; border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex; flex-direction: column; overflow: hidden;
        }

        /* Left Panel Specifics */
        .panel.details-panel { flex: 1.2; }
        .scanner-tabs { display: flex; border-bottom: 1px solid #e5e7eb; }
        .tab-btn {
            flex: 1; padding: 1rem; font-weight: 600; color: #6b7280; background: #f9fafb;
            border: none; cursor: pointer; transition: all 0.2s; display: flex;
            align-items: center; justify-content: center; gap: 0.5rem;
        }
        .tab-btn.active { background: white; color: #00c73f; border-bottom: 3px solid #00c73f; }
        .tab-btn:hover:not(.active) { background: #f3f4f6; }
        
        .scanner-content { flex: 1; position: relative; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        
        /* Face Video */
        video#face-video { width: 100%; height: 100%; object-fit: cover; }
        canvas#face-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        
        /* QR Scanner */
        #qr-reader { width: 100%; height: 100%; background: #f1f5f9; }
        #qr-reader video { object-fit: cover; width: 100% !important; height: 100% !important; }

        /* Right Panel (PIN) */
        .panel-header {
            background: linear-gradient(135deg, #00c73f 0%, #16a34a 100%);
            color: white; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;
        }
        .panel-header h2 { font-size: 1.1rem; font-weight: 600; }
        .panel-body { flex: 1; padding: 1.5rem; display: flex; flex-direction: column; }
        
        .staff-id-input input {
            width: 100%; padding: 0.875rem; font-size: 1.1rem;
            border: 2px solid #d1d5db; border-radius: 10px; text-align: center;
            text-transform: uppercase; font-weight: 500; margin-bottom: 1.5rem;
        }
        .staff-id-input input:focus { outline: none; border-color: #00c73f; }

        .pin-display { display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; }
        .pin-dot { width: 18px; height: 18px; border-radius: 50%; background: #e5e7eb; border: 2px solid #d1d5db; }
        .pin-dot.filled { background: #00c73f; border-color: #00c73f; transform: scale(1.15); }

        .pin-pad { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; flex: 1; }
        .pin-btn {
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 600; background: #f9fafb;
            border: 2px solid #e5e7eb; border-radius: 12px; cursor: pointer;
            transition: all 0.15s; min-height: 50px; color: #374151;
        }
        .pin-btn:active { transform: scale(0.96); background: #00c73f; color: white; border-color: #00c73f; }
        .pin-btn.clear { background: #fee2e2; border-color: #fecaca; color: #dc2626; font-size: 1rem; }
        .pin-btn.submit { background: #ecfdf5; border-color: #a7f3d0; color: #059669; font-size: 1rem; }

        /* Overlays */
        .overlay { position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .overlay.success { background: linear-gradient(135deg, #00c73f 0%, #16a34a 100%); }
        .overlay.error { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .overlay-content { text-align: center; color: white; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .overlay-user-photo { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.5); margin: 0 auto 1.5rem; }
        .overlay h2 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        
        /* Loading Pill */
        .status-pill {
            position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
            background: rgba(0,0,0,0.6); padding: 0.5rem 1rem; border-radius: 20px;
            color: white; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;
            z-index: 10;
        }
    </style>

    <!-- Header -->
    <header class="kiosk-header">
        <div class="header-brand">
            <img src="{{ asset('images/logo.png') }}" alt="GFZA Logo" onerror="this.style.display='none'">
            <h1>NSS Attendance</h1>
        </div>
        <div class="header-time" wire:poll.5s>
            <div class="time">{{ now()->format('h:i A') }}</div>
            <div class="date">{{ now()->format('l, F j, Y') }}</div>
        </div>
    </header>

    <!-- Main -->
    <main class="kiosk-main">
        <!-- Left Panel: Scanner (Face / QR) -->
        <section class="panel details-panel">
            <div class="scanner-tabs">
                <button class="tab-btn" :class="{ 'active': currentMode === 'face' }" @click="switchMode('face')">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </button>
                <button class="tab-btn" :class="{ 'active': currentMode === 'qr' }" @click="switchMode('qr')">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                </button>
            </div>
            
            <div class="scanner-content">
                <!-- Face Scanner -->
                <div x-show="currentMode === 'face'" style="width: 100%; height: 100%; position: relative;">
                    <video id="face-video" autoplay muted onplay="this.dispatchEvent(new CustomEvent('play-video', { bubbles: true }))" @play-video="onPlay"></video>
                    <canvas id="face-canvas"></canvas>
                    
                    <div class="status-pill" x-show="isLoadingModels || feedbackMessage">
                         <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                         <span x-text="isLoadingModels ? 'Initializing AI...' : feedbackMessage"></span>
                    </div>
                </div>

                <!-- QR Scanner -->
                <div x-show="currentMode === 'qr'" style="width: 100%; height: 100%;">
                    <div id="qr-reader"></div>
                </div>
            </div>
        </section>

        <!-- Right Panel: PIN Entry -->
        <section class="panel">
            <div class="panel-header" style="justify-content: center;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <div class="panel-body">
                <div class="staff-id-input">
                    <input type="text" wire:model="staffId" placeholder="Staff ID (e.g. GFZA/001/26)" autocomplete="off">
                </div>
                <div class="pin-display">
                    @for($i = 0; $i < 4; $i++)
                        <div class="pin-dot {{ strlen($pin) > $i ? 'filled' : '' }}"></div>
                    @endfor
                </div>
                <div class="pin-pad">
                    @foreach(['1', '2', '3', '4', '5', '6', '7', '8', '9'] as $digit)
                        <button type="button" class="pin-btn" wire:click="appendPin('{{ $digit }}')">{{ $digit }}</button>
                    @endforeach
                    <button type="button" class="pin-btn clear" wire:click="clearPin">Clear</button>
                    <button type="button" class="pin-btn" wire:click="appendPin('0')">0</button>
                    <button type="button" class="pin-btn submit" wire:click="processPinLogin">OK</button>
                </div>
            </div>
        </section>
    </main>

    <!-- Success Overlay -->
    @if($mode === 'success')
    <div class="overlay success" wire:click="resetState">
        <div class="overlay-content">
            @if($checkedInUser && $checkedInUser['photo'])
                <img src="{{ $checkedInUser['photo'] }}" alt="User Photo" class="overlay-user-photo">
            @else
                <div class="overlay-user-photo bg-white/20 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
            @endif
            <h2>{{ $successMessage }}</h2>
            <p class="text-xl">{{ $checkedInUser['name'] ?? '' }}</p>
            @if($checkedInUser)
                <p class="mt-2 text-lg opacity-80">{{ $checkedInUser['staff_id'] }} • {{ $checkedInUser['department'] }}</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Error Overlay -->
    @if($mode === 'error')
    <div class="overlay error" wire:click="resetState">
        <div class="overlay-content">
            <div class="overlay-user-photo bg-white/20 flex items-center justify-center">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </div>
            <h2>Error</h2>
            <p class="text-xl">{{ $errorMessage }}</p>
        </div>
    </div>
    @endif
</div>
