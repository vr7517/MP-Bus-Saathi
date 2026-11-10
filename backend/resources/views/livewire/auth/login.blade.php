<div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-indigo-900 p-8 text-center text-white">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-inner">
            <span class="text-3xl">🚌</span>
        </div>
        <h2 class="text-2xl font-bold">मध्य प्रदेश बस साथी</h2>
        <p class="text-indigo-200 text-xs mt-1">प्रशासन एवं ऑपरेटर पोर्टल (Admin & Operator Portal)</p>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="login" class="p-8 space-y-6">
        @if ($errorMessage)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-sm text-red-700">
                {{ $errorMessage }}
            </div>
        @endif

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">ईमेल पता (Email Address)</label>
            <input type="email" wire:model="email" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                placeholder="admin@mpbussaathi.in">
            @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">पासवर्ड (Password)</label>
            <input type="password" wire:model="password" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                placeholder="••••••••">
            @error('password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span>मुझे याद रखें (Remember me)</span>
            </label>
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl shadow-md transition duration-200 flex items-center justify-center space-x-2">
            <span>लॉगिन करें (Sign In)</span>
            <span wire:loading wire:target="login" class="animate-spin ml-2">⏳</span>
        </button>

        <!-- Quick Demo Credentials Box -->
        <div class="mt-6 pt-6 border-t border-slate-100 text-xs text-slate-500 space-y-2 bg-slate-50 p-4 rounded-xl">
            <p class="font-semibold text-slate-700">डेमो लॉगिन क्रेडेंशियल्स (Demo Accounts):</p>
            <div class="flex justify-between items-center">
                <span>👑 सुपर एडमिन (Admin):</span>
                <code class="bg-white px-2 py-0.5 rounded border border-slate-200">admin@mpbussaathi.in / password123</code>
            </div>
            <div class="flex justify-between items-center">
                <span>🏢 ऑपरेटर (MPSRTC):</span>
                <code class="bg-white px-2 py-0.5 rounded border border-slate-200">operator.1@mpbussaathi.in / password123</code>
            </div>
        </div>
    </form>
</div>

