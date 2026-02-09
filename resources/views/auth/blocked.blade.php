<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Access Blocked - Security Alert</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    </head>
    <body class="flex min-h-screen items-center justify-center bg-red-50">
        <div class="max-w-auto mx-auto w-auto">
            <div class="rounded-lg bg-white p-8 text-center shadow-lg">
                <!-- Meme Image -->
                <div class="mx-auto mb-6 h-85 w-128">
                    <img
                        src="{{ asset('kairiemote.jpg') }}"
                        alt="Blocked Meme"
                        class="h-full w-full rounded-lg object-cover shadow-md"
                    />
                </div>

                <!-- Warning Icon -->
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                    <i data-lucide="shield-x" class="h-8 w-8 text-red-600"></i>
                </div>

                <!-- Title -->
                <h1 class="mb-4 text-2xl font-bold text-gray-900">Access Blocked</h1>

                <!-- Message -->
                <div class="mb-6 space-y-3 text-gray-600">
                    <p class="text-sm">Your IP address has been temporarily blocked due to suspicious activity.</p>

                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-left">
                        <div class="mb-2 flex items-center">
                            <i data-lucide="info" class="mr-2 h-4 w-4 text-red-600"></i>
                            <span class="font-medium text-red-800">Block Details</span>
                        </div>
                        <div class="space-y-1 text-sm text-red-700">
                            <p>
                                <strong>Reason:</strong>
                                {{ $blockInfo->reason }}
                            </p>
                            <p>
                                <strong>Blocked At:</strong>
                                {{ $blockInfo->blocked_at->format('d M Y, H:i') }}
                            </p>

                            @if ($timeRemaining)
                                <p>
                                    <strong>Time Remaining:</strong>
                                    {{ $timeRemaining }} minutes
                                </p>
                            @elseif ($blockInfo->is_permanent)
                                <p>
                                    <strong>Status:</strong>
                                    <span class="font-medium text-red-800">Permanently Blocked</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-left">
                    <div class="mb-2 flex items-center">
                        <i data-lucide="lightbulb" class="mr-2 h-4 w-4 text-blue-600"></i>
                        <span class="font-medium text-blue-800">What can you do?</span>
                    </div>
                    <div class="space-y-1 text-sm text-blue-700">
                        @if ($timeRemaining)
                            <p>• Wait {{ $timeRemaining }} minutes and try again</p>
                            <p>• Ensure you're using the correct login credentials</p>
                            <p>• Contact system administrator if you believe this is an error</p>
                        @elseif ($blockInfo->is_permanent)
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
                    @if ($timeRemaining)
                        <div class="text-sm text-gray-500">
                            <p>
                                This page will automatically refresh in
                                <span id="countdown">{{ $timeRemaining * 60 }}</span>
                                seconds
                            </p>
                        </div>
                    @endif

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            onclick="window.location.reload()"
                            class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                        >
                            <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                            Refresh Page
                        </button>

                        <a
                            href="mailto:admin@company.com"
                            class="inline-flex flex-1 items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                        >
                            <i data-lucide="mail" class="mr-2 h-4 w-4"></i>
                            Contact Admin
                        </a>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-center text-xs text-gray-500">
                        <i data-lucide="shield-check" class="mr-1 h-3 w-3"></i>
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
