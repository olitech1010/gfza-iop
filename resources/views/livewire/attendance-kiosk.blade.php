<div class="kiosk-container" 
     x-data="{ 
         html5QrcodeScanner: null,
         isScanning: false,
         
         async initScanner() {
             if (this.isScanning || this.html5QrcodeScanner) return;
             
             await this.$nextTick();
             
             const qrContainer = document.getElementById('qr-reader');
             if (!qrContainer) return;
             
             this.isScanning = true;
             
             try {
                 this.html5QrcodeScanner = new Html5Qrcode('qr-reader');
                 await this.html5QrcodeScanner.start(
                     { facingMode: 'environment' },
                     { fps: 10, qrbox: { width: 220, height: 220 } },
                     (decodedText) => {
                         @this.processQrCode(decodedText);
                     },
                     () => {}
                 );
             } catch (err) {
                 console.log('Scanner start error:', err);
                 this.isScanning = false;
             }
         },
         
         async stopScanner() {
             if (this.html5QrcodeScanner) {
                 try {
                     await this.html5QrcodeScanner.stop();
                 } catch (e) {}
                 this.html5QrcodeScanner = null;
             }
             this.isScanning = false;
         },
         
         async restartScanner() {
             await this.stopScanner();
             setTimeout(() => this.initScanner(), 500);
         }
     }"
     x-init="$nextTick(() => initScanner())"
     @reset-state.window="restartScanner()">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .kiosk-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: linear-gradient(135deg, #f8fdf9 0%, #ecfdf5 100%);
            font-family: 'Poppins', 'Segoe UI', system-ui, sans-serif;
        }

        /* Header */
        .kiosk-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: white;
            border-bottom: 3px solid #00c73f;
            box-shadow: 0 2px 10px rgba(0, 199, 63, 0.1);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-brand img {
            height: 45px;
            width: auto;
        }

        .header-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #166534;
        }

        .header-time {
            text-align: right;
        }

        .header-time .time {
            font-size: 2rem;
            font-weight: 700;
            color: #166534;
        }

        .header-time .date {
            font-size: 0.9rem;
            color: #6b7280;
        }

        /* Main */
        .kiosk-main {
            flex: 1;
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            min-height: 0;
        }

        /* Panels */
        .panel {
            flex: 1;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .panel-header {
            background: linear-gradient(135deg, #00c73f 0%, #16a34a 100%);
            color: white;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .panel-header svg {
            width: 24px;
            height: 24px;
        }

        .panel-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .panel-body {
            flex: 1;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
        }

        /* QR Scanner */
        #qr-reader {
            flex: 1;
            background: #f1f5f9;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 250px;
            position: relative;
        }

        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        #qr-reader img {
            display: none !important;
        }

        .scanner-hint {
            text-align: center;
            padding: 1rem;
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Staff ID Input */
        .staff-id-input {
            margin-bottom: 1rem;
        }

        .staff-id-input input {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 1.1rem;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 500;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .staff-id-input input:focus {
            outline: none;
            border-color: #00c73f;
            box-shadow: 0 0 0 3px rgba(0, 199, 63, 0.15);
        }

        /* PIN Display */
        .pin-display {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .pin-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #e5e7eb;
            border: 2px solid #d1d5db;
            transition: all 0.2s;
        }

        .pin-dot.filled {
            background: #00c73f;
            border-color: #00c73f;
            transform: scale(1.15);
        }

        /* PIN Pad */
        .pin-pad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.625rem;
            flex: 1;
        }

        .pin-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.15s;
            min-height: 60px;
            color: #374151;
        }

        .pin-btn:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .pin-btn:active {
            transform: scale(0.96);
            background: #00c73f;
            color: white;
            border-color: #00c73f;
        }

        .pin-btn.clear {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
            font-size: 1rem;
            font-weight: 600;
        }

        .pin-btn.clear:hover {
            background: #fee2e2;
        }

        .pin-btn.submit {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #059669;
            font-size: 1rem;
            font-weight: 600;
        }

        .pin-btn.submit:hover {
            background: #d1fae5;
        }

        /* Overlays */
        .overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 2rem;
        }

        .overlay.success {
            background: linear-gradient(135deg, #00c73f 0%, #16a34a 100%);
        }

        .overlay.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .overlay-content {
            text-align: center;
            color: white;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .overlay-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay-icon svg {
            width: 50px;
            height: 50px;
        }

        .overlay-user-photo {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.5);
            margin: 0 auto 1.5rem;
            display: block;
        }

        .overlay h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .overlay p {
            font-size: 1.3rem;
            opacity: 0.95;
        }

        .overlay .user-detail {
            margin-top: 0.75rem;
            font-size: 1rem;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .kiosk-header {
                padding: 0.75rem 1rem;
            }

            .header-brand h1 {
                font-size: 1.1rem;
            }

            .header-time .time {
                font-size: 1.3rem;
            }

            .kiosk-main {
                flex-direction: column;
                padding: 1rem;
                gap: 1rem;
            }

            #qr-reader {
                min-height: 180px;
                max-height: 220px;
            }

            .pin-btn {
                min-height: 50px;
                font-size: 1.25rem;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            #qr-reader {
                min-height: 280px;
            }
        }

        @media (min-width: 1024px) {
            .panel:first-child {
                flex: 1.2;
            }

            #qr-reader {
                min-height: 320px;
            }

            .pin-btn {
                min-height: 65px;
                font-size: 1.6rem;
            }
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
        <!-- QR Scanner Panel -->
        <section class="panel">
            <div class="panel-header">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <h2>Scan QR Code</h2>
            </div>
            <div class="panel-body">
                <div id="qr-reader" wire:ignore></div>
                <p class="scanner-hint">Position your ID card QR code within the frame</p>
            </div>
        </section>

        <!-- PIN Entry Panel -->
        <section class="panel">
            <div class="panel-header">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2>Enter PIN</h2>
            </div>
            <div class="panel-body">
                <div class="staff-id-input">
                    <input 
                        type="text" 
                        wire:model="staffId" 
                        placeholder="Staff ID (e.g. GFZA/001/26)"
                        autocomplete="off">
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
                <div class="overlay-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            @endif
            <h2>{{ $successMessage }}</h2>
            <p>{{ $checkedInUser['name'] ?? '' }}</p>
            @if($checkedInUser)
                <p class="user-detail">{{ $checkedInUser['staff_id'] }} • {{ $checkedInUser['department'] }}</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Error Overlay -->
    @if($mode === 'error')
    <div class="overlay error" wire:click="resetState">
        <div class="overlay-content">
            <div class="overlay-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2>Error</h2>
            <p>{{ $errorMessage }}</p>
        </div>
    </div>
    @endif
</div>
