-- MR_USER_INFO TEST DATA
TRUNCATE TABLE.MR_USER_INFO;
INSERT INTO MR_USER_INFO (FIRSTNAME,LASTNAME,USERNAME,PASSWORD,USER_TYPE) VALUES 
	('Juan','Dela Cruz','admin','admin','A'),
	('Juana','Santos','test','12345','C');

-- MR_DETAILS TEST_DATA
TRUNCATE TABLE MR_DETAILS;
INSERT INTO MR_DETAILS (TITLE,DESCT,START_DATE,SEAT_PRICE,USER_FROM) VALUES
('Salt','Salt is a 2010 American action thriller film directed by Phillip Noyce, written by Kurt Wimmer, and starring Angelina Jolie, Liev Schreiber, Daniel Olbrychski, August Diehl, Arthur Kade, and Chiwetel Ejiofor. Jolie plays Evelyn Salt, who is accused of being a Russian sleeper agent and goes on the run to try to clear her name.',CAST(GETDATE() AS DATE),'199.00','admin'),
('The Battleship Island','The Battleship Island is a 2017 South Korean period action film starring Hwang Jung-min, So Ji-sub, Song Joong-ki and Lee Jung-hyun. It is a Japanese occupation-era film about an attempted prison break from a forced labor camp on Hashima Island.',CAST(GETDATE() AS DATE),'250.00','admin'),
('Men in Black : International','The Men in Black have expanded to cover the globe but so have the villains of the universe. To keep everyone safe, decorated Agent H and determined rookie M join forces -- an unlikely pairing that just might work. When aliens that can take the form of any human arrive on Earth, H and M embark on a globe-trotting adventure to save the agency -- and ultimately the world -- from their mischievous plans.',CAST(GETDATE()+1 AS DATE),'225.00','admin'),
('Mission : Impossible - Fallout','Mission: Impossible - Fallout is a 2018 American action spy film written, produced, and directed by Christopher McQuarrie. It is the sixth installment in the Mission: Impossible film series, and the second film to be directed by McQuarrie following the 2015 film Rogue Nation, making him the first director to direct more than one film in the franchise. The cast includes Tom Cruise, Ving Rhames, Simon Pegg, Rebecca Ferguson, Sean Harris, Michelle Monaghan, and Alec Baldwin, all of whom reprise their roles from the previous films. Henry Cavill, Vanessa Kirby, and Angela Bassett join the franchise. In the film, Ethan Hunt and his team must track down missing plutonium while being monitored by the Apostles after a mission goes wrong.',CAST(GETDATE()+2 AS DATE),'200.00','admin'),
('Hitman','Raised from childhood by the mysterious Diana organisation, Agent 47 is the perfect killer, but when he is dispatched to kill the Russian president, 47 discovers that his employers have betrayed him. Taking prostitute and possible witness Nika with him, the enigmatic assassin flees from both Interpol and the Russian secret service as he fights to uncover the root of the conspiracy.',CAST(GETDATE()+3 AS DATE),'150.00','admin'),
('Benji','Benji is a 2018 American adventure drama film written and directed by Brandon Camp, and produced by Blumhouse Productions. The film is a reboot of the 1974 film of the same name, which was directed by Camp''s father Joe. It stars Gabriel Bateman and Darby Camp. Jason Blum served as a producer through his Blumhouse Productions label.',CAST(GETDATE()+4 AS DATE),'199.00','admin');

-- MR_TIME_SLOTS TEST DATA
TRUNCATE TABLE MR_TIME_SLOTS;
INSERT INTO MR_TIME_SLOTS (MRD_ID,NUM_SEATS,START_TIME,END_TIME,USER_FROM) VALUES
('1','26',CAST('10:00:00' AS TIME),CAST('12:00:00' AS TIME),'admin'),
('1','38',CAST('11:30:00' AS TIME),CAST('13:30:00' AS TIME),'admin'),
('1','26',CAST('12:00:00' AS TIME),CAST('14:00:00' AS TIME),'admin'),
('1','47',CAST('14:00:00' AS TIME),CAST('16:00:00' AS TIME),'admin'),
('1','38',CAST('15:00:00' AS TIME),CAST('17:00:00' AS TIME),'admin'),
('2','26',CAST('10:30:00' AS TIME),CAST('12:30:00' AS TIME),'admin'),
('2','38',CAST('11:30:00' AS TIME),CAST('13:30:00' AS TIME),'admin'),
('2','26',CAST('12:00:00' AS TIME),CAST('14:00:00' AS TIME),'admin'),
('2','47',CAST('13:30:00' AS TIME),CAST('15:30:00' AS TIME),'admin'),
('2','38',CAST('15:00:00' AS TIME),CAST('17:00:00' AS TIME),'admin'),
('3','26',CAST('10:00:00' AS TIME),CAST('12:00:00' AS TIME),'admin'),
('3','38',CAST('11:30:00' AS TIME),CAST('13:30:00' AS TIME),'admin'),
('3','26',CAST('12:00:00' AS TIME),CAST('14:00:00' AS TIME),'admin'),
('3','47',CAST('14:00:00' AS TIME),CAST('16:00:00' AS TIME),'admin'),
('3','38',CAST('15:00:00' AS TIME),CAST('17:00:00' AS TIME),'admin'),
('4','26',CAST('10:00:00' AS TIME),CAST('12:00:00' AS TIME),'admin'),
('4','38',CAST('11:30:00' AS TIME),CAST('13:30:00' AS TIME),'admin'),
('4','26',CAST('12:00:00' AS TIME),CAST('14:00:00' AS TIME),'admin'),
('4','47',CAST('14:00:00' AS TIME),CAST('16:00:00' AS TIME),'admin'),
('4','38',CAST('15:00:00' AS TIME),CAST('17:00:00' AS TIME),'admin'),
('6','18',CAST('10:00:00' AS TIME),CAST('11:30:00' AS TIME),'admin'),
('6','38',CAST('12:00:00' AS TIME),CAST('13:30:00' AS TIME),'admin'),
('6','26',CAST('13:30:00' AS TIME),CAST('15:00:00' AS TIME),'admin'),
('6','48',CAST('15:00:00' AS TIME),CAST('17:30:00' AS TIME),'admin'),
('6','50',CAST('14:00:00' AS TIME),CAST('15:30:00' AS TIME),'admin'),
('5','26',CAST('10:00:00' AS TIME),CAST('12:00:00' AS TIME),'admin'),
('5','38',CAST('11:30:00' AS TIME),CAST('13:30:00' AS TIME),'admin'),
('5','26',CAST('12:00:00' AS TIME),CAST('14:00:00' AS TIME),'admin'),
('5','47',CAST('14:00:00' AS TIME),CAST('16:00:00' AS TIME),'admin'),
('5','38',CAST('15:00:00' AS TIME),CAST('17:00:00' AS TIME),'admin');

