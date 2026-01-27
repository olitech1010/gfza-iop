<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GFZA Attendance Kiosk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .kiosk-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        @media (min-width: 1024px) {
            .kiosk-wrapper {
                padding: 2rem;
            }
        }
    </style>
    @livewireStyles
</head>
<body>
    <div class="kiosk-wrapper">
        {{ $slot }}
    </div>
    
    @livewireScripts
    
    <!-- HTML5 QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Audio feedback -->
    <audio id="success-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleEccWMnX/MZ5QxVJtdz0xYVIGly60+7Ag04XYLrP676DTBlgs8rlvaRJFmC0ye29n0oXYLXG6bybMxZdssPsu5EqGWKxv+u+lCoaZK++7b+WLBxnrLvrwZouHWqquOq+mDEdaqi26r+YMx9rprPov5oxH2yks+fAmC8far+w5b+XLR9sv67iv5gtH229sOO/mCwfb72u4r+YLB5wvK7hvpgtHnC7reC+mC0ecbqs4L2YLB5xuqzhvpgsHnG5rOC9mCwecbms37yYLB5xuazfvJgsHnG5rd+8mC0dcrms3ryYLR1yuKzfvJgtHXK4rN68mC0dcres3ryYLR1yt6zevJcsHXG3rN68ly0dcrWs37yXLRxyt6zhvJctHHO2quC8mCwcc7aq4LyYLBxztqrhvJcsHHO2quG8lywcc7aq4byXLBxztqrhvJcsHHO2quC8mC0ccbaq4byYLRxyuKvgvJgtHHG4q+C8mC0ccbir4LyYLRxxuavgvJgtG3G5q9+8mC0bcbqr37uYLRtxuqvfvJgtG3C5q9+7mC0bcLmr4LuYLRpwuavfu5gtGnC5q+C7mC0acLqr37uYLRpxuqvfu5gtGnG6q968ly0acbqq3ryXLRpxuqrevJgtGnG6qt67ly0acbqq3ruYLRtxuqrdvJctG3G7qt27mC0bcburw=" type="audio/wav">
    </audio>
    <audio id="error-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACA/3+AgIB/f4CAgH9/f4CAf4CAgH9/gIB/f4CAgH+AgIB/f4CAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH+AgIB/f4CAf4CAgIB/gIB/f4CAgH+AgICAf4CAf3+AgIB/gICAf3+AgH+AgICAgH+Af4CAgIB/gICAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH+AgIB/f4CAf4CAgIB/gIB/f4CAgH+AgICAf4CAf3+AgIB/gICAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH+AgIB/f4CAf4CAgICAf4B/f4CAgH+AgICAgH+Af3+AgIB/gICAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH+AgIB/f4CAf4CAgICAgH+Af3+AgIB/gICAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH+AgIB/f4CAf4CAgICAf4B/f4CAgH+AgICAgH+Af3+AgIB/gICAf4CAgH9/gIB/gICAgH+AgH9/gICAf4CAgH+AgIB/f4CAgH+AgH+AgH+AgIB/" type="audio/wav">
    </audio>
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-success-sound', () => {
                document.getElementById('success-sound')?.play().catch(() => {});
            });
            
            Livewire.on('play-error-sound', () => {
                document.getElementById('error-sound')?.play().catch(() => {});
            });
            
            Livewire.on('auto-reset', ({ delay }) => {
                setTimeout(() => {
                    Livewire.dispatch('resetState');
                }, delay);
            });
        });
    </script>
</body>
</html>
