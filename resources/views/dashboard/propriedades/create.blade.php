{{-- resources/views/propriedades/create.blade.php --}}
<x-layouts.app :title="__('Cadastro de Propriedade')">
    <div class="max-w-5xl mx-auto pb-12" x-data="{
        mascaraCnpj(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 14)
                .replace(/^(\d{2})(\d)/, '$1.$2')
                .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                .replace(/\.(\d{3})(\d)/, '.$1/$2')
                .replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = v;
        }
    }">

        <form action="{{ route('propriedades.store') }}" method="POST">
            @csrf

            {{-- Título --}}
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-ink tracking-tight">{{ __('Nova Propriedade') }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    <p class="text-sm text-ink-muted">Dados cadastrais, áreas e localização da propriedade rural.</p>
                    <span
                        class="text-[10px] bg-danger/10 text-danger px-2 py-0.5 rounded border border-danger/20 font-medium uppercase tracking-wider">
                        Campos com <span class="text-danger font-bold">*</span> são obrigatórios
                    </span>
                </div>
            </div>

            {{-- Erros de validação do backend --}}
            @if ($errors->any())
                <div
                    class="flex items-start gap-2.5 p-4 rounded-xl text-sm border bg-danger/10 border-danger/20 text-danger mb-8">
                    <svg class="size-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div>
                        <p class="font-medium mb-1">{{ __('Corrija os erros abaixo:') }}</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $erro)
                                <li>{{ $erro }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- LINHA 1: Lateral (Status + Produtor) + Informações Básicas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- COLUNA LATERAL: Status + Produtor responsável --}}
                <div class="space-y-6">
                    <div class="bg-surface border border-border rounded-2xl p-7 shadow-sm space-y-6">

                        {{-- Toggle Ativo --}}
                        <div class="flex items-center justify-between p-4 bg-canvas rounded-xl border border-border">
                            <span class="text-sm font-medium text-ink">Ativa <span class="text-danger">*</span></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="eh_ativo" value="1" class="sr-only peer"
                                    @checked(old('eh_ativo', true))>
                                <div
                                    class="w-11 h-6 bg-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success">
                                </div>
                            </label>
                        </div>

                        {{-- Produtor responsável --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-muted mb-1.5">
                                Produtor <span class="text-danger">*</span>
                            </label>
                            <select name="produtor_id" required
                                class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('produtor_id') border-danger @else border-border focus:border-primary @enderror">
                                <option value="">Selecione o produtor...</option>
                                {{-- == e não ===: old() de select volta string; $p->id é int. --}}
                                @foreach ($produtores as $p)
                                    <option value="{{ $p->id }}" @selected(old('produtor_id') == $p->id)>
                                        {{ $p->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('produtor_id')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-ink-muted mt-2 italic">Pessoa responsável pela propriedade.</p>
                        </div>
                    </div>
                </div>

                {{-- COLUNA PRINCIPAL: Informações Básicas --}}
                <div class="lg:col-span-2">
                    <div class="bg-surface border border-border rounded-2xl p-7 shadow-sm h-full">
                        <div class="flex items-center gap-3 mb-8 border-b border-border pb-5">
                            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 21h18" />
                                    <path d="M5 21V7l8-4v18" />
                                    <path d="M19 21V11l-6-4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-ink">Informações Básicas</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-5">
                            <div class="md:col-span-6">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Razão Social <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="razao_social" value="{{ old('razao_social') }}" required
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 transition-all @error('razao_social') border-danger @else border-border focus:border-primary @enderror">
                                @error('razao_social')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-6">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Nome Fantasia</label>
                                <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia') }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 transition-all @error('nome_fantasia') border-danger @else border-border focus:border-primary @enderror">
                                @error('nome_fantasia')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">CNPJ <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="cnpj" value="{{ old('cnpj') }}" @input="mascaraCnpj"
                                    required placeholder="00.000.000/0000-00"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('cnpj') border-danger @else border-border focus:border-primary @enderror">
                                @error('cnpj')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Inscrição Estadual</label>
                                <input type="text" name="ie" value="{{ old('ie') }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('ie') border-danger @else border-border focus:border-primary @enderror">
                                @error('ie')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">NIRF</label>
                                <input type="text" name="nrif" value="{{ old('nrif') }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('nrif') border-danger @else border-border focus:border-primary @enderror"
                                    placeholder="Nº do Imóvel na Receita Federal">
                                @error('nrif')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">CAR</label>
                                <input type="text" name="car" value="{{ old('car') }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('car') border-danger @else border-border focus:border-primary @enderror"
                                    placeholder="Cadastro Ambiental Rural">
                                @error('car')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LINHA 2: Áreas e Produção (full-width) --}}
            <div class="bg-surface border border-border rounded-2xl p-7 shadow-sm mt-8">
                <div class="flex items-center gap-3 mb-4 border-b border-border pb-5">
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3h18v18H3z" />
                            <path d="M3 9h18M9 21V9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-ink">Áreas e Produção</h3>
                </div>
                <p class="text-xs text-ink-muted mb-6 italic">Áreas em hectares. As áreas parciais não podem exceder a
                    área total.</p>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-ink-muted mb-1.5">Área Total (hectares)</label>
                        <input type="number" step="0.01" min="0" name="area_total" value="{{ old('area_total') }}"
                            class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('area_total') border-danger @else border-border focus:border-primary @enderror">
                        @error('area_total')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-muted mb-1.5">Área Consolidada (hectares)</label>
                        <input type="number" step="0.01" min="0" name="area_consolidada"
                            value="{{ old('area_consolidada') }}"
                            class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('area_consolidada') border-danger @else border-border focus:border-primary @enderror">
                        @error('area_consolidada')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-muted mb-1.5">Reserva Legal (hectares)</label>
                        <input type="number" step="0.01" min="0" name="area_reservada_legal"
                            value="{{ old('area_reservada_legal') }}"
                            class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('area_reservada_legal') border-danger @else border-border focus:border-primary @enderror">
                        @error('area_reservada_legal')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-muted mb-1.5">Área de APP (hectares)</label>
                        <input type="number" step="0.01" min="0" name="area_app" value="{{ old('area_app') }}"
                            class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('area_app') border-danger @else border-border focus:border-primary @enderror">
                        @error('area_app')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-ink-muted mb-1.5">
                            Capacidade de Armazenamento (unitário)
                        </label>
                        <input type="number" min="0" name="capacidade_armazenamento"
                            value="{{ old('capacidade_armazenamento') }}"
                            class="w-full md:w-1/4 bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('capacidade_armazenamento') border-danger @else border-border focus:border-primary @enderror">
                        @error('capacidade_armazenamento')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- LINHA 3: Localização (full-width) --}}
            <div class="bg-surface border border-border rounded-2xl p-7 shadow-sm mt-8">
                <div class="flex items-center gap-3 mb-4 border-b border-border pb-5">
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-ink">Localização</h3>
                </div>
                <p class="text-xs text-ink-muted mb-6 italic">Digite o CEP para preencher o endereço automaticamente.</p>

                <x-form.endereco :endereco="old()" />

                {{-- Coordenadas: fora do componente de endereço --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="block text-sm font-medium text-ink-muted mb-1.5">Latitude e Longitude</label>
                        <input type="text" name="latitude_longitude" value="{{ old('latitude_longitude') }}"
                            class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('latitude_longitude') border-danger @else border-border focus:border-primary @enderror"
                            placeholder="-23.061353, -49.125454">
                        @error('latitude_longitude')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- FOOTER DE AÇÕES --}}
            <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-border">
                <a href="{{ route('propriedades.index') }}"
                    class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl bg-surface-hover text-ink-muted hover:bg-border hover:text-ink transition-all border border-border">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Cancelar') }}
                </a>

                <button type="submit"
                    class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-bold rounded-xl bg-success text-white hover:bg-success-hover transition-all shadow-xl shadow-emerald-900/20">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17,3H5C3.89,3,3,3.9,3,5v14c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V7L17,3z M12,19c-1.66,0-3-1.34-3-3s1.34-3,3-3s3,1.34,3,3 S13.66,19,12,19z M15,9H5V5h10V9z" />
                    </svg>
                    {{ __('Salvar') }}
                </button>
            </div>

        </form>
    </div>
</x-layouts.app>