-- MR_TRANSACTION_HISTORY TEST DATA
TRUNCATE TABLE MR_TRANSACTION_HISTORY;
INSERT INTO MR_TRANSACTION_HISTORY (MRD_ID,MRTS_ID,CUSTOMER_NAME,SEAT_LIST,STATUS,USER_FROM) VALUES
('1','1','Customer 1','A4','R','admin'),
('1','1','Customer 1','A5','R','admin'),
('1','1','Customer 1','B6','R','admin'),
('1','1','Customer 1','B5','R','admin'),
('1','2','Customer 2','A4','R','admin'),
('1','2','Customer 2','A5','R','admin'),
('1','2','Customer 2','A6','R','admin'),
('1','2','Customer 2','D4','R','admin'),
('1','2','Customer 2','D5','R','admin'),
('1','2','Customer 2','D6','R','admin'),
('1','2','Customer 2','D7','R','admin'),
('1','3','Customer 1','B4','R','admin'),
('1','3','Customer 1','B5','R','admin'),
('1','3','Customer 1','B6','R','admin'),
('1','2','Customer 2','D4','C','admin'),
('1','2','Customer 2','D5','C','admin'),
('1','2','Customer 2','D6','C','admin'),
('1','2','Customer 2','D7','C','admin'),
('1','4','Customer 1','C4','R','admin'),
('1','4','Customer 1','C5','R','admin'),
('1','4','Customer 1','C6','R','admin'),
('1','4','Customer 1','E10','R','admin'),
('1','4','Customer 1','D9','R','admin'),
('1','4','Customer 1','C7','R','admin'),
('1','5','Customer 2','D4','R','admin'),
('1','5','Customer 2','D5','R','admin'),
('1','5','Customer 2','D6','R','admin'),
('1','5','Customer 2','D7','R','admin'),
('1','5','Customer 2','C5','R','admin'),
('1','5','Customer 2','C6','R','admin'),
('3','11','Customer 3','C1','R','admin'),
('3','11','Customer 3','B9','R','admin'),
('3','11','Customer 3','B1','R','admin'),
('3','11','Customer 3','A1','R','admin'),
('3','12','Customer 4','B4','R','admin'),
('3','12','Customer 4','C5','R','admin'),
('3','12','Customer 4','B6','R','admin'),
('3','12','Customer 4','B5','R','admin'),
('3','12','Customer 4','C6','R','admin'),
('1','1','Customer 1','A4','U','system'),
('1','1','Customer 1','A5','U','system'),
('1','1','Customer 1','B5','U','system'),
('1','1','Customer 1','B6','U','system'),
('1','2','Customer 2','A4','U','system'),
('1','2','Customer 2','A5','U','system'),
('1','2','Customer 2','A6','U','system'),
('1','4','Customer 1','C4','U','system'),
('1','4','Customer 1','C5','U','system'),
('1','4','Customer 1','C6','U','system'),
('1','4','Customer 1','C7','U','system'),
('1','4','Customer 1','D9','U','system'),
('1','4','Customer 1','E10','U','system'),
('3','11','Test Customer 1','A5','R','test'),
('3','11','Test Customer 1','A6','R','test'),
('3','11','Test Customer 1','A7','R','test'),
('3','11','Test Customer 1','A8','R','test'),
('2','8','Test Customer 2','B4','R','test'),
('2','8','Test Customer 2','B5','R','test'),
('2','8','Test Customer 2','B6','R','test'),
('2','8','Test Customer 2','B7','R','test'),
('2','8','Customer 1','B8','R','admin'),
('2','8','Customer 1','B3','R','admin'),
('2','8','Customer 1','B2','R','admin'),
('1','3','Customer 1','B4','U','system'),
('1','3','Customer 1','B5','U','system'),
('1','3','Customer 1','B6','U','system'),
('1','5','Customer 2','C5','U','system'),
('1','5','Customer 2','C6','U','system'),
('1','5','Customer 2','D4','U','system'),
('1','5','Customer 2','D5','U','system'),
('1','5','Customer 2','D6','U','system'),
('1','5','Customer 2','D7','U','system');

-- MR_MOVIE_POSTER
TRUNCATE TABLE MR_MOVIE_POSTER;
INSERT INTO MR_MOVIE_POSTER (MRD_ID,FILE_NAME,USER_FROM) VALUES
('2','The_Battleship_Island_(film).jpg','admin'),
('1','Salt.jpg','admin'),
('3','Men_in_Black_International_(Official_Film_Poster).png','admin'),
('5','hitman.jpg','admin'),
('4','MI_Fallout.jpg','admin'),
('6','Benji_(2018_movie_poster).png','admin');