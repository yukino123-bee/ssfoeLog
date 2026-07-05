<!-- Cinematic Dark Zero-G Background Component -->
<div class="fixed inset-0 z-[-1] overflow-hidden bg-[#050505] select-none pointer-events-none">
    
    <!-- Animated Orbs -->
    <div class="absolute inset-0 opacity-80">
        <!-- Top Left Red Orb -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-optimum-red/20 rounded-full blur-[120px] mix-blend-screen animate-blob"></div>
        
        <!-- Top Right Indigo Orb -->
        <div class="absolute top-[20%] right-[-10%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[120px] mix-blend-screen animate-blob animation-delay-2000"></div>
        
        <!-- Bottom Left Blue Orb -->
        <div class="absolute bottom-[-20%] left-[20%] w-[60%] h-[60%] bg-blue-600/20 rounded-full blur-[150px] mix-blend-screen animate-blob animation-delay-4000"></div>
    </div>

    <!-- Subtle Subtle Grid Overlay for Tech feel -->
    <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px); background-size: 32px 32px;"></div>
    
    <!-- Dark Glass streaks -->
    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-transparent"></div>
</div>

<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }

    .animate-blob {
        animation: blob 10s infinite;
    }
    
    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
