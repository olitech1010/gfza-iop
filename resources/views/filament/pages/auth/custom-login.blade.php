{{-- Single root div required by Livewire --}}
<div class="fi-simple-page">
    {{-- Custom CSS for 2-column layout --}}
    <style>
        .fi-simple-page {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
        .fi-simple-main {
            display: flex !important;
            width: 100%;
            flex-direction: column;
            justify-content: center;
            background-color: white;
            padding: 3rem 1.5rem;
        }
        
        @media (min-width: 1024px) {
            .fi-simple-main {
                width: 40%;
                padding: 3rem 4rem;
            }
        }
        
        .login-hero {
            display: none;
        }
        
        @media (min-width: 1024px) {
            .login-hero {
                display: flex;
                width: 60%;
                position: relative;
                overflow: hidden;
                background: linear-gradient(135deg, #f0fdf4 0%, #f3f4f6 100%);
            }
        }
        
        .login-hero img {
            position: absolute;
            inset: 0;
            height: 100%;
            width: 100%;
            object-fit: cover;
            opacity: 0.9;
        }
        
        .login-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(255,255,255,0.05), rgba(0,199,63,0.1));
        }
        
        .login-hero-text {
            position: absolute;
            bottom: 3rem;
            left: 3rem;
            z-index: 10;
        }
        
        .login-hero-text h2 {
            font-size: 2.25rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 4px 6px rgba(0,0,0,0.3);
            font-family: 'Poppins', sans-serif;
        }
        
        .login-hero-text p {
            margin-top: 0.5rem;
            font-size: 1.125rem;
            color: rgba(255,255,255,0.9);
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>

    {{-- Wrap in Filament's simple page structure --}}
    <x-filament-panels::page.simple>
        {{-- Logo --}}
        @if ($logo = filament()->getBrandLogo())
            <div class="mb-8 flex justify-center">
                <img src="{{ $logo }}" alt="{{ config('app.name') }}" class="h-16 w-auto" />
            </div>
        @endif

        {{-- Welcome Text --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                Welcome Back
            </h1>
            <p class="mt-2 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">
                Sign in to your GFZA Staff Portal
            </p>
        </div>

        {{-- Login Form --}}
        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </x-filament-panels::page.simple>

    {{-- Hero Image Column (hidden on mobile) --}}
    <div class="login-hero">
        <img src="{{ asset('images/login-background.png') }}" alt="GFZA Portal" />
        <div class="login-hero-overlay"></div>
        <div class="login-hero-text">
            <h2>GFZA Internal Operations Portal</h2>
            <p>Green. Fresh. Efficient.</p>
        </div>
    </div>
</div>
