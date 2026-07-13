{{-- resources/views/dashboard/pessoas/edit.blade.php --}}
<x-layouts.app :title="__('Editar Pessoa')">
    <div class="max-w-5xl mx-auto pb-12" x-data="{
        fotoUrl: @js($pessoa->foto_url),
        tipoDoc: '{{ old('tipo_documento', $pessoa->tipo_documento ?? 'CPF') }}',
        criarAcesso: {{ old('criar_acesso') ? 'true' : 'false' }},
        verSenha: false,
        preview(e) {
            const file = e.target.files[0];
            if (file) { this.fotoUrl = URL.createObjectURL(file); }
        },
        mascaraDoc(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (this.tipoDoc === 'CNPJ') {
                v = v.slice(0, 14)
                    .replace(/^(\d{2})(\d)/, '$1.$2')
                    .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                    .replace(/\.(\d{3})(\d)/, '.$1/$2')
                    .replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                v = v.slice(0, 11)
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = v;
        },
        mascaraTelefone(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) {
                v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else {
                v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            }
            e.target.value = v;
        }
    }">

        <form action="{{ route('pessoas.update', $pessoa) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Título --}}
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
                        <a href="{{ route('pessoas.index') }}" class="hover:text-ink">{{ __('Pessoas') }}</a>
                        <span>/</span>
                        <span class="text-primary-light">#{{ $pessoa->id }}</span>
                    </div>
                    <h2 class="text-3xl font-bold text-ink tracking-tight">{{ __('Editar Cadastro') }}</h2>
                    <div class="flex items-center gap-3 mt-2">
                        <p class="text-sm text-ink-muted">{{ $pessoa->nome_completo }}</p>
                        <span
                            class="text-[10px] bg-danger/10 text-danger px-2 py-0.5 rounded border border-danger/20 font-medium uppercase tracking-wider">
                            Campos com <span class="text-danger font-bold">*</span> são obrigatórios
                        </span>
                    </div>
                </div>

                <div class="text-end text-xs text-ink-muted shrink-0">
                    <p>{{ __('Criado em') }} {{ $pessoa->created_at->format('d/m/Y H:i') }}</p>
                    @if ($pessoa->updated_at && $pessoa->updated_at->ne($pessoa->created_at))
                        <p>{{ __('Atualizado') }} {{ $pessoa->updated_at->diffForHumans() }}</p>
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

            {{-- LINHA 1: Foto (lateral) + Informações Básicas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- COLUNA LATERAL: Foto + Ativo --}}
                <div class="space-y-6">
                    <div class="bg-surface border border-border rounded-2xl p-8 text-center shadow-sm">
                        <div class="relative inline-block group">
                            <label class="block cursor-pointer">
                                <div
                                    class="size-36 rounded-full bg-canvas border-2 border-dashed border-border flex items-center justify-center overflow-hidden mb-4 mx-auto group-hover:border-primary/50 transition-all">
                                    <svg x-show="!fotoUrl" class="size-14 text-border" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    <img x-show="fotoUrl" x-cloak :src="fotoUrl" alt="{{ $pessoa->nome_completo }}"
                                        class="size-full object-cover">
                                </div>
                                <span
                                    class="absolute bottom-4 right-0 size-10 bg-primary rounded-full flex items-center justify-center hover:bg-primary-hover transition-all border-4 border-surface">
                                    <svg class="size-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M3 9a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.664-.89l.812-1.22A2 2 0 0 1 10.07 4h3.86a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 18.07 7H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z" />
                                        <circle cx="12" cy="13" r="3" />
                                    </svg>
                                </span>
                                <input type="file" name="url_foto_perfil" accept="image/*" class="hidden"
                                    @change="preview">
                            </label>
                        </div>
                        <h3 class="text-ink font-semibold text-lg">Foto do Perfil</h3>
                        <p class="text-xs text-ink-muted mt-1 italic">
                            {{ $pessoa->foto_url ? 'Clique para substituir' : 'Clique no círculo para adicionar' }}
                        </p>
                        @error('url_foto_perfil')
                            <p class="text-xs text-danger mt-2">{{ $message }}</p>
                        @enderror

                        <hr class="my-8 border-border">

                        <div class="flex items-center justify-between p-4 bg-canvas rounded-xl border border-border">
                            <span class="text-sm font-medium text-ink">Ativo <span class="text-danger">*</span></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="eh_ativo" value="1" class="sr-only peer"
                                    @checked(old('eh_ativo', $pessoa->eh_ativo))>
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
                            <div class="p-2 bg-primary/10 rounded-lg text-primary"><svg class="size-6" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg></div>
                            <h3 class="text-xl font-bold text-ink">Informações Básicas</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Tipo Cadastro <span
                                        class="text-danger">*</span></label>
                                <select name="tipo_cadastro" required
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('tipo_cadastro') border-danger @else border-border focus:border-primary @enderror">
                                    @foreach (['Funcionario' => 'Funcionário', 'Veterinario' => 'Veterinário', 'Advogado' => 'Advogado'] as $valor => $rotulo)
                                        <option value="{{ $valor }}"
                                            @selected(old('tipo_cadastro', $pessoa->tipo_cadastro) === $valor)>
                                            {{ $rotulo }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_cadastro')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Nome Completo <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nome_completo"
                                    value="{{ old('nome_completo', $pessoa->nome_completo) }}" required
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 transition-all @error('nome_completo') border-danger @else border-border focus:border-primary @enderror">
                                @error('nome_completo')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Tipo Doc.</label>
                                <select name="tipo_documento" x-model="tipoDoc"
                                    class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 focus:border-primary focus:ring-primary/20">
                                    <option value="CPF">CPF</option>
                                    <option value="CNPJ">CNPJ</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Documento</label>
                                {{-- documento_formatado remonta a máscara: o banco guarda só dígitos --}}
                                <input type="text" name="documento"
                                    value="{{ old('documento', $pessoa->documento_formatado) }}" @input="mascaraDoc"
                                    :placeholder="tipoDoc === 'CNPJ' ? '00.000.000/0000-00' : '000.000.000-00'"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('documento') border-danger @else border-border focus:border-primary @enderror">
                                @error('documento')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Doc. Profissional</label>
                                <input type="text" name="doc_profissional"
                                    value="{{ old('doc_profissional', $pessoa->doc_profissional) }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('doc_profissional') border-danger @else border-border focus:border-primary @enderror"
                                    placeholder="CRM, OAB, etc.">
                                @error('doc_profissional')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">Telefone</label>
                                <input type="text" name="telefone"
                                    value="{{ old('telefone', $pessoa->telefone_formatado) }}" @input="mascaraTelefone"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('telefone') border-danger @else border-border focus:border-primary @enderror"
                                    placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-ink-muted mb-1.5">E-mail</label>
                                <input type="email" name="email" value="{{ old('email', $pessoa->email) }}"
                                    class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-primary/20 @error('email') border-danger @else border-border focus:border-primary @enderror"
                                    placeholder="email@example.com">
                                @error('email')
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
                    <div class="p-2 bg-primary/10 rounded-lg text-primary"><svg class="size-6" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg></div>
                    <h3 class="text-xl font-bold text-ink">Localização</h3>
                </div>
                <p class="text-xs text-ink-muted mb-6 italic">Digite o CEP para preencher o endereço automaticamente.
                </p>

                {{-- old() tem prioridade; senão usa os dados salvos --}}
                <x-form.endereco :endereco="[
                    'cep' => old('cep', $pessoa->cep_formatado),
                    'logradouro' => old('logradouro', $pessoa->logradouro),
                    'numero' => old('numero', $pessoa->numero),
                    'bairro' => old('bairro', $pessoa->bairro),
                    'cidade' => old('cidade', $pessoa->cidade),
                    'estado' => old('estado', $pessoa->estado),
                    'pais' => old('pais', $pessoa->pais),
                ]" />
            </div>

            {{-- LINHA 3: Acesso ao Sistema (full-width) --}}
            <div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm mt-8">
                <div class="p-7 bg-border/20 flex items-center justify-between border-b border-border">
                    <div class="flex items-center gap-4">
                        <div class="p-2.5 bg-accent/10 rounded-xl text-accent-light flex items-center justify-center">
                            <i class="fa-solid fa-user-shield text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-ink">Acesso ao Sistema</h3>
                            <p class="text-sm text-ink-muted">
                                @if ($pessoa->usuario)
                                    Credenciais de login desta pessoa.
                                @else
                                    Esta pessoa ainda não possui acesso. Ative para criar credenciais.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Toggle só aparece quando NÃO existe usuário --}}
                    @unless ($pessoa->usuario)
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="criar_acesso" value="1" class="sr-only peer"
                                x-model="criarAcesso">
                            <div
                                class="w-11 h-6 bg-border rounded-full peer peer-checked:bg-accent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full transition-all">
                            </div>
                        </label>
                    @endunless
                </div>

                @if ($pessoa->usuario)
                    {{-- JÁ TEM USUÁRIO: exibição em leitura --}}
                    <div class="p-7">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-canvas p-4 rounded-xl border border-border">
                                <span class="block text-[10px] text-ink-muted uppercase mb-1">Nome de Exibição</span>
                                <span class="text-sm text-ink">{{ $pessoa->usuario->name }}</span>
                            </div>
                            <div class="bg-canvas p-4 rounded-xl border border-border">
                                <span class="block text-[10px] text-ink-muted uppercase mb-1">E-mail de Login</span>
                                <span class="text-sm text-ink">{{ $pessoa->usuario->email }}</span>
                            </div>
                            <div class="bg-canvas p-4 rounded-xl border border-border">
                                <span class="block text-[10px] text-ink-muted uppercase mb-1">Status</span>
                                <span
                                    class="inline-flex items-center gap-1.5 text-sm {{ $pessoa->usuario->is_active ? 'text-success' : 'text-danger' }}">
                                    <span
                                        class="size-1.5 rounded-full {{ $pessoa->usuario->is_active ? 'bg-success' : 'bg-danger' }}"></span>
                                    {{ $pessoa->usuario->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-ink-muted mt-4 italic">
                            A edição de credenciais é feita em tela própria de usuários.
                        </p>
                    </div>
                @else
                    {{-- SEM USUÁRIO: permite criar --}}
                    <div x-show="criarAcesso" x-collapse>
                        <div class="p-7 space-y-8">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    class="flex items-center justify-between p-4 bg-canvas rounded-xl border border-border">
                                    <div>
                                        <span class="block text-sm font-medium text-ink">Usuário Ativo</span>
                                        <span class="text-xs text-ink-muted">Permitir entrada no sistema.</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                            @checked(old('is_active', true))>
                                        <div
                                            class="w-11 h-6 bg-border rounded-full peer peer-checked:bg-success after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full transition-all">
                                        </div>
                                    </label>
                                </div>

                                <div
                                    class="flex items-center justify-between p-4 bg-canvas rounded-xl border border-border">
                                    <div>
                                        <span class="block text-sm font-medium text-ink">Troca de Senha</span>
                                        <span class="text-xs text-ink-muted">Forçar a troca de senha.</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="force_password_change" value="1"
                                            class="sr-only peer" @checked(old('force_password_change'))>
                                        <div
                                            class="w-11 h-6 bg-border rounded-full peer peer-checked:bg-caution after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full transition-all">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-ink-muted mb-1.5">Nome de Exibição
                                        <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        value="{{ old('name', $pessoa->nome_completo) }}" :required="criarAcesso"
                                        class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-accent/20 transition-all @error('name') border-danger @else border-border focus:border-accent @enderror">
                                    @error('name')
                                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-ink-muted mb-1.5">E-mail de Login
                                        <span class="text-danger">*</span></label>
                                    <input type="email" name="user_email"
                                        value="{{ old('user_email', $pessoa->email) }}" :required="criarAcesso"
                                        class="w-full bg-canvas rounded-xl text-ink py-2.5 focus:ring-accent/20 transition-all @error('user_email') border-danger @else border-border focus:border-accent @enderror">
                                    @error('user_email')
                                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-ink-muted mb-1.5">Senha Provisória
                                        <span class="text-danger">*</span></label>
                                    <div class="relative">
                                        <input :type="verSenha ? 'text' : 'password'" name="password"
                                            :required="criarAcesso"
                                            class="w-full bg-canvas rounded-xl text-ink py-2.5 pe-11 focus:ring-accent/20 transition-all @error('password') border-danger @else border-border focus:border-accent @enderror">
                                        <button type="button" @click="verSenha = !verSenha" tabindex="-1"
                                            class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-ink-muted hover:text-ink"
                                            :aria-label="verSenha ? 'Ocultar senha' : 'Mostrar senha'">
                                            <svg x-show="!verSenha" class="size-5" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            <svg x-show="verSenha" x-cloak class="size-5" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path
                                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <line x1="1" y1="1" x2="23" y2="23" stroke-linecap="round" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="pt-6 border-t border-border">
                                <h4 class="text-xs font-semibold text-ink-muted uppercase tracking-widest mb-4">
                                    Parâmetros Adicionais</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-canvas/50 p-3 rounded-lg border border-border/50">
                                        <span class="block text-[10px] text-ink-muted uppercase mb-1">Expiração da
                                            Senha</span>
                                        <input type="datetime-local" name="password_reset_expires_at"
                                            value="{{ old('password_reset_expires_at') }}"
                                            class="w-full bg-transparent border-none p-0 text-sm text-ink focus:ring-0">
                                    </div>

                                    <div
                                        class="bg-canvas/30 p-3 rounded-lg border border-border/30 opacity-50 flex items-center justify-between">
                                        <div>
                                            <span class="block text-[10px] text-ink-muted uppercase">Última
                                                Alteração</span>
                                            <span class="text-sm text-ink">Nenhuma registrada</span>
                                        </div>
                                        <i class="fa-solid fa-clock-rotate-left text-border"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mensagem quando o toggle está desligado --}}
                    <div x-show="!criarAcesso" class="p-7 text-center">
                        <svg class="size-10 mx-auto text-border mb-3" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        <p class="text-sm text-ink-muted">
                            Esta pessoa <span class="text-ink font-medium">não possui acesso ao sistema</span>.
                            Ative o botão acima para criar credenciais de login.
                        </p>
                    </div>
                @endif
            </div>

            {{-- FOOTER DE AÇÕES --}}
            <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-border">
                <a href="{{ route('pessoas.index') }}"
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