CREATE TABLE IF NOT EXISTS businesses
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    business_name character varying,
    business_razao character varying,
    business_document character varying,
    business_email character varying,
    business_phone character varying,
    business_whatsapp character varying,
    business_address character varying,
    business_address_number character varying,
    business_address_complement character varying,
    business_address_neighborhood character varying,
    business_address_city character varying,
    business_address_state character varying,
    business_address_cep character varying,
    PRIMARY KEY (id),
    autodata timestamp with time zone NOT NULL DEFAULT now()
);


CREATE TABLE IF NOT EXISTS accounts
(
    id serial NOT NULL,
    acc_id character varying NOT NULL,
    business_id character varying NOT NULL,
    acc_email character varying,
    acc_nickname character varying,
    acc_password character varying,
    acc_created_at timestamp with time zone NOT NULL DEFAULT now(),
    PRIMARY KEY (acc_id)
);

CREATE TABLE IF NOT EXISTS clients
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    code character varying COLLATE pg_catalog."default",
    name character varying COLLATE pg_catalog."default",
    phone character varying COLLATE pg_catalog."default",
    email character varying COLLATE pg_catalog."default",
    price_list_id character varying COLLATE pg_catalog."default",
    type_register character varying COLLATE pg_catalog."default",
    CONSTRAINT clients_pkey PRIMARY KEY (id),
    autodata timestamp with time zone NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS pacientes
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    code character varying COLLATE pg_catalog."default",
    name character varying COLLATE pg_catalog."default",
    client_id character varying COLLATE pg_catalog."default",
    CONSTRAINT pacientes_pkey PRIMARY KEY (id),
    autodata timestamp with time zone NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS chartofaccounts
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    codigo character varying COLLATE pg_catalog."default",
    nome character varying COLLATE pg_catalog."default",
    descricao character varying COLLATE pg_catalog."default",
    PRIMARY KEY (id),
    autodata timestamp with time zone NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS contas_bancarias
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    codigo character varying COLLATE pg_catalog."default",
    nome character varying COLLATE pg_catalog."default",
    cedente character varying COLLATE pg_catalog."default",
    agencia character varying COLLATE pg_catalog."default",
    conta character varying COLLATE pg_catalog."default",
    posto character varying COLLATE pg_catalog."default",
    cnab character varying COLLATE pg_catalog."default",
    banco character varying COLLATE pg_catalog."default",
    autodata timestamp with time zone NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS cobrancas_composicao
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    codigo character varying COLLATE pg_catalog."default",
    order_service_id character varying COLLATE pg_catalog."default",
    contract_id character varying COLLATE pg_catalog."default",
    plano_de_contas_id character varying COLLATE pg_catalog."default",
    descricao character varying COLLATE pg_catalog."default",
    valor character varying COLLATE pg_catalog."default",
    PRIMARY KEY (id),
    autodata timestamp with time zone NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS contratos
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    codigo character varying COLLATE pg_catalog."default",
    client_id character varying COLLATE pg_catalog."default",
    conta_bancaria_id character varying COLLATE pg_catalog."default",
    ativo boolean,
    dia_emissao_cobranca integer NOT NULL DEFAULT 5,
    inicio character varying COLLATE pg_catalog."default",
    valor character varying COLLATE pg_catalog."default",
    PRIMARY KEY (id),
    autodata timestamp with time zone NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS auth_jwtokens
