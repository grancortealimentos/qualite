import 'preline'

document.addEventListener('alpine:init', () => {
    Alpine.data('passwordValidator', () => ({
        password: '',
        confirmation: '',

        get rules() {
            return [
                { label: 'Mínimo de 8 caracteres', valid: this.password.length >= 8 },
                { label: 'Uma letra maiúscula', valid: /[A-Z]/.test(this.password) },
                { label: 'Uma letra minúscula', valid: /[a-z]/.test(this.password) },
                { label: 'Um caractere especial', valid: /[^A-Za-z0-9]/.test(this.password) },
            ];
        },
        get passwordsMatch() {
            return this.password === this.confirmation;
        },
        get isValid() {
            return this.rules.every(r => r.valid)
                && this.passwordsMatch
                && this.confirmation.length > 0;
        }
    }));
});

/**
 * Componente reutilizável de enderço com busca por CEP (ViaCEP).
 * 
 * A busca é apenas uma CONVENIÊNCIA: em qualquer falha (offline, timeout, API fora do ar)
 * o usuário continua podendo preencher todos os campos manualmente - nenhum campo é bloqueado.
*/
Alpine.data('endereco', (inicial = {}) => ({
    cep: inicial.cep || '',
    logradouro: inicial.logradouro || '',
    bairro: inicial.bairro || '',
    cidade: inicial.cidade || '',
    estado: inicial.estado || '',
    buscandoCep: false,
    avisoCep: null,
    avisoTipo: null,

    //aplica a mascara 00000-000 enquanto digita
    mascaraCep(e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 8);
        this.cep = v.replace(/^(\d{5})(\d)/, '$1-$2');

        //limpa aviso antigo assim que o usuário volta a  digitar
        this.avisoCep = null;
        this.avisoTipo = null;
    },

    //dispara a busca (ex: no @blur ou quando completa 8 digitos)
    async buscarCep() {
        const cepLimpo = this.cep.replace(/\D/g, '');
        if(cepLimpo.length !== 8) {
            return;
        }

        this.buscandoCep = true;
        this.avisoCep = null;
        this.avisoTipo = null;

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 6000);

        try {
            const resp = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`, {
                signal: controller.signal,
            });
            if(!resp.ok) {
                throw new Error('resposta inválida');
            }

            const dados = await resp.json();
            if(dados.erro) {
                //cep invalido no formato, mas não existe na base
                this.avisoTipo = 'erro';
                this.avisoCep = 'CEP não encontrado. Confira o número ou preencha o endereço manualmente.';
                return;
            }

            //Sucesso: preenche o que veio
            this.logradouro = dados.logradouro || '';
            this.bairro = dados.bairro || '';
            this.cidade = dados.localidade || '';
            this.estado = dados.uf || '';
        }
        catch(e) {
            this.avisoTipo = 'info';
            this.avisoCep = navigator.onLine
                ? 'Não foi possível buscar o CEP agora. Você pode preencher o endereço manualmente.'
                : 'Você está sem conexão com a internet. Preencha o endereço manualmente.';
        }
        finally {
            clearTimeout(timeout);
            this.buscandoCep = false;
        }
    },
}));