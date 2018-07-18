INSERT INTO `professions`
VALUES
	( 2, 'Аллерголог', NULL, NULL, 1, 1 ),
	( 3, 'Диетолог', NULL, NULL, 1, 1 ),
	( 4, 'Терапевт', NULL, NULL, 1, 1 ),
	( 5, 'Артролог', NULL, NULL, 1, 1 ),
	( 6, 'Логопед', NULL, NULL, 1, 1 ),
	( 7, 'Медсестра', NULL, NULL, 1, 1 );

INSERT INTO `workers`
VALUES
	( 2, 'Казимира ', 'Максимовна', 'Охота', 0, '1976-11-03', 'sommelier.jungle@gmail.com', '380661283348', 6, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_NURSE', NULL, NULL, 1, 1 ),
	( 3, 'Капитон', 'Борисович', 'Муравьев', 1, '1994-08-05', 'sommelier.jungle@gmail.com', '380662709331', 2, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_ADMIN,ROLE_DOCTOR', NULL, NULL, 1, 1 ),
	( 4, 'Луиза', 'Тимофеевна', 'Афанасьева', 0, '1993-01-03', 'sommelier.jungle@gmail.com', '380663504063', 3, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_DOCTOR', NULL, NULL, 1, 1 ),
	( 5, 'Алексей', 'Валентинович', 'Прокофьев', 1, '1978-04-17', 'sommelier.jungle@gmail.com', '380668568869', 2, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_DOCTOR', NULL, NULL, 1, 1 ),
	( 6, 'Бернар', 'Алексеевич', 'Поляков', 1, '1968-04-25', 'sommelier.jungle@gmail.com', '380668884391', 5, 0,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_DOCTOR', NULL, NULL, 1, 1 ),
	( 7, 'Антонин', 'Евгеньевич', 'Ефимов', 1, '1989-11-09', 'sommelier.jungle@gmail.com', '380664317956', 4, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_DOCTOR', NULL, NULL, 1, 1 ),
	( 8, 'Тимур', 'Вячеславович', 'Рзаев', 1, '1977-11-19', 'sommelier.jungle@gmail.com', '380669851151', 3, 1,
			 '7cfa3fee422d9687de56812e2f5ad18f',
		'htrLUMRFyWFWUSEKZJzq2gSuhcCC5VbO9uo6SizUEey3e51iWk/04NAih8ADcKr2LobnI7jNRaqqgRTr2hqGew==',
		'ROLE_DOCTOR', NULL, NULL, 1, 1 );

INSERT INTO `services`
VALUES
	( 1, 'Service 1', 21, NULL, NULL, 1, 1 ),
	( 2, 'Service 2', 889, NULL, NULL, 1, 1 ),
	( 3, 'Service 3', 175.75, NULL, NULL, 1, 1 ),
	( 4, 'Service 4', 83, NULL, NULL, 1, 1 ),
	( 5, 'Service 5', 408, NULL, NULL, 1, 1 ),
	( 6, 'Service 6', 273, NULL, NULL, 1, 1 ),
	( 7, 'Service 7', 240.9, NULL, NULL, 1, 1 ),
	( 8, 'Service 8', 52, NULL, NULL, 1, 1 ),
	( 9, 'Service 9', 1000, NULL, NULL, 1, 1 ),
	( 10, 'Сервис 10', 100, NULL, NULL, 1, 1 ),
	( 11, 'Сервис 11', 21, NULL, NULL, 1, 1 ),
	( 12, 'Сервис 12', 21.4, NULL, NULL, 1, 1 );

INSERT INTO `patients`
VALUES
	( 1, 'Даниил', 'Владимирович', 'Андреев', 1, '1972-08-01', '380999509438',
			 'ул. Бабаевская улица, дом 49, квартира 152', 'AndreevDanila i Daniil321', NULL, NULL, 1, 1 ),
	( 2, 'Лариса', 'Сергеевна', 'Денисова', 0, '1987-08-28', '380668797629',
			 'ул. Деревцова, дом 46, квартира 280', 'DenisovaLarisa337', NULL, NULL, 1, 1 ),
	( 3, 'Каллистрат', 'Леонидович', 'Кузнецов', 1, '1995-01-26', '380669122068',
			 'ул. Дарвина, дом 92, квартира 192', NULL, NULL, NULL, 1, 1 ),
	( 4, 'Олимпий', 'Филиппович', 'Остимчук', 1, '1969-07-02', '380663111141',
			 'ул. Бажова, дом 97, квартира 246', NULL, NULL, NULL, 1, 1 ),
	( 5, 'Капитон', 'Геннадьевич', 'Ичёткин', 1, '1982-01-06', '380668982760',
			 'ул. Лидии Базановой, дом 60, квартира 47', NULL, NULL, NULL, 1, 1 ),
	( 6, 'Тимур', 'Денисович', 'Ичёткин', 1, '1984-08-28', '380661449631',
			 'ул. Вагонников 1-я, дом 76, квартира 119', 'IchetkinTimur313', NULL, NULL, 1, 1 ),
	( 7, 'Григорий', 'Васильевич', 'Афанасьев', 1, '1973-02-11', '380669556623',
			 'ул. Весенняя, дом 48, квартира 249', 'Зелёный', NULL, NULL, 1, 1 ),
	( 8, 'Роман', 'Макарович', 'Наумов', 1, '1979-01-12', '380666965833',
			 'ул. Михаила Агибалова, дом 64, квартира 204', 'Фиолетовый', NULL, NULL, 1, 1 );

INSERT INTO `protocols`
VALUES
	( 1, 'protocols 1', 'TYPE_SAMPLE', 'protocols1', NULL, NULL, 1, 1 ),
	( 2, 'protocols 2', 'TYPE_TEMPLATE', 'protocols2', NULL, NULL, 1, 1 ),
	( 3, 'protocols 3', 'TYPE_TEMPLATE', 'protocols3', NULL, NULL, 1, 1 ),
	( 4, 'protocols 4', 'TYPE_TEMPLATE', 'protocols4', NULL, NULL, 1, 1 ),
	( 5, 'protocols 5', 'TYPE_SAMPLE', 'protocols5', NULL, NULL, 1, 1 ),
	( 6, 'protocols 6', 'TYPE_SAMPLE', 'protocols6', NULL, NULL, 1, 1 ),
	( 7, 'protocols 7', 'TYPE_TEMPLATE', 'protocols7', NULL, NULL, 1, 1 );

INSERT INTO `professions_protocols`
VALUES
	( 1, 2, 1, NULL, NULL, 1, 1 ),
	( 2, 2, 2, NULL, NULL, 1, 1 ),
	( 3, 3, 2, NULL, NULL, 1, 1 );

INSERT INTO `professions_services`
VALUES
	( 1, 2, 1, NULL, NULL, 1, 1 ),
	( 2, 2, 2, NULL, NULL, 1, 1 ),
	( 3, 3, 2, NULL, NULL, 1, 1 );

INSERT INTO `receptions`
VALUES
	( 1, 1, 8, '2017-01-20 01:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 2, 2, 3, '2017-01-20 02:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 3, 3, 4, '2017-01-20 03:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 4, 4, 5, '2017-01-20 04:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 5, 5, 6, '2017-01-20 05:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 6, 6, 6, '2017-01-20 06:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 7, 7, 5, '2017-01-20 07:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 8, 8, 4, '2017-01-20 08:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 9, 7, 4, '2017-01-20 09:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 10, 6, 4, '2017-01-20 10:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 11, 4, 4, '2017-01-20 11:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 12, 4, 8, '2017-01-20 12:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 13, 4, 8, '2017-01-20 13:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 14, 3, 8, '2017-01-20 14:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 15, 2, 8, '2017-01-20 15:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 16, 3, 8, '2017-01-20 16:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 17, 6, 4, '2017-01-20 17:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 18, 2, 5, '2017-01-20 18:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 19, 1, 3, '2017-01-20 19:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 ),
	( 20, 2, 3, '2017-01-20 20:57:06', TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, 1, 1 );

INSERT INTO `receptions_services`
VALUES
	( NULL, 1, 1, NULL, NULL, 1, 1 ),
	( NULL, 2, 2, NULL, NULL, 1, 1 ),
	( NULL, 3, 3, NULL, NULL, 1, 1 ),
	( NULL, 4, 4, NULL, NULL, 1, 1 ),
	( NULL, 5, 5, NULL, NULL, 1, 1 ),
	( NULL, 6, 6, NULL, NULL, 1, 1 ),
	( NULL, 7, 7, NULL, NULL, 1, 1 ),
	( NULL, 8, 8, NULL, NULL, 1, 1 ),
	( NULL, 9, 9, NULL, NULL, 1, 1 ),
	( NULL, 10, 10, NULL, NULL, 1, 1 ),
	( NULL, 11, 11, NULL, NULL, 1, 1 ),
	( NULL, 12, 12, NULL, NULL, 1, 1 ),
	( NULL, 13, 1, NULL, NULL, 1, 1 ),
	( NULL, 14, 2, NULL, NULL, 1, 1 ),
	( NULL, 15, 3, NULL, NULL, 1, 1 ),
	( NULL, 16, 4, NULL, NULL, 1, 1 ),
	( NULL, 17, 5, NULL, NULL, 1, 1 ),
	( NULL, 18, 6, NULL, NULL, 1, 1 ),
	( NULL, 19, 7, NULL, NULL, 1, 1 ),
	( NULL, 20, 8, NULL, NULL, 1, 1 );

-- all password for user: I2ML4bjO5u
