INSERT INTO `workers`
VALUES
	( 1, 'root', 'root', 'root', 1, '1994-11-27', 'root@localhost.tld', '000000000000', 1, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f', 'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_SUPER_ADMIN', NULL, NULL, 1, 1 );

INSERT INTO `professions`
VALUES
	( 1, 'root', NULL, NULL, 1, 1 );

ALTER TABLE `workers`
	ADD CONSTRAINT `fk_workers_profession_id` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`);

-- the root user has been created
-- root login: 000000000000 password: I2ML4bjO5u
