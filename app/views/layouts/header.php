<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="<?php echo asset_url('js/tailwind.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo asset_url('css/all.min.css'); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'optimum-red': '#d32f2f',
                        'optimum-dark': '#111111',
                        'optimum-gray': '#f8fafc',
                        'brand-blue': {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        brand: {
                            DEFAULT: '#d32f2f', 
                            dark: '#111111',
                            light: '#f5f5f5',
                        },
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'ui-sans-serif', 'system-ui'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    letterSpacing: {
                        'extra-wide': '0.3em',
                        'super-wide': '0.5em',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                        'blur-in': 'blurIn 0.4s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        blurIn: {
                            '0%': { opacity: '0', filter: 'blur(10px)' },
                            '100%': { opacity: '1', filter: 'blur(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    },
                    backdropBlur: {
                        'xs': '2px',
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/base.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/layout.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/forms.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/responsive.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css'); ?>">
</head>
<body class="<?php echo $body_class ?? 'bg-slate-50 font-sans text-optimum-dark selection:bg-rose-500/10 selection:text-rose-600'; ?>">
    <!-- Background Accents -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-rose-500/5 blur-[120px] animate-float"></div>
        <div class="absolute top-[20%] -right-[5%] w-[30%] h-[30%] rounded-full bg-blue-500/5 blur-[100px] animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[35%] h-[35%] rounded-full bg-emerald-500/5 blur-[110px] animate-float" style="animation-delay: -4s;"></div>
    </div>

