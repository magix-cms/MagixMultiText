CREATE TABLE IF NOT EXISTS `mc_plug_textmulti` (
    `id_textmulti` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `module_textmulti` varchar(50) NOT NULL,
    `id_module` int(11) unsigned NOT NULL,
    `order_textmulti` smallint(5) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_textmulti`),
    KEY `idx_module_item` (`module_textmulti`, `id_module`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mc_plug_textmulti_content` (
    `id_textmulti_content` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `id_textmulti` int(11) unsigned NOT NULL,
    `id_lang` smallint(3) unsigned NOT NULL,
    `title_textmulti` varchar(255) NOT NULL,
    `desc_textmulti` text,
    `published_textmulti` tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_textmulti_content`),
    KEY `id_lang` (`id_lang`),
    KEY `id_textmulti` (`id_textmulti`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `mc_plug_textmulti_content`
    ADD CONSTRAINT `mc_plug_textmulti_ibfk_1` FOREIGN KEY (`id_textmulti`) REFERENCES `mc_plug_textmulti` (`id_textmulti`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mc_plug_textmulti_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE;