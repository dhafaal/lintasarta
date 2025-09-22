<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Blocked - Security Alert</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-red-50 min-h-screen flex items-center justify-center">
    <div class="max-w-auto w-auto mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Meme Image -->
            <div class="w-128 h-85 mx-auto mb-6">
                <img src="{{ asset('kairiemote.jpg') }}" alt="Blocked Meme" class="w-full h-full object-cover rounded-lg shadow-md">
            </div>
            
            <!-- Warning Icon -->
            <div class="w-16 h-16 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                <i data-lucide="shield-x" class="w-8 h-8 text-red-600"></i>
            </div>
            
            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Access Blocked</h1>
            
            <!-- Message -->
            <div class="text-gray-600 mb-6 space-y-3">
                <p class="text-sm">Your IP address has been temporarily blocked due to suspicious activity.</p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-left">
                    <div class="flex items-center mb-2">
                        <i data-lucide="info" class="w-4 h-4 text-red-600 mr-2"></i>
                        <span class="font-medium text-red-800">Block Details</span>
                    </div>
                    <div class="text-sm text-red-700 space-y-1">
                        <p><strong>Reason:</strong> {{ $blockInfo->reason }}</p>
                        <p><strong>Blocked At:</strong> {{ $blockInfo->blocked_at->format('d M Y, H:i') }}</p>
                        @if($timeRemaining)
                            <p><strong>Time Remaining:</strong> {{ $timeRemaining }} minutes</p>
                        @elseif($blockInfo->is_permanent)
                            <p><strong>Status:</strong> <span class="text-red-800 font-medium">Permanently Blocked</span></p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                <div class="flex items-center mb-2">
                    <i data-lucide="lightbulb" class="w-4 h-4 text-blue-600 mr-2"></i>
                    <span class="font-medium text-blue-800">What can you do?</span>
                </div>
                <div class="text-sm text-blue-700 space-y-1">
                    @if($timeRemaining)
                        <p>• Wait {{ $timeRemaining }} minutes and try again</p>
                        <p>• Ensure you're using the correct login credentials</p>
                        <p>• Contact system administrator if you believe this is an error</p>
                    @elseif($blockInfo->is_permanent)
                        <p>• Contact system administrator immediately</p>
                        <p>• Provide your IP address for investigation</p>
                    @else
                        <p>• Try again later</p>
                        <p>• Contact system administrator if needed</p>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="space-y-3">
                @if($timeRemaining)
                    <div class="text-sm text-gray-500">
                        <p>This page will automatically refresh in <span id="countdown">{{ $timeRemaining * 60 }}</span> seconds</p>
                    </div>
                @endif
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="window.location.reload()" 
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                        Refresh Page
                    </button>
                    
                    <a href="mailto:admin@company.com" 
                       class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                        Contact Admin
                    </a>
                </div>
            </div>
            
            <!-- Security Notice -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center text-xs text-gray-500">
                    <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i>
                    <span>This security measure protects our system from unauthorized access</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            @if($timeRemaining)
            // Countdown timer
            let timeLeft = {{ $timeRemaining * 60 }};
            const countdownElement = document.getElementById('countdown');
            
            const timer = setInterval(function() {
                timeLeft--;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    window.location.reload();
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
            @endif
        });
    </script>
</body>
</html>
