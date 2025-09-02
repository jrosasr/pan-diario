<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pan Diario - Gestión de Organizaciones Sociales</title>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --azul-rey: #002366;
            --gris: #6c757d;
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .hero-bg {
            background: linear-gradient(45deg, var(--azul-rey) 0%, #003399 100%);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .qr-animation {
            animation: pulse 10s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(0.95);
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow-lg fixed w-full z-50" data-aos="fade-down">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-[var(--azul-rey)]">
                        <img src="logo.png" alt="PAN DIARIO" class="rounded-xl" style="max-height: 75px;">
                    </span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features"
                        class="mt-1 text-gray-600 hover:text-[var(--azul-rey)] transition">Funcionalidades</a>
                    <a href="#beneficios"
                        class="mt-1 text-gray-600 hover:text-[var(--azul-rey)] transition">Beneficios</a>
                    <a href="#contacto" class="mt-1 text-gray-600 hover:text-[var(--azul-rey)] transition">Contacto</a>
                    @if (Route::has('login'))
                        @auth
                            @php
                                $firstTeamId = Auth::user()->teams()->first()->id ?? null;
                            @endphp
                            @if ($firstTeamId)
                                <a href="{{ url('/dashboard/' . $firstTeamId) }}"
                                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    Panel de control
                                </a>
                            @else
                                <a href="{{ url('/dashboard/new') }}"
                                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    Registrar Org.
                                </a>
                            @endif
                        @else
                            <a href="{{ route('filament.dashboard.auth.login') }}"
                                class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                Iniciar sesión
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('filament.dashboard.auth.register') }}"
                                    class="inline-block px-5 py-1.5 border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg text-white pt-32 pb-20" data-aos="zoom-in">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Gestión inteligente para organizaciones sociales
                    </h1>
                    <p class="text-xl mb-8 text-gray-200">
                        Solución gratuita para iglesias y organizaciones benéficas.
                    </p>
                    <a href="{{ route('filament.dashboard.auth.login') }}"
                        class="bg-white text-[var(--azul-rey)] px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                        Iniciar sesión
                    </a>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="images/hero-1.webp" alt="Panel de control" class="rounded-xl shadow-2xl qr-animation"
                        style="max-height: 400px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16 text-[var(--azul-rey)]">Funcionalidades Clave</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card bg-gray-50 p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[var(--azul-rey)] rounded-lg mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" class="text-white" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-building2-icon lucide-building-2">
                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
                            <path d="M10 6h4" />
                            <path d="M10 10h4" />
                            <path d="M10 14h4" />
                            <path d="M10 18h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Gestiona multiples instalaciones</h3>
                    <p class="text-gray-600">Gestión centralizada de multiples localizaciones.</p>
                </div>

                <div class="feature-card bg-gray-50 p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[var(--azul-rey)] rounded-lg mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" class="text-white" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar-days-icon lucide-calendar-days">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                            <path d="M8 14h.01" />
                            <path d="M12 14h.01" />
                            <path d="M16 14h.01" />
                            <path d="M8 18h.01" />
                            <path d="M12 18h.01" />
                            <path d="M16 18h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Gestión de Jornadas</h3>
                    <p class="text-gray-600">Gestión centralizada con historial completo.</p>
                </div>

                <div class="feature-card bg-gray-50 p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[var(--azul-rey)] rounded-lg mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" class="text-white" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-users-round-icon lucide-users-round">
                            <path d="M18 21a8 8 0 0 0-16 0" />
                            <circle cx="10" cy="8" r="5" />
                            <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Registro de Beneficiarios</h3>
                    <p class="text-gray-600">Gestión centralizada con historial completo y fotografía.</p>
                </div>

                <div class="feature-card bg-gray-50 p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-[var(--azul-rey)] rounded-lg mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" class="text-white" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-qr-code-icon lucide-qr-code">
                            <rect width="5" height="5" x="3" y="3" rx="1" />
                            <rect width="5" height="5" x="16" y="3" rx="1" />
                            <rect width="5" height="5" x="3" y="16" rx="1" />
                            <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                            <path d="M21 21v.01" />
                            <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                            <path d="M3 12h.01" />
                            <path d="M12 3h.01" />
                            <path d="M12 16v.01" />
                            <path d="M16 12h1" />
                            <path d="M21 12v.01" />
                            <path d="M12 21v-1" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Generación de carnet con QR</h3>
                    <p class="text-gray-600">Genera carnet indetificadores unicos para cada beneficiarios.</p>
                </div>

                <!-- Repetir para otras funcionalidades -->
            </div>
        </div>
    </section>

    <!-- Beneficios Section -->
    {{-- <section id="beneficios" class="py-20 bg-gray-50" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16 text-[var(--azul-rey)]">¿Por qué elegirnos?</h2>
            <div class="grid md:grid-cols-2 gap-12">
                <div class="flex items-start space-x-6">
                    <div class="w-12 h-12 bg-[var(--azul-rey)] rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" ...></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2">Totalmente Gratuito</h3>
                        <p class="text-gray-600">Sin costos ocultos ni límites de uso</p>
                    </div>
                </div>
                <!-- Más beneficios -->
            </div>
        </div>
    </section> --}}

    <!-- CTA Section -->
    <section class="py-20 hero-bg text-white text-center" data-aos="zoom-in">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-4xl font-bold mb-6">Únete hoy mismo sin coste</h2>
            <p class="text-xl mb-9">Optimiza la gestión de tu organización social hoy mismo</p>
            <a href="{{ route('filament.dashboard.auth.register') }}"
                class="bg-white text-[var(--azul-rey)] px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                Registrarse Gratis
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-300 text-[--azul-rey] py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">
                        <img src="logo.png" alt="PAN DIARIO" class="rounded-xl" style="max-height: 75px;">
                    </h4>
                    <p class="text-[--azul-rey]">Una producto de Soluciones ELYON</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4" id="contacto">Contacto</h4>
                    <p class="text-[--azul-rey]">+58 424 706 0700</p>
                </div>
                <!-- Más columnas -->
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-black">
                © 2024
                <a href="https://soluciones-elyon.com" target="_blank" class="text-[--azul-rey]">Soluciones
                    ELYON</a>.

                <a href="https://github.com/jrosasr/pan-diario" target="_blank"
                    class="text-[--azul-rey] flex items-center justify-center gap-2">

                    Github
                    <svg width="20" height="20" viewBox="0 0 1024 1024" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M8 0C3.58 0 0 3.58 0 8C0 11.54 2.29 14.53 5.47 15.59C5.87 15.66 6.02 15.42 6.02 15.21C6.02 15.02 6.01 14.39 6.01 13.72C4 14.09 3.48 13.23 3.32 12.78C3.23 12.55 2.84 11.84 2.5 11.65C2.22 11.5 1.82 11.13 2.49 11.12C3.12 11.11 3.57 11.7 3.72 11.94C4.44 13.15 5.59 12.81 6.05 12.6C6.12 12.08 6.33 11.73 6.56 11.53C4.78 11.33 2.92 10.64 2.92 7.58C2.92 6.71 3.23 5.99 3.74 5.43C3.66 5.23 3.38 4.41 3.82 3.31C3.82 3.31 4.49 3.1 6.02 4.13C6.66 3.95 7.34 3.86 8.02 3.86C8.7 3.86 9.38 3.95 10.02 4.13C11.55 3.09 12.22 3.31 12.22 3.31C12.66 4.41 12.38 5.23 12.3 5.43C12.81 5.99 13.12 6.7 13.12 7.58C13.12 10.65 11.25 11.33 9.47 11.53C9.76 11.78 10.01 12.26 10.01 13.01C10.01 14.08 10 14.94 10 15.21C10 15.42 10.15 15.67 10.55 15.59C13.71 14.53 16 11.53 16 8C16 3.58 12.42 0 8 0Z"
                            transform="scale(64)" fill="#1B1F23" />
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-in-out'
        });
    </script>
</body>

</html>
