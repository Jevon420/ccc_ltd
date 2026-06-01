<div>
    @if($submitted)
    <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center">
        <div class="text-4xl mb-3">✅</div>
        <h3 class="text-lg font-bold text-green-900 mb-2">Request Received!</h3>
        <p class="text-sm text-green-700">Thank you, we've received your quote request and will be in touch within 1 business day.</p>
        <button wire:click="$set('submitted', false)" class="mt-5 text-sm font-semibold text-green-700 underline hover:no-underline">
            Submit another request
        </button>
    </div>
    @else
    <form wire:submit="submit" class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
            <input wire:model="name" type="text" placeholder="John Smith"
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
            <input wire:model="email" type="email" placeholder="john@company.com"
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
            <input wire:model="phone" type="tel" placeholder="+1 (868) 000-0000"
                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Needed <span class="text-red-500">*</span></label>
            <select wire:model="service"
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('service') ? 'border-red-400' : 'border-gray-300' }}">
                <option value="">Select a service…</option>
                <option>Development Advisory</option>
                <option>Rural Development</option>
                <option>Debris Cleaning/Removal</option>
                <option>Land Maintenance</option>
                <option>International Metal Trading</option>
                <option>Multiple Services</option>
                <option>Other</option>
            </select>
            @error('service') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-medium text-gray-700">Project Details <span class="text-red-500">*</span></label>
                @if((bool) \App\Models\Setting::get('ai_public_enabled', false) && (bool) \App\Models\Setting::get('ai_contact_assist_enabled', false))
                <livewire:public.contact-assist @contact-assist-ready="message = $event.description" />
                @endif
            </div>
            <textarea wire:model="message" rows="5"
                placeholder="Describe your project, location, timeline, and any specific requirements…"
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 resize-none {{ $errors->has('message') ? 'border-red-400' : 'border-gray-300' }}"></textarea>
            @error('message') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full sm:w-auto inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-lg transition-colors disabled:opacity-60">
                <svg wire:loading wire:target="submit" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span wire:loading.remove wire:target="submit">Send Quote Request</span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
            <p class="text-xs text-gray-400 mt-2">We'll respond within 1 business day.</p>
        </div>

    </form>
    @endif
</div>
