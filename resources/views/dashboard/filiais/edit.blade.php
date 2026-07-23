{{-- resources/views/dashboard/filiais/edit.blade.php --}}
<x-layouts.app :title="__('Editar Filial')">
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

        <form action="{{ route('filiais.update', $filial) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Título --}}
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
                        <a href="{{ route('filiais.index') }}" class="hover:text-ink">{{ __('Filiais') }}</a>
                        <span>/</span>
                        <span class="text-primary-light">#{{ $filial->codigo }}</span>
                    </div>
                    <h2 class="text-3xl font-bold text-ink tracking-tight">{{ __('Editar Filial') }}</h2>
                    <div class="flex items-center gap-3 mt-2">
                        <p class="text-sm text-ink-muted">{{ $filial->razao_social }}</p>
                        <span
                            class="text-[10px] bg-danger/10 text-danger px-2 py-0.5 rounded border border-danger/20 font-medium uppercase tracking-wider">
                            Campos com <span class="text-danger font-bold">*</span> são obrigatórios
                        </span>
                    </div>
                </div>

                <div class="text-end text-xs text-ink-muted shrink-0">
                    <p>{{ __('Criado em') }} {{ $filial->created_at->format('d/m/Y H:i') }}</p>
                    @if ($filial->updated_at && $filial->updated_at->ne($filial->created_at))
                        <p>{{ __('Atualizado') }} {{ $filial->updated_at->diffForHumans() }}</p>
                    @endif
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

            {{-- LINHA 1: Status (lateral) + Informações Básicas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- COLUNA LATERAL: Status --}}
                <div class="space-y-6">
                    <div class="bg-surface border border-border rounded-2xl p-8 shadow-sm">
                        <div class="text-center">
                            <span
                                class="inline-flex items-center justify-center size-20 rounded-2xl bg-primary/10 text-primary mb-4">
                                <svg class="size-10" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path d="M3 21h18" />
                                    <path d="M5 21V7l8-4v18" />
                                    <path d="M19 21V11l-6-4" />
                                </svg>
                            </span>
                            <h3 class="text-ink font-semibold text-lg">{{ $filial->nome_fantasia ?: $filial->razao_social }}</h3>
                            <p class="text-xs text-ink-muted mt-1 italic">
                                {{ $filial->cnpj ? $filial->cnpj_formatado : 'Sem CNPJ cadastrado' }}
                            </p>
                        </div>

                        <hr class="my-8 border-border">

                        <div class="flex items-center justify-between p-4 bg-canvas rounded-xl border border-border">
                            <span class="text-sm font-medium text-ink">Ativa <span class="text-danger">*</span></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="eh_ativo" value="1" class="sr-only peer"
                                    @checked(old('eh_ativo', $filial->eh_ativo))>
                                <div
                                    class="w-11 h-6 bg-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- COLUNA PRINCIPAL: Informações Básicas --}}
                <div class="lg:col-span-2">
                    <div class="bg-surface border border-border rounded-2xl p-7 shadow-sm h-full">
                        <div class="flex items-center gap-3 mb-8 border-b border-border pb-5">
                            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M3 21h18" />
                                    <path d="M5 21V7l8-4v18" />
                                    <path d="M19 21V11l-6-4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-ink">Informações Básicas</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Código <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="codigo" value="{{ old('codigo', $filial->codigo) }}" required
                                    placeholder="Ex.: 10101"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 transition-all @error('codigo') border-danger @else border-border focus:border-primary @enderror">
                                @error('codigo')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Razão Social <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="razao_social"
                                    value="{{ old('razao_social', $filial->razao_social) }}" required
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 transition-all @error('razao_social') border-danger @else border-border focus:border-primary @enderror">
                                @error('razao_social')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-6">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Nome Fantasia</label>
                                <input type="text" name="nome_fantasia"
                                    value="{{ old('nome_fantasia', $filial->nome_fantasia) }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 transition-all @error('nome_fantasia') border-danger @else border-border focus:border-primary @enderror">
                                @error('nome_fantasia')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">CNPJ</label>
                                {{-- cnpj_formatado remonta a máscara: o banco guarda só dígitos --}}
                                <input type="text" name="cnpj" value="{{ old('cnpj', $filial->cnpj_formatado) }}"
                                    @input="mascaraCnpj" placeholder="00.000.000/0000-00"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('cnpj') border-danger @else border-border focus:border-primary @enderror">
                                @error('cnpj')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Inscrição
                                    Estadual</label>
                                <input type="text" name="ie" value="{{ old('ie', $filial->ie) }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('ie') border-danger @else border-border focus:border-primary @enderror"
                                    placeholder="Isento ou número">
                                @error('ie')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LINHA 2: Localização (full-width) --}}
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
                <p class="text-xs text-ink-muted mb-6 italic">Digite o CEP para preencher o endereço automaticamente.
                </p>

                {{-- old() tem prioridade; senão usa os dados salvos --}}
                <x-form.endereco :endereco="[
                    'cep' => old('cep', $filial->cep_formatado),
                    'logradouro' => old('logradouro', $filial->logradouro),
                    'numero' => old('numero', $filial->numero),
                    'bairro' => old('bairro', $filial->bairro),
                    'cidade' => old('cidade', $filial->cidade),
                    'estado' => old('estado', $filial->estado),
                    'pais' => old('pais', $filial->pais),
                ]" />
            </div>

            {{-- FOOTER DE AÇÕES --}}
            <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-border">
                <a href="{{ route('filiais.index') }}"
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
                    {{ __('Salvar Alterações') }}
                </button>
            </div>

        </form>
    </div>
</x-layouts.app>