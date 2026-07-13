{{--
    resources/views/components/form/endereco.blade.php

    Bloco de endereço com busca automática de CEP (ViaCEP).
    A busca é conveniência; em qualquer falha o usuário preenche à mão.

    Uso (create):  <x-form.endereco />
    Uso (edit):    <x-form.endereco :endereco="['cep' => $p->cep, ...]" />
--}}
@props(['endereco' => []])

@php
    // Aceita old() inteiro ou um array só de endereço — filtra o que interessa.
    // cep/logradouro/bairro/cidade/estado são controlados pelo Alpine (ViaCEP).
    $dadosEndereco = collect($endereco)
        ->only(['cep', 'logradouro', 'bairro', 'cidade', 'estado'])
        ->all();

    // numero e pais não entram no Alpine: renderizam direto no value do input
    $numero = $endereco['numero'] ?? old('numero');
    $pais = $endereco['pais'] ?? old('pais', 'Brasil');
@endphp

<div x-data="endereco({{ Js::from($dadosEndereco) }})" class="space-y-4">

    {{-- Aviso (só aparece quando há mensagem) --}}
    <div x-show="avisoCep" x-cloak x-transition
        class="flex items-start gap-2.5 p-3 rounded-xl text-sm border"
        :class="avisoTipo === 'erro'
            ? 'bg-danger/10 border-danger/20 text-danger'
            : 'bg-caution/10 border-caution/20 text-caution'">
        <svg class="size-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span x-text="avisoCep"></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-5">

        {{-- CEP com busca automática --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">CEP</label>
            <div class="relative">
                <input type="text" name="cep" x-model="cep" @input="mascaraCep" @blur="buscarCep"
                    placeholder="00000-000" maxlength="9"
                    class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 pe-10 focus:border-primary focus:ring-primary/20">
                {{-- Spinner enquanto busca --}}
                <div x-show="buscandoCep" x-cloak class="absolute inset-y-0 end-0 flex items-center pe-3.5">
                    <svg class="size-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="md:col-span-4">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">Logradouro</label>
            <input type="text" name="logradouro" x-model="logradouro"
                class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 focus:border-primary focus:ring-primary/20">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">Número</label>
            <input type="text" name="numero" value="{{ $numero }}"
                class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 focus:border-primary focus:ring-primary/20">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">Bairro</label>
            <input type="text" name="bairro" x-model="bairro"
                class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 focus:border-primary focus:ring-primary/20">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">Cidade</label>
            <input type="text" name="cidade" x-model="cidade"
                class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 focus:border-primary focus:ring-primary/20">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">Estado</label>
            <input type="text" name="estado" x-model="estado" maxlength="2"
                class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 uppercase focus:border-primary focus:ring-primary/20">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-ink-muted mb-1.5">País</label>
            <input type="text" name="pais" value="{{ $pais }}"
                class="w-full bg-canvas border-border rounded-xl text-ink py-2.5 focus:border-primary focus:ring-primary/20">
        </div>
    </div>
</div>