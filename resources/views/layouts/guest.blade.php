<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KasKu') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body {
                font-family: 'Poppins', sans-serif;
            }

            /* Animated gradient background */
            @keyframes gradientShift {
                0%, 100% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
            }

            .animated-gradient {
                background: linear-gradient(-45deg, #0f172a, #581c87, #1e1b4b, #0f172a);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }

            /* Floating animation for decorative elements */
            @keyframes float {
                0%, 100% {
                    transform: translateY(0px) rotate(0deg);
                }
                33% {
                    transform: translateY(-20px) rotate(3deg);
                }
                66% {
                    transform: translateY(10px) rotate(-3deg);
                }
            }

            .float {
                animation: float 8s ease-in-out infinite;
            }

            /* Pulse animation for logo */
            @keyframes pulse-glow {
                0%, 100% {
                    box-shadow: 0 0 20px rgba(139, 92, 246, 0.4), 0 0 40px rgba(59, 130, 246, 0.2);
                }
                50% {
                    box-shadow: 0 0 30px rgba(139, 92, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.4);
                }
            }

            .pulse-glow {
                animation: pulse-glow 3s ease-in-out infinite;
            }

            /* Particles background effect */
            @keyframes particle-float {
                0%, 100% {
                    transform: translate(0, 0);
                    opacity: 0;
                }
                10% {
                    opacity: 0.3;
                }
                90% {
                    opacity: 0.3;
                }
                100% {
                    transform: translate(100px, -100vh);
                    opacity: 0;
                }
            }

            .particle {
                animation: particle-float 15s linear infinite;
            }
        </style>

        @stack('styles')
    </head>
    <body class="min-h-screen antialiased animated-gradient overflow-x-hidden">
        
        {{-- Decorative floating elements --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            {{-- Large floating blobs --}}
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl float" style="animation-delay: 0s;"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl float" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500/10 rounded-full blur-3xl float" style="animation-delay: 4s;"></div>
            
            {{-- Smaller particles --}}
            <div class="absolute top-1/4 right-1/4 w-2 h-2 bg-white/20 rounded-full particle" style="animation-delay: 1s;"></div>
            <div class="absolute top-3/4 left-1/4 w-2 h-2 bg-white/20 rounded-full particle" style="animation-delay: 3s;"></div>
            <div class="absolute top-1/2 right-1/3 w-1.5 h-1.5 bg-white/20 rounded-full particle" style="animation-delay: 5s;"></div>
        </div>

        <div class="relative min-h-screen flex flex-col sm:justify-center items-center px-4 py-12 sm:px-6 lg:px-8">
            
            {{-- Logo Section --}}
            <div class="mb-8" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                <a href="/" class="block group">
                    <div class="relative">
                        {{-- Glow effect ring --}}
                        <div class="absolute -inset-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl blur-2xl opacity-30 group-hover:opacity-50 pulse-glow transition-opacity duration-500"></div>
                        
                        {{-- Logo container --}}
                        <div class="relative flex items-center justify-center w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 via-purple-600 to-purple-700 shadow-2xl transition-all duration-500 group-hover:shadow-purple-500/50"
                            :class="hover ? 'scale-110 rotate-6' : 'scale-100 rotate-0'">
                            <svg class="w-12 h-12 text-white transition-all duration-500" 
                                :class="hover ? 'scale-110 -rotate-6' : 'scale-100'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    {{-- App name & tagline --}}
                    <div class="mt-6 text-center">
                        <h1 class="text-3xl font-extrabold text-white tracking-tight group-hover:text-purple-300 transition-colors duration-300">
                            {{ config('app.name', 'KasKu') }}
                        </h1>
                        <p class="text-sm text-white/70 mt-2 font-medium">Kelola keuangan organisasi dengan mudah</p>
                    </div>
                </a>
            </div>

            {{-- Main Content Card --}}
            <div class="w-full sm:max-w-md" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur-xl shadow-2xl overflow-hidden transition-all duration-500 hover:shadow-purple-500/20 hover:border-white/30"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                    
                    {{-- Decorative top gradient border --}}
                    <div class="h-1.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                    
                    {{-- Content slot --}}
                    <div class="px-8 py-8 sm:px-10 sm:py-10">
                        {{ $slot }}
                    </div>
                </div>

                {{-- Footer links --}}
                <div class="mt-8 text-center space-y-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-700 delay-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    
                    <div class="flex items-center justify-center gap-6 text-sm">
                        <a href="#" class="text-white/60 hover:text-white font-medium transition-all duration-200 hover:scale-105">
                            Tentang
                        </a>
                        <span class="text-white/20">•</span>
                        <a href="#" class="text-white/60 hover:text-white font-medium transition-all duration-200 hover:scale-105">
                            Bantuan
                        </a>
                        <span class="text-white/20">•</span>
                        <a href="#" class="text-white/60 hover:text-white font-medium transition-all duration-200 hover:scale-105">
                            Privasi
                        </a>
                    </div>
                    
                    <div class="flex items-center justify-center gap-4">
                        <a href="#" class="text-white/40 hover:text-white/80 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-white/40 hover:text-white/80 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-white/40 hover:text-white/80 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                    </div>
                    
                    <p class="text-xs text-white/40 font-medium">
                        © {{ date('Y') }} {{ config('app.name', 'KasKu') }}. All rights reserved.
                    </p>
                </div>
            </div>

        </div>

        @stack('scripts')
    </body>
</html>