(
    id serial,
    business_id character varying NOT NULL,
    token character varying COLLATE pg_catalog."default",
    acc_id character varying COLLATE pg_catalog."default",
    autodata timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT auth_jwtokens_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS api_tokens
(
    apit_codigo serial,
    business_id character varying NOT NULL,
    apit_token_banco character varying COLLATE pg_catalog."default" NOT NULL,
    apit_token character varying COLLATE pg_catalog."default",
    apit_token_geracao_timestamp timestamp without time zone NOT NULL DEFAULT now(),
    apit_token_expiration_timestamp timestamp without time zone NOT NULL,
    apit_token_expiration_seconds bigint NOT NULL,
    apit_expires_in numeric,
    apit_token_refresh character varying COLLATE pg_catalog."default",
    apit_token_scope character varying(100) COLLATE pg_catalog."default",
    apit_token_type character varying(50) COLLATE pg_catalog."default",
    autodata timestamp with time zone NOT NULL DEFAULT now(),
    bcc_codigo integer,
    CONSTRAINT api_tokens_pkey PRIMARY KEY (apit_codigo)
);

CREATE TABLE IF NOT EXISTS cobrancas
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    pagador_id character varying COLLATE pg_catalog."default" NOT NULL,
    order_service_id character varying COLLATE pg_catalog."default",
    contract_id character varying COLLATE pg_catalog."default" NOT NULL,
    seu_numero character varying COLLATE pg_catalog."default",
    nosso_numero character varying COLLATE pg_catalog."default",
    linha_digitavel character varying COLLATE pg_catalog."default",
    bar_code character varying COLLATE pg_catalog."default",
    valor DECIMAL,
    data_vencimento DATE,
    mensagem TEXT,
    multa DECIMAL,
    juros DECIMAL,
    valor_desconto_antecipacao DECIMAL,
    aceitoPeloBanco boolean NOT NULL DEFAULT false,
    composicao_boleto_texto TEXT,
    tipo_desconto character varying COLLATE pg_catalog."default",
    tipo_juros character varying COLLATE pg_catalog."default",
    tipo_multa character varying COLLATE pg_catalog."default",
    autodata timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT cobrancas_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS products_group
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    name character varying COLLATE pg_catalog."default",
    code character varying COLLATE pg_catalog."default",
    active character varying COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT products_group_pkey PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS products
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    name character varying COLLATE pg_catalog."default",
    code character varying COLLATE pg_catalog."default",
    description character varying COLLATE pg_catalog."default",
    group_id character varying COLLATE pg_catalog."default",
    cost character varying COLLATE pg_catalog."default",
    value character varying COLLATE pg_catalog."default",
    active character varying COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT products_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS price_list
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    name character varying COLLATE pg_catalog."default",
    code character varying COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT price_list_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cargos
(
    id serial NOT NULL,
    business_id character varying NOT NULL,
    name character varying COLLATE pg_catalog."default",
    code character varying COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT cargos_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS caixa_movimentacoes
(
    id serial NOT NULL,
    code character varying COLLATE pg_catalog."default",
    business_id character varying NOT NULL,
    client_id character varying COLLATE pg_catalog."default",
    chartofaccounts_id character varying COLLATE pg_catalog."default",
    descricao character varying COLLATE pg_catalog."default",
    valor numeric,
    estornado boolean NOT NULL DEFAULT false,
    motivo_estorno character varying COLLATE pg_catalog."default",
    data_estorno timestamp with time zone,
    saldo_anterior numeric,
    saldo_atual numeric,
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT caixa_movimentacoes_pkey PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS price_list_products
(
    id serial NOT NULL,
    code character varying COLLATE pg_catalog."default",
    price_list_id character varying COLLATE pg_catalog."default",
    product_id character varying COLLATE pg_catalog."default",
    value character varying COLLATE pg_catalog."default",
    business_id character varying COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT price_list_products_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS order_of_service
(
    id serial NOT NULL,
    code character varying COLLATE pg_catalog."default",
    client_id character varying COLLATE pg_catalog."default",
    bank_account_id character varying COLLATE pg_catalog."default",
    patient_id character varying COLLATE pg_catalog."default",
    end_date character varying COLLATE pg_catalog."default",
    color character varying COLLATE pg_catalog."default",
    status character varying COLLATE pg_catalog."default",
    note character varying COLLATE pg_catalog."default",
    business_id character varying COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT order_of_service_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS order_of_service_products
(
    id serial NOT NULL,
    code character varying COLLATE pg_catalog."default",
    order_of_service_id character varying COLLATE pg_catalog."default",
    business_id character varying COLLATE pg_catalog."default",
    product_id character varying COLLATE pg_catalog."default",
    product_value DECIMAL,
    quantidade integer,
    CONSTRAINT order_of_service_products_pkey PRIMARY KEY (id)
);