<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">🔑 Lupa Password</h2>

        {{-- Status / Alert --}}
        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Error --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $step = session('step', 'email');
            $email = session('email');
        @endphp

        {{-- Step 1: Masukkan Email --}}
        @if ($step === 'email')
            <form method="POST" action="{{ route('password.send.otp') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Kirim OTP
                </button>
            </form>
        @endif

        {{-- Step 2: Masukkan OTP --}}
        @if ($step === 'otp')
            <form method="POST" action="{{ route('password.verify.otp') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div>
                    <label class="block text-gray-700 mb-1">Kode OTP</label>
                    <input type="number" name="otp" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition">
                    Verifikasi OTP
                </button>
            </form>
        @endif

        {{-- Step 3: Reset Password --}}
        @if ($step === 'reset')
            <form method="POST" action="{{ route('password.reset') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label class="block text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 outline-none" required>
                </div>

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg transition">
                    Reset Password
                </button>
            </form>
        @endif
    </div>
</body>
</html>
