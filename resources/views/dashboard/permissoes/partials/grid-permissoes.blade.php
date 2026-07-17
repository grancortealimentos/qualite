{{-- resources/views/papeis/partials/grid-permissoes.blade.php

Grid de checkboxes MÓDULO / PERMISSÕES, compartilhado por create e edit.

Recebe:
$grupos -> catálogo vindo do config, já ordenado pelo código do módulo
$marcadas -> array de nomes técnicos que nascem marcados (vazio no create)

Todo o estado é do Alpine: marcar/desmarcar não vai ao servidor. Só o
submit do form envia permissoes[] para o PapelController.
--}}
@php
    // Lista achatada de todos os nomes técnicos do catálogo. O "Selecionar
    // tudo" precisa dela para saber o universo completo.
    $todasPermissoes = collect($grupos)
        ->flatMap(fn($grupo) => array_keys($grupo['permissoes']))
        ->values()
        ->all();
@endphp

<div x-data="gridPermissoes({
        todas: @js($todasPermissoes),
        iniciais: @js(old('permissoes', $marcadas))
     })" class="flex flex-col">

    {{-- Cabeçalho das colunas --}}
    <div
        class="grid grid-cols-1 md:grid-cols-[minmax(0,18rem)_1fr] gap-x-6 px-1 pb-2 border-b border-dashed border-border">
        <span class="text-xs font-semibold uppercase text-ink-muted">{{ __('Módulo') }}</span>
        <span class="text-xs font-semibold uppercase text-ink-muted">{{ __('Permissões') }}</span>
    </div>

    {{-- Linha "Selecionar tudo": fica na coluna PERMISSÕES, sem módulo à esquerda --}}
    <div class="grid grid-cols-1 md:grid-cols-[minmax(0,18rem)_1fr] gap-x-6 py-3 border-b border-dashed border-border">
        <div></div>
        <label class="inline-flex items-center gap-x-2.5 cursor-pointer group">
            {{-- x-effect mantém o indeterminate sincronizado: é propriedade do
            DOM, não atributo HTML, então não dá para setar via :checked --}}
            <input type="checkbox" x-model="tudoMarcado" x-effect="$el.indeterminate = tudoParcial"
                class="shrink-0 size-4 rounded bg-canvas border-border text-primary focus:ring-primary focus:ring-offset-0">
            <span class="text-sm font-medium text-ink group-hover:text-primary-light">
                {{ __('Selecionar tudo') }}
            </span>
        </label>
    </div>

    {{-- Um bloco por módulo --}}
    @foreach ($grupos as $chave => $grupo)
        @php
            $nomesDoModulo = array_keys($grupo['permissoes']);
        @endphp

        <div wire:key="modulo-{{ $chave }}"
            class="grid grid-cols-1 md:grid-cols-[minmax(0,18rem)_1fr] gap-x-6 gap-y-3 py-4 border-b border-dashed border-border">

            {{-- Coluna MÓDULO: checkbox que marca/desmarca o módulo inteiro --}}
            <div>
                <label class="inline-flex items-center gap-x-2.5 cursor-pointer group">
                    <input type="checkbox" @change="alternarModulo(@js($nomesDoModulo), $event.target.checked)"
                        :checked="moduloMarcado(@js($nomesDoModulo))"
                        x-effect="$el.indeterminate = moduloParcial(@js($nomesDoModulo))"
                        class="shrink-0 size-4 rounded bg-canvas border-border text-primary focus:ring-primary focus:ring-offset-0">
                    <span class="text-sm font-medium text-primary-light group-hover:text-primary">
                        [{{ $grupo['codigo'] }}] - {{ $grupo['label'] }}
                    </span>
                </label>
            </div>

            {{-- Coluna PERMISSÕES --}}
            <div class="flex flex-col gap-y-2.5">
                @foreach ($grupo['permissoes'] as $nome => $rotulo)
                    <label class="inline-flex items-center gap-x-2.5 cursor-pointer group">
                        {{-- O name com [] é o que faz o PHP receber array. Se
                        nenhuma caixa for marcada, a chave não é enviada —
                        o prepareForValidation do PapelRequest força [] --}}
                        <input type="checkbox" name="permissoes[]" value="{{ $nome }}" x-model="selecionadas"
                            class="shrink-0 size-4 rounded bg-canvas border-border text-primary focus:ring-primary focus:ring-offset-0">
                        <span class="text-sm text-ink-muted group-hover:text-ink">{{ $rotulo }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Contador + erro de validação --}}
    <div class="pt-4 flex items-center justify-between gap-3">
        <p class="text-xs text-ink-muted">
            <span class="font-semibold text-ink" x-text="selecionadas.length"></span>
            <span
                x-text="selecionadas.length === 1 ? '{{ __('permissão selecionada') }}' : '{{ __('permissões selecionadas') }}'"></span>
        </p>

        @error('permissoes')
            <p class="text-xs text-danger">{{ $message }}</p>
        @enderror
        @error('permissoes.*')
            <p class="text-xs text-danger">{{ $message }}</p>
        @enderror
    </div>
</div>

@once
    @push('scripts')
        <script>
            /**
             * Estado do grid de permissões.
             *
             * Registrado como função global, e NÃO via Alpine.data() dentro de
             * alpine:init: esse evento dispara uma única vez, no primeiro
             * carregamento. Como esta tela é alcançada por wire:navigate (que
             * troca o body sem recarregar a página), o alpine:init nunca
             * dispararia de novo e o registro não aconteceria — o x-data
             * quebraria com "gridPermissoes is not defined".
             *
             * Fonte única da verdade: o array `selecionadas`. Os checkboxes de
             * módulo e o "selecionar tudo" são DERIVADOS dele — nunca guardam
             * estado próprio. É isso que impede a dessincronização clássica
             * (marcar o módulo, desmarcar um item, e o pai continuar marcado).
             */
            window.gridPermissoes = ({ todas, iniciais }) => ({
                todas: todas,
                selecionadas: iniciais ?? [],

                /**
                 * Getter/setter: lido para saber se tudo está marcado,
                 * escrito quando o usuário clica no "selecionar tudo".
                 */
                get tudoMarcado() {
                    return this.selecionadas.length === this.todas.length;
                },
                set tudoMarcado(valor) {
                    this.selecionadas = valor ? [...this.todas] : [];
                },

                /**
                 * Estado indeterminado: algo marcado, mas não tudo.
                 */
                get tudoParcial() {
                    return this.selecionadas.length > 0 && !this.tudoMarcado;
                },

                moduloMarcado(nomes) {
                    return nomes.every(n => this.selecionadas.includes(n));
                },

                moduloParcial(nomes) {
                    return nomes.some(n => this.selecionadas.includes(n))
                        && !this.moduloMarcado(nomes);
                },

                /**
                 * Marca ou desmarca o módulo inteiro.
                 *
                 * O filter na hora de marcar evita duplicar nomes que já
                 * estavam no array — duplicata faria a contagem mentir e
                 * quebraria o tudoMarcado.
                 */
                alternarModulo(nomes, marcar) {
                    if (marcar) {
                        const novos = nomes.filter(n => !this.selecionadas.includes(n));
                        this.selecionadas = [...this.selecionadas, ...novos];
                    } else {
                        this.selecionadas = this.selecionadas.filter(n => !nomes.includes(n));
                    }
                },
            });
        </script>
    @endpush
@endonce