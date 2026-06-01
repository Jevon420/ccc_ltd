<div class="space-y-6 max-w-3xl">

    {{-- Master Toggle --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">AI Features</h3>
                <p class="text-xs text-gray-500 mt-1">Enable or disable all AI-powered features across the portal.</p>
            </div>
            <button type="button" wire:click="$toggle('ai_features_enabled')"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $ai_features_enabled ? 'bg-blue-600' : 'bg-gray-300' }}">
                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 ease-in-out {{ $ai_features_enabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </div>
        @if(!$ai_features_enabled)
        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <p class="text-xs text-amber-800">AI features are currently <strong>disabled</strong>. Enable the toggle and save to activate.</p>
        </div>
        @endif
    </div>

    @if($ai_features_enabled)

    {{-- Provider & Model --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Provider & Model</h3>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">AI Provider</label>
                <select wire:model.live="ai_provider"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Livewire\Settings\AiSettings::$providers as $key => $provider)
                    <option value="{{ $key }}">{{ $provider['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Model</label>
                <select wire:model="ai_model"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Livewire\Settings\AiSettings::$providers[$ai_provider]['models'] ?? [] as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Connection Test --}}
        <div class="border-t border-gray-100 pt-4">
            <div class="flex items-center gap-3">
                <button wire:click="testConnection" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-60">
                    <svg wire:loading wire:target="testConnection" class="animate-spin w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span wire:loading.remove wire:target="testConnection">⚡ Test Connection</span>
                    <span wire:loading wire:target="testConnection">Testing…</span>
                </button>

                @if($testResult)
                <div class="flex-1 text-xs rounded-lg px-3 py-2 {{ $testPassed ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
                    {{ $testResult }}
                </div>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-2">
                API keys are managed via environment variables. Contact your system administrator to change keys.
            </p>
        </div>
    </div>

    {{-- Feature Toggles --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Feature Toggles</h3>
        <p class="text-xs text-gray-500 mb-5">Enable only the AI features your team needs. Each can be turned on or off independently.</p>

        <div class="space-y-4">
            @php
            $features = [
                [
                    'prop'  => 'ai_quote_drafting',
                    'icon'  => '📝',
                    'title' => 'Quote Drafter',
                    'desc'  => 'Generate professional quote descriptions from job details. Human must review before saving.',
                    'risk'  => 'low',
                ],
                [
                    'prop'  => 'ai_email_drafting',
                    'icon'  => '✉️',
                    'title' => 'Email Drafter',
                    'desc'  => 'Draft professional client emails from bullet points. Human must review and send manually.',
                    'risk'  => 'low',
                ],
                [
                    'prop'  => 'ai_report_summaries',
                    'icon'  => '📊',
                    'title' => 'Report Summariser',
                    'desc'  => 'Summarise long job reports into concise executive briefs. Read-only — no data is modified.',
                    'risk'  => 'low',
                ],
                [
                    'prop'  => 'ai_contact_categorisation',
                    'icon'  => '🏷️',
                    'title' => 'Contact Categorisation',
                    'desc'  => 'Automatically tag and prioritise incoming contact form requests by service type and urgency.',
                    'risk'  => 'medium',
                ],
            ];
            @endphp

            @foreach($features as $feature)
            <div class="flex items-start gap-4 p-4 rounded-xl border {{ $this->{$feature['prop']} ? 'border-blue-200 bg-blue-50/50' : 'border-gray-200 bg-white' }} transition-colors">
                <div class="text-2xl flex-shrink-0">{{ $feature['icon'] }}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $feature['title'] }}</p>
                        @if($feature['risk'] === 'medium')
                        <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-1.5 py-0.5 rounded">Automated</span>
                        @else
                        <span class="text-xs bg-green-100 text-green-700 font-semibold px-1.5 py-0.5 rounded">User-initiated</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500">{{ $feature['desc'] }}</p>
                </div>
                <button type="button" wire:click="$toggle('{{ $feature['prop'] }}')"
                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out mt-0.5 {{ $this->{$feature['prop']} ? 'bg-blue-600' : 'bg-gray-300' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out {{ $this->{$feature['prop']} ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Public AI --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-bold text-gray-900">Public Website AI</h3>
            <button type="button" wire:click="$toggle('ai_public_enabled')"
                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors {{ $ai_public_enabled ? 'bg-blue-600' : 'bg-gray-300' }}">
                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $ai_public_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
            </button>
        </div>
        <p class="text-xs text-gray-500 mb-5">AI features visible to public website visitors. Apply aggressive rate limits — these cost real money per visitor.</p>

        @if($ai_public_enabled)
        <div class="space-y-4">

            {{-- Chatbot --}}
            <div class="border border-gray-200 rounded-xl p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">🤖 Chatbot Widget</p>
                        <p class="text-xs text-gray-500 mt-0.5">Floating chat on all public pages. Answers service FAQs, guides to contact form.</p>
                    </div>
                    <button type="button" wire:click="$toggle('ai_chatbot_enabled')"
                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors {{ $ai_chatbot_enabled ? 'bg-blue-600' : 'bg-gray-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $ai_chatbot_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>
                @if($ai_chatbot_enabled)
                <div class="space-y-3 pl-3 border-l-2 border-blue-200">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Greeting Message</label>
                        <input wire:model="ai_chatbot_greeting" type="text" maxlength="300"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('ai_chatbot_greeting') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Max messages per session <span class="font-normal text-gray-400">(1–20, recommended 8)</span>
                        </label>
                        <input wire:model="ai_chatbot_max_messages" type="number" min="1" max="20"
                            class="w-28 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('ai_chatbot_max_messages') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        <strong>Limits enforced:</strong> {{ $ai_chatbot_max_messages }} msgs/session · 20 per IP/day
                    </p>
                </div>
                @endif
            </div>

            {{-- Contact Assist --}}
            <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-900">✍️ Contact Form AI Assist</p>
                    <p class="text-xs text-gray-500 mt-0.5">Helps visitors describe their project before submitting.</p>
                    <p class="text-xs text-amber-700 mt-1">Rate limit: 1 use per IP per 30 minutes</p>
                </div>
                <button type="button" wire:click="$toggle('ai_contact_assist_enabled')"
                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors {{ $ai_contact_assist_enabled ? 'bg-blue-600' : 'bg-gray-300' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $ai_contact_assist_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-4 text-center text-gray-400">
            <p class="text-xs">Enable Public AI above to configure chatbot and contact assist.</p>
        </div>
        @endif
    </div>

    {{-- Safety Notice --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <p class="text-xs font-bold text-amber-900 mb-2">⚠️ AI Safety Policy — Always Enforced</p>
        <ul class="text-xs text-amber-800 space-y-1 list-disc list-inside">
            <li>AI may <strong>suggest, draft, and summarise only</strong> — it never acts autonomously.</li>
            <li>AI will <strong>never</strong> approve quotes, mark invoices paid, assign roles, or delete records.</li>
            <li>Every AI output requires explicit human review before any action is taken.</li>
            <li>All AI interactions are logged in the Audit Log.</li>
        </ul>
    </div>

    @endif

    {{-- Save Button --}}
    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors disabled:opacity-60">
            <span wire:loading wire:target="save">Saving…</span>
            <span wire:loading.remove wire:target="save">Save AI Settings</span>
        </button>
    </div>
</div>
