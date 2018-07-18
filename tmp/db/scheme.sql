CREATE TABLE IF NOT EXISTS `workers` (

	`id`                INT UNSIGNED AUTO_INCREMENT                                                        NOT NULL,
	`first`             VARCHAR(50)                                                                        NOT NULL,
	`second`            VARCHAR(50)                                                                        NOT NULL,
	`last`              VARCHAR(50)                                                                        NOT NULL,
	`sex`               BOOLEAN                                                                            NOT NULL,
	`birthday`          DATE                                                                               NOT NULL,
	`email`             VARCHAR(128)                                                                       NOT NULL,
	`phone`             VARCHAR(12)                                                                        NOT NULL,
	`profession_id`     INT UNSIGNED                                                                       NOT NULL,
	`status`            BOOLEAN                                                                            NOT NULL DEFAULT TRUE,
	`salt`              VARCHAR(32)                                                                        NOT NULL,
	`password`          VARCHAR(128)                                                                       NOT NULL,
	`roles`             SET ( 'ROLE_NONE', 'ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_NURSE', 'ROLE_DOCTOR' ) NOT NULL DEFAULT 'ROLE_NONE',

	`info_create_time`  TIMESTAMP                                                                          NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                                                                          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                                                                       NOT NULL,
	`info_changed_id`   INT UNSIGNED                                                                       NOT NULL,

	KEY `fk_workers_profession` (`profession_id`),

	CONSTRAINT `pk_workers_id` PRIMARY KEY (`id`),
	CONSTRAINT `uk_workers_phone` UNIQUE (`phone`),
	CONSTRAINT `fk_workers_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_workers_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `professions` (

	`id`                INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`name`              VARCHAR(100)                NOT NULL,

	`info_create_time`  TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                NOT NULL,
	`info_changed_id`   INT UNSIGNED                NOT NULL,

	CONSTRAINT `pk_professions_id` PRIMARY KEY (`id`),
	CONSTRAINT `uk_professions_name` UNIQUE (`name`),
	CONSTRAINT `fk_professions_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_professions_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `patients` (

	`id`                INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`first`             VARCHAR(50)                 NOT NULL,
	`second`            VARCHAR(50)                 NOT NULL,
	`last`              VARCHAR(50)                 NOT NULL,
	`sex`               BOOLEAN                     NOT NULL,
	`birthday`          DATE                        NOT NULL,
	`phone`             VARCHAR(24)                          DEFAULT NULL,
	`address`           VARCHAR(128)                         DEFAULT NULL,
	`note`              TEXT                                 DEFAULT NULL,

	`info_create_time`  TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                NOT NULL,
	`info_changed_id`   INT UNSIGNED                NOT NULL,

	CONSTRAINT `pk_patients_id` PRIMARY KEY (`id`),
	CONSTRAINT `fk_patients_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_patients_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `protocols` (

	`id`                INT UNSIGNED AUTO_INCREMENT             NOT NULL,
	`name`              VARCHAR(50)                             NOT NULL,
	`type`              ENUM ( 'TYPE_SAMPLE', 'TYPE_TEMPLATE' ) NOT NULL,
	`table`             VARCHAR(50)                             NOT NULL,

	`info_create_time`  TIMESTAMP                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                            NOT NULL,
	`info_changed_id`   INT UNSIGNED                            NOT NULL,

	CONSTRAINT `pk_protocols_id` PRIMARY KEY (`id`),
	CONSTRAINT `uk_protocols_table` UNIQUE (`table`),
	CONSTRAINT `fk_protocols_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_protocols_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `services` (

	`id`                INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`name`              VARCHAR(100)                NOT NULL,
	`price`             FLOAT                       NOT NULL,

	`info_create_time`  TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                NOT NULL,
	`info_changed_id`   INT UNSIGNED                NOT NULL,

	CONSTRAINT `pk_services_id` PRIMARY KEY (`id`),
	CONSTRAINT `uk_services_table` UNIQUE (`name`),
	CONSTRAINT `fk_services_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_services_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `receptions` (

	`id`                   INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`patient_id`           INT UNSIGNED                NOT NULL,
	`worker_id`            INT UNSIGNED                NOT NULL,
	`time`                 DATETIME                    NOT NULL,
	`active`               BOOLEAN                     NOT NULL DEFAULT TRUE,
	`paid`                 BOOLEAN                     NOT NULL DEFAULT FALSE,

	`protocol_sample_id`   INT UNSIGNED                         DEFAULT NULL,
	`protocol_template_id` INT UNSIGNED                         DEFAULT NULL,
	`analyzes`             TEXT                                 DEFAULT NULL,

	`info_create_time`     TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time`    TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`     INT UNSIGNED                NOT NULL,
	`info_changed_id`      INT UNSIGNED                NOT NULL,

	KEY `fk_receptions_patient` (`patient_id`),
	KEY `fk_receptions_worker` (`worker_id`),

	KEY `fk_protocols_sample` (`protocol_sample_id`),
	KEY `fk_protocols_template` (`protocol_template_id`),

	CONSTRAINT `pk_receptions_id` PRIMARY KEY (`id`),
	CONSTRAINT `chk_receptions_time` CHECK (`time` >= CURRENT_TIMESTAMP),
	CONSTRAINT `fk_receptions_patient_id` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
	CONSTRAINT `fk_receptions_worker_id` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`),

	CONSTRAINT `fk_receptions_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_receptions_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`),

	CONSTRAINT `fk_protocols_protocol_sample_id` FOREIGN KEY (`protocol_sample_id`) REFERENCES `protocols` (`id`),
	CONSTRAINT `fk_protocols_protocol_template_id` FOREIGN KEY (`protocol_template_id`) REFERENCES `protocols` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;


CREATE TABLE IF NOT EXISTS `professions_protocols` (

	`id`                INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`profession_id`     INT UNSIGNED                NOT NULL,
	`protocol_id`       INT UNSIGNED                NOT NULL,

	`info_create_time`  TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                NOT NULL,
	`info_changed_id`   INT UNSIGNED                NOT NULL,

	KEY `fk_professions_protocols_profession` (`profession_id`),
	KEY `fk_professions_protocols_protocol` (`protocol_id`),

	CONSTRAINT `pk_professions_protocols_id` PRIMARY KEY (`id`),
	CONSTRAINT `fk_professions_protocols_profession_id` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`),
	CONSTRAINT `fk_professions_protocols_protocol_id` FOREIGN KEY (`protocol_id`) REFERENCES `protocols` (`id`),
	CONSTRAINT `fk_professions_protocols_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_professions_protocols_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `professions_services` (

	`id`                INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`profession_id`     INT UNSIGNED                NOT NULL,
	`service_id`        INT UNSIGNED                NOT NULL,

	`info_create_time`  TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                NOT NULL,
	`info_changed_id`   INT UNSIGNED                NOT NULL,

	KEY `fk_professions_services_profession` (`profession_id`),
	KEY `fk_professions_services_protocol` (`service_id`),

	CONSTRAINT `pk_professions_services_id` PRIMARY KEY (`id`),
	CONSTRAINT `fk_professions_services_profession_id` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`),
	CONSTRAINT `fk_professions_services_protocol_id` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
	CONSTRAINT `fk_professions_services_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_professions_services_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `receptions_services` (

	`id`                INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`reception_id`      INT UNSIGNED                NOT NULL,
	`service_id`        INT UNSIGNED                NOT NULL,

	`info_create_time`  TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`info_changed_time` TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`info_creating_id`  INT UNSIGNED                NOT NULL,
	`info_changed_id`   INT UNSIGNED                NOT NULL,

	KEY `fk_receptions_services_reception` (`reception_id`),
	KEY `fk_receptions_services_service` (`service_id`),

	CONSTRAINT `pk_receptions_services_id` PRIMARY KEY (`id`),
	CONSTRAINT `fk_professions_protocols_reception_id` FOREIGN KEY (`reception_id`) REFERENCES `receptions` (`id`),
	CONSTRAINT `fk_professions_protocols_service_id` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
	CONSTRAINT `fk_receptions_services_info_creating_id` FOREIGN KEY (`info_creating_id`) REFERENCES `workers` (`id`),
	CONSTRAINT `fk_receptions_services_info_changed_id` FOREIGN KEY (`info_changed_id`) REFERENCES `workers` (`id`)

		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8
	COLLATE = utf8_general_ci;
