### GN Clone

<!--
    Apps que podem servir para inspiração:
    - GetNinjas
    - TRIIDER
    - https://codefreela.com/
    - https://play.google.com/store/apps/details?id=com.faztudo&hl=pt_BR
    - https://www.b2bstack.com.br/product/getninjas/alternativas

    Ideias para implementação:
    - https://i.imgur.com/KkirRrI.png
    - https://i.imgur.com/lvf8vQI.png
    - https://i.imgur.com/KLqzWQW.png
    - https://i.imgur.com/oDEaquV.png
    - https://i.imgur.com/VfIjKCe.png
    - https://i.imgur.com/GQ5cLIt.png
    - https://i.imgur.com/0K6qEyF.png
    - https://i.imgur.com/cyy9696.png
    - https://i.imgur.com/9B6sPtQ.png
    - https://i.imgur.com/CJhaCAI.png
    - https://i.imgur.com/IQTUdVF.png
    - https://i.imgur.com/abJUajo.png

    Ver comentários negativos do GetNinjas:
    ./roadmap/principais-reclamacoes-do-getninjas-no-commit.md

    Possíveis nomes:
    - Tri... (pensando nos 3 pilares "confiança", "profissionalismo" e "reciprocidade")
        - Tripad - tripad.com.br está disponível
        - Tripod
    - reciTrust
    - proTrust
    - Recipro
    - ProHub
    - ProTrustHub
    - TrustHub

        Esses nomes incorporam a ideia dos três pilares que você mencionou, transmitindo confiança, profissionalismo e reciprocidade. Lembre-se de verificar a disponibilidade do nome antes de tomá-lo como decisão final.
-->

Identificar cada funcionalidade por [ID]

- [LOGIN_FORM] Login com número de telefone
    - ?[LOGICA_CONTA] Tem registro?
        - [*NAO*]
            - [REGISTER_FORM]
                - [VALIDATE_SMS]
                - [PROFILE_TYPE_SELECT]
                    - Sou Profissional
                    - Preciso de um Profissional

                - ?[PROFESSIONAL]
                    - [PROFESSIONAL_FORM]
                        - Campos:
                            - Cidade de atendimento
                            - Categoria
                            - Endereço
                            - Tipo de atendimento (Remoto|Presencial|Ambos)
                            - Permitir ser encontrado por clientes?
                            - Tecnologias (entre 3 e 5)
                    - [PROFESSIONAL_DASHBOARD]
                - ?[CONTRACTOR]
                    - [CONTRACTOR_DASHBOARD]

        - [*SIM*] Retorna:
            ```json
            {
                "token": "object:token",
                "user": "object:user",
                "gold": "bool",
                "category_id": "string",
                "category": "object:category",
                "pendencies": "null|object:pendencies",
                "blocked": "boolean"
            }
            ```

            - ?[IS_BLOCKED]
                - [*SIM*] [BLOCK_REASON_SCREEN]
                    > Por regra, bloqueios não podem ser desfeitos por ação do usuário (como pode ser o caso de algumas pendências)
                    > Ao apresentar a tela com o detalhe da pendência, apresentar o número da pendência para que
                    > o usuário possa entrar em contato com o suporte e resolver (se possível).
                    - Obter e apresentar razão do bloqueio na conta (por exemplo no caso de denúncia de fraude ou algo parecido)
                - [*NAO*]
                    - ?[HAS_PENDENCIES]
                        - [*SIM*]
                            > Se há alguma pendencia como uma comprovação de identidade que foi solicitado manualmente pela
                            > administração, por exemplo no caso de denúncia de fraude ou algo parecido.
                            - Apresentar tela com lista de pendências
                                > Essas pendências podem ser apresentado com ou sem formulário para normalização
                            - [*NAO*]
                                - [MAIN_SCREEN]

