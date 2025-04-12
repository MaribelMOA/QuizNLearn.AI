<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizNLearn.ai - Interactive Learning Platform</title>
    <meta name="description" content="The best platform for learning with interactive quizzes">

    <!-- Estilos compilados de Laravel (incluye Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js (si lo necesitas) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-gray-800 bg-gray-50">
<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="#" class="flex items-center space-x-2">
            <svg class="w-10 h-10" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="20" cy="20" r="20" fill="#F0F9FF"/>
                <path d="M10 20C10 14.4772 14.4772 10 20 10C25.5228 10 30 14.4772 30 20C30 25.5228 25.5228 30 20 30C14.4772 30 10 25.5228 10 20Z" fill="#3B82F6"/>
                <path d="M20 25C22.7614 25 25 22.7614 25 20C25 17.2386 22.7614 15 20 15C17.2386 15 15 17.2386 15 20C15 22.7614 17.2386 25 20 25Z" fill="#F59E0B"/>
                <path d="M20 23C21.6569 23 23 21.6569 23 20C23 18.3431 21.6569 17 20 17C18.3431 17 17 18.3431 17 20C17 21.6569 18.3431 23 20 23Z" fill="#10B981"/>
            </svg>
            <span class="text-xl font-bold text-gray-800">QuizNLearn<span class="text-primary">.ai</span></span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="#features" class="text-gray-600 hover:text-primary transition-colors">Features</a>
            <a href="#modes" class="text-gray-600 hover:text-primary transition-colors">Modes</a>
            <a href="#plans" class="text-gray-600 hover:text-primary transition-colors">Plans</a>
        </nav>

        <div class="flex items-center space-x-4">
            <a href="{{ route('login') }}" class="px-4 py-2 text-primary hover:text-primary-dark transition-colors">Login</a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors">Sign Up</a>

            <!-- Mobile menu button -->
            <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="#features" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Features</a>
            <a href="#modes" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Modes</a>
            <a href="#plans" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Plans</a>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    QuizNLearn.AI!<br>
                    <span class="text-primary">Welcome to</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8">The best platform for learning with interactive quizzes.</p>
                <div class="flex space-x-4">
                    <a href="#features" class="px-6 py-3 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors shadow-md">
                        Explore Features
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-primary border border-primary rounded-md hover:bg-gray-50 transition-colors shadow-md">
                        Get Started
                    </a>
                </div>
            </div>
            <div class="md:w-1/2">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg opacity-10 blur-xl transform -rotate-6"></div>
                    <div class="relative bg-white p-6 rounded-lg shadow-xl">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-3 bg-blue-100 rounded-lg p-4 flex items-center justify-center">
                                <span class="text-3xl font-bold text-blue-600">QUIZ</span>
                            </div>
                            <div class="bg-yellow-100 rounded-lg p-4 flex items-center justify-center">
                                <i class="fas fa-brain text-2xl text-yellow-500"></i>
                            </div>
                            <div class="bg-green-100 rounded-lg p-4 flex items-center justify-center">
                                <i class="fas fa-lightbulb text-2xl text-green-500"></i>
                            </div>
                            <div class="bg-red-100 rounded-lg p-4 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-2xl text-red-500"></i>
                            </div>
                            <div class="col-span-3 bg-indigo-100 rounded-lg p-4">
                                <div class="h-4 w-full bg-indigo-200 rounded-full mb-2"></div>
                                <div class="h-4 w-3/4 bg-indigo-200 rounded-full"></div>
                            </div>
                            <div class="col-span-2 bg-purple-100 rounded-lg p-4 flex items-center justify-center">
                                <i class="fas fa-chart-line text-2xl text-purple-500 mr-2"></i>
                                <span class="font-semibold text-purple-700">Progress</span>
                            </div>
                            <div class="bg-pink-100 rounded-lg p-4 flex items-center justify-center">
                                <i class="fas fa-trophy text-2xl text-pink-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-16">Features</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-100 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                        <i class="fas fa-pencil-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Quiz Creation</h3>
                </div>
                <p class="text-gray-600 text-center">
                    Generate customized quizzes from multiple sources with our intuitive editor.
                </p>
                <div class="mt-6 text-center">
                    <a href="#" class="text-primary hover:text-blue-700 font-medium inline-flex items-center">
                        Learn more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-100 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                        <i class="fas fa-history text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold">History & Export</h3>
                </div>
                <p class="text-gray-600 text-center">
                    Save, edit, and export quizzes as PDFs or JSON files for easy sharing.
                </p>
                <div class="mt-6 text-center">
                    <a href="#" class="text-primary hover:text-blue-700 font-medium inline-flex items-center">
                        Learn more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-100 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Gamification</h3>
                </div>
                <p class="text-gray-600 text-center">
                    Earn XP and unlock new premium features as you learn and progress.
                </p>
                <div class="mt-6 text-center">
                    <a href="#" class="text-primary hover:text-blue-700 font-medium inline-flex items-center">
                        Learn more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Learning Modes Section -->
<section id="modes" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-16">Learning Modes</h2>

        <div class="grid grid-cols-1 gap-8">
            <!-- Study Mode -->
            <div class="overflow-hidden rounded-lg shadow-lg transition-transform duration-300 hover:scale-[1.02]">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="bg-rose-500 text-white p-8 flex flex-col justify-center">
                        <h3 class="text-2xl md:text-3xl font-bold mb-4">Study Mode</h3>
                        <p class="text-lg">
                            Answer questions with explanations and reinforce learning with AI.
                        </p>
                    </div>
                    <div class="bg-gray-100 h-64 md:h-auto">
                        <div class="w-full h-full bg-[url('https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1073&q=80')] bg-cover bg-center"></div>
                    </div>
                </div>
            </div>

            <!-- Quiz Mode -->
            <div class="overflow-hidden rounded-lg shadow-lg transition-transform duration-300 hover:scale-[1.02]">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="bg-gray-100 order-2 md:order-1 h-64 md:h-auto">
                        <div class="w-full h-full bg-[url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80')] bg-cover bg-center"></div>
                    </div>
                    <div class="bg-blue-500 text-white p-8 flex flex-col justify-center order-1 md:order-2">
                        <h3 class="text-2xl md:text-3xl font-bold mb-4">Quiz Mode</h3>
                        <p class="text-lg">
                            Answer quizzes with instant scoring and performance tracking.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kahoot Mode -->
            <div class="overflow-hidden rounded-lg shadow-lg transition-transform duration-300 hover:scale-[1.02]">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="bg-green-500 text-white p-8 flex flex-col justify-center">
                        <h3 class="text-2xl md:text-3xl font-bold mb-4">Kahoot Mode</h3>
                        <p class="text-lg">
                            Compete live with other users in an interactive experience.
                        </p>
                    </div>
                    <div class="bg-gray-100 h-64 md:h-auto">
                        <div class="w-full h-full bg-[url('https://images.unsplash.com/photo-1543269865-cbf427effbad?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80')] bg-cover bg-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Subscription Plans Section -->
<section id="plans" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-16">Subscription Plans</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Free Plan -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                        <i class="fas fa-user text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Free</h3>
                    <div class="text-4xl font-bold mb-6">
                        $0
                        <span class="text-sm text-gray-500 font-normal">/per month</span>
                    </div>

                    <ul class="text-left space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Access all modes with restrictions</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Quiz Creator (Limited)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Maximum of 5 PDFs per month</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Basic statistics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Community support</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-gray-100 hover:bg-gray-200 text-primary font-medium rounded-md transition-colors">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Standard Plan -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden border-2 border-primary transform scale-105 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="bg-primary text-white py-2 text-center text-sm font-medium">
                    MOST POPULAR
                </div>
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Standard</h3>
                    <div class="text-4xl font-bold mb-6">
                        $5.99
                        <span class="text-sm text-gray-500 font-normal">/per month</span>
                    </div>

                    <ul class="text-left space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Access all modes</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Quiz Creator (Standard)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Unlimited PDFs and JSON exports</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Advanced statistics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Priority email support</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-primary hover:bg-blue-600 text-white font-medium rounded-md transition-colors">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                        <i class="fas fa-crown text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Premium Plan</h3>
                    <div class="text-4xl font-bold mb-6">
                        $9.99
                        <span class="text-sm text-gray-500 font-normal">/per month</span>
                    </div>

                    <ul class="text-left space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Access all modes (premium)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Quiz Creator (Unlimited)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Unlimited PDFs and all export formats</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>AI-powered analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>24/7 priority support</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Early access to new features</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-md transition-colors">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-primary to-blue-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Start Learning?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of students who are already improving their knowledge with QuizNLearn.ai</p>
        <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-primary font-bold rounded-md hover:bg-gray-100 transition-colors shadow-lg">
            Create Free Account
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-white pt-12 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Features</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Learning Modes</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Plans</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-2 text-gray-400"></i>
                        <span>support@quiznlearn.ai</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400"></i>
                        <span>123 AI Learning St, Tech City</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-2 text-gray-400"></i>
                        <span>+1 234 567 890</span>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
                <p class="text-gray-300 mb-4">Subscribe to get updates on new features and promotions.</p>
                <form class="flex">
                    <input type="email" placeholder="Your email" class="px-4 py-2 w-full rounded-l-md focus:outline-none text-gray-800">
                    <button type="submit" class="bg-primary hover:bg-blue-600 px-4 py-2 rounded-r-md transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-8 mt-8 text-center text-gray-400">
            <p>&copy; 2023 QuizNLearn.AI. All rights reserved.</p>
            <div class="mt-2">
                <a href="#" class="text-gray-400 hover:text-white mx-2 transition-colors">Terms of Service</a>
                <a href="#" class="text-gray-400 hover:text-white mx-2 transition-colors">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white mx-2 transition-colors">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Adjust for header height
                    behavior: 'smooth'
                });

                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    });

    // Add animation classes on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const animateElements = document.querySelectorAll('[class*="hover:-translate-y"], [class*="hover:scale"]');

        function checkScroll() {
            const triggerBottom = window.innerHeight * 0.8;

            animateElements.forEach(el => {
                const elementTop = el.getBoundingClientRect().top;

                if (elementTop < triggerBottom) {
                    el.classList.add('opacity-100');
                    el.classList.remove('opacity-0', 'translate-y-10');
                }
            });
        }

        // Add initial classes
        animateElements.forEach(el => {
            el.classList.add('opacity-0', 'translate-y-10', 'transition-all', 'duration-700');
        });

        // Initial check
        checkScroll();

        // Check on scroll
        window.addEventListener('scroll', checkScroll);
    });
</script>
</body>
</html>


