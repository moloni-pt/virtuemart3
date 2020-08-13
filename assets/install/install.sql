CREATE TABLE IF NOT EXISTS `#__moloni_api`
(
    id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    main_token    VARCHAR(100),
    refresh_token VARCHAR(100),
    client_id     VARCHAR(100),
    client_secret VARCHAR(100),
    company_id    INT,
    dated         TIMESTAMP default CURRENT_TIMESTAMP
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 2;

CREATE TABLE IF NOT EXISTS `#__moloni_api_config`
(
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    config      VARCHAR(100),
    description VARCHAR(100),
    selected    VARCHAR(100),
    changed     TIMESTAMP default CURRENT_TIMESTAMP
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 2;


INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('document_set_id', 'Escolha uma Série de Documentos para melhor organização');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('exemption_reason', 'Escolha uma Isenção de Impostos para os produtos que não têm impostos');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('payment_method', 'Escolha um metodo de pagamento por defeito');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('measure_unit', 'Escolha a unidade de medida a usar');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('maturity_date', 'Prazo de Pagamento');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('document_status', 'Escolha o estado do documento (fechado ou em rascunho)');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('document_type', 'Escolha o tipo de documentos que deseja emitir');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('client_prefix', 'Prefixo da referência do cliente');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('product_prefix', 'Prefixo da referência do produto');
INSERT INTO `#__moloni_api_config`(config, description)
VALUES ('vat_field', 'Número de contribuinte');


ALTER TABLE `#__virtuemart_orders`
    ADD `moloni_sent` INT DEFAULT '0' NOT NULL