- [MAIN_SCREEN] Pode ser `professional` ou `contractor` de acordo com o modo selecionado
    > - O usuário pode mudar para modo `professional` ou `contractor` (profissional|contratante)
    > - Não é possível ver informações de um tipo estando como outro
    > - A tela principal muda o tipo de informação de um tipo para outro
    - [PROFESSIONAL_DASHBOARD]
        - Resumo de contatos liberados
        - Resumo de mensagens recebidas
        - Resumo de ganhos interno pela plataforma
    - [CONTRACTOR_DASHBOARD]
        - Resumo de pedidos de orçamento (anúncios)
        - Resumo de mensagens recebidas
        - Resumo de visualizações de seus anúncios

- [GLOBAL_PROFESSIONAL_LIST] Quando logado como `contractor` (contratante)
    > Tela com listagem de profissionais
        > - Listagem infinita
        > - Ao clicar no card abre a visualização dos detalhes do `professional`
        > - Detalhes para cada card:
        >    - Botão de favoritar
        >    - Título profissional
        >    - Cliente
        >    - Estrelas (avaliação geral)
        >    - Categoria
        >    - ?Tempo médio de resposta
        >    - ?Label (ícone e/ou texto para URGENTE|RECRUTAMENTO|VIP)
        >    - Localização do `professional`
        >    - ?Presencial/Remoto
        >    - ?tags (o usuário pode adicionar ou o sistema adiciona de acordo com as tecnologias que ele selecionou)
        >    - Total de X propostas disponíveis (limite de propostas)

- [GLOBAL_PROJECT_LIST] Quando logado como `professional` (profissional)
    > Tela com listagem dos anúncios
        > - Listagem infinita
        > - Ao clicar no card abre a visualização dos detalhes do `project`
        > - Detalhes para cada card:
        >    - Botão de favoritar
        >    - Título
        >    - Cliente
        >    - Data da postagem
        >    - Categoria
        >    - ?Label (ícone e/ou texto para URGENTE|RECRUTAMENTO|VIP)
        >    - Localização do usuário
        >    - ?Presencial/Remoto
        >    - Total de X propostas disponíveis (limite de propostas)

- [PROJECT_DETAIL]
    - Título
    - Total de X propostas disponíveis (limite de propostas)
    - Descrição
    - Valor para liberar o contato (valor em `coins` as moedas da plataforma)
        - Ocultar caso já tenha sido leberado para o usuário atual
        - Se o usuário for `gold`, mostrar opção de liberar usando bônus diário
    - Nome e telefone do `contractor` (parcial pois para liberar chat e número de telefone o profissional paga ou usa bônus diário se for `gold`)
        > Se o contato não foi liberado, apenas Parte do nome e do telefone
        > Se o usuário for `gold`, pode ver o nome completo e mandar mensagem direto para o cliente
        > Se o usuário for `gold`, pode mostrar interesse direto para o cliente (sem fazer proposta, assim o cliente pode entrar em contato com o profissional gratuitamente)

- [PROFESSIONAL_DETAIL] (Perfil do profissional)
    - Nome do `professional`
    - Valor para liberar o contato (valor em `coins` as moedas da plataforma)
        - Ocultar caso já tenha sido leberado para o usuário atual
        - Se o usuário for `gold`, mostrar opção de liberar usando bônus diário
    - Título profissional
    - Total de X propostas disponíveis (limite de propostas)
    - Carta de apresentação
    - Portifólio
    - Vitrine de produtos e serviços (futuramente será possível comprar serviços e produtos digitais, baixar etc)
    - Nome e telefone do `professional` (parcial) (parcial pois para liberar chat e número de telefone o `contractor` paga ou usa bônus diário se for `gold`)
        > Se o contato não foi liberado, apenas Parte do nome e do telefone
        > Se o usuário for `gold`, pode ver o nome completo e mandar mensagem direto para o cliente
        > Se o usuário for `gold`, pode mostrar interesse direto para o cliente (sem fazer proposta, assim o cliente pode entrar em contato com o profissional gratuitamente)
